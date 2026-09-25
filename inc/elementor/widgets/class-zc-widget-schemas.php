<?php
/**
 * ویجت کتابخانه‌ی طرحواره‌ها (فیلترپذیر، از روی نوع نوشته‌ی zc_schema)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Schemas' ) ) :

	/**
	 * ویجت کتابخانه‌ی طرحواره‌ها.
	 */
	class ZC_Widget_Schemas extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-schemas';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'کتابخانه‌ی طرحواره‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-gallery-grid';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return string[]
		 */
		public function get_keywords() {
			return array( 'schema', 'mode', 'distortion', 'طرحواره', 'ذهنیت', 'خطای شناختی', 'zarin' );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$groups = array();
			if ( function_exists( 'zc_sc_top_groups' ) ) {
				foreach ( zc_sc_top_groups() as $slug => $g ) {
					$groups[ $slug ] = $g['name'];
				}
			}

			$this->start_controls_section(
				'content_section',
				array(
					'label' => __( 'سربرگ', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);
			$this->heading_controls();
			$this->add_control(
				'boxed',
				array(
					'label'        => __( 'فاصله‌ی بالا و پایین بخش', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'query_section',
				array(
					'label' => __( 'محتوا و فیلتر', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);
			$this->add_control(
				'groups',
				array(
					'label'       => __( 'دسته‌های نمایش', 'zarincoach' ),
					'description' => __( 'خالی = همه‌ی دسته‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'options'     => $groups,
					'default'     => array(),
				)
			);
			$this->add_control(
				'default_filter',
				array(
					'label'   => __( 'فیلتر فعال در شروع', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array( 'all' => __( 'همه', 'zarincoach' ) ) + $groups,
					'default' => 'all',
				)
			);
			$this->add_control(
				'show_filter',
				array(
					'label'        => __( 'دکمه‌های فیلتر', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_search',
				array(
					'label'        => __( 'جعبه‌ی جستجو', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_subgroups',
				array(
					'label'        => __( 'زیرعنوان حوزه‌ها و زیرگروه‌ها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_desc',
				array(
					'label'        => __( 'توضیح هر دسته و حوزه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->end_controls_section();

			$this->start_controls_section(
				'card_section',
				array(
					'label' => __( 'کارت‌ها', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);
			$this->add_control(
				'columns',
				array(
					'label'   => __( 'تعداد ستون (دسکتاپ)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'2' => __( '۲', 'zarincoach' ),
						'3' => __( '۳', 'zarincoach' ),
						'4' => __( '۴', 'zarincoach' ),
					),
					'default' => '3',
				)
			);
			$this->add_control(
				'show_en',
				array(
					'label'        => __( 'نام انگلیسی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'show_summary',
				array(
					'label'        => __( 'خلاصه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'heading_tag',
				array(
					'label'   => __( 'تگ عنوان دسته‌ها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'h2' => 'H2',
						'h3' => 'H3',
					),
					'default' => 'h2',
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
			if ( ! function_exists( 'zc_schemas_grid' ) ) {
				return;
			}
			$s      = $this->get_settings_for_display();
			$groups = ( ! empty( $s['groups'] ) && is_array( $s['groups'] ) ) ? array_map( 'sanitize_key', $s['groups'] ) : array();
			$boxed  = ! isset( $s['boxed'] ) || 'yes' === (string) $s['boxed'];
			$flag   = function ( $key ) use ( $s ) {
				return ! isset( $s[ $key ] ) || 'yes' === (string) $s[ $key ];
			};
			?>
			<section class="zc-schemas <?php echo $boxed ? 'zc-section' : ''; ?> relative">
				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>
					<div class="<?php echo ( ! empty( $s['title'] ) || ! empty( $s['subtitle'] ) ) ? 'zc-after-head' : ''; ?>">
						<?php
						echo zc_schemas_grid( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							array(
								'groups'       => $groups,
								'default'      => isset( $s['default_filter'] ) ? sanitize_key( (string) $s['default_filter'] ) : 'all',
								'filter'       => $flag( 'show_filter' ),
								'search'       => $flag( 'show_search' ),
								'subgroups'    => $flag( 'show_subgroups' ),
								'group_desc'   => $flag( 'show_desc' ),
								'show_en'      => $flag( 'show_en' ),
								'show_summary' => $flag( 'show_summary' ),
								'columns'      => isset( $s['columns'] ) ? (int) $s['columns'] : 3,
								'heading_tag'  => isset( $s['heading_tag'] ) ? (string) $s['heading_tag'] : 'h2',
							)
						);
						?>
					</div>
				</div>
			</section>
			<?php
		}
	}

endif;
