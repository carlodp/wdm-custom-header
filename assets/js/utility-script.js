(function ($) {
    "use strict";
  
    $(document).ready(function () {
      bindUtilityEvents();
      initializeUtilitySortable();
      bindIconPreviews();
      initIconPickers();
    });
  
    function bindUtilityEvents() {
      $(document).on("click", ".wdm-add-utility-item", function () {
        addUtilityItem();
      });
  
      $(document).on("click", ".wdm-remove-utility-item", function () {
        if (confirm("Are you sure you want to remove this utility item?")) {
          $(this)
            .closest(".wdm-utility-item")
            .fadeOut(200, function () {
              $(this).remove();
              reindexUtilityItems();
            });
        }
      });
  
      $(document).on("click", ".wdm-icon-picker-trigger", function () {
        const $wrapper = $(this).closest(".icon-container");
        $wrapper.find(".iconpicker-popover").show();
      });

      $(document).on("click", ".iconpicker-item", function() {
        const $wrapper = $(this).closest(".icon-container");
        $wrapper.find(".iconpicker-popover").hide();
      });
    }
  
    function addUtilityItem() {
      const index = $(".wdm-utility-item").length;
      const template = getUtilityItemTemplate(index);
      $(".wdm-utility-items").append(template);
      reindexUtilityItems();
      initIconPickers();
      bindIconPreviews();
    }
  
    function getUtilityItemTemplate(index) {
      return `
        <div class="wdm-utility-item" data-index="${index}">
          <div class="wdm-utility-item-header">
            <div class="drag-name-container">
              <span class="wdm-drag-handle ui-sortable-handle">⋮⋮</span>
              <span class="wdm-utility-item-title">Utility Item ${index + 1}</span>
            </div>
            <div class="wdm-utility-item-actions">
              <button type="button" class="wdm-btn wdm-btn-small wdm-btn-danger wdm-remove-utility-item">
                <i class="fas fa-trash-alt"></i> Remove
              </button>
            </div>
          </div>
  
          <div class="wdm-form-row">
            <div class="wdm-form-col">
              <label class="wdm-form-label">Menu Text</label>
              <input type="text" name="wdm_utility_items[${index}][text]" class="wdm-form-input" />
            </div>
            <div class="wdm-form-col">
              <label class="wdm-form-label">URL</label>
              <input type="text" name="wdm_utility_items[${index}][url]" class="wdm-form-input" />
            </div>
            <div class="wdm-form-col-narrow">
              <label class="wdm-form-label">Target</label>
              <select name="wdm_utility_items[${index}][target]" class="wdm-form-select">
                <option value="_self">Same Window</option>
                <option value="_blank">New Window</option>
              </select>
            </div>
            <div class="wdm-form-col icon-container">
              <label class="wdm-form-label">Icon</label>
              <div class="wdm-icon-preview" style="margin-bottom: 5px; font-size: 18px;"></div>
              <button type="button" class="wdm-btn wdm-btn-secondary wdm-icon-picker-trigger">🎨 Choose Icon</button>
              <input type="text" 
                     name="wdm_utility_items[${index}][icon]" 
                     class="wdm-form-input wdm-icon-input" 
                     value=""
                     style="display:none;" 
                     data-icon="" 
                     role="iconpicker" />
            </div>
          </div>
        </div>
      `;
    }
  
    function reindexUtilityItems() {
      $(".wdm-utility-item").each(function (index) {
        $(this).attr("data-index", index);
        $(this)
          .find(".wdm-utility-item-title")
          .text("Utility Item " + (index + 1));
  
        $(this)
          .find("input, select")
          .each(function () {
            let name = $(this).attr("name");
            if (name && name.includes("wdm_utility_items[")) {
              const newName = name.replace(
                /wdm_utility_items\[\d+\]/,
                `wdm_utility_items[${index}]`
              );
              $(this).attr("name", newName);
            }
          });
      });
    }
  
    function bindIconPreviews() {
      $(document)
        .off("input", ".wdm-icon-input")
        .on("input", ".wdm-icon-input", function () {
          const iconClass = $(this).val().trim();
          const $preview = $(this).closest(".icon-container").find(".wdm-icon-preview");
  
          if (iconClass) {
            $preview.html(`<i class="${iconClass}"></i>`);
          } else {
            $preview.empty();
          }
        });
    }
  
    function initIconPickers() {
      $(".wdm-icon-input").iconpicker({
        align: "center",
        arrowClass: "btn-primary",
        arrowPrevIconClass: "fas fa-angle-left",
        arrowNextIconClass: "fas fa-angle-right",
        cols: 10,
        footer: true,
        header: true,
        iconset: "fontawesome5",
        labelHeader: "{0} of {1} pages",
        labelFooter: "Select an icon",
        placement: "bottom",
        rows: 5,
        search: true,
        selectedClass: "btn-success",
        unselectedClass: "",
        hideOnSelect: true
      });
  
      $(".wdm-icon-input").on("iconpickerSelected", function (e) {
        const iconClass = e.iconpickerValue;
        const $container = $(this).closest(".icon-container");
        $container.find(".wdm-icon-preview").html(`<i class="${iconClass}"></i>`);
        $(this).val(iconClass); // update the hidden input field
      });
    }
  
    function initializeUtilitySortable() {
      if (typeof $.fn.sortable !== "undefined") {
        $(".wdm-utility-items").sortable({
          handle: ".wdm-drag-handle",
          placeholder: "wdm-sortable-placeholder",
          opacity: 0.8,
          cursor: "move",
          update: function () {
            reindexUtilityItems();
          },
        });
      }
    }
  })(jQuery);
  