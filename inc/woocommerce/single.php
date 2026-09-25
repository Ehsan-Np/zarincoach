<?php
/**
 * ووکامرس — صفحه‌ی محصول: چیدمان خلاصه، افزوده‌های فروش و بخش‌های محتوا
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * ترتیب هوک‌های خلاصه
 * ========================================================================= */

remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );

add_action( 'woocommerce_before_single_product_summary', 'zc_wc_single_badges', 5 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_eyebrow', 4 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_subtitle', 6 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_rating', 8 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_price_box', 10 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_countdown', 12 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_features', 22 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_stock_bar', 25 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_delivery', 35 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_trust', 38 );
add_action( 'woocommerce_single_product_summary', 'zc_wc_single_share', 50 );
add_action( 'woocommerce_after_single_product_summary', 'zc_wc_single_sections', 10 );
add_action( 'woocommerce_after_add_to_cart_button', 'zc_wc_buy_now_button', 5 );
add_action( 'wp_footer', 'zc_wc_sticky_bar', 5 );

if ( ! function_exists( 'zc_wc_current_product' ) ) :
	/**
	 * محصول جاری.
	 *
	 * @return WC_Product|null
	 */
	function zc_wc_current_product() {
		global $product;
		if ( $product instanceof WC_Product ) {
			return $product;
		}
		$p = wc_get_product( get_the_ID() );
		return $p instanceof WC_Product ? $p : null;
	}
endif;

if ( ! function_exists( 'zc_wc_single_badges' ) ) :
	/**
	 * نشان‌های روی گالری.
	 *
	 * @return void
	 */
	function zc_wc_single_badges() {
		$product = zc_wc_current_product();
		if ( $product ) {
			echo zc_wc_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
endif;

if ( ! function_exists( 'zc_wc_single_eyebrow' ) ) :
	/**
	 * دسته‌ی اصلی + نوع تحویل بالای عنوان.
	 *
	 * @return void
	 */
	function zc_wc_single_eyebrow() {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return;
		}
		$kinds = zc_wc_kinds();
		$kind  = zc_wc_kind( $product );
		$term  = zc_wc_primary_term( $product->get_id() );
		echo '<div class="zc-sp__eyebrow">';
		if ( $term ) {
			echo '<a class="zc-sp__cat" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
		}
		if ( isset( $kinds[ $kind ] ) ) {
			echo '<span class="zc-sp__kind">' . zc_icon( $kinds[ $kind ]['icon'], 'h-3.5 w-3.5', false ) . esc_html( $kinds[ $kind ]['label'] ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_wc_single_subtitle' ) ) :
	/**
	 * زیرعنوان محصول.
	 *
	 * @return void
	 */
	function zc_wc_single_subtitle() {
		$product = zc_wc_current_product();
		$text    = $product ? trim( (string) get_post_meta( $product->get_id(), '_zc_subtitle', true ) ) : '';
		if ( '' !== $text ) {
			echo '<p class="zc-sp__subtitle">' . esc_html( $text ) . '</p>';
		}
	}
endif;

if ( ! function_exists( 'zc_wc_single_price_box' ) ) :
	/**
	 * قیمت + میزان صرفه‌جویی.
	 *
	 * @return void
	 */
	function zc_wc_single_price_box() {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return;
		}
		echo '<div class="zc-sp__pricebox">';
		echo '<p class="' . esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ) . '">' . $product->get_price_html() . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		if ( $product->is_on_sale() && $product->is_type( 'simple' ) ) {
			$save = (float) $product->get_regular_price() - (float) $product->get_sale_price();
			if ( $save > 0 ) {
				$pct = zc_wc_discount_percent( $product );
				/* translators: 1: مبلغ صرفه‌جویی 2: درصد */
				echo '<span class="zc-sp__save">' . wp_kses_post( sprintf( __( 'سود شما از این خرید: %1$s (٪%2$s)', 'zarincoach' ), wc_price( $save ), zc_digits_to_persian( (string) $pct ) ) ) . '</span>';
			}
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_wc_sale_end' ) ) :
	/**
	 * زمان پایان تخفیف (کمترین زمان میان گونه‌ها برای محصول متغیر).
	 *
	 * @param WC_Product $product محصول.
	 * @return int timestamp یا ۰
	 */
	function zc_wc_sale_end( $product ) {
		if ( ! $product->is_on_sale() ) {
			return 0;
		}
		$ends = array();
		$list = $product->is_type( 'variable' ) ? array_filter( array_map( 'wc_get_product', $product->get_visible_children() ) ) : array( $product );
		foreach ( $list as $item ) {
			$to = $item->get_date_on_sale_to();
			if ( $to && $to->getTimestamp() > time() ) {
				$ends[] = $to->getTimestamp();
			}
		}
		return $ends ? min( $ends ) : 0;
	}
endif;

if ( ! function_exists( 'zc_wc_countdown_html' ) ) :
	/**
	 * شمارش معکوس (بدون JS هم تاریخ پایان را نشان می‌دهد).
	 *
	 * @param int    $end   timestamp.
	 * @param string $label برچسب.
	 * @param string $class کلاس.
	 * @return string
	 */
	function zc_wc_countdown_html( $end, $label = '', $class = '' ) {
		if ( $end <= time() ) {
			return '';
		}
		$label = '' !== $label ? $label : __( 'فرصت خرید با تخفیف:', 'zarincoach' );
		$cells = '';
		foreach ( array( 'd' => __( 'روز', 'zarincoach' ), 'h' => __( 'ساعت', 'zarincoach' ), 'm' => __( 'دقیقه', 'zarincoach' ), 's' => __( 'ثانیه', 'zarincoach' ) ) as $k => $t ) {
			$cells .= '<span class="zc-cd__cell"><b data-cd="' . esc_attr( $k ) . '">۰۰</b><small>' . esc_html( $t ) . '</small></span>';
		}
		return '<div class="zc-cd ' . esc_attr( $class ) . '" data-zc-countdown="' . esc_attr( (string) $end ) . '">'
			. '<span class="zc-cd__label">' . zc_icon( 'fire', 'h-4 w-4', false ) . esc_html( $label ) . '</span>'
			. '<span class="zc-cd__cells" role="timer" aria-label="' . esc_attr( wp_date( 'j F Y، H:i', $end ) ) . '">' . $cells . '</span>'
			. '</div>';
	}
endif;

if ( ! function_exists( 'zc_wc_single_countdown' ) ) :
	/**
	 * شمارش معکوس پایان تخفیف.
	 *
	 * @return void
	 */
	function zc_wc_single_countdown() {
		$product = zc_wc_current_product();
		if ( ! $product || ! zc_switch( 'sp_countdown', true ) ) {
			return;
		}
		echo zc_wc_countdown_html( zc_wc_sale_end( $product ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'zc_wc_single_features' ) ) :
	/**
	 * فهرست ویژگی‌های کلیدی (از «داده‌های محصول ← زرین‌کوچ»).
	 *
	 * @return void
	 */
	function zc_wc_single_features() {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return;
		}
		$lines = zc_wc_lines( get_post_meta( $product->get_id(), '_zc_features', true ) );
		if ( ! $lines ) {
			return;
		}
		echo '<ul class="zc-sp__features">';
		foreach ( $lines as $line ) {
			echo '<li>' . zc_icon( 'check', 'h-4 w-4', false ) . '<span>' . esc_html( $line ) . '</span></li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul>';
	}
endif;

if ( ! function_exists( 'zc_wc_single_stock_bar' ) ) :
	/**
	 * نوار «موجودی محدود».
	 *
	 * @return void
	 */
	function zc_wc_single_stock_bar() {
		$product = zc_wc_current_product();
		if ( ! $product || ! zc_switch( 'sp_stock_bar', true ) || ! $product->managing_stock() || ! $product->is_in_stock() ) {
			return;
		}
		$qty       = (int) $product->get_stock_quantity();
		$threshold = max( 1, (int) zc_opt( 'sp_stock_threshold', 10 ) );
		if ( $qty <= 0 || $qty > $threshold ) {
			return;
		}
		$width = max( 8, min( 100, (int) round( ( $qty / $threshold ) * 100 ) ) );
		echo '<div class="zc-stockbar">';
		/* translators: %s: تعداد باقی‌مانده */
		echo '<p>' . zc_icon( 'alert', 'h-4 w-4', false ) . esc_html( sprintf( __( 'فقط %s عدد در انبار باقی مانده است', 'zarincoach' ), zc_digits_to_persian( (string) $qty ) ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<span class="zc-stockbar__track"><span style="width:' . (int) $width . '%"></span></span>';
		echo '</div>';
	}
endif;

// وضعیت موجودی پیش‌فرض ووکامرس وقتی نوار موجودی نمایش داده می‌شود تکراری است.
add_filter(
	'woocommerce_get_stock_html',
	static function ( $html, $product ) {
		if ( ! is_product() || ! $product instanceof WC_Product || ! $product->is_in_stock() ) {
			return $html;
		}
		if ( ! $product->managing_stock() ) {
			return '';
		}
		$qty = (int) $product->get_stock_quantity();
		if ( zc_switch( 'sp_stock_bar', true ) && $qty > 0 && $qty <= max( 1, (int) zc_opt( 'sp_stock_threshold', 10 ) ) ) {
			return '';
		}
		return '<p class="stock in-stock">' . zc_icon( 'check', 'h-4 w-4', false ) . esc_html__( 'موجود در انبار', 'zarincoach' ) . '</p>';
	},
	10,
	2
);

if ( ! function_exists( 'zc_wc_delivery_text' ) ) :
	/**
	 * متن تحویل بر اساس نوع محصول.
	 *
	 * @param WC_Product $product محصول.
	 * @return array{title:string, text:string, icon:string}
	 */
	function zc_wc_delivery_text( $product ) {
		$kind   = zc_wc_kind( $product );
		$custom = trim( (string) get_post_meta( $product->get_id(), '_zc_delivery', true ) );
		$map    = array(
			'digital'  => array(
				'title' => __( 'تحویل فوری و دائمی', 'zarincoach' ),
				'text'  => (string) zc_opt( 'sp_delivery_digital', 'بلافاصله پس از پرداخت، لینک دانلود در صفحه‌ی سفارش، حساب کاربری و ایمیل شما فعال می‌شود.' ),
				'icon'  => 'zap',
			),
			'physical' => array(
				'title' => __( 'ارسال به سراسر ایران', 'zarincoach' ),
				'text'  => (string) zc_opt( 'sp_delivery_physical', 'سفارش‌ها ظرف ۱ تا ۲ روز کاری بسته‌بندی و با پست پیشتاز ارسال می‌شوند؛ کد رهگیری پیامک می‌شود.' ),
				'icon'  => 'truck',
			),
			'session'  => array(
				'title' => __( 'هماهنگی زمان جلسه', 'zarincoach' ),
				'text'  => (string) zc_opt( 'sp_delivery_session', 'پس از پرداخت، ظرف ۲۴ ساعت کاری برای تعیین زمان جلسه با شما تماس گرفته می‌شود. جلسات آنلاین فقط از بستر امن برگزار می‌شوند.' ),
				'icon'  => 'calendar',
			),
		);
		$item = isset( $map[ $kind ] ) ? $map[ $kind ] : $map['physical'];
		if ( '' !== $custom ) {
			$item['text'] = $custom;
		}
		return $item;
	}
endif;

if ( ! function_exists( 'zc_wc_single_delivery' ) ) :
	/**
	 * کادر نحوه‌ی تحویل.
	 *
	 * @return void
	 */
	function zc_wc_single_delivery() {
		$product = zc_wc_current_product();
		if ( ! $product || ! zc_switch( 'sp_delivery', true ) ) {
			return;
		}
		$d = zc_wc_delivery_text( $product );
		if ( '' === trim( $d['text'] ) ) {
			return;
		}
		echo '<div class="zc-sp__delivery"><span class="zc-sp__delivery-icon">' . zc_icon( $d['icon'], 'h-5 w-5', false ) . '</span><div><strong>' . esc_html( $d['title'] ) . '</strong><p>' . esc_html( $d['text'] ) . '</p></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

if ( ! function_exists( 'zc_wc_trust_items' ) ) :
	/**
	 * موارد اعتماد زیر دکمه‌ی خرید (هر خط: عنوان | پیوند اختیاری).
	 *
	 * @return array<int, array{text:string, url:string, icon:string}>
	 */
	function zc_wc_trust_items() {
		$icons = array( 'shield-check', 'refresh', 'headphones', 'lock' );
		$raw   = (string) zc_opt( 'sp_trust_items', "پرداخت امن از درگاه بانکی\nمرجوعی و بازگشت وجه طبق قانون | shipping\nپشتیبانی پس از خرید | contact" );
		$out   = array();
		foreach ( zc_wc_lines( $raw ) as $i => $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			$url   = isset( $parts[1] ) ? $parts[1] : '';
			if ( '' !== $url && ! preg_match( '#^(https?:)?//|^/#', $url ) && function_exists( 'zc_page_url_by_key' ) ) {
				$url = (string) zc_page_url_by_key( $url );
			}
			$out[] = array(
				'text' => $parts[0],
				'url'  => $url,
				'icon' => $icons[ $i % count( $icons ) ],
			);
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_wc_single_trust' ) ) :
	/**
	 * ردیف اعتماد.
	 *
	 * @return void
	 */
	function zc_wc_single_trust() {
		if ( ! zc_switch( 'sp_trust', true ) ) {
			return;
		}
		$items = zc_wc_trust_items();
		if ( ! $items ) {
			return;
		}
		echo '<ul class="zc-sp__trust">';
		foreach ( $items as $item ) {
			$inner = zc_icon( $item['icon'], 'h-4 w-4', false ) . '<span>' . esc_html( $item['text'] ) . '</span>';
			echo '<li>' . ( '' !== $item['url'] ? '<a href="' . esc_url( $item['url'] ) . '">' . $inner . '</a>' : $inner ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</ul>';
	}
endif;

if ( ! function_exists( 'zc_wc_single_share' ) ) :
	/**
	 * اشتراک‌گذاری محصول.
	 *
	 * @return void
	 */
	function zc_wc_single_share() {
		if ( ! zc_switch( 'sp_share', true ) || ! function_exists( 'zc_share_render' ) ) {
			return;
		}
		echo '<div class="zc-sp__share"><span>' . esc_html__( 'اشتراک‌گذاری:', 'zarincoach' ) . '</span>';
		zc_share_render( 'inline', array( 'style' => 'outline' ) );
		echo '</div>';
	}
endif;

/* =========================================================================
 * خرید فوری
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_buy_now_button' ) ) :
	/**
	 * دکمه‌ی «خرید فوری» (افزودن به سبد و انتقال مستقیم به تسویه‌حساب).
	 *
	 * @return void
	 */
	function zc_wc_buy_now_button() {
		$product = zc_wc_current_product();
		if ( ! $product || ! is_product() || ! zc_switch( 'sp_buy_now', true ) || $product->is_type( array( 'external', 'grouped' ) ) ) {
			return;
		}
		echo '<button type="submit" name="zc_buy_now" value="1" class="zc-buy-now button alt">' . zc_icon( 'zap', 'h-4 w-4', false ) . esc_html__( 'خرید فوری', 'zarincoach' ) . '</button>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}
endif;

add_filter(
	'woocommerce_add_to_cart_redirect',
	static function ( $url ) {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- فرم افزودن به سبد ووکامرس (بررسی nonce توسط ووکامرس انجام نمی‌شود؛ عملیات فقط هدایت است).
		if ( ! empty( $_REQUEST['zc_buy_now'] ) && 0 === wc_notice_count( 'error' ) ) {
			wc_clear_notices();
			return wc_get_checkout_url();
		}
		return $url;
	}
);

/* =========================================================================
 * بخش‌های محتوا (جایگزین زبانه‌ها)
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_faq_items' ) ) :
	/**
	 * پرسش‌های متداول محصول (هر خط: پرسش | پاسخ).
	 *
	 * @param int $product_id شناسه.
	 * @return array<int, array{q:string, a:string}>
	 */
	function zc_wc_faq_items( $product_id ) {
		$out = array();
		foreach ( zc_wc_lines( get_post_meta( $product_id, '_zc_faq', true ) ) as $line ) {
			$parts = array_map( 'trim', explode( '|', $line, 2 ) );
			if ( isset( $parts[1] ) && '' !== $parts[0] && '' !== $parts[1] ) {
				$out[] = array(
					'q' => $parts[0],
					'a' => $parts[1],
				);
			}
		}
		return $out;
	}
endif;

add_filter(
	'woocommerce_product_tabs',
	static function ( $tabs ) {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return $tabs;
		}
		if ( isset( $tabs['description'] ) ) {
			$tabs['description']['title'] = __( 'معرفی محصول', 'zarincoach' );
		}
		// مشخصات همیشه (حتی بدون ویژگی) نمایش داده شود: نوع تحویل، شناسه، دسته‌ها.
		$tabs['additional_information'] = array(
			'title'    => __( 'مشخصات', 'zarincoach' ),
			'priority' => 20,
			'callback' => 'zc_wc_specs_section',
		);
		if ( zc_wc_faq_items( $product->get_id() ) ) {
			$tabs['zc_faq'] = array(
				'title'    => __( 'پرسش‌های متداول', 'zarincoach' ),
				'priority' => 25,
				'callback' => 'zc_wc_faq_section',
			);
		}
		if ( isset( $tabs['reviews'] ) ) {
			$count                    = $product->get_review_count();
			$tabs['reviews']['title'] = $count
				/* translators: %s: تعداد دیدگاه */
				? sprintf( __( 'دیدگاه خریداران (%s)', 'zarincoach' ), zc_digits_to_persian( (string) $count ) )
				: __( 'دیدگاه خریداران', 'zarincoach' );
			$tabs['reviews']['priority'] = 30;
		}
		return $tabs;
	},
	98
);

if ( ! function_exists( 'zc_wc_specs_section' ) ) :
	/**
	 * جدول مشخصات.
	 *
	 * @return void
	 */
	function zc_wc_specs_section() {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return;
		}
		$kinds = zc_wc_kinds();
		$kind  = zc_wc_kind( $product );
		$rows  = array();
		if ( isset( $kinds[ $kind ] ) ) {
			$rows[] = array( __( 'نوع تحویل', 'zarincoach' ), esc_html( $kinds[ $kind ]['label'] ) );
		}
		if ( wc_product_sku_enabled() && $product->get_sku() ) {
			$rows[] = array( __( 'شناسه‌ی محصول', 'zarincoach' ), '<span dir="ltr">' . esc_html( $product->get_sku() ) . '</span>' );
		}
		$cats = wc_get_product_category_list( $product->get_id(), '، ' );
		if ( $cats ) {
			$rows[] = array( __( 'دسته', 'zarincoach' ), $cats );
		}
		if ( $product->has_weight() && 'physical' === $kind ) {
			$rows[] = array( __( 'وزن', 'zarincoach' ), esc_html( wc_format_weight( $product->get_weight() ) ) );
		}
		if ( $product->has_dimensions() && 'physical' === $kind ) {
			$rows[] = array( __( 'ابعاد', 'zarincoach' ), esc_html( wc_format_dimensions( $product->get_dimensions( false ) ) ) );
		}
		foreach ( $product->get_attributes() as $attribute ) {
			if ( ! $attribute->get_visible() ) {
				continue;
			}
			$values = $attribute->is_taxonomy()
				? wc_get_product_terms( $product->get_id(), $attribute->get_name(), array( 'fields' => 'names' ) )
				: $attribute->get_options();
			$rows[] = array( wc_attribute_label( $attribute->get_name() ), esc_html( implode( '، ', $values ) ) );
		}
		$tags = wc_get_product_tag_list( $product->get_id(), '، ' );
		if ( $tags ) {
			$rows[] = array( __( 'برچسب‌ها', 'zarincoach' ), $tags );
		}
		echo '<table class="zc-specs"><tbody>';
		foreach ( $rows as $row ) {
			echo '<tr><th scope="row">' . esc_html( $row[0] ) . '</th><td>' . wp_kses_post( $row[1] ) . '</td></tr>';
		}
		echo '</tbody></table>';
	}
endif;

if ( ! function_exists( 'zc_wc_faq_section' ) ) :
	/**
	 * پرسش‌های متداول (details بومی، بدون JS).
	 *
	 * @return void
	 */
	function zc_wc_faq_section() {
		$product = zc_wc_current_product();
		if ( ! $product ) {
			return;
		}
		echo '<div class="zc-sp-faq">';
		foreach ( zc_wc_faq_items( $product->get_id() ) as $i => $item ) {
			echo '<details class="zc-sp-faq__item"' . ( 0 === $i ? ' open' : '' ) . '><summary>' . esc_html( $item['q'] ) . zc_icon( 'chevron-down', 'h-4 w-4', false ) . '</summary><div class="zc-sp-faq__a">' . wpautop( esc_html( $item['a'] ) ) . '</div></details>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_wc_single_sections' ) ) :
	/**
	 * زبانه‌های ووکامرس به‌صورت بخش‌های پشت‌سرهم با ناوبری چسبان (بهتر برای خواندن و سئو).
	 *
	 * @return void
	 */
	function zc_wc_single_sections() {
		$tabs = apply_filters( 'woocommerce_product_tabs', array() );
		if ( empty( $tabs ) ) {
			return;
		}
		uasort(
			$tabs,
			static function ( $a, $b ) {
				return ( isset( $a['priority'] ) ? (int) $a['priority'] : 50 ) <=> ( isset( $b['priority'] ) ? (int) $b['priority'] : 50 );
			}
		);
		?>
		<div class="zc-sp-sections woocommerce-tabs wc-tabs-wrapper">
			<?php if ( count( $tabs ) > 1 && zc_switch( 'sp_sections_nav', true ) ) : ?>
				<nav class="zc-sp-nav" aria-label="<?php esc_attr_e( 'بخش‌های صفحه‌ی محصول', 'zarincoach' ); ?>">
					<ul>
						<?php foreach ( $tabs as $key => $tab ) : ?>
							<li><a href="#tab-<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></a></li>
						<?php endforeach; ?>
					</ul>
				</nav>
			<?php endif; ?>
			<?php foreach ( $tabs as $key => $tab ) : ?>
				<section class="zc-sp-section woocommerce-Tabs-panel woocommerce-Tabs-panel--<?php echo esc_attr( $key ); ?>" id="tab-<?php echo esc_attr( $key ); ?>" aria-labelledby="tab-title-<?php echo esc_attr( $key ); ?>">
					<h2 class="zc-sp-section__title" id="tab-title-<?php echo esc_attr( $key ); ?>"><?php echo wp_kses_post( apply_filters( 'woocommerce_product_' . $key . '_tab_title', $tab['title'], $key ) ); ?></h2>
					<div class="zc-sp-section__body<?php echo 'description' === $key ? ' zc-prose' : ''; ?>">
						<?php
						if ( isset( $tab['callback'] ) ) {
							call_user_func( $tab['callback'], $key, $tab );
						}
						?>
					</div>
				</section>
			<?php endforeach; ?>
			<?php do_action( 'woocommerce_product_after_tabs' ); ?>
		</div>
		<?php
	}
endif;

// عنوان تکراری «توضیحات» داخل بخش معرفی حذف شود (عنوان بخش خودش H2 است).
add_filter( 'woocommerce_product_description_heading', '__return_empty_string' );
add_filter( 'woocommerce_product_additional_information_heading', '__return_empty_string' );

// عنوان «محصولات مرتبط» و «پیشنهادها» فارسی روان‌تر.
add_filter(
	'woocommerce_product_related_products_heading',
	static function () {
		return __( 'شاید این‌ها را هم دوست داشته باشید', 'zarincoach' );
	}
);
add_filter(
	'woocommerce_product_upsells_products_heading',
	static function () {
		return __( 'پیشنهاد مکمل برای این محصول', 'zarincoach' );
	}
);

/* =========================================================================
 * نوار چسبان خرید
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_sticky_bar' ) ) :
	/**
	 * نوار خرید چسبان (پس از عبور از دکمه‌ی اصلی نمایش داده می‌شود).
	 *
	 * @return void
	 */
	function zc_wc_sticky_bar() {
		if ( ! is_product() || ! zc_switch( 'sp_sticky_bar', true ) ) {
			return;
		}
		$product = wc_get_product( get_queried_object_id() );
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			return;
		}
		?>
		<div class="zc-stickybar" data-zc-stickybar hidden>
			<div class="zc-container zc-stickybar__in">
				<div class="zc-stickybar__info">
					<?php echo wp_get_attachment_image( (int) $product->get_image_id(), 'woocommerce_gallery_thumbnail', false, array( 'class' => 'zc-stickybar__img', 'alt' => '' ) ); ?>
					<div>
						<strong><?php echo esc_html( $product->get_name() ); ?></strong>
						<span class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					</div>
				</div>
				<button type="button" class="zc-btn zc-btn-primary zc-btn-sm" data-zc-stickybar-buy>
					<?php zc_icon( 'bag', 'h-4 w-4' ); ?>
					<?php echo esc_html( $product->is_type( 'variable' ) ? __( 'انتخاب و خرید', 'zarincoach' ) : $product->single_add_to_cart_text() ); ?>
				</button>
			</div>
		</div>
		<?php
	}
endif;
