<?php
/**
 * ویجت پرسش‌های پرتکرار (آکاردئون)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Faq' ) ) :

	/**
	 * ویجت FAQ.
	 */
	class ZC_Widget_Faq extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-faq';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پرسش‌های پرتکرار', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-help';
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array(
					'label' => __( 'محتوا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->heading_controls();

			$this->add_control(
				'source',
				array(
					'label'   => __( 'منبع', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'cpt'    => __( 'پرسش‌های ثبت‌شده', 'zarincoach' ),
						'manual' => __( 'ورود دستی', 'zarincoach' ),
					),
					'default' => 'cpt',
				)
			);

			$this->add_control(
				'count',
				array(
					'label'     => __( 'تعداد', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 30,
					'default'   => 6,
					'condition' => array( 'source' => 'cpt' ),
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'faq_question',
				array(
					'label'   => __( 'پرسش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'faq_answer',
				array(
					'label'   => __( 'پاسخ', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 4,
					'default' => '',
				)
			);

			$repeater->add_control(
				'faq_open',
				array(
					'label'        => __( 'باز به صورت پیش‌فرض', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'پرسش‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ faq_question }}}',
					'condition'   => array( 'source' => 'manual' ),
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'side'   => __( 'سربرگ کناری', 'zarincoach' ),
						'top'    => __( 'سربرگ بالا', 'zarincoach' ),
					),
					'default' => 'side',
				)
			);

			$this->add_control(
				'first_open',
				array(
					'label'        => __( 'باز بودن اولین مورد', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'source' => 'cpt' ),
				)
			);

			$this->add_control(
				'acc_icon',
				array(
					'label'       => __( 'آیکن باز/بسته', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = علامت + (هنگام باز شدن می‌چرخد).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'enable_schema',
				array(
					'label'        => __( 'نشانه‌گذاری FAQ برای گوگل', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
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
					'icon'    => array( __( 'آیکن باز/بسته', 'zarincoach' ), '.zc-acc-icon' ),
				)
			);
			$this->zc_style(
				'faq_layout',
				__( 'چیدمان (طرح کناری)', 'zarincoach' ),
				array(
					'grid' => array( 'split', '.zc-faq-grid', '', array( 'children' => '.zc-faq-side, .zc-faq-main', 'valign' => true ) ),
				),
				array( 'condition' => array( 'layout' => 'side' ) )
			);
			$this->zc_style(
				'faq_list',
				__( 'قاب فهرست پرسش‌ها', 'zarincoach' ),
				array(
					'box'  => array( 'box', '.zc-faq-list', '', array( 'width' => true, 'margin' => true ) ),
					'line' => array( 'color', '.zc-acc', __( 'رنگ خط بین پرسش‌ها', 'zarincoach' ), array( 'prop' => 'border-bottom-color' ) ),
					'item' => array( 'box', '.zc-acc', __( 'هر پرسش', 'zarincoach' ), array( 'gradient' => false, 'heading' => true ) ),
				)
			);
			$this->zc_style(
				'faq_q',
				__( 'پرسش', 'zarincoach' ),
				array(
					'q'     => array( 'text', '.zc-acc-head', __( 'متن پرسش', 'zarincoach' ), array( 'hover' => true, 'padding' => true, 'margin' => false ) ),
					'qopen' => array( 'color', '.zc-acc.is-open .zc-acc-head', __( 'رنگ پرسش باز', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'faq_icon',
				__( 'آیکن باز/بسته', 'zarincoach' ),
				array(
					'icon'   => array( 'icon', '.zc-acc-icon', '', array( 'hover' => '.zc-acc-head' ) ),
					'open'   => array( 'color', '.zc-acc.is-open .zc-acc-icon', __( 'رنگ آیکن در حالت باز', 'zarincoach' ) ),
					'openbg' => array( 'color', '.zc-acc.is-open .zc-acc-icon', __( 'زمینه‌ی آیکن در حالت باز', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'rotate' => array( 'size', '.zc-acc.is-open .zc-acc-icon', __( 'چرخش آیکن در حالت باز (درجه)', 'zarincoach' ), array( 'units' => array( 'deg' ), 'max' => 360, 'css' => 'transform: rotate({{SIZE}}deg);' ) ),
				)
			);
			$this->zc_style(
				'faq_a',
				__( 'پاسخ', 'zarincoach' ),
				array(
					'a'   => array( 'text', '.zc-acc-body, .zc-acc-body p', __( 'متن پاسخ', 'zarincoach' ), array( 'align' => true, 'margin' => false ) ),
					'pad' => array( 'box', '.zc-acc-body', __( 'قاب پاسخ', 'zarincoach' ), array( 'gradient' => false, 'heading' => true ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$source  = isset( $settings['source'] ) ? (string) $settings['source'] : 'cpt';
			$layout  = isset( $settings['layout'] ) ? (string) $settings['layout'] : 'side';
			$schema  = ! isset( $settings['enable_schema'] ) || 'yes' === (string) $settings['enable_schema'];
			$group   = 'zc-faq-' . $this->get_id();

			$items = array();

			if ( 'cpt' === $source ) {
				$query        = zc_get_faqs( isset( $settings['count'] ) ? (int) $settings['count'] : 6 );
				$first_open   = ! isset( $settings['first_open'] ) || 'yes' === (string) $settings['first_open'];
				$index        = 0;

				while ( $query->have_posts() ) {
					$query->the_post();
					$items[] = array(
						'question' => get_the_title(),
						'answer'   => get_the_content(),
						'open'     => $first_open && 0 === $index,
					);
					$index++;
				}
				wp_reset_postdata();
			} elseif ( ! empty( $settings['items'] ) ) {
				foreach ( $settings['items'] as $item ) {
					$items[] = array(
						'question' => isset( $item['faq_question'] ) ? (string) $item['faq_question'] : '',
						'answer'   => isset( $item['faq_answer'] ) ? (string) $item['faq_answer'] : '',
						'open'     => isset( $item['faq_open'] ) && 'yes' === (string) $item['faq_open'],
					);
				}
			}

			if ( empty( $items ) ) {
				echo '<p class="zc-lead">' . esc_html__( 'پرسشی ثبت نشده است.', 'zarincoach' ) . '</p>';
				return;
			}

			// اسکیمای FAQ ← جمع‌کننده‌ی گراف (یک JSON-LD واحد در انتهای صفحه).
			if ( $schema && function_exists( 'zc_schema_add_faq' ) && zc_schema_can_collect() ) {
				$qa = array();
				foreach ( $items as $item ) {
					$qa[] = array(
						'q' => $item['question'],
						'a' => $item['answer'],
					);
				}
				zc_schema_add_faq( $qa );
			}

			$delay = 0;

			if ( 'side' === $layout ) :
				?>
				<section class="zc-faq zc-section relative bg-surface2/50">
					<div class="zc-container">
						<div class="zc-faq-grid grid gap-8 lg:grid-cols-12 lg:gap-12">
							<div class="zc-faq-side lg:col-span-4">
								<?php $this->render_heading( '', 'h2' ); ?>
							</div>

							<div class="zc-faq-main lg:col-span-8">
								<?php $this->render_items( $items, $group, $delay ); ?>
							</div>
						</div>
					</div>
				</section>
				<?php
			else :
				?>
				<section class="zc-faq zc-section relative bg-surface2/50">
					<div class="zc-container">
						<?php $this->render_heading( '', 'h2' ); ?>
						<div class="zc-after-head">
							<?php $this->render_items( $items, $group, $delay ); ?>
						</div>
					</div>
				</section>
				<?php
			endif;
		}

		/**
		 * خروجی آیتم‌های آکاردئون.
		 *
		 * @param array  $items آیتم‌ها.
		 * @param string $group نام گروه.
		 * @param int    $delay تأخیر انیمیشن.
		 * @return void
		 */
		protected function render_items( $items, $group, $delay ) {
			?>
			<div class="zc-faq-list overflow-hidden rounded-[var(--zc-radius)] border border-line bg-surface px-5 sm:px-7">
				<?php foreach ( $items as $item ) : ?>
					<?php if ( '' === $item['question'] ) { continue; } ?>
					<div class="zc-acc zc-reveal <?php echo $item['open'] ? 'is-open' : ''; ?>" data-zc-acc data-zc-acc-group="<?php echo esc_attr( $group ); ?>" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
						<h3>
							<button type="button" class="zc-acc-head" data-zc-acc-head aria-expanded="<?php echo $item['open'] ? 'true' : 'false'; ?>">
								<span class="zc-acc-q"><?php echo esc_html( $item['question'] ); ?></span>
								<span class="zc-acc-icon" aria-hidden="true"><?php $this->zc_render_icon_or( $this->get_settings_for_display( 'acc_icon' ), 'plus', 'h-4 w-4' ); ?></span>
							</button>
						</h3>
						<div class="zc-acc-body">
							<div class="zc-prose !max-w-none">
								<?php echo wp_kses_post( wpautop( $item['answer'] ) ); ?>
							</div>
						</div>
					</div>
					<?php
					$delay += 60;
				endforeach;
				?>
			</div>
			<?php
		}
	}
endif;
