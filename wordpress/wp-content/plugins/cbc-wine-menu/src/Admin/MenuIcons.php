<?php
/**
 * Admin menu icons loaded from local SVG files.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds WordPress admin menu_icon values from plugin SVG assets.
 */
final class MenuIcons {

	/**
	 * Returns a data-URI menu icon for the given SVG filename.
	 *
	 * @param string $filename SVG file name inside assets/icons/.
	 * @param string $fallback Dashicon class used when the file is missing.
	 * @return string
	 */
	public static function get( string $filename, string $fallback = 'dashicons-admin-post' ): string {
		$path = CBC_WINE_MENU_PATH . 'assets/icons/' . ltrim( $filename, '/' );

		if ( ! is_readable( $path ) ) {
			return $fallback;
		}

		$svg = file_get_contents( $path );

		if ( false === $svg || '' === trim( $svg ) ) {
			return $fallback;
		}

		// WordPress admin expects muted gray icons.
		$svg = preg_replace( '/fill="#000000"/i', 'fill="#a7aaad"', $svg ) ?? $svg;
		$svg = preg_replace( '/fill="#000"/i', 'fill="#a7aaad"', $svg ) ?? $svg;

		if ( ! preg_match( '/<svg[^>]*\bfill=/i', $svg ) ) {
			$svg = preg_replace( '/<svg\b/i', '<svg fill="#a7aaad"', $svg, 1 ) ?? $svg;
		}

		return 'data:image/svg+xml;base64,' . base64_encode( $svg );
	}
}
