<?php
/**
 * ویجت پانوشت: نمادهای اعتماد
 *
 * نسخه‌ی فشرده‌ی نمادها برای پانوشت: ۱ تا ۶ نماد (اینماد، ساماندهی، زرین‌پال، پی…)،
 * طرح‌های نمایش متنوع و کیت استایل کامل — همان داده‌ی «نمادهای اعتماد» اصلی.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Trust' ) ) :

	/**
	 * ویجت «پانوشت: نمادهای اعتماد».
	 */
	class ZC_Widget_Footer_Trust extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-trust';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: نمادهای اعتماد', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-lock-user';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'اینماد', 'ساماندهی', 'زرین‌پال', 'نماد', 'اعتماد', 'enamad' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'fwtrust_sec',
				array(
					'label' => __( 'نمادها (۱ تا ۶)', 'zarincoach' ),
				)
			);

			$repeater = $this->trust_repeater();

			$this->add_control(
				'items',
				array(
					'label'       => __( 'نمادها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => zc_trust_default_items(),
					'title_field' => '{{{ name || label || type }}}',
					'max_items'   => 6,
				)
			);

			$this->add_control(
				'fallback',
				array(
					'label'        => __( 'طرح پیش‌فرض برای کدهای خالی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'تا زمان درج کد رسمی، کاشی خنثی نمایش داده شود. خاموش = نمادهای بدون کد پنهان می‌شوند.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'tstyle',
				array(
					'label'   => __( 'طرح نمایش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'footer'  => __( 'پانوشت (مینیمال روشن)', 'zarincoach' ),
						'cards'   => __( 'کارت‌ها', 'zarincoach' ),
						'navy'    => __( 'سرمه‌ای', 'zarincoach' ),
						'inline'  => __( 'خطی', 'zarincoach' ),
						'minimal' => __( 'ساده', 'zarincoach' ),
					),
					'default' => 'footer',
				)
			);

			$this->add_control(
				'columns',
				array(
					'label'   => __( 'تعداد ستون نمادها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'2' => '۲',
						'3' => '۳',
						'4' => '۴',
						'5' => '۵',
						'6' => '۶',
					),
					'default' => '4',
				)
			);

			$this->add_control(
				'align',
				array(
					'label'   => __( 'چینش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'start'  => array( 'title' => __( 'راست', 'zarincoach' ), 'icon' => 'eicon-text-align-right' ),
						'center' => array( 'title' => __( 'وسط', 'zarincoach' ), 'icon' => 'eicon-text-align-center' ),
						'end'    => array( 'title' => __( 'چپ', 'zarincoach' ), 'icon' => 'eicon-text-align-left' ),
					),
					'default' => 'center',
				)
			);

			$this->add_control(
				'captions',
				array(
					'label'        => __( 'عنوان زیر نمادها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
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
					'badges' => array( __( 'نمادها', 'zarincoach' ), '.zc-fw-trust .zc-trust' ),
					'label'  => array( __( 'عنوان زیر نماد', 'zarincoach' ), '.zc-fw-trust .zc-trust-label' ),
					'sub'    => array( __( 'توضیح زیر نماد', 'zarincoach' ), '.zc-fw-trust .zc-trust-sub' ),
				)
			);

			$this->zc_style(
				'fwtrust_item',
				__( 'نمادها', 'zarincoach' ),
				array(
					'item'  => array( 'box', '.zc-fw-trust .zc-trust-item', __( 'کارت نماد', 'zarincoach' ), array( 'hover' => true ) ),
					'seal'  => array( 'box', '.zc-fw-trust .zc-trust-seal', __( 'قاب نماد', 'zarincoach' ), array( 'gradient' => false ) ),
					'sealh' => array( 'size', '.zc-fw-trust .zc-trust-seal', __( 'ارتفاع قاب', 'zarincoach' ), array( 'prop' => 'height', 'max' => 220 ) ),
					'img'   => array( 'size', '.zc-fw-trust .zc-trust-seal img', __( 'حداکثر ارتفاع تصویر', 'zarincoach' ), array( 'prop' => 'max-height', 'max' => 160 ) ),
					'emb'   => array( 'icon', '.zc-fw-trust .zc-trust-emblem', __( 'نشان طرح پیش‌فرض', 'zarincoach' ) ),
					'gap'   => array( 'size', '.zc-fw-trust .zc-trust', __( 'فاصله‌ی نمادها', 'zarincoach' ), array( 'max' => 60, 'css' => '--g: {{SIZE}}{{UNIT}}; gap: {{SIZE}}{{UNIT}};' ) ),
				)
			);

			$this->zc_style(
				'fwtrust_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'label' => array( 'text', '.zc-fw-trust .zc-trust-label', __( 'عنوان زیر نماد', 'zarincoach' ), array( 'margin' => false ) ),
					'sub'   => array( 'text', '.zc-fw-trust .zc-trust-sub', __( 'توضیح زیر نماد', 'zarincoach' ), array( 'margin' => false ) ),
					'mark'  => array( 'text', '.zc-fw-trust .zc-trust-mark-name', __( 'نام طرح پیش‌فرض', 'zarincoach' ), array( 'margin' => false ) ),
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
			$items = isset( $s['items'] ) && is_array( $s['items'] ) ? $this->trust_items_from( $s['items'] ) : zc_trust_default_items();

			$html = zc_trust_badges_render(
				$items,
				array(
					'style'    => (string) ( isset( $s['tstyle'] ) ? $s['tstyle'] : 'footer' ),
					'columns'  => (int) ( isset( $s['columns'] ) ? $s['columns'] : 4 ),
					'align'    => (string) ( isset( $s['align'] ) ? $s['align'] : 'center' ),
					'captions' => $this->is_on( $s, 'captions' ),
					'fallback' => $this->is_on( $s, 'fallback' ),
					'class'    => 'zc-fw-trust-list',
				)
			);

			if ( '' === $html ) {
				if ( current_user_can( 'manage_options' ) ) {
					echo '<p class="zc-fw-trust-hint m-0 text-[0.78rem] text-white/50">' . esc_html__( '(فقط برای مدیر) کد نمادها را در همین ویجت یا «تنظیمات قالب ← اطلاعات حقوقی» وارد کنید.', 'zarincoach' ) . '</p>';
				}
				return;
			}

			echo '<div class="zc-fw zc-fw-trust">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی پاک‌سازی‌شده‌ی رندرکننده‌ی نمادها.
		}
	}
endif;
