<?php
/**
 * WDM Settings Class
 * Handles admin settings page functionality
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Settings {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('admin_init', array($this, 'settings_init'));
    }
    
    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        add_options_page(
            'WDM Header Settings',
            'WDM Header',
            'manage_options',
            'wdm-header-settings',
            array($this, 'settings_page')
        );
    }
    
    /**
     * Initialize settings
     */
    public function settings_init() {
        register_setting('wdm_header_settings', 'wdm_header_load_css');
        
        add_settings_section(
            'wdm_header_general',
            'General Settings',
            array($this, 'settings_section_callback'),
            'wdm_header_settings'
        );
        
        add_settings_field(
            'wdm_header_load_css',
            'Load Default CSS',
            array($this, 'load_css_callback'),
            'wdm_header_settings',
            'wdm_header_general'
        );
    }
    
    /**
     * Settings section callback
     */
    public function settings_section_callback() {
        echo '<p>Configure WDM Custom Header settings.</p>';
    }
    
    /**
     * Load CSS field callback
     */
    public function load_css_callback() {
        $load_css = get_option('wdm_header_load_css', '1');
        echo '<input type="checkbox" name="wdm_header_load_css" value="1" ' . checked('1', $load_css, false) . ' />';
        echo '<label for="wdm_header_load_css"> Enable default header styles</label>';
    }
    
    /**
     * Render settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>WDM Header Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('wdm_header_settings');
                do_settings_sections('wdm_header_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }
}
