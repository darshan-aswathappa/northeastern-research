<?php
/**
 * Fellows: Journey Cards — a labelled three-up grid of photo cards, each with
 * a red arrow text link.
 *
 * @package nu-research
 */

$nu_label = get_field( 'label' );
$nu_cards = get_field( 'cards' );
$nu_cards = is_array( $nu_cards ) ? $nu_cards : array();
?>
<section class="section journey-section">
	<div class="wrap">
		<div class="section-header">
			<h2 class="journey-heading"><?php echo esc_html( $nu_label ); ?></h2>
		</div>
		<?php if ( $nu_cards ) : ?>
			<ul class="journey-grid">
				<?php foreach ( $nu_cards as $i => $card ) : ?>
					<li class="journey-card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $i * 100 ); ?>">
						<div class="journey-card-media ratio-4-3">
							<img src="<?php echo esc_url( nu_research_img( $card['image'] ) ); ?>" alt="<?php echo esc_attr( $card['image_alt'] ); ?>" width="1000" height="750" loading="lazy">
						</div>
						<h3 class="journey-card-title"><?php echo esc_html( $card['title'] ); ?></h3>
						<p class="journey-card-body"><?php echo esc_html( $card['body'] ); ?></p>
						<?php if ( $card['cta_label'] ) : ?>
							<a class="arrow-link" href="<?php echo esc_url( nu_research_page_url( $card['cta_slug'] ) ); ?>">
								<?php echo esc_html( $card['cta_label'] ); ?><span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
							</a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>
	</div>
</section>
