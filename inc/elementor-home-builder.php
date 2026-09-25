<?php
/**
 * ساخت خودکار صفحه اصلی با المنتور (بر اساس تنظیمات پنل)
 *
 * این فایل تنها زمانی اجرا می‌شود که افزونه المنتور فعال باشد.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_elementor_id' ) ) :
	/**
	 * تولید شناسه یکتا برای عناصر المنتور.
	 *
	 * @return string
	 */
	function zc_elementor_id() {
		return substr( md5( wp_rand() . microtime() . uniqid( '', true ) ), 0, 8 );
	}
endif;

if ( ! function_exists( 'zc_elementor_widget' ) ) :
	/**
	 * ساخت یک عنصر ویجت.
	 *
	 * @param string $widget   نام ویجت (بدون پیشوند zc-).
	 * @param array  $settings تنظیمات.
	 * @return array<string, mixed>
	 */
	function zc_elementor_widget( $widget, $settings = array() ) {
		return array(
			'id'         => zc_elementor_id(),
			'elType'     => 'widget',
			'widgetType' => 'zc-' . $widget,
			'settings'   => $settings,
			'elements'   => array(),
		);
	}
endif;

if ( ! function_exists( 'zc_elementor_section' ) ) :
	/**
	 * ساخت یک بخش تک‌ستونه شامل یک ویجت.
	 *
	 * @param array $widget عنصر ویجت.
	 * @param array $section_settings تنظیمات بخش.
	 * @return array<string, mixed>
	 */
	function zc_elementor_section( $widget, $section_settings = array() ) {
		$column = array(
			'id'       => zc_elementor_id(),
			'elType'   => 'column',
			'settings' => array( '_column_size' => 100, '_inline_size' => 100 ),
			'elements' => array( $widget ),
		);

		return array(
			'id'       => zc_elementor_id(),
			'elType'   => 'section',
			'settings' => array_merge(
				array(
					'layout'         => 'full_width',
					'gap'            => 'no',
					'padding'        => array( 'unit' => 'px', 'top' => '0', 'right' => '0', 'bottom' => '0', 'left' => '0', 'isLinked' => true ),
				),
				$section_settings
			),
			'elements' => array( $column ),
		);
	}
endif;

if ( ! function_exists( 'zc_build_elementor_home' ) ) :
	/**
	 * ساخت داده‌های المنتور برای صفحه اصلی.
	 *
	 * @param int $page_id شناسه صفحه.
	 * @return bool
	 */
	function zc_build_elementor_home( $page_id ) {
		if ( ! $page_id || ! zc_is_elementor_active() ) {
			return false;
		}

		$data = array();

		/* ---------------------- ۱. سربرگ اصلی ---------------------- */
		$stats = array();
		foreach ( array( 1, 2, 3 ) as $i ) {
			$num   = (string) zc_opt( 'home_hero_stat' . $i . '_num', '' );
			$label = (string) zc_opt( 'home_hero_stat' . $i . '_label', '' );

			if ( '' === $num ) {
				continue;
			}

			$stats[] = array(
				'_id'         => zc_elementor_id(),
				'stat_number' => $num,
				'stat_label'  => $label,
			);
		}

		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'hero',
				array(
					'style'                    => (string) zc_opt( 'home_hero_style', 'split' ),
					'badge'                    => (string) zc_opt( 'home_hero_badge', '' ),
					'title'                    => (string) zc_opt( 'home_hero_title', '' ),
					'title_tag'                => 'h1',
					'title_accent'             => (string) zc_opt( 'home_hero_title_accent', '' ),
					'subtitle'                 => (string) zc_opt( 'home_hero_subtitle', '' ),
					'image'                    => (array) zc_opt( 'home_hero_image', array() ),
					'primary_button_text'      => (string) zc_opt( 'home_hero_primary_text', '' ),
					'primary_button_url'       => array( 'url' => (string) zc_opt( 'home_hero_primary_url', '#booking' ), 'is_external' => '', 'nofollow' => '' ),
					'secondary_button_text'    => (string) zc_opt( 'home_hero_secondary_text', '' ),
					'secondary_button_url'     => array( 'url' => (string) zc_opt( 'home_hero_secondary_url', '#services' ), 'is_external' => '', 'nofollow' => '' ),
					'show_stats'               => ! empty( $stats ) ? 'yes' : '',
					'stats'                    => $stats,
					'show_blobs'               => 'yes',
					'show_grain'               => 'yes',
					'zc_tone'                  => zc_is_inverse_section( 'hero' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'hero' )
		);

		/* ---------------------- ۲. نوار کلمات ---------------------- */
		$words = array_values( array_filter( (array) zc_opt( 'home_marquee_words', array() ) ) );
		if ( ! empty( $words ) ) {
			$word_items = array();
			foreach ( $words as $word ) {
				$word_items[] = array(
					'_id'  => zc_elementor_id(),
					'word' => (string) $word,
				);
			}

			$data[] = zc_elementor_section(
				zc_elementor_widget(
					'marquee',
					array(
						'words'     => $word_items,
						'separator' => '✳',
						'zc_tone'   => zc_is_inverse_section( 'marquee' ) ? 'inverse' : '',
					)
				),
				array( 'gap' => 'no' )
			);
		}

		/* ---------------------- ۳. درباره من ---------------------- */
		$features = array_values( array_filter( (array) zc_opt( 'home_about_features', array() ) ) );
		$feature_items = array();
		foreach ( $features as $feature ) {
			$feature_items[] = array(
				'_id'     => zc_elementor_id(),
				'feature' => (string) $feature,
			);
		}

		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'about',
				array(
					'eyebrow'       => (string) zc_opt( 'home_about_eyebrow', '' ),
					'title'         => (string) zc_opt( 'home_about_title', '' ),
					'subtitle'      => '',
					'align'         => 'right',
					'content'       => (string) zc_opt( 'home_about_content', '' ),
					'image'         => (array) zc_opt( 'home_about_image', array() ),
					'image_position' => 'right',
					'badge_number'  => (string) zc_opt( 'home_about_badge_num', '' ),
					'badge_label'   => (string) zc_opt( 'home_about_badge_label', '' ),
					'features'      => $feature_items,
					'button_text'   => (string) zc_opt( 'home_about_button_text', '' ),
					'button_url'    => array( 'url' => (string) zc_opt( 'home_about_button_url', '' ), 'is_external' => '', 'nofollow' => '' ),
					'zc_tone'       => zc_is_inverse_section( 'about' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'about' )
		);

		/* ---------------------- ۴. خدمات ---------------------- */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'services',
				array(
					'eyebrow'    => (string) zc_opt( 'home_services_eyebrow', '' ),
					'title'      => (string) zc_opt( 'home_services_title', '' ),
					'subtitle'   => (string) zc_opt( 'home_services_subtitle', '' ),
					'align'      => 'center',
					'source'     => 'cpt',
					'count'      => (int) zc_opt( 'home_services_count', 6 ),
					'columns'    => (string) zc_opt( 'home_services_columns', '3' ),
					'show_meta'  => zc_switch( 'home_services_price', true ) ? 'yes' : '',
					'button_text' => (string) zc_opt( 'home_services_button', '' ),
					'notch'      => 'yes',
					'bg_style'   => 'soft',
					'zc_tone'    => zc_is_inverse_section( 'services' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'services' )
		);

		/* ---------------------- ۵. طرحواره‌ها ---------------------- */
		$schema_items = array();
		foreach ( (array) zc_opt( 'home_schema_items', array() ) as $item ) {
			$title = isset( $item['title'] ) ? (string) $item['title'] : '';
			$desc  = isset( $item['description'] ) ? (string) $item['description'] : '';

			if ( '' === $title ) {
				continue;
			}

			$schema_items[] = array(
				'_id'        => zc_elementor_id(),
				'item_title' => $title,
				'item_desc'  => $desc,
				'item_url'   => array( 'url' => function_exists( 'zc_sc_url_for_label' ) ? zc_sc_url_for_label( $title ) : '', 'is_external' => '', 'nofollow' => '' ),
			);
		}

		if ( ! empty( $schema_items ) ) {
			$data[] = zc_elementor_section(
				zc_elementor_widget(
					'schema',
					array(
						'eyebrow'       => (string) zc_opt( 'home_schema_eyebrow', '' ),
						'title'         => (string) zc_opt( 'home_schema_title', '' ),
						'subtitle'      => (string) zc_opt( 'home_schema_subtitle', '' ),
						'align'         => 'center',
						'items'         => $schema_items,
						'columns'       => '3',
						'show_numbers'  => 'yes',
						'show_panel'    => 'yes',
						'panel_button_text' => __( 'شروع ارزیابی', 'zarincoach' ),
						'panel_button_url'  => array( 'url' => (string) zc_opt( 'home_cta_primary_url', '#booking' ), 'is_external' => '', 'nofollow' => '' ),
						'more_text'         => post_type_exists( 'zc_schema' ) ? __( 'کتابخانه‌ی کامل: ۱۸ طرحواره، ۱۴ ذهنیت و ۱۲ خطای شناختی', 'zarincoach' ) : '',
						'more_url'          => array( 'url' => function_exists( 'zc_sc_hub_url' ) ? zc_sc_hub_url() : '', 'is_external' => '', 'nofollow' => '' ),
						'zc_tone'           => zc_is_inverse_section( 'schema' ) ? 'inverse' : '',
					)
				),
				array( '_element_id' => 'schema' )
			);
		}

		/* ---------------------- ۶. مسیر همراهی ---------------------- */
		$process_items = array();
		foreach ( (array) zc_opt( 'home_process_steps', array() ) as $step ) {
			$title = isset( $step['title'] ) ? (string) $step['title'] : '';
			$desc  = isset( $step['description'] ) ? (string) $step['description'] : '';
			$url   = isset( $step['url'] ) ? (string) $step['url'] : '';

			if ( '' === $title ) {
				continue;
			}

			$process_items[] = array(
				'_id'        => zc_elementor_id(),
				'step_title' => $title,
				'step_desc'  => $desc,
				'step_url'   => array( 'url' => $url, 'is_external' => '', 'nofollow' => '' ),
			);
		}

		if ( ! empty( $process_items ) ) {
			$data[] = zc_elementor_section(
				zc_elementor_widget(
					'process',
					array(
						'eyebrow'         => (string) zc_opt( 'home_process_eyebrow', '' ),
						'title'           => (string) zc_opt( 'home_process_title', '' ),
						'align'           => 'center',
						'steps'           => $process_items,
						'persian_numbers' => zc_switch( 'typo_persian_digits', true ) ? 'yes' : '',
						'zc_tone'         => zc_is_inverse_section( 'process' ) ? 'inverse' : '',
					)
				),
				array( '_element_id' => 'process' )
			);
		}

		/* ---------------------- ۶-ب. تست‌ها، دوره‌ها و ذهن مدیر ---------------------- */
		if ( function_exists( 'zc_eb_manual_services' ) ) {
			$data[] = zc_eb_manual_services(
				array(
					'eyebrow'  => __( 'راه‌های دیگر شروع', 'zarincoach' ),
					'title'    => __( 'تست، دوره گروهی یا برنامه‌ی مدیران', 'zarincoach' ),
					'subtitle' => __( 'اگر هنوز برای جلسه فردی آماده نیستی، از یکی از این مسیرها شروع کن.', 'zarincoach' ),
					'bg_style' => 'soft',
				),
				array(
					array( 'icon' => 'clipboard', 'title' => __( 'تست‌ها و ارزیابی‌ها', 'zarincoach' ), 'desc' => __( 'کمال‌گرایی، اهمالکاری، طرحواره‌ها و سبک تصمیم‌گیری؛ نقطه‌ی شروع، نه تشخیص.', 'zarincoach' ), 'duration' => __( '۵ تا ۲۰ دقیقه', 'zarincoach' ), 'badge' => __( 'به‌زودی آنلاین', 'zarincoach' ), 'url' => zc_eb_url( 'assessments' ) ),
					array( 'icon' => 'graduation', 'title' => __( 'دوره‌ها و کارگاه‌ها', 'zarincoach' ), 'desc' => __( 'دوره حضوری ده مهارت زندگی در بوشهر و کارگاه‌های «از کمال‌گرایی تا اقدام» و «ذهن مرتب».', 'zarincoach' ), 'duration' => __( '۸ جلسه ۹۰ دقیقه‌ای', 'zarincoach' ), 'badge' => '', 'url' => zc_eb_url( 'courses' ) ),
					array( 'icon' => 'briefcase', 'title' => __( 'ویژه مدیران؛ ذهن مدیر', 'zarincoach' ), 'desc' => __( 'برای مدیران ۲۸ تا ۴۵ ساله: کنترل‌گری، تفویض، تصمیم‌های معوق و فرسودگی خاموش.', 'zarincoach' ), 'duration' => __( 'حضوری یا آنلاین', 'zarincoach' ), 'badge' => __( 'جدید', 'zarincoach' ), 'url' => zc_eb_url( 'managers' ) ),
				),
				'paths'
			);
		}

		/* ---------------------- ۷. آمار ---------------------- */
		$stat_items = array();
		foreach ( (array) zc_opt( 'home_stats_items', array() ) as $item ) {
			$num   = isset( $item['title'] ) ? (string) $item['title'] : '';
			$label = isset( $item['description'] ) ? (string) $item['description'] : '';

			if ( '' === $num ) {
				continue;
			}

			preg_match( '/^(\d+)(.*)$/u', zc_digits_to_latin( $num ), $matches );

			$stat_items[] = array(
				'_id'         => zc_elementor_id(),
				'stat_number' => isset( $matches[1] ) ? $matches[1] : $num,
				'stat_suffix' => isset( $matches[2] ) ? $matches[2] : '',
				'stat_label'  => $label,
			);
		}

		if ( ! empty( $stat_items ) ) {
			$data[] = zc_elementor_section(
				zc_elementor_widget(
					'stats',
					array(
						'title'          => (string) zc_opt( 'home_stats_title', '' ),
						'items'          => $stat_items,
						'columns'        => '4',
						'persian_digits' => zc_switch( 'typo_persian_digits', true ) ? 'yes' : '',
						'dark_panel'     => 'yes',
						'zc_tone'        => zc_is_inverse_section( 'stats' ) ? 'inverse' : '',
					)
				)
			);
		}

		/* ---------------------- ۸. نظرات ---------------------- */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'testimonials',
				array(
					'eyebrow'     => (string) zc_opt( 'home_testimonials_eyebrow', '' ),
					'title'       => (string) zc_opt( 'home_testimonials_title', '' ),
					'align'       => 'center',
					'source'      => 'cpt',
					'count'       => (int) zc_opt( 'home_testimonials_count', 6 ),
					'style'       => (string) zc_opt( 'home_testimonials_style', 'slider' ),
					'columns'     => '3',
					'per_view'    => '3',
					'autoplay'    => 'yes',
					'interval'    => 6,
					'show_rating' => 'yes',
					'zc_tone'     => zc_is_inverse_section( 'testimonials' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'testimonials' )
		);

		/* ---------------------- ۹. پرسش‌ها ---------------------- */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'faq',
				array(
					'eyebrow'       => (string) zc_opt( 'home_faq_eyebrow', '' ),
					'title'         => (string) zc_opt( 'home_faq_title', '' ),
					'align'         => 'right',
					'source'        => 'cpt',
					'count'         => (int) zc_opt( 'home_faq_count', 6 ),
					'layout'        => 'side',
					'first_open'    => 'yes',
					'enable_schema' => zc_switch( 'home_faq_schema', true ) ? 'yes' : '',
					'zc_tone'       => zc_is_inverse_section( 'faq' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'faq' )
		);

		/* ---------------------- ۹-ب. فروشگاه (با ووکامرس) ---------------------- */
		if ( function_exists( 'zc_demo_shop_home_sections' ) ) {
			foreach ( zc_demo_shop_home_sections() as $zc_shop_section ) {
				$data[] = $zc_shop_section;
			}
		}

		/* ---------------------- ۱۰. نوشته‌ها ---------------------- */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'posts',
				array(
					'eyebrow'          => (string) zc_opt( 'home_blog_eyebrow', '' ),
					'title'            => (string) zc_opt( 'home_blog_title', '' ),
					'align'            => 'right',
					'count'            => (int) zc_opt( 'home_blog_count', 3 ),
					'columns'          => '3',
					'layout'           => 'vertical',
					'excerpt'          => (int) zc_opt( 'archive_excerpt', 22 ),
					'show_meta'        => 'yes',
					'more_button_text' => (string) zc_opt( 'home_blog_button', '' ),
					'more_button_url'  => array( 'url' => zc_blog_url(), 'is_external' => '', 'nofollow' => '' ),
					'more_button_style' => 'outline',
					'zc_tone'          => zc_is_inverse_section( 'blog' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'blog' )
		);

		/* ---------------------- ۱۱. فراخوان اقدام ---------------------- */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'cta',
				array(
					'badge'                 => __( 'قدم اول', 'zarincoach' ),
					'title'                 => (string) zc_opt( 'home_cta_title', '' ),
					'text'                  => (string) zc_opt( 'home_cta_text', '' ),
					'note'                  => (string) zc_opt( 'home_cta_note', '' ),
					'primary_button_text'   => (string) zc_opt( 'home_cta_primary_text', '' ),
					'primary_button_url'    => array( 'url' => (string) zc_opt( 'home_cta_primary_url', '#contact' ), 'is_external' => '', 'nofollow' => '' ),
					'secondary_button_text' => (string) zc_opt( 'home_cta_secondary_text', '' ),
					'secondary_button_url'  => array( 'url' => (string) ( function_exists( 'zc_social_url' ) ? zc_social_url( 'telegram' ) : zc_opt( 'social_telegram', '' ) ), 'is_external' => 'on', 'nofollow' => '' ),
					'secondary_icon'        => 'send',
					'show_form'             => zc_switch( 'contact_form_enable', true ) ? 'yes' : '',
					'form_shortcode'        => (string) zc_opt( 'contact_form_shortcode', '' ),
					'show_blobs'            => 'yes',
					'dark_style'            => 'inverse' === (string) zc_opt( 'palette_cta_tone', 'inverse' ) ? 'yes' : '',
					'zc_tone'               => zc_is_inverse_section( 'cta' ) ? 'inverse' : '',
				)
			),
			array( '_element_id' => 'booking' )
		);

		/**
		 * فیلتر داده‌های صفحه اصلی المنتور پیش از ذخیره.
		 *
		 * @param array $data داده‌ها.
		 * @param int   $page_id شناسه صفحه.
		 */
		$data = (array) apply_filters( 'zc_elementor_home_data', $data, $page_id );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_elementor_save' ) ) :
	/**
	 * ذخیره‌ی استاندارد داده‌های المنتور برای یک برگه.
	 *
	 * @param int   $page_id شناسه برگه.
	 * @param array  $data    ساختار المنتور.
	 * @param string $type    نوع سند المنتور (wp-page، section، …).
	 * @return bool
	 */
	function zc_elementor_save( $page_id, $data, $type = 'wp-page' ) {
		$json = wp_json_encode( array_values( $data ), JSON_UNESCAPED_UNICODE );

		if ( ! $json ) {
			return false;
		}

		update_post_meta( $page_id, '_elementor_data', wp_slash( $json ) );
		update_post_meta( $page_id, '_elementor_edit_mode', 'builder' );
		update_post_meta( $page_id, '_elementor_template_type', $type );
		update_post_meta( $page_id, '_elementor_version', defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '3.20.0' );
		delete_post_meta( $page_id, '_elementor_css' );
		delete_post_meta( $page_id, '_elementor_element_cache' );

		if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
			\Elementor\Plugin::$instance->files_manager->clear_cache();
		}

		return true;
	}
endif;

if ( ! function_exists( 'zc_elementor_row' ) ) :
	/**
	 * ساخت یک بخش جعبه‌ای چندستونه (برای ویجت‌های بدون قاب مانند فرم، عنوان و فهرست).
	 *
	 * @param array $columns  ستون‌ها: array( array( 'size' => 50, 'widgets' => array(...) ) ).
	 * @param array $settings تنظیمات بخش.
	 * @return array<string, mixed>
	 */
	function zc_elementor_row( $columns, $settings = array() ) {
		$elements = array();
		foreach ( $columns as $column ) {
			$elements[] = array(
				'id'       => zc_elementor_id(),
				'elType'   => 'column',
				'settings' => array(
					'_column_size'     => (int) $column['size'],
					'_inline_size'     => (float) $column['size'],
					'content_position' => isset( $column['align'] ) ? $column['align'] : 'center',
				),
				'elements' => $column['widgets'],
			);
		}

		return array(
			'id'       => zc_elementor_id(),
			'elType'   => 'section',
			'settings' => array_merge(
				array(
					'layout'         => 'boxed',
					// هم‌تراز با .zc-container (عرض محتوای ۱۱۲۰ + فاصله ستون‌ها ۲×۱۵).
					'content_width'  => array( 'unit' => 'px', 'size' => 1150, 'sizes' => array() ),
					'gap'            => 'extended',
					'padding'        => array( 'unit' => 'px', 'top' => '56', 'right' => '25', 'bottom' => '56', 'left' => '25', 'isLinked' => false ),
					'padding_tablet' => array( 'unit' => 'px', 'top' => '48', 'right' => '13', 'bottom' => '48', 'left' => '13', 'isLinked' => false ),
					'padding_mobile' => array( 'unit' => 'px', 'top' => '40', 'right' => '5', 'bottom' => '40', 'left' => '5', 'isLinked' => false ),
				),
				$settings
			),
			'elements' => $elements,
		);
	}
endif;

if ( ! function_exists( 'zc_elementor_repeater' ) ) :
	/**
	 * افزودن شناسه‌ی یکتا به آیتم‌های تکرارشونده.
	 *
	 * @param array $items آیتم‌ها.
	 * @return array
	 */
	function zc_elementor_repeater( $items ) {
		$out = array();
		foreach ( $items as $item ) {
			$out[] = array_merge( array( '_id' => zc_elementor_id() ), $item );
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_elementor_url' ) ) :
	/**
	 * مقدار کنترل لینک المنتور.
	 *
	 * @param string $url نشانی.
	 * @return array<string,string>
	 */
	function zc_elementor_url( $url ) {
		return array( 'url' => (string) $url, 'is_external' => '', 'nofollow' => '' );
	}
endif;

if ( ! function_exists( 'zc_elementor_common_blocks' ) ) :
	/**
	 * بلوک‌های پرتکرار صفحات داخلی.
	 *
	 * @param string $block نام بلوک.
	 * @param array  $over  بازنویسی تنظیمات.
	 * @return array<string, mixed>
	 */
	function zc_elementor_common_blocks( $block, $over = array() ) {
		$booking = function_exists( 'zc_page_url_by_key' ) ? zc_page_url_by_key( 'booking' ) : home_url( '/booking/' );

		switch ( $block ) {
			case 'stats':
				$items = array();
				foreach ( (array) zc_opt( 'home_stats_items', array() ) as $item ) {
					$num = isset( $item['title'] ) ? zc_digits_to_latin( (string) $item['title'] ) : '';
					if ( '' === $num ) {
						continue;
					}
					preg_match( '/^(\d+)(.*)$/u', $num, $m );
					$items[] = array(
						'stat_number' => isset( $m[1] ) ? $m[1] : $num,
						'stat_suffix' => isset( $m[2] ) ? $m[2] : '',
						'stat_label'  => isset( $item['description'] ) ? (string) $item['description'] : '',
					);
				}
				return zc_elementor_section(
					zc_elementor_widget(
						'stats',
						array_merge(
							array(
								'title'          => (string) zc_opt( 'home_stats_title', '' ),
								'items'          => zc_elementor_repeater( $items ),
								'columns'        => '4',
								'persian_digits' => 'yes',
								'dark_panel'     => 'yes',
							),
							$over
						)
					)
				);

			case 'process':
				$steps = array();
				foreach ( (array) zc_opt( 'home_process_steps', array() ) as $step ) {
					if ( empty( $step['title'] ) ) {
						continue;
					}
					$steps[] = array(
						'step_title' => (string) $step['title'],
						'step_desc'  => isset( $step['description'] ) ? (string) $step['description'] : '',
						'step_url'   => zc_elementor_url( isset( $step['url'] ) && '#booking' !== $step['url'] ? (string) $step['url'] : '' ),
					);
				}
				return zc_elementor_section(
					zc_elementor_widget(
						'process',
						array_merge(
							array(
								'eyebrow'         => (string) zc_opt( 'home_process_eyebrow', '' ),
								'title'           => (string) zc_opt( 'home_process_title', '' ),
								'align'           => 'center',
								'steps'           => zc_elementor_repeater( $steps ),
								'persian_numbers' => 'yes',
							),
							$over
						)
					)
				);

			case 'testimonials':
				return zc_elementor_section(
					zc_elementor_widget(
						'testimonials',
						array_merge(
							array(
								'eyebrow'     => (string) zc_opt( 'home_testimonials_eyebrow', '' ),
								'title'       => (string) zc_opt( 'home_testimonials_title', '' ),
								'align'       => 'center',
								'source'      => 'cpt',
								'count'       => 6,
								'style'       => 'slider',
								'columns'     => '3',
								'per_view'    => '3',
								'autoplay'    => 'yes',
								'interval'    => 6,
								'show_rating' => 'yes',
								'zc_tone'     => 'inverse',
							),
							$over
						)
					)
				);

			case 'faq':
				return zc_elementor_section(
					zc_elementor_widget(
						'faq',
						array_merge(
							array(
								'eyebrow'       => (string) zc_opt( 'home_faq_eyebrow', '' ),
								'title'         => (string) zc_opt( 'home_faq_title', '' ),
								'align'         => 'right',
								'source'        => 'cpt',
								'count'         => 6,
								'layout'        => 'side',
								'first_open'    => 'yes',
								'enable_schema' => 'yes',
							),
							$over
						)
					)
				);

			case 'cta':
			default:
				return zc_elementor_section(
					zc_elementor_widget(
						'cta',
						array_merge(
							array(
								'badge'                 => __( 'قدم اول', 'zarincoach' ),
								'title'                 => (string) zc_opt( 'home_cta_title', '' ),
								'text'                  => (string) zc_opt( 'home_cta_text', '' ),
								'note'                  => (string) zc_opt( 'home_cta_note', '' ),
								'primary_button_text'   => (string) zc_opt( 'home_cta_primary_text', '' ),
								'primary_button_url'    => zc_elementor_url( $booking ),
								'secondary_button_text' => (string) zc_opt( 'home_cta_secondary_text', '' ),
								'secondary_button_url'  => array( 'url' => (string) ( function_exists( 'zc_social_url' ) ? zc_social_url( 'telegram' ) : zc_opt( 'social_telegram', '' ) ), 'is_external' => 'on', 'nofollow' => '' ),
								'secondary_icon'        => 'send',
								'show_form'             => 'yes',
								'show_blobs'            => 'yes',
								'dark_style'            => 'inverse' === (string) zc_opt( 'palette_cta_tone', 'inverse' ) ? 'yes' : '',
							),
							$over
						)
					),
					array( '_element_id' => 'booking' )
				);
		}
	}
endif;

if ( ! function_exists( 'zc_eb_url' ) ) :
	/**
	 * لینک المنتور به یک برگه‌ی دمو بر اساس کلید.
	 *
	 * @param string $key    کلید برگه.
	 * @param string $anchor لنگر اختیاری (#...).
	 * @return array<string,string>
	 */
	function zc_eb_url( $key, $anchor = '' ) {
		return zc_elementor_url( zc_page_url_by_key( $key ) . $anchor );
	}
endif;

if ( ! function_exists( 'zc_eb_img' ) ) :
	/**
	 * مقدار کنترل تصویر المنتور از تصاویر دمو.
	 *
	 * @param string $key کلید تصویر دمو.
	 * @return array<string,string>
	 */
	function zc_eb_img( $key ) {
		static $media = null;
		if ( null === $media ) {
			$media = function_exists( 'zc_demo_media_ids' ) ? zc_demo_media_ids() : array();
		}
		return ! empty( $media[ $key ] ) && function_exists( 'zc_demo_media_field' ) ? zc_demo_media_field( (int) $media[ $key ] ) : array( 'url' => '' );
	}
endif;

if ( ! function_exists( 'zc_eb_heading' ) ) :
	/**
	 * ویجت عنوان بخش.
	 *
	 * @param string $eyebrow  روتیتر.
	 * @param string $title    عنوان.
	 * @param string $accent   واژه‌ی برجسته.
	 * @param string $subtitle توضیح.
	 * @return array<string,mixed>
	 */
	function zc_eb_heading( $eyebrow, $title, $accent = '', $subtitle = '' ) {
		return zc_elementor_widget(
			'heading',
			array(
				'eyebrow'     => $eyebrow,
				'title'       => $title,
				'accent_word' => $accent,
				'subtitle'    => $subtitle,
				'align'       => 'right',
				'html_tag'    => 'h2',
			)
		);
	}
endif;

if ( ! function_exists( 'zc_eb_list' ) ) :
	/**
	 * ویجت فهرست.
	 *
	 * @param array  $items   آیتم‌ها: array( متن, آیکن ).
	 * @param string $style   check|dash|cards.
	 * @param string $columns ستون‌ها.
	 * @return array<string,mixed>
	 */
	function zc_eb_list( $items, $style = 'check', $columns = '1' ) {
		$rows = array();
		foreach ( $items as $item ) {
			$rows[] = array(
				'item_text' => $item[0],
				'item_icon' => isset( $item[1] ) ? $item[1] : '',
			);
		}
		return zc_elementor_widget(
			'list',
			array(
				'style'   => $style,
				'columns' => $columns,
				'items'   => zc_elementor_repeater( $rows ),
			)
		);
	}
endif;

if ( ! function_exists( 'zc_eb_form' ) ) :
	/**
	 * ویجت فرم تماس داخلی.
	 *
	 * @param string $title    عنوان.
	 * @param string $subtitle توضیح.
	 * @param string $button   متن دکمه.
	 * @param bool   $subject  نمایش موضوع.
	 * @return array<string,mixed>
	 */
	function zc_eb_form( $title, $subtitle, $button, $subject = true ) {
		return zc_elementor_widget(
			'form',
			array(
				'form_source'   => 'internal',
				'form_title'    => $title,
				'form_subtitle' => $subtitle,
				'button_text'   => $button,
				'show_subject'  => $subject ? '1' : '',
				'boxed'         => 'yes',
			)
		);
	}
endif;

if ( ! function_exists( 'zc_eb_manual_services' ) ) :
	/**
	 * بخش کارت‌های دستی (ویجت خدمات در حالت دستی).
	 *
	 * @param array $head  eyebrow/title/subtitle/columns/button_text/zc_tone/bg_style.
	 * @param array $items آیتم‌ها: icon, title, desc, price, duration, badge, url.
	 * @param string $id   شناسه‌ی لنگر.
	 * @return array<string,mixed>
	 */
	function zc_eb_manual_services( $head, $items, $id = '' ) {
		$rows = array();
		foreach ( $items as $item ) {
			$rows[] = array(
				'item_icon'     => isset( $item['icon'] ) ? $item['icon'] : 'sparkles',
				'item_title'    => $item['title'],
				'item_desc'     => isset( $item['desc'] ) ? $item['desc'] : '',
				'item_price'    => isset( $item['price'] ) ? $item['price'] : '',
				'item_duration' => isset( $item['duration'] ) ? $item['duration'] : '',
				'item_badge'    => isset( $item['badge'] ) ? $item['badge'] : '',
				'item_url'      => isset( $item['url'] ) ? $item['url'] : zc_elementor_url( '' ),
			);
		}
		return zc_elementor_section(
			zc_elementor_widget(
				'services',
				array_merge(
					array(
						'align'     => 'center',
						'source'    => 'manual',
						'columns'   => '3',
						'show_meta' => 'yes',
						'notch'     => 'yes',
						'bg_style'  => 'none',
						'items'     => zc_elementor_repeater( $rows ),
					),
					$head
				)
			),
			'' !== $id ? array( '_element_id' => $id ) : array()
		);
	}
endif;

if ( ! function_exists( 'zc_eb_notice' ) ) :
	/**
	 * بخش اطلاعیه/متن کوتاه (ویجت متن و محتوا).
	 *
	 * @param string $notice  متن اطلاعیه.
	 * @param string $tone    info|alert.
	 * @param string $content محتوای اختیاری.
	 * @return array<string,mixed>
	 */
	function zc_eb_notice( $notice, $tone = 'info', $content = '' ) {
		return zc_elementor_section(
			zc_elementor_widget(
				'text',
				array(
					'content'     => $content,
					'width'       => 'readable',
					'boxed'       => '' !== $content ? 'yes' : '',
					'updated'     => '',
					'notice'      => $notice,
					'notice_tone' => $tone,
					'toc'         => '',
				)
			)
		);
	}
endif;

if ( ! function_exists( 'zc_build_elementor_about' ) ) :
	/**
	 * برگه «درباره من» با المنتور.
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_about( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title(
			array(
				'eyebrow'     => 'درباره من',
				'title'       => 'مریم جمالی؛ روان‌شناس الگوهای ذهنی و رفتاری',
				'accent_word' => 'مریم جمالی',
			)
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'about',
				array(
					'eyebrow'        => 'داستان من',
					'title'          => 'از کمال‌گرای مضطربِ دیروز، تا همراهِ مسیر رشدِ امروز',
					'align'          => 'right',
					'content'        => "من <strong>مریم جمالی</strong> هستم؛ ۲۸ ساله، اهل <strong>بوشهر</strong> و <strong>کارشناس ارشد روان‌شناسی بالینی</strong>. خودم سال‌ها یک کمال‌گرای مضطرب بودم؛ دو سال پشت کنکور ماندم تا «بهترین» انتخاب را داشته باشم و همان تجربه مرا به روان‌شناسی رساند. فهمیدم مشکل، استعداد یا تلاش نبود؛ الگویی بود که نمی‌شناختمش.\n\nامروز کارم کمک به جوان‌ها و مدیرانی است که می‌دانند چه باید بکنند، اما در چرخه‌ی کمال‌گرایی، اهمالکاری یا بلاتکلیفی گیر افتاده‌اند. با تکیه بر <strong>شناخت طرحواره‌ها</strong>، <strong>ده مهارت زندگی سازمان جهانی بهداشت</strong> و تمرین‌های رفتاری، از <strong>شناخت الگو تا تصمیم و اقدام</strong> همراهت هستم.",
					'image'          => zc_eb_img( 'maryam-portrait' ),
					'image_position' => 'right',
					'badge_number'   => '۶',
					'badge_label'    => 'محیط تجربه میدانی',
					'features'       => zc_elementor_repeater(
						array(
							array( 'feature' => 'کارشناس ارشد روان‌شناسی بالینی؛ پروانه اشتغال ۲۸۸۵۸ و کد نظام ۷۰۷۶۳' ),
							array( 'feature' => 'نویسنده کتاب «راهنمای تشخیص و درمان اختلالات شخصیت»' ),
							array( 'feature' => 'رویکرد طرحواره‌محور در قالب رشد فردی و آموزشی' ),
							array( 'feature' => 'مدرس ده مهارت زندگی بر پایه‌ی الگوی WHO' ),
							array( 'feature' => 'جلسات حضوری در بوشهر و آنلاین در سراسر ایران' ),
						)
					),
					'button_text'    => 'رزومه و سوابق کامل',
					'button_url'     => zc_eb_url( 'resume' ),
				)
			),
			array( '_element_id' => 'story' )
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'process',
				array(
					'eyebrow'         => 'مسیر حرفه‌ای',
					'title'           => 'شش محیط، شش زاویه برای شناخت آدم‌ها',
					'subtitle'        => 'تجربه‌ی میدانی من از کلاس درس تا خط تلفن بحران شکل گرفته است؛ هر محیط یک لایه از الگوهای ذهنی و رفتاری را نشانم داد.',
					'align'           => 'center',
					'persian_numbers' => 'yes',
					'zc_tone'         => 'inverse',
					'steps'           => zc_elementor_repeater(
						array(
							array( 'step_title' => 'دبیر و مشاور تحصیلی', 'step_desc' => 'مدارس غیرانتفاعی نرگس و سروش ایران (متوسطه اول و دوم)؛ جایی که ریشه‌ی کمال‌گرایی و اضطراب امتحان را از نزدیک دیدم.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'بیمارستان', 'step_desc' => 'روان‌شناس بیمارستان قلب و بیمارستان شهدای خلیج فارس بوشهر؛ همراهی بیماران و خانواده‌ها در شرایط پرفشار درمان.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'کلینیک تخصصی دانا', 'step_desc' => 'از ۱۴۰۳ تا امروز؛ ارزیابی و جلسات درمان فردی ساختارمند، دقت در تشخیص و کار تیمی با متخصصان دیگر.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'خوابگاه‌های علوم پزشکی', 'step_desc' => 'روان‌شناس ۶ خوابگاه علوم پزشکی بوشهر؛ همراهی دانشجویانی که زیر فشار استانداردهای بالا با فرسودگی و خودانتقادی درگیر بودند.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'صدای مشاوره ۱۴۸۰', 'step_desc' => 'پاسخ‌گویی تلفنی؛ جایی که شنیدنِ دقیق و تصمیم‌گیری سریع در موقعیت‌های حساس را آموختم.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'خانه امن بهزیستی', 'step_desc' => 'از ۱۴۰۱ تا ۱۴۰۳؛ همراهی با زنانی که در شرایط دشوار، دوباره زندگی را می‌ساختند؛ درسی عمیق درباره‌ی تاب‌آوری.', 'step_url' => zc_elementor_url( '' ) ),
						)
					),
				)
			),
			array( '_element_id' => 'career' )
		);
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 42,
					'widgets' => array(
						zc_eb_heading( 'اصول کار من', 'صادقانه، علمی و در حدود صلاحیت', 'صادقانه', 'در هر جلسه سه چیز را جدی می‌گیرم: رازداری کامل، احترام بی‌قیدوشرط و حرکت به سمت هدفی که خودت انتخاب می‌کنی. و چند چیز را هرگز وعده نمی‌دهم.' ),
					),
				),
				array(
					'size'    => 58,
					'widgets' => array(
						zc_eb_list(
							array(
								array( 'رازداری کامل مطابق نظام‌نامه اخلاق حرفه‌ای', 'lock' ),
								array( 'هر جلسه هدف، جمع‌بندی و تمرین مشخص دارد', 'target' ),
								array( 'هیچ عنوان یا مدرکی بیش از واقعیت ادعا نمی‌کنم', 'shield' ),
								array( 'نتیجه‌ی تضمینی وعده نمی‌دهم؛ مسیر روشن می‌سازم', 'compass' ),
								array( 'اگر نیازت خارج از حیطه‌ی من باشد، صادقانه ارجاع می‌دهم', 'refresh' ),
								array( 'تعرفه‌ها شفاف و مطابق تعرفه مصوب سازمان نظام', 'file-text' ),
							),
							'cards',
							'2'
						),
					),
				),
			)
		);
		$data[] = zc_elementor_common_blocks( 'stats' );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'about',
				array(
					'eyebrow'        => 'حضوری و آنلاین',
					'title'          => 'در بوشهر کنارت هستم؛ در هر شهری، آنلاین',
					'align'          => 'right',
					'content'        => "جلسات حضوری در <strong>بوشهر، خیابان رئیس‌علی دلواری، ساختمان پزشکان طبیب</strong> برگزار می‌شود؛ شنبه تا چهارشنبه، ساعت ۱۶ تا ۲۰:۳۰.\n\nاگر در شهر دیگری زندگی می‌کنی، جلسات آنلاین با همان ساختار، همان تمرین‌ها و همان رازداری برگزار می‌شود. کافی است یک فضای آرام و اینترنت پایدار داشته باشی.",
					'image'          => zc_eb_img( 'maryam-online' ),
					'image_position' => 'left',
					'badge_number'   => '۴۵',
					'badge_label'    => 'دقیقه جلسه ارزیابی',
					'features'       => zc_elementor_repeater(
						array(
							array( 'feature' => 'تماس هماهنگی ۱۵ دقیقه‌ای رایگان پیش از شروع' ),
							array( 'feature' => 'پشتیبانی در تلگرام و بله در ساعات کاری' ),
						)
					),
					'button_text'    => 'راه‌های ارتباطی',
					'button_url'     => zc_eb_url( 'contact' ),
				)
			)
		);
		$data[] = zc_elementor_common_blocks( 'testimonials', array( 'style' => 'slider' ) );
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_resume' ) ) :
	/**
	 * برگه «رزومه» با المنتور (۱۰۰٪ ویجت‌های اختصاصی قالب).
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_resume( $page_id ) {
		$pco  = 'https://pcoiran.ir/';
		$data = array();

		/* ۱) معرفی حرفه‌ای */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'resume-hero',
				array(
					'zc_tone'         => 'inverse',
					'breadcrumbs'     => 'yes',
					'eyebrow'         => 'رزومه حرفه‌ای',
					'name'            => 'مریم جمالی',
					'degree'          => 'کارشناس ارشد روان‌شناسی بالینی',
					'summary'         => 'روان‌شناس بالینی و درمانگر فردی در بوشهر؛ نویسنده‌ی کتاب «راهنمای تشخیص و درمان اختلالات شخصیت»، دبیر کمیسیون پزشکی تعیین نوع و شدت معلولیت شهرستان بوشهر و مدرس مورد اعتماد بهزیستی. تجربه‌ی کار در کلینیک، بیمارستان، خانه امن، خط مشاوره ۱۴۸۰ و مدرسه، همراه با بیش از ۱۱۰۰ ساعت آموزش تخصصی در درمان فردی، کودک و نوجوان و زوج و خانواده.',
					'tags'            => "درمانگر فردی\nدرمان اختلالات خلقی\nمشاوره روابط عاطفی\nروان‌شناس تخصصی نوجوان\nمشاور رابطه والد و نوجوان\nمدرس کارگاه‌های روان‌شناسی",
					'image'           => zc_eb_img( 'maryam-portrait' ),
					'image_position'  => 'end',
					'badge_title'     => 'پروانه اشتغال ۲۸۸۵۸',
					'badge_text'      => 'سازمان نظام روان‌شناسی و مشاوره',
					'credentials'     => zc_elementor_repeater(
						array(
							array( 'cred_icon' => 'graduation', 'cred_label' => 'مدرک تحصیلی', 'cred_value' => 'کارشناس ارشد روان‌شناسی بالینی', 'cred_verified' => '', 'cred_url' => zc_elementor_url( '' ) ),
							array( 'cred_icon' => 'badge-check', 'cred_label' => 'شماره پروانه اشتغال', 'cred_value' => '۲۸۸۵۸', 'cred_verified' => 'yes', 'cred_url' => array( 'url' => $pco, 'is_external' => 'on', 'nofollow' => 'on' ) ),
							array( 'cred_icon' => 'id-card', 'cred_label' => 'کد نظام روان‌شناسی', 'cred_value' => '۷۰۷۶۳', 'cred_verified' => 'yes', 'cred_url' => array( 'url' => $pco, 'is_external' => 'on', 'nofollow' => 'on' ) ),
							array( 'cred_icon' => 'book', 'cred_label' => 'تألیف', 'cred_value' => 'راهنمای تشخیص و درمان اختلالات شخصیت', 'cred_verified' => '', 'cred_link_label' => 'معرفی کتاب', 'cred_url' => zc_elementor_url( '#book' ) ),
						)
					),
					'verify_label'    => 'استعلام',
					'stats'           => zc_elementor_repeater(
						array(
							array( 'stat_number' => '۴', 'stat_suffix' => '+', 'stat_label' => 'سال تجربه حرفه‌ای (از ۱۴۰۱)' ),
							array( 'stat_number' => '۸', 'stat_suffix' => '', 'stat_label' => 'مرکز درمانی و آموزشی' ),
							array( 'stat_number' => '۱۱۰۰', 'stat_suffix' => '+', 'stat_label' => 'ساعت آموزش تخصصی' ),
							array( 'stat_number' => '۳۰', 'stat_suffix' => '+', 'stat_label' => 'دوره و کارگاه تخصصی' ),
						)
					),
					'button_text'         => 'رزرو نوبت مشاوره',
					'button_url'          => zc_eb_url( 'booking' ),
					'button_style'        => 'primary',
					'second_button_text'  => 'مسیر مطب و تماس',
					'second_button_url'   => zc_eb_url( 'contact' ),
					'second_button_style' => 'outline',
					'print_button'        => 'yes',
					'print_label'         => 'چاپ / ذخیره PDF',
					'nav'                 => zc_elementor_repeater(
						array(
							array( 'nav_label' => 'حوزه‌های تخصصی', 'nav_target' => 'expertise', 'nav_icon' => 'brain' ),
							array( 'nav_label' => 'سمت‌ها و مسئولیت‌ها', 'nav_target' => 'roles', 'nav_icon' => 'award' ),
							array( 'nav_label' => 'کتاب', 'nav_target' => 'book', 'nav_icon' => 'book' ),
							array( 'nav_label' => 'سوابق کاری', 'nav_target' => 'experience', 'nav_icon' => 'briefcase' ),
							array( 'nav_label' => 'دوره‌های تخصصی', 'nav_target' => 'training', 'nav_icon' => 'graduation' ),
						)
					),
					'nav_sticky'          => 'yes',
					'schema'              => 'yes',
				)
			)
		);

		/* ۲) حوزه‌های تخصصی */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'skills',
				array(
					'eyebrow'  => 'حوزه‌های تخصصی',
					'title'    => 'در چه زمینه‌هایی همراهتان هستم',
					'subtitle' => 'چهار حوزه‌ی اصلی کار بالینی و آموزشی من؛ هرکدام پشتوانه‌ای از دوره‌های تخصصی و تجربه‌ی میدانی دارد.',
					'align'    => 'center',
					'columns'  => '2',
					'numbers'  => 'yes',
					'style'    => 'cards',
					'groups'   => zc_elementor_repeater(
						array(
							array( 'g_icon' => 'brain', 'g_title' => 'درمان فردی و اختلالات خلقی', 'g_desc' => 'ارزیابی بالینی و جلسات درمان فردی ساختارمند برای افسردگی، اضطراب و سایر مشکلات خلقی؛ با تکیه بر رویکرد شناختی‌رفتاری و ارجاع به‌موقع هر جا که لازم باشد.', 'g_tags' => "درمانگر فردی\nافسردگی\nاضطراب\nاختلالات خلقی\nمصاحبه بالینی و ارزیابی", 'g_url' => zc_elementor_url( '' ) ),
							array( 'g_icon' => 'heart', 'g_title' => 'مشاوره روابط عاطفی و سوگ', 'g_desc' => 'همراهی در همه‌ی مراحل رابطه؛ از انتخاب آگاهانه پیش از شروع، تا چالش‌های حین رابطه و عبور سالم از جدایی و سوگ عاطفی.', 'g_tags' => "پیش از رابطه\nحین رابطه\nپس از جدایی\nسوگ عاطفی\nآموزش پیش از ازدواج", 'g_url' => zc_elementor_url( '' ) ),
							array( 'g_icon' => 'users', 'g_title' => 'نوجوان و خانواده', 'g_desc' => 'روان‌شناس تخصصی نوجوان و مشاور رابطه‌ی والد و نوجوان؛ با پشتوانه‌ی ۵۰۰ ساعت دوره‌ی تربیت درمانگر کودک و نوجوان و آموزش خانواده‌درمانی سیستمی.', 'g_tags' => "روان‌شناس تخصصی نوجوان\nرابطه والد و نوجوان\nمشاوره تحصیلی\nخانواده‌درمانی سیستمی", 'g_url' => zc_elementor_url( '' ) ),
							array( 'g_icon' => 'graduation', 'g_title' => 'آموزش و کارگاه‌های روان‌شناسی', 'g_desc' => 'مدرس ده‌ها کارگاه روان‌شناسی حضوری در شهرستان‌های استان بوشهر، مدرس مورد اعتماد بهزیستی و مربی فنی و حرفه‌ای بوشهر.', 'g_tags' => "کارگاه‌های حضوری\nمهارت‌های زندگی\nآموزش خانواده\nازدواج آگاهانه", 'g_url' => zc_eb_url( 'courses' ) ),
						)
					),
					'link_label' => 'دوره‌ها و کارگاه‌ها',
				)
			),
			array( '_element_id' => 'expertise' )
		);

		/* ۳) سمت‌ها و مسئولیت‌ها */
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 38,
					'align'   => 'top',
					'widgets' => array(
						zc_eb_heading( 'سمت‌ها و مسئولیت‌ها', 'نقش‌هایی که امروز بر عهده دارم', 'امروز', 'در کنار کار بالینی، در نهادهای درمانی، حمایتی و آموزشی استان بوشهر مسئولیت دارم؛ نقش‌هایی که نگاهم به سلامت روان را از اتاق درمان فراتر می‌برد.' ),
					),
				),
				array(
					'size'    => 62,
					'widgets' => array(
						zc_eb_list(
							array(
								array( 'دبیر کمیسیون پزشکی تعیین نوع و شدت معلولیت شهرستان بوشهر', 'shield' ),
								array( 'روان‌شناس کلینیک تخصصی دانا؛ از فروردین ۱۴۰۳ تا امروز', 'building' ),
								array( 'دبیر مؤسسه مردم‌نهاد جوانان مسیر روشن رستا؛ دارای مجوز از اداره ورزش و جوانان', 'users' ),
								array( 'مدرس مورد اعتماد بهزیستی', 'award' ),
								array( 'مربی فنی و حرفه‌ای بوشهر', 'briefcase' ),
								array( 'مدرس ده‌ها کارگاه روان‌شناسی حضوری در همه‌ی شهرستان‌های استان بوشهر', 'graduation' ),
							),
							'cards',
							'2'
						),
					),
				),
			),
			array( '_element_id' => 'roles', 'background_background' => 'classic', 'background_color' => 'rgba(234,239,247,0.55)' )
		);

		/* ۴) کتاب */
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'book',
				array(
					'eyebrow'        => 'تألیف',
					'book_title'     => 'راهنمای تشخیص و درمان اختلالات شخصیت',
					'author'         => 'مریم جمالی',
					'role_label'     => 'نویسنده',
					'description'    => "اختلالات شخصیت از پیچیده‌ترین موضوعات روان‌شناسی بالینی‌اند؛ الگوهایی پایدار که بر فکر، احساس و روابط فرد اثر می‌گذارند و تشخیص دقیق آن‌ها نیازمند شناخت ظریف تفاوت‌هاست.\n\nاین کتاب حاصل مطالعه و تجربه‌ی بالینی نویسنده است و می‌کوشد مسیر تشخیص و درمان اختلالات شخصیت را به زبانی روشن، ساختارمند و کاربردی در اختیار خواننده بگذارد.",
					'features'       => zc_elementor_repeater(
						array(
							array( 'feature' => 'مسیر تشخیص اختلالات شخصیت', 'feature_icon' => 'clipboard' ),
							array( 'feature' => 'رویکردهای درمانی', 'feature_icon' => 'heart' ),
							array( 'feature' => 'زبان روشن و ساختارمند', 'feature_icon' => 'file-text' ),
							array( 'feature' => 'حاصل تجربه‌ی بالینی نویسنده', 'feature_icon' => 'brain' ),
						)
					),
					'button_text'    => 'پرسش درباره تهیه کتاب',
					'button_url'     => zc_eb_url( 'contact' ),
					'button_style'   => 'secondary',
					'cover'          => array( 'url' => '' ),
					'cover_kicker'   => 'روان‌شناسی بالینی',
					'cover_position' => 'start',
				)
			),
			array( '_element_id' => 'book' )
		);

		/* ۵) سوابق کاری */
		$xp = array(
			array( 'روان‌شناس', 'کلینیک تخصصی دانا', 'فروردین ۱۴۰۳', '', 'yes', 'ارزیابی و درمان فردی مراجعان؛ درمان اختلالات خلقی، اضطراب و مشاوره‌ی روابط عاطفی.', 'بالینی', 'building' ),
			array( 'روان‌شناس', 'صدای مشاوره ۱۴۸۰', 'مرداد ۱۴۰۳', 'فروردین ۱۴۰۴', '', 'پاسخ‌گویی و مداخله در موقعیت‌های حساس از طریق خط ملی مشاوره‌ی تلفنی.', 'مشاوره تلفنی', 'phone' ),
			array( 'روان‌شناس', '۶ خوابگاه دانشگاه علوم پزشکی بوشهر', 'آبان ۱۴۰۳', 'اسفند ۱۴۰۳', '', 'همراهی روان‌شناختی دانشجویان علوم پزشکی در مواجهه با فشار تحصیلی، اضطراب و فرسودگی.', 'دانشجویی', 'graduation' ),
			array( 'روان‌شناس', 'بیمارستان قلب بوشهر', 'آبان ۱۴۰۳', 'اسفند ۱۴۰۳', '', 'حمایت روان‌شناختی از بیماران و خانواده‌ها در دوره‌ی بستری و درمان.', 'بیمارستانی', 'heart' ),
			array( 'روان‌شناس', 'بیمارستان شهدای خلیج فارس بوشهر', 'آبان ۱۴۰۳', 'اسفند ۱۴۰۳', '', 'ارائه‌ی خدمات روان‌شناختی به بیماران بستری و همراهان آن‌ها.', 'بیمارستانی', 'building' ),
			array( 'روان‌شناس', 'خانه امن بهزیستی بوشهر', 'تیر ۱۴۰۱', 'خرداد ۱۴۰۳', '', 'همراهی و توانمندسازی زنان در شرایط دشوار و بحرانی؛ تجربه‌ای عمیق از تاب‌آوری.', 'مداخله در بحران', 'shield' ),
			array( 'دبیر و مشاور تحصیلی', 'مدرسه غیرانتفاعی نرگس (متوسطه اول و دوم)', 'مهر ۱۴۰۱', 'خرداد ۱۴۰۲', '', 'تدریس و مشاوره‌ی تحصیلی دانش‌آموزان؛ برنامه‌ریزی، انگیزه و مدیریت اضطراب امتحان.', 'آموزشی', 'book' ),
			array( 'دبیر و مشاور تحصیلی', 'مدرسه غیرانتفاعی سروش ایران', 'مهر ۱۴۰۱', 'خرداد ۱۴۰۲', '', 'تدریس و مشاوره‌ی تحصیلی دانش‌آموزان و همراهی خانواده‌ها در مسیر تحصیلی.', 'آموزشی', 'book' ),
		);
		$xp_rows = array();
		foreach ( $xp as $row ) {
			$xp_rows[] = array(
				'xp_title'   => $row[0],
				'xp_org'     => $row[1],
				'xp_start'   => $row[2],
				'xp_end'     => $row[3],
				'xp_current' => $row[4],
				'xp_desc'    => $row[5],
				'xp_tag'     => $row[6],
				'xp_icon'    => $row[7],
			);
		}
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'timeline',
				array(
					'zc_tone'       => 'inverse',
					'eyebrow'       => 'سوابق کاری',
					'title'         => 'مسیر حرفه‌ای؛ از مدرسه تا بیمارستان',
					'subtitle'      => 'کلینیک، بیمارستان، خط مشاوره ۱۴۸۰، خانه امن و مدرسه؛ هر محیط زاویه‌ی تازه‌ای برای شناخت و همراهی آدم‌ها بود.',
					'align'         => 'center',
					'layout'        => 'alternate',
					'current_label' => 'هم‌اکنون',
					'present_word'  => 'اکنون',
					'show_desc'     => 'yes',
					'items'         => zc_elementor_repeater( $xp_rows ),
				)
			),
			array( '_element_id' => 'experience' )
		);

		/* ۶) دوره‌های گذرانده‌شده */
		$omid    = 'مرکز مشاوره امید مهر (تحت نظر بهزیستی)';
		$farda   = 'مؤسسه اندیشه‌ورزان زندگی فردا';
		$courses = array(
			array( 'دوره تربیت درمانگر و تسهیلگر کودک و نوجوان', '۵۰۰', 'زیر نظر دکتر ترکمان', 'اردیبهشت تا آذر ۱۴۰۴', 'کودک و نوجوان', 'yes' ),
			array( 'تربیت زوج و خانواده‌درمانگر سیستمی', '۱۵۰', $farda, 'اسفند ۱۴۰۱', 'زوج و خانواده', '' ),
			array( 'تربیت زوج و خانواده‌درمانگر سیستمی — پیشرفته', '۱۵۰', $farda, 'خرداد ۱۴۰۲', 'زوج و خانواده', '' ),
			array( 'پرورش مشاوره کودک و نوجوان', '۱۰۰', 'مؤسسه آموزش عالی آزاد خرد', 'مهر ۱۴۰۱', 'کودک و نوجوان', '' ),
			array( 'تربیت مشاور تحصیلی (دوره جامع)', '۶۴', 'مرکز یادگیری آزاد و الکترونیکی دانشگاه خوارزمی', 'بهمن ۱۴۰۱', 'مشاوره تحصیلی', '' ),
			array( 'مداخله در بحران‌های زوجی و خانوادگی', '۵۰', $farda, 'بهمن ۱۴۰۲', 'زوج و خانواده', '' ),
			array( 'درمان شناختی و رفتاری (CBT)', '۳۰', $omid, 'آذر ۱۴۰۰', 'بالینی و ارزیابی', '' ),
			array( 'آموزش پیش از ازدواج', '۳۰', $omid, 'دی ۱۴۰۰', 'ازدواج و روابط', '' ),
			array( 'دوره مصاحبه بالینی و ارزیابی اختلالات روان‌پزشکی', '۲۴', 'مؤسسه تحقیقات علوم شناختی و رفتاری رخواره — دانشگاه آزاد', 'خرداد ۱۴۰۲', 'بالینی و ارزیابی', '' ),
			array( 'مشاوره ازدواج و خانواده', '۱۶', 'اداره ورزش و جوانان با نظارت سازمان نظام روان‌شناسی', 'اسفند ۱۴۰۰', 'ازدواج و روابط', '' ),
			array( 'خانواده پایدار — ازدواج آگاهانه', '۴', 'سازمان نظام روان‌شناسی و مشاوره', 'خرداد ۱۴۰۱', 'ازدواج و روابط', '' ),
			array( 'تست تخصصی ۱۶ عاملی کتل', '۴', $omid, 'آبان ۱۴۰۰', 'بالینی و ارزیابی', '' ),
			array( 'آموزش پیشگیری از خیانت', '۳', $omid, 'دی ۱۴۰۰', 'ازدواج و روابط', '' ),
			array( 'مهارت‌های پیش از ازدواج', '۲', 'دانشگاه آزاد', 'آذر ۱۳۹۹', 'ازدواج و روابط', '' ),
		);
		$course_rows = array();
		foreach ( $courses as $row ) {
			$course_rows[] = array(
				'c_title'    => $row[0],
				'c_hours'    => $row[1],
				'c_org'      => $row[2],
				'c_date'     => $row[3],
				'c_cat'      => $row[4],
				'c_featured' => $row[5],
			);
		}
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'courses',
				array(
					'eyebrow'     => 'دوره‌های گذرانده‌شده',
					'title'       => 'آموزش مستمر؛ پشتوانه‌ی هر جلسه',
					'subtitle'    => 'از درمان شناختی‌رفتاری و مصاحبه بالینی تا خانواده‌درمانی سیستمی و درمانگری کودک و نوجوان؛ همراه با بیش از ۱۶ دوره‌ی تخصصی دیگر.',
					'align'       => 'center',
					'items'       => zc_elementor_repeater( $course_rows ),
					'extra_count' => '۱۶',
					'extra_label' => 'دوره تخصصی دیگر',
					'columns'     => '3',
					'summary'     => 'yes',
					'label_hours' => 'ساعت آموزش تخصصی',
					'label_count' => 'دوره و کارگاه گذرانده‌شده',
					'label_orgs'  => 'مرجع و مؤسسه آموزشی',
					'filter'      => 'yes',
					'all_label'   => 'همه دوره‌ها',
					'bar'         => 'yes',
					'hours_unit'  => 'ساعت',
				)
			),
			array( '_element_id' => 'training' )
		);

		/* ۷) فراخوان */
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_services' ) ) :
	/**
	 * برگه «خدمات» با المنتور.
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_services( $page_id ) {
		$booking = zc_eb_url( 'booking' );

		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'خدمات و مسیرهای همراهی', 'accent_word' => 'خدمات' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'services',
				array(
					'eyebrow'     => 'مسیرهای همراهی',
					'title'       => 'از ارزیابی اولیه تا ذهن مدیر؛ مسیری که امروز به آن نیاز داری',
					'subtitle'    => 'اگر مطمئن نیستی کدام مسیر مناسب توست، از ارزیابی اولیه شروع کن؛ با هم انتخاب می‌کنیم.',
					'align'       => 'center',
					'source'      => 'cpt',
					'count'       => 6,
					'columns'     => '3',
					'show_meta'   => 'yes',
					'button_text' => 'جزئیات و رزرو',
					'notch'       => 'yes',
					'bg_style'    => 'none',
				)
			),
			array( '_element_id' => 'services' )
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'pricing',
				array(
					'eyebrow'  => 'تعرفه‌ها',
					'title'    => 'شفاف، بدون هزینه‌ی پنهان',
					'subtitle' => 'تعرفه‌ها مطابق تعرفه مصوب سازمان نظام روان‌شناسی و مشاوره استان بوشهر است. تماس هماهنگی ۱۵ دقیقه‌ای همیشه رایگان است.',
					'align'    => 'center',
					'columns'  => '3',
					'zc_tone'  => 'inverse',
					'plans'    => zc_elementor_repeater(
						array(
							array(
								'plan_name'        => 'ارزیابی اولیه و نقشه الگو',
								'plan_desc'        => 'نقطه‌ی شروع هر مسیر',
								'plan_price'       => '۶۵۰',
								'plan_period'      => 'هزار تومان / ۴۵ دقیقه',
								'plan_features'    => "تماس هماهنگی رایگان ۱۵ دقیقه‌ای\nشناسایی الگوی غالب\nنقشه‌ی مکتوب مسیر پیشنهادی\nیک تمرین برای شروع همان هفته",
								'plan_badge'       => '',
								'plan_featured'    => '',
								'plan_button_text' => 'رزرو ارزیابی',
								'plan_button_url'  => $booking,
							),
							array(
								'plan_name'        => 'بسته ۶ جلسه‌ای رشد فردی',
								'plan_desc'        => 'از کمال‌گرایی تا اقدام / ذهن مرتب',
								'plan_price'       => '۵٫۱',
								'plan_period'      => 'میلیون تومان / ۶ جلسه ۶۰ دقیقه‌ای',
								'plan_features'    => "۶ جلسه‌ی هفتگی حضوری یا آنلاین\nتمرین و کاربرگ بین جلسات\nخلاصه‌ی مکتوب هر جلسه\nاسترداد جلسات برگزارنشده",
								'plan_badge'       => 'پیشنهاد اصلی',
								'plan_featured'    => 'yes',
								'plan_button_text' => 'شروع مسیر',
								'plan_button_url'  => $booking,
							),
							array(
								'plan_name'        => 'دوره مهارت‌های زندگی',
								'plan_desc'        => 'گروهی، حضوری در بوشهر',
								'plan_price'       => '۳٫۲',
								'plan_period'      => 'میلیون تومان / ۸ جلسه ۹۰ دقیقه‌ای',
								'plan_features'    => "ده مهارت زندگی WHO\nگروه کوچک و تعاملی\nکاربرگ و تمرین هفتگی\nبازگشت کامل وجه تا ۷۲ ساعت پیش از شروع",
								'plan_badge'       => '',
								'plan_featured'    => '',
								'plan_button_text' => 'جزئیات دوره',
								'plan_button_url'  => zc_eb_url( 'courses' ),
							),
						)
					),
				)
			),
			array( '_element_id' => 'pricing' )
		);
		$data[] = zc_eb_notice( 'شرایط لغو، جابه‌جایی و بازگشت وجه در [zc_link page="refund-policy" slug="refund-policy" text="این صفحه"] و ماهیت خدمات در [zc_link page="informed-consent" slug="informed-consent" text="رضایت‌نامه آگاهانه"] آمده است. خدمات آموزشی و رشدمحور است و جایگزین درمان روان‌پزشکی نیست.' );
		$data[] = zc_elementor_common_blocks( 'process' );
		$data[] = zc_elementor_common_blocks( 'faq' );
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_assessments' ) ) :
	/**
	 * برگه «تست‌ها و ارزیابی‌ها».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_assessments( $page_id ) {
		$booking = zc_eb_url( 'booking' );
		$soon    = 'به‌زودی آنلاین';

		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'ارزیابی‌ها', 'accent_word' => 'تست‌ها' ) );
		$data[] = zc_eb_notice( 'تست‌ها نقطه‌ی شروع‌اند، نه تشخیص. نتیجه‌ی هیچ پرسش‌نامه‌ای به‌تنهایی تشخیص بالینی نیست؛ معنای آن را در جلسه ارزیابی با هم رمزگشایی می‌کنیم. [zc_link page="disclaimer-emergency" slug="disclaimer-emergency" text="سلب مسئولیت"]', 'alert' );
		$data[] = zc_eb_manual_services(
			array(
				'eyebrow'  => 'ابزارهای خودشناسی',
				'title'    => 'الگوی غالبت را با ابزارهای معتبر پیدا کن',
				'subtitle' => 'فعلاً این ارزیابی‌ها در جلسه‌ی ارزیابی اولیه (حضوری یا آنلاین) انجام می‌شوند. نسخه‌ی آنلاین به‌زودی در همین صفحه فعال می‌شود.',
			),
			array(
				array( 'icon' => 'target', 'title' => 'مقیاس چندبعدی کمال‌گرایی', 'desc' => 'استانداردهای شخصی، نگرانی از اشتباه و انتظارات دیگران را جدا می‌کند تا ببینی کمال‌گرایی‌ات سازنده است یا فرساینده.', 'duration' => 'حدود ۱۰ دقیقه', 'badge' => $soon, 'url' => $booking ),
				array( 'icon' => 'clipboard', 'title' => 'مقیاس اهمالکاری', 'desc' => 'شدت و الگوی به‌تعویق‌انداختن کارها را می‌سنجد؛ از تعویق از سر ترس تا تعویق از سر خستگی.', 'duration' => 'حدود ۵ دقیقه', 'badge' => $soon, 'url' => $booking ),
				array( 'icon' => 'brain', 'title' => 'پرسش‌نامه طرحواره‌های یانگ (فرم کوتاه)', 'desc' => 'طرحواره‌های ناسازگار اولیه را شناسایی می‌کند؛ ریشه‌ی الگوهایی مثل «باید بی‌نقص باشم» یا «نباید کسی را ناراحت کنم».', 'duration' => 'حدود ۲۰ دقیقه', 'badge' => $soon, 'url' => $booking ),
				array( 'icon' => 'compass', 'title' => 'سبک‌های تصمیم‌گیری', 'desc' => 'نشان می‌دهد تصمیم‌هایت بیشتر عقلانی، شهودی، وابسته، اجتنابی یا آنی است و کجا گیر می‌کنی.', 'duration' => 'حدود ۸ دقیقه', 'badge' => $soon, 'url' => $booking ),
				array( 'icon' => 'seedling', 'title' => 'خودارزیابی مهارت‌های زندگی', 'desc' => 'تصویر روشنی از ده مهارت زندگی (از خودآگاهی تا مدیریت استرس) می‌دهد تا بدانی تمرین را از کجا شروع کنی.', 'duration' => 'حدود ۱۰ دقیقه', 'badge' => $soon, 'url' => $booking ),
				array( 'icon' => 'briefcase', 'title' => 'الگوهای ذهن مدیر', 'desc' => 'ویژه مدیران: کنترل‌گری، ناتوانی در تفویض، کمال‌گرایی مدیریتی و تصمیم‌های معوق را می‌سنجد.', 'duration' => 'حدود ۱۲ دقیقه', 'badge' => $soon, 'url' => zc_eb_url( 'managers' ) ),
			),
			'tests'
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'process',
				array(
					'eyebrow'         => 'بعد از تست',
					'title'           => 'از عدد تا معنا؛ سه قدم تا نقشه‌ی الگو',
					'align'           => 'center',
					'persian_numbers' => 'yes',
					'zc_tone'         => 'inverse',
					'steps'           => zc_elementor_repeater(
						array(
							array( 'step_title' => 'انجام ارزیابی', 'step_desc' => 'پرسش‌نامه‌ها را در جلسه یا به‌زودی به‌صورت آنلاین تکمیل می‌کنی؛ پاسخ‌ها محرمانه می‌ماند.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'رمزگشایی نتیجه', 'step_desc' => 'در جلسه ارزیابی ۴۵ دقیقه‌ای، نتیجه را کنار داستان زندگی‌ات می‌گذاریم تا الگوی واقعی دیده شود.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'نقشه‌ی مسیر', 'step_desc' => 'نقشه‌ی مکتوب الگو و مسیر پیشنهادی را دریافت می‌کنی؛ یا اگر لازم باشد، ارجاع مناسب.', 'step_url' => $booking ),
						)
					),
				)
			)
		);
		$data[] = zc_elementor_common_blocks( 'cta', array( 'title' => 'نتیجه‌ی تست را به جلسه ارزیابی بیاور', 'badge' => 'قدم بعدی' ) );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_courses' ) ) :
	/**
	 * برگه «دوره‌ها و کارگاه‌ها».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_courses( $page_id ) {
		$booking = zc_eb_url( 'booking' );

		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'آموزش گروهی', 'accent_word' => 'کارگاه‌ها' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'about',
				array(
					'eyebrow'        => 'دوره شاخص',
					'title'          => 'دوره حضوری مهارت‌های زندگی در بوشهر',
					'align'          => 'right',
					'content'        => "ده مهارتی که سازمان جهانی بهداشت پایه‌ی سلامت روان و زندگی مؤثر می‌داند، در <strong>۸ جلسه‌ی ۹۰ دقیقه‌ای</strong> و در گروهی کوچک و تعاملی تمرین می‌کنیم؛ نه با سخنرانی، بلکه با موقعیت‌های واقعی زندگی و کار.\n\nهر جلسه یک تمرین هفتگی دارد و جلسه‌ی بعد با مرور همان تمرین شروع می‌شود؛ چون مهارت با شنیدن ساخته نمی‌شود، با تکرار ساخته می‌شود.",
					'image'          => zc_eb_img( 'maryam-workshop' ),
					'image_position' => 'right',
					'badge_number'   => '۱۰',
					'badge_label'    => 'مهارت زندگی (WHO)',
					'features'       => zc_elementor_repeater(
						array(
							array( 'feature' => '۸ جلسه‌ی ۹۰ دقیقه‌ای، هفته‌ای یک جلسه' ),
							array( 'feature' => 'گروه کوچک برای تعامل و تمرین واقعی' ),
							array( 'feature' => 'کاربرگ و تمرین هفتگی برای هر مهارت' ),
							array( 'feature' => 'هزینه: ۳٫۲ میلیون تومان — بازگشت کامل وجه تا ۷۲ ساعت پیش از شروع' ),
						)
					),
					'button_text'    => 'ثبت‌نام و هماهنگی',
					'button_url'     => $booking,
				)
			),
			array( '_element_id' => 'life-skills' )
		);
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 36,
					'widgets' => array(
						zc_eb_heading( 'سرفصل‌ها', 'ده مهارت زندگی؛ از خودآگاهی تا مدیریت استرس', 'ده مهارت', 'این ده مهارت در چهار محور تمرین می‌شوند: شناخت خود، ارتباط با دیگران، فکر کردن و تصمیم گرفتن، و مدیریت هیجان‌ها.' ),
					),
				),
				array(
					'size'    => 64,
					'widgets' => array(
						zc_eb_list(
							array(
								array( 'خودآگاهی', 'eye' ),
								array( 'همدلی', 'heart' ),
								array( 'ارتباط مؤثر', 'chat' ),
								array( 'روابط بین‌فردی', 'users' ),
								array( 'تصمیم‌گیری', 'compass' ),
								array( 'حل مسئله', 'target' ),
								array( 'تفکر خلاق', 'sparkles' ),
								array( 'تفکر نقاد', 'brain' ),
								array( 'مدیریت هیجان‌ها', 'hand' ),
								array( 'مدیریت استرس', 'leaf' ),
							),
							'cards',
							'2'
						),
					),
				),
			)
		);
		$data[] = zc_eb_manual_services(
			array(
				'eyebrow'  => 'کارگاه‌ها',
				'title'    => 'کارگاه‌های کوتاه و متمرکز',
				'subtitle' => 'زمان‌بندی دوره‌ها و کارگاه‌های بعدی در اینستاگرام و تلگرام اعلام می‌شود.',
				'zc_tone'  => 'inverse',
			),
			array(
				array( 'icon' => 'target', 'title' => 'کارگاه «از کمال‌گرایی تا اقدام»', 'desc' => 'یک کارگاه فشرده برای شناخت زنجیره‌ی کمال‌گرایی و اهمالکاری و تمرین تکنیک «نسخه‌ی ۷۰ درصد».', 'duration' => 'حضوری — بوشهر', 'badge' => 'پرطرفدار', 'url' => $booking ),
				array( 'icon' => 'compass', 'title' => 'کارگاه «ذهن مرتب»', 'desc' => 'تصمیم‌گیری بدون فرسودگی: معیارسازی، مدیریت بلاتکلیفی و ماندن پای انتخاب.', 'duration' => 'حضوری — بوشهر', 'badge' => '', 'url' => $booking ),
				array( 'icon' => 'video', 'title' => 'دوره آنلاین سراسری', 'desc' => 'نسخه‌ی آنلاین مهارت‌های زندگی و کمال‌گرایی برای کسانی که در شهرهای دیگر زندگی می‌کنند.', 'duration' => 'آنلاین', 'badge' => 'به‌زودی', 'url' => zc_eb_url( 'contact' ) ),
			)
		);
		$data[] = zc_eb_notice( 'شرایط انصراف از دوره‌ها: بازگشت کامل وجه تا ۷۲ ساعت پیش از شروع؛ پس از آن، بازگشت هزینه‌ی جلسات باقی‌مانده با کسر ۱۰٪. جزئیات در [zc_link page="refund-policy" slug="refund-policy" text="شرایط لغو و بازگشت وجه"].' );
		$data[] = zc_elementor_common_blocks( 'testimonials', array( 'title' => 'آنچه مراجعان در دکترتو نوشته‌اند', 'zc_tone' => '' ) );
		$data[] = zc_elementor_common_blocks( 'cta', array( 'title' => 'برای دوره‌ی بعدی جا رزرو کن', 'badge' => 'ظرفیت محدود' ) );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_managers' ) ) :
	/**
	 * برگه «ویژه مدیران؛ ذهن مدیر».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_managers( $page_id ) {
		$booking = zc_eb_url( 'booking' );

		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'برنامه ذهن مدیر', 'title' => 'ذهن مدیر؛ رهبری با ذهنی روشن‌تر', 'accent_word' => 'ذهن مدیر', 'size' => 'large' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'about',
				array(
					'eyebrow'        => 'برای مدیران ۲۸ تا ۴۵ سال',
					'title'          => 'وقتی همه‌چیز روی دوش توست و ذهنت هیچ‌وقت خاموش نمی‌شود',
					'align'          => 'right',
					'content'        => "خیلی از مدیران جوان با همان الگوهایی به جایگاه مدیریت رسیده‌اند که حالا مانعشان شده: <strong>کمال‌گرایی</strong> که تفویض را سخت می‌کند، <strong>کنترل‌گری</strong> که تیم را منفعل می‌کند و <strong>تصمیم‌های معوق</strong> که انرژی همه را می‌گیرد.\n\nدر برنامه‌ی «ذهن مدیر» این الگوها را می‌شناسیم، رمزگشایی می‌کنیم و با تمرین‌هایی که مستقیم در محیط کار اجرا می‌شوند، جایگزینشان می‌کنیم.",
					'image'          => zc_eb_img( 'maryam-session' ),
					'image_position' => 'left',
					'badge_number'   => '۶',
					'badge_label'    => 'جلسه‌ی فردی متمرکز',
					'features'       => zc_elementor_repeater(
						array(
							array( 'feature' => 'جلسات فردی حضوری در بوشهر یا آنلاین' ),
							array( 'feature' => 'رازداری کامل؛ مناسب مدیران ارشد' ),
							array( 'feature' => 'تمرین‌های قابل اجرا در جلسات و تصمیم‌های واقعی' ),
						)
					),
					'button_text'    => 'هماهنگی جلسه ارزیابی',
					'button_url'     => $booking,
				)
			)
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'schema',
				array(
					'eyebrow'           => 'الگوهای رایج',
					'title'             => 'شش الگویی که ذهن مدیر را خسته می‌کند',
					'subtitle'          => 'کدام‌یک برایت آشناست؟ هر الگو یک منطق قدیمی دارد که روزی کمکت کرده؛ امروز فقط باید به‌روز شود.',
					'align'             => 'center',
					'columns'           => '3',
					'show_numbers'      => 'yes',
					'show_panel'        => 'yes',
					'panel_button_text' => 'سنجش الگوهای ذهن مدیر',
					'panel_button_url'  => zc_eb_url( 'assessments' ),
					'items'             => zc_elementor_repeater(
						array(
							array( 'item_title' => 'کنترل‌گری', 'item_desc' => '«اگر خودم انجام ندهم، خراب می‌شود.» نتیجه: تیمی که منتظر دستور می‌ماند و مدیری که هیچ‌وقت استراحت نمی‌کند.', 'item_url' => zc_elementor_url( '' ) ),
							array( 'item_title' => 'کمال‌گرایی مدیریتی', 'item_desc' => 'استانداردهایی که هیچ گزارشی را کافی نمی‌داند و سرعت تصمیم‌گیری را پایین می‌آورد.', 'item_url' => zc_elementor_url( '' ) ),
							array( 'item_title' => 'تصمیم‌های معوق', 'item_desc' => 'جمع کردن اطلاعات بیشتر و بیشتر، به امید تصمیمی بی‌ریسک که هیچ‌وقت نمی‌رسد.', 'item_url' => zc_elementor_url( '' ) ),
							array( 'item_title' => 'ناتوانی در تفویض', 'item_desc' => 'سپردن کار و پس گرفتن آن؛ چرخه‌ای که هم تو را فرسوده می‌کند و هم اعتماد تیم را.', 'item_url' => zc_elementor_url( '' ) ),
							array( 'item_title' => 'تأییدطلبی', 'item_desc' => 'سختی در «نه» گفتن، بازخورد صریح دادن و تحمل نارضایتی دیگران.', 'item_url' => zc_elementor_url( '' ) ),
							array( 'item_title' => 'فرسودگی خاموش', 'item_desc' => 'کار زیاد، رضایت کم و ذهنی که حتی در تعطیلات هم مشغول است.', 'item_url' => zc_elementor_url( '' ) ),
						)
					),
				)
			)
		);
		$data[] = zc_eb_manual_services(
			array(
				'eyebrow' => 'قالب‌های همکاری',
				'title'   => 'متناسب با زمان و نیاز تو',
				'zc_tone' => 'inverse',
			),
			array(
				array( 'icon' => 'compass', 'title' => 'ارزیابی الگوهای مدیریتی', 'desc' => 'یک جلسه‌ی ۴۵ دقیقه‌ای برای شناخت الگوی غالب و نقشه‌ی مسیر.', 'price' => '۶۵۰ هزار تومان', 'duration' => '۴۵ دقیقه', 'badge' => 'شروع', 'url' => $booking ),
				array( 'icon' => 'briefcase', 'title' => 'برنامه فردی ذهن مدیر', 'desc' => 'جلسات هفتگی متمرکز بر تفویض، تصمیم‌گیری و مدیریت فشار، با تمرین در محیط کار.', 'price' => 'طبق تعرفه مصوب', 'duration' => '۶۰ دقیقه', 'badge' => 'پیشنهادی', 'url' => $booking ),
				array( 'icon' => 'users', 'title' => 'کارگاه سازمانی', 'desc' => 'کارگاه‌های مهارت‌های زندگی و مدیریت استرس برای تیم‌ها و سازمان‌های بوشهر.', 'price' => 'استعلام', 'duration' => 'نیم‌روزه', 'badge' => '', 'url' => zc_eb_url( 'contact' ) ),
			)
		);
		$data[] = zc_elementor_common_blocks( 'process' );
		$data[] = zc_elementor_common_blocks( 'cta', array( 'title' => 'ذهن روشن‌تر، تصمیم‌های سبک‌تر', 'badge' => 'ویژه مدیران' ) );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_schemas' ) ) :
	/**
	 * برگه «طرحواره‌ها و الگوهای ذهنی» (کتابخانه‌ی کامل).
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_schemas( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'کتابخانه‌ی خودشناسی', 'accent_word' => 'الگوهای ذهنی' ) );
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 58,
					'align'   => 'top',
					'widgets' => array(
						zc_eb_heading(
							'مدل طرحواره‌ی جفری یانگ',
							'الگوهایی که بی‌صدا تصمیم می‌گیرند',
							'بی‌صدا',
							'طرحواره، الگویی عمیق از خاطره، احساس، باور و حس بدنی است که وقتی نیازهای هیجانی کودکی — امنیت، پذیرش، خودمختاری، آزادی بیان احساس، بازی و مرزهای سالم — برآورده نشده‌اند شکل می‌گیرد. این الگوها در بزرگسالی در روابط، کار و تصمیم‌ها تکرار می‌شوند. «ذهنیت‌ها» حالت‌های لحظه‌ای‌اند که طرحواره‌ها را فعال می‌کنند، «سبک‌های مقابله» شیوه‌ی پاسخ ما به آن‌هاست و «خطاهای شناختی» عینک‌هایی که این باورها را در افکار روزمره زنده نگه می‌دارند. هر مدخل این کتابخانه شامل باور مرکزی، نیاز برآورده‌نشده، نشانه‌ها، ریشه‌ها، موقعیت‌های فعال‌ساز، نمونه‌ی واقعی، پیام سالم و یک تمرین عملی است.'
						),
					),
				),
				array(
					'size'    => 42,
					'align'   => 'top',
					'widgets' => array(
						zc_eb_list(
							array(
								array( '۱۸ طرحواره‌ی ناسازگار اولیه در ۵ حوزه‌ی نیاز', 'brain' ),
								array( '۱۴ ذهنیت: کودک، مقابله‌ای، والد و بزرگسال سالم', 'mirror' ),
								array( '۳ سبک مقابله: تسلیم، اجتناب و جبران افراطی', 'refresh' ),
								array( '۱۲ خطای شناختی با بازنویسی متعادل', 'sparkles' ),
								array( 'نوشته‌شده برای خودشناسی؛ نه برچسب‌زدن و تشخیص', 'shield' ),
							),
							'cards'
						),
					),
				),
			),
			array( 'padding' => array( 'unit' => 'px', 'top' => '72', 'right' => '25', 'bottom' => '24', 'left' => '25', 'isLinked' => false ) )
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'schemas',
				array(
					'eyebrow'        => 'فهرست کامل',
					'title'          => 'کدام الگو برایت آشناست؟',
					'subtitle'       => 'با دکمه‌ها دسته را انتخاب کن یا نام طرحواره را جستجو کن؛ روی هر کارت بزن تا شرح کامل، نشانه‌ها و تمرین آن را ببینی.',
					'align'          => 'center',
					'default_filter' => 'all',
					'show_filter'    => 'yes',
					'show_search'    => 'yes',
					'show_subgroups' => 'yes',
					'show_desc'      => 'yes',
					'columns'        => '3',
					'show_en'        => 'yes',
					'show_summary'   => 'yes',
					'heading_tag'    => 'h2',
				)
			),
			array( '_element_id' => 'library' )
		);
		$data[] = zc_eb_notice( 'این کتابخانه برای آموزش و خودشناسی نوشته شده و جایگزین ارزیابی تخصصی نیست. همه‌ی ما کم‌وبیش طرحواره‌هایی داریم؛ دیدن خود در یک توصیف به معنای «اختلال» نیست. برای شناخت دقیق الگوهای خودت، پرسش‌نامه‌ی استاندارد یانگ و گفتگو در [zc_link page="booking" slug="booking" text="جلسه‌ی ارزیابی"] بهترین نقطه‌ی شروع است. در شرایط بحرانی با اورژانس اجتماعی ۱۲۳ تماس بگیر.' );
		$data[] = zc_elementor_common_blocks( 'process', array( 'zc_tone' => 'inverse' ) );
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_faq' ) ) :
	/**
	 * برگه «پرسش‌های پرتکرار».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_faq( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'پاسخ پرسش‌ها', 'accent_word' => 'پرتکرار' ) );
		$data[] = zc_elementor_common_blocks(
			'faq',
			array(
				'eyebrow' => 'پیش از جلسه اول',
				'title'   => 'هر آنچه باید درباره‌ی جلسات، تست‌ها و شرایط بدانی',
				'count'   => 12,
				'layout'  => 'side',
			)
		);
		$data[] = zc_eb_notice( 'پاسخ پرسشت را پیدا نکردی؟ در [zc_link page="contact" slug="contact" text="صفحه تماس"] از طریق تلگرام، بله یا تلفن بپرس. قوانین کامل در [zc_link page="terms" slug="terms" text="قوانین و مقررات"] آمده است.' );
		$data[] = zc_elementor_common_blocks( 'process', array( 'zc_tone' => 'inverse' ) );
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_contact' ) ) :
	/**
	 * برگه «تماس با من».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_contact( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'در ارتباط باشیم', 'accent_word' => 'تماس' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'contact',
				array(
					'eyebrow'      => 'راه‌های ارتباطی',
					'title'        => 'نوبت‌دهی تلفنی؛ پشتیبانی در تلگرام و بله',
					'subtitle'     => 'برای رزرو نوبت تماس بگیر یا پیام بده؛ در ساعات کاری پاسخ می‌دهم.',
					'align'        => 'right',
					'phone_label'  => 'نوبت‌دهی',
					'channels'     => array( 'telegram', 'bale', 'instagram' ),
					'map_iframe'   => (string) zc_opt( 'contact_map', '' ),
					'show_socials' => '',
					'layout'       => 'split',
				)
			),
			array( '_element_id' => 'contact' )
		);
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 45,
					'widgets' => array(
						zc_eb_heading( 'فرم تماس', 'پیامت را بنویس؛ در ساعات کاری پاسخ می‌دهم', 'پاسخ', 'برای رزرو، همکاری سازمانی یا هر پرسشی از این فرم استفاده کن. لطفاً جزئیات شخصی یا بالینی را در فرم ننویس؛ آن‌ها را در جلسه مطرح می‌کنیم.' ),
						zc_eb_list(
							array(
								array( 'شنبه تا چهارشنبه، ساعت ۱۶ تا ۲۰:۳۰', 'clock' ),
								array( 'رزرو نوبت: ۰۹۱۷۹۷۰۶۶۸۸ | تلفن مطب: ۰۹۹۱۴۱۴۲۰۷۰', 'phone' ),
								array( 'بوشهر، خیابان رئیس‌علی دلواری، ساختمان پزشکان طبیب، طبقه پنجم، واحد ۵۰۳', 'map-pin' ),
								array( 'جلسات آنلاین برای سراسر ایران', 'video' ),
								array( 'رازداری کامل طبق اصول اخلاق حرفه‌ای', 'shield' ),
							)
						),
					),
				),
				array(
					'size'    => 55,
					'widgets' => array( zc_eb_form( 'ارسال پیام', 'فیلدهای ستاره‌دار الزامی هستند.', 'ارسال پیام', true ) ),
				),
			),
			array( '_element_id' => 'contact-form' )
		);
		$data[] = zc_eb_notice( '[zc_info field="emergency"]', 'alert' );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'trust-badges',
				array(
					'eyebrow'  => 'اعتماد و شفافیت',
					'title'    => 'مجوزها و نمادهای Maryam-Jamali.ir',
					'subtitle' => 'فعالیت با پروانه اشتغال ۲۸۸۵۸ سازمان نظام روان‌شناسی و مشاوره؛ نمادهای اعتماد برای استعلام در دسترس‌اند.',
					'items'    => zc_elementor_repeater( array( array( 'type' => 'pco', 'mode' => 'code' ), array( 'type' => 'enamad', 'mode' => 'code' ), array( 'type' => 'samandehi', 'mode' => 'code' ), array( 'type' => 'zarinpal', 'mode' => 'code' ), array( 'type' => 'payir', 'mode' => 'code' ) ) ),
					'layout'   => 'stacked',
					'style'    => 'navy',
					'columns'  => '5',
					'captions' => 'yes',
					'fallback' => 'yes',
					'wrap'     => 'section',
				)
			),
			array( '_element_id' => 'trust' )
		);
		$data[] = zc_elementor_common_blocks( 'faq', array( 'count' => 4, 'zc_tone' => 'inverse' ) );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_booking' ) ) :
	/**
	 * برگه «رزرو نوبت».
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_booking( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'قدم اول', 'accent_word' => 'نوبت' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'process',
				array(
					'eyebrow'         => 'روند رزرو',
					'title'           => 'از یک تماس رایگان تا مسیر روشن',
					'align'           => 'center',
					'persian_numbers' => 'yes',
					'steps'           => zc_elementor_repeater(
						array(
							array( 'step_title' => 'ثبت درخواست', 'step_desc' => 'فرم را پر کن یا با ۰۹۱۷۹۷۰۶۶۸۸ تماس بگیر.', 'step_url' => zc_elementor_url( '#booking' ) ),
							array( 'step_title' => 'تماس هماهنگی ۱۵ دقیقه‌ای', 'step_desc' => 'رایگان و بدون تعهد؛ بررسی می‌کنیم خدمات برای موضوع تو مناسب است یا نه.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'جلسه ارزیابی ۴۵ دقیقه‌ای', 'step_desc' => 'حضوری در بوشهر یا آنلاین؛ شناسایی الگوی غالب و هدف.', 'step_url' => zc_elementor_url( '' ) ),
							array( 'step_title' => 'نقشه‌ی مسیر', 'step_desc' => 'پیشنهاد مسیر و تعداد جلسات؛ تصمیم نهایی با توست.', 'step_url' => zc_elementor_url( '' ) ),
						)
					),
				)
			)
		);
		$data[] = zc_elementor_row(
			array(
				array(
					'size'    => 50,
					'widgets' => array(
						zc_eb_heading( 'درخواست نوبت', 'فرم را پر کن؛ برای هماهنگی تماس می‌گیرم', 'تماس', 'نوبت‌دهی شنبه تا چهارشنبه، ساعت ۱۶ تا ۲۰:۳۰ انجام می‌شود. تماس هماهنگی هیچ هزینه و تعهدی ندارد.' ),
						zc_eb_list(
							array(
								array( 'تماس هماهنگی ۱۵ دقیقه‌ای رایگان', 'phone' ),
								array( 'جلسه ارزیابی ۴۵ دقیقه‌ای: ۶۵۰ هزار تومان', 'calendar' ),
								array( 'جابه‌جایی یا بازگشت کامل وجه تا ۴۸ ساعت پیش از جلسه', 'refresh' ),
								array( 'پرداخت فقط از طریق Maryam-Jamali.ir', 'lock' ),
							)
						),
					),
				),
				array(
					'size'    => 50,
					'widgets' => array( zc_eb_form( 'درخواست رزرو نوبت', 'در ساعات کاری برای هماهنگی تماس می‌گیرم.', 'ثبت درخواست', false ) ),
				),
			),
			array( '_element_id' => 'booking' )
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'trust-badges',
				array(
					'eyebrow'  => 'پرداخت امن',
					'title'    => 'پرداخت امن و معتبر',
					'subtitle' => 'هزینه‌ی جلسات فقط در دامنه‌ی Maryam-Jamali.ir و از درگاه‌های دارای مجوز پرداخت می‌شود. اطلاعات کارت بانکی نزد ما ذخیره نمی‌شود.',
					'items'    => zc_elementor_repeater( zc_trust_default_items() ),
					'layout'   => 'split',
					'style'    => 'cards',
					'columns'  => '4',
					'captions' => 'yes',
					'fallback' => 'yes',
					'wrap'     => 'section',
					'note'     => 'برای استعلام، روی هر نماد کلیک کن تا صفحه‌ی رسمی آن باز شود.',
				)
			),
			array( '_element_id' => 'trust' )
		);
		$data[] = zc_eb_notice( 'با ثبت درخواست، [zc_link page="informed-consent" slug="informed-consent" text="رضایت‌نامه آگاهانه"]، [zc_link page="refund-policy" slug="refund-policy" text="شرایط لغو و بازگشت وجه"] و [zc_link page="privacy" slug="privacy" text="حریم خصوصی"] را می‌پذیری. خدمات به افراد زیر ۱۸ سال فقط با رضایت ولی قانونی ارائه می‌شود. در شرایط اضطراری با ۱۲۳ یا ۱۱۵ تماس بگیر.', 'info' );
		$data[] = zc_elementor_common_blocks( 'faq', array( 'count' => 4 ) );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_elementor_page_title' ) ) :
	/**
	 * بخش «عنوان برگه» (ویجت zc-page-title) برای ابتدای برگه‌های داخلی.
	 *
	 * عنوان و توضیح به‌صورت پیش‌فرض از عنوان و چکیده‌ی همان برگه خوانده می‌شوند.
	 *
	 * @param array $settings تنظیمات ویجت.
	 * @return array<string, mixed>
	 */
	function zc_elementor_page_title( $settings = array() ) {
		return zc_elementor_section(
			zc_elementor_widget(
				'page-title',
				array_merge(
					array(
						'breadcrumbs' => 'yes',
						'align'       => 'start',
						'size'        => 'normal',
					),
					$settings
				)
			)
		);
	}
endif;

if ( ! function_exists( 'zc_build_elementor_blog' ) ) :
	/**
	 * برگه «مجله» با المنتور.
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_blog( $page_id ) {
		$data   = array();
		$data[] = zc_elementor_page_title( array( 'eyebrow' => 'یادداشت‌ها', 'accent_word' => 'الگوها' ) );
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'posts',
				array(
					'eyebrow'    => 'تازه‌ترین نوشته‌ها',
					'title'      => 'مفهوم، مثال واقعی، تمرین و قدم بعدی',
					'align'      => 'right',
					'source'     => 'post',
					'count'           => 5,
					'columns'         => '3',
					'arrangement'     => 'magazine',
					'layout'          => 'vertical',
					'ratio'           => '16-10',
					'excerpt'         => 22,
					'show_meta'       => 'yes',
					'show_cat'        => 'yes',
					'show_author'     => 'yes',
					'orderby'         => 'date',
					'cat_filter'      => 'yes',
					'pagination'      => 'yes',
					'pagination_type' => 'loadmore',
				)
			)
		);
		$data[] = zc_elementor_common_blocks( 'cta' );

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_legal' ) ) :
	/**
	 * برگه‌های قوانین (عنوان + متن با فهرست مطالب و پیوند سایر قوانین).
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_legal( $page_id ) {
		$key   = (string) get_post_meta( $page_id, '_zc_demo_page', true );
		$pages = function_exists( 'zc_demo_legal_pages' ) ? zc_demo_legal_pages() : array();
		$def   = isset( $pages[ $key ] ) ? $pages[ $key ] : array();

		$links = array();
		foreach ( $pages as $legal_key => $legal ) {
			$links[] = array(
				'link_text' => $legal['title'],
				'link_url'  => zc_eb_url( $legal_key ),
			);
		}

		$data   = array();
		$data[] = zc_elementor_page_title(
			array(
				'eyebrow' => isset( $def['eyebrow'] ) ? $def['eyebrow'] : 'قوانین',
				'size'    => 'compact',
			)
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'text',
				array(
					'content'     => (string) get_post_field( 'post_content', $page_id ),
					'width'       => 'full',
					'boxed'       => 'yes',
					'updated'     => 'yes',
					'notice'      => isset( $def['notice'] ) ? $def['notice'] : '',
					'notice_tone' => isset( $def['notice_tone'] ) ? $def['notice_tone'] : 'info',
					'toc'         => 'yes',
					'toc_title'   => 'فهرست مطالب',
					'toc_numbering' => 'none',
					'aside_title' => 'سایر قوانین و تعهدات',
					'aside_links' => zc_elementor_repeater( $links ),
				)
			)
		);

		return zc_elementor_save( $page_id, $data );
	}
endif;

if ( ! function_exists( 'zc_build_elementor_privacy' ) ) :
	/**
	 * سازگاری با نسخه‌های قبلی: برگه حریم خصوصی همان قالب قوانین را دارد.
	 *
	 * @param int $page_id شناسه برگه.
	 * @return bool
	 */
	function zc_build_elementor_privacy( $page_id ) {
		return zc_build_elementor_legal( $page_id );
	}
endif;

if ( ! function_exists( 'zc_elementor_layout_templates' ) ) :
	/**
	 * تعریف قالب‌های سربرگ و پاورقی.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function zc_elementor_layout_templates() {
		return array(
			'header' => array(
				'title'  => __( 'سربرگ سایت (زرین‌کوچ)', 'zarincoach' ),
				'widget' => 'site-header',
				'args'   => array(
					'layout'      => 'classic',
					'menu'        => '0',
					'mobile_menu' => '0',
					'topbar'      => 'yes',
					'topbar_text' => 'نوبت‌دهی: شنبه تا چهارشنبه ۱۶ تا ۲۰:۳۰ — حضوری در بوشهر و آنلاین سراسری',
					'topbar_link' => zc_eb_url( 'booking' ),
					'show_phone'  => 'yes',
					'show_social' => 'yes',
					'show_search' => 'yes',
					'show_dark'   => 'yes',
					'cta_text'    => 'رزرو نوبت',
					'cta_url'     => zc_eb_url( 'booking' ),
					'hide_cta'    => '',
				),
			),
			'footer' => array(
				'title'  => __( 'پاورقی سایت (زرین‌کوچ)', 'zarincoach' ),
				'widget' => 'site-footer',
				'args'   => array(
					'show_socials'   => 'yes',
					'glow'           => 'yes',
					'services_title' => 'مسیرهای همراهی',
					'services_count' => 6,
					'posts_title'    => 'تازه‌ترین نوشته‌ها',
					'posts_count'    => 3,
					'contact_title'  => 'نوبت‌دهی و ارتباط',
					'menu'           => '0',
					'credit'         => 'yes',
					'show_trust'     => 'yes',
					'show_emergency' => 'yes',
					'legal_menu'     => '0',
				),
			),
		);
	}
endif;


if ( ! function_exists( 'zc_build_elementor_layout_template' ) ) :
	/**
	 * ساخت/بروزرسانی قالب سربرگ یا پاورقی در کتابخانه‌ی المنتور و اتصال آن در پنل قالب.
	 *
	 * @param string $location header|footer.
	 * @return int شناسه قالب (۰ در صورت خطا).
	 */
	function zc_build_elementor_layout_template( $location ) {
		$defs = zc_elementor_layout_templates();
		if ( empty( $defs[ $location ] ) || ! post_type_exists( 'elementor_library' ) ) {
			return 0;
		}
		$def = $defs[ $location ];

		$found = get_posts(
			array(
				'post_type'        => 'elementor_library',
				'post_status'      => 'any',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'meta_key'         => '_zc_demo_template', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'       => $location,           // phpcs:ignore WordPress.DB.SlowDBQuery
				'suppress_filters' => true,
			)
		);

		$post = array(
			'post_type'   => 'elementor_library',
			'post_status' => 'publish',
			'post_title'  => $def['title'],
			'post_author' => get_current_user_id() ? get_current_user_id() : 1,
		);
		if ( ! empty( $found ) ) {
			$post['ID'] = (int) $found[0];
			$id         = wp_update_post( wp_slash( $post ), true );
		} else {
			$id = wp_insert_post( wp_slash( $post ), true );
		}

		if ( is_wp_error( $id ) || ! $id ) {
			return 0;
		}
		$id = (int) $id;

		update_post_meta( $id, '_zc_demo_template', $location );
		if ( taxonomy_exists( 'elementor_library_type' ) ) {
			wp_set_object_terms( $id, 'section', 'elementor_library_type' );
		}

		$data = array( zc_elementor_section( zc_elementor_widget( $def['widget'], $def['args'] ) ) );
		if ( ! zc_elementor_save( $id, $data, 'section' ) ) {
			return 0;
		}

		if ( function_exists( 'zc_demo_track' ) ) {
			zc_demo_track( 'templates', $id );
		}

		// اتصال در پنل قالب.
		$options = get_option( ZC_OPT, array() );
		$options = is_array( $options ) ? $options : array();
		$options[ $location . '_template' ] = (string) $id;
		update_option( ZC_OPT, $options );
		if ( function_exists( 'zc_opt_flush' ) ) {
			zc_opt_flush();
		}

		return $id;
	}
endif;

