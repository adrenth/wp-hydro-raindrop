<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 */

/**
 * If uninstall not called from WordPress, then exit.
 */
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Setup composer autoloading.
 */
require_once __DIR__ . '/vendor/autoload.php';


/**
 * Delete options used by this plugin.
 */
require_once __DIR__ . '/includes/class-hydro-raindrop-helper.php';

delete_option( Hydro_Raindrop_Helper::OPTION_APPLICATION_ID );
delete_option( Hydro_Raindrop_Helper::OPTION_CLIENT_ID );
delete_option( Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET );
delete_option( Hydro_Raindrop_Helper::OPTION_ENVIRONMENT );
delete_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS );
delete_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE );
delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE );
delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE );

/**
 * Delete user metadata for this plugin.
 */
delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, '', true );
delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_MFA_ENABLED, '', true );
delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_RAINDROP_CONFIRMED, '', true );

/**
 * Delete access token from the transient token storage.
 */
require_once __DIR__ . '/includes/class-hydro-raindrop-token-storage.php';

$storage = new Hydro_Raindrop_TransientTokenStorage();
$storage->unsetAccessToken();
