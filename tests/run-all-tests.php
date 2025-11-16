<?php
/**
 * Test runner for all SiteCompass tests
 *
 * This script runs all automated tests for the SiteCompass plugin.
 * Access via: /wp-admin/admin.php?page=sitecompass-run-tests
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/tests
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( 'Direct access not permitted.' );
}

/**
 * Test runner class.
 */
class Sitecompass_Ai_Test_Runner {

	/**
	 * Run all tests.
	 */
	public static function run_all_tests() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', 'sitecompass-ai' ) );
		}

		?>
		<!DOCTYPE html>
		<html>
		<head>
			<title>SiteCompass Test Runner</title>
			<style>
				body {
					font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
					margin: 20px;
					background: #f0f0f1;
				}
				.test-container {
					background: white;
					padding: 20px;
					margin-bottom: 20px;
					border-radius: 5px;
					box-shadow: 0 1px 3px rgba(0,0,0,0.1);
				}
				h1 {
					color: #1d2327;
					border-bottom: 2px solid #2271b1;
					padding-bottom: 10px;
				}
				h2 {
					color: #2271b1;
					margin-top: 30px;
				}
				.test-nav {
					background: #2271b1;
					color: white;
					padding: 15px;
					border-radius: 5px;
					margin-bottom: 20px;
				}
				.test-nav a {
					color: white;
					text-decoration: none;
					margin-right: 20px;
					padding: 5px 10px;
					background: rgba(255,255,255,0.2);
					border-radius: 3px;
				}
				.test-nav a:hover {
					background: rgba(255,255,255,0.3);
				}
				.manual-test-link {
					display: inline-block;
					margin: 10px 10px 10px 0;
					padding: 10px 15px;
					background: #2271b1;
					color: white;
					text-decoration: none;
					border-radius: 3px;
				}
				.manual-test-link:hover {
					background: #135e96;
				}
			</style>
		</head>
		<body>
			<div class="test-container">
				<h1>🧪 SiteCompass Test Runner</h1>
				<p>This page runs all automated tests for the SiteCompass plugin.</p>
				
				<div class="test-nav">
					<strong>Quick Navigation:</strong>
					<a href="#message-flow">Message Flow</a>
					<a href="#admin-interfaces">Admin Interfaces</a>
					<a href="#wordpress-standards">WordPress Standards</a>
					<a href="#manual-tests">Manual Tests</a>
				</div>
			</div>

			<div class="test-container" id="message-flow">
				<h2>📨 Test 1: Complete Message Flow</h2>
				<p><strong>Requirements:</strong> 6.4, 6.5, 6.6</p>
				<?php
				require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-message-flow.php';
				$message_flow_test = new Sitecompass_Ai_Message_Flow_Test();
				$message_flow_test->run_tests();
				?>
			</div>

			<div class="test-container" id="admin-interfaces">
				<h2>⚙️ Test 2: Admin Interfaces</h2>
				<p><strong>Requirements:</strong> 5.2, 5.3, 5.4, 5.5, 5.6</p>
				<?php
				require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-admin-interfaces.php';
				$admin_test = new Sitecompass_Ai_Admin_Interfaces_Test();
				$admin_test->run_tests();
				?>
			</div>

			<div class="test-container" id="wordpress-standards">
				<h2>✅ Test 3: WordPress Standards Compliance</h2>
				<p><strong>Requirements:</strong> 2.3, 2.4, 2.5, 2.6</p>
				<?php
				require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-wordpress-standards.php';
				$standards_test = new Sitecompass_Ai_WordPress_Standards_Test();
				$standards_test->run_tests();
				?>
			</div>

			<div class="test-container" id="manual-tests">
				<h2>📋 Manual Testing Checklists</h2>
				<p>The following manual tests should be performed to ensure complete functionality:</p>
				
				<h3>Available Manual Test Checklists:</h3>
				<a href="<?php echo esc_url( SITECOMPASS_AI_PLUGIN_URL . 'tests/MANUAL-TEST-MESSAGE-FLOW.md' ); ?>" class="manual-test-link" target="_blank">
					📨 Message Flow Manual Tests
				</a>
				<a href="<?php echo esc_url( SITECOMPASS_AI_PLUGIN_URL . 'tests/MANUAL-TEST-ADMIN-INTERFACES.md' ); ?>" class="manual-test-link" target="_blank">
					⚙️ Admin Interfaces Manual Tests
				</a>
				<a href="<?php echo esc_url( SITECOMPASS_AI_PLUGIN_URL . 'tests/MANUAL-TEST-WORDPRESS-STANDARDS.md' ); ?>" class="manual-test-link" target="_blank">
					✅ WordPress Standards Manual Tests
				</a>

				<h3>How to Use Manual Test Checklists:</h3>
				<ol>
					<li>Download or view the markdown files above</li>
					<li>Follow each test step carefully</li>
					<li>Mark tests as Pass or Fail</li>
					<li>Document any issues found</li>
					<li>Create bug reports for failures</li>
				</ol>
			</div>

			<div class="test-container">
				<h2>📊 Overall Test Summary</h2>
				<p>All automated tests have been completed. Review the results above for any failures.</p>
				<p><strong>Next Steps:</strong></p>
				<ul>
					<li>Review any failed tests and fix issues</li>
					<li>Perform manual testing using the checklists</li>
					<li>Test on different WordPress versions</li>
					<li>Test with different PHP versions</li>
					<li>Perform security audit</li>
					<li>Test with real OpenAI API key</li>
					<li>Test PDF upload and retrieval</li>
					<li>Test complete user flow from frontend</li>
				</ul>

				<p><a href="<?php echo esc_url( admin_url( 'admin.php?page=sitecompass' ) ); ?>" class="manual-test-link">
					← Back to SiteCompass
				</a></p>
			</div>
		</body>
		</html>
		<?php
	}

	/**
	 * Register test runner admin page.
	 */
	public static function register_test_page() {
		add_submenu_page(
			null, // Hidden from menu.
			__( 'Run Tests', 'sitecompass-ai' ),
			__( 'Run Tests', 'sitecompass-ai' ),
			'manage_options',
			'sitecompass-run-tests',
			array( __CLASS__, 'run_all_tests' )
		);
	}
}

// Register the test page.
add_action( 'admin_menu', array( 'Sitecompass_Ai_Test_Runner', 'register_test_page' ) );
