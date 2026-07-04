<?php
/**
 * REST endpoint the enhanced (JS) form posts to.
 *
 * Public form, but not an open door: requests must carry a valid wp_rest
 * nonce (issued by the page that rendered the form), pass the honeypot,
 * a per-IP rate limit, and full server-side validation.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the submit route.
 */
function swe_app_register_route() {
	register_rest_route(
		'swe-app/v1',
		'/submit',
		array(
			'methods'             => WP_REST_Server::CREATABLE,
			'callback'            => 'swe_app_handle_rest_submit',
			'permission_callback' => 'swe_app_rest_permission',
			'args'                => array(
				'name'       => array(
					'type'     => 'string',
					'required' => true,
				),
				'email'      => array(
					'type'     => 'string',
					'required' => true,
				),
				'class_year' => array(
					'type'     => 'string',
					'required' => true,
				),
				'track'      => array(
					'type'     => 'string',
					'required' => true,
				),
				'coursework' => array(
					'type'     => 'string',
					'required' => false,
					'default'  => '',
				),
				'statement'  => array(
					'type'     => 'string',
					'required' => true,
				),
			),
		)
	);
}
add_action( 'rest_api_init', 'swe_app_register_route' );

/**
 * Require the wp_rest nonce issued to the page that rendered the form.
 *
 * @param WP_REST_Request $request Current request.
 * @return true|WP_Error
 */
function swe_app_rest_permission( WP_REST_Request $request ) {
	$nonce = $request->get_header( 'X-WP-Nonce' );

	if ( ! $nonce || ! wp_verify_nonce( $nonce, 'wp_rest' ) ) {
		return new WP_Error(
			'swe_app_bad_nonce',
			__( 'Your session expired — please reload the page and try again.', 'swe-fellows-application' ),
			array( 'status' => 403 )
		);
	}

	return true;
}

/**
 * Validate, rate-limit, store, notify.
 *
 * @param WP_REST_Request $request Current request.
 * @return array|WP_Error
 */
function swe_app_handle_rest_submit( WP_REST_Request $request ) {
	// Validate first (the honeypot check lives here) so bot noise and honest
	// mistakes never consume a visitor's rate-limit quota.
	$data = swe_app_validate( $request->get_params() );
	if ( is_wp_error( $data ) ) {
		return $data;
	}

	$limited = swe_app_check_rate_limit();
	if ( is_wp_error( $limited ) ) {
		return $limited;
	}

	$id = swe_app_insert( $data );
	if ( is_wp_error( $id ) ) {
		return $id;
	}

	return array(
		'success' => true,
		'id'      => $id,
	);
}
