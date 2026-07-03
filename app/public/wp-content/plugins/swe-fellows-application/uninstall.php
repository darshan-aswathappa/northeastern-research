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
