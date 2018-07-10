<?php
/**
 * Hydro Raindrop MFA user profile form extension.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

$hydro_id                          = (string) $user->hydro_id;
$hydro_raindrop_confirmed          = (bool) $user->hydro_raindrop_confirmed;
$has_valid_raindrop_client_options = Hydro_Raindrop::has_valid_raindrop_client_options();
?>
<h2>Hydro Raindrop MFA</h2>
<?php if ( ! $has_valid_raindrop_client_options ) : ?>
    <p class="error-message">
		<?php if ( current_user_can( 'manage_options' ) ) : ?>
            The Hydro Raindrop MFA plugin is not properly configured, please review the
            <a href="options-general.php?page=<?php echo $this->plugin_name; ?>-options">Hydro Raindrop MFA Settings</a>
            and try again.
		<?php else : ?>
            The Hydro Raindrop MFA plugin is not properly configured.
		<?php endif; ?>
    </p>
<?php else : ?>

	<?php if ( ! $hydro_raindrop_confirmed ) : ?>
        <p class="error-message">Your account <strong>does not</strong> have Raindrop MFA enabled.</p>
	<?php endif ?>

    <table class="form-table hydro">
		<?php if ( $hydro_raindrop_confirmed ) : ?>
            <tr>
                <th scope="row">Disable</th>
                <td>
                    <button type="submit" class="button" name="disable_hydro_mfa" id="disable_hydro_mfa_button">
                        Disable and unregister Raindrop MFA
                    </button>
                </td>
            </tr>
		<?php endif ?>
        <tr>
            <th scope="row">
                <label for="hydro_id">HydroID</label>
            </th>
            <td>
				<?php if ( $hydro_raindrop_confirmed ) : ?>
                    <code><?php echo esc_html( $hydro_id ); ?></code>
				<?php else : ?>
                    <input type="text"
                           name="hydro_id"
                           id="hydro_id"
                           maxlength="7"
                           class="code"
                           value="<?php esc_attr( $_POST['hydro_id'] ?? '' ); ?>"
                           autocomplete="off"/>

                    <p class="description">Enter your HydroID, visible in the Hydro mobile app.</p>
				<?php endif ?>
            </td>
        </tr>
    </table>

<?php endif ?>
