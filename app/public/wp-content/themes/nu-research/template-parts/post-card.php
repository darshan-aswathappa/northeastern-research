<?php
/**
 * A single post card, used by the blog listing (home.php) and archives.
 *
 * @package nu-research
 */

$nu_categories = get_the_category();
$nu_primary    = ! empty( $nu_categories ) ? $nu_categories[0] : null;
?>
<?php $nu_delay = isset( $GLOBALS['nu_aos_delay'] ) ? (int) $GLOBALS['nu_aos_delay'] : 0; ?>
<li <?php post_class( 'card' ); ?> data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
	<?php if ( has_post_thumbnail() ) : ?>
		<a class="card-media ratio-4-3" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			the_post_thumbnail(
				'medium_large',
				array(
					'loading' => 'lazy',
					'alt'     => '',
				)
			);
			?>
		</a>
	<?php endif; ?>

	<div class="card-body">
		<?php if ( $nu_primary ) : ?>
			<p class="card-overline">
				<a href="<?php echo esc_url( get_category_link( $nu_primary->term_id ) ); ?>"><?php echo esc_html( $nu_primary->name ); ?></a>
			</p>
		<?php endif; ?>

		<h2 class="card-title">
			<a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a>
		</h2>

		<p class="card-meta">
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
			<span aria-hidden="true"> · </span><?php echo esc_html( get_the_author() ); ?>
		</p>

		<p class="card-text"><?php echo esc_html( get_the_excerpt() ); ?></p>
	</div>
</li>
