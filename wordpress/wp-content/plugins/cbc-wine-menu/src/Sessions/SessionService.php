<?php
/**
 * Temporary access session business logic.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Sessions;

use CBCWineMenu\Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Creates and validates guest sessions for table QR access.
 */
final class SessionService {

	/**
	 * @var SessionRepository
	 */
	private SessionRepository $repository;

	/**
	 * @var SessionCookie
	 */
	private SessionCookie $cookie;

	/**
	 * @param SessionRepository|null $repository Repository instance.
	 * @param SessionCookie|null     $cookie     Cookie helper.
	 */
	public function __construct( ?SessionRepository $repository = null, ?SessionCookie $cookie = null ) {
		$this->repository = $repository ?? new SessionRepository();
		$this->cookie     = $cookie ?? new SessionCookie();
	}

	/**
	 * Creates a new session for a validated table and sets the browser cookie.
	 *
	 * @param string $table_id Normalized table identifier (e.g. 07).
	 * @return array{token: string, table_id: string, expires_at: int}|null
	 */
	public function start_for_table( string $table_id ): ?array {
		$this->repository->cleanup_expired();

		try {
			$token = bin2hex( random_bytes( 32 ) );
		} catch ( \Exception $exception ) {
			return null;
		}

		$created_ts = time();
		$expires_ts = $created_ts + ( Config::SESSION_DURATION_MINUTES * MINUTE_IN_SECONDS );

		$created_at = gmdate( 'Y-m-d H:i:s', $created_ts );
		$expires_at = gmdate( 'Y-m-d H:i:s', $expires_ts );

		$created = $this->repository->create( $token, $table_id, $created_at, $expires_at );

		if ( ! $created ) {
			return null;
		}

		$this->cookie->set( $token, $expires_ts );

		return array(
			'token'      => $token,
			'table_id'   => $table_id,
			'expires_at' => $expires_ts,
		);
	}

	/**
	 * Validates the current browser session.
	 *
	 * @return array<string, mixed>|null
	 */
	public function get_valid_session(): ?array {
		$token = $this->cookie->get();

		if ( '' === $token || ! preg_match( '/^[a-f0-9]{64}$/', $token ) ) {
			return null;
		}

		$session = $this->repository->find_active_by_token( $token );

		if ( null === $session ) {
			$this->cookie->clear();
			return null;
		}

		$this->repository->touch( (int) $session['id'] );

		return $session;
	}

	/**
	 * Clears the browser session cookie.
	 *
	 * @return void
	 */
	public function clear_browser_session(): void {
		$this->cookie->clear();
	}
}
