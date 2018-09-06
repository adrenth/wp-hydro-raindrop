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

		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_APPLICATION_ID );
		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_CLIENT_ID );
		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET );
		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_ENVIRONMENT );
		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE );
		register_setting( 'hydro_api', Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE );

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
			case Hydro_Raindrop_Helper::OPTION_APPLICATION_ID:
			case Hydro_Raindrop_Helper::OPTION_CLIENT_ID:
			case Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET:
			case Hydro_Raindrop_Helper::OPTION_ENVIRONMENT:
				$token_storage = new Hydro_Raindrop_TransientTokenStorage();
				$token_storage->unsetAccessToken();

				delete_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS );

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

		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
		);

		$parent = new WP_Query( $args );

		$posts = [];

		while ( $parent->have_posts() ) {
			$parent->the_post();

			$id = get_the_ID();

			if ( $id ) {
				$posts[ $id ] = get_the_title() . ' - ' . get_the_permalink();
			}
		}

		include __DIR__ . '/../admin/partials/hydro-raindrop-admin-display.php';

	}

	/**
	 * @return bool
	 */
	public function options_are_valid() : bool {

		$token_success = (string) get_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS, '' );

		if ( empty( $token_success ) && Hydro_Raindrop::has_valid_raindrop_client_options() ) {
			try {
				$client = Hydro_Raindrop::get_raindrop_client();
				$client->getAccessToken();

				update_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS, 1 );

				return true;
			} catch ( RefreshTokenFailed $e ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Display the activation notice.
	 *
	 * @return void
	 */
	public function activation_notice() {

		if ( get_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE ) ) {
			return;
		}

		$option_page_url = admin_url( 'options-general.php?page=' . $this->plugin_name . '-options' );

		$message = sprintf(
			__( 'Succesfully activated the WP Hydro Raindrop plugin, to configure the plugin go to the Hydro Raindrop MFA <a style="color: #fff; font-weight: bold;" href="%1$s">settings page</a>.', $this->plugin_name ),
			esc_url( $option_page_url )
		);

		printf(
			'<div class="notice is-dismissible" style="background-color: #5591f3; color: #fff; border-left: none;">
				<p>%1$s</p>
			</div>',
			$message
		);

		add_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE, '1' );

	}

	/**
	 * Add action links to plugins table.
	 *
	 * @param array $links Default links.
	 * @return array
	 */
	public function add_action_links( array $links = [] ) : array {

		$option_page_url = admin_url( 'options-general.php?page=' . $this->plugin_name . '-options' );

		$add_links = [
			'<a href="' . $option_page_url . '">' . __( 'Settings', 'wp-hydro-raindrop' ) . '</a>',
		];

		return array_merge( $links, $add_links );
	}

}
