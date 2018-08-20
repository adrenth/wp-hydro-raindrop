<?php

declare( strict_types=1 );

/**
 * Hydro Raindrop Hydro ID management form.
 *
 * This template will be exposed through the WordPress shortcode API.
 * Use shortcode [hydro_raindrop_manage_hydro_id].
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

$hydro_mfa_disabled = ! ( (bool) $user->hydro_mfa_enabled );
?>

<div class="hydro-raindrop manage-hydro-id">
	<h2><?php esc_html_e( 'Hydro Raindrop MFA', 'wp-hydro-raindrop' ); ?></h2>

	<?php if ( Hydro_Raindrop::has_valid_raindrop_client_options() ) : ?>

		<?php if ( is_wp_error( $errors ) ) : ?>

			<p class="error-message">
				<?php echo esc_html( $errors->get_error_message() ); ?>
			</p>

		<?php else : ?>

			<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-enabled.php'; ?>
			<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-disabled.php'; ?>
			<?php include __DIR__ . '/manage-hydro-id/hydro-raindrop-mfa-unconfirmed.php'; ?>

		<?php endif; ?>

		<form method="post">
			<?php include __DIR__ . '/manage-hydro-id/hydro-id-form.php'; ?>

			<?php if ( $hydro_mfa_disabled ) : ?>
				<button type="submit" class="button" name="save_hydro_id">
					<?php esc_html_e( 'Hydro Raindrop MFA', 'wp-hydro-raindrop' ); ?>
				</button>
			<?php endif; ?>

			<?php echo wp_nonce_field( 'hydro_raindrop_hydro_id', '_hydro_id_nonce' ); ?>
		</form>

	<?php else : ?>

		<?php include __DIR__ . '/manage-hydro-id/invalid-raindrop-client-options.php'; ?>

	<?php endif; ?>

</div>
