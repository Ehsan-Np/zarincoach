<?php
/**
 * صفحات مدیریتی قالب در پیشخوان وردپرس
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_render_demo_page' ) ) :
	/**
	 * صفحه نصب دموی یک‌کلیکی.
	 *
	 * @return void
	 */
	function zc_render_demo_page() {
		$installed = '1' === (string) get_option( 'zc_demo_installed' );
		$version   = (string) get_option( 'zc_demo_version', '' );
		$palettes  = zc_palettes();
		$current   = (string) zc_opt( 'palette_preset', 'navy' );
		$elementor = zc_is_elementor_active();
		$steps     = zc_demo_steps();
		$notice    = isset( $_GET['zc_demo'] ) ? sanitize_key( wp_unslash( $_GET['zc_demo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$shot      = get_template_directory_uri() . '/screenshot.png';
		$home_id   = (int) get_option( 'page_on_front' );
		?>
		<div class="wrap zc-admin-wrap zc-demo-wrap">
			<?php echo zc_panel_header_bar( 'zc-demo-content' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h1 class="screen-reader-text"><?php esc_html_e( 'نصب دمو', 'zarincoach' ); ?></h1>

			<?php if ( 'installed' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'دمو با موفقیت نصب شد.', 'zarincoach' ); ?></p></div>
			<?php elseif ( 'removed' === $notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'محتوای دمو با موفقیت حذف شد.', 'zarincoach' ); ?></p></div>
			<?php endif; ?>

			<div class="zc-demo-hero">
				<div>
					<span class="zc-demo-chip"><?php esc_html_e( 'نصب دموی یک‌کلیکی', 'zarincoach' ); ?></span>
					<h2><?php esc_html_e( 'دموی کامل زرین‌کوچ را در کمتر از یک دقیقه نصب کن', 'zarincoach' ); ?></h2>
					<p><?php esc_html_e( 'برگه‌ها، نوشته‌ها با تصویر و دیدگاه، خدمات، نظرات مراجعان، پرسش‌ها، منوها، تنظیمات پنل و صفحات طراحی‌شده با المنتور — دقیقاً مثل پیش‌نمایش.', 'zarincoach' ); ?></p>
				</div>
				<div class="zc-demo-status">
					<?php if ( $installed ) : ?>
						<span class="zc-badge-ok">
							<?php
							/* translators: %s: نسخه دمو */
							echo esc_html( sprintf( __( 'نصب شده — نسخه %s', 'zarincoach' ), '' !== $version ? $version : '1.0' ) );
							?>
						</span>
					<?php else : ?>
						<span class="zc-badge-warn"><?php esc_html_e( 'هنوز نصب نشده', 'zarincoach' ); ?></span>
					<?php endif; ?>
				</div>
			</div>

			<div class="zc-demo-grid">
				<!-- پیش‌نمایش -->
				<div class="zc-card zc-demo-preview">
					<div class="zc-browser">
						<div class="zc-browser-bar"><i></i><i></i><i></i><span><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></span></div>
						<div class="zc-browser-body"><img src="<?php echo esc_url( $shot ); ?>" alt="<?php esc_attr_e( 'پیش‌نمایش دمو', 'zarincoach' ); ?>"></div>
					</div>

					<ul class="zc-demo-includes">
						<?php
						$zc_legal_n = function_exists( 'zc_demo_legal_pages' ) ? count( zc_demo_legal_pages() ) : 11;
						$zc_page_n  = function_exists( 'zc_demo_pages' ) ? count( zc_demo_pages() ) : 23;
						?>
						<li><strong><?php echo esc_html( zc_digits_to_persian( (string) $zc_page_n ) ); ?></strong><?php
							/* translators: %s: تعداد صفحات قوانین */
							echo esc_html( sprintf( __( 'برگه (رزومه، طرحواره‌ها + %s صفحه قوانین)', 'zarincoach' ), zc_digits_to_persian( (string) $zc_legal_n ) ) );
						?></li>
						<li><strong>۶</strong><?php esc_html_e( 'نوشته + دیدگاه', 'zarincoach' ); ?></li>
						<li><strong>۶</strong><?php esc_html_e( 'خدمت', 'zarincoach' ); ?></li>
						<li><strong>۶</strong><?php esc_html_e( 'بازخورد بی‌نام', 'zarincoach' ); ?></li>
						<li><strong>۱۲</strong><?php esc_html_e( 'پرسش پرتکرار', 'zarincoach' ); ?></li>
						<li><strong><?php echo esc_html( zc_digits_to_persian( (string) ( function_exists( 'zc_demo_media_files' ) ? count( zc_demo_media_files() ) : 15 ) ) ); ?></strong><?php esc_html_e( 'تصویر اختصاصی', 'zarincoach' ); ?></li>
						<li><strong>۴۷</strong><?php esc_html_e( 'مدخل کتابخانه‌ی طرحواره‌ها و الگوهای ذهنی', 'zarincoach' ); ?></li>
						<li><strong>۴</strong><?php esc_html_e( 'منو', 'zarincoach' ); ?></li>
						<?php if ( class_exists( 'WooCommerce' ) && function_exists( 'zc_demo_shop_products' ) ) : ?>
							<li><strong><?php echo esc_html( zc_digits_to_persian( (string) count( zc_demo_shop_products() ) ) ); ?></strong><?php esc_html_e( 'محصول نمونه (کتاب، کارت، دانلودی، بسته‌ی جلسات) + کد تخفیف', 'zarincoach' ); ?></li>
						<?php endif; ?>
						<li><strong><?php echo esc_html( zc_digits_to_persian( (string) ( $zc_page_n + 2 ) ) ); ?></strong><?php esc_html_e( 'طرح المنتور', 'zarincoach' ); ?></li>
					</ul>
				</div>

				<!-- تنظیمات نصب -->
				<div class="zc-card">
					<form id="zc-demo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
						<input type="hidden" name="action" value="zc_install_demo">
						<?php wp_nonce_field( 'zc_install_demo_action', 'zc_install_demo_nonce' ); ?>

						<h2 class="zc-h"><?php esc_html_e( '۱. انتخاب پالت رنگی', 'zarincoach' ); ?></h2>
						<div class="zc-palettes">
							<?php foreach ( $palettes as $key => $palette ) : ?>
								<label class="zc-palette">
									<input type="radio" name="palette" value="<?php echo esc_attr( $key ); ?>" <?php checked( $installed ? $current : 'navy', $key ); ?>>
									<span class="zc-palette-box">
										<span class="zc-swatches">
											<?php foreach ( array( 'secondary', 'primary', 'accent', 'info', 'base' ) as $c ) : ?>
												<i style="background:<?php echo esc_attr( $palette['light'][ $c ] ); ?>"></i>
											<?php endforeach; ?>
										</span>
										<span class="zc-palette-name"><?php echo esc_html( $palette['label'] ); ?></span>
										<?php if ( 'navy' === $key ) : ?>
											<em><?php esc_html_e( 'پیشنهادی دمو', 'zarincoach' ); ?></em>
										<?php endif; ?>
									</span>
								</label>
							<?php endforeach; ?>
						</div>

						<h2 class="zc-h"><?php esc_html_e( '۲. صفحه‌ساز', 'zarincoach' ); ?></h2>
						<?php if ( $elementor ) : ?>
							<label class="zc-check"><input type="checkbox" name="elementor" value="1" checked> <?php esc_html_e( 'طراحی همه‌ی صفحات، صفحات قوانین، سربرگ و پاورقی با ویجت‌های اختصاصی المنتور (پیشنهادی)', 'zarincoach' ); ?></label>
						<?php else : ?>
							<p class="zc-muted">
								<span class="zc-badge-warn"><?php esc_html_e( 'المنتور نصب نیست', 'zarincoach' ); ?></span>
								<?php esc_html_e( 'دمو با طراحی داخلی قالب نصب می‌شود (همه‌ی بخش‌ها از پنل تنظیمات قابل ویرایش‌اند). برای طراحی بصری:', 'zarincoach' ); ?>
								<a href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' ) ); ?>"><?php esc_html_e( 'نصب المنتور', 'zarincoach' ); ?></a>
							</p>
						<?php endif; ?>

						<h2 class="zc-h"><?php esc_html_e( '۳. نصب', 'zarincoach' ); ?></h2>
						<p class="zc-muted"><?php esc_html_e( 'نصب همیشه از صفر انجام می‌شود: ابتدا همه‌ی موارد دموی قبلی (برگه‌ها، نوشته‌ها، تصاویر، منوها و تنظیمات) پاک و سپس دوباره ساخته می‌شوند؛ بنابراین هیچ محتوای تکراری ایجاد نمی‌شود. محتوایی که خودتان ساخته‌اید، لوگو، کدهای سفارشی و شماره‌های مجوز حفظ می‌شوند.', 'zarincoach' ); ?></p>

						<button type="submit" class="button button-primary button-hero zc-demo-btn" id="zc-demo-start">
							<?php echo $installed ? esc_html__( 'نصب دوباره / بروزرسانی دمو', 'zarincoach' ) : esc_html__( 'نصب دمو', 'zarincoach' ); ?>
						</button>
						<input type="hidden" name="force" value="1">
					</form>

					<div id="zc-demo-progress" class="zc-demo-progress" hidden>
						<div class="zc-bar"><span id="zc-demo-bar"></span></div>
						<ol class="zc-steps">
							<?php foreach ( $steps as $key => $label ) : ?>
								<li data-step="<?php echo esc_attr( $key ); ?>"><i></i><span><?php echo esc_html( $label ); ?></span><small></small></li>
							<?php endforeach; ?>
						</ol>
						<div id="zc-demo-done" class="zc-demo-done" hidden>
							<strong><?php esc_html_e( 'دمو با موفقیت نصب شد!', 'zarincoach' ); ?></strong>
							<p>
								<a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><?php esc_html_e( 'مشاهده سایت', 'zarincoach' ); ?></a>
								<?php if ( $elementor ) : ?>
									<a class="button" id="zc-demo-edit" href="<?php echo esc_url( admin_url( 'post.php?action=elementor&post=' . $home_id ) ); ?>"><?php esc_html_e( 'ویرایش خانه با المنتور', 'zarincoach' ); ?></a>
								<?php endif; ?>
								<a class="button" href="<?php echo esc_url( admin_url( 'admin.php?page=zc-options' ) ); ?>"><?php esc_html_e( 'تنظیمات قالب', 'zarincoach' ); ?></a>
							</p>
						</div>
					</div>
				</div>
			</div>

			<div class="zc-card zc-danger">
				<h2><?php esc_html_e( 'حذف دمو', 'zarincoach' ); ?></h2>
				<p><?php esc_html_e( 'فقط مواردی که توسط نصب‌کننده‌ی دمو ساخته شده‌اند (برگه‌ها، نوشته‌ها، تصاویر، خدمات، نظرات، پرسش‌ها، دسته‌ها و منوها) حذف می‌شوند. این عملیات غیرقابل بازگشت است.', 'zarincoach' ); ?></p>
				<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'آیا از حذف محتوای دمو مطمئن هستید؟', 'zarincoach' ) ); ?>');">
					<input type="hidden" name="action" value="zc_remove_demo">
					<?php wp_nonce_field( 'zc_remove_demo_action', 'zc_remove_demo_nonce' ); ?>
					<label class="zc-check"><input type="checkbox" name="reset_options" value="1"> <?php esc_html_e( 'بازنشانی تنظیمات پنل قالب به حالت پیش‌فرض', 'zarincoach' ); ?></label>
					<p><button class="button button-secondary" type="submit"><?php esc_html_e( 'حذف محتوای دمو', 'zarincoach' ); ?></button></p>
				</form>
			</div>
		</div>

		<script>
		(function () {
			var form = document.getElementById('zc-demo-form');
			if (!form || !window.fetch) { return; }
			var steps = <?php echo wp_json_encode( array_keys( $steps ) ); ?>;
			var ajax  = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
			var nonce = <?php echo wp_json_encode( wp_create_nonce( 'zc_demo_import' ) ); ?>;
			var failText = <?php echo wp_json_encode( __( 'خطا در اجرای این گام. دوباره تلاش کنید.', 'zarincoach' ) ); ?>;

			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var btn = document.getElementById('zc-demo-start');
				var palette = (form.querySelector('input[name=palette]:checked') || {}).value || 'navy';
				var el = form.querySelector('input[name=elementor]');
				var useElementor = el ? (el.checked ? 1 : 0) : 0;
				var box = document.getElementById('zc-demo-progress');
				var bar = document.getElementById('zc-demo-bar');
				btn.disabled = true;
				form.classList.add('is-running');
				box.hidden = false;
				var i = 0;

				function mark(step, state, msg) {
					var li = box.querySelector('li[data-step="' + step + '"]');
					if (!li) { return; }
					li.className = state;
					if (msg) { li.querySelector('small').textContent = msg; }
				}

				function run() {
					if (i >= steps.length) {
						bar.style.width = '100%';
						document.getElementById('zc-demo-done').hidden = false;
						return;
					}
					var step = steps[i];
					mark(step, 'is-running');
					var body = new FormData();
					body.append('action', 'zc_demo_step');
					body.append('nonce', nonce);
					body.append('step', step);
					body.append('palette', palette);
					body.append('elementor', useElementor);

					fetch(ajax, { method: 'POST', credentials: 'same-origin', body: body })
						.then(function (r) { return r.json(); })
						.then(function (res) {
							if (!res || !res.success) { throw new Error((res && res.data && res.data.message) || failText); }
							mark(step, res.data.more ? 'is-running' : 'is-done', res.data.message);
							if (!res.data.more) { i++; }
							bar.style.width = Math.round((i / steps.length) * 100) + '%';
							run();
						})
						.catch(function (err) {
							mark(step, 'is-error', err.message || failText);
							btn.disabled = false;
							form.classList.remove('is-running');
						});
				}
				run();
			});
		})();
		</script>
		<?php
	}
endif;

if ( ! function_exists( 'zc_ajax_demo_step' ) ) :
	/**
	 * اجرای یک گام نصب دمو از طریق AJAX.
	 *
	 * @return void
	 */
	function zc_ajax_demo_step() {
		check_ajax_referer( 'zc_demo_import', 'nonce' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'دسترسی غیرمجاز.', 'zarincoach' ) ), 403 );
		}

		$step = isset( $_POST['step'] ) ? sanitize_key( wp_unslash( $_POST['step'] ) ) : '';
		if ( ! array_key_exists( $step, zc_demo_steps() ) ) {
			wp_send_json_error( array( 'message' => __( 'گام نامعتبر است.', 'zarincoach' ) ), 400 );
		}

		$result = zc_demo_run_step(
			$step,
			array(
				'palette'   => isset( $_POST['palette'] ) ? sanitize_key( wp_unslash( $_POST['palette'] ) ) : 'navy',
				'elementor' => ! empty( $_POST['elementor'] ),
			)
		);

		if ( empty( $result['done'] ) ) {
			wp_send_json_error( $result, 500 );
		}

		wp_send_json_success( $result );
	}
endif;
add_action( 'wp_ajax_zc_demo_step', 'zc_ajax_demo_step' );

if ( ! function_exists( 'zc_render_system_page' ) ) :
	/**
	 * صفحه اطلاعات سیستم.
	 *
	 * @return void
	 */
	function zc_render_system_page() {
		global $wpdb;

		$theme = wp_get_theme();
		$info  = array(
			esc_html__( 'نسخه قالب', 'zarincoach' )      => esc_html( $theme->get( 'Version' ) ),
			esc_html__( 'نسخه وردپرس', 'zarincoach' )    => esc_html( get_bloginfo( 'version' ) ),
			esc_html__( 'نسخه PHP', 'zarincoach' )       => esc_html( PHP_VERSION ),
			esc_html__( 'نسخه MySQL', 'zarincoach' )     => esc_html( $wpdb->db_version() ),
			esc_html__( 'محدودیت حافظه', 'zarincoach' )  => esc_html( WP_MEMORY_LIMIT ),
			esc_html__( 'فونت فعال', 'zarincoach' )      => esc_html( zc_switch( 'typo_persian_digits', true ) ? 'آراد (ارقام فارسی)' : 'آراد' ),
			esc_html__( 'پالت رنگی', 'zarincoach' )      => esc_html( (string) zc_opt( 'palette_preset', 'navy' ) ),
			esc_html__( 'المنتور', 'zarincoach' )        => zc_is_elementor_active() ? esc_html__( 'فعال', 'zarincoach' ) : esc_html__( 'غیرفعال', 'zarincoach' ),
			esc_html__( 'Redux Framework', 'zarincoach' ) => zc_redux_available() ? esc_html__( 'در دسترس', 'zarincoach' ) : esc_html__( 'در دسترس نیست', 'zarincoach' ),
			esc_html__( 'حالت RTL', 'zarincoach' )       => is_rtl() ? esc_html__( 'فعال', 'zarincoach' ) : esc_html__( 'غیرفعال', 'zarincoach' ),
			esc_html__( 'محتوای نمونه', 'zarincoach' )   => '1' === (string) get_option( 'zc_demo_installed' ) ? esc_html__( 'نصب شده', 'zarincoach' ) : esc_html__( 'نصب نشده', 'zarincoach' ),
		);
		?>
		<div class="wrap zc-admin-wrap zc-system-wrap">
			<?php echo zc_panel_header_bar( 'zc-system-info' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<h1 class="screen-reader-text"><?php esc_html_e( 'اطلاعات سیستم', 'zarincoach' ); ?></h1>

			<div class="zc-card">
				<h2><?php esc_html_e( 'وضعیت سایت', 'zarincoach' ); ?></h2>
				<table class="zc-admin-table">
					<?php foreach ( $info as $label => $value ) : ?>
						<tr><th><?php echo esc_html( $label ); ?></th><td><?php echo esc_html( $value ); ?></td></tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div class="zc-card">
				<h2><?php esc_html_e( 'افزونه‌های فعال', 'zarincoach' ); ?></h2>
				<table class="zc-admin-table">
					<?php
					$plugins = get_option( 'active_plugins', array() );
					if ( empty( $plugins ) ) {
						echo '<tr><td>' . esc_html__( 'افزونه فعالی وجود ندارد.', 'zarincoach' ) . '</td></tr>';
					} else {
						foreach ( $plugins as $plugin ) {
							$data = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
							if ( empty( $data['Name'] ) ) {
								continue;
							}
							echo '<tr><th>' . esc_html( $data['Name'] ) . '</th><td>' . esc_html( $data['Version'] ) . '</td></tr>';
						}
					}
					?>
				</table>
			</div>
		</div>
		<?php
	}
endif;

if ( ! function_exists( 'zc_handle_install_demo' ) ) :
	/**
	 * پردازش درخواست ایجاد محتوای نمونه.
	 *
	 * @return void
	 */
	function zc_handle_install_demo() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'zarincoach' ) );
		}

		check_admin_referer( 'zc_install_demo_action', 'zc_install_demo_nonce' );

		$force = ! empty( $_POST['force'] );
		zc_install_demo_content(
			(bool) $force,
			array(
				'palette'   => isset( $_POST['palette'] ) ? sanitize_key( wp_unslash( $_POST['palette'] ) ) : 'navy',
				'elementor' => ! empty( $_POST['elementor'] ),
			)
		);

		wp_safe_redirect( admin_url( 'admin.php?page=zc-demo-content&zc_demo=installed' ) );
		exit;
	}
endif;
add_action( 'admin_post_zc_install_demo', 'zc_handle_install_demo' );

if ( ! function_exists( 'zc_handle_remove_demo' ) ) :
	/**
	 * پردازش درخواست حذف محتوای نمونه.
	 *
	 * @return void
	 */
	function zc_handle_remove_demo() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'دسترسی غیرمجاز.', 'zarincoach' ) );
		}

		check_admin_referer( 'zc_remove_demo_action', 'zc_remove_demo_nonce' );

		zc_remove_demo_content( ! empty( $_POST['reset_options'] ) );

		wp_safe_redirect( admin_url( 'admin.php?page=zc-demo-content&zc_demo=removed' ) );
		exit;
	}
endif;
add_action( 'admin_post_zc_remove_demo', 'zc_handle_remove_demo' );

if ( ! function_exists( 'zc_admin_notices' ) ) :
	/**
	 * اطلاعیه‌های مدیریتی.
	 *
	 * @return void
	 */
	function zc_admin_notices() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}

		$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
		if ( $screen && 'dashboard' !== $screen->id && 'themes' !== $screen->id && 'plugins' !== $screen->id ) {
			return;
		}

		if ( ! zc_is_elementor_active() ) {
			$install_url = wp_nonce_url(
				self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ),
				'install-plugin_elementor'
			);
			?>
			<div class="notice notice-info is-dismissible">
				<p>
					<?php esc_html_e( 'قالب زرین‌کوچ با المنتور سازگار است و ۱۶ ویجت اختصاصی برای آن طراحی شده است.', 'zarincoach' ); ?>
					<a href="<?php echo esc_url( $install_url ); ?>"><?php esc_html_e( 'نصب المنتور', 'zarincoach' ); ?></a>
				</p>
			</div>
			<?php
		}
	}
endif;
add_action( 'admin_notices', 'zc_admin_notices' );

if ( ! function_exists( 'zc_theme_action_links' ) ) :
	/**
	 * افزودن لینک‌های سریع به صفحه قالب‌ها (Child Theme friendly).
	 *
	 * @param array $links لینک‌ها.
	 * @return array
	 */
	function zc_theme_action_links( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=zc-options' ) ) . '">' . esc_html__( 'تنظیمات قالب', 'zarincoach' ) . '</a>';
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=zc-demo-content' ) ) . '">' . esc_html__( 'محتوای نمونه', 'zarincoach' ) . '</a>';
		return $links;
	}
endif;
add_filter( 'theme_action_links_' . get_option( 'stylesheet' ), 'zc_theme_action_links' );
