<?php
/**
 * بخش دیدگاه‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="zc-comments mt-10 lg:mt-12">

	<?php if ( have_comments() ) : ?>
		<h2 class="zc-title flex items-center gap-2">
			<?php
			$comments_number = (int) get_comments_number();
			/* translators: %d: تعداد دیدگاه‌ها */
			printf( esc_html__( 'دیدگاه‌ها (%d)', 'zarincoach' ), $comments_number );
			?>
		</h2>

		<ol class="zc-comment-list">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 56,
					'callback'   => 'zc_comment_markup',
				)
			);
			?>
		</ol>

		<?php
		the_comments_pagination(
			array(
				'prev_text' => zc_icon( 'arrow-right', 'h-4 w-4', false ),
				'next_text' => zc_icon( 'arrow-left', 'h-4 w-4', false ),
			)
		);
		?>

		<?php if ( ! comments_open() ) : ?>
			<p class="zc-lead mt-6"><?php esc_html_e( 'امکان ثبت دیدگاه در این نوشته بسته شده است.', 'zarincoach' ); ?></p>
		<?php endif; ?>
	<?php endif; ?>

	<?php
	$commenter   = wp_get_current_commenter();
	$req         = get_option( 'require_name_email' );
	$zc_c_title  = trim( (string) zc_opt( 'comments_form_title', '' ) );
	$zc_c_note   = trim( (string) zc_opt( 'comments_note', '' ) );
	$zc_c_fields = array(
		'author' => '<p class="comment-form-author mb-4"><label class="zc-label" for="author">' . esc_html__( 'نام', 'zarincoach' ) . ( $req ? ' <span class="required text-primary">*</span>' : '' ) . '</label><input id="author" class="zc-input" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) . '" size="30" autocomplete="name"' . ( $req ? ' required' : '' ) . '></p>',
		'email'  => '<p class="comment-form-email mb-4"><label class="zc-label" for="email">' . esc_html__( 'ایمیل', 'zarincoach' ) . ( $req ? ' <span class="required text-primary">*</span>' : '' ) . '</label><input id="email" class="zc-input" name="email" type="email" dir="ltr" value="' . esc_attr( $commenter['comment_author_email'] ) . '" size="30" autocomplete="email"' . ( $req ? ' required' : '' ) . '></p>',
	);
	if ( zc_switch( 'comments_url_field', false ) ) {
		$zc_c_fields['url'] = '<p class="comment-form-url mb-4"><label class="zc-label" for="url">' . esc_html__( 'وب‌سایت', 'zarincoach' ) . '</label><input id="url" class="zc-input" name="url" type="url" dir="ltr" value="' . esc_attr( $commenter['comment_author_url'] ) . '" size="30" autocomplete="url"></p>';
	}

	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="zc-title mt-8 !text-[1.3rem]">',
			'title_reply_after'  => '</h3>',
			'class_submit'       => 'zc-btn zc-btn-primary submit',
			'submit_button'      => '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
			'comment_field'      => '<p class="comment-form-comment mb-4"><label class="zc-label" for="comment">' . esc_html__( 'دیدگاه شما', 'zarincoach' ) . '</label><textarea id="comment" class="zc-textarea" name="comment" rows="5" required></textarea></p>',
			'fields'             => $zc_c_fields,
			'class_form'         => 'zc-comment-form zc-panel mt-8',
			'comment_notes_before' => '' !== $zc_c_note ? '<p class="zc-form-note">' . esc_html( $zc_c_note ) . '</p>' : '',
			'title_reply'          => '' !== $zc_c_title ? esc_html( $zc_c_title ) : __( 'دیدگاهتان را بنویسید', 'zarincoach' ),
		)
	);
	?>
</div>
