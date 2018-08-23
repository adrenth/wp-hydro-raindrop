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

	const OPTION_CUSTOM_MFA_PAGE      = 'hydro_raindrop_custom_mfa_page';
	const OPTION_CUSTOM_HYDRO_ID_PAGE = 'hydro_raindrop_custom_hydro_id_page';

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
