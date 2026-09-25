<?php
/**
 * ویجت متن و محتوا (Prose)
 *
 * محتوای متنی بلند (قوانین، حریم خصوصی، مقاله‌های ثابت) با تایپوگرافی خوانای قالب.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Text' ) ) :

	/**
	 * ویجت متن و محتوا.
	 */
	class ZC_Widget_Text extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-text';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'متن و محتوا', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-text';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int, string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'text', 'content', 'متن', 'محتوا', 'قوانین' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'content_section',
				array(
					'label' => __( 'محتوا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->heading_controls();

			$this->add_control(
				'content',
				array(
					'label'     => __( 'متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::WYSIWYG,
					'default'   => '<p>' . __( 'متن خود را اینجا بنویسید. عنوان‌ها، فهرست‌ها، نقل‌قول و پیوندها با تایپوگرافی قالب نمایش داده می‌شوند.', 'zarincoach' ) . '</p>',
					'separator' => 'before',
				)
			);

			$this->add_control(
				'width',
				array(
					'label'   => __( 'عرض ستون متن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'readable' => __( 'خوانا (باریک)', 'zarincoach' ),
						'wide'  => __( 'عریض', 'zarincoach' ),
						'full'  => __( 'تمام عرض بخش (پیشنهادی)', 'zarincoach' ),
					),
					'default' => 'full',
				)
			);

			$this->add_control(
				'boxed',
				array(
					'label'        => __( 'نمایش در کارت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'updated',
				array(
					'label'        => __( 'نمایش تاریخ به‌روزرسانی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'تاریخ آخرین ویرایش همین برگه در ابتدای متن.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'notice',
				array(
					'label'       => __( 'کادر اطلاعیه (بالای متن)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 3,
					'default'     => '',
					'separator'   => 'before',
					'description' => __( 'مثلاً خلاصه‌ی سند یا هشدار موارد اضطراری. شورت‌کدها پشتیبانی می‌شوند.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'notice_tone',
				array(
					'label'     => __( 'رنگ کادر اطلاعیه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'info'  => __( 'اطلاع‌رسانی (سرمه‌ای)', 'zarincoach' ),
						'alert' => __( 'هشدار (کهربایی)', 'zarincoach' ),
					),
					'default'   => 'info',
					'condition' => array( 'notice!' => '' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'aside_section',
				array(
					'label' => __( 'فهرست مطالب و اسناد مرتبط', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'toc',
				array(
					'label'        => __( 'فهرست مطالب خودکار', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'فهرست خودکار از تیترهای متن ساخته می‌شود؛ مناسب مقالات و برگه‌های قوانین.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'toc_layout',
				array(
					'label'     => __( 'جایگاه فهرست', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'sidebar' => __( 'ستون کناری چسبان (+ اسناد مرتبط)', 'zarincoach' ),
						'inline'  => __( 'بالای متن (چندستونه)', 'zarincoach' ),
					),
					'default'   => 'sidebar',
					'condition' => array( 'toc' => 'yes' ),
				)
			);

			$this->add_control(
				'toc_levels',
				array(
					'label'     => __( 'سطح تیترها', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'2'   => __( 'فقط H2', 'zarincoach' ),
						'2-3' => __( 'H2 و H3', 'zarincoach' ),
						'2-4' => __( 'H2 تا H4', 'zarincoach' ),
					),
					'default'   => '2',
					'condition' => array( 'toc' => 'yes' ),
				)
			);

			$this->add_control(
				'toc_title',
				array(
					'label'     => __( 'عنوان فهرست', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'در این سند', 'zarincoach' ),
					'condition' => array( 'toc' => 'yes' ),
				)
			);

			$this->toc_style_controls(
				'toc_',
				array( 'toc' => 'yes' ),
				array(
					'columns' => '2',
					'style'   => 'card',
				),
				array( 'toc_layout' => 'inline' )
			);

			$this->add_control(
				'toc_floating',
				array(
					'label'        => __( 'دکمه‌ی شناور فهرست هنگام اسکرول', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'condition'    => array( 'toc' => 'yes' ),
				)
			);

			$links = new \Elementor\Repeater();
			$links->add_control(
				'link_text',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'سند مرتبط', 'zarincoach' ),
				)
			);
			$links->add_control(
				'link_url',
				array(
					'label' => __( 'پیوند', 'zarincoach' ),
					'type'  => \Elementor\Controls_Manager::URL,
				)
			);

			$this->add_control(
				'aside_title',
				array(
					'label'     => __( 'عنوان اسناد مرتبط', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'اسناد مرتبط', 'zarincoach' ),
					'condition' => array(
						'toc'        => 'yes',
						'toc_layout' => 'sidebar',
					),
				)
			);

			$this->add_control(
				'aside_links',
				array(
					'label'       => __( 'اسناد مرتبط', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $links->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ link_text }}}',
					'condition'   => array( 'toc' => 'yes' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				array(
					'label' => __( 'متن', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'text_color',
				array(
					'label'     => __( 'رنگ متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .zc-prose' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_responsive_control(
				'font_size',
				array(
					'label'      => __( 'اندازه متن', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'rem' ),
					'range'      => array(
						'px'  => array(
							'min' => 12,
							'max' => 24,
						),
						'rem' => array(
							'min'  => 0.75,
							'max'  => 1.5,
							'step' => 0.05,
						),
					),
					'selectors'  => array(
						'{{WRAPPER}} .zc-prose' => 'font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s       = $this->get_settings_for_display();
			$content = isset( $s['content'] ) ? (string) $s['content'] : '';
			$widths  = array(
				'readable' => 'max-w-prose',
				'wide'     => 'max-w-4xl',
				'full'     => 'max-w-none',
			);
			$width   = isset( $s['width'], $widths[ $s['width'] ] ) ? $widths[ $s['width'] ] : $widths['full'];
			$boxed   = $this->is_on( $s, 'boxed' );
			$has_toc = $this->is_on( $s, 'toc' );

			// شورت‌کدها (اطلاعات پنل و پیوند برگه‌ها) مثل محتوای عادی وردپرس اجرا می‌شوند.
			$html = do_shortcode( shortcode_unautop( wpautop( $content ) ) );

			// شناسه‌گذاری تیترها و ساخت فهرست با موتور مشترک.
			$toc_layout = isset( $s['toc_layout'] ) && 'inline' === $s['toc_layout'] ? 'inline' : 'sidebar';
			$toc_html   = '';
			if ( $has_toc ) {
				$parsed = zc_toc_parse( $html, isset( $s['toc_levels'] ) ? (string) $s['toc_levels'] : '2' );
				$html   = $parsed['html'];
				$words  = count( preg_split( '/\s+/u', trim( wp_strip_all_tags( $html ) ) ) );
				$toc_html = zc_toc_render(
					$parsed['items'],
					array(
						'title'         => isset( $s['toc_title'] ) ? (string) $s['toc_title'] : '',
						'columns'       => 'sidebar' === $toc_layout ? 1 : ( isset( $s['toc_columns'] ) ? (int) $s['toc_columns'] : 2 ),
						'style'         => isset( $s['toc_style'] ) ? (string) $s['toc_style'] : 'card',
						'numbering'     => isset( $s['toc_numbering'] ) ? (string) $s['toc_numbering'] : 'decimal',
						'collapsible'   => ! isset( $s['toc_collapsible'] ) || $this->is_on( $s, 'toc_collapsible' ),
						'open'          => ! isset( $s['toc_open'] ) || $this->is_on( $s, 'toc_open' ),
						'meta'          => ! isset( $s['toc_meta'] ) || $this->is_on( $s, 'toc_meta' ),
						'minutes'       => max( 1, (int) ceil( $words / 220 ) ),
						'floating'      => $this->is_on( $s, 'toc_floating' ),
						'mobile_closed' => 'sidebar' === $toc_layout,
						'class'         => 'sidebar' === $toc_layout ? 'zc-toc-aside' : '',
					)
				);
			}
			$aside = $has_toc && 'sidebar' === $toc_layout;

			$links = array();
			if ( $aside && ! empty( $s['aside_links'] ) && is_array( $s['aside_links'] ) ) {
				foreach ( $s['aside_links'] as $link ) {
					$url = isset( $link['link_url']['url'] ) ? (string) $link['link_url']['url'] : '';
					if ( '' === $url || empty( $link['link_text'] ) ) {
						continue;
					}
					$links[] = array(
						'url'     => $url,
						'text'    => (string) $link['link_text'],
						'current' => untrailingslashit( $url ) === untrailingslashit( (string) get_permalink() ),
					);
				}
			}

			$notice = isset( $s['notice'] ) ? trim( (string) $s['notice'] ) : '';
			$tone   = isset( $s['notice_tone'] ) && 'alert' === $s['notice_tone'] ? 'alert' : 'info';

			// v1.8: بلوک «فقط اطلاعیه» (بدون عنوان و متن) فشرده رندر می‌شود تا فاصله‌ی اضافه ایجاد نکند.
			$has_body    = '' !== trim( wp_strip_all_tags( (string) $html, true ) ) || false !== strpos( (string) $html, '<img' ) || false !== strpos( (string) $html, '<table' );
			$has_heading = ! empty( $s['title'] ) || ! empty( $s['subtitle'] ) || ! empty( $s['eyebrow'] );
			$notice_only = '' !== $notice && ! $has_body && ! $has_heading && ! $has_toc && ! $this->is_on( $s, 'updated' );
			?>
			<section class="zc-text-block zc-section relative<?php echo $notice_only ? ' is-notice-only' : ''; ?>">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>

					<div class="<?php echo $aside ? 'zc-legal-layout grid items-start gap-8 lg:grid-cols-12 lg:gap-10' : ''; ?> <?php echo $has_heading ? 'zc-after-head' : ''; ?>">

						<?php if ( $aside ) : ?>
							<aside class="zc-legal-aside lg:sticky lg:top-28 lg:col-span-4 xl:col-span-3">
								<?php echo $toc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی escape شده‌ی موتور فهرست ?>

								<?php if ( ! empty( $links ) ) : ?>
									<nav class="zc-legal-links zc-card mt-4 p-5" aria-label="<?php echo esc_attr( isset( $s['aside_title'] ) ? (string) $s['aside_title'] : '' ); ?>">
										<p class="mb-3 inline-flex items-center gap-2 text-[0.95rem] font-bold text-secondary"><?php zc_icon( 'shield', 'h-[18px] w-[18px] text-primary' ); ?><?php echo esc_html( isset( $s['aside_title'] ) ? (string) $s['aside_title'] : '' ); ?></p>
										<ul class="grid gap-1 text-[0.86rem]">
											<?php foreach ( $links as $link ) : ?>
												<li>
													<a class="<?php echo $link['current'] ? 'bg-primary/10 font-bold text-primary' : 'text-muted hover:bg-primary/10 hover:text-primary'; ?> flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 transition" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $link['current'] ? ' aria-current="page"' : ''; ?>>
														<span><?php echo esc_html( $link['text'] ); ?></span>
														<?php zc_icon( 'chevron-left', 'h-3.5 w-3.5 opacity-60' ); ?>
													</a>
												</li>
											<?php endforeach; ?>
										</ul>
									</nav>
								<?php endif; ?>
							</aside>
						<?php endif; ?>

						<div class="<?php echo $aside ? 'min-w-0 lg:col-span-8 xl:col-span-9' : 'mx-auto ' . esc_attr( $width ); ?> <?php echo $boxed ? 'zc-card px-4 py-6 sm:p-8 lg:p-10' : ''; ?>">
							<?php if ( $this->is_on( $s, 'updated' ) && get_the_ID() ) : ?>
								<p class="mb-6 inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-1.5 text-[0.82rem] text-muted">
									<?php zc_icon( 'calendar', 'h-4 w-4 text-primary' ); ?>
									<?php
									/* translators: %s: date */
									printf( esc_html__( 'آخرین به‌روزرسانی: %s', 'zarincoach' ), esc_html( zc_date( get_the_ID(), 'modified' ) ) );
									?>
								</p>
							<?php endif; ?>

							<?php if ( '' !== $notice ) : ?>
								<div class="zc-notice zc-notice-<?php echo esc_attr( $tone ); ?> <?php echo $notice_only ? '' : 'mb-5 sm:mb-6'; ?> flex items-start gap-3 rounded-2xl border p-4 text-[0.92rem] leading-[2] sm:p-5">
									<span class="mt-1 shrink-0"><?php zc_icon( 'alert' === $tone ? 'alert' : 'shield', 'h-5 w-5' ); ?></span>
									<div><?php echo wp_kses_post( do_shortcode( $notice ) ); ?></div>
								</div>
							<?php endif; ?>

							<?php
							if ( $has_toc && 'inline' === $toc_layout ) {
								echo $toc_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							}
							?>

							<?php if ( $has_body ) : ?>
								<div class="zc-prose">
									<?php echo wp_kses_post( $html ); ?>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
