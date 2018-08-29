<?php

declare( strict_types=1 );

/**
 * Fired during plugin deactivation
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
class Hydro_Raindrop_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_option( 'hydro_raindrop_activation_notice' );

	}

}
