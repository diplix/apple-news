<?php
/**
 * Entry point for the admin side of the WP Plugin.
 *
 * @author  Federico Ramirez
 * @since   0.0.0
 */

require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-settings.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-post-sync.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-index-page.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-bulk-export-page.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-notice.php';
require_once plugin_dir_path( __FILE__ ) . 'class-admin-apple-meta-boxes.php';

/**
 * Entry-point class for the plugin.
 */
class Admin_Apple_News extends Apple_News {

	/**
	 * Constructor.
	 */
	function __construct() {
		// This is required to download files and setting headers.
		ob_start();

		// Initialize notice messaging utility
		new Admin_Apple_Notice;

		// Register hooks
		add_action( 'admin_print_styles-toplevel_page_apple_news_index', array( $this, 'plugin_styles' ) );

		// Admin_Settings builds the settings page for the plugin. Besides setting
		// it up, let's get the settings getter and setter object and save it into
		// $settings.
		$admin_settings = new Admin_Apple_Settings;
		$settings       = $admin_settings->fetch_settings();

		// Set up main page
		new Admin_Apple_Index_Page( $settings );

		// Set up all sub pages
		new Admin_Apple_Bulk_Export_Page( $settings );

		// Set up posts syncing if enabled in the settings
		new Admin_Apple_Post_Sync( $settings );

		// Set up the publish meta box if enabled in the settings
		new Admin_Apple_Meta_Boxes( $settings );
	}

	/**
	 * Implements certain plugin styles inline.
	 */
	public function plugin_styles() {
		// Styles are tiny, for now just embed them.
		echo '<style type="text/css">';
		echo '.wp-list-table .column-sync { width: 15%; }';
		echo '.wp-list-table .column-updated_at { width: 15%; }';
		// Clipboard fix
		echo '.row-actions.is-active { visibility: visible }';
		echo '</style>';
	}
}
