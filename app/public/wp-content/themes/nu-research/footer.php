<?php
/**
 * Black global footer per the design system.
 *
 * @package nu-research
 */
?>
</main>

<footer class="site-footer">
	<div class="wrap footer-grid">
		<div class="footer-brand">
			<p class="footer-lockup"><?php esc_html_e( 'Northeastern University', 'nu-research' ); ?></p>
			<p class="footer-unit"><?php esc_html_e( 'WordPress Research Fellows Program', 'nu-research' ); ?></p>
		</div>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Program links', 'nu-research' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Program', 'nu-research' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'about-the-program' ) ); ?>"><?php esc_html_e( 'About', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'highlights-team' ) ); ?>"><?php esc_html_e( 'Highlights & Team', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'blog' ) ); ?>"><?php esc_html_e( 'Blog', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'apply-eligibility' ) ); ?>"><?php esc_html_e( 'Apply', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'apply-eligibility' ) ); ?>#faq"><?php esc_html_e( 'FAQ', 'nu-research' ); ?></a></li>
			</ul>
		</nav>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Department links', 'nu-research' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Department', 'nu-research' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Software Engineering Home', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Faculty Directory', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Course Catalog', 'nu-research' ); ?></a></li>
			</ul>
		</nav>

		<nav class="footer-col" aria-label="<?php esc_attr_e( 'Support links', 'nu-research' ); ?>">
			<h2 class="footer-title"><?php esc_html_e( 'Support', 'nu-research' ); ?></h2>
			<ul>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Give Now', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'contact' ) ); ?>"><?php esc_html_e( 'Contact Us', 'nu-research' ); ?></a></li>
			</ul>
		</nav>
	</div>

	<div class="wrap footer-bottom">
		<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Northeastern University · Demo site for the WordPress Research Fellows Program', 'nu-research' ); ?></p>
	</div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
