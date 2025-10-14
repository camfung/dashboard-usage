# User ID Configuration Guide

## How User ID Works

The plugin automatically determines which user's activity to display using the following priority:

### Priority Order

1. **Shortcode attribute** (highest priority)
   ```
   [user_activity_dashboard user_id="200"]
   ```

2. **Currently logged-in WordPress user**
   - If a user is logged in, uses their WordPress user ID automatically
   - WordPress function: `get_current_user_id()`

3. **Default fallback** (lowest priority)
   - Falls back to user ID 125 if no one is logged in
   - Used for testing or public pages

## Usage Scenarios

### Scenario 1: Show Current User's Activity (Recommended)

**Use Case:** Each user sees their own dashboard when logged in.

**Shortcode:**
```
[user_activity_dashboard]
```

**Behavior:**
- User logged in as WordPress user ID 50 → Shows data for user 50
- User logged in as WordPress user ID 100 → Shows data for user 100
- No user logged in → Shows data for user 125 (default)

**Best For:**
- User profile pages
- Account dashboards
- Members-only pages

---

### Scenario 2: Specific User Override

**Use Case:** Always show a specific user's data (admin viewing, public stats)

**Shortcode:**
```
[user_activity_dashboard user_id="200"]
```

**Behavior:**
- Always shows data for user 200, regardless of who's logged in

**Best For:**
- Admin pages
- Public statistics
- Fixed user reports

---

### Scenario 3: Admin Viewing All Users

**Use Case:** Admin can view any user's dashboard by passing user ID via URL parameter

**Add to your theme's `functions.php`:**
```php
add_filter('uad_default_user_id', function($default_user_id) {
    // Allow admins to view any user's dashboard via URL parameter
    if (current_user_can('manage_options') && isset($_GET['view_user_id'])) {
        return intval($_GET['view_user_id']);
    }
    return $default_user_id;
});
```

**Usage:**
- Normal users: `yoursite.com/dashboard/` → See their own data
- Admin: `yoursite.com/dashboard/?view_user_id=150` → See user 150's data

---

## Custom Configuration Examples

### Example 1: Different Default User

Change the default fallback from 125 to another user:

**Add to `functions.php`:**
```php
add_filter('uad_default_user_id', function($default_user_id) {
    // If no user is logged in, show user 999 instead of 125
    if (!is_user_logged_in()) {
        return 999;
    }
    return $default_user_id;
});
```

---

### Example 2: Use Custom User Meta

Map WordPress users to API user IDs using custom meta:

**Add to `functions.php`:**
```php
add_filter('uad_default_user_id', function($default_user_id) {
    $wp_user_id = get_current_user_id();

    if ($wp_user_id > 0) {
        // Get custom user meta that stores the API user ID
        $api_user_id = get_user_meta($wp_user_id, 'api_user_id', true);

        if ($api_user_id) {
            return intval($api_user_id);
        }
    }

    return $default_user_id;
});
```

**Usage:**
1. Set user meta in WordPress:
   ```php
   update_user_meta(50, 'api_user_id', 125); // WP user 50 maps to API user 125
   ```

2. When WP user 50 views the dashboard, it shows data for API user 125

---

### Example 3: Map Email to User ID

If your WordPress users have different IDs than the API:

**Add to `functions.php`:**
```php
add_filter('uad_default_user_id', function($default_user_id) {
    $current_user = wp_get_current_user();

    if ($current_user->ID > 0) {
        // Map email addresses to API user IDs
        $email_to_user_id = [
            'john@example.com' => 125,
            'jane@example.com' => 200,
            'admin@example.com' => 150,
        ];

        if (isset($email_to_user_id[$current_user->user_email])) {
            return $email_to_user_id[$current_user->user_email];
        }
    }

    return $default_user_id;
});
```

---

### Example 4: Restrict to Logged-In Users

Don't show any data if user isn't logged in:

**Add to shortcode or template:**
```php
<?php if (is_user_logged_in()) : ?>
    [user_activity_dashboard]
<?php else : ?>
    <p>Please <a href="<?php echo wp_login_url(get_permalink()); ?>">log in</a> to view your dashboard.</p>
<?php endif; ?>
```

---

## Code Reference

### Where User ID Is Determined

**File:** `includes/class-uad-core.php:69-78`

```php
public function get_default_user_id() {
    // Get current WordPress user ID
    $current_user_id = get_current_user_id();

    // Use current user if logged in, otherwise use default
    $default_user_id = $current_user_id > 0 ? $current_user_id : 125;

    // Allow filtering for customization
    return apply_filters('uad_default_user_id', $default_user_id);
}
```

### Where It's Used

**File:** `includes/class-uad-shortcode.php:42-43`

```php
$atts = shortcode_atts([
    'user_id' => UAD_Core::get_instance()->get_default_user_id(),
    // ...
], $atts, 'user_activity_dashboard');
```

---

## Testing Different Scenarios

### Test 1: Current User
```php
// Log in as WordPress user ID 50
// View page with: [user_activity_dashboard]
// Should show data for user 50
```

### Test 2: Specific User
```php
// Use: [user_activity_dashboard user_id="125"]
// Should always show data for user 125
```

### Test 3: Not Logged In
```php
// Log out
// View page with: [user_activity_dashboard]
// Should show data for user 125 (default)
```

---

## Common Questions

### Q: Can I show multiple users on one dashboard?

A: Not with the current shortcode, but you can use multiple shortcodes:

```
<h2>User 125 Activity</h2>
[user_activity_dashboard user_id="125"]

<h2>User 200 Activity</h2>
[user_activity_dashboard user_id="200"]
```

### Q: How do I hide the dashboard from non-logged-in users?

A: Use a WordPress conditional:

```php
<?php if (is_user_logged_in()) : ?>
    [user_activity_dashboard]
<?php endif; ?>
```

### Q: Can I pass the user ID via URL?

A: Yes, with a custom filter (see Example 3 under "Admin Viewing All Users")

### Q: What if WordPress user IDs don't match API user IDs?

A: Use custom user meta or email mapping (see Examples 2 and 3)

---

## Summary

**Default Behavior (Updated):**
- ✅ Logged-in users see their own data automatically
- ✅ Non-logged-in visitors see user 125 data (test user)
- ✅ Can override with shortcode attribute
- ✅ Fully customizable with WordPress filters

**Migration Note:**
If you were previously using the hardcoded user 125, the behavior is now:
- **Before:** Always showed user 125
- **After:** Shows current WordPress user's data, falls back to 125 if not logged in

To restore the old behavior (always show 125):
```
[user_activity_dashboard user_id="125"]
```

---

**Updated:** 2025-10-14
**Version:** 1.0.0
