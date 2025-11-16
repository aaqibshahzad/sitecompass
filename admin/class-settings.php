<?php
/**
 * The settings page functionality of the plugin.
 *
 * @package    Sitecompass_Ai
 * @subpackage Sitecompass_Ai/admin
 */

/**
 * The settings page functionality of the plugin.
 */
class Sitecompass_Ai_Settings {
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
	 * Register settings using WordPress Settings API.
	 */
	public function register_settings() {
		// General Settings.
		register_setting(
			'sitecompass_general_settings',
			'sitecompass_openai_api_key',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_model',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'gpt-4o-mini',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_bot_name',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'SiteCompass',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_instructions',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => 'You are a versatile, friendly, and helpful assistant designed to support users in a variety of tasks.',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_bot_prompt',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'Enter your question...',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_initial_greeting',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => 'Hello! How can I help you today?',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_subsequent_greeting',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_textarea_field',
				'default'           => 'Welcome back! How can I assist you?',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_show_user_form',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => 'no',
			)
		);

		register_setting(
			'sitecompass_general_settings',
			'sitecompass_assistant_id',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'sanitize_text_field',
				'default'           => '',
			)
		);
	}

	/**
	 * Register the admin menu page.
	 */
	public function register_admin_menu() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		add_menu_page(
			__( 'SiteCompass', 'sitecompass' ),
			__( 'SiteCompass', 'sitecompass' ),
			'manage_options',
			'sitecompass',
			array( $this, 'render_chats_page' ),
			'dashicons-format-chat',
			30
		);

		add_submenu_page(
			'sitecompass',
			__( 'Chats', 'sitecompass' ),
			__( 'Chats', 'sitecompass' ),
			'manage_options',
			'sitecompass',
			array( $this, 'render_chats_page' )
		);

		add_submenu_page(
			'sitecompass',
			__( 'Settings', 'sitecompass' ),
			__( 'Settings', 'sitecompass' ),
			'manage_options',
			'sitecompass-settings',
			array( $this, 'render_settings_page' )
		);
	}

	/**
	 * Render the chats page.
	 */
	public function render_chats_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-chats.php';
		$chats = new Sitecompass_Ai_Chats( $this->plugin_name, $this->version );
		$chats->render_chats_page();
	}

	/**
	 * Render the settings page with tabs.
	 */
	public function render_settings_page() {
		// Check user capabilities.
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		// Get the active tab from the $_GET param.
		$default_tab = 'general';
		$tab         = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : $default_tab;

		?>
		<div class="wrap">
			<h1><?php echo esc_html__( 'SiteCompass Settings', 'sitecompass' ); ?></h1>

			<nav class="nav-tab-wrapper">
				<a href="?page=sitecompass-settings&tab=general" class="nav-tab <?php echo esc_attr( $tab === 'general' ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'General', 'sitecompass' ); ?>
				</a>
				<a href="?page=sitecompass-settings&tab=pdfs" class="nav-tab <?php echo esc_attr( $tab === 'pdfs' ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'PDFs', 'sitecompass' ); ?>
				</a>
				<a href="?page=sitecompass-settings&tab=appearance" class="nav-tab <?php echo esc_attr( $tab === 'appearance' ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'Appearance', 'sitecompass' ); ?>
				</a>
				<a href="?page=sitecompass-settings&tab=avatars" class="nav-tab <?php echo esc_attr( $tab === 'avatars' ? 'nav-tab-active' : '' ); ?>">
					<?php esc_html_e( 'Avatars', 'sitecompass' ); ?>
				</a>
			</nav>

			<div class="tab-content">
				<?php
				switch ( $tab ) {
					case 'pdfs':
						$this->render_pdfs_tab();
						break;
					case 'appearance':
						$this->render_appearance_tab();
						break;
					case 'avatars':
						$this->render_avatars_tab();
						break;
					case 'general':
					default:
						$this->render_general_tab();
						break;
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render the General settings tab.
	 */
	private function render_general_tab() {
		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'General Settings', 'sitecompass' ); ?></h2>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'sitecompass_general_settings' );
				wp_nonce_field( 'sitecompass_general_settings_nonce', 'sitecompass_general_settings_nonce_field' );
				?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_openai_api_key"><?php esc_html_e( 'OpenAI API Key', 'sitecompass' ); ?></label>
						</th>
						<td>
							<input type="text" id="sitecompass_openai_api_key" name="sitecompass_openai_api_key" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_openai_api_key', '' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Enter your OpenAI API key to enable the chatbot.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_model"><?php esc_html_e( 'GPT Model', 'sitecompass' ); ?></label>
						</th>
						<td>
							<?php $selected_model = esc_attr( get_option( 'sitecompass_model', 'gpt-4o-mini' ) ); ?>
							<select id="sitecompass_model" name="sitecompass_model">
								<option value="gpt-4o" <?php selected( $selected_model, 'gpt-4o' ); ?>>GPT-4o</option>
								<option value="gpt-4o-mini" <?php selected( $selected_model, 'gpt-4o-mini' ); ?>>GPT-4o Mini</option>
								<option value="gpt-4-turbo" <?php selected( $selected_model, 'gpt-4-turbo' ); ?>>GPT-4 Turbo</option>
							</select>
							<p class="description"><?php esc_html_e( 'Select the OpenAI model to use for the chatbot.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_bot_name"><?php esc_html_e( 'Bot Name', 'sitecompass' ); ?></label>
						</th>
						<td>
							<input type="text" id="sitecompass_bot_name" name="sitecompass_bot_name" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_bot_name', 'SiteCompass' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'The name displayed for the chatbot.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_instructions"><?php esc_html_e( 'Instructions', 'sitecompass' ); ?></label>
						</th>
						<td>
							<textarea id="sitecompass_instructions" name="sitecompass_instructions" rows="4" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'sitecompass_instructions', 'You are a versatile, friendly, and helpful assistant designed to support users in a variety of tasks.' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'System instructions for the AI assistant.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_bot_prompt"><?php esc_html_e( 'Prompt Placeholder', 'sitecompass' ); ?></label>
						</th>
						<td>
							<input type="text" id="sitecompass_bot_prompt" name="sitecompass_bot_prompt" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_bot_prompt', 'Enter your question...' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Placeholder text for the message input field.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_initial_greeting"><?php esc_html_e( 'Initial Greeting', 'sitecompass' ); ?></label>
						</th>
						<td>
							<textarea id="sitecompass_initial_greeting" name="sitecompass_initial_greeting" rows="2" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'sitecompass_initial_greeting', 'Hello! How can I help you today?' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Greeting message for first-time visitors.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_subsequent_greeting"><?php esc_html_e( 'Subsequent Greeting', 'sitecompass' ); ?></label>
						</th>
						<td>
							<textarea id="sitecompass_subsequent_greeting" name="sitecompass_subsequent_greeting" rows="2" cols="50" class="large-text"><?php echo esc_textarea( get_option( 'sitecompass_subsequent_greeting', 'Welcome back! How can I assist you?' ) ); ?></textarea>
							<p class="description"><?php esc_html_e( 'Greeting message for returning visitors.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_show_user_form"><?php esc_html_e( 'Show User Form', 'sitecompass' ); ?></label>
						</th>
						<td>
							<?php $show_user_form = esc_attr( get_option( 'sitecompass_show_user_form', 'no' ) ); ?>
							<select id="sitecompass_show_user_form" name="sitecompass_show_user_form">
								<option value="yes" <?php selected( $show_user_form, 'yes' ); ?>><?php esc_html_e( 'Yes', 'sitecompass' ); ?></option>
								<option value="no" <?php selected( $show_user_form, 'no' ); ?>><?php esc_html_e( 'No', 'sitecompass' ); ?></option>
							</select>
							<p class="description"><?php esc_html_e( 'Display a form to collect user information before chat.', 'sitecompass' ); ?></p>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save Settings', 'sitecompass' ) ); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the PDFs tab.
	 */
	private function render_pdfs_tab() {
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-pdf-manager.php';
		$pdf_manager = new Sitecompass_Ai_PDF_Manager( $this->plugin_name, $this->version );

		// Handle PDF upload.
		if ( isset( $_POST['submit'] ) && isset( $_FILES['pdf_files'] ) ) {
			$upload_result = $pdf_manager->handle_pdf_upload();
			if ( true === $upload_result ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'PDF uploaded successfully.', 'sitecompass' ) . '</p></div>';
			} elseif ( is_string( $upload_result ) ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $upload_result ) . '</p></div>';
			}
		}

		// Handle PDF deletion.
		if ( isset( $_GET['action'] ) && 'delete' === $_GET['action'] && isset( $_GET['id'] ) ) {
			$pdf_id = intval( $_GET['id'] );
			$delete_result = $pdf_manager->handle_pdf_deletion( $pdf_id );
			if ( true === $delete_result ) {
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'PDF deleted successfully.', 'sitecompass' ) . '</p></div>';
			} elseif ( is_string( $delete_result ) ) {
				echo '<div class="notice notice-error is-dismissible"><p>' . esc_html( $delete_result ) . '</p></div>';
			}
		}

		$pdf_files = $pdf_manager->get_pdfs();
		$openai_api_key = get_option( 'sitecompass_openai_api_key', '' );

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'PDF Management', 'sitecompass' ); ?></h2>

			<?php if ( empty( $openai_api_key ) ) : ?>
				<div class="notice notice-warning">
					<p><?php esc_html_e( 'Please configure your OpenAI API key in the General settings before uploading PDFs.', 'sitecompass' ); ?></p>
				</div>
			<?php else : ?>
				<form method="post" enctype="multipart/form-data" action="">
					<?php wp_nonce_field( 'sitecompass_pdf_upload', 'sitecompass_pdf_upload_nonce' ); ?>
					<div id="pdf_fields">
						<div class="pdf_field">
							<input type="file" name="pdf_files[]" accept="application/pdf" />
						</div>
					</div>
					<p>
						<button type="button" id="add_pdf" class="button"><?php esc_html_e( 'Add Another PDF', 'sitecompass' ); ?></button>
					</p>
					<?php submit_button( __( 'Upload PDFs', 'sitecompass' ) ); ?>
				</form>

				<?php if ( ! empty( $pdf_files ) ) : ?>
					<h3><?php esc_html_e( 'Uploaded PDFs', 'sitecompass' ); ?></h3>
					<table class="widefat">
						<thead>
							<tr>
								<th><?php esc_html_e( 'PDF Name', 'sitecompass' ); ?></th>
								<th><?php esc_html_e( 'OpenAI File ID', 'sitecompass' ); ?></th>
								<th><?php esc_html_e( 'Upload Date', 'sitecompass' ); ?></th>
								<th><?php esc_html_e( 'Action', 'sitecompass' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php foreach ( $pdf_files as $pdf ) : ?>
								<tr>
									<td>
										<a href="<?php echo esc_url( $pdf['path'] ); ?>" target="_blank">
											<?php echo esc_html( $pdf['name'] ); ?>
										</a>
									</td>
									<td><?php echo esc_html( $pdf['openai_file_id'] ); ?></td>
									<td><?php echo esc_html( $pdf['created_at'] ); ?></td>
									<td>
										<a href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'action' => 'delete', 'id' => $pdf['id'] ) ), 'delete_pdf_' . $pdf['id'] ) ); ?>" onclick="return confirm('<?php esc_attr_e( 'Are you sure you want to delete this PDF?', 'sitecompass' ); ?>');">
											<?php esc_html_e( 'Delete', 'sitecompass' ); ?>
										</a>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				<?php else : ?>
					<p><?php esc_html_e( 'No PDFs uploaded yet.', 'sitecompass' ); ?></p>
				<?php endif; ?>

				<script>
					document.addEventListener('DOMContentLoaded', function() {
						var addPdfButton = document.getElementById('add_pdf');
						if (addPdfButton) {
							addPdfButton.addEventListener('click', function(e) {
								e.preventDefault();
								var pdfFields = document.getElementById('pdf_fields');
								var newField = document.createElement('div');
								newField.className = 'pdf_field';
								newField.innerHTML = '<input type="file" name="pdf_files[]" accept="application/pdf" />';
								pdfFields.appendChild(newField);
							});
						}
					});
				</script>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render the Appearance tab.
	 */
	private function render_appearance_tab() {
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-appearance.php';
		$appearance = new Sitecompass_Ai_Appearance( $this->plugin_name, $this->version );

		// Handle restore defaults.
		if ( isset( $_POST['submit'] ) && isset( $_POST['sitecompass_appearance_restore_defaults'] ) && 'yes' === $_POST['sitecompass_appearance_restore_defaults'] ) {
			if ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['_wpnonce'] ) ), 'sitecompass_appearance_settings-options' ) ) {
				$appearance->restore_defaults();
				echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Default settings restored.', 'sitecompass' ) . '</p></div>';
			}
		}

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Appearance Settings', 'sitecompass' ); ?></h2>
			<form method="post" action="options.php">
				<?php settings_fields( 'sitecompass_appearance_settings' ); ?>
				<table class="form-table" role="presentation">
					<tbody>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_button_bg_color"><?php esc_html_e( 'Chatbot Button Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_button_bg_color" name="sitecompass_appearance_button_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_button_bg_color', '#0073aa' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_chatbot_bg_color"><?php esc_html_e( 'Chatbot Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_chatbot_bg_color" name="sitecompass_appearance_chatbot_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_chatbot_bg_color', '#ffffff' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_header_bg_color"><?php esc_html_e( 'Header Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_header_bg_color" name="sitecompass_appearance_header_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_header_bg_color', '#0073aa' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_header_text_color"><?php esc_html_e( 'Header Text Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_header_text_color" name="sitecompass_appearance_header_text_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_header_text_color', '#ffffff' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_text_color"><?php esc_html_e( 'Text Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_text_color" name="sitecompass_appearance_text_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_text_color', '#333333' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_user_text_bg_color"><?php esc_html_e( 'User Text Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_user_text_bg_color" name="sitecompass_appearance_user_text_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_user_text_bg_color', '#0073aa' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_bot_text_bg_color"><?php esc_html_e( 'Bot Text Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_bot_text_bg_color" name="sitecompass_appearance_bot_text_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_bot_text_bg_color', '#f1f1f1' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_greeting_text_color"><?php esc_html_e( 'Greeting Text Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_greeting_text_color" name="sitecompass_appearance_greeting_text_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_greeting_text_color', '#333333' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_subsequent_greeting_bg_color"><?php esc_html_e( 'Subsequent Greeting Background Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_subsequent_greeting_bg_color" name="sitecompass_appearance_subsequent_greeting_bg_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_subsequent_greeting_bg_color', '#f9f9f9' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_subsequent_greeting_text_color"><?php esc_html_e( 'Subsequent Greeting Text Color', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_subsequent_greeting_text_color" name="sitecompass_appearance_subsequent_greeting_text_color" class="sitecompass-color-field" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_subsequent_greeting_text_color', '#333333' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_width_wide"><?php esc_html_e( 'Chatbot Width Wide', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_width_wide" name="sitecompass_appearance_width_wide" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_width_wide', '600px' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_width_narrow"><?php esc_html_e( 'Chatbot Width Narrow', 'sitecompass' ); ?></label>
							</th>
							<td>
								<input type="text" id="sitecompass_appearance_width_narrow" name="sitecompass_appearance_width_narrow" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_appearance_width_narrow', '400px' ) ); ?>" />
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_width_setting"><?php esc_html_e( 'Chatbot Width Setting', 'sitecompass' ); ?></label>
							</th>
							<td>
								<?php $width_setting = esc_attr( get_option( 'sitecompass_appearance_width_setting', 'narrow' ) ); ?>
								<select id="sitecompass_appearance_width_setting" name="sitecompass_appearance_width_setting">
									<option value="narrow" <?php selected( $width_setting, 'narrow' ); ?>><?php esc_html_e( 'Narrow', 'sitecompass' ); ?></option>
									<option value="wide" <?php selected( $width_setting, 'wide' ); ?>><?php esc_html_e( 'Wide', 'sitecompass' ); ?></option>
								</select>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">
								<label for="sitecompass_appearance_restore_defaults"><?php esc_html_e( 'Restore Defaults', 'sitecompass' ); ?></label>
							</th>
							<td>
								<?php $restore_defaults = esc_attr( get_option( 'sitecompass_appearance_restore_defaults', 'no' ) ); ?>
								<select id="sitecompass_appearance_restore_defaults" name="sitecompass_appearance_restore_defaults">
									<option value="yes" <?php selected( $restore_defaults, 'yes' ); ?>><?php esc_html_e( 'Yes', 'sitecompass' ); ?></option>
									<option value="no" <?php selected( $restore_defaults, 'no' ); ?>><?php esc_html_e( 'No', 'sitecompass' ); ?></option>
								</select>
								<p class="description"><?php esc_html_e( 'Select "Yes" and save to restore all appearance settings to defaults.', 'sitecompass' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="sitecompass_appearance_custom_css"><?php esc_html_e( 'Custom CSS', 'sitecompass' ); ?></label>
							</th>
							<td>
								<textarea id="sitecompass_appearance_custom_css" name="sitecompass_appearance_custom_css" rows="10" cols="50" class="large-text code"><?php echo esc_textarea( get_option( 'sitecompass_appearance_custom_css', '' ) ); ?></textarea>
								<p class="description"><?php esc_html_e( 'Add custom CSS to further customize the chatbot appearance.', 'sitecompass' ); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Render the Avatars tab.
	 */
	private function render_avatars_tab() {
		require_once SITECOMPASS_AI_PLUGIN_DIR . 'admin/class-avatar.php';
		$avatar = new Sitecompass_Ai_Avatar( $this->plugin_name, $this->version );

		$icon_sets = $avatar->get_icon_sets();
		$selected_icon = esc_attr( get_option( 'sitecompass_avatar_icon_setting', 'icon-001.png' ) );

		?>
		<div class="wrap">
			<h2><?php esc_html_e( 'Avatar Settings', 'sitecompass' ); ?></h2>
			<div>
				<p><?php esc_html_e( 'Choose an avatar that best represents you and your brand or link to your own avatar by adding a Custom Avatar URL (recommended 60x60px).', 'sitecompass' ); ?></p>
				<p><?php esc_html_e( "It's ok if you don't want an Avatar. Just select the 'None' option among the Avatar Icon Options below.", 'sitecompass' ); ?></p>
				<p><?php esc_html_e( 'Be sure to remove the Custom Avatar URL if you want to select "None" or one from the set below.', 'sitecompass' ); ?></p>
				<p><?php esc_html_e( 'You can change your avatar at any time.', 'sitecompass' ); ?></p>
				<p><strong><em><?php esc_html_e( "Don't forget to click 'Save Settings' to save your changes.", 'sitecompass' ); ?></em></strong></p>
			</div>
			<form method="post" action="options.php">
				<?php settings_fields( 'sitecompass_avatar_settings' ); ?>
				<table class="form-table">
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_avatar_greeting"><?php esc_html_e( 'Avatar Greeting', 'sitecompass' ); ?></label>
						</th>
						<td>
							<input type="text" id="sitecompass_avatar_greeting" name="sitecompass_avatar_greeting" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_avatar_greeting', 'Hi there! 👋' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Short greeting text displayed with the avatar.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_custom_avatar_url"><?php esc_html_e( 'Custom Avatar URL (60x60px)', 'sitecompass' ); ?></label>
						</th>
						<td>
							<input type="text" id="sitecompass_custom_avatar_url" name="sitecompass_custom_avatar_url" class="regular-text" value="<?php echo esc_attr( get_option( 'sitecompass_custom_avatar_url', '' ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Enter a URL to use a custom avatar image. Leave empty to use the icon selection below.', 'sitecompass' ); ?></p>
						</td>
					</tr>
					<tr valign="top">
						<th scope="row">
							<label for="sitecompass_avatar_icon_setting"><?php esc_html_e( 'Avatar Icon Options', 'sitecompass' ); ?></label>
						</th>
						<td>
							<p><?php esc_html_e( "Select your icon by clicking on an image to select it. Don't forget to click 'Save Settings'.", 'sitecompass' ); ?></p>
							<input type="hidden" id="sitecompass_avatar_icon_setting" name="sitecompass_avatar_icon_setting" value="<?php echo esc_attr( $selected_icon ); ?>">
							<table>
								<?php
								$cols = 10;
								foreach ( $icon_sets as $set_name => $icon_count ) {
									$rows = ceil( $icon_count / $cols );
									$icon_index = 0;

									for ( $i = 0; $i < $rows; $i++ ) {
										echo '<tr>';
										for ( $j = 0; $j < $cols; $j++ ) {
											if ( $icon_index < $icon_count ) {
												$icon_name = sprintf( strtolower( str_replace( ' ', '-', $set_name ) ) . '-%03d.png', $icon_index );
												$selected = ( $icon_name === $selected_icon ) ? 'selected-icon' : '';
												$icon_url = $avatar->get_icon_url( $icon_name );
												echo '<td style="padding: 15px;">';
												echo '<img src="' . esc_url( $icon_url ) . '" id="' . esc_attr( $icon_name ) . '" onclick="selectIcon(\'' . esc_js( $icon_name ) . '\')" class="' . esc_attr( $selected ) . '" style="width:60px;height:60px;cursor:pointer;border:2px solid transparent;" alt="' . esc_attr( $icon_name ) . '" />';
												echo '</td>';
												$icon_index++;
											}
										}
										echo '</tr>';
									}
								}
								?>
							</table>
						</td>
					</tr>
				</table>
				<?php submit_button( __( 'Save Settings', 'sitecompass' ) ); ?>
			</form>
		</div>
		<script>
			window.onload = function() {
				var selectedAvatar = localStorage.getItem('sitecompass_avatar_icon_setting');
				var chatIconElement = document.getElementById('sitecompass_avatar_icon_setting');
				
				if (selectedAvatar && chatIconElement) {
					chatIconElement.value = selectedAvatar;
					var selectedIcon = document.getElementById(selectedAvatar);
					if (selectedIcon) {
						selectedIcon.style.border = "2px solid red";
					}
				} else if (chatIconElement && chatIconElement.value) {
					var currentIcon = document.getElementById(chatIconElement.value);
					if (currentIcon) {
						currentIcon.style.border = "2px solid red";
					}
				}

				window.selectIcon = function(icon) {
					var chatIconElement = document.getElementById('sitecompass_avatar_icon_setting');
					if (chatIconElement) {
						var previousIconId = chatIconElement.value;
						var previousIcon = document.getElementById(previousIconId);
						if (previousIcon) {
							previousIcon.style.border = "2px solid transparent";
						}

						var selectedIcon = document.getElementById(icon);
						if (selectedIcon) {
							selectedIcon.style.border = "2px solid red";
						}

						chatIconElement.value = icon;
						localStorage.setItem('sitecompass_avatar_icon_setting', icon);
					}
				}
			}
		</script>
		<?php
	}
}
