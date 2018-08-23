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
	wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
}
?>

<div class="wrap">

	<h1>Hydro Raindrop MFA Settings</h1>

	<div class="notice">
		<p>Register an account at <a href="https://www.hydrogenplatform.com" target="_blank">https://www.hydrogenplatform.com</a> to obtain an Application ID.</p>
	</div>

	<form method="post" action="options.php">

		<?php if ( ! $this->options_are_valid() ) { ?>
			<div id="setting-error-settings_updated" class="error settings-error notice is-dismissible">
				<p><strong>Settings are invalid. Please review settings and try saving again.</strong></p>
			</div>
		<?php } ?>

		<?php settings_fields( 'hydro_api' ); ?>
		<?php do_settings_sections( 'hydro_api' ); ?>

		<table class="form-table">
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
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_application_id' ) ); ?>"
							autocomplete="off"/>
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
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_client_id' ) ); ?>"
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
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_client_secret' ) ); ?>"
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
						<option value="production"<?php if ( get_option( 'hydro_raindrop_environment' ) === 'production' ) : ?> selected<?php endif; ?>>
							Production
						</option>
						<option value="sandbox"<?php if ( get_option( 'hydro_raindrop_environment' ) === 'sandbox' ) : ?> selected<?php endif; ?>>
							Sandbox
						</option>
					</select>
				</td>
			</tr>
		</table>

		<h2 class="title">Customization</h2>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="hydro_raindrop_custom_mfa_page">
						Custom MFA page
					</label>
				</th>
				<td>
					<select id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ); ?>"
							name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_MFA_PAGE ); ?>"
							class="selection">
						<option value="0">Use default Hydro Raindrop MFA page</option>
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
					<p class="description">Please make sure you have implemented the <a href="#">documented</a> shortcodes on the Custom MFA page.</p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="hydro_raindrop_custom_mfa_page">
						Custom HydroID page
					</label>
				</th>
				<td>
					<select id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ); ?>"
							name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_CUSTOM_HYDRO_ID_PAGE ); ?>"
							class="selection">
						<option value="0">Use default HydroID page</option>
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
					<p class="description">Please make sure you have implemented the <a href="#">documented</a> shortcodes on the Custom HydroID page.</p>
				</td>
			</tr>
		</table>

		<?php if ( $this->options_are_valid() ) { ?>
			<div class="error settings-error notice">
				<p><strong>CAUTION: Changing these settings will disable all Users Hydro Raindrop MFA. All users need to re-enable Hydro Raindrop MFA.</strong></p>
			</div>
		<?php } ?>

		<?php submit_button(); ?>
	</form>
</div>
