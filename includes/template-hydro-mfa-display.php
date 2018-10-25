<?php
/**
 * Template Name: Hydro MFA Display Template
 *
 */

get_header(); ?>

<div id="hydro-raindrop-creds-displayer" class="main-content">
	<?php

	// Start the loop.
	while ( have_posts() ) : the_post();
		the_content();
	endwhile;

	?>
</div><!-- #main-content -->

<?php
get_footer();