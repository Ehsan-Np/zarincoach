<?php
/**
 * ویجت «معرفی کتاب»
 *
 * جلد سه‌بعدی (تصویر دلخواه یا جلد تایپوگرافیک خودکار با رنگ‌های پالت)،
 * مشخصات کتاب، توضیح، ویژگی‌ها و دکمه.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Book' ) ) :

	/**
	 * ویجت کتاب.
	 */
	class ZC_Widget_Book extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-book';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'معرفی کتاب', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-document-file';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'کتاب', 'book', 'تألیف', 'رزومه' ) );
		}

		/**
		 * ثبت کنترل‌ها.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section( 'content_section', array( 'label' => __( 'کتاب', 'zarincoach' ) ) );

			$this->add_control(
				'eyebrow',
				array(
					'label'   => __( 'برچسب کوتاه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'تألیف', 'zarincoach' ),
				)
			);

			$this->add_control(
				'book_title',
				array(
					'label'   => __( 'نام کتاب', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'author',
				array(
					'label'   => __( 'نویسنده', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'role_label',
				array(
					'label'   => __( 'برچسب نقش', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'نویسنده', 'zarincoach' ),
				)
			);

			$this->add_control(
				'description',
				array(
					'label'   => __( 'توضیح', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 6,
					'default' => '',
				)
			);

			$feature = new \Elementor\Repeater();
			$feature->add_control(
				'feature',
				array(
					'label'   => __( 'متن', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$feature->add_control(
				'feature_icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => function_exists( 'zc_icon_choice' ) ? zc_icon_choice() : array(),
					'default' => 'check',
				)
			);

			$this->add_control(
				'features',
				array(
					'label'       => __( 'ویژگی‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $feature->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ feature }}}',
				)
			);

			$this->button_controls();

			$this->end_controls_section();

			$this->start_controls_section( 'cover_section', array( 'label' => __( 'جلد', 'zarincoach' ) ) );

			$this->add_control(
				'cover',
				array(
					'label'       => __( 'تصویر جلد (اختیاری)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::MEDIA,
					'default'     => array( 'url' => '' ),
					'description' => __( 'خالی = جلد تایپوگرافیک خودکار با رنگ‌های پالت (سرمه‌ای و طلایی).', 'zarincoach' ),
				)
			);

			$this->add_control(
				'cover_kicker',
				array(
					'label'   => __( 'متن بالای جلد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				'cover_position',
				array(
					'label'   => __( 'جایگاه جلد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'start' => __( 'راست', 'zarincoach' ),
						'end'   => __( 'چپ', 'zarincoach' ),
					),
					'default' => 'start',
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
			$s      = $this->get_settings_for_display();
			$title  = isset( $s['book_title'] ) ? trim( (string) $s['book_title'] ) : '';
			$author = isset( $s['author'] ) ? trim( (string) $s['author'] ) : '';
			if ( '' === $author && function_exists( 'zc_legal_info' ) ) {
				$author = zc_legal_info( 'owner' );
			}
			if ( '' === $title ) {
				return;
			}
			$eyebrow  = isset( $s['eyebrow'] ) ? trim( (string) $s['eyebrow'] ) : '';
			$desc     = isset( $s['description'] ) ? trim( (string) $s['description'] ) : '';
			$features = ! empty( $s['features'] ) ? (array) $s['features'] : array();
			$cover    = isset( $s['cover']['url'] ) ? (string) $s['cover']['url'] : '';
			$kicker   = isset( $s['cover_kicker'] ) ? trim( (string) $s['cover_kicker'] ) : '';
			$role     = isset( $s['role_label'] ) ? trim( (string) $s['role_label'] ) : '';
			$end      = isset( $s['cover_position'] ) && 'end' === $s['cover_position'];

			// اسکیمای Book (نویسنده = Person سایت اگر نام یکی باشد).
			if ( function_exists( 'zc_schema_add_node' ) && zc_schema_can_collect() ) {
				$is_owner = '' === $author || false !== mb_strpos( $author, zc_seo_person_name() );
				zc_schema_add_node(
					array_filter(
						array(
							'@type'       => 'Book',
							'@id'         => zc_schema_id( 'book-' . substr( md5( $title ), 0, 8 ) ),
							'name'        => $title,
							'author'      => $is_owner ? array( '@id' => zc_schema_id( 'person' ) ) : array(
								'@type' => 'Person',
								'name'  => $author,
							),
							'description' => zc_seo_clean( $desc, 300 ),
							'image'       => '' !== $cover ? $cover : null,
							'inLanguage'  => 'fa-IR',
						)
					)
				);
			}
			?>
			<section class="zc-section zc-book-widget relative overflow-hidden">
				<div class="zc-container">
					<div class="zc-book-panel">
						<div class="grid items-center gap-8 lg:grid-cols-12 lg:gap-12">
							<div class="lg:col-span-5 <?php echo $end ? 'lg:order-2' : ''; ?>">
								<div class="zc-book3d-stage zc-reveal">
									<div class="zc-book3d" role="img" aria-label="<?php echo esc_attr( sprintf( /* translators: %s: نام کتاب */ __( 'جلد کتاب %s', 'zarincoach' ), $title ) ); ?>">
										<div class="zc-book3d-front">
											<?php if ( '' !== $cover ) : ?>
												<?php echo zc_image( isset( $s['cover'] ) ? $s['cover'] : $cover, 'medium_large', array( 'sizes' => '(min-width: 1024px) 300px, 60vw' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
											<?php else : ?>
												<span class="zc-book3d-kicker"><?php echo esc_html( $kicker ); ?></span>
												<span class="zc-book3d-mark" aria-hidden="true"><?php zc_icon( 'brain', 'h-10 w-10' ); ?></span>
												<strong class="zc-book3d-title"><?php echo esc_html( $title ); ?></strong>
												<span class="zc-book3d-author"><?php echo esc_html( $author ); ?></span>
											<?php endif; ?>
										</div>
									</div>
									<span class="zc-book3d-shadow" aria-hidden="true"></span>
								</div>
							</div>

							<div class="lg:col-span-7 <?php echo $end ? 'lg:order-1' : ''; ?>">
								<?php if ( '' !== $eyebrow ) : ?>
									<span class="zc-eyebrow zc-reveal"><?php echo esc_html( $eyebrow ); ?></span>
								<?php endif; ?>
								<h2 class="zc-title-lg zc-text-balance zc-reveal mt-4" data-zc-delay="60">«<?php echo esc_html( $title ); ?>»</h2>
								<?php if ( '' !== $author ) : ?>
									<p class="zc-reveal mt-4 inline-flex items-center gap-2 rounded-full border border-line bg-surface px-4 py-1.5 text-[0.88rem] text-muted" data-zc-delay="100">
										<?php zc_icon( 'user', 'h-4 w-4 text-primary' ); ?>
										<span><?php echo esc_html( '' !== $role ? $role . ': ' : '' ); ?><strong class="text-secondary"><?php echo esc_html( $author ); ?></strong></span>
									</p>
								<?php endif; ?>
								<?php if ( '' !== $desc ) : ?>
									<div class="zc-reveal mt-6 space-y-4 text-[0.98rem] leading-[2.05] text-muted" data-zc-delay="140">
										<?php echo wp_kses_post( wpautop( $desc ) ); ?>
									</div>
								<?php endif; ?>
								<?php if ( ! empty( $features ) ) : ?>
									<ul class="zc-reveal mt-7 grid gap-3 sm:grid-cols-2" data-zc-delay="180">
										<?php
										foreach ( $features as $feature ) :
											$text = isset( $feature['feature'] ) ? trim( (string) $feature['feature'] ) : '';
											if ( '' === $text ) {
												continue;
											}
											?>
											<li class="flex items-start gap-3 text-[0.92rem] leading-[1.9] text-ink">
												<span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-primary/10 text-primary"><?php zc_icon( ! empty( $feature['feature_icon'] ) ? (string) $feature['feature_icon'] : 'check', 'h-4 w-4' ); ?></span>
												<span><?php echo esc_html( $text ); ?></span>
											</li>
										<?php endforeach; ?>
									</ul>
								<?php endif; ?>
								<div class="zc-reveal mt-8" data-zc-delay="220"><?php $this->render_button(); ?></div>
							</div>
						</div>
					</div>
				</div>
			</section>
			<?php
		}
	}
endif;
