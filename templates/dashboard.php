<?php
/**
 * Dashboard template
 * Available variables: $data, $user_id, $days, $show_chart, $show_table
 */

if (!defined('ABSPATH')) {
    exit;
}

$activities = $data['activities'];
$summary = $data['summary'];
?>

<div class="uad-dashboard"
     data-user-id="<?php echo esc_attr($user_id); ?>"
     data-start-date="<?php echo esc_attr($data['start_date']); ?>"
     data-end-date="<?php echo esc_attr($data['end_date']); ?>"
     data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>"
     data-nonce="<?php echo wp_create_nonce('uad_load_data'); ?>">

    <!-- Dashboard Header -->
    <div class="uad-header">
        <h2 class="uad-title">Activity Dashboard</h2>

        <!-- Date Range Picker -->
        <div class="uad-date-picker">
            <label for="uad-start-date">Start Date:</label>
            <input type="date" id="uad-start-date" class="uad-date-input" value="<?php echo esc_attr($data['start_date']); ?>">

            <label for="uad-end-date">End Date:</label>
            <input type="date" id="uad-end-date" class="uad-date-input" value="<?php echo esc_attr($data['end_date']); ?>">

            <button id="uad-update-dates" class="uad-button">Update</button>
            <button id="uad-reset-dates" class="uad-button uad-button-secondary">Reset</button>
        </div>

        <div class="uad-summary">
            <div class="uad-summary-item">
                <span class="uad-summary-label">Total Days:</span>
                <span class="uad-summary-value"><?php echo number_format($summary['total_days']); ?></span>
            </div>
            <div class="uad-summary-item">
                <span class="uad-summary-label">Total Hits:</span>
                <span class="uad-summary-value"><?php echo number_format($summary['total_hits']); ?></span>
            </div>
            <div class="uad-summary-item">
                <span class="uad-summary-label">Total Cost:</span>
                <span class="uad-summary-value">$<?php echo number_format($summary['total_cost'], 2); ?></span>
            </div>
            <div class="uad-summary-item">
                <span class="uad-summary-label">Balance:</span>
                <span class="uad-summary-value uad-balance">$<?php echo number_format($summary['final_balance'], 2); ?></span>
            </div>
        </div>
    </div>

    <?php if ($show_chart && !empty($activities)) : ?>
    <!-- Chart Section -->
    <div class="uad-chart-container">
        <h3 class="uad-section-title">Recent Stats</h3>
        <canvas id="uad-activity-chart"></canvas>
    </div>
    <?php endif; ?>

    <?php if ($show_table && !empty($activities)) : ?>
    <!-- Table Section -->
    <div class="uad-table-container">
        <h3 class="uad-section-title">Daily Activity Log</h3>
        <div class="uad-table-wrapper">
            <table class="uad-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>NON-SUI</th>
                        <th>SUI</th>
                        <th>Total Hits</th>
                        <th>Cost</th>
                        <th>Other Services</th>
                        <th>Total</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($activities as $activity) : ?>
                    <tr>
                        <td class="uad-date"><?php echo esc_html($activity['date']); ?></td>
                        <td class="uad-nonsui"><?php echo esc_html($activity['nonSui']); ?></td>
                        <td class="uad-sui"><?php echo esc_html($activity['sui']); ?></td>
                        <td class="uad-hits"><?php echo number_format($activity['totalHits']); ?></td>
                        <td class="uad-cost">
                            <?php if ($activity['hitCost'] != 0) : ?>
                                $<?php echo number_format($activity['hitCost'], 2); ?>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="uad-other"><?php echo esc_html($activity['otherServices']); ?></td>
                        <td class="uad-total">
                            <?php if (!empty($activity['otherServicesTotal'])) : ?>
                                $<?php echo number_format($activity['otherServicesTotal'], 2); ?>
                            <?php else : ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="uad-balance <?php echo $activity['balance'] < 0 ? 'negative' : 'positive'; ?>">
                            $<?php echo number_format($activity['balance'], 2); ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <?php if (empty($activities)) : ?>
    <div class="uad-no-data">
        <p>No activity data found for the selected period.</p>
    </div>
    <?php endif; ?>

</div>

<?php if ($show_chart && !empty($activities)) : ?>
<script type="text/javascript">
    // Prepare chart data
    window.uadChartData = {
        labels: <?php echo json_encode(array_column($activities, 'date')); ?>,
        hits: <?php echo json_encode(array_column($activities, 'totalHits')); ?>,
        balance: <?php echo json_encode(array_column($activities, 'balance')); ?>
    };
</script>
<?php endif; ?>
