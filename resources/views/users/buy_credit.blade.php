@extends('layouts.user.user_main')
@section('content')
<link rel="stylesheet" href="/public/css/credit-form/themify-icons.css">
<link rel="stylesheet" href="/public/css/credit-form/style.css">


<div class="col-sm-12">
	<div class="card">
		<div class="card-header">
			<h5>Buy Credit</h5>
		</div>
		
		<div class="card-block" id="add_bulk_card">
			<div class="row">
				<div class="col-md-12">
					<div id="wizard">
						<section>
							<div class="main">
								<div class="container">
									<?php if(isset( $_GET['message'])){ ?>

							            <div class="alert alert-info">

							              <strong>Info!</strong> <?php echo $_GET['message']; ?>.

							            </div>

							        <?php } ?>
									<h2>Buy Credit</h2>
									<form method="POST" id="signup-form" action="/smsCreditAction" name="Individual-contacts" class="signup-form">
										{{csrf_field()}}
										<h3>
											<span class="icon"><i class="fa fa-user"></i></span>
											<span class="title_text">Personal</span>
										</h3>
										<fieldset>
											<legend>
												<span class="step-heading">Personal Informaltion: </span>
												<span class="step-number">Step 1 / 3</span>
											</legend>
											<div class="form-group">
												<label for="firstname" class="form-label required">First name</label>
												<input type="text" name="firstname" id="first_name" />
											</div>
											<div class="form-group">
												<label for="lastname" class="form-label required">Last name</label>
												<input type="text" name="lastname" id="last_name" />
											</div>
											<div class="form-group">
												<label for="email" class="form-label required">Email</label>
												<input type="email" name="email" id="email" />
											</div>
											<div class="form-group">
												<label for="phoneNumber" class="form-label required">Phone Number</label>
												<input type="text" name="phoneNumber" id="phone" />
											</div>
											<div class="form-group">
												<label for="companyName" class="form-label required">Company Name</label>
												<input type="text" name="companyName" id="company" />
											</div>
										</fieldset>
										<h3>
											<span class="icon"><i class="fa fa-money-bill-alt"></i></span>
											<span class="title_text">Amount</span>
										</h3>
										<fieldset>
											<legend>
												<span class="step-heading">credit Amount: </span>
												<span class="step-number">Step 2 / 3</span>
											</legend>
											<div class="form-group">
												<label for="amount" class="form-label required">Amount</label>
												<input type="number" name="credit_amout" id="card_number" />
												<p style="color: #ff1212;">Minimum Amount = $20 <br> Note: 1 credit = 2.5 Cent</p>
											</div>
										</fieldset>
										<h3>
											<span class="icon"><i class="fa fa-credit-card"></i></span>
											<span class="title_text">Payment</span>
										</h3>
										<fieldset>
											<legend>
												<span class="step-heading">Payment Informaltion: </span>
												<span class="step-number">Step 3 / 3</span>
											</legend>

											<div class="form-group">
												<label for="holder_name" class="form-label required">Holder Name</label>
												<input type="text" name="holder_name" id="holder_name" />
											</div>
											<div class="form-group">
												<label for="cardNumber" class="form-label required">Card Number</label>
												<input maxlength="16" type="tel" class="form-control" name="cardNumber" placeholder="Valid Card Number" autocomplete="cc-number" required="" autofocus="" required />
												 
											</div>
											<div class="form-row">
												<div class="form-date">
													<label for="expiry_date" class="form-label">Expiry Date</label>
													<div class="form-date-group">
														<div class="form-date-item">
														<select id="expiry_month" name="month"></select>
														<span class="select-icon"><i class="fa fa-angle-down"></i></span>
														</div>
														<div class="form-date-item">
														<select id="expiry_year" name="year"></select>
														<span class="select-icon"><i class="fa fa-angle-down"></i></span>
														</div>
													</div>
												</div>
												 
											</div>
											
											<div class="form-row">
												<div class="form-group">
													<label for="cardCVC" class="form-label required">CVC</label>
													<input maxlength="3" type="tel" class="form-control" name="cardCVC" placeholder="CVC" autocomplete="cc-csc" required id="cvc">
												</div>
											</div>
												
										</fieldset>
									</form>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>


<script src="/public/js/credit-form/jquery.min.js"></script>
<script src="/public/js/credit-form/jquery.validate.min.js"></script>
<script src="/public/js/credit-form/additional.min.js"></script>
<script src="/public/js/credit-form/jquery.steps.min.js"></script>
<script src="/public/js/credit-form/dobpicker.js"></script>
<script src="/public/js/credit-form/main.js"></script>

<script async src="https://www.googletagmanager.com/gtag/js?id=UA-23581568-13"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-23581568-13');
</script>
@endsection
@push('scripts')
<script type="text/javascript">
    $(document).ready( function () {
    	$('.actions').find('ul li:last-child').find('a').attr('onclick','submitForm()');
    	var breadcrumb = $('.breadcrumb');
        breadcrumb.append('<a href="smscampaign" class="breadcrumb-item"><span>SMS Campaign</a></li><li class="breadcrumb-item"><b><span>Buy Credit</span></b></li>');
    	
        $('#signup-form').removeClass('wizard');
    } );
    function submitForm(){
    	$('#signup-form').submit();
    }
</script>
@endpush


