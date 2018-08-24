<?php

declare( strict_types=1 );

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 */

use Adrenth\Raindrop\Exception\RegisterUserFailed;
use Adrenth\Raindrop\Exception\UserAlreadyMappedToApplication;

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
	 * Errors occurred when managing HydroID.
	 *
	 * @var WP_Error|null
	 * @since 1.3.0
	 */
	private $manage_hydro_id_errors;

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
	public function enqueue_styles() {

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
	public function enqueue_login_styles() {

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
	public function enqueue_scripts() {

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
	public function custom_user_profile_fields( WP_User $user ) {

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
	public function custom_user_profile_validate( &$errors, bool $update = null, &$user = null ) {

		// Already errors present. Do nothing. User will not be updated to database.
		if ( ! $user || ! $update || count( $errors->errors ) > 0 ) {
			return;
		}

		$this->handle_hydro_id_form( $errors, $user );

	}

	/**
	 * Handles saving of the HydroID.
	 *
	 * @return string
	 */
	public function manage_hydro_id() {

		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user = wp_get_current_user();

		// @codingStandardsIgnoreLine
		$retrieved_nonce = $_POST['_hydro_id_nonce'] ?? null;

		$errors = new WP_Error();

		// @codingStandardsIgnoreLine
		if ( $retrieved_nonce && wp_verify_nonce( $retrieved_nonce, 'hydro_raindrop_hydro_id' ) ) {
			$this->handle_hydro_id_form( $errors, $user->data );
		}

		$this->manage_hydro_id_errors = $errors;

	}

	/**
	 * Handle the HydroID form. Must be handled before the headers are sent.
	 *
	 * @param WP_Error $errors Error collection from edit_user().
	 * @param stdClass $user   User object being edited.
	 */
	public function handle_hydro_id_form( &$errors, stdClass $user ) {

		$user_has_hydro_id = $this->user_has_hydro_id( $user );

		// @codingStandardsIgnoreLine
		$hydro_id = sanitize_text_field((string) ($_POST['hydro_id'] ?? ''));

		if ( ! empty( $hydro_id ) && ! $user_has_hydro_id ) {

			$client = Hydro_Raindrop::get_raindrop_client();

			$length = strlen( $hydro_id );

			if ( $length < 3 || $length > 32 ) {
				$errors->add(
					'hydro_id_invalid',
					esc_html__( 'Please provide a valid HydroID.', 'wp-hydro-raindrop' )
				);
				return;
			}

			$hydro_raindrop_custom_mfa_page = (int) get_option( 'hydro_raindrop_custom_mfa_page' );
			$hydro_raindrop_custom_mfa_url  = get_permalink( $hydro_raindrop_custom_mfa_page );

			if ( $hydro_raindrop_custom_mfa_page > 0
					&& get_post_status( $hydro_raindrop_custom_mfa_page ) === 'publish'
			) {
				$redirect_url = $hydro_raindrop_custom_mfa_url . '?hydro-raindrop-verify=1';
			} else {
				$redirect_url = self_admin_url( 'profile.php?hydro-raindrop-verify=1' );
			}

			try {

				$client->registerUser( sanitize_text_field( $hydro_id ) );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_id', $hydro_id );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_mfa_enabled', 1 );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_raindrop_confirmed', 0 );

				// @codingStandardsIgnoreLine
				wp_redirect( $redirect_url );
				exit;

			} catch ( UserAlreadyMappedToApplication $e) {
				/*
				 * User is already mapped to this application.
				 *
				 * Edge case: A user tries to re-register with Hydro ID. If the user meta has been deleted, the
				 *            user can re-use his Hydro ID but needs to verify it again.
				 */

				$authenticate = new Hydro_Raindrop_Authenticate(
					$this->plugin_name,
					$this->version
				);

				$authenticate->unset_cookies();

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_id', $hydro_id );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_mfa_enabled', 1 );

				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_raindrop_confirmed', 0 );

				// @codingStandardsIgnoreLine
				wp_redirect( $redirect_url );
				exit;

			} catch ( RegisterUserFailed $e ) {

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

				$authenticate->unset_cookies();

			} catch ( \Adrenth\Raindrop\Exception\UnregisterUserFailed $e ) {

				$errors->add( 'hydro_unregister_failed', $e->getMessage() );

			}

		}

	}

	/**
	 * Open <form> tag for the custom MFA page.
	 *
	 * @return string
	 */
	public function shortcode_form_open() : string {
		return '<form action="" method="post">';
	}

	/**
	 * Closing </form> tag for the custom MFA page.
	 *
	 * @return string
	 */
	public function shortcode_form_close() : string {
		return wp_nonce_field( 'hydro_raindrop_mfa' ) . '</form>';
	}

	/**
	 * MFA digits for the custom MFA page.
	 *
	 * @return string
	 * @throws Exception When message cannot be generated.
	 */
	public function shortcode_digits() : string {
		$user = Hydro_Raindrop_Authenticate::get_current_mfa_user();

		if ( ! ( $user instanceof WP_User ) ) {
			return '';
		}

		return (string) Hydro_Raindrop_Authenticate::get_message( $user );
	}

	/**
	 * MFA authorize button for the custom MFA page.
	 *
	 * @return string
	 */
	public function shortcode_button_authorize() : string {
		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'hydro_raindrop',
			'hydro-raindrop-mfa-button-authorize',
			esc_html__( 'Authenticate', 'wp-hydro-raindrop' )
		);
	}

	/**
	 * MFA cancel button for the custom MFA page.
	 *
	 * @return string
	 */
	public function shortcode_button_cancel() : string {
		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'cancel_hydro_raindrop',
			'hydro-raindrop-mfa-button-cancel',
			esc_html__( 'Cancel', 'wp-hydro-raindrop' )
		);
	}

	/**
	 * Manage HydroID.
	 *
	 * @return string
	 */
	public function shortcode_manage_hydro_id() : string {

		if ( ! is_user_logged_in() ) {
			return '';
		}

		$user = wp_get_current_user();

		$errors = $this->manage_hydro_id_errors;

		ob_start();

		include __DIR__ . '/partials/hydro-raindrop-public-manage-hydro-id.php';

		$output = ob_get_contents();

		ob_end_clean();

		return $output;

	}

	/**
	 * Whether given user has a HydroID.
	 *
	 * @param stdClass $user WP User object.
	 *
	 * @return bool
	 */
	private function user_has_hydro_id( stdClass $user ) : bool {
		// @codingStandardsIgnoreLine
		$hydro_id = (string) get_user_meta( $user->ID, 'hydro_id', true );

		return ! empty( $hydro_id );
	}

}
