<?php
/**
 * ووکامرس — کارت محصول، شبکه‌ی فروشگاه و نوار ابزار بایگانی
 *
 * کارت محصول یک تابع واحد است (zc_wc_product_card) که هم در حلقه‌ی فروشگاه (content-product.php)
 * و هم در ویجت‌های المنتور استفاده می‌شود؛ هوک‌های استاندارد ووکامرس درون کارت حفظ شده‌اند
 * تا افزونه‌های جانبی (علاقه‌مندی، مقایسه و…) همچنان کار کنند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

// خروجی‌های پیش‌فرض کارت حذف می‌شوند؛ کارت اختصاصی همه را خودش چاپ می‌کند.
remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

// نوار ابزار بایگانی (در archive-product.php با چیدمان اختصاصی چاپ می‌شود).
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );

if ( ! function_exists( 'zc_wc_badges' ) ) :
	/**
	 * نشان‌های روی تصویر کارت (تخفیف، تازه، پرفروش/دلخواه، ناموجود).
	 *
	 * @param WC_Product $product محصول.
	 * @return string
	 */
	function zc_wc_badges( $product ) {
		$out = array();
		if ( ! $product->is_in_stock() ) {
			$out[] = '<span class="zc-badge-pill is-out">' . esc_html__( 'ناموجود', 'zarincoach' ) . '</span>';
		} elseif ( $product->is_on_sale() ) {
			$pct = zc_wc_discount_percent( $product );
			if ( $pct > 0 && 'percent' === zc_opt( 'shop_badge_style', 'percent' ) ) {
				/* translators: %s: درصد تخفیف */
				$out[] = '<span class="zc-badge-pill is-sale">' . esc_html( sprintf( __( '٪%s تخفیف', 'zarincoach' ), zc_digits_to_persian( (string) $pct ) ) ) . '</span>';
			} else {
				$out[] = '<span class="zc-badge-pill is-sale">' . esc_html__( 'فروش ویژه', 'zarincoach' ) . '</span>';
			}
		}
		$custom = trim( (string) get_post_meta( $product->get_id(), '_zc_badge', true ) );
		if ( '' !== $custom ) {
			$out[] = '<span class="zc-badge-pill is-custom">' . esc_html( $custom ) . '</span>';
		} elseif ( $product->is_featured() && zc_switch( 'shop_badge_featured', true ) ) {
			$out[] = '<span class="zc-badge-pill is-custom">' . esc_html__( 'پیشنهاد ویژه', 'zarincoach' ) . '</span>';
		}
		if ( zc_wc_is_new( $product ) && count( $out ) < 2 ) {
			$out[] = '<span class="zc-badge-pill is-new">' . esc_html__( 'تازه', 'zarincoach' ) . '</span>';
		}
		return $out ? '<div class="zc-pcard__badges">' . implode( '', $out ) . '</div>' : '';
	}
endif;

if ( ! function_exists( 'zc_wc_add_to_cart_button' ) ) :
	/**
	 * دکمه‌ی افزودن به سبد (سازگار با AJAX ووکامرس).
	 *
	 * @param WC_Product $product محصول.
	 * @param string     $class   کلاس اضافه.
	 * @param bool       $icon    فقط آیکون.
	 * @return string
	 */
	function zc_wc_add_to_cart_button( $product, $class = '', $icon = false ) {
		$ajax    = $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock();
		$classes = array_filter(
			array(
				'zc-pcard__atc',
				'button',
				'product_type_' . $product->get_type(),
				$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
				$ajax ? 'ajax_add_to_cart' : '',
				$class,
			)
		);
		$label   = $product->add_to_cart_text();
		$icon_nm = $ajax ? 'bag' : 'arrow-left';
		$inner   = $icon
			? zc_icon( $icon_nm, 'h-[18px] w-[18px]', false ) . '<span class="screen-reader-text">' . esc_html( $label ) . '</span>'
			: zc_icon( $icon_nm, 'h-4 w-4', false ) . '<span>' . esc_html( $label ) . '</span>';

		$html = sprintf(
			'<a href="%1$s" data-quantity="1" class="%2$s" data-product_id="%3$d" data-product_sku="%4$s" aria-label="%5$s" rel="nofollow"%6$s>%7$s</a>',
			esc_url( $product->add_to_cart_url() ),
			esc_attr( implode( ' ', $classes ) ),
			(int) $product->get_id(),
			esc_attr( $product->get_sku() ),
			esc_attr( $product->add_to_cart_description() ),
			$ajax ? ' role="button"' : '',
			$inner
		);

		/** سازگاری با فیلتر استاندارد ووکامرس. */
		return (string) apply_filters( 'woocommerce_loop_add_to_cart_link', $html, $product, array() );
	}
endif;

if ( ! function_exists( 'zc_wc_product_card' ) ) :
	/**
	 * کارت محصول.
	 *
	 * @param WC_Product $product محصول.
	 * @param array      $args    title_tag، excerpt، wrapper (li|div)، class، hover (تصویر دوم)، rating، kind.
	 * @return void
	 */
	function zc_wc_product_card( $product, $args = array() ) {
		if ( ! $product instanceof WC_Product || ! $product->is_visible() ) {
			return;
		}
		$args = wp_parse_args(
			$args,
			array(
				'title_tag' => 'h2',
				'excerpt'   => zc_switch( 'shop_card_excerpt', false ),
				'wrapper'   => 'li',
				'class'     => '',
				'hover'     => zc_switch( 'shop_card_hover_image', true ),
				'rating'    => zc_switch( 'shop_card_rating', true ),
				'kind'      => zc_switch( 'shop_card_kind', true ),
				'cat'       => zc_switch( 'shop_card_category', true ),
				'image'     => 'woocommerce_thumbnail',
			)
		);

		$GLOBALS['product'] = $product; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited -- هوک‌های حلقه به $product سراسری نیاز دارند.

		$tag       = in_array( $args['title_tag'], array( 'h2', 'h3', 'h4', 'p' ), true ) ? $args['title_tag'] : 'h2';
		$wrapper   = 'div' === $args['wrapper'] ? 'div' : 'li';
		$link      = $product->get_permalink();
		$title     = $product->get_name();
		$kind      = zc_wc_kind( $product );
		$kinds     = zc_wc_kinds();
		$image_id  = (int) $product->get_image_id();
		$hover_id  = 0;
		if ( $args['hover'] ) {
			$gallery  = $product->get_gallery_image_ids();
			$hover_id = $gallery ? (int) $gallery[0] : 0;
		}
		$classes = wc_get_product_class( trim( 'zc-pcard ' . ( $hover_id ? 'has-hover ' : '' ) . 'is-' . $kind . ' ' . $args['class'] ), $product );
		?>
		<<?php echo esc_html( $wrapper ); ?> class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
			<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
			<div class="zc-pcard__media">
				<a class="zc-pcard__img" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
					<?php
					if ( $image_id ) {
						echo wp_get_attachment_image(
							$image_id,
							$args['image'],
							false,
							array(
								'class'   => 'zc-pcard__main',
								'alt'     => $title,
								'loading' => 'lazy',
								'sizes'   => '(min-width: 1024px) 300px, (min-width: 640px) 45vw, 90vw',
							)
						);
					} else {
						echo wc_placeholder_img( $args['image'], array( 'class' => 'zc-pcard__main' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $hover_id ) {
						echo wp_get_attachment_image(
							$hover_id,
							$args['image'],
							false,
							array(
								'class'   => 'zc-pcard__alt',
								'alt'     => '',
								'loading' => 'lazy',
								'sizes'   => '(min-width: 1024px) 300px, (min-width: 640px) 45vw, 90vw',
							)
						);
					}
					?>
				</a>
				<?php echo zc_wc_badges( $product ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( $args['kind'] && isset( $kinds[ $kind ] ) ) : ?>
					<span class="zc-pcard__kind"><?php zc_icon( $kinds[ $kind ]['icon'], 'h-3.5 w-3.5' ); ?><?php echo esc_html( $kinds[ $kind ]['short'] ); ?></span>
				<?php endif; ?>
				<?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?>
			</div>

			<div class="zc-pcard__body">
				<?php
				if ( $args['cat'] ) {
					$term = zc_wc_primary_term( $product->get_id() );
					if ( $term ) {
						echo '<a class="zc-pcard__cat" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
					}
				}
				?>
				<<?php echo esc_html( $tag ); ?> class="zc-pcard__title woocommerce-loop-product__title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_html( $tag ); ?>>
				<?php do_action( 'woocommerce_shop_loop_item_title' ); ?>

				<?php
				if ( $args['rating'] && wc_review_ratings_enabled() && $product->get_rating_count() > 0 ) {
					echo '<div class="zc-pcard__rating">' . wc_get_rating_html( $product->get_average_rating(), $product->get_rating_count() ) . '<span>(' . esc_html( zc_digits_to_persian( (string) $product->get_rating_count() ) ) . ')</span></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				$subtitle = trim( (string) get_post_meta( $product->get_id(), '_zc_subtitle', true ) );
				if ( $args['excerpt'] ) {
					$text = '' !== $subtitle ? $subtitle : wp_strip_all_tags( $product->get_short_description() );
					if ( '' !== $text ) {
						echo '<p class="zc-pcard__excerpt">' . esc_html( wp_trim_words( $text, max( 5, (int) ( $args['words'] ?? 16 ) ), '…' ) ) . '</p>';
					}
				} elseif ( '' !== $subtitle ) {
					echo '<p class="zc-pcard__excerpt">' . esc_html( $subtitle ) . '</p>';
				}
				do_action( 'woocommerce_after_shop_loop_item_title' );
				?>

				<div class="zc-pcard__foot">
					<span class="price"><?php echo $product->get_price_html(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<?php echo zc_wc_add_to_cart_button( $product, 'zc-pcard__atc--icon', true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
			<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
		</<?php echo esc_html( $wrapper ); ?>>
		<?php
	}
endif;

/* =========================================================================
 * بایگانی: فیلترها
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_filter_query' ) ) :
	/**
	 * اعمال فیلترهای اختصاصی (نوع تحویل، فقط تخفیف‌دار، فقط موجود) روی حلقه‌ی اصلی فروشگاه.
	 *
	 * @param WP_Query $q کوئری.
	 * @return void
	 */
	function zc_wc_filter_query( $q ) {
		if ( is_admin() || ! $q->is_main_query() || ! ( $q->is_post_type_archive( 'product' ) || $q->is_tax( get_object_taxonomies( 'product' ) ) ) ) {
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- فیلترهای عمومی GET.
		$meta = (array) $q->get( 'meta_query' );
		$tax  = (array) $q->get( 'tax_query' );

		$kind = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
		if ( 'digital' === $kind ) {
			$meta[] = array(
				'key'   => '_downloadable',
				'value' => 'yes',
			);
		} elseif ( 'physical' === $kind ) {
			$meta[] = array(
				'relation' => 'OR',
				array(
					'key'   => '_virtual',
					'value' => 'no',
				),
				array(
					'key'     => '_virtual',
					'compare' => 'NOT EXISTS',
				),
			);
			$tax[] = array(
				'taxonomy' => 'product_type',
				'field'    => 'slug',
				'terms'    => array( 'simple', 'grouped', 'external' ),
			);
		} elseif ( 'session' === $kind ) {
			$tax[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( (string) zc_opt( 'shop_session_cat', 'coaching-sessions' ) ),
			);
		}

		if ( ! empty( $_GET['on_sale'] ) ) {
			$ids = wc_get_product_ids_on_sale();
			$q->set( 'post__in', $ids ? array_map( 'absint', $ids ) : array( 0 ) );
		}
		if ( ! empty( $_GET['in_stock'] ) ) {
			$meta[] = array(
				'key'   => '_stock_status',
				'value' => 'instock',
			);
		}
		// phpcs:enable

		$q->set( 'meta_query', $meta );
		$q->set( 'tax_query', $tax );
	}
endif;
add_action( 'woocommerce_product_query', 'zc_wc_filter_query' );

if ( ! function_exists( 'zc_wc_active_filters' ) ) :
	/**
	 * فیلترهای فعال (برای نمایش چیپ‌ها و noindex).
	 *
	 * @return array<string, string> key => label
	 */
	function zc_wc_active_filters() {
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$out   = array();
		$kinds = zc_wc_kinds();
		$kind  = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
		if ( isset( $kinds[ $kind ] ) ) {
			$out['kind'] = $kinds[ $kind ]['label'];
		}
		if ( ! empty( $_GET['on_sale'] ) ) {
			$out['on_sale'] = __( 'فقط تخفیف‌دار', 'zarincoach' );
		}
		if ( ! empty( $_GET['in_stock'] ) ) {
			$out['in_stock'] = __( 'فقط موجود', 'zarincoach' );
		}
		if ( isset( $_GET['min_price'] ) || isset( $_GET['max_price'] ) ) {
			$min = isset( $_GET['min_price'] ) ? (int) zc_wc_normalize_digits( wp_unslash( $_GET['min_price'] ) ) : 0;
			$max = isset( $_GET['max_price'] ) ? (int) zc_wc_normalize_digits( wp_unslash( $_GET['max_price'] ) ) : 0;
			if ( $min || $max ) {
				$out['price'] = trim( ( $min ? wp_strip_all_tags( wc_price( $min ) ) : '' ) . ' – ' . ( $max ? wp_strip_all_tags( wc_price( $max ) ) : '' ), ' –' );
			}
		}
		// phpcs:enable
		return $out;
	}
endif;

if ( ! function_exists( 'zc_wc_filter_base_url' ) ) :
	/**
	 * نشانی پایه‌ی بایگانی فعلی (بدون صفحه‌بندی).
	 *
	 * @return string
	 */
	function zc_wc_filter_base_url() {
		if ( is_product_taxonomy() ) {
			$link = get_term_link( get_queried_object() );
			return is_wp_error( $link ) ? zc_wc_shop_url() : $link;
		}
		return zc_wc_shop_url();
	}
endif;

if ( ! function_exists( 'zc_wc_render_filters' ) ) :
	/**
	 * ستون فیلترهای فروشگاه.
	 *
	 * اگر ناحیه‌ی ابزارک «ستون فروشگاه» ابزارک داشته باشد، همان نمایش داده می‌شود.
	 *
	 * @return void
	 */
	function zc_wc_render_filters() {
		if ( is_active_sidebar( 'zc-shop' ) ) {
			dynamic_sidebar( 'zc-shop' );
			return;
		}
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$base    = zc_wc_filter_base_url();
		$current = is_product_category() ? get_queried_object_id() : 0;
		$kind    = isset( $_GET['kind'] ) ? sanitize_key( wp_unslash( $_GET['kind'] ) ) : '';
		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( wp_unslash( $_GET['orderby'] ) ) : '';
		$min     = isset( $_GET['min_price'] ) ? (int) zc_wc_normalize_digits( wp_unslash( $_GET['min_price'] ) ) : '';
		$max     = isset( $_GET['max_price'] ) ? (int) zc_wc_normalize_digits( wp_unslash( $_GET['max_price'] ) ) : '';
		// phpcs:enable

		$cats = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => 0,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		?>
		<form class="zc-filters" method="get" action="<?php echo esc_url( $base ); ?>">
			<?php if ( ! empty( $cats ) && ! is_wp_error( $cats ) ) : ?>
				<div class="zc-filter">
					<h2 class="zc-filter__title"><?php esc_html_e( 'دسته‌بندی‌ها', 'zarincoach' ); ?></h2>
					<ul class="zc-filter__cats">
						<li><a class="<?php echo esc_attr( is_shop() ? 'is-active' : '' ); ?>" href="<?php echo esc_url( zc_wc_shop_url() ); ?>"><span><?php esc_html_e( 'همه‌ی محصولات', 'zarincoach' ); ?></span></a></li>
						<?php foreach ( $cats as $cat ) : ?>
							<li>
								<a class="<?php echo esc_attr( $current === $cat->term_id || ( $current && term_is_ancestor_of( $cat->term_id, $current, 'product_cat' ) ) ? 'is-active' : '' ); ?>" href="<?php echo esc_url( get_term_link( $cat ) ); ?>">
									<span><?php echo esc_html( $cat->name ); ?></span>
									<em><?php echo esc_html( zc_digits_to_persian( (string) $cat->count ) ); ?></em>
								</a>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			<?php endif; ?>

			<fieldset class="zc-filter">
				<legend class="zc-filter__title"><?php esc_html_e( 'نوع محصول', 'zarincoach' ); ?></legend>
				<div class="zc-filter__opts">
					<label class="zc-radio"><input type="radio" name="kind" value="" <?php checked( '', $kind ); ?>><span><?php esc_html_e( 'همه', 'zarincoach' ); ?></span></label>
					<?php foreach ( zc_wc_kinds() as $key => $k ) : ?>
						<label class="zc-radio"><input type="radio" name="kind" value="<?php echo esc_attr( $key ); ?>" <?php checked( $key, $kind ); ?>><span><?php zc_icon( $k['icon'], 'h-4 w-4' ); ?><?php echo esc_html( $k['label'] ); ?></span></label>
					<?php endforeach; ?>
				</div>
			</fieldset>

			<fieldset class="zc-filter">
				<legend class="zc-filter__title"><?php esc_html_e( 'محدوده‌ی قیمت', 'zarincoach' ); ?> <small>(<?php echo esc_html( get_woocommerce_currency_symbol() ); ?>)</small></legend>
				<div class="zc-filter__price">
					<label><span class="screen-reader-text"><?php esc_html_e( 'از', 'zarincoach' ); ?></span><input type="text" inputmode="numeric" name="min_price" value="<?php echo esc_attr( (string) $min ); ?>" placeholder="<?php esc_attr_e( 'از', 'zarincoach' ); ?>" class="zc-input"></label>
					<span aria-hidden="true">–</span>
					<label><span class="screen-reader-text"><?php esc_html_e( 'تا', 'zarincoach' ); ?></span><input type="text" inputmode="numeric" name="max_price" value="<?php echo esc_attr( (string) $max ); ?>" placeholder="<?php esc_attr_e( 'تا', 'zarincoach' ); ?>" class="zc-input"></label>
				</div>
			</fieldset>

			<fieldset class="zc-filter">
				<legend class="zc-filter__title"><?php esc_html_e( 'وضعیت', 'zarincoach' ); ?></legend>
				<div class="zc-filter__opts">
					<label class="zc-check"><input type="checkbox" name="on_sale" value="1" <?php checked( ! empty( $_GET['on_sale'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>><span><?php esc_html_e( 'فقط تخفیف‌دار', 'zarincoach' ); ?></span></label>
					<label class="zc-check"><input type="checkbox" name="in_stock" value="1" <?php checked( ! empty( $_GET['in_stock'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended ?>><span><?php esc_html_e( 'فقط کالاهای موجود', 'zarincoach' ); ?></span></label>
				</div>
			</fieldset>

			<?php if ( '' !== $orderby ) : ?>
				<input type="hidden" name="orderby" value="<?php echo esc_attr( $orderby ); ?>">
			<?php endif; ?>

			<div class="zc-filters__actions">
				<button type="submit" class="zc-btn zc-btn-primary zc-btn-sm zc-btn-block"><?php zc_icon( 'filter', 'h-4 w-4' ); ?><?php esc_html_e( 'اعمال فیلتر', 'zarincoach' ); ?></button>
				<?php if ( zc_wc_active_filters() ) : ?>
					<a class="zc-filters__reset" href="<?php echo esc_url( $base ); ?>"><?php esc_html_e( 'حذف همه‌ی فیلترها', 'zarincoach' ); ?></a>
				<?php endif; ?>
			</div>
		</form>
		<?php
	}
endif;

if ( ! function_exists( 'zc_wc_register_sidebar' ) ) :
	/**
	 * ناحیه‌ی ابزارک ستون فروشگاه (اختیاری؛ در صورت خالی بودن فیلترهای داخلی قالب نمایش داده می‌شوند).
	 *
	 * @return void
	 */
	function zc_wc_register_sidebar() {
		register_sidebar(
			array(
				'name'          => __( 'ستون فروشگاه', 'zarincoach' ),
				'id'            => 'zc-shop',
				'description'   => __( 'اختیاری. اگر خالی بماند، فیلترهای اختصاصی قالب (دسته، نوع، قیمت، وضعیت) نمایش داده می‌شوند.', 'zarincoach' ),
				'before_widget' => '<section id="%1$s" class="zc-filter widget %2$s">',
				'after_widget'  => '</section>',
				'before_title'  => '<h2 class="zc-filter__title">',
				'after_title'   => '</h2>',
			)
		);
	}
endif;
add_action( 'widgets_init', 'zc_wc_register_sidebar' );

if ( ! function_exists( 'zc_wc_toolbar' ) ) :
	/**
	 * نوار ابزار بالای شبکه: تعداد نتایج، مرتب‌سازی، دکمه‌ی فیلتر موبایل و چیپ فیلترهای فعال.
	 *
	 * @param bool $sidebar ستون فیلتر فعال است؟
	 * @return void
	 */
	function zc_wc_toolbar( $sidebar ) {
		$active = zc_wc_active_filters();
		?>
		<div class="zc-shop-toolbar">
			<div class="zc-shop-toolbar__start">
				<?php if ( $sidebar ) : ?>
					<button type="button" class="zc-shop-toolbar__filter" data-zc-filters-open aria-controls="zc-shop-filters" aria-expanded="false">
						<?php zc_icon( 'sliders', 'h-4 w-4' ); ?><?php esc_html_e( 'فیلترها', 'zarincoach' ); ?>
						<?php if ( $active ) : ?><em><?php echo esc_html( zc_digits_to_persian( (string) count( $active ) ) ); ?></em><?php endif; ?>
					</button>
				<?php endif; ?>
				<?php woocommerce_result_count(); ?>
			</div>
			<?php woocommerce_catalog_ordering(); ?>
		</div>
		<?php if ( $active ) : ?>
			<div class="zc-shop-active">
				<?php
				foreach ( $active as $key => $label ) {
					$remove = 'price' === $key ? array( 'min_price', 'max_price' ) : array( $key );
					echo '<a class="zc-chip-x" href="' . esc_url( remove_query_arg( array_merge( $remove, array( 'paged' ) ) ) ) . '">' . esc_html( $label ) . zc_icon( 'close', 'h-3.5 w-3.5', false ) . '</a>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		<?php endif; ?>
		<?php
	}
endif;

// متن تعداد نتایج کوتاه و فارسی.
add_filter(
	'woocommerce_catalog_orderby',
	static function ( $options ) {
		$labels = array(
			'menu_order' => __( 'پیش‌فرض', 'zarincoach' ),
			'popularity' => __( 'پرفروش‌ترین', 'zarincoach' ),
			'rating'     => __( 'بیشترین امتیاز', 'zarincoach' ),
			'date'       => __( 'جدیدترین', 'zarincoach' ),
			'price'      => __( 'ارزان‌ترین', 'zarincoach' ),
			'price-desc' => __( 'گران‌ترین', 'zarincoach' ),
		);
		foreach ( $labels as $key => $label ) {
			if ( isset( $options[ $key ] ) ) {
				$options[ $key ] = $label;
			}
		}
		return $options;
	}
);

/* =========================================================================
 * بالای بایگانی: زیردسته‌ها + محتوای المنتور برگه‌ی فروشگاه
 * ========================================================================= */

if ( ! function_exists( 'zc_wc_subcategory_pills' ) ) :
	/**
	 * چیپ زیردسته‌ها در بایگانی دسته.
	 *
	 * @return void
	 */
	function zc_wc_subcategory_pills() {
		$parent = is_product_category() ? get_queried_object_id() : 0;
		$terms  = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'hide_empty' => true,
				'parent'     => $parent,
				'exclude'    => array( (int) get_option( 'default_product_cat' ) ),
			)
		);
		if ( empty( $terms ) || is_wp_error( $terms ) ) {
			return;
		}
		echo '<nav class="zc-shop-cats" aria-label="' . esc_attr__( 'دسته‌بندی محصولات', 'zarincoach' ) . '">';
		foreach ( $terms as $term ) {
			echo '<a class="zc-chip" href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . ' <em>' . esc_html( zc_digits_to_persian( (string) $term->count ) ) . '</em></a>';
		}
		echo '</nav>';
	}
endif;

if ( ! function_exists( 'zc_wc_shop_intro' ) ) :
	/**
	 * محتوای طراحی‌شده با المنتور برای برگه‌ی «فروشگاه» — فقط صفحه‌ی اول بایگانی اصلی بدون فیلتر.
	 *
	 * ووکامرس محتوای برگه‌ی فروشگاه را نمایش نمی‌دهد؛ این تابع آن را بالای شبکه‌ی محصولات می‌آورد
	 * تا بتوان فروشگاه را با ویجت‌های اختصاصی (معرفی، دسته‌ها، پیشنهاد ویژه، مزایا) طراحی کرد.
	 *
	 * @return bool آیا محتوایی نمایش داده شد؟
	 */
	function zc_wc_shop_intro() {
		if ( ! is_shop() || is_search() || is_paged() || zc_wc_active_filters() || ! zc_switch( 'shop_intro', true ) ) {
			return false;
		}
		$shop_id = (int) wc_get_page_id( 'shop' );
		if ( $shop_id <= 0 || ! zc_is_elementor_active() || ! zc_page_uses_elementor( $shop_id ) ) {
			return false;
		}
		$html = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( $shop_id, true );
		if ( '' === trim( (string) $html ) ) {
			return false;
		}
		echo '<div class="zc-shop-intro">' . $html . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی المنتور.
		return true;
	}
endif;

if ( ! function_exists( 'zc_wc_variable_price_from' ) ) :
	/**
	 * قیمت محصولات متغیر: «از …» به‌جای بازه‌ی دوقیمتی (خواناتر در کارت و راست‌به‌چپ).
	 *
	 * @param string              $html    HTML قیمت.
	 * @param WC_Product_Variable $product محصول.
	 * @return string
	 */
	function zc_wc_variable_price_from( $html, $product ) {
		$min = (float) $product->get_variation_price( 'min', true );
		$max = (float) $product->get_variation_price( 'max', true );
		if ( $min === $max || $min <= 0 ) {
			return $html;
		}
		$regular = (float) $product->get_variation_regular_price( 'min', true );
		$price   = $product->is_on_sale() && $regular > $min ? wc_format_sale_price( $regular, $min ) : wc_price( $min );
		return '<span class="zc-price-from">' . esc_html__( 'از', 'zarincoach' ) . '</span> ' . $price . $product->get_price_suffix();
	}
	add_filter( 'woocommerce_variable_price_html', 'zc_wc_variable_price_from', 10, 2 );
endif;

if ( ! function_exists( 'zc_wc_unit_label' ) ) :
	/**
	 * نام فارسی واحدهای وزن و ابعاد.
	 *
	 * @param string $unit واحد.
	 * @return string
	 */
	function zc_wc_unit_label( $unit ) {
		$map = array(
			'kg'  => __( 'کیلوگرم', 'zarincoach' ),
			'g'   => __( 'گرم', 'zarincoach' ),
			'lbs' => __( 'پوند', 'zarincoach' ),
			'oz'  => __( 'اونس', 'zarincoach' ),
			'm'   => __( 'متر', 'zarincoach' ),
			'cm'  => __( 'سانتی‌متر', 'zarincoach' ),
			'mm'  => __( 'میلی‌متر', 'zarincoach' ),
			'in'  => __( 'اینچ', 'zarincoach' ),
			'yd'  => __( 'یارد', 'zarincoach' ),
		);
		return isset( $map[ $unit ] ) ? $map[ $unit ] : $unit;
	}
endif;

add_filter(
	'woocommerce_format_weight',
	static function ( $string, $weight ) {
		return '' === (string) $weight ? $string : wc_format_localized_decimal( $weight ) . ' ' . zc_wc_unit_label( get_option( 'woocommerce_weight_unit' ) );
	},
	10,
	2
);
add_filter(
	'woocommerce_format_dimensions',
	static function ( $string, $dimensions ) {
		$dims = array_filter( array_map( 'wc_format_localized_decimal', (array) $dimensions ) );
		return $dims ? implode( ' × ', $dims ) . ' ' . zc_wc_unit_label( get_option( 'woocommerce_dimension_unit' ) ) : $string;
	},
	10,
	2
);
