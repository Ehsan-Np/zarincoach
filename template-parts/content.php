<?php
/**
 * محتوای پیش‌فرض در حلقه
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'zc-card zc-card-hover zc-reveal p-6' ); ?>>
	<h2 class="text-[1.15rem] font-bold text-secondary">
		<a class="transition-colors hover:text-primary" href="<?php echo esc_url( get_permalink() ); ?>"><?php echo esc_html( get_the_title() ); ?></a>
	</h2>
	<p class="zc-lead mt-2 !text-[0.92rem]"><?php echo esc_html( zc_excerpt( get_the_excerpt() ?: get_post_field( 'post_content' ), 22 ) ); ?></p>
	<a class="mt-4 inline-flex items-center gap-1.5 text-[0.85rem] font-bold text-primary transition-all hover:gap-2.5" href="<?php echo esc_url( get_permalink() ); ?>">
		<?php esc_html_e( 'ادامه مطلب', 'zarincoach' ); ?>
		<?php zc_icon( 'arrow-left', 'h-4 w-4' ); ?>
	</a>
</article>
