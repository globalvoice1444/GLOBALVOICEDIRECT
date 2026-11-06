<?php
  
defined('SIGNUP') or die('Restricted access');
?>
<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
   <title><?php echo $text[strtoupper($layout)]; ?></title>

   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta charset="utf-8">

   <link rel="icon" href="/public/images/favicon/favicon.ico" type="image/x-icon">
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
      div#address-info {
         display: block;
      }
       .form-control {
           border-radius: 6px !important;
           border-style: none !important;
           padding-top: 10px !important;
           padding-bottom: 10px !important;
           border: 1px solid #4fc3f7 !important;
           height: auto !important;
       }
       .category-heading {
           color: #134576 !important;
           font-size: 18px;
           font-weight: 600;
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
           margin-top: 30px;
       }
       .top-title.border-bootm {
           margin-top: -21px;
       }
       h3.table-heading {
           text-align: center;
           background-color: #134576;
           padding-top: 20px;
           padding-bottom: 20px;
           color: #fff;
           margin-bottom: 0px;
           text-transform: uppercase;
           border-radius: 6px 6px 0 0;
           font-weight: 600;
       }
       .form-inline-form {
           padding: 40px 40px 40px 40px;
           border-radius: 0 0 6px 6px;
       }
       label.col-lg-2.control-label {
           text-align: left;
       }
      .form-control{
         width: 100% !important;
      }
      #mandatory-fields {
          padding: 20px 50px 45px;
          border: 1px solid #134576;
      }
      .submit-button-wrapper {
          margin-top: -50px;
      }
      #address-heading {
          padding: 0px;
      }
      .form-inline label{
         display: -webkit-inline-box;
         -webkit-box-pack: unset;
      }
      input#next-button {
          margin-left: 9%;
          padding: 10px 25px;
          font-size: 15px;
      }
      .nav-tabs.nav-justified {
        justify-content: center;
      }
      @media (max-width: 667px){
         input#next-button {
             margin-left: 71.3% !important;
         }
      }
       @media (max-width: 1024px){
         input#next-button {
            margin-left: 77.3% !important;
         }
       }
       @media (min-width: 992px){
          .col-md-3 {
              width: 30% !important;
              margin-right: 15px;
          }
        }
       @media (min-width: 576px){
        .col-sm-3 {
             
          max-width: 100%; 
        }
      }
      span.btn.btn-link.btn-info {
          padding: 10px 25px;
          font-size: 14px;
      }
      .radio-0, .radio-1, .radio-2, .radio-3{
         height: 90px;
         background-size: 100% 90px;
         background-repeat: no-repeat;
         justify-content: center;
         border-radius: 6px;
         text-align: center;
         display: flex;
         color: #fff;
         padding-top: 15px !important;
      }
      .radio-0{
          background-image: url(/public/users/images/5.png);
         
       }
       .radio-1 {
          background-image: url(/public/users/images/6.png);
       }
       .radio-2 {
          background-image: url(/public/users/images/7.png);
       }
       .radio-3 {
         background-image: url(/public/users/images/8.png);
       }
         .bg-white{
            min-height: 560px;
         }
       .hidden-xs.progress.progress-striped {
            margin-left: 3.5%;
            width: 57.1%;
         }
         .pcoded .pcoded-navbar .pcoded-item>li>a>.pcoded-micon {
             font-size: 1.6rem;
         }
         .icon-user {
            background-position: 0 !important;
         }
        iframe {
            padding: 15px 0 6px 0;
        }
        .left-text h4 {
            text-align: center;
            margin-top: 20%;
            color: #134576;
            font-size: 40px;
            font-weight: 800;
            font-family: 'montserrat';
        }
        .row.top-video-section {
            margin-bottom: 10px;
        }
        .top-wizard {
          margin: auto;
        }
        .adress-info-label {
            margin-left: 12.5%;
        }
        .adress-info-label h3 {
            margin-top: 5px;
        }
        div#show-optional {
            margin-left: 40%;
        }
        div#hide-optional {
            margin-left: 37%;
            margin-bottom: 20px;
        }
        .form-horizontal .form-group:first-child {
            margin-right: -15px;
            justify-content: center;
            padding-top: 27px;
        }
      #country, #state, #i_time_zone, #did-api-countries, #did-api-states, #did-api-ratecenters,#did-api-numbers {
          height: calc(2.25rem + 11px);
          width: 100%;
      }
      .col-md-12.package_div {
          padding: 0 7.7%;
      }
      .nav-tabs.nav-justified>li{
         width: 45% !important;
      }
      .nav-tabs.nav-justified>li:first-child {
         margin-right: 30px;
      }
      /*div#progress-bar {
          width: 50% !important;
      }*/
      li.first.active a {
         background-color: rgb(43, 57, 82) !important;
         color: #fff !important;
         height: auto !important;
         padding: 8.5%;
         font-size: 23px;
      }
      button#menu1 {
          margin-left: 87%;
          padding: 8px 18px;
      }
      div#submit-button {
          float: right;
      }
      .last>.cursor-default {
          height: auto !important;
          padding: 8.5% 0;
          background-color: rgb(79, 195, 247) !important;
          color: #fff !important;
          font-size: 23px;
      }
      .last>.cursor-default:hover,  a.cursor-default:hover {
         background-color: rgb(43,57,82,0.5) !important;
         color: #fff !important;
      }
      #address-info {
        padding: 
      }
      .last.active>.cursor-default {
          background-color: rgb(63, 77, 103)  !important;
          color: #fff !important;
      }
      #account-options .form-group.form-inline {
          justify-content: center;
      }
      .nav-tabs.nav-justified {
          margin: 10px 0px 0px;
      }
      .last.active>.cursor-default:hover{
         background-color: rgb(43,57,82) !important;
      }
      li:not(.active)>.cursor-default.text-muted {
          padding: 8.7%;
          font-size: 23px;
          background-color: rgb(79, 195, 247) !important;
          color: #fff!important;
      }
      li:not(.active)>.cursor-default.text-muted:hover {
          color: #3a8baf !important;
          background-color: rgba(43,57,82,0.5) !important;
      }
      li:not(.active)>.cursor-default:hover{
         color: #fff !important;
          background-color: rgb(43, 57, 82) !important;
      }
      .page-header{
         padding: 0px !important;
         border-bottom: 0 !important;
         margin:0 !important;
      }
      .pcoded-inner-content .main-body {
          margin-top: -100px;
      }
       .first, .last{
         height: auto !important;
      }
      i#accountinfo_tab_icon, i#accountoption_tab_icon {
          display: block;
          font-size: 50px;
      }
      .hidden-xs.progress.progress-striped {
          display: none;
      }
      .col-md-11.top-wizard {
          margin-bottom: 20px;
      }
      .form-group.form-inline {
          align-items: baseline !important;
      }
      .col-md-3.radio-2:last-child {
          margin-right: 0px !important;
      }
      @media (min-width: 768px) and (max-width: 1024px){
         .hidden-xs.progress.progress-striped {
             margin-left: 21%;
         }

      }
       @media (min-width: 768px){
        .form-horizontal .control-label {
      
            text-align: unset !important;
        }
        .nav-tabs.nav-justified>li{
            width: 45% !important;
        }
        .nav-tabs.nav-justified>li:first-child {
            margin-right: 29px !important;
        }
        .radio-0, .radio-1, .radio-2, .radio-3 {
              margin-bottom: 15px;
              padding-top: 10px;
        } 
        .col-sm-3 {
            width: 30% !important;
            margin-right: 15px !important;
        }
      }
      @media (max-width: 767px) {
        .nav-tabs.nav-justified>li:first-child {
          margin-right: 0px;
        }
        #show-optional, #hide-optional {
          margin-left: 0px !important;
        }
        #submit-button {
            float: unset !important;
        }
      }
      @media (max-width: 667px){

         #script_block {
             padding: 15px;
         }
         #mandatory-fields {
            padding: 5px 10% 24%;
          }
         .last.active>.cursor-default, .first>a {
             padding: 8.7% 0;
             font-size: 18px;
         }
         .last>.cursor-default, .first>a {
             font-size: 18px !important;
         }
         li.first.active a {
             padding: 8.7% 0px !important;
             font-size: 17px;
         }
         .nav-tabs.nav-justified>li {
            width: 45% !important;
        }
        .radio-0, .radio-1, .radio-2, .radio-3 {
              margin-bottom: 15px;
              padding-top: 10px;
        } 
        .col-sm-3 {
            width: 30% !important;
        }

      }
      
      @media (max-width: 480px){
           button#menu1 {
            margin-left: 63% !important;
          } 
          .nav-tabs.nav-justified>li {
              width: 100% !important;  
              margin-right: unset;       
          }  
          .radio-0, .radio-1, .radio-2, .radio-3 {
              margin-bottom: 15px;
          }  
          .col-sm-3 {
              width: 100% !important;
          }
          .left-text h4 {
              font-size: 27px;
              margin-top: 0;
          }
      }
      @media (max-width: 320px){
           button#menu1 {
            margin-left: 53% !important;
        }
        .left-text h4 {
            font-size: 15px;
            margin-top: 0;
        }
        .nav-tabs.nav-justified>li {
              width: 100% !important; 
              margin-right: unset;        
          }
          .radio-0, .radio-1, .radio-2, .radio-3 {
              margin-bottom: 15px;
          }
          .col-sm-3 {
              width: 100% !important;
          } 
      }   
   </style>
   <link href="/public/css/bootstrap.min.css" rel="stylesheet" media="screen">
   <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
   <!-- <link href="/public/css/font-awesome.min.css" rel="stylesheet" media="screen"> -->
   <!-- <link href="/public/css/style.css" rel="stylesheet" media="screen"> -->

   <link href="<?php echo $path; ?>css/bootstrap-switch.css" rel="stylesheet" media="screen">
   <!-- <link href="<?php echo $path; ?>css/bootstrap-additions.css" rel="stylesheet"> -->
   <link href="<?php echo $path; ?>css/theme.css" rel="stylesheet" media="screen">

   <script src="<?php echo $path; ?>js/jquery-1.11.0.min.js"></script>
   <script src="<?php echo $path; ?>js/bootstrap.min.js"></script>
   <script src="<?php echo $path; ?>js/bootstrap-switch.min.js"></script>
   <script>
      var JS_MAND_CHECKBOX = '<?php echo $text['JS_MAND_CHECKBOX']; ?>',
      JS_ENTER_VALID_EMAIL = '<?php echo $text['JS_ENTER_VALID_EMAIL']; ?>',
      JS_SELECT_ITEM = '<?php echo $text['JS_SELECT_ITEM']; ?>',
      JS_INVALID_CHARACTER = '<?php echo $text['JS_INVALID_CHARACTER']; ?>',
      JS_MAND_FIELD = '<?php echo $text['JS_MAND_FIELD']; ?>',
      JS_NOT_SET = '<?php echo $text['NOT_SET']; ?>',
      lang = '<?php echo $lang; ?>',
      root_path = '<?php echo $root_path; ?>';
   </script>
</head>
<body>
      <div id="pcoded" class="pcoded">
         <div class="pcoded-overlay-box"></div>
         <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
               <div class="navbar-wrapper">
                  <div class="navbar-logo">
                     <a href="/">
                     <img class="img-fluid" src="/public/users/images/logo.png" alt="Global Voice Direct Logo" />
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
                                    <img src="/public/user/profileImages/default.png" class="img-radius" alt="User-Profile-Image">
                                    <span><?php echo $_SESSION['nick_name'] ?></span>
                                    </a>
                                 </li>
                                  <li>
                                    <a href="/user/logout">
                                    <i class="feather icon-log-out"></i> Log Out
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
                                 <a href="/user/profile" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-user"></i></span>
                                 <span class="pcoded-mtext">Profile</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'orders')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="/user/order" class="waves-effect waves-dark">
                                 <span class="pcoded-micon"><i class="feather icon-menu"></i></span>
                                 <span class="pcoded-mtext">Orders</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'fax_mod')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="/user/fax_mod" class="waves-effect waves-dark">
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
                              <?php if(strpos($actual_link, 'BillPay')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="/user/billPay" class="waves-effect waves-dark">
                                    <span class="pcoded-micon">
                                    <i class="feather icon-package"></i>
                                    </span>
                                    <span class="pcoded-mtext">Bill Pay</span>
                                 </a>
                                 
                              </li>
                              <li class="active">
                                 <a href="/connect/beta/" class="waves-effect waves-dark">
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
                                 <a href="/user/credit_app" class="waves-effect waves-dark">
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
                                 <a href="/marketing-form" class="waves-effect waves-dark">
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
                                 <a href="/user/script_recording" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Script Recording</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'addport')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="/addPort" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Add Port</span>
                                 </a>
                              </li>
                              <?php if(strpos($actual_link, 'listingbusinessname')){ ?>
                              <li class="active">
                              <?php }else{ ?>
                                 <li class="">
                              <?php } ?>
                                 <a href="/listingbusinessname" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-command"></i>
                                 </span>
                                 <span class="pcoded-mtext">Business Listings</span>
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
                                 <a href="/smscampaign" class="waves-effect waves-dark">
                                 <span class="pcoded-micon">
                                 <i class="feather icon-clipboard"></i>
                                 </span>
                                 <span class="pcoded-mtext">SMS Campaign</span>
                                 </a>
                                 
                              </li>
                              <?php if(strpos($actual_link, 'emailCampaign') or strpos($actual_link, 'send_email') or strpos($actual_link, 'ot_editor_sendEmail')){ ?>
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
                                       <a href="/emailCampaign" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Manage Subscribers</span>
                                       </a>
                                    </li>
                                    <!-- <li class="">
                                       <a href="javascript:void(0)" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Contact Management</span>
                                       </a>
                                    </li> -->
                                    <!-- <?php if(strpos($actual_link, 'save_email_template')){ ?>
                                       <li class="active">
                                    <?php }else{ ?>
                                          <li class="">
                                    <?php } ?>
                                       <a href="save_email_template.php" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Create and Save Email Template</span>
                                       </a>
                                    </li> -->
                                    <?php if(strpos($actual_link, 'send_email')){ ?>
                                       <li class="active">
                                    <?php }else{ ?>
                                          <li class="">
                                    <?php } ?>
                                       <a href="/send_email" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Send a Campaign</span>
                                       </a>
                                    </li>
                                    <?php if(strpos($actual_link, 'ot_editor_sendEmail')){ ?>
                                       <li class="active">
                                    <?php }else{ ?>
                                          <li class="">
                                    <?php } ?>
                                       <a href="/ot_editor_sendEmail" class="waves-effect waves-dark">
                                       <span class="pcoded-mtext">Office Type Editor Email Campaign</span>
                                       </a>
                                    </li>
                                 </ul>
                                 <?php if(strpos($actual_link, 'index') or strpos($actual_link, 'audiosubscriber') or strpos($actual_link, 'active_calls') or strpos($actual_link, 'calls_history') or strpos($actual_link, 'later_calls_campaign')){ ?>
                                       <li class="active pcoded-hasmenu pcoded-trigger" dropdown-icon="style1" subitem-icon="style5">
                                    <?php }else{ ?>
                                       <li class="pcoded-hasmenu">
                                    <?php } ?>
                                       <a href="javascript:void(0)" class="waves-effect waves-dark">
                                       <span class="pcoded-micon">
                                       <i class="feather icon-mail"></i>
                                       </span>
                                       <span class="pcoded-mtext">Audio Campaign</span>
                                       </a>
                                       <ul class="pcoded-submenu">
                                          <?php if(strpos($actual_link, 'audiosubscriber')){ ?>
                                             <li class="active">
                                          <?php }else{ ?>
                                                <li class="">
                                          <?php } ?>
                                             <a href="/audiosubscriber" class="waves-effect waves-dark">
                                             <span class="pcoded-mtext">Manage Subscribers</span>
                                             </a>
                                          </li>
                                           
                                          <?php if(strpos($actual_link, 'index')){ ?>
                                             <li class="active">
                                          <?php }else{ ?>
                                                <li class="">
                                          <?php } ?>
                                             <a href="/user/audioCampaign" class="waves-effect waves-dark">
                                             <span class="pcoded-mtext">Audio Campaign</span>
                                             </a>
                                          </li>
                                          <?php if(strpos($actual_link, 'later_calls_campaign')){ ?>
                                             <li class="active">
                                          <?php }else{ ?>
                                                <li class="">
                                          <?php } ?>
                                             <a href="/later_calls_campaign" class="waves-effect waves-dark">
                                             <span class="pcoded-mtext">Schedule Audio Campaign</span>
                                             </a>
                                          </li>
                                          <?php if(strpos($actual_link, 'active_calls')){ ?>
                                             <li class="active">
                                          <?php }else{ ?>
                                                <li class="">
                                          <?php } ?>
                                             <a href="/active_calls" class="waves-effect waves-dark">
                                             <span class="pcoded-mtext">View Active Calls</span>
                                             </a>
                                          </li>
                                          <?php if(strpos($actual_link, 'calls_history')){ ?>
                                          <li class="active">
                                             <?php }else{ ?>
                                                <li class="">
                                             <?php } ?>
                                                <a href="/calls_history" class="waves-effect waves-dark">
                                                <span class="pcoded-mtext">View Call History</span>
                                                </a>
                                          </li>
                                       </ul>
                                    </li>
                              <div class="pcoded-navigation-label">Others</div>
                               <ul class="pcoded-item pcoded-left-item">
                                 <?php if(strpos($actual_link, 'howtovideo')){ ?>
                                    <li class="active">
                                 <?php }else{ ?>
                                       <li class="">
                                 <?php } ?>
                                    <a href="/user/howtovideo" class="waves-effect waves-dark">
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
                                    <li class="breadcrumb-item"><b><span>Phone Plans</span></b></li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                      </div>
                      <div class="pcoded-inner-content">
                        <div class="main-body">
                           <div class="page-wrapper">
                              <div class="container">
                                <div class="page-body">
                                    <div class="cards">
                                      <div class="row">
                                          <div class="col-md-12">
                                            <div class="top-title border-bootm">
                                               <h3 class="table-heading"><?php echo $text[strtoupper($layout)]; ?></h3>
                                            </div>
                                          </div>
                                        </div>
                                        <div class="form-inline-form">
                                            <div class="card-block">
                                               <div class="row top-video-section">
                                                  <div class="col-md-6 left-text">
                                                      <h4>You've Got Questions<br/>We've got answers</h4>
                                                  </div>
                                                  <div class="col-md-6 right-video">
                                                      <iframe height="300" width="100%" src="https://www.youtube-nocookie.com/embed/wmnxwPEHbqA?rel=0" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen=""></iframe>
                                                  </div>
                                               </div>
                                               <div class="row">
                                                  <div class="col-md-12">
                                                     <div id="wizard">
                                                      <section>
                                                        <div id="preloader" class="container text-center hidden">
                                                          <h3><?php echo $text['WAIT']; ?></h3>
                                                          <div id="status" class="progress progress-striped active hidden">
                                                            <div class="progress-bar" role="progressbar" id="status-bar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%">
                                                              <span id="status-bar-val" ></span>
                                                            </div>
                                                          </div>
                                                          <div id="progress" class="progress progress-striped active hidden">
                                                            <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                                                              <span id="status-bar-val" ></span>
                                                            </div>
                                                          </div>
                                                        </div>
                                                        <div id="error-container" class="alert alert-dismissable alert-danger<?php if ((!$error)): ?> hidden<?php endif;?>">
                                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                          <span id="error-field"><strong><?php echo $text['ERROR']; ?>:</strong> <?php echo $error; ?></span>
                                                        </div>
                                                        <div class="alert alert-dismissable alert-warning<?php if (!$warning): ?> hidden<?php endif;?>">
                                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                          <strong><?php echo $text['WARNING']; ?>:</strong> <?php echo $warning; ?>
                                                        </div>
                                                        <div class="alert alert-dismissable alert-success<?php if (!$success): ?> hidden<?php endif;?>">
                                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                          <strong><?php echo $text['CONGRATULATIONS']; ?>:</strong> <?php echo $success; ?>
                                                        </div>
                                                        <div class="alert alert-dismissable alert-info<?php if (!$notice): ?> hidden<?php endif;?>">
                                                          <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                                                          <strong><?php echo $text['NOTE']; ?>:</strong> <?php echo $notice; ?>
                                                        </div>
                                                        <span class="spacer10"></span>
                                                        <div class="languages text-center">
                                                          <div class="dropdown ">
                                                            <button class="btn btn-info dropdown-toggle" type="button" id="menu1" data-toggle="dropdown">  <img src="<?php echo $path . 'img/flags/' . $lang . '.gif'; ?>"/>
                                                               <?php echo $language; ?> <?php echo " ".$lang ?>
                                                            </button>
                                                               <!-- <span class="caret"></span></button> -->
                                                            <ul class="dropdown-menu" role="menu" aria-labelledby="menu1">
                                                                <?php  foreach ($langs as $language): ?>
                                                                 <li role="presentation" <?php if ($language == $lang): ?> class="active"<?php endif;?>>
                                                                    <a <?php if ($language == $lang): ?>href="#" role="menuitem" tabindex="-1" onclick="return false;"<?php else: ?>href="<?php echo $root_path . '?lang=' . $language . $vars; ?>"<?php endif;?>>
                                                                     <img src="<?php echo $path . 'img/flags/' . $language . '.gif'; ?>"/>
                                                                     <?php echo $language; ?>
                                                                   </a>
                                                                 </li>
                                                             <?php endforeach;?>
                                                           </ul>
                                                         </div>
                                                       </div>

                                                       <?php require_once 'templates/' . $template . '/html/' . ($layout) . '.php';?>
                                                        <div class="spacer5"></div>
                                                    </section>
                                                  </div>
                                               </div>
                                            </div>
                                         </div>
                                      </div>
                                   </div>
                                  <script src="<?php echo $path; ?>js/validator.js"></script>
                                </div>
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

      <script type="text/javascript">
         function getsmsDetail(id) {
         $.ajax({
            url: 'actions/getSmsSentDet.php',
            type: 'POST',
            data: {
               id: id
            },
            success: function(data) {
                 var queryData = JSON.parse(data);
         
               console.log(queryData);

               $('#sms_date').text(queryData['created_at']);
               $('#sms_to').val(queryData['sent_to']);
               if(queryData['accepted']){
                  $('#sms_status').addClass('green-text');
                  $('#sms_status').val("Sent");
               }else{
                  $('#sms_status').addClass('red-text');
                  $('#sms_status').val("Not Sent");
               }
               $('#sms_text').val(queryData['sent_sms']);
                
                modal.style.display = "block";
                
            },
            error: function() {
                alert("Something Went Wrong...Try Again.");
            },
            
         });
         
      }
      </script>
      <script type="text/javascript">
         $(document).ready(function(){
            $('.radio-0 input').prop("checked",true);
         });
         if($('#navbar .last').hasClass('active')){
            $('#progress-bar').css("width","1000px !important");
         }
         else{
            $('#progress-bar').css("width","400px !important");
         }

      </script>
      <script type="text/javascript">
        $(document).ready(function(){
          $('#navbar').find('.first').find('a').prepend('<i class="fas fa-briefcase fa-w-16" id="accountinfo_tab_icon"></i>');
          $('#navbar').find('.last').find('a').prepend('<i class="fas fa-address-book fa-w-16" id="accountoption_tab_icon"></i>');
        });
      </script>
      <!-- <script src="files/assets/js/script.js"></script> -->
      <script type="text/javascript" src="files/bower_components/jquery-ui/js/jquery-ui.min.js"></script>
      <script type="text/javascript" src="files/bower_components/popper.js/js/popper.min.js"></script>
      <script type="text/javascript" src="files/bower_components/bootstrap/js/bootstrap.min.js"></script>
      <script src="files/assets/pages/waves/js/waves.min.js"></script>
      <script type="text/javascript" src="files/bower_components/jquery-slimscroll/js/jquery.slimscroll.js"></script>
      <script src="files/assets/pages/widget/amchart/amcharts.js"></script>
      <script src="files/assets/pages/widget/amchart/serial.js"></script>
      <script src="files/assets/pages/widget/amchart/light.js"></script>
      <script src="files/bower_components/chartist/js/chartist.js"></script>
      <script src="files/assets/js/pcoded.min.js"></script>
      <script src="files/assets/js/vertical/vertical-layout.min.js"></script>
      <script src="files/bower_components/jquery.steps/js/jquery.steps.js"></script>
      <script type="text/javascript" src="files/assets/pages/dashboard/custom-dashboard.min.js"></script>
      <script type="text/javascript" src="files/assets/js/script.min.js"></script>
   </body>
</html>