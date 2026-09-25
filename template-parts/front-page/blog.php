<?php
/**
 * بخش آخرین نوشته‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_blog_enable', true ) ) {
	return;
}

$posts_query = new WP_Query(
	array(
		'post_type'           => 'post',
		'posts_per_page'      => (int) zc_opt( 'home_blog_count', 3 ),
		'post_status'         => 'publish',
		'ignore_sticky_posts' => true,
		'no_found_rows'       => true,
	)
);

if ( ! $posts_query->have_posts() ) {
	return;
}

$delay = 0;
?>

<section id="blog" class="zc-section relative">
	<div class="zc-container">
		<div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
			<?php
			zc_section_heading(
				array(
					'eyebrow' => (string) zc_opt( 'home_blog_eyebrow', '' ),
					'title'   => (string) zc_opt( 'home_blog_title', '' ),
					'align'   => 'start',
					'class'   => '!max-w-2xl',
				)
			);
			?>

			<?php if ( '' !== (string) zc_opt( 'home_blog_button', '' ) ) : ?>
				<?php
				zc_button(
					array(
						'text'  => (string) zc_opt( 'home_blog_button', '' ),
						'url'   => zc_blog_url(),
						'style' => 'outline',
						'class' => 'shrink-0',
					)
				);
				?>
			<?php endif; ?>
		</div>

		<div class="mt-10 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
			<?php
			while ( $posts_query->have_posts() ) :
				$posts_query->the_post();
				zc_post_card( get_the_ID(), array( 'delay' => $delay ) );
				$delay += 90;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
