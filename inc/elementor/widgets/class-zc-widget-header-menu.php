<?php
/**
 * ویجت هدر: منوی اصلی
 *
 * فهرست دلخواه با عمق ۱ تا ۲، چینش، فاصله و استایل کامل آیتم‌ها و زیرمنوها
 * (کلاس‌های استاندارد قالب: zc-nav-link و zc-submenu).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Menu' ) ) :

	/**
	 * ویجت «هدر: منوی اصلی».
	 */
	class ZC_Widget_Header_Menu extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-menu';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: منوی اصلی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-nav-menu';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'منو', 'فهرست', 'زیرمنو', 'menu', 'nav' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hmenu_sec',
				array(
					'label' => __( 'منوی اصلی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'       => __( 'فهرست', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( '«جایگاه پیش‌فرض» = منوی جایگاه «منوی اصلی» در «نمایش ← فهرست‌ها».', 'zarincoach' ),
				)
			);

			$this->add_control(
				'depth',
				array(
					'label'   => __( 'عمق منو', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'1' => __( 'یک سطح (بدون زیرمنو)', 'zarincoach' ),
						'2' => __( 'دو سطح (با زیرمنو)', 'zarincoach' ),
					),
					'default' => '2',
				)
			);

			$this->add_control(
				'align',
				array(
					'label'     => __( 'چینش منو', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array( 'title' => __( 'راست', 'zarincoach' ), 'icon' => 'eicon-text-align-right' ),
						'center' => array( 'title' => __( 'وسط', 'zarincoach' ), 'icon' => 'eicon-text-align-center' ),
						'end'    => array( 'title' => __( 'چپ', 'zarincoach' ), 'icon' => 'eicon-text-align-left' ),
					),
					'default'   => 'center',
					'selectors_dictionary' => array(
						'start'  => 'justify-content: flex-start;',
						'center' => 'justify-content: center;',
						'end'    => 'justify-content: flex-end;',
					),
					'selectors' => array(
						'{{WRAPPER}} .zc-hdr-menu > ul' => '{{VALUE}};',
					),
				)
			);

			$this->add_control(
				'hide_mobile',
				array(
					'label'        => __( 'پنهان در موبایل (جایگزینی با منوی کشویی)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'در موبایل معمولاً منوی اصلی پنهان و منوی کشویی هدر نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * کلیدهای نمایش و استایل (کیت زرین‌کوچ).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_style(
				'hmenu_items',
				__( 'آیتم‌ها', 'zarincoach' ),
				array(
					'item' => array( 'text', '.zc-hdr-menu .zc-nav-link', __( 'متن آیتم', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'gap'  => array( 'size', '.zc-hdr-menu > ul', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), array( 'max' => 60, 'css' => 'gap: {{SIZE}}{{UNIT}};' ) ),
				)
			);

			$this->zc_style(
				'hmenu_sub',
				__( 'زیرمنو', 'zarincoach' ),
				array(
					'box'  => array( 'box', '.zc-hdr-menu .zc-submenu', __( 'جعبه‌ی زیرمنو', 'zarincoach' ), array( 'gradient' => false ) ),
					'link' => array( 'text', '.zc-hdr-menu .zc-submenu a', __( 'متن زیرمنو', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s     = $this->get_settings_for_display();
			$menu  = isset( $s['menu'] ) ? absint( $s['menu'] ) : 0;
			$depth = ( '1' === (string) ( isset( $s['depth'] ) ? $s['depth'] : '2' ) ) ? 1 : 2;
			$class = 'zc-hdr-menu hidden items-center lg:flex' . ( $this->is_on( $s, 'hide_mobile' ) ? '' : ' !flex' );

			$args = array(
				'container'      => false,
				'menu_class'     => 'flex items-center gap-1',
				'fallback_cb'    => 'zc_fallback_menu',
				'depth'          => $depth,
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				// برای برخورداری آیتم‌ها از کلاس‌های استایل قالب، جایگاه همیشه ست می‌شود.
				'theme_location' => 'zc-primary',
				'echo'           => true,
			);
			if ( $menu > 0 && wp_get_nav_menu_object( $menu ) ) {
				$args['menu'] = $menu;
			}

			echo '<nav class="' . esc_attr( $class ) . '" aria-label="' . esc_attr__( 'منوی اصلی', 'zarincoach' ) . '">';
			wp_nav_menu( $args );
			echo '</nav>';
		}
	}
endif;
