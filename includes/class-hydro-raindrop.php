<?php

declare( strict_types=1 );

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 */

use Adrenth\Raindrop\ApiSettings;
use Adrenth\Raindrop\Client;
use Adrenth\Raindrop\Environment\ProductionEnvironment;
use Adrenth\Raindrop\Environment\SandboxEnvironment;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
class Hydro_Raindrop {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Hydro_Raindrop_Loader $loader Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $plugin_name The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string $version The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Raindrop Client.
	 *
	 * @var Client $raindrop_client The Raindrop Client from the Hydro Raindrop SDK.
	 */
	private static $raindrop_client;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->version     = HYDRO_RAINDROP_VERSION;
		$this->plugin_name = 'wp-hydro-raindrop';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$includes = [
			// The class responsible for orchestrating the actions and filters of the core plugin.
			__DIR__ . '/class-hydro-raindrop-loader.php',
			// The class responsible for storing the access token from the Raindrop API.
			__DIR__ . '/class-hydro-raindrop-token-storage.php',
			// The class responsible for defining internationalization functionality of the plugin.
			__DIR__ . '/class-hydro-raindrop-i18n.php',
			// The class with some convenient helper methods.
			__DIR__ . '/class-hydro-raindrop-helper.php',
			// The class responsible for defining all actions that occur in the admin area.
			__DIR__ . '/../admin/class-hydro-raindrop-admin.php',
			// The class responsible for defining all actions that occur in the public-facing side of the site.
			__DIR__ . '/../public/class-hydro-raindrop-public.php',
			// The class responsible for Hydro Raindrop authentication.
			__DIR__ . '/../public/class-hydro-raindrop-authenticate.php',
		];

		foreach ( $includes as $include ) {
			/**
			 * Dynamic include expressions like there are not being analysed.
			 *
			 * @noinspection PhpIncludeInspection
			 */
			require_once $include;
		}

		// Create an instance of the loader which will be used to register the hooks with WordPress.
		$this->loader = new Hydro_Raindrop_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Hydro_Raindrop_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Hydro_Raindrop_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Hydro_Raindrop_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'before_delete_post', $plugin_admin, 'before_delete_post' );

		$this->loader->add_action( 'update_option', $plugin_admin, 'update_option' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Hydro_Raindrop_Public( $this->get_plugin_name(), $this->get_version() );

		/**
		 * Action: wp_enqueue_scripts
		 *
		 * The wp_enqueue_scripts is the proper hook to use when enqueuing items that are meant to appear on the front
		 * end. Despite the name, it is used for enqueuing both scripts and styles.
		 */
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_login_styles' );

		/**
		 * Action: user_profile_update_errors
		 *
		 * This hook runs AFTER edit_user_profile_update and personal_options_update.
		 * This same callback, after performing your validations, and save the data if it is empty.
		 */
		$this->loader->add_action( 'user_profile_update_errors', $plugin_public, 'custom_user_profile_validate', 10, 3 );

		/**
		 * Action: show_user_profile
		 *
		 * This action hook is typically used to output new fields or data to the bottom of WordPress's user profile
		 * pages.
		 */
		$this->loader->add_action( 'show_user_profile', $plugin_public, 'custom_user_profile_fields' );

		$plugin_authenticate = new Hydro_Raindrop_Authenticate(
			$this->get_plugin_name(),
			$this->get_version()
		);

		/**
		 * Action: wp_authenticate
		 *
		 * This action is located inside of `wp_signon`. In contrast to the `wp_login` action, it is executed before
		 * the WordPress authentication process.
		 */
		$this->loader->add_action( 'wp_authenticate', $plugin_authenticate, 'authenticate', 0, 2 );

		/**
		 * Filter: init
		 *
		 * Most of WP is loaded at this stage, and the user is authenticated.
		 * WP continues to load on the init hook that follows (e.g. widgets), and many plugins instantiate themselves
		 * on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
		 */
		$this->loader->add_filter( 'init', $plugin_authenticate, 'verify', 0 );
		$this->loader->add_filter( 'init', $plugin_public, 'manage_hydro_id', 0 );

		/**
		 * Filter: clear_auth_cookie
		 *
		 * Fires just before the authentication cookies are cleared.
		 */
		$this->loader->add_filter( 'clear_auth_cookie', $plugin_authenticate, 'unset_cookie' );

		/**
		 * Shortcodes
		 *
		 * @see https://codex.wordpress.org/Shortcode_API
		 */
		add_shortcode( 'hydro_raindrop_mfa_form_open', [ $plugin_public, 'shortcode_form_open' ] );
		add_shortcode( 'hydro_raindrop_mfa_form_close', [ $plugin_public, 'shortcode_form_close' ] );
		add_shortcode( 'hydro_raindrop_mfa_digits', [ $plugin_public, 'shortcode_digits' ] );
		add_shortcode( 'hydro_raindrop_mfa_button_authorize', [ $plugin_public, 'shortcode_button_authorize' ] );
		add_shortcode( 'hydro_raindrop_mfa_button_cancel', [ $plugin_public, 'shortcode_button_cancel' ] );
		add_shortcode( 'hydro_raindrop_manage_hydro_id', [ $plugin_public, 'shortcode_manage_hydro_id' ] );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

		$this->loader->run();

	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() : string {

		return $this->plugin_name;

	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hydro_Raindrop_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() : Hydro_Raindrop_Loader {

		return $this->loader;

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() : string {

		return $this->version;

	}

	/**
	 * Retrieve the Raindrop Client.
	 *
	 * @since     1.0.0
	 * @return    Client    The version number of the plugin.
	 */
	public static function get_raindrop_client() : Client {

		if ( ! self::$raindrop_client ) {
			self::$raindrop_client = new Client(
				new ApiSettings(
					(string) get_option( 'hydro_raindrop_client_id' ),
					(string) get_option( 'hydro_raindrop_client_secret' ),
					get_option( 'hydro_raindrop_environment' ) === 'sandbox'
						? new SandboxEnvironment()
						: new ProductionEnvironment()
				),
				new Hydro_Raindrop_TransientTokenStorage(),
				(string) get_option( 'hydro_raindrop_application_id' )
			);
		}

		return self::$raindrop_client;

	}

	/**
	 * Check if the required Raindrop Client options are present.
	 *
	 * @since  1.0.0
	 * @return bool
	 */
	public static function has_valid_raindrop_client_options() : bool {

		$options = [
			'hydro_raindrop_client_id',
			'hydro_raindrop_client_secret',
			'hydro_raindrop_environment',
			'hydro_raindrop_application_id',
		];

		foreach ( $options as $option ) {
			$value = get_option( $option );

			if ( empty( $value ) ) {
				return false;
			}
		}

		return true;

	}
}
