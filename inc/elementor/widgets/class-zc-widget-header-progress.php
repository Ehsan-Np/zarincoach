<?php
/**
 * ویجت هدر: نوار پیشرفت مطالعه
 *
 * نوار باریک پایین هدر که با اسکرول پر می‌شود (سازگار با JS قالب).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Progress' ) ) :

	/**
	 * ویجت «هدر: نوار پیشرفت مطالعه».
	 */
	class ZC_Widget_Header_Progress extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-progress';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: نوار پیشرفت مطالعه', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-skill-bar';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'پیشرفت', 'اسکرول', 'progress' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hprog_sec',
				array(
					'label' => __( 'نوار پیشرفت مطالعه', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show',
				array(
					'label'        => __( 'نمایش نوار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'نوار باریک پایین هدر که با اسکرول صفحه پر می‌شود.', 'zarincoach' ),
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
				'hprog_style',
				__( 'نوار', 'zarincoach' ),
				array(
					'bar'    => array( 'color', '.zc-hdr-progress', __( 'رنگ نوار', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'height' => array( 'size', '.zc-hdr-progress', __( 'ضخامت نوار', 'zarincoach' ), array( 'prop' => 'height', 'max' => 12 ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s = $this->get_settings_for_display();
			if ( ! $this->is_on( $s, 'show' ) ) {
				return;
			}
			echo '<span data-zc-progress class="zc-hdr-progress zc-progress" aria-hidden="true"></span>';
		}
	}
endif;
