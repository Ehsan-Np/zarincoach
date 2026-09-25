<?php
/**
 * پل سازگاری با Yoast SEO / Yoast SEO Premium
 *
 * وقتی Yoast فعال است، عنوان، متا، اوپن‌گراف، کانونیکال، robots، نقشه‌ی سایت و ریدایرکت‌ها در اختیار Yoast است
 * و سئوی داخلی قالب کنار می‌رود. این پل تضمین می‌کند اطلاعات تخصصی قالب از دست نرود:
 *
 * - یک گراف JSON-LD واحد: گره‌های قالب (مطب/LocalBusiness، اعتبارنامه‌ها، خدمات، محصول، FAQ ویجت‌ها،
 *   کتابخانه‌ی طرحواره‌ها و…) از طریق فیلتر رسمی `wpseo_schema_graph` به گراف Yoast افزوده و شناسه‌ها
 *   به شناسه‌های Yoast (WebSite، WebPage، Person/Organization) نگاشت می‌شوند؛ هیچ گره‌ی تکراری ساخته نمی‌شود.
 * - گراف Yoast پس از رندر ویجت‌ها (در پاورقی) چاپ می‌شود تا داده‌ی ویجت‌های المنتور هم در آن باشد.
 * - مسیر راهنمای Yoast همان مسیر راهنمای نمایشی قالب است (BreadcrumbList یکسان با آنچه کاربر می‌بیند).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_yoast_active' ) ) :
	/**
	 * آیا Yoast SEO فعال است؟
	 *
	 * @return bool
	 */
	function zc_yoast_active() {
		return defined( 'WPSEO_VERSION' ) && function_exists( 'YoastSEO' );
	}
endif;

if ( ! function_exists( 'zc_yoast_bridge' ) ) :
	/**
	 * آیا پل اسکیما فعال است؟
	 *
	 * @return bool
	 */
	function zc_yoast_bridge() {
		static $on = null;
		if ( null === $on ) {
			$on = zc_yoast_active() && zc_switch( 'seo_yoast_bridge', true ) && version_compare( WPSEO_VERSION, '20.0', '>=' );
			/**
			 * فیلتر فعال بودن پل Yoast.
			 *
			 * @param bool $on وضعیت.
			 */
			$on = (bool) apply_filters( 'zc_yoast_bridge', $on );
		}
		return $on;
	}
endif;

if ( ! function_exists( 'zc_yoast_is_premium' ) ) :
	/**
	 * آیا Yoast SEO Premium فعال است؟
	 *
	 * @return bool
	 */
	function zc_yoast_is_premium() {
		return defined( 'WPSEO_PREMIUM_VERSION' ) || defined( 'WPSEO_PREMIUM_FILE' );
	}
endif;

/* =========================================================================
 * راه‌اندازی
 * ========================================================================= */

add_action(
	'after_setup_theme',
	static function () {
		add_theme_support( 'yoast-seo-breadcrumbs' );
	},
	20
);

add_action(
	'wp',
	static function () {
		if ( is_admin() || ! zc_yoast_bridge() ) {
			return;
		}
		// انتقال گراف Yoast به پاورقی (پس از رندر ویجت‌ها).
		add_filter( 'wpseo_frontend_presenters', 'zc_yoast_defer_schema_presenter', 99 );
		add_action( 'wp_footer', 'zc_yoast_print_schema', 50 );
		add_filter( 'wpseo_schema_graph', 'zc_yoast_schema_graph', 20, 2 );
		add_filter( 'wpseo_breadcrumb_links', 'zc_yoast_breadcrumb_links', 20 );
	}
);

if ( ! function_exists( 'zc_yoast_defer_schema_presenter' ) ) :
	/**
	 * حذف ارائه‌دهنده‌ی اسکیما از <head>.
	 *
	 * @param array $presenters ارائه‌دهنده‌ها.
	 * @return array
	 */
	function zc_yoast_defer_schema_presenter( $presenters ) {
		if ( ! zc_schema_can_collect() ) {
			return $presenters;
		}
		foreach ( (array) $presenters as $i => $presenter ) {
			if ( $presenter instanceof \Yoast\WP\SEO\Presenters\Schema_Presenter ) {
				unset( $presenters[ $i ] );
				$GLOBALS['zc_yoast_schema_deferred'] = true;
			}
		}
		return $presenters;
	}
endif;

if ( ! function_exists( 'zc_yoast_print_schema' ) ) :
	/**
	 * چاپ گراف کامل Yoast (با گره‌های قالب) در پاورقی.
	 *
	 * @return void
	 */
	function zc_yoast_print_schema() {
		if ( empty( $GLOBALS['zc_yoast_schema_deferred'] ) ) {
			return;
		}
		$schema = YoastSEO()->meta->for_current_page()->schema;
		if ( ! is_array( $schema ) || empty( $schema ) ) {
			return;
		}
		/** سازگاری با فیلتر خروجی Yoast. */
		$schema = apply_filters( 'wpseo_json_ld_output', $schema, YoastSEO()->meta->for_current_page()->context );
		if ( ! is_array( $schema ) || empty( $schema ) ) {
			return;
		}
		$json = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
		if ( $json ) {
			echo '<script type="application/ld+json" class="yoast-schema-graph">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD امن (JSON_HEX_TAG).
		}
	}
endif;

/* =========================================================================
 * ادغام گراف
 * ========================================================================= */

if ( ! function_exists( 'zc_yoast_remap_ids' ) ) :
	/**
	 * جایگزینی بازگشتی شناسه‌ها در یک گره.
	 *
	 * @param mixed $data داده.
	 * @param array $map  نگاشت قدیم ⇒ جدید.
	 * @return mixed
	 */
	function zc_yoast_remap_ids( $data, array $map ) {
		if ( ! is_array( $data ) ) {
			return $data;
		}
		foreach ( $data as $k => $v ) {
			if ( '@id' === $k && is_string( $v ) && isset( $map[ $v ] ) ) {
				$data[ $k ] = $map[ $v ];
			} elseif ( is_array( $v ) ) {
				$data[ $k ] = zc_yoast_remap_ids( $v, $map );
			}
		}
		return $data;
	}
endif;

if ( ! function_exists( 'zc_yoast_has_type' ) ) :
	/**
	 * آیا گره یکی از انواع داده‌شده را دارد؟
	 *
	 * @param array    $node  گره.
	 * @param string[] $types انواع.
	 * @return bool
	 */
	function zc_yoast_has_type( $node, array $types ) {
		return is_array( $node ) && isset( $node['@type'] ) && (bool) array_intersect( (array) $node['@type'], $types );
	}
endif;

if ( ! function_exists( 'zc_yoast_schema_graph' ) ) :
	/**
	 * افزودن گره‌های قالب به گراف Yoast.
	 *
	 * @param array $graph   گره‌های Yoast.
	 * @param mixed $context Meta_Tags_Context.
	 * @return array
	 */
	function zc_yoast_schema_graph( $graph, $context = null ) {
		if ( ! is_array( $graph ) || ! zc_schema_can_collect() || is_404() ) {
			return $graph;
		}

		$theme = zc_schema_graph_keyed();
		if ( ! $theme ) {
			return $graph;
		}

		// شناسایی گره‌های اصلی Yoast.
		$index = array();
		$site  = '';
		$page  = '';
		$rep   = '';
		foreach ( $graph as $i => $node ) {
			if ( ! is_array( $node ) || empty( $node['@id'] ) ) {
				continue;
			}
			$index[ $node['@id'] ] = $i;
			if ( ! $site && zc_yoast_has_type( $node, array( 'WebSite' ) ) ) {
				$site = $node['@id'];
				$rep  = isset( $node['publisher']['@id'] ) ? (string) $node['publisher']['@id'] : '';
			}
		}
		$main = is_object( $context ) && ! empty( $context->main_schema_id ) ? (string) $context->main_schema_id : '';
		if ( '' !== $main && isset( $index[ $main ] ) ) {
			$page = $main;
		} else {
			foreach ( $graph as $node ) {
				if ( zc_yoast_has_type( $node, array( 'WebPage', 'ItemPage', 'CollectionPage', 'ProfilePage', 'AboutPage', 'ContactPage', 'FAQPage', 'CheckoutPage', 'SearchResultsPage', 'MedicalWebPage' ) ) ) {
					$page = (string) $node['@id'];
					break;
				}
			}
		}

		$url      = zc_canonical_url();
		$url      = '' !== $url ? $url : home_url( add_query_arg( array() ) );
		$page_key = $url . '#webpage';
		$map      = array();

		// گره‌هایی که Yoast خود می‌سازد: حذف از گراف قالب.
		$patch = isset( $theme[ $page_key ] ) ? $theme[ $page_key ] : array();
		unset( $theme[ $page_key ], $theme[ $url . '#breadcrumb' ], $theme[ $url . '#article' ], $theme[ $url . '#primaryimage' ], $theme[ zc_schema_id( 'website' ) ] );

		if ( $site ) {
			$map[ zc_schema_id( 'website' ) ] = $site;
		}
		if ( $page ) {
			$map[ $page_key ] = $page;
		}

		// شخص: اگر نماینده‌ی سایت در Yoast «شخص» است، اطلاعات قالب به همان گره افزوده می‌شود.
		$person_key = zc_schema_id( 'person' );
		if ( '' !== $rep && isset( $index[ $rep ] ) && isset( $theme[ $person_key ] ) && zc_yoast_has_type( $graph[ $index[ $rep ] ], array( 'Person' ) ) ) {
			$extra = $theme[ $person_key ];
			unset( $extra['@id'], $extra['@type'], $extra['url'] );
			if ( ! empty( $graph[ $index[ $rep ] ]['image'] ) ) {
				unset( $extra['image'] );
			}
			$map[ $person_key ]    = $rep;
			$graph[ $index[ $rep ] ] = zc_schema_merge( $graph[ $index[ $rep ] ], zc_yoast_remap_ids( $extra, $map ) );
			unset( $theme[ $person_key ] );
		}

		// وصله‌ی WebPage: نوع، موضوع، موجودیت اصلی و پرسش‌ها.
		if ( $page && $patch ) {
			$node  = &$graph[ $index[ $page ] ];
			$types = array_values( array_unique( array_merge( (array) $node['@type'], (array) $patch['@type'] ) ) );
			if ( count( $types ) > 1 ) {
				$types = array_values( array_diff( $types, array( 'WebPage' ) ) );
			}
			$node['@type'] = 1 === count( $types ) ? $types[0] : $types;
			foreach ( array( 'about', 'mentions', 'reviewedBy', 'lastReviewed' ) as $key ) {
				if ( ! empty( $patch[ $key ] ) ) {
					$node[ $key ] = zc_yoast_remap_ids( $patch[ $key ], $map );
				}
			}
			if ( ! empty( $patch['mainEntity'] ) ) {
				$entity = zc_yoast_remap_ids( $patch['mainEntity'], $map );
				if ( ! empty( $node['mainEntity'] ) && isset( $patch['mainEntity'][0] ) ) {
					// پرسش‌های بلوک FAQ خود Yoast + پرسش‌های قالب.
					$entity = array_merge( isset( $node['mainEntity'][0] ) ? $node['mainEntity'] : array( $node['mainEntity'] ), $entity );
				}
				$node['mainEntity'] = $entity;
			}
			unset( $node );
		}

		// افزودن سایر گره‌ها (ادغام با گره‌ی هم‌شناسه در صورت وجود).
		foreach ( $theme as $key => $node ) {
			if ( ! is_array( $node ) || ! $node ) {
				continue;
			}
			$node = zc_yoast_remap_ids( $node, $map );
			$id   = isset( $node['@id'] ) ? (string) $node['@id'] : (string) $key;
			if ( isset( $index[ $id ] ) ) {
				$graph[ $index[ $id ] ] = zc_schema_merge( $graph[ $index[ $id ] ], $node );
			} else {
				$graph[]      = $node;
				$index[ $id ] = count( $graph ) - 1;
			}
		}

		/**
		 * فیلتر گراف ادغام‌شده.
		 *
		 * @param array $graph گره‌ها.
		 */
		return (array) apply_filters( 'zc_yoast_schema_graph', array_values( $graph ) );
	}
endif;

/* =========================================================================
 * مسیر راهنما
 * ========================================================================= */

if ( ! function_exists( 'zc_yoast_breadcrumb_links' ) ) :
	/**
	 * مسیر راهنمای Yoast = مسیر راهنمای قالب (برای یکسانی BreadcrumbList و نمایش).
	 *
	 * @param array $links پیوندهای Yoast.
	 * @return array
	 */
	function zc_yoast_breadcrumb_links( $links ) {
		if ( ! function_exists( 'zc_breadcrumb_items' ) || ! zc_switch( 'seo_breadcrumbs', true ) ) {
			return $links;
		}
		$items = zc_breadcrumb_items();
		if ( count( $items ) < 2 ) {
			return $links;
		}
		$url = zc_canonical_url();
		$url = '' !== $url ? $url : home_url( add_query_arg( array() ) );
		$out = array();
		foreach ( $items as $item ) {
			$out[] = array(
				'url'  => '' !== (string) $item['url'] ? (string) $item['url'] : $url,
				'text' => wp_strip_all_tags( (string) $item['label'] ),
			);
		}
		return $out;
	}
endif;

/* =========================================================================
 * پیشخوان: راهنمای پیکربندی
 * ========================================================================= */

add_action(
	'admin_notices',
	static function () {
		if ( ! zc_yoast_active() || ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( ! $screen || false === strpos( (string) $screen->id, 'zc-options' ) ) {
			return;
		}
		$rep = class_exists( 'WPSEO_Options' ) ? (string) WPSEO_Options::get( 'company_or_person', '' ) : '';
		echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Yoast SEO شناسایی شد.', 'zarincoach' ) . '</strong> ';
		echo esc_html__( 'عنوان، متا، اوپن‌گراف، نقشه‌ی سایت و ریدایرکت‌ها با Yoast است؛ اسکیمای تخصصی قالب (مطب، اعتبارنامه‌ها، خدمات، محصولات و پرسش‌ها) در گراف Yoast ادغام می‌شود.', 'zarincoach' );
		if ( 'person' !== $rep ) {
			echo ' ' . esc_html__( 'پیشنهاد: در Yoast ← تنظیمات ← نمایش سایت، «شخص» را انتخاب و کاربر مریم جمالی را برگزینید تا اطلاعات حرفه‌ای در همان گره‌ی شخص ثبت شود.', 'zarincoach' );
		}
		echo '</p></div>';
	}
);
