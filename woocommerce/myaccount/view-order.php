<?php
/**
 * View Order — جزئیات سفارش در پیشخوان مشتری
 *
 * خلاصه‌ی سفارش، نوار پیشرفت، یادآور پرداخت کارت‌به‌کارت، به‌روزرسانی‌ها و جدول جزئیات ووکامرس.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 10.6.0
 */

defined( 'ABSPATH' ) || exit;

$notes    = $order->get_customer_order_notes();
$zc_steps = zc_acc_order_steps( $order );
$zc_items = $order->get_item_count() - $order->get_item_count_refunded();
?>
<p class="screen-reader-text">
<?php
echo wp_kses_post(
	/**
	 * Filter to modify order detiails status text.
	 *
	 * @param string $order_status The order status text.
	 *
	 * @since 10.1.0
	 */
	apply_filters(
		'woocommerce_order_details_status',
		sprintf(
		/* translators: 1: order number 2: order date 3: order status */
			esc_html__( 'Order #%1$s was placed on %2$s and is currently %3$s.', 'woocommerce' ),
			'<mark class="order-number">' . $order->get_order_number() . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-date">' . wc_format_datetime( $order->get_date_created() ) . '</mark>', // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			'<mark class="order-status">' . wc_get_order_status_name( $order->get_status() ) . '</mark>' // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		),
		$order
	)
);
?>
</p>

<dl class="zc-ma-facts">
	<div><dt><?php esc_html_e( 'شماره‌ی سفارش', 'zarincoach' ); ?></dt><dd><?php echo esc_html( $order->get_order_number() ); ?></dd></div>
	<div><dt><?php esc_html_e( 'تاریخ ثبت', 'zarincoach' ); ?></dt><dd><time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time></dd></div>
	<div><dt><?php esc_html_e( 'مبلغ کل', 'zarincoach' ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
	<div><dt><?php esc_html_e( 'روش پرداخت', 'zarincoach' ); ?></dt><dd><?php echo esc_html( $order->get_payment_method_title() ? $order->get_payment_method_title() : '—' ); ?></dd></div>
	<div><dt><?php esc_html_e( 'تعداد اقلام', 'zarincoach' ); ?></dt><dd><?php echo esc_html( number_format_i18n( $zc_items ) ); ?></dd></div>
</dl>

<?php if ( 'stopped' === $zc_steps['state'] ) : ?>
	<div class="zc-ma-alert is-<?php echo esc_attr( zc_acc_status_tone( $order->get_status() ) ); ?>">
		<?php zc_icon( 'alert', 'h-5 w-5' ); ?>
		<div>
			<strong>
				<?php
				/* translators: %s: وضعیت */
				printf( esc_html__( 'این سفارش «%s» است.', 'zarincoach' ), esc_html( wc_get_order_status_name( $order->get_status() ) ) );
				?>
			</strong>
			<p><?php esc_html_e( 'اگر پرسشی درباره‌ی این سفارش یا بازگشت وجه دارید، با پشتیبانی تماس بگیرید.', 'zarincoach' ); ?></p>
		</div>
	</div>
<?php else : ?>
	<ol class="zc-ma-steps" aria-label="<?php esc_attr_e( 'مراحل سفارش', 'zarincoach' ); ?>">
		<?php foreach ( $zc_steps['steps'] as $zc_i => $zc_step ) : ?>
			<?php
			$zc_state = $zc_i < $zc_steps['current'] || ( 3 === $zc_steps['current'] ) ? 'is-done' : ( $zc_i === $zc_steps['current'] ? 'is-current' : '' );
			?>
			<li class="<?php echo esc_attr( $zc_state ); ?>"<?php echo 'is-current' === $zc_state ? ' aria-current="step"' : ''; ?>>
				<span class="zc-ma-steps__dot"><?php zc_icon( 'is-done' === $zc_state ? 'check' : $zc_step['icon'], 'h-4 w-4' ); ?></span>
				<span class="zc-ma-steps__label"><?php echo esc_html( $zc_step['label'] ); ?></span>
			</li>
		<?php endforeach; ?>
	</ol>
<?php endif; ?>

<?php
// یادآور پرداخت کارت‌به‌کارت (مطابق تعهد برگه‌ی شرایط خرید).
if ( 'bacs' === $order->get_payment_method() && $order->has_status( array( 'on-hold', 'pending' ) ) ) :
	$zc_hours = (int) zc_opt( 'shop_bacs_cancel_hours', 48 );
	?>
	<div class="zc-ma-alert is-warn">
		<?php zc_icon( 'wallet', 'h-5 w-5' ); ?>
		<div>
			<strong><?php esc_html_e( 'در انتظار تأیید پرداخت', 'zarincoach' ); ?></strong>
			<p>
				<?php
				if ( $zc_hours > 0 ) {
					/* translators: %s: ساعت */
					printf( esc_html__( 'پس از واریز، رسید را از راه‌های ارتباطی ارسال کنید. سفارش‌های پرداخت‌نشده پس از %s ساعت به‌طور خودکار لغو می‌شوند.', 'zarincoach' ), esc_html( number_format_i18n( $zc_hours ) ) );
				} else {
					esc_html_e( 'پس از واریز، رسید را از راه‌های ارتباطی ارسال کنید تا سفارش پردازش شود.', 'zarincoach' );
				}
				?>
			</p>
		</div>
	</div>
<?php endif; ?>

<?php if ( $notes ) : ?>
	<section class="zc-ma-card zc-ma-updates">
		<header class="zc-ma-card__head"><h3><?php esc_html_e( 'Order updates', 'woocommerce' ); ?></h3></header>
		<ol class="woocommerce-OrderUpdates commentlist notes">
			<?php foreach ( $notes as $note ) : ?>
			<li class="woocommerce-OrderUpdate comment note">
				<div class="woocommerce-OrderUpdate-inner comment_container">
					<div class="woocommerce-OrderUpdate-text comment-text">
						<?php
						$zc_ts   = strtotime( $note->comment_date_gmt . ' UTC' );
						$zc_date = function_exists( 'zc_jalali_date' ) ? zc_jalali_date( $zc_ts ) : '';
						$zc_date = '' !== $zc_date ? $zc_date . ' — ' . zc_digits_to_persian( wp_date( 'H:i', $zc_ts ) ) : date_i18n( esc_html__( 'l jS \o\f F Y, h:ia', 'woocommerce' ), strtotime( $note->comment_date ) );
						?>
						<p class="woocommerce-OrderUpdate-meta meta"><?php echo esc_html( $zc_date ); ?></p>
						<div class="woocommerce-OrderUpdate-description description">
							<?php echo wp_kses_post( wpautop( wptexturize( $note->comment_content ) ) ); ?>
						</div>
					</div>
				</div>
			</li>
			<?php endforeach; ?>
		</ol>
	</section>
<?php endif; ?>

<?php do_action( 'woocommerce_view_order', $order_id ); ?>
