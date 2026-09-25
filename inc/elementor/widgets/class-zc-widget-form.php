<?php
/**
 * ویجت فرم رزرو / تماس
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Form' ) ) :

	/**
	 * ویجت فرم.
	 */
	class ZC_Widget_Form extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-form';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'فرم رزرو/تماس', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-form-horizontal';
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array(
					'label' => __( 'تنظیمات فرم', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'form_source',
				array(
					'label'   => __( 'منبع فرم', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'internal'   => __( 'فرم داخلی قالب', 'zarincoach' ),
						'shortcode'  => __( 'شورت‌کد دلخواه', 'zarincoach' ),
					),
					'default' => 'internal',
				)
			);

			$this->add_control(
				'shortcode',
				array(
					'label'       => __( 'شورت‌کد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => '[contact-form-7 id="1"]',
					'description' => __( 'برای استفاده از افزونه‌هایی مانند Contact Form 7 یا WPForms.', 'zarincoach' ),
					'condition'   => array( 'form_source' => 'shortcode' ),
				)
			);

			$this->add_control(
				'form_title',
				array(
					'label'     => __( 'عنوان فرم', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'درخواست رزرو جلسه', 'zarincoach' ),
					'condition' => array( 'form_source' => 'internal' ),
				)
			);

			$this->add_control(
				'form_subtitle',
				array(
					'label'     => __( 'زیرعنوان', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXTAREA,
					'rows'      => 2,
					'default'   => __( 'فرم را پر کن؛ در کمتر از ۲۴ ساعت کاری با تو تماس می‌گیرم.', 'zarincoach' ),
					'condition' => array( 'form_source' => 'internal' ),
				)
			);

			$this->add_control(
				'button_text',
				array(
					'label'     => __( 'متن دکمه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'ارسال درخواست', 'zarincoach' ),
					'condition' => array( 'form_source' => 'internal' ),
				)
			);

			$this->add_control(
				'show_subject',
				array(
					'label'        => __( 'نمایش فیلد موضوع', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => '1',
					'default'      => '',
					'condition'    => array( 'form_source' => 'internal' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				array(
					'label' => __( 'استایل', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'boxed',
				array(
					'label'        => __( 'قاب‌دار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'bg_color',
				array(
					'label'     => __( 'رنگ پس‌زمینه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'condition' => array( 'boxed' => 'yes' ),
					'selectors' => array(
						'{{WRAPPER}} .zc-form-box' => 'background-color: {{VALUE}};',
					),
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
			$settings = $this->get_settings_for_display();

			$source   = isset( $settings['form_source'] ) ? (string) $settings['form_source'] : 'internal';
			$boxed    = ! isset( $settings['boxed'] ) || 'yes' === (string) $settings['boxed'];
			$box_open = $boxed ? '<div class="zc-form-box rounded-[var(--zc-radius)] border border-line bg-base p-6 sm:p-8">' : '<div>';
			?>
			<div class="zc-form-widget">
				<?php echo $box_open; // phpcs:ignore ?>

				<?php if ( 'shortcode' === $source && '' !== (string) $settings['shortcode'] ) : ?>
					<?php echo do_shortcode( (string) $settings['shortcode'] ); ?>
				<?php else : ?>
					<?php
					$atts = array(
						'title'        => isset( $settings['form_title'] ) ? (string) $settings['form_title'] : '',
						'subtitle'     => isset( $settings['form_subtitle'] ) ? (string) $settings['form_subtitle'] : '',
						'button'       => isset( $settings['button_text'] ) ? (string) $settings['button_text'] : '',
						'show_subject' => isset( $settings['show_subject'] ) ? (string) $settings['show_subject'] : '0',
					);

					echo do_shortcode( '[zc_contact ' . $this->build_atts( $atts ) . ']' );
					?>
				<?php endif; ?>

				</div>
			</div>
			<?php
		}

		/**
		 * ساخت رشته‌ی ویژگی‌های شورت‌کد.
		 *
		 * @param array $atts ویژگی‌ها.
		 * @return string
		 */
		protected function build_atts( $atts ) {
			$parts = array();

			foreach ( $atts as $key => $value ) {
				if ( '' === (string) $value ) {
					continue;
				}
				$parts[] = $key . '="' . esc_attr( (string) $value ) . '"';
			}

			return implode( ' ', $parts );
		}
	}
endif;
