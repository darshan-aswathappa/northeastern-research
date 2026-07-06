<?php
/**
 * Template Name: Events & Info Sessions
 * Template Post Type: page
 *
 * A living calendar driven by one SCF `events` repeater. The template sorts
 * rows by date at render time and splits them into three zones: the nearest
 * upcoming event as a full-black "Next up" band, remaining upcoming events as
 * editorial calendar rows, and everything already past as a compact archive.
 * Editors only ever maintain a single flat list — no manual ordering.
 *
 * @package nu-research
 */

get_header();

// Pull every repeater row up front so we can sort and split by date. The
// `date` sub-field returns Ymd (e.g. 20260717), which sorts as a plain string.
$nu_events = ( function_exists( 'get_field' ) && get_field( 'events' ) ) ? get_field( 'events' ) : array();
$nu_today  = current_datetime()->format( 'Ymd' );

usort(
	$nu_events,
	static function ( $a, $b ) {
		return strcmp( (string) $a['date'], (string) $b['date'] );
	}
);

$nu_upcoming = array_values(
	array_filter(
		$nu_events,
		static function ( $row ) use ( $nu_today ) {
			return (string) $row['date'] >= $nu_today;
		}
	)
);
$nu_past     = array_reverse(
	array_values(
		array_filter(
			$nu_events,
			static function ( $row ) use ( $nu_today ) {
				return (string) $row['date'] < $nu_today;
			}
		)
	)
);

$nu_next_up = ! empty( $nu_upcoming ) ? $nu_upcoming[0] : null;
$nu_later   = array_slice( $nu_upcoming, 1 );

/**
 * Format an event's Ymd date into display pieces.
 *
 * @param string $ymd Date in Ymd form.
 * @return array{iso: string, day: string, month: string, weekday: string, full: string}
 */
function nu_research_event_date_parts( $ymd ) {
	$timestamp = strtotime( (string) $ymd );
	return array(
		'iso'     => gmdate( 'Y-m-d', $timestamp ),
		'day'     => date_i18n( 'j', $timestamp ),
		'month'   => date_i18n( 'M', $timestamp ),
		'weekday' => date_i18n( 'l', $timestamp ),
		'full'    => date_i18n( 'F j, Y', $timestamp ),
	);
}
?>

<div class="wrap page-pad">

	<?php nu_research_breadcrumb(); ?>

	<section class="section-block" data-aos="fade-up">
		<?php
		nu_research_section_header(
			__( 'Events & Info Sessions', 'nu-research' ),
			__( 'The program calendar', 'nu-research' ),
			__( 'Recruitment info sessions, orientation, mid-summer demos, and the Week 10 showcase — every date that matters for current fellows and future applicants, in one place.', 'nu-research' ),
			'h1'
		);
		?>
	</section>

	<?php if ( $nu_next_up ) : ?>
		<?php $nu_d = nu_research_event_date_parts( $nu_next_up['date'] ); ?>
		<section class="section-block" aria-labelledby="next-up-heading" data-aos="fade-up">
			<h2 id="next-up-heading" class="screen-reader-text"><?php esc_html_e( 'Next event', 'nu-research' ); ?></h2>
			<article class="next-up">
				<div class="next-up-date" aria-hidden="true">
					<span class="next-up-month"><?php echo esc_html( $nu_d['month'] ); ?></span>
					<span class="next-up-day"><?php echo esc_html( $nu_d['day'] ); ?></span>
					<span class="next-up-weekday"><?php echo esc_html( $nu_d['weekday'] ); ?></span>
				</div>
				<div class="next-up-body">
					<p class="next-up-eyebrow">
						<?php esc_html_e( 'Next up', 'nu-research' ); ?>
						<?php if ( ! empty( $nu_next_up['type'] ) ) : ?>
							<span class="next-up-type"><?php echo esc_html( $nu_next_up['type'] ); ?></span>
						<?php endif; ?>
					</p>
					<h3 class="next-up-title"><?php echo esc_html( $nu_next_up['title'] ); ?></h3>
					<p class="next-up-meta">
						<time datetime="<?php echo esc_attr( $nu_d['iso'] ); ?>"><?php echo esc_html( $nu_d['full'] ); ?></time>
						<?php if ( ! empty( $nu_next_up['time'] ) ) : ?>
							&middot; <?php echo esc_html( $nu_next_up['time'] ); ?>
						<?php endif; ?>
						<?php if ( ! empty( $nu_next_up['location'] ) ) : ?>
							&middot; <?php echo esc_html( $nu_next_up['location'] ); ?>
						<?php endif; ?>
					</p>
					<?php if ( ! empty( $nu_next_up['description'] ) ) : ?>
						<p class="next-up-desc"><?php echo esc_html( $nu_next_up['description'] ); ?></p>
					<?php endif; ?>
					<?php if ( ! empty( $nu_next_up['link'] ) ) : ?>
						<?php nu_research_cta( ! empty( $nu_next_up['link_label'] ) ? $nu_next_up['link_label'] : __( 'Register', 'nu-research' ), $nu_next_up['link'] ); ?>
					<?php endif; ?>
				</div>
			</article>
		</section>
	<?php endif; ?>

	<section class="section-block" aria-labelledby="upcoming-heading">
		<h2 id="upcoming-heading" data-aos="fade-up"><?php esc_html_e( 'Upcoming events', 'nu-research' ); ?></h2>

		<?php if ( ! empty( $nu_later ) ) : ?>
			<ol class="event-rows">
				<?php
				$nu_row_index = 0;
				foreach ( $nu_later as $nu_event ) :
					$nu_d     = nu_research_event_date_parts( $nu_event['date'] );
					$nu_delay = min( $nu_row_index, 4 ) * 75;
					$nu_row_index++;
					?>
					<li class="event-row" data-aos="fade-up" data-aos-delay="<?php echo esc_attr( $nu_delay ); ?>">
						<div class="event-date">
							<time datetime="<?php echo esc_attr( $nu_d['iso'] ); ?>">
								<span class="event-date-month"><?php echo esc_html( $nu_d['month'] ); ?></span>
								<span class="event-date-day"><?php echo esc_html( $nu_d['day'] ); ?></span>
								<span class="event-date-weekday"><?php echo esc_html( $nu_d['weekday'] ); ?></span>
							</time>
						</div>
						<div class="event-body">
							<?php if ( ! empty( $nu_event['type'] ) ) : ?>
								<p class="card-overline"><?php echo esc_html( $nu_event['type'] ); ?></p>
							<?php endif; ?>
							<h3 class="event-title"><?php echo esc_html( $nu_event['title'] ); ?></h3>
							<p class="event-meta">
								<?php if ( ! empty( $nu_event['time'] ) ) : ?>
									<?php echo esc_html( $nu_event['time'] ); ?>
								<?php endif; ?>
								<?php if ( ! empty( $nu_event['time'] ) && ! empty( $nu_event['location'] ) ) : ?>
									&middot;
								<?php endif; ?>
								<?php if ( ! empty( $nu_event['location'] ) ) : ?>
									<?php echo esc_html( $nu_event['location'] ); ?>
								<?php endif; ?>
							</p>
							<?php if ( ! empty( $nu_event['description'] ) ) : ?>
								<p class="event-desc"><?php echo esc_html( $nu_event['description'] ); ?></p>
							<?php endif; ?>
						</div>
						<div class="event-action">
							<?php if ( ! empty( $nu_event['link'] ) ) : ?>
								<a class="arrow-link" href="<?php echo esc_url( $nu_event['link'] ); ?>">
									<?php echo esc_html( ! empty( $nu_event['link_label'] ) ? $nu_event['link_label'] : __( 'Details', 'nu-research' ) ); ?>
									<span class="arrow-link-glyph" aria-hidden="true">&rarr;</span>
									<span class="screen-reader-text"><?php echo esc_html( sprintf( /* translators: %s: event title */ __( 'about %s', 'nu-research' ), $nu_event['title'] ) ); ?></span>
								</a>
							<?php endif; ?>
						</div>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php elseif ( ! $nu_next_up ) : ?>
			<p class="empty-state"><?php esc_html_e( 'No upcoming events are scheduled yet — dates for the next cycle are announced each December.', 'nu-research' ); ?></p>
		<?php else : ?>
			<p class="event-rows-note"><?php esc_html_e( 'Nothing else on the calendar yet — more dates are announced as the summer progresses.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

	<section class="section-block" aria-labelledby="past-heading">
		<h2 id="past-heading" data-aos="fade-up"><?php esc_html_e( 'Past events', 'nu-research' ); ?></h2>

		<?php if ( ! empty( $nu_past ) ) : ?>
			<ol class="past-events" data-aos="fade-up">
				<?php foreach ( $nu_past as $nu_event ) : ?>
					<?php $nu_d = nu_research_event_date_parts( $nu_event['date'] ); ?>
					<li class="past-event">
						<time class="past-event-date" datetime="<?php echo esc_attr( $nu_d['iso'] ); ?>"><?php echo esc_html( $nu_d['full'] ); ?></time>
						<span class="past-event-title"><?php echo esc_html( $nu_event['title'] ); ?></span>
						<?php if ( ! empty( $nu_event['type'] ) ) : ?>
							<span class="badge badge-outline"><?php echo esc_html( $nu_event['type'] ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		<?php else : ?>
			<p class="empty-state"><?php esc_html_e( 'Nothing in the archive yet — this is the program’s first cycle.', 'nu-research' ); ?></p>
		<?php endif; ?>
	</section>

</div>

<?php get_footer(); ?>
