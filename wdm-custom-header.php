<?php
/**
 * Plugin Name: WDM Custom Header
 * Plugin URI: https://example.com
 * Description: A custom responsive header with mega menu functionality, inspired by Grey Bull design.
 * Version: 1.0.1
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: wdm-custom-header
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // Define ABSPATH for standalone usage
    define('ABSPATH', __DIR__ . '/');
}

// Load WordPress functions first for fallbacks
require_once __DIR__ . '/includes/wordpress-functions.php';

// Define plugin constants
define('WDM_CUSTOM_HEADER_VERSION', '1.0.1');
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
        if (class_exists('WDM_Custom_Header\WDM_Header')) {
            new WDM_Custom_Header\WDM_Header();
        }
        if (class_exists('WDM_Custom_Header\WDM_Settings')) {
            new WDM_Custom_Header\WDM_Settings();
        }
        
        // Initialize admin interface if in WordPress admin area
        if (function_exists('is_admin') && is_admin() && class_exists('WDM_Custom_Header\WDM_Admin')) {
            WDM_Custom_Header\WDM_Admin::init();
        }
        
        // Initialize auto-updater
        $this->init_updater();
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        $plugin_path = __DIR__ . '/';
        require_once $plugin_path . 'includes/class-wdm-header.php';
        require_once $plugin_path . 'includes/class-wdm-settings.php';
        require_once $plugin_path . 'includes/class-wdm-menu-renderer.php';
        require_once $plugin_path . 'includes/class-wdm-admin.php';
        require_once $plugin_path . 'includes/class-wdm-updater.php';
    }
    
    /**
     * Initialize the auto-updater
     */
    private function init_updater() {
        // Get updater settings from WordPress options
        $options = get_option('wdm_header_options', []);
        $enable_auto_updates = $options['enable_auto_updates'] ?? true;
        $github_username = $options['github_username'] ?? '';
        $github_repo = $options['github_repo'] ?? '';
        $github_token = $options['github_token'] ?? '';
        
        // Only initialize if auto-updates are enabled and GitHub settings are configured
        if ($enable_auto_updates && !empty($github_username) && !empty($github_repo)) {
            new WDM_Custom_Header\WDM_Updater(
                __FILE__,
                WDM_CUSTOM_HEADER_VERSION,
                $github_username,
                $github_repo,
                $github_token
            );
        }
    }
}



// Initialize the plugin
new WDM_Custom_Header_Plugin();
