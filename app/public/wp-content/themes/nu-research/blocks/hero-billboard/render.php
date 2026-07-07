<?php
/**
 * Fellows: Hero Billboard — full-black billboard hero: eyebrow, large serif
 * heading, lead, CTA, and the photo on a side panel.
 *
 * @package nu-research
 */

$nu_eyebrow   = get_field( 'eyebrow' );
$nu_heading   = get_field( 'heading' );
$nu_lead      = get_field( 'lead' );
$nu_cta_label = get_field( 'cta_label' );
$nu_cta_slug  = get_field( 'cta_slug' );
$nu_image     = get_field( 'image' );
$nu_image_alt = get_field( 'image_alt' );
?>
<section class="hero-billboard">
	<div class="hero-billboard-inner wrap">
		<div class="hero-billboard-content">
			<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $nu_eyebrow ); ?></p>
			<h1 class="hero-billboard-heading"><?php echo esc_html( $nu_heading ); ?></h1>
			<?php if ( $nu_lead ) : ?>
				<p class="hero-billboard-lead"><?php echo esc_html( $nu_lead ); ?></p>
			<?php endif; ?>
			<?php nu_research_cta( $nu_cta_label, nu_research_page_url( $nu_cta_slug ) ); ?>
		</div>
		<div class="hero-billboard-media">
			<img src="<?php echo esc_url( nu_research_img( $nu_image ) ); ?>" alt="<?php echo esc_attr( $nu_image_alt ); ?>" width="1600" height="900" fetchpriority="high">
		</div>
	</div>
</section>
