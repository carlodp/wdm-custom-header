/**
 * WDM Main Navigation Script
 * Handles dynamic menu management interface
 */

(function ($) {
  "use strict";

  // Initialize admin interface
  $(document).ready(function () {
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
    if (typeof $.fn.sortable !== "undefined") {
      $(".wdm-menu-items").sortable({
        handle: ".wdm-drag-handle",
        placeholder: "wdm-sortable-placeholder",
        opacity: 0.8,
        cursor: "move",
        update: function () {
          updateMenuIndices();
          markAsChanged();
        },
      });
    }
  }

  /**
   * Initialize tab interface
   */
  function initializeTabInterface() {
    $(".wdm-tab-nav button").on("click", function () {
      var targetTab = $(this).data("tab");

      // Update nav
      $(".wdm-tab-nav button").removeClass("active");
      $(this).addClass("active");

      // Update content
      $(".wdm-tab-content").removeClass("active");
      $("#" + targetTab).addClass("active");
    });
  }

  /**
   * Bind all event handlers
   */
  function bindEvents() {
    // Add new menu item
    $(document).on("click", ".wdm-add-menu-item", function () {
      addNewMenuItem();
    });

    // Add new submenu item
    $(document).on("click", ".wdm-add-submenu-item", function () {
      var menuIndex = $(this).closest(".wdm-menu-item").data("index");
      addNewSubmenuItem(menuIndex);
    });

    // Remove menu item
    $(document).on("click", ".wdm-remove-menu-item", function () {
      if (confirm("Are you sure you want to remove this menu item?")) {
        $(this)
          .closest(".wdm-menu-item")
          .fadeOut(300, function () {
            $(this).remove();
            updateMenuIndices();
            markAsChanged();
          });
      }
    });

    $(document).on(
      "change",
      'input[type="checkbox"][name$="[mega_menu]"]',
      function () {
        var $menuItem = $(this).closest(".wdm-menu-item");
        var isMega = $(this).is(":checked");
        var $submenuItems = $menuItem.find(".wdm-submenu-items");
        var $megaColumns = $menuItem.find(".wdm-mega-columns");
        var $addSubmenuBtn = $menuItem.find(".wdm-add-submenu-item");

        if (isMega) {
          // Hide Add Submenu button instantly
          $addSubmenuBtn.hide();

          // Remove all submenu items except index 0 (main submenu)
          $submenuItems.find(".wdm-submenu-item").each(function (idx) {
            if (idx !== 0) $(this).remove();
          });

          // If no main submenu, add one
          if (
            $submenuItems.find('.wdm-submenu-item[data-submenu-index="0"]')
              .length === 0
          ) {
            addNewSubmenuItem($menuItem.data("index"), 0);
          }

          $submenuItems.removeClass("hidden"); // Show main submenu
          $megaColumns.removeClass("hidden"); // Show columns
        } else {
          // Show Add Submenu button instantly
          $addSubmenuBtn.show();
          $submenuItems.removeClass("hidden");
          $megaColumns.addClass("hidden");
        }
        markAsChanged();
      }
    );

    // Add new column in mega menu mode
    $(document).on("click", ".wdm-add-column", function () {
      var $menuItem = $(this).closest(".wdm-menu-item");
      var menuIndex = $menuItem.data("index");
      var $columnsList = $menuItem.find(".wdm-mega-columns-list");
      var colIndex = $columnsList.find(".wdm-mega-column").length;
      var columnTemplate = getColumnTemplate(menuIndex, colIndex);
      $columnsList.append(columnTemplate);
      markAsChanged();
    });

    // Remove column
    $(document).on("click", ".wdm-remove-column", function () {
      $(this).closest(".wdm-mega-column").remove();
      markAsChanged();
    });

    // Add submenu item (link) to a column
    $(document).on("click", ".wdm-add-link", function () {
      var $col = $(this).closest(".wdm-mega-column");
      var $menuItem = $(this).closest(".wdm-menu-item");
      var menuIndex = $menuItem.data("index");
      var colIndex = $col.data("col-index");
      var linkIndex = $col.find(".wdm-mega-link").length;
      var linkTemplate = getColumnLinkTemplate(menuIndex, colIndex, linkIndex);
      $col.find(".wdm-mega-links").append(linkTemplate);
      markAsChanged();
    });

    // Remove link from column
    $(document).on("click", ".wdm-remove-link", function () {
      $(this).closest(".wdm-mega-link").remove();
      markAsChanged();
    });

    // Remove submenu item
    $(document).on("click", ".wdm-remove-submenu-item", function () {
      if (confirm("Are you sure you want to remove this submenu item?")) {
        $(this)
          .closest(".wdm-submenu-item")
          .fadeOut(300, function () {
            $(this).remove();
            updateSubmenuIndices();
            markAsChanged();
          });
      }
    });

    // Toggle submenu visibility
    $(document).on("click", ".wdm-toggle-submenu", function () {
      var $menuItem = $(this).closest(".wdm-menu-item");
      var isMega = $menuItem
        .find('input[type="checkbox"][name$="[mega_menu]"]')
        .is(":checked");
      var $submenu = $menuItem.find(
        isMega ? ".wdm-mega-columns" : ".wdm-submenu-items"
      );

      $submenu.toggleClass("hidden");
      var isHidden = $submenu.hasClass("hidden");

      if (isMega) {
        // Get current number of columns for the label
        var colCount = $menuItem.find(".wdm-mega-column").length;
        $(this).html(
          isHidden
            ? '<i class="fas fa-chevron-down"></i> Show Mega Menu (' +
                colCount +
                ")"
            : '<i class="fas fa-chevron-up"></i> Hide Mega Menu'
        );
      } else {
        var submenuCount = $menuItem.find(".wdm-submenu-item").length;
        $(this).html(
          isHidden
            ? '<i class="fas fa-chevron-down"></i> Show Submenu (' +
                submenuCount +
                ")"
            : '<i class="fas fa-chevron-up"></i> Hide Submenu'
        );
      }
    });

    // Form field changes
    $(document).on(
      "input change",
      ".wdm-form-input, .wdm-form-select, .wdm-form-textarea",
      function () {
        markAsChanged();
      }
    );

    // Preview functionality
    $(document).on("click", ".wdm-preview-header", function () {
      generatePreview();
    });

    // Form submission
    $("#wdm-menu-settings-form").on("submit", function () {
      showSavingState();
      return true;
    });
  }

  /**
   * Add new menu item
   */
  function addNewMenuItem() {
    var menuCount = $(".wdm-menu-item").length;
    var template = getMenuItemTemplate(menuCount);

    $(".wdm-menu-items").append(template);
    updateMenuIndices();
    markAsChanged();

    // Scroll to new item and focus first input
    var $newItem = $(".wdm-menu-item").last();
    $("html, body").animate(
      {
        scrollTop: $newItem.offset().top - 100,
      },
      500
    );
    $newItem.find(".wdm-form-input").first().focus();
  }

  /**
   * Add new submenu item
   */
  function addNewSubmenuItem(menuIndex, forcedIndex = null) {
    var $menuItem = $('.wdm-menu-item[data-index="' + menuIndex + '"]');
    var $submenuContainer = $menuItem.find(".wdm-submenu-items");
    var submenuCount = $submenuContainer.find(".wdm-submenu-item").length;
    var submenuIndex = forcedIndex !== null ? forcedIndex : submenuCount;

    var template = getSubmenuItemTemplate(menuIndex, submenuIndex);
    $submenuContainer.append(template);

    // Show submenu if hidden
    if ($submenuContainer.hasClass("hidden")) {
      $submenuContainer.removeClass("hidden");
      $menuItem.find(".wdm-toggle-submenu").text("Hide Submenu");
    }

    updateSubmenuIndices();
    markAsChanged();

    // Focus new submenu item
    var $newSubmenu = $submenuContainer.find(".wdm-submenu-item").last();
    $newSubmenu.find(".wdm-form-input").first().focus();
  }

  /**
   * Get menu item template
   */
  function getMenuItemTemplate(index) {
    return `
        <div class="wdm-menu-item" data-index="${index}">
            <div class="wdm-menu-item-header">
                <div class="drag-name-container">
                    <span class="wdm-drag-handle">⋮⋮</span>
                    <span class="wdm-menu-item-title">Menu Item ${
                      index + 1
                    }</span>
                    <label class="wdm-toggle-switch">
                        <input type="checkbox" name="wdm_menu_items[${index}][mega_menu]" value="1" />
                        <span class="toggle-label">Enable Mega Menu</span>
                    </label>
                </div>
                <div class="wdm-menu-item-actions">
                    <button type="button" class="wdm-btn wdm-btn-small wdm-add-submenu-item">Add Submenu</button>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-toggle-submenu"><i class="fas fa-chevron-down"></i> Show Submenu (0)</button>
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
            <div class="wdm-submenu-or-columns">
                <div class="wdm-submenu-items hidden">
                    <!-- Submenu items will be added here (non-mega mode) -->
                </div>
                <div class="wdm-mega-columns hidden">
                    <div class="wdm-mega-columns-list">
                        <!-- Columns will be added here -->
                    </div>
                    <button type="button" class="wdm-btn wdm-btn-small wdm-add-column">Add Column</button>
                </div>
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
                    <span class="wdm-submenu-title">Submenu Item ${
                      submenuIndex + 1
                    }</span>
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
                
                ${
                  submenuIndex === 0
                    ? `
                    <div class="wdm-form-row">
                      <div class="wdm-form-col">
                        <label class="wdm-form-label">Description</label>
                        <textarea name="wdm_menu_items[${menuIndex}][submenu][${submenuIndex}][description]" class="wdm-form-input wdm-form-textarea" placeholder="Optional description for mega menu"></textarea>
                        <div class="wdm-help-text">Brief description shown in mega menu dropdowns</div>
                      </div>
                    </div>
                  `
                    : ""
                }                  
            </div>
        `;
  }

  /**
   * Get column template for Mega Menu
   */
  function getColumnTemplate(menuIndex, colIndex) {
    return `
        <div class="wdm-mega-column" data-col-index="${colIndex}">
            <div class="wdm-mega-column-header">
                <input type="text" name="wdm_menu_items[${menuIndex}][columns][${colIndex}][title]" class="wdm-form-input wdm-mega-col-title" placeholder="Column Title" />
                <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-column">Remove Column</button>
            </div>
            <div class="wdm-mega-links">
                <!-- Submenu items (links) will be added here -->
            </div>
            <button type="button" class="wdm-btn wdm-btn-small wdm-add-link" data-menu-index="${menuIndex}" data-col-index="${colIndex}">Add Submenu Item</button>
        </div>
    `;
  }

  /**
   * Get submenu item (link) template for Mega Menu columns
   */
  function getColumnLinkTemplate(menuIndex, colIndex, linkIndex) {
    return `
        <div class="wdm-mega-link" data-link-index="${linkIndex}">
            <input type="text" name="wdm_menu_items[${menuIndex}][columns][${colIndex}][links][${linkIndex}][text]" class="wdm-form-input" placeholder="Link Text" />
            <input type="url" name="wdm_menu_items[${menuIndex}][columns][${colIndex}][links][${linkIndex}][url]" class="wdm-form-input" placeholder="https://example.com" />
            <select name="wdm_menu_items[${menuIndex}][columns][${colIndex}][links][${linkIndex}][target]" class="wdm-form-select">
                <option value="_self">Same Window</option>
                <option value="_blank">New Window</option>
            </select>
            <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-link">Remove</button>
        </div>
    `;
  }

  /**
   * Update menu item indices after reordering
   */
  function updateMenuIndices() {
    $(".wdm-menu-item").each(function (index) {
      $(this).attr("data-index", index);
      $(this)
        .find(".wdm-menu-item-title")
        .text("Menu Item " + (index + 1));

      // Update input names
      $(this)
        .find("input, select, textarea")
        .each(function () {
          var name = $(this).attr("name");
          if (name && name.includes("wdm_menu_items[")) {
            var newName = name.replace(
              /wdm_menu_items\[\d+\]/,
              "wdm_menu_items[" + index + "]"
            );
            $(this).attr("name", newName);
          }
        });
    });
  }

  /**
   * Update submenu item indices
   */
  function updateSubmenuIndices() {
    $(".wdm-menu-item").each(function () {
      var menuIndex = $(this).data("index");
      $(this)
        .find(".wdm-submenu-item")
        .each(function (submenuIndex) {
          $(this).attr("data-submenu-index", submenuIndex);
          $(this)
            .find(".wdm-submenu-title")
            .text("Submenu Item " + (submenuIndex + 1));

          // Update input names
          $(this)
            .find("input, select, textarea")
            .each(function () {
              var name = $(this).attr("name");
              if (name && name.includes("[submenu][")) {
                var pattern = new RegExp(
                  "wdm_menu_items\\[" + menuIndex + "\\]\\[submenu\\]\\[\\d+\\]"
                );
                var replacement =
                  "wdm_menu_items[" +
                  menuIndex +
                  "][submenu][" +
                  submenuIndex +
                  "]";
                var newName = name.replace(pattern, replacement);
                $(this).attr("name", newName);
              }
            });
        });
    });

    // Update submenu counts in toggle buttons
    $(".wdm-menu-item").each(function () {
      var submenuCount = $(this).find(".wdm-submenu-item").length;
      var $toggle = $(this).find(".wdm-toggle-submenu");
      var isHidden = $(this).find(".wdm-submenu-items").hasClass("hidden");
      $toggle.html(
        isHidden
          ? '<i class="fas fa-chevron-down"></i> Show Submenu (' +
              submenuCount +
              ")"
          : '<i class="fas fa-chevron-up"></i> Hide Submenu'
      );
    });
  }

  /**
   * Toggle submenu sections visibility on load
   */
  function toggleSubmenuVisibility() {
    $(".wdm-menu-item").each(function () {
      var $menuItem = $(this);
      var isMega = $menuItem
        .find('input[type="checkbox"][name$="[mega_menu]"]')
        .is(":checked");
      var $submenu = $menuItem.find(
        isMega ? ".wdm-mega-columns" : ".wdm-submenu-items"
      );
      $submenu.addClass("hidden");

      var label = "";
      if (isMega) {
        var colCount = $menuItem.find(".wdm-mega-column").length;
        label =
          '<i class="fas fa-chevron-down"></i> Show Mega Menu (' +
          colCount +
          ")";
      } else {
        var submenuCount = $menuItem.find(".wdm-submenu-item").length;
        label =
          '<i class="fas fa-chevron-down"></i> Show Submenu (' +
          submenuCount +
          ")";
      }
      $menuItem.find(".wdm-toggle-submenu").html(label);
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
    $(".wdm-admin-container").addClass("wdm-loading");
    $('input[type="submit"]').val("Saving...").prop("disabled", true);
  }

  /**
   * Generate preview of current menu structure
   */
  function generatePreview() {
    var menuData = collectMenuData();
    var previewHtml = generatePreviewHtml(menuData);
    $(".wdm-preview-content").html(previewHtml);
  }

  /**
   * Collect current menu data from form
   */
/**
 * Collect current menu data from form
 */
function collectMenuData() {
    var menuItems = [];
  
    $(".wdm-menu-item").each(function () {
      var $item = $(this);
      var isMega = $item
        .find('input[type="checkbox"][name$="[mega_menu]"]')
        .is(":checked");
  
      var menuItem = {
        text: $item.find('input[name*="[text]"]').val() || "Menu Item",
        url: $item.find('input[name*="[url]"]').val() || "#",
        target: $item.find('select[name*="[target]"]').val() || "_self",
        submenu: [],
        columns: [],
      };
  
      if (isMega) {
        $item.find(".wdm-mega-column").each(function () {
          var $col = $(this);
          var column = {
            title: $col.find('input[name*="[title]"]').val() || "",
            links: [],
          };
  
          $col.find(".wdm-mega-link").each(function () {
            var $link = $(this);
            column.links.push({
              text: $link.find('input[name*="[text]"]').val() || "",
              url: $link.find('input[name*="[url]"]').val() || "#",
              target: $link.find('select[name*="[target]"]').val() || "_self",
            });
          });
  
          menuItem.columns.push(column);
        });
  
        // Also get the main submenu item (index 0), if it exists
        var $mainSub = $item.find(
          '.wdm-submenu-item[data-submenu-index="0"]'
        );
        if ($mainSub.length > 0) {
          var submenuItem = {
            text: $mainSub.find('input[name*="[text]"]').val() || "Main Submenu",
            url: $mainSub.find('input[name*="[url]"]').val() || "#",
            target:
              $mainSub.find('select[name*="[target]"]').val() || "_self",
            description:
              $mainSub.find('textarea[name*="[description]"]').val() || "",
          };
          menuItem.submenu.push(submenuItem);
        }
      } else {
        $item.find(".wdm-submenu-item").each(function () {
          var $submenu = $(this);
          var submenuItem = {
            text: $submenu.find('input[name*="[text]"]').val() || "Submenu Item",
            url: $submenu.find('input[name*="[url]"]').val() || "#",
            target: $submenu.find('select[name*="[target]"]').val() || "_self",
            description:
              $submenu.find('textarea[name*="[description]"]').val() || "",
          };
          menuItem.submenu.push(submenuItem);
        });
      }
  
      menuItems.push(menuItem);
    });
  
    return menuItems;
  }  

  /**
   * Generate preview HTML
   */
  function generatePreviewHtml(menuData) {
    var html = '<div class="wdm-menu-preview"><ul>';

    menuData.forEach(function (item, index) {
      html += "<li>";
      html += "<strong>" + item.text + "</strong>";
      if (item.url && item.url !== "#") {
        html += " → " + item.url;
      }

      if (item.submenu && item.submenu.length > 0) {
        html += '<ul style="margin-left: 20px; margin-top: 5px;">';
        item.submenu.forEach(function (subitem) {
          html += "<li>" + subitem.text;
          if (subitem.url && subitem.url !== "#") {
            html += " → " + subitem.url;
          }
          if (subitem.description) {
            html +=
              '<br><em style="color: #666; font-size: 12px;">' +
              subitem.description +
              "</em>";
          }
          html += "</li>";
        });
        html += "</ul>";
      }
      html += "</li>";
    });

    html += "</ul></div>";
    return html;
  }

  /**
   * Warn user about unsaved changes
   */
  $(window).on("beforeunload", function () {
    if ($("body").hasClass("wdm-form-changed")) {
      return "You have unsaved changes. Are you sure you want to leave?";
    }
  });
})(jQuery);
