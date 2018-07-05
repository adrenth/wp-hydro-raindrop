<?php
	/*
	 * Hydro Raindrop MFA user profile form extension.
	 */

$user = wp_get_current_user();
$hydroId = (string) get_user_meta( $user->ID, 'hydro_id', true );
$hydroMfaEnabled = (bool) get_user_meta( $user->ID, 'hydro_mfa_enabled', true );
$raindropConfirmed = (bool) get_user_meta( $user->ID, 'hydro_raindrop_confirmed', true );
?>
<h2>Hydro Raindrop MFA</h2>

<?php if ( ! $raindropConfirmed ): ?>
    <p>Your account <strong>does not</strong> have Raindrop MFA enabled.</p>
<?php endif ?>

<table class="form-table hydro">
	<?php if ( $raindropConfirmed ): ?>
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
	        <?php if ( $raindropConfirmed ): ?>
                <code><?php echo $hydroId; ?></code>
	        <?php else: ?>
                <input type="text"
                       name="hydro_id"
                       id="hydro_id"
                       maxlength="7"
                       class="code"/>

                <p class="description">Enter your HydroID, visible in the Hydro mobile app.</p>
            <?php endif ?>
        </td>
    </tr>
</table>
