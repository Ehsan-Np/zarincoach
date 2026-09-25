<?php
/**
 * پوسته‌ی مشترک صفحه‌های ابزار قالب (نصب دمو، سربرگ و پاورقی، اطلاعات سیستم)
 *
 * همان زبان طراحی پنل تنظیمات: ستون منوی چسبان در راست، کارت محتوا با نوار عنوان چسبان،
 * بخش‌بندی با سرفصل طلایی. بدون جاوااسکریپت همه‌ی بخش‌ها زیر هم نمایش داده می‌شوند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_tool_shell_open' ) ) :
	/**
	 * شروع پوسته‌ی صفحه‌ی ابزار.
	 *
	 * @param string $page شناسه‌ی صفحه (برای زبانه‌ی فعال نوار برند).
	 * @param array  $args {
	 *     @type string $title    عنوان صفحه.
	 *     @type string $subtitle توضیح کوتاه زیر عنوان (ستون منو).
	 *     @type string $icon     کلاس Font Awesome.
	 *     @type array  $nav      کلید بخش => array( label, icon, badge?, tone? ) یا array( 'group' => برچسب ).
	 *     @type string $actions  HTML دکمه‌های نوار عنوان (از پیش امن‌شده).
	 *     @type string $side     HTML پایین ستون منو (از پیش امن‌شده).
	 * }
	 * @return void
	 */
	function zc_tool_shell_open( $page, $args ) {
		$args = wp_parse_args(
			$args,
			array(
				'title'    => '',
				'subtitle' => '',
				'icon'     => 'fa-solid fa-gear',
				'nav'      => array(),
				'actions'  => '',
				'side'     => '',
			)
		);

		$first = '';
		foreach ( $args['nav'] as $key => $item ) {
			if ( empty( $item['group'] ) ) {
				$first = isset( $item[0] ) ? $item[0] : '';
				break;
			}
		}

		echo '<div class="wrap zc-admin-wrap zc-tool-wrap">';
		echo zc_panel_header_bar( $page ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی امن‌شده.
		echo '<h1 class="screen-reader-text">' . esc_html( $args['title'] ) . '</h1>';
		// وردپرس اعلان‌های عمومی را پس از این خط جابه‌جا می‌کند تا چیدمان به‌هم نریزد.
		echo '<hr class="wp-header-end">';

		echo '<div class="zc-tool" data-zc-tool>';

		/* ستون منو */
		echo '<aside class="zc-tool-side">';
		echo '<div class="zc-tool-side-head"><span class="zc-tool-side-icon" aria-hidden="true"><i class="' . esc_attr( $args['icon'] ) . '"></i></span><div><strong>' . esc_html( $args['title'] ) . '</strong>';
		if ( '' !== $args['subtitle'] ) {
			echo '<small>' . esc_html( $args['subtitle'] ) . '</small>';
		}
		echo '</div></div>';

		echo '<nav class="zc-tool-nav" aria-label="' . esc_attr( $args['title'] ) . '"><ul>';
		foreach ( $args['nav'] as $key => $item ) {
			if ( ! empty( $item['group'] ) ) {
				echo '<li class="zc-tool-nav-label">' . esc_html( $item['group'] ) . '</li>';
				continue;
			}
			$badge = '';
			if ( isset( $item[2] ) && '' !== (string) $item[2] ) {
				$tone  = isset( $item[3] ) ? sanitize_html_class( $item[3] ) : 'muted';
				$badge = '<em class="zc-tool-badge is-' . $tone . '">' . esc_html( (string) $item[2] ) . '</em>';
			}
			printf(
				'<li><a class="zc-tool-link" href="#zc-pane-%1$s" data-pane="%1$s"><i class="%2$s" aria-hidden="true"></i><span>%3$s</span>%4$s</a></li>',
				esc_attr( $key ),
				esc_attr( isset( $item[1] ) ? $item[1] : 'fa-solid fa-circle' ),
				esc_html( isset( $item[0] ) ? $item[0] : $key ),
				$badge // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- بالا امن شده.
			);
		}
		echo '</ul></nav>';

		if ( '' !== $args['side'] ) {
			echo '<div class="zc-tool-side-foot">' . $args['side'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- از پیش امن‌شده.
		}
		echo '</aside>';

		/* ستون محتوا */
		echo '<div class="zc-tool-main">';
		echo '<div class="zc-tool-bar"><div class="zc-bar-title"><span class="zc-bar-crumb">' . esc_html( $args['title'] ) . '</span><span class="zc-bar-current" data-zc-current>' . esc_html( $first ) . '</span></div>';
		if ( '' !== $args['actions'] ) {
			echo '<div class="zc-tool-actions">' . $args['actions'] . '</div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- از پیش امن‌شده.
		}
		echo '</div>';
		echo '<div class="zc-tool-body">';
	}
endif;

if ( ! function_exists( 'zc_tool_shell_close' ) ) :
	/**
	 * پایان پوسته‌ی صفحه‌ی ابزار.
	 *
	 * @return void
	 */
	function zc_tool_shell_close() {
		echo '</div>'; // .zc-tool-body
		echo '<div class="zc-tool-foot"><p class="zc-footer-note"><i class="fa-solid fa-circle-info" aria-hidden="true"></i><span>';
		printf(
			/* translators: %s: پیوند پنل تنظیمات */
			esc_html__( 'ظاهر، رنگ‌ها و محتوای بخش‌ها از %s قابل تغییر است.', 'zarincoach' ),
			'<a href="' . esc_url( admin_url( 'admin.php?page=zc-options' ) ) . '">' . esc_html__( 'پنل تنظیمات قالب', 'zarincoach' ) . '</a>'
		);
		echo '</span></p></div>';
		echo '</div>'; // .zc-tool-main
		echo '</div>'; // .zc-tool
		echo '</div>'; // .wrap
	}
endif;

if ( ! function_exists( 'zc_tool_pane_open' ) ) :
	/**
	 * شروع یک بخش (زبانه).
	 *
	 * @param string $key   شناسه.
	 * @param string $title عنوان.
	 * @param string $desc  توضیح.
	 * @return void
	 */
	function zc_tool_pane_open( $key, $title, $desc = '' ) {
		printf(
			'<section class="zc-tool-pane" id="zc-pane-%1$s" data-pane="%1$s" aria-labelledby="zc-pane-%1$s-title"><header class="zc-tool-pane-head"><h2 id="zc-pane-%1$s-title">%2$s</h2>%3$s</header>',
			esc_attr( $key ),
			esc_html( $title ),
			'' !== $desc ? '<p>' . esc_html( $desc ) . '</p>' : ''
		);
	}
endif;

if ( ! function_exists( 'zc_tool_pane_close' ) ) :
	/**
	 * پایان بخش.
	 *
	 * @return void
	 */
	function zc_tool_pane_close() {
		echo '</section>';
	}
endif;

if ( ! function_exists( 'zc_tool_heading' ) ) :
	/**
	 * سرفصل گروه (با نوار طلایی، مثل پنل تنظیمات).
	 *
	 * @param string $title عنوان.
	 * @param string $aside متن/HTML کوچک سمت چپ (از پیش امن‌شده).
	 * @return void
	 */
	function zc_tool_heading( $title, $aside = '' ) {
		echo '<h3 class="zc-tool-h"><span>' . esc_html( $title ) . '</span>' . ( '' !== $aside ? '<small>' . $aside . '</small>' : '' ) . '</h3>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- $aside از پیش امن‌شده.
	}
endif;

if ( ! function_exists( 'zc_tool_alert' ) ) :
	/**
	 * کادر پیام.
	 *
	 * @param string $tone  ok|warn|danger|info.
	 * @param string $title عنوان.
	 * @param string $text  متن (HTML مجاز محدود).
	 * @param string $extra HTML اضافه (دکمه‌ها، از پیش امن‌شده).
	 * @return string
	 */
	function zc_tool_alert( $tone, $title, $text = '', $extra = '' ) {
		$icons = array(
			'ok'     => 'fa-solid fa-circle-check',
			'warn'   => 'fa-solid fa-triangle-exclamation',
			'danger' => 'fa-solid fa-circle-exclamation',
			'info'   => 'fa-solid fa-circle-info',
		);
		$tone  = isset( $icons[ $tone ] ) ? $tone : 'info';
		$html  = '<div class="zc-tool-alert is-' . $tone . '"' . ( 'ok' === $tone || 'danger' === $tone ? ' role="status"' : '' ) . '>';
		$html .= '<i class="' . $icons[ $tone ] . '" aria-hidden="true"></i><div>';
		$html .= '<strong>' . esc_html( $title ) . '</strong>';
		if ( '' !== $text ) {
			$html .= '<p>' . wp_kses( $text, array( 'a' => array( 'href' => true, 'target' => true, 'rel' => true ), 'b' => array(), 'strong' => array(), 'code' => array(), 'br' => array() ) ) . '</p>';
		}
		$html .= $extra . '</div></div>';
		return $html;
	}
endif;

if ( ! function_exists( 'zc_tool_tag' ) ) :
	/**
	 * برچسب وضعیت کوچک.
	 *
	 * @param string $text متن.
	 * @param string $tone ok|warn|danger|info|muted|gold.
	 * @param string $icon کلاس آیکن اختیاری.
	 * @return string
	 */
	function zc_tool_tag( $text, $tone = 'muted', $icon = '' ) {
		return '<span class="zc-tag is-' . sanitize_html_class( $tone ) . '">' . ( '' !== $icon ? '<i class="' . esc_attr( $icon ) . '" aria-hidden="true"></i>' : '' ) . esc_html( $text ) . '</span>';
	}
endif;

if ( ! function_exists( 'zc_tool_num' ) ) :
	/**
	 * عدد با ارقام فارسی.
	 *
	 * @param int|string $n عدد.
	 * @return string
	 */
	function zc_tool_num( $n ) {
		return function_exists( 'zc_digits_to_persian' ) ? zc_digits_to_persian( (string) $n ) : (string) $n;
	}
endif;

if ( ! function_exists( 'zc_tool_date' ) ) :
	/**
	 * تاریخ و ساعت (شمسی در صورت فعال بودن) از مُهر زمانی UTC.
	 *
	 * @param int  $timestamp مُهر زمانی UTC.
	 * @param bool $time      افزودن ساعت.
	 * @return string
	 */
	function zc_tool_date( $timestamp, $time = true ) {
		$timestamp = (int) $timestamp;
		if ( $timestamp <= 0 ) {
			return '—';
		}
		$local = $timestamp + (int) ( (float) get_option( 'gmt_offset', 0 ) * HOUR_IN_SECONDS );
		$date  = function_exists( 'zc_jalali_date' ) ? zc_jalali_date( $local ) : '';
		if ( '' === $date ) {
			$date = wp_date( (string) get_option( 'date_format', 'Y-m-d' ), $timestamp );
		}
		if ( $time ) {
			$date .= ' · ' . zc_tool_num( wp_date( 'H:i', $timestamp ) );
		}
		return $date;
	}
endif;

if ( ! function_exists( 'zc_tool_assets' ) ) :
	/**
	 * استایل و اسکریپت صفحه‌های ابزار.
	 *
	 * @return void
	 */
	function zc_tool_assets() {
		$page = function_exists( 'zc_is_panel_screen' ) ? zc_is_panel_screen() : '';
		if ( '' === $page || 'zc-options' === $page ) {
			return;
		}
		$css = ZC_DIR . '/assets/admin/tools.css';
		$js  = ZC_DIR . '/assets/admin/tools.js';
		wp_enqueue_style( 'zc-tools', ZC_URI . '/assets/admin/tools.css', array( 'zc-panel' ), ZC_VERSION . '.' . ( file_exists( $css ) ? filemtime( $css ) : 0 ) );
		wp_enqueue_script( 'zc-tools', ZC_URI . '/assets/admin/tools.js', array(), ZC_VERSION . '.' . ( file_exists( $js ) ? filemtime( $js ) : 0 ), true );
		wp_localize_script(
			'zc-tools',
			'zcTools',
			array(
				'copied'   => __( 'کپی شد', 'zarincoach' ),
				'copyFail' => __( 'کپی نشد؛ متن را دستی انتخاب کنید.', 'zarincoach' ),
			)
		);
	}
endif;
add_action( 'admin_enqueue_scripts', 'zc_tool_assets', 31 );

if ( ! function_exists( 'zc_widget_count' ) ) :
	/**
	 * تعداد ویجت‌های اختصاصی المنتور قالب.
	 *
	 * @return int
	 */
	function zc_widget_count() {
		$files = glob( ZC_DIR . '/inc/elementor/widgets/class-zc-widget-*.php' );
		return is_array( $files ) ? count( $files ) : 0;
	}
endif;
