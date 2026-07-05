<?php
/**
 * Uninstall: remove the applications table and plugin options.
 *
 * Runs only when the plugin is deleted from the Plugins screen (not on
 * deactivate), per WordPress convention — deactivation must never destroy
 * submission data.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$swe_table = $wpdb->prefix . 'swe_applications';
$wpdb->query( "DROP TABLE IF EXISTS {$swe_table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

delete_option( 'swe_app_db_version' );

// Remove stored resumes (uploads/swe-resumes/) so nothing is orphaned on delete.
$swe_uploads = wp_upload_dir();
$swe_resumes = trailingslashit( $swe_uploads['basedir'] ) . 'swe-resumes';
if ( is_dir( $swe_resumes ) ) {
	$swe_files = glob( trailingslashit( $swe_resumes ) . '*' );
	if ( is_array( $swe_files ) ) {
		foreach ( $swe_files as $swe_file ) {
			if ( is_file( $swe_file ) ) {
				wp_delete_file( $swe_file );
			}
		}
	}
	@rmdir( $swe_resumes ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_rmdir
}
