<?php
/**
 * Guest menu language (Italian default, English optional).
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\PublicFacing;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Resolves and stores the public menu language preference.
 */
final class MenuLanguage {

	public const COOKIE_NAME = 'cbc_wine_menu_lang';

	/**
	 * Returns the active menu language code (it|en).
	 *
	 * @return string
	 */
	public static function current(): string {
		$lang = self::from_request();

		if ( null === $lang ) {
			$lang = self::from_cookie();
		}

		if ( null === $lang ) {
			$lang = 'it';
		}

		return $lang;
	}

	/**
	 * Persists the language choice in a cookie when requested via query string.
	 *
	 * @return void
	 */
	public static function persist_from_request(): void {
		$lang = self::from_request();

		if ( null === $lang ) {
			return;
		}

		setcookie(
			self::COOKIE_NAME,
			$lang,
			array(
				'expires'  => time() + MONTH_IN_SECONDS,
				'path'     => COOKIEPATH ? COOKIEPATH : '/',
				'domain'   => COOKIE_DOMAIN,
				'secure'   => is_ssl(),
				'httponly' => false,
				'samesite' => 'Lax',
			)
		);

		$_COOKIE[ self::COOKIE_NAME ] = $lang;
	}

	/**
	 * Builds a menu URL for the given language.
	 *
	 * @param string $lang Language code.
	 * @return string
	 */
	public static function url_for( string $lang ): string {
		$lang = self::normalize( $lang );

		return add_query_arg(
			'lang',
			$lang,
			home_url( '/vini/' )
		);
	}

	/**
	 * @return string|null
	 */
	private static function from_request(): ?string {
		if ( ! isset( $_GET['lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
			return null;
		}

		return self::normalize( sanitize_text_field( wp_unslash( (string) $_GET['lang'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}

	/**
	 * @return string|null
	 */
	private static function from_cookie(): ?string {
		if ( ! isset( $_COOKIE[ self::COOKIE_NAME ] ) ) {
			return null;
		}

		return self::normalize( sanitize_text_field( wp_unslash( (string) $_COOKIE[ self::COOKIE_NAME ] ) ) );
	}

	/**
	 * @param string $lang Raw language.
	 * @return string|null
	 */
	private static function normalize( string $lang ): ?string {
		$lang = strtolower( trim( $lang ) );

		if ( in_array( $lang, array( 'it', 'en' ), true ) ) {
			return $lang;
		}

		return null;
	}
}
