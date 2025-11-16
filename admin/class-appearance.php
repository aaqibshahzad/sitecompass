<?php
/**
 * The appearance settings functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The appearance settings functionality of the plugin.
 */
class Sitecompass_Ai_Appearance {
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
	 * Register appearance settings.
	 */
	public function register_settings() {
		// Color settings.
		$color_settings = array(
			'sitecompass_appearance_button_bg_color'              => '#0073aa',
			'sitecompass_appearance_chatbot_bg_color'             => '#ffffff',
			'sitecompass_appearance_header_bg_color'              => '#0073aa',
			'sitecompass_appearance_header_text_color'            => '#ffffff',
			'sitecompass_appearance_text_color'                   => '#333333',
			'sitecompass_appearance_user_text_bg_color'           => '#0073aa',
			'sitecompass_appearance_bot_text_bg_color'            => '#f1f1f1',
			'sitecompass_appearance_greeting_text_color'          => '#333333',
			'sitecompass_appearance_subsequent_greeting_bg_color' => '#f9f9f9',
			'sitecompass_appearance_subsequent_greeting_text_color' => '#333333',
		);

		foreach ( $color_settings as $setting => $default ) {
			register_setting(
				'sitecompass_appearance_settings',
				$setting,
				array(
					'type'              => 'string',
					'sanitize_callback' => 'sanitize_hex_color',
					'default'           => $default,
				)
			);
		}

		// Width settings.
		register_setting(
			'sitecompass_appearance_settings',
			'sitecompass_appearance_width_wide',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '600px',
			)
		);

		register_setting(
			'sitecompass_appearance_settings',
			'sitecompass_appearance_width_narrow',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '400px',
			)
		);

		register_setting(
			'sitecompass_appearance_settings',
			'sitecompass_appearance_width_setting',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'narrow',
			)
		);

		// Custom CSS.
		register_setting(
			'sitecompass_appearance_settings',
			'sitecompass_appearance_custom_css',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'wp_strip_all_tags',
				'default'           => '',
			)
		);

		// Restore defaults.
		register_setting(
			'sitecompass_appearance_settings',
			'sitecompass_appearance_restore_defaults',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'no',
			)
		);
	}

	/**
	 * Get default appearance settings.
	 *
	 * @return array Default settings.
	 */
	public function get_defaults() {
		return array(
			'sitecompass_appearance_button_bg_color'              => '#0073aa',
			'sitecompass_appearance_chatbot_bg_color'             => '#ffffff',
			'sitecompass_appearance_header_bg_color'              => '#0073aa',
			'sitecompass_appearance_header_text_color'            => '#ffffff',
			'sitecompass_appearance_text_color'                   => '#333333',
			'sitecompass_appearance_user_text_bg_color'           => '#0073aa',
			'sitecompass_appearance_bot_text_bg_color'            => '#f1f1f1',
			'sitecompass_appearance_greeting_text_color'          => '#333333',
			'sitecompass_appearance_subsequent_greeting_bg_color' => '#f9f9f9',
			'sitecompass_appearance_subsequent_greeting_text_color' => '#333333',
			'sitecompass_appearance_width_wide'                   => '600px',
			'sitecompass_appearance_width_narrow'                 => '400px',
			'sitecompass_appearance_width_setting'                => 'narrow',
			'sitecompass_appearance_custom_css'                   => '',
		);
	}

	/**
	 * Restore default settings.
	 */
	public function restore_defaults() {
		$defaults = $this->get_defaults();
		foreach ( $defaults as $key => $value ) {
			update_option( $key, $value );
		}
		update_option( 'sitecompass_appearance_restore_defaults', 'no' );
	}
}
