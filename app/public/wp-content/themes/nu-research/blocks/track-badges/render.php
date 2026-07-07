<?php
/**
 * Fellows: Track Badges — a row of outlined badges listing the research tracks.
 *
 * @package nu-research
 */

$nu_label  = get_field( 'label' );
$nu_tracks = get_field( 'tracks' );
$nu_tracks = is_array( $nu_tracks ) ? $nu_tracks : array();
?>
<section class="section section-tight" aria-label="<?php echo esc_attr( $nu_label ); ?>">
	<div class="wrap">
		<ul class="badge-row">
			<?php foreach ( $nu_tracks as $i => $track ) : ?>
				<li class="badge badge-outline" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 75 ); ?>"><?php echo esc_html( $track['name'] ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
