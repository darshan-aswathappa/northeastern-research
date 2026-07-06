<?php
/**
 * Skeleton for the Events & Info Sessions template (page-events.php):
 * section header, a "Next up" band, upcoming event rows, past events list.
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
		<article class="next-up">
			<div class="next-up-date">
				<span class="sk sk-line-sm sk-w-70"></span>
				<span class="sk sk-h1 sk-w-90"></span>
				<span class="sk sk-line-sm sk-w-100"></span>
			</div>
			<div class="next-up-body">
				<span class="sk sk-line-sm sk-w-30"></span>
				<span class="sk sk-h2 sk-w-75"></span>
				<span class="sk sk-line sk-w-55"></span>
				<span class="sk sk-line sk-w-90"></span>
				<span class="sk sk-line sk-w-85"></span>
				<span class="sk sk-btn"></span>
			</div>
		</article>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-40"></span>
		<ol class="event-rows">
			<?php for ( $i = 0; $i < 4; $i++ ) : ?>
				<li class="event-row">
					<div class="event-date">
						<span class="sk sk-line-sm sk-w-70"></span>
						<span class="sk sk-h1 sk-w-90"></span>
						<span class="sk sk-line-sm sk-w-100"></span>
					</div>
					<div class="event-body">
						<span class="sk sk-line-sm sk-w-30"></span>
						<span class="sk sk-h2 sk-w-75"></span>
						<span class="sk sk-line sk-w-45"></span>
						<span class="sk sk-line sk-w-90"></span>
					</div>
					<div class="event-action">
						<span class="sk sk-line sk-w-100"></span>
					</div>
				</li>
			<?php endfor; ?>
		</ol>
	</section>

	<section class="section-block">
		<span class="sk sk-h2 sk-w-40"></span>
		<ol class="past-events">
			<?php for ( $i = 0; $i < 5; $i++ ) : ?>
				<li class="past-event">
					<span class="sk sk-line sk-w-25"></span>
					<span class="sk sk-line sk-w-55"></span>
					<span class="sk sk-line sk-w-25"></span>
				</li>
			<?php endfor; ?>
		</ol>
	</section>
</div>
