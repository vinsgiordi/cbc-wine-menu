<?php
/**
 * Plugin activation routines.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

use CBCWineMenu\PostTypes\Wine;
use CBCWineMenu\Sessions\SessionSchema;
use CBCWineMenu\Setup\DemoContent;
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
		WineCategory::register();
		SessionSchema::create_table();
		TableRoutes::register_rewrites();
		DemoContent::maybe_seed();

		update_option( 'cbc_wine_menu_db_version', '1', false );
		flush_rewrite_rules();
	}
}
