@extends('admin.layouts.layout')

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
                <li class="breadcrumb-item">Payments</li>
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

                <div class="tab-content responsive">
                    <div id="cinfo" class="tab-pane fade active show">
                        <div class="border-box border-top-none-table">
                            <div class="row Information-tab">
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="border-box">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="top-title border-bootm">
                                                    <h3 class="table-heading">Payments</h3>
                                                </div>
                                            </div>
                                        </div>
					<div class="border-box padding-15 border-top">
						<div class="row">
							<div class="col-md-12">
								<div class="table-responsive log-table">
									<table class="table table-hover log-table" id="producttable">
										<thead>
											<tr>
												<th>#</th>
												<th>User Login</th>
												<th>User Email</th>
												<th>Company Name</th>
												<th>Payment Gateway</th>
												<th>Date</th>
												<th>Amount</th>
												<th>Status</th>
											</tr>
										</thead>
										<tbody>
										@php
											$serial = 1;
										@endphp
										@foreach($paymentDetail as $pay)
											<tr>
												<td> {{ $serial }}</td>
												<td>{{ $pay->user_login }}</td>
												<td>{{ $pay->user_email }}</td>
												<td>{{ $pay->display_name }}</td>
												<td>
													{{ $pay->payment_gateway === 'stripe' ? 'Stripe' : ($pay->payment_gateway === 'authorize' ? 'Authorize.Net' : $pay->payment_gateway) }}
												</td>
												<td>{{ $pay->created_at }}</td>
												<td>${{ $pay->transaction_amount }}</td>
												<td><span class="text-success">Paid {{ $pay->created_at }}- Thank you!</span></td>
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
						<!-- <div class="row">
							<div class="col-md-12 text-right">
								<nav aria-label="Page navigation" class="cus-pagination">
									<ul class="pagination">
										<li class="page-item"><a class="page-link" href="#">Previous</a></li>
										<li class="page-item"><a class="page-link" href="#">1</a></li>
										<li class="page-item"><a class="page-link active" href="#">2</a></li>
										<li class="page-item"><a class="page-link" href="#">3</a></li>
										<li class="page-item"><a class="page-link" href="#">Next</a></li>
									</ul>
								</nav>
							</div>
						</div> -->
					</div>
				</div>
			</div>



		</div>


@endsection