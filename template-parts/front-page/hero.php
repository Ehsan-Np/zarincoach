<?php
/**
 * بخش سربرگ اصلی (Hero)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! zc_switch( 'home_hero_enable', true ) ) {
	return;
}

$style    = (string) zc_opt( 'home_hero_style', 'split' );
$title    = (string) zc_opt( 'home_hero_title', '' );
$accent   = (string) zc_opt( 'home_hero_title_accent', '' );
$badge    = (string) zc_opt( 'home_hero_badge', '' );
$subtitle = (string) zc_opt( 'home_hero_subtitle', '' );

$image = (array) zc_opt( 'home_hero_image', array() );
$image = ! empty( $image['url'] ) ? $image : zc_placeholder( 'portrait' );

$stats = array(
	array( 'num' => (string) zc_opt( 'home_hero_stat1_num', '' ), 'label' => (string) zc_opt( 'home_hero_stat1_label', '' ) ),
	array( 'num' => (string) zc_opt( 'home_hero_stat2_num', '' ), 'label' => (string) zc_opt( 'home_hero_stat2_label', '' ) ),
	array( 'num' => (string) zc_opt( 'home_hero_stat3_num', '' ), 'label' => (string) zc_opt( 'home_hero_stat3_label', '' ) ),
);
$stats = array_filter(
	$stats,
	static function ( $item ) {
		return '' !== $item['num'];
	}
);

// رنگی کردن واژه‌ی کلیدی عنوان.
$title_html = esc_html( $title );
if ( '' !== $accent && false !== strpos( $title, $accent ) ) {
	$title_html = str_replace(
		esc_html( $accent ),
		'<span class="zc-gradient-text">' . esc_html( $accent ) . '</span>',
		$title_html
	);
}
?>

<section id="hero" class="relative overflow-hidden pb-14 pt-10 sm:pb-20 sm:pt-14 lg:pb-28 lg:pt-20">
	<!-- پس‌زمینه -->
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-base"></div>
	<div class="pointer-events-none absolute -top-24 start-1/4 -z-10 h-[420px] w-[420px] animate-zc-blob rounded-full bg-primary/20 blur-3xl"></div>
	<div class="pointer-events-none absolute -bottom-32 end-10 -z-10 h-[380px] w-[380px] animate-zc-blob rounded-full bg-info/25 blur-3xl" style="animation-delay:-6s"></div>
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-60"></div>

	<div class="zc-container relative">
		<?php if ( 'centered' === $style ) : ?>

			<div class="mx-auto max-w-3xl text-center">
				<?php if ( '' !== $badge ) : ?>
					<span class="zc-badge zc-reveal"><?php echo esc_html( $badge ); ?></span>
				<?php endif; ?>

				<h1 class="zc-display mt-6 zc-reveal" data-zc-delay="60"><?php echo wp_kses_post( $title_html ); ?></h1>

				<?php if ( '' !== $subtitle ) : ?>
					<p class="zc-lead mx-auto mt-5 max-w-2xl zc-reveal" data-zc-delay="120"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>

				<div class="mt-8 flex flex-wrap items-center justify-center gap-3 zc-reveal" data-zc-delay="180">
					<?php
					zc_button(
						array(
							'text'  => (string) zc_opt( 'home_hero_primary_text', '' ),
							'url'   => (string) zc_opt( 'home_hero_primary_url', '#booking' ),
							'class' => 'zc-btn-lg',
						)
					);
					zc_button(
						array(
							'text'  => (string) zc_opt( 'home_hero_secondary_text', '' ),
							'url'   => (string) zc_opt( 'home_hero_secondary_url', '#services' ),
							'style' => 'outline',
							'class' => 'zc-btn-lg',
							'icon'  => '',
						)
					);
					?>
				</div>
			</div>

		<?php else : ?>

		<div class="grid items-center gap-12 lg:grid-cols-12 lg:gap-8">

			<!-- متن -->
			<div class="lg:col-span-7">
				<?php if ( '' !== $badge ) : ?>
					<span class="zc-badge zc-reveal">
						<span class="relative flex h-2 w-2">
							<span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-primary opacity-70"></span>
							<span class="relative inline-flex h-2 w-2 rounded-full bg-primary"></span>
						</span>
						<?php echo esc_html( $badge ); ?>
					</span>
				<?php endif; ?>

				<h1 class="zc-display mt-6 zc-reveal" data-zc-delay="60"><?php echo wp_kses_post( $title_html ); ?></h1>

				<?php if ( '' !== $subtitle ) : ?>
					<p class="zc-lead mt-5 max-w-xl zc-reveal" data-zc-delay="120"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>

				<div class="mt-8 flex flex-wrap items-center gap-3 zc-reveal" data-zc-delay="180">
					<?php
					zc_button(
						array(
							'text'  => (string) zc_opt( 'home_hero_primary_text', '' ),
							'url'   => (string) zc_opt( 'home_hero_primary_url', '#booking' ),
							'class' => 'zc-btn-lg',
						)
					);
					zc_button(
						array(
							'text'  => (string) zc_opt( 'home_hero_secondary_text', '' ),
							'url'   => (string) zc_opt( 'home_hero_secondary_url', '#services' ),
							'style' => 'outline',
							'class' => 'zc-btn-lg',
							'icon'  => '',
						)
					);
					?>
				</div>

				<?php if ( ! empty( $stats ) ) : ?>
					<div class="zc-rule my-9 max-w-xl"></div>
					<dl class="grid max-w-xl grid-cols-3 gap-4 zc-reveal" data-zc-delay="240">
						<?php foreach ( $stats as $stat ) : ?>
							<div>
								<dt class="zc-stat-num text-secondary"><?php echo esc_html( $stat['num'] ); ?></dt>
								<dd class="mt-1.5 text-[0.8rem] leading-relaxed text-muted"><?php echo esc_html( $stat['label'] ); ?></dd>
							</div>
						<?php endforeach; ?>
					</dl>
				<?php endif; ?>
			</div>

			<!-- تصویر -->
			<div class="relative lg:col-span-5">
				<div class="relative zc-reveal" data-zc-delay="120">
					<div class="zc-figure zc-notch aspect-[4/5] shadow-lift">
						<?php echo zc_image( $image, 'zc_portrait', array( 'alt' => (string) zc_opt( 'seo_person_name', get_bloginfo( 'name' ) ), 'width' => 800, 'height' => 1000, 'sizes' => '(min-width: 1280px) 480px, (min-width: 1024px) 38vw, 92vw', 'priority' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<!-- کارت شناور -->
					<div class="absolute -bottom-6 start-2 z-10 w-[240px] rounded-2xl border border-line bg-surface p-4 shadow-lift sm:start-6">
						<div class="flex items-center gap-3">
							<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
								<?php zc_icon( 'calendar', 'h-5 w-5' ); ?>
							</span>
							<div>
								<p class="m-0 text-[0.82rem] font-bold text-secondary"><?php esc_html_e( 'جلسه آشنایی', 'zarincoach' ); ?></p>
								<p class="zc-arabic-num m-0 text-[0.75rem] text-muted"><?php esc_html_e( '۳۰ دقیقه، بدون هزینه', 'zarincoach' ); ?></p>
							</div>
						</div>
					</div>

					<!-- کارت امتیاز -->
					<div class="absolute -top-5 end-2 z-10 rounded-2xl border border-line bg-surface/95 px-4 py-3 shadow-soft backdrop-blur">
						<div class="flex items-center gap-2">
							<?php zc_stars( 5 ); ?>
							<span class="zc-arabic-num text-[0.78rem] font-bold text-secondary">۵.۰</span>
						</div>
						<p class="zc-arabic-num m-0 mt-1 text-[0.72rem] text-muted"><?php esc_html_e( 'رضایت مراجعان', 'zarincoach' ); ?></p>
					</div>

					<!-- برچسب عمودی -->
					<span class="zc-vlabel absolute bottom-24 end-0 hidden -translate-y-1/2 lg:block">
						<?php echo esc_html( strtoupper( 'Coaching · Schema Therapy' ) ); ?>
					</span>
				</div>
			</div>
		</div>

		<?php endif; ?>
	</div>
</section>
