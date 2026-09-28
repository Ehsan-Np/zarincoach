<?php
/**
 * ویجت بسته‌های همراهی (جدول قیمت)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Pricing' ) ) :

	/**
	 * ویجت بسته‌ها.
	 */
	class ZC_Widget_Pricing extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-pricing';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'بسته‌های همراهی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-price-table';
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
				'plan_name',
				array(
					'label'   => __( 'نام بسته', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'plan_desc',
				array(
					'label'   => __( 'توضیح کوتاه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);

			$repeater->add_control(
				'plan_price',
				array(
					'label'   => __( 'قیمت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'plan_period',
				array(
					'label'       => __( 'دوره', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'مثال: هر جلسه', 'zarincoach' ),
				)
			);

			$repeater->add_control(
				'plan_features',
				array(
					'label'       => __( 'ویژگی‌ها (هر خط یک مورد)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 5,
					'default'     => '',
				)
			);

			$repeater->add_control(
				'plan_badge',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'plan_featured',
				array(
					'label'        => __( 'ویژه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$repeater->add_control(
				'plan_button_text',
				array(
					'label'   => __( 'متن دکمه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'رزرو و مشاوره', 'zarincoach' ),
				)
			);

			$repeater->add_control(
				'plan_button_url',
				array(
					'label'   => __( 'لینک دکمه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '#booking' ),
				)
			);

			$repeater->add_control( 'on', array( 'label' => __( 'نمایش این پلن', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );

			$this->add_control(
				'plans',
				array(
					'label'       => __( 'بسته‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ plan_name }}}',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );

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
					'ribbon'   => array( __( 'روبان بسته', 'zarincoach' ), '.zc-plan-ribbon' ),
					'desc'     => array( __( 'توضیح بسته', 'zarincoach' ), '.zc-plan-desc' ),
					'period'   => array( __( 'دوره/واحد قیمت', 'zarincoach' ), '.zc-plan-period' ),
					'rule'     => array( __( 'خط جداکننده', 'zarincoach' ), '.zc-plan-rule' ),
					'features' => array( __( 'فهرست امکانات', 'zarincoach' ), '.zc-plan-features' ),
					'button'   => array( __( 'دکمه', 'zarincoach' ), '.zc-plan-cta' ),
				)
			);
			$this->zc_style(
				'plan_grid',
				__( 'شبکه‌ی بسته‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'grid', '.zc-pricing-grid', '', array( 'cols' => false, 'valign' => true ) ),
				)
			);
			$this->zc_style(
				'plan_card',
				__( 'کارت بسته', 'zarincoach' ),
				array(
					'card'     => array( 'box', '.zc-plan-card:not(.is-featured)', __( 'کارت عادی', 'zarincoach' ), array( 'hover' => true, 'align' => true ) ),
					'featured' => array( 'box', '.zc-plan-card.is-featured', __( 'کارت ویژه', 'zarincoach' ), array( 'hover' => true, 'align' => true ) ),
					'lift'     => array( 'size', '.zc-plan-card.is-featured', __( 'بالا آمدن کارت ویژه (دسکتاپ)', 'zarincoach' ), array( 'max' => 60, 'css' => 'transform: translateY(calc(-1 * {{SIZE}}{{UNIT}}));' ) ),
					'ribbon'   => array( 'box', '.zc-plan-ribbon', __( 'روبان', 'zarincoach' ), array( 'text' => true, 'gradient' => false ) ),
				)
			);
			$this->zc_style(
				'plan_text',
				__( 'نام، توضیح و قیمت', 'zarincoach' ),
				array(
					'name'   => array( 'text', '.zc-plan-name', __( 'نام بسته', 'zarincoach' ) ),
					'desc'   => array( 'text', '.zc-plan-desc', __( 'توضیح', 'zarincoach' ) ),
					'price'  => array( 'text', '.zc-plan-price', __( 'قیمت', 'zarincoach' ), array( 'margin' => false ) ),
					'period' => array( 'text', '.zc-plan-period', __( 'دوره', 'zarincoach' ), array( 'margin' => false ) ),
					'rule'   => array( 'color', '.zc-plan-rule', __( 'رنگ خط جداکننده', 'zarincoach' ), array( 'prop' => 'background' ) ),
				)
			);
			$this->zc_style(
				'plan_list',
				__( 'امکانات و دکمه', 'zarincoach' ),
				array(
					'list' => array( 'list', '.zc-plan-features li', __( 'فهرست امکانات', 'zarincoach' ), array( 'list' => '.zc-plan-features', 'marker' => '.zc-plan-features li::before' ) ),
					'btn'  => array( 'button', '.zc-plan-btn', __( 'دکمه', 'zarincoach' ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			if ( function_exists( 'zc_schema_hint' ) ) {
				zc_schema_hint( 'services', true );
			}
			$settings = $this->get_settings_for_display();

			$plans   = ! empty( $settings['plans'] ) ? $settings['plans'] : array();
			$columns = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';

			$delay = 0;
			?>
			<section class="zc-pricing zc-section relative">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php if ( ! empty( $plans ) ) : ?>
						<div class="zc-pricing-grid zc-after-head grid gap-6 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
							<?php foreach ( $plans as $plan ) : ?>
								<?php
								if ( isset( $plan['on'] ) && 'yes' !== (string) $plan['on'] ) { continue; }
								$name     = isset( $plan['plan_name'] ) ? (string) $plan['plan_name'] : '';
								$desc     = isset( $plan['plan_desc'] ) ? (string) $plan['plan_desc'] : '';
								$price    = isset( $plan['plan_price'] ) ? (string) $plan['plan_price'] : '';
								$period   = isset( $plan['plan_period'] ) ? (string) $plan['plan_period'] : '';
								$badge    = isset( $plan['plan_badge'] ) ? (string) $plan['plan_badge'] : '';
								$featured = isset( $plan['plan_featured'] ) && 'yes' === (string) $plan['plan_featured'];
								$features = isset( $plan['plan_features'] ) ? array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $plan['plan_features'] ) ) ) : array();
								$btn_text = isset( $plan['plan_button_text'] ) ? (string) $plan['plan_button_text'] : '';
								$btn_url  = isset( $plan['plan_button_url']['url'] ) ? (string) $plan['plan_button_url']['url'] : '';

								$card_class = $featured
									? 'zc-plan-card is-featured zc-card zc-reveal flex flex-col border-primary/40 bg-surface shadow-lift ring-1 ring-primary/20 lg:-translate-y-3'
									: 'zc-plan-card zc-card zc-card-hover zc-reveal flex flex-col';
								?>
								<div class="<?php echo esc_attr( $card_class ); ?>" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
									<?php if ( '' !== $badge ) : ?>
										<span class="zc-plan-ribbon zc-ribbon !end-4 !top-4"><?php echo esc_html( $badge ); ?></span>
									<?php endif; ?>

									<h3 class="zc-plan-name text-[1.1rem] font-bold text-secondary"><?php echo esc_html( $name ); ?></h3>

									<?php if ( '' !== $desc ) : ?>
										<p class="zc-plan-desc zc-lead mt-2 text-[0.88rem]"><?php echo esc_html( $desc ); ?></p>
									<?php endif; ?>

									<?php if ( '' !== $price ) : ?>
										<div class="zc-plan-price-row mt-5 flex items-end gap-2">
											<span class="zc-plan-price zc-price-num"><?php echo esc_html( $price ); ?></span>
											<?php if ( '' !== $period ) : ?>
												<span class="zc-plan-period pb-1 text-[0.8rem] text-muted"><?php echo esc_html( $period ); ?></span>
											<?php endif; ?>
										</div>
									<?php endif; ?>

									<div class="zc-plan-rule zc-rule my-6"></div>

									<?php if ( ! empty( $features ) ) : ?>
										<ul class="zc-plan-features zc-checklist flex-1">
											<?php foreach ( $features as $feature ) : ?>
												<li><?php echo esc_html( $feature ); ?></li>
											<?php endforeach; ?>
										</ul>
									<?php endif; ?>

									<?php if ( '' !== $btn_text && '' !== $btn_url ) : ?>
										<div class="zc-plan-cta mt-7">
											<?php
											zc_button(
												array(
													'text'  => $btn_text,
													'url'   => $btn_url,
													'style' => $featured ? 'primary' : 'outline',
													'class' => 'w-full zc-plan-btn',
												)
											);
											?>
										</div>
									<?php endif; ?>
								</div>
								<?php
								$delay += 90;
							endforeach;
							?>
						</div>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
