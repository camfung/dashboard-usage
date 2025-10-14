# User Activity Dashboard WordPress Plugin

A WordPress plugin that displays user activity data with interactive charts and detailed tables using the User Activity API client.

## Features

- **Server-side rendered** - All data fetching happens on the server
- **Shortcode-based** - Easy to embed anywhere with `[user_activity_dashboard]`
- **Interactive Chart.js visualization** - Beautiful dual-axis charts showing hits and balance
- **Detailed activity table** - Daily breakdown with all metrics
- **Responsive design** - Works on desktop, tablet, and mobile
- **Custom color palette** - Professionally designed with your brand colors
- **Configurable** - Control user ID, date range, and display options

## Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Composer (for installing dependencies)

## Installation

### Step 1: Install Dependencies

Before activating the plugin, you need to install the PHP dependencies:

```bash
cd /path/to/wordpress/wp-content/plugins/dashboard-usage
composer install
```

### Step 2: Copy Plugin to WordPress

Copy the `user-activity-dashboard-plugin` folder to your WordPress plugins directory:

```bash
cp -r user-activity-dashboard-plugin /path/to/wordpress/wp-content/plugins/
```

### Step 3: Activate Plugin

1. Log in to your WordPress admin panel
2. Navigate to **Plugins** → **Installed Plugins**
3. Find "User Activity Dashboard"
4. Click **Activate**

## Usage

### Basic Shortcode

Add this shortcode to any page or post:

```
[user_activity_dashboard]
```

This will display the dashboard with default settings:
- User ID: 125
- Date range: Last 30 days
- Shows both chart and table

### Shortcode Attributes

Customize the dashboard with these attributes:

```
[user_activity_dashboard user_id="125" days="60" show_chart="true" show_table="true"]
```

**Available Attributes:**

- `user_id` - User ID to fetch data for (default: 125)
- `days` - Number of days to show (default: 30)
- `show_chart` - Show the chart (true/false, default: true)
- `show_table` - Show the table (true/false, default: true)

### Examples

**Show only the chart:**
```
[user_activity_dashboard show_table="false"]
```

**Show only the table:**
```
[user_activity_dashboard show_chart="false"]
```

**Show last 90 days for user 200:**
```
[user_activity_dashboard user_id="200" days="90"]
```

## Customization

### Changing Default User ID

Add this to your theme's `functions.php`:

```php
add_filter('uad_default_user_id', function($user_id) {
    return 200; // Your default user ID
});
```

### Changing Default Days Range

```php
add_filter('uad_default_days', function($days) {
    return 60; // Show 60 days by default
});
```

### Custom CSS

Override styles by adding CSS to your theme:

```css
.uad-dashboard {
    /* Your custom styles */
}
```

## Color Palette

The plugin uses the following color scheme:

- **Baby Powder** (#FFFFFA) - Background
- **Bleu de France** (#3083DC) - Primary accents, balance line
- **Jet** (#2D2D2A) - Text, table header
- **Selective Yellow** (#FFB30F) - Highlights, hits line
- **Poppy** (#DF2935) - Negative balances, errors

## File Structure

```
user-activity-dashboard-plugin/
├── assets/
│   ├── css/
│   │   └── uad-styles.css          # Plugin styles
│   └── js/
│       └── uad-scripts.js          # Chart.js integration
├── includes/
│   ├── class-uad-core.php          # Core functionality
│   └── class-uad-shortcode.php     # Shortcode handler
├── templates/
│   └── dashboard.php               # Dashboard template
├── user-activity-dashboard.php     # Main plugin file
└── README.md                       # This file
```

## Table Columns

The activity table displays the following columns:

| Column | Description | Current Status |
|--------|-------------|----------------|
| Date | Activity date | ✅ Active |
| NON-SUI | Non-SUI metrics | 🚧 Placeholder |
| SUI | SUI metrics | 🚧 Placeholder |
| Total Hits | Number of hits/redirects | ✅ Active |
| Cost | Cost for hits | ✅ Active |
| Other Services | Additional services | 🚧 Placeholder |
| Total | Total other services | 🚧 Placeholder |
| Balance | Running balance | ✅ Active |

## Chart Features

The interactive chart displays:

- **Dual Y-axis** - Hits on left, Balance on right
- **Area fill** - Visual gradient fills under lines
- **Responsive** - Adapts to container size
- **Interactive tooltips** - Hover to see exact values
- **Legend** - Toggle datasets on/off
- **Smooth curves** - Tension applied for smoother visualization

## Troubleshooting

### Plugin won't activate

**Error:** "Please run composer install..."

**Solution:** Navigate to the `dashboard-usage` directory and run:
```bash
composer install
```

### No data showing

**Possible causes:**
1. Invalid user ID - Check if the user has activity data
2. API connection issue - Check the API endpoint is accessible
3. Date range has no data - Try a different date range

### Chart not displaying

**Possible causes:**
1. Chart.js failed to load - Check browser console for errors
2. JavaScript conflict - Try disabling other plugins
3. Ad blocker - Some ad blockers block Chart.js CDN

## Development

### Adding New Features

The plugin is designed to be extensible:

1. **Add filters** in `class-uad-core.php`
2. **Add new template variables** in `class-uad-shortcode.php`
3. **Customize template** in `templates/dashboard.php`
4. **Add styles** in `assets/css/uad-styles.css`
5. **Add JS functionality** in `assets/js/uad-scripts.js`

### Testing

Test the plugin without WordPress by viewing the standalone demo:

```bash
php -S localhost:8000 -t user-activity-dashboard-plugin/demo
```

## API Integration

This plugin uses the User Activity API Client:

- **Client Class:** `TpBloomland\UserActivity\UserActivityClient`
- **API Endpoint:** `https://ce7jzbocq1.execute-api.ca-central-1.amazonaws.com/dev`
- **Documentation:** See `../USER_ACTIVITY_SUMMARY_API.md`

## Support

For issues or questions:

1. Check this README
2. Review the API documentation
3. Check WordPress error logs
4. Verify composer dependencies are installed

## Changelog

### Version 1.0.0 (2025-10-14)

- Initial release
- Chart.js integration with dual-axis visualization
- Responsive table with 8 columns
- Server-side rendering
- Shortcode support with customizable attributes
- Custom color palette implementation
- Summary cards with key metrics
- Error handling and validation

## Credits

Built with:
- [Chart.js](https://www.chartjs.org/) - Charting library
- User Activity API Client - Data source
- WordPress Shortcode API - Integration

## License

Copyright (c) 2025 TP Bloomland. All rights reserved.
