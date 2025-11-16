<?php
/**
 * Database management class.
 *
 * Handles creation and management of custom database tables.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/includes
 */

/**
 * Database management class.
 */
class Sitecompass_Ai_Database {

	/**
	 * Create the PDFs table.
	 *
	 * @since 1.0.0
	 */
	public static function create_pdfs_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'sitecompass_pdfs';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			name VARCHAR(255) NOT NULL,
			path VARCHAR(1000) NOT NULL,
			openai_file_id VARCHAR(255) NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_openai_file_id (openai_file_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the users table.
	 *
	 * @since 1.0.0
	 */
	public static function create_users_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'sitecompass_users';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			user_name VARCHAR(255) NULL,
			email VARCHAR(255) NULL,
			phone VARCHAR(255) NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_email (email)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the conversations table.
	 *
	 * @since 1.0.0
	 */
	public static function create_conversations_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'sitecompass_conversations';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			session_id VARCHAR(255) NOT NULL,
			user_id BIGINT UNSIGNED,
			page_id BIGINT UNSIGNED,
			user_type ENUM('assistant', 'user') NOT NULL,
			thread_id VARCHAR(255),
			assistant_id VARCHAR(255),
			message_text TEXT,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_session_id (session_id),
			INDEX idx_thread_id (thread_id),
			INDEX idx_created_at (created_at)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}

	/**
	 * Create the assistants table.
	 *
	 * @since 1.0.0
	 */
	public static function create_assistants_table() {
		global $wpdb;

		$table_name      = $wpdb->prefix . 'sitecompass_assistants';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
			id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
			openai_assistant_id VARCHAR(255) NOT NULL UNIQUE,
			name VARCHAR(255) NOT NULL,
			openai_file_ids TEXT NULL,
			created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
			INDEX idx_openai_assistant_id (openai_assistant_id)
		) $charset_collate;";

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';
		dbDelta( $sql );
	}
}
