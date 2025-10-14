<?php
/**
 * Core plugin functionality
 */

if (!defined('ABSPATH')) {
    exit;
}

class UAD_Core {

    private static $instance = null;

    /**
     * Get singleton instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Constructor
     */
    private function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_scripts']);
    }

    /**
     * Enqueue CSS and JavaScript
     */
    public function enqueue_scripts() {
        // Enqueue Chart.js from CDN
        wp_enqueue_script(
            'chartjs',
            'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
            [],
            '4.4.0',
            true
        );

        // Enqueue plugin CSS
        wp_enqueue_style(
            'uad-styles',
            UAD_PLUGIN_URL . 'assets/css/uad-styles.css',
            [],
            UAD_VERSION
        );

        // Enqueue plugin JavaScript
        wp_enqueue_script(
            'uad-scripts',
            UAD_PLUGIN_URL . 'assets/js/uad-scripts.js',
            ['chartjs'],
            UAD_VERSION,
            true
        );
    }

    /**
     * Get default user ID (can be filtered)
     */
    public function get_default_user_id() {
        return apply_filters('uad_default_user_id', 125);
    }

    /**
     * Get default days range
     */
    public function get_default_days() {
        return apply_filters('uad_default_days', 30);
    }
}
