@extends('layouts.app_old')
@section('css')
<style>
.checkout-sumary span {
    margin-top: 1px;
}
.form-group.form-coupon {
    border: 1px solid #c7c7c7;
    padding: 13px;
    box-shadow: 7px 9px 4px -9px #8e8b8b;
}
.alert.page-alert {
	position: absolute;
    width: 84%;
}
hr{
	  margin-top: 10px;
    margin-bottom: 10px;
}
</style>
@endsection
@section('content')

@if(session()->get('login')==true)

	<div class="checkout-page">
		<div class="checkout-overlay"></div>
		<div class="container wrapper">
			<br>
			<br>
			<?php if(isset($_GET['error'])){?>
			<div class="page-alert alert alert-danger">
			<?php echo $_GET['error']; ?>
			</div>
		<?php }elseif(isset($_GET['message'])){?>
			<div class="page-alert alert alert-success">
			<?php echo $_GET['message']; ?>
			</div>
			
			<?}?>
			<div class="wizard">
				<div class="wizard-inner">
					<div class="connecting-line"></div>
					<ul class="nav nav-tabs" role="tablist">

						<li role="presentation" class="active">
							<a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Customer information">
								<span class="round-tab">
									<i class="glyphicon glyphicon-user"></i>
								</span>
							</a>
						</li>

						<li role="presentation" class="disabled">
							<a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Shipping address">
								<span class="round-tab">
									<i class="fa fa-truck"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Order summary">
								<span class="round-tab">
									<i class="fa fa-tag"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#step4" data-toggle="tab" aria-controls="step4" role="tab" title="Payment method">
								<span class="round-tab">
									<i class="fa fa-credit-card"></i>
								</span>
							</a>
						</li>
						<li role="presentation" class="disabled">
							<a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete">
								<span class="round-tab">
									<i class="glyphicon glyphicon-ok"></i>
								</span>
							</a>
						</li>
					</ul>
				</div>
				<form role="form" method="post" action="/cart/checkout/submitOrder">
					{{csrf_field()}}
					<div class="tab-content">
						<div class="tab-pane active" role="tabpanel" id="step1">
							<h2 class="wizard-title-bar">Customer information</h2>
							<div class="wizard-body">
								<h3>Contact information</h3>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>First Name</label>
											<input type="text" name="firstname" value="{{$firstname}}" placeholder="" class="form-control" required>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>Last Name</label>
											<input type="text" name="lastname" value="{{$lastname}}" placeholder="" class="form-control" required>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Email</label>
											<input type="text" name="email" value="{{$email}}" placeholder="" class="form-control" required="true">
										<!-- <div class="checkbox">
											<label>
												<input type="checkbox" value="">
												<span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
												Keep me up to date on news and exclusive offers
											</label>
										</div> -->
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label>Mobile</label>
										<input type="text" name="mobile" value="{{$phone}}" placeholder="" class="form-control" required>
									</div>
								</div>
							</div>
							<ul class="list-inline wizard-btns">
								<li><a href="/cartItems" title="" class="new-btn btn-blue prev-step">Return to cart</a></li>
								<li><button type="button" class="new-btn btn-blue next-step">Save and continue</button></li>
							</ul>
						</div>
					</div>
					<div class="tab-pane" role="tabpanel" id="step2">
						<h2 class="wizard-title-bar">Shipping information</h2>
						<div class="wizard-body">
							<div id="personel-address" class="select-collapse collapse">
								<div id="" class="inner-address">
									<h3>International Address</h3>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>First Name</label>
												<input type="text" name="cfirstname" value="" placeholder="" class="form-control" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Last Name</label>
												<input type="text" name="clastname" value="" placeholder="" class="form-control" required>
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Company Name</label>
												<input type="text" name="c_name" value="" placeholder="" class="form-control" required="true">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Address 1</label>
												<input type="text" name="address1" value="" placeholder="" class="form-control">
											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>Address 2</label>
												<input type="text" name="address2" value="" placeholder="" class="form-control">
											</div>
										</div>
										
										
										<div class="col-md-6">
											<div class="form-group">
												<label>City</label>
												<input type="text" name="city" class="form-control" required>


											</div>
										</div>
									</div>
									<div class="row">
										<div class="col-md-6">
											<div class="form-group">
												<label>State</label>
												<input type="text" name="state" value="" placeholder="" class="form-control" required>
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>ZIP Code</label>
												<input type="text" name="zip" value="" placeholder="" class="form-control" required="true">
											</div>
										</div>
									</div>
									<div class="row">
										 <div class="col-md-6">
											<div class="form-group">
												<label>Country</label>

												<input type="text" name="country" class="form-control" required>

											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group">
												<label>Mobile</label>

												<input type="text" name="mobileNo" class="form-control" required>

											</div>
										</div>
									</div>
								</div>
							</div>

							<ul class="list-inline wizard-btns">
								<li><button type="button" class="new-btn btn-blue prev-step">Return to customer information</button></li>
								<li><button type="button" class="new-btn btn-blue next-step">Save and continue</button></li>
							</ul>

						</div>
					</div>
					<div class="tab-pane" role="tabpanel" id="step3">
						<h2 class="wizard-title-bar">Order summary</h2>
						<div class="wizard-body">
							<div class="checkout-sumary checkout-left-basket">
								<ul>
									
									<?php
									$item_total = 0;
									$total = 0;
									$i=1;
									if(session()->has('cart_item')){
									foreach (session()->get('cart_item') as $item){
										$id = $item["code"];
										$stmtLists =App\Http\Controllers\storeController::fetchItems($id);
				   
						                $rowListsCount = count($stmtLists);
										

										if($rowListsCount > 0){
										foreach($stmtLists as $rowLists){
												?>
												<li>
													<img src="productImages/<?php echo $rowLists->ProductImage;?>" alt="" class="img-responsive">
													<label><?php echo $rowLists->ProductName; ?></label>
													<!-- <button type="button" class="remove-product"></button> -->
													<?php
													if($rowLists->ProductID == $rowLists->product_id)
													{
														if($rowLists->addonFixedPrice != null)
														{
															$proPrice = $rowLists->addonFixedPrice;
														}
														else
														{
															$discount = $rowLists->addonDiscount * $rowLists->ProductPrice/100;
															$proPrice = $rowLists->ProductPrice-$discount;
														}
														?>
														<span>$<?php echo number_format(($item["quantity"] * $proPrice),2);?></span>
														</li>
														<?php

														$total = $total + ($item["quantity"] * $proPrice);
													}
													else
													{
														?>

														<span>$<?php echo number_format(($item["quantity"] * $rowLists->ProductPrice),2);?></span>
														</li>

														<?php

														$total = $total + ($item["quantity"] * $rowLists->ProductPrice);
													}
													 ?>
												<?php 
												$i++;}}}}
												if (session()->has('coupon')) {
													$coupon = session()->get('coupon');
													$discountedTotal = $total-$coupon['discount'];
													$downpayment = number_format($discountedTotal/2,2);
													$financedAmount = number_format($discountedTotal/2,2);
													$discount = $coupon['discount'];
													$discountOff = $coupon['type']=='fixed'?'$'.$coupon['discount']:$coupon['off'].'%';
												}
												?>
												<li>
													<div class="row">
														<div class="col-md-6">
															<div class="total">
																<ul class="total-value">
																	<li><label>Cart Total:</label></li>
																	<li id="cart-total"><?php echo number_format($total,2);?></li>
																	<li id="coupon-discount-label" @if (!session()->has('coupon')) style="display:none;" @endif><label id="discount-value">Discount(-@php echo $discountOff??'-'; @endphp):</label></li>

																	<li id="coupon-discount" @if (!session()->has('coupon')) style="display:none;" @endif>$<?php echo $discount??'-';?></li>
																	<li id="discount-total-label" @if (!session()->has('coupon')) style="display:none;" @endif><label>Discounted Total:</label></li>

																	<li id="coupon-discount-total" @if (!session()->has('coupon')) style="display:none;" @endif>$<?php echo $discountedTotal??'-';?></li>
																	<li><hr><label>Down Payment (50%):</label></li>
																	<li id="down-payment"><hr>$<?php echo $downpayment??number_format($total/2,2);?></li>
																	<li><hr><label>Amount to be Finance: </label></li>
																	<li id="grand-total"><hr>$<?php echo $financedAmount??number_format($total/2,2);?></li>
																</ul>
															</div>
														</div>
															@if(!session()->get('coupon'))
																<div class="col-md-6">
																  <div class="coupon-container">
																    <label for="coupon-field">Do you have coupon?</label>
																    <div class="form-group form-coupon form-inline">
																    	<input type="text" class="form-control" name="code" id="coupon_code">
																    	<a href="Javascript:;" class="btn btn-default" id="apply_coupon_btn" onclick="validateCoupon();return false;">Apply</a>
																    	<div class="notification"></div>
																    </div>
																 </div>
																</div>
															@endif
													</div>
												</li>
											</ul>
										</div>
									</div>
									<ul class="list-inline wizard-btns">
										<li><button type="button" class="new-btn btn-blue prev-step">Previous</button></li>
										<li><button type="button" class="new-btn btn-blue btn-info-full next-step">Save and continue</button></li>
									</ul>
								</div>
								<div class="tab-pane" role="tabpanel" id="step4">
									<h2 class="wizard-title-bar">Payment method</h2>
									<div class="wizard-body">
										<h2 class="text-center">Payment Details</h2>
										<div class="panel-heading text-center" >
											<img class="" src="images/payment-bg.png">
										</div>

										<div class="row">
											<div class="col-xs-12">
												<div class="form-group">
													<label for="cardNumber">CARD NUMBER</label>
													<div class="input-group">
														<input id="cardNumber" minlength="13" maxlength="16" 
														type="tel"
														class="form-control"
														name="cardnumber"
														placeholder="Valid Card Number"
														autocomplete="cc-number"
														required autofocus
														/>
														<span class="input-group-addon"><i class="fa fa-credit-card"></i></span>
													</div>
												</div>
											</div>
										</div>
										<div class="row">
											<div class="col-xs-8 col-md-8">
												
												<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
												<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">

													<label for="year" style="font-size: 10px">EXPIRATION YEAR</label>
													<select class="form-control" name="year" id="year" required>
														<option value="">Year</option>
														
														<option value="2019">2019</option>
														<option value="2020">2020</option>
														<option value="2021">2021</option>
														<option value="2022">2022</option>
														<option value="2023">2023</option>
														<option value="2024">2024</option>
														<option value="2025">2025</option>
														<option value="2026">2026</option>
														<option value="2027">2027</option>
														<option value="2028">2028</option>
														<option value="2029">2029</option>
													</select>
												</div>
											</div>
											<div class="col-xs-4 col-md-4">
												<div class="form-group">
													<label for="cardCVC" style="font-size: 10px">CV CODE</label>
													<input id="cvc" maxlength="3"
													type="tel"
													class="form-control"
													name="cardcvv"
													placeholder="CVC"
													autocomplete="cc-csc"
													required
													/>
												</div>
											</div>
										</div>
										 
									</div>
									<ul class="list-inline wizard-btns">
										<li><button type="button" class="new-btn btn-blue prev-step">Previous</button></li>
										<li><input type="submit" value="Place Order" class="new-btn btn-blue next-step"></li>
									</ul>
								</div>
								<!-- <div class="tab-pane" role="tabpanel" id="complete">
									<div class="wizard-body text-center">
										<h3>Complete</h3>
										<p>You have successfully completed all steps.</p>
									</div>
								</div> -->
								<div class="clearfix"></div>
							</div>
						</form>
					</div>
				</div>
			</div>

@else
<div class="container Checkout-options">
	<div class="row">
		<div class="col-md-12">
			<h2>Checkout Options</h2>
			<div class="panel panel-default">
				<div class="panel-body text-center">
					<div class="row">
						<div class="col-md-6">
							<div class="option-title text-left">
								<p>Submit Credit Application</p>
							</div>
							<div class="option-body text-left">
								<p>
									Haven't applied for a Membership yet? What are you waiting on? With our unique business model designed in place, Exclusive -Membership-with financing option. Special Discounts and Freebies.
								</p>
							</div>
							<a href="/credit-application" title="" class="btn btn-custom">Apply Now</a>
						</div>
						<div class="col-md-6">
							<div class="option-title text-left">
								<p>Member Please Login</p>
							</div>
							<div class="option-body text-left">
								<p>
									Already have a Membership account?. Login to your account now to finalize your shopping cart.
								</p>
								<form action="/checkout" method="POST" class="text-center">
									{{csrf_field()}}
									<div class="row">
										<div class="col-md-6">
											<div class="form-group text-left">
												<label>User Name:</label>
												<input type="text" name="username" class="form-control">
											</div>
										</div>
										<div class="col-md-6">
											<div class="form-group text-left">
												<label>password:</label>
												<input type="password" name="password" class="form-control">
											</div>
										</div>
									</div>
									<button type="submit" class="btn btn-custom">Login</button>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endif

@endsection
 @push('scripts')
<script>
	$(document).ready(function () {
    //Initialize tooltips
    $('.nav-tabs > li a[title]').tooltip({
    	trigger: "hover",
    });

    //Wizard
    $('.wizard a[data-toggle="tab"]').on('show.bs.tab', function (e) {

    	var $target = $(e.target);

    	if ($target.parent().hasClass('disabled')) {
    		return false;
    	}
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

	function validateCoupon(){
		var coupon_code = $('#coupon_code').val();

		$.ajax({
			url: '/coupon',
			type: 'POST',
			data:{code: coupon_code},
			headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
			success: function(data){
				if (data.status == 'error') {
					$('.notification').html('<p class="text-danger"><b>Oh! </b> '+data.message+'</a>');
				}else if(data.status == 'success'){
					$('.coupon-container').html('<p class="text-success"><b>Success!</b> '+data.message+'</p>');
					$('#coupon-discount-label').show();
					$('#coupon-discount-total').html('$'+(data.cart.total-data.cart.discount).toFixed(2)).show();
					var discount = data.cart.discount;
					if (data.cart.type=='fixed') {
						var discountOff = '$'+data.cart.discount;
					}else{
						var discountOff = data.cart.off+'%';
					}
					$('#coupon-discount').html('$'+discount);
					$('#coupon-discount').show();
					$('#discount-total-label').show();
					$('#discount-value').html('Discount(-'+discountOff+')');
					$('#down-payment').html('<hr>$' + data.cart.downpayment);
					$('#grand-total').html('<hr>$' + data.cart.downpayment);

				}
			},
      error: function(XMLHttpRequest, textStatus, errorThrown) { 
          alert(XMLHttpRequest['responseText']);
          console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
      }
		});
	}
</script>
@endpush