<?php
/**
 * ویجت محصول ویژه (Spotlight)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Product_Spotlight' ) && class_exists( 'ZC_Shop_Widget_Base' ) ) :

	/**
	 * معرفی بزرگ یک محصول با قیمت، ویژگی‌ها، شمارش معکوس و دکمه‌ی خرید.
	 */
	class ZC_Widget_Product_Spotlight extends ZC_Shop_Widget_Base {

		/**
		 * نام.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-product-spotlight';
		}

		/**
		 * عنوان.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'محصول ویژه', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-single-product';
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

			$this->add_control(
				'product_id',
				array(
					'label'       => __( 'محصول', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'label_block' => true,
					'options'     => $this->product_options(),
					'description' => __( 'خالی = نخستین محصول ویژه (ستاره‌دار).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'eyebrow',
				array(
					'label'   => __( 'برچسب بالا', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'پیشنهاد ویژه', 'zarincoach' ),
				)
			);

			$this->add_control(
				'title_override',
				array(
					'label'       => __( 'عنوان (اختیاری)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = نام محصول', 'zarincoach' ),
				)
			);

			$this->add_control(
				'text_override',
				array(
					'label'       => __( 'توضیح (اختیاری)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 3,
					'placeholder' => __( 'خالی = توضیح کوتاه محصول', 'zarincoach' ),
				)
			);

			$this->add_control(
				'media_side',
				array(
					'label'   => __( 'محل تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'start' => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-h-align-right',
						),
						'end'   => array(
							'title' => __( 'چپ', 'zarincoach' ),
							'icon'  => 'eicon-h-align-left',
						),
					),
					'default' => 'start',
					'toggle'  => false,
				)
			);

			foreach (
				array(
					'show_features'  => array( __( 'ویژگی‌های کلیدی', 'zarincoach' ), 'yes' ),
					'show_countdown' => array( __( 'شمارش معکوس پایان تخفیف', 'zarincoach' ), 'yes' ),
					'show_rating'    => array( __( 'امتیاز', 'zarincoach' ), 'yes' ),
					'show_details'   => array( __( 'دکمه‌ی «جزئیات محصول»', 'zarincoach' ), 'yes' ),
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

			$this->end_controls_section();
		}

		/**
		 * محصول انتخابی.
		 *
		 * @param array $s تنظیمات.
		 * @return WC_Product|null
		 */
		protected function get_product( $s ) {
			$id = absint( $s['product_id'] ?? 0 );
			if ( ! $id ) {
				$ids = wc_get_products(
					array(
						'status'   => 'publish',
						'featured' => true,
						'limit'    => 1,
						'return'   => 'ids',
					)
				);
				$id  = $ids ? (int) $ids[0] : 0;
			}
			$product = $id ? wc_get_product( $id ) : null;
			return ( $product && $product->is_visible() ) ? $product : null;
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'tag'    => array( __( 'برچسب ویژه', 'zarincoach' ), '.zc-spot__tag' ),
					'kind'   => array( __( 'نوع محصول', 'zarincoach' ), '.zc-sp__kind' ),
					'sub'    => array( __( 'زیرعنوان محصول', 'zarincoach' ), '.zc-spot__subtitle' ),
					'text'   => array( __( 'توضیح', 'zarincoach' ), '.zc-spot__text' ),
					'feats'  => array( __( 'ویژگی‌ها', 'zarincoach' ), '.zc-sp__features' ),
					'thumbs' => array( __( 'تصاویر کوچک', 'zarincoach' ), '.zc-spot__thumbs' ),
					'more'   => array( __( 'دکمه‌ی جزئیات', 'zarincoach' ), '.zc-spot__more' ),
				)
			);
			$this->zc_style(
				'sp_box',
				__( 'پنل ویژه', 'zarincoach' ),
				array(
					'box'   => array( 'box', '.zc-spot', __( 'پنل', 'zarincoach' ), array( 'gradient' => true, 'minh' => true ) ),
					'grid'  => array( 'size', '.zc-spot', __( 'فاصله‌ی ستون‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 120 ) ),
					'img'   => array( 'image', '.zc-spot__img', __( 'تصویر اصلی', 'zarincoach' ) ),
					'thumb' => array( 'size', '.zc-spot__thumbs img', __( 'اندازه‌ی تصاویر کوچک', 'zarincoach' ), array( 'max' => 160, 'css' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};' ) ),
					'tgap'  => array( 'size', '.zc-spot__thumbs', __( 'فاصله‌ی تصاویر کوچک', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 30 ) ),
				)
			);
			$this->zc_style(
				'sp_text',
				__( 'برچسب، عنوان و متن', 'zarincoach' ),
				array(
					'tag'   => array( 'box', '.zc-spot__tag', __( 'برچسب', 'zarincoach' ), array( 'gradient' => false, 'text' => true ) ),
					'kind'  => array( 'text', '.zc-sp__kind', __( 'نوع محصول', 'zarincoach' ), array( 'bg' => true, 'margin' => false ) ),
					'title' => array( 'text', '.zc-spot__title, .zc-spot__title a', __( 'عنوان', 'zarincoach' ), array( 'hover' => '.zc-spot' ) ),
					'sub'   => array( 'text', '.zc-spot__subtitle', __( 'زیرعنوان', 'zarincoach' ) ),
					'text'  => array( 'text', '.zc-spot__text', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
					'flic'  => array( 'color', '.zc-sp__features svg, .zc-sp__features i', __( 'رنگ تیک ویژگی‌ها', 'zarincoach' ) ),
					'fli'   => array( 'text', '.zc-sp__features li', __( 'متن ویژگی‌ها', 'zarincoach' ) ),
					'lgap'  => array( 'size', '.zc-sp__features', __( 'فاصله‌ی ویژگی‌ها', 'zarincoach' ), array( 'prop' => 'row-gap', 'max' => 40 ) ),
					'price' => array( 'text', '.zc-spot .price', __( 'قیمت', 'zarincoach' ), array( 'margin' => false ) ),
					'more'  => array( 'button', '.zc-spot__more', __( 'دکمه‌ی جزئیات', 'zarincoach' ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s       = $this->get_settings_for_display();
			$product = $this->get_product( $s );
			if ( ! $product ) {
				$this->editor_notice( __( 'یک محصول انتخاب کنید یا محصولی را «ویژه» (ستاره‌دار) کنید.', 'zarincoach' ) );
				return;
			}
			$prev_product       = isset( $GLOBALS['product'] ) ? $GLOBALS['product'] : null;
			$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited

			$link     = $product->get_permalink();
			$title    = trim( (string) ( $s['title_override'] ?? '' ) );
			$title    = '' !== $title ? $title : $product->get_name();
			$text     = trim( (string) ( $s['text_override'] ?? '' ) );
			$text     = '' !== $text ? $text : wp_strip_all_tags( $product->get_short_description() );
			$subtitle = (string) $product->get_meta( '_zc_subtitle' );
			$features = $this->is_on( $s, 'show_features' ) ? array_slice( zc_wc_lines( (string) $product->get_meta( '_zc_features' ) ), 0, 5 ) : array();
			$kind     = zc_wc_kind( $product );
			$kinds    = zc_wc_kinds();
			$end      = $this->is_on( $s, 'show_countdown' ) && function_exists( 'zc_wc_sale_end' ) ? (int) zc_wc_sale_end( $product ) : 0;
			$gallery  = array_slice( $product->get_gallery_image_ids(), 0, 3 );
			$eyebrow  = trim( (string) ( $s['eyebrow'] ?? '' ) );
			$text_words = isset( $s['text_words'] ) && '' !== $s['text_words'] ? (int) $s['text_words'] : 42;
			?>
			<section class="zc-section zc-spot-wrap">
				<div class="zc-container">
					<article class="zc-spot zc-product-spot<?php echo 'end' === ( $s['media_side'] ?? 'start' ) ? ' is-reverse' : ''; ?>">
						<div class="zc-spot__media">
							<?php echo zc_wc_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<a class="zc-spot__img" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
								<?php
								$img = (int) $product->get_image_id();
								echo $img ? wp_get_attachment_image( $img, 'woocommerce_single', false, array( 'loading' => 'lazy', 'alt' => $product->get_name() ) ) : wc_placeholder_img( 'woocommerce_single' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								?>
							</a>
							<?php if ( $gallery ) : ?>
								<div class="zc-spot__thumbs" aria-hidden="true">
									<?php foreach ( $gallery as $gid ) : ?>
										<?php echo wp_get_attachment_image( $gid, 'woocommerce_gallery_thumbnail', false, array( 'loading' => 'lazy', 'alt' => '' ) ); ?>
									<?php endforeach; ?>
								</div>
							<?php endif; ?>
						</div>
						<div class="zc-spot__body">
							<div class="zc-spot__eyebrow">
								<?php if ( '' !== $eyebrow ) : ?>
									<span class="zc-spot__tag"><?php zc_icon( 'sparkles', 'h-4 w-4' ); ?><?php echo esc_html( $eyebrow ); ?></span>
								<?php endif; ?>
								<?php if ( isset( $kinds[ $kind ] ) ) : ?>
									<span class="zc-sp__kind"><?php zc_icon( $kinds[ $kind ]['icon'], 'h-3.5 w-3.5' ); ?><?php echo esc_html( $kinds[ $kind ]['label'] ); ?></span>
								<?php endif; ?>
							</div>
							<h2 class="zc-spot__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h2>
							<?php if ( '' !== $subtitle ) : ?>
								<p class="zc-spot__subtitle"><?php echo esc_html( $subtitle ); ?></p>
							<?php endif; ?>
							<?php if ( $this->is_on( $s, 'show_rating' ) && $product->get_rating_count() > 0 ) : ?>
								<div class="zc-pcard__rating"><?php echo wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span>(<?php echo esc_html( zc_digits_to_persian( (string) $product->get_rating_count() ) ); ?>)</span></div>
							<?php endif; ?>
							<?php if ( '' !== $text ) : ?>
								<p class="zc-spot__text"><?php echo esc_html( wp_trim_words( $text, max( 10, (int) $text_words ), '…' ) ); ?></p>
							<?php endif; ?>
							<?php if ( $features ) : ?>
								<ul class="zc-sp__features">
									<?php foreach ( $features as $line ) : ?>
										<li><?php zc_icon( 'check', 'h-4 w-4' ); ?><span><?php echo esc_html( $line ); ?></span></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>
							<?php
							if ( $end && function_exists( 'zc_wc_countdown_html' ) ) {
								echo zc_wc_countdown_html( $end ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>
							<div class="zc-spot__buy">
								<span class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
								<div class="zc-spot__actions">
									<?php echo zc_wc_add_to_cart_button( $product, 'zc-spot__atc', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php if ( $this->is_on( $s, 'show_details' ) ) : ?>
										<a class="zc-btn zc-btn-outline zc-spot__more" href="<?php echo esc_url( $link ); ?>"><?php esc_html_e( 'جزئیات محصول', 'zarincoach' ); ?></a>
									<?php endif; ?>
								</div>
							</div>
						</div>
					</article>
				</div>
			</section>
			<?php
			$GLOBALS['product'] = $prev_product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		}
	}

endif;
