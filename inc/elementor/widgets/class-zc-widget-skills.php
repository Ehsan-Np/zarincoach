<?php
/**
 * ویجت «حوزه‌های تخصصی»
 *
 * گروه‌های تخصص با آیکن، عنوان، توضیح و برچسب‌ها؛ مناسب رزومه، درباره من و صفحات خدمات.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Skills' ) ) :

	/**
	 * ویجت حوزه‌های تخصصی.
	 */
	class ZC_Widget_Skills extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-skills';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'حوزه‌های تخصصی', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-apps';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'تخصص', 'مهارت', 'skills', 'رزومه' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section( 'content_section', array( 'label' => __( 'محتوا', 'zarincoach' ) ) );

			$this->heading_controls();

			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'g_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array(),
					'default' => 'brain',
				)
			);
			$repeater->add_control(
				'g_title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'g_desc',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);
			$repeater->add_control(
				'g_tags',
				array(
					'label'       => __( 'برچسب‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 4,
					'default'     => '',
					'description' => __( 'هر برچسب در یک خط.', 'zarincoach' ),
				)
			);
			$repeater->add_control(
				'g_url',
				array(
					'label'   => __( 'لینک (اختیاری)', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::URL,
					'default' => array( 'url' => '' ),
				)
			);

			$this->add_control(
				'groups',
				array(
					'label'       => __( 'حوزه‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ g_title }}}',
				)
			);

			$this->add_control(
				'link_label',
				array(
					'label'   => __( 'متن لینک کارت‌ها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'بیشتر بدانید', 'zarincoach' ),
				)
			);

			$this->add_control(
				'link_icon',
				array(
					'label'       => __( 'آیکن پیوند', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = فلش.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section( 'layout_section', array( 'label' => __( 'چیدمان', 'zarincoach' ) ) );

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '2' );

			$this->add_control(
				'numbers',
				array(
					'label'        => __( 'شماره‌گذاری کارت‌ها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'cards'   => __( 'کارتی', 'zarincoach' ),
						'minimal' => __( 'مینیمال (بدون قاب)', 'zarincoach' ),
					),
					'default' => 'cards',
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
					'num'   => array( __( 'شماره', 'zarincoach' ), '.zc-skill-num' ),
					'icon'  => array( __( 'آیکن', 'zarincoach' ), '.zc-skill-ico' ),
					'desc'  => array( __( 'توضیح', 'zarincoach' ), '.zc-skill-desc' ),
					'tags'  => array( __( 'برچسب‌ها', 'zarincoach' ), '.zc-skill-tags' ),
					'link'  => array( __( 'پیوند', 'zarincoach' ), '.zc-skill-link' ),
				)
			);
			$this->zc_style(
				'sk_grid',
				__( 'شبکه و کارت‌ها', 'zarincoach' ),
				array(
					'grid' => array( 'grid', '.zc-skills-grid', __( 'شبکه', 'zarincoach' ), array( 'cols' => false ) ),
					'card' => array( 'box', '.zc-skill', __( 'کارت', 'zarincoach' ), array( 'hover' => true, 'align' => true ) ),
					'num'  => array( 'text', '.zc-skill-num', __( 'شماره', 'zarincoach' ), array( 'margin' => false ) ),
					'icon' => array( 'icon', '.zc-skill-ico', __( 'آیکن', 'zarincoach' ), array( 'hover' => '.zc-skill' ) ),
				)
			);
			$this->zc_style(
				'sk_text',
				__( 'عنوان، متن، برچسب‌ها و پیوند', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-skill-title', __( 'عنوان', 'zarincoach' ), array( 'hover' => '.zc-skill' ) ),
					'desc'  => array( 'text', '.zc-skill-desc', __( 'توضیح', 'zarincoach' ), array( 'align' => true ) ),
					'tags'  => array( 'size', '.zc-skill-tags', __( 'فاصله‌ی برچسب‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 30 ) ),
					'tag'   => array( 'text', '.zc-skill-tags li', __( 'برچسب', 'zarincoach' ), array( 'hover' => true, 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'link'  => array( 'text', '.zc-skill-link', __( 'پیوند', 'zarincoach' ), array( 'hover' => true ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s       = $this->get_settings_for_display();
			$groups  = ! empty( $s['groups'] ) ? (array) $s['groups'] : array();
			$columns = isset( $s['columns'] ) ? (string) $s['columns'] : '2';
			$minimal = isset( $s['style'] ) && 'minimal' === $s['style'];
			$link    = isset( $s['link_label'] ) ? (string) $s['link_label'] : '';

			if ( empty( $groups ) ) {
				return;
			}
			?>
			<section class="zc-section zc-skills">
				<div class="zc-container">
					<?php $this->render_heading(); ?>

					<div class="zc-skills-grid zc-after-head grid gap-5 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
						<?php
						$n = 0;
						foreach ( $groups as $group ) :
							$title = isset( $group['g_title'] ) ? trim( (string) $group['g_title'] ) : '';
							if ( '' === $title ) {
								continue;
							}
							++$n;
							$desc = isset( $group['g_desc'] ) ? trim( (string) $group['g_desc'] ) : '';
							$tags = array_values( array_filter( array_map( 'trim', preg_split( '/\r\n|\r|\n/', isset( $group['g_tags'] ) ? (string) $group['g_tags'] : '' ) ) ) );
							$url  = isset( $group['g_url']['url'] ) ? (string) $group['g_url']['url'] : '';
							?>
							<article class="zc-skill <?php echo $minimal ? 'is-minimal' : 'zc-card zc-card-hover'; ?> zc-reveal" data-zc-delay="<?php echo esc_attr( (string) ( ( $n - 1 ) % 3 * 80 ) ); ?>">
								<?php if ( $this->is_on( $s, 'numbers' ) ) : ?>
									<span class="zc-skill-num" aria-hidden="true"><?php echo esc_html( zc_digits_to_persian( str_pad( (string) $n, 2, '0', STR_PAD_LEFT ) ) ); ?></span>
								<?php endif; ?>
								<span class="zc-skill-ico"><?php zc_icon( ! empty( $group['g_icon'] ) ? (string) $group['g_icon'] : 'brain', 'h-6 w-6' ); ?></span>
								<h3 class="zc-skill-title mt-5 text-[1.15rem] font-bold text-secondary"><?php echo esc_html( $title ); ?></h3>
								<?php if ( '' !== $desc ) : ?>
									<p class="zc-skill-desc mt-2 text-[0.92rem] leading-[1.95] text-muted"><?php echo esc_html( $desc ); ?></p>
								<?php endif; ?>
								<?php if ( ! empty( $tags ) ) : ?>
									<ul class="zc-skill-tags mt-5">
										<?php foreach ( $tags as $tag ) : ?>
											<li><?php echo esc_html( $tag ); ?></li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<?php if ( '' !== $url && '' !== $link ) : ?>
									<a class="zc-skill-link mt-5 inline-flex items-center gap-1.5 text-[0.88rem] font-bold text-primary hover:underline" href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $link ); ?><span class="zc-skill-link-icon zc-btn-icon"><?php $this->zc_render_icon_or( isset( $s['link_icon'] ) ? $s['link_icon'] : array(), 'arrow-left', 'h-4 w-4' ); ?></span></a>
								<?php endif; ?>
							</article>
						<?php endforeach; ?>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
