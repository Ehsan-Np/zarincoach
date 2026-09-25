<?php
/**
 * ویجت بنر پیشنهاد/تخفیف فروشگاه
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Shop_Promo' ) && class_exists( 'ZC_Shop_Widget_Base' ) ) :

	/**
	 * بنر تخفیف با کد کوپن قابل کپی و شمارش معکوس.
	 */
	class ZC_Widget_Shop_Promo extends ZC_Shop_Widget_Base {

		/**
		 * نام.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-shop-promo';
		}

		/**
		 * عنوان.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'بنر تخفیف فروشگاه', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-countdown';
		}

		/**
		 * کنترل‌ها.
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
				'eyebrow',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'پیشنهاد محدود', 'zarincoach' ),
				)
			);
			$this->add_control(
				'title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => __( '۲۰٪ تخفیف اولین خرید از فروشگاه', 'zarincoach' ),
				)
			);
			$this->add_control(
				'text',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => __( 'کد زیر را در سبد خرید وارد کنید؛ برای همه‌ی کتاب‌ها و کارپوشه‌ها معتبر است.', 'zarincoach' ),
				)
			);
			$this->add_control(
				'coupon',
				array(
					'label'       => __( 'کد تخفیف', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'کوپن را در «بازاریابی ← کوپن‌ها» بسازید. خالی = بدون کادر کد.', 'zarincoach' ),
				)
			);
			$this->add_control(
				'end',
				array(
					'label'          => __( 'پایان پیشنهاد', 'zarincoach' ),
					'type'           => \Elementor\Controls_Manager::DATE_TIME,
					'picker_options' => array( 'enableTime' => true ),
					'description'    => __( 'خالی = اگر کد تخفیف تاریخ انقضا دارد، از آن استفاده می‌شود؛ در غیر این صورت شمارش معکوس نمایش داده نمی‌شود.', 'zarincoach' ),
				)
			);
			$this->add_control(
				'hide_expired',
				array(
					'label'        => __( 'پنهان شدن پس از پایان', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'image',
				array(
					'label' => __( 'تصویر (اختیاری)', 'zarincoach' ),
					'type'  => \Elementor\Controls_Manager::MEDIA,
				)
			);

			$this->button_controls();

			$this->end_controls_section();
		}

		/**
		 * زمان پایان (Unix).
		 *
		 * @param array $s تنظیمات.
		 * @return int
		 */
		protected function end_time( $s ) {
			$end = trim( (string) ( $s['end'] ?? '' ) );
			if ( '' !== $end ) {
				try {
					$dt = new DateTime( $end, wp_timezone() );
					return $dt->getTimestamp();
				} catch ( Exception $e ) {
					return 0;
				}
			}
			$code = trim( (string) ( $s['coupon'] ?? '' ) );
			if ( '' !== $code && class_exists( 'WC_Coupon' ) ) {
				$coupon  = new WC_Coupon( $code );
				$expires = $coupon->get_id() ? $coupon->get_date_expires() : null;
				if ( $expires ) {
					return $expires->getTimestamp();
				}
			}
			return 0;
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s       = $this->get_settings_for_display();
			$end     = $this->end_time( $s );
			$expired = $end && $end <= time();
			if ( $expired && $this->is_on( $s, 'hide_expired' ) ) {
				$this->editor_notice( __( 'زمان این پیشنهاد به پایان رسیده و برای بازدیدکنندگان پنهان است.', 'zarincoach' ) );
				return;
			}
			$code  = trim( (string) ( $s['coupon'] ?? '' ) );
			$image = ! empty( $s['image']['id'] ) ? (int) $s['image']['id'] : 0;
			?>
			<section class="zc-section zc-promo-wrap">
				<div class="zc-container">
					<div class="zc-promo<?php echo $image ? ' has-img' : ''; ?>">
						<div class="zc-promo__body">
							<?php if ( '' !== trim( (string) $s['eyebrow'] ) ) : ?>
								<span class="zc-promo__tag"><?php zc_icon( 'percent', 'h-4 w-4' ); ?><?php echo esc_html( $s['eyebrow'] ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $s['title'] ) ) : ?>
								<h2 class="zc-promo__title"><?php echo esc_html( $s['title'] ); ?></h2>
							<?php endif; ?>
							<?php if ( '' !== trim( (string) $s['text'] ) ) : ?>
								<p class="zc-promo__text"><?php echo esc_html( $s['text'] ); ?></p>
							<?php endif; ?>
							<div class="zc-promo__row">
								<?php if ( '' !== $code ) : ?>
									<button type="button" class="zc-promo__code" data-zc-copy="<?php echo esc_attr( $code ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: کد تخفیف */ __( 'کپی کد تخفیف %s', 'zarincoach' ), $code ) ); ?>">
										<code dir="ltr"><?php echo esc_html( $code ); ?></code>
										<span data-zc-copy-label><?php esc_html_e( 'کپی کد', 'zarincoach' ); ?></span>
									</button>
								<?php endif; ?>
								<?php $this->render_button( '', 'zc-promo__btn' ); ?>
							</div>
						</div>
						<?php if ( $end && ! $expired && function_exists( 'zc_wc_countdown_html' ) ) : ?>
							<div class="zc-promo__timer">
								<?php echo zc_wc_countdown_html( $end, __( 'زمان باقی‌مانده:', 'zarincoach' ), 'zc-cd--promo' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
						<?php endif; ?>
						<?php if ( $image ) : ?>
							<div class="zc-promo__media"><?php echo wp_get_attachment_image( $image, 'large', false, array( 'loading' => 'lazy', 'alt' => '' ) ); ?></div>
						<?php endif; ?>
					</div>
				</div>
			</section>
			<?php
		}
	}

endif;
