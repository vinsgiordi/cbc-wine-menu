<?php
/**
 * Wine by the glass items.
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
 * CPT: glasses.
 */
final class WineGlass {

	public const POST_TYPE = 'cbc_wine_glass';

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
				'name'          => _x( 'Wines by the glass', 'Post type general name', 'cbc-wine-menu' ),
				'singular_name' => _x( 'Wine by the glass', 'Post type singular name', 'cbc-wine-menu' ),
				'menu_name'     => _x( 'Glasses', 'Admin Menu text', 'cbc-wine-menu' ),
				'add_new_item'  => __( 'Add New Glass', 'cbc-wine-menu' ),
				'edit_item'     => __( 'Edit Glass', 'cbc-wine-menu' ),
				'view_item'     => __( 'View Glass', 'cbc-wine-menu' ),
				'all_items'     => __( 'All Glasses', 'cbc-wine-menu' ),
				'search_items'  => __( 'Search Glasses', 'cbc-wine-menu' ),
				'not_found'     => __( 'No glasses found.', 'cbc-wine-menu' ),
				'menu_position' => 26,
				'menu_icon'     => MenuIcons::get( 'wine-glass.svg', 'dashicons-carrot' ),
			)
		);
	}
}
