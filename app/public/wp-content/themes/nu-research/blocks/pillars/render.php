<?php
/**
 * Fellows: Commitment Pillars — section heading + intro over a three-up grid
 * of icon / title / body items.
 *
 * @package nu-research
 */

$nu_heading = get_field( 'heading' );
$nu_intro   = get_field( 'intro' );
$nu_items   = get_field( 'items' );
$nu_items   = is_array( $nu_items ) ? $nu_items : array();
?>
<section class="section pillars-section">
	<div class="wrap">
		<div class="section-header">
			<h2 class="pillars-heading"><?php echo esc_html( $nu_heading ); ?></h2>
			<?php if ( $nu_intro ) : ?>
				<p class="section-intro"><?php echo esc_html( $nu_intro ); ?></p>
			<?php endif; ?>
		</div>
		<?php if ( $nu_items ) : ?>
			<ul class="pillars-grid">
				<?php foreach ( $nu_items as $i => $item ) : ?>
					<li class="pillar" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 100 ); ?>">
						<?php nu_research_pillar_icon( sanitize_key( $item['icon'] ) ); ?>
						<h3 class="pillar-title"><?php echo esc_html( $item['title'] ); ?></h3>
						<p class="pillar-body"><?php echo esc_html( $item['body'] ); ?></p>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
