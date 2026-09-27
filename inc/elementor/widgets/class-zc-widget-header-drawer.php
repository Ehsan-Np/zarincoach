<?php
/**
 * ویجت هدر: منوی کشویی موبایل
 *
 * کشوی کامل منو: برندینگ، دکمه بستن با آیکن دلخواه، فهرست، دکمه‌ی فراخوان،
 * تلفن‌ها، ایمیل و شبکه‌های اجتماعی — سازگار با JS قالب (data-zc-drawer) و
 * استایل کامل کیت زرین‌کوچ. با «هدر: دکمه‌ی منوی موبایل» باز می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Drawer' ) ) :

	/**
	 * ویجت «هدر: منوی کشویی موبایل».
	 */
	class ZC_Widget_Header_Drawer extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-drawer';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: منوی کشویی موبایل', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-nav-menu';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'منوی کشویی', 'کشو', 'موبایل', 'drawer' ) );
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
				'hdrw_sec',
				array(
					'label' => __( 'منوی کشویی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_brand',
				array(
					'label'        => __( 'برندینگ بالای کشو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'close_icon',
				array(
					'label'       => __( 'آیکن دکمه‌ی بستن', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = ضربدر قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'       => __( 'فهرست منو', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( '«جایگاه پیش‌فرض» = منوی جایگاه «منوی موبایل» در «نمایش ← فهرست‌ها».', 'zarincoach' ),
				)
			);

			$this->add_control(
				'cta_toggle',
				array(
					'label'        => __( 'دکمه‌ی فراخوان در کشو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'cta_text',
				array(
					'label'       => __( 'متن دکمه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = متن پنل (سربرگ)', 'zarincoach' ),
					'condition'   => array( 'cta_toggle' => 'yes' ),
				)
			);

			$this->add_control(
				'cta_url',
				array(
					'label'       => __( 'پیوند دکمه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => __( 'خالی = پیوند پنل', 'zarincoach' ),
					'condition'   => array( 'cta_toggle' => 'yes' ),
				)
			);

			$this->add_control(
				'show_phones',
				array(
					'label'        => __( 'تلفن‌ها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'show_email',
				array(
					'label'        => __( 'ایمیل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_socials',
				array(
					'label'        => __( 'شبکه‌های اجتماعی', 'zarincoach' ),
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
					'brand'   => array( __( 'برندینگ کشو', 'zarincoach' ), '.zc-hdr-dr-brand' ),
					'close'   => array( __( 'دکمه‌ی بستن', 'zarincoach' ), '.zc-hdr-dr-close' ),
					'cta'     => array( __( 'دکمه‌ی فراخوان کشو', 'zarincoach' ), '.zc-hdr-dr-cta' ),
					'phones'  => array( __( 'تلفن‌ها', 'zarincoach' ), '.zc-hdr-dr-contact a[href^="tel"]' ),
					'email'   => array( __( 'ایمیل', 'zarincoach' ), '.zc-hdr-dr-contact a[href^="mailto"]' ),
					'socials' => array( __( 'شبکه‌های اجتماعی', 'zarincoach' ), '.zc-hdr-dr-socials' ),
				)
			);

			$this->zc_style(
				'hdrw_style',
				__( 'کشو', 'zarincoach' ),
				array(
					'panel'  => array( 'box', '.zc-drawer-panel', __( 'بدنه‌ی کشو', 'zarincoach' ), array( 'gradient' => false ) ),
					'back'   => array( 'color', '.zc-drawer-backdrop', __( 'پرده‌ی تیره', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'items'  => array( 'text', '.zc-drawer-panel nav a', __( 'آیتم‌های منو', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'contact' => array( 'text', '.zc-hdr-dr-contact', __( 'اطلاعات تماس', 'zarincoach' ) ),
					'soc'    => array( 'icon', '.zc-hdr-dr-socials .zc-social', __( 'شبکه‌ها', 'zarincoach' ), array( 'hover' => '.zc-hdr-dr-socials' ) ),
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
			$socials = zc_socials();

			$cta_text = trim( (string) $this->value( 'cta_text', 'header_cta_text', __( 'رزرو جلسه آشنایی', 'zarincoach' ) ) );
			$cta_url  = isset( $s['cta_url']['url'] ) && '' !== trim( (string) $s['cta_url']['url'] ) ? (string) $s['cta_url']['url'] : ( function_exists( 'zc_url' ) ? zc_url( (string) zc_opt( 'header_cta_url', '#booking' ) ) : (string) zc_opt( 'header_cta_url', '#booking' ) );

			echo '<div data-zc-drawer id="zc-drawer" class="zc-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="' . esc_attr__( 'منو', 'zarincoach' ) . '">';
			echo '<div data-zc-drawer-backdrop class="zc-drawer-backdrop"></div>';
			echo '<div class="zc-drawer-panel">';
			echo '<div class="flex items-center justify-between">';

			if ( $this->is_on( $s, 'show_brand' ) ) {
				echo '<div class="zc-hdr-dr-brand">';
				ob_start();
				zc_site_branding();
				echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی امن داخلی قالب.
				echo '</div>';
			} else {
				echo '<span></span>';
			}

			echo '<button type="button" data-zc-drawer-close class="zc-hdr-dr-close zc-btn-icon" aria-label="' . esc_attr__( 'بستن منو', 'zarincoach' ) . '">';
			$this->hdr_icon( isset( $s['close_icon'] ) ? $s['close_icon'] : array(), 'close', 'h-5 w-5' );
			echo '</button></div>';

			echo '<nav aria-label="' . esc_attr__( 'منوی موبایل', 'zarincoach' ) . '">';
			$margs = array(
				'container'      => false,
				'menu_class'     => 'flex flex-col gap-1',
				'fallback_cb'    => 'zc_fallback_menu',
				'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
				'theme_location' => 'zc-mobile',
			);
			$menu = isset( $s['menu'] ) ? absint( $s['menu'] ) : 0;
			if ( $menu > 0 && wp_get_nav_menu_object( $menu ) ) {
				$margs['menu'] = $menu;
			}
			wp_nav_menu( $margs );
			echo '</nav>';

			if ( $this->is_on( $s, 'cta_toggle' ) && '' !== $cta_text ) {
				echo '<a href="' . esc_url( $cta_url ) . '" class="zc-hdr-dr-cta zc-btn zc-btn-primary zc-btn-block zc-btn-lg">' . esc_html( $cta_text ) . '</a>';
			}

			$has_contact = $this->is_on( $s, 'show_phones' ) && ( '' !== $contact['phone'] || '' !== $contact['phone2'] );
			$has_email   = $this->is_on( $s, 'show_email' ) && '' !== $contact['email'];
			if ( $has_contact || $has_email ) {
				echo '<div class="zc-hdr-dr-contact mt-auto grid gap-3 text-[0.85rem] text-muted">';
				if ( $has_contact ) {
					foreach ( array( 'phone', 'phone2' ) as $pk ) {
						if ( '' === $contact[ $pk ] ) {
							continue;
						}
						echo '<a class="inline-flex items-center gap-2 hover:text-primary" href="tel:' . esc_attr( zc_normalize_phone( $contact[ $pk ] ) ) . '">';
						zc_icon( 'phone', 'h-4 w-4' );
						echo '<span>' . esc_html( $contact[ $pk . '_label' ] ) . ':</span><span dir="ltr">' . esc_html( zc_digits_to_persian( $contact[ $pk ] ) ) . '</span></a>';
					}
				}
				if ( $has_email ) {
					echo '<a class="inline-flex items-center gap-2 hover:text-primary" href="mailto:' . esc_attr( $contact['email'] ) . '">';
					zc_icon( 'mail', 'h-4 w-4' );
					echo '<span dir="ltr">' . esc_html( $contact['email'] ) . '</span></a>';
				}
				echo '</div>';
			}

			if ( $this->is_on( $s, 'show_socials' ) && ! empty( $socials ) ) {
				echo '<div class="zc-hdr-dr-socials flex items-center gap-2">';
				foreach ( $socials as $item ) {
					echo '<a class="zc-social" href="' . esc_url( $item['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $item['label'] ) . '">';
					zc_icon( $item['icon'], 'h-[18px] w-[18px]' );
					echo '</a>';
				}
				echo '</div>';
			}

			echo '</div></div>';
		}
	}
endif;
