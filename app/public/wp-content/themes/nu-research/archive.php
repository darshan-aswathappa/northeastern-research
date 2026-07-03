<?php
/**
 * Archive template: category, tag, author, and date archives.
 *
 * @package nu-research
 */

get_header();

// Eyebrow reflects the kind of archive being viewed.
if ( is_category() ) {
	$nu_eyebrow = __( 'Category', 'nu-research' );
} elseif ( is_tag() ) {
	$nu_eyebrow = __( 'Tag', 'nu-research' );
} elseif ( is_author() ) {
	$nu_eyebrow = __( 'Author', 'nu-research' );
} else {
	$nu_eyebrow = __( 'Archive', 'nu-research' );
}

$nu_title = wp_strip_all_tags( get_the_archive_title() );
$nu_intro = wp_strip_all_tags( get_the_archive_description() );
?>

<div class="wrap page-pad">
	<?php
	nu_research_section_header( $nu_eyebrow, $nu_title, $nu_intro, 'h1' );

	nu_research_category_filter_bar();
	?>

	<?php if ( have_posts() ) : ?>
		<ul class="card-grid card-grid-highlights">
			<?php
			while ( have_posts() ) :
				the_post();
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
				'screen_reader_text' => __( 'Archive navigation', 'nu-research' ),
			)
		);
		?>
	<?php else : ?>
		<p class="empty-state"><?php esc_html_e( 'Nothing found in this archive yet.', 'nu-research' ); ?></p>
	<?php endif; ?>
</div>

<?php get_footer(); ?>
