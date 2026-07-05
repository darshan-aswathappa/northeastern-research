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

/**
 * Maximum accepted resume size, in bytes.
 *
 * @return int
 */
function swe_app_resume_max_bytes() {
	return 5 * MB_IN_BYTES;
}

/**
 * Absolute path to the dedicated resume upload directory.
 *
 * Lazily creates the directory and drops an index.html so the folder can't be
 * browsed as a listing. Resumes live in their own subdirectory of uploads/
 * rather than the date-based default so they're easy to isolate and purge.
 *
 * @return string
 */
function swe_app_resume_dir() {
	$uploads = wp_upload_dir();
	$dir     = trailingslashit( $uploads['basedir'] ) . 'swe-resumes';

	if ( ! is_dir( $dir ) ) {
		wp_mkdir_p( $dir );
	}

	$index = trailingslashit( $dir ) . 'index.html';
	if ( ! file_exists( $index ) ) {
		// Silent, best-effort: an unwritable dir will surface later at upload time.
		@file_put_contents( $index, '' ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged, WordPress.WP.AlternativeFunctions.file_system_operations_file_put_contents
	}

	return $dir;
}

/**
 * Validate an uploaded resume before anything is persisted.
 *
 * Checks presence, upload error state, size cap, and — crucially — that the
 * file is genuinely a PDF (content-sniffed via wp_check_filetype_and_ext plus
 * a magic-byte check), not merely named ".pdf".
 *
 * @param array|null $file One entry from $_FILES (or $request->get_file_params()).
 * @return true|WP_Error
 */
function swe_app_validate_resume( $file ) {
	$missing = new WP_Error(
		'swe_app_resume_missing',
		__( 'Please attach your resume as a PDF.', 'swe-fellows-application' ),
		array(
			'status' => 400,
			'field'  => 'resume',
		)
	);

	if ( ! is_array( $file ) || empty( $file['tmp_name'] ) || ! isset( $file['error'] ) ) {
		return $missing;
	}

	// Map PHP upload errors: an oversized file often arrives as INI_SIZE/FORM_SIZE.
	if ( UPLOAD_ERR_OK !== (int) $file['error'] ) {
		if ( in_array( (int) $file['error'], array( UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE ), true ) ) {
			return swe_app_resume_too_large_error();
		}
		return $missing;
	}

	if ( ! is_uploaded_file( $file['tmp_name'] ) ) {
		return $missing;
	}

	$size = isset( $file['size'] ) ? (int) $file['size'] : 0;
	if ( $size <= 0 ) {
		return $missing;
	}
	if ( $size > swe_app_resume_max_bytes() ) {
		return swe_app_resume_too_large_error();
	}

	$type_error = new WP_Error(
		'swe_app_resume_type',
		__( 'Your resume must be a PDF file.', 'swe-fellows-application' ),
		array(
			'status' => 400,
			'field'  => 'resume',
		)
	);

	// Extension + MIME sniffing (uses fileinfo when available).
	$check = wp_check_filetype_and_ext(
		$file['tmp_name'],
		isset( $file['name'] ) ? $file['name'] : '',
		array( 'pdf' => 'application/pdf' )
	);
	if ( 'application/pdf' !== $check['type'] || 'pdf' !== $check['ext'] ) {
		return $type_error;
	}

	// Belt-and-braces: confirm the PDF magic bytes regardless of fileinfo.
	$handle = fopen( $file['tmp_name'], 'rb' ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fopen
	if ( $handle ) {
		$signature = fread( $handle, 5 ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fread
		fclose( $handle ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_fclose
		if ( '%PDF-' !== $signature ) {
			return $type_error;
		}
	}

	return true;
}

/**
 * The shared "too large" error (keeps the message in one place).
 *
 * @return WP_Error
 */
function swe_app_resume_too_large_error() {
	return new WP_Error(
		'swe_app_resume_too_large',
		sprintf(
			/* translators: %d: max size in MB */
			__( 'Your resume must be %d MB or smaller.', 'swe-fellows-application' ),
			(int) ( swe_app_resume_max_bytes() / MB_IN_BYTES )
		),
		array(
			'status' => 400,
			'field'  => 'resume',
		)
	);
}

/**
 * Move a validated upload into the resume directory under a random name.
 *
 * Must run only after swe_app_validate_resume() succeeds. Returns the path
 * relative to the uploads base (e.g. "swe-resumes/ab12…cd.pdf"), which is what
 * gets stored in the database.
 *
 * @param array $file One entry from $_FILES.
 * @return string|WP_Error Relative path on success.
 */
function swe_app_store_resume( array $file ) {
	require_once ABSPATH . 'wp-admin/includes/file.php';

	// Ensure the directory (and its index.html guard) exist before wp_handle_upload runs.
	swe_app_resume_dir();

	// Redirect the upload into swe-resumes/ with an unguessable filename.
	$to_resumes = function ( $dirs ) {
		$dirs['subdir'] = '/swe-resumes';
		$dirs['path']   = $dirs['basedir'] . '/swe-resumes';
		$dirs['url']    = $dirs['baseurl'] . '/swe-resumes';
		return $dirs;
	};
	$random_name = function () {
		return bin2hex( random_bytes( 16 ) ) . '.pdf';
	};

	add_filter( 'upload_dir', $to_resumes );

	$moved = wp_handle_upload(
		$file,
		array(
			'test_form'                => false,
			'mimes'                    => array( 'pdf' => 'application/pdf' ),
			'unique_filename_callback' => $random_name,
		)
	);

	remove_filter( 'upload_dir', $to_resumes );

	if ( isset( $moved['error'] ) || empty( $moved['file'] ) ) {
		$reason = isset( $moved['error'] ) ? $moved['error'] : __( 'Upload failed.', 'swe-fellows-application' );
		return new WP_Error(
			'swe_app_resume_upload_failed',
			$reason,
			array(
				'status' => 500,
				'field'  => 'resume',
			)
		);
	}

	return 'swe-resumes/' . basename( $moved['file'] );
}

/**
 * Public URL for a stored resume.
 *
 * Single seam: if resumes ever move behind an authenticated download handler,
 * only this function changes.
 *
 * @param string $relative_path Path relative to the uploads base.
 * @return string
 */
function swe_app_resume_url( $relative_path ) {
	$uploads = wp_upload_dir();
	return trailingslashit( $uploads['baseurl'] ) . ltrim( $relative_path, '/' );
}

/**
 * Delete a stored resume file, guarding against paths outside the resume dir.
 *
 * @param string $relative_path Path relative to the uploads base (as stored).
 * @return void
 */
function swe_app_delete_resume_file( $relative_path ) {
	$uploads = wp_upload_dir();
	$target  = wp_normalize_path( trailingslashit( $uploads['basedir'] ) . ltrim( $relative_path, '/' ) );
	$dir     = wp_normalize_path( trailingslashit( swe_app_resume_dir() ) );

	// Only ever delete inside our own resume directory, even if the stored
	// value is somehow corrupt or maliciously crafted.
	if ( 0 !== strpos( $target, $dir ) ) {
		return;
	}

	if ( file_exists( $target ) ) {
		wp_delete_file( $target );
	}
}
