<?php
/**
 * Helper to register simple public menu item post types.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers a lightweight CPT for non-bottle menu sections.
 */
final class SimpleMenuPostType {

	/**
	 * @param string               $post_type Post type key.
	 * @param array<string, mixed> $config    Labels and menu settings.
	 * @return void
	 */
	public static function register( string $post_type, array $config ): void {
		$labels = array(
			'name'          => $config['name'],
			'singular_name' => $config['singular_name'],
			'menu_name'     => $config['menu_name'],
			'add_new'       => __( 'Add New', 'cbc-wine-menu' ),
			'add_new_item'  => $config['add_new_item'],
			'edit_item'     => $config['edit_item'],
			'new_item'      => $config['singular_name'],
			'view_item'     => $config['view_item'],
			'all_items'     => $config['all_items'],
			'search_items'  => $config['search_items'],
			'not_found'     => $config['not_found'],
		);

		register_post_type(
			$post_type,
			array(
				'labels'              => $labels,
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'menu_position'       => $config['menu_position'] ?? 26,
				'menu_icon'           => $config['menu_icon'] ?? 'dashicons-admin-post',
				'supports'            => array( 'title', 'page-attributes' ),
				'has_archive'         => false,
				'exclude_from_search' => true,
				'publicly_queryable'  => false,
				'show_in_rest'        => true,
				'capability_type'     => 'post',
			)
		);
	}
}
