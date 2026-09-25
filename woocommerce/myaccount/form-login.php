<?php
/**
 * Login Form — ورود و عضویت (دوبخشی با زبانه‌ها)
 *
 * زبانه‌ها بدون جاوااسکریپت (radio + CSS) کار می‌کنند؛ پس از خطای ثبت‌نام، زبانه‌ی عضویت باز می‌ماند.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package ZarinCoach
 * @version 9.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$zc_can_register = 'yes' === get_option( 'woocommerce_enable_myaccount_registration' );
$zc_register_tab = $zc_can_register && ! empty( $_POST['register'] ); // phpcs:ignore WordPress.Security.NonceVerification.Missing -- فقط برای انتخاب زبانه.
$zc_btn_class    = wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '';
$zc_privacy      = get_privacy_policy_url();
$zc_perks        = array(
	array( 'bag', __( 'پیگیری وضعیت سفارش‌ها در هر لحظه', 'zarincoach' ) ),
	array( 'download', __( 'دسترسی همیشگی به کارپوشه‌ها و دوره‌های صوتی', 'zarincoach' ) ),
	array( 'calendar', __( 'هماهنگی آسان جلسه‌های کوچینگ خریداری‌شده', 'zarincoach' ) ),
	array( 'zap', __( 'تسویه‌حساب سریع‌تر با نشانی‌های ذخیره‌شده', 'zarincoach' ) ),
);

do_action( 'woocommerce_before_customer_login_form' ); ?>

<div class="zc-auth<?php echo $zc_can_register ? ' has-register' : ''; ?>">

	<aside class="zc-auth__brand">
		<span class="zc-auth__badge"><?php zc_icon( 'user', 'h-4 w-4' ); ?><?php esc_html_e( 'حساب کاربری', 'zarincoach' ); ?></span>
		<h2>
			<?php
			/* translators: %s: نام سایت */
			printf( esc_html__( 'به %s خوش آمدید', 'zarincoach' ), esc_html( get_bloginfo( 'name' ) ) );
			?>
		</h2>
		<p><?php esc_html_e( 'با ورود به حساب کاربری، همه‌ی خریدها و فایل‌های خود را یک‌جا و امن در اختیار دارید.', 'zarincoach' ); ?></p>
		<ul class="zc-auth__perks">
			<?php foreach ( $zc_perks as $zc_perk ) : ?>
				<li><span><?php zc_icon( $zc_perk[0], 'h-4 w-4' ); ?></span><?php echo esc_html( $zc_perk[1] ); ?></li>
			<?php endforeach; ?>
		</ul>
		<p class="zc-auth__safe">
			<?php zc_icon( 'lock', 'h-4 w-4' ); ?>
			<?php if ( $zc_privacy ) : ?>
				<?php
				/* translators: %s: پیوند سیاست حریم خصوصی */
				printf( wp_kses( __( 'اطلاعات شما طبق <a href="%s">سیاست حریم خصوصی</a> محرمانه می‌ماند.', 'zarincoach' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( $zc_privacy ) );
				?>
			<?php else : ?>
				<?php esc_html_e( 'اطلاعات شما محرمانه می‌ماند.', 'zarincoach' ); ?>
			<?php endif; ?>
		</p>
	</aside>

	<div class="zc-auth__panel u-columns col2-set" id="customer_login">

		<?php if ( $zc_can_register ) : ?>
			<input class="zc-auth__radio" type="radio" name="zc-auth-tab" id="zc-auth-login" <?php checked( ! $zc_register_tab ); ?> />
			<input class="zc-auth__radio" type="radio" name="zc-auth-tab" id="zc-auth-register" <?php checked( $zc_register_tab ); ?> />
			<div class="zc-auth__tabs">
				<label for="zc-auth-login"><?php esc_html_e( 'Login', 'woocommerce' ); ?></label>
				<label for="zc-auth-register"><?php esc_html_e( 'Register', 'woocommerce' ); ?></label>
			</div>
		<?php endif; ?>

		<div class="zc-auth__pane zc-auth__pane--login u-column1 col-1">

			<h2><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>
			<p class="zc-auth__lead"><?php esc_html_e( 'با نام کاربری یا ایمیل و گذرواژه‌ی خود وارد شوید.', 'zarincoach' ); ?></p>

			<form class="woocommerce-form woocommerce-form-login login" method="post" novalidate>

				<?php do_action( 'woocommerce_login_form_start' ); ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="username"><?php esc_html_e( 'Username or email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="username" autocomplete="username" dir="auto" value="<?php echo ( ! empty( $_POST['username'] ) && is_string( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input class="woocommerce-Input woocommerce-Input--text input-text" type="password" name="password" id="password" autocomplete="current-password" required aria-required="true" />
				</p>

				<?php do_action( 'woocommerce_login_form' ); ?>

				<p class="form-row zc-auth__row">
					<label class="woocommerce-form__label woocommerce-form__label-for-checkbox woocommerce-form-login__rememberme">
						<input class="woocommerce-form__input woocommerce-form__input-checkbox" name="rememberme" type="checkbox" id="rememberme" value="forever" /> <span><?php esc_html_e( 'Remember me', 'woocommerce' ); ?></span>
					</label>
					<span class="woocommerce-LostPassword lost_password">
						<a href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'Lost your password?', 'woocommerce' ); ?></a>
					</span>
				</p>
				<p class="form-row">
					<?php wp_nonce_field( 'woocommerce-login', 'woocommerce-login-nonce' ); ?>
					<button type="submit" class="woocommerce-button button woocommerce-form-login__submit zc-auth__submit<?php echo esc_attr( $zc_btn_class ); ?>" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
				</p>

				<?php do_action( 'woocommerce_login_form_end' ); ?>

			</form>

			<?php if ( $zc_can_register ) : ?>
				<p class="zc-auth__switch"><?php esc_html_e( 'حساب کاربری ندارید؟', 'zarincoach' ); ?> <label for="zc-auth-register"><?php esc_html_e( 'عضو شوید', 'zarincoach' ); ?></label></p>
			<?php endif; ?>

		</div>

		<?php if ( $zc_can_register ) : ?>

		<div class="zc-auth__pane zc-auth__pane--register u-column2 col-2">

			<h2><?php esc_html_e( 'Register', 'woocommerce' ); ?></h2>
			<p class="zc-auth__lead"><?php esc_html_e( 'ساخت حساب کمتر از یک دقیقه زمان می‌برد.', 'zarincoach' ); ?></p>

			<form method="post" class="woocommerce-form woocommerce-form-register register" <?php do_action( 'woocommerce_register_form_tag' ); ?> >

				<?php do_action( 'woocommerce_register_form_start' ); ?>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_username' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_username"><?php esc_html_e( 'Username', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="text" class="woocommerce-Input woocommerce-Input--text input-text" name="username" id="reg_username" autocomplete="username" dir="auto" value="<?php echo ( ! empty( $_POST['username'] ) ) ? esc_attr( wp_unslash( $_POST['username'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
					</p>

				<?php endif; ?>

				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
					<label for="reg_email"><?php esc_html_e( 'Email address', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--text input-text" name="email" id="reg_email" autocomplete="email" dir="ltr" value="<?php echo ( ! empty( $_POST['email'] ) ) ? esc_attr( wp_unslash( $_POST['email'] ) ) : ''; ?>" required aria-required="true" /><?php // @codingStandardsIgnoreLine ?>
				</p>

				<?php if ( 'no' === get_option( 'woocommerce_registration_generate_password' ) ) : ?>

					<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
						<label for="reg_password"><?php esc_html_e( 'Password', 'woocommerce' ); ?>&nbsp;<span class="required" aria-hidden="true">*</span><span class="screen-reader-text"><?php esc_html_e( 'Required', 'woocommerce' ); ?></span></label>
						<input type="password" class="woocommerce-Input woocommerce-Input--text input-text" name="password" id="reg_password" autocomplete="new-password" required aria-required="true" />
					</p>

				<?php else : ?>

					<p class="zc-auth__hint"><?php zc_icon( 'mail', 'h-4 w-4' ); ?><?php esc_html_e( 'A link to set a new password will be sent to your email address.', 'woocommerce' ); ?></p>

				<?php endif; ?>

				<?php do_action( 'woocommerce_register_form' ); ?>

				<p class="woocommerce-form-row form-row">
					<?php wp_nonce_field( 'woocommerce-register', 'woocommerce-register-nonce' ); ?>
					<button type="submit" class="woocommerce-Button woocommerce-button button<?php echo esc_attr( $zc_btn_class ); ?> woocommerce-form-register__submit zc-auth__submit" name="register" value="<?php esc_attr_e( 'Register', 'woocommerce' ); ?>"><?php esc_html_e( 'Register', 'woocommerce' ); ?></button>
				</p>

				<?php do_action( 'woocommerce_register_form_end' ); ?>

			</form>

			<p class="zc-auth__switch"><?php esc_html_e( 'قبلاً عضو شده‌اید؟', 'zarincoach' ); ?> <label for="zc-auth-login"><?php esc_html_e( 'وارد شوید', 'zarincoach' ); ?></label></p>

		</div>

		<?php endif; ?>

	</div>
</div>

<?php do_action( 'woocommerce_after_customer_login_form' ); ?>
