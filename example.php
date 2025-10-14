<?php
/**
 * Example usage of the User Activity API Client
 *
 * This file demonstrates how to use the UserActivityClient to interact
 * with the User Activity Summary API.
 */

require 'vendor/autoload.php';

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

// Create a new client instance
$client = new UserActivityClient();

echo "=== User Activity API Client Examples ===\n\n";

// Example 1: Get all activity for a user
echo "1. Getting all activity for user 125:\n";
echo "-----------------------------------\n";

try {
    $response = $client->getUserActivity(125);

    if ($response['success']) {
        echo "Success! Found " . count($response['source']) . " activity records\n\n";

        // Display first 3 records
        $recordsToShow = array_slice($response['source'], 0, 3);
        foreach ($recordsToShow as $record) {
            echo "Date: {$record['date']}\n";
            echo "  Total Hits: {$record['totalHits']}\n";
            echo "  Hit Cost: $" . number_format($record['hitCost'], 2) . "\n";
            echo "  Balance: $" . number_format($record['balance'], 2) . "\n";
            echo "\n";
        }

        if (count($response['source']) > 3) {
            echo "... and " . (count($response['source']) - 3) . " more records\n";
        }
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 2: Get activity with date range
echo "2. Getting activity for August 2025:\n";
echo "-----------------------------------\n";

try {
    $response = $client->getUserActivity(125, '2025-08-01', '2025-08-31');

    if ($response['success']) {
        $totalHits = 0;
        foreach ($response['source'] as $record) {
            $totalHits += $record['totalHits'];
        }

        echo "Found " . count($response['source']) . " days of activity\n";
        echo "Total hits in August: " . number_format($totalHits) . "\n";

        if (!empty($response['source'])) {
            $lastRecord = end($response['source']);
            echo "Final balance: $" . number_format($lastRecord['balance'], 2) . "\n";
        }
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 3: Get activity from a start date
echo "3. Getting activity from August 1st onwards:\n";
echo "-------------------------------------------\n";

try {
    $response = $client->getUserActivity(125, '2025-08-01');

    if ($response['success']) {
        echo "Found " . count($response['source']) . " days of activity from Aug 1st onwards\n";
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 4: User with no activity
echo "4. Getting activity for user with no data:\n";
echo "------------------------------------------\n";

try {
    $response = $client->getUserActivity(1);

    if ($response['success']) {
        if (empty($response['source'])) {
            echo "User has no activity data (empty result set)\n";
        } else {
            echo "Found " . count($response['source']) . " records\n";
        }
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 5: Error handling - invalid user ID
echo "5. Demonstrating error handling:\n";
echo "--------------------------------\n";

try {
    $response = $client->getUserActivity(-1);
} catch (\InvalidArgumentException $e) {
    echo "Caught validation error: {$e->getMessage()}\n";
} catch (ApiException $e) {
    echo "Caught API error: {$e->getMessage()}\n";
}

echo "\n";

// Example 6: Schema inspector - describe table
echo "6. Using Schema Inspector to describe a table:\n";
echo "---------------------------------------------\n";

try {
    $response = $client->describeTable('payment_records');

    if ($response['success']) {
        echo "Successfully retrieved schema for payment_records table\n";
        echo "Columns found: " . count($response['source']) . "\n";

        // Show first 3 columns
        $columnsToShow = array_slice($response['source'], 0, 3);
        foreach ($columnsToShow as $column) {
            if (isset($column['Field'])) {
                echo "  - {$column['Field']} ({$column['Type']})\n";
            }
        }
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 7: Schema inspector - get sample data
echo "7. Getting sample data from tp_log table:\n";
echo "----------------------------------------\n";

try {
    $response = $client->getSampleData('tp_log', 3);

    if ($response['success']) {
        echo "Retrieved " . count($response['source']) . " sample rows\n";

        if (!empty($response['source'])) {
            $firstRow = $response['source'][0];
            echo "Sample row fields: " . implode(', ', array_keys($firstRow)) . "\n";
        }
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n";

// Example 8: Calculate statistics
echo "8. Calculating activity statistics:\n";
echo "----------------------------------\n";

try {
    $response = $client->getUserActivity(125);

    if ($response['success'] && !empty($response['source'])) {
        $totalHits = 0;
        $totalCost = 0;
        $daysWithActivity = count($response['source']);

        foreach ($response['source'] as $record) {
            $totalHits += $record['totalHits'];
            $totalCost += abs($record['hitCost']);
        }

        $avgHitsPerDay = $totalHits / $daysWithActivity;

        echo "Total days with activity: {$daysWithActivity}\n";
        echo "Total hits: " . number_format($totalHits) . "\n";
        echo "Total cost: $" . number_format($totalCost, 2) . "\n";
        echo "Average hits per day: " . number_format($avgHitsPerDay, 2) . "\n";

        $finalBalance = end($response['source'])['balance'];
        echo "Current balance: $" . number_format($finalBalance, 2) . "\n";
    }
} catch (ApiException $e) {
    echo "Error: {$e->getMessage()}\n";
}

echo "\n=== Examples complete ===\n";
