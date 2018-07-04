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
<html <?php language_attributes(); ?> class="no-js">
<head>
	<?php wp_head(); ?>
</head>
<body>
<!-- This file should primarily consist of HTML with a little bit of PHP. -->

<h1>Hydro Raindrop MFA</h1>

<p>Enter these 6-digits into the Hydro app.</p>

<form action="<?php echo esc_url( site_url( 'wp-login.php', 'login_post' ) ); ?>" method="post">

	<div class="message-digits">
		<span class="digit"><?php echo substr( $message, 0, 1 ) ?></span>
		<span class="digit"><?php echo substr( $message, 1, 1 ) ?></span>
		<span class="digit"><?php echo substr( $message, 2, 1 ) ?></span>
		<span class="digit"><?php echo substr( $message, 3, 1 ) ?></span>
		<span class="digit"><?php echo substr( $message, 4, 1 ) ?></span>
		<span class="digit"><?php echo substr( $message, 5, 1 ) ?></span>
	</div>

	<button type="submit" name="hydro_raindrop" value="authenticate">Authenticate</button>

</form>

<?php wp_footer(); ?>
</body>
</html>
