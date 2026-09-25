<?php
/**
 * بارگذاری Redux Framework به صورت توکار در قالب
 *
 * در صورتی که افزونه‌ی Redux Framework از قبل فعال باشد، همان نسخه استفاده می‌شود
 * تا تداخلی پیش نیاید. در غیر این صورت نسخه‌ی همراهِ قالب بارگذاری می‌گردد.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_load_redux_framework' ) ) :
	/**
	 * بارگذاری هسته‌ی Redux.
	 *
	 * @return bool
	 */
	function zc_load_redux_framework() {
		if ( class_exists( 'ReduxFramework', false ) ) {
			return true;
		}

		$framework = ZC_DIR . '/inc/redux/framework.php';

		if ( ! file_exists( $framework ) ) {
			return false;
		}

		// آدرس و مسیر نسخه‌ی توکار به صورت صریح تعیین می‌شود تا در هاست‌هایی که از
		// symlink یا ساختار پوشه‌ی غیراستاندارد استفاده می‌کنند، فایل‌های CSS/JS پنل به‌درستی بارگذاری شوند.
		add_filter(
			'redux/url',
			static function () {
				return trailingslashit( ZC_URI . '/inc/redux' );
			},
			99
		);
		add_filter(
			'redux/dir',
			static function () {
				return trailingslashit( ZC_DIR . '/inc/redux' );
			},
			99
		);

		require_once $framework;

		return class_exists( 'ReduxFramework', false );
	}
endif;

// بارگذاری در زمان لود فایل‌های قالب تا پیش از هوک init (ایجاد پنل‌ها) آماده باشد.
zc_load_redux_framework();

if ( ! function_exists( 'zc_redux_available' ) ) :
	/**
	 * بررسی در دسترس بودن Redux برای ساخت پنل.
	 *
	 * @return bool
	 */
	function zc_redux_available() {
		return class_exists( 'ReduxFramework', false ) && class_exists( 'Redux' ); // Redux API با autoload بارگذاری می‌شود.
	}
endif;

if ( ! function_exists( 'zc_redux_remove_welcome_page' ) ) :
	/**
	 * حذف صفحه‌ی تبلیغاتی خودِ Redux از پیشخوان (پنل حرفه‌ای قالب جایگزین آن است).
	 *
	 * @return void
	 */
	function zc_redux_remove_welcome_page() {
		if ( ! class_exists( 'Redux_Core', false ) ) {
			return;
		}

		if ( isset( Redux_Core::$welcome ) && is_object( Redux_Core::$welcome ) ) {
			remove_action( 'admin_menu', array( Redux_Core::$welcome, 'admin_menus' ) );
		}
	}
endif;
add_action( 'init', 'zc_redux_remove_welcome_page', 1000 );

if ( ! function_exists( 'zc_redux_missing_notice' ) ) :
	/**
	 * اطلاع‌رسانی در صورت نبود Redux.
	 *
	 * @return void
	 */
	function zc_redux_missing_notice() {
		if ( zc_redux_available() ) {
			return;
		}

		printf(
			'<div class="notice notice-warning"><p>%1$s <a href="%2$s" target="_blank" rel="noopener">%3$s</a></p></div>',
			esc_html__( 'پنل تنظیمات قالب زرین‌کوچ به Redux Framework نیاز دارد. لطفاً افزونه را نصب کنید:', 'zarincoach' ),
			esc_url( 'https://wordpress.org/plugins/redux-framework/' ),
			esc_html__( 'دانلود Redux Framework', 'zarincoach' )
		);
	}
endif;
