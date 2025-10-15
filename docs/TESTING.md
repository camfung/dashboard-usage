# Testing Guide

Comprehensive guide to testing the User Activity Dashboard plugin, including unit tests, integration tests, and manual testing procedures.

## Table of Contents

1. [Testing Overview](#testing-overview)
2. [PHPUnit Setup](#phpunit-setup)
3. [Running Tests](#running-tests)
4. [Test Coverage](#test-coverage)
5. [Writing New Tests](#writing-new-tests)
6. [Manual Testing](#manual-testing)
7. [Browser Testing](#browser-testing)
8. [Performance Testing](#performance-testing)
9. [Continuous Integration](#continuous-integration)

## Testing Overview

### Test Strategy

The plugin uses a **test-driven development (TDD)** approach:

1. Write tests first
2. Run tests (they fail)
3. Write minimum code to pass
4. Refactor
5. Repeat

### Test Types

**Unit Tests:**
- Test individual methods in isolation
- Mock external dependencies
- Fast execution
- Located in `tests/`

**Integration Tests:**
- Test multiple components together
- Real API calls (for API client)
- Slower execution
- Also in `tests/`

**Manual Tests:**
- UI/UX testing
- Browser compatibility
- Visual regression
- User acceptance

## PHPUnit Setup

### Prerequisites

```bash
# PHP 7.4 or higher
php -v

# Composer installed
composer --version
```

### Install PHPUnit

**File:** `composer.json`

```json
{
    "require-dev": {
        "phpunit/phpunit": "^9.6"
    }
}
```

```bash
composer install
```

### Configuration

**File:** `phpunit.xml`

```xml
<?xml version="1.0" encoding="UTF-8"?>
<phpunit
    bootstrap="vendor/autoload.php"
    colors="true"
    verbose="true"
    stopOnFailure="false"
    beStrictAboutOutputDuringTests="true">

    <testsuites>
        <testsuite name="User Activity Dashboard">
            <directory>tests</directory>
        </testsuite>
    </testsuites>

    <coverage processUncoveredFiles="true">
        <include>
            <directory suffix=".php">lib</directory>
            <directory suffix=".php">src</directory>
        </include>
    </coverage>
</phpunit>
```

## Running Tests

### Basic Usage

```bash
# Run all tests
./vendor/bin/phpunit

# Run with verbose output
./vendor/bin/phpunit --verbose

# Run specific test file
./vendor/bin/phpunit tests/UserActivityClientTest.php

# Run specific test method
./vendor/bin/phpunit --filter test_getUserActivity_returns_valid_data
```

### With Code Coverage

**Requires Xdebug:**

```bash
# Install xdebug
pecl install xdebug

# Run tests with coverage
./vendor/bin/phpunit --coverage-html coverage/

# View coverage report
open coverage/index.html
```

### Output Examples

**Successful run:**

```
PHPUnit 9.6.15 by Sebastian Bergmann and contributors.

...............                                                   17 / 17 (100%)

Time: 00:02.145, Memory: 8.00 MB

OK (17 tests, 93 assertions)
```

**Failed test:**

```
F..

1) UserActivityClientTest::test_getUserActivity_returns_valid_data
Failed asserting that false is true.

/path/to/tests/UserActivityClientTest.php:25

FAILURES!
Tests: 17, Assertions: 93, Failures: 1.
```

## Test Coverage

### Current Test Suite

**File:** `tests/UserActivityClientTest.php`

**Statistics:**
- 17 test cases
- 93 assertions
- ~95% code coverage for API client

### Test Cases

#### 1. getUserActivity Tests

```php
test_getUserActivity_returns_valid_data()
test_getUserActivity_with_date_range()
test_getUserActivity_validates_response_structure()
test_getUserActivity_calculates_correct_totals()
```

#### 2. describeTable Tests

```php
test_describeTable_returns_schema()
test_describeTable_has_table_metadata()
```

#### 3. getSampleData Tests

```php
test_getSampleData_returns_limited_records()
test_getSampleData_respects_limit_parameter()
```

#### 4. Data Validation Tests

```php
test_response_has_success_field()
test_response_has_source_array()
test_activity_record_has_required_fields()
test_date_format_is_valid()
test_numeric_fields_are_numeric()
```

#### 5. Error Handling Tests

```php
test_invalid_user_throws_exception()
test_network_error_handling()
test_malformed_response_handling()
```

### Coverage Report

```
Code Coverage Report:
  UserActivityClient.php       95.24%
  ApiException.php            100.00%
  Overall                      96.15%

Uncovered:
  - Constructor exception handling (rare edge case)
  - Network timeout scenarios (requires mock)
```

## Writing New Tests

### Test Class Structure

```php
<?php
namespace TpBloomland\UserActivity\Tests;

use PHPUnit\Framework\TestCase;
use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

class UserActivityClientTest extends TestCase
{
    private $client;

    /**
     * Set up before each test
     */
    protected function setUp(): void
    {
        $this->client = new UserActivityClient();
    }

    /**
     * Clean up after each test
     */
    protected function tearDown(): void
    {
        $this->client = null;
    }

    /**
     * Test method
     */
    public function test_example()
    {
        // Arrange
        $userId = 125;

        // Act
        $result = $this->client->getUserActivity($userId);

        // Assert
        $this->assertTrue($result['success']);
    }
}
```

### Assertion Examples

#### Basic Assertions

```php
// Boolean
$this->assertTrue($value);
$this->assertFalse($value);

// Equality
$this->assertEquals($expected, $actual);
$this->assertSame($expected, $actual);  // Strict comparison

// Type checking
$this->assertIsArray($value);
$this->assertIsString($value);
$this->assertIsInt($value);
$this->assertIsBool($value);

// Null checking
$this->assertNull($value);
$this->assertNotNull($value);

// Empty checking
$this->assertEmpty($value);
$this->assertNotEmpty($value);
```

#### Array Assertions

```php
// Array has key
$this->assertArrayHasKey('success', $response);

// Array contains
$this->assertContains('value', $array);

// Count
$this->assertCount(10, $array);
$this->assertGreaterThan(0, count($array));
```

#### String Assertions

```php
// String contains
$this->assertStringContainsString('error', $message);

// Regex match
$this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $date);
```

#### Exception Assertions

```php
// Expect exception
$this->expectException(ApiException::class);
$this->expectExceptionMessage('Invalid user');
$this->expectExceptionCode(404);

$client->getUserActivity(999999);
```

### Example: Testing a New Method

**Scenario:** Add test for a new `getMonthlyActivity()` method

```php
/**
 * Test getMonthlyActivity returns valid data
 */
public function test_getMonthlyActivity_returns_valid_data()
{
    // Arrange
    $userId = 125;
    $month = '2025-09';

    // Act
    $result = $this->client->getMonthlyActivity($userId, $month);

    // Assert
    $this->assertIsArray($result);
    $this->assertArrayHasKey('success', $result);
    $this->assertTrue($result['success']);
    $this->assertArrayHasKey('source', $result);
    $this->assertIsArray($result['source']);

    // Verify month is correct
    foreach ($result['source'] as $record) {
        $this->assertStringStartsWith($month, $record['date']);
    }
}

/**
 * Test getMonthlyActivity validates month format
 */
public function test_getMonthlyActivity_validates_month_format()
{
    $this->expectException(ApiException::class);
    $this->expectExceptionMessage('Invalid month format');

    // Invalid format
    $this->client->getMonthlyActivity(125, '09-2025');
}

/**
 * Test getMonthlyActivity returns 30 days for September
 */
public function test_getMonthlyActivity_returns_correct_day_count()
{
    $result = $this->client->getMonthlyActivity(125, '2025-09');

    // September has 30 days
    $this->assertCount(30, $result['source']);
}
```

### Data Providers

For testing multiple scenarios:

```php
/**
 * @dataProvider validUserIdProvider
 */
public function test_getUserActivity_with_various_users($userId)
{
    $result = $this->client->getUserActivity($userId);
    $this->assertTrue($result['success']);
}

public function validUserIdProvider()
{
    return [
        'user 1' => [1],
        'user 125' => [125],
        'user 500' => [500],
    ];
}
```

### Mocking (for WordPress functions)

```php
use Mockery;

public function test_wordpress_integration()
{
    // Mock WordPress function
    $mock = Mockery::mock('alias:WP_User');
    $mock->shouldReceive('get_current_user_id')
         ->andReturn(125);

    // Test code that uses get_current_user_id()
    $userId = get_current_user_id();
    $this->assertEquals(125, $userId);
}

protected function tearDown(): void
{
    Mockery::close();
}
```

## Manual Testing

### Pre-Release Checklist

#### Installation Tests

- [ ] Fresh WordPress installation
- [ ] Plugin activates without errors
- [ ] Shortcode renders on page
- [ ] No PHP errors in debug log
- [ ] Assets load correctly (CSS, JS)

#### Functionality Tests

- [ ] Default shortcode displays data
- [ ] Chart renders correctly
- [ ] Table displays with data
- [ ] Pagination works (if >10 rows)
- [ ] Date picker updates data
- [ ] Summary cards show correct totals
- [ ] All columns display properly

#### Date Picker Tests

- [ ] Start date input works
- [ ] End date input works
- [ ] Validation: start < end
- [ ] Validation: dates <= today
- [ ] Update button triggers reload
- [ ] Reset button clears dates
- [ ] URL parameters update correctly
- [ ] Bookmarking date range works

#### Pagination Tests

- [ ] First page displays correctly
- [ ] Next button works
- [ ] Previous button works
- [ ] Last button works
- [ ] Page numbers display correctly
- [ ] Clicking page number works
- [ ] Rows per page selector works
- [ ] "Showing X-Y of Z" updates
- [ ] Disabled states work correctly

#### Chart Tests

- [ ] Chart loads without errors
- [ ] Data points display correctly
- [ ] Tooltips show on hover
- [ ] Chart is responsive
- [ ] Colors match palette
- [ ] Legend displays
- [ ] Grid lines visible
- [ ] No console errors

#### Error Handling Tests

- [ ] Invalid user ID shows error
- [ ] Network failure shows message
- [ ] Empty data shows message
- [ ] Invalid dates show alert
- [ ] API errors display gracefully

### Test Data Scenarios

```
1. Small dataset (< 10 rows)
   - Pagination should not show

2. Medium dataset (10-100 rows)
   - Pagination should work normally

3. Large dataset (> 100 rows)
   - Test performance
   - All pagination features work

4. Edge cases:
   - 0 rows (empty data)
   - 1 row (single day)
   - Exactly 10 rows (boundary)
   - Negative balances
   - Zero hits
   - Large numbers (>1,000,000)
```

### User Acceptance Testing

**Scenario 1: View Last 30 Days**
```
1. User navigates to dashboard page
2. Sees default 30 days of data
3. Chart displays hits over time
4. Table shows daily breakdown
5. Summary shows total stats
```

**Scenario 2: Custom Date Range**
```
1. User clicks start date picker
2. Selects September 1, 2025
3. Clicks end date picker
4. Selects September 30, 2025
5. Clicks "Update"
6. Page reloads with September data
7. Chart and table update
8. URL shows date parameters
```

**Scenario 3: Browse Pages**
```
1. User sees table with pagination
2. Clicks page 2
3. Table shows rows 11-20
4. Clicks "Last"
5. Table shows last page
6. Changes rows per page to 25
7. Table reloads with 25 rows
```

## Browser Testing

### Supported Browsers

| Browser | Version | Status |
|---------|---------|--------|
| Chrome | Latest 2 | ✅ Primary |
| Firefox | Latest 2 | ✅ Primary |
| Safari | Latest 2 | ✅ Primary |
| Edge | Latest 2 | ✅ Primary |
| iOS Safari | 12+ | ✅ Mobile |
| Chrome Android | Latest | ✅ Mobile |

### Test Matrix

**Desktop:**
- Windows 10/11 - Chrome, Firefox, Edge
- macOS - Chrome, Firefox, Safari
- Linux - Chrome, Firefox

**Mobile:**
- iOS 14+ - Safari
- Android 10+ - Chrome

**Tablet:**
- iPad - Safari
- Android Tablet - Chrome

### Visual Testing Checklist

- [ ] Layout renders correctly
- [ ] No horizontal scrolling
- [ ] Colors match design
- [ ] Fonts load properly
- [ ] Icons/symbols display
- [ ] Spacing is consistent
- [ ] Hover states work
- [ ] Transitions smooth
- [ ] Responsive breakpoints work
- [ ] Print stylesheet works

### Console Errors

Check browser console (F12) for:

```javascript
// No errors like:
- Uncaught TypeError
- Uncaught ReferenceError
- Failed to load resource
- CORS errors

// Warnings are okay:
- DevTools warnings (usually harmless)
```

## Performance Testing

### Metrics to Monitor

**Page Load:**
- Initial HTML: < 1s
- Total page load: < 3s
- JavaScript execution: < 500ms

**Interaction:**
- Date picker update: < 100ms
- Pagination: < 50ms
- Chart render: < 500ms

### Tools

**Browser DevTools:**
```
1. Open DevTools (F12)
2. Go to Performance tab
3. Record page load
4. Analyze timeline
```

**Lighthouse:**
```
1. Open DevTools
2. Go to Lighthouse tab
3. Generate report
4. Target scores:
   - Performance: > 90
   - Accessibility: > 95
   - Best Practices: > 90
```

**GTmetrix:**
```
1. Visit gtmetrix.com
2. Enter page URL
3. Analyze results
4. Target: Grade A
```

### Load Testing

**Test with large datasets:**

```php
// Generate test data
$activities = [];
for ($i = 0; $i < 365; $i++) {
    $activities[] = [
        'date' => date('Y-m-d', strtotime("-{$i} days")),
        'totalHits' => rand(1000, 10000),
        'hitCost' => rand(10, 100) / 100,
        'balance' => rand(5000, 10000) / 100
    ];
}

// Render with test data
// Measure:
// - Initial render time
// - Pagination performance
// - Chart render time
// - Memory usage
```

**Expected Performance:**
- 365 rows: < 2s initial load
- Pagination: < 100ms
- Chart: < 1s
- Memory: < 50MB

## Continuous Integration

### GitHub Actions Example

**File:** `.github/workflows/test.yml`

```yaml
name: Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main ]

jobs:
  test:
    runs-on: ubuntu-latest

    strategy:
      matrix:
        php-version: [7.4, 8.0, 8.1]

    steps:
    - uses: actions/checkout@v2

    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: ${{ matrix.php-version }}
        extensions: mbstring, curl, json

    - name: Install dependencies
      run: composer install --prefer-dist --no-progress

    - name: Run tests
      run: ./vendor/bin/phpunit --coverage-clover coverage.xml

    - name: Upload coverage
      uses: codecov/codecov-action@v2
      with:
        files: ./coverage.xml
```

### Pre-Commit Hook

**File:** `.git/hooks/pre-commit`

```bash
#!/bin/bash

echo "Running tests..."

# Run PHPUnit
./vendor/bin/phpunit

if [ $? -ne 0 ]; then
    echo "Tests failed. Commit aborted."
    exit 1
fi

echo "Tests passed!"
exit 0
```

Make executable:
```bash
chmod +x .git/hooks/pre-commit
```

---

For more information:
- [API Client Documentation](./API_CLIENT.md)
- [Architecture Overview](./ARCHITECTURE.md)
- [Troubleshooting](./TROUBLESHOOTING.md)
