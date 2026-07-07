<?php
/**
 * Front page. Renders the admin-managed "Home" page content, which is built
 * from the WordPress Research Fellows SCF blocks (hero, program overview,
 * media cards, research tracks, CTA). Editing happens in Pages -> Home; the
 * markup is produced by the render.php templates in blocks/.
 *
 * @package nu-research
 */

get_header();

while ( have_posts() ) :
	the_post();
	the_content();
endwhile;

get_footer();
