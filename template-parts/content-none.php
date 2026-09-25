<?php
/**
 * پیام «محتوایی یافت نشد»
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="zc-panel mx-auto max-w-xl text-center">
	<span class="mx-auto grid h-16 w-16 place-items-center rounded-2xl bg-primary/10 text-primary">
		<?php zc_icon( 'search', 'h-8 w-8' ); ?>
	</span>

	<h2 class="zc-title mt-5"><?php esc_html_e( 'چیزی پیدا نشد!', 'zarincoach' ); ?></h2>

	<p class="zc-lead mt-3">
		<?php
		if ( is_search() ) {
			esc_html_e( 'متأسفانه نتیجه‌ای برای عبارت جستجو شده یافت نشد. با کلمات دیگری امتحان کنید.', 'zarincoach' );
		} else {
			esc_html_e( 'هنوز محتوایی در این بخش منتشر نشده است. به زودی مطالب جدید اضافه می‌شود.', 'zarincoach' );
		}
		?>
	</p>

	<div class="mt-6 max-w-md mx-auto">
		<?php get_search_form(); ?>
	</div>
</div>
