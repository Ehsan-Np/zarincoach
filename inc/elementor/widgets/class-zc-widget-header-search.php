<?php
/**
 * ویجت هدر: جستجو
 *
 * دکمه‌ی جستجو با آیکون دلخواه + پنل بازشو با فرم قابل‌ویرایش (جای‌نگهداشت،
 * متن و آیکون دکمه‌ی ارسال). با زنجیره‌ی JS قالب (data-zc-search-toggle) سازگار است.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Search' ) ) :

	/**
	 * ویجت «هدر: جستجو».
	 */
	class ZC_Widget_Header_Search extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-search';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: جستجو', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-search';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'جستجو', 'search', 'فرم' ) );
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
			/* ---------- دکمه ---------- */
			$this->start_controls_section(
				'hsearch_btn_sec',
				array(
					'label' => __( 'دکمه‌ی جستجو', 'zarincoach' ),
				)
			);

			$this->add_control(
				'btn_icon',
				array(
					'label'       => __( 'آیکن دکمه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = ذره‌بین قالب.', 'zarincoach' ),
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

			/* ---------- پنل جستجو ---------- */
			$this->start_controls_section(
				'hsearch_panel_sec',
				array(
					'label' => __( 'پنل جستجو', 'zarincoach' ),
				)
			);

			$this->add_control(
				'placeholder',
				array(
					'label'       => __( 'جای‌نگهداشت فیلد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'جستجو در مطالب…', 'zarincoach' ),
				)
			);

			$this->add_control(
				'submit_text',
				array(
					'label'   => __( 'متن دکمه‌ی ارسال', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'جستجو', 'zarincoach' ),
				)
			);

			$this->add_control(
				'submit_icon',
				array(
					'label'       => __( 'آیکن دکمه‌ی ارسال', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = ذره‌بین قالب.', 'zarincoach' ),
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
			$this->zc_toggles(
				array(
					'btn'     => array( __( 'دکمه‌ی جستجو', 'zarincoach' ), '.zc-hdr-search-btn' ),
					'panel'   => array( __( 'پنل جستجو', 'zarincoach' ), '.zc-hdr-search-panel' ),
					'sicon'   => array( __( 'آیکن دکمه‌ی ارسال', 'zarincoach' ), '.zc-hdr-search-panel .zc-btn .zc-btn-icon, .zc-hdr-search-panel .zc-btn svg' ),
				)
			);

			$this->zc_style(
				'hsearch_style',
				__( 'جستجو', 'zarincoach' ),
				array(
					'btn'   => array( 'icon', '.zc-hdr-search-btn', __( 'دکمه‌ی باز کردن', 'zarincoach' ) ),
					'panel' => array( 'box', '.zc-hdr-search-panel', __( 'پنل', 'zarincoach' ), array( 'gradient' => false ) ),
					'input' => array( 'text', '.zc-hdr-search-panel .zc-input', __( 'فیلد جستجو', 'zarincoach' ), array( 'margin' => false ) ),
					'sbtn'  => array( 'text', '.zc-hdr-search-panel .zc-btn', __( 'دکمه‌ی ارسال', 'zarincoach' ), array( 'margin' => false ) ),
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

			echo '<div class="zc-hdr-search inline-flex items-center">';

			echo '<button type="button" data-zc-search-toggle class="zc-hdr-search-btn zc-btn-icon ' . esc_attr( $hide ) . '" aria-label="' . esc_attr__( 'جستجو', 'zarincoach' ) . '">';
			$this->hdr_icon( isset( $s['btn_icon'] ) ? $s['btn_icon'] : array(), 'search', 'h-[18px] w-[18px]' );
			echo '</button>';

			echo '<div data-zc-search-panel class="zc-hdr-search-panel"><div class="zc-container">';
			echo '<form role="search" method="get" class="zc-search-form flex items-center gap-2" action="' . esc_url( home_url( '/' ) ) . '">';
			echo '<label class="screen-reader-text" for="zc-search-field-' . esc_attr( $this->get_id() ) . '">' . esc_html__( 'جستجو برای:', 'zarincoach' ) . '</label>';
			echo '<input type="search" id="zc-search-field-' . esc_attr( $this->get_id() ) . '" class="zc-input w-full min-w-0 flex-1" placeholder="' . esc_attr( (string) ( isset( $s['placeholder'] ) ? $s['placeholder'] : __( 'جستجو در مطالب…', 'zarincoach' ) ) ) . '" value="' . esc_attr( get_search_query() ) . '" name="s">';
			echo '<button type="submit" class="zc-btn zc-btn-primary zc-btn-sm shrink-0 whitespace-nowrap">';
			$this->hdr_icon( isset( $s['submit_icon'] ) ? $s['submit_icon'] : array(), 'search', 'h-4 w-4 zc-btn-icon' );
			if ( '' !== trim( (string) ( isset( $s['submit_text'] ) ? $s['submit_text'] : '' ) ) ) {
				echo '<span>' . esc_html( (string) $s['submit_text'] ) . '</span>';
			}
			echo '</button></form></div></div>';

			echo '</div>';
		}
	}
endif;
