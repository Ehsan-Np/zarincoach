<?php
/**
 * ووکامرس — زبانه‌ی «زرین‌کوچ» در داده‌های محصول
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

add_filter(
	'woocommerce_product_data_tabs',
	static function ( $tabs ) {
		$tabs['zarincoach'] = array(
			'label'    => __( 'زرین‌کوچ', 'zarincoach' ),
			'target'   => 'zc_product_data',
			'class'    => array(),
			'priority' => 85,
		);
		return $tabs;
	}
);

add_action(
	'woocommerce_product_data_panels',
	static function () {
		global $product_object;
		$id = ( $product_object instanceof WC_Product ) ? $product_object->get_id() : get_the_ID();
		echo '<div id="zc_product_data" class="panel woocommerce_options_panel hidden">';

		echo '<div class="options_group">';
		woocommerce_wp_select(
			array(
				'id'          => '_zc_kind',
				'label'       => __( 'نوع تحویل', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_kind', true ),
				'options'     => array(
					''         => __( 'خودکار (بر اساس نوع و دسته‌ی محصول)', 'zarincoach' ),
					'digital'  => __( 'دانلودی / مجازی', 'zarincoach' ),
					'physical' => __( 'فیزیکی (ارسال پستی)', 'zarincoach' ),
					'session'  => __( 'جلسه‌ی کوچینگ', 'zarincoach' ),
				),
				'desc_tip'    => true,
				'description' => __( 'برچسب کارت، متن زمان تحویل و نوع اسکیما (Product یا Service) بر این اساس تعیین می‌شود.', 'zarincoach' ),
			)
		);
		woocommerce_wp_select(
			array(
				'id'          => '_zc_schema_type',
				'label'       => __( 'نوع اسکیما', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_schema_type', true ),
				'options'     => array(
					''        => __( 'خودکار', 'zarincoach' ),
					'Product' => 'Product',
					'Book'    => 'Product + Book',
					'Service' => 'Service',
				),
				'desc_tip'    => true,
				'description' => __( 'در حالت خودکار جلسات Service و بقیه Product هستند. برای کتاب‌ها «Product + Book» را انتخاب کنید.', 'zarincoach' ),
			)
		);
		echo '</div><div class="options_group">';

		woocommerce_wp_text_input(
			array(
				'id'          => '_zc_subtitle',
				'label'       => __( 'زیرعنوان', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_subtitle', true ),
				'placeholder' => __( 'مثلاً: کارپوشه‌ی ۳۰ روزه‌ی تمرین مرزبندی', 'zarincoach' ),
				'desc_tip'    => true,
				'description' => __( 'یک جمله‌ی کوتاه زیر عنوان صفحه‌ی محصول.', 'zarincoach' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => '_zc_badge',
				'label'       => __( 'برچسب ویژه', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_badge', true ),
				'placeholder' => __( 'مثلاً: پرفروش', 'zarincoach' ),
				'desc_tip'    => true,
				'description' => __( 'برچسب دلخواه روی تصویر کارت و صفحه‌ی محصول.', 'zarincoach' ),
			)
		);
		woocommerce_wp_textarea_input(
			array(
				'id'          => '_zc_features',
				'label'       => __( 'ویژگی‌های کلیدی', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_features', true ),
				'rows'        => 5,
				'style'       => 'height:auto',
				'desc_tip'    => true,
				'description' => __( 'هر خط یک ویژگی؛ کنار دکمه‌ی خرید با تیک نمایش داده می‌شود.', 'zarincoach' ),
			)
		);
		woocommerce_wp_text_input(
			array(
				'id'          => '_zc_delivery',
				'label'       => __( 'متن تحویل اختصاصی', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_delivery', true ),
				'desc_tip'    => true,
				'description' => __( 'خالی = متن پیش‌فرض نوع تحویل از تنظیمات قالب.', 'zarincoach' ),
			)
		);
		echo '</div><div class="options_group">';
		woocommerce_wp_textarea_input(
			array(
				'id'          => '_zc_faq',
				'label'       => __( 'پرسش‌های متداول', 'zarincoach' ),
				'value'       => (string) get_post_meta( $id, '_zc_faq', true ),
				'rows'        => 6,
				'style'       => 'height:auto',
				'placeholder' => __( 'پرسش | پاسخ', 'zarincoach' ),
				'desc_tip'    => true,
				'description' => __( 'هر خط: «پرسش | پاسخ». در صفحه‌ی محصول و اسکیمای FAQPage استفاده می‌شود.', 'zarincoach' ),
			)
		);
		echo '</div></div>';
	}
);

add_action(
	'woocommerce_admin_process_product_object',
	static function ( $product ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce ذخیره‌ی محصول توسط ووکامرس بررسی شده است.
		$kind   = isset( $_POST['_zc_kind'] ) ? sanitize_key( wp_unslash( $_POST['_zc_kind'] ) ) : '';
		$schema = isset( $_POST['_zc_schema_type'] ) ? sanitize_text_field( wp_unslash( $_POST['_zc_schema_type'] ) ) : '';
		$product->update_meta_data( '_zc_kind', in_array( $kind, array( 'digital', 'physical', 'session' ), true ) ? $kind : '' );
		$product->update_meta_data( '_zc_schema_type', in_array( $schema, array( 'Product', 'Book', 'Service' ), true ) ? $schema : '' );
		foreach ( array( '_zc_subtitle', '_zc_badge', '_zc_delivery' ) as $key ) {
			$product->update_meta_data( $key, isset( $_POST[ $key ] ) ? sanitize_text_field( wp_unslash( $_POST[ $key ] ) ) : '' );
		}
		foreach ( array( '_zc_features', '_zc_faq' ) as $key ) {
			$product->update_meta_data( $key, isset( $_POST[ $key ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $key ] ) ) : '' );
		}
		// phpcs:enable
	}
);

// آیکون زبانه.
add_action(
	'admin_head',
	static function () {
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || 'product' !== $screen->id ) {
			return;
		}
		echo '<style>#woocommerce-product-data ul.wc-tabs li.zarincoach_options a::before{content:"\f155";font-family:dashicons}#zc_product_data textarea{min-height:110px}</style>';
	}
);

if ( ! function_exists( 'zc_wc_block_pages' ) ) :
	/**
	 * برگه‌های سبد/تسویه که هنوز بلوکی‌اند (طراحی قالب برای نسخه‌ی کلاسیک است).
	 *
	 * @return array<string,int>
	 */
	function zc_wc_block_pages() {
		$out = array();
		foreach ( array( 'cart', 'checkout' ) as $key ) {
			$id = (int) wc_get_page_id( $key );
			if ( $id > 0 && has_block( 'woocommerce/' . $key, $id ) ) {
				$out[ $key ] = $id;
			}
		}
		return $out;
	}
endif;

add_action(
	'admin_notices',
	static function () {
		if ( ! current_user_can( 'manage_woocommerce' ) || ! function_exists( 'wc_get_page_id' ) ) {
			return;
		}
		$pages = zc_wc_block_pages();
		if ( ! $pages ) {
			return;
		}
		$url = wp_nonce_url( admin_url( 'admin-post.php?action=zc_wc_classic_pages' ), 'zc_wc_classic_pages' );
		echo '<div class="notice notice-warning"><p><strong>' . esc_html__( 'زرین‌کوچ:', 'zarincoach' ) . '</strong> ';
		esc_html_e( 'برگه‌های سبد خرید/تسویه حساب با بلوک‌های ووکامرس ساخته شده‌اند. طراحی حرفه‌ای قالب (کشوی سبد، نوار ارسال رایگان، اعتبارسنجی موبایل و کد پستی ایران و…) روی نسخه‌ی کلاسیک اعمال می‌شود.', 'zarincoach' );
		echo ' <a class="button button-primary" href="' . esc_url( $url ) . '">' . esc_html__( 'تبدیل به نسخه‌ی کلاسیک', 'zarincoach' ) . '</a></p></div>';
	}
);

add_action(
	'admin_post_zc_wc_classic_pages',
	static function () {
		if ( ! current_user_can( 'manage_woocommerce' ) ) {
			wp_die( esc_html__( 'دسترسی ندارید.', 'zarincoach' ) );
		}
		check_admin_referer( 'zc_wc_classic_pages' );
		foreach ( zc_wc_block_pages() as $key => $id ) {
			wp_update_post(
				wp_slash(
					array(
						'ID'           => $id,
						'post_content' => '<!-- wp:shortcode -->[woocommerce_' . $key . ']<!-- /wp:shortcode -->',
					)
				)
			);
		}
		wp_safe_redirect( wp_get_referer() ? wp_get_referer() : admin_url() );
		exit;
	}
);
