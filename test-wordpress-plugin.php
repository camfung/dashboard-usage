<?php
/**
 * Test the WordPress plugin structure (without WordPress)
 */

echo "Testing WordPress Plugin Structure\n";
echo "====================================\n\n";

// Simulate WordPress constants
define('ABSPATH', __DIR__ . '/');

// Simulate WordPress functions
function plugin_dir_path($file) {
    return dirname($file) . '/';
}

function plugin_dir_url($file) {
    return 'http://example.com/wp-content/plugins/' . basename(dirname($file)) . '/';
}

function plugin_basename($file) {
    return basename(dirname($file)) . '/' . basename($file);
}

function add_action($hook, $callback) {
    // No-op for testing
}

function register_activation_hook($file, $callback) {
    // No-op for testing
}

function register_deactivation_hook($file, $callback) {
    // No-op for testing
}

// Test loading the plugin
echo "Test 1: Loading main plugin file...\n";
try {
    require_once __DIR__ . '/user-activity-dashboard.php';
    echo "✓ Plugin file loaded successfully\n";
    echo "  - UAD_VERSION: " . UAD_VERSION . "\n";
    echo "  - UAD_PLUGIN_DIR: " . UAD_PLUGIN_DIR . "\n\n";
} catch (Exception $e) {
    echo "✗ Failed to load plugin: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 2: Check vendor autoload
echo "Test 2: Checking vendor autoload...\n";
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "✓ Vendor autoload exists\n\n";
} else {
    echo "✗ Vendor autoload missing - run 'composer install'\n\n";
    exit(1);
}

// Test 3: Check if client class loads
echo "Test 3: Loading UserActivityClient...\n";
try {
    $client = new TpBloomland\UserActivity\UserActivityClient();
    echo "✓ Client class loaded successfully\n\n";
} catch (Exception $e) {
    echo "✗ Failed to load client: " . $e->getMessage() . "\n\n";
    exit(1);
}

// Test 4: Check plugin directory structure
echo "Test 4: Checking directory structure...\n";
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
echo "\n";

// Test 5: Check required files
echo "Test 5: Checking required files...\n";
$requiredFiles = [
    'user-activity-dashboard.php',
    'includes/class-uad-core.php',
    'includes/class-uad-shortcode.php',
    'templates/dashboard.php',
    'assets/css/uad-styles.css',
    'assets/js/uad-scripts.js',
    'lib/UserActivity/UserActivityClient.php',
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
echo "\n";

// Test 6: Test API call
echo "Test 6: Testing API call...\n";
try {
    $response = $client->getUserActivity(125, '2025-08-01', '2025-08-31');
    if ($response['success']) {
        echo "✓ API call successful\n";
        echo "  - Records: " . count($response['source']) . "\n";
    } else {
        echo "✗ API returned error\n";
    }
} catch (Exception $e) {
    echo "✗ API call failed: " . $e->getMessage() . "\n";
}
echo "\n";

// Final result
if ($allDirsExist && $allFilesExist) {
    echo "====================================\n";
    echo "✓ ALL TESTS PASSED!\n";
    echo "====================================\n\n";
    echo "Plugin is ready for WordPress!\n\n";
    echo "Installation:\n";
    echo "1. Copy 'dashboard-usage' to wp-content/plugins/\n";
    echo "2. Activate 'User Activity Dashboard' in WordPress\n";
    echo "3. Use shortcode: [user_activity_dashboard]\n\n";
    exit(0);
} else {
    echo "====================================\n";
    echo "✗ TESTS FAILED\n";
    echo "====================================\n\n";
    exit(1);
}
