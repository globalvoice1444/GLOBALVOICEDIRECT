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
.tab-content.responsive {
    padding: 60px 0 0 0;
}
.form-inline-forms {
    padding: 20px 40px 20px 20px;
    border-radius: 6px;
    background-color: #fff;
    margin: 20px 20px 20px 20px;
}
.table-bg {
    background-color: #dfe4e7;
    margin: 0 20px 40px 20px;
    border-radius: 6px;
    padding-bottom: 1px;
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
                    	<li class="breadcrumb-item">Admin - Send Emails</li>
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
								                            <li><a data-toggle="pill" href="#login-inf" class="active show">Send Email</a></li>
								
								                             </ul>
								                        </div> 
								                    </section> 
								                </div> 
								            </div> 
								        </div> 
								    </div>
									<div class="tab-content responsive">
										<div id="login-info" class="tab-pane fade active show">
											<div class="col-sm-12">
												<div class="table-bg">
													<div class="top-title border-bootm">
														<h3 class="table-heading">Create a New Email</h3>
													</div>
														
												  <div class="form-inline-forms">
												    <div class="card-block" id="orders_card">
												      <div class="row">
												        <div class="col-md-12">
												        @if(session()->has('message'))
										                    <div class="alert alert-info" role="alert">
										                        {{ session()->get('message') }}
										                    </div>
												        @endif
												          <div id="wizard" class="viewSmsWizard">
												            <section>
												              <div class="send_email">
												                <form class="form-material" method="post" action="sendEmailtoclients" enctype="multipart/form-data" name="miniFax" >
												                	<input type="hidden" name="_token" value="{{ csrf_token() }}">
												                    <div class="form-group">
												                    	<div class="form-group form-primary">
												                         	<input class="form-control" name="emailname" id="emailname"  type="text" required />
												                      		<span class="form-bar"></span>
												                      		<label class="float-label">Name</label>
												                      	</div>
												                      <div class="form-group form-primary">
												                         	<input class="form-control" name="emailfrom" id="emailfrom" value="" type="text" required />
												                      		<span class="form-bar"></span>
												                      		<label class="float-label">From</label>
												              
												                      </div>
												                    </div>
												                    <div class="form-group form-primary">
												                      <input class="form-control" name="subject" id="subject" value="" type="text" required />
												                      <span class="form-bar"></span>
												                      <label class="float-label">Subject</label>
												                    </div>
												                    <div class="form-group form-primary">
												                      <textarea name="editor1" id="editor1"  rows="10" cols="80" placeholder="Add Email Content" required></textarea>
												                    </div>
												                    <div class="mt-20 form-group form-primary">
												                      <button type="submit" class="btn btn-info btn-xs waves-effect waves-light">
												                        <i class="fa fa-paper-plane"></i> Send Email
												                      </button>
												                    </div>
												                  </form>
												              </div>



												            </section>
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
@endsection
@section('script')
<script type="text/javascript" src="{{ asset('admin/assets/js/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
  CKEDITOR.replace( 'editor1' );
</script>
@endsection