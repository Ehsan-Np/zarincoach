<?php
/**
 * پیشخوان مشتری ووکامرس (حساب کاربری): داده‌ها، ابزارک‌ها و قلاب‌ها
 *
 * قالب‌های نمایشی در woocommerce/myaccount/*.php و استایل در assets/css/account.css
 * (فقط در صفحه‌ی حساب کاربری بارگذاری می‌شود).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * بارگذاری استایل
 * ========================================================================= */

add_action(
	'wp_enqueue_scripts',
	static function () {
		if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
			return;
		}
		wp_enqueue_style( 'zc-account', ZC_URI . '/assets/css/account.css', array( 'zc-shop' ), zc_asset_version( '/assets/css/account.css' ) );
	},
	30
);

/* =========================================================================
 * برچسب‌های منو
 * ========================================================================= */

add_filter(
	'woocommerce_account_menu_items',
	static function ( $items ) {
		$labels = array(
			'dashboard'       => __( 'پیشخوان', 'zarincoach' ),
			'orders'          => __( 'سفارش‌ها', 'zarincoach' ),
			'downloads'       => __( 'دانلودهای من', 'zarincoach' ),
			'edit-address'    => __( 'نشانی‌ها', 'zarincoach' ),
			'payment-methods' => __( 'روش‌های پرداخت', 'zarincoach' ),
			'edit-account'    => __( 'جزئیات حساب', 'zarincoach' ),
			'customer-logout' => __( 'خروج', 'zarincoach' ),
		);
		foreach ( $labels as $key => $label ) {
			if ( isset( $items[ $key ] ) ) {
				$items[ $key ] = $label;
			}
		}
		return $items;
	},
	5
);

/* =========================================================================
 * داده‌ها
 * ========================================================================= */

if ( ! function_exists( 'zc_acc_endpoint' ) ) :
	/**
	 * بخش جاری حساب کاربری.
	 *
	 * @return string
	 */
	function zc_acc_endpoint() {
		$ep = function_exists( 'WC' ) && WC()->query ? (string) WC()->query->get_current_endpoint() : '';
		return '' === $ep ? 'dashboard' : $ep;
	}
endif;

if ( ! function_exists( 'zc_acc_sections' ) ) :
	/**
	 * آیکن، عنوان و توضیح هر بخش حساب کاربری.
	 *
	 * @return array<string, array{icon:string,title:string,desc:string}>
	 */
	function zc_acc_sections() {
		$sections = array(
			'dashboard'          => array( 'grid', __( 'پیشخوان', 'zarincoach' ), __( 'نمای کلی سفارش‌ها، فایل‌ها و اطلاعات حساب شما.', 'zarincoach' ) ),
			'orders'             => array( 'bag', __( 'سفارش‌ها', 'zarincoach' ), __( 'تاریخچه‌ی خریدها، وضعیت پردازش و جزئیات هر سفارش.', 'zarincoach' ) ),
			'view-order'         => array( 'receipt', __( 'جزئیات سفارش', 'zarincoach' ), '' ),
			'downloads'          => array( 'download', __( 'دانلودهای من', 'zarincoach' ), __( 'کارپوشه‌ها، دوره‌های صوتی و فایل‌هایی که خریده‌اید؛ همیشه در دسترس.', 'zarincoach' ) ),
			'edit-address'       => array( 'map-pin', __( 'نشانی‌ها', 'zarincoach' ), __( 'این نشانی‌ها به‌صورت پیش‌فرض در تسویه‌حساب استفاده می‌شوند.', 'zarincoach' ) ),
			'payment-methods'    => array( 'credit-card', __( 'روش‌های پرداخت', 'zarincoach' ), __( 'روش‌های پرداخت ذخیره‌شده در حساب شما.', 'zarincoach' ) ),
			'add-payment-method' => array( 'credit-card', __( 'افزودن روش پرداخت', 'zarincoach' ), '' ),
			'edit-account'       => array( 'user', __( 'جزئیات حساب', 'zarincoach' ), __( 'نام، نشانی ایمیل و گذرواژه‌ی حساب کاربری خود را به‌روز کنید.', 'zarincoach' ) ),
			'customer-logout'    => array( 'logout', __( 'خروج', 'zarincoach' ), '' ),
		);

		$out = array();
		foreach ( $sections as $key => $s ) {
			$out[ $key ] = array(
				'icon'  => $s[0],
				'title' => $s[1],
				'desc'  => $s[2],
			);
		}

		/**
		 * فیلتر بخش‌های حساب کاربری (برای بخش‌های افزوده‌ی افزونه‌ها).
		 *
		 * @param array $out بخش‌ها.
		 */
		return (array) apply_filters( 'zc_account_sections', $out );
	}
endif;

if ( ! function_exists( 'zc_acc_stats' ) ) :
	/**
	 * آمار پیشخوان مشتری (یک بار در هر درخواست محاسبه می‌شود).
	 *
	 * @param int $user_id شناسه کاربر.
	 * @return array{orders:int,active:int,downloads:int,spent:float,sessions:int,recent:int[]}
	 */
	function zc_acc_stats( $user_id = 0 ) {
		static $cache = array();
		$user_id = $user_id ? (int) $user_id : get_current_user_id();
		if ( isset( $cache[ $user_id ] ) ) {
			return $cache[ $user_id ];
		}

		$stats = array(
			'orders'    => 0,
			'active'    => 0,
			'downloads' => 0,
			'spent'     => 0.0,
			'sessions'  => 0,
			'recent'    => array(),
		);
		if ( ! $user_id || ! function_exists( 'wc_get_orders' ) ) {
			$cache[ $user_id ] = $stats;
			return $stats;
		}

		$statuses = array_keys( wc_get_order_statuses() );
		$ids      = wc_get_orders(
			array(
				'customer_id' => $user_id,
				'status'      => $statuses,
				'limit'       => -1,
				'return'      => 'ids',
				'orderby'     => 'date',
				'order'       => 'DESC',
			)
		);
		$ids = is_array( $ids ) ? array_map( 'intval', $ids ) : array();

		$stats['orders'] = count( $ids );
		$stats['recent'] = array_slice( $ids, 0, 3 );

		// سفارش‌های باز و جلسه‌های خریداری‌شده (۲۰ سفارش اخیر کافی است).
		$paid = wc_get_is_paid_statuses();
		foreach ( array_slice( $ids, 0, 20 ) as $id ) {
			$order = wc_get_order( $id );
			if ( ! $order ) {
				continue;
			}
			if ( $order->has_status( array( 'pending', 'on-hold', 'processing' ) ) ) {
				++$stats['active'];
			}
			if ( $order->has_status( $paid ) && function_exists( 'zc_wc_kind' ) ) {
				foreach ( $order->get_items() as $item ) {
					$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
					if ( $product && 'session' === zc_wc_kind( $product->get_parent_id() ? $product->get_parent_id() : $product ) ) {
						$stats['sessions'] += (int) $item->get_quantity();
					}
				}
			}
		}

		$stats['downloads'] = count( (array) wc_get_customer_available_downloads( $user_id ) );
		$stats['spent']     = (float) wc_get_customer_total_spent( $user_id );

		/**
		 * فیلتر آمار پیشخوان مشتری.
		 *
		 * @param array $stats   آمار.
		 * @param int   $user_id کاربر.
		 */
		$cache[ $user_id ] = (array) apply_filters( 'zc_account_stats', $stats, $user_id );
		return $cache[ $user_id ];
	}
endif;

if ( ! function_exists( 'zc_acc_status_tone' ) ) :
	/**
	 * رنگ‌مایه‌ی وضعیت سفارش.
	 *
	 * @param string $status وضعیت بدون پیشوند wc-.
	 * @return string ok|info|warn|danger|muted
	 */
	function zc_acc_status_tone( $status ) {
		$map = array(
			'completed'      => 'ok',
			'processing'     => 'info',
			'on-hold'        => 'warn',
			'pending'        => 'warn',
			'checkout-draft' => 'muted',
			'cancelled'      => 'danger',
			'failed'         => 'danger',
			'refunded'       => 'muted',
		);
		return isset( $map[ $status ] ) ? $map[ $status ] : 'info';
	}
endif;

if ( ! function_exists( 'zc_acc_status_pill' ) ) :
	/**
	 * برچسب وضعیت سفارش.
	 *
	 * @param WC_Order $order سفارش.
	 * @return string
	 */
	function zc_acc_status_pill( $order ) {
		$status = $order->get_status();
		return '<span class="zc-ma-status is-' . esc_attr( zc_acc_status_tone( $status ) ) . '"><i aria-hidden="true"></i>' . esc_html( wc_get_order_status_name( $status ) ) . '</span>';
	}
endif;

if ( ! function_exists( 'zc_acc_avatar' ) ) :
	/**
	 * نمایه‌ی کاربر: حروف اول نام (بدون درخواست خارجی Gravatar؛ سریع و بدون فیلتر شدن).
	 *
	 * با فیلتر zc_account_gravatar = true از تصویر Gravatar استفاده می‌شود.
	 *
	 * @param WP_User $user کاربر.
	 * @param string  $class کلاس.
	 * @return string
	 */
	function zc_acc_avatar( $user, $class = 'zc-ma-avatar' ) {
		if ( apply_filters( 'zc_account_gravatar', false, $user ) ) {
			return get_avatar( $user->ID, 96, '', '', array( 'class' => $class ) );
		}
		$first = trim( (string) $user->first_name );
		$last  = trim( (string) $user->last_name );
		if ( '' !== $first || '' !== $last ) {
			$letters = mb_substr( '' !== $first ? $first : $last, 0, 1 ) . ( '' !== $first && '' !== $last ? '‌' . mb_substr( $last, 0, 1 ) : '' );
		} else {
			$letters = mb_substr( trim( (string) $user->display_name ), 0, 1 );
		}
		if ( '' === $letters ) {
			return '<span class="' . esc_attr( $class ) . '" aria-hidden="true">' . zc_icon( 'user', 'zc-ma-avatar__ico', false ) . '</span>';
		}
		return '<span class="' . esc_attr( $class ) . '" aria-hidden="true">' . esc_html( $letters ) . '</span>';
	}
endif;

if ( ! function_exists( 'zc_acc_first_name' ) ) :
	/**
	 * نام کوچک یا نام نمایشی کاربر.
	 *
	 * @param WP_User $user کاربر.
	 * @return string
	 */
	function zc_acc_first_name( $user ) {
		$name = trim( (string) $user->first_name );
		return '' !== $name ? $name : (string) $user->display_name;
	}
endif;

if ( ! function_exists( 'zc_acc_order_thumbs' ) ) :
	/**
	 * تصاویر کوچک اقلام سفارش.
	 *
	 * @param WC_Order $order سفارش.
	 * @param int      $max   بیشینه.
	 * @return string
	 */
	function zc_acc_order_thumbs( $order, $max = 3 ) {
		$items = $order->get_items();
		$html  = '';
		$i     = 0;
		foreach ( $items as $item ) {
			if ( $i >= $max ) {
				break;
			}
			$product = is_callable( array( $item, 'get_product' ) ) ? $item->get_product() : null;
			$img_id  = $product ? (int) $product->get_image_id() : 0;
			if ( ! $img_id && $product && $product->get_parent_id() ) {
				$img_id = (int) get_post_thumbnail_id( $product->get_parent_id() );
			}
			$img   = $img_id ? wp_get_attachment_image( $img_id, 'thumbnail', false, array( 'alt' => '', 'loading' => 'lazy', 'decoding' => 'async' ) ) : zc_icon( 'package', 'zc-ma-thumb__ico', false );
			$html .= '<span class="zc-ma-thumb" title="' . esc_attr( $item->get_name() ) . '">' . $img . '</span>';
			++$i;
		}
		$more = count( $items ) - $i;
		if ( $more > 0 ) {
			$html .= '<span class="zc-ma-thumb zc-ma-thumb--more">+' . esc_html( number_format_i18n( $more ) ) . '</span>';
		}
		return '<span class="zc-ma-thumbs">' . $html . '</span>';
	}
endif;

if ( ! function_exists( 'zc_acc_order_summary' ) ) :
	/**
	 * خلاصه‌ی متنی اقلام: «نام نخستین قلم و n مورد دیگر».
	 *
	 * @param WC_Order $order سفارش.
	 * @return string
	 */
	function zc_acc_order_summary( $order ) {
		$items = array_values( $order->get_items() );
		if ( empty( $items ) ) {
			return '';
		}
		$first = $items[0]->get_name();
		$rest  = count( $items ) - 1;
		if ( $rest < 1 ) {
			return $first;
		}
		/* translators: 1: نام محصول 2: تعداد */
		return sprintf( __( '%1$s و %2$s مورد دیگر', 'zarincoach' ), $first, number_format_i18n( $rest ) );
	}
endif;

if ( ! function_exists( 'zc_acc_order_steps' ) ) :
	/**
	 * مراحل پیشرفت سفارش برای نوار وضعیت.
	 *
	 * @param WC_Order $order سفارش.
	 * @return array{state:string,current:int,steps:array<int,array{label:string,icon:string}>}
	 */
	function zc_acc_order_steps( $order ) {
		$needs_ship = $order->needs_shipping_address();
		$steps      = array(
			array(
				'label' => __( 'ثبت سفارش', 'zarincoach' ),
				'icon'  => 'receipt',
			),
			array(
				'label' => __( 'تأیید پرداخت', 'zarincoach' ),
				'icon'  => 'wallet',
			),
			array(
				'label' => $needs_ship ? __( 'آماده‌سازی و ارسال', 'zarincoach' ) : __( 'آماده‌سازی', 'zarincoach' ),
				'icon'  => $needs_ship ? 'truck' : 'package',
			),
			array(
				'label' => __( 'تکمیل', 'zarincoach' ),
				'icon'  => 'check',
			),
		);

		$status = $order->get_status();
		$map    = array(
			'pending'    => 0,
			'on-hold'    => 1,
			'processing' => 2,
			'completed'  => 3,
		);
		$state  = in_array( $status, array( 'cancelled', 'failed', 'refunded' ), true ) ? 'stopped' : 'active';

		return array(
			'state'   => $state,
			'current' => isset( $map[ $status ] ) ? $map[ $status ] : 0,
			'steps'   => $steps,
		);
	}
endif;

if ( ! function_exists( 'zc_acc_empty' ) ) :
	/**
	 * حالت خالی (بدون سفارش/دانلود/…).
	 *
	 * @param string $icon  آیکن.
	 * @param string $title عنوان.
	 * @param string $text  متن.
	 * @param string $cta   متن دکمه.
	 * @param string $url   پیوند دکمه.
	 * @return void
	 */
	function zc_acc_empty( $icon, $title, $text, $cta = '', $url = '' ) {
		echo '<div class="zc-ma-empty">';
		echo '<span class="zc-ma-empty__ico">' . zc_icon( $icon, 'h-7 w-7', false ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<strong>' . esc_html( $title ) . '</strong>';
		if ( '' !== $text ) {
			echo '<p>' . esc_html( $text ) . '</p>';
		}
		if ( '' !== $cta && '' !== $url ) {
			echo '<a class="zc-btn zc-btn-primary zc-btn-sm" href="' . esc_url( $url ) . '">' . esc_html( $cta ) . zc_icon( 'arrow-left', 'h-4 w-4', false ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_acc_shop_url' ) ) :
	/**
	 * پیوند بازگشت به فروشگاه.
	 *
	 * @return string
	 */
	function zc_acc_shop_url() {
		return (string) apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) );
	}
endif;

if ( ! function_exists( 'zc_acc_booking_url' ) ) :
	/**
	 * پیوند برگه‌ی رزرو نوبت (در نبود برگه، برگه‌ی تماس یا خانه).
	 *
	 * @return string
	 */
	function zc_acc_booking_url() {
		$url = function_exists( 'zc_page_url_by_key' ) ? (string) zc_page_url_by_key( 'booking' ) : '';
		if ( '' === $url && function_exists( 'zc_page_url_by_key' ) ) {
			$url = (string) zc_page_url_by_key( 'contact' );
		}
		return '' !== $url ? $url : home_url( '/' );
	}
endif;

if ( ! function_exists( 'zc_acc_content_head' ) ) :
	/**
	 * سرعنوان بخش جاری در ستون محتوا.
	 *
	 * @param string $endpoint بخش جاری.
	 * @return void
	 */
	function zc_acc_content_head( $endpoint ) {
		if ( 'dashboard' === $endpoint ) {
			return;
		}
		$sections = zc_acc_sections();
		$s        = isset( $sections[ $endpoint ] ) ? $sections[ $endpoint ] : array(
			'icon'  => 'sparkles',
			'title' => (string) WC()->query->get_endpoint_title( $endpoint ),
			'desc'  => '',
		);
		$title    = $s['title'];
		$desc     = $s['desc'];
		$back     = '';
		$extra    = '';
		$value    = get_query_var( $endpoint );

		if ( 'view-order' === $endpoint ) {
			$order = wc_get_order( absint( $value ) );
			if ( $order && (int) $order->get_customer_id() === get_current_user_id() ) {
				/* translators: %s: شماره سفارش */
				$title = sprintf( __( 'سفارش #%s', 'zarincoach' ), $order->get_order_number() );
				/* translators: %s: تاریخ */
				$desc  = sprintf( __( 'ثبت‌شده در %s', 'zarincoach' ), wc_format_datetime( $order->get_date_created() ) );
				$extra = zc_acc_status_pill( $order );
			}
			$back = wc_get_account_endpoint_url( 'orders' );
		} elseif ( 'edit-address' === $endpoint && '' !== (string) $value ) {
			$title = 'shipping' === $value ? __( 'ویرایش نشانی ارسال', 'zarincoach' ) : __( 'ویرایش نشانی صورتحساب', 'zarincoach' );
			$desc  = __( 'تغییرات در سفارش‌های بعدی اعمال می‌شود.', 'zarincoach' );
			$back  = wc_get_account_endpoint_url( 'edit-address' );
		} elseif ( 'add-payment-method' === $endpoint ) {
			$back = wc_get_account_endpoint_url( 'payment-methods' );
		}

		echo '<header class="zc-ma-head">';
		echo '<span class="zc-ma-head__ico">' . zc_icon( $s['icon'], 'h-5 w-5', false ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<div class="zc-ma-head__text"><h2>' . esc_html( $title ) . '</h2>';
		if ( '' !== $desc ) {
			echo '<p>' . esc_html( $desc ) . '</p>';
		}
		echo '</div>';
		if ( '' !== $extra || '' !== $back ) {
			echo '<div class="zc-ma-head__aside">';
			echo $extra; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی ایمن zc_acc_status_pill.
			if ( '' !== $back ) {
				echo '<a class="zc-ma-back" href="' . esc_url( $back ) . '">' . esc_html__( 'بازگشت', 'zarincoach' ) . zc_icon( 'arrow-left', 'h-4 w-4', false ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</div>';
		}
		echo '</header>';
	}
endif;

if ( ! function_exists( 'zc_acc_help_box' ) ) :
	/**
	 * کادر پشتیبانی (ستون کناری و پیشخوان).
	 *
	 * @param string $class کلاس اضافه.
	 * @return void
	 */
	function zc_acc_help_box( $class = '' ) {
		$c     = function_exists( 'zc_contact_fields' ) ? zc_contact_fields() : array();
		$phone = ! empty( $c['phone'] ) ? $c['phone'] : ( ! empty( $c['phone2'] ) ? $c['phone2'] : '' );
		$hours = ! empty( $c['hours'] ) ? $c['hours'] : '';
		echo '<div class="zc-ma-help ' . esc_attr( $class ) . '">';
		echo '<span class="zc-ma-help__ico">' . zc_icon( 'headphones', 'h-5 w-5', false ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		echo '<strong>' . esc_html__( 'نیاز به راهنمایی دارید؟', 'zarincoach' ) . '</strong>';
		echo '<p>' . esc_html__( 'برای پیگیری سفارش، دریافت فایل یا هماهنگی جلسه با ما در تماس باشید.', 'zarincoach' ) . '</p>';
		if ( '' !== $phone ) {
			echo '<a class="zc-ma-help__tel" href="tel:' . esc_attr( zc_normalize_phone( $phone ) ) . '">' . zc_icon( 'phone', 'h-4 w-4', false ) . '<span dir="ltr">' . esc_html( zc_digits_to_persian( $phone ) ) . '</span></a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( '' !== $hours ) {
			echo '<small>' . esc_html( $hours ) . '</small>';
		}
		echo '</div>';
	}
endif;

/*
 * تعداد روزهای عضویت/تاریخ عضویت به شمسی.
 */
if ( ! function_exists( 'zc_acc_member_since' ) ) :
	/**
	 * تاریخ عضویت کاربر.
	 *
	 * @param WP_User $user کاربر.
	 * @return string
	 */
	function zc_acc_member_since( $user ) {
		$ts = strtotime( (string) $user->user_registered . ' UTC' );
		if ( ! $ts ) {
			return '';
		}
		$date = function_exists( 'zc_jalali_date' ) ? zc_jalali_date( $ts ) : '';
		return '' !== $date ? $date : date_i18n( get_option( 'date_format' ), $ts );
	}
endif;
