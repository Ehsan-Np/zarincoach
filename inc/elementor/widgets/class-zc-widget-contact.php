<?php
/**
 * ویجت اطلاعات تماس و نقشه
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Contact' ) ) :

	/**
	 * ویجت اطلاعات تماس.
	 */
	class ZC_Widget_Contact extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-contact';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'اطلاعات تماس', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-map-pin';
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

			$contact = function_exists( 'zc_contact_fields' ) ? zc_contact_fields() : array();

			$this->add_control(
				'phone',
				array(
					'label'   => __( 'تلفن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => isset( $contact['phone'] ) ? $contact['phone'] : '',
				)
			);

			$this->add_control(
				'mobile',
				array(
					'label'   => __( 'تلفن دوم (اختیاری)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => isset( $contact['phone2'] ) ? $contact['phone2'] : '',
				)
			);

			$this->add_control(
				'email',
				array(
					'label'   => __( 'ایمیل', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => isset( $contact['email'] ) ? $contact['email'] : '',
				)
			);

			$this->add_control(
				'address',
				array(
					'label'   => __( 'نشانی', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => isset( $contact['address'] ) ? $contact['address'] : '',
				)
			);

			$this->add_control(
				'hours',
				array(
					'label'   => __( 'ساعات پاسخ‌گویی', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => isset( $contact['hours'] ) ? $contact['hours'] : '',
				)
			);

			$this->add_control(
				'map_iframe',
				array(
					'label'       => __( 'کد iframe نقشه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::CODE,
					'language'    => 'html',
					'rows'        => 6,
					'default'     => (string) zc_opt( 'contact_map', '' ),
					'description' => __( 'در صورت خالی بودن، نقشه نمایش داده نمی‌شود. بارگذاری نقشه به صورت تنبل انجام می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'phone_label',
				array(
					'label'       => __( 'برچسب تلفن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'تلفن رزرو نوبت', 'zarincoach' ),
					'description' => __( 'خالی = برچسب ثبت‌شده در تنظیمات قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'channels',
				array(
					'label'       => __( 'پیام‌رسان‌ها و شبکه‌ها (کارت جداگانه)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => array(
						'telegram'  => __( 'تلگرام', 'zarincoach' ),
						'bale'      => __( 'بله', 'zarincoach' ),
						'instagram' => __( 'اینستاگرام', 'zarincoach' ),
						'eitaa'     => __( 'ایتا', 'zarincoach' ),
						'whatsapp'  => __( 'واتس‌اپ', 'zarincoach' ),
					),
					'default'     => array( 'telegram', 'bale', 'instagram' ),
					'description' => __( 'نشانی‌ها از «تنظیمات قالب ← تماس و شبکه‌های اجتماعی» خوانده می‌شوند.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_socials',
				array(
					'label'        => __( 'نمایش شبکه‌های اجتماعی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'split' => __( 'دو ستونه', 'zarincoach' ),
						'stack' => __( 'تک ستونه', 'zarincoach' ),
					),
					'default' => 'split',
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

			$show_socials = ! isset( $settings['show_socials'] ) || 'yes' === (string) $settings['show_socials'];
			$layout       = isset( $settings['layout'] ) ? (string) $settings['layout'] : 'split';
			$map          = isset( $settings['map_iframe'] ) ? trim( (string) $settings['map_iframe'] ) : '';

			$phone  = (string) $this->value( 'phone', 'contact_phone' );
			$phone2 = (string) $this->value( 'mobile', 'contact_mobile' );
			$email  = (string) $this->value( 'email', 'contact_email' );
			$fields = zc_contact_fields();
			$plabel = isset( $settings['phone_label'] ) && '' !== $settings['phone_label'] ? (string) $settings['phone_label'] : $fields['phone_label'];

			$items = array(
				array( 'icon' => 'phone', 'label' => $plabel, 'value' => zc_digits_to_persian( $phone ), 'url' => 'tel:' . zc_normalize_phone( $phone ), 'ltr' => true ),
				array( 'icon' => 'phone', 'label' => $fields['phone2_label'], 'value' => zc_digits_to_persian( $phone2 ), 'url' => 'tel:' . zc_normalize_phone( $phone2 ), 'ltr' => true ),
			);

			$channels = isset( $settings['channels'] ) ? (array) $settings['channels'] : array( 'telegram', 'bale', 'instagram' );
			foreach ( zc_contact_channels( $channels ) as $ch ) {
				$items[] = array( 'icon' => $ch['icon'], 'label' => $ch['label'], 'value' => '' !== $ch['sub'] ? $ch['sub'] : $ch['url'], 'url' => $ch['url'], 'ltr' => true, 'external' => true );
			}

			$items[] = array( 'icon' => 'mail', 'label' => __( 'ایمیل', 'zarincoach' ), 'value' => $email, 'url' => 'mailto:' . sanitize_email( $email ), 'ltr' => true );
			$items[] = array( 'icon' => 'map-pin', 'label' => __( 'نشانی مطب', 'zarincoach' ), 'value' => (string) $this->value( 'address', 'contact_address' ), 'url' => '', 'wide' => true );
			$items[] = array( 'icon' => 'clock', 'label' => __( 'ساعات پاسخ‌گویی', 'zarincoach' ), 'value' => (string) $this->value( 'hours', 'contact_hours' ), 'url' => '', 'wide' => true );

			$items = array_filter(
				$items,
				static function ( $item ) {
					return '' !== trim( (string) $item['value'] );
				}
			);
			?>
			<section class="zc-contact zc-section relative">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>

					<div class="zc-after-head grid gap-6 lg:gap-8 <?php echo 'split' === $layout ? 'lg:grid-cols-2' : ''; ?>">
						<div>
							<ul class="grid gap-4 sm:grid-cols-2">
								<?php foreach ( $items as $item ) : ?>
									<li class="<?php echo ! empty( $item['wide'] ) ? 'sm:col-span-2 ' : ''; ?>flex items-start gap-4 rounded-2xl border border-line bg-surface p-4 transition-colors hover:border-primary/40">
										<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
											<?php zc_icon( $item['icon'], 'h-5 w-5' ); ?>
										</span>
										<div class="min-w-0">
											<span class="block text-[0.75rem] text-muted"><?php echo esc_html( $item['label'] ); ?></span>
											<?php if ( '' !== $item['url'] ) : ?>
												<a class="<?php echo ! empty( $item['ltr'] ) ? 'zc-contact-ltr ' : ''; ?>block break-words font-bold text-secondary transition-colors hover:text-primary" href="<?php echo esc_url( $item['url'] ); ?>"<?php echo ! empty( $item['ltr'] ) ? ' dir="ltr" style="text-align:right"' : ' dir="auto"'; ?><?php echo ! empty( $item['external'] ) ? ' target="_blank" rel="noopener"' : ''; ?>><?php echo esc_html( $item['value'] ); ?></a>
											<?php else : ?>
												<span class="block break-words font-bold text-secondary"><?php echo esc_html( $item['value'] ); ?></span>
											<?php endif; ?>
										</div>
									</li>
								<?php endforeach; ?>
							</ul>

							<?php if ( $show_socials ) : ?>
								<div class="mt-6">
									<?php zc_social_links(); ?>
								</div>
							<?php endif; ?>
						</div>

						<?php if ( '' !== $map ) : ?>
							<div class="zc-figure zc-figure-plain zc-map relative min-h-[320px] overflow-hidden">
								<?php
								// تبدیل src به data-src برای بارگذاری تنبل.
								$lazy = preg_replace( '/(<iframe[^>]*)\ssrc=/i', '$1 data-src=', $map );
								echo $lazy; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- کد مدیر سایت
								?>
							</div>
						<?php endif; ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
