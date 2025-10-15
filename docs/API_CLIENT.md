# API Client Documentation

Complete documentation for the User Activity API client, including usage examples, error handling, and testing.

## Table of Contents

1. [Overview](#overview)
2. [Installation](#installation)
3. [API Client Reference](#api-client-reference)
4. [Usage Examples](#usage-examples)
5. [Error Handling](#error-handling)
6. [Configuration](#configuration)
7. [Testing](#testing)
8. [Advanced Topics](#advanced-topics)

## Overview

The User Activity API client is a PHP library that communicates with the external User Activity Summary API. It's built using Guzzle HTTP client and follows PSR-4 autoloading standards.

### Features

- Simple, intuitive API
- Automatic error handling
- Type-safe responses
- Comprehensive test coverage
- Standalone or WordPress integration

### Namespace

```php
namespace TpBloomland\UserActivity;
```

## Installation

### Via Composer

```bash
composer require tpbloomland/user-activity
```

### Manual Installation

```php
require_once 'path/to/lib/UserActivity/UserActivityClient.php';
require_once 'path/to/lib/UserActivity/Exceptions/ApiException.php';

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;
```

### WordPress Plugin (Included)

```php
// Already autoloaded via Composer in the plugin
use TpBloomland\UserActivity\UserActivityClient;
```

## API Client Reference

### Class: UserActivityClient

**File:** `lib/UserActivity/UserActivityClient.php`

**Constructor:**

```php
public function __construct(?string $apiKey = null)
```

**Parameters:**
- `$apiKey` (string|null) - Optional API key for authentication (not currently used)

**Example:**
```php
$client = new UserActivityClient();
```

---

### Method: getUserActivity

Fetch user activity data for a specific date range.

**Signature:**
```php
public function getUserActivity(
    int $userId,
    ?string $startDate = null,
    ?string $endDate = null
): array
```

**Parameters:**
- `$userId` (int) - The user ID to fetch activity for
- `$startDate` (string|null) - Start date in `Y-m-d` format (default: 30 days ago)
- `$endDate` (string|null) - End date in `Y-m-d` format (default: today)

**Returns:**
```php
[
    'success' => true,
    'source' => [
        [
            'date' => '2025-10-01',
            'totalHits' => 1500,
            'hitCost' => 0.15,
            'balance' => 98.85
        ],
        // ... more daily records
    ]
]
```

**Throws:**
- `ApiException` - If the API request fails

**Example:**
```php
$client = new UserActivityClient();

try {
    $data = $client->getUserActivity(
        125,
        '2025-09-01',
        '2025-09-30'
    );

    echo "Total days: " . count($data['source']) . "\n";

    foreach ($data['source'] as $day) {
        echo "{$day['date']}: {$day['totalHits']} hits\n";
    }
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

### Method: describeTable

Get the schema description for a DynamoDB table.

**Signature:**
```php
public function describeTable(string $tableName): array
```

**Parameters:**
- `$tableName` (string) - The name of the table to describe

**Returns:**
```php
[
    'success' => true,
    'source' => [
        'TableName' => 'UserActivity',
        'KeySchema' => [...],
        'AttributeDefinitions' => [...],
        // ... table metadata
    ]
]
```

**Throws:**
- `ApiException` - If the API request fails

**Example:**
```php
$client = new UserActivityClient();

try {
    $schema = $client->describeTable('UserActivity');
    print_r($schema['source']);
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

### Method: getSampleData

Fetch sample records from a table for testing/debugging.

**Signature:**
```php
public function getSampleData(
    string $tableName,
    int $limit = 5
): array
```

**Parameters:**
- `$tableName` (string) - The table name to fetch samples from
- `$limit` (int) - Maximum number of records to return (default: 5)

**Returns:**
```php
[
    'success' => true,
    'source' => [
        ['userId' => 125, 'date' => '2025-10-01', ...],
        ['userId' => 125, 'date' => '2025-10-02', ...],
        // ... up to $limit records
    ]
]
```

**Throws:**
- `ApiException` - If the API request fails

**Example:**
```php
$client = new UserActivityClient();

try {
    $samples = $client->getSampleData('UserActivity', 10);

    foreach ($samples['source'] as $record) {
        echo "User {$record['userId']} on {$record['date']}\n";
    }
} catch (ApiException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

### Class: ApiException

**File:** `lib/UserActivity/Exceptions/ApiException.php`

**Extends:** `\Exception`

**Usage:**
```php
try {
    $data = $client->getUserActivity(125);
} catch (ApiException $e) {
    echo "Error code: " . $e->getCode() . "\n";
    echo "Message: " . $e->getMessage() . "\n";
    echo "Previous exception: " . $e->getPrevious() . "\n";
}
```

## Usage Examples

### Example 1: Basic Usage

```php
<?php
require_once 'vendor/autoload.php';

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

$client = new UserActivityClient();

try {
    // Get last 30 days of activity
    $result = $client->getUserActivity(125);

    if ($result['success']) {
        $activities = $result['source'];

        echo "Total records: " . count($activities) . "\n";

        // Calculate totals
        $totalHits = 0;
        $totalCost = 0;

        foreach ($activities as $activity) {
            $totalHits += $activity['totalHits'];
            $totalCost += abs($activity['hitCost']);
        }

        echo "Total hits: " . number_format($totalHits) . "\n";
        echo "Total cost: $" . number_format($totalCost, 2) . "\n";
    }
} catch (ApiException $e) {
    echo "API Error: " . $e->getMessage() . "\n";
}
```

### Example 2: Custom Date Range

```php
<?php
$client = new UserActivityClient();

// Get activity for a specific month
$startDate = '2025-09-01';
$endDate = '2025-09-30';

try {
    $result = $client->getUserActivity(125, $startDate, $endDate);

    echo "September 2025 Activity Report\n";
    echo "================================\n\n";

    foreach ($result['source'] as $day) {
        printf(
            "%s: %d hits, $%.2f cost, $%.2f balance\n",
            $day['date'],
            $day['totalHits'],
            abs($day['hitCost']),
            $day['balance']
        );
    }
} catch (ApiException $e) {
    die("Error: " . $e->getMessage() . "\n");
}
```

### Example 3: WordPress Integration

```php
<?php
// In a WordPress plugin or theme

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

function display_user_activity() {
    $client = new UserActivityClient();
    $user_id = get_current_user_id();

    if (!$user_id) {
        return '<p>Please log in to view your activity.</p>';
    }

    try {
        $data = $client->getUserActivity($user_id);

        ob_start();
        ?>
        <div class="activity-summary">
            <h3>Your Activity</h3>
            <p>Total days tracked: <?php echo count($data['source']); ?></p>
            <ul>
                <?php foreach (array_slice($data['source'], 0, 5) as $day): ?>
                    <li>
                        <?php echo esc_html($day['date']); ?>:
                        <?php echo number_format($day['totalHits']); ?> hits
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();

    } catch (ApiException $e) {
        error_log('User Activity API Error: ' . $e->getMessage());
        return '<p>Unable to load activity data.</p>';
    }
}

// Register shortcode
add_shortcode('my_activity', 'display_user_activity');
```

### Example 4: Error Handling with Retry

```php
<?php
function fetchActivityWithRetry($userId, $maxRetries = 3) {
    $client = new UserActivityClient();
    $attempt = 0;

    while ($attempt < $maxRetries) {
        try {
            $data = $client->getUserActivity($userId);
            return $data;

        } catch (ApiException $e) {
            $attempt++;

            if ($attempt >= $maxRetries) {
                throw $e; // Re-throw on final attempt
            }

            // Wait before retry (exponential backoff)
            sleep(pow(2, $attempt));
        }
    }
}

try {
    $data = fetchActivityWithRetry(125);
    echo "Successfully fetched data after retries\n";
} catch (ApiException $e) {
    echo "Failed after all retry attempts: " . $e->getMessage() . "\n";
}
```

### Example 5: Data Export

```php
<?php
function exportActivityToCSV($userId, $filename = 'activity.csv') {
    $client = new UserActivityClient();

    try {
        $result = $client->getUserActivity($userId);

        $fp = fopen($filename, 'w');

        // Write header
        fputcsv($fp, ['Date', 'Total Hits', 'Cost', 'Balance']);

        // Write data
        foreach ($result['source'] as $row) {
            fputcsv($fp, [
                $row['date'],
                $row['totalHits'],
                $row['hitCost'],
                $row['balance']
            ]);
        }

        fclose($fp);

        echo "Exported to {$filename}\n";

    } catch (ApiException $e) {
        die("Export failed: " . $e->getMessage() . "\n");
    }
}

exportActivityToCSV(125, 'user_125_activity.csv');
```

## Error Handling

### Common Exceptions

#### 1. Network Errors

```php
try {
    $data = $client->getUserActivity(125);
} catch (ApiException $e) {
    if (strpos($e->getMessage(), 'Connection') !== false) {
        echo "Network connection error. Please check your internet.";
    }
}
```

#### 2. Invalid Response

```php
try {
    $data = $client->getUserActivity(125);
} catch (ApiException $e) {
    if (strpos($e->getMessage(), 'Invalid JSON') !== false) {
        echo "API returned invalid data. Please try again later.";
    }
}
```

#### 3. HTTP Errors

```php
try {
    $data = $client->getUserActivity(999999); // Invalid user
} catch (ApiException $e) {
    $code = $e->getCode();

    switch ($code) {
        case 404:
            echo "User not found";
            break;
        case 500:
            echo "Server error. Please try again later.";
            break;
        default:
            echo "Unexpected error: " . $e->getMessage();
    }
}
```

### Logging Errors

#### PHP Error Log

```php
try {
    $data = $client->getUserActivity(125);
} catch (ApiException $e) {
    error_log(sprintf(
        'API Error [%d]: %s in %s:%d',
        $e->getCode(),
        $e->getMessage(),
        $e->getFile(),
        $e->getLine()
    ));

    // Show user-friendly message
    echo "We're experiencing technical difficulties. Please try again later.";
}
```

#### WordPress Debug Log

```php
try {
    $data = $client->getUserActivity(125);
} catch (ApiException $e) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('User Activity API Error: ' . $e->getMessage());
    }

    // Return WP_Error for WordPress
    return new WP_Error('api_error', 'Unable to fetch activity data');
}
```

## Configuration

### API Endpoint Configuration

The API base URL is hardcoded in the client. To change it:

**File:** `lib/UserActivity/UserActivityClient.php`

```php
public function __construct(?string $apiKey = null) {
    $this->apiKey = $apiKey;
    $this->client = new Client([
        'base_uri' => 'https://your-api-domain.com/dev/',  // Change this
        'timeout' => 30,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ]
    ]);
}
```

### Timeout Configuration

Default timeout is 30 seconds. To change:

```php
'timeout' => 60,  // 60 seconds
```

### Adding Authentication

If the API requires authentication:

```php
public function __construct(?string $apiKey = null) {
    $this->apiKey = $apiKey;
    $this->client = new Client([
        'base_uri' => 'https://api.example.com/dev/',
        'timeout' => 30,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'Authorization' => 'Bearer ' . $apiKey  // Add auth header
        ]
    ]);
}
```

Usage:
```php
$client = new UserActivityClient('your-api-key-here');
```

## Testing

### Running Tests

```bash
# Run all API client tests
./vendor/bin/phpunit tests/UserActivityClientTest.php

# Run specific test
./vendor/bin/phpunit --filter test_getUserActivity_returns_valid_data

# Run with verbose output
./vendor/bin/phpunit --verbose tests/UserActivityClientTest.php
```

### Test Coverage

The test suite includes 17 test cases covering:

1. **Success Scenarios**
   - Valid user activity retrieval
   - Custom date range queries
   - Table schema retrieval
   - Sample data fetching

2. **Data Validation**
   - Response structure validation
   - Date format validation
   - Numeric field validation
   - Required field presence

3. **Error Scenarios**
   - Invalid user IDs
   - Malformed date formats
   - Network failures (requires mock)

### Writing Custom Tests

```php
<?php
use PHPUnit\Framework\TestCase;
use TpBloomland\UserActivity\UserActivityClient;

class CustomUserActivityTest extends TestCase
{
    private $client;

    protected function setUp(): void
    {
        $this->client = new UserActivityClient();
    }

    public function test_custom_scenario()
    {
        // Arrange
        $userId = 125;
        $startDate = '2025-09-01';
        $endDate = '2025-09-30';

        // Act
        $result = $this->client->getUserActivity($userId, $startDate, $endDate);

        // Assert
        $this->assertTrue($result['success']);
        $this->assertCount(30, $result['source']); // September has 30 days
    }
}
```

## Advanced Topics

### Extending the Client

```php
<?php
namespace App\Services;

use TpBloomland\UserActivity\UserActivityClient as BaseClient;

class CachedActivityClient extends BaseClient
{
    private $cache;

    public function __construct($cache, ?string $apiKey = null)
    {
        parent::__construct($apiKey);
        $this->cache = $cache;
    }

    public function getUserActivity(
        int $userId,
        ?string $startDate = null,
        ?string $endDate = null
    ): array {
        $cacheKey = "activity_{$userId}_{$startDate}_{$endDate}";

        // Check cache
        if ($cached = $this->cache->get($cacheKey)) {
            return $cached;
        }

        // Fetch from API
        $data = parent::getUserActivity($userId, $startDate, $endDate);

        // Store in cache
        $this->cache->set($cacheKey, $data, 900); // 15 minutes

        return $data;
    }
}
```

### Async Requests (Future Enhancement)

```php
<?php
// Using GuzzleHttp promises
use GuzzleHttp\Promise;

class AsyncActivityClient extends UserActivityClient
{
    public function getUserActivityAsync(int $userId): Promise\PromiseInterface
    {
        $endpoint = "user-activity-summary/{$userId}";

        return $this->client->getAsync($endpoint)->then(
            function ($response) {
                $body = $response->getBody()->getContents();
                return json_decode($body, true);
            }
        );
    }
}

// Usage
$promise1 = $client->getUserActivityAsync(125);
$promise2 = $client->getUserActivityAsync(126);

$results = Promise\unwrap([$promise1, $promise2]);
```

### Rate Limiting

```php
<?php
class RateLimitedClient extends UserActivityClient
{
    private $lastRequest = 0;
    private $minInterval = 1; // 1 second between requests

    protected function request(string $method, string $endpoint, array $options = []): array
    {
        // Enforce rate limit
        $now = microtime(true);
        $elapsed = $now - $this->lastRequest;

        if ($elapsed < $this->minInterval) {
            usleep(($this->minInterval - $elapsed) * 1000000);
        }

        $this->lastRequest = microtime(true);

        return parent::request($method, $endpoint, $options);
    }
}
```

---

For more information:
- [Architecture Overview](./ARCHITECTURE.md)
- [Testing Guide](./TESTING.md)
- [Troubleshooting](./TROUBLESHOOTING.md)
