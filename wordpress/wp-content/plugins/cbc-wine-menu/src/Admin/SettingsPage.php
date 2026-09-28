<?php
/**
 * Plugin settings for footer and restaurant social links.
 *
 * @package CBCWineMenu
 */

declare(strict_types=1);

namespace CBCWineMenu\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Settings page under WordPress admin.
 */
final class SettingsPage {

	public const OPTION_KEY = 'cbc_wine_menu_settings';

	/**
	 * @return void
	 */
	public function register_hooks(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
	}

	/**
	 * Default settings.
	 *
	 * @return array<string, string>
	 */
	public static function defaults(): array {
		return array(
			'footer_text_it' => 'Dove la carne di lusso non è un lusso.',
			'footer_text_en' => 'Where luxury meat is not a luxury.',
			'facebook_url'   => '',
			'instagram_url'  => '',
			'cover_charge'   => '2',
		);
	}

	/**
	 * @return array<string, string>
	 */
	public static function get(): array {
		$stored = get_option( self::OPTION_KEY, array() );

		if ( ! is_array( $stored ) ) {
			$stored = array();
		}

		return array_merge( self::defaults(), $stored );
	}

	/**
	 * @return void
	 */
	public function register_menu(): void {
		add_options_page(
			__( 'CBC Wine Menu Settings', 'cbc-wine-menu' ),
			__( 'CBC Wine Menu', 'cbc-wine-menu' ),
			'manage_options',
			'cbc-wine-menu-settings',
			array( $this, 'render_page' )
		);
	}

	/**
	 * @return void
	 */
	public function register_settings(): void {
		register_setting(
			'cbc_wine_menu_settings_group',
			self::OPTION_KEY,
			array(
				'type'              => 'array',
				'sanitize_callback' => array( $this, 'sanitize' ),
				'default'           => self::defaults(),
			)
		);
	}

	/**
	 * @param mixed $input Raw input.
	 * @return array<string, string>
	 */
	public function sanitize( $input ): array {
		$input    = is_array( $input ) ? $input : array();
		$defaults = self::defaults();
		$output   = array();

		$output['footer_text_it'] = sanitize_text_field( (string) ( $input['footer_text_it'] ?? $defaults['footer_text_it'] ) );
		$output['footer_text_en'] = sanitize_text_field( (string) ( $input['footer_text_en'] ?? $defaults['footer_text_en'] ) );
		$output['facebook_url']   = esc_url_raw( (string) ( $input['facebook_url'] ?? '' ) );
		$output['instagram_url']  = esc_url_raw( (string) ( $input['instagram_url'] ?? '' ) );
		$output['cover_charge']   = sanitize_text_field( (string) ( $input['cover_charge'] ?? '' ) );

		return $output;
	}

	/**
	 * @return void
	 */
	public function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$settings = self::get();
		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'CBC Wine Menu Settings', 'cbc-wine-menu' ); ?></h1>
			<form method="post" action="options.php">
				<?php settings_fields( 'cbc_wine_menu_settings_group' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="footer_text_it"><?php echo esc_html__( 'Footer text (Italian)', 'cbc-wine-menu' ); ?></label></th>
						<td><input type="text" class="large-text" id="footer_text_it" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[footer_text_it]" value="<?php echo esc_attr( $settings['footer_text_it'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="footer_text_en"><?php echo esc_html__( 'Footer text (English)', 'cbc-wine-menu' ); ?></label></th>
						<td><input type="text" class="large-text" id="footer_text_en" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[footer_text_en]" value="<?php echo esc_attr( $settings['footer_text_en'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="facebook_url"><?php echo esc_html__( 'Facebook URL', 'cbc-wine-menu' ); ?></label></th>
						<td><input type="url" class="regular-text" id="facebook_url" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[facebook_url]" value="<?php echo esc_attr( $settings['facebook_url'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="instagram_url"><?php echo esc_html__( 'Instagram URL', 'cbc-wine-menu' ); ?></label></th>
						<td><input type="url" class="regular-text" id="instagram_url" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[instagram_url]" value="<?php echo esc_attr( $settings['instagram_url'] ); ?>" /></td>
					</tr>
					<tr>
						<th scope="row"><label for="cover_charge"><?php echo esc_html__( 'Cover charge (€)', 'cbc-wine-menu' ); ?></label></th>
						<td><input type="text" class="small-text" id="cover_charge" name="<?php echo esc_attr( self::OPTION_KEY ); ?>[cover_charge]" value="<?php echo esc_attr( $settings['cover_charge'] ); ?>" /></td>
					</tr>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}
}
