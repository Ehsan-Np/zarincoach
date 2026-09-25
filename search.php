<?php
/**
 * قالب نتایج جستجو
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="<?php echo esc_attr( zc_page_header_class() ); ?>">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>

	<div class="zc-container relative py-10 lg:py-12">
		<?php if ( zc_switch( 'seo_breadcrumbs', true ) ) : ?>
			<div class="mb-4"><?php zc_breadcrumbs(); ?></div>
		<?php endif; ?>

		<h1 class="zc-title-lg">
			<?php
			/* translators: %s: عبارت جستجو شده */
			printf( esc_html__( 'نتایج جستجو برای: %s', 'zarincoach' ), '<span class="zc-gradient-text">' . esc_html( get_search_query() ) . '</span>' );
			?>
		</h1>

		<div class="mt-6 max-w-xl">
			<?php get_search_form(); ?>
		</div>
	</div>
</section>

<section class="zc-section">
	<div class="zc-container">
		<?php if ( have_posts() ) : ?>
			<p class="zc-lead mb-8">
				<?php
				global $wp_query;
				/* translators: %d: تعداد نتایج */
				printf( esc_html__( 'تعداد نتایج یافت‌شده: %d', 'zarincoach' ), (int) $wp_query->found_posts );
				?>
			</p>

			<div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				$delay = 0;
				while ( have_posts() ) :
					the_post();
					zc_post_card( get_the_ID(), array( 'delay' => $delay ) );
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
