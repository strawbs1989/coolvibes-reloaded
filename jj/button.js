$('.control').on('mousedown', function() {
  $(this).toggleClass('pause play');
});

$(document).on('keyup', function(e) {
  if (e.which == 32) {
    $('.control').toggleClass('pause play');
  }
});