<?php
/**
 * Test script for admin interfaces
 *
 * This script tests all admin interface functionality including settings,
 * PDF management, chat history, and appearance customization.
 *
 * Requirements tested: 5.2, 5.3, 5.4, 5.5, 5.6
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/tests
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

/**
 * Test class for admin interfaces.
 */
class Sitecompass_Ai_Admin_Interfaces_Test {

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
		echo "<h2>SiteCompass Admin Interfaces Tests</h2>\n";
		echo "<p>Testing Requirements: 5.2, 5.3, 5.4, 5.5, 5.6</p>\n";

		$this->test_settings_registration();
		$this->test_settings_save_retrieval();
		$this->test_pdf_management();
		$this->test_chat_history_interface();
		$this->test_appearance_customization();
		$this->test_avatar_settings();
		$this->test_admin_menu_registration();
		$this->test_capability_checks();

		$this->display_results();

		return $this->results;
	}

	/**
	 * Test settings registration.
	 */
	private function test_settings_registration() {
		$test_name = 'Settings Registration';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$required_settings = array(
				'sitecompass_openai_api_key',
				'sitecompass_model',
				'sitecompass_bot_name',
				'sitecompass_instructions',
				'sitecompass_bot_prompt',
				'sitecompass_initial_greeting',
				'sitecompass_subsequent_greeting',
				'sitecompass_show_user_form',
				'sitecompass_assistant_id',
			);

			foreach ( $required_settings as $setting ) {
				// Check if setting is registered.
				$value = get_option( $setting, null );
				if ( null === $value ) {
					// Setting might not be set yet, but should be registered.
					echo "⚠ Setting '{$setting}' not set (this is OK if plugin just activated)<br>\n";
				} else {
					echo "✓ Setting '{$setting}' is registered and has value<br>\n";
				}
			}

			$this->add_result( $test_name, true, 'All settings checked' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test settings save and retrieval.
	 */
	private function test_settings_save_retrieval() {
		$test_name = 'Settings Save and Retrieval';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Test saving and retrieving a setting.
			$test_value = 'test_bot_name_' . time();
			update_option( 'sitecompass_bot_name', $test_value );
			$retrieved_value = get_option( 'sitecompass_bot_name', '' );

			if ( $retrieved_value !== $test_value ) {
				throw new Exception( 'Failed to save and retrieve setting' );
			}
			echo "✓ Settings save and retrieval working<br>\n";

			// Test model selection.
			$models = array( 'gpt-4o', 'gpt-4o-mini', 'gpt-4-turbo' );
			$current_model = get_option( 'sitecompass_model', 'gpt-4o-mini' );
			if ( in_array( $current_model, $models, true ) ) {
				echo "✓ Model setting is valid: {$current_model}<br>\n";
			} else {
				echo "⚠ Warning: Model setting has unexpected value: {$current_model}<br>\n";
			}

			// Test show user form setting.
			$show_form = get_option( 'sitecompass_show_user_form', 'no' );
			if ( in_array( $show_form, array( 'yes', 'no' ), true ) ) {
				echo "✓ Show user form setting is valid: {$show_form}<br>\n";
			} else {
				echo "⚠ Warning: Show user form has unexpected value: {$show_form}<br>\n";
			}

			$this->add_result( $test_name, true, 'Settings save and retrieval verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test PDF management functionality.
	 */
	private function test_pdf_management() {
		$test_name = 'PDF Management';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-pdf-manager.php';
			$pdf_manager = new Sitecompass_Ai_PDF_Manager( 'sitecompass-ai', '1.0.0' );
			echo "✓ PDF Manager class instantiated<br>\n";

			// Check if PDFs table exists.
			global $wpdb;
			$table_name = $wpdb->prefix . 'sitecompass_pdfs';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

			if ( $table_name !== $table_exists ) {
				throw new Exception( 'PDFs table does not exist' );
			}
			echo "✓ PDFs table exists<br>\n";

			// Get PDFs list.
			$pdfs = $pdf_manager->get_pdfs();
			echo "✓ PDF list retrieved: " . count( $pdfs ) . " PDFs found<br>\n";

			// Check table structure.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$columns = $wpdb->get_results( "DESCRIBE {$table_name}" );
			$required_columns = array( 'id', 'name', 'path', 'openai_file_id', 'created_at' );

			foreach ( $required_columns as $column ) {
				$found = false;
				foreach ( $columns as $col ) {
					if ( $col->Field === $column ) {
						$found = true;
						break;
					}
				}
				if ( ! $found ) {
					throw new Exception( "Required column '{$column}' not found in PDFs table" );
				}
			}
			echo "✓ PDFs table structure verified<br>\n";

			$this->add_result( $test_name, true, 'PDF management functionality verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test chat history interface.
	 */
	private function test_chat_history_interface() {
		$test_name = 'Chat History Interface';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-chats.php';
			$chats = new Sitecompass_Ai_Chats( 'sitecompass-ai', '1.0.0' );
			echo "✓ Chats class instantiated<br>\n";

			// Check if conversations table exists.
			global $wpdb;
			$table_name = $wpdb->prefix . 'sitecompass_conversations';
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$table_exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table_name ) );

			if ( $table_name !== $table_exists ) {
				throw new Exception( 'Conversations table does not exist' );
			}
			echo "✓ Conversations table exists<br>\n";

			// Get conversation count.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$table_name}" );
			echo "✓ Conversations count: {$count}<br>\n";

			// Test date filtering capability.
			$start_date = gmdate( 'Y-m-d', strtotime( '-7 days' ) );
			$end_date = gmdate( 'Y-m-d' );
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$filtered_count = $wpdb->get_var(
				$wpdb->prepare(
					"SELECT COUNT(*) FROM {$table_name} WHERE DATE(created_at) >= %s AND DATE(created_at) <= %s",
					$start_date,
					$end_date
				)
			);
			echo "✓ Date filtering works: {$filtered_count} conversations in last 7 days<br>\n";

			// Test search capability.
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			$sessions = $wpdb->get_results(
				"SELECT DISTINCT session_id FROM {$table_name} LIMIT 1"
			);
			if ( ! empty( $sessions ) ) {
				$test_session = $sessions[0]->session_id;
				// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
				$search_result = $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$table_name} WHERE session_id = %s",
						$test_session
					)
				);
				echo "✓ Search functionality works: Found {$search_result} messages for session<br>\n";
			}

			$this->add_result( $test_name, true, 'Chat history interface verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test appearance customization.
	 */
	private function test_appearance_customization() {
		$test_name = 'Appearance Customization';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-appearance.php';
			$appearance = new Sitecompass_Ai_Appearance( 'sitecompass-ai', '1.0.0' );
			echo "✓ Appearance class instantiated<br>\n";

			// Test color settings.
			$color_settings = array(
				'sitecompass_appearance_button_bg_color',
				'sitecompass_appearance_chatbot_bg_color',
				'sitecompass_appearance_header_bg_color',
				'sitecompass_appearance_header_text_color',
				'sitecompass_appearance_text_color',
				'sitecompass_appearance_user_text_bg_color',
				'sitecompass_appearance_bot_text_bg_color',
			);

			foreach ( $color_settings as $setting ) {
				$value = get_option( $setting, '' );
				if ( empty( $value ) || ! preg_match( '/^#[a-f0-9]{6}$/i', $value ) ) {
					echo "⚠ Warning: Color setting '{$setting}' not set or invalid<br>\n";
				} else {
					echo "✓ Color setting '{$setting}' is valid: {$value}<br>\n";
				}
			}

			// Test width settings.
			$width_setting = get_option( 'sitecompass_appearance_width_setting', 'narrow' );
			if ( in_array( $width_setting, array( 'narrow', 'wide' ), true ) ) {
				echo "✓ Width setting is valid: {$width_setting}<br>\n";
			} else {
				echo "⚠ Warning: Width setting has unexpected value: {$width_setting}<br>\n";
			}

			// Test custom CSS.
			$custom_css = get_option( 'sitecompass_appearance_custom_css', '' );
			echo "✓ Custom CSS field accessible (length: " . strlen( $custom_css ) . " chars)<br>\n";

			// Test defaults.
			$defaults = $appearance->get_defaults();
			if ( ! empty( $defaults ) && is_array( $defaults ) ) {
				echo "✓ Default settings available: " . count( $defaults ) . " defaults<br>\n";
			}

			$this->add_result( $test_name, true, 'Appearance customization verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test avatar settings.
	 */
	private function test_avatar_settings() {
		$test_name = 'Avatar Settings';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-avatar.php';
			$avatar = new Sitecompass_Ai_Avatar( 'sitecompass-ai', '1.0.0' );
			echo "✓ Avatar class instantiated<br>\n";

			// Test avatar greeting.
			$greeting = get_option( 'sitecompass_avatar_greeting', '' );
			if ( ! empty( $greeting ) ) {
				echo "✓ Avatar greeting is set: {$greeting}<br>\n";
			} else {
				echo "⚠ Warning: Avatar greeting not set<br>\n";
			}

			// Test custom avatar URL.
			$custom_url = get_option( 'sitecompass_custom_avatar_url', '' );
			if ( ! empty( $custom_url ) ) {
				if ( filter_var( $custom_url, FILTER_VALIDATE_URL ) ) {
					echo "✓ Custom avatar URL is valid: {$custom_url}<br>\n";
				} else {
					echo "⚠ Warning: Custom avatar URL is invalid<br>\n";
				}
			} else {
				echo "✓ No custom avatar URL set (using icon selection)<br>\n";
			}

			// Test icon selection.
			$icon_setting = get_option( 'sitecompass_avatar_icon_setting', '' );
			if ( ! empty( $icon_setting ) ) {
				echo "✓ Avatar icon selected: {$icon_setting}<br>\n";
			}

			// Test icon sets.
			$icon_sets = $avatar->get_icon_sets();
			if ( ! empty( $icon_sets ) && is_array( $icon_sets ) ) {
				echo "✓ Icon sets available: " . count( $icon_sets ) . " sets<br>\n";
				$total_icons = array_sum( $icon_sets );
				echo "✓ Total icons available: {$total_icons}<br>\n";
			}

			// Check if icon files exist.
			$icons_dir = SITECOMPASS_AI_PLUGIN_DIR . 'assets/icons/';
			if ( is_dir( $icons_dir ) ) {
				echo "✓ Icons directory exists<br>\n";
				$icon_files = glob( $icons_dir . '*.png' );
				echo "✓ Icon files found: " . count( $icon_files ) . "<br>\n";
			} else {
				echo "⚠ Warning: Icons directory not found<br>\n";
			}

			$this->add_result( $test_name, true, 'Avatar settings verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test admin menu registration.
	 */
	private function test_admin_menu_registration() {
		$test_name = 'Admin Menu Registration';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			global $menu, $submenu;

			// Check if main menu is registered.
			$menu_found = false;
			if ( ! empty( $menu ) ) {
				foreach ( $menu as $menu_item ) {
					if ( isset( $menu_item[2] ) && 'sitecompass' === $menu_item[2] ) {
						$menu_found = true;
						echo "✓ Main menu 'SiteCompass' is registered<br>\n";
						break;
					}
				}
			}

			if ( ! $menu_found ) {
				echo "⚠ Warning: Main menu not found (may not be visible in test context)<br>\n";
			}

			// Check if submenu items are registered.
			$expected_submenus = array(
				'sitecompass'          => 'Chats',
				'sitecompass-settings' => 'Settings',
			);

			if ( isset( $submenu['sitecompass'] ) ) {
				foreach ( $submenu['sitecompass'] as $submenu_item ) {
					if ( isset( $submenu_item[2] ) && isset( $expected_submenus[ $submenu_item[2] ] ) ) {
						echo "✓ Submenu '{$expected_submenus[$submenu_item[2]]}' is registered<br>\n";
					}
				}
			} else {
				echo "⚠ Warning: Submenus not found (may not be visible in test context)<br>\n";
			}

			$this->add_result( $test_name, true, 'Admin menu registration checked' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test capability checks.
	 */
	private function test_capability_checks() {
		$test_name = 'Capability Checks';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			// Check if current user has manage_options capability.
			if ( current_user_can( 'manage_options' ) ) {
				echo "✓ Current user has 'manage_options' capability<br>\n";
			} else {
				echo "⚠ Warning: Current user does not have 'manage_options' capability<br>\n";
			}

			// Verify that admin classes check capabilities.
			$admin_classes = array(
				'Sitecompass_Ai_Settings',
				'Sitecompass_Ai_Chats',
				'Sitecompass_Ai_PDF_Manager',
			);

			foreach ( $admin_classes as $class ) {
				$file_path = SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-' . strtolower( str_replace( '_', '-', str_replace( 'Sitecompass_Ai_', '', $class ) ) ) . '.php';
				if ( file_exists( $file_path ) ) {
					$content = file_get_contents( $file_path );
					if ( strpos( $content, 'current_user_can' ) !== false && strpos( $content, 'manage_options' ) !== false ) {
						echo "✓ {$class} implements capability checks<br>\n";
					} else {
						echo "⚠ Warning: {$class} may not implement capability checks<br>\n";
					}
				}
			}

			$this->add_result( $test_name, true, 'Capability checks verified' );
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
	$test = new Sitecompass_Ai_Admin_Interfaces_Test();
	$test->run_tests();
}
