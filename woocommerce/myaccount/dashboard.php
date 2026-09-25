<?php
/**
 * My Account Dashboard — پیشخوان مشتری زرین‌کوچ
 *
 * خوشامد، آمار، آخرین سفارش‌ها، دسترسی سریع و یادآور جلسه‌های کوچینگ.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$zc_stats    = zc_acc_stats( $current_user->ID );
$zc_today    = function_exists( 'zc_jalali_date' ) ? zc_jalali_date( current_time( 'timestamp', true ) ) : ''; // phpcs:ignore WordPress.DateTime.CurrentTimeTimestamp.RequestedUTC
$zc_today    = '' !== $zc_today ? $zc_today : date_i18n( get_option( 'date_format' ) );
$zc_sections = zc_acc_sections();
$zc_items    = wc_get_account_menu_items();

$zc_cards = array(
	array(
		'icon'  => 'bag',
		'value' => number_format_i18n( $zc_stats['orders'] ),
		'label' => __( 'کل سفارش‌ها', 'zarincoach' ),
		'url'   => wc_get_account_endpoint_url( 'orders' ),
		'tone'  => 'primary',
	),
	array(
		'icon'  => 'clock',
		'value' => number_format_i18n( $zc_stats['active'] ),
		'label' => __( 'سفارش‌های در جریان', 'zarincoach' ),
		'url'   => wc_get_account_endpoint_url( 'orders' ),
		'tone'  => 'warn',
	),
	array(
		'icon'  => 'download',
		'value' => number_format_i18n( $zc_stats['downloads'] ),
		'label' => __( 'فایل‌های قابل دانلود', 'zarincoach' ),
		'url'   => isset( $zc_items['downloads'] ) ? wc_get_account_endpoint_url( 'downloads' ) : '',
		'tone'  => 'ok',
	),
	array(
		'icon'  => 'wallet',
		'value' => wc_price( $zc_stats['spent'] ),
		'html'  => true,
		'label' => __( 'مجموع خریدهای پرداخت‌شده', 'zarincoach' ),
		'url'   => '',
		'tone'  => 'accent',
	),
);
?>

<section class="zc-ma-hero">
	<div class="zc-ma-hero__text">
		<span class="zc-ma-hero__date"><?php zc_icon( 'calendar', 'h-4 w-4' ); ?><?php echo esc_html( $zc_today ); ?></span>
		<h2>
			<?php
			/* translators: %s: نام کاربر */
			printf( esc_html__( 'سلام %s، خوش آمدید', 'zarincoach' ), esc_html( zc_acc_first_name( $current_user ) ) );
			?>
		</h2>
		<p><?php esc_html_e( 'سفارش‌ها، فایل‌های دانلودی، نشانی‌ها و اطلاعات حساب خود را از همین‌جا مدیریت کنید.', 'zarincoach' ); ?></p>
		<p class="zc-ma-hero__not">
			<?php
			/* translators: 1: نام نمایشی 2: پیوند خروج */
			printf( wp_kses( __( '%1$s نیستید؟ <a href="%2$s">خروج از حساب</a>', 'zarincoach' ), array( 'a' => array( 'href' => array() ) ) ), esc_html( $current_user->display_name ), esc_url( wc_logout_url() ) );
			?>
		</p>
	</div>
	<div class="zc-ma-hero__cta">
		<a class="zc-btn zc-btn-primary zc-btn-sm" href="<?php echo esc_url( zc_acc_shop_url() ); ?>"><?php zc_icon( 'bag', 'h-4 w-4' ); ?><?php esc_html_e( 'فروشگاه', 'zarincoach' ); ?></a>
		<a class="zc-btn zc-ma-hero__ghost zc-btn-sm" href="<?php echo esc_url( zc_acc_booking_url() ); ?>"><?php zc_icon( 'calendar', 'h-4 w-4' ); ?><?php esc_html_e( 'رزرو نوبت', 'zarincoach' ); ?></a>
	</div>
</section>

<div class="zc-ma-stats">
	<?php foreach ( $zc_cards as $zc_card ) : ?>
		<?php $zc_tag = '' !== $zc_card['url'] ? 'a' : 'div'; ?>
		<<?php echo esc_attr( $zc_tag ); ?> class="zc-ma-stat is-<?php echo esc_attr( $zc_card['tone'] ); ?>"<?php echo 'a' === $zc_tag ? ' href="' . esc_url( $zc_card['url'] ) . '"' : ''; ?>>
			<span class="zc-ma-stat__ico"><?php zc_icon( $zc_card['icon'], 'h-5 w-5' ); ?></span>
			<span class="zc-ma-stat__value"><?php echo empty( $zc_card['html'] ) ? esc_html( $zc_card['value'] ) : wp_kses_post( $zc_card['value'] ); ?></span>
			<span class="zc-ma-stat__label"><?php echo esc_html( $zc_card['label'] ); ?></span>
		</<?php echo esc_attr( $zc_tag ); ?>>
	<?php endforeach; ?>
</div>

<?php if ( $zc_stats['sessions'] > 0 ) : ?>
	<?php $zc_note = trim( (string) zc_opt( 'sp_delivery_session', '' ) ); ?>
	<div class="zc-ma-note">
		<span class="zc-ma-note__ico"><?php zc_icon( 'video', 'h-5 w-5' ); ?></span>
		<div>
			<strong>
				<?php
				/* translators: %s: تعداد جلسه */
				printf( esc_html__( '%s جلسه‌ی کوچینگ در سفارش‌های شما ثبت شده است', 'zarincoach' ), esc_html( number_format_i18n( $zc_stats['sessions'] ) ) );
				?>
			</strong>
			<p><?php echo esc_html( '' !== $zc_note ? $zc_note : __( 'برای هماهنگی زمان جلسه‌ها از برگه‌ی رزرو نوبت اقدام کنید یا با شماره‌ی نوبت‌دهی تماس بگیرید.', 'zarincoach' ) ); ?></p>
		</div>
		<a class="zc-btn zc-btn-secondary zc-btn-sm" href="<?php echo esc_url( zc_acc_booking_url() ); ?>"><?php esc_html_e( 'هماهنگی جلسه', 'zarincoach' ); ?></a>
	</div>
<?php endif; ?>

<div class="zc-ma-grid">
	<section class="zc-ma-card zc-ma-card--recent" aria-labelledby="zc-ma-recent">
		<header class="zc-ma-card__head">
			<h3 id="zc-ma-recent"><?php esc_html_e( 'آخرین سفارش‌ها', 'zarincoach' ); ?></h3>
			<?php if ( $zc_stats['orders'] > 0 ) : ?>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'همه‌ی سفارش‌ها', 'zarincoach' ); ?><?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?></a>
			<?php endif; ?>
		</header>
		<?php if ( ! empty( $zc_stats['recent'] ) ) : ?>
			<ul class="zc-ma-mini">
				<?php foreach ( $zc_stats['recent'] as $zc_id ) : ?>
					<?php
					$zc_order = wc_get_order( $zc_id );
					if ( ! $zc_order ) {
						continue;
					}
					?>
					<li>
						<a class="zc-ma-mini__row" href="<?php echo esc_url( $zc_order->get_view_order_url() ); ?>">
							<?php echo zc_acc_order_thumbs( $zc_order, 2 ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span class="zc-ma-mini__main">
								<strong>
									<?php
									/* translators: %s: شماره سفارش */
									printf( esc_html__( 'سفارش #%s', 'zarincoach' ), esc_html( $zc_order->get_order_number() ) );
									?>
								</strong>
								<small><?php echo esc_html( zc_acc_order_summary( $zc_order ) ); ?></small>
							</span>
							<span class="zc-ma-mini__side">
								<?php echo zc_acc_status_pill( $zc_order ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<span class="zc-ma-mini__total"><?php echo wp_kses_post( $zc_order->get_formatted_order_total() ); ?></span>
							</span>
						</a>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php else : ?>
			<?php zc_acc_empty( 'bag', __( 'هنوز سفارشی ثبت نکرده‌اید', 'zarincoach' ), __( 'کارپوشه‌ها، دوره‌های صوتی و بسته‌های جلسه را در فروشگاه ببینید.', 'zarincoach' ), __( 'مشاهده‌ی فروشگاه', 'zarincoach' ), zc_acc_shop_url() ); ?>
		<?php endif; ?>
	</section>

	<section class="zc-ma-card zc-ma-card--quick" aria-labelledby="zc-ma-quick">
		<header class="zc-ma-card__head">
			<h3 id="zc-ma-quick"><?php esc_html_e( 'دسترسی سریع', 'zarincoach' ); ?></h3>
		</header>
		<div class="zc-ma-quick">
			<?php foreach ( $zc_items as $zc_ep => $zc_label ) : ?>
				<?php
				if ( in_array( $zc_ep, array( 'dashboard', 'customer-logout' ), true ) ) {
					continue;
				}
				$zc_s = isset( $zc_sections[ $zc_ep ] ) ? $zc_sections[ $zc_ep ] : array(
					'icon' => 'sparkles',
					'desc' => '',
				);
				?>
				<a class="zc-ma-quick__item" href="<?php echo esc_url( wc_get_account_endpoint_url( $zc_ep ) ); ?>">
					<span class="zc-ma-quick__ico"><?php zc_icon( $zc_s['icon'], 'h-5 w-5' ); ?></span>
					<span class="zc-ma-quick__text"><strong><?php echo esc_html( $zc_label ); ?></strong><?php if ( '' !== $zc_s['desc'] ) : ?><small><?php echo esc_html( $zc_s['desc'] ); ?></small><?php endif; ?></span>
					<?php zc_icon( 'chevron-left', 'zc-ma-quick__arrow' ); ?>
				</a>
			<?php endforeach; ?>
		</div>
	</section>
</div>

<?php zc_acc_help_box( 'zc-ma-help--wide' ); ?>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

	/**
	 * Deprecated woocommerce_before_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_before_my_account' );

	/**
	 * Deprecated woocommerce_after_my_account action.
	 *
	 * @deprecated 2.6.0
	 */
	do_action( 'woocommerce_after_my_account' );

/* Omit closing PHP tag at the end of PHP files to avoid "headers already sent" issues. */
