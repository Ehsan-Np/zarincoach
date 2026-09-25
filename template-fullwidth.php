<?php
/**
 * Template Name: عرض کامل (بخش‌های قالب)
 * Template Post Type: page
 *
 * برگه‌ی تمام‌عرض برای چیدن بخش‌های آماده‌ی قالب با شورت‌کد [zc_section]
 * (بدون نیاز به المنتور). اگر برگه با المنتور ساخته شده باشد، محتوای المنتور نمایش داده می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( zc_page_uses_elementor( get_the_ID() ) && zc_is_elementor_active() ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

get_header();

get_template_part( 'template-parts/page', 'header' );
?>

<div class="zc-fullwidth-content">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>

<?php
get_footer();
