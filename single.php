<?php
/**
 * قالب صفحه‌ی هر نوشته
 *
 * سربرگ مجله‌ای (دسته، عنوان، خلاصه، نویسنده و فراداده) + نوار پیشرفت مطالعه، اشتراک‌گذاری در سه جایگاه،
 * فهرست مطالب چندستونه، باکس «بیشتر بخوانید» درون متن، برچسب‌ها، معرفی نویسنده، نوشته‌ی قبلی/بعدی و مطالب مرتبط.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();

$layout      = (string) zc_opt( 'post_layout', 'narrow' );
$has_sidebar = 'sidebar' === $layout && zc_has_sidebar();
$share_on    = zc_switch( 'post_share_enable', true );
$share_top   = $share_on && zc_switch( 'post_share_top', true );
$share_side  = $share_on && zc_switch( 'post_share_sticky', true );
$share_box   = $share_on && zc_switch( 'post_share_bottom', true );
$wrap        = $has_sidebar ? '' : ( 'narrow' === $layout ? 'mx-auto max-w-3xl' : '' );
$head_wrap   = 'narrow' === $layout ? 'mx-auto max-w-3xl' : ( $has_sidebar ? 'max-w-4xl' : 'max-w-5xl' );
?>

<?php if ( zc_switch( 'post_progress_bar', true ) ) : ?>
	<div class="zc-read-progress" data-zc-progress aria-hidden="true"><span></span></div>
<?php endif; ?>

<?php
while ( have_posts() ) :
	the_post();
	$excerpt = has_excerpt() && zc_switch( 'post_show_excerpt', true ) ? get_the_excerpt() : '';
	?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'zc-single' ); ?>>

	<!-- سربرگ مقاله -->
	<header class="<?php echo esc_attr( zc_page_header_class( 'zc-single-head' ) ); ?>">
		<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-40"></div>
		<div class="zc-container relative pb-8 pt-10 lg:pb-10 lg:pt-14">
			<div class="<?php echo esc_attr( $head_wrap ); ?>">
				<?php if ( zc_switch( 'seo_breadcrumbs', true ) ) : ?>
					<div class="mb-5"><?php zc_breadcrumbs(); ?></div>
				<?php endif; ?>

				<?php zc_post_cat_badges(); ?>

				<h1 class="zc-single-title zc-text-balance"><?php echo esc_html( get_the_title() ); ?></h1>

				<?php if ( '' !== $excerpt ) : ?>
					<p class="zc-single-dek"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<div class="zc-single-metarow">
					<?php zc_post_byline(); ?>
					<?php
					if ( $share_top ) {
						zc_share_render( 'inline' );
					}
					?>
				</div>
			</div>
		</div>
	</header>

	<div class="zc-section-tight !pt-8">
		<div class="zc-container">
			<div class="<?php echo esc_attr( $has_sidebar ? 'grid gap-10 lg:grid-cols-12' : $wrap ); ?>">

				<div class="<?php echo $has_sidebar ? 'min-w-0 lg:col-span-8' : 'min-w-0'; ?>">
					<?php if ( has_post_thumbnail() ) : ?>
						<?php $caption = wp_get_attachment_caption( get_post_thumbnail_id() ); ?>
						<figure class="zc-single-figure">
							<div class="zc-figure zc-figure-plain aspect-[16/9]">
								<?php
								the_post_thumbnail(
									'zc_wide',
									array(
										'class'         => 'w-full',
										'loading'       => 'eager',
										'fetchpriority' => 'high',
										'decoding'      => 'async',
										'sizes'         => $has_sidebar ? '(min-width: 1280px) 780px, (min-width: 1024px) 62vw, 100vw' : '(min-width: 1024px) 960px, 100vw',
									)
								);
								?>
							</div>
							<?php if ( $caption ) : ?>
								<figcaption><?php echo esc_html( $caption ); ?></figcaption>
							<?php endif; ?>
						</figure>
					<?php endif; ?>

					<div class="zc-article<?php echo $share_side ? ' has-share-rail' : ''; ?>">
						<?php
						if ( $share_side ) {
							zc_share_render( 'sticky' );
						}
						?>
						<div class="zc-article-main min-w-0">
							<div class="zc-prose !max-w-none" data-zc-article>
								<?php
								$content = apply_filters( 'the_content', get_the_content() );
								echo zc_table_of_contents( $content ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- محتوای فیلترشده

								wp_link_pages(
									array(
										'before' => '<nav class="zc-pagination mt-8" aria-label="' . esc_attr__( 'صفحات', 'zarincoach' ) . '">',
										'after'  => '</nav>',
									)
								);
								?>
							</div>

							<?php zc_post_tags(); ?>

							<?php
							if ( $share_box ) {
								zc_share_render( 'box' );
							}
							?>

							<?php if ( zc_switch( 'post_author_box', true ) ) : ?>
								<?php zc_author_box(); ?>
							<?php endif; ?>

							<?php if ( zc_switch( 'post_nav_enable', true ) ) : ?>
								<?php zc_post_nav(); ?>
							<?php endif; ?>

							<?php
							if ( comments_open() || get_comments_number() ) {
								comments_template();
							}
							?>
						</div>
					</div>
				</div>

				<?php if ( $has_sidebar ) : ?>
					<aside class="lg:col-span-4">
						<?php get_sidebar(); ?>
					</aside>
				<?php endif; ?>
			</div>

			<?php if ( zc_switch( 'post_related_enable', true ) ) : ?>
				<?php zc_related_posts( (int) zc_opt( 'post_related_count', 3 ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</article>
	<?php
endwhile;

get_footer();
