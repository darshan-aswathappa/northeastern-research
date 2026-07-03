<?php
/**
 * Template Name: About the Program
 * Template Post Type: page
 *
 * @package nu-research
 */

get_header();
?>

<div class="wrap page-pad">

	<section class="section-block">
		<?php
		nu_research_section_header(
			__( 'About', 'nu-research' ),
			__( 'What fellows actually do', 'nu-research' ),
			__( 'Over ten weeks, fellows move from onboarding to shipped code — working in small teams on real problems the department’s own WordPress infrastructure faces.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block">
		<div class="media-card">
			<div class="media-card-image">
				<img src="<?php echo esc_url( nu_research_img( 'collab.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Two students pair programming', 'nu-research' ); ?>" width="1000" height="750" loading="lazy">
			</div>
			<div class="media-card-body">
				<h2><?php esc_html_e( 'Track-based teams', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( 'Every fellow joins one of four tracks — Plugin Architecture, Editor & Block UX, Performance & Security, or Accessibility — in a group of 3–4 students led by a faculty mentor and a graduate TA.', 'nu-research' ); ?></p>
			</div>
		</div>
	</section>

	<section class="section-block">
		<h2><?php esc_html_e( 'Program structure', 'nu-research' ); ?></h2>
		<ol class="numbered-list">
			<li><?php esc_html_e( 'Week 1: Paid onboarding — local environment setup, WordPress internals, and team formation', 'nu-research' ); ?></li>
			<li><?php esc_html_e( 'Weeks 2–4: Problem scoping with your faculty mentor and first working prototype', 'nu-research' ); ?></li>
			<li><?php esc_html_e( 'Weeks 5–8: Core build sprint with weekly code review and a Friday all-hands demo', 'nu-research' ); ?></li>
			<li><?php esc_html_e( 'Week 9: Polish, testing, and documentation', 'nu-research' ); ?></li>
			<li><?php esc_html_e( 'Week 10: End-of-summer showcase presenting to faculty and department staff', 'nu-research' ); ?></li>
		</ol>
	</section>

</div>

<section class="section section-muted">
	<div class="wrap">
		<div class="media-card media-card-reverse">
			<div class="media-card-image">
				<img src="<?php echo esc_url( nu_research_img( 'mentor2.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Faculty mentor reviewing code with a student', 'nu-research' ); ?>" width="1000" height="750" loading="lazy">
			</div>
			<div class="media-card-body">
				<h2><?php esc_html_e( 'Faculty mentorship', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( 'Weekly 1:1s, code review, and a Friday all-hands demo keep fellows accountable and connected — mentors also help fellows scope a capstone-ready portfolio piece from their summer work.', 'nu-research' ); ?></p>
			</div>
		</div>
	</div>
</section>

<?php get_footer(); ?>
