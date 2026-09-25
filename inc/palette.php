<?php
/**
 * مدیریت پالت‌های رنگی قالب
 *
 * سه پالت سازمانی تعریف‌شده برای حوزه‌ی کوچینگ، طرحواره‌درمانی و آرامش ذهن
 * به همراه امکان سفارشی‌سازی کامل و حالت تاریک خودکار.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_palettes' ) ) :
	/**
	 * تعریف پالت‌های رنگی قالب.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function zc_palettes() {
		return array(
			'navy'     => array(
				'label' => __( 'اعتماد، عمق و حرفه‌ای‌گری (سرمه‌ای عمیق / طلایی شامپاینی)', 'zarincoach' ),
				'note'  => __( 'پالت پیش‌فرض دمو؛ ترکیب Deep Navy با طلایی ملایم برای حس اعتماد، اقتدار آرام و تخصص', 'zarincoach' ),
				'light' => array(
					'primary'      => '#1D3A72',
					'secondary'    => '#0B1B3A',
					'accent'       => '#C9A45C',
					'info'         => '#8DA9D4',
					'base'         => '#F5F7FB',
					'surface'      => '#FFFFFF',
					'surface2'     => '#EAEFF7',
					'ink'          => '#0E1A33',
					'muted'        => '#56627A',
					'line'         => '#DCE3EF',
					'primary_fg'   => '#FFFFFF',
					'secondary_fg' => '#FFFFFF',
				),
				'dark'  => array(
					'primary'      => '#D6B56E',
					'secondary'    => '#F2F5FB',
					'accent'       => '#9DB8E6',
					'info'         => '#8DA9D4',
					'base'         => '#081530',
					'surface'      => '#0E1E40',
					'surface2'     => '#13274F',
					'ink'          => '#DCE4F2',
					'muted'        => '#9AABC9',
					'line'         => '#20355F',
					'primary_fg'   => '#081530',
					'secondary_fg' => '#081530',
				),
			),
			'coaching' => array(
				'label' => __( 'انگیزه، هدف و کوچینگ موفقیت (طلایی / زیتونی)', 'zarincoach' ),
				'note'  => __( 'مناسب تمرکز بر آینده، اقدام و پتانسیل فردی', 'zarincoach' ),
				'light' => array(
					'primary'      => '#D4AF37',
					'secondary'    => '#3B4436',
					'accent'       => '#C08A7C',
					'info'         => '#95AEC2',
					'base'         => '#FAF9F6',
					'surface'      => '#FFFFFF',
					'surface2'     => '#F2F0EA',
					'ink'          => '#0F172A',
					'muted'        => '#6E6A62',
					'line'         => '#E6E2D9',
					'primary_fg'   => '#26251F',
					'secondary_fg' => '#FFFFFF',
				),
				'dark'  => array(
					'primary'      => '#E0BE4C',
					'secondary'    => '#E9E4D6',
					'accent'       => '#D2A093',
					'info'         => '#A7BFD2',
					'base'         => '#101411',
					'surface'      => '#181E19',
					'surface2'     => '#1F2621',
					'ink'          => '#EAE7DF',
					'muted'        => '#A3A79C',
					'line'         => '#2B332C',
					'primary_fg'   => '#1B1A15',
					'secondary_fg' => '#101411',
				),
			),
			'schema'   => array(
				'label' => __( 'خودآگاهی و کشف درون (تراکوتا / طوسی خنثی)', 'zarincoach' ),
				'note'  => __( 'مناسب کار روی طرحواره‌ها، پذیرش خود و حس امنیت', 'zarincoach' ),
				'light' => array(
					'primary'      => '#C08A7C',
					'secondary'    => '#4A403A',
					'accent'       => '#B5A89E',
					'info'         => '#95AEC2',
					'base'         => '#F5F5F0',
					'surface'      => '#FFFFFF',
					'surface2'     => '#EEEBE4',
					'ink'          => '#0F172A',
					'muted'        => '#6F6963',
					'line'         => '#E3DFD6',
					'primary_fg'   => '#FFFFFF',
					'secondary_fg' => '#FFFFFF',
				),
				'dark'  => array(
					'primary'      => '#D49E90',
					'secondary'    => '#EFE7E2',
					'accent'       => '#C7BCB3',
					'info'         => '#A7BFD2',
					'base'         => '#121010',
					'surface'      => '#1A1717',
					'surface2'     => '#221E1E',
					'ink'          => '#EFEAE4',
					'muted'        => '#AB9F97',
					'line'         => '#332D2B',
					'primary_fg'   => '#1A1210',
					'secondary_fg' => '#121010',
				),
			),
			'calm'     => array(
				'label' => __( 'صلح درونی و سلامت روان (آبی غبارآلود / سرمه‌ای)', 'zarincoach' ),
				'note'  => __( 'مناسب انتقال حس کاهش استرس، آرامش و وضوح ذهنی', 'zarincoach' ),
				'light' => array(
					'primary'      => '#95AEC2',
					'secondary'    => '#0F172A',
					'accent'       => '#D4AF37',
					'info'         => '#C08A7C',
					'base'         => '#F7F8FA',
					'surface'      => '#FFFFFF',
					'surface2'     => '#E5E7EB',
					'ink'          => '#0F172A',
					'muted'        => '#5B6472',
					'line'         => '#E1E5EB',
					'primary_fg'   => '#0F172A',
					'secondary_fg' => '#FFFFFF',
				),
				'dark'  => array(
					'primary'      => '#A7C0D4',
					'secondary'    => '#DCE6F2',
					'accent'       => '#E0BE4C',
					'info'         => '#D2A093',
					'base'         => '#0B1017',
					'surface'      => '#111823',
					'surface2'     => '#18202C',
					'ink'          => '#E6EBF2',
					'muted'        => '#93A1B3',
					'line'         => '#263140',
					'primary_fg'   => '#0B1017',
					'secondary_fg' => '#0B1017',
				),
			),
		);
	}
endif;

if ( ! function_exists( 'zc_contrast_color' ) ) :
	/**
	 * انتخاب رنگ متن مشکی یا سفید بر اساس روشنایی پس‌زمینه.
	 *
	 * @param string $hex رنگ پس‌زمینه.
	 * @return string
	 */
	function zc_contrast_color( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) ) {
			return '#FFFFFF';
		}
		$r = hexdec( substr( $hex, 0, 2 ) ) / 255;
		$g = hexdec( substr( $hex, 2, 2 ) ) / 255;
		$b = hexdec( substr( $hex, 4, 2 ) ) / 255;

		$linear = static function ( $c ) {
			return $c <= 0.03928 ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		};
		$luminance = 0.2126 * $linear( $r ) + 0.7152 * $linear( $g ) + 0.0722 * $linear( $b );

		return $luminance > 0.52 ? '#1B1B18' : '#FFFFFF';
	}
endif;

if ( ! function_exists( 'zc_mix_colors' ) ) :
	/**
	 * ترکیب دو رنگ با نسبت مشخص.
	 *
	 * @param string $from   رنگ پایه.
	 * @param string $to     رنگ مقصد.
	 * @param float  $amount سهم رنگ مقصد (۰ تا ۱).
	 * @return string
	 */
	function zc_mix_colors( $from, $to, $amount ) {
		$parse = static function ( $hex ) {
			$hex = ltrim( (string) $hex, '#' );
			if ( 3 === strlen( $hex ) ) {
				$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
			}
			if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
				return array( 0, 0, 0 );
			}
			return array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
		};

		$amount = max( 0, min( 1, (float) $amount ) );
		$a      = $parse( $from );
		$b      = $parse( $to );
		$out    = '#';
		for ( $i = 0; $i < 3; $i++ ) {
			$out .= str_pad( dechex( (int) round( $a[ $i ] + ( $b[ $i ] - $a[ $i ] ) * $amount ) ), 2, '0', STR_PAD_LEFT );
		}
		return strtoupper( $out );
	}
endif;

if ( ! function_exists( 'zc_get_palette' ) ) :
	/**
	 * رنگ‌های فعلی سایت (نسخه روشن و تاریک) با احتساب سفارشی‌سازی.
	 *
	 * @return array{light: array<string,string>, dark: array<string,string>}
	 */
	function zc_get_palette() {
		$palettes = zc_palettes();
		$preset   = (string) zc_opt( 'palette_preset', 'navy' );
		if ( ! array_key_exists( $preset, $palettes ) ) {
			$preset = 'navy';
		}

		$light = $palettes[ $preset ]['light'];
		$dark  = $palettes[ $preset ]['dark'];

		if ( 'custom' === (string) zc_opt( 'palette_mode', 'preset' ) ) {
			$custom_map = array(
				'primary'   => 'palette_custom_primary',
				'secondary' => 'palette_custom_secondary',
				'accent'    => 'palette_custom_accent',
				'info'      => 'palette_custom_info',
				'base'      => 'palette_custom_base',
				'surface'   => 'palette_custom_surface',
				'ink'       => 'palette_custom_ink',
				'muted'     => 'palette_custom_muted',
			);
			foreach ( $custom_map as $key => $option ) {
				$value = (string) zc_opt( $option, '' );
				if ( '' !== $value ) {
					$light[ $key ] = $value;
				}
			}

			$light['surface2']     = zc_adjust_brightness( $light['base'], -10 );
			$light['line']         = zc_adjust_brightness( $light['base'], -16 );
			$light['primary_fg']   = zc_contrast_color( $light['primary'] );
			$light['secondary_fg'] = zc_contrast_color( $light['secondary'] );

			$dark = array(
				'primary'      => zc_adjust_brightness( $light['primary'], 18 ),
				'secondary'    => zc_adjust_brightness( $light['secondary'], 118 ),
				'accent'       => zc_adjust_brightness( $light['accent'], 14 ),
				'info'         => zc_adjust_brightness( $light['info'], 14 ),
				'base'         => zc_adjust_brightness( $light['ink'], -18 ),
				'surface'      => zc_adjust_brightness( $light['ink'], -10 ),
				'surface2'     => zc_adjust_brightness( $light['ink'], -4 ),
				'ink'          => zc_adjust_brightness( $light['base'], -8 ),
				'muted'        => zc_adjust_brightness( $light['muted'], 44 ),
				'line'         => zc_adjust_brightness( $light['ink'], 34 ),
				'primary_fg'   => zc_contrast_color( zc_adjust_brightness( $light['primary'], 18 ) ),
				'secondary_fg' => zc_adjust_brightness( $light['ink'], -18 ),
			);
		}

		/**
		 * فیلتر نهایی پالت رنگی.
		 *
		 * @param array $palette رنگ‌های روشن و تاریک.
		 */
		return (array) apply_filters( 'zc_palette', array( 'light' => $light, 'dark' => $dark ) );
	}
endif;

if ( ! function_exists( 'zc_palette_css' ) ) :
	/**
	 * تولید متغیرهای CSS از پالت فعلی.
	 *
	 * @return string
	 */
	function zc_palette_css() {
		$palette = zc_get_palette();

		$build = static function ( $colors, $indent = '' ) {
			$css = '';
			foreach ( $colors as $key => $hex ) {
				$name = str_replace( '_', '-', $key );
				$css .= $indent . '--zc-' . $name . ':' . $hex . ';';
				$css .= $indent . '--zc-' . $name . '-rgb:' . zc_hex_to_rgb( $hex ) . ';';
			}
			return $css;
		};

		$css  = ':root{' . $build( $palette['light'] ) . '--zc-font:"Arad VF";}';
		$css .= '[data-theme="dark"]{' . $build( $palette['dark'] ) . '}';

		$css .= zc_inverse_css();

		return $css;
	}
endif;

if ( ! function_exists( 'zc_inverse_css' ) ) :
	/**
	 * متغیرهای بخش‌های «معکوس» (تُن تیره / سرمه‌ای).
	 *
	 * متغیرهای نسخه تاریک پالت به صورت محلی روی بخش اعمال می‌شوند تا همه‌ی اجزای
	 * داخلی (عنوان، کارت، دکمه، فرم و ...) بدون نیاز به کلاس اضافه هماهنگ شوند.
	 * رنگ پس‌زمینه از رنگ ثانویه پالت (در پالت سرمه‌ای: Deep Navy) گرفته می‌شود.
	 *
	 * @return string
	 */
	function zc_inverse_css() {
		$palette = zc_get_palette();

		$build = static function ( $bg ) use ( $palette ) {
			$colors               = $palette['dark'];
			$colors['base']       = $bg;
			$colors['surface']    = zc_mix_colors( $bg, '#FFFFFF', 0.06 );
			$colors['surface2']   = zc_mix_colors( $bg, '#FFFFFF', 0.10 );
			$colors['line']       = zc_mix_colors( $bg, '#FFFFFF', 0.15 );
			$colors['inverse_bg'] = $bg;

			$css = '';
			foreach ( $colors as $key => $hex ) {
				$name = str_replace( '_', '-', $key );
				$css .= '--zc-' . $name . ':' . $hex . ';--zc-' . $name . '-rgb:' . zc_hex_to_rgb( $hex ) . ';';
			}
			return $css;
		};

		$light_bg = isset( $palette['light']['secondary'] ) ? (string) $palette['light']['secondary'] : '#0B1B3A';
		// اگر رنگ ثانویه روشن انتخاب شده باشد، از رنگ متن اصلی به عنوان پس‌زمینه تیره استفاده می‌شود.
		if ( '#FFFFFF' === zc_contrast_color( $light_bg ) ) {
			$bg = $light_bg;
		} else {
			$bg = isset( $palette['light']['ink'] ) ? (string) $palette['light']['ink'] : '#0E1A33';
		}

		$css  = '.zc-tone-inverse{' . $build( $bg ) . '}';
		$css .= '[data-theme="dark"] .zc-tone-inverse{' . $build( zc_mix_colors( $palette['dark']['base'], '#FFFFFF', 0.035 ) ) . '}';

		return $css;
	}
endif;
