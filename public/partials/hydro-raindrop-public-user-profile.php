<?php

declare( strict_types=1 );

/**
 * Hydro Raindrop MFA user profile form extension.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! isset( $user ) ) {
	exit;
}

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	define( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID', true );
}
?>
<h2><?php esc_html_e( 'Hydro Raindrop MFA', 'wp-hydro-raindrop' ); ?></h2>

<?php if ( Hydro_Raindrop::has_valid_raindrop_client_options() ) : ?>

	<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-enabled.php'; ?>
	<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-disabled.php'; ?>
	<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-unconfirmed.php'; ?>
	<?php include __DIR__ . '/manage-hydro-id/hydro-id-form.php'; ?>

<?php else : ?>

	<?php include __DIR__ . '/manage-hydro-id/invalid-raindrop-client-options.php'; ?>

<?php endif ?>
