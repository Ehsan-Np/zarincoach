<?php
/**
 * My Account navigation — ستون کناری پیشخوان مشتری
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$zc_user     = wp_get_current_user();
$zc_sections = function_exists( 'zc_acc_sections' ) ? zc_acc_sections() : array();
$zc_stats    = function_exists( 'zc_acc_stats' ) ? zc_acc_stats( $zc_user->ID ) : array();
$zc_badges   = array(
	'orders'    => isset( $zc_stats['active'] ) ? (int) $zc_stats['active'] : 0,
	'downloads' => isset( $zc_stats['downloads'] ) ? (int) $zc_stats['downloads'] : 0,
);
$zc_since    = function_exists( 'zc_acc_member_since' ) ? zc_acc_member_since( $zc_user ) : '';

do_action( 'woocommerce_before_account_navigation' );
?>

<aside class="zc-ma__side">
	<?php if ( $zc_user->exists() ) : ?>
		<div class="zc-ma-user">
			<?php echo zc_acc_avatar( $zc_user ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی ایمن. ?>
			<div class="zc-ma-user__meta">
				<strong><?php echo esc_html( $zc_user->display_name ); ?></strong>
				<span dir="ltr"><?php echo esc_html( $zc_user->user_email ); ?></span>
				<?php if ( '' !== $zc_since ) : ?>
					<small>
						<?php
						/* translators: %s: تاریخ عضویت */
						printf( esc_html__( 'عضو از %s', 'zarincoach' ), esc_html( $zc_since ) );
						?>
					</small>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<nav class="woocommerce-MyAccount-navigation zc-ma-nav" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
		<ul>
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
				<?php
				$zc_icon  = isset( $zc_sections[ $endpoint ]['icon'] ) ? $zc_sections[ $endpoint ]['icon'] : 'sparkles';
				$zc_badge = isset( $zc_badges[ $endpoint ] ) ? $zc_badges[ $endpoint ] : 0;
				?>
				<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
						<?php zc_icon( $zc_icon, 'zc-ma-nav__ico' ); ?>
						<span class="zc-ma-nav__label"><?php echo esc_html( $label ); ?></span>
						<?php if ( $zc_badge > 0 ) : ?>
							<span class="zc-ma-nav__badge"><?php echo esc_html( number_format_i18n( $zc_badge ) ); ?></span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

	<?php
	if ( function_exists( 'zc_acc_help_box' ) ) {
		zc_acc_help_box( 'zc-ma-help--side' );
	}
	?>
</aside>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
