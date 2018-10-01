<?php

declare( strict_types=1 );

/**
 * Hydro Raindrop Installer
 *
 * Responsible for activation, deactivation and un-installation tasks.
 *
 * @since      2.0.0
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
final class Hydro_Raindrop_Installer {

	/**
	 * Perform activation tasks.
	 *
	 * @since 2.0.0
	 */
	public static function activate() {

		// @codingStandardsIgnoreLine
		$plugin = $_REQUEST['plugin'] ?? '';

		if ( ! current_user_can( 'activate_plugins' )
				|| ! check_admin_referer( "activate-plugin_{$plugin}" )
		) {
			return;
		}

		self::load_dependencies();

		$helper = new Hydro_Raindrop_Helper();

		if ( $helper->is_mfa_page_present() ) {
			$parent_post_id = (int) get_option( Hydro_Raindrop_Helper::OPTION_PAGE_MFA );
		} else {
			$parent_post_id = $helper->create_mfa_page();
		}

		$helper->publish_mfa_page();

		if ( ! $helper->is_setup_page_present() ) {
			$helper->create_setup_page( $parent_post_id );
		}

		$helper->publish_setup_page();

		if ( ! $helper->is_settings_page_present() ) {
			$helper->create_settings_page( $parent_post_id );
		}

		$helper->publish_settings_page();

		update_option( Hydro_Raindrop_Helper::OPTION_ENABLED, 1 );
		update_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, Hydro_Raindrop_Helper::MFA_METHOD_OPTIONAL );

	}

	/**
	 * Perform deactivation tasks.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function deactivate() {

		// @codingStandardsIgnoreLine
		$plugin = $_REQUEST['plugin'] ?? '';

		if ( ! current_user_can( 'activate_plugins' )
				|| ! check_admin_referer( "deactivate-plugin_{$plugin}" ) ) {
			return;
		}

		self::load_dependencies();

		delete_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE );
		update_option( Hydro_Raindrop_Helper::OPTION_ENABLED, 0 );

		$helper = new Hydro_Raindrop_Helper();
		$helper->unpublish_mfa_page();
		$helper->unpublish_setup_page();
		$helper->unpublish_settings_page();

	}

	/**
	 * Perform uninstall tasks.
	 *
	 * Note: Obviously only a static class method or function can be used in an uninstall hook.
	 *
	 * @since 2.0.0
	 * @return void
	 */
	public static function uninstall() {

		if ( ! defined( 'WP_UNINSTALL_PLUGIN' )
				|| ! current_user_can( 'activate_plugins' )
				|| ! check_admin_referer( 'bulk-plugins' )
		) {
			return;
		}

		self::load_dependencies();

		$storage = new Hydro_Raindrop_TransientTokenStorage();
		$storage->unsetAccessToken();

		self::delete_posts();
		self::delete_options();
		self::delete_user_metadata();

	}


	/**
	 * Delete posts which were required for this plugin to work.
	 *
	 * @return void
	 */
	private static function delete_posts() {

		self::load_dependencies();

		$helper = new Hydro_Raindrop_Helper();
		$helper->delete_mfa_page()
			->delete_setup_page()
			->delete_settings_page();

	}

	/**
	 * Delete the options which were required for this plugin to work.
	 *
	 * @return void
	 */
	private static function delete_options() {

		delete_option( Hydro_Raindrop_Helper::OPTION_APPLICATION_ID );
		delete_option( Hydro_Raindrop_Helper::OPTION_CLIENT_ID );
		delete_option( Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET );
		delete_option( Hydro_Raindrop_Helper::OPTION_ENVIRONMENT );
		delete_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS );
		delete_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE );
		delete_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD );
		delete_option( Hydro_Raindrop_Helper::OPTION_MFA_MAXIMUM_ATTEMPTS );
		delete_option( Hydro_Raindrop_Helper::OPTION_PAGE_MFA );
		delete_option( Hydro_Raindrop_Helper::OPTION_PAGE_SETUP );
		delete_option( Hydro_Raindrop_Helper::OPTION_PAGE_SETTINGS );

	}

	/**
	 * Delete user data associated to this plugin.
	 *
	 * @return void
	 */
	private static function delete_user_metadata() {

		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED, '', true );

	}

	/**
	 * Load the installer dependencies.
	 *
	 * @return void
	 */
	private static function load_dependencies() {

		$includes = [
			__DIR__ . '/class-hydro-raindrop-helper.php',
			__DIR__ . '/class-hydro-raindrop-transienttokenstorage.php',
		];

		foreach ( $includes as $include ) {
			/**
			 * Dynamic include expressions like there are not being analysed.
			 *
			 * @noinspection PhpIncludeInspection
			 */
			require_once $include;
		}

	}

}
