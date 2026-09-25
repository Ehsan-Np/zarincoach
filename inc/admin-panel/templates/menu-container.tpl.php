<?php
/**
 * ستون منوی بخش‌ها با جستجو و سرگروه‌ها (بازنویسی قالب Redux).
 *
 * @package ZarinCoach
 * @version 4.5.15
 */

defined( 'ABSPATH' ) || exit;
?>
<div class="redux-sidebar">
	<div class="zc-search">
		<i class="fa-solid fa-magnifying-glass" aria-hidden="true"></i>
		<input type="search" id="zc-panel-search" autocomplete="off" placeholder="<?php esc_attr_e( 'جستجوی تنظیمات…', 'zarincoach' ); ?>" aria-label="<?php esc_attr_e( 'جستجوی تنظیمات', 'zarincoach' ); ?>">
	</div>
	<ul class="redux-group-menu">
		<?php
		foreach ( $this->parent->sections as $redux_key => $redux_section ) {
			$redux_the_title = $redux_section['title'] ?? '';
			$redux_skip_sec  = false;
			foreach ( $this->parent->options_class->hidden_perm_sections as $redux_num => $redux_section_title ) {
				if ( $redux_section_title === $redux_the_title ) {
					$redux_skip_sec = true;
				}
			}

			if ( isset( $redux_section['customizer_only'] ) && true === $redux_section['customizer_only'] ) {
				continue;
			}

			if ( ! empty( $redux_section['zc_group'] ) ) {
				echo '<li class="zc-menu-label" aria-hidden="true">' . esc_html( $redux_section['zc_group'] ) . '</li>';
			} elseif ( isset( $redux_section['id'] ) && 'import/export' === $redux_section['id'] ) {
				echo '<li class="zc-menu-label" aria-hidden="true">' . esc_html__( 'نگهداری', 'zarincoach' ) . '</li>';
			}

			if ( false === $redux_skip_sec ) {
				echo( $this->parent->render_class->section_menu( $redux_key, $redux_section ) ); // phpcs:ignore WordPress.Security.EscapeOutput
			}
		}

		// phpcs:ignore WordPress.NamingConventions.ValidHookName
		do_action( "redux/page/{$this->parent->args['opt_name']}/menu/after", $this );
		?>
	</ul>
</div>
