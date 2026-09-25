<?php
/**
 * Downloads — کارت‌های فایل‌های قابل دانلود مشتری
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 7.8.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$downloads     = WC()->customer->get_downloadable_products();
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>

<?php if ( $has_downloads ) : ?>

	<?php do_action( 'woocommerce_before_available_downloads' ); ?>

	<ul class="zc-ma-dl" role="list">
		<?php foreach ( $downloads as $zc_dl ) : ?>
			<?php
			$zc_file = isset( $zc_dl['file']['file'] ) ? (string) $zc_dl['file']['file'] : '';
			$zc_ext  = strtoupper( (string) pathinfo( (string) wp_parse_url( $zc_file, PHP_URL_PATH ), PATHINFO_EXTENSION ) );
			$zc_ext  = '' !== $zc_ext && strlen( $zc_ext ) <= 4 ? $zc_ext : __( 'فایل', 'zarincoach' );
			$zc_icon = in_array( $zc_ext, array( 'MP3', 'M4A', 'WAV', 'OGG', 'AAC' ), true ) ? 'headphones' : ( in_array( $zc_ext, array( 'MP4', 'MOV', 'WEBM' ), true ) ? 'video' : 'file-text' );
			$zc_left = '' === (string) $zc_dl['downloads_remaining'] ? __( 'نامحدود', 'zarincoach' ) : number_format_i18n( (int) $zc_dl['downloads_remaining'] );
			$zc_exp  = __( 'بدون انقضا', 'zarincoach' );
			if ( ! empty( $zc_dl['access_expires'] ) ) {
				$zc_exp = $zc_dl['access_expires'] instanceof WC_DateTime ? wc_format_datetime( $zc_dl['access_expires'] ) : wc_format_datetime( wc_string_to_datetime( (string) $zc_dl['access_expires'] ) );
			}
			?>
			<li class="zc-ma-file">
				<span class="zc-ma-file__ico"><?php zc_icon( $zc_icon, 'h-6 w-6' ); ?><em><?php echo esc_html( $zc_ext ); ?></em></span>
				<div class="zc-ma-file__main">
					<?php if ( ! empty( $zc_dl['product_url'] ) ) : ?>
						<a class="zc-ma-file__product" href="<?php echo esc_url( $zc_dl['product_url'] ); ?>"><?php echo esc_html( $zc_dl['product_name'] ); ?></a>
					<?php else : ?>
						<strong class="zc-ma-file__product"><?php echo esc_html( $zc_dl['product_name'] ); ?></strong>
					<?php endif; ?>
					<span class="zc-ma-file__name"><?php echo esc_html( $zc_dl['download_name'] ); ?></span>
					<span class="zc-ma-file__meta">
						<span><?php zc_icon( 'refresh', 'h-3.5 w-3.5' ); ?><?php esc_html_e( 'دفعات باقی‌مانده:', 'zarincoach' ); ?> <b><?php echo esc_html( $zc_left ); ?></b></span>
						<span><?php zc_icon( 'clock', 'h-3.5 w-3.5' ); ?><?php esc_html_e( 'اعتبار:', 'zarincoach' ); ?> <b><?php echo esc_html( $zc_exp ); ?></b></span>
						<?php if ( ! empty( $zc_dl['order_id'] ) ) : ?>
							<?php $zc_o = wc_get_order( (int) $zc_dl['order_id'] ); ?>
							<?php if ( $zc_o ) : ?>
								<a href="<?php echo esc_url( $zc_o->get_view_order_url() ); ?>">
									<?php
									/* translators: %s: شماره سفارش */
									printf( esc_html__( 'سفارش #%s', 'zarincoach' ), esc_html( $zc_o->get_order_number() ) );
									?>
								</a>
							<?php endif; ?>
						<?php endif; ?>
					</span>
				</div>
				<a class="zc-btn zc-btn-primary zc-btn-sm zc-ma-file__btn" href="<?php echo esc_url( $zc_dl['download_url'] ); ?>"><?php zc_icon( 'download', 'h-4 w-4' ); ?><?php esc_html_e( 'دانلود', 'zarincoach' ); ?></a>
			</li>
		<?php endforeach; ?>
	</ul>

	<?php
	// جدول پیش‌فرض ووکامرس با کارت‌های بالا جایگزین شده؛ قلاب برای افزونه‌های دیگر حفظ می‌شود.
	$zc_had_table = has_action( 'woocommerce_available_downloads', 'woocommerce_order_downloads_table' );
	if ( false !== $zc_had_table ) {
		remove_action( 'woocommerce_available_downloads', 'woocommerce_order_downloads_table', $zc_had_table );
	}
	do_action( 'woocommerce_available_downloads', $downloads );
	if ( false !== $zc_had_table ) {
		add_action( 'woocommerce_available_downloads', 'woocommerce_order_downloads_table', $zc_had_table );
	}
	?>

	<?php do_action( 'woocommerce_after_available_downloads' ); ?>

<?php else : ?>

	<?php zc_acc_empty( 'download', __( 'هنوز فایلی برای دانلود ندارید', 'zarincoach' ), __( 'پس از پرداخت کارپوشه‌ها و دوره‌های صوتی، فایل‌ها بی‌درنگ این‌جا در دسترس خواهند بود.', 'zarincoach' ), __( 'Browse products', 'woocommerce' ), zc_acc_shop_url() ); ?>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>
