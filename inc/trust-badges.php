<?php
/**
 * نمادهای اعتماد (اینماد، ساماندهی، درگاه‌های زرین‌پال و پی، نظام روان‌شناسی، دلخواه)
 *
 * یک رندرکننده‌ی مشترک برای ویجت المنتوری «نمادهای اعتماد» و نوار اعتماد پاورقی.
 * هر نماد سه حالت دارد: «کد رسمی» (HTML دریافتی از سامانه‌ی صادرکننده)، «تصویر آپلودی» و «طرح پیش‌فرض».
 * طرح پیش‌فرض عمداً یک کاشی خنثی است (نه کپی لوگوی رسمی)، چون نماد فقط با کد رسمی اعتبار دارد.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_trust_types' ) ) :
	/**
	 * انواع نماد و مقادیر پیش‌فرض آن‌ها.
	 *
	 * @return array
	 */
	function zc_trust_types() {
		return array(
			'enamad'    => array(
				'name'   => __( 'اینماد', 'zarincoach' ),
				'label'  => __( 'نماد اعتماد الکترونیکی', 'zarincoach' ),
				'icon'   => 'shield-check',
				'option' => 'legal_enamad',
				'link'   => 'https://enamad.ir',
			),
			'samandehi' => array(
				'name'   => __( 'ساماندهی', 'zarincoach' ),
				'label'  => __( 'نشان ملی ثبت رسانه‌های دیجیتال', 'zarincoach' ),
				'icon'   => 'badge-check',
				'option' => 'legal_samandehi',
				'link'   => 'https://samandehi.ir',
			),
			'zarinpal'  => array(
				'name'   => __( 'زرین‌پال', 'zarincoach' ),
				'label'  => __( 'درگاه پرداخت امن', 'zarincoach' ),
				'icon'   => 'credit-card',
				'option' => 'legal_zarinpal',
				'link'   => 'https://www.zarinpal.com',
			),
			'payir'     => array(
				'name'   => __( 'پی (Pay.ir)', 'zarincoach' ),
				'label'  => __( 'درگاه پرداخت امن', 'zarincoach' ),
				'icon'   => 'credit-card',
				'option' => 'legal_payir',
				'link'   => 'https://pay.ir',
			),
			'pco'       => array(
				'name'   => __( 'نظام روان‌شناسی', 'zarincoach' ),
				'label'  => __( 'عضو سازمان نظام روان‌شناسی', 'zarincoach' ),
				'icon'   => 'id-card',
				'option' => '',
				'link'   => '',
			),
			'custom'    => array(
				'name'   => __( 'نماد دلخواه', 'zarincoach' ),
				'label'  => '',
				'icon'   => 'award',
				'option' => '',
				'link'   => '',
			),
		);
	}
endif;

if ( ! function_exists( 'zc_trust_kses' ) ) :
	/**
	 * پاک‌سازی کد رسمی نمادها با فهرست سفید متناسب با کدهای اینماد/ساماندهی/درگاه‌ها.
	 *
	 * @param string $code کد.
	 * @return string
	 */
	function zc_trust_kses( $code ) {
		$common = array(
			'id'             => true,
			'class'          => true,
			'style'          => true,
			'title'          => true,
			'referrerpolicy' => true,
			'onclick'        => true,
		);
		$allowed = array(
			'a'      => $common + array( 'href' => true, 'target' => true, 'rel' => true ),
			'img'    => $common + array( 'src' => true, 'alt' => true, 'width' => true, 'height' => true, 'code' => true, 'loading' => true ),
			'script' => array( 'src' => true, 'type' => true, 'async' => true, 'defer' => true, 'id' => true, 'referrerpolicy' => true ),
			'div'    => $common,
			'span'   => $common,
			'center' => array(),
		);
		$clean = wp_kses( (string) $code, $allowed, array( 'http', 'https' ) );

		// اسکریپت فقط با src از دامنه‌های رسمی و بدون محتوای درون‌خطی.
		$clean = preg_replace_callback(
			'#<script\b([^>]*)>(.*?)</script>#is',
			static function ( $m ) {
				if ( '' !== trim( $m[2] ) || ! preg_match( '#\ssrc=(["\'])(https://[^"\']+)\1#i', $m[1], $src ) || ! zc_trust_host_ok( $src[2] ) ) {
					return '';
				}
				return '<script' . $m[1] . '></script>';
			},
			$clean
		);
		$clean = preg_replace( '#<script\b[^>]*>(?!.*?</script>)#is', '', (string) $clean );

		// onclick فقط برای باز کردن صفحه‌ی استعلام رسمی (مثل کد ساماندهی).
		$clean = preg_replace_callback(
			'#\sonclick=(["\'])(.*?)\1#is',
			static function ( $m ) {
				$js = html_entity_decode( $m[2], ENT_QUOTES | ENT_HTML5 );
				if ( preg_match( '#^\s*window\.open\(\s*[\'"](https://[^\'"]+)[\'"]\s*(,\s*[\'"][^\'"]*[\'"]\s*)*\)\s*;?\s*$#i', $js, $u ) && zc_trust_host_ok( $u[1] ) ) {
					return $m[0];
				}
				return '';
			},
			(string) $clean
		);

		return (string) $clean;
	}
endif;

if ( ! function_exists( 'zc_trust_host_ok' ) ) :
	/**
	 * آیا نشانی متعلق به یکی از مراجع رسمی نماد/درگاه است؟
	 *
	 * @param string $url نشانی.
	 * @return bool
	 */
	function zc_trust_host_ok( $url ) {
		$host = strtolower( (string) wp_parse_url( $url, PHP_URL_HOST ) );
		$ok   = apply_filters( 'zc_trust_allowed_hosts', array( 'enamad.ir', 'samandehi.ir', 'zarinpal.com', 'pay.ir', 'pcoiran.ir', 'shaparak.ir', 'ecunion.ir' ) );
		foreach ( (array) $ok as $domain ) {
			if ( $host === $domain || substr( $host, -strlen( '.' . $domain ) ) === '.' . $domain ) {
				return true;
			}
		}
		return false;
	}
endif;

if ( ! function_exists( 'zc_trust_default_items' ) ) :
	/**
	 * نمادهای پیش‌فرض (برای پاورقی و ویجت جدید).
	 *
	 * @return array
	 */
	function zc_trust_default_items() {
		return array(
			array( 'type' => 'enamad', 'mode' => 'code' ),
			array( 'type' => 'samandehi', 'mode' => 'code' ),
			array( 'type' => 'zarinpal', 'mode' => 'code' ),
			array( 'type' => 'payir', 'mode' => 'code' ),
		);
	}
endif;

if ( ! function_exists( 'zc_trust_item_html' ) ) :
	/**
	 * خروجی یک نماد.
	 *
	 * @param array $item تنظیمات نماد.
	 * @param array $args تنظیمات کلی.
	 * @return string
	 */
	function zc_trust_item_html( array $item, array $args ) {
		$types = zc_trust_types();
		$type  = isset( $item['type'], $types[ $item['type'] ] ) ? $item['type'] : 'custom';
		$def   = $types[ $type ];
		$mode  = isset( $item['mode'] ) ? (string) $item['mode'] : 'code';
		$label = isset( $item['label'] ) && '' !== trim( (string) $item['label'] ) ? (string) $item['label'] : $def['label'];
		$name  = isset( $item['name'] ) && '' !== trim( (string) $item['name'] ) ? (string) $item['name'] : $def['name'];
		$link  = isset( $item['link'] ) ? trim( (string) $item['link'] ) : '';
		$image = isset( $item['image'] ) ? (string) $item['image'] : '';
		$code  = isset( $item['code'] ) ? trim( (string) $item['code'] ) : '';

		if ( 'pco' === $type ) {
			$pco  = zc_legal_info( 'pco_code' );
			$mode = 'image' === $mode && '' !== $image ? 'image' : 'default';
			if ( '' !== $pco ) {
				/* translators: %s: کد نظام */
				$name = sprintf( __( 'کد نظام: %s', 'zarincoach' ), $pco );
			}
		}

		// کد رسمی: در صورت خالی بودن، از پنل تنظیمات خوانده می‌شود.
		if ( 'code' === $mode && '' === $code && '' !== $def['option'] ) {
			$code = trim( (string) zc_opt( $def['option'], '' ) );
		}

		// کد رسمی خالی: یا حذف نماد، یا نمایش کاشی خنثی (قابل تنظیم) + راهنمای مخصوص مدیر.
		$missing = 'code' === $mode && '' === $code;
		if ( $missing && '' !== $def['option'] && empty( $args['fallback'] ) ) {
			return '';
		}

		$seal = '';
		if ( 'code' === $mode && '' !== $code ) {
			$seal  = '<div class="zc-trust-seal is-code">' . zc_trust_kses( $code ) . '</div>';
			$state = 'code';
		} elseif ( 'image' === $mode && '' !== $image ) {
			// پیوست رسانه ← srcset/ابعاد واقعی؛ آدرس خارجی ← ابعاد ثابت برای جلوگیری از CLS.
			$img   = zc_image(
				$image,
				'thumbnail',
				array(
					'alt'    => $label . ' — ' . $name,
					'sizes'  => '120px',
					'width'  => '120',
					'height' => '120',
				)
			);
			$seal  = '' !== $link
				? '<a class="zc-trust-seal is-image" href="' . esc_url( $link ) . '" target="_blank" rel="noopener nofollow">' . $img . '</a>'
				: '<div class="zc-trust-seal is-image">' . $img . '</div>';
			$state = 'image';
		} else {
			$state = 'default';
			$mark  = '<span class="zc-trust-emblem" aria-hidden="true">' . zc_icon( $def['icon'], 'h-6 w-6', false ) . '</span><span class="zc-trust-mark-name">' . esc_html( $name ) . '</span>';
			$seal  = '' !== $link
				? '<a class="zc-trust-seal is-default" href="' . esc_url( $link ) . '" target="_blank" rel="noopener nofollow" aria-label="' . esc_attr( $label . ' — ' . $name ) . '">' . $mark . '</a>'
				: '<div class="zc-trust-seal is-default">' . $mark . '</div>';

			if ( $missing && '' !== $def['option'] && ( ! empty( $args['editor'] ) || current_user_can( 'manage_options' ) ) ) {
				$seal .= '<span class="zc-trust-hint">' . esc_html__( 'کد رسمی درج نشده', 'zarincoach' ) . '</span>';
			}
		}

		$caption = '';
		if ( ! empty( $args['captions'] ) ) {
			$caption = '<span class="zc-trust-caption"><span class="zc-trust-label">' . esc_html( $label ) . '</span>'
				. '<span class="zc-trust-sub">' . esc_html( $name ) . '</span>'
				. '</span>';
		}

		return '<li class="zc-trust-item is-' . esc_attr( $type ) . ' is-' . esc_attr( $state ) . '">' . $seal . $caption . '</li>';
	}
endif;

if ( ! function_exists( 'zc_trust_badges_render' ) ) :
	/**
	 * خروجی فهرست نمادها.
	 *
	 * @param array $items نمادها (حداکثر ۶).
	 * @param array $args  style (cards|navy|inline|minimal|footer)، columns (1-6)، align، gray، captions، editor.
	 * @return string
	 */
	function zc_trust_badges_render( array $items, array $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'style'    => 'cards',
				'columns'  => 4,
				'align'    => 'center',
				'gray'     => false,
				'captions' => true,
				'fallback' => true,
				'editor'   => false,
				'class'    => '',
			)
		);

		$items = array_slice( array_values( array_filter( $items, 'is_array' ) ), 0, 6 );
		if ( ! $items ) {
			return '';
		}

		$style   = in_array( $args['style'], array( 'cards', 'navy', 'inline', 'minimal', 'footer' ), true ) ? $args['style'] : 'cards';
		$columns = max( 1, min( 6, (int) $args['columns'] ) );
		$align   = in_array( $args['align'], array( 'start', 'center', 'end' ), true ) ? $args['align'] : 'center';
		$classes = array( 'zc-trust', 'zc-trust-style-' . $style, 'zc-trust-align-' . $align );
		if ( $args['gray'] ) {
			$classes[] = 'is-gray';
		}
		if ( $args['class'] ) {
			$classes[] = $args['class'];
		}

		$html  = '';
		$shown = 0;
		foreach ( $items as $item ) {
			$one = zc_trust_item_html( $item, $args );
			if ( '' !== $one ) {
				$html .= $one;
				++$shown;
			}
		}
		if ( ! $shown ) {
			return '';
		}

		return '<ul class="' . esc_attr( implode( ' ', $classes ) ) . '" style="--zc-trust-cols:' . (int) min( $columns, $shown ) . '" role="list">' . $html . '</ul>';
	}
endif;
