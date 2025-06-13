<?php
/**
 * Plugin Name: WDM Custom Header
 * Plugin URI: https://example.com
 * Description: A custom responsive header with mega menu functionality, inspired by Grey Bull design.
 * Version: 1.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: wdm-custom-header
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WDM_CUSTOM_HEADER_VERSION', '1.0.0');
define('WDM_CUSTOM_HEADER_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WDM_CUSTOM_HEADER_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main plugin class
 */
class WDM_Custom_Header_Plugin {
    
    /**
     * Initialize the plugin
     */
    public function __construct() {
        add_action('plugins_loaded', array($this, 'init'));
    }
    
    /**
     * Initialize plugin components
     */
    public function init() {
        // Load required files
        $this->load_dependencies();
        
        // Initialize components
        new WDM_Custom_Header\WDM_Header();
        new WDM_Custom_Header\WDM_Settings();
        
        // Initialize admin interface if in admin area
        if (is_admin()) {
            WDM_Custom_Header\WDM_Admin::init();
        }
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        require_once WDM_CUSTOM_HEADER_PLUGIN_PATH . 'includes/class-wdm-header.php';
        require_once WDM_CUSTOM_HEADER_PLUGIN_PATH . 'includes/class-wdm-settings.php';
        require_once WDM_CUSTOM_HEADER_PLUGIN_PATH . 'includes/class-wdm-menu-renderer.php';
        require_once WDM_CUSTOM_HEADER_PLUGIN_PATH . 'includes/class-wdm-admin.php';
    }
}

// Initialize the plugin
new WDM_Custom_Header_Plugin();
