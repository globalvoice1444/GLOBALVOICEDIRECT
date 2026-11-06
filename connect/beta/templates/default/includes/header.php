<?php
   $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
?>
<!DOCTYPE html>
<html lang="en">
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
      <link rel="icon" href="files/assets/images/favicon.ico" type="image/x-icon">
      <script type="text/javascript" src="files/bower_components/jquery/js/jquery.min.js"></script>
      <script type="text/javascript" src="files/assets/js/pace.min.js"></script>
      <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
      <link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">
      <link rel="stylesheet" type="text/css" href="files/bower_components/bootstrap/css/bootstrap.min.css">
      <link rel="stylesheet" href="files/assets/pages/waves/css/waves.min.css" type="text/css" media="all">
      <link rel="stylesheet" type="text/css" href="files/assets/icon/feather/css/feather.css">
      <link rel="stylesheet" type="text/css" href="files/assets/css/font-awesome-n.min.css">
      <link rel="stylesheet" href="files/assets/pages/chart/radial/css/radial.css" type="text/css" media="all">
      <link rel="stylesheet" type="text/css" href="files/assets/css/style.css">
      <link rel="stylesheet" type="text/css" href="files/assets/css/widget.css">
      <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">
      <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
      <link rel="stylesheet" type="text/css" href="files/bower_components/jquery.steps/css/jquery.steps.css">
      <link rel="stylesheet" type="text/css" href="files/assets/css/pages.css">


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
         /*.style-cont.m-t-10 {
             height: 100vh !important;
         }*/
         input#next-button{
            margin-left: 0px!important;
         }
      </style>
   </head>
   <body>
      <div id="pcoded" class="pcoded">
         <div class="pcoded-overlay-box"></div>
         <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
               <div class="navbar-wrapper">
                  <div class="navbar-logo">
                     <a href="index.html">
                     <img class="img-fluid" src="images/logo.png" alt="Global Voice Direct Logo" />
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
                                    <img src="files/assets/images/avatar-4.jpg" class="img-radius" alt="User-Profile-Image">
                                    <span>Full Name</span>
                                    <a href="auth-sign-in-social.html" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out"></i>
                                    </a>
                                 </li>
                                 <li>
                                    <a href="#!">
                                    <i class="feather icon-settings"></i> Settings
                                    </a>
                                 </li>
                                   <li>
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
                                 </li>
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
                              <?php if(strpos($actual_link, 'profile')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="profile.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                 <span class="pcoded-mtext">Profile</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'orders')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="orders.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Orders</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'fax_mod')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="fax_mod.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Fax</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Billing and Payments</div>

                              <?php if(strpos($actual_link, 'payments')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
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
                              <?php if(strpos($actual_link, 'BillPay')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="BillPay.php" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-package"></i>
                                    </span>
                                    <span class="pcoded-mtext">Bill Pay</span>
                                 </a>
                                 
                              </li>
                              <li class=" ">
                                 <a href="animation.html" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-aperture rotate-refresh"></i>
                                 </span>
                                 <span class="pcoded-mtext">Phone Plans</span>
                                 </a>
                              </li>
                              <div class="pcoded-navigation-label">Forms</div>
                              <?php if(strpos($actual_link, 'credit_app')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="credit_app.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-cpu"></i>
                                 </span>
                                 <span class="pcoded-mtext">Credit Application</span>
                                 <!-- <span class="pcoded-badge label label-danger">HOT</span> -->
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'marketing-form')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="marketing-form.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Marketing Form</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'script_recording')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="script_recording.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Script Recording</span>
                                 </a>
                              </li>
                           </ul>
                           <div class="pcoded-navigation-label">Campaign</div>
                           <ul class="pcoded-item pcoded-left-item">
                              <?php if(strpos($actual_link, 'smscampaign')){ ?>
                                 <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="smscampaign.php" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-clipboard"></i>
                                 </span>
                                 <span class="pcoded-mtext">SMS Campaign</span>
                                 </a>
                                 
                              </li>
                              <?php if(strpos($actual_link, 'emailCampaign')){ ?>
                                 <li class="active pcoded-hasmenu pcoded-trigger" dropdown-icon="style1" subitem-icon="style5">
                              <?php }else{ ?>
                                 <li class="pcoded-hasmenu">
                              <?php } ?>
                                 <a href="javascript:void(0)" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-mail"></i>
                                 </span>
                                 <span class="pcoded-mtext">Email Campaign</span>
                                 </a>
                                  <ul class="pcoded-submenu">
                                    <?php if(strpos($actual_link, 'emailCampaign')){ ?>
                                       <li class="active">
                                    <?php }else{ ?>
                                          <li class="">
                                    <?php } ?>
                                       <a href="emailCampaign.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Email Campaign</span>
                                       </a>
                                    </li>
                                    <!-- <li class="">
                                       <a href="javascript:void(0)" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Contact Management</span>
                                       </a>
                                    </li> -->
                                    <?php if(strpos($actual_link, 'send_email')){ ?>
                                       <li class="active">
                                    <?php }else{ ?>
                                          <li class="">
                                    <?php } ?>
                                       <a href="send_email.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Send Email</span>
                                       </a>
                                    </li>
                                 </ul>
                              </li>
                           </ul>
                              <div class="pcoded-navigation-label">Others</div>
                               <ul class="pcoded-item pcoded-left-item">
                                 <li class="">
                                    <a href="termofservice.php" class="waves-effect waves-dark">
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
                                       <a href="/">
                                       <i class="feather icon-home"></i>
                                       </a>
                                    </li>
                                    <li class="breadcrumb-item">Phone Plans</li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                     <div class="pcoded-inner-content">
                        <div class="main-body">
                           <div class="page-wrapper">
                              <div class="page-body">