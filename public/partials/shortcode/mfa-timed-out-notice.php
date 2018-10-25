<?php
/**
 * Default shortcode template for [hydro_raindrop_mfa_timed_out_notice].
 *
 * @package Hydro_Raindrop
 */

$logo = plugin_dir_url( dirname( __DIR__ ) ) . 'public/images/logo.svg';
?>
<div id="hydro-mfa-timed-out-notice">
	<div class="logo">
		<img src="<?php echo esc_attr( $logo ); ?>"
				alt="<?php esc_attr_e( 'Hydro MFA Timed Out', 'wp-hydro-raindrop' ); ?>">
	</div>
	<div class="text">
		<span><?php esc_html_e( 'Multi Factor Authentication Timed Out, Please retry!', 'wp-hydro-raindrop' ); ?></span>
	</div>
	<div class="close"><a href="#" title="<?php esc_attr_e( 'Close Notice', 'wp-hydro-raindrop' ); ?>">x</a>
	</div>
</div>
