<?php

declare( strict_types=1 );

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 */

use Adrenth\Raindrop\Exception\RefreshTokenFailed;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
class Hydro_Raindrop_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Hydro_Raindrop_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Hydro_Raindrop_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-admin.css',
			[],
			$this->version
		);

	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function admin_init() {

		register_setting( 'hydro_api', 'hydro_raindrop_application_id' );
		register_setting( 'hydro_api', 'hydro_raindrop_client_id' );
		register_setting( 'hydro_api', 'hydro_raindrop_client_secret' );
		register_setting( 'hydro_api', 'hydro_raindrop_environment' );

	}

	/**
	 * Add options page.
	 *
	 * @return void
	 */
	public function admin_menu() {

		add_options_page(
			'Hydro Raindrop MFA',
			'Hydro Raindrop MFA',
			'manage_options',
			$this->plugin_name . '-options',
			[
				$this,
				'admin_page',
			]
		);

	}

	/**
	 * Hydro Raindrop environment options have been changed.
	 *
	 * @param mixed $option Option which has been updated.
	 *
	 * @return void
	 */
	public function update_option( $option ) {

		switch ( $option ) {
			case 'hydro_raindrop_application_id':
			case 'hydro_raindrop_client_id':
			case 'hydro_raindrop_client_secret':
			case 'hydro_raindrop_environment':
				$token_storage = new Hydro_Raindrop_TransientTokenStorage();
				$token_storage->unsetAccessToken();

				$authenticate = new Hydro_Raindrop_Authenticate( $this->plugin_name, $this->version );
				$authenticate->unset_cookie();

				delete_option( 'hydro_raindrop_access_token_success' );

				delete_metadata( 'user', 0, 'hydro_id', '', true );
				delete_metadata( 'user', 0, 'hydro_mfa_enabled', '', true );
				delete_metadata( 'user', 0, 'hydro_raindrop_confirmed', '', true );

				break;
		}

	}

	/**
	 * Display the admin page.
	 *
	 * @return void
	 */
	public function admin_page() {

		include __DIR__ . '/../admin/partials/hydro-raindrop-admin-display.php';

	}

	/**
	 * @return bool
	 */
	public function options_are_valid() : bool {

		$token_success = (string) get_option( 'hydro_raindrop_access_token_success', '' );

		if ( empty( $token_success ) && Hydro_Raindrop::has_valid_raindrop_client_options() ) {
			try {
				$client = Hydro_Raindrop::get_raindrop_client();
				$client->getAccessToken();

				update_option( 'hydro_raindrop_access_token_success', 1 );

				return true;
			} catch ( RefreshTokenFailed $e ) {
				return false;
			}
		}

		return true;

	}

}
