<?php
/**
 * بخش خدمات و برنامه‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$zc_args = isset( $args ) && is_array( $args ) ? $args : array();

if ( empty( $zc_args['force'] ) && ! zc_switch( 'home_services_enable', true ) ) {
	return;
}

$query = zc_get_services( isset( $zc_args['count'] ) ? (int) $zc_args['count'] : (int) zc_opt( 'home_services_count', 6 ) );

if ( ! $query->have_posts() ) {
	return;
}

$columns    = (string) zc_opt( 'home_services_columns', '3' );
$grid_class = 'md:grid-cols-2 lg:grid-cols-3';
if ( '2' === $columns ) {
	$grid_class = 'md:grid-cols-2';
} elseif ( '4' === $columns ) {
	$grid_class = 'sm:grid-cols-2 lg:grid-cols-4';
}

$show_price = zc_switch( 'home_services_price', true );
$button_txt = (string) zc_opt( 'home_services_button', '' );
$delay      = 0;
?>

<section id="services" class="zc-section relative bg-surface2/50">
	<div class="zc-container">
		<?php
		if ( ! isset( $zc_args['heading'] ) || $zc_args['heading'] ) {
			zc_section_heading(
				array(
					'eyebrow'  => (string) zc_opt( 'home_services_eyebrow', '' ),
					'title'    => (string) zc_opt( 'home_services_title', '' ),
					'subtitle' => (string) zc_opt( 'home_services_subtitle', '' ),
				)
			);
		}
		?>

		<div class="<?php echo ( ! isset( $zc_args['heading'] ) || $zc_args['heading'] ) ? 'mt-12' : ''; ?> grid gap-6 <?php echo esc_attr( $grid_class ); ?>">
			<?php
			while ( $query->have_posts() ) :
				$query->the_post();

				$service_id = get_the_ID();
				$icon       = (string) get_post_meta( $service_id, '_zc_service_icon', true );
				$price      = (string) get_post_meta( $service_id, '_zc_service_price', true );
				$duration   = (string) get_post_meta( $service_id, '_zc_service_duration', true );
				$badge      = (string) get_post_meta( $service_id, '_zc_service_badge', true );
				$icon       = '' === $icon ? 'sparkles' : $icon;
				?>
				<article <?php post_class( 'zc-card zc-notch zc-card-hover zc-reveal flex flex-col group', $service_id ); ?> data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
					<?php if ( '' !== $badge ) : ?>
						<span class="zc-ribbon zc-notch-none !end-0 !top-0 rounded-ss-none rounded-ee-none"><?php echo esc_html( $badge ); ?></span>
					<?php endif; ?>

					<span class="mb-5 grid h-14 w-14 place-items-center rounded-2xl bg-primary/10 text-primary transition-transform duration-500 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
						<?php zc_icon( $icon, 'h-7 w-7' ); ?>
					</span>

					<h3 class="text-[1.15rem] font-bold leading-snug text-secondary">
						<a class="transition-colors hover:text-primary" href="<?php echo esc_url( get_permalink( $service_id ) ); ?>"><?php echo esc_html( get_the_title( $service_id ) ); ?></a>
					</h3>

					<p class="zc-lead mt-3 !text-[0.92rem]"><?php echo esc_html( zc_excerpt( get_the_excerpt( $service_id ) ?: get_post_field( 'post_content', $service_id ), 22 ) ); ?></p>

					<div class="mt-auto pt-6">
						<div class="zc-rule mb-4"></div>
						<div class="flex items-center justify-between gap-3">
							<?php if ( $show_price && ( '' !== $price || '' !== $duration ) ) : ?>
								<div class="text-[0.82rem] text-muted">
									<?php if ( '' !== $duration ) : ?>
										<span class="zc-arabic-num inline-flex items-center gap-1.5">
											<?php zc_icon( 'clock', 'h-4 w-4 text-primary' ); ?>
											<?php echo esc_html( $duration ); ?>
										</span>
									<?php endif; ?>
									<?php if ( '' !== $price ) : ?>
										<span class="zc-arabic-num ms-3 font-bold text-secondary"><?php echo esc_html( $price ); ?></span>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<a class="inline-flex items-center gap-1.5 text-[0.85rem] font-bold text-primary transition-all hover:gap-2.5" href="<?php echo esc_url( get_permalink( $service_id ) ); ?>">
								<?php echo esc_html( '' !== $button_txt ? $button_txt : __( 'جزئیات', 'zarincoach' ) ); ?>
								<?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?>
							</a>
						</div>
					</div>
				</article>
				<?php
				$delay += 80;
			endwhile;
			wp_reset_postdata();
			?>
		</div>
	</div>
</section>
