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
		.error {
      color: red;
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
						<h3 class="table-heading">SBFE small business financial exchange credit bureau</h3>
					</div>
			    </div>
			</div>
			@if(!$alreadysubmitted)
				<div class="form-inline-form">
					<div class="card-block">
						<form method="post" id="martform" action="/additional/actother_submitMartForm">
							{{csrf_field()}}
							<div class="form-row">
								<div class="form-group col-md-6 col-sm-12">
									<label class="category-heading" for="accnumber">Business Name</label>
									<input type="text" class="form-control" id="business_name" name="business_name">
									@if($errors->has('business_name'))
	       							<div class="text-danger">{{ $errors->first('business_name') }}</div>
	       							@endif
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="category-heading" for="accnumber">Business Address</label>
									<input type="text" class="form-control" id="business_address" name="business_address">
									@if($errors->has('business_address'))
	       							<div class="text-danger">{{ $errors->first('business_address') }}</div>
	       							@endif
								</div>
								
							</div>
						  	<div class="form-row">

							    <div class="form-group col-md-6 col-sm-12">
							      <label class="category-heading" for="inputEmail4">Duns Number</label>
							      <input type="text" class="form-control" id="duns_number" name="duns_number">
							       @if($errors->has('duns_number'))
	       							<div class="text-danger">{{ $errors->first('duns_number') }}</div>
	       							@endif
							    </div>
							    <div class="form-group col-md-6 col-sm-12">
							      <label class="category-heading" for="inputPassword4">Sic code</label>
							      <input type="text" class="form-control" id="sic_code" name="sic_code">
							   	  @if($errors->has('sic_code'))
	       							<div class="text-danger">{{ $errors->first('sic_code') }}</div>
	       							@endif	
							    </div>
						  	</div>

						  	<div class="form-row">
								
					 			<div class="form-group col-md-6 col-sm-12">
					    			<label class="category-heading" for="additional">Naics code</label>
					    			<input type="text" class="form-control " id="naics_code" name="naics_code">
					    			 @if($errors->has('naics_code'))
	       							<div class="text-danger">{{ $errors->first('naics_code') }}</div>
	       							@endif					
					  			</div>
					  			<div class="form-group col-md-6 col-sm-12">
					    			<label class="category-heading" for="additional">First Sale</label>
					    			<input type="text" class="form-control " id="first_sale" name="first_sale">
									@if($errors->has('first_sale'))
	       							<div class="text-danger">{{ $errors->first('first_sale') }}</div>
	       							@endif
					  			</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 col-sm-12">
									<label class="category-heading" for="carrier">Last Sale</label>
									<input type="text" class="form-control" id="last_sale" name="last_sale">
									@if($errors->has('last_sale'))
	       							<div class="text-danger">{{ $errors->first('last_sale') }}</div>
	       							@endif
								</div>
								<div class="form-group col-md-6 col-sm-12">
									<label class="category-heading" for="company">Credit Limit</label>
									<input type="text" class="form-control" id="credit_limit" placeholder="digit" name="credit_limit">
									@if($errors->has('credit_limit'))
	       							<div class="text-danger">{{ $errors->first('credit_limit') }}</div>
	       							@endif
								</div>
							</div>
							<div class="form-row">
								<div class="form-group col-md-6 col-sm-12">
									<label class="category-heading" for="carrier">Email Address</label>
									<input type="text" class="form-control" id="add_credit_email" name="add_credit_email">
									@if($errors->has('add_credit_email'))
	       							<div class="text-danger">{{ $errors->first('add_credit_email') }}</div>
	       							@endif
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
			@else
			<div class="alert alert-success">

	            Your Additional Credit App Information Submitted. Thanks for Visiting...!

	         </div>
			@endif

		</div>
	</div>

@endsection
@section('scripts')
	<script type="text/javascript">
		 var breadcrumb = $('.breadcrumb');
	    breadcrumb.append('<li class="breadcrumb-item"><b>Additional Credit App</b></li>');
	    $('.main-menu ul').find('.active').removeClass('active');
	    $('.main-menu').find('a[href="/additional/credit_app"]').parents().addClass('active');
	</script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.2/dist/jquery.validate.min.js"></script>
    <script type="text/javascript">
    	$(document).ready(function () {
		  $("#martform").submit(function(event){
		    $("#martform").validate({
		      rules: {
		        duns_number: {
		          required: true,
		        },
		        sic_code: {
		          required: true,
		        },
		        naics_code: {
		          required: true,
		        },
		        first_sale: {
		          required: true,
		        },
		        last_sale: {
		          required: true,
		        },
		        credit_limit: {
		          required: true,
		        },
		        business_address: {
		          required: true,
		        },
		      },
		    });
		    if (!$("#martform").valid()) {
		      event.preventDefault();
		    }
		  });
		});

    </script>
@endsection