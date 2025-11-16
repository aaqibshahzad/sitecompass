# Manual Testing Checklist: Admin Interfaces

**Requirements Tested:** 5.2, 5.3, 5.4, 5.5, 5.6

## Prerequisites

- [ ] WordPress installation is running
- [ ] SiteCompass plugin is activated
- [ ] User is logged in as Administrator
- [ ] OpenAI API key is available for testing

## Test 1: Settings Save and Retrieval (Requirement 5.2)

### General Settings Tab

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Settings
2. Click on "General" tab
3. Fill in the following fields:
   - OpenAI API Key: [Enter valid API key]
   - GPT Model: Select "GPT-4o Mini"
   - Bot Name: "Test Bot"
   - Instructions: "You are a helpful assistant"
   - Prompt Placeholder: "Ask me anything..."
   - Initial Greeting: "Hello! Welcome!"
   - Subsequent Greeting: "Welcome back!"
   - Show User Form: Select "Yes"
4. Click "Save Settings"
5. Refresh the page

#### Expected Results:
- [ ] All fields retain their values after save
- [ ] Success message appears after saving
- [ ] No PHP errors or warnings
- [ ] Settings are persisted in database
- [ ] Form validation works (e.g., required fields)

### Verification:
```sql
-- Check settings in database
SELECT * FROM wp_options 
WHERE option_name LIKE 'sitecompass_%' 
ORDER BY option_name;
```

## Test 2: PDF Upload and Deletion (Requirement 5.5)

### PDF Upload

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Settings
2. Click on "PDFs" tab
3. Click "Choose File" and select a PDF file
4. Click "Upload PDFs"
5. Wait for upload to complete

#### Expected Results:
- [ ] File upload form is visible
- [ ] Only PDF files are accepted
- [ ] Success message appears after upload
- [ ] PDF appears in the uploaded PDFs table
- [ ] OpenAI File ID is displayed
- [ ] Upload date is shown
- [ ] PDF is accessible via link

### PDF Deletion

#### Steps:
1. In the uploaded PDFs table, click "Delete" on a PDF
2. Confirm deletion in the popup
3. Wait for page reload

#### Expected Results:
- [ ] Confirmation dialog appears
- [ ] PDF is removed from the table
- [ ] PDF file is deleted from WordPress uploads
- [ ] PDF is deleted from OpenAI storage
- [ ] Success message appears
- [ ] Database record is removed

### Verification:
```sql
-- Check PDFs in database
SELECT * FROM wp_sitecompass_pdfs 
ORDER BY created_at DESC;
```

## Test 3: Chat History Viewing (Requirement 5.2, 5.6)

### Conversations List

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Chats
2. Review the conversations list
3. Test search functionality:
   - Enter a session ID in search box
   - Click "Filter"
4. Test date filtering:
   - Select start date (7 days ago)
   - Select end date (today)
   - Click "Filter"
5. Click "Reset" to clear filters

#### Expected Results:
- [ ] Conversations are displayed in a table
- [ ] Table shows: Session ID, Thread ID, First Message, Last Message, Messages count
- [ ] Search filters conversations correctly
- [ ] Date filtering works correctly
- [ ] Reset button clears all filters
- [ ] Pagination works if more than 20 conversations
- [ ] "View" button is available for each conversation
- [ ] "Delete" button is available for each conversation

### Single Thread View

#### Steps:
1. Click "View" on any conversation
2. Review the thread details
3. Scroll through all messages

#### Expected Results:
- [ ] Thread details are displayed (Session ID, Thread ID, Total Messages)
- [ ] All messages are shown in chronological order
- [ ] User messages and assistant messages are visually distinct
- [ ] Message timestamps are displayed
- [ ] "Back to Chat History" button works
- [ ] "Export Thread to CSV" button is available

## Test 4: CSV Export (Requirement 5.6)

### Export All Conversations

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Chats
2. Apply date filter (optional)
3. Click "Export to CSV"
4. Open the downloaded CSV file

#### Expected Results:
- [ ] CSV file downloads automatically
- [ ] Filename includes timestamp
- [ ] CSV contains all expected columns
- [ ] Data is properly formatted
- [ ] Special characters are handled correctly
- [ ] Filtered data is exported (if filter applied)

### Export Single Thread

#### Steps:
1. View a single conversation thread
2. Click "Export Thread to CSV"
3. Open the downloaded CSV file

#### Expected Results:
- [ ] CSV file downloads automatically
- [ ] Filename includes thread ID and timestamp
- [ ] CSV contains only messages from that thread
- [ ] All messages are included in chronological order

### CSV Verification:
```
Expected columns:
- ID
- Session ID
- User ID
- Page ID
- User Type
- Thread ID
- Assistant ID
- Message
- Created At
```

## Test 5: Appearance Customization (Requirement 5.3)

### Color Settings

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Settings
2. Click on "Appearance" tab
3. Change the following colors:
   - Chatbot Button Background Color: #FF5733
   - Header Background Color: #3498DB
   - User Text Background Color: #2ECC71
4. Click "Save Changes"
5. Visit frontend and open chatbox

#### Expected Results:
- [ ] Color picker appears for each color field
- [ ] Colors can be selected visually
- [ ] Hex codes can be entered manually
- [ ] Changes are saved successfully
- [ ] Frontend chatbox reflects new colors
- [ ] All color settings work correctly

### Width Settings

#### Steps:
1. In Appearance tab, change "Chatbot Width Setting" to "Wide"
2. Set "Chatbot Width Wide" to "700px"
3. Click "Save Changes"
4. Visit frontend and open chatbox
5. Measure chatbox width

#### Expected Results:
- [ ] Width setting dropdown works
- [ ] Custom width values are accepted
- [ ] Frontend chatbox uses specified width
- [ ] Width changes are responsive

### Custom CSS

#### Steps:
1. In Appearance tab, add custom CSS:
   ```css
   .sitecompass-chat-button {
       border-radius: 50%;
   }
   ```
2. Click "Save Changes"
3. Visit frontend and inspect chatbox button

#### Expected Results:
- [ ] Custom CSS textarea accepts input
- [ ] Custom CSS is applied to frontend
- [ ] No CSS conflicts occur
- [ ] Invalid CSS doesn't break the page

### Restore Defaults

#### Steps:
1. Change several appearance settings
2. Set "Restore Defaults" to "Yes"
3. Click "Save Changes"
4. Review all appearance settings

#### Expected Results:
- [ ] All settings revert to default values
- [ ] Success message appears
- [ ] Frontend reflects default appearance
- [ ] "Restore Defaults" resets to "No"

## Test 6: Avatar Settings (Requirement 5.4)

### Avatar Selection

#### Steps:
1. Navigate to WordPress Admin > SiteCompass > Settings
2. Click on "Avatars" tab
3. Click on different avatar icons
4. Click "Save Settings"
5. Visit frontend and check chatbox button

#### Expected Results:
- [ ] Avatar grid is displayed
- [ ] Multiple avatar sets are available
- [ ] Clicking an avatar selects it (red border)
- [ ] Selected avatar is saved
- [ ] Frontend displays selected avatar
- [ ] Avatar categories are organized

### Custom Avatar URL

#### Steps:
1. In Avatars tab, enter a custom avatar URL
2. Click "Save Settings"
3. Visit frontend and check chatbox button

#### Expected Results:
- [ ] Custom URL field accepts input
- [ ] URL is validated
- [ ] Custom avatar overrides icon selection
- [ ] Frontend displays custom avatar
- [ ] Invalid URLs show error message

### Avatar Greeting

#### Steps:
1. In Avatars tab, change "Avatar Greeting" to "Need help?"
2. Click "Save Settings"
3. Visit frontend

#### Expected Results:
- [ ] Greeting text is saved
- [ ] Frontend displays new greeting
- [ ] Greeting appears near avatar button
- [ ] Special characters are handled correctly

## Test 7: Security and Capability Checks (Requirement 2.6)

### Capability Verification

#### Steps:
1. Log out of WordPress
2. Log in as a user with "Editor" role
3. Try to access WordPress Admin > SiteCompass

#### Expected Results:
- [ ] Non-admin users cannot access SiteCompass menu
- [ ] Direct URL access is blocked
- [ ] Appropriate error message is shown
- [ ] No sensitive data is exposed

### Nonce Verification

#### Steps:
1. Open browser DevTools > Network tab
2. Save settings in any admin page
3. Inspect the POST request
4. Try to replay the request without nonce

#### Expected Results:
- [ ] All form submissions include nonce
- [ ] Nonce is verified on server side
- [ ] Requests without nonce are rejected
- [ ] Appropriate error message is shown

## Test 8: Input Sanitization and Output Escaping (Requirements 2.3, 2.4)

### Input Sanitization

#### Steps:
1. Try to enter the following in various fields:
   - Bot Name: `<script>alert('XSS')</script>`
   - Instructions: `'; DROP TABLE wp_options; --`
   - Custom CSS: `</style><script>alert('XSS')</script>`
2. Save settings
3. View the saved values

#### Expected Results:
- [ ] HTML tags are stripped or escaped
- [ ] SQL injection attempts are prevented
- [ ] JavaScript is not executed
- [ ] Data is safely stored in database
- [ ] No security warnings appear

### Output Escaping

#### Steps:
1. Inspect the HTML source of admin pages
2. Check how user input is displayed
3. Look for unescaped output

#### Expected Results:
- [ ] All output is properly escaped
- [ ] No raw HTML from user input
- [ ] URLs are escaped with esc_url()
- [ ] Attributes are escaped with esc_attr()
- [ ] Text is escaped with esc_html()

## Test 9: Error Handling

### Invalid API Key

#### Steps:
1. Enter an invalid OpenAI API key
2. Try to upload a PDF
3. Try to send a message from frontend

#### Expected Results:
- [ ] Appropriate error message is shown
- [ ] No PHP fatal errors
- [ ] User is guided to fix the issue
- [ ] System remains functional

### File Upload Errors

#### Steps:
1. Try to upload a non-PDF file
2. Try to upload a file larger than PHP limit
3. Try to upload without selecting a file

#### Expected Results:
- [ ] File type validation works
- [ ] File size limits are enforced
- [ ] Clear error messages are shown
- [ ] No files are partially uploaded

## Test 10: Performance and Usability

### Page Load Times

#### Steps:
1. Measure load time of each admin page
2. Test with large datasets (100+ conversations)
3. Test pagination performance

#### Expected Results:
- [ ] Admin pages load in < 2 seconds
- [ ] Pagination handles large datasets
- [ ] No timeout errors
- [ ] UI remains responsive

### User Experience

#### Steps:
1. Navigate through all admin pages
2. Test all form interactions
3. Check for UI consistency

#### Expected Results:
- [ ] Navigation is intuitive
- [ ] Forms are easy to use
- [ ] Success/error messages are clear
- [ ] UI is consistent with WordPress standards
- [ ] No broken layouts or styling issues

## Test Results Summary

| Test | Status | Notes |
|------|--------|-------|
| Settings Save and Retrieval | ☐ Pass ☐ Fail | |
| PDF Upload and Deletion | ☐ Pass ☐ Fail | |
| Chat History Viewing | ☐ Pass ☐ Fail | |
| CSV Export | ☐ Pass ☐ Fail | |
| Appearance Customization | ☐ Pass ☐ Fail | |
| Avatar Settings | ☐ Pass ☐ Fail | |
| Security and Capability Checks | ☐ Pass ☐ Fail | |
| Input Sanitization and Output Escaping | ☐ Pass ☐ Fail | |
| Error Handling | ☐ Pass ☐ Fail | |
| Performance and Usability | ☐ Pass ☐ Fail | |

## Issues Found

Document any issues discovered during testing:

1. 
2. 
3. 

## Recommendations

Document any recommendations for improvements:

1. 
2. 
3. 

---

**Tested By:** _______________  
**Date:** _______________  
**WordPress Version:** _______________  
**PHP Version:** _______________  
**Browser:** _______________
