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

    public function render_general_settings_content() {
        $options = get_option('wdm_header_options', array());
        
        if (isset($_POST['submit_general']) && wp_verify_nonce($_POST['wdm_general_nonce'], 'wdm_save_general')) {
            $options = $this->process_general_submission();
            echo '<div class="notice notice-success is-dismissible"><p>General settings saved successfully!</p></div>';
        }
?>
        <form method="post" action="" id="wdm-general-settings-form">
            <?php wp_nonce_field('wdm_save_general', 'wdm_general_nonce'); ?>
            
            <div class="wdm-form-section">
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
                        <input type="number" name="wdm_header_options[scroll_trigger]" value="<?php echo esc_attr($options['scroll_trigger'] ?? '100'); ?>" class="wdm-form-input" min="0" max="500" />
                        <p class="description">Scroll distance before header behavior changes</p>
                    </div>
                </div>
            </div>

            <div class="wdm-form-section">
                <h3>Brand Settings</h3>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Organization Name</label>
                        <input type="text" name="wdm_header_options[org_name]" value="<?php echo esc_attr($options['org_name'] ?? 'Greybull Rescue'); ?>" class="wdm-form-input" />
                        <p class="description">Display name for your organization</p>
                    </div>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Logo URL</label>
                        <input type="url" name="wdm_header_options[logo_url]" value="<?php echo esc_attr($options['logo_url'] ?? ''); ?>" class="wdm-form-input" />
                        <p class="description">URL to your organization's logo image</p>
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

            <div class="wdm-form-section">
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

            <div class="wdm-form-actions">
                <input type="submit" name="submit_general" class="wdm-btn wdm-btn-primary" value="Save General Settings" />
            </div>
        </form>
<?php
    }

    private function process_general_submission() {
        $options = array();
        
        if (isset($_POST['wdm_header_options'])) {
            $input = $_POST['wdm_header_options'];
            
            // Checkbox values
            $options['enable_sticky'] = isset($input['enable_sticky']) ? 1 : 0;
            $options['enable_mobile_menu'] = isset($input['enable_mobile_menu']) ? 1 : 0;
            $options['load_css'] = isset($input['load_css']) ? 1 : 0;
            $options['load_js'] = isset($input['load_js']) ? 1 : 0;
            
            // Numeric values
            $options['mobile_breakpoint'] = absint($input['mobile_breakpoint'] ?? 768);
            $options['scroll_trigger'] = absint($input['scroll_trigger'] ?? 100);
            
            // Text values
            $options['org_name'] = sanitize_text_field($input['org_name'] ?? 'Greybull Rescue');
            $options['logo_url'] = esc_url_raw($input['logo_url'] ?? '');
            $options['home_url'] = esc_url_raw($input['home_url'] ?? '/');
            $options['custom_css'] = wp_strip_all_tags($input['custom_css'] ?? '');
        }
        
        update_option('wdm_header_options', $options);
        return $options;
    }
}