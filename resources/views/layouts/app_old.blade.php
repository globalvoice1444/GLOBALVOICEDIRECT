<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="description" content="All-Inclusive Cloud Phone System with Voice,Fax,SMS & HD Meetings,Business credit line,business tradeline,start-up vendor account,Conferencing,audio." />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- <title>{{ config('app.name', 'State of the Art VoIP phone system - Global Voice Direct') }}</title> -->
    <title>State of the Art VoIP phone system - Global Voice Direct</title>

    <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet">

    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('images/favicon/favicon.ico') }}">
    <link rel="manifest" href="{{ asset('images/favicon/site.webmanifest') }}">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,600,600i,700,700i,800" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}" media="all">
    <!-- <link rel="stylesheet" href="{{ asset('smsCampaign/css/smsCampaign.css') }}"> -->
    <!-- <link rel="stylesheet" type="text/css" href="/css/font-awesome.css" media="all"> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link href="{{ asset('css/bootstrap-dropdownhover.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.theme.default.min.css') }}">

    <!--Custom CSS-->
    <link rel="stylesheet" type="text/css" href="{{asset('user/assets/icon/feather/css/feather.css')}}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/gvdstyle.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style2.css') }}">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.6.1/css/all.css" integrity="sha384-gfdkjb5BdAXd+lj+gudLWI+BXq4IuLW5IT+brZEZsLFm++aCMlF1V92rMkPaX4PP" crossorigin="anonymous">

    <script>//<![CDATA[
        var tlJsHost = "https://secure.comodo.com/";
        document.write(unescape("%3Cscript src='" + tlJsHost + "trustlogo/javascript/trustlogo.js' type='text/javascript'%3E%3C/script%3E"));
        //]]>
    </script>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-137383409-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-137383409-1');
    </script>
    <script>
        var shareasaleSSCID=shareasaleGetParameterByName("sscid");function shareasaleSetCookie(e,a,r,s,t){if(e&&a){var o,n=s?"; path="+s:"",i=t?"; domain="+t:"",l="";r&&((o=new Date).setTime(o.getTime()+r),l="; expires="+o.toUTCString()),document.cookie=e+"="+a+l+n+i}}function shareasaleGetParameterByName(e,a){a||(a=window.location.href),e=e.replace(/[\[\]]/g,"\\$&");var r=new RegExp("[?&]"+e+"(=([^&#]*)|&|#|$)").exec(a);return r?r[2]?decodeURIComponent(r[2].replace(/\+/g," ")):"":null}shareasaleSSCID&&shareasaleSetCookie("shareasaleSSCID",shareasaleSSCID,94670778e4,"/");
    </script>
    <style>
        nav#myNavbar {
            padding: 0;
        }
        #howitworks img.img-fluid {
            max-height: unset !important;
            width: 100% !important;
        }
        a#displaygetreferalform, a#displaynewreferalform {
            color: #134576;
            font-weight: 600;
            margin: 0px 0px 0px 3px;
        }
    </style>

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    @yield('css')
</head>
<body>
    <div class="main">
        <header>
            <div class="top-bar">
                <div class="container">
                    <div class="row">
                        <div class="col-md-12">
                            <ul class="list-unstyled list-inline pull-left">
                                @if (session()->has('user_id'))
                                    <li><a href="/user/profile"><i class="fa fa-tachometer-alt"></i>Dashboard</a></li>
                                    <li><a href="/user/logout"><i class="feather icon-log-out"></i>Logout</a></li>
                                @else
                                <li><a href="#" data-toggle="modal" data-target="#login-modal"><i class="fa fa-sign-in-alt"></i>Login</a></li>
                                <li><a href="/package-pricing" title=""><i class="fa fa-key"></i>Register</a></li>
                                @endif
                                <li><a href="/support/" title="" target="_blank"><i class="fa fa-support"></i>Support</a></li>

                            </ul>
                            <ul class="list-unstyled list-inline pull-right">
                                <li><a href="tel:1877-308-5803" title="">Call 1877-308-5803</a></li>
                                <li class="serch-bar">
                                    <button type="button" class="search-btn" data-toggle="collapse" data-target="#serch-box"><i class="fa fa-search"></i></button>
                                    <div class="search-box collapse" id="serch-box">
                                       <form class="search" action="search" method="get">
                                              <input type="text" name="search">
                                              <input type="button" value="Search" class="btn-search">
                                        </form>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="logo-bar">
                <div class="container">
                    <div class="navbar-logo">
                        <a href="/" title="">
                            <img src="{{ asset('images/gvdlogo.png') }}" alt="Global Voice Direct" class="img-responsive">
                        </a>
                    </div>
                    <div class="mid-links">
                        <ul class="list-unstyled list-inline">
                            
                            @if (!session()->has('user_id'))
                            <li>
                                <a href="/chose-pakage" title="" class="join-now">
                                    <img src="{{ asset('images/joinnow.png') }}" alt="Join Now - Phone System" class="img-responsive">
                                </a>
                            </li>
                            @endif
                            <li><a href="/howitworks" title="" class="how-it">How it Works</a></li>
                        </ul>
                    </div>
                    <div class="top-cart-contain  pull-right">
                        <!-- Top Cart -->
                        <div class="mini-cart">
                            <div id="cart">
                                <div class="basket dropdown">
                                    <a href="/cartItems" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-animations="zoomInLeft fadeInLeft zoomInLeft fadeInRight">
                                        <?php
                                        if(session()->has('cart_item')){

                                            $item_total = 0;
                                            $total = 0;
                                            $i=1;
                                            $cartSize = 0;
                                            $totalPrice = 0;
                                            foreach (session()->get('cart_item') as $item){

                                                $cartSize = $cartSize + 1;
                                                $id = $item["code"];
                                                $qty = $item["quantity"];
                                                $stmtLists =App\Http\Controllers\storeController::fetchProductsById($id);
                                                
                                                $rowListsCount =count($stmtLists);

                                               
                                                if($rowListsCount > 0){
                                                    /*while($rowLists = $stmtLists->fetch()){*/
                                                 foreach($stmtLists as $rowLists)
                                                 {       

                                                        $totalPrice = $totalPrice + ($rowLists->ProductPrice * $qty );
                                                        break;
                                                    }
                                                }
                                            }
                                            ?>
                                            <span class="cart_count"><?php echo $cartSize; ?></span>
                                            <span class="price hidden-xs">My Cart / $<?php echo $totalPrice;?></span>
                                        </a>
                                        <!-- <ul class="dropdown-menu top-cart-content">
                                            <li>
                                                <a href="cart/cartItems.php">
                                                    View Cart
                                                </a>
                                            </li>
                                        </ul> -->

                                        <ul class="dropdown-menu pull-right top-cart-content arrow_box">
                                            <p class="total-price"><span>Total: </span> <?php echo $totalPrice ;?></p>
                                            <li class="min-height">
                                                <?php foreach (session()->get('cart_item') as $item){
                                                  $id = $item["code"];
              
                                                   $stmtLists =App\Http\Controllers\storeController::fetchItems($id);
                                         
                                                    $rowListsCount = count($stmtLists);

                                                    if($rowListsCount > 0){
                                                      foreach($stmtLists as $list){
                 

                                                  ?>
                                            
                                                    <div class="drop-cart-row">
                                                        <div class="cart-item">
                                                            <img src="productImages/<?php echo $list->ProductImage?>" alt="<?php echo $list->ProductName; ?>" class="img-responsive">
                                                            <a class="item-title" href="#"><?php echo $list->ProductName; ?></a>
                                                            <span class="item-price"><?php if($list->addonFixedPrice != null)
                                                                  {
                                                                    $proPrice = $list->addonFixedPrice;
                                                                    echo "$".$proPrice;
                                                                  }
                                                                  else
                                                                  {
                                                                    $discount = $list->ProductPrice * $list->addonDiscount/100;
                                                                    $proPrice = $list->ProductPrice - $discount;
                                                                    echo "$".$proPrice;

                                                                  }?></span><span class="qty">Quantity: <?php echo $item["quantity"];?></span>
                                                        </div>
                                                    </div>
                                                <?php }}} ?>
                                                 
                                            </li>
                                            <li>
                                                <div class="dis-tabl">
                                                    <a href="/cartItems" class="tabl-cell btn btn-primary" title="">view Cart</a>
                                                    <a href="/checkout" title="" class="tabl-cell btn btn-primary">Checkout</a>
                                                </div>
                                            </li>
                                        </ul>
                                        <?php }else{?>
                                        <span class="cart_count">0</span>
                                        <span class="price">My Cart / $0.00</span>
                                    </a>
                                    <ul class="dropdown-menu top-cart-content">
                                        <li>
                                            <p class="text-center noitem">Your shopping cart is empty!</p>
                                        </li>
                                    </ul>
                                    <?php } ?>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header><!-- /header -->
        <div class="top-navbar">
            <div class="container">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#myNavbar">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <nav class="navbar collapse navbar-collapse" id="myNavbar">
                    <ul class="list-unstyled list-inline">
                        <li><a href="/" title="">Home</a></li>
                        <li><a href="/howitworks" title="">How it works</a></li>
                        <!-- <li class="dropdown">
                            <a href="howit-work.php" title="" class="dropdown-toggle"  data-toggle="dropdown" data-hover="dropdown" data-animations="zoomInLeft fadeInLeft zoomInLeft fadeInRight">How it works</a>
                            <ul class="dropdown-menu">
                                <li><a href="#" title="">Credit Application</a></li>
                            </ul>
                        </li> -->
                        <!-- @if (session()->has('user_id'))
                            <li><a href="/user/profile" title="">my account</a></li>
                        @else
                            <li><a href="#" data-toggle="modal" data-target="#login-modal" title="">my account</a></li>

                        @endif -->
                        <li><a href="/affiliate-referrals" title="Referral Program">Referral Program</a></li>
                        <li><a href="/store" title="Voip Phones">Voip Phones</a></li>
                        <li><a href="/feature" title="Features">Features</a></li>
                        <li><a href="/contact-us" title="contact">contact</a></li>
                        <?php if(!isset($_SESSION["user_id"])){ ?>
                            <li><a href="/chose-pakage" title="credit application">credit application</a></li>
                        <?php } ?>
                        <li><a href="/portin-request-form" title="">port-in-req form</a></li>
                        <li><a href="/download-app" title="">Apps</a></li>
                        <li><a href="https://mybilling.globalvoicedirect.com:8082/" title="">Phone System Login Page</a></li>
                    </ul>
                </nav>
            </div>
        </div>

<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-rapper">
        <div class="panel panel-default">
            <div class="panel-heading"><span class="login-text">LOGIN TO YOUR ACCOUNT</span><button type="button" class="close login-close" data-dismiss="modal">&times;</button></div>
            <div class="panel-body">

                <div class="col-12 col-sm-12 col-md-12">
                
                    <div class="col-12 col-sm-12 col-md-12 fm-p">
                        <form method="post" action="/loginAction">
                            {{ csrf_field() }}
                            <div class="form-group">
                                <input type="text" class="form-control sn-fm username-input" name="user" placeholder="Username">
                            </div>
                    
                            <div class="form-group">
                                <input type="password" class="form-control sn-fm password-input" name="pass" placeholder="Password">
                            </div>  
                            <div class="form-group">
                                <input type="submit" value="LOGIN" class="form-control btn btn-default" name="login">
                            </div>
                        </form>
                    </div>
                    
                    <div class="col-12 col-sm-12 col-md-12">
                        <p class="login-popup-text">
                            <a class="sn-a" href="/package-pricing">Register</a>-
                            <a class="sn-a" href="/forgotpassword">Forget Password</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@yield('content')


<footer>
    <div class="container">
        <div class="row">
            <div class="col-lg-3 col-sm-4 col-xs-6 col-md-3 main-footer-first-col">
                <img src="{{ asset('images/gvdlogo.png') }}" alt="We are one of the largest wholesale voice termination suppliers in the world">
                <p>We are one of the largest wholesale voice termination suppliers in the world, with over 33 billion minutes terminated per year.</p>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6 col-md-3">
                <h4>Customer Service</h4>
                <ul class="list-unstyled">
                    <li class="first"><a href="/contact-us">Contact Us</a></li>
                    <li class=""><a href="/our-offices">Our Offices</a></li>
                    <li class=""><a href="/store">Voip Phones</a></li>
                    <li class=""><a href="/howitworks">HowIt Works</a></li>
                    <li><a href="https://globalvoicedirect.com/script-samples/Automated%20Attendant.pdf" target="_blank">CLICK HERE TO DOWNLOAD</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6 col-md-3">
                <h4>Information</h4>
                <ul class="list-unstyled">
                    <li class="first"><a href="/about">About Us</a></li>
                    <li class=""><a href="/tos">Terms of Service</a></li>
                    <li class=""><a href="/cartItems">View Cart</a></li>
                    <li class=""><a href="/package-pricing">Join Now</a></li>
                </ul>
            </div>
            <div class="col-lg-3 col-sm-4 col-xs-6 col-md-3">
                <h4>Search Now</h4>
                 <form action="search" method="get" class="search">
                    <div class="input-group">
                        <input type="text" class="form-control" name="search" placeholder="Search for...">
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-search" type="button" class="search-btn"><i class="fa fa-search" aria-hidden="true"></i></button>
                        </span>
                    </div><!-- /input-group -->
                </form>
                <h4>Follow Us</h4>
                <ul class="socials list-unstyled list-inline">
                    <li>
                        <a href="#" title="" class="fb">
                            <i class="fab fa-facebook-f" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" title="" class="tw">
                            <i class="fab fa-twitter" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" title="" class="g-plus">
                            <i class="fab fa-google-plus-g" aria-hidden="true"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#" title="" class="linked-in">
                            <i class="fab fa-linkedin-in" aria-hidden="true"></i>
                        </a>
                    </li>

                </ul>

                <a class="comodo-secure" href="javascript:if(window.open('https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=W&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5','tl_wnd_credentials'+(new Date()).getTime(),'toolbar=0,scrollbars=1,location=1,status=1,menubar=1,resizable=1,width=374,height=660,left=60,top=120')){};tLlB(tLTB);" onmouseover="tLeB(event,'https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=C&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5','tl_popupSC5')" onmousemove="tLXB(event)" onmouseout="tLTC('tl_popupSC5')" ondragstart="return false;"><img src="{{asset('images/combo.png')}}" alt="combo" border="0" onmousedown="return tLKB(event,'https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=W&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5');" oncontextmenu="return tLLB(event);"></a>
                <a class="comodo-secure" href="https://ssl.comodo.com/ev-ssl-certificates.php" id="comodoTL">EV SSL</a>

            </div>
        </div>
        <!-- <address>
            <i class="fa fa-map-marker"></i>2211 2nd Avenue North, Suite 106 Lake Worth FL 33461
            <i class="fa fa-mobile"></i>
            <span> +877 308 5803</span>
            <i class="fa fa-envelope"></i><span>
            support@globalvoicedirect.com</span>
        </address> -->
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-12 coppyright text-center">© 2010 - 2019 GlobalVoiceDirect.com, All Rights Reserved.</div>
            </div>
        </div>
    </div>
</footer>
<div class="view-notification">
    <div class="view-notification-container">
        <div class="notification-image">
            
        </div>
        <div class="notification-view-count">
            <img src="{{ asset('images/load.svg') }}" alt="Number of Views">
            <div class="right-notification">
                <h4 class="count">Andrew from Nepa, CA</h4> <p>has recently signed Up</p><p class="days-sec">4 days ago</p>
            </div>
        </div>
        <div class="approved-by">
            
        </div>
    </div>
</div>
<script src="{{ asset('js/jquery.js') }}"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->
<script
  src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
  integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
  crossorigin="anonymous"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/animation.js') }}"></script>
<script src="{{ asset('js/bootstrap-dropdownhover.min.js') }}"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/imagezoom.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src='https://www.google.com/recaptcha/api.js'></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>

@yield('scripts')
<script type="text/javascript">
    function nonmemeberreferal(){
        $.ajax({
            url: 'affiliate-link',
            type: 'POST',
            data: $('#non-memeber-referal').serialize(),
            success: function(result){
                $('#non-memeber-referal')[0].reset();
                $('#refrel-link').text(result);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                alert(XMLHttpRequest['responseText']);
                console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
            }
        });
        return false;
    }
    function getnonmemeberreferal(){
        $.ajax({
            url: 'get-affiliate-link',
            type: 'POST',
            data: $('#non-memeber-referal-link').serialize(),
            success: function(result){
                $('#non-memeber-referal-link')[0].reset();
                $('#refrel-link').text(result);
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) { 
                alert(XMLHttpRequest['responseText']);
                console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
            }
        });
        return false;
    }
    function copyclipboardlink(){
        $("#refrel-link").select();
        document.execCommand('copy');
    }
    function displaygetreferalform(){
        $("#non-memeber-referal").css("display", "none");
        $("#non-memeber-referal-link").css("display", "block");
    }
    function displaynewreferalform(){
        $("#non-memeber-referal-link").css("display", "none");
        $("#non-memeber-referal").css("display", "block");
    }
</script>
<script>
    $(document).ready(function(){

        $( ".hover-product" ).on( "mouseenter", function() {
           $(this).find( '.hidden1' ).css( 'display', 'block' );
          })
          .on( "mouseleave", function() {
             $(this).find( '.hidden1' ).css( 'display', 'none' );
          });

         
        $('.customer-logos').slick({
            slidesToShow: 6,
            slidesToScroll: 1,
            autoplay: true,
            autoplaySpeed: 1500,
            arrows: false,
            dots: false,
            pauseOnHover: false,
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: 4
                }
            }, {
                breakpoint: 520,
                settings: {
                    slidesToShow: 3
                }
            }]
        });
    });
</script>
<script>
    $(document).ready(function() {
        var owl = $('#home-banr');
        owl.owlCarousel({
            animateOut: 'slideOutLeft',
            animateIn: 'slideInRight',
            items: 1,
            margin: 0,
            stagePadding: 0,
            smartSpeed: 450
        });
        var owl = $('#quick-slider');
        owl.owlCarousel({
            items: 4,
            margin: 5,
            nav: true,
            dots:false,
            stagePadding: 0,
            smartSpeed: 450
        });

        $('.animate').scrolla({
            mobile: false,
            once: true
        });
            // $(".quick-view").click(function(){
            //  // $("#quickview").modal();
            // });
        })
    </script>
    <script>
        $('[data-imagezoom]').imageZoom({
            cursorcolor:'255,255,255',
            opacity:0.5,
            cursor:'crosshair',
            zindex:2147483647,
            zoomviewsize:[500,500],
            zoomviewposition:'right',
            zoomviewmargin:10,
            zoomviewborder:'1px solid #000',
            magnification:3
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#smsContactTable').DataTable();
        } );
    </script>
    <!-- <script type="text/javascript">
        var modal = document.getElementById('myModal1');

        // Get the button that opens the modal
         

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];


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
    </script> -->
    <script>
        function getsmsDetail(id) {
            $.ajax({
               url: 'getSmsSentDet.php',
               type: 'POST',
               data: {
                  id: id
               },
               success: function(data) {
                  var queryData = JSON.parse(data);
            
                  console.log(queryData);

                  $('#sms_date').text(queryData['created_at']);
                  $('#sms_to').text(queryData['sent_to']);
                  if(queryData['accepted']){
                    $('#sms_status').addClass('green-text');
                    $('#sms_status').text("Sent");
                  }else{
                    $('#sms_status').addClass('red-text');
                    $('#sms_status').text("Not Sent");
                  }
                  $('#sms_text').text(queryData['sent_sms']);
                   
                   modal.style.display = "block";
                   
               },
               error: function() {
                   alert("Something Went Wrong...Try Again.");
               },
               
            });
           
        }
    </script>

    <!-- <script language="JavaScript" type="text/javascript">
        TrustLogo("https://globalvoicedirect.com/script-samples/comodo_secure_seal_113x59_transp.png", "SC5", "none");
    </script> -->
    

    <script src="https://static.leaddyno.com/js"></script>
    <script>
        LeadDyno.key = "{{ env('LEADDYNO_KEY') }}";
        LeadDyno.recordVisit();
        LeadDyno.autoWatch();
    </script>
    <!-- <script>
        window.setInterval(function(){
            $.ajax({
                url: '/recentlyaddedmemeber',
                method: 'post',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data){
                    jsondata = JSON.parse(data);
                    $('.count').text(jsondata[0]);
                    $('.days-sec').text(jsondata[1]);
                    $('.view-notification').css('display','block');
                    setTimeout(function() {
                        $('.view-notification').css('display','none');
                    }, 3000);
                }
            });
        }, 6000);
        
    </script> -->
    <script>
        function init() {
        var vidDefer = document.getElementsByTagName('iframe');
        for (var i=0; i<vidDefer.length; i++) {
        if(vidDefer[i].getAttribute('data-src')) {
        vidDefer[i].setAttribute('src',vidDefer[i].getAttribute('data-src'));
        } } }
        window.onload = init;
         $('.btn-search').on('click', function(){
          $('.search').submit();
        })
    </script>
@stack('scripts')
<script async src="https://chat.ithinq.ai/widget.js" data-widget-id="wgt_o8bdyw4oybzkfsmnrg8sbwv5711f"></script>
</body>
</html>