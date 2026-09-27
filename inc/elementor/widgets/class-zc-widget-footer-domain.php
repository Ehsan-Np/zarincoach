<?php
/**
 * ویجت پانوشت: دامنه رسمی و مجوزها
 *
 * اعلان دامنه‌ی رسمی خدمات، هشدار اورژانس و شماره‌ی مجوز/پروانه —
 * هر ردیف دارای کلید نمایش و آیکون دلخواه؛ متن‌ها با جای‌نگهداشت پنل تنظیمات.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Domain' ) ) :

	/**
	 * ویجت «پانوشت: دامنه رسمی و مجوزها».
	 */
	class ZC_Widget_Footer_Domain extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-domain';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: دامنه رسمی و مجوزها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-global';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'دامنه رسمی', 'مجوز', 'پروانه', 'اورژانس', 'نظام روان‌شناسی' ) );
		}

		/**
		 * آیکون انتخابی با جایگزین داخلی.
		 *
		 * @param array  $icon     آیکون المنتور.
		 * @param string $fallback نام آیکون داخلی.
		 * @param string $class    کلاس اندازه.
		 * @return void
		 */
		private function fw_icon( $icon, $fallback, $class ) {
			if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				return;
			}
			zc_icon( $fallback, $class );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			/* ---------- دامنه رسمی ---------- */
			$this->start_controls_section(
				'fwdomain_sec',
				array(
					'label' => __( 'دامنه رسمی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_domain',
				array(
					'label'        => __( 'نمایش ردیف دامنه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'dlabel',
				array(
					'label'       => __( 'برچسب', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'دامنه رسمی:', 'zarincoach' ),
					'condition'   => array( 'show_domain' => 'yes' ),
				)
			);

			$this->add_control(
				'ddomain',
				array(
					'label'       => __( 'دامنه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = دامنه‌ی پنل (اطلاعات حقوقی)', 'zarincoach' ),
					'label_block' => true,
					'condition'   => array( 'show_domain' => 'yes' ),
				)
			);

			$this->add_control(
				'durl',
				array(
					'label'       => __( 'پیوند دامنه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => 'https://',
					'description' => __( 'خالی = پیوند پنل تنظیمات.', 'zarincoach' ),
					'condition'   => array( 'show_domain' => 'yes' ),
				)
			);

			$this->add_control(
				'dnote',
				array(
					'label'       => __( 'یادداشت پس از دامنه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = یادداشت پنل (اطلاعات حقوقی)', 'zarincoach' ),
					'label_block' => true,
					'condition'   => array( 'show_domain' => 'yes' ),
				)
			);

			$this->add_control(
				'dicon',
				array(
					'label'       => __( 'آیکن دامنه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = آیکن کره‌ی زمین قالب.', 'zarincoach' ),
					'condition'   => array( 'show_domain' => 'yes' ),
				)
			);

			$this->end_controls_section();

			/* ---------- هشدار اورژانس ---------- */
			$this->start_controls_section(
				'fwemerg_sec',
				array(
					'label' => __( 'هشدار اورژانس', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_emergency',
				array(
					'label'        => __( 'نمایش هشدار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'متن پیش‌فرض: راه‌های کمک در شرایط بحرانی (پنل ← اطلاعات حقوقی).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'etext',
				array(
					'label'       => __( 'متن هشدار', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 3,
					'placeholder' => __( 'خالی = متن پنل تنظیمات', 'zarincoach' ),
					'condition'   => array( 'show_emergency' => 'yes' ),
				)
			);

			$this->add_control(
				'eicon',
				array(
					'label'       => __( 'آیکن هشدار', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = آیکن هشدار قالب.', 'zarincoach' ),
					'condition'   => array( 'show_emergency' => 'yes' ),
				)
			);

			$this->end_controls_section();

			/* ---------- مجوزها ---------- */
			$this->start_controls_section(
				'fwlic_sec',
				array(
					'label' => __( 'مجوزها', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_license',
				array(
					'label'        => __( 'نمایش مجوزها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'شماره‌ی پروانه‌ی نظام روان‌شناسی و کد پروانه از «پنل قالب ← اطلاعات حقوقی» خوانده می‌شود.', 'zarincoach' ),
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
					'card'    => array( __( 'کارت کلی', 'zarincoach' ), '.zc-fw-domain-card' ),
					'domain'  => array( __( 'ردیف دامنه', 'zarincoach' ), '.zc-fw-domain-main' ),
					'dicon'   => array( __( 'آیکن دامنه', 'zarincoach' ), '.zc-fw-domain-ic' ),
					'emerg'   => array( __( 'هشدار اورژانس', 'zarincoach' ), '.zc-fw-emergency' ),
					'eicon'   => array( __( 'آیکن هشدار', 'zarincoach' ), '.zc-fw-emergency-ic' ),
					'license' => array( __( 'نشان‌های مجوز', 'zarincoach' ), '.zc-fw-licenses' ),
				)
			);

			$this->zc_style(
				'fwdomain_box',
				__( 'کارت و متن‌ها', 'zarincoach' ),
				array(
					'card'  => array( 'box', '.zc-fw-domain-card', __( 'کارت', 'zarincoach' ), array( 'gradient' => true, 'text' => true ) ),
					'main'  => array( 'text', '.zc-fw-domain-main', __( 'متن دامنه', 'zarincoach' ), array( 'margin' => false ) ),
					'link'  => array( 'text', '.zc-fw-domain-link', __( 'پیوند دامنه', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'emer'  => array( 'text', '.zc-fw-emergency', __( 'هشدار اورژانس', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwdomain_lic',
				__( 'مجوزها و آیکن‌ها', 'zarincoach' ),
				array(
					'chip'  => array( 'box', '.zc-footer-license', __( 'نشان مجوز', 'zarincoach' ), array( 'gradient' => false ) ),
					'chipt' => array( 'text', '.zc-footer-license', __( 'متن نشان', 'zarincoach' ), array( 'margin' => false ) ),
					'dic'   => array( 'icon', '.zc-fw-domain-ic', '' ),
					'eic'   => array( 'icon', '.zc-fw-emergency-ic', '' ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s          = $this->get_settings_for_display();
			$has_domain = $this->is_on( $s, 'show_domain' );
			$has_emerg  = $this->is_on( $s, 'show_emergency' ) && '' !== trim( (string) $this->value( 'etext', 'legal_emergency', '' ) );
			$has_lic    = $this->is_on( $s, 'show_license' ) && '' !== (string) zc_legal_info( 'license' );

			if ( ! $has_domain && ! $has_emerg && ! $has_lic ) {
				return;
			}

			$domain = '' !== trim( (string) ( isset( $s['ddomain'] ) ? $s['ddomain'] : '' ) ) ? (string) $s['ddomain'] : (string) zc_legal_info( 'domain' );
			$durl   = isset( $s['durl']['url'] ) && '' !== trim( (string) $s['durl']['url'] ) ? (string) $s['durl']['url'] : (string) zc_legal_info( 'domain_url' );
			$dnote  = '' !== trim( (string) ( isset( $s['dnote'] ) ? $s['dnote'] : '' ) ) ? (string) $s['dnote'] : (string) zc_legal_info( 'domain_note' );

			echo '<div class="zc-fw zc-fw-domain">';
			echo '<div class="zc-fw-domain-card zc-footer-domain-card grid gap-3 rounded-3xl border border-white/10 bg-white/[0.04] p-5 sm:p-6">';

			if ( $has_domain && '' !== $domain ) {
				echo '<p class="zc-fw-domain-main m-0 flex items-start gap-3 text-[0.88rem] leading-[2] text-white/80">';
				echo '<span class="zc-fw-domain-ic zc-fw-ic mt-1 shrink-0 text-accent h-5 w-5 inline-flex items-center justify-center">';
				$this->fw_icon( isset( $s['dicon'] ) ? $s['dicon'] : array(), 'globe', 'h-5 w-5' );
				echo '</span><span>';
				echo '<strong class="text-white">' . esc_html( (string) ( isset( $s['dlabel'] ) ? $s['dlabel'] : __( 'دامنه رسمی:', 'zarincoach' ) ) ) . '</strong> ';
				echo '<a class="zc-fw-domain-link font-bold text-accent hover:underline" href="' . esc_url( $durl ) . '" dir="ltr">' . esc_html( $domain ) . '</a>';
				if ( '' !== trim( $dnote ) ) {
					echo ' — ' . esc_html( $dnote );
				}
				echo '</span></p>';
			}

			if ( $has_emerg ) {
				echo '<p class="zc-fw-emergency m-0 flex items-start gap-3 text-[0.82rem] leading-[2] text-white/60">';
				echo '<span class="zc-fw-emergency-ic zc-fw-ic mt-1 shrink-0 text-amber-300 h-[18px] w-[18px] inline-flex items-center justify-center">';
				$this->fw_icon( isset( $s['eicon'] ) ? $s['eicon'] : array(), 'alert', 'h-[18px] w-[18px]' );
				echo '</span><span>' . esc_html( (string) $this->value( 'etext', 'legal_emergency', '' ) ) . '</span></p>';
			}

			if ( $has_lic ) {
				echo '<div class="zc-fw-licenses mt-1 flex flex-wrap gap-2.5">';
				echo '<span class="zc-footer-license"><span class="zc-footer-license-icon zc-fw-ic h-4 w-4 inline-flex items-center justify-center">';
				zc_icon( 'award', 'h-4 w-4' );
				echo '</span><span>' . esc_html( (string) zc_legal_info( 'license_label' ) ) . ': <strong>' . esc_html( (string) zc_legal_info( 'license' ) ) . '</strong></span></span>';
				if ( '' !== (string) zc_legal_info( 'pco_code' ) ) {
					echo '<span class="zc-footer-license"><span class="zc-footer-license-icon zc-fw-ic h-4 w-4 inline-flex items-center justify-center">';
					zc_icon( 'id-card', 'h-4 w-4' );
					echo '</span><span>' . esc_html( (string) zc_legal_info( 'pco_label' ) ) . ': <strong>' . esc_html( (string) zc_legal_info( 'pco_code' ) ) . '</strong></span></span>';
				}
				echo '</div>';
			}

			echo '</div></div>';
		}
	}
endif;
