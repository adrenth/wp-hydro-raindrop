<?php
/**
 * Default shortcode template for [hydro_raindrop_settings].
 *
 * @package Hydro_Raindrop
 */

?>
<div id="hydro-container" class="hydro-settings">
	<?php echo do_shortcode( '[hydro_raindrop_settings_form_open]' ); ?>
	<div class="hydro-header">
		<h1><?php esc_html_e( 'Hydro MFA Settings', 'wp-hydro-raindrop' ); ?></h1>
	</div>
	<div class="hydro-body">
		<div class="item side">
			<?php echo do_shortcode( '[hydro_raindrop_settings_checkbox_mfa_enabled]' ); ?>
			<?php echo do_shortcode( '[hydro_raindrop_settings_flash]' ); ?>
		</div>
	</div>
	<div class="hydro-footer">
		<div class="hydro-submission">
			<?php echo do_shortcode( '[hydro_raindrop_settings_button_submit class="btn btn-primary primary"]' ); ?>
		</div>
		<div class="hydro-abt-us">
			<a href="https://www.hydrogenplatform.com/" target="_blank" title="This website is powered by Hydro - Blockchain based multi factor authentication, visit us to know more">
				<label><?php esc_html_e( 'Powered by', 'wp-hydro-raindrop' ); ?></label>
				<img src="https://www.hydrogenplatform.com/images/logo_hydro.svg" alt="Hydro">
			</a>
		</div>
	</div>
	<?php echo do_shortcode( '[hydro_raindrop_settings_form_close]' ); ?>
</div>
