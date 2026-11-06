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
.form-inline.form-inline-modal {
    margin-top: 15px;
    padding: 0px 120px 0px 120px;
    background-color: #ffffff;
    border-radius: 0 0 6px 6px;
}
.dataTables_length select {
    width: 50px;
}
.img-logo {
	max-width: 500px;
}
div#myModal {
    /*display: flex;*/
    flex-wrap: wrap;
    text-align: -webkit-center;
}
.forms-inline.form-inline-modal {
    margin-top: 15px;
    padding: 0px 120px 0px 120px;
    background-color: #ffffff;
    border-radius: 0 0 6px 6px;
}
span.close {
    text-align: left;
}
.col-lg-6.space-right {
    text-align: left !important;
}
strong.titles {
    font-size: 18px;
    font-weight: 700;
    color: #134576;
}
p#date {
    font-size: 15px;
}
p#b-name {
    font-size: 15px;
    text-transform: capitalize;
}
p#streetadress {
    font-size: 15px;
    text-transform: capitalize;
}
p#city {
    font-size: 15px;
    text-transform: capitalize;
}
p#state {
    font-size: 15px;
    text-transform: capitalize;
}
p#zip {
    font-size: 15px;
    text-transform: capitalize;
}
p#fname {
    font-size: 15px;
    text-transform: capitalize;
}
p#lname {
    font-size: 15px;
    text-transform: capitalize;
}
p#b-phone {
    font-size: 15px;
}
p#b-email {
    font-size: 15px;
}
p#b-hour {
    font-size: 15px;
}
p#b-website {
    font-size: 15px;
}
p#b-cat {
    font-size: 15px;
    text-transform: capitalize;
}
p#b-desc {
    font-size: 15px;
    text-transform: capitalize;
}
p#s-desc {
    font-size: 15px;
    text-transform: capitalize;
}
p#s-sites {
    font-size: 15px;
    text-transform: capitalize;
}
.img-logo {
    max-width: 500px;
    width: 100%;
    margin-bottom: 15px;
}
@media only screen
and (max-width: 1024px) {
.form-inlines.form-inline-modal {
    padding: 0px 110px 0px 110px;
}
}
@media only screen
and (max-width: 768px) {
.form-inlines.form-inline-modal {
    padding: 0px 20px 0px 20px;
}
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
                <li class="breadcrumb-item">Marketing - User Details</li>
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
													@foreach($userFormDet as $det)
														<tr>
															<td>{{ $serial }}</td>
															<td> {{ $det->buisness_name }}</td>
															<td>{{ $det->created_at }}</td>
															<td><a href="javascript:;" onclick="getMarketInfo('{{ $det->id }}')" title="">View All Details</a></td>
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

<!-- The Modal -->
<div id="myModal" class="modal">
  	<!-- Modal content -->
  	<div class="modal-content">
  	   <div clas="pull-right">
            <span class="close">&times;</span>
        </div>
        <div class="forms-inline form-inline-modal">
	 
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Date: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="date"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Name: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="b-name"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Street Address: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="streetadress"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">City: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="city"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">State: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="state"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Zip Code: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="zip"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">First Name: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="fname"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Last Name: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="lname"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Phone#: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="b-phone"></p>
				</div>
				 
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Email: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="b-email"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Hour: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="b-hour"></p>
				</div>
				 
			</div>
			<div class="row">
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Website: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="b-website"></p>
				</div>
				<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Category/Keywords: </strong></label>
				</div>
				<div class="col-lg-6 text-left">
					<p id="b-cat"></p>
				</div>
				 
			</div>
			<div class="row">
			 	<div class="col-lg-6 space-right">
					<label><strong class="titles">Business Description: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="b-desc"></p>
				</div>
				 
			</div>
			<div class="row">
			 	<div class="col-lg-6 space-right">
					<label><strong class="titles">Short Description: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="s-desc"></p>
				</div>
				 
			</div>
			<div class="row">
			 	<div class="col-lg-6 space-right">
					<label><strong class="titles">Social Sites: </strong></label>
				</div>
				<div class="col-lg-6 clearfix text-left">
					<p id="s-sites"></p>
				</div>
				 
			</div>
			<div id="image_log">
			 	
			</div>
    	</div>
    </div>
</div>


@endsection
@section('script')
<script type="text/javascript">
	$(document).ready(function(){
		$('#m-info-table').DataTable();
	});
</script>
@endsection