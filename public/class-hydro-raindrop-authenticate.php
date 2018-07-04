<?php

use Adrenth\Raindrop\Exception\VerifySignatureFailed;

class Hydro_Raindrop_Authenticate {

	const MESSAGE_TRANSIENT_ID = 'HydroRaindropMessage_%s';

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
	 * Action: wp_login
	 *
	 * @param string $user_login The user login.
	 * @param WP_User $user The user object.
	 */
	public function wp_login( $user_login, $user ) {

		if ( ! ( $user instanceof WP_User ) ) {
			return;
		}

		// Show Multi Factor Authentication screen.
		// User must be authenticated on the public blockchain to pass.
		if ( $this->user_requires_mfa( $user ) ) {
			$this->show_mfa();
		}

	}

	/**
	 * Authenticate user on every single request.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function authenticate() {

		// TODO: Check if Hydro MFA is enabled. No need for querying the database.

		if ( ! is_user_logged_in() ) {
			return;
		}

		// TODO: Add nonce verification

		if ( $_REQUEST['hydro_raindrop'] && $this->verify_signature_login() ) {
			wp_redirect( '/' );
			exit();
		}

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			if ( $this->user_requires_mfa( $user ) && ! $this->verify_cookie() ) {
				$this->show_mfa();
			}
		}
	}

	/**
	 * @return bool
	 */
	private function verify_cookie() {

		// TODO: Verify cookie

		return true;

	}

	/**
	 * Shows MFA
	 *
	 * @return void
	 * @throws Exception
	 */
	private function show_mfa() {

		$client = Hydro_Raindrop::get_raindrop_client();

		$user = wp_get_current_user();

		$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );

		$message = get_transient( $transient_id );

		if ( ! $message ) {
			$message = $client->generateMessage();

			set_transient( $transient_id, $message, 60 * 5 );
		}

		require __DIR__ . '/partials/hydro-raindrop-public-mfa.php';

		exit;

	}

	/**
	 * Checks whether current user requires Hydro Raindro MFA
	 *
	 * @param WP_User $user
	 */
	private function user_requires_mfa( WP_User $user ) {

		return true;

		$hydro_id                 = (string) get_user_meta( $user->ID, 'hydro_id', true );
		$hydro_mfa_enabled        = (bool) get_user_meta( $user->ID, 'hydro_mfa_enabled', true );
		$hydro_raindrop_confirmed = (bool) get_user_meta( $user->ID, 'hydro_raindrop_confirmed', true );

		return ! empty( $hydro_id ) && $hydro_mfa_enabled && $hydro_raindrop_confirmed;

	}

	public function verify_signature_login() {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$client = Hydro_Raindrop::get_raindrop_client();

		try {
			$user         = wp_get_current_user();
			$hydro_id     = (string) get_user_meta( $user->ID, 'hydro_id', true );
			$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );
			$message      = (int) get_transient( $transient_id );

			$client->verifySignature( $hydro_id, $message );

			delete_transient( $transient_id );

			return true;
		} catch ( VerifySignatureFailed $e ) {
			return false;
		}
	}
}
