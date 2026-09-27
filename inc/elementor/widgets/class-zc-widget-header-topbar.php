<?php
/**
 * ویجت هدر: نوار اطلاع‌رسانی (تاپ‌بار)
 *
 * آیکن + متن/پیوند در یک سمت، تلفن و شبکه‌های اجتماعی در سمت دیگر —
 * هر جزء دارای کلید نمایش، آیکون دلخواه و کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Topbar' ) ) :

	/**
	 * ویجت «هدر: نوار اطلاع‌رسانی».
	 */
	class ZC_Widget_Header_Topbar extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-topbar';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: نوار اطلاع‌رسانی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-header';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'سربرگ', 'تاپ‌بار', 'نوار بالا', 'اطلاع‌رسانی', 'topbar' ) );
		}

		/**
		 * آیکون انتخابی با جایگزین داخلی.
		 *
		 * @param array|string $icon     آیکون.
		 * @param string       $fallback نام آیکون داخلی.
		 * @param string       $class    کلاس اندازه.
		 * @return void
		 */
		private function hdr_icon( $icon, $fallback, $class ) {
			if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				return;
			}
			zc_icon( is_string( $icon ) && '' !== $icon ? $icon : $fallback, $class );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hb_sec',
				array(
					'label' => __( 'نوار اطلاع‌رسانی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'hicon',
				array(
					'label'       => __( 'آیکن متن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = آیکن ستاره‌ی قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'text',
				array(
					'label'       => __( 'متن نوار', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 2,
					'placeholder' => __( 'خالی = متن پنل (سربرگ)', 'zarincoach' ),
					'label_block' => true,
				)
			);

			$this->add_control(
				'link',
				array(
					'label'       => __( 'پیوند متن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'خالی = پیوند پنل', 'zarincoach' ),
					'description' => __( 'برای غیرلینک‌کردن متن، پاکش کنید.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'mobile',
				array(
					'label'        => __( 'نمایش در موبایل و تبلت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'به‌صورت پیش‌فرض نوار فقط در دسکتاپ نمایش داده می‌شود.', 'zarincoach' ),
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'show_phone',
				array(
					'label'        => __( 'تلفن رزرو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'phone',
				array(
					'label'       => __( 'شماره تلفن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = تلفن پنل تنظیمات', 'zarincoach' ),
					'condition'   => array( 'show_phone' => 'yes' ),
				)
			);

			$this->add_control(
				'show_socials',
				array(
					'label'        => __( 'شبکه‌های اجتماعی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'socials_count',
				array(
					'label'     => __( 'تعداد شبکه‌ها', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 6,
					'default'   => 4,
					'condition' => array( 'show_socials' => 'yes' ),
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
					'icon'    => array( __( 'آیکن متن', 'zarincoach' ), '.zc-hdr-topbar-ic' ),
					'text'    => array( __( 'متن نوار', 'zarincoach' ), '.zc-hdr-topbar-text' ),
					'phone'   => array( __( 'تلفن', 'zarincoach' ), '.zc-hdr-topbar-phone' ),
					'socials' => array( __( 'شبکه‌های اجتماعی', 'zarincoach' ), '.zc-hdr-topbar-socials' ),
				)
			);

			$this->zc_style(
				'hb_box',
				__( 'نوار', 'zarincoach' ),
				array(
					'bar'    => array( 'box', '.zc-hdr-topbar', __( 'زمینه‌ی نوار', 'zarincoach' ), array( 'gradient' => true, 'text' => true ) ),
					'height' => array( 'size', '.zc-hdr-topbar .zc-hdr-tb-in', __( 'ارتفاع نوار', 'zarincoach' ), array( 'prop' => 'height', 'max' => 80 ) ),
					'text'   => array( 'text', '.zc-hdr-topbar-text', __( 'متن', 'zarincoach' ), array( 'margin' => false ) ),
					'phone'  => array( 'text', '.zc-hdr-topbar-phone', __( 'تلفن', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'hb_icons',
				__( 'آیکن‌ها', 'zarincoach' ),
				array(
					'ic'  => array( 'icon', '.zc-hdr-topbar-ic', __( 'آیکن متن', 'zarincoach' ) ),
					'pic' => array( 'color', '.zc-hdr-topbar-phone svg, .zc-hdr-topbar-phone i', __( 'آیکن تلفن', 'zarincoach' ), array( 'prop' => 'color' ) ),
					'soc' => array( 'icon', '.zc-hdr-topbar-socials .zc-social', __( 'آیکن شبکه‌ها', 'zarincoach' ), array( 'hover' => '.zc-hdr-topbar-socials' ) ),
					'gap' => array( 'size', '.zc-hdr-topbar-socials', __( 'فاصله‌ی شبکه‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
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
			$hidden  = $this->is_on( $s, 'mobile' ) ? '' : 'hidden lg:block';
			$text    = trim( (string) $this->value( 'text', 'header_topbar_text', __( 'نوبت‌دهی: شنبه تا چهارشنبه ۱۶ تا ۲۰:۳۰ — حضوری در بوشهر و آنلاین سراسری', 'zarincoach' ) ) );
			$link    = isset( $s['link']['url'] ) ? trim( (string) $s['link']['url'] ) : '';
			if ( '' === $link ) {
				$link = (string) zc_opt( 'header_topbar_link', '#booking' );
				$link = function_exists( 'zc_url' ) ? zc_url( $link ) : $link;
			}

			$phone_on = $this->is_on( $s, 'show_phone' );
			$phone    = trim( (string) $this->value( 'phone', 'contact_phone', '' ) );

			$soc_on  = $this->is_on( $s, 'show_socials' );
			$soc_n   = max( 1, min( 6, (int) ( isset( $s['socials_count'] ) ? $s['socials_count'] : 4 ) ) );

			if ( '' === $text && ( ! $phone_on || '' === $phone ) && ! $soc_on ) {
				return;
			}

			echo '<div class="zc-hdr-topbar relative z-[55] border-b border-line bg-secondary text-white ' . esc_attr( $hidden ) . '" data-zc-topbar>';
			echo '<div class="zc-hdr-tb-in zc-container flex h-11 items-center justify-between gap-4 text-[0.78rem]">';
			echo '<div class="flex min-w-0 items-center gap-2">';

			if ( isset( $s['hicon'] ) && is_array( $s['hicon'] ) && ! empty( $s['hicon']['value'] ) ) {
				echo '<span class="zc-hdr-topbar-ic text-primary h-4 w-4 inline-flex items-center justify-center">';
				$this->hdr_icon( $s['hicon'], '', 'h-4 w-4' );
				echo '</span>';
			}

			if ( '' !== $text ) {
				if ( '' !== $link ) {
					echo '<a class="zc-hdr-topbar-text truncate text-white/90 transition hover:text-primary" href="' . esc_url( $link ) . '">' . esc_html( $text ) . '</a>';
				} else {
					echo '<span class="zc-hdr-topbar-text truncate text-white/90">' . esc_html( $text ) . '</span>';
				}
			}
			echo '</div>';

			if ( ( $phone_on && '' !== $phone ) || $soc_on ) {
				echo '<div class="flex shrink-0 items-center gap-5">';
				if ( $phone_on && '' !== $phone ) {
					echo '<a class="zc-hdr-topbar-phone inline-flex items-center gap-2 text-white/85 transition hover:text-primary" href="tel:' . esc_attr( zc_normalize_phone( $phone ) ) . '">';
					zc_icon( 'phone', 'h-4 w-4' );
					echo '<span dir="ltr" class="text-[0.8rem]">' . esc_html( zc_digits_to_persian( $phone ) ) . '</span></a>';
				}
				if ( $soc_on ) {
					$socials = array_slice( zc_socials(), 0, $soc_n );
					if ( ! empty( $socials ) ) {
						echo '<div class="zc-hdr-topbar-socials flex items-center gap-3">';
						foreach ( $socials as $item ) {
							echo '<a class="inline-flex items-center text-white/80 transition hover:text-primary" href="' . esc_url( $item['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $item['label'] ) . '">';
							zc_icon( $item['icon'], 'h-4 w-4' );
							echo '</a>';
						}
						echo '</div>';
					}
				}
				echo '</div>';
			}

			echo '</div></div>';
		}
	}
endif;
