<?php
/**
 * مرکز لاگ سیستم — زرین‌کوچ (v2.8.0)
 *
 * جمع‌آوری، تریاژ، فیلتر و دانلود لاگ از «همه‌ی» منابع سایت در یک کارت
 * داخل صفحه‌ی اطلاعات سیستم:
 *
 *  ۱) debug.log وردپرس (WP_DEBUG_LOG — مسیر دلخواه هم پشتیبانی می‌شود)
 *  ۲) error_log پی‌اچ‌پی (ini) + error_logهای رایج هاست کنار نصب
 *  ۳) لاگ دیتابیسی المنتور (آپشن elementor_log)
 *  ۴) فایل‌های لاگ ووکامرس (uploads/wc-logs/*.log — هر فایل یک منبع)
 *  ۵) لاگ اجرایی زنده‌ی قالب: خطاهای ایمیل (wp_mail_failed)، خطاهای HTTP
 *     (http_api_debug) و خطاهای پایگاه‌داده (EZSQL_ERROR) — با چرخش ۱MB
 *  ۶) خطاهای پایگاه‌داده‌ی همین درخواست ($EZSQL_ERROR)
 *
 * امنیت: همه‌ی عملیات فقط با مجوز manage_options + nonce؛ مسیر فایل‌ها هرگز
 * از ورودی کاربر گرفته نمی‌شود (شناسه‌ی منبع → مسیر از ثبتر داخلی)؛ پوشه‌ی
 * لاگ اجرایی با .htaccess محافظت می‌شود؛ خروجی‌ها esc_html می‌شوند.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

/* -------------------------------------------------------------------------
 * ۰) منابع
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_uploads_dir' ) ) :
	/**
	 * پوشه‌ی لاگ اجرایی قالب (با محافظت).
	 *
	 * @param bool $create ساخته شود؟
	 * @return string مسیر یا رشته‌ی خالی.
	 */
	function zc_logs_uploads_dir( $create = true ) {
		$up = wp_get_upload_dir();
		if ( empty( $up['basedir'] ) ) {
			return '';
		}
		$dir = trailingslashit( $up['basedir'] ) . 'zarincoach-logs';
		if ( $create && ! is_dir( $dir ) ) {
			wp_mkdir_p( $dir );
			@file_put_contents( $dir . '/.htaccess', "Deny from all\n" ); // phpcs:ignore WordPress.WP.AlternativeFunctions
			@file_put_contents( $dir . '/index.html', '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		}
		return is_dir( $dir ) ? $dir : '';
	}
endif;

if ( ! function_exists( 'zc_logs_runtime_path' ) ) :
	/**
	 * مسیر فایل لاگ اجرایی.
	 *
	 * @return string
	 */
	function zc_logs_runtime_path() {
		$dir = zc_logs_uploads_dir();
		return $dir ? $dir . '/runtime.log' : '';
	}
endif;

if ( ! function_exists( 'zc_logs_runtime_write' ) ) :
	/**
	 * نوشتن یک سطر در لاگ اجرایی (با چرخش ۱ مگابایت).
	 *
	 * @param string $level  سطح.
	 * @param string $source برچسب مبدأ (MAIL/HTTP/DB/…).
	 * @param string $message متن.
	 * @return void
	 */
	function zc_logs_runtime_write( $level, $source, $message ) {
		$path = zc_logs_runtime_path();
		if ( ! $path ) {
			return;
		}
		if ( @filesize( $path ) > 1048576 ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
			$old = (string) @file_get_contents( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents__file_get_contents, WordPress.PHP.NoSilencedErrors
			$keep = substr( $old, -204800 );
			$pos  = strpos( $keep, "\n" );
			@file_put_contents( $path, "[INFO] (rotation) log truncated\n" . ( false === $pos ? $keep : substr( $keep, $pos + 1 ) ), LOCK_EX ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		}
		$line = '[' . current_time( 'Y-m-d H:i:s' ) . '] ZC ' . strtoupper( (string) $level ) . ' [' . $source . ']: ' . wp_strip_all_tags( (string) $message ) . "\n";
		@file_put_contents( $path, $line, FILE_APPEND | LOCK_EX ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
	}
endif;

if ( ! function_exists( 'zc_logs_runtime_boot' ) ) :
	/**
	 * ثبت هوک‌های جمع‌آوری زنده (کلید پنل: log_collect_runtime، پیش‌فرض روشن).
	 *
	 * @return void
	 */
	function zc_logs_runtime_boot() {
		if ( ! function_exists( 'zc_switch' ) || ! zc_switch( 'log_collect_runtime', true ) ) {
			return;
		}

		/* خطاهای ایمیل. */
		add_action(
			'wp_mail_failed',
			static function ( $error ) {
				if ( is_wp_error( $error ) ) {
					zc_logs_runtime_write( 'error', 'MAIL', $error->get_error_message() );
				}
			},
			10
		);

		/* خطاهای HTTP API (فقط شکست‌ها و پاسخ‌های غیرموفق). */
		add_action(
			'http_api_debug',
			static function ( $response, $context, $class, $args, $url ) {
				$bad = is_wp_error( $response );
				$code = 0;
				if ( ! $bad && is_array( $response ) && isset( $response['response']['code'] ) ) {
					$code = (int) $response['response']['code'];
					$bad  = $code >= 400;
				}
				if ( $bad ) {
					zc_logs_runtime_write(
						'warning',
						'HTTP',
						( is_wp_error( $response ) ? $response->get_error_message() : 'HTTP ' . $code ) . ' — ' . (string) parse_url( (string) $url, PHP_URL_HOST )
					);
				}
			},
			10,
			5
		);

		/* خطاهای پایگاه‌داده در پایان هر درخواست (فقط موارد تازه). */
		add_action(
			'shutdown',
			static function () {
				$errors = isset( $GLOBALS['EZSQL_ERROR'] ) && is_array( $GLOBALS['EZSQL_ERROR'] ) ? $GLOBALS['EZSQL_ERROR'] : array();
				$total  = count( $errors );
				$seen   = (int) get_option( 'zc_logs_dberr_seen', 0 );
				if ( $total > $seen ) {
					foreach ( array_slice( $errors, $seen ) as $e ) {
						$q = isset( $e['query'] ) ? (string) $e['query'] : '';
						$m = isset( $e['error_str'] ) ? (string) $e['error_str'] : '';
						zc_logs_runtime_write( 'db', 'DB', $m . ( $q ? ' — query: ' . $q : '' ) );
					}
				}
				if ( $total !== $seen ) {
					update_option( 'zc_logs_dberr_seen', $total, false );
				}
			},
			1
		);
	}
endif;
add_action( 'init', 'zc_logs_runtime_boot', 1 );

/* -------------------------------------------------------------------------
 * ۱) ثبتر منابع
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_registry' ) ) :
	/**
	 * فهرست منابع لاگ شناسایی‌شده.
	 *
	 * @return array<string, array> شناسه => ردیف منبع.
	 */
	function zc_logs_registry() {
		static $reg = null;
		if ( null !== $reg ) {
			return $reg;
		}
		$reg = array();

		/* ۱) debug.log وردپرس. */
		$wplog = '';
		if ( defined( 'WP_DEBUG_LOG' ) ) {
			$wplog = true === WP_DEBUG_LOG ? WP_CONTENT_DIR . '/debug.log' : (string) WP_DEBUG_LOG;
		}
		if ( '' !== $wplog ) {
			$reg['wp_debug'] = array(
				'label'   => __( 'لاگ وردپرس (debug.log)', 'zarincoach' ),
				'kind'    => 'file',
				'path'    => $wplog,
				'origin'  => 'core',
				'clear'   => true,
				'desc'    => __( 'خروجی WP_DEBUG_LOG هسته، افزونه‌ها و قالب — خطاهای PHP، هشدارها و دپریکیشن‌ها.', 'zarincoach' ),
			);
		}

		/* ۲) error_log پی‌اچ‌پی (اگر جزء debug.log نباشد). */
		$ini_log = (string) ini_get( 'error_log' );
		$ini_log = str_replace( '\\', '/', $ini_log );
		if ( '' !== $ini_log && 'syslog' !== $ini_log && 'stderr' !== $ini_log && ! in_array( $ini_log, array_map( 'strval', wp_list_pluck( $reg, 'path' ) ), true ) ) {
			$reg['php_errorlog'] = array(
				'label'   => __( 'لاگ پی‌اچ‌پی (error_log.ini)', 'zarincoach' ),
				'kind'    => 'file',
				'path'    => $ini_log,
				'origin'  => 'host',
				'clear'   => false,
				'desc'    => __( 'مقصد error_log تنظیم‌شده در پی‌اچ‌پی/هاست.', 'zarincoach' ),
			);
		}

		/* ۳) error_logهای رایج کنار نصب. */
		$candidates = array(
			dirname( ABSPATH ) . '/error_log',
			ABSPATH . '/error_log',
			WP_CONTENT_DIR . '/error_log',
		);
		$i = 0;
		foreach ( $candidates as $cand ) {
			$cand = str_replace( '\\', '/', $cand );
			if ( @is_file( $cand ) && ! in_array( $cand, array_map( 'strval', wp_list_pluck( $reg, 'path' ) ), true ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
				++$i;
				$reg[ 'host_' . $i ] = array(
					'label'   => sprintf( /* translators: %s: مسیر */ __( 'لاگ هاست (%s)', 'zarincoach' ), str_replace( str_replace( '\\', '/', ABSPATH ), '', $cand ) ),
					'kind'    => 'file',
					'path'    => $cand,
					'origin'  => 'host',
					'clear'   => false,
					'desc'    => __( 'فایل error_log تولیدی هاست (cPanel/DirectAdmin) در کنار نصب وردپرس.', 'zarincoach' ),
				);
			}
		}

		/* ۴) لاگ دیتابیسی المنتور. */
		if ( class_exists( '\Elementor\Plugin' ) ) {
			$reg['elementor'] = array(
				'label'   => __( 'لاگ داخلی المنتور (پایگاه‌داده)', 'zarincoach' ),
				'kind'    => 'elementor',
				'origin'  => 'plugin',
				'clear'   => true,
				'desc'    => __( 'ورودی‌های لاگر المنتور که در جدول options ذخیره می‌شوند (همان هشدارهایی که پیش‌تر تریاژ شد).', 'zarincoach' ),
			);
		}

		/* ۵) فایل‌های لاگ ووکامرس. */
		if ( class_exists( 'WooCommerce' ) ) {
			$dir = '';
			if ( class_exists( '\Automattic\WooCommerce\Utilities\LoggingUtil' ) && method_exists( '\Automattic\WooCommerce\Utilities\LoggingUtil', 'get_log_directory' ) ) {
				$dir = \Automattic\WooCommerce\Utilities\LoggingUtil::get_log_directory( false );
			}
			if ( '' === $dir || ! is_dir( $dir ) ) {
				$up  = wp_get_upload_dir();
				$dir = trailingslashit( $up['basedir'] ) . 'wc-logs';
			}
			$j = 0;
			foreach ( (array) glob( trailingslashit( $dir ) . '*.log' ) as $file ) {
				$base = basename( (string) $file );
				++$j;
				$reg[ 'wc_' . $j ] = array(
					'label'   => sprintf( /* translators: %s: نام فایل */ __( 'لاگ ووکامرس (%s)', 'zarincoach' ), $base ),
					'kind'    => 'file',
					'path'    => (string) $file,
					'origin'  => 'plugin',
					'clear'   => true,
					'wc_dir'  => (string) $dir,
					'desc'    => __( 'فایل لاگ هندلر فایلی ووکامرس.', 'zarincoach' ),
				);
			}
		}

		/* ۶) لاگ اجرایی زنده‌ی قالب. */
		$rt = zc_logs_runtime_path();
		if ( '' !== $rt ) {
			$reg['runtime'] = array(
				'label'   => __( 'لاگ اجرایی زرین‌کوچ (ایمیل/HTTP/پایگاه‌داده)', 'zarincoach' ),
				'kind'    => 'file',
				'path'    => $rt,
				'origin'  => 'theme',
				'clear'   => true,
				'desc'    => __( 'شکست ایمیل‌ها، خطاهای HTTP API و خطاهای پایگاه‌داده که قالب به‌صورت زنده ثبت می‌کند.', 'zarincoach' ),
			);
		}

		return $reg;
	}
endif;

/* -------------------------------------------------------------------------
 * ۲) خواندن و تریاژ
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_tail' ) ) :
	/**
	 * خواندن N سطر آخر فایل (بهینه؛ حداکثر ۲MB از انتها).
	 *
	 * @param string $path       مسیر.
	 * @param int    $lines      تعداد سطر.
	 * @param int    $max_bytes  سقف بایت خواندنی.
	 * @return string[] سطرها (قدیمی→جدید).
	 */
	function zc_logs_tail( $path, $lines = 1000, $max_bytes = 2097152 ) {
		$size = @filesize( $path ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( ! $size || ! @is_readable( $path ) ) { // phpcs:ignore WordPress.PHP.NoSilencedErrors
			return array();
		}
		$fh = @fopen( $path, 'rb' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
		if ( ! $fh ) {
			return array();
		}
		$read = (int) min( $size, $max_bytes );
		fseek( $fh, $size - $read );
		$chunk = (string) fread( $fh, $read );
		fclose( $fh );
		$all = preg_split( '/\r\n|\r|\n/', $chunk );
		if ( $read < $size && ! empty( $all ) ) {
			array_shift( $all ); // سطر ناقص ابتدای چانک.
		}
		$all = array_values( array_filter( array_map( 'trim', $all ), 'strlen' ) );
		return count( $all ) > $lines ? array_slice( $all, -$lines ) : $all;
	}
endif;

if ( ! function_exists( 'zc_logs_source_lines' ) ) :
	/**
	 * سطرهای هر منبع (فایل یا دیتابیسی).
	 *
	 * @param string $id    شناسه منبع.
	 * @param int    $lines تعداد سطر.
	 * @return string[]
	 */
	function zc_logs_source_lines( $id, $lines = 1000 ) {
		$reg = zc_logs_registry();
		if ( ! isset( $reg[ $id ] ) ) {
			return array();
		}
		$src = $reg[ $id ];

		if ( 'file' === $src['kind'] ) {
			return zc_logs_tail( $src['path'], $lines );
		}

		if ( 'elementor' === $src['kind'] ) {
			$data = get_option( 'elementor_log', array() );
			if ( ! is_array( $data ) ) {
				return array();
			}
			$rows = array();
			foreach ( $data as $entry ) {
				/* ورودی‌ها آبجکت Items\Base هستند (jsonSerialize) یا آرایه‌ی ذخیره‌شده. */
				$item = null;
				if ( is_object( $entry ) && method_exists( $entry, 'jsonSerialize' ) ) {
					$ser  = $entry->jsonSerialize();
					$item = isset( $ser['item'] ) && is_array( $ser['item'] ) ? $ser['item'] : null;
				} elseif ( is_array( $entry ) ) {
					$item = isset( $entry['item'] ) && is_array( $entry['item'] ) ? $entry['item'] : $entry;
				}
				if ( ! $item ) {
					continue;
				}
				$date = isset( $item['date'] ) ? (string) $item['date'] : '';
				$type = isset( $item['type'] ) ? strtoupper( (string) $item['type'] ) : 'INFO';
				$msg  = isset( $item['message'] ) ? (string) $item['message'] : '';
				if ( '' === $msg ) {
					continue;
				}
				$rows[] = '[' . $date . '] EL ' . $type . ': ' . $msg;
			}
			usort( $rows, 'strcmp' );
			return count( $rows ) > $lines ? array_slice( $rows, -$lines ) : $rows;
		}

		return array();
	}
endif;

if ( ! function_exists( 'zc_logs_classify' ) ) :
	/**
	 * دسته‌بندی یک سطر لاگ.
	 *
	 * @param string $line سطر خام.
	 * @return array{level:string,comp:string,comp_label:string,ts:string}
	 */
	function zc_logs_classify( $line ) {
		$level = 'info';
		$map   = array(
			'fatal error'      => 'fatal',
			'parse error'      => 'fatal',
			'uncaught'         => 'fatal',
			'critical'         => 'fatal',
			'wordpress database error' => 'db',
			'error'            => 'error',
			'warning'          => 'warning',
			'deprecated'       => 'deprecated',
			'notice'           => 'notice',
		);
		$low = strtolower( $line );
		foreach ( $map as $needle => $lvl ) {
			if ( false !== strpos( $low, $needle ) ) {
				$level = $lvl;
				break;
			}
		}

		/* مبدأ. */
		$comp = 'unknown';
		$comp_label = __( 'نامشخص', 'zarincoach' );
		if ( preg_match( '#wp-content[\\\\/]+(plugins|themes)[\\\\/]+([A-Za-z0-9\.\-_]+)#', $line, $m ) ) {
			$comp = 'plugin:' === substr( $m[1], 0, 7 ) ? '' : '';
			if ( 'plugins' === $m[1] ) {
				$comp = 'plugin:' . strtolower( $m[2] );
				$comp_label = sprintf( /* translators: %s: نام افزونه */ __( 'افزونه: %s', 'zarincoach' ), $m[2] );
			} else {
				$comp = 'theme:' . strtolower( $m[2] );
				$comp_label = sprintf( /* translators: %s: نام قالب */ __( 'قالب: %s', 'zarincoach' ), $m[2] );
			}
		} elseif ( preg_match( '#wp-(includes|admin)#', $line ) ) {
			$comp = 'core';
			$comp_label = __( 'هسته‌ی وردپرس', 'zarincoach' );
		} elseif ( false !== strpos( $line, ' EL ' ) || false !== strpos( $line, 'Elementor' ) ) {
			$comp = 'plugin:elementor';
			$comp_label = __( 'افزونه: elementor', 'zarincoach' );
		} elseif ( false !== strpos( $line, 'ZC ' ) ) {
			$comp = 'theme:runtime';
			$comp_label = __( 'قالب: لاگ اجرایی', 'zarincoach' );
		}

		$ts = '';
		if ( preg_match( '/^\[([^\]]+)\]/', $line, $t ) ) {
			$ts = $t[1];
		}

		return array(
			'level'      => $level,
			'comp'       => $comp,
			'comp_label' => $comp_label,
			'ts'         => $ts,
		);
	}
endif;

if ( ! function_exists( 'zc_logs_triage_key' ) ) :
	/**
	 * کلید گروه‌بندی (تریاژ): بدون تاریخ، بدون شماره‌ی سطر.
	 *
	 * @param string $line سطر.
	 * @return string
	 */
	function zc_logs_triage_key( $line ) {
		$k = preg_replace( '/^\[[^\]]*\]\s*/', '', $line );
		$k = preg_replace( '/ on line \d+/', ' on line N', (string) $k );
		return (string) $k;
	}
endif;

/* -------------------------------------------------------------------------
 * ۳) رندر
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_size_human' ) ) :
	/**
	 * اندازه‌ی خوانا.
	 *
	 * @param int $bytes بایت.
	 * @return string
	 */
	function zc_logs_size_human( $bytes ) {
		$bytes = (int) $bytes;
		if ( $bytes < 1024 ) {
			return $bytes . ' B';
		}
		if ( $bytes < 1048576 ) {
			return round( $bytes / 1024, 1 ) . ' KB';
		}
		return round( $bytes / 1048576, 2 ) . ' MB';
	}
endif;

if ( ! function_exists( 'zc_logs_source_stats' ) ) :
	/**
	 * آمار منبع روی دمِ آخر.
	 *
	 * @param string $id شناسه.
	 * @return array{lines:int,levels:array<string,int>,exists:bool,size:string,mtime:int}
	 */
	function zc_logs_source_stats( $id ) {
		$reg  = zc_logs_registry();
		$src  = isset( $reg[ $id ] ) ? $reg[ $id ] : array();
		$out  = array( 'lines' => 0, 'levels' => array(), 'exists' => false, 'size' => '—', 'mtime' => 0 );
		if ( ! $src ) {
			return $out;
		}
		if ( 'file' === $src['kind'] ) {
			$out['exists'] = @is_file( $src['path'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
			$out['size']   = $out['exists'] ? zc_logs_size_human( (int) @filesize( $src['path'] ) ) : '—'; // phpcs:ignore WordPress.PHP.NoSilencedErrors
			$out['mtime']  = $out['exists'] ? (int) @filemtime( $src['path'] ) : 0; // phpcs:ignore WordPress.PHP.NoSilencedErrors
		} else {
			$out['exists'] = true;
			$data = get_option( 'elementor_log', array() );
			$out['size'] = is_array( $data ) ? zc_logs_size_human( strlen( wp_json_encode( $data ) ) ) : '—';
		}
		if ( ! $out['exists'] ) {
			return $out;
		}
		$lines = zc_logs_source_lines( $id, 1000 );
		$out['lines'] = count( $lines );
		foreach ( $lines as $l ) {
			$c = zc_logs_classify( $l );
			$out['levels'][ $c['level'] ] = ( isset( $out['levels'][ $c['level'] ] ) ? $out['levels'][ $c['level'] ] : 0 ) + 1;
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_logs_render_view' ) ) :
	/**
	 * بلوک نمایش یک منبع (HTML خالص؛ فراخوان با esc داخلی).
	 *
	 * @param string $id   شناسه.
	 * @param array  $args آرگومان‌ها: lines, level, comp, q.
	 * @return string
	 */
	function zc_logs_render_view( $id, $args = array() ) {
		$reg = zc_logs_registry();
		if ( ! isset( $reg[ $id ] ) ) {
			return '<p>' . esc_html__( 'منبع نامعتبر است.', 'zarincoach' ) . '</p>';
		}
		$src   = $reg[ $id ];
		$lines = max( 100, min( 5000, isset( $args['lines'] ) ? (int) $args['lines'] : 1000 ) );
		$flevel = isset( $args['level'] ) && '' !== $args['level'] ? (string) $args['level'] : '';
		$fcomp  = isset( $args['comp'] ) && '' !== $args['comp'] ? (string) $args['comp'] : '';
		$fq     = isset( $args['q'] ) ? trim( (string) $args['q'] ) : '';

		$all = zc_logs_source_lines( $id, $lines );
		$class = array();
		foreach ( $all as $l ) {
			$class[] = zc_logs_classify( $l );
		}

		/* فیلتر. */
		$shown = array();
		foreach ( $all as $i => $l ) {
			if ( '' !== $flevel && $class[ $i ]['level'] !== $flevel ) {
				continue;
			}
			if ( '' !== $fcomp && $class[ $i ]['comp'] !== $fcomp ) {
				continue;
			}
			if ( '' !== $fq && false === stripos( $l, $fq ) ) {
				continue;
			}
			$shown[] = $i;
		}

		/* تریاژ روی سطرهای نمایش‌داده‌شده. */
		$groups = array();
		foreach ( $shown as $i ) {
			$key = zc_logs_triage_key( $all[ $i ] );
			if ( ! isset( $groups[ $key ] ) ) {
				$groups[ $key ] = array( 'count' => 0, 'first' => $class[ $i ]['ts'], 'last' => $class[ $i ]['ts'], 'level' => $class[ $i ]['level'], 'comp_label' => $class[ $i ]['comp_label'], 'sample' => $all[ $i ] );
			}
			++$groups[ $key ]['count'];
			if ( '' !== $class[ $i ]['ts'] ) {
				$groups[ $key ]['last'] = $class[ $i ]['ts'];
			}
		}
		usort(
			$groups,
			static function ( $a, $b ) {
				return $b['count'] <=> $a['count'];
			}
		);
		$top = array_slice( $groups, 0, 40 );

		/* چیپ‌های سطح. */
		$levels_count = array();
		foreach ( $class as $c ) {
			$levels_count[ $c['level'] ] = ( isset( $levels_count[ $c['level'] ] ) ? $levels_count[ $c['level'] ] : 0 ) + 1;
		}
		$level_names = array(
			'fatal'      => __( 'خطای مرگبار', 'zarincoach' ),
			'error'      => __( 'خطا', 'zarincoach' ),
			'db'         => __( 'خطای پایگاه‌داده', 'zarincoach' ),
			'warning'    => __( 'هشدار', 'zarincoach' ),
			'notice'     => __( 'اعلان', 'zarincoach' ),
			'deprecated'  => __( 'دپریکیشن', 'zarincoach' ),
			'info'       => __( 'اطلاع', 'zarincoach' ),
		);
		$level_marks = array( 'fatal' => 'danger', 'error' => 'danger', 'db' => 'danger', 'warning' => 'warn', 'notice' => 'info', 'deprecated' => 'info', 'info' => 'ok' );

		ob_start();
		?>
		<div class="zc-logs-view" data-zc-logs-src="<?php echo esc_attr( $id ); ?>">
			<div class="zc-logs-chips">
				<span class="zc-logs-chip is-ok"><?php echo esc_html( sprintf( /* translators: %s: عدد */ __( '%s سطر آخر', 'zarincoach' ), number_format_i18n( count( $all ) ) ) ); ?></span>
				<?php
				foreach ( $level_names as $lvl => $lbl ) :
					$n = isset( $levels_count[ $lvl ] ) ? $levels_count[ $lvl ] : 0;
					if ( ! $n ) {
						continue;
					}
					?>
					<span class="zc-logs-chip is-<?php echo esc_attr( isset( $level_marks[ $lvl ] ) ? $level_marks[ $lvl ] : 'info' ); ?>"><?php echo esc_html( $lbl . ': ' . number_format_i18n( $n ) ); ?></span>
				<?php endforeach; ?>
			</div>

			<?php if ( ! $shown ) : ?>
				<p class="zc-logs-empty"><?php esc_html_e( 'با این فیلترها موردی یافت نشد — یا لاگ خالی است. 🎉', 'zarincoach' ); ?></p>
			<?php endif; ?>

			<?php if ( $top ) : ?>
			<h4 class="zc-logs-sub"><?php esc_html_e( 'تریاژ: موارد تکراری گروه‌شده', 'zarincoach' ); ?></h4>
			<table class="zc-logs-table">
				<thead>
					<tr><th><?php esc_html_e( 'تعداد', 'zarincoach' ); ?></th><th><?php esc_html_e( 'سطح', 'zarincoach' ); ?></th><th><?php esc_html_e( 'مبدأ', 'zarincoach' ); ?></th><th><?php esc_html_e( 'آخرین', 'zarincoach' ); ?></th><th><?php esc_html_e( 'نمونه‌ی پیام', 'zarincoach' ); ?></th></tr>
				</thead>
				<tbody>
				<?php foreach ( $top as $g ) : ?>
					<tr>
						<td class="zc-logs-num"><?php echo esc_html( number_format_i18n( $g['count'] ) ); ?>×</td>
						<td><span class="zc-logs-tag is-<?php echo esc_attr( $level_marks[ $g['level'] ] ?? 'info' ); ?>"><?php echo esc_html( $level_names[ $g['level'] ] ?? $g['level'] ); ?></span></td>
						<td class="zc-logs-comp"><?php echo esc_html( $g['comp_label'] ); ?></td>
						<td class="zc-logs-time" dir="ltr"><?php echo esc_html( $g['last'] ); ?></td>
						<td class="zc-logs-sample" dir="ltr" title="<?php echo esc_attr( $g['sample'] ); ?>"><?php echo esc_html( wp_html_excerpt( $g['sample'], 160, '…' ) ); ?></td>
					</tr>
				<?php endforeach; ?>
				</tbody>
			</table>
			<?php endif; ?>

			<?php if ( $shown ) : ?>
			<details class="zc-logs-raw">
				<summary><?php echo esc_html( sprintf( /* translators: %s: عدد */ __( 'نمایش خام (%s سطر آخر)', 'zarincoach' ), number_format_i18n( min( 200, count( $shown ) ) ) ) ); ?></summary>
				<pre dir="ltr"><?php
				foreach ( array_slice( array_reverse( $shown ), 0, 200 ) as $i ) { // newest first.
					echo esc_html( $all[ $i ] ) . "\n";
				}
				?></pre>
			</details>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}
endif;

/* -------------------------------------------------------------------------
 * ۴) AJAX
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_ajax' ) ) :
	/**
	 * کنترل‌کننده‌ی AJAX مرکز لاگ.
	 *
	 * @return void
	 */
	function zc_logs_ajax() {
		check_ajax_referer( 'zc_logs_center', 'nonce' );
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'مجوز کافی ندارید.', 'zarincoach' ) ), 403 );
		}
		$do = isset( $_POST['do'] ) ? sanitize_key( wp_unslash( $_POST['do'] ) ) : 'state';

		if ( 'state' === $do ) {
			$reg = zc_logs_registry();
			$sources = array();
			foreach ( $reg as $id => $src ) {
				$st = zc_logs_source_stats( $id );
				$sources[] = array(
					'id'     => $id,
					'label'  => $src['label'],
					'desc'   => $src['desc'],
					'size'   => $st['size'],
					'mtime'  => $st['mtime'] ? wp_date( 'Y-m-d H:i', $st['mtime'] ) : '',
					'levels' => $st['levels'],
					'exists' => $st['exists'],
					'clear'  => (bool) $src['clear'],
				);
			}
			wp_send_json_success( array( 'sources' => $sources ) );
		}

		if ( 'view' === $do ) {
			$id   = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
			$args = array(
				'lines' => isset( $_POST['lines'] ) ? absint( $_POST['lines'] ) : 1000,
				'level' => isset( $_POST['level'] ) ? sanitize_key( wp_unslash( $_POST['level'] ) ) : '',
				'comp'  => isset( $_POST['comp'] ) ? sanitize_text_field( wp_unslash( $_POST['comp'] ) ) : '',
				'q'     => isset( $_POST['q'] ) ? sanitize_text_field( wp_unslash( $_POST['q'] ) ) : '',
			);
			wp_send_json_success( array( 'html' => zc_logs_render_view( $id, $args ) ) );
		}

		if ( 'clear' === $do ) {
			$id  = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
			$reg = zc_logs_registry();
			if ( ! isset( $reg[ $id ] ) || empty( $reg[ $id ]['clear'] ) ) {
				wp_send_json_error( array( 'message' => __( 'این منبع قابل پاک‌کردن نیست.', 'zarincoach' ) ) );
			}
			$src = $reg[ $id ];
			$ok  = false;
			if ( 'file' === $src['kind'] ) {
				/* فقط فایلِ ثبت‌شده؛ برای ووکامرس باید داخل پوشه‌ی لاگ باشد. */
				$safe = @is_file( $src['path'] ); // phpcs:ignore WordPress.PHP.NoSilencedErrors
				if ( isset( $src['wc_dir'] ) ) {
					$safe = $safe && 0 === strpos( str_replace( '\\', '/', $src['path'] ), str_replace( '\\', '/', trailingslashit( $src['wc_dir'] ) ) );
				}
				if ( $safe ) {
					$ok = false !== @file_put_contents( $src['path'], '' ); // phpcs:ignore WordPress.WP.AlternativeFunctions, WordPress.PHP.NoSilencedErrors
				}
			} elseif ( 'elementor' === $src['kind'] ) {
				$ok = delete_option( 'elementor_log' );
				if ( ! $ok ) {
					$ok = true; // خالی بوده.
				}
			}
			wp_send_json_success( array( 'message' => $ok ? __( 'منبع لاگ پاک شد.', 'zarincoach' ) : __( 'پاک‌سازی ناموفق بود (مجوز نوشتن؟).', 'zarincoach' ) ) );
		}

		if ( 'download' === $do ) {
			$id    = isset( $_POST['source'] ) ? sanitize_key( wp_unslash( $_POST['source'] ) ) : '';
			$reg   = zc_logs_registry();
			nocache_headers();
			header( 'Content-Type: text/plain; charset=utf-8' );
			if ( '' === $id || 'all' === $id ) {
				header( 'Content-Disposition: attachment; filename=zarincoach-logs-' . current_time( 'Ymd-Hi' ) . '.txt' );
				echo '# ' . esc_html__( 'گزارش کامل لاگ سیستم — زرین‌کوچ', 'zarincoach' ) . "\n";
				echo '# site: ' . esc_html( home_url() ) . ' | WP ' . esc_html( get_bloginfo( 'version' ) ) . ' | PHP ' . esc_html( PHP_VERSION ) . ' | theme ' . esc_html( (string) ( defined( 'ZC_VERSION' ) ? ZC_VERSION : '' ) ) . ' | ' . esc_html( current_time( 'Y-m-d H:i:s' ) ) . "\n";
				foreach ( $reg as $sid => $src ) {
					$lines = zc_logs_source_lines( $sid, 2000 );
					if ( ! $lines ) {
						continue;
					}
					echo "\n===== [" . esc_html( $src['label'] ) . "] =====\n";
					echo implode( "\n", array_map( 'esc_html', $lines ) ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- پیش‌نمایش متنی برای دانلود؛ esc برای ایمنی خروجی.
				}
			} elseif ( isset( $reg[ $id ] ) ) {
				$lines = zc_logs_source_lines( $id, 5000 );
				header( 'Content-Disposition: attachment; filename=zarincoach-log-' . $id . '-' . current_time( 'Ymd-Hi' ) . '.txt' );
				echo '# ' . esc_html( $reg[ $id ]['label'] ) . "\n";
				echo implode( "\n", array_map( 'esc_html', $lines ) ) . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			} else {
				status_header( 400 );
				echo 'invalid source';
			}
			exit;
		}

		wp_send_json_error( array( 'message' => __( 'درخواست نامعتبر.', 'zarincoach' ) ) );
	}
endif;
add_action( 'wp_ajax_zc_logs_center', 'zc_logs_ajax' );

/* -------------------------------------------------------------------------
 * ۵) کارت در صفحه‌ی اطلاعات سیستم
 * ---------------------------------------------------------------------- */

if ( ! function_exists( 'zc_logs_system_assets' ) ) :
	/**
	 * پیکربندی جاوااسکریپت کارت لاگ.
	 *
	 * @param string $hook هوک صفحه.
	 * @return void
	 */
	function zc_logs_system_assets( $hook ) {
		if ( false === strpos( (string) $hook, 'zc-system-info' ) ) {
			return;
		}
		$data = array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'nonce' => wp_create_nonce( 'zc_logs_center' ),
			'i18n'  => array(
				'loading' => __( 'در حال بارگذاری…', 'zarincoach' ),
				'fail'    => __( 'عملیات ناموفق بود.', 'zarincoach' ),
				'confirm' => __( 'این منبع لاگ پاک شود؟ این کار بازگشت‌پذیر نیست.', 'zarincoach' ),
				'cleared' => __( 'انجام شد.', 'zarincoach' ),
			),
		);
		wp_register_script( 'zc-logs-center', '', array(), (string) ( defined( 'ZC_VERSION' ) ? ZC_VERSION : '2.8.0' ), true );
		wp_enqueue_script( 'zc-logs-center' );
		wp_add_inline_script( 'zc-logs-center', 'window.zcLogsCenter = ' . wp_json_encode( $data ) . ';' );
	}
endif;
add_action( 'admin_enqueue_scripts', 'zc_logs_system_assets' );

if ( ! function_exists( 'zc_logs_system_card' ) ) :
	/**
	 * HTML کارت مرکز لاگ.
	 *
	 * @return void
	 */
	function zc_logs_system_card() {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		$reg   = zc_logs_registry();
		$first = '';
		$chips = array();
		foreach ( $reg as $id => $src ) {
			$st = zc_logs_source_stats( $id );
			if ( ! $st['exists'] ) {
				continue;
			}
			if ( '' === $first ) {
				$first = $id;
			}
			$bad  = ( isset( $st['levels']['fatal'] ) ? $st['levels']['fatal'] : 0 ) + ( isset( $st['levels']['error'] ) ? $st['levels']['error'] : 0 ) + ( isset( $st['levels']['db'] ) ? $st['levels']['db'] : 0 );
			$warn = isset( $st['levels']['warning'] ) ? $st['levels']['warning'] : 0;
			$chips[] = array(
				'id'   => $id,
				'lbl'  => $src['label'],
				'size' => $st['size'],
				'bad'  => $bad,
				'warn' => $warn,
			);
		}
		if ( ! $chips ) {
			return;
		}
		?>
		<div class="zc-logs-card" id="zc-logs-center">
			<div class="zc-logs-card__head">
				<h2><span class="dashicons dashicons-list-view"></span> <?php esc_html_e( 'مرکز لاگ سیستم', 'zarincoach' ); ?></h2>
				<p><?php esc_html_e( 'جمع‌آوری و تریاژ خودکار لاگ از همه‌ی منابع: وردپرس، پی‌اچ‌پی و هاست، المنتور (پایگاه‌داده)، ووکامرس و لاگ اجرایی زنده‌ی قالب (ایمیل/HTTP/پایگاه‌داده). فیلتر کنید، گروه‌های تکراری را ببینید و برای ارسال به پشتیبانی دانلود کنید.', 'zarincoach' ); ?></p>
			</div>

			<div class="zc-logs-tabs" id="zc-logs-tabs">
				<?php foreach ( $chips as $c ) : ?>
					<button type="button" class="zc-logs-tab<?php echo $c['id'] === $first ? ' is-active' : ''; ?>" data-zc-logs-src="<?php echo esc_attr( $c['id'] ); ?>">
						<span class="zc-logs-tab__lbl"><?php echo esc_html( $c['lbl'] ); ?></span>
						<span class="zc-logs-tab__meta"><?php echo esc_html( $c['size'] ); ?></span>
						<?php if ( $c['bad'] ) : ?><span class="zc-logs-badge is-bad"><?php echo esc_html( number_format_i18n( $c['bad'] ) ); ?></span><?php endif; ?>
						<?php if ( $c['warn'] ) : ?><span class="zc-logs-badge is-warn"><?php echo esc_html( number_format_i18n( $c['warn'] ) ); ?></span><?php endif; ?>
					</button>
				<?php endforeach; ?>
			</div>

			<div class="zc-logs-toolbar">
				<select id="zc-logs-level">
					<option value=""><?php esc_html_e( 'همه‌ی سطوح', 'zarincoach' ); ?></option>
					<option value="fatal"><?php esc_html_e( 'خطای مرگبار', 'zarincoach' ); ?></option>
					<option value="error"><?php esc_html_e( 'خطا', 'zarincoach' ); ?></option>
					<option value="db"><?php esc_html_e( 'خطای پایگاه‌داده', 'zarincoach' ); ?></option>
					<option value="warning"><?php esc_html_e( 'هشدار', 'zarincoach' ); ?></option>
					<option value="notice"><?php esc_html_e( 'اعلان', 'zarincoach' ); ?></option>
					<option value="deprecated"><?php esc_html_e( 'دپریکیشن', 'zarincoach' ); ?></option>
				</select>
				<select id="zc-logs-lines">
					<option value="300"><?php esc_html_e( '۳۰۰ سطر آخر', 'zarincoach' ); ?></option>
					<option value="1000" selected><?php esc_html_e( '۱۰۰۰ سطر آخر', 'zarincoach' ); ?></option>
					<option value="2000"><?php esc_html_e( '۲۰۰۰ سطر آخر', 'zarincoach' ); ?></option>
					<option value="5000"><?php esc_html_e( '۵۰۰۰ سطر آخر', 'zarincoach' ); ?></option>
				</select>
				<input type="search" id="zc-logs-q" placeholder="<?php esc_attr_e( 'جست‌وجو در لاگ… (مثلاً نام افزونه یا متن خطا)', 'zarincoach' ); ?>">
				<button type="button" class="button" id="zc-logs-refresh"><?php esc_html_e( 'به‌روزرسانی', 'zarincoach' ); ?></button>
				<button type="button" class="button" id="zc-logs-download"><?php esc_html_e( 'دانلود این منبع', 'zarincoach' ); ?></button>
				<button type="button" class="button button-primary" id="zc-logs-download-all"><?php esc_html_e( 'دانلود گزارش کامل', 'zarincoach' ); ?></button>
				<button type="button" class="button-link-delete" id="zc-logs-clear"><?php esc_html_e( 'پاک‌کردن', 'zarincoach' ); ?></button>
			</div>

			<div id="zc-logs-view" class="zc-logs-viewbox" aria-live="polite"></div>

			<style>
				.zc-logs-card { margin: 20px 0; padding: 20px 24px; border: 1px solid #dcdfe6; border-radius: 12px; background: #fff; }
				.zc-logs-card__head h2 { display: flex; align-items: center; gap: 8px; margin: 0 0 6px; font-size: 16px; color: #1D3A72; }
				.zc-logs-card__head p { margin: 0 0 14px; color: #5a6270; font-size: 13px; }
				.zc-logs-tabs { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 12px; }
				.zc-logs-tab { display: inline-flex; align-items: center; gap: 6px; padding: 7px 12px; border: 1px solid #d5dae3; border-radius: 8px; background: #f7f8fb; cursor: pointer; font-size: 12.5px; }
				.zc-logs-tab.is-active { border-color: #1D3A72; background: #1D3A72; color: #fff; }
				.zc-logs-tab__lbl { font-weight: 600; }
				.zc-logs-tab__meta { opacity: .75; font-size: 11px; }
				.zc-logs-badge { min-width: 20px; text-align: center; border-radius: 10px; padding: 1px 7px; font-size: 11px; font-weight: 700; color: #fff; }
				.zc-logs-badge.is-bad { background: #d63638; }
				.zc-logs-badge.is-warn { background: #d98a00; }
				.zc-logs-toolbar { display: flex; flex-wrap: wrap; gap: 8px; align-items: center; margin-bottom: 12px; }
				.zc-logs-toolbar input[type="search"] { min-width: 260px; }
				.zc-logs-viewbox { border: 1px solid #eceef3; border-radius: 10px; padding: 14px 16px; background: #fbfcfe; min-height: 60px; }
				.zc-logs-chips { display: flex; flex-wrap: wrap; gap: 6px; margin-bottom: 10px; }
				.zc-logs-chip { padding: 3px 10px; border-radius: 20px; font-size: 11.5px; background: #eef1f6; color: #39415a; }
				.zc-logs-chip.is-ok { background: #e5f5ee; color: #157a52; }
				.zc-logs-chip.is-warn { background: #fdf1dc; color: #a06a00; }
				.zc-logs-chip.is-danger { background: #fbeaea; color: #b32d2e; }
				.zc-logs-chip.is-info { background: #e8eefc; color: #1d4ed8; }
				.zc-logs-sub { margin: 10px 0 6px; font-size: 13px; }
				.zc-logs-table { width: 100%; border-spacing: 0; border-collapse: collapse; }
				.zc-logs-table th { text-align: right; font-size: 11.5px; color: #6b7280; padding: 6px 8px; border-bottom: 2px solid #e5e8ef; }
				.zc-logs-table td { font-size: 12.5px; padding: 8px; border-bottom: 1px solid #f0f1f4; vertical-align: top; }
				.zc-logs-num { font-weight: 700; color: #1D3A72; white-space: nowrap; }
				.zc-logs-comp { white-space: nowrap; }
				.zc-logs-time { white-space: nowrap; color: #6b7280; }
				.zc-logs-sample { word-break: break-all; direction: ltr; text-align: left; }
				.zc-logs-tag { padding: 2px 8px; border-radius: 12px; font-size: 11px; color: #fff; background: #6b7280; display: inline-block; }
				.zc-logs-tag.is-danger { background: #d63638; }
				.zc-logs-tag.is-warn { background: #d98a00; }
				.zc-logs-tag.is-ok { background: #1a9e6b; }
				.zc-logs-tag.is-info { background: #4f6bed; }
				.zc-logs-raw summary { cursor: pointer; font-size: 12.5px; color: #1D3A72; margin: 10px 0 6px; }
				.zc-logs-raw pre { max-height: 340px; overflow: auto; background: #10162a; color: #cfe0ff; border-radius: 8px; padding: 12px 14px; font-size: 11.5px; line-height: 1.7; }
				.zc-logs-empty { color: #157a52; font-weight: 600; }
			</style>
			<script>
			function zcLogsInit() {
				var cfg = window.zcLogsCenter || {}, box = document.getElementById('zc-logs-center');
				if (!box || !cfg.ajax) { return; }
				var cur = (document.querySelector('.zc-logs-tab.is-active') || {}).getAttribute ? document.querySelector('.zc-logs-tab.is-active').getAttribute('data-zc-logs-src') : '';
				var view = document.getElementById('zc-logs-view');
				function fetchView() {
					if (!cur) { view.innerHTML = ''; return; }
					view.innerHTML = '<p>' + cfg.i18n.loading + '</p>';
					var fd = new FormData();
					fd.append('action', 'zc_logs_center'); fd.append('nonce', cfg.nonce); fd.append('do', 'view');
					fd.append('source', cur);
					fd.append('lines', document.getElementById('zc-logs-lines').value);
					fd.append('level', document.getElementById('zc-logs-level').value);
					fd.append('q', document.getElementById('zc-logs-q').value);
					fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
						.then(function (r) { return r.json(); })
						.then(function (j) { if (j && j.success) { view.innerHTML = j.data.html; } else { view.innerHTML = '<p>' + cfg.i18n.fail + '</p>'; } })
						['catch'](function () { view.innerHTML = '<p>' + cfg.i18n.fail + '</p>'; });
				}
				box.addEventListener('click', function (e) {
					var t = e.target.closest('.zc-logs-tab');
					if (t) {
						box.querySelectorAll('.zc-logs-tab').forEach(function (b) { b.classList.remove('is-active'); });
						t.classList.add('is-active');
						cur = t.getAttribute('data-zc-logs-src');
						fetchView();
					}
				});
				document.getElementById('zc-logs-refresh').addEventListener('click', fetchView);
				document.getElementById('zc-logs-level').addEventListener('change', fetchView);
				document.getElementById('zc-logs-lines').addEventListener('change', fetchView);
				var qTimer;
				document.getElementById('zc-logs-q').addEventListener('input', function () { clearTimeout(qTimer); qTimer = setTimeout(fetchView, 500); });
				document.getElementById('zc-logs-download').addEventListener('click', function () {
					if (!cur) { return; }
					var fd = new FormData();
					fd.append('action', 'zc_logs_center'); fd.append('nonce', cfg.nonce); fd.append('do', 'download'); fd.append('source', cur);
					fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
						.then(function (r) { return r.blob(); })
						.then(function (b) { var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'zarincoach-log-' + cur + '.txt'; a.click(); URL.revokeObjectURL(u); });
				});
				document.getElementById('zc-logs-download-all').addEventListener('click', function () {
					var fd = new FormData();
					fd.append('action', 'zc_logs_center'); fd.append('nonce', cfg.nonce); fd.append('do', 'download'); fd.append('source', 'all');
					fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
						.then(function (r) { return r.blob(); })
						.then(function (b) { var u = URL.createObjectURL(b); var a = document.createElement('a'); a.href = u; a.download = 'zarincoach-logs-all.txt'; a.click(); URL.revokeObjectURL(u); });
				});
				document.getElementById('zc-logs-clear').addEventListener('click', function () {
					if (!cur || !window.confirm(cfg.i18n.confirm)) { return; }
					var fd = new FormData();
					fd.append('action', 'zc_logs_center'); fd.append('nonce', cfg.nonce); fd.append('do', 'clear'); fd.append('source', cur);
					fetch(cfg.ajax, { method: 'POST', credentials: 'same-origin', body: fd })
						.then(function (r) { return r.json(); })
						.then(function (j) { window.alert(j && j.data ? j.data.message : cfg.i18n.fail); fetchView(); })
						['catch'](function () { window.alert(cfg.i18n.fail); });
				});
				fetchView();
			}
			if (window.zcLogsCenter && document.getElementById('zc-logs-center')) { zcLogsInit(); }
			else if (document.readyState === 'loading') { document.addEventListener('DOMContentLoaded', zcLogsInit); }
			</script>
		</div>
		<?php
	}
endif;
add_action( 'zc_system_page_after', 'zc_logs_system_card', 20 );
