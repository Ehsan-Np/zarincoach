<?php
/**
 * صفحات مدیریتی قالب در پیشخوان وردپرس (نصب دمو، اطلاعات سیستم)
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_demo_inventory' ) ) :
	/**
	 * فهرست محتوای بسته‌ی دمو با تعداد واقعی (از خودِ تعریف‌های دمو شمرده می‌شود).
	 *
	 * @return array<string, array{0:int,1:string,2:string}> کلید => (تعداد، برچسب، آیکن).
	 */
	function zc_demo_inventory() {
		$count = static function ( $fn ) {
			return function_exists( $fn ) ? count( (array) call_user_func( $fn ) ) : 0;
		};

		$pages = $count( 'zc_demo_pages' );
		$items = array(
			'pages'        => array( $pages, __( 'برگه (خانه، رزومه، خدمات، تماس و…)', 'zarincoach' ), 'fa-solid fa-file-lines' ),
			'legal'        => array( $count( 'zc_demo_legal_pages' ), __( 'صفحه‌ی قوانین و حریم خصوصی', 'zarincoach' ), 'fa-solid fa-scale-balanced' ),
			'posts'        => array( $count( 'zc_demo_posts' ), __( 'نوشته‌ی وبلاگ با دیدگاه', 'zarincoach' ), 'fa-solid fa-pen-nib' ),
			'services'     => array( $count( 'zc_demo_services' ), __( 'خدمت و مسیر همراهی', 'zarincoach' ), 'fa-solid fa-hand-holding-heart' ),
			'testimonials' => array( $count( 'zc_demo_testimonials' ), __( 'بازخورد واقعی مراجعان', 'zarincoach' ), 'fa-solid fa-quote-right' ),
			'faqs'         => array( $count( 'zc_demo_faqs' ), __( 'پرسش پرتکرار', 'zarincoach' ), 'fa-solid fa-circle-question' ),
			'schemas'      => array( $count( 'zc_demo_schemas' ), __( 'مدخل طرحواره و الگوی ذهنی', 'zarincoach' ), 'fa-solid fa-brain' ),
			'media'        => array( $count( 'zc_demo_media_files' ), __( 'تصویر اختصاصی', 'zarincoach' ), 'fa-solid fa-images' ),
			'menus'        => array( $count( 'zc_demo_menu_names' ), __( 'منو (سربرگ، موبایل، پاورقی، قوانین)', 'zarincoach' ), 'fa-solid fa-bars-staggered' ),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$items['products'] = array( $count( 'zc_demo_shop_products' ), __( 'محصول نمونه + کد تخفیف', 'zarincoach' ), 'fa-solid fa-bag-shopping' );
		}
		if ( zc_is_elementor_active() ) {
			// همه‌ی برگه‌ها + قالب سربرگ و پاورقی.
			$items['elementor'] = array( $pages + 2, __( 'طرح المنتور (برگه‌ها، سربرگ، پاورقی)', 'zarincoach' ), 'fa-solid fa-layer-group' );
		}

		return array_filter(
			$items,
			static function ( $item ) {
				return $item[0] > 0;
			}
		);
	}
endif;

if ( ! function_exists( 'zc_render_demo_page' ) ) :
	/**
	 * صفحه نصب دموی یک‌کلیکی.
	 *
	 * @return void
	 */
	function zc_render_demo_page() {
		$installed = '1' === (string) get_option( 'zc_demo_installed' );
		$version   = (string) get_option( 'zc_demo_version', '' );
		$pack      = function_exists( 'zc_demo_version' ) ? (string) zc_demo_version() : '';
		$palettes  = zc_palettes();
		$current   = (string) zc_opt( 'palette_preset', 'navy' );
		$elementor = zc_is_elementor_active();
		$steps     = zc_demo_steps();
		$inventory = zc_demo_inventory();
		$notice    = isset( $_GET['zc_demo'] ) ? sanitize_key( wp_unslash( $_GET['zc_demo'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification
		$removed_n = isset( $_GET['zc_count'] ) ? absint( $_GET['zc_count'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification
		$shot      = get_template_directory_uri() . '/screenshot.png';
		$home_id   = (int) get_option( 'page_on_front' );
		$at        = (int) get_option( 'zc_demo_installed_at', 0 );
		$outdated  = $installed && '' !== $pack && '' !== $version && version_compare( $version, $pack, '<' );

		$status = $installed
			? zc_tool_tag( sprintf( /* translators: %s: نسخه دمو */ __( 'نصب‌شده · نسخه %s', 'zarincoach' ), '' !== $version ? $version : '1.0' ), $outdated ? 'warn' : 'ok', $outdated ? 'fa-solid fa-arrows-rotate' : 'fa-solid fa-circle-check' )
			: zc_tool_tag( __( 'هنوز نصب نشده', 'zarincoach' ), 'warn', 'fa-regular fa-clock' );

		$side  = '<p class="zc-tool-side-title">' . esc_html__( 'پس از نصب', 'zarincoach' ) . '</p>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( home_url( '/' ) ) . '" target="_blank" rel="noopener"><i class="fa-solid fa-house" aria-hidden="true"></i>' . esc_html__( 'مشاهده‌ی سایت', 'zarincoach' ) . '</a>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'admin.php?page=zc-options' ) ) . '"><i class="fa-solid fa-sliders" aria-hidden="true"></i>' . esc_html__( 'تنظیمات قالب', 'zarincoach' ) . '</a>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'admin.php?page=zc-layout' ) ) . '"><i class="fa-solid fa-pen-ruler" aria-hidden="true"></i>' . esc_html__( 'سربرگ و پاورقی', 'zarincoach' ) . '</a>';
		if ( $elementor && $home_id ) {
			$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'post.php?action=elementor&post=' . $home_id ) ) . '"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i>' . esc_html__( 'ویرایش خانه با المنتور', 'zarincoach' ) . '</a>';
		}

		zc_tool_shell_open(
			'zc-demo-content',
			array(
				'title'    => __( 'نصب دمو', 'zarincoach' ),
				'subtitle' => __( 'دموی کامل با داده‌های واقعی', 'zarincoach' ),
				'icon'     => 'fa-solid fa-wand-magic-sparkles',
				'nav'      => array(
					'install' => array( __( 'نصب دمو', 'zarincoach' ), 'fa-solid fa-download', $installed ? ( $outdated ? __( 'بروزرسانی', 'zarincoach' ) : __( 'نصب‌شده', 'zarincoach' ) ) : '', $outdated ? 'warn' : 'ok' ),
					'content' => array( __( 'محتوای بسته', 'zarincoach' ), 'fa-solid fa-box-open', zc_tool_num( array_sum( wp_list_pluck( $inventory, 0 ) ) ), 'muted' ),
					'remove'  => array( __( 'حذف دمو', 'zarincoach' ), 'fa-solid fa-trash-can' ),
				),
				'actions'  => $status,
				'side'     => $side,
			)
		);

		/* ---------------------------------------------------------------- نصب */
		zc_tool_pane_open( 'install', __( 'نصب دمو', 'zarincoach' ), __( 'دموی کامل زرین‌کوچ را با یک کلیک نصب کنید؛ همه‌چیز دقیقاً مثل پیش‌نمایش ساخته می‌شود و بعد از نصب قابل ویرایش است.', 'zarincoach' ) );

		if ( 'installed' === $notice ) {
			echo zc_tool_alert( 'ok', __( 'دمو با موفقیت نصب شد.', 'zarincoach' ), __( 'اکنون می‌توانید سایت را ببینید یا متن‌ها و تصاویر را با المنتور و پنل تنظیمات شخصی‌سازی کنید.', 'zarincoach' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		} elseif ( 'removed' === $notice ) {
			echo zc_tool_alert( 'ok', __( 'محتوای دمو حذف شد.', 'zarincoach' ), $removed_n ? sprintf( /* translators: %s: تعداد */ __( '%s مورد ساخته‌شده توسط نصب‌کننده‌ی دمو پاک شد.', 'zarincoach' ), zc_tool_num( $removed_n ) ) : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		if ( $outdated ) {
			echo zc_tool_alert( 'warn', __( 'نسخه‌ی تازه‌تری از دمو در دسترس است.', 'zarincoach' ), sprintf( /* translators: 1: نسخه نصب‌شده 2: نسخه جدید */ __( 'نسخه‌ی نصب‌شده %1$s و نسخه‌ی بسته‌ی قالب %2$s است. با نصب دوباره، محتوای دمو به‌روز می‌شود.', 'zarincoach' ), '<b>' . esc_html( $version ) . '</b>', '<b>' . esc_html( $pack ) . '</b>' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<div class="zc-demo-intro">
			<div class="zc-demo-intro-text">
				<span class="zc-demo-chip"><i class="fa-solid fa-bolt" aria-hidden="true"></i><?php esc_html_e( 'نصب یک‌کلیکی', 'zarincoach' ); ?></span>
				<h3><?php esc_html_e( 'سایت کامل مریم جمالی، آماده در کمتر از یک دقیقه', 'zarincoach' ); ?></h3>
				<p><?php esc_html_e( 'برگه‌ها، صفحات قوانین، نوشته‌ها با دیدگاه، خدمات، بازخوردها، پرسش‌ها، کتابخانه‌ی طرحواره‌ها، فروشگاه، منوها و طراحی کامل المنتور — همه با اطلاعات و تصاویر واقعی.', 'zarincoach' ); ?></p>
			</div>
			<ul class="zc-demo-facts">
				<li><i class="fa-solid fa-rotate" aria-hidden="true"></i><span><strong><?php esc_html_e( 'نصب از صفر', 'zarincoach' ); ?></strong><?php esc_html_e( 'بدون هیچ محتوای تکراری', 'zarincoach' ); ?></span></li>
				<li><i class="fa-solid fa-shield-halved" aria-hidden="true"></i><span><strong><?php esc_html_e( 'امن برای محتوای شما', 'zarincoach' ); ?></strong><?php esc_html_e( 'فقط موارد دمو بازسازی می‌شوند', 'zarincoach' ); ?></span></li>
				<li><i class="fa-solid fa-list-check" aria-hidden="true"></i><span><strong><?php echo esc_html( sprintf( /* translators: %s: تعداد گام */ __( '%s گام شفاف', 'zarincoach' ), zc_tool_num( count( $steps ) ) ) ); ?></strong><?php esc_html_e( 'با نمایش پیشرفت هر گام', 'zarincoach' ); ?></span></li>
			</ul>
		</div>

		<form id="zc-demo-form" class="zc-demo-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="zc_install_demo">
			<?php wp_nonce_field( 'zc_install_demo_action', 'zc_install_demo_nonce' ); ?>

			<div class="zc-step">
				<div class="zc-step-head"><span class="zc-step-num">۱</span><div><h3><?php esc_html_e( 'پالت رنگی', 'zarincoach' ); ?></h3><p><?php esc_html_e( 'رنگ‌بندی کل سایت؛ بعداً از «تنظیمات قالب ← رنگ‌ها» هم قابل تغییر است.', 'zarincoach' ); ?></p></div></div>
				<div class="zc-palettes">
					<?php foreach ( $palettes as $key => $palette ) : ?>
						<label class="zc-palette">
							<input type="radio" name="palette" value="<?php echo esc_attr( $key ); ?>" <?php checked( $installed ? $current : 'navy', $key ); ?>>
							<span class="zc-palette-box">
								<span class="zc-swatches" aria-hidden="true">
									<?php foreach ( array( 'secondary', 'primary', 'accent', 'info', 'base' ) as $c ) : ?>
										<i style="background:<?php echo esc_attr( $palette['light'][ $c ] ); ?>"></i>
									<?php endforeach; ?>
								</span>
								<span class="zc-palette-name"><?php echo esc_html( $palette['label'] ); ?></span>
								<?php if ( ! empty( $palette['note'] ) ) : ?>
									<span class="zc-palette-note"><?php echo esc_html( $palette['note'] ); ?></span>
								<?php endif; ?>
								<span class="zc-palette-tick" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
								<?php if ( 'navy' === $key ) : ?>
									<em><?php esc_html_e( 'پیشنهادی دمو', 'zarincoach' ); ?></em>
								<?php endif; ?>
							</span>
						</label>
					<?php endforeach; ?>
				</div>
			</div>

			<div class="zc-step">
				<div class="zc-step-head"><span class="zc-step-num">۲</span><div><h3><?php esc_html_e( 'صفحه‌ساز', 'zarincoach' ); ?></h3><p><?php esc_html_e( 'نحوه‌ی ساخت برگه‌ها، سربرگ و پاورقی.', 'zarincoach' ); ?></p></div></div>
				<?php if ( $elementor ) : ?>
					<label class="zc-switch-card">
						<input type="checkbox" name="elementor" value="1" checked>
						<span class="zc-switch" aria-hidden="true"></span>
						<span class="zc-switch-text">
							<strong><?php esc_html_e( 'طراحی با ویجت‌های اختصاصی المنتور', 'zarincoach' ); ?> <?php echo zc_tool_tag( __( 'پیشنهادی', 'zarincoach' ), 'gold' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></strong>
							<small>
								<?php
								/* translators: %s: تعداد ویجت */
								echo esc_html( sprintf( __( 'همه‌ی برگه‌ها، صفحات قوانین، سربرگ و پاورقی با %s ویجت اختصاصی زرین‌کوچ ساخته می‌شوند و به‌صورت بصری قابل ویرایش‌اند.', 'zarincoach' ), zc_tool_num( zc_widget_count() ) ) );
								?>
							</small>
						</span>
					</label>
				<?php else : ?>
					<?php
					echo zc_tool_alert( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						'warn',
						__( 'المنتور نصب یا فعال نیست.', 'zarincoach' ),
						__( 'دمو با طراحی داخلی قالب نصب می‌شود و همه‌ی بخش‌ها از پنل تنظیمات قابل ویرایش‌اند. برای طراحی بصری، ابتدا المنتور را نصب کنید.', 'zarincoach' ),
						'<p class="zc-tool-alert-actions"><a class="zc-btn zc-btn--ghost" href="' . esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' ) ) . '"><i class="fa-solid fa-plug" aria-hidden="true"></i>' . esc_html__( 'نصب المنتور', 'zarincoach' ) . '</a></p>'
					);
					?>
				<?php endif; ?>
			</div>

			<div class="zc-step zc-step--last">
				<div class="zc-step-head"><span class="zc-step-num">۳</span><div><h3><?php esc_html_e( 'شروع نصب', 'zarincoach' ); ?></h3><p><?php esc_html_e( 'ابتدا همه‌ی موارد دموی قبلی پاک و سپس از نو ساخته می‌شوند؛ پس هیچ محتوای تکراری ایجاد نمی‌شود.', 'zarincoach' ); ?></p></div></div>
				<div class="zc-keep">
					<span class="zc-keep-label"><i class="fa-solid fa-lock" aria-hidden="true"></i><?php esc_html_e( 'حفظ می‌شود:', 'zarincoach' ); ?></span>
					<?php
					foreach ( array( __( 'برگه‌ها و نوشته‌های خودتان', 'zarincoach' ), __( 'لوگو و نماد سایت', 'zarincoach' ), __( 'کدهای سفارشی', 'zarincoach' ), __( 'شماره‌های مجوز و نماد اعتماد', 'zarincoach' ), __( 'سفارش‌ها و مشتریان', 'zarincoach' ) ) as $zc_keep ) {
						echo zc_tool_tag( $zc_keep, 'muted' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
				<div class="zc-demo-cta">
					<button type="submit" class="zc-btn zc-btn--primary zc-btn--lg" id="zc-demo-start">
						<i class="fa-solid fa-wand-magic-sparkles" aria-hidden="true"></i>
						<?php echo $installed ? esc_html__( 'نصب دوباره / بروزرسانی دمو', 'zarincoach' ) : esc_html__( 'نصب دمو', 'zarincoach' ); ?>
					</button>
					<span class="zc-muted"><i class="fa-regular fa-clock" aria-hidden="true"></i> <?php esc_html_e( 'معمولاً ۳۰ تا ۹۰ ثانیه؛ تا پایان نصب صفحه را نبندید.', 'zarincoach' ); ?></span>
				</div>
			</div>
			<input type="hidden" name="force" value="1">
		</form>

		<div id="zc-demo-progress" class="zc-demo-progress" hidden>
			<div class="zc-demo-progress-head">
				<strong>
					<span class="zc-when-run"><i class="fa-solid fa-gear fa-spin" aria-hidden="true"></i><?php esc_html_e( 'در حال نصب دمو…', 'zarincoach' ); ?></span>
					<span class="zc-when-done"><i class="fa-solid fa-circle-check" aria-hidden="true"></i><?php esc_html_e( 'نصب کامل شد', 'zarincoach' ); ?></span>
					<span class="zc-when-error"><i class="fa-solid fa-circle-exclamation" aria-hidden="true"></i><?php esc_html_e( 'نصب متوقف شد؛ دوباره تلاش کنید', 'zarincoach' ); ?></span>
				</strong>
				<span id="zc-demo-pct" class="zc-demo-pct">۰٪</span>
			</div>
			<div class="zc-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'پیشرفت نصب', 'zarincoach' ); ?>"><span id="zc-demo-bar"></span></div>
			<ol class="zc-steps">
				<?php foreach ( $steps as $key => $label ) : ?>
					<li data-step="<?php echo esc_attr( $key ); ?>"><i></i><span><?php echo esc_html( $label ); ?></span><small></small></li>
				<?php endforeach; ?>
			</ol>
			<div id="zc-demo-done" class="zc-demo-done" hidden>
				<span class="zc-demo-done-icon" aria-hidden="true"><i class="fa-solid fa-check"></i></span>
				<div>
					<strong><?php esc_html_e( 'دمو با موفقیت نصب شد!', 'zarincoach' ); ?></strong>
					<p><?php esc_html_e( 'همه‌ی گام‌ها کامل شد. سایت را ببینید یا شخصی‌سازی را شروع کنید.', 'zarincoach' ); ?></p>
					<p class="zc-tool-alert-actions">
						<a class="zc-btn zc-btn--primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square" aria-hidden="true"></i><?php esc_html_e( 'مشاهده سایت', 'zarincoach' ); ?></a>
						<?php if ( $elementor ) : ?>
							<a class="zc-btn zc-btn--ghost" id="zc-demo-edit" href="<?php echo esc_url( admin_url( 'post.php?action=elementor&post=' . $home_id ) ); ?>"><i class="fa-solid fa-pen-to-square" aria-hidden="true"></i><?php esc_html_e( 'ویرایش خانه با المنتور', 'zarincoach' ); ?></a>
						<?php endif; ?>
						<a class="zc-btn zc-btn--ghost" href="<?php echo esc_url( admin_url( 'admin.php?page=zc-options' ) ); ?>"><i class="fa-solid fa-sliders" aria-hidden="true"></i><?php esc_html_e( 'تنظیمات قالب', 'zarincoach' ); ?></a>
					</p>
				</div>
			</div>
		</div>
		<?php
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- محتوا */
		zc_tool_pane_open( 'content', __( 'محتوای بسته‌ی دمو', 'zarincoach' ), __( 'آنچه با نصب دمو ساخته می‌شود؛ اعداد مستقیماً از بسته‌ی دموی همین نسخه‌ی قالب شمرده می‌شوند.', 'zarincoach' ) );
		?>
		<div class="zc-demo-grid">
			<div class="zc-inventory">
				<?php foreach ( $inventory as $key => $item ) : ?>
					<div class="zc-inv">
						<i class="<?php echo esc_attr( $item[2] ); ?>" aria-hidden="true"></i>
						<strong><?php echo esc_html( zc_tool_num( $item[0] ) ); ?></strong>
						<span><?php echo esc_html( $item[1] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
			<figure class="zc-browser">
				<div class="zc-browser-bar"><i></i><i></i><i></i><span><?php echo esc_html( wp_parse_url( home_url(), PHP_URL_HOST ) ); ?></span></div>
				<div class="zc-browser-body"><img src="<?php echo esc_url( $shot ); ?>" alt="<?php esc_attr_e( 'پیش‌نمایش دمو', 'zarincoach' ); ?>" loading="lazy" decoding="async"></div>
			</figure>
		</div>

		<?php zc_tool_heading( __( 'گام‌های نصب', 'zarincoach' ), esc_html( sprintf( /* translators: %s: نسخه */ __( 'بسته‌ی دمو نسخه %s', 'zarincoach' ), zc_tool_num( '' !== $pack ? $pack : ZC_VERSION ) ) ) ); ?>
		<ol class="zc-plan">
			<?php foreach ( $steps as $key => $label ) : ?>
				<li><?php echo esc_html( $label ); ?></li>
			<?php endforeach; ?>
		</ol>

		<?php if ( $installed ) : ?>
			<?php zc_tool_heading( __( 'وضعیت فعلی', 'zarincoach' ) ); ?>
			<div class="zc-kv-grid">
				<div class="zc-kv"><span><?php esc_html_e( 'نسخه‌ی نصب‌شده', 'zarincoach' ); ?></span><strong><?php echo esc_html( '' !== $version ? $version : '1.0' ); ?></strong></div>
				<?php if ( $at ) : ?>
					<div class="zc-kv"><span><?php esc_html_e( 'تاریخ نصب', 'zarincoach' ); ?></span><strong><?php echo esc_html( zc_tool_date( $at ) ); ?></strong></div>
				<?php endif; ?>
				<div class="zc-kv"><span><?php esc_html_e( 'پالت فعال', 'zarincoach' ); ?></span><strong><?php echo esc_html( isset( $palettes[ $current ] ) ? $palettes[ $current ]['label'] : $current ); ?></strong></div>
			</div>
		<?php endif; ?>
		<?php
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- حذف */
		zc_tool_pane_open( 'remove', __( 'حذف دمو', 'zarincoach' ), __( 'پاک‌سازی همه‌ی مواردی که نصب‌کننده‌ی دمو ساخته است؛ محتوای خودتان دست‌نخورده می‌ماند.', 'zarincoach' ) );

		if ( ! $installed ) {
			echo zc_tool_alert( 'info', __( 'در حال حاضر دمویی نصب نیست.', 'zarincoach' ), __( 'اگر پیش‌تر دمو را نصب و سپس بخشی از آن را دستی حذف کرده‌اید، این ابزار باقی‌مانده‌ها را هم پاک می‌کند.', 'zarincoach' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
		?>
		<div class="zc-compare">
			<div class="zc-compare-col is-danger">
				<h4><i class="fa-solid fa-trash-can" aria-hidden="true"></i><?php esc_html_e( 'حذف می‌شود', 'zarincoach' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'برگه‌ها، صفحات قوانین و نوشته‌های دمو با دیدگاه‌هایشان', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'خدمات، بازخوردها، پرسش‌ها و کتابخانه‌ی طرحواره‌ها', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'تصاویر دمو، دسته‌ها، برچسب‌ها و منوها', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'قالب‌های سربرگ و پاورقی و محصولات نمونه‌ی دمو', 'zarincoach' ); ?></li>
				</ul>
			</div>
			<div class="zc-compare-col is-ok">
				<h4><i class="fa-solid fa-lock" aria-hidden="true"></i><?php esc_html_e( 'حفظ می‌شود', 'zarincoach' ); ?></h4>
				<ul>
					<li><?php esc_html_e( 'هر محتوایی که خودتان ساخته‌اید', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'سفارش‌ها، مشتریان و پیام‌های فرم تماس', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'تنظیمات پنل قالب (مگر گزینه‌ی بازنشانی را بزنید)', 'zarincoach' ); ?></li>
					<li><?php esc_html_e( 'افزونه‌ها و کاربران', 'zarincoach' ); ?></li>
				</ul>
			</div>
		</div>

		<form class="zc-remove-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" onsubmit="return confirm('<?php echo esc_js( __( 'آیا از حذف محتوای دمو مطمئن هستید؟ این عملیات غیرقابل بازگشت است.', 'zarincoach' ) ); ?>');">
			<input type="hidden" name="action" value="zc_remove_demo">
			<?php wp_nonce_field( 'zc_remove_demo_action', 'zc_remove_demo_nonce' ); ?>
			<label class="zc-switch-card">
				<input type="checkbox" name="reset_options" value="1">
				<span class="zc-switch" aria-hidden="true"></span>
				<span class="zc-switch-text">
					<strong><?php esc_html_e( 'بازنشانی تنظیمات پنل قالب', 'zarincoach' ); ?></strong>
					<small><?php esc_html_e( 'همه‌ی تنظیمات «زرین‌کوچ» (رنگ‌ها، اطلاعات تماس، شبکه‌های اجتماعی و…) به حالت پیش‌فرض برمی‌گردند.', 'zarincoach' ); ?></small>
				</span>
			</label>
			<div class="zc-demo-cta">
				<button class="zc-btn zc-btn--danger" type="submit"><i class="fa-solid fa-trash-can" aria-hidden="true"></i><?php esc_html_e( 'حذف محتوای دمو', 'zarincoach' ); ?></button>
				<span class="zc-muted"><?php esc_html_e( 'این عملیات غیرقابل بازگشت است؛ پیش از آن از سایت پشتیبان بگیرید.', 'zarincoach' ); ?></span>
			</div>
		</form>
		<?php
		zc_tool_pane_close();
		zc_tool_shell_close();
		?>
		<script>
		(function () {
			var form = document.getElementById('zc-demo-form');
			if (!form || !window.fetch) { return; }
			var steps = <?php echo wp_json_encode( array_keys( $steps ) ); ?>;
			var ajax  = <?php echo wp_json_encode( admin_url( 'admin-ajax.php' ) ); ?>;
			var nonce = <?php echo wp_json_encode( wp_create_nonce( 'zc_demo_import' ) ); ?>;
			var failText = <?php echo wp_json_encode( __( 'خطا در اجرای این گام. دوباره تلاش کنید.', 'zarincoach' ) ); ?>;
			var fa = function (n) { return String(n).replace(/\d/g, function (d) { return '۰۱۲۳۴۵۶۷۸۹'[d]; }); };

			form.addEventListener('submit', function (e) {
				e.preventDefault();
				var btn = document.getElementById('zc-demo-start');
				var palette = (form.querySelector('input[name=palette]:checked') || {}).value || 'navy';
				var el = form.querySelector('input[name=elementor]');
				var useElementor = el ? (el.checked ? 1 : 0) : 0;
				var box = document.getElementById('zc-demo-progress');
				var bar = document.getElementById('zc-demo-bar');
				var pct = document.getElementById('zc-demo-pct');
				btn.disabled = true;
				form.classList.add('is-running');
				box.hidden = false;
				box.classList.remove('is-done', 'is-error');
				box.scrollIntoView({ behavior: 'smooth', block: 'start' });
				var i = 0;

				function progress(p) {
					bar.style.width = p + '%';
					if (pct) { pct.textContent = fa(p) + '٪'; }
					if (bar.parentNode) { bar.parentNode.setAttribute('aria-valuenow', p); }
				}

				function mark(step, state, msg) {
					var li = box.querySelector('li[data-step="' + step + '"]');
					if (!li) { return; }
					li.className = state;
					if (msg) { li.querySelector('small').textContent = msg; }
				}

				function run() {
					if (i >= steps.length) {
						progress(100);
						box.classList.add('is-done');
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
							progress(Math.round((i / steps.length) * 100));
							run();
						})
						.catch(function (err) {
							mark(step, 'is-error', err.message || failText);
							box.classList.add('is-error');
							btn.disabled = false;
							form.classList.remove('is-running');
						});
				}
				progress(0);
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

/* ---------------------------------------------------------------------
 * اطلاعات سیستم
 * ------------------------------------------------------------------- */

if ( ! function_exists( 'zc_sys_bytes' ) ) :
	/**
	 * مقدار ini به بایت (‎-1 = نامحدود).
	 *
	 * @param string $value مقدار.
	 * @return int
	 */
	function zc_sys_bytes( $value ) {
		$value = trim( (string) $value );
		if ( '' === $value ) {
			return 0;
		}
		if ( '-1' === $value ) {
			return -1;
		}
		return (int) wp_convert_hr_to_bytes( $value );
	}
endif;

if ( ! function_exists( 'zc_sys_yesno' ) ) :
	/**
	 * متن بله/خیر.
	 *
	 * @param bool $on وضعیت.
	 * @return string
	 */
	function zc_sys_yesno( $on ) {
		return $on ? __( 'فعال', 'zarincoach' ) : __( 'غیرفعال', 'zarincoach' );
	}
endif;

if ( ! function_exists( 'zc_system_checks' ) ) :
	/**
	 * بررسی سلامت سایت برای قالب.
	 *
	 * @return array<string, array{label:string,value:string,state:string,hint:string,icon:string}>
	 */
	function zc_system_checks() {
		$checks = array();
		$add    = static function ( $key, $label, $value, $state, $hint, $icon ) use ( &$checks ) {
			$checks[ $key ] = compact( 'label', 'value', 'state', 'hint', 'icon' );
		};

		// PHP.
		$php = PHP_VERSION;
		$add(
			'php',
			__( 'نسخه‌ی PHP', 'zarincoach' ),
			$php,
			version_compare( $php, '8.1', '>=' ) ? 'ok' : ( version_compare( $php, '7.4', '>=' ) ? 'warn' : 'danger' ),
			version_compare( $php, '8.1', '>=' ) ? __( 'سریع و پشتیبانی‌شده.', 'zarincoach' ) : __( 'حداقل ۷٫۴ لازم است؛ ۸٫۱ یا بالاتر برای سرعت و امنیت پیشنهاد می‌شود.', 'zarincoach' ),
			'fa-brands fa-php'
		);

		// وردپرس.
		$wp = get_bloginfo( 'version' );
		$add(
			'wp',
			__( 'نسخه‌ی وردپرس', 'zarincoach' ),
			$wp,
			version_compare( $wp, '6.4', '>=' ) ? 'ok' : ( version_compare( $wp, '6.0', '>=' ) ? 'warn' : 'danger' ),
			version_compare( $wp, '6.4', '>=' ) ? __( 'به‌روز و سازگار با قالب.', 'zarincoach' ) : __( 'به‌روزرسانی وردپرس پیشنهاد می‌شود (حداقل ۶٫۰).', 'zarincoach' ),
			'fa-brands fa-wordpress'
		);

		// حافظه.
		$mem = zc_sys_bytes( ini_get( 'memory_limit' ) );
		$add(
			'memory',
			__( 'حافظه‌ی PHP', 'zarincoach' ),
			-1 === $mem ? __( 'نامحدود', 'zarincoach' ) : size_format( $mem ),
			( -1 === $mem || $mem >= 256 * MB_IN_BYTES ) ? 'ok' : ( $mem >= 128 * MB_IN_BYTES ? 'warn' : 'danger' ),
			( -1 === $mem || $mem >= 256 * MB_IN_BYTES ) ? __( 'برای المنتور و ووکامرس کافی است.', 'zarincoach' ) : __( 'برای المنتور و ووکامرس ۲۵۶ مگابایت پیشنهاد می‌شود (WP_MEMORY_LIMIT).', 'zarincoach' ),
			'fa-solid fa-memory'
		);

		// زمان اجرا.
		$time = (int) ini_get( 'max_execution_time' );
		$add(
			'time',
			__( 'حداکثر زمان اجرا', 'zarincoach' ),
			0 === $time ? __( 'نامحدود', 'zarincoach' ) : sprintf( /* translators: %s: ثانیه */ __( '%s ثانیه', 'zarincoach' ), $time ),
			( 0 === $time || $time >= 60 ) ? 'ok' : 'warn',
			( 0 === $time || $time >= 60 ) ? __( 'برای نصب دمو و درون‌ریزی کافی است.', 'zarincoach' ) : __( 'نصب دمو گام‌به‌گام است، اما ۶۰ ثانیه یا بیشتر مطمئن‌تر است.', 'zarincoach' ),
			'fa-solid fa-stopwatch'
		);

		// حجم بارگذاری.
		$upload = (int) wp_max_upload_size();
		$add(
			'upload',
			__( 'حداکثر حجم بارگذاری', 'zarincoach' ),
			size_format( $upload ),
			$upload >= 16 * MB_IN_BYTES ? 'ok' : 'warn',
			$upload >= 16 * MB_IN_BYTES ? __( 'برای تصاویر و فایل‌های دانلودی کافی است.', 'zarincoach' ) : __( 'برای محصولات دانلودی دست‌کم ۱۶ مگابایت پیشنهاد می‌شود.', 'zarincoach' ),
			'fa-solid fa-cloud-arrow-up'
		);

		// HTTPS.
		$ssl = is_ssl() || 0 === strpos( home_url(), 'https://' );
		$add(
			'https',
			__( 'اتصال امن (HTTPS)', 'zarincoach' ),
			$ssl ? __( 'فعال', 'zarincoach' ) : __( 'غیرفعال', 'zarincoach' ),
			$ssl ? 'ok' : 'warn',
			$ssl ? __( 'برای امنیت، اعتماد مراجعان و سئو ضروری است.', 'zarincoach' ) : __( 'گواهی SSL را فعال و نشانی سایت را به https تغییر دهید.', 'zarincoach' ),
			'fa-solid fa-lock'
		);

		// نمایه‌شدن.
		$public = '0' !== (string) get_option( 'blog_public', '1' );
		$add(
			'index',
			__( 'نمایش در موتورهای جستجو', 'zarincoach' ),
			$public ? __( 'مجاز', 'zarincoach' ) : __( 'منع شده', 'zarincoach' ),
			$public ? 'ok' : 'warn',
			$public ? __( 'گوگل می‌تواند سایت را نمایه کند.', 'zarincoach' ) : __( 'تیک «منع موتورهای جستجو» در تنظیمات ← خواندن را بردارید.', 'zarincoach' ),
			'fa-brands fa-google'
		);

		// پیوند یکتا.
		$perma = (string) get_option( 'permalink_structure' );
		$add(
			'permalink',
			__( 'پیوندهای یکتا', 'zarincoach' ),
			'' !== $perma ? $perma : __( 'ساده (?p=)', 'zarincoach' ),
			'' !== $perma ? 'ok' : 'warn',
			'' !== $perma ? __( 'نشانی‌های خوانا و مناسب سئو.', 'zarincoach' ) : __( 'در تنظیمات ← پیوندهای یکتا، «نام نوشته» را انتخاب کنید.', 'zarincoach' ),
			'fa-solid fa-link'
		);

		// mbstring.
		$mb = extension_loaded( 'mbstring' );
		$add(
			'mbstring',
			__( 'افزونه‌ی mbstring', 'zarincoach' ),
			zc_sys_yesno( $mb ),
			$mb ? 'ok' : 'danger',
			$mb ? __( 'پردازش درست متن فارسی.', 'zarincoach' ) : __( 'برای متن فارسی لازم است؛ از میزبان بخواهید فعالش کند.', 'zarincoach' ),
			'fa-solid fa-language'
		);

		// پردازش تصویر.
		$editor = '';
		if ( extension_loaded( 'imagick' ) ) {
			$editor = 'Imagick';
		} elseif ( extension_loaded( 'gd' ) ) {
			$editor = 'GD';
		}
		$webp = function_exists( 'wp_image_editor_supports' ) && wp_image_editor_supports( array( 'mime_type' => 'image/webp' ) );
		$add(
			'image',
			__( 'پردازش تصویر', 'zarincoach' ),
			'' !== $editor ? $editor . ( $webp ? ' · WebP' : '' ) : __( 'در دسترس نیست', 'zarincoach' ),
			'' === $editor ? 'danger' : ( $webp ? 'ok' : 'warn' ),
			'' === $editor ? __( 'بدون GD یا Imagick، اندازه‌های تصویر ساخته نمی‌شوند.', 'zarincoach' ) : ( $webp ? __( 'ساخت اندازه‌های تصویر و پشتیبانی WebP.', 'zarincoach' ) : __( 'پشتیبانی WebP برای تصاویر سبک‌تر پیشنهاد می‌شود.', 'zarincoach' ) ),
			'fa-solid fa-image'
		);

		// پوشه‌ی بارگذاری.
		$uploads  = wp_upload_dir( null, false );
		$writable = ! empty( $uploads['basedir'] ) && wp_is_writable( $uploads['basedir'] );
		$add(
			'uploads',
			__( 'پوشه‌ی بارگذاری', 'zarincoach' ),
			$writable ? __( 'قابل نوشتن', 'zarincoach' ) : __( 'غیرقابل نوشتن', 'zarincoach' ),
			$writable ? 'ok' : 'danger',
			$writable ? __( 'تصاویر دمو و رسانه‌ها ذخیره می‌شوند.', 'zarincoach' ) : __( 'دسترسی نوشتن پوشه‌ی wp-content/uploads را بررسی کنید.', 'zarincoach' ),
			'fa-solid fa-folder-open'
		);

		// Redux.
		$redux = zc_redux_available();
		$add(
			'redux',
			__( 'پنل تنظیمات (Redux)', 'zarincoach' ),
			$redux ? __( 'در دسترس', 'zarincoach' ) : __( 'در دسترس نیست', 'zarincoach' ),
			$redux ? 'ok' : 'danger',
			$redux ? __( 'نسخه‌ی توکار قالب بارگذاری شده است.', 'zarincoach' ) : __( 'پوشه‌ی inc/redux کامل نیست؛ قالب را دوباره بارگذاری کنید.', 'zarincoach' ),
			'fa-solid fa-sliders'
		);

		// فایل‌های ساخته‌شده.
		$css_ok  = file_exists( ZC_DIR . '/assets/css/main.css' ) && filesize( ZC_DIR . '/assets/css/main.css' ) > 0;
		$font_ok = file_exists( ZC_DIR . '/assets/fonts/AradFD-VF.woff2' );
		$add(
			'assets',
			__( 'فایل‌های قالب و فونت آراد', 'zarincoach' ),
			( $css_ok && $font_ok ) ? __( 'کامل', 'zarincoach' ) : __( 'ناقص', 'zarincoach' ),
			( $css_ok && $font_ok ) ? 'ok' : 'danger',
			( $css_ok && $font_ok ) ? __( 'استایل فشرده و فونت متغیر آراد موجود است.', 'zarincoach' ) : __( 'فایل‌های assets ناقص‌اند؛ بسته‌ی قالب را دوباره نصب کنید.', 'zarincoach' ),
			'fa-solid fa-font'
		);

		// المنتور.
		$el = zc_is_elementor_active();
		$add(
			'elementor',
			__( 'المنتور', 'zarincoach' ),
			$el ? ( defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : __( 'فعال', 'zarincoach' ) ) : __( 'غیرفعال', 'zarincoach' ),
			$el ? 'ok' : 'warn',
			$el ? sprintf( /* translators: %s: تعداد */ __( '%s ویجت اختصاصی زرین‌کوچ در دسترس است.', 'zarincoach' ), zc_tool_num( zc_widget_count() ) ) : __( 'برای طراحی بصری همه‌ی بخش‌ها، المنتور را نصب کنید.', 'zarincoach' ),
			'fa-brands fa-elementor'
		);

		// المنتور پرو.
		$pro = defined( 'ELEMENTOR_PRO_VERSION' );
		$add(
			'elementor_pro',
			__( 'المنتور پرو', 'zarincoach' ),
			$pro ? ELEMENTOR_PRO_VERSION : __( 'نصب نیست', 'zarincoach' ),
			$pro ? 'ok' : 'info',
			$pro ? __( 'قالب‌ساز و شرط‌های نمایش پشتیبانی می‌شوند.', 'zarincoach' ) : __( 'اختیاری است؛ قالب بدون آن کامل کار می‌کند.', 'zarincoach' ),
			'fa-solid fa-crown'
		);

		// ووکامرس.
		$wc = class_exists( 'WooCommerce' );
		$add(
			'woocommerce',
			__( 'ووکامرس', 'zarincoach' ),
			$wc ? ( defined( 'WC_VERSION' ) ? WC_VERSION : __( 'فعال', 'zarincoach' ) ) : __( 'نصب نیست', 'zarincoach' ),
			$wc ? 'ok' : 'info',
			$wc ? __( 'فروشگاه، حساب کاربری و قالب‌های اختصاصی فعال‌اند.', 'zarincoach' ) : __( 'اختیاری؛ برای فروش کتاب، فایل و بسته‌ی جلسات.', 'zarincoach' ),
			'fa-solid fa-bag-shopping'
		);

		// Yoast.
		$yoast = defined( 'WPSEO_VERSION' );
		$add(
			'yoast',
			__( 'Yoast SEO', 'zarincoach' ),
			$yoast ? WPSEO_VERSION . ( defined( 'WPSEO_PREMIUM_VERSION' ) ? ' · Premium' : '' ) : __( 'نصب نیست', 'zarincoach' ),
			$yoast ? 'ok' : 'info',
			$yoast ? __( 'سئوی قالب با Yoast هماهنگ شده و تکراری خروجی نمی‌دهد.', 'zarincoach' ) : __( 'اختیاری؛ سئو و اسکیمای داخلی قالب فعال است.', 'zarincoach' ),
			'fa-solid fa-magnifying-glass-chart'
		);

		// حالت اشکال‌زدایی.
		$debug   = defined( 'WP_DEBUG' ) && WP_DEBUG;
		$display = $debug && ( ! defined( 'WP_DEBUG_DISPLAY' ) || WP_DEBUG_DISPLAY );
		$add(
			'debug',
			__( 'حالت اشکال‌زدایی', 'zarincoach' ),
			$debug ? ( $display ? __( 'فعال · نمایش خطا', 'zarincoach' ) : __( 'فعال · فقط گزارش', 'zarincoach' ) ) : __( 'غیرفعال', 'zarincoach' ),
			$display ? 'warn' : ( $debug ? 'info' : 'ok' ),
			$display ? __( 'روی سایت واقعی WP_DEBUG_DISPLAY را خاموش کنید.', 'zarincoach' ) : __( 'خطاها به بازدیدکنندگان نمایش داده نمی‌شوند.', 'zarincoach' ),
			'fa-solid fa-bug'
		);

		// کش اشیا.
		$cache = wp_using_ext_object_cache();
		$add(
			'object_cache',
			__( 'کش اشیا', 'zarincoach' ),
			zc_sys_yesno( $cache ),
			$cache ? 'ok' : 'info',
			$cache ? __( 'Redis/Memcached فعال است.', 'zarincoach' ) : __( 'اختیاری؛ برای سایت‌های پربازدید Redis پیشنهاد می‌شود.', 'zarincoach' ),
			'fa-solid fa-bolt'
		);

		// کران.
		$cron_off = defined( 'DISABLE_WP_CRON' ) && DISABLE_WP_CRON;
		$add(
			'cron',
			__( 'زمان‌بندی وردپرس (Cron)', 'zarincoach' ),
			$cron_off ? __( 'کران سیستمی', 'zarincoach' ) : __( 'فعال', 'zarincoach' ),
			$cron_off ? 'info' : 'ok',
			$cron_off ? __( 'مطمئن شوید کران سرور wp-cron.php را اجرا می‌کند (لغو خودکار سفارش‌های کارت‌به‌کارت).', 'zarincoach' ) : __( 'وظایف زمان‌بندی‌شده اجرا می‌شوند.', 'zarincoach' ),
			'fa-regular fa-clock'
		);

		// دمو.
		$demo = '1' === (string) get_option( 'zc_demo_installed' );
		$add(
			'demo',
			__( 'دموی قالب', 'zarincoach' ),
			$demo ? sprintf( /* translators: %s: نسخه */ __( 'نصب‌شده · %s', 'zarincoach' ), (string) get_option( 'zc_demo_version', '1.0' ) ) : __( 'نصب نشده', 'zarincoach' ),
			$demo ? 'ok' : 'info',
			$demo ? __( 'محتوای نمونه آماده‌ی ویرایش است.', 'zarincoach' ) : __( 'از بخش «نصب دمو» با یک کلیک نصب کنید.', 'zarincoach' ),
			'fa-solid fa-wand-magic-sparkles'
		);

		/**
		 * فیلتر بررسی‌های سلامت صفحه‌ی اطلاعات سیستم.
		 *
		 * @param array $checks بررسی‌ها.
		 */
		return (array) apply_filters( 'zc_system_checks', $checks );
	}
endif;

if ( ! function_exists( 'zc_system_sections' ) ) :
	/**
	 * جدول‌های اطلاعات (برای نمایش و گزارش متنی).
	 *
	 * @return array<string, array{title:string,icon:string,rows:array<string,string>}>
	 */
	function zc_system_sections() {
		global $wpdb;

		$theme   = wp_get_theme();
		$parent  = $theme->parent();
		$uploads = wp_upload_dir( null, false );
		$palette = (string) zc_opt( 'palette_preset', 'navy' );
		$pals    = zc_palettes();
		$dark    = array(
			'off'     => __( 'غیرفعال', 'zarincoach' ),
			'toggle'  => __( 'با کلید در سربرگ', 'zarincoach' ),
			'auto'    => __( 'خودکار', 'zarincoach' ),
			'enforce' => __( 'همیشه تاریک', 'zarincoach' ),
		);
		$dark_v  = (string) zc_opt( 'general_dark_mode', 'toggle' );

		$exts = array();
		foreach ( array( 'mbstring', 'intl', 'curl', 'gd', 'imagick', 'zip', 'openssl', 'json', 'xml', 'dom', 'fileinfo', 'exif', 'opcache' => 'Zend OPcache' ) as $k => $ext ) {
			$name   = is_int( $k ) ? $ext : $k;
			$exts[] = ( extension_loaded( $ext ) ? '✓ ' : '✗ ' ) . $name;
		}

		$sections = array(
			'wordpress' => array(
				'title' => __( 'وردپرس', 'zarincoach' ),
				'icon'  => 'fa-brands fa-wordpress',
				'rows'  => array(
					__( 'نشانی سایت', 'zarincoach' )        => home_url( '/' ),
					__( 'نشانی وردپرس', 'zarincoach' )      => site_url( '/' ),
					__( 'نسخه', 'zarincoach' )              => get_bloginfo( 'version' ),
					__( 'زبان', 'zarincoach' )              => get_locale() . ( is_rtl() ? ' · RTL' : '' ),
					__( 'منطقه‌ی زمانی', 'zarincoach' )      => wp_timezone_string(),
					__( 'چندسایته', 'zarincoach' )          => is_multisite() ? __( 'بله', 'zarincoach' ) : __( 'خیر', 'zarincoach' ),
					__( 'پیوند یکتا', 'zarincoach' )        => (string) get_option( 'permalink_structure' ),
					__( 'WP_MEMORY_LIMIT', 'zarincoach' )   => WP_MEMORY_LIMIT,
					__( 'WP_DEBUG', 'zarincoach' )          => zc_sys_yesno( defined( 'WP_DEBUG' ) && WP_DEBUG ),
					__( 'کش اشیا', 'zarincoach' )           => zc_sys_yesno( wp_using_ext_object_cache() ),
					__( 'پوشه‌ی بارگذاری', 'zarincoach' )    => ! empty( $uploads['basedir'] ) ? str_replace( ABSPATH, '/', $uploads['basedir'] ) : '—',
				),
			),
			'server'    => array(
				'title' => __( 'سرور و PHP', 'zarincoach' ),
				'icon'  => 'fa-solid fa-server',
				'rows'  => array(
					__( 'نرم‌افزار سرور', 'zarincoach' )     => isset( $_SERVER['SERVER_SOFTWARE'] ) ? sanitize_text_field( wp_unslash( $_SERVER['SERVER_SOFTWARE'] ) ) : '—',
					__( 'سیستم‌عامل', 'zarincoach' )        => PHP_OS,
					__( 'نسخه‌ی PHP', 'zarincoach' )         => PHP_VERSION . ' (' . PHP_SAPI . ')',
					'memory_limit'                         => (string) ini_get( 'memory_limit' ),
					'max_execution_time'                   => (string) ini_get( 'max_execution_time' ),
					'upload_max_filesize'                  => (string) ini_get( 'upload_max_filesize' ),
					'post_max_size'                        => (string) ini_get( 'post_max_size' ),
					'max_input_vars'                       => (string) ini_get( 'max_input_vars' ),
					__( 'افزونه‌های PHP', 'zarincoach' )     => implode( '  ', $exts ),
				),
			),
			'database'  => array(
				'title' => __( 'پایگاه داده', 'zarincoach' ),
				'icon'  => 'fa-solid fa-database',
				'rows'  => array(
					__( 'نسخه', 'zarincoach' )              => (string) $wpdb->get_var( 'SELECT VERSION()' ), // phpcs:ignore WordPress.DB.DirectDatabaseQuery
					__( 'پیشوند جدول‌ها', 'zarincoach' )     => $wpdb->prefix,
					__( 'کدگذاری', 'zarincoach' )           => $wpdb->charset . ( $wpdb->collate ? ' · ' . $wpdb->collate : '' ),
				),
			),
			'theme'     => array(
				'title' => __( 'قالب زرین‌کوچ', 'zarincoach' ),
				'icon'  => 'fa-solid fa-palette',
				'rows'  => array(
					__( 'قالب فعال', 'zarincoach' )         => $theme->get( 'Name' ) . ' ' . $theme->get( 'Version' ),
					__( 'قالب مادر', 'zarincoach' )         => $parent ? $parent->get( 'Name' ) . ' ' . $parent->get( 'Version' ) : __( 'ندارد (قالب اصلی فعال است)', 'zarincoach' ),
					__( 'نسخه‌ی هسته‌ی قالب', 'zarincoach' ) => ZC_VERSION,
					__( 'پالت رنگی', 'zarincoach' )         => isset( $pals[ $palette ] ) ? $pals[ $palette ]['label'] : $palette,
					__( 'حالت تاریک', 'zarincoach' )        => isset( $dark[ $dark_v ] ) ? $dark[ $dark_v ] : $dark_v,
					__( 'فونت', 'zarincoach' )              => zc_switch( 'typo_persian_digits', true ) ? __( 'آراد متغیر (ارقام فارسی)', 'zarincoach' ) : __( 'آراد متغیر', 'zarincoach' ),
					__( 'ویجت‌های المنتور', 'zarincoach' )   => zc_tool_num( zc_widget_count() ),
					__( 'سربرگ', 'zarincoach' )             => function_exists( 'zc_layout_source' ) ? zc_layout_source( 'header' )['label'] : '—',
					__( 'پاورقی', 'zarincoach' )            => function_exists( 'zc_layout_source' ) ? zc_layout_source( 'footer' )['label'] : '—',
					__( 'دمو', 'zarincoach' )               => '1' === (string) get_option( 'zc_demo_installed' ) ? sprintf( /* translators: %s: نسخه */ __( 'نصب‌شده · %s', 'zarincoach' ), (string) get_option( 'zc_demo_version', '1.0' ) ) : __( 'نصب نشده', 'zarincoach' ),
				),
			),
		);

		return $sections;
	}
endif;

if ( ! function_exists( 'zc_system_assets' ) ) :
	/**
	 * حجم فایل‌های خروجی قالب.
	 *
	 * @return array<string, int> مسیر نسبی => بایت.
	 */
	function zc_system_assets() {
		$files = array(
			'assets/css/main.css',
			'assets/css/shop-core.css',
			'assets/css/shop.css',
			'assets/css/account.css',
			'assets/js/main.min.js',
			'assets/js/shop.min.js',
			'assets/fonts/AradFD-VF.woff2',
			'assets/fonts/Arad-VF.woff2',
		);
		$out   = array();
		foreach ( $files as $file ) {
			$path         = ZC_DIR . '/' . $file;
			$out[ $file ] = file_exists( $path ) ? (int) filesize( $path ) : -1;
		}
		return $out;
	}
endif;

if ( ! function_exists( 'zc_system_plugins' ) ) :
	/**
	 * افزونه‌های نصب‌شده با وضعیت.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function zc_system_plugins() {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}
		$all     = get_plugins();
		$active  = (array) get_option( 'active_plugins', array() );
		$updates = get_site_transient( 'update_plugins' );
		$roles   = array(
			'elementor'             => __( 'صفحه‌ساز', 'zarincoach' ),
			'elementor-pro'         => __( 'قالب‌ساز', 'zarincoach' ),
			'woocommerce'           => __( 'فروشگاه', 'zarincoach' ),
			'wordpress-seo'         => __( 'سئو', 'zarincoach' ),
			'wordpress-seo-premium' => __( 'سئو', 'zarincoach' ),
		);
		$list    = array();
		foreach ( $all as $file => $data ) {
			$dir    = dirname( $file );
			$list[] = array(
				'name'    => $data['Name'],
				'version' => $data['Version'],
				'author'  => wp_strip_all_tags( $data['AuthorName'] ? $data['AuthorName'] : $data['Author'] ),
				'active'  => in_array( $file, $active, true ) || ( is_multisite() && is_plugin_active_for_network( $file ) ),
				'update'  => ( is_object( $updates ) && isset( $updates->response[ $file ]->new_version ) ) ? (string) $updates->response[ $file ]->new_version : '',
				'role'    => isset( $roles[ $dir ] ) ? $roles[ $dir ] : '',
			);
		}
		usort(
			$list,
			static function ( $a, $b ) {
				if ( $a['active'] !== $b['active'] ) {
					return $a['active'] ? -1 : 1;
				}
				return strcasecmp( $a['name'], $b['name'] );
			}
		);
		return $list;
	}
endif;

if ( ! function_exists( 'zc_system_report' ) ) :
	/**
	 * گزارش متنی کامل برای پشتیبانی.
	 *
	 * @param array $checks   بررسی‌ها.
	 * @param array $sections جدول‌ها.
	 * @param array $plugins  افزونه‌ها.
	 * @param array $assets   فایل‌ها.
	 * @return string
	 */
	function zc_system_report( $checks, $sections, $plugins, $assets ) {
		$marks = array(
			'ok'     => '✓',
			'warn'   => '!',
			'danger' => '✗',
			'info'   => '·',
		);
		$lines = array( '### ZarinCoach System Report — ' . gmdate( 'Y-m-d H:i' ) . ' UTC ###', '' );

		$lines[] = '== ' . __( 'بررسی سلامت', 'zarincoach' ) . ' ==';
		foreach ( $checks as $check ) {
			$lines[] = sprintf( '[%s] %s: %s', isset( $marks[ $check['state'] ] ) ? $marks[ $check['state'] ] : '·', $check['label'], $check['value'] );
		}
		foreach ( $sections as $section ) {
			$lines[] = '';
			$lines[] = '== ' . $section['title'] . ' ==';
			foreach ( $section['rows'] as $label => $value ) {
				$lines[] = $label . ': ' . ( '' !== (string) $value ? $value : '—' );
			}
		}
		$lines[] = '';
		$lines[] = '== ' . __( 'فایل‌های قالب', 'zarincoach' ) . ' ==';
		foreach ( $assets as $file => $size ) {
			$lines[] = $file . ': ' . ( $size < 0 ? 'MISSING' : size_format( $size, 1 ) );
		}
		$lines[] = '';
		$lines[] = '== ' . __( 'افزونه‌ها', 'zarincoach' ) . ' ==';
		foreach ( $plugins as $plugin ) {
			$lines[] = sprintf( '[%s] %s %s%s', $plugin['active'] ? 'on' : 'off', $plugin['name'], $plugin['version'], '' !== $plugin['update'] ? ' → ' . $plugin['update'] : '' );
		}
		return implode( "\n", $lines );
	}
endif;

if ( ! function_exists( 'zc_render_system_page' ) ) :
	/**
	 * صفحه اطلاعات سیستم.
	 *
	 * @return void
	 */
	function zc_render_system_page() {
		$checks   = zc_system_checks();
		$sections = zc_system_sections();
		$plugins  = zc_system_plugins();
		$assets   = zc_system_assets();
		$report   = zc_system_report( $checks, $sections, $plugins, $assets );

		$tally = array(
			'ok'     => 0,
			'warn'   => 0,
			'danger' => 0,
			'info'   => 0,
		);
		foreach ( $checks as $check ) {
			if ( isset( $tally[ $check['state'] ] ) ) {
				++$tally[ $check['state'] ];
			}
		}
		$scored = $tally['ok'] + $tally['warn'] + $tally['danger'];
		$score  = $scored ? (int) round( ( $tally['ok'] + $tally['warn'] * 0.5 ) / $scored * 100 ) : 100;
		$attn   = $tally['warn'] + $tally['danger'];
		$tone   = $tally['danger'] ? 'danger' : ( $tally['warn'] ? 'warn' : 'ok' );

		$active_n = count(
			array_filter(
				$plugins,
				static function ( $p ) {
					return $p['active'];
				}
			)
		);
		$update_n = count(
			array_filter(
				$plugins,
				static function ( $p ) {
					return '' !== $p['update'];
				}
			)
		);

		// ترتیب نمایش: بحرانی، هشدار، سالم، اطلاعات.
		$order = array(
			'danger' => 0,
			'warn'   => 1,
			'ok'     => 2,
			'info'   => 3,
		);
		uasort(
			$checks,
			static function ( $a, $b ) use ( $order ) {
				return ( isset( $order[ $a['state'] ] ) ? $order[ $a['state'] ] : 9 ) <=> ( isset( $order[ $b['state'] ] ) ? $order[ $b['state'] ] : 9 );
			}
		);

		$state_icons = array(
			'ok'     => 'fa-solid fa-circle-check',
			'warn'   => 'fa-solid fa-triangle-exclamation',
			'danger' => 'fa-solid fa-circle-xmark',
			'info'   => 'fa-solid fa-circle-info',
		);

		$side  = '<p class="zc-tool-side-title">' . esc_html__( 'ابزارهای وردپرس', 'zarincoach' ) . '</p>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'site-health.php' ) ) . '"><i class="fa-solid fa-heart-pulse" aria-hidden="true"></i>' . esc_html__( 'سلامت سایت وردپرس', 'zarincoach' ) . '</a>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'update-core.php' ) ) . '"><i class="fa-solid fa-arrows-rotate" aria-hidden="true"></i>' . esc_html__( 'به‌روزرسانی‌ها', 'zarincoach' ) . '</a>';
		$side .= '<a class="zc-tool-side-link" href="' . esc_url( admin_url( 'options-permalink.php' ) ) . '"><i class="fa-solid fa-link" aria-hidden="true"></i>' . esc_html__( 'پیوندهای یکتا', 'zarincoach' ) . '</a>';

		zc_tool_shell_open(
			'zc-system-info',
			array(
				'title'    => __( 'اطلاعات سیستم', 'zarincoach' ),
				'subtitle' => __( 'سلامت سایت و گزارش پشتیبانی', 'zarincoach' ),
				'icon'     => 'fa-solid fa-server',
				'nav'      => array(
					'health'  => array( __( 'سلامت سایت', 'zarincoach' ), 'fa-solid fa-heart-pulse', $attn ? zc_tool_num( $attn ) : '', $tally['danger'] ? 'danger' : 'warn' ),
					'server'  => array( __( 'وردپرس و سرور', 'zarincoach' ), 'fa-solid fa-server' ),
					'theme'   => array( __( 'قالب', 'zarincoach' ), 'fa-solid fa-palette' ),
					'plugins' => array( __( 'افزونه‌ها', 'zarincoach' ), 'fa-solid fa-plug', zc_tool_num( $active_n ), 'muted' ),
					'report'  => array( __( 'گزارش پشتیبانی', 'zarincoach' ), 'fa-solid fa-file-lines' ),
				),
				'actions'  => '<button type="button" class="zc-btn zc-btn--ghost" data-zc-copy="#zc-sys-report"><i class="fa-regular fa-copy" aria-hidden="true"></i><span>' . esc_html__( 'کپی گزارش', 'zarincoach' ) . '</span></button>',
				'side'     => $side,
			)
		);

		/* ---------------------------------------------------------------- سلامت */
		zc_tool_pane_open( 'health', __( 'سلامت سایت', 'zarincoach' ), __( 'بررسی خودکار پیش‌نیازها و تنظیمات مهم برای سرعت، امنیت و کارکرد کامل قالب.', 'zarincoach' ) );
		?>
		<div class="zc-health is-<?php echo esc_attr( $tone ); ?>">
			<div class="zc-ring" style="--p:<?php echo (int) $score; ?>" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: درصد */ __( 'امتیاز سلامت %s درصد', 'zarincoach' ), $score ) ); ?>">
				<strong><?php echo esc_html( zc_tool_num( $score ) ); ?><small>٪</small></strong>
			</div>
			<div class="zc-health-text">
				<h3>
					<?php
					if ( $tally['danger'] ) {
						esc_html_e( 'چند مورد مهم نیاز به رسیدگی دارد', 'zarincoach' );
					} elseif ( $tally['warn'] ) {
						esc_html_e( 'سایت سالم است؛ چند پیشنهاد برای بهتر شدن', 'zarincoach' );
					} else {
						esc_html_e( 'همه‌چیز عالی است', 'zarincoach' );
					}
					?>
				</h3>
				<p><?php esc_html_e( 'موارد «اطلاعات» اختیاری‌اند و در امتیاز حساب نمی‌شوند.', 'zarincoach' ); ?></p>
				<div class="zc-health-tally">
					<?php
					echo zc_tool_tag( sprintf( /* translators: %s: تعداد */ __( '%s سالم', 'zarincoach' ), zc_tool_num( $tally['ok'] ) ), 'ok', $state_icons['ok'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					if ( $tally['warn'] ) {
						echo zc_tool_tag( sprintf( /* translators: %s: تعداد */ __( '%s پیشنهاد', 'zarincoach' ), zc_tool_num( $tally['warn'] ) ), 'warn', $state_icons['warn'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $tally['danger'] ) {
						echo zc_tool_tag( sprintf( /* translators: %s: تعداد */ __( '%s بحرانی', 'zarincoach' ), zc_tool_num( $tally['danger'] ) ), 'danger', $state_icons['danger'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					if ( $tally['info'] ) {
						echo zc_tool_tag( sprintf( /* translators: %s: تعداد */ __( '%s اطلاعات', 'zarincoach' ), zc_tool_num( $tally['info'] ) ), 'info', $state_icons['info'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</div>

		<div class="zc-checks">
			<?php foreach ( $checks as $key => $check ) : ?>
				<div class="zc-check-card is-<?php echo esc_attr( $check['state'] ); ?>">
					<span class="zc-check-icon" aria-hidden="true"><i class="<?php echo esc_attr( $check['icon'] ); ?>"></i></span>
					<div class="zc-check-body">
						<span class="zc-check-label"><?php echo esc_html( $check['label'] ); ?></span>
						<strong class="zc-check-value" dir="auto"><?php echo esc_html( $check['value'] ); ?></strong>
						<small><?php echo esc_html( $check['hint'] ); ?></small>
					</div>
					<i class="zc-check-state <?php echo esc_attr( isset( $state_icons[ $check['state'] ] ) ? $state_icons[ $check['state'] ] : '' ); ?>" aria-hidden="true"></i>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- سرور */
		zc_tool_pane_open( 'server', __( 'وردپرس و سرور', 'zarincoach' ), __( 'پیکربندی وردپرس، PHP و پایگاه داده‌ی این سایت.', 'zarincoach' ) );
		foreach ( array( 'wordpress', 'server', 'database' ) as $zc_key ) {
			zc_render_system_table( $sections[ $zc_key ] );
		}
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- قالب */
		zc_tool_pane_open( 'theme', __( 'قالب', 'zarincoach' ), __( 'وضعیت قالب، تنظیمات کلیدی و حجم فایل‌های خروجی که به مرورگر ارسال می‌شوند.', 'zarincoach' ) );
		zc_render_system_table( $sections['theme'] );
		zc_tool_heading( __( 'فایل‌های خروجی', 'zarincoach' ), esc_html__( 'فقط فایل‌های لازم هر صفحه بارگذاری می‌شوند', 'zarincoach' ) );
		$zc_max = max( 1, max( $assets ) );
		?>
		<div class="zc-assets">
			<?php foreach ( $assets as $file => $size ) : ?>
				<div class="zc-asset<?php echo $size < 0 ? ' is-missing' : ''; ?>">
					<code dir="ltr"><?php echo esc_html( basename( $file ) ); ?></code>
					<span class="zc-asset-bar" aria-hidden="true"><i style="width:<?php echo esc_attr( (string) max( 2, (int) round( max( 0, $size ) / $zc_max * 100 ) ) ); ?>%"></i></span>
					<strong><?php echo $size < 0 ? esc_html__( 'موجود نیست', 'zarincoach' ) : esc_html( zc_tool_num( size_format( $size, 1 ) ) ); ?></strong>
				</div>
			<?php endforeach; ?>
		</div>
		<?php
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- افزونه‌ها */
		zc_tool_pane_open( 'plugins', __( 'افزونه‌ها', 'zarincoach' ), __( 'افزونه‌های نصب‌شده؛ افزونه‌هایی که قالب با آن‌ها یکپارچه شده با برچسب مشخص شده‌اند.', 'zarincoach' ) );
		?>
		<div class="zc-kv-grid zc-kv-grid--3">
			<div class="zc-kv"><span><?php esc_html_e( 'فعال', 'zarincoach' ); ?></span><strong><?php echo esc_html( zc_tool_num( $active_n ) ); ?></strong></div>
			<div class="zc-kv"><span><?php esc_html_e( 'غیرفعال', 'zarincoach' ); ?></span><strong><?php echo esc_html( zc_tool_num( count( $plugins ) - $active_n ) ); ?></strong></div>
			<div class="zc-kv"><span><?php esc_html_e( 'به‌روزرسانی موجود', 'zarincoach' ); ?></span><strong><?php echo esc_html( zc_tool_num( $update_n ) ); ?></strong></div>
		</div>
		<?php if ( empty( $plugins ) ) : ?>
			<?php echo zc_tool_alert( 'info', __( 'هیچ افزونه‌ای نصب نیست.', 'zarincoach' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
		<?php else : ?>
			<div class="zc-table-wrap">
				<table class="zc-table">
					<thead><tr><th><?php esc_html_e( 'افزونه', 'zarincoach' ); ?></th><th><?php esc_html_e( 'نسخه', 'zarincoach' ); ?></th><th><?php esc_html_e( 'وضعیت', 'zarincoach' ); ?></th></tr></thead>
					<tbody>
						<?php foreach ( $plugins as $plugin ) : ?>
							<tr class="<?php echo $plugin['active'] ? 'is-active' : 'is-inactive'; ?>">
								<td>
									<strong dir="auto"><?php echo esc_html( $plugin['name'] ); ?></strong>
									<?php if ( '' !== $plugin['role'] ) : ?>
										<?php echo zc_tool_tag( sprintf( /* translators: %s: نقش */ __( 'یکپارچه · %s', 'zarincoach' ), $plugin['role'] ), 'gold' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
									<?php endif; ?>
									<?php if ( '' !== $plugin['author'] ) : ?>
										<small dir="auto"><?php echo esc_html( $plugin['author'] ); ?></small>
									<?php endif; ?>
								</td>
								<td><code dir="ltr"><?php echo esc_html( $plugin['version'] ); ?></code><?php if ( '' !== $plugin['update'] ) : ?> <?php echo zc_tool_tag( '→ ' . $plugin['update'], 'warn' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php endif; ?></td>
								<td><?php echo $plugin['active'] ? zc_tool_tag( __( 'فعال', 'zarincoach' ), 'ok' ) : zc_tool_tag( __( 'غیرفعال', 'zarincoach' ), 'muted' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		<?php endif; ?>
		<?php
		zc_tool_pane_close();

		/* ---------------------------------------------------------------- گزارش */
		zc_tool_pane_open( 'report', __( 'گزارش پشتیبانی', 'zarincoach' ), __( 'این گزارش را هنگام درخواست پشتیبانی ارسال کنید؛ شامل هیچ رمز عبور یا اطلاعات مراجعان نیست.', 'zarincoach' ) );
		?>
		<div class="zc-report">
			<div class="zc-report-bar">
				<span><i class="fa-solid fa-file-lines" aria-hidden="true"></i> <code dir="ltr">zarincoach-system-report.txt</code></span>
				<span class="zc-report-actions">
					<button type="button" class="zc-btn zc-btn--ghost" data-zc-download="#zc-sys-report" data-filename="zarincoach-system-report.txt"><i class="fa-solid fa-download" aria-hidden="true"></i><span><?php esc_html_e( 'دانلود', 'zarincoach' ); ?></span></button>
					<button type="button" class="zc-btn zc-btn--primary" data-zc-copy="#zc-sys-report"><i class="fa-regular fa-copy" aria-hidden="true"></i><span><?php esc_html_e( 'کپی گزارش', 'zarincoach' ); ?></span></button>
				</span>
			</div>
			<label for="zc-sys-report" class="screen-reader-text"><?php esc_html_e( 'گزارش سیستم', 'zarincoach' ); ?></label>
			<textarea id="zc-sys-report" class="zc-report-text" readonly rows="22" dir="rtl" spellcheck="false"><?php echo esc_textarea( $report ); ?></textarea>
		</div>
		<?php
		zc_tool_pane_close();
		zc_tool_shell_close();
	}
endif;

if ( ! function_exists( 'zc_render_system_table' ) ) :
	/**
	 * جدول کلید/مقدار یک بخش.
	 *
	 * @param array $section بخش.
	 * @return void
	 */
	function zc_render_system_table( $section ) {
		zc_tool_heading( $section['title'] );
		echo '<dl class="zc-dl">';
		foreach ( $section['rows'] as $label => $value ) {
			$value = (string) $value;
			echo '<div><dt>' . esc_html( $label ) . '</dt><dd dir="auto">' . ( '' !== $value ? esc_html( $value ) : '<span class="zc-faint">—</span>' ) . '</dd></div>';
		}
		echo '</dl>';
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

		$removed = (int) zc_remove_demo_content( ! empty( $_POST['reset_options'] ) );

		wp_safe_redirect( admin_url( 'admin.php?page=zc-demo-content&zc_demo=removed&zc_count=' . $removed ) );
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
					<?php
					/* translators: %s: تعداد ویجت */
					echo esc_html( sprintf( __( 'قالب زرین‌کوچ برای المنتور طراحی شده و %s ویجت اختصاصی دارد؛ همه‌ی بخش‌های سایت با آن‌ها به‌صورت بصری قابل ویرایش‌اند.', 'zarincoach' ), zc_tool_num( zc_widget_count() ) ) );
					?>
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
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=zc-demo-content' ) ) . '">' . esc_html__( 'نصب دمو', 'zarincoach' ) . '</a>';
		return $links;
	}
endif;
add_filter( 'theme_action_links_' . get_option( 'stylesheet' ), 'zc_theme_action_links' );
