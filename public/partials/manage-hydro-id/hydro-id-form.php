<?php

declare( strict_types=1 );

/**
 * Sub-partial: Hydro Raindrop Hydro ID form.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	exit;
}

$hydro_id          = (string) $user->hydro_id;
$hydro_mfa_enabled = (bool) $user->hydro_mfa_enabled;
?>

<table class="form-table hydro">
	<?php if ( $hydro_mfa_enabled ) : ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Unregister', 'wp-hydro-raindrop' ); ?></th>
			<td>
				<button type="submit" class="button" name="disable_hydro_mfa" id="disable_hydro_mfa_button">
					<?php esc_html_e( 'Unregister Raindrop MFA', 'wp-hydro-raindrop' ); ?>
				</button>
			</td>
		</tr>
	<?php endif ?>
	<tr>
		<th scope="row">
			<label for="hydro_id">HydroID</label>
		</th>
		<td>
			<?php if ( $hydro_mfa_enabled ) : ?>
				<input type="text"
					id="hydro_id"
					maxlength="7"
					class="code"
					value="<?php echo esc_attr( $hydro_id ); ?>"
					disabled
					autocomplete="off"/>
			<?php else : ?>
				<input type="text"
					name="hydro_id"
					id="hydro_id"
					size="7"
					minlength="3"
					maxlength="32"
					class="code"
					value="<?php esc_attr( $_POST['hydro_id'] ?? '' ); ?>"
					autocomplete="off"/>

				<p class="description">
					<?php esc_html_e( 'Enter your HydroID, visible in the Hydro mobile app.', 'wp-hydro-raindrop' ); ?>
				</p>
			<?php endif ?>
		</td>
	</tr>
</table>

