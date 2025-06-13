<?php
/**
 * WDM Main Navigation Settings
 * Handles main navigation menu management functionality
 */

namespace WDM_Custom_Header;

if (!defined('ABSPATH')) {
    exit;
}

class WDM_Main_Navigation {

    public function render_main_navigation_content() {
        $menu_items = get_option('wdm_menu_items', array());
        error_log('Rendering menu items: ' . print_r($menu_items, true));

        if (isset($_POST['submit'])) {
            error_log('Submit key is present.');
        }

        if (isset($_POST['wdm_menu_nonce']) && wp_verify_nonce($_POST['wdm_menu_nonce'], 'wdm_save_menu')) {
            error_log('Nonce verified, processing form.');
            $menu_items = $this->process_menu_submission();
            echo '<div class="notice notice-success is-dismissible"><p>Main navigation menu saved successfully!</p></div>';
        } else {
            error_log('Nonce check failed or not present.');
        }
?>
        <form method="post" action="" id="wdm-menu-settings-form">
            <?php wp_nonce_field('wdm_save_menu', 'wdm_menu_nonce'); ?>
            
            <div class="wdm-form-section">
                <div class="wdm-section-header">
                    <h3>Main Navigation Menu Items</h3>
                    <div class="wdm-section-actions">
                        <button type="button" class="wdm-btn wdm-btn-secondary wdm-add-menu-item">Add Menu Item</button>
                        <button type="button" class="wdm-btn wdm-btn-secondary wdm-preview-header">Preview Menu</button>
                    </div>
                </div>

                <div class="wdm-menu-items">
                    <?php $this->render_menu_items($menu_items); ?>
                </div>

                <div class="wdm-preview-section hidden">
                    <h4>Menu Preview</h4>
                    <div class="wdm-preview-content"></div>
                </div>
            </div>

            <div class="wdm-form-actions">
                <input type="submit" name="submit" class="wdm-btn wdm-btn-primary" value="Save Main Navigation Menu" />
            </div>
        </form>
<?php
    }

    private function process_menu_submission() {
        $menu_items = array();
        
        if (isset($_POST['wdm_menu_items']) && is_array($_POST['wdm_menu_items'])) {
            foreach ($_POST['wdm_menu_items'] as $index => $item) {
                if (!empty($item['text'])) {
                    $menu_item = array(
                        'text'       => sanitize_text_field($item['text']),
                        'url'        => esc_url_raw($item['url']),
                        'target'     => sanitize_text_field($item['target']),
                        'mega_menu'  => isset($item['mega_menu']) && $item['mega_menu'] === '1' ? '1' : '0'
                    );

                    $submenu_items123 = array();
                    if (!empty($item['submenu']) && is_array($item['submenu'])) {
                        foreach ($item['submenu'] as $sub_index => $sub_item) {
                            if (!empty($sub_item['text'])) {
                                $submenu_items123[$sub_index] = array(
                                    'text'        => sanitize_text_field($sub_item['text']),
                                    'url'         => esc_url_raw($sub_item['url']),
                                    'target'      => sanitize_text_field($sub_item['target']),
                                    'description' => sanitize_text_field($sub_item['description'] ?? '')
                                );
                            }
                        }
                    }

                    $menu_item['submenu'] = $submenu_items123; // Always set submenu
                    $menu_items[$index] = $menu_item;
                }
            }
        }

        update_option('wdm_menu_items', $menu_items);
        error_log('Menu items updated: ' . print_r($menu_items, true));
        return $menu_items;
    }

    private function render_menu_items($menu_items) {
        if (!is_array($menu_items)) return;
        foreach ($menu_items as $index => $item) {
            $text       = esc_attr($item['text'] ?? '');
            $url        = esc_attr($item['url'] ?? '');
            $target     = esc_attr($item['target'] ?? '_self');
            $submenu = isset($item['submenu']) && is_array($item['submenu']) ? $item['submenu'] : [];
            $mega_menu  = !empty($item['mega_menu']) ? (bool) $item['mega_menu'] : false;
?>
            <div class="wdm-menu-item" data-index="<?php echo $index; ?>">
                <div class="wdm-menu-item-header">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-menu-item-title">Menu Item <?php echo $index + 1; ?></span>
                    <div class="wdm-menu-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-add-submenu-item" data-index="<?php echo $index; ?>">Add Submenu</button>
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

                <div class="wdm-form-row">
                    <div class="wdm-form-col wdm-form-col-full">
                        <label class="wdm-form-label">
                            <input type="checkbox" name="wdm_menu_items[<?php echo $index; ?>][mega_menu]" value="1" <?php checked($mega_menu, true); ?> />
                            Enable Mega Menu
                        </label>
                    </div>
                </div>


                <div class="wdm-submenu-items hidden">
                    <?php $this->render_submenu_items($index, $submenu); ?>
                </div>
            </div>
<?php
        }
    }

    private function render_submenu_items($menu_index, $submenu_items123) {
        foreach ($submenu_items123 as $sub_index => $sub_item) {
            $text = esc_attr($sub_item['text'] ?? '');
            $url  = esc_attr($sub_item['url'] ?? '');
            $target = esc_attr($sub_item['target'] ?? '_self');
            $description = esc_textarea($sub_item['description'] ?? '');
    ?>
            <div class="wdm-submenu-item" data-submenu-index="<?php echo $sub_index; ?>">
                <div class="wdm-submenu-item-header">
                    <span class="wdm-submenu-title">Submenu Item <?php echo $sub_index + 1; ?></span>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-submenu-item">Remove</button>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Submenu Text</label>
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

?>

<hr>
<h3>🔍 Raw Saved Data (Debug)</h3>
<pre style="background: #f9f9f9; padding: 1em; border: 1px solid #ccc;     width: 63%;
    margin-left: 200px;">
<?php
$raw_menu_data = get_option('wdm_menu_items', []);
print_r($raw_menu_data);
?>
</pre>