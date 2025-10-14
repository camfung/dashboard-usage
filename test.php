<?php
/**
 * Test script to display August 2025 activity log
 */

require 'vendor/autoload.php';

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

// Configuration
$userId = 125;
$startDate = '2025-08-01';
$endDate = '2025-08-31';

// Create client
$client = new UserActivityClient();

echo "\n";
echo "========================================\n";
echo "  Activity Log for August 2025\n";
echo "  User ID: {$userId}\n";
echo "========================================\n\n";

try {
    // Fetch August activity
    $response = $client->getUserActivity($userId, $startDate, $endDate);

    if (!$response['success']) {
        echo "Error: {$response['message']}\n";
        exit(1);
    }

    $activities = $response['source'];

    if (empty($activities)) {
        echo "No activity found for August 2025.\n\n";
        exit(0);
    }

    // Calculate totals
    $totalHits = 0;
    $totalCost = 0;

    foreach ($activities as $record) {
        $totalHits += $record['totalHits'];
        $totalCost += abs($record['hitCost']);
    }

    // Display summary
    echo "Summary:\n";
    echo "--------\n";
    echo "Total Days: " . count($activities) . "\n";
    echo "Total Hits: " . number_format($totalHits) . "\n";
    echo "Total Cost: $" . number_format($totalCost, 2) . "\n";
    echo "Avg Hits/Day: " . number_format($totalHits / count($activities), 2) . "\n";
    echo "\n";

    // Display detailed log
    echo "Daily Activity Log:\n";
    echo "-------------------\n";
    printf("%-12s | %10s | %12s | %12s\n", "Date", "Hits", "Cost", "Balance");
    echo str_repeat("-", 54) . "\n";

    foreach ($activities as $record) {
        printf(
            "%-12s | %10s | %12s | %12s\n",
            $record['date'],
            number_format($record['totalHits']),
            '$' . number_format($record['hitCost'], 2),
            '$' . number_format($record['balance'], 2)
        );
    }

    echo str_repeat("-", 54) . "\n";

    $finalBalance = end($activities)['balance'];
    echo "\nFinal Balance: $" . number_format($finalBalance, 2) . "\n";
    echo "\n";

} catch (ApiException $e) {
    echo "API Error: {$e->getMessage()}\n\n";
    exit(1);
} catch (\Exception $e) {
    echo "Error: {$e->getMessage()}\n\n";
    exit(1);
}
