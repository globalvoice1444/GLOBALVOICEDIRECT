@extends('layouts.user.user_main')
@section('content')
	<style type="text/css">
		body {
		    background: #f5f5f5
		}

		.rounded {
		    border-radius: 1rem
		}

		.nav-pills .nav-link {
		    color: #555
		}

		.nav-pills .nav-link.active {
		    color: white
		}

		input[type="radio"] {
		    margin-right: 5px
		}

		.bold {
		    font-weight: bold
		}
		span.input-group-text.text-muted {
		    padding: 12px 0 12px 0;
		    margin-top: 0;
		}
	</style>
	<div class="container py-5">
    <!-- For demo purpose -->
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto text-center">
             
        </div>
    </div> <!-- End -->
    <div class="row">
        <div class="col-lg-6 ">
            <div class="card ">
                <div class="card-header">
                    <div class="bg-white shadow-sm pt-4 pl-2 pr-2 pb-2">
                        <!-- Credit card form tabs -->
                        <ul role="tablist" class="nav bg-light nav-pills rounded nav-fill mb-3">
                            <li class="nav-item"> <a data-toggle="pill" href="#credit-card" class="nav-link active "> <i class="fas fa-credit-card mr-2"></i> Credit Card </a> </li>
                            @if(false)
                            <!-- For Future Use -->
                           <!--  <li class="nav-item"> <a data-toggle="pill" href="#paypal" class="nav-link "> <i class="fab fa-paypal mr-2"></i> Paypal </a> </li>
                            <li class="nav-item"> <a data-toggle="pill" href="#net-banking" class="nav-link "> <i class="fas fa-mobile-alt mr-2"></i> Net Banking </a> </li> -->
                            @endif
                        </ul>
                    </div> <!-- End -->
                    <!-- Credit card form content -->
                    <div class="tab-content">
                        <!-- credit card info-->
                        <div id="credit-card" class="tab-pane fade show active pt-3">
                            <form role="form" id="unpaid_payment_form" method="post">
                                <div class="form-group"> <label for="username">
                                        <h6>Card Owner</h6>
                                    </label> <input type="text" name="cardname" id="cardname" placeholder="Card Owner Name" required class="form-control "> </div>
                                <div class="form-group"> <label for="cardNumber">
                                        <h6>Card number</h6>
                                    </label>
                                    <div class="input-group"> <input type="text" id="cardNumber" name="cardNumber" placeholder="Valid card number" class="form-control " required maxlength="16" minlength="13">
                                        <div class="input-group-append"> <span class="input-group-text text-muted"> <i class="fab fa-cc-visa mx-1"></i> <i class="fab fa-cc-mastercard mx-1"></i> <i class="fab fa-cc-amex mx-1"></i> </span> </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-8">
                                        <div class="form-group"> <label><span class="hidden-xs">
                                                    <h6>Expiration Date</h6>
                                                </span></label>
                                            <div class="input-group"> <input type="number" placeholder="MM" name="month" id="month" class="form-control" required> <input type="number" placeholder="YYYY" name="year" id="year" class="form-control" required> </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-4">
                                        <div class="form-group mb-4"> <label data-toggle="tooltip" title="Three digit CV code on the back of your card">
                                                <h6>CVV <i class="fa fa-question-circle d-inline"></i></h6>
                                            </label> <input type="text" name="cvv" id="cvv" required class="form-control"> </div>
                                    </div>
                                </div>
                                <div class="card-footer"> <button type="submit" class="subscribe btn btn-primary btn-block shadow-sm"> Confirm Payment </button>
                            </form>
                        </div>
                    </div> <!-- End -->
                    @if(false)
                    <!-- Paypal info -->
                    <!-- <div id="paypal" class="tab-pane fade pt-3">
                        <h6 class="pb-2">Select your paypal account type</h6>
                        <div class="form-group "> <label class="radio-inline"> <input type="radio" name="optradio" checked> Domestic </label> <label class="radio-inline"> <input type="radio" name="optradio" class="ml-5">International </label></div>
                        <p> <button type="button" class="btn btn-primary "><i class="fab fa-paypal mr-2"></i> Log into my Paypal</button> </p>
                        <p class="text-muted"> Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website to view details of your order. </p>
                    </div>   -->
                    <!-- bank transfer info -->
                   <!--  <div id="net-banking" class="tab-pane fade pt-3">
                        <div class="form-group "> <label for="Select Your Bank">
                                <h6>Select your Bank</h6>
                            </label> <select class="form-control" id="ccmonth">
                                <option value="" selected disabled>--Please select your Bank--</option>
                                <option>Bank 1</option>
                                <option>Bank 2</option>
                                <option>Bank 3</option>
                                <option>Bank 4</option>
                                <option>Bank 5</option>
                                <option>Bank 6</option>
                                <option>Bank 7</option>
                                <option>Bank 8</option>
                                <option>Bank 9</option>
                                <option>Bank 10</option>
                            </select> </div>
                        <div class="form-group">
                            <p> <button type="button" class="btn btn-primary "><i class="fas fa-mobile-alt mr-2"></i> Proceed Pyment</button> </p>
                        </div>
                        <p class="text-muted">Note: After clicking on the button, you will be directed to a secure gateway for payment. After completing the payment process, you will be redirected back to the website to view details of your order. </p>
                    </div>   -->
                    <!-- End -->
                    @endif
                </div>
            </div>
        </div>

    </div>
     <div class="col-lg-6 ">
	        <div class="card">
	            <div class="card-header">
	            </div>
	            <div class="card-body">
		             <div class="row mb-4">
		                 <div class="col-sm-6 ">
		                     <h5 class="mb-3">Shipping Address:</h5>
		                     <h3 class="text-dark mb-1">{{$orderDet->OrderShipName}}</h3>
		                     <div>{{$orderDet->OrderShipAddress}}</div>
		                     <div>{{$orderDet->OrderEmail}}</div>
		                     <div>Phone: {{$orderDet->OrderPhone}}</div>
		                 </div>
		             </div>
		             <div class="table-responsive-sm">
		                 <table class="table table-striped table-responsive">
		                     <thead>
		                         <tr>
		                             <th class="center">#</th>
		                             <th>Item</th>
		                             <th>Description</th>
		                             <th class="right">Price</th>
		                             <th class="center">Qty</th>
		                             <th class="right">Total</th>
		                         </tr>
		                     </thead>
		                     <tbody>
		                     	@foreach( $orderProducts as $product)
		                         <tr>
		                             <td class="center">1</td>
		                             <td class="left strong">{{$product->ProductName}}</td>
		                             <td class="left">{{$product->ProductShortDesc}}</td>
		                             <td class="right">${{$product->ProductPrice}}</td>
		                             <td class="center">{{$product->quantity}}</td>
		                             <td class="right">${{$product->order_price}}</td>
		                         </tr>
		                        @endforeach
		                     </tbody>
		                 </table>
		             </div>
		             <div class="row">
		                 <div class="col-lg-12 col-sm-12">
		                     <table class="table table-clear">
		                         <tbody class="float-right">
		                             <tr>
		                                 <td class="left">
		                                     <strong class="text-dark">Subtotal</strong>
		                                 </td>
		                                 <td class="right">${{$orderDet->OrderAmount}}</td>
		                             </tr>
		                             
		                             <tr>
		                                 <td class="left">
		                                     <strong class="text-dark">Total</strong> </td>
		                                 <td class="right">
		                                     <strong class="text-dark">${{$orderDet->OrderAmount}}</strong>
		                                 </td>
		                             </tr>
		                         </tbody>
		                     </table>
		                 </div>
		             </div>
		         </div>
	        </div>
    	</div>


@endsection

@push('scripts')
<script type="text/javascript">

	$(function() {
		$('[data-toggle="tooltip"]').tooltip()
	});

</script>
<script type="text/javascript">
	$(document).ready(function(){

		$('#unpaid_payment_form').submit(function(){
			 $.ajax({
			 	url:'/user/submit_unpaid_payment',
			 	type:'post',
			 	data: {"_token": "{{ csrf_token() }}",'cardName':$('#cardname').val(), 'cardNumber':$('#cardNumber').val(), 'month':$('#month').val(), 'year':$('#year').val(), 'cvv': $('#cvv').val(), 'oid':'{{$orderDet->OrderID}}'},
			 	success: function(response){
			 		reurnRes = response;
			 		if(reurnRes.success == 'true'){
			 			window.location.replace("/user/order?message="+reurnRes.result);
			 			//alert(reurnRes.result);
			 		}else{
			 			//window.location.replace("/user/order?message="+reurnRes.result);
			 			alert(reurnRes.result);
			 		}
			 	},
			 });
			
			return false;
		});
	});
</script>

<script type="text/javascript">

	var breadcrumb = $('.breadcrumb');

    breadcrumb.append('<li class="breadcrumb-item"><b><span>Payment</span></b></li>');
    // $('.main-menu ul').find('.active').removeClass('active');
    // $('.main-menu').find('a[href="/user/order"]').parents().addClass('active');

</script>
@endpush