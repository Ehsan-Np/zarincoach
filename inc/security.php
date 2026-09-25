<?php
/**
 * سخت‌سازی امنیتی سبک (بخش «امنیت» پنل تنظیمات).
 *
 * همه‌ی موارد با کلیدهای پنل قابل خاموش شدن هستند و هیچ‌کدام تغییری در پایگاه داده نمی‌دهند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_security_setup' ) ) :
	/**
	 * ثبت قلاب‌ها بر اساس تنظیمات.
	 *
	 * @return void
	 */
	function zc_security_setup() {
		/* ---------------- XML-RPC ---------------- */
		if ( zc_switch( 'sec_xmlrpc', true ) ) {
			add_filter( 'xmlrpc_enabled', '__return_false' );
			add_filter( 'pings_open', '__return_false', 20 );
			add_filter(
				'xmlrpc_methods',
				static function ( $methods ) {
					unset( $methods['pingback.ping'], $methods['pingback.extensions.getPingbacks'], $methods['system.multicall'] );
					return $methods;
				}
			);
			add_filter(
				'wp_headers',
				static function ( $headers ) {
					unset( $headers['X-Pingback'] );
					return $headers;
				}
			);
			remove_action( 'wp_head', 'rsd_link' );
		}

		/* ---------------- شناسایی نام کاربری ---------------- */
		if ( zc_switch( 'sec_user_enum', true ) ) {
			add_action( 'parse_request', 'zc_security_block_author_query', 1 );
			add_filter( 'rest_endpoints', 'zc_security_rest_users' );
			add_filter(
				'oembed_response_data',
				static function ( $data ) {
					unset( $data['author_name'], $data['author_url'] );
					return $data;
				}
			);
			add_filter(
				'wp_sitemaps_add_provider',
				static function ( $provider, $name ) {
					return 'users' === $name ? false : $provider;
				},
				10,
				2
			);
		}

		/* ---------------- پیام خطای ورود ---------------- */
		if ( zc_switch( 'sec_login_errors', true ) ) {
			add_filter( 'authenticate', 'zc_security_generic_login_error', 99 );
		}

		/* ---------------- ویرایشگر فایل ---------------- */
		if ( zc_switch( 'sec_file_edit', true ) ) {
			add_filter( 'map_meta_cap', 'zc_security_file_edit_caps', 10, 2 );
		}

		/* ---------------- سرآیندهای HTTP ---------------- */
		if ( zc_switch( 'sec_headers', true ) ) {
			add_action( 'send_headers', 'zc_security_headers' );
			add_action( 'login_init', 'zc_security_headers' );
		}
	}
endif;
add_action( 'after_setup_theme', 'zc_security_setup', 20 );

if ( ! function_exists( 'zc_security_block_author_query' ) ) :
	/**
	 * مسدود کردن ‎?author=N برای بازدیدکنندگان (راه رایج کشف نام کاربری).
	 *
	 * @return void
	 */
	function zc_security_block_author_query() {
		if ( is_admin() || is_user_logged_in() ) {
			return;
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['author'] ) && '' !== $_GET['author'] ) {
			wp_safe_redirect( home_url( '/' ), 301 );
			exit;
		}
	}
endif;

if ( ! function_exists( 'zc_security_rest_users' ) ) :
	/**
	 * حذف مسیرهای کاربران REST برای کسانی که اجازه‌ی فهرست کاربران ندارند.
	 *
	 * @param array $endpoints مسیرها.
	 * @return array
	 */
	function zc_security_rest_users( $endpoints ) {
		if ( current_user_can( 'list_users' ) ) {
			return $endpoints;
		}
		foreach ( array_keys( $endpoints ) as $route ) {
			if ( 0 === strpos( $route, '/wp/v2/users' ) && '/wp/v2/users/me' !== $route ) {
				unset( $endpoints[ $route ] );
			}
		}
		return $endpoints;
	}
endif;

if ( ! function_exists( 'zc_security_generic_login_error' ) ) :
	/**
	 * یکسان‌سازی خطای «نام کاربری/ایمیل/رمز اشتباه».
	 *
	 * @param WP_User|WP_Error|null $user نتیجه‌ی احراز هویت.
	 * @return WP_User|WP_Error|null
	 */
	function zc_security_generic_login_error( $user ) {
		if ( is_wp_error( $user ) ) {
			$codes = $user->get_error_codes();
			if ( array_intersect( $codes, array( 'invalid_username', 'invalid_email', 'incorrect_password' ) ) ) {
				return new WP_Error(
					'zc_login_failed',
					'<strong>' . esc_html__( 'خطا:', 'zarincoach' ) . '</strong> ' . esc_html__( 'نام کاربری یا رمز عبور درست نیست.', 'zarincoach' ) .
					' <a href="' . esc_url( wp_lostpassword_url() ) . '">' . esc_html__( 'رمز را فراموش کرده‌اید؟', 'zarincoach' ) . '</a>'
				);
			}
		}
		return $user;
	}
endif;

if ( ! function_exists( 'zc_security_file_edit_caps' ) ) :
	/**
	 * بستن ویرایشگر فایل قالب و افزونه (معادل DISALLOW_FILE_EDIT ولی قابل برگشت از پنل).
	 *
	 * @param string[] $caps توانایی‌های لازم.
	 * @param string   $cap  توانایی درخواستی.
	 * @return string[]
	 */
	function zc_security_file_edit_caps( $caps, $cap ) {
		if ( in_array( $cap, array( 'edit_themes', 'edit_plugins', 'edit_files' ), true ) ) {
			return array( 'do_not_allow' );
		}
		return $caps;
	}
endif;

if ( ! function_exists( 'zc_security_headers' ) ) :
	/**
	 * سرآیندهای امنیتی پایه.
	 *
	 * @return void
	 */
	function zc_security_headers() {
		if ( headers_sent() || is_admin() ) {
			return;
		}
		header( 'X-Content-Type-Options: nosniff' );
		header( 'Referrer-Policy: strict-origin-when-cross-origin' );
		header( 'Permissions-Policy: camera=(), microphone=(), geolocation=(), usb=()' );
		// پیش‌نمایش المنتور و سفارشی‌ساز هم‌مبدأ هستند و با SAMEORIGIN کار می‌کنند.
		header( 'X-Frame-Options: SAMEORIGIN' );
		// نسخه‌ی PHP را افشا نکن.
		if ( function_exists( 'header_remove' ) ) {
			header_remove( 'X-Powered-By' );
		}
	}
endif;
