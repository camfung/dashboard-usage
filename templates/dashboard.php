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
        <div class="uad-table-header">
            <h3 class="uad-section-title">Daily Activity Log</h3>
            <div class="uad-table-info">
                <span class="uad-showing-entries">Showing <span id="uad-showing-start">1</span>-<span id="uad-showing-end">10</span> of <span id="uad-total-entries"><?php echo count($activities); ?></span> entries</span>
                <div class="uad-per-page">
                    <label for="uad-rows-per-page">Rows per page:</label>
                    <select id="uad-rows-per-page" class="uad-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="<?php echo count($activities); ?>">All</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="uad-table-wrapper">
            <table class="uad-table" id="uad-activity-table" data-total-rows="<?php echo count($activities); ?>">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>
                            Top Link #1
                            <?php if (!empty($data['top_links']['link1'])) : ?>
                                <br><small class="uad-top-link-label"><?php echo esc_html($data['top_links']['link1']['keyword'] ?: '(deleted)'); ?></small>
                            <?php endif; ?>
                        </th>
                        <th>
                            Top Link #2
                            <?php if (!empty($data['top_links']['link2'])) : ?>
                                <br><small class="uad-top-link-label"><?php echo esc_html($data['top_links']['link2']['keyword'] ?: '(deleted)'); ?></small>
                            <?php endif; ?>
                        </th>
                        <th>Total Hits</th>
                        <th>Cost</th>
                        <th>Other Services</th>
                        <th>Total</th>
                        <th>Balance</th>
                    </tr>
                </thead>
                <tbody id="uad-table-body">
                    <?php foreach ($activities as $index => $activity) : ?>
                    <tr class="uad-table-row" data-row-index="<?php echo $index; ?>">
                        <td class="uad-date"><?php echo esc_html($activity['date']); ?></td>
                        <td class="uad-nonsui"><?php echo number_format($activity['nonSuiHits']); ?></td>
                        <td class="uad-sui"><?php echo number_format($activity['suiHits']); ?></td>
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

        <!-- Pagination Controls -->
        <div class="uad-pagination">
            <button class="uad-page-btn uad-page-first" id="uad-first-page" disabled>
                <span>&laquo;</span> First
            </button>
            <button class="uad-page-btn uad-page-prev" id="uad-prev-page" disabled>
                <span>&lsaquo;</span> Previous
            </button>

            <div class="uad-page-numbers" id="uad-page-numbers">
                <!-- Page numbers will be generated by JavaScript -->
            </div>

            <button class="uad-page-btn uad-page-next" id="uad-next-page">
                Next <span>&rsaquo;</span>
            </button>
            <button class="uad-page-btn uad-page-last" id="uad-last-page">
                Last <span>&raquo;</span>
            </button>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($link_data['links'])) : ?>
    <!-- Link Activity Table Section -->
    <div class="uad-table-container uad-link-table-container">
        <div class="uad-table-header">
            <h3 class="uad-section-title">Link Activity</h3>
            <div class="uad-table-info">
                <span class="uad-showing-entries">Showing <span id="uad-link-showing-start">1</span>-<span id="uad-link-showing-end">10</span> of <span id="uad-link-total-entries"><?php echo count($link_data['links']); ?></span> entries</span>
                <div class="uad-per-page">
                    <label for="uad-link-rows-per-page">Rows per page:</label>
                    <select id="uad-link-rows-per-page" class="uad-select">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                        <option value="100">100</option>
                        <option value="<?php echo count($link_data['links']); ?>">All</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="uad-table-wrapper">
            <table class="uad-table uad-link-table" id="uad-link-table" data-total-rows="<?php echo count($link_data['links']); ?>">
                <thead>
                    <tr>
                        <th>Link ID</th>
                        <th>Keyword</th>
                        <th>Destination</th>
                        <th>Total Hits</th>
                        <th>Total Cost</th>
                    </tr>
                </thead>
                <tbody id="uad-link-table-body">
                    <?php foreach ($link_data['links'] as $index => $link) : ?>
                    <tr class="uad-table-row" data-row-index="<?php echo $index; ?>">
                        <td class="uad-link-id"><?php echo esc_html($link['mid']); ?></td>
                        <td class="uad-keyword">
                            <?php if ($link['keyword'] !== null) : ?>
                                <code><?php echo esc_html($link['keyword']); ?></code>
                            <?php else : ?>
                                <span class="uad-deleted-link">(deleted)</span>
                            <?php endif; ?>
                        </td>
                        <td class="uad-destination">
                            <?php if ($link['destination'] !== null) : ?>
                                <a href="<?php echo esc_url($link['destination']); ?>" target="_blank" rel="noopener noreferrer" class="uad-destination-link">
                                    <?php echo esc_html($link['destination']); ?>
                                </a>
                            <?php else : ?>
                                <span class="uad-deleted-link">(deleted)</span>
                            <?php endif; ?>
                        </td>
                        <td class="uad-hits"><?php echo number_format($link['totalHits']); ?></td>
                        <td class="uad-cost">$<?php echo number_format(abs($link['totalCost']), 2); ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination Controls for Link Table -->
        <div class="uad-pagination">
            <button class="uad-page-btn uad-page-first" id="uad-link-first-page" disabled>
                <span>&laquo;</span> First
            </button>
            <button class="uad-page-btn uad-page-prev" id="uad-link-prev-page" disabled>
                <span>&lsaquo;</span> Previous
            </button>

            <div class="uad-page-numbers" id="uad-link-page-numbers">
                <!-- Page numbers will be generated by JavaScript -->
            </div>

            <button class="uad-page-btn uad-page-next" id="uad-link-next-page">
                Next <span>&rsaquo;</span>
            </button>
            <button class="uad-page-btn uad-page-last" id="uad-link-last-page">
                Last <span>&raquo;</span>
            </button>
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
        balance: <?php echo json_encode(array_column($activities, 'balance')); ?>,
        topLink1: <?php echo json_encode(array_column($activities, 'nonSuiHits')); ?>,
        topLink2: <?php echo json_encode(array_column($activities, 'suiHits')); ?>,
        topLink1Name: <?php echo json_encode(!empty($data['top_links']['link1']) ? ($data['top_links']['link1']['keyword'] ?: '(deleted)') : 'N/A'); ?>,
        topLink2Name: <?php echo json_encode(!empty($data['top_links']['link2']) ? ($data['top_links']['link2']['keyword'] ?: '(deleted)') : 'N/A'); ?>
    };
</script>
<?php endif; ?>
