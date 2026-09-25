<?php
/**
 * ابزارهای سایت: حالت تعمیر و نگهداری، اعلان کوکی و برندینگ صفحه‌ی ورود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* ================================================================== */
/* حالت تعمیر و نگهداری                                                */
/* ================================================================== */

if ( ! function_exists( 'zc_maintenance_active' ) ) :
	/**
	 * آیا حالت تعمیر روشن است؟
	 *
	 * @return bool
	 */
	function zc_maintenance_active() {
		return zc_switch( 'maint_enable', false );
	}
endif;

if ( ! function_exists( 'zc_maintenance_gate' ) ) :
	/**
	 * بستن سایت برای بازدیدکنندگان با کد ۵۰۳؛ مدیران و ویرایشگران سایت را عادی می‌بینند.
	 *
	 * @return void
	 */
	function zc_maintenance_gate() {
		if ( ! zc_maintenance_active() || current_user_can( 'edit_posts' ) ) {
			return;
		}
		if ( is_admin() || wp_doing_ajax() || wp_doing_cron() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || is_robots() ) {
			return;
		}
		if ( is_customize_preview() ) {
			return;
		}

		nocache_headers();
		status_header( 503 );
		header( 'Retry-After: 3600' );
		header( 'Content-Type: text/html; charset=' . get_bloginfo( 'charset' ) );
		include ZC_DIR . '/template-parts/maintenance.php';
		exit;
	}
endif;
add_action( 'template_redirect', 'zc_maintenance_gate', 0 );

if ( ! function_exists( 'zc_maintenance_admin_bar' ) ) :
	/**
	 * یادآوری در نوار مدیریت تا حالت تعمیر فراموش نشود.
	 *
	 * @param WP_Admin_Bar $bar نوار مدیریت.
	 * @return void
	 */
	function zc_maintenance_admin_bar( $bar ) {
		if ( ! zc_maintenance_active() || ! current_user_can( 'edit_posts' ) ) {
			return;
		}
		$bar->add_node(
			array(
				'id'     => 'zc-maintenance',
				'title'  => esc_html__( 'حالت تعمیر فعال است', 'zarincoach' ),
				'href'   => current_user_can( 'manage_options' ) ? admin_url( 'admin.php?page=zc-options' ) : false,
				'parent' => 'top-secondary',
				'meta'   => array( 'class' => 'zc-ab-maint' ),
			)
		);
	}
endif;
add_action( 'admin_bar_menu', 'zc_maintenance_admin_bar', 100 );

if ( ! function_exists( 'zc_maintenance_admin_bar_css' ) ) :
	/**
	 * رنگ هشدار برای گره نوار مدیریت.
	 *
	 * @return void
	 */
	function zc_maintenance_admin_bar_css() {
		if ( zc_maintenance_active() && is_admin_bar_showing() ) {
			wp_add_inline_style( 'admin-bar', '#wpadminbar .zc-ab-maint>.ab-item{background:#b42318!important;color:#fff!important;font-weight:700}' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_maintenance_admin_bar_css', 20 );
add_action( 'admin_enqueue_scripts', 'zc_maintenance_admin_bar_css', 20 );

/* ================================================================== */
/* اعلان کوکی                                                          */
/* ================================================================== */

if ( ! function_exists( 'zc_cookie_policy_url' ) ) :
	/**
	 * نشانی برگه‌ی سیاست کوکی‌ها (یا در نبود آن، حریم خصوصی).
	 *
	 * @return string
	 */
	function zc_cookie_policy_url() {
		$url = zc_page_url_by_key( 'cookies' );
		if ( untrailingslashit( $url ) === untrailingslashit( home_url( '/cookies/' ) ) && ! get_page_by_path( 'cookies' ) ) {
			$url = (string) get_privacy_policy_url();
		}
		return $url;
	}
endif;

if ( ! function_exists( 'zc_cookie_notice' ) ) :
	/**
	 * چاپ اعلان کوکی؛ ابتدا پنهان است و فقط اگر کاربر قبلاً نبسته باشد نمایش داده می‌شود (بدون جابه‌جایی چیدمان).
	 *
	 * @return void
	 */
	function zc_cookie_notice() {
		if ( ! zc_switch( 'cookie_enable', false ) || is_singular( 'elementor_library' ) || isset( $_GET['elementor-preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}
		$text   = trim( (string) zc_opt( 'cookie_text', '' ) );
		$button = (string) zc_opt( 'cookie_button', 'متوجه شدم' );
		$link   = trim( (string) zc_opt( 'cookie_link_text', '' ) );
		if ( '' === $text ) {
			return;
		}
		$url   = '' !== $link ? zc_cookie_policy_url() : '';
		$class = 'zc-cookie' . ( zc_switch( 'header_mobile_cta_enable', true ) ? ' has-mobile-cta' : '' );
		if ( zc_switch( 'float_enable', true ) && 'right' === (string) zc_opt( 'float_position', 'left' ) ) {
			$class .= ' is-float-right';
		}
		?>
		<div class="<?php echo esc_attr( $class ); ?>" id="zc-cookie" role="region" aria-label="<?php esc_attr_e( 'اعلان کوکی', 'zarincoach' ); ?>" hidden>
			<p class="zc-cookie-text"><?php echo esc_html( $text ); ?><?php if ( '' !== $url ) : ?> <a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $link ); ?></a><?php endif; ?></p>
			<button type="button" class="zc-btn zc-btn-primary zc-cookie-ok"><?php echo esc_html( $button ); ?></button>
		</div>
		<script>(function(){var k='zc-cookie-ok';try{if(Date.now()-(+localStorage.getItem(k)||0)<31536e6)return;}catch(e){}var b=document.getElementById('zc-cookie');if(!b)return;b.hidden=false;b.querySelector('button').addEventListener('click',function(){try{localStorage.setItem(k,String(Date.now()));}catch(e){}b.classList.add('is-out');setTimeout(function(){b.remove();},320);});})();</script>
		<?php
	}
endif;
add_action( 'wp_footer', 'zc_cookie_notice', 30 );

/* ================================================================== */
/* برندینگ صفحه‌ی ورود                                                 */
/* ================================================================== */

if ( ! function_exists( 'zc_login_brand_logo' ) ) :
	/**
	 * نشانی و ابعاد لوگو برای صفحه‌ی ورود.
	 *
	 * @return array{url:string,width:int,height:int}
	 */
	function zc_login_brand_logo() {
		$logo = (array) zc_opt( 'general_logo', array() );
		$url  = isset( $logo['url'] ) ? (string) $logo['url'] : '';
		$w    = isset( $logo['width'] ) ? (int) $logo['width'] : 0;
		$h    = isset( $logo['height'] ) ? (int) $logo['height'] : 0;
		if ( '' === $url && has_site_icon() ) {
			$url = (string) get_site_icon_url( 192 );
			$w   = 96;
			$h   = 96;
		}
		return array(
			'url'    => $url,
			'width'  => $w,
			'height' => $h,
		);
	}
endif;

if ( ! function_exists( 'zc_login_brand_styles' ) ) :
	/**
	 * استایل صفحه‌ی ورود با رنگ‌های پالت و فونت آراد.
	 *
	 * @return void
	 */
	function zc_login_brand_styles() {
		if ( ! zc_switch( 'admin_login_brand', true ) ) {
			return;
		}
		$p     = function_exists( 'zc_get_palette' ) ? zc_get_palette() : array();
		$l     = isset( $p['light'] ) ? $p['light'] : array();
		$pri   = isset( $l['primary'] ) ? $l['primary'] : '#1D3A72';
		$sec   = isset( $l['secondary'] ) ? $l['secondary'] : '#0B1B3A';
		$acc   = isset( $l['accent'] ) ? $l['accent'] : '#C9A45C';
		$base  = isset( $l['base'] ) ? $l['base'] : '#F5F7FB';
		$ink   = isset( $l['ink'] ) ? $l['ink'] : '#0E1A33';
		$line  = isset( $l['line'] ) ? $l['line'] : '#DCE3EF';
		$logo  = zc_login_brand_logo();
		$font  = ZC_URI . '/assets/fonts/AradFD-VF.woff2';
		$logo_css = '';
		if ( '' !== $logo['url'] ) {
			$w = $logo['width'] > 0 ? min( 220, $logo['width'] ) : 180;
			$h = ( $logo['width'] > 0 && $logo['height'] > 0 ) ? (int) round( $w * $logo['height'] / $logo['width'] ) : 72;
			$h = max( 40, min( 120, $h ) );
			$logo_css = sprintf(
				'.login h1 a{background-image:url("%1$s");background-size:contain;background-position:center;width:%2$dpx;height:%3$dpx;margin-bottom:18px}',
				esc_url_raw( $logo['url'] ),
				$w,
				$h
			);
		} else {
			$logo_css = '.login h1 a{background:none;width:auto;height:auto;text-indent:0;font-size:22px;font-weight:800;color:' . $sec . ';line-height:1.6}';
		}

		$css = '@font-face{font-family:"ZC Arad";src:url("' . esc_url_raw( $font ) . '") format("woff2");font-weight:100 900;font-display:swap}'
			. 'body.login{font-family:"ZC Arad",Tahoma,sans-serif;background:' . $base . ';background-image:radial-gradient(60% 50% at 100% 0%,' . $acc . '26,transparent 60%),radial-gradient(50% 50% at 0% 100%,' . $pri . '1f,transparent 60%);color:' . $ink . '}'
			. 'body.login input,body.login button,body.login .button{font-family:inherit}'
			. $logo_css
			. '.login form{border:1px solid ' . $line . ';border-radius:18px;box-shadow:0 24px 50px -30px ' . $sec . '66;padding:26px 24px}'
			. '.login label{font-size:13px;font-weight:600}'
			. '.login input[type=text],.login input[type=password],.login input[type=email]{border-radius:11px;border-color:' . $line . ';font-size:16px;padding:6px 12px}'
			. '.login input:focus{border-color:' . $pri . ';box-shadow:0 0 0 3px ' . $pri . '26}'
			. '.wp-core-ui .button-primary{background:' . $pri . ';border-color:' . $pri . ';border-radius:11px;padding:0 20px;min-height:40px;font-weight:700;text-shadow:none;box-shadow:none}'
			. '.wp-core-ui .button-primary:hover,.wp-core-ui .button-primary:focus{background:' . $sec . ';border-color:' . $sec . '}'
			. '.login .button.wp-hide-pw .dashicons{color:' . $pri . '}'
			. '.login #nav a,.login #backtoblog a,.login .privacy-policy-page-link a{color:' . $sec . '}'
			. '.login #nav a:hover,.login #backtoblog a:hover{color:' . $pri . '}'
			. '.login .message,.login .notice,.login #login_error{border-radius:12px;border-inline-start-color:' . $acc . '}'
			. '.login #login_error{border-inline-start-color:#b42318}';

		wp_register_style( 'zc-login', false, array(), ZC_VERSION );
		wp_enqueue_style( 'zc-login' );
		wp_add_inline_style( 'zc-login', $css );
	}
endif;
add_action( 'login_enqueue_scripts', 'zc_login_brand_styles' );

if ( ! function_exists( 'zc_login_brand_url' ) ) :
	/**
	 * پیوند لوگوی صفحه‌ی ورود به صفحه‌ی اصلی سایت.
	 *
	 * @param string $url نشانی پیش‌فرض.
	 * @return string
	 */
	function zc_login_brand_url( $url ) {
		return zc_switch( 'admin_login_brand', true ) ? home_url( '/' ) : $url;
	}
endif;
add_filter( 'login_headerurl', 'zc_login_brand_url' );

if ( ! function_exists( 'zc_login_brand_text' ) ) :
	/**
	 * متن لوگوی صفحه‌ی ورود.
	 *
	 * @param string $text متن پیش‌فرض.
	 * @return string
	 */
	function zc_login_brand_text( $text ) {
		return zc_switch( 'admin_login_brand', true ) ? get_bloginfo( 'name' ) : $text;
	}
endif;
add_filter( 'login_headertext', 'zc_login_brand_text' );
