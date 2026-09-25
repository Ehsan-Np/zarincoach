<?php
/**
 * ویجت سربرگ بخش
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Heading' ) ) :

	/**
	 * ویجت سربرگ بخش.
	 */
	class ZC_Widget_Heading extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-heading';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'سربرگ بخش', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-t-letter';
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

			$this->heading_controls();

			$this->add_control(
				'html_tag',
				array(
					'label'   => __( 'تگ عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'h1'  => 'H1',
						'h2'  => 'H2',
						'h3'  => 'H3',
						'h4'  => 'H4',
						'div' => 'DIV',
					),
					'default' => 'h2',
				)
			);

			$this->add_control(
				'accent_word',
				array(
					'label'       => __( 'واژه‌ی رنگی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'این واژه در عنوان با رنگ گرادیان نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'max_width',
				array(
					'label'      => __( 'حداکثر عرض', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px', '%' ),
					'range'      => array(
						'px' => array( 'min' => 320, 'max' => 1200 ),
					),
					'default'    => array( 'size' => 760, 'unit' => 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .zc-section-head' => 'max-width: {{SIZE}}{{UNIT}};',
					),
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
				'title_color',
				array(
					'label'     => __( 'رنگ عنوان', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'default'   => '',
					'selectors' => array(
						'{{WRAPPER}} .zc-title-lg' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'title_size',
				array(
					'label'      => __( 'اندازه عنوان', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'rem' ),
					'range'      => array(
						'px' => array( 'min' => 18, 'max' => 90 ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .zc-title-lg' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->add_control(
				'spacing',
				array(
					'label'      => __( 'فاصله پایین', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array( 'min' => 0, 'max' => 120 ),
					),
					'default'    => array( 'size' => 0, 'unit' => 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .zc-section-head' => 'margin-bottom: {{SIZE}}{{UNIT}};',
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

			$tag    = isset( $settings['html_tag'] ) ? (string) $settings['html_tag'] : 'h2';
			$accent = isset( $settings['accent_word'] ) ? (string) $settings['accent_word'] : '';
			$title  = isset( $settings['title'] ) ? (string) $settings['title'] : '';
			$align  = isset( $settings['align'] ) ? (string) $settings['align'] : 'center';

			// اعمال واژه‌ی رنگی.
			if ( '' !== $accent && '' !== $title && false !== strpos( $title, $accent ) ) {
				$safe_title = str_replace( esc_html( $accent ), '<span class="zc-gradient-text">' . esc_html( $accent ) . '</span>', esc_html( $title ) );

				$align_class = array(
					'center' => 'text-center mx-auto items-center',
					'right'  => 'text-right items-start ml-auto mr-0',
					'left'   => 'text-left items-end mr-auto ml-0',
				);
				$align_class = isset( $align_class[ $align ] ) ? $align_class[ $align ] : $align_class['center'];
				?>
				<header class="zc-section-head zc-reveal flex max-w-3xl flex-col gap-3 <?php echo esc_attr( $align_class ); ?>">
					<?php if ( ! empty( $settings['eyebrow'] ) ) : ?>
						<span class="zc-eyebrow"><?php echo esc_html( $settings['eyebrow'] ); ?></span>
					<?php endif; ?>

					<<?php echo esc_html( $tag ); ?> class="zc-title-lg zc-text-balance"><?php echo wp_kses_post( $safe_title ); ?></<?php echo esc_html( $tag ); ?>>

					<?php if ( ! empty( $settings['subtitle'] ) ) : ?>
						<p class="zc-lead mt-1"><?php echo esc_html( $settings['subtitle'] ); ?></p>
					<?php endif; ?>
				</header>
				<?php
				return;
			}

			$this->render_heading( '', $tag );
		}
	}
endif;
