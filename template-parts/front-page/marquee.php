<?php
/**
 * نوار کلمات کلیدی (Marquee)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$words = (array) zc_opt( 'home_marquee_words', array() );
$words = array_values( array_filter( array_map( 'trim', array_map( 'strval', $words ) ) ) );

if ( empty( $words ) ) {
	return;
}
?>

<section class="relative border-y border-line bg-surface2/60 py-5" aria-hidden="true">
	<div class="zc-marquee">
		<div class="zc-marquee-track">
			<?php foreach ( $words as $word ) : ?>
				<span class="zc-marquee-item"><?php echo esc_html( $word ); ?></span>
			<?php endforeach; ?>
		</div>
		<div class="zc-marquee-track">
			<?php foreach ( $words as $word ) : ?>
				<span class="zc-marquee-item"><?php echo esc_html( $word ); ?></span>
			<?php endforeach; ?>
		</div>
	</div>
</section>
