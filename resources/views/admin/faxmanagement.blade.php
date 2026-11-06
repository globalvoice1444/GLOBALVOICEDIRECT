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
    overflow: auto; /* Enable scroll if needed */
    background-color: rgb(0,0,0); /* Fallback color */
    background-color: rgba(0,0,0,0.4); /* Black w/ opacity */
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
.dataTables_length select {
    width: 55px;
}
table#userfaxdet {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-bottom: 22px;
}
table#userupdatefaxdet {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-top: 22px !important;
    margin-bottom: 22px;
}
.form-inlines-form {
    padding: 20px 30px 20px 30px;
    border-radius: 0 0 6px 6px;
}
a.btn.btn-danger.create-fax-acc {
    margin-right: 10px;
}
@media only screen and (max-device-width: 1024px) and (min-device-width: 768px){
.nav.nav-pills {
    height: 0px !important;
}
}
@media only screen and (max-device-width: 812px) and (min-device-width: 375px){
.nav.nav-pills {
    height: 0px !important;
}
}

@media only screen and (max-device-width: 568px) and (min-device-width: 320px){
.nav.nav-pills {
    height: 125px !important;
}
}
@media only screen and (max-device-width: 736px) and (min-device-width: 414px){
.nav.nav-pills {
    height: 125px !important;
}
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
                    <li class="breadcrumb-item">Fax Management</li>
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
									<div class="card-block" id="profile_card">
            							<div class="row">
								           <div class="col-md-12">
								              <div id="wizard">
								                 <section>
								                    <div class="container">
								                         <ul class="nav nav-pills">
								                        <li><a data-toggle="tab" href="#cinfo" class="active show">Create New Fax Account</a></li>
								                        <li><a data-toggle="tab" href="#cc-info">Update DID User</a></li>
								                         </ul>
								                    </div> 
								                </section> 
								                </div> 
								            </div> 
	        							</div> 
	    							</div>

									@if(session()->has("message"))
										<div class="alert alert-info">
						  					{{ session()->get("message") }}
										</div>
									@endif
									<div class="tab-content responsive">

										 <!-- Create Fax Account -->
										<div id="cinfo" class="tab-pane fade active show">
											<form method="post" action="action/act_updateuser.php">
												<div class="border-box border-top-none-form">
													<div class="row Information-tab">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<div class="border-box">
																<div class="row">
																	<div class="col-md-12">
																		<div class="top-title border-bootm">
																			<h3 class="table-heading">Create Fax Account of User</h3>
																		</div>
																	</div>
																</div>
																<div class="form-inline-form">
																	 
																	 
																	<div class="table-responsive log-table">
																		<table class="table table-hover log-table" id="userfaxdet">
																			<thead class="top-text">
																				<tr>
																					<th>#</th>
																					<th>Name</th>
																					<th>User Login</th>
																					<th>User Email</th>
																					<th>Display Name</th>
																					<th>Action</th>
																				</tr>
																			</thead>
																			<tbody>
																			@if($userFaxDet != null)
																				@php
																					$serial = 1;
																				@endphp
																				@foreach($userFaxDet as $userfaxDet)
																				<tr>

																					<td>{{ $serial }}</td>
																					<td>{{ $userfaxDet->user_nicename }}</td>
																					<td>{{ $userfaxDet->user_login }}</td>
																					<td>{{ $userfaxDet->user_email }}</td>
																					<td>{{ $userfaxDet->display_name }}</td>
																					@php 
																						$valll =  $userfaxDet->ID;

																					@endphp
																					<td><a type="button" href="javascript:;" onclick='createfaxmodal("{{ $valll }}")' data-toggle="modal" data-target="#createfaxaccount"  class="btn btn-danger create-fax-acc">Create Fax Account</a><a type="button" href="javascript:;" onclick='createexistingfaxmodal("{{ $valll }}")' data-toggle="modal" data-target="#createexistingfaxaccount"  class="btn btn-danger">Existing Fax Account</a></td>
																					 
																				</tr>
																				@php 
																					$serial = $serial + 1;
																				@endphp
																				@endforeach
																			@endif	 
																			</tbody>
																			<tfoot>
																	            <tr class="bottom-text">
																	                <th>#</th>
																					<th>Name</th>
																					<th>User Login</th>
																					<th>User Email</th>
																					<th>Display Name</th>
																					<th>Action</th>
																	            </tr>
																	        </tfoot>
																		</table>
																	</div>
																	 
																</div>
															</div>
														</div>
														 
													</div>
												</div>
											</form>
										</div>


										<!-- Update Fax Account -->
										<div id="cc-info" class="tab-pane fade">
											<form method="post" action="action/act_updateuser.php">
												<div class="border-box border-top-none-form">
													<div class="row Information-tab">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<div class="border-box">
																<div class="row">
																	<div class="col-md-12">
																		<div class="top-title border-bootm">
																			<h3 class="table-heading">Update DiD(Fax Number)</h3>
																		</div>
																	</div>
																</div>
																<div class="table-responsive log-table">
																	<table class="table table-hover log-table" id="userupdatefaxdet">
																		<thead class="top-text">
																			<tr>
																				<th>#</th>
																				<th>Name</th>
																				<th>User Name</th>
																				<th>Email</th>
																				<th>DID</th>
																				<th>Created At</th>
																				<th>Action</th>
																			</tr>
																		</thead>
																		<tbody>
																		@if($faxDet != null)
																			@php
																				$serial = 1;
																			@endphp
																			@foreach($faxDet as $rowData)
																			<tr>

																				<td>{{ $serial }}</td>
																				<td>{{ $rowData->user_nicename }}</td>
																				<td>{{ $rowData->user_login }}</td>
																				<td>{{ $rowData->user_email }}</td>
																				<td>{{ $rowData->did }}</td>
																				<td>{{ $rowData->created_at }}</td>
																				<td>
																				    <!--<a href="javascript:;" onclick="updatefaxmodal('{{ $rowData->user_id }}')" data-toggle="modal" data-target="#updatedid" class="btn btn-danger create-fax-acc">Update DID Number</a>-->
																				    <a href="javascript:;" onclick="updateRegfaxmodal('{{ $rowData->user_id }}')" data-toggle="modal" data-target="#updateRegisteredId" class="btn btn-danger">Existing DID Number</a></td>
																			</tr>

																			@php 
																				$serial = $serial + 1; 
																			@endphp
																			@endforeach
																		@endif
																		</tbody>
																	</table>
																	 
																</div>
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
			</div>
				 
		</div>
	</div>
 

	<!-- Modal reassign Fax Number -->
	<div id="updatedid" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update Fax DID</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<!-- <h4 class="modal-title">Modal Header</h4> -->
			</div>
	      	<div class="modal-body">
		        <form method="post" action="updatefaxacc" >
		        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        	<input type="Hidden" name="update_id" id="update_id">
					<div class="form-group">
						<div class="form-group form-primary">
							<label class="float-label">Select State</label>
							<select class="form-control statedet" name="state">
								<option value="-1">select state</option>
								@foreach ($stateData as $value)
									<option value="{{ $value }}">{{ $value }}</option>
								@endforeach
							</select>
							<span class="form-bar"></span>
							<!-- <label class="float-label">State</label> -->
						</div>
						<div class="form-group form-primary">
							<label class="float-label">Select Fax Number</label>
							<select class="form-control faxesdid" name="did" id="did1">
								<option value="-1">select Fax Number</option>
							</select>
							<span class="form-bar"></span>
							<!-- <label class="float-label">To</label> -->
						</div>
					</div>
					<div class="mt-20 form-group form-primary">
						<button type="submit" class="btn btn-info btn-block btn-xs waves-effect waves-light create_acc_btn">
							<i class="fa fa-paper-plane"></i> Update DID
						</button>
					</div>
				</form>
	      	</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>

	  </div>
	</div>

	<!-- Modal Update Existing Fax Account with already registered number -->
	<div id="updateRegisteredId" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Update Fax Account</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
			</div>
	      	<div class="modal-body">
		        <form method="post" action="upd_RegDidfaxacc" >
		        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        	<input type="Hidden" name="new_reg_id" id="new_reg_id">
					<div class="form-group form-primary">
						<label class="float-label">Name</label>
						<input type="text" name="accountName" id="accountNameexistUpdate" class="form-control" value=""  required readonly />
						
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Email</label>
						<input type="email" name="accountEmail" id="accountEmailexistUpdate" class="form-control" value="" required readonly />
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Number</label>
						<input type="text" name="account_number_update" id="accountNumberUpdate" maxlength="10" minlength="10" class="form-control" value=""  required />
					</div>
					<div class="mt-20 form-group form-primary">
						<button type="submit" class="btn btn-info btn-block btn-xs waves-effect waves-light create_acc_btn">
							<i class="fa fa-paper-plane"></i> Update DID
						</button>
					</div>
				</form>
	      	</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
			</div>
	    </div>

	  </div>
	</div>

	<!-- Modal Create Fax Account -->
	<div id="createfaxaccount" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Fax Account</h4>
				<button type="button" class="close" data-dismiss="modal" onclick="reset()">&times;</button>
			</div>
	      	<div class="modal-body">
		        <form method="post" action="act_newfaxacc" >
		        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        	<input type="Hidden" name="new_id" id="new_id">
					<div class="form-group">
						<div class="form-group form-primary">
							<label class="float-label">Area Code</label>
							<div class="input-group">
                              <input type="text" id="localareacode" class="form-control" placeholder="561 or 855" aria-label="Search">
                              <button class="btn btn-primary statedet" type="button">Search</button>
                            </div>
							 
							<span class="form-bar"></span>
							<!-- <label class="float-label">State</label> -->
						</div>
						<div class="form-group form-primary">
							<label class="float-label">Select Fax Number</label>
							<select class="form-control faxesdid" name="did" id="did">
								<option value="-1">select Fax Number</option>
							</select>
							<span class="form-bar"></span>
							<!-- <label class="float-label">To</label> -->
						</div>
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Name</label>
						<input type="text" name="accountName" id="accountName" class="form-control" value=""  required readonly />
						
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Email</label>
						<input type="email" name="accountEmail" id="accountEmail" class="form-control" value="" required readonly />
					</div>
					<div class="mt-20 form-group form-primary">
						<button type="submit" class="btn btn-info btn-block btn-xs waves-effect waves-light create_acc_btn">
							<i class="fa fa-paper-plane"></i> Create Account
						</button>
					</div>
				</form>
	      	</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal" onclick="reset()">Close</button>
			</div>
	    </div>

	  </div>
	</div>
	<!-- Modal Create Existing Fax Account -->
	<div id="createexistingfaxaccount" class="modal fade" role="dialog">
	  <div class="modal-dialog">

	    <!-- Modal content-->
	    <div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title">Create Fax Account</h4>
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				
			</div>
	      	<div class="modal-body">
		        <form method="post" action="act_newexistingfaxacc" >
		        	<input type="hidden" name="_token" value="{{ csrf_token() }}">
		        	<input type="Hidden" name="new_existint_id" id="new_existint_id">
					<div class="form-group form-primary">
						<label class="float-label">Name</label>
						<input type="text" name="accountName" id="accountNameexist" class="form-control" value=""  required readonly />
						
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Email</label>
						<input type="email" name="accountEmail" id="accountEmailexist" class="form-control" value="" required />
					</div>
					<div class="form-group form-primary">
						<label class="float-label">Number</label>
						<input type="text" name="accountNumber" id="accountNumber" maxlength="10" minlength="10" class="form-control" value=""  required />
						
					</div>
					<div class="mt-20 form-group form-primary">
						<button type="submit" class="btn btn-info btn-block btn-xs waves-effect waves-light create_acc_btn">
							<i class="fa fa-paper-plane"></i> Create Account
						</button>
					</div>
				</form>
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
	$(document).ready(function(){
		$( ".statedet" ).click(function() {
			var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		   	$.ajax({
                url: 'getDid',
                type: "POST",
                data: { 
                	'state' : $('#localareacode').val(),
                	_token: CSRF_TOKEN
                },
                success: function(data)
                {
                    var response = JSON.parse(data);
                    console.log(response);
                    if(response.success == 1){
                          $didoptions = "<option value='-1'>select did</option>";
                          $.each(response.data , function( key, value ) {
                             $didoptions += "<option value='"+value+"'>"+value+"</option>";
                          });
                          $('.faxesdid').html($didoptions);
                    }else{
                       
                    }
                },
                error: function () {

                }
            });
		});
		$('#userfaxdet').DataTable();
		$('#userupdatefaxdet').DataTable();
	});
</script>
<script type="text/javascript">
	function updatefaxmodal(id){
		//console.log(id);
		$('#update_id').val(id);
	}
	function createfaxmodal(id, email, name){
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$('#accountEmail').val("");
		$('#accountName').val("");
		$('#new_id').val(id);
		$.ajax({
			url:"getUserDetailFax",
			method:"POST",
			data:{
				uid:id,
				_token: CSRF_TOKEN
			},
			success:function(data){
				// console.log(data);
				if(data != ""){
					var result = JSON.parse(data);
					// console.log(result['user_email']);
					$('#accountEmail').val(result['user_email']);
					$('#accountName').val(result['display_name']);
				}
				

			}
		});
		
	}
	function reset(){
			$('#did').prop('selectedIndex',-1);
			//$('.statedet').prop('selectedIndex',-1);
		}
	function createexistingfaxmodal(id, email, name){
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$('#accountEmailexist').val("");
		$('#accountNameexist').val("");
		$('#new_existint_id').val(id);
		$.ajax({
			url:"getUserDetailFax",
			method:"POST",
			data:{
				uid:id,
				_token: CSRF_TOKEN
			},
			success:function(data){
				// console.log(data);
				if(data != ""){
					var result = JSON.parse(data);
					// console.log(result['user_email']);
					$('#accountEmailexist').val(result['user_email']);
					$('#accountNameexist').val(result['display_name']);
				}
				

			}
		});
		
	}

	function updateRegfaxmodal(id, email, name){
		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
		$('#accountEmailexistUpdate').val("");
		$('#accountNameexistUpdate').val("");
		$('#new_reg_id').val(id);
		$.ajax({
			url:"getUserDetailFax",
			method:"POST",
			data:{
				uid:id,
				_token: CSRF_TOKEN
			},
			success:function(data){
				// console.log(data);
				if(data != ""){
					var result = JSON.parse(data);
					// console.log(result['user_email']);
					$('#accountEmailexistUpdate').val(result['user_email']);
					$('#accountNameexistUpdate').val(result['display_name']);
				}
				

			}
		});
		
	}
</script>
@endsection
