<?php
/**
 * صفحه‌ی مدیریتی «سربرگ و پاورقی»
 *
 * منبع نمایش هر مکان با این اولویت تعیین می‌شود:
 *   ۱. قالب‌ساز المنتور پرو (قالب header/footer با شرط نمایش)
 *   ۲. قالب المنتورِ انتخاب‌شده در همین صفحه (گزینه‌ی header_template / footer_template پنل)
 *   ۳. سربرگ/پاورقی داخلی قالب (از تنظیمات پنل)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_layout_locations' ) ) :
	/**
	 * مکان‌ها.
	 *
	 * @return array<string, array<string, string>>
	 */
	function zc_layout_locations() {
		return array(
			'header' => array(
				'label'    => __( 'سربرگ', 'zarincoach' ),
				'internal' => __( 'سربرگ داخلی قالب', 'zarincoach' ),
				'widget'   => 'zc-site-header',
				'wname'    => __( 'سربرگ سایت', 'zarincoach' ),
				'icon'     => 'fa-solid fa-window-maximize',
			),
			'footer' => array(
				'label'    => __( 'پاورقی', 'zarincoach' ),
				'internal' => __( 'پاورقی داخلی قالب', 'zarincoach' ),
				'widget'   => 'zc-site-footer',
				'wname'    => __( 'پاورقی سایت', 'zarincoach' ),
				'icon'     => 'fa-solid fa-window-maximize fa-flip-vertical',
			),
		);
	}
endif;

if ( ! function_exists( 'zc_layout_pro_templates' ) ) :
	/**
	 * قالب‌های سربرگ/پاورقی قالب‌ساز المنتور پرو که شرط نمایش دارند.
	 *
	 * @param string $location header|footer.
	 * @return array<int, array{id:int,title:string,conditions:array}>
	 */
	function zc_layout_pro_templates( $location ) {
		if ( ! defined( 'ELEMENTOR_PRO_VERSION' ) || ! post_type_exists( 'elementor_library' ) ) {
			return array();
		}
		$ids = get_posts(
			array(
				'post_type'        => 'elementor_library',
				'post_status'      => 'publish',
				'posts_per_page'   => 20,
				'fields'           => 'ids',
				'meta_key'         => '_elementor_template_type', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'       => $location,                  // phpcs:ignore WordPress.DB.SlowDBQuery
				'suppress_filters' => true,
			)
		);
		$out = array();
		foreach ( $ids as $id ) {
			$conditions = get_post_meta( $id, '_elementor_conditions', true );
			if ( empty( $conditions ) || ! is_array( $conditions ) ) {
				continue;
			}
			$out[] = array(
				'id'         => (int) $id,
				'title'      => get_the_title( $id ),
				'conditions' => $conditions,
			);
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_layout_conditions_label' ) ) :
	/**
	 * خلاصه‌ی خوانای شرط‌های نمایش المنتور پرو.
	 *
	 * @param array $conditions شرط‌ها.
	 * @return string
	 */
	function zc_layout_conditions_label( $conditions ) {
		$include = 0;
		$exclude = 0;
		$general = false;
		foreach ( (array) $conditions as $condition ) {
			$condition = (string) $condition;
			if ( 0 === strpos( $condition, 'exclude/' ) ) {
				++$exclude;
				continue;
			}
			++$include;
			if ( 'include/general' === $condition ) {
				$general = true;
			}
		}
		$label = $general ? __( 'کل سایت', 'zarincoach' ) : sprintf( /* translators: %s: تعداد */ __( '%s شرط نمایش سفارشی', 'zarincoach' ), zc_tool_num( $include ) );
		if ( $exclude ) {
			/* translators: %s: تعداد */
			$label .= ' · ' . sprintf( __( '%s استثنا', 'zarincoach' ), zc_tool_num( $exclude ) );
		}
		return $label;
	}
endif;

if ( ! function_exists( 'zc_layout_source' ) ) :
	/**
	 * منبع فعلی نمایش سربرگ/پاورقی.
	 *
	 * @param string $location header|footer.
	 * @return array{type:string,label:string,id:int,tone:string}
	 */
	function zc_layout_source( $location ) {
		$locs = zc_layout_locations();
		$loc  = isset( $locs[ $location ] ) ? $locs[ $location ] : $locs['header'];

		$pro = zc_is_elementor_active() ? zc_layout_pro_templates( $location ) : array();
		if ( ! empty( $pro ) ) {
			return array(
				'type'  => 'pro',
				/* translators: %s: عنوان قالب */
				'label' => sprintf( __( 'قالب‌ساز المنتور پرو: %s', 'zarincoach' ), $pro[0]['title'] ),
				'id'    => $pro[0]['id'],
				'tone'  => 'info',
			);
		}

		$id = (int) zc_opt( $location . '_template', 0 );
		if ( $id > 0 ) {
			$valid = 'elementor_library' === get_post_type( $id ) && 'publish' === get_post_status( $id );
			if ( $valid && zc_is_elementor_active() ) {
				return array(
					'type'  => 'theme',
					/* translators: %s: عنوان قالب */
					'label' => sprintf( __( 'قالب المنتور: %s', 'zarincoach' ), get_the_title( $id ) ),
					'id'    => $id,
					'tone'  => 'ok',
				);
			}
			return array(
				'type'  => 'missing',
				'label' => $valid ? __( 'قالب المنتور (المنتور غیرفعال است)', 'zarincoach' ) : __( 'قالب انتخاب‌شده در دسترس نیست', 'zarincoach' ),
				'id'    => $id,
				'tone'  => 'warn',
			);
		}

		return array(
			'type'  => 'internal',
			'label' => $loc['internal'],
			'id'    => 0,
			'tone'  => 'muted',
		);
	}
endif;

if ( ! function_exists( 'zc_layout_candidates' ) ) :
	/**
	 * قالب‌های قابل انتخاب برای سربرگ/پاورقی (همان فهرست پنل تنظیمات).
	 *
	 * @param array $types نوع‌های کتابخانه.
	 * @param array $status وضعیت‌ها.
	 * @return WP_Post[]
	 */
	function zc_layout_candidates( $types = array( 'section', 'container', 'page' ), $status = array( 'publish' ) ) {
		if ( ! post_type_exists( 'elementor_library' ) ) {
			return array();
		}
		$args = array(
			'post_type'        => 'elementor_library',
			'post_status'      => $status,
			'posts_per_page'   => 100,
			'orderby'          => 'modified',
			'order'            => 'DESC',
			'suppress_filters' => true,
		);
		if ( taxonomy_exists( 'elementor_library_type' ) ) {
			$args['tax_query'] = array( // phpcs:ignore WordPress.DB.SlowDBQuery
				array(
					'taxonomy' => 'elementor_library_type',
					'field'    => 'slug',
					'terms'    => $types,
				),
			);
		}
		return get_posts( $args );
	}
endif;

if ( ! function_exists( 'zc_layout_widgets_in' ) ) :
	/**
	 * نوع ویجت‌های به‌کاررفته در یک قالب المنتور.
	 *
	 * @param int $id شناسه.
	 * @return string[]
	 */
	function zc_layout_widgets_in( $id ) {
		$raw = get_post_meta( (int) $id, '_elementor_data', true );
		if ( empty( $raw ) ) {
			return array();
		}
		if ( is_string( $raw ) ) {
			preg_match_all( '/"widgetType":"([a-z0-9_-]+)"/i', $raw, $m );
			return array_values( array_unique( $m[1] ) );
		}
		$found = array();
		$walk  = static function ( $els ) use ( &$walk, &$found ) {
			foreach ( (array) $els as $el ) {
				if ( ! empty( $el['widgetType'] ) ) {
					$found[] = (string) $el['widgetType'];
				}
				if ( ! empty( $el['elements'] ) ) {
					$walk( $el['elements'] );
				}
			}
		};
		$walk( $raw );
		return array_values( array_unique( $found ) );
	}
endif;

if ( ! function_exists( 'zc_layout_template_type' ) ) :
	/**
	 * نوع قالب کتابخانه با برچسب فارسی.
	 *
	 * @param int $id شناسه.
	 * @return string
	 */
	function zc_layout_template_type( $id ) {
		$type   = (string) get_post_meta( (int) $id, '_elementor_template_type', true );
		$labels = array(
			'section'   => __( 'بخش', 'zarincoach' ),
			'container' => __( 'کانتینر', 'zarincoach' ),
			'page'      => __( 'برگه', 'zarincoach' ),
			'header'    => __( 'سربرگ پرو', 'zarincoach' ),
			'footer'    => __( 'پاورقی پرو', 'zarincoach' ),
			'popup'     => __( 'پاپ‌آپ', 'zarincoach' ),
		);
		return isset( $labels[ $type ] ) ? $labels[ $type ] : ( '' !== $type ? $type : '—' );
	}
endif;

if ( ! function_exists( 'zc_layout_url' ) ) :
	/**
	 * نشانی صفحه‌ی سربرگ و پاورقی.
	 *
	 * @param array  $args پارامترها.
	 * @param string $pane بخش.
	 * @return string
	 */
	function zc_layout_url( $args = array(), $pane = '' ) {
		$url = add_query_arg( array_merge( array( 'page' => 'zc-layout' ), $args ), admin_url( 'admin.php' ) );
		return $url . ( '' !== $pane ? '#zc-pane-' . $pane : '' );
	}
endif;

if ( ! function_exists( 'zc_layout_assign_url' ) ) :
	/**
	 * نشانی اتصال سریع یک قالب به مکان.
	 *
	 * @param string $location مکان.
	 * @param int    $id       شناسه (۰ = داخلی).
	 * @return string
	 */
	function zc_layout_assign_url( $location, $id ) {
		return wp_nonce_url(
			add_query_arg(
				array(
					'action'   => 'zc_layout_save',
					'location' => $location,
					'template' => (int) $id,
				),
				admin_url( 'admin-post.php' )
			),
			'zc_layout_save'
		);
	}
endif;

if ( ! function_exists( 'zc_layout_render_location' ) ) :
	/**
	 * بخش یک مکان (سربرگ یا پاورقی).
	 *
	 * @param string $location   مکان.
	 * @param array  $candidates قالب‌های قابل انتخاب.
	 * @return void
	 */
	function zc_layout_render_location( $location, $candidates ) {
		$locs      = zc_layout_locations();
		$loc       = $locs[ $location ];
		$source    = zc_layout_source( $location );
		$elementor = zc_is_elementor_active();
		$saved     = (int) zc_opt( $location . '_template', 0 );
		$defs      = function_exists( 'zc_elementor_layout_templates' ) ? zc_elementor_layout_templates() : array();
		$default   = get_posts(
			array(
				'post_type'        => 'elementor_library',
				'post_status'      => 'any',
				'posts_per_page'   => 1,
				'fields'           => 'ids',
				'meta_key'         => '_zc_demo_template', // phpcs:ignore WordPress.DB.SlowDBQuery
				'meta_value'       => $location,           // phpcs:ignore WordPress.DB.SlowDBQuery
				'suppress_filters' => true,
			)
		);
		$default   = ! empty( $default ) ? (int) $default[0] : 0;

		$desc = array(
			'pro'      => __( 'قالب‌ساز المنتور پرو یک قالب با شرط نمایش دارد و بر انتخاب این صفحه مقدم است. برای استفاده از انتخاب زیر، شرط‌های آن قالب را در المنتور پرو حذف کنید.', 'zarincoach' ),
			'theme'    => sprintf( /* translators: %s: نام ویجت */ __( 'این قالب در همه‌ی صفحه‌ها نمایش داده می‌شود. برای تغییر لوگو، منو، دکمه‌ها و رنگ‌ها آن را با المنتور ویرایش کنید؛ ویجت «%s» همه‌ی تنظیمات را در اختیارتان می‌گذارد.', 'zarincoach' ), $loc['wname'] ),
			'missing'  => __( 'قالبی که قبلاً انتخاب شده بود حذف، پیش‌نویس یا غیرقابل دسترس شده است؛ فعلاً نسخه‌ی داخلی قالب نمایش داده می‌شود. یک قالب دیگر انتخاب یا قالب پیش‌فرض را بسازید.', 'zarincoach' ),
			'internal' => __( 'نسخه‌ی داخلی قالب نمایش داده می‌شود که از «تنظیمات قالب» قابل تنظیم است. برای طراحی بصری کامل، یک قالب المنتور انتخاب یا قالب پیش‌فرض را بسازید.', 'zarincoach' ),
		);

		$source_id = (int) $source['id'];
		$widgets   = ( $source_id && in_array( $source['type'], array( 'theme', 'pro' ), true ) ) ? zc_layout_widgets_in( $source_id ) : array();
		$modified  = $source_id ? (int) get_post_modified_time( 'U', true, $source_id ) : 0;
		?>
		<div class="zc-loc is-<?php echo esc_attr( $source['type'] ); ?>">
			<div class="zc-loc-visual zc-loc-visual--<?php echo esc_attr( $location ); ?>" aria-hidden="true">
				<span class="zc-wire-head"><i></i><i></i><i></i></span>
				<span class="zc-wire-body"><i></i><i></i><i></i></span>
				<span class="zc-wire-foot"><i></i><i></i></span>
			</div>
			<div class="zc-loc-info">
				<span class="zc-loc-kicker"><?php echo esc_html( sprintf( /* translators: %s: سربرگ/پاورقی */ __( 'منبع فعلی %s', 'zarincoach' ), $loc['label'] ) ); ?></span>
				<h3><?php echo esc_html( $source['label'] ); ?></h3>
				<p><?php echo esc_html( $desc[ $source['type'] ] ); ?></p>
				<div class="zc-loc-meta">
					<?php
					$types = array(
						'pro'      => array( __( 'اولویت ۱ · المنتور پرو', 'zarincoach' ), 'info' ),
						'theme'    => array( __( 'اولویت ۲ · قالب المنتور', 'zarincoach' ), 'ok' ),
						'missing'  => array( __( 'نیاز به بررسی', 'zarincoach' ), 'warn' ),
						'internal' => array( __( 'اولویت ۳ · داخلی', 'zarincoach' ), 'muted' ),
					);
					echo zc_tool_tag( $types[ $source['type'] ][0], $types[ $source['type'] ][1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( 'pro' === $source['type'] ) {
						$pro = zc_layout_pro_templates( $location );
						echo zc_tool_tag( zc_layout_conditions_label( $pro[0]['conditions'] ), 'muted', 'fa-solid fa-filter' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $modified ) {
						/* translators: %s: تاریخ */
						echo zc_tool_tag( sprintf( __( 'آخرین ویرایش: %s', 'zarincoach' ), zc_tool_date( $modified, false ) ), 'muted', 'fa-regular fa-clock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( 'theme' === $source['type'] ) {
						echo in_array( $loc['widget'], $widgets, true )
							? zc_tool_tag( sprintf( /* translators: %s: نام ویجت */ __( 'ویجت «%s»', 'zarincoach' ), $loc['wname'] ), 'gold', 'fa-solid fa-puzzle-piece' ) // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							: zc_tool_tag( __( 'طراحی سفارشی', 'zarincoach' ), 'muted', 'fa-solid fa-puzzle-piece' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div class="zc-loc-actions">
					<?php if ( $source_id && $elementor && in_array( $source['type'], array( 'theme', 'pro' ), true ) ) : ?>
						<a class="zc-btn zc-btn--primary" href="<?php echo esc_url( admin_url( 'post.php?post=' . $source_id . '&action=elementor' ) ); ?>"><i class="fa-brands fa-elementor" aria-hidden="true"></i><?php esc_html_e( 'ویرایش با المنتور', 'zarincoach' ); ?></a>
						<a class="zc-btn zc-btn--ghost" href="<?php echo esc_url( get_permalink( $source_id ) ); ?>" target="_blank" rel="noopener"><i class="fa-regular fa-eye" aria-hidden="true"></i><?php esc_html_e( 'پیش‌نمایش قالب', 'zarincoach' ); ?></a>
					<?php else : ?>
						<a class="zc-btn zc-btn--ghost" href="<?php echo esc_url( admin_url( 'admin.php?page=zc-options' ) ); ?>"><i class="fa-solid fa-sliders" aria-hidden="true"></i><?php echo esc_html( sprintf( /* translators: %s: سربرگ/پاورقی */ __( 'تنظیمات %s داخلی', 'zarincoach' ), $loc['label'] ) ); ?></a>
					<?php endif; ?>
					<a class="zc-btn zc-btn--ghost" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i><?php esc_html_e( 'مشاهده در سایت', 'zarincoach' ); ?></a>
				</div>
			</div>
		</div>

		<?php
		if ( ! $elementor ) {
			echo zc_tool_alert( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				'warn',
				__( 'المنتور فعال نیست.', 'zarincoach' ),
				__( 'بدون المنتور نسخه‌ی داخلی قالب نمایش داده می‌شود. برای طراحی بصری سربرگ و پاورقی با ویجت‌های اختصاصی، المنتور را نصب و فعال کنید.', 'zarincoach' ),
				current_user_can( 'install_plugins' ) ? '<p class="zc-tool-alert-actions"><a class="zc-btn zc-btn--ghost" href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' ) ) . '"><i class="fa-solid fa-plug" aria-hidden="true"></i>' . esc_html__( 'نصب المنتور', 'zarincoach' ) . '</a></p>' : ''
			);
			return;
		}

		zc_tool_heading( sprintf( /* translators: %s: سربرگ/پاورقی */ __( 'قالب %s', 'zarincoach' ), $loc['label'] ), esc_html__( 'اولویت ۲', 'zarincoach' ) );
		?>
		<form class="zc-assign" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="zc_layout_save">
			<input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>">
			<?php wp_nonce_field( 'zc_layout_save' ); ?>
			<label class="zc-assign-label" for="zc-layout-<?php echo esc_attr( $location ); ?>"><?php echo esc_html( sprintf( /* translators: %s: سربرگ/پاورقی */ __( 'قالب المنتور برای %s', 'zarincoach' ), $loc['label'] ) ); ?></label>
			<div class="zc-assign-row">
				<span class="zc-select">
					<select id="zc-layout-<?php echo esc_attr( $location ); ?>" name="template">
						<option value="0"<?php selected( $saved, 0 ); ?>><?php echo esc_html( sprintf( /* translators: %s: نسخه داخلی */ __( '— %s (از تنظیمات پنل) —', 'zarincoach' ), $loc['internal'] ) ); ?></option>
						<?php foreach ( $candidates as $tpl ) : ?>
							<?php
							$has   = in_array( $loc['widget'], zc_layout_widgets_in( $tpl->ID ), true );
							$label = $tpl->post_title ? $tpl->post_title : __( '(بدون عنوان)', 'zarincoach' );
							?>
							<option value="<?php echo esc_attr( (string) $tpl->ID ); ?>"<?php selected( $saved, (int) $tpl->ID ); ?>><?php echo esc_html( $label . ' · #' . $tpl->ID . ( $has ? ' ★' : '' ) ); ?></option>
						<?php endforeach; ?>
						<?php if ( $saved > 0 && ! in_array( $saved, wp_list_pluck( $candidates, 'ID' ), true ) ) : ?>
							<option value="<?php echo esc_attr( (string) $saved ); ?>" selected disabled><?php echo esc_html( sprintf( /* translators: %s: شناسه */ __( 'قالب #%s (در دسترس نیست)', 'zarincoach' ), $saved ) ); ?></option>
						<?php endif; ?>
					</select>
				</span>
				<button type="submit" class="zc-btn zc-btn--primary"><i class="fa-solid fa-check" aria-hidden="true"></i><?php esc_html_e( 'ذخیره', 'zarincoach' ); ?></button>
			</div>
			<p class="zc-assign-help">
				<?php
				/* translators: %s: نام ویجت */
				echo esc_html( sprintf( __( 'قالب‌های «بخش»، «کانتینر» و «برگه» کتابخانه‌ی المنتور نمایش داده می‌شوند. ★ یعنی قالب شامل ویجت «%s» است.', 'zarincoach' ), $loc['wname'] ) );
				?>
			</p>
		</form>

		<?php zc_tool_heading( __( 'ابزارها', 'zarincoach' ) ); ?>
		<div class="zc-tools-grid">
			<?php if ( ! empty( $defs[ $location ] ) ) : ?>
				<form class="zc-tool-card" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>"<?php echo $default ? ' onsubmit="return confirm(\'' . esc_js( __( 'طرح این قالب به حالت پیش‌فرض برمی‌گردد و تغییرات فعلی آن از بین می‌رود. ادامه می‌دهید؟', 'zarincoach' ) ) . '\');"' : ''; ?>>
					<input type="hidden" name="action" value="zc_layout_build">
					<input type="hidden" name="location" value="<?php echo esc_attr( $location ); ?>">
					<?php wp_nonce_field( 'zc_layout_build' ); ?>
					<span class="zc-tool-card-icon" aria-hidden="true"><i class="fa-solid <?php echo $default ? 'fa-arrows-rotate' : 'fa-wand-magic-sparkles'; ?>"></i></span>
					<div>
						<strong><?php echo $default ? esc_html__( 'بازسازی طرح پیش‌فرض', 'zarincoach' ) : esc_html( sprintf( /* translators: %s: سربرگ/پاورقی */ __( 'ساخت %s پیش‌فرض', 'zarincoach' ), $loc['label'] ) ); ?></strong>
						<p>
							<?php
							echo $default
								? esc_html( sprintf( /* translators: %s: عنوان قالب */ __( 'قالب «%s» با طرح اولیه‌ی زرین‌کوچ بازنویسی و به سایت متصل می‌شود.', 'zarincoach' ), get_the_title( $default ) ) )
								: esc_html( sprintf( /* translators: %s: نام ویجت */ __( 'یک قالب با ویجت «%s» و تنظیمات پیشنهادی در کتابخانه‌ی المنتور ساخته و به سایت متصل می‌شود.', 'zarincoach' ), $loc['wname'] ) );
							?>
						</p>
						<button type="submit" class="zc-btn zc-btn--ghost zc-btn--sm"><?php echo $default ? esc_html__( 'بازسازی', 'zarincoach' ) : esc_html__( 'ساخت و اتصال', 'zarincoach' ); ?></button>
					</div>
				</form>
			<?php endif; ?>
			<?php if ( $saved > 0 ) : ?>
				<div class="zc-tool-card">
					<span class="zc-tool-card-icon" aria-hidden="true"><i class="fa-solid fa-rotate-left"></i></span>
					<div>
						<strong><?php echo esc_html( sprintf( /* translators: %s: نسخه داخلی */ __( 'بازگشت به %s', 'zarincoach' ), $loc['internal'] ) ); ?></strong>
						<p><?php esc_html_e( 'اتصال قالب المنتور برداشته می‌شود؛ خودِ قالب در کتابخانه باقی می‌ماند.', 'zarincoach' ); ?></p>
						<a class="zc-btn zc-btn--ghost zc-btn--sm" href="<?php echo esc_url( zc_layout_assign_url( $location, 0 ) ); ?>"><?php esc_html_e( 'استفاده از نسخه‌ی داخلی', 'zarincoach' ); ?></a>
					</div>
				</div>
			<?php endif; ?>
			<div class="zc-tool-card">
				<span class="zc-tool-card-icon" aria-hidden="true"><i class="fa-solid fa-plus"></i></span>
				<div>
					<strong><?php esc_html_e( 'طراحی قالب تازه', 'zarincoach' ); ?></strong>
					<p>
						<?php
						/* translators: %s: نام ویجت */
						echo esc_html( sprintf( __( 'در کتابخانه‌ی المنتور یک «بخش» بسازید، ویجت «%s» را از دسته‌ی زرین‌کوچ اضافه کنید و سپس آن را همین‌جا انتخاب کنید.', 'zarincoach' ), $loc['wname'] ) );
						?>
					</p>
					<a class="zc-btn zc-btn--ghost zc-btn--sm" href="<?php echo esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library#add_new' ) ); ?>"><?php esc_html_e( 'قالب جدید در المنتور', 'zarincoach' ); ?></a>
				</div>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_render_layout_page' ) ) :
	/**
	 * صفحه‌ی سربرگ و پاورقی.
	 *
	 * @return void
	 */
	function zc_render_layout_page() {
		$elementor  = zc_is_elementor_active();
		$candidates = $elementor ? zc_layout_candidates() : array();
		$notice     = isset( $_GET['zc_layout'] ) ? sanitize_key( wp_unslash( $_GET['zc_layout'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$locs       = zc_layout_locations();
		$src_h      = zc_layout_source( 'header' );
		$src_f      = zc_layout_source( 'footer' );
		$tones      = array(
			'pro'      => 'info',
			'theme'    => 'ok',
			'missing'  => 'warn',
			'internal' => 'muted',
		);
		$short      = array(
			'pro'      => __( 'پرو', 'zarincoach' ),
			'theme'    => __( 'المنتور', 'zarincoach' ),
			'missing'  => __( 'بررسی', 'zarincoach' ),
			'internal' => __( 'داخلی', 'zarincoach' ),
		);

		$side  = '<p class="zc-tool-side-title">' . esc_html__( 'میان‌برها', 'zarincoach' ) . '</p>';
		if ( $elementor && post_type_exists( 'elementor_library' ) ) {
			$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library' ) ) . '"><i class="fa-brands fa-elementor" aria-hidden="true"></i>' . esc_html__( 'کتابخانه‌ی المنتور', 'zarincoach' ) . '</a>';
		}
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'nav-menus.php' ) ) . '"><i class="fa-solid fa-bars" aria-hidden="true"></i>' . esc_html__( 'منوها', 'zarincoach' ) . '</a>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'admin.php?page=zc-options' ) ) . '"><i class="fa-solid fa-sliders" aria-hidden="true"></i>' . esc_html__( 'تنظیمات قالب', 'zarincoach' ) . '</a>';

		zc_tool_shell_open(
			'zc-layout',
			array(
				'title'    => __( 'سربرگ و پاورقی', 'zarincoach' ),
				'subtitle' => __( 'مدیریت قالب‌های سراسری سایت', 'zarincoach' ),
				'icon'     => 'fa-solid fa-pen-ruler',
				'nav'      => array(
					'header'  => array( __( 'سربرگ', 'zarincoach' ), $locs['header']['icon'], $short[ $src_h['type'] ], $tones[ $src_h['type'] ] ),
					'footer'  => array( __( 'پاورقی', 'zarincoach' ), $locs['footer']['icon'], $short[ $src_f['type'] ], $tones[ $src_f['type'] ] ),
					'library' => array( __( 'کتابخانه‌ی قالب‌ها', 'zarincoach' ), 'fa-solid fa-swatchbook', $elementor ? zc_tool_num( count( $candidates ) ) : '', 'muted' ),
					'guide'   => array( __( 'راهنما', 'zarincoach' ), 'fa-regular fa-lightbulb' ),
				),
				'actions'  => $elementor ? '<a class="zc-btn zc-btn--ghost" href="' . esc_url( admin_url( 'edit.php?post_type=elementor_library&tabs_group=library#add_new' ) ) . '"><i class="fa-solid fa-plus" aria-hidden="true"></i><span>' . esc_html__( 'قالب جدید', 'zarincoach' ) . '</span></a>' : '',
				'side'     => $side,
			)
		);

		$messages = array(
			'saved'   => array( 'ok', __( 'تغییرات ذخیره شد.', 'zarincoach' ) ),
			'built'   => array( 'ok', __( 'قالب پیش‌فرض ساخته و به سایت متصل شد.', 'zarincoach' ) ),
			'invalid' => array( 'danger', __( 'قالب انتخاب‌شده معتبر نیست یا منتشر نشده است.', 'zarincoach' ) ),
			'failed'  => array( 'danger', __( 'ساخت قالب انجام نشد. مطمئن شوید المنتور فعال است و دوباره تلاش کنید.', 'zarincoach' ) ),
		);

		foreach ( array( 'header', 'footer' ) as $location ) {
			zc_tool_pane_open(
				$location,
				$locs[ $location ]['label'],
				'header' === $location
					? __( 'بالای همه‌ی صفحه‌ها: لوگو، منو، نوار اطلاع‌رسانی، دکمه‌ی رزرو، جستجو و حالت تاریک.', 'zarincoach' )
					: __( 'پایین همه‌ی صفحه‌ها: معرفی، خدمات، نوشته‌ها، اطلاعات تماس، نمادهای اعتماد و پیوندهای قوانین.', 'zarincoach' )
			);
			// phpcs:ignore WordPress.Security.NonceVerification
			$for = isset( $_GET['loc'] ) ? sanitize_key( wp_unslash( $_GET['loc'] ) ) : '';
			if ( isset( $messages[ $notice ] ) && $for === $location ) {
				echo zc_tool_alert( $messages[ $notice ][0], $messages[ $notice ][1] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			zc_layout_render_location( $location, $candidates );
			zc_tool_pane_close();
		}

		/* ---------------------------------------------------------------- کتابخانه */
		zc_tool_pane_open( 'library', __( 'کتابخانه‌ی قالب‌ها', 'zarincoach' ), __( 'قالب‌های المنتور که می‌توانند سربرگ یا پاورقی سایت باشند؛ با یک کلیک متصل کنید.', 'zarincoach' ) );
		if ( ! $elementor ) {
			echo zc_tool_alert( 'warn', __( 'المنتور فعال نیست.', 'zarincoach' ), __( 'کتابخانه‌ی قالب‌ها پس از فعال شدن المنتور در دسترس است.', 'zarincoach' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} else {
			$all = zc_layout_candidates( array( 'section', 'container', 'page', 'header', 'footer' ), array( 'publish', 'draft', 'private' ) );
			$h   = (int) zc_opt( 'header_template', 0 );
			$f   = (int) zc_opt( 'footer_template', 0 );
			if ( empty( $all ) ) {
				echo zc_tool_alert( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'info',
					__( 'هنوز قالبی در کتابخانه نیست.', 'zarincoach' ),
					__( 'از بخش «سربرگ» یا «پاورقی» قالب پیش‌فرض را بسازید یا دموی قالب را نصب کنید.', 'zarincoach' )
				);
			} else {
				?>
				<div class="zc-table-wrap">
					<table class="zc-table zc-table--lib">
						<thead><tr><th><?php esc_html_e( 'قالب', 'zarincoach' ); ?></th><th><?php esc_html_e( 'کاربرد', 'zarincoach' ); ?></th><th><?php esc_html_e( 'آخرین ویرایش', 'zarincoach' ); ?></th><th><span class="screen-reader-text"><?php esc_html_e( 'عملیات', 'zarincoach' ); ?></span></th></tr></thead>
						<tbody>
							<?php foreach ( $all as $tpl ) : ?>
								<?php
								$type    = (string) get_post_meta( $tpl->ID, '_elementor_template_type', true );
								$widgets = zc_layout_widgets_in( $tpl->ID );
								$is_pro  = in_array( $type, array( 'header', 'footer' ), true );
								$publish = 'publish' === $tpl->post_status;
								?>
								<tr>
									<td>
										<strong><?php echo esc_html( $tpl->post_title ? $tpl->post_title : __( '(بدون عنوان)', 'zarincoach' ) ); ?></strong>
										<small><?php echo esc_html( zc_layout_template_type( $tpl->ID ) . ' · #' . $tpl->ID ); ?><?php echo $publish ? '' : ' · ' . esc_html( get_post_status_object( $tpl->post_status )->label ); ?></small>
									</td>
									<td class="zc-tags">
										<?php
										$any = false;
										if ( $tpl->ID === $h ) {
											echo zc_tool_tag( __( 'سربرگ فعلی', 'zarincoach' ), 'ok', 'fa-solid fa-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											$any = true;
										}
										if ( $tpl->ID === $f ) {
											echo zc_tool_tag( __( 'پاورقی فعلی', 'zarincoach' ), 'ok', 'fa-solid fa-check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											$any = true;
										}
										if ( $is_pro ) {
											$cond = get_post_meta( $tpl->ID, '_elementor_conditions', true );
											echo zc_tool_tag( ! empty( $cond ) ? zc_layout_conditions_label( $cond ) : __( 'بدون شرط نمایش', 'zarincoach' ), 'info', 'fa-solid fa-crown' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											$any = true;
										}
										foreach ( $locs as $lk => $lv ) {
											if ( in_array( $lv['widget'], $widgets, true ) ) {
												echo zc_tool_tag( sprintf( /* translators: %s: نام ویجت */ __( 'ویجت %s', 'zarincoach' ), $lv['wname'] ), 'gold', 'fa-solid fa-puzzle-piece' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
												$any = true;
											}
										}
										if ( ! $any ) {
											echo '<span class="zc-faint">—</span>';
										}
										?>
									</td>
									<td><?php echo esc_html( zc_tool_date( (int) get_post_modified_time( 'U', true, $tpl ), false ) ); ?></td>
									<td class="zc-row-actions">
										<a class="zc-icon-btn" href="<?php echo esc_url( admin_url( 'post.php?post=' . $tpl->ID . '&action=elementor' ) ); ?>" title="<?php esc_attr_e( 'ویرایش با المنتور', 'zarincoach' ); ?>"><i class="fa-solid fa-pen" aria-hidden="true"></i><span class="screen-reader-text"><?php esc_html_e( 'ویرایش با المنتور', 'zarincoach' ); ?></span></a>
										<?php if ( $publish ) : ?>
											<a class="zc-icon-btn" href="<?php echo esc_url( get_permalink( $tpl ) ); ?>" target="_blank" rel="noopener" title="<?php esc_attr_e( 'پیش‌نمایش', 'zarincoach' ); ?>"><i class="fa-regular fa-eye" aria-hidden="true"></i><span class="screen-reader-text"><?php esc_html_e( 'پیش‌نمایش', 'zarincoach' ); ?></span></a>
											<?php if ( ! $is_pro ) : ?>
												<?php if ( $tpl->ID !== $h ) : ?>
													<a class="zc-btn zc-btn--ghost zc-btn--sm" href="<?php echo esc_url( zc_layout_assign_url( 'header', $tpl->ID ) ); ?>"><?php esc_html_e( 'سربرگ شود', 'zarincoach' ); ?></a>
												<?php endif; ?>
												<?php if ( $tpl->ID !== $f ) : ?>
													<a class="zc-btn zc-btn--ghost zc-btn--sm" href="<?php echo esc_url( zc_layout_assign_url( 'footer', $tpl->ID ) ); ?>"><?php esc_html_e( 'پاورقی شود', 'zarincoach' ); ?></a>
												<?php endif; ?>
											<?php endif; ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<?php
			}
		}
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- راهنما */
		zc_tool_pane_open( 'guide', __( 'راهنما', 'zarincoach' ), __( 'سربرگ و پاورقی چگونه انتخاب می‌شوند و چطور آن‌ها را شخصی‌سازی کنید.', 'zarincoach' ) );
		zc_tool_heading( __( 'ترتیب اولویت نمایش', 'zarincoach' ) );
		?>
		<ol class="zc-priority">
			<li>
				<strong><?php esc_html_e( 'قالب‌ساز المنتور پرو', 'zarincoach' ); ?></strong>
				<p><?php esc_html_e( 'اگر المنتور پرو نصب باشد و قالبی از نوع «سربرگ» یا «پاورقی» با شرط نمایش منتشر شده باشد، در صفحه‌های منطبق همان نمایش داده می‌شود.', 'zarincoach' ); ?></p>
			</li>
			<li>
				<strong><?php esc_html_e( 'قالب المنتورِ انتخاب‌شده در این صفحه', 'zarincoach' ); ?></strong>
				<p><?php esc_html_e( 'قالبی از کتابخانه‌ی المنتور (معمولاً با ویجت «سربرگ سایت» یا «پاورقی سایت») که برای همه‌ی صفحه‌ها نمایش داده می‌شود. این روش بدون المنتور پرو هم کار می‌کند.', 'zarincoach' ); ?></p>
			</li>
			<li>
				<strong><?php esc_html_e( 'نسخه‌ی داخلی قالب', 'zarincoach' ); ?></strong>
				<p><?php esc_html_e( 'اگر هیچ‌کدام از موارد بالا نباشد، سربرگ و پاورقی سبک و سریع قالب بر اساس «تنظیمات قالب» نمایش داده می‌شوند.', 'zarincoach' ); ?></p>
			</li>
		</ol>

		<?php zc_tool_heading( __( 'ساخت سربرگ اختصاصی در ۴ گام', 'zarincoach' ) ); ?>
		<ol class="zc-plan">
			<li><?php esc_html_e( 'در «قالب‌ها ← افزودن جدید» المنتور، نوع «بخش» را انتخاب و نام‌گذاری کنید.', 'zarincoach' ); ?></li>
			<li><?php esc_html_e( 'از دسته‌ی «زرین‌کوچ» ویجت «سربرگ سایت» یا «پاورقی سایت» را بکشید و تنظیم کنید.', 'zarincoach' ); ?></li>
			<li><?php esc_html_e( 'قالب را منتشر کنید تا در فهرست این صفحه ظاهر شود.', 'zarincoach' ); ?></li>
			<li><?php esc_html_e( 'در زبانه‌ی «سربرگ» یا «پاورقی» آن را انتخاب و ذخیره کنید.', 'zarincoach' ); ?></li>
		</ol>

		<?php zc_tool_heading( __( 'برای توسعه‌دهندگان', 'zarincoach' ) ); ?>
		<p class="zc-muted"><?php esc_html_e( 'برای نمایش قالب متفاوت در صفحه‌های خاص (مثلاً فروشگاه) از فیلتر زیر در قالب فرزند استفاده کنید:', 'zarincoach' ); ?></p>
		<pre class="zc-code" dir="ltr"><code>add_filter( 'zc_layout_template_id', function ( $id, $location ) {
	if ( 'header' === $location &amp;&amp; function_exists( 'is_woocommerce' ) &amp;&amp; is_woocommerce() ) {
		return 123; // Elementor template ID
	}
	return $id;
}, 10, 2 );</code></pre>
		<?php
		zc_tool_pane_close();
		zc_tool_shell_close();
	}
endif;

if ( ! function_exists( 'zc_layout_redirect' ) ) :
	/**
	 * بازگشت به صفحه با پیام.
	 *
	 * @param string $status   وضعیت.
	 * @param string $location مکان.
	 * @return void
	 */
	function zc_layout_redirect( $status, $location ) {
		wp_safe_redirect(
			zc_layout_url(
				array(
					'zc_layout' => $status,
					'loc'       => $location,
				),
				$location
			)
		);
		exit;
	}
endif;

if ( ! function_exists( 'zc_handle_layout_save' ) ) :
	/**
	 * ذخیره‌ی قالب سربرگ/پاورقی.
	 *
	 * @return void
	 */
	function zc_handle_layout_save() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'zarincoach' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'zc_layout_save' );

		// phpcs:disable WordPress.Security.NonceVerification.Recommended -- نانس بالا بررسی شد.
		$location = isset( $_REQUEST['location'] ) ? sanitize_key( wp_unslash( $_REQUEST['location'] ) ) : '';
		$template = isset( $_REQUEST['template'] ) ? absint( $_REQUEST['template'] ) : 0;
		// phpcs:enable

		if ( ! array_key_exists( $location, zc_layout_locations() ) ) {
			wp_die( esc_html__( 'مکان نامعتبر است.', 'zarincoach' ), '', array( 'response' => 400 ) );
		}
		if ( $template && ( 'elementor_library' !== get_post_type( $template ) || 'publish' !== get_post_status( $template ) ) ) {
			zc_layout_redirect( 'invalid', $location );
		}

		$options = get_option( ZC_OPT, array() );
		$options = is_array( $options ) ? $options : array();

		$options[ $location . '_template' ] = $template ? (string) $template : '';
		update_option( ZC_OPT, $options );
		if ( function_exists( 'zc_opt_flush' ) ) {
			zc_opt_flush();
		}

		zc_layout_redirect( 'saved', $location );
	}
endif;
add_action( 'admin_post_zc_layout_save', 'zc_handle_layout_save' );

if ( ! function_exists( 'zc_handle_layout_build' ) ) :
	/**
	 * ساخت/بازسازی قالب پیش‌فرض سربرگ یا پاورقی.
	 *
	 * @return void
	 */
	function zc_handle_layout_build() {
		if ( ! current_user_can( 'edit_theme_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'zarincoach' ), '', array( 'response' => 403 ) );
		}
		check_admin_referer( 'zc_layout_build' );

		$location = isset( $_POST['location'] ) ? sanitize_key( wp_unslash( $_POST['location'] ) ) : '';
		if ( ! array_key_exists( $location, zc_layout_locations() ) ) {
			wp_die( esc_html__( 'مکان نامعتبر است.', 'zarincoach' ), '', array( 'response' => 400 ) );
		}

		$id = ( zc_is_elementor_active() && function_exists( 'zc_build_elementor_layout_template' ) ) ? (int) zc_build_elementor_layout_template( $location ) : 0;

		zc_layout_redirect( $id ? 'built' : 'failed', $location );
	}
endif;
add_action( 'admin_post_zc_layout_build', 'zc_handle_layout_build' );
