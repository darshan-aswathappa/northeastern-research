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
				<li><a href="<?php echo esc_url( nu_research_page_url( 'events' ) ); ?>"><?php esc_html_e( 'Events & Info Sessions', 'nu-research' ); ?></a></li>
				<li><a href="<?php echo esc_url( nu_research_page_url( 'press' ) ); ?>"><?php esc_html_e( 'Press & Publications', 'nu-research' ); ?></a></li>
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

<div class="site-footer-inst">
	<div class="wrap footer-inst-inner">

		<div class="footer-inst-brand">
			<img
				class="footer-inst-logo"
				src="<?php echo esc_url( get_theme_file_uri( 'assets/img/nu-mark.png' ) ); ?>"
				alt="<?php esc_attr_e( 'Northeastern University', 'nu-research' ); ?>"
				width="120"
				height="auto"
				loading="lazy"
			/>
			<p><strong><?php esc_html_e( 'Northeastern University', 'nu-research' ); ?></strong> <?php esc_html_e( 'WordPress Research Fellows Program', 'nu-research' ); ?></p>
			<p><?php esc_html_e( '360 Huntington Ave, Boston, MA 02115', 'nu-research' ); ?></p>
			<p>617-373-2000 &bull; <a href="mailto:research@northeastern.edu">research@northeastern.edu</a></p>
		</div>

		<div class="footer-inst-legal">
			<p>&copy; <?php echo esc_html( gmdate( 'Y' ) ); ?> <?php esc_html_e( 'Northeastern University', 'nu-research' ); ?></p>
			<a href="#"><?php esc_html_e( 'DMCA', 'nu-research' ); ?></a>
			<a href="#"><?php esc_html_e( 'Privacy Statement', 'nu-research' ); ?></a>
		</div>

		<div class="footer-inst-social">
			<a href="https://twitter.com/Northeastern" aria-label="<?php esc_attr_e( 'Twitter', 'nu-research' ); ?>" target="_blank" rel="noopener noreferrer">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
					<path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.253 5.622zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
				</svg>
			</a>
			<a href="https://www.youtube.com/NortheasternU" aria-label="<?php esc_attr_e( 'YouTube', 'nu-research' ); ?>" target="_blank" rel="noopener noreferrer">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
					<path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
				</svg>
			</a>
			<a href="https://www.linkedin.com/school/northeastern-university/" aria-label="<?php esc_attr_e( 'LinkedIn', 'nu-research' ); ?>" target="_blank" rel="noopener noreferrer">
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="currentColor" aria-hidden="true">
					<path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
				</svg>
			</a>
		</div>

	</div>
</div>

<?php wp_footer(); ?>
</body>
</html>
