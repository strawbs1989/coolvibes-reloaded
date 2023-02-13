var langToggle = document.querySelectorAll(".mwh_el_lang .mwh_lang_toggle");

if (langToggle) {
  var langContent = document.querySelector(".mwh_el_lang .globe_class");
  var langWindow = document.querySelector(".play_window .mwh_languages");

  Array.prototype.forEach.call(langToggle, function (el) {
    el.addEventListener("click", function () {
      langWindow.innerHTML = langContent.innerHTML;
    });
  });
}
