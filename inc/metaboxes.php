<?php
/**
 * جعبه‌های متای اختصاصی
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_add_meta_boxes' ) ) :
	/**
	 * افزودن جعبه‌های متا.
	 *
	 * @return void
	 */
	function zc_add_meta_boxes() {
		add_meta_box(
			'zc_service_details',
			__( 'جزئیات خدمت', 'zarincoach' ),
			'zc_render_service_meta_box',
			'zc_service',
			'side',
			'high'
		);

		add_meta_box(
			'zc_testimonial_details',
			__( 'اطلاعات مراجع', 'zarincoach' ),
			'zc_render_testimonial_meta_box',
			'zc_testimonial',
			'side',
			'high'
		);
	}
endif;
add_action( 'add_meta_boxes', 'zc_add_meta_boxes' );

if ( ! function_exists( 'zc_render_service_meta_box' ) ) :
	/**
	 * نمایش جعبه متای خدمت.
	 *
	 * @param WP_Post $post نوشته.
	 * @return void
	 */
	function zc_render_service_meta_box( $post ) {
		wp_nonce_field( 'zc_save_service_meta', 'zc_service_meta_nonce' );

		$icon     = (string) get_post_meta( $post->ID, '_zc_service_icon', true );
		$price    = (string) get_post_meta( $post->ID, '_zc_service_price', true );
		$duration = (string) get_post_meta( $post->ID, '_zc_service_duration', true );
		$badge    = (string) get_post_meta( $post->ID, '_zc_service_badge', true );
		$order    = (string) get_post_meta( $post->ID, '_zc_service_order', true );

		$icons = function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array();
		?>
		<p>
			<label class="zc-label" for="zc_service_icon"><strong><?php esc_html_e( 'آیکون', 'zarincoach' ); ?></strong></label>
			<select id="zc_service_icon" name="zc_service_icon" class="widefat">
				<?php foreach ( $icons as $key => $label ) : ?>
					<option value="<?php echo esc_attr( $key ); ?>" <?php selected( $icon, $key ); ?>><?php echo esc_html( $label ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>

		<p>
			<label class="zc-label" for="zc_service_price"><strong><?php esc_html_e( 'قیمت (یا توضیح هزینه)', 'zarincoach' ); ?></strong></label>
			<input class="widefat" type="text" id="zc_service_price" name="zc_service_price" value="<?php echo esc_attr( $price ); ?>" placeholder="<?php esc_attr_e( 'مثال: ۸۵۰ هزار تومان', 'zarincoach' ); ?>">
		</p>

		<p>
			<label class="zc-label" for="zc_service_duration"><strong><?php esc_html_e( 'مدت زمان', 'zarincoach' ); ?></strong></label>
			<input class="widefat" type="text" id="zc_service_duration" name="zc_service_duration" value="<?php echo esc_attr( $duration ); ?>" placeholder="<?php esc_attr_e( 'مثال: ۶۰ دقیقه', 'zarincoach' ); ?>">
		</p>

		<p>
			<label class="zc-label" for="zc_service_badge"><strong><?php esc_html_e( 'برچسب روی کارت', 'zarincoach' ); ?></strong></label>
			<input class="widefat" type="text" id="zc_service_badge" name="zc_service_badge" value="<?php echo esc_attr( $badge ); ?>" placeholder="<?php esc_attr_e( 'مثال: پرطرفدار', 'zarincoach' ); ?>">
		</p>

		<p>
			<label class="zc-label" for="zc_service_order"><strong><?php esc_html_e( 'ترتیب نمایش', 'zarincoach' ); ?></strong></label>
			<input class="widefat" type="number" id="zc_service_order" name="zc_service_order" value="<?php echo esc_attr( '' === $order ? '10' : $order ); ?>" min="0" step="1">
		</p>
		<?php
	}
endif;

if ( ! function_exists( 'zc_render_testimonial_meta_box' ) ) :
	/**
	 * نمایش جعبه متای مراجع.
	 *
	 * @param WP_Post $post نوشته.
	 * @return void
	 */
	function zc_render_testimonial_meta_box( $post ) {
		wp_nonce_field( 'zc_save_testimonial_meta', 'zc_testimonial_meta_nonce' );

		$role   = (string) get_post_meta( $post->ID, '_zc_testimonial_role', true );
		$rating = (int) get_post_meta( $post->ID, '_zc_testimonial_rating', true );
		?>
		<p>
			<label class="zc-label" for="zc_testimonial_role"><strong><?php esc_html_e( 'نقش یا عنوان', 'zarincoach' ); ?></strong></label>
			<input class="widefat" type="text" id="zc_testimonial_role" name="zc_testimonial_role" value="<?php echo esc_attr( $role ); ?>" placeholder="<?php esc_attr_e( 'مثال: مراجع کوچینگ فردی', 'zarincoach' ); ?>">
		</p>

		<p>
			<label class="zc-label" for="zc_testimonial_rating"><strong><?php esc_html_e( 'امتیاز (۱ تا ۵)', 'zarincoach' ); ?></strong></label>
			<select class="widefat" id="zc_testimonial_rating" name="zc_testimonial_rating">
				<?php for ( $i = 5; $i >= 1; $i-- ) : ?>
					<option value="<?php echo esc_attr( (string) $i ); ?>" <?php selected( $rating, $i ); ?>><?php echo esc_html( (string) $i ); ?></option>
				<?php endfor; ?>
			</select>
		</p>
		<?php
	}
endif;

if ( ! function_exists( 'zc_save_meta_boxes' ) ) :
	/**
	 * ذخیره‌سازی امن داده‌های متا.
	 *
	 * @param int      $post_id شناسه نوشته.
	 * @param WP_Post  $post    نوشته.
	 * @param bool     $update  بروزرسانی یا ایجاد.
	 * @return void
	 */
	function zc_save_meta_boxes( $post_id, $post, $update ) {
		// بررسی‌های امنیتی پایه.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		/* ------------------------ خدمت ------------------------ */
		if ( 'zc_service' === $post->post_type ) {
			if ( ! isset( $_POST['zc_service_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zc_service_meta_nonce'] ) ), 'zc_save_service_meta' ) ) {
				return;
			}

			$fields = array(
				'_zc_service_icon'     => isset( $_POST['zc_service_icon'] ) ? sanitize_key( wp_unslash( $_POST['zc_service_icon'] ) ) : 'sparkles',
				'_zc_service_price'    => isset( $_POST['zc_service_price'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_service_price'] ) ) : '',
				'_zc_service_duration' => isset( $_POST['zc_service_duration'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_service_duration'] ) ) : '',
				'_zc_service_badge'    => isset( $_POST['zc_service_badge'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_service_badge'] ) ) : '',
				'_zc_service_order'    => isset( $_POST['zc_service_order'] ) ? absint( wp_unslash( $_POST['zc_service_order'] ) ) : 10,
			);

			foreach ( $fields as $key => $value ) {
				update_post_meta( $post_id, $key, $value );
			}
		}

		/* --------------------- تجربه مراجع --------------------- */
		if ( 'zc_testimonial' === $post->post_type ) {
			if ( ! isset( $_POST['zc_testimonial_meta_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zc_testimonial_meta_nonce'] ) ), 'zc_save_testimonial_meta' ) ) {
				return;
			}

			update_post_meta( $post_id, '_zc_testimonial_role', isset( $_POST['zc_testimonial_role'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_testimonial_role'] ) ) : '' );
			update_post_meta( $post_id, '_zc_testimonial_rating', isset( $_POST['zc_testimonial_rating'] ) ? max( 1, min( 5, absint( wp_unslash( $_POST['zc_testimonial_rating'] ) ) ) ) : 5 );
		}
	}
endif;
add_action( 'save_post', 'zc_save_meta_boxes', 10, 3 );
