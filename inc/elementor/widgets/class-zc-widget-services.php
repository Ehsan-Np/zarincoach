<?php
/**
 * ویجت خدمات و برنامه‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Services' ) ) :

	/**
	 * ویجت خدمات.
	 */
	class ZC_Widget_Services extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-services';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'خدمات و برنامه‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-posts-grid';
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
					'label' => __( 'سربرگ', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->heading_controls();

			$this->end_controls_section();

			$this->start_controls_section(
				'query_section',
				array(
					'label' => __( 'منبع داده', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'source',
				array(
					'label'   => __( 'منبع', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'cpt'    => __( 'خدمات ثبت‌شده در سایت', 'zarincoach' ),
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
					'max'       => 24,
					'step'      => 1,
					'default'   => 6,
					'condition' => array( 'source' => 'cpt' ),
				)
			);

			$this->add_control(
				'category',
				array(
					'label'       => __( 'دسته‌بندی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'options'     => $this->get_service_categories(),
					'default'     => '',
					'multiple'    => false,
					'label_block' => true,
					'condition'   => array( 'source' => 'cpt' ),
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'item_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array( 'sparkles' => 'پیش‌فرض' ),
					'default' => 'target',
				)
			);

			$repeater->add_control(
				'item_title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_desc',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_price',
				array(
					'label'   => __( 'قیمت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_duration',
				array(
					'label'   => __( 'مدت زمان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_badge',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'item_url',
				array(
					'label'   => __( 'لینک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'آیتم‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ item_title }}}',
					'condition'   => array( 'source' => 'manual' ),
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );

			$this->add_control(
				'show_meta',
				array(
					'label'        => __( 'نمایش قیمت و مدت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'button_text',
				array(
					'label'   => __( 'متن لینک کارت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'جزئیات بیشتر', 'zarincoach' ),
				)
			);

			$this->add_control(
				'link_icon',
				array(
					'label'       => __( 'آیکن لینک کارت', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = فلش پیش‌فرض قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'excerpt_words',
				array(
					'label'   => __( 'تعداد واژه‌های توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 22,
					'min'     => 5,
					'max'     => 120,
					'description' => __( 'توضیح هر کارت تا این تعداد واژه کوتاه می‌شود.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				array(
					'label' => __( 'استایل', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'notch',
				array(
					'label'        => __( 'برش گوشه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'bg_style',
				array(
					'label'   => __( 'پس‌زمینه بخش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'none'    => __( 'بدون', 'zarincoach' ),
						'soft'    => __( 'ملایم', 'zarincoach' ),
					),
					'default' => 'soft',
				)
			);

			$this->end_controls_section();
		}

		/**
		 * فهرست دسته‌بندی‌های خدمات.
		 *
		 * @return array<int|string, string>
		 */
		protected function get_service_categories() {
			$options = array( '' => __( 'همه', 'zarincoach' ) );

			$terms = get_terms(
				array(
					'taxonomy'   => 'zc_service_cat',
					'hide_empty' => false,
				)
			);

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$options[ $term->slug ] = $term->name;
				}
			}

			return $options;
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'ribbon'   => array( __( 'روبان (برچسب گوشه)', 'zarincoach' ), '.zc-service-ribbon' ),
					'icon'     => array( __( 'آیکن', 'zarincoach' ), '.zc-service-icon' ),
					'desc'     => array( __( 'توضیح', 'zarincoach' ), '.zc-service-desc' ),
					'rule'     => array( __( 'خط جداکننده', 'zarincoach' ), '.zc-service-rule' ),
					'duration' => array( __( 'مدت جلسه', 'zarincoach' ), '.zc-service-duration' ),
					'price'    => array( __( 'قیمت', 'zarincoach' ), '.zc-service-price' ),
					'link'     => array( __( 'لینک جزئیات', 'zarincoach' ), '.zc-service-link' ),
				)
			);

			$this->zc_style(
				'svc_grid',
				__( 'شبکه‌ی کارت‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'grid', '.zc-services-grid', '', array( 'cols' => false ) ),
				)
			);
			$this->zc_style(
				'svc_card',
				__( 'کارت', 'zarincoach' ),
				array(
					'card'   => array( 'box', '.zc-service-card', '', array( 'hover' => true, 'align' => true, 'minh' => true ) ),
					'ribbon' => array( 'box', '.zc-service-ribbon', __( 'روبان', 'zarincoach' ), array( 'text' => true, 'gradient' => false ) ),
				)
			);
			$this->zc_style(
				'svc_icon',
				__( 'آیکن', 'zarincoach' ),
				array(
					'icon' => array( 'icon', '.zc-service-icon', '', array( 'hover' => '.zc-service-card' ) ),
				)
			);
			$this->zc_style(
				'svc_text',
				__( 'عنوان و توضیح', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-service-title, .zc-service-title a', __( 'عنوان', 'zarincoach' ), array( 'hover' => true ) ),
					'desc'  => array( 'text', '.zc-service-desc', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
				)
			);
			$this->zc_style(
				'svc_foot',
				__( 'پایین کارت (مدت، قیمت، لینک)', 'zarincoach' ),
				array(
					'rule'  => array( 'color', '.zc-service-rule', __( 'رنگ خط جداکننده', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'meta'  => array( 'text', '.zc-service-duration', __( 'مدت', 'zarincoach' ), array( 'margin' => false ) ),
					'micon' => array( 'color', '.zc-service-duration svg', __( 'رنگ آیکن مدت', 'zarincoach' ) ),
					'price' => array( 'text', '.zc-service-price', __( 'قیمت', 'zarincoach' ), array( 'margin' => false ) ),
					'link'  => array( 'text', '.zc-service-link', __( 'لینک', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
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
				zc_schema_hint( 'services', true ); // فهرست خدمات (OfferCatalog) در گراف همین صفحه.
			}
			$settings = $this->get_settings_for_display();

			$columns   = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
			$show_meta = ! isset( $settings['show_meta'] ) || 'yes' === (string) $settings['show_meta'];
			$notch     = ! isset( $settings['notch'] ) || 'yes' === (string) $settings['notch'];
			$bg        = isset( $settings['bg_style'] ) ? (string) $settings['bg_style'] : 'soft';
			$btn_text  = isset( $settings['button_text'] ) ? (string) $settings['button_text'] : '';
			$source    = isset( $settings['source'] ) ? (string) $settings['source'] : 'cpt';
			$words     = isset( $settings['excerpt_words'] ) && '' !== $settings['excerpt_words'] ? (int) $settings['excerpt_words'] : 22;
			$link_icon = isset( $settings['link_icon'] ) ? $settings['link_icon'] : array();

			$section_class = 'soft' === $bg ? 'zc-section relative bg-surface2/50' : 'zc-section relative';
			?>
			<section class="zc-services <?php echo esc_attr( $section_class ); ?>">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>

					<div class="zc-services-grid zc-after-head grid gap-6 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
						<?php
						$delay = 0;

						if ( 'manual' === $source ) {
							if ( empty( $settings['items'] ) ) {
								echo '<p class="zc-lead">' . esc_html__( 'موردی اضافه نشده است.', 'zarincoach' ) . '</p>';
							} else {
								foreach ( $settings['items'] as $item ) {
									$this->render_card(
										array(
											'title'    => isset( $item['item_title'] ) ? $item['item_title'] : '',
											'desc'     => isset( $item['item_desc'] ) ? $item['item_desc'] : '',
											'icon'     => isset( $item['item_icon'] ) ? $item['item_icon'] : 'sparkles',
											'price'    => isset( $item['item_price'] ) ? $item['item_price'] : '',
											'duration' => isset( $item['item_duration'] ) ? $item['item_duration'] : '',
											'badge'    => isset( $item['item_badge'] ) ? $item['item_badge'] : '',
											'url'      => isset( $item['item_url']['url'] ) ? $item['item_url']['url'] : '',
										),
										array(
											'notch'     => $notch,
											'show_meta' => $show_meta,
											'button'    => $btn_text,
											'delay'     => $delay,
											'words'     => $words,
											'icon'      => $link_icon,
										)
									);
									$delay += 80;
								}
							}
						} else {
							$args = array(
								'post_type'           => 'zc_service',
								'posts_per_page'      => isset( $settings['count'] ) ? (int) $settings['count'] : 6,
								'post_status'         => 'publish',
								'ignore_sticky_posts' => true,
								'no_found_rows'       => true,
								'meta_key'            => '_zc_service_order',
								'orderby'             => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ),
							);

							if ( ! empty( $settings['category'] ) ) {
								$args['tax_query'] = array(
									array(
										'taxonomy' => 'zc_service_cat',
										'field'    => 'slug',
										'terms'    => (string) $settings['category'],
									),
								);
							}

							$query = new WP_Query( $args );

							if ( ! $query->have_posts() ) {
								echo '<p class="zc-lead">' . esc_html__( 'خدمتی ثبت نشده است.', 'zarincoach' ) . '</p>';
							} else {
								while ( $query->have_posts() ) {
									$query->the_post();
									$service_id = get_the_ID();

									$this->render_card(
										array(
											'title'    => get_the_title( $service_id ),
											'desc'     => get_the_excerpt( $service_id ) ?: get_post_field( 'post_content', $service_id ),
											'icon'     => (string) get_post_meta( $service_id, '_zc_service_icon', true ),
											'price'    => (string) get_post_meta( $service_id, '_zc_service_price', true ),
											'duration' => (string) get_post_meta( $service_id, '_zc_service_duration', true ),
											'badge'    => (string) get_post_meta( $service_id, '_zc_service_badge', true ),
											'url'      => get_permalink( $service_id ),
										),
										array(
											'notch'     => $notch,
											'show_meta' => $show_meta,
											'button'    => $btn_text,
											'delay'     => $delay,
											'words'     => $words,
											'icon'      => $link_icon,
										)
									);

									$delay += 80;
								}
								wp_reset_postdata();
							}
						}
						?>
					</div>
				</div>
			</section>
			<?php
		}

		/**
		 * خروجی هر کارت خدمت.
		 *
		 * @param array $item   داده‌های کارت.
		 * @param array $config تنظیمات نمایش.
		 * @return void
		 */
		protected function render_card( $item, $config ) {
			$notch_class = ! empty( $config['notch'] ) ? 'zc-notch' : '';
			?>
			<article class="zc-service-card zc-card <?php echo esc_attr( $notch_class ); ?> zc-card-hover zc-reveal flex flex-col group" data-zc-delay="<?php echo esc_attr( (string) (int) $config['delay'] ); ?>">
				<?php if ( '' !== $item['badge'] ) : ?>
					<span class="zc-service-ribbon zc-ribbon !end-0 !top-0 rounded-ss-none rounded-ee-none"><?php echo esc_html( $item['badge'] ); ?></span>
				<?php endif; ?>

				<span class="zc-service-icon mb-5 grid h-14 w-14 place-items-center rounded-2xl bg-primary/10 text-primary transition-transform duration-500 group-hover:scale-110 group-hover:bg-primary group-hover:text-white">
					<?php zc_icon( '' !== $item['icon'] ? $item['icon'] : 'sparkles', 'h-7 w-7' ); ?>
				</span>

				<h3 class="zc-service-title text-[1.15rem] font-bold leading-snug text-secondary">
					<?php if ( '' !== $item['url'] ) : ?>
						<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $item['url'] ); ?>"><?php echo esc_html( $item['title'] ); ?></a>
					<?php else : ?>
						<?php echo esc_html( $item['title'] ); ?>
					<?php endif; ?>
				</h3>

				<p class="zc-service-desc zc-lead mt-3 text-[0.92rem]"><?php echo esc_html( zc_excerpt( $item['desc'], max( 5, (int) $config['words'] ) ) ); ?></p>

				<div class="zc-service-foot mt-auto pt-6">
					<div class="zc-service-rule zc-rule mb-4"></div>
					<div class="zc-service-foot-row flex items-center justify-between gap-3">
						<?php if ( ! empty( $config['show_meta'] ) && ( '' !== $item['price'] || '' !== $item['duration'] ) ) : ?>
							<div class="zc-service-meta text-[0.82rem] text-muted">
								<?php if ( '' !== $item['duration'] ) : ?>
									<span class="zc-service-duration inline-flex items-center gap-1.5">
										<?php zc_icon( 'clock', 'h-4 w-4 text-primary' ); ?>
										<?php echo esc_html( $item['duration'] ); ?>
									</span>
								<?php endif; ?>
								<?php if ( '' !== $item['price'] ) : ?>
									<span class="zc-service-price ms-3 font-bold text-secondary"><?php echo esc_html( $item['price'] ); ?></span>
								<?php endif; ?>
							</div>
						<?php endif; ?>

						<?php if ( '' !== $item['url'] && '' !== $config['button'] ) : ?>
							<a class="zc-service-link inline-flex items-center gap-1.5 text-[0.85rem] font-bold text-primary transition-all hover:gap-2.5" href="<?php echo esc_url( $item['url'] ); ?>">
								<?php echo esc_html( $config['button'] ); ?>
								<?php $this->zc_render_icon_or( $config['icon'], 'arrow-left', 'h-4 w-4' ); ?>
							</a>
						<?php endif; ?>
					</div>
				</div>
			</article>
			<?php
		}
	}
endif;
