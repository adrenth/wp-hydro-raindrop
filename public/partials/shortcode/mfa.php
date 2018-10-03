<?php
/**
 * Default shortcode template for [hydro_raindrop_mfa].
 *
 * @package Hydro_Raindrop
 */

?>
<div class="hydro-raindrop hydro-raindrop-mfa">
	<?php echo do_shortcode( '[hydro_raindrop_mfa_flash]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_form_open]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_digits]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_button_cancel class="btn btn-default"]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_button_authorize class="btn btn-primary"]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_form_close]' ); ?>
</div>
