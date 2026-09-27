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
				'shop_cart',
				array(
					'label'       => __( 'دکمه سبد خرید', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => '',
					'options'     => array(
						''    => __( 'طبق پنل تنظیمات', 'zarincoach' ),
						'yes' => __( 'نمایش', 'zarincoach' ),
						'no'  => __( 'پنهان', 'zarincoach' ),
					),
					'description' => __( 'فقط وقتی ووکامرس فعال است نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'shop_account',
				array(
					'label'       => __( 'دکمه حساب کاربری', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'default'     => '',
					'options'     => array(
						''    => __( 'طبق پنل تنظیمات', 'zarincoach' ),
						'yes' => __( 'نمایش', 'zarincoach' ),
						'no'  => __( 'پنهان', 'zarincoach' ),
					),
					'description' => __( 'فقط وقتی ووکامرس فعال است نمایش داده می‌شود.', 'zarincoach' ),
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
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'topbar'   => array( __( 'نوار بالای سایت', 'zarincoach' ), '[data-zc-topbar]' ),
					'topphone' => array( __( 'تلفن در نوار بالا', 'zarincoach' ), '[data-zc-topbar] a[href^="tel"]' ),
					'topsoc'   => array( __( 'شبکه‌های اجتماعی نوار بالا', 'zarincoach' ), '[data-zc-topbar] .zc-social' ),
					'nav'      => array( __( 'منوی اصلی', 'zarincoach' ), 'nav[aria-label*="منو"]' ),
					'search'   => array( __( 'دکمه‌ی جستجو', 'zarincoach' ), '[data-zc-search-toggle]' ),
					'dark'     => array( __( 'دکمه‌ی حالت تیره', 'zarincoach' ), '[data-zc-theme-toggle]' ),
					'cart'     => array( __( 'سبد خرید', 'zarincoach' ), '.zc-header [class*="cart"]' ),
					'burger'   => array( __( 'دکمه‌ی منوی موبایل', 'zarincoach' ), '[data-zc-drawer-open]' ),
					'progress' => array( __( 'نوار پیشرفت مطالعه', 'zarincoach' ), '.zc-progress' ),
				)
			);
			$this->zc_style(
				'hd_bar',
				__( 'سربرگ: قاب و نوار بالا', 'zarincoach' ),
				array(
					'bar'   => array( 'box', '.zc-header', __( 'نوار سربرگ', 'zarincoach' ), array( 'minh' => true, 'gradient' => false ) ),
					'stuck' => array( 'box', '.zc-header.is-stuck', __( 'نوار هنگام اسکرول', 'zarincoach' ), array( 'gradient' => false, 'heading' => true ) ),
					'inner' => array( 'size', '.zc-header .zc-container > div', __( 'ارتفاع داخلی', 'zarincoach' ), array( 'prop' => 'min-height', 'units' => array( 'px', 'rem' ), 'max' => 200 ) ),
					'top'   => array( 'box', '[data-zc-topbar]', __( 'نوار بالا', 'zarincoach' ), array( 'text' => true, 'gradient' => true ) ),
					'tlink' => array( 'text', '[data-zc-topbar] a', __( 'پیوندهای نوار بالا', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
				)
			);
			$this->zc_style(
				'hd_logo',
				__( 'لوگو', 'zarincoach' ),
				array(
					'img' => array( 'size', '.zc-header a[rel="home"] img, .zc-header .zc-logo img, .zc-drawer a[rel="home"] img', __( 'ارتفاع تصویر لوگو', 'zarincoach' ), array( 'prop' => 'max-height', 'units' => array( 'px', 'rem' ), 'max' => 160 ) ),
					'txt' => array( 'text', '.zc-header a[rel="home"]', __( 'متن لوگو', 'zarincoach' ), array( 'hover' => '.zc-header' ) ),
				)
			);
			$this->zc_style(
				'hd_menu',
				__( 'منو', 'zarincoach' ),
				array(
					'link'   => array( 'text', '.zc-header .zc-nav-link', __( 'آیتم‌های منو', 'zarincoach' ), array( 'hover' => '.zc-header .zc-nav-link' ) ),
					'active' => array( 'color', '.zc-header .zc-nav-link[aria-current="page"]', __( 'رنگ آیتم صفحه‌ی جاری', 'zarincoach' ) ),
					'sub'    => array( 'box', '.zc-submenu', __( 'کادر زیرمنو', 'zarincoach' ), array( 'gradient' => false ) ),
					'subli'  => array( 'text', '.zc-submenu a', __( 'آیتم‌های زیرمنو', 'zarincoach' ), array( 'hover' => '.zc-submenu a', 'padding' => true, 'margin' => false ) ),
					'gap'    => array( 'size', '.zc-header nav ul', __( 'فاصله‌ی آیتم‌های منو', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 60 ) ),
				)
			);
			$this->zc_style(
				'hd_tools',
				__( 'دکمه‌های ابزار و دکمه‌ی اصلی', 'zarincoach' ),
				array(
					'icon' => array( 'icon', '.zc-header .zc-btn-icon', __( 'دکمه‌های گرد', 'zarincoach' ), array( 'hover' => '.zc-header' ) ),
					'cta'  => array( 'button', '.zc-header .zc-btn:not(.zc-btn-icon)', __( 'دکمه‌ی رزرو', 'zarincoach' ) ),
					'soc'  => array( 'color', '[data-zc-topbar] .zc-social a, [data-zc-topbar] div.flex a[target="_blank"]', __( 'رنگ آیکن‌های اجتماعی', 'zarincoach' ), array( 'prop' => 'color' ) ),
				)
			);
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
			$d = zc_header_args();
			foreach ( array( 'shop_cart' => 'show_cart', 'shop_account' => 'show_account' ) as $control => $arg ) {
				$mode         = isset( $s[ $control ] ) ? (string) $s[ $control ] : '';
				$args[ $arg ] = '' === $mode ? ! empty( $d[ $arg ] ) : 'yes' === $mode;
			}

			get_template_part( 'template-parts/site', 'header', $args );
		}
	}
endif;
