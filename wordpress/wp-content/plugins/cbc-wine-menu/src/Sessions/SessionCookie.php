<?php
/**
 * Browser cookie helpers for wine-menu sessions.
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
 * Sets and clears the HttpOnly session cookie.
 */
final class SessionCookie {

	/**
	 * Stores the session token in a browser cookie.
	 *
	 * @param string $token      Session token.
	 * @param int    $expires_at Unix timestamp (UTC).
	 * @return void
	 */
	public function set( string $token, int $expires_at ): void {
		$secure   = is_ssl();
		$httponly = true;
		$samesite = 'Lax';

		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				Config::SESSION_COOKIE_NAME,
				$token,
				array(
					'expires'  => $expires_at,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'secure'   => $secure,
					'httponly' => $httponly,
					'samesite' => $samesite,
				)
			);
		} else {
			setcookie(
				Config::SESSION_COOKIE_NAME,
				$token,
				$expires_at,
				( COOKIEPATH ? COOKIEPATH : '/' ) . '; samesite=' . $samesite,
				COOKIE_DOMAIN,
				$secure,
				$httponly
			);
		}

		$_COOKIE[ Config::SESSION_COOKIE_NAME ] = $token;
	}

	/**
	 * Reads the session token from the request cookie.
	 *
	 * @return string
	 */
	public function get(): string {
		if ( ! isset( $_COOKIE[ Config::SESSION_COOKIE_NAME ] ) ) {
			return '';
		}

		return sanitize_text_field( wp_unslash( (string) $_COOKIE[ Config::SESSION_COOKIE_NAME ] ) );
	}

	/**
	 * Clears the session cookie.
	 *
	 * @return void
	 */
	public function clear(): void {
		if ( PHP_VERSION_ID >= 70300 ) {
			setcookie(
				Config::SESSION_COOKIE_NAME,
				'',
				array(
					'expires'  => time() - YEAR_IN_SECONDS,
					'path'     => COOKIEPATH ? COOKIEPATH : '/',
					'domain'   => COOKIE_DOMAIN,
					'secure'   => is_ssl(),
					'httponly' => true,
					'samesite' => 'Lax',
				)
			);
		} else {
			setcookie(
				Config::SESSION_COOKIE_NAME,
				'',
				time() - YEAR_IN_SECONDS,
				COOKIEPATH ? COOKIEPATH : '/',
				COOKIE_DOMAIN,
				is_ssl(),
				true
			);
		}

		unset( $_COOKIE[ Config::SESSION_COOKIE_NAME ] );
	}
}
