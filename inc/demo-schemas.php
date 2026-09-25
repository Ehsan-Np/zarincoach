<?php
/**
 * درون‌ریزی دموی کتابخانه‌ی طرحواره‌ها
 *
 * داده‌ها در چهار فایل جداگانه نگهداری می‌شوند و فقط هنگام نصب دمو بارگذاری می‌شوند
 * (هیچ هزینه‌ای برای درخواست‌های عادی سایت ندارند).
 *
 * @package ZarinCoach
 */

defined( 'ABSPATH' ) || exit;

if ( ! function_exists( 'zc_demo_schemas' ) ) :
	/**
	 * همه‌ی مدخل‌های دمو (۱۸ طرحواره، ۱۴ ذهنیت، ۳ سبک مقابله، ۱۲ خطای شناختی).
	 *
	 * @return array<int, array<string, mixed>>
	 */
	function zc_demo_schemas() {
		foreach ( array( 'ems-a', 'ems-b', 'modes', 'thinking' ) as $part ) {
			require_once ZC_DIR . '/inc/demo-schemas-' . $part . '.php';
		}
		return array_merge(
			zc_demo_schemas_ems_a(),
			zc_demo_schemas_ems_b(),
			zc_demo_schemas_modes(),
			zc_demo_schemas_thinking()
		);
	}
endif;

if ( ! function_exists( 'zc_demo_install_schema_groups' ) ) :
	/**
	 * ایجاد گروه‌ها (والدها پیش از فرزندان).
	 *
	 * @return array<string, int> نامک => شناسه.
	 */
	function zc_demo_install_schema_groups() {
		$ids = array();
		if ( ! taxonomy_exists( 'zc_schema_group' ) ) {
			return $ids;
		}
		$groups = zc_sc_groups();
		// والدها اول.
		uasort(
			$groups,
			static function ( $a, $b ) {
				return (int) ! empty( $a['parent'] ) - (int) ! empty( $b['parent'] );
			}
		);
		foreach ( $groups as $slug => $g ) {
			$parent = ( ! empty( $g['parent'] ) && ! empty( $ids[ $g['parent'] ] ) ) ? (int) $ids[ $g['parent'] ] : 0;
			$term   = get_term_by( 'slug', $slug, 'zc_schema_group' );
			if ( $term ) {
				wp_update_term(
					(int) $term->term_id,
					'zc_schema_group',
					array(
						'name'        => $g['name'],
						'parent'      => $parent,
						'description' => $g['desc'],
					)
				);
				$ids[ $slug ] = (int) $term->term_id;
				continue;
			}
			$made = wp_insert_term(
				$g['name'],
				'zc_schema_group',
				array(
					'slug'        => $slug,
					'parent'      => $parent,
					'description' => $g['desc'],
				)
			);
			if ( ! is_wp_error( $made ) ) {
				$ids[ $slug ] = (int) $made['term_id'];
				update_term_meta( (int) $made['term_id'], '_zc_demo_term', $slug );
				zc_demo_track( 'schema_groups', (int) $made['term_id'] );
			}
		}
		return $ids;
	}
endif;

if ( ! function_exists( 'zc_demo_install_schemas' ) ) :
	/**
	 * ایجاد همه‌ی مدخل‌ها.
	 *
	 * @param int $author نویسنده.
	 * @return array{0:int,1:int} [ایجادشده، کل]
	 */
	function zc_demo_install_schemas( $author = 0 ) {
		$term_ids = zc_demo_install_schema_groups();
		$items    = zc_demo_schemas();
		$made     = 0;
		$index    = array();

		foreach ( $items as $item ) {
			$group = (string) $item['group'];
			$def   = zc_sc_group( $group );
			$top   = ! empty( $def['parent'] ) ? zc_sc_group( $def['parent'] ) : $def;

			$index[ $group ] = isset( $index[ $group ] ) ? $index[ $group ] + 1 : 1;
			$order           = $index[ $group ];
			$menu_order      = ( (int) ( $top['order'] ?? 9 ) * 1000 ) + ( ! empty( $def['parent'] ) ? (int) $def['order'] * 100 : 0 ) + $order;

			$marker = 'schema:' . $item['slug'];
			if ( zc_demo_find_post( 'zc_schema', '_zc_demo_item', $marker ) ) {
				continue;
			}

			$content = '';
			foreach ( (array) $item['intro'] as $para ) {
				$content .= "<!-- wp:paragraph -->\n<p>" . esc_html( $para ) . "</p>\n<!-- /wp:paragraph -->\n\n";
			}

			$post_id = wp_insert_post(
				wp_slash(
					array(
						'post_type'    => 'zc_schema',
						'post_status'  => 'publish',
						'post_title'   => $item['title'],
						'post_name'    => $item['slug'],
						'post_excerpt' => $item['summary'],
						'post_content' => trim( $content ),
						'menu_order'   => $menu_order,
						'post_author'  => (int) $author,
					)
				),
				true
			);
			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}
			$post_id = (int) $post_id;

			$meta = array(
				'_zc_sc_en'      => (string) ( $item['en'] ?? '' ),
				'_zc_sc_code'    => (string) ( $item['code'] ?? '' ),
				'_zc_sc_order'   => (string) $order,
				'_zc_sc_belief'  => (string) ( $item['belief'] ?? '' ),
				'_zc_sc_need'    => (string) ( $item['need'] ?? '' ),
				'_zc_sc_example' => (string) ( $item['example'] ?? '' ),
				'_zc_sc_healthy' => (string) ( $item['healthy'] ?? '' ),
				'_zc_sc_related' => implode( ',', (array) ( $item['related'] ?? array() ) ),
			);
			foreach ( array( 'signs', 'origins', 'triggers', 'exercise' ) as $list ) {
				$meta[ '_zc_sc_' . $list ] = implode( "\n", (array) ( $item[ $list ] ?? array() ) );
			}
			if ( ! empty( $item['cope'] ) && is_array( $item['cope'] ) ) {
				$meta['_zc_sc_cope_surrender'] = (string) ( $item['cope']['surrender'] ?? '' );
				$meta['_zc_sc_cope_avoid']     = (string) ( $item['cope']['avoid'] ?? '' );
				$meta['_zc_sc_cope_over']      = (string) ( $item['cope']['over'] ?? '' );
			}
			foreach ( $meta as $key => $value ) {
				if ( '' !== $value ) {
					update_post_meta( $post_id, $key, wp_slash( $value ) );
				}
			}
			update_post_meta( $post_id, '_zc_demo_item', $marker );

			if ( ! empty( $term_ids[ $group ] ) ) {
				wp_set_object_terms( $post_id, array( (int) $term_ids[ $group ] ), 'zc_schema_group' );
			}

			zc_demo_track( 'schemas', $post_id );
			$made++;
		}

		return array( $made, count( $items ) );
	}
endif;
