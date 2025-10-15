# Frontend Development Guide

Complete guide to the frontend architecture, JavaScript functionality, CSS styling, and Chart.js integration.

## Table of Contents

1. [Overview](#overview)
2. [File Structure](#file-structure)
3. [JavaScript Architecture](#javascript-architecture)
4. [CSS Styling System](#css-styling-system)
5. [Chart.js Integration](#chartjs-integration)
6. [Date Picker](#date-picker)
7. [Pagination System](#pagination-system)
8. [Template System](#template-system)
9. [Responsive Design](#responsive-design)
10. [Performance Optimization](#performance-optimization)

## Overview

The frontend uses a **progressive enhancement** approach:
1. Server renders complete HTML
2. CSS provides styling and layout
3. JavaScript adds interactivity

### Core Technologies

- **Vanilla JavaScript** - No frameworks, pure ES6+
- **CSS3** - Modern features (Grid, Flexbox, Custom Properties)
- **Chart.js 4.4.0** - Data visualization library
- **HTML5** - Semantic markup and native date inputs

### Browser Support

- **Chrome** - Latest 2 versions
- **Firefox** - Latest 2 versions
- **Safari** - Latest 2 versions
- **Edge** - Latest 2 versions
- **Mobile** - iOS Safari 12+, Chrome Android

## File Structure

```
assets/
├── css/
│   └── uad-styles.css         # Main stylesheet (603 lines)
├── js/
│   └── uad-scripts.js         # Main JavaScript (439 lines)
└── vendor/
    └── chart.min.js           # Chart.js library
```

## JavaScript Architecture

### Module Pattern

The entire JavaScript is wrapped in an IIFE (Immediately Invoked Function Expression):

```javascript
(function() {
    'use strict';

    // Private scope
    const colors = { ... };

    function initChart() { ... }
    function initDatePicker() { ... }
    function initPagination() { ... }

    function init() {
        initChart();
        initDatePicker();
        initPagination();
    }

    // Auto-initialize
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
```

**Benefits:**
- No global scope pollution
- Encapsulation of private variables
- Clean initialization pattern

### Color Palette (assets/js/uad-scripts.js:9-15)

```javascript
const colors = {
    babyPowder: '#FFFFFA',      // Background
    bleuDeFrance: '#3083DC',    // Primary/borders
    jet: '#2D2D2A',             // Text/headers
    selectiveYellow: '#FFB30F', // Accents/highlights
    poppy: '#DF2935'            // Errors/negative
};
```

### Initialization Flow

```
1. DOM Ready Event
   ↓
2. init() function called
   ↓
3. initChart() - Sets up Chart.js
   ↓
4. initDatePicker() - Attaches date picker handlers
   ↓
5. initPagination() - Sets up table pagination
```

## Chart.js Integration

### Chart Initialization (assets/js/uad-scripts.js:20-153)

**Function:** `initChart()`

**Process:**
1. Check if canvas element exists
2. Check if Chart.js library loaded
3. Check if chart data available
4. Create gradients for area fills
5. Initialize Chart.js instance

### Chart Configuration

```javascript
new Chart(ctx, {
    type: 'line',
    data: {
        labels: chartData.labels,     // Date labels from PHP
        datasets: [{
            label: 'Total Hits',
            data: chartData.hits,     // Hit counts from PHP
            borderColor: colors.selectiveYellow,
            backgroundColor: hitsGradient,
            borderWidth: 2,
            fill: true,
            tension: 0.4,
            yAxisID: 'y-hits'
        }]
    },
    options: { ... }
});
```

### Gradient Creation

```javascript
const hitsGradient = ctx.createLinearGradient(0, 0, 0, 400);
hitsGradient.addColorStop(0, colors.selectiveYellow + 'CC'); // 80% opacity
hitsGradient.addColorStop(1, colors.selectiveYellow + '33'); // 20% opacity
```

**Opacity Values:**
- `'FF'` = 100% (255)
- `'CC'` = 80% (204)
- `'80'` = 50% (128)
- `'33'` = 20% (51)
- `'26'` = 15% (38)
- `'00'` = 0% (0)

### Chart Options

**Responsive:**
```javascript
responsive: true,
maintainAspectRatio: true,
aspectRatio: 2.5,  // Width:Height ratio
```

**Interaction:**
```javascript
interaction: {
    mode: 'index',        // Show all datasets at X position
    intersect: false,     // Don't require exact point hover
}
```

**Grid Lines:**
```javascript
grid: {
    color: colors.jet + '26',  // 15% opacity
    drawBorder: false
}
```

**Tooltip Formatting:**
```javascript
callbacks: {
    label: function(context) {
        let label = context.dataset.label || '';
        if (label) {
            label += ': ';
        }
        label += context.parsed.y.toLocaleString();
        return label;
    }
}
```

### Data Preparation (PHP Side)

**File:** `templates/dashboard.php:162-171`

```php
<?php if ($show_chart && !empty($activities)) : ?>
<script type="text/javascript">
    window.uadChartData = {
        labels: <?php echo json_encode(array_column($activities, 'date')); ?>,
        hits: <?php echo json_encode(array_column($activities, 'totalHits')); ?>
    };
</script>
<?php endif; ?>
```

## Date Picker

### Functionality (assets/js/uad-scripts.js:155-238)

**Function:** `initDatePicker()`

**Features:**
1. Date validation (start < end)
2. Maximum date = today
3. Dynamic min/max constraints
4. URL parameter updates
5. Loading states

### HTML Structure (templates/dashboard.php:26-36)

```html
<div class="uad-date-picker">
    <label for="uad-start-date">Start Date:</label>
    <input type="date" id="uad-start-date" class="uad-date-input" value="...">

    <label for="uad-end-date">End Date:</label>
    <input type="date" id="uad-end-date" class="uad-date-input" value="...">

    <button id="uad-update-dates" class="uad-button">Update</button>
    <button id="uad-reset-dates" class="uad-button uad-button-secondary">Reset</button>
</div>
```

### Update Button Handler

```javascript
updateButton.addEventListener('click', function() {
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;

    // Validation
    if (!startDate || !endDate) {
        alert('Please select both start and end dates');
        return;
    }

    if (startDate > endDate) {
        alert('Start date must be before end date');
        return;
    }

    // Show loading state
    updateButton.disabled = true;
    updateButton.textContent = 'Loading...';
    dashboard.classList.add('uad-loading');

    // Reload with URL parameters
    const url = new URL(window.location.href);
    url.searchParams.set('uad_start_date', startDate);
    url.searchParams.set('uad_end_date', endDate);
    window.location.href = url.toString();
});
```

### Reset Button Handler

```javascript
resetButton.addEventListener('click', function() {
    const url = new URL(window.location.href);
    url.searchParams.delete('uad_start_date');
    url.searchParams.delete('uad_end_date');
    window.location.href = url.toString();
});
```

### Date Constraints

```javascript
// Set max date to today
const today = new Date().toISOString().split('T')[0];
startDateInput.max = today;
endDateInput.max = today;

// Dynamic constraints
startDateInput.addEventListener('change', function() {
    endDateInput.min = this.value;  // End must be >= start
});

endDateInput.addEventListener('change', function() {
    startDateInput.max = this.value;  // Start must be <= end
});
```

## Pagination System

### Overview (assets/js/uad-scripts.js:240-424)

**Function:** `initPagination()`

**Features:**
- Client-side pagination (no server requests)
- Dynamic page number generation
- Rows per page selector
- First/Previous/Next/Last navigation
- Smart page number display (max 7 visible)

### State Management

```javascript
let currentPage = 1;
let rowsPerPage = 10;

const allRows = Array.from(tbody.querySelectorAll('.uad-table-row'));
const totalRows = allRows.length;
```

### Show Page Function

```javascript
function showPage(page) {
    const totalPages = getTotalPages();

    // Validate page number
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    currentPage = page;

    // Calculate row range
    const start = (currentPage - 1) * rowsPerPage;
    const end = start + rowsPerPage;

    // Show/hide rows
    allRows.forEach((row, index) => {
        if (index >= start && index < end) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });

    // Update UI
    updateShowingText();
    updatePaginationButtons();
}
```

### Page Number Generation

```javascript
function generatePageNumbers(totalPages) {
    pageNumbers.innerHTML = '';

    if (totalPages <= 1) return;

    let startPage = Math.max(1, currentPage - 3);
    let endPage = Math.min(totalPages, currentPage + 3);

    // Adjust if at beginning or end
    if (currentPage <= 4) {
        endPage = Math.min(7, totalPages);
    }
    if (currentPage >= totalPages - 3) {
        startPage = Math.max(1, totalPages - 6);
    }

    // Add first page + ellipsis if needed
    if (startPage > 1) {
        addPageButton(1);
        if (startPage > 2) {
            addEllipsis();
        }
    }

    // Add page numbers
    for (let i = startPage; i <= endPage; i++) {
        addPageButton(i);
    }

    // Add ellipsis + last page if needed
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            addEllipsis();
        }
        addPageButton(totalPages);
    }
}
```

### Rows Per Page Selector

```javascript
rowsPerPageSelect.addEventListener('change', function() {
    rowsPerPage = parseInt(this.value);
    currentPage = 1;  // Reset to first page
    showPage(1);
});
```

### HTML Structure (templates/dashboard.php:71-150)

```html
<!-- Table Header -->
<div class="uad-table-header">
    <h3>Daily Activity Log</h3>
    <div class="uad-table-info">
        <span class="uad-showing-entries">
            Showing <span id="uad-showing-start">1</span>-<span id="uad-showing-end">10</span>
            of <span id="uad-total-entries">50</span> entries
        </span>
        <select id="uad-rows-per-page">
            <option value="10">10</option>
            <option value="25">25</option>
            <option value="50">50</option>
            <option value="100">100</option>
            <option value="all">All</option>
        </select>
    </div>
</div>

<!-- Pagination Controls -->
<div class="uad-pagination">
    <button id="uad-first-page">&laquo; First</button>
    <button id="uad-prev-page">&lsaquo; Previous</button>
    <div id="uad-page-numbers"><!-- Generated by JS --></div>
    <button id="uad-next-page">Next &rsaquo;</button>
    <button id="uad-last-page">Last &raquo;</button>
</div>
```

## CSS Styling System

### Architecture

The CSS follows a **component-based** architecture with BEM-like naming:

```
Component-Element-Modifier

.uad-dashboard              // Component
.uad-summary-item          // Component-Element
.uad-button-secondary      // Component-Modifier
```

### CSS Variables (assets/css/uad-styles.css:11-17)

```css
:root {
    --baby-powder: #FFFFFA;
    --bleu-de-france: #3083DC;
    --jet: #2D2D2A;
    --selective-yellow: #FFB30F;
    --poppy: #DF2935;
}
```

### Key Components

#### 1. Dashboard Container

```css
.uad-dashboard {
    background-color: var(--baby-powder);
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(45, 45, 42, 0.1);
    padding: 24px;
    margin: 20px 0;
}
```

#### 2. Summary Cards

```css
.uad-summary {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.uad-summary-item {
    background: var(--bleu-de-france);
    color: var(--baby-powder);
    padding: 16px 20px;
    border-radius: 6px;
    transition: transform 0.2s ease;
}

.uad-summary-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(48, 131, 220, 0.3);
}
```

#### 3. Data Table

```css
.uad-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}

.uad-table thead {
    background: linear-gradient(135deg, var(--jet), #3a3a36);
    color: var(--baby-powder);
}

.uad-table tbody tr:hover {
    background-color: rgba(48, 131, 220, 0.05);
}
```

**Column Separators:**
```css
/* Vertical lines between columns */
.uad-table thead th:nth-child(4),
.uad-table tbody td:nth-child(4) {
    border-right: 2px solid var(--bleu-de-france);
}

.uad-table thead th:nth-child(7),
.uad-table tbody td:nth-child(7) {
    border-right: 2px solid var(--bleu-de-france);
}
```

#### 4. Pagination

```css
.uad-pagination {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}

.uad-page-num {
    min-width: 40px;
    height: 40px;
    border: 2px solid var(--bleu-de-france);
    border-radius: 4px;
    transition: all 0.2s ease;
}

.uad-page-num.active {
    background: var(--selective-yellow);
    border-color: var(--selective-yellow);
    color: var(--jet);
}
```

#### 5. Buttons

```css
.uad-button {
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    background: var(--bleu-de-france);
    color: var(--baby-powder);
    cursor: pointer;
    transition: all 0.2s ease;
}

.uad-button:hover {
    background: var(--selective-yellow);
    color: var(--jet);
    transform: translateY(-1px);
}

.uad-button:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}
```

### Utility Classes

```css
/* Loading state */
.uad-dashboard.uad-loading {
    opacity: 0.7;
    pointer-events: none;
}

/* Balance colors */
.uad-balance.negative {
    color: var(--poppy);
}

.uad-balance.positive {
    color: #28a745;
}
```

## Template System

### PHP Template (templates/dashboard.php)

**Available Variables:**
```php
$data          // Array with 'activities', 'summary', 'start_date', 'end_date'
$user_id       // Current user ID
$days          // Number of days to display
$show_chart    // Boolean: show chart section
$show_table    // Boolean: show table section
```

### Template Structure

```
1. Dashboard Container
   ├── 2. Header Section
   │   ├── Title
   │   ├── Date Picker
   │   └── Summary Cards
   ├── 3. Chart Section (conditional)
   │   └── Canvas element
   ├── 4. Table Section (conditional)
   │   ├── Table Header Controls
   │   ├── Data Table
   │   └── Pagination Controls
   └── 5. Empty State (conditional)
```

### Data Attributes

```html
<div class="uad-dashboard"
     data-user-id="<?php echo esc_attr($user_id); ?>"
     data-start-date="<?php echo esc_attr($data['start_date']); ?>"
     data-end-date="<?php echo esc_attr($data['end_date']); ?>"
     data-ajax-url="<?php echo admin_url('admin-ajax.php'); ?>"
     data-nonce="<?php echo wp_create_nonce('uad_load_data'); ?>">
```

**Purpose:** Store configuration for JavaScript access

### Output Escaping

```php
<?php echo esc_html($activity['date']); ?>           // Escape HTML
<?php echo esc_attr($user_id); ?>                    // Escape attributes
<?php echo number_format($activity['totalHits']); ?>  // Format numbers
<?php echo json_encode($data); ?>                     // JSON encoding
```

## Responsive Design

### Breakpoints

```css
/* Desktop: Default styles */

/* Tablet: 768px and below */
@media screen and (max-width: 768px) { ... }

/* Mobile: 480px and below */
@media screen and (max-width: 480px) { ... }
```

### Mobile Adaptations

**Date Picker (Tablet):**
```css
@media screen and (max-width: 768px) {
    .uad-date-picker {
        flex-direction: column;
        align-items: stretch;
    }

    .uad-date-input,
    .uad-button {
        width: 100%;
    }
}
```

**Pagination (Mobile):**
```css
@media screen and (max-width: 480px) {
    .uad-page-btn span {
        display: none;  // Hide "First", "Last" text
    }

    .uad-page-num {
        min-width: 32px;
        height: 32px;
        font-size: 12px;
    }
}
```

**Table (Mobile):**
```css
@media screen and (max-width: 768px) {
    .uad-table {
        font-size: 12px;
    }

    .uad-table thead th,
    .uad-table tbody td {
        padding: 10px 8px;  // Reduce padding
    }
}
```

## Performance Optimization

### CSS Optimizations

1. **Use CSS transforms for animations** (GPU accelerated)
   ```css
   transform: translateY(-2px);  /* Better than top: -2px */
   ```

2. **Minimize reflows**
   ```css
   will-change: transform;  /* Hint to browser */
   ```

3. **Use efficient selectors**
   ```css
   .uad-table tbody tr { }  /* Good */
   div > table > tbody > tr { }  /* Bad - too specific */
   ```

### JavaScript Optimizations

1. **Event delegation** (for dynamically generated elements)
   ```javascript
   // Instead of adding listeners to each page button
   pageNumbers.addEventListener('click', function(e) {
       if (e.target.classList.contains('uad-page-num')) {
           // Handle page click
       }
   });
   ```

2. **Minimize DOM queries**
   ```javascript
   // Cache DOM elements
   const tbody = document.getElementById('uad-table-body');
   const allRows = Array.from(tbody.querySelectorAll('.uad-table-row'));
   ```

3. **Use CSS for show/hide**
   ```javascript
   // Better than removing/adding elements
   row.style.display = 'none';  // Hide
   row.style.display = '';      // Show
   ```

### Loading Strategy

**File:** `includes/class-uad-core.php:66-95`

```php
// CSS in header
wp_enqueue_style('uad-styles', ..., [], '1.2.0');

// JS in footer (better page load performance)
wp_enqueue_script('uad-scripts', ..., ['uad-chart'], '1.2.0', true);
```

### Chart.js Optimization

```javascript
// Use efficient aspect ratio
aspectRatio: 2.5,  // Instead of fixed height

// Disable animations for large datasets (future)
animation: {
    duration: totalRows > 100 ? 0 : 1000
}
```

---

For more information:
- [Architecture Overview](./ARCHITECTURE.md)
- [Customization Guide](./CUSTOMIZATION.md)
- [Troubleshooting](./TROUBLESHOOTING.md)
