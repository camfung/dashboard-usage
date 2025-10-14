# Date Picker Feature Guide

## Overview

The User Activity Dashboard now includes an interactive date picker that allows users to dynamically change the date range without editing the shortcode or refreshing the page manually.

## Features

✅ **Visual Calendar Inputs** - HTML5 date pickers for start and end dates
✅ **Update Button** - Instantly reload data with selected date range
✅ **Reset Button** - Return to default date range
✅ **Date Validation** - Prevents invalid date selections
✅ **Styled Interface** - Matches the dashboard color palette
✅ **Responsive Design** - Works on desktop, tablet, and mobile

## How It Works

### User Interface

The date picker appears at the top of the dashboard with:

1. **Start Date** - Calendar picker for start date
2. **End Date** - Calendar picker for end date
3. **Update Button** - Applies the selected date range
4. **Reset Button** - Returns to default settings

### Usage

#### Step 1: Select Dates

Click on the date inputs to open calendar pickers:
- Choose your desired start date
- Choose your desired end date

#### Step 2: Update

Click the **"Update"** button to reload the dashboard with the new date range.

#### Step 3: Reset (Optional)

Click the **"Reset"** button to return to the default date range (last 30 days or whatever is specified in the shortcode).

## Date Validation

The date picker includes automatic validation:

### Constraints

- **Maximum Date**: Today (can't select future dates)
- **Minimum End Date**: Must be after start date
- **Maximum Start Date**: Can't be after end date

### Interactive Validation

- When you change the start date, the end date picker automatically sets its minimum to that date
- When you change the end date, the start date picker automatically sets its maximum to that date
- Alert shown if you try to update with invalid dates

## Technical Details

### How It Works

1. User selects dates and clicks "Update"
2. JavaScript adds URL parameters: `?uad_start_date=YYYY-MM-DD&uad_end_date=YYYY-MM-DD`
3. Page reloads with new parameters
4. Shortcode handler detects URL parameters and uses them instead of defaults
5. Dashboard displays data for the selected date range

### URL Parameters

The date picker uses URL query parameters:

```
?uad_start_date=2025-08-01&uad_end_date=2025-08-31
```

This means you can also:
- **Bookmark** specific date ranges
- **Share** URLs with specific dates
- **Link** to specific date ranges from other pages

### Default Behavior

When no URL parameters are present:
- Uses the `days` attribute from the shortcode (default: 30)
- Calculates start date as `today - days`
- End date is always today

## Examples

### Example 1: Last 30 Days (Default)

**Shortcode:**
```
[user_activity_dashboard]
```

**Default Dates:**
- Start: 30 days ago
- End: Today

**User can change to any range using the picker**

### Example 2: Custom Default Range

**Shortcode:**
```
[user_activity_dashboard days="90"]
```

**Default Dates:**
- Start: 90 days ago
- End: Today

**User can override with picker**

### Example 3: Direct URL Link

Share a specific date range:

```
https://yoursite.com/dashboard/?uad_start_date=2025-08-01&uad_end_date=2025-08-31
```

This will load the dashboard with August 2025 data, regardless of the shortcode's `days` attribute.

## Styling

### Color Palette

The date picker matches the dashboard theme:

- **Inputs**: Bleu de France borders (#3083DC)
- **Focus**: Selective Yellow highlight (#FFB30F)
- **Update Button**: Bleu de France background
- **Reset Button**: Jet border and text
- **Hover Effects**: Selective Yellow

### Responsive Design

**Desktop:**
- Horizontal layout
- Inline inputs and buttons

**Tablet:**
- Slightly compressed horizontal layout
- All elements visible

**Mobile:**
- Vertical stacked layout
- Full-width inputs and buttons
- Easy touch targets

## Customization

### Hide the Date Picker

If you want to disable the date picker, you can hide it with CSS:

```css
.uad-date-picker {
    display: none !important;
}
```

Or modify the template to remove it entirely.

### Change Default Behavior

#### Set Different Default Range

Use the shortcode attribute:
```
[user_activity_dashboard days="60"]
```

#### Programmatic Date Range

Use URL parameters in your links:

```html
<a href="?uad_start_date=2025-01-01&uad_end_date=2025-12-31">View 2025</a>
<a href="?uad_start_date=2025-08-01&uad_end_date=2025-08-31">View August</a>
```

### Custom Validation

The validation is in the JavaScript. To modify, edit:

**File:** `assets/js/uad-scripts.js`

**Function:** `initDatePicker()`

```javascript
// Current validation
if (!startDate || !endDate) {
    alert('Please select both start and end dates');
    return;
}

if (startDate > endDate) {
    alert('Start date must be before end date');
    return;
}
```

## Troubleshooting

### Date Picker Not Showing

**Check:**
1. Is the template updated? (`templates/dashboard.php`)
2. Is the CSS loaded? (Check browser dev tools)
3. Is JavaScript enabled?

**Fix:**
- Clear browser cache
- Check WordPress plugin is updated
- Verify no JavaScript errors in console (F12)

### Dates Not Updating

**Check:**
1. Click the "Update" button after selecting dates
2. Look for JavaScript errors in console
3. Verify URL parameters appear after clicking Update

**Fix:**
- Check browser console for errors
- Ensure JavaScript file is loaded correctly

### Reset Button Not Working

**Check:**
1. Are there URL parameters to remove?
2. Check browser console for errors

**Fix:**
- The reset button only works if URL parameters exist
- If no parameters, page is already in default state

### Invalid Date Format

**Issue:** Dates not accepted by API

**Solution:**
- The plugin validates date format as YYYY-MM-DD
- Invalid formats are rejected
- Falls back to default date range

## Browser Compatibility

### Date Input Support

HTML5 date inputs are supported by:

✅ **Chrome** - Full support
✅ **Edge** - Full support
✅ **Safari** - Full support (iOS 5+)
✅ **Firefox** - Full support
⚠️ **IE 11** - Falls back to text input (still functional)

### Fallback Behavior

On browsers without native date picker:
- Text input with placeholder "YYYY-MM-DD"
- Manual date entry required
- Still validates format on submission

## Code Reference

### Files Modified

1. **`templates/dashboard.php`** - Added date picker HTML
2. **`assets/js/uad-scripts.js`** - Added date picker JavaScript
3. **`assets/css/uad-styles.css`** - Added date picker styles
4. **`includes/class-uad-shortcode.php`** - Added URL parameter handling

### Key Functions

**JavaScript:**
- `initDatePicker()` - Initializes date picker functionality
- Event listeners for update and reset buttons
- Date validation logic

**PHP:**
- `is_valid_date()` - Validates date format
- URL parameter detection in `render_dashboard()`

## FAQ

### Can I bookmark a specific date range?

Yes! The URL includes the date parameters, so bookmarking works perfectly.

### Can I link to a specific date range?

Yes! Use URL parameters:
```
https://yoursite.com/dashboard/?uad_start_date=2025-07-01&uad_end_date=2025-07-31
```

### Does this work with all shortcode attributes?

Yes! The date picker works alongside:
- `user_id` attribute
- `show_chart` attribute
- `show_table` attribute

Example:
```
[user_activity_dashboard user_id="125" days="90" show_chart="true"]
```

The user can still override the date range with the picker.

### What happens if I select an invalid range?

You'll see an alert message:
- "Please select both start and end dates"
- "Start date must be before end date"

The page won't reload until valid dates are selected.

### Can I disable the date picker for specific users?

Yes! Use WordPress conditionals:

```php
<?php if (current_user_can('administrator')) : ?>
    [user_activity_dashboard]
<?php else : ?>
    [user_activity_dashboard] <!-- Date picker hidden via CSS or template modification -->
<?php endif; ?>
```

---

**Feature Version:** 1.1.0
**Added:** 2025-10-14
**Compatible With:** WordPress 6.8.3+, All modern browsers
