<?php
/**
 * WordPress Admin Demo Page
 * Simulates the WordPress admin environment to test the WDM Header plugin
 */

// Load WordPress environment
require_once 'wp-config.php';

// Handle form submissions
if ($_POST && isset($_POST['option_page']) && $_POST['option_page'] === 'wdm_header_settings') {
    if (isset($_POST['wdm_header_options'])) {
        update_option('wdm_header_options', $_POST['wdm_header_options']);
        add_settings_error('wdm_header_messages', 'wdm_header_message', 'Settings Saved', 'updated');
    }
    
    // Redirect to prevent resubmission
    header('Location: ' . $_SERVER['PHP_SELF'] . '?settings-updated=true');
    exit;
}

// Trigger admin_menu action to register pages
do_action('admin_menu');

// Trigger admin_init to register settings
do_action('admin_init');

// Get the admin page
global $wp_admin_pages;
$page_slug = 'wdm-header-settings';

if (!isset($wp_admin_pages[$page_slug])) {
    die('Admin page not found. Plugin may not be properly loaded.');
}

$page = $wp_admin_pages[$page_slug];

// Trigger admin_enqueue_scripts
do_action('admin_enqueue_scripts', 'settings_page_' . $page_slug);

// Check if settings were updated
if (isset($_GET['settings-updated'])) {
    add_settings_error('wdm_header_messages', 'wdm_header_message', 'Settings Saved', 'updated');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin - <?php echo esc_html($page['title']); ?></title>
    <style>
        /* WordPress Admin Styles */
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f1f1f1;
            color: #3c434a;
            line-height: 1.4;
            margin: 0;
            padding: 20px;
        }
        
        .wrap {
            max-width: 1000px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.13);
        }
        
        h1 {
            font-size: 23px;
            font-weight: 400;
            margin: 0 0 20px 0;
            color: #23282d;
            border-bottom: 1px solid #e1e1e1;
            padding-bottom: 10px;
        }
        
        h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 30px 0 15px 0;
            color: #23282d;
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
        .form-table input[type="password"],
        .form-table input[type="url"],
        .form-table select {
            width: 25em;
            padding: 6px 8px;
            border: 1px solid #8c8f94;
            border-radius: 3px;
            background-color: #fff;
            color: #2c3338;
        }
        
        .form-table input[type="checkbox"] {
            margin-right: 8px;
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
        
        .notice {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-left-width: 4px;
            box-shadow: 0 1px 1px rgba(0,0,0,.04);
            margin: 5px 0 15px;
            padding: 1px 12px;
        }
        
        .notice-updated {
            border-left-color: #00a32a;
        }
        
        .notice-error {
            border-left-color: #d63638;
        }
        
        .wdm-menu-item, .wdm-button-item {
            border: 1px solid #ddd;
            padding: 15px;
            margin: 10px 0;
            background: #f9f9f9;
            border-radius: 4px;
        }
        
        .wdm-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }
        
        .wdm-item-content {
            display: none;
        }
        
        .wdm-item-content.active {
            display: block;
        }
        
        .wdm-version-footer {
            margin-top: 30px;
            padding: 15px;
            background: #f1f1f1;
            border-left: 4px solid #0073aa;
            font-size: 13px;
            color: #666;
            border-radius: 0 4px 4px 0;
        }
        
        .admin-nav {
            background: #23282d;
            color: #fff;
            padding: 10px 20px;
            margin: -20px -20px 20px -20px;
            border-radius: 4px 4px 0 0;
        }
        
        .admin-nav h2 {
            margin: 0;
            color: #fff;
            font-size: 16px;
        }
    </style>
</head>
<body>
    <div class="admin-nav">
        <h2>WordPress Admin → Settings → WDM Header</h2>
    </div>
    
    <div class="wrap">
        <h1><?php echo esc_html($page['title']); ?></h1>
        
        <?php 
        // Display settings errors/messages
        settings_errors('wdm_header_messages'); 
        ?>
        
        <?php
        // Display admin notices
        do_action('admin_notices');
        ?>
        
        <?php
        // Call the page callback function
        if ($page['callback']) {
            call_user_func($page['callback']);
        }
        ?>
    </div>
    
    <script>
        // Admin JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Toggle item content
            document.querySelectorAll('.wdm-toggle-item').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    var content = this.closest('.wdm-menu-item, .wdm-button-item').querySelector('.wdm-item-content');
                    content.classList.toggle('active');
                    this.textContent = content.classList.contains('active') ? 'Close' : 'Edit';
                });
            });
            
            // Remove item
            document.querySelectorAll('.wdm-remove-item').forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to remove this item?')) {
                        this.closest('.wdm-menu-item, .wdm-button-item').remove();
                    }
                });
            });
            
            // Check for updates
            var checkUpdatesBtn = document.getElementById('wdm-check-updates');
            if (checkUpdatesBtn) {
                checkUpdatesBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var button = this;
                    var originalText = button.textContent;
                    
                    button.textContent = 'Checking...';
                    button.disabled = true;
                    
                    // Simulate update check
                    setTimeout(function() {
                        button.textContent = 'Check Complete';
                        setTimeout(function() {
                            button.textContent = originalText;
                            button.disabled = false;
                            // Show update notice
                            var notice = document.createElement('div');
                            notice.className = 'notice notice-updated';
                            notice.innerHTML = '<p>Update check completed. No new updates found.</p>';
                            document.querySelector('.wrap h1').after(notice);
                        }, 2000);
                    }, 1500);
                });
            }
        });
    </script>
</body>
</html>