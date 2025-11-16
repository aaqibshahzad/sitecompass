# Manual Testing Checklist: WordPress Standards Compliance

**Requirements Tested:** 2.3, 2.4, 2.5, 2.6

## Prerequisites

- [ ] WordPress installation is running
- [ ] SiteCompass plugin is activated
- [ ] User is logged in as Administrator
- [ ] Browser DevTools available for inspection

## Test 1: Input Sanitization (Requirement 2.4)

### Text Field Sanitization

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Settings > General
2. Enter the following in "Bot Name" field:
   ```
   <script>alert('XSS')</script>Test Bot
   ```
3. Click "Save Settings"
4. Refresh the page and check the field value
5. Check database value:
   ```sql
   SELECT option_value FROM wp_options WHERE option_name = 'sitecompass_bot_name';
   ```

#### Expected Results:
- [ ] HTML tags are stripped from input
- [ ] Only "Test Bot" remains (or "scriptalert('XSS')/scriptTest Bot")
- [ ] No JavaScript is executed
- [ ] Database contains sanitized value
- [ ] No XSS vulnerability

### Email Sanitization

#### Steps:
1. Enable user form in Settings
2. Open chatbox on frontend
3. Enter the following in email field:
   ```
   test@example.com<script>alert('XSS')</script>
   ```
4. Submit the form
5. Check database:
   ```sql
   SELECT email FROM wp_sitecompass_users ORDER BY id DESC LIMIT 1;
   ```

#### Expected Results:
- [ ] Email is sanitized to "test@example.com"
- [ ] Script tags are removed
- [ ] No JavaScript is executed
- [ ] Database contains clean email

### Textarea Sanitization

#### Steps:
1. Navigate to Settings > General
2. Enter the following in "Instructions" field:
   ```
   You are a helpful assistant.
   <script>alert('XSS')</script>
   <b>Bold text</b>
   ```
3. Save settings
4. Check the saved value

#### Expected Results:
- [ ] Script tags are removed
- [ ] Line breaks are preserved
- [ ] HTML tags are stripped or escaped
- [ ] No JavaScript is executed

### SQL Injection Prevention

#### Steps:
1. Try to enter SQL injection in search field:
   ```
   ' OR '1'='1
   ```
2. Try in chat message:
   ```
   '; DROP TABLE wp_sitecompass_conversations; --
   ```
3. Check database integrity

#### Expected Results:
- [ ] SQL injection attempts are escaped
- [ ] No database errors occur
- [ ] Tables remain intact
- [ ] Queries use $wpdb->prepare()

## Test 2: Output Escaping (Requirement 2.3)

### HTML Output Escaping

#### Steps:
1. Save a bot name with special characters:
   ```
   Test & Bot <Company>
   ```
2. View the chatbox on frontend
3. Inspect the HTML source
4. Check if special characters are escaped

#### Expected Results:
- [ ] `&` is escaped as `&amp;`
- [ ] `<` is escaped as `&lt;`
- [ ] `>` is escaped as `&gt;`
- [ ] No raw HTML in output
- [ ] esc_html() is used

### Attribute Escaping

#### Steps:
1. Set a custom avatar URL with special characters
2. View the chatbox on frontend
3. Inspect the `<img>` tag's `src` attribute
4. Check for proper escaping

#### Expected Results:
- [ ] Quotes are escaped
- [ ] Special characters are handled
- [ ] esc_attr() is used
- [ ] No attribute injection possible

### URL Escaping

#### Steps:
1. Upload a PDF with special characters in filename
2. View the PDF list in admin
3. Click the PDF link
4. Inspect the URL

#### Expected Results:
- [ ] URL is properly encoded
- [ ] Special characters are escaped
- [ ] esc_url() is used
- [ ] Link works correctly

### JavaScript Escaping

#### Steps:
1. Set a greeting with quotes:
   ```
   Hello! I'm here to help.
   ```
2. View the chatbox on frontend
3. Inspect JavaScript variables in source
4. Check for proper escaping

#### Expected Results:
- [ ] Quotes are escaped in JavaScript
- [ ] esc_js() is used where appropriate
- [ ] No JavaScript errors
- [ ] No code injection possible

## Test 3: Nonce Verification (Requirement 2.5)

### Form Nonce Verification

#### Steps:
1. Navigate to Settings > General
2. Open browser DevTools > Network tab
3. Change a setting and click "Save Settings"
4. Inspect the POST request
5. Look for nonce field

#### Expected Results:
- [ ] Nonce field is present in form
- [ ] Nonce is included in POST data
- [ ] Nonce name follows WordPress conventions
- [ ] wp_nonce_field() is used

### AJAX Nonce Verification

#### Steps:
1. Open chatbox on frontend
2. Open browser DevTools > Network tab
3. Send a message
4. Inspect the AJAX request
5. Look for nonce parameter

#### Expected Results:
- [ ] Nonce is included in AJAX data
- [ ] Nonce is verified on server side
- [ ] wp_create_nonce() is used
- [ ] wp_verify_nonce() is used

### Nonce Rejection Test

#### Steps:
1. Open browser DevTools > Console
2. Try to send AJAX request without nonce:
   ```javascript
   jQuery.post(ajaxurl, {
       action: 'sitecompass_send_message',
       message: 'Test'
   });
   ```
3. Check the response

#### Expected Results:
- [ ] Request is rejected
- [ ] 403 Forbidden status
- [ ] Error message is returned
- [ ] No action is performed

### URL Nonce Verification

#### Steps:
1. Navigate to Chats page
2. Click "Delete" on a conversation
3. Inspect the URL
4. Try to access URL without nonce

#### Expected Results:
- [ ] Nonce is in URL
- [ ] wp_nonce_url() is used
- [ ] URL without nonce is rejected
- [ ] Appropriate error message shown

## Test 4: Capability Checks (Requirement 2.6)

### Admin Page Access Control

#### Steps:
1. Log out of WordPress
2. Log in as a user with "Editor" role
3. Try to access:
   - /wp-admin/admin.php?page=sitecompass
   - /wp-admin/admin.php?page=sitecompass-settings
4. Check the response

#### Expected Results:
- [ ] Access is denied
- [ ] "You do not have sufficient permissions" message
- [ ] User is not redirected to admin pages
- [ ] current_user_can('manage_options') is checked

### AJAX Action Access Control

#### Steps:
1. Log in as Editor
2. Open browser DevTools > Console
3. Try to execute admin AJAX action:
   ```javascript
   jQuery.post(ajaxurl, {
       action: 'sitecompass_submit_user_info',
       nonce: 'test',
       user_name: 'Test'
   });
   ```
4. Check the response

#### Expected Results:
- [ ] Non-admin actions work (if appropriate)
- [ ] Admin-only actions are blocked
- [ ] Capability checks are in place
- [ ] Appropriate error message

### Settings Save Access Control

#### Steps:
1. As Editor, try to access settings page directly
2. Try to POST to options.php
3. Check if settings can be modified

#### Expected Results:
- [ ] Settings page is inaccessible
- [ ] POST requests are rejected
- [ ] Settings remain unchanged
- [ ] WordPress handles capability checks

### File Upload Access Control

#### Steps:
1. As Editor, try to access PDF upload page
2. Try to upload a file
3. Check if upload is processed

#### Expected Results:
- [ ] Upload page is inaccessible
- [ ] Upload attempts are rejected
- [ ] No files are uploaded
- [ ] Capability checks prevent upload

## Test 5: Database Query Security (Requirement 2.5)

### Prepared Statements Usage

#### Steps:
1. Review code in these files:
   - admin/class-chats.php
   - admin/class-pdf-manager.php
   - public/class-message-handler.php
2. Look for $wpdb queries
3. Verify $wpdb->prepare() is used

#### Expected Results:
- [ ] All queries with user input use prepare()
- [ ] Placeholders (%s, %d, %i) are used correctly
- [ ] No direct variable interpolation in queries
- [ ] No SQL injection vulnerabilities

### Search Query Security

#### Steps:
1. Navigate to Chats page
2. Enter SQL injection in search:
   ```
   ' UNION SELECT * FROM wp_users --
   ```
3. Click Filter
4. Check results and database logs

#### Expected Results:
- [ ] Search is safely escaped
- [ ] No SQL errors occur
- [ ] No unauthorized data is returned
- [ ] $wpdb->esc_like() is used

### Date Filter Security

#### Steps:
1. In Chats page, enter malicious date:
   ```
   2024-01-01' OR '1'='1
   ```
2. Click Filter
3. Check results

#### Expected Results:
- [ ] Date is validated
- [ ] Invalid dates are rejected
- [ ] No SQL injection occurs
- [ ] Proper date sanitization

## Test 6: Code Quality Checks

### PHP Syntax Check

#### Steps:
1. Run PHP syntax check on all files:
   ```bash
   find sitecompass -name "*.php" -exec php -l {} \;
   ```

#### Expected Results:
- [ ] No syntax errors
- [ ] All files parse correctly
- [ ] No deprecated functions used

### WordPress Coding Standards

#### Steps:
1. Install PHP_CodeSniffer with WordPress standards
2. Run PHPCS on plugin:
   ```bash
   phpcs --standard=WordPress sitecompass/
   ```

#### Expected Results:
- [ ] No critical errors
- [ ] Warnings are acceptable
- [ ] Code follows WordPress standards
- [ ] Naming conventions are correct

### Security Scan

#### Steps:
1. Use a security scanner (e.g., WPScan, Sucuri)
2. Scan the plugin for vulnerabilities
3. Review the report

#### Expected Results:
- [ ] No critical vulnerabilities
- [ ] No known exploits
- [ ] Security best practices followed
- [ ] No hardcoded credentials

## Test 7: Internationalization (Requirement 2.2)

### Text Domain Usage

#### Steps:
1. Search for all translation functions in code
2. Verify text domain is 'sitecompass-ai'
3. Check for consistency

#### Expected Results:
- [ ] All strings use 'sitecompass-ai' text domain
- [ ] No mixed text domains
- [ ] __() and _e() functions used correctly
- [ ] esc_html__() and esc_html_e() used for output

### Translation File

#### Steps:
1. Check if .pot file exists:
   - languages/sitecompass-ai.pot
2. Verify it contains all translatable strings
3. Check file format

#### Expected Results:
- [ ] .pot file exists
- [ ] All strings are included
- [ ] File is properly formatted
- [ ] Metadata is correct

### Translation Loading

#### Steps:
1. Check main plugin file for load_plugin_textdomain()
2. Verify domain path is correct
3. Test with a translation file

#### Expected Results:
- [ ] Text domain is loaded
- [ ] Domain path is '/languages'
- [ ] Translations work when added
- [ ] load_plugin_textdomain() is called

## Test Results Summary

| Test | Status | Notes |
|------|--------|-------|
| Text Field Sanitization | ☐ Pass ☐ Fail | |
| Email Sanitization | ☐ Pass ☐ Fail | |
| Textarea Sanitization | ☐ Pass ☐ Fail | |
| SQL Injection Prevention | ☐ Pass ☐ Fail | |
| HTML Output Escaping | ☐ Pass ☐ Fail | |
| Attribute Escaping | ☐ Pass ☐ Fail | |
| URL Escaping | ☐ Pass ☐ Fail | |
| JavaScript Escaping | ☐ Pass ☐ Fail | |
| Form Nonce Verification | ☐ Pass ☐ Fail | |
| AJAX Nonce Verification | ☐ Pass ☐ Fail | |
| Nonce Rejection Test | ☐ Pass ☐ Fail | |
| URL Nonce Verification | ☐ Pass ☐ Fail | |
| Admin Page Access Control | ☐ Pass ☐ Fail | |
| AJAX Action Access Control | ☐ Pass ☐ Fail | |
| Settings Save Access Control | ☐ Pass ☐ Fail | |
| File Upload Access Control | ☐ Pass ☐ Fail | |
| Prepared Statements Usage | ☐ Pass ☐ Fail | |
| Search Query Security | ☐ Pass ☐ Fail | |
| Date Filter Security | ☐ Pass ☐ Fail | |
| PHP Syntax Check | ☐ Pass ☐ Fail | |
| WordPress Coding Standards | ☐ Pass ☐ Fail | |
| Security Scan | ☐ Pass ☐ Fail | |
| Text Domain Usage | ☐ Pass ☐ Fail | |
| Translation File | ☐ Pass ☐ Fail | |
| Translation Loading | ☐ Pass ☐ Fail | |

## Critical Security Issues Found

Document any critical security issues:

1. 
2. 
3. 

## Recommendations

Document recommendations for security improvements:

1. 
2. 
3. 

---

**Tested By:** _______________  
**Date:** _______________  
**WordPress Version:** _______________  
**PHP Version:** _______________  
**Security Tools Used:** _______________
