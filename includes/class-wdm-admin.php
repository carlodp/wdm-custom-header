<?php
/**
 * WDM Custom Header Admin Interface
 * Provides WordPress admin panel for managing header settings
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
        add_action('admin_menu', array(__CLASS__, 'add_admin_menu'));
        add_action('admin_init', array(__CLASS__, 'register_settings'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueue_admin_scripts'));
        add_action('wp_ajax_wdm_save_menu_items', array(__CLASS__, 'save_menu_items'));
        add_action('wp_ajax_wdm_save_utility_buttons', array(__CLASS__, 'save_utility_buttons'));
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
            array(__CLASS__, 'admin_page')
        );
    }
    
    /**
     * Register settings
     */
    public static function register_settings() {
        // Logo settings
        register_setting('wdm_header_settings', 'wdm_header_logo_url');
        register_setting('wdm_header_settings', 'wdm_header_logo_alt');
        
        // Navigation menu items
        register_setting('wdm_header_settings', 'wdm_header_menu_items');
        
        // Utility buttons
        register_setting('wdm_header_settings', 'wdm_header_utility_buttons');
        
        // Color settings
        register_setting('wdm_header_settings', 'wdm_header_primary_color');
        register_setting('wdm_header_settings', 'wdm_header_secondary_color');
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public static function enqueue_admin_scripts($hook) {
        if ($hook !== 'settings_page_wdm-header-settings') {
            return;
        }
        
        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_script('wp-color-picker');
        wp_enqueue_style('wp-color-picker');
        
        // Custom admin script
        wp_enqueue_script(
            'wdm-admin-script',
            plugin_dir_url(__FILE__) . '../assets/js/admin.js',
            array('jquery', 'jquery-ui-sortable', 'wp-color-picker'),
            '1.0.0',
            true
        );
        
        wp_localize_script('wdm-admin-script', 'wdm_admin_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('wdm_admin_nonce')
        ));
        
        // Custom admin styles
        wp_enqueue_style(
            'wdm-admin-style',
            plugin_dir_url(__FILE__) . '../assets/css/admin.css',
            array(),
            '1.0.0'
        );
    }
    
    /**
     * Admin page content
     */
    public static function admin_page() {
        // Get current settings
        $logo_url = get_option('wdm_header_logo_url', 'https://greybullrescue.org/wp-content/uploads/2025/02/GB_Rescue-Color.png');
        $logo_alt = get_option('wdm_header_logo_alt', 'Greybull Rescue');
        $menu_items = get_option('wdm_header_menu_items', self::get_default_menu_items());
        $utility_buttons = get_option('wdm_header_utility_buttons', self::get_default_utility_buttons());
        $primary_color = get_option('wdm_header_primary_color', '#1a365d');
        $secondary_color = get_option('wdm_header_secondary_color', '#2d3748');
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <div class="wdm-admin-container">
                
                <!-- Logo Settings -->
                <div class="wdm-admin-section">
                    <h2>Logo Settings</h2>
                    <form method="post" action="options.php">
                        <?php settings_fields('wdm_header_settings'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">Logo URL</th>
                                <td>
                                    <input type="url" name="wdm_header_logo_url" value="<?php echo esc_attr($logo_url); ?>" class="regular-text" />
                                    <p class="description">Enter the URL for your logo image.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Logo Alt Text</th>
                                <td>
                                    <input type="text" name="wdm_header_logo_alt" value="<?php echo esc_attr($logo_alt); ?>" class="regular-text" />
                                    <p class="description">Alt text for accessibility.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Logo Settings'); ?>
                    </form>
                </div>
                
                <!-- Navigation Menu Items -->
                <div class="wdm-admin-section">
                    <h2>Navigation Menu</h2>
                    <div id="wdm-menu-items">
                        <?php foreach ($menu_items as $index => $item): ?>
                            <div class="wdm-menu-item" data-index="<?php echo $index; ?>">
                                <div class="wdm-menu-item-header">
                                    <span class="wdm-menu-item-title"><?php echo esc_html($item['title']); ?></span>
                                    <div class="wdm-menu-item-controls">
                                        <button type="button" class="button wdm-toggle-item">Edit</button>
                                        <button type="button" class="button wdm-remove-item">Remove</button>
                                        <span class="wdm-drag-handle">≡</span>
                                    </div>
                                </div>
                                
                                <div class="wdm-menu-item-content" style="display: none;">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">Title</th>
                                            <td>
                                                <input type="text" name="menu_items[<?php echo $index; ?>][title]" value="<?php echo esc_attr($item['title']); ?>" class="regular-text" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">URL</th>
                                            <td>
                                                <input type="url" name="menu_items[<?php echo $index; ?>][url]" value="<?php echo esc_attr($item['url']); ?>" class="regular-text" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Has Dropdown</th>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="menu_items[<?php echo $index; ?>][has_dropdown]" value="1" <?php checked(!empty($item['has_dropdown'])); ?> />
                                                    Enable mega dropdown menu
                                                </label>
                                            </td>
                                        </tr>
                                        <?php if (!empty($item['has_dropdown']) && !empty($item['dropdown_items'])): ?>
                                        <tr class="wdm-dropdown-section">
                                            <th scope="row">Dropdown Items</th>
                                            <td>
                                                <div class="wdm-dropdown-items">
                                                    <?php foreach ($item['dropdown_items'] as $dropdown_index => $dropdown_item): ?>
                                                    <div class="wdm-dropdown-item">
                                                        <input type="text" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $dropdown_index; ?>][title]" value="<?php echo esc_attr($dropdown_item['title']); ?>" placeholder="Title" />
                                                        <input type="url" name="menu_items[<?php echo $index; ?>][dropdown_items][<?php echo $dropdown_index; ?>][url]" value="<?php echo esc_attr($dropdown_item['url']); ?>" placeholder="URL" />
                                                        <button type="button" class="button wdm-remove-dropdown-item">Remove</button>
                                                    </div>
                                                    <?php endforeach; ?>
                                                </div>
                                                <button type="button" class="button wdm-add-dropdown-item">Add Dropdown Item</button>
                                            </td>
                                        </tr>
                                        <?php endif; ?>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="wdm-admin-actions">
                        <button type="button" class="button button-secondary" id="wdm-add-menu-item">Add Menu Item</button>
                        <button type="button" class="button button-primary" id="wdm-save-menu-items">Save Menu Items</button>
                    </div>
                </div>
                
                <!-- Utility Buttons -->
                <div class="wdm-admin-section">
                    <h2>Utility Buttons</h2>
                    <div id="wdm-utility-buttons">
                        <?php foreach ($utility_buttons as $index => $button): ?>
                            <div class="wdm-utility-button" data-index="<?php echo $index; ?>">
                                <div class="wdm-utility-button-header">
                                    <span class="wdm-utility-button-title"><?php echo esc_html($button['label']); ?></span>
                                    <div class="wdm-utility-button-controls">
                                        <button type="button" class="button wdm-toggle-button">Edit</button>
                                        <button type="button" class="button wdm-remove-button">Remove</button>
                                        <span class="wdm-drag-handle">≡</span>
                                    </div>
                                </div>
                                
                                <div class="wdm-utility-button-content" style="display: none;">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row">Label</th>
                                            <td>
                                                <input type="text" name="utility_buttons[<?php echo $index; ?>][label]" value="<?php echo esc_attr($button['label']); ?>" class="regular-text" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">URL</th>
                                            <td>
                                                <input type="url" name="utility_buttons[<?php echo $index; ?>][url]" value="<?php echo esc_attr($button['url']); ?>" class="regular-text" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">CSS Class</th>
                                            <td>
                                                <input type="text" name="utility_buttons[<?php echo $index; ?>][class]" value="<?php echo esc_attr($button['class']); ?>" class="regular-text" />
                                                <p class="description">Custom CSS class for styling (e.g., btn-volunteer, btn-donate)</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Target</th>
                                            <td>
                                                <select name="utility_buttons[<?php echo $index; ?>][target]">
                                                    <option value="_self" <?php selected($button['target'], '_self'); ?>>Same window</option>
                                                    <option value="_blank" <?php selected($button['target'], '_blank'); ?>>New window</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row">Visibility</th>
                                            <td>
                                                <select name="utility_buttons[<?php echo $index; ?>][visibility]">
                                                    <option value="both" <?php selected($button['visibility'] ?? 'both', 'both'); ?>>Desktop & Mobile</option>
                                                    <option value="desktop" <?php selected($button['visibility'] ?? 'both', 'desktop'); ?>>Desktop only</option>
                                                    <option value="mobile" <?php selected($button['visibility'] ?? 'both', 'mobile'); ?>>Mobile only</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="wdm-admin-actions">
                        <button type="button" class="button button-secondary" id="wdm-add-utility-button">Add Utility Button</button>
                        <button type="button" class="button button-primary" id="wdm-save-utility-buttons">Save Utility Buttons</button>
                    </div>
                </div>
                
                <!-- Color Settings -->
                <div class="wdm-admin-section">
                    <h2>Color Settings</h2>
                    <form method="post" action="options.php">
                        <?php settings_fields('wdm_header_settings'); ?>
                        
                        <table class="form-table">
                            <tr>
                                <th scope="row">Primary Color</th>
                                <td>
                                    <input type="text" name="wdm_header_primary_color" value="<?php echo esc_attr($primary_color); ?>" class="wdm-color-picker" />
                                    <p class="description">Main header background color.</p>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row">Secondary Color</th>
                                <td>
                                    <input type="text" name="wdm_header_secondary_color" value="<?php echo esc_attr($secondary_color); ?>" class="wdm-color-picker" />
                                    <p class="description">Button and accent color.</p>
                                </td>
                            </tr>
                        </table>
                        
                        <?php submit_button('Save Color Settings'); ?>
                    </form>
                </div>
                
            </div>
        </div>
        <?php
    }
    
    /**
     * Get default menu items
     */
    private static function get_default_menu_items() {
        return array(
            array(
                'title' => 'How We Serve',
                'url' => '#how-we-serve',
                'has_dropdown' => true,
                'dropdown_items' => array(
                    array('title' => 'Emergency Response', 'url' => '#emergency-response'),
                    array('title' => 'Training Programs', 'url' => '#training'),
                    array('title' => 'Community Outreach', 'url' => '#community')
                )
            ),
            array(
                'title' => 'About',
                'url' => '#about',
                'has_dropdown' => false
            ),
            array(
                'title' => 'Get Involved',
                'url' => '#get-involved',
                'has_dropdown' => true,
                'dropdown_items' => array(
                    array('title' => 'Volunteer', 'url' => '#volunteer'),
                    array('title' => 'Donate', 'url' => '#donate'),
                    array('title' => 'Partner With Us', 'url' => '#partner')
                )
            ),
            array(
                'title' => 'News',
                'url' => '#news',
                'has_dropdown' => false
            ),
            array(
                'title' => 'Contact',
                'url' => '#contact',
                'has_dropdown' => false
            )
        );
    }
    
    /**
     * Get default utility buttons
     */
    private static function get_default_utility_buttons() {
        return array(
            array(
                'label' => 'VOLUNTEER',
                'url' => '#volunteer',
                'class' => 'btn-volunteer',
                'target' => '_self',
                'visibility' => 'desktop'
            ),
            array(
                'label' => 'DONATE',
                'url' => '#donate',
                'class' => 'btn-donate',
                'target' => '_blank',
                'visibility' => 'both'
            )
        );
    }
    
    /**
     * Save menu items via AJAX
     */
    public static function save_menu_items() {
        check_ajax_referer('wdm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        $menu_items = isset($_POST['menu_items']) ? $_POST['menu_items'] : array();
        
        // Sanitize menu items
        $sanitized_items = array();
        foreach ($menu_items as $item) {
            $sanitized_item = array(
                'title' => sanitize_text_field($item['title']),
                'url' => esc_url_raw($item['url']),
                'has_dropdown' => !empty($item['has_dropdown'])
            );
            
            if (!empty($item['dropdown_items'])) {
                $sanitized_item['dropdown_items'] = array();
                foreach ($item['dropdown_items'] as $dropdown_item) {
                    $sanitized_item['dropdown_items'][] = array(
                        'title' => sanitize_text_field($dropdown_item['title']),
                        'url' => esc_url_raw($dropdown_item['url'])
                    );
                }
            }
            
            $sanitized_items[] = $sanitized_item;
        }
        
        update_option('wdm_header_menu_items', $sanitized_items);
        
        wp_send_json_success('Menu items saved successfully.');
    }
    
    /**
     * Save utility buttons via AJAX
     */
    public static function save_utility_buttons() {
        check_ajax_referer('wdm_admin_nonce', 'nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('You do not have sufficient permissions to access this page.'));
        }
        
        $utility_buttons = isset($_POST['utility_buttons']) ? $_POST['utility_buttons'] : array();
        
        // Sanitize utility buttons
        $sanitized_buttons = array();
        foreach ($utility_buttons as $button) {
            $sanitized_buttons[] = array(
                'label' => sanitize_text_field($button['label']),
                'url' => esc_url_raw($button['url']),
                'class' => sanitize_html_class($button['class']),
                'target' => in_array($button['target'], array('_self', '_blank')) ? $button['target'] : '_self',
                'visibility' => in_array($button['visibility'], array('both', 'desktop', 'mobile')) ? $button['visibility'] : 'both'
            );
        }
        
        update_option('wdm_header_utility_buttons', $sanitized_buttons);
        
        wp_send_json_success('Utility buttons saved successfully.');
    }
}