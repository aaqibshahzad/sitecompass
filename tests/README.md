# SiteCompass Testing Suite

This directory contains all automated and manual tests for the SiteCompass plugin.

## Overview

The testing suite is organized into three main categories:

1. **Message Flow Tests** - Testing the complete chat functionality
2. **Admin Interface Tests** - Testing all admin pages and settings
3. **WordPress Standards Tests** - Testing compliance with WordPress coding standards

## Automated Tests

### Running All Tests

To run all automated tests:

1. Navigate to WordPress Admin
2. Go to: `/wp-admin/admin.php?page=sitecompass-run-tests`
3. Review the test results on the page

### Individual Test Files

#### 1. Message Flow Tests (`test-message-flow.php`)

Tests the complete message flow from frontend to OpenAI API and back.

**Requirements Tested:** 6.4, 6.5, 6.6

**Tests Include:**
- Session management functionality
- OpenAI API configuration
- Database table existence and structure
- Message handler AJAX registration
- Chatbox display functionality
- Nonce verification

**To Run:**
```php
require_once 'test-message-flow.php';
$test = new Sitecompass_Ai_Message_Flow_Test();
$test->run_tests();
```

#### 2. Admin Interface Tests (`test-admin-interfaces.php`)

Tests all admin interface functionality.

**Requirements Tested:** 5.2, 5.3, 5.4, 5.5, 5.6

**Tests Include:**
- Settings registration and save/retrieval
- PDF management functionality
- Chat history interface
- Appearance customization
- Avatar settings
- Admin menu registration
- Capability checks

**To Run:**
```php
require_once 'test-admin-interfaces.php';
$test = new Sitecompass_Ai_Admin_Interfaces_Test();
$test->run_tests();
```

#### 3. WordPress Standards Tests (`test-wordpress-standards.php`)

Tests compliance with WordPress coding standards.

**Requirements Tested:** 2.3, 2.4, 2.5, 2.6

**Tests Include:**
- Input sanitization
- Output escaping
- Nonce verification
- Capability checks
- Database query security
- Internationalization

**To Run:**
```php
require_once 'test-wordpress-standards.php';
$test = new Sitecompass_Ai_WordPress_Standards_Test();
$test->run_tests();
```

## Manual Tests

Manual testing checklists are provided for comprehensive testing that cannot be automated.

### Manual Test Checklists

1. **MANUAL-TEST-MESSAGE-FLOW.md**
   - Frontend message sending
   - OpenAI API integration
   - Database storage
   - Response display
   - Session management
   - Error handling
   - Security verification
   - Multi-message conversations
   - User form integration
   - Performance testing

2. **MANUAL-TEST-ADMIN-INTERFACES.md**
   - Settings save and retrieval
   - PDF upload and deletion
   - Chat history viewing
   - CSV export
   - Appearance customization
   - Avatar settings
   - Security and capability checks
   - Input sanitization and output escaping
   - Error handling
   - Performance and usability

3. **MANUAL-TEST-WORDPRESS-STANDARDS.md**
   - Text field sanitization
   - Email sanitization
   - Textarea sanitization
   - SQL injection prevention
   - HTML output escaping
   - Attribute escaping
   - URL escaping
   - JavaScript escaping
   - Form nonce verification
   - AJAX nonce verification
   - Admin page access control
   - Prepared statements usage
   - Code quality checks
   - Internationalization

## Test Requirements

### Prerequisites

- WordPress 5.0 or higher
- PHP 7.4 or higher
- SiteCompass plugin activated
- Administrator user account
- OpenAI API key (for integration tests)

### Optional Tools

- **PHP_CodeSniffer** - For WordPress coding standards check
  ```bash
  composer global require "squizlabs/php_codesniffer=*"
  composer global require wp-coding-standards/wpcs
  phpcs --config-set installed_paths ~/.composer/vendor/wp-coding-standards/wpcs
  ```

- **WPScan** - For security scanning
  ```bash
  gem install wpscan
  ```

## Test Coverage

### Requirements Coverage

| Requirement | Test Type | Status |
|-------------|-----------|--------|
| 2.2 - Internationalization | Automated + Manual | ✅ |
| 2.3 - Output Escaping | Automated + Manual | ✅ |
| 2.4 - Input Sanitization | Automated + Manual | ✅ |
| 2.5 - Nonce Verification | Automated + Manual | ✅ |
| 2.6 - Capability Checks | Automated + Manual | ✅ |
| 5.2 - Admin Pages | Automated + Manual | ✅ |
| 5.3 - Appearance Settings | Automated + Manual | ✅ |
| 5.4 - Avatar Settings | Automated + Manual | ✅ |
| 5.5 - PDF Management | Automated + Manual | ✅ |
| 5.6 - CSV Export | Manual | ✅ |
| 6.4 - Message Sending | Automated + Manual | ✅ |
| 6.5 - OpenAI Integration | Automated + Manual | ✅ |
| 6.6 - Database Storage | Automated + Manual | ✅ |

## Running Tests in Different Environments

### Local Development

```bash
# Navigate to WordPress installation
cd /path/to/wordpress

# Access test runner via browser
open http://localhost/wp-admin/admin.php?page=sitecompass-run-tests
```

### Staging Environment

1. Deploy plugin to staging
2. Access test runner URL
3. Review results
4. Perform manual tests
5. Document any issues

### Production Testing

⚠️ **Warning:** Do not run automated tests on production!

For production, only perform:
- Manual smoke tests
- Monitoring of error logs
- User acceptance testing

## Interpreting Test Results

### Automated Tests

- **✓ PASSED** - Test completed successfully
- **✗ FAILED** - Test failed, review error message
- **⚠ WARNING** - Potential issue, may need investigation

### Manual Tests

- **☐ Pass** - Test passed all criteria
- **☐ Fail** - Test failed, document issue
- **N/A** - Test not applicable

## Reporting Issues

When a test fails:

1. Document the exact steps to reproduce
2. Note the WordPress version
3. Note the PHP version
4. Include error messages
5. Include screenshots if applicable
6. Create a bug report with all details

## Best Practices

1. **Run tests after every code change**
2. **Perform manual tests before releases**
3. **Test on multiple WordPress versions**
4. **Test on multiple PHP versions**
5. **Test with different user roles**
6. **Test with real OpenAI API**
7. **Test with large datasets**
8. **Test error scenarios**
9. **Test security vulnerabilities**
10. **Document all test results**

## Continuous Integration

To integrate with CI/CD:

```yaml
# Example GitHub Actions workflow
name: Tests
on: [push, pull_request]
jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2
      - name: Setup WordPress
        run: |
          # Setup WordPress test environment
      - name: Run Tests
        run: |
          # Run automated tests
```

## Contributing

When adding new features:

1. Write tests for new functionality
2. Update existing tests if needed
3. Add manual test cases
4. Update this README
5. Ensure all tests pass

## Support

For questions about testing:

- Review test documentation
- Check WordPress testing handbook
- Contact the development team

---

**Last Updated:** 2024
**Test Suite Version:** 1.0.0
**Plugin Version:** 1.0.0
