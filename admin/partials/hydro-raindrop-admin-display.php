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
				<th scope="row">Application ID</th>
				<td>
					<input type="text"
							class="regular-text code"
							name="hydro_raindrop_application_id"
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_application_id' ) ); ?>"
							autocomplete="off"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Client ID</th>
				<td>
					<input type="text"
							class="regular-text code"
							name="hydro_raindrop_client_id"
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_client_id' ) ); ?>"
							autocomplete="off"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Client Secret</th>
				<td>
					<input type="text"
							class="regular-text code"
							name="hydro_raindrop_client_secret"
							value="<?php echo esc_attr( get_option( 'hydro_raindrop_client_secret' ) ); ?>"
							autocomplete="off"/>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">Environment</th>
				<td>
					<select name="hydro_raindrop_environment" class="selection">
						<option value="production"<?php if ( get_option( 'hydro_raindrop_environment' ) === 'production' ) { ?> selected<?php } ?>>
							Production
						</option>
						<option value="sandbox"<?php if ( get_option( 'hydro_raindrop_environment' ) === 'sandbox' ) { ?> selected<?php } ?>>
							Sandbox
						</option>
					</select>
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
