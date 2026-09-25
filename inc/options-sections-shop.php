<?php
/**
 * پنل تنظیمات — بخش «فروشگاه» (ووکامرس).
 *
 * فقط وقتی ووکامرس فعال است در پنل نمایش داده می‌شود؛ مقادیر پیش‌فرض همیشه در دسترس‌اند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_panel_shop' ) ) :
	/**
	 * فروشگاه: بایگانی، کارت محصول، صفحه‌ی محصول، سبد و تسویه‌حساب.
	 *
	 * @param bool $for_panel خروجی نمایشی پنل.
	 * @return array
	 */
	function zc_panel_shop( $for_panel = true ) {
		if ( $for_panel && ! class_exists( 'WooCommerce' ) ) {
			return array();
		}

		$toman = __( 'تومان', 'zarincoach' );

		return array(
			zc_panel_section(
				'shop',
				__( 'فروشگاه', 'zarincoach' ),
				'fa-solid fa-bag-shopping',
				__( 'بایگانی محصولات، کارت محصول و دکمه‌های سربرگ. طراحی برگه‌ی «فروشگاه» با ویجت‌های فروشگاهی المنتور بالای شبکه‌ی محصولات نمایش داده می‌شود.', 'zarincoach' ),
				array(
					zc_f_group( 'shop_archive_group', __( 'بایگانی محصولات', 'zarincoach' ) ),
					zc_f_buttons(
						'shop_sidebar',
						__( 'ستون فیلترها', 'zarincoach' ),
						array(
							'start' => __( 'راست', 'zarincoach' ),
							'end'   => __( 'چپ', 'zarincoach' ),
							'none'  => __( 'بدون ستون', 'zarincoach' ),
						),
						'start',
						__( 'در موبایل فیلترها همیشه به‌صورت کشویی باز می‌شوند. اگر در «نمایش ← ابزارک‌ها» به ناحیه‌ی «ستون فروشگاه» ابزارک اضافه کنید، به‌جای فیلترهای داخلی نمایش داده می‌شوند.', 'zarincoach' )
					),
					zc_f_buttons(
						'shop_columns',
						__( 'تعداد ستون محصولات (دسکتاپ)', 'zarincoach' ),
						array(
							'2' => '۲',
							'3' => '۳',
							'4' => '۴',
						),
						'3',
						__( 'در تبلت ۳ و در موبایل ۲ ستون.', 'zarincoach' )
					),
					zc_f_slider( 'shop_per_page', __( 'محصول در هر صفحه', 'zarincoach' ), 12, 4, 48, 1 ),
					zc_f_switch( 'shop_intro', __( 'نمایش طراحی المنتوری برگه‌ی فروشگاه', 'zarincoach' ), true, __( 'محتوای برگه‌ی «فروشگاه» (ساخته‌شده با المنتور) در صفحه‌ی اول بایگانی، بالای محصولات نمایش داده می‌شود.', 'zarincoach' ) ),
					zc_f_textarea( 'shop_hero_text', __( 'متن معرفی سربرگ فروشگاه', 'zarincoach' ), 'کتاب‌ها، کارپوشه‌ها و بسته‌های کوچینگ مریم جمالی؛ ابزارهای عملی برای شناخت الگوهای ذهنی و ساختن تغییر پایدار.', '', 2 ),
					zc_f_switch( 'shop_hero_perks', __( 'نشان‌های مزیت در سربرگ', 'zarincoach' ), true, __( 'تحویل فوری، پرداخت امن، پشتیبانی.', 'zarincoach' ) ),

					zc_f_group( 'shop_card_group', __( 'کارت محصول', 'zarincoach' ) ),
					zc_f_switch( 'shop_card_hover_image', __( 'نمایش تصویر دوم هنگام hover', 'zarincoach' ), true, __( 'اولین تصویر گالری محصول.', 'zarincoach' ) ),
					zc_f_switch( 'shop_card_category', __( 'نمایش دسته', 'zarincoach' ), true ),
					zc_f_switch( 'shop_card_kind', __( 'برچسب نوع تحویل (دانلودی/فیزیکی/جلسه)', 'zarincoach' ), true ),
					zc_f_switch( 'shop_card_rating', __( 'امتیاز ستاره‌ای', 'zarincoach' ), true, __( 'فقط وقتی محصول دیدگاه واقعی دارد.', 'zarincoach' ) ),
					zc_f_switch( 'shop_card_excerpt', __( 'توضیح کوتاه', 'zarincoach' ), false ),
					zc_f_buttons(
						'shop_badge_style',
						__( 'برچسب تخفیف', 'zarincoach' ),
						array(
							'percent' => __( 'درصد (٪۲۰ تخفیف)', 'zarincoach' ),
							'text'    => __( 'متن (فروش ویژه)', 'zarincoach' ),
						),
						'percent'
					),
					zc_f_switch( 'shop_badge_featured', __( 'برچسب «پیشنهاد ویژه» برای محصولات ویژه', 'zarincoach' ), true ),
					zc_f_slider( 'shop_new_days', __( 'برچسب «تازه» تا چند روز پس از انتشار', 'zarincoach' ), 30, 0, 120, 1, __( '۰ = غیرفعال.', 'zarincoach' ) ),

					zc_f_group( 'shop_kind_group', __( 'انواع محصول', 'zarincoach' ) ),
					zc_f_ltr( 'shop_session_cat', __( 'نامک دسته‌ی جلسات کوچینگ', 'zarincoach' ), 'coaching-sessions', __( 'محصولات این دسته «جلسه» محسوب می‌شوند (متن تحویل، برچسب و اسکیمای Service).', 'zarincoach' ) ),

					zc_f_group( 'shop_header_group', __( 'سربرگ', 'zarincoach' ) ),
					zc_f_switch( 'header_cart', __( 'دکمه‌ی سبد خرید در سربرگ', 'zarincoach' ), true, __( 'در ویجت «سربرگ سایت» قابل تغییر است.', 'zarincoach' ) ),
					zc_f_switch( 'header_account', __( 'دکمه‌ی حساب کاربری در سربرگ', 'zarincoach' ), true ),
				),
				array( 'zc_group' => __( 'فروشگاه', 'zarincoach' ) )
			),

			zc_panel_section(
				'shop_single',
				__( 'صفحه‌ی محصول', 'zarincoach' ),
				'fa-solid fa-box-open',
				__( 'ابزارهای فروش صفحه‌ی محصول: شمارش معکوس تخفیف، کمبود موجودی، خرید فوری، نوار چسبان و نشان‌های اعتماد.', 'zarincoach' ),
				array(
					zc_f_group( 'sp_layout_group', __( 'چیدمان', 'zarincoach' ) ),
					zc_f_switch( 'sp_sticky_gallery', __( 'گالری چسبان هنگام اسکرول', 'zarincoach' ), true ),
					zc_f_switch( 'sp_sections_nav', __( 'منوی چسبان بخش‌ها (توضیحات، مشخصات، پرسش‌ها، دیدگاه‌ها)', 'zarincoach' ), true ),
					zc_f_switch( 'sp_variation_pills', __( 'انتخاب گونه با دکمه به‌جای فهرست کشویی', 'zarincoach' ), true ),
					zc_f_slider( 'sp_related_count', __( 'تعداد محصولات مرتبط', 'zarincoach' ), 4, 0, 8, 1, __( '۰ = پنهان.', 'zarincoach' ) ),

					zc_f_group( 'sp_sales_group', __( 'ابزارهای فروش', 'zarincoach' ) ),
					zc_f_switch( 'sp_buy_now', __( 'دکمه‌ی «خرید فوری»', 'zarincoach' ), true, __( 'افزودن به سبد و انتقال مستقیم به تسویه‌حساب.', 'zarincoach' ) ),
					zc_f_switch( 'sp_sticky_bar', __( 'نوار چسبان خرید', 'zarincoach' ), true, __( 'وقتی دکمه‌ی خرید از دید خارج شود، پایین صفحه نمایش داده می‌شود.', 'zarincoach' ) ),
					zc_f_switch( 'sp_countdown', __( 'شمارش معکوس پایان تخفیف', 'zarincoach' ), true, __( 'برای تخفیف‌های زمان‌دار («زمان‌بندی» قیمت فروش ویژه).', 'zarincoach' ) ),
					zc_f_switch( 'sp_stock_bar', __( 'نوار کمبود موجودی', 'zarincoach' ), true, __( 'برای محصولاتی که مدیریت موجودی دارند.', 'zarincoach' ) ),
					zc_req( zc_f_slider( 'sp_stock_threshold', __( 'نمایش وقتی موجودی کمتر یا برابر', 'zarincoach' ), 10, 1, 50, 1 ), 'sp_stock_bar' ),
					zc_f_switch( 'sp_share', __( 'دکمه‌های اشتراک‌گذاری', 'zarincoach' ), true ),

					zc_f_group( 'sp_delivery_group', __( 'زمان و نحوه‌ی تحویل', 'zarincoach' ) ),
					zc_f_switch( 'sp_delivery', __( 'نمایش کادر تحویل', 'zarincoach' ), true, __( 'برای هر محصول می‌توانید در «داده‌های محصول ← زرین‌کوچ» متن اختصاصی بنویسید.', 'zarincoach' ) ),
					zc_req( zc_f_textarea( 'sp_delivery_digital', __( 'محصولات دانلودی', 'zarincoach' ), 'بلافاصله پس از پرداخت، لینک دانلود در صفحه‌ی سفارش، حساب کاربری و ایمیل شما فعال می‌شود.', '', 2 ), 'sp_delivery' ),
					zc_req( zc_f_textarea( 'sp_delivery_physical', __( 'محصولات فیزیکی', 'zarincoach' ), 'سفارش‌ها ظرف ۱ تا ۲ روز کاری بسته‌بندی و با پست پیشتاز ارسال می‌شوند؛ کد رهگیری پیامک می‌شود.', '', 2 ), 'sp_delivery' ),
					zc_f_textarea( 'sp_delivery_session', __( 'بسته‌های جلسه', 'zarincoach' ), 'پس از پرداخت، ظرف ۲۴ ساعت کاری برای تعیین زمان جلسه با شما تماس گرفته می‌شود. جلسات آنلاین فقط از بستر امن برگزار می‌شوند.', __( 'در صفحه‌ی تشکر و ایمیل سفارش هم نمایش داده می‌شود.', 'zarincoach' ), 2 ),

					zc_f_group( 'sp_trust_group', __( 'نشان‌های اعتماد زیر دکمه‌ی خرید', 'zarincoach' ) ),
					zc_f_switch( 'sp_trust', __( 'نمایش', 'zarincoach' ), true ),
					zc_req(
						zc_f_textarea(
							'sp_trust_items',
							__( 'موارد (حداکثر ۳)', 'zarincoach' ),
							"پرداخت امن از درگاه بانکی\nمرجوعی و بازگشت وجه طبق قانون | shipping\nپشتیبانی پس از خرید | contact",
							__( 'هر خط: «متن | پیوند». پیوند می‌تواند نشانی کامل یا کلید برگه باشد: shipping، refund-policy، contact، terms، privacy.', 'zarincoach' ),
							3
						),
						'sp_trust'
					),
				),
				array( 'subsection' => true )
			),

			zc_panel_section(
				'shop_cart',
				__( 'سبد و تسویه‌حساب', 'zarincoach' ),
				'fa-solid fa-cart-shopping',
				__( 'سبد کشویی، ارسال رایگان و فرم تسویه‌حساب ایرانی.', 'zarincoach' ),
				array(
					zc_f_group( 'shop_minicart_group', __( 'سبد کشویی', 'zarincoach' ) ),
					zc_f_switch( 'shop_minicart', __( 'سبد خرید کشویی', 'zarincoach' ), true, __( 'با کلیک روی دکمه‌ی سبد در سربرگ، سبد از کنار صفحه باز می‌شود.', 'zarincoach' ) ),
					zc_req( zc_f_switch( 'shop_minicart_auto_open', __( 'باز شدن خودکار پس از افزودن محصول', 'zarincoach' ), true ), 'shop_minicart' ),
					/* translators: %s: واحد پول */
					zc_f_ltr( 'shop_free_shipping', sprintf( __( 'حد ارسال رایگان (%s)', 'zarincoach' ), $toman ), '', __( 'مثلاً 1500000. خالی یا ۰ = غیرفعال. نوار پیشرفت در سبد نمایش داده می‌شود و هزینه‌ی ارسال به‌طور خودکار صفر می‌شود.', 'zarincoach' ) ),

					zc_f_group( 'checkout_group', __( 'تسویه‌حساب', 'zarincoach' ) ),
					zc_f_switch( 'checkout_virtual_simple', __( 'فرم کوتاه برای سفارش‌های مجازی', 'zarincoach' ), true, __( 'اگر سبد فقط محصول دانلودی یا جلسه باشد، فیلدهای نشانی پستی حذف می‌شوند.', 'zarincoach' ) ),
					zc_f_switch( 'checkout_validate_ir', __( 'اعتبارسنجی تلفن همراه و کد پستی ایران', 'zarincoach' ), true, __( 'تلفن ۰۹xxxxxxxxx و کد پستی ۱۰ رقمی؛ ارقام فارسی خودکار تبدیل می‌شوند.', 'zarincoach' ) ),
					zc_f_slider( 'shop_bacs_cancel_hours', __( 'لغو خودکار سفارش کارت‌به‌کارت پرداخت‌نشده (ساعت)', 'zarincoach' ), 48, 0, 168, 1, __( 'سفارش‌های «در انتظار بررسی» روش کارت‌به‌کارت پس از این مدت لغو و موجودی به انبار برمی‌گردد. ۰ = غیرفعال. با متن برگه‌ی «شرایط خرید، ارسال و مرجوعی» هماهنگ نگه دارید.', 'zarincoach' ) ),
					zc_f_switch( 'checkout_company', __( 'فیلد «نام شرکت»', 'zarincoach' ), false ),
					zc_f_text( 'checkout_note', __( 'یادداشت اعتماد زیر دکمه‌ی پرداخت', 'zarincoach' ), 'پرداخت از طریق درگاه امن شاپرک انجام می‌شود. اطلاعات شما نزد ما محرمانه است.' ),

					zc_f_group( 'shop_schema_group', __( 'ارسال و مرجوعی در نتایج گوگل (اسکیما)', 'zarincoach' ) ),
					/* translators: %s: واحد پول */
					zc_f_ltr( 'seo_ship_rate', sprintf( __( 'هزینه‌ی ارسال پستی (%s)', 'zarincoach' ), $toman ), '', __( 'برای نمایش هزینه‌ی ارسال در نتایج گوگل (OfferShippingDetails) کالاهای فیزیکی. خالی = اعلام نمی‌شود. با حد ارسال رایگان خودکار ۰ می‌شود.', 'zarincoach' ) ),
					zc_f_ltr( 'seo_ship_handling', __( 'زمان آماده‌سازی (روز)', 'zarincoach' ), '0-2', __( 'بازه به شکل حداقل-حداکثر؛ مثلاً 0-2', 'zarincoach' ) ),
					zc_f_ltr( 'seo_ship_transit', __( 'زمان ارسال پستی (روز)', 'zarincoach' ), '2-5', __( 'بازه به شکل حداقل-حداکثر؛ مثلاً 2-5', 'zarincoach' ) ),
					zc_f_slider( 'seo_return_days', __( 'مهلت انصراف کالای فیزیکی (روز)', 'zarincoach' ), 7, 0, 30, 1, __( 'حداقل قانونی ۷ روز (ماده‌ی ۳۷ قانون تجارت الکترونیکی). محصولات دانلودی و جلسات پس از تحویل/آغاز خدمت «غیرقابل مرجوعی» اعلام می‌شوند (ماده‌ی ۳۸).', 'zarincoach' ) ),
				),
				array( 'subsection' => true )
			),
		);
	}
endif;
