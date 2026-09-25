<?php
/**
 * Plugin activation routines.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

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

		// Placeholder for future rewrite rules, DB tables, and default options.
		flush_rewrite_rules();
	}
}
