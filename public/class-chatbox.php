<?php
/**
 * The chatbox display functionality of the plugin.
 *
 * Handles the rendering of the chatbox interface on the frontend,
 * including the chat button, chat interface, user form, and message history.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */

/**
 * The chatbox display class.
 *
 * Renders the chatbox widget via shortcode and manages the display
 * of chat elements including avatar, greetings, user form, and messages.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/public
 */
class Sitecompass_Ai_Chatbox {

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
	 * Session manager instance.
	 *
	 * @var Sitecompass_Ai_Session
	 */
	private $session;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param string $plugin_name The name of the plugin.
	 * @param string $version     The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;

		// Load session management.
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'public/class-session.php';
		$this->session = new Sitecompass_Ai_Session();
	}

	/**
	 * Register the shortcode.
	 */
	public function register_shortcode() {
		add_shortcode( 'sitecompass', array( $this, 'render_chatbox' ) );
	}

	/**
	 * Display chatbox in footer.
	 *
	 * Automatically displays the chatbox on all pages via wp_footer hook.
	 * Can be disabled by setting 'sitecompass_auto_display' option to 'no'.
	 */
	public function display_in_footer() {
		// Check if auto-display is enabled.
		$auto_display = get_option( 'sitecompass_auto_display', 'no' );
		if ( 'yes' !== $auto_display ) {
			return;
		}

		// Render chatbox.
		echo $this->render_chatbox( array() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	/**
	 * Check if OpenAI API is configured.
	 *
	 * Verifies that the OpenAI API key is set before displaying the chatbox.
	 *
	 * @return bool True if API is configured, false otherwise.
	 */
	private function is_api_configured() {
		$api_key = get_option( 'sitecompass_openai_api_key', '' );
		return ! empty( $api_key );
	}

	/**
	 * Render the chatbox shortcode.
	 *
	 * Main method that outputs the chatbox HTML. Checks API configuration,
	 * loads conversation history, and renders all chatbox elements.
	 *
	 * @param array $attrs Shortcode attributes.
	 * @return string Chatbox HTML output.
	 */
	public function render_chatbox( $attrs ) {
		// Check if OpenAI API is configured.
		if ( ! $this->is_api_configured() ) {
			return '';
		}

		// Get settings.
		$avatar             = $this->get_avatar();
		$avatar_greeting    = $this->get_avatar_greeting();
		$initial_greeting   = $this->get_initial_greeting();
		$subsequent_greeting = $this->get_subsequent_greeting();
		$bot_prompt         = $this->get_bot_prompt();
		$show_user_form     = $this->should_show_user_form();
		$bot_name           = $this->get_bot_name();

		// Get session data.
		$session_id = $this->session->get_session_id();
		$user_info_submitted = $this->session->is_user_info_submitted();

		// Load existing conversation.
		$conversation_html = $this->load_conversation_history( $session_id );

		// Determine visibility classes.
		$chatbox_class = '';
		$user_form_class = '';

		if ( 'no' === $show_user_form || $user_info_submitted ) {
			$user_form_class = 'sitecompass-d-none';
		} else {
			$chatbox_class = 'sitecompass-d-none';
		}

		// Generate dynamic CSS.
		$dynamic_css = $this->generate_dynamic_css();

		// Start output buffering.
		ob_start();

		// Output dynamic CSS.
		if ( ! empty( $dynamic_css ) ) {
			echo '<style>' . wp_strip_all_tags( $dynamic_css ) . '</style>';
		}

		// Render chat button.
		$this->render_chat_button( $avatar, $initial_greeting );

		// Render chat interface.
		$this->render_chat_interface(
			$bot_name,
			$user_form_class,
			$chatbox_class,
			$subsequent_greeting,
			$conversation_html,
			$bot_prompt
		);

		return ob_get_clean();
	}

	/**
	 * Render the chat button with avatar.
	 *
	 * @param string $avatar           Avatar image URL or filename.
	 * @param string $initial_greeting Initial greeting message.
	 */
	private function render_chat_button( $avatar, $initial_greeting ) {
		$avatar_url = $this->get_avatar_url( $avatar );
		?>
		<button class="sitecompass-chat-button" id="sitecompass-open-chat">
			<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php esc_attr_e( 'Chat Avatar', 'sitecompass' ); ?>">
			<?php if ( ! empty( $initial_greeting ) ) : ?>
				<div class="sitecompass-initial-greeting">
					<?php echo esc_html( $initial_greeting ); ?>
				</div>
			<?php endif; ?>
		</button>
		<?php
	}

	/**
	 * Render the chat interface.
	 *
	 * @param string $bot_name            Bot display name.
	 * @param string $user_form_class     CSS class for user form visibility.
	 * @param string $chatbox_class       CSS class for chatbox visibility.
	 * @param string $subsequent_greeting Subsequent greeting message.
	 * @param string $conversation_html   Existing conversation HTML.
	 * @param string $bot_prompt          Message input placeholder.
	 */
	private function render_chat_interface( $bot_name, $user_form_class, $chatbox_class, $subsequent_greeting, $conversation_html, $bot_prompt ) {
		?>
		<div class="sitecompass-chat-box" id="sitecompass-chat-box">
			<?php $this->render_chat_header( $bot_name ); ?>
			<?php $this->render_user_form( $user_form_class ); ?>
			<?php $this->render_chat_body( $chatbox_class, $subsequent_greeting, $conversation_html ); ?>
			<?php $this->render_chat_footer( $chatbox_class, $bot_prompt ); ?>
		</div>
		<?php
	}

	/**
	 * Render the chat header.
	 *
	 * @param string $bot_name Bot display name.
	 */
	private function render_chat_header( $bot_name ) {
		?>
		<div class="sitecompass-chat-header">
			<div class="sitecompass-header-title">
				<?php echo esc_html( $bot_name ); ?>
			</div>
			<span class="sitecompass-close-chat" id="sitecompass-close-chat">-</span>
		</div>
		<?php
	}

	/**
	 * Render the user information form.
	 *
	 * @param string $user_form_class CSS class for visibility.
	 */
	private function render_user_form( $user_form_class ) {
		?>
		<div id="sitecompass-user-form" class="sitecompass-form-container <?php echo esc_attr( $user_form_class ); ?>">
			<form class="sitecompass-user-info-form">
				<div id="sitecompass-form-error-message"></div>
				<div id="sitecompass-form-success-message"></div>
				
				<label for="sitecompass-user-name" class="sitecompass-form-label">
					<?php esc_html_e( 'Name:', 'sitecompass' ); ?>
				</label>
				<input type="text" id="sitecompass-user-name" name="name" class="sitecompass-form-input" required>
				
				<label for="sitecompass-user-email" class="sitecompass-form-label">
					<?php esc_html_e( 'Email:', 'sitecompass' ); ?>
				</label>
				<input type="email" id="sitecompass-user-email" name="email" class="sitecompass-form-input" required>
				
				<label for="sitecompass-user-phone" class="sitecompass-form-label">
					<?php esc_html_e( 'Phone:', 'sitecompass' ); ?>
				</label>
				<input type="text" id="sitecompass-user-phone" name="phone" class="sitecompass-form-input">
				
				<button id="sitecompass-submit-user-data" type="button" class="sitecompass-form-button">
					<?php esc_html_e( 'Submit', 'sitecompass' ); ?>
				</button>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the chat body with messages.
	 *
	 * @param string $chatbox_class       CSS class for visibility.
	 * @param string $subsequent_greeting Subsequent greeting message.
	 * @param string $conversation_html   Existing conversation HTML.
	 */
	private function render_chat_body( $chatbox_class, $subsequent_greeting, $conversation_html ) {
		?>
		<div id="sitecompass-chatbox" class="<?php echo esc_attr( $chatbox_class ); ?>">
			<div class="sitecompass-chat-body" id="sitecompass-chat-body">
				<?php if ( ! empty( $subsequent_greeting ) ) : ?>
					<div class="sitecompass-bot-message sitecompass-greeting-message">
						<span><?php echo esc_html( $subsequent_greeting ); ?></span>
					</div>
				<?php endif; ?>
				<?php echo wp_kses_post( $conversation_html ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the chat footer with input area.
	 *
	 * @param string $chatbox_class CSS class for visibility.
	 * @param string $bot_prompt    Message input placeholder.
	 */
	private function render_chat_footer( $chatbox_class, $bot_prompt ) {
		?>
		<div id="sitecompass-chat-footer" class="sitecompass-chat-footer <?php echo esc_attr( $chatbox_class ); ?>">
			<div class="sitecompass-chat-footer-textarea">
				<textarea id="sitecompass-user-message" placeholder="<?php echo esc_attr( $bot_prompt ); ?>"></textarea>
				<div class="sitecompass-message-button">
					<button class="sitecompass-send-button" id="sitecompass-send-msg">
						<?php esc_html_e( 'Enter', 'sitecompass' ); ?>
					</button>
				</div>
			</div>
			<div class="sitecompass-footer-info-text">
				<?php esc_html_e( 'Powered by SiteCompass', 'sitecompass' ); ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Load conversation history from database.
	 *
	 * @param string $session_id Session identifier.
	 * @return string HTML of conversation messages.
	 */
	private function load_conversation_history( $session_id ) {
		global $wpdb;

		$conversation_table = $wpdb->prefix . 'sitecompass_conversations';
		$conversation_html  = '';

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		$conversations = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT * FROM %i WHERE session_id = %s ORDER BY created_at ASC",
				$conversation_table,
				$session_id
			)
		);

		if ( ! empty( $conversations ) ) {
			foreach ( $conversations as $conversation ) {
				$class_name = ( 'user' === $conversation->user_type ) ? 'sitecompass-user-message' : 'sitecompass-bot-message';
				$conversation_html .= sprintf(
					'<div class="sitecompass-chat-message %s"><span>%s</span></div>',
					esc_attr( $class_name ),
					esc_html( $conversation->message_text )
				);
			}
		}

		return $conversation_html;
	}

	/**
	 * Get avatar image.
	 *
	 * @return string Avatar filename or URL.
	 */
	private function get_avatar() {
		$custom_avatar = get_option( 'sitecompass_custom_avatar_url', '' );
		if ( ! empty( $custom_avatar ) ) {
			return $custom_avatar;
		}

		$avatar = get_option( 'sitecompass_avatar_icon_setting', 'icon-001.png' );
		return ! empty( $avatar ) ? $avatar : 'icon-001.png';
	}

	/**
	 * Get avatar URL.
	 *
	 * @param string $avatar Avatar filename or URL.
	 * @return string Full avatar URL.
	 */
	private function get_avatar_url( $avatar ) {
		// If it's already a full URL, return it.
		if ( filter_var( $avatar, FILTER_VALIDATE_URL ) ) {
			return $avatar;
		}

		// Otherwise, construct URL from assets directory.
		return SITECOMPASS_AI_PLUGIN_URL . 'assets/icons/' . $avatar;
	}

	/**
	 * Get avatar greeting message.
	 *
	 * @return string Avatar greeting.
	 */
	private function get_avatar_greeting() {
		$greeting = get_option( 'sitecompass_avatar_greeting', '' );
		return ! empty( $greeting ) ? $greeting : __( 'Hi there! 👋', 'sitecompass' );
	}

	/**
	 * Get initial greeting message.
	 *
	 * @return string Initial greeting.
	 */
	private function get_initial_greeting() {
		return get_option( 'sitecompass_initial_greeting', '' );
	}

	/**
	 * Get subsequent greeting message.
	 *
	 * @return string Subsequent greeting.
	 */
	private function get_subsequent_greeting() {
		return get_option( 'sitecompass_subsequent_greeting', '' );
	}

	/**
	 * Get bot prompt placeholder.
	 *
	 * @return string Bot prompt.
	 */
	private function get_bot_prompt() {
		$prompt = get_option( 'sitecompass_bot_prompt', '' );
		return ! empty( $prompt ) ? $prompt : __( 'Enter your question...', 'sitecompass' );
	}

	/**
	 * Check if user form should be shown.
	 *
	 * @return string 'yes' or 'no'.
	 */
	private function should_show_user_form() {
		return get_option( 'sitecompass_show_user_form', 'no' );
	}

	/**
	 * Get bot name.
	 *
	 * @return string Bot name.
	 */
	private function get_bot_name() {
		$name = get_option( 'sitecompass_bot_name', '' );
		return ! empty( $name ) ? $name : __( 'SiteCompass', 'sitecompass' );
	}

	/**
	 * Generate dynamic CSS from appearance settings.
	 *
	 * Creates inline CSS based on customization options set in the admin panel.
	 *
	 * @return string CSS code.
	 */
	private function generate_dynamic_css() {
		// Get appearance settings.
		$button_bg_color = esc_attr( get_option( 'sitecompass_appearance_button_bg_color', '#0073aa' ) );
		$chatbot_bg_color = esc_attr( get_option( 'sitecompass_appearance_chatbot_bg_color', '#ffffff' ) );
		$header_bg_color = esc_attr( get_option( 'sitecompass_appearance_header_bg_color', '#0073aa' ) );
		$header_text_color = esc_attr( get_option( 'sitecompass_appearance_header_text_color', '#ffffff' ) );
		$text_color = esc_attr( get_option( 'sitecompass_appearance_text_color', '#333333' ) );
		$user_text_bg_color = esc_attr( get_option( 'sitecompass_appearance_user_text_bg_color', '#0073aa' ) );
		$bot_text_bg_color = esc_attr( get_option( 'sitecompass_appearance_bot_text_bg_color', '#f1f1f1' ) );
		$greeting_text_color = esc_attr( get_option( 'sitecompass_appearance_greeting_text_color', '#333333' ) );
		$subsequent_greeting_bg_color = esc_attr( get_option( 'sitecompass_appearance_subsequent_greeting_bg_color', '#f9f9f9' ) );
		$subsequent_greeting_text_color = esc_attr( get_option( 'sitecompass_appearance_subsequent_greeting_text_color', '#333333' ) );
		
		// Get width settings.
		$width_setting = get_option( 'sitecompass_appearance_width_setting', 'narrow' );
		$width_wide = esc_attr( get_option( 'sitecompass_appearance_width_wide', '600px' ) );
		$width_narrow = esc_attr( get_option( 'sitecompass_appearance_width_narrow', '400px' ) );
		$chatbox_width = ( 'wide' === $width_setting ) ? $width_wide : $width_narrow;

		// Get custom CSS.
		$custom_css = wp_strip_all_tags( get_option( 'sitecompass_appearance_custom_css', '' ) );

		// Build CSS.
		$css = "
		/* SiteCompass Dynamic Styles */
		.sitecompass-chat-button {
			background-color: {$button_bg_color} !important;
		}
		.sitecompass-chat-box {
			background-color: {$chatbot_bg_color} !important;
			width: {$chatbox_width} !important;
		}
		.sitecompass-chat-header {
			background-color: {$header_bg_color} !important;
			color: {$header_text_color} !important;
		}
		.sitecompass-header-title {
			color: {$header_text_color} !important;
		}
		.sitecompass-chat-body {
			color: {$text_color} !important;
		}
		.sitecompass-user-message {
			background-color: {$user_text_bg_color} !important;
		}
		.sitecompass-bot-message {
			background-color: {$bot_text_bg_color} !important;
		}
		.sitecompass-initial-greeting {
			color: {$greeting_text_color} !important;
		}
		.sitecompass-greeting-message {
			background-color: {$subsequent_greeting_bg_color} !important;
			color: {$subsequent_greeting_text_color} !important;
		}
		";

		// Add custom CSS if provided.
		if ( ! empty( $custom_css ) ) {
			$css .= "\n/* Custom CSS */\n" . $custom_css;
		}

		return $css;
	}
}
