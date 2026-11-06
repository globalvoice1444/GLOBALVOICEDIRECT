$(document).ready(function(){
	
	if(sessionStorage.getItem('firstVisit') === null){
		sessionStorage.setItem('firstVisit', '1');
	}
	if(sessionStorage.getItem('firstVisit') === "1"){
		$('#banner-popup').fadeIn();
		$('.ui-widget-overlay').fadeIn();
	}
	(function ($){
		$('#thankyou-btn').on('click', function(){
		  	$('#banner-popup').fadeOut();
		  	$('.ui-widget-overlay').fadeOut();
	  		sessionStorage.setItem('firstVisit', '2');
		});
		$('.ui-dialog-titlebar-close').on('click', function(){
		  	$('#banner-popup').fadeOut();
		  	$('.ui-widget-overlay').fadeOut();
	  		sessionStorage.setItem('firstVisit', '2');
		});
	}(jQuery)); 
});

