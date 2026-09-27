<?php
/**
 * ویجت هدر: لوگو و برندینگ
 *
 * سه منبع: هویت سایت (پنل)، تصویر دلخواه (با نسخه‌ی حالت تیره) یا نام سایت دلخواه —
 * با کنترل ارتفاع، توضیح سایت و کیت استایل زرین‌کوچ.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Header_Brand' ) ) :

	/**
	 * ویجت «هدر: لوگو و برندینگ».
	 */
	class ZC_Widget_Header_Brand extends ZC_Widget_Base {

		/**
		 * نام ویجت.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-header-brand';
		}

		/**
		 * عنوان ویجت.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'هدر: لوگو و برندینگ', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-site-identity';
		}

		/**
		 * کلیدواژه‌ها.
		 *
		 * @return array<int,string>
		 */
		public function get_keywords() {
			return array_merge( parent::get_keywords(), array( 'header', 'هدر', 'لوگو', 'برند', 'نام سایت', 'brand', 'logo' ) );
		}

		/**
		 * ثبت کنترل‌های محتوا.
		 *
		 * @return void
		 */
		protected function register_controls() {
			$this->start_controls_section(
				'hbrand_sec',
				array(
					'label' => __( 'لوگو و برندینگ', 'zarincoach' ),
				)
			);

			$this->add_control(
				'source',
				array(
					'label'   => __( 'منبع برند', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'site'   => __( 'هویت سایت (پنل قالب / سفارشی‌سازی)', 'zarincoach' ),
						'custom' => __( 'تصویر دلخواه', 'zarincoach' ),
						'text'   => __( 'نام و توضیح دلخواه', 'zarincoach' ),
					),
					'default' => 'site',
				)
			);

			$this->add_control(
				'image',
				array(
					'label'     => __( 'لوگوی روشن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::MEDIA,
					'media_types' => array( 'image' ),
					'condition' => array( 'source' => 'custom' ),
				)
			);

			$this->add_control(
				'image_dark',
				array(
					'label'       => __( 'لوگوی حالت تیره (اختیاری)', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::MEDIA,
					'media_types' => array( 'image' ),
					'condition'   => array( 'source' => 'custom' ),
					'description' => __( 'در حالت تاریک جایگزین لوگوی روشن می‌شود.', 'zarincoach' ),
				)
			);

			$this->add_control(
				'alt',
				array(
					'label'     => __( 'متن جایگزین تصویر', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = نام سایت', 'zarincoach' ),
					'condition' => array( 'source' => 'custom' ),
				)
			);

			$this->add_control(
				'title',
				array(
					'label'       => __( 'نام برند', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = نام سایت', 'zarincoach' ),
					'condition'   => array( 'source' => 'text' ),
				)
			);

			$this->add_control(
				'tagline',
				array(
					'label'       => __( 'توضیح کوتاه', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'placeholder' => __( 'خالی = توضیح سایت', 'zarincoach' ),
					'condition'   => array( 'source' => 'text' ),
				)
			);

			$this->add_control(
				'show_tagline',
				array(
					'label'        => __( 'نمایش توضیح', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'condition'    => array( 'source' => 'text' ),
				)
			);

			$this->add_control(
				'link_home',
				array(
					'label'        => __( 'پیوند به خانه', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'return_value' => 'yes',
					'default'      => 'yes',
					'separator'    => 'before',
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
					'tagline' => array( __( 'توضیح کوتاه', 'zarincoach' ), '.zc-hdr-brand-tagline' ),
				)
			);

			$this->zc_style(
				'hbrand_style',
				__( 'برند', 'zarincoach' ),
				array(
					'img'   => array( 'size', '.zc-hdr-brand img', __( 'حداکثر ارتفاع لوگو', 'zarincoach' ), array( 'prop' => 'max-height', 'max' => 160 ) ),
					'title' => array( 'text', '.zc-hdr-brand-title', __( 'نام برند', 'zarincoach' ), array( 'margin' => false ) ),
					'tag'   => array( 'text', '.zc-hdr-brand-tagline', __( 'توضیح کوتاه', 'zarincoach' ), array( 'margin' => false ) ),
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
			$source = (string) ( isset( $s['source'] ) ? $s['source'] : 'site' );
			$link   = $this->is_on( $s, 'link_home' ) ? home_url( '/' ) : '';

			echo '<div class="zc-hdr-brand flex shrink-0 items-center">';

			if ( 'custom' === $source ) {
				$light = isset( $s['image']['url'] ) ? (string) $s['image']['url'] : '';
				$dark  = isset( $s['image_dark']['url'] ) ? (string) $s['image_dark']['url'] : '';
				if ( '' === $light && '' === $dark ) {
					echo esc_html( get_bloginfo( 'name' ) );
					echo '</div>';
					return;
				}
				$alt = trim( (string) ( isset( $s['alt'] ) ? $s['alt'] : '' ) );
				$alt = '' !== $alt ? $alt : get_bloginfo( 'name' );
				if ( '' !== $link ) {
					echo '<a class="inline-flex items-center" href="' . esc_url( $link ) . '" rel="home" aria-label="' . esc_attr( get_bloginfo( 'name' ) ) . '">';
				}
				if ( '' !== $light ) {
					echo '<img src="' . esc_url( $light ) . '" alt="' . esc_attr( $alt ) . '" class="zc-hdr-brand-img h-auto w-auto max-w-[60vw] object-contain' . ( '' !== $dark ? ' dark:hidden' : '' ) . '" decoding="async" loading="lazy">';
				}
				if ( '' !== $dark ) {
					echo '<img src="' . esc_url( $dark ) . '" alt="' . esc_attr( $alt ) . '" class="zc-hdr-brand-img h-auto w-auto max-w-[60vw] object-contain' . ( '' !== $light ? ' hidden dark:block' : '' ) . '" decoding="async" loading="lazy">';
				}
				if ( '' !== $link ) {
					echo '</a>';
				}
			} elseif ( 'text' === $source ) {
				$title = trim( (string) ( isset( $s['title'] ) ? $s['title'] : '' ) );
				$title = '' !== $title ? $title : get_bloginfo( 'name' );
				if ( '' !== $link ) {
					echo '<a class="zc-hdr-brand-title inline-flex flex-col" href="' . esc_url( $link ) . '" rel="home">';
				} else {
					echo '<span class="zc-hdr-brand-title inline-flex flex-col">';
				}
				echo '<strong class="text-xl font-extrabold text-secondary dark:text-white">' . esc_html( $title ) . '</strong>';
				if ( $this->is_on( $s, 'show_tagline' ) ) {
					$tag = trim( (string) ( isset( $s['tagline'] ) ? $s['tagline'] : '' ) );
					$tag = '' !== $tag ? $tag : get_bloginfo( 'description' );
					if ( '' !== $tag ) {
						echo '<span class="zc-hdr-brand-tagline text-[0.72rem] text-muted">' . esc_html( $tag ) . '</span>';
					}
				}
				echo '' !== $link ? '</a>' : '</span>';
			} else {
				if ( '' !== $link ) {
					global $zc_branding_wrapped;
					echo '<div class="zc-hdr-brand-site">';
					ob_start();
					zc_site_branding();
					echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- خروجی امن داخلی قالب.
					echo '</div>';
				} else {
					echo '<div class="zc-hdr-brand-site pointer-events-none">';
					ob_start();
					zc_site_branding();
					echo ob_get_clean(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					echo '</div>';
				}
			}

			echo '</div>';
		}
	}
endif;
