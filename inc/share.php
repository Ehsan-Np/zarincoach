<?php
/**
 * دکمه‌های اشتراک‌گذاری (الهام‌گرفته از چیدمان مجلات حرفه‌ای)
 *
 * سه جایگاه: ردیف بالای مقاله (inline)، نوار عمودی چسبان کنار متن (sticky) و باکس پایان مقاله (box).
 * سه سبک: رنگ برند شبکه‌ها، رنگ قالب (سرمه‌ای)، خطی.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_share_networks_all' ) ) :
	/**
	 * همه‌ی شبکه‌های پشتیبانی‌شده.
	 *
	 * @return array
	 */
	function zc_share_networks_all() {
		return array(
			'telegram' => array( 'label' => __( 'تلگرام', 'zarincoach' ), 'icon' => 'telegram', 'color' => '#229ED9', 'type' => 'url' ),
			'whatsapp' => array( 'label' => __( 'واتس‌اپ', 'zarincoach' ), 'icon' => 'whatsapp', 'color' => '#1FAF55', 'type' => 'url' ),
			'x'        => array( 'label' => __( 'ایکس', 'zarincoach' ), 'icon' => 'twitter', 'color' => '#0F1419', 'type' => 'url' ),
			'linkedin' => array( 'label' => __( 'لینکدین', 'zarincoach' ), 'icon' => 'linkedin', 'color' => '#0A66C2', 'type' => 'url' ),
			'facebook' => array( 'label' => __( 'فیسبوک', 'zarincoach' ), 'icon' => 'facebook', 'color' => '#1877F2', 'type' => 'url' ),
			'email'    => array( 'label' => __( 'ایمیل', 'zarincoach' ), 'icon' => 'mail', 'color' => '', 'type' => 'url' ),
			'copy'     => array( 'label' => __( 'کپی پیوند', 'zarincoach' ), 'icon' => 'link', 'color' => '', 'type' => 'copy' ),
			'print'    => array( 'label' => __( 'چاپ', 'zarincoach' ), 'icon' => 'printer', 'color' => '', 'type' => 'print' ),
			'native'   => array( 'label' => __( 'بیشتر', 'zarincoach' ), 'icon' => 'share', 'color' => '', 'type' => 'native' ),
		);
	}
endif;

if ( ! function_exists( 'zc_share_url' ) ) :
	/**
	 * نشانی اشتراک هر شبکه.
	 *
	 * @param string $key   کلید شبکه.
	 * @param string $url   نشانی.
	 * @param string $title عنوان.
	 * @return string
	 */
	function zc_share_url( $key, $url, $title ) {
		$u = rawurlencode( $url );
		$t = rawurlencode( $title );
		switch ( $key ) {
			case 'telegram':
				return 'https://t.me/share/url?url=' . $u . '&text=' . $t;
			case 'whatsapp':
				return 'https://wa.me/?text=' . rawurlencode( $title . ' ' . $url );
			case 'x':
				return 'https://x.com/intent/post?url=' . $u . '&text=' . $t;
			case 'linkedin':
				return 'https://www.linkedin.com/sharing/share-offsite/?url=' . $u;
			case 'facebook':
				return 'https://www.facebook.com/sharer/sharer.php?u=' . $u;
			case 'email':
				return 'mailto:?subject=' . $t . '&body=' . rawurlencode( $title . "\n" . $url );
		}
		return '';
	}
endif;

if ( ! function_exists( 'zc_share_enabled' ) ) :
	/**
	 * شبکه‌های فعال از پنل (به ترتیب تعریف).
	 *
	 * @return string[]
	 */
	function zc_share_enabled() {
		$saved = zc_opt( 'post_share_networks', array() );
		$all   = array_keys( zc_share_networks_all() );

		if ( ! is_array( $saved ) || ! $saved ) {
			return array( 'telegram', 'whatsapp', 'x', 'linkedin', 'email', 'copy', 'native' );
		}

		$keys = array();
		foreach ( $all as $key ) {
			if ( ! empty( $saved[ $key ] ) ) {
				$keys[] = $key;
			}
		}
		return $keys;
	}
endif;

if ( ! function_exists( 'zc_share_render' ) ) :
	/**
	 * خروجی دکمه‌های اشتراک.
	 *
	 * @param string $variant inline|sticky|box.
	 * @param array  $args    تنظیمات.
	 * @return void
	 */
	function zc_share_render( $variant = 'box', $args = array() ) {
		$args = wp_parse_args(
			$args,
			array(
				'url'      => get_permalink(),
				'title'    => get_the_title(),
				'networks' => zc_share_enabled(),
				'style'    => (string) zc_opt( 'post_share_style', 'brand' ),
				'heading'  => (string) zc_opt( 'post_share_box_title', __( 'این مطلب را به اشتراک بگذارید', 'zarincoach' ) ),
				'text'     => (string) zc_opt( 'post_share_box_text', __( 'اگر این نوشته برایتان مفید بود، آن را برای کسی بفرستید که شاید امروز به آن نیاز دارد.', 'zarincoach' ) ),
			)
		);

		$all      = zc_share_networks_all();
		$networks = array_values( array_intersect( (array) $args['networks'], array_keys( $all ) ) );

		// چاپ فقط در باکس پایانی معنا دارد؛ در نوار عمودی و ردیف بالا حذف می‌شود.
		if ( 'box' !== $variant ) {
			$networks = array_values( array_diff( $networks, array( 'print' ) ) );
		}
		if ( ! $networks ) {
			return;
		}

		$style   = in_array( $args['style'], array( 'brand', 'theme', 'outline' ), true ) ? $args['style'] : 'brand';
		$labels  = 'box' === $variant;
		$classes = 'zc-share zc-share-' . $variant . ' zc-share-style-' . $style;
		$tag     = 'sticky' === $variant ? 'aside' : 'div';
		?>
		<<?php echo esc_attr( $tag ); ?> class="<?php echo esc_attr( $classes ); ?>" aria-label="<?php esc_attr_e( 'اشتراک‌گذاری', 'zarincoach' ); ?>">
			<?php if ( 'sticky' === $variant ) : ?><div class="zc-share-sticky-inner"><?php endif; ?>

			<?php if ( 'box' === $variant ) : ?>
				<div class="zc-share-box-head">
					<span class="zc-share-box-icon" aria-hidden="true"><?php zc_icon( 'share', 'h-5 w-5' ); ?></span>
					<div>
						<p class="zc-share-box-title"><?php echo esc_html( $args['heading'] ); ?></p>
						<?php if ( '' !== trim( $args['text'] ) ) : ?>
							<p class="zc-share-box-text"><?php echo esc_html( $args['text'] ); ?></p>
						<?php endif; ?>
					</div>
				</div>
			<?php else : ?>
				<span class="zc-share-label"><?php zc_icon( 'share', 'h-4 w-4' ); ?><span><?php esc_html_e( 'اشتراک', 'zarincoach' ); ?></span></span>
			<?php endif; ?>

			<ul class="zc-share-list">
				<?php
				foreach ( $networks as $key ) :
					$n     = $all[ $key ];
					$brand = '' !== $n['color'] ? ' style="--zc-brand:' . esc_attr( $n['color'] ) . '"' : '';
					$cls   = 'zc-share-btn is-' . $key;
					/* translators: %s: نام شبکه */
					$aria = in_array( $n['type'], array( 'url' ), true ) ? sprintf( __( 'اشتراک در %s', 'zarincoach' ), $n['label'] ) : $n['label'];
					$tip  = $labels ? '' : ' data-zc-tip="' . esc_attr( $n['label'] ) . '"';
					$in   = zc_icon( $n['icon'], 'zc-share-icon', false ) . ( $labels ? '<span class="zc-share-name">' . esc_html( $n['label'] ) . '</span>' : '' );
					?>
					<li<?php echo 'native' === $key ? ' data-zc-share-native-item hidden' : ''; ?>>
						<?php if ( 'url' === $n['type'] ) : ?>
							<a class="<?php echo esc_attr( $cls ); ?>"<?php echo $brand . $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> href="<?php echo esc_url( zc_share_url( $key, $args['url'], $args['title'] ), array( 'https', 'mailto' ) ); ?>"<?php echo 'email' === $key ? '' : ' target="_blank" rel="noopener nofollow"'; ?> aria-label="<?php echo esc_attr( $aria ); ?>"><?php echo $in; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
						<?php elseif ( 'copy' === $n['type'] ) : ?>
							<button type="button" class="<?php echo esc_attr( $cls ); ?>"<?php echo $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-zc-share-copy="<?php echo esc_url( $args['url'] ); ?>" data-zc-done="<?php esc_attr_e( 'پیوند کپی شد', 'zarincoach' ); ?>" aria-label="<?php echo esc_attr( $aria ); ?>"><?php echo $in; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<?php elseif ( 'print' === $n['type'] ) : ?>
							<button type="button" class="<?php echo esc_attr( $cls ); ?>"<?php echo $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-zc-print aria-label="<?php echo esc_attr( $aria ); ?>"><?php echo $in; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<?php else : ?>
							<button type="button" class="<?php echo esc_attr( $cls ); ?>"<?php echo $tip; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> data-zc-share-native data-url="<?php echo esc_url( $args['url'] ); ?>" data-title="<?php echo esc_attr( $args['title'] ); ?>" aria-label="<?php esc_attr_e( 'گزینه‌های بیشتر اشتراک‌گذاری', 'zarincoach' ); ?>"><?php echo $in; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>

			<?php if ( 'sticky' === $variant ) : ?></div><?php endif; ?>
		</<?php echo esc_attr( $tag ); ?>>
		<?php
	}
endif;

if ( ! function_exists( 'zc_share_buttons' ) ) :
	/**
	 * سازگاری با نسخه‌های قبلی: باکس اشتراک پایان مقاله.
	 *
	 * @return void
	 */
	function zc_share_buttons() {
		zc_share_render( 'box' );
	}
endif;
