<?php
/**
 * آرشیو گروه طرحواره‌ها (دسته‌ی اصلی یا حوزه/زیرگروه)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();

$term     = get_queried_object();
$def      = $term ? zc_sc_group( $term->slug ) : array();
$color    = ! empty( $def['color'] ) ? $def['color'] : '#1D3A72';
$desc     = ! empty( $term->description ) ? $term->description : ( ! empty( $def['desc'] ) ? $def['desc'] : '' );
$is_top   = $term && empty( $term->parent );
$parent   = ( $term && $term->parent ) ? get_term( $term->parent, 'zc_schema_group' ) : null;
$hub_url  = zc_sc_hub_url();
$map      = zc_sc_by_group();
$siblings = ( $parent && ! is_wp_error( $parent ) ) ? zc_sc_child_groups( $parent->slug ) : array();
?>

<section class="<?php echo esc_attr( zc_page_header_class( 'zc-sc-hero' ) ); ?>" style="--sc:<?php echo esc_attr( $color ); ?>">
	<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
	<div class="zc-sc-hero-glow pointer-events-none absolute -top-24 end-1/4 -z-10 h-72 w-72 rounded-full blur-3xl"></div>

	<div class="zc-container relative py-9 lg:py-12">
		<?php zc_breadcrumbs(); ?>

		<div class="mt-5 flex flex-col items-start gap-5 sm:flex-row sm:items-center">
			<span class="zc-sc-sec-icon !h-16 !w-16" aria-hidden="true"><?php zc_icon( ! empty( $def['icon'] ) ? (string) $def['icon'] : 'brain', 'h-8 w-8' ); ?></span>
			<div>
				<h1 class="zc-title-lg zc-text-balance"><?php echo esc_html( $term ? $term->name : '' ); ?></h1>
				<?php if ( ! empty( $def['need'] ) ) : ?>
					<span class="zc-sc-need mt-3"><?php zc_icon( 'heart', 'h-3.5 w-3.5' ); ?><?php echo esc_html( sprintf( /* translators: %s: نیاز */ __( 'نیاز بنیادین: %s', 'zarincoach' ), $def['need'] ) ); ?></span>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( '' !== $desc ) : ?>
			<p class="zc-lead mt-5 max-w-3xl"><?php echo esc_html( $desc ); ?></p>
		<?php endif; ?>

		<?php if ( count( $siblings ) > 1 ) : ?>
			<nav class="zc-sc-siblings" aria-label="<?php esc_attr_e( 'گروه‌های هم‌سطح', 'zarincoach' ); ?>">
				<?php foreach ( $siblings as $slug => $sdef ) : ?>
					<?php $link = get_term_link( $slug, 'zc_schema_group' ); ?>
					<?php if ( is_wp_error( $link ) ) { continue; } ?>
					<a class="zc-sc-chip<?php echo ( $term && $slug === $term->slug ) ? ' is-active' : ''; ?>" href="<?php echo esc_url( $link ); ?>" style="--sc:<?php echo esc_attr( $sdef['color'] ); ?>"<?php echo ( $term && $slug === $term->slug ) ? ' aria-current="page"' : ''; ?>>
						<?php echo esc_html( $sdef['short'] ); ?>
						<span class="zc-sc-chip-count"><?php echo esc_html( zc_digits_to_persian( (string) ( isset( $map[ $slug ] ) ? count( $map[ $slug ] ) : 0 ) ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
		<?php endif; ?>
	</div>
</section>

<section class="zc-section" style="--sc:<?php echo esc_attr( $color ); ?>">
	<div class="zc-container">
		<?php
		if ( $is_top ) {
			echo zc_schemas_grid( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'groups'     => array( $term->slug ),
					'filter'     => false,
					'search'     => true,
					'group_desc' => true,
					'columns'    => 3,
				)
			);
		} else {
			$items = ( $term && isset( $map[ $term->slug ] ) ) ? $map[ $term->slug ] : array();
			if ( $items ) {
				echo '<div class="' . esc_attr( zc_sc_grid_class( 3 ) ) . '">';
				foreach ( $items as $item ) {
					echo zc_sc_card( $item, array( 'tag' => 'h2' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				echo '</div>';
			} else {
				echo '<p class="zc-lead">' . esc_html__( 'هنوز مدخلی در این گروه منتشر نشده است.', 'zarincoach' ) . '</p>';
			}
		}
		?>

		<div class="zc-sc-archive-foot">
			<?php if ( $parent && ! is_wp_error( $parent ) ) : ?>
				<a class="zc-sc-link" href="<?php echo esc_url( (string) get_term_link( $parent ) ); ?>"><?php zc_icon( 'arrow-right', 'h-4 w-4' ); ?><?php echo esc_html( sprintf( /* translators: %s: گروه والد */ __( 'همه‌ی %s', 'zarincoach' ), $parent->name ) ); ?></a>
			<?php endif; ?>
			<a class="zc-sc-link" href="<?php echo esc_url( $hub_url ); ?>"><?php esc_html_e( 'کتابخانه‌ی کامل طرحواره‌ها و الگوهای ذهنی', 'zarincoach' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
		</div>

		<?php $zc_sc_gdisc = trim( (string) zc_opt( 'sc_group_disclaimer', '' ) ); ?>
		<?php if ( '' !== $zc_sc_gdisc ) : ?>
		<aside class="zc-sc-disclaimer mt-8" role="note">
			<span class="zc-sc-disclaimer-icon" aria-hidden="true"><?php zc_icon( 'shield-check', 'h-5 w-5' ); ?></span>
			<p><strong><?php esc_html_e( 'یادداشت آموزشی:', 'zarincoach' ); ?></strong> <?php echo esc_html( $zc_sc_gdisc ); ?></p>
		</aside>
		<?php endif; ?>
	</div>
</section>

<?php
get_footer();
