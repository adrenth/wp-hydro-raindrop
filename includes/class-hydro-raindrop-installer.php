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

		if ( ! $helper->is_custom_mfa_page_present() ) {
			$helper->create_custom_mfa_page();
		}

		$helper->publish_custom_mfa_page();

		if ( ! $helper->is_custom_hydro_id_page_present() ) {
			$helper->create_custom_hydro_id_page();
		}

		$helper->publish_custom_hydro_id_page();

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

		$helper = new Hydro_Raindrop_Helper();
		$helper->unpublish_custom_mfa_page();
		$helper->unpublish_custom_hydro_id_page();

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
		$helper->delete_custom_mfa_page();
		$helper->delete_custom_hydro_id_page();

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
		delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE );
		delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE );
		delete_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE );

	}

	/**
	 * Delete user data associated to this plugin.
	 *
	 * @return void
	 */
	private static function delete_user_metadata() {

		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_MFA_ENABLED, '', true );
		delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_RAINDROP_CONFIRMED, '', true );

	}

	/**
	 * Load the installer dependencies.
	 *
	 * @return void
	 */
	private static function load_dependencies() {

		$includes = [
			__DIR__ . '/class-hydro-raindrop-helper.php',
			__DIR__ . '/class-hydro-raindrop-token-storage.php',
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
