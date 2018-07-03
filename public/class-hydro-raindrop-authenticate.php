<?php

class Hydro_Raindrop_Authenticate {
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
	 * @param string  $user_login The user login.
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
	 */
	public function authenticate() {

		// TODO: Check if Hydro MFA is enabled. No need for querying the database.

		if ( ! is_user_logged_in() ) {
			return;
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

		return false;

	}

	/**
	 * Shows MFA
	 *
	 * @return void
	 */
	private function show_mfa() {

		require __DIR__ . '/partials/hydro-raindrop-public-mfa.php';

		exit;

	}

	/**
	 * Checks whether current user requires Hydro Raindro MFA
	 *
	 * @param WP_User $user
	 */
	private function user_requires_mfa( WP_User $user ) {

		$hydro_id                 = (string) get_user_meta( $user->ID, 'hydro_id', true );
		$hydro_mfa_enabled        = (bool) get_user_meta( $user->ID, 'hydro_mfa_enabled', true );
		$hydro_raindrop_confirmed = (bool) get_user_meta( $user->ID, 'hydro_raindrop_confirmed', true );

		return ! empty( $hydro_id ) && $hydro_mfa_enabled && $hydro_raindrop_confirmed;

	}
}
