<?php
/**
 * WordPress Functions Fallback
 * Provides WordPress function compatibility for standalone usage
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    // If not in WordPress, create minimal fallbacks
    
    /**
     * Fallback for get_option
     */
    if (!function_exists('get_option')) {
        function get_option($option, $default = false) {
            // In standalone mode, return defaults
            static $options = array();
            
            if (!isset($options[$option])) {
                switch ($option) {
                    case 'wdm_header_logo_url':
                        return 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png';
                    case 'wdm_header_utility_buttons':
                        return array(
                            array('label' => 'VOLUNTEER', 'url' => '#volunteer', 'class' => 'btn-volunteer', 'target' => '_self'),
                            array('label' => 'DONATE', 'url' => '#donate', 'class' => 'btn-donate', 'target' => '_blank')
                        );
                    case 'wdm_header_menu_items':
                        return array(
                            array('label' => 'How We Serve', 'type' => 'megamenu'),
                            array('label' => 'How to Get Involved', 'type' => 'dropdown'),
                            array('label' => 'Ways to Give', 'type' => 'dropdown'),
                            array('label' => 'About Grey Bull', 'type' => 'dropdown')
                        );
                    default:
                        return $default;
                }
            }
            
            return $options[$option];
        }
    }
    
    /**
     * Fallback for esc_url
     */
    if (!function_exists('esc_url')) {
        function esc_url($url) {
            return filter_var($url, FILTER_SANITIZE_URL);
        }
    }
    
    /**
     * Fallback for esc_attr
     */
    if (!function_exists('esc_attr')) {
        function esc_attr($text) {
            return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
        }
    }
    
    /**
     * Fallback for esc_html
     */
    if (!function_exists('esc_html')) {
        function esc_html($text) {
            return htmlspecialchars($text, ENT_NOQUOTES, 'UTF-8');
        }
    }
    
    if (!function_exists('is_admin')) {
        function is_admin() {
            return defined('WP_ADMIN') && WP_ADMIN;
        }
    }
    
    /**
     * Helper function for checked/selected
     */
    if (!function_exists('__checked_selected_helper')) {
        function __checked_selected_helper($helper, $current, $echo, $type) {
            if ((string) $helper === (string) $current) {
                $result = " $type='$type'";
            } else {
                $result = '';
            }
            
            if ($echo) {
                echo $result;
            }
            
            return $result;
        }
    }
    
    /**
     * Fallback for checked
     */
    if (!function_exists('checked')) {
        function checked($checked, $current = true, $echo = true) {
            return __checked_selected_helper($checked, $current, $echo, 'checked');
        }
    }
    
    /**
     * Fallback for selected
     */
    if (!function_exists('selected')) {
        function selected($selected, $current = true, $echo = true) {
            return __checked_selected_helper($selected, $current, $echo, 'selected');
        }
    }
    
    /**
     * Helper function for checked/selected
     */
    if (!function_exists('__checked_selected_helper')) {
        function __checked_selected_helper($helper, $current, $echo, $type) {
            if ((string) $helper === (string) $current) {
                $result = " $type='$type'";
            } else {
                $result = '';
            }
            
            if ($echo) {
                echo $result;
            }
            
            return $result;
        }
    }
    
    /**
     * Fallback for WordPress settings functions (no-op in standalone)
     */
    if (!function_exists('register_setting')) {
        function register_setting($option_group, $option_name, $args = array()) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('add_settings_section')) {
        function add_settings_section($id, $title, $callback, $page) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('add_settings_field')) {
        function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = array()) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('settings_fields')) {
        function settings_fields($option_group) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('do_settings_sections')) {
        function do_settings_sections($page) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('submit_button')) {
        function submit_button($text = null, $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = null) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('add_action')) {
        function add_action($hook, $function_to_add, $priority = 10, $accepted_args = 1) {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('add_options_page')) {
        function add_options_page($page_title, $menu_title, $capability, $menu_slug, $function = '') {
            // No-op in standalone mode
        }
    }
    
    if (!function_exists('plugin_dir_url')) {
        function plugin_dir_url($file) {
            return '/';
        }
    }
    
    if (!function_exists('plugin_dir_path')) {
        function plugin_dir_path($file) {
            return __DIR__ . '/';
        }
    }
}