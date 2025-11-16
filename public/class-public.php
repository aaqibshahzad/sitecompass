<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */

/**
 * The public-facing functionality of the plugin.
 */
class Sitecompass_Ai_Public {
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
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 */
	public function enqueue_styles() {
		// Enqueue chatbox CSS.
		wp_enqueue_style(
			$this->plugin_name . '-chatbox',
			SITECOMPASS_AI_PLUGIN_URL . 'assets/css/chatbox.css',
			array(),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 */
	public function enqueue_scripts() {
		// Enqueue chatbox script.
		wp_enqueue_script(
			$this->plugin_name . '-chatbox',
			SITECOMPASS_AI_PLUGIN_URL . 'assets/js/chatbox.js',
			array( 'jquery' ),
			$this->version,
			true
		);

		// Generate random ID for session.
		$random_id = wp_generate_password( 32, false );

		// Localize script with AJAX URL and random ID.
		wp_localize_script(
			$this->plugin_name . '-chatbox',
			'sitecompassAjax',
			array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
			)
		);

		wp_localize_script(
			$this->plugin_name . '-chatbox',
			'sitecompassRandomId',
			array(
				'randomId' => $random_id,
			)
		);
	}
}
