<?php
/**
 * The core plugin class.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/includes
 */

/**
 * The core plugin class.
 */
class Sitecompass_Ai_Plugin {
	/**
	 * The loader that's responsible for maintaining and registering all hooks.
	 *
	 * @var Sitecompass_Ai_Loader
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @var string
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @var string
	 */
	protected $version;

	/**
	 * Initialize the plugin.
	 */
	public function __construct() {
		$this->version     = SITECOMPASS_AI_VERSION;
		$this->plugin_name = 'sitecompass-ai';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 */
	private function load_dependencies() {
		// Core classes.
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-loader.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-database.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'includes/class-openai-assistant.php';

		// Admin classes.
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-admin.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-settings.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-appearance.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-avatar.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-chats.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-pdf-manager.php';

		// Public classes.
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-public.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-chatbox.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-session.php';
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-message-handler.php';

		$this->loader = new Sitecompass_Ai_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 */
	private function set_locale() {
		$this->loader->add_action( 'plugins_loaded', $this, 'load_plugin_textdomain' );
	}

	/**
	 * Load the plugin text domain for translation.
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain(
			'sitecompass',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);
	}

	/**
	 * Register all of the hooks related to the admin area functionality.
	 */
	private function define_admin_hooks() {
		$plugin_admin = new Sitecompass_Ai_Admin( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_notices', $plugin_admin, 'display_api_key_notices' );

		$plugin_settings = new Sitecompass_Ai_Settings( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_menu', $plugin_settings, 'register_admin_menu' );
		$this->loader->add_action( 'admin_init', $plugin_settings, 'register_settings' );

		$plugin_appearance = new Sitecompass_Ai_Appearance( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_appearance, 'register_settings' );

		$plugin_avatar = new Sitecompass_Ai_Avatar( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'admin_init', $plugin_avatar, 'register_settings' );

		$plugin_chats = new Sitecompass_Ai_Chats( $this->get_plugin_name(), $this->get_version() );
		// Chat history page is registered as a submenu in the settings class.

		$plugin_pdf_manager = new Sitecompass_Ai_PDF_Manager( $this->get_plugin_name(), $this->get_version() );
		// PDF manager is used within the settings page, no separate hooks needed.
	}

	/**
	 * Register all of the hooks related to the public-facing functionality.
	 */
	private function define_public_hooks() {
		$plugin_public = new Sitecompass_Ai_Public( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

		$plugin_chatbox = new Sitecompass_Ai_Chatbox( $this->get_plugin_name(), $this->get_version() );
		$this->loader->add_action( 'init', $plugin_chatbox, 'register_shortcode' );
		$this->loader->add_action( 'wp_footer', $plugin_chatbox, 'display_in_footer' );

		$message_handler = new Sitecompass_Ai_Message_Handler();
		$message_handler->register_hooks();
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it.
	 *
	 * @return string The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return Sitecompass_Ai_Loader Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @return string The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
