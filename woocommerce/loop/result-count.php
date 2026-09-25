<?php
/**
 * شمارش نتایج بایگانی محصولات (فارسی و مستقل از بسته‌ی ترجمه‌ی ووکامرس).
 *
 * @package ZarinCoach
 * @version 10.8.0
 *
 * @var int $total    تعداد کل.
 * @var int $per_page در هر صفحه.
 * @var int $current  صفحه‌ی جاری.
 */

defined( 'ABSPATH' ) || exit;

$total    = isset( $total ) ? (int) $total : 0;
$per_page = isset( $per_page ) ? max( 1, (int) $per_page ) : 1;
$current  = isset( $current ) ? max( 1, (int) $current ) : 1;
?>
<p class="woocommerce-result-count" role="status" aria-live="polite">
	<?php
	if ( 1 === $total ) {
		esc_html_e( 'یک محصول', 'zarincoach' );
	} elseif ( $total <= $per_page || -1 === $per_page ) {
		/* translators: %s: تعداد */
		printf( esc_html__( 'همه‌ی %s محصول', 'zarincoach' ), esc_html( number_format_i18n( $total ) ) );
	} else {
		$first = ( $per_page * $current ) - $per_page + 1;
		$last  = min( $total, $per_page * $current );
		/* translators: 1: از 2: تا 3: کل */
		printf( esc_html__( 'نمایش %1$s تا %2$s از %3$s محصول', 'zarincoach' ), esc_html( number_format_i18n( $first ) ), esc_html( number_format_i18n( $last ) ), esc_html( number_format_i18n( $total ) ) );
	}
	?>
</p>
