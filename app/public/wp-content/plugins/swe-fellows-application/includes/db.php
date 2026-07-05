<?php
/**
 * Database layer: dedicated applications table.
 *
 * Submissions live in their own table (not posts/postmeta) so a future
 * custom admin backend can query them with plain SQL — proper columns,
 * types, and indexes.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Fully-prefixed table name.
 *
 * @return string
 */
function swe_app_table_name() {
	global $wpdb;
	return $wpdb->prefix . 'swe_applications';
}

/**
 * Create/upgrade the applications table.
 */
function swe_app_install() {
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';

	$table_name      = swe_app_table_name();
	$charset_collate = $wpdb->get_charset_collate();

	$sql = "CREATE TABLE {$table_name} (
		id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
		name VARCHAR(190) NOT NULL,
		email VARCHAR(190) NOT NULL,
		class_year VARCHAR(40) NOT NULL DEFAULT '',
		track VARCHAR(80) NOT NULL DEFAULT '',
		coursework VARCHAR(255) NOT NULL DEFAULT '',
		statement TEXT NOT NULL,
		resume_path VARCHAR(255) NOT NULL DEFAULT '',
		status VARCHAR(20) NOT NULL DEFAULT 'new',
		created_at DATETIME NOT NULL,
		PRIMARY KEY  (id),
		KEY email (email),
		KEY track (track),
		KEY created_at (created_at)
	) {$charset_collate};";

	dbDelta( $sql );
	update_option( 'swe_app_db_version', SWE_APP_DB_VERSION );
}

/**
 * Insert a validated application.
 *
 * @param array $data Validated data from swe_app_validate().
 * @return int|WP_Error Row ID on success.
 */
function swe_app_insert( array $data ) {
	global $wpdb;

	$inserted = $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		swe_app_table_name(),
		array(
			'name'        => $data['name'],
			'email'       => $data['email'],
			'class_year'  => $data['class_year'],
			'track'       => $data['track'],
			'coursework'  => $data['coursework'],
			'statement'   => $data['statement'],
			'resume_path' => isset( $data['resume_path'] ) ? $data['resume_path'] : '',
			'status'      => 'new',
			'created_at'  => current_time( 'mysql', true ),
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	if ( false === $inserted ) {
		return new WP_Error( 'swe_app_save_failed', __( 'Could not save application.', 'swe-fellows-application' ), array( 'status' => 500 ) );
	}

	$id = (int) $wpdb->insert_id;

	// Notify the program office.
	$office_email = get_option( 'admin_email' );
	wp_mail(
		$office_email,
		sprintf( /* translators: %s: applicant name */ __( 'New Fellows application: %s', 'swe-fellows-application' ), $data['name'] ),
		sprintf(
			"Name: %s\nEmail: %s\nClass year: %s\nTrack: %s\nCoursework: %s\n\nStatement:\n%s\n\nReview: %s",
			$data['name'],
			$data['email'],
			$data['class_year'],
			$data['track'],
			$data['coursework'],
			$data['statement'],
			admin_url( 'admin.php?page=swe-applications&view=' . $id )
		)
	);

	// Confirmation email to the applicant.
	swe_app_send_received_email( $data );

	return $id;
}

/**
 * Fetch one application row.
 *
 * @param int $id Row ID.
 * @return object|null
 */
function swe_app_get( $id ) {
	global $wpdb;
	$table_name = swe_app_table_name();
	return $wpdb->get_row( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare( "SELECT * FROM {$table_name} WHERE id = %d", $id ) // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	);
}

/**
 * Delete one application row.
 *
 * @param int $id Row ID.
 * @return bool
 */
function swe_app_delete( $id ) {
	global $wpdb;

	// Remove the applicant's resume from disk before dropping the row, so the
	// file doesn't linger with no record pointing to it.
	$row = swe_app_get( $id );
	if ( $row && ! empty( $row->resume_path ) ) {
		swe_app_delete_resume_file( $row->resume_path );
	}

	return (bool) $wpdb->delete( swe_app_table_name(), array( 'id' => (int) $id ), array( '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery
}

/**
 * Update an application's review status.
 *
 * @param int    $id     Row ID.
 * @param string $status One of the allowed statuses.
 * @return bool
 */
function swe_app_set_status( $id, $status ) {
	global $wpdb;

	if ( ! in_array( $status, swe_app_allowed_statuses(), true ) ) {
		return false;
	}

	$id       = (int) $id;
	$existing = swe_app_get( $id );
	if ( ! $existing ) {
		return false;
	}

	$updated = (bool) $wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
		swe_app_table_name(),
		array( 'status' => $status ),
		array( 'id' => $id ),
		array( '%s' ),
		array( '%d' )
	);

	// Email the applicant only on the transition *into* rejected, so
	// re-saving an already-rejected application doesn't send a duplicate.
	if ( $updated && 'rejected' === $status && 'rejected' !== $existing->status ) {
		swe_app_send_rejection_email( $existing );
	}

	return $updated;
}

/**
 * Allowed review statuses.
 *
 * @return string[]
 */
function swe_app_allowed_statuses() {
	return array( 'new', 'reviewed', 'accepted', 'rejected' );
}
