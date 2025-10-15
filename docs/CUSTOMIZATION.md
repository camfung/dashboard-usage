# Customization Guide

This guide covers how to customize the User Activity Dashboard to match your brand, add features, and modify behavior.

## Table of Contents

1. [Color Palette Customization](#color-palette-customization)
2. [Shortcode Customization](#shortcode-customization)
3. [Template Customization](#template-customization)
4. [Chart Customization](#chart-customization)
5. [Table Customization](#table-customization)
6. [Pagination Customization](#pagination-customization)
7. [WordPress Hooks](#wordpress-hooks)
8. [Adding Custom Fields](#adding-custom-fields)
9. [Custom Styling](#custom-styling)
10. [Advanced Customizations](#advanced-customizations)

## Color Palette Customization

### CSS Variables (Easiest Method)

**File:** `assets/css/uad-styles.css:11-17`

```css
:root {
    --baby-powder: #FFFFFA;      /* Background */
    --bleu-de-france: #3083DC;   /* Primary */
    --jet: #2D2D2A;              /* Text/headers */
    --selective-yellow: #FFB30F; /* Accents */
    --poppy: #DF2935;            /* Errors */
}
```

**To change colors:**
1. Open `assets/css/uad-styles.css`
2. Modify the hex values
3. Save the file
4. Clear browser cache

**Example - Blue Theme:**
```css
:root {
    --baby-powder: #F8F9FA;
    --bleu-de-france: #007BFF;
    --jet: #212529;
    --selective-yellow: #FFC107;
    --poppy: #DC3545;
}
```

### JavaScript Color Configuration

**File:** `assets/js/uad-scripts.js:9-15`

```javascript
const colors = {
    babyPowder: '#FFFFFA',
    bleuDeFrance: '#3083DC',
    jet: '#2D2D2A',
    selectiveYellow: '#FFB30F',
    poppy: '#DF2935'
};
```

**Note:** These must match the CSS variables for consistency.

### Override via Child Theme

Add to your WordPress theme's `style.css`:

```css
/* Override dashboard colors */
.uad-dashboard {
    --baby-powder: #FFFFFF;
    --bleu-de-france: #0066CC;
    --jet: #000000;
    --selective-yellow: #FFCC00;
    --poppy: #CC0000;
}
```

## Shortcode Customization

### Available Attributes

```
[user_activity_dashboard user_id="125" days="30" show_chart="true" show_table="true"]
```

| Attribute | Type | Default | Description |
|-----------|------|---------|-------------|
| `user_id` | int | Current user | User ID to display |
| `days` | int | 30 | Number of days to show |
| `show_chart` | bool | true | Show chart section |
| `show_table` | bool | true | Show table section |

### Examples

**Chart Only:**
```
[user_activity_dashboard show_table="false"]
```

**Table Only:**
```
[user_activity_dashboard show_chart="false"]
```

**Last 90 Days:**
```
[user_activity_dashboard days="90"]
```

**Specific User:**
```
[user_activity_dashboard user_id="125"]
```

**Combined:**
```
[user_activity_dashboard user_id="125" days="60" show_chart="true" show_table="true"]
```

### Modify Default Values

**File:** `includes/class-uad-core.php`

**Change default days:**
```php
public function get_default_days() {
    return apply_filters('uad_default_days', 60);  // Changed from 30
}
```

**Change default user ID behavior:**
```php
public function get_default_user_id() {
    $current_user_id = get_current_user_id();
    $default_user_id = $current_user_id > 0 ? $current_user_id : 1;  // Changed from 125
    return apply_filters('uad_default_user_id', $default_user_id);
}
```

## Template Customization

### Override Template in Theme

1. Create directory in your theme:
   ```
   wp-content/themes/your-theme/user-activity-dashboard/
   ```

2. Copy template file:
   ```bash
   cp wp-content/plugins/dashboard-usage/templates/dashboard.php \
      wp-content/themes/your-theme/user-activity-dashboard/dashboard.php
   ```

3. Modify the template in your theme directory

4. Update the plugin to check theme first:

**File:** `includes/class-uad-shortcode.php:86`

```php
// Check theme directory first
$theme_template = get_stylesheet_directory() . '/user-activity-dashboard/dashboard.php';
if (file_exists($theme_template)) {
    include $theme_template;
} else {
    include UAD_PLUGIN_DIR . 'templates/dashboard.php';
}
```

### Customize Header

**File:** `templates/dashboard.php:23-24`

```php
<!-- Change the title -->
<h2 class="uad-title">Your Custom Title</h2>
```

Or make it dynamic:
```php
<h2 class="uad-title">
    <?php echo apply_filters('uad_dashboard_title', 'Activity Dashboard'); ?>
</h2>
```

Then in your theme's `functions.php`:
```php
add_filter('uad_dashboard_title', function($title) {
    return 'My Custom Dashboard';
});
```

### Add Custom Summary Cards

**File:** `templates/dashboard.php:38-56`

Add after existing summary items:

```php
<div class="uad-summary-item">
    <span class="uad-summary-label">Average Per Day:</span>
    <span class="uad-summary-value">
        <?php echo number_format($summary['avg_hits_per_day'], 0); ?>
    </span>
</div>

<div class="uad-summary-item">
    <span class="uad-summary-label">Peak Day:</span>
    <span class="uad-summary-value">
        <?php
        $maxHits = max(array_column($activities, 'totalHits'));
        echo number_format($maxHits);
        ?>
    </span>
</div>
```

### Modify Table Columns

**File:** `templates/dashboard.php:88-98`

**Remove a column:**
```php
<!-- Comment out or delete -->
<!-- <th>NON-SUI</th> -->
```

**Add a column:**
```php
<th>Average</th>
```

Then in the row data (line 101-126):
```php
<td class="uad-average">
    <?php
    $avg = $activity['totalHits'] > 0
        ? $activity['hitCost'] / $activity['totalHits']
        : 0;
    echo '$' . number_format($avg, 4);
    ?>
</td>
```

## Chart Customization

### Change Chart Type

**File:** `assets/js/uad-scripts.js:42`

```javascript
// Options: 'line', 'bar', 'radar', 'doughnut', 'pie', etc.
new Chart(ctx, {
    type: 'bar',  // Changed from 'line'
    data: { ... }
});
```

### Adjust Chart Height

**File:** `assets/js/uad-scripts.js:67`

```javascript
options: {
    responsive: true,
    maintainAspectRatio: true,
    aspectRatio: 3.0,  // Changed from 2.5 (wider/shorter)
}
```

Or set fixed height in CSS:

**File:** `assets/css/uad-styles.css:209-211`

```css
#uad-activity-chart {
    max-height: 500px;  /* Changed from 400px */
}
```

### Modify Chart Colors

**File:** `assets/js/uad-scripts.js:50-60`

```javascript
datasets: [
    {
        label: 'Total Hits',
        data: chartData.hits,
        borderColor: '#FF6384',        // Custom color
        backgroundColor: 'rgba(255, 99, 132, 0.2)',
        borderWidth: 3,                // Thicker line
        // ...
    }
]
```

### Add Gridline Customization

**File:** `assets/js/uad-scripts.js:110-112`

```javascript
x: {
    grid: {
        color: 'rgba(0, 0, 0, 0.1)',  // Light gray
        lineWidth: 1,                  // Thin lines
        drawBorder: false,
        borderDash: [5, 5]             // Dashed lines
    }
}
```

### Customize Tooltips

**File:** `assets/js/uad-scripts.js:87-106`

```javascript
tooltip: {
    backgroundColor: '#000000',      // Black background
    titleColor: '#FFFFFF',
    bodyColor: '#FFFFFF',
    borderColor: '#FF6384',
    borderWidth: 2,
    padding: 15,                     // More padding
    titleFont: {
        size: 14,
        weight: 'bold'
    },
    bodyFont: {
        size: 13
    },
    callbacks: {
        title: function(context) {
            return 'Date: ' + context[0].label;
        },
        label: function(context) {
            return 'Hits: ' + context.parsed.y.toLocaleString();
        },
        footer: function(context) {
            return 'Click for details';
        }
    }
}
```

## Table Customization

### Change Default Rows Per Page

**File:** `assets/js/uad-scripts.js:264`

```javascript
let rowsPerPage = 25;  // Changed from 10
```

And update the HTML default:

**File:** `templates/dashboard.php:75-80`

```html
<select id="uad-rows-per-page" class="uad-select">
    <option value="10">10</option>
    <option value="25" selected>25</option>  <!-- Add selected -->
    <option value="50">50</option>
    <option value="100">100</option>
</select>
```

### Modify Table Styling

**Zebra Striping:**

**File:** `assets/css/uad-styles.css`

```css
.uad-table tbody tr:nth-child(even) {
    background-color: rgba(48, 131, 220, 0.05);
}

.uad-table tbody tr:nth-child(odd) {
    background-color: #FFFFFF;
}
```

**Alternating Colors:**

```css
.uad-table tbody tr:nth-child(3n+1) {
    background-color: #F8F9FA;
}

.uad-table tbody tr:nth-child(3n+2) {
    background-color: #FFFFFF;
}

.uad-table tbody tr:nth-child(3n+3) {
    background-color: #E9ECEF;
}
```

**Border Styles:**

```css
.uad-table {
    border: 2px solid var(--bleu-de-france);
}

.uad-table thead th {
    border-bottom: 3px solid var(--selective-yellow);
}

.uad-table tbody td {
    border-bottom: 1px solid #DEE2E6;
}
```

### Add Row Click Handler

**File:** `assets/js/uad-scripts.js` (add to initPagination)

```javascript
// Add after pagination initialization
tbody.addEventListener('click', function(e) {
    const row = e.target.closest('.uad-table-row');
    if (row) {
        const date = row.querySelector('.uad-date').textContent;
        const hits = row.querySelector('.uad-hits').textContent;

        alert(`Clicked: ${date} - ${hits} hits`);

        // Or redirect to detail page
        // window.location.href = `/activity-detail?date=${date}`;
    }
});
```

## Pagination Customization

### Change Page Number Display

**File:** `assets/js/uad-scripts.js:332-342`

```javascript
// Show more page numbers
let startPage = Math.max(1, currentPage - 5);  // Changed from 3
let endPage = Math.min(totalPages, currentPage + 5);

// Adjust if at beginning or end
if (currentPage <= 6) {  // Changed from 4
    endPage = Math.min(11, totalPages);  // Changed from 7
}
if (currentPage >= totalPages - 5) {  // Changed from 3
    startPage = Math.max(1, totalPages - 10);  // Changed from 6
}
```

### Customize Pagination Buttons

**File:** `assets/css/uad-styles.css:464-495`

```css
.uad-page-btn {
    padding: 10px 20px;          /* Larger buttons */
    border-radius: 8px;          /* More rounded */
    background: linear-gradient(135deg, #3083DC, #2066B8);  /* Gradient */
    font-size: 16px;             /* Larger text */
}

.uad-page-num {
    min-width: 50px;             /* Wider buttons */
    height: 50px;                /* Taller buttons */
    border-radius: 50%;          /* Circular buttons */
}

.uad-page-num.active {
    background: linear-gradient(135deg, #FFB30F, #FF9500);
    transform: scale(1.2);       /* Larger active page */
}
```

### Add Jump to Page

**File:** `templates/dashboard.php:132-150` (add after pagination buttons)

```html
<div class="uad-jump-to-page">
    <label for="uad-page-jump">Go to page:</label>
    <input type="number" id="uad-page-jump" min="1" class="uad-page-input">
    <button id="uad-go-to-page" class="uad-button">Go</button>
</div>
```

Then add JavaScript:

**File:** `assets/js/uad-scripts.js` (add to initPagination)

```javascript
const pageJumpInput = document.getElementById('uad-page-jump');
const goToPageBtn = document.getElementById('uad-go-to-page');

if (pageJumpInput && goToPageBtn) {
    pageJumpInput.max = getTotalPages();

    goToPageBtn.addEventListener('click', function() {
        const page = parseInt(pageJumpInput.value);
        if (page >= 1 && page <= getTotalPages()) {
            showPage(page);
        }
    });

    pageJumpInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            goToPageBtn.click();
        }
    });
}
```

## WordPress Hooks

### Filter: uad_default_user_id

**Purpose:** Override default user ID

**File:** Your theme's `functions.php`

```php
add_filter('uad_default_user_id', function($default_id) {
    // Always use admin
    return 1;

    // Or use a specific user
    return 125;

    // Or use current user with fallback
    return get_current_user_id() ?: 125;
});
```

### Filter: uad_default_days

**Purpose:** Override default number of days

```php
add_filter('uad_default_days', function($default_days) {
    // Show 60 days instead of 30
    return 60;

    // Or dynamic based on user role
    if (current_user_can('administrator')) {
        return 365;  // Admins see full year
    }
    return 30;  // Others see 30 days
});
```

### Filter: uad_dashboard_title

**Purpose:** Customize dashboard title

```php
add_filter('uad_dashboard_title', function($title) {
    $user = wp_get_current_user();
    return sprintf('Activity Dashboard - %s', $user->display_name);
});
```

### Action: uad_before_dashboard

**Purpose:** Add content before dashboard

**File:** `templates/dashboard.php:15` (add this line)

```php
<?php do_action('uad_before_dashboard', $user_id, $data); ?>
```

Then in `functions.php`:

```php
add_action('uad_before_dashboard', function($user_id, $data) {
    echo '<div class="custom-notice">Welcome to your dashboard!</div>';
}, 10, 2);
```

### Action: uad_after_dashboard

**Purpose:** Add content after dashboard

**File:** `templates/dashboard.php:160` (add this line)

```php
<?php do_action('uad_after_dashboard', $user_id, $data); ?>
```

Then in `functions.php`:

```php
add_action('uad_after_dashboard', function($user_id, $data) {
    echo '<div class="dashboard-footer">Data updated hourly</div>';
}, 10, 2);
```

## Adding Custom Fields

### Modify API Response Processing

**File:** `includes/class-uad-shortcode.php:115-124`

```php
foreach ($activities as &$record) {
    $total_hits += $record['totalHits'];
    $total_cost += abs($record['hitCost']);

    // Add custom calculated fields
    $record['costPerHit'] = $record['totalHits'] > 0
        ? $record['hitCost'] / $record['totalHits']
        : 0;

    $record['dayOfWeek'] = date('l', strtotime($record['date']));

    $record['isWeekend'] = in_array(
        date('N', strtotime($record['date'])),
        [6, 7]
    );

    // Existing placeholders
    $record['nonSui'] = '';
    $record['sui'] = '';
    $record['otherServices'] = '';
    $record['otherServicesTotal'] = '';
}
```

### Display Custom Fields in Table

**File:** `templates/dashboard.php`

Add to header:
```html
<th>Cost/Hit</th>
<th>Day</th>
```

Add to row:
```php
<td class="uad-cost-per-hit">
    $<?php echo number_format($activity['costPerHit'], 4); ?>
</td>
<td class="uad-day <?php echo $activity['isWeekend'] ? 'weekend' : 'weekday'; ?>">
    <?php echo esc_html($activity['dayOfWeek']); ?>
</td>
```

Style weekends:
```css
.uad-table .weekend {
    background-color: rgba(255, 179, 15, 0.1);
    font-weight: 600;
}
```

## Custom Styling

### Add Custom CSS via Plugin

**File:** `includes/class-uad-core.php:66-81`

```php
public function enqueue_assets() {
    // ... existing code ...

    // Add custom styles
    wp_add_inline_style('uad-styles', '
        .uad-dashboard {
            font-family: "Helvetica Neue", Arial, sans-serif;
        }
        .uad-summary-item {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    ');
}
```

### Add Custom CSS via Theme

**File:** Your theme's `style.css`

```css
/* Override dashboard container */
.uad-dashboard {
    background-color: #F5F5F5;
    border: 3px solid #3083DC;
}

/* Custom summary cards */
.uad-summary-item {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}

/* Custom table header */
.uad-table thead {
    background: linear-gradient(90deg, #3083DC 0%, #1E5BB8 100%);
}

/* Hide specific columns */
.uad-table .uad-nonsui,
.uad-table .uad-sui {
    display: none;
}
```

### Add Custom Fonts

```php
// In functions.php
add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'custom-fonts',
        'https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap'
    );

    wp_add_inline_style('uad-styles', '
        .uad-dashboard {
            font-family: "Roboto", sans-serif;
        }
    ');
});
```

## Advanced Customizations

### Add Export to CSV Button

**File:** `templates/dashboard.php:24` (after title)

```php
<button id="uad-export-csv" class="uad-button uad-button-secondary">
    Export to CSV
</button>
```

**JavaScript:**

```javascript
// Add to assets/js/uad-scripts.js
function initExport() {
    const exportBtn = document.getElementById('uad-export-csv');
    if (!exportBtn) return;

    exportBtn.addEventListener('click', function() {
        const table = document.getElementById('uad-activity-table');
        let csv = [];

        // Get headers
        const headers = Array.from(table.querySelectorAll('thead th'))
            .map(th => th.textContent.trim());
        csv.push(headers.join(','));

        // Get all rows (not just visible)
        const allRows = table.querySelectorAll('tbody tr');
        allRows.forEach(row => {
            const cells = Array.from(row.querySelectorAll('td'))
                .map(td => `"${td.textContent.trim()}"`);
            csv.push(cells.join(','));
        });

        // Download
        const csvContent = csv.join('\n');
        const blob = new Blob([csvContent], { type: 'text/csv' });
        const url = URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = 'activity-export.csv';
        a.click();
        URL.revokeObjectURL(url);
    });
}

// Call in init()
function init() {
    initChart();
    initDatePicker();
    initPagination();
    initExport();  // Add this
}
```

### Add Print Stylesheet

```php
// In includes/class-uad-core.php
wp_enqueue_style(
    'uad-print',
    UAD_PLUGIN_URL . 'assets/css/uad-print.css',
    ['uad-styles'],
    '1.2.0',
    'print'  // Print media
);
```

**File:** Create `assets/css/uad-print.css`

```css
@media print {
    /* Hide interactive elements */
    .uad-date-picker,
    .uad-pagination,
    #uad-export-csv {
        display: none !important;
    }

    /* Show all table rows */
    .uad-table-row {
        display: table-row !important;
    }

    /* Optimize for print */
    .uad-dashboard {
        box-shadow: none;
        margin: 0;
        padding: 0;
    }

    /* Page breaks */
    .uad-chart-container {
        page-break-after: always;
    }
}
```

### Add Search/Filter Functionality

**File:** `templates/dashboard.php:69` (before table)

```html
<div class="uad-search">
    <input type="text" id="uad-search-input" placeholder="Search by date...">
</div>
```

**JavaScript:**

```javascript
function initSearch() {
    const searchInput = document.getElementById('uad-search-input');
    if (!searchInput) return;

    searchInput.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const allRows = document.querySelectorAll('.uad-table-row');

        allRows.forEach(row => {
            const date = row.querySelector('.uad-date').textContent.toLowerCase();
            if (date.includes(query)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
}
```

---

For more customization options, see:
- [Frontend Development](./FRONTEND.md)
- [Architecture Overview](./ARCHITECTURE.md)
- [API Client Documentation](./API_CLIENT.md)
