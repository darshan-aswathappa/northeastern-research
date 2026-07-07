<?php
/**
 * Fellows: Ambition Banner — flat black band with a photo collage (large photo
 * over a red stat tile and a second photo) beside eyebrow / serif heading /
 * lead. On small screens the copy leads and the second photo is dropped.
 *
 * @package nu-research
 */

$nu_eyebrow             = get_field( 'eyebrow' );
$nu_heading             = get_field( 'heading' );
$nu_lead                = get_field( 'lead' );
$nu_stat_value          = get_field( 'stat_value' );
$nu_stat_caption        = get_field( 'stat_caption' );
$nu_image_primary       = get_field( 'image_primary' );
$nu_image_primary_alt   = get_field( 'image_primary_alt' );
$nu_image_secondary     = get_field( 'image_secondary' );
$nu_image_secondary_alt = get_field( 'image_secondary_alt' );
?>
<section class="section ambition-section">
	<div class="wrap ambition-inner">
		<div class="ambition-copy" data-aos="fade-up">
			<p class="eyebrow eyebrow-on-dark"><?php echo esc_html( $nu_eyebrow ); ?></p>
			<h2 class="ambition-heading"><?php echo esc_html( $nu_heading ); ?></h2>
			<p class="ambition-lead"><?php echo esc_html( $nu_lead ); ?></p>
		</div>
		<div class="ambition-media" data-aos="fade-up" data-aos-delay="100">
			<div class="ambition-photo ambition-photo-primary">
				<img src="<?php echo esc_url( nu_research_img( $nu_image_primary ) ); ?>" alt="<?php echo esc_attr( $nu_image_primary_alt ); ?>" width="1600" height="900" loading="lazy">
			</div>
			<div class="ambition-stat">
				<p class="ambition-stat-value"><?php echo esc_html( $nu_stat_value ); ?></p>
				<p class="ambition-stat-caption"><?php echo esc_html( $nu_stat_caption ); ?></p>
			</div>
			<div class="ambition-photo ambition-photo-secondary">
				<img src="<?php echo esc_url( nu_research_img( $nu_image_secondary ) ); ?>" alt="<?php echo esc_attr( $nu_image_secondary_alt ); ?>" width="1000" height="750" loading="lazy">
			</div>
		</div>
	</div>
</section>
