<?php

declare( strict_types=1 );

/**
 * Sub-partial: Hydro Raindrop MFA enabled.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	exit;
}

$hydro_raindrop_mfa_enabled = (bool) $user->hydro_mfa_enabled;
$hydro_raindrop_confirmed   = (bool) $user->hydro_raindrop_confirmed;
?>

<?php if ( $hydro_raindrop_mfa_enabled && $hydro_raindrop_confirmed ) : ?>
	<p class="success-message">
		<?php esc_html_e( 'Your account has Hydro Raindrop MFA enabled and confirmed.', 'wp-hydro-raindrop' ); ?>
	</p>
<?php endif ?>
