<?php
/**
 * Fellows: CTA Band — closing call-to-action with heading, lead, and button.
 *
 * @package nu-research
 */

$nu_heading   = get_field( 'heading' );
$nu_lead      = get_field( 'lead' );
$nu_cta_label = get_field( 'cta_label' );
$nu_cta_slug  = get_field( 'cta_slug' );
?>
<section class="section section-cta">
	<div class="wrap cta-wrap" data-aos="fade-up">
		<h2><?php echo esc_html( $nu_heading ); ?></h2>
		<p class="cta-lead"><?php echo esc_html( $nu_lead ); ?></p>
		<?php nu_research_cta( $nu_cta_label, nu_research_page_url( $nu_cta_slug ) ); ?>
	</div>
</section>
