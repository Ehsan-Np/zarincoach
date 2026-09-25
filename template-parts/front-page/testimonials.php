<?php
/**
 * بخش تجربه مراجعان
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_testimonials_enable', true ) ) {
	return;
}

$query = zc_get_testimonials( (int) zc_opt( 'home_testimonials_count', 6 ) );

if ( ! $query->have_posts() ) {
	return;
}

$style = (string) zc_opt( 'home_testimonials_style', 'grid' );
$delay = 0;
?>

<section id="testimonials" class="zc-section relative overflow-hidden">
	<div class="pointer-events-none absolute -bottom-20 start-1/2 -z-10 h-80 w-80 -translate-x-1/2 rounded-full bg-accent/15 blur-3xl"></div>

	<div class="zc-container relative">
		<?php
		zc_section_heading(
			array(
				'eyebrow' => (string) zc_opt( 'home_testimonials_eyebrow', '' ),
				'title'   => (string) zc_opt( 'home_testimonials_title', '' ),
			)
		);
		?>

		<?php if ( 'slider' === $style ) : ?>
			<div class="zc-testimonials mt-12 overflow-hidden" data-zc-slider data-zc-autoplay="0">
				<div class="flex transition-transform duration-700 ease-soft" data-zc-slider-track>
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						$item_id = get_the_ID();
						$role    = (string) get_post_meta( $item_id, '_zc_testimonial_role', true );
						$rating  = (int) get_post_meta( $item_id, '_zc_testimonial_rating', true );
						?>
						<div class="w-full shrink-0 px-1">
							<figure class="zc-card zc-notch mx-auto max-w-2xl p-8 sm:p-10">
								<?php zc_stars( $rating > 0 ? $rating : 5 ); ?>
								<blockquote class="zc-quote mt-5">
									<p class="m-0"><?php echo esc_html( zc_excerpt( get_the_content( null, false, $item_id ), 60 ) ); ?></p>
								</blockquote>
								<figcaption class="mt-6 flex items-center gap-3">
									<span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-secondary text-sm font-bold text-white">
										<?php echo esc_html( mb_substr( get_the_title( $item_id ), 0, 1 ) ); ?>
									</span>
									<span>
										<span class="block font-bold text-secondary"><?php echo esc_html( get_the_title( $item_id ) ); ?></span>
										<?php if ( '' !== $role ) : ?>
											<span class="block text-[0.8rem] text-muted"><?php echo esc_html( $role ); ?></span>
										<?php endif; ?>
									</span>
								</figcaption>
							</figure>
						</div>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</div>

				<div class="mt-7 flex items-center justify-center gap-2">
					<?php
					$total = $query->post_count;
					for ( $i = 0; $i < $total; $i++ ) :
						?>
						<button type="button" data-zc-slider-dot class="h-2.5 w-2.5 rounded-full bg-line transition-all <?php echo 0 === $i ? 'is-active !w-7 !bg-primary' : ''; ?>" aria-label="<?php /* translators: %d: شماره‌ی اسلاید */ echo esc_attr( sprintf( __( 'اسلاید %d', 'zarincoach' ), $i + 1 ) ); ?>"></button>
						<?php
					endfor;
					?>
				</div>
			</div>
		<?php else : ?>

			<div class="mt-12 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				while ( $query->have_posts() ) :
					$query->the_post();
					$item_id = get_the_ID();
					$role    = (string) get_post_meta( $item_id, '_zc_testimonial_role', true );
					$rating  = (int) get_post_meta( $item_id, '_zc_testimonial_rating', true );
					?>
					<figure class="zc-card zc-card-hover zc-reveal flex flex-col p-6" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
						<div class="mb-4 flex items-center justify-between">
							<?php zc_stars( $rating > 0 ? $rating : 5 ); ?>
							<span class="text-primary/40"><?php zc_icon( 'quote', 'h-6 w-6' ); ?></span>
						</div>

						<blockquote class="zc-quote flex-1">
							<p class="m-0"><?php echo esc_html( zc_excerpt( get_the_content( null, false, $item_id ), 42 ) ); ?></p>
						</blockquote>

						<figcaption class="mt-6 flex items-center gap-3 border-t border-line pt-5">
							<span class="grid h-11 w-11 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-bold text-primary">
								<?php echo esc_html( mb_substr( get_the_title( $item_id ), 0, 1 ) ); ?>
							</span>
							<span>
								<span class="block text-[0.92rem] font-bold text-secondary"><?php echo esc_html( get_the_title( $item_id ) ); ?></span>
								<?php if ( '' !== $role ) : ?>
									<span class="block text-[0.78rem] text-muted"><?php echo esc_html( $role ); ?></span>
								<?php endif; ?>
							</span>
						</figcaption>
					</figure>
					<?php
					$delay += 80;
				endwhile;
				wp_reset_postdata();
				?>
			</div>

		<?php endif; ?>

		<?php zc_testimonials_source(); ?>
	</div>
</section>
