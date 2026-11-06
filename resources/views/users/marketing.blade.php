@extends('layouts.user.user_main')
@section('content')

<link href="/public/css/usersStyle2.css" rel="stylesheet" type="text/css" />
	<!--Content-->
	<div class="container">
		@if(session()->has('status'))
			<div class="alert alert-info">{{ session()->get('status')}}</div>
		@endif
		@if ($errors->any())
		    <div class="alert alert-danger">
		        <ul>
		            @foreach ($errors->all() as $error)
		                <li>{{ $error }}</li>
		            @endforeach
		        </ul>
		    </div>
		@endif
		<div class="card">
			<?php if($userFormDetCount > 0){?>
				<div class="card-header" id="script_header">
			       <h6>YOUR BUSINESS INFORMATION HAS ALREADY BEEN RECEIVED, THANK YOU</h6>
			    </div>
			<?php } ?>
	        <div class="card-block" id="script_block">
	            <div class="row">
	            	<div class="col-md-12">
	                	<div id="wizard">
	                    	<section class="top-detail-section" id="section_market">
	                    		<div class="row justify-content-between">
	                    			<div class="col-md-6" id="marketing_text">
			                          	<p>A local citation is any mention of your business on the web. It is any combiation of your company name, phone number address, zip or postal code and or website address.</p>
			                         	<a href="#" class="btn btn-info">Learn More</a>
			                        </div>
			                        <div class="col-md-4">
										<img id="tree_img"  src="/public/images/tree.png" >
									</div>
		                    	</div>
	               			</section>
	                	</div>
	            	</div>
	        	</div>
				<div class="card-block" id="profile_card">
		            <div class="row">
		                <div class="col-md-12">
		                    <div id="wizard">
		                       <section>
								    <div class="container">
						                <ul class="nav nav-pills" id="market_pills">
						                    <li class="step"><i class="fas fa-briefcase fa-w-16 " id="market_tab_icon"></i><br>Business Information</li>
						                    <li  class="step"><i class="fa fa-map-marker-alt fa-w-16 " id="market_tab_icon" ></i><br> Address Detail</li>
						                    <li  class="step"><i class="fa fa-address-book fa-w-16 " id="market_tab_icon"></i><br> Contact Information</li>
						                </ul>
						                <div class="tab-content" id="marketing_content">
						                	<?php if(isset( $_GET['message'])){ ?>
												<div class="alert alert-info">
												  <strong>Info!</strong> <?php echo $_GET['message']; ?>.
												</div>
											<?php } ?>
							                <main class="content" role="content">	
												<form id="regForm" method="post" enctype="multipart/form-data" action="/actions/act_submitMarForm">
													{{ csrf_field()}}
												    <!-- One "tab" for each step in the form: -->
												    <?php if($userFormDetCount > 0){?>
												    <div class="tab">
												    	<div class="row">
												    		<div class="col-md-6">
												    			<div class="col-md-5">
												    				<label class="text-info">First Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info"name="firstname" value="<?php echo $userFormDetail->first_name; ?>" readonly >
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Last Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name="lastname" value="<?php echo $userFormDetail->last_name; ?>" readonly>
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Phone</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info"  name="phone" value="<?php echo $userFormDetail->buisness_phone; ?>" readonly >
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Email</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control input_info"  type="email" name=" email" value="<?php echo $userFormDetail->buisness_email ?>" readonly >
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Hours</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name="b-hour" value="<?php echo $userFormDetail->buisness_hour; ?>" readonly >
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Website</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name=" b-website" value="<?php echo $userFormDetail->buisness_website; ?>" readonly >
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Description</label>
												    			</div>
														  		<div class="col-md-10">
								                      				<textarea class="form-control input_info" rows="4" name="b-description" readonly><?php echo $userFormDetail->buisness_desc; ?></textarea>
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Category/Keywords</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control input_info" type="text" name=" b-category" value="<?php echo $userFormDetail->buisness_keyword; ?>" readonly />
														  		</div>
														  	</div>

							                      		</div>	
							                      		<div class="row">
							                      			<div class="col-md-12 info_textarea">
							                      				<label class="text-info">Short Description</label>
						                      					<textarea name="short-description" class="form-control market_textarea" readonly><?php echo $userFormDetail->short_description; ?></textarea>
							                      			</div>
							                      			<div class="col-md-12 info_textarea">
											    				<label class="text-info">Social Sites</label>
							                      				<textarea name="social-sites"  class="form-control market_textarea" readonly><?php echo $userFormDetail->social_sites; ?></textarea>
							                      			</div>
							                      		</div>
							                      		<br>
							                      	</div>
							                      	<div class="tab">
													  	<div class="col-md-6 " id="marketing_text">
														  	<label class="text-info">Street Address</label>
							                      			<input class="form-control" name="address" value="<?php echo $userFormDetail->street_address; ?>" type="text" readonly />
							                      			<label class="text-info">City</label>
							                      			<input class="form-control" name="city" value="<?php echo $userFormDetail->city; ?>" type="text" readonly />
							                      			<label class="text-info">State</label>
							                      			<input  class="form-control" type="text" name="state" value="<?php echo $userFormDetail->state; ?>" readonly />
							                      			<label class="text-info">Zip Code</label>
							                      			<input class="form-control" name="zip" value="<?php echo $userFormDetail->zip_code; ?>" type="text" readonly />
								                      	</div>
								                    </div>

							                      	<?php }else{ ?>

											    	<div class="tab">
						                      			<div class="row">
												    		<div class="col-md-6">
												    			<div class="col-md-5">
												    				<label class="text-info">First Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name="firstname" id="First_Name" onkeyup="validate('First_Name', 100)" required>
								                      				<div id="First_Name1"></div>
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info ">Last Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name="lastname" id="Last_Name" onkeyup="validate('Last_Name', 100)" required><div id="Last_Name1"></div>
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Phone</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info"  name="phone" id="Business_Phone" onkeyup="validate('Business_Phone', 100)" required><div id="Business_Phone1"></div>
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Email</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control input_info" name="email"  type="email" id="Business_Email" onkeyup="validate('Business_Email', 100)" required><div id="Business_Email1"></div>
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Hours</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info"  name="b_hour" id="Business_Hours" onkeyup="validate('Business_Hours', 50)" required><div id="Business_Hours1"></div>
														  		</div>
														  	</div>
								                      		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Website</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input type="text" class="form-control input_info" name=" b_website" id="Business_Website" onkeyup="validate('Business_Website', 100)" required><div id="Business_Website1"></div>
														  		</div>
														  	</div>
							                      		</div>
							                      		<div class="row">
												    		<div class="col-md-6" >
														  		<div class="col-md-5">
												    				<label class="text-info">Business Description</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<textarea class="form-control input_info" rows="4" name="b_description" id="Business_Description" onkeyup="validate('Business_Description', 3000)"></textarea><div id="Business_Description1"></div>
														  		</div>
														  	</div>
														  	<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Category/Keywords</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control input_info" type="text" name=" b_category" id="Business_Category" onkeyup="validate('Business_Category', 200)" required /><div id="Business_Category1"></div>
														  		</div>
														  	</div>
							                      		</div>
											    		<div class="row">
							                      			<div class="col-md-12 info_textarea">
							                      				<label class="text-info">Short Description</label>
						                      					<textarea name="short_description" class="form-control market_textarea"  id="Short_Description" onkeyup="validate('Short_Description', 250)" required></textarea><div id="Short_Description1"></div>
							                      			</div>
							                      			<div class="col-md-12 info_textarea">
											    				<label class="text-info">Social Sites</label>
							                      				<textarea name="social_sites"  class="form-control market_textarea" id="Social_Sites" onkeyup="validate('Social_Sites', 500)" required></textarea><div id="Social_Sites1"></div>
							                      			</div>
							                      		</div>
										    		</div>
												    <div class="tab">
													  	<div class="col-md-6 " id="marketing_text">
							                      			<label class="text-info">Street Address</label>
							                      			<input type="text" class="form-control" name="address" id="Street_Address"  onkeyup="validate('Street_Address', 100)" required><div id="Street_Address1"></div>
							                      			<label class="text-info">State</label>
							                      			<input type="text" class="form-control" name="city" id="State" onkeyup="validate('State', 50)" required><div id="State1"></div>
							                      			<label class="text-info">City</label>
							                      			<input type="text" class="form-control" name="state" id="City" onkeyup="validate('City', 50)" required><div id="City1"></div>
							                      			<label class="text-info">Zip Code</label>
							                      			<input type="text" class="form-control" name="zip" id="Zip_Code" onkeyup="validate('Zip_Code', 50)" required><div id="Zip_Code1"></div>
							                      		</div>
												    </div>

													<?php } ?>

												    <div class="tab">

												    	<?php
												    	
												    	if(isset($userFormDetail->image_path)){ ?>

												  		<div class="row" >
												    		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control bname" type="text"  name="buisness_name" value="<?php echo $userFormDetail->buisness_name; ?>"  readonly />
														  		</div>
														  	</div>
														</div>
												  		<div class="row">
												    		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Logo or Other Image</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<img width="130" height="85" src="/public/marketImages/<?php echo $userFormDetail->image_path; ?>" id="blah" />
														  		</div>
														  	</div>
							                      		</div>

														<?php }else{ ?>

														<div class="row" >
												    		<div class="col-md-6">
								                      			<div class="col-md-5">
												    				<label class="text-info">Business Name</label>
												    			</div>
														  		<div class="col-md-7">
								                      				<input class="form-control input_info bname" type="text" name="buisness_name" id="Business_Name" onkeyup="validate('Business_Name', 100)" /><div id="Business_Name1"></div>
														  		</div>
														  	</div>
														</div>
														<div class="row">
															<div class="col-lg-2 space-right">
																<label>Logo or Other Image</label>
															</div>
															<div class="col-lg-4">
																<input type="file" class="form-control" name="form-image" id="imgInp" />
															</div>
															<div class="col-lg-6">
																<img src="" id="blah" />
															</div>
														</div>

														<?php } ?>

												    </div>
												  	<div style="overflow:auto; margin-top: 20px;">
													    <div style="float:right;">
													      <button type="button" class="btn btn-default"  id="prevBtn" onclick="nextPrev(-1)">Previous</button>
													      <button type="button" class="btn btn-info" id="nextBtn" onclick="nextPrev(1)">Next</button>
													    </div>
												  	</div>
												</form>
													<!-- /.MultiStep Form -->		
											</main> <!-- /content -->
						            	</div>
						        	</section>
						    	</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	<!--Content Ends-->
  @endsection
  @push('scripts')
	<script type="text/javascript">
	/*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');
	breadcrumb.text('Marketing Form');
	breadcrumb.prop("href", "marketing_form.php");*/
	var breadcrumb = $('.breadcrumb');
            breadcrumb.append('<li class="breadcrumb-item"><b><span>Marketing Form</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="/marketing-form"]').parents().addClass('active');
	
	// Multi-Step Form
		var currentTab = 0; // Current tab is set to be the first tab (0)
		showTab(currentTab); // Display the crurrent tab
		function showTab(n) {
		  // This function will display the specified tab of the form...
		    var x = document.getElementsByClassName("tab");
		    x[n].style.display = "block";
			  //... and fix the Previous/Next buttons:
			if (n == 0) {
			    document.getElementById("prevBtn").style.display = "none";
			} else {
			    document.getElementById("prevBtn").style.display = "inline";
			}
			if (n == (x.length - 1)) {
			    document.getElementById("nextBtn").innerHTML = "Submit";
			    if($('.bname').attr('readonly').length>0){
			    	if($('#nextBtn').text() == 'Submit')
			    		$('#nextBtn').css("display", 'none');
			    	else
			    		$('#nextBtn').css("display", 'inline-block');

			    }else{
			    	$('#nextBtn').css("display", 'inline-block');
			    }
			} else {
			    document.getElementById("nextBtn").innerHTML = "Next";
			    $('#nextBtn').css("display", 'inline-block');
			}
			  //... and run a function that will display the correct step indicator:
			  fixStepIndicator(n)
		}

		function nextPrev(n) {
		  // This function will figure out which tab to display
		  var x = document.getElementsByClassName("tab");
		  // Exit the function if any field in the current tab is invalid:
		  if (n == 1 && !validateForm()) return false;
		  // Hide the current tab:
		  x[currentTab].style.display = "none";
		  // Increase or decrease the current tab by 1:
		  currentTab = currentTab + n;
		  // if you have reached the end of the form...
		  if (currentTab >= x.length) {
		    // ... the form gets submitted:
		    document.getElementById("regForm").submit();
		    return false;
		  }
		  // Otherwise, display the correct tab:
		  showTab(currentTab);
		}

		function validateForm() {
		  // This function deals with validation of the form fields
		  var x, y,z, i, valid = true;
		  x = document.getElementsByClassName("tab");
		  y = x[currentTab].getElementsByTagName("input");
		  z = x[currentTab].getElementsByTagName("textarea");

		  // A loop that checks every input field in the current tab:
		  for (i = 0; i < y.length; i++) {
		    // If a field is empty...
		    if (y[i].value == "") {
		      // add an "invalid" class to the field:
		      y[i].className += " invalid";
		      // and set the current valid status to false
		      valid = false;
		    }

		  }

		  for (var k = 0; k < z.length; k++) {
		  	if(z[k].value == ""){

		    	z[k].className += " invalid";
		    	valid = false;
		    }
		    
		  }
		  // If the valid status is true, mark the step as finished and valid:
		  if (valid) {
		    document.getElementsByClassName("step")[currentTab].className += " finish";
		  }
		  return valid; // return the valid status
		}

		function fixStepIndicator(n) {
		  // This function removes the "active" class of all steps...
		  var i, x = document.getElementsByClassName("step");
		  for (i = 0; i < x.length; i++) {
		    x[i].className = x[i].className.replace(" active", "");
		  }
		  //... and adds the "active" class on the current step:
		  x[n].className += " active";
		}
		/*form validation*/
		function validate(input_name, limit) {

			if (input_name == 'Business_Email') {
				var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/; 
		        var emailaddressVal = $("#"+input_name+"").val();
		        if(!emailReg.test(emailaddressVal)) {
		            $('#'+input_name+'1').html('<p id="error_msg" style="color:red; font-size:smaller">Please Enter a Valid Email Address</p>');
		        }else{
		        	$('#'+input_name+'1').html('');
		        }
			}
			if (input_name == 'Business_Phone') {
				var phoneReg = /\(?([0-9]{3})\)?([ .-]?)([0-9]{3})\2([0-9]{4})/; 
		        var phoneVal = $("#"+input_name+"").val();
		        if(!phoneReg.test(phoneVal)) {
		            $('#'+input_name+'1').html('<p id="error_msg" style="color:red; font-size:smaller">Please Enter a Valid Phone Number</p>');
		        }else{
		        	$('#'+input_name+'1').html('');
		        }
			}
			if (input_name == 'Zip_Code') {
				var zipReg = /(^\d{5}$)|(^\d{5}-\d{4}$)/; 
		        var zipVal = $("#"+input_name+"").val();
		        if(!zipReg.test(zipVal)) {
		            $('#'+input_name+'1').html('<p id="error_msg" style="color:red; font-size:smaller">Please Enter a Valid Zip Code</p>');
		        }else{
		        	$('#'+input_name+'1').html('');
		        }
			}

			if ($("#"+input_name).val().length > limit) {
				$('#'+input_name+'1').html('<p id="error_msg" style="color:red; font-size:smaller">'+input_name+' has to be '+limit+' digits/character long.</p>');
			}			
		}
    </script>
    @endpush
	