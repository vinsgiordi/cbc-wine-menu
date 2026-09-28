<?php
/**
 * Main plugin bootstrap.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

use CBCWineMenu\Admin\WineMetaBox;
use CBCWineMenu\PostTypes\Wine;
use CBCWineMenu\Sessions\SessionSchema;
use CBCWineMenu\Setup\DemoContent;
use CBCWineMenu\Tables\TableRoutes;
use CBCWineMenu\Taxonomies\WineCategory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Coordinates plugin services and WordPress hooks.
 */
final class Plugin {

	/**
	 * Registers hooks for the current request.
	 *
	 * @return void
	 */
	public function run(): void {
		$this->load_textdomain();

		( new Wine() )->register_hooks();
		( new WineCategory() )->register_hooks();
		( new WineMetaBox() )->register_hooks();
		( new TableRoutes() )->register_hooks();

		add_action( 'init', array( $this, 'maybe_upgrade_schema' ), 5 );
		add_action( 'init', array( DemoContent::class, 'maybe_seed' ), 20 );
	}

	/**
	 * Ensures the sessions table and rewrite rules exist after updates.
	 *
	 * @return void
	 */
	public function maybe_upgrade_schema(): void {
		$version = '1';

		if ( get_option( 'cbc_wine_menu_db_version' ) === $version ) {
			return;
		}

		SessionSchema::create_table();
		TableRoutes::register_rewrites();
		flush_rewrite_rules( false );
		update_option( 'cbc_wine_menu_db_version', $version, false );
	}

	/**
	 * Loads translations for the plugin text domain.
	 *
	 * @return void
	 */
	private function load_textdomain(): void {
		load_plugin_textdomain(
			'cbc-wine-menu',
			false,
			dirname( CBC_WINE_MENU_BASENAME ) . '/languages'
		);
	}
}
