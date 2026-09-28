<?php
/**
 * Public rewrite routes for table access and the wine menu.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Tables;

use CBCWineMenu\Config;
use CBCWineMenu\Sessions\SessionService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers and handles QR table entry points.
 */
final class TableRoutes {

	/**
	 * @var TableRegistry
	 */
	private TableRegistry $tables;

	/**
	 * @var SessionService
	 */
	private SessionService $sessions;

	/**
	 * @param TableRegistry|null  $tables   Table registry.
	 * @param SessionService|null $sessions Session service.
	 */
	public function __construct( ?TableRegistry $tables = null, ?SessionService $sessions = null ) {
		$this->tables   = $tables ?? new TableRegistry();
		$this->sessions = $sessions ?? new SessionService();
	}

	/**
	 * Hooks rewrite rules and request handling.
	 *
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'init', array( self::class, 'register_rewrites' ) );
		add_filter( 'query_vars', array( $this, 'register_query_vars' ) );
		add_action( 'template_redirect', array( $this, 'handle_request' ) );
	}

	/**
	 * Registers pretty permalinks for table access and the wine menu.
	 *
	 * @return void
	 */
	public static function register_rewrites(): void {
		add_rewrite_rule(
			'^' . Config::TABLE_REWRITE_SLUG . '/([0-9]{1,2})/?$',
			'index.php?cbc_wine_table=$matches[1]',
			'top'
		);

		add_rewrite_rule(
			'^' . Config::MENU_REWRITE_SLUG . '/?$',
			'index.php?cbc_wine_menu=1',
			'top'
		);
	}

	/**
	 * Exposes custom query vars to WordPress.
	 *
	 * @param array<int, string> $vars Existing query vars.
	 * @return array<int, string>
	 */
	public function register_query_vars( array $vars ): array {
		$vars[] = 'cbc_wine_table';
		$vars[] = 'cbc_wine_menu';
		return $vars;
	}

	/**
	 * Handles table entry and protected menu requests.
	 *
	 * @return void
	 */
	public function handle_request(): void {
		$table_raw = get_query_var( 'cbc_wine_table' );

		if ( is_string( $table_raw ) && '' !== $table_raw ) {
			$this->handle_table_access( $table_raw );
			return;
		}

		$menu_flag = get_query_var( 'cbc_wine_menu' );

		if ( '1' === (string) $menu_flag ) {
			$this->handle_menu_access();
		}
	}

	/**
	 * Validates a table QR hit, starts a session, and redirects to the menu.
	 *
	 * @param string $table_raw Raw table ID from the URL.
	 * @return void
	 */
	private function handle_table_access( string $table_raw ): void {
		$table_id = $this->tables->normalize( $table_raw );

		if ( null === $table_id ) {
			status_header( 404 );
			nocache_headers();
			$this->render_template(
				'table-invalid.php',
				array(
					'table_raw' => $table_raw,
				)
			);
			exit;
		}

		$session = $this->sessions->start_for_table( $table_id );

		if ( null === $session ) {
			status_header( 500 );
			nocache_headers();
			wp_die(
				esc_html__( 'Unable to start a wine menu session. Please try again.', 'cbc-wine-menu' ),
				esc_html__( 'Session error', 'cbc-wine-menu' ),
				array( 'response' => 500 )
			);
		}

		nocache_headers();
		wp_safe_redirect( home_url( '/' . Config::MENU_REWRITE_SLUG . '/' ), 302 );
		exit;
	}

	/**
	 * Shows the wine menu when a valid session cookie is present.
	 *
	 * @return void
	 */
	private function handle_menu_access(): void {
		$session = $this->sessions->get_valid_session();

		nocache_headers();

		if ( null === $session ) {
			status_header( 403 );
			$this->render_template( 'session-expired.php' );
			exit;
		}

		status_header( 200 );

		$wines = get_posts(
			array(
				'post_type'      => 'wine',
				'post_status'    => 'publish',
				'posts_per_page' => 50,
				'orderby'        => array(
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				),
			)
		);

		$this->render_template(
			'menu.php',
			array(
				'session' => $session,
				'wines'   => $wines,
			)
		);
		exit;
	}

	/**
	 * Loads a plugin template file.
	 *
	 * @param string               $template Template filename inside templates/.
	 * @param array<string, mixed> $vars     Variables extracted into the template.
	 * @return void
	 */
	private function render_template( string $template, array $vars = array() ): void {
		$path = CBC_WINE_MENU_PATH . 'templates/' . $template;

		if ( ! is_readable( $path ) ) {
			wp_die( esc_html__( 'Template not found.', 'cbc-wine-menu' ) );
		}

		// phpcs:ignore WordPress.PHP.DontExtract.extract_extract -- scoped template variables only.
		extract( $vars, EXTR_SKIP );

		include $path;
	}
}
