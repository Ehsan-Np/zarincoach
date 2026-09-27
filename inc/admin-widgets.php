<?php
/**
 * صفحه‌ی «مدیریت ویجت‌ها» (زرین‌کوچ ← مدیریت ویجت‌ها) — نسخه‌ی ۲.۲
 *
 * - روشن/خاموش کردن تک‌تک ویجت‌های اختصاصی (همان گزینه‌ی elementor_widgets پنل تنظیمات).
 * - نمایش تعداد صفحه‌هایی که هر ویجت در آن‌ها به کار رفته است.
 * - ویجت خاموشی که در هیچ صفحه‌ای استفاده نشده، اصلاً بارگذاری نمی‌شود؛
 *   ویجت خاموشی که جایی استفاده شده فقط از پنل المنتور پنهان می‌شود تا آن صفحه‌ها خراب نشوند.
 * - راهنمای کامل ویرایش و استایل‌دهی ویجت‌ها در المنتور.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_widget_catalog' ) ) :
	/**
	 * فهرست ویجت‌ها با گروه، آیکن و توضیح.
	 *
	 * @return array<string, array{title:string,group:string,icon:string,desc:string,woo:bool}>
	 */
	function zc_widget_catalog() {
		$meta = array(
			'hero'              => array( 'home', 'fa-solid fa-star', __( 'بخش اول صفحه با عنوان بزرگ، تصویر، دکمه‌ها، آمار و کارت شناور.', 'zarincoach' ) ),
			'heading'           => array( 'home', 'fa-solid fa-heading', __( 'برچسب کوتاه، عنوان و زیرعنوان برای شروع هر بخش.', 'zarincoach' ) ),
			'about'             => array( 'home', 'fa-solid fa-user', __( 'تصویر، معرفی، نشان تجربه، فهرست ویژگی‌ها و دکمه.', 'zarincoach' ) ),
			'services'          => array( 'home', 'fa-solid fa-briefcase', __( 'کارت خدمات از بخش «خدمات سایت» یا ورود دستی.', 'zarincoach' ) ),
			'schema'            => array( 'home', 'fa-solid fa-brain', __( 'کارت‌های شماره‌دار طرحواره‌ها با باکس دعوت به ارزیابی.', 'zarincoach' ) ),
			'process'           => array( 'home', 'fa-solid fa-route', __( 'مراحل همراهی به شکل خط زمانی.', 'zarincoach' ) ),
			'stats'             => array( 'home', 'fa-solid fa-chart-simple', __( 'شمارنده‌های متحرک با ارقام فارسی.', 'zarincoach' ) ),
			'testimonials'      => array( 'home', 'fa-solid fa-quote-right', __( 'نظر مراجعان به شکل شبکه یا اسلایدر.', 'zarincoach' ) ),
			'pricing'           => array( 'home', 'fa-solid fa-tags', __( 'جدول بسته‌ها و قیمت‌ها با بسته‌ی ویژه.', 'zarincoach' ) ),
			'faq'               => array( 'home', 'fa-solid fa-circle-question', __( 'آکاردئون پرسش‌ها با اسکیمای FAQ.', 'zarincoach' ) ),
			'cta'               => array( 'home', 'fa-solid fa-bullhorn', __( 'پنل دعوت به اقدام با دکمه‌ها یا فرم.', 'zarincoach' ) ),
			'marquee'           => array( 'home', 'fa-solid fa-text-width', __( 'نوار لغزنده‌ی کلیدواژه‌ها.', 'zarincoach' ) ),
			'contact'           => array( 'home', 'fa-solid fa-address-card', __( 'راه‌های ارتباط، پیام‌رسان‌ها و نقشه.', 'zarincoach' ) ),
			'form'              => array( 'home', 'fa-solid fa-envelope-open-text', __( 'فرم تماس/رزرو داخلی یا شورت‌کد دلخواه.', 'zarincoach' ) ),
			'list'              => array( 'content', 'fa-solid fa-list-check', __( 'فهرست تیک‌دار، خط‌تیره یا کارتی.', 'zarincoach' ) ),
			'text'              => array( 'content', 'fa-solid fa-align-right', __( 'متن و محتوای بلند با فهرست مطالب و کادر اطلاعیه.', 'zarincoach' ) ),
			'toc'               => array( 'content', 'fa-solid fa-list-ol', __( 'فهرست مطالب خودکار از تیترهای صفحه.', 'zarincoach' ) ),
			'posts'             => array( 'content', 'fa-solid fa-newspaper', __( 'آخرین نوشته‌ها با چند طرح کارت و صفحه‌بندی.', 'zarincoach' ) ),
			'schemas'           => array( 'content', 'fa-solid fa-book-open', __( 'کتابخانه‌ی کامل طرحواره‌ها با فیلتر و جستجو.', 'zarincoach' ) ),
			'resume-hero'       => array( 'resume', 'fa-solid fa-id-badge', __( 'معرفی حرفه‌ای بالای صفحه‌ی رزومه.', 'zarincoach' ) ),
			'skills'            => array( 'resume', 'fa-solid fa-lightbulb', __( 'کارت‌های حوزه‌های تخصصی.', 'zarincoach' ) ),
			'timeline'          => array( 'resume', 'fa-solid fa-clock-rotate-left', __( 'خط زمانی سوابق کاری.', 'zarincoach' ) ),
			'courses'           => array( 'resume', 'fa-solid fa-graduation-cap', __( 'دوره‌ها و گواهی‌ها با فیلتر و خلاصه‌ی خودکار.', 'zarincoach' ) ),
			'book'              => array( 'resume', 'fa-solid fa-book', __( 'معرفی کتاب با جلد سه‌بعدی.', 'zarincoach' ) ),
			'site-header'       => array( 'layout', 'fa-solid fa-window-maximize', __( 'سربرگ سایت: لوگو، منو، جستجو، حالت تیره و دکمه.', 'zarincoach' ) ),
			'site-footer'       => array( 'layout', 'fa-solid fa-grip-lines', __( 'پاورقی سایت: معرفی، ستون‌ها، تماس و نمادهای اعتماد.', 'zarincoach' ) ),
			'footer-about'      => array( 'footer', 'fa-solid fa-user-tie', __( 'پانوشت — درباره من: نام/لوگو، متن معرفی و شبکه‌های اجتماعی.', 'zarincoach' ) ),
			'footer-location'   => array( 'footer', 'fa-solid fa-location-dot', __( 'پانوشت — لوکیشن: تلفن‌ها، ایمیل، نشانی، ساعات کاری و نقشه.', 'zarincoach' ) ),
			'footer-menu'       => array( 'footer', 'fa-solid fa-list-ul', __( 'پانوشت — منوها: فهرست وردپرس، خدمات سایت یا نوشته‌ها با عنوان دلخواه.', 'zarincoach' ) ),
			'footer-domain'     => array( 'footer', 'fa-solid fa-globe', __( 'پانوشت — دامنه رسمی: اعلان دامنه، هشدار اورژانس و شماره مجوزها.', 'zarincoach' ) ),
			'footer-trust'      => array( 'footer', 'fa-solid fa-shield-halved', __( 'پانوشت — نمادهای اعتماد: اینماد، ساماندهی و درگاه‌های پرداخت.', 'zarincoach' ) ),
			'footer-nav'        => array( 'footer', 'fa-solid fa-link', __( 'پانوشت — منوی قوانین: ردیف پیوندهای قوانین و مقررات.', 'zarincoach' ) ),
			'footer-copyright'  => array( 'footer', 'fa-solid fa-copyright', __( 'پانوشت — کپی‌رایت: خط جداکننده، سال، نام سایت و اعتبار طراح.', 'zarincoach' ) ),
			'page-title'        => array( 'layout', 'fa-solid fa-heading', __( 'عنوان و مسیر راهنمای برگه‌های داخلی.', 'zarincoach' ) ),
			'trust-badges'      => array( 'layout', 'fa-solid fa-shield-halved', __( 'نمادهای اعتماد (اینماد، ساماندهی، درگاه پرداخت…).', 'zarincoach' ) ),
			'products'          => array( 'shop', 'fa-solid fa-bag-shopping', __( 'شبکه یا اسلایدر محصولات.', 'zarincoach' ) ),
			'product-cats'      => array( 'shop', 'fa-solid fa-layer-group', __( 'دسته‌بندی‌های محصول.', 'zarincoach' ) ),
			'product-spotlight' => array( 'shop', 'fa-solid fa-gem', __( 'معرفی ویژه‌ی یک محصول با شمارش معکوس.', 'zarincoach' ) ),
			'shop-promo'        => array( 'shop', 'fa-solid fa-percent', __( 'بنر تخفیف با کد قابل کپی.', 'zarincoach' ) ),
			'shop-benefits'     => array( 'shop', 'fa-solid fa-truck-fast', __( 'مزایای خرید (ارسال، پشتیبانی، ضمانت…).', 'zarincoach' ) ),
		);

		$titles = ( class_exists( 'ZC_Elementor' ) && zc_is_elementor_active() ) ? ZC_Elementor::instance()->get_widgets_meta() : array();
		$out    = array();
		foreach ( $meta as $slug => $m ) {
			$out[ $slug ] = array(
				'title' => isset( $titles[ $slug ]['title'] ) ? $titles[ $slug ]['title'] : $slug,
				'group' => $m[0],
				'icon'  => $m[1],
				'desc'  => $m[2],
				'woo'   => ! empty( $titles[ $slug ]['woo'] ),
			);
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_widget_groups' ) ) :
	/**
	 * گروه‌های ویجت.
	 *
	 * @return array<string, string>
	 */
	function zc_widget_groups() {
		return array(
			'home'    => __( 'بخش‌های اصلی صفحه', 'zarincoach' ),
			'content' => __( 'محتوا و مجله', 'zarincoach' ),
			'resume'  => __( 'رزومه و معرفی حرفه‌ای', 'zarincoach' ),
			'layout'  => __( 'سربرگ، پاورقی و ساختار', 'zarincoach' ),
			'footer'  => __( 'اجزای پانوشت (ویجت‌های جدا)', 'zarincoach' ),
			'shop'    => __( 'فروشگاه (ووکامرس)', 'zarincoach' ),
		);
	}
endif;

if ( ! function_exists( 'zc_widget_usage' ) ) :
	/**
	 * تعداد و شناسه‌ی نوشته‌هایی که هر ویجت در آن‌ها استفاده شده است.
	 *
	 * نتیجه در گزینه‌ی zc_widget_usage ذخیره می‌شود تا ثبت ویجت‌ها در هر درخواست بدون کوئری انجام شود.
	 *
	 * @param bool $refresh محاسبه‌ی دوباره.
	 * @return array<string, int[]> slug => شناسه‌ی نوشته‌ها.
	 */
	function zc_widget_usage( $refresh = false ) {
		$cached = get_option( 'zc_widget_usage', null );
		if ( ! $refresh && is_array( $cached ) && isset( $cached['map'] ) ) {
			return (array) $cached['map'];
		}

		global $wpdb;
		$map = array();
		foreach ( array_keys( zc_widget_catalog() ) as $slug ) {
			$like = '%' . $wpdb->esc_like( '"widgetType":"zc-' . $slug . '"' ) . '%';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$ids          = $wpdb->get_col(
				$wpdb->prepare(
					"SELECT pm.post_id FROM {$wpdb->postmeta} pm INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
					WHERE pm.meta_key = '_elementor_data' AND p.post_type <> 'revision'
					AND p.post_status IN ('publish','draft','private','future','pending') AND pm.meta_value LIKE %s",
					$like
				)
			);
			$map[ $slug ] = array_map( 'intval', (array) $ids );
		}
		update_option(
			'zc_widget_usage',
			array(
				'map'  => $map,
				'time' => time(),
			),
			false
		);
		return $map;
	}
endif;

// به‌روزرسانی آمار استفاده پس از ذخیره‌ی هر سند المنتور یا حذف نوشته.
add_action(
	'elementor/document/after_save',
	static function () {
		delete_option( 'zc_widget_usage' );
	}
);
add_action(
	'deleted_post',
	static function () {
		delete_option( 'zc_widget_usage' );
	}
);

if ( ! function_exists( 'zc_widget_should_load' ) ) :
	/**
	 * آیا ویجت باید ثبت شود؟ خاموش + بدون استفاده = خیر.
	 *
	 * اگر آمار استفاده هنوز محاسبه نشده باشد، برای اطمینان ثبت می‌شود.
	 *
	 * @param string $slug    نامک ویجت (بدون zc-).
	 * @param bool   $enabled وضعیت روشن/خاموش.
	 * @return bool
	 */
	function zc_widget_should_load( $slug, $enabled ) {
		if ( $enabled ) {
			return true;
		}
		// در ویرایشگر و پیش‌نمایش همیشه ثبت می‌شود تا صفحه‌ی در حال ویرایش خراب نشود.
		if ( is_admin() || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return true;
		}
		// نقشه‌ی استفاده با اسلاگِ بدون پیشوند (zc-widget-catalog) کلید خورده است.
		$key    = preg_replace( '/^zc-/', '', (string) $slug );
		$cached = get_option( 'zc_widget_usage', null );
		if ( ! is_array( $cached ) || ! isset( $cached['map'][ $key ] ) ) {
			return true;
		}
		return ! empty( $cached['map'][ $key ] );
	}
endif;

if ( ! function_exists( 'zc_widgets_state' ) ) :
	/**
	 * وضعیت روشن/خاموش همه‌ی ویجت‌ها.
	 *
	 * @return array<string, bool>
	 */
	function zc_widgets_state() {
		$state = array();
		foreach ( array_keys( zc_widget_catalog() ) as $slug ) {
			$state[ $slug ] = ( class_exists( 'ZC_Elementor' ) && zc_is_elementor_active() ) ? ZC_Elementor::instance()->is_widget_enabled( $slug ) : true;
		}
		return $state;
	}
endif;

/* ---------------------------------------------------------------------
 * ذخیره
 * ------------------------------------------------------------------- */

add_action( 'admin_post_zc_widgets_save', 'zc_handle_widgets_save' );

if ( ! function_exists( 'zc_handle_widgets_save' ) ) :
	/**
	 * ذخیره‌ی وضعیت ویجت‌ها در همان گزینه‌ی پنل (elementor_widgets).
	 *
	 * @return void
	 */
	function zc_handle_widgets_save() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی کافی ندارید.', 'zarincoach' ) );
		}
		check_admin_referer( 'zc_widgets_save' );

		$posted = isset( $_POST['widgets'] ) && is_array( $_POST['widgets'] ) ? array_map( 'sanitize_key', array_keys( wp_unslash( $_POST['widgets'] ) ) ) : array(); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$value  = array();
		foreach ( array_keys( zc_widget_catalog() ) as $slug ) {
			$value[ $slug ] = in_array( $slug, $posted, true ) ? '1' : '0';
		}

		$options                      = get_option( ZC_OPT, array() );
		$options                      = is_array( $options ) ? $options : array();
		$options['elementor_widgets'] = $value;
		update_option( ZC_OPT, $options );

		zc_widget_usage( true );
		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		$off = count( array_filter( $value, static function ( $v ) { return '0' === $v; } ) );
		wp_safe_redirect(
			add_query_arg(
				array(
					'page'       => 'zc-widgets',
					'zc_widgets' => 'saved',
					'off'        => $off,
				),
				admin_url( 'admin.php' )
			)
		);
		exit;
	}
endif;

/* ---------------------------------------------------------------------
 * صفحه
 * ------------------------------------------------------------------- */

if ( ! function_exists( 'zc_render_widgets_page' ) ) :
	/**
	 * خروجی صفحه‌ی مدیریت ویجت‌ها.
	 *
	 * @return void
	 */
	function zc_render_widgets_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$elementor = zc_is_elementor_active();
		$woo       = class_exists( 'WooCommerce' );
		$catalog   = zc_widget_catalog();
		$groups    = zc_widget_groups();
		$state     = zc_widgets_state();
		$usage     = $elementor ? zc_widget_usage( true ) : array();
		$on        = count( array_filter( $state ) );
		$used      = count( array_filter( $usage ) );
		$total     = count( $catalog );

		$side  = '<p class="zc-tool-side-title">' . esc_html__( 'میان‌برها', 'zarincoach' ) . '</p>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'admin.php?page=zc-options' ) ) . '"><i class="fa-solid fa-sliders" aria-hidden="true"></i>' . esc_html__( 'تنظیمات قالب', 'zarincoach' ) . '</a>';
		$front = (int) get_option( 'page_on_front' );
		if ( $elementor && $front ) {
			$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'post.php?post=' . $front . '&action=elementor' ) ) . '"><i class="fa-brands fa-elementor" aria-hidden="true"></i>' . esc_html__( 'ویرایش خانه با المنتور', 'zarincoach' ) . '</a>';
		}
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'admin.php?page=zc-layout' ) ) . '"><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i>' . esc_html__( 'سربرگ و پاورقی', 'zarincoach' ) . '</a>';

		zc_tool_shell_open(
			'zc-widgets',
			array(
				'title'    => __( 'مدیریت ویجت‌ها', 'zarincoach' ),
				'subtitle' => __( 'روشن/خاموش کردن و راهنمای ویرایش', 'zarincoach' ),
				'icon'     => 'fa-solid fa-cubes',
				'nav'      => array(
					'widgets' => array( __( 'ویجت‌ها', 'zarincoach' ), 'fa-solid fa-toggle-on', zc_tool_num( $on ) . '/' . zc_tool_num( $total ), $on === $total ? 'ok' : 'warn' ),
					'guide'   => array( __( 'راهنمای ویرایش در المنتور', 'zarincoach' ), 'fa-regular fa-lightbulb' ),
				),
				'actions'  => $elementor ? '<button type="submit" form="zc-widgets-form" class="zc-btn zc-btn--primary"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i><span>' . esc_html__( 'ذخیره‌ی تغییرات', 'zarincoach' ) . '</span></button>' : '',
				'side'     => $side,
			)
		);

		/* ---------------------------------------------------------------- ویجت‌ها */
		zc_tool_pane_open( 'widgets', __( 'ویجت‌های اختصاصی', 'zarincoach' ), __( 'هر ویجتی را که لازم ندارید خاموش کنید تا پنل المنتور خلوت‌تر و سایت سبک‌تر شود.', 'zarincoach' ) );

		// phpcs:ignore WordPress.Security.NonceVerification
		if ( isset( $_GET['zc_widgets'] ) && 'saved' === $_GET['zc_widgets'] ) {
			$off = isset( $_GET['off'] ) ? absint( $_GET['off'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
			echo zc_tool_alert( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'ok',
				__( 'تغییرات ذخیره شد.', 'zarincoach' ),
				$off ? sprintf( /* translators: %s: تعداد */ __( '%s ویجت خاموش است. اگر ویرایشگر المنتور باز است، یک‌بار آن را تازه کنید.', 'zarincoach' ), zc_tool_num( $off ) ) : __( 'همه‌ی ویجت‌ها روشن هستند.', 'zarincoach' )
			);
		}

		if ( ! $elementor ) {
			echo zc_tool_alert( 'warn', __( 'المنتور فعال نیست.', 'zarincoach' ), __( 'ویجت‌های اختصاصی زرین‌کوچ با المنتور کار می‌کنند؛ پس از فعال کردن المنتور از همین صفحه مدیریتشان کنید.', 'zarincoach' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			zc_tool_pane_close();
		} else {
			?>
			<div class="zc-wm-stats">
				<div class="zc-wm-stat"><span class="zc-wm-stat-icon is-ok"><i class="fa-solid fa-toggle-on" aria-hidden="true"></i></span><div><strong data-zc-wm-on><?php echo esc_html( zc_tool_num( $on ) ); ?></strong><small><?php esc_html_e( 'ویجت روشن', 'zarincoach' ); ?></small></div></div>
				<div class="zc-wm-stat"><span class="zc-wm-stat-icon is-muted"><i class="fa-solid fa-toggle-off" aria-hidden="true"></i></span><div><strong data-zc-wm-off><?php echo esc_html( zc_tool_num( $total - $on ) ); ?></strong><small><?php esc_html_e( 'ویجت خاموش', 'zarincoach' ); ?></small></div></div>
				<div class="zc-wm-stat"><span class="zc-wm-stat-icon is-info"><i class="fa-regular fa-file-lines" aria-hidden="true"></i></span><div><strong><?php echo esc_html( zc_tool_num( $used ) ); ?></strong><small><?php esc_html_e( 'ویجت در صفحه‌ها استفاده شده', 'zarincoach' ); ?></small></div></div>
			</div>

			<form id="zc-widgets-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" class="zc-wm-form">
				<input type="hidden" name="action" value="zc_widgets_save">
				<?php wp_nonce_field( 'zc_widgets_save' ); ?>

				<div class="zc-wm-toolbar">
					<label class="zc-wm-search">
						<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
						<span class="screen-reader-text"><?php esc_html_e( 'جستجوی ویجت', 'zarincoach' ); ?></span>
						<input type="search" data-zc-wm-search placeholder="<?php esc_attr_e( 'جستجوی ویجت… (مثلاً «کارت» یا «فروشگاه»)', 'zarincoach' ); ?>">
					</label>
					<div class="zc-wm-filters" role="group" aria-label="<?php esc_attr_e( 'فیلتر', 'zarincoach' ); ?>">
						<button type="button" class="zc-wm-chip is-active" data-zc-wm-filter="all"><?php esc_html_e( 'همه', 'zarincoach' ); ?></button>
						<button type="button" class="zc-wm-chip" data-zc-wm-filter="on"><?php esc_html_e( 'روشن', 'zarincoach' ); ?></button>
						<button type="button" class="zc-wm-chip" data-zc-wm-filter="off"><?php esc_html_e( 'خاموش', 'zarincoach' ); ?></button>
						<button type="button" class="zc-wm-chip" data-zc-wm-filter="unused"><?php esc_html_e( 'استفاده‌نشده', 'zarincoach' ); ?></button>
					</div>
					<div class="zc-wm-bulk">
						<button type="button" class="zc-btn zc-btn--ghost zc-btn--sm" data-zc-wm-bulk="on"><i class="fa-solid fa-check-double" aria-hidden="true"></i><span><?php esc_html_e( 'روشن کردن همه', 'zarincoach' ); ?></span></button>
						<button type="button" class="zc-btn zc-btn--ghost zc-btn--sm" data-zc-wm-bulk="unused"><i class="fa-solid fa-broom" aria-hidden="true"></i><span><?php esc_html_e( 'خاموش کردن استفاده‌نشده‌ها', 'zarincoach' ); ?></span></button>
					</div>
				</div>

				<?php foreach ( $groups as $gkey => $glabel ) : ?>
					<?php
					$items = array_filter(
						$catalog,
						static function ( $w ) use ( $gkey ) {
							return $w['group'] === $gkey;
						}
					);
					if ( empty( $items ) ) {
						continue;
					}
					?>
					<section class="zc-wm-group" data-zc-wm-group>
						<h3 class="zc-tool-h zc-wm-group-title"><?php echo esc_html( $glabel ); ?> <small><?php echo esc_html( zc_tool_num( count( $items ) ) ); ?></small></h3>
						<div class="zc-wm-grid">
							<?php foreach ( $items as $slug => $w ) : ?>
								<?php
								$ids      = isset( $usage[ $slug ] ) ? (array) $usage[ $slug ] : array();
								$count    = count( $ids );
								$disabled = $w['woo'] && ! $woo;
								$enabled  = ! empty( $state[ $slug ] );
								?>
								<label class="zc-switch-card zc-wm-card<?php echo $disabled ? ' is-unavailable' : ''; ?>" data-zc-wm-item data-used="<?php echo esc_attr( (string) $count ); ?>" data-search="<?php echo esc_attr( $w['title'] . ' ' . $slug . ' ' . $w['desc'] . ' ' . $glabel ); ?>">
									<input type="checkbox" name="widgets[<?php echo esc_attr( $slug ); ?>]" value="1" <?php checked( $enabled ); ?>>
									<span class="zc-switch" aria-hidden="true"></span>
									<span class="zc-wm-icon" aria-hidden="true"><i class="<?php echo esc_attr( $w['icon'] ); ?>"></i></span>
									<span class="zc-switch-text">
										<strong><?php echo esc_html( $w['title'] ); ?></strong>
										<small><?php echo esc_html( $w['desc'] ); ?></small>
										<span class="zc-wm-meta">
											<code>zc-<?php echo esc_html( $slug ); ?></code>
											<?php if ( $disabled ) : ?>
												<?php echo zc_tool_tag( __( 'نیازمند ووکامرس', 'zarincoach' ), 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<?php elseif ( $count ) : ?>
												<?php
												/* translators: %s: تعداد صفحه */
												echo zc_tool_tag( sprintf( __( 'در %s صفحه', 'zarincoach' ), zc_tool_num( $count ) ), 'info', 'fa-regular fa-file-lines' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												?>
											<?php else : ?>
												<?php echo zc_tool_tag( __( 'استفاده نشده', 'zarincoach' ), 'muted' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<?php endif; ?>
										</span>
										<?php if ( $count ) : ?>
											<span class="zc-wm-note"><?php esc_html_e( 'اگر خاموش شود فقط از پنل المنتور پنهان می‌شود و صفحه‌های فعلی دست نمی‌خورند.', 'zarincoach' ); ?></span>
										<?php endif; ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>
					</section>
				<?php endforeach; ?>

				<p class="zc-wm-empty" data-zc-wm-empty hidden><?php esc_html_e( 'ویجتی با این مشخصات پیدا نشد.', 'zarincoach' ); ?></p>

				<div class="zc-wm-foot">
					<button type="submit" class="zc-btn zc-btn--primary zc-btn--lg"><i class="fa-solid fa-floppy-disk" aria-hidden="true"></i><span><?php esc_html_e( 'ذخیره‌ی تغییرات', 'zarincoach' ); ?></span></button>
					<p class="zc-muted"><?php esc_html_e( 'ویجت خاموشی که در هیچ صفحه‌ای به کار نرفته، دیگر بارگذاری نمی‌شود. همین تنظیم در «تنظیمات قالب ← المنتور» هم هست و همیشه با این صفحه یکی است.', 'zarincoach' ); ?></p>
				</div>
			</form>
			<?php
			zc_tool_pane_close();
		}

		/* ---------------------------------------------------------------- راهنما */
		zc_tool_pane_open( 'guide', __( 'راهنمای ویرایش ویجت‌ها در المنتور', 'zarincoach' ), __( 'همه‌ی اجزای هر ویجت از ویرایشگر بصری المنتور قابل تغییر است؛ این‌طوری:', 'zarincoach' ) );
		$guide = array(
			array( 'fa-solid fa-pen', __( 'زبانه‌ی «محتوا»', 'zarincoach' ), __( 'همه‌ی متن‌ها، تصاویر، پیوندها، آیکن‌ها و آیتم‌های تکرارشونده (کارت‌ها، مراحل، پرسش‌ها…). هر متنی که روی صفحه می‌بینید، اینجا قابل تغییر است؛ خالی گذاشتن یک فیلد یعنی استفاده از مقدار پنل تنظیمات قالب.', 'zarincoach' ) ),
			array( 'fa-solid fa-eye', __( 'بخش «نمایش اجزا»', 'zarincoach' ), __( 'در زبانه‌ی محتوا، هر جزء ویجت (برچسب، تصویر، نشان، آیکن، دکمه، توضیح…) یک کلید نمایش/پنهان دارد؛ تغییرش همان لحظه در ویرایشگر دیده می‌شود.', 'zarincoach' ) ),
			array( 'fa-solid fa-palette', __( 'زبانه‌ی «استایل»', 'zarincoach' ), __( 'برای هر جزء یک بخش جدا: تایپوگرافی (فونت، اندازه، وزن، ارتفاع خط)، رنگ و رنگ هاور، زمینه‌ی ساده یا گرادیان، حاشیه، گردی گوشه‌ها، سایه، فاصله‌ها، اندازه‌ی آیکن و قاب، نسبت ابعاد و فیلتر تصویر، تعداد ستون و فاصله‌ی شبکه در هر اندازه‌ی صفحه.', 'zarincoach' ) ),
			array( 'fa-solid fa-swatchbook', __( 'رنگ‌های این ویجت', 'zarincoach' ), __( 'آخرین بخش زبانه‌ی استایل: با تغییر «رنگ اصلی»، «رنگ تأکیدی»، «زمینه‌ی کارت‌ها» و… همه‌ی اجزای همان ویجت یک‌جا هم‌رنگ می‌شوند؛ بدون اینکه بقیه‌ی سایت تغییر کند.', 'zarincoach' ) ),
			array( 'fa-solid fa-ruler-combined', __( 'چیدمان و فاصله‌های بخش', 'zarincoach' ), __( 'فاصله‌ی بالا و پایین، عرض محتوا، حداقل ارتفاع، زمینه و حاشیه‌ی کل بخش، پنهان کردن اشکال تزئینی و خاموش کردن انیمیشن ظاهر شدن.', 'zarincoach' ) ),
			array( 'fa-solid fa-mobile-screen', __( 'واکنش‌گرا', 'zarincoach' ), __( 'کنار بیشتر تنظیم‌ها آیکن دسکتاپ/تبلت/موبایل هست؛ برای هر اندازه‌ی صفحه مقدار جدا بدهید.', 'zarincoach' ) ),
			array( 'fa-solid fa-circle-half-stroke', __( 'حالت تیره', 'zarincoach' ), __( 'رنگ‌هایی که در بخش‌های استایل می‌دهید در هر دو حالت اعمال می‌شوند؛ پالت اختصاصی ویجت به‌طور پیش‌فرض فقط در حالت روشن اعمال می‌شود تا خوانایی حالت تیره حفظ شود.', 'zarincoach' ) ),
			array( 'fa-solid fa-rotate-left', __( 'برگشت به پیش‌فرض', 'zarincoach' ), __( 'هر تنظیمی را پاک کنید، ظاهر پیش‌فرض قالب برمی‌گردد. همه‌ی تنظیم‌ها پیش‌فرض خالی دارند؛ پس تا چیزی را عوض نکنید، هیچ کد اضافه‌ای به صفحه اضافه نمی‌شود.', 'zarincoach' ) ),
		);
		echo '<div class="zc-tools-grid zc-wm-guide">';
		foreach ( $guide as $g ) {
			printf(
				'<div class="zc-tool-card"><span class="zc-tool-card-icon" aria-hidden="true"><i class="%1$s"></i></span><h3>%2$s</h3><p>%3$s</p></div>',
				esc_attr( $g[0] ),
				esc_html( $g[1] ),
				esc_html( $g[2] )
			);
		}
		echo '</div>';
		zc_tool_pane_close();

		zc_tool_shell_close();
	}
endif;
