<?php
/**
 * ویجت آمار و ارقام (شمارنده)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Stats' ) ) :

	/**
	 * ویجت آمار.
	 */
	class ZC_Widget_Stats extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-stats';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'آمار و ارقام', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-counter';
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

			$this->add_control(
				'title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'stat_number',
				array(
					'label'   => __( 'عدد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '100',
				)
			);

			$repeater->add_control(
				'stat_suffix',
				array(
					'label'       => __( 'پسوند', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'مثل ٪ یا +', 'zarincoach' ),
				)
			);

			$repeater->add_control(
				'stat_label',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'آیتم‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ stat_label }}}',
				)
			);

			$this->add_control(
				'persian_digits',
				array(
					'label'        => __( 'ارقام فارسی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'dark_panel',
				array(
					'label'        => __( 'پس‌زمینه تیره', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '4' );

			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$items    = ! empty( $settings['items'] ) ? $settings['items'] : array();
			$title    = isset( $settings['title'] ) ? (string) $settings['title'] : '';
			$persian  = ! isset( $settings['persian_digits'] ) || 'yes' === (string) $settings['persian_digits'];
			$dark     = ! isset( $settings['dark_panel'] ) || 'yes' === (string) $settings['dark_panel'];
			$columns  = isset( $settings['columns'] ) ? (string) $settings['columns'] : '4';

			$grid = 'grid-cols-2';
			if ( '1' === $columns ) {
				$grid = 'grid-cols-1';
			} elseif ( '3' === $columns ) {
				$grid = 'grid-cols-2 lg:grid-cols-3';
			} elseif ( '4' === $columns ) {
				$grid = 'grid-cols-2 lg:grid-cols-4';
			}

			$panel_class = $dark ? 'zc-panel-dark relative overflow-hidden' : 'zc-panel relative overflow-hidden';
			$num_class   = $dark ? 'text-accent' : 'text-secondary';
			?>
			<section class="zc-stats zc-section-tight relative overflow-hidden">
				<div class="zc-container">
					<div class="<?php echo esc_attr( $panel_class ); ?>">
						<?php if ( $dark ) : ?>
							<div class="pointer-events-none absolute -top-24 start-1/4 h-72 w-72 rounded-full bg-primary/25 blur-3xl"></div>
							<div class="pointer-events-none absolute -bottom-24 end-1/4 h-72 w-72 rounded-full bg-info/20 blur-3xl"></div>
						<?php endif; ?>

						<div class="relative">
							<?php if ( '' !== $title ) : ?>
								<h2 class="max-w-3xl text-[1.5rem] font-bold leading-snug <?php echo esc_attr( $dark ? 'text-white' : 'text-secondary' ); ?> sm:text-[1.9rem]">
									<?php echo esc_html( $title ); ?>
								</h2>
								<div class="<?php echo esc_attr( $dark ? 'my-9 h-px w-full bg-white/15' : 'zc-rule my-9' ); ?>"></div>
							<?php endif; ?>

							<?php if ( ! empty( $items ) ) : ?>
								<dl class="grid gap-8 sm:gap-10 <?php echo esc_attr( $grid ); ?>">
									<?php
									$delay = 0;
									foreach ( $items as $item ) :
										$raw    = isset( $item['stat_number'] ) ? (string) $item['stat_number'] : '0';
										$value  = zc_digits_to_latin( $raw );
										$suffix = isset( $item['stat_suffix'] ) ? (string) $item['stat_suffix'] : '';
										$label  = isset( $item['stat_label'] ) ? (string) $item['stat_label'] : '';
										?>
										<div class="zc-reveal" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
											<dt class="text-[2.4rem] font-bold leading-none <?php echo esc_attr( $num_class ); ?> sm:text-[3rem]">
												<span
													data-zc-count="<?php echo esc_attr( preg_replace( '/[^\d.]/', '', $value ) ); ?>"
													data-zc-suffix="<?php echo esc_attr( $suffix ); ?>"
													data-zc-persian="<?php echo esc_attr( $persian ? '1' : '0' ); ?>"
												><?php echo esc_html( ( $persian ? zc_digits_to_persian( preg_replace( '/[^\d.]/', '', $value ) ) : preg_replace( '/[^\d.]/', '', $value ) ) . $suffix ); ?></span>
											</dt>
											<dd class="mt-3 text-[0.85rem] leading-relaxed <?php echo esc_attr( $dark ? 'text-white/70' : 'text-muted' ); ?>">
												<?php echo esc_html( $label ); ?>
											</dd>
										</div>
										<?php
										$delay += 90;
									endforeach;
									?>
								</dl>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
