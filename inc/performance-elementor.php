<?php
/**
 * بهینه‌سازی سرعت المنتور در صفحاتی که با ویجت‌های اختصاصی زرین‌کوچ ساخته شده‌اند.
 *
 * - قطع بارگذاری Google Fonts المنتور (Roboto / Roboto Slab) — فونت سایت آراد و محلی است.
 * - حذف آیکن‌فونت‌های المنتور و Font Awesome وقتی صفحه از آن‌ها استفاده نمی‌کند.
 * - حذف جاوااسکریپت فرانت‌اند المنتور و در نتیجه jQuery، در صفحاتی که فقط ویجت‌های
 *   «بدون نیاز به JS» دارند (همه‌ی ویجت‌های zc- با جاوااسکریپت خالص قالب کار می‌کنند).
 *
 * تشخیص «صفحه‌ی سبک» خودکار است: داده‌ی المنتور صفحه + قالب سربرگ/پاورقی بررسی می‌شود و
 * اگر ویجت یا تنظیمی نیازمند JS المنتور (اسلایدشو، ویدئوی پس‌زمینه، انیمیشن ورود، بخش کشیده،
 * چسبان، لایت‌باکس، ویجت‌های افزونه‌های دیگر و…) پیدا شود، هیچ چیزی حذف نمی‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_el_active' ) ) :
	/**
	 * المنتور فعال است؟
	 *
	 * @return bool
	 */
	function zc_el_active() {
		return did_action( 'elementor/loaded' ) && class_exists( '\Elementor\Plugin' );
	}
endif;

/* ------------------------------------------------------------------ *
 * ۱) Google Fonts المنتور
 * ------------------------------------------------------------------ */
add_filter(
	'elementor/frontend/print_google_fonts',
	static function ( $print ) {
		return zc_switch( 'perf_el_google_fonts', true ) ? false : $print;
	}
);

/*
 * ماژول «بهینه‌سازی بارگذاری تصویر» المنتور ویژگی‌های loading/fetchpriority را بدون بررسی
 * مقدار موجود دوباره اضافه می‌کند (ویژگی تکراری و lazy شدن تصویر LCP). قالب این ویژگی‌ها را
 * برای همه‌ی تصاویر خودش دقیق تنظیم می‌کند و بقیه را هسته‌ی وردپرس مدیریت می‌کند.
 */
add_filter(
	'pre_option_elementor_optimized_image_loading',
	static function ( $value ) {
		return zc_switch( 'perf_lazy_images', true ) ? '0' : $value;
	}
);

if ( ! function_exists( 'zc_el_safe_widgets' ) ) :
	/**
	 * ویجت‌های هسته‌ی المنتور که برای نمایش به JS فرانت‌اند نیاز ندارند.
	 *
	 * @return string[]
	 */
	function zc_el_safe_widgets() {
		return (array) apply_filters(
			'zc_el_safe_widgets',
			array( 'heading', 'text-editor', 'image', 'spacer', 'divider', 'button', 'icon', 'icon-list', 'icon-box', 'image-box', 'html', 'shortcode', 'wp-widget-custom_html', 'star-rating', 'google_maps' )
		);
	}
endif;

if ( ! function_exists( 'zc_el_elements_need_js' ) ) :
	/**
	 * بررسی بازگشتی عناصر المنتور.
	 *
	 * @param array $elements عناصر.
	 * @param array $flags    پرچم‌ها (js / icons) — با ارجاع پر می‌شود.
	 * @return void
	 */
	function zc_el_elements_need_js( array $elements, array &$flags ) {
		$safe = zc_el_safe_widgets();
		foreach ( $elements as $el ) {
			if ( ! is_array( $el ) ) {
				continue;
			}
			$s    = isset( $el['settings'] ) && is_array( $el['settings'] ) ? $el['settings'] : array();
			$type = isset( $el['elType'] ) ? (string) $el['elType'] : '';

			if ( 'widget' === $type ) {
				$w = isset( $el['widgetType'] ) ? (string) $el['widgetType'] : '';
				if ( 0 !== strpos( $w, 'zc-' ) && ! in_array( $w, $safe, true ) ) {
					$flags['js'] = true;
				}
				if ( in_array( $w, array( 'icon', 'icon-list', 'icon-box', 'star-rating', 'button' ), true ) ) {
					$flags['icons'] = true;
				}
				if ( 'image' === $w && isset( $s['link_to'] ) && 'file' === $s['link_to'] ) {
					$flags['js'] = true; // لایت‌باکس.
				}
			}

			// تنظیماتی که به JS فرانت‌اند المنتور نیاز دارند.
			// «بخش کشیده» در برگه‌ها/قالب‌هایی که قالب تمام‌عرض رندر می‌کند اثری ندارد و JS لازم نیست.
			if ( ( isset( $s['stretch_section'] ) && 'section-stretched' === $s['stretch_section'] && empty( $flags['allow_stretch'] ) )
				|| ( isset( $s['background_background'] ) && in_array( $s['background_background'], array( 'slideshow', 'video' ), true ) )
				|| ( isset( $s['background_overlay_background'] ) && 'video' === $s['background_overlay_background'] )
				|| ! empty( $s['sticky'] )
			) {
				$flags['js'] = true;
			}
			foreach ( array( 'animation', '_animation', 'animation_tablet', 'animation_mobile' ) as $key ) {
				if ( ! empty( $s[ $key ] ) && 'none' !== $s[ $key ] ) {
					$flags['js'] = true;
				}
			}
			foreach ( $s as $key => $val ) {
				if ( 'yes' === $val && ( false !== strpos( (string) $key, 'motion_fx' ) ) ) {
					$flags['js'] = true;
				}
			}

			if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
				zc_el_elements_need_js( $el['elements'], $flags );
			}
			if ( ! empty( $flags['js'] ) && ! empty( $flags['icons'] ) ) {
				return;
			}
		}
	}
endif;

if ( ! function_exists( 'zc_el_document_flags' ) ) :
	/**
	 * پرچم‌های نیاز یک سند المنتور (با کش در متا بر اساس هش داده).
	 *
	 * @param int $post_id شناسه سند.
	 * @return array{js:bool,icons:bool,elementor:bool}
	 */
	function zc_el_document_flags( $post_id ) {
		static $cache = array();
		$post_id = (int) $post_id;
		if ( isset( $cache[ $post_id ] ) ) {
			return $cache[ $post_id ];
		}

		$flags = array( 'js' => false, 'icons' => false, 'elementor' => false );
		if ( $post_id && 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true ) ) {
			$raw = get_post_meta( $post_id, '_elementor_data', true );
			$raw = is_array( $raw ) ? wp_json_encode( $raw ) : (string) $raw;
			if ( '' !== $raw ) {
				$flags['elementor'] = true;
				$hash   = md5( $raw . '|' . ZC_VERSION );
				$stored = get_post_meta( $post_id, '_zc_el_flags', true );
				if ( is_array( $stored ) && isset( $stored['h'] ) && $stored['h'] === $hash ) {
					$flags['js']    = ! empty( $stored['js'] );
					$flags['icons'] = ! empty( $stored['icons'] );
				} else {
					$data = json_decode( $raw, true );
					if ( is_array( $data ) ) {
						$flags['allow_stretch'] = in_array( get_post_type( $post_id ), array( 'page', 'elementor_library' ), true );
						zc_el_elements_need_js( $data, $flags );
						unset( $flags['allow_stretch'] );
					} else {
						$flags['js'] = true;
					}
					update_post_meta(
						$post_id,
						'_zc_el_flags',
						array( 'h' => $hash, 'js' => (bool) $flags['js'], 'icons' => (bool) $flags['icons'] )
					);
				}
			}
		}
		$cache[ $post_id ] = $flags;
		return $flags;
	}
endif;

if ( ! function_exists( 'zc_el_request_flags' ) ) :
	/**
	 * پرچم‌های کل درخواست جاری (صفحه + سربرگ + پاورقی المنتور).
	 *
	 * @return array{js:bool,icons:bool,lean:bool}
	 */
	function zc_el_request_flags() {
		static $flags = null;
		if ( null !== $flags ) {
			return $flags;
		}
		$flags = array( 'js' => false, 'icons' => false, 'lean' => false );

		$docs = array();
		if ( is_singular() ) {
			$docs[] = (int) get_queried_object_id();
		}
		if ( function_exists( 'zc_layout_template_id' ) ) {
			$docs[] = (int) zc_layout_template_id( 'header' );
			$docs[] = (int) zc_layout_template_id( 'footer' );
		}
		foreach ( array_filter( array_unique( $docs ) ) as $doc ) {
			$f = zc_el_document_flags( $doc );
			$flags['js']    = $flags['js'] || $f['js'];
			$flags['icons'] = $flags['icons'] || $f['icons'];
		}
		// در بایگانی‌ها و صفحات غیرالمنتوری، فقط سربرگ/پاورقی تعیین‌کننده است.
		$flags['lean'] = ! $flags['js'];

		/**
		 * فیلتر امکان حذف JS المنتور در درخواست جاری.
		 *
		 * @param bool $lean  true یعنی JS المنتور لازم نیست.
		 * @param array $flags پرچم‌ها.
		 */
		$flags['lean'] = (bool) apply_filters( 'zc_el_lean_request', $flags['lean'], $flags );
		return $flags;
	}
endif;

if ( ! function_exists( 'zc_el_can_optimize' ) ) :
	/**
	 * آیا بهینه‌سازی در این درخواست مجاز است؟ (نه در ویرایشگر/پیش‌نمایش/مدیریت و نه برای ویرایشگران)
	 *
	 * @return bool
	 */
	function zc_el_can_optimize() {
		if ( is_admin() || wp_doing_ajax() || is_customize_preview() || ! zc_el_active() ) {
			return false;
		}
		if ( isset( $_GET['elementor-preview'] ) || isset( $_GET['elementor_library'] ) || isset( $_GET['preview'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return false;
		}
		$el = \Elementor\Plugin::$instance;
		if ( ( isset( $el->preview ) && $el->preview->is_preview_mode() ) || ( isset( $el->editor ) && $el->editor->is_edit_mode() ) ) {
			return false;
		}
		// کاربرانی که می‌توانند ویرایش کنند به نوار ابزار و دکمه‌های المنتور نیاز دارند.
		return ! current_user_can( 'edit_posts' );
	}
endif;

if ( ! function_exists( 'zc_el_dequeue_assets' ) ) :
	/**
	 * حذف فایل‌های غیرضروری المنتور.
	 *
	 * @return void
	 */
	function zc_el_dequeue_assets() {
		if ( ! zc_el_can_optimize() ) {
			return;
		}
		$flags = zc_el_request_flags();

		if ( zc_switch( 'perf_el_icons', true ) && ! $flags['icons'] && $flags['lean'] ) {
			foreach ( array( 'elementor-icons', 'elementor-icons-shared-0', 'elementor-icons-fa-solid', 'elementor-icons-fa-regular', 'elementor-icons-fa-brands', 'font-awesome', 'font-awesome-5-all', 'font-awesome-4-shim' ) as $handle ) {
				wp_dequeue_style( $handle );
			}
			wp_dequeue_script( 'font-awesome-4-shim' );
		}

		if ( zc_switch( 'perf_el_js', true ) && $flags['lean'] ) {
			foreach ( array( 'elementor-frontend', 'elementor-frontend-modules', 'elementor-webpack-runtime', 'elementor-waypoints', 'elementor-dialog', 'swiper', 'e-swiper', 'jquery-ui-core', 'share-link', 'elementor-gallery' ) as $handle ) {
				wp_dequeue_script( $handle );
			}
			// jQuery فقط وقتی حذف می‌شود که اسکریپت دیگری به آن وابسته نباشد.
			if ( ! zc_el_queue_needs( 'jquery' ) ) {
				wp_dequeue_script( 'jquery' );
			}
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_el_dequeue_assets', PHP_INT_MAX );
add_action( 'wp_footer', 'zc_el_dequeue_assets', 19 ); // المنتور بخشی از فایل‌ها را در wp_footer اضافه می‌کند.

if ( ! function_exists( 'zc_el_queue_needs' ) ) :
	/**
	 * آیا یکی از اسکریپت‌های در صف به این handle وابسته است؟
	 *
	 * @param string $dep handle وابستگی.
	 * @return bool
	 */
	function zc_el_queue_needs( $dep ) {
		$scripts = wp_scripts();
		$aliases = array( $dep, $dep . '-core', $dep . '-migrate' );
		foreach ( (array) $scripts->queue as $handle ) {
			if ( in_array( $handle, $aliases, true ) ) {
				continue;
			}
			if ( zc_el_depends_on( $handle, $aliases, $scripts, 0 ) ) {
				return true;
			}
		}
		return false;
	}
endif;

if ( ! function_exists( 'zc_el_depends_on' ) ) :
	/**
	 * بررسی بازگشتی وابستگی.
	 *
	 * @param string     $handle  handle.
	 * @param string[]   $targets اهداف.
	 * @param WP_Scripts $scripts شیء اسکریپت‌ها.
	 * @param int        $depth   عمق.
	 * @return bool
	 */
	function zc_el_depends_on( $handle, array $targets, $scripts, $depth ) {
		if ( $depth > 8 || empty( $scripts->registered[ $handle ] ) ) {
			return false;
		}
		foreach ( (array) $scripts->registered[ $handle ]->deps as $d ) {
			if ( in_array( $d, $targets, true ) || zc_el_depends_on( $d, $targets, $scripts, $depth + 1 ) ) {
				return true;
			}
		}
		return false;
	}
endif;

if ( ! function_exists( 'zc_el_recommended_settings' ) ) :
	/**
	 * تنظیمات پیشنهادی سرعت برای المنتور (هنگام نصب دمو اعمال می‌شود).
	 *
	 * @return void
	 */
	function zc_el_recommended_settings() {
		update_option( 'elementor_google_font', '0' );
		update_option( 'elementor_font_display', 'swap' );
		update_option( 'elementor_css_print_method', 'external' );
		update_option( 'elementor_load_fa4_shim', '' );
		update_option( 'elementor_optimized_image_loading', '0' );
		update_option( 'elementor_experiment-e_font_icon_svg', 'active' );
		update_option( 'elementor_disable_color_schemes', 'yes' );
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
endif;

if ( ! function_exists( 'zc_el_inline_small_css' ) ) :
	/**
	 * درون‌خطی کردن فایل‌های CSS کوچکِ تولیدی المنتور (post-*.css، base-*.css)
	 * برای حذف چند درخواستِ مسدودکننده‌ی رندر (هر فایل معمولاً کمتر از ۳ کیلوبایت است).
	 *
	 * @param string $tag    تگ link.
	 * @param string $handle شناسه.
	 * @param string $href   آدرس.
	 * @param string $media  media.
	 * @return string
	 */
	function zc_el_inline_small_css( $tag, $handle, $href, $media ) {
		if ( ! preg_match( '/^(elementor-post-\d+|base-desktop|base-mobile|base-tablet)$/', (string) $handle ) ) {
			return $tag;
		}
		if ( ! zc_switch( 'perf_el_inline_css', true ) || ! zc_el_can_optimize() ) {
			return $tag;
		}
		$uploads = wp_get_upload_dir();
		$base    = trailingslashit( set_url_scheme( $uploads['baseurl'] ) ) . 'elementor/css/';
		$href    = strtok( set_url_scheme( (string) $href ), '?' );
		if ( 0 !== strpos( $href, $base ) ) {
			return $tag;
		}
		$file = wp_normalize_path( trailingslashit( $uploads['basedir'] ) . 'elementor/css/' . basename( $href ) );
		if ( ! preg_match( '/\.css$/', $file ) || ! is_readable( $file ) || filesize( $file ) > 8192 ) {
			return $tag;
		}
		$css = (string) file_get_contents( $file ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		// آدرس‌های نسبی در CSS درون‌خطی می‌شکنند.
		if ( '' === trim( $css ) || preg_match( '/url\(\s*[\'"]?(?!data:|https?:|\/)/i', $css ) ) {
			return '' === trim( $css ) ? '' : $tag;
		}
		$media = ( '' === (string) $media || 'all' === $media ) ? '' : ' media="' . esc_attr( $media ) . '"';
		return '<style id="' . esc_attr( $handle ) . '-css"' . $media . '>' . str_replace( '</style', '<\/style', $css ) . "</style>\n";
	}
endif;
add_filter( 'style_loader_tag', 'zc_el_inline_small_css', 10, 4 );
