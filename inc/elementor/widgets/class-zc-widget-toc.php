<?php
/**
 * ویجت «فهرست مطالب»
 *
 * فهرست خودکار تیترهای برگه (یا محدوده‌ی دلخواه) با ۱ تا ۴ ستون، چهار سبک، شماره‌گذاری،
 * حالت جمع‌شونده، دنبال‌کردن بخش فعال هنگام اسکرول و دکمه‌ی شناور.
 * پوسته در سرور ساخته می‌شود و جاوااسکریپت فهرست را از تیترهای صفحه پر می‌کند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Toc' ) ) :

	/**
	 * ویجت فهرست مطالب.
	 */
	class ZC_Widget_Toc extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-toc';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'فهرست مطالب', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-table-of-contents';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'فهرست', 'فهرست مطالب', 'toc', 'table of contents', 'تیتر' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section( 'content_section', array( 'label' => __( 'فهرست مطالب', 'zarincoach' ) ) );

			$this->add_control(
				'title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'فهرست مطالب', 'zarincoach' ),
				)
			);
			$this->add_control(
				'scope',
				array(
					'label'   => __( 'تیترها از کجا خوانده شوند؟', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'content' => __( 'متن‌ها (ویرایشگر متن، محتوای نوشته، ویجت متن و محتوا)', 'zarincoach' ),
						'page'    => __( 'کل برگه (به‌جز سربرگ و پاورقی)', 'zarincoach' ),
						'custom'  => __( 'انتخابگر CSS دلخواه', 'zarincoach' ),
					),
					'default' => 'content',
				)
			);
			$this->add_control(
				'selector',
				array(
					'label'       => __( 'انتخابگر CSS', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => '#article, .my-content',
					'condition'   => array( 'scope' => 'custom' ),
				)
			);
			$this->add_control(
				'levels',
				array(
					'label'   => __( 'سطح تیترها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'2'   => __( 'فقط H2', 'zarincoach' ),
						'2-3' => __( 'H2 و H3', 'zarincoach' ),
						'2-4' => __( 'H2 تا H4', 'zarincoach' ),
						'3-4' => __( 'H3 و H4', 'zarincoach' ),
					),
					'default' => '2-3',
				)
			);
			$this->add_control(
				'min',
				array(
					'label'   => __( 'حداقل تعداد تیتر برای نمایش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 10,
					'default' => 2,
				)
			);

			$this->end_controls_section();

			$this->start_controls_section( 'layout_section', array( 'label' => __( 'چیدمان و ظاهر', 'zarincoach' ) ) );
			$this->toc_style_controls();
			$this->add_control(
				'floating',
				array(
					'label'        => __( 'دکمه‌ی شناور فهرست هنگام اسکرول', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'پس از عبور از فهرست، دکمه‌ای با حلقه‌ی پیشرفت مطالعه نمایش داده می‌شود.', 'zarincoach' ),
				)
			);
			$this->add_control(
				'sticky',
				array(
					'label'        => __( 'چسبان (برای ستون کناری)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'فهرست هنگام اسکرول در بالای ستون خودش ثابت می‌ماند (در محدوده‌ی همان بخش).', 'zarincoach' ),
				)
			);
			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s     = $this->get_settings_for_display();
			$scope = isset( $s['scope'] ) ? (string) $s['scope'] : 'content';
			$scan  = array(
				'content' => '.zc-prose, .elementor-widget-text-editor, .elementor-widget-theme-post-content',
				'page'    => '#zc-main',
				'custom'  => isset( $s['selector'] ) && '' !== trim( (string) $s['selector'] ) ? trim( (string) $s['selector'] ) : '#zc-main',
			);

			$args = array(
				'title'       => isset( $s['title'] ) ? (string) $s['title'] : '',
				'levels'      => isset( $s['levels'] ) ? (string) $s['levels'] : '2-3',
				'min'         => isset( $s['min'] ) ? max( 1, (int) $s['min'] ) : 2,
				'columns'     => isset( $s['columns'] ) ? (int) $s['columns'] : 2,
				'style'       => isset( $s['style'] ) ? (string) $s['style'] : 'card',
				'numbering'   => isset( $s['numbering'] ) ? (string) $s['numbering'] : 'decimal',
				'collapsible' => $this->is_on( $s, 'collapsible' ),
				'open'        => $this->is_on( $s, 'open' ),
				'meta'        => $this->is_on( $s, 'meta' ),
				'floating'    => $this->is_on( $s, 'floating' ),
				'scan'        => $scan[ $scope ] ?? $scan['content'],
				'class'       => $this->is_on( $s, 'sticky' ) ? 'is-sticky' : '',
			);

			$placeholder = '<p class="zc-toc-empty">' . esc_html__( 'تیترهای صفحه پس از بارگذاری اینجا فهرست می‌شوند.', 'zarincoach' ) . '</p>';
			echo zc_toc_shell( $placeholder, $args ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}
endif;
