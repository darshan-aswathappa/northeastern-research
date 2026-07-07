<?php
/**
 * Plugin Name: Fellows Deadline Countdown
 * Description: Deadline settings, [fellows_countdown] shortcode, a "closing soon" site-wide banner, and an admin dashboard snapshot widget for the SWE Fellows Program.
 * Version:     1.1.1
 * Author:      Darshan Aswathappa
 * License:     GPL v2 or later
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Text Domain: fellows-deadline
 *
 * @package fellows-deadline
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'FELLOWS_DL_VERSION', '1.1.1' );
define( 'FELLOWS_DL_URL', plugin_dir_url( __FILE__ ) );

// Closing-soon banner: default and maximum lead time (in days) before the close date.
define( 'FELLOWS_DL_BANNER_DAYS_DEFAULT', 7 );
define( 'FELLOWS_DL_BANNER_DAYS_MAX', 90 );

/*
-------------------------------------------------------------------------- *
 * Settings: Settings → Fellows Deadline
 * -------------------------------------------------------------------------- */

/**
 * Register the Settings sub-page under the Settings menu.
 */
function fellows_deadline_admin_menu() {
	add_submenu_page(
		'options-general.php',
		__( 'Fellows Deadline', 'fellows-deadline' ),
		__( 'Fellows Deadline', 'fellows-deadline' ),
		'manage_options',
		'fellows-deadline',
		'fellows_deadline_settings_page'
	);
}

/**
 * Register settings, sections, and fields via the Settings API.
 */
function fellows_deadline_settings_init() {
	register_setting(
		'fellows_deadline_group',
		'fellows_dl_open',
		array( 'sanitize_callback' => 'fellows_deadline_sanitize_date' )
	);
	register_setting(
		'fellows_deadline_group',
		'fellows_dl_close',
		array( 'sanitize_callback' => 'fellows_deadline_sanitize_date' )
	);
	register_setting(
		'fellows_deadline_group',
		'fellows_dl_notify',
		array( 'sanitize_callback' => 'fellows_deadline_sanitize_date' )
	);
	register_setting(
		'fellows_deadline_group',
		'fellows_dl_banner_days',
		array(
			'sanitize_callback' => 'fellows_deadline_sanitize_days',
			'default'           => FELLOWS_DL_BANNER_DAYS_DEFAULT,
		)
	);

	add_settings_section(
		'fellows_dl_main',
		__( 'Application Window Dates', 'fellows-deadline' ),
		static function () {
			echo '<p>' . esc_html__( 'Set the key dates for the application cycle. Leave a field blank to omit it from the shortcode and banner logic.', 'fellows-deadline' ) . '</p>';
		},
		'fellows-deadline'
	);

	$fields = array(
		'fellows_dl_open'   => __( 'Applications open', 'fellows-deadline' ),
		'fellows_dl_close'  => __( 'Applications close', 'fellows-deadline' ),
		'fellows_dl_notify' => __( 'Decision notifications', 'fellows-deadline' ),
	);

	foreach ( $fields as $key => $label ) {
		add_settings_field(
			$key,
			$label,
			static function () use ( $key ) {
				printf(
					'<input type="date" id="%1$s" name="%1$s" value="%2$s" class="regular-text">',
					esc_attr( $key ),
					esc_attr( get_option( $key, '' ) )
				);
			},
			'fellows-deadline',
			'fellows_dl_main'
		);
	}

	add_settings_section(
		'fellows_dl_banner',
		__( 'Closing-Soon Banner', 'fellows-deadline' ),
		static function () {
			echo '<p>' . esc_html__( 'Control the site-wide banner that appears as the deadline approaches.', 'fellows-deadline' ) . '</p>';
		},
		'fellows-deadline'
	);

	add_settings_field(
		'fellows_dl_banner_days',
		__( 'Show banner within', 'fellows-deadline' ),
		static function () {
			printf(
				'<input type="number" id="fellows_dl_banner_days" name="fellows_dl_banner_days" value="%1$s" min="1" max="%2$d" step="1" class="small-text"> %3$s',
				esc_attr( (string) fellows_deadline_banner_threshold() ),
				esc_attr( (string) FELLOWS_DL_BANNER_DAYS_MAX ),
				esc_html__( 'days of the close date', 'fellows-deadline' )
			);
			echo '<p class="description">' . esc_html__( 'The banner is hidden until the deadline is this many days away (1–90). Leave the close date blank to disable the banner entirely.', 'fellows-deadline' ) . '</p>';
		},
		'fellows-deadline',
		'fellows_dl_banner'
	);
}

/**
 * Sanitize callback: only accept valid Y-m-d dates or an empty string.
 *
 * @param mixed $value Raw input.
 * @return string Clean Y-m-d string or ''.
 */
function fellows_deadline_sanitize_date( $value ) {
	$value = sanitize_text_field( (string) $value );
	if ( '' === $value ) {
		return '';
	}
	$dt = DateTime::createFromFormat( 'Y-m-d', $value );
	return ( $dt && $dt->format( 'Y-m-d' ) === $value ) ? $value : '';
}

/**
 * Sanitize callback: clamp the banner lead time to a whole number of days in range.
 *
 * @param mixed $value Raw input.
 * @return int Days between 1 and FELLOWS_DL_BANNER_DAYS_MAX.
 */
function fellows_deadline_sanitize_days( $value ) {
	$value = absint( $value );
	if ( $value < 1 ) {
		return FELLOWS_DL_BANNER_DAYS_DEFAULT;
	}
	return min( $value, FELLOWS_DL_BANNER_DAYS_MAX );
}

/**
 * Get the configured banner lead time in days, clamped to the valid range.
 *
 * @return int Days before the close date the banner starts showing.
 */
function fellows_deadline_banner_threshold() {
	return fellows_deadline_sanitize_days(
		get_option( 'fellows_dl_banner_days', FELLOWS_DL_BANNER_DAYS_DEFAULT )
	);
}

/**
 * Build the "decisions expected" phrase from the notify date.
 *
 * Returns a localized phrase while the decision date is still in the future,
 * or an empty string when no notify date is set or the date has passed.
 *
 * @return string Localized phrase or ''.
 */
function fellows_deadline_notify_phrase() {
	$notify_str = get_option( 'fellows_dl_notify', '' );
	if ( ! $notify_str ) {
		return '';
	}

	$notify_ts = strtotime( $notify_str );
	if ( ! $notify_ts || current_time( 'timestamp' ) > $notify_ts ) {
		return '';
	}

	return sprintf(
		/* translators: %s: formatted decision-notification date */
		esc_html__( 'Decisions expected %s', 'fellows-deadline' ),
		date_i18n( get_option( 'date_format' ), $notify_ts )
	);
}

/**
 * Render the settings page.
 */
function fellows_deadline_settings_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Fellows Deadline Settings', 'fellows-deadline' ); ?></h1>
		<?php settings_errors(); ?>
		<form method="post" action="options.php">
			<?php
			settings_fields( 'fellows_deadline_group' );
			do_settings_sections( 'fellows-deadline' );
			submit_button();
			?>
		</form>
	</div>
	<?php
}

/*
-------------------------------------------------------------------------- *
 * Shortcode: [fellows_countdown]
 * -------------------------------------------------------------------------- */

/**
 * Render the deadline status shortcode.
 *
 * States: not-configured → opening-soon → open → closed.
 *
 * Attributes:
 *   show_days   "true"|"false" — show the "· Closes in N days" suffix while open. Default true.
 *   show_notify "true"|"false" — show the "Decisions expected …" line once closed. Default true.
 *
 * @param array|string $atts Shortcode attributes.
 * @return string HTML output.
 */
function fellows_deadline_render_shortcode( $atts = array() ) {
	$atts = shortcode_atts(
		array(
			'show_days'   => 'true',
			'show_notify' => 'true',
		),
		$atts,
		'fellows_countdown'
	);

	$show_days   = filter_var( $atts['show_days'], FILTER_VALIDATE_BOOLEAN );
	$show_notify = filter_var( $atts['show_notify'], FILTER_VALIDATE_BOOLEAN );

	wp_enqueue_style( 'fellows-deadline' );

	$open_str  = get_option( 'fellows_dl_open', '' );
	$close_str = get_option( 'fellows_dl_close', '' );
	$open_ts   = $open_str ? strtotime( $open_str ) : false;
	$close_ts  = $close_str ? strtotime( $close_str ) : false;
	$now       = current_time( 'timestamp' );

	// Nothing to show if no dates are configured.
	if ( ! $open_ts && ! $close_ts ) {
		return '';
	}

	if ( $open_ts && $now < $open_ts ) {
		// --- Opening soon ---
		ob_start();
		?>
		<div class="fellows-countdown fellows-countdown--soon">
			<span class="fellows-countdown__label">
				<?php
				printf(
					/* translators: %s: formatted open date */
					esc_html__( 'Opening soon: %s', 'fellows-deadline' ),
					esc_html( date_i18n( get_option( 'date_format' ), $open_ts ) )
				);
				?>
			</span>
		</div>
		<?php
		return ob_get_clean();
	}

	if ( $close_ts && $now > $close_ts ) {
		// --- Closed ---
		$notify = $show_notify ? fellows_deadline_notify_phrase() : '';
		ob_start();
		?>
		<div class="fellows-countdown fellows-countdown--closed">
			<span class="fellows-countdown__label">
				<?php esc_html_e( 'Applications are closed for this cycle.', 'fellows-deadline' ); ?>
			</span>
			<?php if ( $notify ) : ?>
				<span class="fellows-countdown__notify"><?php echo esc_html( $notify ); ?></span>
			<?php endif; ?>
		</div>
		<?php
		return ob_get_clean();
	}

	// --- Window is open ---
	$days_label = '';
	if ( $close_ts && $show_days ) {
		$days_left  = (int) ceil( ( $close_ts - $now ) / DAY_IN_SECONDS );
		$days_label = ' &middot; ' . sprintf(
			/* translators: %d: number of days */
			esc_html( _n( 'Closes in %d day', 'Closes in %d days', $days_left, 'fellows-deadline' ) ),
			$days_left
		);
	}

	ob_start();
	?>
	<div class="fellows-countdown fellows-countdown--open">
		<span class="fellows-countdown__indicator" aria-hidden="true"></span>
		<span class="fellows-countdown__label">
			<?php esc_html_e( 'Applications open', 'fellows-deadline' ); ?><?php echo wp_kses_post( $days_label ); ?>
		</span>
	</div>
	<?php
	return ob_get_clean();
}

/*
-------------------------------------------------------------------------- *
 * "Closing soon" banner (fixed bottom, shown ≤ 7 days before deadline)
 * -------------------------------------------------------------------------- */

/**
 * Output the closing-soon banner in wp_footer if within the 7-day window.
 */
function fellows_deadline_banner() {
	if ( is_admin() ) {
		return;
	}

	$close_str = get_option( 'fellows_dl_close', '' );
	if ( ! $close_str ) {
		return;
	}

	$close_ts  = strtotime( $close_str );
	$now       = current_time( 'timestamp' );
	$days_left = (int) ceil( ( $close_ts - $now ) / DAY_IN_SECONDS );
	$threshold = fellows_deadline_banner_threshold();

	if ( $days_left < 1 || $days_left > $threshold ) {
		return;
	}

	// Print the stylesheet in the footer if not already loaded via shortcode.
	wp_print_styles( 'fellows-deadline' );

	$apply_url = function_exists( 'nu_research_page_url' )
		? nu_research_page_url( 'apply-eligibility' )
		: home_url( '/apply-eligibility/' );

	$days_text = sprintf(
		/* translators: %d: number of days */
		_n( 'Applications close in %d day.', 'Applications close in %d days.', $days_left, 'fellows-deadline' ),
		$days_left
	);
	?>
	<div class="fellows-dl-banner" id="fellows-dl-banner" role="alert" aria-live="polite">
		<div class="fellows-dl-banner__inner wrap">
			<p class="fellows-dl-banner__msg">
				<strong><?php echo esc_html( $days_text ); ?></strong>
				<a href="<?php echo esc_url( $apply_url ); ?>"><?php esc_html_e( 'Apply now →', 'fellows-deadline' ); ?></a>
			</p>
			<button class="fellows-dl-banner__dismiss" type="button" aria-label="<?php esc_attr_e( 'Dismiss this notice', 'fellows-deadline' ); ?>">&#x2715;</button>
		</div>
	</div>
	<script>
	( function () {
		var KEY    = 'fellows_dl_dismissed';
		var banner = document.getElementById( 'fellows-dl-banner' );
		if ( ! banner ) { return; }
		if ( sessionStorage.getItem( KEY ) ) {
			banner.style.display = 'none';
			return;
		}
		banner.querySelector( '.fellows-dl-banner__dismiss' )
			.addEventListener( 'click', function () {
				sessionStorage.setItem( KEY, '1' );
				banner.style.display = 'none';
			} );
	} )();
	</script>
	<?php
}

/*
-------------------------------------------------------------------------- *
 * Admin dashboard widget: Application Snapshot
 * -------------------------------------------------------------------------- */

/**
 * Register the dashboard widget.
 *
 * wp_add_dashboard_widget() is only available after admin files are loaded;
 * the function_exists guard prevents a fatal when called in non-admin contexts
 * (e.g. WP-CLI without --require=wp-admin/includes/dashboard.php).
 */
function fellows_deadline_dashboard_setup() {
	if ( ! function_exists( 'wp_add_dashboard_widget' ) ) {
		return;
	}
	wp_add_dashboard_widget(
		'fellows_deadline_snapshot',
		__( 'Application Snapshot', 'fellows-deadline' ),
		'fellows_deadline_dashboard_widget'
	);
}

/**
 * Render the dashboard widget.
 */
function fellows_deadline_dashboard_widget() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}

	$counts = fellows_deadline_query_counts();

	if ( null === $counts ) {
		echo '<p>' . esc_html__( 'The applications table (wp_swe_applications) hasn\'t been created yet. Activate the SWE Fellows Application plugin to create it.', 'fellows-deadline' ) . '</p>';
		return;
	}

	$admin_url = admin_url( 'admin.php?page=swe-applications' );
	$statuses  = array(
		'new'      => __( 'New', 'fellows-deadline' ),
		'reviewed' => __( 'Reviewed', 'fellows-deadline' ),
		'accepted' => __( 'Accepted', 'fellows-deadline' ),
		'rejected' => __( 'Rejected', 'fellows-deadline' ),
	);
	?>
	<table class="fellows-dl-snapshot widefat">
		<tbody>
			<?php foreach ( $statuses as $key => $label ) : ?>
				<tr>
					<th scope="row"><a href="<?php echo esc_url( $admin_url ); ?>"><?php echo esc_html( $label ); ?></a></th>
					<td><?php echo esc_html( (string) $counts[ $key ] ); ?></td>
				</tr>
			<?php endforeach; ?>
			<tr class="fellows-dl-snapshot__total">
				<th scope="row"><?php esc_html_e( 'Total', 'fellows-deadline' ); ?></th>
				<td><?php echo esc_html( (string) $counts['total'] ); ?></td>
			</tr>
		</tbody>
	</table>
	<?php

	$close_str = get_option( 'fellows_dl_close', '' );
	if ( $close_str ) {
		printf(
			'<p class="fellows-dl-snapshot__dates">%s <strong>%s</strong></p>',
			esc_html__( 'Window closes:', 'fellows-deadline' ),
			esc_html( date_i18n( get_option( 'date_format' ), strtotime( $close_str ) ) )
		);
	}

	$notify_str = get_option( 'fellows_dl_notify', '' );
	if ( $notify_str ) {
		printf(
			'<p class="fellows-dl-snapshot__dates">%s <strong>%s</strong></p>',
			esc_html__( 'Decisions sent:', 'fellows-deadline' ),
			esc_html( date_i18n( get_option( 'date_format' ), strtotime( $notify_str ) ) )
		);
	}
}

/**
 * Query application counts grouped by status from the existing custom table.
 *
 * @return array|null Associative array of status => count + 'total', or null if table absent.
 */
function fellows_deadline_query_counts() {
	global $wpdb;

	$table  = $wpdb->prefix . 'swe_applications';
	$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery

	if ( ! $exists ) {
		return null;
	}

	$rows = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		"SELECT status, COUNT(*) AS cnt FROM {$table} GROUP BY status",
		ARRAY_A
	);

	$counts = array(
		'new'      => 0,
		'reviewed' => 0,
		'accepted' => 0,
		'rejected' => 0,
	);
	foreach ( (array) $rows as $row ) {
		$status = $row['status'];
		if ( array_key_exists( $status, $counts ) ) {
			$counts[ $status ] = (int) $row['cnt'];
		}
	}
	$counts['total'] = array_sum( $counts );

	return $counts;
}

/*
-------------------------------------------------------------------------- *
 * Style registration (enqueue happens inside shortcode or banner)
 * -------------------------------------------------------------------------- */

/**
 * Register the stylesheet, then enqueue it on the front end.
 *
 * The utility-bar status pill is printed on every page via the
 * nu_research_utility_bar action, so its styles (and the .utility-bar__row
 * flex layout) must always be present. The shortcode and banner also call
 * wp_enqueue_style()/wp_print_styles() — enqueuing is idempotent, so that
 * remains safe.
 */
function fellows_deadline_register_style() {
	wp_register_style(
		'fellows-deadline',
		FELLOWS_DL_URL . 'assets/deadline.css',
		array(),
		FELLOWS_DL_VERSION
	);

	if ( ! is_admin() ) {
		wp_enqueue_style( 'fellows-deadline' );
	}
}

/*
-------------------------------------------------------------------------- *
 * Utility-bar status pill — hooked into nu_research_utility_bar action
 * -------------------------------------------------------------------------- */

/**
 * Output an inline status pill on the right side of the existing utility bar.
 * Hooked to the nu_research_utility_bar action added to header.php — no
 * separate bar, no double header.
 */
function fellows_deadline_utility_status() {
	$open_str  = get_option( 'fellows_dl_open', '' );
	$close_str = get_option( 'fellows_dl_close', '' );
	$open_ts   = $open_str ? strtotime( $open_str ) : false;
	$close_ts  = $close_str ? strtotime( $close_str ) : false;
	$now       = current_time( 'timestamp' );

	if ( ! $open_ts && ! $close_ts ) {
		return;
	}

	if ( $open_ts && $now < $open_ts ) {
		$state   = 'soon';
		$message = sprintf(
			/* translators: %s: formatted open date */
			__( 'Applications open %s', 'fellows-deadline' ),
			date_i18n( get_option( 'date_format' ), $open_ts )
		);
		$short   = __( 'Applications open soon', 'fellows-deadline' );
	} elseif ( $close_ts && $now > $close_ts ) {
		$state   = 'closed';
		$message = __( 'Applications closed', 'fellows-deadline' );
		$short   = $message;
		$notify  = fellows_deadline_notify_phrase();
		if ( $notify ) {
			/* translators: 1: "Applications closed", 2: "Decisions expected <date>" */
			$message = sprintf(
				__( '%1$s · %2$s', 'fellows-deadline' ),
				$message,
				$notify
			);
		}
	} else {
		$state = 'open';
		$short = __( 'Applications open', 'fellows-deadline' );
		if ( $close_ts ) {
			$days_left = (int) ceil( ( $close_ts - $now ) / DAY_IN_SECONDS );
			$message   = sprintf(
				/* translators: %d: number of days */
				_n( 'Applications open · %d day left', 'Applications open · %d days left', $days_left, 'fellows-deadline' ),
				$days_left
			);
		} else {
			$message = $short;
		}
	}

	// Full text on larger screens; the short, date-free text on phones
	// (deadline.css swaps the two spans at the mobile breakpoint).
	$label = '<span class="fellows-util-status__full">' . esc_html( $message ) . '</span>'
		. '<span class="fellows-util-status__short">' . esc_html( $short ) . '</span>';

	// Closed state is not a link — renders as a plain span to avoid theme link-colour bleed.
	if ( 'closed' === $state ) :
		?>
		<span class="fellows-util-status fellows-util-status--closed" role="status">
			<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html() above. ?>
		</span>
		<?php
		return;
	endif;

	$apply_url = function_exists( 'nu_research_page_url' )
		? nu_research_page_url( 'apply-eligibility' )
		: home_url( '/apply-eligibility/' );
	?>
	<a class="fellows-util-status fellows-util-status--<?php echo esc_attr( $state ); ?>"
		href="<?php echo esc_url( $apply_url ); ?>"
		aria-label="<?php echo esc_attr( $message . ' — ' . __( 'go to apply page', 'fellows-deadline' ) ); ?>">
		<?php if ( 'open' === $state ) : ?>
			<span class="fellows-util-status__dot" aria-hidden="true"></span>
		<?php endif; ?>
		<?php echo $label; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from esc_html() above. ?>
	</a>
	<?php
}

/*
-------------------------------------------------------------------------- *
 * Hook registrations
 * -------------------------------------------------------------------------- */

add_action( 'admin_menu', 'fellows_deadline_admin_menu' );
add_action( 'admin_init', 'fellows_deadline_settings_init' );
add_action( 'wp_dashboard_setup', 'fellows_deadline_dashboard_setup' );
add_action( 'wp_enqueue_scripts', 'fellows_deadline_register_style' );
add_action( 'nu_research_utility_bar', 'fellows_deadline_utility_status' );
add_action( 'wp_footer', 'fellows_deadline_banner' );
add_shortcode( 'fellows_countdown', 'fellows_deadline_render_shortcode' );
