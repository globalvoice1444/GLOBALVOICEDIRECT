@extends('layouts.app_old')
@section('css')
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KDJWX9X9');</script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/gvd-checkout-skin.css?v=20261106">
<style>.main>header:not(.gvd-hdr),.top-navbar,body>footer{display:none!important}.gvdck .gvd-sumcol{order:2}@media(min-width:992px){.gvdck .credit-app-row{flex-wrap:nowrap;align-items:flex-start}}body{background:#F5F7FA}.gvdck{background:#F5F7FA!important;padding:0!important}.gvdck .wizard{box-shadow:0 18px 50px rgba(15,27,42,.10)!important;border-radius:18px}.gvdck .container.wrapper{margin-top:-60px;position:relative;z-index:2;padding-bottom:56px}.gvdck .gvd-sum{margin-bottom:20px}@media(max-width:991px){.gvdck .credit-app-row{flex-direction:column;align-items:center}}</style>
<style>
.gvdck{--ink:#1E2A36;--mut:#5B6875;--o:#FF5000;--o2:#C23A00;--b:#3BA9C8;--b2:#1F6F87;--line:#E6ECF1;font-family:"Plus Jakarta Sans",system-ui,sans-serif;color:var(--ink);background:linear-gradient(180deg,#F3F9FC 0,#fff 520px)!important;padding:0 0 50px}
.gvdck .checkout-overlay{display:none}
.gvdck-top{display:flex;justify-content:space-between;align-items:center;padding:14px 5vw;border-bottom:1px solid var(--line);background:#fff;margin-bottom:28px}
.gvdck-top img{height:38px}.gvdck-top .sec{font-size:13px;color:var(--mut)}
.gvdck-hero{text-align:center;margin:0 auto 22px;max-width:760px;padding:0 16px}
.gvdck-hero .eb{font:600 11px "JetBrains Mono",monospace;letter-spacing:.18em;color:var(--o);text-transform:uppercase}
.gvdck-hero h1{font-size:clamp(26px,4vw,40px);letter-spacing:-.03em;margin:8px 0 6px;font-weight:800;color:var(--ink)}.gvdck-hero h1 em{font-style:normal;color:var(--o)}
.gvdck-hero p{color:var(--mut);margin:0}
.gvdck .wizard{background:#fff!important;border:1px solid var(--line);border-radius:20px;box-shadow:0 20px 50px rgba(30,42,54,.07);overflow:hidden;margin:0 auto}
.gvdck .wizard-inner{padding:18px 18px 4px}.gvdck .connecting-line{display:none}
.gvdck .nav-tabs{display:grid!important;grid-template-columns:repeat(3,1fr);gap:8px;border:0!important;margin:0}
.gvdck .nav-tabs>li{float:none!important;width:auto!important;margin:0!important}
.gvdck .nav-tabs>li>a{display:flex!important;align-items:center;gap:10px;width:auto!important;height:auto!important;margin:0!important;padding:12px 14px!important;border-radius:14px!important;border:1px solid var(--line)!important;background:#fff!important;font-weight:700;font-size:14px;color:var(--ink)!important}
.gvdck .nav-tabs>li>a:after{content:attr(title)}
.gvdck .round-tab{position:static!important;width:30px!important;height:30px!important;line-height:28px!important;border-radius:50%!important;background:#EEF3F6!important;border:0!important;color:var(--mut)!important;font-size:13px!important;display:inline-grid!important;place-items:center;flex:none}
.gvdck .round-tab i{color:inherit!important;position:static!important;margin:0!important;font-size:13px!important;line-height:1!important}
.gvdck .nav-tabs:before,.gvdck .nav-tabs:after{display:none!important;content:none!important}
.gvdck .table thead th,.gvdck table th{background:#1E2A36!important;color:#fff!important;border:0!important}
.gvdck-hero{padding-top:28px!important}
.gvdck .nav-tabs>li.active>a{border-color:var(--o)!important;box-shadow:0 8px 24px rgba(255,80,0,.14)}
.gvdck .nav-tabs>li.active .round-tab{background:var(--o)!important;color:#fff!important}
.gvdck .nav-tabs>li.disabled>a{opacity:.75}
.gvdck .wizard-title-bar{display:none}
.gvdck .wizard-body{padding:10px 28px 18px}
.gvdck .wizard-body h2{font-size:21px;font-weight:800;text-align:left!important;margin:14px 0 14px;color:var(--ink)}
.gvdck label{font-size:12px;font-weight:700;color:var(--mut);text-transform:uppercase;letter-spacing:.04em}
.gvdck .form-control,.gvdck select,.gvdck #card-element{height:auto;min-height:46px;padding:12px 14px;border:1.5px solid var(--line);border-radius:12px;background:#FBFDFE;box-shadow:none;font-size:15px}
.gvdck .form-control:focus{border-color:var(--b);box-shadow:0 0 0 4px rgba(59,169,200,.15);background:#fff}
.gvdck .wizard-btns{display:flex;justify-content:space-between;gap:12px;flex-wrap:wrap;padding:6px 28px 30px!important;margin:0}
.gvdck .wizard-btns li{padding:0}
.gvdck .new-btn{border:0!important;border-radius:12px!important;padding:14px 22px!important;font-weight:700!important;font-size:15px!important;text-transform:none!important;background:#EEF3F6!important;color:var(--ink)!important;box-shadow:none}
.gvdck .new-btn#userInfo,.gvdck .new-btn.next-step{background:linear-gradient(135deg,var(--o),var(--o2))!important;color:#fff!important;box-shadow:0 10px 24px rgba(255,80,0,.28)}
.gvdck .new-btn:hover{transform:translateY(-2px)}
.gvdck table{width:100%;border-collapse:collapse}.gvdck table td,.gvdck table th{padding:12px 8px;border-bottom:1px dashed var(--line)!important;font-size:14px}
.gvdck tr.total td{font-weight:800;font-size:16px}
.gvdck-lock{margin:14px 0 0;padding:14px;border-radius:14px;background:linear-gradient(135deg,#EAF7FB,#F5FBFD);border:1px solid #CFEAF2;font-size:13px;color:var(--b2)}
.gvdck-lock b{color:var(--ink)}
.gvdck-trust{display:flex;gap:16px;justify-content:center;flex-wrap:wrap;margin:18px 0 0;font-size:12px;color:var(--mut)}
@media(max-width:767px){.gvdck .nav-tabs>li>a:after{display:none}.gvdck .nav-tabs>li>a{justify-content:center}.gvdck .wizard-body{padding:8px 16px 14px}.gvdck .wizard-btns{padding:6px 16px 24px!important}.gvdck .wizard-btns li{width:100%}.gvdck .new-btn{width:100%}.gvdck-top .sec{display:none}.gvdck .credit-app-row>div{padding:0 8px}}
</style>
<style type="text/css">
	.error_message {
		color: red;
	}
	#jq-signature-canvas-1{
		width: 100% !important;
	}
	.modal-body.tac-class {
		height: 75vh;
		overflow: auto;
		text-align: justify;
	}
	.credit-app-row {
		display: flex;
		justify-content: center;
	}
	.wizard-btns {
		position: static !important;
		padding-bottom: 40px;
	}
  	.wizard-body {
		padding: 0 30px 30px 30px;
	}
</style>
@endsection
@php
		$testIps = ['154.80.15.250', '139.135.36.160', '110.39.14.26'];
		$clientIp = request()->ip();
		$stripeKey = in_array($clientIp, $testIps)
			? 'pk_test_51MebYrAvrdybUPpyA6quYInM0TTgFyeNRkmKCkm3HAsYmiLCsuWviZgw1Go6mLw8fz1xcpoWgXrtBK9mgbssZUbn00XGroRFCj'
			: config('services.stripe.publishable');
@endphp

@section('scripts')
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.13.1/jquery.validate.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery.payment/1.2.3/jquery.payment.min.js"></script>
<script src="https://js.stripe.com/v3/"></script>
@endsection
@section('content')
<!-- <section class="header-wrap">
  <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
      <div class="container">
          <h2>Customer Information</h2>
      </div>
  </section>
  @include('includes.top_header')
  @include('includes.navbar')
</section> -->
<?php if (session_status() === PHP_SESSION_NONE) { session_start(); } ?>
<?php
  // Display-only: session keeps the raw JSON (RegisterController json_decodes it).
  $gvdPlans = [0=>['Small Business Standard',24.95],1=>['Small Business Premium',49.95],2=>['Enterprise',84.95]];
  $gvdSel = $_SESSION["SelectNumberInfo"] ?? null;
  $gvdNums = [];
  if ($gvdSel) { $raw = json_decode($gvdSel['number'] ?? '', true); $raw = is_array($raw) ? $raw : [$gvdSel['number'] ?? ''];
    foreach ($raw as $n) { $d = preg_replace('/\D/','',(string)$n); if (strlen($d)==11 && $d[0]=='1') $d = substr($d,1);
      $gvdNums[] = strlen($d)==10 ? '('.substr($d,0,3).') '.substr($d,3,3).'-'.substr($d,6) : e($n); } }
  $gvdPlan = ($gvdSel && isset($gvdPlans[(int)$gvdSel['package']])) ? $gvdPlans[(int)$gvdSel['package']] : null;
?>
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDJWX9X9" height="0" width="0" style="display:none"></iframe></noscript>
@include('includes.gvd-checkout-header')
<div class="checkout-page gvdck">
	<div class="checkout-overlay"></div>
	<section class="gvd-hero gvd-co"><span class="eyebrow pop">SECURE CHECKOUT</span><h1 class="pop d1"><?php if ($gvdNums) { ?>Your business line is <?php echo $gvdNums[0] ?><?php } else { ?>Your business line is minutes away<?php } ?></h1><p class="pop d2">Three quick steps and you're live. Your number locks in once payment completes.</p></section>
	<div class="container wrapper">
		<div class="row credit-app-row">
			<div class="col-lg-4 col-md-10 col-sm-12 gvd-co gvd-sumcol"><aside class="gvd-sum pop d2"><h3>ORDER SUMMARY</h3>
<?php if ($gvdPlan) { ?><div class="r"><span>Plan</span><b><?php echo e($gvdPlan[0]) ?></b></div><div class="r"><span>Monthly</span><b>$<?php echo number_format($gvdPlan[1],2) ?>/mo</b></div><?php } ?>
<?php if ($gvdNums) { ?><div class="r"><span>Your number</span><b><?php echo implode(', ', $gvdNums) ?></b></div><?php } ?>
<div class="r tot"><span>Due today</span><b>$149.00</b></div>
<div class="note">Annual membership. Your number locks in once payment completes.</div>
<div class="gvd-trust"><span>&#128274; Stripe secured</span><span>&#128200; Reports to business credit bureaus</span></div></aside></div>
			<div class="col-lg-8 col-md-10 col-sm-12">
				<div class="wizard signup-wizard">
					<div class="wizard-inner">
						<div class="connecting-line"></div>
						<ul class="nav nav-tabs" role="tablist">

							<li role="presentation" class="active">
								<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Customer information">
									<span class="round-tab">
										<i class="fa fa-user"></i>
									</span>
								</a>
							</li>
							<li role="presentation" class="disabled">
								<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Cart Contents">
									<span class="round-tab">
										<i class="fa fa-tag"></i>
									</span>
								</a>
							</li>
							<li role="presentation" class="disabled">
								<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Payment Details">
									<span class="round-tab">
										<i class="fa fa-credit-card"></i>
									</span>
								</a>
							</li>
						</ul>
					</div>
					<form id="form" role="form" method="post" action="signupaction">
            			<input type="hidden" name="_token" value="{{ csrf_token() }}">
            			<input type="hidden" name="referal_token" value="<?php echo isset($_GET['referal_token'])?$_GET['referal_token']:'' ?>">
            			<input type="hidden" name="non_memberreferal_token" value="<?php echo isset($_GET['non-memberreferal_token'])?$_GET['non-memberreferal_token']:'' ?>">
						<div class="tab-content">
							<div class="tab-pane active" role="tabpanel" id="step1">
								<h2 class="wizard-title-bar">Sign Up</h2>
								<?php if(isset($_GET['message'])){ ?>
									<div class="alert alert-danger" role="alert">
									     <?php echo $_GET['message']; ?>
									</div>
								<?php } ?>
								<div class="wizard-body">
									<h2 class="text-center">Customer information</h2>
									<div class="error_message"></div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Nick Name</label>
												<input type="text" name="nicename" id="nicename" class="form-control" tabindex="1" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Company Name</label>
												<input type="text" name="display_name" id="display_name" class="form-control" tabindex="2" required>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>User Name</label>
												<input type="text" name="user_name" id="user_name" class="form-control" tabindex="3" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Email Address</label>
												<input type="email" name="email" id="email" class="form-control" tabindex="4" required>
											</div>
										</div>
									</div>
									<div class="form-group">
										<label>Your Website</label>
										<input type="text" name="user_url" id="user_url" class="form-control" tabindex="4" >
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Password</label>
												<input type="password" name="password" id="password" class="form-control" tabindex="5" required>
												<span id='message'></span>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Confirm Password</label>
												<input type="password" name="password_confirmation" id="password_confirmation" class="form-control" tabindex="6" required>
												<span id='message'></span>
											</div>
										</div>
									</div>
								</div>
								<div class="wizard-body">
									<h2 class="text-center">Billing information</h2>
									<div class="error_message"></div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>First Name</label>
												<input type="text" name="fname" id="fname" class="form-control" tabindex="1" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Last Name</label>
												<input type="text" name="lname" id="lname" class="form-control" tabindex="2" required>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Company Name</label>
												<input type="text" name="companyname" id="companyname" class="form-control" tabindex="3" required>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Address</label>
												<input type="text" name="address1" id="address1" class="form-control" tabindex="4" required>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>City</label>
												<input type="text" name="city" id="city" class="form-control" tabindex="5" required>
												<span id='message'></span>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>State</label>
												<input type="text" name="state" id="state" class="form-control" tabindex="6" required>
												<span id='message'></span>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Zip</label>
												<input type="text" name="zip" id="zip" class="form-control" tabindex="5" required>
												<span id='message'></span>
											</div>
										</div>
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Country</label>
												<input type="text" name="country" id="country" class="form-control" tabindex="6" required>
												<span id='message'></span>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<div class="form-group">
												<label>Phone</label>
												<input type="text" name="bphone" id="bphone" class="form-control" tabindex="5" required>
												<span id='message'></span>
											</div>
										</div>
										 
									</div>
								</div>
								<ul class="list-inline wizard-btns">
									<li><button type="buttton" title="" data-toggle="modal" data-target="#login-modal" class="button_slide slide_right new-btn btn-blue prev-step">Return to Signin</button></li>
									<li><button id="userInfo" type="button" class="button_slide slide_right new-btn btn-blue">Save and continue</button></li>
								</ul>
							</div>
							<div class="tab-pane" role="tabpanel" id="step2">
								<h2 class="wizard-title-bar">Sign Up</h2>
								<div class="wizard-body">
									<h2>Cart Contents</h2>
									<div class="checkout-sumary checkout-left-basket margn0">
										<table class="timetable_sub">
											<thead>
												<tr>
													<th>Product Name</th>
													<th>Quantity</th>
													<th>Price</th>
												</tr>
											</thead>

											<tbody>
												<tr>
													<td class="invert">Annual Fees</td>
													<td>1</td>
													<td class="invert">$149.00</td>
												</tr>
												<?php if ($gvdPlan) { ?>
												<tr>
													<td class="invert">Plan: <?php echo e($gvdPlan[0]) ?> (billed monthly after activation)</td>
													<td>1</td>
													<td class="invert">$<?php echo number_format($gvdPlan[1],2) ?>/mo</td>
												</tr>
												<?php } ?>
												<?php if ($gvdNums) { ?>
												<tr>
													<td class="invert">Your number: <?php echo implode(', ', $gvdNums) ?><br><small>Locked in once payment completes.</small></td>
													<td><?php echo count($gvdNums) ?></td>
													<td class="invert">Included</td>
												</tr>
												<?php } ?>
												 
												<tr class="total">
													<td class="invert">Amount to be Finance</td>
													<td class="invert" colspan="2">$149.00</td>
												</tr>
											</tbody>
										</table>
									</div>
								</div>
								<ul class="list-inline wizard-btns">
									<li><a href="#" class="button_slide slide_right new-btn btn-blue prev-step">Return to Customer information</a></li>
									<li><button type="button" class="button_slide slide_right new-btn btn-blue next-step">confirm and continue</button></li>
								</ul>
							</div>
							<div class="tab-pane" role="tabpanel" id="step3">
								<h2 class="wizard-title-bar">Sign Up</h2>
								<div class="e_message"></div>
								<div class="wizard-body">
									<h2 class="text-center">Payment Details</h2>
									<div class="error_message"></div>
									<div class="panel-heading text-center" >
										<img class="" src="/public/images/payment-bg.png">
									</div>
									<div class="row">
										<div class="col-xs-12">
											<!-- <div class="form-group">
												<label for="cardNumber">CARD NUMBER</label>
												<div class="input-group">
													<input id="cardNumber" minlength="13" maxlength="16" 
													type="tel"
													class="form-control"
													name="cardNumber"
													placeholder="Valid Card Number"
													autocomplete="cc-number"
													required autofocus
													/>
													<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
												</div>
											</div>

											<div class="form-group">
													<label for="card-element">Credit or debit card</label>
													<div id="card-element">
														
													</div>
													
													<div id="card-errors" role="alert" style="color:red; margin-top:5px;"></div>
										    </div> -->
											
											
												@if($payment_gateway === 'authorize')
													<!-- Authorize.Net Form -->
													<div class="form-group">
														<label for="cardNumber">CARD NUMBER</label>
														<div class="input-group">
															<input id="cardNumber" minlength="13" maxlength="16" 
																type="tel"
																class="form-control"
																name="cardNumber"
																placeholder="Valid Card Number"
																autocomplete="cc-number"
																required autofocus
															/>
															<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
														</div>
													</div>
												@elseif($payment_gateway === 'stripe')
													<!-- Stripe Form -->
													<div class="form-group">
														<label for="card-element">Credit or debit card</label>
														<div id="card-element">
															<!-- Stripe Elements will create the card input here -->
														</div>
														<!-- Used to display form errors -->
														<div id="card-errors" role="alert" style="color:red; margin-top:5px;"></div>
													</div>
												@else
													<p>No payment gateway is active. Please contact admin.</p>
												@endif
											
											
									</div>
									<div class="row">
										<div class="col-xs-8 col-md-8">
											
											<div class="col-lg-6 col-md-6 col-sm-12">
												<label for="month" style="font-size: 10px">EXPIRATION MONTH</label>
												<select class="form-control" name="month" id="month" required>
													
													<option value="">Month</option>
													<option value="01">01</option>
													<option value="02">02</option>
													<option value="03">03</option>
													<option value="04">04</option>
													<option value="05">05</option>
													<option value="06">06</option>
													<option value="07">07</option>
													<option value="08">08</option>
													<option value="09">09</option>
													<option value="10">10</option>
													<option value="11">11</option>
													<option value="12">12</option>
												</select>
											</div>
											<div class="col-lg-6 col-md-6 col-sm-12">

												<label for="year" style="font-size: 10px">EXPIRATION YEAR</label>
												<select class="form-control" name="year" id="year" required>
													<option value="">Year</option>
													
													<option value="2025">2025</option>
													<option value="2026">2026</option>
													<option value="2027">2027</option>
													<option value="2028">2028</option>
													<option value="2029">2029</option>
													<option value="2030">2030</option>
													<option value="2031">2031</option>
													<option value="2032">2032</option>
													<option value="2033">2033</option>
													<option value="2034">2034</option>
													<option value="2035">2035</option>
												</select>
											</div>
										</div>
										<div class="col-lg-4 col-md-4 col-sm-12">
											<div class="form-group">
												<label for="cardCVC" style="font-size: 10px">CV CODE</label>
												<input id="cvc" maxlength="4"
												type="tel"
												class="form-control"
												name="cardCVC"
												placeholder="CVC"
												autocomplete="cc-csc"
												required
												/>
											</div>
										</div>
										
										<div class="col-lg-4 col-md-4 col-sm-12">
											<div class="form-group">
												<label for="cardName" style="font-size: 10px">Name On Card</label>
												<input id="nameOnCard"
												type="text"
												class="form-control"
												name="nameOnCard"
												placeholder="Name On Card"
												required />
											</div>
										</div>
									</div>
									<!-- <div class="row">
										<div class="col-xs-12">
											<div class="form-group">
												<label for="couponCode">COUPON CODE</label>
												<input type="text" class="form-control" name="couponCode" />
											</div>
										</div>
									</div> -->
									<div class="row">
										<div class="col-lg-6 col-md-6 col-sm-12">
											
											<div class="form-group" style="float: left;">
												<input type="checkbox" name="term_and_conditions" id="term_and_conditions_check">
												<label> I Agree the <a href="#term_and_conditions_modal" data-toggle="modal">Term and Condition</a></label>
											</div>
										</div>
										
									</div>
									<div class="row">
										<div class="col-lg-8 col-md-8 col-sm-12">
											<div class="form-group">
												<label for=".js-signature">Signature Below:</label>
												<div class='js-signature' data-background="#EEEEEE" data-line-color="#3183d7"></div>
											</div>
											<div class="form-inline" style="margin-bottom: 20px;">
												<input type="button" name="clearConvas" class="btn btn-secondary" id="clearConvas" value="Clear eSign">
												<input type="hidden" name="signature_img" id="signature_img">
											</div>
										</div>
									</div>
									<div class="row" style="display:none;">
										<div class="col-lg-6 col-md-6 col-sm-12">
											<p class="payment-errors"></p>
										</div>
									</div>
								</div>
								<ul class="list-inline wizard-btns">
									<li><a href="#" class="button_slide slide_right new-btn btn-blue prev-step">Return to Cart Contents</a></li>
									<li><button type="submit" class="button_slide slide_right new-btn btn-blue next-step" id="register_btn" data-gtm="click_signup">Register</button></li>
								</ul>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
@include('includes.gvd-checkout-footer')

<div class="modal fade" id="term_and_conditions_modal" tabindex="-1" role="dialog" aria-labelledby="term_and_conditions_modal_title" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #00bcd4; color: #fff">
        <span class="modal-title" id="term_and_conditions_modal_title">Terms and Conditions</span>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body tac-class">
            <div class="sent_sms_table_modal">
                <p>Global Voice Direct does not discriminate on the basis of age, race, color, religion, national origin, gender or sexual orientation. For your benefit, we have posted a copy of its Equal Employment Opportunity (EEO) policy statement</p>
                <p>This Document contains the Purchase Agreement and the Terms and Conditions of becoming an Account Holder with Global Voice Direct and its subsidiaries.</p>
                <p>BY ACCESSING AND/OR USING THE Global Voice Direct  WEBSITE (THE "WEBSITE") AND/OR OUR SERVICE (THE "SERVICE"), YOU AGREE TO COMPLY WITH, AND BE BOUND BY, THE FOLLOWING TERMS AND CONDITIONS (THE "AGREEMENT").</p>
                <p>I authorize  Global Voice Direct  to debit the Credit Card  account indicated in this web form, for the noted amount on today’s date. I understand that because this is an electronic transaction, these funds may be withdrawn from my account as soon as the above noted transaction date. In the case of an Debit / Credit Card  Transaction being rejected for Non Sufficient Funds (NSF) I understand that the business may at its discretion attempt to process the charge again within 30 days, and agree to an additional $0 charge for each attempt as a separate transaction from the authorized payment. I acknowledge that the origination of ACH transactions to my account must comply with the provisions of U.S. law. I will not dispute merchant debiting my debit / Credit card so long as the transaction corresponds to the terms indicated in this web form. Our billing cycle runs on a net 30 terms. We do not charge automatically by default unless you add your credit card on file with us. A net-30 account is one that extends you 30 days to pay the bill in full after you have purchased  products.  </p>
                <h4>CREDIT REPORTING:</h4>
                <p>YOUR ACCOUNT WITH Global Voice Direct  WILL BE ASSIGNED TO A Commercial  FINANCE COMPANY WITHIN APPROXIMATELY 30 DAYS FROM THE DATE OF THAT YOUR ACCOUNT IS APPROVED AND A PURCHASE OF PRODUCTS IS MADE IF THE ACCOUNT IS ACTIVE AT THAT TIME. WHILE PAYMENTS ARE DUE ON YOUR ACCOUNT DURING THIS FIRST Sixty (30) DAYS, YOUR ACCOUNT WILL NOT BE REPORTED TO ANY CREDIT BUREAU UNTIL THE SALE OF YOUR ACCOUNT TO THE Commercial  FINANCE COMPANY IS COMPLETED. CREDIT REPORTING DURING THE TERM OF THIS AGREEMENT IS NOT GUARANTEED. </p>
                <h4>REFUND POLICY ACCEPTED</h4>
                <p>We have a 7 days refund policy.  We do not provide refund after your account has been successfully reported to the commercial business bureaus. We report  positive and negative accounts to the Commercial Business bureaus.</p>
                <h4>SHIPPING POLICY</h4>
                <p>All products are shipped within 14  business days of acceptance of order.</p>
                <h4>RIGHTS AND OBLIGATIONS OF THE BUYER</h4>
                <p>The Buyer must pay the full consideration for this product that the Seller requires as the total price of the product. This consideration includes not only the purchase price, but other obligations that the Buyer accepts as well as potential rights the Buyer agrees to forego. By accepting this Purchase Agreement, the Buyer agrees to receive continuing follow-up contact from the Seller including email, mail, newsletters, product updates, product recall notices, product improvements, telephone calls from the Seller and/or telemarketing organizations and/or pollsters for the purpose of solicitation related to the instant product or any other product or service. Buyer agrees to post-sale contact from joint venture partners of the Seller or from others who have a commercial relationship with the Seller. Buyer agrees that all personal information about the buyer or his or her buying habits and preferences, including address and phone number, may be placed in a general database and agrees that this information may be shared, rented or sold to third parties. However, Buyer shall at all times be fully empowered to sever contact with the Seller by notification using the 'unsubscribe' link in solicitations. Moreover, the Buyer retains the right to refuse specific contact with some third party solicitors and maintain it with others. The Buyer retains the right to have his or her name removed from a general solicitation database. The Buyer's agreement to accept solicitation and contact may be reduced, enhanced, limited or terminated by notification to anyone contacting the Buyer. The burden is on the Buyer to prove that such communication was made to and received by the person making contact. Buyer agrees that Seller is not liable for communications made to the Buyer by parties unrelated to this purchase even though referred by the Seller. Buyer accepts full responsibility for limiting unsolicited contact and Buyer understands that he retains all rights to directly restrict communication or solicitation from any party including the Seller.</p>
                <p>Buyer agrees that in lieu of closing the Account, in the event after 90 days of nonpayment the account will be closed and any balance due will be reported to the commercial credit bureaus , active and reporting until Buyer notifies Global Voice Direct to Close the account or makes the qualifying purchase.</p>
                <h4>CREDIT CARD CHARGES AND CREDIT CARD FRAUD</h4>
                <p>Buyer warrants that he or she is over 18 years of age, not subject to the Child Online Privacy Act, of legal age to enter into contractual agreements in the state in which he is present when he makes this purchase, and is the true and authorized owner of the credit card used to make this purchase. Any Buyer who violates any of these requirements may be liable for civil or criminal prosecution, plus actual damages, and agrees that all information collected by this website may be used for prosecution and may be turned over to law enforcement agencies or to credit card companies and merchant service providers.</p>
                <p>If the true and/or authorized owner of the credit card attempts to commit fraud upon the Seller, he authorizes each and every credit card company or merchant service provider to disclose to the Seller all information that could be construed as proof of credit card fraud</p>
                <p>Any Buyer who attempts to perpetrate a fraud upon Seller involving the use of a credit card herewith gives authorization for the Seller to access all credit information about the Buyer from credit reporting agencies and also authorizes the Seller to discover all relevant information from any source about the fraudulent practices of the Buyer and to reveal such information to credit reporting agencies, credit card companies, merchant service providers, and law enforcement agencies.</p>
                <h4>ASSUMPTION OF RISK</h4>
                <p>Buyer agrees to accept all risk associated with the use of this product, including but not limited to, ingestion of or application to Buyer's person, the use of the product personally or in business, all taxes and regulations applicable to this product, all legal compliance issues related to this product. Buyer warrants an understanding that the Seller is disclaiming all liability from harm of any kind or nature caused directly or indirectly from this product. Buyer agrees, as part of the consideration required to purchase this product, to carefully review and test this product during the refund period and to immediately request a refund if the product is not satisfactory.</p>
                <h4>LIMITATION ON THE LIABILITY LIMITATION</h4>
                <p>Buyer understands that some states do not allow limitation of liability.</p>
                <h4>REFUND POLICY ACCEPTED</h4>
                <p>We have a 7 days refund policy. We do not provide refund after your account has been successfully  reported to the commercial business bureaus. We report  positive and negative accounts to the Commercial Business bureaus.</p>
                <h4>PRIVACY POLICY ACCEPTED</h4>
                <p>Buyer expressly accepts the terms of the Privacy Policy of Seller's website.</p>
                <h4>TERMS OF USE ACCEPTED</h4>
                <p>Buyer expressly accepts the Terms of Use of the Seller's website.</p>
                <h4>RIGHT TO PUBLISH SUBMISSIONS</h4>
                <p>Buyer agrees that Seller may publish for commercial purposes the full or partial content of any and all communication with Buyer at the Seller's sole discretion.</p>
                <h4>INDEMNIFICATION</h4>
                <p>Buyer agrees to indemnify Seller for any and all damage that Buyer causes by using the product or information contained on this website that results in a damage award against the Seller.</p>
                <h4>RIGHT TO STOP SELLING OR SERVICING PRODUCT OR MEMBERSHIP</h4>
                <p>Buyer agrees that Seller has the right to discontinue the product, the service, the membership at any time without notice. Buyer understands that the Seller may discontinue customer service on a product or service at any time without notice.</p>
                <h4>CALIFORNIA RESIDENTS NOTE</h4>
                <p>You are entering into a contract that may modify, restrict, or eliminate rights you may have under the California Online Privacy Protection Act of 2003 (OPPA). Under the Privacy Policy and this Purchase Agreement you waive any right to view or modify the content of our database. You waive any right to force this business or website to divulge when or to whom your information may have been provided to third parties. In the event the website elects at its sole discretion to release information to you, you must clearly identify yourself to the</p>
                <p>In addition, Global Voice Direct  hereby certifies that it does not maintain or provide for their employees any segregated facilities at any establishment. As used in this Certification, the term "Segregated Facilities" means any waiting rooms, work area, rest rooms and wash rooms, restaurants and other eating areas, time clocks, locker rooms and other storage or dressing areas, transportation, and housing facilities provided for employees which are segregated by explicit directive or are in fact segregated on the basis of race, creed, color or national origin, because of habit, local custom or otherwise.</p>
            </div>        
        </div>
        <div class="modal-footer">
        	
            <button type="button" class="btn btn-info button_slide slide_right" id="term_agreement" data-dismiss="modal">I Agree the Terms</button>
        </div>
    </div>
  </div>
</div>
@push('scripts')
<script type="text/javascript">
	$('#password, #password_confirmation').on('keyup', function () {
		if ($('#password').val() == $('#password_confirmation').val()) {
			$('#message').html('Matching').css('color', 'green');
		} else
		$('#message').html('Not Matching').css('color', 'red');
	});
</script>
 
<script type="text/javascript" src="/public/credit-app/signature.js"></script>
<script>
	$('#term_agreement').on('click', function(){
		$('#term_and_conditions_check').prop("checked",true);
	});
	$('#term_and_conditions_check').on('click', function(){
		if($('#term_and_conditions_check').is(':checked')){
			 setTimeout(function() {
		      this.removeAttr('checked');
		    }, 0);

		    event.preventDefault();
		    event.stopPropagation();
			$('#term_and_conditions_modal').modal('show');
		}
	});
	$(document).ready(function () {
	var sign_flag = false;

	if ($('.js-signature').length) {
		$('.js-signature').jqSignature();
	}
	$('#clearConvas').on('click',function(e){
		$('.js-signature').jqSignature('clearCanvas');
		sign_flag = false;
	});
	$('.js-signature').on('jq.signature.changed',function(e){
		sign_flag = true;
	});
    //Initialize tooltips
    // $('.nav-tabs > li a[title]').tooltip({
    // 	trigger: "hover",
    // });

    //Wizard
    $('.wizard a[data-toggle="tab"]').on('show.bs.tab', function (e) {

    	var $target = $(e.target);

    	if ($target.parent().hasClass('disabled')) {
    		return false;
    	}
    });

    $("#userInfo").click(function (e) {
    	var error_message = "<span><ul>";
    	var flag = "true";
    	if($('#nicename').val() == "")
    	{
    		error_message = error_message + "<li>Nick Name must be Enter</li>";
    		flag = "false";
    	}
    	if($('#display_name').val() == "")
    	{
    		error_message = error_message + "<li>Company Name must be Enter</li>";
    		flag = "false";
    	}
    	if($('#user_name').val() == "")
    	{
    		error_message = error_message + "<li>User Name must be Enter</li>";
    		flag = "false";
    	}
    	if($('#email').val() == "")
    	{
    		error_message = error_message + "<li>Email must be Enter</li>";
    		flag = "false";
    	}
    	if($('#password').val() == "")
    	{
    		error_message = error_message + "<li>Password must be Enter</li>";
    		flag = "false";
    	}
    	if($('#password_confirmation').val() == "")
    	{
    		error_message = error_message + "<li>Confirm Password be Enter</li>";
    		flag = "false";
    	}
    	var pass = $('#password').val();
    	var pass1 = $('#password_confirmation').val();
    	if(pass != pass1){
    		error_message = error_message + "<li>Password Must be Same</li>";
    		flag = "false";
    	}
    	if(pass.length < 8){
    		error_message = error_message + "<li>Password Lenghth Must be 8 Character Long</li>";
    		flag = "false";
    	}
    	if($("#fname").val() ==""){
    		error_message = error_message + "<li>First Name must be Enter</li>";
    		flag = "false";
    	}
    	if($("#lname").val() == ""){
    		error_message = error_message + "<li>Last Name must be Enter</li>";
    		flag = "false";
    	}
    	if($("#companyname").val() == ""){
    		error_message = error_message + "<li>Company Name must be Enter</li>";
    		flag = "false";
    	}
    	if($("#address1").val() == ""){
    		error_message = error_message + "<li>Adress must be Enter</li>";
    		flag = "false";
    	}
    	if($("#city").val() == ""){
    		error_message = error_message + "<li>City must be Enter</li>";
    		flag = "false";
    	}
    	if($("#state").val() == ""){
    		error_message = error_message + "<li>State must be Enter</li>";
    		flag = "false";
    	}
    	if($("#zip").val() == ""){
    		error_message = error_message + "<li>Zip Code must be Enter</li>";
    		flag = "false";
    	}
    	if($("#country").val() == ""){
    		error_message = error_message + "<li>Country must be Enter</li>";
    		flag = "false";
    	}
    	if($("#bphone").val() == ""){
    		error_message = error_message + "<li>Phone Number must be Enter</li>";
    		flag = "false";
    	}

    	if(flag == "true"){
    		$(".error_message").html("");
    		var $active = $('.wizard .nav-tabs li.active');
	    	$active.next().removeClass('disabled');
	    	nextTab($active);
    	}else{
    		error_message = error_message + "</ul></span>";
    		$(".error_message").html(error_message);
    	}
    	

    });
    // $('#register_btn').on('click', function(e){
	// 	var error_message = "<span><ul>";
	// 	var flag = "true";
	// 	if(!$('#term_and_conditions_check').is(':checked')){
	// 		error_message = error_message + "<li>Please check term and conditions.</li>";
	// 		flag = "false";
	// 	}
		
	//  	if(sign_flag == false){
	//  		error_message = error_message + "<li>Please sign first before proceeding.</li>";
	// 		flag = "false";
	//  	}
	// 	if(flag == "true"){
	// 		$('#signature_div').empty();
	// 		$(".error_message").html("");
	// 		$("#register_btn").attr('disabled',false);
	// 		var dataUrl = $('.js-signature').jqSignature('getDataURL');
	// 		document.getElementById('signature_img').value = dataUrl;
	// 	}else{
	// 		error_message = error_message + "</ul></span>";
	// 		$(".error_message").html(error_message);
	// 		return false;
	// 	}
    // });

	// $(document).ready(function () {

	// 	let paymentGateway = '{{ $payment_gateway }}';
	// 	console.log("Active Gateway:", paymentGateway);

	// 	let sign_flag = false;
	// 	let stripe = null;
	// 	let card = null;

	// 	// ==============================
	// 	// SIGNATURE INITIALIZATION
	// 	// ==============================
	// 	if ($('.js-signature').length) {
	// 		$('.js-signature').jqSignature();

	// 		// Track signature changes
	// 		$('.js-signature').on('jq.signature.changed', function () {
	// 			sign_flag = true;
	// 		});

	// 		// Clear signature
	// 		$('#clearConvas').on('click', function () {
	// 			$('.js-signature').jqSignature('clearCanvas');
	// 			sign_flag = false;
	// 		});
	// 	}

	// 	// ==============================
	// 	// STRIPE INITIALIZATION
	// 	// ==============================
	// 	if (paymentGateway === 'stripe') {

	// 		let stripeKey = 'REDACTED';
	// 		stripe = Stripe(stripeKey);

	// 		let elements = stripe.elements();
	// 		card = elements.create('card', { hidePostalCode: true });
	// 		card.mount('#card-element');

	// 		card.on('change', function (event) {
	// 			$('#card-errors').text(event.error ? event.error.message : "");
	// 		});
	// 	}

	// 	// ==============================
	// 	// FORM SUBMIT HANDLER
	// 	// ==============================
	// 	$(document).off('click', '#register_btn');

	// 	$('#register_btn').on('click', function (e) {

	// 		e.preventDefault(); // always prevent default first

	// 		$(".error_message").html(""); // reset errors
	// 		let error_message = "<ul>";
	// 		let isValid = true;

	// 		// -------- Terms --------
	// 		if (!$('#term_and_conditions_check').is(':checked')) {
	// 			error_message += "<li>Please accept Terms & Conditions.</li>";
	// 			isValid = false;
	// 		}

	// 		// -------- Signature --------
	// 		if (!sign_flag) {
	// 			error_message += "<li>Please provide your signature.</li>";
	// 			isValid = false;
	// 		}

	// 		// -------- Stripe Name Validation --------
	// 		if (paymentGateway === 'stripe' && $('#nameOnCard').val().trim() === "") {
	// 			error_message += "<li>Please enter name on card.</li>";
	// 			isValid = false;
	// 		}

	// 		// -------- Authorize.Net Card Validation --------
	// 		if (paymentGateway === 'authorize') {
	// 			if (
	// 				$('#cardNumber').val().trim() === "" ||
	// 				$('#cvc').val().trim() === "" ||
	// 				$('#nameOnCard').val().trim() === ""
	// 			) {
	// 				error_message += "<li>Please complete all card fields.</li>";
	// 				isValid = false;
	// 			}
	// 		}

	// 		// If validation failed, show errors and stop
	// 		if (!isValid) {
	// 			error_message += "</ul>";
	// 			$(".error_message").html(error_message);
	// 			return false;
	// 		}

	// 		// Save signature
	// 		let dataUrl = $('.js-signature').jqSignature('getDataURL');
	// 		$('#signature_img').val(dataUrl);

	// 		// Prevent double-click
	// 		$('#register_btn').prop("disabled", true).text("Please wait...");

	// 		// =====================================================
	// 		// STRIPE PAYMENT LOGIC
	// 		// =====================================================
	// 		if (paymentGateway === 'stripe') {

	// 			stripe.createPaymentMethod({
	// 				type: 'card',
	// 				card: card,
	// 				billing_details: { name: $('#nameOnCard').val() }
	// 			}).then(function (result) {

	// 				if (result.error) {
	// 					$('#card-errors').text(result.error.message);
	// 					$('#register_btn').prop("disabled", false).text("Submit");
	// 				} else {

	// 					$('<input>').attr({
	// 						type: 'hidden',
	// 						name: 'stripe_payment_method_id',
	// 						value: result.paymentMethod.id
	// 					}).appendTo('#form');

	// 					console.log("Stripe Payment Method:", result.paymentMethod.id);

	// 					$('#form').submit();
	// 				}
	// 			});

	// 			return false;
	// 		}

	// 		// =====================================================
	// 		// AUTHORIZE.NET PAYMENT LOGIC
	// 		// =====================================================
	// 		if (paymentGateway === 'authorize') {
	// 			console.log("Authorize.Net validated → submitting");
	// 			return true; // normal form submission
	// 		}

	// 	});

	// });

	$(document).ready(function () {

		let paymentGateway = '{{ $payment_gateway }}';
		console.log("Active Gateway:", paymentGateway);

		let sign_flag = false;
		let stripe = null;
		let card = null;

		// ==============================
		// SIGNATURE INITIALIZATION
		// ==============================
		if ($('.js-signature').length) {
			$('.js-signature').jqSignature();

			// Track signature changes
			$('.js-signature').on('jq.signature.changed', function () {
				sign_flag = true;
			});

			// Clear signature
			$('#clearConvas').on('click', function () {
				$('.js-signature').jqSignature('clearCanvas');
				sign_flag = false;
			});
		}

		// ==============================
		// STRIPE INITIALIZATION
		// ==============================
		if (paymentGateway === 'stripe') {

			// Get your publishable key as a string

            //let stripeKey = 'REDACTED';
			let stripeKey = '{{ config("services.stripe.publishable") }}';
			//console.log("Stripe Key:", stripeKey); // should print your key

			// Initialize Stripe only once with the string key
			stripe = Stripe(stripeKey);

			// Create Elements
			let elements = stripe.elements();
			card = elements.create('card', { hidePostalCode: true });
			card.mount('#card-element');

			// Card input validation
			card.on('change', function (event) {
				$('#card-errors').text(event.error ? event.error.message : "");
			});
		}

		// if (paymentGateway === 'stripe') {
		// 	// Get Stripe public key from backend (Laravel config or $stripeKey)
		// 	// let stripeKey = 'REDACTED';
		// 	let stripeKey = '{{ config("services.stripe.publishable") }}';
		// 	stripe = Stripe(stripeKey);
		// 	console.log(stripeKey); // ❌ stripeKey is undefined

		// 	let elements = stripe.elements();
		// 	card = elements.create('card', { hidePostalCode: true });
		// 	card.mount('#card-element');

		// 	card.on('change', function (event) {
		// 		$('#card-errors').text(event.error ? event.error.message : "");
		// 	});

		// 	console.log("Stripe initialized with key:", stripeKey);
		// }

		// ==============================
		// FORM SUBMIT HANDLER
		// ==============================
		$(document).off('click', '#register_btn');

		$('#register_btn').on('click', function (e) {

			let error_message = "<span><ul>";
			let flag = "true";

			// -------- Terms --------
			if (!$('#term_and_conditions_check').is(':checked')) {
				error_message += "<li>Please accept Terms & Conditions.</li>";
				flag = "false";
			}

			// -------- Signature --------
			if (!sign_flag) {
				error_message += "<li>Please provide your signature.</li>";
				flag = "false";
			}

			// -------- Stripe Name Validation --------
			if (paymentGateway === 'stripe' && $('#nameOnCard').val().trim() === "") {
				error_message += "<li>Please enter name on card.</li>";
				flag = "false";
			}

			// -------- Authorize.Net Card Validation --------
			if (paymentGateway === 'authorize') {
				if (
					$('#cardNumber').val().trim() === "" ||
					$('#cvc').val().trim() === "" ||
					$('#nameOnCard').val().trim() === ""
				) {
					error_message += "<li>Please complete all card fields.</li>";
					flag = "false";
				}
			}

			if (flag === "true") {
				// Clear errors & save signature
				$('#signature_div').empty();
				$(".error_message").html("");
				$("#register_btn").attr('disabled', false);

				let dataUrl = $('.js-signature').jqSignature('getDataURL');
				$('#signature_img').val(dataUrl);

				// =====================================================
				// STRIPE PAYMENT LOGIC
				// =====================================================
				if (paymentGateway === 'stripe') {

					e.preventDefault(); // only prevent for stripe

					$('#register_btn').prop("disabled", true).text("Please wait...");

					stripe.createPaymentMethod({
						type: 'card',
						card: card,
						billing_details: { name: $('#nameOnCard').val() }
					}).then(function (result) {

						if (result.error) {
							$('#card-errors').text(result.error.message);
							$('#register_btn').prop("disabled", false).text("Submit");
						} else {

							$('<input>').attr({
								type: 'hidden',
								name: 'stripe_payment_method_id',
								value: result.paymentMethod.id
							}).appendTo('#form');

							console.log("Stripe Payment Method:", result.paymentMethod.id);

							$('#form').submit();
						}
					});

				} else if (paymentGateway === 'authorize') {
					// Authorize.Net can proceed normally
					console.log("Authorize.Net validated → submitting");
					return true;
				}

			} else {
				// Show validation errors
				error_message += "</ul></span>";
				$(".error_message").html(error_message);
				return false;
			}

		});

	});
	
    
    $(".next-step").click(function (e) {

    	var $active = $('.wizard .nav-tabs li.active');
    	$active.next().removeClass('disabled');
    	nextTab($active);

    });
    $(".prev-step").click(function (e) {

    	var $active = $('.wizard .nav-tabs li.active');
    	prevTab($active);

    });
    $('.wizard .collapse').collapse()
    $('html,body').animate({scrollTop: $('.wizard').offset().top}, 800);
    var $collapse = $('.Payment-method');
		$collapse.collapse().on('show.bs.collapse', function() {
			$collapse.collapse('hide');
			});
		});

	function nextTab(elem) {
		$(elem).next().find('a[data-toggle="tab"]').click();
		$('html,body').animate({scrollTop: $('.wizard').offset().top}, 800);
	}
	function prevTab(elem) {
		$(elem).prev().find('a[data-toggle="tab"]').click();
		$('html,body').animate({scrollTop: $('.wizard').offset().top}, 800);
	}

</script>
@endpush
@endsection