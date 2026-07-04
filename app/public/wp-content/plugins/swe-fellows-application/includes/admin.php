<?php
/**
 * "Applications" admin screen: WP_List_Table over the custom table,
 * a per-application detail view, review-status updates, and delete.
 *
 * @package swe-fellows-application
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the top-level menu.
 */
function swe_app_admin_menu() {
	add_menu_page(
		__( 'Applications', 'swe-fellows-application' ),
		__( 'Applications', 'swe-fellows-application' ),
		'manage_options',
		'swe-applications',
		'swe_app_render_admin_page',
		'dashicons-clipboard',
		26
	);
}
add_action( 'admin_menu', 'swe_app_admin_menu' );

/**
 * Load the plugin stylesheet (status pills) on our screen only.
 *
 * @param string $hook Current admin page hook.
 */
function swe_app_admin_assets( $hook ) {
	if ( 'toplevel_page_swe-applications' === $hook ) {
		wp_enqueue_style( 'swe-app-style', SWE_APP_URL . 'assets/style.css', array(), SWE_APP_VERSION );
	}
}
add_action( 'admin_enqueue_scripts', 'swe_app_admin_assets' );

/**
 * Handle delete + status updates before any output.
 */
function swe_app_admin_actions() {
	if ( ! isset( $_REQUEST['page'] ) || 'swe-applications' !== $_REQUEST['page'] || ! current_user_can( 'manage_options' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		return;
	}

	$base = admin_url( 'admin.php?page=swe-applications' );

	// Delete (link with nonce).
	if ( isset( $_GET['action'], $_GET['id'] ) && 'delete' === $_GET['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$id = (int) $_GET['id']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'swe_app_delete_' . $id );
		swe_app_delete( $id );
		wp_safe_redirect( add_query_arg( 'swe_app_notice', 'deleted', $base ) );
		exit;
	}

	// Status update (POST from detail view).
	if ( isset( $_POST['swe_app_status_update'], $_POST['id'], $_POST['status'] ) ) {
		$id = (int) $_POST['id'];
		check_admin_referer( 'swe_app_status_' . $id );
		$updated = swe_app_set_status( $id, sanitize_key( wp_unslash( $_POST['status'] ) ) );
		$notice  = $updated ? 'updated' : 'update_failed';
		wp_safe_redirect( add_query_arg( array( 'view' => $id, 'swe_app_notice' => $notice ), $base ) );
		exit;
	}
}
add_action( 'admin_init', 'swe_app_admin_actions' );

/**
 * Router: list vs. detail.
 */
function swe_app_render_admin_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'You do not have permission to view applications.', 'swe-fellows-application' ) );
	}

	$notice = isset( $_GET['swe_app_notice'] ) ? sanitize_key( $_GET['swe_app_notice'] ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( 'deleted' === $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Application deleted.', 'swe-fellows-application' ) . '</p></div>';
	} elseif ( 'updated' === $notice ) {
		echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Status updated.', 'swe-fellows-application' ) . '</p></div>';
	} elseif ( 'update_failed' === $notice ) {
		echo '<div class="notice notice-error is-dismissible"><p>' . esc_html__( 'Status update failed — the application may no longer exist.', 'swe-fellows-application' ) . '</p></div>';
	}

	$view_id = isset( $_GET['view'] ) ? (int) $_GET['view'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( $view_id ) {
		swe_app_render_detail( $view_id );
		return;
	}

	swe_app_render_list();
}

/**
 * List view.
 */
function swe_app_render_list() {
	$table = new SWE_App_List_Table();
	$table->prepare_items();
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Applications', 'swe-fellows-application' ); ?></h1>
		<p class="description"><?php esc_html_e( 'Submissions from the fellows application form, stored in the wp_swe_applications table.', 'swe-fellows-application' ); ?></p>
		<form method="get">
			<input type="hidden" name="page" value="swe-applications">
			<?php $table->display(); ?>
		</form>
	</div>
	<?php
}

/**
 * Detail view for one application.
 *
 * @param int $id Row ID.
 */
function swe_app_render_detail( $id ) {
	$row = swe_app_get( $id );

	if ( ! $row ) {
		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Application', 'swe-fellows-application' ); ?> #<?php echo esc_html( (string) $id ); ?></h1>
			<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=swe-applications' ) ); ?>">&larr; <?php esc_html_e( 'Back to all applications', 'swe-fellows-application' ); ?></a></p>
			<div class="notice notice-error"><p><?php esc_html_e( 'Application not found.', 'swe-fellows-application' ); ?></p></div>
		</div>
		<?php
		return;
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Application', 'swe-fellows-application' ); ?> #<?php echo esc_html( (string) $id ); ?></h1>
		<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=swe-applications' ) ); ?>">&larr; <?php esc_html_e( 'Back to all applications', 'swe-fellows-application' ); ?></a></p>

		<table class="widefat striped" style="max-width:760px;">
			<tbody>
				<tr><th scope="row" style="width:180px;"><?php esc_html_e( 'Name', 'swe-fellows-application' ); ?></th><td><?php echo esc_html( $row->name ); ?></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Email', 'swe-fellows-application' ); ?></th><td><a href="mailto:<?php echo esc_attr( $row->email ); ?>"><?php echo esc_html( $row->email ); ?></a></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Class year', 'swe-fellows-application' ); ?></th><td><?php echo esc_html( $row->class_year ); ?></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Track', 'swe-fellows-application' ); ?></th><td><?php echo esc_html( $row->track ); ?></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Coursework', 'swe-fellows-application' ); ?></th><td><?php echo esc_html( $row->coursework ); ?></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Statement', 'swe-fellows-application' ); ?></th><td><?php echo wp_kses_post( wpautop( $row->statement ) ); ?></td></tr>
				<tr><th scope="row"><?php esc_html_e( 'Submitted', 'swe-fellows-application' ); ?></th><td><?php echo esc_html( $row->created_at ); ?></td></tr>
			</tbody>
		</table>

		<form method="post" style="margin-top:16px; display:flex; align-items:center; gap:8px;">
			<?php wp_nonce_field( 'swe_app_status_' . $id ); ?>
			<input type="hidden" name="id" value="<?php echo esc_attr( (string) $id ); ?>">
			<label for="swe-app-status"><strong><?php esc_html_e( 'Review status:', 'swe-fellows-application' ); ?></strong></label>
			<select name="status" id="swe-app-status">
				<?php foreach ( swe_app_allowed_statuses() as $swe_status ) : ?>
					<option value="<?php echo esc_attr( $swe_status ); ?>" <?php selected( $row->status, $swe_status ); ?>><?php echo esc_html( ucfirst( $swe_status ) ); ?></option>
				<?php endforeach; ?>
			</select>
			<button type="submit" name="swe_app_status_update" value="1" class="button button-primary"><?php esc_html_e( 'Update', 'swe-fellows-application' ); ?></button>
		</form>

		<p style="margin-top:24px;">
			<a class="button-link-delete" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=swe-applications&action=delete&id=' . $id ), 'swe_app_delete_' . $id ) ); ?>" onclick="return confirm('<?php echo esc_js( __( 'Delete this application permanently?', 'swe-fellows-application' ) ); ?>');">
				<?php esc_html_e( 'Delete application', 'swe-fellows-application' ); ?>
			</a>
		</p>
	</div>
	<?php
}

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * Applications list table.
 */
class SWE_App_List_Table extends WP_List_Table {

	/**
	 * Constructor.
	 */
	public function __construct() {
		parent::__construct(
			array(
				'singular' => 'application',
				'plural'   => 'applications',
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
			'name'       => __( 'Name', 'swe-fellows-application' ),
			'email'      => __( 'Email', 'swe-fellows-application' ),
			'class_year' => __( 'Class year', 'swe-fellows-application' ),
			'track'      => __( 'Track', 'swe-fellows-application' ),
			'status'     => __( 'Status', 'swe-fellows-application' ),
			'created_at' => __( 'Submitted', 'swe-fellows-application' ),
		);
	}

	/**
	 * Sortable columns.
	 *
	 * @return array
	 */
	protected function get_sortable_columns() {
		return array(
			'name'       => array( 'name', false ),
			'email'      => array( 'email', false ),
			'class_year' => array( 'class_year', false ),
			'track'      => array( 'track', false ),
			'status'     => array( 'status', false ),
			'created_at' => array( 'created_at', true ),
		);
	}

	/**
	 * Query the custom table.
	 */
	public function prepare_items() {
		global $wpdb;

		$per_page   = 20;
		$page_num   = max( 1, $this->get_pagenum() );
		$table_name = swe_app_table_name();

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
	 * Name column with row actions.
	 *
	 * @param object $item Row.
	 * @return string
	 */
	public function column_name( $item ) {
		$view_url   = admin_url( 'admin.php?page=swe-applications&view=' . (int) $item->id );
		$delete_url = wp_nonce_url( admin_url( 'admin.php?page=swe-applications&action=delete&id=' . (int) $item->id ), 'swe_app_delete_' . (int) $item->id );

		$actions = array(
			'view'   => '<a href="' . esc_url( $view_url ) . '">' . esc_html__( 'View', 'swe-fellows-application' ) . '</a>',
			'delete' => '<a href="' . esc_url( $delete_url ) . '" onclick="return confirm(\'' . esc_js( __( 'Delete this application permanently?', 'swe-fellows-application' ) ) . '\');">' . esc_html__( 'Delete', 'swe-fellows-application' ) . '</a>',
		);

		return '<strong><a href="' . esc_url( $view_url ) . '">' . esc_html( $item->name ) . '</a></strong>' . $this->row_actions( $actions );
	}

	/**
	 * Status column as a colored pill.
	 *
	 * @param object $item Row.
	 * @return string
	 */
	public function column_status( $item ) {
		return '<span class="swe-app-status-pill swe-app-status-' . esc_attr( $item->status ) . '">' . esc_html( ucfirst( $item->status ) ) . '</span>';
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
		esc_html_e( 'No applications yet — submissions from the Apply page will appear here.', 'swe-fellows-application' );
	}
}
