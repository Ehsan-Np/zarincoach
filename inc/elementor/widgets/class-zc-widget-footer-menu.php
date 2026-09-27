<?php
/**
 * ویجت پانوشت: منوها
 *
 * یک منوی پانوشت با سه منبع: فهرست وردپرس، خدمات سایت یا تازه‌ترین نوشته‌ها —
 * با عنوان دلخواه، چیدمان عمودی/افقی و کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Footer_Menu' ) ) :

	/**
	 * ویجت «پانوشت: منوها».
	 */
	class ZC_Widget_Footer_Menu extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-footer-menu';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'پانوشت: منوها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-nav-menu';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'footer', 'پانوشت', 'پاورقی', 'فوتر', 'منو', 'فهرست', 'خدمات', 'نوشته‌ها' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'fwmenu_sec',
				array(
					'label' => __( 'منوی پانوشت', 'zarincoach' ),
				)
			);

			$this->add_control(
				'ftitle',
				array(
					'label'       => __( 'عنوان ستون', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'مثلاً: دسترسی سریع', 'zarincoach' ),
					'description' => __( 'برای حذف عنوان، خالی بگذارید.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'ftag',
				array(
					'label'     => __( 'تگ عنوان', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'h2' => 'H2',
						'h3' => 'H3',
						'h4' => 'H4',
						'h5' => 'H5',
						'h6' => 'H6',
						'p'  => 'P',
					),
					'default'   => 'h4',
					'condition' => array( 'ftitle!' => '' ),
				)
			);

			$this->add_control(
				'source',
				array(
					'label'   => __( 'منبع آیتم‌ها', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'menu'     => __( 'فهرست وردپرس', 'zarincoach' ),
						'services' => __( 'خدمات سایت', 'zarincoach' ),
						'posts'    => __( 'تازه‌ترین نوشته‌ها', 'zarincoach' ),
					),
					'default' => 'menu',
				)
			);

			$this->add_control(
				'menu',
				array(
					'label'       => __( 'فهرست', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => $this->menu_options(),
					'default'     => '0',
					'description' => __( '«جایگاه پیش‌فرض» = منوی جایگاه «منوی پانوشت» در «نمایش ← فهرست‌ها».', 'zarincoach' ),
					'condition'   => array( 'source' => 'menu' ),
				)
			);

			$this->add_control(
				'count',
				array(
					'label'     => __( 'تعداد آیتم', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::NUMBER,
					'min'       => 1,
					'max'       => 12,
					'default'   => 5,
					'condition' => array( 'source!' => 'menu' ),
				)
			);

			$this->add_control(
				'layout',
				array(
					'label'     => __( 'چیدمان', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'vertical'   => __( 'عمودی (ستونی)', 'zarincoach' ),
						'horizontal' => __( 'افقی (کنار هم)', 'zarincoach' ),
					),
					'default'   => 'vertical',
					'condition' => array( 'source' => 'menu' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * کلیدهای نمایش و استایل (کیت زرین‌کوچ).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'title' => array( __( 'عنوان ستون', 'zarincoach' ), '.zc-footer-title' ),
					'list'  => array( __( 'فهرست منو', 'zarincoach' ), '.zc-fw-menu-list' ),
				)
			);

			$this->zc_style(
				'fwmenu_text',
				__( 'متن‌ها', 'zarincoach' ),
				array(
					'title' => array( 'text', '.zc-footer-title', __( 'عنوان ستون', 'zarincoach' ), array( 'margin' => false ) ),
					'items' => array( 'text', '.zc-fw-menu-list a', __( 'آیتم‌های منو', 'zarincoach' ), array( 'hover' => true, 'margin' => false ) ),
					'list'  => array( 'text', '.zc-fw-menu-list', __( 'کل فهرست', 'zarincoach' ), array( 'margin' => false ) ),
				)
			);

			$this->zc_style(
				'fwmenu_layout',
				__( 'چیدمان فهرست', 'zarincoach' ),
				array(
					'gap' => array( 'size', '.zc-fw-menu-list', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), array( 'max' => 60, 'css' => 'gap: {{SIZE}}{{UNIT}};' ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s      = $this->get_settings_for_display();
			$ftitle = trim( (string) ( isset( $s['ftitle'] ) ? $s['ftitle'] : '' ) );
			$source = (string) ( isset( $s['source'] ) ? $s['source'] : 'menu' );
			$tag    = in_array( (string) ( isset( $s['ftag'] ) ? $s['ftag'] : 'h4' ), array( 'h2', 'h3', 'h4', 'h5', 'h6', 'p' ), true ) ? (string) $s['ftag'] : 'h4';

			echo '<div class="zc-fw zc-fw-menu">';

			if ( '' !== $ftitle ) {
				printf(
					'<%1$s class="zc-footer-title zc-h-sm mb-4 text-[0.95rem] font-bold text-white">%2$s</%1$s>',
					esc_html( $tag ),
					esc_html( $ftitle )
				);
			}

			if ( 'menu' === $source ) {
				$vertical = 'horizontal' !== (string) ( isset( $s['layout'] ) ? $s['layout'] : 'vertical' );
				$class    = 'zc-fw-menu-list ' . ( $vertical ? 'grid gap-2.5 text-[0.86rem] text-white/70' : 'flex flex-wrap gap-x-5 gap-y-2 text-[0.86rem] text-white/70' );
				wp_nav_menu(
					zc_header_menu_args(
						isset( $s['menu'] ) ? absint( $s['menu'] ) : 0,
						'zc-footer',
						array(
							'container'   => false,
							'menu_class'  => $class,
							'depth'       => 1,
							'fallback_cb' => false,
							'items_wrap'  => '<ul id="%1$s" class="%2$s">%3$s</ul>',
						)
					)
				);
			} else {
				$count = max( 1, min( 12, (int) ( isset( $s['count'] ) ? $s['count'] : 5 ) ) );
				$query = 'services' === $source ? array( 'post_type' => 'zc_service', 'posts_per_page' => $count, 'meta_key' => '_zc_service_order', 'orderby' => array( 'meta_value_num' => 'ASC', 'date' => 'DESC' ) ) : array( 'post_type' => 'post', 'posts_per_page' => $count ); // phpcs:ignore WordPress.DB.SlowDBQuery
				$q     = new WP_Query(
					array_merge(
						array(
							'post_status'         => 'publish',
							'no_found_rows'       => true,
							'ignore_sticky_posts' => true,
						),
						$query
					)
				);
				if ( $q->have_posts() ) {
					echo '<ul class="zc-fw-menu-list grid gap-2.5 text-[0.86rem] text-white/70">';
					while ( $q->have_posts() ) :
						$q->the_post();
						echo '<li><a class="line-clamp-1 transition-colors hover:text-primary" href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></li>';
					endwhile;
					echo '</ul>';
					wp_reset_postdata();
				}
			}

			echo '</div>';
		}
	}
endif;
