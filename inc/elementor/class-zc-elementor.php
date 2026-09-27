<?php
/**
 * ماژول المنتور: ثبت دسته‌بندی و ویجت‌های اختصاصی زرین‌کوچ
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Elementor' ) ) :

	/**
	 * کلاس مدیریت یکپارچه ویجت‌های المنتور.
	 */
	class ZC_Elementor {

		/**
		 * نمونه یکتا.
		 *
		 * @var ZC_Elementor|null
		 */
		private static $instance = null;

		/**
		 * فهرست ویجت‌ها.
		 *
		 * @var array<string, array{file:string, class:string, title:string}>
		 */
		private $widgets = array(
			'hero'         => array( 'file' => 'class-zc-widget-hero.php', 'class' => 'ZC_Widget_Hero', 'title' => 'سربرگ اصلی' ),
			'heading'      => array( 'file' => 'class-zc-widget-heading.php', 'class' => 'ZC_Widget_Heading', 'title' => 'سربرگ بخش' ),
			'about'        => array( 'file' => 'class-zc-widget-about.php', 'class' => 'ZC_Widget_About', 'title' => 'درباره من' ),
			'services'     => array( 'file' => 'class-zc-widget-services.php', 'class' => 'ZC_Widget_Services', 'title' => 'خدمات و برنامه‌ها' ),
			'schema'       => array( 'file' => 'class-zc-widget-schema.php', 'class' => 'ZC_Widget_Schema', 'title' => 'طرحواره‌ها' ),
			'schemas'      => array( 'file' => 'class-zc-widget-schemas.php', 'class' => 'ZC_Widget_Schemas', 'title' => 'کتابخانه‌ی طرحواره‌ها' ),
			'process'      => array( 'file' => 'class-zc-widget-process.php', 'class' => 'ZC_Widget_Process', 'title' => 'مسیر همراهی' ),
			'stats'        => array( 'file' => 'class-zc-widget-stats.php', 'class' => 'ZC_Widget_Stats', 'title' => 'آمار و ارقام' ),
			'testimonials' => array( 'file' => 'class-zc-widget-testimonials.php', 'class' => 'ZC_Widget_Testimonials', 'title' => 'تجربه مراجعان' ),
			'pricing'      => array( 'file' => 'class-zc-widget-pricing.php', 'class' => 'ZC_Widget_Pricing', 'title' => 'بسته‌های همراهی' ),
			'faq'          => array( 'file' => 'class-zc-widget-faq.php', 'class' => 'ZC_Widget_Faq', 'title' => 'پرسش‌های پرتکرار' ),
			'cta'          => array( 'file' => 'class-zc-widget-cta.php', 'class' => 'ZC_Widget_Cta', 'title' => 'فراخوان اقدام' ),
			'posts'        => array( 'file' => 'class-zc-widget-posts.php', 'class' => 'ZC_Widget_Posts', 'title' => 'آخرین نوشته‌ها' ),
			'contact'      => array( 'file' => 'class-zc-widget-contact.php', 'class' => 'ZC_Widget_Contact', 'title' => 'اطلاعات تماس' ),
			'marquee'      => array( 'file' => 'class-zc-widget-marquee.php', 'class' => 'ZC_Widget_Marquee', 'title' => 'نوار کلمات' ),
			'list'         => array( 'file' => 'class-zc-widget-list.php', 'class' => 'ZC_Widget_List', 'title' => 'فهرست ویژگی‌ها' ),
			'form'         => array( 'file' => 'class-zc-widget-form.php', 'class' => 'ZC_Widget_Form', 'title' => 'فرم رزرو/تماس' ),
			'site-header'  => array( 'file' => 'class-zc-widget-site-header.php', 'class' => 'ZC_Widget_Site_Header', 'title' => 'سربرگ سایت (هدر)' ),
			'site-footer'  => array( 'file' => 'class-zc-widget-site-footer.php', 'class' => 'ZC_Widget_Site_Footer', 'title' => 'پاورقی سایت (فوتر)' ),
			'page-title'   => array( 'file' => 'class-zc-widget-page-title.php', 'class' => 'ZC_Widget_Page_Title', 'title' => 'عنوان برگه' ),
			'text'         => array( 'file' => 'class-zc-widget-text.php', 'class' => 'ZC_Widget_Text', 'title' => 'متن و محتوا' ),
			'resume-hero'  => array( 'file' => 'class-zc-widget-resume-hero.php', 'class' => 'ZC_Widget_Resume_Hero', 'title' => 'رزومه — معرفی حرفه‌ای' ),
			'timeline'     => array( 'file' => 'class-zc-widget-timeline.php', 'class' => 'ZC_Widget_Timeline', 'title' => 'خط زمانی سوابق' ),
			'courses'      => array( 'file' => 'class-zc-widget-courses.php', 'class' => 'ZC_Widget_Courses', 'title' => 'دوره‌ها و گواهی‌ها' ),
			'skills'       => array( 'file' => 'class-zc-widget-skills.php', 'class' => 'ZC_Widget_Skills', 'title' => 'حوزه‌های تخصصی' ),
			'book'         => array( 'file' => 'class-zc-widget-book.php', 'class' => 'ZC_Widget_Book', 'title' => 'معرفی کتاب' ),
			'toc'          => array( 'file' => 'class-zc-widget-toc.php', 'class' => 'ZC_Widget_Toc', 'title' => 'فهرست مطالب' ),
			'trust-badges' => array( 'file' => 'class-zc-widget-trust-badges.php', 'class' => 'ZC_Widget_Trust_Badges', 'title' => 'نمادهای اعتماد' ),
			// فروشگاه (فقط با ووکامرس فعال).
			'products'          => array( 'file' => 'class-zc-widget-products.php', 'class' => 'ZC_Widget_Products', 'title' => 'فروشگاه — محصولات', 'woo' => true ),
			'product-cats'      => array( 'file' => 'class-zc-widget-product-cats.php', 'class' => 'ZC_Widget_Product_Cats', 'title' => 'فروشگاه — دسته‌بندی محصولات', 'woo' => true ),
			'product-spotlight' => array( 'file' => 'class-zc-widget-product-spotlight.php', 'class' => 'ZC_Widget_Product_Spotlight', 'title' => 'فروشگاه — محصول ویژه', 'woo' => true ),
			'shop-promo'        => array( 'file' => 'class-zc-widget-shop-promo.php', 'class' => 'ZC_Widget_Shop_Promo', 'title' => 'فروشگاه — بنر تخفیف', 'woo' => true ),
			'shop-benefits'     => array( 'file' => 'class-zc-widget-shop-benefits.php', 'class' => 'ZC_Widget_Shop_Benefits', 'title' => 'فروشگاه — مزایای خرید', 'woo' => true ),
		);

		/**
		 * دریافت نمونه یکتا.
		 *
		 * @return ZC_Elementor
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
			}
			return self::$instance;
		}

		/**
		 * سازنده: اتصال به هوک‌های المنتور.
		 */
		private function __construct() {
			add_action( 'elementor/elements/categories_registered', array( $this, 'register_category' ) );

			// المنتور ۳.۵ به بالا.
			add_action( 'elementor/widgets/register', array( $this, 'register_widgets' ) );
			// سازگاری با نسخه‌های قدیمی‌تر.
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'register_widgets_legacy' ) );

			add_action( 'elementor/frontend/after_enqueue_styles', array( $this, 'frontend_styles' ) );
			add_action( 'elementor/theme/register_locations', array( $this, 'register_locations' ) );

			// v1.8: برچسب نوع زمینه روی بخش‌های سطح اول برای حذف فاصله‌ی دوبرابر بین بخش‌های هم‌زمینه.
			add_action( 'elementor/frontend/section/before_render', array( $this, 'tag_section_bg' ) );
			add_action( 'elementor/frontend/container/before_render', array( $this, 'tag_section_bg' ) );

			// v2.2: پالت اختصاصی هر ویجت (متغیرهای --zc-*-rgb) — هم در سایت، هم در پیش‌نمایش ویرایشگر.
			add_filter( 'elementor/widget/render_content', array( $this, 'widget_runtime_css' ), 10, 2 );
		}

		/**
		 * افزودن CSS پالت اختصاصی پیش از خروجی ویجت‌های زرین‌کوچ.
		 *
		 * @param string                $content خروجی ویجت.
		 * @param \Elementor\Widget_Base $widget  ویجت.
		 * @return string
		 */
		public function widget_runtime_css( $content, $widget ) {
			if ( ! ( $widget instanceof ZC_Widget_Base ) ) {
				return $content;
			}
			$css = $widget->zc_runtime_css();
			if ( '' === $css ) {
				return $content;
			}
			return $content . '<style>' . wp_strip_all_tags( $css ) . '</style>';
		}

		/**
		 * نخستین ویجت داخل یک عنصر (بخش/ستون/کانتینر) را پیدا می‌کند.
		 *
		 * @param \Elementor\Element_Base $element عنصر.
		 * @param int                      $depth   عمق جست‌وجو.
		 * @return \Elementor\Widget_Base|null
		 */
		private function first_widget( $element, $depth = 0 ) {
			if ( $depth > 4 || ! method_exists( $element, 'get_children' ) ) {
				return null;
			}
			foreach ( (array) $element->get_children() as $child ) {
				if ( $child instanceof \Elementor\Widget_Base ) {
					return $child;
				}
				$found = $this->first_widget( $child, $depth + 1 );
				if ( $found ) {
					return $found;
				}
			}
			return null;
		}

		/**
		 * افزودن کلاس zc-bgk-{plain|soft|inverse} به بخش سطح اول بر اساس زمینه‌ی ویجت آن.
		 *
		 * @param \Elementor\Element_Base $element بخش یا کانتینر.
		 * @return void
		 */
		public function tag_section_bg( $element ) {
			if ( $element->get_data( 'isInner' ) ) {
				return;
			}
			$widget = $this->first_widget( $element );
			if ( ! $widget ) {
				return;
			}
			// بخشی که پس‌زمینه‌ی اختصاصی دارد با هیچ بخشی ادغام نمی‌شود.
			$bg_type = (string) $element->get_settings( 'background_background' );
			if ( '' !== $bg_type ) {
				return;
			}
			// بخش/ردیفی که خودش padding عمودی دارد (مثل ردیف‌های تیتر + فهرست).
			$pad     = $element->get_settings( 'padding' );
			$own_pad = is_array( $pad ) && ( (float) ( $pad['top'] ?? 0 ) > 0 || (float) ( $pad['bottom'] ?? 0 ) > 0 );
			$name = (string) $widget->get_name();
			if ( 0 !== strpos( $name, 'zc-' ) ) {
				return;
			}
			$tone = (string) $widget->get_settings( 'zc_tone' );
			if ( 'inverse' === $tone ) {
				$key = 'inverse';
			} elseif ( in_array( $name, array( 'zc-faq', 'zc-process' ), true ) || ( 'zc-services' === $name && 'none' !== (string) $widget->get_settings( 'bg_style' ) ) ) {
				$key = 'soft';
			} elseif ( in_array( $name, array( 'zc-hero', 'zc-page-title', 'zc-marquee', 'zc-resume-hero', 'zc-site-header', 'zc-site-footer' ), true ) ) {
				$key = 'edge';
			} else {
				$key = 'plain';
			}
			$classes = array( 'zc-bgk-' . $key );
			if ( $own_pad && 'edge' !== $key ) {
				$classes[] = 'zc-row-pad';
				$classes[] = 'zc-has-sec';
			} elseif ( 'edge' !== $key && $this->has_section_padding( $widget, $name ) ) {
				$classes[] = 'zc-has-sec';
			}
			$element->add_render_attribute( '_wrapper', 'class', $classes );
		}

		/**
		 * آیا ریشه‌ی خروجی ویجت یک .zc-section / .zc-section-tight (دارای padding عمودی) است؟
		 *
		 * @param \Elementor\Widget_Base $widget ویجت.
		 * @param string                  $name   نام ویجت.
		 * @return bool
		 */
		private function has_section_padding( $widget, $name ) {
			if ( 'zc-schemas' === $name ) {
				$boxed = $widget->get_settings( 'boxed' );
				return null === $boxed || 'yes' === (string) $boxed;
			}
			if ( 'zc-trust-badges' === $name ) {
				$wrap = (string) $widget->get_settings( 'wrap' );
				return '' === $wrap || 'section' === $wrap || 'tight' === $wrap;
			}
			return in_array( $name, array( 'zc-about', 'zc-book', 'zc-contact', 'zc-courses', 'zc-cta', 'zc-faq', 'zc-posts', 'zc-pricing', 'zc-process', 'zc-schema', 'zc-services', 'zc-skills', 'zc-stats', 'zc-testimonials', 'zc-text', 'zc-timeline' ), true );
		}

		/**
		 * ثبت دسته‌بندی اختصاصی.
		 *
		 * @param \Elementor\Elements_Manager $elements_manager مدیریت‌کننده عناصر.
		 * @return void
		 */
		public function register_category( $elements_manager ) {
			$elements_manager->add_category(
				'zarincoach',
				array(
					'title' => __( 'زرین‌کوچ — مریم جمالی', 'zarincoach' ),
					'icon'  => 'eicon-star',
				)
			);
		}

		/**
		 * بررسی فعال بودن یک ویجت.
		 *
		 * @param string $slug شناسه ویجت.
		 * @return bool
		 */
		public function is_widget_enabled( $slug ) {
			$enabled = (array) zc_opt( 'elementor_widgets', array() );

			if ( empty( $enabled ) ) {
				return true;
			}

			if ( ! array_key_exists( $slug, $enabled ) ) {
				return true;
			}

			$value = $enabled[ $slug ];
			// '' یعنی کلید پیش از افزوده شدن این ویجت ذخیره شده (نه خاموش کردن عمدی = '0').
			if ( '' === $value || null === $value ) {
				return true;
			}

			if ( is_array( $value ) && isset( $value['enabled'] ) ) {
				$value = $value['enabled'];
			}

			return (bool) $value;
		}

		/**
		 * ثبت ویجت‌ها (المنتور ۳.۵+).
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager مدیریت‌کننده ویجت‌ها.
		 * @return void
		 */
		public function register_widgets( $widgets_manager ) {
			require_once ZC_DIR . '/inc/elementor/class-zc-widget-base.php';
			$woo = class_exists( 'WooCommerce' );
			if ( $woo ) {
				require_once ZC_DIR . '/inc/elementor/class-zc-shop-widget-base.php';
			}

			// ویجت خاموشِ بدون استفاده اصلاً ثبت نمی‌شود؛ ویجت خاموشی که در صفحه‌ای به کار رفته
			// ثبت می‌شود تا آن صفحه خراب نشود و فقط از پنل ویرایشگر پنهان است (ZC_Widget_Base::show_in_panel).
			foreach ( $this->widgets as $slug => $data ) {
				if ( ! empty( $data['woo'] ) && ! $woo ) {
					continue;
				}
				if ( function_exists( 'zc_widget_should_load' ) && ! zc_widget_should_load( $slug, $this->is_widget_enabled( $slug ) ) ) {
					continue;
				}
				$file = ZC_DIR . '/inc/elementor/widgets/' . $data['file'];
				if ( ! file_exists( $file ) ) {
					continue;
				}

				require_once $file;

				if ( class_exists( $data['class'] ) ) {
					$widgets_manager->register( new $data['class']() );
				}
			}
		}

		/**
		 * ثبت ویجت‌ها (نسخه‌های قدیمی المنتور).
		 *
		 * @param \Elementor\Widgets_Manager $widgets_manager مدیریت‌کننده ویجت‌ها.
		 * @return void
		 */
		public function register_widgets_legacy( $widgets_manager ) {
			if ( did_action( 'elementor/widgets/register' ) ) {
				return;
			}
			$this->register_widgets( $widgets_manager );
		}

		/**
		 * بارگذاری استایل‌ها در حالت پیش‌نمایش.
		 *
		 * @return void
		 */
		public function frontend_styles() {
			if ( ! zc_switch( 'elementor_editor_css', true ) ) {
				return;
			}
			wp_enqueue_style( 'zc-main' );
		}

		/**
		 * ثبت جایگاه‌های قالب برای المنتور پرو.
		 *
		 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager مدیریت‌کننده.
		 * @return void
		 */
		public function register_locations( $manager ) {
			if ( ! method_exists( $manager, 'register_location' ) ) {
				return;
			}

			$manager->register_location(
				'header',
				array(
					'label'           => __( 'سربرگ', 'zarincoach' ),
					'multiple'        => false,
					'edit_in_content' => false,
				)
			);

			$manager->register_location(
				'footer',
				array(
					'label'           => __( 'پاورقی', 'zarincoach' ),
					'multiple'        => false,
					'edit_in_content' => false,
				)
			);
		}

		/**
		 * فهرست ویجت‌های موجود (برای استفاده در بخش‌های دیگر).
		 *
		 * @return array<string, string>
		 */
		public function get_widgets_meta() {
			$out = array();
			foreach ( $this->widgets as $slug => $data ) {
				$out[ $slug ] = array(
					'title' => isset( $data['title'] ) ? $data['title'] : $slug,
					'woo'   => ! empty( $data['woo'] ),
				);
			}
			return $out;
		}

		/**
		 * فهرست ویجت‌ها (نامک => نام المنتور).
		 *
		 * @return array<string, string>
		 */
		public function get_widgets_list() {
			$list = array();
			foreach ( $this->widgets as $slug => $data ) {
				$list[ $slug ] = 'zc-' . $slug;
			}
			return $list;
		}
	}

	// راه‌اندازی تنها در صورت فعال بودن المنتور.
	if ( zc_is_elementor_active() ) {
		ZC_Elementor::instance();
	}
endif;
