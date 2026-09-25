<?php
/**
 * قالب برگه‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( zc_page_uses_elementor( get_the_ID() ) && zc_is_elementor_active() ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

get_header();
?>

<?php get_template_part( 'template-parts/page', 'header' ); ?>

<section class="zc-section">
	<div class="zc-container">
		<div class="w-full">
			<article id="post-<?php the_ID(); ?>" <?php post_class( 'zc-prose' ); ?>>
				<?php
				while ( have_posts() ) :
					the_post();

					the_content();

					wp_link_pages(
						array(
							'before' => '<nav class="zc-pagination mt-8" aria-label="' . esc_attr__( 'صفحات', 'zarincoach' ) . '">',
							'after'  => '</nav>',
						)
					);
				endwhile;
				?>
			</article>

			<?php
			if ( comments_open() || get_comments_number() ) {
				comments_template();
			}
			?>
		</div>
	</div>
</section>

<?php
get_footer();
