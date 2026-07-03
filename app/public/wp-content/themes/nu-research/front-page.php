<?php
/**
 * Home: hero, program overview, research tracks, CTA to Apply.
 *
 * @package nu-research
 */

get_header();
$apply_url = nu_research_page_url( 'apply-eligibility' );
?>

<section class="hero" style="background-image:url('<?php echo esc_url( nu_research_img( 'hero.jpg' ) ); ?>');">
	<div class="hero-overlay">
		<div class="wrap">
			<div class="hero-content">
				<p class="eyebrow eyebrow-on-dark"><?php esc_html_e( 'Summer Research Program', 'nu-research' ); ?></p>
				<h1 class="hero-heading"><?php esc_html_e( 'WordPress Research Fellows', 'nu-research' ); ?></h1>
				<p class="hero-lead"><?php esc_html_e( 'A 10-week paid summer fellowship where undergraduate Software Engineering students build, test, and ship real WordPress tooling alongside faculty mentors.', 'nu-research' ); ?></p>
				<?php nu_research_cta( __( 'Apply Now', 'nu-research' ), $apply_url ); ?>
			</div>
		</div>
	</div>
</section>

<section class="section">
	<div class="wrap">
		<?php
		nu_research_section_header(
			__( 'Program Overview', 'nu-research' ),
			__( 'Build the tools you use every day', 'nu-research' ),
			__( 'The WordPress Research Fellows Program pairs Software Engineering undergraduates with faculty mentors to solve real problems in plugin architecture, editor UX, performance, security, and accessibility — the same systems that power the department’s own web infrastructure.', 'nu-research' )
		);
		?>
	</div>
</section>

<section class="section section-tight">
	<div class="wrap">
		<div class="media-card">
			<div class="media-card-image">
				<img src="<?php echo esc_url( nu_research_img( 'mentor.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Fellow working at a laptop with a mentor', 'nu-research' ); ?>" width="1000" height="750" loading="lazy">
			</div>
			<div class="media-card-body">
				<h2><?php esc_html_e( 'Hands-on, mentored research', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( 'Each fellow is paired with a faculty mentor and joins a small track team focused on one problem area — plugin architecture, editor UX, performance, or accessibility. No prior WordPress experience required, just solid PHP or JS fundamentals.', 'nu-research' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section section-tight" aria-label="<?php esc_attr_e( 'Research tracks', 'nu-research' ); ?>">
	<div class="wrap">
		<ul class="badge-row">
			<?php
			$nu_tracks = array(
				__( 'Plugin Architecture', 'nu-research' ),
				__( 'Editor & Block UX', 'nu-research' ),
				__( 'Performance & Security', 'nu-research' ),
				__( 'Accessibility', 'nu-research' ),
				__( 'Developer Tooling', 'nu-research' ),
				__( 'Cloud & Deployment', 'nu-research' ),
			);
			foreach ( $nu_tracks as $nu_track ) :
				?>
				<li class="badge badge-outline"><?php echo esc_html( $nu_track ); ?></li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>

<section class="section section-muted">
	<div class="wrap">
		<div class="media-card media-card-reverse">
			<div class="media-card-image">
				<img src="<?php echo esc_url( nu_research_img( 'fellows.jpg' ) ); ?>" alt="<?php esc_attr_e( 'Fellows presenting a project', 'nu-research' ); ?>" width="1000" height="750" loading="lazy">
			</div>
			<div class="media-card-body">
				<h2><?php esc_html_e( 'Ship something real', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( 'Fellows don’t just study code — every track ships a working plugin, block, or theme feature to a real test site by week 10, presented at the end-of-summer showcase.', 'nu-research' ); ?></p>
			</div>
		</div>
	</div>
</section>

<section class="section section-cta">
	<div class="wrap cta-wrap">
		<h2><?php esc_html_e( 'Ready to spend your summer building?', 'nu-research' ); ?></h2>
		<p class="cta-lead"><?php esc_html_e( 'Applications for Summer 2027 open December 1, 2026.', 'nu-research' ); ?></p>
		<?php nu_research_cta( __( 'See Eligibility & Deadlines', 'nu-research' ), $apply_url ); ?>
	</div>
</section>

<?php get_footer(); ?>
