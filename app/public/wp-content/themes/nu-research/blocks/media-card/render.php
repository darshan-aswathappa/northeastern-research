<?php
/**
 * Fellows: Media Card — image + heading + body, optionally reversed.
 *
 * @package nu-research
 */

$nu_heading   = get_field( 'heading' );
$nu_body      = get_field( 'body' );
$nu_image     = get_field( 'image' );
$nu_image_alt = get_field( 'image_alt' );
$nu_reverse   = get_field( 'reverse' );
$nu_section   = get_field( 'section' );

$nu_card_class = 'media-card' . ( $nu_reverse ? ' media-card-reverse' : '' );
$nu_img_aos    = $nu_reverse ? 'fade-left' : 'fade-right';
$nu_body_aos   = $nu_reverse ? 'fade-right' : 'fade-left';
?>
<section class="<?php echo esc_attr( $nu_section ); ?>">
	<div class="wrap">
		<div class="<?php echo esc_attr( $nu_card_class ); ?>">
			<div class="media-card-image" data-aos="<?php echo esc_attr( $nu_img_aos ); ?>">
				<img src="<?php echo esc_url( nu_research_img( $nu_image ) ); ?>" alt="<?php echo esc_attr( $nu_image_alt ); ?>" width="1000" height="750" loading="lazy">
			</div>
			<div class="media-card-body" data-aos="<?php echo esc_attr( $nu_body_aos ); ?>" data-aos-delay="100">
				<h2><?php echo esc_html( $nu_heading ); ?></h2>
				<p><?php echo esc_html( $nu_body ); ?></p>
			</div>
		</div>
	</div>
</section>
