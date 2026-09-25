<?php
/**
 * سربرگ برگه‌ها (عنوان، مسیر راهنما و خلاصه)
 *
 * تُن سربرگ (روشن / سرمه‌ای) از پنل تنظیمات «رنگ و هویت» کنترل می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;
?>
<section class="<?php echo esc_attr( zc_page_header_class() ); ?>">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
	<div class="pointer-events-none absolute -top-24 end-1/4 -z-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
	<div class="pointer-events-none absolute -bottom-28 start-10 -z-10 h-64 w-64 rounded-full bg-accent/15 blur-3xl"></div>

	<div class="zc-container relative py-9 lg:py-12">
		<?php if ( zc_switch( 'seo_breadcrumbs', true ) ) : ?>
			<div class="mb-4"><?php zc_breadcrumbs(); ?></div>
		<?php endif; ?>

		<h1 class="zc-title-lg max-w-3xl zc-reveal"><?php echo esc_html( get_the_title() ); ?></h1>

		<?php if ( has_excerpt() ) : ?>
			<p class="zc-lead mt-4 max-w-2xl zc-reveal" data-zc-delay="80"><?php echo esc_html( get_the_excerpt() ); ?></p>
		<?php endif; ?>
	</div>
</section>
