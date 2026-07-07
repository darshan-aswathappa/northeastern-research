<?php
/**
 * Fellows: Hero — full-width hero with eyebrow, heading, lead, and a
 * call-to-action button over a background photo.
 *
 * @package nu-research
 */

$nu_eyebrow   = get_field( 'eyebrow' );
$nu_heading   = get_field( 'heading' );
$nu_lead      = get_field( 'lead' );
$nu_cta_label = get_field( 'cta_label' );
$nu_cta_slug  = get_field( 'cta_slug' );
$nu_image     = get_field( 'image' );
?>
<section class="hero" style="background-image:url('<?php echo esc_url( nu_research_img( $nu_image ) ); ?>');">
	<div class="hero-overlay">
		<div class="wrap">
			<div class="hero-content">
				<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $nu_eyebrow ); ?></p>
				<h1 class="hero-heading"><?php echo esc_html( $nu_heading ); ?></h1>
				<p class="hero-lead"><?php echo esc_html( $nu_lead ); ?></p>
				<?php nu_research_cta( $nu_cta_label, nu_research_page_url( $nu_cta_slug ) ); ?>
			</div>
		</div>
	</div>
</section>
