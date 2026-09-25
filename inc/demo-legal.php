<?php
/**
 * صفحات حقوقی دمو (v1.7 — بازنویسی کامل)، منطبق با قوانین و مقررات جمهوری اسلامی ایران:
 *
 * - قانون تشکیل سازمان نظام روان‌شناسی و مشاوره (۱۳۸۲)، به‌ویژه ماده ۵ (الزام پروانه)، و نظام‌نامه اخلاق حرفه‌ای سازمان
 * - قانون تجارت الکترونیکی (۱۳۸۲): مواد ۳۳ تا ۳۸ (اطلاعات پیش از قرارداد و حق انصراف)، ۵۸ و ۵۹ (داده‌های شخصی و حساس)،
 *   ۶۲ و ۷۴ (حق مؤلف در بستر مبادلات الکترونیکی)، ۶۹ و ۷۱ (ضمانت اجرا)
 * - قانون حمایت از حقوق مصرف‌کنندگان (۱۳۸۸)، قانون جرایم رایانه‌ای (۱۳۸۸)
 * - قانون حمایت حقوق مؤلفان و مصنفان و هنرمندان (۱۳۴۸)، قانون حمایت از اطفال و نوجوانان (۱۳۹۹)
 * - قانون مجازات اسلامی، ماده ۶۴۸ (افشای اسرار حرفه‌ای)، و قانون مدنی (قوه‌ی قاهره، مواد ۲۲۷ و ۲۲۹)
 *
 * متن‌ها با نشانه‌های ساده نوشته شده‌اند و هنگام ساخت برگه به شورت‌کد تبدیل می‌شوند:
 * {owner} {domain} {lic} {phone} {mobile} {email} {address} {hours} و {link:کلید|متن پیوند}
 * پس مالک، دامنه، شماره‌ی پروانه و راه‌های تماس همیشه از پنل قالب خوانده می‌شوند.
 *
 * توجه: این متن‌ها نمونه‌ی حرفه‌ای و دقیق‌اند، اما پیش از انتشار نهایی باید توسط مشاور حقوقی بازبینی شوند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/demo-legal-core.php';
require_once __DIR__ . '/demo-legal-service.php';
require_once __DIR__ . '/demo-legal-ethics.php';
require_once __DIR__ . '/demo-legal-shop.php';

if ( ! function_exists( 'zc_demo_legal_fill' ) ) :
	/**
	 * تبدیل نشانه‌های متن حقوقی به شورت‌کدهای پویا.
	 *
	 * @param string $html متن با نشانه.
	 * @return string
	 */
	function zc_demo_legal_fill( $html ) {
		$map  = array(
			'{owner}'   => '[zc_info field="owner"]',
			'{domain}'  => '[zc_info field="domain" link="1"]',
			'{lic}'     => '[zc_info field="license_label"]: [zc_info field="license"] — [zc_info field="pco_label"]: [zc_info field="pco_code"]',
			'{phone}'   => '[zc_info field="phone" link="1"]',
			'{mobile}'  => '[zc_info field="mobile" link="1"]',
			'{email}'   => '[zc_info field="email" link="1"]',
			'{address}' => '[zc_info field="address"]',
			'{hours}'   => '[zc_info field="hours"]',
		);
		$html = strtr( (string) $html, $map );
		$html = zc_demo_legal_table_labels( $html );

		return (string) preg_replace_callback(
			'/\{link:([a-z0-9\-]+)\|([^}]+)\}/u',
			static function ( $m ) {
				return '[zc_link page="' . $m[1] . '" slug="' . $m[1] . '" text="' . str_replace( '"', '', $m[2] ) . '"]';
			},
			$html
		);
	}
endif;

if ( ! function_exists( 'zc_demo_legal_table_labels' ) ) :
	/**
	 * افزودن data-label (از سرستون‌ها) به خانه‌های جدول تا در موبایل به شکل کارت نمایش داده شوند.
	 *
	 * @param string $html متن.
	 * @return string
	 */
	function zc_demo_legal_table_labels( $html ) {
		return (string) preg_replace_callback(
			'#<table>(.*?)</table>#su',
			static function ( $t ) {
				preg_match_all( '#<th>(.*?)</th>#su', $t[1], $heads );
				$labels = array_map( 'wp_strip_all_tags', $heads[1] );
				$body   = preg_replace_callback(
					'#<tr>(.*?)</tr>#su',
					static function ( $row ) use ( $labels ) {
						$i = 0;
						return '<tr>' . preg_replace_callback(
							'#<td(\s[^>]*)?>#u',
							static function ( $td ) use ( $labels, &$i ) {
								$label = isset( $labels[ $i ] ) ? $labels[ $i ] : '';
								$i++;
								return '<td' . ( isset( $td[1] ) ? $td[1] : '' ) . ' data-label="' . esc_attr( $label ) . '">';
							},
							$row[1]
						) . '</tr>';
					},
					$t[1]
				);
				return '<table>' . $body . '</table>';
			},
			$html
		);
	}
endif;

if ( ! function_exists( 'zc_demo_legal_pages' ) ) :
	/**
	 * صفحات قوانین (کلید = نامک) به ترتیب نمایش در منوی قوانین.
	 *
	 * @return array<string, array<string, string>>
	 */
	function zc_demo_legal_pages() {
		static $pages = null;
		if ( null !== $pages ) {
			return $pages;
		}

		$pages = array_merge(
			zc_demo_legal_core(),     // قوانین، حریم خصوصی، کوکی‌ها، لغو و بازگشت وجه.
			zc_demo_legal_service(),  // ضمانت و شکایات، حساب کاربری، رضایت‌نامه، جلسات آنلاین.
			zc_demo_legal_ethics(),   // مالکیت فکری، اخلاق و رازداری، سلب مسئولیت و اضطرار.
			class_exists( 'WooCommerce' ) ? zc_demo_legal_shop() : array() // خرید، ارسال و مرجوعی (فقط با فروشگاه).
		);

		foreach ( $pages as $key => $page ) {
			foreach ( array( 'excerpt', 'notice', 'content' ) as $field ) {
				if ( isset( $page[ $field ] ) ) {
					$pages[ $key ][ $field ] = zc_demo_legal_fill( trim( $page[ $field ] ) );
				}
			}
			if ( empty( $pages[ $key ]['slug'] ) ) {
				$pages[ $key ]['slug'] = $key;
			}
		}

		/**
		 * فیلتر صفحات حقوقی دمو.
		 *
		 * @param array $pages صفحات.
		 */
		$pages = (array) apply_filters( 'zc_demo_legal_pages', $pages );
		return $pages;
	}
endif;

if ( ! function_exists( 'zc_demo_legal_keys' ) ) :
	/**
	 * کلیدهای صفحات قوانین به ترتیب نمایش.
	 *
	 * @return string[]
	 */
	function zc_demo_legal_keys() {
		return array_keys( zc_demo_legal_pages() );
	}
endif;
