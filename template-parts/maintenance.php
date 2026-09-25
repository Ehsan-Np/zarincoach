<?php
/**
 * صفحه‌ی مستقل «حالت تعمیر و نگهداری» (کد ۵۰۳).
 * سبک و بی‌نیاز از CSS/JS قالب؛ فقط فونت آراد بارگذاری می‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

$zc_p     = function_exists( 'zc_get_palette' ) ? zc_get_palette() : array();
$zc_l     = isset( $zc_p['light'] ) ? $zc_p['light'] : array();
$zc_d     = isset( $zc_p['dark'] ) ? $zc_p['dark'] : array();
$zc_col   = static function ( $set, $key, $fallback ) {
	return isset( $set[ $key ] ) ? $set[ $key ] : $fallback;
};
$zc_title = (string) zc_opt( 'maint_title', 'به‌زودی برمی‌گردیم' );
$zc_text  = trim( (string) zc_opt( 'maint_text', '' ) );
$zc_until = trim( (string) zc_opt( 'maint_until', '' ) );
$zc_logo  = function_exists( 'zc_login_brand_logo' ) ? zc_login_brand_logo() : array( 'url' => '' );
$zc_name  = get_bloginfo( 'name' );
$zc_links = array();

if ( zc_switch( 'maint_contact', true ) ) {
	$zc_c = zc_contact_fields();
	foreach ( array( 'phone' => 'contact_phone_label', 'phone2' => 'contact_mobile_label' ) as $zc_k => $zc_label_key ) {
		if ( '' !== $zc_c[ $zc_k ] ) {
			$zc_links[] = array(
				'url'   => 'tel:' . zc_normalize_phone( $zc_c[ $zc_k ] ),
				'label' => (string) zc_opt( $zc_label_key, '' ),
				'sub'   => zc_digits_to_persian( $zc_c[ $zc_k ] ),
				'icon'  => 'phone',
				'ext'   => false,
			);
		}
	}
	foreach ( zc_contact_channels( array( 'telegram', 'bale', 'whatsapp', 'instagram' ) ) as $zc_ch ) {
		if ( ! empty( $zc_ch['url'] ) ) {
			$zc_links[] = array(
				'url'   => $zc_ch['url'],
				'label' => $zc_ch['label'],
				'sub'   => isset( $zc_ch['sub'] ) ? $zc_ch['sub'] : '',
				'icon'  => $zc_ch['icon'],
				'ext'   => true,
			);
		}
	}
}
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<meta name="robots" content="noindex, nofollow">
<title><?php echo esc_html( $zc_title . ' | ' . $zc_name ); ?></title>
<link rel="preload" href="<?php echo esc_url( ZC_URI . '/assets/fonts/AradFD-VF.woff2' ); ?>" as="font" type="font/woff2" crossorigin>
<?php if ( has_site_icon() ) : ?>
<link rel="icon" href="<?php echo esc_url( get_site_icon_url( 32 ) ); ?>" sizes="32x32">
<?php endif; ?>
<style>
@font-face{font-family:"Arad";src:url("<?php echo esc_url( ZC_URI . '/assets/fonts/AradFD-VF.woff2' ); ?>") format("woff2");font-weight:100 900;font-display:swap}
:root{--pri:<?php echo esc_html( $zc_col( $zc_l, 'primary', '#1D3A72' ) ); ?>;--sec:<?php echo esc_html( $zc_col( $zc_l, 'secondary', '#0B1B3A' ) ); ?>;--acc:<?php echo esc_html( $zc_col( $zc_l, 'accent', '#C9A45C' ) ); ?>;--base:<?php echo esc_html( $zc_col( $zc_l, 'base', '#F5F7FB' ) ); ?>;--card:<?php echo esc_html( $zc_col( $zc_l, 'surface', '#FFFFFF' ) ); ?>;--ink:<?php echo esc_html( $zc_col( $zc_l, 'ink', '#0E1A33' ) ); ?>;--muted:<?php echo esc_html( $zc_col( $zc_l, 'muted', '#56627A' ) ); ?>;--line:<?php echo esc_html( $zc_col( $zc_l, 'line', '#DCE3EF' ) ); ?>}
@media (prefers-color-scheme:dark){:root{--pri:<?php echo esc_html( $zc_col( $zc_d, 'primary', '#D6B56E' ) ); ?>;--sec:<?php echo esc_html( $zc_col( $zc_d, 'secondary', '#F2F5FB' ) ); ?>;--base:<?php echo esc_html( $zc_col( $zc_d, 'base', '#081530' ) ); ?>;--card:<?php echo esc_html( $zc_col( $zc_d, 'surface', '#0E1E40' ) ); ?>;--ink:<?php echo esc_html( $zc_col( $zc_d, 'ink', '#DCE4F2' ) ); ?>;--muted:<?php echo esc_html( $zc_col( $zc_d, 'muted', '#9AABC9' ) ); ?>;--line:<?php echo esc_html( $zc_col( $zc_d, 'line', '#20355F' ) ); ?>}}
*{box-sizing:border-box}
html,body{margin:0;min-height:100%}
body{display:grid;place-items:center;min-height:100vh;padding:24px 16px;font-family:"Arad",Tahoma,sans-serif;color:var(--ink);background:var(--base);background-image:radial-gradient(55% 45% at 100% 0%,color-mix(in srgb,var(--acc) 22%,transparent),transparent 70%),radial-gradient(45% 45% at 0% 100%,color-mix(in srgb,var(--pri) 16%,transparent),transparent 70%);line-height:1.9;-webkit-font-smoothing:antialiased}
.m{width:100%;max-width:560px;padding:36px 28px 28px;text-align:center;background:var(--card);border:1px solid var(--line);border-radius:24px;box-shadow:0 30px 60px -40px rgba(11,27,58,.55)}
.m-logo{display:block;max-width:180px;max-height:72px;width:auto;height:auto;margin:0 auto 18px}
.m-name{margin:0 0 14px;font-size:20px;font-weight:800;color:var(--sec)}
.m-chip{display:inline-flex;align-items:center;gap:8px;padding:4px 14px;border-radius:99px;font-size:12.5px;font-weight:700;color:var(--pri);background:color-mix(in srgb,var(--pri) 10%,transparent)}
.m-chip i{width:8px;height:8px;border-radius:50%;background:var(--acc);animation:p 1.6s ease-in-out infinite}
@keyframes p{50%{opacity:.35}}
h1{margin:14px 0 8px;font-size:clamp(22px,4.6vw,28px);line-height:1.5;font-weight:900;color:var(--sec)}
.m-text{margin:0 auto;max-width:44ch;color:var(--muted);font-size:15px}
.m-until{display:inline-block;margin-top:14px;padding:6px 14px;border-radius:12px;font-size:13.5px;font-weight:700;border:1px dashed var(--line)}
.m-links{display:grid;grid-template-columns:repeat(auto-fit,minmax(180px,1fr));gap:10px;margin:24px 0 0;padding:0;list-style:none;text-align:start}
.m-links a{display:flex;align-items:center;gap:10px;padding:10px 12px;border:1px solid var(--line);border-radius:14px;color:var(--ink);text-decoration:none;transition:border-color .2s,transform .2s}
.m-links a:hover,.m-links a:focus-visible{border-color:var(--pri);transform:translateY(-1px)}
.m-links svg{flex:none;width:34px;height:34px;padding:8px;border-radius:10px;color:var(--pri);background:color-mix(in srgb,var(--pri) 10%,transparent)}
.m-links b{display:block;font-size:13px;line-height:1.6}
.m-links small{display:block;color:var(--muted);font-size:12px;direction:ltr;text-align:right;unicode-bidi:plaintext}
.m-foot{margin:22px 0 0;font-size:12px;color:var(--muted)}
@media (prefers-reduced-motion:reduce){*{animation:none!important;transition:none!important}}
</style>
</head>
<body>
<main class="m">
	<?php if ( ! empty( $zc_logo['url'] ) ) : ?>
		<img class="m-logo" src="<?php echo esc_url( $zc_logo['url'] ); ?>" alt="<?php echo esc_attr( $zc_name ); ?>">
	<?php else : ?>
		<p class="m-name"><?php echo esc_html( $zc_name ); ?></p>
	<?php endif; ?>
	<span class="m-chip"><i aria-hidden="true"></i><?php esc_html_e( 'در حال به‌روزرسانی', 'zarincoach' ); ?></span>
	<h1><?php echo esc_html( $zc_title ); ?></h1>
	<?php if ( '' !== $zc_text ) : ?>
		<p class="m-text"><?php echo esc_html( $zc_text ); ?></p>
	<?php endif; ?>
	<?php if ( '' !== $zc_until ) : ?>
		<span class="m-until"><?php echo esc_html( $zc_until ); ?></span>
	<?php endif; ?>
	<?php if ( $zc_links ) : ?>
		<ul class="m-links">
			<?php foreach ( $zc_links as $zc_link ) : ?>
				<li><a href="<?php echo esc_url( $zc_link['url'] ); ?>"<?php echo $zc_link['ext'] ? ' target="_blank" rel="noopener"' : ''; ?>>
					<?php echo zc_icon( $zc_link['icon'], '', false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<span><b><?php echo esc_html( $zc_link['label'] ); ?></b><?php if ( '' !== $zc_link['sub'] ) : ?><small><?php echo esc_html( $zc_link['sub'] ); ?></small><?php endif; ?></span>
				</a></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
	<p class="m-foot"><?php echo esc_html( (string) zc_opt( 'legal_domain', 'Maryam-Jamali.ir' ) ); ?></p>
</main>
</body>
</html>
