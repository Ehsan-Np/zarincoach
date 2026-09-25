<?php
/**
 * ویجت «نمادهای اعتماد»
 *
 * ۱ تا ۶ نماد: اینماد، ساماندهی، درگاه زرین‌پال، درگاه پی (Pay.ir)، نظام روان‌شناسی یا نماد دلخواه.
 * هر نماد: کد رسمی (HTML)، تصویر آپلودی یا طرح پیش‌فرض خنثی. کدهای خالی از پنل تنظیمات قالب خوانده می‌شوند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Trust_Badges' ) ) :

	/**
	 * ویجت نمادهای اعتماد.
	 */
	class ZC_Widget_Trust_Badges extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-trust-badges';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'نمادهای اعتماد', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-lock-user';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'اینماد', 'ساماندهی', 'زرین‌پال', 'پی', 'نماد', 'اعتماد', 'enamad', 'samandehi', 'zarinpal', 'pay.ir', 'trust' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$types = array();
			foreach ( zc_trust_types() as $key => $type ) {
				$types[ $key ] = $type['name'];
			}

			/* ---------- نمادها ---------- */
			$this->start_controls_section( 'badges_section', array( 'label' => __( 'نمادها (۱ تا ۶)', 'zarincoach' ) ) );

			$repeater = $this->trust_repeater();

			$this->add_control(
				'items',
				array(
					'label'       => __( 'نمادها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => zc_trust_default_items(),
					'title_field' => '<# var zcT = ' . wp_json_encode( $types ) . '; #>{{{ label ? label : ( zcT[ type ] || type ) }}}',
					'max_items'   => 6,
				)
			);

			$this->add_control(
				'items_note',
				array(
					'type'            => \Elementor\Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'حداکثر ۶ نماد نمایش داده می‌شود. کدهای اینماد، ساماندهی، زرین‌پال و پی را می‌توانید یک‌بار در «تنظیمات قالب ← اطلاعات حقوقی» ثبت کنید تا همه‌جا (پاورقی و این ویجت) استفاده شود.', 'zarincoach' ),
					'content_classes' => 'elementor-panel-alert elementor-panel-alert-info',
				)
			);

			$this->add_control(
				'fallback',
				array(
					'label'        => __( 'طرح پیش‌فرض برای کدهای خالی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'تا زمان درج کد رسمی، کاشی خنثی نمایش داده شود. خاموش = نمادهای بدون کد پنهان می‌شوند.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			/* ---------- سربرگ ---------- */
			$this->start_controls_section( 'content_section', array( 'label' => __( 'عنوان و توضیح', 'zarincoach' ) ) );
			$this->heading_controls();
			$this->add_control(
				'note',
				array(
					'label'   => __( 'یادداشت زیر نمادها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);
			$this->end_controls_section();

			/* ---------- چیدمان ---------- */
			$this->start_controls_section( 'layout_section', array( 'label' => __( 'چیدمان و ظاهر', 'zarincoach' ) ) );

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان بخش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'stacked' => __( 'عنوان بالا، نمادها پایین', 'zarincoach' ),
						'split'   => __( 'عنوان کنار نمادها (دوستونه)', 'zarincoach' ),
					),
					'default' => 'split',
				)
			);
			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک نمادها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'cards'   => __( 'کارت سفید با عنوان', 'zarincoach' ),
						'navy'    => __( 'کارت سرمه‌ای', 'zarincoach' ),
						'inline'  => __( 'افقی فشرده (مناسب پاورقی)', 'zarincoach' ),
						'minimal' => __( 'مینیمال (فقط نماد)', 'zarincoach' ),
					),
					'default' => 'cards',
				)
			);
			$this->add_control(
				'columns',
				array(
					'label'       => __( 'تعداد ستون (دسکتاپ)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => array(
						'1' => '۱',
						'2' => '۲',
						'3' => '۳',
						'4' => '۴',
						'5' => '۵',
						'6' => '۶',
					),
					'default'     => '4',
					'description' => __( 'در تبلت حداکثر ۳ و در موبایل حداکثر ۲ ستون.', 'zarincoach' ),
					'condition'   => array( 'style!' => 'inline' ),
				)
			);
			$this->add_control(
				'items_align',
				array(
					'label'   => __( 'تراز نمادها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'start'  => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-h-align-right',
						),
						'center' => array(
							'title' => __( 'وسط', 'zarincoach' ),
							'icon'  => 'eicon-h-align-center',
						),
						'end'    => array(
							'title' => __( 'چپ', 'zarincoach' ),
							'icon'  => 'eicon-h-align-left',
						),
					),
					'default' => 'center',
					'toggle'  => false,
				)
			);
			$this->add_control(
				'captions',
				array(
					'label'        => __( 'نمایش عنوان زیر نمادها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);
			$this->add_control(
				'gray',
				array(
					'label'        => __( 'خاکستری؛ رنگی هنگام هاور', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$this->add_control(
				'wrap',
				array(
					'label'   => __( 'فاصله‌ی بخش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'section' => __( 'بخش کامل (با فاصله و عرض محدود)', 'zarincoach' ),
						'tight'   => __( 'بخش فشرده', 'zarincoach' ),
						'plain'   => __( 'بدون فاصله (داخل ستون/قالب دیگر)', 'zarincoach' ),
					),
					'default' => 'section',
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
			$s      = $this->get_settings_for_display();
			$editor = class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor && \Elementor\Plugin::$instance->editor->is_edit_mode();

			$items = $this->trust_items_from( ! empty( $s['items'] ) ? (array) $s['items'] : array() );

			$style  = isset( $s['style'] ) ? (string) $s['style'] : 'cards';
			$badges = zc_trust_badges_render(
				$items,
				array(
					'style'    => $style,
					'columns'  => 'inline' === $style ? 6 : ( isset( $s['columns'] ) ? (int) $s['columns'] : 4 ),
					'align'    => isset( $s['items_align'] ) ? (string) $s['items_align'] : 'center',
					'gray'     => $this->is_on( $s, 'gray' ),
					'captions' => $this->is_on( $s, 'captions' ),
					'fallback' => $this->is_on( $s, 'fallback' ),
					'editor'   => $editor,
				)
			);

			if ( '' === $badges ) {
				if ( $editor ) {
					echo '<div class="zc-panel text-center text-[0.9rem] text-muted">' . esc_html__( 'نمادی برای نمایش وجود ندارد؛ کد رسمی را درج کنید یا «طرح پیش‌فرض برای کدهای خالی» را روشن کنید.', 'zarincoach' ) . '</div>';
				}
				return;
			}

			$wrap   = isset( $s['wrap'] ) ? (string) $s['wrap'] : 'section';
			$layout = isset( $s['layout'] ) ? (string) $s['layout'] : 'split';
			$note   = isset( $s['note'] ) ? trim( (string) $s['note'] ) : '';
			$has_h  = '' !== trim( (string) ( $s['title'] ?? '' ) ) || '' !== trim( (string) ( $s['eyebrow'] ?? '' ) ) || '' !== trim( (string) ( $s['subtitle'] ?? '' ) );
			$outer  = array(
				'section' => 'zc-section',
				'tight'   => 'zc-section-tight',
				'plain'   => '',
			);
			$note_html = '' !== $note ? '<p class="zc-trust-note">' . zc_icon( 'lock', 'h-4 w-4', false ) . '<span>' . esc_html( $note ) . '</span></p>' : '';
			?>
			<section class="zc-trust-section relative <?php echo esc_attr( $outer[ $wrap ] ?? 'zc-section' ); ?>">
				<div class="<?php echo 'plain' === $wrap ? '' : 'zc-container'; ?>">
					<?php if ( 'split' === $layout && ( $has_h || '' !== $note ) ) : ?>
						<div class="zc-trust-split grid gap-8 lg:grid-cols-12 lg:items-center lg:gap-10">
							<div class="lg:col-span-4">
								<?php $this->render_heading( '', 'h2' ); ?>
								<?php echo $note_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</div>
							<div class="zc-reveal lg:col-span-8"><?php echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						</div>
					<?php else : ?>
						<?php $this->render_heading( '', 'h2' ); ?>
						<div class="zc-reveal <?php echo $has_h ? 'zc-after-head' : ''; ?>"><?php echo $badges; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php if ( '' !== $note_html ) : ?>
							<div class="mt-6 flex justify-center"><?php echo $note_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
