<?php
/**
 * توابع قالب‌سازی (Template Tags)
 *
 * این توابع بین قالب‌های اصلی و ویجت‌های المنتور مشترک هستند تا
 * خروجی‌ها دقیقاً یکسان و استاندارد باشند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_site_branding' ) ) :
	/**
	 * نمایش لوگو یا عنوان سایت.
	 *
	 * @param bool $footer نسخه پاورقی (متن روشن).
	 * @return void
	 */
	function zc_site_branding( $footer = false ) {
		$logo      = (array) zc_opt( 'general_logo', array() );
		$logo_dark = (array) zc_opt( 'general_logo_dark', array() );
		$width     = (int) zc_opt( 'general_logo_width', 168 );

		$light_url = isset( $logo['url'] ) ? (string) $logo['url'] : '';
		$dark_url  = isset( $logo_dark['url'] ) ? (string) $logo_dark['url'] : '';

		if ( '' !== $light_url || '' !== $dark_url ) {
			?>
			<a class="inline-flex items-center" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				<?php if ( '' !== $light_url ) : ?>
					<img
						src="<?php echo esc_url( $light_url ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						width="<?php echo esc_attr( (string) $width ); ?>"
						class="h-auto w-auto max-w-[60vw] dark:hidden"
						style="max-height:56px;width:<?php echo esc_attr( (string) $width ); ?>px;object-fit:contain"
						decoding="async"
					>
				<?php endif; ?>
				<?php if ( '' !== $dark_url ) : ?>
					<img
						src="<?php echo esc_url( $dark_url ); ?>"
						alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>"
						class="hidden h-auto w-auto max-w-[60vw] dark:block"
						style="max-height:56px;width:<?php echo esc_attr( (string) $width ); ?>px;object-fit:contain"
						decoding="async"
					>
				<?php endif; ?>
			</a>
			<?php
			return;
		}

		?>
		<a class="group inline-flex flex-col leading-tight" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
			<span class="zc-display text-[1.35rem] <?php echo $footer ? 'text-white' : 'text-secondary'; ?>">
				<?php echo esc_html( get_bloginfo( 'name' ) ); ?>
			</span>
			<?php if ( ! $footer && '' !== get_bloginfo( 'description' ) ) : ?>
				<span class="mt-0.5 text-[0.7rem] text-muted"><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
			<?php endif; ?>
		</a>
		<?php
	}
endif;

if ( ! function_exists( 'zc_section_heading' ) ) :
	/**
	 * سربرگ استاندارد هر بخش.
	 *
	 * @param array $args آرگومان‌ها: eyebrow, title, subtitle, align, class, animation.
	 * @return void
	 */
	function zc_section_heading( $args = array() ) {
		$defaults = array(
			'eyebrow'  => '',
			'title'    => '',
			'subtitle' => '',
			'align'    => 'center',
			'class'    => '',
			'reveal'   => true,
			'as'       => 'h2',
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( '' === $args['title'] && '' === $args['eyebrow'] ) {
			return;
		}

		$align_class = 'center' === $args['align']
			? 'text-center mx-auto items-center'
			: 'text-start items-start';

		$reveal = $args['reveal'] ? 'zc-reveal' : '';
		$tag    = in_array( (string) $args['as'], array( 'h1', 'h2', 'h3', 'h4' ), true ) ? (string) $args['as'] : 'h2';
		?>
		<header class="<?php echo esc_attr( trim( 'zc-section-head flex max-w-3xl flex-col gap-3 ' . $align_class . ' ' . $reveal . ' ' . $args['class'] ) ); ?>">
			<?php if ( '' !== $args['eyebrow'] ) : ?>
				<span class="zc-eyebrow <?php echo 'center' === $args['align'] ? '' : 'flex-row-reverse'; ?>"><?php echo esc_html( $args['eyebrow'] ); ?></span>
			<?php endif; ?>

			<?php if ( '' !== $args['title'] ) : ?>
				<<?php echo esc_html( $tag ); ?> class="zc-title-lg zc-text-balance"><?php echo esc_html( $args['title'] ); ?></<?php echo esc_html( $tag ); ?>>
			<?php endif; ?>

			<?php if ( '' !== $args['subtitle'] ) : ?>
				<p class="zc-lead mt-1"><?php echo esc_html( $args['subtitle'] ); ?></p>
			<?php endif; ?>
		</header>
		<?php
	}
endif;

if ( ! function_exists( 'zc_button' ) ) :
	/**
	 * خروجی استاندارد دکمه‌ها.
	 *
	 * @param array $args آرگومان‌ها: text, url, style, size, icon, icon_html (آیکن آماده‌ی المنتور)، class, new_tab.
	 * @return void
	 */
	function zc_button( $args = array() ) {
		$defaults = array(
			'text'    => '',
			'url'     => '#',
			'style'   => 'primary',
			'size'    => '',
			'icon'    => 'arrow-left',
			'class'   => '',
			'new_tab' => false,
		);
		$args     = wp_parse_args( $args, $defaults );

		if ( '' === $args['text'] ) {
			return;
		}

		$style_class = in_array( (string) $args['style'], array( 'primary', 'secondary', 'outline', 'ghost' ), true )
			? 'zc-btn-' . (string) $args['style']
			: 'zc-btn-primary';

		$size_class = '' === (string) $args['size'] ? '' : 'zc-btn-' . (string) $args['size'];
		?>
		<a
			href="<?php echo esc_url( (string) $args['url'] ); ?>"
			class="<?php echo esc_attr( trim( 'zc-btn ' . $style_class . ' ' . $size_class . ' ' . $args['class'] ) ); ?>"
			<?php echo $args['new_tab'] ? 'target="_blank" rel="noopener"' : ''; ?>
		>
			<span><?php echo esc_html( (string) $args['text'] ); ?></span>
			<?php if ( ! empty( $args['icon_html'] ) ) : ?>
				<?php echo $args['icon_html']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی Icons_Manager المنتور. ?>
			<?php elseif ( 'arrow-left' === $args['icon'] || true === $args['icon'] ) : ?>
				<?php zc_icon( 'arrow-left', 'h-4 w-4 zc-btn-arrow' ); ?>
			<?php elseif ( '' !== (string) $args['icon'] ) : ?>
				<?php zc_icon( (string) $args['icon'], 'h-4 w-4' ); ?>
			<?php endif; ?>
		</a>
		<?php
	}
endif;

if ( ! function_exists( 'zc_post_card' ) ) :
	/**
	 * کارت استاندارد نوشته.
	 *
	 * چیدمان‌ها: vertical (عمودی)، horizontal (افقی)، overlay (متن روی تصویر)، compact (فشرده‌ی فهرستی).
	 *
	 * @param int   $post_id شناسه نوشته.
	 * @param array $args    تنظیمات نمایش.
	 * @return void
	 */
	function zc_post_card( $post_id = null, $args = array() ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$args    = wp_parse_args(
			$args,
			array(
				'size'       => 'zc_card',
				'layout'     => 'vertical',
				'excerpt'    => (int) zc_opt( 'archive_excerpt', 22 ),
				'reveal'     => true,
				'delay'      => 0,
				'show_cat'   => true,
				'show_meta'  => true,
				'author'     => false,
				'ratio'      => '',
				'featured'   => false,
				'title_tag'  => 'h3',
			)
		);

		$link    = get_permalink( $post_id );
		$title   = get_the_title( $post_id );
		$excerpt = '';
		if ( (int) $args['excerpt'] > 0 ) {
			$excerpt = has_excerpt( $post_id ) ? zc_excerpt( get_the_excerpt( $post_id ), (int) $args['excerpt'] ) : zc_excerpt( get_post_field( 'post_content', $post_id ), (int) $args['excerpt'] );
		}
		$cat     = $args['show_cat'] ? zc_post_categories( $post_id ) : '';
		$reveal  = $args['reveal'] ? ' zc-reveal' : '';
		$delay   = ' data-zc-delay="' . esc_attr( (string) (int) $args['delay'] ) . '"';
		$ratio   = in_array( $args['ratio'], array( '16-10', '4-3', '1-1', '3-4', '16-9' ), true ) ? ' zc-ratio-' . $args['ratio'] : '';
		$tag     = in_array( $args['title_tag'], array( 'h2', 'h3', 'h4' ), true ) ? $args['title_tag'] : 'h3';
		$author  = (int) get_post_field( 'post_author', $post_id );

		$meta_html = '';
		if ( $args['show_meta'] ) {
			ob_start();
			zc_post_meta( $post_id, array( 'reading' => zc_switch( 'archive_reading_time', true ) ) );
			$meta_html = (string) ob_get_clean();
		}

		$author_html = '';
		if ( $args['author'] ) {
			$author_html = '<span class="zc-card-author">' . get_avatar( $author, 56, '', '', array( 'class' => 'zc-card-author-avatar' ) ) . '<span>' . esc_html( get_the_author_meta( 'display_name', $author ) ) . '</span></span>';
		}

		/* ---------- روی تصویر ---------- */
		if ( 'overlay' === $args['layout'] ) :
			?>
			<article <?php post_class( 'zc-post-overlay group' . ( $args['featured'] ? ' is-featured' : '' ) . $ratio . $reveal, $post_id ); ?><?php echo $delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<?php echo zc_post_image( $post_id, $args['featured'] ? 'zc_wide' : (string) $args['size'], array( 'width' => 900, 'height' => 640, 'sizes' => $args['featured'] ? '(min-width: 1024px) 760px, 100vw' : '(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<span class="zc-post-overlay-shade" aria-hidden="true"></span>
				<div class="zc-post-overlay-body">
					<?php if ( '' !== $cat ) : ?>
						<span class="zc-post-overlay-cat"><?php echo esc_html( $cat ); ?></span>
					<?php endif; ?>
					<<?php echo esc_attr( $tag ); ?> class="zc-post-overlay-title"><a class="zc-stretched" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_attr( $tag ); ?>>
					<?php if ( $args['featured'] && '' !== $excerpt ) : ?>
						<p class="zc-post-overlay-excerpt"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $author_html || '' !== $meta_html ) : ?>
						<div class="zc-post-overlay-meta"><?php echo $author_html . $meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
					<?php endif; ?>
				</div>
			</article>
			<?php
			return;
		endif;

		/* ---------- فشرده ---------- */
		if ( 'compact' === $args['layout'] ) :
			?>
			<article <?php post_class( 'zc-post-compact group' . $reveal, $post_id ); ?><?php echo $delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<a class="zc-post-compact-thumb" href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true">
					<?php echo zc_post_image( $post_id, 'zc_thumb', array( 'width' => 240, 'height' => 240, 'sizes' => '120px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<div class="min-w-0 flex-1">
					<?php if ( '' !== $cat ) : ?>
						<span class="zc-post-compact-cat"><?php echo esc_html( $cat ); ?></span>
					<?php endif; ?>
					<<?php echo esc_attr( $tag ); ?> class="zc-post-compact-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></<?php echo esc_attr( $tag ); ?>>
					<?php echo $meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</article>
			<?php
			return;
		endif;

		/* ---------- افقی ---------- */
		if ( 'horizontal' === $args['layout'] ) :
			?>
			<article <?php post_class( 'zc-post-card group sm:flex-row' . $reveal, $post_id ); ?><?php echo $delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<a class="zc-post-thumb<?php echo esc_attr( $ratio ); ?> sm:!aspect-auto sm:w-64 sm:shrink-0" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
					<?php echo zc_post_image( $post_id, 'zc_thumb', array( 'alt' => $title, 'width' => 480, 'height' => 320, 'sizes' => '(min-width: 640px) 256px, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
				<div class="flex flex-1 flex-col gap-3 p-5 sm:p-6">
					<?php if ( '' !== $cat ) : ?>
						<span class="zc-badge self-start"><?php echo esc_html( $cat ); ?></span>
					<?php endif; ?>
					<<?php echo esc_attr( $tag ); ?> class="text-[1.1rem] font-bold leading-snug text-secondary">
						<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
					</<?php echo esc_attr( $tag ); ?>>
					<?php if ( '' !== $excerpt ) : ?>
						<p class="zc-lead text-[0.9rem]"><?php echo esc_html( $excerpt ); ?></p>
					<?php endif; ?>
					<div class="zc-card-foot mt-auto"><?php echo $author_html . $meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				</div>
			</article>
			<?php
			return;
		endif;

		/* ---------- عمودی (پیش‌فرض) ---------- */
		?>
		<article <?php post_class( 'zc-post-card group' . ( $args['featured'] ? ' is-featured' : '' ) . $reveal, $post_id ); ?><?php echo $delay; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
			<a class="zc-post-thumb<?php echo esc_attr( $ratio ); ?>" href="<?php echo esc_url( $link ); ?>" aria-label="<?php echo esc_attr( $title ); ?>">
				<?php echo zc_post_image( $post_id, (string) $args['size'], array( 'alt' => $title, 'width' => 900, 'height' => 640, 'sizes' => $args['featured'] ? '(min-width: 1024px) 760px, 100vw' : '(min-width: 1024px) 400px, (min-width: 640px) 50vw, 100vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<?php if ( '' !== $cat ) : ?>
					<span class="zc-cat-tag"><?php echo esc_html( $cat ); ?></span>
				<?php endif; ?>
			</a>

			<div class="flex flex-1 flex-col gap-3 p-6">
				<<?php echo esc_attr( $tag ); ?> class="text-[1.15rem] font-bold leading-snug text-secondary">
					<a class="transition-colors hover:text-primary" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a>
				</<?php echo esc_attr( $tag ); ?>>

				<?php if ( '' !== $excerpt ) : ?>
					<p class="zc-lead text-[0.92rem]"><?php echo esc_html( $excerpt ); ?></p>
				<?php endif; ?>

				<?php if ( '' !== $author_html || '' !== $meta_html ) : ?>
					<div class="zc-card-foot mt-auto pt-2"><?php echo $author_html . $meta_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php endif; ?>
			</div>
		</article>
		<?php
	}
endif;

if ( ! function_exists( 'zc_post_meta' ) ) :
	/**
	 * فراداده‌ی نوشته (تاریخ، زمان مطالعه، نویسنده).
	 *
	 * @param int   $post_id شناسه نوشته.
	 * @param array $args    تنظیمات.
	 * @return void
	 */
	function zc_post_meta( $post_id = null, $args = array() ) {
		$post_id = $post_id ? $post_id : get_the_ID();
		$args    = wp_parse_args(
			$args,
			array(
				'date'    => zc_switch( 'archive_meta_date', true ),
				'reading' => zc_switch( 'archive_reading_time', true ),
				'author'  => false,
			)
		);
		?>
		<div class="zc-meta">
			<?php if ( $args['date'] ) : ?>
				<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( zc_date( $post_id ) ); ?></time>
			<?php endif; ?>

			<?php if ( $args['reading'] ) : ?>
				<span class="zc-meta-sep"><?php
				/* translators: %d: دقیقه */
				printf( esc_html__( '%d دقیقه مطالعه', 'zarincoach' ), (int) zc_reading_time( $post_id ) );
				?></span>
			<?php endif; ?>

			<?php if ( $args['author'] ) : ?>
				<span class="zc-meta-sep"><?php echo esc_html( get_the_author_meta( 'display_name', get_post_field( 'post_author', $post_id ) ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_social_links' ) ) :
	/**
	 * نمایش شبکه‌های اجتماعی.
	 *
	 * @param string $class کلاس اضافه.
	 * @return void
	 */
	function zc_social_links( $class = '' ) {
		$socials = zc_socials();
		if ( empty( $socials ) ) {
			return;
		}
		?>
		<div class="flex flex-wrap items-center gap-2 <?php echo esc_attr( $class ); ?>">
			<?php foreach ( $socials as $item ) : ?>
				<a class="zc-social" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
					<?php zc_icon( $item['icon'], 'h-[18px] w-[18px]' ); ?>
				</a>
			<?php endforeach; ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_pagination' ) ) :
	/**
	 * صفحه‌بندی استاندارد.
	 *
	 * @param WP_Query|null $query پرس‌وجو (اختیاری).
	 * @return void
	 */
	function zc_pagination( $query = null ) {
		$args = array(
			'mid_size'           => 1,
			'prev_text'          => zc_icon( 'arrow-right', 'h-4 w-4', false ),
			'next_text'          => zc_icon( 'arrow-left', 'h-4 w-4', false ),
			'type'               => 'list',
			'before_page_number' => '<span class="sr-only">' . esc_html__( 'صفحه', 'zarincoach' ) . ' </span>',
		);

		if ( $query instanceof WP_Query ) {
			$big        = 999999999;
			$pagination = paginate_links(
				array_merge(
					$args,
					array(
						'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
						'format'  => '?paged=%#%',
						'current' => max( 1, (int) $query->get( 'paged' ) ),
						'total'   => (int) $query->max_num_pages,
					)
				)
			);
		} else {
			$pagination = paginate_links( $args );
		}

		if ( ! $pagination ) {
			return;
		}
		?>
		<nav class="zc-pagination mt-8 lg:mt-10 flex justify-center" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'zarincoach' ); ?>">
			<?php echo zc_kses_svg( str_replace( array( "<ul class='page-numbers'", '<ul class="page-numbers"' ), '<ul class="page-numbers flex flex-wrap items-center gap-2"', $pagination ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		</nav>
		<?php
	}
endif;

if ( ! function_exists( 'zc_author_role' ) ) :
	/**
	 * عنوان/سمت نویسنده (متای zc_role یا مدرک ثبت‌شده در پنل برای مدیر سایت).
	 *
	 * @param int $author_id شناسه کاربر.
	 * @return string
	 */
	function zc_author_role( $author_id ) {
		$role = trim( (string) get_the_author_meta( 'zc_role', $author_id ) );
		if ( '' === $role && user_can( $author_id, 'manage_options' ) ) {
			$role = zc_legal_info( 'degree' );
		}
		return (string) apply_filters( 'zc_author_role', $role, $author_id );
	}
endif;

if ( ! function_exists( 'zc_post_cat_badges' ) ) :
	/**
	 * نشان دسته‌های نوشته.
	 *
	 * @param int $post_id شناسه نوشته.
	 * @param int $limit   حداکثر تعداد.
	 * @return void
	 */
	function zc_post_cat_badges( $post_id = null, $limit = 3 ) {
		$cats = get_the_category( $post_id ? $post_id : get_the_ID() );
		if ( ! $cats ) {
			return;
		}
		echo '<div class="zc-post-cats">';
		foreach ( array_slice( $cats, 0, $limit ) as $cat ) {
			printf( '<a class="zc-post-cat" href="%1$s">%2$s</a>', esc_url( get_category_link( $cat ) ), esc_html( $cat->name ) );
		}
		echo '</div>';
	}
endif;

if ( ! function_exists( 'zc_post_byline' ) ) :
	/**
	 * نوار نویسنده و فراداده‌ی سربرگ نوشته (آواتار، تاریخ انتشار/به‌روزرسانی، زمان مطالعه، دیدگاه‌ها).
	 *
	 * @param int $post_id شناسه نوشته.
	 * @return void
	 */
	function zc_post_byline( $post_id = null ) {
		$post_id   = $post_id ? $post_id : get_the_ID();
		$author_id = (int) get_post_field( 'post_author', $post_id );
		$role      = zc_author_role( $author_id );
		$updated   = get_post_modified_time( 'U', true, $post_id ) - get_post_time( 'U', true, $post_id ) > DAY_IN_SECONDS;
		$comments  = (int) get_comments_number( $post_id );
		?>
		<div class="zc-byline">
			<?php if ( zc_switch( 'post_meta_author', true ) ) : ?>
				<a class="zc-byline-author" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
					<?php echo get_avatar( $author_id, 88, '', '', array( 'class' => 'zc-byline-avatar' ) ); ?>
					<span class="zc-byline-who">
						<span class="zc-byline-name"><span class="zc-byline-by"><?php esc_html_e( 'نوشته‌ی', 'zarincoach' ); ?></span> <?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></span>
						<?php if ( '' !== $role ) : ?>
							<span class="zc-byline-role"><?php echo esc_html( $role ); ?></span>
						<?php endif; ?>
					</span>
				</a>
			<?php endif; ?>

			<ul class="zc-byline-meta">
				<li>
					<?php zc_icon( 'calendar', 'h-4 w-4' ); ?>
					<time datetime="<?php echo esc_attr( get_the_date( 'c', $post_id ) ); ?>"><?php echo esc_html( zc_date( $post_id ) ); ?></time>
				</li>
				<?php if ( $updated && zc_switch( 'post_meta_updated', true ) ) : ?>
					<li>
						<?php zc_icon( 'refresh', 'h-4 w-4' ); ?>
						<span><?php esc_html_e( 'به‌روزرسانی:', 'zarincoach' ); ?> <time datetime="<?php echo esc_attr( get_the_modified_date( 'c', $post_id ) ); ?>"><?php echo esc_html( zc_date( $post_id, 'modified' ) ); ?></time></span>
					</li>
				<?php endif; ?>
				<?php if ( zc_switch( 'archive_reading_time', true ) ) : ?>
					<li>
						<?php zc_icon( 'clock', 'h-4 w-4' ); ?>
						<span><?php
						/* translators: %s: دقیقه */
						printf( esc_html__( '%s دقیقه مطالعه', 'zarincoach' ), esc_html( zc_digits_to_persian( (string) zc_reading_time( $post_id ) ) ) );
						?></span>
					</li>
				<?php endif; ?>
				<?php if ( zc_switch( 'post_meta_comments', true ) && ( comments_open( $post_id ) || $comments ) ) : ?>
					<li>
						<?php zc_icon( 'message', 'h-4 w-4' ); ?>
						<a href="#comments"><?php
						echo esc_html(
							$comments
								/* translators: %s: تعداد دیدگاه */
								? sprintf( __( '%s دیدگاه', 'zarincoach' ), zc_digits_to_persian( (string) $comments ) )
								: __( 'بدون دیدگاه', 'zarincoach' )
						);
						?></a>
					</li>
				<?php endif; ?>
			</ul>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_post_tags' ) ) :
	/**
	 * برچسب‌های نوشته.
	 *
	 * @return void
	 */
	function zc_post_tags() {
		$tags = get_the_tags();
		if ( ! $tags ) {
			return;
		}
		?>
		<div class="zc-post-tags">
			<span class="zc-post-tags-label"><?php zc_icon( 'hash', 'h-4 w-4' ); ?><?php esc_html_e( 'برچسب‌ها', 'zarincoach' ); ?></span>
			<?php foreach ( $tags as $tag ) : ?>
				<a class="zc-tag" href="<?php echo esc_url( get_tag_link( $tag->term_id ) ); ?>" rel="tag"><?php echo esc_html( $tag->name ); ?></a>
			<?php endforeach; ?>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_author_box' ) ) :
	/**
	 * باکس معرفی نویسنده.
	 *
	 * @return void
	 */
	function zc_author_box() {
		$author_id = (int) get_post_field( 'post_author', get_the_ID() );
		$bio       = trim( (string) get_the_author_meta( 'description', $author_id ) );
		$bio       = '' !== $bio ? $bio : (string) zc_opt( 'seo_person_desc', '' );
		$role      = zc_author_role( $author_id );
		$count     = (int) count_user_posts( $author_id, 'post', true );
		$is_owner  = user_can( $author_id, 'manage_options' );
		?>
		<section class="zc-author-box" aria-label="<?php esc_attr_e( 'درباره‌ی نویسنده', 'zarincoach' ); ?>">
			<div class="zc-author-avatar">
				<?php echo get_avatar( $author_id, 176, '', '', array( 'class' => 'h-full w-full rounded-full object-cover' ) ); ?>
			</div>
			<div class="zc-author-body">
				<div class="zc-author-top">
					<div>
						<span class="zc-author-eyebrow"><?php esc_html_e( 'درباره‌ی نویسنده', 'zarincoach' ); ?></span>
						<p class="zc-author-name">
							<a href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>"><?php echo esc_html( get_the_author_meta( 'display_name', $author_id ) ); ?></a>
							<?php if ( $is_owner && '' !== zc_legal_info( 'pco_code' ) ) : ?>
								<span class="zc-author-verified" title="<?php esc_attr_e( 'دارای کد نظام روان‌شناسی', 'zarincoach' ); ?>"><?php zc_icon( 'badge-check', 'h-5 w-5' ); ?></span>
							<?php endif; ?>
						</p>
						<?php if ( '' !== $role ) : ?>
							<p class="zc-author-role"><?php echo esc_html( $role ); ?></p>
						<?php endif; ?>
					</div>
					<?php if ( $is_owner ) : ?>
						<div class="zc-author-follow">
							<span><?php esc_html_e( 'دنبال کنید', 'zarincoach' ); ?></span>
							<?php zc_social_links( 'zc-author-socials' ); ?>
						</div>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $bio ) : ?>
					<p class="zc-author-bio"><?php echo esc_html( $bio ); ?></p>
				<?php endif; ?>
				<a class="zc-author-more" href="<?php echo esc_url( get_author_posts_url( $author_id ) ); ?>">
					<?php
					/* translators: %s: تعداد نوشته */
					printf( esc_html__( 'همه‌ی نوشته‌ها (%s)', 'zarincoach' ), esc_html( zc_digits_to_persian( (string) $count ) ) );
					?>
					<?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?>
				</a>
			</div>
		</section>
		<?php
	}
endif;

if ( ! function_exists( 'zc_post_nav' ) ) :
	/**
	 * کارت‌های نوشته‌ی قبلی/بعدی با تصویر.
	 *
	 * @return void
	 */
	function zc_post_nav() {
		$prev = get_previous_post();
		$next = get_next_post();
		if ( ! $prev && ! $next ) {
			return;
		}
		$items = array(
			'prev' => array( $prev, __( 'نوشته‌ی قبلی', 'zarincoach' ), 'arrow-right' ),
			'next' => array( $next, __( 'نوشته‌ی بعدی', 'zarincoach' ), 'arrow-left' ),
		);
		?>
		<nav class="zc-post-nav" aria-label="<?php esc_attr_e( 'نوشته‌های قبلی و بعدی', 'zarincoach' ); ?>">
			<?php foreach ( $items as $key => $item ) : ?>
				<?php if ( ! $item[0] ) : ?>
					<span class="zc-post-nav-empty" aria-hidden="true"></span>
					<?php continue; ?>
				<?php endif; ?>
				<a class="zc-post-nav-item is-<?php echo esc_attr( $key ); ?>" href="<?php echo esc_url( get_permalink( $item[0] ) ); ?>" rel="<?php echo esc_attr( $key ); ?>">
					<span class="zc-post-nav-thumb"><?php echo zc_post_image( $item[0]->ID, 'zc_thumb', array( 'width' => 160, 'height' => 160, 'sizes' => '80px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
					<span class="zc-post-nav-text">
						<span class="zc-post-nav-label"><?php zc_icon( $item[2], 'h-3.5 w-3.5' ); ?><?php echo esc_html( $item[1] ); ?></span>
						<span class="zc-post-nav-title"><?php echo esc_html( get_the_title( $item[0] ) ); ?></span>
					</span>
				</a>
			<?php endforeach; ?>
		</nav>
		<?php
	}
endif;

if ( ! function_exists( 'zc_related_query' ) ) :
	/**
	 * پرس‌وجوی نوشته‌های مرتبط (هم‌دسته؛ در صورت کمبود، تازه‌ترین‌ها).
	 *
	 * @param int   $count   تعداد.
	 * @param int[] $exclude شناسه‌های کنارگذاشته.
	 * @return WP_Post[]
	 */
	function zc_related_query( $count, $exclude = array() ) {
		$post_id = get_the_ID();
		$exclude = array_merge( array( $post_id ), (array) $exclude );
		$cats    = wp_get_post_categories( $post_id );
		$base    = array(
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => (int) $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		);

		$posts = array();
		if ( $cats ) {
			$posts = get_posts( $base + array( 'category__in' => $cats, 'post__not_in' => $exclude ) );
		}
		if ( count( $posts ) < $count ) {
			$exclude = array_merge( $exclude, wp_list_pluck( $posts, 'ID' ) );
			$more    = get_posts( array_merge( $base, array( 'posts_per_page' => $count - count( $posts ), 'post__not_in' => $exclude ) ) );
			$posts   = array_merge( $posts, $more );
		}
		return $posts;
	}
endif;

if ( ! function_exists( 'zc_more_read_box' ) ) :
	/**
	 * باکس «بیشتر بخوانید» درون متن.
	 *
	 * @param int $count تعداد.
	 * @return string
	 */
	function zc_more_read_box( $count = 3 ) {
		$posts = zc_related_query( max( 1, (int) $count ) );
		if ( ! $posts ) {
			return '';
		}
		$first = array_shift( $posts );
		ob_start();
		?>
		<aside class="zc-more-read not-prose" aria-label="<?php esc_attr_e( 'بیشتر بخوانید', 'zarincoach' ); ?>">
			<p class="zc-more-read-title"><?php zc_icon( 'book', 'h-4 w-4' ); ?><?php echo esc_html( (string) zc_opt( 'post_more_read_title', __( 'بیشتر بخوانید', 'zarincoach' ) ) ); ?></p>
			<a class="zc-more-read-feature" href="<?php echo esc_url( get_permalink( $first ) ); ?>">
				<span class="zc-more-read-thumb"><?php echo zc_post_image( $first->ID, 'zc_thumb', array( 'width' => 200, 'height' => 140, 'sizes' => '100px' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
				<span class="zc-more-read-main">
					<span class="zc-more-read-name"><?php echo esc_html( get_the_title( $first ) ); ?></span>
					<span class="zc-more-read-meta"><?php
					/* translators: %s: دقیقه */
					printf( esc_html__( '%s دقیقه مطالعه', 'zarincoach' ), esc_html( zc_digits_to_persian( (string) zc_reading_time( $first->ID ) ) ) );
					?></span>
				</span>
			</a>
			<?php if ( $posts ) : ?>
				<ul class="zc-more-read-list">
					<?php foreach ( $posts as $p ) : ?>
						<li><a href="<?php echo esc_url( get_permalink( $p ) ); ?>"><?php echo esc_html( get_the_title( $p ) ); ?></a></li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</aside>
		<?php
		return (string) ob_get_clean();
	}
endif;

if ( ! function_exists( 'zc_inject_more_read' ) ) :
	/**
	 * درج باکس «بیشتر بخوانید» پس از پاراگراف N (خارج از نقل‌قول‌ها و فهرست‌ها).
	 *
	 * @param string $content محتوا.
	 * @return string
	 */
	function zc_inject_more_read( $content ) {
		if ( ! zc_switch( 'post_more_read_enable', true ) ) {
			return $content;
		}
		$after = max( 1, (int) zc_opt( 'post_more_read_after', 3 ) );

		if ( ! preg_match_all( '#</p>#i', $content, $m, PREG_OFFSET_CAPTURE ) || count( $m[0] ) <= $after ) {
			return $content; // متن کوتاه است؛ باکس درج نمی‌شود.
		}

		$seen = 0;
		foreach ( $m[0] as $match ) {
			$pos    = $match[1] + 4;
			$before = substr( $content, 0, $pos );
			$nested = substr_count( $before, '<blockquote' ) - substr_count( $before, '</blockquote' )
				+ substr_count( $before, '<li' ) - substr_count( $before, '</li' )
				+ substr_count( $before, '<nav' ) - substr_count( $before, '</nav' )
				+ substr_count( $before, '<aside' ) - substr_count( $before, '</aside' );
			if ( $nested > 0 ) {
				continue;
			}
			++$seen;
			if ( $seen === $after ) {
				$box = zc_more_read_box( (int) zc_opt( 'post_more_read_count', 4 ) );
				return '' === $box ? $content : substr_replace( $content, $box, $pos, 0 );
			}
		}
		return $content;
	}
endif;

if ( ! function_exists( 'zc_related_posts' ) ) :
	/**
	 * نوشته‌های مرتبط.
	 *
	 * @param int $count تعداد.
	 * @return void
	 */
	function zc_related_posts( $count = 3 ) {
		$posts = zc_related_query( (int) $count );
		if ( ! $posts ) {
			return;
		}
		?>
		<section class="zc-related mt-10 border-t border-line pt-8 lg:mt-12 lg:pt-10">
			<div class="flex flex-wrap items-end justify-between gap-4">
				<?php zc_section_heading( array( 'eyebrow' => __( 'پیشنهاد برای شما', 'zarincoach' ), 'title' => (string) zc_opt( 'post_related_title', __( 'ادامه مطالعه', 'zarincoach' ) ), 'align' => 'start' ) ); ?>
				<a class="zc-author-more" href="<?php echo esc_url( zc_blog_url() ); ?>"><?php esc_html_e( 'همه‌ی مقالات', 'zarincoach' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
			</div>
			<div class="mt-8 grid gap-6 md:grid-cols-2 lg:grid-cols-3">
				<?php
				$delay = 0;
				foreach ( $posts as $p ) {
					zc_post_card( $p->ID, array( 'delay' => $delay ) );
					$delay += 90;
				}
				?>
			</div>
		</section>
		<?php
	}
endif;

if ( ! function_exists( 'zc_table_of_contents' ) ) :
	/**
	 * پردازش محتوای نوشته: شناسه‌گذاری تیترها، درج فهرست مطالب و باکس «بیشتر بخوانید».
	 *
	 * @param string $content محتوای نوشته.
	 * @return string
	 */
	function zc_table_of_contents( $content ) {
		if ( ! is_singular( 'post' ) ) {
			return $content;
		}
		$content = zc_inject_more_read( $content );
		if ( ! zc_switch( 'post_toc_enable', true ) ) {
			return $content;
		}
		return zc_toc_apply( $content, zc_toc_post_args() );
	}
endif;

if ( ! function_exists( 'zc_contact_list' ) ) :
	/**
	 * فهرست اطلاعات تماس به صورت کارت.
	 *
	 * @param string $class کلاس اضافه.
	 * @return void
	 */
	function zc_contact_list( $class = '' ) {
		$contact = zc_contact_fields();
		$items   = array_filter(
			array(
				array( 'icon' => 'phone', 'label' => $contact['phone_label'], 'value' => $contact['phone'], 'url' => 'tel:' . zc_normalize_phone( $contact['phone'] ) ),
				array( 'icon' => 'phone', 'label' => $contact['phone2_label'], 'value' => $contact['phone2'], 'url' => 'tel:' . zc_normalize_phone( $contact['phone2'] ) ),
				array( 'icon' => 'mail', 'label' => __( 'ایمیل', 'zarincoach' ), 'value' => $contact['email'], 'url' => 'mailto:' . $contact['email'] ),
				array( 'icon' => 'map-pin', 'label' => __( 'نشانی', 'zarincoach' ), 'value' => $contact['address'], 'url' => '' ),
				array( 'icon' => 'clock', 'label' => __( 'ساعات پاسخ‌گویی', 'zarincoach' ), 'value' => $contact['hours'], 'url' => '' ),
			),
			static function ( $item ) {
				return '' !== trim( (string) $item['value'] );
			}
		);
		?>
		<ul class="grid gap-4 <?php echo esc_attr( $class ); ?>">
			<?php foreach ( $items as $item ) : ?>
				<li class="flex items-start gap-4 rounded-2xl border border-line bg-surface p-4 transition-colors hover:border-primary/40">
					<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary/10 text-primary">
						<?php zc_icon( $item['icon'], 'h-5 w-5' ); ?>
					</span>
					<div class="min-w-0">
						<span class="block text-[0.75rem] text-muted"><?php echo esc_html( $item['label'] ); ?></span>
						<?php if ( '' !== $item['url'] ) : ?>
							<a class="block break-words font-bold text-secondary transition-colors hover:text-primary" href="<?php echo esc_url( $item['url'] ); ?>" dir="auto"><?php echo esc_html( $item['value'] ); ?></a>
						<?php else : ?>
							<span class="block break-words font-bold text-secondary"><?php echo esc_html( $item['value'] ); ?></span>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php
	}
endif;

if ( ! function_exists( 'zc_stars' ) ) :
	/**
	 * نمایش امتیاز ستاره‌ای.
	 *
	 * @param int $rating امتیاز از ۵.
	 * @return void
	 */
	function zc_stars( $rating = 5 ) {
		$rating = max( 0, min( 5, (int) $rating ) );
		?>
		<span class="zc-stars" role="img" aria-label="<?php /* translators: %d: امتیاز (۰ تا ۵) */ printf( esc_attr__( '%d از ۵', 'zarincoach' ), $rating ); ?>">
			<?php
			for ( $i = 1; $i <= 5; $i++ ) {
				$fill = $i <= $rating ? 'fill-current' : 'text-line';
				echo '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="h-4 w-4 ' . esc_attr( $fill ) . '" aria-hidden="true"><path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2L12 17.3 6.4 20.2l1.1-6.2L3 9.6l6.2-.9Z"/></svg>';
			}
			?>
		</span>
		<?php
	}
endif;

if ( ! function_exists( 'zc_comment_markup' ) ) :
	/**
	 * قالب نمایش هر دیدگاه.
	 *
	 * @param WP_Comment $comment دیدگاه.
	 * @param array      $args    آرگومان‌ها.
	 * @param int        $depth   عمق.
	 * @return void
	 */
	function zc_comment_markup( $comment, $args, $depth ) {
		$tag       = ( 'div' === $args['style'] ) ? 'div' : 'li';
		$parent_id = (int) $comment->comment_parent;
		$parent    = $parent_id ? get_comment( $parent_id ) : null;
		$post      = get_post( (int) $comment->comment_post_ID );
		$is_author = $post && (int) $comment->user_id > 0 && (int) $comment->user_id === (int) $post->post_author;
		$has_kids  = ! empty( $args['has_children'] );
		// پوسته‌ی li بدون استایل است تا پاسخ‌ها (ol.children) بیرون از کارت والد و تورفته قرار بگیرند.
		?>
		<<?php echo esc_html( $tag ); ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'zc-comment' . ( $has_kids ? ' has-replies' : '' ), $comment ); ?>>
			<article class="zc-comment-card" id="div-comment-<?php comment_ID(); ?>">
				<div class="zc-comment-avatar">
					<?php echo get_avatar( $comment, 56, '', '', array( 'class' => 'h-full w-full rounded-full object-cover' ) ); ?>
				</div>

				<div class="min-w-0 flex-1">
					<header class="zc-comment-head">
						<span class="zc-comment-author"><?php echo esc_html( get_comment_author( $comment ) ); ?></span>
						<?php if ( $is_author ) : ?>
							<span class="zc-comment-badge"><?php esc_html_e( 'نویسنده', 'zarincoach' ); ?></span>
						<?php endif; ?>
						<a class="zc-comment-date" href="<?php echo esc_url( get_comment_link( $comment ) ); ?>">
							<time datetime="<?php echo esc_attr( get_comment_time( 'c' ) ); ?>"><?php $zc_cdate = zc_jalali_date( (int) get_comment_date( 'U', $comment ) ); echo esc_html( '' !== $zc_cdate ? $zc_cdate : get_comment_date( '', $comment ) ); ?></time>
						</a>
					</header>

					<?php if ( $parent ) : ?>
						<a class="zc-comment-inreply" href="#comment-<?php echo esc_attr( (string) $parent_id ); ?>">
							<?php zc_icon( 'reply', 'h-3.5 w-3.5' ); ?>
							<?php
							/* translators: %s: نام نویسنده‌ی دیدگاه والد */
							printf( esc_html__( 'در پاسخ به %s', 'zarincoach' ), '<strong>' . esc_html( get_comment_author( $parent ) ) . '</strong>' );
							?>
						</a>
					<?php endif; ?>

					<?php if ( '0' === $comment->comment_approved ) : ?>
						<p class="zc-form-note"><?php esc_html_e( 'دیدگاه‌تان ثبت شد؛ به محض تأیید همین‌جا نمایش داده می‌شود. ممنون که وقت گذاشتید.', 'zarincoach' ); ?></p>
					<?php endif; ?>

					<div class="zc-comment-text"><?php comment_text(); ?></div>

					<div class="zc-comment-actions">
						<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'add_below' => 'div-comment',
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
									'before'    => '<span class="zc-comment-reply">',
									'after'     => '</span>',
								)
							)
						);
						edit_comment_link( esc_html__( 'ویرایش', 'zarincoach' ), '<span class="zc-comment-edit">', '</span>' );
						?>
					</div>
				</div>
			</article>
		<?php
		// تگ li توسط Walker (end_el) بسته می‌شود تا پاسخ‌ها داخل همین li و بعد از کارت بیایند.
	}
endif;

if ( ! function_exists( 'zc_footer_default_columns' ) ) :
	/**
	 * ستون‌های پیش‌فرض پاورقی: خدمات و آخرین نوشته‌ها.
	 *
	 * @return void
	 */
	function zc_footer_default_columns( $a = array() ) {
		$a       = wp_parse_args( $a, zc_footer_args() );
		$columns = array();

		if ( '' !== (string) $a['services_title'] ) {
			$columns[] = array(
				'title' => (string) $a['services_title'],
				'query' => array( 'post_type' => 'zc_service', 'posts_per_page' => max( 1, (int) $a['services_count'] ), 'meta_key' => '_zc_service_order', 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ) ), // phpcs:ignore WordPress.DB.SlowDBQuery
			);
		}
		if ( '' !== (string) $a['posts_title'] ) {
			$columns[] = array(
				'title' => (string) $a['posts_title'],
				'query' => array( 'post_type' => 'post', 'posts_per_page' => max( 1, (int) $a['posts_count'] ) ),
			);
		}

		foreach ( $columns as $column ) {
			$query = new WP_Query(
				array_merge(
					array(
						'post_status'         => 'publish',
						'no_found_rows'       => true,
						'ignore_sticky_posts' => true,
					),
					$column['query']
				)
			);

			if ( ! $query->have_posts() ) {
				continue;
			}
			?>
			<div>
				<h4 class="zc-footer-title zc-h-sm mb-4 text-[0.95rem] font-bold text-white"><?php echo esc_html( $column['title'] ); ?></h4>
				<ul class="grid gap-2.5 text-[0.86rem] text-white/70">
					<?php
					while ( $query->have_posts() ) :
						$query->the_post();
						?>
						<li><a class="line-clamp-1 transition-colors hover:text-primary" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a></li>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
				</ul>
			</div>
			<?php
		}
	}
endif;

if ( ! function_exists( 'zc_section_shortcode' ) ) :
	/**
	 * شورت‌کد نمایش بخش‌های آماده‌ی قالب در هر برگه.
	 *
	 * نمونه: [zc_section name="services"]  |  [zc_section name="cta" tone="inverse"]
	 * بخش‌های مجاز: hero, marquee, about, services, schema, process, stats, testimonials, faq, blog, cta
	 *
	 * @param array|string $atts ویژگی‌ها.
	 * @return string
	 */
	function zc_section_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'name' => '',
				'tone' => '',
			),
			$atts,
			'zc_section'
		);

		$name  = sanitize_key( (string) $atts['name'] );
		$valid = array( 'hero', 'marquee', 'about', 'services', 'schema', 'process', 'stats', 'testimonials', 'faq', 'blog', 'cta' );

		if ( ! in_array( $name, $valid, true ) ) {
			return '';
		}

		$inverse = 'inverse' === (string) $atts['tone'];

		ob_start();
		if ( $inverse ) {
			echo '<div class="zc-tone-inverse zc-tone-section" data-zc-section="' . esc_attr( $name ) . '">';
		}
		get_template_part( 'template-parts/front-page/' . $name );
		if ( $inverse ) {
			echo '</div>';
		}
		return (string) ob_get_clean();
	}
endif;
add_shortcode( 'zc_section', 'zc_section_shortcode' );


/* ==========================================================================
   سربرگ و پاورقی: قالب المنتور ← مکان المنتور پرو ← نسخه‌ی داخلی قالب
   ========================================================================== */

if ( ! function_exists( 'zc_header_args' ) ) :
	/**
	 * تنظیمات پیش‌فرض سربرگ (از پنل قالب).
	 *
	 * @return array<string, mixed>
	 */
	function zc_header_args() {
		return array(
			'topbar'      => zc_switch( 'header_topbar_enable', true ),
			'topbar_text' => (string) zc_opt( 'header_topbar_text', '' ),
			'topbar_link' => (string) zc_opt( 'header_topbar_link', '#booking' ),
			'show_phone'  => zc_switch( 'header_phone_enable', true ),
			'show_social' => zc_switch( 'header_social_enable', false ),
			'layout'      => (string) zc_opt( 'header_layout', 'classic' ),
			'menu'        => 0,
			'mobile_menu' => 0,
			'show_search' => zc_switch( 'header_search_enable', true ),
			'show_dark'   => 'off' !== (string) zc_opt( 'general_dark_mode', 'toggle' ) && zc_switch( 'header_dark_toggle', true ),
			'cta_text'    => (string) zc_opt( 'header_cta_text', 'رزرو جلسه آشنایی' ),
			'cta_url'     => (string) zc_opt( 'header_cta_url', '#booking' ),
			'show_cart'    => zc_switch( 'header_cart', true ),
			'show_account' => zc_switch( 'header_account', true ),
		);
	}
endif;

if ( ! function_exists( 'zc_footer_args' ) ) :
	/**
	 * تنظیمات پیش‌فرض پاورقی (از پنل قالب).
	 *
	 * @return array<string, mixed>
	 */
	function zc_footer_args() {
		return array(
			'about'          => (string) zc_opt( 'footer_about', '' ),
			'show_socials'   => true,
			'services_title' => __( 'خدمات', 'zarincoach' ),
			'services_count' => 5,
			'posts_title'    => __( 'تازه‌ترین نوشته‌ها', 'zarincoach' ),
			'posts_count'    => 4,
			'contact_title'  => __( 'راه‌های ارتباط', 'zarincoach' ),
			'menu'           => 0,
			'copyright'      => (string) zc_opt( 'footer_copy', 'کلیه حقوق این وب‌سایت محفوظ است.' ),
			'credit'         => zc_switch( 'footer_credit', true ),
			'use_widgets'    => true,
			'glow'           => true,
			'margin'         => ! zc_is_elementor_page(),
			'show_trust'     => true,
			'trust_items'    => zc_trust_default_items(),
			'trust_fallback' => true,
			'show_license'   => true,
			'show_emergency' => true,
			'legal_menu'     => 0,
		);
	}
endif;

if ( ! function_exists( 'zc_is_elementor_page' ) ) :
	/**
	 * آیا برگه‌ی فعلی با المنتور ساخته شده است؟
	 *
	 * @return bool
	 */
	function zc_is_elementor_page() {
		if ( ! is_singular() || ! zc_is_elementor_active() ) {
			return false;
		}
		return function_exists( 'zc_page_uses_elementor' ) && zc_page_uses_elementor( get_queried_object_id() );
	}
endif;

if ( ! function_exists( 'zc_header_menu_args' ) ) :
	/**
	 * آرگومان‌های wp_nav_menu: منوی انتخابی (شناسه) یا جایگاه منو.
	 *
	 * @param int    $menu_id  شناسه منو (۰ = جایگاه).
	 * @param string $location جایگاه منو.
	 * @param array  $args     سایر آرگومان‌ها.
	 * @return array<string, mixed>
	 */
	function zc_header_menu_args( $menu_id, $location, $args ) {
		$menu_id = (int) $menu_id;
		if ( $menu_id > 0 && wp_get_nav_menu_object( $menu_id ) ) {
			$args['menu'] = $menu_id;
		} else {
			$args['theme_location'] = $location;
		}
		if ( ! isset( $args['fallback_cb'] ) ) {
			$args['fallback_cb'] = 'zc_fallback_menu';
		}
		return $args;
	}
endif;

if ( ! function_exists( 'zc_layout_template_id' ) ) :
	/**
	 * شناسه‌ی قالب المنتورِ انتخاب‌شده برای سربرگ/پاورقی.
	 *
	 * @param string $location header|footer.
	 * @return int
	 */
	function zc_layout_template_id( $location ) {
		if ( ! zc_is_elementor_active() || ! class_exists( '\Elementor\Plugin' ) ) {
			return 0;
		}

		$id = (int) zc_opt( $location . '_template', 0 );

		/**
		 * فیلتر قالب المنتور سربرگ/پاورقی (برای تعیین قالب اختصاصی در صفحات خاص).
		 *
		 * @param int    $id       شناسه قالب.
		 * @param string $location مکان.
		 */
		$id = (int) apply_filters( 'zc_layout_template_id', $id, $location );

		if ( $id <= 0 || 'publish' !== get_post_status( $id ) ) {
			return 0;
		}

		// صفحه‌ی خودِ قالب در حال ویرایش/پیش‌نمایش است.
		if ( is_singular( 'elementor_library' ) ) {
			return 0;
		}

		return $id;
	}
endif;

if ( ! function_exists( 'zc_render_layout_template' ) ) :
	/**
	 * خروجی یک قالب المنتور.
	 *
	 * @param int $id شناسه قالب.
	 * @return bool
	 */
	function zc_render_layout_template( $id ) {
		$html = \Elementor\Plugin::instance()->frontend->get_builder_content_for_display( (int) $id );
		if ( '' === trim( (string) $html ) ) {
			return false;
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی المنتور.
		return true;
	}
endif;

if ( ! function_exists( 'zc_render_site_header' ) ) :
	/**
	 * نمایش سربرگ سایت.
	 *
	 * @return void
	 */
	function zc_render_site_header() {
		if ( is_singular( 'elementor_library' ) ) {
			return;
		}

		// Elementor Pro Theme Builder مقدم است: اگر قالب سراسری header با شرط منطبق تعریف شده باشد همان نمایش داده می‌شود.
		if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'header' ) ) {
			return;
		}

		$id = zc_layout_template_id( 'header' );
		if ( $id ) {
			echo '<div class="zc-el-header" data-zc-el-header>';
			$done = zc_render_layout_template( $id );
			echo '</div>';
			if ( $done ) {
				return;
			}
		}


		get_template_part( 'template-parts/site', 'header' );
	}
endif;

if ( ! function_exists( 'zc_render_site_footer' ) ) :
	/**
	 * نمایش پاورقی سایت.
	 *
	 * @return void
	 */
	function zc_render_site_footer() {
		if ( is_singular( 'elementor_library' ) ) {
			return;
		}

		// Elementor Pro Theme Builder مقدم است: اگر قالب سراسری footer با شرط منطبق تعریف شده باشد همان نمایش داده می‌شود.
		if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( 'footer' ) ) {
			return;
		}

		$id = zc_layout_template_id( 'footer' );
		if ( $id ) {
			echo '<div class="' . esc_attr( zc_is_elementor_page() ? 'zc-el-footer' : 'zc-el-footer mt-10 lg:mt-14' ) . '">';
			$done = zc_render_layout_template( $id );
			echo '</div>';
			if ( $done ) {
				return;
			}
		}


		get_template_part( 'template-parts/site', 'footer' );
	}
endif;

if ( ! function_exists( 'zc_enqueue_layout_templates_css' ) ) :
	/**
	 * بارگذاری CSS قالب‌های سربرگ/پاورقی المنتور در <head> (پیش از نمایش).
	 *
	 * @return void
	 */
	function zc_enqueue_layout_templates_css() {
		$ids = array_filter( array( zc_layout_template_id( 'header' ), zc_layout_template_id( 'footer' ) ) );
		if ( empty( $ids ) ) {
			return;
		}

		$plugin = \Elementor\Plugin::instance();
		$plugin->frontend->enqueue_styles();

		foreach ( $ids as $id ) {
			if ( class_exists( '\Elementor\Core\Files\CSS\Post' ) ) {
				\Elementor\Core\Files\CSS\Post::create( $id )->enqueue();
			}
		}
	}
endif;
add_action( 'wp_enqueue_scripts', 'zc_enqueue_layout_templates_css', 20 );

if ( ! function_exists( 'zc_testimonials_source' ) ) :
	/**
	 * یادداشت شفاف درباره‌ی منبع نظرات؛ مقادیر خالی از تنظیمات قالب خوانده می‌شوند.
	 *
	 * @param string $note  یادداشت.
	 * @param string $label متن پیوند.
	 * @param string $url   نشانی منبع.
	 * @return void
	 */
	function zc_testimonials_source( $note = '', $label = '', $url = '' ) {
		$note  = '' !== $note ? $note : trim( (string) zc_opt( 'testimonials_source_note', '' ) );
		$label = '' !== $label ? $label : trim( (string) zc_opt( 'testimonials_source_label', '' ) );
		$url   = '' !== $url ? $url : trim( (string) zc_opt( 'testimonials_source_url', '' ) );
		$label = '' !== $label ? $label : __( 'مشاهده همه نظرات', 'zarincoach' );

		if ( '' === $note && '' === $url ) {
			return;
		}
		?>
		<p class="zc-testimonials-source mx-auto mt-8 flex max-w-3xl flex-wrap items-center justify-center gap-x-3 gap-y-1 text-center text-[0.8rem] leading-7 text-muted">
			<?php zc_icon( 'shield', 'h-4 w-4 shrink-0 text-accent' ); ?>
			<?php if ( '' !== $note ) : ?>
				<span><?php echo esc_html( $note ); ?></span>
			<?php endif; ?>
			<?php if ( '' !== $url ) : ?>
				<a class="inline-flex items-center gap-1 font-bold text-primary underline-offset-4 hover:underline" href="<?php echo esc_url( $url ); ?>" target="_blank" rel="noopener nofollow"><?php echo esc_html( $label ); ?><?php zc_icon( 'external', 'h-3.5 w-3.5' ); ?></a>
			<?php endif; ?>
		</p>
		<?php
	}
endif;
