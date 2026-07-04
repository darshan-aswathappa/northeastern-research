<?php
/**
 * Uninstall: drop the waitlist table and remove plugin options.
 *
 * @package swe-mail-list
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

global $wpdb;

$swe_ml_table = $wpdb->prefix . 'swe_waitlist';
$wpdb->query( "DROP TABLE IF EXISTS {$swe_ml_table}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

delete_option( 'swe_ml_db_version' );
