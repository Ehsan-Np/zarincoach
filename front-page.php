<?php
/**
 * صفحه اصلی
 *
 * بخش‌ها بر اساس تنظیمات پنل (ترتیب و فعال/غیرفعال بودن) چاپ می‌شوند.
 * اگر صفحه اصلی با المنتور ساخته شده باشد، محتوای المنتور نمایش داده می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$front_id = (int) get_option( 'page_on_front' );

if ( $front_id && zc_page_uses_elementor( $front_id ) && zc_is_elementor_active() ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

// اگر صفحه اصلی محتوای واقعی داشته باشد (ساخته‌شده با ویرایشگر یا هر صفحه‌ساز دیگر)، همان نمایش داده می‌شود.
if ( $front_id && '' !== trim( (string) get_post_field( 'post_content', $front_id ) ) ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

get_header();

/**
 * فهرست بخش‌های فعال به ترتیب تنظیمات.
 */
$order = (array) zc_opt( 'home_sections_order', array() );
$order = isset( $order['enabled'] ) ? (array) $order['enabled'] : array();

if ( empty( $order ) ) {
	$order = array(
		'hero'         => '',
		'marquee'      => '',
		'about'        => '',
		'services'     => '',
		'schema'       => '',
		'process'      => '',
		'stats'        => '',
		'testimonials' => '',
		'faq'          => '',
		'blog'         => '',
		'cta'          => '',
	);
}

$valid = array( 'hero', 'marquee', 'about', 'services', 'schema', 'process', 'stats', 'testimonials', 'faq', 'blog', 'cta' );

foreach ( $order as $section => $label ) {
	if ( 'placebo' === $section || ! in_array( (string) $section, $valid, true ) ) {
		continue;
	}
	$inverse = zc_is_inverse_section( (string) $section );

	if ( $inverse ) {
		echo '<div class="zc-tone-inverse zc-tone-section" data-zc-section="' . esc_attr( (string) $section ) . '">';
	}

	get_template_part( 'template-parts/front-page/' . (string) $section );

	if ( $inverse ) {
		echo '</div>';
	}
}

get_footer();
