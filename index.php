<?php
/**
 * Demo page for WDM Custom Header Plugin
 * This demonstrates the header functionality without WordPress
 */

// Simulate WordPress constants
define('ABSPATH', __DIR__ . '/');

// Mock shortcode attributes for demo
$atts = array(
    'logo_url' => '',
    'logo_alt' => 'Logo'
);

// Get the header HTML directly from template
ob_start();
include __DIR__ . '/templates/header.php';
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
    </div>
    
    <script src="/assets/js/header.js"></script>
</body>
</html>