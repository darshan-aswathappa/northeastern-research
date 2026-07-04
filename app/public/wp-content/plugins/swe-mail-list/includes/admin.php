<?php
/**
 * "Mail List" admin screen: intake open/closed toggle, announcement
 * composer (bulk send to every subscriber), and a WP_List_Table of
 * subscribers with row + bulk delete.
 *
 * @package swe-mail-list
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the top-level menu.
 */
function swe_ml_admin_menu() {
	add_menu_page(
		__( 'Mail List', 'swe-mail-list' ),
		__( 'Mail List', 'swe-mail-list' ),
		'manage_options',
		'swe-mail-list',
		'swe_ml_render_admin_page',
		'dashicons-email-alt',
		27
	);
}
add_action( 'admin_menu', 'swe_ml_admin_menu' );

/**
 * Handle toggle, send, and delete actions before any output.
 */
function swe_ml_admin_actions() {
	if ( ! isset( $_REQUEST['page'] ) || 'swe-mail-list' !== $_REQUEST['page'] || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$base = admin_url( 'admin.php?page=swe-mail-list' );

	// Delete one subscriber (link with nonce).
	if ( isset( $_GET['action'], $_GET['id'] ) && 'delete' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$id = (int) $_GET['id']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'swe_ml_delete_' . $id );
		swe_ml_delete( $id );
		wp_safe_redirect( add_query_arg( 'swe_ml_notice', 'deleted', $base ) );
		exit;
	}

	// Bulk delete from the list table.
	$bulk = isset( $_REQUEST['action'] ) && '-1' !== $_REQUEST['action'] ? sanitize_key( wp_unslash( $_REQUEST['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( '' === $bulk && isset( $_REQUEST['action2'] ) && '-1' !== $_REQUEST['action2'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$bulk = sanitize_key( wp_unslash( $_REQUEST['action2'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	if ( 'bulk-delete' === $bulk && isset( $_REQUEST['ids'] ) ) {
		check_admin_referer( 'bulk-subscribers' );
		$ids = array_map( 'intval', (array) wp_unslash( $_REQUEST['ids'] ) );
		foreach ( $ids as $id ) {
			swe_ml_delete( $id );
		}
		wp_safe_redirect( add_query_arg( 'swe_ml_notice', 'deleted', $base ) );
		exit;
	}

	// Intake open/closed toggle.
	if ( isset( $_POST['swe_ml_save_intake'] ) ) {
		check_admin_referer( 'swe_ml_intake' );
		update_option( 'swe_ml_intake_open', empty( $_POST['intake_open'] ) ? '0' : '1' );
		wp_safe_redirect( add_query_arg( 'swe_ml_notice', 'intake_saved', $base ) );
		exit;
	}

	// Send announcement to all subscribers.
	if ( isset( $_POST['swe_ml_send'] ) ) {
		check_admin_referer( 'swe_ml_send' );

		$subject = isset( $_POST['subject'] ) ? sanitize_text_field( wp_unslash( $_POST['subject'] ) ) : '';
		$message = isset( $_POST['message'] ) ? sanitize_textarea_field( wp_unslash( $_POST['message'] ) ) : '';
		$link    = isset( $_POST['link'] ) ? esc_url_raw( wp_unslash( $_POST['link'] ) ) : '';

		if ( '' === $subject || '' === $message ) {
			wp_safe_redirect( add_query_arg( 'swe_ml_notice', 'send_missing', $base ) );
			exit;
		}

		$sent = swe_ml_send_announcement( swe_ml_get_emails(), $subject, $message, $link );

		wp_safe_redirect(
			add_query_arg(
				array(
					'swe_ml_notice' => 'sent',
					'swe_ml_sent'   => $sent,
				),
				$base
			)
		);
		exit;
	}
}
add_action( 'admin_init', 'swe_ml_admin_actions' );

/**
 * Admin notices for the current request.
 */
function swe_ml_admin_notices() {
	// phpcs:disable WordPress.Security.NonceVerification.Recommended -- read-only notice display.
	$notice = isset( $_GET['swe_ml_notice'] ) ? sanitize_key( $_GET['swe_ml_notice'] ) : '';
	$sent   = isset( $_GET['swe_ml_sent'] ) ? (int) $_GET['swe_ml_sent'] : 0;
	// phpcs:enable

	if ( 'deleted' === $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Subscriber removed.', 'swe-mail-list' ) . '</p></div>';
	} elseif ( 'intake_saved' === $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Intake status saved.', 'swe-mail-list' ) . '</p></div>';
	} elseif ( 'send_missing' === $notice ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'A subject and message are both required to send.', 'swe-mail-list' ) . '</p></div>';
	} elseif ( 'sent' === $notice ) {
		if ( $sent > 0 ) {
			/* translators: %d: number of subscribers emailed */
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html( sprintf( _n( 'Announcement sent to %d subscriber.', 'Announcement sent to %d subscribers.', $sent, 'swe-mail-list' ), $sent ) ) . '</p></div>';
		} else {
			echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Nothing was sent — the waitlist is empty or the mailer failed. Check your mail configuration.', 'swe-mail-list' ) . '</p></div>';
		}
	}
}

/**
 * Render the Mail List screen.
 */
function swe_ml_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view the mail list.', 'swe-mail-list' ) );
	}

	swe_ml_admin_notices();

	$count       = swe_ml_count();
	$intake_open = swe_ml_intake_is_open();

	$table = new SWE_ML_List_Table();
	$table->prepare_items();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Mail List', 'swe-mail-list' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Waitlist signups from the Apply page, stored in the wp_swe_waitlist table.', 'swe-mail-list' ); ?></p>

		<div class="card" style="max-width:760px;">
			<h2><?php esc_html_e( 'Application intake', 'swe-mail-list' ); ?></h2>
			<p>
				<?php
				echo $intake_open
					? esc_html__( 'Intake is OPEN — the Apply page shows the application form.', 'swe-mail-list' )
					: esc_html__( 'Intake is CLOSED — the Apply page shows the waitlist signup instead of the form.', 'swe-mail-list' );
				?>
			</p>
			<form method="post">
				<?php wp_nonce_field( 'swe_ml_intake' ); ?>
				<label>
					<input type="checkbox" name="intake_open" value="1" <?php checked( $intake_open ); ?>>
					<?php esc_html_e( 'Application intake is open', 'swe-mail-list' ); ?>
				</label>
				<p><button type="submit" name="swe_ml_save_intake" value="1" class="button button-primary"><?php esc_html_e( 'Save', 'swe-mail-list' ); ?></button></p>
			</form>
		</div>

		<div class="card" style="max-width:760px;">
			<h2><?php esc_html_e( 'Send announcement', 'swe-mail-list' ); ?></h2>
			<p>
				<?php
				/* translators: %d: subscriber count */
				echo esc_html( sprintf( _n( 'Goes to all %d subscriber on the waitlist (BCC, in batches).', 'Goes to all %d subscribers on the waitlist (BCC, in batches).', $count, 'swe-mail-list' ), $count ) );
				?>
			</p>
			<form method="post">
				<?php wp_nonce_field( 'swe_ml_send' ); ?>
				<table class="form-table" role="presentation">
					<tr>
						<th scope="row"><label for="swe-ml-subject"><?php esc_html_e( 'Subject', 'swe-mail-list' ); ?></label></th>
						<td><input type="text" id="swe-ml-subject" name="subject" class="regular-text" required maxlength="190" value="<?php esc_attr_e( 'Applications are now open — WordPress Research Fellows', 'swe-mail-list' ); ?>"></td>
					</tr>
					<tr>
						<th scope="row"><label for="swe-ml-message"><?php esc_html_e( 'Message', 'swe-mail-list' ); ?></label></th>
						<td><textarea id="swe-ml-message" name="message" class="large-text" rows="6" required><?php esc_html_e( "Hi,\n\nGood news — the application intake for the WordPress Research Fellows Program is now open. Applications are reviewed on a rolling basis, so we encourage you to apply early.", 'swe-mail-list' ); ?></textarea></td>
					</tr>
					<tr>
						<th scope="row"><label for="swe-ml-link"><?php esc_html_e( 'Application link', 'swe-mail-list' ); ?></label></th>
						<td>
							<input type="url" id="swe-ml-link" name="link" class="regular-text" value="<?php echo esc_attr( swe_ml_apply_page_url() ); ?>">
							<p class="description"><?php esc_html_e( 'Appended to the message as “Apply now: …”. Leave blank to omit.', 'swe-mail-list' ); ?></p>
						</td>
					</tr>
				</table>
				<p>
					<button type="submit" name="swe_ml_send" value="1" class="button button-primary" <?php disabled( 0 === $count ); ?> onclick="return confirm('<?php echo esc_js( __( 'Send this email to every subscriber on the waitlist?', 'swe-mail-list' ) ); ?>');">
						<?php esc_html_e( 'Send to all subscribers', 'swe-mail-list' ); ?>
					</button>
				</p>
			</form>
		</div>

		<h2><?php esc_html_e( 'Subscribers', 'swe-mail-list' ); ?></h2>
		<form method="get">
			<input type="hidden" name="page" value="swe-mail-list">
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Subscribers list table.
 */
class SWE_ML_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'subscriber',
				'plural'   => 'subscribers',
				'ajax'     => false,
			)
		);
	}

	/**
	 * Columns.
	 *
	 * @return array
	 */
	public function get_columns() {
		return array(
			'cb'         => '<input type="checkbox">',
			'email'      => __( 'Email', 'swe-mail-list' ),
			'created_at' => __( 'Joined', 'swe-mail-list' ),
		);
	}

	/**
	 * Sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'email'      => array( 'email', false ),
			'created_at' => array( 'created_at', true ),
		);
	}

	/**
	 * Bulk actions.
	 *
	 * @return array
	 */
	protected function get_bulk_actions() {
		return array(
			'bulk-delete' => __( 'Delete', 'swe-mail-list' ),
		);
	}

	/**
	 * Query the custom table.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page   = 50;
		$page_num   = max( 1, $this->get_pagenum() );
		$table_name = swe_ml_table_name();

		// Whitelist orderby against sortable columns — never trust raw input in ORDER BY.
		$allowed = array_keys( $this->get_sortable_columns() );
		$orderby = isset( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'created_at'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$orderby = in_array( $orderby, $allowed, true ) ? $orderby : 'created_at';
		$order   = ( isset( $_GET['order'] ) && 'asc' === strtolower( sanitize_key( $_GET['order'] ) ) ) ? 'ASC' : 'DESC'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery, WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		$this->items = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT * FROM {$table_name} ORDER BY {$orderby} {$order} LIMIT %d OFFSET %d", // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared -- table + whitelisted columns.
				$per_page,
				( $page_num - 1 ) * $per_page
			)
		);

		$this->_column_headers = array( $this->get_columns(), array(), $this->get_sortable_columns() );
		$this->set_pagination_args(
			array(
				'total_items' => $total,
				'per_page'    => $per_page,
				'total_pages' => (int) ceil( $total / $per_page ),
			)
		);
	}

	/**
	 * Checkbox column.
	 *
	 * @param object $item Row.
	 * @return string
	 */
	public function column_cb( $item ) {
		return '<input type="checkbox" name="ids[]" value="' . (int) $item->id . '">';
	}

	/**
	 * Email column with row actions.
	 *
	 * @param object $item Row.
	 * @return string
	 */
	public function column_email( $item ) {
		$delete_url = wp_nonce_url( admin_url( 'admin.php?page=swe-mail-list&action=delete&id=' . (int) $item->id ), 'swe_ml_delete_' . (int) $item->id );

		$actions = array(
			'email'  => '<a href="mailto:' . esc_attr( $item->email ) . '">' . esc_html__( 'Email', 'swe-mail-list' ) . '</a>',
			'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'' . esc_js( __( 'Remove this email from the waitlist?', 'swe-mail-list' ) ) . '\');">' . esc_html__( 'Delete', 'swe-mail-list' ) . '</a>',
		);

		return '<strong>' . esc_html( $item->email ) . '</strong>' . $this->row_actions( $actions );
	}

	/**
	 * Default column output.
	 *
	 * @param object $item        Row.
	 * @param string $column_name Column key.
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return esc_html( $item->{$column_name} ?? '' );
	}

	/**
	 * Empty-table message.
	 */
	public function no_items() {
		esc_html_e( 'No subscribers yet — signups from the Apply page waitlist will appear here.', 'swe-mail-list' );
	}
}
