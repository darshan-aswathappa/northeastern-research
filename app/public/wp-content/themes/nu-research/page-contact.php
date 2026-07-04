<?php
/**
 * Template Name: Contact
 * Template Post Type: page
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
			__( 'Contact', 'nu-research' ),
			__( 'Get in touch', 'nu-research' ),
			__( 'Questions about eligibility, tracks, or the application itself? Reach the program office directly, or explore related resources below.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<section class="section-block two-col two-col-contact">
		<div class="contact-details" data-aos="fade-right">
			<div class="contact-item">
				<h2><?php esc_html_e( 'Program Office', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( '440 Huntington Ave, Boston, MA 02115', 'nu-research' ); ?></p>
			</div>
			<div class="contact-item">
				<h2><?php esc_html_e( 'Email', 'nu-research' ); ?></h2>
				<p><a href="mailto:swe-fellows@northeastern.edu">swe-fellows@northeastern.edu</a></p>
			</div>
			<div class="contact-item">
				<h2><?php esc_html_e( 'Phone', 'nu-research' ); ?></h2>
				<p><a href="tel:+16175550148">(617) 555-0148</a></p>
			</div>
			<div class="contact-item">
				<h2><?php esc_html_e( 'Office hours', 'nu-research' ); ?></h2>
				<p><?php esc_html_e( 'Mon–Thu, 10am–4pm ET, or by appointment', 'nu-research' ); ?></p>
			</div>
		</div>

		<div class="feature-banner" data-aos="fade-left" data-aos-delay="100">
			<h2><?php esc_html_e( 'Connect with the program', 'nu-research' ); ?></h2>
			<ul class="feature-links">
				<li><a href="mailto:swe-fellows@northeastern.edu"><?php esc_html_e( 'Email the Program Office', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'apply-eligibility' ) ); ?>"><?php esc_html_e( 'Book Advising Hours', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Follow Department News', 'nu-research' ); ?></a></li>
			</ul>
		</div>
	</section>

	<section class="section-block">
		<h2 data-aos="fade-up"><?php esc_html_e( 'Related resources', 'nu-research' ); ?></h2>
		<ul class="resource-list">
			<li data-aos="fade-up" data-aos-delay="0">
				<span class="resource-name"><?php esc_html_e( 'Office of Undergraduate Research and Fellowships', 'nu-research' ); ?></span>
				<span class="resource-desc"><?php esc_html_e( 'Campus-wide research funding and advising', 'nu-research' ); ?></span>
			</li>
			<li data-aos="fade-up" data-aos-delay="75">
				<span class="resource-name"><?php esc_html_e( 'WordPress Developer Handbook', 'nu-research' ); ?></span>
				<span class="resource-desc"><?php esc_html_e( 'Official plugin & theme development docs', 'nu-research' ); ?></span>
			</li>
			<li data-aos="fade-up" data-aos-delay="150">
				<span class="resource-name"><?php esc_html_e( 'Department Co-op Advising', 'nu-research' ); ?></span>
				<span class="resource-desc"><?php esc_html_e( 'Pairing fellowship experience with co-op search', 'nu-research' ); ?></span>
			</li>
			<li data-aos="fade-up" data-aos-delay="225">
				<span class="resource-name"><?php esc_html_e( 'Student Accessibility Services', 'nu-research' ); ?></span>
				<span class="resource-desc"><?php esc_html_e( 'Accommodations for program participation', 'nu-research' ); ?></span>
			</li>
		</ul>
	</section>

</div>

<?php get_footer(); ?>
