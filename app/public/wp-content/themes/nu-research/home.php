<?php
/**
 * Blog listing (the Posts page set in Settings → Reading).
 *
 * @package nu-research
 */

get_header();

$nu_blog_id    = (int) get_option( 'page_for_posts' );
$nu_blog_title = $nu_blog_id ? get_the_title( $nu_blog_id ) : __( 'Blog', 'nu-research' );
?>

<div class="wrap page-pad">
	<?php nu_research_breadcrumb( $nu_blog_title ); ?>
	<div data-aos="fade-up">
	<?php
	nu_research_section_header(
		__( 'News & Updates', 'nu-research' ),
		$nu_blog_title,
		__( 'Announcements, project write-ups, and research notes from the WordPress Research Fellows Program.', 'nu-research' ),
		'h1'
	);

	nu_research_category_filter_bar();
	?>
	</div>

	<?php if ( have_posts() ) : ?>
		<ul class="card-grid card-grid-highlights">
			<?php
			$nu_post_index = 0;
			while ( have_posts() ) :
				the_post();
				// Pass the stagger delay via a global so post-card.php can use it.
				$GLOBALS['nu_aos_delay'] = ( $nu_post_index % 3 ) * 100;
				$nu_post_index++;
				get_template_part( 'template-parts/post-card' );
			endwhile;
			?>
		</ul>

		<?php
		the_posts_pagination(
			array(
				'mid_size'           => 1,
				'prev_text'          => __( '&larr; Newer', 'nu-research' ),
				'next_text'          => __( 'Older &rarr;', 'nu-research' ),
				'screen_reader_text' => __( 'Blog posts navigation', 'nu-research' ),
			)
		);
		?>
	<?php else : ?>
		<p class="empty-state"><?php esc_html_e( 'No posts published yet. Check back soon.', 'nu-research' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
