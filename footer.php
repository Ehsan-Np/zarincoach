<?php
/**
 * پاورقی سایت + نوار رزرو موبایل + دکمه شناور ارتباط
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$zc_is_lib    = is_singular( 'elementor_library' );
$zc_cta_on    = zc_switch( 'header_mobile_cta_enable', true ) && ! $zc_is_lib;
$zc_cta_text  = (string) zc_opt( 'header_mobile_cta_text', 'رزرو جلسه ارزیابی اولیه' );
$zc_cta_url   = zc_url( (string) zc_opt( 'header_mobile_cta_url', '/booking/' ) );
$zc_cta_side  = (string) zc_opt( 'header_mobile_cta_icon', 'phone' );
$zc_side_link = null;

if ( $zc_cta_on && 'none' !== $zc_cta_side ) {
	if ( 'social' === $zc_cta_side ) {
		$zc_socials = zc_socials();
		if ( ! empty( $zc_socials[0] ) ) {
			$zc_side_link = array(
				'url'      => $zc_socials[0]['url'],
				'label'    => $zc_socials[0]['label'],
				'icon'     => $zc_socials[0]['icon'],
				'external' => true,
			);
		}
	} else {
		$zc_ch = zc_contact_channels( array( $zc_cta_side ) );
		if ( ! empty( $zc_ch[ $zc_cta_side ] ) ) {
			$zc_side_link = $zc_ch[ $zc_cta_side ];
		}
	}
}

$zc_float_on = zc_switch( 'float_enable', true ) && ! $zc_is_lib;
$zc_channels = array();
if ( $zc_float_on ) {
	$zc_selected = array_keys( array_filter( (array) zc_opt( 'float_channels', array() ) ) );
	$zc_channels = zc_contact_channels( $zc_selected );
	$zc_float_on = ! empty( $zc_channels );
}
?>

	</main><!-- /#zc-main -->

	<?php if ( $zc_cta_on ) : ?>
		<div class="zc-mobile-cta">
			<div class="flex items-center gap-3">
				<?php if ( $zc_side_link ) : ?>
					<a class="zc-btn-icon shrink-0" href="<?php echo esc_url( $zc_side_link['url'] ); ?>"<?php echo $zc_side_link['external'] ? ' target="_blank" rel="noopener"' : ''; ?> aria-label="<?php echo esc_attr( $zc_side_link['label'] ); ?>">
						<?php zc_icon( $zc_side_link['icon'], 'h-[18px] w-[18px]' ); ?>
					</a>
				<?php endif; ?>
				<a class="zc-btn zc-btn-primary zc-btn-block" href="<?php echo esc_url( $zc_cta_url ); ?>">
					<?php echo esc_html( $zc_cta_text ); ?>
				</a>
			</div>
		</div>
	<?php endif; ?>

	<?php zc_render_site_footer(); ?>

	<?php if ( $zc_float_on ) : ?>
		<details class="zc-float<?php echo zc_switch( 'float_pulse', true ) ? ' is-pulse' : ''; ?><?php echo zc_switch( 'float_mobile', true ) ? '' : ' is-desktop-only'; ?><?php echo $zc_cta_on ? ' has-mobile-cta' : ''; ?><?php echo 'right' === (string) zc_opt( 'float_position', 'left' ) ? ' is-right' : ''; ?>" data-zc-float>
			<summary class="zc-float-btn" aria-label="<?php echo esc_attr( (string) zc_opt( 'float_title', 'پشتیبانی و رزرو نوبت' ) ); ?>">
				<span class="zc-float-open"><?php zc_icon( 'message', 'h-6 w-6' ); ?></span>
				<span class="zc-float-close"><?php zc_icon( 'close', 'h-6 w-6' ); ?></span>
			</summary>
			<div class="zc-float-panel" role="dialog" aria-label="<?php echo esc_attr( (string) zc_opt( 'float_title', 'پشتیبانی و رزرو نوبت' ) ); ?>">
				<div class="zc-float-head">
					<strong><?php echo esc_html( (string) zc_opt( 'float_title', 'پشتیبانی و رزرو نوبت' ) ); ?></strong>
					<?php $zc_float_text = (string) zc_opt( 'float_text', '' ); ?>
					<?php if ( '' !== $zc_float_text ) : ?>
						<p><?php echo esc_html( $zc_float_text ); ?></p>
					<?php endif; ?>
				</div>
				<ul class="zc-float-list">
					<?php foreach ( $zc_channels as $zc_id => $zc_item ) : ?>
						<li>
							<a class="zc-float-item is-<?php echo esc_attr( $zc_id ); ?>" href="<?php echo esc_url( $zc_item['url'] ); ?>"<?php echo $zc_item['external'] ? ' target="_blank" rel="noopener"' : ''; ?>>
								<span class="zc-float-ico"><?php zc_icon( $zc_item['icon'], 'h-5 w-5' ); ?></span>
								<span class="min-w-0 flex-1">
									<span class="block font-bold"><?php echo esc_html( $zc_item['label'] ); ?></span>
									<?php if ( '' !== $zc_item['sub'] ) : ?>
										<span class="zc-float-sub" dir="ltr"><?php echo esc_html( $zc_item['sub'] ); ?></span>
									<?php endif; ?>
								</span>
								<?php zc_icon( 'arrow-left', 'h-4 w-4 opacity-50' ); ?>
							</a>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		</details>
	<?php endif; ?>

	<?php if ( zc_switch( 'general_back_to_top', true ) ) : ?>
		<button type="button" data-zc-to-top class="zc-to-top<?php echo ( $zc_float_on && 'left' === (string) zc_opt( 'float_position', 'left' ) ) ? ' is-shifted' : ''; ?>" aria-label="<?php esc_attr_e( 'بازگشت به بالا', 'zarincoach' ); ?>">
			<?php zc_icon( 'arrow-up', 'h-5 w-5' ); ?>
		</button>
	<?php endif; ?>

	<?php wp_footer(); ?>
</body>
</html>
