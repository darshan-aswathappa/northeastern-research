<?php
/**
 * Template Name: Highlights & Team
 * Template Post Type: page
 *
 * ACF-driven page: team_members and research_highlights repeaters.
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">

	<section class="section-block">
		<?php
		nu_research_section_header(
			__( 'Highlights & Team', 'nu-research' ),
			__( 'Meet the 2026 fellows', 'nu-research' ),
			__( 'Content on this page is managed by non-technical staff each summer via two ACF (Advanced Custom Fields) repeater fields on a single WordPress page — no template edits needed to add a new fellow or project.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block" aria-labelledby="team-heading">
		<div class="acf-note">
			<span class="badge badge-red"><?php esc_html_e( 'ACF Repeater: team_members', 'nu-research' ); ?></span>
			<span class="acf-note-detail"><?php esc_html_e( '5 sub-fields per row · photo, name, major, mentor, focus', 'nu-research' ); ?></span>
		</div>
		<h2 id="team-heading" class="screen-reader-text"><?php esc_html_e( 'Fellows', 'nu-research' ); ?></h2>

		<?php if ( function_exists( 'have_rows' ) && have_rows( 'team_members' ) ) : ?>
			<ul class="card-grid card-grid-team">
				<?php
				while ( have_rows( 'team_members' ) ) :
					the_row();
					$nu_photo  = get_sub_field( 'photo' );
					// Image fields may return an array or ID if reconfigured; normalize to URL.
					if ( is_array( $nu_photo ) ) {
						$nu_photo = $nu_photo['url'] ?? '';
					} elseif ( is_numeric( $nu_photo ) ) {
						$nu_photo = wp_get_attachment_image_url( (int) $nu_photo, 'medium_large' );
					}
					$nu_name   = get_sub_field( 'name' );
					$nu_major  = get_sub_field( 'major' );
					$nu_mentor = get_sub_field( 'mentor' );
					$nu_focus  = get_sub_field( 'focus' );
					?>
					<li class="card">
						<div class="card-media ratio-4-3">
							<?php if ( $nu_photo ) : ?>
								<img src="<?php echo esc_url( $nu_photo ); ?>" alt="<?php echo esc_attr( $nu_name ); ?>" loading="lazy">
							<?php endif; ?>
						</div>
						<div class="card-body">
							<h3 class="card-title"><?php echo esc_html( $nu_name ); ?></h3>
							<?php if ( $nu_major || $nu_mentor ) : ?>
								<p class="card-meta">
									<?php echo esc_html( $nu_major ); ?><?php if ( $nu_major && $nu_mentor ) : ?> &middot; <?php endif; ?><?php if ( $nu_mentor ) : ?><?php echo esc_html( sprintf( /* translators: %s: mentor name */ __( 'Mentor: %s', 'nu-research' ), $nu_mentor ) ); ?><?php endif; ?>
								</p>
							<?php endif; ?>
							<?php if ( $nu_focus ) : ?>
								<p class="card-text"><?php echo esc_html( $nu_focus ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'This summer’s cohort hasn’t been published yet — check back soon.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

	<section class="section-block" aria-labelledby="highlights-heading">
		<div class="acf-note">
			<span class="badge badge-red"><?php esc_html_e( 'ACF Repeater: research_highlights', 'nu-research' ); ?></span>
			<span class="acf-note-detail"><?php esc_html_e( '5 sub-fields per row · image, title, track, summary, students', 'nu-research' ); ?></span>
		</div>
		<h2 id="highlights-heading" class="screen-reader-text"><?php esc_html_e( 'Research highlights', 'nu-research' ); ?></h2>

		<?php if ( function_exists( 'have_rows' ) && have_rows( 'research_highlights' ) ) : ?>
			<ul class="card-grid card-grid-highlights">
				<?php
				while ( have_rows( 'research_highlights' ) ) :
					the_row();
					$nu_image    = get_sub_field( 'image' );
					if ( is_array( $nu_image ) ) {
						$nu_image = $nu_image['url'] ?? '';
					} elseif ( is_numeric( $nu_image ) ) {
						$nu_image = wp_get_attachment_image_url( (int) $nu_image, 'medium_large' );
					}
					$nu_title    = get_sub_field( 'title' );
					$nu_track    = get_sub_field( 'track' );
					$nu_summary  = get_sub_field( 'summary' );
					$nu_students = get_sub_field( 'students' );
					?>
					<li class="card">
						<div class="card-media ratio-16-10">
							<?php if ( $nu_image ) : ?>
								<img src="<?php echo esc_url( $nu_image ); ?>" alt="<?php echo esc_attr( $nu_title ); ?>" loading="lazy">
							<?php endif; ?>
						</div>
						<div class="card-body">
							<?php if ( $nu_track ) : ?>
								<p class="card-overline"><?php echo esc_html( $nu_track ); ?></p>
							<?php endif; ?>
							<h3 class="card-title card-title-lg"><?php echo esc_html( $nu_title ); ?></h3>
							<?php if ( $nu_summary ) : ?>
								<p class="card-text"><?php echo esc_html( $nu_summary ); ?></p>
							<?php endif; ?>
							<?php if ( $nu_students ) : ?>
								<p class="card-meta"><?php echo esc_html( sprintf( /* translators: %s: student names */ __( 'Built by %s', 'nu-research' ), $nu_students ) ); ?></p>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'Research highlights will be posted at the end of the summer showcase.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

</div>

<?php get_footer(); ?>
