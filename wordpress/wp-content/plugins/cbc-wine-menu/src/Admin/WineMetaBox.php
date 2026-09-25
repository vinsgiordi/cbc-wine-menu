<?php
/**
 * Wine metadata definition and admin meta box.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

use CBCWineMenu\PostTypes\Wine;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Stores and edits wine-specific fields as post meta.
 */
final class WineMetaBox {

	public const NONCE_ACTION = 'cbc_wine_menu_save_wine_meta';
	public const NONCE_NAME   = 'cbc_wine_menu_wine_meta_nonce';

	/**
	 * Meta keys used by the wine content model.
	 *
	 * @return array<string, array{label: string, type: string}>
	 */
	public static function fields(): array {
		return array(
			'winery'             => array(
				'label' => __( 'Winery', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'vintage'            => array(
				'label' => __( 'Vintage', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'region'             => array(
				'label' => __( 'Region', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'country'            => array(
				'label' => __( 'Country', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'grape_variety'      => array(
				'label' => __( 'Grape variety', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'appellation'        => array(
				'label' => __( 'Appellation', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'alcohol_percentage' => array(
				'label' => __( 'Alcohol percentage', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'bottle_size'        => array(
				'label' => __( 'Bottle size', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'bottle_price'       => array(
				'label' => __( 'Bottle price', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'glass_price'        => array(
				'label' => __( 'Glass price', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'is_available'       => array(
				'label' => __( 'Available', 'cbc-wine-menu' ),
				'type'  => 'checkbox',
			),
			'display_order'      => array(
				'label' => __( 'Display order', 'cbc-wine-menu' ),
				'type'  => 'number',
			),
		);
	}

	/**
	 * Builds the full meta key for a field.
	 *
	 * @param string $field Field slug.
	 * @return string
	 */
	public static function meta_key( string $field ): string {
		return '_cbc_wine_' . $field;
	}

	/**
	 * Hooks meta box UI and save logic.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post_' . Wine::POST_TYPE, array( $this, 'save' ), 10, 2 );
	}

	/**
	 * Registers the wine details meta box.
	 *
	 * @return void
	 */
	public function add_meta_box(): void {
		add_meta_box(
			'cbc_wine_menu_details',
			__( 'Wine details', 'cbc-wine-menu' ),
			array( $this, 'render' ),
			Wine::POST_TYPE,
			'normal',
			'high'
		);
	}

	/**
	 * Renders the meta box fields.
	 *
	 * @param \WP_Post $post Current post.
	 * @return void
	 */
	public function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		echo '<table class="form-table" role="presentation"><tbody>';

		foreach ( self::fields() as $field => $config ) {
			$key   = self::meta_key( $field );
			$value = get_post_meta( $post->ID, $key, true );
			$id    = 'cbc_wine_' . $field;

			echo '<tr>';
			echo '<th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $config['label'] ) . '</label></th>';
			echo '<td>';

			if ( 'checkbox' === $config['type'] ) {
				$checked = ( '' === $value || '1' === (string) $value );
				printf(
					'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>',
					esc_attr( $id ),
					esc_attr( $key ),
					checked( $checked, true, false ),
					esc_html__( 'Show this wine on the menu', 'cbc-wine-menu' )
				);
			} elseif ( 'number' === $config['type'] ) {
				printf(
					'<input type="number" class="small-text" id="%1$s" name="%2$s" value="%3$s" min="0" step="1" />',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( is_scalar( $value ) ? (string) $value : '0' )
				);
			} else {
				printf(
					'<input type="text" class="regular-text" id="%1$s" name="%2$s" value="%3$s" />',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_attr( is_scalar( $value ) ? (string) $value : '' )
				);
			}

			echo '</td></tr>';
		}

		echo '</tbody></table>';
	}

	/**
	 * Saves wine meta after capability and nonce checks.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public function save( int $post_id, \WP_Post $post ): void {
		if ( ! isset( $_POST[ self::NONCE_NAME ] ) ) {
			return;
		}

		$nonce = sanitize_text_field( wp_unslash( (string) $_POST[ self::NONCE_NAME ] ) );

		if ( ! wp_verify_nonce( $nonce, self::NONCE_ACTION ) ) {
			return;
		}

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( Wine::POST_TYPE !== $post->post_type ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::fields() as $field => $config ) {
			$key = self::meta_key( $field );

			if ( 'checkbox' === $config['type'] ) {
				$available = isset( $_POST[ $key ] ) ? '1' : '0';
				update_post_meta( $post_id, $key, $available );
				continue;
			}

			if ( ! isset( $_POST[ $key ] ) ) {
				continue;
			}

			$raw = wp_unslash( (string) $_POST[ $key ] );

			if ( 'number' === $config['type'] ) {
				update_post_meta( $post_id, $key, (string) absint( $raw ) );
				continue;
			}

			update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
		}
	}
}
