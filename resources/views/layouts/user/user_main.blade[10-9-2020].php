<!DOCTYPE html>
<html>
<head>
      <title>Global Voice Direct</title>
      <!--[if lt IE 10]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
      <![endif]-->
      <meta charset="utf-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
      <meta name="_token" content="{{csrf_token()}}">
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="." />
      <meta name="keywords" content="">
      <meta name="author" content="Phoenixcoded" />
      <link rel="icon" href="{{asset('images/favicon/favicon.ico')}}" type="image/x-icon">
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="{{asset('user/bower_components/bootstrap/css/bootstrap.min.css')}}">
      <link rel="stylesheet" href="{{asset('user/assets/pages/waves/css/waves.min.css')}}" type="text/css')}}" media="all">
      <link rel="stylesheet" type="text/css" href="{{asset('user/assets/icon/feather/css/feather.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('user/assets/css/font-awesome-n.min.css')}}">
      <link rel="stylesheet" href="{{asset('user/assets/pages/chart/radial/css/radial.css')}}" type="text/css" media="all">
      <link rel="stylesheet" type="text/css" href="{{asset('user/assets/css/style.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('user/assets/css/widget.css')}}">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
     
      <link rel="stylesheet" type="text/css" href="{{asset('user/bower_components/jquery.steps/css/jquery.steps.css')}}">
      <link rel="stylesheet" type="text/css" href="{{asset('user/assets/css/pages.css')}}">
      <link rel="stylesheet" href="https://davidstutz.de/bootstrap-multiselect/dist/css/bootstrap-multiselect.css" />

      <link rel="stylesheet" href="{{asset('user/css/imgareaselect.css')}}">
      


      <style type="text/css">
         .slimScrollDiv, .slimScrollDiv .style-cont.m-t-10  {
            height: 100vh !important;
         }
         /*.pcoded .pcoded-navbar .nav-list {
            height: calc(100vh - 59px) !important;
         }*/
         span.other-items {
             position: unset !important;
         }
         i.feather.icon-edit {
             color: cornsilk;
         }
         a.edit-user-profile-photo {
             position: absolute;
             top: 23px;
             left: 33px;
         }
         .user-profile-photo {
             display: inline-block;
         }
          
         a.edit-user-profile-photo {
             display: none;
         }
         @media (min-width: 576px){
            .modal-dialog {
               max-width: 560px !important;
            }
         }
         /*.style-cont.m-t-10 {
             height: 100vh !important;
         }*/
      </style>
      @yield('css')
   </head>
  <body>
      <div id="pcoded" class="pcoded">
         <div class="pcoded-overlay-box"></div>
         <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
               <div class="navbar-wrapper">
                  <div class="navbar-logo">
                     <a href="/">
                     <img class="img-fluid" src="{{asset('user/images/logo.png')}}" alt="Global Voice Direct Logo" />
                     </a>
                     <a class="mobile-menu" id="mobile-collapse" href="#!">
                     <i class="feather icon-menu"></i>
                     </a>
                     <a class="mobile-options waves-effect waves-light">
                     <i class="feather icon-more-horizontal"></i>
                     </a>
                  </div>
                  <div class="navbar-container container-fluid">
                     <ul class="nav-left">
                        <li>
                           <a href="#!" onclick="javascript:toggleFullScreen()" class="waves-effect waves-light">
                           <i class="full-screen feather icon-maximize"></i>
                           </a>
                        </li>
                     </ul>
                     <ul class="nav-right">
                        <li class="header-notification">
                           <div class="dropdown-primary dropdown">
                              <div class="dropdown-toggle" data-toggle="dropdown">
                                 <i class="feather icon-bell"></i>
                                 <span class="badge bg-c-red">5</span>
                              </div>
                              <ul class="show-notification notification-view dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                 <li>
                                    <h6>Notifications</h6>
                                    <label class="label label-danger">New</label>
                                 </li>
                              </ul>
                           </div>
                        </li>
                        <li class="header-notification">
                           <div class="dropdown-primary dropdown">
                              <div class="displayChatbox dropdown-toggle" data-toggle="dropdown">
                                 <i class="feather icon-message-square"></i>
                                 <span class="badge bg-c-green">3</span>
                              </div>
                           </div>
                        </li>
                        <li class="user-profile header-notification">
                           <div class="dropdown-primary dropdown">
                              <div class="dropdown-toggle" data-toggle="dropdown">
                                 <i class="icon feather icon-settings"></i>
                              </div>
                              <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                 <li class="drp-u-details">
                                    <div class="user-profile-photo">
                                       
                                        <img src="{{asset('user/profileImages/default.png')}}" id="profile_picture" class="img-radius" alt="User-Profile-Image" style="max-height: 100px; max-width: 100px;">
                                       <a class="edit-user-profile-photo" href="javascript:;" title="Update Your Profile Image" data-toggle="modal" data-target="#profile_pic_modal"><i class="feather icon-edit" aria-hidden="true"></i></a>
                                    </div>
                                    <span>{{session('nice_name')}}</span>
                                    <a href="javascript:;" class="dud-logout" title="Logout">
                                    </a>
                                 </li>
                                 <li>
                                    <a href="{{ url('user/logout')}}">
                                    <i class="feather icon-log-out"></i> Log Out
                                    </a>
                                 </li>
                                   <!-- <li>
                                    <a href="#">
                                       <img class="img-fluid" src="images/support.png" alt="support" />
                                    </a>
                                 </li>
                                 <li>
                                    <a href="user-profile.html">
                                    <i class="feather icon-user"></i> Profile
                                    </a>
                                 </li>
                                 <li>
                                    <a href="email-inbox.html">
                                    <i class="feather icon-mail"></i> My Messages
                                    </a>
                                 </li>
                                 <li>
                                    <a href="auth-lock-screen.html">
                                    <i class="feather icon-lock"></i> Lock Screen
                                    </a>
                                 </li> -->
                              </ul>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </nav>
            <div class="pcoded-main-container">
               <div class="pcoded-wrapper">
                  <nav class="pcoded-navbar">
                     <div class="nav-list">
                        <div class="pcoded-inner-navbar main-menu" style="overflow: auto !important;">
                           <div class="pcoded-navigation-label">Navigation</div>
                           <ul class="pcoded-item pcoded-left-item">
                              <li class="  ">
                                 <a href="/" class="waves-effect waves-dark">
                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
                                    <span class="pcoded-mtext">Home</span>
                                 </a>
                                 
                              </li>
                                 <li>
                                 <a href="/user/profile" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                 <span class="pcoded-mtext">Profile</span>
                                 </a>
                              </li>
                              <li class="{{Request::path() =='user/order' ? 'active' : '' }}">
                                 <a href="/user/order" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Orders</span>
                                 </a>
                              </li>
                              <li>
                                 <a href="/user/fax_mod" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Fax</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Billing and Payments</div>

                              <li class="{{ Request::path()=='payment' ? 'active' : ''}}">
                                 <a href="/payment" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
                                 <span class="pcoded-mtext">Payments</span>
                                 <!-- <span class="pcoded-badge label label-info">100+</span> -->
                                 </a>
                              </li>
                           </ul>
                           <ul class="pcoded-item pcoded-left-item">
                             <!--  <li class="">
                                 <a href="invoices.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-file-minus"></i>
                                 </span>
                                 <span class="pcoded-mtext">Invoices</span>
                                 </a>
                              </li>
                              <li class="">
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-gitlab"></i>
                                 </span>
                                 <span class="pcoded-mtext">Statements</span>
                                 </a>
                                 
                              </li> -->
                              <li class="{{ Request::path()=='user/billPay' ? 'active' : ''}}">
                                 <a href="/user/billPay" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-package"></i>
                                    </span>
                                    <span class="pcoded-mtext">Bill Pay</span>
                                 </a>
                                 
                              </li>
                              <li class=" ">
                                 <a href="/connect/beta/" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-aperture rotate-refresh"></i>
                                 </span>
                                 <span class="pcoded-mtext">Phone Plans</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Forms</div>
                              <li class="{{ Request::path()=='user/credit_app' ? 'active' : ''}} ">
                                 <a href="/user/credit_app" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-cpu"></i>
                                 </span>
                                 <span class="pcoded-mtext">Credit Application</span>
                                 <!-- <span class="pcoded-badge label label-danger">HOT</span> -->
                                 </a>
                              </li>
                             <li class="{{ Request::path()=='marketing-form' ? 'active' : ''}} ">
                                 <a href="/marketing-form" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Marketing Form</span>
                                 </a>
                              </li>
                             <li class="{{ Request::path()=='user/script_recording' ? 'active' : ''}} ">
                                 <a href="/user/script_recording" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Script Recording</span>
                                 </a>
                              </li>
                             <li class="{{ Request::path()=='addPort' ? 'active' : ''}} ">
                                 <a href="/addPort" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Add Port</span>
                                 </a>
                              </li>
                             <li class="{{ Request::path()=='listingbusinessname' ? 'active' : ''}} ">
                                 <a href="/listingbusinessname" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Buisness Listings</span>
                                 </a>
                              </li>
                           </ul>
                           <div class="pcoded-navigation-label">Campaign</div>
                           <ul class="pcoded-item pcoded-left-item">
                              <li class="{{ Request::path()=='smscampaign' ? 'active' : ''}} ">
                                 <a href="/smscampaign" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-clipboard"></i>
                                 </span>
                                 <span class="pcoded-mtext">SMS Campaign</span>
                                 </a>
                                 
                              </li>
                              <li class="pcoded-hasmenu {{ Request::path()=='emailCampaign' ? 'active' : Request::path()=='send_email' ? 'active' : Request::path()=='ot_editor_sendEmail' ? 'active' : ''}}" >
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-mail"></i>
                                 </span>
                                 <span class="pcoded-mtext">Email Campaign</span>
                                 </a>
                                  <ul class="pcoded-submenu">
                                    <li class="{{ Request::path()=='emailCampaign' ? 'active' : ''}} ">
                                       <a href="/emailCampaign" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Manage Subscribers</span>
                                       </a>
                                    </li>
                                    <li class="{{ Request::path()=='send_email' ? 'active' : ''}} ">
                                       <a href="/send_email" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Send a Campaign</span>
                                       </a>
                                    </li>
                                    <li class="{{ Request::path()=='ot_editor_sendEmail' ? 'active' : ''}} ">
                                       <a href="/ot_editor_sendEmail" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Office Type Editor Email Campaign</span>
                                       </a>
                                    </li>
                                 </ul>
                              </li>

                              <li class="pcoded-hasmenu {{ Request::path()=='audiosubscriber' ?  'active' : Request::path()=='user/audioCampaign' ? 'active' : Request::path()== 'leter_calls_campaign' ? 'active' : Request::path()=='calls_history' ? 'active' : Request::path()=='active_calls' ? 'active' : ''}}">
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-mail"></i>
                                 </span>
                                 <span class="pcoded-mtext">Audio Campaign</span>
                                 </a>
                                  <ul class="pcoded-submenu">
                                   <li class=" ">
                                       <a href="/audiosubscriber" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Manage Subscribers</span>
                                       </a>
                                    </li>
                                     
                                    <li class="{{ Request::path()=='user/audioCampaign' ? 'active' : ''}} ">
                                       <a href="/user/audioCampaign" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Audio Campaign</span>
                                       </a>
                                    </li>
                                    <li class="{{ Request::path()=='leter_calls_campaign' ? 'active' : ''}} ">
                                       <a href="/later_calls_campaign" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Schedule Audio Campaign</span>
                                       </a>
                                    </li>
                                    <li class="{{ Request::path()=='active_calls' ? 'active' : ''}} ">
                                       <a href="/active_calls" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">View Active Calls</span>
                                       </a>
                                    </li>
                                    <li class="{{ Request::path()=='calls_history' ? 'active' : ''}} ">
                                       <a href="/calls_history" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">View Call History</span>
                                       </a>
                                    </li>
                                     
                                 </ul>
                              </li>
                           </ul>
                              <div class="pcoded-navigation-label">Others</div>
                               <ul class="pcoded-item pcoded-left-item">
                                 <li class="{{ Request::path()=='user/howtovideo' ? 'active' : ''}} ">
                                    <a href="/user/howtovideo" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-feather"></i>
                                    </span>
                                    <span class="pcoded-mtext">How to Videos</span>
                                    </a>
                                 </li>
                                 <li class="{{ Request::path()=='user/howtovideo' ? 'active' : ''}} ">
                                    <a href="/user/referral_program" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="fas fa-users"></i>
                                    </span>
                                    <span class="pcoded-mtext">Refer a Friend</span>
                                    </a>
                                 </li>
                                 <li class="">
                                    <a href="{{ asset('script-samples/Automated%20Attendant.pdf') }}" target="_blank" class="waves-effect waves-dark">
                                       <span class="pcoded-micon">
                                          <i class="feather icon-feather"></i>
                                       </span>
                                       <span class="pcoded-mtext other-items">Download Phone Script</span>
                                    </a>
                                 </li>
                                 <li class="">
                                    <a href="{{ asset('script-samples/Automated%20Attendant.pdf') }}" target="_blank" class="waves-effect waves-dark">
                                       <span class="pcoded-micon">
                                          <i class="feather icon-feather"></i>
                                       </span>
                                       <span class="pcoded-mtext other-items">Download Phone Script</span>
                                    </a>
                                 </li>

                              </ul>
                              
                           </ul>
                            
                        </div>
                     </div>
                  </nav>
                  <div class="pcoded-content">
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
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="pcoded-inner-content">
                        <div class="main-body">
                           <div class="page-wrapper">
                              <div class="page-body">
                                 @yield('content')
                              </div>

                           </div>
                        </div>
                     </div>
                       <footer >
                        <div class="pcoded pcoded-footer-bottom">
                           <p >© <? echo date('Y'); ?> GlobalVoiceDirect.com, All Rights Reserved.</p>
                        </div>
                       
                        </footer>
                  </div>

               </div>

               <div id="styleSelector"></div>
            </div>

         </div>

      </div>

      <div id="profile_pic_modal" class="modal fade">
         <div class="modal-dialog">
            <div class="modal-content">
               <div class="modal-header">
                  <h3>Change Profile Picture</h3>
                  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                  
               </div>
               <div class="modal-body">
                  <form id="cropimage" method="post" enctype="multipart/form-data" action="change_pic.php">
                     <strong>Upload Image:</strong> <br><br>
                     <input type="file" name="profile-pic" id="profile-pic" />
                     <input type="hidden" name="hdn-profile-id" id="hdn-profile-id" value="" />
                     <input type="hidden" name="hdn-x1-axis" id="hdn-x1-axis" value="" />
                     <input type="hidden" name="hdn-y1-axis" id="hdn-y1-axis" value="" />
                     <input type="hidden" name="hdn-x2-axis" value="" id="hdn-x2-axis" />
                     <input type="hidden" name="hdn-y2-axis" value="" id="hdn-y2-axis" />
                     <input type="hidden" name="hdn-thumb-width" id="hdn-thumb-width" value="" />
                     <input type="hidden" name="hdn-thumb-height" id="hdn-thumb-height" value="" />
                     <input type="hidden" name="action" value="" id="action" />
                     <input type="hidden" name="image_name" value="" id="image_name" />
                     
                     <div id='preview-profile-pic'></div>
                  <div id="thumbs" style="padding:5px; width:600px"></div>
                  </form>
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                  <button type="button" id="save_crop" class="btn btn-primary">Crop & Save</button>
               </div>
            </div>
         </div>
      </div>   

     <script type="text/javascript" src="{{ asset('user/bower_components/jquery/js/jquery.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/assets/js/pace.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/bower_components/jquery-ui/js/jquery-ui.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/bower_components/popper.js/js/popper.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/bower_components/bootstrap/js/bootstrap.min.js')}}"></script>
      <script src="{{ asset('user/assets/pages/waves/js/waves.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/bower_components/jquery-slimscroll/js/jquery.slimscroll.js')}}"></script>
      <script src="{{ asset('user/assets/pages/widget/amchart/amcharts.js')}}"></script>
      <script src="{{ asset('user/assets/pages/widget/amchart/serial.js')}}"></script>
      <script src="{{ asset('user/assets/pages/widget/amchart/light.js')}}"></script>
      <script src="{{ asset('user/bower_components/chartist/js/chartist.js')}}"></script>
      <script src="{{ asset('user/assets/js/pcoded.min.js')}}"></script>
      <script src="{{ asset('user/assets/js/vertical/vertical-layout.min.js')}}"></script>
      <script src="{{ asset('user/bower_components/jquery.steps/js/jquery.steps.js')}}"></script>
       <script type="text/javascript" src="{{ asset('user/assets/pages/dashboard/custom-dashboard.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/assets/js/script.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/ckeditor/ckeditor.js')}}"></script>
      <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
      <script src="{{ asset('user/js/jquery.imgareaselect.js')}}" type="text/javascript"></script>
      <script src="{{ asset('user/js/jquery.form.js')}}"></script>
      <script src="https://davidstutz.de/bootstrap-multiselect/dist/js/bootstrap-multiselect.js"></script>

      <script src="{{ asset('user/js/functions.js')}}"></script>
      @yield('scripts')
      @stack('scripts')
   </body>
</html>