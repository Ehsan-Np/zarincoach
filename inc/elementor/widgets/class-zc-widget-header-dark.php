<?php
/**
 * ویجت هدر: کلید حالت تاریک
 *
 * دکمه‌ی تعویض حالت روشن/تاریک با آیکن‌های قابل‌تغییر برای هر دو حالت
 * (ماه برای روشن، خورشید برای تاریک) و استایل کامل کیت زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Dark' ) ) :

	/**
	 * ویجت «هدر: کلید حالت تاریک».
	 */
	class ZC_Widget_Header_Dark extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-dark';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: کلید حالت تاریک', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-dark-mode';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'حالت تاریک', 'شب', 'dark', 'تغییر پوسته' ) );
		}

		/**
		 * آیکون انتخابی با جایگزین داخلی.
		 *
		 * @param array|string $icon     آیکون.
		 * @param string       $fallback نام آیکون داخلی.
		 * @param string       $class    کلاس اندازه.
		 * @return void
		 */
		private function hdr_icon( $icon, $fallback, $class ) {
			if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				return;
			}
			zc_icon( is_string( $icon ) && '' !== $icon ? $icon : $fallback, $class );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hdark_sec',
				array(
					'label' => __( 'کلید حالت تاریک', 'zarincoach' ),
				)
			);

			$this->add_control(
				'icon_moon',
				array(
					'label'       => __( 'آیکن حالت روشن (ماه)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = ماه قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'icon_sun',
				array(
					'label'       => __( 'آیکن حالت تاریک (خورشید)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = خورشید قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'hide_mobile',
				array(
					'label'        => __( 'پنهان در موبایل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
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
				'hdark_style',
				__( 'دکمه', 'zarincoach' ),
				array(
					'btn' => array( 'icon', '.zc-hdr-dark-btn', __( 'دکمه‌ی حالت تاریک', 'zarincoach' ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s    = $this->get_settings_for_display();
			$hide = $this->is_on( $s, 'hide_mobile' ) ? 'hidden sm:grid' : 'grid';

			echo '<button type="button" data-zc-theme-toggle class="zc-hdr-dark-btn zc-btn-icon ' . esc_attr( $hide ) . '" aria-pressed="false" aria-label="' . esc_attr__( 'تغییر حالت تاریک', 'zarincoach' ) . '">';
			echo '<span class="block dark:hidden">';
			$this->hdr_icon( isset( $s['icon_moon'] ) ? $s['icon_moon'] : array(), 'moon', 'h-[18px] w-[18px]' );
			echo '</span><span class="hidden dark:block">';
			$this->hdr_icon( isset( $s['icon_sun'] ) ? $s['icon_sun'] : array(), 'sun', 'h-[18px] w-[18px]' );
			echo '</span></button>';
		}
	}
endif;
