<?php

declare( strict_types=1 );

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      1.0.0
 *
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/admin/partials
 */

?>

<?php
if ( ! current_user_can( 'manage_options' ) ) {
	wp_die( esc_html__( 'You do not have sufficient permissions to access this page.' ) );
}

// @codingStandardsIgnoreLine
$active_tab             = $_GET['tab'] ?? self::OPTION_GROUP_INITIALIZATION;
$helper                 = new Hydro_Raindrop_Helper();
$hydro_raindrop_enabled = $helper->is_hydro_raindrop_enabled();
$options_are_valid      = Hydro_Raindrop::has_valid_raindrop_client_options() && $this->options_are_valid()
?>
<div class="wrap">
	<h1>Hydro Raindrop Settings</h1>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<a href="?page=<?php echo $this->plugin_name; ?>&tab=hydro_raindrop"
				class="nav-tab<?php echo Hydro_Raindrop_Admin::OPTION_GROUP_INITIALIZATION === $active_tab ? ' nav-tab-active' : ''; ?>">
			Initialization
		</a>

		<?php if ( $helper->is_hydro_raindrop_enabled() ) : ?>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>&tab=<?php echo esc_attr( Hydro_Raindrop_Admin::OPTION_GROUP_API ); ?>"
					class="nav-tab<?php echo Hydro_Raindrop_Admin::OPTION_GROUP_API === $active_tab ? ' nav-tab-active' : ''; ?>">
				<?php if ( $options_are_valid ) : ?>
				<i class="dashicons dashicons-yes" style="color: #46b450; margin-top: 2px"></i>
				<?php else : ?>
				<i class="dashicons dashicons-no" style="color: red; margin-top: 2px"></i>
				<?php endif; ?>
				API Settings
			</a>
			<a href="?page=<?php echo esc_attr( $this->plugin_name ); ?>&tab=<?php echo esc_attr( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION ); ?>"
					class="nav-tab<?php echo Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION === $active_tab ? ' nav-tab-active' : ''; ?>">
				Customization
			</a>
		<?php else : ?>
			<a href="#" class="nav-tab button-disabled">API Settings</a>
			<a href="#" class="nav-tab button-disabled">Customization</a>
		<?php endif; ?>
	</h2>

	<form method="post" action="options.php">
		<?php if ( Hydro_Raindrop_Admin::OPTION_GROUP_INITIALIZATION === $active_tab ) : ?>
		<div class="hydro-notice is-dismissible">
			<p>
				<?php if ( $hydro_raindrop_enabled ) : ?>
					<i class="dashicons dashicons-yes" style="color: #46b450"></i> Hydro Raindrop MFA is currently <strong style="color: #46b450">enabled</strong> for all users. To globally <strong>disable</strong> Hydro Raindrop MFA enter your username and password.
				<?php else : ?>
					<i class="dashicons dashicons-no" style="color: red"></i>Hydro Raindrop MFA is currently <strong style="color: red">disabled</strong> for all users. To globally <strong>enable</strong> Hydro Raindrop MFA enter your username and password.
				<?php endif; ?>
			</p>
		</div>
			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_INITIALIZATION ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_INITIALIZATION ); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="user_login">
							Username
						</label>
					</th>
					<td>
						<input type="text"
								class="input regular-text"
								id="user_login"
								name="user_login"
								autocomplete="off"
								size="20"/>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="hydro_raindrop_application_id">
							Password
						</label>
					</th>
					<td>
						<input type="password"
								class="input regular-text"
								id="password"
								name="password"
								size="20"/>
					</td>
				</tr>
			</table>

			<?php echo wp_nonce_field( 'hydro_raindrop_initialization', '_hydro_raindrop_nonce' ); ?>

			<input name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_ENABLED ); ?>"
					id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_ENABLED ); ?>"
					type="hidden"
					value="<?php echo (int) ( ! $hydro_raindrop_enabled ); ?>">

			<?php if ( $hydro_raindrop_enabled ) : ?>
				<?php submit_button( 'Disable Hydro Raindrop MFA' ); ?>
			<?php else : ?>
				<?php submit_button( 'Enable Hydro Raindrop MFA' ); ?>
			<?php endif; ?>

		<?php elseif ( Hydro_Raindrop_Admin::OPTION_GROUP_API === $active_tab && $hydro_raindrop_enabled ) : ?>

			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_API ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_API ); ?>

			<table class="form-table<?php echo ! $options_are_valid ? ' options-are-invalid' : ''; ?>">
				<tr valign="top">
					<th scope="row">
						<label for="hydro_raindrop_application_id">
							Application ID
						</label>
					</th>
					<td>
						<input type="text"
								class="regular-text code"
								id="hydro_raindrop_application_id"
								name="hydro_raindrop_application_id"
								value="<?php echo esc_attr( get_option( Hydro_Raindrop_Helper::OPTION_APPLICATION_ID ) ); ?>"
								autocomplete="off"/>
						<p class="description">Register an account at <a href="https://www.hydrogenplatform.com" target="_blank">https://www.hydrogenplatform.com</a> to obtain an Application ID.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="hydro_raindrop_client_id">
							Client ID
						</label>
					</th>
					<td>
						<input type="text"
								class="regular-text code"
								id="hydro_raindrop_client_id"
								name="hydro_raindrop_client_id"
								value="<?php echo esc_attr( get_option( Hydro_Raindrop_Helper::OPTION_CLIENT_ID ) ); ?>"
								autocomplete="off"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="hydro_raindrop_client_secret">
							Client Secret
						</label>
					</th>
					<td>
						<input type="text"
								class="regular-text code"
								id="hydro_raindrop_client_secret"
								name="hydro_raindrop_client_secret"
								value="<?php echo esc_attr( get_option( Hydro_Raindrop_Helper::OPTION_CLIENT_SECRET ) ); ?>"
								autocomplete="off"/>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="hydro_raindrop_environment">
							Environment
						</label>
					</th>
					<td>
						<select id="hydro_raindrop_environment"
								name="hydro_raindrop_environment"
								class="selection">
							<option value="production"<?php if ( get_option( Hydro_Raindrop_Helper::OPTION_ENVIRONMENT ) === 'production' ) : ?> selected<?php endif; ?>>
								Production
							</option>
							<option value="sandbox"<?php if ( get_option( Hydro_Raindrop_Helper::OPTION_ENVIRONMENT ) === 'sandbox' ) : ?> selected<?php endif; ?>>
								Sandbox
							</option>
						</select>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>

			<p class="description" style="color: red">
				<strong>CAUTION</strong>: Changing these settings will disable Hydro Raindrop MFA for all users. Every user needs to re-enable Hydro Raindrop MFA from their profile page.
			</p>

		<?php elseif ( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION === $active_tab && $hydro_raindrop_enabled ) : ?>
			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION ); ?>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ); ?>">
							Hydro MFA Login Page
						</label>
					</th>
					<td>
						<select id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ); ?>"
								name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ); ?>"
								class="selection">
							<option value="0">Use default Hydro MFA Login Page</option>
							<option value="0">---</option>
							<?php
							/**
							 * Type hinting.
							 *
							 * @var array $posts
							 */
							foreach ( $posts as $post_id => $post ) :
								?>
								<?php $selected = (int) get_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ) === $post_id ? ' selected' : ''; ?>
								<option value="<?php echo esc_attr( $post_id ); ?>"<?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $post ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">Please make sure you have implemented the shortcodes on the Hydro MFA Login Page.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ); ?>">
							Hydro MFA Settings Page
						</label>
					</th>
					<td>
						<select id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ); ?>"
								name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ); ?>"
								class="selection">
							<option value="0">Use default Hydro MFA Settings Page</option>
							<option value="0">---</option>
							<?php
							/**
							 * Type hinting.
							 *
							 * @var array $posts
							 */
							foreach ( $posts as $post_id => $post ) :
								?>
								<?php $selected = (int) get_option( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ) === $post_id ? ' selected' : ''; ?>
								<option value="<?php echo esc_attr( $post_id ); ?>"<?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $post ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">Please make sure you have implemented the shortcodes on the Hydro MFA Settings Page.</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		<?php endif; ?>

	</form>
</div>
