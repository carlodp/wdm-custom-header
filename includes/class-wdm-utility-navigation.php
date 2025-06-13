<?php
/**
 * WDM Utility Navigation Settings
 * Handles utility navigation menu management functionality
 */

namespace WDM_Custom_Header;

if (!defined('ABSPATH')) {
    exit;
}

class WDM_Utility_Navigation {

    public function render_utility_navigation_content() {
        $utility_menu = get_option('wdm_utility_menu', array());
        
        if (isset($_POST['submit_utility']) && wp_verify_nonce($_POST['wdm_utility_nonce'], 'wdm_save_utility')) {
            $utility_menu = $this->process_utility_submission();
            echo '<div class="notice notice-success is-dismissible"><p>Utility navigation menu saved successfully!</p></div>';
        }
?>
        <form method="post" action="" id="wdm-utility-settings-form">
            <?php wp_nonce_field('wdm_save_utility', 'wdm_utility_nonce'); ?>
            
            <div class="wdm-form-section">
                <div class="wdm-section-header">
                    <h3>Utility Navigation Menu Items</h3>
                    <div class="wdm-section-actions">
                        <button type="button" class="wdm-btn wdm-btn-secondary wdm-add-utility-item">Add Utility Item</button>
                    </div>
                </div>

                <div class="wdm-utility-items">
                    <?php $this->render_utility_items($utility_menu); ?>
                </div>
            </div>

            <div class="wdm-form-actions">
                <input type="submit" name="submit_utility" class="wdm-btn wdm-btn-primary" value="Save Utility Navigation Menu" />
            </div>
        </form>
<?php
    }

    private function process_utility_submission() {
        $utility_items = array();
        
        if (isset($_POST['wdm_utility_items']) && is_array($_POST['wdm_utility_items'])) {
            foreach ($_POST['wdm_utility_items'] as $index => $item) {
                if (!empty($item['text'])) {
                    $utility_items[] = array(
                        'text'   => sanitize_text_field($item['text']),
                        'url'    => esc_url_raw($item['url']),
                        'target' => sanitize_text_field($item['target'])
                    );
                }
            }
        }
        
        update_option('wdm_utility_menu', $utility_items);
        return $utility_items;
    }

    private function render_utility_items($utility_items) {
        if (!is_array($utility_items)) return;
        
        foreach ($utility_items as $index => $item) {
            $text   = esc_attr($item['text'] ?? '');
            $url    = esc_attr($item['url'] ?? '');
            $target = esc_attr($item['target'] ?? '_self');
?>
            <div class="wdm-utility-item" data-index="<?php echo $index; ?>">
                <div class="wdm-utility-item-header">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-utility-item-title">Utility Item <?php echo $index + 1; ?></span>
                    <div class="wdm-utility-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-utility-item">Remove</button>
                    </div>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Text</label>
                        <input type="text" name="wdm_utility_items[<?php echo $index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" />
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="text" name="wdm_utility_items[<?php echo $index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" />
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_utility_items[<?php echo $index; ?>][target]" class="wdm-form-select">
                            <option value="_self" <?php selected($target, '_self'); ?>>Same Window</option>
                            <option value="_blank" <?php selected($target, '_blank'); ?>>New Window</option>
                        </select>
                    </div>
                </div>
            </div>
<?php
        }
    }
}