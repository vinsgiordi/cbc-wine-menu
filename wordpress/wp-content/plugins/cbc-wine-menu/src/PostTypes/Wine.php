<?php
/**
 * Wine custom post type.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\PostTypes;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the wine post type used for the digital wine list.
 */
final class Wine {

	public const POST_TYPE = 'wine';

	/**
	 * Hooks registration into WordPress.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'init', array( self::class, 'register' ) );
	}

	/**
	 * Registers the custom post type.
	 *
	 * @return void
	 */
	public static function register(): void {
		$labels = array(
			'name'                  => _x( 'Wines', 'Post type general name', 'cbc-wine-menu' ),
			'singular_name'         => _x( 'Wine', 'Post type singular name', 'cbc-wine-menu' ),
			'menu_name'             => _x( 'Wines', 'Admin Menu text', 'cbc-wine-menu' ),
			'name_admin_bar'        => _x( 'Wine', 'Add New on Toolbar', 'cbc-wine-menu' ),
			'add_new'               => __( 'Add New', 'cbc-wine-menu' ),
			'add_new_item'          => __( 'Add New Wine', 'cbc-wine-menu' ),
			'new_item'              => __( 'New Wine', 'cbc-wine-menu' ),
			'edit_item'             => __( 'Edit Wine', 'cbc-wine-menu' ),
			'view_item'             => __( 'View Wine', 'cbc-wine-menu' ),
			'all_items'             => __( 'All Wines', 'cbc-wine-menu' ),
			'search_items'          => __( 'Search Wines', 'cbc-wine-menu' ),
			'parent_item_colon'     => __( 'Parent Wines:', 'cbc-wine-menu' ),
			'not_found'             => __( 'No wines found.', 'cbc-wine-menu' ),
			'not_found_in_trash'    => __( 'No wines found in Trash.', 'cbc-wine-menu' ),
			'featured_image'        => __( 'Wine image', 'cbc-wine-menu' ),
			'set_featured_image'    => __( 'Set wine image', 'cbc-wine-menu' ),
			'remove_featured_image' => __( 'Remove wine image', 'cbc-wine-menu' ),
			'use_featured_image'    => __( 'Use as wine image', 'cbc-wine-menu' ),
			'archives'              => __( 'Wine archives', 'cbc-wine-menu' ),
			'insert_into_item'      => __( 'Insert into wine', 'cbc-wine-menu' ),
			'uploaded_to_this_item' => __( 'Uploaded to this wine', 'cbc-wine-menu' ),
			'filter_items_list'     => __( 'Filter wines list', 'cbc-wine-menu' ),
			'items_list_navigation' => __( 'Wines list navigation', 'cbc-wine-menu' ),
			'items_list'            => __( 'Wines list', 'cbc-wine-menu' ),
		);

		$args = array(
			'labels'              => $labels,
			'public'              => true,
			'publicly_queryable'  => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'query_var'           => true,
			'rewrite'             => array( 'slug' => 'wines' ),
			'capability_type'     => 'post',
			'has_archive'         => false,
			'hierarchical'        => false,
			'menu_position'       => 25,
			'menu_icon'           => 'dashicons-food',
			'supports'            => array( 'title', 'editor', 'thumbnail', 'page-attributes' ),
			'show_in_rest'        => true,
			'exclude_from_search' => true,
		);

		register_post_type( self::POST_TYPE, $args );
	}
}
