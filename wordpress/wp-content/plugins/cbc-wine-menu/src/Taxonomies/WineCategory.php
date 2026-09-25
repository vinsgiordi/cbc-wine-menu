<?php
/**
 * Wine category taxonomy.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Taxonomies;

use CBCWineMenu\PostTypes\Wine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the wine category taxonomy.
 *
 * Category term names are not hard-coded in business logic elsewhere.
 */
final class WineCategory {

	public const TAXONOMY = 'wine_category';

	/**
	 * Hooks registration into WordPress.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'init', array( self::class, 'register' ) );
	}

	/**
	 * Registers the taxonomy.
	 *
	 * @return void
	 */
	public static function register(): void {
		$labels = array(
			'name'                       => _x( 'Wine Categories', 'Taxonomy general name', 'cbc-wine-menu' ),
			'singular_name'              => _x( 'Wine Category', 'Taxonomy singular name', 'cbc-wine-menu' ),
			'search_items'               => __( 'Search Wine Categories', 'cbc-wine-menu' ),
			'popular_items'              => __( 'Popular Wine Categories', 'cbc-wine-menu' ),
			'all_items'                  => __( 'All Wine Categories', 'cbc-wine-menu' ),
			'parent_item'                => __( 'Parent Wine Category', 'cbc-wine-menu' ),
			'parent_item_colon'          => __( 'Parent Wine Category:', 'cbc-wine-menu' ),
			'edit_item'                  => __( 'Edit Wine Category', 'cbc-wine-menu' ),
			'update_item'                => __( 'Update Wine Category', 'cbc-wine-menu' ),
			'add_new_item'               => __( 'Add New Wine Category', 'cbc-wine-menu' ),
			'new_item_name'              => __( 'New Wine Category Name', 'cbc-wine-menu' ),
			'separate_items_with_commas' => __( 'Separate wine categories with commas', 'cbc-wine-menu' ),
			'add_or_remove_items'        => __( 'Add or remove wine categories', 'cbc-wine-menu' ),
			'choose_from_most_used'      => __( 'Choose from the most used wine categories', 'cbc-wine-menu' ),
			'not_found'                  => __( 'No wine categories found.', 'cbc-wine-menu' ),
			'menu_name'                  => __( 'Categories', 'cbc-wine-menu' ),
			'back_to_items'              => __( '&larr; Back to Wine Categories', 'cbc-wine-menu' ),
		);

		$args = array(
			'labels'            => $labels,
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
			'show_in_rest'      => true,
			'rewrite'           => array( 'slug' => 'wine-category' ),
		);

		register_taxonomy( self::TAXONOMY, array( Wine::POST_TYPE ), $args );
	}
}
