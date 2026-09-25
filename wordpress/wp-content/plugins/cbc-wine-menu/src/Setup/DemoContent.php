<?php
/**
 * Seeds local dummy wine content for development.
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
 * Inserts sample categories and wines once for local testing.
 */
final class DemoContent {

	private const OPTION_KEY = 'cbc_wine_menu_demo_content_seeded';

	/**
	 * Seeds demo content if it has not been seeded yet.
	 *
	 * @return void
	 */
	public static function maybe_seed(): void {
		if ( get_option( self::OPTION_KEY ) ) {
			return;
		}

		self::seed_categories();
		self::seed_wines();

		update_option( self::OPTION_KEY, '1', false );
	}

	/**
	 * Creates starter wine categories.
	 *
	 * Slugs stay locale-stable; visible names follow the active site language.
	 * These are sample terms only and may change after the real wine PDF arrives.
	 *
	 * @return void
	 */
	private static function seed_categories(): void {
		$categories = array(
			'sparkling-wines' => __( 'Sparkling wines', 'cbc-wine-menu' ),
			'champagne'       => __( 'Champagne', 'cbc-wine-menu' ),
			'white-wines'     => __( 'White wines', 'cbc-wine-menu' ),
			'rose-wines'      => __( 'Rosé wines', 'cbc-wine-menu' ),
			'red-wines'       => __( 'Red wines', 'cbc-wine-menu' ),
			'dessert-wines'   => __( 'Dessert wines', 'cbc-wine-menu' ),
		);

		foreach ( $categories as $slug => $name ) {
			if ( term_exists( $slug, WineCategory::TAXONOMY ) ) {
				continue;
			}

			wp_insert_term(
				$name,
				WineCategory::TAXONOMY,
				array(
					'slug' => $slug,
				)
			);
		}
	}

	/**
	 * Creates a few dummy wines for admin testing.
	 *
	 * @return void
	 */
	private static function seed_wines(): void {
		$wines = array(
			array(
				'title'    => 'Greco di Tufo',
				'content'  => 'Crisp white wine with citrus and mineral notes. Dummy data for local development.',
				'category' => 'white-wines',
				'meta'     => array(
					'winery'             => 'Demo Cantina',
					'vintage'            => '2022',
					'region'             => 'Campania',
					'country'            => 'Italy',
					'grape_variety'      => 'Greco',
					'appellation'        => 'DOCG',
					'alcohol_percentage' => '13',
					'bottle_size'        => '750ml',
					'bottle_price'       => '28.00',
					'glass_price'        => '7.00',
					'is_available'       => '1',
					'display_order'      => '10',
				),
			),
			array(
				'title'    => 'Aglianico',
				'content'  => 'Full-bodied red with dark fruit and spice. Dummy data for local development.',
				'category' => 'red-wines',
				'meta'     => array(
					'winery'             => 'Demo Cantina',
					'vintage'            => '2019',
					'region'             => 'Campania',
					'country'            => 'Italy',
					'grape_variety'      => 'Aglianico',
					'appellation'        => 'DOC',
					'alcohol_percentage' => '14',
					'bottle_size'        => '750ml',
					'bottle_price'       => '32.00',
					'glass_price'        => '8.00',
					'is_available'       => '1',
					'display_order'      => '20',
				),
			),
			array(
				'title'    => 'Prosecco Extra Dry',
				'content'  => 'Light sparkling wine for aperitivo. Dummy data for local development.',
				'category' => 'sparkling-wines',
				'meta'     => array(
					'winery'             => 'Demo Cantina',
					'vintage'            => '2023',
					'region'             => 'Veneto',
					'country'            => 'Italy',
					'grape_variety'      => 'Glera',
					'appellation'        => 'DOC',
					'alcohol_percentage' => '11',
					'bottle_size'        => '750ml',
					'bottle_price'       => '22.00',
					'glass_price'        => '6.00',
					'is_available'       => '0',
					'display_order'      => '5',
				),
			),
		);

		foreach ( $wines as $wine ) {
			$existing = new \WP_Query(
				array(
					'post_type'              => Wine::POST_TYPE,
					'title'                  => $wine['title'],
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);

			if ( $existing->have_posts() ) {
				continue;
			}

			$post_id = wp_insert_post(
				array(
					'post_title'   => $wine['title'],
					'post_content' => $wine['content'],
					'post_status'  => 'publish',
					'post_type'    => Wine::POST_TYPE,
					'menu_order'   => (int) $wine['meta']['display_order'],
				),
				true
			);

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			foreach ( $wine['meta'] as $field => $value ) {
				update_post_meta( $post_id, WineMetaBox::meta_key( $field ), $value );
			}

			$term = get_term_by( 'slug', $wine['category'], WineCategory::TAXONOMY );

			if ( $term instanceof \WP_Term ) {
				wp_set_object_terms( $post_id, array( (int) $term->term_id ), WineCategory::TAXONOMY );
			}
		}
	}
}
