// Get next post path
var next_post_path = document.querySelector(".mblog-next-post").getAttribute("href");
// Number of next post nodes
var npp = 1;

// Init infinite scroll
var infScroll = new InfiniteScroll(document.querySelector(".body-container--blog-post"), {
  path: function () {
    return `${next_post_path}`;
  },
  append: ".play_blog_post-container",
});

// Add append listener
infScroll.on("append", onInfAppend);

function onInfAppend() {
  var nodes = document.querySelectorAll(".mblog-next-post");

  // Comments button
  var containers = document.querySelectorAll(".blog-comments");
  var container = containers[containers.length - 1];
  var get_url = container.querySelector(".bc_url").getAttribute("data-abs_url");
  var btn_text = document.querySelector(".rp_cols").getAttribute("data-vc");
  var btn = "<button class='button-view-comments play_secondary_button' onclick=goToComments('" + get_url + "')>" + btn_text + "</button>";
  container.innerHTML += btn;

  if (nodes.length > npp) {
    // Get new next post path
    next_post_path = nodes[nodes.length - 1].getAttribute("href");
    npp = nodes.length;
  } else {
    // End of posts (Disable loading on scroll)
    infScroll.options.loadOnScroll = false;
  }
}

// Go to post comments
function goToComments(p) {
  window.location.href = p + "#hs_cos_wrapper_blog_comments";
}
