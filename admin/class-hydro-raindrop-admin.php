<?php

declare( strict_types=1 );

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 */

use Adrenth\Raindrop\Exception\RefreshTokenFailed;

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */
class Hydro_Raindrop_Admin {

	const OPTION_GROUP_SYSTEM_REQUIREMENTS = 'hydro_raindrop_system_requirements';
	const OPTION_GROUP_API_SETTINGS        = 'hydro_raindrop_api_settings';
	const OPTION_GROUP_CUSTOMIZATION       = 'hydro_raindrop_customization';
	const WP_NONCE_AJAX                    = 'hydro_raindrop_nonce_ajax';

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
	 * @param      string $version     The version of this plugin.
	 */
	public function __construct( string $plugin_name, string $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the scripts for the admin area.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {

		wp_enqueue_style(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'css/hydro-raindrop-admin.css',
			[],
			$this->version
		);

		wp_enqueue_script(
			$this->plugin_name,
			plugin_dir_url( __FILE__ ) . 'js/hydro-raindrop-admin.js',
			[ 'jquery' ],
			$this->version
		);

		$nonce = wp_create_nonce( self::WP_NONCE_AJAX );

		wp_localize_script(
			$this->plugin_name,
			'hydro_raindrop_ajax',
			[
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'    => $nonce,
			]
		);

	}

	/**
	 * Register settings.
	 *
	 * @return void
	 */
	public function admin_init() {

		register_setting( self::OPTION_GROUP_API_SETTINGS, Hydro_Raindrop_Helper::OPTION_APPLICATION_ID );
		register_setting( self::OPTION_GROUP_API_SETTINGS, Hydro_Raindrop_Helper::OPTION_CLIENT_ID );
		register_setting( self::OPTION_GROUP_API_SETTINGS, Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET );
		register_setting( self::OPTION_GROUP_API_SETTINGS, Hydro_Raindrop_Helper::OPTION_ENVIRONMENT );

		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_PAGE_MFA );
		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_PAGE_SETUP );
		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_PAGE_SETTINGS );
		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_MFA_METHOD );
		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_MFA_MAXIMUM_ATTEMPTS );
		register_setting( self::OPTION_GROUP_CUSTOMIZATION, Hydro_Raindrop_Helper::OPTION_POST_VERIFICATION_TIMEOUT );

	}

	/**
	 * Add options page.
	 *
	 * @return void
	 */
	public function admin_menu() {

		add_menu_page(
			'Hydro Raindrop: General',
			'Hydro Raindrop',
			'manage_options',
			$this->plugin_name,
			[
				$this,
				'settings_page',
			],
			plugins_url( 'images/icon.svg', __FILE__ )
		);

		add_submenu_page(
			$this->plugin_name,
			'Hydro Raindrop: Settings',
			'Settings',
			'manage_options',
			$this->plugin_name,
			[
				$this,
				'settings_page',
			]
		);

		add_submenu_page(
			$this->plugin_name,
			'Hydro Raindrop: Documentation',
			'Documentation',
			'manage_options',
			$this->plugin_name . '-documentation',
			[
				$this,
				'documentation_page',
			]
		);

	}

	/**
	 * This action hook is typically used to output new fields or data to the bottom of WordPress's user profile pages.
	 *
	 * @param null|WP_User $profileuser The WP_User object of the user being edited.
	 *
	 * @return void
	 */
	public function edit_user_profile( $profileuser ) {

		if ( $profileuser instanceof WP_User && current_user_can( 'edit_user' ) ) {
			require __DIR__ . '/partials/hydro-raindrop-profileuser.php';
		}

	}

	/**
	 * This action hook is generally used to save custom fields that have been added to the WordPress profile page.
	 *
	 * @param null|int $user_id The user ID of the user being edited.
	 */
	public function edit_user_profile_update( $user_id ) {

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			return;
		}

		// @codingStandardsIgnoreStart
		$blocked = (int) ( $_POST[ Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED ] ?? 0 );

		update_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED, $blocked );
		// @codingStandardsIgnoreEnd

	}

	/**
	 * Ajax Handler: reset_hydro_id
	 *
	 * @return void
	 */
	public function reset_hydro_id() {

		check_ajax_referer( self::WP_NONCE_AJAX );

		$user_id = (int) ( $_POST['user_id'] ?? '0' );

		if ( ! current_user_can( 'edit_user', $user_id ) ) {
			wp_die( 'Not allowed', 403 );
		}

		if ( ! current_user_can( 'administrator' ) ) {
			wp_die( 'Not allowed', 403 );
		}

		try {
			$hydro_id = (string) get_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, true );

			if ( '' !== $hydro_id ) {
				$client = Hydro_Raindrop::get_raindrop_client();
				$client->unregisterUser( $hydro_id );
			}

			delete_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_HYDRO_ID );
		} catch ( \Throwable $e ) {

		}

		// @codingStandardsIgnoreStart
		delete_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED );
		delete_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED );
		delete_user_meta( $user_id, Hydro_Raindrop_Helper::USER_META_MFA_FAILED_ATTEMPTS );
		// @codingStandardsIgnoreEnd

		wp_die( 'OK' );

	}

	/**
	 * Hydro Raindrop environment options have been changed.
	 *
	 * @param mixed $option Option which has been updated.
	 *
	 * @return void
	 */
	public function update_option( $option ) {

		switch ( $option ) {

			case Hydro_Raindrop_Helper::OPTION_APPLICATION_ID:
			case Hydro_Raindrop_Helper::OPTION_CLIENT_ID:
			case Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET:
			case Hydro_Raindrop_Helper::OPTION_ENVIRONMENT:
				$token_storage = new Hydro_Raindrop_TransientTokenStorage();
				$token_storage->unsetAccessToken();

				delete_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS );

				delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, '', true );
				delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED, '', true );
				delete_metadata( 'user', 0, Hydro_Raindrop_Helper::USER_META_MFA_CONFIRMED, '', true );

				break;
		}

	}

	/**
	 * Display the settings page.
	 *
	 * @return void
	 */
	public function settings_page() {

		include __DIR__ . '/../admin/partials/hydro-raindrop-settings.php';

	}

	/**
	 * Display the FAQ page.
	 *
	 * @return void
	 */
	public function documentation_page() {

	}

	/**
	 * Add redirect to documentation page (action: init).
	 *
	 * @return void
	 */
	public function redirect_from_admin_menu() {

		global $pagenow;

		// @codingStandardsIgnoreLine
		$page = $_GET['page'] ?? null;

		if ( 'admin.php' === $pagenow && 'wp-hydro-raindrop-documentation' === $page ) {

			// @codingStandardsIgnoreLine
			wp_redirect( 'https://github.com/adrenth/wp-hydro-raindrop' );
			exit();

		}

	}

	/**
	 * @return bool
	 */
	public function options_are_valid() : bool {

		$token_success = (string) get_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS, '' );

		if ( empty( $token_success ) && Hydro_Raindrop::has_valid_raindrop_client_options() ) {
			try {
				$client = Hydro_Raindrop::get_raindrop_client();
				$client->getAccessToken();

				update_option( Hydro_Raindrop_Helper::OPTION_ACCESS_TOKEN_SUCCESS, 1 );

				return true;
			} catch ( RefreshTokenFailed $e ) {
				return false;
			}
		}

		return true;

	}

	/**
	 * Display the activation notice.
	 *
	 * @return void
	 */
	public function activation_notice() {

		if ( get_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE ) ) {
			return;
		}

		$option_page_url = admin_url( 'admin.php?page=' . $this->plugin_name );

		$message = sprintf(
			__( 'Succesfully activated the WP Hydro Raindrop plugin, to configure the plugin go to the Hydro Raindrop MFA <a style="color: #fff; font-weight: bold;" href="%1$s">settings page</a>.', $this->plugin_name ),
			esc_url( $option_page_url )
		);

		printf(
			'<div class="notice is-dismissible" style="background-color: #5591f3; color: #fff; border-left: none;">
				<p>%1$s</p>
			</div>',
			$message
		);

		add_option( Hydro_Raindrop_Helper::OPTION_ACTIVATION_NOTICE, '1' );

	}

	/**
	 * Add action links to plugins table.
	 *
	 * @param array $links Default links.
	 * @return array
	 */
	public function add_action_links( array $links = [] ) : array {

		$option_page_url = admin_url( 'admin.php?page=' . $this->plugin_name );

		$add_links = [
			'<a href="' . $option_page_url . '">' . __( 'Settings', 'wp-hydro-raindrop' ) . '</a>',
		];

		return array_merge( $links, $add_links );
	}

	/**
	 * @return array
	 */
	public function get_post_options() : array {

		$args = array(
			'post_type'      => 'page',
			'posts_per_page' => -1,
			'order'          => 'ASC',
			'orderby'        => 'menu_order',
		);

		$parent = new WP_Query( $args );

		$posts = [];

		while ( $parent->have_posts() ) {
			$parent->the_post();

			$post_id = get_the_ID();

			if ( $post_id ) {
				/**
				 * The variable $posts is used in the template file.
				 *
				 * @noinspection OnlyWritesOnParameterInspection
				 */
				$posts[ (int) $post_id ] = get_the_title() . ' - ' . get_the_permalink();
			}
		}

		return $posts;

	}

}
