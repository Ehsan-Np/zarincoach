<?php
/**
 * ویجت تجربه مراجعان
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Testimonials' ) ) :

	/**
	 * ویجت نظرات.
	 */
	class ZC_Widget_Testimonials extends ZC_Widget_Base {

		/**
		 * آیکن نقل‌قول انتخابی (برای render_card).
		 *
		 * @var mixed
		 */
		protected $zc_quote_icon = array();

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-testimonials';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'تجربه مراجعان', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-testimonial';
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
						'cpt'    => __( 'نظرات ثبت‌شده', 'zarincoach' ),
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
					'default'   => 6,
					'condition' => array( 'source' => 'cpt' ),
				)
			);

			$this->add_control(
				'excerpt_words',
				array(
					'label'   => __( 'تعداد واژه‌های متن نظر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 45,
					'min'     => 10,
					'max'     => 300,
					'description' => __( 'متن نظرهای ثبت‌شده در بخش «نظرات مراجعان» تا این تعداد واژه کوتاه می‌شود.', 'zarincoach' ),
				)
			);

			$repeater = new \Elementor\Repeater();

			$repeater->add_control(
				'person_name',
				array(
					'label'   => __( 'نام', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'person_role',
				array(
					'label'   => __( 'نقش / عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$repeater->add_control(
				'person_text',
				array(
					'label'   => __( 'متن نظر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 4,
					'default' => '',
				)
			);

			$repeater->add_control(
				'person_rating',
				array(
					'label'   => __( 'امتیاز', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 5,
					'default' => 5,
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'نظرات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ person_name }}}',
					'condition'   => array( 'source' => 'manual' ),
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک نمایش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'grid'   => __( 'شبکه‌ای', 'zarincoach' ),
						'slider' => __( 'اسلایدر', 'zarincoach' ),
					),
					'default' => 'grid',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );
			$this->update_control( 'columns', array( 'condition' => array( 'style' => 'grid' ) ) );

			$this->add_control(
				'show_rating',
				array(
					'label'        => __( 'نمایش ستاره‌ها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'quote_icon',
				array(
					'label'       => __( 'آیکن نقل‌قول', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = آیکن پیش‌فرض قالب.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'source_note',
				array(
					'label'       => __( 'یادداشت منبع نظرات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 2,
					'default'     => '',
					'placeholder' => __( 'خالی = متن ثبت‌شده در تنظیمات قالب (بخش نظرات)', 'zarincoach' ),
					'separator'   => 'before',
				)
			);

			$this->add_control(
				'source_label',
				array(
					'label'       => __( 'متن پیوند منبع', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'مشاهده همه نظرات', 'zarincoach' ),
				)
			);

			$this->add_control(
				'source_url',
				array(
					'label'       => __( 'نشانی منبع نظرات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'default'     => array( 'url' => '' ),
					'description' => __( 'خالی = نشانی ثبت‌شده در تنظیمات قالب. برای پنهان کردن، در تنظیمات قالب خالی بگذارید.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'slider_section',
				array(
					'label'     => __( 'اسلایدر', 'zarincoach' ),
					'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
					'condition' => array( 'style' => 'slider' ),
				)
			);

			$per_view = array(
				'1' => __( '۱ نظر', 'zarincoach' ),
				'2' => __( '۲ نظر', 'zarincoach' ),
				'3' => __( '۳ نظر', 'zarincoach' ),
			);

			$this->add_control(
				'per_view',
				array(
					'label'   => __( 'تعداد در هر نما (دسکتاپ)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $per_view,
					'default' => '3',
				)
			);

			$this->add_control(
				'per_view_tablet',
				array(
					'label'   => __( 'تعداد در هر نما (تبلت)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $per_view,
					'default' => '2',
				)
			);

			$this->add_control(
				'per_view_mobile',
				array(
					'label'   => __( 'تعداد در هر نما (موبایل)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_slice( $per_view, 0, 2, true ),
					'default' => '1',
				)
			);

			$this->add_control(
				'arrows',
				array(
					'label'        => __( 'دکمه‌های نظر قبلی / بعدی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'prev_icon',
				array(
					'label'       => __( 'آیکن دکمه‌ی قبلی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = فلش پیش‌فرض.', 'zarincoach' ),
					'condition'   => array( 'arrows' => 'yes' ),
				)
			);

			$this->add_control(
				'next_icon',
				array(
					'label'       => __( 'آیکن دکمه‌ی بعدی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = فلش پیش‌فرض.', 'zarincoach' ),
					'condition'   => array( 'arrows' => 'yes' ),
				)
			);

			$this->add_control(
				'dots',
				array(
					'label'        => __( 'صفحه‌بندی (نقطه‌ها)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'loop',
				array(
					'label'        => __( 'چرخش پیوسته (بازگشت به اول)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'autoplay',
				array(
					'label'        => __( 'پخش خودکار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'با قرار گرفتن ماوس یا فوکوس روی اسلایدر و خارج شدن آن از دید، متوقف می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'interval',
				array(
					'label'     => __( 'فاصله‌ی پخش خودکار (ثانیه)', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 2,
					'max'       => 30,
					'step'      => 0.5,
					'default'   => 6,
					'condition' => array( 'autoplay' => 'yes' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * کارت یک نظر.
		 *
		 * @param array $item        نظر.
		 * @param bool  $show_rating نمایش ستاره.
		 * @param bool  $reveal      انیمیشن ورود.
		 * @param int   $delay       تأخیر انیمیشن.
		 * @return void
		 */
		protected function render_card( $item, $show_rating, $reveal = true, $delay = 0 ) {
			?>
			<figure class="zc-card zc-card-hover zc-testimonial-card flex h-full flex-col p-6<?php echo $reveal ? ' zc-reveal' : ''; ?>"<?php echo $reveal ? ' data-zc-delay="' . esc_attr( (string) $delay ) . '"' : ''; ?>>
				<div class="zc-t-top mb-4 flex items-center justify-between">
					<?php if ( $show_rating ) : ?>
						<?php zc_stars( $item['rating'] > 0 ? $item['rating'] : 5 ); ?>
					<?php endif; ?>
					<span class="zc-t-quote-icon text-primary/40"><?php $this->zc_render_icon_or( isset( $this->zc_quote_icon ) ? $this->zc_quote_icon : array(), 'quote', 'h-6 w-6' ); ?></span>
				</div>

				<blockquote class="zc-t-quote zc-quote flex-1">
					<p class="zc-t-text m-0"><?php echo esc_html( $item['text'] ); ?></p>
				</blockquote>

				<figcaption class="zc-t-caption mt-6 flex items-center gap-3 border-t border-line pt-5">
					<span class="zc-t-avatar grid h-11 w-11 shrink-0 place-items-center rounded-full bg-primary/10 text-sm font-bold text-primary">
						<?php echo esc_html( mb_substr( $item['name'], 0, 1 ) ); ?>
					</span>
					<span>
						<span class="zc-t-name block text-[0.92rem] font-bold text-secondary"><?php echo esc_html( $item['name'] ); ?></span>
						<?php if ( '' !== $item['role'] ) : ?>
							<span class="zc-t-role block text-[0.78rem] text-muted"><?php echo esc_html( $item['role'] ); ?></span>
						<?php endif; ?>
					</span>
				</figcaption>
			</figure>
			<?php
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'stars'  => array( __( 'ستاره‌ها', 'zarincoach' ), '.zc-stars' ),
					'quote'  => array( __( 'آیکن نقل‌قول', 'zarincoach' ), '.zc-t-quote-icon' ),
					'avatar' => array( __( 'حرف اول نام (آواتار)', 'zarincoach' ), '.zc-t-avatar' ),
					'role'   => array( __( 'عنوان/نقش مراجع', 'zarincoach' ), '.zc-t-role' ),
					'source' => array( __( 'یادداشت منبع نظرات', 'zarincoach' ), '.zc-testimonials-source' ),
				)
			);
			$this->zc_style(
				'tst_grid',
				__( 'شبکه‌ی کارت‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'grid', '.zc-testimonials-grid', '', array( 'cols' => false ) ),
				)
			);
			$this->zc_style(
				'tst_card',
				__( 'کارت نظر', 'zarincoach' ),
				array(
					'card'  => array( 'box', '.zc-testimonial-card', __( 'کارت', 'zarincoach' ), array( 'hover' => true, 'minh' => true ) ),
					'stars' => array( 'color', '.zc-stars', __( 'رنگ ستاره‌های پر', 'zarincoach' ) ),
					'empty' => array( 'color', '.zc-stars svg.text-line', __( 'رنگ ستاره‌های خالی', 'zarincoach' ) ),
					'ssize' => array( 'size', '.zc-stars svg', __( 'اندازه‌ی ستاره', 'zarincoach' ), array( 'max' => 48, 'css' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ),
					'quote' => array( 'icon', '.zc-t-quote-icon', __( 'آیکن نقل‌قول', 'zarincoach' ), array( 'box' => false ) ),
					'text'  => array( 'text', '.zc-t-text', __( 'متن نظر', 'zarincoach' ), array( 'align' => true ) ),
				)
			);
			$this->zc_style(
				'tst_person',
				__( 'نام و آواتار', 'zarincoach' ),
				array(
					'caption' => array( 'box', '.zc-t-caption', __( 'نوار پایین کارت', 'zarincoach' ), array( 'gradient' => false ) ),
					'avatar'  => array( 'icon', '.zc-t-avatar', __( 'آواتار', 'zarincoach' ) ),
					'name'    => array( 'text', '.zc-t-name', __( 'نام', 'zarincoach' ), array( 'margin' => false ) ),
					'role'    => array( 'text', '.zc-t-role', __( 'نقش', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);
			$this->zc_style(
				'tst_slider',
				__( 'دکمه‌ها و نقطه‌های اسلایدر', 'zarincoach' ),
				array(
					'arrow' => array( 'button', '.zc-slider-arrow', __( 'دکمه‌های قبلی/بعدی', 'zarincoach' ) ),
					'dot'   => array( 'color', '.zc-slider-dot::before', __( 'رنگ نقطه', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'doton' => array( 'color', '.zc-slider-dot.is-active::before', __( 'رنگ نقطه‌ی فعال', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'nav'   => array( 'size', '.zc-slider-nav', __( 'فاصله از کارت‌ها', 'zarincoach' ), array( 'prop' => 'margin-top', 'max' => 100 ) ),
				),
				array( 'condition' => array( 'style' => 'slider' ) )
			);
			$this->zc_style(
				'tst_source',
				__( 'یادداشت منبع نظرات', 'zarincoach' ),
				array(
					'text' => array( 'text', '.zc-testimonials-source', '', array( 'align' => true ) ),
					'link' => array( 'text', '.zc-testimonials-source a', __( 'لینک', 'zarincoach' ), array( 'hover' => true, 'margin' => false, 'heading' => true ) ),
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

			$source      = isset( $settings['source'] ) ? (string) $settings['source'] : 'cpt';
			$style       = isset( $settings['style'] ) ? (string) $settings['style'] : 'grid';
			$columns     = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
			$show_rating = ! isset( $settings['show_rating'] ) || 'yes' === (string) $settings['show_rating'];
			$this->zc_quote_icon = isset( $settings['quote_icon'] ) ? $settings['quote_icon'] : array();

			$items = array();

			if ( 'cpt' === $source ) {
				$query = zc_get_testimonials( isset( $settings['count'] ) ? (int) $settings['count'] : 6 );

				while ( $query->have_posts() ) {
					$query->the_post();
					$item_id = get_the_ID();
					$items[] = array(
						'name'   => get_the_title( $item_id ),
						'role'   => (string) get_post_meta( $item_id, '_zc_testimonial_role', true ),
						'text'   => zc_excerpt( get_the_content( null, false, $item_id ), isset( $settings['excerpt_words'] ) && '' !== $settings['excerpt_words'] ? max( 10, (int) $settings['excerpt_words'] ) : 45 ),
						'rating' => (int) get_post_meta( $item_id, '_zc_testimonial_rating', true ),
					);
				}
				wp_reset_postdata();
			} elseif ( ! empty( $settings['items'] ) ) {
				foreach ( $settings['items'] as $item ) {
					$items[] = array(
						'name'   => isset( $item['person_name'] ) ? (string) $item['person_name'] : '',
						'role'   => isset( $item['person_role'] ) ? (string) $item['person_role'] : '',
						'text'   => isset( $item['person_text'] ) ? (string) $item['person_text'] : '',
						'rating' => isset( $item['person_rating'] ) ? (int) $item['person_rating'] : 5,
					);
				}
			}

			if ( empty( $items ) ) {
				echo '<p class="zc-lead">' . esc_html__( 'نظری ثبت نشده است.', 'zarincoach' ) . '</p>';
				return;
			}

			$delay = 0;
			?>
			<section class="zc-testimonials zc-section relative overflow-hidden">
				<div class="zc-container relative">
					<?php $this->render_heading( '', 'h2' ); ?>

					<?php
					if ( 'slider' === $style ) :
						$pv    = in_array( (string) ( $settings['per_view'] ?? '3' ), array( '1', '2', '3' ), true ) ? (string) $settings['per_view'] : '3';
						$pv_md = in_array( (string) ( $settings['per_view_tablet'] ?? '2' ), array( '1', '2', '3' ), true ) ? (string) $settings['per_view_tablet'] : '2';
						$pv_sm = in_array( (string) ( $settings['per_view_mobile'] ?? '1' ), array( '1', '2' ), true ) ? (string) $settings['per_view_mobile'] : '1';

						$arrows   = $this->is_on( $settings, 'arrows' );
						$dots     = $this->is_on( $settings, 'dots' );
						$loop     = $this->is_on( $settings, 'loop' );
						$autoplay = $this->is_on( $settings, 'autoplay' );
						$interval = isset( $settings['interval'] ) && '' !== (string) $settings['interval'] ? (float) $settings['interval'] : 6;
						$interval = (int) round( max( 2, min( 30, $interval ) ) * 1000 );
						$uid      = 'zc-slider-' . $this->get_id();
						?>
						<div
							id="<?php echo esc_attr( $uid ); ?>"
							class="zc-slider zc-reveal zc-after-head<?php echo '1' === $pv ? ' mx-auto max-w-3xl' : ''; ?>"
							style="<?php echo esc_attr( '--zc-spv:' . $pv . ';--zc-spv-md:' . $pv_md . ';--zc-spv-sm:' . $pv_sm ); ?>"
							data-zc-slider
							data-zc-loop="<?php echo $loop ? '1' : '0'; ?>"
							data-zc-autoplay="<?php echo $autoplay ? '1' : '0'; ?>"
							data-zc-interval="<?php echo esc_attr( (string) $interval ); ?>"
							role="region"
							aria-roledescription="<?php esc_attr_e( 'اسلایدر', 'zarincoach' ); ?>"
							aria-label="<?php esc_attr_e( 'تجربه مراجعان', 'zarincoach' ); ?>"
							data-zc-label-slide="<?php esc_attr_e( 'رفتن به نظر %d', 'zarincoach' ); ?>"
							data-zc-label-status="<?php esc_attr_e( 'نظر %1$d از %2$d', 'zarincoach' ); ?>"
						>
							<div class="zc-slider-viewport" data-zc-slider-viewport>
								<div class="zc-slider-track" data-zc-slider-track>
									<?php foreach ( $items as $i => $item ) : ?>
										<div class="zc-slider-slide" role="group" aria-roledescription="<?php esc_attr_e( 'اسلاید', 'zarincoach' ); ?>" aria-label="<?php echo esc_attr( zc_digits_to_persian( ( $i + 1 ) . ' / ' . count( $items ) ) ); ?>">
											<?php $this->render_card( $item, $show_rating, false ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>

							<?php if ( $arrows || $dots ) : ?>
								<div class="zc-slider-nav">
									<?php if ( $arrows ) : ?>
										<button type="button" class="zc-slider-arrow" data-zc-slider-prev aria-controls="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'نظر قبلی', 'zarincoach' ); ?>">
											<?php $this->zc_render_icon_or( isset( $settings['prev_icon'] ) ? $settings['prev_icon'] : array(), is_rtl() ? 'chevron-right' : 'chevron-left' ); ?>
										</button>
									<?php endif; ?>

									<?php if ( $dots ) : ?>
										<div class="zc-slider-dots" data-zc-slider-dots></div>
									<?php endif; ?>

									<?php if ( $arrows ) : ?>
										<button type="button" class="zc-slider-arrow" data-zc-slider-next aria-controls="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'نظر بعدی', 'zarincoach' ); ?>">
											<?php $this->zc_render_icon_or( isset( $settings['next_icon'] ) ? $settings['next_icon'] : array(), is_rtl() ? 'chevron-left' : 'chevron-right' ); ?>
										</button>
									<?php endif; ?>
								</div>
							<?php endif; ?>

							<p class="sr-only" aria-live="polite" data-zc-slider-status></p>
						</div>
					<?php else : ?>

						<div class="zc-testimonials-grid zc-after-head grid gap-6 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
							<?php
							foreach ( $items as $item ) {
								$this->render_card( $item, $show_rating, true, $delay );
								$delay += 80;
							}
							?>
						</div>

					<?php endif; ?>

					<?php $this->render_source( $settings ); ?>
				</div>
			</section>
			<?php
		}

		/**
		 * یادداشت شفاف درباره‌ی منبع نظرات (اصل صداقت در تبلیغات نظام روان‌شناسی).
		 *
		 * @param array<string, mixed> $settings تنظیمات ابزارک.
		 * @return void
		 */
		protected function render_source( $settings ) {
			$note  = trim( (string) ( $settings['source_note'] ?? '' ) );
			$label = trim( (string) ( $settings['source_label'] ?? '' ) );
			$url   = isset( $settings['source_url']['url'] ) ? trim( (string) $settings['source_url']['url'] ) : '';

			zc_testimonials_source( $note, $label, $url );
		}
	}
endif;
