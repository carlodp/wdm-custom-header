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

    public function __construct() {
        add_action('admin_enqueue_scripts', [$this, 'enqueue_assets']);
    }
    
    public function enqueue_assets($hook) {
        if (
            $hook !== 'toplevel_page_wdm-header-settings' || 
            !isset($_GET['tab']) || $_GET['tab'] !== 'utility_menu'
        ) {
            return;
        }
    
        // Required styles

    
        wp_enqueue_style(
            'iconpicker-css',
            'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/css/fontawesome-iconpicker.min.css',
            array(),
            '3.2.0'
        );
    
        wp_enqueue_style(
            'wdm-utility-nav-style',
            plugin_dir_url(dirname(__FILE__)) . 'assets/css/utility-nav-style.css',
            array(),
            '1.0'
        );
    
        // Required scripts
        wp_enqueue_script('jquery-ui-sortable');
    
        wp_enqueue_script(
            'iconpicker-js',
            'https://cdnjs.cloudflare.com/ajax/libs/fontawesome-iconpicker/3.2.0/js/fontawesome-iconpicker.min.js',
            array('jquery'),
            '3.2.0',
            true
        );
    
        wp_enqueue_script(
            'wdm-utility-nav-script',
            plugin_dir_url(dirname(__FILE__)) . 'assets/js/utility-script.js',
            array('jquery', 'jquery-ui-sortable', 'iconpicker-js'),
            '1.0',
            true
        );
    }
    
    public function render_utility_navigation_content() {
        $utility_menu = get_option('wdm_utility_menu', array());

        if (isset($_POST['submit_utility']) && wp_verify_nonce($_POST['wdm_utility_nonce'], 'wdm_save_utility')) {
            $utility_menu = $this->process_utility_submission();
            echo '<div class="wdm-notice notice notice-success is-dismissible"><p>Utility navigation menu saved successfully!</p></div>';
        }
?>
        <form method="post" action="" id="wdm-utility-settings-form">
            <?php wp_nonce_field('wdm_save_utility', 'wdm_utility_nonce'); ?>
            
            <div class="wdm-form-section">
                <div class="wdm-section-header">
                    <h3>Utility Navigation Menu Items</h3>
                    <div class="wdm-section-actions">
                        <button type="button" class="wdm-btn wdm-btn-secondary wdm-add-utility-item"><i class="fas fa-plus"></i> Add Utility Item</button>
                        <button type="submit" name="submit_utility" class="wdm-btn wdm-btn-primary wdm-submit-btn">
                            <i class="fas fa-save" style="margin-right: 6px;"></i> Save Utility Navigation Menu
                        </button>                    
                    </div>
                </div>

                <div class="wdm-utility-items">
                    <?php $this->render_utility_items($utility_menu); ?>
                </div>
            </div>
        </form>
<?php
    }

    private function process_utility_submission() {
        $utility_items = array();

        if (isset($_POST['wdm_utility_items']) && is_array($_POST['wdm_utility_items'])) {
            foreach ($_POST['wdm_utility_items'] as $index => $item) {
                if (!empty($item['text'])) {
                    $utility_item = array(
                        'text'   => sanitize_text_field($item['text']),
                        'url'    => esc_url_raw($item['url']),
                        'target' => sanitize_text_field($item['target']),
                        'icon'   => sanitize_text_field($item['icon'] ?? ''),
                    );

                    $utility_items[$index] = $utility_item;
                }
            }
        }

        update_option('wdm_utility_menu', $utility_items);
        return $utility_items;
    }

    private function render_utility_items($utility_items) {
        if (!is_array($utility_items)) return;

        foreach ($utility_items as $index => $item) {
            $text      = esc_attr($item['text'] ?? '');
            $url       = esc_attr($item['url'] ?? '');
            $target    = esc_attr($item['target'] ?? '_self');
            $icon      = esc_attr($item['icon'] ?? 'none');
?>
            <div class="wdm-utility-item" data-index="<?php echo $index; ?>">
                <div class="wdm-utility-item-header">
                    <div class="drag-name-container">
                        <span class="wdm-drag-handle">⋮⋮</span>
                        <span class="wdm-utility-item-title">Utility Item <?php echo $index + 1; ?></span>
                    </div>
                    <div class="wdm-utility-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-utility-item"><i class="fas fa-trash-alt"></i> Remove</button>
                    </div>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Menu Text</label>
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
                    <div class="wdm-form-col icon-container">  
                        <!-- Icon Preview -->
                        <div class="wdm-icon-preview" style="font-size: 20px;">
                            <?php if (!empty($item['icon'])) : ?>
                            <i class="<?php echo esc_attr($item['icon']); ?>"></i>
                            <?php endif; ?>
                        </div>

                        <!-- Trigger Button -->
                        <button type="button" class="wdm-btn wdm-icon-picker-trigger" aria-label="Select icon">
                            <i class="fas fa-icons"></i> Choose Icon
                        </button>

                        <!-- Hidden Input for Icon Value -->
                        <input type="text"
                                name="wdm_utility_items[<?php echo $index; ?>][icon]"
                                value="<?php echo esc_attr($item['icon'] ?? ''); ?>"
                                class="wdm-icon-input"
                                data-icon="<?php echo esc_attr($item['icon'] ?? ''); ?>"
                                role="iconpicker"
                                style="display:none;" />
                    </div>


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
            $description = esc_textarea(stripslashes($sub_item['description'] ?? ''));
?>
            <div class="wdm-submenu-item" data-submenu-index="<?php echo $sub_index; ?>">
                <div class="wdm-submenu-item-header">
                    <span class="wdm-submenu-title">
                        <?php echo ($sub_index === 0) ? 'Main Submenu' : 'Submenu Item ' . ($sub_index + 1); ?>
                    </span>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-submenu-item">Remove</button>
                </div>

                <div class="submenu-fields">
                    <div class="wdm-form-row">
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">Submenu Text</label>
                            <input type="text" name="wdm_utility_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][text]" value="<?php echo $text; ?>" class="wdm-form-input" />
                        </div>
                        <div class="wdm-form-col">
                            <label class="wdm-form-label">URL</label>
                            <input type="text" name="wdm_utility_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][url]" value="<?php echo $url; ?>" class="wdm-form-input" />
                        </div>
                        <div class="wdm-form-col-narrow">
                            <label class="wdm-form-label">Target</label>
                            <select name="wdm_utility_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][target]" class="wdm-form-select">
                                <option value="_self" <?php selected($target, '_self'); ?>>Same Window</option>
                                <option value="_blank" <?php selected($target, '_blank'); ?>>New Window</option>
                            </select>
                        </div>
                    </div>

                    <?php if ($sub_index === 0): ?>
                        <div class="wdm-form-row">
                            <div class="wdm-form-col">
                                <label class="wdm-form-label">Description</label>
                                <textarea name="wdm_utility_items[<?php echo $menu_index; ?>][submenu][<?php echo $sub_index; ?>][description]" class="wdm-form-input wdm-form-textarea"><?php echo $description; ?></textarea>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
<?php
        }
    }
}
?>

<?php

/*
echo '<pre style="    width: 60%;
    margin-left: 300px;">';
print_r(get_option('wdm_utility_menu'));
echo '</pre>';
*/

?>