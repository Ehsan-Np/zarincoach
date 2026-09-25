<?php
/**
 * مدیریت بارگذاری فایل‌های استایل و اسکریپت
 *
 * اصل مهم: مجموع دارایی‌های قالب تنها شامل یک فایل CSS، یک فایل JS و یک فونت است.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_asset_version' ) ) :
	/**
	 * نسخه‌ی فایل‌ها برای کش مرورگر.
	 *
	 * @param string $relative_path مسیر نسبی فایل.
	 * @return string
	 */
	function zc_asset_version( $relative_path ) {
		$file = ZC_DIR . $relative_path;
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG && file_exists( $file ) ) {
			return (string) filemtime( $file );
		}
		return ZC_VERSION;
	}
endif;

if ( ! function_exists( 'zc_enqueue_assets' ) ) :
	/**
	 * بارگذاری استایل‌ها و اسکریپت‌های قالب.
	 *
	 * @return void
	 */
	function zc_enqueue_assets() {
		// استایل اصلی (خروجی بهینه‌سازی شده‌ی Tailwind).
		wp_enqueue_style(
			'zc-main',
			ZC_URI . '/assets/css/main.css',
			array(),
			zc_asset_version( '/assets/css/main.css' )
		);

		// style.css قالب فقط شناسنامه است (کلاس‌های ضروری وردپرس داخل main.css) ← یک درخواست کمتر.
		// در قالب فرزند، style.css فرزند بارگذاری می‌شود.
		if ( is_child_theme() ) {
			wp_enqueue_style( 'zc-child', get_stylesheet_uri(), array( 'zc-main' ), (string) wp_get_theme()->get( 'Version' ) );
		}

		// اسکریپت اصلی (جاوااسکریپت خالص، بدون jQuery).
		// نسخه‌ی فشرده (terser) مگر در SCRIPT_DEBUG یا وقتی فایل اصلی تازه‌تر از نسخه‌ی فشرده باشد.
		$js_file = '/assets/js/main.js';
		if ( zc_switch( 'perf_min_js', true ) && ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ) {
			$min = ZC_DIR . '/assets/js/main.min.js';
			if ( is_readable( $min ) && filemtime( $min ) >= filemtime( ZC_DIR . $js_file ) ) {
				$js_file = '/assets/js/main.min.js';
			}
		}
		wp_enqueue_script(
			'zc-main',
			ZC_URI . $js_file,
			array(),
			zc_asset_version( $js_file ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);

		wp_localize_script(
			'zc-main',
			'ZarinCoach',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'restUrl'      => esc_url_raw( rest_url( 'zarincoach/v1/' ) ),
				'nonce'        => wp_create_nonce( 'zc_nonce' ),
				'animations'   => zc_switch( 'general_animations', true ) ? '1' : '0',
				'stickyHeader' => zc_switch( 'general_sticky_header', true ) ? '1' : '0',
				'progress'     => zc_switch( 'general_scroll_progress', true ) ? '1' : '0',
				'backToTop'    => zc_switch( 'general_back_to_top', true ) ? '1' : '0',
				'smoothScroll' => zc_switch( 'general_smooth_scroll', true ) ? '1' : '0',
				'instantPages' => zc_switch( 'perf_instant_pages', true ) ? '1' : '0',
				'darkMode'     => (string) zc_opt( 'general_dark_mode', 'toggle' ),
				'counters'     => '1',
				'fontCookie'   => zc_switch( 'typo_preload_font', true ) ? ( COOKIEPATH ? COOKIEPATH : '/' ) : '',
				'i18n'         => array(
					'menuOpen'  => __( 'باز کردن منو', 'zarincoach' ),
					'menuClose' => __( 'بستن منو', 'zarincoach' ),
					'light'     => __( 'حالت روشن', 'zarincoach' ),
					'dark'      => __( 'حالت تاریک', 'zarincoach' ),
					'sending'   => __( 'در حال ارسال…', 'zarincoach' ),
					'sent'      => __( 'پیام شما با موفقیت ارسال شد.', 'zarincoach' ),
					'error'     => __( 'خطایی رخ داد، دوباره تلاش کنید.', 'zarincoach' ),
				),
			)
		);

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_enqueue_assets', 20 );

if ( ! function_exists( 'zc_preload_font' ) ) :
	/**
	 * پیش‌بارگذاری فونت برای بهبود شاخص LCP.
	 *
	 * @return void
	 */
	function zc_preload_font() {
		if ( ! zc_switch( 'typo_preload_font', true ) ) {
			return;
		}
		// فقط بازدید اول: در بازدیدهای بعدی فونت در کش مرورگر است و پیش‌بارگذاری دوباره
		// در فایرفاکس هشدار «preloaded but not used within a few seconds» می‌دهد.
		// کوکی کارکردی zc_fc پس از بارگذاری کامل فونت توسط main.js ثبت می‌شود.
		if ( ! empty( $_COOKIE['zc_fc'] ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
			return;
		}

		$file = zc_switch( 'typo_persian_digits', true ) ? 'AradFD-VF.woff2' : 'Arad-VF.woff2';

		printf(
			'<link rel="preload" href="%1$s" as="font" type="font/woff2" crossorigin>' . "\n",
			esc_url( ZC_URI . '/assets/fonts/' . $file )
		);
	}
endif;
add_action( 'wp_head', 'zc_preload_font', 2 );

if ( ! function_exists( 'zc_resource_hints' ) ) :
	/**
	 * افزودن پیش‌اتصال‌های دلخواه تنظیمات.
	 *
	 * @param array  $hints      نشانی‌های فعلی.
	 * @param string $relation_type نوع ارتباط.
	 * @return array
	 */
	function zc_resource_hints( $hints, $relation_type ) {
		if ( 'preconnect' !== $relation_type ) {
			return $hints;
		}

		$domains = (array) zc_opt( 'perf_preconnect', array() );
		foreach ( $domains as $domain ) {
			$domain = trim( wp_parse_url( (string) $domain, PHP_URL_HOST ) ?: (string) $domain );
			if ( '' === $domain ) {
				continue;
			}
			$hints[] = array(
				'href' => '//' . $domain,
				'crossorigin',
			);
		}

		return $hints;
	}
endif;
add_filter( 'wp_resource_hints', 'zc_resource_hints', 10, 2 );

if ( ! function_exists( 'zc_elementor_editor_assets' ) ) :
	/**
	 * بارگذاری دارایی‌ها در ویرایشگر المنتور.
	 *
	 * @return void
	 */
	function zc_elementor_editor_assets() {
		if ( ! zc_switch( 'elementor_editor_css', true ) ) {
			return;
		}

		wp_enqueue_style(
			'zc-elementor-editor',
			ZC_URI . '/assets/css/main.css',
			array(),
			zc_asset_version( '/assets/css/main.css' )
		);

		wp_add_inline_style( 'zc-elementor-editor', zc_dynamic_css() );
	}
endif;
add_action( 'elementor/preview/enqueue_styles', 'zc_elementor_editor_assets', 20 );
add_action( 'elementor/editor/before_enqueue_styles', 'zc_elementor_editor_assets', 20 );

if ( ! function_exists( 'zc_head_custom_code' ) ) :
	/**
	 * چاپ کدهای سفارشی بخش head.
	 *
	 * @return void
	 */
	function zc_head_custom_code() {
		$code = trim( (string) zc_opt( 'code_head', '' ) );
		if ( '' !== $code ) {
			echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- کد مدیر سایت
		}

		$body = trim( (string) zc_opt( 'code_body', '' ) );
		if ( '' !== $body ) {
			\add_action( 'wp_body_open', 'zc_body_custom_code', 1 );
		}
	}
endif;
add_action( 'wp_head', 'zc_head_custom_code', 99 );

if ( ! function_exists( 'zc_body_custom_code' ) ) :
	/**
	 * چاپ کدهای سفارشی ابتدای body.
	 *
	 * @return void
	 */
	function zc_body_custom_code() {
		$code = trim( (string) zc_opt( 'code_body', '' ) );
		if ( '' !== $code ) {
			echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- کد مدیر سایت
		}
	}
endif;

if ( ! function_exists( 'zc_footer_custom_code' ) ) :
	/**
	 * چاپ کدهای سفارشی انتهای صفحه.
	 *
	 * @return void
	 */
	function zc_footer_custom_code() {
		$code = trim( (string) zc_opt( 'code_footer', '' ) );
		if ( '' !== $code ) {
			echo "\n" . $code . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- کد مدیر سایت
		}
	}
endif;
add_action( 'wp_footer', 'zc_footer_custom_code', 99 );

if ( ! function_exists( 'zc_defer_scripts' ) ) :
	/**
	 * بارگذاری تأخیری اسکریپت‌ها با API استاندارد وردپرس (strategy).
	 *
	 * وردپرس خودش سازگاری با وابستگی‌ها و اسکریپت‌های درون‌خطی را بررسی می‌کند و
	 * در صورت ناسازگاری، به صورت خودکار بارگذاری عادی را انتخاب می‌کند؛ بنابراین هیچ
	 * افزونه‌ای (مانند المنتور) دچار خطا نمی‌شود.
	 *
	 * @return void
	 */
	function zc_defer_scripts() {
		if ( is_admin() || ! zc_switch( 'perf_defer', true ) ) {
			return;
		}

		// پیش‌نمایش و ویرایشگر المنتور نباید دستکاری شوند.
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$scripts = wp_scripts();
		$skip    = array( 'jquery', 'jquery-core', 'jquery-migrate' );

		foreach ( (array) $scripts->queue as $handle ) {
			if ( in_array( $handle, $skip, true ) || ! isset( $scripts->registered[ $handle ] ) ) {
				continue;
			}

			if ( $scripts->get_data( $handle, 'strategy' ) ) {
				continue;
			}

			// اسکریپت‌هایی که کد درون‌خطیِ «قبل» دارند، دست‌نخورده می‌مانند.
			if ( $scripts->get_data( $handle, 'before' ) ) {
				continue;
			}

			wp_script_add_data( $handle, 'strategy', 'defer' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_defer_scripts', 999 );
