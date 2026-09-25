<?php
/**
 * ویجت محصولات (شبکه / اسلایدر)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Products' ) && class_exists( 'ZC_Shop_Widget_Base' ) ) :

	/**
	 * محصولات فروشگاه با کارت اختصاصی قالب.
	 */
	class ZC_Widget_Products extends ZC_Shop_Widget_Base {

		/**
		 * نام.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-products';
		}

		/**
		 * عنوان.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'محصولات فروشگاه', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-products';
		}

		/**
		 * کنترل‌ها.
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
					'label'     => __( 'منبع محصولات', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'separator' => 'before',
					'options'   => array(
						'recent'       => __( 'جدیدترین', 'zarincoach' ),
						'featured'     => __( 'محصولات ویژه', 'zarincoach' ),
						'sale'         => __( 'تخفیف‌دار', 'zarincoach' ),
						'best_selling' => __( 'پرفروش‌ترین', 'zarincoach' ),
						'top_rated'    => __( 'بیشترین امتیاز', 'zarincoach' ),
						'category'     => __( 'از دسته‌های انتخابی', 'zarincoach' ),
						'manual'       => __( 'انتخاب دستی', 'zarincoach' ),
					),
					'default'   => 'recent',
				)
			);

			$this->add_control(
				'categories',
				array(
					'label'       => __( 'دسته‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => $this->category_options(),
					'condition'   => array( 'source' => 'category' ),
				)
			);

			$this->add_control(
				'ids',
				array(
					'label'       => __( 'محصولات', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => $this->product_options(),
					'description' => __( 'به همین ترتیب نمایش داده می‌شوند.', 'zarincoach' ),
					'condition'   => array( 'source' => 'manual' ),
				)
			);

			$this->add_control(
				'count',
				array(
					'label'     => __( 'تعداد', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 24,
					'default'   => 8,
					'condition' => array( 'source!' => 'manual' ),
				)
			);

			$this->add_control(
				'orderby',
				array(
					'label'     => __( 'مرتب‌سازی', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'date'       => __( 'جدیدترین', 'zarincoach' ),
						'menu_order' => __( 'ترتیب دستی', 'zarincoach' ),
						'price'      => __( 'ارزان‌ترین', 'zarincoach' ),
						'price-desc' => __( 'گران‌ترین', 'zarincoach' ),
						'popularity' => __( 'پرفروش‌ترین', 'zarincoach' ),
						'rand'       => __( 'تصادفی', 'zarincoach' ),
					),
					'default'   => 'date',
					'condition' => array( 'source' => array( 'recent', 'featured', 'sale', 'category' ) ),
				)
			);

			$this->add_control(
				'hide_out',
				array(
					'label'        => __( 'پنهان کردن ناموجودها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'layout_section',
				array(
					'label' => __( 'چیدمان', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'نمایش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'grid'     => __( 'شبکه‌ای', 'zarincoach' ),
						'carousel' => __( 'اسلایدر', 'zarincoach' ),
					),
					'default' => 'grid',
				)
			);

			$cols = array(
				'1' => '۱',
				'2' => '۲',
				'3' => '۳',
				'4' => '۴',
				'5' => '۵',
			);
			$this->add_control(
				'cols',
				array(
					'label'   => __( 'ستون (دسکتاپ)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_slice( $cols, 1, null, true ),
					'default' => '4',
				)
			);
			$this->add_control(
				'cols_md',
				array(
					'label'   => __( 'ستون (تبلت)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_slice( $cols, 1, 3, true ),
					'default' => '3',
				)
			);
			$this->add_control(
				'cols_sm',
				array(
					'label'   => __( 'ستون (موبایل)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_slice( $cols, 0, 2, true ),
					'default' => '2',
				)
			);

			foreach (
				array(
					'arrows'   => array( __( 'فلش‌ها', 'zarincoach' ), 'yes' ),
					'dots'     => array( __( 'نقطه‌ها', 'zarincoach' ), '' ),
					'autoplay' => array( __( 'پخش خودکار', 'zarincoach' ), '' ),
				) as $key => $c
			) {
				$this->add_control(
					$key,
					array(
						'label'        => $c[0],
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => 'yes',
						'default'      => $c[1],
						'condition'    => array( 'layout' => 'carousel' ),
					)
				);
			}

			$this->add_control(
				'card_heading',
				array(
					'label'     => __( 'کارت محصول', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);
			foreach (
				array(
					'show_cat'     => array( __( 'دسته', 'zarincoach' ), 'yes' ),
					'show_kind'    => array( __( 'برچسب نوع تحویل', 'zarincoach' ), 'yes' ),
					'show_rating'  => array( __( 'امتیاز', 'zarincoach' ), 'yes' ),
					'show_excerpt' => array( __( 'توضیح کوتاه', 'zarincoach' ), '' ),
					'show_hover'   => array( __( 'تصویر دوم هنگام hover', 'zarincoach' ), 'yes' ),
				) as $key => $c
			) {
				$this->add_control(
					$key,
					array(
						'label'        => $c[0],
						'type'         => \Elementor\Controls_Manager::SWITCHER,
						'return_value' => 'yes',
						'default'      => $c[1],
					)
				);
			}

			$this->button_controls( '', __( 'دکمه‌ی «مشاهده همه»', 'zarincoach' ) );

			$this->end_controls_section();
		}

		/**
		 * پرس‌وجوی محصولات.
		 *
		 * @param array $s تنظیمات.
		 * @return WC_Product[]
		 */
		protected function query_products( $s ) {
			$source = isset( $s['source'] ) ? (string) $s['source'] : 'recent';
			$count  = max( 1, min( 24, (int) ( $s['count'] ?? 8 ) ) );
			$args   = array(
				'status'     => 'publish',
				'visibility' => 'catalog',
				'limit'      => $count,
				'return'     => 'objects',
			);
			if ( $this->is_on( $s, 'hide_out' ) ) {
				$args['stock_status'] = 'instock';
			}

			$orderby = isset( $s['orderby'] ) ? (string) $s['orderby'] : 'date';
			switch ( $orderby ) {
				case 'price':
				case 'price-desc':
					$args['orderby'] = 'price';
					$args['order']   = 'price' === $orderby ? 'ASC' : 'DESC';
					break;
				case 'popularity':
					$args['orderby'] = 'popularity';
					break;
				case 'menu_order':
					$args['orderby'] = 'menu_order';
					$args['order']   = 'ASC';
					break;
				case 'rand':
					$args['orderby'] = 'rand';
					break;
				default:
					$args['orderby'] = 'date';
					$args['order']   = 'DESC';
			}

			switch ( $source ) {
				case 'featured':
					$args['featured'] = true;
					break;
				case 'sale':
					$ids             = wc_get_product_ids_on_sale();
					$args['include'] = $ids ? $ids : array( 0 );
					break;
				case 'best_selling':
					$args['orderby'] = 'popularity';
					break;
				case 'top_rated':
					$args['orderby']  = 'meta_value_num';
					$args['meta_key'] = '_wc_average_rating'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
					$args['order']    = 'DESC';
					break;
				case 'category':
					$cats = array_filter( (array) ( $s['categories'] ?? array() ) );
					if ( $cats ) {
						$args['category'] = array_map( 'sanitize_title', $cats );
					}
					break;
				case 'manual':
					$ids = array_filter( array_map( 'absint', (array) ( $s['ids'] ?? array() ) ) );
					if ( ! $ids ) {
						return array();
					}
					$args['include'] = $ids;
					$args['limit']   = count( $ids );
					$args['orderby'] = 'include';
					unset( $args['order'] );
					break;
			}

			$products = wc_get_products( $args );
			return is_array( $products ) ? $products : array();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s        = $this->get_settings_for_display();
			$products = $this->query_products( $s );
			if ( ! $products ) {
				$this->editor_notice( __( 'محصولی برای نمایش یافت نشد. منبع یا دسته را تغییر دهید.', 'zarincoach' ) );
				return;
			}

			$prev_product = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
			$title    = trim( (string) ( $s['title'] ?? '' ) );
			$card     = array(
				'title_tag' => '' !== $title ? 'h3' : 'h2',
				'excerpt'   => $this->is_on( $s, 'show_excerpt' ),
				'hover'     => $this->is_on( $s, 'show_hover' ),
				'rating'    => $this->is_on( $s, 'show_rating' ),
				'kind'      => $this->is_on( $s, 'show_kind' ),
				'cat'       => $this->is_on( $s, 'show_cat' ),
			);
			$layout   = 'carousel' === ( $s['layout'] ?? 'grid' ) ? 'carousel' : 'grid';
			$cols     = max( 2, min( 5, (int) ( $s['cols'] ?? 4 ) ) );
			$cols_md  = max( 2, min( 3, (int) ( $s['cols_md'] ?? 3 ) ) );
			$cols_sm  = max( 1, min( 2, (int) ( $s['cols_sm'] ?? 2 ) ) );
			$align    = (string) ( $s['align'] ?? 'center' );
			$has_head = '' !== $title || '' !== trim( (string) ( $s['eyebrow'] ?? '' ) ) || '' !== trim( (string) ( $s['subtitle'] ?? '' ) );
			$button   = trim( (string) ( $s['button_text'] ?? '' ) ) !== '' && ! empty( $s['button_url']['url'] );
			?>
			<section class="zc-section zc-products-wrap woocommerce">
				<div class="zc-container">
					<?php if ( $has_head || $button ) : ?>
						<div class="zc-products-head<?php echo 'center' === $align ? ' is-center' : ''; ?>">
							<?php $this->render_heading( '', 'h2' ); ?>
							<?php if ( $button && 'center' !== $align ) : ?>
								<?php $this->render_button( '', 'zc-btn-sm' ); ?>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( 'carousel' === $layout ) : ?>
						<?php $uid = 'zc-slider-' . $this->get_id(); ?>
						<div
							id="<?php echo esc_attr( $uid ); ?>"
							class="zc-slider zc-products-slider"
							style="<?php echo esc_attr( '--zc-spv:' . $cols . ';--zc-spv-md:' . $cols_md . ';--zc-spv-sm:' . $cols_sm ); ?>"
							data-zc-slider
							data-zc-loop="0"
							data-zc-autoplay="<?php echo $this->is_on( $s, 'autoplay' ) ? '1' : '0'; ?>"
							data-zc-interval="5000"
							role="region"
							aria-roledescription="<?php esc_attr_e( 'اسلایدر', 'zarincoach' ); ?>"
							aria-label="<?php echo esc_attr( '' !== $title ? $title : __( 'محصولات', 'zarincoach' ) ); ?>"
							data-zc-label-slide="<?php esc_attr_e( 'رفتن به محصول %d', 'zarincoach' ); ?>"
							data-zc-label-status="<?php esc_attr_e( 'محصول %1$d از %2$d', 'zarincoach' ); ?>"
						>
							<div class="zc-slider-viewport" data-zc-slider-viewport>
								<div class="zc-slider-track" data-zc-slider-track>
									<?php foreach ( $products as $i => $product ) : ?>
										<div class="zc-slider-slide" role="group" aria-roledescription="<?php esc_attr_e( 'اسلاید', 'zarincoach' ); ?>" aria-label="<?php echo esc_attr( zc_digits_to_persian( ( $i + 1 ) . ' / ' . count( $products ) ) ); ?>">
											<?php zc_wc_product_card( $product, array_merge( $card, array( 'wrapper' => 'div' ) ) ); ?>
										</div>
									<?php endforeach; ?>
								</div>
							</div>
							<?php if ( $this->is_on( $s, 'arrows' ) || $this->is_on( $s, 'dots' ) ) : ?>
								<div class="zc-slider-nav">
									<?php if ( $this->is_on( $s, 'arrows' ) ) : ?>
										<button type="button" class="zc-slider-arrow" data-zc-slider-prev aria-controls="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'محصولات قبلی', 'zarincoach' ); ?>"><?php zc_icon( is_rtl() ? 'chevron-right' : 'chevron-left', 'h-5 w-5' ); ?></button>
									<?php endif; ?>
									<?php if ( $this->is_on( $s, 'dots' ) ) : ?>
										<div class="zc-slider-dots" data-zc-slider-dots></div>
									<?php endif; ?>
									<?php if ( $this->is_on( $s, 'arrows' ) ) : ?>
										<button type="button" class="zc-slider-arrow" data-zc-slider-next aria-controls="<?php echo esc_attr( $uid ); ?>" aria-label="<?php esc_attr_e( 'محصولات بعدی', 'zarincoach' ); ?>"><?php zc_icon( is_rtl() ? 'chevron-left' : 'chevron-right', 'h-5 w-5' ); ?></button>
									<?php endif; ?>
								</div>
							<?php endif; ?>
							<p class="sr-only" aria-live="polite" data-zc-slider-status></p>
						</div>
					<?php else : ?>
						<ul class="products zc-products-grid" style="<?php echo esc_attr( '--zc-cols:' . $cols . ';--zc-cols-md:' . $cols_md . ';--zc-cols-sm:' . $cols_sm ); ?>">
							<?php
							foreach ( $products as $product ) {
								zc_wc_product_card( $product, $card );
							}
							?>
						</ul>
					<?php endif; ?>

					<?php if ( $button && 'center' === $align ) : ?>
						<div class="zc-products-more"><?php $this->render_button( '', 'zc-btn-outline' ); ?></div>
					<?php endif; ?>
				</div>
			</section>
			<?php
			wc_reset_loop();
			$GLOBALS['product'] = $prev_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- بازگرداندن محصول جاری صفحه.
		}
	}

endif;
