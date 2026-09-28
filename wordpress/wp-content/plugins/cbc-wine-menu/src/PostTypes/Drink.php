<?php
/**
 * Drink items (cocktails, spritz, etc.).
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
 * CPT: drinks.
 */
final class Drink {

	public const POST_TYPE = 'cbc_drink';

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
				'name'          => _x( 'Drinks', 'Post type general name', 'cbc-wine-menu' ),
				'singular_name' => _x( 'Drink', 'Post type singular name', 'cbc-wine-menu' ),
				'menu_name'     => _x( 'Drinks', 'Admin Menu text', 'cbc-wine-menu' ),
				'add_new_item'  => __( 'Add New Drink', 'cbc-wine-menu' ),
				'edit_item'     => __( 'Edit Drink', 'cbc-wine-menu' ),
				'view_item'     => __( 'View Drink', 'cbc-wine-menu' ),
				'all_items'     => __( 'All Drinks', 'cbc-wine-menu' ),
				'search_items'  => __( 'Search Drinks', 'cbc-wine-menu' ),
				'not_found'     => __( 'No drinks found.', 'cbc-wine-menu' ),
				'menu_position' => 27,
				'menu_icon'     => MenuIcons::get( 'whiskey-glass.svg', 'dashicons-coffee' ),
			)
		);
	}
}
