<?php
/**
 * ویجت پانوشت: کپی‌رایت
 *
 * نوار پایانی پانوشت: خط جداکننده، کپی‌رایت با سال و نام سایت و اعتبار طراح —
 * همه با کلید نمایش و کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Copyright' ) ) :

	/**
	 * ویجت «پانوشت: کپی‌رایت».
	 */
	class ZC_Widget_Footer_Copyright extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-copyright';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: کپی‌رایت', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-footer';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'کپی‌رایت', 'حقوق', 'کپی رایت', 'طراح' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			/* ---------- کپی‌رایت ---------- */
			$this->start_controls_section(
				'fwcopy_sec',
				array(
					'label' => __( 'کپی‌رایت', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_rule',
				array(
					'label'        => __( 'خط جداکننده‌ی بالا', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_year',
				array(
					'label'        => __( 'نمایش سال', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_sitename',
				array(
					'label'        => __( 'نمایش نام سایت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'copy',
				array(
					'label'       => __( 'متن کپی‌رایت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 2,
					'placeholder' => __( 'خالی = متن پنل قالب (پاورقی)', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			/* ---------- اعتبار طراح ---------- */
			$this->start_controls_section(
				'fwcredit_sec',
				array(
					'label' => __( 'اعتبار طراح', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_credit',
				array(
					'label'        => __( 'نمایش اعتبار طراح', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'credit_label',
				array(
					'label'       => __( 'برچسب', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'طراحی و توسعه:', 'zarincoach' ),
					'condition'   => array( 'show_credit' => 'yes' ),
				)
			);

			$this->add_control(
				'credit_name',
				array(
					'label'       => __( 'نام طراح', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'زرین‌کد', 'zarincoach' ),
					'condition'   => array( 'show_credit' => 'yes' ),
				)
			);

			$this->add_control(
				'credit_url',
				array(
					'label'       => __( 'پیوند طراح', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'default'     => array( 'url' => 'https://zarincode.com' ),
					'placeholder' => 'https://',
					'description' => __( 'خالی = نام طراح بدون پیوند.', 'zarincoach' ),
					'condition'   => array( 'show_credit' => 'yes' ),
					'separator'   => 'before',
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
					'rule'   => array( __( 'خط جداکننده', 'zarincoach' ), '.zc-fw-copy .zc-rule' ),
					'copy'   => array( __( 'متن کپی‌رایت', 'zarincoach' ), '.zc-footer-copy' ),
					'credit' => array( __( 'اعتبار طراح', 'zarincoach' ), '.zc-fw-credit' ),
				)
			);

			$this->zc_style(
				'fwcopy_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'bar'   => array( 'text', '.zc-footer-bottom', __( 'نوار پایین', 'zarincoach' ), array( 'align' => true ) ),
					'copy'  => array( 'text', '.zc-footer-copy', __( 'متن کپی‌رایت', 'zarincoach' ), array( 'margin' => false ) ),
					'link'  => array( 'text', '.zc-footer-bottom a', __( 'پیوند طراح', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwcopy_layout',
				__( 'چیدمان', 'zarincoach' ),
				array(
					'rule'  => array( 'color', '.zc-fw-copy .zc-rule', __( 'رنگ خط جداکننده', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'gap'   => array( 'size', '.zc-footer-bottom', __( 'فاصله‌ی دو طرف نوار', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 60 ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s    = $this->get_settings_for_display();
			$copy = trim( (string) $this->value( 'copy', 'footer_copy', __( 'کلیه حقوق این وب‌سایت محفوظ است.', 'zarincoach' ) ) );
			$credit_name = trim( (string) ( isset( $s['credit_name'] ) ? $s['credit_name'] : '' ) );

			$has_copy   = '' !== $copy;
			$has_credit = $this->is_on( $s, 'show_credit' ) && '' !== $credit_name;

			if ( ! $this->is_on( $s, 'show_rule' ) && ! $has_copy && ! $has_credit ) {
				return;
			}

			echo '<div class="zc-fw zc-fw-copy">';

			if ( $this->is_on( $s, 'show_rule' ) ) {
				echo '<div class="zc-rule my-8 opacity-20"></div>';
			}

			echo '<div class="zc-footer-bottom flex flex-col items-center justify-between gap-4 text-[0.8rem] text-white/60 sm:flex-row">';

			if ( '' !== $copy ) {
				echo '<p class="zc-footer-copy m-0">';
				if ( $this->is_on( $s, 'show_year' ) ) {
					echo esc_html( gmdate( 'Y' ) ) . ' ';
				}
				if ( $this->is_on( $s, 'show_sitename' ) ) {
					echo esc_html( get_bloginfo( 'name' ) ) . ' — ';
				}
				echo esc_html( $copy );
				echo '</p>';
			}

			if ( $has_credit ) {
				$credit_url = isset( $s['credit_url']['url'] ) ? trim( (string) $s['credit_url']['url'] ) : '';
				echo '<p class="zc-fw-credit m-0 inline-flex items-center gap-1.5">';
				echo esc_html( (string) ( isset( $s['credit_label'] ) ? $s['credit_label'] : __( 'طراحی و توسعه:', 'zarincoach' ) ) ) . ' ';
				if ( '' !== $credit_url ) {
					echo '<a class="font-bold text-white/90 transition hover:text-accent" href="' . esc_url( $credit_url ) . '" target="_blank" rel="noopener">' . esc_html( $credit_name ) . '</a>';
				} else {
					echo '<strong>' . esc_html( $credit_name ) . '</strong>';
				}
				echo '</p>';
			}

			echo '</div></div>';
		}
	}
endif;
