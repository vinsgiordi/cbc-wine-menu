<?php
/**
 * Seeds glasses, drinks and beers from the restaurant catalog.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Setup;

use CBCWineMenu\Admin\ExtraItemMetaBox;
use CBCWineMenu\PostTypes\Beer;
use CBCWineMenu\PostTypes\Drink;
use CBCWineMenu\PostTypes\WineGlass;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * One-time import of extra menu sections.
 */
final class ExtrasImporter {

	private const OPTION_KEY = 'cbc_wine_menu_extras_imported_v1';

	/**
	 * @param bool $force Force re-import.
	 * @return int
	 */
	public static function maybe_import( bool $force = false ): int {
		if ( ! $force && get_option( self::OPTION_KEY ) ) {
			return 0;
		}

		$count = 0;
		$count += self::import_type( WineGlass::POST_TYPE, self::glasses() );
		$count += self::import_type( Drink::POST_TYPE, self::drinks() );
		$count += self::import_type( Beer::POST_TYPE, self::beers() );

		update_option( self::OPTION_KEY, '1', false );

		return $count;
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private static function glasses(): array {
		return array(
			array(
				'title' => 'Calice vino bianco',
				'meta'  => array(
					'price'          => '6.00',
					'description_it' => 'Calice di vino bianco',
					'description_en' => 'Glass of white wine',
					'display_order'  => '10',
				),
			),
			array(
				'title' => 'Calice vino rosso',
				'meta'  => array(
					'price'          => '6.00',
					'description_it' => 'Calice di vino rosso',
					'description_en' => 'Glass of red wine',
					'display_order'  => '20',
				),
			),
			array(
				'title' => 'Bollicina',
				'meta'  => array(
					'price'          => '6.00',
					'description_it' => 'Calice di bollicina',
					'description_en' => 'Glass of sparkling wine',
					'display_order'  => '30',
				),
			),
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private static function drinks(): array {
		return array(
			array(
				'title' => 'Aperol Spritz',
				'meta'  => array(
					'price'          => '8.00',
					'display_order'  => '10',
				),
			),
			array(
				'title' => 'Campari Spritz',
				'meta'  => array(
					'price'          => '8.00',
					'display_order'  => '20',
				),
			),
			array(
				'title' => 'Hugo Spritz',
				'meta'  => array(
					'price'          => '8.00',
					'display_order'  => '30',
				),
			),
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	private static function beers(): array {
		return array(
			array(
				'title' => 'Bionda lager',
				'meta'  => array(
					'price'          => '8.00',
					'size'           => '35,5 cl',
					'description_it' => 'Colore ambrato. Secco e piacevolmente arrotondato dall’amarezza dei luppoli più pregiati. 5.6% vol.',
					'description_en' => 'Amber colour. Dry and pleasantly rounded by the bitterness of the finest hops. 5.6% vol.',
					'display_order'  => '10',
				),
			),
			array(
				'title' => 'Rossa red ale',
				'meta'  => array(
					'price'          => '8.00',
					'size'           => '35,5 cl',
					'description_it' => 'Riflessi ramati e color rubino. Ricca di sapori di spezie, calda, finale rotondo e persistente. 6,6% vol.',
					'description_en' => 'Copper and ruby reflections. Rich in spicy flavors, warm, round and persistent finish. 6.6% vol.',
					'display_order'  => '20',
				),
			),
			array(
				'title' => 'Ipa India pale ale',
				'meta'  => array(
					'price'          => '8.00',
					'size'           => '35,5 cl',
					'description_it' => 'Rotondità di aromi tropicali e toni resinati, su un corpo delicatamente maltato. 5,8% vol.',
					'description_en' => 'Tropical aromas with resin tones on a delicately malty body. 5.8% vol.',
					'display_order'  => '30',
				),
			),
		);
	}

	/**
	 * @param string                       $post_type Post type.
	 * @param array<int, array<string, mixed>> $items Items.
	 * @return int
	 */
	private static function import_type( string $post_type, array $items ): int {
		$count = 0;

		foreach ( $items as $item ) {
			$title = (string) $item['title'];

			$query = new \WP_Query(
				array(
					'post_type'              => $post_type,
					'title'                  => $title,
					'post_status'            => 'any',
					'posts_per_page'         => 1,
					'fields'                 => 'ids',
					'no_found_rows'          => true,
					'update_post_meta_cache' => false,
					'update_post_term_cache' => false,
				)
			);

			$order   = isset( $item['meta']['display_order'] ) ? (int) $item['meta']['display_order'] : 0;
			$postarr = array(
				'post_title'  => $title,
				'post_status' => 'publish',
				'post_type'   => $post_type,
				'menu_order'  => $order,
			);

			if ( $query->have_posts() ) {
				$postarr['ID'] = (int) $query->posts[0];
				$post_id       = wp_update_post( $postarr, true );
			} else {
				$post_id = wp_insert_post( $postarr, true );
			}

			if ( is_wp_error( $post_id ) || ! $post_id ) {
				continue;
			}

			$meta = is_array( $item['meta'] ) ? $item['meta'] : array();

			if ( ! isset( $meta['is_available'] ) ) {
				$meta['is_available'] = '1';
			}

			foreach ( $meta as $field => $value ) {
				update_post_meta( (int) $post_id, ExtraItemMetaBox::meta_key( (string) $field ), (string) $value );
			}

			++$count;
		}

		return $count;
	}
}
