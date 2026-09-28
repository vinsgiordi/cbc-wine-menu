<?php
/**
 * Plugin activation routines.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

use CBCWineMenu\PostTypes\Beer;
use CBCWineMenu\PostTypes\Drink;
use CBCWineMenu\PostTypes\Wine;
use CBCWineMenu\PostTypes\WineGlass;
use CBCWineMenu\Sessions\SessionSchema;
use CBCWineMenu\Setup\CatalogImporter;
use CBCWineMenu\Setup\DemoContent;
use CBCWineMenu\Setup\ExtrasImporter;
use CBCWineMenu\Tables\TableRoutes;
use CBCWineMenu\Taxonomies\WineCategory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Runs once when the plugin is activated.
 */
final class Activator {

	/**
	 * Activation callback.
	 *
	 * @return void
	 */
	public static function activate(): void {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		Wine::register();
		WineGlass::register();
		Drink::register();
		Beer::register();
		WineCategory::register();
		SessionSchema::create_table();
		TableRoutes::register_rewrites();
		CatalogImporter::maybe_import();
		ExtrasImporter::maybe_import();
		DemoContent::maybe_seed();

		update_option( 'cbc_wine_menu_db_version', '2', false );
		flush_rewrite_rules();
	}
}
