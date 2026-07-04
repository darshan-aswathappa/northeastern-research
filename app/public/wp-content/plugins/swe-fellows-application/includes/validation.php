<?php
/**
 * Shared validation for both submission paths (REST and admin-post),
 * plus spam controls: honeypot and per-IP rate limiting.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Allowed class years.
 *
 * @return string[]
 */
function swe_app_class_years() {
	return array( 'First-year', 'Sophomore', 'Junior', 'Senior' );
}

/**
 * Allowed research tracks.
 *
 * @return string[]
 */
function swe_app_tracks() {
	return array( 'Plugin Architecture', 'Editor & Block UX', 'Performance & Security', 'Accessibility' );
}

/**
 * Maximum words allowed in the statement of interest.
 */
function swe_app_statement_word_limit() {
	return 500;
}

/**
 * Validate and sanitize raw submission input.
 *
 * @param array $raw Raw request params.
 * @return array|WP_Error Sanitized data keyed by column, or the first error.
 *                        WP_Error data carries 'status' and 'field'.
 */
function swe_app_validate( array $raw ) {
	// Honeypot: real users never fill this; bots usually do. Report a generic
	// success-shaped rejection so bots learn nothing.
	if ( ! empty( $raw['swe_website'] ) ) {
		return new WP_Error( 'swe_app_rejected', __( 'Submission rejected.', 'swe-fellows-application' ), array( 'status' => 400 ) );
	}

	$name       = isset( $raw['name'] ) ? sanitize_text_field( wp_unslash( $raw['name'] ) ) : '';
	$email      = isset( $raw['email'] ) ? sanitize_email( wp_unslash( $raw['email'] ) ) : '';
	$class_year = isset( $raw['class_year'] ) ? sanitize_text_field( wp_unslash( $raw['class_year'] ) ) : '';
	$track      = isset( $raw['track'] ) ? sanitize_text_field( wp_unslash( $raw['track'] ) ) : '';
	$coursework = isset( $raw['coursework'] ) ? sanitize_text_field( wp_unslash( $raw['coursework'] ) ) : '';
	$statement  = isset( $raw['statement'] ) ? sanitize_textarea_field( wp_unslash( $raw['statement'] ) ) : '';

	if ( '' === $name || mb_strlen( $name ) > 190 ) {
		return new WP_Error(
			'swe_app_invalid_name',
			__( 'Please enter your full name.', 'swe-fellows-application' ),
			array(
				'status' => 400,
				'field'  => 'name',
			)
		);
	}

	if ( ! is_email( $email ) ) {
		return new WP_Error(
			'swe_app_invalid_email',
			__( 'Please enter a valid email address.', 'swe-fellows-application' ),
			array(
				'status' => 400,
				'field'  => 'email',
			)
		);
	}

	if ( ! in_array( $class_year, swe_app_class_years(), true ) ) {
		return new WP_Error(
			'swe_app_invalid_year',
			__( 'Please select your class year.', 'swe-fellows-application' ),
			array(
				'status' => 400,
				'field'  => 'class_year',
			)
		);
	}

	if ( ! in_array( $track, swe_app_tracks(), true ) ) {
		return new WP_Error(
			'swe_app_invalid_track',
			__( 'Please select a preferred track.', 'swe-fellows-application' ),
			array(
				'status' => 400,
				'field'  => 'track',
			)
		);
	}

	if ( '' === trim( $statement ) ) {
		return new WP_Error(
			'swe_app_invalid_statement',
			__( 'Please include a statement of interest.', 'swe-fellows-application' ),
			array(
				'status' => 400,
				'field'  => 'statement',
			)
		);
	}

	$word_count = str_word_count( wp_strip_all_tags( $statement ) );
	if ( $word_count > swe_app_statement_word_limit() ) {
		return new WP_Error(
			'swe_app_statement_too_long',
			sprintf( /* translators: %d: word limit */ __( 'The statement of interest must be %d words or fewer.', 'swe-fellows-application' ), swe_app_statement_word_limit() ),
			array(
				'status' => 400,
				'field'  => 'statement',
			)
		);
	}

	return array(
		'name'       => $name,
		'email'      => $email,
		'class_year' => $class_year,
		'track'      => $track,
		'coursework' => $coursework,
		'statement'  => $statement,
	);
}

/**
 * Per-IP rate limit: 5 submissions per 10 minutes.
 *
 * @return true|WP_Error
 */
function swe_app_check_rate_limit() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

	$key   = 'swe_app_rl_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= 5 ) {
		return new WP_Error(
			'swe_app_rate_limited',
			__( 'Too many submissions from this connection — please wait a few minutes and try again.', 'swe-fellows-application' ),
			array( 'status' => 429 )
		);
	}

	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}
