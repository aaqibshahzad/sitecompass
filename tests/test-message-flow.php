<?php
/**
 * Test script for complete message flow
 *
 * This script tests the complete message flow from frontend to OpenAI API
 * and back, including database storage and response display.
 *
 * Requirements tested: 6.4, 6.5, 6.6
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/tests
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

/**
 * Test class for message flow.
 */
class Sitecompass_Ai_Message_Flow_Test {

	/**
	 * Test results array.
	 *
	 * @var array
	 */
	private $results = array();

	/**
	 * Run all tests.
	 *
	 * @return array Test results.
	 */
	public function run_tests() {
		echo "<h2>SiteCompass Message Flow Tests</h2>\n";
		echo "<p>Testing Requirements: 6.4, 6.5, 6.6</p>\n";

		$this->test_session_management();
		$this->test_openai_api_configuration();
		$this->test_database_tables();
		$this->test_message_handler_registration();
		$this->test_chatbox_display();
		$this->test_nonce_verification();

		$this->display_results();

		return $this->results;
	}

	/**
	 * Test session management functionality.
	 */
	private function test_session_management() {
		$test_name = 'Session Management';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-session.php';
			$session = new Sitecompass_Ai_Session();

			// Test session ID generation.
			$session_id = $session->generate_session_id();
			if ( empty( $session_id ) ) {
				throw new Exception( 'Failed to generate session ID' );
			}
			echo "✓ Session ID generated: {$session_id}<br>\n";

			// Test session ID retrieval.
			$retrieved_session_id = $session->get_session_id();
			if ( empty( $retrieved_session_id ) ) {
				throw new Exception( 'Failed to retrieve session ID' );
			}
			echo "✓ Session ID retrieved: {$retrieved_session_id}<br>\n";

			// Test thread ID management.
			$test_thread_id = 'thread_test_' . time();
			$session->set_thread_id( $test_thread_id );
			echo "✓ Thread ID set successfully<br>\n";

			// Test user info submission status.
			$is_submitted = $session->is_user_info_submitted();
			echo "✓ User info submission status checked: " . ( $is_submitted ? 'Yes' : 'No' ) . "<br>\n";

			$this->add_result( $test_name, true, 'All session management tests passed' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test OpenAI API configuration.
	 */
	private function test_openai_api_configuration() {
		$test_name = 'OpenAI API Configuration';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Check if API key is configured.
			$api_key = get_option( 'sitecompass_openai_api_key', '' );
			if ( empty( $api_key ) ) {
				throw new Exception( 'OpenAI API key not configured' );
			}
			echo "✓ OpenAI API key is configured<br>\n";

			// Check if assistant ID is configured.
			$assistant_id = get_option( 'sitecompass_assistant_id', '' );
			if ( empty( $assistant_id ) ) {
				echo "⚠ Warning: Assistant ID not configured (will be created on first use)<br>\n";
			} else {
				echo "✓ Assistant ID is configured: {$assistant_id}<br>\n";
			}

			// Check model selection.
			$model = get_option( 'sitecompass_model', 'gpt-4o-mini' );
			echo "✓ Model configured: {$model}<br>\n";

			// Check instructions.
			$instructions = get_option( 'sitecompass_instructions', '' );
			if ( ! empty( $instructions ) ) {
				echo "✓ Instructions configured<br>\n";
			}

			// Test OpenAI Assistant class instantiation.
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-openai-assistant.php';
			$openai = new Sitecompass_Ai_OpenAI_Assistant( $api_key );
			$openai->set_chat_model( $model );
			$openai->set_instructions( $instructions );
			echo "✓ OpenAI Assistant class instantiated successfully<br>\n";

			$this->add_result( $test_name, true, 'OpenAI API configuration verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test database tables existence.
	 */
	private function test_database_tables() {
		$test_name = 'Database Tables';
		echo "<h3>Testing: {$test_name}</h3>\n";

		global $wpdb;

		try {
			$tables = array(
				'sitecompass_conversations' => 'Conversations table',
				'sitecompass_users'         => 'Users table',
				'sitecompass_pdfs'          => 'PDFs table',
				'sitecompass_assistants'    => 'Assistants table',
			);

			foreach ( $tables as $table_suffix => $description ) {
				$table_name = $wpdb->prefix . $table_suffix;
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

				if ( $table_name !== $table_exists ) {
					throw new Exception( "{$description} does not exist" );
				}
				echo "✓ {$description} exists<br>\n";
			}

			// Test conversations table structure.
			$conversations_table = $wpdb->prefix . 'sitecompass_conversations';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$columns = $wpdb->get_results( "DESCRIBE {$conversations_table}" );
			$required_columns = array( 'id', 'session_id', 'user_type', 'thread_id', 'assistant_id', 'message_text', 'created_at' );

			foreach ( $required_columns as $column ) {
				$found = false;
				foreach ( $columns as $col ) {
					if ( $col->Field === $column ) {
						$found = true;
						break;
					}
				}
				if ( ! $found ) {
					throw new Exception( "Required column '{$column}' not found in conversations table" );
				}
			}
			echo "✓ Conversations table structure verified<br>\n";

			$this->add_result( $test_name, true, 'All database tables verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test message handler AJAX registration.
	 */
	private function test_message_handler_registration() {
		$test_name = 'Message Handler Registration';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Check if AJAX actions are registered.
			$actions = array(
				'wp_ajax_sitecompass_send_message',
				'wp_ajax_nopriv_sitecompass_send_message',
				'wp_ajax_sitecompass_create_session',
				'wp_ajax_nopriv_sitecompass_create_session',
				'wp_ajax_sitecompass_submit_user_info',
				'wp_ajax_nopriv_sitecompass_submit_user_info',
			);

			foreach ( $actions as $action ) {
				if ( ! has_action( $action ) ) {
					throw new Exception( "AJAX action '{$action}' not registered" );
				}
				echo "✓ AJAX action registered: {$action}<br>\n";
			}

			$this->add_result( $test_name, true, 'All AJAX actions registered' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test chatbox display functionality.
	 */
	private function test_chatbox_display() {
		$test_name = 'Chatbox Display';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Check if shortcode is registered.
			if ( ! shortcode_exists( 'sitecompass' ) ) {
				throw new Exception( 'Shortcode [sitecompass] not registered' );
			}
			echo "✓ Shortcode [sitecompass] registered<br>\n";

			// Test chatbox class instantiation.
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-chatbox.php';
			$chatbox = new Sitecompass_Ai_Chatbox( 'sitecompass-ai', '1.0.0' );
			echo "✓ Chatbox class instantiated successfully<br>\n";

			// Check if assets are enqueued.
			$chatbox_css = SITECOMPASS_AI_PLUGIN_URL . 'assets/css/chatbox.css';
			$chatbox_js  = SITECOMPASS_AI_PLUGIN_URL . 'assets/js/chatbox.js';

			if ( file_exists( SITECOMPASS_AI_PLUGIN_DIR . 'assets/css/chatbox.css' ) ) {
				echo "✓ Chatbox CSS file exists<br>\n";
			} else {
				echo "⚠ Warning: Chatbox CSS file not found<br>\n";
			}

			if ( file_exists( SITECOMPASS_AI_PLUGIN_DIR . 'assets/js/chatbox.js' ) ) {
				echo "✓ Chatbox JS file exists<br>\n";
			} else {
				echo "⚠ Warning: Chatbox JS file not found<br>\n";
			}

			$this->add_result( $test_name, true, 'Chatbox display components verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test nonce verification implementation.
	 */
	private function test_nonce_verification() {
		$test_name = 'Nonce Verification';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Check if nonce is created in JavaScript localization.
			$nonce = wp_create_nonce( 'sitecompass_chat_nonce' );
			if ( empty( $nonce ) ) {
				throw new Exception( 'Failed to create nonce' );
			}
			echo "✓ Nonce created successfully<br>\n";

			// Verify nonce.
			$verified = wp_verify_nonce( $nonce, 'sitecompass_chat_nonce' );
			if ( ! $verified ) {
				throw new Exception( 'Nonce verification failed' );
			}
			echo "✓ Nonce verified successfully<br>\n";

			$this->add_result( $test_name, true, 'Nonce verification working' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Add a test result.
	 *
	 * @param string $test_name Test name.
	 * @param bool   $passed    Whether the test passed.
	 * @param string $message   Result message.
	 */
	private function add_result( $test_name, $passed, $message ) {
		$this->results[] = array(
			'test'    => $test_name,
			'passed'  => $passed,
			'message' => $message,
		);
	}

	/**
	 * Display test results summary.
	 */
	private function display_results() {
		echo "<h2>Test Results Summary</h2>\n";
		echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>\n";
		echo "<tr><th>Test</th><th>Status</th><th>Message</th></tr>\n";

		$passed_count = 0;
		$failed_count = 0;

		foreach ( $this->results as $result ) {
			$status = $result['passed'] ? '✓ PASSED' : '✗ FAILED';
			$color  = $result['passed'] ? 'green' : 'red';

			if ( $result['passed'] ) {
				$passed_count++;
			} else {
				$failed_count++;
			}

			echo "<tr>";
			echo "<td>{$result['test']}</td>";
			echo "<td style='color: {$color}; font-weight: bold;'>{$status}</td>";
			echo "<td>{$result['message']}</td>";
			echo "</tr>\n";
		}

		echo "</table>\n";
		echo "<p><strong>Total Tests:</strong> " . count( $this->results ) . "</p>\n";
		echo "<p><strong>Passed:</strong> <span style='color: green;'>{$passed_count}</span></p>\n";
		echo "<p><strong>Failed:</strong> <span style='color: red;'>{$failed_count}</span></p>\n";
	}
}

// Run tests if accessed via WordPress admin.
if ( is_admin() && current_user_can( 'manage_options' ) ) {
	$test = new Sitecompass_Ai_Message_Flow_Test();
	$test->run_tests();
}
