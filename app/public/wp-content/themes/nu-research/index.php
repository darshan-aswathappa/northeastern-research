<?php
/**
 * Last-resort fallback template.
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">
	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			?>
			<article <?php post_class(); ?>>
				<h2><a href="<?php the_permalink(); ?>"><?php echo esc_html( get_the_title() ); ?></a></h2>
				<div class="entry-content"><?php the_excerpt(); ?></div>
			</article>
		<?php endwhile; ?>
	<?php else : ?>
		<p><?php esc_html_e( 'Nothing found.', 'nu-research' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
