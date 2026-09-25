<?php
/**
 * ویجت کارت‌های طرحواره
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Schema' ) ) :

	/**
	 * ویجت طرحواره‌ها.
	 */
	class ZC_Widget_Schema extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-schema';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'طرحواره‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-lightbox';
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
				'item_title',
				array(
					'label'   => __( 'نام طرحواره', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_desc',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_url',
				array(
					'label'   => __( 'لینک (اختیاری)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'طرحواره‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ item_title }}}',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );

			$this->add_control(
				'show_numbers',
				array(
					'label'        => __( 'نمایش شماره', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'more_text',
				array(
					'label'     => __( 'متن پیوند «مشاهده همه»', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => '',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'more_url',
				array(
					'label'   => __( 'نشانی پیوند «مشاهده همه»', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'show_panel',
				array(
					'label'        => __( 'نمایش باکس دعوت به ارزیابی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->button_controls( 'panel_', __( 'دکمه باکس پایانی', 'zarincoach' ) );

			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$columns      = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
			$show_numbers = ! isset( $settings['show_numbers'] ) || 'yes' === (string) $settings['show_numbers'];
			$show_panel   = ! isset( $settings['show_panel'] ) || 'yes' === (string) $settings['show_panel'];
			$items        = ! empty( $settings['items'] ) ? $settings['items'] : array();

			$delay = 0;
			$index = 0;
			?>
			<section class="zc-schema zc-section relative overflow-hidden">
				<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-grid opacity-70"></div>

				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php if ( ! empty( $items ) ) : ?>
						<div class="zc-after-head grid gap-5 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
							<?php foreach ( $items as $item ) : ?>
								<?php
								$title = isset( $item['item_title'] ) ? trim( (string) $item['item_title'] ) : '';
								$desc  = isset( $item['item_desc'] ) ? trim( (string) $item['item_desc'] ) : '';
								$url   = isset( $item['item_url']['url'] ) ? (string) $item['item_url']['url'] : '';

								if ( '' === $title && '' === $desc ) {
									continue;
								}

								$index++;
								$number = str_pad( (string) $index, 2, '0', STR_PAD_LEFT );
								$tag    = '' !== $url ? 'a' : 'article';
								?>
								<<?php echo esc_html( $tag ); ?>
									class="zc-card zc-card-hover zc-reveal group relative overflow-hidden"
									data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>"
									<?php echo '' !== $url ? 'href="' . esc_url( $url ) . '"' : ''; ?>
								>
									<?php if ( $show_numbers ) : ?>
										<span class="zc-outline-num pointer-events-none absolute top-4 end-5 text-[3.25rem] leading-none opacity-40 transition-opacity duration-500 group-hover:opacity-100">
											<?php echo esc_html( $number ); ?>
										</span>
									<?php endif; ?>

									<?php if ( '' !== $title ) : ?>
										<h3 class="relative mt-3 text-[1.1rem] font-bold text-secondary"><?php echo esc_html( $title ); ?></h3>
									<?php endif; ?>

									<?php if ( '' !== $desc ) : ?>
										<p class="zc-lead relative mt-2 !text-[0.9rem]"><?php echo esc_html( $desc ); ?></p>
									<?php endif; ?>
								</<?php echo esc_html( $tag ); ?>>
								<?php
								$delay += 70;
							endforeach;
							?>
						</div>
					<?php endif; ?>

					<?php
					$more_text = isset( $settings['more_text'] ) ? trim( (string) $settings['more_text'] ) : '';
					$more_url  = isset( $settings['more_url']['url'] ) ? (string) $settings['more_url']['url'] : '';
					if ( '' !== $more_text && '' !== $more_url ) :
						?>
						<p class="mt-8 text-center">
							<a class="zc-sc-link" href="<?php echo esc_url( $more_url ); ?>"><?php echo esc_html( $more_text ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
						</p>
					<?php endif; ?>

					<?php if ( $show_panel ) : ?>
						<div class="zc-panel mt-8 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
							<div class="flex items-start gap-3">
								<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary"><?php zc_icon( 'mirror', 'h-5 w-5' ); ?></span>
								<div>
									<p class="m-0 font-bold text-secondary"><?php esc_html_e( 'نمی‌دانی کدام طرحواره در تو فعال است؟', 'zarincoach' ); ?></p>
									<p class="zc-lead m-0 !text-[0.9rem]"><?php esc_html_e( 'در جلسه آشنایی با یک پرسش‌نامه استاندارد، نقشه‌ی الگوهای تو را ترسیم می‌کنیم.', 'zarincoach' ); ?></p>
								</div>
							</div>

							<?php
							$panel_text = isset( $settings['panel_button_text'] ) ? (string) $settings['panel_button_text'] : '';
							$panel_url  = isset( $settings['panel_button_url']['url'] ) ? (string) $settings['panel_button_url']['url'] : '';

							if ( '' !== $panel_text && '' !== $panel_url ) {
								zc_button(
									array(
										'text' => $panel_text,
										'url'  => $panel_url,
									)
								);
							}
							?>
						</div>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
