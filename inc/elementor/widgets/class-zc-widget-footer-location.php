<?php
/**
 * ویجت پانوشت: لوکیشن و راه‌های ارتباط
 *
 * تلفن‌ها، ایمیل، نشانی، ساعات کاری و پیوند نقشه — هر ردیف دارای کلید نمایش،
 * مقدار و برچسب مستقل (خالی = پنل تنظیمات). همراه با کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Location' ) ) :

	/**
	 * ویجت «پانوشت: لوکیشن و راه‌های ارتباط».
	 */
	class ZC_Widget_Footer_Location extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-location';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: لوکیشن و راه‌های ارتباط', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-google-maps';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'لوکیشن', 'نشانی', 'تلفن', 'تماس', 'نقشه', 'ساعات کاری' ) );
		}

		/**
		 * یک کنترل ردیف (کلید نمایش + مقدار + برچسب).
		 *
		 * @param string $key    کلید پایه.
		 * @param string $label  برچسب کلید نمایش.
		 * @param string $ph     جای‌نگهداشت مقدار.
		 * @param bool   $labeled آیا برچسب جدا دارد (تلفن‌ها).
		 * @return void
		 */
		private function row_controls( $key, $label, $ph, $labeled = false ) {
			$this->add_control(
				'show_' . $key,
				array(
					'label'        => $label,
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$value_args = array(
				'label'       => sprintf( /* translators: %s: نام ردیف */ __( 'مقدار %s', 'zarincoach' ), $label ),
				'type'        => \Elementor\Controls_Manager::TEXT,
				'placeholder' => $ph,
				'condition'   => array( 'show_' . $key => 'yes' ),
			);
			if ( 'email' === $key ) {
				$value_args['label_block'] = true;
			}
			$this->add_control( $key, $value_args );

			if ( $labeled ) {
				$this->add_control(
					$key . '_label',
					array(
						'label'       => sprintf( /* translators: %s: نام ردیف */ __( 'برچسب %s', 'zarincoach' ), $label ),
						'type'        => \Elementor\Controls_Manager::TEXT,
						'placeholder' => __( 'خالی = برچسب پنل تنظیمات', 'zarincoach' ),
						'condition'   => array( 'show_' . $key => 'yes' ),
					)
				);
			}
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			/* ---------- عنوان ---------- */
			$this->start_controls_section(
				'fwloc_title_sec',
				array(
					'label' => __( 'عنوان', 'zarincoach' ),
				)
			);

			$this->add_control(
				'ftitle',
				array(
					'label'       => __( 'عنوان ستون', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'راه‌های ارتباط', 'zarincoach' ),
					'description' => __( 'برای حذف عنوان، خالی بگذارید.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'ftag',
				array(
					'label'   => __( 'تگ عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'p'  => 'P',
					),
					'default' => 'h4',
				)
			);

			$this->end_controls_section();

			/* ---------- ردیف‌ها ---------- */
			$this->start_controls_section(
				'fwloc_rows_sec',
				array(
					'label' => __( 'ردیف‌های ارتباط', 'zarincoach' ),
				)
			);

			$this->add_control(
				'fwloc_note',
				array(
					'type'            => \Elementor\Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'مقادیر خالی از «پنل قالب ← اطلاعات و ارتباط ← اطلاعات تماس» خوانده می‌شوند.', 'zarincoach' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->row_controls( 'phone', __( 'تلفن اول', 'zarincoach' ), __( 'خالی = تلفن رزرو پنل', 'zarincoach' ), true );
			$this->row_controls( 'phone2', __( 'تلفن دوم', 'zarincoach' ), __( 'خالی = تلفن مطب پنل', 'zarincoach' ), true );
			$this->row_controls( 'email', __( 'ایمیل', 'zarincoach' ), __( 'خالی = ایمیل پنل', 'zarincoach' ) );
			$this->row_controls( 'address', __( 'نشانی', 'zarincoach' ), __( 'خالی = نشانی پنل', 'zarincoach' ) );
			$this->row_controls( 'hours', __( 'ساعات کاری', 'zarincoach' ), __( 'خالی = ساعات پنل', 'zarincoach' ) );

			$this->end_controls_section();

			/* ---------- نقشه ---------- */
			$this->start_controls_section(
				'fwloc_map_sec',
				array(
					'label' => __( 'پیوند نقشه', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_map',
				array(
					'label'        => __( 'پیوند «مشاهده روی نقشه»', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'map_label',
				array(
					'label'       => __( 'برچسب پیوند', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'مشاهده روی نقشه', 'zarincoach' ),
					'condition'   => array( 'show_map' => 'yes' ),
				)
			);

			$this->add_control(
				'map_url',
				array(
					'label'       => __( 'نشانی نقشه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => 'https://',
					'description' => __( 'خالی = جستجوی خودکار نشانی در گوگل‌مپ.', 'zarincoach' ),
					'condition'   => array( 'show_map' => 'yes' ),
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
					'title'   => array( __( 'عنوان ستون', 'zarincoach' ), '.zc-footer-title' ),
					'phone'   => array( __( 'ردیف تلفن اول', 'zarincoach' ), '.zc-fw-li-phone' ),
					'phone2'  => array( __( 'ردیف تلفن دوم', 'zarincoach' ), '.zc-fw-li-phone2' ),
					'email'   => array( __( 'ردیف ایمیل', 'zarincoach' ), '.zc-fw-li-email' ),
					'address' => array( __( 'ردیف نشانی', 'zarincoach' ), '.zc-fw-li-address' ),
					'hours'   => array( __( 'ردیف ساعات کاری', 'zarincoach' ), '.zc-fw-li-hours' ),
					'map'     => array( __( 'پیوند نقشه', 'zarincoach' ), '.zc-fw-map' ),
					'icons'   => array( __( 'آیکن‌های ردیف‌ها', 'zarincoach' ), '.zc-footer-contact-list .zc-fw-ic' ),
				)
			);

			$this->zc_style(
				'fwloc_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-footer-title', __( 'عنوان ستون', 'zarincoach' ), array( 'margin' => false ) ),
					'list'  => array( 'text', '.zc-footer-contact-list', __( 'متن ردیف‌ها', 'zarincoach' ), array( 'align' => true ) ),
					'link'  => array( 'text', '.zc-footer-contact-list a', __( 'پیوندها', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'map'   => array( 'text', '.zc-fw-map a', __( 'پیوند نقشه', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwloc_icon',
				__( 'آیکن‌ها', 'zarincoach' ),
				array(
					'ic'  => array( 'icon', '.zc-footer-contact-list .zc-fw-ic', '', array( 'hover' => '.zc-footer-contact-list li' ) ),
					'gap' => array( 'size', '.zc-footer-contact-list', __( 'فاصله‌ی ردیف‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s       = $this->get_settings_for_display();
			$contact = zc_contact_fields();

			$rows = array(
				'phone'   => array(
					'on'    => $this->is_on( $s, 'show_phone' ),
					'value' => (string) $this->value( 'phone', 'contact_phone', '' ),
					'label' => (string) $this->value( 'phone_label', 'contact_phone_label', __( 'تلفن رزرو نوبت', 'zarincoach' ) ),
					'icon'  => 'phone',
				),
				'phone2'  => array(
					'on'    => $this->is_on( $s, 'show_phone2' ),
					'value' => (string) $this->value( 'phone2', 'contact_mobile', '' ),
					'label' => (string) $this->value( 'phone2_label', 'contact_mobile_label', __( 'تلفن مطب', 'zarincoach' ) ),
					'icon'  => 'phone',
				),
				'email'   => array(
					'on'    => $this->is_on( $s, 'show_email' ),
					'value' => (string) $this->value( 'email', 'contact_email', '' ),
					'icon'  => 'mail',
				),
				'address' => array(
					'on'    => $this->is_on( $s, 'show_address' ),
					'value' => (string) $this->value( 'address', 'contact_address', '' ),
					'icon'  => 'map-pin',
				),
				'hours'   => array(
					'on'    => $this->is_on( $s, 'show_hours' ),
					'value' => (string) $this->value( 'hours', 'contact_hours', '' ),
					'icon'  => 'clock',
				),
			);

			$has_rows = false;
			foreach ( $rows as $row ) {
				if ( $row['on'] && '' !== trim( $row['value'] ) ) {
					$has_rows = true;
					break;
				}
			}
			$has_map = $this->is_on( $s, 'show_map' ) && '' !== trim( (string) ( isset( $s['map_label'] ) ? $s['map_label'] : '' ) );
			$ftitle  = trim( (string) ( isset( $s['ftitle'] ) ? $s['ftitle'] : '' ) );
			if ( ! $has_rows && ! $has_map && '' === $ftitle ) {
				return;
			}

			$tag = in_array( (string) ( isset( $s['ftag'] ) ? $s['ftag'] : 'h4' ), array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ), true ) ? (string) $s['ftag'] : 'h4';

			echo '<div class="zc-fw zc-fw-location">';

			if ( '' !== $ftitle ) {
				printf(
					'<%1$s class="zc-footer-title zc-h-sm mb-4 text-[0.95rem] font-bold text-white">%2$s</%1$s>',
					esc_html( $tag ),
					esc_html( $ftitle )
				);
			}

			echo '<ul class="zc-footer-contact-list zc-fw-list grid gap-3 text-[0.88rem] text-white/70">';

			foreach ( $rows as $key => $row ) {
				if ( ! $row['on'] || '' === trim( $row['value'] ) ) {
					continue;
				}
				echo '<li class="zc-fw-li-' . esc_attr( $key ) . ' flex items-start gap-3">';
				echo '<span class="zc-fw-ic mt-0.5 shrink-0 text-accent h-4 w-4 inline-flex items-center justify-center">';
				zc_icon( $row['icon'], 'h-4 w-4' );
				echo '</span>';

				if ( 'phone' === $key || 'phone2' === $key ) {
					echo '<a class="transition hover:text-accent" href="tel:' . esc_attr( zc_normalize_phone( $row['value'] ) ) . '">';
					if ( '' !== trim( $row['label'] ) ) {
						echo '<span class="block text-[0.72rem] text-white/50">' . esc_html( $row['label'] ) . '</span>';
					}
					echo '<span dir="ltr">' . esc_html( zc_digits_to_persian( $row['value'] ) ) . '</span></a>';
				} elseif ( 'email' === $key ) {
					echo '<a class="transition hover:text-accent" href="mailto:' . esc_attr( $row['value'] ) . '" dir="ltr">' . esc_html( $row['value'] ) . '</a>';
				} else {
					echo '<span>' . esc_html( $row['value'] ) . '</span>';
				}

				echo '</li>';
			}

			if ( $has_map ) {
				$map_url = isset( $s['map_url']['url'] ) ? trim( (string) $s['map_url']['url'] ) : '';
				if ( '' === $map_url ) {
					$map_url = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode( '' !== trim( (string) $rows['address']['value'] ) ? $rows['address']['value'] : get_bloginfo( 'name' ) );
				}
				echo '<li class="zc-fw-map flex items-start gap-3">';
				echo '<span class="zc-fw-ic mt-0.5 shrink-0 text-accent h-4 w-4 inline-flex items-center justify-center">';
				zc_icon( 'map-pin', 'h-4 w-4' );
				echo '</span>';
				echo '<a class="transition hover:text-accent" href="' . esc_url( $map_url ) . '" target="_blank" rel="noopener">' . esc_html( (string) $s['map_label'] ) . '</a>';
				echo '</li>';
			}

			echo '</ul></div>';
		}
	}
endif;
