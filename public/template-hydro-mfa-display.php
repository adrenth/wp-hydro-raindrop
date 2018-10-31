<?php
/**
 * Template Name: Hydro MFA Display Template
 *
 * @package Hydro_Raindrop
 */

get_header();
?>
	<div id="hydro-raindrop-creds-displayer" class="main-content">
		<?php

		// Start the loop.
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;

		?>
	</div>

<?php
get_footer();
