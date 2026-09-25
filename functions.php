<?php
/**
 * ZarinCoach — هسته‌ی اصلی قالب
 *
 * قالب وردپرس روان‌شناسی، کوچینگ و رشد فردی ویژه‌ی مریم جمالی (Maryam-Jamali.ir)
 * توسعه: احسان نادری‌پناه | زرین‌کد — Zarincode.com
 *
 * @package ZarinCoach
 * @version 2.1.1
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ *
 * ثابت‌های قالب
 * ------------------------------------------------------------------ */
define( 'ZC_VERSION', '2.1.1' );
define( 'ZC_DIR', get_template_directory() );
define( 'ZC_URI', get_template_directory_uri() );
define( 'ZC_OPT', 'zc_options' );       // کلید تنظیمات در جدول wp_options
define( 'ZC_TEXTDOMAIN', 'zarincoach' );
define( 'ZC_MIN_PHP', '7.4' );

/* ------------------------------------------------------------------ *
 * بارگذاری ماژول‌ها
 * ------------------------------------------------------------------ */
require_once ZC_DIR . '/inc/helpers.php';
require_once ZC_DIR . '/inc/icons.php';
require_once ZC_DIR . '/inc/template-tags.php';
require_once ZC_DIR . '/inc/toc.php';
require_once ZC_DIR . '/inc/share.php';
require_once ZC_DIR . '/inc/trust-badges.php';
require_once ZC_DIR . '/inc/setup.php';
require_once ZC_DIR . '/inc/palette.php';
require_once ZC_DIR . '/inc/redux-loader.php';   // بارگذاری Redux Framework توکار
require_once ZC_DIR . '/inc/options-panel.php';    // ساختار بخش‌ها و فیلدهای پنل
require_once ZC_DIR . '/inc/options.php';        // راه‌اندازی پنل تنظیمات
require_once ZC_DIR . '/inc/dynamic-css.php';
require_once ZC_DIR . '/inc/enqueue.php';
require_once ZC_DIR . '/inc/cpt.php';
require_once ZC_DIR . '/inc/schemas.php';
require_once ZC_DIR . '/inc/metaboxes.php';
require_once ZC_DIR . '/inc/seo.php';
require_once ZC_DIR . '/inc/seo-schema.php';
require_once ZC_DIR . '/inc/seo-metabox.php';
require_once ZC_DIR . '/inc/seo-yoast.php';
require_once ZC_DIR . '/inc/performance.php';
require_once ZC_DIR . '/inc/performance-elementor.php';
require_once ZC_DIR . '/inc/contact-form.php';
require_once ZC_DIR . '/inc/security.php';
require_once ZC_DIR . '/inc/site-tools.php';
require_once ZC_DIR . '/inc/elementor-home-builder.php';
require_once ZC_DIR . '/inc/demo-content.php';
require_once ZC_DIR . '/inc/demo-shop.php';
require_once ZC_DIR . '/inc/demo-schemas.php';
require_once ZC_DIR . '/inc/elementor/class-zc-elementor.php';
require_once ZC_DIR . '/inc/admin-shell.php';  // پوسته‌ی مشترک صفحه‌های ابزار
require_once ZC_DIR . '/inc/admin.php';
require_once ZC_DIR . '/inc/admin-layout.php'; // سربرگ و پاورقی

// ماژول فروشگاه: فقط وقتی ووکامرس فعال است.
if ( class_exists( 'WooCommerce' ) ) {
	foreach ( array( 'setup', 'loop', 'single', 'cart', 'account', 'admin', 'seo' ) as $zc_wc_file ) {
		require_once ZC_DIR . '/inc/woocommerce/' . $zc_wc_file . '.php';
	}
	unset( $zc_wc_file );
}

/* ------------------------------------------------------------------ *
 * ترجمه و متون قابل ترجمه
 * ------------------------------------------------------------------ */
if ( ! function_exists( 'zc_load_textdomain' ) ) :
	/**
	 * بارگذاری فایل‌های ترجمه.
	 *
	 * @return void
	 */
	function zc_load_textdomain() {
		load_theme_textdomain( ZC_TEXTDOMAIN, ZC_DIR . '/languages' );
	}
endif;
add_action( 'after_setup_theme', 'zc_load_textdomain', 5 );

/* ------------------------------------------------------------------ *
 * عرض محتوا (Content Width)
 * ------------------------------------------------------------------ */
if ( ! isset( $content_width ) ) {
	$content_width = 1200;
}

/* ------------------------------------------------------------------ *
 * فعال‌سازی قالب
 * ------------------------------------------------------------------ */
if ( ! function_exists( 'zc_theme_activation' ) ) :
	/**
	 * اقدامات پس از فعال‌سازی قالب.
	 *
	 * @return void
	 */
	function zc_theme_activation() {
		// پاک‌سازی کش تنظیمات و CSS پویا.
		delete_transient( function_exists( 'zc_field_defaults_key' ) ? zc_field_defaults_key() : 'zc_field_defaults_' . ZC_VERSION );
		delete_transient( 'zc_dynamic_css_' . ZC_VERSION );

		// تنظیمات پیش‌فرضِ وردپرس برای یک سایت شرکتی/شخصی حرفه‌ای.
		if ( '1' === (string) get_option( 'zc_first_activation_done' ) ) {
			return;
		}

		update_option( 'permalink_structure', '/%postname%/' );
		update_option( 'blogdescription', 'روان‌شناس الگوهای ذهنی و رفتاری در مسیر رشد' );
		update_option( 'timezone_string', 'Asia/Tehran' );
		update_option( 'start_of_week', 6 );
		update_option( 'posts_per_page', 9 );
		update_option( 'zc_first_activation_done', '1' );

		// ایجاد خودکار محتوای اولیه (صفحات، نوشته‌ها، منوها و خدمات).
		// فقط روی سایت‌های تازه؛ در سایت‌های دارای محتوا، دمو از «زرین‌کوچ ← نصب دمو» قابل نصب است.
		if ( function_exists( 'zc_install_demo_content' ) && function_exists( 'zc_site_is_fresh' ) && zc_site_is_fresh() ) {
			zc_install_demo_content( false );
		}

		flush_rewrite_rules();
	}
endif;
add_action( 'after_switch_theme', 'zc_theme_activation', 20 );

/* ------------------------------------------------------------------ *
 * غیرفعال‌سازی قالب
 * ------------------------------------------------------------------ */
if ( ! function_exists( 'zc_theme_deactivation' ) ) :
	/**
	 * پاک‌سازی کش‌ها هنگام تعویض قالب.
	 *
	 * @return void
	 */
	function zc_theme_deactivation() {
		delete_transient( function_exists( 'zc_field_defaults_key' ) ? zc_field_defaults_key() : 'zc_field_defaults_' . ZC_VERSION );
		delete_transient( 'zc_dynamic_css_' . ZC_VERSION );
		flush_rewrite_rules();
	}
endif;
add_action( 'switch_theme', 'zc_theme_deactivation' );
