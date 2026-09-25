<?php
/**
 * کتابخانه‌ی آیکون‌های داخلی قالب (SVG سبک، بدون وابستگی خارجی)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_icons' ) ) :
	/**
	 * تعریف مسیرهای SVG آیکون‌ها.
	 *
	 * @return array<string, string>
	 */
	function zc_icons() {
		return array(
			// جهت‌نما و پیمایش.
			'arrow-left'    => '<path d="M19 12H5"/><path d="m12 19-7-7 7-7"/>',
			'arrow-right'   => '<path d="M5 12h14"/><path d="m12 5 7 7-7 7"/>',
			'arrow-up'      => '<path d="M12 19V5"/><path d="m5 12 7-7 7 7"/>',
			'arrow-down'    => '<path d="M12 5v14"/><path d="m19 12-7 7-7-7"/>',
			'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
			'chevron-left'  => '<path d="m15 18-6-6 6-6"/>',
			'chevron-right' => '<path d="m9 18 6-6-6-6"/>',
			'chevron-up'    => '<path d="m18 15-6-6-6 6"/>',
			'list'          => '<path d="M9 6h11M9 12h11M9 18h11"/><path d="M4.5 6h.01M4.5 12h.01M4.5 18h.01"/>',
			'link'          => '<path d="M10 13a5 5 0 0 0 7.5.5l3-3a5 5 0 0 0-7-7l-1.7 1.7"/><path d="M14 11a5 5 0 0 0-7.5-.5l-3 3a5 5 0 0 0 7 7l1.7-1.7"/>',
			'share'         => '<circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><path d="m8.6 13.5 6.8 4M15.4 6.5l-6.8 4"/>',
			'reply'         => '<path d="m9 17-5-5 5-5"/><path d="M20 18v-2a4 4 0 0 0-4-4H4"/>',
			'hash'          => '<path d="M5 9h14M4 15h14M10 3 8 21M16 3l-2 18"/>',
			'credit-card'   => '<rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="M2.5 10h19M6.5 15h4"/>',
			'shield-check'  => '<path d="M12 3 4.5 6v5.5c0 4.6 3.1 8.4 7.5 9.5 4.4-1.1 7.5-4.9 7.5-9.5V6Z"/><path d="m8.8 12 2.2 2.2 4.3-4.4"/>',
			'facebook'      => '<path d="M15 3h-2.5A4.5 4.5 0 0 0 8 7.5V10H5.5v3.5H8V21h3.5v-7.5H14l.5-3.5h-3V8c0-.6.4-1 1-1H15Z"/>',
			'menu'          => '<path d="M4 6h16M4 12h16M4 18h16"/>',
			'close'         => '<path d="M18 6 6 18M6 6l12 12"/>',
			'search'        => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
			'plus'          => '<path d="M12 5v14M5 12h14"/>',
			'minus'         => '<path d="M5 12h14"/>',
			'check'         => '<path d="M20 6 9 17l-5-5"/>',
			'external'      => '<path d="M15 3h6v6"/><path d="M10 14 21 3"/><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/>',
			'download'      => '<path d="M12 3v12"/><path d="m7 10 5 5 5-5"/><path d="M5 21h14"/>',

			// ارتباطی.
			'phone'         => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 1.9.7 2.8a2 2 0 0 1-.5 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.8.7a2 2 0 0 1 1.7 2Z"/>',
			'mail'          => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
			'map-pin'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
			'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
			'calendar'      => '<rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 10h18"/>',
			'message'       => '<path d="M21 11.5a8.4 8.4 0 0 1-9 8.4 9.9 9.9 0 0 1-3.8-.8L3 21l1.9-4.7A8.4 8.4 0 0 1 4 11.5 8.5 8.5 0 0 1 12.5 3 8.4 8.4 0 0 1 21 11.5Z"/>',
			'send'          => '<path d="M22 2 11 13"/><path d="M22 2l-7 20-4-9-9-4Z"/>',
			'bale'          => '<path d="M12 3a9 9 0 0 1 0 18H4.5a1.5 1.5 0 0 1-1.5-1.5V12a9 9 0 0 1 9-9Z"/><path d="M8 9.5v5a2 2 0 0 0 2 2h2.2a2.6 2.6 0 0 0 0-5.2H8"/>',
			'eitaa'         => '<rect x="3" y="3" width="18" height="18" rx="6"/><path d="M15.5 14.5a4 4 0 1 1 .6-4.5H8.2"/>',
			'video'         => '<rect x="2" y="6" width="14" height="12" rx="2"/><path d="m22 8-6 4 6 4Z"/>',

			// مفاهیم کوچینگ و روان‌شناسی.
			'target'        => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4"/>',
			'brain'         => '<path d="M12 5a3 3 0 0 0-6 0v1a3 3 0 0 0-1 5.8V14a3 3 0 0 0 3 3h1a2 2 0 0 0 2-2V5Z"/><path d="M12 5a3 3 0 0 1 6 0v1a3 3 0 0 1 1 5.8V14a3 3 0 0 1-3 3h-1a2 2 0 0 1-2-2V5Z"/><path d="M12 5v14"/>',
			'heart'         => '<path d="M20.8 5.6a5.4 5.4 0 0 0-7.7 0L12 6.7l-1.1-1.1a5.4 5.4 0 0 0-7.7 7.7L12 21.5l8.8-8.2a5.4 5.4 0 0 0 0-7.7Z"/>',
			'sparkles'      => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4"/><path d="m6.3 6.3 2.2 2.2M15.5 15.5l2.2 2.2M6.3 17.7l2.2-2.2M15.5 8.5l2.2-2.2"/>',
			'seedling'      => '<path d="M12 22V11"/><path d="M12 11C12 8 9.5 5 5 5c0 4.5 3 6 7 6Z"/><path d="M12 11c0-2 2.5-4 6-4 0 3.5-2.5 4-6 4Z"/>',
			'compass'       => '<circle cx="12" cy="12" r="9"/><path d="m15.5 8.5-2 5.5-5.5 2 2-5.5Z"/>',
			'mirror'        => '<path d="M12 3v18"/><path d="M9 21h6"/><circle cx="12" cy="10" r="5"/>',
			'shield'        => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10Z"/><path d="m9 12 2 2 4-4"/>',
			'alert'         => '<path d="M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0Z"/><path d="M12 9v4M12 17h.01"/>',
			'clipboard'     => '<rect x="8" y="2" width="8" height="4" rx="1"/><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><path d="m9 14 2 2 4-4"/>',
			'briefcase'     => '<rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 7V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2"/><path d="M2 13h20"/>',
			'graduation'    => '<path d="M22 10 12 5 2 10l10 5 10-5Z"/><path d="M6 12v5c3 2.5 9 2.5 12 0v-5"/><path d="M22 10v6"/>',
			'file-text'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h5"/>',
			'refresh'       => '<path d="M21 12a9 9 0 0 1-15.5 6.2L3 16"/><path d="M3 12a9 9 0 0 1 15.5-6.2L21 8"/><path d="M21 3v5h-5M3 21v-5h5"/>',
			'user'          => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
			'globe'         => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
			'trending-up'   => '<path d="m3 17 6-6 4 4 8-8"/><path d="M17 7h4v4"/>',
			'users'         => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.9"/><path d="M16 3.1a4 4 0 0 1 0 7.8"/>',
			'book'          => '<path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2Z"/>',
			'printer'       => '<path d="M6 9V3h12v6"/><rect x="3" y="9" width="18" height="8" rx="2"/><path d="M6 14h12v7H6z"/><path d="M17 12h.01"/>',
			'badge-check'   => '<path d="M12 2.8 14.4 4.5l2.9-.2.9 2.8 2.4 1.7-.9 2.8.9 2.8-2.4 1.7-.9 2.8-2.9-.2L12 21.2l-2.4-1.7-2.9.2-.9-2.8-2.4-1.7.9-2.8-.9-2.8 2.4-1.7.9-2.8 2.9.2Z"/><path d="m8.8 12.2 2.2 2.2 4.4-4.6"/>',
			'building'      => '<rect x="4" y="2.5" width="16" height="19" rx="2"/><path d="M9 21.5v-4h6v4M8.5 7h1M14.5 7h1M8.5 11h1M14.5 11h1M8.5 15h1M14.5 15h1"/>',
			'id-card'       => '<rect x="2.5" y="5" width="19" height="14" rx="2"/><circle cx="8.5" cy="11" r="2.2"/><path d="M5.3 16.2a3.6 3.6 0 0 1 6.4 0M14 10h4.5M14 13.5h3"/>',
			'award'         => '<circle cx="12" cy="8" r="6"/><path d="m8.5 13.5-1.5 8 5-3 5 3-1.5-8"/>',
			'quote'         => '<path d="M9 7H5a2 2 0 0 0-2 2v3h4a1 1 0 0 1 0 2H3v1a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/><path d="M20 7h-4a2 2 0 0 0-2 2v3h4a1 1 0 0 1 0 2h-4v1a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>',
			'star'          => '<path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9Z"/>',
			'hand'          => '<path d="M18 11V6a1.6 1.6 0 0 0-3.2 0"/><path d="M14.8 11V4.2a1.6 1.6 0 0 0-3.2 0V11"/><path d="M11.6 11V5.6a1.6 1.6 0 0 0-3.2 0V13"/><path d="M8.4 13V9.8a1.6 1.6 0 1 0-3.2 0V15a7 7 0 0 0 7 7h2.6a5.4 5.4 0 0 0 5.4-5.4V11"/>',
			'eye'           => '<path d="M2 12s3.6-6.5 10-6.5S22 12 22 12s-3.6 6.5-10 6.5S2 12 2 12Z"/><circle cx="12" cy="12" r="3"/>',
			'lock'          => '<rect x="4" y="10" width="16" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
			'sun'           => '<circle cx="12" cy="12" r="4.2"/><path d="M12 2v2.5M12 19.5V22M2 12h2.5M19.5 12H22M4.9 4.9l1.8 1.8M17.3 17.3l1.8 1.8M4.9 19.1l1.8-1.8M17.3 6.7l1.8-1.8"/>',
			'moon'          => '<path d="M21 12.8A8.5 8.5 0 1 1 11.2 3a6.6 6.6 0 0 0 9.8 9.8Z"/>',
			'leaf'          => '<path d="M11 20A7 7 0 0 1 4 13c0-6 6-10 16-10 0 9-5 17-9 17Z"/><path d="M4 21c2-6 6-9 12-11"/>',
			'chat'          => '<path d="M21 15a3 3 0 0 1-3 3H8l-5 3V6a3 3 0 0 1 3-3h12a3 3 0 0 1 3 3Z"/>',
			'play'          => '<circle cx="12" cy="12" r="9"/><path d="m10 8.5 6 3.5-6 3.5Z"/>',

			// شبکه‌های اجتماعی.
			'instagram'     => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/>',
			'whatsapp'      => '<path d="M20.5 11.6A8.4 8.4 0 0 1 7.9 19l-4.4 1.5 1.5-4.3A8.4 8.4 0 1 1 20.5 11.6Z"/><path d="M8.5 9.2c0 4 3.3 7.3 7.3 7.3.6 0 1.2-.1 1.7-.3"/><path d="M8.8 8.4c-.2.6-.4 1.2-.4 1.8 0 3.4 2.8 6.2 6.2 6.2.6 0 1.2-.1 1.8-.3"/>',
			'telegram'      => '<path d="M21.5 4.5 2.8 11.4c-1 .4-1 1.3.1 1.6l4.6 1.4 1.8 5.5c.3.9 1 1 1.5.3l2.2-2.6 4.5 3.3c.8.5 1.4.2 1.6-.8l2.9-13.6c.3-1.3-.5-1.9-1.5-1.5Z"/><path d="m8.5 14.4 8.4-5.3c.4-.3.8-.1.5.2l-4.3 4"/>',
			'linkedin'      => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10v7M8 7v.01M12 17v-4a2 2 0 0 1 4 0v4"/>',
			'twitter'       => '<path d="M3 3h4.5l4.5 6 5-6H21l-7 8.5L21.5 21H17l-5-6.5L6.5 21H3l7.5-9Z"/>',
			'youtube'       => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10.5 9.5 5 2.5-5 2.5Z"/>',
			'aparat'        => '<circle cx="12" cy="12" r="9"/><path d="M12 8v8M8 12h8"/>',
		);
	}
endif;

if ( ! function_exists( 'zc_icon' ) ) :
	/**
	 * خروجی یک آیکون SVG.
	 *
	 * @param string $name  نام آیکون.
	 * @param string $class کلاس‌های CSS (پیش‌فرض h-5 w-5).
	 * @param bool   $echo  چاپ مستقیم (پیش‌فرض) یا بازگرداندن رشته با false.
	 * @return string|void
	 */
	function zc_icon( $name, $class = 'h-5 w-5', $echo = true ) {
		$icons = zc_icons();
		$name  = (string) $name;

		if ( ! array_key_exists( $name, $icons ) ) {
			$name = 'sparkles';
		}

		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round" class="' . esc_attr( $class ) . '" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';

		/**
		 * فیلتر خروجی آیکون‌ها.
		 *
		 * @param string $svg  خروجی SVG.
		 * @param string $name نام آیکون.
		 */
		$svg = apply_filters( 'zc_icon', $svg, $name, $class );

		if ( $echo ) {
			echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- SVG ایمن و ایستا است
			return;
		}

		return $svg;
	}
endif;

if ( ! function_exists( 'zc_icon_choice' ) ) :
	/**
	 * فهرست آیکون‌ها برای استفاده در فیلدهای انتخابی پنل تنظیمات.
	 *
	 * @return array<string, string>
	 */
	function zc_icon_choice() {
		return array(
			'target'      => __( 'هدف', 'zarincoach' ),
			'brain'       => __( 'ذهن', 'zarincoach' ),
			'heart'       => __( 'قلب', 'zarincoach' ),
			'sparkles'    => __( 'درخشش', 'zarincoach' ),
			'seedling'    => __( 'رشد', 'zarincoach' ),
			'compass'     => __( 'قطب‌نما', 'zarincoach' ),
			'mirror'      => __( 'آینه', 'zarincoach' ),
			'shield'      => __( 'سپر', 'zarincoach' ),
			'trending-up' => __( 'پیشرفت', 'zarincoach' ),
			'users'       => __( 'افراد', 'zarincoach' ),
			'book'        => __( 'کتاب', 'zarincoach' ),
			'award'       => __( 'افتخار', 'zarincoach' ),
			'check'       => __( 'تیک', 'zarincoach' ),
			'badge-check' => __( 'تأییدشده', 'zarincoach' ),
			'id-card'     => __( 'کارت شناسایی / پروانه', 'zarincoach' ),
			'building'    => __( 'سازمان / مرکز', 'zarincoach' ),
			'printer'     => __( 'چاپ', 'zarincoach' ),
			'clock'       => __( 'زمان', 'zarincoach' ),
			'map-pin'     => __( 'موقعیت', 'zarincoach' ),
			'quote'       => __( 'نقل‌قول', 'zarincoach' ),
			'star'        => __( 'ستاره', 'zarincoach' ),
			'hand'        => __( 'همراهی', 'zarincoach' ),
			'eye'         => __( 'آگاهی', 'zarincoach' ),
			'lock'        => __( 'امنیت', 'zarincoach' ),
			'leaf'        => __( 'آرامش', 'zarincoach' ),
			'chat'        => __( 'گفتگو', 'zarincoach' ),
			'video'       => __( 'جلسه آنلاین', 'zarincoach' ),
			'calendar'    => __( 'زمان‌بندی', 'zarincoach' ),
			'message'     => __( 'پیام', 'zarincoach' ),
			'bale'        => __( 'بله', 'zarincoach' ),
			'send'        => __( 'تلگرام / ارسال', 'zarincoach' ),
			'mail'        => __( 'ایمیل', 'zarincoach' ),
			'phone'       => __( 'تماس', 'zarincoach' ),
			'clipboard'   => __( 'تست و ارزیابی', 'zarincoach' ),
			'briefcase'   => __( 'مدیران و سازمان', 'zarincoach' ),
			'graduation'  => __( 'دوره آموزشی', 'zarincoach' ),
			'file-text'   => __( 'سند و قانون', 'zarincoach' ),
			'refresh'     => __( 'بازگشت وجه', 'zarincoach' ),
			'user'        => __( 'حساب کاربری', 'zarincoach' ),
			'globe'       => __( 'آنلاین', 'zarincoach' ),
			'alert'       => __( 'هشدار', 'zarincoach' ),
		);
	}
endif;
