/**
 * WDM Custom Header Admin JavaScript
 * Handles dynamic menu management interface
 */

(function($) {
    'use strict';
  
    // Initialize admin interface
    $(document).ready(function() {
        initializeMenuManagement();
        initializeSortable();
        initializeTabInterface();
        bindEvents();
    });
  
    /**
     * Initialize menu management functionality
     */
    function initializeMenuManagement() {
        updateMenuIndices();
        toggleSubmenuVisibility();
    }
  
    /**
     * Initialize sortable functionality for menu items
     */
    function initializeSortable() {
        if (typeof $.fn.sortable !== 'undefined') {
            $('.wdm-menu-items').sortable({
                handle: '.wdm-drag-handle',
                placeholder: 'wdm-sortable-placeholder',
                opacity: 0.8,
                cursor: 'move',
                update: function() {
                    updateMenuIndices();
                    markAsChanged();
                }
            });
        }
    }
  
    /**
     * Initialize tab interface
     */
    function initializeTabInterface() {
        $('.wdm-tab-nav button').on('click', function() {
            var targetTab = $(this).data('tab');
            
            // Update nav
            $('.wdm-tab-nav button').removeClass('active');
            $(this).addClass('active');
            
            // Update content
            $('.wdm-tab-content').removeClass('active');
            $('#' + targetTab).addClass('active');
        });
    }
  
    /**
     * Bind all event handlers
     */
    function bindEvents() {
        // Add new menu item
        $(document).on('click', '.wdm-add-menu-item', function() {
            addNewMenuItem();
        });
  
        // Add new submenu item
        $(document).on('click', '.wdm-add-submenu-item', function() {
            var menuIndex = $(this).closest('.wdm-menu-item').data('index');
            addNewSubmenuItem(menuIndex);
        });
  
        // Remove menu item
        $(document).on('click', '.wdm-remove-menu-item', function() {
            if (confirm('Are you sure you want to remove this menu item?')) {
                $(this).closest('.wdm-menu-item').fadeOut(300, function() {
                    $(this).remove();
                    updateMenuIndices();
                    markAsChanged();
                });
            }
        });
  
        // Remove submenu item
        $(document).on('click', '.wdm-remove-submenu-item', function() {
            if (confirm('Are you sure you want to remove this submenu item?')) {
                $(this).closest('.wdm-submenu-item').fadeOut(300, function() {
                    $(this).remove();
                    updateSubmenuIndices();
                    markAsChanged();
                });
            }
        });
  
        // Toggle submenu visibility
        $(document).on('click', '.wdm-toggle-submenu', function() {
            var $submenu = $(this).closest('.wdm-menu-item').find('.wdm-submenu-items');
            $submenu.toggleClass('hidden');
            
            var isHidden = $submenu.hasClass('hidden');
            $(this).text(isHidden ? 'Show Submenu (' + $submenu.find('.wdm-submenu-item').length + ')' : 'Hide Submenu');
        });
  
        // Form field changes
        $(document).on('input change', '.wdm-form-input, .wdm-form-select, .wdm-form-textarea', function() {
            markAsChanged();
        });
  
        // Preview functionality
        $(document).on('click', '.wdm-preview-header', function() {
            generatePreview();
        });
  
        // Form submission
        $('#wdm-menu-settings-form').on('submit', function() {
            showSavingState();
            return true;
        });
    }
  
    /**
     * Add new menu item
     */
    function addNewMenuItem() {
        var menuCount = $('.wdm-menu-item').length;
        var template = getMenuItemTemplate(menuCount);
        
        $('.wdm-menu-items').append(template);
        updateMenuIndices();
        markAsChanged();
        
        // Scroll to new item and focus first input
        var $newItem = $('.wdm-menu-item').last();
        $('html, body').animate({
            scrollTop: $newItem.offset().top - 100
        }, 500);
        $newItem.find('.wdm-form-input').first().focus();
    }
  
    /**
     * Add new submenu item
     */
    function addNewSubmenuItem(menuIndex) {
        var $menuItem = $('.wdm-menu-item[data-index="' + menuIndex + '"]');
        var $submenuContainer = $menuItem.find('.wdm-submenu-items');
        var submenuCount = $submenuContainer.find('.wdm-submenu-item').length;
        
        var template = getSubmenuItemTemplate(menuIndex, submenuCount);
        $submenuContainer.append(template);
        
        // Show submenu if hidden
        if ($submenuContainer.hasClass('hidden')) {
            $submenuContainer.removeClass('hidden');
            $menuItem.find('.wdm-toggle-submenu').text('Hide Submenu');
        }
        
        updateSubmenuIndices();
        markAsChanged();
        
        // Focus new submenu item
        var $newSubmenu = $submenuContainer.find('.wdm-submenu-item').last();
        $newSubmenu.find('.wdm-form-input').first().focus();
    }
  
    /**
     * Get menu item template
     */
    function getMenuItemTemplate(index) {
        return `
            <div class="wdm-menu-item" data-index="${index}">
                <div class="wdm-menu-item-header">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-menu-item-title">Menu Item ${index + 1}</span>
                    <div class="wdm-menu-item-actions">
                        <button type="button" class="wdm-btn wdm-btn-small wdm-add-submenu-item">Add Submenu</button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-toggle-submenu">Show Submenu (0)</button>
                        <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-menu-item">Remove</button>
                    </div>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Menu Text</label>
                        <input type="text" name="wdm_menu_items[${index}][text]" class="wdm-form-input" placeholder="Menu Item Text" />
                        <div class="wdm-help-text">Text displayed in the navigation menu</div>
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="url" name="wdm_menu_items[${index}][url]" class="wdm-form-input" placeholder="https://example.com" />
                        <div class="wdm-help-text">Link destination (leave empty for dropdown-only)</div>
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_menu_items[${index}][target]" class="wdm-form-select">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>
                </div>

                <div class="wdm-form-row">
                    <div class="wdm-form-col wdm-form-col-full">
                        <label class="wdm-form-label">
                        <input type="hidden" name="wdm_menu_items[${index}][mega_menu]" value="0" />
                        <input type="checkbox" name="wdm_menu_items[${index}][mega_menu]" value="1" />
                        Enable Mega Menu
                        </label>
                    </div>
                </div>
                
                <div class="wdm-submenu-items hidden">
                    <!-- Submenu items will be added here -->
                </div>
            </div>
        `;
    }
  
    /**
     * Get submenu item template
     */
    function getSubmenuItemTemplate(menuIndex, submenuIndex) {
        return `
            <div class="wdm-submenu-item" data-submenu-index="${submenuIndex}">
                <div class="wdm-submenu-header">
                    <span class="wdm-submenu-title">Submenu Item ${submenuIndex + 1}</span>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-submenu-item">Remove</button>
                </div>
                
                <div class="wdm-form-row">
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">Text</label>
                        <input type="text" name="wdm_menu_items[${menuIndex}][submenu][${submenuIndex}][text]" class="wdm-form-input" placeholder="Submenu Text" />
                    </div>
                    <div class="wdm-form-col">
                        <label class="wdm-form-label">URL</label>
                        <input type="url" name="wdm_menu_items[${menuIndex}][submenu][${submenuIndex}][url]" class="wdm-form-input" placeholder="https://example.com" />
                    </div>
                    <div class="wdm-form-col-narrow">
                        <label class="wdm-form-label">Target</label>
                        <select name="wdm_menu_items[${menuIndex}][submenu][${submenuIndex}][target]" class="wdm-form-select">
                            <option value="_self">Same Window</option>
                            <option value="_blank">New Window</option>
                        </select>
                    </div>
                </div>
                
                ${submenuIndex === 0 ? `
                    <div class="wdm-form-row">
                      <div class="wdm-form-col">
                        <label class="wdm-form-label">Description</label>
                        <textarea name="wdm_menu_items[${menuIndex}][submenu][${submenuIndex}][description]" class="wdm-form-input wdm-form-textarea" placeholder="Optional description for mega menu"></textarea>
                        <div class="wdm-help-text">Brief description shown in mega menu dropdowns</div>
                      </div>
                    </div>
                  ` : ''}                  
            </div>
        `;
    }
  
    /**
     * Update menu item indices after reordering
     */
    function updateMenuIndices() {
        $('.wdm-menu-item').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('.wdm-menu-item-title').text('Menu Item ' + (index + 1));
            
            // Update input names
            $(this).find('input, select, textarea').each(function() {
                var name = $(this).attr('name');
                if (name && name.includes('wdm_menu_items[')) {
                    var newName = name.replace(/wdm_menu_items\[\d+\]/, 'wdm_menu_items[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }
  
    /**
     * Update submenu item indices
     */
    function updateSubmenuIndices() {
        $('.wdm-menu-item').each(function() {
            var menuIndex = $(this).data('index');
            $(this).find('.wdm-submenu-item').each(function(submenuIndex) {
                $(this).attr('data-submenu-index', submenuIndex);
                $(this).find('.wdm-submenu-title').text('Submenu Item ' + (submenuIndex + 1));
                
                // Update input names
                $(this).find('input, select, textarea').each(function() {
                    var name = $(this).attr('name');
                    if (name && name.includes('[submenu][')) {
                        var pattern = new RegExp('wdm_menu_items\\[' + menuIndex + '\\]\\[submenu\\]\\[\\d+\\]');
                        var replacement = 'wdm_menu_items[' + menuIndex + '][submenu][' + submenuIndex + ']';
                        var newName = name.replace(pattern, replacement);
                        $(this).attr('name', newName);
                    }
                });
            });
        });
        
        // Update submenu counts in toggle buttons
        $('.wdm-menu-item').each(function() {
            var submenuCount = $(this).find('.wdm-submenu-item').length;
            var $toggle = $(this).find('.wdm-toggle-submenu');
            var isHidden = $(this).find('.wdm-submenu-items').hasClass('hidden');
            $toggle.text(isHidden ? 'Show Submenu (' + submenuCount + ')' : 'Hide Submenu');
        });
    }
  
    /**
     * Toggle submenu sections visibility on load
     */
    function toggleSubmenuVisibility() {
        $('.wdm-menu-item').each(function() {
            var submenuCount = $(this).find('.wdm-submenu-item').length;
            var $submenu = $(this).find('.wdm-submenu-items');
            var $toggle = $(this).find('.wdm-toggle-submenu');
            
            $submenu.addClass('hidden');
            $toggle.text('Show Submenu (' + submenuCount + ')');
        });
    }
  
    /**
     * Mark form as changed
     */
    function markAsChanged() {
        // Remove form change tracking to prevent "Leave site?" dialog
        // Form changes are now tracked internally without browser intervention
    }
  
    /**
     * Show saving state
     */
    function showSavingState() {
        $('.wdm-admin-container').addClass('wdm-loading');
        $('input[type="submit"]').val('Saving...').prop('disabled', true);
    }
  
    /**
     * Generate preview of current menu structure
     */
    function generatePreview() {
        var menuData = collectMenuData();
        var previewHtml = generatePreviewHtml(menuData);
        $('.wdm-preview-content').html(previewHtml);
    }
  
    /**
     * Collect current menu data from form
     */
    function collectMenuData() {
        var menuItems = [];
        
        $('.wdm-menu-item').each(function() {
            var $item = $(this);
            var menuItem = {
                text: $item.find('input[name*="[text]"]').val() || 'Menu Item',
                url: $item.find('input[name*="[url]"]').val() || '#',
                target: $item.find('select[name*="[target]"]').val() || '_self',
                submenu: []
            };
            
            $item.find('.wdm-submenu-item').each(function() {
                var $submenu = $(this);
                var submenuItem = {
                    text: $submenu.find('input[name*="[text]"]').val() || 'Submenu Item',
                    url: $submenu.find('input[name*="[url]"]').val() || '#',
                    target: $submenu.find('select[name*="[target]"]').val() || '_self',
                    description: $submenu.find('textarea[name*="[description]"]').val() || ''
                };
                menuItem.submenu.push(submenuItem);
            });
            
            menuItems.push(menuItem);
        });
        
        return menuItems;
    }
  
    /**
     * Generate preview HTML
     */
    function generatePreviewHtml(menuData) {
        var html = '<div class="wdm-menu-preview"><ul>';
        
        menuData.forEach(function(item, index) {
            html += '<li>';
            html += '<strong>' + item.text + '</strong>';
            if (item.url && item.url !== '#') {
                html += ' → ' + item.url;
            }
            
            if (item.submenu && item.submenu.length > 0) {
                html += '<ul style="margin-left: 20px; margin-top: 5px;">';
                item.submenu.forEach(function(subitem) {
                    html += '<li>' + subitem.text;
                    if (subitem.url && subitem.url !== '#') {
                        html += ' → ' + subitem.url;
                    }
                    if (subitem.description) {
                        html += '<br><em style="color: #666; font-size: 12px;">' + subitem.description + '</em>';
                    }
                    html += '</li>';
                });
                html += '</ul>';
            }
            html += '</li>';
        });
        
        html += '</ul></div>';
        return html;
    }
  
    /**
     * Warn user about unsaved changes
     */
    $(window).on('beforeunload', function() {
        if ($('body').hasClass('wdm-form-changed')) {
            return 'You have unsaved changes. Are you sure you want to leave?';
        }
    });
  
  })(jQuery);