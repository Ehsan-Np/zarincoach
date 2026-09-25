<?php
/**
 * کلاس پایه‌ی ویجت‌های فروشگاهی زرین‌کوچ (نیازمند ووکامرس).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Shop_Widget_Base' ) && class_exists( 'ZC_Widget_Base' ) ) :

	/**
	 * پایه‌ی ویجت‌های فروشگاه.
	 */
	abstract class ZC_Shop_Widget_Base extends ZC_Widget_Base {

		/**
		 * استایل فروشگاه.
		 *
		 * @return array
		 */
		public function get_style_depends() {
			return array( 'zc-shop' );
		}

		/**
		 * اسکریپت فروشگاه (شمارش معکوس، کپی کد، افزودن به سبد).
		 *
		 * @return array
		 */
		public function get_script_depends() {
			return array( 'zc-shop' );
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array
		 */
		public function get_keywords() {
			return array( 'فروشگاه', 'محصول', 'ووکامرس', 'shop', 'product', 'woocommerce', 'زرین‌کوچ' );
		}

		/**
		 * گزینه‌های محصولات (برای انتخاب دستی).
		 *
		 * @return array<string, string>
		 */
		protected function product_options() {
			static $cache = null;
			if ( null !== $cache ) {
				return $cache;
			}
			$cache = array();
			$ids   = get_posts(
				array(
					'post_type'      => 'product',
					'post_status'    => 'publish',
					'posts_per_page' => 200,
					'orderby'        => 'title',
					'order'          => 'ASC',
					'fields'         => 'ids',
					'no_found_rows'  => true,
				)
			);
			foreach ( $ids as $id ) {
				$cache[ (string) $id ] = get_the_title( $id );
			}
			return $cache;
		}

		/**
		 * گزینه‌های دسته‌های محصول.
		 *
		 * @return array<string, string>
		 */
		protected function category_options() {
			static $cache = null;
			if ( null !== $cache ) {
				return $cache;
			}
			$cache = array();
			$terms = get_terms(
				array(
					'taxonomy'   => 'product_cat',
					'hide_empty' => false,
				)
			);
			if ( ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$cache[ $term->slug ] = $term->name;
				}
			}
			return $cache;
		}

		/**
		 * پیام راهنما در ویرایشگر وقتی داده‌ای وجود ندارد.
		 *
		 * @param string $text متن.
		 * @return void
		 */
		protected function editor_notice( $text ) {
			if ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) {
				echo '<div class="zc-container"><p class="woocommerce-info">' . esc_html( $text ) . '</p></div>';
			}
		}
	}

endif;
