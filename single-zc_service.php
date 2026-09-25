<?php
/**
 * قالب صفحه‌ی هر خدمت (دارای کارت مشخصات و فرم رزرو)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( zc_page_uses_elementor( get_the_ID() ) && zc_is_elementor_active() ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

get_header();

while ( have_posts() ) :
	the_post();

	$service_id = get_the_ID();
	$icon       = (string) get_post_meta( $service_id, '_zc_service_icon', true );
	$price      = (string) get_post_meta( $service_id, '_zc_service_price', true );
	$duration   = (string) get_post_meta( $service_id, '_zc_service_duration', true );
	$badge      = (string) get_post_meta( $service_id, '_zc_service_badge', true );
	?>

	<section class="<?php echo esc_attr( zc_page_header_class() ); ?>">
		<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
		<div class="pointer-events-none absolute -top-24 end-1/4 -z-10 h-72 w-72 rounded-full bg-primary/20 blur-3xl"></div>

		<div class="zc-container relative py-10 lg:py-14">
			<?php zc_breadcrumbs(); ?>

			<div class="mt-5 flex flex-col items-start gap-5 sm:flex-row sm:items-center">
				<span class="grid h-16 w-16 shrink-0 place-items-center rounded-2xl bg-primary/15 text-primary">
					<?php zc_icon( '' !== $icon ? $icon : 'sparkles', 'h-8 w-8' ); ?>
				</span>
				<div>
					<?php if ( '' !== $badge ) : ?>
						<span class="zc-badge mb-2"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>
					<h1 class="zc-title-lg"><?php echo esc_html( get_the_title() ); ?></h1>
				</div>
			</div>

			<?php if ( has_excerpt() ) : ?>
				<p class="zc-lead mt-5 max-w-2xl"><?php echo esc_html( get_the_excerpt() ); ?></p>
			<?php endif; ?>
		</div>
	</section>

	<section class="zc-section">
		<div class="zc-container">
			<div class="grid gap-8 lg:grid-cols-12 lg:gap-12">

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'lg:col-span-7' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<figure class="zc-figure zc-figure-plain zc-notch mb-8 aspect-[16/9]">
							<?php the_post_thumbnail( 'zc_wide', array( 'loading' => 'eager', 'fetchpriority' => 'high', 'decoding' => 'async', 'sizes' => '(min-width: 1024px) 700px, 100vw' ) ); ?>
						</figure>
					<?php endif; ?>

					<div class="zc-prose !max-w-none">
						<?php the_content(); ?>
					</div>

					<?php
					$faqs = zc_get_faqs( 4 );
					if ( $faqs->have_posts() ) :
						if ( function_exists( 'zc_schema_add_faq_query' ) ) {
							zc_schema_add_faq_query( $faqs );
						}
						?>
						<div class="mt-8 lg:mt-10">
							<h2 class="zc-title !text-[1.4rem]"><?php esc_html_e( 'پرسش‌های پرتکرار', 'zarincoach' ); ?></h2>
							<div class="mt-5 overflow-hidden rounded-[var(--zc-radius)] border border-line bg-surface px-5 sm:px-7">
								<?php
								while ( $faqs->have_posts() ) :
									$faqs->the_post();
									?>
									<div class="zc-acc" data-zc-acc data-zc-acc-group="zc-service-faq">
										<h3>
											<button type="button" class="zc-acc-head" data-zc-acc-head aria-expanded="false">
												<span><?php echo esc_html( get_the_title() ); ?></span>
												<span class="zc-acc-icon" aria-hidden="true"><?php zc_icon( 'plus', 'h-4 w-4' ); ?></span>
											</button>
										</h3>
										<div class="zc-acc-body"><?php echo wp_kses_post( wpautop( get_the_content() ) ); ?></div>
									</div>
									<?php
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						</div>
					<?php endif; ?>
				</article>

				<aside class="lg:col-span-5">
					<div class="lg:sticky lg:top-28">
						<div class="zc-card zc-notch p-6 sm:p-7">
							<h2 class="text-[1.05rem] font-bold text-secondary"><?php esc_html_e( 'مشخصات این مسیر', 'zarincoach' ); ?></h2>

							<ul class="mt-5 grid gap-3">
								<?php if ( '' !== $duration ) : ?>
									<li class="flex items-center justify-between gap-3 rounded-2xl bg-surface2/70 px-4 py-3">
										<span class="inline-flex items-center gap-2 text-[0.88rem] text-muted"><?php zc_icon( 'clock', 'h-4 w-4 text-primary' ); ?><?php esc_html_e( 'مدت هر جلسه', 'zarincoach' ); ?></span>
										<span class="font-bold text-secondary"><?php echo esc_html( $duration ); ?></span>
									</li>
								<?php endif; ?>
								<?php if ( '' !== $price ) : ?>
									<li class="flex items-center justify-between gap-3 rounded-2xl bg-surface2/70 px-4 py-3">
										<span class="inline-flex items-center gap-2 text-[0.88rem] text-muted"><?php zc_icon( 'award', 'h-4 w-4 text-primary' ); ?><?php esc_html_e( 'هزینه', 'zarincoach' ); ?></span>
										<span class="font-bold text-secondary"><?php echo esc_html( $price ); ?></span>
									</li>
								<?php endif; ?>
								<li class="flex items-center justify-between gap-3 rounded-2xl bg-surface2/70 px-4 py-3">
									<span class="inline-flex items-center gap-2 text-[0.88rem] text-muted"><?php zc_icon( 'video', 'h-4 w-4 text-primary' ); ?><?php esc_html_e( 'نحوه برگزاری', 'zarincoach' ); ?></span>
									<span class="font-bold text-secondary"><?php esc_html_e( 'حضوری / آنلاین', 'zarincoach' ); ?></span>
								</li>
							</ul>

							<div class="mt-6">
								<?php
								$shortcode = (string) zc_opt( 'contact_form_shortcode', '' );
								if ( '' !== $shortcode ) {
									echo do_shortcode( $shortcode );
								} elseif ( zc_switch( 'contact_form_enable', true ) ) {
									echo do_shortcode( '[zc_contact title="' . esc_attr__( 'رزرو همین مسیر', 'zarincoach' ) . '" subtitle=""]' );
								} else {
									zc_contact_list();
								}
								?>
							</div>
						</div>
					</div>
				</aside>
			</div>
		</div>
	</section>

	<?php
endwhile;

get_footer();
