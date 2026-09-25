<?php
/**
 * فرم تماس داخلی قالب (شورت‌کد [zc_contact])
 *
 * ویژگی‌ها: nonce، فیلد مخفی ضد ربات (Honeypot)، محدودیت ارسال،
 * پاک‌سازی ورودی‌ها و ارسال ایمیل با wp_mail — بدون وابستگی خارجی.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_contact_form_shortcode' ) ) :
	/**
	 * نمایش فرم تماس.
	 *
	 * @param array $atts ویژگی‌های شورت‌کد.
	 * @return string
	 */
	function zc_contact_form_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'title'       => __( 'درخواست رزرو جلسه', 'zarincoach' ),
				'subtitle'    => __( 'فرم را پر کن؛ در کمتر از ۲۴ ساعت کاری با تو تماس می‌گیرم.', 'zarincoach' ),
				'button'      => __( 'ارسال درخواست', 'zarincoach' ),
				'show_subject' => '0',
				'class'       => '',
			),
			$atts,
			'zc_contact'
		);

		if ( ! zc_switch( 'contact_form_enable', true ) ) {
			return '';
		}

		ob_start();
		?>
		<form class="zc-contact-form grid gap-4 <?php echo esc_attr( $atts['class'] ); ?>" method="post" data-zc-form novalidate>
			<?php if ( '' !== (string) $atts['title'] ) : ?>
				<div>
					<h3 class="text-[1.05rem] font-bold text-secondary"><?php echo esc_html( $atts['title'] ); ?></h3>
					<?php if ( '' !== (string) $atts['subtitle'] ) : ?>
						<p class="zc-lead mt-1 !text-[0.88rem]"><?php echo esc_html( $atts['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<div class="grid gap-4 sm:grid-cols-2">
				<div>
					<label class="zc-label" for="zc-name"><?php esc_html_e( 'نام و نام خانوادگی', 'zarincoach' ); ?> <span class="text-primary">*</span></label>
					<input class="zc-input" type="text" id="zc-name" name="zc_name" required maxlength="80" autocomplete="name" placeholder="<?php esc_attr_e( 'مثال: مریم احمدی', 'zarincoach' ); ?>">
				</div>

				<div>
					<label class="zc-label" for="zc-contact"><?php esc_html_e( 'شماره تماس یا ایمیل', 'zarincoach' ); ?> <span class="text-primary">*</span></label>
					<input class="zc-input" type="text" id="zc-contact" name="zc_contact" required maxlength="120" autocomplete="tel" placeholder="<?php esc_attr_e( 'مثال: ۰۹۱۲۳۴۵۶۷۸۹', 'zarincoach' ); ?>" dir="auto">
				</div>
			</div>

			<?php if ( '1' === (string) $atts['show_subject'] ) : ?>
				<div>
					<label class="zc-label" for="zc-subject"><?php esc_html_e( 'موضوع', 'zarincoach' ); ?></label>
					<input class="zc-input" type="text" id="zc-subject" name="zc_subject" maxlength="150">
				</div>
			<?php endif; ?>

			<div>
				<label class="zc-label" for="zc-message"><?php esc_html_e( 'توضیح کوتاه درباره درخواست', 'zarincoach' ); ?> <span class="text-primary">*</span></label>
				<textarea class="zc-textarea" id="zc-message" name="zc_message" required minlength="10" maxlength="2000" placeholder="<?php esc_attr_e( 'درباره چه موضوعی می‌خواهی صحبت کنیم؟', 'zarincoach' ); ?>"></textarea>
			</div>

			<!-- فیلد مخفی برای جلوگیری از ربات‌ها -->
			<div class="zc-hp" aria-hidden="true" style="position:absolute;width:1px;height:1px;margin:-1px;padding:0;overflow:hidden;clip:rect(0 0 0 0);clip-path:inset(50%);white-space:nowrap;border:0">
				<label for="zc-hp"><?php esc_html_e( 'این فیلد را خالی بگذارید', 'zarincoach' ); ?></label>
				<input type="text" id="zc-hp" name="zc_hp" tabindex="-1" autocomplete="off">
			</div>

			<input type="hidden" name="action" value="zc_contact">
			<input type="hidden" name="zc_nonce" value="<?php echo esc_attr( wp_create_nonce( 'zc_contact_form' ) ); ?>">

			<button type="submit" class="zc-btn zc-btn-primary zc-btn-lg w-full">
				<?php echo esc_html( $atts['button'] ); ?>
			</button>

			<div class="zc-form-msg" data-zc-form-msg role="status" aria-live="polite"></div>

			<p class="zc-form-note !mt-1">
				<?php
				printf(
					/* translators: %s: پیوند سیاست حریم خصوصی */
					esc_html__( 'اطلاعات شما محرمانه است و فقط برای پاسخ‌گویی استفاده می‌شود (%s). لطفاً جزئیات سلامت را اینجا ننویسید و برای جلسه نگه دارید.', 'zarincoach' ),
					'<a href="' . esc_url( zc_page_url_by_key( 'privacy' ) ) . '">' . esc_html__( 'حریم خصوصی', 'zarincoach' ) . '</a>'
				);
				?>
			</p>
		</form>
		<?php
		return ob_get_clean();
	}
endif;
add_shortcode( 'zc_contact', 'zc_contact_form_shortcode' );

if ( ! function_exists( 'zc_contact_form_submit' ) ) :
	/**
	 * پردازش ارسال فرم (AJAX).
	 *
	 * @return void
	 */
	function zc_contact_form_submit() {
		// بررسی nonce.
		$nonce = isset( $_POST['zc_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_nonce'] ) ) : '';
		if ( ! wp_verify_nonce( $nonce, 'zc_contact_form' ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'نشست کاری منقضی شده است؛ صفحه را تازه‌سازی کنید.', 'zarincoach' ) ), 403 );
		}

		// بررسی فیلد مخفی.
		$honeypot = isset( $_POST['zc_hp'] ) ? trim( sanitize_text_field( wp_unslash( $_POST['zc_hp'] ) ) ) : '';
		if ( '' !== $honeypot ) {
			wp_send_json_error( array( 'message' => esc_html__( 'ارسال شما شناسایی نشد.', 'zarincoach' ) ), 400 );
		}

		// محدودیت ارسال: هر IP هر ۳۰ ثانیه یک بار.
		$ip       = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';
		$rate_key = 'zc_form_' . md5( $ip );
		if ( false !== get_transient( $rate_key ) ) {
			wp_send_json_error( array( 'message' => esc_html__( 'کمی صبر کن و دوباره تلاش کن.', 'zarincoach' ) ), 429 );
		}

		// دریافت و پاک‌سازی ورودی‌ها.
		$name    = isset( $_POST['zc_name'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_name'] ) ) : '';
		$contact = isset( $_POST['zc_contact'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_contact'] ) ) : '';
		$message = isset( $_POST['zc_message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['zc_message'] ) ) : '';
		$subject = isset( $_POST['zc_subject'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_subject'] ) ) : '';

		// اعتبارسنجی.
		if ( mb_strlen( $name ) < 2 ) {
			wp_send_json_error( array( 'message' => esc_html__( 'لطفاً نام خود را وارد کنید.', 'zarincoach' ) ), 422 );
		}

		if ( mb_strlen( $contact ) < 5 ) {
			wp_send_json_error( array( 'message' => esc_html__( 'یک شماره تماس یا ایمیل معتبر وارد کنید.', 'zarincoach' ) ), 422 );
		}

		if ( mb_strlen( $message ) < 10 ) {
			wp_send_json_error( array( 'message' => esc_html__( 'لطفاً توضیح کامل‌تری بنویسید.', 'zarincoach' ) ), 422 );
		}

		// ذخیره درخواست (اختیاری و سبک) برای پیگیری در پیشخوان.
		$entry_id = wp_insert_post(
			array(
				'post_type'    => 'zc_message',
				'post_status'  => 'private',
				'post_title'   => sprintf( '[%s] %s', current_time( 'Y-m-d H:i' ), $name ),
				'post_content' => wp_json_encode(
					array(
						'name'    => $name,
						'contact' => $contact,
						'subject' => $subject,
						'message' => $message,
					),
					JSON_UNESCAPED_UNICODE
				),
			),
			true
		);

		if ( ! is_wp_error( $entry_id ) ) {
			update_post_meta( $entry_id, '_zc_message_contact', $contact );
		}

		// ارسال ایمیل.
		$to          = (string) zc_opt( 'contact_form_email', '' );
		$to          = '' !== $to ? $to : get_option( 'admin_email' );
		$mail_subject = '' !== $subject
			? sprintf( '[%s] %s', get_bloginfo( 'name' ), $subject )
			: sprintf( '[%s] %s', get_bloginfo( 'name' ), __( 'درخواست جدید از فرم سایت', 'zarincoach' ) );

		$body  = '<div dir="rtl" style="font-family:Tahoma,Arial,sans-serif;line-height:2">';
		$body .= '<h3 style="margin:0 0 12px">' . esc_html__( 'درخواست جدید ثبت شد', 'zarincoach' ) . '</h3>';
		$body .= '<p><strong>' . esc_html__( 'نام:', 'zarincoach' ) . '</strong> ' . esc_html( $name ) . '</p>';
		$body .= '<p><strong>' . esc_html__( 'راه ارتباطی:', 'zarincoach' ) . '</strong> ' . esc_html( $contact ) . '</p>';
		if ( '' !== $subject ) {
			$body .= '<p><strong>' . esc_html__( 'موضوع:', 'zarincoach' ) . '</strong> ' . esc_html( $subject ) . '</p>';
		}
		$body .= '<p><strong>' . esc_html__( 'پیام:', 'zarincoach' ) . '</strong><br>' . nl2br( esc_html( $message ) ) . '</p>';
		$body .= '<hr style="border:none;border-top:1px solid #eee;margin:16px 0">';
		$body .= '<p style="font-size:12px;color:#777">' . esc_html__( 'این ایمیل از طریق فرم سایت ارسال شده است.', 'zarincoach' ) . '</p>';
		$body .= '</div>';

		$headers = array(
			'Content-Type: text/html; charset=UTF-8',
			'From: ' . get_bloginfo( 'name' ) . ' <' . get_option( 'admin_email' ) . '>',
		);

		// اگر مراجع ایمیل وارد کرده باشد، پاسخ مستقیم به همان ایمیل ارسال می‌شود.
		if ( is_email( $contact ) ) {
			$headers[] = 'Reply-To: ' . $name . ' <' . sanitize_email( $contact ) . '>';
		}

		$sent = wp_mail( $to, $mail_subject, $body, $headers );

		set_transient( $rate_key, time(), 30 );

		$stored = ! is_wp_error( $entry_id ) && $entry_id > 0;

		if ( $stored ) {
			update_post_meta( $entry_id, '_zc_message_mailed', $sent ? '1' : '0' );
		}

		// پیام در پیشخوان ذخیره شده است؛ حتی اگر ایمیل ارسال نشود، درخواست از دست نمی‌رود.
		if ( ! $sent && ! $stored ) {
			wp_send_json_error( array( 'message' => esc_html__( 'ارسال پیام با خطا مواجه شد؛ لطفاً تماس بگیرید.', 'zarincoach' ) ), 500 );
		}

		wp_send_json_success( array( 'message' => esc_html__( 'پیام شما با موفقیت ارسال شد. به‌زودی با شما تماس می‌گیرم.', 'zarincoach' ) ) );
	}
endif;
add_action( 'wp_ajax_zc_contact', 'zc_contact_form_submit' );
add_action( 'wp_ajax_nopriv_zc_contact', 'zc_contact_form_submit' );

if ( ! function_exists( 'zc_register_message_cpt' ) ) :
	/**
	 * نوع نوشته داخلی برای ذخیره پیام‌ها (خصوصی).
	 *
	 * @return void
	 */
	function zc_register_message_cpt() {
		register_post_type(
			'zc_message',
			array(
				'labels'       => array(
					'name'          => __( 'پیام‌های فرم', 'zarincoach' ),
					'singular_name' => __( 'پیام', 'zarincoach' ),
				),
				'public'       => false,
				'show_ui'      => true,
				'show_in_menu' => 'zc-options',
				'supports'     => array( 'title', 'editor' ),
				'capability_type' => 'post',
				'capabilities' => array(
					'create_posts' => 'do_not_allow',
				),
				'map_meta_cap' => true,
			)
		);
	}
endif;
add_action( 'init', 'zc_register_message_cpt', 6 );
