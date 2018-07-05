<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/adrenth
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 * @author     Alwin Drenth <adrenth@gmail.com>
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
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

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
			array(),
			$this->version
		);

	}

	public function admin_init() {
		register_setting( 'hydro_api', 'application_id' );
		register_setting( 'hydro_api', 'client_id' );
		register_setting( 'hydro_api', 'client_secret' );
		register_setting( 'hydro_api', 'environment' );
	}

	/**
	 * Add options page.
	 */
	public function admin_menu() {
		add_options_page(
			'Hydro Raindrop MFA',
			'Hydro Raindrop MFA',
			'manage_options',
			$this->plugin_name . '-options',
			array(
				$this,
				'admin_page'
			)
		);
	}

	/**
	 * Display the admin page.
	 */
	public function admin_page() {
		include __DIR__ . '/../admin/partials/hydro-raindrop-admin-display.php';
	}

}
