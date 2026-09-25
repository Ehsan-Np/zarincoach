<?php
/**
 * ویجت آخرین نوشته‌ها
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Posts' ) ) :

	/**
	 * ویجت نوشته‌ها.
	 */
	class ZC_Widget_Posts extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-posts';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'آخرین نوشته‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-post-list';
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
				'count',
				array(
					'label'   => __( 'تعداد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 1,
					'max'     => 24,
					'default' => 3,
				)
			);

			$this->add_control(
				'source',
				array(
					'label'   => __( 'نوع محتوا', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'post'        => __( 'نوشته‌ها', 'zarincoach' ),
						'zc_service'  => __( 'خدمات', 'zarincoach' ),
					),
					'default' => 'post',
				)
			);

			$this->add_control(
				'category',
				array(
					'label'       => __( 'دسته‌بندی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'options'     => $this->get_categories_options(),
					'default'     => '',
					'label_block' => true,
					'condition'   => array( 'source' => 'post' ),
				)
			);

			$this->add_control(
				'orderby',
				array(
					'label'   => __( 'مرتب‌سازی', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'date'          => __( 'جدیدترین', 'zarincoach' ),
						'title'         => __( 'عنوان', 'zarincoach' ),
						'comment_count' => __( 'پربحث‌ترین', 'zarincoach' ),
						'rand'          => __( 'تصادفی', 'zarincoach' ),
					),
					'default' => 'date',
				)
			);

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );

			$this->add_control(
				'layout',
				array(
					'label'   => __( 'چیدمان کارت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'vertical'   => __( 'عمودی (تصویر بالا)', 'zarincoach' ),
						'horizontal' => __( 'افقی (تصویر کنار)', 'zarincoach' ),
						'overlay'    => __( 'متن روی تصویر', 'zarincoach' ),
						'compact'    => __( 'فهرستی فشرده', 'zarincoach' ),
					),
					'default' => 'vertical',
				)
			);

			$this->add_control(
				'arrangement',
				array(
					'label'       => __( 'آرایش', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => array(
						'grid'     => __( 'شبکه‌ای', 'zarincoach' ),
						'magazine' => __( 'مجله‌ای (مطلب ویژه + فهرست کناری)', 'zarincoach' ),
					),
					'default'     => 'grid',
					'description' => __( 'مجله‌ای: نخستین مطلب بزرگ روی تصویر و ۴ مطلب بعدی به‌صورت فهرست کنار آن؛ بقیه در شبکه‌ی زیرین.', 'zarincoach' ),
					'condition'   => array( 'source' => 'post' ),
				)
			);

			$this->add_control(
				'ratio',
				array(
					'label'     => __( 'نسبت تصویر', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''      => __( 'پیش‌فرض (۱۶:۱۰)', 'zarincoach' ),
						'16-9'  => '16:9',
						'4-3'   => '4:3',
						'1-1'   => __( 'مربع', 'zarincoach' ),
						'3-4'   => __( 'عمودی ۳:۴', 'zarincoach' ),
					),
					'default'   => '',
					'condition' => array( 'layout' => array( 'vertical', 'overlay' ) ),
				)
			);

			$this->add_control(
				'excerpt',
				array(
					'label'   => __( 'تعداد واژه خلاصه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::NUMBER,
					'min'     => 0,
					'max'     => 60,
					'default' => 22,
				)
			);

			$this->add_control(
				'show_meta',
				array(
					'label'        => __( 'نمایش تاریخ و زمان مطالعه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_cat',
				array(
					'label'        => __( 'نمایش دسته', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'show_author',
				array(
					'label'        => __( 'نمایش تصویر و نام نویسنده', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'cat_filter',
				array(
					'label'        => __( 'نوار دسته‌بندی‌ها', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'پیوند دسته‌بندی‌های نوشته‌ها بالای فهرست (مناسب برگه وبلاگ).', 'zarincoach' ),
					'condition'    => array( 'source' => 'post' ),
				)
			);

			$this->add_control(
				'pagination',
				array(
					'label'        => __( 'صفحه‌بندی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
					'description'  => __( 'برای ساخت برگه وبلاگ/آرشیو با المنتور؛ «تعداد» = تعداد در هر صفحه.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'pagination_type',
				array(
					'label'     => __( 'نوع صفحه‌بندی', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'numbers'  => __( 'شماره‌ی صفحات', 'zarincoach' ),
						'loadmore' => __( 'دکمه‌ی «مطالب بیشتر» (بدون بارگذاری مجدد صفحه)', 'zarincoach' ),
					),
					'default'   => 'numbers',
					'condition' => array( 'pagination' => 'yes' ),
				)
			);

			$this->button_controls( 'more_', __( 'دکمه مشاهده همه', 'zarincoach' ) );

			$this->end_controls_section();
		}

		/**
		 * گزینه‌های دسته‌بندی نوشته‌ها.
		 *
		 * @return array<int|string, string>
		 */
		protected function get_categories_options() {
			$options = array( '' => __( 'همه', 'zarincoach' ) );

			$terms = get_terms(
				array(
					'taxonomy'   => 'category',
					'hide_empty' => true,
				)
			);

			if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
				foreach ( $terms as $term ) {
					$options[ $term->slug ] = $term->name;
				}
			}

			return $options;
		}

		/**
		 * نوار دسته‌بندی‌ها.
		 *
		 * @return void
		 */
		protected function render_categories_bar() {
			$terms = get_terms(
				array(
					'taxonomy'   => 'category',
					'hide_empty' => true,
					'orderby'    => 'count',
					'order'      => 'DESC',
					'number'     => 8,
				)
			);
			if ( empty( $terms ) || is_wp_error( $terms ) ) {
				return;
			}
			$current = (string) $this->get_settings_for_display( 'category' );
			$all_url = get_permalink();
			?>
			<nav class="zc-post-filter mt-8 flex flex-wrap gap-2" aria-label="<?php esc_attr_e( 'دسته‌بندی نوشته‌ها', 'zarincoach' ); ?>">
				<a class="zc-chip <?php echo '' === $current ? 'is-active' : ''; ?>" href="<?php echo esc_url( $all_url ? $all_url : home_url( '/' ) ); ?>"><?php esc_html_e( 'همه نوشته‌ها', 'zarincoach' ); ?></a>
				<?php foreach ( $terms as $term ) : ?>
					<a class="zc-chip <?php echo $current === $term->slug ? 'is-active' : ''; ?>" href="<?php echo esc_url( get_term_link( $term ) ); ?>">
						<?php echo esc_html( $term->name ); ?>
						<span class="text-[0.75em] opacity-60"><?php echo esc_html( number_format_i18n( $term->count ) ); ?></span>
					</a>
				<?php endforeach; ?>
			</nav>
			<?php
		}

		/**
		 * صفحه‌بندی (سازگار با برگه‌ی عادی و برگه‌ی نخست).
		 *
		 * @param WP_Query $query پرس‌وجو.
		 * @param int      $paged صفحه جاری.
		 * @return void
		 */
		protected function render_pagination( $query, $paged ) {
			$base = get_permalink();
			if ( ! $base ) {
				return;
			}

			$links = paginate_links(
				array(
					'base'               => trailingslashit( $base ) . '%_%',
					'format'             => get_option( 'permalink_structure' ) ? user_trailingslashit( 'page/%#%', 'paged' ) : '?paged=%#%',
					'current'            => $paged,
					'total'              => (int) $query->max_num_pages,
					'mid_size'           => 1,
					'prev_text'          => zc_icon( 'arrow-right', 'h-4 w-4', false ),
					'next_text'          => zc_icon( 'arrow-left', 'h-4 w-4', false ),
					'type'               => 'list',
					'before_page_number' => '<span class="sr-only">' . esc_html__( 'صفحه', 'zarincoach' ) . ' </span>',
				)
			);

			if ( ! $links ) {
				return;
			}
			?>
			<nav class="zc-pagination mt-8 lg:mt-10 flex justify-center" aria-label="<?php esc_attr_e( 'صفحه‌بندی', 'zarincoach' ); ?>">
				<?php echo zc_kses_svg( str_replace( array( "<ul class='page-numbers'", '<ul class="page-numbers"' ), '<ul class="page-numbers flex flex-wrap items-center gap-2"', $links ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</nav>
			<?php
		}

		/**
		 * نشانی صفحه‌ی N فهرست (سازگار با برگه‌ی عادی و برگه‌ی نخست).
		 *
		 * @param int $page شماره صفحه.
		 * @return string
		 */
		protected function page_url( $page ) {
			$base = (string) get_permalink();
			if ( get_option( 'permalink_structure' ) ) {
				return trailingslashit( $base ) . user_trailingslashit( 'page/' . (int) $page, 'paged' );
			}
			return add_query_arg( 'paged', (int) $page, $base );
		}

		/**
		 * دکمه‌ی «مطالب بیشتر»: صفحه‌ی بعد با fetch خوانده و کارت‌ها به فهرست افزوده می‌شوند.
		 * بدون جاوااسکریپت، یک پیوند عادی به صفحه‌ی بعد است (سازگار با موتورهای جستجو).
		 *
		 * @param WP_Query $query پرس‌وجو.
		 * @param int      $paged صفحه جاری.
		 * @param string   $uid   شناسه‌ی فهرست.
		 * @return void
		 */
		protected function render_load_more( $query, $paged, $uid ) {
			if ( $paged >= (int) $query->max_num_pages ) {
				return;
			}
			?>
			<div class="mt-8 lg:mt-10 flex justify-center">
				<a class="zc-btn zc-btn-outline zc-loadmore" href="<?php echo esc_url( $this->page_url( $paged + 1 ) ); ?>" data-zc-loadmore="<?php echo esc_attr( $uid ); ?>" data-zc-loading="<?php esc_attr_e( 'در حال بارگذاری…', 'zarincoach' ); ?>" rel="next">
					<span class="zc-loadmore-label"><?php esc_html_e( 'مطالب بیشتر', 'zarincoach' ); ?></span>
					<?php zc_icon( 'arrow-down', 'h-4 w-4' ); ?>
				</a>
			</div>
			<?php
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$settings = $this->get_settings_for_display();

			$count     = isset( $settings['count'] ) ? (int) $settings['count'] : 3;
			$columns   = isset( $settings['columns'] ) ? (string) $settings['columns'] : '3';
			$layout    = isset( $settings['layout'] ) ? (string) $settings['layout'] : 'vertical';
			$source    = isset( $settings['source'] ) ? (string) $settings['source'] : 'post';
			$excerpt   = isset( $settings['excerpt'] ) ? (int) $settings['excerpt'] : 22;
			$show_meta = ! isset( $settings['show_meta'] ) || 'yes' === (string) $settings['show_meta'];
			$orderby   = isset( $settings['orderby'] ) ? (string) $settings['orderby'] : 'date';
			$paginate  = isset( $settings['pagination'] ) && 'yes' === (string) $settings['pagination'];
			$cats_bar  = 'post' === $source && isset( $settings['cat_filter'] ) && 'yes' === (string) $settings['cat_filter'];
			$show_cat  = ! isset( $settings['show_cat'] ) || 'yes' === (string) $settings['show_cat'];
			$show_auth = isset( $settings['show_author'] ) && 'yes' === (string) $settings['show_author'];
			$ratio     = isset( $settings['ratio'] ) ? (string) $settings['ratio'] : '';
			$magazine  = 'post' === $source && isset( $settings['arrangement'] ) && 'magazine' === (string) $settings['arrangement'];
			$pag_type  = isset( $settings['pagination_type'] ) ? (string) $settings['pagination_type'] : 'numbers';
			$paged     = $paginate ? max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ) : 1;

			$args = array(
				'post_type'           => $source,
				'posts_per_page'      => $count,
				'post_status'         => 'publish',
				'ignore_sticky_posts' => true,
				'no_found_rows'       => ! $paginate,
				'paged'               => $paged,
				'orderby'             => $orderby,
				'order'               => 'rand' === $orderby ? 'ASC' : 'DESC',
			);

			if ( ! empty( $settings['category'] ) && 'post' === $source ) {
				$args['category_name'] = (string) $settings['category'];
			}

			$query = new WP_Query( $args );

			if ( ! $query->have_posts() ) {
				echo '<p class="zc-lead">' . esc_html__( 'مطلبی یافت نشد.', 'zarincoach' ) . '</p>';
				return;
			}
			?>
			<section class="zc-posts zc-section relative">
				<div class="zc-container">
					<div class="flex flex-col items-start justify-between gap-6 sm:flex-row sm:items-end">
						<?php $this->render_heading( '', 'h2' ); ?>

						<?php if ( ! empty( $settings['more_button_text'] ) ) : ?>
							<div class="shrink-0">
								<?php $this->render_button( 'more_', '' ); ?>
							</div>
						<?php endif; ?>
					</div>

					<?php
					if ( $cats_bar ) {
						$this->render_categories_bar();
					}
					?>

					<?php
					$card = array(
						'layout'    => $layout,
						'excerpt'   => $excerpt,
						'show_meta' => $show_meta,
						'show_cat'  => $show_cat,
						'author'    => $show_auth,
						'ratio'     => $ratio,
					);
					$uid  = 'zc-posts-' . $this->get_id();
					$top  = $cats_bar ? 'mt-5' : 'zc-after-head';
					$grid = 'compact' === $layout ? 'grid gap-x-8 gap-y-5 ' . $this->grid_classes( $columns ) : 'grid gap-6 ' . $this->grid_classes( $columns );

					if ( $magazine && 1 === $paged ) :
						$all   = $query->posts;
						$lead  = array_shift( $all );
						$side  = array_splice( $all, 0, 4 );
						$rest  = $all;
						?>
						<div class="zc-posts-magazine <?php echo esc_attr( $top ); ?> grid gap-6 lg:grid-cols-12">
							<div class="lg:col-span-7">
								<?php zc_post_card( $lead->ID, array_merge( $card, array( 'layout' => 'overlay', 'featured' => true, 'excerpt' => max( 18, $excerpt ), 'ratio' => '' ) ) ); ?>
							</div>
							<?php if ( $side ) : ?>
								<div class="zc-posts-side flex flex-col gap-5 lg:col-span-5">
									<?php
									$delay = 80;
									foreach ( $side as $p ) {
										zc_post_card( $p->ID, array_merge( $card, array( 'layout' => 'compact', 'delay' => $delay ) ) );
										$delay += 80;
									}
									?>
								</div>
							<?php endif; ?>
						</div>
						<?php if ( $rest ) : ?>
							<div class="mt-8 grid gap-6 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>" data-zc-posts-grid="<?php echo esc_attr( $uid ); ?>">
								<?php
								$delay = 0;
								foreach ( $rest as $p ) {
									zc_post_card( $p->ID, array_merge( $card, array( 'layout' => 'overlay' === $layout ? 'overlay' : 'vertical', 'delay' => $delay ) ) );
									$delay += 80;
								}
								?>
							</div>
						<?php else : ?>
							<div class="mt-8 grid gap-6 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>" data-zc-posts-grid="<?php echo esc_attr( $uid ); ?>" hidden></div>
						<?php endif; ?>
					<?php else : ?>
						<div class="<?php echo esc_attr( $top . ' ' . $grid ); ?>" data-zc-posts-grid="<?php echo esc_attr( $uid ); ?>">
							<?php
							$delay = 0;
							while ( $query->have_posts() ) :
								$query->the_post();

								if ( 'zc_service' === $source ) {
									$service_id = get_the_ID();
									?>
									<article class="zc-card zc-card-hover zc-reveal flex flex-col" data-zc-delay="<?php echo esc_attr( (string) $delay ); ?>">
										<h3 class="text-[1.1rem] font-bold text-secondary">
											<a class="transition-colors hover:text-primary" href="<?php echo esc_url( get_permalink( $service_id ) ); ?>"><?php echo esc_html( get_the_title( $service_id ) ); ?></a>
										</h3>
										<p class="zc-lead mt-2 !text-[0.92rem]"><?php echo esc_html( zc_excerpt( get_the_excerpt( $service_id ) ?: get_post_field( 'post_content', $service_id ), $excerpt ) ); ?></p>
									</article>
									<?php
								} else {
									zc_post_card( get_the_ID(), array_merge( $card, array( 'layout' => $magazine && 'compact' === $layout ? 'vertical' : $layout, 'delay' => $delay ) ) );
								}

								$delay += 80;
							endwhile;
							?>
						</div>
					<?php endif; ?>
					<?php wp_reset_postdata(); ?>

					<?php
					if ( $paginate && $query->max_num_pages > 1 ) {
						if ( 'loadmore' === $pag_type ) {
							$this->render_load_more( $query, $paged, $uid );
						} else {
							$this->render_pagination( $query, $paged );
						}
					}
					?>
				</div>
			</section>
			<?php
		}
	}
endif;
