<?php

declare( strict_types=1 );

/**
 * Sub-partial: Invalid Raindrop Client Options.
 *
 * @package    Hydro_Raindrop
 * @author     Alwin Drenth <adrenth@gmail.com>, Ronald Drenth <ronalddrenth@gmail.com>
 */

if ( ! defined( 'HYDRO_RAINDROP_MANAGE_HYDRO_ID' ) ) {
	exit;
}
?>

<p class="error-message">
	<?php if ( current_user_can( 'manage_options' ) ) : ?>
		<?php
		echo sprintf(
			// translators: The placeholder contains a link to the Hydro Raindrop MFA Settings.
			esc_html__(
				'The Hydro Raindrop MFA plugin is not properly configured, please review the %s and try again.'
			),
			'<a href="' . esc_attr( self_admin_url( 'options-general.php?page=' . $this->plugin_name ) ) . '-options">'
				. esc_html__( 'Hydro Raindrop MFA Settings', 'wp-hydro-raindrop' )
			. '</a>'
		);
		?>
	<?php else : ?>

		<?php esc_html_e( 'The Hydro Raindrop MFA plugin is not properly configured.', 'wp-hydro-raindrop' ); ?>

	<?php endif; ?>
</p>
