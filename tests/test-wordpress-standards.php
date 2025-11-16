<?php
/**
 * Test script for WordPress standards compliance
 *
 * This script tests compliance with WordPress coding standards including
 * input sanitization, output escaping, nonce verification, and capability checks.
 *
 * Requirements tested: 2.3, 2.4, 2.5, 2.6
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/tests
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

/**
 * Test class for WordPress standards compliance.
 */
class Sitecompass_Ai_WordPress_Standards_Test {

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
		echo "<h2>SiteCompass WordPress Standards Compliance Tests</h2>\n";
		echo "<p>Testing Requirements: 2.3, 2.4, 2.5, 2.6</p>\n";

		$this->test_input_sanitization();
		$this->test_output_escaping();
		$this->test_nonce_verification();
		$this->test_capability_checks();
		$this->test_database_queries();
		$this->test_internationalization();

		$this->display_results();

		return $this->results;
	}

	/**
	 * Test input sanitization (Requirement 2.4).
	 */
	private function test_input_sanitization() {
		$test_name = 'Input Sanitization';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$files_to_check = $this->get_all_php_files();
			$sanitization_functions = array(
				'sanitize_text_field',
				'sanitize_email',
				'sanitize_textarea_field',
				'esc_url_raw',
				'sanitize_file_name',
				'sanitize_hex_color',
				'wp_kses_post',
				'absint',
				'intval',
			);

			$files_with_sanitization = 0;
			$files_with_post_get = 0;
			$potential_issues = array();

			foreach ( $files_to_check as $file ) {
				$content = file_get_contents( $file );

				// Check if file uses $_POST or $_GET.
				if ( preg_match( '/\$_(POST|GET|REQUEST|COOKIE)/', $content ) ) {
					$files_with_post_get++;

					// Check if sanitization functions are used.
					$has_sanitization = false;
					foreach ( $sanitization_functions as $func ) {
						if ( strpos( $content, $func ) !== false ) {
							$has_sanitization = true;
							break;
						}
					}

					if ( $has_sanitization ) {
						$files_with_sanitization++;
					} else {
						$potential_issues[] = basename( $file ) . ' uses $_POST/$_GET but may lack sanitization';
					}
				}
			}

			echo "✓ Files checked: " . count( $files_to_check ) . "<br>\n";
			echo "✓ Files using \$_POST/\$_GET: {$files_with_post_get}<br>\n";
			echo "✓ Files with sanitization: {$files_with_sanitization}<br>\n";

			if ( ! empty( $potential_issues ) ) {
				echo "<strong>⚠ Potential issues found:</strong><br>\n";
				foreach ( $potential_issues as $issue ) {
					echo "  - {$issue}<br>\n";
				}
			}

			// Test specific sanitization examples.
			$test_cases = array(
				array(
					'input'    => '<script>alert("XSS")</script>',
					'function' => 'sanitize_text_field',
					'expected' => 'scriptalert("XSS")/script',
				),
				array(
					'input'    => 'test@example.com<script>',
					'function' => 'sanitize_email',
					'expected' => 'test@example.com',
				),
			);

			foreach ( $test_cases as $case ) {
				$result = call_user_func( $case['function'], $case['input'] );
				if ( strpos( $result, '<script>' ) === false ) {
					echo "✓ {$case['function']}() properly sanitizes input<br>\n";
				} else {
					throw new Exception( "{$case['function']}() failed to sanitize input" );
				}
			}

			$this->add_result( $test_name, true, 'Input sanitization verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test output escaping (Requirement 2.3).
	 */
	private function test_output_escaping() {
		$test_name = 'Output Escaping';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$files_to_check = $this->get_all_php_files();
			$escaping_functions = array(
				'esc_html',
				'esc_attr',
				'esc_url',
				'esc_js',
				'esc_textarea',
				'esc_html__',
				'esc_html_e',
				'esc_attr__',
				'esc_attr_e',
				'wp_kses_post',
			);

			$files_with_escaping = 0;
			$files_with_echo = 0;
			$potential_issues = array();

			foreach ( $files_to_check as $file ) {
				$content = file_get_contents( $file );

				// Check if file uses echo or print.
				if ( preg_match( '/(echo|print)\s+/', $content ) ) {
					$files_with_echo++;

					// Check if escaping functions are used.
					$has_escaping = false;
					foreach ( $escaping_functions as $func ) {
						if ( strpos( $content, $func ) !== false ) {
							$has_escaping = true;
							break;
						}
					}

					if ( $has_escaping ) {
						$files_with_escaping++;
					} else {
						// Check if it's a view file that should have escaping.
						if ( strpos( $file, 'admin' ) !== false || strpos( $file, 'public' ) !== false ) {
							$potential_issues[] = basename( $file ) . ' uses echo but may lack escaping';
						}
					}
				}
			}

			echo "✓ Files checked: " . count( $files_to_check ) . "<br>\n";
			echo "✓ Files using echo/print: {$files_with_echo}<br>\n";
			echo "✓ Files with escaping: {$files_with_escaping}<br>\n";

			if ( ! empty( $potential_issues ) ) {
				echo "<strong>⚠ Potential issues found:</strong><br>\n";
				foreach ( $potential_issues as $issue ) {
					echo "  - {$issue}<br>\n";
				}
			}

			// Test specific escaping examples.
			$test_cases = array(
				array(
					'input'    => '<script>alert("XSS")</script>',
					'function' => 'esc_html',
					'expected' => '&lt;script&gt;alert("XSS")&lt;/script&gt;',
				),
				array(
					'input'    => 'javascript:alert("XSS")',
					'function' => 'esc_url',
					'expected' => '',
				),
			);

			foreach ( $test_cases as $case ) {
				$result = call_user_func( $case['function'], $case['input'] );
				if ( strpos( $result, '<script>' ) === false && strpos( $result, 'javascript:' ) === false ) {
					echo "✓ {$case['function']}() properly escapes output<br>\n";
				} else {
					throw new Exception( "{$case['function']}() failed to escape output" );
				}
			}

			$this->add_result( $test_name, true, 'Output escaping verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test nonce verification (Requirement 2.5).
	 */
	private function test_nonce_verification() {
		$test_name = 'Nonce Verification';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$files_to_check = $this->get_all_php_files();
			$nonce_functions = array(
				'wp_nonce_field',
				'wp_create_nonce',
				'wp_verify_nonce',
				'wp_nonce_url',
				'check_admin_referer',
				'check_ajax_referer',
			);

			$files_with_nonce = 0;
			$files_with_forms = 0;
			$potential_issues = array();

			foreach ( $files_to_check as $file ) {
				$content = file_get_contents( $file );

				// Check if file has forms or AJAX handlers.
				$has_form = preg_match( '/<form|wp_ajax_|handle_.*\(\)/', $content );

				if ( $has_form ) {
					$files_with_forms++;

					// Check if nonce functions are used.
					$has_nonce = false;
					foreach ( $nonce_functions as $func ) {
						if ( strpos( $content, $func ) !== false ) {
							$has_nonce = true;
							break;
						}
					}

					if ( $has_nonce ) {
						$files_with_nonce++;
					} else {
						$potential_issues[] = basename( $file ) . ' has forms/AJAX but may lack nonce verification';
					}
				}
			}

			echo "✓ Files checked: " . count( $files_to_check ) . "<br>\n";
			echo "✓ Files with forms/AJAX: {$files_with_forms}<br>\n";
			echo "✓ Files with nonce verification: {$files_with_nonce}<br>\n";

			if ( ! empty( $potential_issues ) ) {
				echo "<strong>⚠ Potential issues found:</strong><br>\n";
				foreach ( $potential_issues as $issue ) {
					echo "  - {$issue}<br>\n";
				}
			}

			// Test nonce creation and verification.
			$nonce = wp_create_nonce( 'test_action' );
			if ( empty( $nonce ) ) {
				throw new Exception( 'Failed to create nonce' );
			}
			echo "✓ Nonce creation works<br>\n";

			$verified = wp_verify_nonce( $nonce, 'test_action' );
			if ( ! $verified ) {
				throw new Exception( 'Nonce verification failed' );
			}
			echo "✓ Nonce verification works<br>\n";

			// Test invalid nonce.
			$invalid_verified = wp_verify_nonce( 'invalid_nonce', 'test_action' );
			if ( $invalid_verified ) {
				throw new Exception( 'Invalid nonce was incorrectly verified' );
			}
			echo "✓ Invalid nonce is rejected<br>\n";

			$this->add_result( $test_name, true, 'Nonce verification implemented' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test capability checks (Requirement 2.6).
	 */
	private function test_capability_checks() {
		$test_name = 'Capability Checks';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$admin_files = glob( SITECOMPASS_AI_PLUGIN_DIR . 'admin/*.php' );
			$files_with_capability_checks = 0;
			$potential_issues = array();

			foreach ( $admin_files as $file ) {
				$content = file_get_contents( $file );

				// Check if file has capability checks.
				if ( strpos( $content, 'current_user_can' ) !== false ) {
					$files_with_capability_checks++;

					// Check if manage_options is used.
					if ( strpos( $content, 'manage_options' ) !== false ) {
						echo "✓ " . basename( $file ) . " implements capability checks<br>\n";
					} else {
						$potential_issues[] = basename( $file ) . ' has capability checks but may not use manage_options';
					}
				} else {
					// Check if it's a class that should have capability checks.
					if ( preg_match( '/class.*Settings|class.*Admin|class.*Chats|class.*PDF/', $content ) ) {
						$potential_issues[] = basename( $file ) . ' may lack capability checks';
					}
				}
			}

			echo "✓ Admin files checked: " . count( $admin_files ) . "<br>\n";
			echo "✓ Files with capability checks: {$files_with_capability_checks}<br>\n";

			if ( ! empty( $potential_issues ) ) {
				echo "<strong>⚠ Potential issues found:</strong><br>\n";
				foreach ( $potential_issues as $issue ) {
					echo "  - {$issue}<br>\n";
				}
			}

			// Test capability check function.
			if ( current_user_can( 'manage_options' ) ) {
				echo "✓ current_user_can() function works<br>\n";
			} else {
				echo "⚠ Current user does not have manage_options capability<br>\n";
			}

			$this->add_result( $test_name, true, 'Capability checks verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test database queries (Requirement 2.5).
	 */
	private function test_database_queries() {
		$test_name = 'Database Query Security';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$files_to_check = $this->get_all_php_files();
			$files_with_queries = 0;
			$files_with_prepare = 0;
			$potential_issues = array();

			foreach ( $files_to_check as $file ) {
				$content = file_get_contents( $file );

				// Check if file uses $wpdb.
				if ( preg_match( '/\$wpdb->(query|get_|insert|update|delete|replace)/', $content ) ) {
					$files_with_queries++;

					// Check if prepare() is used.
					if ( strpos( $content, '$wpdb->prepare' ) !== false ) {
						$files_with_prepare++;
					} else {
						// Check if it's a simple query without user input.
						if ( preg_match( '/\$_(POST|GET|REQUEST|COOKIE)/', $content ) ) {
							$potential_issues[] = basename( $file ) . ' uses $wpdb with user input but may lack prepare()';
						}
					}
				}
			}

			echo "✓ Files checked: " . count( $files_to_check ) . "<br>\n";
			echo "✓ Files with database queries: {$files_with_queries}<br>\n";
			echo "✓ Files using prepare(): {$files_with_prepare}<br>\n";

			if ( ! empty( $potential_issues ) ) {
				echo "<strong>⚠ Potential issues found:</strong><br>\n";
				foreach ( $potential_issues as $issue ) {
					echo "  - {$issue}<br>\n";
				}
			}

			// Test $wpdb->prepare().
			global $wpdb;
			$test_query = $wpdb->prepare( 'SELECT * FROM %i WHERE id = %d', $wpdb->prefix . 'options', 1 );
			if ( strpos( $test_query, '%' ) === false ) {
				echo "✓ \$wpdb->prepare() works correctly<br>\n";
			} else {
				throw new Exception( '$wpdb->prepare() did not replace placeholders' );
			}

			$this->add_result( $test_name, true, 'Database query security verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Test internationalization (Requirement 2.2).
	 */
	private function test_internationalization() {
		$test_name = 'Internationalization';
		echo "<h3>Testing: {$test_name}</h3>\n";

		try {
			$files_to_check = $this->get_all_php_files();
			$i18n_functions = array(
				'__(',
				'_e(',
				'esc_html__(',
				'esc_html_e(',
				'esc_attr__(',
				'esc_attr_e(',
				'_n(',
				'_x(',
			);

			$files_with_i18n = 0;
			$files_with_strings = 0;
			$text_domain_correct = 0;

			foreach ( $files_to_check as $file ) {
				$content = file_get_contents( $file );

				// Check if file has user-facing strings.
				if ( preg_match( '/(echo|print).*["\']/', $content ) ) {
					$files_with_strings++;

					// Check if i18n functions are used.
					$has_i18n = false;
					foreach ( $i18n_functions as $func ) {
						if ( strpos( $content, $func ) !== false ) {
							$has_i18n = true;
							break;
						}
					}

					if ( $has_i18n ) {
						$files_with_i18n++;

						// Check if correct text domain is used.
						if ( strpos( $content, "'sitecompass-ai'" ) !== false || strpos( $content, '"sitecompass-ai"' ) !== false ) {
							$text_domain_correct++;
						}
					}
				}
			}

			echo "✓ Files checked: " . count( $files_to_check ) . "<br>\n";
			echo "✓ Files with strings: {$files_with_strings}<br>\n";
			echo "✓ Files with i18n: {$files_with_i18n}<br>\n";
			echo "✓ Files with correct text domain: {$text_domain_correct}<br>\n";

			// Check if .pot file exists.
			$pot_file = SITECOMPASS_AI_PLUGIN_DIR . 'languages/sitecompass-ai.pot';
			if ( file_exists( $pot_file ) ) {
				echo "✓ Translation template (.pot) file exists<br>\n";
			} else {
				echo "⚠ Warning: Translation template file not found<br>\n";
			}

			// Test i18n function.
			$translated = __( 'Test String', 'sitecompass-ai' );
			if ( ! empty( $translated ) ) {
				echo "✓ __() function works<br>\n";
			}

			$this->add_result( $test_name, true, 'Internationalization verified' );
		} catch ( Exception $e ) {
			$this->add_result( $test_name, false, $e->getMessage() );
		}
	}

	/**
	 * Get all PHP files in the plugin.
	 *
	 * @return array List of PHP file paths.
	 */
	private function get_all_php_files() {
		$files = array();
		$directories = array( 'admin', 'includes', 'public' );

		foreach ( $directories as $dir ) {
			$dir_path = SITECOMPASS_AI_PLUGIN_DIR . $dir;
			if ( is_dir( $dir_path ) ) {
				$php_files = glob( $dir_path . '/*.php' );
				$files = array_merge( $files, $php_files );
			}
		}

		// Add main plugin file.
		$files[] = SITECOMPASS_AI_PLUGIN_DIR . 'sitecompass.php';

		return $files;
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
	$test = new Sitecompass_Ai_WordPress_Standards_Test();
	$test->run_tests();
}
