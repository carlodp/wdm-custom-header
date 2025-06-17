jQuery(document).ready(function ($) {
  console.log('jQuery available:', typeof jQuery !== 'undefined');

  /** MEDIA PICKER **/
  $(".wdm-media-picker").on("click", function (e) {
    e.preventDefault();

    const target = $(this).data("target");
    const input = $(target);

    const frame = wp.media({
      title: "Select or Upload Logo",
      button: {
        text: "Use this image",
      },
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      input.val(attachment.url);
    });

    frame.open();
  });

  /** UTILITY BUTTON MANAGER **/
  const maxButtons = 3;

  function updateButtonState() {
    const count = $(".utility-button-row").length;
    $("#add-utility-button").prop("disabled", count >= maxButtons);
  }

  $("#add-utility-button").on("click", function (e) {
    e.preventDefault();

    const currentCount = $(".utility-button-row").length;
    if (currentCount >= maxButtons) return;

    const newIndex = currentCount;
    const newRow = `
      <div class="utility-button-row">
        <label class="wdm-form-label">Button Label</label>
        <input type="text" name="wdm_header_options[utility_buttons][${newIndex}][label]" placeholder="Button Label" class="wdm-form-input" />

        <label class="wdm-form-label">Button URL</label>
        <input type="url" name="wdm_header_options[utility_buttons][${newIndex}][url]" placeholder="https://example.com" class="wdm-form-input" />

        <label class="wdm-form-label">Button Color</label>
        <input type="color" name="wdm_header_options[utility_buttons][${newIndex}][color]" value="#d13a30" class="wdm-color-input" />

        <button type="button" class="remove-utility-button button">Remove</button>
      </div>
    `;

    $("#utility-buttons-wrapper").append(newRow);
    updateButtonState();
  });

  $(document).on("click", ".remove-utility-button", function () {
    $(this).closest(".utility-button-row").remove();

    // Re-index all utility button fields
    $(".utility-button-row").each(function (i) {
      $(this).find('input[name*="[label]"]').attr("name", `wdm_header_options[utility_buttons][${i}][label]`);
      $(this).find('input[name*="[url]"]').attr("name", `wdm_header_options[utility_buttons][${i}][url]`);
      $(this).find('input[name*="[color]"]').attr("name", `wdm_header_options[utility_buttons][${i}][color]`);
    });

    updateButtonState();
  });

  updateButtonState(); // run on load
});
