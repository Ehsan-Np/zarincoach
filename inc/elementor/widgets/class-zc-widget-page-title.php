<?php
/**
 * ویجت عنوان برگه (Page Title / Hero داخلی)
 *
 * عنوان و خلاصه‌ی برگه را به‌صورت پویا نمایش می‌دهد (قابل بازنویسی) به‌همراه مسیر راهنما.
 * با وجود این ویجت در برگه، سربرگ پیش‌فرض قالب برای آن برگه چاپ نمی‌شود.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Page_Title' ) ) :

	/**
	 * ویجت عنوان برگه.
	 */
	class ZC_Widget_Page_Title extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-page-title';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'عنوان برگه', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-archive-title';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int, string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'title', 'breadcrumb', 'عنوان', 'مسیر' ) );
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

			$this->add_control(
				'eyebrow',
				array(
					'label'   => __( 'برچسب کوتاه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'title',
				array(
					'label'       => __( 'عنوان', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 2,
					'placeholder' => __( 'خالی = عنوان همین برگه', 'zarincoach' ),
				)
			);

			$this->add_control(
				'accent_word',
				array(
					'label'       => __( 'واژه‌ی رنگی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'description' => __( 'بخشی از عنوان که با گرادیان رنگی نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'subtitle',
				array(
					'label'       => __( 'توضیح کوتاه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 3,
					'placeholder' => __( 'خالی = خلاصه (چکیده) همین برگه', 'zarincoach' ),
				)
			);

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
				'align',
				array(
					'label'   => __( 'تراز', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'start'  => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-text-align-right',
						),
						'center' => array(
							'title' => __( 'وسط', 'zarincoach' ),
							'icon'  => 'eicon-text-align-center',
						),
					),
					'default' => 'start',
					'toggle'  => false,
				)
			);

			$this->add_control(
				'size',
				array(
					'label'   => __( 'ارتفاع', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'compact' => __( 'فشرده', 'zarincoach' ),
						'normal'  => __( 'معمولی', 'zarincoach' ),
						'large'   => __( 'بلند', 'zarincoach' ),
					),
					'default' => 'normal',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'zc_tone_section',
				array(
					'label' => __( 'تُن بخش (رنگ زمینه)', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			// کنترل تُن اختصاصی (با گزینه‌ی پیروی از پنل قالب).
			$this->add_control(
				'zc_tone',
				array(
					'label'   => __( 'تُن رنگی', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						''        => __( 'مطابق پنل قالب', 'zarincoach' ),
						'inverse' => __( 'تیره / سرمه‌ای (Deep Navy)', 'zarincoach' ),
						'light'   => __( 'روشن', 'zarincoach' ),
					),
					'default' => '',
				)
			);

			$this->end_controls_section();

			$this->start_controls_section(
				'style_section',
				array(
					'label' => __( 'رنگ‌ها', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);

			$this->add_control(
				'title_color',
				array(
					'label'     => __( 'رنگ عنوان', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .zc-title-lg' => 'color: {{VALUE}};',
					),
				)
			);

			$this->add_control(
				'text_color',
				array(
					'label'     => __( 'رنگ توضیح', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						'{{WRAPPER}} .zc-lead' => 'color: {{VALUE}};',
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
			$post_id = get_the_ID();

			$title = isset( $s['title'] ) ? trim( (string) $s['title'] ) : '';
			if ( '' === $title ) {
				$title = $post_id ? get_the_title( $post_id ) : wp_get_document_title();
			}

			$subtitle = isset( $s['subtitle'] ) ? trim( (string) $s['subtitle'] ) : '';
			if ( '' === $subtitle && $post_id && has_excerpt( $post_id ) ) {
				$subtitle = get_the_excerpt( $post_id );
			}

			$accent     = isset( $s['accent_word'] ) ? trim( (string) $s['accent_word'] ) : '';
			$safe_title = esc_html( $title );
			if ( '' !== $accent && false !== strpos( $title, $accent ) ) {
				$safe_title = str_replace( esc_html( $accent ), '<span class="zc-gradient-text">' . esc_html( $accent ) . '</span>', $safe_title );
			}

			$tone = isset( $s['zc_tone'] ) ? (string) $s['zc_tone'] : '';
			if ( '' === $tone ) {
				$class = zc_page_header_class( 'zc-page-title' );
			} else {
				$class = 'zc-page-hero zc-page-title relative overflow-hidden border-b border-line ' . ( 'light' === $tone ? 'bg-surface2/50' : 'zc-tone-inverse' );
			}

			$center  = isset( $s['align'] ) && 'center' === $s['align'];
			$padding = array(
				'compact' => 'py-7 lg:py-9',
				'normal'  => 'py-9 lg:py-12',
				'large'   => 'py-12 lg:py-16',
			);
			$size    = isset( $s['size'], $padding[ $s['size'] ] ) ? $s['size'] : 'normal';
			$eyebrow = isset( $s['eyebrow'] ) ? trim( (string) $s['eyebrow'] ) : '';
			?>
			<section class="<?php echo esc_attr( $class ); ?>">
				<div class="zc-grain pointer-events-none absolute inset-0 -z-10 bg-zc-dots opacity-50"></div>
				<div class="pointer-events-none absolute -top-24 end-1/4 -z-10 h-72 w-72 rounded-full bg-primary/15 blur-3xl"></div>
				<div class="pointer-events-none absolute -bottom-28 start-10 -z-10 h-64 w-64 rounded-full bg-accent/15 blur-3xl"></div>

				<div class="zc-container relative <?php echo esc_attr( $padding[ $size ] ); ?> <?php echo $center ? 'flex flex-col items-center text-center' : ''; ?>">
					<?php if ( $this->is_on( $s, 'breadcrumbs' ) && function_exists( 'zc_breadcrumbs' ) ) : ?>
						<div class="mb-4"><?php zc_breadcrumbs(); ?></div>
					<?php endif; ?>

					<?php if ( '' !== $eyebrow ) : ?>
						<span class="zc-eyebrow mb-3"><?php echo esc_html( $eyebrow ); ?></span>
					<?php endif; ?>

					<h1 class="zc-title-lg zc-text-balance max-w-3xl zc-reveal"><?php echo wp_kses_post( $safe_title ); ?></h1>

					<?php if ( '' !== $subtitle ) : ?>
						<p class="zc-lead mt-4 max-w-2xl zc-reveal" data-zc-delay="80"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</div>
			</section>
			<?php
		}
	}
endif;
