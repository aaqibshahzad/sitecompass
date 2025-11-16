<?php
/**
 * OpenAI Assistant API Wrapper
 *
 * Handles all interactions with the OpenAI Assistant API using WordPress HTTP API.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/includes
 */

/**
 * OpenAI Assistant API wrapper class.
 *
 * Provides methods for interacting with OpenAI's Assistant API including
 * assistants, threads, messages, runs, and file management.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/includes
 */
class Sitecompass_Ai_OpenAI_Assistant {

	/**
	 * OpenAI API base URL.
	 *
	 * @var string
	 */
	const BASE_URL = 'https://api.openai.com/v1/';

	/**
	 * OpenAI API beta version header.
	 *
	 * @var string
	 */
	const ASSISTANT_VERSION = 'assistants=v2';

	/**
	 * OpenAI API key.
	 *
	 * @var string
	 */
	private $api_key = '';

	/**
	 * Chat model to use.
	 *
	 * @var string
	 */
	private $chat_model = 'gpt-4o-mini';

	/**
	 * Assistant instructions.
	 *
	 * @var string
	 */
	private $instructions = 'You are a customer support chatbot. Use your knowledge base to best respond to customer queries.';

	/**
	 * Tools configuration for the assistant.
	 *
	 * @var array
	 */
	private $tools = array( array( 'type' => 'file_search' ) );

	/**
	 * Constructor.
	 *
	 * @param string $key OpenAI API key.
	 */
	public function __construct( $key ) {
		$this->api_key = $key;
	}

	/**
	 * Set the chat model.
	 *
	 * @param string $model Model name (gpt-4o, gpt-4o-mini, gpt-4-turbo).
	 */
	public function set_chat_model( $model ) {
		$this->chat_model = $model;
	}

	/**
	 * Get the current chat model.
	 *
	 * @return string Current model name.
	 */
	public function get_chat_model() {
		return $this->chat_model;
	}

	/**
	 * Set assistant instructions.
	 *
	 * @param string $instruction Instructions for the assistant.
	 */
	public function set_instructions( $instruction ) {
		$this->instructions = $instruction;
	}

	/**
	 * Get assistant instructions.
	 *
	 * @return string Current instructions.
	 */
	public function get_instructions() {
		return $this->instructions;
	}

	/**
	 * Set tools configuration.
	 *
	 * @param array $tools Tools array.
	 */
	public function set_tools( $tools ) {
		$this->tools = $tools;
	}

	/**
	 * Get tools configuration.
	 *
	 * @return array Current tools configuration.
	 */
	public function get_tools() {
		return $this->tools;
	}

	/**
	 * Create a new assistant.
	 *
	 * @param string $assistant_name Name for the assistant.
	 * @param array  $file_ids       Optional array of file IDs.
	 * @return array Response from OpenAI API.
	 */
	public function create_assistant( $assistant_name, $file_ids = null ) {
		$data = array(
			'name'         => $assistant_name,
			'instructions' => $this->instructions,
			'tools'        => $this->tools,
			'model'        => $this->chat_model,
		);

		if ( ! is_null( $file_ids ) && ! empty( $file_ids ) ) {
			$data['tool_resources'] = array(
				'file_search' => array(
					'vector_store_ids' => $file_ids,
				),
			);
		}

		$response = $this->make_request( 'assistants', $data, 'POST' );

		return $response;
	}

	/**
	 * List assistants.
	 *
	 * @param string $order  Order direction (asc or desc).
	 * @param int    $limit  Number of results to return.
	 * @param string $after  Cursor for pagination.
	 * @param string $before Cursor for pagination.
	 * @return array Response from OpenAI API.
	 */
	public function list_assistants( $order = 'desc', $limit = 20, $after = null, $before = null ) {
		$data = array(
			'order' => $order,
			'limit' => $limit,
		);

		if ( ! is_null( $after ) ) {
			$data['after'] = $after;
		}

		if ( ! is_null( $before ) ) {
			$data['before'] = $before;
		}

		$response = $this->make_request( 'assistants', $data, 'GET' );
		return $response;
	}

	/**
	 * Get a specific assistant.
	 *
	 * @param string $assistant_id Assistant ID.
	 * @return array Response from OpenAI API.
	 */
	public function get_assistant( $assistant_id ) {
		$url      = 'assistants/' . $assistant_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Modify an existing assistant.
	 *
	 * @param string $assistant_id Assistant ID.
	 * @param array  $data         Data to update.
	 * @return array Response from OpenAI API.
	 */
	public function modify_assistant( $assistant_id, $data ) {
		$url      = 'assistants/' . $assistant_id;
		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Delete an assistant.
	 *
	 * @param string $assistant_id Assistant ID.
	 * @return array Response from OpenAI API.
	 */
	public function delete_assistant( $assistant_id ) {
		$url      = 'assistants/' . $assistant_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'DELETE' );
		return $response;
	}

	/**
	 * Create a new thread.
	 *
	 * @return array Response from OpenAI API.
	 */
	public function create_thread() {
		$url      = 'threads';
		$data     = array();
		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Get a specific thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @return array Response from OpenAI API.
	 */
	public function get_thread( $thread_id ) {
		$url      = 'threads/' . $thread_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Modify a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @param array  $data      Data to update (must include metadata).
	 * @return array Response from OpenAI API.
	 */
	public function modify_thread( $thread_id, $data ) {
		$url = 'threads/' . $thread_id;

		if ( ! isset( $data['metadata'] ) ) {
			return array(
				'error' => array(
					'message' => 'No metadata found',
				),
			);
		}

		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Delete a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @return array Response from OpenAI API.
	 */
	public function delete_thread( $thread_id ) {
		$url      = 'threads/' . $thread_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'DELETE' );
		return $response;
	}

	/**
	 * Create a message in a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @param array  $data      Message data.
	 * @return array Response from OpenAI API.
	 */
	public function create_message( $thread_id, $data ) {
		$url      = 'threads/' . $thread_id . '/messages';
		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * List messages in a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $order     Order direction (asc or desc).
	 * @param int    $limit     Number of results to return.
	 * @param string $after     Cursor for pagination.
	 * @param string $before    Cursor for pagination.
	 * @return array Response from OpenAI API.
	 */
	public function list_messages( $thread_id, $order = 'desc', $limit = 20, $after = null, $before = null ) {
		$url = 'threads/' . $thread_id . '/messages';

		$data = array(
			'order' => $order,
			'limit' => $limit,
		);

		if ( ! is_null( $after ) ) {
			$data['after'] = $after;
		}

		if ( ! is_null( $before ) ) {
			$data['before'] = $before;
		}

		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Get a specific message.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $msg_id    Message ID.
	 * @return array Response from OpenAI API.
	 */
	public function get_message( $thread_id, $msg_id ) {
		$url      = 'threads/' . $thread_id . '/messages/' . $msg_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Modify a message.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $msg_id    Message ID.
	 * @param array  $data      Data to update (must include metadata).
	 * @return array Response from OpenAI API.
	 */
	public function modify_message( $thread_id, $msg_id, $data ) {
		$url = 'threads/' . $thread_id . '/messages/' . $msg_id;

		if ( ! isset( $data['metadata'] ) ) {
			return array(
				'error' => array(
					'message' => 'No metadata found',
				),
			);
		}

		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Run an assistant on a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @param array  $data      Run data (must include assistant_id).
	 * @return array Response from OpenAI API.
	 */
	public function run( $thread_id, $data ) {
		$url = 'threads/' . $thread_id . '/runs';

		if ( ! isset( $data['assistant_id'] ) ) {
			return array(
				'error' => array(
					'message' => 'No Assistant Id found',
				),
			);
		}

		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Create a thread and run in one request.
	 *
	 * @param array $data Run data (must include assistant_id and thread).
	 * @return array Response from OpenAI API.
	 */
	public function create_thread_and_run( $data ) {
		$url = 'threads/runs';

		if ( ! isset( $data['assistant_id'] ) ) {
			return array(
				'error' => array(
					'message' => 'No assistant id found',
				),
			);
		}

		if ( ! isset( $data['thread'] ) ) {
			return array(
				'error' => array(
					'message' => 'No thread index found',
				),
			);
		}

		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * List runs for a thread.
	 *
	 * @param string $thread_id Thread ID.
	 * @return array Response from OpenAI API.
	 */
	public function list_runs( $thread_id ) {
		$url      = 'threads/' . $thread_id . '/runs';
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * List run steps.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @return array Response from OpenAI API.
	 */
	public function list_run_steps( $thread_id, $run_id ) {
		$url      = 'threads/' . $thread_id . '/runs/' . $run_id . '/steps';
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Retrieve a specific run.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @return array Response from OpenAI API.
	 */
	public function retrieve_run( $thread_id, $run_id ) {
		$url      = 'threads/' . $thread_id . '/runs/' . $run_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Retrieve a specific run step.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @param string $step_id   Step ID.
	 * @return array Response from OpenAI API.
	 */
	public function retrieve_run_step( $thread_id, $run_id, $step_id ) {
		$url      = 'threads/' . $thread_id . '/runs/' . $run_id . '/steps/' . $step_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Modify a run.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @param array  $data      Data to update (must include metadata).
	 * @return array Response from OpenAI API.
	 */
	public function modify_run( $thread_id, $run_id, $data ) {
		$url = 'threads/' . $thread_id . '/runs/' . $run_id;

		if ( ! isset( $data['metadata'] ) ) {
			return array(
				'error' => array(
					'message' => 'No metadata found',
				),
			);
		}

		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Submit tool outputs to a run.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @param array  $data      Tool outputs data.
	 * @return array Response from OpenAI API.
	 */
	public function submit_tool_output_to_run( $thread_id, $run_id, $data = array() ) {
		$url      = 'threads/' . $thread_id . '/runs/' . $run_id . '/submit_tool_outputs';
		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Cancel a run.
	 *
	 * @param string $thread_id Thread ID.
	 * @param string $run_id    Run ID.
	 * @return array Response from OpenAI API.
	 */
	public function cancel_run( $thread_id, $run_id ) {
		$url      = 'threads/' . $thread_id . '/runs/' . $run_id . '/cancel';
		$data     = array();
		$response = $this->make_request( $url, $data, 'POST' );
		return $response;
	}

	/**
	 * Verify the OpenAI API key.
	 *
	 * @return string Returns 'valid_api_key' if valid, or error code if invalid.
	 */
	public function verify_api_key() {
		$list_assistants = $this->list_assistants( 'desc', 1 );
		
		if ( isset( $list_assistants['error']['code'] ) ) {
			return $list_assistants['error']['code'];
		}
		
		// Note: This is a status code, not a user-facing string, so no i18n needed.
		return 'valid_api_key';
	}

	/**
	 * Upload a file to OpenAI.
	 *
	 * @param string $file_path Path to the file to upload.
	 * @return array Response from OpenAI API.
	 */
	public function upload_file( $file_path ) {
		$response = $this->make_file_upload_request( $file_path );
		return $response;
	}

	/**
	 * Delete a file from OpenAI.
	 *
	 * @param string $file_id File ID.
	 * @return array Response from OpenAI API.
	 */
	public function delete_file( $file_id ) {
		$url      = 'files/' . $file_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'DELETE' );
		return $response;
	}

	/**
	 * Get file information.
	 *
	 * @param string $file_id File ID.
	 * @return array Response from OpenAI API.
	 */
	public function get_file( $file_id ) {
		$url      = 'files/' . $file_id;
		$data     = array();
		$response = $this->make_request( $url, $data, 'GET' );
		return $response;
	}

	/**
	 * Make an HTTP request to OpenAI API using WordPress HTTP API.
	 *
	 * @param string $action API endpoint.
	 * @param array  $data   Request data.
	 * @param string $type   HTTP method (GET, POST, DELETE).
	 * @return array Decoded JSON response.
	 */
	private function make_request( $action, $data = array(), $type = 'GET' ) {
		$url = self::BASE_URL . $action;

		$headers = array(
			'Authorization' => 'Bearer ' . $this->api_key,
			'OpenAI-Beta'   => self::ASSISTANT_VERSION,
			'Content-Type'  => 'application/json',
		);

		$args = array(
			'headers' => $headers,
			'timeout' => 60,
		);

		if ( 'GET' === $type ) {
			if ( count( $data ) > 0 ) {
				$url = add_query_arg( $data, $url );
			}
			$response = wp_remote_get( $url, $args );
		} elseif ( 'POST' === $type ) {
			$args['body']   = wp_json_encode( $data );
			$args['method'] = 'POST';
			$response       = wp_remote_post( $url, $args );
		} elseif ( 'DELETE' === $type ) {
			$args['method'] = 'DELETE';
			$response       = wp_remote_request( $url, $args );
		} else {
			$args['method'] = $type;
			$args['body']   = wp_json_encode( $data );
			$response       = wp_remote_request( $url, $args );
		}

		if ( is_wp_error( $response ) ) {
			return array(
				'error' => array(
					'message' => $response->get_error_message(),
					'code'    => 'wp_http_error',
				),
			);
		}

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}

	/**
	 * Upload a file using WordPress HTTP API.
	 *
	 * @param string $file_path Path to the file to upload.
	 * @return array Decoded JSON response.
	 */
	private function make_file_upload_request( $file_path ) {
		$url = self::BASE_URL . 'files';

		$boundary = wp_generate_password( 24, false );

		$headers = array(
			'Authorization' => 'Bearer ' . $this->api_key,
			'OpenAI-Beta'   => self::ASSISTANT_VERSION,
			'Content-Type'  => 'multipart/form-data; boundary=' . $boundary,
		);

		// Build multipart form data.
		$payload = '';

		// Add purpose field.
		$payload .= '--' . $boundary . "\r\n";
		$payload .= 'Content-Disposition: form-data; name="purpose"' . "\r\n\r\n";
		$payload .= 'assistants' . "\r\n";

		// Add file field.
		$file_contents = file_get_contents( $file_path );
		$file_name     = basename( $file_path );

		$payload .= '--' . $boundary . "\r\n";
		$payload .= 'Content-Disposition: form-data; name="file"; filename="' . $file_name . '"' . "\r\n";
		$payload .= 'Content-Type: application/octet-stream' . "\r\n\r\n";
		$payload .= $file_contents . "\r\n";
		$payload .= '--' . $boundary . '--';

		$args = array(
			'headers' => $headers,
			'body'    => $payload,
			'timeout' => 120,
		);

		$response = wp_remote_post( $url, $args );

		if ( is_wp_error( $response ) ) {
			return array(
				'error' => array(
					'message' => $response->get_error_message(),
					'code'    => 'wp_http_error',
				),
			);
		}

		$body = wp_remote_retrieve_body( $response );
		return json_decode( $body, true );
	}
}
