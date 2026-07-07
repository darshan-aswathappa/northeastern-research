<?php
/**
 * Skeleton for the About the Program template (page-about.php):
 * full-bleed hero photo, page title + intro, stats rows, tracks list.
 *
 * @package nu-research
 */

?>
<section class="page-hero-media" aria-hidden="true">
	<span class="sk sk-hero-media"></span>
</section>

<div class="wrap page-pad">
	<span class="sk sk-breadcrumb"></span>

	<header class="page-head">
		<span class="sk sk-h1 sk-w-55"></span>
		<span class="sk sk-line sk-w-90"></span>
		<span class="sk sk-line sk-w-75"></span>
	</header>

	<section class="prog-section">
		<span class="sk sk-h2 sk-w-40"></span>
		<div class="stat-rows">
			<?php for ( $i = 0; $i < 3; $i++ ) : ?>
				<article class="stat-row">
					<div class="stat-figure">
						<span class="sk sk-line-sm sk-w-70"></span>
						<span class="sk sk-h1 sk-w-45"></span>
					</div>
					<span class="sk sk-h2 sk-w-60"></span>
					<span class="sk sk-line sk-w-100"></span>
					<span class="sk sk-line sk-w-85"></span>
				</article>
			<?php endfor; ?>
		</div>
	</section>

	<section class="prog-section">
		<span class="sk sk-h2 sk-w-40"></span>
		<div class="prog-list">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<article class="prog-item">
					<span class="sk sk-h2 sk-w-55"></span>
					<span class="sk sk-line sk-w-100"></span>
					<span class="sk sk-line sk-w-90"></span>
				</article>
			<?php endfor; ?>
		</div>
	</section>
</div>
