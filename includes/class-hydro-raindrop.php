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
			__DIR__ . '/class-hydro-raindrop-transienttokenstorage.php',
			// The class responsible for defining internationalization functionality of the plugin.
			__DIR__ . '/class-hydro-raindrop-i18n.php',
			// The class with some convenient helper methods.
			__DIR__ . '/class-hydro-raindrop-helper.php',
			// The class for handling the MFA cookie.
			__DIR__ . '/class-hydro-raindrop-cookie.php',
			// The class responsible for checking requirements.
			__DIR__ . '/class-hydro-raindrop-requirementchecker.php',
			// The class responsible for defining all actions that occur in the admin area.
			__DIR__ . '/../admin/class-hydro-raindrop-admin.php',
			// The class responsible for defining all actions that occur in the public-facing side of the site.
			__DIR__ . '/../public/class-hydro-raindrop-public.php',
			// The class responsible for Hydro Raindrop authentication.
			__DIR__ . '/../public/class-hydro-raindrop-authenticate.php',
			// The class responsible for rendering the Hydro Raindrop shortcodes.
			__DIR__ . '/../public/class-hydro-raindrop-shortcode.php',
			// The class responsible for rendering the Flash messages.
			__DIR__ . '/../public/class-hydro-raindrop-flash.php',
			// The class responsible for handling the meta boxes.
			__DIR__ . '/../public/class-hydro-raindrop-metabox.php',
			// Cookie Expired Exception.
			__DIR__ . '/exceptions/class-hydro-raindrop-cookieexpired.php',
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

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_init', $plugin_admin, 'admin_init' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'admin_menu' );
		$this->loader->add_action( 'update_option', $plugin_admin, 'update_option' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'activation_notice' );
		$this->loader->add_action( 'edit_user_profile', $plugin_admin, 'edit_user_profile' );
		$this->loader->add_action( 'edit_user_profile_update', $plugin_admin, 'edit_user_profile_update' );
		$this->loader->add_action( 'wp_ajax_reset-hydro-id', $plugin_admin, 'reset_hydro_id' );

		$this->loader->add_filter(
			"plugin_action_links_{$this->plugin_name}/{$this->plugin_name}.php",
			$plugin_admin,
			'add_action_links'
		);

		$meta_box = new Hydro_Raindrop_MetaBox();

		$this->loader->add_action( 'load-post.php', $meta_box, 'init' );
		$this->loader->add_action( 'load-post-new.php', $meta_box, 'init' );
		$this->loader->add_action( 'add_meta_boxes', $meta_box, 'init' );
		$this->loader->add_action( 'save_post', $meta_box, 'save' );

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
		 * Action: wp_head
		 *
		 * The wp_head action hook is triggered within the <head></head> section of the user's template by
		 * the wp_head() function. Although this is theme-dependent, it is one of the most essential theme hooks,
		 * so it is widely supported.
		 */
		$this->loader->add_action( 'wp_head', $plugin_public, 'init_head' );

		/**
		 * Action: show_user_profile
		 *
		 * This action hook is typically used to output new fields or data to the bottom of WordPress's user profile
		 * pages.
		 */
		$this->loader->add_action( 'show_user_profile', $plugin_public, 'custom_user_profile_fields' );

		/**
		 * Action: personal_options_update
		 *
		 * Generally, action hook is used to save custom fields that have been added to the WordPress profile page.
		 */
		$this->loader->add_action( 'personal_options_update', $plugin_public, 'update_extra_profile_fields' );

		$plugin_admin = new Hydro_Raindrop_Admin(
			$this->get_plugin_name(),
			$this->get_version()
		);

		$plugin_authenticate = new Hydro_Raindrop_Authenticate(
			$this->get_plugin_name(),
			$this->get_version()
		);

		/**
		 * Filter: authenticate
		 *
		 * The authenticate filter hook is used to perform additional validation/authentication any time a user logs in
		 * to WordPress.
		 */
		$this->loader->add_filter( 'authenticate', $plugin_authenticate, 'authenticate', 21 );

		/**
		 * Filter: init
		 *
		 * Most of WP is loaded at this stage, and the user is authenticated.
		 * WP continues to load on the init hook that follows (e.g. widgets), and many plugins instantiate themselves
		 * on it for all sorts of reasons (e.g. they need a user, a taxonomy, etc.).
		 */
		$this->loader->add_filter( 'init', $plugin_authenticate, 'verify', 0 );
		$this->loader->add_action( 'init', $plugin_admin, 'redirect_from_admin_menu' );

		if ( version_compare( (string) get_bloginfo( 'version' ), '4.7', '<' ) ) {

			/**
			 * Filter: page_attributes_dropdown_pages_args
			 *
			 * Filters the arguments used to generate a Pages drop-down element.
			 */
			$this->loader->add_filter( 'page_attributes_dropdown_pages_args', $plugin_public, 'register_templates' );

		} else {

			$this->loader->add_filter( 'theme_page_templates', $plugin_public, 'add_new_template' );

		}

		/**
		 * Filter: wp_insert_post_data
		 *
		 * A filter hook called by the wp_insert_post function prior to inserting into or updating the database.
		 */
		$this->loader->add_filter( 'wp_insert_post_data', $plugin_public, 'register_templates' );

		/**
		 * Filter: template_include
		 *
		 * This filter hook is executed immediately before WordPress includes the predetermined template file.
		 * This can be used to override WordPress's default template behavior.
		 */
		$this->loader->add_filter( 'template_include', $plugin_public, 'view_template' );

		/**
		 * Shortcodes
		 *
		 * @see https://codex.wordpress.org/Shortcode_API
		 */
		$plugin_shortcode = new Hydro_Raindrop_Shortcode(
			$this->get_plugin_name(),
			$this->get_version()
		);

		add_shortcode( 'hydro_raindrop_mfa', [ $plugin_shortcode, 'mfa' ] );
		add_shortcode( 'hydro_raindrop_mfa_flash', [ $plugin_shortcode, 'mfa_flash' ] );
		add_shortcode( 'hydro_raindrop_mfa_form_open', [ $plugin_shortcode, 'mfa_form_open' ] );
		add_shortcode( 'hydro_raindrop_mfa_digits', [ $plugin_shortcode, 'mfa_digits' ] );
		add_shortcode( 'hydro_raindrop_mfa_button_authorize', [ $plugin_shortcode, 'mfa_button_authorize' ] );
		add_shortcode( 'hydro_raindrop_mfa_button_cancel', [ $plugin_shortcode, 'mfa_button_cancel' ] );
		add_shortcode( 'hydro_raindrop_mfa_form_close', [ $plugin_shortcode, 'mfa_form_close' ] );

		add_shortcode( 'hydro_raindrop_setup', [ $plugin_shortcode, 'setup' ] );
		add_shortcode( 'hydro_raindrop_setup_flash', [ $plugin_shortcode, 'setup_flash' ] );
		add_shortcode( 'hydro_raindrop_setup_form_open', [ $plugin_shortcode, 'setup_form_open' ] );
		add_shortcode( 'hydro_raindrop_setup_hydro_id', [ $plugin_shortcode, 'setup_hydro_id' ] );
		add_shortcode( 'hydro_raindrop_setup_button_submit', [ $plugin_shortcode, 'setup_button_submit' ] );
		add_shortcode( 'hydro_raindrop_setup_button_skip', [ $plugin_shortcode, 'setup_button_skip' ] );
		add_shortcode( 'hydro_raindrop_setup_form_close', [ $plugin_shortcode, 'setup_form_close' ] );

		add_shortcode( 'hydro_raindrop_settings', [ $plugin_shortcode, 'settings' ] );
		add_shortcode( 'hydro_raindrop_settings_flash', [ $plugin_shortcode, 'settings_flash' ] );
		add_shortcode( 'hydro_raindrop_settings_form_open', [ $plugin_shortcode, 'settings_form_open' ] );
		add_shortcode( 'hydro_raindrop_settings_checkbox_mfa_enabled', [ $plugin_shortcode, 'settings_checkbox_mfa_enabled' ] );
		add_shortcode( 'hydro_raindrop_settings_button_submit', [ $plugin_shortcode, 'settings_button_submit' ] );
		add_shortcode( 'hydro_raindrop_settings_form_close', [ $plugin_shortcode, 'settings_form_close' ] );

		add_shortcode( 'hydro_raindrop_mfa_timed_out_notice', [ $plugin_shortcode, 'mfa_timed_out_notice' ] );

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
					(string) get_option( Hydro_Raindrop_Helper::OPTION_CLIENT_ID ),
					(string) get_option( Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET ),
					get_option( Hydro_Raindrop_Helper::OPTION_ENVIRONMENT ) === 'sandbox'
						? new SandboxEnvironment()
						: new ProductionEnvironment()
				),
				new Hydro_Raindrop_TransientTokenStorage(),
				(string) get_option( Hydro_Raindrop_Helper::OPTION_APPLICATION_ID )
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
			Hydro_Raindrop_Helper::OPTION_CLIENT_ID,
			Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET,
			Hydro_Raindrop_Helper::OPTION_ENVIRONMENT,
			Hydro_Raindrop_Helper::OPTION_APPLICATION_ID,
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
