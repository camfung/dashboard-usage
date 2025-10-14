# WordPress Plugin Installation Guide

## Quick Start

Follow these steps to install and use the User Activity Dashboard plugin in WordPress.

## Prerequisites

- WordPress 5.0+ installed
- PHP 7.4+ installed
- Access to WordPress plugins directory
- Composer installed (for dependencies)

## Installation Steps

### 1. Install PHP Dependencies

First, ensure the PHP client dependencies are installed:

```bash
cd /path/to/dashboard-usage
composer install
```

This installs:
- Guzzle HTTP Client
- PHPUnit (dev dependency)
- All required dependencies

### 2. Copy Plugin to WordPress

Copy the plugin folder to your WordPress plugins directory:

```bash
# For standard WordPress installation
cp -r user-activity-dashboard-plugin /path/to/wordpress/wp-content/plugins/

# Example on macOS with MAMP
cp -r user-activity-dashboard-plugin /Applications/MAMP/htdocs/wordpress/wp-content/plugins/

# Example on Linux
cp -r user-activity-dashboard-plugin /var/www/html/wordpress/wp-content/plugins/
```

### 3. Activate the Plugin

1. Log in to your WordPress admin dashboard
2. Navigate to **Plugins** → **Installed Plugins**
3. Find "User Activity Dashboard" in the list
4. Click **Activate**

If you see an error about composer dependencies, make sure you ran `composer install` in step 1.

### 4. Add Shortcode to a Page

1. Create a new page or edit an existing one
2. Add the shortcode:

```
[user_activity_dashboard]
```

3. Publish or update the page
4. View the page to see your dashboard

## Usage Examples

### Basic Usage

```
[user_activity_dashboard]
```

Shows the default dashboard:
- User ID: 125
- Last 30 days
- Both chart and table

### Custom User ID

```
[user_activity_dashboard user_id="200"]
```

### Custom Date Range

```
[user_activity_dashboard days="60"]
```

Shows last 60 days of activity.

### Chart Only

```
[user_activity_dashboard show_table="false"]
```

### Table Only

```
[user_activity_dashboard show_chart="false"]
```

### Combined Attributes

```
[user_activity_dashboard user_id="125" days="90" show_chart="true" show_table="true"]
```

## File Structure

After installation, your plugin directory should look like this:

```
wp-content/plugins/user-activity-dashboard-plugin/
├── assets/
│   ├── css/
│   │   └── uad-styles.css
│   └── js/
│       └── uad-scripts.js
├── includes/
│   ├── class-uad-core.php
│   └── class-uad-shortcode.php
├── templates/
│   └── dashboard.php
├── user-activity-dashboard.php
├── README.md
└── demo.html
```

## Testing Without WordPress

You can preview the dashboard design without WordPress:

```bash
cd user-activity-dashboard-plugin
php -S localhost:8000
```

Then open `http://localhost:8000/demo.html` in your browser.

## Troubleshooting

### Plugin Won't Activate

**Error Message:**
```
Please run "composer install" in the dashboard-usage directory before activating this plugin.
```

**Solution:**
```bash
cd /path/to/dashboard-usage
composer install
```

### No Data Showing

**Possible Issues:**

1. **Wrong User ID** - User 125 is the test user. Use `user_id` attribute to change.
2. **API Connection Failed** - Check if the API endpoint is accessible.
3. **No Activity in Date Range** - Try a different date range with the `days` attribute.

**Debug Steps:**

1. Check WordPress debug log:
   - Enable debug mode in `wp-config.php`:
     ```php
     define('WP_DEBUG', true);
     define('WP_DEBUG_LOG', true);
     ```
   - Check logs in `wp-content/debug.log`

2. Test the API directly:
   ```bash
   cd /path/to/dashboard-usage
   php test.php
   ```

### Chart Not Displaying

**Possible Issues:**

1. **Chart.js CDN Blocked** - Ad blockers may block the CDN
2. **JavaScript Error** - Check browser console for errors
3. **Theme Conflict** - Try with a default WordPress theme

**Solutions:**

1. Disable ad blockers
2. Check browser console (F12)
3. Temporarily switch to Twenty Twenty-One theme for testing

### Styling Issues

If the plugin looks unstyled:

1. **Clear Cache** - Clear WordPress cache and browser cache
2. **Check CSS Loading**:
   - View page source
   - Look for `uad-styles.css` in the `<head>`
3. **Theme Conflict** - Some themes override plugin styles

## Customization

### Change Default User ID

Add to your theme's `functions.php`:

```php
add_filter('uad_default_user_id', function($user_id) {
    return 200; // Your default user ID
});
```

### Change Default Days

```php
add_filter('uad_default_days', function($days) {
    return 60; // Show 60 days by default
});
```

### Override Styles

Add custom CSS to your theme:

```css
/* In your theme's style.css or custom CSS */
.uad-dashboard {
    border: 2px solid #000;
    /* Your custom styles */
}

.uad-table {
    font-size: 16px;
    /* Larger table text */
}
```

## Advanced Configuration

### Using in Templates

You can call the shortcode directly in PHP templates:

```php
<?php echo do_shortcode('[user_activity_dashboard user_id="125" days="30"]'); ?>
```

### Widget Area

Use the shortcode in a text widget or custom HTML widget.

### Gutenberg Block

The shortcode works in the Shortcode block in Gutenberg editor.

## Updating the Plugin

When updating:

1. Deactivate the plugin
2. Replace the plugin files
3. Run `composer install` if dependencies changed
4. Reactivate the plugin

## Uninstallation

To remove the plugin:

1. Deactivate the plugin in WordPress
2. Delete the plugin from the Plugins page, or
3. Manually delete the folder:
   ```bash
   rm -rf /path/to/wordpress/wp-content/plugins/user-activity-dashboard-plugin
   ```

## Support

For issues:

1. Check this guide
2. Review `user-activity-dashboard-plugin/README.md`
3. Check API documentation in `../USER_ACTIVITY_SUMMARY_API.md`
4. Test the PHP client with `php test.php`

## Security Notes

- The plugin uses server-side rendering (no API keys exposed to frontend)
- User IDs are validated and sanitized
- All output is properly escaped for security
- No database writes (read-only plugin)

## Performance Tips

1. **Use Caching** - The plugin doesn't cache API responses. Consider using a caching plugin.
2. **Limit Date Range** - Smaller date ranges load faster
3. **CDN** - Chart.js loads from CDN for better performance

## Next Steps

After installation:

1. ✅ Test with default shortcode
2. ✅ Customize with attributes
3. ✅ Style to match your theme
4. ✅ Add to multiple pages if needed
5. ✅ Monitor API usage and performance

---

**Plugin Version:** 1.0.0
**Last Updated:** 2025-10-14
**WordPress Version:** 6.8.3
**PHP Version:** 7.4+
