<?php
/**
 * Database schema for temporary wine-menu sessions.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Sessions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and upgrades the custom sessions table.
 */
final class SessionSchema {

	public const TABLE_SUFFIX = 'cbc_wine_sessions';

	/**
	 * Returns the full table name including the WordPress prefix.
	 *
	 * @return string
	 */
	public static function table_name(): string {
		global $wpdb;

		return $wpdb->prefix . self::TABLE_SUFFIX;
	}

	/**
	 * Creates the sessions table if it does not exist.
	 *
	 * @return void
	 */
	public static function create_table(): void {
		global $wpdb;

		$table           = self::table_name();
		$charset_collate = $wpdb->get_charset_collate();

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			token varchar(64) NOT NULL,
			table_id varchar(10) NOT NULL,
			created_at datetime NOT NULL,
			expires_at datetime NOT NULL,
			last_access datetime DEFAULT NULL,
			status varchar(20) NOT NULL DEFAULT 'active',
			PRIMARY KEY  (id),
			UNIQUE KEY token (token),
			KEY expires_at (expires_at),
			KEY table_id (table_id),
			KEY status (status)
		) {$charset_collate};";

		dbDelta( $sql );
	}
}
