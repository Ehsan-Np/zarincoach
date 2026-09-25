<?php
/**
 * سربرگ سایت (نوار بالا، منو، ابزارها و منوی موبایل)
 *
 * مشترک بین header.php (حالت پیش‌فرض) و ویجت المنتور «سربرگ سایت».
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$a       = wp_parse_args( isset( $args ) && is_array( $args ) ? $args : array(), zc_header_args() );
$a['layout'] = in_array( $a['layout'], array( 'classic', 'centered', 'minimal' ), true ) ? $a['layout'] : 'classic';
$contact = zc_contact_fields();
$socials = zc_socials();
?>
<?php if ( $a['topbar'] ) : ?>
	<div data-zc-topbar class="relative z-[55] hidden border-b border-line bg-secondary text-white lg:block">
		<div class="zc-container flex h-11 items-center justify-between gap-4 text-[0.78rem]">
			<div class="flex min-w-0 items-center gap-2">
				<?php zc_icon( 'sparkles', 'h-4 w-4 shrink-0 text-primary' ); ?>
				<?php if ( '' !== $a['topbar_link'] ) : ?>
					<a class="truncate text-white/90 transition hover:text-primary" href="<?php echo esc_url( $a['topbar_link'] ); ?>">
						<?php echo esc_html( $a['topbar_text'] ); ?>
					</a>
				<?php else : ?>
					<span class="truncate text-white/90"><?php echo esc_html( $a['topbar_text'] ); ?></span>
				<?php endif; ?>
			</div>

			<div class="flex shrink-0 items-center gap-5">
				<?php if ( $a['show_phone'] && '' !== $contact['phone'] ) : ?>
					<a class="inline-flex items-center gap-2 text-white/85 transition hover:text-primary" href="tel:<?php echo esc_attr( zc_normalize_phone( $contact['phone'] ) ); ?>">
						<?php zc_icon( 'phone', 'h-4 w-4' ); ?>
						<span dir="ltr" class="text-[0.8rem]"><?php echo esc_html( $contact['phone'] ); ?></span>
					</a>
				<?php endif; ?>

				<?php if ( $a['show_social'] ) : ?>
					<div class="flex items-center gap-2">
						<?php foreach ( array_slice( $socials, 0, 4 ) as $item ) : ?>
							<a class="text-white/80 transition hover:text-primary" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
								<?php zc_icon( $item['icon'], 'h-4 w-4' ); ?>
							</a>
						<?php endforeach; ?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	</div>
<?php endif; ?>

<header data-zc-header class="zc-header">
	<div class="zc-container">
		<div class="flex items-center justify-between gap-4 py-3 lg:py-4">

			<!-- لوگو -->
			<div class="flex shrink-0 items-center">
				<?php zc_site_branding(); ?>
			</div>

			<!-- منوی اصلی -->
			<?php if ( 'classic' === $a['layout'] ) : ?>
				<nav class="hidden items-center gap-1 lg:flex" aria-label="<?php esc_attr_e( 'منوی اصلی', 'zarincoach' ); ?>">
					<?php
					wp_nav_menu(
						zc_header_menu_args(
							$a['menu'],
							'zc-primary',
							array(
								'container'      => false,
								'menu_class'     => 'flex items-center gap-1',
								'fallback_cb'    => 'zc_fallback_menu',
								'depth'          => 2,
								'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
							)
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<!-- ابزارها -->
			<div class="flex items-center gap-2">
				<?php if ( $a['show_search'] ) : ?>
					<button type="button" data-zc-search-toggle class="zc-btn-icon hidden sm:grid" aria-label="<?php esc_attr_e( 'جستجو', 'zarincoach' ); ?>">
						<?php zc_icon( 'search', 'h-[18px] w-[18px]' ); ?>
					</button>
				<?php endif; ?>

				<?php if ( $a['show_dark'] ) : ?>
					<button type="button" data-zc-theme-toggle class="zc-btn-icon hidden sm:grid" aria-pressed="false" aria-label="<?php esc_attr_e( 'تغییر حالت تاریک', 'zarincoach' ); ?>">
						<span class="block dark:hidden"><?php zc_icon( 'moon', 'h-[18px] w-[18px]' ); ?></span>
						<span class="hidden dark:block"><?php zc_icon( 'sun', 'h-[18px] w-[18px]' ); ?></span>
					</button>
				<?php endif; ?>

				<?php
				if ( function_exists( 'zc_wc_header_tools' ) ) {
					echo zc_wc_header_tools( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی درون تابع escape شده است.
						array(
							'show_cart'    => ! empty( $a['show_cart'] ),
							'show_account' => ! empty( $a['show_account'] ),
						)
					);
				}
				?>

				<?php if ( '' !== $a['cta_text'] ) : ?>
					<a href="<?php echo esc_url( $a['cta_url'] ); ?>" class="zc-btn zc-btn-primary zc-btn-sm hidden lg:inline-flex">
						<?php echo esc_html( $a['cta_text'] ); ?>
						<?php zc_icon( 'arrow-left', 'h-4 w-4 zc-btn-arrow' ); ?>
					</a>
				<?php endif; ?>

				<button type="button" data-zc-drawer-open class="<?php echo esc_attr( 'minimal' === $a['layout'] ? 'zc-hamburger' : 'zc-hamburger lg:hidden' ); ?>" aria-label="<?php esc_attr_e( 'باز کردن منو', 'zarincoach' ); ?>" aria-controls="zc-drawer" aria-expanded="false">
					<span></span><span></span><span></span>
				</button>
			</div>
		</div>

		<?php if ( 'centered' === $a['layout'] ) : ?>
			<nav class="hidden justify-center border-t border-line py-3 lg:flex" aria-label="<?php esc_attr_e( 'منوی اصلی', 'zarincoach' ); ?>">
				<?php
				wp_nav_menu(
					zc_header_menu_args(
						$a['menu'],
						'zc-primary',
						array(
							'container'      => false,
							'menu_class'     => 'flex items-center gap-1',
							'fallback_cb'    => 'zc_fallback_menu',
							'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					)
				);
				?>
			</nav>
		<?php endif; ?>
	</div>

	<!-- پنل جستجو -->
	<?php if ( $a['show_search'] ) : ?>
		<div data-zc-search-panel class="absolute inset-x-0 top-full z-40 hidden border-b border-line bg-base/95 px-5 py-4 shadow-soft backdrop-blur-xl [&.is-open]:block">
			<div class="zc-container">
				<?php get_search_form(); ?>
			</div>
		</div>
	<?php endif; ?>

	<span data-zc-progress class="zc-progress" aria-hidden="true"></span>
</header>

<!-- منوی موبایل -->
<div data-zc-drawer id="zc-drawer" class="zc-drawer" aria-hidden="true" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'منو', 'zarincoach' ); ?>">
	<div data-zc-drawer-backdrop class="zc-drawer-backdrop"></div>

	<div class="zc-drawer-panel">
		<div class="flex items-center justify-between">
			<?php zc_site_branding(); ?>
			<button type="button" data-zc-drawer-close class="zc-btn-icon" aria-label="<?php esc_attr_e( 'بستن منو', 'zarincoach' ); ?>">
				<?php zc_icon( 'close', 'h-5 w-5' ); ?>
			</button>
		</div>

		<nav aria-label="<?php esc_attr_e( 'منوی موبایل', 'zarincoach' ); ?>">
			<?php
			wp_nav_menu(
				zc_header_menu_args(
					$a['mobile_menu'],
					'zc-mobile',
					array(
						'container'      => false,
						'menu_class'     => 'flex flex-col gap-1',
						'fallback_cb'    => 'zc_fallback_menu',
						'items_wrap'     => '<ul id="%1$s" class="%2$s">%3$s</ul>',
					)
				)
			);
			?>
		</nav>

		<?php if ( '' !== $a['cta_text'] ) : ?>
			<a href="<?php echo esc_url( $a['cta_url'] ); ?>" class="zc-btn zc-btn-primary zc-btn-block zc-btn-lg">
				<?php echo esc_html( $a['cta_text'] ); ?>
			</a>
		<?php endif; ?>

		<div class="mt-auto grid gap-3 text-[0.85rem] text-muted">
			<?php foreach ( array( 'phone', 'phone2' ) as $zc_pk ) : ?>
				<?php if ( '' !== $contact[ $zc_pk ] ) : ?>
					<a class="inline-flex items-center gap-2 hover:text-primary" href="tel:<?php echo esc_attr( zc_normalize_phone( $contact[ $zc_pk ] ) ); ?>">
						<?php zc_icon( 'phone', 'h-4 w-4' ); ?>
						<span><?php echo esc_html( $contact[ $zc_pk . '_label' ] ); ?>:</span>
						<span dir="ltr"><?php echo esc_html( zc_digits_to_persian( $contact[ $zc_pk ] ) ); ?></span>
					</a>
				<?php endif; ?>
			<?php endforeach; ?>
			<?php if ( '' !== $contact['email'] ) : ?>
				<a class="inline-flex items-center gap-2 hover:text-primary" href="mailto:<?php echo esc_attr( $contact['email'] ); ?>">
					<?php zc_icon( 'mail', 'h-4 w-4' ); ?>
					<span dir="ltr"><?php echo esc_html( $contact['email'] ); ?></span>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $socials ) ) : ?>
			<div class="flex items-center gap-2">
				<?php foreach ( $socials as $item ) : ?>
					<a class="zc-social" href="<?php echo esc_url( $item['url'] ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( $item['label'] ); ?>">
						<?php zc_icon( $item['icon'], 'h-[18px] w-[18px]' ); ?>
					</a>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
	</div>
</div>
