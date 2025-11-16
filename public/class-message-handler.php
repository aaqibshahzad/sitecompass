<?php
/**
 * Message handler for AJAX requests.
 *
 * Handles all AJAX interactions for the chatbox including sending messages,
 * creating sessions, and submitting user information.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */

/**
 * Message handler class.
 *
 * Processes AJAX requests for chat functionality including message sending,
 * session management, and user information collection.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */
class Sitecompass_Ai_Message_Handler {

	/**
	 * Session manager instance.
	 *
	 * @var Sitecompass_Ai_Session
	 */
	private $session;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->session = new Sitecompass_Ai_Session();
	}

	/**
	 * Register AJAX hooks.
	 */
	public function register_hooks() {
		// Register AJAX actions for both logged-in and non-logged-in users.
		add_action( 'wp_ajax_sitecompass_send_message', array( $this, 'handle_send_message' ) );
		add_action( 'wp_ajax_nopriv_sitecompass_send_message', array( $this, 'handle_send_message' ) );

		add_action( 'wp_ajax_sitecompass_create_session', array( $this, 'handle_create_session' ) );
		add_action( 'wp_ajax_nopriv_sitecompass_create_session', array( $this, 'handle_create_session' ) );

		add_action( 'wp_ajax_sitecompass_submit_user_info', array( $this, 'handle_submit_user_info' ) );
		add_action( 'wp_ajax_nopriv_sitecompass_submit_user_info', array( $this, 'handle_submit_user_info' ) );
	}

	/**
	 * Handle send message AJAX request.
	 *
	 * Processes user messages, sends them to OpenAI Assistant API,
	 * and returns the assistant's response.
	 */
	public function handle_send_message() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sitecompass_chat_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security verification failed.', 'sitecompass-ai' ),
				),
				403
			);
		}

		// Get and validate message.
		if ( ! isset( $_POST['message'] ) || empty( $_POST['message'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Message cannot be empty.', 'sitecompass-ai' ),
				),
				400
			);
		}

		$user_message = sanitize_textarea_field( wp_unslash( $_POST['message'] ) );

		// Get session ID.
		$session_id = $this->session->get_session_id();
		if ( empty( $session_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Invalid session.', 'sitecompass-ai' ),
				),
				400
			);
		}

		// Get OpenAI API key and settings.
		$api_key = get_option( 'sitecompass_openai_api_key', '' );
		if ( empty( $api_key ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'OpenAI API key not configured.', 'sitecompass-ai' ),
				),
				500
			);
		}

		$assistant_id = get_option( 'sitecompass_assistant_id', '' );
		if ( empty( $assistant_id ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Assistant not configured.', 'sitecompass-ai' ),
				),
				500
			);
		}

		// Initialize OpenAI assistant.
		$openai = new Sitecompass_Ai_OpenAI_Assistant( $api_key );

		// Get or create thread.
		$thread_id = $this->session->get_thread_id();
		if ( empty( $thread_id ) ) {
			$thread_response = $openai->create_thread();
			if ( isset( $thread_response['error'] ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Failed to create conversation thread.', 'sitecompass-ai' ),
					),
					500
				);
			}
			$thread_id = $thread_response['id'];
			$this->session->set_thread_id( $thread_id );
		}

		// Save user message to database.
		$this->save_message( $session_id, $thread_id, $assistant_id, $user_message, 'user' );

		// Send message to OpenAI.
		$message_data = array(
			'role'    => 'user',
			'content' => $user_message,
		);

		$message_response = $openai->create_message( $thread_id, $message_data );
		if ( isset( $message_response['error'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to send message to assistant.', 'sitecompass-ai' ),
				),
				500
			);
		}

		// Run the assistant.
		$run_data = array(
			'assistant_id' => $assistant_id,
		);

		$run_response = $openai->run( $thread_id, $run_data );
		if ( isset( $run_response['error'] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to run assistant.', 'sitecompass-ai' ),
				),
				500
			);
		}

		$run_id = $run_response['id'];

		// Poll for run completion.
		$max_attempts = 30;
		$attempt      = 0;
		$run_status   = '';

		while ( $attempt < $max_attempts ) {
			sleep( 1 );

			$run_status_response = $openai->retrieve_run( $thread_id, $run_id );
			if ( isset( $run_status_response['error'] ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Failed to retrieve run status.', 'sitecompass-ai' ),
					),
					500
				);
			}

			$run_status = $run_status_response['status'];

			if ( 'completed' === $run_status ) {
				break;
			} elseif ( in_array( $run_status, array( 'failed', 'cancelled', 'expired' ), true ) ) {
				wp_send_json_error(
					array(
						'message' => __( 'Assistant run failed.', 'sitecompass-ai' ),
					),
					500
				);
			}

			$attempt++;
		}

		if ( 'completed' !== $run_status ) {
			wp_send_json_error(
				array(
					'message' => __( 'Assistant response timeout.', 'sitecompass-ai' ),
				),
				500
			);
		}

		// Retrieve assistant response.
		$messages_response = $openai->list_messages( $thread_id, 'desc', 1 );
		if ( isset( $messages_response['error'] ) || ! isset( $messages_response['data'][0] ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to retrieve assistant response.', 'sitecompass-ai' ),
				),
				500
			);
		}

		$assistant_message = $messages_response['data'][0];
		$response_text     = '';

		if ( isset( $assistant_message['content'][0]['text']['value'] ) ) {
			$response_text = $assistant_message['content'][0]['text']['value'];
		}

		// Save assistant response to database.
		$this->save_message( $session_id, $thread_id, $assistant_id, $response_text, 'assistant' );

		// Return success response.
		wp_send_json_success(
			array(
				'message'    => $response_text,
				'thread_id'  => $thread_id,
				'session_id' => $session_id,
			)
		);
	}

	/**
	 * Handle create session AJAX request.
	 *
	 * Creates a new session for the user.
	 */
	public function handle_create_session() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sitecompass_chat_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security verification failed.', 'sitecompass-ai' ),
				),
				403
			);
		}

		// Get or create session ID.
		$session_id = $this->session->get_session_id();

		wp_send_json_success(
			array(
				'session_id' => $session_id,
			)
		);
	}

	/**
	 * Handle submit user info AJAX request.
	 *
	 * Saves user information to the database and sets user session cookie.
	 */
	public function handle_submit_user_info() {
		// Verify nonce.
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'sitecompass_chat_nonce' ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Security verification failed.', 'sitecompass-ai' ),
				),
				403
			);
		}

		// Sanitize and validate user input.
		$user_name = isset( $_POST['user_name'] ) ? sanitize_text_field( wp_unslash( $_POST['user_name'] ) ) : '';
		$email     = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
		$phone     = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';

		// Validate that at least one field is provided.
		if ( empty( $user_name ) && empty( $email ) && empty( $phone ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please provide at least one piece of information.', 'sitecompass-ai' ),
				),
				400
			);
		}

		// Validate email if provided.
		if ( ! empty( $email ) && ! is_email( $email ) ) {
			wp_send_json_error(
				array(
					'message' => __( 'Please provide a valid email address.', 'sitecompass-ai' ),
				),
				400
			);
		}

		// Save user info to database.
		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_users';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$table_name,
			array(
				'user_name'  => $user_name,
				'email'      => $email,
				'phone'      => $phone,
				'created_at' => current_time( 'mysql' ),
			),
			array(
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		if ( false === $result ) {
			wp_send_json_error(
				array(
					'message' => __( 'Failed to save user information.', 'sitecompass-ai' ),
				),
				500
			);
		}

		$user_id = $wpdb->insert_id;

		// Set user session cookie.
		$this->session->set_user_info_submitted( $user_id );

		wp_send_json_success(
			array(
				'message' => __( 'User information saved successfully.', 'sitecompass-ai' ),
				'user_id' => $user_id,
			)
		);
	}

	/**
	 * Save a message to the database.
	 *
	 * @param string $session_id   Session identifier.
	 * @param string $thread_id    OpenAI thread ID.
	 * @param string $assistant_id OpenAI assistant ID.
	 * @param string $message_text Message content.
	 * @param string $user_type    Message type ('user' or 'assistant').
	 * @return int|false Insert ID on success, false on failure.
	 */
	private function save_message( $session_id, $thread_id, $assistant_id, $message_text, $user_type ) {
		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_conversations';

		// Get user ID if available.
		$user_id = $this->session->get_user_session_id();

		// Get current page ID.
		$page_id = isset( $_POST['page_id'] ) ? absint( $_POST['page_id'] ) : 0;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$result = $wpdb->insert(
			$table_name,
			array(
				'session_id'   => $session_id,
				'user_id'      => $user_id,
				'page_id'      => $page_id,
				'user_type'    => $user_type,
				'thread_id'    => $thread_id,
				'assistant_id' => $assistant_id,
				'message_text' => $message_text,
				'created_at'   => current_time( 'mysql' ),
			),
			array(
				'%s',
				'%d',
				'%d',
				'%s',
				'%s',
				'%s',
				'%s',
				'%s',
			)
		);

		if ( false === $result ) {
			error_log( 'SiteCompass: Failed to save message to database' );
			return false;
		}

		return $wpdb->insert_id;
	}
}
