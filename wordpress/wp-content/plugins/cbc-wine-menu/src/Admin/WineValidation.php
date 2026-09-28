<?php
/**
 * Publish-time validation for wines.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

use CBCWineMenu\PostTypes\Wine;
use CBCWineMenu\Taxonomies\WineCategory;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enforces the minimum required fields when publishing a wine.
 */
final class WineValidation {

	private const NOTICE_KEY = 'cbc_wine_menu_validation_errors';

	/**
	 * Registers validation hooks.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'save_post_' . Wine::POST_TYPE, array( $this, 'validate_published_wine' ), 20, 2 );
		add_action( 'admin_notices', array( $this, 'render_admin_notices' ) );
	}

	/**
	 * Downgrades invalid published wines to draft and stores an admin notice.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return void
	 */
	public function validate_published_wine( int $post_id, \WP_Post $post ): void {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( wp_is_post_revision( $post_id ) ) {
			return;
		}

		if ( 'publish' !== $post->post_status ) {
			return;
		}

		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		$errors = $this->collect_errors( $post_id, $post );

		if ( empty( $errors ) ) {
			return;
		}

		remove_action( 'save_post_' . Wine::POST_TYPE, array( $this, 'validate_published_wine' ), 20 );

		wp_update_post(
			array(
				'ID'          => $post_id,
				'post_status' => 'draft',
			)
		);

		add_action( 'save_post_' . Wine::POST_TYPE, array( $this, 'validate_published_wine' ), 20, 2 );

		set_transient( self::NOTICE_KEY . '_' . get_current_user_id(), $errors, MINUTE_IN_SECONDS * 5 );
	}

	/**
	 * Shows validation errors in wp-admin.
	 *
	 * @return void
	 */
	public function render_admin_notices(): void {
		$key    = self::NOTICE_KEY . '_' . get_current_user_id();
		$errors = get_transient( $key );

		if ( ! is_array( $errors ) || empty( $errors ) ) {
			return;
		}

		delete_transient( $key );

		echo '<div class="notice notice-error is-dismissible"><p>';
		echo esc_html__( 'This wine was saved as a draft because required information is missing:', 'cbc-wine-menu' );
		echo '</p><ul style="list-style:disc;margin-left:1.25rem;">';

		foreach ( $errors as $error ) {
			echo '<li>' . esc_html( (string) $error ) . '</li>';
		}

		echo '</ul></div>';
	}

	/**
	 * Builds the list of validation errors for a wine.
	 *
	 * @param int      $post_id Post ID.
	 * @param \WP_Post $post    Post object.
	 * @return array<int, string>
	 */
	private function collect_errors( int $post_id, \WP_Post $post ): array {
		$errors = array();

		if ( '' === trim( $post->post_title ) ) {
			$errors[] = __( 'Wine name is required.', 'cbc-wine-menu' );
		}

		$terms = wp_get_post_terms( $post_id, WineCategory::TAXONOMY, array( 'fields' => 'ids' ) );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			$errors[] = __( 'At least one wine category is required.', 'cbc-wine-menu' );
		}

		$price = trim( (string) get_post_meta( $post_id, WineMetaBox::meta_key( 'bottle_price' ), true ) );

		if ( '' === $price ) {
			$errors[] = __( 'Bottle price is required.', 'cbc-wine-menu' );
		}

		return $errors;
	}
}
