<?php
/**
 * Default shortcode template for [hydro_raindrop_setup].
 *
 * @package Hydro_Raindrop
 */

?>
<div class="hydro-raindrop hydro-raindrop-setup">
	<?php echo do_shortcode( '[hydro_raindrop_setup_flash]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_setup_form_open]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_setup_hydro_id]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_setup_button_submit class="btn btn-primary"]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_setup_button_skip class="btn btn-default"]' ); ?>
	<?php echo do_shortcode( '[hydro_raindrop_setup_form_close]' ); ?>
</div>
