<?php
/**
 * Bulk announcement sending.
 *
 * Recipients go in BCC batches so subscriber addresses are never exposed
 * to each other and large lists don't mean hundreds of wp_mail() calls.
 *
 * @package swe-mail-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * How many BCC recipients per wp_mail() call.
 *
 * @return int
 */
function swe_ml_batch_size() {
	return 50;
}

/**
 * Send an announcement to a list of subscriber emails.
 *
 * @param string[] $recipients Subscriber emails.
 * @param string   $subject    Email subject.
 * @param string   $message    Plain-text body.
 * @param string   $link       Optional link appended to the body.
 * @return int Number of recipients in batches that sent successfully.
 */
function swe_ml_send_announcement( array $recipients, $subject, $message, $link = '' ) {
	$recipients = array_values( array_filter( array_map( 'sanitize_email', $recipients ), 'is_email' ) );
	if ( empty( $recipients ) ) {
		return 0;
	}

	$body = trim( $message );
	if ( '' !== $link ) {
		$body .= "\n\n" . sprintf( /* translators: %s: application URL */ __( 'Apply now: %s', 'swe-mail-list' ), $link );
	}
	$body .= "\n\n" . sprintf( /* translators: %s: site name */ __( '— %s', 'swe-mail-list' ), get_bloginfo( 'name' ) );

	$sent = 0;
	foreach ( array_chunk( $recipients, swe_ml_batch_size() ) as $batch ) {
		$headers = array( 'Bcc: ' . implode( ', ', $batch ) );
		// To: goes to the site inbox; every real recipient is in BCC.
		if ( wp_mail( get_option( 'admin_email' ), $subject, $body, $headers ) ) {
			$sent += count( $batch );
		}
	}

	return $sent;
}
