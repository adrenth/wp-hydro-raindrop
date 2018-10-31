<?php

declare( strict_types=1 );

/**
 * Hydro Raindrop Setup MFA.
 *
 * @link       https://github.com/adrenth/wp-hydro-raindrop
 * @package    Hydro_Raindrop
 * @subpackage Hydro_Raindrop/public/partials
 */

?><!DOCTYPE html>
<!DOCTYPE html>
<!--[if IE 8]>
<html xmlns="http://www.w3.org/1999/xhtml" class="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 8) ]><!-->
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
	<meta http-equiv="Content-Type"
		content="<?php bloginfo( 'html_type' ); ?>; charset=<?php bloginfo( 'charset' ); ?>"/>
	<title><?php esc_html_e( 'Hydro Raindrop Setup', 'wp-hydro-raindrop' ); ?></title>
	<?php
	wp_enqueue_style( 'login' );
	do_action( 'login_enqueue_scripts' );
	?>
</head>
<?php
$classes[] = ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );

/**
 * Filters the login page body classes.
 *
 * @since 3.5.0
 *
 * @param array $classes An array of body classes.
 */
$classes = apply_filters( 'login_body_class', $classes );
$logo    = plugin_dir_url( 'wp-hydro-raindrop/public/images/logo.svg' ) . 'logo.svg';
$image   = plugin_dir_url( 'wp-hydro-raindrop/public/images/input-hydro-id.svg' ) . 'input-message.png';
?>
<body class="hydro-raindrop-setup login login-action-login wp-core-ui<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
<div id="login" class="hydro-raindrop-mfa ">
	<div class="clear"></div>
	<h1>
		<img src="<?php echo esc_attr( $logo ); ?>" height="46" alt="Hydro">
	</h1>
	<?php
	// @codingStandardsIgnoreLine
	echo ( new Hydro_Raindrop_Flash( $user->user_login ) )->render();
	?>
	<form action="" method="post">
		<p class="hydro-illustration">
			<img src="<?php echo esc_attr( $image ); ?>" width="180" alt="">
		</p>
		<p>
			<label for="hydro_digits">
				<?php esc_html_e( 'Enter your HydroID.', 'wp-hydro-raindrop' ); ?>
			</label>
		</p>
		<input type="text"
				name="hydro_id"
				title="HydroID"
				autocomplete="off"
				placeholder=""
				autofocus>
		<input type="submit"
				name="hydro_raindrop_setup_skip"
				class="button button-secondary button-large button-cancel"
				value="<?php esc_html_e( 'Skip', 'wp-hydro-raindrop' ); ?>">
		<p class="submit">
			<?php wp_nonce_field( 'hydro_raindrop_setup' ); ?>
			<input type="submit"
					name="hydro_raindrop_setup"
					class="button button-primary button-large"
					value="<?php esc_html_e( 'Submit', 'wp-hydro-raindrop' ); ?>">
		</p>
	</form>
</div>
<?php wp_footer(); ?>
</body>
</html>


