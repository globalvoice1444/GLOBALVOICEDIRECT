@php 
   $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head> 
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="csrf-token" content="{{ csrf_token() }}" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, maximum-scale=1.0, user-scalable=0" />

	<title>Global Voice Direct</title>
	<!-- Latest compiled and minified CSS -->
   <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/favicon.ico') }}">
   <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
	<script type="text/javascript" src="{{ asset('admin/bower_components/jquery/js/jquery.min.js') }}"></script>
	<script>
	window.paceOptions = {
		ajax: false
	};
	</script>
   <script type="text/javascript" src="{{ asset('admin/assets/js/pace.min.js') }}"></script>
<!-- 	 <link rel="stylesheet" href="https://globalvoicedirect.com/admin/admin_bak/css/bootstrap.min.css" type="text/css" > -->
<!-- 	<link href="css/font-awesome.min.css" rel="stylesheet" type="text/css" /> --> 
	<!-- <link href="css/style.css" rel="stylesheet" type="text/css" /> -->
	<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet">
	<link href="https://fonts.googleapis.com/css?family=Montserrat:400,500,600,700" rel="stylesheet">
   <link rel="stylesheet" type="text/css" href="{{ asset('admin/bower_components/bootstrap/css/bootstrap.min.css') }}"> 
  <link rel="stylesheet" href="{{ asset('admin/assets/pages/waves/css/waves.min.css') }}" type="text/css" media="all">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/icon/feather/css/feather.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/font-awesome-n.min.css') }}">
  <link rel="stylesheet" href="{{ asset('admin/assets/pages/chart/radial/css/radial.css') }}" type="text/css" media="all">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/style.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('admin/assets/css/widget.css') }}">
  @yield('css')
</head>
<body class="pace-done gvd-adm" data-gr-c-s-loaded="true" themebg-pattern="theme1">
	<div id="pcoded" class="pcoded">
         <div class="pcoded-overlay-box"></div>
         <div class="pcoded-container navbar-wrapper">
            <nav class="navbar header-navbar pcoded-header">
               <div class="navbar-wrapper">
                  <div class="navbar-logo">
                     <a href="/">
                     <img class="img-fluid" src="{{ asset('admin/assets/images/gvdlogo.png') }}" alt="Theme-Logo" />
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
                                 <li>
                                    <div class="media" style="display:none">
                                       <img class="img-radius" src="assets/images/avatar-4.jpg" alt="Generic placeholder image">
                                       <div class="media-body">
                                          <h5 class="notification-user">John Doe</h5>
                                          <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                          <span class="notification-time">30 minutes ago</span>
                                       </div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="media" style="display:none">
                                       <img class="img-radius" src="assets/images/avatar-3.jpg" alt="Generic placeholder image">
                                       <div class="media-body">
                                          <h5 class="notification-user">Joseph William</h5>
                                          <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                          <span class="notification-time">30 minutes ago</span>
                                       </div>
                                    </div>
                                 </li>
                                 <li>
                                    <div class="media" style="display:none">
                                       <img class="img-radius" src="assets/images/avatar-4.jpg" alt="Generic placeholder image">
                                       <div class="media-body">
                                          <h5 class="notification-user">Sara Soudein</h5>
                                          <p class="notification-msg">Lorem ipsum dolor sit amet, consectetuer elit.</p>
                                          <span class="notification-time">30 minutes ago</span>
                                       </div>
                                    </div>
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
                                 <i class="icon feather icon-settings">
                                    <span class="badge bg-c-red">5</span>
                                 </i>
                              </div>
                              <ul class="show-notification profile-notification dropdown-menu" data-dropdown-in="fadeIn" data-dropdown-out="fadeOut">
                                 <li class="drp-u-details">
                                    <img src="https://img.icons8.com/color/48/000000/admin-settings-male.png" class="img-radius" alt="User-Profile-Image">
                                    <span>Admin</span>
                                    <a href="logout" class="dud-logout" title="Logout">
                                    <i class="feather icon-log-out"></i>
                                    </a>
                                 </li>
                              </ul>
                           </div>
                        </li>
                     </ul>
                  </div>
               </div>
            </nav>
            <div id="sidebar" class="users p-chat-user showChat">
               <div class="had-container">
                  <div class="p-fixed users-main">
                     <div class="user-box">
                        <div class="chat-search-box">
                           <a class="back_friendlist">
                           <i class="feather icon-x"></i>
                           </a>
                           <div class="right-icon-control">
                              <div class="input-group input-group-button">
                                 <input type="text" id="search-friends" name="footer-email" class="form-control" placeholder="Search Friend">
                                 <div class="input-group-append">
                                    <button class="btn btn-primary waves-effect waves-light" type="button"><i class="feather icon-search"></i></button>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <div class="showChat_inner">
               
            </div>
            <div class="pcoded-main-container">
            	<nav class="pcoded-navbar">
                    <div class="nav-list">
                        <div class="pcoded-inner-navbar main-menu">
                           <div class="pcoded-navigation-label">Navigation</div>
                           <ul class="pcoded-item pcoded-left-item">
                                <li class="  ">
	                                <a href="/" class="waves-effect waves-dark">
	                                    <span class="pcoded-micon"><i class="feather icon-home"></i></span>
	                                    <span class="pcoded-mtext">Home</span>
	                                </a>
                                 
                                </li>
                               	@if(strpos($actual_link, 'categories'))
                                 <li class="active">
                                 @else
                                    <li class="">
                                 @endif
	                                <a href="categories" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-list"></i></span>
	                                <span class="pcoded-mtext">Categories</span>
	                                </a>
                                </li> 
                                @if(strpos($actual_link, 'addon'))
                                 	<li class="active">
                                @else
                                    <li class="">
                                @endif
	                                <a href="addon" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fas fa-puzzle-piece"></i></span>
	                                <span class="pcoded-mtext">Addons</span>
	                                </a>
	                            </li>
                                @if(strpos($actual_link, 'products'))
	                            	<li class="active">
	                            @else
	                                <li class="">
	                            @endif
	                                <a href="products" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-briefcase"></i></span>
	                                <span class="pcoded-mtext">Products</span>
	                                </a>
	                            </li>
                                @if(strpos($actual_link, 'orders'))
	                                <li class="active">
	                            @else
	                                <li class="">
	                            @endif
	                                <a href="orders" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-credit-card"></i></span>
	                                <span class="pcoded-mtext">Orders</span>
	                                </a>
	                            </li>
                                @if(strpos($actual_link, 'Payments'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif 
	                                <a href="Payments" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-layers"></i></span>
	                                <span class="pcoded-mtext">Payments</span>
	                                </a>
                            	</li>
								@if(strpos($actual_link, 'paymentgateway'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif 
	                                <a href="paymentgateways" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-credit-card"></i></span>
	                                <span class="pcoded-mtext">Payment Gateways</span>
	                                </a>
                            	</li>
                                @if(strpos($actual_link, 'usermanagement'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif
	                                <a href="usermanagement" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-user"></i></span>
	                                <span class="pcoded-mtext">User Management</span>
	                                </a>
	                            </li>
                                @if(strpos($actual_link, 'm_form'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif 
	                                <a href="m_form" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-credit-card"></i></span>
	                                <span class="pcoded-mtext">Marketing Form</span>
	                                </a>
                                </li>
                                @if(strpos($actual_link, 'add_credit_form'))
                                  <li class="active">
                                @else
                                   <li class="">
                                @endif 
                                    <a href="/admin/add_credit_form" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="fa fa-credit-card"></i></span>
                                      <span class="pcoded-mtext">Additional Credit App</span>
                                    </a>
                                  </li>
                                @if(strpos($actual_link, 'scriptRecording'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif
	                                <a href="scriptRecording" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-credit-card"></i></span>
	                                <span class="pcoded-mtext">Script Recording</span>
	                                </a>
	                            </li>
                                <li class="{{ strpos($actual_link, 'admin/alerts') ? 'active' : '' }}">
                                    <a href="/admin/alerts" class="waves-effect waves-dark">
                                      <span class="pcoded-micon"><i class="fa fa-bell"></i></span>
                                      <span class="pcoded-mtext">Alerts @php $__gvdUnread = \App\Support\AdminAlerts::unread(); @endphp @if($__gvdUnread)<span class="badge badge-danger" style="background:#e74c3c;color:#fff;border-radius:10px;padding:1px 7px;margin-left:4px">{{ $__gvdUnread }}</span>@endif</span>
                                    </a>
                                  </li>
                                <li class="{{ strpos($actual_link, 'admin/automations/drip') ? 'active' : '' }}"><a href="/admin/automations/drip" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-envelope"></i></span><span class="pcoded-mtext">Automations · Drip Sequences</span></a></li>
                                <li class="{{ strpos($actual_link, 'admin/automations/workflows') ? 'active' : '' }}"><a href="/admin/automations/workflows" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-sitemap"></i></span><span class="pcoded-mtext">Automations · Workflows</span></a></li>
                                <li class="{{ strpos($actual_link, 'admin/automations/broadcasts') ? 'active' : '' }}"><a href="/admin/automations/broadcasts" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-bullhorn"></i></span><span class="pcoded-mtext">Automations · Broadcasts</span></a></li>
                                <li class="{{ strpos($actual_link, 'admin/automations/customer-email') ? 'active' : '' }}"><a href="/admin/automations/customer-email" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-paper-plane"></i></span><span class="pcoded-mtext">Automations · Customer email</span></a></li>
                                @include('admin.partials.ai-connect-nav')
                                @include('admin.partials.ai-connect-voice-nav')
                                <li class="{{ strpos($actual_link, 'admin/forms/marketing') ? 'active' : '' }}"><a href="/admin/forms/marketing" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-bullhorn"></i></span><span class="pcoded-mtext">Marketing Requests</span></a></li>
                                <li class="{{ strpos($actual_link, 'admin/forms/listings') ? 'active' : '' }}"><a href="/admin/forms/listings" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-map-marker"></i></span><span class="pcoded-mtext">Business Listings</span></a></li>
                                <li class="{{ strpos($actual_link, 'admin/forms/credit') ? 'active' : '' }}"><a href="/admin/forms/credit" class="waves-effect waves-dark"><span class="pcoded-micon"><i class="fa fa-credit-card"></i></span><span class="pcoded-mtext">Credit Applications (new)</span></a></li>

                                 
                                @if(strpos($actual_link, 'creditappInfo'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif
	                                <a href="creditappInfo" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-credit-card"></i></span>
	                                <span class="pcoded-mtext">Credit Application Form</span>
	                                </a>
                                </li>
                              @if(strpos($actual_link, 'fax_management'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif
	                                <a href="fax_management" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-fax"></i></span>
	                                <span class="pcoded-mtext">Fax</span>
	                                </a>
	                            </li>
								@if(strpos($actual_link, 'sms_management'))
	                              	<li class="active">
	                            @else
	                                <li class="">
	                            @endif
	                                <a href="sms_management" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-fax"></i></span>
	                                <span class="pcoded-mtext">SMS</span>
	                                </a>
	                            </li>
								@if(strpos($actual_link, 'ghl-workflow'))
	                              	<li class="active">
	                            @else
	                                <li class="">
	                            @endif
	                                <a href="ghl-workflow" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="fa fa-fax"></i></span>
	                                <span class="pcoded-mtext">Go High Level</span>
	                                </a>
	                            </li>
                              <li class="{{ (\Request::route()->getName() == 'admin.coupon.show') ? 'active' : '' }}">
                                  <a href="{{route('admin.coupon.show')}}" class="waves-effect waves-dark">
                                  <span class="pcoded-micon"><i class="fa fa-fax"></i></span>
                                  <span class="pcoded-mtext">Coupons</span>
                                  </a>
                              </li>
                              @if(strpos($actual_link, 'sendemail'))
	                              <li class="active">
	                            @else
	                                 <li class="">
	                            @endif
	                                <a href="sendemail" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-mail"></i></span>
	                                <span class="pcoded-mtext">Send Emails To Active Uers</span>
	                                </a>
                                </li>
                                <li class="">
	                                <a href="logout" class="waves-effect waves-dark">
	                                <span class="pcoded-micon"><i class="feather icon-log-out"></i></span>
	                                <span class="pcoded-mtext">Log Out</span>
	                                </a>
	                            </li>
                                 
                           </ul>
                           
                        </div>
                     </div>
               	</nav>
            <div class="pcoded-content">
            	@php $__gvdSec = \App\Support\AdminAlerts::unread('security'); @endphp
            	@if($__gvdSec && !request()->is('admin/alerts'))<div class="alert alert-danger" style="margin:15px 15px 0"><b>Security:</b> {{ $__gvdSec }} unread security alert{{ $__gvdSec > 1 ? 's' : '' }}. <a href="/admin/alerts?type=security&amp;state=unread">Review now →</a></div>@endif
            	@if(request()->is("admin/categories"))@include("admin.gvd-hero")@endif
            	@yield("content")

            </div>
		</div>
	</div>
</div>
	<!-- <script src="js/jquery-1.11.3.min.js"></script> -->
	  <script type="text/javascript" src="{{ asset('admin/bower_components/jquery-ui/js/jquery-ui.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('admin/bower_components/popper.js/js/popper.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('admin/bower_components/bootstrap/js/bootstrap.min.js') }}"></script>
      <script src="{{ asset('admin/assets/pages/waves/js/waves.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('admin/bower_components/jquery-slimscroll/js/jquery.slimscroll.js') }}"></script>
      <script src="{{ asset('admin/assets/pages/widget/amchart/amcharts.js') }}"></script>
      <script src="{{ asset('admin/assets/pages/widget/amchart/serial.js') }}"></script>
      <script src="{{ asset('admin/assets/pages/widget/amchart/light.js') }}"></script>
      <script src="{{ asset('admin/bower_components/chartist/js/chartist.js') }}"></script>
      <script src="{{ asset('admin/assets/js/pcoded.min.js') }}"></script>
      <script src="{{ asset('admin/assets/js/vertical/vertical-layout.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('admin/assets/pages/dashboard/custom-dashboard.min.js') }}"></script>
      <script type="text/javascript" src="{{ asset('admin/assets/js/script.min.js') }}"></script>
	<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
	<script src="js/bootstrap.min.js"></script> -->
	<script type="text/javascript">
		var modal = document.getElementById('myModal');

		// Get the button that opens the modal
		 

		// Get the <span> element that closes the modal
		var span = document.getElementsByClassName("close")[0];

		function updateProduct(id) {
			var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
			   url: 'get_updateProduct',
			   type: 'POST',
			   data: {
			      id: id,
			      _token: CSRF_TOKEN
			   },
			   success: function(data) {
			      var  prodDet = JSON.parse(data);
			      $('#productId').val(prodDet['ProductID']);
			      $('#uproductname').val(prodDet['ProductName']);
			      $('#udescription').val(prodDet['ProductCartDesc']);
			      $('#ushortDesc').val(prodDet['ProductShortDesc']);
			      $('#ulongDesc').val(prodDet['ProductLongDesc']);
			      $('#uprice').val(prodDet['ProductPrice']);
			      $('#uweight').val(prodDet['ProductWeight']);
			       
			      $('#categorySel option[value="'+prodDet['ProductCategoryID']+'"]').attr("selected",true);
			      $('#uquantity').val(prodDet['ProductStock']);

			       modal.style.display = "block";
			       
			   },
			   error: function() {
			       alert("Something Went Wrong...Try Again.");
			   },
			   
			});
		   
		}
function updateAddonProduct(id) {
			var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
			   	url: 'get_updateAddonProducts',
			   	type: 'POST',
			   	data: {
			   		id: id,
			   		_token: CSRF_TOKEN
			   	},
			   	success: function(data) {
			      var  prodDet = JSON.parse(data);
			      $('#addonProductId').val(prodDet['id']);
			      $('#proid').val(prodDet['product_id']);
			      $('#title').val(prodDet['addonTitle']);
			      $('#desc').val(prodDet['addonDescription']);
			      var path = prodDet['addonDescription'];
			      if(prodDet['addonDiscount']==null){
			      	$('#descountPercentage').val(prodDet['addonFixedPrice']);
			      	$('#discountAddon').val(1);
			      }
			      else{
			      	$('#descountPercentage').val(prodDet['addonDiscount']);
			      	$('#discountAddon').val(2);
			      }
			      //$('#descountPercentage').val(prodDet['addonDiscount']);
			     // $('#startDate').val(prodDet['start_date']);
			      //$('#endDate').val(prodDet['end_date']);
			      var startStr = prodDet['start_date'];
			      var startdate = startStr.slice(0,10);
			      var endstr = prodDet['end_date'];
			      var enddate = endstr.slice(0,10);
			      $('#startDate').val(startdate);
			      $('#endDate').val(enddate);
			      //document.getElementById("startDate").startdate = date;//"2014-02-09"; //prodDet['start_date'];
			      /* if(prodDet['addonFixedPrice'] != null){
			      	console.log('ok');
			      	//return false;
			      	$('#descountPercentage').val(prodDet['addonFixedPrice']);
			       }*/
			      
			       //addonModal.style.display = "block";
			       $("#add_addons").modal('show');
			       
			   },
			   error: function() {
			       alert("Something Went Wrong...Try Again.");
			   },
			   
			});
		   
		}
		function updateOrder(id) {
			var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
			   url: 'get_orderDetailss',
			   type: 'POST',
			   data: {
			      id: id,
			      _token: CSRF_TOKEN
			   },
			   success: function(data) {
			   	  var queryData = JSON.parse(data);
			      var orderDet = queryData[0];
			      console.log(orderDet);

			      $('#bname').text(orderDet['OrderShipName']);
			      $('#trackno').text(orderDet['OrderTrackingNumber']);
			      $('#amount').text(orderDet['OrderAmount']);
			      $('#downpayment').text(orderDet['OrderAmount']/2);
			      $('#date').text(orderDet['OrderDate']);
			      $('#phone').text(orderDet['OrderPhone']);
			      $('#email').text(orderDet['OrderEmail']);
			      $('#city').text(orderDet['OrderCity']);
			      $('#state').text(orderDet['OrderState']);
			      $('#country').text(orderDet['OrderCountry']);
			      $('#Zip').text(orderDet['OrderZip']);
			      $('#address1').text(orderDet['OrderShipAddress']);
			      $('#address2').text(orderDet['OrderShipAddress2']);

			       

			       var orderDetails = queryData[1];

			       var productDet = queryData[2];

			       console.log(queryData[2]);

			       var str = "";

			       for(var val in orderDetails){
			       	var orPrice = 0;
			       	if(orderDetails[val]['order_price'] > 0){
			       		orPrice = orderDetails[val]['order_price'];
			       	}else {
			       		orPrice = productDet[val]['ProductPrice']
			       	}
			       		console.log(orderDetails[val]['product_id']);
			       		str = str + "<tr><td>"+(val)+"</td><td>"+productDet[val]['ProductName']+"</td><td>$"+orPrice+"</td><td>"+productDet[val]['ProductCartDesc']+"</td><td>"+orderDetails[val]['quantity']+"</td><td>$"+(orderDetails[val]['quantity'] * orPrice)+"</td></tr>"
			       }
			       $('#orderDet').html(str);

			       
			       $("#url1").attr("href", "updateOrderStatus?status=1&oid="+orderDet['OrderID']);
			       $("#url2").attr("href", "updateOrderStatus?status=2&oid="+orderDet['OrderID']);
			        

			       modal.style.display = "block";
			       
			   },
			   error: function() {
			       alert("Something Went Wrong...Try Again.");
			   },
			   
			});
		   
		}
	 	
	 	function getMarketInfo(id) {
	 		var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
			   url: 'get_MarketInfo',
			   type: 'POST',
			   data: {
			      id: id,
			      _token: CSRF_TOKEN
			   },
			   success: function(data) {
			   	  var queryData = JSON.parse(data);
			       
			      console.log(queryData);

			      $('#b-name').text(queryData['buisness_name']);
			      $('#streetadress').text(queryData['street_address']);
			      $('#city').text(queryData['city']);
			      $('#state').text(queryData['state']);
			      $('#zip').text(queryData['zip_code']);
			      $('#fname').text(queryData['first_name']);
			      $('#lname').text(queryData['last_name']);
			      $('#b-phone').text(queryData['buisness_phone']);
			      $('#b-email').text(queryData['buisness_email']);
			      $('#b-hour').text(queryData['buisness_hour']);
			      $('#b-website').text(queryData['buisness_website']);
			      $('#b-cat').text(queryData['buisness_keyword']);
			      $('#b-desc').text(queryData['buisness_desc']);
			      $('#s-desc').text(queryData['short_description']);
			      $('#s-sites').text(queryData['social_sites']);
			      $('#date').text(queryData['created_at']);
			       
 				  if(queryData['image_path'] != ""){
 				  	var imgPath = queryData['image_path'];
 				  	var imgdiv = '<div class="row"><div class="col-lg-2 space-right"><label><strong>Logo/Other Image: </strong></label></div><div class="col-lg-10 clearfix text-left"><img class="img-logo" src="/marketImages/'+imgPath+'" /></div>';

 				  	$('#image_log').html(imgdiv);
 				  }
 

			       modal.style.display = "block";
			       
			   },
			   error: function() {
			       alert("Something Went Wrong...Try Again.");
			   },
			   
			});
		   
		}

		function getScriptInfo(id) {
			var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
			$.ajax({
			   url: 'get_scriptrec',
			   type: 'POST',
			   data: {
			      id: id,
			      _token: CSRF_TOKEN
			   },
			   success: function(data) {
			   	  var queryData = JSON.parse(data);
			       
			      console.log(queryData);
            

			      $('#maingreetingscript').text(queryData['main_greeting_script']);
			      $('#after-hours-greeting').text(queryData['after_hours_script']);
			      $('#hold-script').text(queryData['hold_script']);
            $("#special_pronunciation_words").text(queryData['special_pronunciation_words']);
            //$("<audio controls><source src='/"+queryData['audio_file_path']+"' type='audio/mpeg'></audio>").appendTo("#audioplayer");
            $("#audioplayer").html(queryData['audio_file_path'] ? "<audio controls><source src='/admin/script-audio/"+parseInt(id,10)+"' type='audio/mpeg'></audio>" : "");
			       
			      $('#date').text(queryData['created_at']);
			       
			       modal.style.display = "block";
			       
			   },
			   error: function() {
			       alert("Something Went Wrong...Try Again.");
			   },
			   
			});
		   
		}

		function addTrackNumber(id) {
	 		$('#orderid').val(id);
		}
	 
		function gettrackstatus( trackcompany, trackno ){
		//	alert(trackno + trackcompany);
			$.ajax({
			   url: 'action/get_traackingstatus.php',
			   type: 'POST',
			   data: {
			      trackingnumber: trackno,
			      cparcel: trackcompany
			   },
			   success: function(data) {
			   	$('#track_status').html(data);			   
			   }
			});
		}
		// When the user clicks on <span> (x), close the modal
		span.onclick = function() {
		    modal.style.display = "none";
		}

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
		    if (event.target == modal) {
		        modal.style.display = "none";
		    }
		}
	</script>
	<link rel="stylesheet" href="https://cdn.datatables.net/1.10.2/css/jquery.dataTables.min.css"></style>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="/css/gvd-admin.css?v=4">
<script type="text/javascript" src="https://cdn.datatables.net/1.10.2/js/jquery.dataTables.min.js"></script>
<script type="text/javascript">
	$(document).ready(function() {
		// Get the modal
    	$('#producttable').DataTable();
    	 
    	
	} );
</script>
<script type="text/javascript">
	$(document).ready(function() {
		// Get the modal
  
    	$('#m-info-table').DataTable();
    	
	} );
	function confirmRemove(id)
        {
        var status=confirm('Are you sure you want to remove?');

        if(status)
        {
        	//$('#pid').val(id);
        // $('#remove_form').attr('onsubmit', true);
        window.location.href="act_removeAddonproduct?pid="+id+"";
          // $('#remove_form').submit();
        }
        else
        {
           return false;
        }
        }
</script>
	@yield('script')
</body>
</html>