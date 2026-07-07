<?php
/**
 * Skeleton for the Apply & Eligibility template (page-apply.php):
 * section header, two-column criteria/steps, deadlines, FAQ, application band.
 *
 * @package nu-research
 */

?>
<div class="wrap page-pad">
	<span class="sk sk-breadcrumb"></span>

	<section class="section-block">
		<?php nu_skeleton_section_header(); ?>
	</section>

	<section class="section-block two-col">
		<div>
			<span class="sk sk-h2 sk-w-55"></span>
			<?php for ( $i = 0; $i < 5; $i++ ) : ?>
				<span class="sk sk-line sk-w-90"></span>
			<?php endfor; ?>
		</div>
		<div>
			<span class="sk sk-h2 sk-w-55"></span>
			<?php for ( $i = 0; $i < 6; $i++ ) : ?>
				<span class="sk sk-line sk-w-85"></span>
			<?php endfor; ?>
		</div>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-40"></span>
		<?php for ( $i = 0; $i < 4; $i++ ) : ?>
			<span class="sk sk-line sk-w-100"></span>
		<?php endfor; ?>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-50"></span>
		<?php for ( $i = 0; $i < 5; $i++ ) : ?>
			<span class="sk sk-input"></span>
		<?php endfor; ?>
	</section>
</div>

<section class="section section-muted">
	<div class="wrap form-wrap">
		<span class="sk sk-h2 sk-w-45"></span>
		<?php for ( $i = 0; $i < 5; $i++ ) : ?>
			<span class="sk sk-input"></span>
		<?php endfor; ?>
		<span class="sk sk-btn"></span>
	</div>
</section>
