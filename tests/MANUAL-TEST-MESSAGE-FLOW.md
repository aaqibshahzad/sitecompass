# Manual Testing Checklist: Complete Message Flow

**Requirements Tested:** 6.4, 6.5, 6.6

## Prerequisites

- [ ] WordPress installation is running
- [ ] SiteCompass plugin is activated
- [ ] OpenAI API key is configured in Settings > General
- [ ] At least one PDF is uploaded (optional, for knowledge base testing)

## Test 1: Frontend Message Sending (Requirement 6.4)

### Steps:
1. Navigate to any page on the frontend
2. Verify the chatbox button appears in the bottom-right corner
3. Click the chatbox button to open the chat interface
4. Type a test message: "Hello, can you help me?"
5. Click the "Enter" button or press Enter key

### Expected Results:
- [ ] Chatbox button is visible with avatar
- [ ] Chatbox opens smoothly when clicked
- [ ] Message input field accepts text
- [ ] User message appears in the chat body immediately after sending
- [ ] User message has correct styling (user message bubble)
- [ ] Loading indicator appears while waiting for response
- [ ] No JavaScript errors in browser console

### Verification:
```javascript
// Check browser console for:
// - No errors
// - AJAX request to wp-admin/admin-ajax.php with action=sitecompass_send_message
// - Response received with success status
```

## Test 2: OpenAI API Integration (Requirement 6.5)

### Steps:
1. Send a message from the chatbox
2. Wait for the assistant response
3. Verify the response content

### Expected Results:
- [ ] Response is received within 30 seconds
- [ ] Assistant message appears in the chat body
- [ ] Assistant message has correct styling (bot message bubble)
- [ ] Response content is relevant to the question
- [ ] If PDFs are uploaded, assistant can reference PDF content
- [ ] Thread ID is maintained across multiple messages

### Verification:
```php
// Check WordPress debug.log for:
// - No OpenAI API errors
// - Successful thread creation
// - Successful message creation
// - Successful run completion
```

## Test 3: Database Storage (Requirement 6.6)

### Steps:
1. Send 2-3 messages in a conversation
2. Access WordPress database
3. Query the conversations table

### Expected Results:
- [ ] All user messages are stored in `wp_sitecompass_conversations` table
- [ ] All assistant messages are stored in `wp_sitecompass_conversations` table
- [ ] Each message has correct `user_type` ('user' or 'assistant')
- [ ] `session_id` is consistent for the conversation
- [ ] `thread_id` is populated and consistent
- [ ] `assistant_id` is populated
- [ ] `message_text` contains the full message content
- [ ] `created_at` timestamp is accurate

### SQL Verification:
```sql
-- Run this query in phpMyAdmin or database tool
SELECT * FROM wp_sitecompass_conversations 
ORDER BY created_at DESC 
LIMIT 10;

-- Verify columns:
-- - session_id (should be same for conversation)
-- - user_type (alternates between 'user' and 'assistant')
-- - thread_id (should be same for conversation)
-- - message_text (contains actual messages)
-- - created_at (recent timestamps)
```

## Test 4: Response Display (Requirement 6.6)

### Steps:
1. Send a message and receive a response
2. Refresh the page
3. Open the chatbox again

### Expected Results:
- [ ] Previous conversation is loaded from database
- [ ] Messages appear in correct chronological order
- [ ] User messages and assistant messages are visually distinct
- [ ] All message content is displayed correctly
- [ ] No duplicate messages appear
- [ ] Conversation history persists across page refreshes

## Test 5: Session Management

### Steps:
1. Open chatbox and send a message
2. Close chatbox
3. Navigate to a different page
4. Open chatbox again
5. Send another message

### Expected Results:
- [ ] Session ID cookie is set (`sitecompass_session_id`)
- [ ] Thread ID cookie is set (`sitecompass_thread_id`)
- [ ] Conversation continues in the same thread
- [ ] Previous messages are visible
- [ ] New messages are added to the same conversation

### Cookie Verification:
```javascript
// Check browser DevTools > Application > Cookies
// Should see:
// - sitecompass_session_id
// - sitecompass_thread_id
// Both should persist across page loads
```

## Test 6: Error Handling

### Steps:
1. Test with invalid API key (temporarily change in settings)
2. Test with network disconnection
3. Test with very long message (>1000 characters)

### Expected Results:
- [ ] Invalid API key shows user-friendly error message
- [ ] Network error shows appropriate message
- [ ] Long messages are handled gracefully
- [ ] No PHP fatal errors
- [ ] No JavaScript console errors
- [ ] User can retry after error

## Test 7: Security Verification

### Steps:
1. Inspect AJAX requests in browser DevTools
2. Verify nonce is included in all requests
3. Attempt to send request without nonce

### Expected Results:
- [ ] All AJAX requests include `nonce` parameter
- [ ] Nonce is verified on server side
- [ ] Request without nonce is rejected with 403 error
- [ ] User input is sanitized before storage
- [ ] Output is escaped when displayed

## Test 8: Multi-Message Conversation

### Steps:
1. Send message: "What is your name?"
2. Wait for response
3. Send message: "Can you remember what I just asked?"
4. Wait for response

### Expected Results:
- [ ] Assistant responds to first question
- [ ] Assistant can reference previous messages in thread
- [ ] Conversation context is maintained
- [ ] All messages are stored in database
- [ ] Thread ID remains consistent

## Test 9: User Form Integration (if enabled)

### Steps:
1. Enable user form in Settings > General
2. Open chatbox on frontend
3. Fill in user information form
4. Submit form
5. Send a message

### Expected Results:
- [ ] User form appears before chat interface
- [ ] Form validation works correctly
- [ ] User data is saved to `wp_sitecompass_users` table
- [ ] User session cookie is set
- [ ] Chat interface appears after form submission
- [ ] User ID is associated with conversation messages

## Test 10: Performance Testing

### Steps:
1. Send 10 messages in quick succession
2. Monitor response times
3. Check database query performance

### Expected Results:
- [ ] All messages are processed successfully
- [ ] Response times are consistent (< 30 seconds each)
- [ ] No database deadlocks or errors
- [ ] No memory issues
- [ ] Browser remains responsive

## Test Results Summary

| Test | Status | Notes |
|------|--------|-------|
| Frontend Message Sending | ☐ Pass ☐ Fail | |
| OpenAI API Integration | ☐ Pass ☐ Fail | |
| Database Storage | ☐ Pass ☐ Fail | |
| Response Display | ☐ Pass ☐ Fail | |
| Session Management | ☐ Pass ☐ Fail | |
| Error Handling | ☐ Pass ☐ Fail | |
| Security Verification | ☐ Pass ☐ Fail | |
| Multi-Message Conversation | ☐ Pass ☐ Fail | |
| User Form Integration | ☐ Pass ☐ Fail | |
| Performance Testing | ☐ Pass ☐ Fail | |

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
