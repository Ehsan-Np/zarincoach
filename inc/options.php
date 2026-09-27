<?php
/**
 * راه‌اندازی پنل تنظیمات قالب (Redux Framework) و منوی یکپارچه‌ی «زرین‌کوچ».
 *
 * - یک منوی واحد در پیشخوان: تنظیمات قالب، نصب دمو، سربرگ و پاورقی، اطلاعات سیستم.
 * - پوسته‌ی اختصاصی راست‌چین با فونت آراد (assets/admin/panel.css).
 * - قالب‌های اختصاصی هدر/نوار ذخیره/فوتر پنل در inc/admin-panel/templates.
 * - ترجمه‌ی فارسی رشته‌های Redux.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_admin_menu_icon' ) ) :
	/**
	 * آیکن SVG منوی زرین‌کوچ (با رنگ‌آمیزی خودکار وردپرس).
	 *
	 * @return string
	 */
	function zc_admin_menu_icon() {
		$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><path fill="#a7aaad" d="M9 1.5c.55 4.1 2.6 6.15 6.7 6.7-4.1.55-6.15 2.6-6.7 6.7-.55-4.1-2.6-6.15-6.7-6.7C6.4 7.65 8.45 5.6 9 1.5zm6.6 10.2c.28 1.95 1.2 2.87 3.15 3.15-1.95.28-2.87 1.2-3.15 3.15-.28-1.95-1.2-2.87-3.15-3.15 1.95-.28 2.87-1.2 3.15-3.15z"/></svg>';
		return 'data:image/svg+xml;base64,' . base64_encode( $svg ); // phpcs:ignore WordPress.PHP.DiscouragedPHPFunctions.obfuscation_base64_encode
	}
endif;

if ( ! function_exists( 'zc_redux_args' ) ) :
	/**
	 * تنظیمات کلی پنل.
	 *
	 * @return array<string, mixed>
	 */
	function zc_redux_args() {
		return array(
			'opt_name'                  => ZC_OPT,
			'display_name'              => __( 'زرین‌کوچ', 'zarincoach' ),
			'display_version'           => ZC_VERSION,
			'menu_type'                 => 'menu',
			'allow_sub_menu'            => false,
			'menu_title'                => __( 'زرین‌کوچ', 'zarincoach' ),
			'page_title'                => __( 'تنظیمات قالب زرین‌کوچ', 'zarincoach' ),
			'page_slug'                 => 'zc-options',
			'page_permissions'          => 'manage_options',
			'page_priority'             => 58,
			'menu_icon'                 => zc_admin_menu_icon(),
			'admin_bar'                 => true,
			'admin_bar_icon'            => 'dashicons-admin-appearance',
			'admin_bar_priority'        => 50,
			'global_variable'           => '',
			'dev_mode'                  => false,
			'forced_dev_mode_off'       => true,
			'update_notice'             => false,
			'customizer'                => false,
			'ajax_save'                 => true,
			'use_cdn'                   => false,
			'show_import_export'        => true,
			'show_options_object'       => false,
			'hide_reset'                => false,
			'hide_save'                 => false,
			'hide_expand'               => true,
			'open_expanded'             => false,
			'async_typography'          => false,
			'disable_google_fonts_link' => true,
			'google_api_key'            => '',
			'footer_credit'             => ' ',
			'default_show'              => false,
			'default_mark'              => '',
			'class'                     => 'zc-panel',
			'templates_path'            => ZC_DIR . '/inc/admin-panel/templates/',
			'hints'                     => array(
				'icon'          => 'fa-regular fa-circle-question',
				'icon_position' => 'left',
				'tip_style'     => array(
					'color'   => 'light',
					'shadow'  => true,
					'rounded' => true,
				),
			),
		);
	}
endif;

if ( ! function_exists( 'zc_register_options_panel' ) ) :
	/**
	 * ثبت آرگومان‌ها و بخش‌های پنل تنظیمات.
	 *
	 * @return void
	 */
	function zc_register_options_panel() {
		if ( ! zc_redux_available() ) {
			add_action( 'admin_notices', 'zc_redux_missing_notice' );
			return;
		}

		Redux::set_args( ZC_OPT, zc_redux_args() );
		Redux::set_sections( ZC_OPT, zc_redux_sections() );
	}
endif;
add_action( 'after_setup_theme', 'zc_register_options_panel', 20 );

if ( ! function_exists( 'zc_options_saved' ) ) :
	/**
	 * پاک‌سازی کش‌ها پس از ذخیره‌ی تنظیمات.
	 *
	 * @return void
	 */
	function zc_options_saved() {
		delete_transient( function_exists( 'zc_field_defaults_key' ) ? zc_field_defaults_key() : 'zc_field_defaults_' . ZC_VERSION );
		delete_transient( 'zc_dynamic_css_' . ZC_VERSION );
		if ( function_exists( 'zc_opt_flush' ) ) {
			zc_opt_flush();
		}
		/**
		 * پس از ذخیره/بازنشانی/درون‌ریزی تنظیمات قالب.
		 */
		do_action( 'zc_options_saved' );
	}
endif;
add_action( 'redux/options/' . ZC_OPT . '/saved', 'zc_options_saved', 10 );
add_action( 'redux/options/' . ZC_OPT . '/reset', 'zc_options_saved', 10 );
add_action( 'redux/options/' . ZC_OPT . '/section/reset', 'zc_options_saved', 10 );
add_action( 'redux/options/' . ZC_OPT . '/import', 'zc_options_saved', 10 );

/* ---------------------------------------------------------------------
 * منوی یکپارچه
 * ------------------------------------------------------------------- */

if ( ! function_exists( 'zc_admin_pages' ) ) :
	/**
	 * صفحه‌های مدیریتی قالب (شناسه‌ی صفحه => برچسب).
	 *
	 * @return array<string, string>
	 */
	function zc_admin_pages() {
		return array(
			'zc-options'      => __( 'تنظیمات قالب', 'zarincoach' ),
			'zc-demo-content' => __( 'نصب دمو', 'zarincoach' ),
			'zc-layout'       => __( 'سربرگ و پاورقی', 'zarincoach' ),
			'zc-widgets'      => __( 'مدیریت ویجت‌ها', 'zarincoach' ),
			'zc-system-info'  => __( 'اطلاعات سیستم', 'zarincoach' ),
		);
	}
endif;

if ( ! function_exists( 'zc_register_options_page' ) ) :
	/**
	 * زیرمنوهای «زرین‌کوچ» (زیر همان منوی Redux).
	 *
	 * @return void
	 */
	function zc_register_options_page() {
		global $submenu;

		$parent = 'zc-options';

		// اگر Redux در دسترس نباشد، منوی والد را خودمان می‌سازیم تا صفحات ابزار از دست نروند.
		if ( ! zc_redux_available() ) {
			add_menu_page( __( 'زرین‌کوچ', 'zarincoach' ), __( 'زرین‌کوچ', 'zarincoach' ), 'manage_options', $parent, 'zc_render_demo_page', zc_admin_menu_icon(), 58 );
		}

		add_submenu_page( $parent, __( 'نصب دمو', 'zarincoach' ), __( 'نصب دمو', 'zarincoach' ), 'manage_options', 'zc-demo-content', 'zc_render_demo_page' );

		add_submenu_page( $parent, __( 'سربرگ و پاورقی', 'zarincoach' ), __( 'سربرگ و پاورقی', 'zarincoach' ), 'edit_theme_options', 'zc-layout', 'zc_render_layout_page' );

		add_submenu_page( $parent, __( 'مدیریت ویجت‌ها', 'zarincoach' ), __( 'مدیریت ویجت‌ها', 'zarincoach' ), 'manage_options', 'zc-widgets', 'zc_render_widgets_page' );

		add_submenu_page( $parent, __( 'اطلاعات سیستم', 'zarincoach' ), __( 'اطلاعات سیستم', 'zarincoach' ), 'manage_options', 'zc-system-info', 'zc_render_system_page' );

		if ( empty( $submenu[ $parent ] ) ) {
			return;
		}

		// ترتیب ثابت: تنظیمات قالب، نصب دمو، سربرگ و پاورقی، پیام‌های فرم، اطلاعات سیستم.
		$weight = static function ( $slug ) {
			if ( 'zc-options' === $slug ) {
				return 0;
			}
			if ( 'zc-demo-content' === $slug ) {
				return 1;
			}
			if ( 'zc-layout' === $slug ) {
				return 2;
			}
			if ( 'zc-widgets' === $slug ) {
				return 3;
			}
			if ( 'zc-system-info' === $slug ) {
				return 9;
			}
			return 5;
		};
		$items = array_values( $submenu[ $parent ] );

		// اگر زیرمنوی پیام‌ها پیش از صفحه‌ی Redux ثبت شده باشد، وردپرس خودِ صفحه را به زیرمنو اضافه نمی‌کند.
		if ( ! in_array( $parent, wp_list_pluck( $items, 2 ), true ) ) {
			array_unshift( $items, array( __( 'تنظیمات قالب', 'zarincoach' ), 'manage_options', $parent, __( 'تنظیمات قالب', 'zarincoach' ) ) );
		}
		usort(
			$items,
			static function ( $a, $b ) use ( $weight ) {
				return $weight( $a[2] ) <=> $weight( $b[2] );
			}
		);
		foreach ( $items as $i => $item ) {
			if ( 'zc-options' === $item[2] ) {
				$items[ $i ][0] = __( 'تنظیمات قالب', 'zarincoach' );
			}
		}
		$submenu[ $parent ] = $items; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
	}
endif;
add_action( 'admin_menu', 'zc_register_options_page', 20 );

if ( ! function_exists( 'zc_legacy_admin_redirect' ) ) :
	/**
	 * انتقال نشانی قدیمی منوی «zc-theme» به پنل جدید.
	 *
	 * @return void
	 */
	function zc_legacy_admin_redirect() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'] ) && 'zc-theme' === $_GET['page'] ) {
			wp_safe_redirect( admin_url( 'admin.php?page=zc-options' ) );
			exit;
		}
	}
endif;
add_action( 'admin_init', 'zc_legacy_admin_redirect', 1 );

if ( ! function_exists( 'zc_is_panel_screen' ) ) :
	/**
	 * آیا صفحه‌ی فعلی یکی از صفحه‌های قالب است؟
	 *
	 * @return string شناسه‌ی صفحه یا رشته‌ی خالی.
	 */
	function zc_is_panel_screen() {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$page = isset( $_GET['page'] ) ? sanitize_key( wp_unslash( $_GET['page'] ) ) : '';
		return array_key_exists( $page, zc_admin_pages() ) ? $page : '';
	}
endif;

/* ---------------------------------------------------------------------
 * پوسته‌ی پنل
 * ------------------------------------------------------------------- */

if ( ! function_exists( 'zc_panel_assets' ) ) :
	/**
	 * استایل و اسکریپت پنل (فقط در صفحه‌های قالب).
	 *
	 * @return void
	 */
	function zc_panel_assets() {
		$page = zc_is_panel_screen();
		if ( '' === $page ) {
			return;
		}
		$css = ZC_DIR . '/assets/admin/panel.css';
		$js  = ZC_DIR . '/assets/admin/panel.js';
		wp_enqueue_style( 'zc-panel', ZC_URI . '/assets/admin/panel.css', array(), ZC_VERSION . '.' . ( file_exists( $css ) ? filemtime( $css ) : 0 ) );

		if ( 'zc-options' === $page ) {
			// Font Awesome را خودِ Redux در این صفحه بارگذاری می‌کند.
			wp_enqueue_script( 'zc-panel', ZC_URI . '/assets/admin/panel.js', array( 'jquery' ), ZC_VERSION . '.' . ( file_exists( $js ) ? filemtime( $js ) : 0 ), true );
			wp_localize_script(
				'zc-panel',
				'zcPanel',
				array(
					'noResults' => __( 'هیچ تنظیمی با این عبارت پیدا نشد.', 'zarincoach' ),
					'chars'       => __( 'نویسه', 'zarincoach' ),
					'searchLabel' => __( 'نتایج جستجو', 'zarincoach' ),
				)
			);
		} elseif ( file_exists( ZC_DIR . '/inc/redux/assets/font-awesome/css/all.min.css' ) ) {
			wp_enqueue_style( 'zc-panel-fa', ZC_URI . '/inc/redux/assets/font-awesome/css/all.min.css', array(), '6.5' );
		}
	}
endif;
add_action( 'admin_enqueue_scripts', 'zc_panel_assets', 30 );

// پوسته‌ی رنگی پیش‌فرض Redux (منوی تیره با !important) بارگذاری نشود؛ panel.css جایگزین کامل آن است.
add_filter( 'redux/enqueue/' . ZC_OPT . '/args/admin_theme/css_url', '__return_false' );

if ( ! function_exists( 'zc_panel_body_class' ) ) :
	/**
	 * کلاس بدنه‌ی صفحه‌های قالب.
	 *
	 * @param string $classes کلاس‌ها.
	 * @return string
	 */
	function zc_panel_body_class( $classes ) {
		$page = zc_is_panel_screen();
		if ( '' !== $page ) {
			$classes .= ' zc-admin zc-admin--' . $page;
		}
		return $classes;
	}
endif;
add_filter( 'admin_body_class', 'zc_panel_body_class' );

if ( ! function_exists( 'zc_redux_fill_check_defaults' ) ) {
	/**
	 * Redux کلیدِ جاافتاده‌ی گروه چک‌باکس را «خاموش» نمایش می‌دهد و با ذخیره‌ی بعدی خاموش ثبت می‌کند.
	 * گزینه‌های تازه‌ی نسخه‌های بعدی (ویجت، کانال ارتباطی و…) با پیش‌فرض خودشان نمایش داده شوند.
	 *
	 * @param array $options  مقادیر.
	 * @param array $sections بخش‌ها.
	 * @return array
	 */
	function zc_redux_fill_check_defaults( $options, $sections ) {
		if ( ! is_array( $options ) || empty( $options ) ) {
			return $options;
		}
		foreach ( (array) $sections as $section ) {
			if ( empty( $section['fields'] ) || ! is_array( $section['fields'] ) ) {
				continue;
			}
			foreach ( $section['fields'] as $field ) {
				if ( empty( $field['id'] ) || empty( $field['type'] ) || 'checkbox' !== $field['type'] || empty( $field['options'] ) ) {
					continue;
				}
				if ( ! isset( $field['default'] ) || ! is_array( $field['default'] ) ) {
					continue;
				}
				$id = $field['id'];
				if ( isset( $options[ $id ] ) && is_array( $options[ $id ] ) ) {
					$options[ $id ] = zc_merge_check_defaults( $options[ $id ], $field['default'] );
				}
			}
		}
		return $options;
	}
}
add_filter( 'redux/options/' . ZC_OPT . '/options', 'zc_redux_fill_check_defaults', 10, 2 );

if ( ! function_exists( 'zc_panel_hide_foreign_notices' ) ) :
	/**
	 * اعلان‌های دیگر افزونه‌ها در صفحه‌های قالب (پنل، دمو، سربرگ و پاورقی، اطلاعات سیستم) پنهان می‌شوند تا چیدمان به‌هم نریزد.
	 * (اعلان‌های خودِ قالب همچنان نمایش داده می‌شوند.)
	 *
	 * @return void
	 */
	function zc_panel_hide_foreign_notices() {
		if ( '' === zc_is_panel_screen() ) {
			return;
		}
		remove_all_actions( 'admin_notices' );
		remove_all_actions( 'all_admin_notices' );
		if ( function_exists( 'zc_admin_notices' ) ) {
			add_action( 'admin_notices', 'zc_admin_notices' );
		}
	}
endif;
add_action( 'in_admin_header', 'zc_panel_hide_foreign_notices', 1000 );

if ( ! function_exists( 'zc_panel_admin_footer_text' ) ) :
	/**
	 * متن پاورقی پیشخوان در صفحه‌های قالب.
	 *
	 * @param string $text متن.
	 * @return string
	 */
	function zc_panel_admin_footer_text( $text ) {
		if ( '' === zc_is_panel_screen() ) {
			return $text;
		}
		return sprintf(
			/* translators: %s: زرین‌کد */
			esc_html__( 'قالب زرین‌کوچ · طراحی و توسعه: %s', 'zarincoach' ),
			'<a href="https://zarincode.com" target="_blank" rel="noopener">' . esc_html__( 'احسان نادری‌پناه | زرین‌کد', 'zarincoach' ) . '</a>'
		);
	}
endif;
add_filter( 'admin_footer_text', 'zc_panel_admin_footer_text', 99 );

if ( ! function_exists( 'zc_panel_header_bar' ) ) :
	/**
	 * نوار برند مشترک بالای صفحه‌های قالب (پنل، دمو، اطلاعات سیستم).
	 *
	 * @param string $current شناسه‌ی صفحه‌ی فعال.
	 * @return string
	 */
	function zc_panel_header_bar( $current ) {
		$tabs = array(
			'zc-options'      => array( __( 'تنظیمات قالب', 'zarincoach' ), 'fa-solid fa-sliders', admin_url( 'admin.php?page=zc-options' ) ),
			'zc-demo-content' => array( __( 'نصب دمو', 'zarincoach' ), 'fa-solid fa-wand-magic-sparkles', admin_url( 'admin.php?page=zc-demo-content' ) ),
			'zc-layout'       => array( __( 'سربرگ و پاورقی', 'zarincoach' ), 'fa-solid fa-pen-ruler', admin_url( 'admin.php?page=zc-layout' ) ),
			'zc-widgets'      => array( __( 'ویجت‌ها', 'zarincoach' ), 'fa-solid fa-cubes', admin_url( 'admin.php?page=zc-widgets' ) ),
			'zc-system-info'  => array( __( 'اطلاعات سیستم', 'zarincoach' ), 'fa-solid fa-server', admin_url( 'admin.php?page=zc-system-info' ) ),
		);

		$html  = '<div class="zc-hero">';
		$html .= '<div class="zc-hero-brand"><span class="zc-hero-logo" aria-hidden="true"><svg viewBox="0 0 20 20" width="26" height="26"><path fill="currentColor" d="M9 1.5c.55 4.1 2.6 6.15 6.7 6.7-4.1.55-6.15 2.6-6.7 6.7-.55-4.1-2.6-6.15-6.7-6.7C6.4 7.65 8.45 5.6 9 1.5zm6.6 10.2c.28 1.95 1.2 2.87 3.15 3.15-1.95.28-2.87 1.2-3.15 3.15-.28-1.95-1.2-2.87-3.15-3.15 1.95-.28 2.87-1.2 3.15-3.15z"/></svg></span>';
		$html .= '<div><h1 class="zc-hero-title">' . esc_html__( 'زرین‌کوچ', 'zarincoach' ) . ' <span class="zc-hero-ver">' . esc_html( sprintf( /* translators: %s: نسخه */ __( 'نسخه %s', 'zarincoach' ), ZC_VERSION ) ) . '</span></h1>';
		$html .= '<p class="zc-hero-sub">' . esc_html( sprintf( /* translators: %s: نام سایت */ __( 'قالب اختصاصی %s', 'zarincoach' ), get_bloginfo( 'name' ) ) ) . '</p></div></div>';
		$html .= '<a class="zc-hero-site" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i>' . esc_html__( 'مشاهده‌ی سایت', 'zarincoach' ) . '</a>';
		$html .= '<nav class="zc-hero-tabs" aria-label="' . esc_attr__( 'بخش‌های زرین‌کوچ', 'zarincoach' ) . '">';
		foreach ( $tabs as $key => $tab ) {
			$html .= sprintf(
				'<a class="zc-hero-tab%1$s" href="%2$s"%3$s><i class="%4$s" aria-hidden="true"></i>%5$s</a>',
				$key === $current ? ' is-active' : '',
				esc_url( $tab[2] ),
				$key === $current ? ' aria-current="page"' : '',
				esc_attr( $tab[1] ),
				esc_html( $tab[0] )
			);
		}
		$html .= '</nav></div>';
		return $html;
	}
endif;

/* ---------------------------------------------------------------------
 * ترجمه‌ی فارسی رشته‌های Redux
 * ------------------------------------------------------------------- */

if ( ! function_exists( 'zc_redux_strings' ) ) :
	/**
	 * جدول ترجمه.
	 *
	 * @return array<string, string>
	 */
	function zc_redux_strings() {
		return array(
			'Save Changes'                     => 'ذخیره‌ی تغییرات',
			'Reset Section'                    => 'بازنشانی این بخش',
			'Reset All'                        => 'بازنشانی همه',
			'Expand'                           => 'نمایش همه',
			'Upload'                           => 'انتخاب تصویر',
			'Remove'                           => 'حذف',
			'No media selected'                => 'تصویری انتخاب نشده است',
			'On'                               => 'فعال',
			'Off'                              => 'غیرفعال',
			'Transparent'                      => 'شفاف',
			'Add More'                         => 'افزودن',
			'Search for field(s)'              => 'جستجوی تنظیمات…',
			'Import / Export'                  => 'پشتیبان‌گیری و انتقال',
			'Import Options'                   => 'درون‌ریزی تنظیمات',
			'Import from Clipboard'            => 'درون‌ریزی از متن',
			'Import from File'                 => 'درون‌ریزی از فایل',
			'Upload file'                      => 'بارگذاری فایل',
			'Paste your clipboard data here.'  => 'متن پشتیبان را اینجا بچسبانید.',
			'Import'                           => 'درون‌ریزی',
			'Export Options'                   => 'برون‌بری تنظیمات',
			'Copy to Clipboard'                => 'کپی در حافظه',
			'Copy Data'                        => 'کپی داده',
			'Copied!'                          => 'کپی شد!',
			'Export File'                      => 'دانلود فایل پشتیبان',
			'Download Data File'               => 'دانلود فایل پشتیبان',
			'Please Wait'                      => 'لطفاً صبر کنید',
			'Settings Saved!'                  => 'تنظیمات ذخیره شد.',
			'Settings Imported!'               => 'تنظیمات درون‌ریزی شد.',
			'All Defaults Restored!'           => 'همه‌ی تنظیمات به حالت پیش‌فرض برگشت.',
			'Section Defaults Restored!'       => 'تنظیمات این بخش به حالت پیش‌فرض برگشت.',
			'Settings have changed, you should save them!' => 'تغییرات ذخیره نشده‌اند؛ «ذخیره‌ی تغییرات» را بزنید.',
			'error(s) were found!'             => 'خطا پیدا شد!',
			'warning(s) were found!'           => 'هشدار پیدا شد!',
			'WARNING! This will overwrite all existing option values, please proceed with caution!' => 'هشدار: همه‌ی تنظیمات فعلی جایگزین می‌شوند؛ با احتیاط ادامه دهید.',
			'Here you can copy/download your current option settings. Keep this safe as you can use it as a backup should anything go wrong, or you can use it to restore your settings on this site (or any other site).' => 'از تنظیمات فعلی نسخه‌ی پشتیبان بگیرید تا در صورت بروز مشکل یا انتقال سایت، آن را بازگردانید.',
			'Your panel has unchanged values, would you like to save them now?' => 'پنل مقادیر ذخیره‌نشده دارد؛ اکنون ذخیره شوند؟',
			'You have changes that are not saved. Would you like to save them now?' => 'تغییراتی دارید که ذخیره نشده‌اند؛ اکنون ذخیره شوند؟',
			'Are you sure? Resetting will lose all custom values.' => 'مطمئن هستید؟ همه‌ی تنظیمات سفارشی حذف و به پیش‌فرض برمی‌گردند.',
			'Are you sure? Resetting will lose all custom values in this section.' => 'مطمئن هستید؟ تنظیمات این بخش به پیش‌فرض برمی‌گردند.',
			'Your current options will be replaced with the values of this preset. Would you like to proceed?' => 'تنظیمات فعلی با مقادیر این پیش‌تنظیم جایگزین می‌شوند. ادامه می‌دهید؟',
			'Your current options will be replaced with the values of this import. Would you like to proceed?' => 'تنظیمات فعلی با فایل درون‌ریزی جایگزین می‌شوند. ادامه می‌دهید؟',
			'There was an error saving. Here is the result of your action:' => 'در ذخیره خطایی رخ داد. نتیجه:',
			'There was a problem with your action. Please try again or reload the page.' => 'مشکلی پیش آمد؛ دوباره تلاش کنید یا صفحه را تازه کنید.',
			'Warning- This options panel will not work properly without javascript!' => 'برای کار با پنل تنظیمات، جاوااسکریپت مرورگر باید فعال باشد.',
			'Developer Mode Enabled'           => 'حالت توسعه‌دهنده فعال است',
			'You must provide a valid URL for this option.' => 'نشانی اینترنتی معتبر وارد کنید.',
			'You must provide a valid email for this option.' => 'نشانی ایمیل معتبر وارد کنید.',
			'This field must be a valid color value.' => 'کد رنگ معتبر وارد کنید.',
			'You must provide a numerical value for this option.' => 'یک عدد وارد کنید.',
			'Options'                          => 'تنظیمات',
		);
	}
endif;

if ( ! function_exists( 'zc_redux_gettext' ) ) :
	/**
	 * ترجمه‌ی رشته‌های دامنه‌ی redux-framework.
	 *
	 * @param string $translation ترجمه.
	 * @param string $text        متن اصلی.
	 * @param string $domain      دامنه.
	 * @return string
	 */
	function zc_redux_gettext( $translation, $text, $domain ) {
		if ( 'redux-framework' !== $domain ) {
			return $translation;
		}
		static $map = null;
		if ( null === $map ) {
			$map = zc_redux_strings();
		}
		return isset( $map[ $text ] ) ? $map[ $text ] : $translation;
	}
endif;
if ( is_admin() || wp_doing_ajax() ) {
	add_filter( 'gettext', 'zc_redux_gettext', 20, 3 );
}
