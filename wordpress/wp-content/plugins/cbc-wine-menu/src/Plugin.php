<?php
/**
 * Main plugin bootstrap.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

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
