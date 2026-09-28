<?php
/**
 * Shared plugin settings used by tables and sessions.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Default configuration values for the first release.
 */
final class Config {

	public const TABLE_COUNT = 20;

	public const SESSION_DURATION_MINUTES = 30;

	public const SESSION_COOKIE_NAME = 'cbc_wine_session';

	public const MENU_REWRITE_SLUG = 'vini';

	public const TABLE_REWRITE_SLUG = 'vini/tavolo';

	/**
	 * Designer credit shown in the public menu footer (not editable in admin).
	 */
	public const DESIGNER_FIRST_NAME = 'Vincenzo';

	public const DESIGNER_LAST_NAME = 'Giordano';

	/** Instagram username without @. */
	public const DESIGNER_INSTAGRAM = 'vinsgiordi';

	/**
	 * Full designer display name.
	 *
	 * @return string
	 */
	public static function designer_full_name(): string {
		return trim( self::DESIGNER_FIRST_NAME . ' ' . self::DESIGNER_LAST_NAME );
	}

	/**
	 * Absolute Instagram profile URL for the designer, or empty if unset.
	 *
	 * @return string
	 */
	public static function designer_instagram_url(): string {
		$username = ltrim( self::DESIGNER_INSTAGRAM, '@' );

		if ( '' === $username ) {
			return '';
		}

		return 'https://instagram.com/' . rawurlencode( $username );
	}
}
