<?php
/**
 * مرکز دانلود — سازگاری کامل قالب با محصولات دانلودی ووکامرس (نسخه‌ی ۲.۷).
 *
 * هدف: دانلود فایل‌های محصولات دیجیتال باید روی هر میزبان و در هر شرایطی
 * بدون خطای «۴۰۳» یا «لینک/فایل نامعتبر» کار کند. این ماژول سه کار می‌کند:
 *
 *  ۱) هم‌ترازسازی خودکار «دایرکتوری‌های مجاز دانلود»: اگر محدودسازی فعال باشد،
 *     پوشه‌ی بارگذاری (و woocommerce_uploads) به فهرست مجازها اضافه می‌شود؛
 *     اگر محدودسازی با فهرستِ خالی فعال شده باشد (علت اصلی رد شدن همه‌ی لینک‌ها)
 *     به‌صورت ایمن غیرفعال می‌شود تا هیچ دانلودی بی‌دلیل مسدود نشود.
 *  ۲) نرمال‌سازی مسیر فایل‌ها: نشانی‌های ذخیره‌شده که پس از انتقال دامنه یا
 *     https شدن سایت دیگر با پیشوند فعلی سایت یکی نیستند، هنگام حل مسیر به
 *     مسیر واقعی روی دیسک نگاشت می‌شوند (متناسب با نصب زیرپوشه‌ای).
 *  ۳) مرکز وضعیت و تست کامل: کارت «مرکز دانلود» در صفحه‌ی اطلاعات سیستم با
 *     وضعیت زنده‌ی همه‌ی تنظیمات مؤثر بر دانلود + تست سرتاسری واقعی (ساخت
 *     فایل، محصول، سفارش، مجوز و گرفتن خود فایل از طریق HTTP).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_dl_register' ) ) :
	/**
	 * نمونه‌ی سامانه‌ی دایرکتوری‌های مجاز دانلود ووکامرس (اگر موجود باشد).
	 *
	 * @return \Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register|null
	 */
	function zc_dl_register() {
		if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_container' ) ) {
			return null;
		}
		try {
			$reg = wc_get_container()->get( \Automattic\WooCommerce\Internal\ProductDownloads\ApprovedDirectories\Register::class );
			return ( $reg && method_exists( $reg, 'get_mode' ) ) ? $reg : null;
		} catch ( \Throwable $e ) { // phpcs:ignore WordPress.DateTime.RestrictedFunctions -- لاگ خطا لازم نیست.
			return null;
		}
	}
endif;

if ( ! function_exists( 'zc_dl_setup' ) ) :
	/**
	 * ثبت قلاب‌ها.
	 *
	 * @return void
	 */
	function zc_dl_setup() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}

		/* نرمال‌سازی مسیر فایل‌های دانلودی (پیش‌فرض: روشن). */
		if ( zc_switch( 'dl_normalize', true ) ) {
			add_filter( 'woocommerce_product_get_file_download_path', 'zc_dl_normalize_path', 20 );
			add_filter( 'woocommerce_product_variation_get_file_download_path', 'zc_dl_normalize_path', 20 );
			add_filter( 'woocommerce_download_product_filepath', 'zc_dl_normalize_path', 20 );
			add_filter( 'woocommerce_downloadable_file_exists', 'zc_dl_file_exists', 20, 2 );
		}

		/* هم‌ترازسازی خودکار دایرکتوری‌های مجاز (پیش‌فرض: روشن). */
		add_action( 'admin_init', 'zc_dl_maybe_heal' );

		/* کارت مرکز دانلود در صفحه‌ی اطلاعات سیستم. */
		add_action( 'admin_enqueue_scripts', 'zc_dl_system_assets' );
	}
endif;
add_action( 'after_setup_theme', 'zc_dl_setup', 30 );

if ( ! function_exists( 'zc_dl_host_matches' ) ) :
	/**
	 * آیا میزبان دو نشانی یکسان‌اند؟ (با و بدون www برابر در نظر گرفته می‌شود)
	 *
	 * @param string $a میزبان اول.
	 * @param string $b میزبان دوم.
	 * @return bool
	 */
	function zc_dl_host_matches( $a, $b ) {
		$a = strtolower( preg_replace( '/^www\./i', '', (string) $a ) );
		$b = strtolower( preg_replace( '/^www\./i', '', (string) $b ) );
		return '' !== $a && '' !== $b && $a === $b;
	}
endif;

if ( ! function_exists( 'zc_dl_normalize_path' ) ) :
	/**
	 * نگاشت نشانی فایل دانلودی به مسیر واقعی روی دیسک، حتی وقتی پیشوند نشانی
	 * ذخیره‌شده با پیشوند فعلی سایت در http/https یا www تفاوت دارد.
	 *
	 * @param string $path نشانی یا مسیر فایل.
	 * @return string
	 */
	function zc_dl_normalize_path( $path ) {
		if ( ! is_string( $path ) || '' === $path || '[' === substr( $path, 0, 1 ) ) {
			return $path;
		}
		$host = (string) wp_parse_url( $path, PHP_URL_HOST );
		if ( '' === $host ) {
			return $path; // مسیر نسبی یا مطلق دیسک — دست‌نخورده.
		}

		$up      = wp_get_upload_dir();
		$scheme  = is_ssl() ? 'https' : 'http';
		$in_port = wp_parse_url( $path, PHP_URL_PORT );
		$port    = $in_port ? ':' . absint( $in_port ) : '';
		$cand    = array();
		foreach ( array( true, false ) as $www ) {
			$h = ( $www ? 'www.' : '' ) . $host;
			$cand[] = 'http://' . $h . $port;
			$cand[] = 'https://' . $h . $port;
		}

		$bases = array(
			'uploads' => array( untrailingslashit( $up['baseurl'] ), untrailingslashit( $up['basedir'] ) ),
			'site'    => array( untrailingslashit( site_url( '/' ) ), untrailingslashit( ABSPATH ) ),
		);
		if ( is_multisite() ) {
			$bases['network'] = array( untrailingslashit( network_site_url( '/' ) ), untrailingslashit( ABSPATH ) );
		}

		foreach ( $bases as $pair ) {
			$prefix = $pair[0];
			$root   = $pair[1];
			foreach ( $cand as $p ) {
				$full = $p . wp_parse_url( $prefix, PHP_URL_PATH );
				if ( '' !== $full && 0 === strpos( $path, $full . '/' ) ) {
					$tail = substr( $path, strlen( $full ) );
					$real = $root . $tail;
					if ( @file_exists( $real ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
						return $real;
					}
				}
			}
		}

		// نگاشت پایانی: اگر نشانی متعلق به همین سایت است اما فایل محلی نیست،
		// نشانی را با پیشوند فعلی سایت بازنویسی کن (اصلاح دامنه‌های قدیمی).
		$site_hosts = array_unique(
			array_filter(
				array(
					(string) wp_parse_url( home_url(), PHP_URL_HOST ),
					(string) wp_parse_url( $up['baseurl'], PHP_URL_HOST ),
				)
			)
		);
		$port = wp_parse_url( $path, PHP_URL_PORT );
		foreach ( $site_hosts as $sh ) {
			if ( zc_dl_host_matches( $host, $sh ) ) {
				$new = $scheme . '://' . $sh . ( $port ? ':' . absint( $port ) : '' ) . (string) wp_parse_url( $path, PHP_URL_PATH );
				return ( $new !== $path ) ? $new : $path;
			}
		}

		return $path;
	}
endif;

if ( ! function_exists( 'zc_dl_file_exists' ) ) :
	/**
	 * بررسی وجود فایل هنگام ذخیره‌ی محصول در مدیریت، پس از نرمال‌سازی مسیر.
	 *
	 * @param bool   $exists   نتیجه‌ی ووکامرس.
	 * @param string $file_url نشانی فایل.
	 * @return bool
	 */
	function zc_dl_file_exists( $exists, $file_url ) {
		if ( $exists ) {
			return true;
		}
		$resolved = zc_dl_normalize_path( (string) $file_url );
		return ( '' !== $resolved && $resolved !== $file_url && @file_exists( $resolved ) ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
	}
endif;

if ( ! function_exists( 'zc_dl_ensure_directories' ) ) :
	/**
	 * افزودن پوشه‌ی بارگذاری و woocommerce_uploads به دایرکتوری‌های مجاز.
	 *
	 * @return array<string,int> تعداد موارد افزوده‌شده.
	 */
	function zc_dl_ensure_directories() {
		$reg = zc_dl_register();
		if ( ! $reg || ! method_exists( $reg, 'add_approved_directory' ) ) {
			return array();
		}
		$up   = wp_get_upload_dir();
		$adds = array(
			'uploads'          => $reg->add_approved_directory( untrailingslashit( $up['basedir'] ), true ),
			'uploads_url'      => $reg->add_approved_directory( untrailingslashit( $up['baseurl'] ), true ),
			'woo_uploads'      => $reg->add_approved_directory( $up['basedir'] . '/woocommerce_uploads', true ),
			'woo_uploads_url'  => $reg->add_approved_directory( $up['baseurl'] . '/woocommerce_uploads', true ),
		);
		return $adds;
	}
endif;

if ( ! function_exists( 'zc_dl_maybe_heal' ) ) :
	/**
	 * خودترمیمی روزانه: اگر محدودسازی دایرکتوری فعال باشد، فهرست مجازها با
	 * مسیرهای واقعی سایت هم‌تراز می‌شود؛ فهرست خالی یعنی همه‌چیز مسدود است و
	 * به‌صورت ایمن محدودسازی خاموش می‌شود.
	 *
	 * @return void
	 */
	function zc_dl_maybe_heal() {
		if ( ! zc_switch( 'dl_sync', true ) || wp_doing_ajax() || wp_doing_cron() ) {
			return;
		}
		if ( get_transient( 'zc_dl_healed' ) ) {
			return;
		}
		set_transient( 'zc_dl_healed', 1, DAY_IN_SECONDS );

		$reg = zc_dl_register();
		if ( ! $reg || 'enabled' !== $reg->get_mode() ) {
			return; // محدودسازی خاموش است — پیش‌فرض امن ووکامرس؛ کاری نیست.
		}

		$enabled_before = ( method_exists( $reg, 'count' ) ) ? (int) $reg->count( true ) : -1;
		zc_dl_ensure_directories();
		$enabled_after = ( method_exists( $reg, 'count' ) ) ? (int) $reg->count( true ) : 0;

		if ( 0 === $enabled_before && 0 === $enabled_after ) {
			// محدودسازی بدون هیچ دایرکتوری مجاز و بدون امکان افزودن = مسدود شدن همه‌چیز؛ خاموش می‌کنیم.
			$reg->set_mode( 'disabled' );
			set_transient( 'zc_dl_notice', 'empty_list', HOUR_IN_SECONDS );
		}
	}
endif;

if ( ! function_exists( 'zc_dl_status' ) ) :
	/**
	 * وضعیت کامل تنظیمات مؤثر بر دانلود — برای کارت مرکز دانلود.
	 *
	 * @return array<int, array<string,mixed>>
	 */
	function zc_dl_status() {
		$rows = array();

		/* روش دانلود. */
		$method = (string) get_option( 'woocommerce_file_download_method', 'force' );
		$names  = array(
			'force'       => __( 'اجبار دانلود (پیشنهادی)', 'zarincoach' ),
			'xsendfile'   => __( 'X-Sendfile', 'zarincoach' ),
			'redirect'    => __( 'ریدایرکت مستقیم', 'zarincoach' ),
			'unprotected' => __( 'بدون محافظت', 'zarincoach' ),
		);
		$rows[] = array(
			'key'   => 'method',
			'label' => __( 'روش دانلود فایل', 'zarincoach' ),
			'value' => isset( $names[ $method ] ) ? $names[ $method ] : $method,
			'state' => in_array( $method, array( 'force', 'xsendfile' ), true ) ? 'ok' : 'warn',
			'hint'  => in_array( $method, array( 'force', 'xsendfile' ), true )
				? __( 'مسیر امن؛ فایل مستقیماً در دسترس نیست.', 'zarincoach' )
				: __( 'این روش‌ها فایل را بی‌محافظت می‌کنند؛ «اجبار دانلود» را برگزینید.', 'zarincoach' ),
			'fix'   => in_array( $method, array( 'force', 'xsendfile' ), true ) ? '' : 'method_force',
		);

		/* دایرکتوری‌های مجاز. */
		$reg = zc_dl_register();
		if ( $reg ) {
			$mode   = $reg->get_mode();
			$counts = method_exists( $reg, 'count' ) ? (int) $reg->count( true ) : 0;
			if ( 'enabled' !== $mode ) {
				$rows[] = array(
					'key'   => 'dirs',
					'label' => __( 'محدودسازی دایرکتوری‌های مجاز', 'zarincoach' ),
					'value' => __( 'غیرفعال (پیش‌فرض امن ووکامرس)', 'zarincoach' ),
					'state' => 'ok',
					'hint'  => __( 'همه‌ی لینک‌ها پذیرفته می‌شوند؛ نیازی به تغییر نیست.', 'zarincoach' ),
					'fix'   => '',
				);
			} elseif ( 0 === $counts ) {
				$rows[] = array(
					'key'   => 'dirs',
					'label' => __( 'محدودسازی دایرکتوری‌های مجاز', 'zarincoach' ),
					'value' => __( 'فعال با فهرست خالی — همه‌ی دانلودها رد می‌شوند!', 'zarincoach' ),
					'state' => 'danger',
					'hint'  => __( 'این عامل خطای «فایل نامعتبر» است؛ هم‌ترازسازی را اجرا کنید.', 'zarincoach' ),
					'fix'   => 'dirs',
				);
			} else {
				$up      = wp_get_upload_dir();
				$base    = untrailingslashit( $up['baseurl'] );
				$covered = false;
				if ( method_exists( $reg, 'list' ) ) {
					// list() آرایه‌ای با کلید approved_directories (فهرست StoredUrl) برمی‌گرداند.
					$list   = $reg->list(
						array(
							'per_page' => 200,
							'enabled'  => true,
						)
					);
					$paths  = ( is_array( $list ) && isset( $list['approved_directories'] ) && is_array( $list['approved_directories'] ) ) ? $list['approved_directories'] : array();
					foreach ( $paths as $stored ) {
						if ( ! is_object( $stored ) || ! method_exists( $stored, 'get_url' ) ) {
							continue;
						}
						$url = untrailingslashit( $stored->get_url() );
						// والد یا فرزندِ پوشه‌ی بارگذاری کافی است (بررسی ووکامرس همه‌ی والدین را می‌پذیرد).
						if ( '' !== $url && ( 0 === strpos( $base, $url ) || 0 === strpos( $url, $base ) ) ) {
							$covered = true;
							break;
						}
					}
				}
				$rows[] = array(
					'key'   => 'dirs',
					'label' => __( 'محدودسازی دایرکتوری‌های مجاز', 'zarincoach' ),
					'value' => sprintf( /* translators: %s: تعداد */ __( 'فعال با %s مسیر مجاز', 'zarincoach' ), number_format_i18n( $counts ) ),
					'state' => $covered ? 'ok' : 'warn',
					'hint'  => $covered ? __( 'پوشه‌ی بارگذاری در فهرست مجاز است.', 'zarincoach' ) : __( 'پوشه‌ی بارگذاری در فهرست نیست؛ هم‌ترازسازی را اجرا کنید.', 'zarincoach' ),
					'fix'   => $covered ? '' : 'dirs',
				);
			}
		}

		/* ورود اجباری برای دانلود. */
		$login = 'yes' === get_option( 'woocommerce_downloads_require_login', 'no' );
		$rows[] = array(
			'key'   => 'login',
			'label' => __( 'اجبار ورود برای دانلود', 'zarincoach' ),
			'value' => $login ? __( 'فعال', 'zarincoach' ) : __( 'غیرفعال', 'zarincoach' ),
			'state' => $login ? 'warn' : 'ok',
			'hint'  => $login
				? __( 'لینک‌های ایمیلی برای مهمان‌ها خطای ۴۰۳ می‌دهند؛ فقط اگر عمداً می‌خواهید روشن بماند.', 'zarincoach' )
				: __( 'لینک‌های ایمیلی بدون ورود کار می‌کنند.', 'zarincoach' ),
			'fix'   => $login ? 'login_off' : '',
		);

		/* دسترسی پس از پرداخت. */
		$grant = 'yes' === get_option( 'woocommerce_downloads_grant_access_after_payment', 'yes' );
		$rows[] = array(
			'key'   => 'grant',
			'label' => __( 'اعطای دانلود پس از پرداخت', 'zarincoach' ),
			'value' => $grant ? __( 'فعال', 'zarincoach' ) : __( 'غیرفعال', 'zarincoach' ),
			'state' => $grant ? 'ok' : 'warn',
			'hint'  => $grant ? __( 'مجوز دانلود بلافاصله پس از پرداخت صادر می‌شود.', 'zarincoach' ) : __( 'مشتری تا تکمیل دستی سفارش دانلود ندارد.', 'zarincoach' ),
			'fix'   => $grant ? '' : 'grant_on',
		);

		/* محافظت از پوشه‌ی دانلودها. */
		$up       = wp_get_upload_dir();
		$woo_dir  = $up['basedir'] . '/woocommerce_uploads';
		$htaccess = $woo_dir . '/.htaccess';
		$server   = isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '';
		$apache   = (bool) preg_match( '/apache|litespeed/i', $server );
		if ( ! wp_mkdir_p( $woo_dir ) ) {
			$rows[] = array(
				'key'   => 'protect',
				'label' => __( 'محافظت از پوشه‌ی دانلودها', 'zarincoach' ),
				'value' => __( 'پوشه‌ی woocommerce_uploads ساخته نشد', 'zarincoach' ),
				'state' => 'danger',
				'hint'  => __( 'مجوز نوشتن پوشه‌ی بارگذاری را بررسی کنید.', 'zarincoach' ),
				'fix'   => '',
			);
		} elseif ( file_exists( $htaccess ) || ! $apache ) {
			$rows[] = array(
				'key'   => 'protect',
				'label' => __( 'محافظت از پوشه‌ی دانلودها', 'zarincoach' ),
				'value' => file_exists( $htaccess ) ? __( 'فعال (.htaccess)', 'zarincoach' ) : __( 'متناسب با میزبان', 'zarincoach' ),
				'state' => 'ok',
				'hint'  => file_exists( $htaccess ) ? __( 'دسترسی مستقیم به فایل‌ها بسته است.', 'zarincoach' ) : __( 'روی این میزبان محافظت در سطح PHP انجام می‌شود.', 'zarincoach' ),
				'fix'   => file_exists( $htaccess ) || ! $apache ? '' : 'protect',
			);
		} else {
			$rows[] = array(
				'key'   => 'protect',
				'label' => __( 'محافظت از پوشه‌ی دانلودها', 'zarincoach' ),
				'value' => __( 'فایل محافظت موجود نیست', 'zarincoach' ),
				'state' => 'warn',
				'hint'  => __( 'فایل .htaccess محافظ ساخته شود تا دانلود مستقیم بسته شود.', 'zarincoach' ),
				'fix'   => 'protect',
			);
		}

		return $rows;
	}
endif;

if ( ! function_exists( 'zc_downloads_run_test' ) ) :
	/**
	 * تست سرتاسری واقعی دانلود: ساخت فایل آزمایشی، محصول دانلودی، سفارش تکمیل‌شده،
	 * اعتبارسنجی مسیر (همان منطق مدیریت) و گرفتن خود فایل از طریق HTTP.
	 *
	 * @return array<string,mixed> گام‌ها و نتیجه.
	 */
	function zc_downloads_run_test() {
		$steps = array();
		$add   = static function ( $label, $ok, $detail = '', $warn = false ) use ( &$steps ) {
			$steps[] = array(
				'label'  => $label,
				'ok'     => (bool) $ok,
				'warn'   => (bool) $warn,
				'detail' => (string) $detail,
			);
		};

		$product_id = 0;
		$order_id   = 0;
		$test_file  = '';

		try {
			/* ۱) فایل آزمایشی داخل woocommerce_uploads. */
			$up      = wp_get_upload_dir();
			$woo_dir = $up['basedir'] . '/woocommerce_uploads';
			if ( ! wp_mkdir_p( $woo_dir ) ) {
				throw new Exception( __( 'ساخت پوشه‌ی woocommerce_uploads ممکن نشد.', 'zarincoach' ) );
			}
			$source   = ZC_DIR . '/assets/demo/shop-sample.pdf';
			$content  = is_readable( $source ) ? (string) file_get_contents( $source ) : "%PDF-1.4\n%ZarinCoach download test\n%%EOF\n"; // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			$test_file = $woo_dir . '/zc-download-test-' . wp_generate_password( 8, false, false ) . '.pdf';
			if ( false === file_put_contents( $test_file, $content ) ) { // phpcs:ignore WordPress.WP.AlternativeFunctions
				throw new Exception( __( 'نوشتن فایل آزمایشی ممکن نشد (مجوز پوشه‌ی بارگذاری).', 'zarincoach' ) );
			}
			$test_url = untrailingslashit( $up['baseurl'] ) . '/woocommerce_uploads/' . wp_basename( $test_file );
			$add( __( 'ساخت فایل آزمایشی در پوشه‌ی امن دانلودها', 'zarincoach' ), true, $test_url );

			/* ۲) اعتبارسنجی دقیقاً مطابق منطق مدیریت ووکامرس. */
			$download = new WC_Product_Download();
			$download->set_id( wp_generate_uuid4() );
			$download->set_name( __( 'فایل آزمایشی مرکز دانلود', 'zarincoach' ) );
			$download->set_file( $test_url );
			try {
				$download->check_is_valid( false );
				$add( __( 'اعتبارسنجی لینک (همان بررسی صفحه‌ی ویرایش محصول)', 'zarincoach' ), true );
			} catch ( Exception $e ) {
				$add( __( 'اعتبارسنجی لینک (همان بررسی صفحه‌ی ویرایش محصول)', 'zarincoach' ), false, $e->getMessage() );
			}

			/* ۳) محصول دانلودی. */
			$product = wc_get_product_object( 'simple' );
			$product->set_props(
				array(
					'name'               => __( '— تست دانلود زرین‌کوچ (حذف خودکار)', 'zarincoach' ),
					'catalog_visibility' => 'hidden',
					'status'             => 'private',
					'virtual'            => true,
					'downloadable'       => true,
					'regular_price'      => '0',
					'downloads'          => array( $download ),
				)
			);
			$product_id = $product->save();
			$add( __( 'ساخت محصول دانلودی آزمایشی', 'zarincoach' ), (bool) $product_id );

			/* ۴) سفارش تکمیل‌شده و صدور مجوز. */
			add_filter( 'woocommerce_email_recipient_customer_completed_order', '__return_empty_string', 9999 );
			add_filter( 'woocommerce_email_recipient_new_order', '__return_empty_string', 9999 );
			$order = wc_create_order( array( 'customer_id' => 0 ) );
			if ( is_wp_error( $order ) || ! $order instanceof WC_Order ) {
				throw new Exception( __( 'ساخت سفارش آزمایشی ممکن نشد.', 'zarincoach' ) );
			}
			$order_id = $order->get_id();
			// ایمیل صورت‌حساب باید روی خود سفارش بنشیند تا مجوز دانلود مهمان صادر شود.
			$order->set_billing_email( 'zc-download-test@example.com' );
			$order->save();
			$order->add_product( wc_get_product( $product_id ), 1 );
			$order->calculate_totals();
			$order->update_status( 'completed', __( 'تست مرکز دانلود زرین‌کوچ', 'zarincoach' ) );
			remove_filter( 'woocommerce_email_recipient_customer_completed_order', '__return_empty_string', 9999 );
			remove_filter( 'woocommerce_email_recipient_new_order', '__return_empty_string', 9999 );

			$items   = $order->get_downloadable_items();
			$has_url = ! empty( $items );
			$add( __( 'صدور مجوز دانلود پس از تکمیل سفارش', 'zarincoach' ), $has_url );

			/* ۵) گرفتن واقعی فایل از طریق HTTP. */
			$dl_url = '';
			if ( $has_url ) {
				$first  = reset( $items );
				$dl_url = add_query_arg(
					array(
						'download_file' => $first['product_id'],
						'order'         => $first['order_key'],
						'email'         => rawurlencode( 'zc-download-test@example.com' ),
						'key'           => $first['download_id'],
					),
					home_url( '/' )
				);
				$response = wp_remote_get( $dl_url, array( 'timeout' => 25, 'sslverify' => false ) );
				if ( is_wp_error( $response ) ) {
					$add( __( 'دریافت فایل از طریق HTTP', 'zarincoach' ), true, __( 'میزبان اجازه‌ی درخواست به خودش را نمی‌دهد (حلقه‌ی بازگشت مسدود است)؛ زنجیره تا پیش از این گام تأیید شد — برای تأیید نهایی، لینک دانلود را در مرورگر باز کنید.', 'zarincoach' ), true );
				} else {
					$code = (int) wp_remote_retrieve_response_code( $response );
					$body = (string) wp_remote_retrieve_body( $response );
					$ok   = 200 === $code && 0 === strpos( ltrim( $body ), '%PDF' );
					$add( __( 'دریافت فایل از طریق HTTP', 'zarincoach' ), $ok, 200 === $code ? '' : sprintf( /* translators: %s: کد وضعیت */ __( 'کد پاسخ: %s', 'zarincoach' ), $code ) );
				}
			}
		} catch ( Exception $e ) {
			$add( __( 'خطای غیرمنتظره در تست', 'zarincoach' ), false, $e->getMessage() );
		} catch ( \Throwable $e ) {
			$add( __( 'خطای غیرمنتظره در تست', 'zarincoach' ), false, $e->getMessage() );
		}

		/* ۶) پاک‌سازی کامل آثار آزمایش. */
		if ( $order_id ) {
			$order = wc_get_order( $order_id );
			if ( $order ) {
				$order->delete( true );
			}
		}
		if ( $product_id ) {
			$product = wc_get_product( $product_id );
			if ( $product ) {
				$product->delete( true );
			}
		}
		global $wpdb;
		if ( $order_id ) {
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wc_download_log WHERE order_id = %d", $order_id ) ); // phpcs:ignore WordPress.DB
			$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}woocommerce_downloadable_product_permissions WHERE order_id = %d", $order_id ) ); // phpcs:ignore WordPress.DB
		}
		if ( '' !== $test_file && file_exists( $test_file ) ) {
			wp_delete_file( $test_file );
		}

		$bad_n  = count(
			array_filter(
				$steps,
				static function ( $s ) {
					return ! $s['ok'];
				}
			)
		);
		$warn_n = count(
			array_filter(
				$steps,
				static function ( $s ) {
					return $s['ok'] && ! empty( $s['warn'] );
				}
			)
		);
		$all_ok = ! empty( $steps ) && 0 === $bad_n;
		update_option( 'zc_dl_last_test', array( 'time' => time(), 'ok' => $all_ok ), false );

		return array(
			'ok'   => $all_ok,
			'warn' => $all_ok && $warn_n > 0,
			'steps' => $steps,
		);
	}
endif;

if ( ! function_exists( 'zc_dl_ajax_action' ) ) :
	/**
	 * کنش‌های AJAX مرکز دانلود (تست کامل / اصلاح‌های یک‌کلیکی).
	 *
	 * @return void
	 */
	function zc_dl_ajax_action() {
		check_ajax_referer( 'zc_dl_center', 'nonce' );

		if ( ! current_user_can( 'manage_woocommerce' ) && ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'دسترسی غیرمجاز.', 'zarincoach' ) ), 403 );
		}

		$do = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : '';

		if ( 'test' === $do ) {
			wp_send_json_success( zc_downloads_run_test() );
		}

		$reg = zc_dl_register();

		switch ( $do ) {
			case 'dirs':
				if ( $reg ) {
					zc_dl_ensure_directories();
					if ( 0 === ( method_exists( $reg, 'count' ) ? (int) $reg->count( true ) : 0 ) ) {
						$reg->set_mode( 'disabled' ); // افزودن ممکن نشد؛ برای رفع انسداد کامل خاموش کن.
					}
				}
				wp_send_json_success( array( 'message' => __( 'دایرکتوری‌های مجاز با پوشه‌ی بارگذاری هم‌تراز شد.', 'zarincoach' ), 'rows' => zc_dl_status() ) );
				// no fallthrough: wp_send_json_success ends the request.
			case 'mode_off':
				if ( $reg ) {
					$reg->set_mode( 'disabled' );
				}
				wp_send_json_success( array( 'message' => __( 'محدودسازی دایرکتوری‌ها غیرفعال شد (پیش‌فرض امن ووکامرس).', 'zarincoach' ), 'rows' => zc_dl_status() ) );
			case 'login_off':
				update_option( 'woocommerce_downloads_require_login', 'no' );
				wp_send_json_success( array( 'message' => __( 'اجبار ورود برای دانلود خاموش شد؛ لینک‌های ایمیلی بدون ۴۰۳ کار می‌کنند.', 'zarincoach' ), 'rows' => zc_dl_status() ) );
			case 'grant_on':
				update_option( 'woocommerce_downloads_grant_access_after_payment', 'yes' );
				wp_send_json_success( array( 'message' => __( 'دانلودها بلافاصله پس از پرداخت فعال می‌شوند.', 'zarincoach' ), 'rows' => zc_dl_status() ) );
			case 'method_force':
				update_option( 'woocommerce_file_download_method', 'force' );
				wp_send_json_success( array( 'message' => __( 'روش دانلود روی «اجبار دانلود» (امن) تنظیم شد.', 'zarincoach' ), 'rows' => zc_dl_status() ) );
			case 'protect':
				$up      = wp_get_upload_dir();
				$woo_dir = $up['basedir'] . '/woocommerce_uploads';
				wp_mkdir_p( $woo_dir );
				if ( ! file_exists( $woo_dir . '/.htaccess' ) ) {
					file_put_contents( $woo_dir . '/.htaccess', "Options -Indexes\n<IfModule mod_authz_core.c>\n\tRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\n\tDeny from all\n</IfModule>\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				}
				if ( ! file_exists( $woo_dir . '/index.html' ) ) {
					file_put_contents( $woo_dir . '/index.html', '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
				}
				wp_send_json_success( array( 'message' => __( 'محافظت پوشه‌ی دانلودها برقرار شد.', 'zarincoach' ), 'rows' => zc_dl_status() ) );
		}

		wp_send_json_error( array( 'message' => __( 'کنش نامعتبر است.', 'zarincoach' ) ), 400 );
	}
endif;
add_action( 'wp_ajax_zc_dl_center', 'zc_dl_ajax_action' );

if ( ! function_exists( 'zc_dl_system_assets' ) ) :
	/**
	 * سبک و اسکریپت کارت مرکز دانلود فقط در صفحه‌ی اطلاعات سیستم.
	 *
	 * @param string $hook صفحه‌ی جاری.
	 * @return void
	 */
	function zc_dl_system_assets( $hook ) {
		if ( false === strpos( (string) $hook, 'zc-system-info' ) ) {
			return;
		}
		$data = array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'zc_dl_center' ),
			'i18n'  => array(
				'running' => __( 'در حال اجرای تست…', 'zarincoach' ),
				'fail'    => __( 'عملیات ناموفق بود؛ دوباره تلاش کنید.', 'zarincoach' ),
			),
		);
		wp_register_script( 'zc-dl-center', '', array(), (string) ( defined( 'ZC_VERSION' ) ? ZC_VERSION : '2.7.0' ), true );
		wp_enqueue_script( 'zc-dl-center' );
		wp_add_inline_script(
			'zc-dl-center',
			'window.zcDlCenter = ' . wp_json_encode( $data ) . ';'
		);
	}
endif;

if ( ! function_exists( 'zc_dl_system_card' ) ) :
	/**
	 * خروجی HTML کارت مرکز دانلود (در انتهای صفحه‌ی اطلاعات سیستم).
	 *
	 * @return void
	 */
	function zc_dl_system_card() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return;
		}
		$rows  = zc_dl_status();
		$marks = array( 'ok' => '✓', 'warn' => '!', 'danger' => '✗' );
		?>
		<div class="zc-dl-card" id="zc-dl-center">
			<div class="zc-dl-card__head">
				<h2><span class="dashicons dashicons-download"></span> <?php esc_html_e( 'مرکز دانلود (محصولات دیجیتال)', 'zarincoach' ); ?></h2>
				<p><?php esc_html_e( 'سلامت کامل زنجیره‌ی دانلود ووکامرس: مجوزها، دایرکتوری‌های مجاز، محافظت پوشه و تست واقعی دریافت فایل.', 'zarincoach' ); ?></p>
			</div>
			<table class="zc-dl-card__table">
				<tbody id="zc-dl-rows">
				<?php foreach ( $rows as $row ) : ?>
					<tr>
						<td class="zc-dl-mark zc-dl-mark--<?php echo esc_attr( $row['state'] ); ?>"><?php echo esc_html( isset( $marks[ $row['state'] ] ) ? $marks[ $row['state'] ] : '·' ); ?></td>
						<td class="zc-dl-label"><?php echo esc_html( $row['label'] ); ?></td>
						<td class="zc-dl-value"><?php echo esc_html( $row['value'] ); ?></td>
						<td class="zc-dl-hint"><?php echo esc_html( $row['hint'] ); ?></td>
						<td class="zc-dl-action">
							<?php if ( ! empty( $row['fix'] ) ) : ?>
								<button type="button" class="button" data-zc-dl-fix="<?php echo esc_attr( $row['fix'] ); ?>"><?php esc_html_e( 'اصلاح خودکار', 'zarincoach' ); ?></button>
							<?php endif; ?>
						</td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<div class="zc-dl-card__foot">
				<button type="button" class="button button-primary" id="zc-dl-test"><?php esc_html_e( 'اجرای تست کامل دانلود', 'zarincoach' ); ?></button>
				<span id="zc-dl-test-result" class="zc-dl-result" aria-live="polite"></span>
			</div>
			<script>
			function zcDlCenterInit() {
				var cfg = window.zcDlCenter || {}; var box = document.getElementById('zc-dl-center');
				if (!box || !cfg.ajax) { return; }
				function renderRows(rows) {
					var marks = { ok: '\u2713', warn: '!', danger: '\u2717' }, tb = document.getElementById('zc-dl-rows'); tb.innerHTML = '';
					rows.forEach(function (r) {
						var tr = document.createElement('tr');
						tr.innerHTML = '<td class="zc-dl-mark zc-dl-mark--' + r.state + '">' + (marks[r.state] || '\u00b7') + '</td>' +
							'<td class="zc-dl-label"></td><td class="zc-dl-value"></td><td class="zc-dl-hint"></td><td class="zc-dl-action"></td>';
						tr.querySelector('.zc-dl-label').textContent = r.label;
						tr.querySelector('.zc-dl-value').textContent = r.value;
						tr.querySelector('.zc-dl-hint').textContent = r.hint;
						if (r.fix) {
							var b = document.createElement('button'); b.type = 'button'; b.className = 'button';
							b.setAttribute('data-zc-dl-fix', r.fix); b.textContent = <?php echo wp_json_encode( __( 'اصلاح خودکار', 'zarincoach' ) ); ?>;
							b.addEventListener('click', function () { act(b.getAttribute('data-zc-dl-fix')); });
							tr.querySelector('.zc-dl-action').appendChild(b);
						}
						tb.appendChild(tr);
					});
				}
				function act(fix, btn) {
					if (btn) { btn.disabled = true; }
					var fd = new FormData(); fd.append('action', 'zc_dl_center'); fd.append('nonce', cfg.nonce); fd.append('do', fix);
					fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
						.then(function (r) { return r.json(); })
						.then(function (j) {
							if (j && j.success) {
								if (j.data && j.data.rows) { renderRows(j.data.rows); }
								var res = document.getElementById('zc-dl-test-result'); if (res && j.data.message) { res.textContent = j.data.message; }
							} else if (btn) { btn.disabled = false; }
						})['catch'](function () { if (btn) { btn.disabled = false; } });
				}
				box.addEventListener('click', function (e) {
					var b = e.target.closest('[data-zc-dl-fix]');
					if (b) { act(b.getAttribute('data-zc-dl-fix'), b); }
				});
				var test = document.getElementById('zc-dl-test');
				if (test) {
					test.addEventListener('click', function () {
						test.disabled = true;
						var res = document.getElementById('zc-dl-test-result');
						res.textContent = cfg.i18n.running; res.className = 'zc-dl-result';
						var fd = new FormData(); fd.append('action', 'zc_dl_center'); fd.append('nonce', cfg.nonce); fd.append('do', 'test');
						fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
							.then(function (r) { return r.json(); })
							.then(function (j) {
								test.disabled = false;
								if (j && j.success && j.data && j.data.steps) {
									var bad = j.data.steps.filter(function (s) { return !s.ok; });
									var warn = j.data.steps.filter(function (s) { return s.ok && s.warn; });
									if (bad.length) {
										res.className = 'zc-dl-result is-bad';
										res.textContent = bad.map(function (s) { return s.label + (s.detail ? ' — ' + s.detail : ''); }).join(' | ');
									} else if (warn.length) {
										res.className = 'zc-dl-result is-warn';
										res.textContent = <?php echo wp_json_encode( __( 'زنجیره سالم است (یک هشدار): ', 'zarincoach' ) ); ?> + warn.map(function (s) { return s.detail; }).join(' ');
									} else {
										res.className = 'zc-dl-result is-ok';
										res.textContent = <?php echo wp_json_encode( __( 'تست کامل موفق بود — زنجیره‌ی دانلود سالم است.', 'zarincoach' ) ); ?>;
									}
								} else { res.className = 'zc-dl-result is-bad'; res.textContent = cfg.i18n.fail; }
							})['catch'](function () { test.disabled = false; res.className = 'zc-dl-result is-bad'; res.textContent = cfg.i18n.fail; });
					});
				}
			}
			if (window.zcDlCenter && document.getElementById('zc-dl-center')) { zcDlCenterInit(); }
			else if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', zcDlCenterInit); }
			</script>
			<style>
				.zc-dl-card { margin: 20px 0; padding: 20px 24px; border: 1px solid #dcdfe6; border-radius: 12px; background: #fff; }
				.zc-dl-card__head h2 { display: flex; align-items: center; gap: 8px; margin: 0 0 6px; font-size: 16px; }
				.zc-dl-card__head p { margin: 0 0 14px; color: #5a6270; font-size: 13px; }
				.zc-dl-card__table { width: 100%; border-spacing: 0; }
				.zc-dl-card__table td { padding: 9px 10px; border-bottom: 1px solid #f0f1f4; vertical-align: middle; font-size: 13px; }
				.zc-dl-mark { width: 26px; font-weight: 700; text-align: center; }
				.zc-dl-mark--ok { color: #1a9e6b; } .zc-dl-mark--warn { color: #d98a00; } .zc-dl-mark--danger { color: #d63638; }
				.zc-dl-label { width: 26%; font-weight: 600; }
				.zc-dl-hint { color: #7a8191; }
				.zc-dl-action { width: 120px; text-align: left; }
				.zc-dl-card__foot { margin-top: 14px; display: flex; align-items: center; gap: 12px; }
				.zc-dl-result.is-ok { color: #1a9e6b; font-weight: 600; }
				.zc-dl-result.is-warn { color: #d98a00; font-weight: 600; }
				.zc-dl-result.is-bad { color: #d63638; font-weight: 600; }
				body.rtl .zc-dl-action { text-align: right; }
			</style>
		</div>
		<?php
	}
endif;
add_action( 'zc_system_page_after', 'zc_dl_system_card' );
