<?php
/**
 * WDM GitHub Updater Class
 * Handles automatic updates from GitHub repository
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Updater {
    
    private $plugin_slug;
    private $version;
    private $github_username;
    private $github_repo;
    private $plugin_file;
    
    /**
     * Constructor
     */
    public function __construct($plugin_file, $github_username, $github_repo, $version) {
        $this->plugin_file = $plugin_file;
        $this->plugin_slug = plugin_basename($plugin_file);
        $this->version = $version;
        $this->github_username = $github_username;
        $this->github_repo = $github_repo;
        
        add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_update'));
        add_filter('plugins_api', array($this, 'plugin_popup'), 10, 3);
        add_filter('upgrader_post_install', array($this, 'after_install'), 10, 3);
    }
    
    /**
     * Check for plugin update
     */
    public function check_for_update($transient) {
        if (empty($transient->checked)) {
            return $transient;
        }
        
        $remote_version = $this->get_remote_version();
        
        if ($remote_version && version_compare($this->version, $remote_version, '<')) {
            $transient->response[$this->plugin_slug] = (object) array(
                'slug' => $this->plugin_slug,
                'new_version' => $remote_version,
                'url' => $this->get_github_repo_url(),
                'package' => $this->get_download_url()
            );
        }
        
        return $transient;
    }
    
    /**
     * Get remote version from GitHub
     */
    private function get_remote_version() {
        $request = wp_remote_get($this->get_api_url());
        
        if (!is_wp_error($request) && wp_remote_retrieve_response_code($request) === 200) {
            $body = wp_remote_retrieve_body($request);
            $releases = json_decode($body, true);
            
            if (!empty($releases) && isset($releases[0]['tag_name'])) {
                return $releases[0]['tag_name'];
            }
        }
        
        return false;
    }
    
    /**
     * Get GitHub API URL
     */
    private function get_api_url() {
        return "https://api.github.com/repos/{$this->github_username}/{$this->github_repo}/releases";
    }
    
    /**
     * Get GitHub repository URL
     */
    private function get_github_repo_url() {
        return "https://github.com/{$this->github_username}/{$this->github_repo}";
    }
    
    /**
     * Get download URL
     */
    private function get_download_url() {
        return "https://github.com/{$this->github_username}/{$this->github_repo}/archive/refs/heads/main.zip";
    }
    
    /**
     * Plugin popup for update details
     */
    public function plugin_popup($result, $action, $args) {
        if ($action !== 'plugin_information') {
            return $result;
        }
        
        if ($args->slug !== $this->plugin_slug) {
            return $result;
        }
        
        $remote_version = $this->get_remote_version();
        
        return (object) array(
            'name' => 'WDM Custom Header',
            'slug' => $this->plugin_slug,
            'version' => $remote_version,
            'author' => 'WDM Developer',
            'homepage' => $this->get_github_repo_url(),
            'short_description' => 'A custom responsive header with mega menu functionality for Grey Bull Rescue.',
            'sections' => array(
                'description' => 'A custom responsive header with mega menu functionality for Grey Bull Rescue.',
                'changelog' => 'Updates available from GitHub repository.'
            ),
            'download_link' => $this->get_download_url()
        );
    }
    
    /**
     * After plugin install
     */
    public function after_install($response, $hook_extra, $result) {
        global $wp_filesystem;
        
        $install_directory = plugin_dir_path($this->plugin_file);
        $wp_filesystem->move($result['destination'], $install_directory);
        $result['destination'] = $install_directory;
        
        if ($this->plugin_slug === $hook_extra['plugin']) {
            $wp_filesystem->delete($result['destination_name']);
        }
        
        return $result;
    }
}