<?php
/**
 * Plugin Name: User Activity Dashboard
 * Plugin URI: https://example.com/user-activity-dashboard
 * Description: Displays user activity data with charts and tables using the User Activity API
 * Version: 1.0.0
 * Author: TP Bloomland
 * Author URI: https://example.com
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: user-activity-dashboard
 * Requires at least: 5.0
 * Requires PHP: 7.4
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Plugin constants
define('UAD_VERSION', '1.0.0');
define('UAD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UAD_PLUGIN_URL', plugin_dir_url(__FILE__));
define('UAD_PLUGIN_FILE', __FILE__);

// Autoload composer dependencies
require_once __DIR__ . '/../vendor/autoload.php';

// Include required files
require_once UAD_PLUGIN_DIR . 'includes/class-uad-core.php';
require_once UAD_PLUGIN_DIR . 'includes/class-uad-shortcode.php';

/**
 * Main plugin initialization
 */
function uad_init() {
    // Initialize core functionality
    UAD_Core::get_instance();
    UAD_Shortcode::get_instance();
}
add_action('plugins_loaded', 'uad_init');

/**
 * Plugin activation hook
 */
function uad_activate() {
    // Check PHP version
    if (version_compare(PHP_VERSION, '7.4', '<')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('This plugin requires PHP 7.4 or higher.', 'user-activity-dashboard'));
    }

    // Check if vendor directory exists
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        deactivate_plugins(plugin_basename(__FILE__));
        wp_die(__('Please run "composer install" in the dashboard-usage directory before activating this plugin.', 'user-activity-dashboard'));
    }
}
register_activation_hook(__FILE__, 'uad_activate');

/**
 * Plugin deactivation hook
 */
function uad_deactivate() {
    // Cleanup if needed
}
register_deactivation_hook(__FILE__, 'uad_deactivate');
