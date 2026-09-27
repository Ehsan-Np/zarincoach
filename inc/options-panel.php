<?php
/**
 * ساختار کامل پنل تنظیمات زرین‌کوچ (نسخه ۱.۹ — بازنویسی کامل).
 *
 * همه‌ی شناسه‌ها و مقادیر پیش‌فرضِ نسخه‌های قبل حفظ شده‌اند تا تنظیمات ذخیره‌شده‌ی
 * کاربر بدون مهاجرت کار کند. فیلدهایی که با المنتور بی‌اثرند با `zc_classic` علامت
 * خورده‌اند و در حالت المنتور از پنل حذف می‌شوند (پیش‌فرضشان در دسترس می‌ماند).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

require_once ZC_DIR . '/inc/options-fields.php';
require_once ZC_DIR . '/inc/options-sections-home.php';
require_once ZC_DIR . '/inc/options-sections-shop.php';

if ( ! function_exists( 'zc_redux_is_elementor_mode' ) ) :
	/**
	 * آیا المنتور فعال است (و پنل باید گزینه‌های زائد را پنهان کند)؟
	 *
	 * @return bool
	 */
	function zc_redux_is_elementor_mode() {
		$on = did_action( 'elementor/loaded' ) || defined( 'ELEMENTOR_VERSION' );
		/**
		 * امکان اجبار نمایش همه گزینه‌ها.
		 *
		 * @param bool $on حالت المنتور.
		 */
		return (bool) apply_filters( 'zc_redux_elementor_mode', $on );
	}
endif;

if ( ! function_exists( 'zc_classic' ) ) :
	/**
	 * علامت‌گذاری فیلد به‌عنوان «فقط بدون المنتور».
	 *
	 * @param array $field فیلد.
	 * @return array
	 */
	function zc_classic( $field ) {
		$field['zc_classic'] = true;
		return $field;
	}
endif;

if ( ! function_exists( 'zc_redux_sections' ) ) :
	/**
	 * تمام بخش‌های پنل تنظیمات.
	 *
	 * @param bool $for_panel true = خروجی نمایشی پنل (با حذف گزینه‌های زائد در حالت المنتور).
	 *                        false = همه‌ی فیلدها (برای محاسبه‌ی مقادیر پیش‌فرض).
	 * @return array<int, array<string, mixed>>
	 */
	function zc_redux_sections( $for_panel = true ) {
		$sections = array_merge(
			zc_panel_dashboard( $for_panel ),
			zc_panel_brand(),
			zc_panel_palette(),
			zc_panel_typography(),
			zc_panel_layout(),
			zc_panel_header(),
			zc_panel_footer(),
			zc_panel_blog(),
			zc_panel_schemas(),
			zc_panel_pages(),
			zc_panel_home_classic(),
			zc_panel_shop( $for_panel ),
			zc_panel_contact(),
			zc_panel_social(),
			zc_panel_legal(),
			zc_panel_engagement(),
			zc_panel_seo(),
			zc_panel_performance(),
			zc_panel_security(),
			zc_panel_elementor(),
			zc_panel_code()
		);

		/**
		 * فیلتر نهایی بخش‌های پنل تنظیمات.
		 *
		 * @param array $sections فهرست بخش‌ها.
		 */
		$sections = (array) apply_filters( 'zc_redux_sections', $sections );

		if ( $for_panel ) {
			$sections = zc_redux_is_elementor_mode() ? zc_redux_prune_for_elementor( $sections ) : zc_redux_prune_elementor_notes( $sections );
		}

		return $sections;
	}
endif;

if ( ! function_exists( 'zc_redux_prune_for_elementor' ) ) :
	/**
	 * حذف فیلدها و بخش‌هایی که با فعال بودن المنتور بی‌اثرند.
	 *
	 * @param array $sections بخش‌ها.
	 * @return array
	 */
	function zc_redux_prune_for_elementor( $sections ) {
		$out = array();
		foreach ( (array) $sections as $section ) {
			if ( ! empty( $section['zc_classic'] ) ) {
				continue;
			}
			if ( ! empty( $section['fields'] ) ) {
				$section['fields'] = array_values(
					array_filter(
						$section['fields'],
						static function ( $f ) {
							return empty( $f['zc_classic'] );
						}
					)
				);
			}
			$out[] = $section;
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_redux_prune_elementor_notes' ) ) :
	/**
	 * بدون المنتور: حذف راهنماهایی که فقط در حالت المنتور معنا دارند.
	 *
	 * @param array $sections بخش‌ها.
	 * @return array
	 */
	function zc_redux_prune_elementor_notes( $sections ) {
		foreach ( $sections as $i => $section ) {
			if ( ! empty( $section['fields'] ) ) {
				$sections[ $i ]['fields'] = array_values(
					array_filter(
						$section['fields'],
						static function ( $f ) {
							return empty( $f['zc_elementor_only'] );
						}
					)
				);
			}
		}
		return $sections;
	}
endif;

if ( ! function_exists( 'zc_panel_section' ) ) :
	/**
	 * سازنده‌ی بخش.
	 *
	 * @param string $id     شناسه.
	 * @param string $title  عنوان.
	 * @param string $icon   کلاس آیکن (Font Awesome 6).
	 * @param string $desc   توضیح زیر عنوان.
	 * @param array  $fields فیلدها.
	 * @param array  $extra  ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_panel_section( $id, $title, $icon, $desc, $fields, $extra = array() ) {
		return array_merge(
			array(
				'id'     => $id,
				'title'  => $title,
				'icon'   => $icon,
				'desc'   => $desc,
				'class'  => ' zc-sec-' . $id,
				'fields' => $fields,
			),
			$extra
		);
	}
endif;

if ( ! function_exists( 'zc_panel_template_edit_url' ) ) :
	/**
	 * نشانی ویرایش قالب المنتوری سربرگ/پاورقی.
	 *
	 * @param string $loc header|footer.
	 * @return string
	 */
	function zc_panel_template_edit_url( $loc ) {
		$opts = get_option( ZC_OPT, array() );
		$id   = ( is_array( $opts ) && ! empty( $opts[ $loc . '_template' ] ) ) ? (int) $opts[ $loc . '_template' ] : 0;
		if ( $id && 'elementor_library' === get_post_type( $id ) ) {
			return admin_url( 'post.php?post=' . $id . '&action=elementor' );
		}
		return admin_url( 'edit.php?post_type=elementor_library' );
	}
endif;

/* =====================================================================
 * ۰. پیشخوان
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_dashboard' ) ) :
	/**
	 * پیشخوان: وضعیت، میان‌برها و راهنما.
	 *
	 * @param bool $for_panel فقط در پنل HTML ساخته شود.
	 * @return array
	 */
	function zc_panel_dashboard( $for_panel ) {
		$html = ( $for_panel && is_admin() ) ? zc_panel_dashboard_html() : '';
		return array(
			zc_panel_section(
				'dashboard',
				__( 'پیشخوان', 'zarincoach' ),
				'fa-solid fa-house',
				__( 'نمای کلی وضعیت سایت، میان‌برهای پرکاربرد و راهنمای شروع.', 'zarincoach' ),
				array( zc_f_raw( 'dash_overview', $html ) ),
				array( 'zc_group' => __( 'شروع', 'zarincoach' ) )
			),
		);
	}
endif;

if ( ! function_exists( 'zc_panel_dashboard_html' ) ) :
	/**
	 * محتوای پیشخوان.
	 *
	 * @return string
	 */
	function zc_panel_dashboard_html() {
		$opts      = get_option( ZC_OPT, array() );
		$opts      = is_array( $opts ) ? $opts : array();
		$el_on     = defined( 'ELEMENTOR_VERSION' );
		$demo      = '1' === (string) get_option( 'zc_demo_installed', '' );
		$palettes  = function_exists( 'zc_palettes' ) ? zc_palettes() : array();
		$mode      = isset( $opts['palette_mode'] ) ? $opts['palette_mode'] : 'preset';
		$preset    = isset( $opts['palette_preset'] ) ? $opts['palette_preset'] : 'navy';
		$pal_label = 'custom' === $mode ? __( 'پالت سفارشی', 'zarincoach' ) : ( isset( $palettes[ $preset ] ) ? $palettes[ $preset ]['label'] : '' );
		$pal_label = preg_replace( '/\s*\(.*\)$/u', '', (string) $pal_label );
		$header_ok = ! empty( $opts['header_template'] ) && 'elementor_library' === get_post_type( (int) $opts['header_template'] );
		$footer_ok = ! empty( $opts['footer_template'] ) && 'elementor_library' === get_post_type( (int) $opts['footer_template'] );
		// وضعیت دقیق سربرگ/پاورقی (المنتور پرو ← قالب انتخابی ← داخلی).
		$layout_value = ( $header_ok && $footer_ok ) ? __( 'المنتوری · متصل', 'zarincoach' ) : __( 'قالب داخلی', 'zarincoach' );
		$layout_state = ( $header_ok && $footer_ok ) ? 'ok' : 'info';
		if ( function_exists( 'zc_layout_source' ) ) {
			$zc_short = array(
				'pro'      => __( 'المنتور پرو', 'zarincoach' ),
				'theme'    => __( 'المنتوری', 'zarincoach' ),
				'missing'  => __( 'نیاز به بررسی', 'zarincoach' ),
				'internal' => __( 'داخلی', 'zarincoach' ),
			);
			$zc_h         = zc_layout_source( 'header' )['type'];
			$zc_f         = zc_layout_source( 'footer' )['type'];
			$layout_value = $zc_h === $zc_f ? $zc_short[ $zc_h ] : sprintf( /* translators: 1: سربرگ 2: پاورقی */ __( 'سربرگ %1$s · پاورقی %2$s', 'zarincoach' ), $zc_short[ $zc_h ], $zc_short[ $zc_f ] );
			$layout_state = ( 'missing' === $zc_h || 'missing' === $zc_f ) ? 'warn' : ( ( 'internal' === $zc_h || 'internal' === $zc_f ) ? 'info' : 'ok' );
		}
		$maint     = ! empty( $opts['maint_enable'] ) && '1' === (string) $opts['maint_enable'];
		$front_id  = (int) get_option( 'page_on_front' );

		$cards = array(
			array(
				'label' => __( 'نسخه‌ی قالب', 'zarincoach' ),
				'value' => ZC_VERSION,
				'state' => 'ok',
				'icon'  => 'fa-solid fa-code-branch',
			),
			array(
				'label' => __( 'صفحه‌ساز المنتور', 'zarincoach' ),
				'value' => $el_on ? sprintf( /* translators: %s: نسخه */ __( 'فعال · %s', 'zarincoach' ), ELEMENTOR_VERSION ) : __( 'نصب/فعال نیست', 'zarincoach' ),
				'state' => $el_on ? 'ok' : 'warn',
				'icon'  => 'fa-solid fa-layer-group',
			),
			array(
				'label' => __( 'محتوای نمونه (دمو)', 'zarincoach' ),
				'value' => $demo ? __( 'نصب شده', 'zarincoach' ) : __( 'نصب نشده', 'zarincoach' ),
				'state' => $demo ? 'ok' : 'warn',
				'icon'  => 'fa-solid fa-box-open',
			),
			array(
				'label' => __( 'سربرگ و پاورقی', 'zarincoach' ),
				'value' => $layout_value,
				'state' => $layout_state,
				'icon'  => 'fa-solid fa-window-maximize',
			),
			array(
				'label' => __( 'پالت رنگی', 'zarincoach' ),
				'value' => $pal_label,
				'state' => 'info',
				'icon'  => 'fa-solid fa-palette',
			),
			array(
				'label' => __( 'حالت تعمیر و نگهداری', 'zarincoach' ),
				'value' => $maint ? __( 'روشن — سایت برای بازدیدکنندگان بسته است', 'zarincoach' ) : __( 'خاموش', 'zarincoach' ),
				'state' => $maint ? 'danger' : 'ok',
				'icon'  => 'fa-solid fa-person-digging',
			),
		);

		$html = '<div class="zc-dash">';
		$html .= '<div class="zc-dash-cards">';
		foreach ( $cards as $c ) {
			$html .= sprintf(
				'<div class="zc-dash-card is-%1$s"><i class="%2$s" aria-hidden="true"></i><span class="zc-dash-card-label">%3$s</span><strong class="zc-dash-card-value">%4$s</strong></div>',
				esc_attr( $c['state'] ),
				esc_attr( $c['icon'] ),
				esc_html( $c['label'] ),
				esc_html( $c['value'] )
			);
		}
		$html .= '</div>';

		$actions = array(
			array( home_url( '/' ), __( 'مشاهده‌ی سایت', 'zarincoach' ), 'fa-solid fa-arrow-up-right-from-square', true ),
			array( admin_url( 'admin.php?page=zc-demo-content' ), __( 'نصب / بازنصب دمو', 'zarincoach' ), 'fa-solid fa-wand-magic-sparkles', false ),
			array( admin_url( 'admin.php?page=zc-layout' ), __( 'سربرگ و پاورقی', 'zarincoach' ), 'fa-solid fa-window-maximize', false ),
			array( zc_panel_template_edit_url( 'header' ), __( 'ویرایش سربرگ', 'zarincoach' ), 'fa-solid fa-pen-ruler', false ),
			array( zc_panel_template_edit_url( 'footer' ), __( 'ویرایش پاورقی', 'zarincoach' ), 'fa-solid fa-pen-ruler', false ),
		);
		if ( $front_id && $el_on ) {
			$actions[] = array( admin_url( 'post.php?post=' . $front_id . '&action=elementor' ), __( 'ویرایش صفحه‌ی اصلی', 'zarincoach' ), 'fa-solid fa-house-chimney', false );
		}
		$actions[] = array( admin_url( 'nav-menus.php' ), __( 'فهرست‌ها (منوها)', 'zarincoach' ), 'fa-solid fa-bars', false );
		$actions[] = array( admin_url( 'edit.php?post_type=zc_schema' ), __( 'کتابخانه‌ی طرحواره‌ها', 'zarincoach' ), 'fa-solid fa-brain', false );
		$actions[] = array( admin_url( 'admin.php?page=zc-system-info' ), __( 'اطلاعات سیستم', 'zarincoach' ), 'fa-solid fa-server', false );

		$html .= '<h3 class="zc-dash-title">' . esc_html__( 'میان‌برها', 'zarincoach' ) . '</h3><div class="zc-dash-actions">';
		foreach ( $actions as $a ) {
			$html .= sprintf(
				'<a class="zc-dash-action" href="%1$s"%2$s><i class="%3$s" aria-hidden="true"></i><span>%4$s</span></a>',
				esc_url( $a[0] ),
				$a[3] ? ' target="_blank" rel="noopener"' : '',
				esc_attr( $a[2] ),
				esc_html( $a[1] )
			);
		}
		$html .= '</div>';

		$jump = array(
			'brand'       => __( 'لوگو و هویت برند', 'zarincoach' ),
			'palette'     => __( 'رنگ‌ها', 'zarincoach' ),
			'contact'     => __( 'شماره‌ها و نشانی', 'zarincoach' ),
			'legal'       => __( 'مجوزها و نمادها', 'zarincoach' ),
			'engagement'  => __( 'دکمه‌ی شناور و نوار موبایل', 'zarincoach' ),
			'seo'         => __( 'سئو و اسکیما', 'zarincoach' ),
			'performance' => __( 'سرعت', 'zarincoach' ),
			'security'    => __( 'امنیت', 'zarincoach' ),
		);
		$html .= '<h3 class="zc-dash-title">' . esc_html__( 'رفتن به تنظیمات', 'zarincoach' ) . '</h3><div class="zc-dash-jump">';
		foreach ( $jump as $sec => $label ) {
			$html .= sprintf( '<button type="button" class="zc-chip" data-zc-goto="%1$s">%2$s</button>', esc_attr( $sec ), esc_html( $label ) );
		}
		$html .= '</div>';

		$steps = array(
			__( 'از «نصب دمو»، محتوای کامل سایت (صفحات المنتوری، سربرگ، پاورقی، مقالات و کتابخانه‌ی طرحواره‌ها) را نصب کنید.', 'zarincoach' ),
			__( 'در «هویت و برند» لوگو را بارگذاری و در «رنگ‌ها» پالت را انتخاب کنید.', 'zarincoach' ),
			__( '«اطلاعات تماس»، «شبکه‌های اجتماعی» و «حقوقی و نمادها» را کامل کنید؛ این داده‌ها همه‌جا (پاورقی، صفحات قوانین، اسکیما) استفاده می‌شوند.', 'zarincoach' ),
			__( 'متن و چیدمان هر صفحه را مستقیماً در المنتور با ویجت‌های «زرین‌کوچ» ویرایش کنید.', 'zarincoach' ),
			__( 'پس از هر تغییر، «ذخیره‌ی تغییرات» را بزنید. کش سایت به‌طور خودکار پاک می‌شود.', 'zarincoach' ),
		);
		$html .= '<h3 class="zc-dash-title">' . esc_html__( 'راهنمای شروع', 'zarincoach' ) . '</h3><ol class="zc-dash-steps">';
		foreach ( $steps as $s ) {
			$html .= '<li>' . esc_html( $s ) . '</li>';
		}
		$html .= '</ol>';

		$html .= '<p class="zc-dash-foot">' . sprintf(
			/* translators: %s: پیوند زرین‌کد */
			esc_html__( 'طراحی و توسعه: %s · برای تغییر مستقیم فایل‌ها فقط از قالب فرزند استفاده کنید.', 'zarincoach' ),
			'<a href="https://zarincode.com" target="_blank" rel="noopener">' . esc_html__( 'زرین‌کد', 'zarincoach' ) . '</a>'
		) . '</p>';
		$html .= '</div>';

		return $html;
	}
endif;

/* =====================================================================
 * ۱. هویت و برند
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_brand' ) ) :
	/**
	 * هویت و برند.
	 *
	 * @return array
	 */
	function zc_panel_brand() {
		return array(
			zc_panel_section(
				'brand',
				__( 'هویت و برند', 'zarincoach' ),
				'fa-solid fa-gem',
				__( 'لوگو، رنگ نوار مرورگر موبایل و برندینگ صفحه‌ی ورود.', 'zarincoach' ),
				array(
					zc_f_group( 'brand_logo_group', __( 'لوگو', 'zarincoach' ), __( 'در سربرگ، پاورقی، صفحه‌ی ورود و اسکیمای سازمان استفاده می‌شود.', 'zarincoach' ) ),
					zc_f_media( 'general_logo', __( 'لوگو', 'zarincoach' ), __( 'ترجیحاً SVG یا PNG شفاف. خالی = نام سایت به‌صورت متنی.', 'zarincoach' ), array( 'default' => array( 'url' => '' ) ) ),
					zc_f_media( 'general_logo_dark', __( 'لوگوی حالت تاریک', 'zarincoach' ), __( 'اختیاری؛ روی زمینه‌های تیره نمایش داده می‌شود.', 'zarincoach' ), array( 'default' => array( 'url' => '' ) ) ),
					zc_f_slider( 'general_logo_width', __( 'عرض لوگو', 'zarincoach' ), 168, 60, 360, 2, __( 'پیکسل؛ در موبایل خودکار کوچک‌تر می‌شود.', 'zarincoach' ) ),
					zc_f_note(
						'brand_site_icon',
						__( 'نماد سایت (Favicon)', 'zarincoach' ),
						sprintf(
							/* translators: %s: پیوند سفارشی‌سازی */
							__( 'نماد سایت از %s تنظیم می‌شود (مربعی، حداقل ۵۱۲×۵۱۲ پیکسل).', 'zarincoach' ),
							'<a href="' . esc_url( admin_url( 'customize.php?autofocus[section]=title_tagline' ) ) . '">' . esc_html__( 'سفارشی‌سازی ← هویت سایت', 'zarincoach' ) . '</a>'
						)
					),

					zc_f_group( 'brand_browser_group', __( 'نوار مرورگر موبایل', 'zarincoach' ), __( 'رنگ نوار آدرس کروم اندروید و سافاری (theme-color).', 'zarincoach' ) ),
					zc_f_buttons(
						'brand_theme_color_mode',
						__( 'رنگ نوار مرورگر', 'zarincoach' ),
						array(
							'auto'   => __( 'خودکار (زمینه‌ی پالت)', 'zarincoach' ),
							'brand'  => __( 'رنگ تیره‌ی برند', 'zarincoach' ),
							'custom' => __( 'دلخواه', 'zarincoach' ),
						),
						'auto',
						__( 'در حالت تاریکِ سیستم کاربر، رنگ تیره‌ی پالت به‌طور خودکار اعمال می‌شود.', 'zarincoach' )
					),
					zc_req( zc_f_color( 'brand_theme_color', __( 'رنگ دلخواه', 'zarincoach' ), '#0B1B3A' ), 'brand_theme_color_mode', '=', 'custom' ),

					zc_f_group( 'brand_login_group', __( 'صفحه‌ی ورود وردپرس', 'zarincoach' ) ),
					zc_f_switch( 'admin_login_brand', __( 'برندینگ صفحه‌ی ورود', 'zarincoach' ), true, __( 'لوگوی سایت، رنگ‌های پالت و فونت آراد در wp-login؛ پیوند لوگو به صفحه‌ی اصلی سایت.', 'zarincoach' ) ),
				),
				array( 'zc_group' => __( 'ظاهر', 'zarincoach' ) )
			),
		);
	}
endif;

/* =====================================================================
 * ۲. رنگ‌ها
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_palette' ) ) :
	/**
	 * پالت رنگی.
	 *
	 * @return array
	 */
	function zc_panel_palette() {
		$palettes = function_exists( 'zc_palettes' ) ? zc_palettes() : array();
		$options  = array();
		$preview  = '<div class="zc-pal-cards">';
		foreach ( $palettes as $key => $palette ) {
			$options[ $key ] = $palette['label'];
			$swatches        = '';
			foreach ( array( 'secondary', 'primary', 'accent', 'info', 'base' ) as $c ) {
				$swatches .= '<i style="background:' . esc_attr( $palette['light'][ $c ] ) . '"></i>';
			}
			$parts    = preg_split( '/\s*\(/u', (string) $palette['label'], 2 );
			$preview .= sprintf(
				'<button type="button" class="zc-pal-card" data-preset="%1$s"><span class="zc-pal-sw">%2$s</span><strong>%3$s</strong><small>%4$s</small></button>',
				esc_attr( $key ),
				$swatches,
				esc_html( $parts[0] ),
				esc_html( $palette['note'] )
			);
		}
		$preview .= '</div>';

		$req_custom = static function ( $field ) {
			return zc_req( $field, 'palette_mode', '=', 'custom' );
		};

		return array(
			zc_panel_section(
				'palette',
				__( 'رنگ‌ها', 'zarincoach' ),
				'fa-solid fa-palette',
				__( 'پالت رنگی سازمانی سایت. حالت تاریک به‌طور خودکار از همین پالت ساخته می‌شود.', 'zarincoach' ),
				array(
					zc_f_buttons(
						'palette_mode',
						__( 'حالت انتخاب رنگ', 'zarincoach' ),
						array(
							'preset' => __( 'پالت آماده', 'zarincoach' ),
							'custom' => __( 'سفارشی', 'zarincoach' ),
						),
						'preset'
					),
					zc_req( zc_f_raw( 'palette_preview', $preview ), 'palette_mode', '=', 'preset' ),
					zc_req( zc_f_select( 'palette_preset', __( 'پالت انتخاب‌شده', 'zarincoach' ), $options, 'navy', __( 'روی هر کارت بالا کلیک کنید یا از فهرست انتخاب کنید.', 'zarincoach' ) ), 'palette_mode', '=', 'preset' ),
					$req_custom( zc_f_color( 'palette_custom_primary', __( 'رنگ اصلی (Primary)', 'zarincoach' ), '#1D3A72', __( 'دکمه‌ها، پیوندها و تأکیدها.', 'zarincoach' ) ) ),
					$req_custom( zc_f_color( 'palette_custom_secondary', __( 'رنگ تیره (Secondary)', 'zarincoach' ), '#0B1B3A', __( 'تیترها و پس‌زمینه‌های تیره (سرمه‌ای عمیق).', 'zarincoach' ) ) ),
					$req_custom( zc_f_color( 'palette_custom_accent', __( 'رنگ تأکیدی (Accent)', 'zarincoach' ), '#C9A45C', __( 'جزئیات طلایی، نشان‌ها و خط‌های تزئینی.', 'zarincoach' ) ) ),
					$req_custom( zc_f_color( 'palette_custom_info', __( 'رنگ آرامش (Info)', 'zarincoach' ), '#8DA9D4' ) ),
					$req_custom( zc_f_color( 'palette_custom_base', __( 'پس‌زمینه‌ی سایت', 'zarincoach' ), '#F5F7FB' ) ),
					$req_custom( zc_f_color( 'palette_custom_surface', __( 'پس‌زمینه‌ی کارت‌ها', 'zarincoach' ), '#FFFFFF' ) ),
					$req_custom( zc_f_color( 'palette_custom_ink', __( 'رنگ متن اصلی', 'zarincoach' ), '#0E1A33' ) ),
					$req_custom( zc_f_color( 'palette_custom_muted', __( 'رنگ متن کم‌رنگ', 'zarincoach' ), '#56627A' ) ),

					zc_f_group( 'palette_tones_group', __( 'تُن بخش‌ها', 'zarincoach' ) ),
					zc_f_buttons(
						'palette_page_header_tone',
						__( 'سربرگ برگه‌ها و نوشته‌ها', 'zarincoach' ),
						array(
							'inverse' => __( 'تیره (سرمه‌ای)', 'zarincoach' ),
							'light'   => __( 'روشن', 'zarincoach' ),
						),
						'inverse',
						__( 'سربرگ وبلاگ، بایگانی‌ها، نوشته‌ها، کتابخانه‌ی طرحواره‌ها و ۴۰۴.', 'zarincoach' )
					),
					zc_classic(
						zc_f_buttons(
							'palette_cta_tone',
							__( 'کارت فراخوان اقدام', 'zarincoach' ),
							array(
								'inverse' => __( 'تیره (سرمه‌ای)', 'zarincoach' ),
								'light'   => __( 'روشن', 'zarincoach' ),
							),
							'inverse'
						)
					),
					zc_classic(
						zc_f(
							'checkbox',
							'palette_inverse_sections',
							__( 'بخش‌های تیره‌ی صفحه‌ی اصلی', 'zarincoach' ),
							array(
								'subtitle' => __( 'این بخش‌ها با پس‌زمینه‌ی سرمه‌ای و متن روشن نمایش داده می‌شوند.', 'zarincoach' ),
								'options'  => array(
									'hero'         => __( 'سربرگ اصلی (Hero)', 'zarincoach' ),
									'marquee'      => __( 'نوار کلمات', 'zarincoach' ),
									'about'        => __( 'درباره من', 'zarincoach' ),
									'services'     => __( 'خدمات', 'zarincoach' ),
									'schema'       => __( 'طرحواره‌ها', 'zarincoach' ),
									'process'      => __( 'مسیر همراهی', 'zarincoach' ),
									'stats'        => __( 'آمار', 'zarincoach' ),
									'testimonials' => __( 'نظرات مراجعان', 'zarincoach' ),
									'faq'          => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
									'blog'         => __( 'نوشته‌ها', 'zarincoach' ),
									'cta'          => __( 'فراخوان اقدام', 'zarincoach' ),
								),
								'default'  => array(
									'hero'         => '1',
									'marquee'      => '1',
									'schema'       => '1',
									'testimonials' => '1',
								),
							)
						)
					),
					zc_f_switch( 'palette_grain', __( 'بافت کاغذی (Grain)', 'zarincoach' ), true, __( 'لایه‌ی بسیار سبکِ بافت‌دار برای حس گرم و انسانی.', 'zarincoach' ) ),
					array_merge( zc_f_note( 'palette_el_note', __( 'رنگ هر بخش در المنتور', 'zarincoach' ), __( 'تیره یا روشن بودن هر بخش از تب «ظاهر ← تُن بخش» همان ویجت در المنتور تعیین می‌شود.', 'zarincoach' ) ), array( 'zc_elementor_only' => true ) ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۳. تایپوگرافی
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_typography' ) ) :
	/**
	 * تایپوگرافی.
	 *
	 * @return array
	 */
	function zc_panel_typography() {
		return array(
			zc_panel_section(
				'typography',
				__( 'تایپوگرافی', 'zarincoach' ),
				'fa-solid fa-font',
				__( 'فونت متغیر آراد (۱۰۰ تا ۹۰۰) به‌صورت محلی و بدون هیچ درخواست خارجی بارگذاری می‌شود؛ حجم هر نسخه کمتر از ۵۰ کیلوبایت.', 'zarincoach' ),
				array(
					zc_f_group( 'typo_font_group', __( 'فونت و ارقام', 'zarincoach' ) ),
					zc_f_switch( 'typo_persian_digits', __( 'ارقام فارسی', 'zarincoach' ), true, __( 'نسخه‌ی ارقام فارسیِ آراد (۰۱۲۳۴۵۶۷۸۹) بارگذاری می‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'typo_jalali', __( 'تاریخ شمسی', 'zarincoach' ), true, __( 'بدون افزونه؛ اگر افزونه‌ی تاریخ شمسی نصب باشد، خروجی آن استفاده می‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'typo_preload_font', __( 'بارگذاری زودهنگام فونت', 'zarincoach' ), true, __( 'Preload؛ برای بهبود LCP فعال بماند.', 'zarincoach' ) ),

					zc_f_group( 'typo_size_group', __( 'اندازه و فاصله', 'zarincoach' ) ),
					zc_f_slider( 'typo_base_size', __( 'اندازه‌ی متن بدنه', 'zarincoach' ), 16, 13, 20, 1, __( 'پیکسل', 'zarincoach' ) ),
					zc_f_slider( 'typo_line_height', __( 'فاصله‌ی خطوط', 'zarincoach' ), 1.9, 1.5, 2.4, 0.05, __( 'متن فارسی به فاصله‌ی خطوط بیشتری نیاز دارد (پیشنهاد: ۱٫۸ تا ۲).', 'zarincoach' ) ),
					zc_f_select(
						'typo_heading_weight',
						__( 'وزن تیترها', 'zarincoach' ),
						array(
							'600' => __( 'نیمه‌ضخیم (۶۰۰)', 'zarincoach' ),
							'700' => __( 'ضخیم (۷۰۰)', 'zarincoach' ),
							'800' => __( 'خیلی ضخیم (۸۰۰) — پیش‌فرض', 'zarincoach' ),
							'900' => __( 'سیاه (۹۰۰)', 'zarincoach' ),
						),
						'800'
					),
					zc_f_buttons(
						'typo_scale',
						__( 'مقیاس تیترها', 'zarincoach' ),
						array(
							'compact' => __( 'فشرده', 'zarincoach' ),
							'normal'  => __( 'متعادل', 'zarincoach' ),
							'large'   => __( 'بزرگ', 'zarincoach' ),
						),
						'normal'
					),
					zc_f_switch( 'typo_justify', __( 'تراز دوطرفه‌ی متن مقالات', 'zarincoach' ), true, __( 'متن مقاله‌ها، برگه‌ها و طرحواره‌ها با شکستن هوشمند واژه‌ها دوطرفه چیده می‌شود.', 'zarincoach' ) ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۴. چیدمان و جلوه‌ها
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_layout' ) ) :
	/**
	 * چیدمان، دکمه‌ها و جلوه‌ها.
	 *
	 * @return array
	 */
	function zc_panel_layout() {
		return array(
			zc_panel_section(
				'layout',
				__( 'چیدمان و جلوه‌ها', 'zarincoach' ),
				'fa-solid fa-table-cells-large',
				__( 'عرض محتوا، فاصله‌ها، دکمه‌ها، انیمیشن‌ها و حالت تاریک در کل سایت.', 'zarincoach' ),
				array(
					zc_f_group( 'layout_grid_group', __( 'چیدمان', 'zarincoach' ) ),
					zc_f_select(
						'general_container',
						__( 'حداکثر عرض محتوا', 'zarincoach' ),
						array(
							'1140px' => __( 'باریک (۱۱۴۰ پیکسل)', 'zarincoach' ),
							'1200px' => __( 'استاندارد (۱۲۰۰ پیکسل)', 'zarincoach' ),
							'1320px' => __( 'عریض (۱۳۲۰ پیکسل)', 'zarincoach' ),
							'1440px' => __( 'خیلی عریض (۱۴۴۰ پیکسل)', 'zarincoach' ),
						),
						'1200px'
					),
					zc_f_buttons(
						'general_spacing',
						__( 'فاصله‌ی عمودی بخش‌ها', 'zarincoach' ),
						array(
							'compact'  => __( 'فشرده (پیشنهادی)', 'zarincoach' ),
							'balanced' => __( 'متعادل', 'zarincoach' ),
							'spacious' => __( 'باز', 'zarincoach' ),
						),
						'compact',
						__( 'فاصله‌ی بالا و پایین بلوک‌ها در کل سایت؛ از موبایل تا دسکتاپ سیال.', 'zarincoach' )
					),
					zc_f_slider( 'general_radius', __( 'انحنای لبه‌ی کارت‌ها', 'zarincoach' ), 20, 0, 32, 1, __( 'پیکسل', 'zarincoach' ) ),

					zc_f_group( 'layout_btn_group', __( 'دکمه‌ها', 'zarincoach' ) ),
					zc_f_buttons(
						'general_btn_shape',
						__( 'شکل دکمه‌ها', 'zarincoach' ),
						array(
							'pill'    => __( 'کپسولی', 'zarincoach' ),
							'rounded' => __( 'گوشه‌گرد', 'zarincoach' ),
							'square'  => __( 'گوشه‌تیز', 'zarincoach' ),
						),
						'pill'
					),
					zc_f_switch( 'general_btn_shine', __( 'درخشش روی دکمه‌ها', 'zarincoach' ), true ),
					zc_f_switch( 'general_btn_shadow', __( 'سایه‌ی طلایی دکمه‌ی اصلی', 'zarincoach' ), true ),

					zc_f_group( 'layout_fx_group', __( 'جلوه‌ها و امکانات کمکی', 'zarincoach' ) ),
					zc_f_switch( 'general_animations', __( 'انیمیشن نمایش هنگام اسکرول', 'zarincoach' ), true, __( 'با احترام به تنظیم «کاهش حرکت» سیستم‌عامل کاربر.', 'zarincoach' ) ),
					zc_f_switch( 'general_smooth_scroll', __( 'اسکرول نرم پیوندهای داخلی', 'zarincoach' ), true ),
					zc_f_switch( 'general_scroll_progress', __( 'نوار پیشرفت اسکرول', 'zarincoach' ), true, __( 'نوار باریک بالای صفحه در همه‌ی برگه‌ها.', 'zarincoach' ) ),
					zc_f_switch( 'general_back_to_top', __( 'دکمه‌ی بازگشت به بالا', 'zarincoach' ), true ),
					zc_f_switch( 'general_preloader', __( 'صفحه‌ی بارگذاری (Preloader)', 'zarincoach' ), false, __( 'پیشنهاد نمی‌شود؛ نمایش محتوا را کمی دیرتر می‌کند.', 'zarincoach' ) ),

					zc_f_group( 'layout_dark_group', __( 'حالت تاریک', 'zarincoach' ) ),
					zc_f_select(
						'general_dark_mode',
						__( 'حالت تاریک', 'zarincoach' ),
						array(
							'off'     => __( 'غیرفعال', 'zarincoach' ),
							'toggle'  => __( 'با کلید تغییر در سربرگ', 'zarincoach' ),
							'auto'    => __( 'خودکار بر اساس سیستم کاربر', 'zarincoach' ),
							'enforce' => __( 'همیشه تاریک', 'zarincoach' ),
						),
						'toggle'
					),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۵. سربرگ
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_template_select' ) ) :
	/**
	 * فیلد انتخاب قالب المنتور.
	 *
	 * @param string $id    شناسه.
	 * @param string $title عنوان.
	 * @param string $desc  توضیح.
	 * @return array
	 */
	function zc_panel_template_select( $id, $title, $desc ) {
		return zc_f(
			'select',
			$id,
			$title,
			array(
				'subtitle'    => $desc,
				'data'        => 'posts',
				'placeholder' => __( '— قالب داخلی قالب —', 'zarincoach' ),
				'args'        => array(
					'post_type'      => 'elementor_library',
					'posts_per_page' => 100,
					'post_status'    => 'publish',
					'tax_query'      => array( // phpcs:ignore WordPress.DB.SlowDBQuery
						array(
							'taxonomy' => 'elementor_library_type',
							'field'    => 'slug',
							'terms'    => array( 'section', 'container', 'page' ),
						),
					),
				),
				'default'     => '',
			)
		);
	}
endif;

if ( ! function_exists( 'zc_panel_header' ) ) :
	/**
	 * سربرگ.
	 *
	 * @return array
	 */
	function zc_panel_header() {
		$el_note = zc_f_raw(
			'header_elementor_note',
			'<div class="zc-callout"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><div><strong>' . esc_html__( 'سربرگ ۱۰۰٪ المنتوری', 'zarincoach' ) . '</strong><p>' . esc_html__( 'چیدمان، منو، نوار اطلاعیه، دکمه‌ی اقدام، تلفن، جستجو و شبکه‌های اجتماعی سربرگ در ویجت «سربرگ سایت» تنظیم می‌شوند.', 'zarincoach' ) . '</p>' . zc_admin_link( zc_panel_template_edit_url( 'header' ), __( 'ویرایش سربرگ در المنتور', 'zarincoach' ), 'primary' ) . '</div></div>'
		);
		$el_note['zc_elementor_only'] = true;

		return array(
			zc_panel_section(
				'header',
				__( 'سربرگ', 'zarincoach' ),
				'fa-solid fa-window-maximize',
				__( 'قالب سربرگ سایت و رفتار آن هنگام اسکرول.', 'zarincoach' ),
				array(
					$el_note,
					zc_panel_template_select( 'header_template', __( 'قالب المنتور سربرگ', 'zarincoach' ), __( 'قالبی از «المنتور ← قالب‌ها» که با ویجت «سربرگ سایت» ساخته شده. خالی = سربرگ داخلی.', 'zarincoach' ) ),
					zc_f_switch( 'general_sticky_header', __( 'سربرگ چسبان', 'zarincoach' ), true, __( 'سربرگ هنگام اسکرول بالای صفحه ثابت می‌ماند (بدون پرش محتوا).', 'zarincoach' ) ),
					zc_f_slider( 'general_header_height', __( 'ارتفاع سربرگ (پیکسل)', 'zarincoach' ), 76, 56, 120, 2, __( 'مبنای فاصله‌ی چسبیدن فهرست مطالب، نوار اشتراک‌گذاری و پرش دقیق به سرفصل‌ها.', 'zarincoach' ) ),
					zc_classic(
						zc_f_buttons(
							'header_layout',
							__( 'چیدمان سربرگ', 'zarincoach' ),
							array(
								'classic'  => __( 'کلاسیک', 'zarincoach' ),
								'centered' => __( 'لوگوی وسط', 'zarincoach' ),
								'minimal'  => __( 'مینیمال', 'zarincoach' ),
							),
							'classic'
						)
					),
					zc_classic( zc_f_switch( 'header_topbar_enable', __( 'نوار بالایی', 'zarincoach' ), true ) ),
					zc_classic( zc_req( zc_f_text( 'header_topbar_text', __( 'متن نوار بالایی', 'zarincoach' ), 'خدمات مریم جمالی فقط از طریق وب‌سایت رسمی Maryam-Jamali.ir ارائه می‌شود' ), 'header_topbar_enable' ) ),
					zc_classic( zc_req( zc_f_ltr( 'header_topbar_link', __( 'پیوند نوار بالایی', 'zarincoach' ), '/terms/' ), 'header_topbar_enable' ) ),
					zc_classic( zc_f_switch( 'header_phone_enable', __( 'نمایش شماره تماس', 'zarincoach' ), true ) ),
					zc_classic( zc_f_switch( 'header_search_enable', __( 'نمایش جستجو', 'zarincoach' ), true ) ),
					zc_classic( zc_f_switch( 'header_dark_toggle', __( 'کلید حالت تاریک', 'zarincoach' ), true ) ),
					zc_classic( zc_f_switch( 'header_social_enable', __( 'نمایش شبکه‌های اجتماعی', 'zarincoach' ), false ) ),
					zc_classic( zc_f_text( 'header_cta_text', __( 'متن دکمه‌ی اقدام', 'zarincoach' ), 'رزرو جلسه' ) ),
					zc_classic( zc_f_ltr( 'header_cta_url', __( 'پیوند دکمه‌ی اقدام', 'zarincoach' ), '/booking/' ) ),
				),
				array( 'zc_group' => __( 'ساختار سایت', 'zarincoach' ) )
			),
		);
	}
endif;

/* =====================================================================
 * ۶. پاورقی
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_footer' ) ) :
	/**
	 * پاورقی.
	 *
	 * @return array
	 */
	function zc_panel_footer() {
		$el_note = zc_f_raw(
			'footer_elementor_note',
			'<div class="zc-callout"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><div><strong>' . esc_html__( 'پاورقی ۱۰۰٪ المنتوری', 'zarincoach' ) . '</strong><p>' . esc_html__( 'ستون‌ها، متن معرفی، کپی‌رایت، نمادهای اعتماد و منوی قوانین در ویجت «پاورقی سایت» تنظیم می‌شوند. اطلاعات تماس و حقوقی از بخش‌های مربوط در همین پنل خوانده می‌شود.', 'zarincoach' ) . '</p>' . zc_admin_link( zc_panel_template_edit_url( 'footer' ), __( 'ویرایش پاورقی در المنتور', 'zarincoach' ), 'primary' ) . '</div></div>'
		);
		$el_note['zc_elementor_only'] = true;

		return array(
			zc_panel_section(
				'footer',
				__( 'پاورقی', 'zarincoach' ),
				'fa-solid fa-table-columns',
				__( 'قالب پاورقی سایت.', 'zarincoach' ),
				array(
					$el_note,
					zc_panel_template_select( 'footer_template', __( 'قالب المنتور پاورقی', 'zarincoach' ), __( 'قالبی که با ویجت «پاورقی سایت» ساخته شده. خالی = پاورقی داخلی.', 'zarincoach' ) ),
					zc_classic( zc_f_textarea( 'footer_about', __( 'متن معرفی کوتاه', 'zarincoach' ), 'سلام! من مریم جمالی‌ام؛ روان‌شناس الگوهای ذهنی و رفتاری. کارم این است که سردرگمی را به وضوح، تصمیم و اقدام تبدیل کنم؛ با آموزش، ارزیابی و همراهی رشدمحور، برای جوانان و مدیرانی که می‌خواهند بهتر زندگی کنند.' ) ),
					zc_classic(
						zc_f_buttons(
							'footer_columns',
							__( 'ستون‌های ابزارک پاورقی', 'zarincoach' ),
							array( __( 'بدون ستون', 'zarincoach' ), __( 'یک ستون', 'zarincoach' ), __( 'دو ستون', 'zarincoach' ), __( 'سه ستون', 'zarincoach' ) ),
							'3'
						)
					),
					zc_classic( zc_f_text( 'footer_copy', __( 'متن کپی‌رایت', 'zarincoach' ), 'کلیه حقوق مادی و معنوی محتوای این وب‌سایت متعلق به مریم جمالی است.' ) ),
					zc_classic( zc_f_switch( 'footer_credit', __( 'نمایش اعتبار طراحی (زرین‌کد)', 'zarincoach' ), true ) ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۷. وبلاگ (+ زیربخش‌ها)
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_blog' ) ) :
	/**
	 * وبلاگ، مقاله، فهرست مطالب، اشتراک‌گذاری و دیدگاه‌ها.
	 *
	 * @return array
	 */
	function zc_panel_blog() {
		$cols = array(
			'2' => __( '۲ ستون', 'zarincoach' ),
			'3' => __( '۳ ستون', 'zarincoach' ),
			'4' => __( '۴ ستون', 'zarincoach' ),
		);

		return array(
			zc_panel_section(
				'blog',
				__( 'وبلاگ', 'zarincoach' ),
				'fa-solid fa-newspaper',
				__( 'بایگانی نوشته‌ها، دسته‌ها و نتایج جستجو.', 'zarincoach' ),
				array(
					zc_f_group( 'blog_hero_group', __( 'سربرگ وبلاگ', 'zarincoach' ) ),
					zc_f_text( 'blog_hero_title', __( 'عنوان', 'zarincoach' ), 'مجله الگوها' ),
					zc_f_textarea( 'blog_hero_text', __( 'توضیح', 'zarincoach' ), 'اینجا هر هفته درباره‌ی کمال‌گرایی، اهمالکاری، تصمیم‌گیری، مهارت‌های زندگی و روان‌شناسی مدیران می‌نویسم؛ همیشه با یک مثال واقعی، یک تمرین ساده و یک قدم بعدی که می‌توانید همین امروز بردارید.' ),
					zc_f_group( 'blog_archive_group', __( 'فهرست نوشته‌ها', 'zarincoach' ) ),
					zc_f_select(
						'archive_layout',
						__( 'چیدمان', 'zarincoach' ),
						array(
							'grid'    => __( 'شبکه‌ای', 'zarincoach' ),
							'list'    => __( 'فهرستی', 'zarincoach' ),
							'sidebar' => __( 'شبکه‌ای با ستون کناری', 'zarincoach' ),
						),
						'grid'
					),
					zc_req( zc_f_buttons( 'archive_columns', __( 'تعداد ستون', 'zarincoach' ), $cols, '3' ), 'archive_layout', '!=', 'list' ),
					zc_f_slider( 'archive_excerpt', __( 'طول خلاصه', 'zarincoach' ), 22, 8, 60, 1, __( 'تعداد واژه', 'zarincoach' ) ),
					zc_f_switch( 'archive_meta_date', __( 'نمایش تاریخ', 'zarincoach' ), true ),
					zc_f_switch( 'archive_reading_time', __( 'نمایش زمان مطالعه', 'zarincoach' ), true ),
				),
				array( 'zc_subgroup' => 'blog' )
			),

			zc_panel_section(
				'blog_single',
				__( 'صفحه‌ی مقاله', 'zarincoach' ),
				'fa-regular fa-file-lines',
				__( 'سربرگ مجله‌ای، باکس «بیشتر بخوانید»، معرفی نویسنده، نوشته‌ی قبلی/بعدی و مطالب مرتبط.', 'zarincoach' ),
				array(
					zc_f_select(
						'post_layout',
						__( 'چیدمان مقاله', 'zarincoach' ),
						array(
							'full'    => __( 'تمام‌عرض', 'zarincoach' ),
							'narrow'  => __( 'ستون میانی خوانا', 'zarincoach' ),
							'sidebar' => __( 'با ستون کناری', 'zarincoach' ),
						),
						'narrow'
					),
					zc_f_switch( 'post_progress_bar', __( 'نوار پیشرفت مطالعه', 'zarincoach' ), true ),
					zc_f_group( 'post_meta_group', __( 'سربرگ مقاله', 'zarincoach' ) ),
					zc_f_switch( 'post_show_excerpt', __( 'چکیده زیر عنوان', 'zarincoach' ), true, __( 'فقط اگر برای نوشته «چکیده» نوشته شده باشد.', 'zarincoach' ) ),
					zc_f_switch( 'post_meta_author', __( 'تصویر و نام نویسنده', 'zarincoach' ), true ),
					zc_f_switch( 'post_meta_updated', __( 'تاریخ به‌روزرسانی', 'zarincoach' ), true, __( 'اگر نوشته بیش از یک روز پس از انتشار ویرایش شده باشد.', 'zarincoach' ) ),
					zc_f_switch( 'post_meta_comments', __( 'تعداد دیدگاه‌ها', 'zarincoach' ), true ),
					zc_f_group( 'post_more_group', __( 'باکس «بیشتر بخوانید» درون متن', 'zarincoach' ) ),
					zc_f_switch( 'post_more_read_enable', __( 'نمایش باکس', 'zarincoach' ), true, __( 'پیشنهاد مطالب هم‌دسته در میانه‌ی مقاله.', 'zarincoach' ) ),
					zc_req( zc_f_text( 'post_more_read_title', __( 'عنوان باکس', 'zarincoach' ), 'بیشتر بخوانید' ), 'post_more_read_enable' ),
					zc_req( zc_f_slider( 'post_more_read_after', __( 'درج پس از پاراگراف', 'zarincoach' ), 3, 1, 15 ), 'post_more_read_enable' ),
					zc_req( zc_f_slider( 'post_more_read_count', __( 'تعداد پیشنهادها', 'zarincoach' ), 4, 1, 6 ), 'post_more_read_enable' ),
					zc_f_group( 'post_end_group', __( 'پایان مقاله', 'zarincoach' ) ),
					zc_f_switch( 'post_author_box', __( 'معرفی نویسنده', 'zarincoach' ), true ),
					zc_f_switch( 'post_nav_enable', __( 'کارت‌های نوشته‌ی قبلی و بعدی', 'zarincoach' ), true ),
					zc_f_switch( 'post_related_enable', __( 'مقالات مرتبط', 'zarincoach' ), true ),
					zc_req( zc_f_text( 'post_related_title', __( 'عنوان مقالات مرتبط', 'zarincoach' ), 'ادامه مطالعه' ), 'post_related_enable' ),
					zc_req( zc_f_slider( 'post_related_count', __( 'تعداد مقالات مرتبط', 'zarincoach' ), 3, 2, 6 ), 'post_related_enable' ),
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'blog_toc',
				__( 'فهرست مطالب', 'zarincoach' ),
				'fa-solid fa-list-ol',
				__( 'فهرست خودکار تیترهای مقاله؛ در موبایل خودکار یک‌ستونه می‌شود و کلیک روی هر مورد دقیقاً روی تیتر می‌نشیند.', 'zarincoach' ),
				array_merge(
					array( zc_f_switch( 'post_toc_enable', __( 'فهرست خودکار مطالب', 'zarincoach' ), true ) ),
					zc_req_on(
						array(
							zc_f_text( 'post_toc_title', __( 'عنوان فهرست', 'zarincoach' ), 'فهرست مطالب' ),
							zc_f_buttons(
								'post_toc_levels',
								__( 'سطح تیترها', 'zarincoach' ),
								array(
									'2'   => 'H2',
									'2-3' => 'H2 + H3',
									'2-4' => __( 'H2 تا H4', 'zarincoach' ),
								),
								'2-3'
							),
							zc_f_slider( 'post_toc_min', __( 'حداقل تیتر برای نمایش', 'zarincoach' ), 3, 1, 10 ),
							zc_f_buttons(
								'post_toc_columns',
								__( 'تعداد ستون', 'zarincoach' ),
								array(
									'1' => __( '۱ ستون', 'zarincoach' ),
									'2' => __( '۲ ستون', 'zarincoach' ),
									'3' => __( '۳ ستون', 'zarincoach' ),
									'4' => __( '۴ ستون', 'zarincoach' ),
								),
								'2'
							),
							zc_f_select(
								'post_toc_style',
								__( 'سبک', 'zarincoach' ),
								array(
									'card'    => __( 'کارت با نوار رنگی', 'zarincoach' ),
									'soft'    => __( 'زمینه‌ی ملایم', 'zarincoach' ),
									'navy'    => __( 'سرمه‌ای', 'zarincoach' ),
									'minimal' => __( 'مینیمال (خطی)', 'zarincoach' ),
								),
								'card'
							),
							zc_f_select(
								'post_toc_numbering',
								__( 'شماره‌گذاری', 'zarincoach' ),
								array(
									'decimal'      => __( 'دو رقمی (۰۱، ۰۲…)', 'zarincoach' ),
									'hierarchical' => __( 'سلسله‌مراتبی (۱، ۱.۱…)', 'zarincoach' ),
									'none'         => __( 'بدون شماره', 'zarincoach' ),
								),
								'decimal'
							),
							zc_f_select(
								'post_toc_position',
								__( 'جایگاه', 'zarincoach' ),
								array(
									'before_first'  => __( 'پیش از نخستین تیتر (پس از مقدمه)', 'zarincoach' ),
									'after_first_p' => __( 'پس از نخستین پاراگراف', 'zarincoach' ),
									'top'           => __( 'ابتدای متن', 'zarincoach' ),
								),
								'before_first'
							),
							zc_f_switch( 'post_toc_collapsible', __( 'قابل جمع‌شدن', 'zarincoach' ), true ),
							zc_req( zc_f_switch( 'post_toc_open', __( 'در ابتدا باز باشد', 'zarincoach' ), true ), 'post_toc_collapsible' ),
							zc_f_switch( 'post_toc_floating', __( 'دکمه‌ی شناور فهرست', 'zarincoach' ), true, __( 'دکمه‌ای با حلقه‌ی پیشرفت مطالعه که فهرست را در هر نقطه از مقاله باز می‌کند.', 'zarincoach' ) ),
						),
						'post_toc_enable'
					)
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'blog_share',
				__( 'اشتراک‌گذاری', 'zarincoach' ),
				'fa-solid fa-share-nodes',
				__( 'دکمه‌های اشتراک در سه جایگاه: کنار نویسنده، نوار عمودی چسبان (دسکتاپ) و باکس پایان مقاله.', 'zarincoach' ),
				array_merge(
					array( zc_f_switch( 'post_share_enable', __( 'دکمه‌های اشتراک‌گذاری', 'zarincoach' ), true ) ),
					zc_req_on(
						array(
							zc_f(
								'checkbox',
								'post_share_networks',
								__( 'شبکه‌ها', 'zarincoach' ),
								array(
									'subtitle' => __( '«بیشتر» منوی اشتراک خود دستگاه را باز می‌کند. «چاپ» فقط در باکس پایانی.', 'zarincoach' ),
									'options'  => array(
										'telegram' => __( 'تلگرام', 'zarincoach' ),
										'whatsapp' => __( 'واتس‌اپ', 'zarincoach' ),
										'x'        => __( 'ایکس (توییتر)', 'zarincoach' ),
										'linkedin' => __( 'لینکدین', 'zarincoach' ),
										'facebook' => __( 'فیسبوک', 'zarincoach' ),
										'email'    => __( 'ایمیل', 'zarincoach' ),
										'copy'     => __( 'کپی پیوند', 'zarincoach' ),
										'print'    => __( 'چاپ', 'zarincoach' ),
										'native'   => __( 'بیشتر (اشتراک دستگاه)', 'zarincoach' ),
									),
									'default'  => array(
										'telegram' => '1',
										'whatsapp' => '1',
										'x'        => '1',
										'linkedin' => '1',
										'facebook' => '0',
										'email'    => '1',
										'copy'     => '1',
										'print'    => '1',
										'native'   => '1',
									),
									'class'    => 'zc-grid-checks',
								)
							),
							zc_f_buttons(
								'post_share_style',
								__( 'سبک دکمه‌ها', 'zarincoach' ),
								array(
									'brand'   => __( 'رنگ شبکه‌ها', 'zarincoach' ),
									'theme'   => __( 'رنگ قالب', 'zarincoach' ),
									'outline' => __( 'خطی', 'zarincoach' ),
								),
								'brand'
							),
							zc_f_switch( 'post_share_top', __( 'کنار نویسنده (بالای مقاله)', 'zarincoach' ), true ),
							zc_f_switch( 'post_share_sticky', __( 'نوار عمودی چسبان', 'zarincoach' ), true, __( 'فقط دسکتاپ (۱۰۲۴ پیکسل به بالا).', 'zarincoach' ) ),
							zc_f_switch( 'post_share_bottom', __( 'باکس پایان مقاله', 'zarincoach' ), true ),
							zc_req( zc_f_text( 'post_share_box_title', __( 'عنوان باکس پایانی', 'zarincoach' ), 'این مطلب را به اشتراک بگذارید' ), 'post_share_bottom' ),
							zc_req( zc_f_textarea( 'post_share_box_text', __( 'توضیح باکس پایانی', 'zarincoach' ), 'اگر این نوشته برایتان مفید بود، آن را برای کسی بفرستید که شاید امروز به آن نیاز دارد.', '', 2 ), 'post_share_bottom' ),
						),
						'post_share_enable'
					)
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'blog_comments',
				__( 'دیدگاه‌ها', 'zarincoach' ),
				'fa-regular fa-comments',
				__( 'فرم دیدگاه مقاله‌ها. تأیید و مدیریت دیدگاه‌ها از «تنظیمات ← گفتگو»ی وردپرس انجام می‌شود.', 'zarincoach' ),
				array(
					zc_f_text( 'comments_form_title', __( 'عنوان فرم', 'zarincoach' ), 'دوست داریم نظرتان را بدانیم' ),
					zc_f_textarea( 'comments_note', __( 'یادداشت بالای فرم', 'zarincoach' ), 'خیالتان راحت، نشانی ایمیل‌تان هرگز نمایش داده نمی‌شود. فقط خواهش ما این است که شماره تماس یا اطلاعات خصوصی‌تان را در دیدگاه ننویسید؛ دیدگاه‌ها پس از بررسی منتشر می‌شوند و جای خالیِ یک گفت‌وگوی شخصی را هم پر نمی‌کنند.', __( 'خالی = بدون یادداشت.', 'zarincoach' ), 3 ),
					zc_f_switch( 'comments_url_field', __( 'فیلد «وب‌سایت»', 'zarincoach' ), false, __( 'غیرفعال بودن آن، هرزنامه‌ی تبلیغاتی را به‌شدت کم می‌کند.', 'zarincoach' ) ),
					zc_f_note(
						'comments_wp_note',
						__( 'تنظیمات وردپرس', 'zarincoach' ),
						sprintf(
							/* translators: %s: پیوند تنظیمات گفتگو */
							__( 'پاسخ تودرتو، تأیید دستی و فهرست سیاه واژه‌ها در %s فعال می‌شوند.', 'zarincoach' ),
							'<a href="' . esc_url( admin_url( 'options-discussion.php' ) ) . '">' . esc_html__( 'تنظیمات ← گفتگو', 'zarincoach' ) . '</a>'
						)
					),
				),
				array( 'subsection' => true )
			),
		);
	}
endif;

/* =====================================================================
 * ۸. کتابخانه‌ی طرحواره‌ها
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_schemas' ) ) :
	/**
	 * کتابخانه‌ی طرحواره‌ها و الگوهای ذهنی.
	 *
	 * @return array
	 */
	function zc_panel_schemas() {
		return array(
			zc_panel_section(
				'schemas',
				__( 'کتابخانه‌ی طرحواره‌ها', 'zarincoach' ),
				'fa-solid fa-brain',
				__( 'صفحه‌ی هر طرحواره/ذهنیت و صفحه‌ی گروه‌ها. محتوای مدخل‌ها از «طرحواره‌ها» در پیشخوان ویرایش می‌شود.', 'zarincoach' ),
				array(
					zc_f_group( 'sc_parts_group', __( 'اجزای صفحه‌ی مدخل', 'zarincoach' ) ),
					zc_f_switch( 'sc_toc', __( 'فهرست «در این صفحه»', 'zarincoach' ), true ),
					zc_f_switch( 'sc_pager', __( 'مدخل قبلی و بعدیِ همان گروه', 'zarincoach' ), true ),
					zc_f_switch( 'sc_related', __( 'طرحواره‌های مرتبط', 'zarincoach' ), true ),

					zc_f_group( 'sc_disc_group', __( 'یادداشت آموزشی (سلب مسئولیت)', 'zarincoach' ), __( 'طبق آیین‌نامه‌ی اخلاقی نظام روان‌شناسی، روشن کنید که محتوا جایگزین ارزیابی تخصصی نیست.', 'zarincoach' ) ),
					zc_f_textarea( 'sc_disclaimer', __( 'متن صفحه‌ی مدخل', 'zarincoach' ), 'این مطلب برای خودشناسی و آشنایی با مفاهیم نوشته شده و جایگزین ارزیابی تخصصی نیست. اگر خودتان را در یکی دو توصیف دیدید، نگران نشوید؛ داشتن «مشکل» یا «اختلال» نیست، همه‌ی ما کم‌وبیش طرحواره‌هایی داریم. برای شناخت دقیق الگوهایتان، پرسش‌نامه‌ی استاندارد و گفت‌وگو در جلسه‌ی ارزیابی بهترین نقطه‌ی شروع است. و یادتان باشد: اگر در بحران هستید یا به آسیب زدن به خود فکر می‌کنید، همین حالا با اورژانس اجتماعی ۱۲۳ یا صدای مشاور ۱۴۸۰ تماس بگیرید.', '', 5 ),
					zc_f_textarea( 'sc_group_disclaimer', __( 'متن صفحه‌ی گروه‌ها', 'zarincoach' ), 'این توضیحات صرفاً برای خودشناسی است و جایگزین ارزیابی تخصصی نیست. هر وقت خواستید شناخت دقیق‌تری از الگوهایتان داشته باشید، جلسه‌ی ارزیابی بهترین نقطه‌ی شروع است.', '', 3 ),

					zc_f_group( 'sc_cta_group', __( 'فراخوان پایان صفحه', 'zarincoach' ) ),
					zc_f_switch( 'sc_cta_enable', __( 'نمایش فراخوان', 'zarincoach' ), true ),
					zc_req( zc_f_text( 'sc_cta_eyebrow', __( 'برچسب', 'zarincoach' ), 'از شناختن تا تغییر' ), 'sc_cta_enable' ),
					zc_req( zc_f_text( 'sc_cta_title', __( 'عنوان', 'zarincoach' ), 'دوست دارید نقشه‌ی طرحواره‌های خودتان را ببینید؟' ), 'sc_cta_enable' ),
					zc_req( zc_f_textarea( 'sc_cta_text', __( 'متن', 'zarincoach' ), 'در جلسه‌ی ارزیابی، با پرسش‌نامه‌ی استاندارد یانگ و یک گفت‌وگوی آسوده، طرحواره‌ها و ذهنیت‌های فعال شما مشخص می‌شود و با هم یک مسیر کوچینگ شخصی برای تقویت «بزرگسال سالم» طراحی می‌کنیم.', '', 3 ), 'sc_cta_enable' ),
					zc_req( zc_f_text( 'sc_cta_btn_text', __( 'متن دکمه', 'zarincoach' ), 'رزرو جلسه‌ی ارزیابی' ), 'sc_cta_enable' ),
					zc_req( zc_f_ltr( 'sc_cta_btn_url', __( 'پیوند دکمه', 'zarincoach' ), '', __( 'خالی = برگه‌ی رزرو نوبت.', 'zarincoach' ) ), 'sc_cta_enable' ),
					zc_req( zc_f_switch( 'sc_cta_back', __( 'دکمه‌ی «بازگشت به کتابخانه»', 'zarincoach' ), true ), 'sc_cta_enable' ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۹. صفحات ویژه (۴۰۴ + تعمیر و نگهداری)
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_pages' ) ) :
	/**
	 * صفحات ویژه.
	 *
	 * @return array
	 */
	function zc_panel_pages() {
		return array(
			zc_panel_section(
				'pages',
				__( 'صفحات ویژه', 'zarincoach' ),
				'fa-solid fa-file-circle-exclamation',
				__( 'صفحه‌ی خطای ۴۰۴ و حالت تعمیر و نگهداری.', 'zarincoach' ),
				array(
					zc_f_group( 'p404_group', __( 'صفحه‌ی ۴۰۴', 'zarincoach' ) ),
					zc_f_text( 'p404_title', __( 'عنوان', 'zarincoach' ), 'این مسیر پیدا نشد' ),
					zc_f_textarea( 'p404_text', __( 'متن', 'zarincoach' ), 'گاهی مسیرها عوض می‌شوند؛ درست مثل الگوهای ذهنی. از جستجو یا پیوندهای زیر استفاده کنید تا به مقصد برسید.', '', 2 ),
					zc_f_text( 'p404_button', __( 'متن دکمه‌ی بازگشت', 'zarincoach' ), 'بازگشت به صفحه اصلی' ),

					zc_f_group( 'maint_group', __( 'حالت تعمیر و نگهداری', 'zarincoach' ), __( 'سایت برای بازدیدکنندگان با کد ۵۰۳ (استاندارد گوگل برای توقف موقت) بسته می‌شود؛ مدیران و ویرایشگران سایت را عادی می‌بینند.', 'zarincoach' ) ),
					zc_f_switch( 'maint_enable', __( 'فعال‌سازی', 'zarincoach' ), false ),
					zc_req( zc_f_text( 'maint_title', __( 'عنوان', 'zarincoach' ), 'به‌زودی برمی‌گردیم' ), 'maint_enable' ),
					zc_req( zc_f_textarea( 'maint_text', __( 'متن', 'zarincoach' ), 'در حال به‌روزرسانی وب‌سایت هستیم تا تجربه‌ی بهتری برایتان بسازیم. برای هماهنگی جلسه همچنان می‌توانید از راه‌های زیر با ما در تماس باشید.', '', 3 ), 'maint_enable' ),
					zc_req( zc_f_text( 'maint_until', __( 'زمان تقریبی بازگشت', 'zarincoach' ), '', __( 'اختیاری؛ مثال: «تا ساعت ۱۸ امروز».', 'zarincoach' ) ), 'maint_enable' ),
					zc_req( zc_f_switch( 'maint_contact', __( 'نمایش راه‌های تماس', 'zarincoach' ), true ), 'maint_enable' ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * صفحه‌ی اصلی کلاسیک (فقط بدون المنتور)
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_home_classic' ) ) :
	/**
	 * بخش‌های صفحه‌ی اصلی داخلی (بدون المنتور).
	 *
	 * @return array
	 */
	function zc_panel_home_classic() {
		$sections = function_exists( 'zc_sections_home' ) ? zc_sections_home() : array();
		foreach ( $sections as $i => $section ) {
			$sections[ $i ]['zc_classic'] = true;
			$sections[ $i ]['class']      = ' zc-sec-' . ( isset( $section['id'] ) ? $section['id'] : 'home' );
			if ( isset( $section['icon'] ) && 0 === strpos( $section['icon'], 'el ' ) ) {
				$sections[ $i ]['icon'] = 'fa-solid fa-house-chimney';
			}
		}
		return $sections;
	}
endif;

/* =====================================================================
 * ۱۰. اطلاعات تماس
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_contact' ) ) :
	/**
	 * اطلاعات تماس و فرم.
	 *
	 * @return array
	 */
	function zc_panel_contact() {
		return array(
			zc_panel_section(
				'contact',
				__( 'اطلاعات تماس', 'zarincoach' ),
				'fa-solid fa-address-book',
				__( 'یک منبع واحد برای سربرگ، پاورقی، دکمه‌ی شناور، نوار موبایل، صفحه‌ی تماس، صفحات قوانین و اسکیما.', 'zarincoach' ),
				array(
					zc_f_group( 'contact_phones_group', __( 'تلفن‌ها', 'zarincoach' ) ),
					zc_f_ltr( 'contact_phone', __( 'تلفن رزرو نوبت', 'zarincoach' ), '۰۹۱۷۹۷۰۶۶۸۸', __( 'تلفن اصلی؛ در دکمه‌های تماس و اسکیما استفاده می‌شود.', 'zarincoach' ) ),
					zc_f_text( 'contact_phone_label', __( 'برچسب تلفن رزرو', 'zarincoach' ), 'تلفن رزرو نوبت' ),
					zc_f_ltr( 'contact_mobile', __( 'تلفن دوم / مطب', 'zarincoach' ), '۰۹۹۱۴۱۴۲۰۷۰', __( 'اختیاری؛ برای حذف، خالی بگذارید.', 'zarincoach' ) ),
					zc_req( zc_f_text( 'contact_mobile_label', __( 'برچسب تلفن دوم', 'zarincoach' ), 'تلفن مطب' ), 'contact_mobile', '!=', '' ),

					zc_f_group( 'contact_place_group', __( 'نشانی و ساعات', 'zarincoach' ) ),
					zc_f_ltr( 'contact_email', __( 'ایمیل', 'zarincoach' ), 'info@maryam-jamali.ir', '', 'email' ),
					zc_f_textarea( 'contact_address', __( 'نشانی', 'zarincoach' ), 'بوشهر، خیابان رئیس‌علی دلواری، حدفاصل چهارراه کشتیرانی و شیلات، روبه‌روی خیابان حافظ و اداره کل بنیاد شهید، ساختمان پزشکان طبیب، طبقه پنجم، واحد ۵۰۳', '', 3 ),
					zc_f_text( 'contact_city', __( 'شهر', 'zarincoach' ), 'بوشهر' ),
					zc_f_text( 'contact_hours', __( 'ساعات پاسخ‌گویی', 'zarincoach' ), 'شنبه تا چهارشنبه | ۱۶:۰۰ تا ۲۰:۳۰' ),
					zc_f_textarea( 'contact_map', __( 'کد نقشه (iframe)', 'zarincoach' ), '', __( 'کد جاسازی نقشه (نشان، بلد یا گوگل). بارگذاری تنبل انجام می‌شود.', 'zarincoach' ), 3, array( 'class' => 'zc-ltr' ) ),

					zc_f_group( 'contact_form_group', __( 'فرم تماس', 'zarincoach' ) ),
					zc_f_switch( 'contact_form_enable', __( 'فرم تماس داخلی', 'zarincoach' ), true, __( 'فرم امن و سبک با شورت‌کد [zc_contact] و ویجت «فرم رزرو/تماس».', 'zarincoach' ) ),
					zc_req( zc_f_ltr( 'contact_form_email', __( 'ایمیل دریافت پیام‌ها', 'zarincoach' ), '', __( 'خالی = ایمیل مدیر سایت.', 'zarincoach' ), 'email' ), 'contact_form_enable' ),
					zc_f_ltr( 'contact_form_shortcode', __( 'شورت‌کد فرم دلخواه', 'zarincoach' ), '', __( 'برای Contact Form 7 و افزونه‌های مشابه؛ جایگزین فرم داخلی می‌شود.', 'zarincoach' ) ),
				),
				array( 'zc_group' => __( 'اطلاعات و ارتباط', 'zarincoach' ) )
			),
		);
	}
endif;

/* =====================================================================
 * ۱۱. شبکه‌های اجتماعی
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_social' ) ) :
	/**
	 * شبکه‌های اجتماعی و پیام‌رسان‌ها.
	 *
	 * @return array
	 */
	function zc_panel_social() {
		$handle = __( 'نشانی کامل یا شناسه با @', 'zarincoach' );
		return array(
			zc_panel_section(
				'social',
				__( 'شبکه‌های اجتماعی', 'zarincoach' ),
				'fa-solid fa-share-nodes',
				__( 'موارد خالی هیچ‌جا نمایش داده نمی‌شوند. همه‌ی نشانی‌ها به‌طور خودکار به اسکیمای Person (sameAs) افزوده می‌شوند.', 'zarincoach' ),
				array(
					zc_f_group( 'social_msg_group', __( 'پیام‌رسان‌ها', 'zarincoach' ) ),
					zc_f_ltr( 'social_telegram', __( 'تلگرام', 'zarincoach' ), 'https://t.me/Maaryam_jamali', $handle ),
					zc_f_ltr( 'social_bale', __( 'بله', 'zarincoach' ), 'https://ble.ir/admin_maryamjamali', $handle ),
					zc_f_ltr( 'social_eitaa', __( 'ایتا', 'zarincoach' ), '', $handle ),
					zc_f_ltr( 'social_rubika', __( 'روبیکا', 'zarincoach' ), '' ),
					zc_f_ltr( 'social_whatsapp', __( 'واتس‌اپ', 'zarincoach' ), '', __( 'شماره با پیش‌شماره یا پیوند کامل.', 'zarincoach' ) ),
					zc_f_group( 'social_net_group', __( 'شبکه‌های اجتماعی', 'zarincoach' ) ),
					zc_f_ltr( 'social_instagram', __( 'اینستاگرام', 'zarincoach' ), 'https://instagram.com/Maaryam_jamali' ),
					zc_f_ltr( 'social_linkedin', __( 'لینکدین', 'zarincoach' ), '' ),
					zc_f_ltr( 'social_youtube', __( 'یوتیوب', 'zarincoach' ), '' ),
					zc_f_ltr( 'social_aparat', __( 'آپارات', 'zarincoach' ), '' ),
					zc_f_ltr( 'social_twitter', __( 'ایکس (توییتر)', 'zarincoach' ), '' ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۱۲. حقوقی، مجوزها و نمادها
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_legal' ) ) :
	/**
	 * اطلاعات حقوقی، نمادهای اعتماد، منبع نظرات و اعلان کوکی.
	 *
	 * @return array
	 */
	function zc_panel_legal() {
		return array(
			zc_panel_section(
				'legal',
				__( 'حقوقی و نمادها', 'zarincoach' ),
				'fa-solid fa-scale-balanced',
				__( 'این مقادیر در پاورقی، رزومه، اسکیما و متن برگه‌های قوانین (شورت‌کد [zc_info]) استفاده می‌شوند. فقط اطلاعات واقعی و قابل استعلام وارد کنید.', 'zarincoach' ),
				array(
					zc_f_group( 'legal_id_group', __( 'ارائه‌دهنده و دامنه‌ی رسمی', 'zarincoach' ) ),
					zc_f_text( 'legal_owner', __( 'نام ارائه‌دهنده‌ی خدمات', 'zarincoach' ), 'مریم جمالی' ),
					zc_f_ltr( 'legal_domain', __( 'دامنه‌ی رسمی', 'zarincoach' ), 'Maryam-Jamali.ir', __( 'بدون http؛ مثال: Maryam-Jamali.ir', 'zarincoach' ) ),
					zc_f_textarea( 'legal_domain_note', __( 'اطلاعیه‌ی دامنه‌ی رسمی', 'zarincoach' ), 'تمام خدمات، تست‌ها، دوره‌ها و نوبت‌دهی مریم جمالی فقط از طریق دامنه رسمی Maryam-Jamali.ir ارائه می‌شود. هر وب‌سایت، درگاه پرداخت یا حساب دیگری با این نام، غیررسمی است.', '', 3 ),

					zc_f_group( 'legal_pro_group', __( 'مدرک و مجوزهای حرفه‌ای', 'zarincoach' ) ),
					zc_f_text( 'legal_degree', __( 'مدرک تحصیلی', 'zarincoach' ), 'کارشناس ارشد روان‌شناسی بالینی' ),
					zc_f_text( 'legal_license_label', __( 'عنوان مجوز حرفه‌ای', 'zarincoach' ), 'شماره پروانه اشتغال' ),
					zc_f_text( 'legal_license_no', __( 'شماره‌ی پروانه‌ی اشتغال', 'zarincoach' ), '۲۸۸۵۸', __( 'صادرشده از سازمان نظام روان‌شناسی و مشاوره.', 'zarincoach' ) ),
					zc_f_text( 'legal_pco_label', __( 'عنوان کد عضویت', 'zarincoach' ), 'کد نظام روان‌شناسی' ),
					zc_f_text( 'legal_pco_code', __( 'کد نظام روان‌شناسی', 'zarincoach' ), '۷۰۷۶۳', __( 'خالی = نمایش داده نمی‌شود.', 'zarincoach' ) ),
					zc_f_textarea( 'legal_emergency', __( 'اطلاعیه‌ی موارد اضطراری', 'zarincoach' ), 'این وب‌سایت خدمات اورژانسی ارائه نمی‌دهد. در شرایط بحران یا خطر برای خود یا دیگران با اورژانس اجتماعی ۱۲۳، صدای مشاور بهزیستی ۱۴۸۰ یا اورژانس ۱۱۵ تماس بگیرید.', '', 3 ),

					zc_f_group( 'legal_badges_group', __( 'نمادهای اعتماد', 'zarincoach' ), __( 'کد HTML هر نماد را از پنل مربوط کپی کنید. در ویجت «نمادهای اعتماد» و پاورقی نمایش داده می‌شوند.', 'zarincoach' ) ),
					zc_f_textarea( 'legal_enamad', __( 'اینماد (نماد اعتماد الکترونیکی)', 'zarincoach' ), '', __( 'از enamad.ir؛ خالی = جای‌نگهدار.', 'zarincoach' ), 3, array( 'class' => 'zc-ltr' ) ),
					zc_f_textarea( 'legal_samandehi', __( 'نشان ساماندهی', 'zarincoach' ), '', __( 'از samandehi.ir', 'zarincoach' ), 3, array( 'class' => 'zc-ltr' ) ),
					zc_f_textarea( 'legal_zarinpal', __( 'نشان اعتماد زرین‌پال', 'zarincoach' ), '', '', 3, array( 'class' => 'zc-ltr' ) ),
					zc_f_textarea( 'legal_payir', __( 'نشان درگاه پی (Pay.ir)', 'zarincoach' ), '', '', 3, array( 'class' => 'zc-ltr' ) ),

					zc_f_group( 'legal_reviews_group', __( 'منبع نظرات مراجعان', 'zarincoach' ), __( 'طبق آیین‌نامه‌ی اخلاقی، منبع نظرات باید شفاف باشد. زیر همه‌ی بخش‌های نظرات نمایش داده می‌شود.', 'zarincoach' ) ),
					zc_f_textarea( 'testimonials_source_note', __( 'یادداشت منبع', 'zarincoach' ), 'این نظرات از بخش نظرات کاربران در سامانه نوبت‌دهی دکترتو نقل شده‌اند؛ متن‌ها بدون تغییر محتوایی آمده و بخش‌های حذف‌شده با «…» مشخص شده است.', '', 2 ),
					zc_f_text( 'testimonials_source_label', __( 'متن پیوند منبع', 'zarincoach' ), 'مشاهده همه نظرات در دکترتو' ),
					zc_f_ltr( 'testimonials_source_url', __( 'نشانی منبع', 'zarincoach' ), 'https://doctoreto.com/doctor/jamali/GXPYab', '', 'url' ),

					zc_f_group( 'cookie_group', __( 'اعلان کوکی', 'zarincoach' ), __( 'نوار کوچک و سبک پایین صفحه؛ پس از تأیید، تا یک سال نمایش داده نمی‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'cookie_enable', __( 'نمایش اعلان', 'zarincoach' ), false ),
					zc_req( zc_f_textarea( 'cookie_text', __( 'متن', 'zarincoach' ), 'این وب‌سایت فقط از کوکی‌های ضروری برای عملکرد صحیح، امنیت و به‌خاطر سپردن ترجیحات شما (مانند حالت تاریک) استفاده می‌کند.', '', 2 ), 'cookie_enable' ),
					zc_req( zc_f_text( 'cookie_button', __( 'متن دکمه', 'zarincoach' ), 'متوجه شدم' ), 'cookie_enable' ),
					zc_req( zc_f_text( 'cookie_link_text', __( 'متن پیوند سیاست کوکی‌ها', 'zarincoach' ), 'سیاست کوکی‌ها', __( 'به برگه‌ی «سیاست کوکی‌ها» (یا در نبود آن، حریم خصوصی) پیوند داده می‌شود. خالی = بدون پیوند.', 'zarincoach' ) ), 'cookie_enable' ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۱۳. ارتباط سریع
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_engagement' ) ) :
	/**
	 * دکمه‌ی شناور و نوار رزرو موبایل.
	 *
	 * @return array
	 */
	function zc_panel_engagement() {
		return array(
			zc_panel_section(
				'engagement',
				__( 'ارتباط سریع', 'zarincoach' ),
				'fa-solid fa-headset',
				__( 'دکمه‌ی شناور پشتیبانی و نوار رزرو موبایل؛ مستقل از المنتور در همه‌ی صفحات.', 'zarincoach' ),
				array_merge(
					array(
						zc_f_group( 'float_group', __( 'دکمه‌ی شناور ارتباط', 'zarincoach' ), __( 'دکمه‌ای گرد در گوشه‌ی صفحه که کانال‌های ارتباطی را باز می‌کند.', 'zarincoach' ) ),
						zc_f_switch( 'float_enable', __( 'نمایش دکمه‌ی شناور', 'zarincoach' ), true ),
					),
					zc_req_on(
						array(
							zc_f_buttons(
								'float_position',
								__( 'موقعیت', 'zarincoach' ),
								array(
									'right' => __( 'راست', 'zarincoach' ),
									'left'  => __( 'چپ', 'zarincoach' ),
								),
								'left'
							),
							zc_f_text( 'float_title', __( 'عنوان پنجره', 'zarincoach' ), 'پشتیبانی و رزرو نوبت' ),
							zc_f_textarea( 'float_text', __( 'متن راهنما', 'zarincoach' ), 'برای هماهنگی جلسه یا پرسش درباره خدمات، یکی از راه‌های زیر را انتخاب کنید. پاسخ‌گویی: شنبه تا چهارشنبه، ۱۶ تا ۲۰:۳۰.', '', 2 ),
							zc_f(
								'checkbox',
								'float_channels',
								__( 'کانال‌ها', 'zarincoach' ),
								array(
									'subtitle' => __( 'مقادیر از «اطلاعات تماس» و «شبکه‌های اجتماعی» خوانده می‌شوند؛ کانال‌های خالی نمایش داده نمی‌شوند.', 'zarincoach' ),
									'options'  => array(
										'phone'     => __( 'تماس تلفنی (رزرو)', 'zarincoach' ),
										'telegram'  => __( 'تلگرام', 'zarincoach' ),
										'bale'      => __( 'بله', 'zarincoach' ),
										'eitaa'     => __( 'ایتا', 'zarincoach' ),
										'whatsapp'  => __( 'واتس‌اپ', 'zarincoach' ),
										'instagram' => __( 'اینستاگرام', 'zarincoach' ),
										'email'     => __( 'ایمیل', 'zarincoach' ),
										'booking'   => __( 'صفحه‌ی رزرو آنلاین', 'zarincoach' ),
									),
									'default'  => array(
										'phone'     => '1',
										'telegram'  => '1',
										'bale'      => '1',
										'eitaa'     => '1',
										'whatsapp'  => '1',
										'instagram' => '1',
										'email'     => '0',
										'booking'   => '1',
									),
									'class'    => 'zc-grid-checks',
								)
							),
							zc_f_ltr( 'float_booking_url', __( 'پیوند صفحه‌ی رزرو', 'zarincoach' ), '/booking/' ),
							zc_f_switch( 'float_pulse', __( 'تپش ملایم', 'zarincoach' ), true ),
							zc_f_switch( 'float_mobile', __( 'نمایش در موبایل', 'zarincoach' ), true, __( 'در موبایل بالای نوار رزرو قرار می‌گیرد.', 'zarincoach' ) ),
						),
						'float_enable'
					),
					array(
						zc_f_group( 'mobilebar_group', __( 'نوار رزرو ثابت موبایل', 'zarincoach' ) ),
						zc_f_switch( 'header_mobile_cta_enable', __( 'نمایش نوار پایین در موبایل', 'zarincoach' ), true ),
					),
					zc_req_on(
						array(
							zc_f_text( 'header_mobile_cta_text', __( 'متن دکمه', 'zarincoach' ), 'رزرو جلسه ارزیابی اولیه' ),
							zc_f_ltr( 'header_mobile_cta_url', __( 'پیوند دکمه', 'zarincoach' ), '/booking/' ),
							zc_f_select(
								'header_mobile_cta_icon',
								__( 'دکمه‌ی کناری', 'zarincoach' ),
								array(
									'phone'    => __( 'تماس تلفنی', 'zarincoach' ),
									'telegram' => __( 'تلگرام', 'zarincoach' ),
									'bale'     => __( 'بله', 'zarincoach' ),
									'social'   => __( 'اولین شبکه‌ی اجتماعی', 'zarincoach' ),
									'none'     => __( 'بدون دکمه', 'zarincoach' ),
								),
								'phone'
							),
						),
						'header_mobile_cta_enable'
					)
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۱۴. سئو (+ زیربخش‌ها)
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_seo' ) ) :
	/**
	 * سئو و اسکیما.
	 *
	 * @return array
	 */
	function zc_panel_seo() {
		return array(
			zc_panel_section(
				'seo',
				__( 'سئو', 'zarincoach' ),
				'fa-solid fa-magnifying-glass-chart',
				__( 'عنوان‌ها، متا، robots، canonical، اوپن‌گراف، نقشه‌ی سایت و گراف کامل اسکیما. برای هر برگه/نوشته هم جعبه‌ی «سئو» در ویرایشگر وجود دارد.', 'zarincoach' ),
				array(
					zc_f_switch( 'seo_enable', __( 'سیستم سئوی داخلی', 'zarincoach' ), true, __( 'با نصب Yoast، Rank Math، AIOSEO، SEOPress یا The SEO Framework خودکار کنار می‌رود.', 'zarincoach' ) ),
					zc_f_switch( 'seo_yoast_bridge', __( 'ادغام اسکیمای قالب با Yoast SEO', 'zarincoach' ), true, __( 'با Yoast SEO / Premium فعال: گره‌های تخصصی قالب (مطب، پروانه و اعتبارنامه‌ها، خدمات، محصولات، پرسش‌ها، طرحواره‌ها) در یک گراف واحد با Yoast ادغام می‌شوند و مسیر راهنمای Yoast همان مسیر راهنمای قالب می‌شود.', 'zarincoach' ) ),
					zc_f_select(
						'seo_title_sep',
						__( 'جداکننده‌ی عنوان', 'zarincoach' ),
						array(
							'|' => '|',
							'–' => '–',
							'—' => '—',
							'·' => '·',
						),
						'|'
					),
					zc_f_switch( 'seo_breadcrumbs', __( 'مسیر راهنما (Breadcrumb)', 'zarincoach' ), true, __( 'همراه با اسکیمای BreadcrumbList.', 'zarincoach' ) ),
					zc_f_group( 'seo_index_group', __( 'ایندکس و نقشه‌ی سایت', 'zarincoach' ), __( 'جلوگیری از صفحات کم‌ارزش و تکراری در نتایج گوگل.', 'zarincoach' ) ),
					zc_f_switch( 'seo_noindex_tags', __( 'noindex بایگانی برچسب‌ها', 'zarincoach' ), true, __( 'پیوندها همچنان دنبال می‌شوند (follow).', 'zarincoach' ) ),
					zc_f_switch( 'seo_noindex_date', __( 'noindex بایگانی‌های تاریخ', 'zarincoach' ), true ),
					zc_f_switch( 'seo_redirect_attachment', __( 'انتقال ۳۰۱ صفحات پیوست', 'zarincoach' ), true, __( 'به نوشته‌ی والد یا خود فایل.', 'zarincoach' ) ),
					zc_f_switch( 'seo_redirect_author', __( 'انتقال ۳۰۱ بایگانی نویسنده', 'zarincoach' ), true, __( 'به برگه‌ی «درباره من»؛ سایت تک‌نویسنده است.', 'zarincoach' ) ),
					zc_f_switch( 'seo_sitemap', __( 'بهینه‌سازی نقشه‌ی سایت وردپرس', 'zarincoach' ), true, __( 'حذف کاربران، قالب‌های المنتور و صفحات noindex؛ افزودن lastmod.', 'zarincoach' ) ),
				),
				array( 'zc_group' => __( 'فنی', 'zarincoach' ) )
			),

			zc_panel_section(
				'seo_person',
				__( 'هویت حرفه‌ای', 'zarincoach' ),
				'fa-solid fa-id-card',
				__( 'اسکیمای Person برای E-E-A-T. طبق آیین‌نامه‌ی نظام روان‌شناسی، عنوان یا مدرک خلاف واقع ممنوع است. مدرک و شماره‌ی پروانه از «حقوقی و نمادها» خوانده می‌شود.', 'zarincoach' ),
				array(
					zc_f_text( 'seo_person_name', __( 'نام کامل', 'zarincoach' ), 'مریم جمالی' ),
					zc_f_ltr( 'seo_person_alt', __( 'نام لاتین', 'zarincoach' ), 'Maryam Jamali' ),
					zc_f_text( 'seo_person_job', __( 'عنوان تخصصی', 'zarincoach' ), 'روان‌شناس الگوهای ذهنی و رفتاری در مسیر رشد' ),
					zc_f_textarea( 'seo_person_desc', __( 'معرفی کوتاه', 'zarincoach' ), 'آموزش و همراهی رشدمحور برای شناخت الگوهایی مثل کمال‌گرایی، اهمالکاری و تصمیم‌گیری ناکارآمد؛ از شناخت به وضوح، تصمیم و اقدام.', '', 3 ),
					zc_f_media( 'seo_person_image', __( 'تصویر پروفایل', 'zarincoach' ), '', array( 'default' => array( 'url' => '' ) ) ),
					zc_f_textarea( 'seo_person_knows', __( 'حوزه‌های تخصص (knowsAbout)', 'zarincoach' ), "کمال‌گرایی\nاهمالکاری\nتصمیم‌گیری\nطرحواره‌درمانی\nمهارت‌های زندگی\nروان‌شناسی مدیران\nالگوهای ذهنی و رفتاری", __( 'هر مورد در یک خط.', 'zarincoach' ), 6 ),
					zc_f(
						'multi_text',
						'seo_person_sameas',
						__( 'پروفایل‌های رسمی (sameAs)', 'zarincoach' ),
						array(
							'subtitle'  => __( 'نشانی کامل صفحات رسمی؛ شبکه‌های اجتماعی خودکار افزوده می‌شوند.', 'zarincoach' ),
							'default'   => array( 'https://doctoreto.com/doctor/jamali/GXPYab' ),
							'add_text'  => __( 'افزودن نشانی', 'zarincoach' ),
							'class'     => 'zc-ltr',
							'show_empty' => false,
						)
					),
					zc_f_switch( 'seo_author_person', __( 'نویسنده‌ی همه‌ی مقالات همین شخص است', 'zarincoach' ), true, __( 'author مقاله‌ها در اسکیما به همین Person متصل می‌شود.', 'zarincoach' ) ),
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'seo_home',
				__( 'صفحه‌ی اصلی و اشتراک', 'zarincoach' ),
				'fa-solid fa-share-from-square',
				__( 'عنوان و توضیح صفحه‌ی اصلی در نتایج گوگل و تصویر پیش‌فرض اشتراک‌گذاری.', 'zarincoach' ),
				array(
					zc_f_text( 'seo_home_title', __( 'عنوان صفحه‌ی اصلی', 'zarincoach' ), 'مریم جمالی | روان‌شناس الگوهای ذهنی و رفتاری در بوشهر', __( 'حداکثر حدود ۶۰ نویسه.', 'zarincoach' ), array( 'attributes' => array( 'data-zc-count' => '60' ) ) ),
					zc_f_textarea( 'seo_home_desc', __( 'توضیح متای صفحه‌ی اصلی', 'zarincoach' ), 'مریم جمالی، کارشناس ارشد روان‌شناسی بالینی در بوشهر: شناخت و تغییر الگوهای کمال‌گرایی، اهمالکاری و تصمیم‌گیری با جلسات حضوری و آنلاین.', __( 'حداکثر حدود ۱۵۵ نویسه.', 'zarincoach' ), 3, array( 'attributes' => array( 'data-zc-count' => '155' ) ) ),
					zc_f_media( 'seo_og_image', __( 'تصویر پیش‌فرض اشتراک‌گذاری', 'zarincoach' ), __( '۱۲۰۰×۶۳۰ پیکسل (JPEG). برای نوشته‌ها تصویر شاخص اولویت دارد.', 'zarincoach' ), array( 'default' => array( 'url' => '' ) ) ),
					zc_f_ltr( 'seo_twitter', __( 'نام کاربری ایکس', 'zarincoach' ), '', __( 'با @؛ برای twitter:site.', 'zarincoach' ) ),
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'seo_local',
				__( 'کسب‌وکار محلی', 'zarincoach' ),
				'fa-solid fa-location-dot',
				__( 'اسکیمای ProfessionalService برای نتایج محلی و نقشه‌ی گوگل.', 'zarincoach' ),
				array_merge(
					array( zc_f_switch( 'seo_local_enable', __( 'اسکیمای کسب‌وکار محلی', 'zarincoach' ), true ) ),
					zc_req_on(
						array(
							zc_f_text( 'seo_business_name', __( 'نام کسب‌وکار', 'zarincoach' ), 'مطب مریم جمالی' ),
							zc_f_textarea( 'seo_local_street', __( 'نشانی خیابان', 'zarincoach' ), 'خیابان رئیس‌علی دلواری، حدفاصل چهارراه کشتیرانی و شیلات، روبه‌روی خیابان حافظ، ساختمان پزشکان طبیب، طبقه پنجم، واحد ۵۰۳', __( 'بدون شهر و استان.', 'zarincoach' ), 2 ),
							zc_f_text( 'seo_local_region', __( 'استان', 'zarincoach' ), 'استان بوشهر' ),
							zc_f_ltr( 'seo_local_postal', __( 'کد پستی', 'zarincoach' ), '', __( 'اختیاری.', 'zarincoach' ) ),
							zc_f_ltr( 'seo_local_lat', __( 'عرض جغرافیایی', 'zarincoach' ), '', __( 'از پین دقیق مطب (مثال: 28.97778). خالی = بدون geo.', 'zarincoach' ) ),
							zc_f_ltr( 'seo_local_lng', __( 'طول جغرافیایی', 'zarincoach' ), '', __( 'مثال: 50.83930', 'zarincoach' ) ),
							zc_f_ltr( 'seo_local_hours', __( 'ساعات کاری (اسکیما)', 'zarincoach' ), 'Sa-We 16:00-20:30', __( 'روزها: Sa Su Mo Tu We Th Fr؛ چند بازه با کاما. مثال: Sa-We 16:00-20:30, Th 10:00-13:00', 'zarincoach' ) ),
							zc_f_text( 'seo_price_range', __( 'بازه‌ی قیمت', 'zarincoach' ), '۶۵۰ هزار تا ۵٫۱ میلیون تومان' ),
							zc_f_textarea( 'seo_area_served', __( 'محدوده‌ی خدمت‌رسانی', 'zarincoach' ), "بوشهر\nایران", __( 'هر مورد در یک خط؛ «ایران» = جلسات آنلاین در سراسر کشور.', 'zarincoach' ), 2 ),
						),
						'seo_local_enable'
					)
				),
				array( 'subsection' => true )
			),
		);
	}
endif;

/* =====================================================================
 * ۱۵. سرعت (+ زیربخش‌ها)
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_performance' ) ) :
	/**
	 * بهینه‌سازی سرعت.
	 *
	 * @return array
	 */
	function zc_panel_performance() {
		return array(
			zc_panel_section(
				'performance',
				__( 'سرعت', 'zarincoach' ),
				'fa-solid fa-gauge-high',
				__( 'بهینه‌سازی Core Web Vitals بدون نیاز به افزونه. اگر افزونه‌ی کش دارید، گزینه‌های مشابه را فقط در یکی فعال کنید.', 'zarincoach' ),
				array(
					zc_f_group( 'perf_assets_group', __( 'فایل‌ها و منابع', 'zarincoach' ) ),
					zc_f_switch( 'perf_defer', __( 'بارگذاری تأخیری اسکریپت‌ها (defer)', 'zarincoach' ), true ),
					zc_f_switch( 'perf_min_js', __( 'جاوااسکریپت فشرده‌ی قالب', 'zarincoach' ), true, __( 'در حالت SCRIPT_DEBUG همیشه نسخه‌ی کامل بارگذاری می‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'perf_query_strings', __( 'حذف ?ver= از نشانی منابع', 'zarincoach' ), true ),
					zc_f_switch( 'perf_classic_styles', __( 'حذف استایل‌های اضافی گوتنبرگ', 'zarincoach' ), true ),
					zc_f_switch( 'perf_jquery_migrate', __( 'حذف jQuery Migrate', 'zarincoach' ), true ),
					zc_f_switch( 'perf_dashicons', __( 'حذف Dashicons برای بازدیدکنندگان', 'zarincoach' ), true ),
					zc_f_switch( 'perf_emoji', __( 'حذف اسکریپت ایموجی', 'zarincoach' ), true ),
					zc_f_switch( 'perf_embeds', __( 'حذف اسکریپت جاسازی (oEmbed)', 'zarincoach' ), true ),
					zc_f_switch( 'perf_head_cleanup', __( 'پاک‌سازی head', 'zarincoach' ), true, __( 'حذف پیوندهای RSD، WLW، shortlink، REST، کشف oEmbed و resource hints پیش‌فرض.', 'zarincoach' ) ),
					zc_f_group( 'perf_media_group', __( 'تصاویر و رسانه', 'zarincoach' ) ),
					zc_f_switch( 'perf_lazy_images', __( 'بارگذاری تنبل تصاویر و iframeها', 'zarincoach' ), true ),
					zc_f_switch( 'perf_webp', __( 'تولید خودکار WebP', 'zarincoach' ), true, __( 'اندازه‌های فرعی JPEG/PNG جدید به WebP ساخته می‌شوند (۲۵ تا ۴۰٪ سبک‌تر)؛ اگر سرور پشتیبانی کند.', 'zarincoach' ) ),
					zc_f_switch( 'perf_local_avatars', __( 'آواتار محلی به‌جای Gravatar', 'zarincoach' ), true, __( 'بدون درخواست به Gravatar (کند و ناپایدار در ایران).', 'zarincoach' ) ),
					zc_f_group( 'perf_net_group', __( 'شبکه و پیش‌بارگذاری', 'zarincoach' ) ),
					zc_f_switch( 'perf_instant_pages', __( 'پیش‌بارگذاری هوشمند صفحات', 'zarincoach' ), true, __( 'با Speculation Rules وردپرس؛ صفحه با نشانه‌ی قصد کاربر (هاور/لمس) از پیش واکشی می‌شود.', 'zarincoach' ) ),
					zc_f(
						'multi_text',
						'perf_preconnect',
						__( 'پیش‌اتصال (Preconnect)', 'zarincoach' ),
						array(
							'subtitle'   => __( 'دامنه‌های خارجی پرکاربرد، بدون پروتکل (مثال: www.googletagmanager.com).', 'zarincoach' ),
							'add_text'   => __( 'افزودن دامنه', 'zarincoach' ),
							'class'      => 'zc-ltr',
							'show_empty' => false,
						)
					),
				),
				array( 'zc_subgroup' => 'performance' )
			),
			zc_panel_section(
				'performance_el',
				__( 'بهینه‌سازی المنتور', 'zarincoach' ),
				'fa-solid fa-bolt',
				__( 'حذف بار اضافه‌ی المنتور برای بازدیدکنندگان؛ مدیران همیشه همه‌چیز را دریافت می‌کنند.', 'zarincoach' ),
				array(
					zc_f_switch( 'perf_el_google_fonts', __( 'قطع Google Fonts المنتور', 'zarincoach' ), true, __( 'فونت سایت (آراد) محلی است.', 'zarincoach' ) ),
					zc_f_switch( 'perf_el_icons', __( 'حذف آیکن‌فونت‌های بلااستفاده', 'zarincoach' ), true, __( 'eicons و Font Awesome فقط در صفحاتی که ویجت آیکن هسته‌ی المنتور دارند.', 'zarincoach' ) ),
					zc_f_switch( 'perf_el_inline_css', __( 'درون‌خطی کردن CSSهای کوچک', 'zarincoach' ), true, __( 'فایل‌های CSS کمتر از ۸ کیلوبایت داخل HTML قرار می‌گیرند.', 'zarincoach' ) ),
					zc_f_switch( 'perf_el_js', __( 'حذف هوشمند جاوااسکریپت و jQuery', 'zarincoach' ), true, __( 'در صفحاتی که فقط ویجت‌های زرین‌کوچ و ویجت‌های ساده دارند. اگر افزونه‌ای به‌هم ریخت، خاموش کنید.', 'zarincoach' ) ),
				),
				array( 'subsection' => true )
			),
			zc_panel_section(
				'performance_admin',
				__( 'پیشخوان و پایگاه داده', 'zarincoach' ),
				'fa-solid fa-database',
				__( 'کاهش بار سرور و حجم پایگاه داده.', 'zarincoach' ),
				array(
					zc_f_slider( 'perf_revisions', __( 'حداکثر نسخه‌های ذخیره‌شده', 'zarincoach' ), 5, 0, 50, 1, __( 'برای هر نوشته؛ ۰ = بدون نسخه.', 'zarincoach' ) ),
					zc_f_slider( 'perf_autosave', __( 'فاصله‌ی ذخیره‌ی خودکار', 'zarincoach' ), 180, 60, 600, 30, __( 'ثانیه', 'zarincoach' ) ),
					zc_f_select(
						'perf_heartbeat',
						__( 'ضربان وردپرس (Heartbeat)', 'zarincoach' ),
						array(
							'default'   => __( 'پیش‌فرض', 'zarincoach' ),
							'optimized' => __( 'بهینه (فقط در ویرایشگر)', 'zarincoach' ),
							'disabled'  => __( 'غیرفعال', 'zarincoach' ),
						),
						'optimized'
					),
					zc_f_switch( 'perf_block_patterns', __( 'غیرفعال‌سازی الگوهای راه دور گوتنبرگ', 'zarincoach' ), true, __( 'جلوی درخواست اضافه به api.wordpress.org را می‌گیرد.', 'zarincoach' ) ),
				),
				array( 'subsection' => true )
			),
		);
	}
endif;

/* =====================================================================
 * ۱۶. امنیت
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_security' ) ) :
	/**
	 * امنیت.
	 *
	 * @return array
	 */
	function zc_panel_security() {
		return array(
			zc_panel_section(
				'security',
				__( 'امنیت', 'zarincoach' ),
				'fa-solid fa-shield-halved',
				__( 'سخت‌سازی سبک و بی‌خطر وردپرس؛ بدون تداخل با المنتور، فرم‌ها و افزونه‌های امنیتی.', 'zarincoach' ),
				array(
					zc_f_switch( 'sec_xmlrpc', __( 'غیرفعال‌سازی XML-RPC', 'zarincoach' ), true, __( 'رایج‌ترین مسیر حمله‌ی حدس رمز. اگر از اپ موبایل وردپرس یا Jetpack استفاده می‌کنید، خاموش کنید.', 'zarincoach' ) ),
					zc_f_switch( 'sec_user_enum', __( 'جلوگیری از شناسایی نام کاربری', 'zarincoach' ), true, __( 'مسدود کردن ?author=N و فهرست کاربران REST برای بازدیدکنندگان.', 'zarincoach' ) ),
					zc_f_switch( 'sec_login_errors', __( 'پیام خطای ورود یکسان', 'zarincoach' ), true, __( 'مشخص نمی‌کند نام کاربری اشتباه است یا رمز.', 'zarincoach' ) ),
					zc_f_switch( 'sec_file_edit', __( 'غیرفعال‌سازی ویرایشگر فایل', 'zarincoach' ), true, __( 'ویرایش مستقیم کد قالب و افزونه‌ها از پیشخوان بسته می‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'sec_headers', __( 'سرآیندهای امنیتی HTTP', 'zarincoach' ), true, __( 'X-Content-Type-Options، Referrer-Policy، X-Frame-Options (SAMEORIGIN) و Permissions-Policy.', 'zarincoach' ) ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۱۷. المنتور
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_elementor' ) ) :
	/**
	 * ویجت‌های المنتور.
	 *
	 * @return array
	 */
	function zc_panel_elementor() {
		$widgets = array(
			'site-header'  => __( 'سربرگ سایت', 'zarincoach' ),
			'site-footer'  => __( 'پاورقی سایت', 'zarincoach' ),
			'page-title'   => __( 'عنوان برگه', 'zarincoach' ),
			'hero'         => __( 'سربرگ اصلی', 'zarincoach' ),
			'heading'      => __( 'سربرگ بخش', 'zarincoach' ),
			'text'         => __( 'متن و محتوا', 'zarincoach' ),
			'about'        => __( 'درباره من', 'zarincoach' ),
			'services'     => __( 'خدمات و برنامه‌ها', 'zarincoach' ),
			'schema'       => __( 'طرحواره‌ها', 'zarincoach' ),
			'schemas'      => __( 'کتابخانه‌ی طرحواره‌ها', 'zarincoach' ),
			'process'      => __( 'مسیر همراهی', 'zarincoach' ),
			'stats'        => __( 'آمار و ارقام', 'zarincoach' ),
			'testimonials' => __( 'تجربه‌ی مراجعان', 'zarincoach' ),
			'pricing'      => __( 'بسته‌های همراهی', 'zarincoach' ),
			'faq'          => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
			'cta'          => __( 'فراخوان اقدام', 'zarincoach' ),
			'posts'        => __( 'آخرین نوشته‌ها', 'zarincoach' ),
			'contact'      => __( 'اطلاعات تماس', 'zarincoach' ),
			'form'         => __( 'فرم رزرو/تماس', 'zarincoach' ),
			'marquee'      => __( 'نوار کلمات', 'zarincoach' ),
			'list'         => __( 'فهرست ویژگی‌ها', 'zarincoach' ),
			'toc'          => __( 'فهرست مطالب', 'zarincoach' ),
			'trust-badges' => __( 'نمادهای اعتماد', 'zarincoach' ),
			'resume-hero'  => __( 'رزومه — معرفی حرفه‌ای', 'zarincoach' ),
			'timeline'     => __( 'خط زمانی سوابق', 'zarincoach' ),
			'courses'      => __( 'دوره‌ها و گواهی‌ها', 'zarincoach' ),
			'skills'       => __( 'حوزه‌های تخصصی', 'zarincoach' ),
			'book'         => __( 'معرفی کتاب', 'zarincoach' ),
		);
		if ( class_exists( 'WooCommerce' ) ) {
			$widgets += array(
				'products'          => __( 'فروشگاه — محصولات', 'zarincoach' ),
				'product-cats'      => __( 'فروشگاه — دسته‌بندی محصولات', 'zarincoach' ),
				'product-spotlight' => __( 'فروشگاه — محصول ویژه', 'zarincoach' ),
				'shop-promo'        => __( 'فروشگاه — بنر تخفیف', 'zarincoach' ),
				'shop-benefits'     => __( 'فروشگاه — مزایای خرید', 'zarincoach' ),
			);
		}

		return array(
			zc_panel_section(
				'elementor',
				__( 'المنتور', 'zarincoach' ),
				'fa-solid fa-layer-group',
				__( 'ویجت‌های اختصاصی زرین‌کوچ در دسته‌ی «زرین‌کوچ» پنل المنتور.', 'zarincoach' ),
				array(
					zc_f(
						'checkbox',
						'elementor_widgets',
						__( 'ویجت‌های فعال', 'zarincoach' ),
						array(
							'subtitle' => __( 'ویجت‌های خاموش از فهرست ویرایشگر المنتور پنهان می‌شوند تا پنل خلوت‌تر شود؛ صفحاتی که قبلاً از آن‌ها استفاده کرده‌اند بدون تغییر نمایش داده می‌شوند.', 'zarincoach' ),
							'options'  => $widgets,
							'default'  => array_fill_keys( array_keys( $widgets ), '1' ),
							'class'    => 'zc-grid-checks',
							'desc'     => sprintf(
								/* translators: %s: پیوند صفحه‌ی مدیریت ویجت‌ها */
								__( 'برای جستجو، مشاهده‌ی صفحه‌های استفاده‌کننده و روشن/خاموش کردن گروهی، به %s بروید.', 'zarincoach' ),
								'<a href="' . esc_url( admin_url( 'admin.php?page=zc-widgets' ) ) . '">' . esc_html__( 'مدیریت ویجت‌ها', 'zarincoach' ) . '</a>'
							),
						)
					),
					zc_f_switch( 'elementor_editor_css', __( 'استایل قالب در ویرایشگر', 'zarincoach' ), true, __( 'برای نمایش دقیق ویجت‌ها هنگام ویرایش.', 'zarincoach' ) ),
					zc_f_switch( 'elementor_defaults', __( 'مقادیر پیش‌فرض از پنل', 'zarincoach' ), true, __( 'فیلدهای خالی ویجت‌ها (تلفن، لوگو، پیوند رزرو و…) از همین پنل خوانده می‌شوند.', 'zarincoach' ) ),
				)
			),
		);
	}
endif;

/* =====================================================================
 * ۱۸. کدهای سفارشی
 * ===================================================================== */
if ( ! function_exists( 'zc_panel_code' ) ) :
	/**
	 * کدهای سفارشی.
	 *
	 * @return array
	 */
	function zc_panel_code() {
		return array(
			zc_panel_section(
				'code',
				__( 'کدهای سفارشی', 'zarincoach' ),
				'fa-solid fa-code',
				__( 'CSS و کدهای ابزارهای آمار (Analytics، Clarity و…) بدون دستکاری فایل‌های قالب.', 'zarincoach' ),
				array(
					zc_f_code( 'code_css', __( 'CSS سفارشی', 'zarincoach' ), 'css', __( 'بعد از استایل قالب بارگذاری می‌شود.', 'zarincoach' ) ),
					zc_f_code( 'code_head', __( 'کدهای head', 'zarincoach' ), 'html', __( 'پیش از بسته شدن تگ head؛ تگ script را هم بنویسید.', 'zarincoach' ) ),
					zc_f_code( 'code_body', __( 'کدهای ابتدای body', 'zarincoach' ), 'html', __( 'بلافاصله پس از باز شدن تگ body (مثل noscript گوگل تگ منیجر).', 'zarincoach' ) ),
					zc_f_code( 'code_footer', __( 'کدهای انتهای صفحه', 'zarincoach' ), 'html', __( 'پیش از بسته شدن تگ body.', 'zarincoach' ) ),
				)
			),
		);
	}
endif;
