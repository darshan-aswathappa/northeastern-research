<?php
/**
 * Fellows: Photo Marquee — full-bleed strip of photos that drifts sideways,
 * follows the page scroll direction (assets/js/marquee.js), and fades under a
 * blur at both ends. The track holds two identical groups so the loop wraps
 * seamlessly; the second group is decorative and hidden from assistive tech.
 *
 * @package nu-research
 */

$nu_label  = get_field( 'label' );
$nu_images = get_field( 'images' );

if ( empty( $nu_images ) ) {
	return;
}

/**
 * One copy of the photo group. Dimensions come from the file itself so the
 * browser can reserve layout space before the images load.
 *
 * @param array $images     Repeater rows ( image, image_alt ).
 * @param bool  $decorative Whether this copy is the aria-hidden duplicate.
 */
$nu_marquee_group = static function ( $images, $decorative ) {
	printf( '<div class="photo-marquee-group"%s>', $decorative ? ' aria-hidden="true"' : '' );
	foreach ( $images as $row ) {
		if ( empty( $row['image'] ) ) {
			continue;
		}
		$size = getimagesize( get_theme_file_path( 'assets/img/' . sanitize_file_name( $row['image'] ) ) );
		printf(
			'<img src="%s" alt="%s"%s loading="lazy" decoding="async">',
			esc_url( nu_research_img( $row['image'] ) ),
			$decorative ? '' : esc_attr( $row['image_alt'] ?? '' ),
			$size ? sprintf( ' width="%d" height="%d"', $size[0], $size[1] ) : ''
		);
	}
	echo '</div>';
};
?>
<section class="photo-marquee" data-marquee <?php echo $nu_label ? 'aria-label="' . esc_attr( $nu_label ) . '"' : ''; ?>>
	<div class="photo-marquee-track" data-marquee-track>
		<?php
		$nu_marquee_group( $nu_images, false );
		$nu_marquee_group( $nu_images, true );
		?>
	</div>
</section>
