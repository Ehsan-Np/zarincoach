<?php
/**
 * بخش درباره من
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_about_enable', true ) ) {
	return;
}

$image = (array) zc_opt( 'home_about_image', array() );
$image = ! empty( $image['url'] ) ? $image : zc_placeholder( 'portrait' );

$features = (array) zc_opt( 'home_about_features', array() );
$features = array_values( array_filter( array_map( 'trim', array_map( 'strval', $features ) ) ) );

$content = (string) zc_opt( 'home_about_content', '' );
$content = preg_split( '/\n\s*\n/', $content ) ?: array( $content );
?>

<section id="about" class="zc-section relative overflow-hidden">
	<div class="pointer-events-none absolute -top-20 end-1/3 -z-10 h-72 w-72 rounded-full bg-accent/15 blur-3xl"></div>

	<div class="zc-container">
		<div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-14">

			<!-- تصویر -->
			<div class="lg:col-span-5">
				<div class="relative zc-reveal">
					<div class="zc-figure zc-notch-alt aspect-[4/5] shadow-lift">
						<?php echo zc_image( $image, 'zc_portrait', array( 'alt' => (string) zc_opt( 'home_about_title', get_bloginfo( 'name' ) ), 'width' => 800, 'height' => 1000, 'sizes' => '(min-width: 1280px) 520px, (min-width: 1024px) 40vw, 92vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<!-- نشان تجربه -->
					<?php if ( '' !== (string) zc_opt( 'home_about_badge_num', '' ) ) : ?>
						<div class="absolute -bottom-7 end-4 z-10 grid h-28 w-28 place-items-center rounded-full border border-line bg-base text-center shadow-lift">
							<div>
								<span class="block text-[1.9rem] font-bold leading-none text-primary"><?php echo esc_html( (string) zc_opt( 'home_about_badge_num', '' ) ); ?></span>
								<span class="mt-1 block text-[0.68rem] leading-tight text-muted"><?php echo esc_html( (string) zc_opt( 'home_about_badge_label', '' ) ); ?></span>
							</div>
						</div>
					<?php endif; ?>

					<!-- لایه تزیینی -->
					<div class="pointer-events-none absolute -top-6 -start-6 -z-10 h-24 w-24 rounded-2xl border-2 border-primary/30"></div>
				</div>
			</div>

			<!-- متن -->
			<div class="lg:col-span-7">
				<?php
				zc_section_heading(
					array(
						'eyebrow'  => (string) zc_opt( 'home_about_eyebrow', '' ),
						'title'    => (string) zc_opt( 'home_about_title', '' ),
						'align'    => 'start',
						'reveal'   => true,
					)
				);
				?>

				<div class="zc-lead mt-6 grid gap-4 zc-reveal" data-zc-delay="80">
					<?php foreach ( $content as $paragraph ) : ?>
						<?php if ( '' === trim( (string) $paragraph ) ) { continue; } ?>
						<p class="m-0"><?php echo esc_html( trim( (string) $paragraph ) ); ?></p>
					<?php endforeach; ?>
				</div>

				<?php if ( ! empty( $features ) ) : ?>
					<ul class="zc-checklist mt-7 zc-reveal" data-zc-delay="140">
						<?php foreach ( $features as $feature ) : ?>
							<li><?php echo esc_html( $feature ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== (string) zc_opt( 'home_about_button_text', '' ) ) : ?>
					<div class="mt-8 zc-reveal" data-zc-delay="200">
						<?php
						zc_button(
							array(
								'text'  => (string) zc_opt( 'home_about_button_text', '' ),
								'url'   => (string) zc_opt( 'home_about_button_url', '#' ),
								'style' => 'secondary',
							)
						);
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
