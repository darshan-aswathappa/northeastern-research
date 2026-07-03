<?php
/**
 * Generic page fallback.
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">
	<?php
	while ( have_posts() ) :
		the_post();
		?>
		<article <?php post_class(); ?>>
			<h1><?php echo esc_html( get_the_title() ); ?></h1>
			<div class="entry-content">
				<?php the_content(); ?>
			</div>
		</article>
	<?php endwhile; ?>
</div>

<?php get_footer(); ?>
