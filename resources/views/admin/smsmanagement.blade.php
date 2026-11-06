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
@if(!empty($smsNotice))
<div style="margin:20px 0;padding:22px 26px;border-radius:12px;background:#0b1f3a;color:#fff;border-left:6px solid #f7941d;box-shadow:0 6px 18px rgba(11,31,58,.18)">
  <div style="font-size:18px;font-weight:700;margin-bottom:6px">SMS Management</div>
  <div style="opacity:.92">{{ $smsNotice }}</div>
</div>
@endif
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
                    <li class="breadcrumb-item">SMS Management</li>
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
															<li><a data-toggle="tab" href="#cinfo" class="active show">Setup HTTP</a></li>
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
											 
												<div class="border-box border-top-none-form">
													<div class="row Information-tab">
														<div class="col-lg-12 col-md-12 col-sm-12">
															<div class="border-box">
																<div class="row">
																	<div class="col-md-12">
																		<div class="top-title border-bootm">
																			<h3 class="table-heading">Setup HTTP</h3>
																		</div>
																	</div>
																</div>
																<div class="form-inline-form">
																	 
																	<form method="post" action="act_addhttp">
																		<input type="hidden" name="_token" value="{{ csrf_token() }}">
																		<div class="form-inline-table">
																			<div class="row">
																				<div class="col-lg-6 space-right">
																					<label class="category-heading">HTTP URL</label>
																				</div>
																				<div class="col-lg-12">
																					<input class="form-control" name="httpurl" value="" type="text" required>
																				</div>
																				<div class="row margint-30">
																					<div class="col-lg-12">
																						<button type="submit" class="btn btn-info btn-blue">Add HTTP URL</button>
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
 
@endsection
@section('script')
 
@endsection
