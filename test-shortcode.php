<?php
/**
 * Test page for WDM Custom Header shortcode
 */

// Load WordPress
require_once 'wdm-custom-header.php';

// Simple HTML page to test shortcode
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shortcode Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .test-section { margin: 20px 0; padding: 20px; border: 1px solid #ddd; }
    </style>
</head>
<body>
    <h1>WDM Custom Header Shortcode Test</h1>
    
    <div class="test-section">
        <h2>Testing [wdm_custom_header] shortcode:</h2>
        <?php
        // Simulate WordPress environment
        if (!function_exists('do_shortcode')) {
            function do_shortcode($content) {
                // Simple shortcode parser
                $pattern = '/\[wdm_custom_header\]/';
                return preg_replace_callback($pattern, function($matches) {
                    // Initialize the WDM_Header class
                    if (class_exists('WDM_Custom_Header\WDM_Header')) {
                        $header = new WDM_Custom_Header\WDM_Header();
                        return $header->render_header(array());
                    }
                    return 'Shortcode class not found';
                }, $content);
            }
        }
        
        // Test the shortcode
        echo do_shortcode('[wdm_custom_header]');
        ?>
    </div>
    
    <div class="test-section">
        <h2>Direct function call test:</h2>
        <?php
        // Test direct function call
        if (function_exists('wdm_display_header')) {
            wdm_display_header();
        } else {
            echo 'Function wdm_display_header not found';
        }
        ?>
    </div>
    
    <div class="test-section">
        <h2>Direct class method test:</h2>
        <?php
        // Test direct class method
        if (class_exists('WDM_Custom_Header\WDM_Header')) {
            $header = new WDM_Custom_Header\WDM_Header();
            echo $header->render_header(array());
        } else {
            echo 'WDM_Header class not found';
        }
        ?>
    </div>
</body>
</html>