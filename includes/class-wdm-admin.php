<?php
/**
 * WDM Custom Header Admin Class
 * Handles WordPress admin integration
 */

namespace WDM_Custom_Header;

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class WDM_Admin {
    
    /**
     * Initialize admin functionality
     */
    public static function init() {
        add_action('admin_menu', [__CLASS__, 'add_admin_menu']);
        add_action('admin_init', [__CLASS__, 'init_settings']);
        add_action('admin_enqueue_scripts', [__CLASS__, 'enqueue_admin_scripts']);
        add_action('admin_notices', [__CLASS__, 'check_for_updates']);
    }
    
    /**
     * Add admin menu page
     */
    public static function add_admin_menu() {
        add_options_page(
            'WDM Header Settings',
            'WDM Header',
            'manage_options',
            'wdm-header-settings',
            [__CLASS__, 'settings_page']
        );
    }
    
    /**
     * Initialize settings
     */
    public static function init_settings() {
        register_setting('wdm_header_settings', 'wdm_header_options');
        
        // General Settings Section
        add_settings_section(
            'wdm_header_general',
            'General Settings',
            [__CLASS__, 'general_section_callback'],
            'wdm-header-settings'
        );
        
        add_settings_field(
            'enable_header',
            'Enable Header',
            [__CLASS__, 'checkbox_field_callback'],
            'wdm-header-settings',
            'wdm_header_general',
            ['field' => 'enable_header', 'label' => 'Enable WDM Custom Header']
        );
        
        add_settings_field(
            'logo_url',
            'Logo URL',
            [__CLASS__, 'text_field_callback'],
            'wdm-header-settings',
            'wdm_header_general',
            ['field' => 'logo_url', 'placeholder' => 'https://example.com/logo.png']
        );
        
        // Menu Settings Section
        add_settings_section(
            'wdm_header_menu',
            'Menu Settings',
            [__CLASS__, 'menu_section_callback'],
            'wdm-header-settings'
        );
        
        // Utility Buttons Section
        add_settings_section(
            'wdm_header_buttons',
            'Utility Buttons',
            [__CLASS__, 'buttons_section_callback'],
            'wdm-header-settings'
        );
        
        // Auto Updates Section
        add_settings_section(
            'wdm_header_updates',
            'Auto Updates',
            [__CLASS__, 'updates_section_callback'],
            'wdm-header-settings'
        );
        
        add_settings_field(
            'enable_auto_updates',
            'Enable Auto Updates',
            [__CLASS__, 'checkbox_field_callback'],
            'wdm-header-settings',
            'wdm_header_updates',
            ['field' => 'enable_auto_updates', 'label' => 'Automatically check for plugin updates from GitHub']
        );
        
        add_settings_field(
            'github_username',
            'GitHub Username',
            [__CLASS__, 'text_field_callback'],
            'wdm-header-settings',
            'wdm_header_updates',
            ['field' => 'github_username', 'placeholder' => 'your-username']
        );
        
        add_settings_field(
            'github_repo',
            'Repository Name',
            [__CLASS__, 'text_field_callback'],
            'wdm-header-settings',
            'wdm_header_updates',
            ['field' => 'github_repo', 'placeholder' => 'wdm-custom-header']
        );
        
        add_settings_field(
            'github_token',
            'GitHub Token (Optional)',
            [__CLASS__, 'password_field_callback'],
            'wdm-header-settings',
            'wdm_header_updates',
            ['field' => 'github_token', 'placeholder' => 'ghp_xxxxxxxxxxxxxxxxxxxx']
        );
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_wdm-header-settings') {
            return;
        }
        
        wp_enqueue_script('jquery');
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');
        
        // Add custom admin CSS
        wp_add_inline_style('wp-admin', '
            .wdm-menu-item, .wdm-button-item {
                border: 1px solid #ddd;
                padding: 15px;
                margin: 10px 0;
                background: #f9f9f9;
            }
            .wdm-item-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 10px;
            }
            .wdm-item-content {
                display: none;
            }
            .wdm-item-content.active {
                display: block;
            }
            .wdm-version-footer {
                margin-top: 20px;
                padding: 10px;
                background: #f1f1f1;
                border-left: 4px solid #0073aa;
                font-size: 12px;
                color: #666;
            }
        ');
        
        // Add custom admin JavaScript
        wp_add_inline_script('jquery', '
            jQuery(document).ready(function($) {
                $(".wdm-toggle-item").click(function(e) {
                    e.preventDefault();
                    var content = $(this).closest(".wdm-menu-item, .wdm-button-item").find(".wdm-item-content");
                    content.toggleClass("active");
                    $(this).text(content.hasClass("active") ? "Close" : "Edit");
                });
                
                $(".wdm-remove-item").click(function(e) {
                    e.preventDefault();
                    if (confirm("Are you sure you want to remove this item?")) {
                        $(this).closest(".wdm-menu-item, .wdm-button-item").remove();
                    }
                });
                
                $("#wdm-add-menu-item").click(function(e) {
                    e.preventDefault();
                    // Add new menu item logic
                });
                
                $("#wdm-add-button").click(function(e) {
                    e.preventDefault();
                    // Add new button logic
                });
                
                $("#wdm-check-updates").click(function(e) {
                    e.preventDefault();
                    var button = $(this);
                    button.text("Checking...").prop("disabled", true);
                    
                    $.post(ajaxurl, {
                        action: "wdm_check_updates",
                        nonce: "' . wp_create_nonce('wdm_check_updates') . '"
                    }).done(function(response) {
                        button.text("Check Complete");
                        setTimeout(function() {
                            button.text("Check for Updates Now").prop("disabled", false);
                            location.reload();
                        }, 2000);
                    }).fail(function() {
                        button.text("Check for Updates Now").prop("disabled", false);
                    });
                });
            });
        ');
    }
    
    /**
     * Settings page content
     */
    public static function settings_page() {
        if (!current_user_can('manage_options')) {
            return;
        }
        
        // Handle form submission
        if (isset($_GET['settings-updated'])) {
            add_settings_error('wdm_header_messages', 'wdm_header_message', 'Settings Saved', 'updated');
        }
        
        settings_errors('wdm_header_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('wdm_header_settings');
                do_settings_sections('wdm-header-settings');
                submit_button('Save Settings');
                ?>
            </form>
            
            <div class="wdm-version-footer">
                <strong>WDM Custom Header</strong> version <?php echo WDM_CUSTOM_HEADER_VERSION; ?> | 
                <a href="#" id="wdm-check-updates">Check for Updates Now</a>
            </div>
        </div>
        <?php
    }
    
    /**
     * Section callbacks
     */
    public static function general_section_callback() {
        echo '<p>Configure WDM Custom Header general settings.</p>';
    }
    
    public static function menu_section_callback() {
        echo '<p>Configure the navigation menu items.</p>';
        self::render_menu_items();
    }
    
    public static function buttons_section_callback() {
        echo '<p>Configure the utility buttons (Volunteer, Donate, etc.)</p>';
        self::render_utility_buttons();
    }
    
    public static function updates_section_callback() {
        echo '<p>Configure automatic updates from GitHub repository.</p>';
        echo '<div style="background: #f9f9f9; border: 1px solid #ddd; padding: 15px; margin: 20px 0;">';
        echo '<h4>How it works:</h4>';
        echo '<ol>';
        echo '<li>The plugin checks for new releases on your GitHub repository</li>';
        echo '<li>When a new version is available, you\'ll see an "Update Now" notification</li>';
        echo '<li>Click "Update Now" to automatically download and install the latest version</li>';
        echo '</ol>';
        echo '<p><strong>Setup:</strong> Create GitHub releases with semantic versioning (v1.0.0, v1.1.0, etc.)</p>';
        echo '</div>';
    }
    
    /**
     * Field callbacks
     */
    public static function checkbox_field_callback($args) {
        $options = get_option('wdm_header_options', []);
        $value = $options[$args['field']] ?? false;
        ?>
        <label>
            <input type="checkbox" name="wdm_header_options[<?php echo $args['field']; ?>]" value="1" <?php checked($value, true); ?>>
            <?php echo $args['label']; ?>
        </label>
        <?php
    }
    
    public static function text_field_callback($args) {
        $options = get_option('wdm_header_options', []);
        $value = $options[$args['field']] ?? '';
        ?>
        <input type="text" name="wdm_header_options[<?php echo $args['field']; ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($args['placeholder'] ?? ''); ?>">
        <?php
    }
    
    public static function password_field_callback($args) {
        $options = get_option('wdm_header_options', []);
        $value = $options[$args['field']] ?? '';
        ?>
        <input type="password" name="wdm_header_options[<?php echo $args['field']; ?>]" value="<?php echo esc_attr($value); ?>" class="regular-text" placeholder="<?php echo esc_attr($args['placeholder'] ?? ''); ?>">
        <p class="description">Personal access token for private repositories or higher rate limits. Leave empty for public repositories.</p>
        <?php
    }
    
    /**
     * Render menu items section
     */
    private static function render_menu_items() {
        $options = get_option('wdm_header_options', []);
        $menu_items = $options['menu_items'] ?? self::get_default_menu_items();
        
        echo '<div id="wdm-menu-items">';
        foreach ($menu_items as $index => $item) {
            self::render_menu_item($item, $index);
        }
        echo '</div>';
        echo '<button type="button" id="wdm-add-menu-item" class="button">Add Menu Item</button>';
    }
    
    /**
     * Render utility buttons section
     */
    private static function render_utility_buttons() {
        $options = get_option('wdm_header_options', []);
        $buttons = $options['utility_buttons'] ?? self::get_default_utility_buttons();
        
        echo '<div id="wdm-utility-buttons">';
        foreach ($buttons as $index => $button) {
            self::render_utility_button($button, $index);
        }
        echo '</div>';
        echo '<button type="button" id="wdm-add-button" class="button">Add Button</button>';
    }
    
    /**
     * Render individual menu item
     */
    private static function render_menu_item($item, $index) {
        ?>
        <div class="wdm-menu-item">
            <div class="wdm-item-header">
                <strong><?php echo esc_html($item['title'] ?? 'Menu Item'); ?></strong>
                <div>
                    <button type="button" class="button wdm-toggle-item">Edit</button>
                    <button type="button" class="button wdm-remove-item">Remove</button>
                </div>
            </div>
            <div class="wdm-item-content">
                <table class="form-table">
                    <tr>
                        <th>Title</th>
                        <td><input type="text" name="wdm_header_options[menu_items][<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><input type="text" name="wdm_header_options[menu_items][<?php echo $index; ?>][url]" value="<?php echo esc_attr($item['url'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Has Dropdown</th>
                        <td><label><input type="checkbox" name="wdm_header_options[menu_items][<?php echo $index; ?>][has_dropdown]" value="1" <?php checked($item['has_dropdown'] ?? false, true); ?>> Enable mega dropdown menu</label></td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render individual utility button
     */
    private static function render_utility_button($button, $index) {
        ?>
        <div class="wdm-button-item">
            <div class="wdm-item-header">
                <strong><?php echo esc_html($button['label'] ?? 'Button'); ?></strong>
                <div>
                    <button type="button" class="button wdm-toggle-item">Edit</button>
                    <button type="button" class="button wdm-remove-item">Remove</button>
                </div>
            </div>
            <div class="wdm-item-content">
                <table class="form-table">
                    <tr>
                        <th>Label</th>
                        <td><input type="text" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][label]" value="<?php echo esc_attr($button['label'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>URL</th>
                        <td><input type="text" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][url]" value="<?php echo esc_attr($button['url'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>CSS Class</th>
                        <td><input type="text" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][class]" value="<?php echo esc_attr($button['class'] ?? ''); ?>" class="regular-text"></td>
                    </tr>
                    <tr>
                        <th>Target</th>
                        <td>
                            <select name="wdm_header_options[utility_buttons][<?php echo $index; ?>][target]">
                                <option value="_self" <?php selected($button['target'] ?? '_self', '_self'); ?>>Same Window</option>
                                <option value="_blank" <?php selected($button['target'] ?? '_self', '_blank'); ?>>New Window</option>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <?php
    }
    
    /**
     * Get default menu items
     */
    private static function get_default_menu_items() {
        return [
            [
                'title' => 'How We Serve',
                'url' => '#how-we-serve',
                'has_dropdown' => true,
                'dropdown_items' => [
                    ['title' => 'Emergency Response', 'url' => '#emergency-response'],
                    ['title' => 'Training Programs', 'url' => '#training'],
                    ['title' => 'Community Outreach', 'url' => '#community']
                ]
            ],
            [
                'title' => 'About',
                'url' => '#about',
                'has_dropdown' => false
            ],
            [
                'title' => 'Get Involved',
                'url' => '#get-involved',
                'has_dropdown' => false
            ],
            [
                'title' => 'News',
                'url' => '#news',
                'has_dropdown' => false
            ],
            [
                'title' => 'Contact',
                'url' => '#contact',
                'has_dropdown' => false
            ]
        ];
    }
    
    /**
     * Get default utility buttons
     */
    private static function get_default_utility_buttons() {
        return [
            [
                'label' => 'VOLUNTEER',
                'url' => '#volunteer',
                'class' => 'btn-volunteer',
                'target' => '_self',
                'visibility' => 'both'
            ],
            [
                'label' => 'DONATE',
                'url' => '#donate',
                'class' => 'btn-donate',
                'target' => '_blank',
                'visibility' => 'both'
            ]
        ];
    }
    
    /**
     * Check for updates and show admin notices
     */
    public static function check_for_updates() {
        $options = get_option('wdm_header_options', []);
        
        if (!($options['enable_auto_updates'] ?? false)) {
            return;
        }
        
        $github_username = $options['github_username'] ?? '';
        $github_repo = $options['github_repo'] ?? '';
        
        if (empty($github_username) || empty($github_repo)) {
            return;
        }
        
        // Check for updates (simplified version)
        $current_version = WDM_CUSTOM_HEADER_VERSION;
        $latest_version = get_transient('wdm_header_latest_version');
        
        if ($latest_version === false) {
            // Fetch latest version from GitHub
            $github_token = $options['github_token'] ?? '';
            $response = wp_remote_get("https://api.github.com/repos/{$github_username}/{$github_repo}/releases/latest", [
                'headers' => $github_token ? ['Authorization' => 'token ' . $github_token] : []
            ]);
            
            if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                $body = json_decode(wp_remote_retrieve_body($response), true);
                $latest_version = ltrim($body['tag_name'] ?? '', 'v');
                set_transient('wdm_header_latest_version', $latest_version, 12 * HOUR_IN_SECONDS);
            }
        }
        
        if ($latest_version && version_compare($current_version, $latest_version, '<')) {
            ?>
            <div class="notice notice-warning is-dismissible">
                <p>
                    <strong>WDM Custom Header:</strong> There is a new version available. 
                    <a href="#" class="button button-primary" onclick="alert('Update functionality would be implemented here')">Update Now</a>
                </p>
            </div>
            <?php
        }
    }
}