var inc = 0;
var states = ["mgi-col1", "mgi-col2"];
var cols = document.querySelector(".rp_cols").getAttribute("data-cols");

if (cols == "3col") {
  states.push("mgi-col3");
}

const date_options = { year: "numeric", month: "short", day: "numeric" };

var blog_post_formatter = function (blogposts) {
  var formatted = '<div class="play_grid-container"><section class="play_grid-inner">';
  for (var i = 0; i < blogposts.length; i++) {
    var blogpost = blogposts[i];
    formatted += '<article class="play_grid-col play_grid--' + cols + " " + states[inc] + '">';
    formatted += '<div class="play_widget-container play_widget-single_post">';
    formatted += `<a class="play_widget_post_item-a mw_single_post" href="${blogpost.url}">`;
    if (blogpost.featuredImage) {
      formatted += `<img src="${blogpost.featuredImage}" alt="${blogpost.featuredImageAltText}" width="100%" loading="lazy">`;
    }
    formatted += '<div class="play_widget_post_item-text">';
    formatted += `<h4 class="play_widget_post_item-title">${blogpost.name}</h4>`;
    formatted += `<time class="play_widget_post_item-date">${new Date(blogpost.publishDate).toLocaleDateString(
      "en-EN",
      date_options
    )}</time>`;
    formatted += "</div></a></div>";
    formatted += "</article>";
    inc = ++inc % states.length;
  }
  formatted += "</section></div>";
  return formatted;
};
