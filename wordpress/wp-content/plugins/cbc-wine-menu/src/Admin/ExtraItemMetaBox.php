<?php
/**
 * Shared meta box for glasses, drinks and beers.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Simple price/description fields for extra menu sections.
 */
final class ExtraItemMetaBox {

	public const NONCE_ACTION = 'cbc_wine_menu_save_extra_item';
	public const NONCE_NAME   = 'cbc_wine_menu_extra_item_nonce';

	/**
	 * @var array<int, string>
	 */
	private array $post_types;

	/**
	 * @param array<int, string> $post_types Post types that use this meta box.
	 */
	public function __construct( array $post_types ) {
		$this->post_types = $post_types;
	}

	/**
	 * @return array<string, array{label: string, type: string}>
	 */
	public static function fields(): array {
		return array(
			'price'            => array(
				'label' => __( 'Price', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'size'             => array(
				'label' => __( 'Size / format', 'cbc-wine-menu' ),
				'type'  => 'text',
			),
			'description_it'   => array(
				'label' => __( 'Description (Italian)', 'cbc-wine-menu' ),
				'type'  => 'textarea',
			),
			'description_en'   => array(
				'label' => __( 'Description (English)', 'cbc-wine-menu' ),
				'type'  => 'textarea',
			),
			'is_available'     => array(
				'label' => __( 'Available', 'cbc-wine-menu' ),
				'type'  => 'checkbox',
			),
			'display_order'    => array(
				'label' => __( 'Display order', 'cbc-wine-menu' ),
				'type'  => 'number',
			),
		);
	}

	/**
	 * @param string $field Field slug.
	 * @return string
	 */
	public static function meta_key( string $field ): string {
		return '_cbc_item_' . $field;
	}

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );

		foreach ( $this->post_types as $post_type ) {
			add_action( 'save_post_' . $post_type, array( $this, 'save' ), 10, 2 );
		}
	}

	/**
	 * @return void
	 */
	public function add_meta_box(): void {
		foreach ( $this->post_types as $post_type ) {
			add_meta_box(
				'cbc_wine_menu_extra_item',
				__( 'Item details', 'cbc-wine-menu' ),
				array( $this, 'render' ),
				$post_type,
				'normal',
				'high'
			);
		}
	}

	/**
	 * @param \WP_Post $post Post.
	 * @return void
	 */
	public function render( \WP_Post $post ): void {
		wp_nonce_field( self::NONCE_ACTION, self::NONCE_NAME );

		echo '<table class="form-table" role="presentation"><tbody>';

		foreach ( self::fields() as $field => $config ) {
			$key   = self::meta_key( $field );
			$value = get_post_meta( $post->ID, $key, true );
			$id    = 'cbc_item_' . $field;

			echo '<tr><th scope="row"><label for="' . esc_attr( $id ) . '">' . esc_html( $config['label'] ) . '</label></th><td>';

			if ( 'checkbox' === $config['type'] ) {
				$checked = ( '' === $value || '1' === (string) $value );
				printf(
					'<label><input type="checkbox" id="%1$s" name="%2$s" value="1" %3$s /> %4$s</label>',
					esc_attr( $id ),
					esc_attr( $key ),
					checked( $checked, true, false ),
					esc_html__( 'Show this item on the menu', 'cbc-wine-menu' )
				);
			} elseif ( 'textarea' === $config['type'] ) {
				printf(
					'<textarea class="large-text" rows="4" id="%1$s" name="%2$s">%3$s</textarea>',
					esc_attr( $id ),
					esc_attr( $key ),
					esc_textarea( is_scalar( $value ) ? (string) $value : '' )
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
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post.
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

		if ( ! in_array( $post->post_type, $this->post_types, true ) ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		foreach ( self::fields() as $field => $config ) {
			$key = self::meta_key( $field );

			if ( 'checkbox' === $config['type'] ) {
				update_post_meta( $post_id, $key, isset( $_POST[ $key ] ) ? '1' : '0' );
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

			if ( 'textarea' === $config['type'] ) {
				update_post_meta( $post_id, $key, sanitize_textarea_field( $raw ) );
				continue;
			}

			update_post_meta( $post_id, $key, sanitize_text_field( $raw ) );
		}
	}
}
