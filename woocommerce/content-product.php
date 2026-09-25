<?php
/**
 * کارت محصول در حلقه‌ی فروشگاه (بازنویسی قالب ووکامرس)
 *
 * طراحی کارت در zc_wc_product_card() (inc/woocommerce/loop.php) است تا با ویجت‌های المنتور یکسان بماند.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

zc_wc_product_card(
	$product,
	array(
		// زیر عنوان H1 بایگانی H2 و در بخش‌های «مرتبط/پیشنهادی» صفحه‌ی محصول H3.
		'title_tag' => is_product() || is_cart() ? 'h3' : 'h2',
	)
);
