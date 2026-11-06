@extends('layouts.user.user_main')
    @section('css')
  <style type="text/css">
    .container.page-wrapper {
        background: #fff;
        border: 1px solid #3f4d67;
    }
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
  	<div class="container">
  		<div class="cards">
	      	<div class="row">
				<div class="col-md-12">
					<div class="top-title border-bootm">
						<h3 class="table-heading">Buisness Listing</h3>
					</div>
			    </div>
			</div>
	        <div class="form-inline-form">
		        <div class="card-block">
		    		<form action="act_Listingbusinessname" method="post">
		    		  	{{csrf_field()}}
		      			<div class="form-row">
		              <div class="form-group col-md-6 col-sm-12">
		                <label class="category-heading" for="inputPassword4">DID:</label>
		                <input type="text" class="form-control " id="did" placeholder="DID" name="did" required>
		                <small>DID or comma-separated list of DIDs</small>
		              </div>
		        			<div class="form-group col-md-6 col-sm-12">
		          				<label class="category-heading" for="Name">Name:</label>
		          				<input type="text" class="form-control" id="bname" placeholder="Name" name="businessname" required>
		    					<small class="form-text">The business name to be displayed on the listing</small>
		        			</div>
		        			
		      			</div>
		    			  <div class="form-row">
		    				
		      			<div class="form-group col-md-6 col-sm-12">
		        			<label class="category-heading"  for="StreetNumber">Street Number:</label>
		        			<input type="text" class="form-control " id="streetNumber" placeholder="Street Number" name="streetnumber" required>
		    				<small class="form-text">The street number of the business</small>
		      			</div>
		     			  <div class="form-group col-md-6 col-sm-12">
		        			<label class="category-heading"  for="StreetName">Street Name:</label>
		        			<input type="text" class="form-control " id="streetName" placeholder="Street Name" name="streetname" required>
		    				<small class="form-text">The street name of the business</small>
		      		  </div>
		    			 </div>
		      			<div class="form-row">
		        		<div class="form-group col-md-6 col-sm-12">
		        		  <label class="category-heading"  for="City">City:</label>
		        		  <input type="text" class="form-control " id="City" name="city" placeholder="City" required>
		    				<small class="form-text">The city of the business</small>
		    				
		        		</div>
		        			<div class="form-group col-md-6 col-sm-12">
		        			  <label class="category-heading"  for="State">State:</label>
		    				<input type="text" class="form-control " id="state" name="state" placeholder="State" required>
		    				<small class="form-text">The 2-character state abbreviation of the business</small>
		          	    		
		        			</div>
		        			<div class="form-group col-md-6 col-sm-12">
		          				<label class="category-heading"  for="inputZip">Zip:</label>
		          				<input type="text" class="form-control " id="zip" name="zip" maxlength="5" required>
		    					<small class="form-text">The 5-digit zipcode of the business</small>
		        			</div>
		      			</div>
		    			 <div class="form-row">
		    				<div class="form-group col-md-6 col-sm-12">
		    					<label class="category-heading"  for="email">Email:</label>
		    					<input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
		    					<small class="form-text">The email address of the listing owner, for verification if needed</small>
		    				</div>
		    				<div class="form-group col-md-6 col-sm-12">
		    					<label class="category-heading"  for="Contact_phon">Contact_phone:</label>
		    					<input type="text" class="form-control" id="contactPhon" name="contactphone" placeholder="Contact Phone" required>
		    					<small class="form-text">The phone number of the listing owner, for verification if needed</small>
		    				</div>
		    			 </div>
				         <div class="mt-20 form-group form-primary">
		                        <button type="submit" name="submit" class="btn btn-info btn-xs waves-effect waves-light">
		                          <i class="fa fa-paper-plane"></i>SUBMIT</button>
		                 </div>
		    			</form>
			        </div>
			    </div>
	    	</div>	
	  	</div>
  	</div>

@endsection
@section('scripts')
	<script type="text/javascript">
		 var breadcrumb = $('.breadcrumb');
    breadcrumb.append('<li class="breadcrumb-item"><b>Business Listing</b></li>');
    $('.main-menu ul').find('.active').removeClass('active');
    $('.main-menu').find('a[href="/listingbusinessname"]').parents().addClass('active');
	</script>
@endsection