@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">

<style type="text/css">
  select.form-control.yeardropdown {
      padding-left: 5px;
      padding-right: 0px;
  }
  .phoneBillLabel a {
      font-size: 14px;
      font-weight: 800;
      color: #4fc3f7;
  }
  span.phoneBillLabel {
      margin-left: 15px;
  }
  .row.creditcardinfo {
      margin-bottom: 20px;
  }
</style>
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         @if (session()->has('success'))
            <div class="alert close_alert alert-success">
              <strong>Info!</strong> {!! session()->get('success') !!}
            </div>
        @elseif (session()->has('error'))
            <div class="alert close_alert alert-danger">
              <strong>Info!</strong> {!! session()->get('error') !!}
            </div>
        @endif
         <div class="card-block" id="profile_card">
            <div class="row">
               <div class="col-md-12">
                  <div id="wizard">
                     <section>
                        <div class="container">
                          <ul class="nav nav-pills">
                            <li><a class="active show" data-toggle="pill" href="#menu1">Finance / credit line information</a></li>
                            <li><a data-toggle="pill" href="#menu2">Monthly Phone Bill</a></li>
                            <li><a data-toggle="pill" href="#menu3">Login Information</a></li>
                            <!--<li><a data-toggle="pill" href="#menu4">Credit Card Information</a></li>-->
                            <li><a data-toggle="pill" href="#menu5">Credit Card Billing Information</a></li>

                          </ul>
                          <div class="tab-content">
                            <div id="menu1" class="tab-pane fade in active show">
                              <form method="post" action="/act_updateCreditLineInfo">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <div class="row">   
                                  <div class="col-lg-12 col-md-12 col-sm-12" id="tab_body">
                                    <h3>Finance / credit line information </h3>
                                    <hr>
                                    <div class="row form-group">
                                      @if($payablebalanceDet)
                                        <div class="col-lg-4 space-right">
                                           <label>Credit Limit</label>
                                        </div>
                                        <div class="col-lg-2 clearfix text-left">
                                           <p>$7500.00</p>
                                        </div>
                                        <div class="col-lg-4 space-right">
                                           <label>Balance</label>
                                        </div>
                                        <div class="col-lg-2 text-left">
                                         
                                             <p>
                                                <?php
                                                   $r_amount=round(floatval($payablebalanceDet->r_amount),3);
                                                   echo "$".$r_amount;
                                                ?>

                                             </p>
                                       </div>
                                    </div>
                                    <div class="row form-group">
                                          <div class="col-lg-4 space-right">
                                             <label>Available Credit</label>
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <p>${{ 7500 - $r_amount }}</p>
                                          </div>

                                          @else
                                          <div class="col-lg-4 space-right">
                                             <label>Credit Limit</label>
                                          </div>
                                          <div class="col-lg-2 clearfix text-left">
                                             <p>$7500.00</p>
                                          </div>
                                          <div class="col-lg-4 space-right">
                                             <label>Balance</label>
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <p>$0.00</p>
                                          </div>
                                       </div>
                                       <div class="row form-group">
                                          <div class="col-lg-4 space-right">
                                             <label>Available Credit</label>
                                          </div>
                                          <div class="col-lg-6 text-left">
                                             <p>$7500.00</p>
                                          </div>

                                          @endif

                                         <div class="col-lg-4 space-right">
                                             <label>Finance Terms</label>
                                          </div>

                                          <div class="col-lg-4 text-left">
                                             <p>50% down</p>
                                          </div>
                                       </div>
                                       @if($userDet)
                                       <div class="row form-group" >
                                          <div class="col-lg-4 space-right">
                                             <!-- <label>Globalvoicedirect Acct #</label> -->
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <!-- <p>{{ $userDet->ID }}</p> -->
                                          </div>
                                       </div>
                                        <div class="row margint-20" id="form_row">
                                          <div class="col-lg-3 space-right">
                                             <label>Company Name</label>
                                          </div>
                                          <div class="col-lg-8">
                                             <input class="form-control" name="nicename" value="{{ $userDet->user_nicename }}" type="text">
                                          </div>
                                       </div>
                                        <div class="row margint-20" id="form_row">
                                          <div class="col-lg-3 space-right">
                                             <label>Display Name</label>
                                          </div>
                                          <div class="col-lg-8">
                                             <input class="form-control" name="displayname" value="{{ $userDet->display_name }}" type="text">
                                          </div>
                                       </div>
                                       <div class="row margint-20" id="form_row">
                                          <div class="col-lg-3 space-right">
                                             <label>Email</label>
                                          </div>
                                          <div class="col-lg-8">
                                             <input class="form-control" name="email" value="{{ $userDet->user_email }}" type="text">
                                          </div>
                                       </div>
                                        <div class="row margint-20" id="form_row">
                                          <div class="col-lg-3 space-right">
                                             <label>URL</label>
                                          </div>
                                          <div class="col-lg-8">
                                             <input class="form-control" name="url" value="{{ $userDet->user_url }}" type="text">
                                          </div>
                                       </div>
                                       <div class="row margint-30" id="form_row">
                                          <div class="col-lg-12">
                                             <button type="submit" class="btn btn-info btn-blue">Update</button>
                                          </div>
                                       </div>
                                       @endif
                                       
                                  </div>
                                   
                                </div>
                              </form>
                             
                            </div>
                            <div id="menu2" class="tab-pane fade">
                               <div class="row">
                                 <div class="col-lg-12 col-md-12 col-sm-12" id="tab_body">
                                    <h3>Monthly Phone Bill</h3>
                                    <hr>
                                    <div class="form-inline">
                                       <div class="row form-group">
                                           <span class="phoneBillLabel">Please <a href="https://mybilling.globalvoicedirect.com:8082">Click Here</a> to see the Monthly Phone Bill Details.</span>
                                       </div>
                                    </div>
                                 </div>
                                  
                              </div>
                            </div>
                            <div id="menu3" class="tab-pane fade">
                              <div class="row">
                                 <div class="col-lg-12 col-md-12 col-sm-12" id="tab_body">
                                    <h3>Login Information</h3>
                                    <hr>
                                    <div class="login_info">
                                       <form method="post" action="updateLogin">
                                          <div class="border-box border-top-none">
                                            @if($userDet)
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                <div class="row" id="form_row">
                                                   <div class="col-lg-3 space-right">
                                                      <label>Username</label>
                                                   </div>
                                                   <div class="col-lg-8">
                                                      <input class="form-control" name="username" value="{{ $userDet->user_login }}" disabled="" type="text">
                                                   </div>
                                                </div>
                                                <div class="row" id="form_row">
                                                   <div class="col-lg-3 space-right">
                                                      <label>Email</label>
                                                   </div>
                                                   <div class="col-lg-8">
                                                      <input class="form-control" name="email" value="{{ $userDet->user_email }}" type="email">
                                                   </div>
                                                </div>
                                                <div class="row" id="form_row">
                                                   <div class="col-lg-3 space-right">
                                                      <label>Password</label>
                                                   </div>
                                                   <div class="col-lg-8">
                                                      <input class="form-control" name="password1" value="" type="password" required>
                                                   </div>
                                                </div>
                                                <div class="row" id="form_row">
                                                   <div class="col-lg-3 space-right">
                                                      <label>Confirm Password</label>
                                                   </div>
                                                   <div class="col-lg-8">
                                                      <input class="form-control" name="password2" value="" type="password" required>
                                                   </div>
                                                </div>
                                                <div class="row margint-30">
                                                   <div class="col-lg-12">
                                                      <button type="submit" class="btn btn-info btn-blue">Update</button>
                                                   </div>
                                                </div>
                                             @endif
                                          </div>
                                       </form>
                                    </div>
                                 </div>
                              </div>
                            </div>
                           
                           <div id="menu5" class="tab-pane fade">
                              <div class="row">
                                 <div class="col-lg-12 col-md-12 col-sm-12" id="tab_body">
                                    <h3>Credit Card Billing Information</h3>
                                    <hr>
                                    <div class="credit_info">
                                       <form method="post" action="/user/updateBillingAddress">
                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                        @if($billingDet)
                                            
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>First Name</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="firstname" value="{{ $billingDet->firstname }}" type="text" required>
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>Last Name</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="lastname" value="{{ $billingDet->lastname }}" type="text" required>
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>Address</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="address" value="{{ $billingDet->address }}" type="text" required>
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>City</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="city" value="{{ $billingDet->city }}" type="text" required>
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>State</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" name="state" class="form-control" value="{{ $billingDet->state }}" required>
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>Zip Code</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="zip" minlength="4" maxlength="6" value="{{ $billingDet->zip }}" type="text" required>
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>Phone</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input type="text" name="phone" class="form-control" value="{{ $billingDet->phone }}" required>
                                            </div>

                                        </div>
                                        <div class="row margint-30" >
                                            <div class="col-lg-12">
                                                <button type="submit" class="btn btn-info btn-blue">Update</button>
                                            </div>
                                        </div>
                                        @else
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>First Name</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="firstname" value="" type="text">
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>Last Name</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="lastname" value="" type="text">
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>Address</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="address" value="" type="text">
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>City</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="city" value="" type="text">
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>State</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input  class="form-control" type="text" name="state" value="">
                                            </div>
                                            <div class="col-lg-2 space-right">
                                                <label>Zip Code</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" name="zip" value="" type="text">
                                            </div>
                                        </div>
                                        <div class="row" id="form_row">
                                            <div class="col-lg-2 space-right">
                                                <label>Phone</label>
                                            </div>
                                            <div class="col-lg-4">
                                                <input class="form-control" type="text" name="phone" value="">
                                            </div>

                                        </div>
                                        <div class="row margint-30">
                                            <div class="col-lg-12">
                                                <button type="submit" class="btn btn-info btn-blue">Update</button>
                                            </div>
                                        </div>
                                      @endif
                                      </form>
                                    </div>
                                </div>
                                 
                              </div>
                            </div>
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
@endsection
@section('scripts')
    <script>
         $(document).ready(function(){
           $(".user-profile-photo").hover(function(){
             $('.edit-user-profile-photo').css("display", "block");
             $('.img-radius').css('opacity','0.6');
             }, function(){
               $('.edit-user-profile-photo').css("display", "none");
               $('.img-radius').css('opacity','1');
           });
         });
           var breadcrumb = $('.breadcrumb');
            breadcrumb.append('<li class="breadcrumb-item"><b><span>Profile</span></b></li>');
          $('.main-menu ul').find('.active').removeClass('active');
          $('.main-menu').find('a[href="/user/profile"]').parents().addClass('active');
      </script>
@endsection