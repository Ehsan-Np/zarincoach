<?php
/**
 * ویجت سربرگ اصلی (Hero)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Hero' ) ) :

	/**
	 * ویجت Hero.
	 */
	class ZC_Widget_Hero extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-hero';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'سربرگ اصلی (Hero)', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-banner';
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {

			/* ---------------- محتوا ---------------- */
			$this->start_controls_section(
				'content_section',
				array(
					'label' => __( 'محتوا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک نمایش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'split'    => __( 'دو ستونه با تصویر', 'zarincoach' ),
						'centered' => __( 'متن میانی', 'zarincoach' ),
					),
					'default' => 'split',
				)
			);

			$this->add_control(
				'badge',
				array(
					'label'       => __( 'برچسب', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'کوچینگ توسعه فردی و طرحواره‌درمانی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'title',
				array(
					'label'       => __( 'عنوان اصلی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 2,
					'default'     => '',
					'placeholder' => __( 'نسخه‌ی آگاهانه‌ی خودت را بنویس', 'zarincoach' ),
				)
			);

			$this->add_control(
				'title_tag',
				array(
					'label'   => __( 'تگ عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'h1' => 'H1',
						'h2' => 'H2',
						'div' => 'DIV',
					),
					'default' => 'h1',
				)
			);

			$this->add_control(
				'title_accent',
				array(
					'label'       => __( 'واژه‌ی رنگی در عنوان', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'این واژه با رنگ گرادیان پالت نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'subtitle',
				array(
					'label'   => __( 'زیرعنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$this->add_control(
				'image',
				array(
					'label'   => __( 'تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => array( 'url' => '' ),
				)
			);

			$this->end_controls_section();

			/* ---------------- دکمه‌ها ---------------- */
			$this->start_controls_section(
				'buttons_section',
				array(
					'label' => __( 'دکمه‌ها', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->button_controls( 'primary_', __( 'دکمه اول', 'zarincoach' ) );
			$this->button_controls( 'secondary_', __( 'دکمه دوم', 'zarincoach' ) );

			$this->end_controls_section();

			/* ---------------- آمار ---------------- */
			$this->start_controls_section(
				'stats_section',
				array(
					'label' => __( 'آمار کوتاه', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'show_stats',
				array(
					'label'        => __( 'نمایش آمار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'بله', 'zarincoach' ),
					'label_off'    => __( 'خیر', 'zarincoach' ),
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'stat_number',
				array(
					'label'   => __( 'عدد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
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
				'stats',
				array(
					'label'       => __( 'آیتم‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ stat_number }}}',
					'condition'   => array( 'show_stats' => 'yes' ),
				)
			);

			$this->end_controls_section();

			/* ---------------- استایل ---------------- */
			$this->start_controls_section(
				'style_section',
				array(
					'label' => __( 'استایل', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'show_blobs',
				array(
					'label'        => __( 'اشکال رنگی پس‌زمینه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_grain',
				array(
					'label'        => __( 'بافت کاغذی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_responsive_control(
				'padding_y',
				array(
					'label'      => __( 'فاصله عمودی', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px' ),
					'range'      => array(
						'px' => array( 'min' => 20, 'max' => 200, 'step' => 4 ),
					),
					'default'        => array( 'size' => 64, 'unit' => 'px' ),
					'tablet_default' => array( 'size' => 52, 'unit' => 'px' ),
					'mobile_default' => array( 'size' => 40, 'unit' => 'px' ),
					'selectors'  => array(
						'{{WRAPPER}} .zc-hero' => 'padding-top: {{SIZE}}{{UNIT}}; padding-bottom: {{SIZE}}{{UNIT}};',
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

			$style        = isset( $settings['style'] ) ? (string) $settings['style'] : 'split';
			$title        = (string) $this->value( 'title', 'home_hero_title' );
			$accent       = (string) $this->value( 'title_accent', 'home_hero_title_accent' );
			$badge        = (string) $this->value( 'badge', 'home_hero_badge' );
			$subtitle     = (string) $this->value( 'subtitle', 'home_hero_subtitle' );
			$title_tag    = isset( $settings['title_tag'] ) ? (string) $settings['title_tag'] : 'h1';
			$show_stats   = isset( $settings['show_stats'] ) && 'yes' === (string) $settings['show_stats'];
			$show_blobs   = ! isset( $settings['show_blobs'] ) || 'yes' === (string) $settings['show_blobs'];
			$show_grain   = ! isset( $settings['show_grain'] ) || 'yes' === (string) $settings['show_grain'];
			$image        = $this->image_src( 'image', 'home_hero_image', 'portrait' );

			// دکمه‌ها.
			$primary_text   = (string) $this->value( 'primary_button_text', 'home_hero_primary_text' );
			$primary_url    = isset( $settings['primary_button_url']['url'] ) ? (string) $settings['primary_button_url']['url'] : (string) zc_opt( 'home_hero_primary_url', '#booking' );
			$secondary_text = (string) $this->value( 'secondary_button_text', 'home_hero_secondary_text' );
			$secondary_url  = isset( $settings['secondary_button_url']['url'] ) ? (string) $settings['secondary_button_url']['url'] : (string) zc_opt( 'home_hero_secondary_url', '#services' );

			$title_html = esc_html( $title );
			if ( '' !== $accent && false !== strpos( $title, $accent ) ) {
				$title_html = str_replace( esc_html( $accent ), '<span class="zc-gradient-text">' . esc_html( $accent ) . '</span>', $title_html );
			}
			?>
			<section class="zc-hero relative overflow-hidden">
				<?php if ( $show_grain ) : ?>
					<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-base"></div>
				<?php endif; ?>

				<?php if ( $show_blobs ) : ?>
					<div class="pointer-events-none absolute -top-24 start-1/4 -z-10 h-[420px] w-[420px] animate-zc-blob rounded-full bg-primary/20 blur-3xl"></div>
					<div class="pointer-events-none absolute -bottom-32 end-10 -z-10 h-[380px] w-[380px] animate-zc-blob rounded-full bg-info/25 blur-3xl" style="animation-delay:-6s"></div>
				<?php endif; ?>

				<div class="zc-container relative">
					<?php if ( 'centered' === $style ) : ?>
						<div class="mx-auto max-w-3xl text-center">
							<?php if ( '' !== $badge ) : ?>
								<span class="zc-badge"><?php echo esc_html( $badge ); ?></span>
							<?php endif; ?>

							<<?php echo esc_html( $title_tag ); ?> class="zc-display mt-6"><?php echo wp_kses_post( $title_html ); ?></<?php echo esc_html( $title_tag ); ?>>

							<?php if ( '' !== $subtitle ) : ?>
								<p class="zc-lead mx-auto mt-5 max-w-2xl"><?php echo esc_html( $subtitle ); ?></p>
							<?php endif; ?>

							<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
								<?php
								if ( '' !== $primary_text ) {
									zc_button(
										array(
											'text'  => $primary_text,
											'url'   => $primary_url,
											'class' => 'zc-btn-lg',
										)
									);
								}
								if ( '' !== $secondary_text ) {
									zc_button(
										array(
											'text'  => $secondary_text,
											'url'   => $secondary_url,
											'style' => 'outline',
											'icon'  => '',
											'class' => 'zc-btn-lg',
										)
									);
								}
								?>
							</div>
						</div>
					<?php else : ?>

						<div class="grid items-center gap-10 lg:grid-cols-12 lg:gap-8">
							<div class="lg:col-span-7">
								<?php if ( '' !== $badge ) : ?>
									<span class="zc-badge"><?php echo esc_html( $badge ); ?></span>
								<?php endif; ?>

								<<?php echo esc_html( $title_tag ); ?> class="zc-display mt-6"><?php echo wp_kses_post( $title_html ); ?></<?php echo esc_html( $title_tag ); ?>>

								<?php if ( '' !== $subtitle ) : ?>
									<p class="zc-lead mt-5 max-w-xl"><?php echo esc_html( $subtitle ); ?></p>
								<?php endif; ?>

								<div class="mt-8 flex flex-wrap items-center gap-3">
									<?php
									if ( '' !== $primary_text ) {
										zc_button(
											array(
												'text'  => $primary_text,
												'url'   => $primary_url,
												'class' => 'zc-btn-lg',
											)
										);
									}
									if ( '' !== $secondary_text ) {
										zc_button(
											array(
												'text'  => $secondary_text,
												'url'   => $secondary_url,
												'style' => 'outline',
												'icon'  => '',
												'class' => 'zc-btn-lg',
											)
										);
									}
									?>
								</div>

								<?php if ( $show_stats && ! empty( $settings['stats'] ) ) : ?>
									<div class="zc-rule my-9 max-w-xl"></div>
									<dl class="grid max-w-xl grid-cols-3 gap-4">
										<?php foreach ( $settings['stats'] as $stat ) : ?>
											<div>
												<dt class="zc-stat-num text-secondary"><?php echo esc_html( isset( $stat['stat_number'] ) ? $stat['stat_number'] : '' ); ?></dt>
												<dd class="mt-1.5 text-[0.8rem] leading-relaxed text-muted"><?php echo esc_html( isset( $stat['stat_label'] ) ? $stat['stat_label'] : '' ); ?></dd>
											</div>
										<?php endforeach; ?>
									</dl>
								<?php endif; ?>
							</div>

							<div class="relative lg:col-span-5">
								<div class="relative">
									<div class="zc-figure zc-notch aspect-[4/5] shadow-lift">
										<?php
										// تصویر اصلی بالای صفحه = LCP ← بدون lazy و با اولویت بالا.
										echo zc_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											$image,
											'zc_portrait',
											array(
												'alt'      => $title,
												'width'    => 800,
												'height'   => 1000,
												'sizes'    => '(min-width: 1280px) 480px, (min-width: 1024px) 38vw, 92vw',
												'priority' => true,
											)
										);
										?>
									</div>

									<div class="absolute -bottom-6 start-2 z-10 w-[240px] rounded-2xl border border-line bg-surface p-4 shadow-lift sm:start-6">
										<div class="flex items-center gap-3">
											<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
												<?php zc_icon( 'calendar', 'h-5 w-5' ); ?>
											</span>
											<div>
												<p class="m-0 text-[0.82rem] font-bold text-secondary"><?php esc_html_e( 'جلسه آشنایی', 'zarincoach' ); ?></p>
												<p class="m-0 text-[0.75rem] text-muted"><?php esc_html_e( '۳۰ دقیقه، بدون هزینه', 'zarincoach' ); ?></p>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>

					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
