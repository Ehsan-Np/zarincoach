<?php
/**
 * ویجت هدر: دکمه‌ی منوی موبایل (همبرگر)
 *
 * دکمه‌ی همبرگری با انیمیشن تبدیل به ضربدر (کلاس استاندارد zc-hamburger)؛
 * جفت ویجت «هدر: منوی کشویی موبایل» را باز می‌کند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Burger' ) ) :

	/**
	 * ویجت «هدر: دکمه‌ی منوی موبایل».
	 */
	class ZC_Widget_Header_Burger extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-burger';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: دکمه‌ی منوی موبایل', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-menu-bar';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'همبرگر', 'منوی موبایل', 'hamburger', 'burger' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hburger_sec',
				array(
					'label' => __( 'دکمه‌ی منوی موبایل', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show',
				array(
					'label'        => __( 'نمایش دکمه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'desktop',
				array(
					'label'        => __( 'نمایش در دسکتاپ هم', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'به‌صورت پیش‌فرض دکمه فقط در موبایل و تبلت دیده می‌شود.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * استایل (کیت زرین‌کوچ).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_style(
				'hburger_style',
				__( 'دکمه', 'zarincoach' ),
				array(
					'bars' => array( 'color', '.zc-hamburger span', __( 'رنگ خطوط', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'btn'  => array( 'icon', '.zc-hamburger', __( 'دکمه', 'zarincoach' ), array( 'box' => false, 'hover' => '' ) ),
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
			if ( ! $this->is_on( $s, 'show' ) ) {
				return;
			}

			$cls = $this->is_on( $s, 'desktop' ) ? 'zc-hamburger' : 'zc-hamburger lg:hidden';
			echo '<div class="zc-hdr-burger inline-flex items-center">';
			echo '<button type="button" data-zc-drawer-open class="' . esc_attr( $cls ) . '" aria-controls="zc-drawer" aria-expanded="false" aria-label="' . esc_attr__( 'باز کردن منو', 'zarincoach' ) . '"><span></span><span></span><span></span></button>';
			echo '</div>';
		}
	}
endif;
