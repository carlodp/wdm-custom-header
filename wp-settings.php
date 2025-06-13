<?php
/**
 * Minimal WordPress core functions for testing
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define WordPress constants
define('WPINC', 'wp-includes');
define('WP_CONTENT_URL', '/wp-content');
define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
define('COOKIEHASH', md5('localhost'));
define('HOUR_IN_SECONDS', 3600);

// Global variables
global $wp_version, $wp_db_version;
$wp_version = '6.0';
$wp_db_version = 50000;

// Options storage
global $wp_options;
$wp_options = [];

// WordPress core functions
function get_option($option, $default = false) {
    global $wp_options;
    return $wp_options[$option] ?? $default;
}

function update_option($option, $value) {
    global $wp_options;
    $wp_options[$option] = $value;
    return true;
}

function add_option($option, $value) {
    return update_option($option, $value);
}

function delete_option($option) {
    global $wp_options;
    unset($wp_options[$option]);
    return true;
}

function get_transient($transient) {
    return get_option('_transient_' . $transient);
}

function set_transient($transient, $value, $expiration = 0) {
    return update_option('_transient_' . $transient, $value);
}

function delete_transient($transient) {
    return delete_option('_transient_' . $transient);
}

function wp_remote_get($url, $args = []) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_USERAGENT, 'WordPress/6.0');
    
    if (isset($args['headers']) && is_array($args['headers'])) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, $args['headers']);
    }
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    return [
        'body' => $response,
        'response' => ['code' => $http_code]
    ];
}

function wp_remote_retrieve_response_code($response) {
    return $response['response']['code'] ?? 0;
}

function wp_remote_retrieve_body($response) {
    return $response['body'] ?? '';
}

function is_wp_error($thing) {
    return false; // Simplified for demo
}

function current_user_can($capability) {
    return true; // Allow all for demo
}

function add_action($hook, $callback, $priority = 10, $accepted_args = 1) {
    global $wp_actions;
    $wp_actions[$hook][] = $callback;
}

function add_filter($hook, $callback, $priority = 10, $accepted_args = 1) {
    global $wp_filters;
    $wp_filters[$hook][] = $callback;
}

function do_action($hook, ...$args) {
    global $wp_actions;
    if (isset($wp_actions[$hook])) {
        foreach ($wp_actions[$hook] as $callback) {
            call_user_func_array($callback, $args);
        }
    }
}

function apply_filters($hook, $value, ...$args) {
    global $wp_filters;
    if (isset($wp_filters[$hook])) {
        foreach ($wp_filters[$hook] as $callback) {
            $value = call_user_func_array($callback, array_merge([$value], $args));
        }
    }
    return $value;
}

function add_options_page($page_title, $menu_title, $capability, $menu_slug, $function = '') {
    // Store admin page for demo
    global $wp_admin_pages;
    $wp_admin_pages[$menu_slug] = [
        'title' => $page_title,
        'menu_title' => $menu_title,
        'callback' => $function
    ];
}

function register_setting($option_group, $option_name, $args = []) {
    // Store registered settings
    global $wp_registered_settings;
    $wp_registered_settings[$option_group][] = $option_name;
}

function add_settings_section($id, $title, $callback, $page) {
    global $wp_settings_sections;
    $wp_settings_sections[$page][$id] = [
        'id' => $id,
        'title' => $title,
        'callback' => $callback
    ];
}

function add_settings_field($id, $title, $callback, $page, $section = 'default', $args = []) {
    global $wp_settings_fields;
    $wp_settings_fields[$page][$section][$id] = [
        'id' => $id,
        'title' => $title,
        'callback' => $callback,
        'args' => $args
    ];
}

function settings_fields($option_group) {
    echo '<input type="hidden" name="option_page" value="' . esc_attr($option_group) . '" />';
    wp_nonce_field($option_group . '-options');
}

function do_settings_sections($page) {
    global $wp_settings_sections, $wp_settings_fields;
    
    if (!isset($wp_settings_sections[$page])) {
        return;
    }
    
    foreach ($wp_settings_sections[$page] as $section) {
        echo '<h2>' . $section['title'] . '</h2>';
        if ($section['callback']) {
            call_user_func($section['callback'], $section);
        }
        
        if (isset($wp_settings_fields[$page][$section['id']])) {
            echo '<table class="form-table" role="presentation">';
            foreach ($wp_settings_fields[$page][$section['id']] as $field) {
                echo '<tr>';
                echo '<th scope="row">' . $field['title'] . '</th>';
                echo '<td>';
                call_user_func($field['callback'], $field['args']);
                echo '</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
}

function submit_button($text = 'Save Changes', $type = 'primary', $name = 'submit', $wrap = true, $other_attributes = '') {
    $button = '<input type="submit" name="' . esc_attr($name) . '" id="' . esc_attr($name) . '" class="button button-' . esc_attr($type) . '" value="' . esc_attr($text) . '" ' . $other_attributes . ' />';
    if ($wrap) {
        $button = '<p class="submit">' . $button . '</p>';
    }
    echo $button;
}

function wp_nonce_field($action = -1, $name = "_wpnonce", $referer = true, $echo = true) {
    $name = esc_attr($name);
    $nonce_field = '<input type="hidden" id="' . $name . '" name="' . $name . '" value="' . wp_create_nonce($action) . '" />';
    
    if ($referer) {
        $nonce_field .= wp_referer_field(false);
    }
    
    if ($echo) {
        echo $nonce_field;
    }
    
    return $nonce_field;
}

function wp_create_nonce($action = -1) {
    return substr(md5($action . 'nonce'), 0, 10);
}

function wp_referer_field($echo = true) {
    $referer_field = '<input type="hidden" name="_wp_http_referer" value="' . esc_attr($_SERVER['REQUEST_URI'] ?? '') . '" />';
    if ($echo) {
        echo $referer_field;
    }
    return $referer_field;
}

function settings_errors($setting = '', $sanitize = false, $hide_on_update = false) {
    global $wp_settings_errors;
    
    if (empty($wp_settings_errors)) {
        return;
    }
    
    foreach ($wp_settings_errors as $error) {
        $css_class = 'notice notice-' . $error['type'];
        echo '<div class="' . $css_class . ' is-dismissible"><p>' . $error['message'] . '</p></div>';
    }
}

function add_settings_error($setting, $code, $message, $type = 'error') {
    global $wp_settings_errors;
    $wp_settings_errors[] = [
        'setting' => $setting,
        'code' => $code,
        'message' => $message,
        'type' => $type
    ];
}

function get_admin_page_title() {
    return 'WDM Header Settings';
}

function wp_enqueue_script($handle, $src = '', $deps = [], $ver = false, $in_footer = false) {
    // Simplified for demo
}

function wp_enqueue_style($handle, $src = '', $deps = [], $ver = false, $media = 'all') {
    // Simplified for demo
}

function wp_add_inline_style($handle, $data) {
    echo '<style>' . $data . '</style>';
}

function wp_add_inline_script($handle, $data, $position = 'after') {
    echo '<script>' . $data . '</script>';
}

// version_compare is a PHP built-in function

// Initialize global arrays
global $wp_actions, $wp_filters, $wp_admin_pages, $wp_registered_settings, $wp_settings_sections, $wp_settings_fields, $wp_settings_errors;
$wp_actions = [];
$wp_filters = [];
$wp_admin_pages = [];
$wp_registered_settings = [];
$wp_settings_sections = [];
$wp_settings_fields = [];
$wp_settings_errors = [];

// Load the plugin
require_once __DIR__ . '/wdm-custom-header.php';