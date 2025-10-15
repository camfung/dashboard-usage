# User Activity Dashboard - Developer Documentation

Welcome to the User Activity Dashboard developer documentation. This documentation covers everything you need to know to develop, customize, and maintain this WordPress plugin.

## Table of Contents

1. [Installation Guide](./INSTALLATION.md) - Setup and installation instructions
2. [Architecture Overview](./ARCHITECTURE.md) - System design and structure
3. [API Client Documentation](./API_CLIENT.md) - PHP client for User Activity API
4. [Frontend Development](./FRONTEND.md) - Templates, JavaScript, and CSS
5. [Customization Guide](./CUSTOMIZATION.md) - How to customize the dashboard
6. [Testing Guide](./TESTING.md) - Running and writing tests
7. [Troubleshooting](./TROUBLESHOOTING.md) - Common issues and solutions

## Quick Start

### Prerequisites

- PHP 7.4 or higher
- WordPress 6.8.3 or higher
- Composer (for dependency management)
- Node.js (optional, for development tools)

### Basic Installation

```bash
# Clone or copy the dashboard-usage directory to wp-content/plugins/
cd wp-content/plugins/dashboard-usage

# Install PHP dependencies
php composer.phar install

# Activate the plugin in WordPress admin
```

### Usage

Add the shortcode to any WordPress page or post:

```
[user_activity_dashboard]
```

Optional attributes:
- `user_id` - Specific user ID (defaults to current logged-in user)
- `days` - Number of days to show (default: 30)
- `show_chart` - Show chart (default: true)
- `show_table` - Show table (default: true)

Example:
```
[user_activity_dashboard user_id="125" days="90" show_chart="true" show_table="true"]
```

## Project Structure

```
dashboard-usage/
├── assets/                 # Frontend assets
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   └── vendor/            # Third-party assets (Chart.js)
├── docs/                  # Documentation (you are here)
├── includes/              # WordPress plugin core files
├── lib/                   # PHP libraries
│   └── UserActivity/      # API client library
├── src/                   # Source files for standalone client
├── templates/             # PHP template files
├── tests/                 # PHPUnit tests
├── vendor/                # Composer dependencies
├── composer.json          # PHP dependencies
├── phpunit.xml            # PHPUnit configuration
└── user-activity-dashboard.php  # Main plugin file
```

## Key Features

- **Server-side rendering** - Fast initial page load
- **Date picker** - Interactive date range selection
- **Chart visualization** - Chart.js powered analytics
- **Pagination** - Handle large datasets efficiently
- **Responsive design** - Works on desktop, tablet, and mobile
- **Custom color palette** - Branded color scheme
- **Test-driven development** - Comprehensive test coverage

## Technology Stack

### Backend
- **PHP 7.4+** - Server-side language
- **WordPress 6.8.3+** - CMS platform
- **Guzzle HTTP 7.5** - HTTP client for API requests
- **PHPUnit 9.6** - Testing framework
- **PSR-4 Autoloading** - Modern PHP autoloading

### Frontend
- **Chart.js 4.4.0** - Data visualization
- **Vanilla JavaScript** - No frameworks, pure JS
- **CSS3** - Modern styling with CSS Grid and Flexbox
- **HTML5** - Semantic markup and date inputs

## Color Palette

The dashboard uses a consistent color palette defined in CSS variables:

- **Baby Powder** (`#FFFFFA`) - Background color
- **Bleu de France** (`#3083DC`) - Primary color (buttons, borders)
- **Jet** (`#2D2D2A`) - Text and header color
- **Selective Yellow** (`#FFB30F`) - Accent color (highlights, active states)
- **Poppy** (`#DF2935`) - Error and negative values

## Development Workflow

1. **Read the documentation** - Start with [Installation](./INSTALLATION.md) and [Architecture](./ARCHITECTURE.md)
2. **Set up your environment** - Install dependencies and run tests
3. **Make changes** - Follow the coding standards
4. **Write tests** - Add tests for new functionality
5. **Test locally** - Run PHPUnit and test in WordPress
6. **Commit and push** - Use descriptive commit messages

## Getting Help

- Check the [Troubleshooting Guide](./TROUBLESHOOTING.md) for common issues
- Review the [Architecture Documentation](./ARCHITECTURE.md) to understand the system
- Look at the test files in `tests/` for usage examples

## Contributing

When contributing to this project:

1. Follow the existing code style
2. Write tests for new features
3. Update documentation as needed
4. Use descriptive commit messages
5. Test thoroughly before committing

## Version History

- **1.2.0** - Added pagination, removed balance from chart
- **1.1.0** - Added date picker functionality
- **1.0.0** - Initial release with chart and table display

## License

This project is proprietary software for TP Bloomland.

---

For detailed information on specific topics, please refer to the individual documentation files listed in the Table of Contents above.
