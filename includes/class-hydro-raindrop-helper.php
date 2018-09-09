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
	 *
	 * @since 1.3.0
	 */
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
	 *
	 * @since 1.4.0
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
	 * Get the custom HydroID page URL.
	 *
	 * @return string
	 */
	public function get_custom_hydro_id_page_url() : string {

		$post_id = (int) get_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

		return $post_id > 0 ? get_permalink( $post_id ) : '';

	}

	/**
	 * Checks if MFA page is configured as custom.
	 *
	 * @return bool
	 */
	public function is_custom_mfa_page_enabled() : bool {

		$post_id = (int) get_option( self::OPTION_CUSTOM_MFA_PAGE );

		return $post_id > 0 && get_post_status( $post_id ) === 'publish';

	}

	/**
	 * Checks if MFA page is configured as custom.
	 *
	 * @return bool
	 */
	public function is_custom_hydro_id_page_enabled() : bool {

		$post_id = (int) get_option( self::OPTION_CUSTOM_HYDRO_ID_PAGE );

		return $post_id > 0 && get_post_status( $post_id ) === 'publish';

	}
}
