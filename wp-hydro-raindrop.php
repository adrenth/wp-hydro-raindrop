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
 * Version:           1.0.0
 * Author:            Alwin Drenth, Ronald Drenth
 * Author URI:        https://github.com/adrenth
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-hydro-raindrop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Require composer autoloader if installed on it's own
if ( file_exists( $composer = __DIR__ . '/vendor/autoload.php' ) ) {
	require_once $composer;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-hydro-raindrop-activator.php
 */
function activate_hydro_raindrop() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hydro-raindrop-activator.php';
	Hydro_Raindrop_Activator::activate();

}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-hydro-raindrop-deactivator.php
 */
function deactivate_hydro_raindrop() {

	require_once plugin_dir_path( __FILE__ ) . 'includes/class-hydro-raindrop-deactivator.php';
	Hydro_Raindrop_Deactivator::deactivate();

}

register_activation_hook( __FILE__, 'activate_hydro_raindrop' );
register_deactivation_hook( __FILE__, 'deactivate_hydro_raindrop' );

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
 *
 * @since    1.0.0
 */
function run_hydro_raindrop() {

	$plugin = new Hydro_Raindrop();
	$plugin->run();

}

run_hydro_raindrop();
