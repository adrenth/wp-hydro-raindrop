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

use Hydro_Raindrop_Helper as Helper;

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
$active_tab           = $_GET['tab'] ?? Hydro_Raindrop_Admin::OPTION_GROUP_SYSTEM_REQUIREMENTS;
$helper               = new Helper();
$requirement_checker  = new Hydro_Raindrop_RequirementChecker();
$requirements_are_met = $requirement_checker->passes();
$options_are_valid    = Hydro_Raindrop::has_valid_raindrop_client_options() && $this->options_are_valid();

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

		<?php elseif ( Hydro_Raindrop_Admin::OPTION_GROUP_API_SETTINGS === $active_tab ) : ?>

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
								value="<?php echo esc_attr( get_option( Helper::OPTION_APPLICATION_ID ) ); ?>"
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
								value="<?php echo esc_attr( get_option( Helper::OPTION_CLIENT_ID ) ); ?>"
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
								value="<?php echo esc_attr( get_option( Helper::OPTION_CLIENT_SECRET ) ); ?>"
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
							<option value="production"<?php if ( get_option( Helper::OPTION_ENVIRONMENT ) === 'production' ) : ?> selected<?php endif; ?>>
								Production
							</option>
							<option value="sandbox"<?php if ( get_option( Helper::OPTION_ENVIRONMENT ) === 'sandbox' ) : ?> selected<?php endif; ?>>
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

		<?php elseif ( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION === $active_tab ) : ?>
			<?php settings_fields( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION ); ?>
			<?php do_settings_sections( Hydro_Raindrop_Admin::OPTION_GROUP_CUSTOMIZATION ); ?>

			<?php
			$posts      = $this->get_post_options();
			$mfa_method = get_option( Helper::OPTION_MFA_METHOD )
			?>

			<h2>Multi Factor Authentication</h2>

			<table class="form-table">
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_MFA_METHOD ); ?>">
							Method
						</label>
					</th>
					<td>
						<fieldset>
							<p>
								<label>
									<input type="radio"
											id="<?php echo esc_attr( Helper::OPTION_MFA_METHOD ); ?>"
											name="<?php echo esc_attr( Helper::OPTION_MFA_METHOD ); ?>"
											value="<?php echo esc_attr( Helper::MFA_METHOD_OPTIONAL ); ?>"
										<?php if ( ! $mfa_method || Helper::MFA_METHOD_OPTIONAL === $mfa_method ) : ?>
											checked="checked"
										<?php endif; ?>>
									<span>Optional</span>
									User decides to enable MFA on their account.
								</label>
								<br>
								<label>
									<input type="radio"
											name="<?php echo esc_attr( Helper::OPTION_MFA_METHOD ); ?>"
											value="<?php echo esc_attr( Helper::MFA_METHOD_PROMPTED ); ?>"
										<?php if ( Helper::MFA_METHOD_PROMPTED === $mfa_method ) : ?>
											checked="checked"
										<?php endif; ?>>
									<span>Prompted</span>
									MFA setup screen will be prompted after logging in. User can skip this step and setup MFA later. (default)
								</label>
								<br>
								<label>
									<input type="radio"
											name="<?php echo esc_attr( Helper::OPTION_MFA_METHOD ); ?>"
											value="<?php echo esc_attr( Helper::MFA_METHOD_ENFORCED ); ?>"
										<?php if ( Helper::MFA_METHOD_ENFORCED === $mfa_method ) : ?>
											checked="checked"
										<?php endif; ?>>
									<span>Enforced</span>
									MFA is forced site wide. Users will have to setup MFA after logging in.
								</label>
							</p>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_MFA_MAXIMUM_ATTEMPTS ); ?>">
							MFA Maximum Attempts
						</label>
					</th>
					<td>
						<input type="number"
								size="3"
								id="<?php echo esc_attr( Helper::OPTION_MFA_MAXIMUM_ATTEMPTS ); ?>"
								name="<?php echo esc_attr( Helper::OPTION_MFA_MAXIMUM_ATTEMPTS ); ?>"
								value="<?php echo esc_attr( (int) get_option( Helper::OPTION_MFA_MAXIMUM_ATTEMPTS ) ); ?>"> (default: 0 = unlimited)
						<p class="description">The user account will be blocked if the number of attempts exceeds this value.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_PAGE_MFA ); ?>">
							MFA Page
						</label>
					</th>
					<td>
						<select id="<?php echo esc_attr( Helper::OPTION_PAGE_MFA ); ?>"
								name="<?php echo esc_attr( Helper::OPTION_PAGE_MFA ); ?>"
								class="selection">
							<option value="0">Use default MFA Page</option>
							<option value="0">---</option>
							<?php
							/**
							 * Type hinting.
							 *
							 * @var array $posts
							 */
							foreach ( $posts as $post_id => $post ) :
								?>
								<?php $selected = (int) get_option( Helper::OPTION_PAGE_MFA ) === $post_id ? ' selected' : ''; ?>
								<option value="<?php echo esc_attr( $post_id ); ?>"<?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $post ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">
							The shortcode <code>[hydro_raindrop_mfa]</code> must be present in the page content or template.
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_PAGE_SETUP ); ?>">
							MFA Setup Page
						</label>
					</th>
					<td>
						<select id="<?php echo esc_attr( Helper::OPTION_PAGE_SETUP ); ?>"
								name="<?php echo esc_attr( Helper::OPTION_PAGE_SETUP ); ?>"
								class="selection">
							<option value="0">Use default Hydro MFA Setup Page</option>
							<option value="0">---</option>
							<?php
							/**
							 * Type hinting.
							 *
							 * @var array $posts
							 */
							foreach ( $posts as $post_id => $post ) :
								?>
								<?php $selected = (int) get_option( Helper::OPTION_PAGE_SETUP ) === $post_id ? ' selected' : ''; ?>
								<option value="<?php echo esc_attr( $post_id ); ?>"<?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $post ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">
							The shortcode <code>[hydro_raindrop_setup]</code> must be present in the page content or template.
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_PAGE_SETTINGS ); ?>">
							MFA Settings Page
						</label>
					</th>
					<td>
						<select id="<?php echo esc_attr( Helper::OPTION_PAGE_SETTINGS ); ?>"
								name="<?php echo esc_attr( Helper::OPTION_PAGE_SETTINGS ); ?>"
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
								<?php $selected = (int) get_option( Helper::OPTION_PAGE_SETTINGS ) === $post_id ? ' selected' : ''; ?>
								<option value="<?php echo esc_attr( $post_id ); ?>"<?php echo esc_attr( $selected ); ?>>
									<?php echo esc_html( $post ); ?>
								</option>
							<?php endforeach; ?>
						</select>
						<p class="description">
							The shortcode <code>[hydro_raindrop_setting]</code> must be present in the page content or template.
						</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row">
						<label for="<?php echo esc_attr( Helper::OPTION_POST_VERIFICATION_TIMEOUT ); ?>">
							MFA Lifetime for Posts
						</label>
					</th>
					<td>
						<input type="number"
								size="4"
								id="<?php echo esc_attr( Helper::OPTION_POST_VERIFICATION_TIMEOUT ); ?>"
								name="<?php echo esc_attr( Helper::OPTION_POST_VERIFICATION_TIMEOUT ); ?>"
								value="<?php echo esc_attr( (int) get_option( Helper::OPTION_POST_VERIFICATION_TIMEOUT ) ); ?>"> seconds (default: 3600 = 1 hour)
						<p class="description">
							To add an extra layer of security, Editors can require Users to perform MFA before viewing a Post.<br>
							The MFA Lifetime for Posts indicates how long the Post will be accessible. If the lifetime expires, Users need to perform MFA again to view the Post.<br>
						</p>
					</td>
				</tr>
			</table>
			<?php submit_button(); ?>
		<?php endif; ?>

	</form>
</div>
