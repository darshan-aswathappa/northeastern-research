<?php
/**
 * Fellows: Logo Marquee — a "trusted by" line over a strip of partner logos
 * that drifts sideways, follows the page scroll direction (assets/js/marquee.js),
 * and fades under a blur at both ends. The logos are real text plus an abstract
 * mark, so they stay crisp and readable. The track holds two identical groups
 * so the loop wraps seamlessly; the second group is decorative and hidden from
 * assistive tech.
 *
 * @package nu-research
 */

$nu_heading = get_field( 'heading' );
$nu_logos   = get_field( 'logos' );

if ( empty( $nu_logos ) ) {
	return;
}

/**
 * One copy of the logo group.
 *
 * @param array $logos      Repeater rows ( mark, name ).
 * @param bool  $decorative Whether this copy is the aria-hidden duplicate.
 */
$nu_logo_group = static function ( $logos, $decorative ) {
	printf( '<div class="logo-marquee-group" data-marquee-group%s>', $decorative ? ' aria-hidden="true"' : '' );
	foreach ( $logos as $row ) {
		if ( empty( $row['name'] ) ) {
			continue;
		}
		echo '<span class="logo-item">';
		nu_research_logo_mark( $row['mark'] ?? 'hexagon' );
		printf( '<span class="logo-name">%s</span>', esc_html( $row['name'] ) );
		echo '</span>';
	}
	echo '</div>';
};
?>
<section class="logo-marquee" data-marquee <?php echo $nu_heading ? 'aria-label="' . esc_attr( $nu_heading ) . '"' : ''; ?>>
	<?php if ( $nu_heading ) : ?>
		<p class="logo-marquee-heading"><?php echo esc_html( $nu_heading ); ?></p>
	<?php endif; ?>
	<div class="logo-marquee-track" data-marquee-track>
		<?php
		$nu_logo_group( $nu_logos, false );
		$nu_logo_group( $nu_logos, true );
		?>
	</div>
</section>
