<?php
/**
 * Fellows: Section Header — eyebrow, heading, and intro paragraph.
 *
 * @package nu-research
 */

$nu_eyebrow = get_field( 'eyebrow' );
$nu_heading = get_field( 'heading' );
$nu_intro   = get_field( 'intro' );
?>
<section class="section" data-aos="fade-up">
	<div class="wrap">
		<?php nu_research_section_header( $nu_eyebrow, $nu_heading, $nu_intro ); ?>
	</div>
</section>
