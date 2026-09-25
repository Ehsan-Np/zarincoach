<?php
/**
 * صفحه ۴۰۴
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<section class="zc-grain relative zc-min-h-screen-70 overflow-hidden py-14 lg:py-20 <?php echo 'light' === (string) zc_opt( 'palette_page_header_tone', 'inverse' ) ? '' : 'zc-tone-inverse'; ?>">
	<div class="pointer-events-none absolute top-1/4 start-1/4 -z-10 h-80 w-80 animate-zc-blob rounded-full bg-primary/20 blur-3xl"></div>
	<div class="pointer-events-none absolute bottom-1/4 end-1/4 -z-10 h-80 w-80 animate-zc-blob rounded-full bg-info/20 blur-3xl" style="animation-delay:-8s"></div>

	<div class="zc-container relative">
		<div class="mx-auto max-w-2xl text-center">
			<span class="zc-outline-num block text-[7rem] leading-none sm:text-[9rem]">۴۰۴</span>

			<h1 class="zc-title-lg mt-4"><?php echo esc_html( (string) zc_opt( 'p404_title', __( 'این مسیر پیدا نشد', 'zarincoach' ) ) ); ?></h1>

			<?php $zc_404_text = trim( (string) zc_opt( 'p404_text', '' ) ); ?>
			<?php if ( '' !== $zc_404_text ) : ?>
				<p class="zc-lead mt-4"><?php echo esc_html( $zc_404_text ); ?></p>
			<?php endif; ?>

			<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
				<a class="zc-btn zc-btn-primary zc-btn-lg" href="<?php echo esc_url( home_url( '/' ) ); ?>">
					<?php echo esc_html( (string) zc_opt( 'p404_button', __( 'بازگشت به صفحه اصلی', 'zarincoach' ) ) ); ?>
					<?php zc_icon( 'arrow-left', 'h-4 w-4 zc-btn-arrow' ); ?>
				</a>
				<a class="zc-btn zc-btn-outline zc-btn-lg" href="<?php echo esc_url( zc_blog_url() ); ?>">
					<?php esc_html_e( 'مطالعه نوشته‌ها', 'zarincoach' ); ?>
				</a>
			</div>

			<div class="mx-auto mt-10 max-w-md">
				<?php get_search_form(); ?>
			</div>
		</div>
	</div>
</section>

<?php
get_footer();
