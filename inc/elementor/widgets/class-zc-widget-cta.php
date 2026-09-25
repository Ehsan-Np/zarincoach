<?php
/**
 * ویجت فراخوان اقدام (CTA)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Cta' ) ) :

	/**
	 * ویجت CTA.
	 */
	class ZC_Widget_Cta extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-cta';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'فراخوان اقدام', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-call-to-action';
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
				'badge',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
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

			$this->add_control(
				'text',
				array(
					'label'   => __( 'متن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$this->add_control(
				'note',
				array(
					'label'   => __( 'نکته زیر دکمه‌ها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->button_controls( 'primary_', __( 'دکمه اول', 'zarincoach' ) );
			$this->button_controls( 'secondary_', __( 'دکمه دوم', 'zarincoach' ) );

			$this->add_control(
				'secondary_icon',
				array(
					'label'   => __( 'آیکن دکمه دوم', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'send'      => __( 'تلگرام', 'zarincoach' ),
						'bale'      => __( 'بله', 'zarincoach' ),
						'whatsapp'  => __( 'واتس‌اپ', 'zarincoach' ),
						'phone'     => __( 'تلفن', 'zarincoach' ),
						'instagram' => __( 'اینستاگرام', 'zarincoach' ),
						'calendar'  => __( 'تقویم', 'zarincoach' ),
						'none'      => __( 'بدون آیکن', 'zarincoach' ),
					),
					'default' => 'send',
				)
			);

			$this->add_control(
				'show_form',
				array(
					'label'        => __( 'نمایش فرم رزرو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'form_shortcode',
				array(
					'label'       => __( 'شورت‌کد فرم دلخواه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'در صورت خالی بودن، فرم داخلی [zc_contact] نمایش داده می‌شود.', 'zarincoach' ),
					'condition'   => array( 'show_form' => 'yes' ),
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
				'dark_style',
				array(
					'label'        => __( 'نسخه تیره', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'show_blobs',
				array(
					'label'        => __( 'اشکال رنگی', 'zarincoach' ),
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

			$title        = (string) $this->value( 'title', 'home_cta_title' );
			$text         = (string) $this->value( 'text', 'home_cta_text' );
			$badge        = (string) $this->value( 'badge', '' );
			$note         = (string) $this->value( 'note', 'home_cta_note' );
			$show_form    = ! isset( $settings['show_form'] ) || 'yes' === (string) $settings['show_form'];
			$shortcode    = isset( $settings['form_shortcode'] ) ? (string) $settings['form_shortcode'] : '';
			$dark         = isset( $settings['dark_style'] ) && 'yes' === (string) $settings['dark_style'];
			$show_blobs   = ! isset( $settings['show_blobs'] ) || 'yes' === (string) $settings['show_blobs'];

			$primary_text   = (string) $this->value( 'primary_button_text', 'home_cta_primary_text' );
			$primary_url    = isset( $settings['primary_button_url']['url'] ) ? (string) $settings['primary_button_url']['url'] : (string) zc_opt( 'home_cta_primary_url', '#contact' );
			$secondary_text = (string) $this->value( 'secondary_button_text', 'home_cta_secondary_text' );
			$secondary_url  = isset( $settings['secondary_button_url']['url'] ) ? (string) $settings['secondary_button_url']['url'] : '';

			$panel_class = $dark
				? 'zc-tone-inverse relative overflow-hidden rounded-[var(--zc-radius)] p-6 shadow-lift sm:p-10 lg:p-12'
				: 'relative overflow-hidden rounded-[var(--zc-radius)] border border-line bg-surface p-6 shadow-lift sm:p-10 lg:p-12';

			$title_class = $dark ? 'text-white' : 'text-secondary';
			$text_class  = $dark ? 'text-white/75' : 'text-muted';
			?>
			<section class="zc-cta zc-section-tight relative overflow-hidden">
				<div class="zc-container">
					<div class="<?php echo esc_attr( $panel_class ); ?>">
						<div class="zc-grain pointer-events-none absolute inset-0 -z-10 <?php echo esc_attr( $dark ? 'bg-zc-grid opacity-70' : 'bg-surface2' ); ?>"></div>

						<?php if ( $show_blobs ) : ?>
							<div class="pointer-events-none absolute -top-24 start-0 -z-10 h-72 w-72 animate-zc-blob rounded-full bg-primary/25 blur-3xl"></div>
							<div class="pointer-events-none absolute -bottom-24 end-10 -z-10 h-72 w-72 animate-zc-blob rounded-full bg-info/25 blur-3xl" style="animation-delay:-7s"></div>
						<?php endif; ?>

						<div class="relative grid gap-8 lg:grid-cols-12 lg:gap-12">
							<div class="lg:col-span-7">
								<?php if ( '' !== $badge ) : ?>
									<span class="zc-badge"><?php echo esc_html( $badge ); ?></span>
								<?php endif; ?>

								<h2 class="zc-title-lg mt-5 <?php echo esc_attr( $title_class ); ?>"><?php echo esc_html( $title ); ?></h2>

								<?php if ( '' !== $text ) : ?>
									<p class="zc-lead mt-4 max-w-xl <?php echo esc_attr( $text_class ); ?>"><?php echo esc_html( $text ); ?></p>
								<?php endif; ?>

								<div class="mt-6 flex flex-wrap items-center gap-3">
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
									if ( '' !== $secondary_text && '' !== $secondary_url ) {
										zc_button(
											array(
												'text'  => $secondary_text,
												'url'   => $secondary_url,
												'style' => 'outline',
												'icon'  => ( isset( $settings['secondary_icon'] ) && 'none' !== $settings['secondary_icon'] ) ? (string) $settings['secondary_icon'] : '',
												'class' => 'zc-btn-lg',
											)
										);
									}
									?>
								</div>

								<?php if ( '' !== $note ) : ?>
									<p class="mt-5 inline-flex items-center gap-2 text-[0.82rem] <?php echo esc_attr( $text_class ); ?>">
										<?php zc_icon( 'clock', 'h-4 w-4 text-primary' ); ?>
										<?php echo esc_html( $note ); ?>
									</p>
								<?php endif; ?>
							</div>

							<?php if ( $show_form ) : ?>
								<div class="lg:col-span-5">
									<div class="rounded-[var(--zc-radius)] border border-line bg-base p-6 sm:p-7">
										<?php
										if ( '' !== $shortcode ) {
											echo do_shortcode( $shortcode );
										} else {
											echo do_shortcode( '[zc_contact]' );
										}
										?>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
