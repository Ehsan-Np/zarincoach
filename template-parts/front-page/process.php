<?php
/**
 * بخش مسیر همراهی (Timeline)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_process_enable', true ) ) {
	return;
}

$steps = (array) zc_opt( 'home_process_steps', array() );

if ( empty( $steps ) ) {
	return;
}

$delay = 0;
?>

<section id="process" class="zc-section relative overflow-hidden bg-surface2/50">
	<div class="pointer-events-none absolute top-1/3 start-0 -z-10 h-80 w-80 rounded-full bg-info/20 blur-3xl"></div>

	<div class="zc-container relative">
		<?php
		zc_section_heading(
			array(
				'eyebrow' => (string) zc_opt( 'home_process_eyebrow', '' ),
				'title'   => (string) zc_opt( 'home_process_title', '' ),
			)
		);
		?>

		<ol class="zc-timeline mx-auto mt-14 grid max-w-3xl gap-8">
			<?php
			$index = 0;
			foreach ( $steps as $step ) :
				$title       = isset( $step['title'] ) ? trim( (string) $step['title'] ) : '';
				$description = isset( $step['description'] ) ? trim( (string) $step['description'] ) : '';
				$url         = isset( $step['url'] ) ? trim( (string) $step['url'] ) : '';

				if ( '' === $title && '' === $description ) {
					continue;
				}
				$index++;
				?>
				<li class="zc-reveal relative grid grid-cols-[auto_1fr] gap-5 md:grid-cols-[auto_1fr] md:gap-8" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
					<span class="zc-tl-dot">
						<span class="zc-arabic-num"><?php echo esc_html( zc_format_number( $index ) ); ?></span>
					</span>

					<div class="pb-2">
						<?php if ( '' !== $url ) : ?>
							<h3 class="text-[1.2rem] font-bold text-secondary">
								<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
							</h3>
						<?php else : ?>
							<h3 class="text-[1.2rem] font-bold text-secondary"><?php echo esc_html( $title ); ?></h3>
						<?php endif; ?>

						<?php if ( '' !== $description ) : ?>
							<p class="zc-lead mt-2 !text-[0.95rem]"><?php echo esc_html( $description ); ?></p>
						<?php endif; ?>
					</div>
				</li>
				<?php
				$delay += 90;
			endforeach;
			?>
		</ol>
	</div>
</section>
