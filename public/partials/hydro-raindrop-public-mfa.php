<?php

/**
 * Provide a public-facing view for the plugin
 *
 * This file is used to markup the public-facing aspects of the plugin.
 *
 * @link       https://github.com/adrenth
 * @since      1.0.0
 *
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
	<title>Hydro Raindrop MFA</title>
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
?>
<body class="login login-action-login wp-core-ui<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
<div id="login" class="hydro-raindrop-mfa">
	<h1>
		<img src="<?php echo $logo; ?>" height="46" alt="Hydro Raindrop MFA">
	</h1>
	<?php if ($error) { ?>
	<div id="login_error"><?php echo $error; ?></div>
	<?php } ?>
	<form action="" method="post">
		<p class="hydro-illustration">
			<img src="<?php echo esc_attr( $image ) ?>" width="180" alt="">
		</p>
		<p>
			<label for="hydro_digits">
				Enter security code into the Hydro app.
			</label>
		</p>
		<div id="hydro_digits" class="message-digits">
			<span class="digit"><?php echo substr( $message, 0, 1 ); ?></span>
			<span class="digit"><?php echo substr( $message, 1, 1 ); ?></span>
			<span class="digit"><?php echo substr( $message, 2, 1 ); ?></span>
			<span class="digit"><?php echo substr( $message, 3, 1 ); ?></span>
			<span class="digit"><?php echo substr( $message, 4, 1 ); ?></span>
			<span class="digit"><?php echo substr( $message, 5, 1 ); ?></span>
		</div>
		<input type="submit"
				name="cancel_hydro_raindrop"
				class="button button-secondary button-large button-cancel"
				value="Cancel">
		<p class="submit">
			<?php wp_nonce_field( 'hydro_raindrop_mfa' ); ?>
			<input type="submit"
					name="hydro_raindrop"
					class="button button-primary button-large"
					value="Authenticate">
		</p>
	</form>
</div>
<div class="clear"></div>
</body>
<?php wp_footer(); ?>
</body>
</html>
