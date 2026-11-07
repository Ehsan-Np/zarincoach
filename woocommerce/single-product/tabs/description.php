<?php
/**
 * زرین‌کوچ — قالب بخش «معرفی محصول» (توضیحات) — v2.8.1
 *
 * تضمین پاراگراف‌بندی: اگر محتوا «متنی ساده» باشد (اینتر دارد ولی هیچ نشانه‌گذاری
 * HTML/بلوکی ندارد)، پاراگراف‌بندی (wpautop) قطعاً اجرا می‌شود حتی اگر افزونه‌ای
 * فیلتر wpautop را از the_content برداشته باشد — تا اینترها و خطوط نوشته‌ی فروشنده
 * همیشه در صفحه‌ی محصول حفظ شوند.
 *
 * نسخه‌ی الگوی اصلی: 9.4.0
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$content = (string) get_the_content();
$is_plain = '' !== trim( $content )
	&& false !== strpos( $content, "\n" )
	&& ! preg_match( '#<!--\s*wp:#', $content )
	&& ! preg_match( '#<(p|div|h[1-6]|ul|ol|li|table|blockquote|figure|pre|br)\b#i', $content );

if ( $is_plain ) {
	// متنی ساده با اینتر: پاراگراف‌بندی پیش از زنجیره‌ی the_content تا نتیجه قطعی باشد.
	echo apply_filters( 'the_content', wpautop( $content ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی استاندارد the_content.
} else {
	the_content();
}
