<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package    Sitecompass_Ai
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

// Check user capabilities.
if ( ! current_user_can( 'activate_plugins' ) ) {
	return;
}

global $wpdb;

// Delete all plugin options using wildcard query for efficiency.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$wpdb->query(
	$wpdb->prepare(
		"DELETE FROM {$wpdb->options} WHERE option_name LIKE %s",
		$wpdb->esc_like( 'sitecompass_' ) . '%'
	)
);

// Get all uploaded PDF files and delete them.
$upload_dir = wp_upload_dir();
$pdfs_table = $wpdb->prefix . 'sitecompass_pdfs';

// Check if table exists before querying.
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
$table_exists = $wpdb->get_var(
	$wpdb->prepare(
		'SHOW TABLES LIKE %s',
		$wpdb->esc_like( $pdfs_table )
	)
);

if ( $table_exists ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
	$pdf_files = $wpdb->get_results(
		"SELECT * FROM {$pdfs_table}",
		ARRAY_A
	);

	if ( ! empty( $pdf_files ) ) {
		foreach ( $pdf_files as $pdf ) {
			// Delete local file.
			if ( ! empty( $pdf['path'] ) ) {
				$file_path = str_replace( $upload_dir['baseurl'], $upload_dir['basedir'], $pdf['path'] );
				if ( file_exists( $file_path ) ) {
					wp_delete_file( $file_path );
				}
			}
		}
	}
}

// Drop custom database tables.
$tables = array(
	$wpdb->prefix . 'sitecompass_pdfs',
	$wpdb->prefix . 'sitecompass_users',
	$wpdb->prefix . 'sitecompass_conversations',
	$wpdb->prefix . 'sitecompass_assistants',
);

foreach ( $tables as $table ) {
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
	$wpdb->query( "DROP TABLE IF EXISTS {$table}" );
}
  