<?php
/**
 * Plugin Name: WDM Custom Header
 * Plugin URI: https://wdmorgan.com
 * Description: A custom responsive header with mega menu functionality for Grey Bull Rescue.
 * Version: 1.3.0
 * Author: Carlo Santos
 * License: GPL v2 or later
 * Text Domain: wdm-custom-header
 * Requires at least: 5.0
 * Tested up to: 6.4
 * Requires PHP: 7.4
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('WDM_CUSTOM_HEADER_VERSION', '1.3.0');
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
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        register_activation_hook(__FILE__, array($this, 'activate'));
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
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
    }
    
    /**
     * Enqueue plugin assets
     */
    public function enqueue_assets() {
        wp_enqueue_style(
            'wdm-header-css',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/header.css',
            array(),
            WDM_CUSTOM_HEADER_VERSION
        );
        
        wp_enqueue_script(
            'wdm-header-js',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/header.js',
            array(),
            WDM_CUSTOM_HEADER_VERSION,
            true
        );

        wp_enqueue_style(
            'fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
            array(),
            '6.7.2'
        );    
    }
    
    /**
     * Load plugin dependencies
     */
    private function load_dependencies() {
        $files = array(
            'includes/class-wdm-header.php',
            'includes/class-wdm-settings.php',
            'includes/class-wdm-updater.php'
        );
        
        foreach ($files as $file) {
            $filepath = WDM_CUSTOM_HEADER_PLUGIN_PATH . $file;
            if (file_exists($filepath)) {
                require_once $filepath;
            }
        }
        
        // Initialize GitHub updater if configured
        $this->init_updater();
    }
    
    /**
     * Initialize GitHub updater
     */
    private function init_updater() {
        $options = get_option('wdm_header_options', array());
        $auto_update = isset($options['auto_update']) ? $options['auto_update'] : '0';
        $username = isset($options['github_username']) ? $options['github_username'] : '';
        $repo = isset($options['github_repo']) ? $options['github_repo'] : '';
        
        if ($auto_update === '1' && !empty($username) && !empty($repo)) {
            if (class_exists('WDM_Custom_Header\WDM_Updater')) {
                new WDM_Custom_Header\WDM_Updater(__FILE__, $username, $repo, WDM_CUSTOM_HEADER_VERSION);
            }
        }
    }
    
    /**
     * Plugin activation
     */
    public function activate() {
        flush_rewrite_rules();
    }
    
    /**
     * Plugin deactivation
     */
    public function deactivate() {
        flush_rewrite_rules();
    }
}

/**
 * Helper function to display header
 */
function wdm_display_header() {
    $template_path = WDM_CUSTOM_HEADER_PLUGIN_PATH . 'templates/header.php';
    if (file_exists($template_path)) {
        include $template_path;
    }
}

/**
 * Shortcode callback function
 */
function wdm_custom_header_shortcode($atts) {
    // Start output buffering
    ob_start();
    
    // Load CSS and JS
    wp_enqueue_style(
        'wdm-header-css',
        WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/header.css',
        array(),
        WDM_CUSTOM_HEADER_VERSION
    );
    
    wp_enqueue_script(
        'wdm-header-js',
        WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/header.js',
        array(),
        WDM_CUSTOM_HEADER_VERSION,
        true
    );
    
    // Include the header template
    $template_path = WDM_CUSTOM_HEADER_PLUGIN_PATH . 'templates/header.php';
    if (file_exists($template_path)) {
        include $template_path;
    } else {
        echo '<div class="wdm-error">WDM Header template not found at: ' . $template_path . '</div>';
    }
    
    // Return buffered content
    return ob_get_clean();
}

// Register shortcode
add_shortcode('wdm_custom_header', 'wdm_custom_header_shortcode');

// Initialize the plugin
new WDM_Custom_Header_Plugin();
