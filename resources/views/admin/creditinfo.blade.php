@extends('admin.layouts.layout')
@section('css')

<style>
.modal {
    display: none; /* Hidden by default */
    position: fixed; /* Stay in place */
    z-index: 999999; /* Sit on top */
    padding-top: 100px; /* Location of the box */
    left: 0;
    top: 0;
    width: 100%; /* Full width */
    height: 100%; /* Full height */
    overflow: auto !important; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
}
.dataTables_length select {
    width: 50px;
}
.img-logo {
	max-width: 500px;
}

/* Modal Content */
.modal-content {
    background-color: #fefefe;
    margin: auto;
    padding: 20px;
    border: 1px solid #888;
    width: 80%;
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
.submit-btn {
	margin: 20 0 10 0;
}
.othertracks {
	cursor: pointer;
}
table#m-info-table {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-bottom: 22px;
}
.border-box.padding-15.border-top-none-user {
    margin: 0px 30px 30px 30px;
    border-radius: 6px;
    background-color: #dfe4e7;
    padding: 30px;
    border: none;
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
                	<li class="breadcrumb-item">Credit Application</li>
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

								<div class="border-box padding-15 border-top-none-user">
									<div class="row">
										<div class="col-md-12">
											<div class="table-responsive log-table">
												<table class="table table-hover log-table" id="m-info-table">
													<thead>
														<tr>
															<th>#</th>
															<th>Name</th>
															<th>Created Date</th>
															<th>Action</th>
														</tr>
													</thead>							
													<tbody>
													@php
														$serial = 1;
													@endphp
													@foreach($creditInfo as $credit)
													
														<tr>
															<td> {{ $serial }}</td>
															<td> {{ $credit->buisness_name }}</td>
															<td>{{ $credit->created_at }}</td>
															<td><a href="dis_all_detail?fid={{ $credit->id }}" target="_blank" title="">View All Details</a></td>
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

@endsection