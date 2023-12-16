	// Add active class to the current nav item
$(document).ready(function() {
    $('nav .nav-link').click(function() {
        $('nav .nav-link').removeClass('active');
        $(this).addClass('active');
    });
});