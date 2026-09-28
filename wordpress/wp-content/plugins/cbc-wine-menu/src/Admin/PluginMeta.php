<?php
/**
 * Plugin list metadata and “View details” modal for this custom plugin.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Adds author-friendly plugin details outside wordpress.org.
 */
final class PluginMeta {

	private const SLUG = 'cbc-wine-menu';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_filter( 'plugin_row_meta', array( $this, 'add_details_link' ), 10, 2 );
		add_filter( 'plugins_api', array( $this, 'plugin_information' ), 20, 3 );
	}

	/**
	 * Adds a “View details” link next to the version row.
	 *
	 * @param array<int, string> $links Existing meta links.
	 * @param string             $file  Plugin basename.
	 * @return array<int, string>
	 */
	public function add_details_link( array $links, string $file ): array {
		if ( CBC_WINE_MENU_BASENAME !== $file ) {
			return $links;
		}

		$url = add_query_arg(
			array(
				'tab'       => 'plugin-information',
				'plugin'    => self::SLUG,
				'section'   => 'description',
				'TB_iframe' => 'true',
				'width'     => '772',
				'height'    => '780',
			),
			self_admin_url( 'plugin-install.php' )
		);

		$links[] = sprintf(
			'<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
			esc_url( $url ),
			esc_attr__( 'More information about CBC Wine Menu', 'cbc-wine-menu' ),
			esc_attr__( 'CBC Wine Menu', 'cbc-wine-menu' ),
			esc_html__( 'View details' )
		);

		return $links;
	}

	/**
	 * Supplies plugin information for the WordPress details modal.
	 *
	 * @param false|object|array $result Current API result.
	 * @param string             $action API action.
	 * @param object             $args   Request arguments.
	 * @return false|object|array
	 */
	public function plugin_information( $result, string $action, $args ) {
		if ( 'plugin_information' !== $action ) {
			return $result;
		}

		if ( ! is_object( $args ) || empty( $args->slug ) || self::SLUG !== $args->slug ) {
			return $result;
		}

		$author_url = 'https://github.com/vinsgiordi';
		$repo_url   = 'https://github.com/vinsgiordi/cbc-wine-menu';

		$description = '<p>' . esc_html__(
			'Digital wine menu with QR-based table access and temporary guest sessions for CB Cannavale.',
			'cbc-wine-menu'
		) . '</p>';
		$description .= '<p>' . esc_html__(
			'Restaurant staff manage wines, glasses, drinks and beers from WordPress Admin. Guests scan a permanent table QR code, receive a 30-minute session, and browse the mobile-first menu in Italian or English.',
			'cbc-wine-menu'
		) . '</p>';

		$installation  = '<ol>';
		$installation .= '<li>' . esc_html__( 'Copy the plugin folder into wp-content/plugins/.', 'cbc-wine-menu' ) . '</li>';
		$installation .= '<li>' . esc_html__( 'Activate CBC Wine Menu from Plugins.', 'cbc-wine-menu' ) . '</li>';
		$installation .= '<li>' . esc_html__( 'Add or import wines, then configure footer and restaurant social links under Settings → CBC Wine Menu.', 'cbc-wine-menu' ) . '</li>';
		$installation .= '</ol>';

		return (object) array(
			'name'           => __( 'CBC Wine Menu', 'cbc-wine-menu' ),
			'slug'           => self::SLUG,
			'version'        => CBC_WINE_MENU_VERSION,
			'author'         => '<a href="' . esc_url( $author_url ) . '">Vincenzo Giordano</a>',
			'author_profile' => $author_url,
			'homepage'       => $repo_url,
			'requires'       => '6.0',
			'requires_php'   => '8.0',
			'tested'         => '6.7',
			'download_link'  => '',
			'sections'       => array(
				'description'  => $description,
				'installation' => $installation,
			),
		);
	}
}
