<?php
/**
 * Database layer: dedicated waitlist table.
 *
 * Subscribers live in their own table (not posts/postmeta) with a UNIQUE
 * key on email so duplicates are rejected at the database level, not just
 * in application code.
 *
 * @package swe-mail-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fully-prefixed table name.
 *
 * @return string
 */
function swe_ml_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'swe_waitlist';
}

/**
 * Create/upgrade the waitlist table.
 */
function swe_ml_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = swe_ml_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		email VARCHAR(190) NOT NULL,
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		UNIQUE KEY email (email)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'swe_ml_db_version', SWE_ML_DB_VERSION );
}

/**
 * Add an email to the waitlist.
 *
 * Emails are stored lowercased so the UNIQUE key catches case-variant
 * duplicates too.
 *
 * @param string $email Raw email input.
 * @return int|WP_Error Row ID on success, WP_Error on invalid/duplicate.
 */
function swe_ml_subscribe( $email ) {
	global $wpdb;

	$email = strtolower( sanitize_email( (string) $email ) );

	if ( '' === $email || ! is_email( $email ) ) {
		return new WP_Error( 'swe_ml_invalid_email', __( 'Please enter a valid email address.', 'swe-mail-list' ), array( 'status' => 400 ) );
	}

	if ( swe_ml_email_exists( $email ) ) {
		return new WP_Error( 'swe_ml_duplicate', __( 'This email is already on the waitlist.', 'swe-mail-list' ), array( 'status' => 409 ) );
	}

	$inserted = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		swe_ml_table_name(),
		array(
			'email'      => $email,
			'created_at' => current_time( 'mysql', true ),
		),
		array( '%s', '%s' )
	);

	if ( false === $inserted ) {
		// A concurrent request may have won the race; the UNIQUE key makes
		// the insert fail, which we report as a duplicate rather than a
		// generic server error.
		if ( swe_ml_email_exists( $email ) ) {
			return new WP_Error( 'swe_ml_duplicate', __( 'This email is already on the waitlist.', 'swe-mail-list' ), array( 'status' => 409 ) );
		}
		return new WP_Error( 'swe_ml_save_failed', __( 'Could not save your email — please try again.', 'swe-mail-list' ), array( 'status' => 500 ) );
	}

	return (int) $wpdb->insert_id;
}

/**
 * Whether an email is already on the waitlist.
 *
 * @param string $email Lowercased, sanitized email.
 * @return bool
 */
function swe_ml_email_exists( $email ) {
	global $wpdb;
	$table_name = swe_ml_table_name();
	return (bool) $wpdb->get_var( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare( "SELECT id FROM {$table_name} WHERE email = %s", $email ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	);
}

/**
 * All subscriber emails, oldest first.
 *
 * @return string[]
 */
function swe_ml_get_emails() {
	global $wpdb;
	$table_name = swe_ml_table_name();
	return $wpdb->get_col( "SELECT email FROM {$table_name} ORDER BY id ASC" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Total subscriber count.
 *
 * @return int
 */
function swe_ml_count() {
	global $wpdb;
	$table_name = swe_ml_table_name();
	return (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
}

/**
 * Delete one subscriber row.
 *
 * @param int $id Row ID.
 * @return bool
 */
function swe_ml_delete( $id ) {
	global $wpdb;
	return (bool) $wpdb->delete( swe_ml_table_name(), array( 'id' => (int) $id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}
