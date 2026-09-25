<?php
/**
 * کلاس پایه برای ویجت‌های اختصاصی زرین‌کوچ
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Base' ) ) :

	/**
	 * کلاس پایه ویجت‌ها.
	 */
	abstract class ZC_Widget_Base extends \Elementor\Widget_Base {

		/**
		 * ویجت غیرفعال‌شده در پنل «ویجت‌های فعال» فقط از فهرست ویرایشگر پنهان می‌شود؛
		 * همچنان ثبت و رندر می‌شود تا صفحاتی که از آن استفاده کرده‌اند خراب نشوند.
		 *
		 * @return bool
		 */
		/**
		 * دسته‌بندی ویجت.
		 *
		 * @return array<int, string>
		 */
		public function show_in_panel() {
			$slug = preg_replace( '/^zc-/', '', $this->get_name() );
			return ! class_exists( 'ZC_Elementor' ) || ZC_Elementor::instance()->is_widget_enabled( $slug );
		}

		/**
		 * دسته‌ی ویجت.
		 *
		 * @return array
		 */
		public function get_categories() {
			return array( 'zarincoach' );
		}

		/**
		 * آیکون پیش‌فرض.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-star';
		}

		/**
		 * کلیدواژه‌های جستجو.
		 *
		 * @return array<int, string>
		 */
		public function get_keywords() {
			return array( 'زرین‌کوچ', 'کوچینگ', 'مریم جمالی', 'zarincode', 'coaching' );
		}

		/**
		 * ثبت کنترل‌ها + کنترل مشترک «تُن بخش» برای همه‌ی ویجت‌های زرین‌کوچ.
		 *
		 * با انتخاب «تیره / سرمه‌ای»، کلاس zc-tone-inverse به پوسته‌ی ویجت افزوده می‌شود
		 * و تمام اجزای داخلی با متغیرهای رنگی نسخه تیره (Deep Navy) هماهنگ می‌شوند.
		 *
		 * @return void
		 */
		protected function init_controls() {
			parent::init_controls();

			if ( null !== $this->get_controls( 'zc_tone' ) ) {
				return;
			}

			$this->start_controls_section(
				'zc_tone_section',
				array(
					'label' => __( 'تُن بخش (رنگ زمینه)', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);

			$this->add_control(
				'zc_tone',
				array(
					'label'        => __( 'تُن رنگی', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SELECT,
					'options'      => array(
						''        => __( 'روشن (پیش‌فرض)', 'zarincoach' ),
						'inverse' => __( 'تیره / سرمه‌ای (Deep Navy)', 'zarincoach' ),
					),
					'default'      => '',
					'prefix_class' => 'zc-tone-',
					'description'  => __( 'پس‌زمینه‌ی بخش با رنگ ثانویه پالت (در پالت سرمه‌ای: Deep Navy) و متن روشن نمایش داده می‌شود.', 'zarincoach' ),
				)
			);

			$this->end_controls_section();
		}

		/**
		 * دریافت مقدار یک تنظیم با fallback به پنل تنظیمات قالب.
		 *
		 * @param string $key     کلید کنترل.
		 * @param string $option  کلید متناظر در پنل تنظیمات (اختیاری).
		 * @param string $default مقدار پیش‌فرض نهایی.
		 * @return string
		 */
		protected function value( $key, $option = '', $default = '' ) {
			$settings = $this->get_settings_for_display();
			$value    = isset( $settings[ $key ] ) ? $settings[ $key ] : '';

			if ( is_array( $value ) ) {
				if ( isset( $value['url'] ) ) {
					$value = (string) $value['url'];
				} else {
					$value = '';
				}
			}

			$value = (string) $value;

			if ( '' !== trim( $value ) ) {
				return $value;
			}

			if ( '' !== $option && zc_switch( 'elementor_defaults', true ) ) {
				$from_option = (string) zc_opt( $option, '' );
				if ( '' !== trim( $from_option ) ) {
					return $from_option;
				}
			}

			return $default;
		}

		/**
		 * رسانه‌ی یک کنترل (شناسه + آدرس) برای خروجی واکنش‌گرا با zc_image().
		 *
		 * @param string $key     کلید کنترل.
		 * @param string $option  گزینه‌ی Redux جایگزین.
		 * @param string $default نوع تصویر جایگزین.
		 * @return array{id:int,url:string}
		 */
		protected function image_src( $key, $option = '', $default = 'card' ) {
			$settings = $this->get_settings_for_display();
			$media    = isset( $settings[ $key ] ) && is_array( $settings[ $key ] ) ? $settings[ $key ] : array();

			if ( empty( $media['url'] ) && '' !== $option && zc_switch( 'elementor_defaults', true ) ) {
				$media = (array) zc_opt( $option, array() );
			}
			if ( empty( $media['url'] ) ) {
				return array(
					'id'  => 0,
					'url' => '' === $default ? '' : zc_placeholder( $default ),
				);
			}
			return array(
				'id'  => isset( $media['id'] ) ? (int) $media['id'] : 0,
				'url' => (string) $media['url'],
			);
		}

		/**
		 * آدرس تصویر یک کنترل رسانه (با جایگزین Redux و تصویر پیش‌فرض).
		 *
		 * @param string $key     کلید کنترل.
		 * @param string $option  گزینه‌ی Redux جایگزین.
		 * @param string $default نوع تصویر جایگزین.
		 * @return string
		 */
		protected function image_url( $key, $option = '', $default = 'card' ) {
			$settings = $this->get_settings_for_display();
			$url      = '';

			if ( isset( $settings[ $key ]['url'] ) ) {
				$url = (string) $settings[ $key ]['url'];
			}

			if ( '' === $url && '' !== $option && zc_switch( 'elementor_defaults', true ) ) {
				$media = (array) zc_opt( $option, array() );
				if ( isset( $media['url'] ) ) {
					$url = (string) $media['url'];
				}
			}

			if ( '' === $url ) {
				$url = '' === $default ? '' : zc_placeholder( $default );
			}

			return $url;
		}

		/**
		 * ثبت کنترل‌های مشترکِ سربرگِ بخش.
		 *
		 * @param string $prefix پیشوند کلیدها (اختیاری).
		 * @return void
		 */
		protected function heading_controls( $prefix = '' ) {
			$this->add_control(
				$prefix . 'eyebrow',
				array(
					'label'   => __( 'برچسب کوتاه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				$prefix . 'title',
				array(
					'label'   => __( 'عنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 2,
					'default' => '',
				)
			);

			$this->add_control(
				$prefix . 'subtitle',
				array(
					'label'   => __( 'زیرعنوان', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXTAREA,
					'rows'    => 3,
					'default' => '',
				)
			);

			$this->add_control(
				$prefix . 'align',
				array(
					'label'   => __( 'تراز', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::CHOOSE,
					'options' => array(
						'right'  => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-text-align-right',
						),
						'center' => array(
							'title' => __( 'وسط', 'zarincoach' ),
							'icon'  => 'eicon-text-align-center',
						),
						'left'   => array(
							'title' => __( 'چپ', 'zarincoach' ),
							'icon'  => 'eicon-text-align-left',
						),
					),
					'default' => 'center',
					'toggle'  => false,
				)
			);
		}

		/**
		 * خروجی سربرگ بخش.
		 *
		 * @param string $prefix پیشوند کلیدها.
		 * @param string $tag   تگ عنوان.
		 * @return void
		 */
		protected function render_heading( $prefix = '', $tag = 'h2' ) {
			$settings = $this->get_settings_for_display();

			$eyebrow  = isset( $settings[ $prefix . 'eyebrow' ] ) ? trim( (string) $settings[ $prefix . 'eyebrow' ] ) : '';
			$title    = isset( $settings[ $prefix . 'title' ] ) ? trim( (string) $settings[ $prefix . 'title' ] ) : '';
			$subtitle = isset( $settings[ $prefix . 'subtitle' ] ) ? trim( (string) $settings[ $prefix . 'subtitle' ] ) : '';
			$align    = isset( $settings[ $prefix . 'align' ] ) ? (string) $settings[ $prefix . 'align' ] : 'center';

			if ( '' === $eyebrow && '' === $title && '' === $subtitle ) {
				return;
			}

			$align_class = array(
				'center' => 'text-center mx-auto items-center',
				'right'  => 'text-right items-start ml-auto mr-0',
				'left'   => 'text-left items-end mr-auto ml-0',
			);
			$align_class = isset( $align_class[ $align ] ) ? $align_class[ $align ] : $align_class['center'];

			$tag = in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'div' ), true ) ? $tag : 'h2';
			?>
			<header class="zc-section-head zc-reveal flex max-w-3xl flex-col gap-3 <?php echo esc_attr( $align_class ); ?>">
				<?php if ( '' !== $eyebrow ) : ?>
					<span class="zc-eyebrow <?php echo 'center' === $align ? '' : 'flex-row-reverse'; ?>"><?php echo esc_html( $eyebrow ); ?></span>
				<?php endif; ?>

				<?php if ( '' !== $title ) : ?>
					<<?php echo esc_html( $tag ); ?> class="zc-title-lg zc-text-balance"><?php echo esc_html( $title ); ?></<?php echo esc_html( $tag ); ?>>
				<?php endif; ?>

				<?php if ( '' !== $subtitle ) : ?>
					<p class="zc-lead mt-1 !text-[1rem]"><?php echo esc_html( $subtitle ); ?></p>
				<?php endif; ?>
			</header>
			<?php
		}

		/**
		 * ثبت کنترل‌های دکمه.
		 *
		 * @param string $prefix پیشوند.
		 * @param string $label برچسب گروه.
		 * @return void
		 */
		protected function button_controls( $prefix = '', $label = '' ) {
			$this->add_control(
				$prefix . 'button_heading',
				array(
					'label'     => '' !== $label ? $label : __( 'دکمه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::HEADING,
					'separator' => 'before',
				)
			);

			$this->add_control(
				$prefix . 'button_text',
				array(
					'label'   => __( 'متن دکمه', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::TEXT,
					'default' => '',
				)
			);

			$this->add_control(
				$prefix . 'button_url',
				array(
					'label'       => __( 'لینک', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => '#booking',
					'default'     => array( 'url' => '' ),
				)
			);

			$this->add_control(
				$prefix . 'button_style',
				array(
					'label'   => __( 'سبک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'primary'   => __( 'اصلی', 'zarincoach' ),
						'secondary' => __( 'فرعی', 'zarincoach' ),
						'outline'   => __( 'خطی', 'zarincoach' ),
						'ghost'     => __( 'ساده', 'zarincoach' ),
					),
					'default' => 'primary',
				)
			);
		}

		/**
		 * خروجی دکمه.
		 *
		 * @param string $prefix پیشوند.
		 * @param string $class کلاس اضافی.
		 * @return void
		 */
		protected function render_button( $prefix = '', $class = '' ) {
			$settings = $this->get_settings_for_display();

			$text  = isset( $settings[ $prefix . 'button_text' ] ) ? trim( (string) $settings[ $prefix . 'button_text' ] ) : '';
			$style = isset( $settings[ $prefix . 'button_style' ] ) ? (string) $settings[ $prefix . 'button_style' ] : 'primary';
			$url   = isset( $settings[ $prefix . 'button_url' ]['url'] ) ? (string) $settings[ $prefix . 'button_url' ]['url'] : '';

			if ( '' === $text ) {
				return;
			}

			if ( '' === $url ) {
				return;
			}

			$target = ! empty( $settings[ $prefix . 'button_url' ]['is_external'] ) ? ' target="_blank" rel="noopener"' : '';
			$nofollow = ! empty( $settings[ $prefix . 'button_url' ]['nofollow'] ) ? ' rel="nofollow"' : '';
			?>
			<a href="<?php echo esc_url( $url ); ?>" class="<?php echo esc_attr( trim( 'zc-btn zc-btn-' . $style . ' ' . $class ) ); ?>"<?php echo $target . $nofollow; // phpcs:ignore ?>>
				<span><?php echo esc_html( $text ); ?></span>
				<?php zc_icon( 'arrow-left', 'h-4 w-4 zc-btn-arrow' ); ?>
			</a>
			<?php
		}

		/**
		 * انتخاب ستون‌ها برای شبکه.
		 *
		 * @param string $key     کلید کنترل.
		 * @param string $label   برچسب.
		 * @param string $default پیش‌فرض.
		 * @return void
		 */
		protected function columns_control( $key = 'columns', $label = '', $default = '3' ) {
			$this->add_control(
				$key,
				array(
					'label'   => '' !== $label ? $label : __( 'تعداد ستون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'1' => __( '۱', 'zarincoach' ),
						'2' => __( '۲', 'zarincoach' ),
						'3' => __( '۳', 'zarincoach' ),
						'4' => __( '۴', 'zarincoach' ),
					),
					'default' => $default,
				)
			);
		}

		/**
		 * تبدیل تعداد ستون به کلاس‌های شبکه.
		 *
		 * @param string $columns تعداد ستون.
		 * @return string
		 */
		protected function grid_classes( $columns ) {
			switch ( (string) $columns ) {
				case '1':
					return 'grid-cols-1';
				case '2':
					return 'sm:grid-cols-2';
				case '4':
					return 'sm:grid-cols-2 lg:grid-cols-4';
				case '3':
				default:
					return 'md:grid-cols-2 lg:grid-cols-3';
			}
		}

		/**
		 * خروجی آیکون انتخاب‌شده.
		 *
		 * @param string $icon  نام آیکون داخلی یا آرایه آیکون المنتور.
		 * @param string $class کلاس.
		 * @return void
		 */
		protected function render_icon( $icon, $class = 'h-7 w-7' ) {
			if ( is_array( $icon ) && ! empty( $icon['value'] ) ) {
				\Elementor\Icons_Manager::render_icon( $icon, array( 'aria-hidden' => 'true' ) );
				return;
			}

			$name = is_array( $icon ) ? '' : (string) $icon;
			zc_icon( '' !== $name ? $name : 'sparkles', $class );
		}

		/**
		 * گزینه‌های انتخاب منو (۰ = جایگاه منوی تعیین‌شده در «نمایش ← فهرست‌ها»).
		 *
		 * @return array<int|string, string>
		 */
		protected function menu_options() {
			$options = array( '0' => __( 'جایگاه منوی پیش‌فرض قالب', 'zarincoach' ) );
			$menus   = wp_get_nav_menus();
			if ( is_array( $menus ) ) {
				foreach ( $menus as $menu ) {
					$options[ (string) $menu->term_id ] = $menu->name;
				}
			}
			return $options;
		}

		/**
		 * مقدار سوییچ المنتور.
		 *
		 * @param array<string, mixed> $settings تنظیمات.
		 * @param string               $key      کلید.
		 * @return bool
		 */
		protected function is_on( $settings, $key ) {
			return isset( $settings[ $key ] ) && 'yes' === (string) $settings[ $key ];
		}

		/**
		 * گزینه‌های مشترک فهرست (برای استفاده در ویجت «متن و محتوا» هم).
		 *
		 * @param string                    $prefix    پیشوند کلید.
		 * @param array                     $condition شرط نمایش.
		 * @param array                     $defaults  مقادیر پیش‌فرض.
		 * @param array                     $columns_condition شرط اضافه برای کنترل ستون‌ها.
		 * @return void
		 */
		protected function toc_style_controls( $prefix = '', $condition = array(), $defaults = array(), $columns_condition = array() ) {
			$el = $this;
			$d = wp_parse_args(
				$defaults,
				array(
					'columns'   => '2',
					'style'     => 'card',
					'numbering' => 'decimal',
				)
			);

			$el->add_control(
				$prefix . 'columns',
				array(
					'label'       => __( 'تعداد ستون تیترها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::CHOOSE,
					'options'     => array(
						'1' => array( 'title' => __( 'یک ستون', 'zarincoach' ), 'icon' => 'eicon-column' ),
						'2' => array( 'title' => __( 'دو ستون', 'zarincoach' ), 'icon' => 'eicon-columns' ),
						'3' => array( 'title' => __( 'سه ستون', 'zarincoach' ), 'icon' => 'eicon-gallery-grid' ),
						'4' => array( 'title' => __( 'چهار ستون', 'zarincoach' ), 'icon' => 'eicon-apps' ),
					),
					'default'     => $d['columns'],
					'toggle'      => false,
					'description' => __( 'ستون‌ها بر اساس عرض واقعی فهرست تنظیم می‌شوند: در فضای باریک و موبایل خودکار کمتر می‌شوند.', 'zarincoach' ),
					'condition'   => array_merge( $condition, $columns_condition ),
				)
			);
			$el->add_control(
				$prefix . 'style',
				array(
					'label'     => __( 'سبک', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'card'    => __( 'کارت با نوار رنگی', 'zarincoach' ),
						'soft'    => __( 'زمینه‌ی ملایم', 'zarincoach' ),
						'navy'    => __( 'سرمه‌ای', 'zarincoach' ),
						'minimal' => __( 'مینیمال (خطی)', 'zarincoach' ),
					),
					'default'   => $d['style'],
					'condition' => $condition,
				)
			);
			$el->add_control(
				$prefix . 'numbering',
				array(
					'label'     => __( 'شماره‌گذاری', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						'decimal'      => __( 'شماره‌ی دو رقمی (۰۱، ۰۲…)', 'zarincoach' ),
						'hierarchical' => __( 'سلسله‌مراتبی (۱، ۱.۱، ۱.۲…)', 'zarincoach' ),
						'none'         => __( 'بدون شماره (نقطه)', 'zarincoach' ),
					),
					'default'   => $d['numbering'],
					'condition' => $condition,
				)
			);
			$el->add_control(
				$prefix . 'collapsible',
				array(
					'label'        => __( 'قابل جمع‌شدن', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => $condition,
				)
			);
			$el->add_control(
				$prefix . 'open',
				array(
					'label'        => __( 'در ابتدا باز باشد', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array_merge( $condition, array( $prefix . 'collapsible' => 'yes' ) ),
				)
			);
			$el->add_control(
				$prefix . 'meta',
				array(
					'label'        => __( 'نمایش تعداد بخش‌ها و زمان مطالعه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => $condition,
				)
			);
		}

		/**
		 * فیلدهای تکرارشونده‌ی «نماد اعتماد» (مشترک بین ویجت نمادها و پاورقی).
		 *
		 * @return \Elementor\Repeater
		 */
		protected function trust_repeater() {
			$types = array();
			foreach ( zc_trust_types() as $key => $type ) {
				$types[ $key ] = $type['name'];
			}

			$repeater = new \Elementor\Repeater();
			$repeater->add_control(
				'type',
				array(
					'label'   => __( 'نوع نماد', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $types,
					'default' => 'enamad',
				)
			);
			$repeater->add_control(
				'mode',
				array(
					'label'       => __( 'نحوه‌ی نمایش', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => array(
						'code'    => __( 'کد رسمی (پیشنهادی)', 'zarincoach' ),
						'image'   => __( 'تصویر آپلودی', 'zarincoach' ),
						'default' => __( 'طرح پیش‌فرض قالب', 'zarincoach' ),
					),
					'default'     => 'code',
					'description' => __( 'نماد فقط با «کد رسمی» دریافتی از سامانه‌ی صادرکننده قابل استعلام و معتبر است.', 'zarincoach' ),
				)
			);
			$repeater->add_control(
				'code',
				array(
					'label'       => __( 'کد رسمی (HTML)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXTAREA,
					'rows'        => 5,
					'default'     => '',
					'placeholder' => '<a referrerpolicy="origin" target="_blank" href="https://trustseal.enamad.ir/?id=…"><img …></a>',
					'description' => __( 'کد را همان‌طور که از enamad.ir / samandehi.ir / پنل درگاه دریافت کرده‌اید جای‌گذاری کنید. خالی = کد ثبت‌شده در «تنظیمات قالب ← اطلاعات حقوقی».', 'zarincoach' ),
					'condition'   => array( 'mode' => 'code' ),
				)
			);
			$repeater->add_control(
				'image',
				array(
					'label'     => __( 'تصویر نماد', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'condition' => array( 'mode' => 'image' ),
				)
			);
			$repeater->add_control(
				'link',
				array(
					'label'       => __( 'پیوند (اختیاری)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::URL,
					'placeholder' => 'https://',
					'condition'   => array( 'mode!' => 'code' ),
				)
			);
			$repeater->add_control(
				'label',
				array(
					'label'       => __( 'عنوان زیر نماد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'خالی = عنوان پیش‌فرض نوع نماد', 'zarincoach' ),
				)
			);
			$repeater->add_control(
				'name',
				array(
					'label'       => __( 'نام/توضیح کوتاه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'default'     => '',
					'placeholder' => __( 'مثلاً: زرین‌پال', 'zarincoach' ),
				)
			);

			return $repeater;
		}

		/**
		 * آماده‌سازی نمادها از تنظیمات تکرارشونده برای رندرکننده‌ی مشترک.
		 *
		 * @param array $rows ردیف‌های تکرارشونده.
		 * @return array
		 */
		protected function trust_items_from( $rows ) {
			$items = array();
			foreach ( (array) $rows as $row ) {
				if ( ! is_array( $row ) ) {
					continue;
				}
				$items[] = array(
					'type'  => isset( $row['type'] ) ? (string) $row['type'] : 'custom',
					'mode'  => isset( $row['mode'] ) ? (string) $row['mode'] : 'code',
					'code'  => isset( $row['code'] ) ? (string) $row['code'] : '',
					'image' => isset( $row['image']['url'] ) ? (string) $row['image']['url'] : '',
					'link'  => isset( $row['link']['url'] ) ? (string) $row['link']['url'] : '',
					'label' => isset( $row['label'] ) ? (string) $row['label'] : '',
					'name'  => isset( $row['name'] ) ? (string) $row['name'] : '',
				);
			}
			return $items;
		}
	}
endif;
