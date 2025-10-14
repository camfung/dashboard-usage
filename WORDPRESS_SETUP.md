# WordPress Plugin Setup - User Activity Dashboard

## Directory Structure

The `dashboard-usage` directory is now **a complete WordPress plugin**. All plugin files are direct children of this directory:

```
dashboard-usage/                      ← THIS IS THE WORDPRESS PLUGIN!
├── user-activity-dashboard.php      ← Main plugin file
├── includes/                         ← Plugin classes
│   ├── class-uad-core.php
│   └── class-uad-shortcode.php
├── templates/                        ← HTML templates
│   └── dashboard.php
├── assets/                           ← CSS & JavaScript
│   ├── css/uad-styles.css
│   └── js/uad-scripts.js
├── lib/                              ← API client library
│   └── UserActivity/
│       ├── UserActivityClient.php
│       └── Exceptions/
├── vendor/                           ← Composer dependencies
├── composer.json                     ← Dependency definition
├── src/                              ← Original source (not used by plugin)
├── tests/                            ← PHPUnit tests (not used by plugin)
└── README.md                         ← Documentation
```

## Installation (Super Simple!)

### Step 1: Copy to WordPress

Copy the entire `dashboard-usage` directory to WordPress plugins:

```bash
cp -r dashboard-usage /path/to/wordpress/wp-content/plugins/
```

**That's it!** The plugin files are already in the right place.

### Step 2: Activate in WordPress

1. Go to WordPress Admin
2. Navigate to **Plugins** → **Installed Plugins**
3. Find **"User Activity Dashboard"**
4. Click **Activate**

### Step 3: Use the Shortcode

Add to any page or post:

```
[user_activity_dashboard]
```

## How It Works

WordPress will recognize `dashboard-usage` as a plugin because:

1. ✅ It's a direct child of `wp-content/plugins/`
2. ✅ It contains `user-activity-dashboard.php` with proper plugin headers
3. ✅ All dependencies are included in `vendor/`
4. ✅ All plugin files (includes, templates, assets) are in the same directory

## File Locations in WordPress

After copying:

```
/path/to/wordpress/
└── wp-content/
    └── plugins/
        └── dashboard-usage/          ← Your plugin
            ├── user-activity-dashboard.php  ← WordPress finds this!
            ├── includes/
            ├── templates/
            ├── assets/
            ├── lib/
            └── vendor/
```

## What's Included

### Core Plugin Files
- **user-activity-dashboard.php** - Main plugin file with WordPress headers
- **includes/** - Plugin classes (core, shortcode handler)
- **templates/** - HTML template for dashboard display
- **assets/** - Styled CSS and Chart.js integration

### API Client
- **lib/UserActivity/** - Complete API client library
- **vendor/** - Guzzle HTTP client and dependencies

### Development Files (Optional)
- **src/** - Original API client source
- **tests/** - PHPUnit test suite
- **composer.json** - Dependency management
- **phpunit.xml** - Test configuration

## Shortcode Usage

### Default Dashboard
```
[user_activity_dashboard]
```
Shows: User 125, Last 30 days, Chart + Table

### Custom User ID
```
[user_activity_dashboard user_id="200"]
```

### Custom Date Range
```
[user_activity_dashboard days="60"]
```

### Chart Only
```
[user_activity_dashboard show_table="false"]
```

### Table Only
```
[user_activity_dashboard show_chart="false"]
```

### All Options
```
[user_activity_dashboard user_id="125" days="90" show_chart="true" show_table="true"]
```

## Verification

### Before WordPress Installation

Test that everything is set up correctly:

```bash
cd dashboard-usage
php test-wordpress-plugin.php
```

Expected output:
```
✓ ALL TESTS PASSED!
Plugin is ready for WordPress!
```

### After WordPress Installation

1. Check WordPress recognizes it:
   - Go to **Plugins** → **Installed Plugins**
   - Should see "User Activity Dashboard"

2. Activate and test:
   - Click **Activate**
   - Create a test page
   - Add shortcode: `[user_activity_dashboard]`
   - View the page

## Troubleshooting

### Plugin Not Showing in WordPress

**Check:** Is the directory directly in `wp-content/plugins/`?

```bash
ls /path/to/wordpress/wp-content/plugins/
```

Should show:
```
dashboard-usage/
```

### Activation Error About Vendor

**Error:** "Please run composer install"

**Solution:**
```bash
cd /path/to/wordpress/wp-content/plugins/dashboard-usage
composer install
```

### No Data Showing

**Solutions:**
- Use test user: `[user_activity_dashboard user_id="125"]`
- Increase range: `[user_activity_dashboard days="90"]`
- Check API access from your server

### Chart Not Loading

**Check browser console** (F12) for JavaScript errors.

**Common fixes:**
- Disable ad blockers (they may block Chart.js CDN)
- Clear browser cache
- Check for JavaScript conflicts with theme

## Features

### Dashboard Components

1. **Summary Cards**
   - Total Days
   - Total Hits
   - Total Cost
   - Current Balance

2. **Interactive Chart**
   - Dual Y-axis (Hits vs Balance)
   - Area fill with gradients
   - Responsive design
   - Hover tooltips

3. **Activity Table**
   - Date
   - NON-SUI (placeholder)
   - SUI (placeholder)
   - Total Hits
   - Cost
   - Other Services (placeholder)
   - Total (placeholder)
   - Balance (color-coded)

### Styling

Custom color palette:
- **Baby Powder** (#FFFFFA) - Backgrounds
- **Bleu de France** (#3083DC) - Primary accents
- **Jet** (#2D2D2A) - Text
- **Selective Yellow** (#FFB30F) - Highlights
- **Poppy** (#DF2935) - Negative values

## Customization

### Change Default User ID

Add to your theme's `functions.php`:

```php
add_filter('uad_default_user_id', function($user_id) {
    return 200;
});
```

### Change Default Days

```php
add_filter('uad_default_days', function($days) {
    return 60;
});
```

### Custom CSS

Override styles in your theme:

```css
.uad-dashboard {
    /* Your custom styles */
}
```

## Updates

When updating the plugin:

1. Deactivate in WordPress
2. Replace `dashboard-usage` directory
3. Reactivate

Or use version control and pull updates:

```bash
cd /path/to/wordpress/wp-content/plugins/dashboard-usage
git pull
composer install
```

## Production Tips

### Optimize for Production

1. **Install without dev dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

2. **Enable caching** (use a caching plugin)

3. **Limit date ranges** for better performance

4. **Use a CDN** for static assets (optional)

## Support Files

- **README.md** - Plugin and API client documentation
- **USER_ACTIVITY_SUMMARY_API.md** - Complete API reference
- **postman_collection.json** - API testing collection
- **test-wordpress-plugin.php** - Plugin validation script
- **test.php** - API client test script
- **example.php** - API usage examples

---

## Quick Reference

**Installation:**
```bash
cp -r dashboard-usage /path/to/wordpress/wp-content/plugins/
```

**Activation:**
WordPress Admin → Plugins → Activate "User Activity Dashboard"

**Usage:**
```
[user_activity_dashboard]
```

**Test:**
```bash
php test-wordpress-plugin.php
```

---

**Plugin Version:** 1.0.0
**WordPress Version:** 6.8.3+
**PHP Version:** 7.4+
