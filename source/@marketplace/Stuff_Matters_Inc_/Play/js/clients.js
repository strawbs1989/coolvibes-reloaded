// Masonry

// Get field options from hidden input
var dMasonry = document.getElementById("m_info_fields-clients").getAttribute("data-masonry");

if (dMasonry == "true") {
  // Masonry
  var grids = document.querySelectorAll(".play_grid-clients");
  grids.forEach(function (grid) {
    var msnry = new Masonry(grid, {
      itemSelector: ".play_grid-item",
      columnWidth: ".play_grid-sizer",
      gutter: ".play_grid-gutter-sizer",
      percentPosition: true,
    });
    imagesLoaded(grid).on("progress", function () {
      msnry.layout();
    });
  });
}
