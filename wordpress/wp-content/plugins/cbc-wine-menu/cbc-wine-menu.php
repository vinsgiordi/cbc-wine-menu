<?php
/**
 * Plugin Name:       CBC Wine Menu
 * Plugin URI:        https://github.com/vinsgiordi/cbc-wine-menu
 * Description:       Digital wine menu with QR-based table access and temporary guest sessions for CBC Cannavale.
 * Version:           0.1.0
 * Requires at least: 6.0
 * Requires PHP:      8.0
 * Author:            Vincenzo Giordano
 * Author URI:        https://github.com/vinsgiordi
 * License:           Proprietary
 * Text Domain:       cbc-wine-menu
 * Domain Path:       /languages
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CBC_WINE_MENU_VERSION', '0.1.0' );
define( 'CBC_WINE_MENU_FILE', __FILE__ );
define( 'CBC_WINE_MENU_PATH', plugin_dir_path( __FILE__ ) );
define( 'CBC_WINE_MENU_URL', plugin_dir_url( __FILE__ ) );
define( 'CBC_WINE_MENU_BASENAME', plugin_basename( __FILE__ ) );

require_once CBC_WINE_MENU_PATH . 'includes/Autoloader.php';

CBCWineMenu\Autoloader::register( CBC_WINE_MENU_PATH . 'src/' );

register_activation_hook( __FILE__, array( CBCWineMenu\Activator::class, 'activate' ) );
register_deactivation_hook( __FILE__, array( CBCWineMenu\Deactivator::class, 'deactivate' ) );

/**
 * Boots the plugin after WordPress is loaded.
 *
 * @return void
 */
function cbc_wine_menu_run(): void {
	$plugin = new CBCWineMenu\Plugin();
	$plugin->run();
}

add_action( 'plugins_loaded', 'cbc_wine_menu_run' );
