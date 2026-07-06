<?php
/**
 * Skeleton for the Highlights & Team template (page-highlights-team.php):
 * section header, a team card grid (4:3), a research-highlights card grid.
 *
 * @package nu-research
 */

?>
<div class="wrap page-pad">
	<span class="sk sk-breadcrumb"></span>

	<section class="section-block">
		<?php nu_skeleton_section_header(); ?>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-30"></span>
		<ul class="card-grid card-grid-team">
			<?php for ( $i = 0; $i < 6; $i++ ) : ?>
				<li class="card">
					<div class="card-media ratio-4-3 sk"></div>
					<div class="card-body">
						<span class="sk sk-h2 sk-w-70"></span>
						<span class="sk sk-line sk-w-90"></span>
						<span class="sk sk-line sk-w-60"></span>
					</div>
				</li>
			<?php endfor; ?>
		</ul>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-30"></span>
		<ul class="card-grid card-grid-highlights">
			<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<li class="card">
					<div class="card-media ratio-16-10 sk"></div>
					<div class="card-body">
						<span class="sk sk-line-sm sk-w-30"></span>
						<span class="sk sk-h2 sk-w-85"></span>
						<span class="sk sk-line sk-w-100"></span>
						<span class="sk sk-line sk-w-75"></span>
					</div>
				</li>
			<?php endfor; ?>
		</ul>
	</section>
</div>
