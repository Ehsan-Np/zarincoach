<?php
/**
 * نوار چسبان ذخیره (بازنویسی قالب Redux؛ شناسه‌های لازم برای جاوااسکریپت Redux حفظ شده‌اند).
 *
 * @package ZarinCoach
 * @version 4.5.15
 */

defined( 'ABSPATH' ) || exit;
?>
<div id="redux-sticky">
	<div id="info_bar">
		<div class="zc-bar-title">
			<span class="zc-bar-crumb"><?php esc_html_e( 'تنظیمات قالب', 'zarincoach' ); ?></span>
			<strong class="zc-bar-current" id="zc-current-section"></strong>
		</div>
		<a href="javascript:void(0);" class="expand_options" style="display:none;"><?php esc_html_e( 'نمایش همه', 'zarincoach' ); ?></a>
		<div class="redux-action_bar">
			<span class="spinner"></span>
			<?php
			if ( false === $this->parent->args['hide_reset'] ) {
				submit_button( __( 'بازنشانی همه', 'zarincoach' ), 'secondary zc-btn-reset-all', $this->parent->args['opt_name'] . '[defaults]', false, array( 'id' => 'redux-defaults-top' ) );
				submit_button( __( 'بازنشانی این بخش', 'zarincoach' ), 'secondary zc-btn-reset', $this->parent->args['opt_name'] . '[defaults-section]', false, array( 'id' => 'redux-defaults-section-top' ) );
			}
			if ( false === $this->parent->args['hide_save'] ) {
				submit_button( __( 'ذخیره‌ی تغییرات', 'zarincoach' ), 'primary zc-btn-save', 'redux_save', false, array( 'id' => 'redux_top_save' ) );
			}
			?>
		</div>
	</div>

	<div id="redux_notification_bar">
		<?php $this->notification_bar(); ?>
	</div>
</div>
