# Quick Start Guide

## 🚀 Installation (3 Steps)

### Step 1: Copy Plugin to WordPress

If you're copying the entire `dashboard-usage` directory to WordPress plugins:

```bash
# Copy everything
cp -r /path/to/dashboard-usage /path/to/wordpress/wp-content/plugins/

# Go to the plugin directory
cd /path/to/wordpress/wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin

# Install dependencies
composer install --no-dev
```

### Step 2: Create Symlink (Important!)

WordPress needs plugins in the immediate `wp-content/plugins/` directory, so create a symlink:

```bash
cd /path/to/wordpress/wp-content/plugins/
ln -s dashboard-usage/user-activity-dashboard-plugin user-activity-dashboard-plugin
```

### Step 3: Activate in WordPress

1. Log in to WordPress Admin
2. Go to **Plugins** → **Installed Plugins**
3. Find "User Activity Dashboard"
4. Click **Activate**

## 📝 Using the Shortcode

Add this to any page or post:

```
[user_activity_dashboard]
```

That's it! You'll see:
- ✅ Summary cards (Total Days, Total Hits, Total Cost, Balance)
- ✅ Interactive Chart.js graph
- ✅ Detailed activity table

## 🎨 Customization

### Change User ID
```
[user_activity_dashboard user_id="200"]
```

### Change Date Range
```
[user_activity_dashboard days="60"]
```

### Show Only Chart
```
[user_activity_dashboard show_table="false"]
```

### Show Only Table
```
[user_activity_dashboard show_chart="false"]
```

### All Options Combined
```
[user_activity_dashboard user_id="125" days="90" show_chart="true" show_table="true"]
```

## 🧪 Test Before Installing

Test the plugin works before WordPress installation:

```bash
cd user-activity-dashboard-plugin
php test-plugin.php
```

You should see:
```
✓ ALL TESTS PASSED - Plugin is ready for WordPress!
```

## 🎯 What You Get

### Dashboard Features:
- 📊 **Chart**: Dual-axis line chart with area fill (Hits vs Balance)
- 📋 **Table**: Daily breakdown with 8 columns
- 📈 **Summary**: Key metrics at a glance
- 🎨 **Styled**: Custom color palette matching your brand
- 📱 **Responsive**: Works on desktop, tablet, mobile

### Color Palette:
- Baby Powder (#FFFFFA) - Backgrounds
- Bleu de France (#3083DC) - Primary accents
- Jet (#2D2D2A) - Text and headers
- Selective Yellow (#FFB30F) - Highlights
- Poppy (#DF2935) - Negative values

## 📂 File Locations

After copying to WordPress:

```
wp-content/plugins/
├── dashboard-usage/                    # Your copied directory
│   ├── user-activity-dashboard-plugin/ # The actual plugin
│   │   ├── vendor/                     # Composer dependencies ✓
│   │   ├── lib/                        # API client code ✓
│   │   ├── includes/                   # Plugin classes
│   │   ├── templates/                  # HTML templates
│   │   ├── assets/                     # CSS & JS
│   │   └── user-activity-dashboard.php # Main plugin file
│   ├── src/                            # Original client source
│   ├── tests/                          # PHPUnit tests
│   └── composer.json                   # Project dependencies
└── user-activity-dashboard-plugin/     # Symlink → dashboard-usage/user-activity-dashboard-plugin/
```

## ⚠️ Common Issues

### Plugin Not Showing
**Problem:** Can't see the plugin in WordPress.

**Solution:** Create the symlink (see Step 2 above).

### "Please run composer install"
**Problem:** Activation error about missing vendor.

**Solution:**
```bash
cd /path/to/plugins/dashboard-usage/user-activity-dashboard-plugin
composer install --no-dev
```

### No Data Showing
**Problem:** Dashboard says "No activity data found."

**Solutions:**
- Try user ID 125 (the test user): `[user_activity_dashboard user_id="125"]`
- Increase date range: `[user_activity_dashboard days="90"]`
- Check API is accessible from your server

## 🔍 Verify Installation

Check everything is set up correctly:

### 1. Check symlink exists:
```bash
ls -la /path/to/wordpress/wp-content/plugins/ | grep user-activity-dashboard-plugin
```

Should show:
```
lrwxr-xr-x ... user-activity-dashboard-plugin -> dashboard-usage/user-activity-dashboard-plugin
```

### 2. Check vendor exists:
```bash
ls /path/to/wordpress/wp-content/plugins/dashboard-usage/user-activity-dashboard-plugin/vendor
```

Should show Guzzle and other dependencies.

### 3. Check WordPress can see it:
- Go to WordPress Admin → Plugins
- Should see "User Activity Dashboard" in the list

## 📚 Additional Documentation

- **`WORDPRESS_INSTALLATION.md`** - Detailed installation guide
- **`user-activity-dashboard-plugin/README.md`** - Plugin documentation
- **`USER_ACTIVITY_SUMMARY_API.md`** - API documentation
- **`README.md`** - PHP client documentation

## 🎉 You're Done!

Once activated, just add `[user_activity_dashboard]` to any page and you're good to go!

---

**Plugin Version:** 1.0.0
**Tested with WordPress:** 6.8.3
**Requires PHP:** 7.4+
