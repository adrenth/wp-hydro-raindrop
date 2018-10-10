<?php
/**
 * Default shortcode template for [hydro_raindrop_settings].
 *
 * @package Hydro_Raindrop
 */

?>
<div id="hydro-container" class="hydro-settings">
	<?php echo do_shortcode( '[hydro_raindrop_settings_flash]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_settings_form_open]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_settings_checkbox_mfa_enabled]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_settings_button_submit class="btn btn-primary"]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_settings_form_close]' ); ?>
</div>
