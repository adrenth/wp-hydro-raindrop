<?php

declare( strict_types=1 );

/**
 * User Profile
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>
 */

// @codingStandardsIgnoreStart
$hydro_id                   = (string) get_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_HYDRO_ID, true );
$hydro_raindrop_mfa_enabled = (bool) get_user_meta( $user->ID, Hydro_Raindrop_Helper::USER_META_MFA_ENABLED, true );
$hydro_raindrop_mfa_method  = (string) get_option( Hydro_Raindrop_Helper::OPTION_MFA_METHOD, true );
// @codingStandardsIgnoreEnd
?>
<table class="form-table">
	<tr>
		<th scope="row"><?php esc_html_e( 'Hydro Raindrop MFA', 'wp-hydro-raindrop' ); ?></th>
		<td>
			<label>
				<input name="<?php echo esc_attr( Hydro_Raindrop_Helper::USER_META_MFA_ENABLED ); ?>"
						type="checkbox"
						value="1"
					<?php if ( $hydro_raindrop_mfa_enabled ) : ?>
						checked
					<?php endif; ?>
					<?php if ( Hydro_Raindrop_Helper::MFA_METHOD_ENFORCED === $hydro_raindrop_mfa_method ) : ?>
						disabled
					<?php endif; ?>>
				Enable Multi Factor Authentication
			</label>
		</td>
	</tr>
</table>
