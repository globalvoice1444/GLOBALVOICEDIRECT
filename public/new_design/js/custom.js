/*====================================
			Search JS
		======================================*/
$(".search .search-icon").on("click", function () {
  $(".search-top").toggleClass("active");
});
/*====================================
			user JS
		======================================*/
$(".top-cart .cart").on("click", function () {
  $(".shopping-cart").toggleClass("active");
});
/*====================================
			Cart JS
		======================================*/
$(".user-bar .user-item").on("click", function () {
  $(".user-bar-top").toggleClass("active");
});
/*====================================
			Slider JS
		======================================*/
    $(document).ready(function(){

if($('.brands_slider').length)
{
var brandsSlider = $('.brands_slider');

brandsSlider.owlCarousel(
{
loop:true,
autoplay:true,
autoplayTimeout:5000,
nav:false,
dots:false,
autoWidth:true,
items:8,
margin:42
});

if($('.brands_prev').length)
{
var prev = $('.brands_prev');
prev.on('click', function()
{
brandsSlider.trigger('prev.owl.carousel');
});
}

if($('.brands_next').length)
{
var next = $('.brands_next');
next.on('click', function()
{
brandsSlider.trigger('next.owl.carousel');
});
}
}


});