<?php
/**
 * انواع نوشته اختصاصی قالب
 *
 * - zc_service     : خدمات و برنامه‌های کوچینگ
 * - zc_testimonial : تجربه مراجعان
 * - zc_faq         : پرسش‌های پرتکرار
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_register_post_types' ) ) :
	/**
	 * ثبت انواع نوشته و طبقه‌بندی‌ها.
	 *
	 * @return void
	 */
	function zc_register_post_types() {

		/* ------------------------- خدمات ------------------------- */
		$service_labels = array(
			'name'               => __( 'خدمات', 'zarincoach' ),
			'singular_name'      => __( 'خدمت', 'zarincoach' ),
			'menu_name'          => __( 'خدمات سایت', 'zarincoach' ),
			'add_new'            => __( 'افزودن خدمت', 'zarincoach' ),
			'add_new_item'       => __( 'خدمت جدید', 'zarincoach' ),
			'edit_item'          => __( 'ویرایش خدمت', 'zarincoach' ),
			'new_item'           => __( 'خدمت جدید', 'zarincoach' ),
			'view_item'          => __( 'مشاهده خدمت', 'zarincoach' ),
			'search_items'       => __( 'جستجوی خدمات', 'zarincoach' ),
			'not_found'          => __( 'خدمتی یافت نشد', 'zarincoach' ),
			'not_found_in_trash' => __( 'خدمتی در زباله‌دان یافت نشد', 'zarincoach' ),
			'all_items'          => __( 'همه خدمات', 'zarincoach' ),
			'featured_image'     => __( 'تصویر خدمت', 'zarincoach' ),
			'set_featured_image' => __( 'انتخاب تصویر خدمت', 'zarincoach' ),
		);

		register_post_type(
			'zc_service',
			array(
				'labels'              => $service_labels,
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'query_var'           => true,
				'rewrite'             => array( 'slug' => 'services', 'with_front' => false ),
				'capability_type'     => 'post',
				'has_archive'         => true,
				'hierarchical'        => false,
				'menu_position'       => 21,
				'menu_icon'           => 'dashicons-superhero',
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions', 'elementor' ),
				'taxonomies'          => array( 'zc_service_cat' ),
				'exclude_from_search' => false,
			)
		);

		register_taxonomy(
			'zc_service_cat',
			array( 'zc_service' ),
			array(
				'labels'            => array(
					'name'          => __( 'دسته‌بندی خدمات', 'zarincoach' ),
					'singular_name' => __( 'دسته‌بندی خدمت', 'zarincoach' ),
					'search_items'  => __( 'جستجوی دسته‌بندی', 'zarincoach' ),
					'all_items'     => __( 'همه دسته‌بندی‌ها', 'zarincoach' ),
					'edit_item'     => __( 'ویرایش دسته‌بندی', 'zarincoach' ),
					'add_new_item'  => __( 'افزودن دسته‌بندی', 'zarincoach' ),
					'new_item_name' => __( 'نام دسته‌بندی جدید', 'zarincoach' ),
					'menu_name'     => __( 'دسته‌بندی خدمات', 'zarincoach' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'service-category', 'with_front' => false ),
			)
		);

		/* --------------------- تجربه مراجعان --------------------- */
		register_post_type(
			'zc_testimonial',
			array(
				'labels'              => array(
					'name'          => __( 'تجربه مراجعان', 'zarincoach' ),
					'singular_name' => __( 'تجربه مراجع', 'zarincoach' ),
					'menu_name'     => __( 'نظرات مراجعان', 'zarincoach' ),
					'add_new_item'  => __( 'افزودن نظر جدید', 'zarincoach' ),
					'edit_item'     => __( 'ویرایش نظر', 'zarincoach' ),
					'search_items'  => __( 'جستجوی نظرات', 'zarincoach' ),
					'all_items'     => __( 'همه نظرات', 'zarincoach' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'query_var'           => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 22,
				'menu_icon'           => 'dashicons-format-quote',
				'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			)
		);

		/* --------------------- پرسش‌های پرتکرار --------------------- */
		register_post_type(
			'zc_faq',
			array(
				'labels'              => array(
					'name'          => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
					'singular_name' => __( 'پرسش', 'zarincoach' ),
					'menu_name'     => __( 'سوالات پرتکرار', 'zarincoach' ),
					'add_new_item'  => __( 'افزودن پرسش جدید', 'zarincoach' ),
					'edit_item'     => __( 'ویرایش پرسش', 'zarincoach' ),
					'search_items'  => __( 'جستجوی پرسش‌ها', 'zarincoach' ),
					'all_items'     => __( 'همه پرسش‌ها', 'zarincoach' ),
				),
				'public'              => false,
				'publicly_queryable'  => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'exclude_from_search' => true,
				'query_var'           => false,
				'rewrite'             => false,
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 23,
				'menu_icon'           => 'dashicons-editor-help',
				'supports'            => array( 'title', 'editor', 'page-attributes' ),
			)
		);
	}
endif;
add_action( 'init', 'zc_register_post_types', 5 );

if ( ! function_exists( 'zc_cpt_admin_columns' ) ) :
	/**
	 * ستون‌های اختصاصی در فهرست مدیریت.
	 *
	 * @param array $columns ستون‌ها.
	 * @return array
	 */
	function zc_cpt_admin_columns( $columns ) {
		$screen = get_current_screen();

		if ( $screen && 'edit-zc_service' === $screen->id ) {
			$new = array();
			foreach ( $columns as $key => $value ) {
				$new[ $key ] = $value;
				if ( 'title' === $key ) {
					$new['zc_icon']  = __( 'آیکون', 'zarincoach' );
					$new['zc_price'] = __( 'قیمت/مدت', 'zarincoach' );
				}
			}
			return $new;
		}

		if ( $screen && 'edit-zc_testimonial' === $screen->id ) {
			$new = array();
			foreach ( $columns as $key => $value ) {
				$new[ $key ] = $value;
				if ( 'title' === $key ) {
					$new['zc_role']   = __( 'نقش/عنوان', 'zarincoach' );
					$new['zc_rating'] = __( 'امتیاز', 'zarincoach' );
				}
			}
			return $new;
		}

		return $columns;
	}
endif;
add_filter( 'manage_posts_columns', 'zc_cpt_admin_columns' );

if ( ! function_exists( 'zc_cpt_admin_column_content' ) ) :
	/**
	 * محتوای ستون‌های اختصاصی.
	 *
	 * @param string $column  نام ستون.
	 * @param int    $post_id شناسه نوشته.
	 * @return void
	 */
	function zc_cpt_admin_column_content( $column, $post_id ) {
		switch ( $column ) {
			case 'zc_icon':
				$icon = (string) get_post_meta( $post_id, '_zc_service_icon', true );
				echo esc_html( '' !== $icon ? $icon : '—' );
				break;

			case 'zc_price':
				$price    = (string) get_post_meta( $post_id, '_zc_service_price', true );
				$duration = (string) get_post_meta( $post_id, '_zc_service_duration', true );
				echo esc_html( trim( $price . ' ' . $duration ) );
				break;

			case 'zc_role':
				echo esc_html( (string) get_post_meta( $post_id, '_zc_testimonial_role', true ) );
				break;

			case 'zc_rating':
				$rating = (int) get_post_meta( $post_id, '_zc_testimonial_rating', true );
				echo esc_html( str_repeat( '★', max( 0, min( 5, $rating ) ) ) );
				break;
		}
	}
endif;
add_action( 'manage_posts_custom_column', 'zc_cpt_admin_column_content', 10, 2 );

if ( ! function_exists( 'zc_services_page_as_archive' ) ) :
	/**
	 * اگر برگه‌ای هم‌نام با آرشیو خدمات (مثلاً /services/) منتشر شده باشد،
	 * همان برگه (طراحی‌شده با المنتور) به جای آرشیو پیش‌فرض نمایش داده می‌شود.
	 * صفحه‌بندی و خوراک آرشیو همچنان کار می‌کنند.
	 *
	 * @param array $query_vars متغیرهای درخواست.
	 * @return array
	 */
	function zc_services_page_as_archive( $query_vars ) {
		if ( is_admin() || empty( $query_vars['post_type'] ) || 'zc_service' !== $query_vars['post_type'] ) {
			return $query_vars;
		}
		if ( ! empty( $query_vars['zc_service'] ) || ! empty( $query_vars['name'] ) || ! empty( $query_vars['feed'] ) || ! empty( $query_vars['paged'] ) ) {
			return $query_vars;
		}

		$object = get_post_type_object( 'zc_service' );
		$slug   = ( $object && ! empty( $object->rewrite['slug'] ) ) ? (string) $object->rewrite['slug'] : 'services';
		$page   = get_page_by_path( $slug, OBJECT, 'page' );

		if ( $page && 'publish' === $page->post_status ) {
			return array( 'page_id' => (int) $page->ID );
		}
		return $query_vars;
	}
	add_filter( 'request', 'zc_services_page_as_archive' );
endif;
