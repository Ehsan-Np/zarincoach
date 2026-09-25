<?php
/**
 * توابع کمکی قالب زرین‌کوچ
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_merge_check_defaults' ) ) {
	/**
	 * تکمیل گروه چک‌باکس ذخیره‌شده با پیش‌فرض‌ها.
	 * کلید جاافتاده یا '' یعنی گزینه پس از آخرین ذخیره اضافه شده؛ خاموش کردن عمدی در Redux مقدار '0' می‌گذارد.
	 *
	 * @param array $saved    مقدار ذخیره‌شده.
	 * @param array $defaults پیش‌فرض فیلد.
	 * @return array
	 */
	function zc_merge_check_defaults( $saved, $defaults ) {
		foreach ( $defaults as $k => $v ) {
			if ( ! array_key_exists( $k, $saved ) || '' === $saved[ $k ] || null === $saved[ $k ] ) {
				$saved[ $k ] = $v;
			}
		}
		return $saved;
	}
}

if ( ! function_exists( 'zc_opt' ) ) :
	/**
	 * خواندن یک گزینه از پنل تنظیمات (Redux) با مقدار پیش‌فرض هوشمند.
	 *
	 * @param string $key     کلید فیلد.
	 * @param mixed  $default مقدار جایگزین نهایی.
	 * @return mixed
	 */
	function zc_opt( $key, $default = '' ) {
		static $options = null;
		static $version = null;

		$current = isset( $GLOBALS['zc_opt_version'] ) ? (int) $GLOBALS['zc_opt_version'] : 0;
		if ( $version !== $current ) {
			$options = null;
			$version = $current;
		}

		if ( null === $options ) {
			$options = get_option( ZC_OPT, array() );
			if ( ! is_array( $options ) ) {
				$options = array();
			}
		}

		// مقدار ذخیره‌شده (حتی خالی) اولویت دارد؛ کاربر باید بتواند فیلدی مثل «تلفن دوم» را عمداً خالی کند.
		// فقط وقتی کلید هرگز ذخیره نشده (گزینه‌ی تازه اضافه‌شده) از پیش‌فرض استفاده می‌شود.
		/**
		 * کلیدهایی که خالی ماندنشان خروجی را خراب می‌کند (پیوند دکمه‌ها، دامنه، شهر در اسکیما).
		 * برای پنهان کردن یک دکمه، «متن» آن را خالی کنید؛ پیوند خالی به پیش‌فرض برمی‌گردد.
		 */
		static $non_empty = null;
		if ( null === $non_empty ) {
			$non_empty = array_flip(
				(array) apply_filters(
					'zc_opt_non_empty_keys',
					array( 'header_cta_url', 'header_mobile_cta_url', 'header_topbar_link', 'float_booking_url', 'home_hero_primary_url', 'home_hero_secondary_url', 'home_cta_primary_url', 'home_about_button_url', 'legal_domain', 'legal_owner', 'contact_city', 'seo_person_name', 'seo_business_name', 'p404_button', 'cookie_button', 'maint_title' )
				)
			);
		}

		if ( array_key_exists( $key, $options ) && null !== $options[ $key ] ) {
			if ( ! ( isset( $non_empty[ $key ] ) && is_string( $options[ $key ] ) && '' === trim( $options[ $key ] ) ) ) {
				// گروه چک‌باکس ذخیره‌شده پیش از افزوده شدن یک گزینه‌ی تازه: کلیدهای جاافتاده از پیش‌فرض پر می‌شوند.
				if ( is_array( $options[ $key ] ) ) {
					$defaults = zc_field_defaults();
					if ( isset( $defaults[ $key ] ) && is_array( $defaults[ $key ] ) && ! wp_is_numeric_array( $defaults[ $key ] ) ) {
						return zc_merge_check_defaults( $options[ $key ], $defaults[ $key ] );
					}
				}
				return $options[ $key ];
			}
		}

		$defaults = zc_field_defaults();
		if ( array_key_exists( $key, $defaults ) ) {
			return $defaults[ $key ];
		}

		/**
		 * فیلتر مقدار پیش‌فرضِ گزینه‌های قالب.
		 *
		 * @param mixed  $default مقدار پیش‌فرض.
		 * @param string $key     کلید گزینه.
		 */
		return apply_filters( 'zc_option_default', $default, $key );
	}
endif;

if ( ! function_exists( 'zc_opt_flush' ) ) :
	/**
	 * پاک‌سازی کش داخلی تنظیمات (پس از بروزرسانی گزینه‌ها در همان درخواست).
	 *
	 * @return void
	 */
	function zc_opt_flush() {
		$GLOBALS['zc_opt_version'] = isset( $GLOBALS['zc_opt_version'] ) ? (int) $GLOBALS['zc_opt_version'] + 1 : 1;
	}
endif;

if ( ! function_exists( 'zc_field_defaults_key' ) ) :
	/**
	 * کلید کش پیش‌فرض‌ها؛ با تغییر نسخه یا فایل‌های تعریف فیلد خودکار عوض می‌شود.
	 *
	 * @return string
	 */
	function zc_field_defaults_key() {
		$stamp = 0;
		foreach ( array( '/inc/options-panel.php', '/inc/options-sections-home.php', '/inc/options-fields.php' ) as $file ) {
			$stamp += (int) @filemtime( ZC_DIR . $file ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
		}
		return 'zc_field_defaults_' . substr( md5( ZC_VERSION . '|' . $stamp ), 0, 10 );
	}
endif;

if ( ! function_exists( 'zc_field_defaults' ) ) :
	/**
	 * استخراج خودکار مقادیر پیش‌فرض از تعریف فیلدهای Redux.
	 *
	 * @return array<string, mixed>
	 */
	function zc_field_defaults() {
		static $cache = null;

		if ( null !== $cache ) {
			return $cache;
		}

		$cached = get_transient( zc_field_defaults_key() );
		if ( is_array( $cached ) && ! empty( $cached ) ) {
			$cache = $cached;
			return $cache;
		}

		$cache   = array();
		$collect = static function ( $fields ) use ( &$collect, &$cache ) {
			foreach ( (array) $fields as $field ) {
				if ( ! is_array( $field ) ) {
					continue;
				}
				if ( isset( $field['fields'] ) && is_array( $field['fields'] ) ) {
					$collect( $field['fields'] );
				}
				if ( empty( $field['id'] ) || ! isset( $field['default'] ) ) {
					continue;
				}
				$type = isset( $field['type'] ) ? $field['type'] : 'text';
				if ( in_array( $type, array( 'section', 'info', 'divide', 'raw', 'import_export' ), true ) ) {
					continue;
				}
				$cache[ $field['id'] ] = $field['default'];
			}
		};

		if ( function_exists( 'zc_redux_sections' ) ) {
			foreach ( zc_redux_sections( false ) as $section ) {
				if ( ! empty( $section['fields'] ) ) {
					$collect( $section['fields'] );
				}
			}
		}

		set_transient( zc_field_defaults_key(), $cache, DAY_IN_SECONDS );
		return $cache;
	}
endif;

if ( ! function_exists( 'zc_switch' ) ) :
	/**
	 * بررسی مقدار یک کلیدِ روشن/خاموش.
	 *
	 * @param string $key     کلید گزینه.
	 * @param bool   $default مقدار پیش‌فرض در صورت نبود گزینه.
	 * @return bool
	 */
	function zc_switch( $key, $default = false ) {
		$value = zc_opt( $key, $default ? '1' : '0' );
		if ( is_bool( $value ) ) {
			return $value;
		}
		return in_array( (string) $value, array( '1', 'true', 'on', 'yes' ), true );
	}
endif;

if ( ! function_exists( 'zc_hex_to_rgb' ) ) :
	/**
	 * تبدیل رنگ هگز به سه‌گانه‌ی RGB (برای پشتیبانی از شفافیت در Tailwind).
	 *
	 * @param string $hex رنگ هگز.
	 * @return string
	 */
	function zc_hex_to_rgb( $hex ) {
		$hex = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) || ! ctype_xdigit( $hex ) ) {
			return '15 23 42';
		}
		return implode( ' ', array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) ) );
	}
endif;

if ( ! function_exists( 'zc_adjust_brightness' ) ) :
	/**
	 * روشن یا تیره کردن یک رنگ هگز.
	 *
	 * @param string $hex   رنگ هگز.
	 * @param int    $steps مقدار تغییر (منفی تیره‌تر).
	 * @return string
	 */
	function zc_adjust_brightness( $hex, $steps ) {
		$hex   = ltrim( (string) $hex, '#' );
		if ( 3 === strlen( $hex ) ) {
			$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
		}
		if ( 6 !== strlen( $hex ) ) {
			return '#' . $hex;
		}
		$r = max( 0, min( 255, hexdec( substr( $hex, 0, 2 ) ) + $steps ) );
		$g = max( 0, min( 255, hexdec( substr( $hex, 2, 2 ) ) + $steps ) );
		$b = max( 0, min( 255, hexdec( substr( $hex, 4, 2 ) ) + $steps ) );
		return sprintf( '#%02x%02x%02x', $r, $g, $b );
	}
endif;

if ( ! function_exists( 'zc_img' ) ) :
	/**
	 * آدرس یک تصویر داخلی قالب.
	 *
	 * @param string $file نام فایل داخل پوشه assets/img.
	 * @return string
	 */
	function zc_img( $file ) {
		return ZC_URI . '/assets/img/' . ltrim( (string) $file, '/' );
	}
endif;

if ( ! function_exists( 'zc_placeholder' ) ) :
	/**
	 * تصویر جایگزینِ آماده (SVG بسیار سبک و بدون درخواست خارجی).
	 *
	 * @param string $kind نوع تصویر: portrait | card | wide | avatar.
	 * @return string
	 */
	function zc_placeholder( $kind = 'card' ) {
		$map = array(
			'portrait' => 'placeholder-portrait.svg',
			'card'     => 'placeholder-card.svg',
			'wide'     => 'placeholder-wide.svg',
			'avatar'   => 'placeholder-avatar.svg',
			'hero'     => 'placeholder-hero.svg',
		);
		$file = array_key_exists( $kind, $map ) ? $map[ $kind ] : $map['card'];
		return zc_img( $file );
	}
endif;

if ( ! function_exists( 'zc_thumb' ) ) :
	/**
	 * دریافت آدرس تصویر شاخص با جایگزین مناسب.
	 *
	 * @param int|null $post_id شناسه نوشته.
	 * @param string   $size    اندازه تصویر.
	 * @param string   $kind    نوع تصویر جایگزین.
	 * @return string
	 */
	function zc_thumb( $post_id = null, $size = 'zc_card', $kind = 'card' ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		if ( has_post_thumbnail( $post_id ) ) {
			$url = get_the_post_thumbnail_url( $post_id, $size );
			if ( $url ) {
				return $url;
			}
		}
		return zc_placeholder( $kind );
	}
endif;

if ( ! function_exists( 'zc_image_id' ) ) :
	/**
	 * یافتن شناسه‌ی پیوست از شناسه/آرایه‌ی رسانه/آدرس (با کش).
	 *
	 * @param int|array|string $src شناسه، آرایه‌ی ['id','url'] یا آدرس.
	 * @return int
	 */
	function zc_image_id( $src ) {
		if ( is_numeric( $src ) ) {
			return (int) $src;
		}
		if ( is_array( $src ) ) {
			if ( ! empty( $src['id'] ) && 'attachment' === get_post_type( (int) $src['id'] ) ) {
				return (int) $src['id'];
			}
			$src = isset( $src['url'] ) ? (string) $src['url'] : '';
		}
		$src = (string) $src;
		if ( '' === $src || false === strpos( $src, '/uploads/' ) ) {
			return 0; // فایل‌های داخلی قالب (SVG جایگزین و…) پیوست نیستند.
		}
		$key = 'u' . md5( $src );
		$id  = wp_cache_get( $key, 'zc_img' );
		if ( false === $id ) {
			$id = (int) attachment_url_to_postid( $src );
			if ( ! $id ) {
				// آدرس اندازه‌ی فرعی (…-800x1000.jpg یا ‎-scaled) → فایل اصلی.
				$orig = preg_replace( '/-(\d+x\d+|scaled)(?=\.[a-z0-9]+$)/i', '', $src );
				if ( $orig !== $src ) {
					$id = (int) attachment_url_to_postid( $orig );
				}
			}
			wp_cache_set( $key, $id, 'zc_img', HOUR_IN_SECONDS );
		}
		return (int) $id;
	}
endif;

if ( ! function_exists( 'zc_image' ) ) :
	/**
	 * خروجی واکنش‌گرای تصویر (srcset/sizes، ابعاد، lazy یا اولویت بالا برای LCP).
	 *
	 * @param int|array|string $src  شناسه، آرایه‌ی رسانه یا آدرس.
	 * @param string           $size اندازه‌ی وردپرس (zc_portrait، zc_card، …).
	 * @param array            $attr ویژگی‌ها؛ کلیدهای خاص:
	 *                               priority (bool) = تصویر LCP ← eager + fetchpriority=high،
	 *                               width/height برای آدرس‌های غیرپیوست.
	 * @return string
	 */
	function zc_image( $src, $size = 'large', array $attr = array() ) {
		$priority = ! empty( $attr['priority'] );
		unset( $attr['priority'] );

		$attr = array_merge(
			array(
				'alt'      => '',
				'decoding' => 'async',
			),
			$attr
		);
		if ( $priority ) {
			$attr['loading']       = 'eager';
			$attr['fetchpriority'] = 'high';
		} elseif ( empty( $attr['loading'] ) ) {
			$attr['loading'] = 'lazy';
		}
		if ( 'lazy' === $attr['loading'] ) {
			unset( $attr['fetchpriority'] );
		}

		$id = zc_image_id( $src );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$att = $attr;
			unset( $att['width'], $att['height'] ); // ابعاد واقعی را وردپرس از فراداده می‌گذارد.
			$html = wp_get_attachment_image( $id, $size, false, $att );
			if ( '' !== $html ) {
				return $html;
			}
		}

		$url = is_array( $src ) ? ( isset( $src['url'] ) ? (string) $src['url'] : '' ) : ( is_numeric( $src ) ? '' : (string) $src );
		if ( '' === $url ) {
			return '';
		}
		unset( $attr['sizes'] ); // بدون srcset بی‌معناست.
		$html = '<img src="' . esc_url( $url ) . '"';
		foreach ( $attr as $name => $value ) {
			if ( '' === $value && 'alt' !== $name ) {
				continue;
			}
			$html .= ' ' . esc_attr( $name ) . '="' . esc_attr( (string) $value ) . '"';
		}
		return $html . '>';
	}
endif;

if ( ! function_exists( 'zc_post_image' ) ) :
	/**
	 * تصویر شاخص واکنش‌گرای یک نوشته (یا تصویر جایگزین سبک SVG).
	 *
	 * @param int    $post_id شناسه نوشته.
	 * @param string $size    اندازه.
	 * @param array  $attr    ویژگی‌ها (مانند zc_image؛ width/height برای جایگزین).
	 * @param string $kind    نوع تصویر جایگزین.
	 * @return string
	 */
	function zc_post_image( $post_id, $size = 'zc_card', array $attr = array(), $kind = 'card' ) {
		$thumb = has_post_thumbnail( $post_id ) ? (int) get_post_thumbnail_id( $post_id ) : 0;
		return zc_image( $thumb ? $thumb : zc_placeholder( $kind ), $size, $attr );
	}
endif;

if ( ! function_exists( 'zc_reading_time' ) ) :
	/**
	 * محاسبه زمان تقریبی مطالعه (بر اساس ۲۲۰ واژه در دقیقه برای فارسی).
	 *
	 * @param int|null $post_id شناسه نوشته.
	 * @return int دقیقه
	 */
	function zc_reading_time( $post_id = null ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$content = get_post_field( 'post_content', $post_id );
		$words   = count( preg_split( '/\s+/u', wp_strip_all_tags( (string) $content ) ) );
		$minutes = (int) ceil( $words / 220 );
		return max( 1, $minutes );
	}
endif;

if ( ! function_exists( 'zc_excerpt' ) ) :
	/**
	 * بریدن متن به تعداد واژه مشخص به صورت امن و یونیکد.
	 *
	 * @param string $text  متن ورودی.
	 * @param int    $limit تعداد واژه.
	 * @return string
	 */
	function zc_excerpt( $text, $limit = 24 ) {
		$text  = wp_strip_all_tags( (string) $text );
		$words = preg_split( '/\s+/u', trim( $text ) );
		if ( ! $words || count( $words ) <= $limit ) {
			return trim( $text );
		}
		$words = array_slice( $words, 0, $limit );
		return implode( ' ', $words ) . '…';
	}
endif;

if ( ! function_exists( 'zc_normalize_phone' ) ) :
	/**
	 * نرمال‌سازی شماره تلفن برای استفاده در لینک‌های tel و https://wa.me
	 *
	 * @param string $phone شماره تلفن.
	 * @return string
	 */
	function zc_normalize_phone( $phone ) {
		$phone = trim( (string) $phone );
		$phone = zc_digits_to_latin( $phone );
		$phone = preg_replace( '/[^\d+]/', '', $phone );
		if ( 0 === strpos( $phone, '00' ) ) {
			$phone = '+' . substr( $phone, 2 );
		}
		if ( '0' === substr( $phone, 0, 1 ) && strlen( $phone ) > 10 ) {
			$phone = '+98' . ltrim( $phone, '0' );
		}
		return $phone;
	}
endif;

if ( ! function_exists( 'zc_digits_to_latin' ) ) :
	/**
	 * تبدیل ارقام فارسی و عربی به لاتین.
	 *
	 * @param string $value مقدار ورودی.
	 * @return string
	 */
	function zc_digits_to_latin( $value ) {
		$value = (string) $value;
		$fa    = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		$ar    = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );
		$en    = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		return str_replace( array_merge( $fa, $ar ), array_merge( $en, $en ), $value );
	}
endif;

if ( ! function_exists( 'zc_digits_to_persian' ) ) :
	/**
	 * تبدیل ارقام لاتین به فارسی (برای نمایش زیباتر در صورت نیاز).
	 *
	 * @param string $value مقدار ورودی.
	 * @return string
	 */
	function zc_digits_to_persian( $value ) {
		$value = (string) $value;
		$en    = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		$fa    = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
		return str_replace( $en, $fa, $value );
	}
endif;

if ( ! function_exists( 'zc_format_number' ) ) :
	/**
	 * قالب‌بندی اعداد با توجه به تنظیم «ارقام فارسی».
	 *
	 * @param string|int|float $number عدد.
	 * @return string
	 */
	function zc_format_number( $number ) {
		$formatted = number_format_i18n( (float) $number );
		if ( zc_switch( 'typo_persian_digits', true ) ) {
			return zc_digits_to_persian( $formatted );
		}
		return $formatted;
	}
endif;

if ( ! function_exists( 'zc_socials' ) ) :
	/**
	 * فهرست شبکه‌های اجتماعیِ فعال به همراه آیکون و لینک.
	 *
	 * @return array<int, array{id:string,label:string,url:string,icon:string}>
	 */
	function zc_socials() {
		$map = array(
			'instagram' => array( 'label' => 'اینستاگرام', 'icon' => 'instagram' ),
			'telegram'  => array( 'label' => 'تلگرام', 'icon' => 'send' ),
			'whatsapp'  => array( 'label' => 'واتس‌اپ', 'icon' => 'whatsapp' ),
			'linkedin'  => array( 'label' => 'لینکدین', 'icon' => 'linkedin' ),
			'youtube'   => array( 'label' => 'یوتیوب', 'icon' => 'play' ),
			'aparat'    => array( 'label' => 'آپارات', 'icon' => 'play' ),
			'twitter'   => array( 'label' => 'ایکس', 'icon' => 'twitter' ),
			'bale'      => array( 'label' => 'بله', 'icon' => 'bale' ),
			'eitaa'     => array( 'label' => 'ایتا', 'icon' => 'eitaa' ),
			'rubika'    => array( 'label' => 'روبیکا', 'icon' => 'message' ),
		);

		$list = array();
		foreach ( $map as $id => $data ) {
			$url = zc_social_url( $id );
			if ( '' === $url ) {
				continue;
			}
			$list[] = array(
				'id'    => $id,
				'label' => $data['label'],
				'icon'  => $data['icon'],
				'url'   => $url,
			);
		}

		/**
		 * فیلتر شبکه‌های اجتماعی.
		 *
		 * @param array $list فهرست شبکه‌ها.
		 */
		return (array) apply_filters( 'zc_socials', $list );
	}
endif;

if ( ! function_exists( 'zc_social_url' ) ) :
	/**
	 * نشانی کامل یک شبکه اجتماعی؛ شناسه‌های «@name» و شماره‌ها به پیوند تبدیل می‌شوند.
	 *
	 * @param string $id شناسه شبکه (instagram, telegram, bale, eitaa, whatsapp, ...).
	 * @return string
	 */
	function zc_social_url( $id ) {
		$raw = trim( (string) zc_opt( 'social_' . $id, '' ) );
		if ( '' === $raw ) {
			return '';
		}
		if ( preg_match( '#^(https?:)?//#i', $raw ) || 0 === strpos( $raw, 'tg:' ) ) {
			return $raw;
		}
		$bases = array(
			'instagram' => 'https://instagram.com/',
			'telegram'  => 'https://t.me/',
			'bale'      => 'https://ble.ir/',
			'eitaa'     => 'https://eitaa.com/',
			'rubika'    => 'https://rubika.ir/',
			'twitter'   => 'https://x.com/',
			'linkedin'  => 'https://www.linkedin.com/in/',
			'aparat'    => 'https://www.aparat.com/',
			'youtube'   => 'https://www.youtube.com/@',
		);
		if ( 'whatsapp' === $id ) {
			return 'https://wa.me/' . ltrim( zc_normalize_phone( $raw ), '+' );
		}
		$handle = ltrim( preg_replace( '#^(www\.)?[a-z0-9.-]+\.[a-z]{2,}/#i', '', $raw ), '@/' );
		if ( preg_match( '#^[a-z0-9.-]+\.[a-z]{2,}/#i', $raw ) ) {
			return 'https://' . $raw;
		}
		return isset( $bases[ $id ] ) ? $bases[ $id ] . rawurlencode( $handle ) : '';
	}
endif;

if ( ! function_exists( 'zc_social_handle' ) ) :
	/**
	 * شناسه نمایشی (@name) یک شبکه اجتماعی.
	 *
	 * @param string $id شناسه شبکه.
	 * @return string
	 */
	function zc_social_handle( $id ) {
		$url = zc_social_url( $id );
		if ( '' === $url || 'whatsapp' === $id ) {
			return '';
		}
		$path = trim( (string) wp_parse_url( $url, PHP_URL_PATH ), '/@' );
		return '' !== $path ? '@' . rawurldecode( $path ) : '';
	}
endif;

if ( ! function_exists( 'zc_contact_fields' ) ) :
	/**
	 * اطلاعات تماسِ ثبت‌شده در پنل تنظیمات.
	 *
	 * @return array{phone:string,phone2:string,phone_label:string,phone2_label:string,email:string,address:string,hours:string}
	 */
	function zc_contact_fields() {
		return array(
			'phone'   => (string) zc_opt( 'contact_phone', '' ),
			'phone2'  => (string) zc_opt( 'contact_mobile', '' ),
			'phone_label'  => (string) zc_opt( 'contact_phone_label', __( 'تلفن رزرو نوبت', 'zarincoach' ) ),
			'phone2_label' => (string) zc_opt( 'contact_mobile_label', __( 'تلفن مطب', 'zarincoach' ) ),
			'email'   => (string) zc_opt( 'contact_email', 'info@maryam-jamali.ir' ),
			'address' => (string) zc_opt( 'contact_address', '' ),
			'hours'   => (string) zc_opt( 'contact_hours', '' ),
		);
	}
endif;

if ( ! function_exists( 'zc_url' ) ) :
	/**
	 * تبدیل پیوندهای نسبی («/booking/») به نشانی کامل سایت؛ سازگار با نصب در زیرپوشه.
	 *
	 * @param string $url پیوند.
	 * @return string
	 */
	function zc_url( $url ) {
		$url = trim( (string) $url );
		if ( '' !== $url && '/' === $url[0] && ( ! isset( $url[1] ) || '/' !== $url[1] ) ) {
			return home_url( $url );
		}
		return $url;
	}
endif;

if ( ! function_exists( 'zc_contact_channels' ) ) :
	/**
	 * کانال‌های ارتباطی آماده نمایش (دکمه شناور، نوار موبایل، ابزارک‌ها).
	 *
	 * @param array $only فقط این شناسه‌ها (خالی = همه) به همین ترتیب.
	 * @return array<string, array{label:string,sub:string,icon:string,url:string,external:bool}>
	 */
	function zc_contact_channels( $only = array() ) {
		$c     = zc_contact_fields();
		$phone = '' !== $c['phone'] ? $c['phone'] : $c['phone2'];
		$all   = array(
			'phone'     => array(
				'label'    => __( 'تماس و رزرو نوبت', 'zarincoach' ),
				'sub'      => zc_digits_to_persian( $phone ),
				'icon'     => 'phone',
				'url'      => '' !== $phone ? 'tel:' . zc_normalize_phone( $phone ) : '',
				'external' => false,
			),
			'telegram'  => array(
				'label'    => __( 'پشتیبانی در تلگرام', 'zarincoach' ),
				'sub'      => zc_social_handle( 'telegram' ),
				'icon'     => 'send',
				'url'      => zc_social_url( 'telegram' ),
				'external' => true,
			),
			'bale'      => array(
				'label'    => __( 'پشتیبانی در بله', 'zarincoach' ),
				'sub'      => zc_social_handle( 'bale' ),
				'icon'     => 'bale',
				'url'      => zc_social_url( 'bale' ),
				'external' => true,
			),
			'eitaa'     => array(
				'label'    => __( 'پیام در ایتا', 'zarincoach' ),
				'sub'      => zc_social_handle( 'eitaa' ),
				'icon'     => 'eitaa',
				'url'      => zc_social_url( 'eitaa' ),
				'external' => true,
			),
			'whatsapp'  => array(
				'label'    => __( 'پیام در واتس‌اپ', 'zarincoach' ),
				'sub'      => '',
				'icon'     => 'whatsapp',
				'url'      => zc_social_url( 'whatsapp' ),
				'external' => true,
			),
			'instagram' => array(
				'label'    => __( 'اینستاگرام', 'zarincoach' ),
				'sub'      => zc_social_handle( 'instagram' ),
				'icon'     => 'instagram',
				'url'      => zc_social_url( 'instagram' ),
				'external' => true,
			),
			'email'     => array(
				'label'    => __( 'ارسال ایمیل', 'zarincoach' ),
				'sub'      => $c['email'],
				'icon'     => 'mail',
				'url'      => '' !== $c['email'] ? 'mailto:' . sanitize_email( $c['email'] ) : '',
				'external' => false,
			),
			'booking'   => array(
				'label'    => __( 'رزرو آنلاین جلسه', 'zarincoach' ),
				'sub'      => __( 'فرم درخواست نوبت', 'zarincoach' ),
				'icon'     => 'calendar',
				'url'      => zc_url( (string) zc_opt( 'float_booking_url', '/booking/' ) ),
				'external' => false,
			),
		);

		$out  = array();
		$keys = empty( $only ) ? array_keys( $all ) : (array) $only;
		foreach ( $keys as $key ) {
			if ( isset( $all[ $key ] ) && '' !== $all[ $key ]['url'] ) {
				$out[ $key ] = $all[ $key ];
			}
		}
		return (array) apply_filters( 'zc_contact_channels', $out, $only );
	}
endif;

if ( ! function_exists( 'zc_is_elementor_active' ) ) :
	/**
	 * بررسی فعال بودن افزونه المنتور.
	 *
	 * @return bool
	 */
	function zc_is_elementor_active() {
		return defined( 'ELEMENTOR_VERSION' ) || did_action( 'elementor/loaded' );
	}
endif;

if ( ! function_exists( 'zc_page_uses_elementor' ) ) :
	/**
	 * بررسی ساخته شدن یک صفحه با المنتور.
	 *
	 * @param int $post_id شناسه نوشته.
	 * @return bool
	 */
	function zc_page_uses_elementor( $post_id ) {
		if ( ! $post_id ) {
			return false;
		}
		$mode = get_post_meta( $post_id, '_elementor_edit_mode', true );
		if ( 'builder' !== $mode ) {
			return false;
		}
		$data = get_post_meta( $post_id, '_elementor_data', true );
		return ! empty( $data );
	}
endif;

if ( ! function_exists( 'zc_has_sidebar' ) ) :
	/**
	 * آیا در صفحه‌ی جاری ستون کناری نمایش داده شود؟
	 *
	 * @return bool
	 */
	function zc_has_sidebar() {
		$layout = zc_opt( 'blog_layout', 'grid' );
		if ( is_singular( 'post' ) ) {
			$layout = zc_opt( 'post_layout', 'full' );
		} elseif ( ! is_singular() ) {
			$layout = zc_opt( 'archive_layout', $layout );
		}
		return ( 'sidebar' === $layout ) && is_active_sidebar( 'zc-blog' );
	}
endif;

if ( ! function_exists( 'zc_breadcrumb_items' ) ) :
	/**
	 * آیتم‌های مسیر راهنمای صفحه‌ی جاری (منبع مشترک خروجی HTML و اسکیمای BreadcrumbList).
	 *
	 * @param string $home برچسب خانه.
	 * @return array<int, array{label:string,url:string}>
	 */
	function zc_breadcrumb_items( $home = '' ) {
		static $cache = array();
		$home = '' !== $home ? $home : __( 'خانه', 'zarincoach' );
		if ( isset( $cache[ $home ] ) ) {
			return $cache[ $home ];
		}
		if ( is_front_page() ) {
			$cache[ $home ] = array();
			return array();
		}

		$items   = array();
		$items[] = array( 'label' => $home, 'url' => home_url( '/' ) );

		if ( is_singular() ) {
			$post_id = get_the_ID();
			if ( 'post' === get_post_type( $post_id ) ) {
				$blog_id = zc_blog_page_id();
				if ( $blog_id ) {
					$items[] = array( 'label' => get_the_title( $blog_id ), 'url' => get_permalink( $blog_id ) );
				}
				$categories = get_the_category( $post_id );
				if ( ! empty( $categories ) ) {
					$cat = $categories[0];
					foreach ( array_reverse( get_ancestors( $cat->term_id, 'category', 'taxonomy' ) ) as $parent_id ) {
						$parent = get_term( $parent_id, 'category' );
						if ( $parent && ! is_wp_error( $parent ) ) {
							$items[] = array( 'label' => $parent->name, 'url' => get_category_link( $parent ) );
						}
					}
					$items[] = array( 'label' => $cat->name, 'url' => get_category_link( $cat->term_id ) );
				}
			} elseif ( is_post_type_hierarchical( get_post_type( $post_id ) ) ) {
				$ancestors = get_post_ancestors( $post_id );
				foreach ( array_reverse( $ancestors ) as $ancestor ) {
					$items[] = array( 'label' => get_the_title( $ancestor ), 'url' => get_permalink( $ancestor ) );
				}
			} else {
				$post_type = get_post_type_object( get_post_type( $post_id ) );
				if ( $post_type && $post_type->has_archive ) {
					$items[] = array( 'label' => $post_type->labels->name, 'url' => get_post_type_archive_link( $post_type->name ) );
				}
			}
			$items[] = array( 'label' => get_the_title( $post_id ), 'url' => '' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			if ( $term ) {
				$items[] = array( 'label' => $term->name, 'url' => '' );
			}
		} elseif ( is_post_type_archive() ) {
			$items[] = array( 'label' => post_type_archive_title( '', false ), 'url' => '' );
		} elseif ( is_home() ) {
			$items[] = array( 'label' => get_the_title( get_option( 'page_for_posts' ) ), 'url' => '' );
		} elseif ( is_search() ) {
			$items[] = array( 'label' => sprintf( /* translators: %s: search query */ __( 'نتایج جستجو برای: %s', 'zarincoach' ), get_search_query( false ) ), 'url' => '' );
		} elseif ( is_404() ) {
			$items[] = array( 'label' => __( 'صفحه یافت نشد', 'zarincoach' ), 'url' => '' );
		} elseif ( is_author() ) {
			$items[] = array( 'label' => get_the_author(), 'url' => '' );
		} elseif ( is_date() ) {
			$items[] = array( 'label' => get_the_date( 'F Y' ), 'url' => '' );
		} else {
			return array();
		}

		/**
		 * فیلتر آیتم‌های مسیر راهنما.
		 *
		 * @param array $items آیتم‌ها.
		 */
		$cache[ $home ] = (array) apply_filters( 'zc_breadcrumb_items', $items );
		return $cache[ $home ];
	}
endif;

if ( ! function_exists( 'zc_breadcrumbs' ) ) :
	/**
	 * تولید مسیر راهنما (Breadcrumb) با نشانه‌گذاری سازگار با سئو.
	 *
	 * @param array $args تنظیمات نمایش.
	 * @return string
	 */
	function zc_breadcrumbs( $args = array() ) {
		if ( ! zc_switch( 'seo_breadcrumbs', true ) ) {
			return '';
		}

		$defaults = array(
			'home'      => __( 'خانه', 'zarincoach' ),
			'separator' => '/',
			'echo'      => true,
		);
		$args     = wp_parse_args( $args, $defaults );

		$items = zc_breadcrumb_items( $args['home'] );
		if ( count( $items ) < 2 ) {
			return '';
		}

		$html = '<nav class="zc-breadcrumb zc-arabic-num" aria-label="' . esc_attr__( 'مسیر راهنما', 'zarincoach' ) . '"><ol class="flex flex-wrap items-center gap-1.5">';
		$last = count( $items ) - 1;
		foreach ( $items as $index => $item ) {
			$label = esc_html( $item['label'] );
			if ( '' !== $item['url'] ) {
				$html .= '<li><a href="' . esc_url( $item['url'] ) . '">' . $label . '</a></li>';
			} else {
				$html .= '<li aria-current="page" class="font-bold text-secondary">' . $label . '</li>';
			}
			if ( $index < $last ) {
				$html .= '<li aria-hidden="true" class="text-primary/50">' . esc_html( $args['separator'] ) . '</li>';
			}
		}
		$html .= '</ol></nav>';

		if ( $args['echo'] ) {
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی از قبل پاک‌سازی شده است
			return '';
		}
		return $html;
	}
endif;

if ( ! function_exists( 'zc_is_woo' ) ) :
	/**
	 * بررسی فعال بودن ووکامرس (برای سازگاری آینده).
	 *
	 * @return bool
	 */
	function zc_is_woo() {
		return class_exists( 'WooCommerce' );
	}
endif;

if ( ! function_exists( 'zc_section_enabled' ) ) :
	/**
	 * بررسی فعال بودن یک بخش از صفحه اصلی.
	 *
	 * @param string $section نام بخش.
	 * @return bool
	 */
	function zc_section_enabled( $section ) {
		return zc_switch( 'home_' . $section . '_enable', true );
	}
endif;

if ( ! function_exists( 'zc_get_services' ) ) :
	/**
	 * دریافت خدمات (نوع نوشته اختصاصی).
	 *
	 * @param int $count تعداد.
	 * @return WP_Query
	 */
	function zc_get_services( $count = 6 ) {
		$count = ( $count > 0 || -1 === (int) $count ) ? (int) $count : 6;
		return new WP_Query(
			array(
				'post_type'           => 'zc_service',
				'posts_per_page'      => $count,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'meta_key'            => '_zc_service_order',
				'orderby'             => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ),
			)
		);
	}
endif;

if ( ! function_exists( 'zc_get_testimonials' ) ) :
	/**
	 * دریافت نظرات مراجعان.
	 *
	 * @param int $count تعداد.
	 * @return WP_Query
	 */
	function zc_get_testimonials( $count = 6 ) {
		$count = $count > 0 ? (int) $count : 6;
		return new WP_Query(
			array(
				'post_type'           => 'zc_testimonial',
				'posts_per_page'      => $count,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			)
		);
	}
endif;

if ( ! function_exists( 'zc_get_faqs' ) ) :
	/**
	 * دریافت پرسش‌های پرتکرار.
	 *
	 * @param int $count تعداد.
	 * @return WP_Query
	 */
	function zc_get_faqs( $count = 8 ) {
		$count = $count > 0 ? (int) $count : 8;
		return new WP_Query(
			array(
				'post_type'           => 'zc_faq',
				'posts_per_page'      => $count,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => true,
				'orderby'             => array( 'menu_order' => 'ASC', 'date' => 'ASC' ),
			)
		);
	}
endif;

if ( ! function_exists( 'zc_post_categories' ) ) :
	/**
	 * دسته‌بندی‌های یک نوشته برای نمایش در کارت.
	 *
	 * @param int $post_id شناسه نوشته.
	 * @return string
	 */
	function zc_post_categories( $post_id = null ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$terms   = get_the_terms( $post_id, 'category' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return '';
		}
		return $terms[0]->name;
	}
endif;

if ( ! function_exists( 'zc_gregorian_to_jalali' ) ) :
	/**
	 * تبدیل تاریخ میلادی به شمسی (الگوریتم دقیق و بدون وابستگی).
	 *
	 * @param int $gy سال میلادی.
	 * @param int $gm ماه میلادی.
	 * @param int $gd روز میلادی.
	 * @return array{0:int,1:int,2:int} سال، ماه و روز شمسی.
	 */
	function zc_gregorian_to_jalali( $gy, $gm, $gd ) {
		$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );
		$gy2   = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
		$days  = 355666 + ( 365 * $gy ) + (int) ( ( $gy2 + 3 ) / 4 ) - (int) ( ( $gy2 + 99 ) / 100 ) + (int) ( ( $gy2 + 399 ) / 400 ) + $gd + $g_d_m[ $gm - 1 ];
		$jy    = -1595 + ( 33 * (int) ( $days / 12053 ) );
		$days %= 12053;
		$jy   += 4 * (int) ( $days / 1461 );
		$days %= 1461;
		if ( $days > 365 ) {
			$jy   += (int) ( ( $days - 1 ) / 365 );
			$days  = ( $days - 1 ) % 365;
		}
		if ( $days < 186 ) {
			$jm = 1 + (int) ( $days / 31 );
			$jd = 1 + ( $days % 31 );
		} else {
			$jm = 7 + (int) ( ( $days - 186 ) / 30 );
			$jd = 1 + ( ( $days - 186 ) % 30 );
		}
		return array( $jy, $jm, $jd );
	}
endif;

if ( ! function_exists( 'zc_date' ) ) :
	/**
	 * تاریخ نمایشی نوشته: شمسی (پیش‌فرض) یا مطابق تنظیمات وردپرس.
	 *
	 * اگر افزونه‌های تاریخ شمسی (WP-Parsidate و ...) فعال باشند، خروجی خود وردپرس استفاده می‌شود.
	 *
	 * @param int|WP_Post|null $post نوشته.
	 * @param string           $type نوع تاریخ: published | modified.
	 * @return string
	 */
	function zc_date( $post = null, $type = 'published' ) {
		$post = get_post( $post );
		if ( ! $post ) {
			return '';
		}

		$timestamp = 'modified' === $type ? get_post_modified_time( 'U', false, $post ) : get_post_time( 'U', false, $post );
		$jalali    = zc_jalali_date( (int) $timestamp );

		if ( '' === $jalali ) {
			return 'modified' === $type ? get_the_modified_date( '', $post ) : get_the_date( '', $post );
		}
		return $jalali;
	}
endif;

if ( ! function_exists( 'zc_jalali_date' ) ) :
	/**
	 * تاریخ شمسی از مُهر زمانی محلی («۲۴ شهریور ۱۴۰۵»)؛ اگر تاریخ شمسی خاموش باشد یا افزونه‌ی
	 * تاریخ شمسی فعال باشد، رشته‌ی خالی برمی‌گرداند تا خروجی خود وردپرس استفاده شود.
	 *
	 * @param int $timestamp مُهر زمانی (زمان محلی سایت).
	 * @return string
	 */
	function zc_jalali_date( $timestamp ) {
		$third_party = defined( 'WP_PARSI_VER' ) || function_exists( 'parsidate' ) || class_exists( 'WPP_ParsiDate' );
		if ( ! $timestamp || ! zc_switch( 'typo_jalali', true ) || $third_party ) {
			return '';
		}

		list( $jy, $jm, $jd ) = zc_gregorian_to_jalali( (int) gmdate( 'Y', $timestamp ), (int) gmdate( 'n', $timestamp ), (int) gmdate( 'j', $timestamp ) );

		$months = array( 1 => 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );

		return zc_digits_to_persian( $jd . ' ' . $months[ $jm ] . ' ' . $jy );
	}
endif;

if ( ! function_exists( 'zc_is_inverse_section' ) ) :
	/**
	 * آیا بخش صفحه اصلی با تُن تیره (سرمه‌ای) نمایش داده شود؟
	 *
	 * @param string $section شناسه بخش.
	 * @return bool
	 */
	function zc_is_inverse_section( $section ) {
		$selected = zc_opt( 'palette_inverse_sections', array() );

		if ( ! is_array( $selected ) || ! array_key_exists( $section, $selected ) ) {
			return false;
		}

		return ! empty( $selected[ $section ] ) && '0' !== (string) $selected[ $section ];
	}
endif;

if ( ! function_exists( 'zc_kses_svg' ) ) :
	/**
	 * پاک‌سازی HTML با اجازه‌ی آیکون‌های SVG داخلی قالب (برای صفحه‌بندی و …).
	 *
	 * @param string $html HTML.
	 * @return string
	 */
	function zc_kses_svg( $html ) {
		$allowed = wp_kses_allowed_html( 'post' );
		$svg     = array(
			'class'           => true,
			'xmlns'           => true,
			'width'           => true,
			'height'          => true,
			'viewbox'         => true,
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'aria-hidden'     => true,
			'focusable'       => true,
			'role'            => true,
			'd'               => true,
			'cx'              => true,
			'cy'              => true,
			'r'               => true,
			'x'               => true,
			'y'               => true,
			'x1'              => true,
			'x2'              => true,
			'y1'              => true,
			'y2'              => true,
			'rx'              => true,
			'points'          => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
		);
		foreach ( array( 'svg', 'path', 'circle', 'rect', 'line', 'polyline', 'polygon', 'g' ) as $tag ) {
			$allowed[ $tag ] = $svg;
		}
		return wp_kses( $html, $allowed );
	}
endif;

if ( ! function_exists( 'zc_blog_page_id' ) ) :
	/**
	 * شناسه برگه‌ی وبلاگ: «برگه نوشته‌ها»ی وردپرس یا برگه‌ی وبلاگِ ساخته‌شده با المنتور.
	 *
	 * @return int
	 */
	function zc_blog_page_id() {
		$id = (int) get_option( 'page_for_posts' );
		if ( ! $id ) {
			$id = (int) get_option( 'zc_blog_page' );
		}
		return ( $id && 'publish' === get_post_status( $id ) ) ? $id : 0;
	}
endif;

if ( ! function_exists( 'zc_blog_url' ) ) :
	/**
	 * نشانی وبلاگ.
	 *
	 * @return string
	 */
	function zc_blog_url() {
		$id = zc_blog_page_id();
		if ( $id ) {
			return (string) get_permalink( $id );
		}
		return 'posts' === get_option( 'show_on_front' ) ? home_url( '/' ) : (string) get_post_type_archive_link( 'post' );
	}
endif;

if ( ! function_exists( 'zc_elementor_has_widget' ) ) :
	/**
	 * آیا داده‌ی المنتورِ یک نوشته شامل ویجت مشخصی است؟
	 *
	 * @param int    $post_id شناسه نوشته.
	 * @param string|string[] $widget نام ویجت یا فهرست نام‌ها (مثلاً zc-page-title).
	 * @return bool
	 */
	function zc_elementor_has_widget( $post_id, $widget ) {
		$data = get_post_meta( (int) $post_id, '_elementor_data', true );
		if ( is_array( $data ) ) {
			$data = wp_json_encode( $data );
		}
		if ( ! is_string( $data ) ) {
			return false;
		}
		foreach ( (array) $widget as $name ) {
			if ( false !== strpos( $data, '"widgetType":"' . $name . '"' ) ) {
				return true;
			}
		}
		return false;
	}
endif;

if ( ! function_exists( 'zc_page_header_class' ) ) :
	/**
	 * کلاس‌های سربرگ برگه‌ها، بایگانی‌ها و نوشته‌ها (روشن یا سرمه‌ای).
	 *
	 * @param string $extra کلاس‌های اضافه.
	 * @return string
	 */
	function zc_page_header_class( $extra = '' ) {
		$classes = 'zc-page-hero relative overflow-hidden border-b border-line';

		if ( 'light' === (string) zc_opt( 'palette_page_header_tone', 'inverse' ) ) {
			$classes .= ' bg-surface2/50';
		} else {
			$classes .= ' zc-tone-inverse';
		}

		return trim( $classes . ' ' . $extra );
	}
endif;

if ( ! function_exists( 'zc_legal_info' ) ) :
	/**
	 * اطلاعات حقوقی و هویتی ارائه‌دهنده خدمات (برای پاورقی و برگه‌های قوانین).
	 *
	 * @param string $field کلید: owner|domain|domain_url|domain_note|license|license_label|pco_code|pco_label|degree|email|phone|mobile|address|city|hours|emergency|instagram.
	 * @return string
	 */
	function zc_legal_info( $field ) {
		$contact = zc_contact_fields();
		$domain  = trim( (string) zc_opt( 'legal_domain', 'Maryam-jamali.ir' ) );
		$domain  = '' !== $domain ? $domain : wp_parse_url( home_url(), PHP_URL_HOST );

		switch ( $field ) {
			case 'owner':
				return (string) zc_opt( 'legal_owner', get_bloginfo( 'name' ) );
			case 'domain':
				return (string) $domain;
			case 'domain_url':
				return 'https://' . strtolower( (string) $domain );
			case 'domain_note':
				return (string) zc_opt( 'legal_domain_note', '' );
			case 'license':
				$number = trim( (string) zc_opt( 'legal_license_no', '' ) );
				return '' !== $number ? $number : __( '(در حال درج توسط مدیر سایت)', 'zarincoach' );
			case 'pco_code':
				return trim( (string) zc_opt( 'legal_pco_code', '' ) );
			case 'pco_label':
				return (string) zc_opt( 'legal_pco_label', __( 'کد نظام روان‌شناسی', 'zarincoach' ) );
			case 'degree':
				return trim( (string) zc_opt( 'legal_degree', '' ) );
			case 'license_label':
				return (string) zc_opt( 'legal_license_label', __( 'شماره نظام روان‌شناسی و مشاوره', 'zarincoach' ) );
			case 'email':
			case 'phone':
			case 'address':
			case 'hours':
				return isset( $contact[ $field ] ) ? (string) $contact[ $field ] : '';
			case 'mobile':
				return (string) $contact['phone2'];
			case 'city':
				return (string) zc_opt( 'contact_city', '' );
			case 'emergency':
				return (string) zc_opt( 'legal_emergency', '' );
			case 'instagram':
				return (string) zc_opt( 'social_instagram', '' );
		}

		return '';
	}
endif;

if ( ! function_exists( 'zc_shortcode_info' ) ) :
	/**
	 * شورت‌کد [zc_info field="domain"] برای درج اطلاعات پنل در متن برگه‌ها.
	 *
	 * @param array|string $atts ویژگی‌ها.
	 * @return string
	 */
	function zc_shortcode_info( $atts ) {
		$atts  = shortcode_atts( array( 'field' => 'domain', 'link' => '' ), (array) $atts, 'zc_info' );
		$field = sanitize_key( $atts['field'] );
		$value = zc_legal_info( $field );

		if ( '' === $value ) {
			return '';
		}

		if ( 'domain' === $field && '' !== $atts['link'] ) {
			return '<a href="' . esc_url( zc_legal_info( 'domain_url' ) ) . '" dir="ltr">' . esc_html( $value ) . '</a>';
		}
		if ( 'email' === $field ) {
			return '<a href="mailto:' . esc_attr( $value ) . '" dir="ltr">' . esc_html( $value ) . '</a>';
		}
		if ( in_array( $field, array( 'phone', 'mobile' ), true ) ) {
			return '<a href="tel:' . esc_attr( zc_normalize_phone( $value ) ) . '" dir="ltr">' . esc_html( $value ) . '</a>';
		}

		return '<span class="zc-info zc-info-' . esc_attr( $field ) . '"' . ( 'domain' === $field ? ' dir="ltr"' : '' ) . '>' . esc_html( $value ) . '</span>';
	}
	add_shortcode( 'zc_info', 'zc_shortcode_info' );
endif;

if ( ! function_exists( 'zc_page_url_by_key' ) ) :
	/**
	 * نشانی برگه بر اساس کلید دمو (مستقل از ساختار پیوند یکتا).
	 *
	 * @param string $key      کلید برگه.
	 * @param string $fallback نامک جایگزین.
	 * @return string
	 */
	function zc_page_url_by_key( $key, $fallback = '' ) {
		static $cache = array();
		if ( ! isset( $cache[ $key ] ) ) {
			$found = get_posts(
				array(
					'post_type'        => 'page',
					'post_status'      => 'publish',
					'posts_per_page'   => 1,
					'fields'           => 'ids',
					'meta_key'         => '_zc_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'       => $key,            // phpcs:ignore WordPress.DB.SlowDBQuery
					'no_found_rows'    => true,
					'suppress_filters' => true,
				)
			);
			$cache[ $key ] = ! empty( $found ) ? (string) get_permalink( (int) $found[0] ) : '';
		}
		if ( '' !== $cache[ $key ] ) {
			return $cache[ $key ];
		}
		return home_url( '/' . trim( '' !== $fallback ? $fallback : $key, '/' ) . '/' );
	}
endif;

if ( ! function_exists( 'zc_shortcode_page_link' ) ) :
	/**
	 * شورت‌کد [zc_link page="refund" text="..."] پیوند به برگه‌های قوانین و خدمات.
	 *
	 * @param array|string $atts ویژگی‌ها.
	 * @return string
	 */
	function zc_shortcode_page_link( $atts ) {
		$atts = shortcode_atts( array( 'page' => '', 'text' => '', 'slug' => '' ), (array) $atts, 'zc_link' );
		$key  = sanitize_key( $atts['page'] );
		if ( '' === $key ) {
			return '';
		}
		$url  = zc_page_url_by_key( $key, (string) $atts['slug'] );
		$text = '' !== $atts['text'] ? (string) $atts['text'] : $key;
		return '<a href="' . esc_url( $url ) . '">' . esc_html( $text ) . '</a>';
	}
	add_shortcode( 'zc_link', 'zc_shortcode_page_link' );
endif;

if ( ! function_exists( 'zc_user_avatar_id' ) ) :
	/**
	 * شناسه‌ی تصویر نمایه‌ی اختصاصی کاربر (از کتابخانه رسانه).
	 *
	 * @param mixed $id_or_email شناسه، ایمیل، کاربر، نوشته یا دیدگاه.
	 * @return int
	 */
	function zc_user_avatar_id( $id_or_email ) {
		$user_id = 0;
		if ( is_numeric( $id_or_email ) ) {
			$user_id = (int) $id_or_email;
		} elseif ( $id_or_email instanceof WP_User ) {
			$user_id = (int) $id_or_email->ID;
		} elseif ( $id_or_email instanceof WP_Post ) {
			$user_id = (int) $id_or_email->post_author;
		} elseif ( $id_or_email instanceof WP_Comment ) {
			$user_id = (int) $id_or_email->user_id;
		} elseif ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			$user    = get_user_by( 'email', $id_or_email );
			$user_id = $user ? (int) $user->ID : 0;
		}
		if ( ! $user_id ) {
			return 0;
		}
		$att = (int) get_user_meta( $user_id, 'zc_avatar_id', true );
		return ( $att && wp_attachment_is_image( $att ) ) ? $att : 0;
	}
endif;

/**
 * تصویر نمایه‌ی اختصاصی به‌جای Gravatar (بدون درخواست به سرور خارجی).
 */
add_filter(
	'pre_get_avatar_data',
	static function ( $args, $id_or_email ) {
		$att = zc_user_avatar_id( $id_or_email );
		if ( ! $att ) {
			// آواتار محلی به‌جای Gravatar (در ایران کند/ناپایدار است و بارگذاری صفحه را معطل می‌کند).
			if ( ! is_admin() && zc_switch( 'perf_local_avatars', true ) ) {
				$args['url']           = ZC_URI . '/assets/img/avatar-default.svg';
				$args['found_avatars'] = true;
			}
			return $args;
		}
		$size = isset( $args['size'] ) ? (int) $args['size'] : 96;
		$src  = wp_get_attachment_image_src( $att, $size > 150 ? 'medium' : 'thumbnail' );
		if ( $src ) {
			$args['url']           = $src[0];
			$args['found_avatars'] = true;
		}
		return $args;
	},
	10,
	2
);

/**
 * فیلد «تصویر نمایه» در پروفایل کاربر.
 *
 * @param WP_User $user کاربر.
 */
function zc_user_avatar_field( $user ) {
	if ( ! current_user_can( 'upload_files' ) ) {
		return;
	}
	$att = (int) get_user_meta( $user->ID, 'zc_avatar_id', true );
	?>
	<h2><?php esc_html_e( 'تصویر نمایه (قالب زرین‌کوچ)', 'zarincoach' ); ?></h2>
	<table class="form-table" role="presentation">
		<tr>
			<th><label for="zc_avatar_id"><?php esc_html_e( 'شناسه تصویر از کتابخانه رسانه', 'zarincoach' ); ?></label></th>
			<td>
				<?php if ( $att && wp_attachment_is_image( $att ) ) : ?>
					<?php echo wp_get_attachment_image( $att, array( 64, 64 ), false, array( 'style' => 'border-radius:50%;vertical-align:middle;margin-inline-end:10px' ) ); ?>
				<?php endif; ?>
				<input type="number" min="0" name="zc_avatar_id" id="zc_avatar_id" value="<?php echo esc_attr( $att ? (string) $att : '' ); ?>" class="small-text" />
				<p class="description"><?php esc_html_e( 'در کتابخانه رسانه روی تصویر کلیک کنید و عدد «post=» در نشانی را اینجا وارد کنید. این تصویر در بخش نویسنده، سطر نویسنده و دیدگاه‌ها به‌جای Gravatar نمایش داده می‌شود. برای حذف، خالی بگذارید.', 'zarincoach' ); ?></p>
				<?php wp_nonce_field( 'zc_avatar_' . $user->ID, 'zc_avatar_nonce' ); ?>
			</td>
		</tr>
	</table>
	<?php
}
add_action( 'show_user_profile', 'zc_user_avatar_field' );
add_action( 'edit_user_profile', 'zc_user_avatar_field' );

/**
 * ذخیره‌ی فیلد تصویر نمایه.
 *
 * @param int $user_id شناسه کاربر.
 */
function zc_user_avatar_save( $user_id ) {
	if ( ! current_user_can( 'edit_user', $user_id ) || ! current_user_can( 'upload_files' ) ) {
		return;
	}
	if ( ! isset( $_POST['zc_avatar_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zc_avatar_nonce'] ) ), 'zc_avatar_' . $user_id ) ) {
		return;
	}
	$att = isset( $_POST['zc_avatar_id'] ) ? absint( $_POST['zc_avatar_id'] ) : 0;
	if ( $att && wp_attachment_is_image( $att ) ) {
		update_user_meta( $user_id, 'zc_avatar_id', $att );
	} else {
		delete_user_meta( $user_id, 'zc_avatar_id' );
	}
}
add_action( 'personal_options_update', 'zc_user_avatar_save' );
add_action( 'edit_user_profile_update', 'zc_user_avatar_save' );

if ( ! function_exists( 'zc_theme_color_meta' ) ) :
	/**
	 * متای theme-color (رنگ نوار مرورگر موبایل) بر پایه‌ی تنظیم «برند».
	 *
	 * @return void
	 */
	function zc_theme_color_meta() {
		$palette = function_exists( 'zc_get_palette' ) ? zc_get_palette() : array();
		$light   = isset( $palette['light']['base'] ) ? $palette['light']['base'] : '#F5F7FB';
		$dark    = isset( $palette['dark']['base'] ) ? $palette['dark']['base'] : '#0B1B3A';
		$mode    = (string) zc_opt( 'brand_theme_color_mode', 'auto' );

		if ( 'custom' === $mode ) {
			$color = sanitize_hex_color( (string) zc_opt( 'brand_theme_color', '#0B1B3A' ) );
			printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $color ? $color : '#0B1B3A' ) );
			return;
		}
		if ( 'brand' === $mode ) {
			$brand = isset( $palette['light']['secondary'] ) ? $palette['light']['secondary'] : '#0B1B3A';
			printf( '<meta name="theme-color" content="%s">' . "\n", esc_attr( $brand ) );
			return;
		}
		printf( '<meta name="theme-color" content="%s" media="(prefers-color-scheme: light)">' . "\n", esc_attr( $light ) );
		printf( '<meta name="theme-color" content="%s" media="(prefers-color-scheme: dark)">' . "\n", esc_attr( $dark ) );
	}
endif;
add_action( 'wp_head', 'zc_theme_color_meta', 2 );

if ( ! function_exists( 'zc_front_body_class' ) ) :
	/**
	 * کلاس‌های سراسری بدنه بر پایه‌ی تنظیمات پنل.
	 *
	 * @param string[] $classes کلاس‌ها.
	 * @return string[]
	 */
	function zc_front_body_class( $classes ) {
		if ( ! zc_switch( 'typo_justify', true ) ) {
			$classes[] = 'zc-no-justify';
		}
		return $classes;
	}
endif;
add_filter( 'body_class', 'zc_front_body_class' );

if ( ! function_exists( 'zc_strip_comment_url' ) ) :
	/**
	 * وقتی فیلد «وب‌سایت» خاموش است، نشانی ارسالیِ مستقیم ربات‌ها هم ذخیره نشود.
	 *
	 * @param array $data داده‌ی دیدگاه.
	 * @return array
	 */
	function zc_strip_comment_url( $data ) {
		if ( ! zc_switch( 'comments_url_field', false ) && empty( $data['user_id'] ) ) {
			$data['comment_author_url'] = '';
		}
		return $data;
	}
endif;
add_filter( 'preprocess_comment', 'zc_strip_comment_url' );
