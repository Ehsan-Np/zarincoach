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

			$this->add_control(
				'all_label',
				array(
					'label'       => __( 'متن دکمه‌ی «همه»', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'همه', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);

			$this->add_control(
				'placeholder',
				array(
					'label'       => __( 'متن داخل جستجو', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'جستجو: مثلاً رهاشدگی، Shame یا کمال‌گرایی', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);

			$this->add_control(
				'need_label',
				array(
					'label'       => __( 'برچسب نیاز', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'نیاز: %s', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
					'description' => __( '%s جای نیاز هیجانی است.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'more_label',
				array(
					'label'       => __( 'متن «شرح کامل» روی کارت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'شرح کامل', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);

			$this->add_control(
				'empty_text',
				array(
					'label'       => __( 'پیام نبود نتیجه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'default'     => __( 'چیزی با این عبارت پیدا نکردیم. می‌تونید واژه‌ی دیگه‌ای رو امتحان کنید یا فیلتر «همه» رو بزنید.', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
				)
			);
			$this->end_controls_section();
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'chip_count' => array( __( 'عدد روی دکمه‌های فیلتر', 'zarincoach' ), '.zc-sc-chip-count' ),
					'sec_icon'   => array( __( 'آیکن دسته‌ها', 'zarincoach' ), '.zc-sc-sec-icon' ),
					'sec_count'  => array( __( 'تعداد مدخل هر دسته', 'zarincoach' ), '.zc-sc-sec-count' ),
					'need'       => array( __( 'برچسب نیاز', 'zarincoach' ), '.zc-sc-need' ),
					'card_group' => array( __( 'نام گروه روی کارت', 'zarincoach' ), '.zc-sc-card-group' ),
					'card_icon'  => array( __( 'آیکن کارت', 'zarincoach' ), '.zc-sc-card-icon' ),
					'card_more'  => array( __( '«شرح کامل» روی کارت', 'zarincoach' ), '.zc-sc-card-more' ),
				)
			);
			$this->zc_style(
				'sc_toolbar',
				__( 'نوار فیلتر و جستجو', 'zarincoach' ),
				array(
					'bar'    => array( 'box', '.zc-sc-toolbar', __( 'نوار', 'zarincoach' ), array( 'gradient' => false ) ),
					'chip'   => array( 'button', '.zc-sc-chip', __( 'دکمه‌های فیلتر', 'zarincoach' ) ),
					'active' => array( 'color', '.zc-sc-chip.is-active', __( 'پس‌زمینه‌ی فیلتر فعال', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'actc'   => array( 'color', '.zc-sc-chip.is-active', __( 'رنگ متن فیلتر فعال', 'zarincoach' ) ),
					'search' => array( 'input', '.zc-sc-search input', __( 'کادر جستجو', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'sc_sec',
				__( 'سربرگ دسته‌ها و زیردسته‌ها', 'zarincoach' ),
				array(
					'sec'    => array( 'box', '.zc-sc-sec', __( 'قاب هر دسته', 'zarincoach' ), array( 'gradient' => false ) ),
					'icon'   => array( 'icon', '.zc-sc-sec-icon', __( 'آیکن دسته', 'zarincoach' ) ),
					'title'  => array( 'text', '.zc-sc-sec-title', __( 'عنوان دسته', 'zarincoach' ) ),
					'count'  => array( 'text', '.zc-sc-sec-count', __( 'تعداد', 'zarincoach' ), array( 'bg' => true, 'margin' => false ) ),
					'desc'   => array( 'text', '.zc-sc-sec-desc', __( 'توضیح دسته', 'zarincoach' ), array( 'align' => true ) ),
					'sub'    => array( 'text', '.zc-sc-sub-title', __( 'عنوان زیردسته', 'zarincoach' ) ),
					'subd'   => array( 'text', '.zc-sc-sub-desc', __( 'توضیح زیردسته', 'zarincoach' ), array( 'align' => true ) ),
					'need'   => array( 'text', '.zc-sc-need', __( 'برچسب نیاز', 'zarincoach' ), array( 'bg' => true, 'padding' => true, 'margin' => false ) ),
				)
			);
			$this->zc_style(
				'sc_card',
				__( 'کارت‌ها', 'zarincoach' ),
				array(
					'grid'  => array( 'grid', '.zc-sc-grid', __( 'شبکه', 'zarincoach' ), array( 'cols' => false ) ),
					'card'  => array( 'box', '.zc-sc-card', __( 'کارت', 'zarincoach' ), array( 'hover' => true ) ),
					'group' => array( 'text', '.zc-sc-card-group', __( 'نام گروه', 'zarincoach' ), array( 'margin' => false ) ),
					'title' => array( 'text', '.zc-sc-card-title', __( 'عنوان', 'zarincoach' ), array( 'hover' => '.zc-sc-card' ) ),
					'en'    => array( 'text', '.zc-sc-card-en', __( 'نام انگلیسی', 'zarincoach' ), array( 'margin' => false ) ),
					'text'  => array( 'text', '.zc-sc-card-text', __( 'خلاصه', 'zarincoach' ), array( 'align' => true ) ),
					'more'  => array( 'text', '.zc-sc-card-more', __( 'شرح کامل', 'zarincoach' ), array( 'hover' => '.zc-sc-card', 'margin' => false ) ),
				)
			);
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
								'all_label'    => isset( $s['all_label'] ) ? (string) $s['all_label'] : '',
								'placeholder'  => isset( $s['placeholder'] ) ? (string) $s['placeholder'] : '',
								'need_label'   => isset( $s['need_label'] ) ? (string) $s['need_label'] : '',
								'more_label'   => isset( $s['more_label'] ) ? (string) $s['more_label'] : '',
								'empty_text'   => isset( $s['empty_text'] ) ? (string) $s['empty_text'] : '',
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
