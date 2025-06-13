<?php
/**
 * WDM Header Class
 * Handles header rendering and shortcode functionality
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Header {
    
    /**
     * Constructor
     */
    public function __construct() {
        \add_action('init', array($this, 'init'));
        \add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
    }
    
    /**
     * Initialize shortcode
     */
    public function init() {
        \add_shortcode('wdm_custom_header', array($this, 'render_header'));
    }
    
    /**
     * Enqueue CSS and JS assets
     */
    public function enqueue_assets() {
        // Force enqueue on pages with shortcode
        global $post;
        if (is_a($post, 'WP_Post') && has_shortcode($post->post_content, 'wdm_custom_header')) {
            $this->force_enqueue_assets();
        }
        
        // Check if CSS should be loaded based on admin setting
        $options = get_option('wdm_header_options', array());
        $load_css = isset($options['load_css']) ? $options['load_css'] : '1';
        
        if ($load_css === '1') {
            \wp_enqueue_style(
                'wdm-header-css',
                WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/header.css',
                array(),
                WDM_CUSTOM_HEADER_VERSION . '-' . time(),
                'all'
            );
        }
        
        \wp_enqueue_script(
            'wdm-header-js',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/header.js',
            array('jquery'),
            WDM_CUSTOM_HEADER_VERSION . '-' . time(),
            true
        );
    }
    
    /**
     * Force enqueue assets for shortcode usage
     */
    public function force_enqueue_assets() {
        \wp_enqueue_style(
            'wdm-header-css',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/header.css',
            array(),
            WDM_CUSTOM_HEADER_VERSION . '-' . time(),
            'all'
        );
        
        \wp_enqueue_script(
            'wdm-header-js',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/header.js',
            array('jquery'),
            WDM_CUSTOM_HEADER_VERSION . '-' . time(),
            true
        );
    }
    
    /**
     * Render header shortcode
     */
    public function render_header($atts) {
        // Force enqueue assets when shortcode is used
        $this->force_enqueue_assets();
        
        // Parse shortcode attributes
        $atts = \shortcode_atts(array(
            'logo_url' => '',
            'logo_alt' => 'Logo'
        ), $atts);
        
        // Start output buffering
        ob_start();
        
        // Include header template
        $template_path = WDM_CUSTOM_HEADER_PLUGIN_PATH . 'templates/header.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            echo '<div class="wdm-error">WDM Header template not found.</div>';
        }
        
        // Return buffered content
        return ob_get_clean();
    }
}
