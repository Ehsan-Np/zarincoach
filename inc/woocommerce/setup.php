<?php
/**
 * ووکامرس — راه‌اندازی، دارایی‌ها، پوشش‌ها و تنظیمات پایه‌ی فروشگاه
 *
 * این ماژول فقط وقتی ووکامرس فعال است بارگذاری می‌شود (functions.php).
 * طراحی کامل فروشگاه در assets/css/shop.css است؛ استایل‌های پیش‌فرض ووکامرس غیرفعال می‌شوند
 * تا ظاهر فروشگاه دقیقاً با سیستم طراحی قالب (Tailwind + متغیرهای پالت) یکی باشد.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * ابزارها
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_context' ) ) :
	/**
	 * آیا صفحه‌ی جاری از صفحات فروشگاه است؟
	 *
	 * @return bool
	 */
	function zc_wc_context() {
		if ( ! function_exists( 'is_woocommerce' ) ) {
			return false;
		}
		/**
		 * صفحات دیگری که استایل کامل فروشگاه لازم دارند (مثلاً برگه‌های دارای ویجت محصولات).
		 */
		return (bool) apply_filters( 'zc_wc_context', is_woocommerce() || is_cart() || is_checkout() || is_account_page() );
	}
endif;

if ( ! function_exists( 'zc_wc_shop_url' ) ) :
	/**
	 * نشانی صفحه‌ی فروشگاه.
	 *
	 * @return string
	 */
	function zc_wc_shop_url() {
		$id = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
		return $id > 0 ? (string) get_permalink( $id ) : home_url( '/' );
	}
endif;

if ( ! function_exists( 'zc_wc_kind' ) ) :
	/**
	 * نوع تحویل محصول: digital (دانلودی/مجازی)، physical (ارسال پستی) یا session (جلسه‌ی کوچینگ).
	 *
	 * قابل تعیین دستی در «داده‌های محصول ← زرین‌کوچ»؛ در حالت خودکار از دسته‌ی «جلسات» یا نوع محصول تشخیص داده می‌شود.
	 *
	 * @param WC_Product|int $product محصول.
	 * @return string
	 */
	function zc_wc_kind( $product ) {
		$product = is_numeric( $product ) ? wc_get_product( $product ) : $product;
		if ( ! $product instanceof WC_Product ) {
			return 'physical';
		}
		$id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$saved = (string) get_post_meta( $id, '_zc_kind', true );
		if ( in_array( $saved, array( 'digital', 'physical', 'session' ), true ) ) {
			return $saved;
		}
		$session_cat = (string) zc_opt( 'shop_session_cat', 'coaching-sessions' );
		if ( '' !== $session_cat && has_term( $session_cat, 'product_cat', $id ) ) {
			return 'session';
		}
		if ( $product->is_downloadable() ) {
			return 'digital';
		}
		if ( $product->is_virtual() ) {
			return $product->is_type( 'variable' ) ? 'session' : 'digital';
		}
		if ( $product->is_type( 'variable' ) ) {
			$children = $product->get_children();
			if ( $children ) {
				$first = wc_get_product( $children[0] );
				if ( $first && $first->is_virtual() ) {
					return $first->is_downloadable() ? 'digital' : 'session';
				}
			}
		}
		return 'physical';
	}
endif;

if ( ! function_exists( 'zc_wc_kinds' ) ) :
	/**
	 * برچسب و آیکون انواع تحویل.
	 *
	 * @return array<string, array{label:string, icon:string, short:string}>
	 */
	function zc_wc_kinds() {
		return array(
			'digital'  => array(
				'label' => __( 'محصول دانلودی', 'zarincoach' ),
				'short' => __( 'دانلودی', 'zarincoach' ),
				'icon'  => 'download',
			),
			'physical' => array(
				'label' => __( 'ارسال پستی', 'zarincoach' ),
				'short' => __( 'فیزیکی', 'zarincoach' ),
				'icon'  => 'truck',
			),
			'session'  => array(
				'label' => __( 'جلسه‌ی کوچینگ', 'zarincoach' ),
				'short' => __( 'جلسه', 'zarincoach' ),
				'icon'  => 'video',
			),
		);
	}
endif;

if ( ! function_exists( 'zc_wc_discount_percent' ) ) :
	/**
	 * درصد تخفیف (برای محصول متغیر: بیشترین تخفیف میان گونه‌ها).
	 *
	 * @param WC_Product $product محصول.
	 * @return int
	 */
	function zc_wc_discount_percent( $product ) {
		if ( ! $product instanceof WC_Product || ! $product->is_on_sale() ) {
			return 0;
		}
		$max = 0;
		if ( $product->is_type( 'variable' ) ) {
			foreach ( $product->get_visible_children() as $child_id ) {
				$child = wc_get_product( $child_id );
				if ( ! $child || ! $child->is_on_sale() ) {
					continue;
				}
				$reg  = (float) $child->get_regular_price();
				$sale = (float) $child->get_sale_price();
				if ( $reg > 0 && $sale >= 0 && $sale < $reg ) {
					$max = max( $max, (int) round( ( ( $reg - $sale ) / $reg ) * 100 ) );
				}
			}
			return $max;
		}
		$reg  = (float) $product->get_regular_price();
		$sale = (float) $product->get_sale_price();
		if ( $reg > 0 && $sale < $reg ) {
			$max = (int) round( ( ( $reg - $sale ) / $reg ) * 100 );
		}
		return $max;
	}
endif;

if ( ! function_exists( 'zc_wc_is_new' ) ) :
	/**
	 * آیا محصول «تازه» است؟ (بر اساس تعداد روز تنظیم‌شده در پنل)
	 *
	 * @param WC_Product $product محصول.
	 * @return bool
	 */
	function zc_wc_is_new( $product ) {
		$days = (int) zc_opt( 'shop_new_days', 30 );
		if ( $days <= 0 || ! $product instanceof WC_Product ) {
			return false;
		}
		$created = $product->get_date_created();
		return $created && ( time() - $created->getTimestamp() ) < $days * DAY_IN_SECONDS;
	}
endif;

if ( ! function_exists( 'zc_wc_lines' ) ) :
	/**
	 * خطوط غیرخالی یک متن چندخطی.
	 *
	 * @param string $text متن.
	 * @return string[]
	 */
	function zc_wc_lines( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
	}
endif;

if ( ! function_exists( 'zc_wc_normalize_digits' ) ) :
	/**
	 * تبدیل ارقام فارسی/عربی به لاتین (کد پستی، تلفن و…).
	 *
	 * @param string $value مقدار.
	 * @return string
	 */
	function zc_wc_normalize_digits( $value ) {
		return strtr(
			(string) $value,
			array(
				'۰' => '0', '۱' => '1', '۲' => '2', '۳' => '3', '۴' => '4', '۵' => '5', '۶' => '6', '۷' => '7', '۸' => '8', '۹' => '9',
				'٠' => '0', '١' => '1', '٢' => '2', '٣' => '3', '٤' => '4', '٥' => '5', '٦' => '6', '٧' => '7', '٨' => '8', '٩' => '9',
			)
		);
	}
endif;

/* =========================================================================
 * پشتیبانی قالب
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_setup' ) ) :
	/**
	 * اعلام پشتیبانی از ووکامرس و گالری محصول.
	 *
	 * @return void
	 */
	function zc_wc_setup() {
		add_theme_support(
			'woocommerce',
			array(
				'thumbnail_image_width'         => 600,
				'gallery_thumbnail_image_width' => 180,
				'single_image_width'            => 960,
				'product_grid'                  => array(
					'default_rows'    => 4,
					'min_rows'        => 1,
					'default_columns' => 3,
					'min_columns'     => 2,
					'max_columns'     => 4,
				),
			)
		);
		add_theme_support( 'wc-product-gallery-zoom' );
		add_theme_support( 'wc-product-gallery-lightbox' );
		add_theme_support( 'wc-product-gallery-slider' );
	}
endif;
add_action( 'after_setup_theme', 'zc_wc_setup' );

// نسبت تصویر کارت محصول ۴:۵ (برش یکدست در شبکه).
add_filter(
	'woocommerce_get_image_size_thumbnail',
	static function ( $size ) {
		$size['width']  = 600;
		$size['height'] = 750;
		$size['crop']   = 1;
		return $size;
	}
);

/* =========================================================================
 * دارایی‌ها
 * ========================================================================= */

// استایل‌های پیش‌فرض ووکامرس غیرفعال؛ طراحی کامل در shop.css.
add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

if ( ! function_exists( 'zc_wc_enqueue' ) ) :
	/**
	 * بارگذاری استایل/اسکریپت فروشگاه.
	 *
	 * shop.css و shop.js فقط در صفحات فروشگاه و صفحاتی که ویجت فروشگاهی دارند بارگذاری می‌شوند؛
	 * سبد کشویی (کوچک) در همه‌ی صفحات فعال است و به همین دو فایل تکیه دارد.
	 *
	 * @return void
	 */
	function zc_wc_enqueue() {
		wp_register_style( 'zc-shop-core', ZC_URI . '/assets/css/shop-core.css', array( 'zc-main' ), zc_asset_version( '/assets/css/shop-core.css' ) );
		wp_register_style( 'zc-shop', ZC_URI . '/assets/css/shop.css', array( 'zc-shop-core' ), zc_asset_version( '/assets/css/shop.css' ) );

		$js = '/assets/js/shop.js';
		if ( ! ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) && is_readable( ZC_DIR . '/assets/js/shop.min.js' ) && filemtime( ZC_DIR . '/assets/js/shop.min.js' ) >= filemtime( ZC_DIR . $js ) ) {
			$js = '/assets/js/shop.min.js';
		}
		wp_register_script(
			'zc-shop',
			ZC_URI . $js,
			array( 'jquery' ),
			zc_asset_version( $js ),
			array(
				'in_footer' => true,
				'strategy'  => 'defer',
			)
		);
		wp_localize_script(
			'zc-shop',
			'ZarinShop',
			array(
				'cartUrl'      => wc_get_cart_url(),
				'checkoutUrl'  => wc_get_checkout_url(),
				'drawer'       => zc_wc_minicart_enabled() ? '1' : '0',
				'autoOpen'     => zc_switch( 'shop_minicart_auto_open', true ) ? '1' : '0',
				'pills'        => zc_switch( 'sp_variation_pills', true ) ? '1' : '0',
				'stickyBar'    => zc_switch( 'sp_sticky_bar', true ) ? '1' : '0',
				'i18n'         => array(
					'days'    => __( 'روز', 'zarincoach' ),
					'hours'   => __( 'ساعت', 'zarincoach' ),
					'minutes' => __( 'دقیقه', 'zarincoach' ),
					'seconds' => __( 'ثانیه', 'zarincoach' ),
					'copied'  => __( 'کپی شد', 'zarincoach' ),
					'added'   => __( 'به سبد خرید اضافه شد', 'zarincoach' ),
					'inc'     => __( 'افزایش تعداد', 'zarincoach' ),
					'dec'     => __( 'کاهش تعداد', 'zarincoach' ),
				),
			)
		);

		$context = zc_wc_context();
		if ( ! $context && ! zc_wc_minicart_enabled() && ! zc_switch( 'header_cart', true ) ) {
			return;
		}
		// هسته (شمارنده‌ی سبد و سبد کشویی) همه‌جا؛ استایل کامل فقط در صفحات فروشگاهی.
		wp_enqueue_style( $context ? 'zc-shop' : 'zc-shop-core' );
		wp_enqueue_script( 'zc-shop' );

		// شمارنده‌ی سبد در سربرگ و سبد کشویی به fragments ووکامرس نیاز دارند (از ووکامرس ۷.۸ به‌صورت پیش‌فرض بارگذاری نمی‌شود).
		if ( zc_wc_minicart_enabled() ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}
		if ( ! is_product() && 'yes' === get_option( 'woocommerce_enable_ajax_add_to_cart' ) ) {
			wp_enqueue_script( 'wc-add-to-cart' );
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_wc_enqueue', 25 );

/**
 * برگه‌هایی که ویجت فروشگاهی قالب یا ووکامرس المنتور دارند هم استایل کامل فروشگاه را در <head> می‌گیرند
 * (جلوگیری از بارگذاری دیرهنگام CSS و پرش ظاهر).
 */
add_filter(
	'zc_wc_context',
	static function ( $is ) {
		if ( $is || ! is_singular() ) {
			return $is;
		}
		$id = (int) get_queried_object_id();
		if ( $id <= 0 || ! function_exists( 'zc_page_uses_elementor' ) || ! zc_page_uses_elementor( $id ) ) {
			return $is;
		}
		$data = (string) get_post_meta( $id, '_elementor_data', true );
		return '' !== $data && (bool) preg_match( '/"widgetType":"(zc-products|zc-product-cats|zc-product-spotlight|zc-shop-promo|zc-shop-benefits|woocommerce-[a-z-]+|wc-[a-z-]+)"/', $data );
	}
);

if ( ! function_exists( 'zc_wc_minicart_enabled' ) ) :
	/**
	 * آیا سبد کشویی فعال است؟
	 *
	 * @return bool
	 */
	function zc_wc_minicart_enabled() {
		if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() ) ) {
			return false;
		}
		return zc_switch( 'shop_minicart', true );
	}
endif;

/* =========================================================================
 * پوشش صفحات (Wrapper) و حذف موارد پیش‌فرض
 * ========================================================================= */

remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
remove_action( 'woocommerce_sidebar', 'woocommerce_get_sidebar', 10 );

add_action(
	'woocommerce_before_main_content',
	static function () {
		echo '<div class="zc-shop-wrap">';
	},
	10
);
add_action(
	'woocommerce_after_main_content',
	static function () {
		echo '</div>';
	},
	10
);

/* =========================================================================
 * شبکه‌ی محصولات
 * ========================================================================= */

add_filter(
	'loop_shop_columns',
	static function () {
		return max( 2, min( 4, (int) zc_opt( 'shop_columns', 3 ) ) );
	},
	20
);

add_filter(
	'loop_shop_per_page',
	static function () {
		return max( 3, min( 60, (int) zc_opt( 'shop_per_page', 12 ) ) );
	},
	20
);

add_filter(
	'woocommerce_output_related_products_args',
	static function ( $args ) {
		$args['posts_per_page'] = max( 0, min( 8, (int) zc_opt( 'sp_related_count', 4 ) ) );
		$args['columns']        = 4;
		return $args;
	}
);

add_filter(
	'woocommerce_upsell_display_args',
	static function ( $args ) {
		$args['columns'] = 4;
		return $args;
	}
);

add_filter(
	'woocommerce_cross_sells_columns',
	static function () {
		return 4;
	}
);

// متن‌های کوتاه‌تر و فارسی روان‌تر.
add_filter(
	'woocommerce_product_add_to_cart_text',
	static function ( $text, $product ) {
		if ( $product instanceof WC_Product && $product->is_type( 'simple' ) && $product->is_purchasable() && $product->is_in_stock() ) {
			return __( 'افزودن به سبد', 'zarincoach' );
		}
		if ( $product instanceof WC_Product && $product->is_type( 'variable' ) ) {
			return __( 'انتخاب گزینه', 'zarincoach' );
		}
		return $text;
	},
	10,
	2
);

add_filter(
	'woocommerce_product_single_add_to_cart_text',
	static function ( $text, $product ) {
		if ( $product instanceof WC_Product && 'session' === zc_wc_kind( $product ) ) {
			return __( 'رزرو و پرداخت', 'zarincoach' );
		}
		return __( 'افزودن به سبد خرید', 'zarincoach' );
	},
	10,
	2
);

/* =========================================================================
 * مسیر راهنما: فروشگاه ← دسته‌ها ← محصول
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_primary_term' ) ) :
	/**
	 * دسته‌ی اصلی محصول (دسته‌ی اصلی یواست در صورت تعیین).
	 *
	 * @param int $product_id شناسه.
	 * @return WP_Term|null
	 */
	function zc_wc_primary_term( $product_id ) {
		$primary = (int) get_post_meta( $product_id, '_yoast_wpseo_primary_product_cat', true );
		if ( $primary ) {
			$term = get_term( $primary, 'product_cat' );
			if ( $term instanceof WP_Term ) {
				return $term;
			}
		}
		$terms = get_the_terms( $product_id, 'product_cat' );
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return null;
		}
		// عمیق‌ترین دسته (دقیق‌ترین مسیر).
		usort(
			$terms,
			static function ( $a, $b ) {
				return count( get_ancestors( $b->term_id, 'product_cat' ) ) <=> count( get_ancestors( $a->term_id, 'product_cat' ) );
			}
		);
		return $terms[0];
	}
endif;

add_filter(
	'zc_breadcrumb_items',
	static function ( $items ) {
		if ( ! function_exists( 'is_woocommerce' ) || count( $items ) < 1 ) {
			return $items;
		}
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id <= 0 ) {
			return $items;
		}
		$shop = array(
			'label' => get_the_title( $shop_id ),
			'url'   => get_permalink( $shop_id ),
		);
		$home = $items[0];

		if ( is_product() ) {
			$id    = get_queried_object_id();
			$trail = array( $home, $shop );
			$term  = zc_wc_primary_term( $id );
			if ( $term ) {
				foreach ( array_reverse( get_ancestors( $term->term_id, 'product_cat', 'taxonomy' ) ) as $parent_id ) {
					$parent = get_term( $parent_id, 'product_cat' );
					if ( $parent instanceof WP_Term ) {
						$trail[] = array( 'label' => $parent->name, 'url' => get_term_link( $parent ) );
					}
				}
				$trail[] = array( 'label' => $term->name, 'url' => get_term_link( $term ) );
			}
			$trail[] = array( 'label' => get_the_title( $id ), 'url' => '' );
			return $trail;
		}

		if ( is_product_taxonomy() ) {
			$term  = get_queried_object();
			$trail = array( $home, $shop );
			if ( $term instanceof WP_Term ) {
				foreach ( array_reverse( get_ancestors( $term->term_id, $term->taxonomy, 'taxonomy' ) ) as $parent_id ) {
					$parent = get_term( $parent_id, $term->taxonomy );
					if ( $parent instanceof WP_Term ) {
						$trail[] = array( 'label' => $parent->name, 'url' => get_term_link( $parent ) );
					}
				}
				$trail[] = array( 'label' => $term->name, 'url' => '' );
			}
			return $trail;
		}

		if ( is_shop() ) {
			return array( $home, array( 'label' => get_the_title( $shop_id ), 'url' => '' ) );
		}

		return $items;
	}
);

/* =========================================================================
 * پیش‌فرض‌های ایران
 * ========================================================================= */

// ترتیب و الزام فیلدهای نشانی برای ایران (نام، نام خانوادگی، استان، شهر، نشانی، کد پستی).
add_filter(
	'woocommerce_get_country_locale',
	static function ( $locale ) {
		$locale['IR'] = isset( $locale['IR'] ) ? $locale['IR'] : array();
		$locale['IR'] = array_merge(
			$locale['IR'],
			array(
				'state'     => array(
					'label'    => __( 'استان', 'zarincoach' ),
					'required' => true,
					'priority' => 50,
				),
				'city'      => array(
					'label'    => __( 'شهر', 'zarincoach' ),
					'priority' => 60,
				),
				'address_1' => array(
					'label'       => __( 'نشانی دقیق پستی', 'zarincoach' ),
					'placeholder' => __( 'خیابان، کوچه، پلاک، واحد', 'zarincoach' ),
					'priority'    => 70,
				),
				'address_2' => array(
					'hidden'   => true,
					'required' => false,
				),
				'postcode'  => array(
					'label'    => __( 'کد پستی ۱۰ رقمی', 'zarincoach' ),
					'priority' => 80,
				),
			)
		);
		return $locale;
	}
);

// واحد پول ریال/تومان: بدون اعشار.
add_filter(
	'wc_get_price_decimals',
	static function ( $decimals ) {
		return in_array( get_woocommerce_currency(), array( 'IRT', 'IRR', 'IRHT', 'IRHR' ), true ) ? 0 : $decimals;
	}
);
