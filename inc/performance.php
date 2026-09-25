<?php
/**
 * بهینه‌سازی عملکرد و سرعت (Core Web Vitals)
 *
 * تمام موارد زیر بدون نیاز به افزونه‌ی جانبی انجام می‌شود و
 * از طریق پنل تنظیمات (بخش سرعت) قابل مدیریت هستند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_performance_cleanup' ) ) :
	/**
	 * حذف اسکریپت‌ها و استایل‌های غیرضروری وردپرس.
	 *
	 * @return void
	 */
	function zc_performance_cleanup() {

		/* ------------------ ایموجی ------------------ */
		if ( zc_switch( 'perf_emoji', true ) ) {
			remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
			remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
			remove_action( 'wp_print_styles', 'print_emoji_styles' );
			remove_action( 'admin_print_styles', 'print_emoji_styles' );
			remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
			remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
			remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
			add_filter( 'emoji_svg_url', '__return_false' );
			add_filter( 'tiny_mce_plugins', 'zc_disable_emoji_tinymce' );
		}

		/* ------------------ oEmbed ------------------ */
		if ( zc_switch( 'perf_embeds', true ) ) {
			add_action( 'wp_footer', 'zc_deregister_embeds' );
			add_filter( 'embed_oembed_discover', '__return_false' );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'wp_head', 'wp_oembed_add_host_js' );
		}

		/* ------------------ jQuery Migrate ------------------ */
		if ( zc_switch( 'perf_jquery_migrate', true ) && ! is_admin() ) {
			add_action( 'wp_default_scripts', 'zc_remove_jquery_migrate' );
		}

		/* ------------------ Dashicons ------------------ */
		if ( zc_switch( 'perf_dashicons', true ) && ! is_user_logged_in() ) {
			add_action( 'wp_enqueue_scripts', 'zc_remove_dashicons', 100 );
		}

		/* ------------------ الگوهای بلوکی ------------------ */
		if ( zc_switch( 'perf_block_patterns', true ) ) {
			remove_theme_support( 'core-block-patterns' );
			add_filter( 'should_load_remote_block_patterns', '__return_false' );
		}

		/* ------------------ استایل‌های گوتنبرگ ------------------ */
		if ( zc_switch( 'perf_classic_styles', true ) && ! is_admin() ) {
			add_action( 'wp_enqueue_scripts', 'zc_remove_block_library_css', 100 );
		}

		/* ------------------ پاک‌سازی head ------------------ */
		// نسخه‌ی وردپرس همیشه پنهان می‌ماند (افشای نسخه هیچ سودی ندارد).
		remove_action( 'wp_head', 'wp_generator' );
		add_filter( 'the_generator', '__return_empty_string' );

		if ( zc_switch( 'perf_head_cleanup', true ) ) {
			remove_action( 'wp_head', 'rsd_link' );
			remove_action( 'wp_head', 'wlwmanifest_link' );
			remove_action( 'wp_head', 'wp_shortlink_wp_head' );
			remove_action( 'wp_head', 'rest_output_link_wp_head' );
			remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );
			remove_action( 'template_redirect', 'rest_output_link_header', 11 );
			remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
			remove_action( 'wp_head', 'wp_resource_hints', 2 );
		}
	}
endif;
add_action( 'init', 'zc_performance_cleanup', 1 );

if ( ! function_exists( 'zc_remove_generator_tags' ) ) :
	/**
	 * حذف متاتگ‌های generator افزونه‌ها (Redux، المنتور)؛ افشای نسخه‌ها هیچ سودی برای سئو ندارد.
	 *
	 * @return void
	 */
	function zc_remove_generator_tags() {
		remove_action( 'wp_head', array( 'Redux_Functions_Ex', 'meta_tag' ) );
	}
endif;
add_action( 'wp_head', 'zc_remove_generator_tags', 0 );
add_filter(
	'pre_option_elementor_meta_generator_tag',
	static function () {
		return '1'; // «1» یعنی تگ generator المنتور غیرفعال شود.
	}
);

if ( ! function_exists( 'zc_disable_emoji_tinymce' ) ) :
	/**
	 * حذف افزونه ایموجی از تینى‌ام‌سی‌ای.
	 *
	 * @param array $plugins فهرست افزونه‌ها.
	 * @return array
	 */
	function zc_disable_emoji_tinymce( $plugins ) {
		if ( is_array( $plugins ) ) {
			return array_diff( $plugins, array( 'wpemoji' ) );
		}
		return array();
	}
endif;

if ( ! function_exists( 'zc_deregister_embeds' ) ) :
	/**
	 * حذف اسکریپت embed.
	 *
	 * @return void
	 */
	function zc_deregister_embeds() {
		wp_deregister_script( 'wp-embed' );
	}
endif;

if ( ! function_exists( 'zc_remove_jquery_migrate' ) ) :
	/**
	 * حذف jQuery Migrate.
	 *
	 * @param WP_Scripts $scripts شیء اسکریپت‌ها.
	 * @return void
	 */
	function zc_remove_jquery_migrate( $scripts ) {
		if ( ! empty( $scripts->registered['jquery'] ) ) {
			$jquery = $scripts->registered['jquery'];
			if ( ! empty( $jquery->deps ) ) {
				$jquery->deps = array_diff( $jquery->deps, array( 'jquery-migrate' ) );
			}
		}
	}
endif;

if ( ! function_exists( 'zc_remove_dashicons' ) ) :
	/**
	 * حذف Dashicons برای کاربران غیرمدیر.
	 *
	 * @return void
	 */
	function zc_remove_dashicons() {
		wp_dequeue_style( 'dashicons' );
		wp_deregister_style( 'dashicons' );
	}
endif;

if ( ! function_exists( 'zc_remove_block_library_css' ) ) :
	/**
	 * حذف استایل‌های اضافی بلوک‌ها در صفحات عادی.
	 *
	 * @return void
	 */
	function zc_remove_block_library_css() {
		// فقط در صفحاتی که با ویرایشگر بلوک ساخته نشده‌اند.
		$post_id = get_queried_object_id();
		if ( $post_id && has_blocks( $post_id ) ) {
			return;
		}

		wp_dequeue_style( 'core-block-supports' );
		wp_dequeue_style( 'wp-block-library-theme' );
		wp_dequeue_style( 'classic-theme-styles' );
		wp_dequeue_style( 'global-styles' );
	}
endif;

if ( ! function_exists( 'zc_limit_revisions' ) ) :
	/**
	 * محدود کردن تعداد نسخه‌ها.
	 *
	 * @param int     $num تعداد پیش‌فرض.
	 * @param WP_Post $post نوشته.
	 * @return int
	 */
	function zc_limit_revisions( $num, $post ) {
		$limit = (int) zc_opt( 'perf_revisions', 5 );
		return $limit > 0 ? $limit : 0;
	}
endif;
add_filter( 'wp_revisions_to_keep', 'zc_limit_revisions', 10, 2 );

if ( ! function_exists( 'zc_autosave_interval' ) ) :
	/**
	 * تغییر فاصله ذخیره خودکار.
	 *
	 * @param int $seconds فاصله پیش‌فرض.
	 * @return int
	 */
	function zc_autosave_interval( $seconds ) {
		$value = (int) zc_opt( 'perf_autosave', 180 );
		return $value >= 60 ? $value : $seconds;
	}
endif;
add_filter( 'autosave_interval', 'zc_autosave_interval' );

if ( ! function_exists( 'zc_heartbeat_settings' ) ) :
	/**
	 * مدیریت ضربان قلب وردپرس.
	 *
	 * @param array $settings تنظیمات.
	 * @return array
	 */
	function zc_heartbeat_settings( $settings ) {
		$mode = (string) zc_opt( 'perf_heartbeat', 'optimized' );

		if ( 'disabled' === $mode ) {
			$settings['interval'] = 120; // حداکثر مقدار مجاز وردپرس.
			return $settings;
		}

		if ( 'optimized' === $mode ) {
			$settings['interval'] = 60;
		}

		return $settings;
	}
endif;
add_filter( 'heartbeat_settings', 'zc_heartbeat_settings' );

if ( ! function_exists( 'zc_heartbeat_frontend' ) ) :
	/**
	 * حذف ضربان قلب از بخش عمومی سایت برای بازدیدکنندگان (بدون شکستن وابستگی‌های پیشخوان).
	 *
	 * @return void
	 */
	function zc_heartbeat_frontend() {
		$mode = (string) zc_opt( 'perf_heartbeat', 'optimized' );
		if ( 'default' === $mode || is_user_logged_in() ) {
			return;
		}
		wp_dequeue_script( 'heartbeat' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_heartbeat_frontend', 100 );

if ( ! function_exists( 'zc_remove_version_query' ) ) :
	/**
	 * حذف رشته‌ی نسخه از منابع (فقط زمانی که برابر نسخه وردپرس است).
	 *
	 * @param string $src آدرس منبع.
	 * @return string
	 */
	function zc_remove_version_query( $src ) {
		if ( ! zc_switch( 'perf_query_strings', true ) ) {
			return $src;
		}

		if ( $src && false !== strpos( $src, 'ver=' . get_bloginfo( 'version' ) ) ) {
			$src = remove_query_arg( 'ver', $src );
		}

		return $src;
	}
endif;
add_filter( 'style_loader_src', 'zc_remove_version_query', 20 );
add_filter( 'script_loader_src', 'zc_remove_version_query', 20 );

if ( ! function_exists( 'zc_lazy_load_content_media' ) ) :
	/**
	 * افزودن ویژگی‌های بارگذاری تنبل به تصاویر محتوا.
	 *
	 * @param string $content محتوا.
	 * @return string
	 */
	function zc_lazy_load_content_media( $content ) {
		if ( ! zc_switch( 'perf_lazy_images', true ) || is_admin() || is_feed() ) {
			return $content;
		}

		$content = preg_replace_callback(
			'/<img([^>]+)>/i',
			static function ( $matches ) {
				$img = $matches[0];

				// تصاویر LCP (fetchpriority=high) یا دارای loading صریح دست نمی‌خورند.
				if ( preg_match( '/\sloading\s*=|\sfetchpriority\s*=\s*["\']?high|data-no-lazy|\sskip-lazy/i', $img ) ) {
					return $img;
				}

				$add = ' loading="lazy"';
				if ( ! preg_match( '/\sdecoding\s*=/i', $img ) ) {
					$add .= ' decoding="async"';
				}
				return preg_replace( '/^<img/i', '<img' . $add, $img, 1 );
			},
			(string) $content
		);

		$content = preg_replace_callback(
			'/<iframe([^>]+)>/i',
			static function ( $matches ) {
				$iframe = $matches[0];

				if ( preg_match( '/\sloading\s*=/i', $iframe ) ) {
					return $iframe;
				}

				return preg_replace( '/^<iframe/i', '<iframe loading="lazy"', $iframe, 1 );
			},
			(string) $content
		);

		return $content;
	}
endif;
add_filter( 'the_content', 'zc_lazy_load_content_media', 99 );
add_filter( 'post_thumbnail_html', 'zc_lazy_load_content_media', 99 );

if ( ! function_exists( 'zc_disable_jetpack_devicepx' ) ) :
	/**
	 * غیرفعال‌سازی اسکریپت‌های اضافی برخی افزونه‌های رایج.
	 *
	 * @return void
	 */
	function zc_disable_jetpack_devicepx() {
		wp_dequeue_script( 'devicepx' );
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_disable_jetpack_devicepx', 100 );

if ( ! function_exists( 'zc_webp_supported' ) ) :
	/**
	 * پشتیبانی ویرایشگر تصویر سرور از WebP (با کش).
	 *
	 * @return bool
	 */
	function zc_webp_supported() {
		static $ok = null;
		if ( null === $ok ) {
			$ok = (bool) wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
		}
		return $ok;
	}
endif;

if ( ! function_exists( 'zc_webp_output_format' ) ) :
	/**
	 * ساخت اندازه‌های فرعی تصاویر JPEG/PNG به‌صورت WebP (فایل اصلی دست‌نخورده می‌ماند).
	 *
	 * @param array $formats نگاشت فرمت‌ها.
	 * @return array
	 */
	function zc_webp_output_format( $formats ) {
		if ( ! zc_switch( 'perf_webp', true ) || ! zc_webp_supported() ) {
			return $formats;
		}
		$formats = (array) $formats;
		foreach ( array( 'image/jpeg', 'image/png' ) as $mime ) {
			if ( empty( $formats[ $mime ] ) ) {
				$formats[ $mime ] = 'image/webp';
			}
		}
		return $formats;
	}
endif;
add_filter( 'image_editor_output_format', 'zc_webp_output_format' );

if ( ! function_exists( 'zc_webp_quality' ) ) :
	/**
	 * کیفیت متعادل WebP/JPEG برای اندازه‌های فرعی.
	 *
	 * @param int    $quality   کیفیت.
	 * @param string $mime_type نوع.
	 * @return int
	 */
	function zc_webp_quality( $quality, $mime_type = '' ) {
		return 'image/webp' === $mime_type ? 80 : $quality;
	}
endif;
add_filter( 'wp_editor_set_quality', 'zc_webp_quality', 10, 2 );

if ( ! function_exists( 'zc_speculation_rules' ) ) :
	/**
	 * پیکربندی قوانین حدس‌زنی بومی وردپرس (۶٫۸+) برای بازشدن تقریباً آنی صفحات.
	 *
	 * prefetch (نه prerender) تا اسکریپت‌های آمار دوبار اجرا نشوند؛ moderate = با هاور/لمس.
	 *
	 * @param array|null $config پیکربندی فعلی.
	 * @return array|null
	 */
	function zc_speculation_rules( $config ) {
		if ( ! is_array( $config ) || ! zc_switch( 'perf_instant_pages', true ) || is_user_logged_in() ) {
			return $config;
		}
		return array(
			'mode'      => 'prefetch',
			'eagerness' => 'moderate',
		);
	}
endif;
add_filter( 'wp_speculation_rules_configuration', 'zc_speculation_rules' );

if ( ! function_exists( 'zc_speculation_exclude' ) ) :
	/**
	 * مسیرهایی که نباید پیش‌واکشی شوند (پرس‌وجوها را خود وردپرس مستثنا می‌کند).
	 *
	 * @param string[] $paths مسیرها.
	 * @return string[]
	 */
	function zc_speculation_exclude( $paths ) {
		$paths[] = '/wp-json/*';
		return $paths;
	}
endif;
add_filter( 'wp_speculation_rules_href_exclude_paths', 'zc_speculation_exclude' );

if ( ! function_exists( 'zc_separate_block_assets' ) ) :
	/**
	 * بارگذاری جداگانه‌ی استایل بلوک‌ها به‌جای فایل کامل block-library (~۱۳۷KB).
	 *
	 * المنتور بافر خروجی وردپرس ۶٫۹+ را خاموش می‌کند، پس وردپرس نمی‌تواند استایل بلوک‌ها را خودش
	 * به head منتقل کند؛ zc_enqueue_used_block_styles() همین کار را برای محتوای نوشته انجام می‌دهد.
	 *
	 * @param bool $load مقدار فعلی.
	 * @return bool
	 */
	function zc_separate_block_assets( $load ) {
		if ( is_admin() || ! zc_switch( 'perf_classic_styles', true ) ) {
			return $load;
		}
		return true;
	}
endif;
add_filter( 'should_load_separate_core_block_assets', 'zc_separate_block_assets', 20 );

if ( ! function_exists( 'zc_collect_block_names' ) ) :
	/**
	 * نام همه‌ی بلوک‌های به‌کاررفته (شامل تودرتو و بلوک‌های قابل استفاده‌ی مجدد).
	 *
	 * @param array    $blocks بلوک‌های parse_blocks.
	 * @param string[] $names  نام‌های جمع‌شده.
	 * @param int      $depth  عمق فعلی (جلوگیری از حلقه).
	 * @return string[]
	 */
	function zc_collect_block_names( array $blocks, array $names = array(), $depth = 0 ) {
		if ( $depth > 8 ) {
			return $names;
		}
		foreach ( $blocks as $block ) {
			if ( empty( $block['blockName'] ) ) {
				continue;
			}
			$names[ $block['blockName'] ] = true;
			if ( 'core/block' === $block['blockName'] && ! empty( $block['attrs']['ref'] ) ) {
				$ref = get_post( (int) $block['attrs']['ref'] );
				if ( $ref && 'wp_block' === $ref->post_type ) {
					$names = zc_collect_block_names( parse_blocks( $ref->post_content ), $names, $depth + 1 );
				}
			}
			if ( ! empty( $block['innerBlocks'] ) ) {
				$names = zc_collect_block_names( $block['innerBlocks'], $names, $depth + 1 );
			}
		}
		return $names;
	}
endif;

if ( ! function_exists( 'zc_enqueue_used_block_styles' ) ) :
	/**
	 * استایل همان بلوک‌هایی که در نوشته‌ی جاری استفاده شده‌اند، در head (بدون پرش چیدمان).
	 *
	 * @return void
	 */
	function zc_enqueue_used_block_styles() {
		if ( ! is_singular() || ! wp_should_load_separate_core_block_assets() ) {
			return;
		}
		$post = get_queried_object();
		if ( ! $post instanceof WP_Post || ! has_blocks( $post->post_content ) ) {
			return;
		}
		$registry = WP_Block_Type_Registry::get_instance();
		foreach ( array_keys( zc_collect_block_names( parse_blocks( $post->post_content ) ) ) as $name ) {
			$type = $registry->get_registered( $name );
			if ( ! $type ) {
				continue;
			}
			foreach ( (array) $type->style_handles as $handle ) {
				if ( wp_style_is( $handle, 'registered' ) ) {
					wp_enqueue_style( $handle );
				}
			}
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_enqueue_used_block_styles', 20 );

if ( ! function_exists( 'zc_fallback_favicon' ) ) :
	/**
	 * نشانک پیش‌فرض قالب (SVG سبک) تا زمانی که «نشانک سایت» در سفارشی‌ساز تنظیم نشده است؛ جلوگیری از درخواست ۴۰۴ به ‎/favicon.ico.
	 *
	 * @return void
	 */
	function zc_fallback_favicon() {
		if ( has_site_icon() ) {
			return;
		}
		printf( '<link rel="icon" href="%s" type="image/svg+xml">' . "\n", esc_url( ZC_URI . '/assets/img/favicon.svg' ) );
	}
endif;
add_action( 'wp_head', 'zc_fallback_favicon', 99 );
