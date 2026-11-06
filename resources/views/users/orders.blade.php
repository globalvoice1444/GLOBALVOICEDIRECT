@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
	<div class="col-sm-12">
		@if(isset($_GET['message']))
			<div class="alert alert-info alert-dismissible fade show" role="alert">
			  {{$_GET['message']}}
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
			    <span aria-hidden="true">×</span>
			  </button>
			</div>
		@endif
		<div class="card">

			<div class="card-header">
				<h5>Orders</h5>
			</div>
			<div class="card-block" id="orders_card">
				<div class="row">
					<div class="col-md-12">
						<div id="wizard">
							<section>
								<table class="table table-responsive" id="orders-table">
									<thead>
										<tr>
											<th>#</th>
											<th>Date</th>
											<th>Status</th>
											<th>Tracking Number</th>
											<th>Action</th>
										</tr>
									</thead>
									<tbody>
										<?php $count = count($orderDet); ?>
										@if($count > 0)
											<?php $serial = 1; ?>
											@foreach($orderDet as $order)
											<tr>
												<td>{{ $serial }}</td>
												<td>{{ $order->OrderDate }}</td>
												<td>
													@if($order->paid == 0)
														<p class="text-danger">Unpaid</p>
													@else
														<p class="text-success">Paid</p>
													@endif
												</td>
												@if($order->paid == 0)
													<td><a class="text-danger" href="/user/unpaid_order/{{$order->OrderID}}" target="_blank" title="Send Email To Ask for Payment">Please Complete the Order Payment</a></td>
												@else
													@if($order->order_id)
														<td>{{ strtoupper($order->company_name) }} /
															@if($order->company_name == 'usps')
																<a href="https://tools.usps.com/go/TrackConfirmAction?qtc_tLabels1={{ $order->tracking_number }}" target="_blank" title="">
															@elseif($order->company_name == 'fedex')
																<a href="https://www.fedex.com/apps/fedextrack/?action=track&tracknumbers{{ $order->tracking_number }}&locale=en_US&cntry_code=ca_english" target="_blank" title="">
															@else
																<a href="javascript:;" class="othertracks" onclick="gettrackstatus('{{ $order->company_name }}', '{{ $order->tracking_number }}')" data-toggle="modal" data-target="#myModal2" title="">
															@endif
															{{ $order->tracking_number }}</a>
														</td>
													@else
													<td>N/A</td>
													@endif
												@endif
												<td><a class="text-primary" href="/user/view_order_detail/{{$order->OrderID}}" target="_blank">View Detail</a></td>
											</tr>
											<?php $serial = $serial + 1; ?>
											@endforeach
										@endif
									</tbody>
								</table>
							</section>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
@push('scripts')
<script type="text/javascript">

	var breadcrumb = $('.breadcrumb');

    breadcrumb.append('<li class="breadcrumb-item"><b><span>Orders</span></b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/user/order"]').parents().addClass('active');
	$(document).ready( function () {

	    ordertable = $('#orders-table').DataTable();

	} );

</script>
@endpush