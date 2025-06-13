<?php
/**
 * WDM Header Settings Page
 * Dedicated settings page for header configuration
 */

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
    <title>WDM Header Settings</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; background: #f1f1f1; color: #3c434a; }
        .container { max-width: 1200px; margin: 0 auto; padding: 20px; }
        .header { background: #fff; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .nav-tabs { display: flex; background: #fff; border-radius: 8px 8px 0 0; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .nav-tab { padding: 15px 25px; background: #f9f9f9; border: none; border-right: 1px solid #ddd; cursor: pointer; font-size: 14px; transition: all 0.3s ease; }
        .nav-tab.active { background: #fff; color: #0073aa; border-bottom: 2px solid #0073aa; }
        .nav-tab:hover { background: #e9e9ea; }
        .tab-content { background: #fff; padding: 30px; border-radius: 0 0 8px 8px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .tab-pane { display: none; } .tab-pane.active { display: block; }
        .form-table { width: 100%; margin-bottom: 20px; }
        .form-table th { width: 200px; text-align: left; padding: 15px 10px 15px 0; vertical-align: top; font-weight: 600; }
        .form-table td { padding: 15px 0; }
        .form-control { width: 100%; max-width: 400px; padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; }
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; transition: all 0.3s ease; text-decoration: none; display: inline-block; }
        .btn-primary { background: #0073aa; color: #fff; } .btn-primary:hover { background: #005a87; }
        .btn-secondary { background: #f0f0f1; color: #50575e; } .btn-secondary:hover { background: #e9e9ea; }
        .btn-danger { background: #dc3232; color: #fff; } .btn-danger:hover { background: #ba2626; }
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; border-left: 4px solid; }
        .alert-success { background: #d4edda; border-color: #28a745; color: #155724; }
        .menu-item, .utility-button { background: #f9f9f9; border: 1px solid #ddd; border-radius: 4px; margin-bottom: 15px; overflow: hidden; }
        .item-header { padding: 15px; background: #fff; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
        .item-title { font-weight: 600; }
        .item-controls { display: flex; gap: 10px; }
        .item-content { padding: 20px; display: none; }
        .dropdown-item { display: flex; gap: 10px; margin-bottom: 10px; align-items: center; }
        .dropdown-item input { flex: 1; }
        .color-input { width: 60px; height: 40px; border: 1px solid #ddd; border-radius: 4px; cursor: pointer; }
        .back-link { display: inline-block; margin-bottom: 20px; color: #0073aa; text-decoration: none; }
        .back-link:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <a href="../wp-admin/" class="back-link">← Back to Dashboard</a>
        
        <div class="header">
            <h1>WDM Header Settings</h1>
            <p>Manage your website header configuration including logo, navigation menu, and utility buttons.</p>
        </div>
        
        <?php if (isset($success_message)): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>
        
        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('logo')">Logo Settings</button>
            <button class="nav-tab" onclick="showTab('menu')">Navigation Menu</button>
            <button class="nav-tab" onclick="showTab('buttons')">Utility Buttons</button>
            <button class="nav-tab" onclick="showTab('colors')">Colors</button>
        </div>
        
        <div class="tab-content">
            <!-- Logo Settings -->
            <div id="logo-tab" class="tab-pane active">
                <h2>Logo Settings</h2>
                <form method="post">
                    <input type="hidden" name="action" value="save_logo">
                    <table class="form-table">
                        <tr>
                            <th>Logo URL</th>
                            <td>
                                <input type="url" name="logo_url" value="<?php echo htmlspecialchars($logo_url); ?>" class="form-control" required>
                                <p style="margin-top: 5px; color: #666; font-size: 13px;">Enter the URL for your logo image.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Logo Alt Text</th>
                            <td>
                                <input type="text" name="logo_alt" value="<?php echo htmlspecialchars($logo_alt); ?>" class="form-control" required>
                                <p style="margin-top: 5px; color: #666; font-size: 13px;">Alt text for accessibility.</p>
                            </td>
                        </tr>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Logo Settings</button>
                </form>
            </div>
            
            <!-- Navigation Menu -->
            <div id="menu-tab" class="tab-pane">
                <h2>Navigation Menu</h2>
                <form method="post" id="menu-form">
                    <input type="hidden" name="action" value="save_menu_items">
                    <div id="menu-items">
                        <?php foreach ($menu_items as $index => $item): ?>
                            <div class="menu-item">
                                <div class="item-header">
                                    <span class="item-title"><?php echo htmlspecialchars($item['title']); ?></span>
                                    <div class="item-controls">
                                        <button type="button" class="btn btn-secondary" onclick="toggleItem(this)">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                                    </div>
                                </div>
                                <div class="item-content">
                                    <table class="form-table">
                                        <tr>
                                            <th>Title</th>
                                            <td><input type="text" name="menu_items[<?php echo $index; ?>][title]" value="<?php echo htmlspecialchars($item['title']); ?>" class="form-control" required></td>
                                        </tr>
                                        <tr>
                                            <th>URL</th>
                                            <td><input type="text" name="menu_items[<?php echo $index; ?>][url]" value="<?php echo htmlspecialchars($item['url']); ?>" class="form-control" required></td>
                                        </tr>
                                        <tr>
                                            <th>Has Dropdown</th>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="menu_items[<?php echo $index; ?>][has_dropdown]" value="1" <?php echo !empty($item['has_dropdown']) ? 'checked' : ''; ?>>
                                                    Enable mega dropdown menu
                                                </label>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['has_dropdown'])): ?>
                                        <tr class="dropdown-section">
                                            <th>Dropdown Items</th>
                                            <td>
                                                <div class="dropdown-items">
                                                    <?php if (!empty($item['dropdown_items'])): ?>
                                                        <?php foreach ($item['dropdown_items'] as $d_index => $dropdown_item): ?>
                                                        <div class="dropdown-item">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][title]" value="<?php echo htmlspecialchars($dropdown_item['title']); ?>" placeholder="Title" class="form-control">
                                                            <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $d_index; ?>][url]" value="<?php echo htmlspecialchars($dropdown_item['url']); ?>" placeholder="URL" class="form-control">
                                                            <button type="button" class="btn btn-danger" onclick="removeDropdownItem(this)">Remove</button>
                                                        </div>
                                                        <?php endforeach; ?>
                                                    <?php endif; ?>
                                                </div>
                                                <button type="button" class="btn btn-secondary" onclick="addDropdownItem(this, <?php echo $index; ?>)">Add Dropdown Item</button>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div style="margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="addMenuItem()">Add Menu Item</button>
                        <button type="submit" class="btn btn-primary">Save Menu Items</button>
                    </div>
                </form>
            </div>
            
            <!-- Utility Buttons -->
            <div id="buttons-tab" class="tab-pane">
                <h2>Utility Buttons</h2>
                <form method="post" id="buttons-form">
                    <input type="hidden" name="action" value="save_utility_buttons">
                    <div id="utility-buttons">
                        <?php foreach ($utility_buttons as $index => $button): ?>
                            <div class="utility-button">
                                <div class="item-header">
                                    <span class="item-title"><?php echo htmlspecialchars($button['label']); ?></span>
                                    <div class="item-controls">
                                        <button type="button" class="btn btn-secondary" onclick="toggleItem(this)">Edit</button>
                                        <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                                    </div>
                                </div>
                                <div class="item-content">
                                    <table class="form-table">
                                        <tr>
                                            <th>Label</th>
                                            <td><input type="text" name="utility_buttons[<?php echo $index; ?>][label]" value="<?php echo htmlspecialchars($button['label']); ?>" class="form-control" required></td>
                                        </tr>
                                        <tr>
                                            <th>URL</th>
                                            <td><input type="text" name="utility_buttons[<?php echo $index; ?>][url]" value="<?php echo htmlspecialchars($button['url']); ?>" class="form-control" required></td>
                                        </tr>
                                        <tr>
                                            <th>CSS Class</th>
                                            <td>
                                                <input type="text" name="utility_buttons[<?php echo $index; ?>][class]" value="<?php echo htmlspecialchars($button['class']); ?>" class="form-control">
                                                <p style="margin-top: 5px; color: #666; font-size: 13px;">Custom CSS class for styling (e.g., btn-volunteer, btn-donate)</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Target</th>
                                            <td>
                                                <select name="utility_buttons[<?php echo $index; ?>][target]" class="form-control">
                                                    <option value="_self" <?php echo $button['target'] === '_self' ? 'selected' : ''; ?>>Same window</option>
                                                    <option value="_blank" <?php echo $button['target'] === '_blank' ? 'selected' : ''; ?>>New window</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>Visibility</th>
                                            <td>
                                                <select name="utility_buttons[<?php echo $index; ?>][visibility]" class="form-control">
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
                    <div style="margin-top: 20px;">
                        <button type="button" class="btn btn-secondary" onclick="addUtilityButton()">Add Utility Button</button>
                        <button type="submit" class="btn btn-primary">Save Utility Buttons</button>
                    </div>
                </form>
            </div>
            
            <!-- Colors -->
            <div id="colors-tab" class="tab-pane">
                <h2>Color Settings</h2>
                <form method="post">
                    <input type="hidden" name="action" value="save_colors">
                    <table class="form-table">
                        <tr>
                            <th>Primary Color</th>
                            <td>
                                <input type="color" name="primary_color" value="<?php echo htmlspecialchars($primary_color); ?>" class="color-input">
                                <p style="margin-top: 5px; color: #666; font-size: 13px;">Main header background color.</p>
                            </td>
                        </tr>
                        <tr>
                            <th>Secondary Color</th>
                            <td>
                                <input type="color" name="secondary_color" value="<?php echo htmlspecialchars($secondary_color); ?>" class="color-input">
                                <p style="margin-top: 5px; color: #666; font-size: 13px;">Button and accent color.</p>
                            </td>
                        </tr>
                    </table>
                    <button type="submit" class="btn btn-primary">Save Color Settings</button>
                </form>
            </div>
        </div>
    </div>
    
    <script>
        function showTab(tabName) {
            document.querySelectorAll('.tab-pane').forEach(tab => tab.classList.remove('active'));
            document.querySelectorAll('.nav-tab').forEach(tab => tab.classList.remove('active'));
            document.getElementById(tabName + '-tab').classList.add('active');
            event.target.classList.add('active');
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
            container.insertAdjacentHTML('beforeend', `
                <div class="menu-item">
                    <div class="item-header">
                        <span class="item-title">New Menu Item</span>
                        <div class="item-controls">
                            <button type="button" class="btn btn-secondary" onclick="toggleItem(this)">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                        </div>
                    </div>
                    <div class="item-content" style="display: block;">
                        <table class="form-table">
                            <tr><th>Title</th><td><input type="text" name="menu_items[${index}][title]" value="" class="form-control" required></td></tr>
                            <tr><th>URL</th><td><input type="text" name="menu_items[${index}][url]" value="" class="form-control" required></td></tr>
                            <tr><th>Has Dropdown</th><td><label><input type="checkbox" name="menu_items[${index}][has_dropdown]" value="1"> Enable mega dropdown menu</label></td></tr>
                        </table>
                    </div>
                </div>
            `);
        }
        
        function addUtilityButton() {
            const container = document.getElementById('utility-buttons');
            const index = container.children.length;
            container.insertAdjacentHTML('beforeend', `
                <div class="utility-button">
                    <div class="item-header">
                        <span class="item-title">New Button</span>
                        <div class="item-controls">
                            <button type="button" class="btn btn-secondary" onclick="toggleItem(this)">Edit</button>
                            <button type="button" class="btn btn-danger" onclick="removeItem(this)">Remove</button>
                        </div>
                    </div>
                    <div class="item-content" style="display: block;">
                        <table class="form-table">
                            <tr><th>Label</th><td><input type="text" name="utility_buttons[${index}][label]" value="" class="form-control" required></td></tr>
                            <tr><th>URL</th><td><input type="text" name="utility_buttons[${index}][url]" value="" class="form-control" required></td></tr>
                            <tr><th>CSS Class</th><td><input type="text" name="utility_buttons[${index}][class]" value="" class="form-control"></td></tr>
                            <tr><th>Target</th><td><select name="utility_buttons[${index}][target]" class="form-control"><option value="_self">Same window</option><option value="_blank">New window</option></select></td></tr>
                            <tr><th>Visibility</th><td><select name="utility_buttons[${index}][visibility]" class="form-control"><option value="both">Desktop & Mobile</option><option value="desktop">Desktop only</option><option value="mobile">Mobile only</option></select></td></tr>
                        </table>
                    </div>
                </div>
            `);
        }
        
        function removeDropdownItem(button) {
            button.closest('.dropdown-item').remove();
        }
        
        function addDropdownItem(button, menuIndex) {
            const container = button.closest('td').querySelector('.dropdown-items');
            const dropdownIndex = container.children.length;
            const template = `
                <div class="dropdown-item">
                    <input type="text" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][title]" value="" placeholder="Title" class="form-control">
                    <input type="text" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][url]" value="" placeholder="URL" class="form-control">
                    <button type="button" class="btn btn-danger" onclick="removeDropdownItem(this)">Remove</button>
                </div>
            `;
            container.insertAdjacentHTML('beforeend', template);
        }
    </script>
</body>
</html>