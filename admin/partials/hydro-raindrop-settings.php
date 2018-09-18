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

$groups = [
	Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS => 'System Requirements',
	Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS        => 'API Settings',
	Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION       => 'Customization',
];

// @codingStandardsIgnoreLine
$active_tab             = $_GET['tab'] ?? Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS;
$helper                 = new Hydro_Raindrop_Helper();
$requirement_checker    = new Hydro_Raindrop_RequirementChecker();
$requirements_are_met   = $requirement_checker->passes();
$hydro_raindrop_enabled = $helper->is_hydro_raindrop_enabled();
$options_are_valid      = Hydro_Raindrop::has_valid_raindrop_client_options() && $this->options_are_valid();

$tabs = [];

foreach ( $groups as $group => $caption ) {

	$valid    = null;
	$disabled = false;

	if ( Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS === $group ) {
		$valid = $requirements_are_met;
	}

	if ( Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS === $group ) {
		$valid    = $options_are_valid;
		$disabled = ! $requirements_are_met;
	}

	if ( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION === $group ) {
		$disabled = ! $options_are_valid || ! $requirements_are_met;
	}

	$classes  = $group === $active_tab ? 'nav-tab nav-tab-active' : 'nav-tab';
	$classes .= $disabled ? ' button-disabled' : '';

	$tabs[ $group ] = [
		'url'      => sprintf( '?page=%s&tab=%s', $this->plugin_name, $group ),
		'classes'  => $classes,
		'caption'  => $caption,
		'valid'    => $valid,
		'disabled' => $disabled,
	];

}
?>
<div class="wrap hydro-raindrop-settings">
	<h1>Hydro Raindrop Settings</h1>

	<?php settings_errors(); ?>

	<h2 class="nav-tab-wrapper">
		<?php foreach ( $tabs as $group => $tab ) : ?>
			<a href="<?php echo $tab['disabled'] ? '#' : esc_attr( $tab['url'] ); ?>"
					class="<?php echo esc_attr( $tab ['classes'] ); ?>">
				<?php if ( null !== $tab['valid'] ) : ?>
					<?php if ( $tab['valid'] ) : ?>
						<i class="dashicons dashicons-yes"></i>
					<?php else : ?>
						<i class="dashicons dashicons-no"></i>
					<?php endif; ?>
				<?php endif; ?>
				<?php echo esc_html( $tab['caption'] ); ?>
			</a>
		<?php endforeach; ?>
	</h2>

	<form method="post" action="options.php">
		<?php if ( Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS === $active_tab ) : ?>
			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS ); ?>
			<table class="form-table">
				<thead>
				<tr valign="top">
					<th></th>
					<th>Passes</th>
					<th>Requirement</th>
				</tr>
				</thead>
				<tbody>
				<?php foreach ( $requirement_checker->get_requirements() as $result ) : ?>
				<tr valign="top">
					<th scope="row">
						<label for="hydro_raindrop_application_id">
							<?php echo esc_html( $result['label'] ); ?>
						</label>
					</th>
					<td>
						<?php if ( $requirement_checker->check( $result['test'] ) ) : ?>
							<i class="dashicons dashicons-yes"></i>
						<?php else : ?>
							<i class="dashicons dashicons-no"></i>
						<?php endif; ?>
					</td>
					<td>
						<?php echo esc_html( $result['requirement'] ); ?>
					</td>
				</tr>
				<?php endforeach; ?>
				</tbody>
			</table>

			<input name="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_ENABLED ); ?>"
					id="<?php echo esc_attr( Hydro_Raindrop_Helper::OPTION_ENABLED ); ?>"
					type="hidden"
					value="1">

		<?php elseif ( Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS === $active_tab && $hydro_raindrop_enabled ) : ?>

			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS ); ?>

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
			<?php $posts = $this->get_post_options(); ?>

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
