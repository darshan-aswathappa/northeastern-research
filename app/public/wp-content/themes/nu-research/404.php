<?php
/**
 * 404 template.
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">
	<section class="section-block">
		<?php
		nu_research_section_header(
			__( 'Error 404', 'nu-research' ),
			__( 'Page not found', 'nu-research' ),
			__( 'The page you’re looking for doesn’t exist or may have moved. Try one of the program pages from the navigation above.', 'nu-research' ),
			'h1'
		);
		?>
		<?php nu_research_cta( __( 'Back to Home', 'nu-research' ), home_url( '/' ) ); ?>
	</section>
</div>

<?php get_footer(); ?>
