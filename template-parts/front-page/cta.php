<?php
/**
 * بخش فراخوان اقدام (رزرو جلسه)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_cta_enable', true ) ) {
	return;
}

$contact = zc_contact_fields();
$socials = zc_socials();
?>

<section id="booking" class="zc-section-tight relative overflow-hidden">
	<div class="zc-container">
		<?php $zc_cta_dark = 'inverse' === (string) zc_opt( 'palette_cta_tone', 'inverse' ); ?>
		<div class="<?php echo esc_attr( $zc_cta_dark ? 'zc-tone-inverse' : 'border border-line bg-surface' ); ?> relative overflow-hidden rounded-[var(--zc-radius)] p-8 shadow-lift sm:p-12 lg:p-16">
			<!-- پس‌زمینه -->
			<div class="zc-grain pointer-events-none absolute inset-0 -z-10 <?php echo esc_attr( $zc_cta_dark ? 'bg-zc-grid opacity-70' : 'bg-surface2' ); ?>"></div>
			<div class="pointer-events-none absolute -top-24 start-0 -z-10 h-72 w-72 animate-zc-blob rounded-full bg-primary/25 blur-3xl"></div>
			<div class="pointer-events-none absolute -bottom-24 end-10 -z-10 h-72 w-72 animate-zc-blob rounded-full bg-info/25 blur-3xl" style="animation-delay:-7s"></div>

			<div class="relative grid gap-10 lg:grid-cols-12 lg:gap-14">

				<!-- متن -->
				<div class="lg:col-span-7">
					<span class="zc-badge zc-reveal"><?php esc_html_e( 'قدم اول', 'zarincoach' ); ?></span>

					<h2 class="zc-title-lg mt-5 zc-reveal" data-zc-delay="60">
						<?php echo esc_html( (string) zc_opt( 'home_cta_title', '' ) ); ?>
					</h2>

					<p class="zc-lead mt-4 max-w-xl zc-reveal" data-zc-delay="120">
						<?php echo esc_html( (string) zc_opt( 'home_cta_text', '' ) ); ?>
					</p>

					<div class="mt-8 flex flex-wrap items-center gap-3 zc-reveal" data-zc-delay="180">
						<?php
						zc_button(
							array(
								'text'  => (string) zc_opt( 'home_cta_primary_text', '' ),
								'url'   => (string) zc_opt( 'home_cta_primary_url', '#contact' ),
								'class' => 'zc-btn-lg',
							)
						);

						$secondary_text = (string) zc_opt( 'home_cta_secondary_text', '' );
						if ( '' !== $secondary_text ) {
							$zc_msg = zc_contact_channels( array( 'telegram', 'bale', 'whatsapp', 'phone' ) );
							$zc_msg = reset( $zc_msg );
							if ( $zc_msg ) {
								zc_button(
									array(
										'text'    => $secondary_text,
										'url'     => $zc_msg['url'],
										'style'   => 'outline',
										'icon'    => $zc_msg['icon'],
										'class'   => 'zc-btn-lg',
										'new_tab' => $zc_msg['external'],
									)
								);
							}
						}
						?>
					</div>

					<?php if ( '' !== (string) zc_opt( 'home_cta_note', '' ) ) : ?>
						<p class="zc-arabic-num mt-5 inline-flex items-center gap-2 text-[0.82rem] text-muted">
							<?php zc_icon( 'clock', 'h-4 w-4 text-primary' ); ?>
							<?php echo esc_html( (string) zc_opt( 'home_cta_note', '' ) ); ?>
						</p>
					<?php endif; ?>
				</div>

				<!-- فرم یا اطلاعات تماس -->
				<div class="lg:col-span-5">
					<div class="zc-reveal rounded-[var(--zc-radius)] border border-line bg-base p-6 sm:p-7" data-zc-delay="120">
						<?php
						$shortcode = (string) zc_opt( 'contact_form_shortcode', '' );

						if ( '' !== $shortcode ) {
							echo do_shortcode( $shortcode );
						} elseif ( zc_switch( 'contact_form_enable', true ) ) {
							echo do_shortcode( '[zc_contact]' );
						} else {
							zc_contact_list();
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
