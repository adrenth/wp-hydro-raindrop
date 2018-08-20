<?php

declare( strict_types=1 );

/**
 * Sub-partial: Hydro Raindrop MFA disabled.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	exit;
}

$hydro_raindrop_mfa_disabled = ! ( (bool) $user->hydro_mfa_enabled );
?>

<?php if ( $hydro_raindrop_mfa_disabled ) : ?>
	<p class="success-message">
		<?php esc_html_e( 'Your account does not have Hydro Raindrop MFA enabled.', 'wp-hydro-raindrop' ); ?>
	</p>
<?php endif ?>
