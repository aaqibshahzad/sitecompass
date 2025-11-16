<?php
/**
 * The avatar settings functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The avatar settings functionality of the plugin.
 */
class Sitecompass_Ai_Avatar {
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
	 * Register avatar settings.
	 */
	public function register_settings() {
		register_setting(
			'sitecompass_avatar_settings',
			'sitecompass_avatar_greeting',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Hi there! 👋',
			)
		);

		register_setting(
			'sitecompass_avatar_settings',
			'sitecompass_custom_avatar_url',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);

		register_setting(
			'sitecompass_avatar_settings',
			'sitecompass_avatar_icon_setting',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_file_name',
				'default'           => 'icon-001.png',
			)
		);
	}

	/**
	 * Get available avatar icon sets.
	 *
	 * @return array Icon sets with counts.
	 */
	public function get_icon_sets() {
		return array(
			'Icon'         => 30,
			'Chinese'      => 10,
			'Christmas'    => 10,
			'Fall'         => 10,
			'Halloween'    => 10,
			'Spring'       => 10,
			'Summer'       => 10,
			'Thanksgiving' => 10,
			'Winter'       => 10,
			'Custom'       => 1,
		);
	}

	/**
	 * Get icon URL.
	 *
	 * @param string $icon_name Icon filename.
	 * @return string Icon URL.
	 */
	public function get_icon_url( $icon_name ) {
		return SITECOMPASS_AI_PLUGIN_URL . 'assets/icons/' . $icon_name;
	}
}
