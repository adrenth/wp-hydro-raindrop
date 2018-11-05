<?php

declare( strict_types=1 );

use Adrenth\Raindrop\Exception\RegisterUserFailed;
use Adrenth\Raindrop\Exception\UnregisterUserFailed;
use Adrenth\Raindrop\Exception\UserAlreadyMappedToApplication;
use Adrenth\Raindrop\Exception\VerifySignatureFailed;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Hydro_Raindrop_Authenticate
 */
final class Hydro_Raindrop_Authenticate {

	const TRANSIENT_ID_MESSAGE = 'hydro_raindrop_mfa_message_%d';

	/**
	 * Stores the post which is being verified.
	 *
	 * @var string
	 */
	const TRANSIENT_ID_POST_VERIFICATION = 'hydro_raindrop_post_verification_%d';

	/**
	 * Stores the post which is verified.
	 *
	 * @var string
	 */
	const TRANSIENT_ID_POST_VERIFIED = 'hydro_raindrop_post_verified_%d_%d';

	/**
	 * The ID of this plugin.
	 *
	 * @var string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Helper.
	 *
	 * @var Hydro_Raindrop_Helper $helper
	 */
	private $helper;

	/**
	 * Cookie helper.
	 *
	 * @var Hydro_Raindrop_Cookie $cookie
	 */
	private $cookie;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->helper      = new Hydro_Raindrop_Helper();
		$this->cookie      = new Hydro_Raindrop_Cookie( $plugin_name, $version );

	}

	/**
	 * The authenticate filter hook is used to perform additional validation/authentication any time a user logs in to
	 * WordPress.
	 *
	 * @param null|WP_User|WP_Error $user NULL indicates no process has authenticated the user yet. A WP_Error object
	 *                                    indicates another process has failed the authentication.
	 *                                    A WP_User object indicates another process has authenticated the user.
	 *
	 * @return null|WP_User|WP_Error
	 * @throws Exception If authentication message cannot be generated.
	 */
	public function authenticate( $user = null ) {

		if ( ! $user
				|| $user instanceof WP_Error
				|| ! ( $user instanceof WP_User )
				|| ! is_ssl()
		) {
			return $user;
		}

		// @codingStandardsIgnoreLine
		$account_blocked = (bool) get_user_meta(
			$user->ID,
			Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED,
			true
		);

		/*
		 * User account was blocked because of too many failed MFA attempts.
		 */
		if ( $account_blocked ) {
			return new WP_Error(
				'hydro_raindrop_account_blocked',
				__( 'Your account has been blocked.', 'wp-hydro-raindrop' )
			);
		}

		/*
		 * Set up of Hydro Raindrop MFA is required.
		 */
		if ( $this->user_requires_setup_mfa( $user ) ) {
			$this->log( 'User authenticates and requires Hydro Raindrop MFA Setup.' );
			$this->start_mfa_setup( $user );
		}

		/*
		 * Hydro Raindrop MFA is required to proceed.
		 */
		if ( $this->user_requires_mfa( $user ) ) {
			$this->log( 'User authenticates and requires Hydro Raindrop MFA.' );
			$this->start_mfa( $user );
		}

		return $user;

	}

	/**
	 * Verify request.
	 *
	 * @return void
	 * @throws Exception When MFA could not be started.
	 */
	public function verify() {

		try {
			$cookie_is_valid = $this->cookie->validate();
		} catch ( Hydro_Raindrop_CookieExpired $e ) {
			$this->log( 'Cookie has expired.' );

			$flash = new Hydro_Raindrop_Flash( $this->plugin_name );
			$flash->warning( __( 'The Multi Factor Authentication process has been timed out.', 'wp-hydro-raindrop' ) );

			// @codingStandardsIgnoreStart

			// Check and set cookie timed out
			if ( ! isset( $_COOKIE[ Hydro_Raindrop_Helper::COOKIE_MFA_TIMED_OUT ] )
					|| $_COOKIE[ Hydro_Raindrop_Helper::COOKIE_MFA_TIMED_OUT ] === 'false'
			) {
				setcookie( Hydro_Raindrop_Helper::COOKIE_MFA_TIMED_OUT, 'true', time() + 3600, COOKIEPATH, '' );
			}

			wp_redirect( home_url() );
			exit();
			// @codingStandardsIgnoreEnd
		}

		$this->verify_post_request();

		// @codingStandardsIgnoreLine
		$post_id = url_to_postid( $this->helper->get_current_url() );

		/*
		 * Skip further verification when we're at the MFA Settings page.
		 */
		if ( $this->helper->is_settings_page_enabled()
				&& $this->helper->get_settings_page_id() === $post_id
		) {
			$this->log( 'Accessing Custom MFA Settings page.' );
			return;
		}

		/*
		 * Validating MFA cookie.
		 */
		$cookie_elements = $this->cookie->parse();

		if ( ! $cookie_is_valid && $cookie_elements ) {
			$this->log( 'Unset MFA cookie. MFA cookie did not pass validation.' );
			$this->cookie->unset();
		}

		/*
		 * Perform first time verification.
		 */
		if ( $this->is_request_verify() && is_user_logged_in() ) {
			$this->log( 'Start first time verification.' );
			$this->start_mfa( wp_get_current_user() );
		}

		/*
		 * Protect MFA page.
		 */
		if ( ! $cookie_is_valid
				&& $this->helper->is_mfa_page_enabled()
				&& $this->helper->get_mfa_page_id() === $post_id
		) {
			$this->log( 'Cookie not valid. User accessing MFA Page which is not allowed. Redirecting to home.' );
			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit();
		}

		/*
		 * Protect Setup page.
		 */
		if ( ! $cookie_is_valid
				&& $this->helper->is_setup_page_enabled()
				&& $this->helper->get_setup_page_id() === $post_id
		) {
			$this->log( 'Cookie not valid. User accessing MFA Setup Page which is not allowed. Redirecting to home.' );
			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit();
		}

		/*
		 * Perform re-verification on post.
		 */
		if ( is_user_logged_in() ) {
			$user          = false;
			$mfa_required  = false;
			$mfa_timestamp = 0; // If timestamp > 0; the MFA has been successfully completed for post.

			if ( $post_id > 0 ) {
				$user          = wp_get_current_user();
				$mfa_required  = (bool) get_post_meta( $post_id, Hydro_Raindrop_Helper::POST_META_MFA_REQUIRED, true );
				$mfa_timestamp = (string) get_transient( sprintf( self::TRANSIENT_ID_POST_VERIFIED, $user->ID, $post_id ) );
			}

			if ( $mfa_required && $user && '' === $mfa_timestamp ) {
				$this->log( 'Storing Redirect URL: ' . $this->helper->get_current_url() );

				// @codingStandardsIgnoreLine
				update_user_meta(
					$user->ID,
					Hydro_Raindrop_Helper::USER_META_REDIRECT_URL,
					$this->helper->get_current_url()
				);

				$this->log( sprintf( 'User %s is accessing a post which requires MFA.', $user->user_login ) );

				// Register for which Post we need to perform a MFA.
				set_transient(
					sprintf( self::TRANSIENT_ID_POST_VERIFICATION, $user->ID ),
					$post_id,
					Hydro_Raindrop_Cookie::MFA_TIME_OUT
				);

				$this->start_mfa( $user );
			}
		}

		if ( ! $cookie_is_valid ) {
			return;
		}

		$user = $this->get_current_mfa_user();

		$mfa_post_id = (int) get_transient( sprintf( self::TRANSIENT_ID_POST_VERIFICATION, $user->ID ) );

		/*
		 * Redirect to MFA page if not already.
		 */
		if ( $this->helper->is_mfa_page_enabled()
				&& $this->user_requires_mfa( $user )
				&& $this->helper->get_mfa_page_id() !== $post_id
		) {
			if ( $mfa_post_id > 0 ) {
				$this->log( 'Post re-authentication MFA in effect, but different page accessed. Cancel MFA.' );
				delete_transient( sprintf( self::TRANSIENT_ID_POST_VERIFICATION, $user->ID ) );
				delete_transient( sprintf( self::TRANSIENT_ID_POST_VERIFIED, $user->ID, $mfa_post_id ) );
				$this->cookie->unset();

				if ( $mfa_post_id === $post_id ) {
					// @codingStandardsIgnoreLine
					wp_redirect( home_url() );
					exit();
				}

				return;
			}

			$this->log( 'User not on Hydro Raindrop MFA page. Redirecting...' );

			// @codingStandardsIgnoreLine
			wp_redirect( $this->helper->get_mfa_page_url() );
			exit();
		}

		/*
		 * User requires Hydro Raindrop MFA setup.
		 */
		if ( $this->user_requires_setup_mfa( $user ) ) {
			$this->log( 'User requires setup Hydro Raindrop MFA.' );

			if ( $this->helper->is_setup_page_enabled()
					&& $this->helper->get_setup_page_id() !== $post_id
			) {
				$this->log( 'User not on Hydro Raindrop Setup page. Redirecting...' );

				// @codingStandardsIgnoreLine
				wp_redirect( $this->helper->get_setup_page_url() );
				exit();
			}

			$this->start_mfa_setup( $user );
		}

		/*
		 * Render MFA or Setup page if not on login page.
		 */
		if ( ! $this->helper->is_mfa_page_enabled()
				&& strpos( $this->helper->get_current_url(), 'wp-login.php' ) !== false
		) {
			$user = $this->get_current_mfa_user();

			if ( $this->user_requires_setup_mfa( $user ) ) {
				$this->start_mfa_setup( $user );
			}

			$this->log( 'User not on Hydro Raindrop MFA page. Render MFA page.' );
			$this->start_mfa( $user );
		}

	}

	/**
	 * Get's the current MFA user from cookie.
	 *
	 * @return WP_User|null
	 */
	public function get_current_mfa_user() {

		try {
			$user_id = $this->cookie->validate();
		} catch ( Hydro_Raindrop_CookieExpired $e ) {
			return null;
		}

		if ( ! $user_id ) {
			return null;
		}

		$user = get_user_by( 'ID', $user_id );

		if ( ! ( $user instanceof WP_User ) ) {
			return null;
		}

		return $user;

	}

	/**
	 * Get the Raindrop MFA message.
	 *
	 * @param WP_User $user Current logged in user.
	 *
	 * @return int
	 * @throws Exception When message could not be generated.
	 */
	public static function get_message( WP_User $user ) : int {

		$client = Hydro_Raindrop::get_raindrop_client();

		$transient_id = sprintf( self::TRANSIENT_ID_MESSAGE, $user->ID );

		$message = get_transient( $transient_id );

		if ( ! $message ) {
			$message = $client->generateMessage();
			set_transient( $transient_id, $message, Hydro_Raindrop_Cookie::MFA_TIME_OUT );
		}

		return (int) $message;

	}

	/**
	 * Verify POST request for Hydro Raindrop specifics.
	 *
	 * @return void
	 */
	private function verify_post_request() {
		// @codingStandardsIgnoreStart
		$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';

		if ( ! $is_post || ! is_ssl() ) {
			return;
		}

		$nonce = $_POST['_wpnonce'] ?? null;

		$user = wp_get_current_user();

		/*
		 * Handle Hydro Raindrop Settings.
		 */
		if ( isset( $_POST['hydro_raindrop_settings'] )
				&& $user
				&& wp_verify_nonce( $nonce, 'hydro_raindrop_settings' )
				&& ! $this->handle_settings( $user )
		) {

			return;

		}

		$user = $this->get_current_mfa_user();

		/*
		 * VERIFY NONCE FOR THE HYDRO RAINDROP MFA/SETUP PAGE
		 */
		if ( ( isset( $_POST['hydro_raindrop_mfa'] ) && ! wp_verify_nonce( $nonce, 'hydro_raindrop_mfa' ) )
				|| ( isset ( $_POST['hydro_raindrop_setup'] ) && ! wp_verify_nonce( $nonce, 'hydro_raindrop_setup' ) )
		) {
			$this->log( 'Nonce verification failed: Unset MFA cookie and redirect to home URL.' );

			$this->cookie->unset();

			wp_redirect( home_url() );
			exit();
		}

		/*
		 * Verify Hydro Raindrop MFA message
		*/
		if ( isset( $_POST['hydro_raindrop_mfa'] )
				&& $user
				&& ! $this->handle_mfa( $user )
		) {

			return;

		}

		/*
		 * Handle Hydro Raindrop Setup.
		 */
		if ( isset( $_POST['hydro_raindrop_setup'] )
				&& $user
				&& ! $this->handle_setup( $user )
		) {

			return;

		}

		// @codingStandardsIgnoreEnd

		/*
		 * Skip Hydro Raindrop Setup
		 */
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['hydro_raindrop_setup_skip'] )
				&& wp_verify_nonce( $nonce, 'hydro_raindrop_setup' )
		) {
			$method = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD );

			$user = $this->get_current_mfa_user();

			if ( $user
					&& ( Hydro_Raindrop_Helper::MFA_METHOD_PROMPTED === $method
						|| Hydro_Raindrop_Helper::MFA_METHOD_OPTIONAL === $method
					)
			) {
				$this->cookie->unset();
				$this->delete_transient_data( $user );
				$this->set_auth_cookie( $user );
				$this->redirect( $user );
			}
		}

		/*
		 * Allow user to cancel the MFA. Which results in a logout.
		 */
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['hydro_raindrop_mfa_cancel'] )
				&& wp_verify_nonce( $nonce, 'hydro_raindrop_mfa' )
		) {
			$this->log( 'User cancels MFA.' );

			$this->cookie->unset();

			// Delete all transient data which is used during the MFA process.
			if ( $user ) {
				$this->delete_transient_data( $user );
			}

			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit();
		}

	}

	/**
	 * Start Hydro Raindrop Multi Factor Authentication.
	 *
	 * @param WP_User $user Authenticated user.
	 *
	 * @throws Exception If message could not be generated.
	 */
	private function start_mfa( WP_User $user ) {

		$this->log( sprintf( 'Start MFA for user %s.', $user->user_login ) );

		// Delete all transient data for current user.
		$this->delete_transient_data( $user );

		// Set the MFA cookie.
		$this->cookie->set( $user->ID );

		do_action( Hydro_Raindrop_Helper::ACTION_PRE_MFA, $user );

		$error = null;

		// Redirect to the Custom MFA page (if applicable).
		if ( $this->helper->is_mfa_page_enabled() ) {

			// @codingStandardsIgnoreLine
			$post_id = url_to_postid( $this->helper->get_current_url() );

			$this->log( 'MFA page is enabled.' );

			if ( $this->helper->get_mfa_page_id() !== $post_id ) {

				$this->log( 'MFA Page is enabled but currently not on MFA Page. Redirecting to MFA page.' );

				// @codingStandardsIgnoreLine
				wp_redirect( $this->helper->get_mfa_page_url() );
				exit();
			}

			return;
		}

		$this->log( 'Render the default MFA Page.' );

		require __DIR__ . '/partials/hydro-raindrop-public-mfa.php';
		exit();

	}

	/**
	 * Start Hydro Raindrop Setup.
	 *
	 * @param WP_User $user Authenticated user.
	 *
	 * @return void
	 */
	private function start_mfa_setup( WP_User $user ) {

		$this->log( 'Start MFA Setup.' );

		$this->cookie->set( $user->ID );

		do_action( Hydro_Raindrop_Helper::ACTION_PRE_SETUP_MFA, $user );

		if ( $this->helper->is_setup_page_enabled() ) {

			$this->log( 'Setup page is enabled.' );

			if ( strpos( $this->helper->get_current_url(), $this->helper->get_setup_page_url() ) !== 0 ) {
				// @codingStandardsIgnoreLine
				wp_redirect( $this->helper->get_setup_page_url() );
				exit();
			}

			return;
		}

		$this->log( 'Render the default MFA Setup Page.' );

		require __DIR__ . '/partials/hydro-raindrop-public-setup.php';
		exit();

	}

	/**
	 * Handle Setup POST Request.
	 *
	 * @param WP_User $user Authenticated user.
	 *
	 * @return bool
	 */
	private function handle_setup( WP_User $user ) : bool {

		// @codingStandardsIgnoreLine
		$hydro_id = sanitize_text_field( (string) ( $_POST['hydro_id'] ?? '' ) );
		$flash    = new Hydro_Raindrop_Flash( $user->user_login );
		$client   = Hydro_Raindrop::get_raindrop_client();
		$length   = strlen( $hydro_id );

		if ( $length < 3 || $length > 32 ) {
			$flash->error( esc_html__( 'Please provide a valid HydroID.', 'wp-hydro-raindrop' ) );
			return false;
		}

		$redirect_to = $this->helper->get_current_url( true ) . '?hydro-raindrop-verify=1';

		if ( $this->helper->is_mfa_page_enabled() ) {
			$redirect_to = $this->helper->get_mfa_page_url() . '?hydro-raindrop-verify=1';
		}

		try {

			$client->registerUser( sanitize_text_field( $hydro_id ) );

			$flash->info( 'Your HydroID has been successfully set-up. Enter security code in the Hydro app.' );

		} catch ( UserAlreadyMappedToApplication $e ) {
			/*
			 * User is already mapped to this application.
			 *
			 * Edge case: A user tries to re-register with HydroID. If the user meta has been deleted, the
			 *            user can re-use his HydroID but needs to verify it again.
			 */

			$this->log( 'User is already mapped to this application: ' . $e->getMessage() );

			try {
				$client->unregisterUser( $hydro_id );

				$flash->warning( 'Your HydroID was already mapped to this site. Mapping is removed. Please re-enter your HydroID to proceed.' );

				$redirect_to = $this->helper->get_current_url();

				if ( $this->helper->is_setup_page_enabled() ) {
					$redirect_to = $this->helper->get_setup_page_url();
				}
			} catch ( UnregisterUserFailed $e ) {
				$this->log( 'Unregistering user failed: ' . $e->getMessage() );
			}
		} catch ( RegisterUserFailed $e ) {

			$flash->error( $e->getMessage() );

			// @codingStandardsIgnoreStart
			delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID );
			delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED );
			delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED );
			delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS );
			// @codingStandardsIgnoreEnd

			do_action( Hydro_Raindrop_Helper::ACTION_SETUP_FAILED, $user );

			return false;

		}

		// @codingStandardsIgnoreStart
		update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, $hydro_id );
		update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED, 1 );
		update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED, 0 );
		update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS, 0 );

		wp_redirect( $redirect_to );
		exit();
		// @codingStandardsIgnoreEnd

	}

	/**
	 * Handle MFA POST Request.
	 *
	 * @param WP_User $user Authenticated user.
	 *
	 * @return bool
	 */
	private function handle_mfa( WP_User $user ) : bool {

		$signature_verified = $this->verify_signature_login( $user );

		if ( $signature_verified ) {
			$this->handle_mfa_success( $user );
		} else {
			$this->handle_mfa_failure( $user );
		}

		return false;
	}

	/**
	 * Handle a successful MFA.
	 *
	 * @param WP_User $user The user for which the MFA was successful.
	 *
	 * @return void
	 */
	private function handle_mfa_success( WP_User $user ) {

		$this->log( 'MFA success.' );

		$this->log( 'Unset MFA cookie.' );

		$this->cookie->unset();

		do_action( Hydro_Raindrop_Helper::ACTION_MFA_SUCCESS, $user );

		if ( ! is_user_logged_in() ) {
			$this->log( 'User is not logged in. Set the auth cookie.' );
			$this->set_auth_cookie( $user );
		}

		/*
		 * Register that MFA has been verified for a Post which requires MFA.
		 */
		$post_id = (int) get_transient( sprintf( self::TRANSIENT_ID_POST_VERIFICATION, $user->ID ) );

		if ( $post_id > 0 ) {
			$this->log( sprintf( 'MFA for Post %d for user %s has been verified.', $post_id, $user->ID ) );

			$timeout = (int) get_option( Hydro_Raindrop_Helper::OPTION_POST_VERIFICATION_TIMEOUT, 3600 );

			delete_transient( sprintf( self::TRANSIENT_ID_POST_VERIFICATION, $user->ID ) );

			$message = 'Verification expires at ' . date( 'Y-m-d H:i:s', time() + $timeout );

			set_transient(
				sprintf( self::TRANSIENT_ID_POST_VERIFIED, $user->ID, $post_id ),
				$message,
				$timeout
			);

			$this->log( $message );
		}

		/*
		 * Disable Hydro Raindrop MFA.
		 */
		$hydro_raindrop_mfa_method = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, true );

		if ( Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED !== $hydro_raindrop_mfa_method
				&& $this->is_action_disable()
		) {
			$client = Hydro_Raindrop::get_raindrop_client();

			// @codingStandardsIgnoreStart
			$hydro_id = get_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, true );

			$this->log( 'Attempting to unregister HydroID ' . $hydro_id );

			try {
				$client->unregisterUser( $hydro_id );

				delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID );
				delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED );
				delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED );
				delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS );
				delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED );

			} catch ( UnregisterUserFailed $e ) {
				$this->log( 'Could not unregister user: ' . $e->getMessage() );
			}
			// @codingStandardsIgnoreEnd
		}

		// @codingStandardsIgnoreLine
		$redirect_to = get_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_REDIRECT_URL, true );

		if ( $redirect_to ) {
			$this->log( sprintf( 'Found User Meta Redirect URL %s ', $redirect_to ) );
			// @codingStandardsIgnoreStart
			delete_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_REDIRECT_URL );
			wp_redirect( $redirect_to );
			exit();
			// @codingStandardsIgnoreEnd
		}

		// Redirect the user to it's intended location.
		$this->redirect( $user );

	}

	/**
	 * Handle a MFA failure.
	 *
	 * @param WP_User $user The user for which the MFA has failed.
	 *
	 * @return void
	 */
	private function handle_mfa_failure( WP_User $user ) {

		$this->log( 'MFA failed.' );

		$flash = new Hydro_Raindrop_Flash( $user->user_login );
		$flash->error( esc_html__( 'Authentication failed.', 'wp-hydro-raindrop' ) );

		$this->delete_transient_data( $user );

		$meta_key = Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS;

		// @codingStandardsIgnoreLine
		$failed_attempts = (int) get_user_meta( $user->ID, $meta_key, true );

		// @codingStandardsIgnoreLine
		update_user_meta( $user->ID, $meta_key, ++ $failed_attempts );

		$this->log( 'MFA failed attempts: ' . $failed_attempts );

		do_action( Hydro_Raindrop_Helper::ACTION_MFA_FAILED, $user, $failed_attempts );

		/*
		 * Block user account if maximum MFA attempts has been reached.
		 */
		$maximum_attempts = (int) get_option( Hydro_Raindrop_Helper::OPTION_MFA_MAXIMUM_ATTEMPTS );

		if ( $maximum_attempts > 0 && $failed_attempts > $maximum_attempts ) {
			// @codingStandardsIgnoreStart
			update_user_meta( $user->ID, $meta_key, 0 );
			update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED, true );

			$flash->error( esc_html__( 'Your account has been blocked.', 'wp-hydro-raindrop' ) );

			$this->cookie->unset();

			do_action( Hydro_Raindrop_Helper::ACTION_USER_BLOCKED, $user );

			wp_logout();

			$redirect_to = apply_filters( 'logout_redirect', wp_login_url(), $_REQUEST['redirect_to'] ?? '', $user );
			wp_safe_redirect( $redirect_to );
			exit();
			// @codingStandardsIgnoreEnd
		}

	}

	/**
	 * Handle Settings POST Request.
	 *
	 * @param WP_User $user Authenticated user.
	 *
	 * @return bool
	 */
	private function handle_settings( WP_User $user ) : bool {

		$public = new Hydro_Raindrop_Public( $this->plugin_name, $this->version );
		$public->update_extra_profile_fields( $user->ID );

		return false;

	}

	/**
	 * Set the WP Auth Cookie.
	 *
	 * @param WP_User $user     Authenticated user.
	 * @param bool    $remember Whether to remember the user.
	 *
	 * @return void
	 */
	private function set_auth_cookie( WP_User $user, bool $remember = false ) {

		wp_set_auth_cookie( $user->ID, $remember ); // TODO: Remember login parameter.

	}

	/**
	 * Redirects user after successful login and MFA.
	 *
	 * @param WP_User $user Current logged in user.
	 *
	 * @return void
	 */
	private function redirect( WP_User $user ) {

		if ( $this->is_request_verify() && $this->helper->is_setup_page_enabled() ) {

			// @codingStandardsIgnoreLine
			wp_redirect( $this->helper->get_setup_page_url() );
			exit();

		}

		// @codingStandardsIgnoreLine
		if ( isset( $_REQUEST['redirect_to'] ) ) {
			// @codingStandardsIgnoreLine
			$redirect_to = $_REQUEST['redirect_to'];
		} else {
			$redirect_to = admin_url();
		}

		// @codingStandardsIgnoreLine
		$requested_redirect_to = $_REQUEST['redirect_to'] ?? '';

		/**
		 * Filters the login redirect URL.
		 *
		 * @since 3.0.0
		 *
		 * @param string           $redirect_to           The redirect destination URL.
		 * @param string           $requested_redirect_to The requested redirect destination URL passed as a parameter.
		 * @param WP_User|WP_Error $user                  WP_User object if login was successful, WP_Error object otherwise.
		 */
		$redirect_to = apply_filters( 'login_redirect', $redirect_to, $requested_redirect_to, $user );

		if ( ( empty( $redirect_to ) || 'wp-admin/' === $redirect_to || admin_url() === $redirect_to ) ) {
			/*
			 * If the user doesn't belong to a blog, send them to user admin.
			 * If the user can't edit posts, send them to their profile.
			 */
			if ( is_multisite() && ! get_active_blog_for_user( $user->ID ) && ! is_super_admin( $user->ID ) ) {
				$redirect_to = user_admin_url();
			} elseif ( is_multisite() && ! $user->has_cap( 'read' ) ) {
				$redirect_to = get_dashboard_url( $user->ID );
			} elseif ( ! $user->has_cap( 'edit_posts' ) ) {
				$redirect_to = $user->has_cap( 'read' ) ? admin_url( 'profile.php' ) : $this->helper->get_home_url();
			}

			// @codingStandardsIgnoreLine
			wp_redirect( $redirect_to );
			exit();
		}

		wp_safe_redirect( $redirect_to );
		exit();

	}

	/**
	 * Get the users' HydroID.
	 *
	 * @param WP_User $user Current logged in user.
	 *
	 * @return string
	 */
	private function get_user_hydro_id( WP_User $user ) : string {

		// @codingStandardsIgnoreLine
		return (string) get_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, true );

	}

	/**
	 * Whether this is a request for verification.
	 *
	 * @return bool
	 */
	private function is_request_verify() : bool {

		// @codingStandardsIgnoreLine
		return (int) ( $_GET['hydro-raindrop-verify'] ?? 0 ) === 1;

	}

	/**
	 * Whether to disable Hydro Raindrop MFA.
	 *
	 * @return bool
	 */
	private function is_action_disable() : bool {

		// @codingStandardsIgnoreLine
		return ( $_GET['hydro-raindrop-action'] ?? '' ) === 'disable';

	}

	/**
	 * Whether to enable Hydro Raindrop MFA.
	 *
	 * @return bool
	 */
	private function is_action_enable() : bool {

		// @codingStandardsIgnoreLine
		return ( $_GET['hydro-raindrop-action'] ?? '' ) === 'enable';

	}

	/**
	 * Checks whether given user requires Hydro Raindrop MFA.
	 *
	 * @param WP_User $user An authenticated user.
	 *
	 * @return bool
	 */
	private function user_requires_mfa( WP_User $user ) : bool {

		// @codingStandardsIgnoreStart

		$hydro_id                 = $this->get_user_hydro_id( $user );
		$hydro_mfa_enabled        = (bool) get_user_meta(
			$user->ID,
			Hydro_Raindrop_Helper::USER_META_MFA_ENABLED,
			true
		);
		$hydro_raindrop_confirmed = (bool) get_user_meta(
			$user->ID,
			Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED,
			true
		);

		// @codingStandardsIgnoreEnd

		return ! empty( $hydro_id )
			&& $hydro_mfa_enabled
			&& ( $hydro_raindrop_confirmed || $this->is_request_verify() );

	}

	/**
	 * Checks whether given User requires to set up Hydro Raindrop MFA.
	 *
	 * @param WP_User $user An authenticated user.
	 *
	 * @return bool
	 */
	private function user_requires_setup_mfa( WP_User $user ) : bool {

		// User wants to enable Hydro Raindrop MFA from user profile.
		if ( $this->is_action_enable() ) {
			return true;
		}

		$method = get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD );

		switch ( $method ) {
			case Hydro_Raindrop_Helper::MFA_METHOD_OPTIONAL:
				return false;
			case Hydro_Raindrop_Helper::MFA_METHOD_PROMPTED:
			case Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED:
				return ! $this->user_requires_mfa( $user );
		}

		return false;

	}

	/**
	 * Delete any transient data for current user.
	 *
	 * @param WP_User $user User.
	 *
	 * @return void
	 */
	private function delete_transient_data( WP_User $user ) {

		$transient_id = sprintf( self::TRANSIENT_ID_MESSAGE, $user->ID );

		delete_transient( $transient_id );

		$this->log( 'Deleted transient data.' );

	}

	/**
	 * Perform Hydro Raindrop signature verification.
	 *
	 * @param WP_User $user The user to verify the signature login for.
	 *
	 * @return bool
	 */
	private function verify_signature_login( WP_User $user ) : bool {

		$client = Hydro_Raindrop::get_raindrop_client();

		try {
			$hydro_id     = $this->get_user_hydro_id( $user );
			$transient_id = sprintf( self::TRANSIENT_ID_MESSAGE, $user->ID );
			$message      = (int) get_transient( $transient_id );

			$client->verifySignature( $hydro_id, $message );

			$this->delete_transient_data( $user );

			if ( $this->is_request_verify() ) {
				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED, 1 );

				do_action( Hydro_Raindrop_Helper::ACTION_SETUP_SUCCESS, $user, $hydro_id );
			}

			return true;
		} catch ( VerifySignatureFailed $e ) {
			$this->log( $e->getMessage() );

			return false;
		}

	}

	/**
	 * Log message.
	 *
	 * @param string $message The message to log.
	 *
	 * @return void
	 */
	private function log( string $message ) {

		if ( WP_DEBUG && WP_DEBUG_LOG ) {
			// @codingStandardsIgnoreLine
			error_log( $this->plugin_name . ' (' . $this->version . '): ' . $message );
		}

	}

}
