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
class Hydro_Raindrop_Helper {

	/**
	 * Hydro Raindrop database options.
	 */
	const OPTION_ENABLED              = 'hydro_raindrop_enabled';
	const OPTION_APPLICATION_ID       = 'hydro_raindrop_application_id';
	const OPTION_CLIENT_ID            = 'hydro_raindrop_client_id';
	const OPTION_CLIENT_SECRET        = 'hydro_raindrop_client_secret';
	const OPTION_ENVIRONMENT          = 'hydro_raindrop_environment';
	const OPTION_CUSTOM_MFA_PAGE      = 'hydro_raindrop_custom_mfa_page';
	const OPTION_CUSTOM_HYDRO_ID_PAGE = 'hydro_raindrop_custom_hydro_id_page';
	const OPTION_ACTIVATION_NOTICE    = 'hydro_raindrop_activation_notice';
	const OPTION_ACCESS_TOKEN_SUCCESS = 'hydro_raindrop_access_token_success';

	/**
	 * Hydro Raindrop user meta.
	 */
	const USER_META_HYDRO_ID                 = 'hydro_id';
	const USER_META_HYDRO_MFA_ENABLED        = 'hydro_mfa_enabled';
	const USER_META_HYDRO_RAINDROP_CONFIRMED = 'hydro_raindrop_confirmed';

	/**
	 * Construct the Helper.
	 */
	public function __construct() {

	}

	/**
	 * Whether Hydro Raindrop is enabled.
	 *
	 * Only the site admin can enabled Hydro Raindrop MFA.
	 *
	 * @return bool
	 */
	public function is_hydro_raindrop_enabled() : bool {

		return (int) get_option( self::OPTION_ENABLED ) === 1;

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
	 * @return string
	 */
	public function get_current_url() : string {

		return home_url( add_query_arg( null, null ) );

	}

	/**
	 * Get the custom MFA page URL.
	 *
	 * @return string
	 */
	public function get_custom_mfa_page_url() : string {

		$post_id = (int) get_option( self::OPTION_CUSTOM_MFA_PAGE );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Get the custom Hydro ID page URL.
	 *
	 * @return string
	 */
	public function get_custom_hydro_id_page_url() : string {

		$post_id = (int) get_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Checks if MFA page is present and enabled.
	 *
	 * @return bool
	 */
	public function is_custom_mfa_page_enabled() : bool {

		return $this->is_page_enabled_with_option( self::OPTION_CUSTOM_MFA_PAGE );

	}

	/**
	 * Checks if Hydro ID page is present and enabled.
	 *
	 * @return bool
	 */
	public function is_custom_hydro_id_page_enabled() : bool {

		return $this->is_page_enabled_with_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

	}

	/**
	 * Whether the Custom MFA Page is present.
	 *
	 * @return bool
	 */
	public function is_custom_mfa_page_present() : bool {

		return $this->is_page_present_with_option( self::OPTION_CUSTOM_MFA_PAGE );

	}

	/**
	 * Whether the Custom Hydro ID Page is present.
	 *
	 * @return bool
	 */
	public function is_custom_hydro_id_page_present() : bool {

		return $this->is_page_present_with_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

	}

	/**
	 * Create the custom MFA page.
	 *
	 * @return void
	 */
	public function create_custom_mfa_page() {

		$post_id = wp_insert_post( [
			'post_title'  => 'Hydro MFA Login Page',
			'post_name'   => 'hydro-raindrop-mfa',
			'post_status' => 'publish',
			'post_type'   => 'page',
		], true );


		if ( $post_id instanceof WP_Error ) {
			return;
		}

		update_option( self::OPTION_CUSTOM_MFA_PAGE, $post_id );

	}

	/**
	 * Create the custom Hydro ID page.
	 *
	 * @return void
	 */
	public function create_custom_hydro_id_page() {

		$post_id = wp_insert_post( [
			'post_title'  => 'Hydro MFA Settings Page',
			'post_name'   => 'hydro-raindrop-settings',
			'post_status' => 'publish',
			'post_type'   => 'page',
		], true );

		if ( $post_id instanceof WP_Error ) {
			return;
		}

		update_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE, $post_id );

	}

	/**
	 * Delete the custom MFA page.
	 *
	 * @return void
	 */
	public function delete_custom_mfa_page() {

		wp_delete_post( get_option( self::OPTION_CUSTOM_MFA_PAGE ) );

	}

	/**
	 * Delete the custom Hydro ID page.
	 *
	 * @return void
	 */
	public function delete_custom_hydro_id_page() {

		wp_delete_post( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

	}

	/**
	 * Un-publish the custom MFA page.
	 *
	 * @return void
	 */
	public function unpublish_custom_mfa_page() {

		$this->change_page_status_with_option( self::OPTION_CUSTOM_MFA_PAGE, false );

	}

	/**
	 * Un-publish the Hydro ID page.
	 *
	 * @return void
	 */
	public function unpublish_custom_hydro_id_page() {

		$this->change_page_status_with_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE, false );

	}

	/**
	 * Publish the custom MFA page.
	 *
	 * @return void
	 */
	public function publish_custom_mfa_page() {

		$this->change_page_status_with_option( self::OPTION_CUSTOM_MFA_PAGE, true );

	}

	/**
	 * Publish the Hydro ID page.
	 *
	 * @return void
	 */
	public function publish_custom_hydro_id_page() {

		$this->change_page_status_with_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE, true );

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
