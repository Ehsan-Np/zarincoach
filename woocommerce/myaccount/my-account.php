<?php
/**
 * My Account page — چیدمان پیشخوان مشتری زرین‌کوچ
 *
 * ستون کناری (کارت کاربر + منو + پشتیبانی) و ستون محتوا با سرعنوان بخش جاری.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

$zc_endpoint = function_exists( 'zc_acc_endpoint' ) ? zc_acc_endpoint() : 'dashboard';
?>
<div class="zc-ma" data-endpoint="<?php echo esc_attr( $zc_endpoint ); ?>">
	<?php
	/**
	 * My Account navigation.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_navigation' );
	?>

	<div class="woocommerce-MyAccount-content zc-ma__main">
		<?php
		if ( function_exists( 'zc_acc_content_head' ) ) {
			zc_acc_content_head( $zc_endpoint );
		}
		?>
		<div class="zc-ma__body">
			<?php
			/**
			 * My Account content.
			 *
			 * @since 2.6.0
			 */
			do_action( 'woocommerce_account_content' );
			?>
		</div>
	</div>
</div>
