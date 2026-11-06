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
                	<li class="breadcrumb-item"><a href="/admin/add_credit_form"> Additional Credit Apllication</a></li>
                	<li class="breadcrumb-item">User Details</li>
            	</ul>
          	</div>
        </div>
    </div>
</div>
<div class="admin-content">
	<div class="bg-white">
		<div class="top-title">
			<h3>Additional Credit Apllication - User Detail</h3>
		</div>
		<div class="border-box padding-15 border-top-none">
			<div class="row">
				<div class="col-md-12">
					<div class="col-md-6 col-sm-12">				 	
					 	<h3>Additional Credit Apllication Details</h3>
					 	<div class="col-md-12"><p><strong>Business Name: </strong>{{ $userForm->business_name }}</p></div>
					 	<div class="col-md-12"><p><strong>Business Address: </strong>{{ $userForm->business_address }}</p></div>
					 	<div class="col-md-12"><p><strong>Duns Number: </strong>{{ $userForm->duns_number }}</p></div>
					 	<div class="col-md-12"><p><strong>Sic Code: </strong>{{ $userForm->sic_code }}</p></div>
					 	<div class="col-md-12"><p><strong>Naics Code: </strong>{{ $userForm->naics_code }}</p></div>
					 	<div class="col-md-12"><p><strong>First Sale: </strong>{{ $userForm->first_sale }}</p></div>
					 	<div class="col-md-12"><p><strong>Last Sale: </strong>{{ $userForm->last_sale }}</p></div>
					 	<div class="col-md-12"><p><strong>Credit Limit: </strong>{{ $userForm->credit_limit }}</p></div>

					 	
					</div>
					
					
				</div>
			</div>
		</div>
	</div>
</div>
@endsection