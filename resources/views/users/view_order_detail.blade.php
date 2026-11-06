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
     
     <div class="col-lg-12 ">
	        <div class="card">
	            <div class="card-header">
	            </div>
	            <div class="card-body">
		             <div class="row mb-4">
		                 <div class="col-sm-6 text-center">
		                     <h5 class="mb-3">Shipping Address:</h5>
		                     <h3 class="text-dark mb-1">{{$orderDet->OrderShipName}}</h3>
		                     <div>{{$orderDet->OrderShipAddress}}</div>
		                     <div>{{$orderDet->OrderEmail}}</div>
		                     <div>Phone: {{$orderDet->OrderPhone}}</div>
		                 </div>
		                 <div class="col-sm-4 text-center">
		                 	<h3 class="mb-3">Payment Status:</h3>
		                 	@if($orderDet->paid == 1)
		                 		<h5 class="text-success mb-1">Paid</h5>
		                 	@else
		                 		<h5 class="text-danger mb-1">Unpaid</h5>
		                 	@endif
		                 	
		                 </div>
		             </div>
		             <div class="table-responsive-sm" style="margin-left: 10%!important;">
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
		                 <div class="col-xl-10 col-md-11 col-sm-12">
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

	var breadcrumb = $('.breadcrumb');

    breadcrumb.append('<li class="breadcrumb-item"><b><span>View Order Detail</span></b></li>');
    // $('.main-menu ul').find('.active').removeClass('active');
    // $('.main-menu').find('a[href="/user/order"]').parents().addClass('active');

</script>
@endpush