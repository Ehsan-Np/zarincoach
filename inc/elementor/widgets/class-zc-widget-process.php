<?php
/**
 * ویجت مسیر همراهی (Timeline)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Process' ) ) :

	/**
	 * ویجت مسیر همراهی.
	 */
	class ZC_Widget_Process extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-process';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'مسیر همراهی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-time-line';
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

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'step_title',
				array(
					'label'   => __( 'عنوان مرحله', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'step_desc',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$repeater->add_control(
				'step_url',
				array(
					'label'   => __( 'لینک (اختیاری)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'steps',
				array(
					'label'       => __( 'مراحل', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ step_title }}}',
				)
			);

			$this->add_control(
				'persian_numbers',
				array(
					'label'        => __( 'اعداد فارسی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
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

			$steps    = ! empty( $settings['steps'] ) ? $settings['steps'] : array();
			$persian  = ! isset( $settings['persian_numbers'] ) || 'yes' === (string) $settings['persian_numbers'];

			$delay = 0;
			$index = 0;
			?>
			<section class="zc-process zc-section relative overflow-hidden bg-surface2/50">
				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php if ( ! empty( $steps ) ) : ?>
						<ol class="zc-timeline zc-after-head mx-auto grid max-w-3xl gap-6 lg:gap-8">
							<?php foreach ( $steps as $step ) : ?>
								<?php
								$title = isset( $step['step_title'] ) ? trim( (string) $step['step_title'] ) : '';
								$desc  = isset( $step['step_desc'] ) ? trim( (string) $step['step_desc'] ) : '';
								$url   = isset( $step['step_url']['url'] ) ? (string) $step['step_url']['url'] : '';

								if ( '' === $title && '' === $desc ) {
									continue;
								}

								$index++;
								$number = $persian ? zc_digits_to_persian( (string) $index ) : (string) $index;
								?>
								<li class="zc-reveal relative grid grid-cols-[auto_1fr] gap-5 md:gap-8" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
									<span class="zc-tl-dot"><span class="zc-arabic-num"><?php echo esc_html( $number ); ?></span></span>

									<div class="pb-2">
										<h3 class="text-[1.2rem] font-bold text-secondary">
											<?php if ( '' !== $url ) : ?>
												<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $title ); ?>
											<?php endif; ?>
										</h3>

										<?php if ( '' !== $desc ) : ?>
											<p class="zc-lead mt-2 !text-[0.95rem]"><?php echo esc_html( $desc ); ?></p>
										<?php endif; ?>
									</div>
								</li>
								<?php
								$delay += 90;
							endforeach;
							?>
						</ol>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
