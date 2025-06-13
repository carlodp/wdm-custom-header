<?php
/**
 * Demo page for WDM Custom Header Plugin
 * This demonstrates the header functionality without WordPress
 */

// Mock WordPress functions
if (!function_exists('get_option')) {
    function get_option($option, $default = false) {
        // Return test menu data
        if ($option === 'wdm_header_menu_data') {
            return array(
                array(
                    'text' => 'Home',
                    'url' => '#home',
                    'has_submenu' => false,
                    'submenu_items' => array()
                ),
                array(
                    'text' => 'About',
                    'url' => '#about',
                    'has_submenu' => true,
                    'submenu_items' => array(
                        array('text' => 'Our Story', 'url' => '#story'),
                        array('text' => 'Team', 'url' => '#team')
                    )
                ),
                array(
                    'text' => 'Services',
                    'url' => '#services',
                    'has_submenu' => false,
                    'submenu_items' => array()
                )
            );
        }
        
        if ($option === 'wdm_header_options') {
            return array(
                'volunteer_text' => 'VOLUNTEER',
                'volunteer_url' => '#volunteer',
                'donate_text' => 'DONATE',
                'donate_url' => '#donate',
                'show_search' => '1'
            );
        }
        
        return $default;
    }
}

if (!function_exists('esc_html')) {
    function esc_html($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_url')) {
    function esc_url($url) {
        return htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('esc_attr')) {
    function esc_attr($text) {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }
}

// Simulate WordPress constants
define('ABSPATH', __DIR__ . '/');

// Mock shortcode attributes for demo
$atts = array(
    'logo_url' => '',
    'logo_alt' => 'Logo'
);

// Get the header HTML directly from template
ob_start();
$template_file = __DIR__ . '/templates/header.php';
if (file_exists($template_file)) {
    include $template_file;
} else {
    echo '<div style="color: red;">Template not found: ' . $template_file . '</div>';
}
$header_html = ob_get_clean();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WDM Custom Header Demo</title>
    <link rel="stylesheet" href="/assets/css/header.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
            background: #f8f9fa;
        }
        .demo-content {
            padding: 40px 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .demo-section {
            background: white;
            padding: 30px;
            margin: 20px 0;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #212529;
            margin-bottom: 20px;
        }
        h2 {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .feature-list {
            list-style: none;
            padding: 0;
        }
        .feature-list li {
            padding: 8px 0;
            border-bottom: 1px solid #e5e5e5;
        }
        .feature-list li:before {
            content: "✓";
            color: #28a745;
            margin-right: 10px;
            font-weight: bold;
        }
        .shortcode-example {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            font-family: monospace;
            border-left: 4px solid #dc3545;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <?php echo $header_html; ?>
    
    <div class="demo-content">
        <div class="demo-section">
            <h1>WDM Custom Header Plugin Demo</h1>
            <p>This demonstrates the WDM Custom Header plugin functionality. The header above is generated using the <code>[wdm_custom_header]</code> shortcode.</p>
            
            <h2>Plugin Features</h2>
            <ul class="feature-list">
                <li>Responsive design that works on all devices</li>
                <li>Click-based mega menu (not hover)</li>
                <li>Mobile hamburger menu</li>
                <li>Search functionality with toggle</li>
                <li>Team Rubicon inspired design</li>
                <li>Modular file structure</li>
                <li>WordPress namespace implementation</li>
                <li>Admin settings for CSS control</li>
                <li>Custom logo support via shortcode attributes</li>
                <li>Clean, semantic HTML structure</li>
            </ul>
            
            <h2>How to Use</h2>
            <p>Simply add the shortcode to any WordPress page or post:</p>
            <div class="shortcode-example">
                [wdm_custom_header]
            </div>
            
            <p>Or with custom logo:</p>
            <div class="shortcode-example">
                [wdm_custom_header logo_url="/path/to/logo.png" logo_alt="Your Logo"]
            </div>
            
            <h2>Testing Instructions</h2>
            <ul class="feature-list">
                <li>Click on any mega menu item (How We Serve, How to Get Involved, etc.)</li>
                <li>Try the search icon to reveal the search input</li>
                <li>Resize your browser to test mobile responsiveness</li>
                <li>Use the hamburger menu on mobile/tablet sizes</li>
                <li>Click outside menus to close them</li>
                <li>Test keyboard navigation (Escape to close search)</li>
            </ul>
        </div>
        
        <div class="demo-section">
            <h2>Installation</h2>
            <p>To install this plugin in WordPress:</p>
            <ol>
                <li>Upload the <code>wdm-custom-header</code> folder to <code>/wp-content/plugins/</code></li>
                <li>Activate the plugin through the 'Plugins' menu in WordPress</li>
                <li>Go to Settings → WDM Header to configure options</li>
                <li>Add <code>[wdm_custom_header]</code> shortcode to any page</li>
            </ol>
        </div>
        
        <div class="demo-section">
            <h2>Scroll Test Section</h2>
            <p>Scroll down to test the header behavior. When you scroll past this section, the mega menu area should slide up and a hamburger menu should appear in the top header.</p>
            <p>The hamburger menu will allow you to access the mega menu items even when scrolled down.</p>
            
            <h3>Key Features Tested:</h3>
            <ul class="feature-list">
                <li>Mega menu slides up on scroll</li>
                <li>Hamburger menu appears when scrolled</li>
                <li>Top navigation stays visible</li>
                <li>Volunteer and Donate buttons remain accessible</li>
                <li>Search functionality works in both states</li>
            </ul>
        </div>
        
        <div class="demo-section">
            <h2>More Content for Scrolling</h2>
            <p>Keep scrolling to see the scroll behavior in action. The header should transform as you scroll down past the first 100 pixels.</p>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
            <p>Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
        </div>
        
        <div class="demo-section">
            <h2>Additional Test Content</h2>
            <p>This section provides more content to enable proper scroll testing of the header functionality.</p>
            <p>The header should maintain its responsive behavior across different screen sizes while providing the scroll-based navigation changes.</p>
            <p>Test the mega menu panels by clicking on the navigation items both in normal state and when the hamburger menu is active after scrolling.</p>
        </div>
        
        <div class="demo-section">
            <h2>Final Section</h2>
            <p>Continue testing all the interactive elements to ensure they work properly in both scroll states.</p>
            <p>The plugin is designed to work seamlessly with any WordPress theme and page builder.</p>
        </div>
    </div>
    
    <script src="/assets/js/header.js"></script>
</body>
</html>