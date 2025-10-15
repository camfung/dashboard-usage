# Architecture Overview

This document provides a comprehensive overview of the User Activity Dashboard architecture, design patterns, and system components.

## Table of Contents

1. [System Overview](#system-overview)
2. [Architecture Diagram](#architecture-diagram)
3. [Directory Structure](#directory-structure)
4. [Core Components](#core-components)
5. [Data Flow](#data-flow)
6. [Design Patterns](#design-patterns)
7. [WordPress Integration](#wordpress-integration)
8. [Frontend Architecture](#frontend-architecture)
9. [API Communication](#api-communication)
10. [Security Considerations](#security-considerations)

## System Overview

The User Activity Dashboard is a WordPress plugin that displays user activity data from an external API. It follows a server-side rendering (SSR) architecture with progressive enhancement through JavaScript.

### Key Characteristics

- **Server-Side Rendering**: Initial HTML is rendered on the server
- **Progressive Enhancement**: JavaScript adds interactivity
- **Stateless**: No database storage, all data from API
- **Modular**: Clear separation of concerns
- **Testable**: Comprehensive PHPUnit test coverage

### Technology Stack

**Backend:**
- PHP 7.4+ (WordPress plugin architecture)
- Composer (dependency management)
- Guzzle HTTP Client (API communication)
- PHPUnit (testing framework)

**Frontend:**
- Vanilla JavaScript (no frameworks)
- Chart.js 4.4.0 (data visualization)
- CSS3 (modern styling)
- HTML5 (semantic markup)

## Architecture Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     WordPress Core                          │
└─────────────────────┬───────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              User Activity Dashboard Plugin                 │
│                                                             │
│  ┌────────────────────────────────────────────────────┐   │
│  │  user-activity-dashboard.php (Main Plugin File)    │   │
│  │  - Plugin initialization                           │   │
│  │  - Asset registration                              │   │
│  │  - Shortcode registration                          │   │
│  └─────────────────┬──────────────────────────────────┘   │
│                    │                                        │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │         includes/ (Core Classes)                    │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  class-uad-core.php                          │  │   │
│  │  │  - Singleton pattern                         │  │   │
│  │  │  - Plugin lifecycle management               │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  class-uad-shortcode.php                     │  │   │
│  │  │  - Shortcode rendering                       │  │   │
│  │  │  - Data fetching                             │  │   │
│  │  │  - Template loading                          │  │   │
│  │  └──────────────┬───────────────────────────────┘  │   │
│  └─────────────────┼──────────────────────────────────┘   │
│                    │                                        │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │      lib/UserActivity/ (API Client)                 │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  UserActivityClient.php                      │  │   │
│  │  │  - API communication                         │  │   │
│  │  │  - HTTP requests via Guzzle                  │  │   │
│  │  │  - Response parsing                          │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  Exceptions/ApiException.php                 │  │   │
│  │  │  - Custom exception handling                 │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └─────────────────┬──────────────────────────────────┘   │
│                    │                                        │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │      templates/ (View Layer)                        │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  dashboard.php                               │  │   │
│  │  │  - HTML structure                            │  │   │
│  │  │  - PHP data interpolation                    │  │   │
│  │  │  - Chart data preparation                    │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └─────────────────┬──────────────────────────────────┘   │
│                    │                                        │
│  ┌─────────────────▼──────────────────────────────────┐   │
│  │       assets/ (Frontend Resources)                  │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  css/uad-styles.css                          │  │   │
│  │  │  - Component styling                         │  │   │
│  │  │  - Responsive design                         │  │   │
│  │  │  - Color palette                             │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  js/uad-scripts.js                           │  │   │
│  │  │  - Chart initialization                      │  │   │
│  │  │  - Date picker logic                         │  │   │
│  │  │  - Pagination control                        │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  │  ┌──────────────────────────────────────────────┐  │   │
│  │  │  vendor/chart.min.js                         │  │   │
│  │  │  - Third-party chart library                 │  │   │
│  │  └──────────────────────────────────────────────┘  │   │
│  └────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                      │
                      ▼
┌─────────────────────────────────────────────────────────────┐
│              External User Activity API                     │
│  - GET /user-activity-summary/{userId}                      │
│  - GET /describe-table/{tableName}                          │
│  - GET /sample-data/{tableName}                             │
└─────────────────────────────────────────────────────────────┘
```

## Directory Structure

```
dashboard-usage/
├── assets/                      # Frontend assets
│   ├── css/
│   │   └── uad-styles.css      # Main stylesheet
│   ├── js/
│   │   └── uad-scripts.js      # Main JavaScript file
│   └── vendor/
│       └── chart.min.js         # Chart.js library
│
├── docs/                        # Documentation
│   ├── README.md               # Documentation index
│   ├── INSTALLATION.md         # Installation guide
│   ├── ARCHITECTURE.md         # This file
│   ├── API_CLIENT.md           # API client docs
│   ├── FRONTEND.md             # Frontend docs
│   ├── CUSTOMIZATION.md        # Customization guide
│   ├── TESTING.md              # Testing guide
│   └── TROUBLESHOOTING.md      # Troubleshooting
│
├── includes/                    # WordPress plugin classes
│   ├── class-uad-core.php      # Core plugin class
│   └── class-uad-shortcode.php # Shortcode handler
│
├── lib/                         # PHP libraries
│   └── UserActivity/
│       ├── UserActivityClient.php    # API client
│       └── Exceptions/
│           └── ApiException.php      # Custom exceptions
│
├── src/                         # Source for standalone client
│   └── (mirrors lib/UserActivity/)
│
├── templates/                   # PHP templates
│   └── dashboard.php           # Main dashboard template
│
├── tests/                       # PHPUnit tests
│   └── UserActivityClientTest.php   # API client tests
│
├── vendor/                      # Composer dependencies (generated)
│   ├── autoload.php
│   ├── guzzlehttp/             # HTTP client
│   └── phpunit/                # Testing framework
│
├── .gitignore                   # Git ignore rules
├── composer.json                # PHP dependencies
├── composer.lock                # Locked dependency versions
├── phpunit.xml                  # PHPUnit configuration
├── DATE_PICKER_GUIDE.md        # Date picker documentation
└── user-activity-dashboard.php # Main plugin file
```

## Core Components

### 1. Main Plugin File

**File:** `user-activity-dashboard.php`

**Responsibilities:**
- Plugin metadata (name, version, author)
- WordPress hooks registration
- Composer autoloader initialization
- Error handling for missing dependencies

**Key Code:**
```php
define('UAD_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('UAD_PLUGIN_URL', plugin_dir_url(__FILE__));

require_once UAD_PLUGIN_DIR . 'vendor/autoload.php';

add_action('plugins_loaded', function() {
    UAD_Core::get_instance();
});
```

### 2. Core Plugin Class

**File:** `includes/class-uad-core.php`

**Pattern:** Singleton

**Responsibilities:**
- Initialize plugin components
- Register assets (CSS, JS)
- Manage plugin lifecycle
- Provide configuration defaults

**Key Methods:**
```php
public static function get_instance()        // Singleton access
private function init()                      // Initialize hooks
public function enqueue_assets()             // Register CSS/JS
public function get_default_user_id()        // Configuration
public function get_default_days()           // Configuration
```

### 3. Shortcode Handler

**File:** `includes/class-uad-shortcode.php`

**Pattern:** Singleton

**Responsibilities:**
- Process shortcode attributes
- Fetch data from API
- Load and render templates
- Handle errors gracefully

**Key Methods:**
```php
public function render_dashboard($atts)                          // Main render
private function fetch_activity_data($user_id, $start, $end)   // API call
private function render_error($message)                         // Error display
private function is_valid_date($date)                          // Validation
```

### 4. API Client

**File:** `lib/UserActivity/UserActivityClient.php`

**Pattern:** Service class

**Responsibilities:**
- Communicate with external API
- Handle HTTP requests/responses
- Parse and validate data
- Throw exceptions on errors

**Key Methods:**
```php
public function getUserActivity($userId, $startDate, $endDate)  // Fetch activity
public function describeTable($tableName)                       // Get schema
public function getSampleData($tableName, $limit)              // Get samples
private function request($method, $endpoint, $options)         // HTTP wrapper
```

### 5. Template System

**File:** `templates/dashboard.php`

**Pattern:** PHP templating

**Responsibilities:**
- Render HTML structure
- Display data from controller
- Prepare JavaScript data
- Maintain semantic markup

**Available Variables:**
```php
$data          // Activity data array
$user_id       // Current user ID
$days          // Number of days
$show_chart    // Boolean: show chart
$show_table    // Boolean: show table
```

## Data Flow

### Request Flow

```
1. User visits page with shortcode
   ↓
2. WordPress parses shortcode
   ↓
3. UAD_Shortcode::render_dashboard() called
   ↓
4. Parse shortcode attributes (user_id, days, etc.)
   ↓
5. Check for URL parameters (date override)
   ↓
6. Calculate date range
   ↓
7. Call UserActivityClient::getUserActivity()
   ↓
8. API client makes HTTP request via Guzzle
   ↓
9. External API returns JSON response
   ↓
10. Parse and process response data
   ↓
11. Calculate summary statistics
   ↓
12. Load template (dashboard.php)
   ↓
13. Template renders HTML with data
   ↓
14. Return HTML to WordPress
   ↓
15. WordPress displays page
   ↓
16. Browser loads JavaScript
   ↓
17. JavaScript initializes chart, pagination, date picker
```

### Data Transformation

```
External API Response:
{
  "success": true,
  "source": [
    {
      "date": "2025-10-01",
      "totalHits": 1500,
      "hitCost": 0.15,
      "balance": 98.50
    },
    ...
  ]
}
   ↓
PHP Processing:
- Add placeholder fields (nonSui, sui, otherServices)
- Calculate totals (total_hits, total_cost)
- Calculate summary statistics
   ↓
Template Data:
[
  'activities' => [...],  // Array of daily records
  'summary' => [
    'total_days' => 30,
    'total_hits' => 45000,
    'total_cost' => 4.50,
    'final_balance' => 95.50
  ],
  'start_date' => '2025-09-15',
  'end_date' => '2025-10-14',
  'user_id' => 125
]
   ↓
HTML Output:
<div class="uad-dashboard">
  <!-- Header, summary, chart, table -->
</div>
   ↓
JavaScript Enhancement:
- Initialize Chart.js visualization
- Setup pagination handlers
- Attach date picker event listeners
```

## Design Patterns

### 1. Singleton Pattern

**Used in:**
- `UAD_Core`
- `UAD_Shortcode`

**Purpose:**
- Ensure single instance per request
- Centralized configuration
- Prevent duplicate hook registration

**Implementation:**
```php
class UAD_Core {
    private static $instance = null;

    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        $this->init();
    }
}
```

### 2. MVC Pattern (Modified)

**Model:** `UserActivityClient` - Data fetching and business logic
**View:** `templates/dashboard.php` - Presentation layer
**Controller:** `UAD_Shortcode` - Request handling and data preparation

**Note:** Not strict MVC due to WordPress constraints, but follows the spirit

### 3. Dependency Injection

**Used in:** API client initialization

```php
// In shortcode handler
$client = new UserActivityClient();  // Could be injected for testing
$response = $client->getUserActivity($user_id, $start_date, $end_date);
```

### 4. Template Method Pattern

**Used in:** Shortcode rendering

```php
public function render_dashboard($atts) {
    // 1. Parse attributes
    // 2. Validate input
    // 3. Fetch data
    // 4. Handle errors
    // 5. Load template
    // 6. Return output
}
```

### 5. Factory Pattern

**Used in:** Error handling

```php
private function render_error($message) {
    return sprintf(
        '<div class="uad-error"><p>%s</p></div>',
        esc_html($message)
    );
}
```

## WordPress Integration

### Plugin Lifecycle

```
1. WordPress loads plugins
   ↓
2. user-activity-dashboard.php executed
   ↓
3. Plugin constants defined
   ↓
4. Composer autoloader loaded
   ↓
5. 'plugins_loaded' action fires
   ↓
6. UAD_Core singleton created
   ↓
7. Hooks registered:
   - wp_enqueue_scripts (assets)
   - shortcode: user_activity_dashboard
   ↓
8. WordPress continues loading
```

### Hook System

**Actions:**
```php
add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);
```

**Filters:**
```php
apply_filters('uad_default_user_id', $default_user_id);
apply_filters('uad_default_days', 30);
```

**Shortcodes:**
```php
add_shortcode('user_activity_dashboard', [$this, 'render_dashboard']);
```

### Asset Management

```php
// CSS
wp_enqueue_style(
    'uad-styles',
    UAD_PLUGIN_URL . 'assets/css/uad-styles.css',
    [],
    '1.2.0'
);

// JavaScript (with dependencies)
wp_enqueue_script(
    'uad-scripts',
    UAD_PLUGIN_URL . 'assets/js/uad-scripts.js',
    ['uad-chart'],
    '1.2.0',
    true
);
```

## Frontend Architecture

### JavaScript Module Pattern

```javascript
(function() {
    'use strict';

    // Private variables
    const colors = { ... };

    // Private functions
    function initChart() { ... }
    function initDatePicker() { ... }
    function initPagination() { ... }

    // Initialization
    function init() {
        initChart();
        initDatePicker();
        initPagination();
    }

    // DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
```

### Component-Based CSS

```css
/* Component: Dashboard */
.uad-dashboard { ... }

/* Component: Summary Cards */
.uad-summary { ... }
.uad-summary-item { ... }

/* Component: Chart */
.uad-chart-container { ... }

/* Component: Table */
.uad-table { ... }
.uad-table-row { ... }

/* Component: Pagination */
.uad-pagination { ... }
.uad-page-btn { ... }
```

## API Communication

### HTTP Client Configuration

```php
$this->client = new Client([
    'base_uri' => 'https://api.example.com/dev/',
    'timeout' => 30,
    'headers' => [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ]
]);
```

### Request/Response Flow

```
1. PHP calls UserActivityClient method
   ↓
2. Client builds request parameters
   ↓
3. Guzzle sends HTTP request
   ↓
4. External API processes request
   ↓
5. API returns JSON response
   ↓
6. Guzzle receives response
   ↓
7. Client parses JSON
   ↓
8. Validate response structure
   ↓
9. Return data or throw exception
```

### Error Handling

```php
try {
    $response = $this->request('GET', $endpoint, $options);
    return $response;
} catch (GuzzleException $e) {
    throw new ApiException(
        'API request failed: ' . $e->getMessage(),
        $e->getCode(),
        $e
    );
}
```

## Security Considerations

### Input Validation

```php
// Sanitize URL parameters
$url_start_date = isset($_GET['uad_start_date'])
    ? sanitize_text_field($_GET['uad_start_date'])
    : null;

// Validate date format
private function is_valid_date($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

// Type casting
$user_id = intval($atts['user_id']);
$days = intval($atts['days']);
```

### Output Escaping

```php
// Escape HTML
echo esc_html($activity['date']);

// Escape attributes
echo esc_attr($user_id);

// Escape URLs
echo esc_url($image_url);

// JSON encoding
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP);
```

### Nonce Verification (Future Enhancement)

```php
// For AJAX requests (not currently implemented)
wp_create_nonce('uad_load_data');
wp_verify_nonce($_POST['nonce'], 'uad_load_data');
```

### API Security

- HTTPS enforced in API base URL
- No credentials stored in frontend code
- Server-side API calls only
- Rate limiting handled by external API

## Performance Optimization

### Server-Side

1. **Caching Strategy** (future enhancement)
   - Transient API for WordPress caching
   - Cache API responses for 5-15 minutes
   - Cache key based on user_id + date range

2. **Efficient Data Processing**
   - Single loop for summary calculations
   - No unnecessary database queries
   - Minimal memory footprint

### Client-Side

1. **Asset Loading**
   - Minified CSS and JS (production)
   - Chart.js loaded conditionally
   - Deferred script loading

2. **Pagination**
   - Hide rows with CSS (display: none)
   - No DOM manipulation for large datasets
   - Event delegation for page buttons

3. **Chart Rendering**
   - Responsive aspect ratio
   - Canvas-based (hardware accelerated)
   - Efficient data structure

## Scalability Considerations

### Current Limitations

- No caching (every request hits API)
- Server-side rendering only
- No lazy loading for large datasets

### Future Enhancements

1. **Caching Layer**
   ```php
   $cache_key = 'uad_' . $user_id . '_' . $start_date . '_' . $end_date;
   $data = get_transient($cache_key);
   if (false === $data) {
       $data = $client->getUserActivity(...);
       set_transient($cache_key, $data, 15 * MINUTE_IN_SECONDS);
   }
   ```

2. **AJAX Loading**
   - Load initial page structure
   - Fetch data via AJAX
   - Show loading spinner
   - Better for slow API responses

3. **Virtual Scrolling**
   - For datasets > 1000 rows
   - Only render visible rows
   - Improves initial render time

## Testing Architecture

### Unit Tests

**File:** `tests/UserActivityClientTest.php`

**Coverage:**
- API client methods
- Request/response handling
- Error scenarios
- Data validation

**Pattern:** Arrange-Act-Assert

```php
public function test_getUserActivity_returns_valid_data() {
    // Arrange
    $client = new UserActivityClient();

    // Act
    $result = $client->getUserActivity(125, '2025-09-01', '2025-09-30');

    // Assert
    $this->assertTrue($result['success']);
    $this->assertIsArray($result['source']);
}
```

### Integration Points

1. **WordPress Integration** - Tested manually
2. **API Integration** - Tested with PHPUnit
3. **Frontend Integration** - Tested in browser

---

For implementation details on specific components:
- [API Client Documentation](./API_CLIENT.md)
- [Frontend Development](./FRONTEND.md)
- [Testing Guide](./TESTING.md)
