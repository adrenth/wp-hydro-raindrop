<?php

declare( strict_types=1 );

/**
 * Sub-partial: Hydro Raindrop MFA unconfirmed.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	exit;
}

$hydro_raindrop_mfa_disabled   = ! ( (bool) $user->hydro_mfa_enabled );
$hydro_raindrop_confirmed      = (bool) $user->hydro_raindrop_confirmed;
$hydro_raindrop_page_mfa       = (int) get_option( Hydro_Raindrop_Helper::OPTION_PAGE_MFA );
$hydro_raindrop_custom_mfa_url = get_permalink( $hydro_raindrop_page_mfa );
?>

<?php if ( ! $hydro_raindrop_mfa_disabled && ! $hydro_raindrop_confirmed ) : ?>
	<p class="error-message">
		<?php esc_html_e( 'Your account does have Hydro Raindrop MFA enabled, but it is unconfirmed.', 'wp-hydro-raindrop' ); ?>

		<?php if ( $hydro_raindrop_page_mfa > 0 && get_post_status( $hydro_raindrop_page_mfa ) === 'publish' ) : ?>

			<a href="<?php echo esc_attr( $hydro_raindrop_custom_mfa_url . '?hydro-raindrop-verify=1' ); ?>"
				class="hydro-raindrop-link-confirm">
				<?php esc_html_e( 'Confirm Hydro Raindrop MFA.', 'wp-hydro-raindrop' ); ?>
			</a>

		<?php else : ?>

			<a href="<?php echo esc_attr( self_admin_url( 'profile.php?hydro-raindrop-verify=1' ) ); ?>"
				class="hydro-raindrop-link-confirm">
				<?php esc_html_e( 'Confirm Hydro Raindrop MFA.', 'wp-hydro-raindrop' ); ?>
			</a>

		<?php endif; ?>
	</p>
<?php endif ?>
