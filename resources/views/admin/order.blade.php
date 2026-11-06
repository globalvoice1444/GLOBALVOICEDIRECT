@extends('admin.layouts.layout')
@section('css')
<style>

	.modal {
    padding-top: 15px; /* Location of the box */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
    overflow: auto;
}
.table-responsive{
	background-color: transparent;
}
/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80% !important;
}

/* The Close Button */
.close {
    color: #aaaaaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
}

.close:hover,
.close:focus {
    color: #000;
    text-decoration: none;
    cursor: pointer;
}
#log-table a {
    margin-right: 10px;
}
strong.titles {
    font-size: 18px;
    font-weight: 700;
    color: #134576;
}
label {
    justify-content: unset !important;
}
</style>
@endsection
@section('content')


<div class="page-header">
	<div class="page-block">
	    <div class="row align-items-center">
	        <div class="col-md-12">
	         	<ul class="breadcrumb">
	            	<li class="breadcrumb-item">
		                <a href="/">
		                  	<i class="feather icon-home"></i>
		                </a>
		            </li>
	            	<li class="breadcrumb-item">Orders</li>
	         	</ul>
	      	</div>
	   	</div>
	</div>
</div>
<div class="admin-content">
  	<div class="pcoded-inner-content">
   		<div class="main-body">
			<div class="page-wrapper">
      			<div class="page-body">
        			<div class="row">
          				<div class="col-sm-12">
           					<div class="card">
				            	<div class="card-header">
				             	</div>

							    @if(session()->has("message"))
									<div class="alert alert-info">
					  					{{ session()->get("message") }}
									</div>
								@endif

								<div class="tab-content responsive">
									<div id="cinfo" class="tab-pane fade active show">
									    <div class="border-box border-top-none-table">
									        <div class="row Information-tab">
									            <div class="col-lg-12 col-md-12 col-sm-12">
									                <div class="border-box">
									                    <div class="row">
									                        <div class="col-md-12">
									                            <div class="top-title border-bootm">
									                                <h3 class="table-heading">Orders</h3>
									                            </div>
									                        </div>
									                    </div>
														<div class="border-box padding-15 border-top-none">
															<div class="row">
																<div class="col-md-12">
																	<div class="table-responsive log-table sa">
																		<table class="table table-hover log-table dataTable" id="producttable">
																			<thead class="top-text">
																				<tr>
																					<th>#</th>
																				  
																					<th>User Login</th>
																					<th>User Email</th>
																					<th>Company Name</th>
																					<th>Status</th>
																					<th>Date</th>
																					
																					<th>Tracking Number</th>
																					<th>Action</th>
																					<th>View Detail</th>
																					<th>Order Slip</th>
																				</tr>
																			</thead>
																			<tbody>
																			@php
																				$serial = 1;
																			@endphp
																			@foreach($orders as $ord)
																				<tr>
																					<td>{{ $serial }}</td>
																					<td>{{ $ord->user_login }}</td>
																					<td>{{ $ord->user_email }}</td>
																					<td>{{ $ord->display_name }}</td>
																					<td>
																						@if($ord->paid==1)
																							<p class="text-success">PAID</p>
																						@else
																							<p class="text-danger">Unpaid</p>
																						@endif

																					</td>
																					<td>{{ $ord->OrderDate }}</td>
																					
																						

																						@if($trackDetail[$serial-1] != null)

																						
																							<td>
																								{{ strtoupper($trackDetail[$serial-1]->company_name) }} / 
																								 
																								@if($trackDetail[$serial-1]->company_name == 'usps')
																									<a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1={{ $trackDetail[$serial-1]->tracking_number }}" target="_blank" title="">
																								@elseif($trackDetail[$serial-1]->company_name == 'fedex') 
																									<a href="https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers={{ $trackDetail[$serial-1]->tracking_number }}&locale=en_US&cntry_code=ca_english" target="_blank" title=""> 
																								@else 
																									<a href="javascript:;" class="othertracks" onclick="gettrackstatus('{{ $trackDetail[$serial-1]->company_name }}', '{{ $trackDetail[$serial-1]->tracking_number }}')" data-toggle="modal" data-target="#myModal2" title=""> 
																								@endif
																								{{ $trackDetail[$serial-1]->tracking_number }} 

																								</a></td>

																						 @else
																						 	<td>N/A</td>
																						 @endif
																					
																					@php 
																						$ordetId = $ord->OrderID;
																					@endphp
																					<td>
																						@if($ord->paid==1)
																							<a href="javascript:;" onclick="addTrackNumber('{{ $ordetId }}')" data-toggle="modal" data-target="#myModal1" title="">Assign Track Number</a>
																						@else
																							<a class="text-danger" href="" target="_blank" title="Send Email To Ask for Payment">Send Email To Ask for Payment</a>
																						@endif
																					</td>
																					<td><a href="javascript:;" onclick="updateOrder('{{ $ordetId }}')" title="">View Order Details</a></td><td> <a href="orderslip?id={{ $ordetId }}" title="Order Slip" target="_blank">Order Slip</a></td>
																				</tr>
																				@php
																					$serial++;
																				@endphp
																			@endforeach
																				 
																			</tbody>
																		</table>
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
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

	<!-- The Modal -->
	
<div id="myModal" class="modal">
	<!-- Modal content -->
	<div class="modal-content">
	  	<div class="pull-right"><span class="close">&times;</span></div>
		<form method="post" action="action/act_updateProduct.php" enctype="multipart/form-data">	
			<input type="hidden" name="productId" id="productId" value="">		 
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Name: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="bname"></p>
				</div>
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Order Tracking Number: </strong></label>
				</div>
				<div class="col-lg-4 text-left">
					<p id="trackno"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Order Amount: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="amount"></p>
				</div>
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Down Payment: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="downpayment"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Phone#: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="phone"></p>
				</div>
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Order Email: </strong></label>
				</div>
				<div class="col-lg-4 text-left">
					<p id="email"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">City: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="city"></p>
				</div>
				<div class="col-lg-2 space-right">
					<label><strong class="titles">State: </strong></label>
				</div>
				<div class="col-lg-4 text-left">
					<p id="state"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Country: </strong></label>
				</div>
				<div class="col-lg-4 clearfix text-left">
					<p id="country"></p>
				</div>
				<div class="col-lg-2 space-right">
					<label><strong class="titles">ZIP: </strong></label>
				</div>
				<div class="col-lg-4 text-left">
					<p id="Zip"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-2 space-right">
					<label><strong class="titles">Order Date: </strong></label>
				</div>
				<div class="col-lg-4 text-left">
					<p id="date"></p>
				</div>
			</div>
			<div class="row">
			 	<div class="col-lg-2 space-right">
					<label><strong class="titles">Sipping Address1: </strong></label>
				</div>
				<div class="col-lg-8 clearfix text-left">
					<p id="address1"></p>
				</div> 
			</div>
			<div class="row">
			 	<div class="col-lg-2 space-right">
					<label><strong class="titles">Shipping Address2: </strong></label>
				</div>
				<div class="col-lg-8 clearfix text-left">
					<p id="address2"></p>
				</div>
			</div>
			<div class="row">
			 	<table class="table table-hover log-table table-responsive">
			 		<thead>
						<tr>
							<th>#</th>
							<th>Product Name</th>
							<th>Product Price</th>
							<th>Product Description</th>
							<th>Quantity</th>
							<th>Subtotal</th>
						</tr>
					</thead>
					<tbody id="orderDet">
						
					</tbody>
			 	</table>
			</div>
			<div class="row margint-30">
				<div class="col-lg-4">
					<a type="submit" id="url1" href="updateOrderStatus?status=1" class="btn btn-info btn-blue">Shipped</a>
				</div>
				<div class="col-lg-4">
					<a type="submit" id="url2" href="updateOrderStatus?status=2" class="btn btn-info btn-blue">Completed</a>
				</div>
			</div>
		</form>
	</div>
</div>

<div id="myModal1" class="modal fade" role="dialog">
    <div class="modal-dialog">
	    <!-- Modal content-->
	    <div class="modal-content">
		    <div class="modal-header">
		        <h4 class="modal-title">Assign A Track Number</h4>
		         <button type="button" class="close" data-dismiss="modal">&times;</button>
		    </div>
		    <div class="modal-body">
		      	<form method="post" action="assigntrackno">
		      		<input type="hidden" name="_token" value="{{ csrf_token() }}">
		      		<input type="Hidden" name="orderid" id="orderid" value="" />
		      		<div class="row">
			         	<label><strong class="titles">Track Number: </strong></label>
			         	<input type="text" class="form-control" name="tracknumber" id="tracknumber" required />
			        </div>
			        <div class="row">
			        	<label><strong class="titles">Parcel Agency: </strong></label>
			        	<select id="parcelAg" class="form-control" name="parcelAg">
			        		<option value="usps">USPS</option>
			        		<option value="usp">USP</option>
			        		<option value="fedex" >Fedex</option>
			        	</select>
			        </div>
			        <div class="row">
			        	<input type="submit" class="submit-btn btn btn-primary" name="assignTrackNo" value="Submit" />
			        </div>
		      	</form>
		    </div>
		    <div class="modal-footer">
		        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
		    </div>
	    </div>
	</div>
</div>

<div id="myModal2" class="modal fade" role="dialog">
    <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
	        <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Order Status</h4>
		    </div>
	        <div id="track_status" class="modal-body">
	      		<div class="row">
		         	<label>Status Not Available</label>
		        </div>
	        </div>
	        <div class="modal-footer">
	        	<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	        </div>
	    </div>

	</div>
</div>

@endsection
@section('script')

<script type="text/javascript">
	$('#producttable').DataTable({
	    "order": [[ 5, "desc" ]], //or asc 
	    "columnDefs" : [{"targets":5, "type":"date-eu"}],
	});
</script>

@endsection