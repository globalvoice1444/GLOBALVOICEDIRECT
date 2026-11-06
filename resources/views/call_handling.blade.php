@extends('layouts.app')
@section('content')
	<section class="header-wrap">
        <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
            <div class="container">
                <h2>Call Handling</h2>
            </div>
    	</section>
       @include('includes.top_header')
       @include('includes.navbar')
    </section>
	<div class="container body_container">

		<div class="feature_body">
			<div class="feature-module">	
				<h2 class="features-heading">Send callers to the right place the first time.</h2>
				<p class="features-paragraph">Create your own customized rules to route incoming calls to their desired location.</p>
			</div>
	    	<div class="tab-content">
	    		<div class="row">
					@include('layouts.feature_sidebar')
					<div class="col-md-9 tab">		
					    <div class="row user-section-container">

	                		<div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">
			                    <div class="row">
			                    	<div class="col-md-12">
			                            <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/14.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									    Menus/Auto-Attendant</h5>
									    <p>Automatically route calls to the extension or department callers want to reach with menus and an automated receptionist. Professional and efficient.</p>
								    </div>        
								</div>
								<div class="row">
									<div class="col-md-12">
				                        <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/1.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									    Dial-by-Name Directory</h5>
									    <p>Ask callers to enter (via keypad) the first or last name of the person they’re seeking. Callers can find their own way to employees in your company directory.</p> 
								    </div>
								</div>
	                		</div>

			                <div class="col-xs-12 col-sm-12 col-md-6 col-lg-6 col-xl-6 wow fadeInUp" data-wow-duration="3s" style="visibility: visible; animation-duration: 3s; animation-name: fadeInUp;">

			                    <!-- Pricing Table 02 -->
			                    <div class="row">
								    <div class="col-md-12 imge"> 
								        <img src="{{ asset('images/feature-icon/42.png')}}" alt="A boatload of features with every user extension." class="boatloadimg lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/features-extensions-image@2x.png" data-was-processed="true">
								        <noscript>
								         		<img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/features-extensions-image@2x.png" alt="A boatload of features with every user extension." class="boatloadimg">
								        </noscript>
								    </div>
								</div>
			                </div>

			                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

			                    <!-- Pricing Table 03 -->
			                    <div class="row">
			                    	<div class="col-md-12">
			                             <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/30.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									        	Forwarding</h5>
									           <p>Easily forward all calls to your cell phone, home phone or practically anything that rings. Your extension can go with you everywhere, but only if you want it to.</p> 
								    </div>
								</div>

								<div class="row">
									<div class="col-md-12">
			                             <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/13.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									        	Call Transfer</h5>
									           <p>An in-call feature that allows you to transfer any call from your phone to another number or extension on the same Globalvoicedirect.com account.</p>
								    </div> 
								</div>

			                </div>

			                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

			                    <!-- Pricing Table 04 -->
			                    <div class="row">
			                    	<div class="col-md-12 left">
			                             <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/31.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									        	Dial by Extension</h5>
									           <p>Since every employee has his or her own extension (for free), callers can just enter the extension of the person they want to speak with and they’re connected instantly.</p>
								    </div> 
								</div>

			                </div>

			                <div class="col-xs-12 col-sm-12 col-md-3 col-lg-3 col-xl-3 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

			                    <!-- Pricing Table 05 -->
			                    <div class="row">
			                    	<div class="col-md-12 right">
			                             <h5 class="green-titles">
									        <img src="{{ asset('images/feature-icon/41.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png" data-was-processed="true">
									        <noscript>
									            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/Voicemail.png')}}" width="32" height="32" alt="Voicemail" class="sm-icon">
									        </noscript>
									        	Call Blocking</h5>
									           <p>Block incoming calls from unwanted callers, unknown numbers or certain area codes. Your phone won’t even ring. Also block outgoing calls to international numbers and Directory Assistance (411).</p>
								    </div> 
								</div>

			                </div>

	            		</div> 
		            </div>
		        </div>
	       
        	</div>
		</div>
	</div>  

	<div class="gray-bg awesome800">
	    <div class="container">
	        <h3 class="text-center">Set it and forget it.</h3>
	        <p class="text-center">Set your phone to route calls exactly how you want. It’s the ultimate traffic cop. And it never takes a day off.</p>
	        <div class="row">
	            <div class="col-md-12"> 
	                <img src="{{ asset('images/feature-icon/43.png')}}" alt="1-800-Awesome" class="awesome800img lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/numbers2.png" data-was-processed="true"> 
	            </div>
	        </div>
	    </div>
	</div>
	<div class="features-facts">
	    <div class="container">
	        <div class="row">
	            <div class="schedule col-md-6 col-sm-6 col-xs-12">
	                <h5 class="blue-title">
	                    <img src="{{ asset('images/feature-icon/17.png')}}" width="32" height="32" alt="Custom Local Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityLocalNumbers.png" data-was-processed="true">
	                    Scheduling</h5>
	                <p class="brief">Route incoming calls to different numbers, extensions, greetings, menus and more, based on the day and time a call is received.</p>
	            </div>
	            <div class="find col-md-6 col-sm-6 col-xs-12">
	                <h5 class="green-title">
	                    <img src="{{ asset('images/feature-icon/4.1.png')}}" width="32" height="32" alt="Custom Toll Free Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityTollFreeNumbers.png" data-was-processed="true">
	                    Find-Me Follow-Me
	                </h5>
	                <p class="brief">Direct calls to a single or multiple phone numbers and have them ring all at once or one after the other.</p>
	            </div>
	        </div> 
	    </div> 
	</div> 

	<div class="grays-bg promusic">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-5 col-sm-5 col-xs-12 text-center"> 
	                <img src="{{ asset('images/feature-icon/44.png')}}" alt="Give your customers something worth listening to" class="promusicimgs lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" data-was-processed="true">
	                <noscript>
	                    <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" alt="Give your customers something worth listening to" class="promusicimg">
	                </noscript>
	            </div>
	            <div class="col-md-7 col-sm-7 col-xs-12">
	                <h3 class="heading-gray">Run your own mini call center..</h3>
	                <p class="paragraph-gray">Put a plan in place to efficiently manage all of your incoming calls. Show customers you’ve got your act together. So smart. So easy.</p>
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{ asset('images/feature-icon/19.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon">
	                        </noscript>
	                        Queues</h5>
	                    <p>Route incoming calls to any individual or group of employees. Ideal for customer service, sales departments, help desks and appointments. Say goodbye to the busy signal!</p>
	                </div>
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{ asset('images/feature-icon/11.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
	                        </noscript>
	                        Custom Hold Message</h5>
	                    <p>Create your own music track or custom recording detailing info about your business; new products, special offers and more.</p>
	                </div>
	                 <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{ asset('images/feature-icon/18.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
	                        </noscript>
	                        Call Voice Tagging</h5>
	                    <p>Specify an audio message—a voice tag like “Sales” or “Support”—to identify callers based on the number or the extension they dialed.</p>
	                </div>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="green-bg see-all-features">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-12 text-center">
	                <h5>Looking for a specific feature? <a href="/features" title="See them all">See them all ➞.</a></h5>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="white-bg features-bottom-menu nopadding">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12 left-nav text-center">
				 <a href="/user-extension">
				 	<div class="arrow arrow-left">
				 		<i class="fa fa-angle-left"></i> 
				 	</div>
			 		<div class="nav-text">
			 			<h5>User Extensions</h5>
			 			<p>Give each employee their own User Extension and personalize their Greetings, Recordings, Call Routing, and more.</p> 
			 		</div> 
			 	</a> 
			 </div>
			 <div class="col-md-6 col-sm-6 col-xs-12 right-nav text-center"> 
			 	<a href="/adminlogin">
			 		<div class="nav-text">
			 			<h5>Administration</h5>
			 			<p>Our intuitive web interface lets you and your employees manage your Phone.com account from anywhere.</p> 
			 		</div> 
			 		<div class="arrow arrow-right">
			 			<i class="fa fa-angle-right"></i> 
			 		</div> 
			 	</a> 
			 </div> 
			</div> 
		</div> 
	</div>
	  @include('layouts.footer_slider')
	<div class="get-started-box">
		<div class="bubble bubble-one">
		</div>
		<div class="bubble bubble-two">
		</div>
		<div class="container">
			<div class="row">
				<div class="col-md-12 text-center">
					<h3>We're here to help.</h3>
					<h1><span style="color: #929292">Give us a call at </span><span class="highlight2" rel="nofollow" style="display: block;padding-top: 10px;"> <span style="font-weight: 500; font-size: 42px">1877-308-5803</span></span></h1>
					<div class="btn-feature col-md-3 col-sm-3">
						<center><a href="/package-pricing" title="Get Started Today" class="btn btn-default btn-xlg btm_btn">Get Started Today
					 	</a></center>
					</div>
					
				</div>
			</div>
		</div>
	</div>
@endsection
@section('scripts')
	<script>
		$('body').popover({
	    selector: '[rel=popover]'
		});
		$('.popover-dismiss').popover({
		  trigger: 'focus'
		});
		$('[data-toggle="popover"]').popover();
	</script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
	s<script type="text/javascript">
		$(document).ready(function(){
		    $('.customer-logos').slick({
		        slidesToShow: 6,
		        slidesToScroll: 1,
		        autoplay: true,
		        autoplaySpeed: 1500,
		        arrows: false,
		        dots: false,
		        pauseOnHover: false,
		        responsive: [{
		            breakpoint: 768,
		            settings: {
		                slidesToShow: 4
		            }
		        }, {
		            breakpoint: 520,
		            settings: {
		                slidesToShow: 3
		            }
		        }]
		    });
		});
	</script>
@endsection