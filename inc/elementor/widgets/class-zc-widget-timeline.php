<?php
/**
 * ویجت «خط زمانی سوابق»
 *
 * سوابق کاری / تحصیلی به‌صورت خط زمانی دوطرفه (زیگزاگ فشرده) یا یک‌طرفه،
 * با نشان «هم‌اکنون»، بازه‌ی زمانی، سازمان، برچسب و توضیح.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Timeline' ) ) :

	/**
	 * ویجت خط زمانی.
	 */
	class ZC_Widget_Timeline extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-timeline';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'خط زمانی سوابق', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-time-line';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'سوابق', 'timeline', 'رزومه', 'تجربه', 'experience' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section( 'content_section', array( 'label' => __( 'محتوا', 'zarincoach' ) ) );

			$this->heading_controls();

			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'xp_title',
				array(
					'label'   => __( 'سمت / عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_org',
				array(
					'label'   => __( 'سازمان / مرکز', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_start',
				array(
					'label'   => __( 'شروع', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_end',
				array(
					'label'   => __( 'پایان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_current',
				array(
					'label'        => __( 'هم‌اکنون ادامه دارد', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$repeater->add_control(
				'xp_desc',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_tag',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'xp_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array(),
					'default' => 'briefcase',
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'سوابق', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ xp_title }}} | {{{ xp_org }}}',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section( 'layout_section', array( 'label' => __( 'چیدمان', 'zarincoach' ) ) );

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'alternate' => __( 'دوطرفه (زیگزاگ)', 'zarincoach' ),
						'single'    => __( 'یک‌طرفه', 'zarincoach' ),
					),
					'default' => 'alternate',
				)
			);

			$this->add_control(
				'current_label',
				array(
					'label'   => __( 'متن نشان «هم‌اکنون»', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'هم‌اکنون', 'zarincoach' ),
				)
			);

			$this->add_control(
				'present_word',
				array(
					'label'   => __( 'واژه‌ی پایان بازه برای موارد جاری', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'اکنون', 'zarincoach' ),
				)
			);

			$this->add_control(
				'show_desc',
				array(
					'label'        => __( 'نمایش توضیحات', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
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
			$s       = $this->get_settings_for_display();
			$items   = ! empty( $s['items'] ) ? (array) $s['items'] : array();
			$layout  = isset( $s['layout'] ) && 'single' === $s['layout'] ? 'single' : 'alternate';
			$current = isset( $s['current_label'] ) ? (string) $s['current_label'] : '';
			$present = isset( $s['present_word'] ) ? (string) $s['present_word'] : '';

			if ( empty( $items ) ) {
				return;
			}
			?>
			<section class="zc-section zc-xp">
				<div class="zc-container">
					<?php $this->render_heading(); ?>

					<ol class="zc-xp-list is-<?php echo esc_attr( $layout ); ?> zc-after-head">
						<?php
						$row = 1;
						foreach ( $items as $item ) :
							$title = isset( $item['xp_title'] ) ? trim( (string) $item['xp_title'] ) : '';
							if ( '' === $title ) {
								continue;
							}
							$org    = isset( $item['xp_org'] ) ? trim( (string) $item['xp_org'] ) : '';
							$start  = isset( $item['xp_start'] ) ? trim( (string) $item['xp_start'] ) : '';
							$end    = isset( $item['xp_end'] ) ? trim( (string) $item['xp_end'] ) : '';
							$is_now = $this->is_on( $item, 'xp_current' );
							if ( $is_now && '' === $end ) {
								$end = $present;
							}
							$period = trim( $start . ( '' !== $start && '' !== $end ? ' — ' : '' ) . $end );
							$desc   = isset( $item['xp_desc'] ) ? trim( (string) $item['xp_desc'] ) : '';
							$tag    = isset( $item['xp_tag'] ) ? trim( (string) $item['xp_tag'] ) : '';
							$icon   = ! empty( $item['xp_icon'] ) ? (string) $item['xp_icon'] : 'briefcase';
							?>
							<li class="zc-xp-item<?php echo $is_now ? ' is-current' : ''; ?>" style="--zc-row: <?php echo (int) $row; ?>">
								<span class="zc-xp-dot" aria-hidden="true"><?php zc_icon( $icon, 'h-5 w-5' ); ?></span>
								<article class="zc-card zc-card-hover zc-xp-card zc-reveal">
									<div class="flex flex-wrap items-center gap-2">
										<?php if ( '' !== $period ) : ?>
											<span class="zc-xp-period"><?php zc_icon( 'calendar', 'h-3.5 w-3.5' ); ?><?php echo esc_html( $period ); ?></span>
										<?php endif; ?>
										<?php if ( $is_now && '' !== $current ) : ?>
											<span class="zc-xp-now"><i aria-hidden="true"></i><?php echo esc_html( $current ); ?></span>
										<?php endif; ?>
									</div>
									<h3 class="mt-3 text-[1.08rem] font-bold leading-[1.7] text-secondary"><?php echo esc_html( $title ); ?></h3>
									<?php if ( '' !== $org ) : ?>
										<p class="mt-1 flex items-start gap-1.5 text-[0.9rem] text-primary"><?php zc_icon( 'building', 'mt-1 h-4 w-4 shrink-0' ); ?><span><?php echo esc_html( $org ); ?></span></p>
									<?php endif; ?>
									<?php if ( '' !== $desc && $this->is_on( $s, 'show_desc' ) ) : ?>
										<p class="mt-3 text-[0.9rem] leading-[1.95] text-muted"><?php echo esc_html( $desc ); ?></p>
									<?php endif; ?>
									<?php if ( '' !== $tag ) : ?>
										<span class="zc-chip mt-4"><?php echo esc_html( $tag ); ?></span>
									<?php endif; ?>
								</article>
							</li>
							<?php
							++$row;
						endforeach;
						?>
					</ol>
				</div>
			</section>
			<?php
		}
	}
endif;
