<?php
/**
 * Template Name: Press & Publications
 * Template Post Type: page
 *
 * Research outputs, driven by three SCF repeaters: `publications` (conference
 * papers rendered as a numbered citation list), `repositories` (open-source
 * work in a full-black band), and `press_coverage` (news and department blog
 * posts as editorial headline rows).
 *
 * @package nu-research
 */

get_header();

$nu_has_rows = function_exists( 'have_rows' );
?>

<div class="wrap page-pad">

	<?php nu_research_breadcrumb(); ?>

	<section class="section-block" data-aos="fade-up">
		<?php
		nu_research_section_header(
			__( 'Press & Publications', 'nu-research' ),
			__( 'What the program produces', 'nu-research' ),
			__( 'Conference papers co-authored by fellows, open-source tools that shipped from the summer, and coverage of the program — the public record of ten weeks of research.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block" aria-labelledby="publications-heading">
		<h2 id="publications-heading" data-aos="fade-up"><?php esc_html_e( 'Peer-reviewed publications', 'nu-research' ); ?></h2>

		<?php if ( $nu_has_rows && have_rows( 'publications' ) ) : ?>
			<ol class="pub-list">
				<?php
				$nu_pub_index = 0;
				while ( have_rows( 'publications' ) ) :
					the_row();
					$nu_title   = get_sub_field( 'title' );
					$nu_authors = get_sub_field( 'authors' );
					$nu_venue   = get_sub_field( 'venue' );
					$nu_year    = get_sub_field( 'year' );
					$nu_link    = get_sub_field( 'link' );
					$nu_delay   = min( $nu_pub_index, 4 ) * 75;
					$nu_pub_index++;
					?>
					<li class="pub-item" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
						<span class="pub-number" aria-hidden="true"><?php echo esc_html( str_pad( (string) $nu_pub_index, 2, '0', STR_PAD_LEFT ) ); ?></span>
						<div class="pub-body">
							<h3 class="pub-title">
								<?php if ( $nu_link ) : ?>
									<a href="<?php echo esc_url( $nu_link ); ?>"><?php echo esc_html( $nu_title ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $nu_title ); ?>
								<?php endif; ?>
							</h3>
							<p class="pub-meta">
								<?php if ( $nu_authors ) : ?>
									<span class="pub-authors"><?php echo esc_html( $nu_authors ); ?></span>
								<?php endif; ?>
								<?php if ( $nu_authors && ( $nu_venue || $nu_year ) ) : ?>
									&middot;
								<?php endif; ?>
								<?php if ( $nu_venue ) : ?>
									<cite class="pub-venue"><?php echo esc_html( $nu_venue ); ?></cite>
								<?php endif; ?>
								<?php if ( $nu_venue && $nu_year ) : ?>
									&middot;
								<?php endif; ?>
								<?php if ( $nu_year ) : ?>
									<span class="pub-year"><?php echo esc_html( $nu_year ); ?></span>
								<?php endif; ?>
							</p>
						</div>
					</li>
				<?php endwhile; ?>
			</ol>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'Papers from the current cohort are under review — accepted work is listed here as it publishes.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

</div>

<section class="section repo-band" aria-labelledby="repos-heading">
	<div class="wrap">
		<div class="section-header" data-aos="fade-up">
			<p class="eyebrow eyebrow-on-dark"><?php esc_html_e( 'Open source', 'nu-research' ); ?></p>
			<h2 id="repos-heading" class="section-heading repo-band-heading"><?php esc_html_e( 'Shipped in the open', 'nu-research' ); ?></h2>
			<p class="repo-band-lead"><?php esc_html_e( 'Every fellow ships working code. These repositories started as summer research projects and are maintained in public.', 'nu-research' ); ?></p>
		</div>

		<?php if ( $nu_has_rows && have_rows( 'repositories' ) ) : ?>
			<ul class="repo-grid">
				<?php
				$nu_repo_index = 0;
				while ( have_rows( 'repositories' ) ) :
					the_row();
					$nu_name  = get_sub_field( 'name' );
					$nu_desc  = get_sub_field( 'description' );
					$nu_url   = get_sub_field( 'url' );
					$nu_tag   = get_sub_field( 'tag' );
					$nu_delay = min( $nu_repo_index, 2 ) * 100;
					$nu_repo_index++;
					?>
					<li class="repo-card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
						<h3 class="repo-name">
							<?php if ( $nu_url ) : ?>
								<a href="<?php echo esc_url( $nu_url ); ?>">
									<?php echo esc_html( $nu_name ); ?>
									<span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
								</a>
							<?php else : ?>
								<?php echo esc_html( $nu_name ); ?>
							<?php endif; ?>
						</h3>
						<?php if ( $nu_desc ) : ?>
							<p class="repo-desc"><?php echo esc_html( $nu_desc ); ?></p>
						<?php endif; ?>
						<?php if ( $nu_tag ) : ?>
							<span class="repo-tag"><?php echo esc_html( $nu_tag ); ?></span>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="repo-band-empty"><?php esc_html_e( 'Repositories are published at the end-of-summer showcase.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</div>
</section>

<div class="wrap">

	<section class="section-block section-block-after-band" aria-labelledby="coverage-heading">
		<h2 id="coverage-heading" data-aos="fade-up"><?php esc_html_e( 'In the news', 'nu-research' ); ?></h2>

		<?php if ( $nu_has_rows && have_rows( 'press_coverage' ) ) : ?>
			<ul class="press-rows">
				<?php
				$nu_press_index = 0;
				while ( have_rows( 'press_coverage' ) ) :
					the_row();
					$nu_outlet   = get_sub_field( 'outlet' );
					$nu_headline = get_sub_field( 'headline' );
					$nu_date     = get_sub_field( 'date' ); // Ymd.
					$nu_link     = get_sub_field( 'link' );
					$nu_stamp    = $nu_date ? strtotime( (string) $nu_date ) : false;
					$nu_delay    = min( $nu_press_index, 4 ) * 75;
					$nu_press_index++;
					?>
					<li class="press-row" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
						<div class="press-row-body">
							<?php if ( $nu_outlet ) : ?>
								<p class="card-overline"><?php echo esc_html( $nu_outlet ); ?></p>
							<?php endif; ?>
							<h3 class="press-headline">
								<?php if ( $nu_link ) : ?>
									<a href="<?php echo esc_url( $nu_link ); ?>"><?php echo esc_html( $nu_headline ); ?></a>
								<?php else : ?>
									<?php echo esc_html( $nu_headline ); ?>
								<?php endif; ?>
							</h3>
						</div>
						<?php if ( $nu_stamp ) : ?>
							<time class="press-date" datetime="<?php echo esc_attr( gmdate( 'Y-m-d', $nu_stamp ) ); ?>"><?php echo esc_html( date_i18n( 'F j, Y', $nu_stamp ) ); ?></time>
						<?php endif; ?>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'No coverage yet — press inquiries can reach the program through the contact page.', 'nu-research' ); ?></p>
		<?php endif; ?>

		<p class="press-blog-link" data-aos="fade-up">
			<a class="arrow-link" href="<?php echo esc_url( nu_research_page_url( 'blog' ) ); ?>">
				<?php esc_html_e( 'More from the program blog', 'nu-research' ); ?>
				<span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
			</a>
		</p>
	</section>

</div>

<?php get_footer(); ?>
