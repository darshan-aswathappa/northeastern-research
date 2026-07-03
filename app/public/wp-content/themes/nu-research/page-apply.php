<?php
/**
 * Template Name: Apply & Eligibility
 * Template Post Type: page
 *
 * Eligibility criteria, application steps, ACF-driven deadlines, FAQ,
 * and the [swe_fellows_application] form via page content.
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">

	<section class="section-block">
		<?php
		nu_research_section_header(
			__( 'Apply & Eligibility', 'nu-research' ),
			__( 'How to apply', 'nu-research' ),
			__( 'Applications for Summer 2027 open December 1, 2026. Review the criteria and deadlines below, then use the application form at the bottom of this page.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block two-col">
		<div>
			<h2><?php esc_html_e( 'Eligibility criteria', 'nu-research' ); ?></h2>
			<ul class="check-list">
				<li><?php esc_html_e( 'Currently enrolled undergraduate in Computer Science, Software Engineering, or a related field', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Minimum 3.0 cumulative GPA', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'At least one completed course in web development, databases, or systems programming', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Available full-time for all 10 weeks — no concurrent internship or coursework', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Open to U.S. citizens, permanent residents, and international students with valid visa status', 'nu-research' ); ?></li>
			</ul>
		</div>
		<div>
			<h2><?php esc_html_e( 'Application steps', 'nu-research' ); ?></h2>
			<ol class="numbered-list">
				<li><?php esc_html_e( 'Review eligibility criteria and the four research tracks', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Complete the online application below', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Submit an unofficial transcript and one faculty reference', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Upload a short statement of interest (500 words max)', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Interview with a track mentor (selected applicants only)', 'nu-research' ); ?></li>
				<li><?php esc_html_e( 'Receive a decision by the notification date', 'nu-research' ); ?></li>
			</ol>
		</div>
	</section>

	<section class="section-block">
		<h2><?php esc_html_e( 'Key deadlines', 'nu-research' ); ?></h2>
		<?php if ( function_exists( 'have_rows' ) && have_rows( 'deadlines' ) ) : ?>
			<table class="deadlines-table">
				<caption class="screen-reader-text"><?php esc_html_e( 'Application deadlines for the upcoming program year', 'nu-research' ); ?></caption>
				<thead class="screen-reader-text">
					<tr>
						<th scope="col"><?php esc_html_e( 'Milestone', 'nu-research' ); ?></th>
						<th scope="col"><?php esc_html_e( 'Date', 'nu-research' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					while ( have_rows( 'deadlines' ) ) :
						the_row();
						?>
						<tr>
							<th scope="row"><?php echo esc_html( get_sub_field( 'label' ) ); ?></th>
							<td><?php echo esc_html( get_sub_field( 'date' ) ); ?></td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'Deadlines for the next cycle haven’t been announced yet.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

	<section class="section-block" id="faq">
		<h2><?php esc_html_e( 'Frequently asked questions', 'nu-research' ); ?></h2>
		<div class="faq-list">
			<details open>
				<summary><?php esc_html_e( 'Do I need prior WordPress experience?', 'nu-research' ); ?></summary>
				<p><?php esc_html_e( 'No. Solid fundamentals in PHP or JavaScript (from any coursework) are enough — the first week is a paid onboarding sprint covering WordPress-specific concepts.', 'nu-research' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'Is the fellowship paid?', 'nu-research' ); ?></summary>
				<p><?php esc_html_e( 'Yes. Fellows receive a flat stipend for the 10-week program, paid biweekly, plus a travel allowance for the end-of-summer showcase.', 'nu-research' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'Can I apply if I have a concurrent internship?', 'nu-research' ); ?></summary>
				<p><?php esc_html_e( 'The program requires full-time availability for all 10 weeks, so a concurrent internship or heavy course load isn’t compatible.', 'nu-research' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'Do international students qualify?', 'nu-research' ); ?></summary>
				<p><?php esc_html_e( 'Yes — U.S. citizens, permanent residents, and international students with valid visa status are all eligible to apply.', 'nu-research' ); ?></p>
			</details>
			<details>
				<summary><?php esc_html_e( 'How are tracks assigned?', 'nu-research' ); ?></summary>
				<p><?php esc_html_e( 'You’ll rank your top two track preferences on the application; final placement balances student interest with mentor capacity.', 'nu-research' ); ?></p>
			</details>
		</div>
	</section>

</div>

<section class="section section-muted" aria-labelledby="application-heading">
	<div class="wrap form-wrap">
		<!-- <div class="acf-note">
			<span class="badge badge-red"><?php esc_html_e( 'Custom Plugin', 'nu-research' ); ?></span>
			<span class="acf-note-detail"><?php esc_html_e( 'swe-fellows-application (multi-step form)', 'nu-research' ); ?></span>
		</div> -->
		<h2 id="application-heading"><?php esc_html_e( 'Start your application', 'nu-research' ); ?></h2>
		<?php
		// Page content carries the [swe_fellows_application] shortcode so
		// editors can move or replace the form without a template edit.
		while ( have_posts() ) :
			the_post();
			the_content();
		endwhile;
		?>
	</div>
</section>

<?php get_footer(); ?>
