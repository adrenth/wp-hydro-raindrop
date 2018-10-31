<?php

declare( strict_types=1 );

/**
 * Hydro Raindrop MFA.
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
	<title><?php esc_html_e( 'Hydro Raindrop MFA', 'wp-hydro-raindrop' ); ?></title>
	<?php
		wp_enqueue_style( 'login' );
		do_action( 'login_enqueue_scripts' );
	?>
</head>
<?php
$classes[] = ' locale-' . sanitize_html_class( strtolower( str_replace( '_', '-', get_locale() ) ) );
$classes   = apply_filters( 'login_body_class', $classes );
$message   = Hydro_Raindrop_Authenticate::get_message( $user );
$logo      = plugin_dir_url( 'wp-hydro-raindrop/public/images/logo.svg' ) . 'logo.svg';
$image     = plugin_dir_url( 'wp-hydro-raindrop/public/images/input-message.svg' ) . 'input-message.png';
?>
<body class="login login-action-login wp-core-ui<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
	<div id="login" class="hydro-raindrop-mfa">
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
					<?php esc_html_e( 'Enter security code into the Hydro app.', 'wp-hydro-raindrop' ); ?>
				</label>
			</p>
			<div id="hydro_digits" class="message-digits">
				<span class="digit"><?php echo esc_html( substr( (string) $message, 0, 1 ) ); ?></span>
				<span class="digit"><?php echo esc_html( substr( (string) $message, 1, 1 ) ); ?></span>
				<span class="digit"><?php echo esc_html( substr( (string) $message, 2, 1 ) ); ?></span>
				<span class="digit"><?php echo esc_html( substr( (string) $message, 3, 1 ) ); ?></span>
				<span class="digit"><?php echo esc_html( substr( (string) $message, 4, 1 ) ); ?></span>
				<span class="digit"><?php echo esc_html( substr( (string) $message, 5, 1 ) ); ?></span>
			</div>
			<input type="submit"
					name="hydro_raindrop_mfa_cancel"
					class="button button-secondary button-large button-cancel"
					value="<?php esc_html_e( 'Cancel', 'wp-hydro-raindrop' ); ?>">
			<p class="submit">
				<?php wp_nonce_field( 'hydro_raindrop_mfa' ); ?>
				<input type="submit"
						name="hydro_raindrop_mfa"
						class="button button-primary button-large"
						value="<?php esc_html_e( 'Authenticate', 'wp-hydro-raindrop' ); ?>">
			</p>
		</form>
	</div>
	<div class="clear"></div>
	<?php wp_footer(); ?>
</body>
</html>
