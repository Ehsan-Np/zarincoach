<?php
/**
 * ویجت فهرست ویژگی‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_List' ) ) :

	/**
	 * ویجت فهرست.
	 */
	class ZC_Widget_List extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-list';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'فهرست ویژگی‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-bullet-list';
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

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'item_text',
				array(
					'label'   => __( 'متن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_merge(
						array( '' => __( 'بدون', 'zarincoach' ) ),
						function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array()
					),
					'default' => '',
				)
			);

			$repeater->add_control( 'item_eicon', array( 'label' => __( 'آیکون از کتابخانه (اختیاری)', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::ICONS, 'default' => array( 'value' => '', 'library' => '' ), 'description' => __( 'در صورت انتخاب، جای آیکون فهرستی را می‌گیرد', 'zarincoach' ) ) );
			$repeater->add_control( 'on', array( 'label' => __( 'نمایش این آیتم', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );

			$this->add_control(
				'items',
				array(
					'label'       => __( 'آیتم‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ item_text }}}',
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'check' => __( 'تیک‌دار', 'zarincoach' ),
						'dash'  => __( 'خط تیره', 'zarincoach' ),
						'cards' => __( 'کارتی', 'zarincoach' ),
					),
					'default' => 'check',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '1' );

			$this->end_controls_section();
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_style(
				'list_items',
				__( 'آیتم‌ها', 'zarincoach' ),
				array(
					'wrap' => array( 'size', '.zc-list-items', __( 'فاصله از سربرگ', 'zarincoach' ), array( 'prop' => 'margin-top', 'max' => 100 ) ),
					'grid' => array( 'grid', '.zc-list-items', __( 'شبکه', 'zarincoach' ), array( 'cols' => false ) ),
					'list' => array( 'list', '.zc-list-ul li', __( 'آیتم‌های فهرست (تیک/خط)', 'zarincoach' ), array( 'list' => '.zc-list-ul', 'marker' => '.zc-list-ul li::before' ) ),
				)
			);
			$this->zc_style(
				'list_cards',
				__( 'کارت‌ها (طرح کارتی)', 'zarincoach' ),
				array(
					'card' => array( 'box', '.zc-list-card', __( 'کارت', 'zarincoach' ), array( 'hover' => true ) ),
					'icon' => array( 'icon', '.zc-list-icon', __( 'آیکن', 'zarincoach' ), array( 'hover' => '.zc-list-card' ) ),
					'text' => array( 'text', '.zc-list-text', __( 'متن', 'zarincoach' ), array( 'hover' => false ) ),
				),
				array( 'condition' => array( 'style' => 'cards' ) )
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$items   = ! empty( $settings['items'] ) ? $settings['items'] : array();
			$style   = isset( $settings['style'] ) ? (string) $settings['style'] : 'check';
			$columns = isset( $settings['columns'] ) ? (string) $settings['columns'] : '1';

			if ( empty( $items ) ) {
				return;
			}

			$grid_class = '1' === $columns ? '' : 'grid gap-3 ' . $this->grid_classes( $columns );
			?>
			<div class="zc-list-widget">
				<?php $this->render_heading( '', 'h3' ); ?>

				<?php if ( 'cards' === $style ) : ?>
					<div class="zc-list-cards zc-list-items mt-6 grid gap-4 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
						<?php foreach ( $items as $item ) : ?>
							<?php
							$text = isset( $item['item_text'] ) ? trim( (string) $item['item_text'] ) : '';
							$icon = isset( $item['item_icon'] ) ? (string) $item['item_icon'] : '';

							if ( isset( $item['on'] ) && 'yes' !== (string) $item['on'] ) { continue; }
							$icon  = isset( $item['item_icon'] ) ? (string) $item['item_icon'] : '';
							$eicon = ( isset( $item['item_eicon']['value'] ) && '' !== (string) $item['item_eicon']['value'] ) ? $item['item_eicon'] : array();
							if ( isset( $item['on'] ) && 'yes' !== (string) $item['on'] ) { continue; }
							$icon  = isset( $item['item_icon'] ) ? (string) $item['item_icon'] : '';
							$eicon = ( isset( $item['item_eicon']['value'] ) && '' !== (string) $item['item_eicon']['value'] ) ? $item['item_eicon'] : array();
							if ( '' === $text ) {
								continue;
							}
							?>
							<div class="zc-list-card zc-card flex items-start gap-3 p-5">
								<?php if ( '' !== $icon ) : ?>
									<span class="zc-list-icon grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
										<?php if ( ! empty( $eicon ) ) { $this->render_icon( $eicon, 'h-5 w-5' ); } else { zc_icon( $icon, 'h-5 w-5' ); } ?>
									</span>
								<?php endif; ?>
								<p class="zc-list-text m-0 text-[0.94rem] leading-[1.9] text-muted"><?php echo esc_html( $text ); ?></p>
							</div>
						<?php endforeach; ?>
					</div>
				<?php else : ?>
					<ul class="zc-list-ul zc-list-items mt-6 <?php echo 'check' === $style ? 'zc-checklist' : 'zc-dashlist'; ?> <?php echo esc_attr( $grid_class ); ?>">
						<?php foreach ( $items as $item ) : ?>
							<?php
							$text = isset( $item['item_text'] ) ? trim( (string) $item['item_text'] ) : '';
							if ( '' === $text ) {
								continue;
							}
							?>
							<li<?php echo ( ! empty( $eicon ) || '' !== $icon ) ? ' class="zc-li-has-ic"' : ''; ?>><?php if ( ! empty( $eicon ) || '' !== $icon ) : ?><span class="zc-list-li-ic"><?php if ( ! empty( $eicon ) ) { $this->render_icon( $eicon, 'h-4 w-4' ); } else { zc_icon( $icon, 'h-4 w-4' ); } ?></span><?php endif; ?><?php echo esc_html( $text ); ?></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>
			<?php
		}
	}
endif;
