<?php
/**
 * Uninstall: remove all options written by this plugin.
 *
 * @package fellows-deadline
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'fellows_dl_open' );
delete_option( 'fellows_dl_close' );
delete_option( 'fellows_dl_notify' );
