<?php

declare( strict_types=1 );

use Adrenth\Raindrop\Exception\VerifySignatureFailed;

/** @noinspection AutoloadingIssuesInspection */

/**
 * Class Hydro_Raindrop_Authenticate
 */
final class Hydro_Raindrop_Authenticate {

	const MESSAGE_TRANSIENT_ID = 'HydroRaindropMessage_%s';

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.0.0
	 * @var     string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.0.0
	 * @var     string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Helper.
	 *
	 * @since   1.3.0
	 * @var     Hydro_Raindrop_Helper $helper
	 */
	private $helper;

	/**
	 * Cookie helper.
	 *
	 * @since   1.3.0
	 * @var     Hydro_Raindrop_Cookie $cookie
	 */
	private $cookie;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;
		$this->helper      = new Hydro_Raindrop_Helper();
		$this->cookie      = new Hydro_Raindrop_Cookie( $plugin_name, $version );

	}

	/**
	 * Hook into the WordPress sign-on flow.
	 *
	 * @param string      $user_login    Username which was entered in the sign-on form.
	 * @param string|null $user_password The password which was entered in the sign-on form.
	 *
	 * @throws Exception
	 */
	public function authenticate( string $user_login = null, string $user_password = null ) {

		if ( ! $user_login || ! username_exists( $user_login ) ) {
			return;
		}

		if ( ! $this->is_hydro_raindrop_mfa_enabled() ) {
			return;
		}

		if ( ! is_ssl() ) {
			$this->log( 'SSL disabled, skipping Hydro Raindrop MFA.' );
			return;
		}

		$user = wp_authenticate( $user_login, $user_password );

		if ( ! ( $user instanceof WP_User ) ) {
			return;
		}

		if ( $this->user_requires_mfa( $user ) ) {
			$this->log( 'User authenticates and requires Hydro Raindrop MFA.' );
			$this->delete_transient_data( $user );
			$this->cookie->set( $user->ID );
			$this->start_mfa( $user );
		}

	}

	/**
	 * Verify request.
	 *
	 * @return void
	 * @throws Exception When MFA could not be started.
	 */
	public function verify() {

		$this->verify_post_request();

		// Perform first time verification.
		if ( is_user_logged_in() && $this->is_first_time_verify() ) {

			$user = wp_get_current_user();

			$this->log( 'Start first time verification.' );
			$this->delete_transient_data( $user );
			$this->cookie->set( $user->ID );
			$this->start_mfa( $user );

			return;

		}

		// Allow administrator to view the MFA page.
		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();

			if ( user_can( $user, 'administrator' )
					&& $this->helper->is_custom_mfa_page_enabled()
					&& $this->helper->get_current_url() === $this->helper->get_custom_mfa_page_url() ) {
				return;
			}
		}

		$cookie_is_valid = $this->cookie->validate();

		// Protect custom MFA page.
		if ( ! $cookie_is_valid
			&& $this->helper->is_custom_mfa_page_enabled()
			&& $this->helper->get_current_url() === $this->helper->get_custom_mfa_page_url() ) {
			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit;
		}

		if ( $cookie_is_valid ) {
			// Redirect to MFA page if not already.
			if ( $this->helper->is_custom_mfa_page_enabled()
					&& ! in_array(
						$this->helper->get_current_url(),
						[
							$this->helper->get_custom_mfa_page_url(),
							$this->helper->get_custom_mfa_page_url() . '?hydro-raindrop-error=1',
						],
						true
					)
			) {
				$this->log( 'User not on Hydro Raindrop MFA page. Redirecting...' );

				// @codingStandardsIgnoreLine
				wp_redirect( $this->helper->get_custom_mfa_page_url() );
				exit;
			}

			// Render MFA page if not on login page.
			if ( ! $this->helper->is_custom_mfa_page_enabled()
					&& strpos( $this->helper->get_current_url(), 'wp-login.php' ) !== false
			) {
				$this->log( 'User not on Hydro Raindrop MFA page. Render MFA page.' );

				$user = $this->get_current_mfa_user();
				$this->start_mfa( $user );
			}
		}

	}

	/**
	 * Get's the current MFA user from cookie.
	 *
	 * @return WP_User|null
	 */
	public function get_current_mfa_user() {

		$user_id = $this->cookie->validate();

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

		$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );

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
		// @codingStandardsIgnoreLine
		$is_post = $_SERVER['REQUEST_METHOD'] === 'POST';

		if ( ! $is_post || ! is_ssl() ) {
			return;
		}

		// @codingStandardsIgnoreLine
		$retrieved_nonce = $_POST['_wpnonce'] ?? null;

		$user = $this->get_current_mfa_user();

		// @codingStandardsIgnoreLine
		if ( isset( $_POST['hydro_raindrop'] )
				&& ! wp_verify_nonce( $retrieved_nonce, 'hydro_raindrop_mfa' )
		) {
			$this->log( 'Nonce verification failed. Logging out.' );

			$this->cookie->unset();

			// Delete all transient data which is used during the MFA process.
			if ( $user ) {
				$this->delete_transient_data( $user );
			}

			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit;
		}

		// Verify MFA message
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['hydro_raindrop'] ) && $user ) {

			if ( $this->verify_signature_login( $user ) ) {
				$this->log( 'MFA success.' );

				$this->cookie->unset();

				// Delete all transient data which is used during the MFA process.
				if ( $user ) {
					$this->delete_transient_data( $user );
				}

				wp_set_auth_cookie( $user->ID ); // TODO: Remember login parameter.

				// Redirect the user to it's intended location.
				$this->redirect( $user );
			} else {
				$this->log( 'MFA failed.' );

				$this->delete_transient_data( $user );

				$to_url = $this->helper->get_current_url();

				if ( $this->helper->is_custom_mfa_page_enabled() ) {
					$to_url = $this->helper->get_custom_mfa_page_url();
				}

				if ( strpos( $to_url, '?hydro-raindrop-error=1' ) === false ) {
					$to_url .= '?hydro-raindrop-error=1';
				}

				// @codingStandardsIgnoreLine
				wp_redirect( $to_url );

				exit;
			}

		}

		// Allow user to cancel the MFA. Which results in a logout.
		// @codingStandardsIgnoreLine
		if ( isset( $_POST['cancel_hydro_raindrop'] )
				&& wp_verify_nonce( $retrieved_nonce, 'hydro_raindrop_mfa' )
		) {
			$this->log( 'User cancels MFA.' );

			$this->cookie->unset();

			// Delete all transient data which is used during the MFA process.
			if ( $user ) {
				$this->delete_transient_data( $user );
			}

			// @codingStandardsIgnoreLine
			wp_redirect( home_url() );
			exit;
		}

	}

	/**
	 * Start Hydro Raindrop Multi Factor Authentication.
	 *
	 * @param WP_User $user Logged in user.
	 *
	 * @throws Exception If message could not be generated.
	 */
	private function start_mfa( WP_User $user ) {

		$this->log( 'Start MFA.' );

		$error = null;

		/*
		 * The authentication failed. Delete transient data to make sure a new message will be generated.
		 */
		// @codingStandardsIgnoreLine
		if ( $_SERVER['REQUEST_METHOD'] === 'POST' && isset( $_REQUEST['hydro_raindrop'] ) ) {
			$this->log( 'Authentication failed.' );

			$error = 'Authentication failed.';

			$this->delete_transient_data( $user );
		}

		/*
		 * Redirect to the Custom MFA page (if applicable).
		 */
		if ( $this->helper->is_custom_mfa_page_enabled() ) {

			$this->log( 'Custom MFA page is enabled.' );

			if ( strpos( $this->helper->get_current_url(), $this->helper->get_custom_mfa_page_url() ) !== 0 ) {
				// @codingStandardsIgnoreLine
				wp_redirect( $this->helper->get_custom_mfa_page_url() );
				exit;
			}

			return;
		}

		/*
		 * Display the default (non customizable) MFA page.
		 */
		$message = self::get_message( $user );
		$logo    = plugin_dir_url( __FILE__ ) . 'images/logo.svg';
		$image   = plugin_dir_url( __FILE__ ) . 'images/security-code.png';

		require __DIR__ . '/partials/hydro-raindrop-public-mfa.php';
		exit;

	}

	/**
	 * Redirects user after successful login and MFA.
	 *
	 * @param WP_User $user Current logged in user.
	 *
	 * @return void
	 */
	private function redirect( WP_User $user ) {

		if ( $this->is_first_time_verify() && $this->helper->is_custom_hydro_id_page_enabled() ) {

			// @codingStandardsIgnoreLine
			wp_redirect( $this->helper->get_custom_hydro_id_page_url() );
			exit;

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

		if ( ( empty( $redirect_to ) || $redirect_to === 'wp-admin/' || $redirect_to === admin_url() ) ) {
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
			exit;
		}

		wp_safe_redirect( $redirect_to );
		exit;

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
		return (string) get_user_meta( $user->ID, 'hydro_id', true );

	}

	/**
	 * Whether the Hydro Raindrop MFA is enabled.
	 *
	 * @return bool
	 */
	private function is_hydro_raindrop_mfa_enabled() : bool {

		return true;

	}

	/**
	 * Whether this is the first time verification.
	 *
	 * @return bool
	 */
	private function is_first_time_verify() : bool {

		// @codingStandardsIgnoreLine
		return (int) ($_GET['hydro-raindrop-verify'] ?? 0) === 1;

	}

	/**
	 * Checks whether current user requires Hydro Raindrop MFA.
	 *
	 * @param WP_User $user Currently logged in user.
	 *
	 * @return bool
	 */
	private function user_requires_mfa( WP_User $user ) : bool {

		// @codingStandardsIgnoreLine
		$hydro_id = $this->get_user_hydro_id( $user );

		// @codingStandardsIgnoreLine
		$hydro_mfa_enabled = (bool) get_user_meta( $user->ID, 'hydro_mfa_enabled', true );

		// @codingStandardsIgnoreLine
		$hydro_raindrop_confirmed = (bool) get_user_meta( $user->ID, 'hydro_raindrop_confirmed', true );

		return ! empty( $hydro_id )
			&& $hydro_mfa_enabled
			&& ( $hydro_raindrop_confirmed || $this->is_first_time_verify() );

	}

	/**
	 * Delete any transient data for current user.
	 *
	 * @param WP_User $user User.
	 *
	 * @return void
	 */
	private function delete_transient_data( WP_User $user ) {

		$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );

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
			$transient_id = sprintf( self::MESSAGE_TRANSIENT_ID, $user->ID );
			$message      = (int) get_transient( $transient_id );

			$client->verifySignature( $hydro_id, $message );

			$this->delete_transient_data( $user );

			if ( $this->is_first_time_verify() ) {
				// @codingStandardsIgnoreLine
				update_user_meta( $user->ID, 'hydro_raindrop_confirmed', 1 );
			}

			return true;
		} catch ( VerifySignatureFailed $e ) {
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
