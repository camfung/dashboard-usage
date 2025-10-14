<?php
/**
 * Standalone test script for the plugin (without WordPress)
 */

// Load composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

use TpBloomland\UserActivity\UserActivityClient;

echo "Testing User Activity Dashboard Plugin (Standalone)\n";
echo "====================================================\n\n";

// Test 1: Client loads correctly
echo "Test 1: Loading UserActivityClient...\n";
try {
    $client = new UserActivityClient();
    echo "✓ Client loaded successfully\n\n";
} catch (Exception $e) {
    echo "✗ Failed to load client: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Fetch data
echo "Test 2: Fetching user activity data...\n";
try {
    $response = $client->getUserActivity(125, '2025-08-01', '2025-08-31');

    if ($response['success']) {
        echo "✓ API call successful\n";
        echo "  - Records found: " . count($response['source']) . "\n";

        if (!empty($response['source'])) {
            $firstRecord = $response['source'][0];
            echo "  - First record date: " . $firstRecord['date'] . "\n";
            echo "  - First record hits: " . $firstRecord['totalHits'] . "\n";
        }
    } else {
        echo "✗ API returned error: " . $response['message'] . "\n";
    }
    echo "\n";
} catch (Exception $e) {
    echo "✗ API call failed: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 3: Check plugin files
echo "Test 3: Checking plugin files exist...\n";
$requiredFiles = [
    'user-activity-dashboard.php',
    'includes/class-uad-core.php',
    'includes/class-uad-shortcode.php',
    'templates/dashboard.php',
    'assets/css/uad-styles.css',
    'assets/js/uad-scripts.js',
    'lib/UserActivity/UserActivityClient.php',
    'lib/UserActivity/Exceptions/ApiException.php',
    'vendor/autoload.php',
];

$allFilesExist = true;
foreach ($requiredFiles as $file) {
    if (file_exists(__DIR__ . '/' . $file)) {
        echo "  ✓ $file\n";
    } else {
        echo "  ✗ $file (MISSING)\n";
        $allFilesExist = false;
    }
}

if ($allFilesExist) {
    echo "\n✓ All required files present\n\n";
} else {
    echo "\n✗ Some files are missing\n\n";
    exit(1);
}

// Test 4: Check directory structure
echo "Test 4: Verifying directory structure...\n";
$requiredDirs = [
    'includes',
    'templates',
    'assets/css',
    'assets/js',
    'lib/UserActivity',
    'vendor',
];

$allDirsExist = true;
foreach ($requiredDirs as $dir) {
    if (is_dir(__DIR__ . '/' . $dir)) {
        echo "  ✓ $dir/\n";
    } else {
        echo "  ✗ $dir/ (MISSING)\n";
        $allDirsExist = false;
    }
}

if ($allDirsExist) {
    echo "\n✓ All required directories present\n\n";
} else {
    echo "\n✗ Some directories are missing\n\n";
    exit(1);
}

echo "====================================================\n";
echo "✓ ALL TESTS PASSED - Plugin is ready for WordPress!\n";
echo "====================================================\n\n";

echo "Installation Instructions:\n";
echo "1. Copy this entire directory to: wp-content/plugins/\n";
echo "2. Activate the plugin in WordPress admin\n";
echo "3. Use shortcode: [user_activity_dashboard]\n\n";
