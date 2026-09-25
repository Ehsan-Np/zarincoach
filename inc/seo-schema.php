<?php
/**
 * گراف اسکیمای یکپارچه (JSON-LD) با «جمع‌کننده».
 *
 * همه‌ی داده‌های ساختاریافته‌ی صفحه در یک <script type="application/ld+json"> و یک @graph
 * با شناسه‌های (@id) پایدار و ارجاع متقابل چاپ می‌شوند:
 *   WebSite ← Person ← ProfessionalService ← WebPage (نوع‌دار) ← BreadcrumbList
 *   + BlogPosting / Service+Offer / OfferCatalog / FAQ / Book / مدارک (از ویجت‌ها).
 *
 * ویجت‌های المنتور به‌جای چاپ JSON-LD جداگانه، گره‌ی خود را با zc_schema_add_node()
 * یا zc_schema_add_faq() به جمع‌کننده می‌دهند؛ خروجی نهایی در wp_footer چاپ می‌شود
 * (گوگل JSON-LD را در هر جای سند می‌خواند).
 *
 * طبق تصمیم صاحب سایت، AggregateRating/Review در اسکیما درج نمی‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* =========================================================================
 * جمع‌کننده
 * ========================================================================= */

if ( ! function_exists( 'zc_schema_store' ) ) :
	/**
	 * مخزن سراسری گراف (با ارجاع).
	 *
	 * @return array
	 */
	function &zc_schema_store() {
		static $store = array(
			'nodes' => array(),
			'faq'   => array(),
			'hints' => array(),
		);
		return $store;
	}
endif;

if ( ! function_exists( 'zc_schema_id' ) ) :
	/**
	 * شناسه‌ی پایدار یک گره‌ی سراسری.
	 *
	 * @param string $key person | practice | website | pco | …
	 * @return string
	 */
	function zc_schema_id( $key ) {
		return home_url( '/' ) . '#' . $key;
	}
endif;

if ( ! function_exists( 'zc_schema_merge' ) ) :
	/**
	 * ادغام دو گره: مقادیر خالی پر می‌شوند و فهرست‌ها (hasCredential، knowsAbout، sameAs…) یکتا ادغام می‌شوند.
	 *
	 * @param array $a گره‌ی موجود.
	 * @param array $b گره‌ی جدید.
	 * @return array
	 */
	function zc_schema_merge( array $a, array $b ) {
		foreach ( $b as $key => $value ) {
			if ( ! array_key_exists( $key, $a ) || '' === $a[ $key ] || array() === $a[ $key ] || null === $a[ $key ] ) {
				$a[ $key ] = $value;
				continue;
			}
			$is_list_a = is_array( $a[ $key ] ) && array_values( $a[ $key ] ) === $a[ $key ];
			$is_list_b = is_array( $value ) && array_values( $value ) === $value;
			if ( $is_list_a && $is_list_b && '@type' !== $key ) {
				$seen = array();
				foreach ( array_merge( $a[ $key ], $value ) as $item ) {
					$sig = is_array( $item ) ? ( isset( $item['name'] ) ? 'n:' . $item['name'] : md5( wp_json_encode( $item ) ) ) : 's:' . $item;
					$seen[ $sig ] = $item;
				}
				$a[ $key ] = array_values( $seen );
			}
		}
		return $a;
	}
endif;

if ( ! function_exists( 'zc_schema_add_node' ) ) :
	/**
	 * افزودن/ادغام یک گره در گراف صفحه.
	 *
	 * @param array $node گره (ترجیحاً با @id).
	 * @return void
	 */
	function zc_schema_add_node( array $node ) {
		if ( empty( $node['@type'] ) && empty( $node['@id'] ) ) {
			return;
		}
		$store = &zc_schema_store();
		$key   = isset( $node['@id'] ) ? (string) $node['@id'] : 'n' . count( $store['nodes'] );
		$store['nodes'][ $key ] = isset( $store['nodes'][ $key ] ) ? zc_schema_merge( $store['nodes'][ $key ], $node ) : $node;
	}
endif;

if ( ! function_exists( 'zc_schema_add_faq' ) ) :
	/**
	 * افزودن پرسش و پاسخ‌ها (FAQPage) — تکراری‌ها حذف می‌شوند.
	 *
	 * @param array $items آیتم‌ها: [ ['q' => پرسش, 'a' => پاسخ], … ].
	 * @return void
	 */
	function zc_schema_add_faq( array $items ) {
		$store = &zc_schema_store();
		foreach ( $items as $item ) {
			$q = isset( $item['q'] ) ? zc_seo_clean( $item['q'] ) : '';
			$a = isset( $item['a'] ) ? trim( wp_kses( (string) $item['a'], array( 'a' => array( 'href' => true ), 'br' => array(), 'p' => array(), 'strong' => array(), 'b' => array(), 'ul' => array(), 'ol' => array(), 'li' => array() ) ) ) : '';
			if ( '' === $q || '' === wp_strip_all_tags( $a ) ) {
				continue;
			}
			$store['faq'][ md5( $q ) ] = array(
				'@type'          => 'Question',
				'name'           => $q,
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => $a,
				),
			);
		}
	}
endif;

if ( ! function_exists( 'zc_schema_add_faq_query' ) ) :
	/**
	 * افزودن پرسش‌های یک WP_Query (نوع zc_faq) بدون تغییر حلقه.
	 *
	 * @param WP_Query $query کوئری.
	 * @return void
	 */
	function zc_schema_add_faq_query( $query ) {
		if ( ! ( $query instanceof WP_Query ) || ! zc_schema_can_collect() || ! zc_switch( 'home_faq_schema', true ) ) {
			return;
		}
		$items = array();
		foreach ( (array) $query->posts as $faq ) {
			$items[] = array(
				'q' => get_the_title( $faq ),
				'a' => wpautop( (string) $faq->post_content ),
			);
		}
		zc_schema_add_faq( $items );
	}
endif;

if ( ! function_exists( 'zc_schema_hint' ) ) :
	/**
	 * راهنمای ویجت‌ها برای گراف (مثلاً page_type = ProfilePage یا services = true).
	 *
	 * @param string $key   کلید.
	 * @param mixed  $value مقدار (null = خواندن).
	 * @return mixed
	 */
	function zc_schema_hint( $key, $value = null ) {
		$store = &zc_schema_store();
		if ( null !== $value ) {
			$store['hints'][ $key ] = $value;
		}
		return isset( $store['hints'][ $key ] ) ? $store['hints'][ $key ] : null;
	}
endif;

if ( ! function_exists( 'zc_schema_can_collect' ) ) :
	/**
	 * جمع‌آوری فقط در فرانت‌اند واقعی (نه ویرایشگر/پیش‌نمایش المنتور).
	 *
	 * @return bool
	 */
	function zc_schema_can_collect() {
		if ( is_admin() || wp_doing_ajax() || ! zc_seo_active() ) {
			return false;
		}
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$el = \Elementor\Plugin::$instance;
			if ( ( isset( $el->editor ) && $el->editor->is_edit_mode() ) || ( isset( $el->preview ) && $el->preview->is_preview_mode() ) ) {
				return false;
			}
		}
		return true;
	}
endif;

/* =========================================================================
 * نوع صفحه
 * ========================================================================= */

if ( ! function_exists( 'zc_schema_page_types' ) ) :
	/**
	 * انواع مجاز WebPage برای جعبه‌ی سئو.
	 *
	 * @return array<string,string>
	 */
	function zc_schema_page_types() {
		return array(
			''                  => __( 'خودکار', 'zarincoach' ),
			'WebPage'           => __( 'صفحه‌ی عمومی (WebPage)', 'zarincoach' ),
			'AboutPage'         => __( 'درباره (AboutPage)', 'zarincoach' ),
			'ContactPage'       => __( 'تماس (ContactPage)', 'zarincoach' ),
			'ProfilePage'       => __( 'رزومه / پروفایل (ProfilePage)', 'zarincoach' ),
			'FAQPage'           => __( 'پرسش‌های پرتکرار (FAQPage)', 'zarincoach' ),
			'CollectionPage'    => __( 'فهرست/مجموعه (CollectionPage)', 'zarincoach' ),
			'CheckoutPage'      => __( 'رزرو/پرداخت (CheckoutPage)', 'zarincoach' ),
			'MedicalWebPage'    => __( 'اطلاعات سلامت (MedicalWebPage)', 'zarincoach' ),
		);
	}
endif;

if ( ! function_exists( 'zc_schema_page_type' ) ) :
	/**
	 * نوع WebPage صفحه‌ی جاری: جعبه‌ی سئو ← راهنمای ویجت ← تشخیص خودکار.
	 *
	 * @return string
	 */
	function zc_schema_page_type() {
		if ( is_singular() ) {
			$meta = zc_seo_post_meta( 'schema' );
			if ( '' !== $meta && array_key_exists( $meta, zc_schema_page_types() ) ) {
				return $meta;
			}
		}
		$hint = zc_schema_hint( 'page_type' );
		if ( $hint ) {
			return (string) $hint;
		}
		if ( is_front_page() ) {
			return 'WebPage';
		}
		if ( is_search() ) {
			return 'SearchResultsPage';
		}
		if ( is_home() || is_archive() ) {
			return 'CollectionPage';
		}
		return 'WebPage';
	}
endif;

/* =========================================================================
 * گره‌های سراسری
 * ========================================================================= */

if ( ! function_exists( 'zc_schema_lines' ) ) :
	/**
	 * متن چندخطی ← آرایه.
	 *
	 * @param string $text متن.
	 * @return string[]
	 */
	function zc_schema_lines( $text ) {
		return array_values( array_filter( array_map( 'trim', preg_split( '/[\r\n]+/', (string) $text ) ) ) );
	}
endif;

if ( ! function_exists( 'zc_schema_image_node' ) ) :
	/**
	 * گره‌ی ImageObject.
	 *
	 * @param mixed  $src     تصویر (شناسه/آرایه/آدرس).
	 * @param string $id      @id.
	 * @param string $caption توضیح.
	 * @return array
	 */
	function zc_schema_image_node( $src, $id, $caption = '' ) {
		$img = zc_seo_image( $src );
		if ( empty( $img ) ) {
			return array();
		}
		return array_filter(
			array(
				'@type'      => 'ImageObject',
				'@id'        => $id,
				'url'        => $img['url'],
				'contentUrl' => $img['url'],
				'width'      => $img['width'] ? $img['width'] : null,
				'height'     => $img['height'] ? $img['height'] : null,
				'caption'    => '' !== $caption ? $caption : $img['alt'],
				'inLanguage' => 'fa-IR',
			)
		);
	}
endif;

if ( ! function_exists( 'zc_schema_same_as' ) ) :
	/**
	 * پروفایل‌های رسمی (تنظیمات سئو + شبکه‌های اجتماعی).
	 *
	 * @return string[]
	 */
	function zc_schema_same_as() {
		$list = array_filter( (array) zc_opt( 'seo_person_sameas', array() ) );
		foreach ( array( 'instagram', 'telegram', 'bale', 'linkedin', 'youtube', 'aparat', 'eitaa', 'x' ) as $net ) {
			$url = function_exists( 'zc_social_url' ) ? zc_social_url( $net ) : '';
			if ( '' !== $url ) {
				$list[] = $url;
			}
		}
		$list = array_map( 'esc_url_raw', array_map( 'trim', $list ) );
		$list = array_filter(
			$list,
			static function ( $u ) {
				return (bool) preg_match( '#^https?://#', $u );
			}
		);
		return array_values( array_unique( $list ) );
	}
endif;

if ( ! function_exists( 'zc_schema_pco_node' ) ) :
	/**
	 * سازمان نظام روان‌شناسی و مشاوره (صادرکننده‌ی پروانه).
	 *
	 * @return array
	 */
	function zc_schema_pco_node() {
		return array(
			'@type'         => 'Organization',
			'@id'           => zc_schema_id( 'pco' ),
			'name'          => 'سازمان نظام روان‌شناسی و مشاوره جمهوری اسلامی ایران',
			'alternateName' => 'Psychology and Counseling Organization of Iran',
			'url'           => 'https://pcoiran.ir/',
		);
	}
endif;

if ( ! function_exists( 'zc_schema_address' ) ) :
	/**
	 * نشانی پستی مطب.
	 *
	 * @return array
	 */
	function zc_schema_address() {
		$street = trim( (string) zc_opt( 'seo_local_street', '' ) );
		if ( '' === $street ) {
			$street = trim( (string) zc_opt( 'contact_address', '' ) );
		}
		return array_filter(
			array(
				'@type'           => 'PostalAddress',
				'streetAddress'   => $street,
				'addressLocality' => trim( (string) zc_opt( 'contact_city', 'بوشهر' ) ),
				'addressRegion'   => trim( (string) zc_opt( 'seo_local_region', '' ) ),
				'postalCode'      => zc_digits_to_latin( trim( (string) zc_opt( 'seo_local_postal', '' ) ) ),
				'addressCountry'  => 'IR',
			)
		);
	}
endif;

if ( ! function_exists( 'zc_schema_geo' ) ) :
	/**
	 * مختصات (فقط اگر معتبر وارد شده باشد).
	 *
	 * @return array{lat:float,lng:float}|array{}
	 */
	function zc_schema_geo() {
		$lat = zc_digits_to_latin( trim( (string) zc_opt( 'seo_local_lat', '' ) ) );
		$lng = zc_digits_to_latin( trim( (string) zc_opt( 'seo_local_lng', '' ) ) );
		if ( ! is_numeric( $lat ) || ! is_numeric( $lng ) || abs( (float) $lat ) > 90 || abs( (float) $lng ) > 180 || ( 0.0 === (float) $lat && 0.0 === (float) $lng ) ) {
			return array();
		}
		return array(
			'lat' => round( (float) $lat, 6 ),
			'lng' => round( (float) $lng, 6 ),
		);
	}
endif;

if ( ! function_exists( 'zc_schema_person_node' ) ) :
	/**
	 * گره‌ی Person (صاحب سایت).
	 *
	 * @return array
	 */
	function zc_schema_person_node() {
		$contact = zc_contact_fields();
		$license = zc_digits_to_latin( trim( (string) zc_opt( 'legal_license_no', '' ) ) );
		$pco     = zc_digits_to_latin( trim( (string) zc_opt( 'legal_pco_code', '' ) ) );
		$degree  = trim( (string) zc_opt( 'legal_degree', '' ) );
		$image   = zc_schema_image_node( (array) zc_opt( 'seo_person_image', array() ), zc_schema_id( 'person-image' ), zc_seo_person_name() );

		$credentials = array();
		if ( '' !== $license ) {
			$credentials[] = array(
				'@type'              => 'EducationalOccupationalCredential',
				'@id'                => zc_schema_id( 'license' ),
				'credentialCategory' => 'license',
				'name'               => 'پروانه اشتغال روان‌شناسی',
				'identifier'         => $license,
				'recognizedBy'       => array( '@id' => zc_schema_id( 'pco' ) ),
			);
		}
		if ( '' !== $degree ) {
			$credentials[] = array(
				'@type'              => 'EducationalOccupationalCredential',
				'credentialCategory' => 'degree',
				'educationalLevel'   => 'Master',
				'name'               => $degree,
			);
		}

		$member = array();
		if ( '' !== $pco ) {
			$member[] = array(
				'@type'               => 'ProgramMembership',
				'programName'         => 'عضویت سازمان نظام روان‌شناسی و مشاوره',
				'membershipNumber'    => $pco,
				'hostingOrganization' => array( '@id' => zc_schema_id( 'pco' ) ),
			);
		}

		$phones = array_values( array_filter( array( zc_seo_phone_e164( $contact['phone'] ), zc_seo_phone_e164( $contact['phone2'] ) ) ) );

		return array_filter(
			array(
				'@type'         => 'Person',
				'@id'           => zc_schema_id( 'person' ),
				'name'          => zc_seo_person_name(),
				'alternateName' => trim( (string) zc_opt( 'seo_person_alt', '' ) ),
				'url'           => home_url( '/' ),
				'jobTitle'      => trim( (string) zc_opt( 'seo_person_job', '' ) ),
				'description'   => zc_seo_clean( (string) zc_opt( 'seo_person_desc', '' ) ),
				'image'         => $image ? $image : null,
				'gender'        => 'https://schema.org/Female',
				'email'         => sanitize_email( $contact['email'] ),
				'telephone'     => isset( $phones[0] ) ? $phones[0] : '',
				'address'       => array_filter(
					array(
						'@type'           => 'PostalAddress',
						'addressLocality' => trim( (string) zc_opt( 'contact_city', 'بوشهر' ) ),
						'addressRegion'   => trim( (string) zc_opt( 'seo_local_region', '' ) ),
						'addressCountry'  => 'IR',
					)
				),
				'worksFor'      => zc_switch( 'seo_local_enable', true ) ? array( '@id' => zc_schema_id( 'practice' ) ) : null,
				'hasCredential' => $credentials,
				'memberOf'      => $member,
				'knowsAbout'    => zc_schema_lines( (string) zc_opt( 'seo_person_knows', '' ) ),
				'knowsLanguage' => 'fa',
				'sameAs'        => zc_schema_same_as(),
			)
		);
	}
endif;

if ( ! function_exists( 'zc_schema_practice_node' ) ) :
	/**
	 * گره‌ی ProfessionalService (مطب / کسب‌وکار محلی).
	 *
	 * @return array
	 */
	function zc_schema_practice_node() {
		$contact = zc_contact_fields();
		$geo     = zc_schema_geo();
		$hours   = zc_parse_opening_hours( (string) zc_opt( 'seo_local_hours', '' ) );
		$booking = zc_seo_phone_e164( $contact['phone'] );
		$office  = zc_seo_phone_e164( $contact['phone2'] );
		$logo    = get_site_icon_url( 512 );
		$share   = zc_seo_image( (array) zc_opt( 'seo_og_image', array() ) );
		$face    = zc_seo_image( (array) zc_opt( 'seo_person_image', array() ) );

		$points = array();
		if ( '' !== $booking ) {
			$points[] = array(
				'@type'             => 'ContactPoint',
				'telephone'         => $booking,
				'contactType'       => 'reservations',
				'name'              => 'نوبت‌دهی',
				'availableLanguage' => array( 'fa' ),
				'areaServed'        => 'IR',
			);
		}
		if ( '' !== $office && $office !== $booking ) {
			$points[] = array(
				'@type'             => 'ContactPoint',
				'telephone'         => $office,
				'contactType'       => 'customer service',
				'name'              => 'تلفن مطب',
				'availableLanguage' => array( 'fa' ),
				'areaServed'        => 'IR',
			);
		}

		$areas = array();
		foreach ( zc_schema_lines( (string) zc_opt( 'seo_area_served', '' ) ) as $area ) {
			$areas[] = array(
				'@type' => in_array( $area, array( 'ایران', 'Iran', 'IR' ), true ) ? 'Country' : 'City',
				'name'  => $area,
			);
		}

		$node = array(
			'@type'                     => 'ProfessionalService',
			'@id'                       => zc_schema_id( 'practice' ),
			'name'                      => trim( (string) zc_opt( 'seo_business_name', '' ) ) ? trim( (string) zc_opt( 'seo_business_name', '' ) ) : zc_seo_person_name(),
			'url'                       => home_url( '/' ),
			'description'               => zc_seo_clean( (string) zc_opt( 'seo_home_desc', get_bloginfo( 'description' ) ) ),
			'image'                     => ! empty( $share ) ? $share['url'] : ( ! empty( $face ) ? $face['url'] : '' ),
			'logo'                      => $logo ? $logo : '',
			'telephone'                 => '' !== $booking ? $booking : $office,
			'email'                     => sanitize_email( $contact['email'] ),
			'address'                   => zc_schema_address(),
			'openingHoursSpecification' => $hours,
			'priceRange'                => mb_substr( trim( (string) zc_opt( 'seo_price_range', '' ) ), 0, 90 ),
			'currenciesAccepted'        => 'IRR',
			'paymentAccepted'           => 'کارت بانکی، درگاه پرداخت اینترنتی',
			'areaServed'                => $areas,
			'contactPoint'              => $points,
			'founder'                   => array( '@id' => zc_schema_id( 'person' ) ),
			'employee'                  => array( '@id' => zc_schema_id( 'person' ) ),
			'knowsLanguage'             => 'fa',
		);
		if ( ! empty( $geo ) ) {
			$node['geo']    = array(
				'@type'     => 'GeoCoordinates',
				'latitude'  => $geo['lat'],
				'longitude' => $geo['lng'],
			);
			$node['hasMap'] = sprintf( 'https://www.openstreetmap.org/?mlat=%1$s&mlon=%2$s#map=18/%1$s/%2$s', $geo['lat'], $geo['lng'] );
		}
		return array_filter( $node );
	}
endif;

/* =========================================================================
 * گره‌های وابسته به صفحه
 * ========================================================================= */

if ( ! function_exists( 'zc_schema_service_node' ) ) :
	/**
	 * گره‌ی Service (+ Offer) برای یک خدمت.
	 *
	 * @param int  $post_id شناسه.
	 * @param bool $full    خروجی کامل (صفحه‌ی خود خدمت) یا خلاصه (فهرست).
	 * @return array
	 */
	function zc_schema_service_node( $post_id, $full = true ) {
		$url      = get_permalink( $post_id );
		$price    = (string) get_post_meta( $post_id, '_zc_service_price', true );
		$duration = (string) get_post_meta( $post_id, '_zc_service_duration', true );
		$irr      = zc_seo_price_irr( $price );
		$provider = zc_switch( 'seo_local_enable', true ) ? zc_schema_id( 'practice' ) : zc_schema_id( 'person' );
		$terms    = get_the_terms( $post_id, 'zc_service_cat' );

		$node = array(
			'@type'    => 'Service',
			'@id'      => $url . '#service',
			'name'     => zc_seo_clean( get_the_title( $post_id ) ),
			'url'      => $url,
			'provider' => array( '@id' => $provider ),
		);
		if ( $full ) {
			$excerpt = has_excerpt( $post_id ) ? get_post_field( 'post_excerpt', $post_id ) : get_post_field( 'post_content', $post_id );
			$node   += array_filter(
				array(
					'description' => zc_seo_clean( $excerpt, 300 ),
					'serviceType' => ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->name : 'آموزش و مشاوره‌ی روان‌شناختی',
					'areaServed'  => array(
						array(
							'@type' => 'City',
							'name'  => trim( (string) zc_opt( 'contact_city', 'بوشهر' ) ),
						),
						array(
							'@type' => 'Country',
							'name'  => 'ایران',
						),
					),
					'image'       => has_post_thumbnail( $post_id ) ? (string) get_the_post_thumbnail_url( $post_id, 'zc_wide' ) : '',
					'audience'    => array(
						'@type'           => 'PeopleAudience',
						'suggestedMinAge' => 15,
					),
				)
			);
		}
		if ( $irr > 0 ) {
			$node['offers'] = array_filter(
				array(
					'@type'         => 'Offer',
					'price'         => (string) $irr,
					'priceCurrency' => 'IRR',
					'availability'  => 'https://schema.org/InStock',
					'url'           => $url,
					'description'   => zc_seo_clean( trim( $price . ( '' !== $duration ? ' — ' . $duration : '' ) ) ),
					'seller'        => array( '@id' => $provider ),
				)
			);
		}
		return $node;
	}
endif;

if ( ! function_exists( 'zc_schema_catalog_node' ) ) :
	/**
	 * فهرست خدمات (OfferCatalog).
	 *
	 * @return array
	 */
	function zc_schema_catalog_node() {
		if ( ! post_type_exists( 'zc_service' ) ) {
			return array();
		}
		$ids = get_posts(
			array(
				'post_type'      => 'zc_service',
				'post_status'    => 'publish',
				'posts_per_page' => 20,
				'fields'         => 'ids',
				'orderby'        => array(
					'menu_order' => 'ASC',
					'date'       => 'DESC',
				),
				'no_found_rows'  => true,
			)
		);
		if ( empty( $ids ) ) {
			return array();
		}
		$items = array();
		foreach ( $ids as $id ) {
			$service = zc_schema_service_node( $id, false );
			$offer   = isset( $service['offers'] ) ? $service['offers'] : array( '@type' => 'Offer', 'url' => $service['url'] );
			unset( $service['offers'] );
			$offer['itemOffered'] = $service;
			$items[]              = $offer;
		}
		return array(
			'@type'           => 'OfferCatalog',
			'@id'             => zc_schema_id( 'services' ),
			'name'            => __( 'خدمات', 'zarincoach' ) . ' ' . zc_seo_person_name(),
			'numberOfItems'   => count( $items ),
			'itemListElement' => $items,
		);
	}
endif;

if ( ! function_exists( 'zc_schema_article_node' ) ) :
	/**
	 * گره‌ی BlogPosting.
	 *
	 * @param int    $post_id شناسه.
	 * @param string $url     آدرس.
	 * @param bool   $image   تصویر اصلی موجود است؟
	 * @return array
	 */
	function zc_schema_article_node( $post_id, $url, $image ) {
		$post    = get_post( $post_id );
		$content = zc_seo_clean( (string) $post->post_content );
		$words   = $content ? count( preg_split( '/\s+/u', $content ) ) : 0;
		$cats    = get_the_category( $post_id );
		$tags    = get_the_tags( $post_id );

		if ( zc_switch( 'seo_author_person', true ) ) {
			$author = array( '@id' => zc_schema_id( 'person' ) );
		} else {
			$author = array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) $post->post_author ),
			);
		}

		return array_filter(
			array(
				'@type'            => 'BlogPosting',
				'@id'              => $url . '#article',
				'isPartOf'         => array( '@id' => $url . '#webpage' ),
				'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
				'headline'         => mb_substr( zc_seo_clean( get_the_title( $post_id ) ), 0, 110 ),
				'description'      => zc_meta_description(),
				'image'            => $image ? array( '@id' => $url . '#primaryimage' ) : null,
				'datePublished'    => get_post_time( 'c', true, $post ),
				'dateModified'     => get_post_modified_time( 'c', true, $post ),
				'author'           => $author,
				'publisher'        => array( '@id' => zc_schema_id( 'person' ) ),
				'articleSection'   => ! empty( $cats ) ? wp_list_pluck( $cats, 'name' ) : null,
				'keywords'         => ( $tags && ! is_wp_error( $tags ) ) ? implode( '، ', wp_list_pluck( $tags, 'name' ) ) : null,
				'wordCount'        => $words ? $words : null,
				'timeRequired'     => 'PT' . zc_reading_time( $post_id ) . 'M',
				'commentCount'     => (int) get_comments_number( $post_id ),
				'inLanguage'       => 'fa-IR',
				'copyrightHolder'  => array( '@id' => zc_schema_id( 'person' ) ),
				'copyrightYear'    => (int) get_post_time( 'Y', true, $post ),
			),
			static function ( $v ) {
				return null !== $v && '' !== $v;
			}
		);
	}
endif;

/* =========================================================================
 * ساخت و چاپ گراف
 * ========================================================================= */

if ( ! function_exists( 'zc_schema_graph' ) ) :
	/**
	 * ساخت گراف کامل صفحه‌ی جاری.
	 *
	 * @return array
	 */
	function zc_schema_graph() {
		$store = &zc_schema_store();
		$local = zc_switch( 'seo_local_enable', true );
		$graph = array();

		// وب‌سایت.
		$graph[ zc_schema_id( 'website' ) ] = array(
			'@type'           => 'WebSite',
			'@id'             => zc_schema_id( 'website' ),
			'url'             => home_url( '/' ),
			'name'            => get_bloginfo( 'name' ),
			'alternateName'   => wp_parse_url( home_url(), PHP_URL_HOST ),
			'description'     => zc_seo_clean( get_bloginfo( 'description' ) ),
			'inLanguage'      => 'fa-IR',
			'publisher'       => array( '@id' => zc_schema_id( 'person' ) ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => home_url( '/?s={search_term_string}' ),
				),
				'query-input' => 'required name=search_term_string',
			),
		);

		// شخص + سازمان صادرکننده‌ی پروانه.
		$graph[ zc_schema_id( 'person' ) ] = zc_schema_person_node();
		if ( '' !== trim( (string) zc_opt( 'legal_license_no', '' ) ) || '' !== trim( (string) zc_opt( 'legal_pco_code', '' ) ) ) {
			$graph[ zc_schema_id( 'pco' ) ] = zc_schema_pco_node();
		}

		// مطب.
		if ( $local ) {
			$graph[ zc_schema_id( 'practice' ) ] = zc_schema_practice_node();
		}

		// صفحه‌ی جاری.
		if ( ! is_404() ) {
			$url   = zc_canonical_url();
			$url   = '' !== $url ? $url : home_url( add_query_arg( array() ) );
			$type  = zc_schema_page_type();
			$crumb = zc_switch( 'seo_breadcrumbs', true ) ? zc_breadcrumb_items() : array();
			$pid   = is_singular() ? (int) get_queried_object_id() : 0;

			if ( ! empty( $store['faq'] ) ) {
				$type = 'WebPage' === $type ? 'FAQPage' : ( 'FAQPage' === $type ? $type : array( $type, 'FAQPage' ) );
			}

			$page = array(
				'@type'       => $type,
				'@id'         => $url . '#webpage',
				'url'         => $url,
				'name'        => wp_get_document_title(),
				'description' => zc_meta_description(),
				'isPartOf'    => array( '@id' => zc_schema_id( 'website' ) ),
				'inLanguage'  => 'fa-IR',
			);

			// تصویر اصلی.
			$has_img = false;
			if ( $pid && has_post_thumbnail( $pid ) ) {
				$img = zc_schema_image_node( (int) get_post_thumbnail_id( $pid ), $url . '#primaryimage' );
				if ( $img ) {
					$graph[ $img['@id'] ]       = $img;
					$page['primaryImageOfPage'] = array( '@id' => $img['@id'] );
					$page['image']              = array( '@id' => $img['@id'] );
					$has_img                    = true;
				}
			}
			if ( $pid ) {
				$page['datePublished'] = get_post_time( 'c', true, $pid );
				$page['dateModified']  = get_post_modified_time( 'c', true, $pid );
			}

			// ارتباط صفحه با شخص/مطب.
			$types = (array) $type;
			if ( is_front_page() ) {
				$page['about'] = array( '@id' => zc_schema_id( 'person' ) );
				if ( $local ) {
					$page['mentions'] = array( '@id' => zc_schema_id( 'practice' ) );
				}
			}
			if ( array_intersect( $types, array( 'AboutPage', 'ProfilePage' ) ) ) {
				$page['mainEntity'] = array( '@id' => zc_schema_id( 'person' ) );
				$page['about']      = array( '@id' => zc_schema_id( 'person' ) );
			}
			if ( in_array( 'ContactPage', $types, true ) ) {
				$page['mainEntity'] = array( '@id' => $local ? zc_schema_id( 'practice' ) : zc_schema_id( 'person' ) );
				$page['about']      = $page['mainEntity'];
			}
			if ( in_array( 'MedicalWebPage', $types, true ) ) {
				$page['lastReviewed'] = get_post_modified_time( 'Y-m-d', true, $pid );
				$page['reviewedBy']   = array( '@id' => zc_schema_id( 'person' ) );
			}
			if ( ! empty( $store['faq'] ) ) {
				$page['mainEntity'] = array_values( $store['faq'] );
			}

			// مسیر راهنما.
			if ( count( $crumb ) > 1 ) {
				$list = array();
				foreach ( $crumb as $i => $item ) {
					$entry = array(
						'@type'    => 'ListItem',
						'position' => $i + 1,
						'name'     => zc_seo_clean( $item['label'] ),
					);
					$entry['item'] = '' !== $item['url'] ? $item['url'] : $url;
					$list[]        = $entry;
				}
				$graph[ $url . '#breadcrumb' ] = array(
					'@type'           => 'BreadcrumbList',
					'@id'             => $url . '#breadcrumb',
					'itemListElement' => $list,
				);
				$page['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
			}

			// مقاله.
			if ( is_singular( 'post' ) ) {
				$graph[ $url . '#article' ] = zc_schema_article_node( $pid, $url, $has_img );
				$page['author']             = zc_switch( 'seo_author_person', true ) ? array( '@id' => zc_schema_id( 'person' ) ) : null;
			}

			// خدمت.
			if ( is_singular( 'zc_service' ) ) {
				$service                 = zc_schema_service_node( $pid, true );
				$graph[ $service['@id'] ] = $service;
				// در صورت وجود پرسش، mainEntity همان پرسش‌هاست و خدمت موضوع صفحه (about) می‌شود.
				if ( empty( $store['faq'] ) ) {
					$page['mainEntity'] = array( '@id' => $service['@id'] );
				}
				$page['about'] = array( '@id' => $service['@id'] );
			}

			// مدخل کتابخانه‌ی طرحواره‌ها (DefinedTerm).
			if ( is_singular( 'zc_schema' ) && function_exists( 'zc_sc_get' ) ) {
				$sc_item = zc_sc_get( $pid );
				$sc_hub  = zc_sc_hub_url();
				$sc_alt  = array_values( array_filter( array( (string) $sc_item['en'], (string) $sc_item['code'] ) ) );
				$sc_term = array(
					'@type'            => 'DefinedTerm',
					'@id'              => $url . '#term',
					'name'             => zc_seo_clean( (string) $sc_item['title'] ),
					'alternateName'    => $sc_alt ? $sc_alt : null,
					'termCode'         => '' !== (string) $sc_item['code'] ? (string) $sc_item['code'] : null,
					'description'      => zc_seo_clean( (string) $sc_item['summary'] ),
					'url'              => $url,
					'inDefinedTermSet' => array(
						'@type' => 'DefinedTermSet',
						'@id'   => $sc_hub . '#termset',
						'name'  => ( $sc_hub_id = url_to_postid( $sc_hub ) ) ? zc_seo_clean( get_the_title( $sc_hub_id ) ) : __( 'طرحواره‌ها و الگوهای ذهنی', 'zarincoach' ), // phpcs:ignore Squiz.PHP.DisallowMultipleAssignments
						'url'   => $sc_hub,
					),
				);
				$graph[ $url . '#term' ] = array_filter( $sc_term );
				$page['mainEntity']      = array( '@id' => $url . '#term' );
				$page['about']           = array( '@id' => $url . '#term' );
				$page['author']          = array( '@id' => zc_schema_id( 'person' ) );
			}
			$sc_hub_set = zc_schema_hint( 'schemas_hub' );
			if ( $sc_hub_set && empty( $store['faq'] ) ) {
				$page['mainEntity'] = array( '@id' => (string) $sc_hub_set );
				$page['author']     = array( '@id' => zc_schema_id( 'person' ) );
			}

			// فهرست خدمات.
			if ( is_front_page() || is_post_type_archive( 'zc_service' ) || zc_schema_hint( 'services' ) ) {
				$catalog = zc_schema_catalog_node();
				if ( $catalog ) {
					$graph[ $catalog['@id'] ] = $catalog;
					if ( $local ) {
						$graph[ zc_schema_id( 'practice' ) ]['hasOfferCatalog'] = array( '@id' => $catalog['@id'] );
					}
				}
			}

			$graph[ $url . '#webpage' ] = array_filter( $page );
		}

		// گره‌های ویجت‌ها (ادغام با گره‌های موجود).
		foreach ( $store['nodes'] as $key => $node ) {
			$graph[ $key ] = isset( $graph[ $key ] ) ? zc_schema_merge( $graph[ $key ], $node ) : $node;
		}

		$graph = array_values( array_filter( $graph ) );

		/**
		 * فیلتر گراف اسکیما (آرایه‌ای از گره‌ها).
		 *
		 * @param array $graph گره‌ها.
		 */
		return (array) apply_filters( 'zc_schema_graph_nodes', $graph );
	}
endif;

if ( ! function_exists( 'zc_output_schema' ) ) :
	/**
	 * چاپ JSON-LD در انتهای صفحه (پس از رندر ویجت‌ها).
	 *
	 * @return void
	 */
	function zc_output_schema() {
		if ( ! zc_schema_can_collect() ) {
			return;
		}
		$schema = array(
			'@context' => 'https://schema.org',
			'@graph'   => zc_schema_graph(),
		);

		/**
		 * فیلتر سازگار با نسخه‌های قبل.
		 *
		 * @param array $schema داده‌ها.
		 */
		$schema = apply_filters( 'zc_schema_graph', $schema );
		$json   = wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
		if ( $json ) {
			echo '<script type="application/ld+json" class="zc-schema-graph">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD امن (JSON_HEX_TAG).
		}
	}
endif;
add_action( 'wp_footer', 'zc_output_schema', 50 );
