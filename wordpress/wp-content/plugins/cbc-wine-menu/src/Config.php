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
}
