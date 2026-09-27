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
				'updated_label',
				array(
					'label'       => __( 'متن «آخرین به‌روزرسانی»', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => __( 'آخرین به‌روزرسانی: %s', 'zarincoach' ),
					'label_block' => true,
					'dynamic'     => array( 'active' => true ),
					'description' => __( '%s جای تاریخ است.', 'zarincoach' ),
					'condition'   => array( 'updated' => 'yes' ),
				)
			);

			$this->add_control(
				'updated_icon',
				array(
					'label'       => __( 'آیکن تاریخ', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = تقویم.', 'zarincoach' ),
					'condition'   => array( 'updated' => 'yes' ),
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

			$this->add_control(
				'notice_icon',
				array(
					'label'       => __( 'آیکن اطلاعیه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = آیکن پیش‌فرض بر اساس نوع اطلاعیه.', 'zarincoach' ),
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
				'aside_icon',
				array(
					'label'       => __( 'آیکن عنوان پیوندها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = سپر.', 'zarincoach' ),
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
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'updated_icon' => array( __( 'آیکن تاریخ به‌روزرسانی', 'zarincoach' ), '.zc-text-updated-icon' ),
					'notice_icon'  => array( __( 'آیکن اطلاعیه', 'zarincoach' ), '.zc-notice-icon' ),
					'aside_links'  => array( __( 'باکس پیوندهای کناری', 'zarincoach' ), '.zc-legal-links' ),
				)
			);
			$this->zc_style(
				'txt_body',
				__( 'قاب و متن اصلی', 'zarincoach' ),
				array(
					'main'  => array( 'box', '.zc-text-main', __( 'قاب محتوا', 'zarincoach' ), array( 'width' => true ) ),
					'prose' => array( 'text', '.zc-prose, .zc-prose p, .zc-prose li', __( 'متن پاراگراف‌ها', 'zarincoach' ), array( 'align' => true, 'margin' => false ) ),
					'pgap'  => array( 'size', '.zc-prose p', __( 'فاصله‌ی بین پاراگراف‌ها', 'zarincoach' ), array( 'prop' => 'margin-block-end', 'max' => 60 ) ),
					'link'  => array( 'text', '.zc-prose a', __( 'پیوندهای داخل متن', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'bold'  => array( 'color', '.zc-prose strong, .zc-prose b', __( 'رنگ متن پررنگ', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'txt_heads',
				__( 'تیترهای داخل متن', 'zarincoach' ),
				array(
					'h2'  => array( 'text', '.zc-prose h2', __( 'تیتر ۲', 'zarincoach' ) ),
					'h3'  => array( 'text', '.zc-prose h3', __( 'تیتر ۳', 'zarincoach' ) ),
					'h4'  => array( 'text', '.zc-prose h4', __( 'تیتر ۴', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'txt_blocks',
				__( 'نقل‌قول، فهرست و جدول', 'zarincoach' ),
				array(
					'quote' => array( 'box', '.zc-prose blockquote', __( 'نقل‌قول', 'zarincoach' ), array( 'text' => true ) ),
					'list'  => array( 'list', '.zc-prose li', __( 'فهرست‌ها', 'zarincoach' ), array( 'list' => '.zc-prose ul, .zc-prose ol', 'marker' => '.zc-prose li::marker' ) ),
					'table' => array( 'box', '.zc-prose table', __( 'جدول', 'zarincoach' ), array( 'gradient' => false ) ),
					'th'    => array( 'text', '.zc-prose th', __( 'سرستون جدول', 'zarincoach' ), array( 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'td'    => array( 'text', '.zc-prose td', __( 'خانه‌های جدول', 'zarincoach' ), array( 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'img'   => array( 'image', '.zc-prose img', __( 'تصاویر', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'txt_meta',
				__( 'تاریخ و اطلاعیه', 'zarincoach' ),
				array(
					'upd'    => array( 'box', '.zc-text-updated', __( 'نشان تاریخ', 'zarincoach' ), array( 'gradient' => false, 'text' => true ) ),
					'updi'   => array( 'color', '.zc-text-updated-icon', __( 'رنگ آیکن تاریخ', 'zarincoach' ) ),
					'notice' => array( 'box', '.zc-notice', __( 'قاب اطلاعیه', 'zarincoach' ), array( 'text' => true ) ),
					'ntext'  => array( 'text', '.zc-notice-text', __( 'متن اطلاعیه', 'zarincoach' ), array( 'align' => true, 'margin' => false ) ),
					'nicon'  => array( 'color', '.zc-notice-icon', __( 'رنگ آیکن اطلاعیه', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'txt_aside',
				__( 'ستون کناری (پیوندها)', 'zarincoach' ),
				array(
					'grid'  => array( 'split', '.zc-legal-layout', __( 'ستون‌ها (کناری / محتوا)', 'zarincoach' ), array( 'children' => '.zc-legal-aside, .zc-text-main' ) ),
					'box'   => array( 'box', '.zc-legal-links', __( 'باکس پیوندها', 'zarincoach' ) ),
					'title' => array( 'text', '.zc-legal-links-title', __( 'عنوان', 'zarincoach' ) ),
					'link'  => array( 'text', '.zc-legal-link:not(.is-current)', __( 'پیوند', 'zarincoach' ), array( 'hover' => true, 'bg' => true, 'margin' => false ) ),
					'cur'   => array( 'text', '.zc-legal-link.is-current', __( 'پیوند صفحه‌ی جاری', 'zarincoach' ), array( 'bg' => true, 'margin' => false ) ),
				),
				array( 'condition' => array( 'toc' => 'yes', 'toc_layout!' => 'inline' ) )
			);
			$this->zc_toc_styles( 'txt', array( 'condition' => array( 'toc' => 'yes' ) ) );
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
										<p class="zc-legal-links-title mb-3 inline-flex items-center gap-2 text-[0.95rem] font-bold text-secondary"><span class="zc-legal-links-icon inline-flex text-primary"><?php $this->zc_render_icon_or( isset( $s['aside_icon'] ) ? $s['aside_icon'] : array(), 'shield', 'h-[18px] w-[18px]' ); ?></span><?php echo esc_html( isset( $s['aside_title'] ) ? (string) $s['aside_title'] : '' ); ?></p>
										<ul class="grid gap-1 text-[0.86rem]">
											<?php foreach ( $links as $link ) : ?>
												<li>
													<a class="zc-legal-link<?php echo $link['current'] ? ' is-current bg-primary/10 font-bold text-primary' : ' text-muted hover:bg-primary/10 hover:text-primary'; ?> flex items-center justify-between gap-2 rounded-lg px-2 py-1.5 transition" href="<?php echo esc_url( $link['url'] ); ?>"<?php echo $link['current'] ? ' aria-current="page"' : ''; ?>>
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

						<div class="zc-text-main <?php echo $aside ? 'min-w-0 lg:col-span-8 xl:col-span-9' : 'mx-auto ' . esc_attr( $width ); ?> <?php echo $boxed ? 'zc-card px-4 py-6 sm:p-8 lg:p-10' : ''; ?>">
							<?php if ( $this->is_on( $s, 'updated' ) && get_the_ID() ) : ?>
								<p class="zc-text-updated mb-6 inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-1.5 text-[0.82rem] text-muted">
									<span class="zc-text-updated-icon inline-flex text-primary"><?php $this->zc_render_icon_or( isset( $s['updated_icon'] ) ? $s['updated_icon'] : array(), 'calendar', 'h-4 w-4' ); ?></span>
									<?php
									/* translators: %s: date */
									$zc_upd = $this->zc_label( $s, 'updated_label', __( 'آخرین به‌روزرسانی: %s', 'zarincoach' ) );
									echo esc_html( false !== strpos( $zc_upd, '%s' ) ? str_replace( '%s', zc_date( get_the_ID(), 'modified' ), $zc_upd ) : $zc_upd . ' ' . zc_date( get_the_ID(), 'modified' ) );
									?>
								</p>
							<?php endif; ?>

							<?php if ( '' !== $notice ) : ?>
								<div class="zc-notice zc-notice-<?php echo esc_attr( $tone ); ?> <?php echo $notice_only ? '' : 'mb-5 sm:mb-6'; ?> flex items-start gap-3 rounded-2xl border p-4 text-[0.92rem] leading-[2] sm:p-5">
									<span class="zc-notice-icon mt-1 shrink-0"><?php $this->zc_render_icon_or( isset( $s['notice_icon'] ) ? $s['notice_icon'] : array(), 'alert' === $tone ? 'alert' : 'shield', 'h-5 w-5' ); ?></span>
									<div class="zc-notice-text"><?php echo wp_kses_post( do_shortcode( $notice ) ); ?></div>
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
