<?php
/**
 * Skeleton for the Press & Publications template (page-press.php):
 * section header, numbered publications, a dark open-source band, press rows.
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
		<span class="sk sk-h2 sk-w-40"></span>
		<ol class="pub-list">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<li class="pub-item">
					<span class="sk sk-pub-number"></span>
					<div class="pub-body">
						<span class="sk sk-h2 sk-w-75"></span>
						<span class="sk sk-line sk-w-55"></span>
					</div>
				</li>
			<?php endfor; ?>
		</ol>
	</section>
</div>

<section class="section repo-band">
	<div class="wrap">
		<div class="section-header">
			<span class="sk sk-eyebrow"></span>
			<span class="sk sk-h2 sk-w-55"></span>
			<span class="sk sk-line sk-w-90"></span>
			<span class="sk sk-line sk-w-75"></span>
		</div>
		<ul class="repo-grid">
			<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<li class="repo-card">
					<span class="sk sk-h2 sk-w-70"></span>
					<span class="sk sk-line sk-w-100"></span>
					<span class="sk sk-line sk-w-85"></span>
					<span class="sk sk-tag"></span>
				</li>
			<?php endfor; ?>
		</ul>
	</div>
</section>

<div class="wrap">
	<section class="section-block section-block-after-band">
		<span class="sk sk-h2 sk-w-40"></span>
		<ul class="press-rows">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<li class="press-row">
					<div class="press-row-body">
						<span class="sk sk-line-sm sk-w-25"></span>
						<span class="sk sk-h2 sk-w-85"></span>
					</div>
					<span class="sk sk-line sk-w-100"></span>
				</li>
			<?php endfor; ?>
		</ul>
	</section>
</div>
