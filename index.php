<?php
/**
 * قالب پیش‌فرض (Fallback)
 *
 * در صورتی که قالب تخصصی‌تری برای صفحه پیدا نشود، این فایل اجرا می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( is_front_page() ) {
	get_template_part( 'front-page' );
	return;
}

get_header();
?>

<section class="zc-section">
	<div class="zc-container">
		<?php if ( have_posts() ) : ?>
			<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				$delay = 0;
				while ( have_posts() ) :
					the_post();
					get_template_part( 'template-parts/content', get_post_type() );
					$delay += 70;
				endwhile;
				?>
			</div>
			<?php zc_pagination(); ?>
		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
