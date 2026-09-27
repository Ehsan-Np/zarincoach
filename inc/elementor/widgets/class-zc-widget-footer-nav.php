<?php
/**
 * ویجت پانوشت: منوی قوانین
 *
 * ردیف پیوندهای پانوشت (منوی قوانین و مقررات یا هر فهرست دیگر) با چینش دلخواه
 * و کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Nav' ) ) :

	/**
	 * ویجت «پانوشت: منوی قوانین».
	 */
	class ZC_Widget_Footer_Nav extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-nav';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: منوی قوانین', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-single-post';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'قوانین', 'مقررات', 'پیوندها', 'منوی دوم' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'fwnav_sec',
				array(
					'label' => __( 'منوی قوانین', 'zarincoach' ),
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'       => __( 'فهرست', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( '«جایگاه پیش‌فرض» = منوی جایگاه «منوی قوانین (پاورقی)» در «نمایش ← فهرست‌ها».', 'zarincoach' ),
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
					'default' => 'start',
					'selectors_dictionary' => array(
						'start'  => 'justify-content: flex-start;',
						'center' => 'justify-content: center;',
						'end'    => 'justify-content: flex-end;',
					),
					'selectors' => array(
						'{{WRAPPER}} .zc-fw-nav-list' => '{{VALUE}};',
					),
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
					'nav' => array( __( 'منوی قوانین', 'zarincoach' ), '.zc-fw-nav-list' ),
				)
			);

			$this->zc_style(
				'fwnav_text',
				__( 'آیتم‌ها', 'zarincoach' ),
				array(
					'items' => array( 'text', '.zc-fw-nav-list a', __( 'متن آیتم‌ها', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'list'  => array( 'text', '.zc-fw-nav-list', __( 'کل ردیف', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwnav_layout',
				__( 'چیدمان', 'zarincoach' ),
				array(
					'gap' => array( 'size', '.zc-fw-nav-list', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), array( 'max' => 60, 'css' => 'gap: {{SIZE}}{{UNIT}};' ) ),
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
			$menu  = isset( $s['menu'] ) ? absint( $s['menu'] ) : 0;

			$has_menu = $menu > 0 || has_nav_menu( 'zc-legal' );
			if ( ! $has_menu ) {
				if ( current_user_can( 'manage_options' ) ) {
					echo '<p class="zc-fw-nav-hint m-0 text-[0.78rem] text-white/50">' . esc_html__( '(فقط برای مدیر) فهرستی به جایگاه «منوی قوانین (پاورقی)» متصل کنید یا از همین ویجت یک فهرست انتخاب کنید.', 'zarincoach' ) . '</p>';
				}
				return;
			}

			echo '<div class="zc-fw zc-fw-nav">';
			wp_nav_menu(
				zc_header_menu_args(
					$menu,
					'zc-legal',
					array(
						'container'   => false,
						'menu_class'  => 'zc-fw-nav-list zc-footer-nav-list flex flex-wrap items-center gap-x-6 gap-y-2 text-[0.8rem] text-white/60',
						'depth'       => 1,
						'fallback_cb' => false,
						'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					)
				)
			);
			echo '</div>';
		}
	}
endif;
