<?php

declare( strict_types=1 );

use Adrenth\Raindrop\Exception\VerifySignatureFailed;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Hydro_Raindrop_Authenticate
 */
final class Hydro_Raindrop_Authenticate {

	private const MESSAGE_TRANSIENT_ID = 'HydroRaindropMessage_%s';

	private const COOKIE_NAME = 'HydroRaindropMfa';

	private const COOKIE_NAME_SSL = 'HydroRaindropMfa';

	private const TIME_OUT = 90;

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
	 * @throws Exception
	 */
	public function verify() {

		if ( ! $this->is_hydro_raindrop_mfa_enabled() ) {
			return;
		}

		// @codingStandardsIgnoreLine
		$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';

		// @codingStandardsIgnoreLine
		$retrieved_nonce = $_REQUEST['_wpnonce'] ?? null;

		// @codingStandardsIgnoreLine
		if ( $is_post && $_REQUEST['hydro_raindrop'] && ! wp_verify_nonce( $retrieved_nonce, 'hydro_raindrop_mfa' ) ) {

			die( 'Failed security check' );

		}

		// @codingStandardsIgnoreLine
		if ($is_post &&  $_REQUEST['hydro_raindrop'] && $this->verify_signature_login() ) {

			$this->set_cookie();

			$this->delete_transient_data();

			// TODO: Figure out how to redirect after successful verification.

			// wp_redirect( $_REQUEST['redirect_to'] );

			return;
		}

		if ( is_user_logged_in() ) {

			$user = wp_get_current_user();

			if ( $this->user_requires_mfa( $user ) && ! $this->verify_cookie() ) {

				$this->start_mfa( $user, $this->get_uri() );

			}
		}
	}

	public function logout() {

		// TODO: Do logout stuff; auth cookie is not present anymore.

	}

	/**
	 * Whether the Hydro Raindrop MFA is enabled.
	 *
	 * @return bool
	 */
	public function is_hydro_raindrop_mfa_enabled(): bool {

		return true;

	}

	/**
	 * Start Hydro Raindrop Multi Factor Authentication.
	 *
	 * @param WP_User     $user Logged in user.
	 * @param string|null $redirect_to Redirect after login.
	 *
	 * @throws Exception
	 */
	public function start_mfa( WP_User $user, string $redirect_to = null): void {

		if ( ! $redirect_to ) {
			// @codingStandardsIgnoreLine
			$redirect_to = $_POST['redirect_to'] ?? admin_url();
		}

		$error = null;

		// The authentication failed. Delete transient data to make sure a new message will be generated.
		// @codingStandardsIgnoreLine
		if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_REQUEST['hydro_raindrop']) {

			$error = 'Authentication failed.';

			$this->delete_transient_data();

		}

		$client = Hydro_Raindrop::get_raindrop_client();

		$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );

		$message = get_transient( $transient_id );

		if ( ! $message ) {
			$message = $client->generateMessage();

			set_transient( $transient_id, $message, self::TIME_OUT );
		}

		// wp_logout();

		$logo  = plugin_dir_url( __FILE__ ) . 'images/logo.svg';
		$image = plugin_dir_url( __FILE__ ) . 'images/security-code.png';

		require __DIR__ . '/partials/hydro-raindrop-public-mfa.php';

		exit;
	}

	/**
	 * @return string
	 */
	public function get_uri(): string {
		// phpcs:disable
		// Workaround for IIS which may not set REQUEST_URI, or QUERY parameters
		if ( ! isset( $_SERVER['REQUEST_URI'] ) ||
		     ( ! empty( $_SERVER['QUERY_STRING'] ) && ! strpos( $_SERVER['REQUEST_URI'], '?' ) ) ) {
			$current_uri = substr( $_SERVER['PHP_SELF'], 1 );
			if ( isset( $_SERVER['QUERY_STRING'] ) && $_SERVER['QUERY_STRING'] !== '' ) {
				$current_uri .= '?' . $_SERVER['QUERY_STRING'];
			}

			return $current_uri;

		}

		return $_SERVER['REQUEST_URI'];
		// phpcs:enable
	}

	private function set_cookie() {
		// phpcs:disable

		// TODO: Create secret cookie

		$cookie = 'Hydro!';
		// $expire = strtotime('+24 hours');

		setcookie( self::COOKIE_NAME, $cookie, 0, COOKIEPATH, COOKIE_DOMAIN, false, true );

		if ( COOKIEPATH !== SITECOOKIEPATH ) {
			setcookie( self::COOKIE_NAME, $cookie, 0, SITECOOKIEPATH, COOKIE_DOMAIN, false, true );
		}

		if ( is_ssl() ) {
			setcookie( self::COOKIE_NAME_SSL, $cookie, 0, COOKIEPATH, COOKIE_DOMAIN, true, true );

			if ( COOKIEPATH !== SITECOOKIEPATH ) {
				setcookie( self::COOKIE_NAME_SSL, $cookie, 0, SITECOOKIEPATH, COOKIE_DOMAIN, true, true );
			}

		}
		// phpcs:enable
	}

	/**
	 * Unset the Hydro Raindrop MFA cookie
	 *
	 * @return void
	 */
	public function unset_cookie(): void {

		// @codingStandardsIgnoreLine
		setcookie( self::COOKIE_NAME, '', strtotime( '-1 day' ), COOKIEPATH, COOKIE_DOMAIN );

		// @codingStandardsIgnoreLine
		setcookie( self::COOKIE_NAME, '', strtotime( '-1 day' ), SITECOOKIEPATH, COOKIE_DOMAIN );

		// @codingStandardsIgnoreLine
		setcookie( self::COOKIE_NAME_SSL, '', strtotime( '-1 day' ), COOKIEPATH, COOKIE_DOMAIN );

		// @codingStandardsIgnoreLine
		setcookie( self::COOKIE_NAME_SSL, '', strtotime( '-1 day' ), SITECOOKIEPATH, COOKIE_DOMAIN );

	}

	/**
	 * Verify Hydro Raindrop MFA cookie.
	 *
	 * @return bool
	 */
	private function verify_cookie(): bool {
		$cookie_name = null;

		// @codingStandardsIgnoreLine
		if ( isset( $_COOKIE[ self::COOKIE_NAME_SSL]) || is_ssl() ) {
			$cookie_name = self::COOKIE_NAME_SSL;
		}
		else {
			$cookie_name = self::COOKIE_NAME;
		}

		// @codingStandardsIgnoreLine
		if ( ! isset( $_COOKIE[ $cookie_name ] ) ) {
			return false;
		}

		// TODO: Verify contents of cookie

		return true;
	}

	/**
	 * Checks whether current user requires Hydro Raindro MFA.
	 *
	 * @param WP_User $user Currently logged in user.
	 * @return bool
	 */
	private function user_requires_mfa( WP_User $user ): bool {

		// @codingStandardsIgnoreLine
		$hydro_id = (string) get_user_meta( $user->ID, 'hydro_id', true );

		// @codingStandardsIgnoreLine
		$hydro_mfa_enabled = (bool) get_user_meta( $user->ID, 'hydro_mfa_enabled', true );

		// @codingStandardsIgnoreLine
		$hydro_raindrop_confirmed = (bool) get_user_meta( $user->ID, 'hydro_raindrop_confirmed', true );

		return ! empty( $hydro_id ) && $hydro_mfa_enabled && $hydro_raindrop_confirmed;

	}

	/**
	 * Delete any transient data for current user.
	 *
	 * @return void
	 */
	private function delete_transient_data(): void {

		$user = wp_get_current_user();

		if ( ! $user instanceof WP_User ) {
			return;
		}

		$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );

		delete_transient( $transient_id );

	}

	/**
	 * Perform Hydro Raindrop signature verification.
	 *
	 * @return bool
	 */
	private function verify_signature_login(): bool {

		if ( ! is_user_logged_in() ) {
			return false;
		}

		$client = Hydro_Raindrop::get_raindrop_client();

		try {
			$user = wp_get_current_user();

			// @codingStandardsIgnoreLine
			$hydro_id     = (string) get_user_meta( $user->ID, 'hydro_id', true );
			$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );
			$message      = (int) get_transient( $transient_id );

			$response = $client->verifySignature( $hydro_id, $message );

			// @codingStandardsIgnoreLine
			add_user_meta( $user->ID, 'hydro_verification_signature_identifier', $response->getIdentifier() );

			// @codingStandardsIgnoreLine
			add_user_meta( $user->ID, 'hydro_verification_signature_timestamp', $response->getTimestamp() );

			$this->delete_transient_data();

			return true;
		} catch ( VerifySignatureFailed $e ) {
			return false;
		}
	}

}
