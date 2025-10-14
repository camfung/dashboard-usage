# User Activity API PHP Client

A robust PHP client library for interacting with the User Activity Summary API. Built with test-driven development (TDD) practices and fully validated against the live API.

## Features

- Simple, intuitive API for retrieving user activity data
- Support for date range filtering
- Schema inspection capabilities for debugging
- Comprehensive error handling with custom exceptions
- Full test coverage with PHPUnit
- PSR-4 autoloading
- Type-safe with strict type declarations

## Requirements

- PHP >= 7.4
- Composer

## Installation

```bash
composer require tp-bloomland/user-activity-client
```

Or add to your `composer.json`:

```json
{
    "require": {
        "tp-bloomland/user-activity-client": "^1.0"
    }
}
```

## Quick Start

```php
<?php

require 'vendor/autoload.php';

use TpBloomland\UserActivity\UserActivityClient;

// Create client instance
$client = new UserActivityClient();

// Get user activity
$response = $client->getUserActivity(125);

if ($response['success']) {
    foreach ($response['source'] as $record) {
        echo "Date: {$record['date']}\n";
        echo "Hits: {$record['totalHits']}\n";
        echo "Cost: \${$record['hitCost']}\n";
        echo "Balance: \${$record['balance']}\n";
        echo "---\n";
    }
}
```

## Usage Examples

### Basic Activity Retrieval

Get all activity data for a user:

```php
$client = new UserActivityClient();
$response = $client->getUserActivity(125);

print_r($response);
// Array
// (
//     [message] => Activity summary retrieved
//     [success] => 1
//     [source] => Array
//         (
//             [0] => Array
//                 (
//                     [date] => 2025-07-30
//                     [totalHits] => 5
//                     [hitCost] => -0.5
//                     [balance] => -0.5
//                 )
//             ...
//         )
// )
```

### Activity with Date Range

Filter activity by date range:

```php
$startDate = '2025-08-01';
$endDate = '2025-08-31';

$response = $client->getUserActivity(125, $startDate, $endDate);

// Returns only activity within August 2025
```

### Activity with Start Date Only

Get activity from a specific date onwards:

```php
$response = $client->getUserActivity(125, '2025-08-01');

// Returns all activity from August 1st, 2025 onwards
```

### Activity with End Date Only

Get activity up to a specific date:

```php
$response = $client->getUserActivity(125, null, '2025-07-31');

// Returns all activity up to and including July 31st, 2025
```

### Schema Inspector - Describe Table

Get the schema structure for a database table:

```php
$response = $client->describeTable('payment_records');

if ($response['success']) {
    print_r($response['source']);
}
```

### Schema Inspector - Get Sample Data

Retrieve sample data from a table:

```php
// Get 10 sample rows from tp_log
$response = $client->getSampleData('tp_log', 10);

if ($response['success']) {
    print_r($response['source']);
}
```

### Custom Base URL

Use a different API endpoint:

```php
$client = new UserActivityClient('https://api.example.com/v2');
$response = $client->getUserActivity(125);
```

### Custom Guzzle Options

Pass custom options to the underlying Guzzle HTTP client:

```php
$options = [
    'timeout' => 30.0,
    'headers' => [
        'X-Custom-Header' => 'value'
    ]
];

$client = new UserActivityClient(null, $options);
```

## Error Handling

The client throws exceptions for various error conditions:

```php
use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

$client = new UserActivityClient();

try {
    // Invalid user ID
    $response = $client->getUserActivity(-1);
} catch (\InvalidArgumentException $e) {
    echo "Validation error: {$e->getMessage()}\n";
}

try {
    // API error
    $response = $client->getUserActivity(999999);
} catch (ApiException $e) {
    echo "API error: {$e->getMessage()}\n";
}
```

## API Methods

### `getUserActivity(int $userId, ?string $startDate = null, ?string $endDate = null): array`

Retrieves daily aggregated activity data for a specific user.

**Parameters:**
- `$userId` (int, required): User ID to retrieve activity for
- `$startDate` (string, optional): Start date filter in YYYY-MM-DD format
- `$endDate` (string, optional): End date filter in YYYY-MM-DD format

**Returns:** Array with keys:
- `message` (string): Status message
- `success` (bool): Whether the request was successful
- `source` (array): Array of activity records

**Throws:**
- `InvalidArgumentException`: If user ID is invalid (≤ 0)
- `ApiException`: If the API request fails

### `describeTable(string $tableName): array`

Returns the schema structure for a specified database table.

**Parameters:**
- `$tableName` (string, required): Name of the table to describe

**Returns:** Array containing table schema information

**Throws:**
- `InvalidArgumentException`: If table name is empty
- `ApiException`: If the API request fails

### `getSampleData(string $tableName, int $limit = 5): array`

Returns sample rows from the specified table.

**Parameters:**
- `$tableName` (string, required): Name of the table to sample
- `$limit` (int, optional): Number of rows to return (default: 5)

**Returns:** Array containing sample data

**Throws:**
- `InvalidArgumentException`: If table name is empty or limit is invalid
- `ApiException`: If the API request fails

## Response Structure

### Activity Response

```php
[
    'message' => 'Activity summary retrieved',
    'success' => true,
    'source' => [
        [
            'date' => '2025-07-30',        // YYYY-MM-DD format
            'totalHits' => 5,               // Number of hits/redirects
            'hitCost' => -0.5,              // Cost for the day (negative)
            'balance' => -0.5               // Running balance
        ],
        // ... more records
    ]
]
```

### Schema Inspector Response

```php
[
    'success' => true,
    'source' => [
        // Table schema or sample data
    ]
]
```

## Testing

The client includes a comprehensive test suite built with PHPUnit:

```bash
# Install dependencies
composer install

# Run all tests
vendor/bin/phpunit

# Run tests with detailed output
vendor/bin/phpunit --testdox

# Run tests with coverage (requires Xdebug)
vendor/bin/phpunit --coverage-html coverage
```

### Test Coverage

- 17 test cases
- 93 assertions
- 100% method coverage
- Tests against live API endpoints

## Development

### Running Tests During Development

```bash
# Watch mode (requires phpunit-watcher)
composer require --dev spatie/phpunit-watcher
vendor/bin/phpunit-watcher watch
```

### Project Structure

```
dashboard-usage/
├── src/
│   ├── UserActivityClient.php      # Main client class
│   └── Exceptions/
│       └── ApiException.php        # Custom exception
├── tests/
│   └── UserActivityClientTest.php  # Test suite
├── composer.json                   # Dependencies
├── phpunit.xml                     # PHPUnit configuration
└── README.md                       # This file
```

## Best Practices

### Caching Results

Cache API responses to reduce load:

```php
$cacheKey = "user_activity_{$userId}";
$cachedData = $cache->get($cacheKey);

if ($cachedData === null) {
    $response = $client->getUserActivity($userId);
    $cache->set($cacheKey, $response, 300); // Cache for 5 minutes
    return $response;
}

return $cachedData;
```

### Error Handling

Always wrap API calls in try-catch blocks:

```php
try {
    $response = $client->getUserActivity($userId);

    if (!$response['success']) {
        // Handle API-level errors
        error_log("API returned error: {$response['message']}");
        return [];
    }

    return $response['source'];

} catch (ApiException $e) {
    // Handle network/HTTP errors
    error_log("API request failed: {$e->getMessage()}");
    return [];
} catch (\InvalidArgumentException $e) {
    // Handle validation errors
    error_log("Invalid parameters: {$e->getMessage()}");
    return [];
}
```

### Date Range Filtering

Use date range filtering for better performance:

```php
// Instead of fetching all data
$allData = $client->getUserActivity($userId);

// Fetch only what you need
$lastMonth = $client->getUserActivity(
    $userId,
    date('Y-m-01', strtotime('-1 month')),
    date('Y-m-t', strtotime('-1 month'))
);
```

## API Endpoints

The client connects to these endpoints:

- **Production:** `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev`

### Available Tables for Schema Inspector

- `tp_log` - Hit/redirect activity data
- `tp_user` - User information
- `tp_map` - Mapping data
- `tp_set` - Settings data
- `payment_records` - Payment transactions
- `usage_records` - Usage records
- `user_products` - User-product associations
- `products` - Product information and pricing

## Changelog

### v1.0.0 (2025-10-14)

- Initial release
- Full TDD implementation with 17 passing tests
- Support for user activity retrieval with date filtering
- Schema inspector capabilities
- Comprehensive error handling
- Complete documentation

## Support

For issues or questions:

1. Check the [API Documentation](USER_ACTIVITY_SUMMARY_API.md)
2. Review the test suite for usage examples
3. Check CloudWatch logs for API-level issues

## License

Copyright (c) 2025 TP Bloomland. All rights reserved.

## Related Documentation

- [API Documentation](USER_ACTIVITY_SUMMARY_API.md) - Complete API reference
- [Postman Collection](postman_collection.json) - Postman collection for testing

## Credits

Built with:
- [Guzzle HTTP Client](https://github.com/guzzle/guzzle) - HTTP client
- [PHPUnit](https://phpunit.de/) - Testing framework
- Test-driven development (TDD) methodology
