<?php
/**
 * ستون کناری
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! is_active_sidebar( 'zc-blog' ) ) {
	return;
}
?>

<div class="zc-sidebar">
	<?php dynamic_sidebar( 'zc-blog' ); ?>
</div>
