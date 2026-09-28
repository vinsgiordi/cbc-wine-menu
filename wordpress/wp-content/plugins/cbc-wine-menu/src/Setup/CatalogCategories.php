<?php
/**
 * Catalog wine categories aligned with the restaurant PDF.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Setup;

use CBCWineMenu\Taxonomies\WineCategory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Ensures the four core menu categories exist.
 */
final class CatalogCategories {

	/**
	 * Returns locale-stable category definitions.
	 *
	 * @return array<string, string>
	 */
	public static function definitions(): array {
		return array(
			'sparkling-wines' => __( 'Sparkling wines', 'cbc-wine-menu' ),
			'white-wines'     => __( 'White wines', 'cbc-wine-menu' ),
			'rose-wines'      => __( 'Rosé wines', 'cbc-wine-menu' ),
			'red-wines'       => __( 'Red wines', 'cbc-wine-menu' ),
		);
	}

	/**
	 * Creates or updates catalog categories.
	 *
	 * @return void
	 */
	public static function ensure(): void {
		foreach ( self::definitions() as $slug => $name ) {
			$existing = term_exists( $slug, WineCategory::TAXONOMY );

			if ( ! $existing ) {
				wp_insert_term(
					$name,
					WineCategory::TAXONOMY,
					array(
						'slug' => $slug,
					)
				);
				continue;
			}

			$term_id = is_array( $existing ) ? (int) $existing['term_id'] : (int) $existing;

			wp_update_term(
				$term_id,
				WineCategory::TAXONOMY,
				array(
					'name' => $name,
					'slug' => $slug,
				)
			);
		}
	}
}
