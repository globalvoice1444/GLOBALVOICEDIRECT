/*====================================
			Search JS
		======================================*/
$(".search .search-icon").on("click", function () {
  $(".search-top").toggleClass("active");
});
/*====================================
      user JS
    ======================================*/
$(".user-cart .user-list").on("click", function () {
  $(".user-cart-top").toggleClass("active");
});
/*====================================
			Header Sticky JS
		======================================*/
jQuery(window).on("scroll", function () {
  if ($(this).scrollTop() > 1) {
    $(".navbar").addClass("sticky");
  } else {
    $(".navbar").removeClass("sticky");
  }
});

$(document).ready(function () {
  $(".ct-slick-homepage").on("init", function (event, slick) {
    $(".animated").addClass("activate fadeInUp");
  });

  $(".ct-slick-homepage").slick({
    autoplay: true,
    autoplaySpeed: 3000,
    pauseOnHover: false,
  });

  $(".ct-slick-homepage").on("afterChange", function (
    event,
    slick,
    currentSlide
  ) {
    $(".animated").removeClass("off");
    $(".animated").addClass("activate fadeInUp");
  });

  $(".ct-slick-homepage").on("beforeChange", function (
    event,
    slick,
    currentSlide
  ) {
    $(".animated").removeClass("activate fadeInUp");
    $(".animated").addClass("off");
  });
});

$(document).ready(function () {
  if ($("[data-background]").length > 0) {
    $("[data-background]").each(function () {
      var $background, $backgroundmobile, $this;
      $this = $(this);
      $background = $(this).attr("data-background");
      $backgroundmobile = $(this).attr("data-background-mobile");
      if ($this.attr("data-background").substr(0, 1) === "#") {
        return $this.css("background-color", $background);
      } else if ($this.attr("data-background-mobile") && device.mobile()) {
        return $this.css("background-image", "url(" + $backgroundmobile + ")");
      } else {
        return $this.css("background-image", "url(" + $background + ")");
      }
    });
  }
});
