<?php
/**
 * دموی فروشگاه: دسته‌ها، محصولات نمونه (فیزیکی، دانلودی، بسته‌ی جلسات)، کد تخفیف،
 * ارسال، روش‌های پرداخت آفلاین، برگه‌های ووکامرس و طراحی المنتوری برگه‌ی فروشگاه.
 *
 * همه‌چیز نشان‌دار و ردیابی می‌شود تا نصب دوباره‌ی دمو بدون محتوای تکراری و حذف آن کامل باشد.
 * قیمت‌ها و محصولات نمونه‌اند و پیش از فروش واقعی باید با محصولات و قیمت‌های واقعی جایگزین شوند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_demo_shop_media' ) ) :
	/**
	 * تصاویر فروشگاه (به فهرست تصاویر دمو افزوده می‌شوند).
	 *
	 * @return array<string, array<string, string>>
	 */
	function zc_demo_shop_media() {
		return array(
			'shop-book'     => array(
				'file'  => 'shop-book.jpg',
				'title' => 'کتاب از الگو تا انتخاب',
				'alt'   => 'کتاب «از الگو تا انتخاب» با جلد سرمه‌ای و طرح طلایی مسیر و قطب‌نما',
			),
			'shop-journal'  => array(
				'file'  => 'shop-journal.jpg',
				'title' => 'ژورنال ۹۰ روزه ردپای الگوها',
				'alt'   => 'ژورنال جلد پارچه‌ای سرمه‌ای با کش طلایی و صفحه‌های نقطه‌چین',
			),
			'shop-cards'    => array(
				'file'  => 'shop-cards.jpg',
				'title' => 'کارت‌های گفت‌وگو با خود',
				'alt'   => 'جعبه‌ی سرمه‌ای کارت‌های خودشناسی با نمادهای طلایی',
			),
			'shop-workbook' => array(
				'file'  => 'shop-workbook.jpg',
				'title' => 'کارپوشه نقشه طرحواره‌های من',
				'alt'   => 'کارپوشه‌ی دیجیتال نقشه‌ی طرحواره‌ها روی تبلت کنار فنجان چای',
			),
			'shop-planner'  => array(
				'file'  => 'shop-planner.jpg',
				'title' => 'برنامه ۳۰ روزه از اهمالکاری تا اقدام',
				'alt'   => 'برنامه‌ی ۳۰ روزه‌ی چاپی روی تخته‌شاسی سرمه‌ای با تیک‌های طلایی',
			),
			'shop-audio'    => array(
				'file'  => 'shop-audio.jpg',
				'title' => 'دوره صوتی ذهن آرام، تصمیم روشن',
				'alt'   => 'هدفون سرمه‌ای و گوشی با موج صدای طلایی برای دوره‌ی صوتی',
			),
		);
	}
endif;

if ( ! function_exists( 'zc_demo_shop_categories' ) ) :
	/**
	 * دسته‌های محصول.
	 *
	 * @return array<string, array<string, string>>
	 */
	function zc_demo_shop_categories() {
		return array(
			'coaching-sessions' => array(
				'name'  => 'بسته‌های جلسات',
				'desc'  => 'جلسه‌ی ارزیابی و بسته‌های چندجلسه‌ای کوچینگ الگوهای ذهنی؛ حضوری در بوشهر و آنلاین.',
				'image' => 'maryam-session',
			),
			'workbooks'         => array(
				'name'  => 'کارپوشه‌ها',
				'desc'  => 'کارپوشه‌ها و برنامه‌های دانلودی برای تمرین در خانه؛ تحویل فوری پس از پرداخت.',
				'image' => 'shop-workbook',
			),
			'audio-courses'     => array(
				'name'  => 'دوره‌های صوتی',
				'desc'  => 'دوره‌های صوتی کوتاه با تمرین‌های گام‌به‌گام؛ هر زمان و هر جا.',
				'image' => 'shop-audio',
			),
			'books-journals'    => array(
				'name'  => 'کتاب، ژورنال و کارت',
				'desc'  => 'نسخه‌های چاپی برای خواندن، نوشتن و گفت‌وگو با خود؛ ارسال پستی به سراسر ایران.',
				'image' => 'shop-book',
			),
		);
	}
endif;

if ( ! function_exists( 'zc_demo_shop_products' ) ) :
	/**
	 * محصولات نمونه.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function zc_demo_shop_products() {
		$session_note = '<p><em>هزینه‌ی جلسات بر اساس تعرفه‌ی مصوب سازمان نظام روان‌شناسی و مشاوره برای سال جاری تنظیم می‌شود و در صورت تغییر تعرفه اصلاح خواهد شد.</em></p>';

		return array(
			/* ---------------------------------------------------------- فیزیکی */
			'book'      => array(
				'name'     => 'کتاب «از الگو تا انتخاب»',
				'slug'     => 'from-pattern-to-choice',
				'kind'     => 'physical',
				'schema'   => 'Book',
				'cat'      => 'books-journals',
				'image'    => 'shop-book',
				'gallery'  => array( 'shop-journal', 'compass-notebook' ),
				'regular'  => 420000,
				'sale'     => 357000,
				'sale_days' => 12,
				'stock'    => 40,
				'weight'   => '0.45',
				'dims'     => array( 21, 14, 2 ),
				'featured' => true,
				'sku'      => 'ZC-BK-01',
				'subtitle' => 'راهنمای عملی شناخت الگوهای تکرارشونده و ساختن انتخاب تازه',
				'short'    => 'کتابی برای وقتی که می‌دانی چه باید بکنی، اما باز همان کار همیشگی را تکرار می‌کنی. ۱۲ فصل کوتاه، هر فصل با یک مثال واقعی و یک تمرین ده‌دقیقه‌ای.',
				'desc'     => '<h3>این کتاب برای چه کسی است؟</h3><p>برای جوان‌ها و مدیرانی که الگوهایی مثل کمال‌گرایی، اهمالکاری، تأییدطلبی یا تصمیم‌های معوق را در زندگی خود می‌بینند و می‌خواهند از «فهمیدن» به «انتخاب کردن» برسند.</p><h3>در این کتاب می‌خوانید</h3><ul><li>الگو چیست و چرا با وجود آگاهی تکرار می‌شود؟</li><li>نقشه‌ی چهارمرحله‌ای «موقعیت، فکر، احساس، رفتار»</li><li>۱۸ طرحواره‌ی ناسازگار به زبان ساده، با نشانه‌های روزمره</li><li>از خودسرزنشی تا خودمهربانی: گفت‌وگوی درونی تازه</li><li>برنامه‌ی ۲۱ روزه‌ی تمرین انتخاب</li></ul><h3>مشخصات</h3><p>قطع رقعی، ۲۲۴ صفحه، جلد نرم با روکش مات، چاپ دورنگ.</p>',
				'features' => "۲۲۴ صفحه در ۱۲ فصل کوتاه و خوش‌خوان\nهر فصل: مثال واقعی + تمرین ده‌دقیقه‌ای\nبرنامه‌ی ۲۱ روزه‌ی تمرین در پایان کتاب\nارسال پستی با کد رهگیری به سراسر ایران",
				'faq'      => "نسخه‌ی الکترونیکی هم دارد؟ | فعلاً فقط نسخه‌ی چاپی عرضه می‌شود. کارپوشه‌ی «نقشه‌ی طرحواره‌های من» مکمل دانلودی همین کتاب است.\nچند روزه به دستم می‌رسد؟ | ۱ تا ۲ روز کاری آماده‌سازی و معمولاً ۲ تا ۵ روز کاری ارسال با پست پیشتاز.\nاگر پسندم نبود می‌توانم برگردانم؟ | بله؛ تا ۷ روز پس از تحویل، اگر کتاب سالم و استفاده‌نشده باشد، طبق شرایط مرجوعی بازگردانده می‌شود.",
				'upsells'  => array( 'workbook' ),
				'cross'    => array( 'journal', 'cards' ),
			),
			'journal'   => array(
				'name'     => 'ژورنال ۹۰ روزه‌ی «ردپای الگوها»',
				'slug'     => 'pattern-journal-90',
				'kind'     => 'physical',
				'cat'      => 'books-journals',
				'image'    => 'shop-journal',
				'gallery'  => array( 'shop-planner' ),
				'regular'  => 280000,
				'stock'    => 25,
				'weight'   => '0.38',
				'dims'     => array( 21, 15, 2 ),
				'sku'      => 'ZC-JR-01',
				'subtitle' => 'هر روز پنج دقیقه نوشتن؛ دیدنِ الگو پیش از تکرار آن',
				'short'    => 'ژورنال راهنمادار ۹۰ روزه با پرسش‌های روزانه، مرور هفتگی و نقشه‌ی ماهانه‌ی الگوها. جلد پارچه‌ای سرمه‌ای، کش و روبان نشانگر.',
				'desc'     => '<h3>چرا نوشتن؟</h3><p>الگوها در لحظه دیده نمی‌شوند؛ در مرور دیده می‌شوند. این ژورنال با پرسش‌های کوتاه روزانه کمک می‌کند ردپای موقعیت‌ها، فکرها و واکنش‌هایت را ثبت کنی و در مرور هفتگی، تکرارها را ببینی.</p><ul><li>۹۰ صفحه‌ی روزانه با سه پرسش ثابت و یک پرسش متغیر</li><li>۱۳ صفحه‌ی مرور هفتگی و ۳ نقشه‌ی ماهانه</li><li>کاغذ ۱۰۰ گرمی کرم، مناسب خودکار و روان‌نویس</li></ul>',
				'features' => "۹۰ روز پرسش راهنما + مرور هفتگی و ماهانه\nجلد سخت پارچه‌ای، کش طلایی و روبان نشانگر\nکاغذ کرم ۱۰۰ گرمی، ۲۴۰ صفحه\nهمراه عالی کتاب «از الگو تا انتخاب»",
				'faq'      => 'تاریخ‌دار است؟ | خیر؛ از هر روزی می‌توانی شروع کنی و روزهای جاافتاده بی‌اشکال است.',
				'cross'    => array( 'book', 'cards' ),
			),
			'cards'     => array(
				'name'     => 'کارت‌های «گفت‌وگو با خود»',
				'slug'     => 'self-talk-cards',
				'kind'     => 'physical',
				'cat'      => 'books-journals',
				'image'    => 'shop-cards',
				'gallery'  => array( 'tea-conversation' ),
				'regular'  => 340000,
				'stock'    => 4,
				'weight'   => '0.30',
				'dims'     => array( 12, 9, 4 ),
				'sku'      => 'ZC-CD-52',
				'subtitle' => '۵۲ کارت پرسش خودشناسی برای یک سال گفت‌وگوی صادقانه',
				'short'    => '۵۲ کارت در چهار دسته‌ی «ریشه‌ها، باورها، روابط و انتخاب‌ها» با راهنمای استفاده‌ی فردی، دونفره و گروهی. جعبه‌ی آهنربایی سرمه‌ای.',
				'desc'     => '<h3>چطور استفاده کنم؟</h3><p>هر هفته یک کارت بکش و پرسش آن را در ژورنال یا در گفت‌وگو با یک دوست مورد اعتماد پاسخ بده. پشت هر کارت یک «تمرین کوچک» آمده تا پاسخ به عمل تبدیل شود.</p><ul><li><strong>ریشه‌ها:</strong> ۱۳ کارت درباره‌ی تجربه‌های اولیه</li><li><strong>باورها:</strong> ۱۳ کارت درباره‌ی گفت‌وگوی درونی</li><li><strong>روابط:</strong> ۱۳ کارت درباره‌ی مرزها و نیازها</li><li><strong>انتخاب‌ها:</strong> ۱۳ کارت درباره‌ی اقدام و تغییر</li></ul>',
				'features' => "۵۲ کارت در ۴ دسته با کد رنگی\nکاغذ گلاسه‌ی ۳۵۰ گرمی با روکش مات\nدفترچه‌ی راهنمای استفاده‌ی فردی و گروهی\nجعبه‌ی سخت آهنربایی، مناسب هدیه",
				'badge'    => 'مناسب هدیه',
				'cross'    => array( 'journal', 'book' ),
			),

			/* ---------------------------------------------------------- دانلودی */
			'workbook'  => array(
				'name'     => 'کارپوشه‌ی «نقشه‌ی طرحواره‌های من»',
				'slug'     => 'my-schema-map-workbook',
				'kind'     => 'digital',
				'cat'      => 'workbooks',
				'image'    => 'shop-workbook',
				'gallery'  => array( 'schema-journal', 'pattern-notebook' ),
				'regular'  => 189000,
				'sale'     => 149000,
				'sale_days' => 12,
				'featured' => true,
				'sku'      => 'ZC-WB-01',
				'badge'    => 'انتخاب مریم',
				'subtitle' => 'کارپوشه‌ی ۹۶ صفحه‌ای برای شناخت طرحواره‌های غالب و ذهنیت‌های فعال',
				'short'    => 'قدم‌به‌قدم طرحواره‌های غالب خود را بشناس، ذهنیت‌های فعال را در موقعیت‌های واقعی ردیابی کن و برای هر کدام کارت یادآور و برنامه‌ی تمرین بساز. فایل PDF قابل چاپ و قابل پر کردن روی تبلت.',
				'desc'     => '<h3>درون این کارپوشه</h3><ul><li>پرسش‌نامه‌ی خودسنجی برای ۱۸ طرحواره (برای خودشناسی، نه تشخیص)</li><li>کاربرگ «ردپای یک الگو» برای ثبت موقعیت‌ها</li><li>نقشه‌ی ذهنیت‌ها: کودک آسیب‌پذیر، والد منتقد، بزرگسال سالم و…</li><li>کارت‌های یادآور قابل چاپ برای هر طرحواره</li><li>برنامه‌ی ۴ هفته‌ای تمرین و ارزیابی پیشرفت</li></ul><h3>نحوه‌ی تحویل</h3><p>پیوند دانلود بلافاصله پس از پرداخت در صفحه‌ی تأیید سفارش، ایمیل و بخش «دانلودها»ی حساب کاربری فعال می‌شود.</p><p><em>این کارپوشه ابزار آموزشی و خودشناسی است و جایگزین ارزیابی تخصصی فردی نیست.</em></p>',
				'features' => "۹۶ صفحه‌ی PDF با کیفیت چاپ (A4)\nقابل پر کردن روی تبلت با هر برنامه‌ی PDF\nتحویل فوری پس از پرداخت\n۵ بار دانلود و یک سال اعتبار پیوند",
				'faq'      => "فایل روی موبایل باز می‌شود؟ | بله، با هر نمایشگر PDF. برای نوشتن، تبلت یا نسخه‌ی چاپی راحت‌تر است.\nاگر پیوند دانلود را گم کنم؟ | در بخش «دانلودها»ی حساب کاربری همیشه در دسترس است؛ در غیر این صورت با شماره‌ی سفارش پیام بده تا دوباره فعال شود.\nبعد از خرید می‌توانم انصراف دهم؟ | تا پیش از نخستین دانلود بله؛ پس از دانلود طبق شرایط خرید مشمول انصراف نیست، اما فایل خراب همیشه جایگزین می‌شود.",
				'upsells'  => array( 'audio' ),
				'cross'    => array( 'planner', 'book' ),
			),
			'planner'   => array(
				'name'     => 'برنامه‌ی ۳۰ روزه‌ی «از اهمالکاری تا اقدام»',
				'slug'     => 'procrastination-to-action-30',
				'kind'     => 'digital',
				'cat'      => 'workbooks',
				'image'    => 'shop-planner',
				'gallery'  => array( 'compass-notebook' ),
				'regular'  => 129000,
				'sku'      => 'ZC-PL-30',
				'subtitle' => 'هر روز یک قدم کوچک؛ به‌جای انگیزه، روی سیستم کار کن',
				'short'    => 'برنامه‌ی دانلودی ۳۰ روزه با کاربرگ روزانه، ردیاب عادت و تمرین‌های کوتاه برای کسانی که کارها را به آخرین لحظه می‌رسانند و از این چرخه خسته‌اند.',
				'desc'     => '<h3>مسیر ۳۰ روزه</h3><ul><li><strong>هفته‌ی ۱:</strong> شناخت محرک‌ها و «فرار»های معمول</li><li><strong>هفته‌ی ۲:</strong> کوچک کردن کار تا حد «شروع‌شدنی»</li><li><strong>هفته‌ی ۳:</strong> مدیریت کمال‌گرایی و ترس از قضاوت</li><li><strong>هفته‌ی ۴:</strong> ساختن سیستم و مرور پیشرفت</li></ul><p>هر روز کمتر از ۱۵ دقیقه زمان می‌برد.</p>',
				'features' => "۳۰ کاربرگ روزانه + ۴ مرور هفتگی\nردیاب عادت و نوار پیشرفت\nPDF قابل چاپ (A4 و A5)\nتحویل فوری پس از پرداخت",
				'cross'    => array( 'workbook', 'audio' ),
			),
			'audio'     => array(
				'name'     => 'دوره‌ی صوتی «ذهن آرام، تصمیم روشن»',
				'slug'     => 'calm-mind-clear-decision',
				'kind'     => 'digital',
				'cat'      => 'audio-courses',
				'image'    => 'shop-audio',
				'gallery'  => array( 'calm-sofa', 'mountain-dawn' ),
				'regular'  => 490000,
				'sale'     => 390000,
				'sale_days' => 12,
				'featured' => true,
				'sku'      => 'ZC-AU-08',
				'subtitle' => '۸ جلسه‌ی صوتی برای مهار نشخوار فکری و تصمیم‌گیری بدون فرسودگی',
				'short'    => 'دوره‌ی صوتی ۸ جلسه‌ای (در مجموع ۲۴۰ دقیقه) با تمرین‌های هدایت‌شده‌ی ذهن‌آگاهی، کاربرگ PDF هر جلسه و برنامه‌ی تمرین روزانه.',
				'desc'     => '<h3>سرفصل‌ها</h3><ol><li>نشخوار فکری چیست و چرا «فکر کردن بیشتر» کمکی نمی‌کند؟</li><li>تنفس و لنگر انداختن در لحظه</li><li>جدا شدن از فکر: «من فکر نیستم»</li><li>تحمل ابهام و عدم قطعیت</li><li>ارزش‌ها؛ قطب‌نمای تصمیم</li><li>تصمیم‌گیری «به اندازه‌ی کافی خوب»</li><li>خستگی تصمیم و مدیریت انرژی</li><li>برنامه‌ی نگهداری و پیشگیری از بازگشت</li></ol><p>فایل‌ها MP3 با کیفیت بالا و قابل پخش در همه‌ی دستگاه‌ها هستند.</p>',
				'features' => "۸ جلسه‌ی صوتی، مجموعاً ۲۴۰ دقیقه\nتمرین هدایت‌شده‌ی ذهن‌آگاهی در هر جلسه\nکاربرگ PDF برای هر جلسه\nدسترسی فوری و دائمی به فایل‌ها",
				'faq'      => 'فایل‌ها آنلاین پخش می‌شوند یا دانلودی‌اند؟ | دانلودی‌اند؛ پس از دانلود بدون اینترنت هم قابل گوش دادن هستند.',
				'cross'    => array( 'workbook', 'planner' ),
			),

			/* ---------------------------------------------------------- جلسات */
			'session'   => array(
				'name'     => 'جلسه‌ی ارزیابی و نقشه‌ی الگو',
				'slug'     => 'assessment-session',
				'kind'     => 'session',
				'cat'      => 'coaching-sessions',
				'image'    => 'maryam-session',
				'gallery'  => array( 'maryam-online', 'counseling-room' ),
				'variable' => array(
					'name'    => 'نحوه‌ی برگزاری',
					'options' => array(
						'آنلاین (تماس تصویری امن)' => 300000,
						'حضوری در مطب بوشهر'       => 550000,
					),
					'default' => 'آنلاین (تماس تصویری امن)',
				),
				'sku'      => 'ZC-SS-45',
				'subtitle' => 'جلسه‌ی ۴۵ دقیقه‌ای برای شناخت الگوی غالب و طراحی مسیر همراهی',
				'short'    => 'نقطه‌ی شروع همراهی: در ۴۵ دقیقه موضوع اصلی، الگوهای غالب و هدف‌ها را روشن می‌کنیم و در پایان، نقشه‌ی مکتوب مسیر پیشنهادی را دریافت می‌کنی.',
				'desc'     => '<h3>در این جلسه</h3><ul><li>گفت‌وگوی ساختاریافته درباره‌ی موضوع و هدف تو</li><li>بررسی نتایج تست‌های خودسنجی (در صورت تکمیل)</li><li>شناسایی ۱ تا ۲ الگوی غالب</li><li>پیشنهاد مسیر: جلسات فردی، بسته‌ها یا منابع خودیاری</li></ul><h3>پس از خرید</h3><p>ظرف یک روز کاری برای هماهنگی زمان جلسه با تو تماس می‌گیرم. جلسات آنلاین فقط از بسترهای امن اعلام‌شده برگزار می‌شوند.</p>' . $session_note,
				'features' => "۴۵ دقیقه، حضوری در بوشهر یا آنلاین\nنقشه‌ی مکتوب مسیر پیشنهادی پس از جلسه\nهماهنگی زمان ظرف یک روز کاری\nلغو رایگان تا ۴۸ ساعت پیش از جلسه",
				'faq'      => "این جلسه درمان است؟ | خیر؛ جلسه‌ای آموزشی و رشدمحور است. اگر نیاز به خدمات تخصصی دیگری مطرح باشد، ارجاع مناسب پیشنهاد می‌شود.\nزمان جلسه را چطور انتخاب کنم؟ | پس از خرید برای هماهنگی تماس می‌گیرم؛ می‌توانی زمان‌های مناسبت را در یادداشت سفارش بنویسی.",
				'upsells'  => array( 'pack4', 'pack8' ),
			),
			'pack4'     => array(
				'name'     => 'بسته‌ی ۴ جلسه‌ای «از کمال‌گرایی تا اقدام»',
				'slug'     => 'perfectionism-to-action-pack',
				'kind'     => 'session',
				'cat'      => 'coaching-sessions',
				'image'    => 'maryam-online',
				'gallery'  => array( 'maryam-session' ),
				'regular'  => 2000000,
				'sale'     => 1800000,
				'sale_days' => 12,
				'featured' => true,
				'sku'      => 'ZC-PK-04',
				'badge'    => 'پرطرفدار',
				'subtitle' => '۴ جلسه‌ی ۴۵ دقیقه‌ای در ۴ تا ۶ هفته؛ از استانداردهای فرساینده به اقدام پیوسته',
				'short'    => 'برای کسانی که از ترس «کامل نبودن» شروع نمی‌کنند یا تمام نمی‌کنند. چهار جلسه‌ی فردی با تمرین بین جلسات و کاربرگ‌های اختصاصی.',
				'desc'     => '<h3>مسیر چهار جلسه</h3><ol><li>نقشه‌ی کمال‌گرایی: کجا کمک می‌کند و کجا فلج می‌کند؟</li><li>استانداردهای «به اندازه‌ی کافی خوب» و آزمایش‌های رفتاری</li><li>منتقد درونی و خودمهربانی عملی</li><li>سیستم اقدام پیوسته و برنامه‌ی نگهداری</li></ol><p>بسته ۹۰ روز اعتبار دارد و جلسات حضوری یا آنلاین برگزار می‌شوند.</p>' . $session_note,
				'features' => "۴ جلسه‌ی ۴۵ دقیقه‌ای فردی\nکاربرگ و تمرین اختصاصی بین جلسات\n۹۰ روز اعتبار از تاریخ خرید\nبازگشت وجه جلسات برگزارنشده",
			),
			'pack8'     => array(
				'name'     => 'بسته‌ی ۸ جلسه‌ای «ذهن مدیر»',
				'slug'     => 'manager-mind-pack',
				'kind'     => 'session',
				'cat'      => 'coaching-sessions',
				'image'    => 'maryam-workshop',
				'gallery'  => array( 'maryam-online' ),
				'regular'  => 4200000,
				'sku'      => 'ZC-PK-08',
				'badge'    => 'ویژه‌ی مدیران',
				'subtitle' => '۸ جلسه برای مدیرانی که می‌خواهند با ذهنی مرتب‌تر تصمیم بگیرند و رهبری کنند',
				'short'    => 'برنامه‌ی فردی «ذهن مدیر» برای شناخت الگوهای کنترل‌گری، کمال‌گرایی و تصمیم‌های معوق؛ با ارزیابی آغازین، برنامه‌ی شخصی و جلسه‌ی مرور پایانی.',
				'desc'     => '<h3>برای چه کسی؟</h3><p>مدیران ۲۸ تا ۴۵ ساله‌ای که مسئولیت تیم دارند و احساس می‌کنند الگوهای شخصی‌شان روی تصمیم‌ها، تفویض و روابط کاری اثر گذاشته است.</p><h3>محورها</h3><ul><li>کنترل‌گری و تفویض</li><li>خستگی تصمیم و اولویت‌بندی</li><li>گفت‌وگوهای دشوار و مرزهای کاری</li><li>تعادل کار و زندگی شخصی</li></ul><p>بسته ۱۲۰ روز اعتبار دارد.</p>' . $session_note,
				'features' => "۸ جلسه‌ی ۴۵ دقیقه‌ای فردی\nارزیابی آغازین و برنامه‌ی شخصی\nجلسه‌ی مرور پایانی و برنامه‌ی نگهداری\n۱۲۰ روز اعتبار از تاریخ خرید",
			),
		);
	}
endif;

/* =========================================================================
 * نصب
 * ========================================================================= */

if ( ! function_exists( 'zc_demo_shop_settings' ) ) :
	/**
	 * تنظیمات عمومی ووکامرس برای فروشگاه ایرانی.
	 *
	 * @return void
	 */
	function zc_demo_shop_settings() {
		$options = array(
			'woocommerce_currency'                        => 'IRT',
			'woocommerce_currency_pos'                    => 'right_space',
			'woocommerce_price_thousand_sep'              => ',',
			'woocommerce_price_decimal_sep'               => '.',
			'woocommerce_price_num_decimals'              => '0',
			'woocommerce_default_country'                 => 'IR:BHR',
			'woocommerce_store_city'                      => 'بوشهر',
			'woocommerce_allowed_countries'               => 'specific',
			'woocommerce_specific_allowed_countries'      => array( 'IR' ),
			'woocommerce_ship_to_countries'               => '',
			'woocommerce_default_customer_address'        => 'base',
			'woocommerce_calc_taxes'                      => 'no',
			'woocommerce_weight_unit'                     => 'kg',
			'woocommerce_dimension_unit'                  => 'cm',
			'woocommerce_enable_reviews'                  => 'yes',
			'woocommerce_enable_review_rating'            => 'yes',
			'woocommerce_review_rating_required'          => 'yes',
			'woocommerce_review_rating_verification_label' => 'yes',
			'woocommerce_review_rating_verification_required' => 'yes',
			'woocommerce_enable_guest_checkout'           => 'yes',
			'woocommerce_enable_checkout_login_reminder'  => 'yes',
			'woocommerce_enable_signup_and_login_from_checkout' => 'yes',
			'woocommerce_enable_myaccount_registration'   => 'yes',
			'woocommerce_registration_generate_password'  => 'yes',
			'woocommerce_downloads_grant_access_after_payment' => 'yes',
			'woocommerce_downloads_require_login'         => 'no',
			'woocommerce_manage_stock'                    => 'yes',
			'woocommerce_notify_low_stock_amount'         => '5',
			'woocommerce_hold_stock_minutes'              => '60',
			'woocommerce_cart_redirect_after_add'         => 'no',
			'woocommerce_enable_ajax_add_to_cart'         => 'yes',
			'woocommerce_coming_soon'                     => 'no',
			'woocommerce_store_pages_only'                => 'no',
			'woocommerce_email_from_name'                 => 'مریم جمالی',
			'woocommerce_checkout_privacy_policy_text'    => 'اطلاعات شما فقط برای پردازش سفارش، ارسال و پشتیبانی استفاده می‌شود و طبق [privacy_policy] محرمانه می‌ماند.',
			'woocommerce_registration_privacy_policy_text' => 'اطلاعات شما برای مدیریت حساب کاربری و سفارش‌ها استفاده می‌شود و طبق [privacy_policy] محرمانه می‌ماند.',
			'woocommerce_checkout_terms_and_conditions_checkbox_text' => '[terms] را خوانده‌ام و می‌پذیرم.',
		);
		foreach ( $options as $key => $value ) {
			update_option( $key, $value );
		}

		// روش‌های پرداخت آفلاین (درگاه زرین‌پال بعداً افزوده می‌شود).
		update_option(
			'woocommerce_bacs_settings',
			array(
				'enabled'         => 'yes',
				'title'           => 'کارت به کارت / واریز به حساب',
				'description'     => 'پس از ثبت سفارش، شماره‌ی کارت و شبا برای واریز نمایش داده می‌شود. سفارش پس از تأیید واریز (معمولاً همان روز کاری) پردازش می‌شود.',
				'instructions'    => 'لطفاً مبلغ سفارش را به حساب اعلام‌شده واریز کنید و شماره‌ی پیگیری را با ذکر شماره‌ی سفارش از طریق تلفن یا ایمیل ارسال کنید. سفارش‌های بدون واریز پس از ۴۸ ساعت لغو می‌شوند.',
				'account_details' => '',
			)
		);
		update_option(
			'woocommerce_cod_settings',
			array(
				'enabled'            => 'yes',
				'title'              => 'پرداخت در محل',
				'description'        => 'پرداخت با کارتخوان هنگام تحویل مرسوله (فقط برای سفارش‌های کالای فیزیکی).',
				'instructions'       => 'مبلغ سفارش را هنگام تحویل مرسوله به مأمور پست پرداخت کنید.',
				'enable_for_methods' => array(),
				'enable_for_virtual' => 'no',
			)
		);
		update_option( 'woocommerce_cheque_settings', array( 'enabled' => 'no' ) );
		update_option( 'woocommerce_gateway_order', array( 'bacs' => 0, 'cod' => 1, 'cheque' => 2 ) );
	}
endif;

if ( ! function_exists( 'zc_demo_shop_pages' ) ) :
	/**
	 * برگه‌های ووکامرس: ایجاد در صورت نبود و عنوان فارسی.
	 *
	 * @return array<string, int>
	 */
	function zc_demo_shop_pages() {
		if ( class_exists( 'WC_Install' ) ) {
			WC_Install::create_pages();
		}
		$map = array(
			'shop'      => array( 'فروشگاه', 'shop' ),
			'cart'      => array( 'سبد خرید', 'cart' ),
			'checkout'  => array( 'تسویه حساب', 'checkout' ),
			'myaccount' => array( 'حساب کاربری', 'my-account' ),
		);
		$ids = array();
		foreach ( $map as $key => $data ) {
			$id = (int) wc_get_page_id( $key );
			if ( $id <= 0 || ! get_post( $id ) ) {
				continue;
			}
			$update = array(
				'ID'          => $id,
				'post_title'  => $data[0],
				'post_name'   => $data[1],
				'post_status' => 'publish',
			);
			// قالب برای سبد و تسویه‌ی کلاسیک (شورت‌کد) طراحی شده است؛ ووکامرس جدید برگه‌ها را بلوکی می‌سازد.
			if ( 'cart' === $key || 'checkout' === $key ) {
				$update['post_content'] = '<!-- wp:shortcode -->[woocommerce_' . $key . ']<!-- /wp:shortcode -->';
			}
			wp_update_post( wp_slash( $update ) );
			update_post_meta( $id, '_zc_demo_wc_page', $key );
			$ids[ $key ] = $id;
		}
		$shipping = zc_demo_find_post( 'page', '_zc_demo_page', 'shipping' );
		if ( $shipping ) {
			update_option( 'woocommerce_terms_page_id', $shipping );
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'zc_demo_shop_sample_file' ) ) :
	/**
	 * فایل نمونه‌ی محصولات دانلودی (PDF) در پوشه‌ی بارگذاری‌ها (مسیر مجاز دانلود ووکامرس).
	 *
	 * @return string نشانی فایل.
	 */
	function zc_demo_shop_sample_file() {
		$found = get_posts(
			array(
				'post_type'      => 'attachment',
				'post_status'    => 'inherit',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_zc_demo_media', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'     => 'shop-sample-pdf', // phpcs:ignore WordPress.DB.SlowDBQuery
			)
		);
		if ( $found ) {
			return (string) wp_get_attachment_url( (int) $found[0] );
		}
		$source = ZC_DIR . '/assets/demo/shop-sample.pdf';
		if ( ! is_readable( $source ) ) {
			return '';
		}
		$upload = wp_upload_bits( 'zc-demo-shop-sample.pdf', null, (string) file_get_contents( $source ) ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( ! empty( $upload['error'] ) ) {
			return '';
		}
		$id = wp_insert_attachment(
			array(
				'post_mime_type' => 'application/pdf',
				'post_title'     => 'فایل نمونه‌ی محصولات دانلودی',
				'post_status'    => 'inherit',
			),
			$upload['file']
		);
		if ( $id && ! is_wp_error( $id ) ) {
			update_post_meta( $id, '_zc_demo_media', 'shop-sample-pdf' );
			zc_demo_track( 'media', (int) $id );
		}
		return (string) $upload['url'];
	}
endif;

if ( ! function_exists( 'zc_demo_shop_shipping' ) ) :
	/**
	 * ناحیه‌ی ارسال ایران: پست پیشتاز + تحویل حضوری در مطب.
	 *
	 * @return void
	 */
	function zc_demo_shop_shipping() {
		if ( ! class_exists( 'WC_Shipping_Zone' ) ) {
			return;
		}
		$state = (array) get_option( 'zc_demo_shop', array() );
		if ( ! empty( $state['zone'] ) ) {
			$zone = new WC_Shipping_Zone( (int) $state['zone'] );
			if ( $zone->get_id() ) {
				return;
			}
		}
		$zone = new WC_Shipping_Zone();
		$zone->set_zone_name( 'ایران' );
		$zone->set_zone_order( 0 );
		$zone->add_location( 'IR', 'country' );
		$zone->save();

		$flat = $zone->add_shipping_method( 'flat_rate' );
		if ( $flat ) {
			update_option(
				'woocommerce_flat_rate_' . $flat . '_settings',
				array(
					'title'      => 'پست پیشتاز (۲ تا ۵ روز کاری)',
					'tax_status' => 'none',
					'cost'       => '60000',
				)
			);
		}
		$pickup = $zone->add_shipping_method( 'local_pickup' );
		if ( $pickup ) {
			update_option(
				'woocommerce_local_pickup_' . $pickup . '_settings',
				array(
					'title'      => 'تحویل حضوری در مطب (بوشهر)',
					'tax_status' => 'none',
					'cost'       => '0',
				)
			);
		}
		$state['zone'] = (int) $zone->get_id();
		update_option( 'zc_demo_shop', $state, false );
		zc_demo_shop_flush_shipping();
	}
endif;

if ( ! function_exists( 'zc_demo_shop_flush_shipping' ) ) :
	/**
	 * پاک‌سازی کش روش‌های ارسال (نسخه‌ی گذرای ووکامرس بر پایه‌ی ثانیه است و حذف و ساخت در یک ثانیه را تشخیص نمی‌دهد).
	 *
	 * @return void
	 */
	function zc_demo_shop_flush_shipping() {
		delete_transient( 'wc_shipping_method_count' );
		delete_transient( 'wc_shipping_method_count_legacy' );
		if ( class_exists( 'WC_Cache_Helper' ) ) {
			WC_Cache_Helper::get_transient_version( 'shipping', true );
			WC_Cache_Helper::invalidate_cache_group( 'shipping_zones' );
		}
	}
endif;

if ( ! function_exists( 'zc_demo_shop_install' ) ) :
	/**
	 * نصب کامل دموی فروشگاه.
	 *
	 * @return string پیام.
	 */
	function zc_demo_shop_install() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return __( 'ووکامرس فعال نیست؛ دموی فروشگاه رد شد.', 'zarincoach' );
		}

		zc_demo_shop_settings();
		$pages = zc_demo_shop_pages();
		zc_demo_shop_shipping();
		$media = zc_demo_media_ids();
		$file  = zc_demo_shop_sample_file();
		$tz    = wp_timezone();

		// دسته‌ها.
		$cats = array();
		foreach ( zc_demo_shop_categories() as $slug => $cat ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( ! $term ) {
				$made = wp_insert_term( $cat['name'], 'product_cat', array( 'slug' => $slug, 'description' => $cat['desc'] ) );
				if ( is_wp_error( $made ) ) {
					continue;
				}
				$term_id = (int) $made['term_id'];
				zc_demo_track( 'product_cats', $term_id );
			} else {
				$term_id = (int) $term->term_id;
				wp_update_term( $term_id, 'product_cat', array( 'name' => $cat['name'], 'description' => $cat['desc'] ) );
			}
			if ( ! empty( $media[ $cat['image'] ] ) ) {
				update_term_meta( $term_id, 'thumbnail_id', (int) $media[ $cat['image'] ] );
			}
			$cats[ $slug ] = $term_id;
		}
		// ترتیب نمایش دسته‌ها.
		$order = 0;
		foreach ( $cats as $term_id ) {
			update_term_meta( $term_id, 'order', $order++ );
		}

		// محصولات (دو گذر: ساخت، سپس پیوند فروش مکمل/جایگزین).
		$made = array();
		$defs = zc_demo_shop_products();
		$menu = 0;
		foreach ( $defs as $key => $def ) {
			$existing = zc_demo_find_post( 'product', '_zc_demo_product', $key );
			if ( $existing ) {
				$made[ $key ] = $existing;
				continue;
			}
			$is_var  = ! empty( $def['variable'] );
			$product = $is_var ? new WC_Product_Variable() : new WC_Product_Simple();
			$product->set_name( $def['name'] );
			$product->set_slug( $def['slug'] );
			$product->set_status( 'publish' );
			$product->set_catalog_visibility( 'visible' );
			$product->set_featured( ! empty( $def['featured'] ) );
			$product->set_short_description( $def['short'] );
			$product->set_description( $def['desc'] );
			$product->set_sku( $def['sku'] );
			$product->set_menu_order( $menu++ );
			$product->set_reviews_allowed( 'session' !== $def['kind'] );
			if ( isset( $cats[ $def['cat'] ] ) ) {
				$product->set_category_ids( array( $cats[ $def['cat'] ] ) );
			}
			if ( ! empty( $media[ $def['image'] ] ) ) {
				$product->set_image_id( (int) $media[ $def['image'] ] );
			}
			$gallery = array();
			foreach ( (array) ( $def['gallery'] ?? array() ) as $g ) {
				if ( ! empty( $media[ $g ] ) ) {
					$gallery[] = (int) $media[ $g ];
				}
			}
			$product->set_gallery_image_ids( $gallery );

			if ( ! $is_var ) {
				$product->set_regular_price( (string) $def['regular'] );
				if ( ! empty( $def['sale'] ) ) {
					$product->set_sale_price( (string) $def['sale'] );
					if ( ! empty( $def['sale_days'] ) ) {
						$end = new DateTime( 'today 23:59:59', $tz );
						$end->modify( '+' . (int) $def['sale_days'] . ' days' );
						$product->set_date_on_sale_to( $end->getTimestamp() );
					}
				}
			}

			if ( 'physical' === $def['kind'] ) {
				$product->set_manage_stock( true );
				$product->set_stock_quantity( (int) $def['stock'] );
				$product->set_low_stock_amount( 5 );
				$product->set_weight( $def['weight'] );
				if ( ! empty( $def['dims'] ) ) {
					$product->set_length( (string) $def['dims'][0] );
					$product->set_width( (string) $def['dims'][1] );
					$product->set_height( (string) $def['dims'][2] );
				}
			} else {
				$product->set_virtual( true );
				$product->set_sold_individually( true );
			}
			if ( 'digital' === $def['kind'] && '' !== $file ) {
				$product->set_downloadable( true );
				$download = new WC_Product_Download();
				$download->set_id( wp_generate_uuid4() );
				$download->set_name( $def['name'] . ' (فایل نمونه‌ی دمو)' );
				$download->set_file( $file );
				$product->set_downloads( array( $download ) );
				$product->set_download_limit( 5 );
				$product->set_download_expiry( 365 );
			}

			if ( $is_var ) {
				$attr = new WC_Product_Attribute();
				$attr->set_id( 0 );
				$attr->set_name( $def['variable']['name'] );
				$attr->set_options( array_keys( $def['variable']['options'] ) );
				$attr->set_visible( true );
				$attr->set_variation( true );
				$product->set_attributes( array( $attr ) );
				$product->set_default_attributes( array( sanitize_title( $def['variable']['name'] ) => $def['variable']['default'] ) );
			}

			$meta = array(
				'_zc_demo_product' => $key,
				'_zc_kind'         => $def['kind'],
				'_zc_subtitle'     => $def['subtitle'],
				'_zc_features'     => $def['features'],
				'_zc_faq'          => $def['faq'] ?? '',
				'_zc_badge'        => $def['badge'] ?? '',
				'_zc_schema_type'  => $def['schema'] ?? '',
			);
			foreach ( $meta as $mk => $mv ) {
				$product->update_meta_data( $mk, $mv );
			}

			try {
				$id = (int) $product->save();
			} catch ( Exception $e ) {
				continue;
			}
			if ( ! $id ) {
				continue;
			}

			if ( $is_var ) {
				$attr_key = sanitize_title( $def['variable']['name'] );
				foreach ( $def['variable']['options'] as $label => $price ) {
					$variation = new WC_Product_Variation();
					$variation->set_parent_id( $id );
					$variation->set_attributes( array( $attr_key => $label ) );
					$variation->set_regular_price( (string) $price );
					$variation->set_virtual( true );
					$variation->set_status( 'publish' );
					$variation->save();
				}
				WC_Product_Variable::sync( $id );
			}

			zc_demo_track( 'products', $id );
			$made[ $key ] = $id;
		}

		foreach ( $defs as $key => $def ) {
			if ( empty( $made[ $key ] ) ) {
				continue;
			}
			$product = wc_get_product( $made[ $key ] );
			if ( ! $product ) {
				continue;
			}
			$map = static function ( $keys ) use ( $made ) {
				return array_values( array_filter( array_map( static function ( $k ) use ( $made ) { return $made[ $k ] ?? 0; }, (array) $keys ) ) );
			};
			$product->set_upsell_ids( $map( $def['upsells'] ?? array() ) );
			$product->set_cross_sell_ids( $map( $def['cross'] ?? array() ) );
			$product->save();
		}

		// کد تخفیف خوش‌آمد (جلسات مشمول تخفیف نیستند؛ تعرفه‌ی مصوب).
		$coupon_id = zc_demo_find_post( 'shop_coupon', '_zc_demo_coupon', 'welcome' );
		if ( ! $coupon_id ) {
			$coupon = new WC_Coupon();
			$coupon->set_code( 'WELCOME15' );
			$coupon->set_description( 'کد خوش‌آمد دمو: ۱۵٪ تخفیف اولین خرید کتاب، ژورنال، کارت و فایل‌های دانلودی.' );
			$coupon->set_discount_type( 'percent' );
			$coupon->set_amount( 15 );
			$coupon->set_individual_use( true );
			$coupon->set_usage_limit_per_user( 1 );
			$coupon->set_minimum_amount( '200000' );
			$coupon->set_excluded_product_categories( isset( $cats['coaching-sessions'] ) ? array( $cats['coaching-sessions'] ) : array() );
			$coupon->set_date_expires( ( new DateTime( 'today 23:59:59', $tz ) )->modify( '+45 days' )->getTimestamp() );
			$coupon->update_meta_data( '_zc_demo_coupon', 'welcome' );
			$coupon_id = (int) $coupon->save();
			zc_demo_track( 'coupons', $coupon_id );
		}

		// طراحی المنتوری برگه‌ی فروشگاه (بالای شبکه‌ی محصولات، صفحه‌ی اول بایگانی).
		if ( ! empty( $pages['shop'] ) && function_exists( 'zc_elementor_save' ) && zc_is_elementor_active() ) {
			zc_elementor_save( (int) $pages['shop'], zc_demo_shop_page_sections() );
			update_post_meta( (int) $pages['shop'], '_zc_demo_shop_elementor', '1' );
		}

		wc_delete_product_transients();
		delete_transient( 'wc_term_counts' );
		_wc_term_recount( get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => false ) ), get_taxonomy( 'product_cat' ), true, false );

		/* translators: 1: محصولات 2: دسته‌ها */
		return sprintf( __( 'فروشگاه آماده شد: %1$d محصول نمونه در %2$d دسته، کد تخفیف WELCOME15، ارسال پستی و پرداخت آفلاین.', 'zarincoach' ), count( $made ), count( $cats ) );
	}
endif;

if ( ! function_exists( 'zc_demo_shop_page_sections' ) ) :
	/**
	 * بخش‌های المنتوری برگه‌ی فروشگاه.
	 *
	 * @return array
	 */
	function zc_demo_shop_page_sections() {
		$data   = array();
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'product-cats',
				array(
					'eyebrow'    => 'دسته‌بندی‌ها',
					'title'      => 'از کجا شروع کنیم؟',
					'subtitle'   => 'ابزار مناسب مسیرت را انتخاب کن؛ از جلسه‌ی ارزیابی تا کارپوشه‌ها و کتاب‌های چاپی.',
					'style'      => 'tiles',
					'cols'       => '4',
					'show_count' => 'yes',
					'show_desc'  => '',
				)
			)
		);
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'shop-promo',
				array(
					'eyebrow'      => 'پیشنهاد خوش‌آمد',
					'title'        => '۱۵٪ تخفیف اولین خرید از فروشگاه',
					'text'         => 'برای کتاب‌ها، ژورنال، کارت‌ها و همه‌ی فایل‌های دانلودی؛ کافی است کد زیر را در سبد خرید وارد کنی. (جلسات بر اساس تعرفه‌ی مصوب ارائه می‌شوند و مشمول کد تخفیف نیستند.)',
					'coupon'       => 'WELCOME15',
					'end'          => '',
					'hide_expired' => 'yes',
					'button_text'  => 'مشاهده‌ی محصولات',
					'button_url'   => zc_elementor_url( '#zc-products' ),
					'button_style' => 'primary',
				)
			)
		);
		return $data;
	}
endif;

if ( ! function_exists( 'zc_demo_shop_home_sections' ) ) :
	/**
	 * بخش‌های فروشگاهی صفحه‌ی نخست.
	 *
	 * @return array
	 */
	function zc_demo_shop_home_sections() {
		if ( ! class_exists( 'WooCommerce' ) ) {
			return array();
		}
		$shop   = function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/shop/' );
		$book   = zc_demo_find_post( 'product', '_zc_demo_product', 'workbook' );
		$data   = array();
		$data[] = zc_elementor_section(
			zc_elementor_widget(
				'products',
				array(
					'eyebrow'      => 'فروشگاه',
					'title'        => 'ابزارهایی برای ادامه‌ی مسیر در خانه',
					'subtitle'     => 'کارپوشه‌ها، دوره‌های صوتی و کتاب‌هایی که بین جلسات یا مستقل از آن‌ها کمکت می‌کنند الگوها را ببینی و تمرین کنی.',
					'align'        => 'start',
					'source'       => 'featured',
					'count'        => 8,
					'orderby'      => 'menu_order',
					'layout'       => 'carousel',
					'cols'         => '4',
					'cols_md'      => '2',
					'cols_sm'      => '1',
					'button_text'  => 'همه‌ی محصولات',
					'button_url'   => zc_elementor_url( $shop ),
					'button_style' => 'outline',
				)
			)
		);
		if ( $book ) {
			$data[] = zc_elementor_section(
				zc_elementor_widget(
					'product-spotlight',
					array(
						'product_id'     => (string) $book,
						'eyebrow'        => 'پیشنهاد این هفته',
						'media_side'     => 'start',
						'show_features'  => 'yes',
						'show_countdown' => 'yes',
						'show_rating'    => 'yes',
						'show_details'   => 'yes',
					)
				)
			);
		}
		return $data;
	}
endif;

/* =========================================================================
 * حذف
 * ========================================================================= */

if ( ! function_exists( 'zc_demo_shop_reset' ) ) :
	/**
	 * حذف کامل دموی فروشگاه (محصولات و گونه‌ها، کوپن، دسته‌ها، ناحیه‌ی ارسال، طراحی برگه‌ی فروشگاه).
	 *
	 * @return int تعداد موارد حذف‌شده.
	 */
	function zc_demo_shop_reset() {
		global $wpdb;
		$count   = 0;
		$objects = (array) get_option( 'zc_demo_objects', array() );

		if ( ! class_exists( 'WooCommerce' ) ) {
			return 0;
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$ids = array_map( 'intval', (array) $wpdb->get_col( $wpdb->prepare( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key IN (%s, %s)", '_zc_demo_product', '_zc_demo_coupon' ) ) );
		$ids = array_unique( array_merge( $ids, array_map( 'intval', (array) ( $objects['products'] ?? array() ) ), array_map( 'intval', (array) ( $objects['coupons'] ?? array() ) ) ) );
		foreach ( $ids as $id ) {
			$post = get_post( $id );
			if ( ! $post ) {
				continue;
			}
			if ( 'product' === $post->post_type ) {
				$product = wc_get_product( $id );
				if ( $product ) {
					foreach ( $product->get_children() as $child ) {
						wp_delete_post( (int) $child, true );
					}
					$product->delete( true );
					$count++;
				}
			} elseif ( 'shop_coupon' === $post->post_type && wp_delete_post( $id, true ) ) {
				$count++;
			}
		}

		// دسته‌های خالی‌شده‌ی دمو.
		$terms = array_map( 'intval', (array) ( $objects['product_cats'] ?? array() ) );
		foreach ( array_keys( zc_demo_shop_categories() ) as $slug ) {
			$term = get_term_by( 'slug', $slug, 'product_cat' );
			if ( $term ) {
				$terms[] = (int) $term->term_id;
			}
		}
		$default = (int) get_option( 'default_product_cat' );
		foreach ( array_unique( $terms ) as $term_id ) {
			clean_term_cache( $term_id, 'product_cat' );
			$term = get_term( $term_id, 'product_cat' );
			if ( $term && ! is_wp_error( $term ) && $term_id !== $default && 0 === (int) $term->count && ! is_wp_error( wp_delete_term( $term_id, 'product_cat' ) ) ) {
				$count++;
			}
		}

		// ناحیه‌ی ارسال.
		$state = (array) get_option( 'zc_demo_shop', array() );
		if ( ! empty( $state['zone'] ) && class_exists( 'WC_Shipping_Zone' ) ) {
			$zone = new WC_Shipping_Zone( (int) $state['zone'] );
			if ( $zone->get_id() ) {
				$zone->delete();
				$count++;
			}
		}
		delete_option( 'zc_demo_shop' );
		zc_demo_shop_flush_shipping();

		// طراحی المنتوری برگه‌ی فروشگاه (خود برگه‌های ووکامرس حذف نمی‌شوند).
		foreach ( get_posts( array( 'post_type' => 'page', 'posts_per_page' => 10, 'fields' => 'ids', 'meta_key' => '_zc_demo_shop_elementor' ) ) as $page_id ) { // phpcs:ignore WordPress.DB.SlowDBQuery
			foreach ( array( '_elementor_data', '_elementor_edit_mode', '_elementor_template_type', '_elementor_version', '_elementor_css', '_zc_demo_shop_elementor' ) as $meta ) {
				delete_post_meta( (int) $page_id, $meta );
			}
		}

		wc_delete_product_transients();
		return $count;
	}
endif;
