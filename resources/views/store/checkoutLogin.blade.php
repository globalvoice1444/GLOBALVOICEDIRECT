@extends('layouts.app')
@section('content')
<section class="header-wrap">
  <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
      <div class="container">
          <h2>Checkout Options</h2>
      </div>
  </section>
  @include('includes.top_header')
  @include('includes.navbar')
</section>
	<div class="container checkout-options">
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<h3>Checkout Options</h3>
			</div>
		</div>
		<div class="row">
			<div class="col-lg-12 col-md-12 col-sm-12">
				<div class="panel panel-default">
					<div class="panel-body text-center">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="option-title text-left">
									<h3>Submit Credit Application</h3>
								</div>
								<div class="option-body text-left">
									<p>
										Haven't applied for a Membership yet? What are you waiting on? With our unique business model designed in place, Exclusive -Membership-with financing option. Special Discounts and Freebies.
									</p>
								</div>
								<a href="/credit-application" title="" class="button_slide slide_right btn-custom">Apply Now</a>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-12">
								<div class="option-title text-left">
									<h3>Member Please Login</h3>
								</div>
								<div class="option-body text-left">
									<p>
										Already have a Membership account?. Login to your account now to finalize your shopping cart.
									</p>
									<form action="/checkout" method="POST" class="text-center checkout-form">
										{{csrf_field()}}
										<div class="row">
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="form-group text-left">
													<label>User Name:</label>
													<input type="text" name="username" class="form-control">
												</div>
											</div>
											<div class="col-lg-6 col-md-6 col-sm-12">
												<div class="form-group text-left">
													<label>Password:</label>
													<input type="password" name="password" class="form-control">
												</div>
											</div>
										</div>
										<button type="submit" class="button_slide slide_right btn-custom">Login</button>
									</form>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
@endsection