<?php
/**
 * Default shortcode template for [hydro_raindrop_setup].
 *
 * @package Hydro_Raindrop
 */

$image          = plugin_dir_url(dirname(__FILE__)) . '../images/input-hydro-id.png';
$custom_logo_id = get_theme_mod( 'custom_logo' );
$custom_logo    = wp_get_attachment_image_src( $custom_logo_id, 'full' );
?>
<div id="hydro-container" class="hydro-setup">
	<?php echo do_shortcode( '[hydro_raindrop_setup_form_open]' ); ?>
	<div class="hydro-header">
		<?php if ( is_array( $custom_logo ) && isset( $custom_logo[0] ) ): ?>
			<div class="logo">
				<a href="<?php echo esc_attr( home_url( '/' ) ); ?>">
					<img src="<?php echo esc_url( $custom_logo[0] ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
				</a>
			</div>
		<?php endif; ?>
		<h1><?php esc_html_e( 'Hydro MFA', 'wp-hydro-raindrop' ); ?></h1>
	</div>
	<div class="hydro-body">
		<div class="item full">
			<img src="<?php echo esc_url( $image ); ?>" class="hydro-illustration"
					alt="<?php esc_html_e( 'Enter your HydroID', 'wp-hydro-raindrop' ); ?>">
			<label><?php esc_html_e( 'Enter your HydroID', 'wp-hydro-raindrop' ); ?></label>
			<?php echo do_shortcode( '[hydro_raindrop_setup_hydro_id]' ); ?>
			<?php echo do_shortcode( '[hydro_raindrop_setup_flash]' ); ?>
			<div class="notice info">
				<h3>Steps for getting a HydroID</h3>
				<span>Download Free Hydro Mobile App
					<a href="https://itunes.apple.com/app/id1406519814">iOS</a>/<a href="https://play.google.com/store/apps/details?id=com.hydrogenplatform.hydro">Android</a>
				</span>
				<span>Setup Mobile App and enter HydroID to continue.</span>
			</div>

		</div>
		<div class="item actions">
			<?php echo do_shortcode( '[hydro_raindrop_setup_button_skip class="secondary"]' ); ?>
		</div>
	</div>
	<div class="hydro-footer">
		<div class="hydro-submission">
			<?php echo do_shortcode( '[hydro_raindrop_setup_button_submit class="primary"]' ); ?>
		</div>
		<div class="hydro-abt-us">
			<a href="https://www.hydrogenplatform.com/" target="_blank" title="This website is powered by Hydro - Blockchain based multi factor authentication, visit us to know more">
				<label><?php esc_html_e( 'Powered by', 'wp-hydro-raindrop' ); ?></label>
				<img src="https://www.hydrogenplatform.com/images/logo_hydro.svg" alt="Hydro">
			</a>
		</div>
	</div>
	<?php echo do_shortcode( '[hydro_raindrop_setup_form_close]' ); ?>
</div>
