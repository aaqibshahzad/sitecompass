<?php
/**
 * Plugin Name: Site Compass
 * Plugin URI: https://sitecompass.ai
 * Description: A WordPress plugin that seamlessly integrates OpenAI's advanced language models into your website, enabling interactive chatbots, virtual assistants, and automated content support. Upload custom PDF documents to create a knowledge base, and let the AI assistant answer visitor questions based on your content.
 * Version: 1.0.0
 * Author: Site Compass Team
 * Author URI: https://sitecompass.ai 
 * License: GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: sitecompass
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'SITECOMPASS_AI_VERSION', '1.0.0' );
define( 'SITECOMPASS_AI_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'SITECOMPASS_AI_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'SITECOMPASS_AI_ICONS_URL', SITECOMPASS_AI_PLUGIN_URL . 'assets/icons/' );

/**
 * Activate the plugin.
 */
function activate_sitecompass_ai() {
	require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-activator.php';
	Sitecompass_Ai_Activator::activate();
}
register_activation_hook( __FILE__, 'activate_sitecompass_ai' );

/**
 * Deactivate the plugin.
 */
function deactivate_sitecompass_ai() {
	require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-deactivator.php';
	Sitecompass_Ai_Deactivator::deactivate();
}
register_deactivation_hook( __FILE__, 'deactivate_sitecompass_ai' );

/**
 * Load the main plugin class.
 */
require SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-plugin.php';

/**
 * Load test runner in admin (for development/testing).
 */
if ( is_admin() ) {
	require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/run-all-tests.php';
}

/**
 * Run the plugin.
 */
function run_sitecompass_ai() {
	$plugin = new Sitecompass_Ai_Plugin();
	$plugin->run();
}
run_sitecompass_ai();
