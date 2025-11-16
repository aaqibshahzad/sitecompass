<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The admin-specific functionality of the plugin.
 */
class Sitecompass_Ai_Admin {
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
	 * Register the stylesheets for the admin area.
	 */
	public function enqueue_styles() {
		// Enqueue color picker styles.
		wp_enqueue_style( 'wp-color-picker' );

		// Enqueue admin CSS.
		wp_enqueue_style(
			$this->plugin_name . '-admin',
			SITECOMPASS_AI_PLUGIN_URL . 'assets/css/admin.css',
			array( 'wp-color-picker' ),
			$this->version,
			'all'
		);
	}

	/**
	 * Register the JavaScript for the admin area.
	 */
	public function enqueue_scripts() {
		// Enqueue color picker.
		wp_enqueue_script( 'wp-color-picker' );

		// Enqueue color picker script.
		wp_enqueue_script(
			$this->plugin_name . '-color-picker',
			SITECOMPASS_AI_PLUGIN_URL . 'assets/js/color-picker.js',
			array( 'jquery', 'wp-color-picker' ),
			$this->version,
			false
		);

		// Enqueue admin script.
		wp_enqueue_script(
			$this->plugin_name . '-admin',
			SITECOMPASS_AI_PLUGIN_URL . 'assets/js/admin.js',
			array( 'jquery' ),
			$this->version,
			false
		);
	}

	/**
	 * Display admin notices for API key validation.
	 */
	public function display_api_key_notices() {
		// Only show on admin pages.
		if ( ! is_admin() ) {
			return;
		}

		// Get the OpenAI API key from settings.
		$api_key = get_option( 'sitecompass_openai_api_key', '' );

		// If no API key is set, show a notice.
		if ( empty( $api_key ) ) {
			$this->show_notice(
				__( 'SiteCompass AI: Please configure your OpenAI API key in the settings to enable the chatbot.', 'sitecompass' ),
				'warning'
			);
			return;
		}

		// Check if we've already validated this key recently (cache for 1 hour).
		$validation_cache_key = 'sitecompass_api_key_validation_' . md5( $api_key );
		$cached_validation    = get_transient( $validation_cache_key );

		if ( false !== $cached_validation ) {
			if ( 'valid' !== $cached_validation ) {
				$this->show_notice(
					__( 'SiteCompass AI: Your OpenAI API key appears to be invalid. Please check your settings.', 'sitecompass' ),
					'error'
				);
			}
			return;
		}

		// Validate the API key.
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-openai-assistant.php';
		$openai_assistant = new Sitecompass_Ai_OpenAI_Assistant( $api_key );
		$validation_result = $openai_assistant->verify_api_key();

		if ( 'valid_api_key' === $validation_result ) {
			// Cache the valid result for 1 hour.
			set_transient( $validation_cache_key, 'valid', HOUR_IN_SECONDS );
		} else {
			// Cache the invalid result for 5 minutes (shorter to allow quick fixes).
			set_transient( $validation_cache_key, 'invalid', 5 * MINUTE_IN_SECONDS );
			$this->show_notice(
				__( 'SiteCompass AI: Your OpenAI API key appears to be invalid. Please check your settings.', 'sitecompass' ),
				'error'
			);
		}
	}

	/**
	 * Show an admin notice.
	 *
	 * @param string $message Notice message.
	 * @param string $type    Notice type (success, error, warning, info).
	 */
	private function show_notice( $message, $type = 'info' ) {
		$class = 'notice notice-' . esc_attr( $type ) . ' is-dismissible';
		printf(
			'<div class="%1$s"><p>%2$s</p></div>',
			esc_attr( $class ),
			esc_html( $message )
		);
	}
}
