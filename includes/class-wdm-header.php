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
        // Check if CSS should be loaded based on admin setting
        $load_css = get_option('wdm_header_load_css', '1');

        if ($load_css === '1') {
            \wp_enqueue_style(
                'wdm-header-css',
                WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/header.css',
                array(),
                WDM_CUSTOM_HEADER_VERSION
            );
        }

        \wp_enqueue_script(
            'wdm-header-js',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/header.js',
            array(),
            WDM_CUSTOM_HEADER_VERSION,
            true
        );
    }

    /**
     * Render header shortcode
     */
    public function render_header($atts) {
        // Parse shortcode attributes
        $atts = \shortcode_atts(array(
            'logo_url' => '',
            'logo_alt' => 'Logo'
        ), $atts);

        // Start output buffering
        ob_start();

        // Include header template
        include WDM_CUSTOM_HEADER_PLUGIN_PATH . 'templates/header.php';

        // Return buffered content
        return ob_get_clean();
    }
}
