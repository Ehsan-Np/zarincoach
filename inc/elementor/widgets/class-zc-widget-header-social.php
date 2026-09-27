<?php
/**
 * ویجت هدر: شبکه‌های اجتماعی
 *
 * ردیف آیکونی شبکه‌ها (از پنل تنظیمات) با کنترل تعداد، فاصله و استایل کامل کیت.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Social' ) ) :

	/**
	 * ویجت «هدر: شبکه‌های اجتماعی».
	 */
	class ZC_Widget_Header_Social extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-social';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: شبکه‌های اجتماعی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-social-icons';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'اینستاگرام', 'تلگرام', 'بله', 'شبکه‌های اجتماعی', 'social' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hsocial_sec',
				array(
					'label' => __( 'شبکه‌های اجتماعی', 'zarincoach' ),
				)
			);

			$this->add_control(
				'count',
				array(
					'label'       => __( 'تعداد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::NUMBER,
					'min'         => 1,
					'max'         => 6,
					'default'     => 4,
					'description' => __( 'نشانی شبکه‌ها از «پنل قالب ← اطلاعات و ارتباط ← شبکه‌های اجتماعی» خوانده می‌شود.', 'zarincoach' ),
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
				'hsocial_style',
				__( 'شبکه‌ها', 'zarincoach' ),
				array(
					'ic'  => array( 'icon', '.zc-hdr-socials .zc-social', __( 'دکمه‌ها', 'zarincoach' ), array( 'hover' => '.zc-hdr-socials' ) ),
					'gap' => array( 'size', '.zc-hdr-socials', __( 'فاصله', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
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
			$count   = max( 1, min( 6, (int) ( isset( $s['count'] ) ? $s['count'] : 4 ) ) );
			$socials = array_slice( zc_socials(), 0, $count );
			if ( empty( $socials ) ) {
				return;
			}

			echo '<div class="zc-hdr-socials flex items-center gap-2">';
			foreach ( $socials as $item ) {
				echo '<a class="zc-social" href="' . esc_url( $item['url'] ) . '" target="_blank" rel="noopener" aria-label="' . esc_attr( $item['label'] ) . '">';
				zc_icon( $item['icon'], 'h-[18px] w-[18px]' );
				echo '</a>';
			}
			echo '</div>';
		}
	}
endif;
