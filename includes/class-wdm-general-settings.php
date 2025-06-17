<?php
/**
 * WDM General Settings
 * Handles general plugin configuration and settings
 */

namespace WDM_Custom_Header;

if (!defined('ABSPATH')) {
    exit;
}

class WDM_General_Settings {

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function enqueue_assets($hook) {
        // Load only on the "General Settings" tab of your plugin
        if (
            $hook !== 'toplevel_page_wdm-header-settings' ||
            !isset($_GET['tab']) || $_GET['tab'] !== 'general'
        ) {
            return;
        }
    
        // ✅ Load WordPress media uploader scripts
        wp_enqueue_media();
    
        // ✅ Load custom styles for the General Settings page
        wp_enqueue_style(
            'wdm-general-settings-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/general-settings.css',
            array(),
            '1.0'
        );
    
        // ✅ Load custom JS with jQuery dependency
        wp_enqueue_script(
            'wdm-general-settings-js',
            WDM_CUSTOM_HEADER_PLUGIN_URL . 'assets/js/general-settings.js',
            array('jquery'),
            WDM_CUSTOM_HEADER_VERSION,
            true
        );
    }    

    public function render_general_settings_content() {
        $options = get_option('wdm_header_options', array());
        
        if (isset($_POST['submit_general']) && wp_verify_nonce($_POST['wdm_general_nonce'], 'wdm_save_general')) {
            $options = $this->process_general_submission();
            echo '<div class="wdm-notice notice notice-success is-dismissible"><p>General settings saved successfully!</p></div>';
        }
?>
        <form method="post" action="" id="wdm-general-settings-form">
            <?php wp_nonce_field('wdm_save_general', 'wdm_general_nonce'); ?>
            
            <div class="wdm-section-header">
                <h3>General Settings</h3>
                    <div class="wdm-section-actions">
                        <button type="submit" name="submit_general" class="wdm-btn wdm-btn-primary">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Save General Settings
                        </button>
                    </div>
            </div>
            <div class="general-settings-container">
                <div class="wdm-form-section general-settings-column">
                    <h3>Header Display Settings</h3>
                    
                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">
                                <input type="checkbox" name="wdm_header_options[enable_sticky]" value="1" <?php checked(isset($options['enable_sticky']) ? $options['enable_sticky'] : 0, 1); ?> />
                                Enable sticky header behavior
                            </label>
                            <p class="description">Makes the header stick to the top when scrolling</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">
                                <input type="checkbox" name="wdm_header_options[enable_mobile_menu]" value="1" <?php checked(isset($options['enable_mobile_menu']) ? $options['enable_mobile_menu'] : 1, 1); ?> />
                                Enable mobile hamburger menu
                            </label>
                            <p class="description">Shows hamburger menu on mobile devices</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Mobile Breakpoint (px)</label>
                            <input type="number" name="wdm_header_options[mobile_breakpoint]" value="<?php echo esc_attr($options['mobile_breakpoint'] ?? '768'); ?>" class="wdm-form-input" min="320" max="1200" />
                            <p class="description">Screen width below which mobile menu appears</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Scroll Trigger Distance (px)</label>
                            <input type="number" name="wdm_header_options[scroll_trigger]" value="<?php echo esc_attr($options['scroll_trigger'] ?? '400'); ?>" class="wdm-form-input" min="0" max="1000" />
                            <p class="description">Scroll distance before header behavior changes</p>
                        </div>
                    </div>
                </div>

                <div class="wdm-form-section general-settings-column">
                    <h3>Brand Settings</h3>
                    
                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Website Name</label>
                            <input type="text" name="wdm_header_options[org_name]" value="<?php echo esc_attr($options['org_name'] ?? 'Greybull Rescue'); ?>" class="wdm-form-input" />
                            <p class="description">Display name for your organization</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Logo URL</label>
                            <div class="logo-upload-btn-container">
                                <input type="text" id="logo_url" name="wdm_header_options[logo_url]" value="<?php echo esc_attr($options['logo_url'] ?? ''); ?>" class="wdm-form-input" />
                                <button type="button" class="button wdm-media-picker" data-target="#logo_url">Select Image</button>
                            </div>
                            <p class="description">Select or upload your organization's logo image</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Home URL</label>
                            <input type="url" name="wdm_header_options[home_url]" value="<?php echo esc_attr($options['home_url'] ?? '/'); ?>" class="wdm-form-input" />
                            <p class="description">URL for the main logo/brand link</p>
                        </div>
                    </div>
                </div>

                <div class="wdm-form-section general-settings-column">
                    <h3>Emergency Alert Banner</h3>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">
                                <!-- Always send a value, even if unchecked -->
                                <input type="hidden" name="wdm_header_options[enable_emergency]" value="0" />
                                <input type="checkbox" name="wdm_header_options[enable_emergency]" value="1"
                                    <?php checked(isset($options['enable_emergency']) ? $options['enable_emergency'] : 0, 1); ?> />
                                Enable Emergency Alert Banner
                            </label>
                            <p class="description">Display an emergency alert banner at the top of your site</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Alert Text</label>
                            <input type="text" name="wdm_header_options[emergency_text]" value="<?php echo esc_attr($options['emergency_text'] ?? ''); ?>" class="wdm-form-input" />
                            <p class="description">Main message to display in the alert banner</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Button Text</label>
                            <input type="text" name="wdm_header_options[emergency_button_text]" value="<?php echo esc_attr($options['emergency_button_text'] ?? ''); ?>" class="wdm-form-input" />
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Button URL</label>
                            <input type="url" name="wdm_header_options[emergency_button_url]" value="<?php echo esc_url($options['emergency_button_url'] ?? ''); ?>" class="wdm-form-input" />
                        </div>
                    </div>
                </div>
                <div class="wdm-form-section general-settings-column">
                    <h3>Utility Buttons (Max 3)</h3>

                    <div id="utility-buttons-wrapper">
                        <?php
                        $utility_buttons = $options['utility_buttons'] ?? [];
                        foreach ($utility_buttons as $index => $btn) : ?>
                            <div class="utility-button-row">
                                <label class="wdm-form-label">Button Label</label>
                                <input type="text" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][label]" value="<?php echo esc_attr($btn['label']); ?>" placeholder="Button Label" class="wdm-form-input" />

                                <label class="wdm-form-label">Button URL</label>
                                <input type="url" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][url]" value="<?php echo esc_url($btn['url']); ?>" placeholder="https://example.com" class="wdm-form-input" />

                                <label class="wdm-form-label">Button Color</label>
                                <input type="color" name="wdm_header_options[utility_buttons][<?php echo $index; ?>][color]" value="<?php echo esc_attr($btn['color'] ?? '#d13a30'); ?>" class="wdm-color-input" />

                                <button type="button" class="remove-utility-button button">Remove</button>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <button type="button" class="button button-primary" id="add-utility-button">Add Button</button>
                    <p class="description">You can add up to 3 utility buttons.</p>
                </div>

                <div class="wdm-form-section general-settings-column">
                    <h3>Advanced Settings</h3>
                    
                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">
                                <input type="checkbox" name="wdm_header_options[load_css]" value="1" <?php checked(isset($options['load_css']) ? $options['load_css'] : 1, 1); ?> />
                                Load default CSS styles
                            </label>
                            <p class="description">Uncheck if you want to use custom CSS only</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">
                                <input type="checkbox" name="wdm_header_options[load_js]" value="1" <?php checked(isset($options['load_js']) ? $options['load_js'] : 1, 1); ?> />
                                Load default JavaScript
                            </label>
                            <p class="description">Uncheck if you want to use custom JavaScript only</p>
                        </div>
                    </div>

                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Custom CSS</label>
                            <textarea name="wdm_header_options[custom_css]" rows="10" class="wdm-form-textarea"><?php echo esc_textarea($options['custom_css'] ?? ''); ?></textarea>
                            <p class="description">Additional CSS rules for header customization</p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
<?php
    }

    private function process_general_submission() {
        $existing = get_option('wdm_header_options', []);
        $options = $existing; // preserve old values unless overwritten
    
        if (isset($_POST['wdm_header_options'])) {
            $input = $_POST['wdm_header_options']; // 🔄 move this to the top
    
            // Utility Buttons - must go AFTER $input is defined
            $options['utility_buttons'] = [];

            if (!empty($input['utility_buttons']) && is_array($input['utility_buttons'])) {
                foreach ($input['utility_buttons'] as $btn) {
                    $label = isset($btn['label']) ? sanitize_text_field($btn['label']) : '';
                    $url   = isset($btn['url']) ? esc_url_raw($btn['url']) : '';
                    $color = isset($btn['color']) && function_exists('sanitize_hex_color') ? sanitize_hex_color($btn['color']) : '#d13a30';
            
                    if ($label && $url && count($options['utility_buttons']) < 3) {
                        $options['utility_buttons'][] = [
                            'label' => $label,
                            'url'   => $url,
                            'color' => $color,
                        ];
                    }
                }
            }            
    
            // Checkbox values
            $options['enable_sticky'] = isset($input['enable_sticky']) ? 1 : 0;
            $options['enable_mobile_menu'] = isset($input['enable_mobile_menu']) ? 1 : 0;
            $options['load_css'] = isset($input['load_css']) ? 1 : 0;
            $options['load_js'] = isset($input['load_js']) ? 1 : 0;
            $options['enable_emergency'] = isset($input['enable_emergency']) && $input['enable_emergency'] === '1' ? '1' : '0';
    
            // Numeric values
            $options['mobile_breakpoint'] = absint($input['mobile_breakpoint'] ?? 768);
            $options['scroll_trigger'] = absint($input['scroll_trigger'] ?? 400);
    
            // Text values
            $options['org_name'] = sanitize_text_field($input['org_name'] ?? 'Greybull Rescue');
            $options['logo_url'] = esc_url_raw($input['logo_url'] ?? '');
            $options['home_url'] = esc_url_raw($input['home_url'] ?? '/');
            $options['custom_css'] = wp_strip_all_tags($input['custom_css'] ?? '');
    
            // Emergency Banner Texts
            $options['emergency_text'] = sanitize_text_field($input['emergency_text'] ?? '');
            $options['emergency_button_text'] = sanitize_text_field($input['emergency_button_text'] ?? '');
            $options['emergency_button_url'] = esc_url_raw($input['emergency_button_url'] ?? '');
        }
    
        update_option('wdm_header_options', $options);
        return $options;
    }
      
}

echo '<pre>';
print_r(get_option('wdm_header_options'));
echo '</pre>';
