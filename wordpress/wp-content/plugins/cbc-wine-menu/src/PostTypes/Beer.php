<?php
/**
 * Beer items.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\PostTypes;

use CBCWineMenu\Admin\MenuIcons;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CPT: beers.
 */
final class Beer {

	public const POST_TYPE = 'cbc_beer';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'init', array( self::class, 'register' ) );
	}

	/**
	 * @return void
	 */
	public static function register(): void {
		SimpleMenuPostType::register(
			self::POST_TYPE,
			array(
				'name'          => _x( 'Beers', 'Post type general name', 'cbc-wine-menu' ),
				'singular_name' => _x( 'Beer', 'Post type singular name', 'cbc-wine-menu' ),
				'menu_name'     => _x( 'Beers', 'Admin Menu text', 'cbc-wine-menu' ),
				'add_new_item'  => __( 'Add New Beer', 'cbc-wine-menu' ),
				'edit_item'     => __( 'Edit Beer', 'cbc-wine-menu' ),
				'view_item'     => __( 'View Beer', 'cbc-wine-menu' ),
				'all_items'     => __( 'All Beers', 'cbc-wine-menu' ),
				'search_items'  => __( 'Search Beers', 'cbc-wine-menu' ),
				'not_found'     => __( 'No beers found.', 'cbc-wine-menu' ),
				'menu_position' => 28,
				'menu_icon'     => MenuIcons::get( 'beer.svg', 'dashicons-beer' ),
			)
		);
	}
}
