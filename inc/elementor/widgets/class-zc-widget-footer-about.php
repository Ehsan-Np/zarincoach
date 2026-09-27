<?php
/**
 * ویجت پانوشت: درباره من
 *
 * نام/لوگوی سایت، متن معرفی و شبکه‌های اجتماعی — همه با کیت استایل زرین‌کوچ و کلیدهای نمایش.
 * پیش‌فرض برای زمینه‌ی سرمه‌ای پاورقی تنظیم شده؛ همه‌چیز از تب استایل قابل تغییر است.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_About' ) ) :

	/**
	 * ویجت «پانوشت: درباره من».
	 */
	class ZC_Widget_Footer_About extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-about';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: درباره من', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-site-identity';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'درباره من', 'معرفی', 'شبکه‌های اجتماعی', 'اینستاگرام' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'fw_about_sec',
				array(
					'label' => __( 'درباره من', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_brand',
				array(
					'label'        => __( 'نام / لوگوی سایت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'لوگو از «سفارشی‌سازی ← هویت سایت» خوانده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'about',
				array(
					'label'       => __( 'متن معرفی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 4,
					'placeholder' => __( 'خالی = متن ثبت‌شده در «پنل قالب ← ساختار سایت ← پاورقی»', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_socials',
				array(
					'label'        => __( 'شبکه‌های اجتماعی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'نشانی‌ها از «پنل قالب ← اطلاعات و ارتباط ← شبکه‌های اجتماعی» خوانده می‌شود.', 'zarincoach' ),
					'separator'    => 'before',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * کلیدهای نمایش و استایل (کیت زرین‌کوچ).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'brand'   => array( __( 'نام / لوگوی سایت', 'zarincoach' ), '.zc-fw-brand' ),
					'about'   => array( __( 'متن معرفی', 'zarincoach' ), '.zc-footer-about-text' ),
					'socials' => array( __( 'شبکه‌های اجتماعی', 'zarincoach' ), '.zc-footer-socials' ),
				)
			);

			$this->zc_style(
				'fwab_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'about' => array( 'text', '.zc-footer-about-text', __( 'متن معرفی', 'zarincoach' ), array( 'align' => true, 'width' => true ) ),
					'brand' => array( 'text', '.zc-fw-brand', __( 'نام / لوگو', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwab_social',
				__( 'شبکه‌های اجتماعی', 'zarincoach' ),
				array(
					'soc' => array( 'icon', '.zc-footer-socials .zc-social', '', array( 'hover' => '.zc-footer-socials .zc-social' ) ),
					'gap' => array( 'size', '.zc-footer-socials', __( 'فاصله‌ی دکمه‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s     = $this->get_settings_for_display();
			$about = trim( (string) $this->value( 'about', 'footer_about', '' ) );

			echo '<div class="zc-fw zc-fw-about">';

			if ( $this->is_on( $s, 'show_brand' ) ) {
				echo '<div class="zc-fw-brand mb-5">';
				ob_start();
				zc_site_branding( true );
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی امن داخلی قالب.
				echo '</div>';
			}

			if ( '' !== $about ) {
				echo '<p class="zc-footer-about-text m-0 max-w-sm text-[0.9rem] leading-[2] text-white/70">' . esc_html( $about ) . '</p>';
			}

			if ( $this->is_on( $s, 'show_socials' ) ) {
				$socials = zc_socials();
				if ( ! empty( $socials ) ) {
					echo '<div class="zc-footer-socials mt-6 flex flex-wrap items-center gap-2">';
					foreach ( $socials as $item ) {
						echo '<a class="zc-social border-white/15 bg-white/5 text-white hover:border-primary hover:bg-primary hover:text-secondary" href="' . esc_url( $item['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $item['label'] ) . '">';
						zc_icon( $item['icon'], 'h-[18px] w-[18px]' );
						echo '</a>';
					}
					echo '</div>';
				}
			}

			echo '</div>';
		}
	}
endif;
