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
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'sep'  => array( __( 'جداکننده‌ی بین کلمات', 'zarincoach' ), '.zc-marquee-item::after' ),
				)
			);
			$this->zc_style(
				'mq_bar',
				__( 'نوار', 'zarincoach' ),
				array(
					'bar'   => array( 'box', '.zc-marquee-wrap', __( 'قاب نوار', 'zarincoach' ), array( 'gradient' => true ) ),
					'speed' => array( 'size', '.zc-marquee-track', __( 'مدت یک دور حرکت (ثانیه)', 'zarincoach' ), array( 'units' => array( 's' ), 'max' => 200, 'css' => 'animation-duration: {{SIZE}}s;' ) ),
					'dir'   => array( 'select', '.zc-marquee-track', __( 'جهت حرکت', 'zarincoach' ), array( 'options' => array( 'normal' => __( 'عادی', 'zarincoach' ), 'reverse' => __( 'معکوس', 'zarincoach' ) ), 'css' => 'animation-direction: {{VALUE}};' ) ),
					'hover' => array( 'select', '.zc-marquee:hover .zc-marquee-track', __( 'هنگام هاور', 'zarincoach' ), array( 'options' => array( 'paused' => __( 'توقف', 'zarincoach' ), 'running' => __( 'ادامه‌ی حرکت', 'zarincoach' ) ), 'css' => 'animation-play-state: {{VALUE}};' ) ),
					'fade'  => array( 'select', '.zc-marquee', __( 'محو شدن لبه‌ها', 'zarincoach' ), array( 'options' => array( 'none' => __( 'خاموش', 'zarincoach' ) ), 'css' => '-webkit-mask-image: {{VALUE}}; mask-image: {{VALUE}};' ) ),
					'gap'   => array( 'size', '.zc-marquee-track, .zc-marquee-item', __( 'فاصله‌ی کلمات', 'zarincoach' ), array( 'max' => 160, 'css' => 'gap: {{SIZE}}{{UNIT}};' ) ),
				)
			);
			$this->zc_style(
				'mq_words',
				__( 'کلمات و جداکننده', 'zarincoach' ),
				array(
					'word' => array( 'text', '.zc-marquee-item', __( 'کلمات', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'sep'  => array( 'text', '.zc-marquee-item::after', __( 'جداکننده', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);
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
