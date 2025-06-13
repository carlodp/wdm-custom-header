<?php
/**
 * WDM Custom Header Auto-Updater
 * Handles automatic updates from GitHub repository
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Updater {
    
    private $plugin_file;
    private $plugin_slug;
    private $version;
    private $github_username;
    private $github_repo;
    private $github_token;
    
    /**
     * Initialize the updater
     */
    public function __construct($plugin_file, $version, $github_username, $github_repo, $github_token = '') {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->version = $version;
        $this->github_username = $github_username;
        $this->github_repo = $github_repo;
        $this->github_token = $github_token;
        
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_info'), 20, 3);
        add_filter('upgrader_pre_download', array($this, 'download_package'), 10, 3);
        add_action('admin_notices', array($this, 'update_notice'));
    }
    
    /**
     * Check for updates
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        // Get remote version
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            $transient->response[$this->plugin_slug] = (object) array(
                'slug' => dirname($this->plugin_slug),
                'plugin' => $this->plugin_slug,
                'new_version' => $remote_version,
                'url' => $this->get_github_repo_url(),
                'package' => $this->get_download_url($remote_version)
            );
        }
        
        return $transient;
    }
    
    /**
     * Get plugin information for the update screen
     */
    public function plugin_info($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if (!isset($args->slug) || $args->slug !== dirname($this->plugin_slug)) {
            return $result;
        }
        
        $remote_version = $this->get_remote_version();
        $changelog = $this->get_changelog();
        
        return (object) array(
            'name' => 'WDM Custom Header',
            'slug' => dirname($this->plugin_slug),
            'version' => $remote_version,
            'author' => '<a href="https://github.com/' . $this->github_username . '">GitHub Repository</a>',
            'homepage' => $this->get_github_repo_url(),
            'short_description' => 'A custom responsive header with mega menu functionality.',
            'sections' => array(
                'description' => 'WDM Custom Header provides a professional, responsive header with advanced navigation features including mega menus, utility buttons, and customizable styling options.',
                'changelog' => $changelog,
                'installation' => 'Upload the plugin files to the `/wp-content/plugins/wdm-custom-header` directory, or install the plugin through the WordPress plugins screen directly. Activate the plugin through the \'Plugins\' screen in WordPress.'
            ),
            'download_link' => $this->get_download_url($remote_version),
            'banners' => array(
                'low' => 'https://via.placeholder.com/772x250/0073aa/ffffff?text=WDM+Custom+Header',
                'high' => 'https://via.placeholder.com/1544x500/0073aa/ffffff?text=WDM+Custom+Header'
            )
        );
    }
    
    /**
     * Download the update package
     */
    public function download_package($reply, $package, $upgrader) {
        if (strpos($package, 'github.com') !== false && 
            strpos($package, $this->github_username . '/' . $this->github_repo) !== false) {
            
            $args = array(
                'timeout' => 300,
                'headers' => array()
            );
            
            if (!empty($this->github_token)) {
                $args['headers']['Authorization'] = 'token ' . $this->github_token;
            }
            
            $response = wp_remote_get($package, $args);
            
            if (is_wp_error($response)) {
                return $response;
            }
            
            $body = wp_remote_retrieve_body($response);
            $temp_file = download_url($package);
            
            if (!is_wp_error($temp_file)) {
                return $temp_file;
            }
        }
        
        return $reply;
    }
    
    /**
     * Show update notice in admin
     */
    public function update_notice() {
        if (!current_user_can('update_plugins')) {
            return;
        }
        
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            $plugin_data = get_plugin_data($this->plugin_file);
            $plugin_name = $plugin_data['Name'];
            $update_url = wp_nonce_url(self_admin_url('update.php?action=upgrade-plugin&plugin=' . urlencode($this->plugin_slug)), 'upgrade-plugin_' . $this->plugin_slug);
            
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p>';
            echo sprintf(
                __('There is a new version of %s available. <a href="%s">View version %s details</a> or <a href="%s" class="update-link">update now</a>.', 'wdm-custom-header'),
                '<strong>' . esc_html($plugin_name) . '</strong>',
                esc_url($this->get_github_repo_url() . '/releases/tag/v' . $remote_version),
                esc_html($remote_version),
                esc_url($update_url)
            );
            echo '</p>';
            echo '</div>';
        }
    }
    
    /**
     * Get the remote version from GitHub
     */
    private function get_remote_version() {
        $version_cache_key = 'wdm_header_remote_version';
        $cached_version = get_transient($version_cache_key);
        
        if ($cached_version !== false) {
            return $cached_version;
        }
        
        $api_url = 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/releases/latest';
        
        $args = array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WDM-Custom-Header-Updater'
            )
        );
        
        if (!empty($this->github_token)) {
            $args['headers']['Authorization'] = 'token ' . $this->github_token;
        }
        
        $response = wp_remote_get($api_url, $args);
        
        if (is_wp_error($response)) {
            return false;
        }
        
        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);
        
        if (isset($data['tag_name'])) {
            $version = ltrim($data['tag_name'], 'v');
            
            // Cache for 12 hours
            set_transient($version_cache_key, $version, 12 * HOUR_IN_SECONDS);
            
            return $version;
        }
        
        return false;
    }
    
    /**
     * Get changelog from GitHub releases
     */
    private function get_changelog() {
        $changelog_cache_key = 'wdm_header_changelog';
        $cached_changelog = get_transient($changelog_cache_key);
        
        if ($cached_changelog !== false) {
            return $cached_changelog;
        }
        
        $api_url = 'https://api.github.com/repos/' . $this->github_username . '/' . $this->github_repo . '/releases';
        
        $args = array(
            'timeout' => 30,
            'headers' => array(
                'Accept' => 'application/vnd.github.v3+json',
                'User-Agent' => 'WDM-Custom-Header-Updater'
            )
        );
        
        if (!empty($this->github_token)) {
            $args['headers']['Authorization'] = 'token ' . $this->github_token;
        }
        
        $response = wp_remote_get($api_url, $args);
        
        if (is_wp_error($response)) {
            return '<p>Unable to fetch changelog.</p>';
        }
        
        $body = wp_remote_retrieve_body($response);
        $releases = json_decode($body, true);
        
        if (!is_array($releases)) {
            return '<p>Unable to fetch changelog.</p>';
        }
        
        $changelog = '<h3>Recent Releases</h3>';
        
        foreach (array_slice($releases, 0, 5) as $release) {
            $version = ltrim($release['tag_name'], 'v');
            $date = date('F j, Y', strtotime($release['published_at']));
            $body = !empty($release['body']) ? $release['body'] : 'No release notes available.';
            
            $changelog .= '<h4>Version ' . esc_html($version) . ' (' . esc_html($date) . ')</h4>';
            $changelog .= '<div>' . wp_kses_post(wpautop($body)) . '</div>';
        }
        
        // Cache for 6 hours
        set_transient($changelog_cache_key, $changelog, 6 * HOUR_IN_SECONDS);
        
        return $changelog;
    }
    
    /**
     * Get GitHub repository URL
     */
    private function get_github_repo_url() {
        return 'https://github.com/' . $this->github_username . '/' . $this->github_repo;
    }
    
    /**
     * Get download URL for a specific version
     */
    private function get_download_url($version) {
        return 'https://github.com/' . $this->github_username . '/' . $this->github_repo . '/archive/refs/tags/v' . $version . '.zip';
    }
    
    /**
     * Force check for updates
     */
    public static function force_update_check() {
        delete_transient('wdm_header_remote_version');
        delete_transient('wdm_header_changelog');
        delete_site_transient('update_plugins');
    }
}