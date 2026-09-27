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

			$this->add_control(
				'panel_icon',
				array(
					'label'       => __( 'آیکن باکس دعوت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = آیکن آینه‌ی قالب.', 'zarincoach' ),
					'condition'   => array( 'show_panel' => 'yes' ),
				)
			);

			$this->add_control(
				'panel_title',
				array(
					'label'       => __( 'عنوان باکس دعوت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'مطمئن نیستید کدام طرحواره‌ها در شما فعال‌اند؟', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
					'condition'   => array( 'show_panel' => 'yes' ),
				)
			);

			$this->add_control(
				'panel_text',
				array(
					'label'       => __( 'متن باکس دعوت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'default'     => __( 'در جلسه‌ی آشنایی با یک پرسش‌نامه‌ی استاندارد، با هم نقشه‌ی الگوهای ذهنی‌تان را ترسیم می‌کنیم.', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
					'condition'   => array( 'show_panel' => 'yes' ),
				)
			);

			$this->button_controls( 'panel_', __( 'دکمه باکس پایانی', 'zarincoach' ) );

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
					'num'   => array( __( 'شماره‌ی کارت‌ها', 'zarincoach' ), '.zc-schema-num' ),
					'desc'  => array( __( 'توضیح کارت‌ها', 'zarincoach' ), '.zc-schema-desc' ),
					'more'  => array( __( 'لینک «همه‌ی طرحواره‌ها»', 'zarincoach' ), '.zc-schema-more' ),
					'picon' => array( __( 'آیکن باکس دعوت', 'zarincoach' ), '.zc-schema-panel-icon' ),
					'pbtn'  => array( __( 'دکمه‌ی باکس دعوت', 'zarincoach' ), '.zc-schema-panel-btn' ),
					'bg'    => array( __( 'پس‌زمینه‌ی شطرنجی', 'zarincoach' ), '.zc-schema-bg' ),
				)
			);
			$this->zc_style(
				'sch_grid',
				__( 'شبکه‌ی کارت‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'grid', '.zc-schema-grid', '', array( 'cols' => false ) ),
				)
			);
			$this->zc_style(
				'sch_card',
				__( 'کارت‌ها', 'zarincoach' ),
				array(
					'card'  => array( 'box', '.zc-schema-card', __( 'کارت', 'zarincoach' ), array( 'hover' => true, 'align' => true, 'minh' => true ) ),
					'num'   => array( 'text', '.zc-schema-num', __( 'شماره', 'zarincoach' ), array( 'margin' => false ) ),
					'numc'  => array( 'color', '.zc-schema-num', __( 'رنگ خط دور شماره', 'zarincoach' ), array( 'prop' => '-webkit-text-stroke-color' ) ),
					'title' => array( 'text', '.zc-schema-title', __( 'عنوان', 'zarincoach' ) ),
					'desc'  => array( 'text', '.zc-schema-desc', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
					'more'  => array( 'text', '.zc-schema-more a', __( 'لینک «همه‌ی طرحواره‌ها»', 'zarincoach' ), array( 'hover' => true ) ),
				)
			);
			$this->zc_style(
				'sch_panel',
				__( 'باکس دعوت', 'zarincoach' ),
				array(
					'box'   => array( 'box', '.zc-schema-panel', __( 'باکس', 'zarincoach' ), array( 'margin' => true ) ),
					'icon'  => array( 'icon', '.zc-schema-panel-icon', __( 'آیکن', 'zarincoach' ) ),
					'title' => array( 'text', '.zc-schema-panel-title', __( 'عنوان', 'zarincoach' ), array( 'margin' => false ) ),
					'text'  => array( 'text', '.zc-schema-panel-text', __( 'متن', 'zarincoach' ), array( 'margin' => false ) ),
					'btn'   => array( 'button', '.zc-schema-panel-btn', __( 'دکمه', 'zarincoach' ) ),
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

			$columns      = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
			$show_numbers = ! isset( $settings['show_numbers'] ) || 'yes' === (string) $settings['show_numbers'];
			$show_panel   = ! isset( $settings['show_panel'] ) || 'yes' === (string) $settings['show_panel'];
			$items        = ! empty( $settings['items'] ) ? $settings['items'] : array();
			$panel_title  = isset( $settings['panel_title'] ) ? trim( (string) $settings['panel_title'] ) : __( 'مطمئن نیستید کدام طرحواره‌ها در شما فعال‌اند؟', 'zarincoach' );
			$panel_body   = isset( $settings['panel_text'] ) ? trim( (string) $settings['panel_text'] ) : __( 'در جلسه‌ی آشنایی با یک پرسش‌نامه‌ی استاندارد، با هم نقشه‌ی الگوهای ذهنی‌تان را ترسیم می‌کنیم.', 'zarincoach' );

			$delay = 0;
			$index = 0;
			?>
			<section class="zc-schema zc-section relative overflow-hidden">
				<div class="zc-schema-bg zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-grid opacity-70" aria-hidden="true"></div>

				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php if ( ! empty( $items ) ) : ?>
						<div class="zc-schema-grid zc-after-head grid gap-5 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
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
									class="zc-schema-card zc-card zc-card-hover zc-reveal group relative overflow-hidden"
									data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>"
									<?php echo '' !== $url ? 'href="' . esc_url( $url ) . '"' : ''; ?>
								>
									<?php if ( $show_numbers ) : ?>
										<span class="zc-schema-num zc-outline-num pointer-events-none absolute top-4 end-5 text-[3.25rem] leading-none opacity-40 transition-opacity duration-500 group-hover:opacity-100">
											<?php echo esc_html( $number ); ?>
										</span>
									<?php endif; ?>

									<?php if ( '' !== $title ) : ?>
										<h3 class="zc-schema-title relative mt-3 text-[1.1rem] font-bold text-secondary"><?php echo esc_html( $title ); ?></h3>
									<?php endif; ?>

									<?php if ( '' !== $desc ) : ?>
										<p class="zc-schema-desc zc-lead relative mt-2 text-[0.9rem]"><?php echo esc_html( $desc ); ?></p>
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
						<p class="zc-schema-more mt-8 text-center">
							<a class="zc-sc-link" href="<?php echo esc_url( $more_url ); ?>"><?php echo esc_html( $more_text ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
						</p>
					<?php endif; ?>

					<?php if ( $show_panel ) : ?>
						<div class="zc-schema-panel zc-panel mt-8 flex flex-col items-start gap-4 sm:flex-row sm:items-center sm:justify-between">
							<div class="flex items-start gap-3">
								<span class="zc-schema-panel-icon grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary"><?php $this->zc_render_icon_or( isset( $settings['panel_icon'] ) ? $settings['panel_icon'] : array(), 'mirror' ); ?></span>
								<div>
									<?php if ( '' !== $panel_title ) : ?>
										<p class="zc-schema-panel-title m-0 font-bold text-secondary"><?php echo esc_html( $panel_title ); ?></p>
									<?php endif; ?>
									<?php if ( '' !== $panel_body ) : ?>
										<p class="zc-schema-panel-text zc-lead m-0 text-[0.9rem]"><?php echo esc_html( $panel_body ); ?></p>
									<?php endif; ?>
								</div>
							</div>

							<?php
							$panel_text = isset( $settings['panel_button_text'] ) ? (string) $settings['panel_button_text'] : '';
							$panel_url  = isset( $settings['panel_button_url']['url'] ) ? (string) $settings['panel_button_url']['url'] : '';

							if ( '' !== $panel_text && '' !== $panel_url ) {
								zc_button(
									$this->zc_button_icon_args(
										array(
											'text'  => $panel_text,
											'url'   => $panel_url,
											'class' => 'zc-schema-panel-btn',
										),
										$settings,
										'panel_'
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
