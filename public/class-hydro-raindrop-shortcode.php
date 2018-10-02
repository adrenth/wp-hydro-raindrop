<?php

declare( strict_types=1 );

/**
 * Shortcode class
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
class Hydro_Raindrop_Shortcode {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Show flash messages.
	 *
	 * @return string
	 */
	public function mfa_flash() : string {

		$user = $this->get_user();

		if ( $user ) {
			return ( new Hydro_Raindrop_Flash( $user->user_login ) )->render();
		}

		return '';

	}

	/**
	 * Open <form> tag for the MFA page.
	 *
	 * @return string
	 */
	public function mfa_form_open() : string {

		return '<form action="" method="post">';

	}

	/**
	 * Closing </form> tag for the MFA page.
	 *
	 * @return string
	 */
	public function mfa_form_close() : string {

		return wp_nonce_field( 'hydro_raindrop_mfa' ) . '</form>';

	}

	/**
	 * MFA digits for the MFA page.
	 *
	 * @return string
	 * @throws Exception When message cannot be generated.
	 */
	public function mfa_digits() : string {

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
		} else {
			$authenticate = new Hydro_Raindrop_Authenticate( $this->plugin_name, $this->version );
			$user         = $authenticate->get_current_mfa_user();
		}

		if ( ! ( $user instanceof WP_User ) ) {
			return '';
		}

		return (string) Hydro_Raindrop_Authenticate::get_message( $user );

	}

	/**
	 * MFA authorize button for the MFA page.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function mfa_button_authorize( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-mfa-button-authorize',
			'label' => esc_html__( 'Authenticate', 'wp-hydro-raindrop' ),
		], $attributes);

		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'hydro_raindrop_mfa',
			$attributes['class'],
			$attributes['label']
		);

	}

	/**
	 * MFA cancel button for the MFA page.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function mfa_button_cancel( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-mfa-button-cancel',
			'label' => esc_html__( 'Cancel', 'wp-hydro-raindrop' ),
		], $attributes);

		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'hydro_raindrop_mfa_cancel',
			$attributes['class'],
			$attributes['label']
		);

	}

	/**
	 * Show flash messages.
	 *
	 * @return string
	 */
	public function setup_flash() : string {

		$user = $this->get_user();

		if ( $user ) {
			return ( new Hydro_Raindrop_Flash( $user->user_login ) )->render();
		}

		return '';

	}

	/**
	 * Open <form> tag for the Setup page.
	 *
	 * @return string
	 */
	public function setup_form_open() : string {

		return '<form action="" method="post">';

	}

	/**
	 * Closing </form> tag for the Setup page.
	 *
	 * @return string
	 */
	public function setup_form_close() : string {

		return wp_nonce_field( 'hydro_raindrop_setup' ) . '</form>';

	}

	/**
	 * Renders the HydroID input field.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function setup_hydro_id( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-setup-hydro-id',
		], $attributes);

		return sprintf(
			'<input type="text" name="%s" class="%s" title="HydroID" autocomplete="off" autofocus>',
			'hydro_id',
			$attributes['class']
		);

	}

	/**
	 * Renders the Setup page Submit button.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function setup_button_submit( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-setup-button-submit',
			'label' => esc_html__( 'Submit', 'wp-hydro-raindrop' ),
		], $attributes);

		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'hydro_raindrop_setup',
			$attributes['class'],
			$attributes['label']
		);

	}

	/**
	 * Renders the Setup page Skip button (if applicable).
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function setup_button_skip( $attributes ) : string {

		$method = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD );

		if ( Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED === $method ) {
			return '';
		}

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-setup-button-skip',
			'label' => esc_html__( 'Skip', 'wp-hydro-raindrop' ),
		], $attributes);

		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s">',
			'hydro_raindrop_setup_skip',
			$attributes['class'],
			$attributes['label']
		);

	}

	/**
	 * Show flash messages.
	 *
	 * @return string
	 */
	public function settings_flash() : string {

		$user = $this->get_user();

		if ( $user ) {
			return ( new Hydro_Raindrop_Flash( $user->user_login ) )->render();
		}

		return '';

	}

	/**
	 * Open <form> tag for the Settings page.
	 *
	 * @return string
	 */
	public function settings_form_open() : string {

		return '<form action="" method="post">';

	}

	/**
	 * Closing </form> tag for the Settings page.
	 *
	 * @return string
	 */
	public function settings_form_close() : string {

		return wp_nonce_field( 'hydro_raindrop_settings' ) . '</form>';

	}

	/**
	 * Render checkbox for enabling/disabling MFA.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function settings_checkbox_mfa_enabled( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-setup-button-skip',
			'label' => esc_html__( 'Skip', 'wp-hydro-raindrop' ),
		], $attributes);

		$user = $this->get_user();

		if ( ! $user ) {
			return '';
		}

		// @codingStandardsIgnoreStart
		$hydro_raindrop_mfa_method  = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, true );
		$hydro_raindrop_mfa_enabled = (bool) get_user_meta(
			$user->ID,
			Hydro_Raindrop_Helper::USER_META_MFA_ENABLED,
			true
		);
		// @codingStandardsIgnoreEnd

		return sprintf(
			'<label class="%s"><input name="%s" type="checkbox" value="1"%s%s>%s</label>',
			$attributes['class'],
			Hydro_Raindrop_Helper::USER_META_MFA_ENABLED,
			$hydro_raindrop_mfa_enabled ? ' checked' : '',
			Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED === $hydro_raindrop_mfa_method ? ' disabled' : '',
			esc_html__( 'Enable Multi Factor Authentication', 'wp-hydro-raindrop' )
		);

	}

	/**
	 * Renders the Settings page Submit button.
	 *
	 * @param mixed $attributes Shortcode attributes.
	 * @return string
	 */
	public function settings_button_submit( $attributes ) : string {

		$attributes = shortcode_atts( [
			'class' => 'hydro-raindrop-settings-button-submit',
			'label' => esc_html__( 'Submit', 'wp-hydro-raindrop' ),
		], $attributes);

		$user = $this->get_user();

		if ( ! $user ) {
			return '';
		}

		$hydro_raindrop_mfa_method = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, true );

		return sprintf(
			'<input type="submit" name="%s" class="%s" value="%s"%s>',
			'hydro_raindrop_settings',
			$attributes['class'],
			$attributes['label'],
			Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED === $hydro_raindrop_mfa_method ? ' disabled' : ''
		);

	}

	/**
	 * Get the current User.
	 *
	 * @return null|WP_User
	 */
	private function get_user() {

		if ( is_user_logged_in() ) {
			$user = wp_get_current_user();
		} else {
			$user = ( new Hydro_Raindrop_Authenticate( $this->plugin_name, $this->version ) )->get_current_mfa_user();
		}

		return $user;

	}

}
