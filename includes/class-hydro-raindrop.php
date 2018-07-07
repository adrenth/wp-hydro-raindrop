<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://github.com/adrenth
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/includes
 */

use Adrenth\Raindrop\ApiSettings;
use Adrenth\Raindrop\Client;
use Adrenth\Raindrop\Environment\ProductionEnvironment;
use Adrenth\Raindrop\Environment\SandboxEnvironment;
use Adrenth\Raindrop\TokenStorage\FileTokenStorage;

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
 * @author     Alwin Drenth <adrenth@gmail.com>
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
     * @var Client
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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'wp-hydro-raindrop';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Hydro_Raindrop_Loader. Orchestrates the hooks of the plugin.
	 * - Hydro_Raindrop_i18n. Defines internationalization functionality.
	 * - Hydro_Raindrop_Admin. Defines all hooks for the admin area.
	 * - Hydro_Raindrop_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hydro-raindrop-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-hydro-raindrop-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-hydro-raindrop-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hydro-raindrop-public.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-hydro-raindrop-authenticate.php';

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

		// TODO: Add action documentation
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'login_enqueue_scripts', $plugin_public, 'enqueue_login_styles' );
		$this->loader->add_action( 'show_user_profile', $plugin_public, 'custom_user_profile_fields' );
		$this->loader->add_action( 'personal_options_update', $plugin_public, 'custom_user_profile_update' );
		$this->loader->add_action( 'user_profile_update_errors', $plugin_public, 'custom_user_profile_validate' );

		$plugin_authenticate = new Hydro_Raindrop_Authenticate(
			$this->get_plugin_name(),
			$this->get_version()
		);

		/**
		 * Filter: init
		 *
		 * Most of WP is loaded at this stage, and the user is authenticated.
		 * WP continues to load on the init hook that follows (e.g. widgets), and many plugins instantiate themselves
		 * on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
		 */
		$this->loader->add_filter( 'init', $plugin_authenticate, 'verify' );

		/**
		 * Filter: clear_auth_cookie
		 *
		 * Fires just before the authentication cookies are cleared.
		 */
		$this->loader->add_filter( 'clear_auth_cookie', $plugin_authenticate, 'unset_cookie' );

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
	public function get_plugin_name() {

		return $this->plugin_name;

	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Hydro_Raindrop_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {

		return $this->loader;

	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {

		return $this->version;

	}

    /**
     * Retrieve the Raindrop Client.
     *
     * @since     1.0.0
     * @return    Client    The version number of the plugin.
     */
	public static function get_raindrop_client() {

        if (self::$raindrop_client === null) {
            self::$raindrop_client = new Client(
                new ApiSettings(
                    get_option( 'client_id' ),
                    get_option( 'client_secret' ),
                    get_option( 'environment' ) === 'sandbox'
                        ? new SandboxEnvironment()
                        : new ProductionEnvironment()
                ),
                new FileTokenStorage(__DIR__ . '/token.txt'),
                get_option( 'application_id' )
            );
        }

        return self::$raindrop_client;

    }

}
