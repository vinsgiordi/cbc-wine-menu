<?php
/**
 * Persistence for temporary access sessions.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Sessions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Reads and writes session rows safely via $wpdb.
 */
final class SessionRepository {

	/**
	 * Inserts a new active session.
	 *
	 * @param string $token      Cryptographically secure token.
	 * @param string $table_id   Normalized table identifier.
	 * @param string $created_at MySQL datetime.
	 * @param string $expires_at MySQL datetime.
	 * @return bool
	 */
	public function create( string $token, string $table_id, string $created_at, string $expires_at ): bool {
		global $wpdb;

		$result = $wpdb->insert(
			SessionSchema::table_name(),
			array(
				'token'       => $token,
				'table_id'    => $table_id,
				'created_at'  => $created_at,
				'expires_at'  => $expires_at,
				'last_access' => $created_at,
				'status'      => 'active',
			),
			array( '%s', '%s', '%s', '%s', '%s', '%s' )
		);

		return false !== $result;
	}

	/**
	 * Finds an active, non-expired session by token.
	 *
	 * @param string $token Session token.
	 * @return array<string, mixed>|null
	 */
	public function find_active_by_token( string $token ): ?array {
		global $wpdb;

		$table = SessionSchema::table_name();
		$now   = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
		$row = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT * FROM {$table} WHERE token = %s AND status = %s AND expires_at > %s LIMIT 1",
				$token,
				'active',
				$now
			),
			ARRAY_A
		);

		return is_array( $row ) ? $row : null;
	}

	/**
	 * Updates the last access timestamp for a session.
	 *
	 * @param int $session_id Session row ID.
	 * @return void
	 */
	public function touch( int $session_id ): void {
		global $wpdb;

		$wpdb->update(
			SessionSchema::table_name(),
			array(
				'last_access' => current_time( 'mysql', true ),
			),
			array(
				'id' => $session_id,
			),
			array( '%s' ),
			array( '%d' )
		);
	}

	/**
	 * Marks a session as expired.
	 *
	 * @param int $session_id Session row ID.
	 * @return void
	 */
	public function mark_expired( int $session_id ): void {
		global $wpdb;

		$wpdb->update(
			SessionSchema::table_name(),
			array(
				'status' => 'expired',
			),
			array(
				'id' => $session_id,
			),
			array( '%s' ),
			array( '%d' )
		);
	}

	/**
	 * Deletes expired sessions to keep the table small.
	 *
	 * @return int Number of deleted rows.
	 */
	public function cleanup_expired(): int {
		global $wpdb;

		$table = SessionSchema::table_name();
		$now   = current_time( 'mysql', true );

		// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table name is internal.
		$deleted = $wpdb->query(
			$wpdb->prepare(
				"DELETE FROM {$table} WHERE expires_at <= %s OR status = %s",
				$now,
				'expired'
			)
		);

		return is_int( $deleted ) ? $deleted : 0;
	}
}
