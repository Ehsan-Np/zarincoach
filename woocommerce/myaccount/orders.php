<?php
/**
 * Orders — فهرست کارتی سفارش‌های مشتری
 *
 * ستون‌های سفارشیِ افزوده‌شده با wc_get_account_orders_columns و قلاب‌های
 * woocommerce_my_account_my_orders_column_{id} پشتیبانی می‌شوند.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<?php if ( $has_orders ) : ?>

	<?php
	$zc_columns  = wc_get_account_orders_columns();
	$zc_standard = array( 'order-number', 'order-date', 'order-status', 'order-total', 'order-actions' );
	?>
	<div class="zc-ma-orders woocommerce-orders-table woocommerce-MyAccount-orders" role="list">
		<?php
		foreach ( $customer_orders->orders as $customer_order ) {
			$order      = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			$item_count = $order->get_item_count() - $order->get_item_count_refunded();
			$zc_tone    = zc_acc_status_tone( $order->get_status() );
			?>
			<article class="zc-ma-order woocommerce-orders-table__row woocommerce-orders-table__row--status-<?php echo esc_attr( $order->get_status() ); ?> is-<?php echo esc_attr( $zc_tone ); ?>" role="listitem">
				<header class="zc-ma-order__head">
					<div class="zc-ma-order__id">
						<?php if ( has_action( 'woocommerce_my_account_my_orders_column_order-number' ) ) : ?>
							<?php do_action( 'woocommerce_my_account_my_orders_column_order-number', $order ); ?>
						<?php else : ?>
							<?php /* translators: %s: the order number, usually accompanied by a leading # */ ?>
							<a class="zc-ma-order__num" href="<?php echo esc_url( $order->get_view_order_url() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'View order number %s', 'woocommerce' ), $order->get_order_number() ) ); ?>">
								<?php
								/* translators: %s: شماره سفارش */
								printf( esc_html__( 'سفارش #%s', 'zarincoach' ), esc_html( $order->get_order_number() ) );
								?>
							</a>
						<?php endif; ?>
						<span class="zc-ma-order__date">
							<?php zc_icon( 'calendar', 'h-4 w-4' ); ?>
							<?php if ( has_action( 'woocommerce_my_account_my_orders_column_order-date' ) ) : ?>
								<?php do_action( 'woocommerce_my_account_my_orders_column_order-date', $order ); ?>
							<?php else : ?>
								<time datetime="<?php echo esc_attr( $order->get_date_created()->date( 'c' ) ); ?>"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></time>
							<?php endif; ?>
						</span>
					</div>
					<?php if ( has_action( 'woocommerce_my_account_my_orders_column_order-status' ) ) : ?>
						<?php do_action( 'woocommerce_my_account_my_orders_column_order-status', $order ); ?>
					<?php else : ?>
						<?php echo zc_acc_status_pill( $order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php endif; ?>
				</header>

				<div class="zc-ma-order__body">
					<?php echo zc_acc_order_thumbs( $order, 3 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<div class="zc-ma-order__items">
						<strong><?php echo esc_html( zc_acc_order_summary( $order ) ); ?></strong>
						<small><?php echo esc_html( $order->get_payment_method_title() ); ?></small>
					</div>
					<div class="zc-ma-order__total">
						<?php if ( has_action( 'woocommerce_my_account_my_orders_column_order-total' ) ) : ?>
							<?php do_action( 'woocommerce_my_account_my_orders_column_order-total', $order ); ?>
						<?php else : ?>
							<span class="zc-ma-order__amount"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
							<small>
								<?php
								/* translators: %s: تعداد اقلام */
								printf( esc_html( _n( '%s قلم', '%s قلم', $item_count, 'zarincoach' ) ), esc_html( number_format_i18n( $item_count ) ) );
								?>
							</small>
						<?php endif; ?>
					</div>
				</div>

				<?php foreach ( $zc_columns as $column_id => $column_name ) : ?>
					<?php
					if ( in_array( $column_id, $zc_standard, true ) || ! has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) {
						continue;
					}
					?>
					<div class="zc-ma-order__extra woocommerce-orders-table__cell-<?php echo esc_attr( $column_id ); ?>">
						<span><?php echo esc_html( $column_name ); ?></span>
						<div><?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?></div>
					</div>
				<?php endforeach; ?>

				<?php
				$actions = wc_get_account_orders_actions( $order );
				if ( has_action( 'woocommerce_my_account_my_orders_column_order-actions' ) ) {
					echo '<footer class="zc-ma-order__actions">';
					do_action( 'woocommerce_my_account_my_orders_column_order-actions', $order );
					echo '</footer>';
				} elseif ( ! empty( $actions ) ) {
					echo '<footer class="zc-ma-order__actions">';
					foreach ( $actions as $key => $action ) { // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
						if ( empty( $action['aria-label'] ) ) {
							/* translators: %1$s Action name, %2$s Order number. */
							$action_aria_label = sprintf( __( '%1$s order number %2$s', 'woocommerce' ), $action['name'], $order->get_order_number() );
						} else {
							$action_aria_label = $action['aria-label'];
						}
						$zc_primary = 'view' === $key ? ' is-primary' : ( 'cancel' === $key ? ' is-danger' : '' );
						echo '<a href="' . esc_url( $action['url'] ) . '" class="woocommerce-button' . esc_attr( $wp_button_class ) . ' button zc-ma-action ' . sanitize_html_class( $key ) . esc_attr( $zc_primary ) . '" aria-label="' . esc_attr( $action_aria_label ) . '">' . esc_html( $action['name'] ) . '</a>';
						unset( $action_aria_label );
					}
					echo '</footer>';
				}
				?>
			</article>
			<?php
		}
		?>
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="woocommerce-pagination woocommerce-pagination--without-numbers woocommerce-Pagination zc-ma-pager">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--previous woocommerce-Button woocommerce-Button--previous button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php zc_icon( 'arrow-right', 'h-4 w-4' ); ?><?php esc_html_e( 'Previous', 'woocommerce' ); ?></a>
			<?php endif; ?>

			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="woocommerce-button woocommerce-button--next woocommerce-Button woocommerce-Button--next button<?php echo esc_attr( $wp_button_class ); ?>" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'Next', 'woocommerce' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<?php zc_acc_empty( 'bag', __( 'هنوز سفارشی ثبت نکرده‌اید', 'zarincoach' ), __( 'پس از نخستین خرید، وضعیت و جزئیات سفارش‌ها این‌جا نمایش داده می‌شود.', 'zarincoach' ), __( 'Browse products', 'woocommerce' ), zc_acc_shop_url() ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
