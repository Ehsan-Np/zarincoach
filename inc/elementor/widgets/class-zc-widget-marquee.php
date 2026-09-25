<?php
/**
 * ویجت نوار کلمات لغزنده
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Marquee' ) ) :

	/**
	 * ویجت Marquee.
	 */
	class ZC_Widget_Marquee extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-marquee';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'نوار کلمات', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-carousel';
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
					'label' => __( 'محتوا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'word',
				array(
					'label'   => __( 'کلمه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'words',
				array(
					'label'       => __( 'کلمات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ word }}}',
				)
			);

			$this->add_control(
				'speed',
				array(
					'label'      => __( 'سرعت حرکت (ثانیه)', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 's' ),
					'range'      => array(
						's' => array( 'min' => 10, 'max' => 120, 'step' => 2 ),
					),
					'default'    => array( 'size' => 38, 'unit' => 's' ),
					'selectors'  => array(
						'{{WRAPPER}} .zc-marquee-track' => 'animation-duration: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'separator',
				array(
					'label'   => __( 'جداکننده', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '✳',
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
				'text_color',
				array(
					'label'     => __( 'رنگ متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .zc-marquee-item' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'bg_color',
				array(
					'label'     => __( 'رنگ پس‌زمینه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .zc-marquee-wrap' => 'background-color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'font_size',
				array(
					'label'      => __( 'اندازه متن', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'rem' ),
					'range'      => array(
						'px' => array( 'min' => 12, 'max' => 64 ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .zc-marquee-item' => 'font-size: {{SIZE}}{{UNIT}};',
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

			$words = array();

			if ( ! empty( $settings['words'] ) ) {
				foreach ( $settings['words'] as $item ) {
					if ( ! empty( $item['word'] ) ) {
						$words[] = (string) $item['word'];
					}
				}
			}

			if ( empty( $words ) ) {
				$words = array_values( array_filter( (array) zc_opt( 'home_marquee_words', array() ) ) );
			}

			if ( empty( $words ) ) {
				return;
			}

			$separator = isset( $settings['separator'] ) ? (string) $settings['separator'] : '✳';
			?>
			<section class="zc-marquee-wrap relative border-y border-line bg-surface2/60 py-5" aria-hidden="true">
				<div class="zc-marquee" style="--zc-marquee-sep:'<?php echo esc_attr( $separator ); ?>'">
					<div class="zc-marquee-track">
						<?php foreach ( $words as $word ) : ?>
							<span class="zc-marquee-item"><?php echo esc_html( $word ); ?></span>
						<?php endforeach; ?>
					</div>
					<div class="zc-marquee-track">
						<?php foreach ( $words as $word ) : ?>
							<span class="zc-marquee-item"><?php echo esc_html( $word ); ?></span>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
