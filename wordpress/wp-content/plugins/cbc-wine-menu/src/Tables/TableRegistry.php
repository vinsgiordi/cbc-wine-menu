<?php
/**
 * Restaurant table identifiers.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Tables;

use CBCWineMenu\Config;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Validates and normalizes table IDs for QR entry points.
 */
final class TableRegistry {

	/**
	 * Returns the configured number of tables.
	 *
	 * @return int
	 */
	public function get_table_count(): int {
		/**
		 * Filters the number of restaurant tables supported by QR access.
		 *
		 * @param int $count Table count.
		 */
		$count = (int) apply_filters( 'cbc_wine_menu_table_count', Config::TABLE_COUNT );

		return max( 1, $count );
	}

	/**
	 * Normalizes a raw table identifier to a zero-padded value such as 07.
	 *
	 * @param string $raw Raw table ID from the URL.
	 * @return string|null
	 */
	public function normalize( string $raw ): ?string {
		$raw = trim( $raw );

		if ( ! preg_match( '/^\d{1,2}$/', $raw ) ) {
			return null;
		}

		$number = (int) $raw;

		if ( $number < 1 || $number > $this->get_table_count() ) {
			return null;
		}

		return sprintf( '%02d', $number );
	}

	/**
	 * Checks whether a raw table identifier is valid.
	 *
	 * @param string $raw Raw table ID.
	 * @return bool
	 */
	public function is_valid( string $raw ): bool {
		return null !== $this->normalize( $raw );
	}
}
