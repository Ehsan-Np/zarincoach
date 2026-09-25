<?php
/**
 * بایگانی خدمات (/services/)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();

$zc_title = post_type_archive_title( '', false );
$zc_desc  = (string) zc_opt( 'home_services_subtitle', '' );
?>

<section class="<?php echo esc_attr( zc_page_header_class() ); ?>">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
	<div class="pointer-events-none absolute -top-24 end-1/3 -z-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>

	<div class="zc-container relative py-10 lg:py-14">
		<?php if ( zc_switch( 'seo_breadcrumbs', true ) ) : ?>
			<div class="mb-4"><?php zc_breadcrumbs(); ?></div>
		<?php endif; ?>

		<h1 class="zc-title-lg zc-reveal"><?php echo esc_html( $zc_title ); ?></h1>

		<?php if ( '' !== $zc_desc ) : ?>
			<p class="zc-lead mt-4 max-w-2xl zc-reveal" data-zc-delay="80"><?php echo esc_html( $zc_desc ); ?></p>
		<?php endif; ?>
	</div>
</section>

<?php
if ( have_posts() ) {
	get_template_part(
		'template-parts/front-page/services',
		null,
		array(
			'force'   => true,
			'count'   => -1,
			'heading' => false,
		)
	);
	get_template_part( 'template-parts/front-page/process' );
	echo zc_section_shortcode( array( 'name' => 'cta' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی قالب‌های داخلی.
} else {
	echo '<section class="zc-section"><div class="zc-container">';
	get_template_part( 'template-parts/content', 'none' );
	echo '</div></section>';
}

get_footer();
