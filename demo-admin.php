<?php
/**
 * WordPress Admin Demo for WDM Header Plugin
 * This demonstrates how the plugin integrates with WordPress wp-admin
 */

// Load WordPress functions
require_once __DIR__ . '/includes/wordpress-functions.php';

// Define WordPress constants for demo
define('ABSPATH', __DIR__ . '/');
define('WP_ADMIN', true);
define('WPINC', 'wp-includes');

// Mock WordPress options storage
$wp_options = [];

function get_option($option, $default = false) {
    global $wp_options;
    return $wp_options[$option] ?? $default;
}

function update_option($option, $value) {
    global $wp_options;
    $wp_options[$option] = $value;
    return true;
}

// Handle form submission
if ($_POST && isset($_POST['wdm_header_options'])) {
    update_option('wdm_header_options', $_POST['wdm_header_options']);
    $success_message = 'Settings saved successfully!';
}

// Load the plugin
require_once __DIR__ . '/wdm-custom-header.php';

// Get current options
$options = get_option('wdm_header_options', []);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin - WDM Header Settings</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f1f1f1; margin: 0; padding: 20px; }
        .wrap { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.13); }
        h1 { font-size: 23px; font-weight: 400; margin: 0 0 20px; color: #23282d; border-bottom: 1px solid #e1e1e1; padding-bottom: 10px; }
        h2 { font-size: 20px; font-weight: 600; margin: 30px 0 15px; color: #23282d; }
        .form-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 0.5em; }
        .form-table th { width: 200px; padding: 20px 10px 20px 0; text-align: left; vertical-align: top; font-weight: 600; color: #23282d; }
        .form-table td { padding: 15px 10px; line-height: 1.3; vertical-align: middle; }
        .form-table input[type="text"], .form-table input[type="password"], .form-table input[type="url"] { width: 25em; padding: 6px 8px; border: 1px solid #8c8f94; border-radius: 3px; background: #fff; color: #2c3338; }
        .form-table p.description { margin: 2px 0 5px; color: #646970; font-size: 13px; }
        .button { background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 3px; color: #2c3338; cursor: pointer; display: inline-block; font-size: 13px; line-height: 2.15384615; margin: 0; padding: 0 10px; text-decoration: none; }
        .button-primary { background: #2271b1; border-color: #2271b1; color: #fff; }
        .button-primary:hover { background: #135e96; border-color: #135e96; }
        .notice { background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #00a32a; margin: 5px 0 15px; padding: 1px 12px; }
        .admin-nav { background: #23282d; color: #fff; padding: 10px 20px; margin: -20px -20px 20px; }
        .version-footer { margin-top: 30px; padding: 15px; background: #f1f1f1; border-left: 4px solid #0073aa; font-size: 13px; color: #666; }
        .github-info { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="admin-nav">
        <h2>WordPress Admin → Settings → WDM Header</h2>
    </div>
    
    <div class="wrap">
        <h1>WDM Header Settings</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="notice"><p><?php echo esc_html($success_message); ?></p></div>
        <?php endif; ?>
        
        <form method="post" action="">
            
            <h2>General Settings</h2>
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Header</th>
                    <td>
                        <label>
                            <input type="checkbox" name="wdm_header_options[enable_header]" value="1" <?php checked($options['enable_header'] ?? true, true); ?>>
                            Enable WDM Custom Header
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Logo URL</th>
                    <td>
                        <input type="url" name="wdm_header_options[logo_url]" value="<?php echo esc_attr($options['logo_url'] ?? 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png'); ?>" class="regular-text">
                        <p class="description">Enter the full URL to your logo image.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Menu Settings</h2>
            <p>Configure the navigation menu items.</p>
            <table class="form-table">
                <tr>
                    <th scope="row">Navigation Items</th>
                    <td>
                        <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                            <strong>How We Serve</strong> - Mega dropdown menu<br>
                            <strong>About</strong> - Simple link<br>
                            <strong>Get Involved</strong> - Simple link<br>
                            <strong>News</strong> - Simple link<br>
                            <strong>Contact</strong> - Simple link
                        </div>
                        <p class="description">Menu items are configured programmatically. Advanced editing available in code.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Utility Buttons</h2>
            <p>Configure the utility buttons (Volunteer, Donate, etc.)</p>
            <table class="form-table">
                <tr>
                    <th scope="row">Button Configuration</th>
                    <td>
                        <div style="border: 1px solid #ddd; padding: 15px; background: #f9f9f9;">
                            <strong>VOLUNTEER</strong> - Call-to-action button<br>
                            <strong>DONATE</strong> - Donation button (opens in new window)
                        </div>
                        <p class="description">Button styling and behavior configured in CSS and JavaScript.</p>
                    </td>
                </tr>
            </table>
            
            <h2>Auto Updates</h2>
            <p>Configure automatic updates from GitHub repository.</p>
            
            <div class="github-info">
                <h4>How it works:</h4>
                <ol>
                    <li>The plugin checks for new releases on your GitHub repository</li>
                    <li>When a new version is available, you'll see an "Update Now" notification</li>
                    <li>Click "Update Now" to automatically download and install the latest version</li>
                </ol>
                <p><strong>Setup:</strong> Create GitHub releases with semantic versioning (v1.0.0, v1.1.0, etc.)</p>
            </div>
            
            <table class="form-table">
                <tr>
                    <th scope="row">Enable Auto Updates</th>
                    <td>
                        <label>
                            <input type="checkbox" name="wdm_header_options[enable_auto_updates]" value="1" <?php checked($options['enable_auto_updates'] ?? true, true); ?>>
                            Automatically check for plugin updates from GitHub
                        </label>
                        <p class="description">When enabled, the plugin will check for new releases on GitHub and show update notifications.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">GitHub Username</th>
                    <td>
                        <input type="text" name="wdm_header_options[github_username]" value="<?php echo esc_attr($options['github_username'] ?? ''); ?>" class="regular-text" placeholder="your-username">
                        <p class="description">The GitHub username or organization name where the repository is hosted.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Repository Name</th>
                    <td>
                        <input type="text" name="wdm_header_options[github_repo]" value="<?php echo esc_attr($options['github_repo'] ?? ''); ?>" class="regular-text" placeholder="wdm-custom-header">
                        <p class="description">The name of the GitHub repository containing the plugin code.</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">GitHub Token (Optional)</th>
                    <td>
                        <input type="password" name="wdm_header_options[github_token]" value="<?php echo esc_attr($options['github_token'] ?? ''); ?>" class="regular-text" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx">
                        <p class="description">Personal access token for private repositories or higher rate limits. Leave empty for public repositories.</p>
                    </td>
                </tr>
            </table>
            
            <p class="submit">
                <input type="submit" name="submit" class="button button-primary" value="Save Settings">
                <button type="button" class="button" onclick="checkForUpdates()" style="margin-left: 10px;">Check for Updates Now</button>
            </p>
        </form>
        
        <div class="version-footer">
            <strong>WDM Custom Header</strong> version <?php echo WDM_CUSTOM_HEADER_VERSION; ?> | 
            GitHub Auto-Update System Active
        </div>
    </div>
    
    <script>
        function checkForUpdates() {
            const button = event.target;
            const originalText = button.textContent;
            
            button.textContent = 'Checking...';
            button.disabled = true;
            
            setTimeout(() => {
                button.textContent = 'Check Complete';
                setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                    
                    // Show update notification demo
                    const notice = document.createElement('div');
                    notice.className = 'notice';
                    notice.innerHTML = '<p><strong>WDM Custom Header:</strong> Update check completed. No new updates found at this time.</p>';
                    document.querySelector('.wrap h1').after(notice);
                }, 1500);
            }, 2000);
        }
    </script>
</body>
</html>