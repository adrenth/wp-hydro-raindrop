<?php

declare( strict_types=1 );

/**
 * The public-facing functionality of the plugin.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public
 * @author     Alwin Drenth <adrenth@gmail.com>
 */
class Hydro_Raindrop_Public {

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
	 * @param      string $plugin_name The name of the plugin.
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-public.css',
			array(),
			$this->version
		);

	}

	/**
	 * Register the stylesheets for the login part of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_login_styles() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-public.css',
			[],
			$this->version
		);

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery' );
		
		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/hydro-raindrop-public.js',
			[
				'jquery',
			],
			$this->version
		);

	}
	
	/**
	 * Handle Timed out cookie for MFA
	 *
	 * @since    2.0.0
	 */
	public function init_head() {
		if (isset($_COOKIE[COOKIE_MFA_TIMED_OUT])) {
			//do the shortcode
			$sh = do_shortcode('[hydro_raindrop_mfa_timed_out_notice]');
			echo "<script type='text/javascript'>var hydro_mfa_timed_out = '" . $sh . "';var hydro_mfa_timed_out_notice=true;</script>";
			
		}
	}
	

	/**z
	 * Extend the User Profile form.
	 *
	 * @param WP_User $user The current user.
	 */
	public function custom_user_profile_fields( WP_User $user ) {

		include __DIR__ . '/partials/hydro-raindrop-public-user-profile.php';

	}

	/**
	 * Update the extra User Profile fields.
	 *
	 * @param int|null $user_id The user ID of the user being edited.
	 */
	public function update_extra_profile_fields( $user_id = null ) {

		// @codingStandardsIgnoreLine
		$enabled = (bool) ( $_POST[ Hydro_Raindrop_Helper::USER_META_MFA_ENABLED ] ?? false );
		$helper  = new Hydro_Raindrop_Helper();

		$hydro_raindrop_mfa_method = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, true );
		$is_mfa_method_enforced    = Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED === $hydro_raindrop_mfa_method;

		// Register the redirect URL to goto when flow is finished.
		// @codingStandardsIgnoreLine
		update_user_meta(
			$user_id,
			Hydro_Raindrop_Helper::USER_META_REDIRECT_URL,
			$helper->is_settings_page_enabled()
				? $helper->get_settings_page_url()
				: get_edit_profile_url( $user_id )
		);

		/*
		 * Disable Hydro Raindrop MFA.
		 */
		if ( ! $enabled
				&& ! $is_mfa_method_enforced
				&& current_user_can( 'edit_user', $user_id )
		) {
			$redirect_url = $helper->get_current_url() . '?hydro-raindrop-verify=1&hydro-raindrop-action=disable';

			if ( $helper->is_mfa_page_enabled() ) {
				$redirect_url = $helper->get_mfa_page_url() . '?hydro-raindrop-verify=1&hydro-raindrop-action=disable';
			}

			$user = wp_get_current_user();

			$flash = new Hydro_Raindrop_Flash( $user->user_login );
			$flash->info( 'Enter the security code into the Hydro app to disable Hydro Raindrop MFA.' );

			$cookie = new Hydro_Raindrop_Cookie( $this->plugin_name, $this->version );
			$cookie->set( $user->ID );

			// @codingStandardsIgnoreLine
			wp_redirect( $redirect_url );
			exit();

		}

		/*
		 * Enable Hydro Raindrop MFA.
		 */
		if ( $enabled && current_user_can( 'edit_user', $user_id ) ) {

			$user   = wp_get_current_user();
			$helper = new Hydro_Raindrop_Helper();

			$cookie = new Hydro_Raindrop_Cookie( $this->plugin_name, $this->version );
			$cookie->set( $user->ID );

			// @codingStandardsIgnoreLine
			wp_redirect( $helper->get_current_url() . '?hydro-raindrop-action=enable' );
			exit();

		}

	}

}
