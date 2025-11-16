# Quick Start: Testing SiteCompass Plugin

## 🚀 Run Tests in 3 Steps

### Step 1: Access Test Runner
Navigate to your WordPress admin and go to:
```
/wp-admin/admin.php?page=sitecompass-run-tests
```

### Step 2: Review Automated Test Results
The page will automatically run all 20 automated tests and display results:
- ✅ Green = Passed
- ❌ Red = Failed
- ⚠️ Yellow = Warning (usually non-critical)

### Step 3: Complete Manual Tests
Download and follow these checklists:
1. `MANUAL-TEST-MESSAGE-FLOW.md` - Test frontend chatbox
2. `MANUAL-TEST-ADMIN-INTERFACES.md` - Test admin settings
3. `MANUAL-TEST-WORDPRESS-STANDARDS.md` - Test security

---

## 📊 What Gets Tested

### Automated Tests (20 total)

**Message Flow (6 tests)**
- Session management
- OpenAI API configuration
- Database tables
- AJAX handlers
- Chatbox display
- Security nonces

**Admin Interfaces (8 tests)**
- Settings save/retrieval
- PDF management
- Chat history
- Appearance customization
- Avatar settings
- Menu registration
- Access control

**WordPress Standards (6 tests)**
- Input sanitization
- Output escaping
- Nonce verification
- Capability checks
- Database security
- Internationalization

---

## ✅ Expected Results

When everything is working correctly:

```
Message Flow Tests: 6/6 Passed ✓
Admin Interface Tests: 8/8 Passed ✓
WordPress Standards Tests: 6/6 Passed ✓

Total: 20/20 Tests Passed ✓
```

---

## ⚠️ Common Warnings (OK to ignore)

- "Assistant ID not configured" - Normal on first run
- "Translation .pot file not found" - Can be generated later
- "Custom avatar URL not set" - Optional feature

---

## 🐛 If Tests Fail

1. **Check Prerequisites:**
   - Plugin is activated
   - Database tables exist (deactivate/reactivate plugin)
   - User has admin permissions

2. **Review Error Message:**
   - Read the specific error
   - Check which test failed
   - Follow suggested fixes

3. **Common Fixes:**
   - Reactivate plugin → Creates database tables
   - Clear cache → Fixes registration issues
   - Check file permissions → Fixes asset loading

---

## 📋 Manual Testing Checklist

After automated tests pass, complete these manual tests:

### Frontend Testing
- [ ] Send a message from chatbox
- [ ] Verify OpenAI response
- [ ] Test multi-turn conversation
- [ ] Test user form submission

### Admin Testing
- [ ] Save settings and verify
- [ ] Upload and delete PDF
- [ ] View chat history
- [ ] Export to CSV
- [ ] Customize appearance

### Security Testing
- [ ] Test with malicious input
- [ ] Test with non-admin user
- [ ] Verify nonce protection
- [ ] Check capability restrictions

---

## 📚 Full Documentation

For detailed information, see:

- `README.md` - Complete testing guide
- `TEST-EXECUTION-SUMMARY.md` - Detailed test execution instructions
- `FINAL-TEST-REPORT.md` - Comprehensive test report
- `MANUAL-TEST-*.md` - Step-by-step manual test checklists

---

## 🎯 Quick Commands

### Run Individual Test Files

```php
// Message Flow Tests
require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-message-flow.php';
$test = new Sitecompass_Ai_Message_Flow_Test();
$test->run_tests();

// Admin Interface Tests
require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-admin-interfaces.php';
$test = new Sitecompass_Ai_Admin_Interfaces_Test();
$test->run_tests();

// WordPress Standards Tests
require_once SITECOMPASS_AI_PLUGIN_DIR . 'tests/test-wordpress-standards.php';
$test = new Sitecompass_Ai_WordPress_Standards_Test();
$test->run_tests();
```

### Check Database Tables

```sql
-- Verify all tables exist
SHOW TABLES LIKE 'wp_sitecompass_%';

-- Check conversations
SELECT COUNT(*) FROM wp_sitecompass_conversations;

-- Check PDFs
SELECT * FROM wp_sitecompass_pdfs;

-- Check settings
SELECT * FROM wp_options WHERE option_name LIKE 'sitecompass_%';
```

---

## 🔧 Troubleshooting

### Test Runner Not Accessible
**Problem:** Can't access `/wp-admin/admin.php?page=sitecompass-run-tests`  
**Solution:** 
1. Check if plugin is activated
2. Verify you're logged in as admin
3. Clear WordPress cache

### Database Tables Missing
**Problem:** Tests fail with "table does not exist"  
**Solution:**
1. Deactivate plugin
2. Reactivate plugin
3. Run tests again

### OpenAI API Tests Fail
**Problem:** API configuration tests fail  
**Solution:**
1. Go to Settings > General
2. Enter valid OpenAI API key
3. Save settings
4. Run tests again

### Asset Files Not Found
**Problem:** CSS/JS file warnings  
**Solution:**
1. Verify files exist in `assets/` directory
2. Check file permissions
3. Clear browser cache

---

## 📞 Need Help?

1. **Review Documentation:** Check `README.md` for detailed information
2. **Check Error Messages:** Read the specific error and suggested fixes
3. **Review Code:** Look at the test file to understand what's being tested
4. **Report Issues:** Document the problem with environment details

---

## ✨ Success Criteria

Your plugin is ready for production when:

- ✅ All 20 automated tests pass
- ✅ All manual tests complete successfully
- ✅ No critical security issues found
- ✅ Performance is acceptable
- ✅ Works on target WordPress/PHP versions

---

**Quick Start Version:** 1.0.0  
**Last Updated:** 2024  
**For:** SiteCompass Plugin v1.0.0

**Ready to test?** Go to `/wp-admin/admin.php?page=sitecompass-run-tests` 🚀
