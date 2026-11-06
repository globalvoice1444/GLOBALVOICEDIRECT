@extends('layouts.user.user_main')
@section('css')
	<link href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
 	<style type="text/css">
        .wizard {
            margin: 20px auto;
            background: #fff;
            border: 1px solid #cbcbcb;
            border-radius: 8px;
        }
        .container section {
            width: 95% !important;
        }
        .wizard .nav-tabs {
            width: 90%;
            position: relative;
            margin: 40px auto;
            margin-bottom: 0;
            border-bottom-color: #5bc0de;
        }

        .wizard > div.wizard-inner {
            position: relative;
        }

        .connecting-line {
            height: 2px;
            background: #e0e0e0;
            position: absolute;
            width: 80%;
            margin: 0 auto;
            left: 0;
            right: 0;
            top: 50%;
            z-index: 1;
        }

        .wizard .nav-tabs > li.active > a, .wizard .nav-tabs > li.active > a:hover, .wizard .nav-tabs > li.active > a:focus {
            color: #555555;
            cursor: default;
            border: 0;
            border-bottom-color: transparent;
        }

        span.round-tab {
            width: 70px;
            height: 70px;
            line-height: 70px;
            display: inline-block;
            border-radius: 100px;
            background: #fff;
            border: 2px solid #e0e0e0;
            z-index: 2;
            position: absolute;
            left: 0;
            text-align: center;
            font-size: 25px;
        }
        span.round-tab i{
            color:#555555;
        }
        .wizard li.active span.round-tab {
            background: #fff;
            border: 2px solid #5bc0de;
        }
        .wizard li.active span.round-tab i{
            color: #5bc0de;
        }

        span.round-tab:hover {
            color: #333;
            border: 2px solid #333;
        }
        .wizard .nav-tabs > li {
            width: 20%;
        }
        .wizard li:after {
            content: " ";
            position: absolute;
            left: 46%;
            opacity: 0;
            margin: 0 auto;
            bottom: 0px;
            border: 5px solid transparent;
            border-bottom-color: #5bc0de;
            transition: 0.1s ease-in-out;
        }

        .wizard li.active:after {
            content: " ";
            position: absolute;
            left: 46%;
            opacity: 1;
            margin: 0 auto;
            bottom: 0px;
            border: 10px solid transparent;
            border-bottom-color: #5bc0de;
        }
        .wizard .nav-tabs > li a {
            width: 70px;
            height: 70px;
            margin: 20px auto;
            border-radius: 100%;
            padding: 0;
        }
        .wizard .nav-tabs > li a:hover {
            background: transparent;
        }

        .wizard .tab-pane {
            position: relative;
        }
        .wizard h3 {
            margin-top: 0;
        }
        .tab-content {
            background-color: #fff;
            margin: 0;
        }
        .form-group {
            width: 100%;
        }
        h2 {
            color: #5bc0de;
        }
        .form-group {
            color: #88cce0;
        }
        .col-md-12 {
            margin-bottom: 5px;
        }
        input[type="text"] {
            border: 1px solid #97cce2;
        }
        iframe {
            padding: 15px 0 6px 0;
        }
        .left-text h4 {
            text-align: center;
            margin-top: 10%;
            color: #fff;
            font-size: 40px;
        }
        .row.top-video-section {
            background: #3f4d67;
        }
        button#step1-next {
            margin-top: 5%;
            margin-left: 3%;
        }
        button#step2-previous {
            margin-left: 15px;
        }
        .form-group.step3-buttons {
            margin-top: 38px;
            margin-left: 0px;
        }
        .form-group.step4-button {
            margin: 0px;
        }
        .btn {
            width: 160px;
        }
        .display-info {
            text-align: center;
            color: #686363;
        }
        @media( max-width : 585px ) {

            .wizard {
                width: 90%;
                height: auto !important;
            }

            span.round-tab {
                font-size: 16px;
                width: 50px;
                height: 50px;
                line-height: 50px;
            }

            .wizard .nav-tabs > li a {
                width: 50px;
                height: 50px;
                line-height: 50px;
            }
            .wizard li.active:after {
                content: " ";
                position: absolute;
                left: 35%;
            }
        }
    </style>
@endsection
@section('content')
	 <div class="container">
	 	@if($errors->any())
	 	<div class="alert alert-danger">
	 		@foreach($errors->all() as $error)
	 		{{ $error }}
	 		@endforeach
	 	</div>
	 	@endif
	 	@if(session('status'))
        	<div class="alert alert-success">{{ session('status')}}</div>
      	@endif
        <div class="row">
            <section>
                <div class="wizard">
                    <div class="row top-video-section">
                        <div class="col-md-7 left-text">
                            <h4>You've Got Questions<br/>We've Got Answers</h4>
                        </div>
                        <div class="col-md-1"></div>
                        <div class="col-md-4 right-video">
                            <iframe height="200" width="320" src="https://www.youtube-nocookie.com/embed/wmnxwPEHbqA?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>
                        </div>
                    </div>
                    <?php $userFormDetCount = count($userFormDet); ?>
                    @if($userFormDetCount <= 0)
	                      <div class="wizard-inner">
                          <div class="connecting-line"></div>
                            <ul class="nav nav-tabs" role="tablist">
                              <li role="presentation" id="step1-tab" class="active">
                                <a href="#step1" data-toggle="tab" aria-controls="step1" role="tab" title="Step 1" class="hideShow">
                                  <span class="round-tab">
                                    <i class="glyphicon glyphicon-briefcase"></i>
                                  </span>
                                </a>
                              </li>
                              <li role="presentation" id="step2-tab" class="disabled">
                                 <a href="#step2" data-toggle="tab" aria-controls="step2" role="tab" title="Step 2" class="hideShow">
                                     <span class="round-tab">
                                         <i class="glyphicon glyphicon-list-alt"></i>
                                     </span>
                                 </a>
                              </li>
                              <li role="presentation" id="step3-tab" class="disabled">
                                 <a href="#step3" data-toggle="tab" aria-controls="step3" role="tab" title="Step 3" class="hideShow">
                                     <span class="round-tab">
                                         <i class="glyphicon glyphicon-user"></i>
                                     </span>
                                 </a>
                              </li>
                              <!-- <li role="presentation" id="step4-tab" class="disabled">
                                  <a href="#step4" data-toggle="tab" aria-controls="step4" role="tab" title="Step 4" class="hideShow">
                                      <span class="round-tab">
                                          <i class="glyphicon glyphicon-briefcase"></i>
                                      </span>
                                  </a>
                              </li> -->

                              <li role="presentation" id="step5-tab" class="disabled">
                                 <a href="#complete" data-toggle="tab" aria-controls="complete" role="tab" title="Complete" class="hideShow">
                                     <span class="round-tab">
                                         <i class="glyphicon glyphicon-ok"></i>
                                     </span>
                                 </a>
                              </li>
                            </ul>
                        </div>
                        <form class="form-horizontal" method="post" action="{{ route('act_submitcreditinfo')}}" enctype="multipart/form-data">
                          {{ csrf_field() }}
                          <div class="tab-content">
                            <div class="tab-pane active" role="tabpanel" id="step1">
                              <div class="row">
                                 <h2>About Your Business</h2>
                              </div>
                              <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Legal Business name : <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="lbuisnessname" id="lbuisnessname" value="" placeholder="Please enter the Legal Business Name" required="true" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Business Address <span class="starcolor">*</span></strong></div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Street Address: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="streetaddress"  id="streetaddress" class="form-control" value="" placeholder="Street Address" required="" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">City: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="city" id="city" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Zip / Postal Code: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="zip_code" id="zip_code" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Main Business Phone: <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12"><input type="text" name="phone_number" id="phone_number" class="form-control" value="" required="" /></div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Year Business Started: <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12"><input type="text" name="buisnessyear" id="buisnessyear" class="form-control" value="" required="" /></div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Tax ID or (FEIN) Number: </strong></div>
                                         <div class="col-md-12"><input type="text" name="taxnumber" id="taxnumber" class="form-control" value="" required="" /></div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Doing business As:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="doingbuisness" id="doingbuisness" class="form-control" value="" placeholder="Please enter a DBA (Doing Business As) name" />
                                         </div>
                                     </div>
                                      <div class="form-group">
                                         <div class="col-md-12"><strong>Business Address <span class="starcolor">*</span></strong></div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Address Line 2:</div>
                                         <div class="col-md-12">
                                             <input type="text" name="streetaddress1" id="streetaddress1" class="form-control" value="" placeholder="Address Line 2" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">State / Province / Region: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="state" id="state" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Country </div>
                                         <div class="col-md-12">
                                             <input type="text" name="country" id="country" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Estimated Monthly Purchase Volume: $ <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12"><input type="text" name="monthlypurchase" id="monthlypurchase" class="form-control" value="" required="" /></div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Total Number of Employees: <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12"><input type="text" name="employees" id="employees" class="form-control" value="" required="" /></div>
                                     </div>
                                     <div class="form-group">
                                         <button type="button" id="step1-next" class="btn btn-primary next-step">Save and continue</button>
                                     </div>

                                 </div>
                              </div>
                              <!-- <ul class="list-inline pull-right">
                                 <li><button type="button" class="btn btn-primary next-step">Save and continue</button></li>
                              </ul> -->
                            </div>
                            <div class="tab-pane" role="tabpanel" id="step2">
                             <div class="row">
                                 <h2>Billing Address</h2>
                             </div>
                             <div class="row">
                                 <div class="col-md-12">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Billing Address if Diferent From Above: <span class="starcolor">*</span></strong></div>
                                     </div>
                                 </div>
                                 <div class="col-md-12">
                                     <div class="form-group">
                                         <div class="col-md-12"><input type="checkbox" class="checkbox" name="checkbox" value="Same as previous"  />Same as previous</div>
                                     </div>
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Street Address:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bstreetaddress" id="bstreetaddress" class="form-control" value="" placeholder="Street Address" required="" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>City:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bcity1" id="bcity1" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Zip / Postal Code:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bzip_code1" id="bzip_code1" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                 </div>

                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Address Line 2:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bstreetaddress1" id="bstreetaddress1" class="form-control" value="" placeholder="Address Line 2" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>State / Province / Region:</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bstate1" id="bstate1" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Country</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="bcountry1" id="bcountry1" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <button type="button" id="step2-previous" class="btn btn-default prev-step">Previous</button>
                                         <button type="button" id="step2-next" class="btn btn-primary next-step">Save and continue</button>
                                     </div>
                                 </div>
                             </div>
                              <!-- <ul class="list-inline pull-right">
                                 <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                                 <li><button type="button" class="btn btn-primary next-step">Save and continue</button></li>
                              </ul> -->
                            </div>
                            <div class="tab-pane" role="tabpanel" id="step3">
                             <div class="row">
                                 <h2>Billing Address</h2>
                             </div>
                             <div class="row">
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Business Title: <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="buisnesstitle" id="buisnesstitle" value="" placeholder="Please enter the Legal Business Name" required="true" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Last Name <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="lastname" id="lastname" class="form-control" value=""  required="true" placeholder="Lat Name" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Address Line 2:</div>
                                         <div class="col-md-12">
                                             <input type="text" name="hstreetaddress1" id="hstreetaddress1" class="form-control" value="" placeholder="Address Line 2" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">State / Province / Region: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="hstate" id="hstate" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Country </div>
                                         <div class="col-md-12">
                                             <input type="text" name="hcountry" class="form-control" value="" required/>
                                         </div>
                                     </div> 
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Mobile : <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="mobile" value="" placeholder=" " required="true" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Birthday : <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="date" class="form-control" name="birthday" value="" placeholder=" " required="true" />
                                         </div>
                                     </div>
                                      <div class="form-group">
                                         <div class="col-md-12">License State : </div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="licensestate" value="" placeholder=" " required="true" />
                                         </div>
                                     </div>     
                                 </div>
                                 <div class="col-md-6">
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>First Name</strong></div>
                                         <div class="col-md-12">
                                             <input type="text" name="firstname" id="firstname" class="form-control" value="" placeholder="First Name" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Street Address: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="hstreetaddress" id="hstreetaddress" class="form-control" value="" placeholder="Street Address" required="" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">City: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="hcity" id="hcity" class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12">Zip / Postal Code: </div>
                                         <div class="col-md-12">
                                             <input type="text" name="hzip_code" id="hzip_code"class="form-control" value="" required/>
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Home Phone : <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="homephone" id="homephone" value="" placeholder=" " required="true" />
                                         </div>
                                     </div>
                                     <div class="form-group">
                                         <div class="col-md-12"><strong>Email Address : <span class="starcolor">*</span></strong></div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" id="emailaddress" name="emailaddress" value="" placeholder="" required="true" />
                                         </div>
                                      </div>
                                      <div class="form-group">
                                         <div class="col-md-12">Driver License : </div>
                                         <div class="col-md-12">
                                             <input type="text" class="form-control" name="driverlicence" id="driverlicence" value="" placeholder=" " required="true" />
                                         </div>
                                      </div>
                                      <div class="form-group step3-buttons">
                                         <button type="button" id="step3-previous" class="btn btn-default prev-step">Previous</button>
                                         <button type="button" id="step3-next" class="btn btn-primary next-step">Save and continue</button>
                                     </div>
                                 </div>
                             </div>
                             <!-- <ul class="list-inline pull-right">
                                 <li><button type="button" class="btn btn-default prev-step">Previous</button></li>
                                 <li><button type="button" class="btn btn-primary btn-info-full next-step">Save and continue</button></li>
                             </ul> -->
                            </div>
                            <!-- <div class="tab-pane" role="tabpanel" id="step4">
                                <div class="row">
                                    <h2>SBFE SMALL BUSINESS FINANCIAL EXCHANGE CREDIT BUREAU</h2>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-12">Business Name : </div>
                                            <div class="col-md-12">
                                                <input type="text" id="additional_bussiness_name" class="form-control" name="additional_bussiness_name" value="" placeholder=" " required="true" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12"><strong>Business Address : <span class="starcolor">*</span></strong></div>
                                            <div class="col-md-12">
                                                <input type="text" id="additional_bussines_address" class="form-control" name="additional_bussines_address" value="" placeholder=" " required="true" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12"><strong>Duns Number: <span class="starcolor">*</span></strong></div>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="duns_number" id="duns_number" value="" placeholder="Please enter the Legal Business Name" required="true" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12"><strong>Sic code <span class="starcolor">*</span></strong></div>
                                            <div class="col-md-12">
                                                <input type="text" name="sic_code" id="sic_code" class="form-control" value=""  required="true" placeholder="Lat Name" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12"><strong>Additional Credit Email<span class="starcolor">*</span></strong></div>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="add_credit_email" id="add_credit_email" value="" placeholder=" " required="true" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="col-md-12">Naics code</div>
                                            <div class="col-md-12">
                                                <input type="text" name="naics_code" id="naics_code" class="form-control" value="" placeholder="Address Line 2" required/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">First Sale</div>
                                            <div class="col-md-12">
                                                <input type="text" name="first_sale" id="first_sale" class="form-control" value="" required/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="col-md-12">Last Sale </div>
                                            <div class="col-md-12">
                                                <input type="text" name="last_sale" id="last_sale" class="form-control" value="" required/>
                                            </div>
                                        </div> 
                                        <div class="form-group">
                                            <div class="col-md-12"><strong>Credit Limit<span class="starcolor">*</span></strong></div>
                                            <div class="col-md-12">
                                                <input type="text" class="form-control" name="credit_limit" id="credit_limit" value="" placeholder=" " required="true" />
                                            </div>
                                        </div>
                                       <div class="form-group step3-buttons">
                                            <button type="button" id="step4-previous" class="btn btn-default prev-step">Previous</button>
                                            <button type="button" id="step4-next" class="btn btn-primary next-step">Save and continue</button>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                            <div class="tab-pane" role="tabpanel" id="complete">
                               <div class="row">
                                   <h2>Proof of Buissness</h2>
                               </div>
                               <div class="row">
                                   <div class="form-group">
                                       <div class="col-md-12"><strong>Please upload proof of business filling from your secretary of state : </strong>
                                       </div>
                                   </div>
                                   <div class="form-group">
                                    
                                       <div class="col-md-12">
                                           <input type="file" name="file" id="file" size="50" required />
                                       </div>
                                   </div>
                                   <div class="form-group">
                                       <button type="button" id="step5-previous" class="btn btn-default prev-step">Previous</button>
                                       <button type="Submit" id="step5-next" class="btn btn-primary next-step">Submit</button>
                                   </div>
                               </div>
                            </div>
                            <div class="clearfix"></div>
                          </div>
                        </form>
                    @else
                    <div class="admin-content">
        
					        <div class="bg-white">
					            <div class="tab-content responsive">
					                <div class="display-info">
					                    <p>We've received your Global Voice Direct Credit line / loan application</p>
					                    <p>This is an automated response to your Credit Application.</p>
					                    <p>If you have a question about your application, please email <a href="mailto:underwriting@globalvoicedirect.com">underwriting@globalvoicedirect.com</a></p>
					                </div>
					            </div>
					        </div>
					    </div>
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
@section('scripts')
	<script type="text/javascript">
        $(".checkbox").change(function() {
            if(this.checked) {
                $('#bstreetaddress1').val($('#streetaddress1').val());
                $('#bstreetaddress').val($('#streetaddress').val());
                $('#bcity1').val($('#city').val());
                $('#bstate1').val($('#state').val());
                $('#bzip_code1').val($('#zip_code').val());
                $('#bcountry1').val($('#country').val());
            }else{
                $('#bstreetaddress1').val("");
                $('#bstreetaddress').val("");
                $('#bcity1').val("");
                $('#bstate1').val("");
                $('#bzip_code1').val("");
                $('#bcountry1').val("");
            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            //Initialize tooltips
            $('.nav-tabs > li a[title]').tooltip();
            
            //Wizard
            $('a[data-toggle="tab"]').on('show.bs.tab', function (e) {

                var $target = $(e.target);
            
                if ($target.parent().hasClass('disabled')) {
                    return false;
                }
            });

            // $(".next-step").click(function (e) {

            //     var $active = $('.wizard .nav-tabs li.active');
            //     $active.next().removeClass('disabled');
            //     nextTab($active);

            // });
            // $(".prev-step").click(function (e) {

            //     var $active = $('.wizard .nav-tabs li.active');
            //     prevTab($active);

            // });
            $(".hideShow").click(function (e) {
                removeShowclass(e);
            });
            $("#step1-next").click(function (e) {
                var flag = true;
                var missingfields = "";
                if($('#lbuisnessname').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Legal Buisness Name.\n";
                }
                if($('#doingbuisness').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Doing Buisness As.\n";
                }
                if($('#streetaddress').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Street Address.\n";
                }
                if($('#streetaddress1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Address Line.\n";
                }
                if($('#city').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the City.\n";
                }
                if($('#state').val() == ""){
                    flag = false;
                    missingfields += "Please Enter State.\n";
                }
                if($('#zip_code').val() == ""){
                    flag = false;
                    missingfields += "Please Enter zip code.\n";
                }
                if($('#country').val() == ""){
                    flag = false;
                    missingfields += "Please Enter country.\n";
                }
                if($('#phone_number').val() == ""){
                    flag = false;
                    missingfields += "Please Enter phone number.\n";
                }
                if($('#employees').val() == ""){
                    flag = false;
                    missingfields += "Please Enter Total Number of Employees.\n";
                }
                if($('#taxnumber').val() == ""){
                    flag = false;
                    missingfields += "Please Enter Tax id or FEIN (Number).\n";
                }
                if($('#monthlypurchase').val() == ""){
                    flag = false;
                    missingfields += "Please Enter monthly purchase.\n";
                }
                if($('#buisnessyear').val() == ""){
                    flag = false;
                    missingfields += "Please Enter Year Buisness Started.\n";
                }
                 
                if(flag){
                    $("#step2-tab").addClass("active");
                    $("#step1-tab").removeClass("active");
                    $("#step1-tab").addClass("disabled");
                    //panel
                    $("#step2").addClass("active");
                    $("#step1").removeClass("active");
                }
                else{
                    alert(missingfields);
                }
                 
            });
            $("#step2-next").click(function (e) {
                var flag = true;
                var missingfields = "";
                if($('#bstreetaddress').val() == ""){
                    flag = false;
                    missingfields += "Please Enter street address.\n";
                }
                if($('#bstreetaddress1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the address line2.\n";
                }
                if($('#bcity1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter City.\n";
                }
                if($('#bstate1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the State.\n";
                }
                if($('#bzip_code1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter Zip Code.\n";
                }
                if($('#bcountry1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter Country.\n";
                }
                if(flag){
                    $("#step3-tab").addClass("active");
                    $("#step2-tab").removeClass("active");
                    $("#step2-tab").addClass("disabled");
                    //panel
                    $("#step3").addClass("active");
                    $("#step2").removeClass("active");
                }else{
                    alert(missingfields);
                }
                
            });
            $("#step2-previous").click(function (e) {
                 
                $("#step1-tab").addClass("active");
                $("#step2-tab").removeClass("active");
                $("#step2-tab").addClass("disabled");
                //panel
                $("#step1").addClass("active");
                $("#step2").removeClass("active");
                 
            });
            $("#step3-next").click(function (e) {

                var flag = true;
                var missingfields = "";
                if($('#buisnesstitle').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the buisness title.\n";
                }
                if($('#firstname').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the First Name.\n";
                }
                if($('#lastname').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the last name.\n";
                }
                if($('#hstreetaddress').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the street address.\n";
                }
                if($('#hstreetaddress1').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Address Line2.\n";
                }
                if($('#hcity').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the city.\n";
                }
                if($('#hstate').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the state.\n";
                }
                if($('#hzip_code').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the zip code.\n";
                }
                if($('#hcountry').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the country.\n";
                }
                if($('#homephone').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the chome phone.\n";
                }
                if($('#mobile').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the mobile number.\n";
                }
                if($('#emailaddress').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the email address.\n";
                }
                if($('#birthday').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the Birthday.\n";
                }
                if($('#driverlicence').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the driver licence.\n";
                }
                if($('#licensestate').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the licence state.\n";
                }
                if(flag){
                    $("#step4-tab").addClass("active");
                    $("#step3-tab").removeClass("active");
                    $("#step3-tab").addClass("disabled");
                    //panel
                    // $("#step4").addClass("active");
                    // $("#step3").removeClass("active");
                    //10-2-21
                    $("#complete").addClass("active");
                    $("#step3").removeClass("active");
                }else{
                    alert(missingfields);
                }
                
            });
            $("#step3-previous").click(function (e) {
                $("#step2-tab").addClass("active");
                $("#step3-tab").removeClass("active");
                $("#step3-tab").addClass("disabled");
                //panel
                $("#step2").addClass("active");
                $("#step3").removeClass("active");
            });
            $("#step4-next").click(function (e) {

                var flag = true;
                var missingfields = "";
                if($('#additional_bussiness_name').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the buisness name.\n";
                }
                if($('#additional_bussines_address').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the business.\n";
                }
                if($('#first_sale').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the first sale.\n";
                }
                if($('#last_sale').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the last sale.\n";
                }
                if($('#duns_number').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the duns number Line2.\n";
                }
                if($('#credit_limit').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the credit limit.\n";
                }
                if($('#naics_code').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the naics code.\n";
                }
                if($('#sic_code').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the zip sic code.\n";
                }
                else if(!(/^[0-9]*$/).test($('#credit_limit').val())){
                    flag = false;
                    missingfields += "Please Enter the correct credit limit.\n";
                }else if($('#add_credit_email').val() == ""){
                    flag = false;
                    missingfields += "Please Enter the additional credit email.\n";
                }
                if(flag){
                    $("#step5-tab").addClass("active");
                    $("#step4-tab").removeClass("active");
                    $("#step4-tab").addClass("disabled");
                    //panel
                    $("#complete").addClass("active");
                    $("#step4").removeClass("active");
                }else{
                    alert(missingfields);
                }
                
            });
            $("#step4-previous").click(function (e) {
                $("#step3-tab").addClass("active");
                $("#step4-tab").removeClass("active");
                $("#step4-tab").addClass("disabled");
                //panel
                $("#step3").addClass("active");
                $("#step4").removeClass("active");
            });
            $("#step5-previous").click(function (e) {
                $("#step4-tab").addClass("active");
                $("#step5-tab").removeClass("active");
                $("#step5-tab").addClass("disabled");
                //panel
                $("#step4").addClass("active");
                $("#complete").removeClass("active");
            });
            function removeShowclass(e){
                 
                if ( $('.tab-pane').hasClass("show") ) { 
                    $('.tab-pane').removeClass("show");
                }

            }
        });

        function nextTab(elem) {
            $(elem).next().find('a[data-toggle="tab"]').click();
        }
        function prevTab(elem) {
            $(elem).prev().find('a[data-toggle="tab"]').click();
        }
    </script>
    <script type="text/javascript">
        /*var breadcrumb = $('.breadcrumb .breadcrumb-item').last().find('a');
        breadcrumb.text('Credit Form');
        breadcrumb.prop("href", "/user/credit_app");*/
        var breadcrumb = $('.breadcrumb');
        breadcrumb.append('<li class="breadcrumb-item"><b><span>Credit Form</span></b></li>');
        $('.main-menu ul').find('.active').removeClass('active');
        $('.main-menu').find('a[href="/user/credit_app"]').parents().addClass('active');
    </script>
@endsection