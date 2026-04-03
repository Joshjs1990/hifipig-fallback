(function ($) {
  let frame = null;
  let $active = null;

  function setImage($wrap, id, url) {
    const $preview = $wrap.find("[data-category-image-preview]");
    const $input = $wrap.find("[data-category-image-id]");
    const $select = $wrap.find("[data-category-image-select]");
    const $remove = $wrap.find("[data-category-image-remove]");

    $input.val(id || "");

    if (url) {
      $preview.html(`<img src="${url}" class="hifipig-category-image__img" alt="">`);
      $remove.show();
      $select.text("Change image");
    } else {
      $preview.html('<div class="hifipig-category-image__placeholder">No image selected</div>');
      $remove.hide();
      $select.text("Select image");
    }
  }

  $(document).on("click", "[data-category-image-select]", function (e) {
    e.preventDefault();
    $active = $(this).closest(".hifipig-category-image");
    if (!$active.length) return;

    if (!frame) {
      frame = wp.media({
        title: "Select category image",
        button: { text: "Use this image" },
        library: { type: "image" },
        multiple: false,
      });

      frame.on("select", function () {
        if (!$active) return;
        const attachment = frame.state().get("selection").first().toJSON();
        const url = attachment.sizes?.thumbnail?.url || attachment.url;
        setImage($active, attachment.id, url);
      });
    }

    frame.open();
  });

  $(document).on("click", "[data-category-image-remove]", function (e) {
    e.preventDefault();
    const $wrap = $(this).closest(".hifipig-category-image");
    if (!$wrap.length) return;
    setImage($wrap, "", "");
  });
})(jQuery);
