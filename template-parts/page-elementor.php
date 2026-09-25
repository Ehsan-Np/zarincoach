<?php
/**
 * نمایش محتوای ساخته‌شده با المنتور (بدون قاب اضافی قالب)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();

// سربرگ برگه برای صفحات داخلی ساخته‌شده با المنتور؛ به جز صفحه اصلی، صفحاتی که سربرگ را
// غیرفعال کرده‌اند و صفحاتی که خودشان ویجت المنتور «عنوان برگه» دارند.
if (
	! is_front_page()
	&& is_singular( array( 'page', 'zc_service' ) )
	&& ! get_post_meta( get_queried_object_id(), '_zc_hide_page_header', true )
	&& ! zc_elementor_has_widget( get_queried_object_id(), (array) apply_filters( 'zc_page_title_widgets', array( 'zc-page-title', 'zc-resume-hero' ) ) )
) {
	get_template_part( 'template-parts/page', 'header' );
}
?>

<div class="zc-page-content elementor-page">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>

<?php
get_footer();
