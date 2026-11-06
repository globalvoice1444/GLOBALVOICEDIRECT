@extends('admin.layouts.layout')
@section('css')
<style type="text/css">
	.col-md-12 {
    	margin-bottom: 15px;
	}
	h3 {
	    margin-bottom: 30px;
	}
	.top-title {
	    padding: 32px 29px 0px;
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
                	<li class="breadcrumb-item"><a href="creditappInfo"> Credit Application</a></li>
                	<li class="breadcrumb-item">User Details</li>
            	</ul>
          	</div>
        </div>
    </div>
</div>
<div class="admin-content">
	<div class="bg-white">
		<div class="top-title">
			<h3>Credit Apllication - User Detail</h3>
		</div>
		<div class="border-box padding-15 border-top-none">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 col-sm-12">
					 	<h3>About Your Business</h3>
					 	<div class="col-md-12"><p><strong>Legal Business name: </strong>{{ $allDet->buisness_name }}</p></div>
					 	<div class="col-md-12"><p><strong>Doing business As: </strong>{{ $allDet->buisness_doing }}</p></div>
					 	<h3>Business Address</h3>
					 	<div class="col-md-12"><p><strong>Street Address: </strong>{{ $allDet->bstreet_address1 }}</p></div>
					 	<div class="col-md-12"><p><strong>City: </strong>{{ $allDet->bcity }}</p></div>
					 	<div class="col-md-12"><p><strong>State / Province / Region: </strong>{{ $allDet->bstate }}</p></div>
					 	<div class="col-md-12"><p><strong>Zip / Postal Code: </strong>{{ $allDet->bzip }}</p></div>
					 	<div class="col-md-12"><p><strong>Country: </strong>{{ $allDet->bcountry }}</p></div>
					 	<div class="col-md-12"><p><strong>Main Business Phone: </strong>{{ $allDet->bphone }}</p></div>
					 	<div class="col-md-12"><p><strong>Estimated Monthly Purchase Volume: </strong>{{ $allDet->monthly_purchase_volume }}</p></div>
					 	<div class="col-md-12"><p><strong>Total Number of Employees: </strong>{{ $allDet->total_num_employees }}</p></div>
					 	<div class="col-md-12"><p><strong>Year Business Started: </strong>{{ $allDet->buisness_year_started }}</p></div>
					 	<div class="col-md-12"><p><strong>Tax ID or (FEIN) Number: </strong>{{ $allDet->taxid }}</p></div>
					 	<h3>Billing Address</h3>
					 	<div class="col-md-12"><p><strong>Street Address: </strong>{{ $allDet->billing_street_address1 }}</p></div>
					 	<div class="col-md-12"><p><strong>Address Line 2: </strong>{{ $allDet->billing_street_address2 }}</p></div>
					 	<div class="col-md-12"><p><strong>City: </strong>{{ $allDet->billing_city }}</p></div>
					 	<div class="col-md-12"><p><strong>State / Province / Region: </strong>{{ $allDet->billing_state }}</p></div>
					 	<div class="col-md-12"><p><strong>Zip / Postal Code: </strong>{{ $allDet->billing_zip }}</p></div>
					 	<div class="col-md-12"><p><strong>Country: </strong>{{ $allDet->billing_country }}</p></div>
					</div>
					<div class="col-md-6 col-sm-12">
					 	 
					 		<h3>About Your Business</h3>
					 		<div class="col-md-12"><p><strong>Business Title: </strong>{{ $allDet->buisness_title }}</p></div>
					 		<div class="col-md-12"><p><strong>First Name: </strong>{{ $allDet->first_name }}</p></div>
					 		<div class="col-md-12"><p><strong>Last Name: </strong>{{ $allDet->last_name }}</p></div>
					 		<h3>Home Address</h3>
					 		<div class="col-md-12"><p><strong>Street Address: </strong>{{ $allDet->home_address1 }}</p></div>
					 		<div class="col-md-12"><p><strong>Address Line 2: </strong>{{ $allDet->home_address2 }}</p></div>
					 		<div class="col-md-12"><p><strong>City: </strong>{{ $allDet->home_city }}</p></div>
					 		<div class="col-md-12"><p><strong>State / Province / Region: </strong>{{ $allDet->home_state }}</p></div>
					 		<div class="col-md-12"><p><strong>Zip / Postal Code: </strong>{{ $allDet->home_zip }}</p></div>
					 		<div class="col-md-12"><p><strong>Country: </strong>{{ $allDet->home_country }}</p></div>
					 		<div class="col-md-12"><p><strong>Mobile: </strong>{{ $allDet->home_phone }}</p></div>
					 		<div class="col-md-12"><p><strong>Email Address: </strong>{{ $allDet->home_mobile }}</p></div>
					 		<div class="col-md-12"><p><strong>Birthday: </strong>{{ $allDet->birthday }}</p></div>
					 		<h3>License/Residence</h3>
					 		<div class="col-md-12"><p><strong>Driver License: </strong>{{ $allDet->driver_license }}</p></div>
					 		<div class="col-md-12"><p><strong>License State: </strong>{{ $allDet->license_state }}</p></div>
					 		@if($allDet->file_path != "")

					 			<div class="col-md-12"><p><a href="/admin/forms/credit/legacy/{{ $allDet->id }}/file/upload" target="_blank">Proof of business filling from secretary of state </a></p></div>
					 		
					 		@endif
					</div>
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection