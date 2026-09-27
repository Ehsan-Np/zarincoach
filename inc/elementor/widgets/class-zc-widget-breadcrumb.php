<?php
/**
 * ویجت صفحات: مسیر راهنما (Breadcrumb)
 *
 * بردکامب مستقل برای هر برگه/نوشته/بایگانی — از همان داده‌ی سئوی قالب
 * (zc_breadcrumb_items) استفاده می‌کند؛ آیکن خانه، جداکننده، آیتم جاری،
 * چینش و استایل کامل کیت زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Breadcrumb' ) ) :

	/**
	 * ویجت «مسیر راهنما».
	 */
	class ZC_Widget_Breadcrumb extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-breadcrumb';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'مسیر راهنما (Breadcrumb)', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-instagram-gallery';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'breadcrumb', 'بردکامب', 'مسیر راهنما', 'سلسله‌مراتب', 'صفحه' ) );
		}

		/**
		 * آیکون انتخابی با جایگزین داخلی.
		 *
		 * @param array|string $icon     آیکون.
		 * @param string       $fallback نام آیکون داخلی.
		 * @param string       $class    کلاس اندازه.
		 * @return void
		 */
		private function bc_icon( $icon, $fallback, $class ) {
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
				'bc_sec',
				array(
					'label' => __( 'مسیر راهنما', 'zarincoach' ),
				)
			);

			$this->add_control(
				'home_label',
				array(
					'label'       => __( 'برچسب خانه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'خانه', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_home_icon',
				array(
					'label'        => __( 'آیکن خانه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'home_icon',
				array(
					'label'       => __( 'آیکن دلخواه خانه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'خالی = آیکن خانه‌ی قالب.', 'zarincoach' ),
					'condition'   => array( 'show_home_icon' => 'yes' ),
				)
			);

			$this->add_control(
				'separator',
				array(
					'label'   => __( 'جداکننده', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'chevron' => __( 'فلش (پیش‌فرض قالب)', 'zarincoach' ),
						'slash'   => ' / ',
						'gt'      => ' › ',
						'dot'     => ' • ',
						'dash'    => ' — ',
					),
					'default' => 'chevron',
				)
			);

			$this->add_control(
				'sep_icon',
				array(
					'label'       => __( 'آیکن جداکننده‌ی دلخواه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'default'     => array( 'value' => '', 'library' => '' ),
					'description' => __( 'در صورت انتخاب، از جداکننده‌ی متنی بالاتر پیشی می‌گیرد.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_current',
				array(
					'label'        => __( 'نمایش آیتم جاری', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'align',
				array(
					'label'     => __( 'چینش', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'start'  => array( 'title' => __( 'راست', 'zarincoach' ), 'icon' => 'eicon-text-align-right' ),
						'center' => array( 'title' => __( 'وسط', 'zarincoach' ), 'icon' => 'eicon-text-align-center' ),
						'end'    => array( 'title' => __( 'چپ', 'zarincoach' ), 'icon' => 'eicon-text-align-left' ),
					),
					'default'   => 'start',
					'selectors_dictionary' => array(
						'start'  => 'justify-content: flex-start;',
						'center' => 'justify-content: center;',
						'end'    => 'justify-content: flex-end;',
					),
					'selectors' => array(
						'{{WRAPPER}} .zc-bc ol' => '{{VALUE}};',
					),
				)
			);

			$this->add_control(
				'boxed',
				array(
					'label'        => __( 'قاب کاردی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'زمینه، حاشیه و انحنا را می‌توانید در تب استایل تنظیم کنید.', 'zarincoach' ),
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
					'home'  => array( __( 'آیتم خانه', 'zarincoach' ), '.zc-bc-home' ),
					'hicon' => array( __( 'آیکن خانه', 'zarincoach' ), '.zc-bc-home-ic' ),
					'sep'   => array( __( 'جداکننده‌ها', 'zarincoach' ), '.zc-bc-sep' ),
					'cur'   => array( __( 'آیتم جاری', 'zarincoach' ), '.zc-bc-current' ),
				)
			);

			$this->zc_style(
				'bc_style',
				__( 'مسیر راهنما', 'zarincoach' ),
				array(
					'box' => array( 'box', '.zc-bc', __( 'قاب', 'zarincoach' ), array( 'gradient' => true, 'text' => true ) ),
					'link' => array( 'text', '.zc-bc a', __( 'پیوندها', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'cur' => array( 'text', '.zc-bc-current', __( 'آیتم جاری', 'zarincoach' ), array( 'margin' => false ) ),
					'sep' => array( 'color', '.zc-bc-sep', __( 'جداکننده', 'zarincoach' ), array( 'prop' => 'color' ) ),
					'hic' => array( 'icon', '.zc-bc-home-ic', __( 'آیکن خانه', 'zarincoach' ) ),
					'sic' => array( 'icon', '.zc-bc-sep-ic', __( 'آیکن جداکننده', 'zarincoach' ) ),
					'gap' => array( 'size', '.zc-bc ol', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), array( 'max' => 40, 'css' => 'gap: {{SIZE}}{{UNIT}};' ) ),
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
			$items = zc_breadcrumb_items( trim( (string) ( isset( $s['home_label'] ) ? $s['home_label'] : '' ) ) );
			if ( count( $items ) < 2 ) {
				return;
			}

			$sep    = (string) ( isset( $s['separator'] ) ? $s['separator'] : 'chevron' );
			$sep_ic = isset( $s['sep_icon'] ) && is_array( $s['sep_icon'] ) && ! empty( $s['sep_icon']['value'] );

			$boxed = $this->is_on( $s, 'boxed' ) ? ' rounded-2xl border border-line bg-surface px-4 py-3' : '';

			echo '<nav class="zc-bc zc-breadcrumb zc-arabic-num' . esc_attr( $boxed ) . '" aria-label="' . esc_attr__( 'مسیر راهنما', 'zarincoach' ) . '">';
			echo '<ol class="flex flex-wrap items-center gap-1.5 text-[0.8rem] text-muted">';

			$last = count( $items ) - 1;
			foreach ( $items as $i => $item ) {
				$is_last = $i === $last;
				$is_home = 0 === $i;

				echo '<li class="flex items-center gap-1.5">';

				if ( $is_last && ! $this->is_on( $s, 'show_current' ) ) {
					echo '</li>';
					continue;
				}

				if ( $is_home && $this->is_on( $s, 'show_home_icon' ) ) {
					echo '<span class="zc-bc-home-ic inline-flex items-center justify-center h-3.5 w-3.5 text-primary">';
					$this->bc_icon( isset( $s['home_icon'] ) ? $s['home_icon'] : array(), 'home', 'h-3.5 w-3.5' );
					echo '</span>';
				}

				if ( '' !== (string) $item['url'] ) {
					echo '<a class="transition hover:text-primary' . ( $is_home ? ' zc-bc-home' : '' ) . '" href="' . esc_url( (string) $item['url'] ) . '">' . esc_html( (string) $item['label'] ) . '</a>';
				} else {
					$cur_class = $is_last ? ' zc-bc-current font-bold text-secondary dark:text-white' : '';
					echo '<span class="zc-bc-item' . esc_attr( $cur_class ) . '">' . esc_html( (string) $item['label'] ) . '</span>';
				}

				if ( ! $is_last ) {
					echo '<span class="zc-bc-sep inline-flex items-center justify-center h-3 w-3 text-muted/60">';
					if ( $sep_ic ) {
						echo '<span class="zc-bc-sep-ic inline-flex items-center justify-center h-3 w-3">';
						$this->bc_icon( $s['sep_icon'], '', 'h-3 w-3' );
						echo '</span>';
					} elseif ( 'chevron' === $sep ) {
						zc_icon( 'chevron-left', 'h-3 w-3' );
					} else {
						echo '<span class="px-0.5">' . esc_html( trim( (string) $s['separator'] ) ) . '</span>';
					}
					echo '</span>';
				}

				echo '</li>';
			}

			echo '</ol></nav>';
		}
	}
endif;
