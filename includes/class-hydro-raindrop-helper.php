<?php

declare( strict_types=1 );

/**
 * A helper class
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 */
final class Hydro_Raindrop_Helper {

	/**
	 * Hydro Raindrop database options.
	 */
	const OPTION_ENABLED                   = 'hydro_raindrop_enabled';
	const OPTION_APPLICATION_ID            = 'hydro_raindrop_application_id';
	const OPTION_CLIENT_ID                 = 'hydro_raindrop_client_id';
	const OPTION_CLIENT_SECRET             = 'hydro_raindrop_client_secret';
	const OPTION_ENVIRONMENT               = 'hydro_raindrop_environment';
	const OPTION_ACTIVATION_NOTICE         = 'hydro_raindrop_activation_notice';
	const OPTION_ACCESS_TOKEN_SUCCESS      = 'hydro_raindrop_access_token_success';
	const OPTION_MFA_METHOD                = 'hydro_raindrop_mfa_method';
	const OPTION_MFA_MAXIMUM_ATTEMPTS      = 'hydro_raindrop_mfa_maximum_attempts';
	const OPTION_POST_VERIFICATION_TIMEOUT = 'hydro_raindrop_post_verification_timeout';

	/**
	 * Hydro Raindrop Pages.
	 */
	const OPTION_PAGE_MFA      = 'hydro_raindrop_page_mfa';
	const OPTION_PAGE_SETUP    = 'hydro_raindrop_page_setup';
	const OPTION_PAGE_SETTINGS = 'hydro_raindrop_page_settings';

	/**
	 * Hydro Raindrop user meta.
	 */
	const USER_META_HYDRO_ID            = 'hydro_raindrop_hydro_id'; // TODO: Prior 2.0.0 -> `hydro_id`.
	const USER_META_MFA_ENABLED         = 'hydro_raindrop_mfa_enabled'; // TODO: Prior 2.0.0 -> `hydro_mfa_enabled`.
	const USER_META_MFA_CONFIRMED       = 'hydro_raindrop_mfa_confirmed'; // TODO: Prior 2.0.0 -> `hydro_raindrop_confirmed`.
	const USER_META_MFA_FAILED_ATTEMPTS = 'hydro_raindrop_mfa_failed_attempts';
	const USER_META_ACCOUNT_BLOCKED     = 'hydro_raindrop_account_blocked';
	const USER_META_REDIRECT_URL        = 'hydro_raindrop_redirect_url';

	/**
	 * Hydro Raindrop post meta.
	 */
	const POST_META_MFA_REQUIRED = 'hydro_raindrop_mfa_required';

	/**
	 * MFA Method
	 */
	const MFA_METHOD_ENFORCED = 'enforced';
	const MFA_METHOD_PROMPTED = 'prompted';
	const MFA_METHOD_OPTIONAL = 'optional';

	/**
	 * Actions
	 */
	const ACTION_USER_BLOCKED  = 'hydro_raindrop_user_blocked';
	const ACTION_MFA_FAILED    = 'hydro_raindrop_mfa_failed';
	const ACTION_MFA_SUCCESS   = 'hydro_raindrop_mfa_success';
	const ACTION_SETUP_FAILED  = 'hydro_raindrop_setup_failed';
	const ACTION_SETUP_SUCCESS = 'hydro_raindrop_setup_success';
	const ACTION_PRE_SETUP_MFA = 'hydro_raindrop_pre_setup';
	const ACTION_PRE_MFA       = 'hydro_raindrop_pre_mfa';

	/**
	 * Cookie Flags
	 */
	const COOKIE_MFA_TIMED_OUT = 'hydro_raindrop_cookie_mfa_timed_out';

	/**
	 * Construct the Helper.
	 */
	public function __construct() {

	}

	/**
	 * Get the home URL.
	 *
	 * @return string
	 */
	public function get_home_url() : string {

		return home_url();

	}

	/**
	 * Get the current URL.
	 *
	 * @param bool $without_action Whether to strip the Hydro Raindrop Action parameter.
	 *
	 * @return string
	 */
	public function get_current_url( bool $without_action = false ) : string {

		$current_url = home_url( add_query_arg( null, null ) );

		if ( $without_action ) {
			$current_url = str_replace(
				[
					'?hydro-raindrop-action=enable',
					'&hydro-raindrop-action=enable',
				],
				'',
				$current_url
			);
		}

		return $current_url;

	}

	/**
	 * Get the MFA page URL.
	 *
	 * @return string
	 */
	public function get_mfa_page_url() : string {

		$post_id = (int) get_option( self::OPTION_PAGE_MFA );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Get the MFA page ID.
	 *
	 * @return int|bool
	 */
	public function get_mfa_page_id() {

		$post_id = (int) get_option( self::OPTION_PAGE_MFA );

		return $post_id > 0 ? $post_id : false;

	}

	/**
	 * Get the HydroID page URL.
	 *
	 * @return string
	 */
	public function get_setup_page_url() : string {

		$post_id = (int) get_option( self::OPTION_PAGE_SETUP );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Get the HydroID page ID.
	 *
	 * @return int|bool
	 */
	public function get_setup_page_id() {

		$post_id = (int) get_option( self::OPTION_PAGE_SETUP );

		return $post_id > 0 ? $post_id : false;

	}

	/**
	 * Get the Settings page URL.
	 *
	 * @return string
	 */
	public function get_settings_page_url() : string {

		$post_id = (int) get_option( self::OPTION_PAGE_SETTINGS );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Get the Settings page ID.
	 *
	 * @return int|bool
	 */
	public function get_settings_page_id() {

		$post_id = (int) get_option( self::OPTION_PAGE_SETTINGS );

		return $post_id > 0 ? $post_id : false;

	}

	/**
	 * Checks if MFA page is present and enabled.
	 *
	 * @return bool
	 */
	public function is_mfa_page_enabled() : bool {

		return $this->is_page_enabled_with_option( self::OPTION_PAGE_MFA );

	}

	/**
	 * Checks if HydroID page is present and enabled.
	 *
	 * @return bool
	 */
	public function is_setup_page_enabled() : bool {

		return $this->is_page_enabled_with_option( self::OPTION_PAGE_SETUP );

	}

	/**
	 * Checks if Settings page is present and enabled.
	 *
	 * @return bool
	 */
	public function is_settings_page_enabled() : bool {

		return $this->is_page_enabled_with_option( self::OPTION_PAGE_SETTINGS );

	}

	/**
	 * Whether the MFA page is present.
	 *
	 * @return bool
	 */
	public function is_mfa_page_present() : bool {

		return $this->is_page_present_with_option( self::OPTION_PAGE_MFA );

	}

	/**
	 * Whether the HydroID page is present.
	 *
	 * @return bool
	 */
	public function is_setup_page_present() : bool {

		return $this->is_page_present_with_option( self::OPTION_PAGE_SETUP );

	}

	/**
	 * Whether the Settings page is present.
	 *
	 * @return bool
	 */
	public function is_settings_page_present() : bool {

		return $this->is_page_present_with_option( self::OPTION_PAGE_SETTINGS );

	}

	/**
	 * Create the MFA page.
	 *
	 * @return int Returns 0 on failure, otherwise a valid post ID when creation is success.
	 */
	public function create_mfa_page() : int {

		if ( $this->is_mfa_page_present() ) {
			return (int) get_option( self::OPTION_PAGE_MFA );
		}

		$post_id = wp_insert_post( [
			'post_title'   => 'Hydro Raindrop MFA Page',
			'post_name'    => 'hydro-raindrop',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => null,
			'post_content' => '[hydro_raindrop_mfa]',
		], true );


		if ( $post_id instanceof WP_Error ) {
			return 0;
		}

		update_option( self::OPTION_PAGE_MFA, $post_id );

		return $post_id;

	}

	/**
	 * Create the HydroID page.
	 *
	 * @param int $post_parent_id The parent post ID.
	 *
	 * @return int Returns 0 on failure, otherwise a valid post ID when creation is success.
	 */
	public function create_setup_page( int $post_parent_id ) : int {

		if ( $this->is_setup_page_present() ) {
			return (int) get_option( self::OPTION_PAGE_SETUP );
		}

		$post_id = wp_insert_post( [
			'post_title'   => 'HydroID Setup Page',
			'post_name'    => 'setup',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $post_parent_id,
			'post_content' => '[hydro_raindrop_setup]',
		], true );

		if ( $post_id instanceof WP_Error ) {
			return 0;
		}

		update_option( self::OPTION_PAGE_SETUP, $post_id );

		return $post_id;

	}

	/**
	 * Create the custom HydroID page.
	 *
	 * @param int $post_parent_id The parent post ID.
	 *
	 * @return int Returns 0 on failure, otherwise a valid post ID when creation is success.
	 */
	public function create_settings_page( int $post_parent_id ) : int {

		if ( $this->is_settings_page_present() ) {
			return (int) get_option( self::OPTION_PAGE_SETTINGS );
		}

		$post_id = wp_insert_post( [
			'post_title'   => 'Hydro Raindrop Settings Page',
			'post_name'    => 'settings',
			'post_status'  => 'publish',
			'post_type'    => 'page',
			'post_parent'  => $post_parent_id,
			'post_content' => '[hydro_raindrop_settings]',
		], true );

		if ( $post_id instanceof WP_Error ) {
			return 0;
		}

		update_option( self::OPTION_PAGE_SETTINGS, $post_id );

		return $post_id;

	}

	/**
	 * Delete the MFA page.
	 *
	 * @return Hydro_Raindrop_Helper
	 */
	public function delete_mfa_page() : Hydro_Raindrop_Helper {

		$this->delete_page_with_option( self::OPTION_PAGE_MFA );

		return $this;

	}

	/**
	 * Delete the HydroID page.
	 *
	 * @return Hydro_Raindrop_Helper
	 */
	public function delete_setup_page() : Hydro_Raindrop_Helper {

		$this->delete_page_with_option( self::OPTION_PAGE_SETUP );

		return $this;

	}

	/**
	 * Delete the Settings page.
	 *
	 * @return Hydro_Raindrop_Helper
	 */
	public function delete_settings_page() : Hydro_Raindrop_Helper {

		$this->delete_page_with_option( self::OPTION_PAGE_SETTINGS );

		return $this;

	}

	/**
	 * Un-publish the custom MFA page.
	 *
	 * @return void
	 */
	public function unpublish_mfa_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_MFA, false );

	}

	/**
	 * Un-publish the HydroID page.
	 *
	 * @return void
	 */
	public function unpublish_setup_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_SETUP, false );

	}

	/**
	 * Un-publish the Settings page.
	 */
	public function unpublish_settings_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_SETTINGS, false );

	}

	/**
	 * Publish the custom MFA page.
	 *
	 * @return void
	 */
	public function publish_mfa_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_MFA, true );

	}

	/**
	 * Publish the HydroID page.
	 *
	 * @return void
	 */
	public function publish_setup_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_SETUP, true );

	}

	/**
	 * Publish the Settings page.
	 *
	 * @return void
	 */
	public function publish_settings_page() {

		$this->change_page_status_with_option( self::OPTION_PAGE_SETTINGS, true );

	}

	/**
	 * Whether a page is present.
	 *
	 * @param string $option E.g. The self::OPTION_CUSTOM_HYDRO_ID_PAGE constant.
	 *
	 * @return bool
	 */
	private function is_page_present_with_option( string $option ) : bool {

		$post_id = (int) get_option( $option );

		$post = get_post( $post_id );

		return $post instanceof WP_Post;

	}

	/**
	 * Whether a page is enabled (post must be present and published).
	 *
	 * @param string $option E.g. The self::OPTION_CUSTOM_HYDRO_ID_PAGE constant.
	 *
	 * @return bool
	 */
	private function is_page_enabled_with_option( string $option ) : bool {

		$post_id = (int) get_option( $option );

		return $post_id > 0 && get_post_status( $post_id ) === 'publish';

	}

	/**
	 * Delete a page.
	 *
	 * @param string $option E.g. The self::OPTION_CUSTOM_HYDRO_ID_PAGE constant.
	 * @return void
	 */
	private function delete_page_with_option( string $option ) {

		$post_id = (int) get_option( $option );

		wp_delete_post( $post_id, true );

	}

	/**
	 * Un-publish a page.
	 *
	 * @param string $option E.g. The self::OPTION_CUSTOM_HYDRO_ID_PAGE constant.
	 * @param bool   $published Whether the page should be published or not.
	 */
	private function change_page_status_with_option( string $option, bool $published ) {

		$post_id = (int) get_option( $option );

		if ( $post_id ) {
			wp_update_post( [
				'ID'          => $post_id,
				'post_status' => $published ? 'publish' : 'draft',
			] );
		}

	}

}
