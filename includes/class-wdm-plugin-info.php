<?php
/**
 * WDM Plugin Information
 * Handles plugin information display and GitHub auto-update functionality
 */

namespace WDM_Custom_Header;

if (!defined('ABSPATH')) {
    exit;
}

class WDM_Plugin_Info {

    public function render_plugin_info_content() {
?>
        <div class="wdm-form-section">
            <h3>Plugin Information</h3>
            
            <div class="wdm-info-grid">
                <div class="wdm-info-item">
                    <label class="wdm-info-label">Plugin Name:</label>
                    <span class="wdm-info-value">WDM Custom Header</span>
                </div>
                
                <div class="wdm-info-item">
                    <label class="wdm-info-label">Version:</label>
                    <span class="wdm-info-value"><?php echo WDM_CUSTOM_HEADER_VERSION; ?></span>
                </div>
                
                <div class="wdm-info-item">
                    <label class="wdm-info-label">Author:</label>
                    <span class="wdm-info-value">Carlo Santos</span>
                </div>
                
                <div class="wdm-info-item">
                    <label class="wdm-info-label">Organization:</label>
                    <span class="wdm-info-value">WD Morgan Solutions</span>
                </div>
            </div>
        </div>

        <div class="wdm-form-section">
            <h3>GitHub Auto-Update System</h3>
            
            <div class="wdm-update-status">
                <div class="wdm-update-info">
                    <p><strong>Repository:</strong> WDM Custom Header Plugin</p>
                    <p><strong>Branch:</strong> main</p>
                    <p><strong>Last Checked:</strong> <span id="wdm-last-check">Never</span></p>
                    <p><strong>Status:</strong> <span id="wdm-update-status" class="wdm-status-current">Up to date</span></p>
                </div>
                
                <div class="wdm-update-actions">
                    <button type="button" id="wdm-check-updates" class="wdm-btn wdm-btn-secondary">Check for Updates</button>
                    <button type="button" id="wdm-force-update" class="wdm-btn wdm-btn-primary" style="display: none;">Update Now</button>
                </div>
            </div>
            
            <div id="wdm-update-log" class="wdm-update-log hidden">
                <h4>Update Log</h4>
                <div class="wdm-log-content"></div>
            </div>
        </div>

        <div class="wdm-form-section">
            <h3>Shortcode Usage</h3>
            
            <div class="wdm-shortcode-info">
                <p>Use the following shortcode to display the custom header in your WordPress content:</p>
                <code class="wdm-shortcode-example">[wdm_custom_header]</code>
                
                <h4>Available Attributes:</h4>
                <ul class="wdm-shortcode-attributes">
                    <li><code>class</code> - Add custom CSS classes to the header container</li>
                    <li><code>id</code> - Set a custom ID for the header element</li>
                </ul>
                
                <h4>Example Usage:</h4>
                <code class="wdm-shortcode-example">[wdm_custom_header class="my-custom-header" id="main-navigation"]</code>
            </div>
        </div>

        <div class="wdm-form-section">
            <h3>Template Integration</h3>
            
            <div class="wdm-template-info">
                <p>To integrate the header directly into your theme templates, use the following PHP code:</p>
                <pre class="wdm-code-example"><code>&lt;?php
if (function_exists('wdm_display_header')) {
    wdm_display_header();
}
?&gt;</code></pre>
                
                <p>Or use the shortcode function:</p>
                <pre class="wdm-code-example"><code>&lt;?php echo do_shortcode('[wdm_custom_header]'); ?&gt;</code></pre>
            </div>
        </div>

        <div class="wdm-form-section">
            <h3>Support & Documentation</h3>
            
            <div class="wdm-support-info">
                <p>For support and documentation, please visit:</p>
                <ul class="wdm-support-links">
                    <li><a href="https://greybullrescue.org" target="_blank">Greybull Rescue Website</a></li>
                    <li><a href="#" onclick="window.open('mailto:support@greybullrescue.org'); return false;">Email Support</a></li>
                </ul>
                
                <h4>System Requirements:</h4>
                <ul class="wdm-requirements">
                    <li>WordPress 5.0 or higher</li>
                    <li>PHP 7.4 or higher</li>
                    <li>Modern web browser with JavaScript enabled</li>
                </ul>
            </div>
        </div>

        <script>
        jQuery(document).ready(function($) {
            // Check for updates functionality
            $('#wdm-check-updates').on('click', function() {
                var $button = $(this);
                var $status = $('#wdm-update-status');
                var $log = $('#wdm-update-log');
                
                $button.text('Checking...').prop('disabled', true);
                $status.text('Checking for updates...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wdm_check_update',
                        nonce: '<?php echo wp_create_nonce("wdm_update_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $('#wdm-last-check').text(new Date().toLocaleString());
                            if (response.data.update_available) {
                                $status.text('Update available!').removeClass('wdm-status-current').addClass('wdm-status-update');
                                $('#wdm-force-update').show();
                            } else {
                                $status.text('Up to date').removeClass('wdm-status-update').addClass('wdm-status-current');
                                $('#wdm-force-update').hide();
                            }
                            
                            if (response.data.log) {
                                $log.removeClass('hidden').find('.wdm-log-content').html(response.data.log);
                            }
                        } else {
                            $status.text('Check failed');
                        }
                    },
                    error: function() {
                        $status.text('Connection error');
                    },
                    complete: function() {
                        $button.text('Check for Updates').prop('disabled', false);
                    }
                });
            });
            
            // Force update functionality
            $('#wdm-force-update').on('click', function() {
                if (!confirm('Are you sure you want to update the plugin? This will overwrite any local changes.')) {
                    return;
                }
                
                var $button = $(this);
                var $status = $('#wdm-update-status');
                
                $button.text('Updating...').prop('disabled', true);
                $status.text('Updating plugin...');
                
                $.ajax({
                    url: ajaxurl,
                    type: 'POST',
                    data: {
                        action: 'wdm_force_update',
                        nonce: '<?php echo wp_create_nonce("wdm_update_nonce"); ?>'
                    },
                    success: function(response) {
                        if (response.success) {
                            $status.text('Update complete!').removeClass('wdm-status-update').addClass('wdm-status-current');
                            $button.hide();
                            alert('Plugin updated successfully! Please refresh the page.');
                            location.reload();
                        } else {
                            $status.text('Update failed');
                            alert('Update failed: ' + (response.data || 'Unknown error'));
                        }
                    },
                    error: function() {
                        $status.text('Update error');
                        alert('Update failed due to connection error');
                    },
                    complete: function() {
                        $button.text('Update Now').prop('disabled', false);
                    }
                });
            });
        });
        </script>
<?php
    }
}