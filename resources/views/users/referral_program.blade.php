@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
<style>
	.referral-text {
	    margin: 15% 0% 10% 10%;
	}
	.referral-h1 {
		text-transform: capitalize;
		text-align: inherit;
		margin: 38px 0px;
		color: #3f4d67;
		font-style: italic;
		font-weight: 800;
    }
    .referral-h1 span {
        font-size: 58px;
        line-height: 70px;
    }
    .referral-text .referral-input {
        width: unset;
        height: 65px;
        border-radius: 8px;
        padding: 10px;
        border: 3px solid #7db4db;
        box-shadow: 0px 0px 14px 2px #f2f2f2;
        font-size: 20px;
        color: #61737f;
        font-weight: 900;
    }
    .referral-copy-btn {
        padding: 9px;
        height: 64px;
        width: 89px;
        border: 3px solid #3f4d67;
        background-color: #3f4d67;
        border-radius: 9px;
        margin-left: 10px;
        transform: translate(2px, 0);
        box-shadow: 3px 0px 4px 4px #f4f4f4;
        font-weight: bolder;
        color: #fff;
    }
    .referral-copy-btn:active, .referral-copy-btn:focus, .referral-copy-btn:hover {
        box-shadow: none;
        transform: translate(2px, 0);
        margin-left: 10px;
        outline: none !important;
        border: 3px solid #7dd2f9;
        background-color: #7dd2f9;
    }
    .referral-image img {
        width: 500px;
    }
    .tw-share-btn {
        margin: 0px 28px;
    }
    .insta-share-btn {
        margin-left: 45px;
    }
    .social-btn {
        display: flex;
        margin-left: 50px;
    }
    .insta-share-btn span {
        height: 28px;
        font-size: 12px !important;
        border-radius: 4px !important;
        padding: 4px 10px 1px 9px !important;
    }
    .referral-heading {
        text-align: center;
        font-style: italic;
        color: #3f4d67;
        font-weight: 700;
        padding: 50px;
    }
    .referral-box.round-border .icon {
        width: 70px;
        height: 70px;
        float: left;
        margin-right: 20px;
        text-align: center;
        border-radius: 50%;
        border: 1px solid #f6f6f6;
        transition: all 0.5s ease-in-out;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
        -o-transition: all 0.5s ease-in-out;
    }
    .referral-box.round-border .icon i {
        font-size: 20px;
        background: #f6f6f6;
        color: #999999;
        display: block;
        border-radius: 50%;
        margin: 9px 10px 10px 9px;
        width: 50px;
        height: 50px;
        padding: 15px 12px;
        transition: all 0.5s ease-in-out;
        -webkit-transition: all 0.5s ease-in-out;
        -moz-transition: all 0.5s ease-in-out;
        -ms-transition: all 0.5s ease-in-out;
    }
    .referral-box.round-border.box-hover:hover .icon, .referral-box.round-border.box-hover:hover .icon i {
        border-color: #7dd2f9;
    }
    .referral-box.round-border.box-hover:hover .icon i{
        background: #7dd2f9;
        color: #ffffff;
    }
    .referral-box.round-border.box-hover {
        margin: 40px 20px;
        display: grid;
    }
    .content {
        margin-top: 15px;
    }
    .content h6 {
        font-size: 22px;
    }
    .referral-step {
        margin-bottom: 50px
    }
    @media (max-width: 1024px) {
    	.referral-text .referral-input {
    	    width: 165px;
    	}
    	.referral-image img {
    	    width: 310px;
    	}
    }
    @media (max-width: 768px) {
    	.referral-text .referral-input {
    	    width: 165px;
    	}
    	.referral-image img {
    	    width: 310px;
    	}
    }
    @media (max-width: 480px) {
    	.referral-text .referral-input {
    	    width: 160px;
    	}
    	.social-btn {
    	    display: flex;
    	    margin-left: 33px;
    	}
    	.tw-share-btn {
    	    margin: 0px -18px;
    	}
    	.insta-share-btn {
    	    margin-left: 0px;
    	}
    	.referral-image img {
    	    width: 310px;
    	}
    }
    @media (max-width: 414px) {
    	.referral-text .referral-input {
    	    width: 200px;
    	}
    	.social-btn {
    	    display: flex;
    	    margin-left: 33px;
    	}
    	.tw-share-btn {
    	    margin: 0px -18px;
    	}
    	.insta-share-btn {
    	    margin-left: 0px;
    	}
    	.referral-image img {
    	    width: 368px;
    	}
    }
    @media (max-width: 411px) {
    	.referral-text .referral-input {
    	    width: 200px;
    	}
    	.social-btn {
    	    display: flex;
    	    margin-left: 33px;
    	}
    	.tw-share-btn {
    	    margin: 0px -18px;
    	}
    	.insta-share-btn {
    	    margin-left: 0px;
    	}
    	.referral-image img {
    	    width: 368px;
    	}
    }
    @media (max-width: 375px) {
    	.referral-text .referral-input {
    	    width: 175px;
    	}
    	.social-btn {
    	    display: flex;
    	    margin-left: 33px;
    	}
    	.tw-share-btn {
    	    margin: 0px -18px;
    	}
    	.insta-share-btn {
    	    margin-left: 0px;
    	}
    	.referral-image img {
    	    width: 325px;
    	}
    }
    @media (max-width: 320px) {
        .referral-text .referral-input {
            width: 128px;
        }
        .referral-image img {
            width: 276px;
        }
        .social-btn {
            display: flex;
            margin-left: 10px;
        }
    }
</style>
<div id="fb-root"></div>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
		<div class="referal-container">
		      <div class="heading">
			  <div class="card-block" id="profile_card">
		          <div class="row">
		              <div class="col-md-12">
		                  <div id="wizard">
			                  <section>
		                          <div class="row">
		                        	<div class="col-md-6">
			                           <div class="referral-text">
			                           	    <h1 class="referral-h1">refer a friend <br><span> to earn credit</span></h1>
			                        		<div class="inline-form">
			                        			<input type="text" name="referral" class="referral-input" id="referrer_url" value="<?php echo $referrer_url ?>">
			                        			<button class="referral-copy-btn" onclick="clipboard()">Copy</button>
			                        		</div>
			                           </div>
			                           <div class="row">
				                           	<div class="social-btn">
				                        		<div class="col-md-2">
					                        		<div class="fb-share-button" data-href="{{ $referrer_url }}" data-layout="button_count" data-size="large"><a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fdevelopers.facebook.com%2Fdocs%2Fplugins%2F&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Share</a></div>
					                        	</div>
					                        	<div class="col-md-2">
					                        		<div class="tw-share-btn">
					                        			<a href="https://twitter.com/share?ref_src=twsrc%5Etfw" class="twitter-share-button" data-size="large" data-text="{{ $referrer_url }}" data-show-count="true">Tweet</a>
					                        		</div>
					                        	</div>
					                        	<div class="col-md-2">
					                        		<div class="insta-share-btn">
					                        			<a class="pinterest-btn" href="https://www.pinterest.com/pin/create/button/" data-pin-do="buttonBookmark"></a>
					                        		</div>
					                        	</div>
					                        </div>
			                           </div>
			                        </div>
			                        <div class="col-md-6">
			                        	 <div class="referral-image">
				                        	<img src="/public/images/referral2.png" width="600">
				                         </div>
			                        </div>
		                          </div>
			                  </section>
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
@endsection
@section('scripts')
<script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>
<script async defer crossorigin="anonymous" src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v5.0&appId=2460895607563001&autoLogAppEvents=1"></script>
<script
    type="text/javascript"
    async defer
    src="//assets.pinterest.com/js/pinit.js"
></script>
<script type="text/javascript">
	  var breadcrumb = $('.breadcrumb');
            breadcrumb.append('<li class="breadcrumb-item"><b><span>Referral Program</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="/user/referral_program"]').parents().addClass('active');


    function clipboard() {
	  /* Get the text field */
	  var copyText = document.getElementById("referrer_url");

	  /* Select the text field */
	  copyText.select();
	  copyText.setSelectionRange(0, 99999); /*For mobile devices*/

	  /* Copy the text inside the text field */
	  document.execCommand("copy");

	  /* Alert the copied text */
	  $('.referral-copy-btn').text('Copied!');
	}
</script>
@endsection