<?php
/**
 * WDM Settings Class
 * Handles admin settings page functionality with GitHub auto-update
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
        \add_action('admin_menu', array($this, 'add_admin_menu'));
        \add_action('admin_init', array($this, 'settings_init'));
        \add_action('wp_ajax_wdm_check_update', array($this, 'ajax_check_update'));
        \add_action('wp_ajax_wdm_force_update', array($this, 'ajax_force_update'));
    }
    
    /**
     * Add admin menu item
     */
    public function add_admin_menu() {
        \add_options_page(
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
        \register_setting('wdm_header_settings', 'wdm_header_options');
        
        \add_settings_section(
            'wdm_header_general',
            'General Settings',
            array($this, 'general_section_callback'),
            'wdm_header_settings'
        );
        
        \add_settings_field(
            'load_css',
            'Load Default CSS',
            array($this, 'load_css_callback'),
            'wdm_header_settings',
            'wdm_header_general'
        );
        
        \add_settings_section(
            'wdm_header_github',
            'GitHub Auto-Update Settings',
            array($this, 'github_section_callback'),
            'wdm_header_settings'
        );
        
        \add_settings_field(
            'github_username',
            'GitHub Username',
            array($this, 'github_username_callback'),
            'wdm_header_settings',
            'wdm_header_github'
        );
        
        \add_settings_field(
            'github_repo',
            'GitHub Repository',
            array($this, 'github_repo_callback'),
            'wdm_header_settings',
            'wdm_header_github'
        );
        
        \add_settings_field(
            'auto_update',
            'Enable Auto-Update',
            array($this, 'auto_update_callback'),
            'wdm_header_settings',
            'wdm_header_github'
        );
    }
    
    /**
     * General settings section callback
     */
    public function general_section_callback() {
        echo '<p>Configure basic header settings.</p>';
    }
    
    /**
     * GitHub settings section callback
     */
    public function github_section_callback() {
        echo '<p>Configure GitHub repository for automatic plugin updates.</p>';
    }
    
    /**
     * Load CSS field callback
     */
    public function load_css_callback() {
        $options = get_option('wdm_header_options', array());
        $load_css = isset($options['load_css']) ? $options['load_css'] : '1';
        echo '<input type="checkbox" name="wdm_header_options[load_css]" value="1" ' . checked('1', $load_css, false) . ' />';
        echo '<label> Enable default header styles</label>';
    }
    
    /**
     * GitHub username field callback
     */
    public function github_username_callback() {
        $options = get_option('wdm_header_options', array());
        $username = isset($options['github_username']) ? $options['github_username'] : '';
        echo '<input type="text" name="wdm_header_options[github_username]" value="' . esc_attr($username) . '" placeholder="your-username" />';
        echo '<p class="description">Enter your GitHub username where the plugin repository is hosted.</p>';
    }
    
    /**
     * GitHub repository field callback
     */
    public function github_repo_callback() {
        $options = get_option('wdm_header_options', array());
        $repo = isset($options['github_repo']) ? $options['github_repo'] : '';
        echo '<input type="text" name="wdm_header_options[github_repo]" value="' . esc_attr($repo) . '" placeholder="repository-name" />';
        echo '<p class="description">Enter the repository name (e.g., wdm-custom-header).</p>';
    }
    
    /**
     * Auto-update field callback
     */
    public function auto_update_callback() {
        $options = get_option('wdm_header_options', array());
        $auto_update = isset($options['auto_update']) ? $options['auto_update'] : '0';
        echo '<input type="checkbox" name="wdm_header_options[auto_update]" value="1" ' . checked('1', $auto_update, false) . ' />';
        echo '<label> Enable automatic updates from GitHub repository</label>';
    }
    
    /**
     * AJAX check for updates
     */
    public function ajax_check_update() {
        if (!wp_verify_nonce($_POST['nonce'], 'wdm_update_nonce') || !current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        $options = get_option('wdm_header_options', array());
        $username = isset($options['github_username']) ? $options['github_username'] : '';
        $repo = isset($options['github_repo']) ? $options['github_repo'] : '';
        
        if (empty($username) || empty($repo)) {
            wp_send_json_error('GitHub credentials not configured');
        }
        
        $api_url = "https://api.github.com/repos/{$username}/{$repo}/releases/latest";
        $response = wp_remote_get($api_url);
        
        if (is_wp_error($response)) {
            wp_send_json_error('Failed to connect to GitHub API');
        }
        
        $body = wp_remote_retrieve_body($response);
        $release = json_decode($body, true);
        
        if (isset($release['tag_name'])) {
            $remote_version = $release['tag_name'];
            $current_version = WDM_CUSTOM_HEADER_VERSION;
            
            if (version_compare($current_version, $remote_version, '<')) {
                wp_send_json_success(array(
                    'update_available' => true,
                    'current_version' => $current_version,
                    'remote_version' => $remote_version,
                    'download_url' => $release['zipball_url']
                ));
            } else {
                wp_send_json_success(array(
                    'update_available' => false,
                    'current_version' => $current_version,
                    'message' => 'Plugin is up to date'
                ));
            }
        } else {
            wp_send_json_error('Invalid response from GitHub API');
        }
    }
    
    /**
     * AJAX force update check
     */
    public function ajax_force_update() {
        if (!wp_verify_nonce($_POST['nonce'], 'wdm_update_nonce') || !current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        
        delete_site_transient('update_plugins');
        wp_update_plugins();
        
        wp_send_json_success('Update check triggered successfully');
    }
    
    /**
     * Render settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>WDM Custom Header Settings</h1>
            
            <form method="post" action="options.php">
                <?php
                settings_fields('wdm_header_settings');
                do_settings_sections('wdm_header_settings');
                submit_button();
                ?>
            </form>
            
            <div class="wdm-update-section" style="margin-top: 30px; padding: 20px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
                <h2>Plugin Information & Updates</h2>
                <p><strong>Current Version:</strong> <?php echo WDM_CUSTOM_HEADER_VERSION; ?></p>
                
                <div id="wdm-update-status" style="margin: 15px 0;"></div>
                
                <p>
                    <button type="button" id="wdm-check-update" class="button button-primary">Check for Updates</button>
                    <button type="button" id="wdm-force-update" class="button">Force Update Check</button>
                </p>
                
                <div class="shortcode-info" style="margin-top: 20px; padding: 15px; background: #fff; border-left: 4px solid #0073aa;">
                    <h3>Usage Instructions</h3>
                    <p><strong>Shortcode:</strong> Use <code>[wdm_custom_header]</code> to display the header in posts, pages, or widgets.</p>
                    <p><strong>PHP Function:</strong> Use <code>&lt;?php wdm_display_header(); ?&gt;</code> in your theme files.</p>
                </div>
            </div>
        </div>
        
        <script type="text/javascript">
        jQuery(document).ready(function($) {
            $('#wdm-check-update').on('click', function() {
                var button = $(this);
                var originalText = button.text();
                button.prop('disabled', true).text('Checking...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wdm_check_update',
                        nonce: '<?php echo wp_create_nonce('wdm_update_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            var data = response.data;
                            if (data.update_available) {
                                $('#wdm-update-status').html('<div class="notice notice-warning inline"><p><strong>Update Available!</strong> Version ' + data.remote_version + ' is available. Current version: ' + data.current_version + '</p></div>');
                            } else {
                                $('#wdm-update-status').html('<div class="notice notice-success inline"><p><strong>Up to Date!</strong> ' + data.message + ' (Version: ' + data.current_version + ')</p></div>');
                            }
                        } else {
                            $('#wdm-update-status').html('<div class="notice notice-error inline"><p><strong>Error:</strong> ' + response.data + '</p></div>');
                        }
                    },
                    error: function() {
                        $('#wdm-update-status').html('<div class="notice notice-error inline"><p><strong>Error:</strong> Failed to check for updates</p></div>');
                    },
                    complete: function() {
                        button.prop('disabled', false).text(originalText);
                    }
                });
            });
            
            $('#wdm-force-update').on('click', function() {
                var button = $(this);
                var originalText = button.text();
                button.prop('disabled', true).text('Triggering...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wdm_force_update',
                        nonce: '<?php echo wp_create_nonce('wdm_update_nonce'); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#wdm-update-status').html('<div class="notice notice-info inline"><p><strong>Success:</strong> ' + response.data + '. Refresh this page to see any available updates.</p></div>');
                        } else {
                            $('#wdm-update-status').html('<div class="notice notice-error inline"><p><strong>Error:</strong> ' + response.data + '</p></div>');
                        }
                    },
                    complete: function() {
                        button.prop('disabled', false).text(originalText);
                    }
                });
            });
        });
        </script>
        <?php
    }
}
