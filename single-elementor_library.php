<?php
/**
 * پیش‌نمایش و ویرایش قالب‌های کتابخانه‌ی المنتور (سربرگ، پاورقی، بخش‌ها)
 *
 * بوم تمیز بدون سربرگ/پاورقی/عنوان قالب تا قالب دقیقاً همان‌طور که در سایت دیده می‌شود ویرایش شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>

<div class="zc-library-canvas">
	<?php
	while ( have_posts() ) :
		the_post();
		the_content();
	endwhile;
	?>
</div>

<?php
get_footer();
