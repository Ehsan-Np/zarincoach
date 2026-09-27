<?php
/**
 * ویجت هدر: دکمه‌ی فراخوان و تلفن
 *
 * دکمه‌ی CTA با متن، پیوند و آیکون دلخواه + چیپ تلفن اختیاری — استایل کامل
 * دکمه از بخش خودکار «دکمه» در کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Cta' ) ) :

	/**
	 * ویجت «هدر: دکمه‌ی فراخوان و تلفن».
	 */
	class ZC_Widget_Header_Cta extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-cta';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: دکمه‌ی فراخوان و تلفن', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-button';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'دکمه', 'رزرو', 'فراخوان', 'تلفن', 'cta' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hcta_sec',
				array(
					'label' => __( 'دکمه‌ی فراخوان', 'zarincoach' ),
				)
			);

			// button_text + button_url → بخش خودکار «دکمه» در کیت استایل هم فعال می‌شود.
			$this->button_controls( '', __( 'دکمه', 'zarincoach' ) );

			$this->add_control(
				'btn_size',
				array(
					'label'   => __( 'اندازه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'zc-btn-sm' => __( 'کوچک', 'zarincoach' ),
						''          => __( 'معمولی', 'zarincoach' ),
						'zc-btn-lg' => __( 'بزرگ', 'zarincoach' ),
					),
					'default' => 'zc-btn-sm',
				)
			);

			$this->add_control(
				'hide_mobile',
				array(
					'label'        => __( 'پنهان در موبایل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_phone',
				array(
					'label'        => __( 'چیپ تلفن کنار دکمه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'phone',
				array(
					'label'       => __( 'شماره تلفن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = تلفن رزرو پنل', 'zarincoach' ),
					'condition'   => array( 'show_phone' => 'yes' ),
				)
			);

			$this->add_control(
				'phone_label',
				array(
					'label'       => __( 'برچسب تلفن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = برچسب پنل', 'zarincoach' ),
					'condition'   => array( 'show_phone' => 'yes' ),
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
					'icon'  => array( __( 'آیکن دکمه', 'zarincoach' ), '.zc-hdr-cta .zc-btn-icon' ),
					'phone' => array( __( 'چیپ تلفن', 'zarincoach' ), '.zc-hdr-cta-phone' ),
					'pic'   => array( __( 'آیکن تلفن', 'zarincoach' ), '.zc-hdr-cta-phone .zc-fw-ic' ),
				)
			);

			$this->zc_style(
				'hcta_style',
				__( 'دکمه و تلفن', 'zarincoach' ),
				array(
					'phone' => array( 'text', '.zc-hdr-cta-phone', __( 'متن تلفن', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'gap'   => array( 'size', '.zc-hdr-cta', __( 'فاصله‌ی دکمه و تلفن', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
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
			$hide  = $this->is_on( $s, 'hide_mobile' ) ? ' hidden lg:inline-flex' : '';
			$size  = (string) ( isset( $s['btn_size'] ) ? $s['btn_size'] : 'zc-btn-sm' );

			echo '<div class="zc-hdr-cta inline-flex items-center' . esc_attr( $hide ) . '">';

			ob_start();
			$this->render_button( '', $size );
			$btn = trim( (string) ob_get_clean() );
			if ( '' !== $btn ) {
				echo $btn; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی امن متد قالب.
			}

			if ( $this->is_on( $s, 'show_phone' ) ) {
				$phone = trim( (string) $this->value( 'phone', 'contact_phone', '' ) );
				if ( '' !== $phone ) {
					$label = trim( (string) $this->value( 'phone_label', 'contact_phone_label', __( 'تلفن رزرو نوبت', 'zarincoach' ) ) );
					echo '<a class="zc-hdr-cta-phone inline-flex items-center gap-2 text-[0.8rem] text-muted transition hover:text-primary" href="tel:' . esc_attr( zc_normalize_phone( $phone ) ) . '">';
					echo '<span class="zc-fw-ic h-4 w-4 inline-flex items-center justify-center text-primary">';
					zc_icon( 'phone', 'h-4 w-4' );
					echo '</span><span>';
					if ( '' !== $label ) {
						echo '<span class="block text-[0.68rem] text-muted/80">' . esc_html( $label ) . '</span>';
					}
					echo '<span dir="ltr" class="font-bold">' . esc_html( zc_digits_to_persian( $phone ) ) . '</span></span></a>';
				}
			}

			echo '</div>';
		}
	}
endif;
