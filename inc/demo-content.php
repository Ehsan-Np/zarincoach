<?php
/**
 * موتور نصب و مدیریت محتوای اولیه (دمو).
 *
 * داده‌ها در inc/demo-data.php و صفحات قوانین در inc/demo-legal.php تعریف شده‌اند.
 * هر بار نصب، ابتدا همه‌ی آثار دموی قبلی (بر اساس نشانه‌ی متا) پاک می‌شود تا محتوای تکراری ایجاد نشود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/demo-legal.php';
require_once __DIR__ . '/demo-data.php';

if ( ! function_exists( 'zc_demo_track' ) ) :
	/**
	 * ثبت شناسه‌ی موارد ایجادشده برای حذف دقیق در آینده.
	 *
	 * @param string $type نوع.
	 * @param int    $id   شناسه.
	 * @return void
	 */
	function zc_demo_track( $type, $id ) {
		$objects = get_option( 'zc_demo_objects', array() );
		$objects = is_array( $objects ) ? $objects : array();

		if ( ! isset( $objects[ $type ] ) || ! is_array( $objects[ $type ] ) ) {
			$objects[ $type ] = array();
		}

		$id = (int) $id;
		if ( $id && ! in_array( $id, $objects[ $type ], true ) ) {
			$objects[ $type ][] = $id;
		}

		update_option( 'zc_demo_objects', $objects, false );
	}
endif;

if ( ! function_exists( 'zc_demo_media_ids' ) ) :
	/**
	 * شناسه‌ی تصاویر دموی درون‌ریزی‌شده.
	 *
	 * @return array<string,int>
	 */
	function zc_demo_media_ids() {
		$ids = array();
		foreach ( array_keys( zc_demo_media_files() ) as $key ) {
			$found = get_posts(
				array(
					'post_type'      => 'attachment',
					'post_status'    => 'inherit',
					'posts_per_page' => 1,
					'fields'         => 'ids',
					'meta_key'       => '_zc_demo_media', // phpcs:ignore WordPress.DB.SlowDBQuery
					'meta_value'     => $key,            // phpcs:ignore WordPress.DB.SlowDBQuery
					'no_found_rows'  => true,
				)
			);
			if ( ! empty( $found ) ) {
				$ids[ $key ] = (int) $found[0];
			}
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'zc_demo_import_media' ) ) :
	/**
	 * درون‌ریزی یک تصویر دمو به کتابخانه‌ی رسانه.
	 *
	 * @param string $key کلید تصویر.
	 * @return int
	 */
	function zc_demo_import_media( $key ) {
		$files = zc_demo_media_files();
		if ( ! isset( $files[ $key ] ) ) {
			return 0;
		}

		$existing = zc_demo_media_ids();
		if ( ! empty( $existing[ $key ] ) ) {
			return (int) $existing[ $key ];
		}

		$source = trailingslashit( ZC_DIR ) . 'assets/demo/' . $files[ $key ]['file'];
		if ( ! is_readable( $source ) ) {
			return 0;
		}

		$bits = file_get_contents( $source ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents -- فایل محلی قالب
		if ( false === $bits ) {
			return 0;
		}

		$upload = wp_upload_bits( 'zc-demo-' . $files[ $key ]['file'], null, $bits );
		if ( ! empty( $upload['error'] ) || empty( $upload['file'] ) ) {
			return 0;
		}

		$attachment_id = wp_insert_attachment(
			array(
				'post_mime_type' => (string) ( wp_check_filetype( $upload['file'] )['type'] ?: 'image/jpeg' ),
				'post_title'     => $files[ $key ]['title'],
				'post_content'   => '',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);

		if ( is_wp_error( $attachment_id ) || ! $attachment_id ) {
			return 0;
		}

		require_once ABSPATH . 'wp-admin/includes/image.php';

		// نشانک سایت: اندازه‌های استاندارد وردپرس (۳۲، ۱۸۰، ۱۹۲، ۲۷۰، ۵۱۲) به‌صورت PNG؛ apple-touch-icon با WebP سازگار نیست.
		$is_icon = ! empty( $files[ $key ]['icon'] );
		$icon    = null;
		if ( $is_icon ) {
			require_once ABSPATH . 'wp-admin/includes/class-wp-site-icon.php';
			$site_icon = new WP_Site_Icon();
			// فقط اندازه‌های نشانک ساخته شود، نه برش‌های محتوایی قالب.
			$icon = static function () use ( $site_icon ) {
				return $site_icon->additional_sizes( array() );
			};
			add_filter( 'intermediate_image_sizes_advanced', $icon );
			remove_filter( 'image_editor_output_format', 'zc_webp_output_format' );
		}

		wp_update_attachment_metadata( $attachment_id, wp_generate_attachment_metadata( $attachment_id, $upload['file'] ) );

		if ( $is_icon ) {
			remove_filter( 'intermediate_image_sizes_advanced', $icon );
			add_filter( 'image_editor_output_format', 'zc_webp_output_format' );
			update_post_meta( $attachment_id, '_wp_attachment_context', 'site-icon' );
		}

		update_post_meta( $attachment_id, '_wp_attachment_image_alt', $files[ $key ]['alt'] );
		update_post_meta( $attachment_id, '_zc_demo_media', $key );
		zc_demo_track( 'media', $attachment_id );

		return (int) $attachment_id;
	}
endif;

if ( ! function_exists( 'zc_demo_media_field' ) ) :
	/**
	 * خروجی تصویر در قالب فیلد رسانه‌ی Redux / المنتور.
	 *
	 * @param int $id شناسه پیوست.
	 * @return array<string, string>
	 */
	function zc_demo_media_field( $id ) {
		$id   = (int) $id;
		$full = $id ? wp_get_attachment_image_src( $id, 'full' ) : false;
		if ( ! $full ) {
			return array( 'url' => '' );
		}
		$thumb = wp_get_attachment_image_src( $id, 'thumbnail' );

		return array(
			'url'       => (string) $full[0],
			'id'        => (string) $id,
			'width'     => (string) $full[1],
			'height'    => (string) $full[2],
			'thumbnail' => $thumb ? (string) $thumb[0] : (string) $full[0],
			'title'     => get_the_title( $id ),
			'alt'       => (string) get_post_meta( $id, '_wp_attachment_image_alt', true ),
		);
	}
endif;

if ( ! function_exists( 'zc_demo_steps' ) ) :
	/**
	 * گام‌های درون‌ریزی دمو.
	 *
	 * @return array<string, string>
	 */
	function zc_demo_steps() {
		$steps = array(
			'reset'     => __( 'پاک‌سازی کامل دموی قبلی (بدون حذف محتوای شخصی شما)', 'zarincoach' ),
			'media'     => __( 'درون‌ریزی تصاویر دمو (عکس‌های مریم جمالی) به کتابخانه رسانه', 'zarincoach' ),
			'options'   => __( 'بازنشانی و اعمال تنظیمات قالب، اطلاعات تماس و پالت رنگی', 'zarincoach' ),
			'pages'     => __( 'ایجاد برگه‌ها و صفحات قوانین', 'zarincoach' ),
			'posts'     => __( 'ایجاد نوشته‌ها، دسته‌ها، برچسب‌ها و دیدگاه‌ها', 'zarincoach' ),
			'items'     => __( 'ایجاد خدمات، بازخوردها و پرسش‌های پرتکرار', 'zarincoach' ),
			'schemas'   => __( 'ایجاد کتابخانه‌ی طرحواره‌ها، ذهنیت‌ها، سبک‌های مقابله و خطاهای شناختی', 'zarincoach' ),
			'shop'      => __( 'فروشگاه: دسته‌ها، محصولات نمونه (فیزیکی، دانلودی، بسته‌ی جلسات)، کد تخفیف، ارسال و پرداخت', 'zarincoach' ),
			'menus'     => __( 'ساخت منوهای سربرگ، موبایل، پاورقی و قوانین', 'zarincoach' ),
			'elementor' => __( 'طراحی صفحات، سربرگ و پاورقی با ویجت‌های المنتور', 'zarincoach' ),
			'finalize'  => __( 'نهایی‌سازی، پیوندهای یکتا و پاک‌سازی کش', 'zarincoach' ),
		);
		if ( ! class_exists( 'WooCommerce' ) ) {
			unset( $steps['shop'] );
		}
		return $steps;
	}
endif;

if ( ! function_exists( 'zc_demo_page_ids' ) ) :
	/**
	 * شناسه برگه‌های دمو بر اساس کلید.
	 *
	 * @return array<string,int>
	 */
	function zc_demo_page_ids() {
		$ids   = array();
		$pages = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'posts_per_page'   => 100,
				'meta_key'         => '_zc_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery
				'orderby'          => 'ID',
				'order'            => 'ASC',
				'suppress_filters' => true,
			)
		);
		foreach ( $pages as $page ) {
			$key = (string) get_post_meta( $page->ID, '_zc_demo_page', true );
			if ( '' !== $key && ! isset( $ids[ $key ] ) ) {
				$ids[ $key ] = (int) $page->ID;
			}
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'zc_demo_find_post' ) ) :
	/**
	 * یافتن پست دمو بر اساس متا (برای جلوگیری از ایجاد تکراری).
	 *
	 * @param string $type     نوع پست.
	 * @param string $meta_key کلید متا.
	 * @param string $value    مقدار.
	 * @return int
	 */
	function zc_demo_find_post( $type, $meta_key, $value ) {
		$found = get_posts(
			array(
				'post_type'        => $type,
				'post_status'      => array( 'publish', 'draft', 'pending', 'private', 'future' ),
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'meta_key'         => $meta_key, // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'       => $value,    // phpcs:ignore WordPress.DB.SlowDBQuery
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);
		return ! empty( $found ) ? (int) $found[0] : 0;
	}
endif;

if ( ! function_exists( 'zc_demo_menu_names' ) ) :
	/**
	 * نام منوهایی که دمو می‌سازد (نسخه فعلی و نسخه‌های قبلی) => جایگاه.
	 *
	 * @return array<string,string>
	 */
	function zc_demo_menu_names() {
		return array(
			'منوی اصلی زرین‌کوچ'   => 'zc-primary',
			'منوی موبایل زرین‌کوچ' => 'zc-mobile',
			'منوی پاورقی زرین‌کوچ' => 'zc-footer',
			'منوی قوانین زرین‌کوچ' => 'zc-legal',
		);
	}
endif;

if ( ! function_exists( 'zc_demo_reset_content' ) ) :
	/**
	 * پاک‌سازی کامل هر آنچه دمو (نسخه فعلی یا قبلی) ساخته است.
	 *
	 * فقط مواردی حذف می‌شوند که نشانه‌ی دمو (متای _zc_demo_*) دارند یا در فهرست ردیابی ثبت شده‌اند؛
	 * بنابراین برگه‌ها، نوشته‌ها و رسانه‌هایی که کاربر خودش ساخته دست‌نخورده باقی می‌مانند.
	 *
	 * @return array<string,int> تعداد حذف‌شده‌ها بر اساس نوع.
	 */
	function zc_demo_reset_content() {
		global $wpdb;

		$stats   = array( 'posts' => 0, 'media' => 0, 'menus' => 0, 'terms' => 0, 'comments' => 0 );
		// فروشگاه پیش از بقیه (محصولات، گونه‌ها، کوپن، دسته‌ها و ناحیه‌ی ارسال).
		if ( function_exists( 'zc_demo_shop_reset' ) ) {
			$stats['posts'] += zc_demo_shop_reset();
		}
		$objects = (array) get_option( 'zc_demo_objects', array() );
		$ids     = array();

		// ۱) همه‌ی پست‌های نشان‌دار دمو (هر نوع و هر وضعیتی، حتی زباله‌دان).
		$meta_keys = array( '_zc_demo_page', '_zc_demo_post', '_zc_demo_item', '_zc_demo_template' );
		$in_keys   = implode( ',', array_fill( 0, count( $meta_keys ), '%s' ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$found = $wpdb->get_col( $wpdb->prepare( "SELECT DISTINCT post_id FROM {$wpdb->postmeta} WHERE meta_key IN ($in_keys)", $meta_keys ) );
		foreach ( (array) $found as $id ) {
			$ids[ (int) $id ] = true;
		}
		foreach ( array( 'pages', 'posts', 'services', 'testimonials', 'faqs', 'schemas', 'templates' ) as $type ) {
			foreach ( isset( $objects[ $type ] ) ? (array) $objects[ $type ] : array() as $id ) {
				$ids[ (int) $id ] = true;
			}
		}

		// برگه‌ها و نوشته‌های پیش‌فرض وردپرس (سلام دنیا، برگه نمونه، پیش‌نویس حریم خصوصی).
		foreach ( array( array( 'hello-world', 'post', '' ), array( 'sample-page', 'page', '' ), array( 'privacy-policy', 'page', 'draft' ) ) as $default ) {
			$post = get_page_by_path( $default[0], OBJECT, $default[1] );
			if ( $post && ( '' === $default[2] || $default[2] === $post->post_status ) && ! get_post_meta( $post->ID, '_zc_demo_page', true ) ) {
				$ids[ (int) $post->ID ] = true;
			}
		}

		// اصطلاحات متصل به نوشته‌ها/خدمات دمو، پیش از حذف (برای پاک کردن دسته و برچسب‌های خالی‌شده).
		$terms = array();
		foreach ( array_keys( $ids ) as $id ) {
			foreach ( array( 'category', 'post_tag', 'zc_service_cat' ) as $taxonomy ) {
				if ( ! taxonomy_exists( $taxonomy ) ) {
					continue;
				}
				$attached = wp_get_object_terms( $id, $taxonomy, array( 'fields' => 'ids' ) );
				if ( ! is_wp_error( $attached ) ) {
					foreach ( $attached as $term_id ) {
						$terms[ $taxonomy ][ (int) $term_id ] = true;
					}
				}
			}
		}

		// دیدگاه‌های دمو.
		$comment_ids = get_comments(
			array(
				'meta_key' => '_zc_demo_comment', // phpcs:ignore WordPress.DB.SlowDBQuery
				'fields'   => 'ids',
				'status'   => 'all',
				'number'   => 0,
			)
		);
		foreach ( (array) $comment_ids as $comment_id ) {
			if ( wp_delete_comment( (int) $comment_id, true ) ) {
				$stats['comments']++;
			}
		}

		// اگر برگه‌ی نخست/نوشته‌ها از موارد حذفی است، تنظیمات خواندن بازنشانی می‌شود.
		foreach ( array( 'page_on_front', 'page_for_posts', 'wp_page_for_privacy_policy', 'zc_blog_page' ) as $option ) {
			if ( isset( $ids[ (int) get_option( $option ) ] ) ) {
				update_option( $option, 0 );
			}
		}
		if ( ! (int) get_option( 'page_on_front' ) ) {
			update_option( 'show_on_front', 'posts' );
		}

		foreach ( array_keys( $ids ) as $id ) {
			$post = get_post( $id );
			if ( ! $post || 'attachment' === $post->post_type ) {
				continue;
			}
			if ( wp_delete_post( $id, true ) ) {
				$stats['posts']++;
			}
		}

		// ۲) رسانه‌های دمو (متای _zc_demo_media، فهرست ردیابی و فایل‌های قدیمی با پیشوند zc-demo-).
		$media = array();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = %s", '_zc_demo_media' ) ) as $id ) {
			$media[ (int) $id ] = true;
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		foreach ( (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s", '%' . $wpdb->esc_like( 'zc-demo-' ) . '%' ) ) as $id ) {
			$media[ (int) $id ] = true;
		}
		foreach ( isset( $objects['media'] ) ? (array) $objects['media'] : array() as $id ) {
			$media[ (int) $id ] = true;
		}
		foreach ( array_keys( $media ) as $id ) {
			$post = get_post( $id );
			if ( $post && 'attachment' === $post->post_type && wp_delete_attachment( $id, true ) ) {
				$stats['media']++;
			}
		}

		// ۳) منوهای دمو.
		$menus = isset( $objects['menus'] ) ? array_map( 'intval', (array) $objects['menus'] ) : array();
		foreach ( array_keys( zc_demo_menu_names() ) as $name ) {
			$menu = wp_get_nav_menu_object( $name );
			if ( $menu ) {
				$menus[] = (int) $menu->term_id;
			}
		}
		foreach ( array_unique( $menus ) as $menu_id ) {
			if ( $menu_id && wp_get_nav_menu_object( $menu_id ) && ! is_wp_error( wp_delete_nav_menu( $menu_id ) ) ) {
				$stats['menus']++;
			}
		}

		// ۴) دسته‌ها و برچسب‌های خالی‌شده + دسته‌های ردیابی‌شده.
		foreach ( array( 'categories' => 'category', 'service_cats' => 'zc_service_cat', 'schema_groups' => 'zc_schema_group' ) as $type => $taxonomy ) {
			foreach ( isset( $objects[ $type ] ) ? (array) $objects[ $type ] : array() as $term_id ) {
				$terms[ $taxonomy ][ (int) $term_id ] = true;
			}
		}
		foreach ( zc_demo_post_categories() as $cat ) {
			$term = get_term_by( 'slug', $cat['slug'], 'category' );
			if ( $term ) {
				$terms['category'][ (int) $term->term_id ] = true;
			}
		}
		if ( taxonomy_exists( 'zc_service_cat' ) ) {
			foreach ( array_merge( wp_list_pluck( zc_demo_service_cats(), 'slug' ), array( 'coaching', 'therapy' ) ) as $slug ) {
				$term = get_term_by( 'slug', $slug, 'zc_service_cat' );
				if ( $term ) {
					$terms['zc_service_cat'][ (int) $term->term_id ] = true;
				}
			}
		}
		// گروه‌های طرحواره: فرزندان پیش از والدها حذف می‌شوند.
		if ( taxonomy_exists( 'zc_schema_group' ) && function_exists( 'zc_sc_groups' ) ) {
			$sc_groups = zc_sc_groups();
			uasort(
				$sc_groups,
				static function ( $a, $b ) {
					return (int) empty( $a['parent'] ) - (int) empty( $b['parent'] );
				}
			);
			$sc_terms = array();
			foreach ( array_keys( $sc_groups ) as $slug ) {
				$term = get_term_by( 'slug', $slug, 'zc_schema_group' );
				if ( $term ) {
					$sc_terms[ (int) $term->term_id ] = true;
				}
			}
			foreach ( isset( $terms['zc_schema_group'] ) ? $terms['zc_schema_group'] : array() as $term_id => $on ) {
				$sc_terms[ (int) $term_id ] = true;
			}
			$terms['zc_schema_group'] = $sc_terms;
		}
		$default_cat = (int) get_option( 'default_category' );
		foreach ( $terms as $taxonomy => $list ) {
			foreach ( array_keys( $list ) as $term_id ) {
				if ( 'category' === $taxonomy && $term_id === $default_cat ) {
					continue;
				}
				clean_term_cache( $term_id, $taxonomy );
				$term = get_term( $term_id, $taxonomy );
				if ( $term && ! is_wp_error( $term ) && 0 === (int) $term->count && ! is_wp_error( wp_delete_term( $term_id, $taxonomy ) ) ) {
					$stats['terms']++;
				}
			}
		}

		delete_option( 'zc_demo_objects' );
		delete_option( 'zc_demo_installed' );
		delete_option( 'zc_demo_version' );
		delete_transient( 'zc_dynamic_css_' . ZC_VERSION );

		return $stats;
	}
endif;

if ( ! function_exists( 'zc_demo_assign_terms' ) ) :
	/**
	 * ایجاد (یا یافتن) یک اصطلاح دمو و بازگرداندن شناسه‌ی آن.
	 *
	 * @param string $name     نام.
	 * @param string $slug     نامک.
	 * @param string $taxonomy طبقه‌بندی.
	 * @param string $track    کلید ردیابی.
	 * @return int
	 */
	function zc_demo_assign_terms( $name, $slug, $taxonomy, $track ) {
		if ( ! taxonomy_exists( $taxonomy ) ) {
			return 0;
		}
		$term = get_term_by( 'slug', $slug, $taxonomy );
		if ( $term ) {
			return (int) $term->term_id;
		}
		$term = wp_insert_term( $name, $taxonomy, array( 'slug' => $slug ) );
		if ( is_wp_error( $term ) ) {
			return 0;
		}
		zc_demo_track( $track, (int) $term['term_id'] );
		return (int) $term['term_id'];
	}
endif;

if ( ! function_exists( 'zc_demo_run_step' ) ) :
	/**
	 * اجرای یک گام از درون‌ریزی.
	 *
	 * @param string              $step گام.
	 * @param array<string,mixed> $args تنظیمات (palette، elementor).
	 * @return array{done:bool, more:bool, message:string}
	 */
	function zc_demo_run_step( $step, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'palette'   => 'navy',
				'elementor' => true,
			)
		);

		if ( function_exists( 'set_time_limit' ) ) {
			@set_time_limit( 180 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		wp_raise_memory_limit( 'admin' );

		$author = get_current_user_id() ? get_current_user_id() : 1;
		if ( 'posts' === $step ) {
			zc_demo_prepare_author( $author );
		}

		switch ( $step ) {
			/* ------------------------------------------------ پاک‌سازی */
			case 'reset':
				$stats = zc_demo_reset_content();
				return array(
					'done'    => true,
					'more'    => false,
					'message' => sprintf(
						/* translators: 1: posts 2: media 3: menus 4: terms 5: comments */
						__( 'دموی قبلی پاک شد: %1$d محتوا، %2$d تصویر، %3$d منو، %4$d دسته/برچسب و %5$d دیدگاه.', 'zarincoach' ),
						$stats['posts'],
						$stats['media'],
						$stats['menus'],
						$stats['terms'],
						$stats['comments']
					),
				);

			/* ------------------------------------------------ تصاویر */
			case 'media':
				$files    = zc_demo_media_files();
				$existing = zc_demo_media_ids();
				$batch    = 0;

				foreach ( array_keys( $files ) as $key ) {
					if ( ! empty( $existing[ $key ] ) ) {
						continue;
					}
					zc_demo_import_media( $key );
					$batch++;
					if ( $batch >= 3 ) {
						break;
					}
				}

				$total = count( zc_demo_media_ids() );
				$more  = $total < count( $files ) && $batch > 0;

				return array(
					'done'    => true,
					'more'    => $more,
					/* translators: 1: تعداد درون‌ریزی‌شده 2: تعداد کل */
					'message' => sprintf( __( '%1$d از %2$d تصویر درون‌ریزی شد.', 'zarincoach' ), $total, count( $files ) ),
				);

			/* ------------------------------------------------ تنظیمات */
			case 'options':
				$current   = get_option( ZC_OPT, array() );
				$current   = is_array( $current ) ? $current : array();
				$preserved = array_intersect_key( $current, array_flip( zc_demo_preserved_options() ) );
				$preserved = array_filter(
					$preserved,
					static function ( $value ) {
						return ! ( '' === $value || null === $value || ( is_array( $value ) && empty( array_filter( $value ) ) ) );
					}
				);

				// بازنشانی کامل به پیش‌فرض‌ها + حفظ موارد شخصی (لوگو، کدها، مجوزها) + مقادیر دمو.
				$values = array_merge( zc_field_defaults(), zc_demo_options( zc_demo_media_ids(), (string) $args['palette'] ), $preserved );
				// نشانک سایت (favicon + لوگوی اسکیما) فقط اگر مدیر سایت نشانک دیگری تنظیم نکرده باشد.
				$zc_media_ids = zc_demo_media_ids();
				$zc_icon_now  = (int) get_option( 'site_icon' );
				if ( ! empty( $zc_media_ids['site-icon'] ) && ( ! $zc_icon_now || ! get_post( $zc_icon_now ) || get_post_meta( $zc_icon_now, '_zc_demo_media', true ) ) ) {
					update_option( 'site_icon', (int) $zc_media_ids['site-icon'] );
				}

				update_option( ZC_OPT, $values );
				zc_opt_flush();
				delete_transient( 'zc_dynamic_css_' . ZC_VERSION );

				// هماهنگی المنتور با رنگ و فونت قالب.
				update_option( 'elementor_disable_color_schemes', 'yes' );
				update_option( 'elementor_disable_typography_schemes', 'yes' );
				// تنظیمات سرعت المنتور (بدون Google Fonts، آیکن SVG، CSS خارجی قابل کش).
				if ( function_exists( 'zc_el_recommended_settings' ) ) {
					zc_el_recommended_settings();
				}

				update_option( 'blogname', 'مریم جمالی' );
				update_option( 'blogdescription', 'روان‌شناس الگوهای ذهنی و رفتاری در مسیر رشد' );

				return array(
					'done'    => true,
					'more'    => false,
					'message' => __( 'تنظیمات قالب بازنشانی و اطلاعات واقعی تماس، پالت رنگی و تصاویر اعمال شد.', 'zarincoach' ),
				);

			/* ------------------------------------------------ برگه‌ها */
			case 'pages':
				$media = zc_demo_media_ids();
				$count = 0;

				foreach ( zc_demo_pages( $media ) as $key => $page ) {
					$found = zc_demo_find_post( 'page', '_zc_demo_page', $key );
					$data  = array(
						'post_type'      => 'page',
						'post_status'    => 'publish',
						'post_title'     => $page['title'],
						'post_name'      => $page['slug'],
						'post_content'   => (string) $page['content'],
						'post_excerpt'   => isset( $page['excerpt'] ) ? (string) $page['excerpt'] : '',
						'post_author'    => $author,
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
					);

					if ( $found ) {
						$data['ID'] = $found;
						$page_id    = wp_update_post( wp_slash( $data ), true );
					} else {
						$page_id = wp_insert_post( wp_slash( $data ), true );
					}

					if ( is_wp_error( $page_id ) || ! $page_id ) {
						continue;
					}

					$page_id = (int) $page_id;
					update_post_meta( $page_id, '_zc_demo_page', $key );
					if ( ! empty( $page['template'] ) ) {
						update_post_meta( $page_id, '_wp_page_template', $page['template'] );
					}
					if ( ! empty( $page['legal'] ) ) {
						update_post_meta( $page_id, '_zc_legal_page', '1' );
					}
					// سئوی اختصاصی برگه (جعبه‌ی «سئو و اسکیما»).
					foreach ( array( 'title', 'desc', 'schema' ) as $zc_seo_key ) {
						if ( ! empty( $page['seo'][ $zc_seo_key ] ) ) {
							update_post_meta( $page_id, '_zc_seo_' . $zc_seo_key, $page['seo'][ $zc_seo_key ] );
						} else {
							delete_post_meta( $page_id, '_zc_seo_' . $zc_seo_key );
						}
					}
					zc_demo_track( 'pages', $page_id );
					$count++;

					if ( ! empty( $page['front'] ) ) {
						update_option( 'show_on_front', 'page' );
						update_option( 'page_on_front', $page_id );
					}
					if ( ! empty( $page['posts'] ) ) {
						update_option( 'page_for_posts', $page_id );
					}
				}

				$page_ids = zc_demo_page_ids();
				if ( ! empty( $page_ids['privacy'] ) ) {
					update_option( 'wp_page_for_privacy_policy', (int) $page_ids['privacy'] );
				}

				return array(
					'done'    => true,
					'more'    => false,
					/* translators: 1: تعداد برگه، 2: تعداد صفحات قوانین */
					'message' => sprintf( __( '%1$d برگه (شامل %2$d صفحه قوانین) ایجاد شد.', 'zarincoach' ), $count, count( zc_demo_legal_pages() ) ),
				);

			/* ------------------------------------------------ نوشته‌ها */
			case 'posts':
				$media = zc_demo_media_ids();
				$cats  = array();
				foreach ( zc_demo_post_categories() as $cat_key => $cat ) {
					$cats[ $cat_key ] = zc_demo_assign_terms( $cat['name'], $cat['slug'], 'category', 'categories' );
				}

				$count = 0;
				$index = 0;
				$posts = zc_demo_posts();
				$total = count( $posts );

				foreach ( $posts as $post_data ) {
					$index++;
					if ( zc_demo_find_post( 'post', '_zc_demo_post', $post_data['slug'] ) ) {
						continue;
					}

					$cat_id = ! empty( $post_data['category'] ) && ! empty( $cats[ $post_data['category'] ] ) ? $cats[ $post_data['category'] ] : (int) get_option( 'default_category' );

					// تاریخ‌ها با فاصله‌ی چندروزه (جدیدترین = اولین).
					$date = gmdate( 'Y-m-d H:i:s', time() - ( ( $index - 1 ) * 5 + 1 ) * DAY_IN_SECONDS - $index * 3600 );

					$post_id = wp_insert_post(
						wp_slash(
							array(
								'post_type'      => 'post',
								'post_status'    => 'publish',
								'post_title'     => $post_data['title'],
								'post_name'      => $post_data['slug'],
								'post_excerpt'   => $post_data['excerpt'],
								'post_content'   => $post_data['content'],
								'post_category'  => array( $cat_id ),
								'post_author'    => $author,
								'post_date_gmt'  => $date,
								'post_date'      => get_date_from_gmt( $date ),
								'comment_status' => 'open',
							)
						),
						true
					);

					if ( is_wp_error( $post_id ) || ! $post_id ) {
						continue;
					}

					$post_id = (int) $post_id;
					update_post_meta( $post_id, '_zc_demo_post', $post_data['slug'] );
					zc_demo_track( 'posts', $post_id );
					$count++;

					if ( ! empty( $post_data['tags'] ) ) {
						wp_set_post_tags( $post_id, (array) $post_data['tags'], false );
					}
					if ( ! empty( $post_data['image'] ) && ! empty( $media[ $post_data['image'] ] ) ) {
						set_post_thumbnail( $post_id, (int) $media[ $post_data['image'] ] );
					}

					zc_demo_add_comments( $post_id, $index, $date );
				}

				/* translators: 1: تعداد 2: کل */
				return array( 'done' => true, 'more' => false, 'message' => sprintf( __( '%1$d نوشته از %2$d ایجاد شد (همراه با دسته، برچسب، تصویر شاخص و دیدگاه).', 'zarincoach' ), $count, $total ) );

			/* ------------------------------------------------ خدمات، بازخوردها، پرسش‌ها */
			case 'items':
				$media   = zc_demo_media_ids();
				$counts  = array( 0, 0, 0 );
				$cat_ids = array();
				foreach ( zc_demo_service_cats() as $cat_key => $cat ) {
					$cat_ids[ $cat_key ] = zc_demo_assign_terms( $cat['name'], $cat['slug'], 'zc_service_cat', 'service_cats' );
				}

				$order = 10;
				foreach ( zc_demo_services() as $service ) {
					if ( zc_demo_find_post( 'zc_service', '_zc_demo_item', 'service:' . $service['slug'] ) ) {
						$order += 10;
						continue;
					}

					$service_id = wp_insert_post(
						wp_slash(
							array(
								'post_type'    => 'zc_service',
								'post_status'  => 'publish',
								'post_title'   => $service['title'],
								'post_name'    => $service['slug'],
								'post_excerpt' => $service['excerpt'],
								'post_content' => $service['content'] . zc_demo_service_extra( $service ),
								'menu_order'   => $order,
								'post_author'  => $author,
							)
						),
						true
					);

					if ( is_wp_error( $service_id ) || ! $service_id ) {
						continue;
					}

					$service_id = (int) $service_id;
					update_post_meta( $service_id, '_zc_service_icon', $service['icon'] );
					update_post_meta( $service_id, '_zc_service_price', $service['price'] );
					update_post_meta( $service_id, '_zc_service_duration', $service['duration'] );
					update_post_meta( $service_id, '_zc_service_badge', $service['badge'] );
					update_post_meta( $service_id, '_zc_service_order', $order );
					update_post_meta( $service_id, '_zc_demo_item', 'service:' . $service['slug'] );

					if ( ! empty( $service['image'] ) && ! empty( $media[ $service['image'] ] ) ) {
						set_post_thumbnail( $service_id, (int) $media[ $service['image'] ] );
					}
					if ( ! empty( $service['cat'] ) && ! empty( $cat_ids[ $service['cat'] ] ) ) {
						wp_set_object_terms( $service_id, array( $cat_ids[ $service['cat'] ] ), 'zc_service_cat' );
					}

					zc_demo_track( 'services', $service_id );
					$counts[0]++;
					$order += 10;
				}

				foreach ( zc_demo_testimonials() as $i => $testimonial ) {
					$marker = 'testimonial:' . ( $i + 1 );
					if ( zc_demo_find_post( 'zc_testimonial', '_zc_demo_item', $marker ) ) {
						continue;
					}

					$item_id = wp_insert_post(
						wp_slash(
							array(
								'post_type'    => 'zc_testimonial',
								'post_status'  => 'publish',
								'post_title'   => $testimonial['name'],
								'post_content' => $testimonial['content'],
								'menu_order'   => ( $i + 1 ) * 10,
								'post_author'  => $author,
							)
						),
						true
					);

					if ( is_wp_error( $item_id ) || ! $item_id ) {
						continue;
					}

					update_post_meta( (int) $item_id, '_zc_testimonial_role', $testimonial['role'] );
					update_post_meta( (int) $item_id, '_zc_testimonial_rating', (int) $testimonial['rating'] );
					update_post_meta( (int) $item_id, '_zc_demo_item', $marker );
					zc_demo_track( 'testimonials', (int) $item_id );
					$counts[1]++;
				}

				foreach ( zc_demo_faqs() as $i => $faq ) {
					$marker = 'faq:' . ( $i + 1 );
					if ( zc_demo_find_post( 'zc_faq', '_zc_demo_item', $marker ) ) {
						continue;
					}
					$faq_id = wp_insert_post(
						wp_slash(
							array(
								'post_type'    => 'zc_faq',
								'post_status'  => 'publish',
								'post_title'   => $faq['question'],
								'post_content' => $faq['answer'],
								'menu_order'   => ( $i + 1 ) * 10,
								'post_author'  => $author,
							)
						),
						true
					);

					if ( ! is_wp_error( $faq_id ) && $faq_id ) {
						update_post_meta( (int) $faq_id, '_zc_demo_item', $marker );
						zc_demo_track( 'faqs', (int) $faq_id );
						$counts[2]++;
					}
				}

				return array(
					'done'    => true,
					'more'    => false,
					/* translators: 1: خدمات 2: نظرات 3: پرسش‌ها */
					'message' => sprintf( __( '%1$d خدمت، %2$d بازخورد و %3$d پرسش پرتکرار ایجاد شد.', 'zarincoach' ), $counts[0], $counts[1], $counts[2] ),
				);

			/* ------------------------------------------------ کتابخانه‌ی طرحواره‌ها */
			case 'schemas':
				if ( ! function_exists( 'zc_demo_install_schemas' ) || ! post_type_exists( 'zc_schema' ) ) {
					return array( 'done' => true, 'more' => false, 'message' => __( 'ماژول طرحواره‌ها در دسترس نیست.', 'zarincoach' ) );
				}
				$sc_result = zc_demo_install_schemas( $author );
				return array(
					'done'    => true,
					'more'    => false,
					/* translators: 1: ایجادشده 2: کل */
					'message' => sprintf( __( '%1$d مدخل از %2$d مدخل کتابخانه (۱۸ طرحواره‌ی ناسازگار اولیه، ۱۴ ذهنیت، ۳ سبک مقابله و ۱۲ خطای شناختی) ایجاد شد.', 'zarincoach' ), $sc_result[0], $sc_result[1] ),
				);

			/* ------------------------------------------------ منوها */
			case 'menus':
				$pages = zc_demo_page_ids();

				$services = get_posts(
					array(
						'post_type'        => 'zc_service',
						'post_status'      => 'publish',
						'posts_per_page'   => 12,
						'orderby'          => 'menu_order',
						'order'            => 'ASC',
						'meta_key'         => '_zc_demo_item', // phpcs:ignore WordPress.DB.SlowDBQuery
						'meta_compare'     => 'LIKE',
						'meta_value'       => 'service:',      // phpcs:ignore WordPress.DB.SlowDBQuery
						'suppress_filters' => true,
					)
				);
				$children = array();
				foreach ( $services as $service ) {
					$children[] = array( 'type' => 'post', 'object' => 'zc_service', 'id' => $service->ID, 'title' => $service->post_title );
				}

				$page_item = static function ( $key, $title = '', $sub = array() ) use ( $pages ) {
					if ( empty( $pages[ $key ] ) ) {
						return null;
					}
					return array( 'type' => 'post', 'object' => 'page', 'id' => $pages[ $key ], 'title' => $title, 'children' => array_values( array_filter( $sub ) ) );
				};
				$home = array( 'type' => 'custom', 'url' => home_url( '/' ), 'title' => __( 'خانه', 'zarincoach' ) );

				// فروشگاه (با ووکامرس): برگه‌ی فروشگاه + دسته‌های محصول.
				$shop_item   = null;
				$shop_flat   = null;
				$shop_page   = function_exists( 'wc_get_page_id' ) ? (int) wc_get_page_id( 'shop' ) : 0;
				if ( $shop_page > 0 && function_exists( 'zc_demo_shop_categories' ) ) {
					$shop_children = array( array( 'type' => 'post', 'object' => 'page', 'id' => $shop_page, 'title' => __( 'همه‌ی محصولات', 'zarincoach' ) ) );
					foreach ( array_keys( zc_demo_shop_categories() ) as $shop_slug ) {
						$shop_term = get_term_by( 'slug', $shop_slug, 'product_cat' );
						if ( $shop_term ) {
							$shop_children[] = array( 'type' => 'tax', 'object' => 'product_cat', 'id' => (int) $shop_term->term_id, 'title' => $shop_term->name );
						}
					}
					$shop_item = array( 'type' => 'post', 'object' => 'page', 'id' => $shop_page, 'title' => __( 'فروشگاه', 'zarincoach' ), 'children' => $shop_children );
					$shop_flat = array( 'type' => 'post', 'object' => 'page', 'id' => $shop_page, 'title' => __( 'فروشگاه', 'zarincoach' ), 'children' => array() );
				}

				// زیرمنوی کتابخانه‌ی طرحواره‌ها: برگه‌ی مرکزی + چهار دسته‌ی اصلی.
				$sc_children = array( $page_item( 'schemas', __( 'همه‌ی طرحواره‌ها و الگوها', 'zarincoach' ) ) );
				if ( function_exists( 'zc_sc_top_groups' ) ) {
					foreach ( zc_sc_top_groups() as $sc_slug => $sc_group ) {
						$sc_term = get_term_by( 'slug', $sc_slug, 'zc_schema_group' );
						if ( $sc_term ) {
							$sc_children[] = array( 'type' => 'tax', 'object' => 'zc_schema_group', 'id' => (int) $sc_term->term_id, 'title' => $sc_group['name'] );
						}
					}
				}

				$primary = array_filter(
					array(
						$home,
						$page_item(
							'about',
							'',
							array(
								$page_item( 'about', __( 'داستان من', 'zarincoach' ) ),
								$page_item( 'resume', __( 'رزومه و سوابق', 'zarincoach' ) ),
							)
						),
						$page_item( 'services', '', $children ),
						$page_item( 'schemas', __( 'طرحواره‌ها', 'zarincoach' ), $sc_children ),
						$page_item(
							'assessments',
							__( 'تست‌ها و دوره‌ها', 'zarincoach' ),
							array(
								$page_item( 'assessments', __( 'تست‌ها و ارزیابی‌ها', 'zarincoach' ) ),
								$page_item( 'courses', __( 'دوره‌ها و کارگاه‌ها', 'zarincoach' ) ),
							)
						),
						$page_item( 'managers', __( 'ویژه مدیران', 'zarincoach' ) ),
						$shop_item,
						$page_item( 'blog', __( 'مجله', 'zarincoach' ) ),
						$page_item( 'contact', __( 'تماس', 'zarincoach' ) ),
					)
				);

				$mobile = array_filter(
					array(
						$home,
						$page_item( 'about' ),
						$page_item( 'resume', __( 'رزومه و سوابق', 'zarincoach' ) ),
						$page_item( 'services' ),
						$page_item( 'schemas', __( 'طرحواره‌ها و الگوهای ذهنی', 'zarincoach' ) ),
						$page_item( 'assessments', __( 'تست‌ها و ارزیابی‌ها', 'zarincoach' ) ),
						$page_item( 'courses', __( 'دوره‌ها و کارگاه‌ها', 'zarincoach' ) ),
						$page_item( 'managers', __( 'ویژه مدیران', 'zarincoach' ) ),
						$shop_flat,
						$page_item( 'blog', __( 'مجله', 'zarincoach' ) ),
						$page_item( 'faq' ),
						$page_item( 'booking', __( 'رزرو نوبت', 'zarincoach' ) ),
						$page_item( 'contact', __( 'تماس', 'zarincoach' ) ),
					)
				);

				$footer = array_filter(
					array(
						$page_item( 'about' ),
						$page_item( 'resume', __( 'رزومه', 'zarincoach' ) ),
						$page_item( 'services' ),
						$page_item( 'schemas', __( 'طرحواره‌ها', 'zarincoach' ) ),
						$page_item( 'assessments', __( 'تست‌ها', 'zarincoach' ) ),
						$page_item( 'courses', __( 'دوره‌ها', 'zarincoach' ) ),
						$page_item( 'managers', __( 'ویژه مدیران', 'zarincoach' ) ),
						$page_item( 'blog', __( 'مجله', 'zarincoach' ) ),
						$page_item( 'faq' ),
						$page_item( 'booking', __( 'رزرو نوبت', 'zarincoach' ) ),
						$page_item( 'contact', __( 'تماس', 'zarincoach' ) ),
					)
				);

				$legal = array();
				foreach ( zc_demo_legal_pages() as $legal_key => $legal_page ) {
					$legal[] = $page_item( $legal_key, ! empty( $legal_page['menu'] ) ? $legal_page['menu'] : '' );
				}
				$legal = array_filter( $legal );

				$made  = 0;
				$names = array_flip( zc_demo_menu_names() );
				foreach ( array(
					'zc-primary' => $primary,
					'zc-mobile'  => $mobile,
					'zc-footer'  => $footer,
					'zc-legal'   => $legal,
				) as $location => $items ) {
					$menu_id = zc_create_menu( $names[ $location ], $items, $location );
					if ( $menu_id ) {
						zc_demo_track( 'menus', $menu_id );
						$made++;
					}
				}

				// ستون‌های ابزارک پاورقی خالی می‌شوند تا طراحی قالب/المنتور نمایش داده شود.
				$sidebars = (array) get_option( 'sidebars_widgets', array() );
				foreach ( array( 'zc-footer-1', 'zc-footer-2', 'zc-footer-3' ) as $sidebar_id ) {
					if ( ! empty( $sidebars[ $sidebar_id ] ) ) {
						$sidebars['wp_inactive_widgets'] = array_merge( isset( $sidebars['wp_inactive_widgets'] ) ? (array) $sidebars['wp_inactive_widgets'] : array(), (array) $sidebars[ $sidebar_id ] );
					}
					$sidebars[ $sidebar_id ] = array();
				}
				update_option( 'sidebars_widgets', $sidebars );

				/* translators: %d: تعداد منو */
				return array( 'done' => true, 'more' => false, 'message' => sprintf( __( '%d منو ساخته و به جایگاه‌ها اختصاص داده شد.', 'zarincoach' ), $made ) );

			/* ------------------------------------------------ المنتور */
			case 'elementor':
				if ( ! zc_is_elementor_active() ) {
					return array( 'done' => true, 'more' => false, 'message' => __( 'المنتور فعال نیست؛ صفحات با طراحی داخلی قالب نمایش داده می‌شوند.', 'zarincoach' ) );
				}
				if ( empty( $args['elementor'] ) ) {
					return array( 'done' => true, 'more' => false, 'message' => __( 'ساخت صفحات المنتور به انتخاب شما رد شد.', 'zarincoach' ) );
				}

				zc_opt_flush();
				$built = 0;
				$pages = zc_demo_page_ids();
				foreach ( zc_demo_pages() as $key => $page ) {
					if ( empty( $page['elementor'] ) || empty( $pages[ $key ] ) || ! function_exists( $page['elementor'] ) ) {
						continue;
					}
					if ( call_user_func( $page['elementor'], (int) $pages[ $key ] ) ) {
						$built++;

						// وبلاگ المنتوری: برگه‌ی عادی با ویجت «آخرین نوشته‌ها» جای «برگه نوشته‌ها» را می‌گیرد.
						if ( ! empty( $page['posts'] ) ) {
							if ( (int) get_option( 'page_for_posts' ) === (int) $pages[ $key ] ) {
								update_option( 'page_for_posts', 0 );
							}
							update_option( 'zc_blog_page', (int) $pages[ $key ] );
						}
					}
				}

				$templates = 0;
				if ( function_exists( 'zc_build_elementor_layout_template' ) ) {
					foreach ( array( 'header', 'footer' ) as $location ) {
						if ( zc_build_elementor_layout_template( $location ) ) {
							$templates++;
						}
					}
				}

				return array( 'done' => true, 'more' => false, 'message' => sprintf( /* translators: 1: pages 2: templates */ __( '%1$d صفحه و %2$d قالب (سربرگ و پاورقی) با ویجت‌های اختصاصی المنتور طراحی شد.', 'zarincoach' ), $built, $templates ) );

			/* ------------------------------------------------ فروشگاه */
			case 'shop':
				if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'zc_demo_shop_install' ) ) {
					return array( 'done' => true, 'more' => false, 'message' => __( 'ووکامرس فعال نیست؛ دموی فروشگاه رد شد.', 'zarincoach' ) );
				}
				return array( 'done' => true, 'more' => false, 'message' => zc_demo_shop_install() );

			/* ------------------------------------------------ نهایی‌سازی */
			case 'finalize':
				if ( '' === (string) get_option( 'permalink_structure' ) ) {
					update_option( 'permalink_structure', '/%postname%/' );
				}
				if ( function_exists( 'zc_register_post_types' ) ) {
					zc_register_post_types();
				}
				if ( function_exists( 'zc_register_schema_type' ) ) {
					zc_register_schema_type();
				}
				flush_rewrite_rules( false );

				// Yoast SEO: نماینده‌ی سایت = شخص (مریم جمالی) تا اطلاعات حرفه‌ای قالب در همان گره‌ی شخص ادغام شود.
				if ( class_exists( 'WPSEO_Options' ) ) {
					WPSEO_Options::set( 'company_or_person', 'person' );
					WPSEO_Options::set( 'company_or_person_user_id', (int) $author );
					WPSEO_Options::set( 'breadcrumbs-enable', true );
				}

				update_option( 'zc_demo_installed', '1' );
				update_option( 'zc_demo_version', zc_demo_version() );
				update_option( 'zc_demo_installed_at', time() );
				delete_transient( 'zc_dynamic_css_' . ZC_VERSION );
				delete_transient( function_exists( 'zc_field_defaults_key' ) ? zc_field_defaults_key() : 'zc_field_defaults_' . ZC_VERSION );
				zc_opt_flush();

				if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
					\Elementor\Plugin::$instance->files_manager->clear_cache();
					delete_option( 'zc_widget_usage' );
				}

				return array( 'done' => true, 'more' => false, 'message' => __( 'دمو با موفقیت و از صفر نصب شد.', 'zarincoach' ) );
		}

		return array( 'done' => false, 'more' => false, 'message' => __( 'گام نامعتبر است.', 'zarincoach' ) );
	}
endif;


if ( ! function_exists( 'zc_install_demo_content' ) ) :
	/**
	 * نصب کامل دمو در یک مرحله (هنگام فعال‌سازی قالب یا WP-CLI).
	 *
	 * @param bool                $force اجبار به اجرای دوباره.
	 * @param array<string,mixed> $args  تنظیمات.
	 * @return array{created:int, skipped:bool}
	 */
	function zc_install_demo_content( $force = false, $args = array() ) {
		if ( ! $force && '1' === (string) get_option( 'zc_demo_installed' ) ) {
			return array( 'created' => 0, 'skipped' => true );
		}

		if ( ! current_user_can( 'manage_options' ) && ! wp_doing_cron() && ! ( defined( 'WP_CLI' ) && WP_CLI ) ) {
			return array( 'created' => 0, 'skipped' => true );
		}

		foreach ( array_keys( zc_demo_steps() ) as $step ) {
			$guard = 0;
			do {
				$result = zc_demo_run_step( $step, $args );
				$guard++;
			} while ( ! empty( $result['more'] ) && $guard < 10 );
		}

		$objects = (array) get_option( 'zc_demo_objects', array() );
		$count   = 0;
		foreach ( array( 'pages', 'posts', 'services', 'testimonials', 'faqs', 'schemas' ) as $type ) {
			$count += isset( $objects[ $type ] ) ? count( (array) $objects[ $type ] ) : 0;
		}

		return array( 'created' => $count, 'skipped' => false );
	}
endif;

if ( ! function_exists( 'zc_site_is_fresh' ) ) :
	/**
	 * آیا سایت تازه نصب است؟ (برای نصب خودکار دمو هنگام فعال‌سازی قالب)
	 *
	 * @return bool
	 */
	function zc_site_is_fresh() {
		$counts = wp_count_posts( 'post' );
		$pages  = wp_count_posts( 'page' );
		$total  = ( isset( $counts->publish ) ? (int) $counts->publish : 0 ) + ( isset( $pages->publish ) ? (int) $pages->publish : 0 );

		return $total <= 3;
	}
endif;

if ( ! function_exists( 'zc_create_menu' ) ) :
	/**
	 * ایجاد یک منو (با پشتیبانی از زیرمنو) و اختصاص آن به جایگاه.
	 *
	 * @param string $name     نام منو.
	 * @param array  $items    آیتم‌ها: آرایه‌ای از type/url/object/id/title/children (یا عنوان => لینک).
	 * @param string $location جایگاه.
	 * @return int
	 */
	function zc_create_menu( $name, $items, $location ) {
		$menu_object = wp_get_nav_menu_object( $name );
		$menu_id     = $menu_object ? (int) $menu_object->term_id : wp_create_nav_menu( $name );

		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			return 0;
		}
		$menu_id = (int) $menu_id;

		// پاک‌سازی آیتم‌های قبلی برای جلوگیری از تکرار.
		$existing_items = wp_get_nav_menu_items( $menu_id, array( 'post_status' => 'publish,draft' ) );
		if ( $existing_items ) {
			foreach ( $existing_items as $item ) {
				wp_delete_post( $item->ID, true );
			}
		}

		$add = static function ( $item, $parent = 0 ) use ( $menu_id, &$add ) {
			if ( ! is_array( $item ) ) {
				return;
			}

			if ( isset( $item['type'] ) && 'post' === $item['type'] ) {
				$args = array(
					'menu-item-object-id' => (int) $item['id'],
					'menu-item-object'    => (string) $item['object'],
					'menu-item-type'      => 'post_type',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => (int) $parent,
				);
				if ( ! empty( $item['title'] ) ) {
					$args['menu-item-title'] = (string) $item['title'];
				}
			} elseif ( isset( $item['type'] ) && 'tax' === $item['type'] ) {
				$args = array(
					'menu-item-object-id' => (int) $item['id'],
					'menu-item-object'    => (string) $item['object'],
					'menu-item-type'      => 'taxonomy',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => (int) $parent,
				);
				if ( ! empty( $item['title'] ) ) {
					$args['menu-item-title'] = (string) $item['title'];
				}
			} else {
				$args = array(
					'menu-item-title'     => isset( $item['title'] ) ? (string) $item['title'] : '',
					'menu-item-url'       => isset( $item['url'] ) ? (string) $item['url'] : '#',
					'menu-item-type'      => 'custom',
					'menu-item-status'    => 'publish',
					'menu-item-parent-id' => (int) $parent,
				);
			}

			$item_id = wp_update_nav_menu_item( $menu_id, 0, $args );

			if ( ! is_wp_error( $item_id ) && ! empty( $item['children'] ) ) {
				foreach ( (array) $item['children'] as $child ) {
					$add( $child, (int) $item_id );
				}
			}
		};

		foreach ( $items as $key => $item ) {
			// سازگاری با قالب قدیمی (عنوان => لینک).
			if ( is_string( $item ) ) {
				$item = array( 'type' => 'custom', 'title' => (string) $key, 'url' => $item );
			}
			$add( $item );
		}

		$locations = (array) get_theme_mod( 'nav_menu_locations' );
		$locations[ $location ] = $menu_id;
		set_theme_mod( 'nav_menu_locations', $locations );

		return $menu_id;
	}
endif;

if ( ! function_exists( 'zc_remove_demo_content' ) ) :
	/**
	 * حذف کامل محتوای دمو (برگه‌ها، نوشته‌ها، رسانه‌ها، منوها، دسته‌ها، قالب‌های المنتور).
	 *
	 * @param bool $reset_options بازنشانی تنظیمات قالب به پیش‌فرض.
	 * @return int
	 */
	function zc_remove_demo_content( $reset_options = false ) {
		$stats   = zc_demo_reset_content();
		$removed = (int) array_sum( $stats );

		if ( $reset_options ) {
			delete_option( ZC_OPT );
		} else {
			// قطع اتصال قالب‌های سربرگ/پاورقی حذف‌شده.
			$options = get_option( ZC_OPT, null );
			if ( is_array( $options ) ) {
				$options['header_template'] = '';
				$options['footer_template'] = '';
				update_option( ZC_OPT, $options );
			}
		}
		zc_opt_flush();

		$zc_icon_now = (int) get_option( 'site_icon' );
		if ( $zc_icon_now && ! get_post( $zc_icon_now ) ) {
			delete_option( 'site_icon' );
		}
		update_option( 'show_on_front', 'posts' );
		update_option( 'page_on_front', 0 );
		update_option( 'page_for_posts', 0 );
		delete_option( 'zc_blog_page' );
		delete_transient( 'zc_dynamic_css_' . ZC_VERSION );

		return $removed;
	}
endif;


if ( defined( 'WP_CLI' ) && WP_CLI && class_exists( 'WP_CLI' ) ) {
	/**
	 * نصب و حذف دموی زرین‌کوچ از خط فرمان.
	 *
	 * ## EXAMPLES
	 *
	 *     wp zarincoach demo install --palette=navy
	 *     wp zarincoach demo remove --reset-options
	 *
	 * @param array $args       آرگومان‌ها.
	 * @param array $assoc_args گزینه‌ها.
	 */
	WP_CLI::add_command(
		'zarincoach demo',
		static function ( $args, $assoc_args ) {
			$action = isset( $args[0] ) ? $args[0] : 'install';

			if ( 'remove' === $action ) {
				zc_remove_demo_content( ! empty( $assoc_args['reset-options'] ) );
				WP_CLI::success( 'Demo content removed.' );
				return;
			}

			$params = array(
				'palette'   => isset( $assoc_args['palette'] ) ? sanitize_key( $assoc_args['palette'] ) : 'navy',
				'elementor' => ! isset( $assoc_args['no-elementor'] ),
			);

			foreach ( array_keys( zc_demo_steps() ) as $step ) {
				do {
					$result = zc_demo_run_step( $step, $params );
					WP_CLI::log( '[' . $step . '] ' . $result['message'] );
				} while ( ! empty( $result['more'] ) && ! empty( $result['done'] ) );
			}
			WP_CLI::success( 'Demo installed.' );
		}
	);
}

if ( ! function_exists( 'zc_demo_prepare_author' ) ) :
	/**
	 * نام نمایشی و زندگی‌نامه‌ی نویسنده‌ی نوشته‌های دمو (برای بخش نویسنده و سطر نویسنده).
	 *
	 * فقط وقتی تغییر می‌کند که کاربر هنوز نام نمایشی/زندگی‌نامه‌ی اختصاصی ندارد.
	 *
	 * @param int $user_id شناسه کاربر.
	 * @return void
	 */
	function zc_demo_prepare_author( $user_id ) {
		$user = get_userdata( (int) $user_id );
		if ( ! $user ) {
			return;
		}
		$data = array( 'ID' => $user->ID );
		if ( '' === trim( (string) $user->display_name ) || $user->display_name === $user->user_login ) {
			$data['display_name'] = 'مریم جمالی';
			$data['first_name']   = 'مریم';
			$data['last_name']    = 'جمالی';
			$data['nickname']     = 'مریم جمالی';
		}
		if ( '' === trim( (string) get_user_meta( $user->ID, 'description', true ) ) ) {
			$data['description'] = 'روان‌شناس الگوهای ذهنی و رفتاری در مسیر رشد؛ کارشناس ارشد روان‌شناسی بالینی با پروانه اشتغال ۲۸۸۵۸ از سازمان نظام روان‌شناسی و مشاوره. در این یادداشت‌ها درباره‌ی کمال‌گرایی، اهمالکاری، تصمیم‌گیری و ذهن مدیر می‌نویسم.';
		}
		if ( count( $data ) > 1 ) {
			wp_update_user( $data );
		}

		// تصویر نمایه: عکس واقعی مریم جمالی (اگر کاربر تصویر دیگری انتخاب نکرده باشد).
		$current = (int) get_user_meta( $user->ID, 'zc_avatar_id', true );
		if ( ! $current || ! wp_attachment_is_image( $current ) || get_post_meta( $current, '_zc_demo_media', true ) ) {
			$avatar = function_exists( 'zc_demo_import_media' ) ? zc_demo_import_media( 'maryam-avatar' ) : 0;
			if ( $avatar ) {
				update_user_meta( $user->ID, 'zc_avatar_id', $avatar );
			}
		}
	}
endif;
