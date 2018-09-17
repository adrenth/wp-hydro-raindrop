<?php

declare( strict_types=1 );

/**
 * Manage customization
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @since      2.0.0
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
	<h1>Customization</h1>
	<form method="post" action="options.php">
		<?php settings_fields( 'hydro_raindrop_customization' ); ?>
		<?php do_settings_sections( 'hydro_raindrop_customization' ); ?>

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
					<p class="description">Please make sure you have implemented the <a href="#">documented</a> shortcodes on the Hydro MFA Login Page.</p>
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
					<p class="description">Please make sure you have implemented the <a href="#">documented</a> shortcodes on the Hydro MFA Settings Page.</p>
				</td>
			</tr>
		</table>
		<?php submit_button(); ?>
	</form>
</div>
