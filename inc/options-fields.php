<?php
/**
 * سازنده‌های فشرده‌ی فیلدهای پنل Redux.
 *
 * هر تابع یک آرایه‌ی فیلد استاندارد Redux برمی‌گرداند تا تعریف بخش‌ها کوتاه،
 * خوانا و یکدست بماند. کلید `$extra` هر ویژگی دلخواه Redux را اضافه/بازنویسی می‌کند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_f' ) ) :
	/**
	 * سازنده‌ی پایه‌ی همه‌ی فیلدها.
	 *
	 * @param string $type  نوع فیلد Redux.
	 * @param string $id    شناسه‌ی گزینه.
	 * @param string $title عنوان.
	 * @param array  $extra ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f( $type, $id, $title, $extra = array() ) {
		$field = array(
			'id'    => $id,
			'type'  => $type,
			'title' => $title,
		);
		foreach ( array( 'desc', 'subtitle' ) as $key ) {
			if ( isset( $extra[ $key ] ) && '' === $extra[ $key ] ) {
				unset( $extra[ $key ] );
			}
		}
		return array_merge( $field, $extra );
	}
endif;

if ( ! function_exists( 'zc_f_switch' ) ) :
	/**
	 * کلید روشن/خاموش.
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param bool   $default پیش‌فرض.
	 * @param string $desc    توضیح کوتاه زیر عنوان.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_switch( $id, $title, $default = true, $desc = '', $extra = array() ) {
		return zc_f(
			'switch',
			$id,
			$title,
			array_merge(
				array(
					'default'  => $default ? '1' : '0',
					'on'       => __( 'فعال', 'zarincoach' ),
					'off'      => __( 'غیرفعال', 'zarincoach' ),
					'subtitle' => $desc,
				),
				$extra
			)
		);
	}
endif;

if ( ! function_exists( 'zc_f_text' ) ) :
	/**
	 * فیلد متن تک‌خطی.
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param string $default پیش‌فرض.
	 * @param string $desc    توضیح.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_text( $id, $title, $default = '', $desc = '', $extra = array() ) {
		return zc_f( 'text', $id, $title, array_merge( array( 'default' => $default, 'subtitle' => $desc ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_ltr' ) ) :
	/**
	 * فیلد متن چپ‌چین (نشانی، ایمیل، شماره، مختصات).
	 *
	 * @param string $id       شناسه.
	 * @param string $title    عنوان.
	 * @param string $default  پیش‌فرض.
	 * @param string $desc     توضیح.
	 * @param string $validate اعتبارسنجی Redux (url|email|numeric|…).
	 * @param array  $extra    ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_ltr( $id, $title, $default = '', $desc = '', $validate = '', $extra = array() ) {
		$field = array(
			'default'    => $default,
			'subtitle'   => $desc,
			'class'      => 'zc-ltr',
			'attributes' => array( 'dir' => 'ltr' ),
		);
		if ( 'url' === $validate ) {
			$field['placeholder'] = 'https://';
		}
		if ( in_array( $validate, array( 'url', 'email' ), true ) ) {
			// اعتبارسنجی Redux مقدار خالی را رد می‌کند؛ این نسخه خالی را مجاز می‌داند.
			$field['zc_validate']       = $validate;
			$field['validate_callback'] = 'zc_validate_optional';
		} elseif ( '' !== $validate ) {
			$field['validate'] = $validate;
		}
		return zc_f( 'text', $id, $title, array_merge( $field, $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_textarea' ) ) :
	/**
	 * متن چندخطی.
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param string $default پیش‌فرض.
	 * @param string $desc    توضیح.
	 * @param int    $rows    تعداد سطر.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_textarea( $id, $title, $default = '', $desc = '', $rows = 3, $extra = array() ) {
		return zc_f( 'textarea', $id, $title, array_merge( array( 'default' => $default, 'subtitle' => $desc, 'rows' => $rows ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_select' ) ) :
	/**
	 * فهرست کشویی.
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param array  $options گزینه‌ها.
	 * @param string $default پیش‌فرض.
	 * @param string $desc    توضیح.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_select( $id, $title, $options, $default = '', $desc = '', $extra = array() ) {
		return zc_f( 'select', $id, $title, array_merge( array( 'options' => $options, 'default' => $default, 'subtitle' => $desc, 'select2' => array( 'allowClear' => false, 'minimumResultsForSearch' => 8 ) ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_buttons' ) ) :
	/**
	 * گروه دکمه‌ای (انتخاب یکی از چند گزینه‌ی کوتاه).
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param array  $options گزینه‌ها.
	 * @param string $default پیش‌فرض.
	 * @param string $desc    توضیح.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_buttons( $id, $title, $options, $default = '', $desc = '', $extra = array() ) {
		return zc_f( 'button_set', $id, $title, array_merge( array( 'options' => $options, 'default' => $default, 'subtitle' => $desc ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_slider' ) ) :
	/**
	 * لغزنده‌ی عددی.
	 *
	 * @param string    $id      شناسه.
	 * @param string    $title   عنوان.
	 * @param int|float $default پیش‌فرض.
	 * @param int|float $min     کمینه.
	 * @param int|float $max     بیشینه.
	 * @param int|float $step    گام.
	 * @param string    $desc    توضیح.
	 * @param array     $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_slider( $id, $title, $default, $min, $max, $step = 1, $desc = '', $extra = array() ) {
		$field = array(
			'default'       => $default,
			'min'           => $min,
			'max'           => $max,
			'step'          => $step,
			'display_value' => 'text',
			'subtitle'      => $desc,
		);
		if ( is_float( $step ) || ( $step < 1 ) ) {
			$field['resolution'] = ( $step < 0.1 ) ? 0.01 : 0.1;
		}
		return zc_f( 'slider', $id, $title, array_merge( $field, $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_media' ) ) :
	/**
	 * انتخاب تصویر از کتابخانه.
	 *
	 * @param string $id    شناسه.
	 * @param string $title عنوان.
	 * @param string $desc  توضیح.
	 * @param array  $extra ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_media( $id, $title, $desc = '', $extra = array() ) {
		return zc_f( 'media', $id, $title, array_merge( array( 'url' => false, 'preview_size' => 'medium', 'library_filter' => array( 'jpg', 'jpeg', 'png', 'webp', 'svg', 'gif', 'avif' ), 'subtitle' => $desc ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_color' ) ) :
	/**
	 * انتخاب رنگ.
	 *
	 * @param string $id      شناسه.
	 * @param string $title   عنوان.
	 * @param string $default پیش‌فرض.
	 * @param string $desc    توضیح.
	 * @param array  $extra   ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_color( $id, $title, $default, $desc = '', $extra = array() ) {
		return zc_f( 'color', $id, $title, array_merge( array( 'default' => $default, 'transparent' => false, 'validate' => 'color', 'subtitle' => $desc ), $extra ) );
	}
endif;

if ( ! function_exists( 'zc_f_code' ) ) :
	/**
	 * ویرایشگر کد (Ace).
	 *
	 * @param string $id    شناسه.
	 * @param string $title عنوان.
	 * @param string $mode  html|css|javascript.
	 * @param string $desc  توضیح.
	 * @param array  $extra ویژگی‌های اضافه.
	 * @return array
	 */
	function zc_f_code( $id, $title, $mode = 'html', $desc = '', $extra = array() ) {
		return zc_f(
			'ace_editor',
			$id,
			$title,
			array_merge(
				array(
					'mode'     => $mode,
					'theme'    => 'chrome',
					'default'  => '',
					'subtitle' => $desc,
					'options'  => array( 'minLines' => 8, 'maxLines' => 30, 'fontSize' => 13 ),
				),
				$extra
			)
		);
	}
endif;

if ( ! function_exists( 'zc_f_group' ) ) :
	/**
	 * سرفصل گروه (Redux section-field) برای دسته‌بندی فیلدها داخل یک بخش.
	 *
	 * @param string $id    شناسه‌ی یکتا.
	 * @param string $title عنوان.
	 * @param string $desc  توضیح.
	 * @return array
	 */
	function zc_f_group( $id, $title, $desc = '' ) {
		return zc_f( 'section', $id, $title, array( 'indent' => false, 'subtitle' => $desc ) );
	}
endif;

if ( ! function_exists( 'zc_f_note' ) ) :
	/**
	 * کادر راهنما.
	 *
	 * @param string $id    شناسه‌ی یکتا.
	 * @param string $title عنوان.
	 * @param string $desc  متن (HTML مجاز).
	 * @param string $style info|warning|success|critical.
	 * @return array
	 */
	function zc_f_note( $id, $title, $desc, $style = 'info' ) {
		return zc_f( 'info', $id, $title, array( 'style' => $style, 'desc' => $desc, 'class' => 'zc-note zc-note--' . $style ) );
	}
endif;

if ( ! function_exists( 'zc_f_raw' ) ) :
	/**
	 * محتوای HTML دلخواه (بدون عنوان).
	 *
	 * @param string $id      شناسه‌ی یکتا.
	 * @param string $content HTML.
	 * @return array
	 */
	function zc_f_raw( $id, $content ) {
		return array(
			'id'         => $id,
			'type'       => 'raw',
			'content'    => $content,
			'full_width' => true,
		);
	}
endif;

if ( ! function_exists( 'zc_req' ) ) :
	/**
	 * میان‌بر شرط نمایش فیلد (required).
	 *
	 * @param array  $field فیلد.
	 * @param string $id    شناسه‌ی فیلد والد.
	 * @param string $op    عملگر.
	 * @param mixed  $value مقدار.
	 * @return array
	 */
	function zc_req( $field, $id, $op = '=', $value = '1' ) {
		$field['required'] = array( $id, $op, $value );
		return $field;
	}
endif;

if ( ! function_exists( 'zc_req_on' ) ) :
	/**
	 * نمایش گروهی از فیلدها فقط وقتی کلید والد روشن است.
	 *
	 * @param array  $fields فیلدها.
	 * @param string $id     شناسه‌ی کلید والد.
	 * @return array
	 */
	function zc_req_on( $fields, $id ) {
		foreach ( $fields as $i => $field ) {
			if ( empty( $field['required'] ) ) {
				$fields[ $i ]['required'] = array( $id, '=', '1' );
			}
		}
		return $fields;
	}
endif;

if ( ! function_exists( 'zc_admin_link' ) ) :
	/**
	 * پیوند دکمه‌ای برای استفاده در محتوای پنل.
	 *
	 * @param string $url     نشانی.
	 * @param string $label   برچسب.
	 * @param string $variant primary|ghost.
	 * @param bool   $blank   باز شدن در زبانه‌ی جدید.
	 * @return string
	 */
	function zc_admin_link( $url, $label, $variant = 'ghost', $blank = false ) {
		return sprintf(
			'<a class="zc-btn zc-btn--%1$s" href="%2$s"%3$s>%4$s</a>',
			esc_attr( $variant ),
			esc_url( $url ),
			$blank ? ' target="_blank" rel="noopener"' : '',
			esc_html( $label )
		);
	}
endif;

if ( ! function_exists( 'zc_validate_optional' ) ) :
	/**
	 * اعتبارسنجی نشانی/ایمیلِ اختیاری (خالی مجاز است).
	 *
	 * @param array  $field    فیلد.
	 * @param mixed  $value    مقدار جدید.
	 * @param mixed  $existing مقدار فعلی.
	 * @return array
	 */
	function zc_validate_optional( $field, $value, $existing ) {
		$value = trim( (string) $value );
		$type  = isset( $field['zc_validate'] ) ? $field['zc_validate'] : '';
		if ( '' === $value ) {
			return array( 'value' => '' );
		}
		if ( 'email' === $type ) {
			if ( is_email( $value ) ) {
				return array( 'value' => sanitize_email( $value ) );
			}
			$field['msg'] = __( 'نشانی ایمیل معتبر نیست؛ مقدار قبلی حفظ شد.', 'zarincoach' );
		} else {
			if ( false !== filter_var( $value, FILTER_VALIDATE_URL ) ) {
				return array( 'value' => esc_url_raw( $value ) );
			}
			$field['msg'] = __( 'نشانی اینترنتی معتبر نیست (با https:// شروع کنید)؛ مقدار قبلی حفظ شد.', 'zarincoach' );
		}
		$field['current'] = $existing;
		return array(
			'value' => (string) $existing,
			'error' => $field,
		);
	}
endif;
