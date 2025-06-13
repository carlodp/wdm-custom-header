# WDM Custom Header - Installation Guide

## Quick Installation

1. **Download the Plugin**
   - Download all files maintaining the folder structure
   - Ensure you have all directories: `assets/`, `includes/`, `templates/`

2. **Upload to WordPress**
   ```
   /wp-content/plugins/wdm-custom-header/
   ├── wdm-custom-header.php
   ├── README.md
   ├── INSTALLATION.md
   ├── assets/
   │   ├── css/header.css
   │   └── js/header.js
   ├── includes/
   │   ├── class-wdm-header.php
   │   └── class-wdm-settings.php
   └── templates/
       └── header.php
   ```

3. **Activate Plugin**
   - Go to WordPress Admin > Plugins
   - Find "WDM Custom Header"
   - Click "Activate"

4. **Display Header**
   Add to your theme's header.php or template files:
   ```php
   <?php wdm_display_header(); ?>
   ```

## Troubleshooting

### CSS/JS Not Loading
- Check file permissions (644 for files, 755 for directories)
- Clear any caching plugins
- Verify plugin URL constants are correct

### Header Not Displaying
- Ensure you're calling `wdm_display_header()` in your theme
- Check if plugin is activated
- Verify template file exists

### Mobile Menu Issues
- Check browser console for JavaScript errors
- Ensure jQuery is loaded by WordPress
- Verify hamburger button elements exist

## Configuration

### Update Logo
In `templates/header.php`, line 19:
```html
<img src="https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png" alt="Greybull Rescue" class="wdm-logo-image">
```

### Customize Navigation
Edit the navigation menu in `templates/header.php` starting around line 95.

### Styling Changes
All styles are in `assets/css/header.css` with WDM-prefixed classes.

## Support

For issues with the plugin:
1. Check browser console for errors
2. Verify all files are uploaded correctly
3. Ensure WordPress meets minimum requirements (5.0+, PHP 7.4+)