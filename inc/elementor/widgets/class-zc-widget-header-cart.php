<?php
/**
 * ویجت هدر: حساب کاربری و سبد خرید (ووکامرس)
 *
 * دکمه‌ی حساب کاربری و سبد خرید با نشان تعداد — فقط وقتی ووکامرس فعال است ثبت می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Cart' ) ) :

	/**
	 * ویجت «هدر: حساب کاربری و سبد خرید».
	 */
	class ZC_Widget_Header_Cart extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-cart';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: حساب و سبد خرید', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-cart-medium';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'سبد خرید', 'حساب کاربری', 'ووکامرس', 'cart', 'shop' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hcart_sec',
				array(
					'label' => __( 'حساب و سبد خرید', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_account',
				array(
					'label'        => __( 'حساب کاربری (ورود/عضویت)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_cart',
				array(
					'label'        => __( 'سبد خرید (با نشان تعداد)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'اگر مینی‌کارت در پنل فعال باشد، با کلیک باز می‌شود؛ وگرنه به صفحه‌ی سبد می‌رود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'hide_mobile',
				array(
					'label'        => __( 'پنهان در موبایل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * استایل (کیت زرین‌کوچ).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_style(
				'hcart_style',
				__( 'دکمه‌ها', 'zarincoach' ),
				array(
					'btn'   => array( 'icon', '.zc-hdr-cart-wrap .zc-btn-icon', __( 'دکمه‌ها', 'zarincoach' ) ),
					'badge' => array( 'text', '.zc-hdr-cart-wrap .zc-cart-count', __( 'نشان تعداد', 'zarincoach' ), array( 'margin' => false ) ),
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
			$hide  = $this->is_on( $s, 'hide_mobile' ) ? ' hidden sm:inline-flex' : '';
			$html  = zc_wc_header_tools(
				array(
					'show_cart'    => $this->is_on( $s, 'show_cart' ),
					'show_account' => $this->is_on( $s, 'show_account' ),
				)
			);
			$html  = trim( (string) $html );
			if ( '' === $html ) {
				return;
			}

			echo '<div class="zc-hdr-cart-wrap inline-flex items-center gap-2' . esc_attr( $hide ) . '">';
			echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی escape‌شده‌ی درون تابع.
			echo '</div>';
		}
	}
endif;
