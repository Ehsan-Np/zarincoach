<?php
/**
 * پاورقی سایت
 *
 * مشترک بین footer.php (حالت پیش‌فرض) و ویجت المنتور «پاورقی سایت».
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$a       = wp_parse_args( isset( $args ) && is_array( $args ) ? $args : array(), zc_footer_args() );
$contact = zc_contact_fields();
$socials = zc_socials();
?>
<footer class="<?php echo esc_attr( trim( 'zc-footer relative overflow-hidden border-t border-line bg-secondary text-white ' . ( $a['margin'] ? 'mt-10 lg:mt-14' : '' ) ) ); ?>">
	<?php if ( $a['glow'] ) : ?>
		<div class="pointer-events-none absolute -top-24 end-0 h-72 w-72 rounded-full bg-primary/20 blur-3xl"></div>
		<div class="pointer-events-none absolute -bottom-32 start-10 h-72 w-72 rounded-full bg-info/20 blur-3xl"></div>
	<?php endif; ?>

	<div class="zc-container relative py-10 lg:py-14">
		<div class="grid gap-8 lg:grid-cols-12 lg:gap-10">

			<!-- ستون معرفی -->
			<div class="lg:col-span-4">
				<div class="mb-5">
					<?php zc_site_branding( true ); ?>
				</div>

				<p class="max-w-sm text-[0.9rem] leading-[2] text-white/70">
					<?php echo esc_html( $a['about'] ); ?>
				</p>

				<?php if ( $a['show_socials'] && ! empty( $socials ) ) : ?>
					<div class="mt-6 flex flex-wrap items-center gap-2">
						<?php foreach ( $socials as $item ) : ?>
							<a class="zc-social border-white/15 bg-white/5 text-white hover:border-primary hover:bg-primary hover:text-secondary" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
								<?php zc_icon( $item['icon'], 'h-[18px] w-[18px]' ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>

			<!-- ستون‌های میانی -->
			<div class="grid gap-8 sm:grid-cols-2 lg:col-span-5">
				<?php
				$zc_has_footer_widgets = false;
				if ( $a['use_widgets'] ) {
					$zc_columns = max( 0, min( 3, (int) zc_opt( 'footer_columns', 3 ) ) );
					for ( $i = 1; $i <= $zc_columns; $i++ ) {
						if ( is_active_sidebar( 'zc-footer-' . $i ) ) {
							$zc_has_footer_widgets = true;
							echo '<div class="zc-footer-widgets">';
							dynamic_sidebar( 'zc-footer-' . $i );
							echo '</div>';
						}
					}
				}

				// ستون‌های هماهنگ با طراحی (خدمات و تازه‌ترین نوشته‌ها).
				if ( ! $zc_has_footer_widgets ) {
					zc_footer_default_columns( $a );
				}
				?>
			</div>

			<!-- ستون تماس -->
			<div class="lg:col-span-3">
				<?php if ( '' !== $a['contact_title'] ) : ?>
					<h4 class="mb-4 text-[0.95rem] font-bold text-white"><?php echo esc_html( $a['contact_title'] ); ?></h4>
				<?php endif; ?>

				<ul class="grid gap-3 text-[0.88rem] text-white/70">
					<?php if ( '' !== $contact['phone'] ) : ?>
						<li class="flex items-start gap-3">
							<span class="mt-0.5 text-accent"><?php zc_icon( 'phone', 'h-4 w-4' ); ?></span>
							<a class="transition hover:text-accent" href="tel:<?php echo esc_attr( zc_normalize_phone( $contact['phone'] ) ); ?>"><span class="block text-[0.72rem] text-white/50"><?php echo esc_html( $contact['phone_label'] ); ?></span><span dir="ltr"><?php echo esc_html( zc_digits_to_persian( $contact['phone'] ) ); ?></span></a>
						</li>
					<?php endif; ?>

					<?php if ( '' !== $contact['phone2'] ) : ?>
						<li class="flex items-start gap-3">
							<span class="mt-0.5 text-accent"><?php zc_icon( 'phone', 'h-4 w-4' ); ?></span>
							<a class="transition hover:text-accent" href="tel:<?php echo esc_attr( zc_normalize_phone( $contact['phone2'] ) ); ?>"><span class="block text-[0.72rem] text-white/50"><?php echo esc_html( $contact['phone2_label'] ); ?></span><span dir="ltr"><?php echo esc_html( zc_digits_to_persian( $contact['phone2'] ) ); ?></span></a>
						</li>
					<?php endif; ?>

					<?php if ( '' !== $contact['email'] ) : ?>
						<li class="flex items-start gap-3">
							<span class="mt-0.5 text-accent"><?php zc_icon( 'mail', 'h-4 w-4' ); ?></span>
							<a class="transition hover:text-accent" href="mailto:<?php echo esc_attr( $contact['email'] ); ?>" dir="ltr"><?php echo esc_html( $contact['email'] ); ?></a>
						</li>
					<?php endif; ?>

					<?php if ( '' !== $contact['address'] ) : ?>
						<li class="flex items-start gap-3">
							<span class="mt-0.5 text-accent"><?php zc_icon( 'map-pin', 'h-4 w-4' ); ?></span>
							<span><?php echo esc_html( $contact['address'] ); ?></span>
						</li>
					<?php endif; ?>

					<?php if ( '' !== $contact['hours'] ) : ?>
						<li class="flex items-start gap-3">
							<span class="mt-0.5 text-accent"><?php zc_icon( 'clock', 'h-4 w-4' ); ?></span>
							<span><?php echo esc_html( $contact['hours'] ); ?></span>
						</li>
					<?php endif; ?>
				</ul>

				<?php if ( $a['menu'] > 0 || has_nav_menu( 'zc-footer' ) ) : ?>
					<nav class="mt-6 border-t border-white/10 pt-5" aria-label="<?php esc_attr_e( 'منوی پاورقی', 'zarincoach' ); ?>">
						<?php
						wp_nav_menu(
							zc_header_menu_args(
								$a['menu'],
								'zc-footer',
								array(
									'container'   => false,
									'menu_class'  => 'flex flex-wrap gap-x-5 gap-y-2 text-[0.85rem] text-white/70',
									'depth'       => 1,
									'fallback_cb' => false,
									'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
								)
							)
						);
						?>
					</nav>
				<?php endif; ?>
			</div>
		</div>

		<?php
		$zc_domain      = zc_legal_info( 'domain' );
		$zc_domain_note = zc_legal_info( 'domain_note' );
		$zc_emergency   = zc_legal_info( 'emergency' );
		?>
		<?php if ( $a['show_trust'] ) : ?>
			<?php
			$zc_badges = zc_trust_badges_render(
				(array) $a['trust_items'],
				array(
					'style'    => 'footer',
					'columns'  => 6,
					'align'    => 'end',
					'captions' => true,
					'fallback' => ! empty( $a['trust_fallback'] ),
				)
			);
			?>
			<div class="zc-footer-trust mt-8 lg:mt-10 grid gap-6 rounded-3xl border border-white/10 bg-white/[0.04] p-5 sm:p-6 lg:grid-cols-12 lg:items-center lg:gap-8">
				<div class="grid gap-3 <?php echo '' !== $zc_badges ? 'lg:col-span-6' : 'lg:col-span-12'; ?>">
					<p class="m-0 flex items-start gap-3 text-[0.88rem] leading-[2] text-white/80">
						<span class="mt-1 shrink-0 text-accent"><?php zc_icon( 'globe', 'h-5 w-5' ); ?></span>
						<span>
							<strong class="text-white"><?php esc_html_e( 'دامنه رسمی:', 'zarincoach' ); ?></strong>
							<a class="font-bold text-accent hover:underline" href="<?php echo esc_url( zc_legal_info( 'domain_url' ) ); ?>" dir="ltr"><?php echo esc_html( $zc_domain ); ?></a>
							<?php if ( '' !== $zc_domain_note ) : ?>
								— <?php echo esc_html( $zc_domain_note ); ?>
							<?php endif; ?>
						</span>
					</p>
					<?php if ( $a['show_emergency'] && '' !== $zc_emergency ) : ?>
						<p class="m-0 flex items-start gap-3 text-[0.82rem] leading-[2] text-white/60">
							<span class="mt-1 shrink-0 text-amber-300"><?php zc_icon( 'alert', 'h-[18px] w-[18px]' ); ?></span>
							<span><?php echo esc_html( $zc_emergency ); ?></span>
						</p>
					<?php endif; ?>
					<?php if ( ! empty( $a['show_license'] ) ) : ?>
						<div class="mt-1 flex flex-wrap gap-2.5">
							<span class="zc-footer-license">
								<span class="zc-footer-license-icon"><?php zc_icon( 'award', 'h-4 w-4' ); ?></span>
								<span><?php echo esc_html( zc_legal_info( 'license_label' ) ); ?>: <strong><?php echo esc_html( zc_legal_info( 'license' ) ); ?></strong></span>
							</span>
							<?php if ( '' !== zc_legal_info( 'pco_code' ) ) : ?>
								<span class="zc-footer-license">
									<span class="zc-footer-license-icon"><?php zc_icon( 'id-card', 'h-4 w-4' ); ?></span>
									<span><?php echo esc_html( zc_legal_info( 'pco_label' ) ); ?>: <strong><?php echo esc_html( zc_legal_info( 'pco_code' ) ); ?></strong></span>
								</span>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( '' !== $zc_badges ) : ?>
					<div class="lg:col-span-6">
						<?php echo $zc_badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی پاک‌سازی‌شده‌ی رندرکننده‌ی نمادها ?>
					</div>
				<?php elseif ( current_user_can( 'manage_options' ) ) : ?>
					<p class="m-0 text-[0.78rem] text-white/50 lg:col-span-12"><?php esc_html_e( '(فقط برای مدیر) کد نمادهای اعتماد را در «تنظیمات قالب ← اطلاعات حقوقی» یا تنظیمات ویجت پاورقی وارد کنید.', 'zarincoach' ); ?></p>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $a['legal_menu'] > 0 || has_nav_menu( 'zc-legal' ) ) : ?>
			<nav class="zc-footer-legal mt-8" aria-label="<?php esc_attr_e( 'قوانین و مقررات', 'zarincoach' ); ?>">
				<?php
				wp_nav_menu(
					zc_header_menu_args(
						$a['legal_menu'],
						'zc-legal',
						array(
							'container'   => false,
							'menu_class'  => 'flex flex-wrap items-center justify-center gap-x-6 gap-y-2 text-[0.8rem] text-white/60 lg:justify-start',
							'depth'       => 1,
							'fallback_cb' => false,
							'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					)
				);
				?>
			</nav>
		<?php endif; ?>

		<div class="zc-rule my-8 opacity-20"></div>

		<div class="flex flex-col items-center justify-between gap-4 text-[0.8rem] text-white/60 sm:flex-row">
			<p class="m-0">
				&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?>
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?> —
				<?php echo esc_html( $a['copyright'] ); ?>
			</p>

			<?php if ( $a['credit'] ) : ?>
				<p class="m-0 inline-flex items-center gap-1.5">
					<?php esc_html_e( 'طراحی و توسعه:', 'zarincoach' ); ?>
					<a class="font-bold text-white/90 transition hover:text-accent" href="https://zarincode.com" target="_blank" rel="noopener">
						<?php esc_html_e( 'زرین‌کد', 'zarincoach' ); ?>
					</a>
				</p>
			<?php endif; ?>
		</div>
	</div>
</footer>
