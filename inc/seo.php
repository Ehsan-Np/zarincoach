<?php
/**
 * سیستم سئو داخلی: عنوان، توضیح متا، robots، canonical، اوپن‌گراف/توییتر،
 * ریدایرکت صفحات کم‌ارزش و بهینه‌سازی نقشه‌ی سایت وردپرس.
 *
 * گراف اسکیما در inc/seo-schema.php و جعبه‌ی سئوی هر برگه در inc/seo-metabox.php است.
 * با فعال بودن افزونه‌های سئو (Yoast / Rank Math / AIOSEO / SEOPress / The SEO Framework)
 * همه‌چیز خودکار غیرفعال می‌شود تا خروجی تکراری ایجاد نشود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_seo_active' ) ) :
	/**
	 * آیا سیستم سئو داخلی فعال است؟
	 *
	 * @return bool
	 */
	function zc_seo_active() {
		static $active = null;
		if ( null !== $active ) {
			return $active;
		}
		if ( ! zc_switch( 'seo_enable', true ) ) {
			$active = false;
			return $active;
		}

		$third_party = defined( 'WPSEO_VERSION' )               // Yoast SEO.
			|| function_exists( 'rank_math' )                   // Rank Math.
			|| class_exists( 'RankMath\Helper' )
			|| defined( 'AIOSEO_VERSION' )                      // All in One SEO.
			|| defined( 'SEOPRESS_VERSION' )                    // SEOPress.
			|| defined( 'THE_SEO_FRAMEWORK_VERSION' )           // The SEO Framework.
			|| class_exists( 'The_SEO_Framework\Builders' );

		/**
		 * فیلتر فعال بودن سئو داخلی.
		 *
		 * @param bool $active وضعیت فعال بودن.
		 */
		$active = (bool) apply_filters( 'zc_seo_active', ! $third_party );
		return $active;
	}
endif;

/* =========================================================================
 * ابزارها
 * ========================================================================= */

if ( ! function_exists( 'zc_seo_clean' ) ) :
	/**
	 * متن ساده، بدون شورت‌کد/تگ و فاصله‌ی اضافه، بریده‌شده روی مرز واژه.
	 *
	 * @param string $text متن.
	 * @param int    $max  حداکثر نویسه (۰ = بدون برش).
	 * @return string
	 */
	function zc_seo_clean( $text, $max = 0 ) {
		$text = strip_shortcodes( (string) $text );
		$text = wp_strip_all_tags( $text, true );
		$text = html_entity_decode( $text, ENT_QUOTES, 'UTF-8' );
		$text = trim( preg_replace( '/\s+/u', ' ', $text ) );
		if ( $max > 0 && mb_strlen( $text ) > $max ) {
			$cut  = mb_substr( $text, 0, $max - 1 );
			$last = mb_strrpos( $cut, ' ' );
			if ( false !== $last && $last > $max * 0.6 ) {
				$cut = mb_substr( $cut, 0, $last );
			}
			$text = rtrim( $cut, " \t\n\r\0\x0B،,.;:؛-–" ) . '…';
		}
		return $text;
	}
endif;

if ( ! function_exists( 'zc_seo_phone_e164' ) ) :
	/**
	 * تبدیل شماره تلفن ایران به قالب بین‌المللی E.164 (مثال: ۰۹۱۷… ← ‎+98917…).
	 *
	 * @param string $phone شماره.
	 * @return string
	 */
	function zc_seo_phone_e164( $phone ) {
		$digits = preg_replace( '/\D+/', '', zc_digits_to_latin( (string) $phone ) );
		if ( '' === $digits ) {
			return '';
		}
		if ( 0 === strpos( $digits, '0098' ) ) {
			$digits = substr( $digits, 4 );
		} elseif ( 0 === strpos( $digits, '98' ) && strlen( $digits ) >= 12 ) {
			$digits = substr( $digits, 2 );
		} elseif ( 0 === strpos( $digits, '0' ) ) {
			$digits = substr( $digits, 1 );
		}
		return '+98' . $digits;
	}
endif;

if ( ! function_exists( 'zc_seo_price_irr' ) ) :
	/**
	 * استخراج مبلغ از متن قیمت («۸۵۰ هزار تومان»، «۳٫۲ میلیون تومان»، «650000 ریال») به ریال (واحد ISO: IRR).
	 *
	 * @param string $text متن قیمت.
	 * @return int ۰ یعنی قابل استخراج نیست (مثلاً «رایگان» یا «تماس بگیرید»).
	 */
	function zc_seo_price_irr( $text ) {
		$text = zc_digits_to_latin( (string) $text );
		$text = str_replace( array( '٫', '/' ), '.', $text );
		$text = str_replace( array( ',', '٬', '،' ), '', $text );
		if ( ! preg_match( '/(\d+(?:\.\d+)?)/', $text, $m ) ) {
			return 0;
		}
		$num = (float) $m[1];
		if ( false !== mb_strpos( $text, 'میلیون' ) ) {
			$num *= 1000000;
		} elseif ( false !== mb_strpos( $text, 'هزار' ) ) {
			$num *= 1000;
		}
		// پیش‌فرض واحد تومان است؛ فقط اگر «ریال» صریح باشد تبدیل نمی‌شود.
		if ( false === mb_strpos( $text, 'ریال' ) ) {
			$num *= 10;
		}
		return (int) round( $num );
	}
endif;

if ( ! function_exists( 'zc_seo_image' ) ) :
	/**
	 * داده‌ی تصویر برای اوپن‌گراف/اسکیما: آدرس، ابعاد واقعی، alt و نوع.
	 * اگر فایل اصلی JPEG/PNG موجود باشد (پیش از تبدیل WebP) همان استفاده می‌شود تا
	 * پیش‌نمایش در همه‌ی پیام‌رسان‌ها درست باشد.
	 *
	 * @param int|array|string $src شناسه، آرایه‌ی رسانه یا آدرس.
	 * @return array{url:string,width:int,height:int,alt:string,type:string,id:int}|array{}
	 */
	function zc_seo_image( $src ) {
		$id = function_exists( 'zc_image_id' ) ? zc_image_id( $src ) : ( is_numeric( $src ) ? (int) $src : 0 );
		if ( $id && wp_attachment_is_image( $id ) ) {
			$url  = '';
			$w    = 0;
			$h    = 0;
			$path = function_exists( 'wp_get_original_image_path' ) ? (string) wp_get_original_image_path( $id ) : '';
			if ( '' !== $path && preg_match( '/\.(jpe?g|png)$/i', $path ) && is_readable( $path ) ) {
				$size = wp_getimagesize( $path );
				if ( $size ) {
					$url = (string) wp_get_original_image_url( $id );
					$w   = (int) $size[0];
					$h   = (int) $size[1];
				}
			}
			if ( '' === $url ) {
				$img = wp_get_attachment_image_src( $id, 'full' );
				if ( $img ) {
					$url = (string) $img[0];
					$w   = (int) $img[1];
					$h   = (int) $img[2];
				}
			}
			if ( '' !== $url ) {
				$ft = wp_check_filetype( $url );
				return array(
					'id'     => $id,
					'url'    => $url,
					'width'  => $w,
					'height' => $h,
					'alt'    => trim( (string) get_post_meta( $id, '_wp_attachment_image_alt', true ) ),
					'type'   => (string) $ft['type'],
				);
			}
		}
		$url = is_array( $src ) ? ( isset( $src['url'] ) ? (string) $src['url'] : '' ) : ( is_numeric( $src ) ? '' : (string) $src );
		if ( '' === $url ) {
			return array();
		}
		$ft = wp_check_filetype( strtok( $url, '?' ) );
		return array(
			'id'     => 0,
			'url'    => $url,
			'width'  => 0,
			'height' => 0,
			'alt'    => '',
			'type'   => (string) $ft['type'],
		);
	}
endif;

if ( ! function_exists( 'zc_seo_post_meta' ) ) :
	/**
	 * فراداده‌ی سئوی یک نوشته/برگه (جعبه‌ی «سئو»).
	 *
	 * @param string   $key     title | desc | noindex | canonical | schema.
	 * @param int|null $post_id شناسه.
	 * @return string
	 */
	function zc_seo_post_meta( $key, $post_id = null ) {
		$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
		return $post_id ? trim( (string) get_post_meta( $post_id, '_zc_seo_' . $key, true ) ) : '';
	}
endif;

if ( ! function_exists( 'zc_seo_paged' ) ) :
	/**
	 * شماره‌ی صفحه‌ی جاری در بایگانی‌ها/نوشته‌های چندصفحه‌ای.
	 *
	 * @return int
	 */
	function zc_seo_paged() {
		return max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	}
endif;

if ( ! function_exists( 'zc_seo_person_name' ) ) :
	/**
	 * نام شخص صاحب سایت.
	 *
	 * @return string
	 */
	function zc_seo_person_name() {
		$name = trim( (string) zc_opt( 'seo_person_name', '' ) );
		return '' !== $name ? $name : get_bloginfo( 'name' );
	}
endif;

/* =========================================================================
 * عنوان
 * ========================================================================= */

if ( ! function_exists( 'zc_document_title' ) ) :
	/**
	 * عنوان بهینه‌ی هر صفحه.
	 *
	 * @param string $title عنوان پیش‌فرض.
	 * @return string
	 */
	function zc_document_title( $title ) {
		if ( ! zc_seo_active() ) {
			return $title;
		}

		$site  = get_bloginfo( 'name' );
		$sep   = ' ' . (string) zc_opt( 'seo_title_sep', '|' ) . ' ';
		$paged = zc_seo_paged();
		$page  = $paged > 1 ? sprintf( /* translators: %s: شماره صفحه */ __( 'صفحه %s', 'zarincoach' ), zc_digits_to_persian( (string) $paged ) ) : '';
		$parts = array();

		if ( is_front_page() ) {
			$custom = zc_seo_post_meta( 'title' );
			if ( '' === $custom ) {
				$custom = trim( (string) zc_opt( 'seo_home_title', '' ) );
			}
			$base = '' !== $custom ? $custom : $site . $sep . get_bloginfo( 'description' );
			return '' !== $page ? $base . $sep . $page : $base;
		}

		if ( is_singular() || ( is_home() && get_option( 'page_for_posts' ) ) ) {
			$post_id = is_home() ? (int) get_option( 'page_for_posts' ) : (int) get_queried_object_id();
			$custom  = zc_seo_post_meta( 'title', $post_id );
			if ( '' !== $custom ) {
				return '' !== $page ? $custom . $sep . $page : $custom;
			}
			$parts[] = zc_seo_clean( get_the_title( $post_id ) );
		} elseif ( is_home() ) {
			$parts[] = __( 'وبلاگ', 'zarincoach' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$parts[] = single_term_title( '', false );
		} elseif ( is_post_type_archive() ) {
			$parts[] = post_type_archive_title( '', false );
		} elseif ( is_author() ) {
			$parts[] = get_the_author_meta( 'display_name', (int) get_query_var( 'author' ) );
		} elseif ( is_search() ) {
			/* translators: %s: عبارت جستجو */
			$parts[] = sprintf( __( 'جستجو برای «%s»', 'zarincoach' ), get_search_query( false ) );
		} elseif ( is_404() ) {
			$parts[] = __( 'صفحه یافت نشد', 'zarincoach' );
		} elseif ( is_year() ) {
			$parts[] = get_the_date( 'Y' );
		} elseif ( is_month() ) {
			$parts[] = get_the_date( 'F Y' );
		} elseif ( is_day() ) {
			$parts[] = get_the_date();
		}

		$parts[] = $page;
		$parts[] = $site;

		return implode( $sep, array_filter( array_map( 'trim', $parts ) ) );
	}
endif;
add_filter( 'pre_get_document_title', 'zc_document_title', 20 );

/* =========================================================================
 * توضیح متا
 * ========================================================================= */

if ( ! function_exists( 'zc_meta_description' ) ) :
	/**
	 * توضیح متای صفحه‌ی جاری (حداکثر ۱۵۵ نویسه).
	 *
	 * @return string
	 */
	function zc_meta_description() {
		static $desc = null;
		if ( null !== $desc ) {
			return $desc;
		}
		$desc = '';

		if ( is_front_page() ) {
			$desc = zc_seo_post_meta( 'desc' );
			if ( '' === $desc ) {
				$desc = (string) zc_opt( 'seo_home_desc', '' );
			}
		} elseif ( is_singular() || ( is_home() && get_option( 'page_for_posts' ) ) ) {
			$post_id = is_home() ? (int) get_option( 'page_for_posts' ) : (int) get_queried_object_id();
			$desc    = zc_seo_post_meta( 'desc', $post_id );
			if ( '' === $desc && has_excerpt( $post_id ) ) {
				$desc = get_post_field( 'post_excerpt', $post_id );
			}
			if ( '' === trim( $desc ) && ! ( function_exists( 'zc_page_uses_elementor' ) && zc_page_uses_elementor( $post_id ) ) ) {
				$desc = get_post_field( 'post_content', $post_id );
			}
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$desc = term_description();
			if ( '' === trim( wp_strip_all_tags( (string) $desc ) ) ) {
				/* translators: 1: نام دسته/برچسب 2: نام سایت */
				$desc = sprintf( __( 'مقاله‌های «%1$s» در وب‌سایت %2$s: آموزش‌های کاربردی روان‌شناسی برای شناخت و تغییر الگوهای ذهنی و رفتاری.', 'zarincoach' ), single_term_title( '', false ), zc_seo_person_name() );
			}
		} elseif ( is_post_type_archive( 'zc_service' ) ) {
			/* translators: %s: نام شخص */
			$desc = sprintf( __( 'خدمات %s: ارزیابی و جلسات فردی، بسته‌های همراهی و دوره‌های آموزشی؛ حضوری در بوشهر و آنلاین.', 'zarincoach' ), zc_seo_person_name() );
		} elseif ( is_home() ) {
			$desc = (string) zc_opt( 'seo_home_desc', '' );
		}

		if ( '' === trim( wp_strip_all_tags( (string) $desc ) ) && ! is_search() && ! is_404() ) {
			$desc = (string) zc_opt( 'seo_person_desc', get_bloginfo( 'description' ) );
		}

		$desc = zc_seo_clean( $desc, 155 );

		/**
		 * فیلتر توضیح متا.
		 *
		 * @param string $desc توضیح.
		 */
		$desc = (string) apply_filters( 'zc_meta_description', $desc );
		return $desc;
	}
endif;

/* =========================================================================
 * canonical
 * ========================================================================= */

if ( ! function_exists( 'zc_canonical_url' ) ) :
	/**
	 * آدرس canonical صفحه‌ی جاری (بدون پارامترهای ردیابی).
	 *
	 * @return string خالی یعنی canonical درج نشود (جستجو، ۴۰۴).
	 */
	function zc_canonical_url() {
		static $url = null;
		if ( null !== $url ) {
			return $url;
		}
		$url   = '';
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		$base  = '';

		if ( is_search() || is_404() ) {
			return $url;
		}

		if ( is_singular() ) {
			$custom = zc_seo_post_meta( 'canonical' );
			if ( '' !== $custom ) {
				$url = esc_url_raw( $custom );
				return $url;
			}
			$url = is_front_page() ? home_url( '/' ) : (string) wp_get_canonical_url( get_queried_object_id() );
			return $url;
		}

		if ( is_front_page() ) {
			$base = home_url( '/' );
		} elseif ( is_home() ) {
			$pfp  = (int) get_option( 'page_for_posts' );
			$base = $pfp ? get_permalink( $pfp ) : home_url( '/' );
		} elseif ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			$link = $term instanceof WP_Term ? get_term_link( $term ) : '';
			$base = is_wp_error( $link ) ? '' : (string) $link;
		} elseif ( is_post_type_archive() ) {
			$type = get_query_var( 'post_type' );
			$base = (string) get_post_type_archive_link( is_array( $type ) ? reset( $type ) : $type );
		} elseif ( is_author() ) {
			$base = get_author_posts_url( (int) get_query_var( 'author' ) );
		} elseif ( is_year() ) {
			$base = get_year_link( (int) get_query_var( 'year' ) );
		} elseif ( is_month() ) {
			$base = get_month_link( (int) get_query_var( 'year' ), (int) get_query_var( 'monthnum' ) );
		} elseif ( is_day() ) {
			$base = get_day_link( (int) get_query_var( 'year' ), (int) get_query_var( 'monthnum' ), (int) get_query_var( 'day' ) );
		}

		if ( '' === $base ) {
			return $url;
		}
		if ( $paged > 1 ) {
			global $wp_rewrite;
			$base = $wp_rewrite->using_permalinks()
				? user_trailingslashit( trailingslashit( $base ) . $wp_rewrite->pagination_base . '/' . $paged, 'paged' )
				: add_query_arg( 'paged', $paged, $base );
		}
		$url = $base;
		return $url;
	}
endif;

/* =========================================================================
 * robots (از طریق API هسته‌ی وردپرس ← یک متای robots واحد)
 * ========================================================================= */

if ( ! function_exists( 'zc_seo_robots' ) ) :
	/**
	 * تنظیم دستورات robots.
	 *
	 * @param array $robots دستورات.
	 * @return array
	 */
	function zc_seo_robots( $robots ) {
		if ( ! zc_seo_active() || is_admin() ) {
			return $robots;
		}
		$noindex = is_search() || is_404()
			|| is_singular( 'elementor_library' )
			|| ( is_singular() && '1' === zc_seo_post_meta( 'noindex' ) )
			|| ( is_tag() && zc_switch( 'seo_noindex_tags', true ) )
			|| ( is_date() && zc_switch( 'seo_noindex_date', true ) )
			|| is_author() && zc_switch( 'seo_redirect_author', true );

		/**
		 * فیلتر noindex صفحه‌ی جاری.
		 *
		 * @param bool $noindex وضعیت.
		 */
		$noindex = (bool) apply_filters( 'zc_seo_noindex', $noindex );

		if ( $noindex || ! empty( $robots['noindex'] ) ) {
			unset( $robots['max-image-preview'], $robots['max-snippet'], $robots['max-video-preview'], $robots['index'] );
			$robots['noindex'] = true;
			$robots['follow']  = true;
			return $robots;
		}

		$robots['index']             = true;
		$robots['follow']            = true;
		$robots['max-image-preview'] = 'large';
		$robots['max-snippet']       = '-1';
		$robots['max-video-preview'] = '-1';
		return $robots;
	}
endif;
add_filter( 'wp_robots', 'zc_seo_robots', 20 );

/* =========================================================================
 * متاتگ‌ها و اوپن‌گراف
 * ========================================================================= */

if ( ! function_exists( 'zc_seo_share_image' ) ) :
	/**
	 * تصویر اشتراک‌گذاری صفحه‌ی جاری: تصویر شاخص ← تصویر سئوی تنظیمات ← تصویر پروفایل.
	 *
	 * @return array
	 */
	function zc_seo_share_image() {
		static $img = null;
		if ( null !== $img ) {
			return $img;
		}
		$img = array();
		if ( is_singular() && has_post_thumbnail( get_queried_object_id() ) ) {
			$img = zc_seo_image( (int) get_post_thumbnail_id( get_queried_object_id() ) );
		}
		if ( empty( $img ) ) {
			$img = zc_seo_image( (array) zc_opt( 'seo_og_image', array() ) );
		}
		if ( empty( $img ) ) {
			$img = zc_seo_image( (array) zc_opt( 'seo_person_image', array() ) );
		}
		if ( ! empty( $img ) && '' === $img['alt'] ) {
			$img['alt'] = is_singular() ? zc_seo_clean( get_the_title( get_queried_object_id() ) ) : zc_seo_person_name();
		}
		return $img;
	}
endif;

if ( ! function_exists( 'zc_output_meta_tags' ) ) :
	/**
	 * چاپ متاتگ‌ها در head.
	 *
	 * @return void
	 */
	function zc_output_meta_tags() {
		if ( ! zc_seo_active() ) {
			return;
		}

		$description = zc_meta_description();
		$title       = wp_get_document_title();
		$canonical   = zc_canonical_url();
		$site_name   = get_bloginfo( 'name' );
		$is_article  = is_singular( 'post' );
		$image       = zc_seo_share_image();
		$twitter     = ltrim( trim( (string) zc_opt( 'seo_twitter', '' ) ), '@' );
		$url         = '' !== $canonical ? $canonical : home_url( add_query_arg( array() ) );

		$tags = array();
		if ( '' !== $description ) {
			$tags[] = array( 'name', 'description', $description );
		}
		if ( $is_article ) {
			$tags[] = array( 'name', 'author', zc_switch( 'seo_author_person', true ) ? zc_seo_person_name() : get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', get_queried_object_id() ) ) );
		}

		// Open Graph.
		$tags[] = array( 'property', 'og:locale', 'fa_IR' );
		$og_type = $is_article ? 'article' : ( is_singular() && 'ProfilePage' === zc_schema_page_type() ? 'profile' : 'website' );
		$tags[]  = array( 'property', 'og:type', $og_type );
		$tags[] = array( 'property', 'og:site_name', $site_name );
		$tags[] = array( 'property', 'og:title', $title );
		if ( '' !== $description ) {
			$tags[] = array( 'property', 'og:description', $description );
		}
		$tags[] = array( 'property', 'og:url', $url );
		if ( ! empty( $image ) ) {
			$tags[] = array( 'property', 'og:image', $image['url'] );
			if ( 0 === strpos( $image['url'], 'https://' ) ) {
				$tags[] = array( 'property', 'og:image:secure_url', $image['url'] );
			}
			if ( $image['width'] && $image['height'] ) {
				$tags[] = array( 'property', 'og:image:width', (string) $image['width'] );
				$tags[] = array( 'property', 'og:image:height', (string) $image['height'] );
			}
			if ( '' !== $image['type'] ) {
				$tags[] = array( 'property', 'og:image:type', $image['type'] );
			}
			$tags[] = array( 'property', 'og:image:alt', $image['alt'] );
		}
		if ( 'profile' === $og_type ) {
			$name   = explode( ' ', trim( (string) zc_opt( 'seo_person_alt', '' ) ), 2 );
			$tags[] = array( 'property', 'profile:first_name', isset( $name[0] ) ? $name[0] : '' );
			$tags[] = array( 'property', 'profile:last_name', isset( $name[1] ) ? $name[1] : '' );
			$tags[] = array( 'property', 'profile:gender', 'female' );
		}
		if ( $is_article ) {
			$post_id = get_queried_object_id();
			$tags[]  = array( 'property', 'article:published_time', get_post_time( 'c', true, $post_id ) );
			$tags[]  = array( 'property', 'article:modified_time', get_post_modified_time( 'c', true, $post_id ) );
			$cats    = get_the_category( $post_id );
			if ( ! empty( $cats ) ) {
				$tags[] = array( 'property', 'article:section', $cats[0]->name );
			}
			foreach ( array_slice( (array) get_the_tags( $post_id ), 0, 6 ) as $tag ) {
				if ( $tag instanceof WP_Term ) {
					$tags[] = array( 'property', 'article:tag', $tag->name );
				}
			}
		}

		// X / Twitter.
		$tags[] = array( 'name', 'twitter:card', ! empty( $image ) ? 'summary_large_image' : 'summary' );
		$tags[] = array( 'name', 'twitter:title', $title );
		if ( '' !== $description ) {
			$tags[] = array( 'name', 'twitter:description', $description );
		}
		if ( ! empty( $image ) ) {
			$tags[] = array( 'name', 'twitter:image', $image['url'] );
			$tags[] = array( 'name', 'twitter:image:alt', $image['alt'] );
		}
		if ( '' !== $twitter ) {
			$tags[] = array( 'name', 'twitter:site', '@' . $twitter );
		}
		if ( $is_article ) {
			$tags[] = array( 'name', 'twitter:label1', __( 'زمان مطالعه', 'zarincoach' ) );
			/* translators: %s: دقیقه */
			$tags[] = array( 'name', 'twitter:data1', sprintf( __( '%s دقیقه', 'zarincoach' ), zc_digits_to_persian( (string) zc_reading_time( get_queried_object_id() ) ) ) );
		}

		/**
		 * فیلتر متاتگ‌ها. هر آیتم: [ نوع ویژگی (name|property), کلید, مقدار ].
		 *
		 * @param array $tags متاتگ‌ها.
		 */
		$tags = (array) apply_filters( 'zc_seo_meta_tags', $tags );

		echo "\n<!-- ZarinCoach SEO -->\n";
		if ( '' !== $canonical ) {
			echo '<link rel="canonical" href="' . esc_url( $canonical ) . '">' . "\n";
		}
		foreach ( $tags as $tag ) {
			if ( ! is_array( $tag ) || 3 !== count( $tag ) || '' === (string) $tag[2] ) {
				continue;
			}
			$attr = 'property' === $tag[0] ? 'property' : 'name';
			$val  = ( false !== strpos( (string) $tag[1], ':url' ) || in_array( $tag[1], array( 'og:image', 'og:image:secure_url', 'twitter:image' ), true ) ) ? esc_url( $tag[2] ) : esc_attr( $tag[2] );
			echo '<meta ' . $attr . '="' . esc_attr( $tag[1] ) . '" content="' . $val . '">' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above.
		}
		echo "<!-- /ZarinCoach SEO -->\n";
	}
endif;
add_action( 'wp_head', 'zc_output_meta_tags', 2 );

if ( ! function_exists( 'zc_seo_head_cleanup' ) ) :
	/**
	 * حذف خروجی‌های تکراری/غیرضروری هسته (canonical و shortlink).
	 *
	 * @return void
	 */
	function zc_seo_head_cleanup() {
		if ( ! zc_seo_active() ) {
			return;
		}
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
		remove_action( 'template_redirect', 'wp_shortlink_header', 11 );
	}
endif;
add_action( 'wp', 'zc_seo_head_cleanup' );

/* =========================================================================
 * ریدایرکت صفحات کم‌ارزش
 * ========================================================================= */

if ( ! function_exists( 'zc_seo_redirects' ) ) :
	/**
	 * ۳۰۱ برای صفحات پیوست و بایگانی نویسنده.
	 *
	 * @return void
	 */
	function zc_seo_redirects() {
		if ( ! zc_seo_active() || is_admin() || is_customize_preview() ) {
			return;
		}

		if ( is_attachment() && zc_switch( 'seo_redirect_attachment', true ) ) {
			$post   = get_queried_object();
			$target = ( $post && $post->post_parent && 'publish' === get_post_status( $post->post_parent ) ) ? get_permalink( $post->post_parent ) : wp_get_attachment_url( get_queried_object_id() );
			if ( $target ) {
				wp_safe_redirect( $target, 301, 'ZarinCoach' );
				exit;
			}
		}

		if ( is_author() && zc_switch( 'seo_redirect_author', true ) ) {
			$about  = get_page_by_path( 'about-me' );
			$target = $about ? get_permalink( $about ) : home_url( '/' );
			/**
			 * مقصد ریدایرکت بایگانی نویسنده.
			 *
			 * @param string $target آدرس.
			 */
			$target = (string) apply_filters( 'zc_author_redirect', $target );
			wp_safe_redirect( $target, 301, 'ZarinCoach' );
			exit;
		}
	}
endif;
add_action( 'template_redirect', 'zc_seo_redirects', 1 );

/* =========================================================================
 * نقشه‌ی سایت هسته (wp-sitemap.xml)
 * ========================================================================= */

if ( ! function_exists( 'zc_sitemap_on' ) ) :
	/**
	 * بهینه‌سازی نقشه‌ی سایت فعال است؟
	 *
	 * @return bool
	 */
	function zc_sitemap_on() {
		return zc_seo_active() && zc_switch( 'seo_sitemap', true );
	}
endif;

add_filter(
	'wp_sitemaps_add_provider',
	static function ( $provider, $name ) {
		return ( 'users' === $name && zc_sitemap_on() ) ? false : $provider;
	},
	10,
	2
);

add_filter(
	'wp_sitemaps_post_types',
	static function ( $types ) {
		if ( zc_sitemap_on() ) {
			foreach ( array( 'elementor_library', 'e-landing-page', 'e-floating-buttons', 'attachment', 'zc_testimonial', 'zc_faq', 'zc_message' ) as $t ) {
				unset( $types[ $t ] );
			}
		}
		return $types;
	}
);

add_filter(
	'wp_sitemaps_taxonomies',
	static function ( $taxonomies ) {
		if ( zc_sitemap_on() ) {
			unset( $taxonomies['post_format'] );
			if ( zc_switch( 'seo_noindex_tags', true ) ) {
				unset( $taxonomies['post_tag'] );
			}
		}
		return $taxonomies;
	}
);

add_filter(
	'wp_sitemaps_posts_query_args',
	static function ( $args ) {
		if ( ! zc_sitemap_on() ) {
			return $args;
		}
		$args['meta_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_query
			'relation' => 'OR',
			array(
				'key'     => '_zc_seo_noindex',
				'compare' => 'NOT EXISTS',
			),
			array(
				'key'     => '_zc_seo_noindex',
				'value'   => '1',
				'compare' => '!=',
			),
		);
		return $args;
	}
);

add_filter(
	'wp_sitemaps_posts_entry',
	static function ( $entry, $post ) {
		if ( zc_sitemap_on() && $post instanceof WP_Post ) {
			$entry['lastmod'] = get_post_modified_time( 'c', true, $post );
		}
		return $entry;
	},
	10,
	2
);

add_filter(
	'wp_sitemaps_posts_show_on_front_entry',
	static function ( $entry ) {
		if ( zc_sitemap_on() ) {
			$front = (int) get_option( 'page_on_front' );
			if ( $front ) {
				$entry['lastmod'] = get_post_modified_time( 'c', true, $front );
			}
		}
		return $entry;
	}
);

add_filter(
	'robots_txt',
	static function ( $output, $is_public ) {
		if ( ! $is_public || ! zc_seo_active() ) {
			return $output;
		}
		$extra = "Disallow: /?s=\nDisallow: /search/\nDisallow: /*?replytocom=\n";
		return preg_replace( '/(Allow: \/wp-admin\/admin-ajax\.php\n)/', '$1' . $extra, (string) $output, 1 );
	},
	20,
	2
);

/* =========================================================================
 * متن جایگزین تصاویر
 * ========================================================================= */

if ( ! function_exists( 'zc_image_alt_fix' ) ) :
	/**
	 * متن جایگزین خودکار برای تصاویر بدون alt (عنوان پیوست ← عنوان نوشته).
	 *
	 * @param array   $attr       ویژگی‌ها.
	 * @param WP_Post $attachment پیوست.
	 * @return array
	 */
	function zc_image_alt_fix( $attr, $attachment = null ) {
		if ( isset( $attr['alt'] ) && '' !== trim( (string) $attr['alt'] ) ) {
			return $attr;
		}
		if ( $attachment instanceof WP_Post ) {
			$alt = trim( (string) get_post_meta( $attachment->ID, '_wp_attachment_image_alt', true ) );
			if ( '' === $alt && ! preg_match( '/^(img|image|dsc|photo|zc-demo)[\s_-]?\d*/i', $attachment->post_title ) ) {
				$alt = $attachment->post_title;
			}
			if ( '' !== $alt ) {
				$attr['alt'] = $alt;
				return $attr;
			}
		}
		if ( is_singular() && in_the_loop() ) {
			$attr['alt'] = zc_seo_clean( get_the_title() );
		}
		return $attr;
	}
endif;
add_filter( 'wp_get_attachment_image_attributes', 'zc_image_alt_fix', 10, 2 );

/* =========================================================================
 * ساعات کاری
 * ========================================================================= */

if ( ! function_exists( 'zc_parse_opening_hours' ) ) :
	/**
	 * تبدیل رشته ساعات کاری (مثال: «Sa-We 16:00-20:30, Th 10:00-13:00») به اسکیمای OpeningHoursSpecification.
	 *
	 * @param string $raw رشته ورودی.
	 * @return array<int, array<string, mixed>>
	 */
	function zc_parse_opening_hours( $raw ) {
		$days  = array(
			'sa' => 'Saturday',
			'su' => 'Sunday',
			'mo' => 'Monday',
			'tu' => 'Tuesday',
			'we' => 'Wednesday',
			'th' => 'Thursday',
			'fr' => 'Friday',
		);
		$keys  = array_keys( $days );
		$out   = array();
		$parts = preg_split( '/[,;\n]+/', zc_digits_to_latin( (string) $raw ) );
		foreach ( (array) $parts as $part ) {
			if ( ! preg_match( '/^\s*([a-z]{2})(?:\s*-\s*([a-z]{2}))?\s+(\d{1,2}:\d{2})\s*-\s*(\d{1,2}:\d{2})\s*$/i', $part, $m ) ) {
				continue;
			}
			$from = array_search( strtolower( $m[1] ), $keys, true );
			$to   = '' !== $m[2] ? array_search( strtolower( $m[2] ), $keys, true ) : $from;
			if ( false === $from || false === $to ) {
				continue;
			}
			$list = array();
			for ( $i = $from, $n = 0; $n < 7; $i = ( $i + 1 ) % 7, $n++ ) {
				$list[] = $days[ $keys[ $i ] ];
				if ( $i === $to ) {
					break;
				}
			}
			$out[] = array(
				'@type'     => 'OpeningHoursSpecification',
				'dayOfWeek' => $list,
				'opens'     => str_pad( $m[3], 5, '0', STR_PAD_LEFT ),
				'closes'    => str_pad( $m[4], 5, '0', STR_PAD_LEFT ),
			);
		}
		return $out;
	}
endif;
