<?php
/**
 * تنظیمات بخش‌های صفحه اصلی (Redux)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_sections_home' ) ) :
	/**
	 * بخش‌های مربوط به صفحه اصلی.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function zc_sections_home() {
		return array(
			/* ---------------- صفحه اصلی: چیدمان ---------------- */
			array(
				'title'  => __( 'صفحه اصلی', 'zarincoach' ),
				'id'     => 'home',
				'icon'   => 'el el-home',
				'desc'   => __( 'ترتیب و محتوای بخش‌های صفحه اصلی. هر بخش را می‌توانید با المنتور هم بازطراحی کنید.', 'zarincoach' ),
				'fields' => array(
					array(
						'id'      => 'home_sections_order',
						'type'    => 'sorter',
						'title'   => __( 'ترتیب بخش‌ها', 'zarincoach' ),
						'desc'    => __( 'برای جابه‌جایی، هر مورد را بکشید و در ستون مورد نظر رها کنید.', 'zarincoach' ),
						'options' => array(
							'enabled'  => array(
								'hero'         => __( 'سربرگ اصلی (Hero)', 'zarincoach' ),
								'marquee'      => __( 'نوار کلمات کلیدی', 'zarincoach' ),
								'about'        => __( 'درباره من', 'zarincoach' ),
								'services'     => __( 'خدمات و برنامه‌ها', 'zarincoach' ),
								'schema'       => __( 'طرحواره‌ها', 'zarincoach' ),
								'process'      => __( 'مسیر همراهی', 'zarincoach' ),
								'stats'        => __( 'آمار و ارقام', 'zarincoach' ),
								'testimonials' => __( 'تجربه مراجعان', 'zarincoach' ),
								'faq'          => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
								'blog'         => __( 'آخرین نوشته‌ها', 'zarincoach' ),
								'cta'          => __( 'فراخوان اقدام', 'zarincoach' ),
							),
							'disabled' => array(),
						),
						'default' => array(
							'enabled'  => array(
								'hero'         => __( 'سربرگ اصلی (Hero)', 'zarincoach' ),
								'marquee'      => __( 'نوار کلمات کلیدی', 'zarincoach' ),
								'about'        => __( 'درباره من', 'zarincoach' ),
								'services'     => __( 'خدمات و برنامه‌ها', 'zarincoach' ),
								'schema'       => __( 'طرحواره‌ها', 'zarincoach' ),
								'process'      => __( 'مسیر همراهی', 'zarincoach' ),
								'stats'        => __( 'آمار و ارقام', 'zarincoach' ),
								'testimonials' => __( 'تجربه مراجعان', 'zarincoach' ),
								'faq'          => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
								'blog'         => __( 'آخرین نوشته‌ها', 'zarincoach' ),
								'cta'          => __( 'فراخوان اقدام', 'zarincoach' ),
							),
							'disabled' => array(),
						),
					),
					array(
						'id'    => 'home_marquee_words',
						'type'  => 'multi_text',
						'title' => __( 'کلمات نوار لغزنده', 'zarincoach' ),
						'desc'  => __( 'کلیدواژه‌های اصلی حوزه‌ی کاری شما.', 'zarincoach' ),
						'add_text' => __( 'افزودن کلمه', 'zarincoach' ),
						'default' => array(
							'شناخت الگو',
							'کمال‌گرایی',
							'اهمالکاری',
							'تصمیم‌گیری',
							'ذهن مرتب',
							'مهارت‌های زندگی',
							'مرزبندی و نه گفتن',
							'حل مسئله',
							'ذهن مدیر',
							'خودکفایی',
						),
					),
				),
			),

			/* ---------------- هیرو ---------------- */
			array(
				'title'      => __( 'سربرگ اصلی', 'zarincoach' ),
				'id'         => 'home-hero',
				'subsection' => true,
				'icon'       => 'el el-photo',
				'fields'     => array(
					array(
						'id'      => 'home_hero_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_hero_style',
						'type'    => 'button_set',
						'title'   => __( 'سبک نمایش', 'zarincoach' ),
						'options' => array(
							'split'    => __( 'دو ستونه با تصویر', 'zarincoach' ),
							'centered' => __( 'متن میانی', 'zarincoach' ),
							'minimal'  => __( 'مینیمال', 'zarincoach' ),
						),
						'default' => 'split',
					),
					array(
						'id'      => 'home_hero_badge',
						'type'    => 'text',
						'title'   => __( 'برچسب بالای عنوان', 'zarincoach' ),
						'default' => 'روان‌شناس · آموزش مهارت‌های زندگی · رویکرد طرحواره',
					),
					array(
						'id'      => 'home_hero_title',
						'type'    => 'text',
						'title'   => __( 'عنوان اصلی', 'zarincoach' ),
						'default' => 'از شناخت الگوها تا تصمیم و اقدام',
					),
					array(
						'id'      => 'home_hero_title_accent',
						'type'    => 'text',
						'title'   => __( 'بخش رنگی عنوان', 'zarincoach' ),
						'desc'    => __( 'این عبارت با رنگ گرادیان پالت نمایش داده می‌شود.', 'zarincoach' ),
						'default' => 'تصمیم و اقدام',
					),
					array(
						'id'      => 'home_hero_subtitle',
						'type'    => 'textarea',
						'title'   => __( 'زیرعنوان', 'zarincoach' ),
						'default' => 'اینجا قرار نیست کسی قضاوت‌تان کند؛ قرار است با هم الگوهایی مثل کمال‌گرایی، اهمالکاری و تصمیم‌گیری‌های ناکارآمد را بشناسیم و از شناخت برسیم به وضوح، اقدام و عملکرد بهتر؛ با زبان ساده، تمرین مشخص و همراهی تا نتیجه.',
					),
					array(
						'id'      => 'home_hero_primary_text',
						'type'    => 'text',
						'title'   => __( 'متن دکمه اول', 'zarincoach' ),
						'default' => 'رزرو جلسه ارزیابی اولیه',
					),
					array(
						'id'      => 'home_hero_primary_url',
						'type'    => 'text',
						'title'   => __( 'لینک دکمه اول', 'zarincoach' ),
						'default' => '/booking/',
					),
					array(
						'id'      => 'home_hero_secondary_text',
						'type'    => 'text',
						'title'   => __( 'متن دکمه دوم', 'zarincoach' ),
						'default' => 'شروع با تست‌های سایت',
					),
					array(
						'id'      => 'home_hero_secondary_url',
						'type'    => 'text',
						'title'   => __( 'لینک دکمه دوم', 'zarincoach' ),
						'default' => '/assessments/',
					),
					array(
						'id'      => 'home_hero_image',
						'type'    => 'media',
						'title'   => __( 'تصویر', 'zarincoach' ),
						'default' => array( 'url' => '' ),
					),
					array(
						'id'      => 'home_hero_stat1_num',
						'type'    => 'text',
						'title'   => __( 'آمار ۱ — عدد', 'zarincoach' ),
						'default' => '۶',
					),
					array(
						'id'      => 'home_hero_stat1_label',
						'type'    => 'text',
						'title'   => __( 'آمار ۱ — عنوان', 'zarincoach' ),
						'default' => 'محیط تجربه میدانی',
					),
					array(
						'id'      => 'home_hero_stat2_num',
						'type'    => 'text',
						'title'   => __( 'آمار ۲ — عدد', 'zarincoach' ),
						'default' => '۱۰',
					),
					array(
						'id'      => 'home_hero_stat2_label',
						'type'    => 'text',
						'title'   => __( 'آمار ۲ — عنوان', 'zarincoach' ),
						'default' => 'مهارت زندگی (الگوی WHO)',
					),
					array(
						'id'      => 'home_hero_stat3_num',
						'type'    => 'text',
						'title'   => __( 'آمار ۳ — عدد', 'zarincoach' ),
						'default' => '۵',
					),
					array(
						'id'      => 'home_hero_stat3_label',
						'type'    => 'text',
						'title'   => __( 'آمار ۳ — عنوان', 'zarincoach' ),
						'default' => 'گام از الگو تا اقدام',
					),
				),
			),

			/* ---------------- درباره ---------------- */
			array(
				'title'      => __( 'درباره من', 'zarincoach' ),
				'id'         => 'home-about',
				'subsection' => true,
				'icon'       => 'el el-user',
				'fields'     => array(
					array(
						'id'      => 'home_about_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_about_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'درباره من',
					),
					array(
						'id'      => 'home_about_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'من مریم جمالی‌ام؛ روان‌شناس الگوهای ذهنی و رفتاری',
					),
					array(
						'id'      => 'home_about_content',
						'type'    => 'textarea',
						'title'   => __( 'متن', 'zarincoach' ),
						'default' => "خودم سال‌ها یک کمال‌گرای مضطرب بودم؛ همیشه شاگرد اول، دو سال پشت کنکور پزشکی و بعد، انتخاب آگاهانه‌ی روان‌شناسی از سر علاقه. همین تجربه به من یاد داد که «دانستن» با «تغییر» فرق دارد.\n\nدر مدرسه، بهزیستی، خط ۱۴۸۰، خانه امن، کلینیک و کنار دانشجویان علوم پزشکی دیده‌ام که آدم‌ها وقتی الگوی خودشان را می‌شناسند، از سردرگمی به تصمیم می‌رسند. کار من آموزش همین مسیر است؛ ساختارمند، تمرین‌محور و همراه با پیگیری. هدفم فقط حال بهتر نیست؛ خودکفایی بیشتر است.",
					),
					array(
						'id'      => 'home_about_image',
						'type'    => 'media',
						'title'   => __( 'تصویر', 'zarincoach' ),
						'default' => array( 'url' => '' ),
					),
					array(
						'id'       => 'home_about_features',
						'type'     => 'multi_text',
						'title'    => __( 'ویژگی‌ها', 'zarincoach' ),
						'add_text' => __( 'افزودن مورد', 'zarincoach' ),
						'default'  => array(
							'آموزش ده مهارت زندگی بر پایه‌ی الگوی سازمان جهانی بهداشت',
							'کار با طرحواره‌ها و سبک‌های مقابله‌ای در مسیر رشد فردی',
							'تجربه‌ی مشاوره در مدرسه، بهزیستی، دانشگاه و خط ۱۴۸۰',
							'تمرین بین جلسات، پیگیری و سنجش پیشرفت',
						),
					),
					array(
						'id'      => 'home_about_badge_num',
						'type'    => 'text',
						'title'   => __( 'عدد نشان تجربه', 'zarincoach' ),
						'default' => '۶',
					),
					array(
						'id'      => 'home_about_badge_label',
						'type'    => 'text',
						'title'   => __( 'برچسب نشان تجربه', 'zarincoach' ),
						'default' => 'محیط تجربه میدانی',
					),
					array(
						'id'      => 'home_about_button_text',
						'type'    => 'text',
						'title'   => __( 'متن دکمه', 'zarincoach' ),
						'default' => 'داستان و مسیر حرفه‌ای من',
					),
					array(
						'id'      => 'home_about_button_url',
						'type'    => 'text',
						'title'   => __( 'لینک دکمه', 'zarincoach' ),
						'default' => '/about-me/',
					),
				),
			),

			/* ---------------- خدمات ---------------- */
			array(
				'title'      => __( 'خدمات و برنامه‌ها', 'zarincoach' ),
				'id'         => 'home-services',
				'subsection' => true,
				'icon'       => 'el el-th-list',
				'fields'     => array(
					array(
						'id'      => 'home_services_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_services_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'خدمات',
					),
					array(
						'id'      => 'home_services_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'مسیرهای همراهی؛ از شناخت تا اقدام',
					),
					array(
						'id'      => 'home_services_subtitle',
						'type'    => 'textarea',
						'title'   => __( 'زیرعنوان', 'zarincoach' ),
						'default' => 'هر مسیر با یک ارزیابی اولیه‌ی آسوده و بدون عجله شروع می‌شود. خدمات من آموزشی و رشدمحور است و جایگزین درمان اختلال‌های روان‌پزشکی نیست.',
					),
					array(
						'id'      => 'home_services_count',
						'type'    => 'spinner',
						'title'   => __( 'تعداد نمایش', 'zarincoach' ),
						'default' => 6,
						'min'     => 2,
						'step'    => 1,
						'max'     => 12,
					),
					array(
						'id'      => 'home_services_columns',
						'type'    => 'button_set',
						'title'   => __( 'تعداد ستون', 'zarincoach' ),
						'options' => array(
							'2' => __( '۲ ستون', 'zarincoach' ),
							'3' => __( '۳ ستون', 'zarincoach' ),
							'4' => __( '۴ ستون', 'zarincoach' ),
						),
						'default' => '3',
					),
					array(
						'id'      => 'home_services_price',
						'type'    => 'switch',
						'title'   => __( 'نمایش قیمت/مدت', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_services_button',
						'type'    => 'text',
						'title'   => __( 'متن دکمه کارت‌ها', 'zarincoach' ),
						'default' => 'جزئیات بیشتر',
					),
				),
			),

			/* ---------------- طرحواره‌ها ---------------- */
			array(
				'title'      => __( 'طرحواره‌ها', 'zarincoach' ),
				'id'         => 'home-schema',
				'subsection' => true,
				'icon'       => 'el el-idea',
				'fields'     => array(
					array(
						'id'      => 'home_schema_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_schema_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'رمزگشایی الگوها',
					),
					array(
						'id'      => 'home_schema_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'کدام الگو دارد مسیر امروز شما را کند می‌کند؟',
					),
					array(
						'id'      => 'home_schema_subtitle',
						'type'    => 'textarea',
						'title'   => __( 'زیرعنوان', 'zarincoach' ),
						'default' => 'الگوها بهانه نیستند؛ نقشه‌اند. وقتی بدانید کدام الگو فعال است، به‌جای سرزنش خودتان می‌توانید قدم بعدی را آگاهانه انتخاب کنید.',
					),
					array(
						'id'      => 'home_schema_items',
						'type'    => 'slides',
						'title'   => __( 'طرحواره‌ها', 'zarincoach' ),
						'show'    => array(
							'title'       => true,
							'description' => true,
							'url'         => false,
							'image'       => false,
						),
						'placeholder' => array(
							'title'       => __( 'نام طرحواره', 'zarincoach' ),
							'description' => __( 'توضیح کوتاه درباره این طرحواره', 'zarincoach' ),
						),
						'default' => array(
							array(
								'title'       => 'کمال‌گرایی پنهان',
								'description' => 'استاندارد آن‌قدر بالاست که شروع کردن ترسناک می‌شود؛ «یا عالی، یا هیچ».',
								'sort'        => '1',
							),
							array(
								'title'       => 'اهمالکاری',
								'description' => 'مشکل تنبلی نیست؛ فرار از احساس ناخوشایندِ ناقص انجام دادن است.',
								'sort'        => '2',
							),
							array(
								'title'       => 'بلاتکلیفی در تصمیم',
								'description' => 'مقایسه‌ی بی‌پایان گزینه‌ها، ترس از انتخاب اشتباه و ماندن در جا.',
								'sort'        => '3',
							),
							array(
								'title'       => 'ذهن شلوغ',
								'description' => 'پرونده‌های باز ذهن، فکرِ زیاد و اقدامِ کم؛ خستگی بدون پیشرفت.',
								'sort'        => '4',
							),
							array(
								'title'       => 'نه نگفتن و مرزهای مبهم',
								'description' => 'پذیرفتن همه‌ی درخواست‌ها برای تأیید گرفتن و خالی شدن از انرژی.',
								'sort'        => '5',
							),
							array(
								'title'       => 'کنترل‌گری مدیرانه',
								'description' => 'انجام همه‌ی کارها به دست خود، تفویض نکردن و بردن استرس کار به خانه.',
								'sort'        => '6',
							),
						),
					),
				),
			),

			/* ---------------- مسیر همراهی ---------------- */
			array(
				'title'      => __( 'مسیر همراهی', 'zarincoach' ),
				'id'         => 'home-process',
				'subsection' => true,
				'icon'       => 'el el-road',
				'fields'     => array(
					array(
						'id'      => 'home_process_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_process_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'روش کار من',
					),
					array(
						'id'      => 'home_process_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'پنج گام از شناخت الگو تا خودکفایی',
					),
					array(
						'id'      => 'home_process_steps',
						'type'    => 'slides',
						'title'   => __( 'مراحل', 'zarincoach' ),
						'show'    => array(
							'title'       => true,
							'description' => true,
							'url'         => true,
							'image'       => false,
						),
						'placeholder' => array(
							'title'       => __( 'عنوان مرحله', 'zarincoach' ),
							'description' => __( 'توضیح این مرحله', 'zarincoach' ),
							'url'         => __( 'لینک (اختیاری)', 'zarincoach' ),
						),
						'default' => array(
							array(
								'title'       => 'کشف الگو',
								'description' => 'با ارزیابی اولیه و تست‌های معتبر، الگوی غالب را پیدا می‌کنیم: کمال‌گرایی، اهمالکاری، بلاتکلیفی یا کنترل‌گری.',
								'sort'        => '1',
								'url'         => '#booking',
							),
							array(
								'title'       => 'رمزگشایی',
								'description' => 'زنجیره‌ی فکر، احساس و رفتار را روی کاغذ می‌آوریم تا با هم ببینیم الگو دقیقاً کجا و چطور فعال می‌شود.',
								'sort'        => '2',
								'url'         => '',
							),
							array(
								'title'       => 'انتخاب',
								'description' => 'به‌جای واکنش خودکار، پاسخ جایگزین را انتخاب می‌کنید؛ با معیار روشن و هدفی که واقعاً مال خودتان است.',
								'sort'        => '3',
								'url'         => '',
							),
							array(
								'title'       => 'اقدام',
								'description' => 'تمرین‌های کوچک و قابل سنجش بین جلسات؛ «نسخه‌ی اول» به‌جای «نسخه‌ی بی‌نقص».',
								'url'         => '',
							),
							array(
								'title'       => 'تثبیت',
								'description' => 'مرور پیشرفت، پیشگیری از بازگشت الگو و رسیدن به خودکفایی؛ تا کم‌کم برای هر قدم به من نیاز نداشته باشید. هدف اصلی من همین است.',
								'sort'        => '4',
								'url'         => '',
							),
						),
					),
				),
			),

			/* ---------------- آمار ---------------- */
			array(
				'title'      => __( 'آمار و ارقام', 'zarincoach' ),
				'id'         => 'home-stats',
				'subsection' => true,
				'icon'       => 'el el-graph',
				'fields'     => array(
					array(
						'id'      => 'home_stats_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_stats_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'پشتوانه‌ی کار من؛ تجربه‌ی میدانی و ساختار روشن، نه ادعای آماری',
					),
					array(
						'id'      => 'home_stats_items',
						'type'    => 'slides',
						'title'   => __( 'آمارها', 'zarincoach' ),
						'desc'    => __( 'در فیلد عنوان، عدد و در توضیح، برچسب را وارد کنید.', 'zarincoach' ),
						'show'    => array(
							'title'       => true,
							'description' => true,
							'url'         => false,
							'image'       => false,
						),
						'placeholder' => array(
							'title'       => 'مثال: ۹۶۰',
							'description' => 'مثال: جلسه همراهی',
						),
						'default' => array(
							array( 'title' => '۶', 'description' => 'محیط تجربه میدانی؛ از مدرسه تا خط ۱۴۸۰', 'sort' => '1' ),
							array( 'title' => '۱۰', 'description' => 'مهارت زندگی در برنامه آموزشی (WHO)', 'sort' => '2' ),
							array( 'title' => '۱۸', 'description' => 'طرحواره‌ی شناخته‌شده در مدل یانگ', 'sort' => '3' ),
							array( 'title' => '۵', 'description' => 'گام روش «از الگو تا اقدام»', 'sort' => '4' ),
						),
					),
				),
			),

			/* ---------------- تجربه مراجعان ---------------- */
			array(
				'title'      => __( 'تجربه مراجعان', 'zarincoach' ),
				'id'         => 'home-testimonials',
				'subsection' => true,
				'icon'       => 'el el-quotes',
				'fields'     => array(
					array(
						'id'      => 'home_testimonials_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_testimonials_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'تجربه مراجعان',
					),
					array(
						'id'      => 'home_testimonials_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'آنچه مراجعان در دکترتو نوشته‌اند',
					),
					array(
						'id'      => 'home_testimonials_count',
						'type'    => 'spinner',
						'title'   => __( 'تعداد نمایش', 'zarincoach' ),
						'default' => 6,
						'min'     => 2,
						'step'    => 1,
						'max'     => 12,
					),
					array(
						'id'      => 'home_testimonials_style',
						'type'    => 'button_set',
						'title'   => __( 'سبک نمایش', 'zarincoach' ),
						'options' => array(
							'grid'   => __( 'شبکه‌ای', 'zarincoach' ),
							'slider' => __( 'اسلایدر', 'zarincoach' ),
						),
						'default' => 'slider',
					),
				),
			),

			/* ---------------- پرسش‌های پرتکرار ---------------- */
			array(
				'title'      => __( 'پرسش‌های پرتکرار', 'zarincoach' ),
				'id'         => 'home-faq',
				'subsection' => true,
				'icon'       => 'el el-question-sign',
				'fields'     => array(
					array(
						'id'      => 'home_faq_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_faq_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'پیش از شروع',
					),
					array(
						'id'      => 'home_faq_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'پرسش‌هایی که بیشترِ مراجعان پیش از جلسه اول از من می‌پرسند',
					),
					array(
						'id'      => 'home_faq_count',
						'type'    => 'spinner',
						'title'   => __( 'تعداد نمایش', 'zarincoach' ),
						'default' => 6,
						'min'     => 2,
						'step'    => 1,
						'max'     => 20,
					),
					array(
						'id'      => 'home_faq_schema',
						'type'    => 'switch',
						'title'   => __( 'افزودن نشانه‌گذاری FAQ', 'zarincoach' ),
						'desc'    => __( 'اسکیما به نمایش پرسش و پاسخ‌ها در نتایج گوگل کمک می‌کند.', 'zarincoach' ),
						'default' => true,
					),
				),
			),

			/* ---------------- آخرین نوشته‌ها ---------------- */
			array(
				'title'      => __( 'آخرین نوشته‌ها', 'zarincoach' ),
				'id'         => 'home-blog',
				'subsection' => true,
				'icon'       => 'el el-edit',
				'fields'     => array(
					array(
						'id'      => 'home_blog_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_blog_eyebrow',
						'type'    => 'text',
						'title'   => __( 'برچسب', 'zarincoach' ),
						'default' => 'مجله الگوها',
					),
					array(
						'id'      => 'home_blog_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'در هر یادداشت: مفهوم ساده، مثال واقعی، تمرین و قدم بعدی',
					),
					array(
						'id'      => 'home_blog_count',
						'type'    => 'spinner',
						'title'   => __( 'تعداد نمایش', 'zarincoach' ),
						'default' => 3,
						'min'     => 2,
						'step'    => 1,
						'max'     => 9,
					),
					array(
						'id'      => 'home_blog_button',
						'type'    => 'text',
						'title'   => __( 'متن دکمه مشاهده همه', 'zarincoach' ),
						'default' => 'همه یادداشت‌ها',
					),
				),
			),

			/* ---------------- فراخوان اقدام ---------------- */
			array(
				'title'      => __( 'فراخوان اقدام', 'zarincoach' ),
				'id'         => 'home-cta',
				'subsection' => true,
				'icon'       => 'el el-bullhorn',
				'fields'     => array(
					array(
						'id'      => 'home_cta_enable',
						'type'    => 'switch',
						'title'   => __( 'نمایش این بخش', 'zarincoach' ),
						'default' => true,
					),
					array(
						'id'      => 'home_cta_title',
						'type'    => 'text',
						'title'   => __( 'عنوان', 'zarincoach' ),
						'default' => 'قدم اول را با هم کوچک و دقیق برمی‌داریم',
					),
					array(
						'id'      => 'home_cta_text',
						'type'    => 'textarea',
						'title'   => __( 'متن', 'zarincoach' ),
						'default' => 'در جلسه‌ی ارزیابی اولیه، موضوع اصلی و الگوی غالب را مشخص می‌کنیم و می‌بینیم کدام مسیر برای شما مناسب‌تر است. اگر جایی نیاز به درمان تخصصی باشد، صادقانه راهنمایی‌تان می‌کنم.',
					),
					array(
						'id'      => 'home_cta_primary_text',
						'type'    => 'text',
						'title'   => __( 'متن دکمه اول', 'zarincoach' ),
						'default' => 'رزرو جلسه ارزیابی',
					),
					array(
						'id'      => 'home_cta_primary_url',
						'type'    => 'text',
						'title'   => __( 'لینک دکمه اول', 'zarincoach' ),
						'default' => '/booking/',
					),
					array(
						'id'      => 'home_cta_secondary_text',
						'type'    => 'text',
						'title'   => __( 'متن دکمه دوم', 'zarincoach' ),
						'default' => 'پیام در تلگرام',
					),
					array(
						'id'      => 'home_cta_note',
						'type'    => 'text',
						'title'   => __( 'نکته زیر دکمه‌ها', 'zarincoach' ),
						'default' => 'خدمات فقط از طریق Maryam-Jamali.ir · تعرفه مطابق مصوبه سازمان نظام روان‌شناسی و مشاوره',
					),
				),
			),
		);
	}
endif;
