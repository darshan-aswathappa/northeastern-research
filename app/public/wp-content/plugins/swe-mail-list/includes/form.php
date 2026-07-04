<?php
/**
 * The [swe_waitlist] shortcode and its no-JS submission path.
 *
 * One ordinary <form> posting to admin-post.php: native validation applies
 * and the visitor is redirected back to the form with a status message.
 * No JavaScript required.
 *
 * @package swe-mail-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Human message for a redirect error code.
 *
 * @param string $code Error code.
 * @return string
 */
function swe_ml_error_message( $code ) {
	$messages = array(
		'swe_ml_invalid_email' => __( 'Please enter a valid email address.', 'swe-mail-list' ),
		'swe_ml_duplicate'     => __( 'This email is already on the waitlist.', 'swe-mail-list' ),
		'swe_ml_rate_limited'  => __( 'Too many attempts from this connection — please wait a few minutes and try again.', 'swe-mail-list' ),
		'swe_ml_bad_nonce'     => __( 'Your session expired — please reload the page and try again.', 'swe-mail-list' ),
	);
	return isset( $messages[ $code ] ) ? $messages[ $code ] : __( 'Something went wrong — please try again.', 'swe-mail-list' );
}

/**
 * Render the waitlist signup form.
 *
 * @return string
 */
function swe_ml_shortcode() {
	wp_enqueue_style( 'swe-ml-style', SWE_ML_URL . 'assets/style.css', array(), SWE_ML_VERSION );

	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only status display.
	$status     = isset( $_GET['swe_ml_status'] ) ? sanitize_key( $_GET['swe_ml_status'] ) : '';
	$error_code = isset( $_GET['swe_ml_code'] ) ? sanitize_key( $_GET['swe_ml_code'] ) : '';
	// phpcs:enable

	ob_start();

	if ( 'joined' === $status ) {
		?>
		<div class="swe-ml swe-ml-success" id="swe-waitlist" role="status">
			<h3><?php esc_html_e( 'You’re on the waitlist', 'swe-mail-list' ); ?></h3>
			<p><?php esc_html_e( 'We’ll email you the moment applications open for the new intake.', 'swe-mail-list' ); ?></p>
		</div>
		<?php
		return ob_get_clean();
	}
	?>
	<div class="swe-ml" id="swe-waitlist">

		<?php if ( 'error' === $status ) : ?>
			<div class="swe-ml-errors" role="alert" tabindex="-1">
				<p><?php echo esc_html( swe_ml_error_message( $error_code ) ); ?></p>
			</div>
		<?php endif; ?>

		<form class="swe-ml-form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="swe_ml_subscribe">
			<?php wp_nonce_field( 'swe_ml_subscribe', 'swe_ml_nonce' ); ?>

			<p class="swe-ml-hp" aria-hidden="true">
				<input type="text" name="swe_ml_website" tabindex="-1" autocomplete="off" aria-hidden="true">
			</p>

			<div class="swe-ml-row">
				<label class="screen-reader-text" for="swe-ml-email"><?php esc_html_e( 'Email address', 'swe-mail-list' ); ?></label>
				<input type="email" id="swe-ml-email" name="email" required autocomplete="email" maxlength="190" placeholder="<?php esc_attr_e( 'you@university.edu', 'swe-mail-list' ); ?>">
				<button type="submit" class="swe-ml-btn"><?php esc_html_e( 'Join the waitlist', 'swe-mail-list' ); ?></button>
			</div>
			<p class="swe-ml-hint"><?php esc_html_e( 'One email when applications open — nothing else.', 'swe-mail-list' ); ?></p>
		</form>
	</div>
	<?php
	return ob_get_clean();
}
add_shortcode( 'swe_waitlist', 'swe_ml_shortcode' );

/**
 * Per-IP rate limit: 5 attempts per 10 minutes.
 *
 * @return true|WP_Error
 */
function swe_ml_check_rate_limit() {
	$ip = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : 'unknown';

	$key   = 'swe_ml_rl_' . md5( $ip );
	$count = (int) get_transient( $key );

	if ( $count >= 5 ) {
		return new WP_Error( 'swe_ml_rate_limited', __( 'Too many attempts from this connection — please wait a few minutes and try again.', 'swe-mail-list' ), array( 'status' => 429 ) );
	}

	set_transient( $key, $count + 1, 10 * MINUTE_IN_SECONDS );
	return true;
}

/**
 * Submission handler (admin-post.php).
 */
function swe_ml_handle_post() {
	$redirect = wp_get_referer() ? remove_query_arg( array( 'swe_ml_status', 'swe_ml_code' ), wp_get_referer() ) : home_url( '/' );

	$fail = function ( $code ) use ( $redirect ) {
		wp_safe_redirect(
			add_query_arg(
				array(
					'swe_ml_status' => 'error',
					'swe_ml_code'   => $code,
				),
				$redirect
			) . '#swe-waitlist'
		);
		exit;
	};

	if ( ! isset( $_POST['swe_ml_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['swe_ml_nonce'] ) ), 'swe_ml_subscribe' ) ) {
		$fail( 'swe_ml_bad_nonce' );
	}

	// Honeypot: silently pretend success so bots learn nothing.
	if ( ! empty( $_POST['swe_ml_website'] ) ) {
		wp_safe_redirect( add_query_arg( 'swe_ml_status', 'joined', $redirect ) . '#swe-waitlist' );
		exit;
	}

	$email = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	if ( '' === $email || ! is_email( $email ) ) {
		$fail( 'swe_ml_invalid_email' );
	}

	$limited = swe_ml_check_rate_limit();
	if ( is_wp_error( $limited ) ) {
		$fail( $limited->get_error_code() );
	}

	$result = swe_ml_subscribe( $email );
	if ( is_wp_error( $result ) ) {
		$fail( $result->get_error_code() );
	}

	wp_safe_redirect( add_query_arg( 'swe_ml_status', 'joined', $redirect ) . '#swe-waitlist' );
	exit;
}
add_action( 'admin_post_nopriv_swe_ml_subscribe', 'swe_ml_handle_post' );
add_action( 'admin_post_swe_ml_subscribe', 'swe_ml_handle_post' );
