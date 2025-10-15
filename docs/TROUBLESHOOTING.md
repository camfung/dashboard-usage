# Troubleshooting Guide

Common issues and solutions for the User Activity Dashboard plugin.

## Table of Contents

1. [Installation Issues](#installation-issues)
2. [Display Issues](#display-issues)
3. [Data Loading Issues](#data-loading-issues)
4. [Chart Issues](#chart-issues)
5. [Pagination Issues](#pagination-issues)
6. [Date Picker Issues](#date-picker-issues)
7. [Performance Issues](#performance-issues)
8. [WordPress Integration Issues](#wordpress-integration-issues)
9. [Debugging Techniques](#debugging-techniques)
10. [Error Messages Reference](#error-messages-reference)

## Installation Issues

### Issue: "Composer command not found"

**Symptoms:**
```bash
$ composer install
bash: composer: command not found
```

**Solution:**

**Option 1: Install Composer globally**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

**Option 2: Use local composer.phar**
```bash
cd /path/to/dashboard-usage
curl -sS https://getcomposer.org/installer | php
php composer.phar install
```

---

### Issue: "vendor/autoload.php not found"

**Symptoms:**
```
Warning: require_once(vendor/autoload.php): failed to open stream: No such file or directory
```

**Cause:** Composer dependencies not installed

**Solution:**
```bash
cd /path/to/wp-content/plugins/dashboard-usage
composer install
```

Or:
```bash
php composer.phar install
```

**Verify:**
```bash
ls -la vendor/autoload.php
```

---

### Issue: "PHP version incompatible"

**Symptoms:**
```
Fatal error: Composer detected issues in your platform: Your Composer dependencies require a PHP version ">= 7.4.0"
```

**Solution:**

**Check PHP version:**
```bash
php -v
```

**If < 7.4, upgrade PHP:**

**Ubuntu/Debian:**
```bash
sudo apt-get update
sudo apt-get install php7.4
```

**macOS (Homebrew):**
```bash
brew install php@7.4
```

**cPanel/WHM:**
- Use MultiPHP Manager
- Select PHP 7.4 or higher

---

### Issue: "Memory limit exceeded"

**Symptoms:**
```
Fatal error: Allowed memory size of 33554432 bytes exhausted
```

**Solution:**

**Increase PHP memory limit:**

**Option 1: wp-config.php**
```php
define('WP_MEMORY_LIMIT', '128M');
```

**Option 2: php.ini**
```ini
memory_limit = 128M
```

**Option 3: .htaccess**
```apache
php_value memory_limit 128M
```

---

## Display Issues

### Issue: Shortcode shows as plain text

**Symptoms:**
```
Page displays: [user_activity_dashboard]
```

**Causes:**
1. Plugin not activated
2. Shortcode typo
3. Text editor formatting

**Solutions:**

**1. Verify plugin is active:**
- WordPress Admin → Plugins → Installed Plugins
- Check "User Activity Dashboard" is Active

**2. Check shortcode spelling:**
```
Correct:   [user_activity_dashboard]
Incorrect: [user-activity-dashboard]
Incorrect: [user_activity_dashbaord]
```

**3. Use Text editor (not Visual):**
- In WordPress page editor
- Click "Text" tab (not "Visual")
- Paste shortcode
- Ensure no extra formatting

---

### Issue: Dashboard appears broken/unstyled

**Symptoms:**
- No colors
- Elements stacked incorrectly
- Looks like plain HTML

**Cause:** CSS not loading

**Solutions:**

**1. Check browser console (F12):**
```
Look for: Failed to load resource: .../uad-styles.css
```

**2. Verify file exists:**
```bash
ls -la assets/css/uad-styles.css
```

**3. Check file permissions:**
```bash
chmod 644 assets/css/uad-styles.css
```

**4. Clear cache:**
- Browser cache (Ctrl+Shift+R / Cmd+Shift+R)
- WordPress cache (if using caching plugin)
- CDN cache (if using CDN)

**5. Check for theme conflicts:**
```php
// Add to functions.php temporarily
add_action('wp_enqueue_scripts', function() {
    wp_dequeue_style('theme-style-that-conflicts');
}, 100);
```

---

### Issue: Layout issues on mobile

**Symptoms:**
- Horizontal scrolling
- Elements too small
- Text overlapping

**Solutions:**

**1. Check viewport meta tag:**
```html
<!-- Should be in your theme's header.php -->
<meta name="viewport" content="width=device-width, initial-scale=1">
```

**2. Test responsive styles:**
```bash
# Open browser DevTools (F12)
# Toggle device toolbar (Ctrl+Shift+M / Cmd+Shift+M)
# Test different screen sizes
```

**3. Override mobile styles if needed:**
```css
/* Add to theme's style.css */
@media screen and (max-width: 768px) {
    .uad-dashboard {
        font-size: 12px;
    }
    .uad-table {
        font-size: 10px;
    }
}
```

---

## Data Loading Issues

### Issue: "No activity data found"

**Symptoms:**
```
Dashboard displays: No activity data found for the selected period.
```

**Causes:**
1. No data in API for user/dates
2. API connection failure
3. Invalid user ID
4. Date range has no data

**Solutions:**

**1. Verify user has data:**
- Try different user ID
- Try different date range
- Check API directly

**2. Check API connection:**
```php
// Add to functions.php temporarily
add_action('init', function() {
    if (isset($_GET['test_api'])) {
        $client = new \TpBloomland\UserActivity\UserActivityClient();
        try {
            $result = $client->getUserActivity(125);
            echo '<pre>';
            print_r($result);
            echo '</pre>';
            exit;
        } catch (Exception $e) {
            echo 'Error: ' . $e->getMessage();
            exit;
        }
    }
});

// Visit: yoursite.com/?test_api=1
```

**3. Check user ID:**
```
Default user ID is current logged-in user
Fallback is 125 if not logged in
```

**4. Try broader date range:**
```
[user_activity_dashboard days="365"]
```

---

### Issue: API errors

**Symptoms:**
```
API Error: Connection timed out
API Error: Invalid JSON response
API Error: 404 Not Found
```

**Solutions:**

**Connection timeout:**
```php
// Increase timeout in UserActivityClient.php
'timeout' => 60,  // Changed from 30
```

**Invalid JSON:**
- Check API endpoint is correct
- Verify API is returning JSON
- Check for HTML error pages

**404 Not Found:**
- Verify API URL is correct
- Check user ID exists
- Verify API endpoint exists

**Debug API requests:**
```php
// Add to UserActivityClient.php temporarily
error_log('API Request: ' . $endpoint);
error_log('API Response: ' . $body);
```

---

### Issue: Slow data loading

**Symptoms:**
- Page takes 10+ seconds to load
- White screen before display
- Timeout errors

**Solutions:**

**1. Implement caching:**
```php
// In class-uad-shortcode.php
private function fetch_activity_data($user_id, $start_date, $end_date) {
    $cache_key = "uad_{$user_id}_{$start_date}_{$end_date}";

    // Check cache
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        return $cached;
    }

    // Fetch from API
    try {
        $client = new UserActivityClient();
        $response = $client->getUserActivity($user_id, $start_date, $end_date);

        // Cache for 15 minutes
        set_transient($cache_key, $response, 15 * MINUTE_IN_SECONDS);

        return $response;
    } catch (ApiException $e) {
        return new WP_Error('api_error', $e->getMessage());
    }
}
```

**2. Reduce date range:**
```
Instead of: days="365"
Try: days="30"
```

**3. Check server resources:**
```bash
# Check PHP memory
php -i | grep memory_limit

# Check server load
top
```

---

## Chart Issues

### Issue: Chart not displaying

**Symptoms:**
- Empty white space where chart should be
- Canvas element visible but empty
- No errors in console

**Solutions:**

**1. Verify Chart.js loaded:**
```javascript
// Open browser console (F12)
console.log(typeof Chart);
// Should output: "function"
// If "undefined", Chart.js not loaded
```

**2. Check chart data:**
```javascript
// In browser console
console.log(window.uadChartData);
// Should show: {labels: Array, hits: Array}
// If undefined, data not passed from PHP
```

**3. Check canvas element:**
```javascript
// In browser console
document.getElementById('uad-activity-chart');
// Should return: <canvas id="uad-activity-chart">
// If null, element not in DOM
```

**4. Verify show_chart attribute:**
```
Shortcode should have: show_chart="true"
Not: show_chart="false"
```

**5. Check for JavaScript errors:**
```
Open console (F12)
Look for red errors
Fix any errors before chart initialization
```

---

### Issue: Chart displays incorrectly

**Symptoms:**
- Wrong data values
- Missing data points
- Incorrect colors
- Layout issues

**Solutions:**

**Wrong data:**
```php
// Check PHP data preparation in dashboard.php
<?php if ($show_chart && !empty($activities)) : ?>
<script>
    // Debug data
    console.log('Activities:', <?php echo json_encode($activities); ?>);

    window.uadChartData = {
        labels: <?php echo json_encode(array_column($activities, 'date')); ?>,
        hits: <?php echo json_encode(array_column($activities, 'totalHits')); ?>
    };
</script>
<?php endif; ?>
```

**Missing data points:**
- Verify all dates have data
- Check for null values
- Ensure array lengths match

**Incorrect colors:**
```javascript
// Check color definitions in uad-scripts.js
const colors = {
    babyPowder: '#FFFFFA',
    bleuDeFrance: '#3083DC',
    jet: '#2D2D2A',
    selectiveYellow: '#FFB30F',
    poppy: '#DF2935'
};
```

**Layout issues:**
```css
/* Check CSS for chart container */
.uad-chart-container {
    max-width: 100%;
    overflow: hidden;
}

#uad-activity-chart {
    max-height: 400px;
}
```

---

## Pagination Issues

### Issue: Pagination not appearing

**Symptoms:**
- No pagination buttons shown
- All rows visible at once

**Causes:**
1. Less than 11 rows (pagination not needed)
2. JavaScript not loaded
3. Element ID mismatch

**Solutions:**

**1. Check row count:**
```javascript
// In browser console
document.querySelectorAll('.uad-table-row').length;
// If < 11, pagination hidden by design
```

**2. Verify JavaScript loaded:**
```javascript
// In browser console
typeof initPagination
// Should be "function"
```

**3. Check element IDs:**
```html
<!-- Verify these exist -->
<div id="uad-page-numbers"></div>
<button id="uad-first-page"></button>
<button id="uad-prev-page"></button>
<button id="uad-next-page"></button>
<button id="uad-last-page"></button>
```

---

### Issue: Pagination buttons not working

**Symptoms:**
- Clicking buttons does nothing
- Page numbers don't change display

**Solutions:**

**1. Check for JavaScript errors:**
```
Open console (F12)
Look for errors
```

**2. Verify event listeners attached:**
```javascript
// In browser console
$0  // Select button in Elements tab first
getEventListeners($0)
// Should show click listener
```

**3. Debug showPage function:**
```javascript
// Add to uad-scripts.js temporarily
function showPage(page) {
    console.log('showPage called with:', page);
    // ... rest of function
}
```

---

## Date Picker Issues

### Issue: Date picker not appearing

**Symptoms:**
- No date inputs visible
- Date picker section missing

**Solutions:**

**1. Check HTML:**
```html
<!-- Verify this exists in page source -->
<div class="uad-date-picker">
    <input type="date" id="uad-start-date" ...>
    <input type="date" id="uad-end-date" ...>
</div>
```

**2. Check CSS:**
```css
/* Ensure not hidden */
.uad-date-picker {
    display: flex; /* Not display: none */
}
```

**3. Browser support:**
```
HTML5 date inputs supported in:
- Chrome, Edge, Safari, Firefox
- Not IE 11 (shows text input instead)
```

---

### Issue: Date validation not working

**Symptoms:**
- Can select future dates
- Can select end before start
- Invalid dates accepted

**Solutions:**

**1. Verify JavaScript initialization:**
```javascript
// In browser console
const startInput = document.getElementById('uad-start-date');
console.log(startInput.max);  // Should be today's date
```

**2. Check validation logic:**
```javascript
// In uad-scripts.js
if (!startDate || !endDate) {
    alert('Please select both start and end dates');
    return;
}

if (startDate > endDate) {
    alert('Start date must be before end date');
    return;
}
```

**3. Test manually:**
```
1. Select start date: 2025-10-14
2. Check end date min: Should be 2025-10-14
3. Select end date: 2025-10-01 (should be invalid)
4. Click Update: Should show alert
```

---

### Issue: Update button not working

**Symptoms:**
- Clicking Update does nothing
- Page doesn't reload
- URL doesn't change

**Solutions:**

**1. Check for JavaScript errors:**
```
Console should show any errors
```

**2. Debug button click:**
```javascript
// In browser console
document.getElementById('uad-update-dates').onclick = function() {
    console.log('Update clicked');
    console.log('Start:', document.getElementById('uad-start-date').value);
    console.log('End:', document.getElementById('uad-end-date').value);
};
```

**3. Verify URL construction:**
```javascript
// Should see in code
const url = new URL(window.location.href);
url.searchParams.set('uad_start_date', startDate);
url.searchParams.set('uad_end_date', endDate);
console.log(url.toString());  // Debug output
```

---

## Performance Issues

### Issue: Page loads slowly

**Solutions:**

**1. Enable caching:** (see Data Loading Issues)

**2. Optimize asset loading:**
```php
// Load JS in footer
wp_enqueue_script('uad-scripts', ..., [], '1.2.0', true);  // true = footer
```

**3. Reduce data:**
```
Use shorter date ranges
Limit initial view to 30 days
```

**4. Check server:**
```bash
# Test API response time
time curl -X GET "https://api.example.com/user-activity-summary/125"
```

---

## WordPress Integration Issues

### Issue: Plugin conflicts

**Symptoms:**
- Dashboard breaks after activating another plugin
- JavaScript errors appear
- Styles override each other

**Solutions:**

**1. Identify conflicting plugin:**
```
1. Deactivate all other plugins
2. Reactivate one at a time
3. Test dashboard after each
4. Note which plugin causes issue
```

**2. Fix script conflicts:**
```php
// In class-uad-core.php
wp_enqueue_script('uad-scripts', ..., ['jquery'], '1.2.0', true);
// Add jQuery dependency if needed
```

**3. Fix style conflicts:**
```css
/* Use more specific selectors */
.uad-dashboard .uad-table {
    /* Styles */
}

/* Or use !important as last resort */
.uad-dashboard {
    color: #2D2D2A !important;
}
```

---

### Issue: Permission errors

**Symptoms:**
```
You do not have sufficient permissions to access this page.
```

**Solutions:**

**1. Check user capabilities:**
```php
// Allow editors to view
if (current_user_can('edit_posts')) {
    // Show dashboard
}
```

**2. Add capability check:**
```php
// In shortcode handler
if (!is_user_logged_in()) {
    return '<p>Please log in to view this page.</p>';
}
```

---

## Debugging Techniques

### Enable WordPress Debug Mode

**File:** `wp-config.php`

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
@ini_set('display_errors', 0);
```

**View debug log:**
```bash
tail -f wp-content/debug.log
```

### Browser Developer Tools

**Open DevTools:** F12 (Windows/Linux) or Cmd+Option+I (Mac)

**Console Tab:**
- View JavaScript errors
- Run debug commands
- Test functions

**Network Tab:**
- Check if assets load
- View API requests
- Check response times

**Elements Tab:**
- Inspect HTML structure
- Check applied styles
- Verify data attributes

### PHP Error Logging

**Add to plugin file:**
```php
error_log('UAD Debug: ' . print_r($data, true));
```

**View PHP error log:**
```bash
tail -f /var/log/php_errors.log
```

### JavaScript Console Logging

**Add to uad-scripts.js:**
```javascript
console.log('Chart data:', window.uadChartData);
console.log('Current page:', currentPage);
console.log('Total rows:', totalRows);
```

### Test API Directly

```bash
# Using curl
curl -X GET "https://api.example.com/dev/user-activity-summary/125?startDate=2025-09-01&endDate=2025-09-30"

# Using wget
wget -O response.json "https://api.example.com/dev/user-activity-summary/125"

# View response
cat response.json | python -m json.tool
```

## Error Messages Reference

### Common Error Messages

| Error | Cause | Solution |
|-------|-------|----------|
| "vendor/autoload.php not found" | Dependencies not installed | Run `composer install` |
| "Class UserActivityClient not found" | Autoloader not loaded | Check require statement |
| "API Error: Connection timed out" | Network/API slow | Increase timeout, check network |
| "Invalid JSON response" | API returned HTML/error | Check API endpoint, credentials |
| "Memory limit exceeded" | Large dataset | Increase PHP memory limit |
| "Chart is not defined" | Chart.js not loaded | Verify Chart.js enqueued |
| "Cannot read property" | JavaScript initialization error | Check element exists |
| "Permission denied" | File permissions wrong | chmod 644 files, 755 dirs |

---

For more help:
- [Installation Guide](./INSTALLATION.md)
- [Architecture Overview](./ARCHITECTURE.md)
- [Testing Guide](./TESTING.md)
- GitHub Issues: https://github.com/yourrepo/dashboard-usage/issues
