<?php
/**
 * ویجت دسته‌بندی محصولات
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Product_Cats' ) && class_exists( 'ZC_Shop_Widget_Base' ) ) :

	/**
	 * کاشی‌های دسته‌بندی محصولات.
	 */
	class ZC_Widget_Product_Cats extends ZC_Shop_Widget_Base {

		/**
		 * نام.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-product-cats';
		}

		/**
		 * عنوان.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'دسته‌بندی محصولات', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-product-categories';
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
				'cats',
				array(
					'label'       => __( 'دسته‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'separator'   => 'before',
					'options'     => $this->category_options(),
					'description' => __( 'خالی = همه‌ی دسته‌های اصلی دارای محصول (به ترتیب دستی دسته‌ها).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'tiles' => __( 'کاشی تصویری', 'zarincoach' ),
						'cards' => __( 'کارت با آیکون', 'zarincoach' ),
						'pills' => __( 'دکمه‌های کوچک', 'zarincoach' ),
					),
					'default' => 'tiles',
				)
			);

			$this->add_control(
				'cols',
				array(
					'label'     => __( 'ستون (دسکتاپ)', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'2' => '۲',
						'3' => '۳',
						'4' => '۴',
						'5' => '۵',
						'6' => '۶',
					),
					'default'   => '4',
					'condition' => array( 'style!' => 'pills' ),
				)
			);

			$this->add_control(
				'show_count',
				array(
					'label'        => __( 'تعداد محصولات', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_desc',
				array(
					'label'        => __( 'توضیح دسته', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'style!' => 'pills' ),
				)
			);

			$this->add_control(
				'desc_words',
				array(
					'label'   => __( 'تعداد واژه‌های توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'default' => 14,
					'min'     => 5,
					'max'     => 80,
				)
			);

			$this->end_controls_section();
		}

		/**
		 * آیکون پیش‌فرض بر اساس نامک.
		 *
		 * @param WP_Term $term دسته.
		 * @return string
		 */
		protected function term_icon( $term ) {
			$map = array(
				'session' => 'video',
				'coach'   => 'video',
				'book'    => 'book',
				'ketab'   => 'book',
				'work'    => 'clipboard',
				'digital' => 'download',
				'course'  => 'graduation',
				'kit'     => 'gift',
				'card'    => 'sparkles',
			);
			foreach ( $map as $needle => $icon ) {
				if ( false !== strpos( $term->slug, $needle ) ) {
					return $icon;
				}
			}
			return 'bag';
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'img'   => array( __( 'تصویر/آیکن دسته', 'zarincoach' ), '.zc-pcat__img, .zc-pcat__icon' ),
					'desc'  => array( __( 'توضیح', 'zarincoach' ), '.zc-pcat__desc' ),
					'count' => array( __( 'تعداد محصول', 'zarincoach' ), '.zc-pcat__count, .zc-pcats-pills em' ),
					'arrow' => array( __( 'فلش', 'zarincoach' ), '.zc-pcat__count svg, .zc-pcat__count i' ),
				)
			);
			$this->zc_style(
				'pc_grid',
				__( 'شبکه و کارت‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'size', '.zc-pcats', __( 'فاصله‌ی کارت‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 60 ) ),
					'card' => array( 'box', '.zc-pcat', __( 'کارت', 'zarincoach' ), array( 'hover' => true ) ),
					'img'  => array( 'size', '.zc-pcat.has-img', __( 'ارتفاع تصویر', 'zarincoach' ), array( 'prop' => 'height', 'max' => 400 ) ),
					'icon' => array( 'icon', '.zc-pcat__icon', __( 'قاب آیکن', 'zarincoach' ), array( 'hover' => '.zc-pcat' ) ),
				)
			);
			$this->zc_style(
				'pc_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-pcat__title, .zc-pcat__title a', __( 'عنوان', 'zarincoach' ), array( 'hover' => '.zc-pcat' ) ),
					'desc'  => array( 'text', '.zc-pcat__desc', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
					'count' => array( 'text', '.zc-pcat__count', __( 'تعداد', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);
			$this->zc_style(
				'pc_pills',
				__( 'دکمه‌ها (طرح قرصی)', 'zarincoach' ),
				array(
					'pill'   => array( 'button', '.zc-pcats-pills .zc-chip', __( 'دکمه‌ها', 'zarincoach' ) ),
					'gap'    => array( 'size', '.zc-pcats-pills', __( 'فاصله‌ی دکمه‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 40 ) ),
					'countc' => array( 'color', '.zc-pcats-pills em', __( 'رنگ عدد', 'zarincoach' ) ),
				),
				array( 'condition' => array( 'style' => 'pills' ) )
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s     = $this->get_settings_for_display();
			$slugs = array_filter( (array) ( $s['cats'] ?? array() ) );
			$args  = array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			);
			if ( $slugs ) {
				$args['slug']       = array_map( 'sanitize_title', $slugs );
				$args['orderby']    = 'slug__in';
				$args['hide_empty'] = false;
			} else {
				$args['parent']  = 0;
				$args['orderby'] = 'term_order';
			}
			$terms = get_terms( $args );
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				$this->editor_notice( __( 'دسته‌ای برای نمایش یافت نشد.', 'zarincoach' ) );
				return;
			}

			$style = in_array( $s['style'] ?? 'tiles', array( 'tiles', 'cards', 'pills' ), true ) ? $s['style'] : 'tiles';
			$cols  = max( 2, min( 6, (int) ( $s['cols'] ?? 4 ) ) );
			$count = $this->is_on( $s, 'show_count' );
			$desc  = $this->is_on( $s, 'show_desc' );
			$desc_words = isset( $s['desc_words'] ) && '' !== $s['desc_words'] ? (int) $s['desc_words'] : 14;
			?>
			<section class="zc-section zc-pcats-wrap">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>
					<?php if ( 'pills' === $style ) : ?>
						<nav class="zc-pcats-pills zc-after-head" aria-label="<?php esc_attr_e( 'دسته‌بندی محصولات', 'zarincoach' ); ?>">
							<?php foreach ( $terms as $term ) : ?>
								<a class="zc-chip" href="<?php echo esc_url( get_term_link( $term ) ); ?>"><?php zc_icon( $this->term_icon( $term ), 'h-4 w-4' ); ?><?php echo esc_html( $term->name ); ?><?php if ( $count ) : ?> <em><?php echo esc_html( zc_digits_to_persian( (string) $term->count ) ); ?></em><?php endif; ?></a>
							<?php endforeach; ?>
						</nav>
					<?php else : ?>
						<ul class="zc-pcats zc-pcats--<?php echo esc_attr( $style ); ?> zc-after-head" style="<?php echo esc_attr( '--zc-cols:' . $cols ); ?>">
							<?php
							foreach ( $terms as $term ) :
								$thumb = (int) get_term_meta( $term->term_id, 'thumbnail_id', true );
								$link  = get_term_link( $term );
								?>
								<li class="zc-pcat<?php echo $thumb ? ' has-img' : ''; ?>">
									<?php if ( 'tiles' === $style && $thumb ) : ?>
										<?php echo wp_get_attachment_image( $thumb, 'woocommerce_thumbnail', false, array( 'class' => 'zc-pcat__img', 'loading' => 'lazy', 'alt' => '' ) ); ?>
									<?php else : ?>
										<span class="zc-pcat__icon"><?php zc_icon( $this->term_icon( $term ), 'h-6 w-6' ); ?></span>
									<?php endif; ?>
									<div class="zc-pcat__body">
										<h3 class="zc-pcat__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $term->name ); ?></a></h3>
										<?php if ( $desc && '' !== trim( $term->description ) ) : ?>
											<p class="zc-pcat__desc"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $term->description ), max( 5, (int) $desc_words ), '…' ) ); ?></p>
										<?php endif; ?>
										<?php if ( $count ) : ?>
											<span class="zc-pcat__count">
												<?php
												/* translators: %s: تعداد محصولات */
												echo esc_html( sprintf( __( '%s محصول', 'zarincoach' ), zc_digits_to_persian( (string) $term->count ) ) );
												?>
												<?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?>
											</span>
										<?php endif; ?>
									</div>
								</li>
							<?php endforeach; ?>
						</ul>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}

endif;
