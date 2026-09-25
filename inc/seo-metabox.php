<?php
/**
 * جعبه‌ی «سئو» برای برگه‌ها، نوشته‌ها و خدمات: عنوان و توضیح اختصاصی (با شمارنده و پیش‌نمایش گوگل)،
 * noindex، canonical و نوع صفحه در اسکیما.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_seo_meta_post_types' ) ) :
	/**
	 * انواع محتوایی که جعبه‌ی سئو دارند.
	 *
	 * @return string[]
	 */
	function zc_seo_meta_post_types() {
		return (array) apply_filters( 'zc_seo_meta_post_types', array( 'page', 'post', 'zc_service' ) );
	}
endif;

if ( ! function_exists( 'zc_seo_register_meta' ) ) :
	/**
	 * ثبت فراداده‌ها (برای REST/ویرایشگر بلوکی و پاک‌سازی خودکار).
	 *
	 * @return void
	 */
	function zc_seo_register_meta() {
		$fields = array(
			'_zc_seo_title'     => 'sanitize_text_field',
			'_zc_seo_desc'      => 'sanitize_textarea_field',
			'_zc_seo_noindex'   => 'zc_seo_sanitize_flag',
			'_zc_seo_canonical' => 'esc_url_raw',
			'_zc_seo_schema'    => 'zc_seo_sanitize_schema_type',
		);
		foreach ( zc_seo_meta_post_types() as $type ) {
			foreach ( $fields as $key => $cb ) {
				register_post_meta(
					$type,
					$key,
					array(
						'type'              => 'string',
						'single'            => true,
						'show_in_rest'      => false,
						'sanitize_callback' => $cb,
						'auth_callback'     => static function () {
							return current_user_can( 'edit_posts' );
						},
					)
				);
			}
		}
	}
endif;
add_action( 'init', 'zc_seo_register_meta', 20 );

if ( ! function_exists( 'zc_seo_sanitize_flag' ) ) :
	/**
	 * پاک‌سازی پرچم ۰/۱.
	 *
	 * @param mixed $v مقدار.
	 * @return string
	 */
	function zc_seo_sanitize_flag( $v ) {
		return ( '1' === (string) $v || 'on' === $v || true === $v ) ? '1' : '';
	}
endif;

if ( ! function_exists( 'zc_seo_sanitize_schema_type' ) ) :
	/**
	 * پاک‌سازی نوع صفحه.
	 *
	 * @param mixed $v مقدار.
	 * @return string
	 */
	function zc_seo_sanitize_schema_type( $v ) {
		$v = (string) $v;
		return array_key_exists( $v, zc_schema_page_types() ) ? $v : '';
	}
endif;

if ( ! function_exists( 'zc_seo_add_metabox' ) ) :
	/**
	 * افزودن جعبه.
	 *
	 * @return void
	 */
	function zc_seo_add_metabox() {
		if ( ! zc_seo_active() ) {
			return;
		}
		foreach ( zc_seo_meta_post_types() as $type ) {
			add_meta_box( 'zc-seo', __( 'سئو و اسکیما — زرین‌کوچ', 'zarincoach' ), 'zc_seo_render_metabox', $type, 'normal', 'default' );
		}
	}
endif;
add_action( 'add_meta_boxes', 'zc_seo_add_metabox' );

if ( ! function_exists( 'zc_seo_render_metabox' ) ) :
	/**
	 * خروجی جعبه.
	 *
	 * @param WP_Post $post نوشته.
	 * @return void
	 */
	function zc_seo_render_metabox( $post ) {
		wp_nonce_field( 'zc_seo_save', 'zc_seo_nonce' );
		$title     = (string) get_post_meta( $post->ID, '_zc_seo_title', true );
		$desc      = (string) get_post_meta( $post->ID, '_zc_seo_desc', true );
		$noindex   = '1' === (string) get_post_meta( $post->ID, '_zc_seo_noindex', true );
		$canonical = (string) get_post_meta( $post->ID, '_zc_seo_canonical', true );
		$schema    = (string) get_post_meta( $post->ID, '_zc_seo_schema', true );
		$sep       = ' ' . (string) zc_opt( 'seo_title_sep', '|' ) . ' ';
		$auto      = get_the_title( $post ) . $sep . get_bloginfo( 'name' );
		$permalink = get_permalink( $post );
		?>
		<div class="zc-seo-box" dir="rtl" style="display:grid;gap:14px;max-width:760px">
			<div class="zc-seo-preview" style="border:1px solid #dcdcde;border-radius:10px;padding:12px 14px;background:#fff;font-family:Tahoma,Arial,sans-serif">
				<div style="font-size:12px;color:#4d5156;direction:ltr;text-align:right;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"><?php echo esc_html( $permalink ); ?></div>
				<div data-zc-seo-pv-title style="font-size:18px;color:#1a0dab;line-height:1.5;margin:3px 0"><?php echo esc_html( '' !== $title ? $title : $auto ); ?></div>
				<div data-zc-seo-pv-desc style="font-size:13px;color:#4d5156;line-height:1.7"><?php echo esc_html( '' !== $desc ? $desc : __( 'توضیح متا خالی است؛ از خلاصه‌ی نوشته یا معرفی سایت استفاده می‌شود.', 'zarincoach' ) ); ?></div>
			</div>

			<p style="margin:0">
				<label for="zc_seo_title"><strong><?php esc_html_e( 'عنوان سئو', 'zarincoach' ); ?></strong> <span data-zc-seo-count="zc_seo_title" data-max="60" style="color:#646970"></span></label>
				<input type="text" class="widefat" id="zc_seo_title" name="zc_seo_title" value="<?php echo esc_attr( $title ); ?>" placeholder="<?php echo esc_attr( $auto ); ?>">
				<span class="description"><?php esc_html_e( 'خالی = «عنوان برگه | نام سایت». پیشنهاد: ۳۰ تا ۶۰ نویسه، کلیدواژه‌ی اصلی در ابتدا.', 'zarincoach' ); ?></span>
			</p>

			<p style="margin:0">
				<label for="zc_seo_desc"><strong><?php esc_html_e( 'توضیح متا', 'zarincoach' ); ?></strong> <span data-zc-seo-count="zc_seo_desc" data-max="155" style="color:#646970"></span></label>
				<textarea class="widefat" rows="3" id="zc_seo_desc" name="zc_seo_desc"><?php echo esc_textarea( $desc ); ?></textarea>
				<span class="description"><?php esc_html_e( 'پیشنهاد: ۱۲۰ تا ۱۵۵ نویسه؛ خلاصه‌ای دقیق و دعوت‌کننده از همین صفحه.', 'zarincoach' ); ?></span>
			</p>

			<div style="display:grid;gap:14px;grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
				<p style="margin:0">
					<label for="zc_seo_schema"><strong><?php esc_html_e( 'نوع صفحه در اسکیما', 'zarincoach' ); ?></strong></label>
					<select class="widefat" id="zc_seo_schema" name="zc_seo_schema">
						<?php foreach ( zc_schema_page_types() as $value => $label ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $schema, $value ); ?>><?php echo esc_html( $label ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>
				<p style="margin:0">
					<label for="zc_seo_canonical"><strong><?php esc_html_e( 'آدرس canonical (اختیاری)', 'zarincoach' ); ?></strong></label>
					<input type="url" class="widefat" dir="ltr" id="zc_seo_canonical" name="zc_seo_canonical" value="<?php echo esc_attr( $canonical ); ?>" placeholder="<?php echo esc_attr( $permalink ); ?>">
				</p>
			</div>

			<p style="margin:0">
				<label><input type="checkbox" name="zc_seo_noindex" value="1" <?php checked( $noindex ); ?>> <?php esc_html_e( 'این صفحه در نتایج موتورهای جستجو نمایش داده نشود (noindex) و از نقشه‌ی سایت حذف شود', 'zarincoach' ); ?></label>
			</p>
		</div>
		<script>
		( function () {
			var box = document.querySelector( '.zc-seo-box' );
			if ( ! box ) { return; }
			var auto = <?php echo wp_json_encode( $auto ); ?>;
			var emptyDesc = <?php echo wp_json_encode( __( 'توضیح متا خالی است؛ از خلاصه‌ی نوشته یا معرفی سایت استفاده می‌شود.', 'zarincoach' ) ); ?>;
			function fa( n ) { return String( n ).replace( /\d/g, function ( d ) { return '۰۱۲۳۴۵۶۷۸۹'[ d ]; } ); }
			function update() {
				box.querySelectorAll( '[data-zc-seo-count]' ).forEach( function ( el ) {
					var input = document.getElementById( el.getAttribute( 'data-zc-seo-count' ) );
					var max = parseInt( el.getAttribute( 'data-max' ), 10 );
					var len = input.value.trim().length;
					el.textContent = '(' + fa( len ) + ' / ' + fa( max ) + ')';
					el.style.color = len > max ? '#b32d2e' : ( len >= max * 0.6 ? '#008a20' : '#646970' );
				} );
				var t = document.getElementById( 'zc_seo_title' ).value.trim();
				var d = document.getElementById( 'zc_seo_desc' ).value.trim();
				box.querySelector( '[data-zc-seo-pv-title]' ).textContent = t || auto;
				box.querySelector( '[data-zc-seo-pv-desc]' ).textContent = d ? ( d.length > 160 ? d.slice( 0, 157 ) + '…' : d ) : emptyDesc;
			}
			box.addEventListener( 'input', update );
			update();
		}() );
		</script>
		<?php
	}
endif;

if ( ! function_exists( 'zc_seo_save_metabox' ) ) :
	/**
	 * ذخیره.
	 *
	 * @param int $post_id شناسه.
	 * @return void
	 */
	function zc_seo_save_metabox( $post_id ) {
		if ( ! isset( $_POST['zc_seo_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['zc_seo_nonce'] ) ), 'zc_seo_save' ) ) {
			return;
		}
		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || wp_is_post_revision( $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}
		$values = array(
			'_zc_seo_title'     => isset( $_POST['zc_seo_title'] ) ? sanitize_text_field( wp_unslash( $_POST['zc_seo_title'] ) ) : '',
			'_zc_seo_desc'      => isset( $_POST['zc_seo_desc'] ) ? sanitize_textarea_field( wp_unslash( $_POST['zc_seo_desc'] ) ) : '',
			'_zc_seo_noindex'   => isset( $_POST['zc_seo_noindex'] ) ? '1' : '',
			'_zc_seo_canonical' => isset( $_POST['zc_seo_canonical'] ) ? esc_url_raw( wp_unslash( $_POST['zc_seo_canonical'] ) ) : '',
			'_zc_seo_schema'    => isset( $_POST['zc_seo_schema'] ) ? zc_seo_sanitize_schema_type( sanitize_text_field( wp_unslash( $_POST['zc_seo_schema'] ) ) ) : '',
		);
		foreach ( $values as $key => $value ) {
			if ( '' === $value ) {
				delete_post_meta( $post_id, $key );
			} else {
				update_post_meta( $post_id, $key, $value );
			}
		}
	}
endif;
add_action( 'save_post', 'zc_seo_save_metabox' );
