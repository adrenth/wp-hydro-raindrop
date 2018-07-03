<?php

use Adrenth\Raindrop\Exception\RegisterUserFailed;
use Adrenth\Raindrop\Exception\VerifySignatureFailed;

class Hydro_Raindrop_Api {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $plugin_name The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 *
	 * @param      string $plugin_name The name of this plugin.
	 * @param      string $version The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	public function register_routes()
	{
		register_rest_route('hydro-raindrop/v1', 'register-user', array (
			'methods' => WP_REST_Server::CREATABLE,
			'callback' => [
				$this,
				'register_user'
			]
		));

        register_rest_route('hydro-raindrop/v1', 'verify-signature', array (
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [
                $this,
                'verify_signature'
            ]
        ));
	}

	public function register_user( WP_REST_Request $request )
	{
	    $client = Hydro_Raindrop::get_raindrop_client();

	    try {
            $client->registerUser($request['hydro_id']);
            return true;
        } catch (RegisterUserFailed $e) {
	        return false;
        }
	}

	public function verify_signature( WP_REST_Request $request )
    {
        $client = Hydro_Raindrop::get_raindrop_client();

        try {
            $client->verifySignature($request['hydro_id'], (int) $request['message']);
            return true;
        } catch (VerifySignatureFailed $e) {
            return false;
        }
    }
}
