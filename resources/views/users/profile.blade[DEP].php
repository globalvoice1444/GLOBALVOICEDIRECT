@extends('layouts.user.user_main')
@section('content')
<div class="row">
   <div class="col-sm-12">
      <div class="card">
         <?php if(isset($_GET['message'])){?>
            <div class="alert close_alert alert-info">
              <strong>Info!</strong> <?php echo $_GET['message']; ?>
            </div>
         <?php } ?>
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
                            <li><a data-toggle="pill" href="#menu4">Credit Card Information</a></li>
                            <li><a data-toggle="pill" href="#menu5">Credit Card Billing Information</a></li>

                          </ul>
                          <div class="tab-content">
                            <div id="menu1" class="tab-pane fade in active show">
                              <form method="post" action="actions/act_updateuser.php">
                                <div class="row">
                                  <div class="col-lg-8 col-md-7 col-sm-6" id="tab_body">
                                    <h3>Finance / credit line information </h3>
                                    <hr>
                                    <div class="row form-group">
                                      @if($balenceDet)
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
                                           <p>{{ $payablebalanceDet->r_amount }}</p>
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                          <div class="col-lg-4 space-right">
                                             <label>Available Credit</label>
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <p>${{ 7500 - $payablebalanceDet->r_amount }}</p>
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
                                          <div class="col-lg-6 space-right">
                                             <label>Available Credit</label>
                                          </div>
                                          <div class="col-lg-6 text-left">
                                             <p>$7500.00</p>
                                          </div>

                                          @endif

                                         <div class="col-lg-4 space-right">
                                             <label>Finance Terms</label>
                                          </div>

                                          <div class="col-lg-2 text-left">
                                             <p>50% down</p>
                                          </div>
                                       </div>
                                       @if($userDet)
                                       <div class="row form-group" >
                                          <div class="col-lg-4 space-right">
                                             <label>Globalvoicedirect Acct #</label>
                                          </div>
                                          <div class="col-lg-2 text-left">
                                             <p>{{ $userDet->ID }}</p>
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
                                  <div class="col-lg-4 col-md-5 col-sm-6 space-left" id="tab_map">
                                     <div class="img-thumbnail cus-thumbnail">
                                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059949311!2d-74.25986972616433!3d40.69714940906666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1518003746633" height="255" frameborder="0" style="border:0; width:100%;" allowfullscreen></iframe>
                                     </div>
                                  </div>
                                </div>
                              </form>
                             
                            </div>
                            <div id="menu2" class="tab-pane fade">
                               <div class="row">
                                 <div class="col-lg-8 col-md-7 col-sm-6" id="tab_body">
                                    <h3>Monthly Phone Bill</h3>
                                    <hr>
                                    <div class="form-inline">
                                       <div class="row form-group">
                                           <span class="phoneBillLabel">Please <a href="https://mybilling.globalvoicedirect.com:8082">Click Here</a> to see the Monthly Phone Bill Details.</span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="col-lg-4 col-md-5 col-sm-6 space-left" id="tab_map">
                                    <div class="img-thumbnail cus-thumbnail">
                                       <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d387193.3059949311!2d-74.25986972616433!3d40.69714940906666!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c24fa5d33f083b%3A0xc80b8f06e177fe62!2sNew+York%2C+NY!5e0!3m2!1sen!2s!4v1518003746633" height="255" frameborder="0" style="border:0; width:100%;" allowfullscreen></iframe>
                                    </div>
                                 </div>
                              </div>
                            </div>
                            <div id="menu3" class="tab-pane fade">
                              <div class="row">
                                 <div class="col-lg-8 col-md-7 col-sm-6" id="tab_body">
                                    <h3>Login Information</h3>
                                    <hr>
                                    <div class="login_info">
                                       <form method="post" action="actions/act_updatelogin.php">
                                          <div class="border-box border-top-none">
                                            @if($userDet)
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
                             <div id="menu4" class="tab-pane fade">
                              <div class="row">
                                 <div class="col-lg-8 col-md-7 col-sm-6" id="tab_body">
                                    <h3>Credit Card Information</h3>
                                    <hr>
                                    <form method="post" action="actions/act_updateaccount.php">
                                       @if($accountDet)
                                       <div class="row">
                                          <div class="col-lg-2 space-right">
                                             <label>Card Type</label>
                                          </div>
                                          <div class="col-lg-4">
                                             <select name="" class="form-control">
                                                <option value="">Visa</option>
                                             </select>
                                          </div>
                                          <div class="col-lg-2 space-right">
                                             <label>Card Number</label>
                                          </div>
                                          <div class="col-lg-4">
                                              
                                              <input class="form-control" name="cardnumber" value="" type="text"> 
                                              
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-lg-2 space-right">
                                             <label>Expiration Date</label>
                                          </div>
                                          <div class="col-lg-2 two-control">
                                             <input type="text" name="year" value="{{ $accountDet->expire_year }}" class="form-control">
                                          </div>
                                          <div class="col-lg-2 two-control">
                                             <input type="text" name="month" value="{{ $accountDet->expire_month }}" class="form-control">
                                          </div>
                                          <div class="col-lg-2 space-right">
                                             <label>Card CCV #</label>
                                          </div>
                                          <div class="col-lg-4">
                                             <input class="form-control" name="cvv" value="{{ $accountDet->CVC }}" type="text">
                                          </div>
                                       </div>

                                       <div class="row margint-30">
                                          <div class="col-lg-12">
                                             <button type="submit" class="btn btn-info btn-blue">Update</button>
                                          </div>
                                       </div>
                                       @else
                                       <div class="row">
                                          <div class="col-lg-2 space-right">
                                             <label>Card Type</label>
                                          </div>
                                          <div class="col-lg-4">
                                             <select name="" class="form-control">
                                                <option value="">Visa</option>
                                             </select>
                                          </div>
                                          <div class="col-lg-2 space-right">
                                             <label>Card Number</label>
                                          </div>
                                          <div class="col-lg-4">
                                             <input class="form-control" name="cardnumber" maxlength="16" value="" type="text">
                                          </div>
                                       </div>
                                       <div class="row">
                                          <div class="col-lg-2 space-right">
                                             <label>Expiration Date</label>
                                          </div>
                                          <div class="col-lg-2 two-control form-inline">
                                             <input type="text" name="year" value="" placeholder="yyyy" class="form-control">

                                             
                                          </div>
                                          <div class="col-lg-2 two-control form-inline">
                                              <input type="text" name="month" value="" placeholder="mm" class="form-control">
                                          </div>
                                          <div class="col-lg-2 space-right">
                                             <label>Card CCV #</label>
                                          </div>
                                          <div class="col-lg-4">
                                             <input class="form-control" name="cvv" value="" maxlength="3" type="text">
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
                           <div id="menu5" class="tab-pane fade">
                              <div class="row">
                                 <div class="col-lg-8 col-md-7 col-sm-6" id="tab_body">
                                    <h3>Credit Card Billing Information</h3>
                                    <hr>
                                    <div class="credit_info">
                                       <form method="post" action="actions/act_updatebilling.php">
                                    @if($billingDet)
                                    <div class="row" id="form_row">
                                      <div class="col-lg-2 space-right">
                                      <label>First Name</label>
                                      </div>
                                      <div class="col-lg-4">
                                      <input class="form-control" name="firstname" value="{{ $billingDet->firstname }}" type="text">
                                      </div>
                                    <div class="col-lg-2 space-right">
                                    <label>Last Name</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input class="form-control" name="lastname" value="{{ $billingDet->lastname }}" type="text">
                                    </div>
                                    </div>
                                    <div class="row" id="form_row">
                                    <div class="col-lg-2 space-right">
                                    <label>Address</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input class="form-control" name="address" value="{{ $billingDet->address }}" type="text">
                                    </div>
                                    <div class="col-lg-2 space-right">
                                    <label>City</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input class="form-control" name="city" value="{{ $billingDet->city }}" type="text">
                                    </div>
                                    </div>
                                    <div class="row" id="form_row">
                                    <div class="col-lg-2 space-right">
                                    <label>State</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input type="text" name="state" class="form-control" value="{{ $billingDet->state }}">
                                    </div>
                                    <div class="col-lg-2 space-right">
                                    <label>Zip Code</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input class="form-control" name="zip" value="{{ $billingDet->zip }}" type="text">
                                    </div>
                                    </div>
                                    <div class="row" id="form_row">
                                    <div class="col-lg-2 space-right">
                                    <label>Phone</label>
                                    </div>
                                    <div class="col-lg-4">
                                    <input type="text" name="phone" class="form-control" value="{{ $billingDet->phone }}">
                                    </div>

                                    </div>
                                    <div class="row margint-30" >
                                    <di