<?php

/**
 * WP Hydro Raindrop
 *
 * @link              https://github.com/adrenth
 * @since             1.0.0
 * @package           Hydro_Raindrop
 *
 * @wordpress-plugin
 * Plugin Name:       WP Hydro Raindrop
 * Plugin URI:        https://github.com/adrenth/wp-hydro-raindrop
 * Description:       A WordPress plugin to integrate Hydro Raindrop MFA
 * Version:           2.1.1
 * Author:            Hydrogen API
 * Author URI:        https://projecthydro.org
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-hydro-raindrop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST === true ) {
	return;
}

// Require composer autoloader if installed on it's own.
$composer = __DIR__ . '/vendor/autoload.php';

if ( file_exists( $composer ) ) {
	/**
	 * Expression is not analysed.
	 *
	 * @noinspection PhpIncludeInspection
	 */
	require_once $composer;
}

/**
 * Current WP Hydro Raindrop plugin version.
 *
 * @var string
 */
define( 'HYDRO_RAINDROP_VERSION', '2.1.1' );

/**
 * The installer class which handles the activation,
 * deactivation and un-installation of this plugin.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hydro-raindrop-installer.php';

register_activation_hook( __FILE__, [ 'Hydro_Raindrop_Installer', 'activate' ] );
register_deactivation_hook( __FILE__, [ 'Hydro_Raindrop_Installer', 'deactivate' ] );
register_uninstall_hook( __FILE__, [ 'Hydro_Raindrop_Installer', 'uninstall' ] );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-hydro-raindrop.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 */
function run_hydro_raindrop() {

	$plugin = new Hydro_Raindrop();
	$plugin->run();

}

run_hydro_raindrop();
