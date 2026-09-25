<?php
/**
 * ووکامرس — سبد خرید سربرگ، سبد کشویی، نوار ارسال رایگان، تسویه‌حساب و حساب کاربری
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * دکمه‌ی سبد در سربرگ + fragments
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_cart_count' ) ) :
	/**
	 * تعداد اقلام سبد.
	 *
	 * @return int
	 */
	function zc_wc_cart_count() {
		return ( function_exists( 'WC' ) && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
	}
endif;

if ( ! function_exists( 'zc_wc_header_tools' ) ) :
	/**
	 * آیکون‌های حساب کاربری و سبد خرید در سربرگ.
	 *
	 * @param array $args show_cart، show_account.
	 * @return string
	 */
	function zc_wc_header_tools( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_cart'    => zc_switch( 'header_cart', true ),
				'show_account' => zc_switch( 'header_account', true ),
			)
		);
		$html = '';
		if ( $args['show_account'] ) {
			$account = wc_get_page_permalink( 'myaccount' );
			$label   = is_user_logged_in() ? __( 'حساب کاربری', 'zarincoach' ) : __( 'ورود / ثبت‌نام', 'zarincoach' );
			$html   .= '<a class="zc-btn-icon zc-hdr-account hidden sm:grid" href="' . esc_url( $account ) . '" aria-label="' . esc_attr( $label ) . '" title="' . esc_attr( $label ) . '">' . zc_icon( 'user', 'h-[18px] w-[18px]', false ) . '</a>';
		}
		if ( $args['show_cart'] ) {
			$count   = zc_wc_cart_count();
			$drawer  = zc_wc_minicart_enabled();
			$attrs   = $drawer ? ' data-zc-cart-open aria-controls="zc-cart-drawer" aria-expanded="false"' : '';
			$html   .= '<a class="zc-btn-icon zc-hdr-cart" href="' . esc_url( wc_get_cart_url() ) . '"' . $attrs . ' aria-label="' . esc_attr__( 'سبد خرید', 'zarincoach' ) . '">'
				. zc_icon( 'bag', 'h-[18px] w-[18px]', false )
				. zc_wc_count_badge( $count )
				. '</a>';
		}
		return $html;
	}
endif;

if ( ! function_exists( 'zc_wc_count_badge' ) ) :
	/**
	 * نشان تعداد (قابل به‌روزرسانی با fragments).
	 *
	 * @param int $count تعداد.
	 * @return string
	 */
	function zc_wc_count_badge( $count ) {
		return '<span class="zc-cart-count' . ( $count > 0 ? '' : ' is-empty' ) . '" aria-live="polite"><span class="screen-reader-text">' . esc_html__( 'تعداد اقلام:', 'zarincoach' ) . ' </span>' . esc_html( zc_digits_to_persian( (string) $count ) ) . '</span>';
	}
endif;

add_filter(
	'woocommerce_add_to_cart_fragments',
	static function ( $fragments ) {
		$count                            = zc_wc_cart_count();
		$fragments['span.zc-cart-count']  = zc_wc_count_badge( $count );
		$fragments['span.zc-drawer-count'] = '<span class="zc-drawer-count">' . esc_html( zc_digits_to_persian( (string) $count ) ) . '</span>';
		return $fragments;
	}
);

/* =========================================================================
 * سبد کشویی
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_cart_drawer' ) ) :
	/**
	 * سبد کشویی (پایان صفحه).
	 *
	 * @return void
	 */
	function zc_wc_cart_drawer() {
		if ( ! zc_wc_minicart_enabled() || is_singular( 'elementor_library' ) ) {
			return;
		}
		?>
		<div class="zc-cartdrawer" id="zc-cart-drawer" data-zc-cart-drawer role="dialog" aria-modal="true" aria-labelledby="zc-cart-drawer-title" hidden>
			<div class="zc-cartdrawer__backdrop" data-zc-cart-close></div>
			<div class="zc-cartdrawer__panel" tabindex="-1">
				<div class="zc-cartdrawer__head">
					<p class="zc-cartdrawer__title" id="zc-cart-drawer-title"><?php zc_icon( 'bag', 'h-5 w-5' ); ?><?php esc_html_e( 'سبد خرید شما', 'zarincoach' ); ?> <span class="zc-drawer-count"><?php echo esc_html( zc_digits_to_persian( (string) zc_wc_cart_count() ) ); ?></span></p>
					<button type="button" class="zc-btn-icon" data-zc-cart-close aria-label="<?php esc_attr_e( 'بستن سبد خرید', 'zarincoach' ); ?>"><?php zc_icon( 'close', 'h-4 w-4' ); ?></button>
				</div>
				<div class="widget_shopping_cart_content"><?php woocommerce_mini_cart(); ?></div>
			</div>
		</div>
		<?php
	}
endif;
add_action( 'wp_footer', 'zc_wc_cart_drawer', 4 );

/* =========================================================================
 * نوار ارسال رایگان
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_free_shipping_bar' ) ) :
	/**
	 * پیشرفت تا ارسال رایگان (فقط سبدهای دارای کالای فیزیکی).
	 *
	 * @return void
	 */
	function zc_wc_free_shipping_bar() {
		$threshold = (float) zc_wc_normalize_digits( (string) zc_opt( 'shop_free_shipping', 0 ) );
		if ( $threshold <= 0 || ! WC()->cart || WC()->cart->is_empty() || ! WC()->cart->needs_shipping() ) {
			return;
		}
		$subtotal = (float) WC()->cart->get_displayed_subtotal();
		$left     = max( 0, $threshold - $subtotal );
		$pct      = (int) min( 100, round( ( $subtotal / $threshold ) * 100 ) );
		echo '<div class="zc-freeship' . ( $left <= 0 ? ' is-done' : '' ) . '">';
		if ( $left > 0 ) {
			/* translators: %s: مبلغ باقی‌مانده */
			echo '<p>' . zc_icon( 'truck', 'h-4 w-4', false ) . wp_kses_post( sprintf( __( 'فقط %s تا ارسال رایگان باقی مانده است', 'zarincoach' ), '<b>' . wc_price( $left ) . '</b>' ) ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			echo '<p>' . zc_icon( 'check', 'h-4 w-4', false ) . esc_html__( 'تبریک! ارسال این سفارش رایگان است.', 'zarincoach' ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '<span class="zc-freeship__track"><span style="width:' . (int) $pct . '%"></span></span></div>';
	}
endif;
add_action( 'woocommerce_before_mini_cart_contents', 'zc_wc_free_shipping_bar', 5 );
add_action( 'woocommerce_before_cart_table', 'zc_wc_free_shipping_bar', 5 );

// ارسال رایگان خودکار پس از رسیدن به آستانه (بدون نیاز به تعریف روش «ارسال رایگان» جداگانه).
add_filter(
	'woocommerce_package_rates',
	static function ( $rates, $package ) {
		$threshold = (float) zc_wc_normalize_digits( (string) zc_opt( 'shop_free_shipping', 0 ) );
		if ( $threshold <= 0 || ! WC()->cart || (float) WC()->cart->get_displayed_subtotal() < $threshold ) {
			return $rates;
		}
		foreach ( $rates as $rate ) {
			if ( 'local_pickup' === $rate->get_method_id() ) {
				continue;
			}
			$rate->set_cost( 0 );
			$rate->set_taxes( array() );
			/* translators: %s: نام روش ارسال */
			$rate->set_label( sprintf( __( '%s (رایگان)', 'zarincoach' ), $rate->get_label() ) );
		}
		return $rates;
	},
	20,
	2
);

/* =========================================================================
 * سبد خرید خالی
 * ========================================================================= */

remove_action( 'woocommerce_cart_is_empty', 'wc_empty_cart_message', 10 );
add_action(
	'woocommerce_cart_is_empty',
	static function () {
		?>
		<div class="zc-empty-cart wc-empty-cart-message">
			<span class="zc-empty-cart__icon"><?php zc_icon( 'bag', 'h-9 w-9' ); ?></span>
			<h2 class="cart-empty"><?php esc_html_e( 'سبد خرید شما خالی است', 'zarincoach' ); ?></h2>
			<p><?php esc_html_e( 'کتاب‌ها، کارپوشه‌ها و بسته‌های کوچینگ را ببینید و مسیر رشدتان را همین امروز شروع کنید.', 'zarincoach' ); ?></p>
		</div>
		<?php
	},
	10
);

add_filter(
	'woocommerce_return_to_shop_text',
	static function () {
		return __( 'مشاهده‌ی فروشگاه', 'zarincoach' );
	}
);

/* =========================================================================
 * تسویه‌حساب
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_cart_is_virtual' ) ) :
	/**
	 * آیا سبد فقط محصول مجازی/دانلودی دارد؟
	 *
	 * @return bool
	 */
	function zc_wc_cart_is_virtual() {
		if ( ! WC()->cart || WC()->cart->is_empty() ) {
			return false;
		}
		return ! WC()->cart->needs_shipping();
	}
endif;

add_filter(
	'woocommerce_checkout_fields',
	static function ( $fields ) {
		if ( isset( $fields['billing']['billing_company'] ) && ! zc_switch( 'checkout_company', false ) ) {
			unset( $fields['billing']['billing_company'] );
		}
		if ( isset( $fields['shipping']['shipping_company'] ) && ! zc_switch( 'checkout_company', false ) ) {
			unset( $fields['shipping']['shipping_company'] );
		}

		// تلفن همراه (ضروری) بلافاصله پس از نام.
		if ( isset( $fields['billing']['billing_phone'] ) ) {
			$fields['billing']['billing_phone']['label']       = __( 'تلفن همراه', 'zarincoach' );
			$fields['billing']['billing_phone']['placeholder'] = '09xxxxxxxxx';
			$fields['billing']['billing_phone']['required']    = true;
			$fields['billing']['billing_phone']['priority']    = 22;
			$fields['billing']['billing_phone']['class']       = array( 'form-row-first' );
			$fields['billing']['billing_phone']['custom_attributes'] = array(
				'dir'       => 'ltr',
				'inputmode' => 'tel',
			);
		}
		if ( isset( $fields['billing']['billing_email'] ) ) {
			$fields['billing']['billing_email']['priority'] = 24;
			$fields['billing']['billing_email']['class']    = array( 'form-row-last' );
			$fields['billing']['billing_email']['custom_attributes'] = array( 'dir' => 'ltr' );
		}
		foreach ( array( 'billing', 'shipping' ) as $group ) {
			if ( isset( $fields[ $group ][ $group . '_postcode' ] ) ) {
				$fields[ $group ][ $group . '_postcode' ]['custom_attributes'] = array(
					'dir'       => 'ltr',
					'inputmode' => 'numeric',
					'maxlength' => '10',
				);
			}
		}

		// سبد مجازی: نشانی پستی لازم نیست (فقط نام، تلفن، ایمیل).
		if ( zc_switch( 'checkout_virtual_simple', true ) && zc_wc_cart_is_virtual() ) {
			foreach ( array( 'billing_country', 'billing_state', 'billing_city', 'billing_address_1', 'billing_address_2', 'billing_postcode' ) as $key ) {
				unset( $fields['billing'][ $key ] );
			}
		}

		if ( isset( $fields['order']['order_comments'] ) ) {
			$fields['order']['order_comments']['label']       = __( 'توضیحات سفارش (اختیاری)', 'zarincoach' );
			$fields['order']['order_comments']['placeholder'] = __( 'اگر نکته‌ای درباره‌ی سفارش یا زمان ترجیحی جلسه دارید بنویسید.', 'zarincoach' );
		}
		return $fields;
	},
	20
);

// تلفن همراه در قالب نشانی پیش‌فرض هم ضروری است؛ وگرنه اسکریپت «محلی‌سازی کشور» ووکامرس
// هنگام انتخاب کشور، برچسب و وضعیت ضروری را به «تلفن (اختیاری)» برمی‌گرداند.
add_filter(
	'woocommerce_get_country_locale_default',
	static function ( $locale ) {
		{
			$locale['phone'] = array_merge(
				isset( $locale['phone'] ) ? (array) $locale['phone'] : array(),
				array(
					'label'    => __( 'تلفن همراه', 'zarincoach' ),
					'required' => true,
					'priority' => 22,
					'class'    => array( 'form-row-first' ),
				)
			);
		}
		return $locale;
	},
	20
);
// ایران: ووکامرس ترتیب «استان، شهر، نشانی» را اعمال می‌کند؛ «ادامه‌ی نشانی» باید پس از نشانی بیاید.
add_filter(
	'woocommerce_get_country_locale',
	static function ( $locale ) {
		$locale['IR']['address_2'] = array_merge( isset( $locale['IR']['address_2'] ) ? (array) $locale['IR']['address_2'] : array(), array( 'priority' => 85 ) );
		return $locale;
	},
	20
);
add_filter(
	'woocommerce_checkout_fields',
	static function ( $fields ) {
		if ( isset( $fields['billing']['billing_phone'] ) ) {
			$fields['billing']['billing_phone']['required'] = true;
		}
		return $fields;
	},
	99
);

// نشانی پستی سبد مجازی حذف شد؛ کشور پیش‌فرض برای محاسبه‌ی مالیات/درگاه همچنان ایران است.
add_filter(
	'default_checkout_billing_country',
	static function ( $country ) {
		return '' !== (string) $country ? $country : 'IR';
	}
);

add_action(
	'woocommerce_after_checkout_validation',
	static function ( $data, $errors ) {
		// phpcs:disable WordPress.Security.NonceVerification.Missing -- nonce تسویه‌حساب توسط ووکامرس بررسی شده است.
		if ( zc_switch( 'checkout_validate_ir', true ) ) {
			$phone = isset( $data['billing_phone'] ) ? preg_replace( '/[\s\-]/', '', zc_wc_normalize_digits( $data['billing_phone'] ) ) : '';
			if ( '' !== $phone && ! preg_match( '/^(\+98|0098|0)?9\d{9}$/', $phone ) ) {
				$errors->add( 'validation', __( 'شماره‌ی تلفن همراه معتبر نیست (نمونه: ۰۹۱۲۳۴۵۶۷۸۹).', 'zarincoach' ) );
			}
			foreach ( array( 'billing', 'shipping' ) as $group ) {
				$country = isset( $data[ $group . '_country' ] ) ? $data[ $group . '_country' ] : '';
				$code    = isset( $data[ $group . '_postcode' ] ) ? zc_wc_normalize_digits( $data[ $group . '_postcode' ] ) : '';
				if ( 'IR' === $country && '' !== $code && ! preg_match( '/^\d{10}$/', $code ) ) {
					$errors->add( 'validation', __( 'کد پستی باید ۱۰ رقم (بدون خط تیره) باشد.', 'zarincoach' ) );
					break;
				}
			}
		}
		// phpcs:enable
	},
	10,
	2
);

// ذخیره‌ی ارقام لاتین در سفارش (کد پستی/تلفن با ارقام فارسی وارد شده‌اند).
add_filter(
	'woocommerce_process_checkout_field_billing_phone',
	'zc_wc_normalize_digits'
);
add_filter(
	'woocommerce_process_checkout_field_billing_postcode',
	'zc_wc_normalize_digits'
);
add_filter(
	'woocommerce_process_checkout_field_shipping_postcode',
	'zc_wc_normalize_digits'
);

// یادداشت اعتماد و قوانین کنار دکمه‌ی پرداخت.
add_action(
	'woocommerce_review_order_after_submit',
	static function () {
		$text = trim( (string) zc_opt( 'checkout_note', 'پرداخت از طریق درگاه امن شاپرک انجام می‌شود. اطلاعات شما نزد ما محرمانه است.' ) );
		if ( '' !== $text ) {
			echo '<p class="zc-checkout-note">' . zc_icon( 'lock', 'h-4 w-4', false ) . esc_html( $text ) . '</p>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
);

// عنوان‌های روشن‌تر.
add_filter(
	'woocommerce_order_button_text',
	static function () {
		return __( 'ثبت سفارش و پرداخت', 'zarincoach' );
	}
);

/* =========================================================================
 * پس از خرید: یادآور هماهنگی جلسه
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_order_has_session' ) ) :
	/**
	 * آیا سفارش شامل بسته‌ی جلسه است؟
	 *
	 * @param WC_Order $order سفارش.
	 * @return bool
	 */
	function zc_wc_order_has_session( $order ) {
		if ( ! $order instanceof WC_Order ) {
			return false;
		}
		foreach ( $order->get_items() as $item ) {
			$product = $item instanceof WC_Order_Item_Product ? $item->get_product() : null;
			if ( $product && 'session' === zc_wc_kind( $product ) ) {
				return true;
			}
		}
		return false;
	}
endif;

add_action(
	'woocommerce_thankyou',
	static function ( $order_id ) {
		$order = wc_get_order( $order_id );
		if ( ! zc_wc_order_has_session( $order ) ) {
			return;
		}
		$contact = function_exists( 'zc_contact_fields' ) ? zc_contact_fields() : array();
		$phone   = isset( $contact['phone'] ) ? (string) $contact['phone'] : '';
		echo '<div class="zc-order-session">' . zc_icon( 'calendar', 'h-5 w-5', false ) . '<div><strong>' . esc_html__( 'گام بعدی: هماهنگی زمان جلسه', 'zarincoach' ) . '</strong><p>' . esc_html( (string) zc_opt( 'sp_delivery_session', '' ) ) . ( '' !== $phone ? ' ' . esc_html__( 'تلفن هماهنگی:', 'zarincoach' ) . ' <a dir="ltr" href="tel:' . esc_attr( zc_normalize_phone( $phone ) ) . '">' . esc_html( $phone ) . '</a>' : '' ) . '</p></div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	5
);

add_action(
	'woocommerce_email_order_details',
	static function ( $order, $sent_to_admin, $plain_text ) {
		if ( $sent_to_admin || ! zc_wc_order_has_session( $order ) ) {
			return;
		}
		$text = (string) zc_opt( 'sp_delivery_session', '' );
		if ( '' === trim( $text ) ) {
			return;
		}
		if ( $plain_text ) {
			echo "\n" . esc_html__( 'هماهنگی زمان جلسه:', 'zarincoach' ) . ' ' . esc_html( $text ) . "\n";
		} else {
			echo '<p style="margin:0 0 16px;padding:12px 14px;border-radius:10px;background:#f5f7fb;"><strong>' . esc_html__( 'هماهنگی زمان جلسه:', 'zarincoach' ) . '</strong> ' . esc_html( $text ) . '</p>';
		}
	},
	5,
	3
);

/* =========================================================================
 * صفحات سبد/تسویه/حساب: بدون سربرگ صفحه‌ی بزرگ، با عنوان جمع‌وجور
 * ========================================================================= */

add_filter(
	'body_class',
	static function ( $classes ) {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			$classes[] = 'zc-wc-page';
			if ( is_checkout() && ! is_order_received_page() ) {
				$classes[] = 'zc-wc-checkout';
			}
		}
		if ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) {
			$classes[] = 'zc-shop-page';
		}
		return $classes;
	}
);

/*
 * تاریخ شمسی در قالب‌های ووکامرس (صفحه‌ی تشکر، سفارش‌های حساب کاربری، دانلودها).
 * فقط هنگام اجرای قالب‌های ووکامرس در پیشخوان کاربر فعال است تا تاریخ‌های دیگر وردپرس دست نخورند.
 */
add_action(
	'woocommerce_before_template_part',
	static function () {
		$GLOBALS['zc_wc_tpl_depth'] = ( isset( $GLOBALS['zc_wc_tpl_depth'] ) ? (int) $GLOBALS['zc_wc_tpl_depth'] : 0 ) + 1;
	}
);
add_action(
	'woocommerce_after_template_part',
	static function () {
		$GLOBALS['zc_wc_tpl_depth'] = max( 0, ( isset( $GLOBALS['zc_wc_tpl_depth'] ) ? (int) $GLOBALS['zc_wc_tpl_depth'] : 0 ) - 1 );
	}
);
add_filter(
	'date_i18n',
	static function ( $date, $format, $timestamp ) {
		if ( is_admin() || empty( $GLOBALS['zc_wc_tpl_depth'] ) || ! function_exists( 'zc_jalali_date' ) || ! function_exists( 'wc_date_format' ) || wc_date_format() !== $format ) {
			return $date;
		}
		$jalali = zc_jalali_date( (int) $timestamp );
		return '' !== $jalali ? $jalali : $date;
	},
	10,
	3
);

/*
 * لغو خودکار سفارش‌های کارت‌به‌کارت پرداخت‌نشده (تعهد برگه‌ی شرایط خرید: پیش‌فرض ۴۸ ساعت).
 * ووکامرس فقط سفارش‌های «در انتظار پرداخت» را لغو می‌کند، نه «در انتظار بررسی» روش BACS را.
 */
add_action(
	'init',
	static function () {
		if ( (int) zc_opt( 'shop_bacs_cancel_hours', 48 ) > 0 ) {
			if ( ! wp_next_scheduled( 'zc_wc_cancel_unpaid_bacs' ) ) {
				wp_schedule_event( time() + HOUR_IN_SECONDS, 'hourly', 'zc_wc_cancel_unpaid_bacs' );
			}
		} elseif ( wp_next_scheduled( 'zc_wc_cancel_unpaid_bacs' ) ) {
			wp_clear_scheduled_hook( 'zc_wc_cancel_unpaid_bacs' );
		}
	}
);

if ( ! function_exists( 'zc_wc_cancel_unpaid_bacs' ) ) :
	/**
	 * لغو سفارش‌های کارت‌به‌کارت قدیمی‌تر از مهلت تعیین‌شده.
	 *
	 * @return int تعداد سفارش‌های لغوشده.
	 */
	function zc_wc_cancel_unpaid_bacs() {
		$hours = (int) zc_opt( 'shop_bacs_cancel_hours', 48 );
		if ( $hours <= 0 || ! function_exists( 'wc_get_orders' ) ) {
			return 0;
		}
		$orders = wc_get_orders(
			array(
				'status'         => array( 'on-hold' ),
				'payment_method' => 'bacs',
				'date_created'   => '<' . ( time() - $hours * HOUR_IN_SECONDS ),
				'limit'          => 50,
				'return'         => 'objects',
			)
		);
		$count = 0;
		foreach ( $orders as $order ) {
			/* translators: %d: ساعت */
			$order->update_status( 'cancelled', sprintf( __( 'لغو خودکار: پرداخت کارت‌به‌کارت ظرف %d ساعت تأیید نشد.', 'zarincoach' ), $hours ) );
			$count++;
		}
		return $count;
	}
	add_action( 'zc_wc_cancel_unpaid_bacs', 'zc_wc_cancel_unpaid_bacs' );
endif;

// غیرفعال‌سازی قالب: رویداد زمان‌بندی‌شده پاک شود.
add_action(
	'switch_theme',
	static function () {
		wp_clear_scheduled_hook( 'zc_wc_cancel_unpaid_bacs' );
	}
);
