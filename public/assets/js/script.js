/*========================
Preloader
========================*/

$(window).on("load", function() { //make sure that whole website is loaded.
    $("#status").fadeOut();
    $("#preloader").delay(350).fadeOut('slow');
});