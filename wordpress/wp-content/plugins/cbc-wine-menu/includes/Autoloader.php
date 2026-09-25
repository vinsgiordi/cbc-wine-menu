<?php
/**
 * Simple PSR-4 style autoloader for the CBCWineMenu namespace.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Maps CBCWineMenu\* classes to files under src/.
 */
final class Autoloader {

	/**
	 * Base directory for class files.
	 *
	 * @var string
	 */
	private static string $base_dir = '';

	/**
	 * Registers the autoloader.
	 *
	 * @param string $base_dir Absolute path to the src directory.
	 * @return void
	 */
	public static function register( string $base_dir ): void {
		self::$base_dir = trailingslashit( $base_dir );

		spl_autoload_register( array( self::class, 'autoload' ) );
	}

	/**
	 * Loads a class file when requested.
	 *
	 * @param string $class Fully qualified class name.
	 * @return void
	 */
	public static function autoload( string $class ): void {
		$prefix = __NAMESPACE__ . '\\';

		if ( 0 !== strpos( $class, $prefix ) ) {
			return;
		}

		$relative = substr( $class, strlen( $prefix ) );
		$relative = str_replace( '\\', DIRECTORY_SEPARATOR, $relative );
		$file     = self::$base_dir . $relative . '.php';

		if ( is_readable( $file ) ) {
			require_once $file;
		}
	}
}
