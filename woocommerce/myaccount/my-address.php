<?php
/**
 * My Addresses — کارت‌های نشانی صورتحساب و ارسال
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}
?>

<p class="zc-ma-intro">
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>

<div class="u-columns woocommerce-Addresses col2-set addresses zc-ma-addresses">
	<?php foreach ( $get_addresses as $name => $address_title ) : ?>
		<?php
		$address = wc_get_account_formatted_address( $name );
		$zc_icon = 'shipping' === $name ? 'truck' : ( 'billing' === $name ? 'receipt' : 'map-pin' );
		?>
		<div class="woocommerce-Address zc-ma-address<?php echo $address ? '' : ' is-empty'; ?>">
			<header class="woocommerce-Address-title title">
				<span class="zc-ma-address__ico"><?php zc_icon( $zc_icon, 'h-5 w-5' ); ?></span>
				<h2><?php echo esc_html( $address_title ); ?></h2>
			</header>
			<address>
				<?php
					echo $address ? wp_kses_post( $address ) : esc_html__( 'You have not set up this type of address yet.', 'woocommerce' );

					/**
					 * Used to output content after core address fields.
					 *
					 * @param string $name Address type.
					 * @since 8.7.0
					 */
					do_action( 'woocommerce_my_account_after_my_address', $name );
				?>
			</address>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit zc-ma-address__edit">
				<?php zc_icon( $address ? 'sliders' : 'plus', 'h-4 w-4' ); ?>
				<?php
					printf(
						/* translators: %s: Address title */
						$address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
						esc_html( $address_title )
					);
				?>
			</a>
		</div>
	<?php endforeach; ?>
</div>
