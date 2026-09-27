<?php
/**
 * کتابخانه‌ی طرحواره‌ها و الگوهای ذهنی
 *
 * - نوع نوشته‌ی zc_schema (نشانی /schemas/نامک/)
 * - طبقه‌بندی سلسله‌مراتبی zc_sc_group (حوزه‌ها و دسته‌ها)
 * - متاباکس فیلدهای ساختاریافته
 * - توابع خروجی کارت و شبکه‌ی فیلترپذیر (مشترک ویجت، آرشیو گروه و «مرتبط‌ها»)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* ------------------------------------------------------------------ *
 * تعریف گروه‌ها
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_sc_groups' ) ) :
	/**
	 * تعریف همه‌ی گروه‌ها (منبع واحد: نصب دمو، رنگ، آیکون و متن‌های راهنما).
	 *
	 * type: ems | mode | coping | distortion
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function zc_sc_groups() {
		static $groups = null;
		if ( null !== $groups ) {
			return $groups;
		}

		$groups = array(
			/* ---------- طرحواره‌های ناسازگار اولیه ---------- */
			'early-maladaptive-schemas' => array(
				'name'   => __( 'طرحواره‌های ناسازگار اولیه', 'zarincoach' ),
				'short'  => __( 'طرحواره‌ها', 'zarincoach' ),
				'parent' => '',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#1D3A72',
				'icon'   => 'brain',
				'order'  => 1,
				'desc'   => __( 'هجده الگوی عمیق و فراگیر از خاطره‌ها، هیجان‌ها، باورها و احساس‌های بدنی درباره‌ی خود و رابطه با دیگران که در کودکی و نوجوانی، وقتی نیازهای هیجانی بنیادین برآورده نشده‌اند، شکل گرفته‌اند و در بزرگسالی بارها تکرار می‌شوند. جفری یانگ این هجده طرحواره را در پنج حوزه دسته‌بندی کرده است؛ هر حوزه به یکی از نیازهای اصلی کودکی مربوط است.', 'zarincoach' ),
				'need'   => '',
			),
			'disconnection-rejection'   => array(
				'name'   => __( 'حوزه‌ی ۱: بریدگی و طرد', 'zarincoach' ),
				'short'  => __( 'بریدگی و طرد', 'zarincoach' ),
				'parent' => 'early-maladaptive-schemas',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#B4535B',
				'icon'   => 'heart',
				'order'  => 1,
				'desc'   => __( 'انتظار اینکه نیاز به امنیت، ثبات، محبت، همدلی و پذیرش به شکلی قابل‌پیش‌بینی برآورده نشود. این طرحواره‌ها معمولاً در خانواده‌های سرد، طردکننده، بی‌ثبات یا آسیب‌زا ریشه دارند و بیشترین اثر را بر روابط نزدیک می‌گذارند.', 'zarincoach' ),
				'need'   => __( 'دلبستگی ایمن، ثبات، مراقبت و پذیرش', 'zarincoach' ),
			),
			'impaired-autonomy'         => array(
				'name'   => __( 'حوزه‌ی ۲: خودگردانی و عملکرد مختل', 'zarincoach' ),
				'short'  => __( 'خودگردانی مختل', 'zarincoach' ),
				'parent' => 'early-maladaptive-schemas',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#C07A2E',
				'icon'   => 'compass',
				'order'  => 2,
				'desc'   => __( 'انتظاراتی درباره‌ی خود و دنیا که توانایی جدا شدن، مستقل عمل کردن و موفق بودن را تضعیف می‌کنند. خانواده‌های بیش‌ازحد حمایت‌گر، درهم‌تنیده یا تضعیف‌کننده‌ی اعتمادبه‌نفس، زمینه‌ی رایج این حوزه‌اند.', 'zarincoach' ),
				'need'   => __( 'خودمختاری، شایستگی و هویت مستقل', 'zarincoach' ),
			),
			'impaired-limits'           => array(
				'name'   => __( 'حوزه‌ی ۳: محدودیت‌های مختل', 'zarincoach' ),
				'short'  => __( 'محدودیت‌های مختل', 'zarincoach' ),
				'parent' => 'early-maladaptive-schemas',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#7A5AA6',
				'icon'   => 'target',
				'order'  => 3,
				'desc'   => __( 'کمبود مرزهای درونی، مسئولیت‌پذیری در برابر دیگران یا جهت‌گیری بلندمدت. این طرحواره‌ها اغلب در خانواده‌هایی شکل می‌گیرند که بیش‌ازحد سهل‌گیر بوده‌اند یا حس برتری را پرورش داده‌اند.', 'zarincoach' ),
				'need'   => __( 'محدودیت‌های واقع‌بینانه و خویشتن‌داری', 'zarincoach' ),
			),
			'other-directedness'        => array(
				'name'   => __( 'حوزه‌ی ۴: دیگرجهت‌مندی', 'zarincoach' ),
				'short'  => __( 'دیگرجهت‌مندی', 'zarincoach' ),
				'parent' => 'early-maladaptive-schemas',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#2F8F83',
				'icon'   => 'users',
				'order'  => 4,
				'desc'   => __( 'تمرکز افراطی بر خواسته‌ها، احساسات و واکنش‌های دیگران به قیمت نادیده گرفتن نیازهای خود، برای به دست آوردن محبت، تأیید یا پرهیز از تلافی. پذیرش مشروط در کودکی، ریشه‌ی رایج این حوزه است.', 'zarincoach' ),
				'need'   => __( 'آزادی در بیان نیازها و احساسات', 'zarincoach' ),
			),
			'overvigilance-inhibition'  => array(
				'name'   => __( 'حوزه‌ی ۵: گوش‌به‌زنگی بیش‌ازحد و بازداری', 'zarincoach' ),
				'short'  => __( 'گوش‌به‌زنگی و بازداری', 'zarincoach' ),
				'parent' => 'early-maladaptive-schemas',
				'type'   => 'ems',
				'key'    => 'ems',
				'color'  => '#3D6FB6',
				'icon'   => 'eye',
				'order'  => 5,
				'desc'   => __( 'تأکید افراطی بر سرکوب احساسات و خودانگیختگی، یا پیروی از قواعد سفت و انتظارات درونی‌شده درباره‌ی عملکرد، به قیمت شادی، آرامش، سلامت و روابط نزدیک. خانواده‌های سخت‌گیر، تنبیه‌گر و کمال‌گرا زمینه‌ی رایج این حوزه‌اند.', 'zarincoach' ),
				'need'   => __( 'خودانگیختگی، بازی و تفریح', 'zarincoach' ),
			),

			/* ---------- ذهنیت‌ها ---------- */
			'schema-modes'              => array(
				'name'   => __( 'ذهنیت‌های طرحواره‌ای', 'zarincoach' ),
				'short'  => __( 'ذهنیت‌ها', 'zarincoach' ),
				'parent' => '',
				'type'   => 'mode',
				'key'    => 'modes',
				'color'  => '#0B1B3A',
				'icon'   => 'mirror',
				'order'  => 2,
				'desc'   => __( 'اگر طرحواره‌ها صفت‌های پایدار باشند، ذهنیت‌ها «حالت‌های لحظه‌ای» هستند: مجموعه‌ای از احساس، فکر و رفتار که در یک موقعیت خاص فعال می‌شود. مدل ذهنیت‌ها چهارده حالت را در چهار دسته توصیف می‌کند: ذهنیت‌های کودک، مقابله‌ای ناکارآمد، والد ناکارآمد و ذهنیت سالم.', 'zarincoach' ),
				'need'   => '',
			),
			'child-modes'               => array(
				'name'   => __( 'ذهنیت‌های کودک', 'zarincoach' ),
				'short'  => __( 'کودک', 'zarincoach' ),
				'parent' => 'schema-modes',
				'type'   => 'mode',
				'key'    => 'modes',
				'color'  => '#C8694A',
				'icon'   => 'seedling',
				'order'  => 1,
				'desc'   => __( 'حالت‌های هیجانی شدید و کودکانه که وقتی نیازهای اصلی برآورده نمی‌شوند فعال می‌شوند. کودک شاد، تنها ذهنیت سالم این دسته است.', 'zarincoach' ),
				'need'   => __( 'دیده شدن، آرام شدن و مرزگذاری مهربان', 'zarincoach' ),
			),
			'coping-modes'              => array(
				'name'   => __( 'ذهنیت‌های مقابله‌ای ناکارآمد', 'zarincoach' ),
				'short'  => __( 'مقابله‌ای', 'zarincoach' ),
				'parent' => 'schema-modes',
				'type'   => 'mode',
				'key'    => 'modes',
				'color'  => '#5F7D95',
				'icon'   => 'shield',
				'order'  => 2,
				'desc'   => __( 'حالت‌هایی که در کودکی برای محافظت از درد ساخته شده‌اند و حالا خودکار عمل می‌کنند؛ در سه شکل تسلیم، اجتناب و جبران افراطی.', 'zarincoach' ),
				'need'   => __( 'امنیت، بدون قطع ارتباط با خود و دیگران', 'zarincoach' ),
			),
			'parent-modes'              => array(
				'name'   => __( 'ذهنیت‌های والد ناکارآمد', 'zarincoach' ),
				'short'  => __( 'والد', 'zarincoach' ),
				'parent' => 'schema-modes',
				'type'   => 'mode',
				'key'    => 'modes',
				'color'  => '#8C4A5E',
				'icon'   => 'alert',
				'order'  => 3,
				'desc'   => __( 'صداهای درونی‌شده‌ی انتقاد، تنبیه و توقع که از پیام‌های بزرگ‌ترهای مهم کودکی گرفته شده‌اند و حالا از درون با ما حرف می‌زنند.', 'zarincoach' ),
				'need'   => __( 'مهربانی با خود و معیارهای انسانی', 'zarincoach' ),
			),
			'healthy-modes'             => array(
				'name'   => __( 'ذهنیت سالم', 'zarincoach' ),
				'short'  => __( 'سالم', 'zarincoach' ),
				'parent' => 'schema-modes',
				'type'   => 'mode',
				'key'    => 'modes',
				'color'  => '#3E8E5E',
				'icon'   => 'sun',
				'order'  => 4,
				'desc'   => __( 'بخشی که از کودک درون مراقبت می‌کند، با صدای والد انتقادگر مقابله می‌کند و تصمیم‌های متعادل می‌گیرد. هدف اصلی کار طرحواره‌ای، تقویت همین ذهنیت است.', 'zarincoach' ),
				'need'   => __( 'تقویت و تمرین روزانه', 'zarincoach' ),
			),

			/* ---------- سبک‌های مقابله ---------- */
			'coping-styles'             => array(
				'name'   => __( 'سبک‌های مقابله‌ای', 'zarincoach' ),
				'short'  => __( 'سبک‌های مقابله‌ای', 'zarincoach' ),
				'parent' => '',
				'type'   => 'coping',
				'key'    => 'coping',
				'color'  => '#2A5A9E',
				'icon'   => 'refresh',
				'order'  => 3,
				'desc'   => __( 'هر طرحواره را می‌توان به سه شکل پاسخ داد: تسلیم شدن به آن، فرار از آن یا جنگیدن افراطی با آن. این سه سبک در کودکی برای بقا مفید بوده‌اند، اما در بزرگسالی اغلب طرحواره را زنده نگه می‌دارند.', 'zarincoach' ),
				'need'   => '',
			),

			/* ---------- خطاهای شناختی ---------- */
			'cognitive-distortions'     => array(
				'name'   => __( 'خطاهای شناختی', 'zarincoach' ),
				'short'  => __( 'خطاهای شناختی', 'zarincoach' ),
				'parent' => '',
				'type'   => 'distortion',
				'key'    => 'distortions',
				'color'  => '#A9832F',
				'icon'   => 'sparkles',
				'order'  => 4,
				'desc'   => __( 'میان‌برهای خودکار ذهن که واقعیت را به شکلی نادقیق و معمولاً منفی تفسیر می‌کنند. طرحواره‌ها «باورهای عمیق» هستند و خطاهای شناختی، «عینک‌هایی» که این باورها را در افکار لحظه‌ای روزمره زنده نگه می‌دارند. این دوازده خطا بر اساس کار آرون بک و دیوید برنز معرفی شده‌اند.', 'zarincoach' ),
				'need'   => '',
			),
		);

		/**
		 * فیلتر تعریف گروه‌های طرحواره.
		 *
		 * @param array $groups گروه‌ها.
		 */
		$groups = (array) apply_filters( 'zc_sc_groups', $groups );
		return $groups;
	}
endif;

if ( ! function_exists( 'zc_sc_group' ) ) :
	/**
	 * تعریف یک گروه با نامک.
	 *
	 * @param string $slug نامک.
	 * @return array<string, mixed>
	 */
	function zc_sc_group( $slug ) {
		$groups = zc_sc_groups();
		return isset( $groups[ $slug ] ) ? $groups[ $slug ] : array();
	}
endif;

if ( ! function_exists( 'zc_sc_top_groups' ) ) :
	/**
	 * گروه‌های سطح بالا به ترتیب نمایش.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	function zc_sc_top_groups() {
		$top = array_filter(
			zc_sc_groups(),
			static function ( $g ) {
				return empty( $g['parent'] );
			}
		);
		uasort(
			$top,
			static function ( $a, $b ) {
				return (int) $a['order'] - (int) $b['order'];
			}
		);
		return $top;
	}
endif;

if ( ! function_exists( 'zc_sc_child_groups' ) ) :
	/**
	 * زیرگروه‌های یک گروه سطح بالا به ترتیب نمایش.
	 *
	 * @param string $parent نامک والد.
	 * @return array<string, array<string, mixed>>
	 */
	function zc_sc_child_groups( $parent ) {
		$children = array_filter(
			zc_sc_groups(),
			static function ( $g ) use ( $parent ) {
				return isset( $g['parent'] ) && $parent === $g['parent'];
			}
		);
		uasort(
			$children,
			static function ( $a, $b ) {
				return (int) $a['order'] - (int) $b['order'];
			}
		);
		return $children;
	}
endif;

if ( ! function_exists( 'zc_sc_type_labels' ) ) :
	/**
	 * برچسب بخش‌ها بر اساس نوع مدخل.
	 *
	 * @param string $type ems|mode|coping|distortion.
	 * @return array<string, string>
	 */
	function zc_sc_type_labels( $type ) {
		$base = array(
			'belief'   => __( 'باور مرکزی', 'zarincoach' ),
			'intro'    => __( 'این طرحواره چیست؟', 'zarincoach' ),
			'need'     => __( 'نیاز برآورده‌نشده', 'zarincoach' ),
			'signs'    => __( 'نشانه‌ها در زندگی روزمره', 'zarincoach' ),
			'origins'  => __( 'ریشه‌های کودکی', 'zarincoach' ),
			'triggers' => __( 'موقعیت‌های فعال‌ساز', 'zarincoach' ),
			'cope'     => __( 'سه پاسخ مقابله‌ای به این طرحواره', 'zarincoach' ),
			'example'  => __( 'یک نمونه‌ی واقعی', 'zarincoach' ),
			'healthy'  => __( 'پیام سالم', 'zarincoach' ),
			'exercise' => __( 'تمرین خودشناسی', 'zarincoach' ),
			'related'  => __( 'طرحواره‌ها و الگوهای مرتبط', 'zarincoach' ),
			'kind'     => __( 'طرحواره', 'zarincoach' ),
		);

		switch ( $type ) {
			case 'mode':
				return array_merge(
					$base,
					array(
						'belief'  => __( 'صدای درونی', 'zarincoach' ),
						'intro'   => __( 'این ذهنیت چیست؟', 'zarincoach' ),
						'need'    => __( 'کارکرد این ذهنیت', 'zarincoach' ),
						'signs'   => __( 'نشانه‌های فعال شدن', 'zarincoach' ),
						'origins' => __( 'ریشه‌ها', 'zarincoach' ),
						'healthy' => __( 'پاسخ بزرگسال سالم', 'zarincoach' ),
						'kind'    => __( 'ذهنیت', 'zarincoach' ),
					)
				);
			case 'coping':
				return array_merge(
					$base,
					array(
						'belief'  => __( 'منطق درونی', 'zarincoach' ),
						'intro'   => __( 'این سبک مقابله چیست؟', 'zarincoach' ),
						'need'    => __( 'کارکرد این سبک', 'zarincoach' ),
						'signs'   => __( 'نشانه‌ها', 'zarincoach' ),
						'origins' => __( 'چرا شکل می‌گیرد؟', 'zarincoach' ),
						'healthy' => __( 'جایگزین سالم', 'zarincoach' ),
						'kind'    => __( 'سبک مقابله', 'zarincoach' ),
					)
				);
			case 'distortion':
				return array_merge(
					$base,
					array(
						'belief'   => __( 'جمله‌ی آشنا', 'zarincoach' ),
						'intro'    => __( 'این خطای شناختی چیست؟', 'zarincoach' ),
						'need'     => __( 'چرا ذهن این کار را می‌کند؟', 'zarincoach' ),
						'signs'    => __( 'نشانه‌ها', 'zarincoach' ),
						'origins'  => __( 'ریشه‌ها و پیوند با طرحواره‌ها', 'zarincoach' ),
						'triggers' => __( 'کی بیشتر سراغمان می‌آید؟', 'zarincoach' ),
						'example'  => __( 'نمونه‌ی فکر', 'zarincoach' ),
						'healthy'  => __( 'بازنویسی متعادل', 'zarincoach' ),
						'exercise' => __( 'تمرین به چالش کشیدن فکر', 'zarincoach' ),
						'kind'     => __( 'خطای شناختی', 'zarincoach' ),
					)
				);
		}
		return $base;
	}
endif;

/* ------------------------------------------------------------------ *
 * ثبت نوع نوشته و طبقه‌بندی
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_register_schema_type' ) ) :
	/**
	 * ثبت zc_schema و zc_schema_group.
	 *
	 * @return void
	 */
	function zc_register_schema_type() {
		register_post_type(
			'zc_schema',
			array(
				'labels'              => array(
					'name'               => __( 'طرحواره‌ها و الگوها', 'zarincoach' ),
					'singular_name'      => __( 'طرحواره / الگو', 'zarincoach' ),
					'menu_name'          => __( 'طرحواره‌ها', 'zarincoach' ),
					'add_new'            => __( 'افزودن مدخل', 'zarincoach' ),
					'add_new_item'       => __( 'افزودن طرحواره یا الگوی جدید', 'zarincoach' ),
					'edit_item'          => __( 'ویرایش مدخل', 'zarincoach' ),
					'new_item'           => __( 'مدخل جدید', 'zarincoach' ),
					'view_item'          => __( 'مشاهده مدخل', 'zarincoach' ),
					'search_items'       => __( 'جستجوی طرحواره‌ها', 'zarincoach' ),
					'not_found'          => __( 'مدخلی یافت نشد', 'zarincoach' ),
					'not_found_in_trash' => __( 'مدخلی در زباله‌دان یافت نشد', 'zarincoach' ),
					'all_items'          => __( 'همه مدخل‌ها', 'zarincoach' ),
				),
				'public'              => true,
				'publicly_queryable'  => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_rest'        => true,
				'query_var'           => true,
				'rewrite'             => array( 'slug' => 'schemas', 'with_front' => false ),
				'capability_type'     => 'post',
				'has_archive'         => false,
				'hierarchical'        => false,
				'menu_position'       => 24,
				'menu_icon'           => 'dashicons-networking',
				'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
				'taxonomies'          => array( 'zc_schema_group' ),
				'exclude_from_search' => false,
			)
		);

		register_taxonomy(
			'zc_schema_group',
			array( 'zc_schema' ),
			array(
				'labels'            => array(
					'name'          => __( 'گروه‌های طرحواره', 'zarincoach' ),
					'singular_name' => __( 'گروه طرحواره', 'zarincoach' ),
					'search_items'  => __( 'جستجوی گروه', 'zarincoach' ),
					'all_items'     => __( 'همه گروه‌ها', 'zarincoach' ),
					'parent_item'   => __( 'گروه والد', 'zarincoach' ),
					'edit_item'     => __( 'ویرایش گروه', 'zarincoach' ),
					'add_new_item'  => __( 'افزودن گروه', 'zarincoach' ),
					'new_item_name' => __( 'نام گروه جدید', 'zarincoach' ),
					'menu_name'     => __( 'گروه‌ها و حوزه‌ها', 'zarincoach' ),
				),
				'public'            => true,
				'hierarchical'      => true,
				'show_ui'           => true,
				'show_admin_column' => true,
				'show_in_rest'      => true,
				'query_var'         => true,
				'rewrite'           => array( 'slug' => 'schema-group', 'with_front' => false, 'hierarchical' => false ),
			)
		);
	}
endif;
add_action( 'init', 'zc_register_schema_type', 9 );

/* ------------------------------------------------------------------ *
 * فیلدها و داده
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_sc_fields' ) ) :
	/**
	 * فیلدهای متای مدخل: کلید => [متا، نوع، برچسب].
	 *
	 * @return array<string, array{0:string,1:string,2:string}>
	 */
	function zc_sc_fields() {
		return array(
			'en'             => array( '_zc_sc_en', 'text', __( 'نام انگلیسی', 'zarincoach' ) ),
			'code'           => array( '_zc_sc_code', 'text', __( 'کد کوتاه (مثل AB)', 'zarincoach' ) ),
			'order'          => array( '_zc_sc_order', 'int', __( 'ترتیب در گروه', 'zarincoach' ) ),
			'belief'         => array( '_zc_sc_belief', 'textarea', __( 'باور مرکزی / صدای درونی', 'zarincoach' ) ),
			'need'           => array( '_zc_sc_need', 'textarea', __( 'نیاز برآورده‌نشده / کارکرد', 'zarincoach' ) ),
			'signs'          => array( '_zc_sc_signs', 'lines', __( 'نشانه‌ها (هر سطر یک مورد)', 'zarincoach' ) ),
			'origins'        => array( '_zc_sc_origins', 'lines', __( 'ریشه‌ها (هر سطر یک مورد)', 'zarincoach' ) ),
			'triggers'       => array( '_zc_sc_triggers', 'lines', __( 'موقعیت‌های فعال‌ساز (هر سطر یک مورد)', 'zarincoach' ) ),
			'cope_surrender' => array( '_zc_sc_cope_surrender', 'textarea', __( 'مقابله: تسلیم', 'zarincoach' ) ),
			'cope_avoid'     => array( '_zc_sc_cope_avoid', 'textarea', __( 'مقابله: اجتناب', 'zarincoach' ) ),
			'cope_over'      => array( '_zc_sc_cope_over', 'textarea', __( 'مقابله: جبران افراطی', 'zarincoach' ) ),
			'example'        => array( '_zc_sc_example', 'textarea', __( 'نمونه‌ی واقعی', 'zarincoach' ) ),
			'healthy'        => array( '_zc_sc_healthy', 'textarea', __( 'پیام سالم / بازنویسی', 'zarincoach' ) ),
			'exercise'       => array( '_zc_sc_exercise', 'lines', __( 'تمرین (هر سطر یک گام)', 'zarincoach' ) ),
			'related'        => array( '_zc_sc_related', 'slugs', __( 'مدخل‌های مرتبط (نامک‌ها، با ویرگول)', 'zarincoach' ) ),
		);
	}
endif;

if ( ! function_exists( 'zc_sc_lines' ) ) :
	/**
	 * تبدیل متن چندسطری به آرایه.
	 *
	 * @param string $value متن.
	 * @return string[]
	 */
	function zc_sc_lines( $value ) {
		$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
		return array_values( array_filter( array_map( 'trim', (array) $lines ), 'strlen' ) );
	}
endif;

if ( ! function_exists( 'zc_sc_terms' ) ) :
	/**
	 * گروه مستقیم (فرزند) و گروه سطح بالای یک مدخل.
	 *
	 * @param int $post_id شناسه.
	 * @return array{group:?WP_Term,top:?WP_Term}
	 */
	function zc_sc_terms( $post_id ) {
		$terms = get_the_terms( $post_id, 'zc_schema_group' );
		$group = null;
		$top   = null;
		if ( is_array( $terms ) && $terms ) {
			// عمیق‌ترین گروه را به‌عنوان گروه اصلی در نظر می‌گیریم.
			usort(
				$terms,
				static function ( $a, $b ) {
					return (int) $b->parent - (int) $a->parent;
				}
			);
			foreach ( $terms as $t ) {
				if ( $t->parent ) {
					$group = $t;
					break;
				}
			}
			if ( ! $group ) {
				$group = $terms[0];
			}
			$top = $group;
			while ( $top && $top->parent ) {
				$parent = get_term( $top->parent, 'zc_schema_group' );
				if ( ! $parent || is_wp_error( $parent ) ) {
					break;
				}
				$top = $parent;
			}
		}
		return array(
			'group' => $group,
			'top'   => $top,
		);
	}
endif;

if ( ! function_exists( 'zc_sc_get' ) ) :
	/**
	 * همه‌ی داده‌ی یک مدخل در قالب آرایه (با کش درون‌درخواستی).
	 *
	 * @param int|WP_Post $post نوشته.
	 * @return array<string, mixed>
	 */
	function zc_sc_get( $post ) {
		static $cache = array();
		$post = get_post( $post );
		if ( ! $post ) {
			return array();
		}
		if ( isset( $cache[ $post->ID ] ) ) {
			return $cache[ $post->ID ];
		}

		$data = array(
			'id'      => $post->ID,
			'slug'    => $post->post_name,
			'title'   => get_the_title( $post ),
			'url'     => get_permalink( $post ),
			'summary' => has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( (string) $post->post_content ), 30 ),
		);

		foreach ( zc_sc_fields() as $key => $def ) {
			$raw = get_post_meta( $post->ID, $def[0], true );
			switch ( $def[1] ) {
				case 'lines':
					$data[ $key ] = zc_sc_lines( (string) $raw );
					break;
				case 'slugs':
					$data[ $key ] = array_values( array_filter( array_map( 'sanitize_title', explode( ',', (string) $raw ) ) ) );
					break;
				case 'int':
					$data[ $key ] = (int) $raw;
					break;
				default:
					$data[ $key ] = trim( (string) $raw );
			}
		}

		$terms         = zc_sc_terms( $post->ID );
		$group_slug    = $terms['group'] ? $terms['group']->slug : '';
		$top_slug      = $terms['top'] ? $terms['top']->slug : '';
		$def_group     = zc_sc_group( $group_slug );
		$def_top       = zc_sc_group( $top_slug );
		$data['group'] = $terms['group'];
		$data['top']   = $terms['top'];
		$data['type']  = ! empty( $def_top['type'] ) ? $def_top['type'] : ( ! empty( $def_group['type'] ) ? $def_group['type'] : 'ems' );
		$data['key']   = ! empty( $def_top['key'] ) ? $def_top['key'] : 'ems';
		$data['color'] = ! empty( $def_group['color'] ) ? $def_group['color'] : ( ! empty( $def_top['color'] ) ? $def_top['color'] : '#1D3A72' );
		$data['icon']  = ! empty( $def_group['icon'] ) ? $def_group['icon'] : ( ! empty( $def_top['icon'] ) ? $def_top['icon'] : 'brain' );

		$cache[ $post->ID ] = $data;
		return $data;
	}
endif;

if ( ! function_exists( 'zc_sc_strip_label' ) ) :
	/**
	 * حذف پیشوند «برچسب:» از ابتدای متن (وقتی تیتر بخش همان برچسب است).
	 *
	 * @param string $text  متن.
	 * @param string $label برچسب.
	 * @return string
	 */
	function zc_sc_strip_label( $text, $label ) {
		$text = trim( (string) $text );
		foreach ( array( $label . ':', $label . ' :' ) as $prefix ) {
			if ( 0 === strpos( $text, $prefix ) ) {
				return trim( substr( $text, strlen( $prefix ) ) );
			}
		}
		return $text;
	}
endif;

if ( ! function_exists( 'zc_sc_query_all' ) ) :
	/**
	 * همه‌ی مدخل‌ها به ترتیب (یک کوئری، با کش درون‌درخواستی).
	 *
	 * @return WP_Post[]
	 */
	function zc_sc_query_all() {
		static $posts = null;
		if ( null !== $posts ) {
			return $posts;
		}
		$q     = new WP_Query(
			array(
				'post_type'              => 'zc_schema',
				'post_status'            => 'publish',
				'posts_per_page'         => 300,
				'no_found_rows'          => true,
				'orderby'                => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
			)
		);
		$posts = $q->posts;
		return $posts;
	}
endif;

if ( ! function_exists( 'zc_sc_by_group' ) ) :
	/**
	 * مدخل‌ها دسته‌بندی‌شده بر اساس نامک گروه مستقیم.
	 *
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	function zc_sc_by_group() {
		static $map = null;
		if ( null !== $map ) {
			return $map;
		}
		$map = array();
		foreach ( zc_sc_query_all() as $p ) {
			$item = zc_sc_get( $p );
			$slug = $item['group'] ? $item['group']->slug : '_none';
			$map[ $slug ][] = $item;
		}
		foreach ( $map as $slug => $items ) {
			usort(
				$items,
				static function ( $a, $b ) {
					return ( (int) $a['order'] - (int) $b['order'] ) ?: strcmp( (string) $a['title'], (string) $b['title'] );
				}
			);
			$map[ $slug ] = $items;
		}
		return $map;
	}
endif;

if ( ! function_exists( 'zc_sc_find' ) ) :
	/**
	 * یافتن مدخل با نامک.
	 *
	 * @param string $slug نامک.
	 * @return array<string, mixed>
	 */
	function zc_sc_find( $slug ) {
		foreach ( zc_sc_query_all() as $p ) {
			if ( $p->post_name === $slug ) {
				return zc_sc_get( $p );
			}
		}
		return array();
	}
endif;

if ( ! function_exists( 'zc_sc_hub_url' ) ) :
	/**
	 * نشانی برگه‌ی مرکزی کتابخانه.
	 *
	 * @return string
	 */
	function zc_sc_hub_url() {
		static $url = null;
		if ( null !== $url ) {
			return $url;
		}
		$page = get_posts(
			array(
				'post_type'        => 'page',
				'post_status'      => 'publish',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'meta_key'         => '_zc_demo_page', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'       => 'schemas',       // phpcs:ignore WordPress.DB.SlowDBQuery
				'no_found_rows'    => true,
				'suppress_filters' => true,
			)
		);
		if ( ! $page ) {
			$by_path = get_page_by_path( 'schemas' );
			$page    = $by_path ? array( $by_path->ID ) : array();
		}
		if ( $page ) {
			$url = (string) get_permalink( (int) $page[0] );
		} else {
			// بدون برگه‌ی مرکزی (پیش از نصب دمو): بایگانی نخستین گروه اصلی؛ /schemas/ بایگانی ندارد.
			$url   = '';
			$first = get_terms(
				array(
					'taxonomy'   => 'zc_schema_group',
					'parent'     => 0,
					'hide_empty' => false,
					'number'     => 1,
				)
			);
			if ( ! is_wp_error( $first ) && $first ) {
				$link = get_term_link( $first[0] );
				$url  = is_wp_error( $link ) ? '' : (string) $link;
			}
			if ( '' === $url ) {
				$url = home_url( '/' );
			}
		}
		/** فیلتر نشانی برگه‌ی مرکزی کتابخانه‌ی طرحواره‌ها. */
		$url = (string) apply_filters( 'zc_schemas_hub_url', $url );
		return $url;
	}
endif;

if ( ! function_exists( 'zc_sc_number' ) ) :
	/**
	 * نشان کارت: کد کوتاه یا شماره‌ی ترتیب (فارسی، دو رقمی).
	 *
	 * @param array<string, mixed> $item مدخل.
	 * @return string
	 */
	function zc_sc_number( $item ) {
		if ( '' !== (string) $item['code'] ) {
			return (string) $item['code'];
		}
		$n = str_pad( (string) max( 1, (int) $item['order'] ), 2, '0', STR_PAD_LEFT );
		return function_exists( 'zc_digits_to_persian' ) ? zc_digits_to_persian( $n ) : $n;
	}
endif;

/* ------------------------------------------------------------------ *
 * خروجی کارت و شبکه
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_sc_card' ) ) :
	/**
	 * کارت یک مدخل.
	 *
	 * @param array<string, mixed> $item مدخل.
	 * @param array<string, mixed> $args show_en, show_summary, show_group, tag.
	 * @return string
	 */
	function zc_sc_card( $item, $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'show_en'      => true,
				'show_summary' => true,
				'show_group'   => false,
				'tag'          => 'h3',
				'more_label'   => '',
			)
		);
		$tag   = in_array( $args['tag'], array( 'h2', 'h3', 'h4' ), true ) ? $args['tag'] : 'h3';
		$group = $item['group'] ? zc_sc_group( $item['group']->slug ) : array();
		$alias = zc_sc_search_aliases();
		$slug  = isset( $item['slug'] ) ? (string) $item['slug'] : '';
		$text  = implode( ' ', array( $item['title'], $item['en'], $item['code'], $item['summary'], $item['belief'], isset( $alias[ $slug ] ) ? $alias[ $slug ] : '' ) );
		$badge = zc_sc_number( $item );
		$mono  = '' !== (string) $item['code'];

		ob_start();
		?>
		<a class="zc-sc-card" href="<?php echo esc_url( $item['url'] ); ?>" style="--sc:<?php echo esc_attr( $item['color'] ); ?>" data-zc-sc-card data-zc-sc-text="<?php echo esc_attr( wp_strip_all_tags( $text ) ); ?>">
			<span class="zc-sc-card-top">
				<span class="zc-sc-code<?php echo $mono ? ' is-code' : ''; ?>"<?php echo $mono ? ' dir="ltr"' : ''; ?>><?php echo esc_html( $badge ); ?></span>
				<?php if ( $args['show_group'] && $group ) : ?>
					<span class="zc-sc-card-group"><?php echo esc_html( $group['short'] ); ?></span>
				<?php endif; ?>
				<span class="zc-sc-card-icon" aria-hidden="true"><?php zc_icon( (string) $item['icon'], 'h-4 w-4' ); ?></span>
			</span>
			<<?php echo esc_html( $tag ); ?> class="zc-sc-card-title"><?php echo esc_html( $item['title'] ); ?></<?php echo esc_html( $tag ); ?>>
			<?php if ( $args['show_en'] && '' !== (string) $item['en'] ) : ?>
				<span class="zc-sc-card-en" dir="ltr" lang="en"><?php echo esc_html( $item['en'] ); ?></span>
			<?php endif; ?>
			<?php if ( $args['show_summary'] && '' !== (string) $item['summary'] ) : ?>
				<p class="zc-sc-card-text"><?php echo esc_html( $item['summary'] ); ?></p>
			<?php endif; ?>
			<span class="zc-sc-card-more"><?php echo esc_html( '' !== trim( (string) $args['more_label'] ) ? (string) $args['more_label'] : __( 'شرح کامل', 'zarincoach' ) ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></span>
		</a>
		<?php
		return (string) ob_get_clean();
	}
endif;

if ( ! function_exists( 'zc_sc_grid_class' ) ) :
	/**
	 * کلاس ستون‌های شبکه.
	 *
	 * @param int $columns تعداد ستون.
	 * @return string
	 */
	function zc_sc_grid_class( $columns ) {
		$columns = max( 1, min( 4, (int) $columns ) );
		return 'zc-sc-grid zc-sc-cols-' . $columns;
	}
endif;

if ( ! function_exists( 'zc_schemas_grid' ) ) :
	/**
	 * شبکه‌ی کامل کتابخانه با فیلتر، جستجو و زیرعنوان حوزه‌ها.
	 *
	 * @param array<string, mixed> $args تنظیمات.
	 * @return string
	 */
	function zc_schemas_grid( $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'groups'       => array(),     // نامک گروه‌های سطح بالا؛ خالی = همه.
				'filter'       => true,
				'search'       => true,
				'columns'      => 3,
				'subgroups'    => true,
				'group_desc'   => true,
				'show_en'      => true,
				'show_summary' => true,
				'heading_tag'  => 'h2',
				'default'      => 'all',
				'all_label'    => '',
				'placeholder'  => '',
				'empty_text'   => '',
				'need_label'   => '',
				'more_label'   => '',
			)
		);
		$txt = array(
			'all'   => '' !== trim( (string) $args['all_label'] ) ? (string) $args['all_label'] : __( 'همه', 'zarincoach' ),
			'ph'    => '' !== trim( (string) $args['placeholder'] ) ? (string) $args['placeholder'] : __( 'جستجو: مثلاً رهاشدگی، Shame یا کمال‌گرایی', 'zarincoach' ),
			'empty' => '' !== trim( (string) $args['empty_text'] ) ? (string) $args['empty_text'] : __( 'چیزی با این عبارت پیدا نکردیم. می‌تونید واژه‌ی دیگه‌ای رو امتحان کنید یا فیلتر «همه» رو بزنید.', 'zarincoach' ),
			'need'  => '' !== trim( (string) $args['need_label'] ) ? (string) $args['need_label'] : __( 'نیاز: %s', 'zarincoach' ),
		);

		$tops = zc_sc_top_groups();
		if ( ! empty( $args['groups'] ) ) {
			$tops = array_intersect_key( $tops, array_flip( (array) $args['groups'] ) );
		}
		$map = zc_sc_by_group();

		// ساخت داده‌ی بخش‌ها و شمارش.
		$sections = array();
		$total    = 0;
		foreach ( $tops as $top_slug => $top ) {
			$children = zc_sc_child_groups( $top_slug );
			$subs     = array();
			$count    = 0;
			if ( $children ) {
				foreach ( $children as $child_slug => $child ) {
					$items = isset( $map[ $child_slug ] ) ? $map[ $child_slug ] : array();
					if ( $items ) {
						$subs[ $child_slug ] = array(
							'def'   => $child,
							'items' => $items,
						);
						$count += count( $items );
					}
				}
			}
			if ( ! empty( $map[ $top_slug ] ) ) {
				$subs = array(
					$top_slug => array(
						'def'   => array(),
						'items' => $map[ $top_slug ],
					),
				) + $subs;
				$count += count( $map[ $top_slug ] );
			}
			if ( ! $count ) {
				continue;
			}
			$sections[ $top_slug ] = array(
				'def'   => $top,
				'subs'  => $subs,
				'count' => $count,
			);
			$total += $count;
		}

		if ( ! $sections ) {
			if ( current_user_can( 'edit_posts' ) ) {
				return '<p class="zc-sc-empty-admin">' . esc_html__( 'هنوز مدخلی در کتابخانه‌ی طرحواره‌ها وجود ندارد. از «طرحواره‌ها ← افزودن مدخل» یا نصب دمو استفاده کنید.', 'zarincoach' ) . '</p>';
			}
			return '';
		}

		zc_sc_schema_termset( $sections );

		$h_tag   = in_array( $args['heading_tag'], array( 'h2', 'h3' ), true ) ? $args['heading_tag'] : 'h2';
		$sub_tag = 'h2' === $h_tag ? 'h3' : 'h4';
		$uid     = wp_unique_id( 'zc-sc-' );
		$fa      = static function ( $n ) {
			return function_exists( 'zc_digits_to_persian' ) ? zc_digits_to_persian( (string) $n ) : (string) $n;
		};
		$multi   = count( $sections ) > 1;
		$default = ( 'all' !== $args['default'] && isset( $sections[ $args['default'] ] ) ) ? (string) $args['default'] : 'all';

		ob_start();
		?>
		<div class="zc-sc-hub" data-zc-sc data-zc-sc-default="<?php echo esc_attr( $default ); ?>">
			<?php if ( ( $args['filter'] && $multi ) || $args['search'] ) : ?>
				<div class="zc-sc-toolbar">
					<?php if ( $args['filter'] && $multi ) : ?>
						<div class="zc-sc-chips" role="group" aria-label="<?php esc_attr_e( 'فیلتر بر اساس دسته', 'zarincoach' ); ?>">
							<button type="button" class="zc-sc-chip<?php echo 'all' === $default ? ' is-active' : ''; ?>" data-zc-sc-filter="all" aria-pressed="<?php echo 'all' === $default ? 'true' : 'false'; ?>">
								<?php echo esc_html( $txt['all'] ); ?>
								<span class="zc-sc-chip-count"><?php echo esc_html( $fa( $total ) ); ?></span>
							</button>
							<?php foreach ( $sections as $slug => $sec ) : ?>
								<button type="button" class="zc-sc-chip<?php echo $default === $slug ? ' is-active' : ''; ?>" data-zc-sc-filter="<?php echo esc_attr( $slug ); ?>" aria-pressed="<?php echo $default === $slug ? 'true' : 'false'; ?>" style="--sc:<?php echo esc_attr( $sec['def']['color'] ); ?>">
									<?php echo esc_html( $sec['def']['short'] ); ?>
									<span class="zc-sc-chip-count"><?php echo esc_html( $fa( $sec['count'] ) ); ?></span>
								</button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
					<?php if ( $args['search'] ) : ?>
						<label class="zc-sc-search" for="<?php echo esc_attr( $uid ); ?>-q">
							<span class="screen-reader-text"><?php esc_html_e( 'جستجو در طرحواره‌ها و الگوها', 'zarincoach' ); ?></span>
							<?php zc_icon( 'search', 'h-4 w-4' ); ?>
							<input type="search" id="<?php echo esc_attr( $uid ); ?>-q" data-zc-sc-search placeholder="<?php echo esc_attr( $txt['ph'] ); ?>" autocomplete="off" enterkeyhint="search">
						</label>
					<?php endif; ?>
				</div>
				<p class="zc-sc-status screen-reader-text" data-zc-sc-status aria-live="polite" data-tpl="<?php esc_attr_e( '%s مورد یافت شد', 'zarincoach' ); ?>"></p>
			<?php endif; ?>

			<?php foreach ( $sections as $slug => $sec ) : ?>
				<?php $hidden = ( 'all' !== $default && $default !== $slug ); ?>
				<section class="zc-sc-sec" id="<?php echo esc_attr( $sec['def']['key'] ); ?>" data-zc-sc-sec="<?php echo esc_attr( $slug ); ?>" style="--sc:<?php echo esc_attr( $sec['def']['color'] ); ?>"<?php echo $hidden ? ' hidden' : ''; ?>>
					<header class="zc-sc-sec-head">
						<span class="zc-sc-sec-icon" aria-hidden="true"><?php zc_icon( (string) $sec['def']['icon'], 'h-6 w-6' ); ?></span>
						<div class="zc-sc-sec-titles">
							<<?php echo esc_html( $h_tag ); ?> class="zc-sc-sec-title">
								<?php echo esc_html( $sec['def']['name'] ); ?>
								<span class="zc-sc-sec-count"><?php echo esc_html( sprintf( /* translators: %s: تعداد */ __( '%s مدخل', 'zarincoach' ), $fa( $sec['count'] ) ) ); ?></span>
							</<?php echo esc_html( $h_tag ); ?>>
							<?php if ( $args['group_desc'] && ! empty( $sec['def']['desc'] ) ) : ?>
								<p class="zc-sc-sec-desc"><?php echo esc_html( $sec['def']['desc'] ); ?></p>
							<?php endif; ?>
						</div>
					</header>

					<?php foreach ( $sec['subs'] as $sub_slug => $sub ) : ?>
						<?php $has_sub_head = $args['subgroups'] && ! empty( $sub['def'] ); ?>
						<div class="zc-sc-sub" data-zc-sc-sub<?php echo ! empty( $sub['def']['color'] ) ? ' style="--sc:' . esc_attr( $sub['def']['color'] ) . '"' : ''; ?>>
							<?php if ( $has_sub_head ) : ?>
								<div class="zc-sc-sub-head">
									<<?php echo esc_html( $sub_tag ); ?> class="zc-sc-sub-title">
										<a href="<?php echo esc_url( (string) get_term_link( $sub_slug, 'zc_schema_group' ) ); ?>"><?php echo esc_html( $sub['def']['name'] ); ?></a>
									</<?php echo esc_html( $sub_tag ); ?>>
									<?php if ( ! empty( $sub['def']['need'] ) ) : ?>
										<span class="zc-sc-need"><?php zc_icon( 'heart', 'h-3.5 w-3.5' ); ?><?php echo esc_html( false !== strpos( $txt['need'], '%s' ) ? str_replace( '%s', $sub['def']['need'], $txt['need'] ) : $txt['need'] . ' ' . $sub['def']['need'] ); ?></span>
									<?php endif; ?>
									<?php if ( $args['group_desc'] && ! empty( $sub['def']['desc'] ) ) : ?>
										<p class="zc-sc-sub-desc"><?php echo esc_html( $sub['def']['desc'] ); ?></p>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<div class="<?php echo esc_attr( zc_sc_grid_class( $args['columns'] ) ); ?>">
								<?php
								foreach ( $sub['items'] as $item ) {
									echo zc_sc_card( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
										$item,
										array(
											'show_en'      => $args['show_en'],
											'show_summary' => $args['show_summary'],
											'tag'          => $has_sub_head ? 'h4' : $sub_tag,
											'more_label'   => isset( $args['more_label'] ) ? (string) $args['more_label'] : '',
										)
									);
								}
								?>
							</div>
						</div>
					<?php endforeach; ?>
				</section>
			<?php endforeach; ?>

			<div class="zc-sc-empty" data-zc-sc-empty hidden>
				<?php zc_icon( 'search', 'h-6 w-6' ); ?>
				<p><?php echo esc_html( $txt['empty'] ); ?></p>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}
endif;

if ( ! function_exists( 'zc_sc_search_aliases' ) ) :
	/**
	 * واژه‌های رایج و محاوره‌ای برای جست‌وجوی کتابخانه (مثلاً «کمال‌گرایی» ← معیارهای سرسختانه).
	 * فقط در متن جست‌وجوی کارت می‌آید و نمایش داده نمی‌شود.
	 *
	 * @return array<string, string> نامک مدخل => واژه‌ها.
	 */
	function zc_sc_search_aliases() {
		static $map = null;
		if ( null === $map ) {
			$map = (array) apply_filters(
				'zc_schemas_search_aliases',
				array(
					'abandonment'               => 'ترس از ترک شدن ترس از جدایی وابستگی عاطفی حسادت',
					'mistrust-abuse'            => 'بی‌اعتمادی بدگمانی سوءاستفاده خیانت',
					'emotional-deprivation'     => 'کمبود محبت تنهایی عاطفی درک نشدن',
					'defectiveness-shame'       => 'شرم بی‌ارزشی حس نقص خودکم‌بینی عزت نفس پایین',
					'social-isolation'          => 'تنهایی غریبگی متفاوت بودن',
					'dependence-incompetence'   => 'ناتوانی در تصمیم‌گیری بی‌عرضگی وابستگی',
					'vulnerability-to-harm'     => 'اضطراب نگرانی ترس از بیماری ترس از فاجعه',
					'enmeshment'                => 'وابستگی به والدین بی‌هویتی درهم‌تنیدگی',
					'failure'                   => 'ناکامی احساس عقب ماندن مقایسه',
					'entitlement'               => 'خودشیفتگی خودمحوری حق به جانب',
					'insufficient-self-control' => 'اهمال‌کاری تنبلی بی‌حوصلگی تکانشگری',
					'subjugation'               => 'نه گفتن مردم‌داری People Pleasing سرکوب خشم',
					'self-sacrifice'            => 'از خودگذشتگی نجات‌دهنده نادیده گرفتن خود',
					'approval-seeking'          => 'تأییدطلبی دیده شدن لایک',
					'negativity-pessimism'      => 'منفی‌بافی نگرانی',
					'emotional-inhibition'      => 'سرکوب احساسات خونسردی افراطی',
					'unrelenting-standards'     => 'کمال‌گرایی کمال گرا پرفکشنیسم Perfectionism وسواس کاری فرسودگی',
					'punitiveness'              => 'خودانتقادی سرزنش خود نبخشیدن',
					'vulnerable-child'          => 'غم تنهایی ترس',
					'angry-child'               => 'خشم عصبانیت',
					'enraged-child'             => 'خشم انفجاری کنترل خشم',
					'detached-protector'        => 'بی‌حسی کرختی بی‌تفاوتی',
					'detached-self-soother'     => 'پرخوری گوشی اعتیاد رفتاری',
					'compliant-surrenderer'     => 'مردم‌داری نه گفتن',
					'demanding-parent'          => 'کمال‌گرایی منتقد درونی',
					'punitive-parent'           => 'منتقد درونی خودانتقادی',
					'catastrophizing'           => 'بزرگ‌نمایی فاجعه اضطراب',
					'should-statements'         => 'باید نباید',
					'all-or-nothing'            => 'سیاه و سفید کمال‌گرایی',
				)
			);
		}
		return $map;
	}
endif;

if ( ! function_exists( 'zc_sc_schema_termset' ) ) :
	/**
	 * گره DefinedTermSet برای برگه‌ی مرکزی کتابخانه (فقط وقتی شبکه در همان برگه رندر شود).
	 *
	 * @param array $sections بخش‌های رندرشده.
	 * @return void
	 */
	function zc_sc_schema_termset( $sections ) {
		if ( ! is_singular() || ! function_exists( 'zc_schema_add_node' ) || ! function_exists( 'zc_schema_can_collect' ) || ! zc_schema_can_collect() ) {
			return;
		}
		$hub  = zc_sc_hub_url();
		$here = (string) get_permalink( get_queried_object_id() );
		if ( untrailingslashit( $hub ) !== untrailingslashit( $here ) || zc_schema_hint( 'schemas_hub' ) ) {
			return;
		}
		$terms = array();
		foreach ( $sections as $sec ) {
			foreach ( $sec['subs'] as $sub ) {
				foreach ( $sub['items'] as $item ) {
					$terms[] = array_filter(
						array(
							'@type'         => 'DefinedTerm',
							'@id'           => $item['url'] . '#term',
							'name'          => (string) $item['title'],
							'alternateName' => '' !== (string) $item['en'] ? (string) $item['en'] : null,
							'termCode'      => '' !== (string) $item['code'] ? (string) $item['code'] : null,
							'url'           => $item['url'],
						)
					);
				}
			}
		}
		$id = $hub . '#termset';
		zc_schema_add_node(
			array(
				'@type'          => 'DefinedTermSet',
				'@id'            => $id,
				'name'           => get_the_title( get_queried_object_id() ),
				'description'    => __( 'طرحواره‌های ناسازگار اولیه، ذهنیت‌های طرحواره‌ای، سبک‌های مقابله و خطاهای شناختی', 'zarincoach' ),
				'url'            => $hub,
				'inLanguage'     => 'fa-IR',
				'hasDefinedTerm' => $terms,
			)
		);
		zc_schema_hint( 'schemas_hub', $id );
	}
endif;

/* ------------------------------------------------------------------ *
 * متاباکس
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_sc_add_meta_box' ) ) :
	/**
	 * ثبت متاباکس.
	 *
	 * @return void
	 */
	function zc_sc_add_meta_box() {
		add_meta_box( 'zc_schema_details', __( 'جزئیات طرحواره / الگو', 'zarincoach' ), 'zc_sc_render_meta_box', 'zc_schema', 'normal', 'high' );
	}
endif;
add_action( 'add_meta_boxes', 'zc_sc_add_meta_box' );

if ( ! function_exists( 'zc_sc_render_meta_box' ) ) :
	/**
	 * خروجی متاباکس.
	 *
	 * @param WP_Post $post نوشته.
	 * @return void
	 */
	function zc_sc_render_meta_box( $post ) {
		wp_nonce_field( 'zc_save_schema_meta', 'zc_schema_meta_nonce' );
		echo '<p class="description">' . esc_html__( 'خلاصه‌ی کارت از «چکیده» و شرح کامل از ویرایشگر اصلی خوانده می‌شود. فیلدهای خالی در صفحه نمایش داده نمی‌شوند. فیلدهای «مقابله» فقط برای طرحواره‌های ناسازگار اولیه کاربرد دارند.', 'zarincoach' ) . '</p>';
		echo '<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:12px 18px">';
		foreach ( zc_sc_fields() as $key => $def ) {
			$value = get_post_meta( $post->ID, $def[0], true );
			$id    = 'zc_sc_' . $key;
			$wide  = in_array( $def[1], array( 'textarea', 'lines' ), true );
			echo '<p style="margin:0' . ( $wide ? ';grid-column:1/-1' : '' ) . '"><label for="' . esc_attr( $id ) . '"><strong>' . esc_html( $def[2] ) . '</strong></label><br>';
			if ( $wide ) {
				echo '<textarea class="widefat" rows="' . ( 'lines' === $def[1] ? 5 : 3 ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '">' . esc_textarea( (string) $value ) . '</textarea>';
			} else {
				$type = 'int' === $def[1] ? 'number' : 'text';
				$dir  = in_array( $key, array( 'en', 'code', 'related' ), true ) ? ' dir="ltr"' : '';
				echo '<input class="widefat" type="' . esc_attr( $type ) . '"' . $dir . ' id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( (string) $value ) . '">'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			echo '</p>';
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_sc_save_meta' ) ) :
	/**
	 * ذخیره‌ی متاباکس.
	 *
	 * @param int $post_id شناسه.
	 * @return void
	 */
	function zc_sc_save_meta( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( ! isset( $_POST['zc_schema_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zc_schema_meta_nonce'] ) ), 'zc_save_schema_meta' ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		foreach ( zc_sc_fields() as $key => $def ) {
			$name = 'zc_sc_' . $key;
			if ( ! isset( $_POST[ $name ] ) ) {
				continue;
			}
			$raw = wp_unslash( $_POST[ $name ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			switch ( $def[1] ) {
				case 'int':
					$val = (string) absint( $raw );
					break;
				case 'slugs':
					$val = implode( ',', array_filter( array_map( 'sanitize_title', explode( ',', (string) $raw ) ) ) );
					break;
				case 'textarea':
				case 'lines':
					$val = sanitize_textarea_field( (string) $raw );
					break;
				default:
					$val = sanitize_text_field( (string) $raw );
			}
			if ( '' === $val ) {
				delete_post_meta( $post_id, $def[0] );
			} else {
				update_post_meta( $post_id, $def[0], $val );
			}
		}
	}
endif;
add_action( 'save_post_zc_schema', 'zc_sc_save_meta' );

/* ------------------------------------------------------------------ *
 * مسیر راهنما، ترتیب آرشیو و بدنه
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_sc_breadcrumbs' ) ) :
	/**
	 * افزودن برگه‌ی مرکزی (و گروه) به مسیر راهنمای مدخل‌ها و آرشیو گروه.
	 *
	 * @param array $items آیتم‌ها.
	 * @return array
	 */
	function zc_sc_breadcrumbs( $items ) {
		$is_single = is_singular( 'zc_schema' );
		$is_tax    = is_tax( 'zc_schema_group' );
		if ( ( ! $is_single && ! $is_tax ) || count( $items ) < 2 ) {
			return $items;
		}

		$hub_url   = zc_sc_hub_url();
		$hub_id    = url_to_postid( $hub_url );
		$hub_label = $hub_id ? get_the_title( $hub_id ) : __( 'طرحواره‌ها و الگوهای ذهنی', 'zarincoach' );
		// بدون برگه‌ی مرکزی، حلقه‌ی مرکز حذف می‌شود تا بایگانی گروه تکراری نشود.
		$middle    = $hub_id ? array( array( 'label' => $hub_label, 'url' => $hub_url ) ) : array();

		if ( $is_single ) {
			$item = zc_sc_get( get_queried_object_id() );
			if ( ! empty( $item['group'] ) ) {
				$top = $item['top'];
				if ( $top && $top->term_id !== $item['group']->term_id ) {
					$middle[] = array( 'label' => $top->name, 'url' => (string) get_term_link( $top ) );
				}
				$middle[] = array( 'label' => $item['group']->name, 'url' => (string) get_term_link( $item['group'] ) );
			}
		} else {
			$term = get_queried_object();
			if ( $term && ! empty( $term->parent ) ) {
				$parent = get_term( $term->parent, 'zc_schema_group' );
				if ( $parent && ! is_wp_error( $parent ) ) {
					$middle[] = array( 'label' => $parent->name, 'url' => (string) get_term_link( $parent ) );
				}
			}
		}

		$last = array_pop( $items );
		return array_merge( $items, $middle, array( $last ) );
	}
endif;
add_filter( 'zc_breadcrumb_items', 'zc_sc_breadcrumbs' );

if ( ! function_exists( 'zc_sc_archive_query' ) ) :
	/**
	 * آرشیو گروه: همه‌ی مدخل‌ها در یک صفحه و به ترتیب.
	 *
	 * @param WP_Query $q کوئری.
	 * @return void
	 */
	function zc_sc_archive_query( $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! $q->is_tax( 'zc_schema_group' ) ) {
			return;
		}
		$q->set( 'posts_per_page', 100 );
		$q->set( 'no_found_rows', true );
		$q->set( 'orderby', array( 'menu_order' => 'ASC', 'title' => 'ASC' ) );
	}
endif;
add_action( 'pre_get_posts', 'zc_sc_archive_query' );

if ( ! function_exists( 'zc_sc_admin_columns' ) ) :
	/**
	 * ستون کد/ترتیب در فهرست مدیریت.
	 *
	 * @param array $cols ستون‌ها.
	 * @return array
	 */
	function zc_sc_admin_columns( $cols ) {
		$out = array();
		foreach ( $cols as $k => $v ) {
			$out[ $k ] = $v;
			if ( 'title' === $k ) {
				$out['zc_sc_code'] = __( 'کد / ترتیب', 'zarincoach' );
			}
		}
		return $out;
	}
endif;
add_filter( 'manage_zc_schema_posts_columns', 'zc_sc_admin_columns' );

add_action(
	'manage_zc_schema_posts_custom_column',
	static function ( $col, $post_id ) {
		if ( 'zc_sc_code' === $col ) {
			$code  = (string) get_post_meta( $post_id, '_zc_sc_code', true );
			$order = (string) get_post_meta( $post_id, '_zc_sc_order', true );
			echo esc_html( trim( $code . ' · ' . $order, ' ·' ) );
		}
	},
	10,
	2
);

if ( ! function_exists( 'zc_sc_body_class' ) ) :
	/**
	 * کلاس بدنه برای صفحات کتابخانه.
	 *
	 * @param string[] $classes کلاس‌ها.
	 * @return string[]
	 */
	function zc_sc_body_class( $classes ) {
		if ( is_singular( 'zc_schema' ) || is_tax( 'zc_schema_group' ) ) {
			$classes[] = 'zc-schema-library';
		}
		return $classes;
	}
endif;
add_filter( 'body_class', 'zc_sc_body_class' );

/* ------------------------------------------------------------------ *
 * شورت‌کد و پیوند برچسب‌های آزاد
 * ------------------------------------------------------------------ */

if ( ! function_exists( 'zc_schemas_shortcode' ) ) :
	/**
	 * شورت‌کد [zc_schemas groups="ems,modes" columns="3" filter="1" search="1"] برای حالت بدون المنتور.
	 *
	 * @param array|string $atts ویژگی‌ها.
	 * @return string
	 */
	function zc_schemas_shortcode( $atts ) {
		$atts   = shortcode_atts(
			array(
				'groups'  => '',
				'columns' => '3',
				'filter'  => '1',
				'search'  => '1',
			),
			$atts,
			'zc_schemas'
		);
		$keys   = array_filter( array_map( 'trim', explode( ',', (string) $atts['groups'] ) ) );
		$groups = array();
		foreach ( zc_sc_top_groups() as $slug => $g ) {
			if ( in_array( $slug, $keys, true ) || in_array( $g['key'], $keys, true ) ) {
				$groups[] = $slug;
			}
		}
		return '<div class="not-prose">' . zc_schemas_grid(
			array(
				'groups'  => $groups,
				'columns' => (int) $atts['columns'],
				'filter'  => '1' === (string) $atts['filter'],
				'search'  => '1' === (string) $atts['search'],
			)
		) . '</div>';
	}
endif;
add_shortcode( 'zc_schemas', 'zc_schemas_shortcode' );

if ( ! function_exists( 'zc_sc_url_for_label' ) ) :
	/**
	 * نشانی مدخل متناظر با برچسب‌های آزاد صفحه‌ی نخست (مثل «کمال‌گرایی پنهان»).
	 *
	 * @param string $label برچسب.
	 * @return string نشانی یا رشته‌ی خالی.
	 */
	function zc_sc_url_for_label( $label ) {
		$map = array(
			'کمال‌گرایی پنهان'       => 'unrelenting-standards',
			'اهمالکاری'              => 'insufficient-self-control',
			'بلاتکلیفی در تصمیم'     => 'dependence-incompetence',
			'ذهن شلوغ'               => 'negativity-pessimism',
			'نه نگفتن و مرزهای مبهم' => 'subjugation',
			'کنترل‌گری مدیرانه'      => 'demanding-parent',
			'کنترل‌گری'              => 'demanding-parent',
			'کمال‌گرایی مدیریتی'     => 'unrelenting-standards',
			'تأییدطلبی'              => 'approval-seeking',
		);
		/**
		 * فیلتر نگاشت برچسب‌ها به نامک مدخل‌ها.
		 *
		 * @param array $map نگاشت.
		 */
		$map   = (array) apply_filters( 'zc_schema_label_map', $map );
		$label = trim( (string) $label );
		if ( empty( $map[ $label ] ) ) {
			return '';
		}
		$item = zc_sc_find( (string) $map[ $label ] );
		return ! empty( $item['url'] ) ? (string) $item['url'] : '';
	}
endif;
