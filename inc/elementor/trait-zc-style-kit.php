<?php
/**
 * جعبه‌ابزار استایل ویجت‌های زرین‌کوچ (نسخه‌ی ۲.۲)
 *
 * هدف: هر جزء هر ویجت (عنوان، متن، کارت، تصویر، آیکن، دکمه، فهرست، شبکه، فیلد فرم…)
 * از زبانه‌ی «استایل» المنتور به‌طور کامل قابل تنظیم باشد و هر جزء از زبانه‌ی «محتوا»
 * قابل نمایش/پنهان شدن باشد؛ بدون اینکه خروجی پیش‌فرض (دمو) تغییر کند.
 *
 * قواعد:
 * - شناسه‌ی کنترل‌ها ثابت است و هرگز تغییر نمی‌کند: zs_{بخش}_{جزء}_{ویژگی} و zv_{جزء}.
 * - همه‌ی کنترل‌ها پیش‌فرض خالی دارند؛ یعنی تا کاربر چیزی تغییر ندهد، هیچ CSS اضافه‌ای تولید نمی‌شود.
 * - انتخابگرها نسبت به پوسته‌ی ویجت ({{WRAPPER}}) نوشته می‌شوند؛ «&» یعنی خودِ پوسته.
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! trait_exists( 'ZC_Style_Kit' ) ) :

	/**
	 * جعبه‌ابزار استایل.
	 */
	trait ZC_Style_Kit {

		/**
		 * ساخت انتخابگر المنتور از انتخابگر(های) نسبی.
		 *
		 * @param string $selector انتخابگر نسبی (با کاما برای چند انتخابگر؛ «&» = خود پوسته).
		 * @param string $suffix   پسوند هر انتخابگر (مثلاً :hover یا « img»).
		 * @return string
		 */
		protected function zc_sel( $selector, $suffix = '' ) {
			$out = array();
			foreach ( explode( ',', (string) $selector ) as $part ) {
				$part = trim( $part );
				if ( '' === $part ) {
					continue;
				}
				if ( '&' === $part[0] ) {
					$out[] = '{{WRAPPER}}' . substr( $part, 1 ) . $suffix;
				} else {
					$out[] = '{{WRAPPER}} ' . $part . $suffix;
				}
			}
			return implode( ', ', $out );
		}

		/**
		 * آرایه‌ی selectors برای یک ویژگی CSS.
		 *
		 * @param string $selector انتخابگر نسبی.
		 * @param string $css      اعلان CSS با جای‌گذار المنتور.
		 * @param string $suffix   پسوند انتخابگر.
		 * @return array<string, string>
		 */
		protected function zc_css( $selector, $css, $suffix = '' ) {
			return array( $this->zc_sel( $selector, $suffix ) => $css );
		}

		/**
		 * یک بخش کامل در زبانه‌ی استایل.
		 *
		 * @param string               $id    شناسه‌ی بخش (ثابت).
		 * @param string               $label عنوان بخش.
		 * @param array<string, array> $parts اجزا: کلید => array( نوع، انتخابگر، برچسب، گزینه‌ها ).
		 * @param array<string, mixed> $args  condition (شرط نمایش بخش).
		 * @return void
		 */
		protected function zc_style( $id, $label, array $parts, array $args = array() ) {
			$section = array(
				'label' => $label,
				'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
			);
			if ( ! empty( $args['condition'] ) ) {
				$section['condition'] = $args['condition'];
			}
			$this->start_controls_section( 'zs_' . $id, $section );

			$first = true;
			foreach ( $parts as $key => $part ) {
				$type  = isset( $part[0] ) ? (string) $part[0] : 'text';
				$sel   = isset( $part[1] ) ? (string) $part[1] : '';
				$plbl  = isset( $part[2] ) ? (string) $part[2] : '';
				$opts  = isset( $part[3] ) && is_array( $part[3] ) ? $part[3] : array();
				$pid   = 'zs_' . $id . '_' . $key;

				// اجزای تک‌کنترلی (اندازه، رنگ، انتخاب): برچسب جزء همان برچسب کنترل است؛ سرتیتر جدا لازم نیست.
				$single = in_array( $type, array( 'size', 'color', 'select' ), true );
				if ( $single && '' !== $plbl && ! isset( $opts['label'] ) ) {
					$opts['label'] = $plbl;
					$plbl          = '';
				}

				if ( '' !== $plbl && ( count( $parts ) > 1 || ! empty( $opts['heading'] ) ) ) {
					$this->add_control(
						$pid . '_hd',
						array(
							'label'     => $plbl,
							'type'      => \Elementor\Controls_Manager::HEADING,
							'separator' => $first ? 'none' : 'before',
						)
					);
				}
				$first = false;

				$method = 'zc_part_' . $type;
				if ( method_exists( $this, $method ) ) {
					$this->{$method}( $pid, $sel, $opts );
				}
			}

			$this->end_controls_section();
		}

		/* ------------------------------------------------------------------
		 * اجزای پایه
		 * ------------------------------------------------------------------ */

		/**
		 * متن: تایپوگرافی، رنگ (+ هاور)، سایه، تراز، فاصله.
		 *
		 * گزینه‌ها: hover (bool)، align (bool)، bg (bool)، margin (bool، پیش‌فرض true)، padding (bool)، width (bool).
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها.
		 * @return void
		 */
		protected function zc_part_text( $pid, $sel, array $opts = array() ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $pid . '_typo',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_control(
				$pid . '_color',
				array(
					'label'     => __( 'رنگ متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};' ),
				)
			);
			if ( ! empty( $opts['hover'] ) ) {
				$this->add_control(
					$pid . '_hcolor',
					array(
						'label'     => __( 'رنگ متن (هاور)', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};', ':hover' ),
					)
				);
			}
			if ( ! empty( $opts['bg'] ) ) {
				$this->add_control(
					$pid . '_bgc',
					array(
						'label'     => __( 'رنگ زمینه', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => $this->zc_css( $sel, 'background-color: {{VALUE}};' ),
					)
				);
			}
			$this->add_group_control(
				\Elementor\Group_Control_Text_Shadow::get_type(),
				array(
					'name'     => $pid . '_tshadow',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			if ( ! empty( $opts['align'] ) ) {
				$this->zc_align_control( $pid . '_align', $sel );
			}
			if ( ! empty( $opts['width'] ) ) {
				$this->zc_slider( $pid . '_maxw', __( 'حداکثر عرض', 'zarincoach' ), $sel, 'max-width', array( 'px', '%', 'ch', 'rem' ), 1400 );
			}
			if ( ! empty( $opts['padding'] ) ) {
				$this->zc_dims( $pid . '_padding', __( 'فاصله‌ی داخلی', 'zarincoach' ), $sel, 'padding' );
			}
			if ( ! isset( $opts['margin'] ) || $opts['margin'] ) {
				$this->zc_dims( $pid . '_margin', __( 'فاصله‌ی بیرونی', 'zarincoach' ), $sel, 'margin' );
			}
		}

		/**
		 * جعبه/کارت: زمینه، حاشیه، گردی، سایه، فاصله‌ها؛ با زبانه‌ی هاور (اختیاری).
		 *
		 * گزینه‌ها: hover (bool)، margin (bool)، padding (bool، پیش‌فرض true)، width (bool)، minh (bool)،
		 *           gradient (bool، پیش‌فرض true)، align (bool)، text (bool: رنگ متن داخل جعبه).
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها.
		 * @return void
		 */
		protected function zc_part_box( $pid, $sel, array $opts = array() ) {
			$types = ( isset( $opts['gradient'] ) && ! $opts['gradient'] ) ? array( 'classic' ) : array( 'classic', 'gradient' );
			$hover = ! empty( $opts['hover'] );

			if ( $hover ) {
				$this->start_controls_tabs( $pid . '_tabs' );
				$this->start_controls_tab( $pid . '_tab_n', array( 'label' => __( 'عادی', 'zarincoach' ) ) );
			}

			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				array(
					'name'     => $pid . '_bg',
					'types'    => $types,
					'exclude'  => array( 'image' ),
					'selector' => $this->zc_sel( $sel ),
				)
			);
			if ( ! empty( $opts['text'] ) ) {
				$this->add_control(
					$pid . '_tcolor',
					array(
						'label'     => __( 'رنگ متن', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};' ),
					)
				);
			}
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'     => $pid . '_border',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => $pid . '_shadow',
					'selector' => $this->zc_sel( $sel ),
				)
			);

			if ( $hover ) {
				$this->end_controls_tab();
				$this->start_controls_tab( $pid . '_tab_h', array( 'label' => __( 'هاور', 'zarincoach' ) ) );
				$this->add_group_control(
					\Elementor\Group_Control_Background::get_type(),
					array(
						'name'     => $pid . '_bg_h',
						'types'    => $types,
						'exclude'  => array( 'image' ),
						'selector' => $this->zc_sel( $sel, ':hover' ),
					)
				);
				if ( ! empty( $opts['text'] ) ) {
					$this->add_control(
						$pid . '_tcolor_h',
						array(
							'label'     => __( 'رنگ متن', 'zarincoach' ),
							'type'      => \Elementor\Controls_Manager::COLOR,
							'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};', ':hover' ),
						)
					);
				}
				$this->add_control(
					$pid . '_bcolor_h',
					array(
						'label'     => __( 'رنگ حاشیه', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => $this->zc_css( $sel, 'border-color: {{VALUE}};', ':hover' ),
					)
				);
				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => $pid . '_shadow_h',
						'selector' => $this->zc_sel( $sel, ':hover' ),
					)
				);
				$this->add_control(
					$pid . '_lift',
					array(
						'label'      => __( 'جابه‌جایی عمودی', 'zarincoach' ),
						'type'       => \Elementor\Controls_Manager::SLIDER,
						'size_units' => array( 'px' ),
						'range'      => array(
							'px' => array(
								'min' => -20,
								'max' => 20,
							),
						),
						'selectors'  => $this->zc_css( $sel, 'transform: translateY({{SIZE}}{{UNIT}});', ':hover' ),
					)
				);
				$this->add_control(
					$pid . '_dur',
					array(
						'label'     => __( 'مدت انیمیشن (ثانیه)', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::SLIDER,
						'range'     => array(
							'px' => array(
								'min'  => 0,
								'max'  => 2,
								'step' => 0.05,
							),
						),
						'selectors' => $this->zc_css( $sel, 'transition-duration: {{SIZE}}s;' ),
					)
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
			}

			$this->zc_dims( $pid . '_radius', __( 'گردی گوشه‌ها', 'zarincoach' ), $sel, 'border-radius', array( 'px', '%', 'em' ), $hover ? 'before' : 'none' );
			if ( ! isset( $opts['padding'] ) || $opts['padding'] ) {
				$this->zc_dims( $pid . '_padding', __( 'فاصله‌ی داخلی', 'zarincoach' ), $sel, 'padding' );
			}
			if ( ! empty( $opts['margin'] ) ) {
				$this->zc_dims( $pid . '_margin', __( 'فاصله‌ی بیرونی', 'zarincoach' ), $sel, 'margin' );
			}
			if ( ! empty( $opts['width'] ) ) {
				$this->zc_slider( $pid . '_maxw', __( 'حداکثر عرض', 'zarincoach' ), $sel, 'max-width', array( 'px', '%', 'vw' ), 1600 );
			}
			if ( ! empty( $opts['minh'] ) ) {
				$this->zc_slider( $pid . '_minh', __( 'حداقل ارتفاع', 'zarincoach' ), $sel, 'min-height', array( 'px', 'vh', 'rem' ), 1200 );
			}
			if ( ! empty( $opts['align'] ) ) {
				$this->zc_align_control( $pid . '_align', $sel );
			}
		}

		/**
		 * دکمه: تایپوگرافی، رنگ‌ها و زمینه در حالت عادی/هاور، حاشیه، گردی، سایه، فاصله، اندازه‌ی آیکن.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها: width (bool).
		 * @return void
		 */
		protected function zc_part_button( $pid, $sel, array $opts = array() ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $pid . '_typo',
					'selector' => $this->zc_sel( $sel ),
				)
			);

			$this->start_controls_tabs( $pid . '_tabs' );
			foreach ( array(
				'n' => array( __( 'عادی', 'zarincoach' ), '' ),
				'h' => array( __( 'هاور', 'zarincoach' ), ':hover' ),
			) as $state => $info ) {
				$this->start_controls_tab( $pid . '_tab_' . $state, array( 'label' => $info[0] ) );
				$sfx = 'n' === $state ? '' : '_h';
				$this->add_control(
					$pid . '_color' . $sfx,
					array(
						'label'     => __( 'رنگ متن و آیکن', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$this->zc_sel( $sel, $info[1] ) => 'color: {{VALUE}};',
						),
					)
				);
				$this->add_group_control(
					\Elementor\Group_Control_Background::get_type(),
					array(
						'name'     => $pid . '_bg' . $sfx,
						'types'    => array( 'classic', 'gradient' ),
						'exclude'  => array( 'image' ),
						'selector' => $this->zc_sel( $sel, $info[1] ),
					)
				);
				$this->add_control(
					$pid . '_bcolor' . $sfx,
					array(
						'label'     => __( 'رنگ حاشیه', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$this->zc_sel( $sel, $info[1] ) => 'border-color: {{VALUE}};',
						),
					)
				);
				$this->add_group_control(
					\Elementor\Group_Control_Box_Shadow::get_type(),
					array(
						'name'     => $pid . '_shadow' . $sfx,
						'selector' => $this->zc_sel( $sel, $info[1] ),
					)
				);
				if ( 'h' === $state ) {
					$this->add_control(
						$pid . '_lift',
						array(
							'label'      => __( 'جابه‌جایی عمودی', 'zarincoach' ),
							'type'       => \Elementor\Controls_Manager::SLIDER,
							'size_units' => array( 'px' ),
							'range'      => array(
								'px' => array(
									'min' => -10,
									'max' => 10,
								),
							),
							'selectors'  => $this->zc_css( $sel, 'transform: translateY({{SIZE}}{{UNIT}});', ':hover' ),
						)
					);
				}
				$this->end_controls_tab();
			}
			$this->end_controls_tabs();

			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'      => $pid . '_border',
					'selector'  => $this->zc_sel( $sel ),
					'separator' => 'before',
					'exclude'   => array( 'color' ),
				)
			);
			$this->zc_dims( $pid . '_radius', __( 'گردی گوشه‌ها', 'zarincoach' ), $sel, 'border-radius', array( 'px', '%', 'em' ) );
			$this->zc_dims( $pid . '_padding', __( 'فاصله‌ی داخلی', 'zarincoach' ), $sel, 'padding' );
			$this->zc_slider( $pid . '_gap', __( 'فاصله‌ی متن تا آیکن', 'zarincoach' ), $sel, 'gap', array( 'px', 'em' ), 40 );
			$this->zc_slider( $pid . '_isize', __( 'اندازه‌ی آیکن', 'zarincoach' ), $sel, 'width', array( 'px', 'em' ), 60, ' svg, ' . $sel . ' i', '--zc-bi: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};' );
			if ( ! empty( $opts['width'] ) ) {
				$this->zc_slider( $pid . '_minw', __( 'حداقل عرض', 'zarincoach' ), $sel, 'min-width', array( 'px', '%' ), 600 );
			}
		}

		/**
		 * آیکن: اندازه، رنگ (+ هاور)، زمینه، اندازه‌ی قاب، گردی، حاشیه.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر قاب آیکن (svg/i داخل آن اندازه می‌گیرد).
		 * @param array  $opts گزینه‌ها: hover (انتخابگر والد برای هاور)، box (bool، پیش‌فرض true).
		 * @return void
		 */
		protected function zc_part_icon( $pid, $sel, array $opts = array() ) {
			$inner = array();
			foreach ( explode( ',', $sel ) as $s ) {
				$s       = trim( $s );
				$inner[] = $s . ' svg';
				$inner[] = $s . ' i';
				$inner[] = $s . ' img';
			}
			$inner = implode( ', ', $inner );

			$this->add_responsive_control(
				$pid . '_size',
				array(
					'label'      => __( 'اندازه‌ی آیکن', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => array( 'px', 'em', 'rem' ),
					'range'      => array(
						'px' => array(
							'min' => 6,
							'max' => 160,
						),
					),
					'selectors'  => array(
						$this->zc_sel( $inner ) => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; font-size: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->add_control(
				$pid . '_color',
				array(
					'label'     => __( 'رنگ آیکن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$this->zc_sel( $sel ) => 'color: {{VALUE}};',
						$this->zc_sel( $inner ) => 'color: {{VALUE}};',
					),
				)
			);
			if ( ! empty( $opts['hover'] ) ) {
				$this->add_control(
					$pid . '_hcolor',
					array(
						'label'     => __( 'رنگ آیکن (هاور)', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$this->zc_sel( (string) $opts['hover'] . ':hover ' . $sel ) => 'color: {{VALUE}};',
						),
					)
				);
			}
			if ( ! isset( $opts['box'] ) || $opts['box'] ) {
				$this->add_group_control(
					\Elementor\Group_Control_Background::get_type(),
					array(
						'name'     => $pid . '_bg',
						'types'    => array( 'classic', 'gradient' ),
						'exclude'  => array( 'image' ),
						'selector' => $this->zc_sel( $sel ),
					)
				);
				$this->zc_slider( $pid . '_box', __( 'اندازه‌ی قاب', 'zarincoach' ), $sel, 'width', array( 'px', 'em' ), 200, '', 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};' );
				$this->add_group_control(
					\Elementor\Group_Control_Border::get_type(),
					array(
						'name'     => $pid . '_border',
						'selector' => $this->zc_sel( $sel ),
					)
				);
				$this->zc_dims( $pid . '_radius', __( 'گردی قاب', 'zarincoach' ), $sel, 'border-radius', array( 'px', '%' ) );
			}
		}

		/**
		 * تصویر: عرض/ارتفاع، نسبت ابعاد، برش، موقعیت، گردی، حاشیه، سایه، فیلترها، هاور.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر قاب تصویر (img داخل آن هم تنظیم می‌شود).
		 * @param array  $opts گزینه‌ها: ratio (bool، پیش‌فرض true)، size (bool، پیش‌فرض true)، hover (انتخابگر والد).
		 * @return void
		 */
		protected function zc_part_image( $pid, $sel, array $opts = array() ) {
			$img = array();
			foreach ( explode( ',', $sel ) as $s ) {
				$img[] = trim( $s ) . ' img';
			}
			$img = implode( ', ', $img );

			if ( ! isset( $opts['size'] ) || $opts['size'] ) {
				$this->zc_slider( $pid . '_w', __( 'عرض', 'zarincoach' ), $sel, 'width', array( 'px', '%', 'vw' ), 1400 );
				$this->zc_slider( $pid . '_maxw', __( 'حداکثر عرض', 'zarincoach' ), $sel, 'max-width', array( 'px', '%', 'vw' ), 1400 );
				$this->zc_slider( $pid . '_h', __( 'ارتفاع', 'zarincoach' ), $sel, 'height', array( 'px', 'vh', 'rem' ), 1200 );
			}
			if ( ! isset( $opts['ratio'] ) || $opts['ratio'] ) {
				$this->add_responsive_control(
					$pid . '_ratio',
					array(
						'label'     => __( 'نسبت ابعاد', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'options'   => array(
							''     => __( 'پیش‌فرض قالب', 'zarincoach' ),
							'1/1'  => '۱:۱',
							'4/5'  => '۴:۵',
							'3/4'  => '۳:۴',
							'2/3'  => '۲:۳',
							'4/3'  => '۴:۳',
							'3/2'  => '۳:۲',
							'16/9' => '۱۶:۹',
							'21/9' => '۲۱:۹',
							'auto' => __( 'ابعاد اصلی تصویر', 'zarincoach' ),
						),
						'selectors' => array(
							$this->zc_sel( $sel ) => 'aspect-ratio: {{VALUE}}; height: auto;',
						),
					)
				);
			}
			$this->add_control(
				$pid . '_fit',
				array(
					'label'     => __( 'نحوه‌ی قرارگیری', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''        => __( 'پیش‌فرض', 'zarincoach' ),
						'cover'   => __( 'پوشش کامل (برش)', 'zarincoach' ),
						'contain' => __( 'نمایش کامل (بدون برش)', 'zarincoach' ),
						'fill'    => __( 'کشیده', 'zarincoach' ),
					),
					'selectors' => array(
						$this->zc_sel( $img ) => 'object-fit: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				$pid . '_pos',
				array(
					'label'     => __( 'موقعیت تصویر', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SELECT,
					'options'   => array(
						''              => __( 'پیش‌فرض', 'zarincoach' ),
						'center center' => __( 'وسط', 'zarincoach' ),
						'center top'    => __( 'بالا', 'zarincoach' ),
						'center bottom' => __( 'پایین', 'zarincoach' ),
						'right center'  => __( 'راست', 'zarincoach' ),
						'left center'   => __( 'چپ', 'zarincoach' ),
					),
					'selectors' => array(
						$this->zc_sel( $img ) => 'object-position: {{VALUE}};',
					),
				)
			);
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'     => $pid . '_border',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_responsive_control(
				$pid . '_radius',
				array(
					'label'      => __( 'گردی گوشه‌ها', 'zarincoach' ),
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => array( 'px', '%', 'em' ),
					'selectors'  => array(
						$this->zc_sel( $sel ) => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden; clip-path: none;',
						$this->zc_sel( $img ) => 'border-radius: inherit;',
					),
				)
			);
			$this->add_group_control(
				\Elementor\Group_Control_Box_Shadow::get_type(),
				array(
					'name'     => $pid . '_shadow',
					'selector' => $this->zc_sel( $sel ),
				)
			);

			$this->start_controls_tabs( $pid . '_tabs' );
			$this->start_controls_tab( $pid . '_tab_n', array( 'label' => __( 'عادی', 'zarincoach' ) ) );
			$this->add_group_control(
				\Elementor\Group_Control_Css_Filter::get_type(),
				array(
					'name'     => $pid . '_filter',
					'selector' => $this->zc_sel( $img ),
				)
			);
			$this->add_control(
				$pid . '_opacity',
				array(
					'label'     => __( 'شفافیت', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0.1,
							'max'  => 1,
							'step' => 0.05,
						),
					),
					'selectors' => array(
						$this->zc_sel( $img ) => 'opacity: {{SIZE}};',
					),
				)
			);
			$this->end_controls_tab();
			$this->start_controls_tab( $pid . '_tab_h', array( 'label' => __( 'هاور', 'zarincoach' ) ) );
			$hover_parent = ! empty( $opts['hover'] ) ? (string) $opts['hover'] : $sel;
			$img_h        = array();
			foreach ( explode( ',', $sel ) as $s ) {
				$img_h[] = trim( $hover_parent ) . ':hover ' . ( trim( $hover_parent ) === trim( $s ) ? '' : trim( $s ) . ' ' ) . 'img';
			}
			$img_h = implode( ', ', $img_h );
			$this->add_group_control(
				\Elementor\Group_Control_Css_Filter::get_type(),
				array(
					'name'     => $pid . '_filter_h',
					'selector' => $this->zc_sel( $img_h ),
				)
			);
			$this->add_control(
				$pid . '_scale_h',
				array(
					'label'     => __( 'بزرگ‌نمایی', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::SLIDER,
					'range'     => array(
						'px' => array(
							'min'  => 0.8,
							'max'  => 1.4,
							'step' => 0.01,
						),
					),
					'selectors' => array(
						$this->zc_sel( $img_h ) => 'transform: scale({{SIZE}});',
						$this->zc_sel( $img ) => 'transition: transform .5s ease, filter .3s ease, opacity .3s ease;',
					),
				)
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();
		}

		/**
		 * فهرست: فاصله‌ی آیتم‌ها، تایپوگرافی و رنگ، رنگ و اندازه‌ی نشانگر.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر آیتم‌ها (مثلاً «.zc-checklist li»).
		 * @param array  $opts گزینه‌ها: marker (انتخابگر نشانگر، مثلاً «.zc-checklist li::before»)، list (انتخابگر فهرست برای gap).
		 * @return void
		 */
		protected function zc_part_list( $pid, $sel, array $opts = array() ) {
			if ( ! empty( $opts['list'] ) ) {
				$this->zc_slider( $pid . '_gap', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), (string) $opts['list'], 'gap', array( 'px', 'em', 'rem' ), 80, '', 'row-gap: {{SIZE}}{{UNIT}}; gap: {{SIZE}}{{UNIT}};' );
			}
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $pid . '_typo',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_control(
				$pid . '_color',
				array(
					'label'     => __( 'رنگ متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};' ),
				)
			);
			if ( ! empty( $opts['marker'] ) ) {
				$this->add_control(
					$pid . '_mcolor',
					array(
						'label'     => __( 'رنگ نشانگر', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::COLOR,
						'selectors' => array(
							$this->zc_sel( (string) $opts['marker'] ) => 'color: {{VALUE}}; background-color: {{VALUE}}; border-color: {{VALUE}};',
						),
					)
				);
				$this->add_control(
					$pid . '_mbg',
					array(
						'label'       => __( 'رنگ تیک/علامت داخل نشانگر', 'zarincoach' ),
						'type'        => \Elementor\Controls_Manager::COLOR,
						'description' => __( 'برای نشانگرهای دایره‌ای که علامت داخلشان رنگ جدا دارد.', 'zarincoach' ),
						'selectors'   => array(
							$this->zc_sel( (string) $opts['marker'] ) => '--zc-mark-fg: {{VALUE}};',
						),
					)
				);
			}
			$this->zc_dims( $pid . '_padding', __( 'فاصله‌ی داخلی آیتم', 'zarincoach' ), $sel, 'padding' );
		}

		/**
		 * شبکه: تعداد ستون واکنش‌گرا و فاصله‌ی ستون/ردیف.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر شبکه.
		 * @param array  $opts گزینه‌ها: cols (bool، پیش‌فرض true)، max (حداکثر ستون، پیش‌فرض ۶).
		 * @return void
		 */
		protected function zc_part_grid( $pid, $sel, array $opts = array() ) {
			if ( ! isset( $opts['cols'] ) || $opts['cols'] ) {
				$max     = isset( $opts['max'] ) ? (int) $opts['max'] : 6;
				$choices = array( '' => __( 'پیش‌فرض ویجت', 'zarincoach' ) );
				for ( $i = 1; $i <= $max; $i++ ) {
					$choices[ (string) $i ] = zc_digits_to_persian( (string) $i );
				}
				$this->add_responsive_control(
					$pid . '_cols',
					array(
						'label'     => __( 'تعداد ستون', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'options'   => $choices,
						'selectors' => array(
							$this->zc_sel( $sel ) => 'display: grid; grid-template-columns: repeat({{VALUE}}, minmax(0, 1fr));',
						),
					)
				);
			}
			$this->zc_slider( $pid . '_cgap', __( 'فاصله‌ی ستون‌ها', 'zarincoach' ), $sel, 'column-gap', array( 'px', 'em', 'rem' ), 120 );
			$this->zc_slider( $pid . '_rgap', __( 'فاصله‌ی ردیف‌ها', 'zarincoach' ), $sel, 'row-gap', array( 'px', 'em', 'rem' ), 120 );
			if ( ! empty( $opts['valign'] ) ) {
				$this->add_responsive_control(
					$pid . '_valign',
					array(
						'label'     => __( 'تراز عمودی', 'zarincoach' ),
						'type'      => \Elementor\Controls_Manager::SELECT,
						'options'   => array(
							''        => __( 'پیش‌فرض', 'zarincoach' ),
							'start'   => __( 'بالا', 'zarincoach' ),
							'center'  => __( 'وسط', 'zarincoach' ),
							'end'     => __( 'پایین', 'zarincoach' ),
							'stretch' => __( 'کشیده', 'zarincoach' ),
						),
						'selectors' => $this->zc_css( $sel, 'align-items: {{VALUE}};' ),
					)
				);
			}
		}

		/**
		 * فیلد فرم: تایپوگرافی، رنگ متن و placeholder، زمینه، حاشیه (+ فوکوس)، گردی، فاصله، ارتفاع.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر فیلدها.
		 * @param array  $opts گزینه‌ها.
		 * @return void
		 */
		protected function zc_part_input( $pid, $sel, array $opts = array() ) {
			$this->add_group_control(
				\Elementor\Group_Control_Typography::get_type(),
				array(
					'name'     => $pid . '_typo',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_control(
				$pid . '_color',
				array(
					'label'     => __( 'رنگ متن', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};' ),
				)
			);
			$this->add_control(
				$pid . '_ph',
				array(
					'label'     => __( 'رنگ متن راهنما (placeholder)', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, 'color: {{VALUE}};', '::placeholder' ),
				)
			);
			$this->add_control(
				$pid . '_bgc',
				array(
					'label'     => __( 'رنگ زمینه', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, 'background-color: {{VALUE}};' ),
				)
			);
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'     => $pid . '_border',
					'selector' => $this->zc_sel( $sel ),
				)
			);
			$this->add_control(
				$pid . '_focus',
				array(
					'label'     => __( 'رنگ حاشیه در حالت فوکوس', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => array(
						$this->zc_sel( $sel, ':focus' ) => 'border-color: {{VALUE}}; box-shadow: 0 0 0 3px color-mix(in srgb, {{VALUE}} 18%, transparent);',
					),
				)
			);
			$this->zc_dims( $pid . '_radius', __( 'گردی گوشه‌ها', 'zarincoach' ), $sel, 'border-radius', array( 'px', '%', 'em' ) );
			$this->zc_dims( $pid . '_padding', __( 'فاصله‌ی داخلی', 'zarincoach' ), $sel, 'padding' );
			$this->zc_slider( $pid . '_h', __( 'ارتفاع فیلدهای یک‌خطی', 'zarincoach' ), $sel, 'min-height', array( 'px', 'rem' ), 90 );
		}

		/**
		 * فقط یک رنگ (برای خط، نقطه، نوار پیشرفت و…).
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها: prop (ویژگی CSS؛ پیش‌فرض color)، label.
		 * @return void
		 */
		protected function zc_part_color( $pid, $sel, array $opts = array() ) {
			$prop = isset( $opts['prop'] ) ? (string) $opts['prop'] : 'color';
			$this->add_control(
				$pid . '_v',
				array(
					'label'     => isset( $opts['label'] ) ? (string) $opts['label'] : __( 'رنگ', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::COLOR,
					'selectors' => $this->zc_css( $sel, $prop . ': {{VALUE}};' ),
				)
			);
		}

		/**
		 * یک اندازه (اسلایدر واکنش‌گرا) برای هر ویژگی CSS.
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها: prop، label، units، max.
		 * @return void
		 */
		protected function zc_part_size( $pid, $sel, array $opts = array() ) {
			$this->zc_slider(
				$pid . '_v',
				isset( $opts['label'] ) ? (string) $opts['label'] : __( 'اندازه', 'zarincoach' ),
				$sel,
				isset( $opts['prop'] ) ? (string) $opts['prop'] : 'width',
				isset( $opts['units'] ) ? (array) $opts['units'] : array( 'px', 'em', 'rem', '%' ),
				isset( $opts['max'] ) ? (int) $opts['max'] : 400,
				'',
				isset( $opts['css'] ) ? (string) $opts['css'] : ''
			);
		}

		/* ------------------------------------------------------------------
		 * کنترل‌های کمکی
		 * ------------------------------------------------------------------ */

		/**
		 * اسلایدر واکنش‌گرا.
		 *
		 * @param string $id     شناسه.
		 * @param string $label  برچسب.
		 * @param string $sel    انتخابگر.
		 * @param string $prop   ویژگی CSS.
		 * @param array  $units  واحدها.
		 * @param int    $max    حداکثر (px).
		 * @param string $suffix پسوند انتخابگر.
		 * @param string $css    اعلان سفارشی (به جای «prop: size»).
		 * @return void
		 */
		protected function zc_slider( $id, $label, $sel, $prop, array $units = array( 'px' ), $max = 200, $suffix = '', $css = '' ) {
			$range = array(
				'px'  => array(
					'min'  => 0,
					'max'  => $max,
					'step' => $max <= 5 ? 0.05 : 1,
				),
				's'   => array(
					'min'  => 0,
					'max'  => $max,
					'step' => 0.5,
				),
				'deg' => array(
					'min' => -360,
					'max' => 360,
				),
				'%'   => array(
					'min' => 0,
					'max' => 100,
				),
				'em'  => array(
					'min'  => 0,
					'max'  => 20,
					'step' => 0.1,
				),
				'rem' => array(
					'min'  => 0,
					'max'  => 20,
					'step' => 0.1,
				),
				'vw'  => array(
					'min' => 0,
					'max' => 100,
				),
				'vh'  => array(
					'min' => 0,
					'max' => 100,
				),
				'ch'  => array(
					'min' => 10,
					'max' => 140,
				),
			);
			$this->add_responsive_control(
				$id,
				array(
					'label'      => $label,
					'type'       => \Elementor\Controls_Manager::SLIDER,
					'size_units' => $units,
					'range'      => array_intersect_key( $range, array_flip( $units ) ),
					'selectors'  => array(
						$this->zc_sel( $sel . $suffix ) => '' !== $css ? $css : $prop . ': {{SIZE}}{{UNIT}};',
					),
				)
			);
		}

		/**
		 * کنترل ابعاد واکنش‌گرا (padding / margin / border-radius).
		 *
		 * @param string $id        شناسه.
		 * @param string $label     برچسب.
		 * @param string $sel       انتخابگر.
		 * @param string $prop      ویژگی CSS.
		 * @param array  $units     واحدها.
		 * @param string $separator جداکننده.
		 * @return void
		 */
		protected function zc_dims( $id, $label, $sel, $prop, array $units = array( 'px', 'em', 'rem', '%' ), $separator = 'none' ) {
			$this->add_responsive_control(
				$id,
				array(
					'label'      => $label,
					'type'       => \Elementor\Controls_Manager::DIMENSIONS,
					'size_units' => $units,
					'separator'  => $separator,
					'selectors'  => $this->zc_css( $sel, $prop . ': {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};' ),
				)
			);
		}

		/**
		 * تراز متن واکنش‌گرا.
		 *
		 * @param string $id  شناسه.
		 * @param string $sel انتخابگر.
		 * @return void
		 */
		protected function zc_align_control( $id, $sel ) {
			$this->add_responsive_control(
				$id,
				array(
					'label'     => __( 'تراز', 'zarincoach' ),
					'type'      => \Elementor\Controls_Manager::CHOOSE,
					'options'   => array(
						'right'   => array(
							'title' => __( 'راست', 'zarincoach' ),
							'icon'  => 'eicon-text-align-right',
						),
						'center'  => array(
							'title' => __( 'وسط', 'zarincoach' ),
							'icon'  => 'eicon-text-align-center',
						),
						'left'    => array(
							'title' => __( 'چپ', 'zarincoach' ),
							'icon'  => 'eicon-text-align-left',
						),
						'justify' => array(
							'title' => __( 'تراز دوطرفه', 'zarincoach' ),
							'icon'  => 'eicon-text-align-justify',
						),
					),
					'selectors' => $this->zc_css( $sel, 'text-align: {{VALUE}}; text-align-last: auto;' ),
				)
			);
		}

		/**
		 * بخش «نمایش اجزا» در زبانه‌ی محتوا: هر جزء با یک کلید روشن/خاموش.
		 *
		 * پیاده‌سازی با selectors_dictionary: خاموش ('') => display:none؛ روشن => بدون CSS.
		 * بنابراین هم در ویرایشگر (زنده) و هم در سایت بدون هیچ کد رندر اضافه‌ای کار می‌کند.
		 *
		 * @param array<string, array{0:string,1:string,2?:string}> $items کلید => array( برچسب، انتخابگر، توضیح ).
		 * @param string                                            $label عنوان بخش.
		 * @return void
		 */
		protected function zc_toggles( array $items, $label = '' ) {
			if ( ! empty( $this->zc_toggles_done ) ) {
				return;
			}
			$this->zc_toggles_done = true;

			// اجزای سربرگ مشترک (برای همه‌ی ویجت‌هایی که سربرگ بخش دارند).
			if ( null !== $this->get_controls( 'eyebrow' ) ) {
				$items = array_merge(
					array(
						'head'          => array( __( 'کل سربرگ بخش', 'zarincoach' ), '.zc-section-head' ),
						'head_eyebrow'  => array( __( 'برچسب کوتاه سربرگ', 'zarincoach' ), '.zc-section-head .zc-eyebrow' ),
						'head_title'    => array( __( 'عنوان سربرگ', 'zarincoach' ), '.zc-section-head :is(h1,h2,h3,h4,h5,h6,.zc-title-lg)' ),
						'head_subtitle' => array( __( 'زیرعنوان سربرگ', 'zarincoach' ), '.zc-section-head .zc-lead' ),
					),
					$items
				);
			}
			if ( empty( $items ) ) {
				return;
			}
			$this->start_controls_section(
				'zv_section',
				array(
					'label' => '' !== $label ? $label : __( 'نمایش اجزا', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_CONTENT,
				)
			);
			foreach ( $items as $key => $item ) {
				$args = array(
					'label'                => $item[0],
					'type'                 => \Elementor\Controls_Manager::SWITCHER,
					'label_on'             => __( 'نمایش', 'zarincoach' ),
					'label_off'            => __( 'پنهان', 'zarincoach' ),
					'return_value'         => 'yes',
					'default'              => 'yes',
					'selectors_dictionary' => array(
						''    => 'none !important',
						'yes' => '',
					),
					'selectors'            => $this->zc_css( $item[1], 'display: {{VALUE}};' ),
				);
				if ( ! empty( $item[2] ) ) {
					$args['description'] = $item[2];
				}
				$this->add_control( 'zv_' . $key, $args );
			}
			$this->end_controls_section();
		}

		/* ------------------------------------------------------------------
		 * بخش‌های خودکار (برای همه‌ی ویجت‌ها)
		 * ------------------------------------------------------------------ */

		/**
		 * انتخابگر ریشه‌ی خروجی ویجت (با یا بدون elementor-widget-container).
		 *
		 * @return string
		 */
		protected function zc_root_sel() {
			return '& > :not(style):not(script):not(link):not(.elementor-widget-container), & > .elementor-widget-container > :not(style):not(script):not(link)';
		}

		/**
		 * جزء «انتخاب» برای zc_style: گزینه‌ها options (مقدار => برچسب)، css (با {{VALUE}})، label، responsive (bool).
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر.
		 * @param array  $opts گزینه‌ها.
		 * @return void
		 */
		protected function zc_part_select( $pid, $sel, array $opts = array() ) {
			$args = array(
				'label'     => isset( $opts['label'] ) ? $opts['label'] : '',
				'type'      => \Elementor\Controls_Manager::SELECT,
				'options'   => array( '' => __( 'پیش‌فرض', 'zarincoach' ) ) + ( isset( $opts['options'] ) ? (array) $opts['options'] : array() ),
				'selectors' => $this->zc_css( $sel, isset( $opts['css'] ) ? $opts['css'] : '{{VALUE}}' ),
			);
			if ( ! empty( $opts['responsive'] ) ) {
				$this->add_responsive_control( $pid, $args );
			} else {
				$this->add_control( $pid, $args );
			}
		}

		/**
		 * بخش‌های استایل فهرست مطالب (zc_toc_render).
		 *
		 * @param string $id   پیشوند شناسه‌ی بخش‌ها (ثابت).
		 * @param array  $args condition.
		 * @return void
		 */
		protected function zc_toc_styles( $id, array $args = array() ) {
			$this->zc_style(
				$id . '_toc_box',
				__( 'فهرست مطالب: قاب و سربرگ', 'zarincoach' ),
				array(
					'box'      => array( 'box', 'nav.zc-toc', __( 'قاب فهرست', 'zarincoach' ), array( 'width' => true ) ),
					'head'     => array( 'box', '.zc-toc-head', __( 'سربرگ', 'zarincoach' ), array( 'gradient' => false ) ),
					'icon'     => array( 'icon', '.zc-toc-icon', __( 'آیکن سربرگ', 'zarincoach' ) ),
					'title'    => array( 'text', '.zc-toc-title', __( 'عنوان', 'zarincoach' ), array( 'margin' => false ) ),
					'meta'     => array( 'text', '.zc-toc-meta', __( 'اطلاعات زمان مطالعه', 'zarincoach' ), array( 'margin' => false ) ),
					'toggle'   => array( 'text', '.zc-toc-toggle', __( 'دکمه باز/بستن', 'zarincoach' ), array( 'hover' => true, 'bg' => true, 'margin' => false ) ),
					'progress' => array( 'color', '.zc-toc-progress', __( 'رنگ زمینه‌ی نوار پیشرفت', 'zarincoach' ), array( 'prop' => 'background-color' ) ),
					'bar'      => array( 'color', '.zc-toc-progress span', __( 'رنگ نوار پیشرفت', 'zarincoach' ), array( 'prop' => 'background' ) ),
				),
				$args
			);
			$this->zc_style(
				$id . '_toc_links',
				__( 'فهرست مطالب: پیوندها', 'zarincoach' ),
				array(
					'body'   => array( 'box', '.zc-toc-body', __( 'بدنه‌ی فهرست', 'zarincoach' ), array( 'gradient' => false ) ),
					'link'   => array( 'text', '.zc-toc-link:not(.is-sub)', __( 'تیتر اصلی', 'zarincoach' ), array( 'hover' => true, 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'sub'    => array( 'text', '.zc-toc-link.is-sub', __( 'زیرتیتر', 'zarincoach' ), array( 'hover' => true, 'bg' => true, 'padding' => true, 'margin' => false ) ),
					'active' => array( 'text', '.zc-toc-link.is-active', __( 'پیوند فعال', 'zarincoach' ), array( 'bg' => true, 'margin' => false ) ),
					'num'    => array( 'icon', '.zc-toc-num', __( 'شماره‌ها', 'zarincoach' ) ),
					'numact' => array( 'color', '.zc-toc-link.is-active .zc-toc-num', __( 'پس‌زمینه‌ی شماره‌ی فعال', 'zarincoach' ), array( 'prop' => 'background' ) ),
					'dot'    => array( 'color', '.zc-toc-dot', __( 'رنگ نقطه‌ی زیرتیترها', 'zarincoach' ), array( 'prop' => 'background' ) ),
				),
				$args
			);
		}

		/**
		 * بخش‌های استایل فرم تماس داخلی قالب ([zc_contact]).
		 *
		 * @param string $id    پیشوند شناسه‌ی بخش‌ها (ثابت).
		 * @param string $scope انتخابگر ظرف فرم.
		 * @param array  $args  condition.
		 * @return void
		 */
		protected function zc_form_styles( $id, $scope, array $args = array() ) {
			$f = trim( $scope ) . ' ';
			$this->zc_style(
				$id . '_form',
				__( 'فرم: قاب و عنوان', 'zarincoach' ),
				array(
					'box'   => array( 'box', trim( $scope ), __( 'قاب فرم', 'zarincoach' ), array( 'width' => true ) ),
					'gap'   => array( 'size', $f . '.zc-contact-form', __( 'فاصله‌ی فیلدها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 60 ) ),
					'title' => array( 'text', $f . '.zc-form-title', __( 'عنوان فرم', 'zarincoach' ) ),
					'desc'  => array( 'text', $f . '.zc-form-desc', __( 'توضیح فرم', 'zarincoach' ) ),
				),
				$args
			);
			$this->zc_style(
				$id . '_fields',
				__( 'فرم: برچسب‌ها و فیلدها', 'zarincoach' ),
				array(
					'label' => array( 'text', $f . '.zc-label', __( 'برچسب فیلد', 'zarincoach' ) ),
					'input' => array( 'input', $f . '.zc-input, ' . $f . '.zc-textarea, ' . $f . 'select', __( 'فیلدها', 'zarincoach' ) ),
					'note'  => array( 'text', $f . '.zc-form-note', __( 'متن زیر فرم', 'zarincoach' ) ),
				),
				$args
			);
			$this->zc_style(
				$id . '_submit',
				__( 'فرم: دکمه‌ی ارسال', 'zarincoach' ),
				array(
					'btn' => array( 'button', $f . '.zc-contact-form .zc-btn', '', array( 'width' => true ) ),
				),
				$args
			);
		}

		/**
		 * جزء «نسبت ستون‌ها» برای zc_style: گزینه‌ها children (انتخابگر ستون‌ها)، gap (bool، پیش‌فرض true)، valign (bool).
		 *
		 * @param string $pid  پیشوند شناسه.
		 * @param string $sel  انتخابگر شبکه.
		 * @param array  $opts گزینه‌ها.
		 * @return void
		 */
		protected function zc_part_split( $pid, $sel, array $opts = array() ) {
			$this->zc_split_control( $pid . '_cols', $sel, isset( $opts['children'] ) ? $opts['children'] : $sel . ' > *' );
			$this->zc_part_grid( $pid, $sel, array( 'cols' => false, 'valign' => ! empty( $opts['valign'] ) ) );
		}

		/**
		 * نسبت ستون‌های چیدمان دوستونه (تصویر/متن) — واکنش‌گرا.
		 *
		 * @param string $id       شناسه‌ی کنترل.
		 * @param string $grid     انتخابگر شبکه.
		 * @param string $children انتخابگر ستون‌ها (برای خنثی کردن col-span قالب).
		 * @param string $label    برچسب.
		 * @return void
		 */
		protected function zc_split_control( $id, $grid, $children, $label = '' ) {
			$this->add_responsive_control(
				$id,
				array(
					'label'       => '' !== $label ? $label : __( 'نسبت ستون‌ها', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SELECT,
					'options'     => array(
						''              => __( 'پیش‌فرض قالب', 'zarincoach' ),
						'1fr'           => __( 'یک ستون (زیر هم)', 'zarincoach' ),
						'1fr 2fr'       => '۱ : ۲',
						'2fr 3fr'       => '۲ : ۳',
						'5fr 7fr'       => '۵ : ۷',
						'1fr 1fr'       => '۱ : ۱',
						'7fr 5fr'       => '۷ : ۵',
						'3fr 2fr'       => '۳ : ۲',
						'2fr 1fr'       => '۲ : ۱',
					),
					'description' => __( 'ستون اول = سمت راست. برای موبایل «یک ستون» را انتخاب کنید.', 'zarincoach' ),
					'selectors'   => array(
						$this->zc_sel( $grid )     => 'grid-template-columns: {{VALUE}};',
						$this->zc_sel( $children ) => 'grid-column: auto;',
					),
				)
			);
		}

		/**
		 * کنترل‌های اختصاصی ویجت (در هر ویجت بازنویسی می‌شود): zc_toggles() و zc_style().
		 *
		 * @return void
		 */
		protected function zc_widget_controls() {
		}

		/**
		 * آیا بخش «نمایش اجزا» ساخته شده است؟
		 *
		 * @var bool
		 */
		protected $zc_toggles_done = false;

		/**
		 * افزودن بخش‌های مشترک به انتهای کنترل‌های هر ویجت.
		 *
		 * @return void
		 */
		protected function zc_auto_sections() {
			// ۱) سربرگ بخش (اگر ویجت کنترل‌های سربرگ دارد).
			if ( null !== $this->get_controls( 'eyebrow' ) ) {
				$this->zc_style(
					'head',
					__( 'سربرگ بخش (برچسب، عنوان، زیرعنوان)', 'zarincoach' ),
					array(
						'wrap'     => array(
							'box',
							'.zc-section-head',
							__( 'قاب سربرگ', 'zarincoach' ),
							array(
								'padding' => false,
								'width'   => true,
								'margin'  => true,
							),
						),
						'eyebrow'  => array( 'text', '.zc-section-head .zc-eyebrow', __( 'برچسب کوتاه', 'zarincoach' ), array( 'bg' => true, 'padding' => true ) ),
						'dot'      => array(
							'color',
							'.zc-section-head .zc-eyebrow::before, .zc-section-head .zc-eyebrow::after',
							__( 'خط/نقطه‌ی کنار برچسب', 'zarincoach' ),
							array( 'prop' => 'background-color' ),
						),
						'title'    => array( 'text', '.zc-section-head :is(h1,h2,h3,h4,h5,h6,.zc-title-lg)', __( 'عنوان', 'zarincoach' ), array( 'width' => true ) ),
						'accent'   => array( 'color', '.zc-section-head .zc-gradient-text', __( 'رنگ واژه‌ی رنگی عنوان (به‌جای گرادیان)', 'zarincoach' ), array( 'prop' => '-webkit-text-fill-color' ) ),
						'subtitle' => array( 'text', '.zc-section-head .zc-lead', __( 'زیرعنوان', 'zarincoach' ), array( 'width' => true ) ),
					)
				);
			}

			// ۲) دکمه (اگر ویجت دکمه‌ی مشترک دارد).
			if ( null !== $this->get_controls( 'button_text' ) && null !== $this->get_controls( 'button_url' ) ) {
				$this->zc_style(
					'btn',
					__( 'دکمه', 'zarincoach' ),
					array(
						'btn' => array( 'button', '.zc-btn', '', array( 'width' => true ) ),
					)
				);
			}

			// ۳) کنترل‌های اختصاصی هر ویجت (نمایش اجزا + بخش‌های استایل اجزا).
			$this->zc_widget_controls();

			// اگر ویجت خودش بخش نمایش اجزا نساخته، اجزای سربرگ مشترک را اضافه کن.
			$this->zc_toggles( array() );

			// ۴) چیدمان بخش.
			$this->start_controls_section(
				'zs_layout',
				array(
					'label' => __( 'چیدمان و فاصله‌های بخش', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);
			$root = $this->zc_root_sel();
			$this->zc_dims( 'zs_layout_pad', __( 'فاصله‌ی داخلی بخش', 'zarincoach' ), $root, 'padding' );
			$this->add_responsive_control(
				'zs_layout_container',
				array(
					'label'       => __( 'عرض محتوای بخش', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::SLIDER,
					'size_units'  => array( 'px', '%', 'vw' ),
					'range'       => array(
						'px' => array(
							'min' => 320,
							'max' => 1920,
						),
						'%'  => array(
							'min' => 30,
							'max' => 100,
						),
						'vw' => array(
							'min' => 30,
							'max' => 100,
						),
					),
					'description' => __( 'حداکثر عرض ظرف داخلی (پیش‌فرض: عرض سایت در پنل تنظیمات).', 'zarincoach' ),
					'selectors'   => array(
						$this->zc_sel( '.zc-container' ) => 'max-width: {{SIZE}}{{UNIT}};',
					),
				)
			);
			$this->zc_dims( 'zs_layout_cpad', __( 'فاصله‌ی افقی ظرف داخلی', 'zarincoach' ), '.zc-container', 'padding' );
			$this->zc_slider( 'zs_layout_minh', __( 'حداقل ارتفاع بخش', 'zarincoach' ), $root, 'min-height', array( 'px', 'vh' ), 1400 );
			$this->add_group_control(
				\Elementor\Group_Control_Background::get_type(),
				array(
					'name'     => 'zs_layout_bg',
					'types'    => array( 'classic', 'gradient' ),
					'selector' => $this->zc_sel( $root ),
				)
			);
			$this->add_group_control(
				\Elementor\Group_Control_Border::get_type(),
				array(
					'name'     => 'zs_layout_border',
					'selector' => $this->zc_sel( $root ),
				)
			);
			$this->zc_dims( 'zs_layout_radius', __( 'گردی گوشه‌های بخش', 'zarincoach' ), $root, 'border-radius', array( 'px', '%', 'em' ) );
			$this->add_control(
				'zs_layout_decor',
				array(
					'label'                => __( 'اشکال تزئینی پس‌زمینه', 'zarincoach' ),
					'type'                 => \Elementor\Controls_Manager::SWITCHER,
					'label_on'             => __( 'نمایش', 'zarincoach' ),
					'label_off'            => __( 'پنهان', 'zarincoach' ),
					'return_value'         => 'yes',
					'default'              => 'yes',
					'separator'            => 'before',
					'description'          => __( 'دایره‌ها، نورها و خطوط تزئینی پشت محتوا.', 'zarincoach' ),
					'selectors_dictionary' => array(
						''    => 'none !important',
						'yes' => '',
					),
					'selectors'            => array(
						$this->zc_sel( '.zc-decor, [aria-hidden="true"].pointer-events-none, .pointer-events-none.absolute:empty' ) => 'display: {{VALUE}};',
						$this->zc_sel( '&.zc-tone-inverse::before, &.zc-tone-inverse::after' ) => 'display: {{VALUE}};',
					),
				)
			);
			$this->add_control(
				'zs_layout_reveal',
				array(
					'label'        => __( 'انیمیشن ظاهر شدن هنگام اسکرول', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'روشن', 'zarincoach' ),
					'label_off'    => __( 'خاموش', 'zarincoach' ),
					'return_value' => 'yes',
					'default'      => 'yes',
					'selectors_dictionary' => array(
						''    => '1 !important; transform: none !important; transition: none !important',
						'yes' => '',
					),
					'selectors'    => array(
						$this->zc_sel( '.zc-reveal' ) => 'opacity: {{VALUE}};',
					),
				)
			);
			$this->end_controls_section();

			// ۴) رنگ‌های ویجت (بازنویسی متغیرهای پالت فقط برای همین ویجت).
			$this->start_controls_section(
				'zp_section',
				array(
					'label' => __( 'رنگ‌های این ویجت (پالت اختصاصی)', 'zarincoach' ),
					'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
				)
			);
			$this->add_control(
				'zp_note',
				array(
					'type'            => \Elementor\Controls_Manager::RAW_HTML,
					'raw'             => esc_html__( 'با هر رنگی که اینجا انتخاب کنید، همه‌ی اجزای این ویجت که از آن رنگ پالت استفاده می‌کنند یک‌جا عوض می‌شوند. خالی = رنگ پالت سایت.', 'zarincoach' ),
					'content_classes' => 'elementor-descriptor',
				)
			);
			foreach ( $this->zc_palette_keys() as $key => $label ) {
				$this->add_control(
					'zp_' . $key,
					array(
						'label'  => $label,
						'type'   => \Elementor\Controls_Manager::COLOR,
						'alpha'  => false,
						'global' => array( 'active' => false ),
					)
				);
			}
			$this->add_control(
				'zp_dark',
				array(
					'label'        => __( 'در حالت تیره هم اعمال شود', 'zarincoach' ),
					'type'         => \Elementor\Controls_Manager::SWITCHER,
					'label_on'     => __( 'بله', 'zarincoach' ),
					'label_off'    => __( 'خیر', 'zarincoach' ),
					'return_value' => 'yes',
					'default'      => '',
					'separator'    => 'before',
					'description'  => __( 'خاموش: در حالت تیره، رنگ‌های پالت تیره‌ی قالب حفظ می‌شود تا خوانایی به هم نخورد.', 'zarincoach' ),
				)
			);
			$this->end_controls_section();
		}

		/**
		 * کلیدهای پالت قابل بازنویسی در هر ویجت.
		 *
		 * @return array<string, string>
		 */
		protected function zc_palette_keys() {
			return array(
				'primary'    => __( 'رنگ اصلی (دکمه‌ها، آیکن‌ها، تأکیدها)', 'zarincoach' ),
				'primary-fg' => __( 'متن روی رنگ اصلی', 'zarincoach' ),
				'secondary'  => __( 'رنگ ثانویه (سرمه‌ای عمیق)', 'zarincoach' ),
				'accent'     => __( 'رنگ تأکیدی (طلایی)', 'zarincoach' ),
				'ink'        => __( 'رنگ متن اصلی', 'zarincoach' ),
				'muted'      => __( 'رنگ متن کم‌رنگ', 'zarincoach' ),
				'base'       => __( 'زمینه‌ی بخش', 'zarincoach' ),
				'surface'    => __( 'زمینه‌ی کارت‌ها', 'zarincoach' ),
				'surface2'   => __( 'زمینه‌ی ثانویه (جعبه‌های ملایم)', 'zarincoach' ),
				'line'       => __( 'رنگ خطوط و حاشیه‌ها', 'zarincoach' ),
				'inverse-bg' => __( 'زمینه‌ی حالت سرمه‌ای (تُن تیره)', 'zarincoach' ),
			);
		}

		/**
		 * تبدیل رنگ (hex / rgb / rgba) به سه‌تایی «r g b» برای متغیرهای --zc-*-rgb.
		 *
		 * @param string $color رنگ.
		 * @return string سه‌تایی یا رشته‌ی خالی.
		 */
		public static function zc_rgb_triplet( $color ) {
			$color = strtolower( trim( (string) $color ) );
			if ( preg_match( '/^#([0-9a-f]{3,8})$/', $color, $m ) ) {
				$hex = $m[1];
				if ( 3 === strlen( $hex ) || 4 === strlen( $hex ) ) {
					$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
				}
				if ( strlen( $hex ) < 6 ) {
					return '';
				}
				return hexdec( substr( $hex, 0, 2 ) ) . ' ' . hexdec( substr( $hex, 2, 2 ) ) . ' ' . hexdec( substr( $hex, 4, 2 ) );
			}
			if ( preg_match( '/^rgba?\(\s*(\d{1,3})[\s,]+(\d{1,3})[\s,]+(\d{1,3})/', $color, $m ) ) {
				return min( 255, (int) $m[1] ) . ' ' . min( 255, (int) $m[2] ) . ' ' . min( 255, (int) $m[3] );
			}
			return '';
		}

		/**
		 * CSS زمان اجرا (پالت اختصاصی ویجت) که پیش از خروجی ویجت چاپ می‌شود.
		 *
		 * @return string
		 */
		public function zc_runtime_css() {
			$settings = $this->get_settings();
			$vars     = array();
			foreach ( array_keys( $this->zc_palette_keys() ) as $key ) {
				$value = isset( $settings[ 'zp_' . $key ] ) ? (string) $settings[ 'zp_' . $key ] : '';
				if ( '' === $value ) {
					continue;
				}
				$rgb = self::zc_rgb_triplet( $value );
				if ( '' !== $rgb ) {
					$vars[] = '--zc-' . $key . '-rgb:' . $rgb;
				}
			}
			if ( empty( $vars ) ) {
				return '';
			}
			$id    = preg_replace( '/[^a-z0-9]/i', '', (string) $this->get_id() );
			$scope = '.elementor-element-' . $id;
			$sel   = ( isset( $settings['zp_dark'] ) && 'yes' === $settings['zp_dark'] ) ? $scope : 'html:not([data-theme="dark"]) ' . $scope;
			return $sel . '{' . implode( ';', $vars ) . '}';
		}
	}

endif;
