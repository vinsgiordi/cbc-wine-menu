<?php
/**
 * Imports wines from the Cannavale catalog dataset.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Setup;

use CBCWineMenu\Admin\WineMetaBox;
use CBCWineMenu\PostTypes\Wine;
use CBCWineMenu\Taxonomies\WineCategory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One-time catalog import for local/development content.
 */
final class CatalogImporter {

	private const OPTION_KEY = 'cbc_wine_menu_catalog_imported_v1';

	/**
	 * Imports catalog wines once.
	 *
	 * @param bool $force Re-run even if already imported.
	 * @return int Number of wines imported/updated.
	 */
	public static function maybe_import( bool $force = false ): int {
		if ( ! $force && get_option( self::OPTION_KEY ) ) {
			return 0;
		}

		CatalogCategories::ensure();
		self::cleanup_legacy_demo_wines();

		$wines = self::load_catalog();
		$count = 0;

		foreach ( $wines as $index => $wine ) {
			if ( self::upsert_wine( $wine, (int) $index ) ) {
				++$count;
			}
		}

		update_option( self::OPTION_KEY, '1', false );
		update_option( 'cbc_wine_menu_demo_content_seeded', '1', false );

		return $count;
	}

	/**
	 * Loads the catalog dataset.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	private static function load_catalog(): array {
		$path = CBC_WINE_MENU_PATH . 'data/catalog-wines.php';

		if ( ! is_readable( $path ) ) {
			return array();
		}

		$data = require $path;

		return is_array( $data ) ? $data : array();
	}

	/**
	 * Creates or updates a wine post from catalog data.
	 *
	 * @param array<string, mixed> $wine  Wine payload.
	 * @param int                  $index Sort index.
	 * @return bool
	 */
	private static function upsert_wine( array $wine, int $index ): bool {
		$title = isset( $wine['title'] ) ? (string) $wine['title'] : '';

		if ( '' === $title ) {
			return false;
		}

		$existing = new \WP_Query(
			array(
				'post_type'              => Wine::POST_TYPE,
				'title'                  => $title,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache' => false,
				'update_post_term_cache' => false,
			)
		);

		$display_order = isset( $wine['display_order'] ) ? (int) $wine['display_order'] : ( ( $index + 1 ) * 10 );
		$content       = isset( $wine['content'] ) ? (string) $wine['content'] : '';

		$postarr = array(
			'post_title'   => $title,
			'post_content' => $content,
			'post_status'  => 'publish',
			'post_type'    => Wine::POST_TYPE,
			'menu_order'   => $display_order,
		);

		if ( $existing->have_posts() ) {
			$postarr['ID'] = (int) $existing->posts[0];
			$post_id       = wp_update_post( $postarr, true );
		} else {
			$post_id = wp_insert_post( $postarr, true );
		}

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return false;
		}

		$meta = isset( $wine['meta'] ) && is_array( $wine['meta'] ) ? $wine['meta'] : array();

		if ( ! isset( $meta['is_available'] ) ) {
			$meta['is_available'] = '1';
		}

		if ( ! isset( $meta['bottle_size'] ) ) {
			$meta['bottle_size'] = '750ml';
		}

		if ( ! isset( $meta['display_order'] ) ) {
			$meta['display_order'] = (string) $display_order;
		}

		if ( ! isset( $meta['ask_for_vintage'] ) ) {
			$meta['ask_for_vintage'] = '0';
		}

		foreach ( $meta as $field => $value ) {
			update_post_meta( (int) $post_id, WineMetaBox::meta_key( (string) $field ), (string) $value );
		}

		$category_slug = isset( $wine['category'] ) ? (string) $wine['category'] : '';

		if ( '' !== $category_slug ) {
			$term = get_term_by( 'slug', $category_slug, WineCategory::TAXONOMY );

			if ( $term instanceof \WP_Term ) {
				wp_set_object_terms( (int) $post_id, array( (int) $term->term_id ), WineCategory::TAXONOMY );
			}
		}

		return true;
	}

	/**
	 * Removes early placeholder wines that are not part of the real catalog.
	 *
	 * @return void
	 */
	private static function cleanup_legacy_demo_wines(): void {
		$titles = array(
			'Greco di Tufo',
			'Aglianico',
			'Prosecco Extra Dry',
		);

		foreach ( $titles as $title ) {
			$query = new \WP_Query(
				array(
					'post_type'              => Wine::POST_TYPE,
					'title'                  => $title,
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);

			if ( $query->have_posts() ) {
				wp_delete_post( (int) $query->posts[0], true );
			}
		}
	}
}
