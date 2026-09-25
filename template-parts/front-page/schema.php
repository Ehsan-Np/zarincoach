<?php
/**
 * بخش طرحواره‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_schema_enable', true ) ) {
	return;
}

$items = (array) zc_opt( 'home_schema_items', array() );

if ( empty( $items ) ) {
	return;
}

$delay = 0;
?>

<section id="schema" class="zc-section relative overflow-hidden">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-grid opacity-70"></div>

	<div class="zc-container relative">
		<?php
		zc_section_heading(
			array(
				'eyebrow'  => (string) zc_opt( 'home_schema_eyebrow', '' ),
				'title'    => (string) zc_opt( 'home_schema_title', '' ),
				'subtitle' => (string) zc_opt( 'home_schema_subtitle', '' ),
			)
		);
		?>

		<div class="mt-12 grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
			<?php
			$number = 0;
			foreach ( $items as $item ) :
				$title       = isset( $item['title'] ) ? trim( (string) $item['title'] ) : '';
				$description = isset( $item['description'] ) ? trim( (string) $item['description'] ) : '';

				if ( '' === $title && '' === $description ) {
					continue;
				}
				$number++;
				$num = str_pad( (string) $number, 2, '0', STR_PAD_LEFT );
				?>
				<article class="zc-card zc-card-hover zc-reveal group relative overflow-hidden" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
					<span class="zc-outline-num pointer-events-none absolute top-4 end-5 text-[3.25rem] leading-none opacity-40 transition-opacity duration-500 group-hover:opacity-100">
						<?php echo esc_html( $num ); ?>
					</span>

					<?php if ( '' !== $title ) : ?>
						<h3 class="relative mt-3 text-[1.1rem] font-bold text-secondary"><?php echo esc_html( $title ); ?></h3>
					<?php endif; ?>

					<?php if ( '' !== $description ) : ?>
						<p class="zc-lead relative mt-2 !text-[0.9rem]"><?php echo esc_html( $description ); ?></p>
					<?php endif; ?>

					<span class="mt-5 inline-flex items-center gap-1.5 text-[0.82rem] font-bold text-primary opacity-0 transition-opacity duration-300 group-hover:opacity-100">
						<?php esc_html_e( 'قابل کار در جلسات', 'zarincoach' ); ?>
						<?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?>
					</span>
				</article>
				<?php
				$delay += 70;
			endforeach;
			?>
		</div>

		<div class="zc-panel mt-10 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
			<div class="flex items-start gap-3">
				<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary"><?php zc_icon( 'mirror', 'h-5 w-5' ); ?></span>
				<div>
					<p class="m-0 font-bold text-secondary"><?php esc_html_e( 'نمی‌دانی کدام طرحواره در تو فعال است؟', 'zarincoach' ); ?></p>
					<p class="zc-lead m-0 !text-[0.9rem]"><?php esc_html_e( 'در جلسه آشنایی با یک پرسش‌نامه استاندارد، نقشه‌ی الگوهای تو را ترسیم می‌کنیم.', 'zarincoach' ); ?></p>
				</div>
			</div>

			<?php
			zc_button(
				array(
					'text' => __( 'شروع ارزیابی', 'zarincoach' ),
					'url'  => '#booking',
				)
			);
			?>
		</div>
	</div>
</section>
