<?php
/**
 * بایگانی محصولات (فروشگاه، دسته و برچسب) — بازنویسی قالب ووکامرس
 *
 * چیدمان: سربرگ فروشگاه (مسیر راهنما، عنوان، توضیح، زیردسته‌ها) ← محتوای المنتور برگه‌ی فروشگاه (اختیاری)
 * ← ستون فیلتر + نوار ابزار + شبکه‌ی محصولات + صفحه‌بندی.
 * اگر در المنتور پرو برای بایگانی محصولات قالب ساخته شود، المنتور پرو این فایل را جایگزین می‌کند.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || exit;

get_header( 'shop' );

$zc_sidebar = 'none' !== zc_opt( 'shop_sidebar', 'start' );
$zc_side    = 'end' === zc_opt( 'shop_sidebar', 'start' ) ? 'is-end' : 'is-start';

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked zc wrapper - 10
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
?>

<header class="zc-shop-hero">
	<div class="zc-container">
		<?php zc_breadcrumbs(); ?>
		<div class="zc-shop-hero__row">
			<div class="zc-shop-hero__text">
				<?php if ( apply_filters( 'woocommerce_show_page_title', true ) ) : ?>
					<h1 class="zc-shop-hero__title"><?php woocommerce_page_title(); ?></h1>
				<?php endif; ?>
				<?php
				/**
				 * Hook: woocommerce_archive_description.
				 *
				 * @hooked woocommerce_taxonomy_archive_description - 10
				 * @hooked woocommerce_product_archive_description - 10
				 */
				if ( ! is_shop() || '' === trim( (string) zc_opt( 'shop_hero_text', '' ) ) ) {
					do_action( 'woocommerce_archive_description' );
				} else {
					echo '<div class="term-description"><p>' . esc_html( (string) zc_opt( 'shop_hero_text', '' ) ) . '</p></div>';
				}
				?>
			</div>
			<?php if ( zc_switch( 'shop_hero_perks', true ) ) : ?>
				<ul class="zc-shop-hero__perks">
					<li><?php zc_icon( 'zap', 'h-4 w-4' ); ?><?php esc_html_e( 'تحویل فوری محصولات دانلودی', 'zarincoach' ); ?></li>
					<li><?php zc_icon( 'shield-check', 'h-4 w-4' ); ?><?php esc_html_e( 'پرداخت امن', 'zarincoach' ); ?></li>
					<li><?php zc_icon( 'headphones', 'h-4 w-4' ); ?><?php esc_html_e( 'پشتیبانی پس از خرید', 'zarincoach' ); ?></li>
				</ul>
			<?php endif; ?>
		</div>
		<?php
		if ( ! is_shop() || ! zc_switch( 'shop_intro', true ) ) {
			zc_wc_subcategory_pills();
		}
		?>
	</div>
</header>

<?php zc_wc_shop_intro(); ?>

<section class="zc-shop-main" id="zc-products">
	<div class="zc-container">
		<div class="zc-shop-layout <?php echo esc_attr( $zc_sidebar ? 'has-sidebar ' . $zc_side : 'no-sidebar' ); ?>">
			<?php if ( $zc_sidebar ) : ?>
				<aside class="zc-shop-sidebar" id="zc-shop-filters" aria-label="<?php esc_attr_e( 'فیلتر محصولات', 'zarincoach' ); ?>" data-zc-filters>
					<div class="zc-shop-sidebar__head">
						<strong><?php esc_html_e( 'فیلتر محصولات', 'zarincoach' ); ?></strong>
						<button type="button" class="zc-btn-icon" data-zc-filters-close aria-label="<?php esc_attr_e( 'بستن فیلترها', 'zarincoach' ); ?>"><?php zc_icon( 'close', 'h-4 w-4' ); ?></button>
					</div>
					<?php zc_wc_render_filters(); ?>
				</aside>
				<div class="zc-shop-backdrop" data-zc-filters-close hidden></div>
			<?php endif; ?>

			<div class="zc-shop-content">
				<?php
				if ( woocommerce_product_loop() ) {
					zc_wc_toolbar( $zc_sidebar );

					/**
					 * Hook: woocommerce_before_shop_loop.
					 *
					 * @hooked woocommerce_output_all_notices - 10
					 */
					do_action( 'woocommerce_before_shop_loop' );

					woocommerce_product_loop_start();

					if ( wc_get_loop_prop( 'total' ) ) {
						while ( have_posts() ) {
							the_post();

							/**
							 * Hook: woocommerce_shop_loop.
							 */
							do_action( 'woocommerce_shop_loop' );

							wc_get_template_part( 'content', 'product' );
						}
					}

					woocommerce_product_loop_end();

					/**
					 * Hook: woocommerce_after_shop_loop.
					 *
					 * @hooked woocommerce_pagination - 10
					 */
					do_action( 'woocommerce_after_shop_loop' );
				} else {
					if ( zc_wc_active_filters() ) {
						zc_wc_toolbar( $zc_sidebar );
					}
					/**
					 * Hook: woocommerce_no_products_found.
					 *
					 * @hooked wc_no_products_found - 10
					 */
					do_action( 'woocommerce_no_products_found' );
				}
				?>
			</div>
		</div>
	</div>
</section>

<?php
/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked zc wrapper end - 10
 */
do_action( 'woocommerce_after_main_content' );

get_footer( 'shop' );
