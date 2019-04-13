<?php
$blocked = (bool) get_the_author_meta( Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED, $profileuser->ID );
?>
<table class="form-table">
	<tr>
		<th>
			<label for="<?php echo esc_attr( Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED ); ?>">
				<?php esc_html_e( 'Account Status' ); ?>
			</label>
		</th>
		<td>
			<label for="admin_bar_front">
				<input name="<?php echo esc_attr( Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED ); ?>"
						id="<?php echo esc_attr( Hydro_Raindrop_Helper::USER_META_ACCOUNT_BLOCKED ); ?>"
						type="checkbox"
						id="admin_bar_front"
						value="1"
					<?php if ( $blocked ) : ?>
						checked="checked"
					<?php endif; ?>>
				Blocked
			</label>
		</td>
	</tr>
	<tr>
		<th></th>
		<td>
			<a href="javascript:;" id="resetHydroId" data-user-id="<?php echo $profileuser->ID; ?>">
				<?php esc_html_e( 'Reset HydroID' ); ?>
			</a>
			<p class="description">
				<?php esc_html_e( 'After resetting the HydroID, the user is able to set-up Hydro Raindrop MFA again.' ); ?>
			</p>
		</td>
	</tr>
</table>
