<?php
/**
 * The PDF management functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The PDF management functionality of the plugin.
 */
class Sitecompass_Ai_PDF_Manager {
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
	 * Handle PDF upload.
	 *
	 * @return string|bool Error message or true on success.
	 */
	public function handle_pdf_upload() {
		// Verify nonce.
		if ( ! isset( $_POST['sitecompass_pdf_upload_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['sitecompass_pdf_upload_nonce'] ) ), 'sitecompass_pdf_upload' ) ) {
			return __( 'Security check failed.', 'sitecompass' );
		}

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return __( 'You do not have permission to upload files.', 'sitecompass' );
		}

		// Check if files were uploaded.
		if ( ! isset( $_FILES['pdf_files'] ) || empty( $_FILES['pdf_files']['name'][0] ) ) {
			return __( 'No files selected.', 'sitecompass' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_pdfs';
		$uploaded_files = $_FILES['pdf_files'];
		$openai_api_key = get_option( 'sitecompass_openai_api_key', '' );

		if ( empty( $openai_api_key ) ) {
			return __( 'OpenAI API key is not configured.', 'sitecompass' );
		}

		require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-openai-assistant.php';
		$openai_assistant = new Sitecompass_Ai_OpenAI_Assistant( $openai_api_key );

		foreach ( $uploaded_files['name'] as $key => $value ) {
			if ( $uploaded_files['error'][ $key ] === 0 ) {
				// Validate file type.
				if ( $uploaded_files['type'][ $key ] !== 'application/pdf' ) {
					continue;
				}

				$upload_dir = wp_upload_dir();
				$file_name  = time() . '-' . sanitize_file_name( $uploaded_files['name'][ $key ] );
				$upload_path = $upload_dir['path'] . '/' . $file_name;
				$public_path = $upload_dir['url'] . '/' . $file_name;

				// Move uploaded file.
				if ( move_uploaded_file( $uploaded_files['tmp_name'][ $key ], $upload_path ) ) {
					// Upload to OpenAI.
					$openai_response = $openai_assistant->upload_file( $upload_path );

					if ( isset( $openai_response['id'] ) && ! empty( $openai_response['id'] ) ) {
						// Insert record into database.
						// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
						$wpdb->insert(
							$table_name,
							array(
								'name'           => sanitize_text_field( $uploaded_files['name'][ $key ] ),
								'path'           => esc_url_raw( $public_path ),
								'openai_file_id' => sanitize_text_field( $openai_response['id'] ),
								'created_at'     => current_time( 'mysql' ),
							),
							array( '%s', '%s', '%s', '%s' )
						);
					} else {
						// Delete local file if OpenAI upload failed.
						wp_delete_file( $upload_path );
						return __( 'Failed to upload file to OpenAI.', 'sitecompass' );
					}
				}
			}
		}

		return true;
	}

	/**
	 * Handle PDF deletion.
	 *
	 * @param int $pdf_id The PDF ID to delete.
	 * @return string|bool Error message or true on success.
	 */
	public function handle_pdf_deletion( $pdf_id ) {
		// Verify nonce.
		if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_GET['_wpnonce'] ) ), 'delete_pdf_' . $pdf_id ) ) {
			return __( 'Security check failed.', 'sitecompass' );
		}

		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return __( 'You do not have permission to delete files.', 'sitecompass' );
		}

		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_pdfs';

		// Get PDF file info.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$pdf_file = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM %i WHERE id = %d", $table_name, $pdf_id ), ARRAY_A );

		if ( ! $pdf_file ) {
			return __( 'PDF file not found.', 'sitecompass' );
		}

		// Delete from OpenAI if file ID exists.
		if ( ! empty( $pdf_file['openai_file_id'] ) ) {
			$openai_api_key = get_option( 'sitecompass_openai_api_key', '' );
			if ( ! empty( $openai_api_key ) ) {
				require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-openai-assistant.php';
				$openai_assistant = new Sitecompass_Ai_OpenAI_Assistant( $openai_api_key );
				$openai_assistant->delete_file( $pdf_file['openai_file_id'] );
			}
		}

		// Delete local file.
		$file_path = str_replace( wp_upload_dir()['url'], wp_upload_dir()['path'], $pdf_file['path'] );
		if ( file_exists( $file_path ) ) {
			wp_delete_file( $file_path );
		}

		// Delete from database.
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$wpdb->delete( $table_name, array( 'id' => $pdf_id ), array( '%d' ) );

		return true;
	}

	/**
	 * Get all uploaded PDFs.
	 *
	 * @return array List of PDFs.
	 */
	public function get_pdfs() {
		global $wpdb;
		$table_name = $wpdb->prefix . 'sitecompass_pdfs';
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return $wpdb->get_results( $wpdb->prepare( "SELECT * FROM %i ORDER BY created_at DESC", $table_name ), ARRAY_A );
	}
}
