<?php
/**
 * Default shortcode template for [hydro_raindrop_mfa_timed_out_notice].
 *
 * @package Hydro_Raindrop
 */

$logo = plugin_dir_url( dirname( __DIR__ ) ) . 'images/logo.svg';
?>
<div id="hydro-mfa-timed-out-notice">
	<div class="logo">
		<img src="<?php echo esc_attr( $logo ); ?>" alt="Hydro">
	</div>
	<div class="text">
		<span><?php esc_html_e( 'Multi Factor Authentication timed out, please retry.', 'wp-hydro-raindrop' ); ?></span>
	</div>
	<div class="close"><a href="#" title="<?php esc_attr_e( 'Close Notice', 'wp-hydro-raindrop' ); ?>">x</a>
	</div>
</div>
