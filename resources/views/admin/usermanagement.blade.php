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
table#userdetailtable,#producttable12,#deactivateUserTable,#userlegaldetailtable,#userPhoneInfo,#membershipFailureTable {
    border-style: none !important;
    background: #fff !important;
    border-radius: 6px !important;
    margin-bottom: 22px;
}
.adduserform{
	margin: auto;
}
#referral-users .modal-content .modal-body {
    padding: 20px 0px;
}
ul.nav.nav-pills {
    height: 197px;
}
.badge {
    border-radius: 17px;
    padding: 7px 12px;
}
@media only screen and (min-device-width : 320px) and (max-device-width : 480px){
.nav.nav-pills {
    height: 582px !important;
}
}
@media only screen and (min-device-width : 768px) and (max-device-width : 1024px) and (-webkit-min-device-pixel-ratio : 2){
.nav.nav-pills {
    height: 243px !important;
}
}
@media screen and (max-width: 1200px)  {
.adjust_x
{
position: fixed !important;
    left: 77% !important;
    top: 8% !important;
}
}
@media screen and (max-width: 812px) {
.small_screen_submit
{
	position: fixed;
    left: 38px;
}
.small_screen_close
{
    position: relative;
    left: 116px;
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
                <li class="breadcrumb-item">User Management</li>
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
                            <li><a data-toggle="tab" href="#cinfo" class="active show">Active Users</a></li>
							<li><a data-toggle="tab" href="#cc-info">DeActive Users</a></li>
							<li><a data-toggle="tab" href="#login-info">Add New User</a></li>
							<li><a data-toggle="tab" href="#activate-user">Activate User</a></li>
							<li><a data-toggle="tab" href="#user_password">Change User Password</a></li>
							<li><a data-toggle="tab" href="#legal-user">User Legal Information</a></li>
							<li><a data-toggle="tab" href="#pn-info">User Phone Numbers</a></li>
							<li><a data-toggle="tab" href="#f-membership">Failure Membership</a></li>
							<li><a data-toggle="tab" href="#affiliates">Affiliates/Non Members</a></li>
							<li><a data-toggle="tab" href="#affiliates-withdraw">Affiliates Withdraw</a></li>
							<li><a data-toggle="tab" href="#member-withdraw-request">Member Withdraw Request</a></li>
                             </ul>
                        </div> 
                    </section> 
	                </div> 
	            </div> 
	        </div> 
	    </div>
			@if(session()->has('message'))
				<div class="alert alert-info">
  					{{ session()->get('message') }}
				</div>
			@endif
			<div class="tab-content responsive">

				<!-- View Active Users -->
				<div id="cinfo" class="tab-pane fade active show">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													View Active Users
												</h3>

											</div>

										</div>

									</div>

									<div class="row">

										<div class="col-md-4">

											<div class="form-group">

												<div class="input-group">

													<input type="text" id="activeUserSearch" class="form-control" placeholder="Search by name, login, or email" aria-label="Search users">

													<button class="btn btn-primary" type="button" onclick="loadActiveUsers(1)">Search</button>

												</div>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<!-- AJAX CONTENT -->

										<div id="active-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Active Users...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>


				<!-- View DeActive Users -->
				<div id="cc-info" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													View DeActive Users
												</h3>

											</div>

										</div>

									</div>

									<div class="row">

										<div class="col-md-4">

											<div class="form-group">

												<div class="input-group">

													<input type="text" id="deactiveUserSearch" class="form-control" placeholder="Search by name, login, or email" aria-label="Search users">

													<button class="btn btn-primary" type="button" onclick="loadDeActiveUsers(1)">Search</button>

												</div>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="deactive-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading DeActive Users...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>
				 
				<!-- / Contact Information -->
				
				<div id="login-info" class="tab-pane fade">
					<form method="post" action="/action/addNewUser" enctype="multipart/form-data">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<div class="border-box border-top-none">
							<div class="row Information-tab">
								<div class="col-lg-8 col-md-12 col-sm-12 adduserform">
									<div class="border-box">
										<div class="row">
											<div class="col-md-12">
												<div class="top-title border-bootm">
													<h3 class="table-heading">Add User</h3>
												</div>
											</div>
										</div>
										<div class="form-inline-table">
											 
											<div class="row">
												<div class="col-lg-6 space-right">
													<label class="category-heading">Buisness Name</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="bname" value="" type="text" required>
												</div>
												<div class="col-lg-6 space-right">
													<label class="category-heading">User Login Id</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="userid" value="" type="text" required>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-6 space-right">
													<label class="category-heading">User Email</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="useremail" value="" type="text" required="">
												</div>
												<div class="col-lg-6 space-right">
													<label class="category-heading">User Url</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="userurl" value="" type="text">
												</div>
											</div>
											<div class="row">
												<div class="col-lg-6 space-right">
													<label class="category-heading">Password</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="pass1" value="" type="text" required>
												</div>
												<div class="col-lg-6 space-right">
													<label class="category-heading">Retype Password</label>
												</div>
												<div class="col-lg-12">
													<input class="form-control" name="pass2" value="" type="text" required>
												</div>
											</div>
											<div class="row">
												<div class="col-lg-6 space-right">
													<label class="category-heading">Display Name</label>
												</div>
												 
												<div class="col-lg-12">
													<input class="form-control" name="dname" value="" type="text" required>
												</div>
											</div>
											 
											<div class="row margint-30">
												<div class="col-lg-12">
													<button type="submit" class="btn btn-info btn-blue">Add User</button>
												</div>
											</div>
											 
										</div>
									</div>
								</div>
							</div>
						</div>
					</form>
				</div>
				 
				<!-- Activate Users -->
				<div id="activate-user" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													Activate Users
												</h3>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="email-active-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Activate Users...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

				<!-- Change User Password -->
				<div id="user_password" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">
										<div class="col-md-12">
											<div class="top-title border-bootm">
												<h3 class="table-heading">
													Change User Password
												</h3>
											</div>
										</div>
									</div>

									<div class="form-inline-form">

										<div id="password-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Users...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

				<!-- User Legal Information -->
				<div id="legal-user" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													Legal Users Info
												</h3>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="legal-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Legal Users...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>
				 
				<!-- User Phone Numbers Info -->
				<div id="pn-info" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">
										<div class="col-md-12">
											<div class="top-title border-bootm">
												<h3 class="table-heading">
													Users Phone Numbers Info
												</h3>
											</div>
										</div>
									</div>

									<div class="form-inline-form">

										<div id="phone-users-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Phone Numbers...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>

				<!-- Failure Membership Information -->
				<div id="f-membership" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													Failure Membership
												</h3>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="failure-membership-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Failure Membership...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>
				<!-- Affiliates / Non Members -->
				<div id="affiliates" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">
										<div class="col-md-12">
											<div class="top-title border-bootm">
												<h3 class="table-heading">
													Affiliates / Non Members
												</h3>
											</div>
										</div>
									</div>

									<div class="form-inline-form">

										<div id="affiliates-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Affiliates...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>
				<!-- Affiliates Withdraw Requests -->
				<div id="affiliates-withdraw" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													Affiliates Withdraw Requests
												</h3>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="affiliate-withdraw-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Withdraw Requests...

											</div>

										</div>

									</div>

								</div>

							</div>

						</div>

					</div>

				</div>
				<!-- Member Withdraw Requests -->
				<div id="member-withdraw-request" class="tab-pane fade">

					<div class="border-box border-top-none-form">

						<div class="row Information-tab">

							<div class="col-lg-12 col-md-12 col-sm-12">

								<div class="border-box">

									<div class="row">

										<div class="col-md-12">

											<div class="top-title border-bootm">

												<h3 class="table-heading">
													Member Withdraw Requests
												</h3>

											</div>

										</div>

									</div>

									<div class="form-inline-form">

										<div id="member-withdraw-container">

											<div class="text-center p-5">

												<i class="fa fa-spinner fa-spin fa-3x"></i>

												<br><br>

												Loading Member Withdraw Requests...

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
	    <span class="close">&times;</span>
	    <div class="form-inline">
			<form method="post" action="action/act_updateProduct.php" enctype="multipart/form-data">	
				<input type="Hidden" name="productId" id="productId" value="">		 
				<div class="row">
					<div class="col-lg-2 space-right">
						<label>Product Name</label>
					</div>
					<div class="col-lg-4">
						<input class="form-control" name="uproductname" id="uproductname" value="" type="text" required>
					</div>
					<div class="col-lg-2 space-right">
						<label>Cart Description</label>
					</div>
					<div class="col-lg-2">
						<input class="form-control" name="udescription" id="udescription"  value="" type="text" required>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 space-right">
						<label>Short Description</label>
					</div>
					<div class="col-lg-4">
						<textarea name="ushortDesc" id="ushortDesc" class="form-control"></textarea>
					</div>
					<div class="col-lg-2 space-right">
						<label>Long Description</label>
					</div>
					<div class="col-lg-4">
						<textarea name="ulongDesc" id="ulongDesc" class="form-control"></textarea> 
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 space-right">
						<label>Price</label>
					</div>
					<div class="col-lg-4">
						<input class="form-control" name="uprice" id="uprice"  value="" type="text">
					</div>
					<div class="col-lg-2 space-right">
						<label>Weight</label>
					</div>
					<div class="col-lg-4">
						<input class="form-control" name="uweight" id="uweight" value="" type="text" required>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 space-right">
						<label>Category</label>
					</div>
					<div class="col-lg-4">
						<select class="form-control" name="ucategorySel" id="categorySel">
						  <option value="-1">--Select Category--</option>
						  	

						</select>
					</div>
					<div class="col-lg-2 space-right">
						<label>Quantity</label>
					</div>
					<div class="col-lg-4">
						<input class="form-control" name="uquantity" id="uquantity" min="1" value="" type="number" required>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-2 space-right">
						<label>Image</label>
					</div>
					<div class="col-lg-5">
						<input class="form-control" name="uimage" id="uimage" value="" type="file" >
					</div>
				</div>
				<div class="row margint-30">
					<div class="col-lg-12">
						<button type="submit" class="btn btn-info btn-blue">Update Products</button>
					</div>
				</div>
			</form>
		</div>
	  </div>

	</div>
	<!-- Change password Modal -->
	<div id="changepassword" class="modal fade" role="dialog">
		<div class="modal-dialog">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Change User Password</h4>
					<button type="button" class="close adjust_x" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<form method="post" action="changepassword">
						<input type="hidden" name="_token" value="{{ csrf_token() }}">
						<input type="hidden" name="uservalue" id="uservalue">
						<div class="row">
							<label>Password</label>
							<input class="form-control" type="text" name="password" id="password" required>
						</div>
						<div class="row">
							<label>Again Password</label>
							<input class="form-control" type="text" name="againpassword" id="againpassword" required>
						</div>
						<div class="row">
				
							<div class="col-md-9 small_screen_submit">
								<input type="submit" class="btn btn-info btn-lg" name="submit" id="submit">
							</div>
							<div class="col-md-3 small_screen_close">
								<button  type="button" class="btn btn-default btn-lg " data-dismiss="modal">Close</button>
							</div>
						
						
							<!-- clearfix -->
						</div>
					</form>
				</div>
							<!-- <div class="modal-footer">
								<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							</div> -->
						</div>
					</div>
				</div>
			</div>
			</div>
		</div>
	</div>
	<!-- Change password Modal -->
	<div id="referral-users" class="modal fade" role="dialog">
		<div class="modal-dialog modal-lg">
			<!-- Modal content-->
			<div class="modal-content">
				<div class="modal-header">
					<h4 class="modal-title">Affiliate Users</h4>
					<button type="button" class="close adjust_x" data-dismiss="modal">&times;</button>
				</div>
				<div class="modal-body">
					<table class="table table-hover log-table" id="affiliate-users">
						<thead class="top-text">
							<tr>
								<th>#</th>
								<th>Affiliate Name</th>
								<th>Referral User</th>
								<th>Reward Status</th>
								<th>Signup At</th>
							</tr>
						</thead>
						<tbody class="affilate-referrals-info">
						</tbody>
						<tfoot>
							<tr class="bottom-text">
								<th>#</th>
								<th>Affiliate Name</th>
								<th>Referral User</th>
								<th>Reward Status</th>
								<th>Signup At</th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
		</div>
	</div>
	</div>
	</div>

@endsection

@section('script')

<script type="text/javascript">

    var affiliateUsersTable = '';

    $(document).ready(function () {

        // Load Active Users on page load
        loadActiveUsers();

        var activeUserSearchTimer = null;

        $('#activeUserSearch').on('keyup', function () {

            clearTimeout(activeUserSearchTimer);

            activeUserSearchTimer = setTimeout(function () {

                loadActiveUsers(1);

            }, 400);

        });

        var deactiveUserSearchTimer = null;

        $('#deactiveUserSearch').on('keyup', function () {

            clearTimeout(deactiveUserSearchTimer);

            deactiveUserSearchTimer = setTimeout(function () {

                loadDeActiveUsers(1);

            }, 400);

        });

        // Existing DataTables (keep these if they are NOT converted to AJAX yet)

        // $('#userdetailtable').DataTable();
        // $('#userlegaldetailtable').DataTable();
        // $('#producttable12').DataTable();
        // $('#deactivateUserTable').DataTable();
        // $('#userPhoneInfo').DataTable();
        // $('#membershipFailureTable').DataTable();
        // $('#affiliatesTable').DataTable();

    });


    /*
    |--------------------------------------------------------------------------
    | Active Users AJAX
    |--------------------------------------------------------------------------
    */

    function loadActiveUsers(page = 1)
    {

        var search = $('#activeUserSearch').val() || '';

        $.ajax({

            url: "{{ url('/admin/usermanagement/active-users') }}?page=" + page + "&search=" + encodeURIComponent(search),

            type: "GET",

            beforeSend: function () {

                $('#active-users-container').html(
                    '<div class="text-center p-5">'+
                    '<i class="fa fa-spinner fa-spin fa-3x"></i>'+
                    '<br><br>Loading Active Users...'+
                    '</div>'
                );

            },

            success: function (response) {

                $('#active-users-container').html(response);

                initializeActiveUserTable();

            },

            error: function () {

                $('#active-users-container').html(
                    '<div class="alert alert-danger">Something went wrong.</div>'
                );

            }

        });

    }

	/*
	|--------------------------------------------------------------------------
	| Load DeActive Users
	|--------------------------------------------------------------------------
	*/

	function loadDeActiveUsers(page = 1)
	{

		var search = $('#deactiveUserSearch').val() || '';

		$.ajax({

			url: "{{ url('/admin/usermanagement/deactive-users') }}?page=" + page + "&search=" + encodeURIComponent(search),

			type: "GET",

			beforeSend: function () {

				$('#deactive-users-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading DeActive Users...'+
					'</div>'

				);

			},

			success: function (response) {

				$('#deactive-users-container').html(response);

				initializeDeActiveUserTable();

			},

			error: function () {

				$('#deactive-users-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#cc-info"]').on('shown.bs.tab', function () {

		if ($('#deactive-users-container').html().indexOf('table') === -1) {

			loadDeActiveUsers();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Email Activate Users
	|--------------------------------------------------------------------------
	*/

	function loadEmailActiveUsers(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/email-users') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#email-active-users-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Activate Users...'+
					'</div>'

				);

			},

			success:function(response){

				$('#email-active-users-container').html(response);

				initializeEmailUsersTable();

			},

			error:function(){

				$('#email-active-users-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#activate-user"]').on('shown.bs.tab', function () {

		if($('#email-active-users-container').html().indexOf('table')==-1){

			loadEmailActiveUsers();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Password Users
	|--------------------------------------------------------------------------
	*/

	function loadPasswordUsers(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/password-users') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#password-users-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Users...'+
					'</div>'

				);

			},

			success:function(response){

				$('#password-users-container').html(response);

				initializePasswordUsersTable();

			},

			error:function(){

				$('#password-users-container').html(

					'<div class="alert alert-danger">Unable to load users.</div>'

				);

			}

		});

	}

	$('a[href="#user_password"]').on('shown.bs.tab', function () {

		if($('#password-users-container').html().indexOf('table')==-1){

			loadPasswordUsers();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Legal Users
	|--------------------------------------------------------------------------
	*/

	function loadLegalUsers(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/legal-users') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#legal-users-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Legal Users...'+
					'</div>'

				);

			},

			success:function(response){

				$('#legal-users-container').html(response);

				initializeLegalUsersTable();

			},

			error:function(){

				$('#legal-users-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#legal-user"]').on('shown.bs.tab', function () {

		if($('#legal-users-container').html().indexOf('table')==-1){

			loadLegalUsers();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Phone Users
	|--------------------------------------------------------------------------
	*/

	function loadPhoneUsers(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/phone-users') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#phone-users-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Phone Numbers...'+
					'</div>'

				);

			},

			success:function(response){

				$('#phone-users-container').html(response);

				initializePhoneUsersTable();

			},

			error:function(){

				$('#phone-users-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#pn-info"]').on('shown.bs.tab', function () {

		if($('#phone-users-container').html().indexOf('table')==-1){

			loadPhoneUsers();

		}

	});

	
	/*
	|--------------------------------------------------------------------------
	| Affiliates
	|--------------------------------------------------------------------------
	*/

	function loadAffiliates(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/affiliates') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#affiliates-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Affiliates...'+
					'</div>'

				);

			},

			success:function(response){

				$('#affiliates-container').html(response);

				initializeAffiliatesTable();

			},

			error:function(){

				$('#affiliates-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#affiliates"]').on('shown.bs.tab', function () {

		if($('#affiliates-container').html().indexOf('table') == -1){

			loadAffiliates();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Affiliate Withdraw Requests
	|--------------------------------------------------------------------------
	*/

	function loadAffiliateWithdraw(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/affiliate-withdraw') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#affiliate-withdraw-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Withdraw Requests...'+
					'</div>'

				);

			},

			success:function(response){

				$('#affiliate-withdraw-container').html(response);

				initializeAffiliateWithdrawTable();

			},

			error:function(){

				$('#affiliate-withdraw-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#affiliates-withdraw"]').on('shown.bs.tab', function () {

		if($('#affiliate-withdraw-container').html().indexOf('table') == -1){

			loadAffiliateWithdraw();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Failure Membership
	|--------------------------------------------------------------------------
	*/

	function loadFailureMembership(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/failure-membership') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#failure-membership-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Failure Membership...'+
					'</div>'

				);

			},

			success:function(response){

				$('#failure-membership-container').html(response);

				initializeFailureMembershipTable();

			},

			error:function(){

				$('#failure-membership-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#f-membership"]').on('shown.bs.tab', function () {

		if($('#failure-membership-container').html().indexOf('table')==-1){

			loadFailureMembership();

		}

	});

	/*
	|--------------------------------------------------------------------------
	| Member Withdraw Requests
	|--------------------------------------------------------------------------
	*/

	function loadMemberWithdraw(page = 1)
	{

		$.ajax({

			url: "{{ url('/admin/usermanagement/member-withdraw') }}?page=" + page,

			type: "GET",

			beforeSend:function(){

				$('#member-withdraw-container').html(

					'<div class="text-center p-5">'+
					'<i class="fa fa-spinner fa-spin fa-3x"></i>'+
					'<br><br>Loading Member Withdraw Requests...'+
					'</div>'

				);

			},

			success:function(response){

				$('#member-withdraw-container').html(response);

				initializeMemberWithdrawTable();

			},

			error:function(){

				$('#member-withdraw-container').html(

					'<div class="alert alert-danger">Unable to load records.</div>'

				);

			}

		});

	}

	$('a[href="#member-withdraw-request"]').on('shown.bs.tab', function () {

		if($('#member-withdraw-container').html().indexOf('table')==-1){

			loadMemberWithdraw();

		}

	});

    /*
    |--------------------------------------------------------------------------
    | Laravel Pagination AJAX
    |--------------------------------------------------------------------------
    */

    $(document).on(
        'click',
        '#active-users-container .pagination a',
        function (e) {

            e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

            loadActiveUsers(page);

        }
    );

	$(document).on(
		'click',
		'#deactive-users-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadDeActiveUsers(page);

	});

	$(document).on(
		'click',
		'#email-active-users-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadEmailActiveUsers(page);

	});

	$(document).on(
		'click',
		'#password-users-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadPasswordUsers(page);

	});

	$(document).on(
		'click',
		'#legal-users-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadLegalUsers(page);

	});

	$(document).on(
		'click',
		'#phone-users-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadPhoneUsers(page);

	});
	$(document).on(
		'click',
		'#failure-membership-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadFailureMembership(page);

	});

	$(document).on(
		'click',
		'#affiliates-container .pagination a',
		function(e){

			e.preventDefault();

			var page = $(this).attr('href').split('page=')[1];

			loadAffiliates(page);

	});

	$(document).on(
		'click',
		'#affiliate-withdraw-container .pagination a',
		function(e){

			e.preventDefault();

			var page = $(this).attr('href').split('page=')[1];

			loadAffiliateWithdraw(page);

	});

	$(document).on(
		'click',
		'#member-withdraw-container .pagination a',
		function(e){

			e.preventDefault();

			var page=$(this).attr('href').split('page=')[1];

			loadMemberWithdraw(page);

	});

    /*
    |--------------------------------------------------------------------------
    | DataTable
    |--------------------------------------------------------------------------
    */

    function initializeActiveUserTable()
    {

        if ($.fn.DataTable.isDataTable('#activeUserTable')) {

            $('#activeUserTable').DataTable().destroy();

        }

        $('#activeUserTable').DataTable({

            paging: false,          // Laravel pagination
            info: false,
            searching: true,
            ordering: true,
            lengthChange: false,
            autoWidth: false

        });

    }
	
	function initializeDeActiveUserTable()
	{

		if($.fn.DataTable.isDataTable('#deactivateUserTable')){

			$('#deactivateUserTable').DataTable().destroy();

		}

		$('#deactivateUserTable').DataTable({

			paging:false,          // Laravel handles pagination
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false

		});

	}

	function initializeEmailUsersTable()
	{

		if($.fn.DataTable.isDataTable('#emailActiveUserTable')){

			$('#emailActiveUserTable').DataTable().destroy();

		}

		$('#emailActiveUserTable').DataTable({

			paging:false,
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false

		});

	}
    
	function initializePasswordUsersTable()
	{

		if($.fn.DataTable.isDataTable('#passwordUsersTable')){

			$('#passwordUsersTable').DataTable().destroy();

		}

		$('#passwordUsersTable').DataTable({

			paging:false,          // Laravel handles pagination
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false

		});

	}

	function initializeLegalUsersTable()
	{

		if($.fn.DataTable.isDataTable('#legalUsersTable')){

			$('#legalUsersTable').DataTable().destroy();

		}

		$('#legalUsersTable').DataTable({

			paging:false,      // Laravel handles pagination
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false,
			responsive:true

		});

	}

	function initializePhoneUsersTable()
	{

		if($.fn.DataTable.isDataTable('#phoneUsersTable')){

			$('#phoneUsersTable').DataTable().destroy();

		}

		$('#phoneUsersTable').DataTable({

			paging:false,      // Laravel Pagination

			searching:true,

			ordering:true,

			info:false,

			lengthChange:false,

			autoWidth:false,

			responsive:true

		});

	}

	function initializeFailureMembershipTable()
	{

		if($.fn.DataTable.isDataTable('#membershipFailureTable')){

			$('#membershipFailureTable').DataTable().destroy();

		}

		$('#membershipFailureTable').DataTable({

			paging:false,          // Laravel Pagination

			searching:true,

			ordering:true,

			info:false,

			lengthChange:false,

			autoWidth:false,

			responsive:true

		});

	}

	function initializeAffiliatesTable()
	{

		if($.fn.DataTable.isDataTable('#affiliatesTable')){

			$('#affiliatesTable').DataTable().destroy();

		}

		$('#affiliatesTable').DataTable({

			paging:false,          // Laravel handles pagination
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false,
			responsive:true

		});

	}

	function initializeAffiliateWithdrawTable()
	{

		if($.fn.DataTable.isDataTable('#affiliateWithdrawTable')){

			$('#affiliateWithdrawTable').DataTable().destroy();

		}

		$('#affiliateWithdrawTable').DataTable({

			paging:false,
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false,
			responsive:true

		});

	}

	function initializeMemberWithdrawTable()
	{

		if($.fn.DataTable.isDataTable('#memberWithdrawTable')){

			$('#memberWithdrawTable').DataTable().destroy();

		}

		$('#memberWithdrawTable').DataTable({

			paging:false,
			searching:true,
			ordering:true,
			info:false,
			lengthChange:false,
			autoWidth:false,
			responsive:true

		});

	}

</script>


<script type="text/javascript">

    $('#categorySel').on('change', function () {

        var str = $("#categorySel option:selected").text();

        $('#updatename').val(str);

    });

</script>


<script type="text/javascript">

    function changeuserID(val)
    {

        $('#uservalue').val(val);

    }


    function getAffiliateUsers(affiliateid)
    {

        affiliateUsersTable = $('#affiliate-users').DataTable();

        $.ajax({

            url: 'affiliate-referrals',

            type: 'POST',

            headers: {

                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')

            },

            data: {

                id: affiliateid

            },

            success: function(result){

                var affiliate_users = JSON.parse(result);

                if (affiliate_users.length) {

                    affiliateUsersTable.clear();

                    for (var i = 0; i < affiliate_users.length; i++) {

                        affiliateUsersTable.row.add([

                            i + 1,

                            affiliate_users[i].name,

                            affiliate_users[i].user_nicename,

                            affiliate_users[i].reward_status,

                            affiliate_users[i].created_at

                        ]);

                    }

                    affiliateUsersTable.draw();

                }
                else {

                    affiliateUsersTable.clear();

                    $('.affilate-referrals-info').html(
                        '<tr class="odd">'+
                        '<td valign="top" colspan="5" class="dataTables_empty">'+
                        'No data available in table'+
                        '</td>'+
                        '</tr>'
                    );

                }

            },

            error: function(XMLHttpRequest, textStatus, errorThrown){

                alert(XMLHttpRequest.responseText);

                console.log(textStatus);

                console.log(errorThrown);

            }

        });

    }

</script>

@endsection