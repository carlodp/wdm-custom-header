# WDM Custom Header Plugin

A responsive WordPress header plugin with mega menu functionality, designed for Grey Bull Rescue.

## Features

- Responsive design that works on all devices
- Mega menu dropdown system
- Scroll-based hamburger menu for mobile
- Smooth animations and transitions
- Custom utility buttons (Volunteer/Donate)
- Left-aligned dropdown menus
- Clean, modern design

## Installation

1. Download all plugin files
2. Upload to your WordPress `/wp-content/plugins/wdm-custom-header/` directory
3. Activate the plugin in WordPress admin
4. The header will automatically be available for use

## File Structure

```
wdm-custom-header/
├── wdm-custom-header.php          # Main plugin file
├── templates/
│   └── header.php                 # Header HTML template
├── assets/
│   ├── css/
│   │   └── header.css            # All header styles
│   └── js/
│       └── header.js             # Header functionality
├── includes/
│   ├── class-wdm-header.php      # Header class
│   └── class-wdm-settings.php    # Settings class
└── README.md                      # This file
```

## Usage

To display the header in your theme, use:

```php
<?php
if (function_exists('wdm_display_header')) {
    wdm_display_header();
}
?>
```

## Customization

### Logo
Update the logo URL in `templates/header.php`:
```html
<img src="YOUR_LOGO_URL" alt="Your Site Name" class="wdm-logo-image">
```

### Navigation Menu
Modify the navigation items in `templates/header.php` within the `.Nav-list` section.

### Styling
All CSS is contained in `assets/css/header.css` with WDM-prefixed classes for uniqueness.

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Version

1.0.0 - Initial release with Grey Bull Rescue branding