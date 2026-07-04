<?php
/**
 * Plugin Name: Fellows Mail List
 * Description: Waitlist email capture for the WordPress Research Fellows Program. Renders via [swe_waitlist], stores subscribers in a dedicated database table (wp_swe_waitlist), and provides a Mail List admin screen with bulk announcement emails. Intake open/closed state derives from the Fellows Deadline application-window dates.
 * Version: 1.0.0
 * Author: Darshan Aswathappa
 * License: GPL v2 or later
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Text Domain: swe-mail-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SWE_ML_VERSION', '1.0.0' );
define( 'SWE_ML_DB_VERSION', '1' ); // String: get_option() returns strings, and the upgrade check compares strictly.
define( 'SWE_ML_PATH', plugin_dir_path( __FILE__ ) );
define( 'SWE_ML_URL', plugin_dir_url( __FILE__ ) );

require_once SWE_ML_PATH . 'includes/db.php';
require_once SWE_ML_PATH . 'includes/form.php';
require_once SWE_ML_PATH . 'includes/emails.php';

if ( is_admin() ) {
	require_once SWE_ML_PATH . 'includes/admin.php';
}

register_activation_hook( __FILE__, 'swe_ml_install' );

/**
 * Upgrade-safe schema check (covers updates where the activation hook
 * doesn't re-fire).
 */
function swe_ml_maybe_upgrade() {
	if ( get_option( 'swe_ml_db_version' ) !== SWE_ML_DB_VERSION ) {
		swe_ml_install();
	}
}
add_action( 'plugins_loaded', 'swe_ml_maybe_upgrade' );

/**
 * Whether the application intake is currently open.
 *
 * Derived from the Fellows Deadline plugin's application-window dates
 * (fellows_dl_open / fellows_dl_close) so the Apply page form, the waitlist,
 * the utility-bar status pill, and the closing-soon banner all agree. The
 * comparisons intentionally mirror fellows_deadline_render_shortcode().
 *
 * The theme's Apply template checks this to decide between rendering the
 * application form and the closed-intake waitlist.
 *
 * @return bool
 */
function swe_ml_intake_is_open() {
	$open_str  = get_option( 'fellows_dl_open', '' );
	$close_str = get_option( 'fellows_dl_close', '' );
	$open_ts   = $open_str ? strtotime( $open_str ) : false;
	$close_ts  = $close_str ? strtotime( $close_str ) : false;

	// No window configured → no active intake; collect waitlist signups.
	if ( ! $open_ts && ! $close_ts ) {
		return false;
	}

	$now = current_time( 'timestamp' );

	if ( $open_ts && $now < $open_ts ) {
		return false; // Opening soon.
	}
	if ( $close_ts && $now > $close_ts ) {
		return false; // Deadline passed.
	}

	return true;
}

/**
 * Best-guess URL for the Apply page (used to prefill the announcement link).
 *
 * @return string
 */
function swe_ml_apply_page_url() {
	$pages = get_pages(
		array(
			'meta_key'   => '_wp_page_template', // phpcs:ignore WordPress.DB.SlowDBQuery
			'meta_value' => 'page-apply.php', // phpcs:ignore WordPress.DB.SlowDBQuery
			'number'     => 1,
		)
	);

	if ( ! empty( $pages ) ) {
		return get_permalink( $pages[0] );
	}

	return home_url( '/' );
}
