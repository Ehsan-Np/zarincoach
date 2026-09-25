<?php
/**
 * محتوای صفحه‌ی محصول (بازنویسی قالب ووکامرس)
 *
 * چیدمان دو ستونه: گالری چسبان | خلاصه‌ی خرید. پس از آن بخش‌های محتوا (به‌جای زبانه‌ها)، پیشنهادی‌ها و مرتبط‌ها.
 * هوک‌های استاندارد ووکامرس همگی حفظ شده‌اند؛ ترتیب و افزوده‌ها در inc/woocommerce/single.php است.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

/**
 * Hook: woocommerce_before_single_product.
 *
 * @hooked woocommerce_output_all_notices - 10
 */
do_action( 'woocommerce_before_single_product' );

if ( post_password_required() ) {
	echo get_the_password_form(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	return;
}
?>
<div id="product-<?php the_ID(); ?>" <?php wc_product_class( 'zc-sp is-' . zc_wc_kind( $product ), $product ); ?>>

	<div class="zc-sp__crumbs"><?php zc_breadcrumbs(); ?></div>

	<div class="zc-sp__top">
		<div class="zc-sp__gallery<?php echo zc_switch( 'sp_sticky_gallery', true ) ? ' is-sticky' : ''; ?>">
			<?php
			/**
			 * Hook: woocommerce_before_single_product_summary.
			 *
			 * @hooked zc_wc_single_badges - 5
			 * @hooked woocommerce_show_product_images - 20
			 */
			do_action( 'woocommerce_before_single_product_summary' );
			?>
		</div>

		<div class="summary entry-summary zc-sp__summary">
			<?php
			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked zc_wc_single_eyebrow - 4
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked zc_wc_single_subtitle - 6
			 * @hooked woocommerce_template_single_rating - 8
			 * @hooked zc_wc_single_price_box - 10
			 * @hooked zc_wc_single_countdown - 12
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked zc_wc_single_features - 22
			 * @hooked zc_wc_single_stock_bar - 25
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @hooked zc_wc_single_delivery - 35
			 * @hooked zc_wc_single_trust - 38
			 * @hooked zc_wc_single_share - 50
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 */
			do_action( 'woocommerce_single_product_summary' );
			?>
		</div>
	</div>

	<?php
	/**
	 * Hook: woocommerce_after_single_product_summary.
	 *
	 * @hooked zc_wc_single_sections - 10
	 * @hooked woocommerce_upsell_display - 15
	 * @hooked woocommerce_output_related_products - 20
	 */
	do_action( 'woocommerce_after_single_product_summary' );
	?>
</div>

<?php do_action( 'woocommerce_after_single_product' ); ?>
