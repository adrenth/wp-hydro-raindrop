<?php
/**
 * Default shortcode template for [hydro_raindrop_mfa].
 *
 * @package Hydro_Raindrop
 */

$image          = plugin_dir_url(dirname(__FILE__)) . '../images/input-message.png';
$custom_logo_id = get_theme_mod( 'custom_logo' );
$custom_logo    = wp_get_attachment_image_src( $custom_logo_id, 'full' );
?>
<div id="hydro-container" class="hydro-mfa">
	<?php echo do_shortcode( '[hydro_raindrop_mfa_form_open]' ); ?>
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
		<div class="item full nobottom">
			<img src="<?php echo esc_url( $image ); ?>" class="hydro-illustration"
					alt="<?php esc_html_e( 'Enter Code in the Hydro App', 'wp-hydro-raindrop' ); ?>">
			<label><?php esc_html_e( 'Enter Security Code into the Hydro App', 'wp-hydro-raindrop' ); ?></label>
			<div class="hydro-digits">
				<?php echo do_shortcode( '[hydro_raindrop_mfa_digits]' ); ?>
			</div>
			<?php echo do_shortcode( '[hydro_raindrop_mfa_flash]' ); ?>
		</div>
		<div class="item actions">
			<?php echo do_shortcode( '[hydro_raindrop_mfa_button_cancel class="secondary"]' ); ?>
		</div>
	</div>
	<div class="hydro-footer">
		<div class="hydro-submission">
			<?php echo do_shortcode( '[hydro_raindrop_mfa_button_authorize class="primary"]' ); ?>
		</div>
		<div class="hydro-abt-us">
			<a href="https://www.hydrogenplatform.com/" target="_blank" title="This website is powered by Hydro - Blockchain based multi factor authentication, visit us to know more">
				<label><?php esc_html_e( 'Powered by', 'wp-hydro-raindrop' ); ?></label>
				<img src="https://www.hydrogenplatform.com/images/logo_hydro.svg" alt="Hydro">
			</a>
		</div>
	</div>
	<?php echo do_shortcode( '[hydro_raindrop_mfa_form_close]' ); ?>
</div>
