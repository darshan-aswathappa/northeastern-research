<?php
/**
 * Template Name: About the Program
 * Template Post Type: page
 *
 * Editorial layout mirroring the Northeastern Graduate "Academic Programs"
 * page: a full-bleed hero photo, a breadcrumb, a large serif page title,
 * then text-and-links sections — no further images.
 *
 * @package nu-research
 */

get_header();

$apply_url   = nu_research_page_url( 'apply' );
$contact_url = nu_research_page_url( 'contact' );

/**
 * Research tracks — each renders as a linked serif heading, a description,
 * and an inline "Learn more." link, matching the reference program list.
 */
$tracks = array(
	array(
		'title' => __( 'Plugin Architecture', 'nu-research' ),
		'body'  => __( 'Design and ship extensible plugins for the department’s WordPress infrastructure — custom post types, settings APIs, and REST endpoints built to production standards.', 'nu-research' ),
	),
	array(
		'title' => __( 'Editor & Block UX', 'nu-research' ),
		'body'  => __( 'Build Gutenberg blocks and editor experiences that content teams actually enjoy using, pairing React and the block APIs with real editorial workflows.', 'nu-research' ),
	),
	array(
		'title' => __( 'Performance & Security', 'nu-research' ),
		'body'  => __( 'Profile, harden, and speed up live sites — from query optimization and caching to input validation, capability checks, and dependency audits.', 'nu-research' ),
	),
	array(
		'title' => __( 'Accessibility', 'nu-research' ),
		'body'  => __( 'Make interfaces work for everyone, testing against WCAG with assistive technology and folding accessibility into the build rather than bolting it on.', 'nu-research' ),
	),
);

/**
 * The ten-week arc — headings with supporting copy, no links.
 */
$structure = array(
	array(
		'title' => __( 'Week 1 — Paid onboarding', 'nu-research' ),
		'body'  => __( 'Local environment setup, a tour of WordPress internals, and team formation across the four tracks.', 'nu-research' ),
	),
	array(
		'title' => __( 'Weeks 2–4 — Scoping', 'nu-research' ),
		'body'  => __( 'Problem scoping with your faculty mentor, ending in a first working prototype you can demo.', 'nu-research' ),
	),
	array(
		'title' => __( 'Weeks 5–8 — Core build', 'nu-research' ),
		'body'  => __( 'The main build sprint, with weekly code review and a Friday all-hands demo to keep momentum honest.', 'nu-research' ),
	),
	array(
		'title' => __( 'Week 9 — Polish', 'nu-research' ),
		'body'  => __( 'Testing, documentation, and the final round of review before the work goes in front of the department.', 'nu-research' ),
	),
	array(
		'title' => __( 'Week 10 — Showcase', 'nu-research' ),
		'body'  => __( 'The end-of-summer showcase, presenting shipped work to faculty and department staff.', 'nu-research' ),
	),
);
?>

<section class="page-hero-media" aria-hidden="true">
	<img src="<?php echo esc_url( nu_research_img( 'collab.jpg' ) ); ?>" alt="" width="2000" height="900" fetchpriority="high" decoding="async">
</section>

<div class="wrap page-pad">

	<?php nu_research_breadcrumb( __( 'About', 'nu-research' ) ); ?>

	<header class="page-head">
		<h1 class="page-title"><?php esc_html_e( 'About the program', 'nu-research' ); ?></h1>
		<p class="page-intro"><?php esc_html_e( 'Over ten weeks, fellows move from onboarding to shipped code — working in small teams on real problems the department’s own WordPress infrastructure faces.', 'nu-research' ); ?></p>
	</header>

	<section class="prog-section">
		<h2><?php esc_html_e( 'Research tracks', 'nu-research' ); ?></h2>
		<div class="prog-list">
			<?php foreach ( $tracks as $track ) : ?>
				<article class="prog-item">
					<h3><a href="<?php echo esc_url( $apply_url ); ?>"><?php echo esc_html( $track['title'] ); ?></a></h3>
					<p>
						<?php echo esc_html( $track['body'] ); ?>
						<a class="learn-more" href="<?php echo esc_url( $apply_url ); ?>"><?php esc_html_e( 'Learn more.', 'nu-research' ); ?></a>
					</p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="prog-section">
		<h2><?php esc_html_e( 'How the summer runs', 'nu-research' ); ?></h2>
		<div class="prog-list">
			<?php foreach ( $structure as $phase ) : ?>
				<article class="prog-item">
					<h3><?php echo esc_html( $phase['title'] ); ?></h3>
					<p><?php echo esc_html( $phase['body'] ); ?></p>
				</article>
			<?php endforeach; ?>
		</div>
	</section>

	<section class="prog-section">
		<h2><?php esc_html_e( 'Faculty mentorship', 'nu-research' ); ?></h2>
		<div class="prog-list">
			<article class="prog-item">
				<h3><a href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Weekly 1:1s and code review', 'nu-research' ); ?></a></h3>
				<p>
					<?php esc_html_e( 'Every fellow joins a group of three to four students led by a faculty mentor and a graduate TA. Weekly one-on-ones, code review, and a Friday all-hands demo keep fellows accountable — mentors also help scope a capstone-ready portfolio piece from the summer’s work.', 'nu-research' ); ?>
					<a class="learn-more" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'Learn more.', 'nu-research' ); ?></a>
				</p>
			</article>
		</div>
	</section>

</div>

<?php get_footer(); ?>
