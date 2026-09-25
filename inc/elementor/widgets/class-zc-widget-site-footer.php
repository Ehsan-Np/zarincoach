<?php
/**
 * ویجت پاورقی سایت (فوتر)
 *
 * معرفی و شبکه‌های اجتماعی، ستون خدمات، تازه‌ترین نوشته‌ها، راه‌های ارتباط، منوی پاورقی و کپی‌رایت.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Site_Footer' ) ) :

	/**
	 * ویجت پاورقی سایت.
	 */
	class ZC_Widget_Site_Footer extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-site-footer';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پاورقی سایت (فوتر)', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-footer';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int, string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'فوتر', 'پاورقی' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			/* ---------- ستون معرفی ---------- */
			$this->start_controls_section(
				'about_section',
				array(
					'label' => __( 'ستون معرفی', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			// پاورقی همیشه سرمه‌ای است؛ کنترل تُن عمومی لازم نیست.
			$this->add_control(
				'zc_tone',
				array(
					'type'    => \Elementor\Controls_Manager::HIDDEN,
					'default' => '',
				)
			);

			$this->add_control(
				'about',
				array(
					'label'       => __( 'متن معرفی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 4,
					'placeholder' => __( 'خالی = متن پنل تنظیمات قالب', 'zarincoach' ),
					'description' => __( 'لوگو از «سفارشی‌سازی ← هویت سایت» و شبکه‌های اجتماعی از «پنل قالب ← تماس و شبکه‌ها» خوانده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_socials',
				array(
					'label'        => __( 'نمایش شبکه‌های اجتماعی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'glow',
				array(
					'label'        => __( 'هاله‌های رنگی پس‌زمینه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->end_controls_section();

			/* ---------- ستون‌های میانی ---------- */
			$this->start_controls_section(
				'columns_section',
				array(
					'label' => __( 'ستون‌های خدمات و نوشته‌ها', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'services_title',
				array(
					'label'       => __( 'عنوان ستون خدمات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'خدمات', 'zarincoach' ),
					'description' => __( 'برای حذف ستون، عنوان را خالی بگذارید.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'services_count',
				array(
					'label'   => __( 'تعداد خدمات', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 12,
					'default' => 5,
				)
			);

			$this->add_control(
				'posts_title',
				array(
					'label'       => __( 'عنوان ستون نوشته‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'تازه‌ترین نوشته‌ها', 'zarincoach' ),
					'description' => __( 'برای حذف ستون، عنوان را خالی بگذارید.', 'zarincoach' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'posts_count',
				array(
					'label'   => __( 'تعداد نوشته‌ها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 12,
					'default' => 4,
				)
			);

			$this->add_control(
				'use_widgets',
				array(
					'label'        => __( 'استفاده از ابزارک‌های پاورقی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'در صورت فعال بودن و وجود ابزارک در ناحیه‌های «پاورقی ۱ تا ۳»، به‌جای ستون‌های بالا نمایش داده می‌شوند.', 'zarincoach' ),
					'separator'    => 'before',
				)
			);

			$this->end_controls_section();

			/* ---------- تماس و کپی‌رایت ---------- */
			$this->start_controls_section(
				'bottom_section',
				array(
					'label' => __( 'تماس، منو و کپی‌رایت', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'contact_title',
				array(
					'label'   => __( 'عنوان ستون تماس', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'راه‌های ارتباط', 'zarincoach' ),
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'   => __( 'منوی پاورقی', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $this->menu_options(),
					'default' => '0',
				)
			);

			$this->add_control(
				'copyright',
				array(
					'label'       => __( 'متن کپی‌رایت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __( 'خالی = متن پنل تنظیمات قالب', 'zarincoach' ),
				)
			);

			$this->add_control(
				'credit',
				array(
					'label'        => __( 'نمایش اعتبار طراح', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => zc_switch( 'footer_credit', true ) ? 'yes' : '',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'legal_section',
				array(
					'label' => __( 'اعتماد، مجوزها و قوانین', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'show_trust',
				array(
					'label'        => __( 'نوار اعتماد (دامنه رسمی، مجوز، نمادها)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'متن‌ها از «پنل قالب ← تماس و پاورقی ← اطلاعات حقوقی» خوانده می‌شوند.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'trust_items',
				array(
					'label'       => __( 'نمادهای اعتماد (۱ تا ۶)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $this->trust_repeater()->get_controls(),
					'default'     => zc_trust_default_items(),
					'title_field' => '{{{ label ? label : type }}}',
					'max_items'   => 6,
					'condition'   => array( 'show_trust' => 'yes' ),
				)
			);

			$this->add_control(
				'trust_fallback',
				array(
					'label'        => __( 'طرح پیش‌فرض برای کدهای خالی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'کدهای خالی از «تنظیمات قالب ← اطلاعات حقوقی» خوانده می‌شوند؛ اگر آنجا هم خالی باشند، کاشی خنثی نمایش داده می‌شود.', 'zarincoach' ),
					'condition'    => array( 'show_trust' => 'yes' ),
				)
			);

			$this->add_control(
				'show_license',
				array(
					'label'        => __( 'نمایش شماره پروانه و کد نظام', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'show_trust' => 'yes' ),
				)
			);

			$this->add_control(
				'show_emergency',
				array(
					'label'        => __( 'نمایش اطلاعیه موارد اضطراری', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'legal_menu',
				array(
					'label'       => __( 'منوی قوانین و مقررات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( '«پیش‌فرض» = منوی جایگاه «منوی قوانین (پاورقی)».', 'zarincoach' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s = $this->get_settings_for_display();

			$args = array(
				'about'          => $this->value( 'about', 'footer_about', '' ),
				'show_socials'   => $this->is_on( $s, 'show_socials' ),
				'glow'           => $this->is_on( $s, 'glow' ),
				'services_title' => isset( $s['services_title'] ) ? (string) $s['services_title'] : '',
				'services_count' => isset( $s['services_count'] ) ? (int) $s['services_count'] : 5,
				'posts_title'    => isset( $s['posts_title'] ) ? (string) $s['posts_title'] : '',
				'posts_count'    => isset( $s['posts_count'] ) ? (int) $s['posts_count'] : 4,
				'use_widgets'    => $this->is_on( $s, 'use_widgets' ),
				'contact_title'  => isset( $s['contact_title'] ) ? (string) $s['contact_title'] : '',
				'menu'           => isset( $s['menu'] ) ? absint( $s['menu'] ) : 0,
				'copyright'      => $this->value( 'copyright', 'footer_copy', __( 'کلیه حقوق این وب‌سایت محفوظ است.', 'zarincoach' ) ),
				'credit'         => $this->is_on( $s, 'credit' ),
				'margin'         => false,
				'show_trust'     => $this->is_on( $s, 'show_trust' ),
				'show_emergency' => $this->is_on( $s, 'show_emergency' ),
				'trust_items'    => isset( $s['trust_items'] ) && is_array( $s['trust_items'] ) ? $this->trust_items_from( $s['trust_items'] ) : zc_trust_default_items(),
				'trust_fallback' => ! isset( $s['trust_fallback'] ) || $this->is_on( $s, 'trust_fallback' ),
				'show_license'   => ! isset( $s['show_license'] ) || $this->is_on( $s, 'show_license' ),
				'legal_menu'     => isset( $s['legal_menu'] ) ? absint( $s['legal_menu'] ) : 0,
			);

			get_template_part( 'template-parts/site', 'footer', $args );
		}
	}
endif;
