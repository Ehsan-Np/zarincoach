<?php
/**
 * تولید و چاپ CSS پویا بر اساس تنظیمات پنل
 *
 * خروجی به صورت یک تگ <style> بسیار سبک در head چاپ می‌شود،
 * بنابراین هیچ درخواست اضافه‌ای ایجاد نمی‌کند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_dynamic_css' ) ) :
	/**
	 * ساخت CSS پویا (با کش transient).
	 *
	 * @return string
	 */
	function zc_dynamic_css() {
		$cached = get_transient( 'zc_dynamic_css_' . ZC_VERSION );
		if ( is_string( $cached ) && '' !== $cached && ! ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ) {
			return $cached;
		}

		$palette   = zc_get_palette();
		$build     = static function ( $colors ) {
			$out = '';
			foreach ( $colors as $key => $hex ) {
				$name = str_replace( '_', '-', $key );
				$out .= '--zc-' . $name . ':' . $hex . ';';
				$out .= '--zc-' . $name . '-rgb:' . zc_hex_to_rgb( $hex ) . ';';
			}
			return $out;
		};

		$font_family = zc_switch( 'typo_persian_digits', true ) ? 'Arad FD VF' : 'Arad VF';
		$radius      = (int) zc_opt( 'general_radius', 20 );
		$container   = zc_opt( 'general_container', '1200px' );
		$header_h    = (int) zc_opt( 'general_header_height', 76 );
		$base_size   = (float) zc_opt( 'typo_base_size', 16 );
		$line_height = (float) zc_opt( 'typo_line_height', 1.9 );
		$heading_wt  = (int) zc_opt( 'typo_heading_weight', 800 );
		$scale       = (string) zc_opt( 'typo_scale', 'normal' );

		$css  = ':root{';
		$css .= $build( $palette['light'] );
		$css .= '--zc-font:"' . $font_family . '";';
		$css .= '--zc-fs-base:' . $base_size . 'px;';
		$css .= '--zc-lh-base:' . $line_height . ';';
		$css .= '--zc-heading-weight:' . $heading_wt . ';';
		$css .= '--zc-container:' . $container . ';';
		$css .= '--zc-radius:' . $radius . 'px;';
		$css .= '--zc-radius-sm:' . max( 0, (int) round( $radius * 0.6 ) ) . 'px;';
		$css .= '--zc-header-h:' . $header_h . 'px;';
		$css .= '}';

		// v1.8: تراکم فاصله‌ی عمودی بخش‌ها (پیش‌فرض «فشرده» در خود main.css تعریف شده است).
		$spacing = (string) zc_opt( 'general_spacing', 'compact' );
		if ( 'balanced' === $spacing ) {
			$css .= ':root{--zc-sec-y:clamp(3rem,2.3rem + 2vw,4.5rem);--zc-sec-y-tight:clamp(2.5rem,2rem + 1.4vw,3.5rem);--zc-head-gap:clamp(1.75rem,1.4rem + 1vw,2.5rem);}';
		} elseif ( 'spacious' === $spacing ) {
			$css .= ':root{--zc-sec-y:clamp(3.5rem,2.5rem + 3vw,6rem);--zc-sec-y-tight:clamp(3rem,2.2rem + 2vw,4.5rem);--zc-head-gap:clamp(2rem,1.5rem + 1.4vw,3rem);}';
		}

		$css .= '[data-theme="dark"]{' . $build( $palette['dark'] ) . '}';

		// بخش‌های تیره (سرمه‌ای).
		$css .= zc_inverse_css();

		// اعمال فونت و وزن تیترها.
		$css .= 'body,.zc-prose{font-family:var(--zc-font),Tahoma,sans-serif;}';
		$css .= 'h1,h2,h3,h4,h5,h6,.zc-title,.zc-title-lg,.zc-display,.zc-stat-num,.zc-price-num{font-weight:var(--zc-heading-weight);}';

		// مقیاس تیترها.
		if ( 'compact' === $scale ) {
			$css .= '.zc-display{font-size:clamp(2.1rem,5vw,3.6rem)}.zc-title-lg{font-size:clamp(1.8rem,3.6vw,2.6rem)}.zc-title{font-size:clamp(1.55rem,2.9vw,2.05rem)}';
		} elseif ( 'large' === $scale ) {
			$css .= '.zc-display{font-size:clamp(2.9rem,7.4vw,6rem)}.zc-title-lg{font-size:clamp(2.4rem,5.4vw,4rem)}.zc-title{font-size:clamp(1.95rem,4vw,3rem)}';
		}

		// شکل و افکت دکمه‌ها.
		$btn_shape = (string) zc_opt( 'general_btn_shape', 'pill' );
		if ( 'rounded' === $btn_shape ) {
			$css .= '.zc-btn,.zc-btn-icon{border-radius:12px}';
		} elseif ( 'square' === $btn_shape ) {
			$css .= '.zc-btn,.zc-btn-icon{border-radius:3px}';
		}
		if ( ! zc_switch( 'general_btn_shine', true ) ) {
			$css .= '.zc-btn::after{display:none}';
		}
		if ( ! zc_switch( 'general_btn_shadow', true ) ) {
			$css .= '.zc-btn-primary{box-shadow:none}';
		}
		if ( 'left' !== (string) zc_opt( 'float_position', 'left' ) ) {
			$css .= '.zc-float{left:auto;right:1.25rem}.zc-float-panel{left:auto;right:0;transform-origin:bottom right}';
		}

		// عرض محتوا.
		$css .= '.zc-container,.elementor-section .elementor-container.zc-container{max-width:var(--zc-container)}';

		// بافت کاغذی.
		if ( ! zc_switch( 'palette_grain', true ) ) {
			$css .= '.zc-grain::before{display:none !important}';
		}

		// نوار اقدام ثابت موبایل.
		if ( zc_switch( 'header_mobile_cta_enable', true ) ) {
			$css .= 'body{padding-bottom:78px}@media(min-width:1024px){body{padding-bottom:0}}';
		}

		// کد CSS سفارشی مدیر.
		$custom = trim( (string) zc_opt( 'code_css', '' ) );
		if ( '' !== $custom ) {
			$custom = preg_replace( '#</?style[^>]*>#i', '', $custom );
			$css   .= "\n/* کد سفارشی مدیر */\n" . $custom;
		}

		/**
		 * فیلتر CSS پویای نهایی.
		 *
		 * @param string $css خروجی CSS.
		 */
		$css = (string) apply_filters( 'zc_dynamic_css', $css );

		set_transient( 'zc_dynamic_css_' . ZC_VERSION, $css, 12 * HOUR_IN_SECONDS );

		return $css;
	}
endif;

if ( ! function_exists( 'zc_output_dynamic_css' ) ) :
	/**
	 * چاپ CSS پویا در بخش head.
	 *
	 * @return void
	 */
	function zc_output_dynamic_css() {
		$css = zc_dynamic_css();
		if ( '' === $css ) {
			return;
		}
		echo '<style id="zc-dynamic-css" data-theme-style="dynamic">' . $css . '</style>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی CSS تولیدشده توسط خود قالب
	}
endif;
add_action( 'wp_head', 'zc_output_dynamic_css', 3 );

if ( ! function_exists( 'zc_no_js_theme_script' ) ) :
	/**
	 * جلوگیری از پرش تصویری هنگام تعویض حالت تاریک (بدون وابستگی به جاوااسکریپت).
	 *
	 * @return void
	 */
	function zc_no_js_theme_script() {
		$mode = (string) zc_opt( 'general_dark_mode', 'toggle' );
		if ( 'off' === $mode ) {
			return;
		}

		if ( 'enforce' === $mode ) {
			echo "<script>document.documentElement.setAttribute('data-theme','dark');</script>"; // phpcs:ignore
			return;
		}

		if ( 'auto' === $mode ) {
			echo "<script>(function(){try{var m=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';document.documentElement.setAttribute('data-theme',m);}catch(e){}})();</script>"; // phpcs:ignore
			return;
		}

		echo "<script>(function(){try{var s=localStorage.getItem('zc-theme');if(!s){s=window.matchMedia('(prefers-color-scheme: dark)').matches?'dark':'light';}document.documentElement.setAttribute('data-theme',s);}catch(e){}})();</script>"; // phpcs:ignore
	}
endif;
add_action( 'wp_head', 'zc_no_js_theme_script', 1 );
