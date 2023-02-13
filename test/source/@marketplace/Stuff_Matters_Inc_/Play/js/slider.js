document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".play_splide").forEach(function (slider) {
    new Splide(slider).mount();
  });
});
