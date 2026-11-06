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
				<h2 class="features-heading">We work where you work!</h2>
				<p class="features-paragraph">Enable your smartphone to access, manage and become your Globalvoicedirect.com extension. Just  <br>
				 download Global Voice for iOS or Android and you'll be connected 24/7.</p>
			</div>
			<div class="white-bg keepnumbers">
		        <div class="container">
		            <div class="row">
		            	@include('layouts.feature_sidebar')
		            	<div class="col-md-9">
		            		<div class="row">
				            	<div class="col-md-6 col-sm-6 col-xs-12 text-center iphonecol">
					            	 <img src="{{asset('images/feature-icon/52.png')}}" alt="Turn your mobile phone into a workstation" class="phoneapp lazyloaded"  data-was-processed="true">
					            	 <noscript>
					            	 	<img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/mobileiphone@2x.png"  alt="Turn your mobile phone into a workstation" class="phoneapp"> 
					            	 </noscript> 
				            	</div>
				            	<div class="col-md-6 col-sm-6 col-xs-12 text-center"> 
				            		<img src="{{asset('images/feature-icon/51.png')}}" alt="Turn your mobile phone into a workstation" class="phoneapp lazyloaded"  data-was-processed="true">
				            		<noscript>
				            			<img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/mobileandroid@2x.png"  alt="Turn your mobile phone into a workstation" class="phoneapp"> 
				            		</noscript> 
				            	</div> 
				            </div>
			            </div>
		            </div>
	        
	       			<div class="row numbs">
            
		                <div class=" iphones col-md-4 col-sm-6 col-xs-12">
		                    <p class="iphone">
	                           <a class="link" href="#"><img data-was-processed="true" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/IosApp.png" class="sm-icon lazyloaded" alt="Phone.com for iOS" height="32" width="30" src="{{asset('images/feature-icon/30.png')}}">
	                           Globalvoicedirect.com <br>
	                            for iOS</a>
	                        </p>

		                    <p class="para">Turn your iPhone into your Globalvoicedirect.com extension. Call using your mobile carrier, 3G/4G, or WiFi. You decide. Other features include SMS, FAX, and more.</p>

	                        <p class="icon">
	                           <a href="#" target="_blank" rel="nofollow">
			                           <img src="{{asset('images/feature-icon/50.png')}}" alt="Available on the App Store" class="appstore lazyloaded"  data-was-processed="true" assets="" img="" feature-icon="">
						       		<noscript>
						               <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/appstore@2x.png"  
						               alt="Available on the App Store" class="appstore">
						            </noscript> 
						        </a>
	                        </p>
		                </div>
		                <div class=" iphones col-md-4 col-sm-6 col-xs-12">
		                    <p class="iphone">
		                    	<a  class="link" href="#">
		                    		<img src="{{asset('images/feature-icon/31.png')}}" width="30" height="32" alt="Phone.com for iOS" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/androidman@2x.png" data-was-processed="true">
		                    		<noscript>
		                    			<img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/androidman@2x.png')}}" width="32" height="32" alt="Phone.com for iOS" class="sm-icon"> 
		                    		</noscript> Globalvoicedirect.com <br>
		                    		 for Android 
		                    	</a> 
		                    </p>

		                    <p class="para">Android user? We have you covered. Download Globalvoicedirect.com for Android and turn your smartphone into a fully integrated extension with your Global Voice service.</p>

	                        <p class="icon">
	                           <a href="#" target="_blank" rel="nofollow">
	                                <img src="{{asset('images/feature-icon/49.png')}}" alt="Available on the App Store" class="appstore lazyloaded" data-was-processed="true" assets="" img="" feature-icon="">
	                              <noscript>
	                                <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/mobile/appstore@2x.png"  
	                                alt="Available on the App Store" class="appstore">
	                              </noscript> 
	                           </a>
	                        </p>
		                </div>
            		</div>
	   			</div>
			</div>
    	</div>
	</div>  


    <div class="grays-bg promusic">
	    <div class="container">
	        <div class="row">
	        	<div class="col-md-12">
	        		<h3 class="text-center">A phone system that goes wherever you go.</h3>
	                <p class="text-center">Missing important phone calls just because you’re away from your desk? You may want to <br> 
	                donate your bell bottoms and join the 21st century.</p>
	            </div>

	            <div class="col-md-7 col-sm-7 col-xs-12">
	              <div class="row">
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{asset('images/feature-icon/15.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/ProfessionalRecording.png')}}" width="32" height="32" alt="Professional Recordings" class="sm-icon">
	                        </noscript>
	                        Forwarding</h5>
	                    <p>Forward calls to single or multiple phone numbers, including your cell, home, office, or anything else that rings. No internet connection required.</p>
	                </div>
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{asset('images/feature-icon/24.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
	                        </noscript>
	                        Softphone</h5>
	                    <p>Transform your laptop or tablet into a feature-rich web-based phone as your Globalvoicedirect.com extension. Make, take and transfer calls and more.</p>
	                </div>
	                <div class="col-md-12">
	                    <h5 class="green-titles">
	                        <img src="{{asset('images/feature-icon/18.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png" data-was-processed="true">
	                        <noscript>
	                            <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/HoldMusic.png')}}" width="32" height="32" alt="Premium Hold Music" class="sm-icon">
	                        </noscript>
	                        Mobile Apps</h5>
	                    <p>Download and run phone.com directly on your smart phone. Use your data connection or Wi-Fi on an iPhone to make and receive business calls when cell service is lacking.</p>
	                </div>
	              </div>  
	            </div>
	            <div class="col-md-5 col-sm-5 col-xs-12 text-center"> 
	                <img src="{{asset('images/feature-icon/48.png')}}" alt="Give your customers something worth listening to" class="promusicimgs lazysloadeds" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png" data-was-processed="true">
	                <noscript>
	                    <img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/user-extensions/music-image.png')}}" alt="Give your customers something worth listening to" class="promusicimg">
	                </noscript>
	            </div>
	        </div>
	    </div>
    </div>

    <div class="green-bg see-all-features">
	    <div class="container">
	        <div class="row">
	            <div class="col-md-12 text-center">
	                <h5>Looking for a specific feature? <a href="/feature" title="See them all">See them all ➞.</a></h5>
	            </div>
	        </div>
	    </div>
	</div>

	<div class="white-bg features-bottom-menu nopadding">
		<div class="container">
			<div class="row">
				<div class="col-md-6 col-sm-6 col-xs-12 left-nav text-center">
				 <a href="/adminlogin">
				 	<div class="arrow arrow-left">
				 		<i class="fa fa-angle-left"></i> 
				 	</div>
			 		<div class="nav-text">
			 			<h5>Administration</h5>
			 			<p>Our intuitive web interface lets you and your employees manage your Globalvoicedirect.com account from anywhere.</p> 
			 		</div> 
			 	</a> 
			 </div>
			 <div class="col-md-6 col-sm-6 col-xs-12 right-nav text-center"> 
			 	<a href="/feature">
			 		<div class="nav-text">
			 			<h5>All Features</h5>
			 			<p>Enjoy phone service that’s packed with features to help your company become more efficient, more mobile and more productive.</p> 
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