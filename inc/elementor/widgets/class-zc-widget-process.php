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

			$repeater->add_control( 'step_icon', array( 'label' => __( 'آیکون مرحله (اختیاری)', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::ICONS, 'default' => array( 'value' => '', 'library' => '' ), 'description' => __( 'خالی = شماره‌ی مرحله', 'zarincoach' ) ) );
			$repeater->add_control( 'on', array( 'label' => __( 'نمایش این مرحله', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );

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
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'line' => array( __( 'خط عمودی', 'zarincoach' ), '.zc-process-list::before' ),
					'dotic' => array( __( 'آیکن داخل دایره', 'zarincoach' ), '.zc-process-dot-ic' ),
					'ring' => array( __( 'حلقه‌ی متحرک دور شماره', 'zarincoach' ), '.zc-process-dot::after' ),
					'desc' => array( __( 'توضیح مراحل', 'zarincoach' ), '.zc-process-desc' ),
				)
			);
			$this->zc_style(
				'proc_list',
				__( 'فهرست مراحل', 'zarincoach' ),
				array(
					'list' => array( 'size', '.zc-process-list', __( 'عرض فهرست', 'zarincoach' ), array( 'prop' => 'max-width', 'units' => array( 'px', '%' ), 'max' => 1400 ) ),
					'gap'  => array( 'size', '.zc-process-list', __( 'فاصله‌ی مراحل', 'zarincoach' ), array( 'prop' => 'row-gap', 'max' => 120 ) ),
					'step' => array( 'box', '.zc-process-body', __( 'قاب متن هر مرحله', 'zarincoach' ), array( 'hover' => true ) ),
					'line' => array( 'color', '.zc-process-list::before', __( 'رنگ خط عمودی', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'dotic' => array( 'icon', '.zc-process-dot-ic', '' ),
				)
			);
			$this->zc_style(
				'proc_dot',
				__( 'دایره‌ی شماره', 'zarincoach' ),
				array(
					'dot'  => array( 'icon', '.zc-process-dot', '', array( 'hover' => '.zc-process-step' ) ),
					'num'  => array( 'text', '.zc-process-dot', __( 'عدد', 'zarincoach' ), array( 'margin' => false, 'heading' => true ) ),
					'ring' => array( 'color', '.zc-process-dot::after', __( 'رنگ حلقه', 'zarincoach' ), array( 'prop' => 'border-color' ) ),
				)
			);
			$this->zc_style(
				'proc_text',
				__( 'عنوان و توضیح', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-process-title, .zc-process-title a', __( 'عنوان', 'zarincoach' ), array( 'hover' => true ) ),
					'desc'  => array( 'text', '.zc-process-desc', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
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

			$steps    = ! empty( $settings['steps'] ) ? $settings['steps'] : array();
			$persian  = ! isset( $settings['persian_numbers'] ) || 'yes' === (string) $settings['persian_numbers'];

			$delay = 0;
			$index = 0;
			?>
			<section class="zc-process zc-section relative overflow-hidden bg-surface2/50">
				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php if ( ! empty( $steps ) ) : ?>
						<ol class="zc-process-list zc-timeline zc-after-head mx-auto grid max-w-3xl gap-6 lg:gap-8">
							<?php foreach ( $steps as $step ) : ?>
								<?php
								$title = isset( $step['step_title'] ) ? trim( (string) $step['step_title'] ) : '';
								$desc  = isset( $step['step_desc'] ) ? trim( (string) $step['step_desc'] ) : '';
								$url   = isset( $step['step_url']['url'] ) ? (string) $step['step_url']['url'] : '';

								if ( isset( $step['on'] ) && 'yes' !== (string) $step['on'] ) { continue; }
								if ( '' === $title && '' === $desc ) {
									continue;
								}

								$index++;
								$zicon = ( isset( $step['step_icon']['value'] ) && '' !== (string) $step['step_icon']['value'] ) ? $step['step_icon'] : array();
								$number = $persian ? zc_digits_to_persian( (string) $index ) : (string) $index;
								?>
								<li class="zc-process-step zc-reveal relative grid grid-cols-[auto_1fr] gap-5 md:gap-8" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
									<span class="zc-process-dot zc-tl-dot<?php echo ! empty( $zicon ) ? ' zc-dot-has-ic' : ''; ?>"><?php if ( ! empty( $zicon ) ) : ?><span class="zc-process-dot-ic"><?php $this->render_icon( $zicon, 'h-4 w-4' ); ?></span><?php else : ?><span class="zc-arabic-num"><?php echo esc_html( $number ); ?></span><?php endif; ?></span>

									<div class="zc-process-body pb-2">
										<h3 class="zc-process-title text-[1.2rem] font-bold text-secondary">
											<?php if ( '' !== $url ) : ?>
												<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $title ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $title ); ?>
											<?php endif; ?>
										</h3>

										<?php if ( '' !== $desc ) : ?>
											<p class="zc-process-desc zc-lead mt-2 text-[0.95rem]"><?php echo esc_html( $desc ); ?></p>
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
