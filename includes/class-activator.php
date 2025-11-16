<?php
/**
 * Fired during plugin activation.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/includes
 */

/**
 * Fired during plugin activation.
 */
class Sitecompass_Ai_Activator {
	/**
	 * Activate the plugin.
	 *
	 * @since 1.0.0
	 */
	public static function activate() {
		// Load database class.
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-database.php';

		// Create database tables.
		Sitecompass_Ai_Database::create_pdfs_table();
		Sitecompass_Ai_Database::create_users_table();
		Sitecompass_Ai_Database::create_conversations_table();
		Sitecompass_Ai_Database::create_assistants_table();
	}
}
