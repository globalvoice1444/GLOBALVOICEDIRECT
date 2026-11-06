@extends('layouts.app')
@section('content')
<section class="header-wrap">
    <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
        <div class="container">
            <h2>Non Member Referral</h2>
        </div>
    </section>
    @include('includes.top_header')
    @include('includes.navbar')
    
</section>
<style type="text/css"></style>
<section class="about">
    <div class="container">
        <div class="row offer-response">
            <div class="col-lg-12 col-md-12 col-sm-12">
                <div class="left wt-small-separator-outer">
                    <div class="products contactus-page affiliate-page">
						<div class="container">
				           <?php if(isset($_GET['error'])) { ?>
								<div class="alert alert-danger">
								  <?php echo $_GET['error']; ?>
								</div>
							<?php }if(isset($_GET['message'])){ ?>
								<div class="alert alert-success">
								  <?php echo $_GET['message']; ?>
								</div>
							<?php } ?>
							@if(session()->has('error'))
								<div class="alert alert-danger">
								  {{session()->get('error')}}
								</div>
							@endif
							<div class="row sub-container-contact-us">
								<div class="col-md-8">
									<h3>Referral Program</h3>
									<p>We are happy to meet with you face to face or liaise with you via the phone or email to provide a service that's right for you. Let us walk you through configuring your PBX phone system the way it works best for your business!</p>
									<div class="contact-us-image-container">
										<img src="{{ asset('images/p-clash-mayhem-tv-vintage-vinyl-house.jpg') }}" alt="Get set up in no time at all" class="img-responsive" itemprop="image">
									</div>
									<div class="check-referral">
										<div class="affiliate-header m-2">
											<h4>Affiliate Dashboard</h4>
										</div>
										<div class="affiliate-check">
											<form action="affiliate-dashboard" method="POST">
												{{csrf_field()}}
												<div class="form-inline">
													<div class="form-group affiliate-form">
														<label>Email</label>
														<input type="email" name="email" class="form-control m-2" id="email_referral">
														@if (is_array($errors) && $errors->has('email'))
															<div class="alert alert-danger">{{$errors->first('email')}}</div>
														@endif
													</div>
													<button class="our-product button_slide slide_right activate fadeInUp" type="submit">Check</button>
												</div>
											</form>
										</div>
									</div>
								</div>
								<div class="col-md-4">
									<div class="panel panel-default right-affiliate-form">
										<div class="panel-heading contact-panel-heading">Get Referal Link</div>
										<div class="panel-body">
											<form id="non-memeber-referal" onsubmit="nonmemeberreferal();return false;">
												{{csrf_field()}}
												<p>Get free stuff if your referral becomes our customer.</p>
												<div class="form-group">
													<!-- <label>Your Name (required)</label> -->
													<input type="text" name="name" value="" placeholder="Your Name" required class="form-control">
												</div>
												<div class="form-group">
													<!-- <label>Your Email (required)</label> -->
													<input type="email" name="email" value="" placeholder="Your Email" required class="form-control">
												</div>
												<div class="form-group">
													<!-- <label>Subject</label> -->
													<input type="text" name="phone" value="" placeholder="Your Phone" class="form-control">
												</div>
												 
												<!-- <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div> -->

												<a href="javaScript:;" id="displaygetreferalform" onclick="displaygetreferalform()">Click Here if you already registered.</a>

												<div class="form-group">
													<button type="submit" class="our-product button_slide slide_right activate fadeInUp">Submit</button>
												</div>
											</form>
											<form id="non-memeber-referal-link" style="display: none" onsubmit="getnonmemeberreferal();return false;">
												{{csrf_field()}}
												<p>Get free stuff if your referral becomes our customer.</p>
												 
												<div class="form-group">
													<!-- <label>Your Email (required)</label> -->
													<input type="email" name="email" value="" placeholder="Your Email" required class="form-control">
												</div>
												 
												<!-- <div class="g-recaptcha" data-sitekey="{{ env('RECAPTCHA_SITE_KEY') }}"></div> -->
												<a href="javaScript:;" id="displaynewreferalform" onclick="displaynewreferalform()">Click Here for Register new Email.</a>
												<div class="form-group">
													<button type="submit" class="our-product button_slide slide_right activate fadeInUp">Submit</button>
												</div>
											</form>

											<div class="form-group">
												<!-- <label>Subject</label> -->
												<label>Referal Link</label>
												<textarea placeholder="You will get referal link here after form submission" class="form-control" id='refrel-link'></textarea>
											</div>
											<div class="form-group">
												<button onclick="copyclipboardlink()" class="our-product button_slide slide_right activate fadeInUp">Copy</button>
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
    </div>
</section>
@endsection