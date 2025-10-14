<?php
/**
 * Shortcode handler for User Activity Dashboard
 */

if (!defined('ABSPATH')) {
    exit;
}

use TpBloomland\UserActivity\UserActivityClient;
use TpBloomland\UserActivity\Exceptions\ApiException;

class UAD_Shortcode {

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
        add_shortcode('user_activity_dashboard', [$this, 'render_dashboard']);
    }

    /**
     * Render the dashboard shortcode
     *
     * @param array $atts Shortcode attributes
     * @return string HTML output
     */
    public function render_dashboard($atts) {
        // Parse attributes
        $atts = shortcode_atts([
            'user_id' => UAD_Core::get_instance()->get_default_user_id(),
            'days' => UAD_Core::get_instance()->get_default_days(),
            'show_chart' => 'true',
            'show_table' => 'true',
        ], $atts, 'user_activity_dashboard');

        $user_id = intval($atts['user_id']);
        $days = intval($atts['days']);
        $show_chart = filter_var($atts['show_chart'], FILTER_VALIDATE_BOOLEAN);
        $show_table = filter_var($atts['show_table'], FILTER_VALIDATE_BOOLEAN);

        // Check for URL parameters to override dates
        $url_start_date = isset($_GET['uad_start_date']) ? sanitize_text_field($_GET['uad_start_date']) : null;
        $url_end_date = isset($_GET['uad_end_date']) ? sanitize_text_field($_GET['uad_end_date']) : null;

        // Calculate date range (URL parameters take priority)
        if ($url_start_date && $url_end_date) {
            // Validate date format
            if ($this->is_valid_date($url_start_date) && $this->is_valid_date($url_end_date)) {
                $start_date = $url_start_date;
                $end_date = $url_end_date;
            } else {
                // Invalid dates, use default
                $end_date = current_time('Y-m-d');
                $start_date = date('Y-m-d', strtotime("-{$days} days"));
            }
        } else {
            // Use default date range based on 'days' attribute
            $end_date = current_time('Y-m-d');
            $start_date = date('Y-m-d', strtotime("-{$days} days"));
        }

        // Fetch data
        $data = $this->fetch_activity_data($user_id, $start_date, $end_date);

        if (is_wp_error($data)) {
            return $this->render_error($data->get_error_message());
        }

        // Start output buffering
        ob_start();

        // Include template
        include UAD_PLUGIN_DIR . 'templates/dashboard.php';

        return ob_get_clean();
    }

    /**
     * Fetch activity data from API
     *
     * @param int $user_id User ID
     * @param string $start_date Start date (Y-m-d)
     * @param string $end_date End date (Y-m-d)
     * @return array|WP_Error Activity data or error
     */
    private function fetch_activity_data($user_id, $start_date, $end_date) {
        try {
            $client = new UserActivityClient();
            $response = $client->getUserActivity($user_id, $start_date, $end_date);

            if (!$response['success']) {
                return new WP_Error('api_error', $response['message']);
            }

            // Process data
            $activities = $response['source'];

            // Calculate totals
            $total_hits = 0;
            $total_cost = 0;

            foreach ($activities as &$record) {
                $total_hits += $record['totalHits'];
                $total_cost += abs($record['hitCost']);

                // Add placeholders for future fields
                $record['nonSui'] = '';
                $record['sui'] = '';
                $record['otherServices'] = '';
                $record['otherServicesTotal'] = '';
            }

            return [
                'activities' => $activities,
                'summary' => [
                    'total_days' => count($activities),
                    'total_hits' => $total_hits,
                    'total_cost' => $total_cost,
                    'avg_hits_per_day' => count($activities) > 0 ? $total_hits / count($activities) : 0,
                    'final_balance' => !empty($activities) ? end($activities)['balance'] : 0,
                ],
                'start_date' => $start_date,
                'end_date' => $end_date,
                'user_id' => $user_id,
            ];

        } catch (ApiException $e) {
            return new WP_Error('api_exception', 'API Error: ' . $e->getMessage());
        } catch (Exception $e) {
            return new WP_Error('general_error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Render error message
     *
     * @param string $message Error message
     * @return string HTML error
     */
    private function render_error($message) {
        return sprintf(
            '<div class="uad-error"><p>%s</p></div>',
            esc_html($message)
        );
    }

    /**
     * Validate date format (Y-m-d)
     *
     * @param string $date Date string
     * @return bool
     */
    private function is_valid_date($date) {
        $d = DateTime::createFromFormat('Y-m-d', $date);
        return $d && $d->format('Y-m-d') === $date;
    }
}
