<?php
/**
 * WordPress Admin Demo for WDM Header Plugin
 * This demonstrates how the plugin integrates with WordPress wp-admin
 */

// Simple WordPress function fallbacks
function esc_html($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function esc_attr($text) { return htmlspecialchars($text, ENT_QUOTES, 'UTF-8'); }
function checked($checked, $current = true, $echo = true) {
    $result = ($checked == $current) ? ' checked="checked"' : '';
    if ($echo) echo $result;
    return $result;
}

// Plugin version
define('WDM_CUSTOM_HEADER_VERSION', '1.0.1');

// Handle form submission
$success_message = '';
if ($_POST && isset($_POST['wdm_header_options'])) {
    // In real WordPress, this would save to wp_options table
    $success_message = 'Settings saved successfully!';
}

// Default values
$options = [
    'enable_header' => true,
    'logo_url' => 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png',
    'enable_auto_updates' => true,
    'github_username' => '',
    'github_repo' => '',
    'github_token' => ''
];

// Override with posted values
if ($_POST && isset($_POST['wdm_header_options'])) {
    $options = array_merge($options, $_POST['wdm_header_options']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin - WDM Header Settings</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f1f1f1; margin: 0; padding: 20px; line-height: 1.4; color: #3c434a; }
        .wrap { max-width: 1000px; margin: 0 auto; background: #fff; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.13); border-radius: 4px; }
        .admin-header { background: #23282d; color: #fff; padding: 15px 20px; margin: -20px -20px 20px; border-radius: 4px 4px 0 0; }
        .admin-header h2 { margin: 0; font-size: 16px; font-weight: 600; }
        h1 { font-size: 23px; font-weight: 400; margin: 0 0 20px; color: #23282d; border-bottom: 1px solid #e1e1e1; padding-bottom: 10px; }
        h2 { font-size: 20px; font-weight: 600; margin: 30px 0 15px; color: #23282d; }
        .form-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 0.5em; }
        .form-table th { width: 200px; padding: 20px 10px 20px 0; text-align: left; vertical-align: top; font-weight: 600; color: #23282d; }
        .form-table td { padding: 15px 10px; line-height: 1.3; vertical-align: middle; }
        .form-table input[type="text"], .form-table input[type="password"], .form-table input[type="url"] { 
            width: 25em; padding: 6px 8px; border: 1px solid #8c8f94; border-radius: 3px; background: #fff; color: #2c3338; font-size: 14px;
        }
        .form-table input[type="checkbox"] { margin-right: 8px; }
        .form-table p.description { margin: 2px 0 5px; color: #646970; font-size: 13px; font-style: normal; }
        .button { 
            background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 3px; color: #2c3338; cursor: pointer; 
            display: inline-block; font-size: 13px; line-height: 2.15384615; margin: 0; padding: 0 10px; 
            text-decoration: none; white-space: nowrap; transition: all 0.1s ease-in-out;
        }
        .button:hover { background: #f0f0f1; border-color: #8c8f94; color: #2c3338; }
        .button-primary { background: #2271b1; border-color: #2271b1; color: #fff; }
        .button-primary:hover { background: #135e96; border-color: #135e96; }
        .notice { background: #fff; border: 1px solid #c3c4c7; border-left: 4px solid #00a32a; margin: 5px 0 15px; padding: 1px 12px; border-radius: 0 4px 4px 0; }
        .version-footer { 
            margin-top: 30px; padding: 15px; background: #f1f1f1; border-left: 4px solid #0073aa; 
            font-size: 13px; color: #666; border-radius: 0 4px 4px 0; display: flex; justify-content: space-between; align-items: center;
        }
        .github-info { background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px; }
        .github-info h4 { margin: 0 0 10px; color: #23282d; }
        .github-info ol { margin-left: 20px; }
        .github-info p { margin: 10px 0 0; }
        .demo-menu-items, .demo-buttons { border: 1px solid #ddd; padding: 15px; background: #f9f9f9; border-radius: 4px; }
        .demo-menu-items strong, .demo-buttons strong { color: #2271b1; }
        .submit { padding-top: 20px; }
    </style>
</head>
<body>
    <div class="admin-header">
        <h2>WordPress Admin → Settings → WDM Header</h2>
    </div>
    
    <div class="wrap">
        <h1>WDM Header Settings</h1>
        
        <?php if (isset($success_message)): ?>
            <div class="notice"><p><?php echo esc_html($success_message); ?></p></div>
        <?php endif; ?>
        
        <form method="post" action="">
            
            <h2>General Settings</h2>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Enable Header</th>
                        <td>
                            <fieldset>
                                <label for="enable_header">
                                    <input name="wdm_header_options[enable_header]" type="checkbox" id="enable_header" value="1" <?php checked($options['enable_header']); ?>>
                                    Enable WDM Custom Header
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="logo_url">Logo URL</label></th>
                        <td>
                            <input name="wdm_header_options[logo_url]" type="url" id="logo_url" value="<?php echo esc_attr($options['logo_url']); ?>" class="regular-text">
                            <p class="description">Enter the full URL to your logo image.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <h2>Menu Settings</h2>
            <p>Configure the navigation menu items.</p>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Navigation Items</th>
                        <td>
                            <div class="demo-menu-items">
                                <strong>How We Serve</strong> - Mega dropdown menu with Emergency Response, Training Programs, Community Outreach<br>
                                <strong>About</strong> - Simple navigation link<br>
                                <strong>Get Involved</strong> - Simple navigation link<br>
                                <strong>News</strong> - Simple navigation link<br>
                                <strong>Contact</strong> - Simple navigation link
                            </div>
                            <p class="description">Menu items are configured in the plugin code. Advanced editing available through WordPress customizer or theme functions.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <h2>Utility Buttons</h2>
            <p>Configure the utility buttons (Volunteer, Donate, etc.)</p>
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Button Configuration</th>
                        <td>
                            <div class="demo-buttons">
                                <strong>VOLUNTEER</strong> - Primary call-to-action button with custom styling<br>
                                <strong>DONATE</strong> - Secondary action button (opens in new window)
                            </div>
                            <p class="description">Button styling and behavior are managed through CSS classes and JavaScript event handlers.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <h2>Auto Updates</h2>
            <p>Configure automatic updates from GitHub repository.</p>
            
            <div class="github-info">
                <h4>How GitHub Auto-Updates Work:</h4>
                <ol>
                    <li>The plugin checks for new releases on your specified GitHub repository</li>
                    <li>When a new version is available, you'll see an "Update Now" notification in WordPress admin</li>
                    <li>Click "Update Now" to automatically download and install the latest version</li>
                    <li>Updates are fetched directly from GitHub releases using the GitHub API</li>
                </ol>
                <p><strong>Setup Requirements:</strong> Create GitHub releases with semantic versioning (v1.0.0, v1.1.0, v1.2.0, etc.)</p>
                <p><strong>Private Repositories:</strong> Generate a Personal Access Token with "repo" permissions for private repositories.</p>
            </div>
            
            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th scope="row">Enable Auto Updates</th>
                        <td>
                            <fieldset>
                                <label for="enable_auto_updates">
                                    <input name="wdm_header_options[enable_auto_updates]" type="checkbox" id="enable_auto_updates" value="1" <?php checked($options['enable_auto_updates']); ?>>
                                    Automatically check for plugin updates from GitHub
                                </label>
                                <p class="description">When enabled, the plugin will check for new releases on GitHub and show update notifications.</p>
                            </fieldset>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="github_username">GitHub Username</label></th>
                        <td>
                            <input name="wdm_header_options[github_username]" type="text" id="github_username" value="<?php echo esc_attr($options['github_username']); ?>" class="regular-text" placeholder="your-username">
                            <p class="description">The GitHub username or organization name where the repository is hosted.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="github_repo">Repository Name</label></th>
                        <td>
                            <input name="wdm_header_options[github_repo]" type="text" id="github_repo" value="<?php echo esc_attr($options['github_repo']); ?>" class="regular-text" placeholder="wdm-custom-header">
                            <p class="description">The name of the GitHub repository containing the plugin code.</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="github_token">GitHub Token (Optional)</label></th>
                        <td>
                            <input name="wdm_header_options[github_token]" type="password" id="github_token" value="<?php echo esc_attr($options['github_token']); ?>" class="regular-text" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx">
                            <p class="description">Personal access token for private repositories or higher rate limits. Leave empty for public repositories.</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            
            <p class="submit">
                <input type="submit" name="submit" class="button button-primary" value="Save Settings">
                <button type="button" class="button" onclick="checkForUpdates()" style="margin-left: 10px;">Check for Updates Now</button>
            </p>
        </form>
        
        <div class="version-footer">
            <div>
                <strong>WDM Custom Header</strong> version <?php echo WDM_CUSTOM_HEADER_VERSION; ?> | 
                GitHub Auto-Update System Ready
            </div>
            <div>
                <a href="/" style="color: #0073aa; text-decoration: none;">← View Header Demo</a>
            </div>
        </div>
    </div>
    
    <script>
        function checkForUpdates() {
            const button = event.target;
            const originalText = button.textContent;
            
            button.textContent = 'Checking...';
            button.disabled = true;
            button.style.opacity = '0.6';
            
            // Simulate GitHub API check
            setTimeout(() => {
                button.textContent = 'Check Complete';
                
                // Show simulated update notice
                const notice = document.createElement('div');
                notice.className = 'notice';
                notice.innerHTML = '<p><strong>WDM Custom Header:</strong> Update check completed. Configure your GitHub repository settings to enable automatic updates.</p>';
                document.querySelector('.wrap h1').after(notice);
                
                setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                    button.style.opacity = '1';
                }, 2000);
            }, 2000);
        }
        
        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.getElementById('github_username').value;
            const repo = document.getElementById('github_repo').value;
            const autoUpdates = document.getElementById('enable_auto_updates').checked;
            
            if (autoUpdates && (!username || !repo)) {
                e.preventDefault();
                alert('Please enter both GitHub username and repository name to enable auto-updates.');
                return false;
            }
        });
    </script>
</body>
</html>