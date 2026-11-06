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
      <meta http-equiv="X-UA-Compatible" content="IE=edge" />
      <meta name="description" content="." />
      <meta name="keywords" content="">
      <meta name="author" content="Phoenixcoded" />
      <link rel="icon" href="{{asset('user/assets/images/favicon.ico')}}" type="image/x-icon">
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
                                    <a href="actions/logout.php">
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
                                 <a href="profile.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                 <span class="pcoded-mtext">Profile</span>
                                 </a>
                              </li>
                              <li>
                                 <a href="/user/order" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Orders</span>
                                 </a>
                              </li>
                              <li>
                                 <a href="fax_mod.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Fax</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Billing and Payments</div>

                              <li>
                                 <a href="payments.php" class="waves-effect waves-dark">
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
                              <li>
                                 <a href="BillPay.php" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-package"></i>
                                    </span>
                                    <span class="pcoded-mtext">Bill Pay</span>
                                 </a>
                                 
                              </li>
                              <li class=" ">
                                 <a href="/users/connect/beta/" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-aperture rotate-refresh"></i>
                                 </span>
                                 <span class="pcoded-mtext">Phone Plans</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Forms</div>
                              <li>
                                 <a href="credit_app.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-cpu"></i>
                                 </span>
                                 <span class="pcoded-mtext">Credit Application</span>
                                 <!-- <span class="pcoded-badge label label-danger">HOT</span> -->
                                 </a>
                              </li>
                             <li>
                                 <a href="marketing-form.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Marketing Form</span>
                                 </a>
                              </li>
                             <li>
                                 <a href="script_recording.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Script Recording</span>
                                 </a>
                              </li>
                             <li>
                                 <a href="addport.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Add Port</span>
                                 </a>
                              </li>
                             <li>
                                 <a href="listingbusinessname.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Buisness Listings</span>
                                 </a>
                              </li>
                           </ul>
                           <div class="pcoded-navigation-label">Campaign</div>
                           <ul class="pcoded-item pcoded-left-item">
                              <li>
                                 <a href="smscampaign.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-clipboard"></i>
                                 </span>
                                 <span class="pcoded-mtext">SMS Campaign</span>
                                 </a>
                                 
                              </li>
                              <li class="pcoded-hasmenu">
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-mail"></i>
                                 </span>
                                 <span class="pcoded-mtext">Email Campaign</span>
                                 </a>
                                  <ul class="pcoded-submenu">
                                    <li>
                                       <a href="emailCampaign.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Manage Subscribers</span>
                                       </a>
                                    </li>
                                    <li>
                                       <a href="send_email.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Send a Campaign</span>
                                       </a>
                                    </li>
                                    <li>
                                       <a href="ot_editor_sendEmail.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Office Type Editor Email Campaign</span>
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                              <li class="pcoded-hasmenu">
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-mail"></i>
                                 </span>
                                 <span class="pcoded-mtext">Audio Campaign</span>
                                 </a>
                                  <ul class="pcoded-submenu">
                                   <li>
                                       <a href="audiosubscriber.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Manage Subscribers</span>
                                       </a>
                                    </li>
                                     
                                    <li>
                                       <a href="index.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Audio Campaign</span>
                                       </a>
                                    </li>
                                    <li>
                                       <a href="later_calls_campaign.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Schedule Audio Campaign</span>
                                       </a>
                                    </li>
                                    <li>
                                       <a href="active_calls.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">View Active Calls</span>
                                       </a>
                                    </li>
                                    <li>
                                       <a href="calls_history.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">View Call History</span>
                                       </a>
                                    </li>
                                     
                                 </ul>
                              </li>
                           </ul>
                              <div class="pcoded-navigation-label">Others</div>
                               <ul class="pcoded-item pcoded-left-item">
                                  <li>
                                    <a href="howtovideo.php" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-feather"></i>
                                    </span>
                                    <span class="pcoded-mtext">How to Videos</span>
                                    </a>
                                 </li>
                                 <li class="">
                                    <a href="https://globalvoicedirect.com/script-samples/Automated%20Attendant.pdf" target="_blank" class="waves-effect waves-dark">
                                       <span class="pcoded-micon">
                                          <i class="feather icon-feather"></i>
                                       </span>
                                       <span class="pcoded-mtext other-items">Download Phone Script</span>
                                    </a>
                                 </li>
                                 <li class="">
                                    <a href="https://globalvoicedirect.com/script-samples/Automated%20Attendant.pdf" target="_blank" class="waves-effect waves-dark">
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
                                       <a href="/../index.php">
                                          <i class="feather icon-home"></i>
                                       </a>
             