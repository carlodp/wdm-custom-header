/**
 * WDM Custom Header Admin JavaScript
 * Handles admin interface interactions
 */

jQuery(document).ready(function($) {
    
    // Initialize color pickers
    $('.wdm-color-picker').wpColorPicker();
    
    // Initialize sortable for menu items
    $('#wdm-menu-items').sortable({
        handle: '.wdm-drag-handle',
        placeholder: 'wdm-sortable-placeholder',
        tolerance: 'pointer',
        axis: 'y'
    });
    
    // Initialize sortable for utility buttons
    $('#wdm-utility-buttons').sortable({
        handle: '.wdm-drag-handle',
        placeholder: 'wdm-sortable-placeholder',
        tolerance: 'pointer',
        axis: 'y'
    });
    
    // Toggle menu item editing
    $(document).on('click', '.wdm-toggle-item', function() {
        var $item = $(this).closest('.wdm-menu-item');
        var $content = $item.find('.wdm-menu-item-content');
        
        if ($content.is(':visible')) {
            $content.slideUp();
            $(this).text('Edit');
        } else {
            $content.slideDown();
            $(this).text('Close');
        }
    });
    
    // Toggle utility button editing
    $(document).on('click', '.wdm-toggle-button', function() {
        var $button = $(this).closest('.wdm-utility-button');
        var $content = $button.find('.wdm-utility-button-content');
        
        if ($content.is(':visible')) {
            $content.slideUp();
            $(this).text('Edit');
        } else {
            $content.slideDown();
            $(this).text('Close');
        }
    });
    
    // Remove menu item
    $(document).on('click', '.wdm-remove-item', function() {
        if (confirm('Are you sure you want to remove this menu item?')) {
            $(this).closest('.wdm-menu-item').fadeOut(function() {
                $(this).remove();
                updateMenuItemIndexes();
            });
        }
    });
    
    // Remove utility button
    $(document).on('click', '.wdm-remove-button', function() {
        if (confirm('Are you sure you want to remove this utility button?')) {
            $(this).closest('.wdm-utility-button').fadeOut(function() {
                $(this).remove();
                updateUtilityButtonIndexes();
            });
        }
    });
    
    // Add new menu item
    $('#wdm-add-menu-item').click(function() {
        var index = $('#wdm-menu-items .wdm-menu-item').length;
        var template = getMenuItemTemplate(index);
        $('#wdm-menu-items').append(template);
    });
    
    // Add new utility button
    $('#wdm-add-utility-button').click(function() {
        var index = $('#wdm-utility-buttons .wdm-utility-button').length;
        var template = getUtilityButtonTemplate(index);
        $('#wdm-utility-buttons').append(template);
    });
    
    // Handle dropdown checkbox
    $(document).on('change', 'input[name*="[has_dropdown]"]', function() {
        var $item = $(this).closest('.wdm-menu-item');
        var $dropdownSection = $item.find('.wdm-dropdown-section');
        
        if ($(this).is(':checked')) {
            if ($dropdownSection.length === 0) {
                var index = $item.data('index');
                var dropdownTemplate = getDropdownTemplate(index);
                $item.find('.wdm-menu-item-content table').append(dropdownTemplate);
            } else {
                $dropdownSection.show();
            }
        } else {
            $dropdownSection.hide();
        }
    });
    
    // Add dropdown item
    $(document).on('click', '.wdm-add-dropdown-item', function() {
        var $container = $(this).siblings('.wdm-dropdown-items');
        var menuIndex = $(this).closest('.wdm-menu-item').data('index');
        var dropdownIndex = $container.find('.wdm-dropdown-item').length;
        var template = getDropdownItemTemplate(menuIndex, dropdownIndex);
        $container.append(template);
    });
    
    // Remove dropdown item
    $(document).on('click', '.wdm-remove-dropdown-item', function() {
        $(this).closest('.wdm-dropdown-item').remove();
    });
    
    // Save menu items
    $('#wdm-save-menu-items').click(function() {
        var menuItems = [];
        
        $('#wdm-menu-items .wdm-menu-item').each(function(index) {
            var $item = $(this);
            var menuItem = {
                title: $item.find('input[name*="[title]"]').val(),
                url: $item.find('input[name*="[url]"]').val(),
                has_dropdown: $item.find('input[name*="[has_dropdown]"]').is(':checked')
            };
            
            if (menuItem.has_dropdown) {
                menuItem.dropdown_items = [];
                $item.find('.wdm-dropdown-item').each(function() {
                    var $dropdownItem = $(this);
                    menuItem.dropdown_items.push({
                        title: $dropdownItem.find('input[placeholder="Title"]').val(),
                        url: $dropdownItem.find('input[placeholder="URL"]').val()
                    });
                });
            }
            
            menuItems.push(menuItem);
        });
        
        saveMenuItems(menuItems);
    });
    
    // Save utility buttons
    $('#wdm-save-utility-buttons').click(function() {
        var utilityButtons = [];
        
        $('#wdm-utility-buttons .wdm-utility-button').each(function(index) {
            var $button = $(this);
            var utilityButton = {
                label: $button.find('input[name*="[label]"]').val(),
                url: $button.find('input[name*="[url]"]').val(),
                class: $button.find('input[name*="[class]"]').val(),
                target: $button.find('select[name*="[target]"]').val(),
                visibility: $button.find('select[name*="[visibility]"]').val()
            };
            
            utilityButtons.push(utilityButton);
        });
        
        saveUtilityButtons(utilityButtons);
    });
    
    // Update menu item indexes
    function updateMenuItemIndexes() {
        $('#wdm-menu-items .wdm-menu-item').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/menu_items\[\d+\]/, 'menu_items[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }
    
    // Update utility button indexes
    function updateUtilityButtonIndexes() {
        $('#wdm-utility-buttons .wdm-utility-button').each(function(index) {
            $(this).attr('data-index', index);
            $(this).find('input, select').each(function() {
                var name = $(this).attr('name');
                if (name) {
                    var newName = name.replace(/utility_buttons\[\d+\]/, 'utility_buttons[' + index + ']');
                    $(this).attr('name', newName);
                }
            });
        });
    }
    
    // Get menu item template
    function getMenuItemTemplate(index) {
        return `
            <div class="wdm-menu-item" data-index="${index}">
                <div class="wdm-menu-item-header">
                    <span class="wdm-menu-item-title">New Menu Item</span>
                    <div class="wdm-menu-item-controls">
                        <button type="button" class="button wdm-toggle-item">Edit</button>
                        <button type="button" class="button wdm-remove-item">Remove</button>
                        <span class="wdm-drag-handle">≡</span>
                    </div>
                </div>
                
                <div class="wdm-menu-item-content" style="display: block;">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Title</th>
                            <td>
                                <input type="text" name="menu_items[${index}][title]" value="" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">URL</th>
                            <td>
                                <input type="url" name="menu_items[${index}][url]" value="" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Has Dropdown</th>
                            <td>
                                <label>
                                    <input type="checkbox" name="menu_items[${index}][has_dropdown]" value="1" />
                                    Enable mega dropdown menu
                                </label>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
    }
    
    // Get utility button template
    function getUtilityButtonTemplate(index) {
        return `
            <div class="wdm-utility-button" data-index="${index}">
                <div class="wdm-utility-button-header">
                    <span class="wdm-utility-button-title">New Button</span>
                    <div class="wdm-utility-button-controls">
                        <button type="button" class="button wdm-toggle-button">Edit</button>
                        <button type="button" class="button wdm-remove-button">Remove</button>
                        <span class="wdm-drag-handle">≡</span>
                    </div>
                </div>
                
                <div class="wdm-utility-button-content" style="display: block;">
                    <table class="form-table">
                        <tr>
                            <th scope="row">Label</th>
                            <td>
                                <input type="text" name="utility_buttons[${index}][label]" value="" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">URL</th>
                            <td>
                                <input type="url" name="utility_buttons[${index}][url]" value="" class="regular-text" />
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">CSS Class</th>
                            <td>
                                <input type="text" name="utility_buttons[${index}][class]" value="" class="regular-text" />
                                <p class="description">Custom CSS class for styling (e.g., btn-volunteer, btn-donate)</p>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Target</th>
                            <td>
                                <select name="utility_buttons[${index}][target]">
                                    <option value="_self">Same window</option>
                                    <option value="_blank">New window</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row">Visibility</th>
                            <td>
                                <select name="utility_buttons[${index}][visibility]">
                                    <option value="both">Desktop & Mobile</option>
                                    <option value="desktop">Desktop only</option>
                                    <option value="mobile">Mobile only</option>
                                </select>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        `;
    }
    
    // Get dropdown template
    function getDropdownTemplate(menuIndex) {
        return `
            <tr class="wdm-dropdown-section">
                <th scope="row">Dropdown Items</th>
                <td>
                    <div class="wdm-dropdown-items">
                    </div>
                    <button type="button" class="button wdm-add-dropdown-item">Add Dropdown Item</button>
                </td>
            </tr>
        `;
    }
    
    // Get dropdown item template
    function getDropdownItemTemplate(menuIndex, dropdownIndex) {
        return `
            <div class="wdm-dropdown-item">
                <input type="text" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][title]" value="" placeholder="Title" />
                <input type="url" name="menu_items[${menuIndex}][dropdown_items][${dropdownIndex}][url]" value="" placeholder="URL" />
                <button type="button" class="button wdm-remove-dropdown-item">Remove</button>
            </div>
        `;
    }
    
    // Save menu items via AJAX
    function saveMenuItems(menuItems) {
        $.ajax({
            url: wdm_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wdm_save_menu_items',
                nonce: wdm_admin_ajax.nonce,
                menu_items: menuItems
            },
            beforeSend: function() {
                $('#wdm-save-menu-items').prop('disabled', true).text('Saving...');
            },
            success: function(response) {
                if (response.success) {
                    showNotice('Menu items saved successfully!', 'success');
                } else {
                    showNotice('Error saving menu items: ' + response.data, 'error');
                }
            },
            error: function() {
                showNotice('Error saving menu items. Please try again.', 'error');
            },
            complete: function() {
                $('#wdm-save-menu-items').prop('disabled', false).text('Save Menu Items');
            }
        });
    }
    
    // Save utility buttons via AJAX
    function saveUtilityButtons(utilityButtons) {
        $.ajax({
            url: wdm_admin_ajax.ajax_url,
            type: 'POST',
            data: {
                action: 'wdm_save_utility_buttons',
                nonce: wdm_admin_ajax.nonce,
                utility_buttons: utilityButtons
            },
            beforeSend: function() {
                $('#wdm-save-utility-buttons').prop('disabled', true).text('Saving...');
            },
            success: function(response) {
                if (response.success) {
                    showNotice('Utility buttons saved successfully!', 'success');
                } else {
                    showNotice('Error saving utility buttons: ' + response.data, 'error');
                }
            },
            error: function() {
                showNotice('Error saving utility buttons. Please try again.', 'error');
            },
            complete: function() {
                $('#wdm-save-utility-buttons').prop('disabled', false).text('Save Utility Buttons');
            }
        });
    }
    
    // Show admin notice
    function showNotice(message, type) {
        var $notice = $('<div class="notice notice-' + type + ' is-dismissible"><p>' + message + '</p></div>');
        $('.wrap h1').after($notice);
        
        setTimeout(function() {
            $notice.fadeOut(function() {
                $(this).remove();
            });
        }, 3000);
    }
    
});