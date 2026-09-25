<?php
/**
 * ویجت درباره من
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_About' ) ) :

	/**
	 * ویجت درباره من.
	 */
	class ZC_Widget_About extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-about';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'درباره من', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-person';
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
				'content',
				array(
					'label'       => __( 'متن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 8,
					'default'     => '',
					'description' => __( 'برای جدا کردن پاراگراف‌ها از یک خط خالی استفاده کنید.', 'zarincoach' ),
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

			$this->add_control(
				'image_position',
				array(
					'label'   => __( 'موقعیت تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'right' => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-h-align-right',
						),
						'left'  => array(
							'title' => __( 'چپ', 'zarincoach' ),
							'icon'  => 'eicon-h-align-left',
						),
					),
					'default' => 'right',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'badge_number',
				array(
					'label'   => __( 'عدد نشان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'badge_label',
				array(
					'label'   => __( 'برچسب نشان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'feature',
				array(
					'label'   => __( 'متن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'features',
				array(
					'label'       => __( 'ویژگی‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ feature }}}',
				)
			);

			$this->button_controls( '', __( 'دکمه', 'zarincoach' ) );

			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$image   = $this->image_src( 'image', 'home_about_image', 'portrait' );
			$content = (string) $this->value( 'content', 'home_about_content' );
			$content = preg_split( '/\n\s*\n/', $content ) ?: array( $content );

			$features = array();
			if ( ! empty( $settings['features'] ) ) {
				foreach ( $settings['features'] as $item ) {
					if ( ! empty( $item['feature'] ) ) {
						$features[] = (string) $item['feature'];
					}
				}
			}

			$position = isset( $settings['image_position'] ) ? (string) $settings['image_position'] : 'right';

			$image_col = 'lg:col-span-5';
			$text_col  = 'lg:col-span-7';
			?>
			<section class="zc-about zc-section relative overflow-hidden">
				<div class="zc-container">
					<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
						<?php if ( 'right' === $position ) : ?>
							<div class="<?php echo esc_attr( $image_col ); ?>">
								<?php $this->render_image( $image, $settings ); ?>
							</div>
						<?php endif; ?>

						<div class="<?php echo esc_attr( $text_col ); ?>">
							<?php $this->render_heading( '', 'h2' ); ?>

							<div class="zc-lead mt-6 grid gap-4 zc-reveal">
								<?php foreach ( $content as $paragraph ) : ?>
									<?php if ( '' === trim( (string) $paragraph ) ) { continue; } ?>
									<p class="m-0"><?php echo wp_kses( trim( (string) $paragraph ), array( 'strong' => array(), 'b' => array(), 'em' => array(), 'br' => array(), 'a' => array( 'href' => array(), 'target' => array(), 'rel' => array() ) ) ); ?></p>
								<?php endforeach; ?>
							</div>

							<?php if ( ! empty( $features ) ) : ?>
								<ul class="zc-checklist mt-7 zc-reveal">
									<?php foreach ( $features as $feature ) : ?>
										<li><?php echo esc_html( $feature ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<div class="mt-8">
								<?php $this->render_button( '', 'zc-btn-lg' ); ?>
							</div>
						</div>

						<?php if ( 'left' === $position ) : ?>
							<div class="<?php echo esc_attr( $image_col ); ?>">
								<?php $this->render_image( $image, $settings ); ?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
			<?php
		}

		/**
		 * خروجی تصویر با نشان.
		 *
		 * @param array  $image    رسانه (id/url).
		 * @param array  $settings تنظیمات.
		 * @return void
		 */
		protected function render_image( $image, $settings ) {
			$badge_number = isset( $settings['badge_number'] ) ? (string) $settings['badge_number'] : '';
			$badge_label  = isset( $settings['badge_label'] ) ? (string) $settings['badge_label'] : '';
			?>
			<div class="relative zc-reveal">
				<div class="zc-figure zc-notch-alt aspect-[4/5] shadow-lift">
					<?php
					echo zc_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						$image,
						'zc_portrait',
						array(
							'alt'    => (string) zc_opt( 'seo_person_name', get_bloginfo( 'name' ) ),
							'width'  => 800,
							'height' => 1000,
							'sizes'  => '(min-width: 1280px) 520px, (min-width: 1024px) 40vw, 92vw',
						)
					);
					?>
				</div>

				<?php if ( '' !== $badge_number ) : ?>
					<div class="absolute -bottom-7 end-4 z-10 grid h-28 w-28 place-items-center rounded-full border border-line bg-base text-center shadow-lift">
						<div>
							<span class="block text-[1.9rem] font-bold leading-none text-primary"><?php echo esc_html( $badge_number ); ?></span>
							<span class="mt-1 block text-[0.68rem] leading-tight text-muted"><?php echo esc_html( $badge_label ); ?></span>
						</div>
					</div>
				<?php endif; ?>

				<div class="pointer-events-none absolute -top-6 -start-6 -z-10 h-24 w-24 rounded-2xl border-2 border-primary/30"></div>
			</div>
			<?php
		}
	}
endif;
