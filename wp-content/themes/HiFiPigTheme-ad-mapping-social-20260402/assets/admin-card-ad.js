(function ($) {
  const $id = $("#hifipig_card_ad_image_id");
  const $select = $("#hifipig_card_ad_select");
  const $clear = $("#hifipig_card_ad_clear");
  const $preview = $(".hifipig-card-ad__preview");

  if (!$id.length || !$select.length || !$preview.length) return;

  let frame = null;

  function setImage(id, html) {
    $id.val(id || "");
    if (html) {
      $preview.html(html);
      $clear.show();
      $select.text("Change image");
    } else {
      $preview.html('<div class="hifipig-card-ad__placeholder">No image selected</div>');
      $clear.hide();
      $select.text("Select image");
    }
  }

  $select.on("click", function (e) {
    e.preventDefault();

    if (frame) {
      frame.open();
      return;
    }

    frame = wp.media({
      title: "Select secondary image",
      button: { text: "Use this image" },
      library: { type: "image" },
      multiple: false,
    });

    frame.on("select", function () {
      const attachment = frame.state().get("selection").first().toJSON();
      setImage(attachment.id, attachment.sizes?.thumbnail?.url
        ? `<img src="${attachment.sizes.thumbnail.url}" class="hifipig-card-ad-preview" alt="">`
        : `<img src="${attachment.url}" class="hifipig-card-ad-preview" alt="">`);
    });

    frame.open();
  });

  $clear.on("click", function (e) {
    e.preventDefault();
    setImage("", "");
  });
})(jQuery);
