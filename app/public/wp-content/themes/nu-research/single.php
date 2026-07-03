<?php
/**
 * Single post.
 *
 * @package nu-research
 */

get_header();

$nu_blog_id    = (int) get_option( 'page_for_posts' );
$nu_blog_url   = $nu_blog_id ? get_permalink( $nu_blog_id ) : home_url( '/' );
$nu_blog_title = $nu_blog_id ? get_the_title( $nu_blog_id ) : __( 'Blog', 'nu-research' );
?>

<div class="wrap page-pad">
	<?php
	while ( have_posts() ) :
		the_post();
		nu_research_breadcrumb(
			get_the_title(),
			array(
				array(
					'label' => $nu_blog_title,
					'url'   => $nu_blog_url,
				),
			)
		);
		?>
		<article <?php post_class( 'post-single' ); ?>>
			<header class="post-header">
				<?php
				$nu_categories = get_the_category();
				if ( ! empty( $nu_categories ) ) :
					?>
					<ul class="badge-row post-categories">
						<?php foreach ( $nu_categories as $nu_category ) : ?>
							<li>
								<a class="badge badge-red" href="<?php echo esc_url( get_category_link( $nu_category->term_id ) ); ?>"><?php echo esc_html( $nu_category->name ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<h1 class="post-title"><?php the_title(); ?></h1>

				<p class="post-meta">
					<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo esc_html( get_the_date() ); ?></time>
					<span aria-hidden="true"> · </span><?php echo esc_html( get_the_author() ); ?>
				</p>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<figure class="post-featured-image">
					<?php the_post_thumbnail( 'large', array( 'fetchpriority' => 'high' ) ); ?>
				</figure>
			<?php endif; ?>

			<div class="entry-content">
				<?php the_content(); ?>
			</div>

			<?php
			$nu_tags = get_the_tags();
			if ( ! empty( $nu_tags ) ) :
				?>
				<footer class="post-tags">
					<h2 class="post-tags-title"><?php esc_html_e( 'Tagged', 'nu-research' ); ?></h2>
					<ul class="badge-row">
						<?php foreach ( $nu_tags as $nu_tag ) : ?>
							<li>
								<a class="badge badge-outline" href="<?php echo esc_url( get_tag_link( $nu_tag->term_id ) ); ?>">#<?php echo esc_html( $nu_tag->name ); ?></a>
							</li>
						<?php endforeach; ?>
					</ul>
				</footer>
			<?php endif; ?>
		</article>

		<nav class="post-navigation" aria-label="<?php esc_attr_e( 'Post navigation', 'nu-research' ); ?>">
			<?php
			previous_post_link( '<div class="post-nav-prev">%link</div>', '&larr; %title' );
			next_post_link( '<div class="post-nav-next">%link</div>', '%title &rarr;' );
			?>
		</nav>

		<p class="post-back">
			<a href="<?php echo esc_url( $nu_blog_url ); ?>"><?php esc_html_e( '&larr; Back to Blog', 'nu-research' ); ?></a>
		</p>
		<?php
	endwhile;
	?>
</div>

<?php get_footer(); ?>
