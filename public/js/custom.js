/**************************************************************************
* Common js

**************************************************************************/
jQuery(document).ready(function() {

	/* Bestsell slider */
	jQuery(".product-flexslider .slider-items").owlCarousel({
		items: 4, //10 items above 1000px browser width
		itemsDesktop: [1024, 3], //5 items between 1024px and 901px
		itemsDesktopSmall: [980, 2], // 3 items betweem 900px and 601px
		itemsTablet: [768, 2], //2 items between 600 and 0;
		itemsMobile: [360, 1],
		navigation: true,
		navigationText: ["<a class=\"flex-prev\"><i class=\"fa fa-angle-left\"></i></a>", "<a class=\"flex-next\"><i class=\"fa fa-angle-right\"></i></a>"],
		slideSpeed: 500,
		pagination: true
	});

	(function ( $ ) {

		$.fn.bootstrapNumber = function( options ) {

			var settings = $.extend({
				upClass: 'default',
				downClass: 'default',
				upText: '<i class="fa fa-plus" aria-hidden="true"></i>',
				downText: '<i class="fa fa-minus" aria-hidden="true"></i>',
				center: true
			}, options );

			return this.each(function(e) {
				var self = $(this);
				var clone = self.clone(true, true);

				var min = self.attr('min');
				var max = self.attr('max');
				var step = parseInt(self.attr('step')) || 1;

				function setText(n) {
					if (isNaN(n) || (min && n < min) || (max && n > max)) {
						return false;
					}

					clone.focus().val(n);
					clone.trigger('change');
					return true;
				}

				var group = $("<div class='group-input'></div>");
				var down = $("<button type='button'>" + settings.downText + "</button>").attr('class', 'btn btn-' + settings.downClass).click(function() {
					setText(parseInt(clone.val() || clone.attr('value')) - step);
				});
				var up = $("<button type='button'>" + settings.upText + "</button>").attr('class', 'btn btn-' + settings.upClass).click(function() {
					setText(parseInt(clone.val() || clone.attr('value')) + step);
				});
				$("<span class='input-btn'></span>").append(down).appendTo(group);
				clone.appendTo(group);
				if(clone && settings.center) {
					clone.css('text-align', 'center');
				}
				$("<span class='input-btn'></span>").append(up).appendTo(group);

			// remove spins from original
			clone.prop('type', 'text').keydown(function(e) {
				if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
					(e.keyCode == 65 && e.ctrlKey === true) ||
					(e.keyCode >= 35 && e.keyCode <= 39)) {
					return;
			}
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}

			var c = String.fromCharCode(e.which);
			var n = parseInt(clone.val() + c);

			if ((min && n < min) || (max && n > max)) {
				e.preventDefault();
			}
		});

			self.replaceWith(group);
		});
		};
	} ( jQuery ));

	$('.after').bootstrapNumber();
	$('#colorful').bootstrapNumber({
		upClass: 'success',
		downClass: 'danger'
	});

});