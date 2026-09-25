<?php
/**
 * پاورقی پنل (بازنویسی قالب Redux؛ شناسه‌های لازم حفظ شده‌اند).
 *
 * @package ZarinCoach
 * @version 4.5.15
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="redux-sticky-padder" style="display: none;">&nbsp;</div>
<div id="redux-footer-sticky">
	<div id="redux-footer">
		<p class="zc-footer-note"><i class="fa-regular fa-lightbulb" aria-hidden="true"></i><?php esc_html_e( 'تغییرات پس از «ذخیره» بلافاصله روی سایت اعمال و کش قالب پاک می‌شود.', 'zarincoach' ); ?></p>
		<div class="redux-action_bar">
			<span class="spinner"></span>
			<?php
			if ( false === $this->parent->args['hide_reset'] ) {
				submit_button( __( 'بازنشانی همه', 'zarincoach' ), 'secondary zc-btn-reset-all', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'redux-defaults-bottom' ) );
				submit_button( __( 'بازنشانی این بخش', 'zarincoach' ), 'secondary zc-btn-reset', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'redux-defaults-section-bottom' ) );
			}
			if ( false === $this->parent->args['hide_save'] ) {
				submit_button( __( 'ذخیره‌ی تغییرات', 'zarincoach' ), 'primary zc-btn-save', 'redux_save', false, array( 'id' => 'redux_bottom_save' ) );
			}
			?>
		</div>
	</div>
</div>
