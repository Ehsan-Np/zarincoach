<?php
/**
 * سربرگ سایت
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

?>
<!doctype html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<link rel="profile" href="https://gmpg.org/xfn/11">
<?php wp_head(); ?>
</head>

<body <?php body_class( 'zc-body bg-base text-ink antialiased' ); ?>>
<?php wp_body_open(); ?>

<?php if ( zc_switch( 'general_preloader', false ) && ! is_singular( 'elementor_library' ) && ! ( isset( $_GET['elementor-preview'] ) ) ) : // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div class="zc-preloader" id="zc-preloader" aria-hidden="true"><span class="zc-preloader-ring"></span></div>
	<noscript><style>#zc-preloader{display:none}</style></noscript>
	<script>(function(){var p=document.getElementById('zc-preloader');function d(){if(p){p.classList.add('is-done');setTimeout(function(){p.remove();},600);}}window.addEventListener('load',d);setTimeout(d,3500);})();</script>
<?php endif; ?>

<a class="skip-link screen-reader-text" href="#zc-main"><?php esc_html_e( 'پرش به محتوای اصلی', 'zarincoach' ); ?></a>

<?php zc_render_site_header(); ?>

<main id="zc-main" class="zc-main">
