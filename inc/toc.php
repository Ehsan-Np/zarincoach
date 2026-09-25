<?php
/**
 * موتور فهرست مطالب (Table of Contents)
 *
 * یک موتور واحد برای نوشته‌های وبلاگ، ویجت «متن/قوانین» و ویجت مستقل «فهرست مطالب».
 * - شناسه‌ی یکتا و فارسی‌پسند برای هر تیتر (شناسه‌های موجود حفظ می‌شوند)
 * - انتخاب سطح تیترها، ۱ تا ۴ ستون، چهار سبک، سه نوع شماره‌گذاری
 * - جمع‌شونده (با حالت پیش‌فرض باز/بسته) و دکمه‌ی شناور
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_toc_defaults' ) ) :
	/**
	 * مقادیر پیش‌فرض فهرست.
	 *
	 * @return array
	 */
	function zc_toc_defaults() {
		return array(
			'title'       => __( 'فهرست مطالب', 'zarincoach' ),
			'levels'      => '2-3',
			'min'         => 3,
			'columns'     => 2,
			'style'       => 'card',
			'numbering'   => 'decimal',
			'collapsible' => true,
			'open'        => true,
			'meta'        => true,
			'minutes'     => 0,
			'floating'    => false,
			'position'    => 'before_first',
			'class'       => '',
		);
	}
endif;

if ( ! function_exists( 'zc_toc_levels' ) ) :
	/**
	 * تبدیل مقدار «سطح تیترها» به بازه‌ی عددی.
	 *
	 * @param string $levels مثلا 2 یا 2-3.
	 * @return int[] [min, max]
	 */
	function zc_toc_levels( $levels ) {
		$parts = array_map( 'intval', explode( '-', (string) $levels ) );
		$min   = max( 1, min( 6, isset( $parts[0] ) && $parts[0] ? $parts[0] : 2 ) );
		$max   = max( $min, min( 6, isset( $parts[1] ) && $parts[1] ? $parts[1] : $min ) );
		return array( $min, $max );
	}
endif;

if ( ! function_exists( 'zc_toc_slug' ) ) :
	/**
	 * ساخت شناسه‌ی یکتا و خوانا (حروف فارسی و نیم‌فاصله حفظ می‌شوند).
	 *
	 * @param string $text متن تیتر.
	 * @param array  $used شناسه‌های استفاده‌شده (ارجاعی).
	 * @return string
	 */
	function zc_toc_slug( $text, array &$used ) {
		$text = html_entity_decode( wp_strip_all_tags( (string) $text ), ENT_QUOTES, 'UTF-8' );
		$text = zc_digits_to_latin( trim( $text ) );
		$slug = preg_replace( '/[^\p{L}\p{N}\x{200C}\s_-]+/u', '', $text );
		$slug = preg_replace( '/[\s_-]+/u', '-', (string) $slug );
		$slug = trim( strtolower( (string) $slug ), '-' );
		if ( function_exists( 'mb_substr' ) ) {
			$slug = trim( mb_substr( $slug, 0, 60 ), '-' );
		}
		if ( '' === $slug || is_numeric( $slug[0] ) ) {
			$slug = 'section-' . $slug;
			$slug = rtrim( $slug, '-' );
		}

		$base = $slug;
		$i    = 2;
		while ( isset( $used[ $slug ] ) ) {
			$slug = $base . '-' . $i;
			++$i;
		}
		$used[ $slug ] = true;
		return $slug;
	}
endif;

if ( ! function_exists( 'zc_toc_parse' ) ) :
	/**
	 * افزودن شناسه به تیترها و استخراج فهرست.
	 *
	 * @param string $html محتوا.
	 * @param string $levels بازه‌ی سطح (مثلا 2-3).
	 * @return array{html:string,items:array}
	 */
	function zc_toc_parse( $html, $levels = '2-3' ) {
		list( $min, $max ) = zc_toc_levels( $levels );

		$items = array();
		$used  = array();

		// شناسه‌های موجود در محتوا را رزرو کن تا تکراری ساخته نشود.
		if ( preg_match_all( '/\sid\s*=\s*(["\'])(.*?)\1/i', (string) $html, $found ) ) {
			foreach ( $found[2] as $existing ) {
				$used[ $existing ] = true;
			}
		}

		$html = preg_replace_callback(
			'#<h([1-6])(\s[^>]*)?>(.*?)</h\1>#is',
			static function ( $m ) use ( $min, $max, &$items, &$used ) {
				$level = (int) $m[1];
				$attrs = isset( $m[2] ) ? (string) $m[2] : '';
				$inner = (string) $m[3];
				$text  = trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( $inner ), ENT_QUOTES, 'UTF-8' ) ) );

				if ( $level < $min || $level > $max || '' === $text || preg_match( '/class\s*=\s*(["\'])[^"\']*\b(?:zc-toc-skip|no-toc)\b/i', $attrs ) ) {
					return $m[0];
				}

				if ( preg_match( '/\sid\s*=\s*(["\'])(.*?)\1/i', $attrs, $idm ) && '' !== $idm[2] ) {
					$id = $idm[2];
				} else {
					$id    = zc_toc_slug( $text, $used );
					$attrs = ' id="' . esc_attr( $id ) . '"' . $attrs;
				}

				$items[] = array(
					'level' => $level,
					'id'    => $id,
					'text'  => $text,
				);

				return '<h' . $level . $attrs . '>' . $inner . '</h' . $level . '>';
			},
			(string) $html
		);

		return array(
			'html'  => (string) $html,
			'items' => $items,
		);
	}
endif;

if ( ! function_exists( 'zc_toc_tree' ) ) :
	/**
	 * تبدیل فهرست تخت به درخت (بر اساس سطح تیتر).
	 *
	 * @param array $items آیتم‌ها.
	 * @return array
	 */
	function zc_toc_tree( array $items ) {
		$root  = array();
		$stack = array();

		foreach ( $items as $item ) {
			$node = $item + array( 'children' => array() );

			while ( $stack && $stack[ count( $stack ) - 1 ]['level'] >= $node['level'] ) {
				array_pop( $stack );
			}

			if ( ! $stack ) {
				$root[] = $node;
				$stack  = array( array( 'level' => $node['level'], 'ref' => &$root[ count( $root ) - 1 ] ) );
				continue;
			}

			$parent               = &$stack[ count( $stack ) - 1 ]['ref'];
			$parent['children'][] = $node;
			$stack[]              = array( 'level' => $node['level'], 'ref' => &$parent['children'][ count( $parent['children'] ) - 1 ] );
			unset( $parent );
		}

		return $root;
	}
endif;

if ( ! function_exists( 'zc_toc_render_list' ) ) :
	/**
	 * خروجی بازگشتی فهرست.
	 *
	 * @param array  $nodes     گره‌ها.
	 * @param string $numbering decimal|hierarchical|none.
	 * @param string $prefix    پیشوند شماره (سلسله‌مراتبی).
	 * @param int    $depth     عمق.
	 * @return string
	 */
	function zc_toc_render_list( array $nodes, $numbering, $prefix = '', $depth = 0 ) {
		$out = '<ol class="' . ( $depth ? 'zc-toc-sub' : 'zc-toc-list' ) . '">';
		$i   = 0;

		foreach ( $nodes as $node ) {
			++$i;
			$number = $prefix ? $prefix . '.' . $i : (string) $i;

			if ( 'none' === $numbering ) {
				$label = '';
			} elseif ( 'hierarchical' === $numbering ) {
				$label = zc_digits_to_persian( $number );
			} else {
				$label = $depth ? '' : zc_digits_to_persian( str_pad( (string) $i, 2, '0', STR_PAD_LEFT ) );
			}

			$out .= '<li class="zc-toc-item">';
			$out .= '<a class="zc-toc-link' . ( $depth ? ' is-sub' : '' ) . '" href="#' . esc_attr( $node['id'] ) . '" data-zc-toc-target="' . esc_attr( $node['id'] ) . '">';
			$out .= '' !== $label ? '<span class="zc-toc-num" aria-hidden="true">' . esc_html( $label ) . '</span>' : '<span class="zc-toc-dot" aria-hidden="true"></span>';
			$out .= '<span class="zc-toc-text">' . esc_html( $node['text'] ) . '</span>';
			$out .= '</a>';

			if ( ! empty( $node['children'] ) ) {
				$out .= zc_toc_render_list( $node['children'], $numbering, $number, $depth + 1 );
			}

			$out .= '</li>';
		}

		return $out . '</ol>';
	}
endif;

if ( ! function_exists( 'zc_toc_shell' ) ) :
	/**
	 * پوسته‌ی فهرست (سربرگ + بدنه). اگر $list_html خالی باشد، جاوااسکریپت فهرست را می‌سازد.
	 *
	 * @param string $list_html فهرست آماده یا خالی.
	 * @param array  $args      تنظیمات.
	 * @param int    $count     تعداد بخش‌ها (برای زیرعنوان).
	 * @return string
	 */
	function zc_toc_shell( $list_html, array $args, $count = 0 ) {
		$args    = wp_parse_args( $args, zc_toc_defaults() );
		$uid     = wp_unique_id( 'zc-toc-' );
		$columns = max( 1, min( 4, (int) $args['columns'] ) );
		$style   = in_array( $args['style'], array( 'card', 'soft', 'navy', 'minimal' ), true ) ? $args['style'] : 'card';
		$num     = in_array( $args['numbering'], array( 'decimal', 'hierarchical', 'none' ), true ) ? $args['numbering'] : 'decimal';
		$collaps = ! empty( $args['collapsible'] );
		$start   = ( $collaps && empty( $args['open'] ) ) ? 'closed' : 'open';

		$meta = array();
		if ( ! empty( $args['meta'] ) ) {
			if ( $count ) {
				/* translators: %s: تعداد بخش */
				$meta[] = sprintf( __( '%s بخش', 'zarincoach' ), zc_digits_to_persian( (string) $count ) );
			}
			if ( ! empty( $args['minutes'] ) ) {
				/* translators: %s: دقیقه */
				$meta[] = sprintf( __( '%s دقیقه مطالعه', 'zarincoach' ), zc_digits_to_persian( (string) (int) $args['minutes'] ) );
			}
		}

		$classes = array( 'zc-toc', 'not-prose', 'zc-toc-style-' . $style, 'zc-toc-cols-' . $columns, 'zc-toc-num-' . $num, 'is-open' );
		if ( $args['class'] ) {
			$classes[] = $args['class'];
		}

		$attrs = sprintf(
			' class="%1$s" data-zc-toc data-zc-toc-start="%2$s" data-zc-toc-numbering="%3$s" data-zc-toc-float="%4$s" aria-labelledby="%5$s-title"',
			esc_attr( implode( ' ', $classes ) ),
			esc_attr( $start ),
			esc_attr( $num ),
			! empty( $args['floating'] ) ? '1' : '0',
			esc_attr( $uid )
		);

		if ( ! empty( $args['mobile_closed'] ) ) {
			$attrs .= ' data-zc-toc-mobile="closed"';
		}

		if ( ! empty( $args['scan'] ) ) {
			$attrs .= sprintf(
				' data-zc-toc-scan="%1$s" data-zc-toc-levels="%2$s" data-zc-toc-min="%3$d"',
				esc_attr( (string) $args['scan'] ),
				esc_attr( (string) $args['levels'] ),
				(int) $args['min']
			);
		}

		ob_start();
		?>
		<nav<?php echo $attrs; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<div class="zc-toc-head">
				<span class="zc-toc-icon" aria-hidden="true"><?php zc_icon( 'list', 'h-5 w-5' ); ?></span>
				<div class="zc-toc-heading">
					<p class="zc-toc-title" id="<?php echo esc_attr( $uid ); ?>-title"><?php echo esc_html( $args['title'] ); ?></p>
					<?php if ( ! empty( $args['meta'] ) ) : ?>
						<p class="zc-toc-meta" data-zc-toc-meta data-tpl-count="<?php esc_attr_e( '%s بخش', 'zarincoach' ); ?>" data-tpl-read="<?php esc_attr_e( '%s دقیقه مطالعه', 'zarincoach' ); ?>"><?php echo esc_html( implode( ' · ', $meta ) ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( $collaps ) : ?>
					<button type="button" class="zc-toc-toggle" aria-expanded="true" aria-controls="<?php echo esc_attr( $uid ); ?>-body" data-zc-toc-toggle data-label-open="<?php esc_attr_e( 'بستن', 'zarincoach' ); ?>" data-label-closed="<?php esc_attr_e( 'نمایش', 'zarincoach' ); ?>">
						<span class="zc-toc-toggle-label"><?php esc_html_e( 'بستن', 'zarincoach' ); ?></span>
						<?php zc_icon( 'chevron-down', 'zc-toc-chevron h-4 w-4' ); ?>
					</button>
				<?php endif; ?>
			</div>
			<div class="zc-toc-progress" aria-hidden="true"><span></span></div>
			<div class="zc-toc-body" id="<?php echo esc_attr( $uid ); ?>-body">
				<div class="zc-toc-inner">
					<?php echo $list_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی از قبل escape شده ?>
				</div>
			</div>
		</nav>
		<?php
		return (string) ob_get_clean();
	}
endif;

if ( ! function_exists( 'zc_toc_render' ) ) :
	/**
	 * خروجی کامل فهرست برای آیتم‌های استخراج‌شده.
	 *
	 * @param array $items آیتم‌ها (خروجی zc_toc_parse).
	 * @param array $args  تنظیمات.
	 * @return string
	 */
	function zc_toc_render( array $items, array $args = array() ) {
		if ( ! $items ) {
			return '';
		}
		$args = wp_parse_args( $args, zc_toc_defaults() );
		$list = zc_toc_render_list( zc_toc_tree( $items ), (string) $args['numbering'] );
		return zc_toc_shell( $list, $args, count( $items ) );
	}
endif;

if ( ! function_exists( 'zc_toc_inject' ) ) :
	/**
	 * قرار دادن فهرست در محتوا.
	 *
	 * @param string $html     محتوا.
	 * @param string $toc      خروجی فهرست.
	 * @param string $position top|before_first|after_first_p.
	 * @param string $first_id شناسه‌ی نخستین تیتر.
	 * @return string
	 */
	function zc_toc_inject( $html, $toc, $position, $first_id = '' ) {
		if ( 'after_first_p' === $position ) {
			$pos = stripos( $html, '</p>' );
			if ( false !== $pos ) {
				return substr_replace( $html, '</p>' . $toc, $pos, 4 );
			}
		} elseif ( 'before_first' === $position && '' !== $first_id ) {
			if ( preg_match( '#<h[1-6][^>]*\sid=(["\'])' . preg_quote( $first_id, '#' ) . '\1#i', $html, $m, PREG_OFFSET_CAPTURE ) ) {
				return substr_replace( $html, $toc, $m[0][1], 0 );
			}
		}
		return $toc . $html;
	}
endif;

if ( ! function_exists( 'zc_toc_post_args' ) ) :
	/**
	 * تنظیمات فهرست نوشته‌ها از پنل.
	 *
	 * @return array
	 */
	function zc_toc_post_args() {
		$d = zc_toc_defaults();
		return array(
			'title'       => (string) zc_opt( 'post_toc_title', $d['title'] ),
			'levels'      => (string) zc_opt( 'post_toc_levels', '2-3' ),
			'min'         => (int) zc_opt( 'post_toc_min', 3 ),
			'columns'     => (int) zc_opt( 'post_toc_columns', 2 ),
			'style'       => (string) zc_opt( 'post_toc_style', 'card' ),
			'numbering'   => (string) zc_opt( 'post_toc_numbering', 'decimal' ),
			'collapsible' => zc_switch( 'post_toc_collapsible', true ),
			'open'        => zc_switch( 'post_toc_open', true ),
			'floating'    => zc_switch( 'post_toc_floating', true ),
			'position'    => (string) zc_opt( 'post_toc_position', 'before_first' ),
			'meta'        => true,
			'minutes'     => zc_reading_time(),
		);
	}
endif;

if ( ! function_exists( 'zc_toc_apply' ) ) :
	/**
	 * پردازش محتوا: شناسه‌گذاری تیترها + درج فهرست (اگر تعداد تیترها کافی باشد).
	 *
	 * @param string $content محتوا.
	 * @param array  $args    تنظیمات.
	 * @return string
	 */
	function zc_toc_apply( $content, array $args = array() ) {
		$args   = wp_parse_args( $args, zc_toc_defaults() );
		$parsed = zc_toc_parse( $content, $args['levels'] );

		if ( count( $parsed['items'] ) < max( 1, (int) $args['min'] ) ) {
			return $parsed['html'];
		}

		$toc = zc_toc_render( $parsed['items'], $args );
		return zc_toc_inject( $parsed['html'], $toc, (string) $args['position'], $parsed['items'][0]['id'] );
	}
endif;
