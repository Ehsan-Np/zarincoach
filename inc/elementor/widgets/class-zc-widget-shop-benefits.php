<?php
/**
 * ویجت مزایای خرید از فروشگاه
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'ZC_Widget_Shop_Benefits' ) && class_exists( 'ZC_Shop_Widget_Base' ) ) :

	/**
	 * نوار مزایا: تحویل فوری، ارسال، پرداخت امن، ضمانت.
	 */
	class ZC_Widget_Shop_Benefits extends ZC_Shop_Widget_Base {

		/**
		 * نام.
		 *
		 * @return string
		 */
		public function get_name() {
			return 'zc-shop-benefits';
		}

		/**
		 * عنوان.
		 *
		 * @return string
		 */
		public function get_title() {
			return __( 'مزایای خرید', 'zarincoach' );
		}

		/**
		 * آیکون.
		 *
		 * @return string
		 */
		public function get_icon() {
			return 'eicon-info-box';
		}

		/**
		 * بدون اسکریپت.
		 *
		 * @return array
		 */
		public function get_script_depends() {
			return array();
		}

		/**
		 * آیکون‌های قابل انتخاب.
		 *
		 * @return array<string, string>
		 */
		protected function icon_options() {
			return array(
				'zap'          => __( 'صاعقه (فوری)', 'zarincoach' ),
				'download'     => __( 'دانلود', 'zarincoach' ),
				'truck'        => __( 'ارسال', 'zarincoach' ),
				'package'      => __( 'بسته', 'zarincoach' ),
				'shield-check' => __( 'سپر', 'zarincoach' ),
				'lock'         => __( 'قفل', 'zarincoach' ),
				'credit-card'  => __( 'کارت بانکی', 'zarincoach' ),
				'refresh'      => __( 'بازگشت', 'zarincoach' ),
				'headphones'   => __( 'پشتیبانی', 'zarincoach' ),
				'gift'         => __( 'هدیه', 'zarincoach' ),
				'video'        => __( 'جلسه آنلاین', 'zarincoach' ),
				'award'        => __( 'نشان', 'zarincoach' ),
				'badge-check'  => __( 'تأیید', 'zarincoach' ),
				'heart'        => __( 'قلب', 'zarincoach' ),
			);
		}

		/**
		 * کنترل‌ها.
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

			$rep = new \Elementor\Repeater();
			$rep->add_control(
				'icon',
				array(
					'label'   => __( 'آیکون', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => $this->icon_options(),
					'default' => 'zap',
				)
			);
			$rep->add_control(
				'title',
				array(
					'label'       => __( 'عنوان', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::TEXT,
					'label_block' => true,
				)
			);
			$rep->add_control(
				'text',
				array(
					'label' => __( 'توضیح', 'zarincoach' ),
					'type'  => \Elementor\Controls_Manager::TEXTAREA,
					'rows'  => 2,
				)
			);
			$rep->add_control(
				'url',
				array(
					'label' => __( 'پیوند (اختیاری)', 'zarincoach' ),
					'type'  => \Elementor\Controls_Manager::URL,
				)
			);

			$rep->add_control( 'eicon', array( 'label' => __( 'آیکون از کتابخانه (اختیاری)', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::ICONS, 'default' => array( 'value' => '', 'library' => '' ), 'description' => __( 'در صورت انتخاب، جای آیکون فهرستی را می‌گیرد', 'zarincoach' ) ) );
			$rep->add_control( 'on', array( 'label' => __( 'نمایش این آیتم', 'zarincoach' ), 'type' => \Elementor\Controls_Manager::SWITCHER, 'return_value' => 'yes', 'default' => 'yes' ) );

			$this->add_control(
				'items',
				array(
					'label'       => __( 'موارد', 'zarincoach' ),
					'type'        => \Elementor\Controls_Manager::REPEATER,
					'fields'      => $rep->get_controls(),
					'separator'   => 'before',
					'title_field' => '{{{ title }}}',
					'default'     => array(
						array(
							'icon'  => 'zap',
							'title' => __( 'تحویل فوری فایل‌ها', 'zarincoach' ),
							'text'  => __( 'لینک دانلود بلافاصله پس از پرداخت', 'zarincoach' ),
						),
						array(
							'icon'  => 'truck',
							'title' => __( 'ارسال به سراسر ایران', 'zarincoach' ),
							'text'  => __( 'پست پیشتاز با کد رهگیری', 'zarincoach' ),
						),
						array(
							'icon'  => 'shield-check',
							'title' => __( 'پرداخت امن', 'zarincoach' ),
							'text'  => __( 'درگاه بانکی معتبر شاپرک', 'zarincoach' ),
						),
						array(
							'icon'  => 'headphones',
							'title' => __( 'پشتیبانی واقعی', 'zarincoach' ),
							'text'  => __( 'پاسخ‌گویی در روزهای کاری', 'zarincoach' ),
						),
					),
				)
			);

			$this->add_control(
				'style',
				array(
					'label'   => __( 'سبک', 'zarincoach' ),
					'type'    => \Elementor\Controls_Manager::SELECT,
					'options' => array(
						'strip' => __( 'نوار یکپارچه', 'zarincoach' ),
						'cards' => __( 'کارت‌های جدا', 'zarincoach' ),
					),
					'default' => 'strip',
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
					'icon' => array( __( 'آیکن‌ها', 'zarincoach' ), '.zc-benefit__icon' ),
					'text' => array( __( 'متن توضیح', 'zarincoach' ), '.zc-benefit__text small' ),
				)
			);
			$this->zc_style(
				'bn_items',
				__( 'آیتم‌ها', 'zarincoach' ),
				array(
					'gap'  => array( 'size', '.zc-benefits', __( 'فاصله‌ی آیتم‌ها', 'zarincoach' ), array( 'prop' => 'gap', 'max' => 60 ) ),
					'item' => array( 'box', '.zc-benefit', __( 'آیتم', 'zarincoach' ), array( 'hover' => true ) ),
					'icon' => array( 'icon', '.zc-benefit__icon', __( 'آیکن', 'zarincoach' ), array( 'hover' => '.zc-benefit' ) ),
					'title'=> array( 'text', '.zc-benefit__text strong', __( 'عنوان', 'zarincoach' ), array( 'margin' => false ) ),
					'text' => array( 'text', '.zc-benefit__text small', __( 'توضیح', 'zarincoach' ), array( 'margin' => false ) ),
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
			$items = array_filter(
				(array) ( $s['items'] ?? array() ),
				static function ( $i ) {
					return '' !== trim( (string) ( $i['title'] ?? '' ) );
				}
			);
			if ( ! $items ) {
				return;
			}
			$icons = $this->icon_options();
			$style = 'cards' === ( $s['style'] ?? 'strip' ) ? 'cards' : 'strip';
			?>
			<section class="zc-section zc-benefits-wrap">
				<div class="zc-container">
					<?php $this->render_heading( '', 'h2' ); ?>
					<ul class="zc-benefits zc-benefits--<?php echo esc_attr( $style ); ?>" style="<?php echo esc_attr( '--zc-count:' . min( 6, count( $items ) ) ); ?>">
						<?php foreach ( $items as $item ) : ?>
							<?php
							if ( isset( $item['on'] ) && 'yes' !== (string) $item['on'] ) { continue; }
							$eicon = ( isset( $item['eicon']['value'] ) && '' !== (string) $item['eicon']['value'] ) ? $item['eicon'] : array();
							$icon = isset( $icons[ $item['icon'] ?? '' ] ) ? $item['icon'] : 'check';
							$url  = ! empty( $item['url']['url'] ) ? (string) $item['url']['url'] : '';
							$tag  = '' !== $url ? 'a' : 'div';
							?>
							<li>
								<<?php echo esc_html( $tag ); ?> class="zc-benefit"<?php echo '' !== $url ? ' href="' . esc_url( zc_url( $url ) ) . '"' . ( ! empty( $item['url']['is_external'] ) ? ' target="_blank" rel="noopener"' : '' ) : ''; ?>>
									<span class="zc-benefit__icon"><?php if ( ! empty( $eicon ) ) { $this->render_icon( $eicon, 'h-6 w-6' ); } else { zc_icon( $icon, 'h-6 w-6' ); } ?></span>
									<span class="zc-benefit__text">
										<strong><?php echo esc_html( $item['title'] ); ?></strong>
										<?php if ( '' !== trim( (string) ( $item['text'] ?? '' ) ) ) : ?>
											<small><?php echo esc_html( $item['text'] ); ?></small>
										<?php endif; ?>
									</span>
								</<?php echo esc_html( $tag ); ?>>
							</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</section>
			<?php
		}
	}

endif;
