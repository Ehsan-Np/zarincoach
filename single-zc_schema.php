<?php
/**
 * قالب صفحه‌ی هر طرحواره / ذهنیت / سبک مقابله / خطای شناختی
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( zc_page_uses_elementor( get_the_ID() ) && zc_is_elementor_active() ) {
	get_template_part( 'template-parts/page', 'elementor' );
	return;
}

get_header();

while ( have_posts() ) :
	the_post();

	$item   = zc_sc_get( get_the_ID() );
	$labels = zc_sc_type_labels( $item['type'] );
	$gdef   = $item['group'] ? zc_sc_group( $item['group']->slug ) : array();
	$tdef   = $item['top'] ? zc_sc_group( $item['top']->slug ) : array();
	$fa     = static function ( $n ) {
		return function_exists( 'zc_digits_to_persian' ) ? zc_digits_to_persian( (string) $n ) : (string) $n;
	};

	// بخش‌ها (برای فهرست و بدنه).
	$content  = trim( (string) get_the_content() );
	$sections = array();
	if ( '' !== $content ) {
		$sections['intro'] = $labels['intro'];
	}
	foreach ( array( 'need', 'signs', 'origins', 'triggers' ) as $k ) {
		if ( ! empty( $item[ $k ] ) ) {
			$sections[ $k ] = $labels[ $k ];
		}
	}
	$cope = array();
	if ( 'ems' === $item['type'] ) {
		$cope_map = array(
			'cope_surrender' => array( 'surrender', __( 'تسلیم', 'zarincoach' ), 'hand' ),
			'cope_avoid'     => array( 'avoidance', __( 'اجتناب', 'zarincoach' ), 'eye' ),
			'cope_over'      => array( 'overcompensation', __( 'جبران افراطی', 'zarincoach' ), 'shield' ),
		);
		foreach ( $cope_map as $k => $def ) {
			if ( '' !== (string) $item[ $k ] ) {
				$target = zc_sc_find( $def[0] );
				$cope[] = array(
					'label' => $def[1],
					'icon'  => $def[2],
					'text'  => (string) $item[ $k ],
					'url'   => ! empty( $target['url'] ) ? $target['url'] : '',
				);
			}
		}
		if ( $cope ) {
			$sections['cope'] = $labels['cope'];
		}
	}
	foreach ( array( 'example', 'healthy', 'exercise' ) as $k ) {
		if ( ! empty( $item[ $k ] ) ) {
			$sections[ $k ] = $labels[ $k ];
		}
	}

	$toc_items = array();
	foreach ( $sections as $k => $label ) {
		$toc_items[] = array(
			'level' => 2,
			'id'    => 'sc-' . $k,
			'text'  => $label,
		);
	}

	// مرتبط‌ها.
	$related = array();
	foreach ( (array) $item['related'] as $slug ) {
		$r = zc_sc_find( $slug );
		if ( $r && (int) $r['id'] !== (int) $item['id'] ) {
			$related[] = $r;
		}
	}

	// قبلی/بعدی در همان گروه.
	$siblings = array();
	if ( $item['group'] ) {
		$map      = zc_sc_by_group();
		$siblings = isset( $map[ $item['group']->slug ] ) ? $map[ $item['group']->slug ] : array();
	}
	$prev = null;
	$next = null;
	foreach ( $siblings as $i => $s ) {
		if ( (int) $s['id'] === (int) $item['id'] ) {
			$prev = $i > 0 ? $siblings[ $i - 1 ] : null;
			$next = isset( $siblings[ $i + 1 ] ) ? $siblings[ $i + 1 ] : null;
			break;
		}
	}
	$position = 0;
	foreach ( $siblings as $i => $s ) {
		if ( (int) $s['id'] === (int) $item['id'] ) {
			$position = $i + 1;
		}
	}

	$hub_url = zc_sc_hub_url();
	$booking = function_exists( 'zc_page_url_by_key' ) ? zc_page_url_by_key( 'booking' ) : home_url( '/booking/' );

	// تنظیمات پنل ← «کتابخانه‌ی طرحواره‌ها».
	$sc_show_toc   = zc_switch( 'sc_toc', true );
	$sc_show_pager = zc_switch( 'sc_pager', true );
	$sc_disclaimer = trim( (string) zc_opt( 'sc_disclaimer', '' ) );
	$sc_cta        = zc_switch( 'sc_cta_enable', true );
	$sc_cta_url    = trim( (string) zc_opt( 'sc_cta_btn_url', '' ) );
	$sc_cta_url    = '' !== $sc_cta_url ? zc_url( $sc_cta_url ) : $booking;
	if ( ! zc_switch( 'sc_related', true ) ) {
		$related = array();
	}
	?>

	<section class="<?php echo esc_attr( zc_page_header_class( 'zc-sc-hero' ) ); ?>" style="--sc:<?php echo esc_attr( $item['color'] ); ?>">
		<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
		<div class="zc-sc-hero-glow pointer-events-none absolute -top-24 end-1/4 -z-10 h-72 w-72 rounded-full blur-3xl"></div>

		<div class="zc-container relative py-9 lg:py-12">
			<?php zc_breadcrumbs(); ?>

			<div class="zc-sc-hero-grid">
				<div class="zc-sc-hero-main">
					<div class="zc-sc-hero-meta">
						<span class="zc-sc-hero-code<?php echo '' !== (string) $item['code'] ? ' is-code' : ''; ?>"<?php echo '' !== (string) $item['code'] ? ' dir="ltr"' : ''; ?>><?php echo esc_html( zc_sc_number( $item ) ); ?></span>
						<span class="zc-sc-kind"><?php echo esc_html( $labels['kind'] ); ?></span>
						<?php if ( $item['group'] ) : ?>
							<a class="zc-sc-group-chip" href="<?php echo esc_url( (string) get_term_link( $item['group'] ) ); ?>">
								<?php zc_icon( (string) $item['icon'], 'h-3.5 w-3.5' ); ?>
								<?php echo esc_html( $item['group']->name ); ?>
							</a>
						<?php endif; ?>
					</div>

					<h1 class="zc-title-lg zc-text-balance mt-4"><?php echo esc_html( $item['title'] ); ?></h1>
					<?php if ( '' !== (string) $item['en'] ) : ?>
						<p class="zc-sc-hero-en" dir="ltr" lang="en"><?php echo esc_html( $item['en'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== (string) $item['summary'] ) : ?>
						<p class="zc-lead mt-5 max-w-2xl"><?php echo esc_html( $item['summary'] ); ?></p>
					<?php endif; ?>
				</div>

				<?php if ( '' !== (string) $item['belief'] ) : ?>
					<figure class="zc-sc-belief">
						<figcaption class="zc-sc-belief-label"><?php zc_icon( 'quote', 'h-4 w-4' ); ?><?php echo esc_html( $labels['belief'] ); ?></figcaption>
						<blockquote class="zc-sc-belief-text">«<?php echo esc_html( trim( (string) $item['belief'], '«» ' ) ); ?>»</blockquote>
					</figure>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="zc-section zc-sc-body" style="--sc:<?php echo esc_attr( $item['color'] ); ?>">
		<div class="zc-container">
			<div class="grid gap-8 lg:grid-cols-12 lg:gap-12">

				<aside class="lg:order-last lg:col-span-4" aria-label="<?php esc_attr_e( 'راهنمای صفحه', 'zarincoach' ); ?>">
					<div class="zc-sc-aside">
						<?php
						if ( $sc_show_toc && count( $toc_items ) >= 3 ) {
							echo zc_toc_render( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
								$toc_items,
								array(
									'title'         => __( 'در این صفحه', 'zarincoach' ),
									'columns'       => 1,
									'style'         => 'card',
									'numbering'     => 'decimal',
									'collapsible'   => true,
									'open'          => true,
									'mobile_closed' => true,
									'meta'          => true,
									'floating'      => false,
									'class'         => 'zc-sc-toc',
								)
							);
						}
						?>

						<div class="zc-sc-facts">
							<dl>
								<?php if ( $item['top'] ) : ?>
									<div><dt><?php esc_html_e( 'دسته', 'zarincoach' ); ?></dt><dd><a href="<?php echo esc_url( (string) get_term_link( $item['top'] ) ); ?>"><?php echo esc_html( $item['top']->name ); ?></a></dd></div>
								<?php endif; ?>
								<?php if ( $item['group'] && $item['top'] && $item['group']->term_id !== $item['top']->term_id ) : ?>
									<div><dt><?php echo 'ems' === $item['type'] ? esc_html__( 'حوزه', 'zarincoach' ) : esc_html__( 'گروه', 'zarincoach' ); ?></dt><dd><a href="<?php echo esc_url( (string) get_term_link( $item['group'] ) ); ?>"><?php echo esc_html( ! empty( $gdef['short'] ) ? $gdef['short'] : $item['group']->name ); ?></a></dd></div>
								<?php endif; ?>
								<?php if ( ! empty( $gdef['need'] ) ) : ?>
									<div><dt><?php esc_html_e( 'نیاز بنیادین', 'zarincoach' ); ?></dt><dd><?php echo esc_html( $gdef['need'] ); ?></dd></div>
								<?php endif; ?>
								<?php if ( $position && count( $siblings ) > 1 ) : ?>
									<div><dt><?php esc_html_e( 'جایگاه', 'zarincoach' ); ?></dt><dd><?php echo esc_html( sprintf( /* translators: 1: شماره 2: کل */ __( '%1$s از %2$s', 'zarincoach' ), $fa( $position ), $fa( count( $siblings ) ) ) ); ?></dd></div>
								<?php endif; ?>
							</dl>
							<a class="zc-sc-facts-cta" href="<?php echo esc_url( $booking ); ?>">
								<?php zc_icon( 'calendar', 'h-4 w-4' ); ?>
								<?php esc_html_e( 'جلسه‌ی ارزیابی طرحواره', 'zarincoach' ); ?>
							</a>
						</div>
					</div>
				</aside>

				<article id="post-<?php the_ID(); ?>" <?php post_class( 'zc-sc-article lg:col-span-8' ); ?>>

					<?php if ( isset( $sections['intro'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-intro">
							<h2 id="sc-intro" class="zc-sc-block-title"><?php echo esc_html( $sections['intro'] ); ?></h2>
							<div class="zc-prose !max-w-none">
								<?php the_content(); ?>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['need'] ) ) : ?>
						<section class="zc-sc-block zc-sc-need-block" aria-labelledby="sc-need">
							<span class="zc-sc-block-icon" aria-hidden="true"><?php zc_icon( 'heart', 'h-5 w-5' ); ?></span>
							<div>
								<h2 id="sc-need" class="zc-sc-block-title"><?php echo esc_html( $sections['need'] ); ?></h2>
								<p class="zc-sc-p"><?php echo esc_html( $item['need'] ); ?></p>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['signs'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-signs">
							<h2 id="sc-signs" class="zc-sc-block-title"><?php echo esc_html( $sections['signs'] ); ?></h2>
							<ul class="zc-sc-checks">
								<?php foreach ( $item['signs'] as $line ) : ?>
									<li><span class="zc-sc-check" aria-hidden="true"><?php zc_icon( 'check', 'h-3.5 w-3.5' ); ?></span><span><?php echo esc_html( $line ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['origins'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-origins">
							<h2 id="sc-origins" class="zc-sc-block-title"><?php echo esc_html( $sections['origins'] ); ?></h2>
							<ul class="zc-sc-dots">
								<?php foreach ( $item['origins'] as $line ) : ?>
									<li><?php echo esc_html( $line ); ?></li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['triggers'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-triggers">
							<h2 id="sc-triggers" class="zc-sc-block-title"><?php echo esc_html( $sections['triggers'] ); ?></h2>
							<ul class="zc-sc-tags">
								<?php foreach ( $item['triggers'] as $line ) : ?>
									<li><?php zc_icon( 'alert', 'h-3.5 w-3.5' ); ?><span><?php echo esc_html( $line ); ?></span></li>
								<?php endforeach; ?>
							</ul>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['cope'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-cope">
							<h2 id="sc-cope" class="zc-sc-block-title"><?php echo esc_html( $sections['cope'] ); ?></h2>
							<p class="zc-sc-p zc-sc-muted"><?php esc_html_e( 'یک طرحواره‌ی واحد می‌تواند در سه آدم مختلف، به سه شکل کاملاً متفاوت دیده شود. شناختن سبک غالب، اولین قدم برای انتخاب آگاهانه است.', 'zarincoach' ); ?></p>
							<div class="zc-sc-cope">
								<?php foreach ( $cope as $c ) : ?>
									<div class="zc-sc-cope-card">
										<p class="zc-sc-cope-head">
											<span class="zc-sc-cope-icon" aria-hidden="true"><?php zc_icon( $c['icon'], 'h-4 w-4' ); ?></span>
											<?php if ( '' !== $c['url'] ) : ?>
												<a href="<?php echo esc_url( $c['url'] ); ?>"><?php echo esc_html( $c['label'] ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $c['label'] ); ?>
											<?php endif; ?>
										</p>
										<p class="zc-sc-cope-text"><?php echo esc_html( $c['text'] ); ?></p>
									</div>
								<?php endforeach; ?>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['example'] ) ) : ?>
						<section class="zc-sc-block zc-sc-story" aria-labelledby="sc-example">
							<h2 id="sc-example" class="zc-sc-block-title"><?php echo esc_html( $sections['example'] ); ?></h2>
							<p class="zc-sc-p"><?php echo esc_html( zc_sc_strip_label( (string) $item['example'], $labels['example'] ) ); ?></p>
							<?php if ( 'distortion' !== $item['type'] ) : ?>
								<p class="zc-sc-note"><?php esc_html_e( 'نام‌ها و جزئیات این نمونه ساختگی‌اند و برای آموزش نوشته شده‌اند.', 'zarincoach' ); ?></p>
							<?php endif; ?>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['healthy'] ) ) : ?>
						<section class="zc-sc-block zc-sc-healthy" aria-labelledby="sc-healthy">
							<span class="zc-sc-block-icon" aria-hidden="true"><?php zc_icon( 'sun', 'h-5 w-5' ); ?></span>
							<div>
								<h2 id="sc-healthy" class="zc-sc-block-title"><?php echo esc_html( $sections['healthy'] ); ?></h2>
								<p class="zc-sc-p"><?php echo esc_html( zc_sc_strip_label( (string) $item['healthy'], $labels['healthy'] ) ); ?></p>
							</div>
						</section>
					<?php endif; ?>

					<?php if ( isset( $sections['exercise'] ) ) : ?>
						<section class="zc-sc-block" aria-labelledby="sc-exercise">
							<h2 id="sc-exercise" class="zc-sc-block-title"><?php echo esc_html( $sections['exercise'] ); ?></h2>
							<ol class="zc-sc-steps">
								<?php foreach ( $item['exercise'] as $line ) : ?>
									<li><?php echo esc_html( $line ); ?></li>
								<?php endforeach; ?>
							</ol>
						</section>
					<?php endif; ?>

<?php if ( '' !== $sc_disclaimer ) : ?>
					<aside class="zc-sc-disclaimer" role="note">
						<span class="zc-sc-disclaimer-icon" aria-hidden="true"><?php zc_icon( 'shield-check', 'h-5 w-5' ); ?></span>
						<p>
							<strong><?php esc_html_e( 'یادداشت آموزشی:', 'zarincoach' ); ?></strong>
							<?php echo esc_html( $sc_disclaimer ); ?>
						</p>
					</aside>
					<?php endif; ?>

					<?php if ( $sc_show_pager && ( $prev || $next ) ) : ?>
						<nav class="zc-sc-pager" aria-label="<?php esc_attr_e( 'مدخل‌های همین گروه', 'zarincoach' ); ?>">
							<?php if ( $prev ) : ?>
								<a class="zc-sc-pager-link is-prev" href="<?php echo esc_url( $prev['url'] ); ?>" rel="prev">
									<span class="zc-sc-pager-dir"><?php zc_icon( 'arrow-right', 'h-4 w-4' ); ?><?php esc_html_e( 'قبلی', 'zarincoach' ); ?></span>
									<span class="zc-sc-pager-title"><?php echo esc_html( $prev['title'] ); ?></span>
								</a>
							<?php else : ?>
								<span></span>
							<?php endif; ?>
							<?php if ( $next ) : ?>
								<a class="zc-sc-pager-link is-next" href="<?php echo esc_url( $next['url'] ); ?>" rel="next">
									<span class="zc-sc-pager-dir"><?php esc_html_e( 'بعدی', 'zarincoach' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></span>
									<span class="zc-sc-pager-title"><?php echo esc_html( $next['title'] ); ?></span>
								</a>
							<?php endif; ?>
						</nav>
					<?php endif; ?>
				</article>
			</div>
		</div>
	</section>

	<?php if ( $related ) : ?>
		<section class="zc-section zc-sc-related !pt-0">
			<div class="zc-container">
				<div class="zc-sc-related-head">
					<h2 class="zc-title !text-[1.45rem]"><?php echo esc_html( $labels['related'] ); ?></h2>
					<a class="zc-sc-link" href="<?php echo esc_url( $hub_url ); ?>"><?php esc_html_e( 'همه‌ی طرحواره‌ها و الگوها', 'zarincoach' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
				</div>
				<div class="<?php echo esc_attr( zc_sc_grid_class( min( 4, count( $related ) ) ) ); ?> mt-6">
					<?php
					foreach ( $related as $r ) {
						echo zc_sc_card( $r, array( 'show_group' => true ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</section>
	<?php endif; ?>

	<?php if ( $sc_cta ) : ?>
		<?php
		$sc_cta_eyebrow = trim( (string) zc_opt( 'sc_cta_eyebrow', '' ) );
		$sc_cta_title   = trim( (string) zc_opt( 'sc_cta_title', '' ) );
		$sc_cta_text    = trim( (string) zc_opt( 'sc_cta_text', '' ) );
		$sc_cta_btn     = trim( (string) zc_opt( 'sc_cta_btn_text', '' ) );
		?>
	<section class="zc-section !pt-0">
		<div class="zc-container">
			<div class="zc-panel-dark zc-sc-cta">
				<div>
					<?php if ( '' !== $sc_cta_eyebrow ) : ?>
						<p class="zc-sc-cta-eyebrow"><?php echo esc_html( $sc_cta_eyebrow ); ?></p>
					<?php endif; ?>
					<?php if ( '' !== $sc_cta_title ) : ?>
						<h2 class="zc-sc-cta-title"><?php echo esc_html( $sc_cta_title ); ?></h2>
					<?php endif; ?>
					<?php if ( '' !== $sc_cta_text ) : ?>
						<p class="zc-sc-cta-text"><?php echo esc_html( $sc_cta_text ); ?></p>
					<?php endif; ?>
				</div>
				<div class="zc-sc-cta-actions">
					<?php
					if ( '' !== $sc_cta_btn ) {
						zc_button(
							array(
								'text'  => $sc_cta_btn,
								'url'   => $sc_cta_url,
								'style' => 'primary',
							)
						);
					}
					if ( zc_switch( 'sc_cta_back', true ) ) {
						zc_button(
							array(
								'text'  => __( 'بازگشت به کتابخانه', 'zarincoach' ),
								'url'   => $hub_url,
								'style' => 'ghost',
								'icon'  => 'list',
							)
						);
					}
					?>
				</div>
			</div>
		</div>
	</section>
	<?php endif; ?>

	<?php
endwhile;

get_footer();
