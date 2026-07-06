<?php
/**
 * Skeleton for the Contact template (page-contact.php):
 * section header, two-column contact details + feature banner, resource list.
 *
 * @package nu-research
 */

?>
<div class="wrap page-pad">
	<span class="sk sk-breadcrumb"></span>

	<section class="section-block">
		<?php nu_skeleton_section_header(); ?>
	</section>

	<section class="section-block two-col two-col-contact">
		<div class="contact-details">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<div class="contact-item">
					<span class="sk sk-line sk-w-40"></span>
					<span class="sk sk-line sk-w-75"></span>
				</div>
			<?php endfor; ?>
		</div>
		<div class="feature-banner">
			<span class="sk sk-h2 sk-w-60"></span>
			<span class="sk sk-line sk-w-85"></span>
			<span class="sk sk-line sk-w-85"></span>
			<span class="sk sk-line sk-w-70"></span>
		</div>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-40"></span>
		<ul class="resource-list">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<li>
					<span class="sk sk-line sk-w-55"></span>
					<span class="sk sk-line sk-w-40"></span>
				</li>
			<?php endfor; ?>
		</ul>
	</section>
</div>
