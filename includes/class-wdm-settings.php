<?php
/**
 * WDM Settings Class
 * Main settings controller that coordinates all admin functionality
 */

namespace WDM_Custom_Header;

if (!defined('ABSPATH')) {
    exit;
}

// Include modular settings classes
require_once __DIR__ . '/class-wdm-main-navigation.php';
require_once __DIR__ . '/class-wdm-utility-navigation.php';
require_once __DIR__ . '/class-wdm-general-settings.php';
require_once __DIR__ . '/class-wdm-plugin-info.php';

class WDM_Settings {

    private $main_navigation;
    private $utility_navigation;
    private $general_settings;
    private $plugin_info;

    public function __construct() {
        $this->main_navigation = new WDM_Main_Navigation();
        $this->utility_navigation = new WDM_Utility_Navigation();
        $this->general_settings = new WDM_General_Settings();
        $this->plugin_info = new WDM_Plugin_Info();

        \add_action('admin_menu', array($this, 'add_admin_menu'));
        \add_action('admin_init', array($this, 'settings_init'));
        \add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        \add_action('wp_ajax_wdm_check_update', array($this, 'ajax_check_update'));
        \add_action('wp_ajax_wdm_force_update', array($this, 'ajax_force_update'));
    }

    public function add_admin_menu() {
        \add_options_page(
            'WDM Header Settings',
            'WDM Header',
            'manage_options',
            'wdm-header-settings',
            array($this, 'settings_page')
        );
    }

    public function enqueue_admin_assets($hook_suffix) {
        if ($hook_suffix !== 'settings_page_wdm-header-settings') {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');
        wp_enqueue_style('wdm-admin-styles', WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/admin-styles.css', array(), WDM_CUSTOM_HEADER_VERSION);
        wp_enqueue_script('wdm-admin-script', WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/admin-script.js', array('jquery', 'jquery-ui-sortable'), WDM_CUSTOM_HEADER_VERSION, true);
    }

    public function settings_init() {
        register_setting('wdm_header_settings', 'wdm_header_options', array($this, 'sanitize_options'));
        register_setting('wdm_menu_settings', 'wdm_menu_items', array($this, 'sanitize_menu_items'));
    }

    public function sanitize_options($input) {
        $sanitized = array();

        $sanitized['load_css']        = isset($input['load_css']) ? '1' : '0';
        $sanitized['github_username'] = isset($input['github_username']) ? sanitize_text_field($input['github_username']) : '';
        $sanitized['github_repo']     = isset($input['github_repo']) ? sanitize_text_field($input['github_repo']) : '';
        $sanitized['auto_update']     = isset($input['auto_update']) ? '1' : '0';

        $sanitized['volunteer_text']  = isset($input['volunteer_text']) ? sanitize_text_field($input['volunteer_text']) : 'Volunteer';
        $sanitized['volunteer_url']   = isset($input['volunteer_url']) ? esc_url_raw($input['volunteer_url']) : '#volunteer';
        $sanitized['donate_text']     = isset($input['donate_text']) ? sanitize_text_field($input['donate_text']) : 'Donate';
        $sanitized['donate_url']      = isset($input['donate_url']) ? esc_url_raw($input['donate_url']) : '#donate';
        $sanitized['show_search']     = isset($input['show_search']) ? '1' : '0';

        return $sanitized;
    }

    public function sanitize_menu_items($input) {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();

        foreach ($input as $index => $item) {
            if (!is_array($item)) continue;

            $sanitized_item = array();
            $sanitized_item['text']   = isset($item['text']) ? sanitize_text_field($item['text']) : '';
            $sanitized_item['url']    = isset($item['url']) ? esc_url_raw($item['url']) : '';
            $sanitized_item['target'] = isset($item['target']) && in_array($item['target'], array('_self', '_blank')) ? $item['target'] : '_self';
            $sanitized_item['submenu'] = array();

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                foreach ($item['submenu'] as $sub_index => $sub_item) {
                    if (!is_array($sub_item)) continue;

                    $sanitized_sub = array();
                    $sanitized_sub['text'] = isset($sub_item['text']) ? sanitize_text_field($sub_item['text']) : '';
                    $sanitized_sub['url']  = isset($sub_item['url']) ? esc_url_raw($sub_item['url']) : '';
                    $sanitized_sub['target'] = isset($sub_item['target']) && in_array($sub_item['target'], array('_self', '_blank')) ? $sub_item['target'] : '_self';
                    $sanitized_sub['description'] = isset($sub_item['description']) ? sanitize_textarea_field($sub_item['description']) : '';

                    $sanitized_item['submenu'][] = $sanitized_sub;
                }
            }

            $sanitized[] = $sanitized_item;
        }

        return $sanitized;
    }

    public function ajax_check_update() {
        if (!wp_verify_nonce($_POST['nonce'], 'wdm_update_nonce') || !current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        $options = get_option('wdm_header_options', array());
        $username = isset($options['github_username']) ? $options['github_username'] : '';
        $repo     = isset($options['github_repo']) ? $options['github_repo'] : '';

        if (empty($username) || empty($repo)) {
            wp_send_json_error('GitHub credentials not configured');
        }

        $api_url = "https://api.github.com/repos/{$username}/{$repo}/releases/latest";
        $response = wp_remote_get($api_url, array(
            'timeout' => 30,
            'headers' => array('User-Agent' => 'WDM-Custom-Header-Plugin')
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
                    'download_url' => $release['zipball_url'] ?? ''
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

    public function ajax_force_update() {
        if (!wp_verify_nonce($_POST['nonce'], 'wdm_update_nonce') || !current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }

        delete_site_transient('update_plugins');
        wp_update_plugins();

        wp_send_json_success('Update check triggered successfully');
    }

    public function settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'main_menu';

        ?>
        <div class="wrap wdm-admin-wrap">
            <h1>WDM Custom Header Settings</h1>
            
            <h2 class="nav-tab-wrapper">
                <a href="?page=wdm-header-settings&tab=main_menu" class="nav-tab <?php echo $active_tab == 'main_menu' ? 'nav-tab-active' : ''; ?>">Main Navigation Menu</a>
                <a href="?page=wdm-header-settings&tab=utility_menu" class="nav-tab <?php echo $active_tab == 'utility_menu' ? 'nav-tab-active' : ''; ?>">Utility Navigation Menu</a>
                <a href="?page=wdm-header-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>">General Settings</a>
                <a href="?page=wdm-header-settings&tab=plugin_info" class="nav-tab <?php echo $active_tab == 'plugin_info' ? 'nav-tab-active' : ''; ?>">Plugin Information</a>
            </h2>
            
            <div class="wdm-admin-container">
                <?php
                switch($active_tab) {
                    case 'main_menu':
                        $this->main_navigation->render_main_navigation_content();
                        break;
                    case 'utility_menu':
                        $this->utility_navigation->render_utility_navigation_content();
                        break;
                    case 'general':
                        $this->general_settings->render_general_settings_content();
                        break;
                    case 'plugin_info':
                        $this->plugin_info->render_plugin_info_content();
                        break;
                    default:
                        $this->main_navigation->render_main_navigation_content();
                }
                ?>
            </div>
        </div>
        <?php
    }

            <div id="main-navigation" class="wdm-tab-content active">
                <div class="wdm-settings-section">
                    <h2 class="wdm-section-header">Main Navigation Menu Management</h2>
                    <div class="wdm-section-content">
                        <form method="post" id="wdm-menu-settings-form">
                            <?php wp_nonce_field('wdm_save_menu_settings', 'wdm_menu_nonce'); ?>

                            <div class="wdm-notice">
                                <strong>Instructions:</strong> Drag menu items to reorder them. Use "Add Submenu" to create dropdown menus. Empty URL fields will create dropdown-only items.
                            </div>

                            <div class="wdm-menu-items">
                                <?php $this->render_menu_items($menu_data); ?>
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

            <div id="utility-navigation" class="wdm-tab-content">
                <div class="wdm-settings-section">
                    <h2 class="wdm-section-header">Utility Navigation Menu</h2>
                    <div class="wdm-section-content">
                        <form method="post" id="wdm-utility-settings-form">
                            <?php wp_nonce_field('wdm_save_general_settings', 'wdm_general_nonce'); ?>
                            
                            <div class="wdm-notice">
                                <strong>Utility Navigation:</strong> Configure the volunteer, donate buttons and search functionality in the header utility area.
                            </div>

                            <h3>Volunteer Button</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Button Text</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[volunteer_text]" value="<?php echo esc_attr($options['volunteer_text'] ?? 'Volunteer'); ?>" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Button URL</th>
                                    <td>
                                        <input type="url" name="wdm_header_options[volunteer_url]" value="<?php echo esc_attr($options['volunteer_url'] ?? '#volunteer'); ?>" class="regular-text" />
                                    </td>
                                </tr>
                            </table>

                            <h3>Donate Button</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Button Text</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[donate_text]" value="<?php echo esc_attr($options['donate_text'] ?? 'Donate'); ?>" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Button URL</th>
                                    <td>
                                        <input type="url" name="wdm_header_options[donate_url]" value="<?php echo esc_attr($options['donate_url'] ?? '#donate'); ?>" class="regular-text" />
                                    </td>
                                </tr>
                            </table>

                            <h3>Search Functionality</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">Show Search</th>
                                    <td>
                                        <input type="checkbox" name="wdm_header_options[show_search]" value="1" <?php checked($options['show_search'] ?? '1', '1'); ?> />
                                        <label>Enable search functionality in header</label>
                                    </td>
                                </tr>
                            </table>

                            <input type="submit" name="wdm_save_general" class="button button-primary" value="Save Utility Navigation Settings" />
                        </form>
                    </div>
                </div>
            </div>

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
                                        <input type="checkbox" name="wdm_header_options[load_css]" value="1" <?php checked($options['load_css'] ?? '1', '1'); ?> />
                                        <label>Enable default header styles</label>
                                        <p class="description">Uncheck to use your own custom CSS styles.</p>
                                    </td>
                                </tr>
                            </table>



                            <h3>GitHub Auto-Update Settings</h3>
                            <table class="form-table">
                                <tr>
                                    <th scope="row">GitHub Username</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[github_username]" value="<?php echo esc_attr($options['github_username'] ?? ''); ?>" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">GitHub Repository</th>
                                    <td>
                                        <input type="text" name="wdm_header_options[github_repo]" value="<?php echo esc_attr($options['github_repo'] ?? ''); ?>" class="regular-text" />
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row">Enable Auto-Update</th>
                                    <td>
                                        <input type="checkbox" name="wdm_header_options[auto_update]" value="1" <?php checked($options['auto_update'] ?? '0', '1'); ?> />
                                    </td>
                                </tr>
                            </table>

                            <input type="submit" name="wdm_save_general" class="button button-primary" value="Save General Settings" />
                        </form>
                    </div>
                </div>
            </div>

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
                            <p><strong>Shortcode:</strong> <code>[wdm_custom_header]</code></p>
                            <p><strong>PHP Function:</strong> <code>&lt;?php wdm_display_header(); ?&gt;</code></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
        jQuery(function($) {
            $('#wdm-check-update').on('click', function() {
                var btn = $(this).prop('disabled', true).text('Checking...');
                $.post(ajaxurl, {
                    action: 'wdm_check_update',
                    nonce: '<?php echo wp_create_nonce('wdm_update_nonce'); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('Check for Updates');
                    if (res.success) {
                        let msg = res.data.update_available
                            ? '<div class="notice notice-warning"><p><strong>Update Available:</strong> ' + res.data.remote_version + '</p></div>'
                            : '<div class="notice notice-success"><p>' + res.data.message + '</p></div>';
                        $('#wdm-update-status').html(msg);
                    } else {
                        $('#wdm-update-status').html('<div class="notice notice-error"><p>' + res.data + '</p></div>');
                    }
                });
            });

            $('#wdm-force-update').on('click', function() {
                var btn = $(this).prop('disabled', true).text('Triggering...');
                $.post(ajaxurl, {
                    action: 'wdm_force_update',
                    nonce: '<?php echo wp_create_nonce('wdm_update_nonce'); ?>'
                }, function(res) {
                    btn.prop('disabled', false).text('Force Update Check');
                    let msg = res.success
                        ? '<div class="notice notice-info"><p>' + res.data + '</p></div>'
                        : '<div class="notice notice-error"><p>' + res.data + '</p></div>';
                    $('#wdm-update-status').html(msg);
                });
            });
        });
        </script>
<?php
    }

    private function render_menu_items($menu_items) {
        if (!is_array($menu_items)) return;
        foreach ($menu_items as $index => $item) {
            $text    = esc_attr($item['text'] ?? '');
            $url     = esc_attr($item['url'] ?? '');
            $target  = esc_attr($item['target'] ?? '_self');
            // Handle both stored format (submenu_items) and admin format (submenu)
            $submenu = $item['submenu'] ?? $item['submenu_items'] ?? array();
?>
            <div class="wdm-menu-item" data-index="<?php echo $index; ?>">
                <div class="wdm-menu-item-header">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-menu-item-title">Menu Item <?php echo $index + 1; ?></span>
                    <div class="wdm-menu-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-add-submenu-item">Add Submenu</button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-toggle-submenu">Show Submenu (<?php echo count($submenu); ?>)</button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-menu-item">Remove</button>
                    </div>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Menu Text</label>
                        <input type="text" name="wdm_menu_items[<?php echo $index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" />
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="text" name="wdm_menu_items[<?php echo $index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" />
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_menu_items[<?php echo $index; ?>][target]" class="wdm-form-select">
                            <option value="_self" <?php selected($target, '_self'); ?>>Same Window</option>
                            <option value="_blank" <?php selected($target, '_blank'); ?>>New Window</option>
                        </select>
                    </div>
                </div>

                <div class="wdm-submenu-items hidden">
                    <?php $this->render_submenu_items($index, $submenu); ?>
                </div>
            </div>
<?php
        }
    }

    private function render_submenu_items($menu_index, $submenu_items) {
        foreach ($submenu_items as $sub_index => $sub_item) {
            $text = esc_attr($sub_item['text'] ?? '');
            $url  = esc_attr($sub_item['url'] ?? '');
            $target = esc_attr($sub_item['target'] ?? '_self');
            $description = esc_textarea($sub_item['description'] ?? '');
?>
            <div class="wdm-submenu-item" data-submenu-index="<?php echo $sub_index; ?>">
                <div class="wdm-submenu-header">
                    <span class="wdm-submenu-title">Submenu Item <?php echo $sub_index + 1; ?></span>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-submenu-item">Remove</button>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Text</label>
                        <input type="text" name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" />
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="text" name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" />
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
                        <textarea name="wdm_menu_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][description]" class="wdm-form-input wdm-form-textarea"><?php echo $description; ?></textarea>
                    </div>
                </div>
            </div>
<?php
        }
    }
}

