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

delete_option( 'hydro_raindrop_application_id' );
delete_option( 'hydro_raindrop_client_id' );
delete_option( 'hydro_raindrop_client_secret' );
delete_option( 'hydro_raindrop_environment' );
delete_option( 'hydro_raindrop_access_token_success' );
delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE );
delete_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE );

/**
 * Delete user metadata by for this plugin.
 */
delete_metadata( 'user', 0, 'hydro_id', '', true );
delete_metadata( 'user', 0, 'hydro_mfa_enabled', '', true );
delete_metadata( 'user', 0, 'hydro_raindrop_confirmed', '', true );

/**
 * Delete access token from the transient token storage.
 */
require_once __DIR__ . '/includes/class-hydro-raindrop-token-storage.php';

$storage = new Hydro_Raindrop_TransientTokenStorage();
$storage->unsetAccessToken();
