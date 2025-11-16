<?php
/**
 * The chat history functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The chat history functionality of the plugin.
 */
class Sitecompass_Ai_Chats {
	/**
	 * The ID of this plugin.
	 *
	 * @var string
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @var string
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of this plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Render the chat history page.
	 */
	public function render_chats_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'sitecompass' ) );
		}

		// Check if viewing a single thread.
		if ( isset( $_GET['thread_id'] ) && ! empty( $_GET['thread_id'] ) ) {
			$this->render_single_thread_view();
			return;
		}

		// Handle CSV export.
		if ( isset( $_GET['action'] ) && 'export_csv' === $_GET['action'] ) {
			$this->handle_csv_export();
			return;
		}

		// Handle conversation deletion.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['session_id'] ) ) {
			$this->handle_conversation_deletion();
		}

		$this->render_conversations_list();
	}

	/**
	 * Render the conversations list page.
	 */
	private function render_conversations_list() {
		global $wpdb;

		// Get filter parameters.
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		// Pagination.
		$per_page = 20;
		$current_page = isset( $_GET['paged'] ) ? max( 1, intval( $_GET['paged'] ) ) : 1;
		$offset = ( $current_page - 1 ) * $per_page;

		// Build query.
		$table_name = $wpdb->prefix . 'sitecompass_conversations';
		$where_clauses = array( '1=1' );
		$query_params = array();

		if ( ! empty( $search ) ) {
			$where_clauses[] = '(session_id LIKE %s OR thread_id LIKE %s OR message_text LIKE %s)';
			$search_term = '%' . $wpdb->esc_like( $search ) . '%';
			$query_params[] = $search_term;
			$query_params[] = $search_term;
			$query_params[] = $search_term;
		}

		if ( ! empty( $start_date ) ) {
			$where_clauses[] = 'DATE(created_at) >= %s';
			$query_params[] = $start_date;
		}

		if ( ! empty( $end_date ) ) {
			$where_clauses[] = 'DATE(created_at) <= %s';
			$query_params[] = $end_date;
		}

		$where_sql = implode( ' AND ', $where_clauses );

		// Get total count.
		$count_query = $wpdb->prepare( "SELECT COUNT(DISTINCT session_id) FROM %i WHERE {$where_sql}", $table_name ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( ! empty( $query_params ) ) {
			$count_query = $wpdb->prepare( $count_query, $query_params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}
		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$total_items = $wpdb->get_var( $count_query );

		// Get conversations grouped by session.
		$conversations_query = $wpdb->prepare(
			"SELECT 
				session_id,
				thread_id,
				MIN(created_at) as first_message_time,
				MAX(created_at) as last_message_time,
				COUNT(*) as message_count
			FROM %i
			WHERE {$where_sql}
			GROUP BY session_id, thread_id
			ORDER BY last_message_time DESC
			LIMIT %d OFFSET %d",
			$table_name,
			$per_page,
			$offset
		); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared

		// Prepend table name parameter to query params for the WHERE clause.
		if ( ! empty( $query_params ) ) {
			$conversations_query = $wpdb->prepare( $conversations_query, $query_params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$conversations = $wpdb->get_results( $conversations_query );

		$total_pages = ceil( $total_items / $per_page );

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Chat History', 'sitecompass' ); ?></h1>

			<!-- Search and Filter Form -->
			<form method="get" action="">
				<input type="hidden" name="page" value="sitecompass" />
				<p class="search-box">
					<label class="screen-reader-text" for="chat-search-input"><?php esc_html_e( 'Search Chats:', 'sitecompass' ); ?></label>
					<input type="search" id="chat-search-input" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search by session ID, thread ID, or message...', 'sitecompass' ); ?>" />
				</p>
				<p>
					<label for="start_date"><?php esc_html_e( 'Start Date:', 'sitecompass' ); ?></label>
					<input type="date" id="start_date" name="start_date" value="<?php echo esc_attr( $start_date ); ?>" />
					
					<label for="end_date"><?php esc_html_e( 'End Date:', 'sitecompass' ); ?></label>
					<input type="date" id="end_date" name="end_date" value="<?php echo esc_attr( $end_date ); ?>" />
					
					<?php submit_button( __( 'Filter', 'sitecompass' ), 'secondary', 'filter', false ); ?>
					<a href="?page=sitecompass" class="button"><?php esc_html_e( 'Reset', 'sitecompass' ); ?></a>
				</p>
			</form>

			<!-- Export Button -->
			<p>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'export_csv', 's' => $search, 'start_date' => $start_date, 'end_date' => $end_date ) ), 'export_csv_nonce' ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Export to CSV', 'sitecompass' ); ?>
				</a>
			</p>

			<?php if ( empty( $conversations ) ) : ?>
				<p><?php esc_html_e( 'No conversations found.', 'sitecompass' ); ?></p>
			<?php else : ?>
				<!-- Conversations Table -->
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Session ID', 'sitecompass' ); ?></th>
							<th><?php esc_html_e( 'Thread ID', 'sitecompass' ); ?></th>
							<th><?php esc_html_e( 'First Message', 'sitecompass' ); ?></th>
							<th><?php esc_html_e( 'Last Message', 'sitecompass' ); ?></th>
							<th><?php esc_html_e( 'Messages', 'sitecompass' ); ?></th>
							<th><?php esc_html_e( 'Actions', 'sitecompass' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $conversations as $conversation ) : ?>
							<tr>
								<td><?php echo esc_html( $conversation->session_id ); ?></td>
								<td><?php echo esc_html( $conversation->thread_id ); ?></td>
								<td><?php echo esc_html( $conversation->first_message_time ); ?></td>
								<td><?php echo esc_html( $conversation->last_message_time ); ?></td>
								<td><?php echo esc_html( $conversation->message_count ); ?></td>
								<td>
									<a href="<?php echo esc_url( add_query_arg( array( 'thread_id' => $conversation->thread_id ) ) ); ?>" class="button button-small">
										<?php esc_html_e( 'View', 'sitecompass' ); ?>
									</a>
									<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'session_id' => $conversation->session_id ) ), 'delete_conversation_' . $conversation->session_id ) ); ?>" class="button button-small" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this conversation?', 'sitecompass' ); ?>');">
										<?php esc_html_e( 'Delete', 'sitecompass' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<!-- Pagination -->
				<?php if ( $total_pages > 1 ) : ?>
					<div class="tablenav">
						<div class="tablenav-pages">
							<?php
							$page_links = paginate_links(
								array(
									'base'      => add_query_arg( 'paged', '%#%' ),
									'format'    => '',
									'prev_text' => __( '&laquo;', 'sitecompass' ),
									'next_text' => __( '&raquo;', 'sitecompass' ),
									'total'     => $total_pages,
									'current'   => $current_page,
								)
							);
							echo wp_kses_post( $page_links );
							?>
						</div>
					</div>
				<?php endif; ?>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render single thread view.
	 */
	private function render_single_thread_view() {
		global $wpdb;

		// Verify nonce if present.
		if ( isset( $_GET['_wpnonce'] ) && ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'view_thread' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'sitecompass' ) );
		}

		$thread_id = isset( $_GET['thread_id'] ) ? sanitize_text_field( wp_unslash( $_GET['thread_id'] ) ) : '';

		if ( empty( $thread_id ) ) {
			wp_die( esc_html__( 'Invalid thread ID.', 'sitecompass' ) );
		}

		// Handle CSV export for single thread.
		if ( isset( $_GET['action'] ) && 'export_thread_csv' === $_GET['action'] ) {
			$this->handle_thread_csv_export( $thread_id );
			return;
		}

		// Get messages for this thread.
		$table_name = $wpdb->prefix . 'sitecompass_conversations';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$messages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM %i WHERE thread_id = %s ORDER BY created_at ASC",
				$table_name,
				$thread_id
			)
		);

		if ( empty( $messages ) ) {
			wp_die( esc_html__( 'No messages found for this thread.', 'sitecompass' ) );
		}

		$session_id = $messages[0]->session_id;

		?>
		<div class="wrap">
			<h1><?php esc_html_e( 'Conversation Thread', 'sitecompass' ); ?></h1>
			
			<p>
				<a href="?page=sitecompass" class="button"><?php esc_html_e( '&larr; Back to Chat History', 'sitecompass' ); ?></a>
				<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'export_thread_csv', 'thread_id' => $thread_id ) ), 'export_thread_csv_' . $thread_id ) ); ?>" class="button button-primary">
					<?php esc_html_e( 'Export Thread to CSV', 'sitecompass' ); ?>
				</a>
			</p>

			<div class="sitecompass-thread-info">
				<p><strong><?php esc_html_e( 'Session ID:', 'sitecompass' ); ?></strong> <?php echo esc_html( $session_id ); ?></p>
				<p><strong><?php esc_html_e( 'Thread ID:', 'sitecompass' ); ?></strong> <?php echo esc_html( $thread_id ); ?></p>
				<p><strong><?php esc_html_e( 'Total Messages:', 'sitecompass' ); ?></strong> <?php echo esc_html( count( $messages ) ); ?></p>
			</div>

			<div class="sitecompass-thread-messages" style="background: #fff; padding: 20px; border: 1px solid #ccc; margin-top: 20px;">
				<?php foreach ( $messages as $message ) : ?>
					<div class="sitecompass-message" style="margin-bottom: 20px; padding: 15px; background: <?php echo esc_attr( 'user' === $message->user_type ? '#e3f2fd' : '#f5f5f5' ); ?>; border-radius: 5px;">
						<div class="message-header" style="margin-bottom: 10px;">
							<strong><?php echo 'user' === $message->user_type ? esc_html__( 'User', 'sitecompass' ) : esc_html__( 'Assistant', 'sitecompass' ); ?>:</strong>
							<span style="color: #666; font-size: 0.9em; margin-left: 10px;"><?php echo esc_html( $message->created_at ); ?></span>
						</div>
						<div class="message-content">
							<?php echo wp_kses_post( nl2br( $message->message_text ) ); ?>
						</div>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Handle CSV export of all conversations.
	 */
	private function handle_csv_export() {
		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'export_csv_nonce' ) ) {
			wp_die( esc_html__( 'Security check failed.', 'sitecompass' ) );
		}

		global $wpdb;

		// Get filter parameters.
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( $_GET['s'] ) ) : '';
		$start_date = isset( $_GET['start_date'] ) ? sanitize_text_field( wp_unslash( $_GET['start_date'] ) ) : '';
		$end_date = isset( $_GET['end_date'] ) ? sanitize_text_field( wp_unslash( $_GET['end_date'] ) ) : '';

		// Build query.
		$table_name = $wpdb->prefix . 'sitecompass_conversations';
		$where_clauses = array( '1=1' );
		$query_params = array();

		if ( ! empty( $search ) ) {
			$where_clauses[] = '(session_id LIKE %s OR thread_id LIKE %s OR message_text LIKE %s)';
			$search_term = '%' . $wpdb->esc_like( $search ) . '%';
			$query_params[] = $search_term;
			$query_params[] = $search_term;
			$query_params[] = $search_term;
		}

		if ( ! empty( $start_date ) ) {
			$where_clauses[] = 'DATE(created_at) >= %s';
			$query_params[] = $start_date;
		}

		if ( ! empty( $end_date ) ) {
			$where_clauses[] = 'DATE(created_at) <= %s';
			$query_params[] = $end_date;
		}

		$where_sql = implode( ' AND ', $where_clauses );

		$query = $wpdb->prepare( "SELECT * FROM %i WHERE {$where_sql} ORDER BY created_at ASC", $table_name ); // phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		if ( ! empty( $query_params ) ) {
			$query = $wpdb->prepare( $query, $query_params ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
		}

		// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$conversations = $wpdb->get_results( $query );

		// Set headers for CSV download.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=sitecompass-chats-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Create output stream.
		$output = fopen( 'php://output', 'w' );

		// Add CSV headers.
		fputcsv( $output, array( 'ID', 'Session ID', 'User ID', 'Page ID', 'User Type', 'Thread ID', 'Assistant ID', 'Message', 'Created At' ) );

		// Add data rows.
		foreach ( $conversations as $conversation ) {
			fputcsv(
				$output,
				array(
					$conversation->id,
					$conversation->session_id,
					$conversation->user_id,
					$conversation->page_id,
					$conversation->user_type,
					$conversation->thread_id,
					$conversation->assistant_id,
					$conversation->message_text,
					$conversation->created_at,
				)
			);
		}

		fclose( $output );
		exit;
	}

	/**
	 * Handle CSV export of a single thread.
	 *
	 * @param string $thread_id The thread ID to export.
	 */
	private function handle_thread_csv_export( $thread_id ) {
		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'export_thread_csv_' . $thread_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'sitecompass' ) );
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'sitecompass_conversations';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$messages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM %i WHERE thread_id = %s ORDER BY created_at ASC",
				$table_name,
				$thread_id
			)
		);

		if ( empty( $messages ) ) {
			wp_die( esc_html__( 'No messages found for this thread.', 'sitecompass' ) );
		}

		// Set headers for CSV download.
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename=sitecompass-thread-' . sanitize_file_name( $thread_id ) . '-' . gmdate( 'Y-m-d-H-i-s' ) . '.csv' );
		header( 'Pragma: no-cache' );
		header( 'Expires: 0' );

		// Create output stream.
		$output = fopen( 'php://output', 'w' );

		// Add CSV headers.
		fputcsv( $output, array( 'ID', 'Session ID', 'User ID', 'Page ID', 'User Type', 'Thread ID', 'Assistant ID', 'Message', 'Created At' ) );

		// Add data rows.
		foreach ( $messages as $message ) {
			fputcsv(
				$output,
				array(
					$message->id,
					$message->session_id,
					$message->user_id,
					$message->page_id,
					$message->user_type,
					$message->thread_id,
					$message->assistant_id,
					$message->message_text,
					$message->created_at,
				)
			);
		}

		fclose( $output );
		exit;
	}

	/**
	 * Handle conversation deletion.
	 */
	private function handle_conversation_deletion() {
		$session_id = isset( $_GET['session_id'] ) ? sanitize_text_field( wp_unslash( $_GET['session_id'] ) ) : '';

		if ( empty( $session_id ) ) {
			return;
		}

		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'delete_conversation_' . $session_id ) ) {
			wp_die( esc_html__( 'Security check failed.', 'sitecompass' ) );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_conversations';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$deleted = $wpdb->delete(
			$table_name,
			array( 'session_id' => $session_id ),
			array( '%s' )
		);

		if ( false !== $deleted ) {
			add_settings_error(
				'sitecompass_messages',
				'conversation_deleted',
				__( 'Conversation deleted successfully.', 'sitecompass' ),
				'success'
			);
		} else {
			add_settings_error(
				'sitecompass_messages',
				'conversation_delete_failed',
				__( 'Failed to delete conversation.', 'sitecompass' ),
				'error'
			);
		}

		// Redirect to remove the action parameter.
		wp_safe_redirect( admin_url( 'admin.php?page=sitecompass' ) );
		exit;
	}
}
