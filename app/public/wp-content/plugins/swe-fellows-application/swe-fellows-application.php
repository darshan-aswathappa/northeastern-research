<?php
/**
 * Plugin Name: SWE Fellows Multi-Step Application
 * Description: Multi-step application form for the WordPress Research Fellows Program. Renders via [swe_fellows_application], stores submissions in a dedicated database table (wp_swe_applications), and provides an Applications admin screen. Works without JavaScript via a standard form post; JavaScript progressively enhances it into an accessible 3-step flow submitting over the REST API.
 * Version: 2.0.0
 * Author: Summer Research Program Web Team
 * License: GPL v2 or later
 * Requires at least: 6.3
 * Requires PHP: 8.0
 * Text Domain: swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SWE_APP_VERSION', '2.0.0' );
define( 'SWE_APP_DB_VERSION', '1' ); // String: get_option() returns strings, and the upgrade check compares strictly.
define( 'SWE_APP_PATH', plugin_dir_path( __FILE__ ) );
define( 'SWE_APP_URL', plugin_dir_url( __FILE__ ) );

require_once SWE_APP_PATH . 'includes/db.php';
require_once SWE_APP_PATH . 'includes/validation.php';
require_once SWE_APP_PATH . 'includes/form.php';
require_once SWE_APP_PATH . 'includes/rest.php';

if ( is_admin() ) {
	require_once SWE_APP_PATH . 'includes/admin.php';
}

register_activation_hook( __FILE__, 'swe_app_install' );

/**
 * Upgrade-safe schema check (covers updates where the activation hook
 * doesn't re-fire).
 */
function swe_app_maybe_upgrade() {
	if ( get_option( 'swe_app_db_version' ) !== SWE_APP_DB_VERSION ) {
		swe_app_install();
	}
}
add_action( 'plugins_loaded', 'swe_app_maybe_upgrade' );
