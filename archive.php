<?php
/**
 * قالب بایگانی‌ها، دسته‌ها و برچسب‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();

$layout     = (string) zc_opt( 'archive_layout', 'grid' );
$columns    = (string) zc_opt( 'archive_columns', '3' );
$has_sidebar = 'sidebar' === $layout && zc_has_sidebar();

$grid_class = 'md:grid-cols-2 lg:grid-cols-3';
if ( '2' === $columns ) {
	$grid_class = 'md:grid-cols-2';
} elseif ( '4' === $columns ) {
	$grid_class = 'sm:grid-cols-2 lg:grid-cols-4';
}
if ( $has_sidebar ) {
	$grid_class = 'xl:grid-cols-2';
}

$title = (string) zc_opt( 'blog_hero_title', '' );
if ( is_category() ) {
	$title = single_cat_title( '', false );
} elseif ( is_tag() ) {
	$title = single_tag_title( '', false );
} elseif ( is_author() ) {
	$title = get_the_author();
} elseif ( is_date() ) {
	$title = get_the_date( 'F Y' );
} elseif ( is_post_type_archive() ) {
	$title = post_type_archive_title( '', false );
} elseif ( is_home() ) {
	$title = get_the_title( get_option( 'page_for_posts' ) );
}

$description = (string) zc_opt( 'blog_hero_text', '' );
if ( is_category() || is_tag() ) {
	$term_description = term_description();
	if ( $term_description ) {
		$description = wp_strip_all_tags( $term_description );
	}
}
?>

<section class="<?php echo esc_attr( zc_page_header_class() ); ?>">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
	<div class="pointer-events-none absolute -top-24 end-1/3 -z-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>

	<div class="zc-container relative py-10 lg:py-14">
		<?php if ( zc_switch( 'seo_breadcrumbs', true ) ) : ?>
			<div class="mb-4"><?php zc_breadcrumbs(); ?></div>
		<?php endif; ?>

		<h1 class="zc-title-lg zc-reveal"><?php echo esc_html( $title ); ?></h1>

		<?php if ( '' !== $description ) : ?>
			<p class="zc-lead mt-4 max-w-2xl zc-reveal" data-zc-delay="80"><?php echo esc_html( $description ); ?></p>
		<?php endif; ?>
	</div>
</section>

<section class="zc-section">
	<div class="zc-container">
		<?php if ( have_posts() ) : ?>

			<div class="<?php echo $has_sidebar ? 'grid gap-10 lg:grid-cols-12' : ''; ?>">
				<div class="<?php echo $has_sidebar ? 'lg:col-span-8' : ''; ?>">

					<?php if ( 'list' === $layout ) : ?>
						<div class="grid gap-6">
							<?php
							$delay = 0;
							while ( have_posts() ) :
								the_post();
								zc_post_card( get_the_ID(), array( 'layout' => 'horizontal', 'delay' => $delay ) );
								$delay += 70;
							endwhile;
							?>
						</div>
					<?php else : ?>
						<div class="grid gap-6 <?php echo esc_attr( $grid_class ); ?>">
							<?php
							$delay = 0;
							while ( have_posts() ) :
								the_post();
								zc_post_card( get_the_ID(), array( 'delay' => $delay ) );
								$delay += 80;
							endwhile;
							?>
						</div>
					<?php endif; ?>

					<?php zc_pagination(); ?>
				</div>

				<?php if ( $has_sidebar ) : ?>
					<aside class="lg:col-span-4">
						<?php get_sidebar(); ?>
					</aside>
				<?php endif; ?>
			</div>

		<?php else : ?>

			<?php get_template_part( 'template-parts/content', 'none' ); ?>

		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
