<?php
/**
 * WDM Header Settings - Complete WordPress Admin Interface
 * Provides full WordPress-style settings management
 */

// Load WordPress functions fallback
require_once '../includes/wordpress-functions.php';

// Start session for data persistence
session_start();

// Simulate WordPress admin environment
define('WP_ADMIN', true);
define('ABSPATH', dirname(__DIR__) . '/');

// Handle form submissions with proper validation
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $action = $_POST['action'] ?? '';
    
    try {
        switch ($action) {
            case 'update_general':
                // General settings
                $_SESSION['wdm_enable_header'] = !empty($_POST['enable_header']);
                $_SESSION['wdm_load_default_css'] = !empty($_POST['load_default_css']);
                $_SESSION['wdm_header_logo_url'] = sanitize_url($_POST['logo_url'] ?? '');
                $success_message = 'General settings updated successfully.';
                break;
                
            case 'update_menu':
                // Menu settings
                $menu_items = [];
                if (!empty($_POST['menu_items'])) {
                    foreach ($_POST['menu_items'] as $item) {
                        if (!empty($item['title']) && !empty($item['url'])) {
                            $menu_item = [
                                'title' => sanitize_text_field($item['title']),
                                'url' => sanitize_url($item['url']),
                                'has_dropdown' => !empty($item['has_dropdown'])
                            ];
                            
                            if (!empty($item['dropdown_items'])) {
                                $menu_item['dropdown_items'] = [];
                                foreach ($item['dropdown_items'] as $dropdown) {
                                    if (!empty($dropdown['title']) && !empty($dropdown['url'])) {
                                        $menu_item['dropdown_items'][] = [
                                            'title' => sanitize_text_field($dropdown['title']),
                                            'url' => sanitize_url($dropdown['url'])
                                        ];
                                    }
                                }
                            }
                            
                            $menu_items[] = $menu_item;
                        }
                    }
                }
                $_SESSION['wdm_header_menu_items'] = $menu_items;
                $success_message = 'Menu settings updated successfully.';
                break;
                
            case 'update_buttons':
                // Button settings
                $utility_buttons = [];
                if (!empty($_POST['utility_buttons'])) {
                    foreach ($_POST['utility_buttons'] as $button) {
                        if (!empty($button['label']) && !empty($button['url'])) {
                            $utility_buttons[] = [
                                'label' => sanitize_text_field($button['label']),
                                'url' => sanitize_url($button['url']),
                                'class' => sanitize_text_field($button['class'] ?? ''),
                                'target' => in_array($button['target'] ?? '_self', ['_self', '_blank']) ? $button['target'] : '_self',
                                'visibility' => in_array($button['visibility'] ?? 'both', ['both', 'desktop', 'mobile']) ? $button['visibility'] : 'both'
                            ];
                        }
                    }
                }
                $_SESSION['wdm_header_utility_buttons'] = $utility_buttons;
                $success_message = 'Button settings updated successfully.';
                break;
                
            case 'update_github':
                // GitHub updater settings
                $_SESSION['wdm_enable_auto_updates'] = !empty($_POST['enable_auto_updates']);
                $_SESSION['wdm_github_username'] = sanitize_text_field($_POST['github_username'] ?? '');
                $_SESSION['wdm_github_repo'] = sanitize_text_field($_POST['github_repo'] ?? '');
                $_SESSION['wdm_github_token'] = sanitize_text_field($_POST['github_token'] ?? '');
                $success_message = 'Auto-update settings saved successfully.';
                break;
        }
    } catch (Exception $e) {
        $error_message = 'Error updating settings: ' . $e->getMessage();
    }
}

// Get current settings with defaults
$enable_header = $_SESSION['wdm_enable_header'] ?? true;
$load_default_css = $_SESSION['wdm_load_default_css'] ?? true;
$logo_url = $_SESSION['wdm_header_logo_url'] ?? 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png';

$menu_items = $_SESSION['wdm_header_menu_items'] ?? [
    [
        'title' => 'How We Serve',
        'url' => '#how-we-serve',
        'has_dropdown' => true,
        'dropdown_items' => [
            ['title' => 'Emergency Response', 'url' => '#emergency-response'],
            ['title' => 'Training Programs', 'url' => '#training'],
            ['title' => 'Community Outreach', 'url' => '#community']
        ]
    ],
    [
        'title' => 'About',
        'url' => '#about',
        'has_dropdown' => false
    ],
    [
        'title' => 'Get Involved',
        'url' => '#get-involved',
        'has_dropdown' => true,
        'dropdown_items' => [
            ['title' => 'Volunteer', 'url' => '#volunteer'],
            ['title' => 'Donate', 'url' => '#donate'],
            ['title' => 'Partner With Us', 'url' => '#partner']
        ]
    ],
    [
        'title' => 'News',
        'url' => '#news',
        'has_dropdown' => false
    ],
    [
        'title' => 'Contact',
        'url' => '#contact',
        'has_dropdown' => false
    ]
];

$utility_buttons = $_SESSION['wdm_header_utility_buttons'] ?? [
    [
        'label' => 'VOLUNTEER',
        'url' => '#volunteer',
        'class' => 'btn-volunteer',
        'target' => '_self',
        'visibility' => 'desktop'
    ],
    [
        'label' => 'DONATE',
        'url' => '#donate',
        'class' => 'btn-donate',
        'target' => '_blank',
        'visibility' => 'both'
    ]
];

// GitHub updater settings
$enable_auto_updates = $_SESSION['wdm_enable_auto_updates'] ?? true;
$github_username = $_SESSION['wdm_github_username'] ?? '';
$github_repo = $_SESSION['wdm_github_repo'] ?? '';
$github_token = $_SESSION['wdm_github_token'] ?? '';

function sanitize_text_field($string) {
    return trim(strip_tags($string));
}

function sanitize_url($url) {
    return filter_var($url, FILTER_SANITIZE_URL);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDM Header Settings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f1f1f1; color: #3c434a; line-height: 1.4; }
        .wrap { max-width: 1000px; margin: 20px auto; padding: 0 20px; }
        h1 { font-size: 23px; font-weight: 400; margin: 0 0 20px 0; color: #23282d; }
        .nav-tab-wrapper { border-bottom: 1px solid #c3c4c7; margin: 0 0 1px; padding: 0; }
        .nav-tab { background: #f0f0f1; border: 1px solid #c3c4c7; border-bottom: none; color: #50575e; text-decoration: none; display: inline-block; font-size: 14px; font-weight: 600; line-height: 24px; margin: 0; padding: 10px 12px; position: relative; cursor: pointer; }
        .nav-tab:hover { background-color: #fff; color: #135e96; }
        .nav-tab-active { background: #fff; border-bottom: 1px solid #fff; color: #000; }
        .tab-content { background: #fff; border: 1px solid #c3c4c7; padding: 20px; margin: 0; }
        .tab-pane { display: none; }
        .tab-pane.active { display: block; }
        .form-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 0.5em; }
        .form-table th { width: 200px; padding: 20px 10px 20px 0; text-align: left; vertical-align: top; font-weight: 600; color: #23282d; }
        .form-table td { padding: 15px 10px; line-height: 1.3; vertical-align: middle; }
        .form-table input[type="text"], .form-table input[type="url"], .form-table input[type="email"], .form-table select { width: 25em; padding: 6px 8px; border: 1px solid #8c8f94; border-radius: 3px; background-color: #fff; color: #2c3338; }
        .form-table p.description { margin: 2px 0 5px; color: #646970; font-size: 13px; font-style: normal; }
        .button { background: #f6f7f7; border: 1px solid #dcdcde; border-radius: 3px; color: #2c3338; cursor: pointer; display: inline-block; font-size: 13px; line-height: 2.15384615; margin: 0; padding: 0 10px; text-decoration: none; white-space: nowrap; }
        .button:hover { background: #f0f0f1; border-color: #8c8f94; color: #2c3338; }
        .button-primary { background: #2271b1; border-color: #2271b1; color: #fff; }
        .button-primary:hover { background: #135e96; border-color: #135e96; }
        .button-secondary { background: #f6f7f7; border-color: #dcdcde; color: #50575e; }
        .notice { background: #fff; border-left: 4px solid #00a32a; box-shadow: 0 1px 1px rgba(0,0,0,.04); margin: 5px 0 15px; padding: 12px; }
        .notice-success { border-left-color: #00a32a; }
        .notice-error { border-left-color: #d63638; }
        .menu-item, .utility-button { background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; overflow: hidden; }
        .item-header { padding: 15px; background: #fff; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .item-title { font-weight: 600; }
        .item-controls { display: flex; gap: 10px; }
        .item-content { padding: 20px; display: none; }
        .dropdown-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .dropdown-item input { flex: 1; width: auto; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #0073aa; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
        @media (max-width: 768px) {
            .form-table th, .form-table td { display: block; width: 100%; padding: 10px 0; }
            .form-table input[type="text"], .form-table input[type="url"], .form-table input[type="email"], .form-table select { width: 100%; }
        }
    </style>
</head>
<body>
    <div class="wrap">
        <a href="index.php" class="back-link">← Back to Dashboard</a>
        
        <h1>WDM Header Settings</h1>
        
        <?php if ($success_message): ?>
            <div class="notice notice-success"><p><?php echo esc_html($success_message); ?></p></div>
        <?php endif; ?>
        
        <?php if ($error_message): ?>
            <div class="notice notice-error"><p><?php echo esc_html($error_message); ?></p></div>
        <?php endif; ?>
        
        <nav class="nav-tab-wrapper">
            <a href="#general" class="nav-tab nav-tab-active" onclick="showTab(event, 'general')">General Settings</a>
            <a href="#menu" class="nav-tab" onclick="showTab(event, 'menu')">Menu Settings</a>
            <a href="#buttons" class="nav-tab" onclick="showTab(event, 'buttons')">Utility Buttons</a>
            <a href="#updates" class="nav-tab" onclick="showTab(event, 'updates')">Auto Updates</a>
        </nav>
        
        <div class="tab-content">
            <!-- General Settings -->
            <div id="general-tab" class="tab-pane active">
                <form method="post" action="">
                    <input type="hidden" name="action" value="update_general">
                    
                    <h3>General Settings</h3>
                    <p>Configure WDM Custom Header general settings.</p>
                    
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">Load Default CSS</th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span>Load Default CSS</span></legend>
                                        <label for="load_default_css">
                                            <input name="load_default_css" type="checkbox" id="load_default_css" value="1" <?php checked($load_default_css); ?>>
                                            Enable default header styles
                                        </label>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="logo_url">Logo URL</label></th>
                                <td>
                                    <input name="logo_url" type="url" id="logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text">
                                    <p class="description">Enter the full URL to your logo image.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <p class="submit">
                        <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Changes">
                    </p>
                </form>
            </div>
            
            <!-- Menu Settings -->
            <div id="menu-tab" class="tab-pane">
                <form method="post" action="" id="menu-form">
                    <input type="hidden" name="action" value="update_menu">
                    
                    <h3>Configure Navigation Menu</h3>
                    <p>Configure the utility buttons (Volunteer, Donate, etc.)</p>
                    
                    <h4>Configure Buttons</h4>
                    
                    <div id="menu-items">
                        <?php foreach ($menu_items as $index => $item): ?>
                            <div class="menu-item">
                                <div class="item-header">
                                    <span class="item-title"><?php echo esc_html($item['title']); ?></span>
                                    <div class="item-controls">
                                        <button type="button" class="button" onclick="toggleItem(this)">Edit</button>
                                        <button type="button" class="button" onclick="removeItem(this)">Remove</button>
                                    </div>
                                </div>
                                <div class="item-content">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">Title</th>
                                            <td><input type="text" name="menu_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">URL</th>
                                            <td><input type="text" name="menu_items[<?php echo $index; ?>][url]" value="<?php echo esc_attr($item['url']); ?>" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Has Dropdown</th>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="menu_items[<?php echo $index; ?>][has_dropdown]" value="1" <?php checked(!empty($item['has_dropdown'])); ?> onchange="toggleDropdownSection(this)">
                                                    Enable mega dropdown menu
                                                </label>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['has_dropdown'])): ?>
                                        <tr class="dropdown-section">
                                            <th scope="row">Dropdown Items</th>
                                            <td>
                                                <div class="dropdown-items">
                                                    <?php if (!empty($item['dropdown_items'])): ?>
                                                        <?php foreach ($item['dropdown_items'] as $d_index => $dropdown_item): ?>
                                                        <div class="dropdown-item">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][title]" value="<?php echo esc_attr($dropdown_item['title']); ?>" placeholder="Title">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][url]" value="<?php echo esc_attr($dropdown_item['url']); ?>" placeholder="URL">
                                                            <button type="button" class="button" onclick="removeDropdownItem(this)">Remove</button>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="button button-secondary" onclick="addDropdownItem(this, <?php echo $index; ?>)">Add Dropdown Item</button>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <p style="margin-top: 20px;">
                        <button type="button" class="button button-secondary" onclick="addMenuItem()">Add Menu Item</button>
                    </p>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Save Changes">
                    </p>
                </form>
            </div>
            
            <!-- Utility Buttons -->
            <div id="buttons-tab" class="tab-pane">
                <form method="post" action="" id="buttons-form">
                    <input type="hidden" name="action" value="update_buttons">
                    
                    <h3>Utility Buttons</h3>
                    <p>Configure the utility buttons (Volunteer, Donate, etc.)</p>
                    
                    <h4>Configure Buttons</h4>
                    
                    <div id="utility-buttons">
                        <?php foreach ($utility_buttons as $index => $button): ?>
                            <div class="utility-button">
                                <div class="item-header">
                                    <span class="item-title"><?php echo esc_html($button['label']); ?></span>
                                    <div class="item-controls">
                                        <button type="button" class="button" onclick="toggleItem(this)">Edit</button>
                                        <button type="button" class="button" onclick="removeItem(this)">Remove</button>
                                    </div>
                                </div>
                                <div class="item-content">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">Button 1</th>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Label</th>
                                            <td><input type="text" name="utility_buttons[<?php echo $index; ?>][label]" value="<?php echo esc_attr($button['label']); ?>" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">URL</th>
                                            <td><input type="text" name="utility_buttons[<?php echo $index; ?>][url]" value="<?php echo esc_attr($button['url']); ?>" class="regular-text" required></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">CSS Class</th>
                                            <td><input type="text" name="utility_buttons[<?php echo $index; ?>][class]" value="<?php echo esc_attr($button['class']); ?>" class="regular-text"></td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Target</th>
                                            <td>
                                                <select name="utility_buttons[<?php echo $index; ?>][target]">
                                                    <option value="_self" <?php selected($button['target'], '_self'); ?>>Same Window</option>
                                                    <option value="_blank" <?php selected($button['target'], '_blank'); ?>>New Window</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>
                                                <button type="button" class="button" onclick="removeItem(this.closest('.utility-button'))">Remove Button</button>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <p style="margin-top: 20px;">
                        <button type="button" class="button button-secondary" onclick="addUtilityButton()">Add Button</button>
                    </p>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Save Changes">
                    </p>
                </form>
            </div>
            
            <!-- Auto Updates -->
            <div id="updates-tab" class="tab-pane">
                <form method="post" action="">
                    <input type="hidden" name="action" value="update_github">
                    
                    <h3>Auto Updates</h3>
                    <p>Configure automatic updates from GitHub repository.</p>
                    
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">Enable Auto Updates</th>
                                <td>
                                    <fieldset>
                                        <legend class="screen-reader-text"><span>Enable Auto Updates</span></legend>
                                        <label for="enable_auto_updates">
                                            <input name="enable_auto_updates" type="checkbox" id="enable_auto_updates" value="1" <?php checked($enable_auto_updates); ?>>
                                            Automatically check for plugin updates from GitHub
                                        </label>
                                        <p class="description">When enabled, the plugin will check for new releases on GitHub and show update notifications.</p>
                                    </fieldset>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="github_username">GitHub Username</label></th>
                                <td>
                                    <input name="github_username" type="text" id="github_username" value="<?php echo esc_attr($github_username); ?>" class="regular-text" placeholder="your-username">
                                    <p class="description">The GitHub username or organization name where the repository is hosted.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="github_repo">Repository Name</label></th>
                                <td>
                                    <input name="github_repo" type="text" id="github_repo" value="<?php echo esc_attr($github_repo); ?>" class="regular-text" placeholder="wdm-custom-header">
                                    <p class="description">The name of the GitHub repository containing the plugin code.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="github_token">GitHub Token (Optional)</label></th>
                                <td>
                                    <input name="github_token" type="password" id="github_token" value="<?php echo esc_attr($github_token); ?>" class="regular-text" placeholder="ghp_xxxxxxxxxxxxxxxxxxxx">
                                    <p class="description">Personal access token for private repositories or higher rate limits. Leave empty for public repositories.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    
                    <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0; border-radius: 4px;">
                        <h4>How it works:</h4>
                        <ol style="margin-left: 20px;">
                            <li>The plugin checks for new releases on your GitHub repository</li>
                            <li>When a new version is available, you'll see an "Update Now" notification</li>
                            <li>Click "Update Now" to automatically download and install the latest version</li>
                            <li>Updates are fetched directly from GitHub releases</li>
                        </ol>
                        
                        <h4 style="margin-top: 15px;">Setup Instructions:</h4>
                        <ol style="margin-left: 20px;">
                            <li>Create a GitHub repository for your plugin</li>
                            <li>Tag releases using semantic versioning (e.g., v1.0.0, v1.1.0)</li>
                            <li>Enter your GitHub username and repository name above</li>
                            <li>For private repos, create a Personal Access Token with "repo" permissions</li>
                        </ol>
                        
                        <p style="margin-top: 15px;"><strong>Example:</strong> For repository <code>https://github.com/yourusername/wdm-custom-header</code>, enter:</p>
                        <ul style="margin-left: 20px;">
                            <li>Username: <code>yourusername</code></li>
                            <li>Repository: <code>wdm-custom-header</code></li>
                        </ul>
                    </div>
                    
                    <p class="submit">
                        <input type="submit" name="submit" class="button button-primary" value="Save Changes">
                        <button type="button" class="button button-secondary" onclick="checkForUpdates()" style="margin-left: 10px;">Check for Updates Now</button>
                    </p>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(event, tabName) {
            document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('nav-tab-active'));
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('nav-tab-active');
            event.preventDefault();
        }
        
        function toggleItem(button) {
            const content = button.closest('.menu-item, .utility-button').querySelector('.item-content');
            if (content.style.display === 'none' || content.style.display === '') {
                content.style.display = 'block';
                button.textContent = 'Close';
            } else {
                content.style.display = 'none';
                button.textContent = 'Edit';
            }
        }
        
        function removeItem(button) {
            if (confirm('Are you sure you want to remove this item?')) {
                button.closest('.menu-item, .utility-button').remove();
            }
        }
        
        function addMenuItem() {
            const container = document.getElementById('menu-items');
            const index = container.children.length;
            const template = `
                <div class="menu-item">
                    <div class="item-header">
                        <span class="item-title">New Menu Item</span>
                        <div class="item-controls">
                            <button type="button" class="button" onclick="toggleItem(this)">Edit</button>
                            <button type="button" class="button" onclick="removeItem(this)">Remove</button>
                        </div>
                    </div>
                    <div class="item-content" style="display: block;">
                        <table class="form-table">
                            <tr><th scope="row">Title</th><td><input type="text" name="menu_items[${index}][title]" value="" class="regular-text" required></td></tr>
                            <tr><th scope="row">URL</th><td><input type="text" name="menu_items[${index}][url]" value="" class="regular-text" required></td></tr>
                            <tr><th scope="row">Has Dropdown</th><td><label><input type="checkbox" name="menu_items[${index}][has_dropdown]" value="1" onchange="toggleDropdownSection(this)"> Enable mega dropdown menu</label></td></tr>
                        </table>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }
        
        function addUtilityButton() {
            const container = document.getElementById('utility-buttons');
            const index = container.children.length;
            const template = `
                <div class="utility-button">
                    <div class="item-header">
                        <span class="item-title">New Button</span>
                        <div class="item-controls">
                            <button type="button" class="button" onclick="toggleItem(this)">Edit</button>
                            <button type="button" class="button" onclick="removeItem(this)">Remove</button>
                        </div>
                    </div>
                    <div class="item-content" style="display: block;">
                        <table class="form-table">
                            <tr><th scope="row">Button ${index + 1}</th><td></td></tr>
                            <tr><th scope="row">Label</th><td><input type="text" name="utility_buttons[${index}][label]" value="" class="regular-text" required></td></tr>
                            <tr><th scope="row">URL</th><td><input type="text" name="utility_buttons[${index}][url]" value="" class="regular-text" required></td></tr>
                            <tr><th scope="row">CSS Class</th><td><input type="text" name="utility_buttons[${index}][class]" value="" class="regular-text"></td></tr>
                            <tr><th scope="row">Target</th><td><select name="utility_buttons[${index}][target]"><option value="_self">Same Window</option><option value="_blank">New Window</option></select></td></tr>
                            <tr><td><button type="button" class="button" onclick="removeItem(this.closest('.utility-button'))">Remove Button</button></td></tr>
                        </table>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }
        
        function toggleDropdownSection(checkbox) {
            const menuItem = checkbox.closest('.menu-item');
            const dropdownSection = menuItem.querySelector('.dropdown-section');
            
            if (checkbox.checked) {
                if (!dropdownSection) {
                    const menuIndex = Array.from(document.getElementById('menu-items').children).indexOf(menuItem);
                    const template = `
                        <tr class="dropdown-section">
                            <th scope="row">Dropdown Items</th>
                            <td>
                                <div class="dropdown-items"></div>
                                <button type="button" class="button button-secondary" onclick="addDropdownItem(this, ${menuIndex})">Add Dropdown Item</button>
                            </td>
                        </tr>
                    `;
                    checkbox.closest('tr').insertAdjacentHTML('afterend', template);
                } else {
                    dropdownSection.style.display = '';
                }
            } else if (dropdownSection) {
                dropdownSection.style.display = 'none';
            }
        }
        
        function addDropdownItem(button, menuIndex) {
            const container = button.closest('td').querySelector('.dropdown-items');
            const dropdownIndex = container.children.length;
            const template = `
                <div class="dropdown-item">
                    <input type="text" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][title]" value="" placeholder="Title">
                    <input type="text" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][url]" value="" placeholder="URL">
                    <button type="button" class="button" onclick="removeDropdownItem(this)">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }
        
        function removeDropdownItem(button) {
            button.closest('.dropdown-item').remove();
        }
        
        function checkForUpdates() {
            const button = event.target;
            const originalText = button.textContent;
            
            button.textContent = 'Checking...';
            button.disabled = true;
            
            // Simulate checking for updates
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'action=force_update_check'
            })
            .then(response => response.text())
            .then(() => {
                button.textContent = 'Check Complete';
                setTimeout(() => {
                    button.textContent = originalText;
                    button.disabled = false;
                    // Refresh page to show any new update notifications
                    window.location.reload();
                }, 2000);
            })
            .catch(() => {
                button.textContent = originalText;
                button.disabled = false;
            });
        }
    </script>
</body>
</html>
<?php
function esc_html($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function esc_attr($text) {
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function checked($checked, $current = true, $echo = true) {
    $result = '';
    if ($checked == $current) {
        $result = ' checked="checked"';
    }
    
    if ($echo) {
        echo $result;
    }
    
    return $result;
}

function selected($selected, $current = true, $echo = true) {
    $result = '';
    if ($selected == $current) {
        $result = ' selected="selected"';
    }
    
    if ($echo) {
        echo $result;
    }
    
    return $result;
}
?>