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
        \add_action('admin_menu', array($this, 'set_active_submenu_highlight'));
        \add_action('admin_init', array($this, 'settings_init'));
        \add_action('admin_head', array($this, 'hide_elementor_notices_on_settings_page'));
        \add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_assets'));
        \add_action('wp_ajax_wdm_check_update', array($this, 'ajax_check_update'));
        \add_action('wp_ajax_wdm_force_update', array($this, 'ajax_force_update'));
        \add_action('admin_footer', array($this, 'hide_default_footer_on_plugin_pages'));
        
    }

    public function add_admin_menu() {
        // Top-level menu
        add_menu_page(
            'Header Matrix',
            'Header Matrix',
            'manage_options',
            'wdm-header-settings',
            [$this, 'settings_page'],
            'dashicons-screenoptions',
            61
        );
    
        // Submenus with working tab links
        add_submenu_page(
            'wdm-header-settings',
            'Main Navigation',
            'Main Navigation',
            'manage_options',
            'wdm-header-settings&tab=main_menu',
            [$this, 'settings_page']
        );
    
        add_submenu_page(
            'wdm-header-settings',
            'Utility Navigation',
            'Utility Navigation',
            'manage_options',
            'wdm-header-settings&tab=utility_menu',
            [$this, 'settings_page']
        );
    
        add_submenu_page(
            'wdm-header-settings',
            'General Settings',
            'General Settings',
            'manage_options',
            'wdm-header-settings&tab=general',
            [$this, 'settings_page']
        );
    
        add_submenu_page(
            'wdm-header-settings',
            'Plugin Info',
            'Plugin Info',
            'manage_options',
            'wdm-header-settings&tab=plugin_info',
            [$this, 'settings_page']
        );

        global $submenu;
        if (isset($submenu['wdm-header-settings'])) {
            foreach ($submenu['wdm-header-settings'] as $index => $item) {
                if (isset($item[2]) && $item[2] === 'wdm-header-settings') {
                    unset($submenu['wdm-header-settings'][$index]);
                    break;
                }
            }
        }
    }    

    public function set_active_submenu_highlight() {
        global $submenu_file, $parent_file;
    
        if (
            isset($_GET['page']) &&
            $_GET['page'] === 'wdm-header-settings'
        ) {
            $parent_file = 'wdm-header-settings';
    
            $tab = $_GET['tab'] ?? 'main_menu';
    
            switch ($tab) {
                case 'utility_menu':
                    $submenu_file = 'wdm-header-settings&tab=utility_menu';
                    break;
                case 'general':
                    $submenu_file = 'wdm-header-settings&tab=general';
                    break;
                case 'plugin_info':
                    $submenu_file = 'wdm-header-settings&tab=plugin_info';
                    break;
                case 'main_menu':
                default:
                    $submenu_file = 'wdm-header-settings&tab=main_menu';
                    break;
            }
        }
    }    

    public function hide_default_footer_on_plugin_pages() {
        $screen = get_current_screen();
        if ($screen && $screen->id === 'toplevel_page_wdm-header-settings') {
            echo '<style>#wpfooter { display: none !important; }</style>';
        }
    }

    public function render_custom_footer() {
        echo '<div class="wdm-admin-footer">
            <p>Designed and Developed by <a href="https://wdmorgan.com" target="_blank" style="color: #666; text-decoration: underline;">WD Morgan Solutions</a></p>
            <span style="font-size: 11px;">Plugin Version: ' . esc_html(WDM_CUSTOM_HEADER_VERSION) . '</span></p>
        </div>';
    }

    public function enqueue_admin_assets($hook_suffix) {
        if ($hook_suffix !== 'toplevel_page_wdm-header-settings') {
            return;
        }

        wp_enqueue_script('jquery-ui-sortable');

        // Enqueue Google Fonts
        wp_enqueue_style(
            'poppins-google-font',
            'https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap',
            array(),
            null
        );

        wp_enqueue_style(
            'fontawesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css',
            array(),
            '6.7.2'
        );        

        wp_enqueue_style('wdm-admin-styles', WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/css/admin-styles.css', array(), WDM_CUSTOM_HEADER_VERSION);
        wp_enqueue_script('wdm-admin-script', WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/main-navigation-script.js', array('jquery', 'jquery-ui-sortable'), WDM_CUSTOM_HEADER_VERSION, true);
    }

    public function settings_init() {
        register_setting('wdm_header_settings', 'wdm_header_options', array($this, 'sanitize_options'));
        register_setting('wdm_menu_settings', 'wdm_menu_items', array($this, 'sanitize_menu_items'));
        register_setting('wdm_utility_settings', 'wdm_utility_menu', array($this, 'sanitize_utility_items'));
    }

    public function sanitize_options($input) {
        $sanitized = array();

        $sanitized['enable_sticky'] = isset($input['enable_sticky']) ? 1 : 0;
        $sanitized['enable_mobile_menu'] = isset($input['enable_mobile_menu']) ? 1 : 0;
        $sanitized['load_css'] = isset($input['load_css']) ? 1 : 0;
        $sanitized['load_js'] = isset($input['load_js']) ? 1 : 0;
        
        $sanitized['mobile_breakpoint'] = isset($input['mobile_breakpoint']) ? absint($input['mobile_breakpoint']) : 768;
        $sanitized['scroll_trigger'] = isset($input['scroll_trigger']) ? absint($input['scroll_trigger']) : 100;
        
        $sanitized['org_name'] = isset($input['org_name']) ? sanitize_text_field($input['org_name']) : 'Greybull Rescue';
        $sanitized['logo_url'] = isset($input['logo_url']) ? esc_url_raw($input['logo_url']) : '';
        $sanitized['home_url'] = isset($input['home_url']) ? esc_url_raw($input['home_url']) : '/';
        $sanitized['custom_css'] = isset($input['custom_css']) ? wp_strip_all_tags($input['custom_css']) : '';

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
            $sanitized_item['mega_menu'] = isset($item['mega_menu']) && $item['mega_menu'] === '1' ? '1' : '0';

            
            $sanitized_item['submenu'] = array();

            if (isset($item['submenu']) && is_array($item['submenu'])) {
                foreach ($item['submenu'] as $sub_index => $sub_item) {
                    if (!is_array($sub_item)) continue;
                
                    $sanitized_sub = array();
                    $sanitized_sub['text'] = isset($sub_item['text']) ? sanitize_text_field($sub_item['text']) : '';
                    $sanitized_sub['url']  = isset($sub_item['url']) ? esc_url_raw($sub_item['url']) : '';
                    $sanitized_sub['target'] = isset($sub_item['target']) && in_array($sub_item['target'], array('_self', '_blank')) ? $sub_item['target'] : '_self';
                
                    if ($sub_index === 0 && isset($sub_item['description'])) {
                        $sanitized_sub['description'] = wp_kses_post($sub_item['description']);
                    }
                
                    $sanitized_item['submenu'][] = $sanitized_sub;
                }
                
            }

            $sanitized[] = $sanitized_item;
        }

        return $sanitized;
    }

    public function sanitize_utility_items($input) {
        if (!is_array($input)) {
            return array();
        }

        $sanitized = array();

        foreach ($input as $index => $item) {
            if (!is_array($item)) continue;

            $sanitized[] = array(
                'text'   => isset($item['text']) ? sanitize_text_field($item['text']) : '',
                'url'    => isset($item['url']) ? esc_url_raw($item['url']) : '',
                'target' => isset($item['target']) && in_array($item['target'], array('_self', '_blank')) ? $item['target'] : '_self',
                'icon'   => isset($item['icon']) ? sanitize_text_field($item['icon']) : '',
            );
        }

        return $sanitized;
    }

    public function hide_elementor_notices_on_settings_page() {
        $screen = get_current_screen();
    
        if ($screen && $screen->id === 'toplevel_page_wdm-header-settings') {
            echo '<style>
                .e-notice,
                .e-notice--dismissible,
                .e-notice--extended,
                .elementor-message,
                .elementor-message-success {
                    display: none !important;
                }
            </style>';
        }
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

        if (!empty($release['tag_name'])) {
            $remote_version = ltrim($release['tag_name'], 'v');
            $local_version = WDM_CUSTOM_HEADER_VERSION;
            
            $update_available = version_compare($remote_version, $local_version, '>');
            
            wp_send_json_success(array(
                'update_available' => $update_available,
                'remote_version' => $remote_version,
                'local_version' => $local_version,
                'message' => $update_available ? 'Update available!' : 'Plugin is up to date',
                'log' => 'Last checked: ' . current_time('mysql')
            ));
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
    
        // Set correct submenu file for highlighting
        switch ($active_tab) {
            case 'utility_menu':
                $submenu_file = 'wdm-header-settings&tab=utility_menu';
                break;
            case 'general':
                $submenu_file = 'wdm-header-settings&tab=general';
                break;
            case 'plugin_info':
                $submenu_file = 'wdm-header-settings&tab=plugin_info';
                break;
            case 'main_menu':
            default:
                $submenu_file = 'wdm-header-settings&tab=main_menu';
                break;
        }
        ?>
        <div class="wrap wdm-admin-wrap">
            <div class="sticky-wrapper">
                <div class="sticky-title">
                    <h1>Header Matrix</h1>
                    <span>by <a href="https://wdmorgan.com/" target="_blank">WD Morgan Solutions</a></span>
                </div>
                
                <h2 class="nav-tab-wrapper">
                    <a href="?page=wdm-header-settings&tab=main_menu" class="nav-tab <?php echo $active_tab == 'main_menu' ? 'nav-tab-active' : ''; ?>"><i class="fas fa-sitemap"></i> Main Navigation Menu</a>
                    <a href="?page=wdm-header-settings&tab=utility_menu" class="nav-tab <?php echo $active_tab == 'utility_menu' ? 'nav-tab-active' : ''; ?>"><i class="fas fa-tools"></i> Utility Navigation Menu</a>
                    <a href="?page=wdm-header-settings&tab=general" class="nav-tab <?php echo $active_tab == 'general' ? 'nav-tab-active' : ''; ?>"><i class="fas fa-cogs"></i> General Settings</a>
                    <a href="?page=wdm-header-settings&tab=plugin_info" class="nav-tab <?php echo $active_tab == 'plugin_info' ? 'nav-tab-active' : ''; ?>"><i class="fas fa-info-circle"></i> Plugin Information</a>
                </h2>
            
            </div>
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
        $this->render_custom_footer();
    }
    
}

