<?php
/**
 * WordPress Admin Dashboard - WDM Header Integration
 * This creates a WordPress-like admin experience
 */

// Load WordPress functions fallback
require_once '../includes/wordpress-functions.php';

// Start session
session_start();

// Simulate WordPress admin environment
define('WP_ADMIN', true);
define('ABSPATH', dirname(__DIR__) . '/');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WordPress Admin Dashboard</title>
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
        }
        
        .admin-content {
            margin-left: 160px;
            flex: 1;
            padding: 20px;
        }
        
        .admin-header {
            background: #fff;
            padding: 0;
            margin: -20px -20px 20px -20px;
            border-bottom: 1px solid #ddd;
        }
        
        .admin-header h1 {
            padding: 23px 20px;
            font-size: 23px;
            font-weight: 400;
            color: #23282d;
        }
        
        .dashboard-widgets {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        
        .dashboard-widget {
            background: #fff;
            border: 1px solid #c3c4c7;
            border-radius: 4px;
        }
        
        .dashboard-widget h3 {
            background: #f6f7f7;
            padding: 15px 20px;
            margin: 0;
            border-bottom: 1px solid #c3c4c7;
            font-size: 14px;
            font-weight: 600;
        }
        
        .dashboard-widget .inside {
            padding: 20px;
        }
        
        .btn {
            background: #2271b1;
            color: #fff;
            border: 1px solid #2271b1;
            padding: 8px 12px;
            border-radius: 3px;
            text-decoration: none;
            display: inline-block;
            font-size: 13px;
            cursor: pointer;
        }
        
        .btn:hover {
            background: #135e96;
            border-color: #135e96;
            color: #fff;
        }
        
        .btn-secondary {
            background: #f6f7f7;
            color: #2c3338;
            border-color: #dcdcde;
        }
        
        .btn-secondary:hover {
            background: #f0f0f1;
            border-color: #8c8f94;
            color: #2c3338;
        }
        
        .quick-draft textarea {
            width: 100%;
            min-height: 100px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
        }
        
        .activity-block {
            margin-bottom: 15px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f0f0f1;
        }
        
        .activity-block:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .activity-block h4 {
            margin: 0 0 5px 0;
            font-size: 13px;
        }
        
        .activity-block p {
            margin: 0;
            color: #646970;
            font-size: 13px;
        }
        
        @media (max-width: 768px) {
            .admin-menu {
                width: 36px;
            }
            
            .admin-menu .wp-menu-name {
                padding: 8px;
                text-align: center;
            }
            
            .admin-content {
                margin-left: 36px;
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
        <div class="admin-header">
            <h1>Dashboard</h1>
        </div>
        
        <div class="dashboard-widgets">
            <div class="dashboard-widget">
                <h3>At a Glance</h3>
                <div class="inside">
                    <ul style="list-style: none; padding: 0;">
                        <li style="margin-bottom: 10px;">📄 5 Posts</li>
                        <li style="margin-bottom: 10px;">📑 2 Pages</li>
                        <li style="margin-bottom: 10px;">💬 3 Comments</li>
                        <li>🎨 Active Theme: WDM Custom Header</li>
                    </ul>
                </div>
            </div>
            
            <div class="dashboard-widget">
                <h3>WDM Custom Header</h3>
                <div class="inside">
                    <p>Your custom header is active and configured. Manage all header settings including navigation menu, utility buttons, and styling options.</p>
                    <p style="margin-top: 15px;">
                        <a href="options-general.php?page=wdm-header-settings" class="btn">Header Settings</a>
                        <a href="../" class="btn btn-secondary" style="margin-left: 10px;">View Site</a>
                    </p>
                </div>
            </div>
            
            <div class="dashboard-widget">
                <h3>Quick Draft</h3>
                <div class="inside">
                    <form>
                        <p style="margin-bottom: 10px;">
                            <input type="text" placeholder="What's on your mind?" style="width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 3px;">
                        </p>
                        <p style="margin-bottom: 15px;">
                            <textarea placeholder="Write your thoughts here..."></textarea>
                        </p>
                        <p>
                            <button type="submit" class="btn">Save Draft</button>
                        </p>
                    </form>
                </div>
            </div>
            
            <div class="dashboard-widget">
                <h3>Activity</h3>
                <div class="inside">
                    <div class="activity-block">
                        <h4>Header settings updated</h4>
                        <p>WDM Custom Header configuration was modified - 2 hours ago</p>
                    </div>
                    <div class="activity-block">
                        <h4>Navigation menu configured</h4>
                        <p>Added new menu items and dropdown sections - 1 day ago</p>
                    </div>
                    <div class="activity-block">
                        <h4>Theme activated</h4>
                        <p>WDM Custom Header theme was activated - 2 days ago</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        function toggleSubmenu(menuId) {
            const submenu = document.getElementById(menuId + '-submenu');
            if (submenu) {
                submenu.classList.toggle('open');
            }
        }
    </script>
</body>
</html>