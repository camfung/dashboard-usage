# Installation Guide

This guide covers the complete installation process for the User Activity Dashboard plugin.

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installing Composer](#installing-composer)
3. [Plugin Installation](#plugin-installation)
4. [WordPress Configuration](#wordpress-configuration)
5. [Verifying Installation](#verifying-installation)
6. [Development Setup](#development-setup)

## System Requirements

### Minimum Requirements

- **PHP**: 7.4 or higher
- **WordPress**: 6.8.3 or higher
- **Memory**: 64MB minimum (128MB recommended)
- **Disk Space**: 10MB minimum

### PHP Extensions Required

- `curl` - For API requests
- `json` - For JSON parsing
- `mbstring` - For string handling
- `openssl` - For secure connections

### Checking PHP Version

```bash
php -v
```

### Checking PHP Extensions

```bash
php -m | grep -E "curl|json|mbstring|openssl"
```

## Installing Composer

Composer is required for managing PHP dependencies.

### Option 1: Global Installation (Recommended)

**macOS/Linux:**
```bash
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
chmod +x /usr/local/bin/composer
```

**Windows:**
Download and run the installer from https://getcomposer.org/

**Verify installation:**
```bash
composer --version
```

### Option 2: Local Installation (Project-specific)

If you can't install Composer globally, download it to the project directory:

```bash
cd /path/to/wp-content/plugins/dashboard-usage
curl -sS https://getcomposer.org/installer | php
```

Then use it with:
```bash
php composer.phar install
```

## Plugin Installation

### Step 1: Copy Plugin Files

Copy the entire `dashboard-usage` directory to your WordPress plugins folder:

```bash
# Example path
cp -r dashboard-usage /path/to/wordpress/wp-content/plugins/
```

Your structure should look like:
```
wp-content/
└── plugins/
    └── dashboard-usage/
        ├── assets/
        ├── includes/
        ├── lib/
        ├── templates/
        ├── composer.json
        └── user-activity-dashboard.php
```

### Step 2: Install Dependencies

Navigate to the plugin directory and install PHP dependencies:

```bash
cd /path/to/wp-content/plugins/dashboard-usage

# If Composer is installed globally
composer install

# If using local composer.phar
php composer.phar install
```

This will create a `vendor/` directory with all required packages:
- Guzzle HTTP Client
- PHPUnit (for development)

### Step 3: Verify File Structure

After installation, verify these directories exist:

```
dashboard-usage/
├── vendor/           # ✓ Created by composer install
│   ├── autoload.php
│   └── guzzlehttp/
├── assets/
├── includes/
├── lib/
└── templates/
```

### Step 4: Set File Permissions

Ensure proper permissions (especially on Linux/Unix):

```bash
# Set directory permissions
find dashboard-usage -type d -exec chmod 755 {} \;

# Set file permissions
find dashboard-usage -type f -exec chmod 644 {} \;
```

## WordPress Configuration

### Step 1: Activate the Plugin

1. Log in to WordPress admin panel
2. Navigate to **Plugins** → **Installed Plugins**
3. Find "User Activity Dashboard"
4. Click **Activate**

### Step 2: Configure User Settings (Optional)

The plugin automatically uses the current logged-in user's ID. To customize:

**Using WordPress hooks in your theme's `functions.php`:**

```php
// Override default user ID
add_filter('uad_default_user_id', function($default) {
    return 125; // Your preferred default user ID
});

// Override default days
add_filter('uad_default_days', function($default) {
    return 60; // Default to 60 days instead of 30
});
```

### Step 3: Add Shortcode to a Page

1. Create a new page or edit an existing one
2. Add the shortcode:

```
[user_activity_dashboard]
```

**With custom attributes:**

```
[user_activity_dashboard user_id="125" days="90" show_chart="true" show_table="true"]
```

### Step 4: Publish and View

1. Publish the page
2. View the page on the frontend
3. You should see the Activity Dashboard

## Verifying Installation

### Check 1: Plugin Activation

In WordPress admin:
- **Plugins** → **Installed Plugins**
- "User Activity Dashboard" should show as **Active**

### Check 2: Shortcode Output

Visit the page with the shortcode. You should see:
- Dashboard header with title
- Date picker controls
- Summary cards (Total Days, Total Hits, Total Cost, Balance)
- Chart (if `show_chart="true"`)
- Table with pagination (if `show_table="true"`)

### Check 3: JavaScript Console

Open browser developer tools (F12) and check the console:
- No JavaScript errors
- Chart.js should load successfully

### Check 4: API Connection

The dashboard will attempt to fetch data from the API. If successful:
- Data appears in the table
- Chart displays properly
- No error messages

**If you see an error:**
- Check the error message
- Verify API credentials are configured
- See [Troubleshooting Guide](./TROUBLESHOOTING.md)

### Check 5: Pagination

If you have more than 10 days of data:
- Pagination controls should appear
- Clicking page numbers should change displayed rows
- "Showing X-Y of Z entries" should update

### Check 6: Date Picker

- Select a start date and end date
- Click "Update"
- Page should reload with new date range
- URL should include `?uad_start_date=...&uad_end_date=...`

## Development Setup

For developers who want to contribute or customize:

### Step 1: Clone the Repository

```bash
git clone https://github.com/yourusername/dashboard-usage.git
cd dashboard-usage
```

### Step 2: Install All Dependencies

```bash
# Install PHP dependencies (including dev dependencies)
composer install

# PHPUnit and other dev tools are now available
```

### Step 3: Configure Testing

Copy the PHPUnit configuration:

```bash
# The phpunit.xml file should already exist
cat phpunit.xml
```

### Step 4: Run Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific test
./vendor/bin/phpunit tests/UserActivityClientTest.php

# Run with coverage (requires xdebug)
./vendor/bin/phpunit --coverage-html coverage/
```

### Step 5: Set Up Version Control

```bash
# Initialize git (if not already done)
git init

# Create .gitignore
cat > .gitignore << 'EOF'
/vendor/
/node_modules/
.DS_Store
*.log
coverage/
EOF

# Make initial commit
git add .
git commit -m "Initial commit"
```

### Step 6: Development Tools (Optional)

Install additional tools for development:

```bash
# PHP CodeSniffer (coding standards)
composer require --dev squizlabs/php_codesniffer

# PHP CS Fixer (auto-formatting)
composer require --dev friendsofphp/php-cs-fixer
```

## Environment-Specific Setup

### Local Development (MAMP/XAMPP/Local WP)

1. Install WordPress locally
2. Follow standard plugin installation steps
3. Use local URLs for testing
4. Enable WordPress debug mode in `wp-config.php`:

```php
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', false);
```

### Staging Environment

1. Deploy WordPress to staging server
2. Copy plugin files via FTP/SSH
3. SSH into server and run `composer install --no-dev`
4. Activate plugin through WordPress admin
5. Test thoroughly before production

### Production Environment

1. Create a backup of your WordPress site
2. Deploy plugin files
3. SSH into server:
   ```bash
   cd /path/to/wp-content/plugins/dashboard-usage
   composer install --no-dev --optimize-autoloader
   ```
4. Activate plugin
5. Monitor error logs
6. Test all functionality

## Updating the Plugin

### Manual Update

1. Deactivate the plugin in WordPress
2. Backup the current plugin directory
3. Replace files with new version
4. Run `composer install` (or `php composer.phar install`)
5. Reactivate the plugin
6. Clear any caching

### Via Git

```bash
cd /path/to/wp-content/plugins/dashboard-usage
git pull origin main
composer install
```

## Uninstalling

### Complete Removal

1. Deactivate the plugin in WordPress admin
2. Delete the plugin through WordPress admin, OR:

```bash
rm -rf /path/to/wp-content/plugins/dashboard-usage
```

3. (Optional) Remove database entries if any custom settings were saved

### Keeping Settings

If you want to preserve settings for future reinstallation:
- Only deactivate the plugin
- Don't delete the plugin files

## Common Installation Issues

### Issue: "Composer command not found"

**Solution:** Install Composer (see [Installing Composer](#installing-composer))

### Issue: "vendor/autoload.php not found"

**Solution:** Run `composer install` in the plugin directory

### Issue: "PHP version incompatible"

**Solution:** Upgrade PHP to 7.4 or higher, or use a compatible hosting environment

### Issue: "Memory limit exceeded"

**Solution:** Increase PHP memory limit in `php.ini` or `wp-config.php`:
```php
define('WP_MEMORY_LIMIT', '128M');
```

### Issue: "Chart not displaying"

**Solution:**
- Check browser console for JavaScript errors
- Ensure Chart.js is loaded (check network tab)
- Verify `show_chart="true"` in shortcode

### Issue: "No data appearing"

**Solution:**
- Verify API connection
- Check API credentials
- Review WordPress error logs
- See [Troubleshooting Guide](./TROUBLESHOOTING.md)

## Next Steps

After successful installation:

1. Read the [Architecture Overview](./ARCHITECTURE.md) to understand the system
2. Review [Frontend Development](./FRONTEND.md) for customization options
3. Check [API Client Documentation](./API_CLIENT.md) for API details
4. Explore [Customization Guide](./CUSTOMIZATION.md) for theming and configuration

---

**Need help?** See the [Troubleshooting Guide](./TROUBLESHOOTING.md) or review the error logs in WordPress.
