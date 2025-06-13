<?php
/**
 * WordPress Settings - WDM Header Integration
 * Provides WordPress-style settings page for WDM Header
 */

// Load WordPress functions fallback
require_once '../includes/wordpress-functions.php';

// Start session
session_start();

// Simulate WordPress admin environment
define('WP_ADMIN', true);
define('ABSPATH', dirname(__DIR__) . '/');

// Check if this is the WDM Header settings page
$page = $_GET['page'] ?? '';
$is_wdm_header_page = ($page === 'wdm-header-settings');

// If no page parameter, show general settings or redirect to dashboard
if (empty($page)) {
    header('Location: index.php');
    exit;
}

// If not our page, show a simple message
if (!$is_wdm_header_page) {
    echo '<h1>WordPress General Settings</h1>';
    echo '<p>This would be the general WordPress settings page.</p>';
    echo '<p><a href="?page=wdm-header-settings">Go to WDM Header Settings</a></p>';
    exit;
}

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'save_logo':
                $_SESSION['wdm_header_logo_url'] = $_POST['logo_url'];
                $_SESSION['wdm_header_logo_alt'] = $_POST['logo_alt'];
                $success_message = 'Logo settings saved successfully!';
                break;
                
            case 'save_menu_items':
                $_SESSION['wdm_header_menu_items'] = $_POST['menu_items'] ?? [];
                $success_message = 'Menu items saved successfully!';
                break;
                
            case 'save_utility_buttons':
                $_SESSION['wdm_header_utility_buttons'] = $_POST['utility_buttons'] ?? [];
                $success_message = 'Utility buttons saved successfully!';
                break;
                
            case 'save_colors':
                $_SESSION['wdm_header_primary_color'] = $_POST['primary_color'];
                $_SESSION['wdm_header_secondary_color'] = $_POST['secondary_color'];
                $success_message = 'Color settings saved successfully!';
                break;
        }
    }
}

// Get current settings
$logo_url = $_SESSION['wdm_header_logo_url'] ?? 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png';
$logo_alt = $_SESSION['wdm_header_logo_alt'] ?? 'Greybull Rescue';
$menu_items = $_SESSION['wdm_header_menu_items'] ?? get_default_menu_items();
$utility_buttons = $_SESSION['wdm_header_utility_buttons'] ?? get_default_utility_buttons();
$primary_color = $_SESSION['wdm_header_primary_color'] ?? '#1a365d';
$secondary_color = $_SESSION['wdm_header_secondary_color'] ?? '#2d3748';

function get_default_menu_items() {
    return array(
        array(
            'title' => 'How We Serve',
            'url' => '#how-we-serve',
            'has_dropdown' => true,
            'dropdown_items' => array(
                array('title' => 'Emergency Response', 'url' => '#emergency-response'),
                array('title' => 'Training Programs', 'url' => '#training'),
                array('title' => 'Community Outreach', 'url' => '#community')
            )
        ),
        array(
            'title' => 'About',
            'url' => '#about',
            'has_dropdown' => false
        ),
        array(
            'title' => 'Get Involved',
            'url' => '#get-involved',
            'has_dropdown' => true,
            'dropdown_items' => array(
                array('title' => 'Volunteer', 'url' => '#volunteer'),
                array('title' => 'Donate', 'url' => '#donate'),
                array('title' => 'Partner With Us', 'url' => '#partner')
            )
        ),
        array(
            'title' => 'News',
            'url' => '#news',
            'has_dropdown' => false
        ),
        array(
            'title' => 'Contact',
            'url' => '#contact',
            'has_dropdown' => false
        )
    );
}

function get_default_utility_buttons() {
    return array(
        array(
            'label' => 'VOLUNTEER',
            'url' => '#volunteer',
            'class' => 'btn-volunteer',
            'target' => '_self',
            'visibility' => 'desktop'
        ),
        array(
            'label' => 'DONATE',
            'url' => '#donate',
            'class' => 'btn-donate',
            'target' => '_blank',
            'visibility' => 'both'
        )
    );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDM Header Settings - WordPress Admin</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f1f1f1;
            color: #3c434a;
        }
        
        .wp-admin {
            display: flex;
            min-height: 100vh;
        }
        
        .admin-menu {
            width: 160px;
            background: #23282d;
            color: #fff;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
        }
        
        .admin-menu .wp-menu-separator {
            height: 5px;
            background: #32373c;
            margin: 0;
        }
        
        .admin-menu .menu-top {
            position: relative;
        }
        
        .admin-menu .wp-menu-name {
            padding: 8px 20px;
            display: block;
            color: #e5e5e5;
            text-decoration: none;
            font-size: 13px;
            line-height: 18px;
            border: none;
            background: none;
            width: 100%;
            text-align: left;
            cursor: pointer;
        }
        
        .admin-menu .wp-menu-name:hover {
            background: #0073aa;
            color: #fff;
        }
        
        .admin-menu .wp-submenu {
            background: #32373c;
            display: none;
        }
        
        .admin-menu .wp-submenu.open {
            display: block;
        }
        
        .admin-menu .wp-submenu a {
            padding: 8px 20px 8px 35px;
            display: block;
            color: #e5e5e5;
            text-decoration: none;
            font-size: 13px;
        }
        
        .admin-menu .wp-submenu a:hover {
            background: #0073aa;
            color: #fff;
        }
        
        .admin-menu .wp-submenu a.current {
            background: #0073aa;
            color: #fff;
            font-weight: 600;
        }
        
        .admin-content {
            margin-left: 160px;
            flex: 1;
            padding: 20px;
        }
        
        .wrap {
            margin: 0;
        }
        
        .wrap h1 {
            font-size: 23px;
            font-weight: 400;
            color: #23282d;
            margin: 0 0 20px 0;
            padding: 0;
        }
        
        .nav-tab-wrapper {
            border-bottom: 1px solid #c3c4c7;
            margin: 0 0 1px;
            padding: 0;
        }
        
        .nav-tab {
            background: #f0f0f1;
            border: 1px solid #c3c4c7;
            border-bottom: none;
            color: #50575e;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            font-weight: 600;
            line-height: 24px;
            margin: 0;
            padding: 10px 12px;
            position: relative;
        }
        
        .nav-tab:hover {
            background-color: #fff;
            color: #135e96;
        }
        
        .nav-tab-active {
            background: #fff;
            border-bottom: 1px solid #fff;
            color: #000;
        }
        
        .tab-content {
            background: #fff;
            border: 1px solid #c3c4c7;
            padding: 20px;
            margin: 0;
        }
        
        .tab-pane {
            display: none;
        }
        
        .tab-pane.active {
            display: block;
        }
        
        .form-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-top: 0.5em;
        }
        
        .form-table th {
            width: 200px;
            padding: 20px 10px 20px 0;
            text-align: left;
            vertical-align: top;
            font-weight: 600;
            color: #23282d;
        }
        
        .form-table td {
            padding: 15px 10px;
            line-height: 1.3;
            vertical-align: middle;
        }
        
        .form-table input[type="text"],
        .form-table input[type="url"],
        .form-table input[type="email"],
        .form-table select {
            width: 25em;
            padding: 6px 8px;
            border: 1px solid #8c8f94;
            border-radius: 3px;
            background-color: #fff;
            color: #2c3338;
        }
        
        .form-table input[type="color"] {
            width: 60px;
            height: 40px;
            padding: 0;
            border: 1px solid #8c8f94;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .form-table p.description {
            margin: 2px 0 5px;
            color: #646970;
            font-size: 13px;
            font-style: normal;
        }
        
        .button {
            background: #f6f7f7;
            border: 1px solid #dcdcde;
            border-radius: 3px;
            color: #2c3338;
            cursor: pointer;
            display: inline-block;
            font-size: 13px;
            line-height: 2.15384615;
            margin: 0;
            padding: 0 10px;
            text-decoration: none;
            white-space: nowrap;
        }
        
        .button:hover {
            background: #f0f0f1;
            border-color: #8c8f94;
            color: #2c3338;
        }
        
        .button-primary {
            background: #2271b1;
            border-color: #2271b1;
            color: #fff;
        }
        
        .button-primary:hover {
            background: #135e96;
            border-color: #135e96;
        }
        
        .button-secondary {
            background: #f6f7f7;
            border-color: #dcdcde;
            color: #50575e;
        }
        
        .notice {
            background: #fff;
            border-left: 4px solid #00a32a;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            margin: 5px 0 15px;
            padding: 12px;
        }
        
        .notice-success {
            border-left-color: #00a32a;
        }
        
        .menu-item,
        .utility-button {
            background: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            margin-bottom: 15px;
            overflow: hidden;
        }
        
        .item-header {
            padding: 15px;
            background: #fff;
            border-bottom: 1px solid #ddd;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .item-title {
            font-weight: 600;
        }
        
        .item-controls {
            display: flex;
            gap: 10px;
        }
        
        .item-content {
            padding: 20px;
            display: none;
        }
        
        .dropdown-item {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }
        
        .dropdown-item input {
            flex: 1;
            width: auto;
        }
        
        @media (max-width: 768px) {
            .admin-menu {
                width: 36px;
            }
            
            .admin-content {
                margin-left: 36px;
            }
            
            .form-table th,
            .form-table td {
                display: block;
                width: 100%;
                padding: 10px 0;
            }
            
            .form-table input[type="text"],
            .form-table input[type="url"],
            .form-table input[type="email"],
            .form-table select {
                width: 100%;
            }
        }
    </style>
</head>
<body class="wp-admin">
    <div class="admin-menu">
        <div class="wp-menu-separator"></div>
        
        <div class="menu-top">
            <a class="wp-menu-name" href="index.php">Dashboard</a>
        </div>
        
        <div class="wp-menu-separator"></div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('posts')">Posts</button>
        </div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('media')">Media</button>
        </div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('pages')">Pages</button>
        </div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('appearance')">Appearance</button>
            <ul class="wp-submenu" id="appearance-submenu">
                <li><a href="#themes">Themes</a></li>
                <li><a href="#customize">Customize</a></li>
                <li><a href="#widgets">Widgets</a></li>
                <li><a href="#menus">Menus</a></li>
            </ul>
        </div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('plugins')">Plugins</button>
        </div>
        
        <div class="wp-menu-separator"></div>
        
        <div class="menu-top">
            <button class="wp-menu-name" onclick="toggleSubmenu('settings')">Settings</button>
            <ul class="wp-submenu open" id="settings-submenu">
                <li><a href="#general">General</a></li>
                <li><a href="#writing">Writing</a></li>
                <li><a href="#reading">Reading</a></li>
                <li><a href="options-general.php?page=wdm-header-settings" class="current">WDM Header</a></li>
            </ul>
        </div>
    </div>
    
    <div class="admin-content">
        <div class="wrap">
            <h1>WDM Header Settings</h1>
            
            <?php if (isset($success_message)): ?>
                <div class="notice notice-success">
                    <p><?php echo htmlspecialchars($success_message); ?></p>
                </div>
            <?php endif; ?>
            
            <nav class="nav-tab-wrapper">
                <a href="#logo" class="nav-tab nav-tab-active" onclick="showTab(event, 'logo')">Logo Settings</a>
                <a href="#menu" class="nav-tab" onclick="showTab(event, 'menu')">Navigation Menu</a>
                <a href="#buttons" class="nav-tab" onclick="showTab(event, 'buttons')">Utility Buttons</a>
                <a href="#colors" class="nav-tab" onclick="showTab(event, 'colors')">Colors</a>
            </nav>
            
            <div class="tab-content">
                <!-- Logo Settings -->
                <div id="logo-tab" class="tab-pane active">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="save_logo">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="logo_url">Logo URL</label></th>
                                    <td>
                                        <input name="logo_url" type="url" id="logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text" required>
                                        <p class="description">Enter the URL for your logo image.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="logo_alt">Logo Alt Text</label></th>
                                    <td>
                                        <input name="logo_alt" type="text" id="logo_alt" value="<?php echo esc_attr($logo_alt); ?>" class="regular-text" required>
                                        <p class="description">Alt text for accessibility.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Logo Settings">
                        </p>
                    </form>
                </div>
                
                <!-- Navigation Menu -->
                <div id="menu-tab" class="tab-pane">
                    <form method="post" action="" id="menu-form">
                        <input type="hidden" name="action" value="save_menu_items">
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
                                                <td>
                                                    <input type="text" name="menu_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="regular-text" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">URL</th>
                                                <td>
                                                    <input type="text" name="menu_items[<?php echo $index; ?>][url]" value="<?php echo esc_attr($item['url']); ?>" class="regular-text" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Has Dropdown</th>
                                                <td>
                                                    <label>
                                                        <input type="checkbox" name="menu_items[<?php echo $index; ?>][has_dropdown]" value="1" <?php echo !empty($item['has_dropdown']) ? 'checked' : ''; ?>>
                                                        Enable mega dropdown menu
                                                    </label>
                                                </td>
                                            </tr>
                                            <?php if (!empty($item['has_dropdown']) && !empty($item['dropdown_items'])): ?>
                                            <tr class="dropdown-section">
                                                <th scope="row">Dropdown Items</th>
                                                <td>
                                                    <div class="dropdown-items">
                                                        <?php foreach ($item['dropdown_items'] as $d_index => $dropdown_item): ?>
                                                        <div class="dropdown-item">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][title]" value="<?php echo esc_attr($dropdown_item['title']); ?>" placeholder="Title">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][url]" value="<?php echo esc_attr($dropdown_item['url']); ?>" placeholder="URL">
                                                            <button type="button" class="button" onclick="removeDropdownItem(this)">Remove</button>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                    <button type="button" class="button" onclick="addDropdownItem(this)">Add Dropdown Item</button>
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
                            <input type="submit" name="submit" class="button button-primary" value="Save Menu Items">
                        </p>
                    </form>
                </div>
                
                <!-- Utility Buttons -->
                <div id="buttons-tab" class="tab-pane">
                    <form method="post" action="" id="buttons-form">
                        <input type="hidden" name="action" value="save_utility_buttons">
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
                                                <th scope="row">Label</th>
                                                <td>
                                                    <input type="text" name="utility_buttons[<?php echo $index; ?>][label]" value="<?php echo esc_attr($button['label']); ?>" class="regular-text" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">URL</th>
                                                <td>
                                                    <input type="text" name="utility_buttons[<?php echo $index; ?>][url]" value="<?php echo esc_attr($button['url']); ?>" class="regular-text" required>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">CSS Class</th>
                                                <td>
                                                    <input type="text" name="utility_buttons[<?php echo $index; ?>][class]" value="<?php echo esc_attr($button['class']); ?>" class="regular-text">
                                                    <p class="description">Custom CSS class for styling (e.g., btn-volunteer, btn-donate)</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Target</th>
                                                <td>
                                                    <select name="utility_buttons[<?php echo $index; ?>][target]">
                                                        <option value="_self" <?php echo $button['target'] === '_self' ? 'selected' : ''; ?>>Same window</option>
                                                        <option value="_blank" <?php echo $button['target'] === '_blank' ? 'selected' : ''; ?>>New window</option>
                                                    </select>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th scope="row">Visibility</th>
                                                <td>
                                                    <select name="utility_buttons[<?php echo $index; ?>][visibility]">
                                                        <option value="both" <?php echo ($button['visibility'] ?? 'both') === 'both' ? 'selected' : ''; ?>>Desktop & Mobile</option>
                                                        <option value="desktop" <?php echo ($button['visibility'] ?? 'both') === 'desktop' ? 'selected' : ''; ?>>Desktop only</option>
                                                        <option value="mobile" <?php echo ($button['visibility'] ?? 'both') === 'mobile' ? 'selected' : ''; ?>>Mobile only</option>
                                                    </select>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <p style="margin-top: 20px;">
                            <button type="button" class="button button-secondary" onclick="addUtilityButton()">Add Utility Button</button>
                            <input type="submit" name="submit" class="button button-primary" value="Save Utility Buttons">
                        </p>
                    </form>
                </div>
                
                <!-- Colors -->
                <div id="colors-tab" class="tab-pane">
                    <form method="post" action="">
                        <input type="hidden" name="action" value="save_colors">
                        <table class="form-table" role="presentation">
                            <tbody>
                                <tr>
                                    <th scope="row"><label for="primary_color">Primary Color</label></th>
                                    <td>
                                        <input name="primary_color" type="color" id="primary_color" value="<?php echo esc_attr($primary_color); ?>">
                                        <p class="description">Main header background color.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row"><label for="secondary_color">Secondary Color</label></th>
                                    <td>
                                        <input name="secondary_color" type="color" id="secondary_color" value="<?php echo esc_attr($secondary_color); ?>">
                                        <p class="description">Button and accent color.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        
                        <p class="submit">
                            <input type="submit" name="submit" id="submit" class="button button-primary" value="Save Color Settings">
                        </p>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(event, tabName) {
            // Remove active class from all tabs and nav links
            document.querySelectorAll('.tab-pane').forEach(tab => {
                tab.classList.remove('active');
            });
            document.querySelectorAll('.nav-tab').forEach(tab => {
                tab.classList.remove('nav-tab-active');
            });
            
            // Show selected tab and mark nav as active
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('nav-tab-active');
            
            event.preventDefault();
        }
        
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId + '-submenu');
            if (submenu) {
                submenu.classList.toggle('open');
            }
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
                            <tr>
                                <th scope="row">Title</th>
                                <td><input type="text" name="menu_items[${index}][title]" value="" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row">URL</th>
                                <td><input type="text" name="menu_items[${index}][url]" value="" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row">Has Dropdown</th>
                                <td>
                                    <label>
                                        <input type="checkbox" name="menu_items[${index}][has_dropdown]" value="1">
                                        Enable mega dropdown menu
                                    </label>
                                </td>
                            </tr>
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
                            <tr>
                                <th scope="row">Label</th>
                                <td><input type="text" name="utility_buttons[${index}][label]" value="" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row">URL</th>
                                <td><input type="text" name="utility_buttons[${index}][url]" value="" class="regular-text" required></td>
                            </tr>
                            <tr>
                                <th scope="row">CSS Class</th>
                                <td><input type="text" name="utility_buttons[${index}][class]" value="" class="regular-text"></td>
                            </tr>
                            <tr>
                                <th scope="row">Target</th>
                                <td>
                                    <select name="utility_buttons[${index}][target]">
                                        <option value="_self">Same window</option>
                                        <option value="_blank">New window</option>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Visibility</th>
                                <td>
                                    <select name="utility_buttons[${index}][visibility]">
                                        <option value="both">Desktop & Mobile</option>
                                        <option value="desktop">Desktop only</option>
                                        <option value="mobile">Mobile only</option>
                                    </select>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }
        
        function removeDropdownItem(button) {
            button.closest('.dropdown-item').remove();
        }
        
        function addDropdownItem(button) {
            // Implementation for adding dropdown items would go here
            console.log('Add dropdown item functionality');
        }
    </script>
</body>
</html>