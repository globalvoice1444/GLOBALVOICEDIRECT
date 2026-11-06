@extends('layouts.user.user_main')
@section('css')
<link href="/public/css/usersStyle.css" rel="stylesheet" type="text/css" />
<style type="text/css">
	
	.text-amount-size {
		font-size: 35px !important;
	    line-height: 0.6em;
	    color: #fff !important;
	}
	.checkbox-new.radio-new.text-amount {
	    margin-left: 30%;
	    margin-top: 10px;
	}
	.checkbox-new.radio-new.other-amount {
	    margin-left: 14%;
	    margin-top: 20px;
	}
	input#amount {
	    position: absolute;
	    left: 70%;
	    background: transparent;
	    border: 2px solid #fff;
	    width: 188px;
	    padding: 12px;
	    border-radius: 5px;
	    line-height: 0.6px;
	}
	label.float-left.font_size_14.other-amount-text {
	    color: #fff;
	    font-size: 15px;
	}
	p {
	    font-size: 16px;
	    color: #666;
	}
	a {
	    text-decoration: none;
	    color: #36a6c6;
	    font-size: 16px;
	}
	.bg-white {
		border:none;
	}
	select.form-control, select.form-control:focus, select.form-control:hover{
		border: 1px solid #e2e6e9;
	}
	select.form-control{
		font-size: 14px;
		font-family: "Open Sans",sans-serif;
	}
	@media (min-width: 667px){
		.checkbox-new.radio-new.text-amount {
		    margin-left: 30%;
		}
		.checkbox-new.radio-new.other-amount {
		    margin-left: 14%;
		    /* margin-top: 20px; */
		    padding-bottom: 27px;
		}
	}
	@media (min-width: 767px) and (max-width: 1024px){
		.border-box.payment-box .top-title {
		    padding-bottom: 0px;
		    margin-bottom: 0px;
		}
		.checkbox-new.radio-new.text-amount {
		    margin-left: 17%;
		}
	}
	@media (max-width: 600px){
		input#amount {
     
		    top: -10px;
		}
	}
	.payment_input_field{
		    height: 44px !important;
	}
	.credit_img_div img{
		display: block;
	    margin-left: auto;
	    margin-right: auto;
	}
	
</style>
@endsection
@section('content')
	
			<?php if(isset($_GET['message'])){
				?> <div class="alert alert-success"><?php echo $_GET['message']; ?></div><?php
			} if(isset($_GET['error'])){
				?> <div class="alert alert-danger"><?php echo $_GET['error']; ?></div><?php
			}
			?>
			<div class="col-sm-12">
			    <div class="card">

			        <div class="card-header">
			            <h5>My Account - Bill Pay</h5>
			        </div>
					<div class="bg-white">
						<div class="border-box padding-15 border-top-none">
							<div class="row">
							<form id="myform" method="post" action="/act_submitPayment">
								{{csrf_field()}}
								<div class="col-md-12">
									<div class="border-box">
										<div class="row">
											<div class="col-md-12">
												<div class="top-title border-bootm">
													<h3>Amount to pay</h3>
												</div>
											</div>
										</div>
										<div class="form-inline">
										<?php if($payableDetail){ ?>
											<div class="row form-group">
												<div class="col-lg-2 space-right">
													<span>Current balance</span>

													<p>$<?php echo $payableDetail->r_amount ?></p>
												</div>
												<?php if($payableDetail->r_amount > 0){ ?>
												<div class="col-lg-2 space-right">
													<span>Minimum payment</span>
													<p>$30.00</p>
												</div>
												<?php } else{?>
												<div class="col-lg-2 space-right">
													<span>Minimum payment</span>
													<p>$<?php echo $payableDetail->r_amount; ?></p>
												</div>
												<?php } ?>
												 
											</div>
											<?php }else{ ?>
												<div class="row form-group">
													<div class="col-lg-2 space-right">
														<span>Current balance</span>
														<p>$0.0</p>
													</div>
													 
													<div class="col-lg-2 space-right">
														<span>Minimum payment</span>
														<p>$30.00</p>
													</div>
													 
												</div>
											<?php } ?>
											
											<div class="row margint-30">
												<div class="col-md-4">
													<div style="background-image: url('/public/users/images/1.png');" class="border-box payment-box">
														<div class="row">
															<div class="col-md-12">
																<div class="top-title border-bootm">
																	<h3>Minimum Payment</h3>
																	<div class="checkbox-new radio-new text-amount">
																		<input value="30" name="NotifyCandidate" id="rememberMe" class="float-left" type="radio">
																		<label class="float-left font_size_14 text-amount-size" for="radio-new"> $30.00</label>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-inline">
															<div class="row form-group">
																<p>
																	If you make no additional purchases and make only the minimum payment then you will pay off this balance in about 0 month(s).
																</p>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div style="background-image: url('/public/users/images/2.png');" class="border-box payment-box">
														<div class="row">
															<div class="col-md-12">
																<div class="top-title border-bootm">
																	<h3>Current balance</h3>
																	<?php if($payableDetail){ ?>
																	<div class="checkbox-new radio-new text-amount">
																		<input checked="" value="<?php echo $payableDetail->r_amount; ?>" name="NotifyCandidate" id="r_amount" class="float-left" type="radio">
																		<label class="float-left font_size_14 text-amount-size" for="radio-new"> $<?php echo $payableDetail->r_amount; ?></label>
																	</div>
																	<?php }else{ ?>
																		<div class="checkbox-new radio-new text-amount">
																			<input checked="" value="0" name="NotifyCandidate" id="r_amount" class="float-left" type="radio">
																			<label class="float-left font_size_14 text-amount-size" for="radio-new"> $0.0</label>
																		</div>
																	<?php } ?>
																</div>
															</div>
														</div>
														<div class="form-inline">
															<div class="row form-group">
																<?php if($payableDetail){ ?>
																<p>
																	Paying the account balance of $<?php echo $payableDetail->r_amount; ?> will pay off your account.
																</p>
																<?php }else{ ?>
																	<p>
																	Paying the account balance of $0.0 will pay off your account.
																</p>
																<?php } ?>
															</div>
														</div>
													</div>
												</div>
												<div class="col-md-4">
													<div style="background-image: url('/public/users/images/3.png');" class="border-box payment-box">
														<div class="row">
															<div class="col-md-12">
																<div class="top-title border-bootm">
																	<h3>Other Amount</h3>
																	<div class="checkbox-new radio-new other-amount">
																		<input checked="" value="-1" name="NotifyCandidate" id="rememberMe" class="float-left" type="radio">
																		<label class="float-left font_size_14 other-amount-text" for="radio-new">$ <input type="text" name="amount" id="amount" value="" placeholder=""></label>
																	</div>
																</div>
															</div>
														</div>
														<div class="form-inline">
															<div class="row form-group">
																<p>
																	The "other amount" cannot be greater than account balance nor lower than the minimum due on the account.
																</p>
															</div>
														</div>
													</div>
												</div>
											</div>
											
										</div>
										<div class="row margint-30">
											<div class="col-lg-12">
												<h3 class="simple-title">Payment Information</h3>
												<?php if($result != NULL){
												 $account = $result->account_number;
												} else { $account = ""; } ?>
												<p>The <strong>Visa</strong> credit card that you have placed on file <strong>(****-****-****-<?php echo substr($account, 12) ?>) </strong>will be debited for this payment today. If you need to use a different credit card, <a data-toggle="modal" data-target="#payment_modal" href="">please click</a> here to update your payment information.
												</p>
												<p>By clicking the "Pay Now!", you are authorizing an electronic debit from your <strong>Visa</strong> credit card. This charge will be reflected on your bank statement as an "Globalvoice Direct Club".</p>
											 
											</div>
										</div>
										<div class="row margint-30">
											<div class="col-lg-12">
												<button type="submit" class="btn btn-info btn-blue">Pay Now!</button>
											</div>
										</div>
									</div>
								</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="payment_modal" tabindex="-1" role="dialog" aria-labelledby="add_contact_individual_title" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-md" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="bill_pay_title">Payment Details</h4>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
        	<form id="myform1" method="post" action="/act_submitPayment" >
        		{{csrf_field()}}
        		<div class="credit_img_div">
        			<img src="/public/users/images/credit_img.png">
        			
        		</div>
        		<input type="hidden" name="amount1" id="amount1">
        		<div class="row">
        			<div class="col-md-12">
        				<label for="cardnumber" class="text-info">Card Number</label>
		        		<div class="input-group">
						    <input class="form-control payment_input_field" name="cardnumber" maxlength="16" value="" type="text" required>
						    <div class="input-group-append">
							    <button class="btn btn-secondary" type="button">
							        <i class="fa fa-credit-card"></i>
							    </button>
						    </div>
						</div>
        			</div>
        			
				</div>
				<div class="row">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-lg-4">
								<label for="month" class="text-info">Expiration Month</label>

								<div class="form-group">
									<select class="form-control"  name="month" id="month"> 
										<option value="0">Select Month</option>
										@for ($i = 1; $i <= 12; $i++)
										<option value="{{sprintf('%02d', $i)}}">{{sprintf("%02d", $i)}}</option>
										@endfor
									</select>
									
								</div>
							</div>
							<div class="col-lg-4">
								<label for="year" class="text-info">Expiration Year</label>

								<div class="form-group">
									<?php 
										$startYear = date("Y");
										$endYear = $startYear+10;
									?>
									<select class="form-control"  name="year" id="year"> 
										<option value="0">Select Year</option>
										@for ($i = $startYear; $i <= $endYear; $i++)
										<option value="{{$i}}">{{$i}}</option>
										@endfor
									</select>
								</div>
							</div>
							<div class="col-lg-4">
								<label for="cvv" class="text-info">CVV Code</label>
								<div class="form-group">
									<input class="form-control payment_input_field" name="cvv" value="" maxlength="3" type="text" required>
								</div>
							</div>
						</div>
					</div>
				</div>
        	</div>
        	<div class="modal-footer">
        	<button type="submit" class="btn btn-info btn-blue btn-lg">Pay Now</button>
        </form>
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>
@endsection
@push('scripts')
	<script type="text/javascript">
		$('#myform1').submit(function () {
			var val = $('#r_amount').val();
			var enterval = $('input[name=NotifyCandidate]:checked').val();
			if(enterval == -1){
		   		enterval = $('#amount').val();
		   	}
		   	$('#amount1').val(enterval);

		   	if(enterval == "")
	   		{
	   			alert("Please Some Amount in Box");
	   			return false;
	   		}
		   if(val == 0){
		   		alert("Your Current Balance is Zero");
		   		return false;
		   }
		   if(parseFloat(enterval) > parseFloat(val)){
		   		alert("Your Entered Amount is Greater than Current Balance");
		   		return false;
		   }
		   if($('#month').val() == 0 || $('#year').val() == 0){
		   	alert("Please select month or year");
		   	return false;
		   }
			
		});

		$('#myform').submit(function() {
		   var val = $('#r_amount').val();
		   var enterval = $('input[name=NotifyCandidate]:checked').val();
			if(enterval == -1){
		   		enterval = $('#amount').val();
		   	}

		   console.log(enterval);
		   console.log(val);
		   
		    if(enterval == "")
	   		{
	   			alert("Please Enter Some Amount in Box");
	   			return false;
	   		}
		   if(val == 0){
		   		alert("Your Current Balance is Zero");
		   		return false;
		   }
		   if(parseFloat(enterval) > parseFloat(val)){
		   		alert("Your Entered Amount is Greater than Current Balance");
		   		return false;
		   }

		   
		});


        var breadcrumb = $('.breadcrumb');

          breadcrumb.append('<li class="breadcrumb-item"><b><span>Bill Pay</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="/user/billPay"]').parents().addClass('active').addClass('pcoded-trigger');
	</script>
	@endpush