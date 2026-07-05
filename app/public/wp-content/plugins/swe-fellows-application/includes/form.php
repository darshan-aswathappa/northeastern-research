<?php
/**
 * The [swe_fellows_application] shortcode and the no-JS submission path.
 *
 * Progressive enhancement contract:
 * - Without JS this is one ordinary <form> posting to admin-post.php; every
 *   field is visible, native validation applies, and the visitor is
 *   redirected back with a status message.
 * - With JS (assets/form.js) the fieldsets become a 3-step flow with a
 *   progress indicator, per-step validation, focus management, and a
 *   fetch() submit to the REST endpoint.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Human message for a redirect error code (no-JS path).
 *
 * @param string $code Error code.
 * @return string
 */
function swe_app_error_message( $code ) {
	$messages = array(
		'swe_app_invalid_name'         => __( 'Please enter your full name.', 'swe-fellows-application' ),
		'swe_app_invalid_email'        => __( 'Please enter a valid email address.', 'swe-fellows-application' ),
		'swe_app_invalid_year'         => __( 'Please select your class year.', 'swe-fellows-application' ),
		'swe_app_invalid_track'        => __( 'Please select a preferred track.', 'swe-fellows-application' ),
		'swe_app_invalid_statement'    => __( 'Please include a statement of interest.', 'swe-fellows-application' ),
		'swe_app_statement_too_long'   => sprintf( /* translators: %d: word limit */ __( 'The statement of interest must be %d words or fewer.', 'swe-fellows-application' ), swe_app_statement_word_limit() ),
		'swe_app_resume_missing'       => __( 'Please attach your resume as a PDF.', 'swe-fellows-application' ),
		'swe_app_resume_type'          => __( 'Your resume must be a PDF file.', 'swe-fellows-application' ),
		'swe_app_resume_too_large'     => sprintf( /* translators: %d: max size in MB */ __( 'Your resume must be %d MB or smaller.', 'swe-fellows-application' ), (int) ( swe_app_resume_max_bytes() / MB_IN_BYTES ) ),
		'swe_app_resume_upload_failed' => __( 'Your resume could not be uploaded — please try again.', 'swe-fellows-application' ),
		'swe_app_rate_limited'         => __( 'Too many submissions from this connection — please wait a few minutes and try again.', 'swe-fellows-application' ),
		'swe_app_bad_nonce'            => __( 'Your session expired — please reload the page and try again.', 'swe-fellows-application' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Something went wrong — please check your answers and try again.', 'swe-fellows-application' );
}

/**
 * Render the application form.
 *
 * @return string
 */
function swe_app_shortcode() {
	wp_enqueue_style( 'swe-app-style', SWE_APP_URL . 'assets/style.css', array(), SWE_APP_VERSION );
	wp_enqueue_script(
		'swe-app-script',
		SWE_APP_URL . 'assets/form.js',
		array(),
		SWE_APP_VERSION,
		array(
			'in_footer' => true,
			'strategy'  => 'defer',
		)
	);
	wp_localize_script(
		'swe-app-script',
		'sweAppConfig',
		array(
			'restUrl' => esc_url_raw( rest_url( 'swe-app/v1/submit' ) ),
			'nonce'   => wp_create_nonce( 'wp_rest' ),
			'i18n'    => array(
				'stepAnnouncement' => __( 'Step %1$s of %2$s: %3$s', 'swe-fellows-application' ),
				'submitting'       => __( 'Submitting…', 'swe-fellows-application' ),
				'submitLabel'      => __( 'Submit application', 'swe-fellows-application' ),
				'networkError'     => __( 'Something went wrong submitting your application — please try again.', 'swe-fellows-application' ),
				'resumeType'       => __( 'Your resume must be a PDF file.', 'swe-fellows-application' ),
				'resumeTooLarge'   => sprintf( /* translators: %d: max size in MB */ __( 'Your resume must be %d MB or smaller.', 'swe-fellows-application' ), (int) ( swe_app_resume_max_bytes() / MB_IN_BYTES ) ),
			),
		)
	);

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only status display.
	$status     = isset( $_GET['swe_app_status'] ) ? sanitize_key( $_GET['swe_app_status'] ) : '';
	$error_code = isset( $_GET['swe_app_code'] ) ? sanitize_key( $_GET['swe_app_code'] ) : '';
	// phpcs:enable

	ob_start();

	if ( 'received' === $status ) {
		?>
		<div class="swe-app swe-app-success" id="swe-app" role="status">
			<h3><?php esc_html_e( 'Application received', 'swe-fellows-application' ); ?></h3>
			<p><?php esc_html_e( 'Thanks for applying — you’ll hear back by the notification date listed in the key deadlines above.', 'swe-fellows-application' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
	?>
	<div class="swe-app" id="swe-app" data-swe-app>

		<?php if ( 'error' === $status ) : ?>
			<div class="swe-app-errors" role="alert" tabindex="-1">
				<p><?php echo esc_html( swe_app_error_message( $error_code ) ); ?></p>
			</div>
		<?php else : ?>
			<div class="swe-app-errors" role="alert" tabindex="-1" hidden></div>
		<?php endif; ?>

		<p class="swe-app-live screen-reader-text" aria-live="polite"></p>

		<ol class="swe-app-progress" hidden data-progress>
			<li data-progress-step="1"><?php esc_html_e( 'Personal info', 'swe-fellows-application' ); ?></li>
			<li data-progress-step="2"><?php esc_html_e( 'Program details', 'swe-fellows-application' ); ?></li>
			<li data-progress-step="3"><?php esc_html_e( 'Statement of interest', 'swe-fellows-application' ); ?></li>
		</ol>

		<form class="swe-app-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" enctype="multipart/form-data">
			<input type="hidden" name="action" value="swe_app_submit">
			<?php wp_nonce_field( 'swe_app_submit', 'swe_app_nonce' ); ?>

			<p class="swe-app-hp" aria-hidden="true">
				<input type="text" name="swe_website" tabindex="-1" autocomplete="off" aria-hidden="true">
			</p>

			<fieldset class="swe-app-step" data-step="1">
				<legend tabindex="-1"><span class="swe-app-step-count"><?php esc_html_e( 'Step 1 of 3', 'swe-fellows-application' ); ?></span> <?php esc_html_e( 'Personal info', 'swe-fellows-application' ); ?></legend>

				<p class="swe-app-field">
					<label for="swe-name"><?php esc_html_e( 'Full name', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<input type="text" id="swe-name" name="name" required autocomplete="name" maxlength="190">
				</p>

				<p class="swe-app-field">
					<label for="swe-email"><?php esc_html_e( 'Email address', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<input type="email" id="swe-email" name="email" required autocomplete="email" maxlength="190">
				</p>

				<p class="swe-app-field">
					<label for="swe-year"><?php esc_html_e( 'Class year', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<select id="swe-year" name="class_year" required>
						<option value=""><?php esc_html_e( 'Select one', 'swe-fellows-application' ); ?></option>
						<?php foreach ( swe_app_class_years() as $swe_year ) : ?>
							<option value="<?php echo esc_attr( $swe_year ); ?>"><?php echo esc_html( $swe_year ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="swe-app-nav" hidden data-nav>
					<button type="button" class="swe-app-btn swe-app-next"><?php esc_html_e( 'Continue', 'swe-fellows-application' ); ?></button>
				</p>
			</fieldset>

			<fieldset class="swe-app-step" data-step="2">
				<legend tabindex="-1"><span class="swe-app-step-count"><?php esc_html_e( 'Step 2 of 3', 'swe-fellows-application' ); ?></span> <?php esc_html_e( 'Program details', 'swe-fellows-application' ); ?></legend>

				<p class="swe-app-field">
					<label for="swe-track"><?php esc_html_e( 'Preferred track', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<select id="swe-track" name="track" required>
						<option value=""><?php esc_html_e( 'Select one', 'swe-fellows-application' ); ?></option>
						<?php foreach ( swe_app_tracks() as $swe_track ) : ?>
							<option value="<?php echo esc_attr( $swe_track ); ?>"><?php echo esc_html( $swe_track ); ?></option>
						<?php endforeach; ?>
					</select>
				</p>

				<p class="swe-app-field">
					<label for="swe-coursework"><?php esc_html_e( 'Relevant coursework', 'swe-fellows-application' ); ?></label>
					<input type="text" id="swe-coursework" name="coursework" autocomplete="off" maxlength="255" placeholder="<?php esc_attr_e( 'e.g. Databases, Web Development', 'swe-fellows-application' ); ?>">
				</p>

				<p class="swe-app-nav" hidden data-nav>
					<button type="button" class="swe-app-btn swe-app-btn-secondary swe-app-back"><?php esc_html_e( 'Back', 'swe-fellows-application' ); ?></button>
					<button type="button" class="swe-app-btn swe-app-next"><?php esc_html_e( 'Continue', 'swe-fellows-application' ); ?></button>
				</p>
			</fieldset>

			<fieldset class="swe-app-step" data-step="3">
				<legend tabindex="-1"><span class="swe-app-step-count"><?php esc_html_e( 'Step 3 of 3', 'swe-fellows-application' ); ?></span> <?php esc_html_e( 'Statement of interest', 'swe-fellows-application' ); ?></legend>

				<p class="swe-app-field">
					<label for="swe-statement"><?php esc_html_e( 'Statement of interest', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<textarea id="swe-statement" name="statement" rows="7" required aria-describedby="swe-statement-hint"></textarea>
					<span class="swe-app-hint" id="swe-statement-hint"><?php esc_html_e( '500 words max — why do you want to spend a summer building WordPress tooling?', 'swe-fellows-application' ); ?></span>
				</p>

				<p class="swe-app-field">
					<label for="swe-resume"><?php esc_html_e( 'Resume (PDF)', 'swe-fellows-application' ); ?> <span class="swe-app-req" aria-hidden="true">*</span></label>
					<input type="file" id="swe-resume" name="resume" required accept="application/pdf,.pdf" aria-describedby="swe-resume-hint">
					<span class="swe-app-hint" id="swe-resume-hint"><?php esc_html_e( 'PDF only, 5 MB max.', 'swe-fellows-application' ); ?></span>
				</p>

				<p class="swe-app-nav swe-app-nav-final">
					<button type="button" class="swe-app-btn swe-app-btn-secondary swe-app-back" hidden data-nav-back><?php esc_html_e( 'Back', 'swe-fellows-application' ); ?></button>
					<button type="submit" class="swe-app-btn swe-app-submit"><?php esc_html_e( 'Submit application', 'swe-fellows-application' ); ?></button>
				</p>
			</fieldset>
		</form>

		<div class="swe-app-success" hidden tabindex="-1" data-success>
			<h3><?php esc_html_e( 'Application received', 'swe-fellows-application' ); ?></h3>
			<p><?php esc_html_e( 'Thanks for applying — you’ll hear back by the notification date listed in the key deadlines above.', 'swe-fellows-application' ); ?></p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'swe_fellows_application', 'swe_app_shortcode' );

/**
 * No-JS submission handler (admin-post.php).
 */
function swe_app_handle_post() {
	$redirect = wp_get_referer() ? remove_query_arg( array( 'swe_app_status', 'swe_app_code' ), wp_get_referer() ) : home_url( '/' );

	$fail = function ( $code ) use ( $redirect ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'swe_app_status' => 'error',
					'swe_app_code'   => $code,
				),
				$redirect
			) . '#swe-app'
		);
		exit;
	};

	if ( ! isset( $_POST['swe_app_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['swe_app_nonce'] ) ), 'swe_app_submit' ) ) {
		$fail( 'swe_app_bad_nonce' );
	}

	// Validate first (honeypot included) so bot noise and honest mistakes
	// never consume a visitor's rate-limit quota.
	$data = swe_app_validate( $_POST );
	if ( is_wp_error( $data ) ) {
		$fail( $data->get_error_code() );
	}

	// $_FILES is not slashed, so it's passed through untouched.
	$resume       = isset( $_FILES['resume'] ) ? $_FILES['resume'] : null; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput
	$resume_valid = swe_app_validate_resume( $resume );
	if ( is_wp_error( $resume_valid ) ) {
		$fail( $resume_valid->get_error_code() );
	}

	$limited = swe_app_check_rate_limit();
	if ( is_wp_error( $limited ) ) {
		$fail( $limited->get_error_code() );
	}

	// Persist the file only after every check has passed.
	$resume_path = swe_app_store_resume( $resume );
	if ( is_wp_error( $resume_path ) ) {
		$fail( $resume_path->get_error_code() );
	}
	$data['resume_path'] = $resume_path;

	$result = swe_app_insert( $data );
	if ( is_wp_error( $result ) ) {
		// Don't leave an orphaned file behind if the row never saved.
		swe_app_delete_resume_file( $resume_path );
		$fail( $result->get_error_code() );
	}

	wp_safe_redirect( add_query_arg( 'swe_app_status', 'received', $redirect ) . '#swe-app' );
	exit;
}
add_action( 'admin_post_nopriv_swe_app_submit', 'swe_app_handle_post' );
add_action( 'admin_post_swe_app_submit', 'swe_app_handle_post' );
