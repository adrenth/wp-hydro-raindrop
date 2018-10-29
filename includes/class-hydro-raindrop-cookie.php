<?php

declare( strict_types=1 );

/**
 * Helper class for Hydro Raindrop MFA cookie.
 *
 * @since      1.3.0
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
class Hydro_Raindrop_Cookie {

	/**
	 * Cookie name.
	 *
	 * @var string
	 */
	const NAME = 'hydro_raindrop_cookie_mfa';

	/**
	 * Multi Factor Timeout in seconds.
	 *
	 * The user needs to verify the MFA message within MFA_TIME_OUT seconds.
	 *
	 * @var int
	 */
	const MFA_TIME_OUT = 90;

	/**
	 * The ID of this plugin.
	 *
	 * @since   1.3.0
	 * @var     string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since   1.3.0
	 * @var     string $version The current version of this plugin.
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
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Set the Hydro Raindrop MFA cookie.
	 *
	 * @param int $user_id ID of authenticated user.
	 *
	 * @return bool
	 */
	public function set( int $user_id ) {

		$user = get_userdata( $user_id );

		if ( ! $user ) {
			return false;
		}

		$expiration = time() + self::MFA_TIME_OUT;
		$manager    = WP_Session_Tokens::get_instance( $user_id );
		$token      = $manager->create( $expiration );
		$pass_frag  = substr( $user->user_pass, 8, 4 );
		$key        = wp_hash( $user->user_login . '|' . $pass_frag . '|' . $expiration . '|' . $token );
		$algorithm  = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$hash       = hash_hmac( $algorithm, $user->user_login . '|' . $expiration . '|' . $token, $key );
		$cookie     = $user->user_login . '|' . $expiration . '|' . $token . '|' . $hash;

		// @codingStandardsIgnoreLine
		setcookie( self::NAME, $cookie, 0, COOKIEPATH, (string) COOKIE_DOMAIN, true, true );

		if ( COOKIEPATH !== SITECOOKIEPATH ) {
			// @codingStandardsIgnoreLine
			setcookie( self::NAME, $cookie, 0, SITECOOKIEPATH, (string) COOKIE_DOMAIN, true, true );
		}

	}

	/**
	 * Parse the Hydro Raindrop MFA cookie.
	 *
	 * @return array|bool
	 */
	public function parse() {

		// @codingStandardsIgnoreLine
		if ( empty( $_COOKIE[ self::NAME ] ) ) {
			return false;
		}

		// @codingStandardsIgnoreLine
		$cookie = $_COOKIE[ self::NAME ];

		$cookie_elements = explode( '|', $cookie );

		if ( count( $cookie_elements ) !== 4 ) {
			return false;
		}

		list( $username, $expiration, $token, $hmac ) = $cookie_elements;

		return compact( 'username', 'expiration', 'token', 'hmac' );

	}

	/**
	 * Validate the Hydro Raindrop MFA cookie.
	 *
	 * Returns the User ID when validates or FALSE when invalidates.
	 *
	 * @return bool|int
	 * @throws Hydro_Raindrop_CookieExpired When cookie is expired.
	 */
	public function validate() {
		/**
		 * The cookie elements.
		 *
		 * @var array $cookie_elements
		 */
		$cookie_elements = $this->parse();

		if ( ! $cookie_elements ) {
			return false;
		}

		$username   = $cookie_elements['username'];
		$hmac       = $cookie_elements['hmac'];
		$token      = $cookie_elements['token'];
		$expiration = $cookie_elements['expiration'];

		if ( $expiration < time() ) {

			$this->unset();

			throw new Hydro_Raindrop_CookieExpired( 'MFA Cookie expired!' );

		}

		$user = get_user_by( 'login', $username );

		if ( ! $user ) {
			return false;
		}

		$pass_frag = substr( $user->user_pass, 8, 4 );
		$key       = wp_hash( $username . '|' . $pass_frag . '|' . $expiration . '|' . $token );
		$algorithm = function_exists( 'hash' ) ? 'sha256' : 'sha1';
		$hash      = hash_hmac( $algorithm, $username . '|' . $expiration . '|' . $token, $key );

		if ( ! hash_equals( $hash, $hmac ) ) {
			return false;
		}

		$manager = WP_Session_Tokens::get_instance( $user->ID );

		if ( ! $manager->verify( $token ) ) {
			return false;
		}

		return $user->ID;

	}

	/**
	 * Unset the Hydro Raindrop MFA cookie
	 *
	 * @return void
	 */
	public function unset() {

		// @codingStandardsIgnoreLine
		setcookie( self::NAME, '', strtotime( '-1 day' ), (string) COOKIEPATH, (string) COOKIE_DOMAIN );
		// @codingStandardsIgnoreLine
		setcookie( self::NAME, '', strtotime( '-1 day' ), (string) SITECOOKIEPATH, (string) COOKIE_DOMAIN );

	}

}
