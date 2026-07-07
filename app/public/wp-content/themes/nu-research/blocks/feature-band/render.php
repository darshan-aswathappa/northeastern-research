<?php
/**
 * Fellows: Feature Band — serif heading, lead, and an arrow text-link beside a
 * large photo. Toggles cover the dark (black field) and light variants and
 * which side the photo sits on; small screens always stack copy over photo.
 *
 * @package nu-research
 */

$nu_heading    = get_field( 'heading' );
$nu_lead       = get_field( 'lead' );
$nu_link_label = get_field( 'link_label' );
$nu_link_slug  = get_field( 'link_slug' );
$nu_image      = get_field( 'image' );
$nu_image_alt  = get_field( 'image_alt' );
$nu_dark       = get_field( 'dark' );
$nu_reverse    = get_field( 'reverse' );

$nu_section_class = 'section feature-band ' . ( $nu_dark ? 'feature-band-dark' : 'feature-band-light' ) . ( $nu_reverse ? ' feature-band-reverse' : '' );
?>
<section class="<?php echo esc_attr( $nu_section_class ); ?>">
	<div class="wrap feature-band-inner">
		<div class="feature-band-copy" data-aos="fade-up">
			<h2 class="feature-band-heading"><?php echo esc_html( $nu_heading ); ?></h2>
			<p class="feature-band-lead"><?php echo esc_html( $nu_lead ); ?></p>
			<?php if ( $nu_link_label ) : ?>
				<a class="arrow-link" href="<?php echo esc_url( nu_research_page_url( $nu_link_slug ) ); ?>">
					<?php echo esc_html( $nu_link_label ); ?><span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
				</a>
			<?php endif; ?>
		</div>
		<div class="feature-band-media" data-aos="fade-up" data-aos-delay="100">
			<img src="<?php echo esc_url( nu_research_img( $nu_image ) ); ?>" alt="<?php echo esc_attr( $nu_image_alt ); ?>" width="1200" height="900" loading="lazy">
		</div>
	</div>
</section>
