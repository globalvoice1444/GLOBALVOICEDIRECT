@extends('layouts.app')
@section('content')
	<section class="header-wrap">
        <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
            <div class="container">
                <h2>Phone Number</h2>
            </div>
        </section>
        @include('includes.top_header')
        @include('includes.navbar')
        
    </section>
	<div class="container body_container">
		<div class="feature_body">
			<div class="feature-module">	
				<h2 class="features-heading">Pick a number.Any number.</h2>
				<p class="features-paragraph">We have phone numbers in your neighborhood and from around the world. Pick one that’s 
				<br> right for you.</p>
			</div>
		    <div class="tab-content" style="margin-top: 40px;">
		    	<div class="row">
					@include('layouts.feature_sidebar')
					<div class="col-md-9">			
					    <div class="row">

			                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 wow fadeInLeft" style="visibility: visible; animation-name: fadeInLeft;">

			                    <!-- Pricing Table 01 -->
			                    <div class="search-table">
			                       <div class="row">
				                       	<div class="number-type">
				                       		<div class="col-md-12 col-sm-12 col-xs-12"> 
				                       			<a class="rounded" href="#" onclick="">
				                       				<p class="text-uppercase">Local</p>
				                       				<p><i>SEARCH NOW</i></p>
				                       			</a> 
				                       		</div> 
				                       	</div> 
			                       </div>

			                       <div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
			    							<h5 class="green-title">
			        							<img src="{{ asset('images/feature-icon/28.png')}}"  width="32" height="32" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/LocalNumbers.png" data-was-processed="true">
			        								Local Numbers
			       							</h5>
			       							<p class="detail-one">
			       								Find phone numbers with any area code from across the U.S. Be your own version of David Blaine and give the illusion of being local, no matter where your business is actually located.
			       							</p>
									    </div>
									</div>

			                    </div>

			                </div>

			                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 wow fadeInUp" data-wow-duration="3s" style="visibility: visible; animation-duration: 3s; animation-name: fadeInUp;">

			                    <!-- Pricing Table 02 -->
			                    <div class="search-table riogrande">
			                       <div class="row">
				                       	<div class="number-types">
				                       		<div class="col-md-12 col-sm-12 col-xs-12"> 
				                       			<a class="roundeds" href="#" onclick="">
				                       				<p class="text-uppercase">Toll Free</p>
				                       				<p><i>SEARCH NOW</i></p>
				                       			</a> 
				                       		</div> 
				                       	</div> 
			                       </div>

			                       <div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
			    							<h5 class="blue-title">
			        							<img src="{{ asset('images/feature-icon/27.png')}}" width="32" height="32" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/LocalNumbers.png" data-was-processed="true">
			        								Toll Free
			       							</h5>
			       							<p class="paragraph-one">
			       								Give your business instant credibility and establish a bigger presence with a toll free number, and it doesn’t cost a cent more. Select 800, 888, 877, 866, 855, and 844.
			       							</p>
									    </div>
									</div>

			                    </div>
			                </div>

			                <div class="col-xs-12 col-sm-12 col-md-4 col-lg-4 col-xl-4 wow fadeInRight" style="visibility: visible; animation-name: fadeInRight;">

			                    <!-- Pricing Table 03 -->
			                    <div class="search-table">
			                       <div class="row">
				                       	<div class="number-type">
				                       		<div class="col-md-12 col-sm-12 col-xs-12"> 
				                       			<a class="round" href="#" onclick="">
				                       				<p class="text-uppercase">International</p>
				                       				<p><i>SEARCH NOW</i></p>
				                       			</a> 
				                       		</div> 
				                       	</div> 
			                       </div>

			                       <div class="row">
										<div class="col-md-12 col-sm-12 col-xs-12">
			    							<h5 class="inter-title">
			        							<img src="{{ asset('images/feature-icon/29.png')}}" width="32" height="32" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/LocalNumbers.png" data-was-processed="true">
			        								International
			       							</h5>
			       							<p class="inter-one">
			       								Make it easy for your global customers to reach you locally. Select a number from almost anywhere in the world. Merci.
			       							</p>
									    </div>
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
	        <h3 class="text-center">If your number is easy to remember, it's easier to call.</h3>
	        <p class="text-center">Custom phone numbers help customers remember and reinforce your product, service or
	        	slogan. <br>
	            Plus, it expands your brand visibility and increases marketing effectiveness. It’s a <br> 
	            win-win-win!</p>
	        <div class="row">
	            <div class="col-md-12"> 
	                <img src="{{ asset('images/feature-icon/35.png')}}" alt="1-800-Awesome" class="awesome800img lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/numbers2.png" data-was-processed="true"> 
	            </div>
	        </div>
	    </div>
    </div>
    <div class="features-facts">
	    <div class="container">
	        <div class="row">
	            <div class=" local col-md-6 col-sm-6 col-xs-12">
	                <h5 class="blue-title">
	                    <img src="{{ asset('images/feature-icon/36.png')}}" width="32" height="32" alt="Custom Local Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityLocalNumbers.png" data-was-processed="true">
	                    Custom Local Numbers</h5>
	                <p class="brief">Enhance your brand visibility with a memorable number that reinforces that you’re a local, trustworthy business.</p>
	            </div>
	            <div class=" toll col-md-6 col-sm-6 col-xs-12">
	                <h5 class="green-title">
	                    <img src="{{ asset('images/feature-icon/37.png')}}" width="32" height="32" alt="Custom Toll Free Numbers" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/VanityTollFreeNumbers.png" data-was-processed="true">
	                    Custom Toll Free Numbers
	                </h5>
	                <p class="brief">Establish a nationwide presence with an easy-to-remember number. Select 800, 888, 877, 866, 855 and 844. Reap the benefits and build your brand.</p>
	            </div>
	        </div> 
	    </div> 
	</div>
	<div class="white-bg keepnumber">
	    <div class="container">
	        <h3 class="text-center">Keep your number. And keep your business cards!</h3>
	        <h4 class="text-center">Get all the benefits of Globalvoicedirect.com with your existing phone number.</h4>
	        <div class="row">
	            <div class="col-md-12 text-center">
	                <img src="{{ asset('images/feature-icon/32.png')}}" alt="Keep your number" class="keepnumbering lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/NumberPortingHero.png" data-was-processed="true">
	                <noscript><img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/NumberPortingHero.png" alt="Keep your number" class="keepnumberimg"></noscript>
	            </div>
	        </div>
	        <div class="row">
	            <div class="col-md-6 col-sm-6 col-xs-12">
	                <h5 class="green-title">
	                    <img src="{{ asset('images/feature-icon/33.png')}}" width="32" height="32" alt="Number Porting" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/NumberPorting.png" data-was-processed="true">
	                    <noscript><img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/NumberPorting.png" width="32" height="32" alt="Number Porting" class="sm-icon"></noscript>
	                    Number Porting</h5>
	                <p>Already have a great number that your customers have memorized? We’ll guide you through the simple process of transferring your existing number to Globalvoicedirect.com.</p>
	            </div>
	            <div class="col-md-6 col-sm-6 col-xs-12">
	                <h5 class="green-title">
	                    <img src="{{ asset('images/feature-icon/34.png')}}" width="32" height="32" alt="Bulk Number Porting" class="sm-icon lazyloaded" data-lazy-src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/BulkNumberPorting.png')}}" data-was-processed="true">
	                    <noscript><img src="https://3macae71618368hq3ytkqe1c-wpengine.netdna-ssl.com/wp-content/themes/phone.com/images/phone-numbers/BulkNumberPorting.png" width="32" height="32" alt="Bulk Number Porting" class="sm-icon"></noscript>
	                    Bulk Number Porting</h5>
	                <p>Want to bring more than one number with you? No problem. We’ll have a bulk number party and port them all at once. It’ll be awesome.</p>
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
				 <a href="/feature">
				 	<div class="arrow arrow-left">
				 		<i class="fa fa-angle-left"></i> 
				 	</div>
			 		<div class="nav-text">
			 			<h5>All Features</h5>
			 			<p>Enjoy phone service that’s packed with features to help your company become more efficient, more mobile and more productive.</p> 
			 		</div> 
			 	</a> 
			 </div>
			 <div class="col-md-6 col-sm-6 col-xs-12 right-nav text-center"> 
			 	<a href="/user-extension">
			 		<div class="nav-text">
			 			<h5>User Extensions</h5>
			 			<p>Give each employee their own User Extension and personalize their Greetings, Recordings, Call Routing, and more.</p> 
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
@endsection