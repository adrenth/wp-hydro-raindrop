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
	 * @param      string $version     The version of this plugin.
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
	public function enqueue_styles() : void {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-public.css',
			array(),
			$this->version
		);

	}

	/**
	 * Register the stylesheets for the login part of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_login_styles() : void {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-public.css',
			[],
			$this->version
		);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() : void {

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/hydro-raindrop-public.js',
			[
				'jquery',
			],
			$this->version
		);

	}

	/**
	 * @param WP_User $user
	 */
	public function custom_user_profile_fields( WP_User $user ) : void {

		include __DIR__ . '/partials/hydro-raindrop-public-user-profile.php';

	}

	/**
	 * Validate and process Hydro Raindrop MFA post data.
	 *
	 * @param WP_Error      $errors Error collection from edit_user().
	 * @param bool|null     $update Wether the user profile is edited or not.
	 * @param stdClass|null $user   User object being edited.
	 *
	 * @return void
	 */
	public function custom_user_profile_validate( &$errors, bool $update = null, &$user = null ) : void {

		// Already errors present. Do nothing. User will not be updated to database.
		if ( ! $user || ! $update || count( $errors->errors ) > 0 ) {

			return;

		}

		$user_has_hydro_id = $this->user_has_hydro_id( $user );

		// @codingStandardsIgnoreLine
		$hydro_id = sanitize_text_field((string) ($_POST['hydro_id'] ?? ''));

		if ( ! empty( $hydro_id )
				&& ! $user_has_hydro_id ) {

			$client = Hydro_Raindrop::get_raindrop_client();

			if ( strlen( $hydro_id ) !== 7 ) {

				$errors->add(
					'hydro_id_invalid',
					esc_html__( 'Please provide a valid HydroID.', 'wp-hydro-raindrop' )
				);

				return;

			}

			try {

				$client->registerUser( sanitize_text_field( $hydro_id ) );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_id', $hydro_id );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_mfa_enabled', 1 );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_raindrop_confirmed', 0 );

				if ( wp_redirect( self_admin_url( 'profile.php?hydro-raindrop-verify=1' ) ) ) {
					exit;
				}

			} catch ( \Adrenth\Raindrop\Exception\RegisterUserFailed $e ) {

				$errors->add( 'hydro_register_failed', $e->getMessage() );

				// @codingStandardsIgnoreLine
				delete_user_meta( $user->ID, 'hydro_id' );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_mfa_enabled', 0 );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_raindrop_confirmed', 0 );

			}

		}

		// @codingStandardsIgnoreLine
		$disable_hydro_mfa = isset( $_POST['disable_hydro_mfa'] );

		if ( $disable_hydro_mfa && $user_has_hydro_id ) {

			$client       = Hydro_Raindrop::get_raindrop_client();
			$hydro_id     = (string) get_user_meta( $user->ID, 'hydro_id', true );
			$authenticate = new Hydro_Raindrop_Authenticate(
				$this->plugin_name,
				$this->version
			);

			try {
				$client->unregisterUser( $hydro_id );

				// @codingStandardsIgnoreLine
				delete_user_meta( $user->ID, 'hydro_id', $hydro_id );

				// @codingStandardsIgnoreLine
				delete_user_meta( $user->ID, 'hydro_mfa_enabled' );

				// @codingStandardsIgnoreLine
				delete_user_meta( $user->ID, 'hydro_raindrop_confirmed' );

				$authenticate->unset_cookie();

			} catch ( \Adrenth\Raindrop\Exception\UnregisterUserFailed $e ) {

				$errors->add( 'hydro_unregister_failed', $e->getMessage() );

			}

		}

	}

	/**
	 * @param stdClass $user
	 *
	 * @return bool
	 */
	private function user_has_hydro_id( stdClass $user ) : bool {

		// @codingStandardsIgnoreLine
		$hydro_id = (string) get_user_meta( $user->ID, 'hydro_id', true );

		return ! empty( $hydro_id );

	}
}
