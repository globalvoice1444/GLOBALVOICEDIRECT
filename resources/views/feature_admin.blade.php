@extends('layouts.app')
@section('content')
	<section class="header-wrap">
    <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
        <div class="container">
            <h2>Admin Features</h2>
        </div>
	</section>
   @include('includes.top_header')
   @include('includes.navbar')
    </section>
	<div class="container body_container">

		<div class="feature_body">
				<div class="feature-module">	
					<h2 class="features-heading">It may be our platform, but you’re in control.</h2>
					<p class="features-paragraph">Though our features are powerful, we’ve designed them to be simple and easy to use. <br>
					 But when in doubt, Globalvoicedirect.com is always here to help.</p>
				</div>
		    <div class="grays-bgs promusic">
	    <div class="container">
	        <div class="row">
	        	@include('layouts.feature_sidebar')
	        	<div class="col-md-9">
	        		<div class="row">
			            <div class="col-md-5 col-sm-5 col-xs-12 text-center"> 
			                <img src="{{asset('images/feature-icon/45.png')}}" alt="Give your customers something worth listening to" class="promusicimgs lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" data-was-processed="true">
			                <noscript>
			                    <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" alt="Give your customers something worth listening to" class="promusicimg">
			                </noscript>
			            </div>
			            <div class="col-md-7 col-sm-7 col-xs-12">
			                <div class="col-md-12">
			                    <h5 class="green-titles">
			                        <img src="{{asset('images/feature-icon/21.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png" data-was-processed="true">
			                        <noscript>
			                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon">
			                        </noscript>
			                        Intuitive Web Interfaces</h5>
			                    <p>Our user-friendly design lets you configure and manage more than 50 Globalvoicedirect.com features with a few simple clicks. After all, we haven’t forgotten you’ve got a business to run.</p>
			                </div>
			                <div class="col-md-12">
			                    <h5 class="green-titles">
			                        <img src="{{asset('images/feature-icon/22.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
			                        <noscript>
			                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
			                        </noscript>
			                        Instant Provisioning</h5>
			                    <p>Sign up and you’re ready to work. Even with new phone numbers. In fact, it’ll take you longer to throw out the packaging than it does to be up and running with our ring-ready phones.</p>
			                </div>
			                 <div class="col-md-12">
			                    <h5 class="green-titles">
			                        <img src="{{asset('images/feature-icon/20.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
			                        <noscript>
			                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
			                        </noscript>
			                        Phone Support</h5>
			                    <p>Not that you’ll need our help, but if you do, our dedicated staff is available 365 days a year to assist. Our customer service agents are always ready to support all your configuration needs. We’ve got your back!</p>
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
	        <h3 class="text-center">Adding numbers and extensions with us is touch and go. In a good way.</h3>
	        <p class="text-center">As your business grows, Globalvoicedirect.com can grow right along with you. It only takes a few seconds to <br>
	         be up and running. Helping you grow is in our DNA.</p>
	        <div class="row">
	            <div class="col-md-12"> 
	                <img src="{{asset('images/feature-icon/46.png')}}" alt="1-800-Awesome" class="awesome800img lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/numbers2.png" data-was-processed="true"> 
	            </div>
	        </div>
	    </div>
    </div>
    <div class="features-facts">
	    <div class="container">
	        <div class="row">
	            <div class="schedule col-md-6 col-sm-6 col-xs-12">
	                <h5 class="blue-title">
	                    <img src="{{asset('images/feature-icon/26.png')}}" width="32" height="32" alt="Custom Local Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityLocalNumbers.png" data-was-processed="true">
	                    Add Phone Numbers</h5>
	                <p class="brief">Add numbers with the click of a button. Local numbers. Custom numbers. Any kind of number. Perfect for tracking your next marketing campaign with a unique number.</p>
	            </div>
	            <div class="find col-md-6 col-sm-6 col-xs-12">
	                <h5 class="green-title">
	                    <img src="{{asset('images/feature-icon/16.1.png')}}" width="32" height="32" alt="Custom Toll Free Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityTollFreeNumbers.png" data-was-processed="true">
	                    Add User Extensions
	                </h5>
	                <p class="brief">With just a few clicks, you can create, edit, add or remove extensions whenever you want. So easy, even the intern can do it.</p>
	            </div>
	        </div> 
	    </div> 
	</div>

    <div class="grays-bg promusic">
	    <div class="container">
	        <div class="row">
	        	<div class="col-md-12">
	        		<h3 class="text-center">Gain new insight into your customers and employee call history.</h3>
	                <p class="text-center">Want to improve efficiency and productivity? We thought so. Dig in to a wealth of intel <br> 
	                about your customers’ calling behavior.</p>
	            </div>

	            <div class="col-md-7 col-sm-7 col-xs-12">
	              <div class="row">
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{asset('images/feature-icon/23.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon">
	                        </noscript>
	                        Caller Analytics</h5>
	                    <p>Analyze calls and gain insight to your callers' call patterns. See caller breakdown, demographics, social profile and mobile provider.</p>
	                </div>
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{asset('images/feature-icon/2.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
	                        </noscript>
	                        Call Recordings</h5>
	                    <p>Monitor, review and save calls to your account in order to improve performance and enhance customer service capabilities. We store voicemails for an industry-leading 13 months!</p>
	                </div>
	              </div>  
	            </div>
	            <div class="col-md-5 col-sm-5 col-xs-12 text-center"> 
	                <img src="{{asset('images/feature-icon/47.png')}}" alt="Give your customers something worth listening to" class="promusicimgs lazyloadeds" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" data-was-processed="true">
	                <noscript>
	                    <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" alt="Give your customers something worth listening to" class="promusicimg">
	                </noscript>
	            </div>
	        </div>
	    </div>
    </div>

    <div class="green-bg see-all-features">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-12 text-center">
	                <h5>Looking for a specific feature? <a href="feature" title="See them all">See them all ➞.</a></h5>
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