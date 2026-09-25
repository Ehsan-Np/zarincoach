<?php
/**
 * ویجت سربرگ سایت (هدر)
 *
 * نوار بالا، لوگو، منوی اصلی، جستجو، حالت تیره، دکمه اقدام و منوی کشویی موبایل.
 * در قالب «سربرگ سایت» (کتابخانه المنتور) استفاده و از «پنل قالب ← سربرگ» به کل سایت اعمال می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Site_Header' ) ) :

	/**
	 * ویجت سربرگ سایت.
	 */
	class ZC_Widget_Site_Header extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-site-header';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'سربرگ سایت (هدر)', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-header';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int, string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'menu', 'هدر', 'منو', 'سربرگ' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$d = zc_header_args();

			/* ---------- چیدمان و منو ---------- */
			$this->start_controls_section(
				'layout_section',
				array(
					'label' => __( 'چیدمان و منو', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'classic'  => __( 'کلاسیک (لوگو، منو، ابزارها)', 'zarincoach' ),
						'centered' => __( 'وسط‌چین (منو در ردیف دوم)', 'zarincoach' ),
						'minimal'  => __( 'مینیمال (منوی کشویی در همه اندازه‌ها)', 'zarincoach' ),
					),
					'default' => in_array( $d['layout'], array( 'classic', 'centered', 'minimal' ), true ) ? $d['layout'] : 'classic',
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'       => __( 'منوی اصلی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( 'لوگو و نام سایت از «سفارشی‌سازی ← هویت سایت» خوانده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'mobile_menu',
				array(
					'label'   => __( 'منوی موبایل (کشویی)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $this->menu_options(),
					'default' => '0',
				)
			);

			$this->end_controls_section();

			/* ---------- نوار بالا ---------- */
			$this->start_controls_section(
				'topbar_section',
				array(
					'label' => __( 'نوار اطلاع‌رسانی بالا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'topbar',
				array(
					'label'        => __( 'نمایش نوار بالا', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $d['topbar'] ? 'yes' : '',
				)
			);

			$this->add_control(
				'topbar_text',
				array(
					'label'       => __( 'متن اطلاعیه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
					'placeholder' => __( 'خالی = متن پنل تنظیمات قالب', 'zarincoach' ),
					'condition'   => array( 'topbar' => 'yes' ),
				)
			);

			$this->add_control(
				'topbar_link',
				array(
					'label'       => __( 'پیوند اطلاعیه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'خالی = پیوند پنل تنظیمات', 'zarincoach' ),
					'condition'   => array( 'topbar' => 'yes' ),
				)
			);

			$this->add_control(
				'show_phone',
				array(
					'label'        => __( 'نمایش تلفن و ایمیل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $d['show_phone'] ? 'yes' : '',
					'condition'    => array( 'topbar' => 'yes' ),
				)
			);

			$this->add_control(
				'show_social',
				array(
					'label'        => __( 'نمایش شبکه‌های اجتماعی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $d['show_social'] ? 'yes' : '',
					'condition'    => array( 'topbar' => 'yes' ),
				)
			);

			$this->end_controls_section();

			/* ---------- ابزارها و دکمه ---------- */
			$this->start_controls_section(
				'tools_section',
				array(
					'label' => __( 'ابزارها و دکمه اقدام', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'show_search',
				array(
					'label'        => __( 'دکمه جستجو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => $d['show_search'] ? 'yes' : '',
				)
			);

			$this->add_control(
				'show_dark',
				array(
					'label'        => __( 'دکمه حالت تیره', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'در صورت غیرفعال بودن حالت تیره در پنل قالب، نمایش داده نمی‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'cta_text',
				array(
					'label'       => __( 'متن دکمه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = متن پنل تنظیمات', 'zarincoach' ),
				)
			);

			$this->add_control(
				'cta_url',
				array(
					'label'       => __( 'پیوند دکمه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'خالی = پیوند پنل تنظیمات', 'zarincoach' ),
				)
			);

			$this->add_control(
				'hide_cta',
				array(
					'label'        => __( 'پنهان کردن دکمه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
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
				'layout'      => isset( $s['layout'] ) ? (string) $s['layout'] : 'classic',
				'menu'        => isset( $s['menu'] ) ? absint( $s['menu'] ) : 0,
				'mobile_menu' => isset( $s['mobile_menu'] ) ? absint( $s['mobile_menu'] ) : 0,
				'topbar'      => $this->is_on( $s, 'topbar' ),
				'topbar_text' => $this->value( 'topbar_text', 'header_topbar_text', '' ),
				'topbar_link' => $this->value( 'topbar_link', 'header_topbar_link', '#booking' ),
				'show_phone'  => $this->is_on( $s, 'show_phone' ),
				'show_social' => $this->is_on( $s, 'show_social' ),
				'show_search' => $this->is_on( $s, 'show_search' ),
				'show_dark'   => $this->is_on( $s, 'show_dark' ) && 'off' !== (string) zc_opt( 'general_dark_mode', 'toggle' ),
				'cta_text'    => $this->is_on( $s, 'hide_cta' ) ? '' : $this->value( 'cta_text', 'header_cta_text', __( 'رزرو جلسه آشنایی', 'zarincoach' ) ),
				'cta_url'     => $this->value( 'cta_url', 'header_cta_url', '#booking' ),
			);

			get_template_part( 'template-parts/site', 'header', $args );
		}
	}
endif;
