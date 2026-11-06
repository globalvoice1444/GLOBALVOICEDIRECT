	@extends('layouts.user.user_main')
    @section('css')
	 <style> 
	h3.table-heading {
	    text-align: center;
	    background-color: #134576;
	    padding-top: 20px;
	    padding-bottom: 20px;
	    color: #fff;
	    text-transform: uppercase;
	    border-radius: 6px 6px 0 0;
	}
	.cards {
	    border-radius: 5px;
	    -webkit-box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
	    box-shadow: 0 12px 5px -10px rgba(0,0,0,0.1), 0 0 4px 0 rgba(0,0,0,0.1);
	    border: none;
	    margin-bottom: 30px;
	    -webkit-transition: all 0.2s ease-in-out;
	    transition: all 0.2s ease-in-out;
	    background: #dfe4e7;
	}
	.form-control {
	    display: block;
	    width: 90%;
	}
	.form-inline-form {
	    padding: 40px 40px 40px 80px;
	    border-radius: 0 0 6px 6px;
	}
	label.category-heading {
	    color: #134576;
	    font-size: 18px;
	    font-weight: 600;
	}
	.mt-20.form-group.form-primary {
	    margin-top: 20px;
	}
	.btn-info {
	    background-color: #134576;
	    border-color: #134576;
	    color: #fff;
	    cursor: pointer;
	    -webkit-transition: all ease-in 0.3s;
	    transition: all ease-in 0.3s;
	    border-radius: 6px;
	}
	.btn-info:active, .btn-info:not([disabled]):not(.disabled).active, .btn-info:not([disabled]):not(.disabled):active, .btn-info:hover, .btn-info:not([disabled]):not(.disabled).hover, .btn-info:not([disabled]):not(.disabled):hover {
	    background-color: #b0cb02;
	    border-color: #b0cb02;
	    -webkit-box-shadow: none;
	    box-shadow: none;
	    color: #fff;
	}
	@media only screen
		and (max-width : 1920px) {
			.form-inline-form {
			    padding: 40px 20px 40px 68px;
			    border-radius: 0 0 6px 6px;
			}
		}
	@media only screen
		and (max-width : 1024px) {
			.form-inline-form {
			    padding: 40px 20px 40px 50px;
			    border-radius: 0 0 6px 6px;
			}
		}
    @media only screen
		and (max-width : 480px) {
			.form-inline-form {
			    padding: 40px 20px 40px 40px;
			    border-radius: 0 0 6px 6px;
			}
		}
	 </style>
@endsection
@section('content')	 
	<div class="container">
		<div class="cards">
			@if(session()->has('message'))

	          <div class="alert alert-info">

	            <strong>Info!</strong> {{session()->get('message')}}.

	          </div>

	      	@endif
	      	@if(session()->has('error'))

	          <div class="alert alert-info">

	            <strong>Info!</strong> {{session()->get('error')}}.

	          </div>

	      	@endif
			<div class="row">
				<div class="col-md-12">
					<div class="top-title border-bootm">
						<h3 class="table-heading">Add Port</h3>
					</div>
			    </div>
			</div>
			<div class="form-inline-form">
				<div class="card-block">
					<form method="post" action="/act_addport">
						{{csrf_field()}}
					  	<div class="form-row">
						    <div class="form-group col-md-6 col-sm-12">
						      <label class="category-heading" for="inputEmail4">Port Number:</label>
						      <input type="number" class="form-control" id="portnumber" name="portnumber" placeholder="10 digit Port Number" required>
						      <small class="form-text">Telephone number to port</small>
						    </div>
						    <div class="form-group col-md-6 col-sm-12">
						      <label class="category-heading" for="inputPassword4">End Port Number:</label>
						      <input type="number" class="form-control" id="endportnumber" name="endportnumber" placeholder="10 digit number">
						      <small class="form-text">End Sequential Telephone number to port</small>
						    </div>
					  	</div>

					  	<div class="form-row">
							
				  			<div class="form-group col-md-6 col-sm-12">
				    			<label class="category-heading" for="partial">Partial:</label>
								<select name="partial" class="form-control " id="partial" required>
									<option value="select">Select</option>
									<option value="yes">Yes</option>
									<option value="No">No</option>
								</select>
								<small class="form-text">Partial Port Indicator</small>
				  			</div>
				 			<div class="form-group col-md-6 col-sm-12">
				    			<label class="category-heading" for="additional">Additional:</label>
				    			<input type="text" class="form-control " id="additional" placeholder="Additional" name="additional">
								<small class="form-text">If partial port order is yes this is required</small>
				  			</div>
						</div>
			  			<div class="form-row">
			    			<div class="form-group col-md-6 col-sm-12">
			    		 	 <label class="category-heading" for="wireless">Wireless:</label>
								<select class="form-control " id="wireless" name="wireless" required>
								<option value="select">Select</option>
								<option>Yes</option>
								<option>No</option>
								</select>
								<small class="form-text">Wireless Port Indicator</small>
							</div>
			    			<div class="form-group col-md-6 col-sm-12">
			    			  <label class="category-heading" for="ssnpin">SSNPIN:</label>
								<input type="text" class="form-control " id="ssnpin" name="ssnpin" placeholder="Ssnpin">
								<small class="form-text">If wireless port order is yes this is required.</small>
			      	    	</div>
			  			</div>
						<div class="form-row">
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="carrier">Carrier:</label>
								<input type="text" class="form-control" id="carrier" name="carrier" placeholder="Carrier" required>
								<small class="form-text">Current Provider</small>
							</div>
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="company">Company:</label>
								<input type="text" class="form-control" id="company" name="company" placeholder="Contact Phone" required>
								<small class="form-text">End user company name</small>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="accnumber">Account Number:</label>
								<input type="text" class="form-control" id="accnumber" name="accnumber" placeholder="Accnumber" required>
								<small class="form-text">Account number with current provider</small>
							</div>
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="name">Name:</label>
								<input type="text" class="form-control" id="name" name="name" placeholder="Name" required>
								<small class="form-text">End User name</small>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="streetnumber">Street Number:</label>
								<input type="text" class="form-control" id="streetnumber" name="streetnumber" placeholder="Streetnumber" required>
								<small class="form-text">Street Number of End user address</small>
							</div>
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="streetprefix">Street Prefix:</label>
								<input type="text" class="form-control" id="streetprefix" name="streetprefix" placeholder="Streetsuffix">
								<small class="form-text">Directional Prefix for End user address street name</small>
							</div>
						</div>
						<div class="form-row">
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="streetname">Street Name:</label>
								<input type="text" class="form-control" id="streetname" name="streetname" placeholder="Streetname" required>
								<small class="form-text">Street Name of End user address</small>
							</div>
							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="streetsuffix">Street Suffix:</label>
								<input type="text" class="form-control" id="streetsuffix" name="streetsuffix" placeholder="Streetsuffix">
								<small class="form-text">Directional Suffix for End user address street name</small>
							</div>

							<div class="form-group col-md-6 col-sm-12">
								<label class="category-heading" for="unit">Unit:</label>
								<input type="text" class="form-control" id="unit" name="unit" placeholder="Unit">
								<small class="form-text">End user address Suite, Unit, Apt number</small>
							</div>
							<div class="form-group col-md-6 col-sm-12">
			    		  		<label for="billnumber">Bill Number</label>
			    		  		<input type="number" class="form-control " id="billnumber" name="billnumber" placeholder="Billnumber" required>
								<small class="form-text text-muted">Billing Telephone Number associated with current provider for DID</small>
							</div>
							
			    			<div class="form-group col-md-6 col-sm-12">
			    			 	<label class="category-heading" for="contactnumber">Contact Number:</label>
							 	<input type="number" class="form-control" id="contactnumber" name="contactnumber" placeholder="Contactnumber" required>
							  	<small class="form-text">Contact Telephone Number for any questions</small>
			      	    	</div>

			    			<div class="form-group col-md-6 col-sm-12">
			    		  		<label class="category-heading" for="routeto">Route To:</label>
			    		  		<input type="text" class="form-control " id="routeto" name="routeto" placeholder="Routeto">
								<small class="form-text">Specify Sub Account / Host for default route upon port completion</small>
							</div>

			    			<div class="form-group col-md-6 col-sm-12">
			    			    <label class="category-heading" for="rateplan">Rate Plan:</label>
								<input type="text" class="form-control " id="rateplan" name="rateplan" placeholder="Rateplan">
								<small class="form-text">Specify Rateplan for Port Order. PPM is default.</small>
			      	    	</div>
			    			<div class="form-group col-md-6 col-sm-12">
			      				<label class="category-heading" for="porttype">Port Type:</label>
			      				<input type="text" class="form-control " id="porttype" name="porttype">
								<small class="form-text">Specify type of port, residential or business. Business is default</small>
			    			</div>
			    		</div>
					   	<div class="form-row">
						    <div class="form-group col-md-6 col-sm-12">
						      <label class="category-heading" for="inputCity">City Name:</label>
						      <input type="text" class="form-control" id="inputCity" name="city" required>
						      <small class="form-text">City of End user address</small>
						    </div>
						    <div class="form-group col-md-6 col-sm-12">
						      <label class="category-heading" for="inputState">State:</label>
						      <input type="text" class="form-control" id="state" name="state" placeholder="State of End user address" required>
						      <small class="form-text">2 Letter State Abbreviation</small>
						    </div>
						    <div class="form-group col-md-6 col-sm-12">
						      <label class="category-heading" for="inputZip">Zip:</label>
						      <input type="number" class="form-control" id="inputZip" name="zip" placeholder="Zip Code of End user address" required>
						      <small class="form-text">5 digit Zip Code</small>
						    </div>
					  	</div>
					  	<div class="mt-20 form-group form-primary">
		                        <button type="submit" name="submit" class="btn btn-info btn-xs waves-effect waves-light">
		                          <i class="fa fa-paper-plane"></i>SUBMIT</button>
		                 </div>

					   <div>
					   	
					   </div>
					
					</form>
				</div>
			</div>
		</div>
	</div>

@endsection
@section('scripts')
	<script type="text/javascript">
		 var breadcrumb = $('.breadcrumb');
	    breadcrumb.append('<li class="breadcrumb-item"><b>Add Port</b></li>');
	    $('.main-menu ul').find('.active').removeClass('active');
	    $('.main-menu').find('a[href="/addPort"]').parents().addClass('active');
	</script>
@endsection