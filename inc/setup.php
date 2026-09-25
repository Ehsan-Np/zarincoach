<?php
/**
 * راه‌اندازی امکانات استاندارد وردپرس
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_setup' ) ) :
	/**
	 * پشتیبانی‌های قالب، منوها، اندازه تصاویر و نواحی ابزارک.
	 *
	 * @return void
	 */
	function zc_setup() {
		load_theme_textdomain( ZC_TEXTDOMAIN, ZC_DIR . '/languages' );

		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		add_theme_support( 'post-thumbnails' );
		add_theme_support( 'responsive-embeds' );
		add_theme_support( 'align-wide' );
		add_theme_support( 'custom-spacing' );
		add_theme_support( 'custom-line-height' );
		add_theme_support( 'wp-block-styles' );
		add_theme_support( 'editor-styles' );
		add_theme_support( 'post-formats', array() );

		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
				'navigation-widgets',
			)
		);

		add_theme_support(
			'custom-logo',
			array(
				'height'      => 64,
				'width'       => 220,
				'flex-height' => true,
				'flex-width'  => true,
				'header-text' => array( 'site-title', 'site-description' ),
			)
		);

		add_theme_support(
			'custom-background',
			array(
				'default-color' => 'FAF9F6',
			)
		);

		// پالت رنگی سفارشی در ویرایشگر گوتنبرگ (هماهنگ با هویت بصری).
		add_theme_support(
			'editor-color-palette',
			array(
				array(
					'name'  => __( 'طلایی / اصلی', 'zarincoach' ),
					'slug'  => 'primary',
					'color' => '#D4AF37',
				),
				array(
					'name'  => __( 'زیتونی تیره', 'zarincoach' ),
					'slug'  => 'secondary',
					'color' => '#3B4436',
				),
				array(
					'name'  => __( 'تراکوتا', 'zarincoach' ),
					'slug'  => 'accent',
					'color' => '#C08A7C',
				),
				array(
					'name'  => __( 'آبی غبارآلود', 'zarincoach' ),
					'slug'  => 'info',
					'color' => '#95AEC2',
				),
				array(
					'name'  => __( 'استخوانی', 'zarincoach' ),
					'slug'  => 'base',
					'color' => '#FAF9F6',
				),
				array(
					'name'  => __( 'سرمه‌ای', 'zarincoach' ),
					'slug'  => 'ink',
					'color' => '#0F172A',
				),
			)
		);

		register_nav_menus(
			array(
				'zc-primary' => __( 'منوی اصلی', 'zarincoach' ),
				'zc-mobile'  => __( 'منوی موبایل', 'zarincoach' ),
				'zc-footer'  => __( 'منوی پاورقی', 'zarincoach' ),
				'zc-legal'   => __( 'منوی قوانین (پاورقی)', 'zarincoach' ),
			)
		);

		// اندازه‌های اختصاصی تصاویر (بهینه برای کارت‌ها و سربرگ‌ها).
		// خلاصه‌ی برگه‌ها در سربرگ صفحات داخلی نمایش داده می‌شود.
		add_post_type_support( 'page', 'excerpt' );

		add_image_size( 'zc_card', 900, 640, true );
		add_image_size( 'zc_wide', 1400, 720, true );
		add_image_size( 'zc_portrait', 800, 1000, true );
		add_image_size( 'zc_thumb', 480, 320, true );
		add_image_size( 'zc_avatar', 160, 160, true );
		// نیم‌اندازه‌های هم‌نسبت ← srcset واقعی برای موبایل (وردپرس فقط اندازه‌های هم‌نسبت را در srcset می‌گذارد).
		add_image_size( 'zc_card_sm', 450, 320, true );
		add_image_size( 'zc_wide_md', 700, 360, true );
		add_image_size( 'zc_portrait_sm', 400, 500, true );

		add_editor_style( 'assets/css/main.css' );
	}
endif;
add_action( 'after_setup_theme', 'zc_setup', 10 );

if ( ! function_exists( 'zc_widgets_init' ) ) :
	/**
	 * ثبت نواحی ابزارک.
	 *
	 * @return void
	 */
	function zc_widgets_init() {
		register_sidebar(
			array(
				'name'          => __( 'ستون کناری مقالات', 'zarincoach' ),
				'id'            => 'zc-blog',
				'description'   => __( 'ابزارک‌های این ناحیه در کنار مقالات نمایش داده می‌شود.', 'zarincoach' ),
				'before_widget' => '<div id="%1$s" class="zc-card mb-6 %2$s">',
				'after_widget'  => '</div>',
				'before_title'  => '<h3 class="mb-4 text-lg font-bold text-secondary">',
				'after_title'   => '</h3>',
			)
		);

		for ( $i = 1; $i <= 3; $i++ ) {
			register_sidebar(
				array(
					/* translators: %d: شماره ستون پاورقی */
					'name'          => sprintf( __( 'ستون پاورقی %d', 'zarincoach' ), $i ),
					'id'            => 'zc-footer-' . $i,
					'description'   => __( 'ستون‌های پاورقی سایت', 'zarincoach' ),
					'before_widget' => '<div id="%1$s" class="mb-8 %2$s">',
					'after_widget'  => '</div>',
					'before_title'  => '<h4 class="mb-4 text-[0.95rem] font-bold text-white/90">',
					'after_title'   => '</h4>',
				)
			);
		}
	}
endif;
add_action( 'widgets_init', 'zc_widgets_init' );

if ( ! function_exists( 'zc_fallback_menu' ) ) :
	/**
	 * منوی پیش‌فرض در صورتی که کاربر منویی نساخته باشد.
	 *
	 * @return void
	 */
	function zc_fallback_menu() {
		$pages = get_pages(
			array(
				'sort_column' => 'menu_order,post_title',
				'number'      => 6,
				'post_status' => 'publish',
			)
		);

		echo '<ul class="flex flex-wrap items-center gap-1">';
		echo '<li><a class="zc-nav-link" href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'zarincoach' ) . '</a></li>';

		if ( ! empty( $pages ) ) {
			foreach ( $pages as $page ) {
				if ( (int) get_option( 'page_on_front' ) === (int) $page->ID ) {
					continue;
				}
				printf(
					'<li><a class="zc-nav-link" href="%1$s">%2$s</a></li>',
					esc_url( get_permalink( $page->ID ) ),
					esc_html( $page->post_title )
				);
			}
		}

		echo '</ul>';
	}
endif;

if ( ! function_exists( 'zc_force_rtl' ) ) :
	/**
	 * قالب فارسی است؛ حتی اگر زبان سایت روی انگلیسی باشد، جهت صفحه راست‌به‌چپ می‌شود.
	 *
	 * @return void
	 */
	function zc_force_rtl() {
		global $wp_locale;

		if ( is_admin() || ! ( $wp_locale instanceof WP_Locale ) ) {
			return;
		}

		/**
		 * فیلتر اجبار به راست‌چین بودن سایت.
		 *
		 * @param bool $force اجبار (پیش‌فرض: بله).
		 */
		if ( apply_filters( 'zc_force_rtl', true ) ) {
			$wp_locale->text_direction = 'rtl';
		}
	}
endif;
add_action( 'wp', 'zc_force_rtl', 1 );

if ( ! function_exists( 'zc_language_attributes' ) ) :
	/**
	 * زبان صفحه برای موتورهای جستجو (fa-IR) در صورت انگلیسی بودن تنظیمات.
	 *
	 * @param string $output ویژگی‌های فعلی.
	 * @return string
	 */
	function zc_language_attributes( $output ) {
		if ( is_admin() || ! apply_filters( 'zc_force_rtl', true ) ) {
			return $output;
		}
		if ( false === strpos( $output, 'dir=' ) ) {
			$output .= ' dir="rtl"';
		}
		if ( 0 !== strpos( get_locale(), 'fa' ) ) {
			$output = preg_replace( '/lang="[^"]*"/', 'lang="fa-IR"', $output );
		}
		return $output;
	}
endif;
add_filter( 'language_attributes', 'zc_language_attributes', 20 );

if ( ! function_exists( 'zc_nav_link_attributes' ) ) :
	/**
	 * افزودن کلاس‌های طراحی به لینک‌های منو بر اساس جایگاه.
	 *
	 * @param array    $atts  ویژگی‌های لینک.
	 * @param WP_Post  $item  آیتم منو.
	 * @param stdClass $args  آرگومان‌های منو.
	 * @param int      $depth عمق.
	 * @return array
	 */
	function zc_nav_link_attributes( $atts, $item, $args, $depth = 0 ) {
		$location = isset( $args->theme_location ) ? (string) $args->theme_location : '';

		$classes = array(
			'zc-primary' => 0 === (int) $depth ? 'zc-nav-link' : '',
			'zc-mobile'  => 0 === (int) $depth
				? 'flex items-center justify-between rounded-xl px-3 py-3 text-[0.95rem] font-bold text-secondary transition-colors hover:bg-surface2 hover:text-primary'
				: 'block rounded-lg px-3 py-2 text-[0.88rem] text-muted hover:text-primary',
			'zc-footer'  => 'transition-colors hover:text-primary',
		);

		if ( isset( $classes[ $location ] ) && '' !== $classes[ $location ] ) {
			$atts['class'] = trim( ( isset( $atts['class'] ) ? $atts['class'] . ' ' : '' ) . $classes[ $location ] );
		}

		return $atts;
	}
endif;
add_filter( 'nav_menu_link_attributes', 'zc_nav_link_attributes', 10, 4 );

if ( ! function_exists( 'zc_nav_item_classes' ) ) :
	/**
	 * کلاس آیتم‌های دارای زیرمنو.
	 *
	 * @param array    $classes کلاس‌ها.
	 * @param WP_Post  $item    آیتم.
	 * @param stdClass $args    آرگومان‌ها.
	 * @return array
	 */
	function zc_nav_item_classes( $classes, $item, $args ) {
		if ( isset( $args->theme_location ) && 'zc-primary' === $args->theme_location && in_array( 'menu-item-has-children', (array) $classes, true ) ) {
			$classes[] = 'zc-has-submenu';
			$classes[] = 'relative';
		}
		return $classes;
	}
endif;
add_filter( 'nav_menu_css_class', 'zc_nav_item_classes', 10, 3 );

if ( ! function_exists( 'zc_nav_submenu_classes' ) ) :
	/**
	 * کلاس زیرمنوها.
	 *
	 * @param array    $classes کلاس‌ها.
	 * @param stdClass $args    آرگومان‌ها.
	 * @return array
	 */
	function zc_nav_submenu_classes( $classes, $args ) {
		$location = isset( $args->theme_location ) ? (string) $args->theme_location : '';
		if ( 'zc-primary' === $location ) {
			$classes[] = 'zc-submenu';
		} elseif ( 'zc-mobile' === $location ) {
			$classes[] = 'ms-4 border-s border-line ps-2';
		}
		return $classes;
	}
endif;
add_filter( 'nav_menu_submenu_css_class', 'zc_nav_submenu_classes', 10, 2 );
