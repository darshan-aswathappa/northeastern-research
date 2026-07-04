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

	<?php nu_research_breadcrumb(); ?>

	<section class="section-block" data-aos="fade-up">
		<?php
		nu_research_section_header(
			__( 'Highlights & Team', 'nu-research' ),
			__( 'Meet the 2026 fellows', 'nu-research' ),
			__( 'List of Team members and members Research Highlights listed down in the page', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block" aria-labelledby="team-heading">
		<div class="acf-note">
			<span class="badge badge-red"><?php esc_html_e( 'Team Members', 'nu-research' ); ?></span>
			<span class="acf-note-detail"><?php esc_html_e( '', 'nu-research' ); ?></span>
		</div>
		<h2 id="team-heading" class="screen-reader-text"><?php esc_html_e( 'Fellows', 'nu-research' ); ?></h2>

		<?php if ( function_exists( 'have_rows' ) && have_rows( 'team_members' ) ) : ?>
			<ul class="card-grid card-grid-team">
				<?php
				$nu_card_index = 0;
				while ( have_rows( 'team_members' ) ) :
					the_row();
					// Raw attachment ID (ACF stores the ID regardless of return format),
					// so wp_get_attachment_image can emit srcset for right-sized delivery.
					$nu_photo_id = (int) get_sub_field( 'photo', false );
					$nu_name     = get_sub_field( 'name' );
					$nu_major    = get_sub_field( 'major' );
					$nu_mentor   = get_sub_field( 'mentor' );
					$nu_focus    = get_sub_field( 'focus' );
					$nu_delay    = ( $nu_card_index % 3 ) * 100;
					$nu_card_index++;
					?>
					<li class="card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
						<div class="card-media ratio-4-3">
							<?php
							if ( $nu_photo_id ) {
								echo wp_get_attachment_image(
									$nu_photo_id,
									'medium_large',
									false,
									array(
										'alt'     => $nu_name,
										'loading' => 'lazy',
										'sizes'   => '(max-width: 599px) 100vw, (max-width: 900px) 50vw, 300px',
									)
								);
							}
							?>
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
			<span class="badge badge-red"><?php esc_html_e( 'Research Highlights', 'nu-research' ); ?></span>
			<span class="acf-note-detail"><?php esc_html_e( '', 'nu-research' ); ?></span>
		</div>
		<h2 id="highlights-heading" class="screen-reader-text"><?php esc_html_e( 'Research highlights', 'nu-research' ); ?></h2>

		<?php if ( function_exists( 'have_rows' ) && have_rows( 'research_highlights' ) ) : ?>
			<ul class="card-grid card-grid-highlights">
				<?php
				$nu_highlight_index = 0;
				while ( have_rows( 'research_highlights' ) ) :
					the_row();
					$nu_image_id = (int) get_sub_field( 'image', false );
					$nu_title    = get_sub_field( 'title' );
					$nu_track    = get_sub_field( 'track' );
					$nu_summary  = get_sub_field( 'summary' );
					$nu_students = get_sub_field( 'students' );
					$nu_h_delay  = ( $nu_highlight_index % 3 ) * 100;
					$nu_highlight_index++;
					?>
					<li class="card" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_h_delay ); ?>">
						<div class="card-media ratio-16-10">
							<?php
							if ( $nu_image_id ) {
								echo wp_get_attachment_image(
									$nu_image_id,
									'medium_large',
									false,
									array(
										'alt'     => $nu_title,
										'loading' => 'lazy',
										'sizes'   => '(max-width: 599px) 100vw, (max-width: 900px) 50vw, 380px',
									)
								);
							}
							?>
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
