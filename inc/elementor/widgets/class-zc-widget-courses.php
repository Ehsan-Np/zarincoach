<?php
/**
 * ویجت «دوره‌ها و گواهی‌ها»
 *
 * فهرست دوره‌های گذرانده‌شده با جمع خودکار ساعات، شمارش دوره‌ها و مراجع آموزشی،
 * فیلتر دسته‌بندی، نوار نسبت ساعت و کارت «دوره‌های دیگر».
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Courses' ) ) :

	/**
	 * ویجت دوره‌ها.
	 */
	class ZC_Widget_Courses extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-courses';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'دوره‌ها و گواهی‌ها', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-price-list';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'دوره', 'گواهی', 'certificate', 'رزومه', 'آموزش' ) );
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
				'c_title',
				array(
					'label'   => __( 'عنوان دوره', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);
			$repeater->add_control(
				'c_hours',
				array(
					'label'   => __( 'ساعت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'c_org',
				array(
					'label'   => __( 'مرجع برگزارکننده', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'c_date',
				array(
					'label'   => __( 'تاریخ', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);
			$repeater->add_control(
				'c_cat',
				array(
					'label'       => __( 'دسته‌بندی', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'دکمه‌های فیلتر از همین دسته‌ها ساخته می‌شوند.', 'zarincoach' ),
				)
			);
			$repeater->add_control(
				'c_featured',
				array(
					'label'        => __( 'دوره شاخص (کارت برجسته)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => '',
				)
			);

			$this->add_control(
				'items',
				array(
					'label'       => __( 'دوره‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $repeater->get_controls(),
					'default'     => array(),
					'title_field' => '{{{ c_title }}} ({{{ c_hours }}})',
				)
			);

			$this->add_control(
				'extra_count',
				array(
					'label'       => __( 'تعداد دوره‌های دیگر (ذکرنشده)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'description' => __( 'در کارت پایانی و جمع تعداد دوره‌ها لحاظ می‌شود. خالی = بدون کارت.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'extra_label',
				array(
					'label'   => __( 'متن کارت دوره‌های دیگر', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'دوره تخصصی دیگر', 'zarincoach' ),
				)
			);

			$this->end_controls_section();

			$this->start_controls_section( 'layout_section', array( 'label' => __( 'چیدمان و نمایش', 'zarincoach' ) ) );

			$this->columns_control( 'columns', __( 'تعداد ستون', 'zarincoach' ), '3' );

			$this->add_control(
				'summary',
				array(
					'label'        => __( 'نوار خلاصه (جمع ساعات، تعداد دوره‌ها، مراجع)', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'label_hours',
				array(
					'label'     => __( 'برچسب جمع ساعات', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'ساعت آموزش تخصصی', 'zarincoach' ),
					'condition' => array( 'summary' => 'yes' ),
				)
			);

			$this->add_control(
				'label_count',
				array(
					'label'     => __( 'برچسب تعداد دوره‌ها', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'دوره و کارگاه گذرانده‌شده', 'zarincoach' ),
					'condition' => array( 'summary' => 'yes' ),
				)
			);

			$this->add_control(
				'label_orgs',
				array(
					'label'     => __( 'برچسب تعداد مراجع', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'مرجع و مؤسسه آموزشی', 'zarincoach' ),
					'condition' => array( 'summary' => 'yes' ),
				)
			);

			$this->add_control(
				'filter',
				array(
					'label'        => __( 'فیلتر دسته‌بندی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'all_label',
				array(
					'label'     => __( 'متن دکمه «همه»', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'default'   => __( 'همه دوره‌ها', 'zarincoach' ),
					'condition' => array( 'filter' => 'yes' ),
				)
			);

			$this->add_control(
				'bar',
				array(
					'label'        => __( 'نوار نسبت ساعت', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
				)
			);

			$this->add_control(
				'hours_unit',
				array(
					'label'   => __( 'واحد ساعت', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => __( 'ساعت', 'zarincoach' ),
				)
			);

			$this->add_control(
				'org_icon',
				array(
					'label'       => __( 'آیکن برگزارکننده', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::ICONS,
					'skin'        => 'inline',
					'label_block' => false,
					'default'     => array(
						'value'   => '',
						'library' => '',
					),
					'description' => __( 'خالی = ساختمان.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'date_icon',
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
				)
			);

			$this->end_controls_section();
		}

		/**
		 * عدد لاتین از رشته.
		 *
		 * @param string $value مقدار.
		 * @return float
		 */
		private function num( $value ) {
			return (float) preg_replace( '/[^\d.]/', '', zc_digits_to_latin( (string) $value ) );
		}

		/**
		 * کنترل‌های نمایش اجزا و استایل (نسخه‌ی ۲.۲).
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
			$this->zc_toggles(
				array(
					'cat'   => array( __( 'دسته‌ی دوره', 'zarincoach' ), '.zc-course-cat' ),
					'hours' => array( __( 'ساعت دوره', 'zarincoach' ), '.zc-course-hours' ),
					'org'   => array( __( 'برگزارکننده', 'zarincoach' ), '.zc-course-org' ),
					'date'  => array( __( 'تاریخ', 'zarincoach' ), '.zc-course-date' ),
					'icons' => array( __( 'آیکن‌های جزئیات', 'zarincoach' ), '.zc-course-meta svg, .zc-course-meta i' ),
					'extra' => array( __( 'کارت «دوره‌های دیگر»', 'zarincoach' ), '.zc-course.is-extra' ),
				)
			);
			$this->zc_style(
				'cr_summary',
				__( 'آمار خلاصه', 'zarincoach' ),
				array(
					'box'  => array( 'box', '.zc-courses-summary', __( 'قاب آمار', 'zarincoach' ), array( 'gradient' => false ) ),
					'cell' => array( 'box', '.zc-courses-summary > div', __( 'خانه‌ها', 'zarincoach' ), array( 'align' => true ) ),
					'num'  => array( 'text', '.zc-courses-summary dd', __( 'عدد', 'zarincoach' ), array( 'margin' => false ) ),
					'num1' => array( 'color', '.zc-courses-summary > div:first-child dd', __( 'رنگ عدد اول', 'zarincoach' ) ),
					'lbl'  => array( 'text', '.zc-courses-summary dt', __( 'برچسب', 'zarincoach' ) ),
				),
				array( 'condition' => array( 'summary' => 'yes' ) )
			);
			$this->zc_style(
				'cr_filter',
				__( 'فیلتر دسته‌ها', 'zarincoach' ),
				array(
					'wrap'   => array( 'size', '.zc-courses-filter', __( 'فاصله از بالا', 'zarincoach' ), array( 'prop' => 'margin-top', 'max' => 80 ) ),
					'chip'   => array( 'button', '.zc-courses-filter .zc-chip', __( 'دکمه‌ها', 'zarincoach' ) ),
					'active' => array( 'color', '.zc-courses-filter .zc-chip.is-active', __( 'پس‌زمینه‌ی دکمه‌ی فعال', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'actc'   => array( 'color', '.zc-courses-filter .zc-chip.is-active', __( 'رنگ متن دکمه‌ی فعال', 'zarincoach' ) ),
				),
				array( 'condition' => array( 'filter' => 'yes' ) )
			);
			$this->zc_style(
				'cr_card',
				__( 'کارت دوره‌ها', 'zarincoach' ),
				array(
					'grid'  => array( 'grid', '.zc-courses-grid', __( 'شبکه', 'zarincoach' ), array( 'cols' => false ) ),
					'card'  => array( 'box', '.zc-course:not(.is-featured):not(.is-extra)', __( 'کارت عادی', 'zarincoach' ), array( 'hover' => true ) ),
					'feat'  => array( 'box', '.zc-course.is-featured', __( 'کارت ویژه', 'zarincoach' ), array( 'hover' => true, 'gradient' => true ) ),
					'extra' => array( 'box', '.zc-course.is-extra', __( 'کارت «دوره‌های دیگر»', 'zarincoach' ) ),
				)
			);
			$this->zc_style(
				'cr_text',
				__( 'متن کارت‌ها', 'zarincoach' ),
				array(
					'cat'   => array( 'text', '.zc-course-cat', __( 'دسته', 'zarincoach' ), array( 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'hours' => array( 'text', '.zc-course-hours strong', __( 'عدد ساعت', 'zarincoach' ), array( 'margin' => false ) ),
					'unit'  => array( 'text', '.zc-course-hours', __( 'واحد ساعت', 'zarincoach' ), array( 'margin' => false ) ),
					'title' => array( 'text', '.zc-course-title', __( 'عنوان', 'zarincoach' ), array( 'hover' => '.zc-course' ) ),
					'meta'  => array( 'text', '.zc-course-meta', __( 'جزئیات', 'zarincoach' ), array( 'margin' => false ) ),
					'micon' => array( 'color', '.zc-course-meta svg, .zc-course-meta i', __( 'رنگ آیکن جزئیات', 'zarincoach' ) ),
					'bar'   => array( 'color', '.zc-course-bar', __( 'زمینه‌ی نوار ساعت', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'fill'  => array( 'color', '.zc-course-bar i', __( 'رنگ نوار ساعت', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'barh'  => array( 'size', '.zc-course-bar', __( 'ضخامت نوار', 'zarincoach' ), array( 'prop' => 'height', 'units' => array( 'px' ), 'max' => 20 ) ),
					'xnum'  => array( 'text', '.zc-course-extra-num', __( 'عدد «دوره‌های دیگر»', 'zarincoach' ), array( 'margin' => false ) ),
					'xlbl'  => array( 'text', '.zc-course-extra-label', __( 'متن «دوره‌های دیگر»', 'zarincoach' ) ),
				)
			);
		}

		/**
		 * خروجی.
		 *
		 * @return void
		 */
		protected function render() {
			$s     = $this->get_settings_for_display();
			$items = ! empty( $s['items'] ) ? (array) $s['items'] : array();

			$rows  = array();
			$cats  = array();
			$orgs  = array();
			$total = 0;
			$max   = 0;

			foreach ( $items as $item ) {
				$title = isset( $item['c_title'] ) ? trim( (string) $item['c_title'] ) : '';
				if ( '' === $title ) {
					continue;
				}
				$hours = $this->num( isset( $item['c_hours'] ) ? $item['c_hours'] : '' );
				$cat   = isset( $item['c_cat'] ) ? trim( (string) $item['c_cat'] ) : '';
				$org   = isset( $item['c_org'] ) ? trim( (string) $item['c_org'] ) : '';

				$total += $hours;
				$max    = max( $max, $hours );
				if ( '' !== $cat && ! in_array( $cat, $cats, true ) ) {
					$cats[] = $cat;
				}
				if ( '' !== $org ) {
					$orgs[ md5( preg_replace( '/\s+/u', '', $org ) ) ] = true;
				}

				$rows[] = array(
					'title'    => $title,
					'hours'    => $hours,
					'org'      => $org,
					'date'     => isset( $item['c_date'] ) ? trim( (string) $item['c_date'] ) : '',
					'cat'      => $cat,
					'featured' => $this->is_on( $item, 'c_featured' ),
				);
			}

			if ( empty( $rows ) ) {
				return;
			}

			// دوره‌های گذرانده‌شده ← مدارک (certificate) در گره‌ی Person اسکیما.
			if ( function_exists( 'zc_schema_add_node' ) && zc_schema_can_collect() ) {
				$certs = array();
				foreach ( array_slice( $rows, 0, 30 ) as $row ) {
					$certs[] = array_filter(
						array(
							'@type'              => 'EducationalOccupationalCredential',
							'credentialCategory' => 'certificate',
							'name'               => $row['title'],
							'recognizedBy'       => ( '' !== $row['org'] && ! preg_match( '/^زیر نظر/u', $row['org'] ) ) ? array(
								'@type' => 'Organization',
								'name'  => $row['org'],
							) : null,
						)
					);
				}
				zc_schema_add_node(
					array(
						'@id'           => zc_schema_id( 'person' ),
						'hasCredential' => $certs,
					)
				);
			}

			$extra   = (int) $this->num( isset( $s['extra_count'] ) ? $s['extra_count'] : '' );
			$unit    = isset( $s['hours_unit'] ) ? (string) $s['hours_unit'] : '';
			$columns = isset( $s['columns'] ) ? (string) $s['columns'] : '3';
			$uid     = 'zc-courses-' . $this->get_id();
			$fmt     = static function ( $n ) {
				return zc_digits_to_persian( number_format( (float) $n, 0, '.', '٬' ) );
			};
			?>
			<section class="zc-section zc-courses" id="<?php echo esc_attr( $uid ); ?>" data-zc-filter-scope>
				<div class="zc-container">
					<?php $this->render_heading(); ?>

					<?php if ( $this->is_on( $s, 'summary' ) ) : ?>
						<dl class="zc-courses-summary zc-reveal zc-after-head">
							<div>
								<dd><span data-zc-count="<?php echo esc_attr( (string) (int) $total ); ?>" data-zc-suffix="+" data-zc-persian="1"><?php echo esc_html( zc_digits_to_persian( (string) (int) $total ) . '+' ); ?></span></dd>
								<dt><?php echo esc_html( isset( $s['label_hours'] ) ? (string) $s['label_hours'] : '' ); ?></dt>
							</div>
							<div>
								<dd><span data-zc-count="<?php echo esc_attr( (string) ( count( $rows ) + $extra ) ); ?>" data-zc-suffix="<?php echo $extra ? '+' : ''; ?>" data-zc-persian="1"><?php echo esc_html( zc_digits_to_persian( (string) ( count( $rows ) + $extra ) ) . ( $extra ? '+' : '' ) ); ?></span></dd>
								<dt><?php echo esc_html( isset( $s['label_count'] ) ? (string) $s['label_count'] : '' ); ?></dt>
							</div>
							<?php if ( ! empty( $orgs ) ) : ?>
								<div>
									<dd><span data-zc-count="<?php echo esc_attr( (string) count( $orgs ) ); ?>" data-zc-suffix="" data-zc-persian="1"><?php echo esc_html( zc_digits_to_persian( (string) count( $orgs ) ) ); ?></span></dd>
									<dt><?php echo esc_html( isset( $s['label_orgs'] ) ? (string) $s['label_orgs'] : '' ); ?></dt>
								</div>
							<?php endif; ?>
						</dl>
					<?php endif; ?>

					<?php if ( $this->is_on( $s, 'filter' ) && count( $cats ) > 1 ) : ?>
						<div class="zc-courses-filter zc-post-filter zc-no-scrollbar mt-8 flex gap-2 overflow-x-auto pb-1" role="group" aria-label="<?php esc_attr_e( 'فیلتر دوره‌ها', 'zarincoach' ); ?>">
							<button type="button" class="zc-chip is-active" data-zc-filter="*" aria-pressed="true"><?php echo esc_html( isset( $s['all_label'] ) ? (string) $s['all_label'] : '' ); ?></button>
							<?php foreach ( $cats as $cat ) : ?>
								<button type="button" class="zc-chip whitespace-nowrap" data-zc-filter="<?php echo esc_attr( md5( $cat ) ); ?>" aria-pressed="false"><?php echo esc_html( $cat ); ?></button>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<ul class="zc-courses-grid mt-8 grid gap-4 <?php echo esc_attr( $this->grid_classes( $columns ) ); ?>">
						<?php foreach ( $rows as $row ) : ?>
							<?php $pct = $max > 0 ? max( 6, round( $row['hours'] / $max * 100 ) ) : 0; ?>
							<li class="zc-course<?php echo $row['featured'] ? ' is-featured' : ''; ?>" data-zc-filter-item="<?php echo esc_attr( '' !== $row['cat'] ? md5( $row['cat'] ) : '' ); ?>">
								<div class="zc-course-top flex items-start justify-between gap-3">
									<?php if ( '' !== $row['cat'] ) : ?>
										<span class="zc-course-cat"><?php echo esc_html( $row['cat'] ); ?></span>
									<?php endif; ?>
									<?php if ( $row['hours'] > 0 ) : ?>
										<span class="zc-course-hours"><strong><?php echo esc_html( $fmt( $row['hours'] ) ); ?></strong> <?php echo esc_html( $unit ); ?></span>
									<?php endif; ?>
								</div>
								<h3 class="zc-course-title"><?php echo esc_html( $row['title'] ); ?></h3>
								<div class="zc-course-meta">
									<?php if ( '' !== $row['org'] ) : ?>
										<span class="zc-course-org"><?php $this->zc_render_icon_or( isset( $s['org_icon'] ) ? $s['org_icon'] : array(), 'building', 'h-4 w-4 shrink-0' ); ?><?php echo esc_html( $row['org'] ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $row['date'] ) : ?>
										<span class="zc-course-date"><?php $this->zc_render_icon_or( isset( $s['date_icon'] ) ? $s['date_icon'] : array(), 'calendar', 'h-4 w-4 shrink-0' ); ?><?php echo esc_html( $row['date'] ); ?></span>
									<?php endif; ?>
								</div>
								<?php if ( $this->is_on( $s, 'bar' ) && $pct > 0 ) : ?>
									<span class="zc-course-bar" aria-hidden="true"><i style="width: <?php echo esc_attr( (string) $pct ); ?>%"></i></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>

						<?php if ( $extra > 0 ) : ?>
							<li class="zc-course is-extra" data-zc-filter-item="*">
								<strong class="zc-course-extra-num block text-[2.4rem] font-bold leading-none text-primary"><?php echo esc_html( '+' . zc_digits_to_persian( (string) $extra ) ); ?></strong>
								<span class="zc-course-extra-label mt-2 block text-[0.95rem] font-bold text-secondary"><?php echo esc_html( isset( $s['extra_label'] ) ? (string) $s['extra_label'] : '' ); ?></span>
							</li>
						<?php endif; ?>
					</ul>
				</div>
			</section>
			<?php
		}
	}
endif;
