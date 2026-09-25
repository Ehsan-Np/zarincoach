<?php
/**
 * ویجت «رزومه — معرفی حرفه‌ای»
 *
 * سربرگ اختصاصی صفحه رزومه: پرتره، نام، عنوان تحصیلی، مدارک و مجوزها (با امکان استعلام)،
 * نقش‌ها، آمار، دکمه‌ها (رزرو + چاپ رزومه)، ناوبری درون‌صفحه‌ای و اسکیمای Person / ProfilePage.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Resume_Hero' ) ) :

	/**
	 * ویجت معرفی رزومه.
	 */
	class ZC_Widget_Resume_Hero extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-resume-hero';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'رزومه — معرفی حرفه‌ای', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-person';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'رزومه', 'resume', 'cv', 'پروانه', 'مدرک' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$icons = function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array();

			/* ---------- معرفی ---------- */
			$this->start_controls_section( 'intro_section', array( 'label' => __( 'معرفی', 'zarincoach' ) ) );

			$this->add_control(
				'breadcrumbs',
				array(
					'label'        => __( 'مسیر راهنما (Breadcrumb)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'eyebrow',
				array(
					'label'   => __( 'برچسب کوتاه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'رزومه حرفه‌ای', 'zarincoach' ),
				)
			);

			$this->add_control(
				'name',
				array(
					'label'       => __( 'نام و نام خانوادگی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'خالی = «نام مالک» از پنل تنظیمات (اطلاعات حقوقی).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'degree',
				array(
					'label'   => __( 'عنوان تحصیلی / حرفه‌ای', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'summary',
				array(
					'label'   => __( 'خلاصه حرفه‌ای', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 5,
					'default' => '',
				)
			);

			$this->add_control(
				'tags',
				array(
					'label'       => __( 'نقش‌ها و برچسب‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 5,
					'default'     => '',
					'description' => __( 'هر مورد در یک خط.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			/* ---------- تصویر ---------- */
			$this->start_controls_section( 'image_section', array( 'label' => __( 'تصویر', 'zarincoach' ) ) );

			$this->add_control(
				'image',
				array(
					'label'   => __( 'پرتره', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::MEDIA,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'image_position',
				array(
					'label'   => __( 'جایگاه تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'end'   => __( 'چپ (کنار متن)', 'zarincoach' ),
						'start' => __( 'راست', 'zarincoach' ),
					),
					'default' => 'end',
				)
			);

			$this->add_control(
				'badge_title',
				array(
					'label'   => __( 'عنوان نشان روی تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'badge_text',
				array(
					'label'   => __( 'متن نشان روی تصویر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->end_controls_section();

			/* ---------- مدارک و مجوزها ---------- */
			$this->start_controls_section( 'cred_section', array( 'label' => __( 'مدارک و مجوزها', 'zarincoach' ) ) );

			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'cred_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $icons,
					'default' => 'badge-check',
				)
			);
			$repeater->add_control(
				'cred_label',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'cred_value',
				array(
					'label'   => __( 'مقدار', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'cred_verified',
				array(
					'label'        => __( 'نشان تأیید', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);
			$repeater->add_control(
				'cred_url',
				array(
					'label'   => __( 'لینک استعلام (اختیاری)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$repeater->add_control(
				'cred_link_label',
				array(
					'label'       => __( 'متن لینک (موارد بدون نشان تأیید)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'مشاهده', 'zarincoach' ),
					'description' => __( 'برای موارد تأییدشده، «متن لینک استعلام» نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'credentials',
				array(
					'label'       => __( 'موارد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ cred_label }}} — {{{ cred_value }}}',
				)
			);

			$this->add_control(
				'verify_label',
				array(
					'label'   => __( 'متن لینک استعلام', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'استعلام', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			/* ---------- آمار ---------- */
			$this->start_controls_section( 'stats_section', array( 'label' => __( 'آمار', 'zarincoach' ) ) );

			$stat = new \Elementor\Repeater();
			$stat->add_control(
				'stat_number',
				array(
					'label'   => __( 'عدد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$stat->add_control(
				'stat_suffix',
				array(
					'label'   => __( 'پسوند', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '+',
				)
			);
			$stat->add_control(
				'stat_label',
				array(
					'label'   => __( 'برچسب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'stats',
				array(
					'label'       => __( 'آمار', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $stat->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ stat_number }}}{{{ stat_suffix }}} {{{ stat_label }}}',
				)
			);

			$this->end_controls_section();

			/* ---------- دکمه‌ها ---------- */
			$this->start_controls_section( 'buttons_section', array( 'label' => __( 'دکمه‌ها', 'zarincoach' ) ) );

			$this->button_controls( '', __( 'دکمه اصلی', 'zarincoach' ) );
			$this->button_controls( 'second_', __( 'دکمه دوم', 'zarincoach' ) );

			$this->add_control(
				'print_button',
				array(
					'label'        => __( 'دکمه «چاپ / ذخیره PDF»', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
				)
			);

			$this->add_control(
				'print_label',
				array(
					'label'     => __( 'متن دکمه چاپ', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'چاپ / ذخیره PDF', 'zarincoach' ),
					'condition' => array( 'print_button' => 'yes' ),
				)
			);

			$this->end_controls_section();

			/* ---------- ناوبری درون‌صفحه‌ای ---------- */
			$this->start_controls_section( 'nav_section', array( 'label' => __( 'ناوبری بخش‌های رزومه', 'zarincoach' ) ) );

			$nav = new \Elementor\Repeater();
			$nav->add_control(
				'nav_label',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$nav->add_control(
				'nav_target',
				array(
					'label'       => __( 'شناسه بخش مقصد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => 'experience',
					'description' => __( 'همان «شناسه CSS» بخش در تب پیشرفته، بدون #.', 'zarincoach' ),
				)
			);
			$nav->add_control(
				'nav_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array_merge( array( '' => __( 'بدون', 'zarincoach' ) ), $icons ),
					'default' => '',
				)
			);

			$this->add_control(
				'nav',
				array(
					'label'       => __( 'پیوندها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $nav->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ nav_label }}}',
				)
			);

			$this->add_control(
				'nav_sticky',
				array(
					'label'        => __( 'چسبیدن نوار به بالای صفحه هنگام پیمایش', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->end_controls_section();

			/* ---------- سئو ---------- */
			$this->start_controls_section( 'seo_section', array( 'label' => __( 'سئو', 'zarincoach' ) ) );

			$this->add_control(
				'schema',
				array(
					'label'        => __( 'اسکیمای ProfilePage / Person', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'description'  => __( 'نام، عنوان حرفه‌ای، مدارک (hasCredential) و حوزه‌های تخصصی (knowsAbout) برای موتورهای جستجو.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * خط‌های یک textarea.
		 *
		 * @param string $text متن.
		 * @return string[]
		 */
		private function lines( $text ) {
			return array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', (string) $text ) ) ) );
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s = $this->get_settings_for_display();

			$name = isset( $s['name'] ) ? trim( (string) $s['name'] ) : '';
			if ( '' === $name ) {
				$name = function_exists( 'zc_legal_info' ) ? zc_legal_info( 'owner' ) : get_bloginfo( 'name' );
			}

			$eyebrow = isset( $s['eyebrow'] ) ? trim( (string) $s['eyebrow'] ) : '';
			$degree  = isset( $s['degree'] ) ? trim( (string) $s['degree'] ) : '';
			$summary = isset( $s['summary'] ) ? trim( (string) $s['summary'] ) : '';
			$tags    = $this->lines( isset( $s['tags'] ) ? $s['tags'] : '' );
			$image   = isset( $s['image']['url'] ) ? (string) $s['image']['url'] : '';
			$img_id  = isset( $s['image']['id'] ) ? (int) $s['image']['id'] : 0;
			$creds   = ! empty( $s['credentials'] ) ? (array) $s['credentials'] : array();
			$stats   = ! empty( $s['stats'] ) ? (array) $s['stats'] : array();
			$nav     = ! empty( $s['nav'] ) ? (array) $s['nav'] : array();
			$img_pos = isset( $s['image_position'] ) && 'start' === $s['image_position'] ? 'start' : 'end';
			$verify  = isset( $s['verify_label'] ) ? (string) $s['verify_label'] : '';
			$b_title = isset( $s['badge_title'] ) ? trim( (string) $s['badge_title'] ) : '';
			$b_text  = isset( $s['badge_text'] ) ? trim( (string) $s['badge_text'] ) : '';
			?>
			<section class="zc-resume-hero relative overflow-hidden">
				<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-40"></div>
				<div class="pointer-events-none absolute -top-40 end-[8%] -z-10 h-[26rem] w-[26rem] rounded-full bg-primary/15 blur-3xl"></div>
				<div class="pointer-events-none absolute -bottom-40 start-[4%] -z-10 h-80 w-80 rounded-full bg-accent/15 blur-3xl"></div>

				<div class="zc-container relative pb-8 pt-8 lg:pb-12 lg:pt-12">
					<?php if ( $this->is_on( $s, 'breadcrumbs' ) && function_exists( 'zc_breadcrumbs' ) ) : ?>
						<div class="mb-8"><?php zc_breadcrumbs(); ?></div>
					<?php endif; ?>

					<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
						<div class="lg:col-span-7 <?php echo 'start' === $img_pos ? 'lg:order-2' : ''; ?>">
							<?php if ( '' !== $eyebrow ) : ?>
								<span class="zc-eyebrow zc-reveal"><?php echo esc_html( $eyebrow ); ?></span>
							<?php endif; ?>

							<h1 class="zc-resume-name zc-reveal mt-4" data-zc-delay="60"><?php echo esc_html( $name ); ?></h1>

							<?php if ( '' !== $degree ) : ?>
								<p class="zc-resume-degree zc-reveal mt-3" data-zc-delay="120">
									<?php zc_icon( 'graduation', 'h-5 w-5 shrink-0' ); ?>
									<span><?php echo esc_html( $degree ); ?></span>
								</p>
							<?php endif; ?>

							<?php if ( '' !== $summary ) : ?>
								<p class="zc-lead zc-reveal mt-6 max-w-2xl" data-zc-delay="160"><?php echo esc_html( $summary ); ?></p>
							<?php endif; ?>

							<?php if ( ! empty( $tags ) ) : ?>
								<ul class="zc-resume-tags zc-reveal mt-6" data-zc-delay="200">
									<?php foreach ( $tags as $tag ) : ?>
										<li><?php echo esc_html( $tag ); ?></li>
									<?php endforeach; ?>
								</ul>
							<?php endif; ?>

							<div class="zc-resume-actions zc-reveal mt-8 flex flex-wrap items-center gap-3" data-zc-delay="240">
								<?php $this->render_button( '', 'zc-btn-lg' ); ?>
								<?php $this->render_button( 'second_', 'zc-btn-lg' ); ?>
								<?php if ( $this->is_on( $s, 'print_button' ) ) : ?>
									<button type="button" class="zc-btn zc-btn-ghost zc-btn-lg" data-zc-print>
										<?php zc_icon( 'printer', 'h-5 w-5' ); ?>
										<span><?php echo esc_html( isset( $s['print_label'] ) ? (string) $s['print_label'] : '' ); ?></span>
									</button>
								<?php endif; ?>
							</div>
						</div>

						<div class="lg:col-span-5 <?php echo 'start' === $img_pos ? 'lg:order-1' : ''; ?>">
							<figure class="zc-resume-photo zc-reveal" data-zc-delay="120">
								<span class="zc-resume-photo-ring" aria-hidden="true"></span>
								<span class="zc-resume-photo-frame">
									<?php
									if ( $img_id || '' !== $image ) {
										echo zc_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
											$img_id ? $img_id : $image,
											'zc_portrait',
											array(
												'class'    => 'h-full w-full object-cover',
												'alt'      => $name,
												'sizes'    => '(min-width: 1024px) 420px, 80vw',
												'priority' => true,
											)
										);
									} else {
										echo '<span class="grid h-full w-full place-items-center text-primary/60">';
										zc_icon( 'user', 'h-24 w-24' );
										echo '</span>';
									}
									?>
								</span>
								<?php if ( '' !== $b_title || '' !== $b_text ) : ?>
									<figcaption class="zc-resume-photo-badge">
										<span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-primary text-white"><?php zc_icon( 'badge-check', 'h-6 w-6' ); ?></span>
										<span class="min-w-0">
											<strong class="block text-[0.95rem] text-secondary"><?php echo esc_html( $b_title ); ?></strong>
											<span class="block text-[0.8rem] text-muted"><?php echo esc_html( $b_text ); ?></span>
										</span>
									</figcaption>
								<?php endif; ?>
							</figure>
						</div>
					</div>

					<?php if ( ! empty( $creds ) ) : ?>
						<?php $cred_cols = array( 1 => 'lg:grid-cols-1', 2 => 'lg:grid-cols-2', 3 => 'lg:grid-cols-3' ); ?>
						<ul class="zc-resume-creds mt-8 lg:mt-10 grid gap-3 sm:grid-cols-2 <?php echo esc_attr( isset( $cred_cols[ count( $creds ) ] ) ? $cred_cols[ count( $creds ) ] : 'lg:grid-cols-4' ); ?>">
							<?php
							$delay = 0;
							foreach ( $creds as $cred ) :
								$label = isset( $cred['cred_label'] ) ? trim( (string) $cred['cred_label'] ) : '';
								$value = isset( $cred['cred_value'] ) ? trim( (string) $cred['cred_value'] ) : '';
								if ( '' === $label && '' === $value ) {
									continue;
								}
								$icon = ! empty( $cred['cred_icon'] ) ? (string) $cred['cred_icon'] : 'badge-check';
								$url       = isset( $cred['cred_url']['url'] ) ? (string) $cred['cred_url']['url'] : '';
								$external  = ! empty( $cred['cred_url']['is_external'] );
								$verified  = $this->is_on( $cred, 'cred_verified' );
								$link_text = $verified ? $verify : ( isset( $cred['cred_link_label'] ) ? trim( (string) $cred['cred_link_label'] ) : '' );
								?>
								<li class="zc-resume-cred zc-reveal !items-start" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
									<span class="zc-resume-cred-ico"><?php zc_icon( $icon, 'h-5 w-5' ); ?></span>
									<span class="min-w-0 flex-1">
										<span class="block text-[0.78rem] text-muted"><?php echo esc_html( $label ); ?></span>
										<strong class="mt-0.5 flex flex-wrap items-center gap-1.5 text-[0.98rem] leading-[1.75] text-secondary">
											<span><?php echo esc_html( $value ); ?></span>
											<?php if ( $verified ) : ?>
												<span class="text-primary" title="<?php esc_attr_e( 'تأییدشده', 'zarincoach' ); ?>"><?php zc_icon( 'badge-check', 'h-4 w-4' ); ?></span>
											<?php endif; ?>
										</strong>
										<?php if ( '' !== $url && '' !== $link_text ) : ?>
											<a class="zc-resume-verify mt-2" href="<?php echo esc_url( $url ); ?>"<?php echo $external ? ' target="_blank" rel="noopener nofollow"' : ''; ?>><?php echo esc_html( $link_text ); ?><?php zc_icon( $external ? 'external' : 'arrow-left', 'h-3.5 w-3.5' ); ?></a>
										<?php endif; ?>
									</span>
								</li>
								<?php
								$delay += 70;
							endforeach;
							?>
						</ul>
					<?php endif; ?>

					<?php if ( ! empty( $stats ) ) : ?>
						<dl class="zc-resume-stats mt-6 grid grid-cols-2 <?php echo 3 === count( $stats ) ? 'lg:grid-cols-3' : ( count( $stats ) >= 4 ? 'lg:grid-cols-4' : '' ); ?>">
							<?php
							foreach ( $stats as $stat ) :
								$raw    = isset( $stat['stat_number'] ) ? (string) $stat['stat_number'] : '';
								$number = preg_replace( '/[^\d.]/', '', zc_digits_to_latin( $raw ) );
								$suffix = isset( $stat['stat_suffix'] ) ? (string) $stat['stat_suffix'] : '';
								$label  = isset( $stat['stat_label'] ) ? (string) $stat['stat_label'] : '';
								if ( '' === $number && '' === $label ) {
									continue;
								}
								?>
								<div class="zc-resume-stat">
									<dt class="order-2 mt-1 text-[0.82rem] leading-relaxed text-muted"><?php echo esc_html( $label ); ?></dt>
									<dd class="order-1 m-0 text-[2rem] font-bold leading-none text-secondary sm:text-[2.35rem]">
										<span data-zc-count="<?php echo esc_attr( $number ); ?>" data-zc-suffix="<?php echo esc_attr( $suffix ); ?>" data-zc-persian="1"><?php echo esc_html( zc_digits_to_persian( $number ) . $suffix ); ?></span>
									</dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $nav ) ) : ?>
					<nav class="zc-resume-nav<?php echo $this->is_on( $s, 'nav_sticky' ) ? ' is-sticky' : ''; ?>" aria-label="<?php esc_attr_e( 'بخش‌های رزومه', 'zarincoach' ); ?>" data-zc-resume-nav>
						<div class="zc-container">
							<ul class="zc-no-scrollbar">
								<?php
								foreach ( $nav as $link ) :
									$label  = isset( $link['nav_label'] ) ? trim( (string) $link['nav_label'] ) : '';
									$target = isset( $link['nav_target'] ) ? sanitize_html_class( ltrim( trim( (string) $link['nav_target'] ), '#' ) ) : '';
									if ( '' === $label || '' === $target ) {
										continue;
									}
									?>
									<li>
										<a href="#<?php echo esc_attr( $target ); ?>">
											<?php if ( ! empty( $link['nav_icon'] ) ) : ?>
												<?php zc_icon( (string) $link['nav_icon'], 'h-4 w-4' ); ?>
											<?php endif; ?>
											<span><?php echo esc_html( $label ); ?></span>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</nav>
				<?php endif; ?>
			</section>
			<?php
			if ( $this->is_on( $s, 'schema' ) && ! ( class_exists( '\Elementor\Plugin' ) && \Elementor\Plugin::$instance->editor->is_edit_mode() ) ) {
				$this->render_schema( $name, $degree, $summary, $image, $creds, $tags );
			}
		}

		/**
		 * اسکیمای ProfilePage + Person.
		 *
		 * @param string $name    نام.
		 * @param string $degree  عنوان.
		 * @param string $summary خلاصه.
		 * @param string $image   تصویر.
		 * @param array  $creds   مدارک.
		 * @param array  $tags    برچسب‌ها.
		 * @return void
		 */
		private function render_schema( $name, $degree, $summary, $image, $creds, $tags ) {
			if ( ! function_exists( 'zc_schema_add_node' ) || ! zc_schema_can_collect() ) {
				return;
			}
			// شماره‌هایی که از تنظیمات سئو در گره‌ی Person هستند تکرار نشوند.
			$known = array_filter(
				array(
					zc_digits_to_latin( (string) zc_opt( 'legal_license_no', '' ) ),
					zc_digits_to_latin( (string) zc_opt( 'legal_pco_code', '' ) ),
				)
			);

			$credentials = array();
			foreach ( $creds as $cred ) {
				$label = isset( $cred['cred_label'] ) ? trim( (string) $cred['cred_label'] ) : '';
				$value = isset( $cred['cred_value'] ) ? trim( zc_digits_to_latin( (string) $cred['cred_value'] ) ) : '';
				if ( '' === $label || ( '' !== $value && in_array( preg_replace( '/\D+/', '', $value ), $known, true ) ) ) {
					continue;
				}
				// تألیف (گره‌ی Book جداگانه دارد) و مدرک تحصیلیِ تکراری با تنظیمات سئو.
				if ( preg_match( '/تألیف|تالیف|کتاب/u', $label ) || ( preg_match( '/مدرک|تحصیل/u', $label ) && '' !== trim( (string) zc_opt( 'legal_degree', '' ) ) ) ) {
					continue;
				}
				$credentials[] = array(
					'@type'              => 'EducationalOccupationalCredential',
					'name'               => trim( $label . ( '' !== $value ? ' — ' . $value : '' ) ),
					'credentialCategory' => $label,
				);
			}

			zc_schema_hint( 'page_type', 'ProfilePage' );
			zc_schema_add_node(
				array_filter(
					array(
						'@id'           => zc_schema_id( 'person' ),
						'hasCredential' => $credentials,
						'knowsAbout'    => array_values( array_filter( array_map( 'trim', (array) $tags ) ) ),
					)
				)
			);
		}

	}
endif;
