<?php
/**
 * بخش آمار و ارقام
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_stats_enable', true ) ) {
	return;
}

$items = (array) zc_opt( 'home_stats_items', array() );

if ( empty( $items ) ) {
	return;
}

$persian = zc_switch( 'typo_persian_digits', true ) ? '1' : '0';
?>

<section class="zc-section-tight relative overflow-hidden">
	<div class="zc-container">
		<div class="zc-panel-dark relative overflow-hidden">
			<div class="pointer-events-none absolute -top-24 start-1/4 h-72 w-72 rounded-full bg-primary/25 blur-3xl"></div>
			<div class="pointer-events-none absolute -bottom-24 end-1/4 h-72 w-72 rounded-full bg-info/20 blur-3xl"></div>

			<div class="relative">
				<?php if ( '' !== (string) zc_opt( 'home_stats_title', '' ) ) : ?>
					<h2 class="max-w-3xl text-[1.5rem] font-bold leading-snug text-white sm:text-[1.9rem]">
						<?php echo esc_html( (string) zc_opt( 'home_stats_title', '' ) ); ?>
					</h2>
				<?php endif; ?>

				<div class="my-9 h-px w-full bg-white/15"></div>

				<dl class="grid grid-cols-2 gap-8 sm:gap-10 lg:grid-cols-4">
					<?php
					$delay = 0;
					foreach ( $items as $item ) :
						$num   = isset( $item['title'] ) ? (string) $item['title'] : '';
						$label = isset( $item['description'] ) ? (string) $item['description'] : '';

						if ( '' === $num ) {
							continue;
						}

						// استخراج عدد و پسوند (مثل ۹۶۰+ یا ۹۴٪) — اعداد فارسی ابتدا به لاتین تبدیل می‌شوند.
						$latin_num = zc_digits_to_latin( $num );
						preg_match( '/^(\d+)(.*)$/', $latin_num, $matches );
						$value  = isset( $matches[1] ) ? $matches[1] : $num;
						$suffix = isset( $matches[2] ) ? $matches[2] : '';
						?>
						<div class="zc-reveal" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
							<dt class="text-[2.4rem] font-bold leading-none text-accent sm:text-[3rem]">
								<span data-zc-count="<?php echo esc_attr( $value ); ?>" data-zc-suffix="<?php echo esc_attr( $suffix ); ?>" data-zc-persian="<?php echo esc_attr( $persian ); ?>"><?php echo esc_html( ( $persian ? zc_digits_to_persian( $value ) : $value ) . $suffix ); ?></span>
							</dt>
							<dd class="mt-3 text-[0.85rem] leading-relaxed text-white/70"><?php echo esc_html( $label ); ?></dd>
						</div>
						<?php
						$delay += 90;
					endforeach;
					?>
				</dl>
			</div>
		</div>
	</div>
</section>
