=== Site Compass ===
Contributors: sitecompassteam, ahsanzameer
Tags: chatbot, ai, openai, gpt, assistant, chat, pdf, knowledge-base, customer-support, virtual-assistant
Requires at least: 5.8
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

An AI-powered chatbot plugin that integrates OpenAI's language models with custom PDF knowledge bases to provide intelligent, context-aware responses to your visitors.

== Description ==

Site Compass transforms your WordPress website into an intelligent, interactive experience by integrating OpenAI's advanced language models. Create a powerful AI assistant that can answer visitor questions based on your custom PDF documents, providing accurate and contextual support 24/7.

= Key Features =

* **OpenAI Integration** - Seamlessly connect with OpenAI's GPT-4o, GPT-4o-mini, and GPT-4-turbo models
* **Custom Knowledge Base** - Upload PDF documents to create a personalized knowledge base for your AI assistant
* **Interactive Chatbox** - Beautiful, customizable chat interface that engages visitors
* **Conversation History** - Track and export all chat conversations for analysis and improvement
* **Customizable Appearance** - Adjust colors, width, and styling to match your brand
* **Avatar Selection** - Choose from predefined avatars or upload your own custom avatar
* **User Information Collection** - Optional form to collect visitor details (name, email, phone)
* **Session Management** - Persistent conversations across page navigation
* **CSV Export** - Export chat history for reporting and analysis
* **WordPress Standards Compliant** - Built following WordPress coding standards and best practices

= How It Works =

1. **Configure OpenAI** - Add your OpenAI API key in the settings
2. **Upload PDFs** - Add documents containing information you want the AI to reference
3. **Customize Appearance** - Adjust colors, avatars, and styling to match your brand
4. **Deploy Chatbox** - Use the `[sitecompass]` shortcode or automatic footer display
5. **Engage Visitors** - Your AI assistant answers questions based on your uploaded content

= Use Cases =

* **Customer Support** - Provide instant answers to common questions
* **Product Information** - Help visitors find product details and specifications
* **Documentation** - Make technical documentation easily accessible through conversation
* **Lead Generation** - Collect visitor information while providing helpful assistance
* **FAQ Automation** - Reduce support workload by automating frequently asked questions

= Privacy & Security =

Site Compass follows WordPress security best practices:
* All user inputs are sanitized and validated
* Nonce verification on all forms
* Capability checks for administrative functions
* Secure database queries using prepared statements
* Data is transmitted securely to OpenAI's API

== Installation ==

= Automatic Installation =

1. Log in to your WordPress admin panel
2. Navigate to Plugins > Add New
3. Search for "Site Compass"
4. Click "Install Now" and then "Activate"

= Manual Installation =

1. Download the plugin ZIP file
2. Log in to your WordPress admin panel
3. Navigate to Plugins > Add New > Upload Plugin
4. Choose the downloaded ZIP file and click "Install Now"
5. Click "Activate Plugin"

= Configuration =

1. Navigate to Site Compass > Settings in your WordPress admin
2. Enter your OpenAI API key (get one at https://platform.openai.com/api-keys)
3. Select your preferred GPT model
4. Configure bot name, instructions, and greetings
5. Upload PDF documents in the PDFs tab
6. Customize appearance in the Appearance tab
7. Select or upload an avatar in the Avatars tab
8. Add the `[sitecompass]` shortcode to any page or post

== Frequently Asked Questions ==

= Do I need an OpenAI API key? =

Yes, Site Compass requires an OpenAI API key to function. You can obtain one by creating an account at https://platform.openai.com/ and generating an API key in your account settings.

= What file formats are supported for the knowledge base? =

Currently, Site Compass supports PDF documents. You can upload multiple PDFs, and the AI assistant will use them as reference material when answering questions.

= How much does OpenAI API usage cost? =

OpenAI charges based on API usage. Costs vary depending on the model you select (GPT-4o, GPT-4o-mini, or GPT-4-turbo) and the number of tokens processed. Check OpenAI's pricing page for current rates: https://openai.com/pricing

= Can I customize the chatbox appearance? =

Yes! Site Compass provides extensive customization options including colors, width, custom CSS, and avatar selection. Navigate to Site Compass > Settings > Appearance to customize.

= Where can I view chat conversations? =

All conversations are stored in your WordPress database. Navigate to Site Compass > Chats to view, search, and export conversation history.

= Can I use the chatbot on multiple pages? =

Yes, you can add the `[sitecompass]` shortcode to any page or post. The chatbox can also be configured to appear automatically in the footer of all pages.

= Is the chat history exportable? =

Yes, you can export chat conversations to CSV format from the Site Compass > Chats admin page. You can filter by date range before exporting.

= Does the plugin work with caching plugins? =

Yes, Site Compass is compatible with most caching plugins. The chatbox uses AJAX for dynamic content, so it will function properly even on cached pages.

= Can visitors continue conversations across different pages? =

Yes, Site Compass uses session management to maintain conversation context as visitors navigate your website.

= How do I remove all plugin data when uninstalling? =

When you uninstall Site Compass through the WordPress Plugins page, all plugin data including database tables, options, and uploaded files will be automatically removed.

== Screenshots ==

1. Frontend chatbox interface with customizable appearance
2. Admin settings page - General tab with OpenAI configuration
3. PDF management interface for uploading knowledge base documents
4. Appearance customization with color pickers and style options
5. Avatar selection with predefined and custom upload options
6. Chat history view with search and export functionality
7. Individual conversation thread view with full message history

== Changelog ==

= 1.0.0 =
* Initial release
* OpenAI GPT-4o, GPT-4o-mini, and GPT-4-turbo integration
* PDF knowledge base support
* Customizable chatbox appearance
* Avatar selection and customization
* User information collection form
* Conversation history tracking
* CSV export functionality
* Session management for persistent conversations
* WordPress coding standards compliance
* Security hardening with input sanitization and output escaping
* Internationalization ready

== Upgrade Notice ==

= 1.0.0 =
Initial release of Site Compass. Welcome to intelligent, AI-powered customer engagement!

== Credits ==

= Development Team =
Site Compass was developed by the SiteCompass Team, building upon the foundation of ChatGenie by Ahsan Zameer. The team is committed to bringing advanced AI capabilities to the WordPress ecosystem.

**Core Contributors:**
* SiteCompass Team - Plugin architecture, WordPress standards compliance, and feature integration
* Ahsan Zameer - Original ChatGenie plugin development and OpenAI integration

= Mentors =
Special thanks to our mentors who provided guidance and expertise throughout the development process, helping ensure the plugin meets WordPress standards and best practices.

= Third-Party Services =
This plugin integrates with OpenAI's API to provide AI-powered chat functionality. By using this plugin, you agree to OpenAI's terms of service and privacy policy:
* OpenAI Terms: https://openai.com/terms
* OpenAI Privacy: https://openai.com/privacy

= Libraries & Resources =
* OpenAI Assistant API
* WordPress HTTP API
* WordPress Settings API

== Support ==

For support, feature requests, or bug reports, please visit:
* Plugin Website: https://sitecompass.ai
* Documentation: https://sitecompass.ai/docs

== Privacy Policy ==

Site Compass stores chat conversations in your WordPress database. When visitors interact with the chatbot:
* Messages are sent to OpenAI's API for processing
* Conversations are stored locally in your WordPress database
* Optional user information (name, email, phone) is collected only if the feature is enabled
* Session cookies are used to maintain conversation context

Please review OpenAI's privacy policy to understand how they handle data: https://openai.com/privacy

== Technical Requirements ==

* WordPress 5.8 or higher
* PHP 7.4 or higher
* MySQL 5.6 or higher
* OpenAI API key
* HTTPS recommended for secure API communication

== Developer Information ==

= Hooks & Filters =

Site Compass provides hooks and filters for developers to extend functionality:

* `sitecompass_chatbox_display` - Filter chatbox HTML output
* `sitecompass_message_before_send` - Filter message before sending to OpenAI
* `sitecompass_message_after_receive` - Filter assistant response before display
* `sitecompass_settings_tabs` - Add custom settings tabs
* `sitecompass_default_settings` - Modify default plugin settings

= Database Tables =

Site Compass creates the following custom database tables:
* `{prefix}sitecompass_pdfs` - Stores PDF metadata
* `{prefix}sitecompass_users` - Stores visitor information
* `{prefix}sitecompass_conversations` - Stores chat messages
* `{prefix}sitecompass_assistants` - Stores OpenAI assistant configurations

= Shortcode =

`[sitecompass]` - Display the chatbox on any page or post

== Roadmap ==

Future enhancements planned for Site Compass:
* Multi-language support
* Additional file format support (DOCX, TXT, HTML)
* Advanced analytics dashboard
* Integration with popular CRM systems
* Voice input/output capabilities
* Mobile app companion
* Custom training data management
* A/B testing for bot responses

== License ==

This plugin is licensed under the GPL v2 or later.

This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
