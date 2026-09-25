<?php
/**
 * بخش پرسش‌های پرتکرار
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_faq_enable', true ) ) {
	return;
}

$query = zc_get_faqs( (int) zc_opt( 'home_faq_count', 6 ) );

if ( ! $query->have_posts() ) {
	return;
}

if ( function_exists( 'zc_schema_add_faq_query' ) ) {
	zc_schema_add_faq_query( $query );
}

$group = 'zc-faq-' . wp_rand( 1000, 9999 );
$delay = 0;
?>

<section id="faq" class="zc-section relative bg-surface2/50">
	<div class="zc-container">
		<div class="grid gap-10 lg:grid-cols-12 lg:gap-14">

			<div class="lg:col-span-4">
				<?php
				zc_section_heading(
					array(
						'eyebrow' => (string) zc_opt( 'home_faq_eyebrow', '' ),
						'title'   => (string) zc_opt( 'home_faq_title', '' ),
						'align'   => 'start',
					)
				);
				?>

				<div class="zc-panel mt-8 zc-reveal" data-zc-delay="120">
					<div class="flex items-start gap-3">
						<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary"><?php zc_icon( 'message', 'h-5 w-5' ); ?></span>
						<div>
							<p class="m-0 font-bold text-secondary"><?php esc_html_e( 'سوال دیگری داری؟', 'zarincoach' ); ?></p>
							<p class="zc-lead m-0 mt-1 !text-[0.9rem]"><?php esc_html_e( 'پیام بده؛ در کمتر از ۲۴ ساعت کاری پاسخ می‌دهم.', 'zarincoach' ); ?></p>
						</div>
					</div>

					<?php
					zc_button(
						array(
							'text'  => __( 'ارسال پیام', 'zarincoach' ),
							'url'   => '#booking',
							'style' => 'secondary',
							'class' => 'mt-5',
						)
					);
					?>
				</div>
			</div>

			<div class="lg:col-span-8">
				<div class="overflow-hidden rounded-[var(--zc-radius)] border border-line bg-surface px-5 sm:px-7">
					<?php
					$index = 0;
					while ( $query->have_posts() ) :
						$query->the_post();
						$index++;
						?>
						<div class="zc-acc zc-reveal <?php echo 1 === $index ? 'is-open' : ''; ?>" data-zc-acc data-zc-acc-group="<?php echo esc_attr( $group ); ?>" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
							<h3>
								<button type="button" class="zc-acc-head" data-zc-acc-head aria-expanded="<?php echo 1 === $index ? 'true' : 'false'; ?>">
									<span><?php echo esc_html( get_the_title() ); ?></span>
									<span class="zc-acc-icon" aria-hidden="true"><?php zc_icon( 'plus', 'h-4 w-4' ); ?></span>
								</button>
							</h3>
							<div class="zc-acc-body">
								<div class="zc-prose !max-w-none">
									<?php
									$answer = get_the_content( null, false, get_the_ID() );
									echo wp_kses_post( wpautop( $answer ) );
									?>
								</div>
							</div>
						</div>
						<?php
						$delay += 60;
					endwhile;
					wp_reset_postdata();
					?>
				</div>
			</div>
		</div>
	</div>
</section>
