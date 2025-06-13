# WDM Custom Header - Installation Guide v1.1.0

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
   │   ├── class-wdm-settings.php
   │   └── class-wdm-updater.php
   └── templates/
       └── header.php
   ```

3. **Activate Plugin**
   - Go to WordPress Admin > Plugins
   - Find "WDM Custom Header"
   - Click "Activate"

4. **Display Header**
   Use the shortcode or PHP function:
   - **Shortcode:** `[wdm_custom_header]` (works in posts, pages, widgets)
   - **PHP Function:** `<?php wdm_display_header(); ?>` (theme files)

## New Features in v1.0.1

### Fixed Asset Loading
- CSS and JS files now load correctly in WordPress
- Proper WordPress asset enqueueing system
- Cache-busting timestamps for immediate updates

### Restored Shortcode
- `[wdm_custom_header]` shortcode fully functional
- Forces asset loading when shortcode is used
- Works in posts, pages, and widgets

### GitHub Auto-Update System
- Complete GitHub integration for automatic updates
- Admin settings in Settings > WDM Header
- Manual update checking and forcing
- Version comparison and update notifications

## GitHub Auto-Update Setup

1. **Go to Settings > WDM Header**
2. **Configure GitHub Settings:**
   - GitHub Username: Your GitHub username
   - GitHub Repository: Repository name (e.g., wdm-custom-header)
   - Enable Auto-Update: Check to enable automatic updates

3. **Test Update System:**
   - Click "Check for Updates" to manually check
   - Click "Force Update Check" to refresh WordPress update cache

## Usage

### Shortcode Method
```
[wdm_custom_header]
```
Perfect for pages, posts, and widgets.

### PHP Function Method
```php
<?php wdm_display_header(); ?>
```
For theme integration and template files.

## Troubleshooting

### Assets Not Loading
- Verify plugin activation
- Check Settings > WDM Header > Load Default CSS is enabled
- Clear any caching plugins
- Force refresh browser (Ctrl+F5)

### GitHub Updates Not Working
- Verify GitHub username and repository are correct
- Ensure repository is public or you have access
- Check that repository has releases/tags
- Test API connectivity in admin panel

### Shortcode Not Working
- Confirm shortcode spelling: `[wdm_custom_header]`
- Check if plugin is activated
- Verify in WordPress admin that WDM Header appears in settings

## Advanced Configuration

### Custom GitHub Repository
1. Fork or create your own repository
2. Add releases with version tags (e.g., v1.0.2)
3. Configure GitHub settings in admin panel
4. Enable auto-update

### Asset Management
- CSS loading can be disabled in settings if using custom styles
- JavaScript always loads for functionality
- All assets include cache-busting for development

## Support

For technical issues:
1. Check WordPress error logs
2. Verify file permissions (644 for files, 755 for directories)
3. Test with default WordPress theme
4. Disable other plugins to check for conflicts
5. Use GitHub update system for latest fixes