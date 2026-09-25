<?php
/**
 * فرم جستجو
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;
?>

<form role="search" method="get" class="zc-search-form flex items-center gap-2" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label class="screen-reader-text" for="zc-search-field"><?php esc_html_e( 'جستجو برای:', 'zarincoach' ); ?></label>
	<input
		type="search"
		id="zc-search-field"
		class="zc-input"
		placeholder="<?php esc_attr_e( 'جستجو در مطالب…', 'zarincoach' ); ?>"
		value="<?php echo esc_attr( get_search_query() ); ?>"
		name="s"
	>
	<button type="submit" class="zc-btn zc-btn-primary zc-btn-sm shrink-0">
		<?php zc_icon( 'search', 'h-4 w-4' ); ?>
		<span><?php esc_html_e( 'جستجو', 'zarincoach' ); ?></span>
	</button>
</form>
