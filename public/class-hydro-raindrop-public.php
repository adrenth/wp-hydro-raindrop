<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/adrenth
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
class Hydro_Raindrop_Public {

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
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
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

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

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

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/hydro-raindrop-public.js',
			array( 'jquery' ),
			$this->version
		);

	}

	/**
	 * @param $user
	 */
	public function custom_user_profile_fields( $user ) {
		include __DIR__ . '/partials/hydro-raindrop-public-user-profile.php';
	}

	public function custom_user_profile_update( $user_id ) {
		if ( !current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		if ( !empty( $_POST['hydro_id'] ) ) {
			$client = Hydro_Raindrop::get_raindrop_client();
			$hydroId = sanitize_text_field( $_POST['hydro_id'] );

			try {
				$client->registerUser( sanitize_text_field( $_POST['hydro_id'] ) );
				update_user_meta( $user_id, 'hydro_id', $hydroId );
				update_user_meta( $user_id, 'hydro_mfa_enabled', 1 );
				update_user_meta( $user_id, 'hydro_raindrop_confirmed', 1 );
			} catch ( \Adrenth\Raindrop\Exception\RegisterUserFailed $e ) {
				var_dump($e->getMessage());exit;
				// @todo error handling
				delete_user_meta( $user_id, 'hydro_id' );
				update_user_meta( $user_id, 'hydro_mfa_enabled', 0 );
				update_user_meta( $user_id, 'hydro_raindrop_confirmed', 0 );
			}
		}

		if ( isset( $_POST['disable_hydro_mfa'] ) ) {
			$client = Hydro_Raindrop::get_raindrop_client();
			$hydroId = (string) get_user_meta( $user_id , 'hydro_id', true );

			// @todo empty hydroId check

			try {
				$client->unregisterUser( $hydroId );
				delete_user_meta( $user_id, 'hydro_id', $hydroId );
				delete_user_meta( $user_id, 'hydro_mfa_enabled' );
				delete_user_meta( $user_id, 'hydro_raindrop_confirmed' );
			} catch ( \Adrenth\Raindrop\Exception\UnregisterUserFailed $e ) {
				var_dump($e->getMessage());exit;
				// @todo error handling
			}
		}
	}

	public function custom_user_profile_validate( &$errors, $update = null, &$user = null ) {
		// @todo
		// if ( empty( $_POST['hydro_id'] ) ) {
		// 	 $errors->add( 'hydro_id', esc_html__( 'Please provide a HydroID.', $this->plugin_name ) );
		// }
	}

}
