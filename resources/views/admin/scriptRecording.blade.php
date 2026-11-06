@extends('admin.layouts.layout')
@section('css')
<style>
span.script-recording-disapprove {
    color: #ff4f00;
    font-size: 16px;
    font-weight: 800;
}
span.script-recording-approve {
    color: #37bf8d;
    font-size: 16px;
    font-weight: 800;
}
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
div#myModal {
    /*display: flex;*/
    flex-wrap: wrap;
    text-align: -webkit-center;
}.form-inlines.form-inline-modal {
    margin-top: 15px;
    padding: 0px 110px 0px 200px;
    background-color: #ffffff;
    border-radius: 0 0 6px 6px;
    margin-bottom: 15px;
}
strong.title {
    font-size: 18px;
    font-weight: 700;
    color: #134576;
}
p#date {
    font-size: 16px;
}
p#maingreetingscript {
    font-size: 16px;
    text-transform: capitalize;
}
p#after-hours-greeting, p#hold-script, p#special_pronunciation_words {
    font-size: 16px;
}
p#message-hold-scripts {
    font-size: 16px;
}
span.close {
    text-align: left;
}
.col-md-6.space-right {
    text-align: left !important;
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
#script-recording {
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
                <li class="breadcrumb-item">Script - Recording Details</li>
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
												<table class="table table-hover log-table" id="script-recording-table">
													<thead>
														<tr>
															<th>#</th>
															<th>Created Date</th>
															<th>User Login</th>
															<th>User Email</th>
															<th>Company Name</th>
															<th>View Detail</th>
															<th>Action</th>

														</tr>
													</thead>							
													<tbody>
													@php
														$serial = 1;
													@endphp
													@foreach($userDet as $det)
													
														<tr>
															<td>{{ $serial }}</td>
															<td>{{ $det->created_at }}</td>
															<td>{{ $det->user_login }}</td>
															<td>{{ $det->user_email }}</td>
															<td>{{ $det->display_name }}</td>
															<td><a href="javascript:;" onclick="getScriptInfo('{{ $det->id }}')" title="">View All Details</a></td>
															<td>
																@if($det->active == 1)
																	<a class="" href="/admin/approvescriptrecording?sid={{ $det->id }}"title="">Approve</a>
																	<a class="" href="/admin/removescriptrecording?sid={{ $det->id }}"title="">DisApprove</a>
																@elseif($det->active == 0)
																	 <strong><span class="script-recording-disapprove">disapproved</span></strong>
																@elseif($det->active == 2)
																	 <strong><span class="script-recording-approve">Approved</span></strong>
																@endif
															</td>
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
	  	<div class="pull-right">
		    <span class="close">&times;</span>
		</div>
		<div class="form-inlines form-inline-modal">
			 
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">Date: </strong></label>
				</div>
				<div class="col-md-6 clearfix text-left">
					<p id="date"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">Main greeting script: </strong></label>
				</div>
				<div class="col-md-6 text-left">
					<p id="maingreetingscript"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">After-hours greeting script: </strong></label>
				</div>
				<div class="col-md-6 clearfix text-left">
					<p id="after-hours-greeting"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">Hold Script: </strong></label>
				</div>
				<div class="col-md-6 clearfix text-left">
					<p id="hold-script"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">Special Pronunciation Words: </strong></label>
				</div>
				<div class="col-md-6 text-left">
					<p id="special_pronunciation_words"></p>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 space-right">
					<label><strong class="title">Audio: </strong></label>
				</div>
				<div class="col-md-6 text-left" id="audioplayer">
					<!-- <audio controls id="audioid">
						<source id="audio_file_path" src="" type="audio/mpeg">
					</audio> -->
				</div>
			</div>
		</div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(document).ready(function(){
		$('#script-recording-table').DataTable();
	});
</script>
@endsection