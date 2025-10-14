# WordPress Installation Guide

## Installing the User Activity Dashboard Plugin

Since you're copying the entire `dashboard-usage` directory to your WordPress plugins folder, the plugin will be located at:

```
wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin/
```

However, **WordPress only recognizes plugins in the immediate subdirectories** of `wp-content/plugins/`, so you have two options:

## Option 1: Copy Just the Plugin (Recommended)

Copy only the plugin directory to WordPress:

```bash
# From your project directory
cp -r user-activity-dashboard-plugin /path/to/wordpress/wp-content/plugins/

# Example for MAMP
cp -r user-activity-dashboard-plugin /Applications/MAMP/htdocs/wordpress/wp-content/plugins/

# Example for standard Linux installation
cp -r user-activity-dashboard-plugin /var/www/html/wordpress/wp-content/plugins/
```

Then install dependencies in the plugin:

```bash
cd /path/to/wordpress/wp-content/plugins/user-activity-dashboard-plugin
composer install --no-dev
```

## Option 2: Copy Everything and Symlink

If you want to keep the entire `dashboard-usage` directory in plugins:

```bash
# Copy everything
cp -r /path/to/dashboard-usage /path/to/wordpress/wp-content/plugins/

# Create symlink so WordPress can find the plugin
cd /path/to/wordpress/wp-content/plugins/
ln -s dashboard-usage/user-activity-dashboard-plugin user-activity-dashboard-plugin
```

Then install dependencies:

```bash
cd /path/to/wordpress/wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin
composer install --no-dev
```

## What You Said: "Copy dashboard-usage to plugins"

If you copy the entire `dashboard-usage` directory, you'll need to do this:

```bash
# 1. Copy the entire directory
cp -r /path/to/dashboard-usage /path/to/wordpress/wp-content/plugins/

# 2. Install dependencies in the plugin subdirectory
cd /path/to/wordpress/wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin
composer install --no-dev

# 3. Create a symlink so WordPress recognizes it
cd /path/to/wordpress/wp-content/plugins/
ln -s dashboard-usage/user-activity-dashboard-plugin user-activity-dashboard-plugin
```

**OR** you can install it directly from within WordPress:

1. Go to WordPress Admin → Plugins → Add New
2. Upload a ZIP file
3. Navigate to `dashboard-usage/user-activity-dashboard-plugin`
4. Zip it: `zip -r user-activity-dashboard-plugin.zip user-activity-dashboard-plugin`
5. Upload and activate

## After Installation

1. **Activate the Plugin**
   - Go to WordPress Admin → Plugins
   - Find "User Activity Dashboard"
   - Click "Activate"

2. **Use the Shortcode**
   - Create or edit any page
   - Add the shortcode:
     ```
     [user_activity_dashboard]
     ```
   - Publish/Update the page
   - View the page to see your dashboard

## Plugin Structure (Self-Contained)

The plugin is now **completely self-contained** with its own dependencies:

```
user-activity-dashboard-plugin/
├── vendor/                         # Composer dependencies (Guzzle, etc.)
├── lib/
│   └── UserActivity/              # PHP API client code
│       ├── UserActivityClient.php
│       └── Exceptions/
│           └── ApiException.php
├── includes/                       # Plugin classes
├── templates/                      # HTML templates
├── assets/                         # CSS and JavaScript
├── composer.json                   # Dependencies definition
└── user-activity-dashboard.php    # Main plugin file
```

## Troubleshooting

### Plugin Not Showing in WordPress

**Problem:** Plugin doesn't appear in the plugins list.

**Solution:** WordPress needs the plugin in the **immediate subdirectory** of `wp-content/plugins/`.

Check your path:
- ✅ Good: `/wp-content/plugins/user-activity-dashboard-plugin/user-activity-dashboard.php`
- ❌ Bad: `/wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin/user-activity-dashboard.php`

If you copied the entire `dashboard-usage` folder, create a symlink as shown in Option 2 above.

### Activation Error: "Please run composer install"

**Problem:** Plugin shows error on activation about missing vendor directory.

**Solution:**
```bash
cd /path/to/wordpress/wp-content/plugins/user-activity-dashboard-plugin
composer install --no-dev
```

### No Data Showing

**Problem:** Dashboard shows "No activity data found."

**Solutions:**
1. Check user ID - Default is 125 (use shortcode attribute to change)
2. Try different date range: `[user_activity_dashboard days="90"]`
3. Check API is accessible from your server
4. Enable WordPress debug mode and check logs

## Shortcode Examples

### Basic (Default Settings)
```
[user_activity_dashboard]
```
Shows: User 125, Last 30 days, Chart + Table

### Custom User
```
[user_activity_dashboard user_id="200"]
```

### Custom Date Range
```
[user_activity_dashboard days="60"]
```
Shows last 60 days

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

## Testing Before WordPress

You can test the plugin works **before** installing in WordPress:

```bash
cd user-activity-dashboard-plugin
php test-plugin.php
```

This verifies:
- ✓ All files present
- ✓ Composer dependencies installed
- ✓ API client works
- ✓ Can fetch data

## Summary

**Simplest Installation:**

1. Copy plugin to WordPress:
   ```bash
   cp -r user-activity-dashboard-plugin /path/to/wordpress/wp-content/plugins/
   ```

2. Install dependencies:
   ```bash
   cd /path/to/wordpress/wp-content/plugins/user-activity-dashboard-plugin
   composer install --no-dev
   ```

3. Activate in WordPress Admin → Plugins

4. Use shortcode: `[user_activity_dashboard]`

**Done!** 🎉
