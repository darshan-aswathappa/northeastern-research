<?php
/**
 * Applicant notification emails.
 *
 * Two applicant-facing messages: a confirmation when an application is
 * received, and a decision email when an application is rejected. Both are
 * plain-text (matching the office notification) and are sent from the site's
 * name so replies reach the program office.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Human-readable program name used in subjects and signatures.
 *
 * @return string
 */
function swe_app_program_name() {
	$name = get_bloginfo( 'name' );
	return $name ? $name : __( 'WordPress Research Fellows Program', 'swe-fellows-application' );
}

/**
 * Standard headers so applicant email comes from the program, and replies go
 * to the program office rather than the default wordpress@ address.
 *
 * @return string[]
 */
function swe_app_email_headers() {
	$office = get_option( 'admin_email' );
	$domain = wp_parse_url( home_url(), PHP_URL_HOST );
	$domain = $domain ? preg_replace( '/^www\./', '', $domain ) : 'localhost';

	return array(
		sprintf( 'From: %s <no-reply@%s>', swe_app_program_name(), $domain ),
		sprintf( 'Reply-To: %s', $office ),
		'Content-Type: text/plain; charset=UTF-8',
	);
}

/**
 * Confirmation email sent to the applicant on submission.
 *
 * @param array $data Validated application data (name, email, class_year, track, coursework).
 * @return bool Whether wp_mail accepted the message for delivery.
 */
function swe_app_send_received_email( array $data ) {
	if ( empty( $data['email'] ) || ! is_email( $data['email'] ) ) {
		return false;
	}

	$program = swe_app_program_name();

	$subject = sprintf(
		/* translators: %s: program name */
		__( 'Application received — %s', 'swe-fellows-application' ),
		$program
	);

	$lines = array(
		sprintf( /* translators: %s: applicant name */ __( 'Hi %s,', 'swe-fellows-application' ), $data['name'] ),
		'',
		sprintf(
			/* translators: 1: program name, 2: track */
			__( 'Thanks for applying to the %1$s. We\'ve received your application%2$s and it is now under review.', 'swe-fellows-application' ),
			$program,
			! empty( $data['track'] ) ? sprintf( __( ' for the %s track', 'swe-fellows-application' ), $data['track'] ) : ''
		),
		'',
		__( 'Here is a copy of what you submitted:', 'swe-fellows-application' ),
	);

	if ( ! empty( $data['class_year'] ) ) {
		$lines[] = sprintf( __( 'Class year: %s', 'swe-fellows-application' ), $data['class_year'] );
	}
	if ( ! empty( $data['track'] ) ) {
		$lines[] = sprintf( __( 'Track: %s', 'swe-fellows-application' ), $data['track'] );
	}
	if ( ! empty( $data['coursework'] ) ) {
		$lines[] = sprintf( __( 'Relevant coursework: %s', 'swe-fellows-application' ), $data['coursework'] );
	}

	$lines[] = '';
	$lines[] = __( 'We will be in touch after the review period. If you need to update anything, just reply to this email.', 'swe-fellows-application' );
	$lines[] = '';
	$lines[] = sprintf( /* translators: %s: program name */ __( '— The %s team', 'swe-fellows-application' ), $program );

	return wp_mail( $data['email'], $subject, implode( "\n", $lines ), swe_app_email_headers() );
}

/**
 * Decision email sent to the applicant when their application is rejected.
 *
 * @param object $row Application row (needs name, email).
 * @return bool Whether wp_mail accepted the message for delivery.
 */
function swe_app_send_rejection_email( $row ) {
	if ( empty( $row->email ) || ! is_email( $row->email ) ) {
		return false;
	}

	$program = swe_app_program_name();

	$subject = sprintf(
		/* translators: %s: program name */
		__( 'Update on your %s application', 'swe-fellows-application' ),
		$program
	);

	$lines = array(
		sprintf( /* translators: %s: applicant name */ __( 'Hi %s,', 'swe-fellows-application' ), $row->name ),
		'',
		sprintf(
			/* translators: %s: program name */
			__( 'Thank you for applying to the %s and for the time you put into your application.', 'swe-fellows-application' ),
			$program
		),
		'',
		__( 'After careful review, we are not able to offer you a place in the program this cycle. This was a competitive round and the decision was a difficult one.', 'swe-fellows-application' ),
		'',
		__( 'We would genuinely encourage you to apply again in a future cycle, and we wish you the very best with your studies and projects.', 'swe-fellows-application' ),
		'',
		sprintf( /* translators: %s: program name */ __( '— The %s team', 'swe-fellows-application' ), $program ),
	);

	return wp_mail( $row->email, $subject, implode( "\n", $lines ), swe_app_email_headers() );
}
