<?php
/**
 * سئو و داده‌های ساختاریافته‌ی فروشگاه
 *
 * - گره‌ی Product / Book / Service برای هر محصول، با قیمت ریالی (IRR) — تومان در استاندارد ISO 4217 وجود ندارد.
 * - Offer / AggregateOffer، موجودی، اعتبار قیمت، فروشنده، هزینه و زمان ارسال، سیاست مرجوعی.
 * - امتیاز و نقدها فقط از دیدگاه‌های واقعی و تأییدشده‌ی خریداران.
 * - ItemList برای برگه‌ی فروشگاه و دسته‌ها.
 * - noindex برای نشانی‌های فیلتر/مرتب‌سازی (جلوگیری از محتوای تکراری).
 * - اوپن‌گراف product:* در حالت سئوی داخلی.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * داده‌ی ساختاریافته‌ی خود ووکامرس
 * ========================================================================= */

/**
 * آیا داده‌ی ساختاریافته‌ی محصول را قالب تولید می‌کند؟
 * اگر افزونه‌ی Yoast WooCommerce SEO نصب باشد، محصول به آن سپرده می‌شود.
 *
 * @return bool
 */
function zc_wc_schema_owner() {
	$own = zc_schema_enabled() && ! defined( 'WPSEO_WOO_VERSION' );
	/**
	 * فیلتر مالکیت اسکیمای محصول.
	 *
	 * @param bool $own قالب تولید کند؟
	 */
	return (bool) apply_filters( 'zc_wc_schema_owner', $own );
}

// JSON-LD پیش‌فرض ووکامرس با واحد IRT نامعتبر است و با گراف قالب تکرار می‌شود؛ حذف.
// نکته: آرایه‌ی خالی در ووکامرس یعنی «همه‌ی نوع‌ها»؛ پس یک نوع ناموجود برمی‌گردانیم تا خروجی خالی شود.
// (داده‌ی ساختاریافته‌ی ایمیل سفارش دست نمی‌خورد.)
add_filter(
	'woocommerce_structured_data_type_for_page',
	static function ( $types ) {
		if ( did_action( 'woocommerce_email_order_details' ) ) {
			return $types;
		}
		return zc_wc_schema_owner() ? array( 'zc-theme-graph' ) : $types;
	},
	99
);

/* =========================================================================
 * ابزارها
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_irr' ) ) :
	/**
	 * مبلغ به ریال (عدد صحیح، رشته) — ووکامرس به تومان (IRT) یا ریال (IRR).
	 *
	 * @param float|string $amount مبلغ به واحد فروشگاه.
	 * @return string
	 */
	function zc_wc_irr( $amount ) {
		$amount = (float) $amount;
		$factor = 'IRT' === get_woocommerce_currency() ? 10 : 1;
		return (string) (int) round( $amount * $factor );
	}
endif;

if ( ! function_exists( 'zc_wc_schema_currency' ) ) :
	/**
	 * واحد پول اسکیما: IRT و IRR هر دو به IRR؛ بقیه همان کد ISO فروشگاه.
	 *
	 * @return string
	 */
	function zc_wc_schema_currency() {
		$cur = get_woocommerce_currency();
		return in_array( $cur, array( 'IRT', 'IRR', 'IRHT', 'IRHR' ), true ) ? 'IRR' : $cur;
	}
endif;

if ( ! function_exists( 'zc_wc_schema_price' ) ) :
	/**
	 * مبلغ اسکیما (با احتساب مالیات طبق تنظیم نمایش فروشگاه).
	 *
	 * @param WC_Product $product محصول.
	 * @param float      $price   مبلغ خام.
	 * @return string
	 */
	function zc_wc_schema_price( $product, $price ) {
		$price = (float) wc_get_price_to_display( $product, array( 'price' => $price ) );
		if ( 'IRR' === zc_wc_schema_currency() ) {
			$cur = get_woocommerce_currency();
			return (string) (int) round( in_array( $cur, array( 'IRT', 'IRHT' ), true ) ? $price * 10 : $price );
		}
		return wc_format_decimal( $price, wc_get_price_decimals() );
	}
endif;

if ( ! function_exists( 'zc_wc_schema_availability' ) ) :
	/**
	 * وضعیت موجودی ← schema.org.
	 *
	 * @param WC_Product $product محصول.
	 * @return string
	 */
	function zc_wc_schema_availability( $product ) {
		if ( ! $product->is_in_stock() ) {
			return 'https://schema.org/OutOfStock';
		}
		if ( $product->is_on_backorder() ) {
			return 'https://schema.org/BackOrder';
		}
		if ( 'session' === zc_wc_kind( $product ) || $product->is_virtual() || $product->is_downloadable() ) {
			return $product->managing_stock() && $product->get_stock_quantity() <= 3 ? 'https://schema.org/LimitedAvailability' : 'https://schema.org/InStock';
		}
		return 'https://schema.org/InStock';
	}
endif;

if ( ! function_exists( 'zc_wc_schema_images' ) ) :
	/**
	 * نشانی تصاویر محصول (اصلی + گالری، حداکثر ۶) — فایل اصلی پیش از تبدیل WebP.
	 *
	 * @param WC_Product $product محصول.
	 * @return string[]
	 */
	function zc_wc_schema_images( $product ) {
		$ids = array_filter( array_merge( array( (int) $product->get_image_id() ), array_map( 'intval', $product->get_gallery_image_ids() ) ) );
		$out = array();
		foreach ( array_slice( array_unique( $ids ), 0, 6 ) as $id ) {
			$src = function_exists( 'wp_get_original_image_url' ) ? wp_get_original_image_url( $id ) : '';
			$src = $src ? $src : wp_get_attachment_url( $id );
			if ( $src ) {
				$out[] = $src;
			}
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_wc_schema_return_policy' ) ) :
	/**
	 * سیاست مرجوعی (MerchantReturnPolicy).
	 * - فیزیکی: ۷ روز مهلت انصراف (ماده‌ی ۳۷ قانون تجارت الکترونیکی)، ارسال پستی.
	 * - دانلودی/جلسه: پس از تحویل یا آغاز خدمت با رضایت مصرف‌کننده، مستثنا (ماده‌ی ۳۸).
	 *
	 * @param WC_Product $product محصول.
	 * @return array
	 */
	function zc_wc_schema_return_policy( $product ) {
		$kind   = zc_wc_kind( $product );
		$days   = max( 0, (int) zc_opt( 'seo_return_days', 7 ) );
		$policy = array(
			'@type'               => 'MerchantReturnPolicy',
			'applicableCountry'   => 'IR',
			'merchantReturnLink'  => function_exists( 'zc_page_url_by_key' ) ? (string) zc_page_url_by_key( 'shipping' ) : '',
		);
		if ( 'physical' === $kind && $days > 0 ) {
			$policy += array(
				'returnPolicyCategory' => 'https://schema.org/MerchantReturnFiniteReturnWindow',
				'merchantReturnDays'   => $days,
				'returnMethod'         => 'https://schema.org/ReturnByMail',
				'returnFees'           => 'https://schema.org/ReturnFeesCustomerResponsibility',
			);
		} else {
			$policy['returnPolicyCategory'] = 'https://schema.org/MerchantReturnNotPermitted';
		}
		/**
		 * فیلتر سیاست مرجوعی اسکیما.
		 *
		 * @param array      $policy  گره.
		 * @param WC_Product $product محصول.
		 */
		return array_filter( (array) apply_filters( 'zc_wc_schema_return_policy', $policy, $product ) );
	}
endif;

if ( ! function_exists( 'zc_wc_schema_shipping' ) ) :
	/**
	 * جزئیات ارسال (OfferShippingDetails) — فقط کالای فیزیکی و فقط اگر هزینه‌ی ارسال در پنل تعیین شده باشد.
	 *
	 * @param WC_Product $product محصول.
	 * @return array
	 */
	function zc_wc_schema_shipping( $product ) {
		if ( 'physical' !== zc_wc_kind( $product ) || $product->is_virtual() ) {
			return array();
		}
		$rate = trim( zc_wc_normalize_digits( (string) zc_opt( 'seo_ship_rate', '' ) ) );
		if ( '' === $rate || ! is_numeric( str_replace( ',', '', $rate ) ) ) {
			return array();
		}
		$rate = (float) str_replace( ',', '', $rate );
		$free = (float) str_replace( ',', '', zc_wc_normalize_digits( (string) zc_opt( 'shop_free_shipping', '' ) ) );
		if ( $free > 0 && (float) $product->get_price() >= $free ) {
			$rate = 0.0;
		}
		$handling = array_map( 'intval', explode( '-', zc_wc_normalize_digits( (string) zc_opt( 'seo_ship_handling', '0-2' ) ) ) + array( 0, 2 ) );
		$transit  = array_map( 'intval', explode( '-', zc_wc_normalize_digits( (string) zc_opt( 'seo_ship_transit', '2-5' ) ) ) + array( 2, 5 ) );
		$currency = zc_wc_schema_currency();
		return array(
			'@type'               => 'OfferShippingDetails',
			'shippingRate'        => array(
				'@type'    => 'MonetaryAmount',
				'value'    => 'IRR' === $currency ? zc_wc_irr( $rate ) : wc_format_decimal( $rate, wc_get_price_decimals() ),
				'currency' => $currency,
			),
			'shippingDestination' => array(
				'@type'          => 'DefinedRegion',
				'addressCountry' => 'IR',
			),
			'deliveryTime'        => array(
				'@type'        => 'ShippingDeliveryTime',
				'handlingTime' => array(
					'@type'    => 'QuantitativeValue',
					'minValue' => min( $handling[0], $handling[1] ),
					'maxValue' => max( $handling[0], $handling[1] ),
					'unitCode' => 'DAY',
				),
				'transitTime'  => array(
					'@type'    => 'QuantitativeValue',
					'minValue' => min( $transit[0], $transit[1] ),
					'maxValue' => max( $transit[0], $transit[1] ),
					'unitCode' => 'DAY',
				),
			),
		);
	}
endif;

if ( ! function_exists( 'zc_wc_schema_offer' ) ) :
	/**
	 * Offer یا AggregateOffer محصول.
	 *
	 * @param WC_Product $product محصول.
	 * @param string     $url     نشانی.
	 * @return array
	 */
	function zc_wc_schema_offer( $product, $url ) {
		if ( '' === (string) $product->get_price() ) {
			return array();
		}
		$currency = zc_wc_schema_currency();
		$seller   = array( '@id' => zc_schema_id( 'person' ) );
		$end      = function_exists( 'zc_wc_sale_end' ) ? (int) zc_wc_sale_end( $product ) : 0;
		// اعتبار قیمت: پایان حراج؛ در غیر این صورت پایان سال میلادی بعد (توصیه‌ی گوگل برای جلوگیری از هشدار).
		$valid    = $end ? wp_date( 'Y-m-d', $end ) : gmdate( 'Y-12-31', strtotime( '+1 year' ) );

		if ( $product->is_type( 'variable' ) ) {
			$prices = $product->get_variation_prices( true );
			$list   = array_filter( array_map( 'floatval', (array) $prices['price'] ) );
			if ( ! $list ) {
				return array();
			}
			$low  = min( $list );
			$high = max( $list );
			$to   = static function ( $v ) use ( $currency ) {
				return 'IRR' === $currency ? zc_wc_irr( $v ) : wc_format_decimal( $v, wc_get_price_decimals() );
			};
			$offer = array(
				'@type'         => 'AggregateOffer',
				'lowPrice'      => $to( $low ),
				'highPrice'     => $to( $high ),
				'offerCount'    => count( $list ),
				'priceCurrency' => $currency,
			);
		} else {
			$offer = array(
				'@type'              => 'Offer',
				'price'              => zc_wc_schema_price( $product, $product->get_price() ),
				'priceCurrency'      => $currency,
				'priceValidUntil'    => $valid,
			);
			if ( $product->is_on_sale() && '' !== (string) $product->get_regular_price() ) {
				// قیمت پیش از تخفیف (Strikethrough) مطابق مستند گوگل.
				$offer['priceSpecification'] = array(
					'@type'         => 'UnitPriceSpecification',
					'priceType'     => 'https://schema.org/StrikethroughPrice',
					'price'         => zc_wc_schema_price( $product, $product->get_regular_price() ),
					'priceCurrency' => $currency,
				);
			}
		}

		$offer += array(
			'availability'  => zc_wc_schema_availability( $product ),
			'itemCondition' => 'https://schema.org/NewCondition',
			'url'           => $url,
			'seller'        => $seller,
		);
		if ( 'session' !== zc_wc_kind( $product ) ) {
			$offer['hasMerchantReturnPolicy'] = zc_wc_schema_return_policy( $product );
			$ship                             = zc_wc_schema_shipping( $product );
			if ( $ship ) {
				$offer['shippingDetails'] = $ship;
			}
		}
		/**
		 * فیلتر پیشنهاد (Offer) اسکیما.
		 *
		 * @param array      $offer   گره.
		 * @param WC_Product $product محصول.
		 */
		return array_filter( (array) apply_filters( 'zc_wc_schema_offer', $offer, $product ) );
	}
endif;

if ( ! function_exists( 'zc_wc_schema_reviews' ) ) :
	/**
	 * امتیاز و نقدها فقط از دیدگاه‌های واقعی تأییدشده (بدون جعل/پیش‌فرض).
	 *
	 * @param WC_Product $product محصول.
	 * @return array{rating:array, reviews:array}
	 */
	function zc_wc_schema_reviews( $product ) {
		$out = array(
			'rating'  => array(),
			'reviews' => array(),
		);
		if ( ! wc_review_ratings_enabled() || ! $product->get_reviews_allowed() ) {
			return $out;
		}
		$count = (int) $product->get_review_count();
		if ( $count > 0 && (float) $product->get_average_rating() > 0 ) {
			$out['rating'] = array(
				'@type'       => 'AggregateRating',
				'ratingValue' => wc_format_decimal( $product->get_average_rating(), 1 ),
				'reviewCount' => $count,
				'bestRating'  => '5',
				'worstRating' => '1',
			);
		}
		$comments = get_comments(
			array(
				'post_id' => $product->get_id(),
				'status'  => 'approve',
				'type'    => 'review',
				'parent'  => 0,
				'number'  => 5,
				'orderby' => 'comment_date_gmt',
				'order'   => 'DESC',
			)
		);
		foreach ( $comments as $c ) {
			$rating = (int) get_comment_meta( $c->comment_ID, 'rating', true );
			$body   = zc_seo_clean( $c->comment_content, 500 );
			if ( $rating < 1 || '' === $body ) {
				continue;
			}
			$out['reviews'][] = array(
				'@type'         => 'Review',
				'author'        => array(
					'@type' => 'Person',
					'name'  => zc_seo_clean( get_comment_author( $c ) ),
				),
				'datePublished' => mysql2date( 'c', $c->comment_date_gmt ),
				'reviewBody'    => $body,
				'reviewRating'  => array(
					'@type'       => 'Rating',
					'ratingValue' => (string) $rating,
					'bestRating'  => '5',
					'worstRating' => '1',
				),
			);
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_wc_schema_type' ) ) :
	/**
	 * نوع اسکیمای محصول: تنظیم دستی ← جلسه = Service ← Product.
	 *
	 * @param WC_Product $product محصول.
	 * @return string Product | Book | Service
	 */
	function zc_wc_schema_type( $product ) {
		$id    = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();
		$saved = (string) get_post_meta( $id, '_zc_schema_type', true );
		if ( in_array( $saved, array( 'Product', 'Book', 'Service' ), true ) ) {
			return $saved;
		}
		return 'session' === zc_wc_kind( $product ) ? 'Service' : 'Product';
	}
endif;

if ( ! function_exists( 'zc_schema_product_node' ) ) :
	/**
	 * گره‌ی کامل محصول.
	 *
	 * @param WC_Product $product محصول.
	 * @param string     $url     نشانی کانونیکال.
	 * @return array
	 */
	function zc_schema_product_node( $product, $url ) {
		$type    = zc_wc_schema_type( $product );
		$id      = $product->get_id();
		$desc    = zc_seo_clean( $product->get_short_description(), 5000 );
		$desc    = '' !== $desc ? $desc : zc_seo_clean( $product->get_description(), 5000 );
		$term    = function_exists( 'zc_wc_primary_term' ) ? zc_wc_primary_term( $id ) : null;
		$reviews = zc_wc_schema_reviews( $product );
		$offer   = zc_wc_schema_offer( $product, $url );
		$node_id = $url . '#' . strtolower( $type );

		$node = array(
			'@type'            => 'Book' === $type ? array( 'Book', 'Product' ) : $type,
			'@id'              => $node_id,
			'name'             => zc_seo_clean( $product->get_name() ),
			'url'              => $url,
			'description'      => $desc,
			'image'            => zc_wc_schema_images( $product ),
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
			'inLanguage'       => 'fa-IR',
		);

		if ( 'Service' === $type ) {
			$node += array(
				'serviceType' => $term ? zc_seo_clean( $term->name ) : __( 'جلسه‌ی کوچینگ', 'zarincoach' ),
				'provider'    => array( '@id' => zc_schema_id( 'person' ) ),
				'areaServed'  => array(
					'@type' => 'Country',
					'name'  => 'IR',
				),
				'availableChannel' => array(
					'@type'      => 'ServiceChannel',
					'serviceUrl' => $url,
				),
			);
			unset( $node['inLanguage'] );
		} else {
			$sku = (string) $product->get_sku();
			$node += array(
				'sku'      => '' !== $sku ? $sku : (string) $id,
				'category' => $term ? zc_seo_clean( $term->name ) : '',
				'brand'    => array(
					'@type' => 'Brand',
					'name'  => zc_seo_person_name(),
				),
			);
			$gtin = trim( (string) get_post_meta( $id, '_global_unique_id', true ) ); // ووکامرس ۹.۲+ (GTIN/ISBN).
			if ( '' !== $gtin && preg_match( '/^\d{8,14}$/', $gtin ) ) {
				$node['gtin'] = $gtin;
			}
			if ( $product->has_weight() ) {
				$node['weight'] = array(
					'@type'    => 'QuantitativeValue',
					'value'    => wc_format_decimal( $product->get_weight() ),
					'unitCode' => 'kg' === get_option( 'woocommerce_weight_unit' ) ? 'KGM' : 'GRM',
				);
			}
			if ( 'Book' === $type ) {
				$node['author']     = array( '@id' => zc_schema_id( 'person' ) );
				$node['bookFormat'] = 'digital' === zc_wc_kind( $product ) ? 'https://schema.org/EBook' : 'https://schema.org/Paperback';
				if ( '' !== $gtin && preg_match( '/^(97[89])?\d{9}[\dX]$/', $gtin ) ) {
					$node['isbn'] = $gtin;
				}
			}
		}

		if ( $offer ) {
			$node['offers'] = $offer;
		}
		if ( $reviews['rating'] ) {
			$node['aggregateRating'] = $reviews['rating'];
		}
		if ( $reviews['reviews'] ) {
			$node['review'] = $reviews['reviews'];
		}

		/**
		 * فیلتر گره‌ی محصول.
		 *
		 * @param array      $node    گره.
		 * @param WC_Product $product محصول.
		 */
		return array_filter( (array) apply_filters( 'zc_schema_product_node', $node, $product ) );
	}
endif;

/* =========================================================================
 * اتصال به گراف
 * ========================================================================= */

add_filter(
	'zc_schema_context',
	static function ( $ctx ) {
		if ( ! zc_wc_schema_owner() ) {
			return $ctx;
		}
		$url = (string) $ctx['url'];

		// برگه‌ی محصول.
		if ( is_product() ) {
			$product = wc_get_product( (int) $ctx['pid'] );
			if ( ! $product instanceof WC_Product ) {
				return $ctx;
			}
			$node                         = zc_schema_product_node( $product, $url );
			$ctx['graph'][ $node['@id'] ] = $node;

			$type = array_values( array_diff( (array) $ctx['page']['@type'], array( 'WebPage', 'ItemPage' ) ) );
			array_unshift( $type, 'ItemPage' );
			$ctx['page']['@type'] = 1 === count( $type ) ? $type[0] : $type;
			$ctx['page']['about'] = array( '@id' => $node['@id'] );
			if ( empty( $ctx['page']['mainEntity'] ) ) {
				$ctx['page']['mainEntity'] = array( '@id' => $node['@id'] );
			}
			return $ctx;
		}

		// فروشگاه و دسته‌ها: ItemList از محصولات همین صفحه.
		if ( is_shop() || is_product_taxonomy() ) {
			$ids = array();
			global $wp_query;
			foreach ( (array) $wp_query->posts as $p ) {
				if ( $p instanceof WP_Post && 'product' === $p->post_type ) {
					$ids[] = $p->ID;
				}
			}
			if ( $ids ) {
				$paged = max( 1, (int) get_query_var( 'paged' ) );
				$per   = max( 1, (int) $wp_query->get( 'posts_per_page' ) );
				$items = array();
				foreach ( $ids as $i => $pid ) {
					$items[] = array(
						'@type'    => 'ListItem',
						'position' => ( $paged - 1 ) * $per + $i + 1,
						'url'      => get_permalink( $pid ),
						'name'     => zc_seo_clean( get_the_title( $pid ) ),
					);
				}
				$list_id                  = $url . '#itemlist';
				$ctx['graph'][ $list_id ] = array(
					'@type'           => 'ItemList',
					'@id'             => $list_id,
					'itemListOrder'   => 'https://schema.org/ItemListUnordered',
					'numberOfItems'   => (int) $wp_query->found_posts,
					'itemListElement' => $items,
				);
				$ctx['page']['@type']     = 'CollectionPage';
				if ( empty( $ctx['page']['mainEntity'] ) ) {
					$ctx['page']['mainEntity'] = array( '@id' => $list_id );
				}
			}
		}
		return $ctx;
	}
);

// پرسش‌های محصول ← FAQPage (فقط وقتی بخش پرسش‌ها در صفحه چاپ می‌شود).
add_action(
	'woocommerce_after_single_product',
	static function () {
		if ( ! zc_schema_can_collect() || ! function_exists( 'zc_wc_faq_items' ) || ! is_product() ) {
			return;
		}
		$items = zc_wc_faq_items( (int) get_queried_object_id() );
		if ( $items ) {
			zc_schema_add_faq( $items );
		}
	}
);

/* =========================================================================
 * robots و متاتگ‌ها
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_is_filtered_request' ) ) :
	/**
	 * آیا نشانی فعلی فیلتر/مرتب‌سازی دارد؟ (نسخه‌های تکراری فهرست محصولات).
	 *
	 * @return bool
	 */
	function zc_wc_is_filtered_request() {
		if ( ! ( is_shop() || is_product_taxonomy() ) ) {
			return false;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		foreach ( array_keys( $_GET ) as $key ) {
			$key = (string) $key;
			if ( in_array( $key, array( 'kind', 'on_sale', 'in_stock', 'min_price', 'max_price', 'orderby', 'rating_filter', 'product_view' ), true ) || 0 === strpos( $key, 'filter_' ) || 0 === strpos( $key, 'query_type_' ) ) {
				return true;
			}
		}
		// phpcs:enable
		return false;
	}
endif;

add_filter(
	'zc_seo_noindex',
	static function ( $noindex ) {
		return $noindex || zc_wc_is_filtered_request() || is_cart() || is_checkout() || is_account_page();
	}
);

// هماهنگی با Yoast: نشانی‌های فیلتر noindex, follow.
add_filter(
	'wpseo_robots_array',
	static function ( $robots ) {
		if ( zc_wc_is_filtered_request() ) {
			$robots['index']  = 'noindex';
			$robots['follow'] = 'follow';
		}
		return $robots;
	}
);

add_filter(
	'zc_seo_meta_tags',
	static function ( $tags ) {
		if ( ! is_product() ) {
			return $tags;
		}
		$product = wc_get_product( (int) get_queried_object_id() );
		if ( ! $product instanceof WC_Product ) {
			return $tags;
		}
		foreach ( $tags as $i => $tag ) {
			if ( isset( $tag[1] ) && 'og:type' === $tag[1] ) {
				$tags[ $i ][2] = 'product';
			}
		}
		if ( '' !== (string) $product->get_price() ) {
			$currency = zc_wc_schema_currency();
			$tags[]   = array( 'property', 'product:price:amount', zc_wc_schema_price( $product, $product->get_price() ) );
			$tags[]   = array( 'property', 'product:price:currency', $currency );
			if ( $product->is_on_sale() && '' !== (string) $product->get_regular_price() && ! $product->is_type( 'variable' ) ) {
				$tags[] = array( 'property', 'product:original_price:amount', zc_wc_schema_price( $product, $product->get_regular_price() ) );
				$tags[] = array( 'property', 'product:original_price:currency', $currency );
			}
		}
		$tags[] = array( 'property', 'product:availability', $product->is_in_stock() ? 'in stock' : 'out of stock' );
		$tags[] = array( 'property', 'product:condition', 'new' );
		if ( '' !== (string) $product->get_sku() ) {
			$tags[] = array( 'property', 'product:retailer_item_id', $product->get_sku() );
		}
		$tags[] = array( 'name', 'twitter:label1', __( 'قیمت', 'zarincoach' ) );
		$tags[] = array( 'name', 'twitter:data1', wp_strip_all_tags( html_entity_decode( wc_price( wc_get_price_to_display( $product ) ), ENT_QUOTES, 'UTF-8' ) ) );
		return $tags;
	}
);

// نقشه‌ی سایت داخلی: برچسب‌های محصول (کم‌محتوا) حذف؛ محصولات مخفی از کاتالوگ حذف.
add_filter(
	'wp_sitemaps_taxonomies',
	static function ( $taxonomies ) {
		if ( function_exists( 'zc_sitemap_on' ) && zc_sitemap_on() ) {
			unset( $taxonomies['product_tag'], $taxonomies['product_shipping_class'] );
		}
		return $taxonomies;
	},
	20
);
add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( $args, $post_type ) {
		if ( 'product' === $post_type && function_exists( 'zc_sitemap_on' ) && zc_sitemap_on() ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
				array(
					'taxonomy' => 'product_visibility',
					'field'    => 'name',
					'terms'    => array( 'exclude-from-search', 'exclude-from-catalog' ),
					'operator' => 'NOT IN',
				),
			);
		}
		return $args;
	},
	20,
	2
);
