<?php
/**
 * WDM Settings Class
 * Handles admin settings page functionality with dynamic menu management and GitHub auto-update
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
        \add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
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
     * Enqueue admin assets
     */
    public function enqueue_admin_assets($hook_suffix) {
        // Only load on our settings page
        if ($hook_suffix !== 'settings_page_wdm-header-settings') {
            return;
        }
        
        wp_enqueue_script('jquery-ui-sortable');
        
        wp_enqueue_style(
            'wdm-admin-styles',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/admin-styles.css',
            array(),
            WDM_CUSTOM_HEADER_VERSION
        );
        
        wp_enqueue_script(
            'wdm-admin-script',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/admin-script.js',
            array('jquery', 'jquery-ui-sortable'),
            WDM_CUSTOM_HEADER_VERSION,
            true
        );
    }
    
    /**
     * Initialize settings
     */
    public function settings_init() {
        // Register settings
        \register_setting('wdm_header_settings', 'wdm_header_options', array($this, 'sanitize_options'));
        \register_setting('wdm_menu_settings', 'wdm_menu_items', array($this, 'sanitize_menu_items'));
    }
    
    /**
     * Sanitize general options
     */
    public function sanitize_options($input) {
        $sanitized = array();
        
        $sanitized['load_css'] = isset($input['load_css']) ? '1' : '0';
        $sanitized['github_username'] = isset($input['github_username']) ? sanitize_text_field($input['github_username']) : '';
        $sanitized['github_repo'] = isset($input['github_repo']) ? sanitize_text_field($input['github_repo']) : '';
        $sanitized['auto_update'] = isset($input['auto_update']) ? '1' : '0';
        
        // Utility navigation settings
        $sanitized['volunteer_text'] = isset($input['volunteer_text']) ? sanitize_text_field($input['volunteer_text']) : 'Volunteer';
        $sanitized['volunteer_url'] = isset($input['volunteer_url']) ? esc_url_raw($input['volunteer_url']) : '#volunteer';
        $sanitized['donate_text'] = isset($input['donate_text']) ? sanitize_text_field($input['donate_text']) : 'Donate';
        $sanitized['donate_url'] = isset($input['donate_url']) ? esc_url_raw($input['donate_url']) : '#donate';
        $sanitized['show_search'] = isset($input['show_search']) ? '1' : '0';
        
        return $sanitized;
    }
    
    /**
     * Sanitize menu items
     */
    public function sanitize_menu_items($input) {
        if (!is_array($input)) {
            return array();
        }
        
        $sanitized = array();
        
        foreach ($input as $index => $item) {
            if (!is_array($item)) continue;
            
            $sanitized_item = array();
            $sanitized_item['text'] = isset($item['text']) ? sanitize_text_field($item['text']) : '';
            $sanitized_item['url'] = isset($item['url']) ? esc_url_raw($item['url']) : '';
            $sanitized_item['target'] = isset($item['target']) && in_array($item['target'], array('_self', '_blank')) ? $item['target'] : '_self';
            $sanitized_item['submenu'] = array();
            
            // Sanitize submenu items
            if (isset($item['submenu']) && is_array($item['submenu'])) {
                foreach ($item['submenu'] as $sub_index => $sub_item) {
                    if (!is_array($sub_item)) continue;
                    
                    $sanitized_sub = array();
                    $sanitized_sub['text'] = isset($sub_item['text']) ? sanitize_text_field($sub_item['text']) : '';
                    $sanitized_sub['url'] = isset($sub_item['url']) ? esc_url_raw($sub_item['url']) : '';
                    $sanitized_sub['target'] = isset($sub_item['target']) && in_array($sub_item['target'], array('_self', '_blank')) ? $sub_item['target'] : '_self';
                    $sanitized_sub['description'] = isset($sub_item['description']) ? sanitize_textarea_field($sub_item['description']) : '';
                    
                    $sanitized_item['submenu'][] = $sanitized_sub;
                }
            }
            
            $sanitized[] = $sanitized_item;
        }
        
        return $sanitized;
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
        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'headers' => array(
                'User-Agent' => 'WDM-Custom-Header-Plugin'
            )
        ));
        
        if (is_wp_error($response)) {
            wp_send_json_error('Failed to connect to GitHub API: ' . $response->get_error_message());
        }
        
        $response_code = wp_remote_retrieve_response_code($response);
        $body = wp_remote_retrieve_body($response);
        
        if ($response_code !== 200) {
            wp_send_json_error('GitHub API returned error code: ' . $response_code . '. Response: ' . $body);
        }
        
        $release = json_decode($body, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            wp_send_json_error('Invalid JSON response from GitHub API');
        }
        
        if (isset($release['tag_name'])) {
            $remote_version = ltrim($release['tag_name'], 'v');
            $current_version = WDM_CUSTOM_HEADER_VERSION;
            
            if (version_compare($current_version, $remote_version, '<')) {
                wp_send_json_success(array(
                    'update_available' => true,
                    'current_version' => $current_version,
                    'remote_version' => $remote_version,
                    'download_url' => isset($release['zipball_url']) ? $release['zipball_url'] : ''
                ));
            } else {
                wp_send_json_success(array(
                    'update_available' => false,
                    'current_version' => $current_version,
                    'message' => 'Plugin is up to date'
                ));
            }
        } else {
            wp_send_json_error('No release information found. Make sure the repository has releases.');
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
     * Get default menu items
     */
    private function get_default_menu_items() {
        return array(
            array(
                'text' => 'How We Serve',
                'url' => '#how-we-serve',
                'target' => '_self',
                'submenu' => array(
                    array(
                        'text' => 'Disaster Response',
                        'url' => '#disaster-response',
                        'target' => '_self',
                        'description' => 'Rapid response to natural disasters and emergencies'
                    ),
                    array(
                        'text' => 'Long Term Recovery',
                        'url' => '#long-term-recovery',
                        'target' => '_self',
                        'description' => 'Sustainable rebuilding and community support'
                    ),
                    array(
                        'text' => 'International',
                        'url' => '#international',
                        'target' => '_self',
                        'description' => 'Global humanitarian assistance programs'
                    ),
                    array(
                        'text' => 'Domestic Operations',
                        'url' => '#domestic-operations',
                        'target' => '_self',
                        'description' => 'Local community disaster response services'
                    ),
                    array(
                        'text' => 'International Work',
                        'url' => '#international-work',
                        'target' => '_self',
                        'description' => 'Overseas humanitarian missions and support'
                    )
                )
            ),
            array(
                'text' => 'How To Get Involved',
                'url' => '#how-to-get-involved',
                'target' => '_self',
                'submenu' => array(
                    array(
                        'text' => 'Volunteer With Us',
                        'url' => '#volunteer-with-us',
                        'target' => '_self',
                        'description' => 'Join our volunteer team and make a difference'
                    ),
                    array(
                        'text' => 'Become a Partner',
                        'url' => '#become-a-partner',
                        'target' => '_self',
                        'description' => 'Partner with us for greater impact'
                    ),
                    array(
                        'text' => 'Build Your Skillset',
                        'url' => '#build-your-skillset',
                        'target' => '_self',
                        'description' => 'Training and development opportunities'
                    )
                )
            ),
            array(
                'text' => 'Ways to Give',
                'url' => '#ways-to-give',
                'target' => '_self',
                'submenu' => array(
                    array(
                        'text' => 'One-Time Gifts',
                        'url' => '#one-time-gifts',
                        'target' => '_self',
                        'description' => 'Make an immediate impact with a single donation'
                    ),
                    array(
                        'text' => 'Monthly Giving',
                        'url' => '#monthly-giving',
                        'target' => '_self',
                        'description' => 'Provide sustained support through recurring donations'
                    ),
                    array(
                        'text' => 'Legacy Giving',
                        'url' => '#legacy-giving',
                        'target' => '_self',
                        'description' => 'Leave a lasting impact through planned giving'
                    ),
                    array(
                        'text' => 'Other Giving Options',
                        'url' => '#other-giving-options',
                        'target' => '_self',
                        'description' => 'Alternative ways to support our mission'
                    )
                )
            ),
            array(
                'text' => 'News',
                'url' => '#news',
                'target' => '_self',
                'submenu' => array()
            ),
            array(
                'text' => 'About',
                'url' => '#about',
                'target' => '_self',
                'submenu' => array(
                    array(
                        'text' => 'Who We Are',
                        'url' => '#who-we-are',
                        'target' => '_self',
                        'description' => 'Learn about our mission, vision, and values'
                    ),
                    array(
                        'text' => 'Strategic Plan',
                        'url' => '#strategic-plan',
                        'target' => '_self',
                        'description' => 'Our roadmap for the future'
                    ),
                    array(
                        'text' => 'Program History',
                        'url' => '#program-history',
                        'target' => '_self',
                        'description' => 'Our track record of service and impact'
                    ),
                    array(
                        'text' => 'Leadership',
                        'url' => '#leadership',
                        'target' => '_self',
                        'description' => 'Meet our executive team and board'
                    ),
                    array(
                        'text' => 'Financials and Annual Reports',
                        'url' => '#financials-and-annual-reports',
                        'target' => '_self',
                        'description' => 'Transparency in our operations and finances'
                    ),
                    array(
                        'text' => 'Careers',
                        'url' => '#careers',
                        'target' => '_self',
                        'description' => 'Join our team and build a meaningful career'
                    ),
                    array(
                        'text' => 'Grey Bull Ventures',
                        'url' => '#grey-bull-ventures',
                        'target' => '_self',
                        'description' => 'Our innovation and development initiatives'
                    )
                )
            )
        );
    }
    
    /**
     * Render settings page
     */
    public function settings_page() {
        // Handle form submissions
        if (isset($_POST['wdm_save_menu']) && wp_verify_nonce($_POST['wdm_menu_nonce'], 'wdm_save_menu_settings')) {
            $menu_items = isset($_POST['wdm_menu_items']) ? $_POST['wdm_menu_items'] : array();
            $sanitized_menu = $this->sanitize_menu_items($menu_items);
            update_option('wdm_menu_items', $sanitized_menu);
            add_settings_error('wdm_menu_settings', 'menu_updated', 'Menu settings saved successfully!', 'success');
        }
        
        if (isset($_POST['wdm_save_general']) && wp_verify_nonce($_POST['wdm_general_nonce'], 'wdm_save_general_settings')) {
            $options = isset($_POST['wdm_header_options']) ? $_POST['wdm_header_options'] : array();
            $sanitized_options = $this->sanitize_options($options);
            update_option('wdm_header_options', $sanitized_options);
            add_settings_error('wdm_header_settings', 'options_updated', 'General settings saved successfully!', 'success');
        }
        
        // Get current options
        $options = get_option('wdm_header_options', array());
        $menu_items = get_option('wdm_menu_items', $this->get_default_menu_items());
        
        ?>
        <div class="wrap wdm-admin-container">
            <div class="wdm-admin-header">
                <h1>WDM Custom Header Settings</h1>
                <p>Manage your header menu content, appearance, and plugin settings.</p>
            </div>
            
            <?php settings_errors(); ?>
            
            <div class="wdm-tab-nav">
                <button type="button" class="active" data-tab="menu-content">Menu Content</button>
                <button type="button" data-tab="general-settings">General Settings</button>
                <button type="button" data-tab="plugin-info">Plugin Information</button>
            </div>
            
            <!-- Menu Content Tab -->
            <div id="menu-content" class="wdm-tab-content active">
                <div class="wdm-settings-section">
                    <h2 class="wdm-section-header">Dynamic Menu Management</h2>
                    <div class="wdm-section-content">
                        <form method="post" id="wdm-menu-settings-form">
                            <?php wp_nonce_field('wdm_save_menu_settings', 'wdm_menu_nonce'); ?>
                            
                            <div class="wdm-notice">
                                <strong>Instructions:</strong> Drag menu items to reorder them. Use "Add Submenu" to create dropdown menus. Empty URL fields will create dropdown-only items.
                            </div>
                            
                            <div class="wdm-menu-items">
                                <?php $this->render_menu_items($menu_items); ?>
                            </div>
                            
                            <div class="wdm-add-buttons">
                                <button type="button" class="wdm-btn wdm-btn-primary wdm-add-menu-item">Add Menu Item</button>
                            </div>
                            
                            <div class="wdm-preview-section">
                                <div class="wdm-preview-header">Menu Preview <button type="button" class="wdm-btn wdm-btn-small wdm-preview-header">Generate Preview</button></div>
                                <div class="wdm-preview-content">Click "Generate Preview" to see current menu structure</div>
                            </div>
                            
                            <input type="submit" name="wdm_save_menu" class="button button-primary button-large" value="Save Menu Settings" />
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- General Settings Tab -->
            <div id="general-settings" class="wdm-tab-content">
                <div class="wdm-settings-section">
                    <h2 class="wdm-section-header">General Settings</h2>
                    <div class="wdm-section-content">
                        <form method="post">
                            <?php wp_nonce_field('wdm_save_general_settings', 'wdm_general_nonce'); ?>
                            
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Load Default CSS</th>
                                    <td>
                                        <input type="checkbox" name="wdm_header_options[load_css]" value="1" <?php checked(isset($options['load_css']) ? $options['load_css'] : '1', '1'); ?> />
                                        <label>Enable default header styles</label>
                                        <p class="description">Uncheck to use your own custom CSS styles.</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <h3>Utility Navigation Settings</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Volunteer Button Text</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[volunteer_text]" value="<?php echo esc_attr(isset($options['volunteer_text']) ? $options['volunteer_text'] : 'Volunteer'); ?>" class="regular-text" />
                                        <p class="description">Text for the volunteer button in utility navigation.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Volunteer Button URL</th>
                                    <td>
                                        <input type="url" name="wdm_header_options[volunteer_url]" value="<?php echo esc_attr(isset($options['volunteer_url']) ? $options['volunteer_url'] : '#volunteer'); ?>" class="regular-text" />
                                        <p class="description">URL for the volunteer button.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Donate Button Text</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[donate_text]" value="<?php echo esc_attr(isset($options['donate_text']) ? $options['donate_text'] : 'Donate'); ?>" class="regular-text" />
                                        <p class="description">Text for the donate button in utility navigation.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Donate Button URL</th>
                                    <td>
                                        <input type="url" name="wdm_header_options[donate_url]" value="<?php echo esc_attr(isset($options['donate_url']) ? $options['donate_url'] : '#donate'); ?>" class="regular-text" />
                                        <p class="description">URL for the donate button.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Show Search</th>
                                    <td>
                                        <input type="checkbox" name="wdm_header_options[show_search]" value="1" <?php checked(isset($options['show_search']) ? $options['show_search'] : '1', '1'); ?> />
                                        <label>Enable search functionality in header</label>
                                    </td>
                                </tr>
                            </table>
                            
                            <h3>GitHub Auto-Update Settings</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">GitHub Username</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[github_username]" value="<?php echo esc_attr(isset($options['github_username']) ? $options['github_username'] : ''); ?>" placeholder="your-username" class="regular-text" />
                                        <p class="description">Enter your GitHub username where the plugin repository is hosted.</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">GitHub Repository</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[github_repo]" value="<?php echo esc_attr(isset($options['github_repo']) ? $options['github_repo'] : ''); ?>" placeholder="repository-name" class="regular-text" />
                                        <p class="description">Enter the repository name (e.g., wdm-custom-header).</p>
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Enable Auto-Update</th>
                                    <td>
                                        <input type="checkbox" name="wdm_header_options[auto_update]" value="1" <?php checked(isset($options['auto_update']) ? $options['auto_update'] : '0', '1'); ?> />
                                        <label>Enable automatic updates from GitHub repository</label>
                                    </td>
                                </tr>
                            </table>
                            
                            <input type="submit" name="wdm_save_general" class="button button-primary" value="Save General Settings" />
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Plugin Information Tab -->
            <div id="plugin-info" class="wdm-tab-content">
                <div class="wdm-settings-section">
                    <h2 class="wdm-section-header">Plugin Information & Updates</h2>
                    <div class="wdm-section-content">
                        <p><strong>Current Version:</strong> <?php echo WDM_CUSTOM_HEADER_VERSION; ?></p>
                        
                        <div id="wdm-update-status" style="margin: 15px 0;"></div>
                        
                        <p>
                            <button type="button" id="wdm-check-update" class="button button-primary">Check for Updates</button>
                            <button type="button" id="wdm-force-update" class="button">Force Update Check</button>
                        </p>
                        
                        <div class="wdm-notice">
                            <h3>Usage Instructions</h3>
                            <p><strong>Shortcode:</strong> Use <code>[wdm_custom_header]</code> to display the header in posts, pages, or widgets.</p>
                            <p><strong>PHP Function:</strong> Use <code>&lt;?php wdm_display_header(); ?&gt;</code> in your theme files.</p>
                        </div>
                    </div>
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
    
    /**
     * Render menu items in admin interface
     */
    private function render_menu_items($menu_items) {
        foreach ($menu_items as $index => $item) {
            $text = isset($item['text']) ? esc_attr($item['text']) : '';
            $url = isset($item['url']) ? esc_attr($item['url']) : '';
            $target = isset($item['target']) ? $item['target'] : '_self';
            $submenu = isset($item['submenu']) ? $item['submenu'] : array();
            
            ?>
            <div class="wdm-menu-item" data-index="<?php echo $index; ?>">
                <div class="wdm-menu-item-header">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-menu-item-title">Menu Item <?php echo $index + 1; ?></span>
                    <div class="wdm-menu-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-add-submenu-item">Add Submenu</button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-toggle-submenu">
                            <?php echo count($submenu) > 0 ? 'Hide Submenu' : 'Show Submenu (0)'; ?>
                        </button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-menu-item">Remove</button>
                    </div>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Menu Text</label>
                        <input type="text" name="wdm_menu_items[<?php echo $index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" placeholder="Menu Item Text" />
                        <div class="wdm-help-text">Text displayed in the navigation menu</div>
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="url" name="wdm_menu_items[<?php echo $index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" placeholder="https://example.com" />
                        <div class="wdm-help-text">Link destination (leave empty for dropdown-only)</div>
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_menu_items[<?php echo $index; ?>][target]" class="wdm-form-select">
                            <option value="_self" <?php selected($target, '_self'); ?>>Same Window</option>
                            <option value="_blank" <?php selected($target, '_blank'); ?>>New Window</option>
                        </select>
                    </div>
                </div>
                
                <div class="wdm-submenu-items <?php echo count($submenu) === 0 ? 'hidden' : ''; ?>">
                    <?php $this->render_submenu_items($index, $submenu); ?>
                </div>
            </div>
            <?php
        }
    }
    
    /**
     * Render submenu items
     */
    private function render_submenu_items($menu_index, $submenu_items) {
        foreach ($submenu_items as $sub_index => $sub_item) {
            $text = isset($sub_item['text']) ? esc_attr($sub_item['text']) : '';
            $url = isset($sub_item['url']) ? esc_attr($sub_item['url']) : '';
            $target = isset($sub_item['target']) ? $sub_item['target'] : '_self';
            $description = isset($sub_item['description']) ? esc_textarea($sub_item['description']) : '';
            
            ?>
            <div class="wdm-submenu-item" data-submenu-index="<?php echo $sub_index; ?>">
                <div class="wdm-submenu-header">
                    <span class="wdm-submenu-title">Submenu Item <?php echo $sub_index + 1; ?></span>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-submenu-item">Remove</button>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Text</label>
                        <input type="text" name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" placeholder="Submenu Text" />
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="url" name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" placeholder="https://example.com" />
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][target]" class="wdm-form-select">
                            <option value="_self" <?php selected($target, '_self'); ?>>Same Window</option>
                            <option value="_blank" <?php selected($target, '_blank'); ?>>New Window</option>
                        </select>
                    </div>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Description</label>
                        <textarea name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][description]" class="wdm-form-input wdm-form-textarea" placeholder="Optional description for mega menu"><?php echo $description; ?></textarea>
                        <div class="wdm-help-text">Brief description shown in mega menu dropdowns</div>
                    </div>
                </div>
            </div>
            <?php
        }
    }
}
