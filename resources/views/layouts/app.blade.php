<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <!-- Meta Tags -->
    <meta http-equiv="content-type" content="text/html;charset=utf-8">
    <meta charset="UTF-8">
    <meta http-equiv = "content-language" content = "en-US">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="description" content="All-Inclusive Cloud Phone System with Voice,Fax,SMS &amp; HD Meetings,Business credit line,business tradeline,start-up vendor account,Conferencing,audio.">
    <meta name="keywords" content="">
    <!-- CSRF TOKEN -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Title -->
    <title>State of the Art VoIP phone system - Global Voice Direct</title>
    
    <!-- Favicon -->
    <link
      rel="apple-touch-icon"
      sizes="180x180"
      href="{{asset('/newtemplate/asset/img/favicon/apple-touch-icon.png')}}"
    />
   
    <link
      rel="icon"
      sizes="32x32"
      href="{{asset('/newtemplate/asset/img/favicon/favicon-32x32.png')}}"
    />
    <link
      rel="icon"
      sizes="16x16"
      href="{{asset('/newtemplate/asset/img/favicon/favicon-16x16.png')}}"
    />
    <link rel="manifest" href="{{ asset('/images/favicon/site.webmanifest') }}">

    <!-- -----------ajax---------- -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <!-- >\Animation Css -->
    <link rel="stylesheet" href="{{ asset('/new_design/vendor/animation/css/animate.min.css')}}" />
    <link rel="stylesheet" href="{{ asset('/new_design/vendor/animation/css/slick.min.css')}}" />
    <!-- >\Bootstrap Css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.css">
    <link rel="stylesheet" href="{{ asset('/new_design/vendor/bootstrap/css/bootstrap.min.css')}}">
    <!-- >\FontAwesome Css -->
    <link rel="stylesheet" href="{{ asset('/new_design/vendor/fonts/css/all.min.css')}}">
    <link rel="stylesheet" href="{{ asset('/new_design/vendor/fonts/css/fontawesome.min.css')}}">
    <!-- >\Custom Css -->
    <link rel="stylesheet" href="{{ asset('/new_design/css/style_new.css')}}">
    <link rel="stylesheet" href="{{ asset('/new_design/css/responsive.css')}}">

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

    <!-- Styles -->
    <!-- <link href="{{ asset('css/app.css') }}" rel="stylesheet"> -->
    @yield('css')
    <style>
        @media screen and (max-width: 500px) {
            .alphaeon-credit {
                display: block !important;
            }
        }
        
    </style>
</head>
<body>
    <!-- Header -->
    
    @yield('content')
    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="row">
                <div id="bunch_about_us-3" class="col-lg-3 col-md-6 col-sm-12 footer-column footer-widget widget_bunch_about_us">       
                    <div class="logo-widget">
                        <div class="logo">
                            <a href="#"><img src="{{ asset('/new_design/img/logo/gvdlogo.png')}}" alt="GLobal Voice Direct"></a>
                        </div>
                        <div class="text">We are one of the largest wholesale voice termination suppliers in the world,
                                        with over 33 billion minutes terminated per year.
                        </div>
                        <ul class="social-nav">
                            <li class="follow">Follow Us</li>
                            <li><a href="https://globalvoicedirect.leaddyno.com/sharing_widget/share?channel=facebook" target="_blank"><i class="fab fa-facebook"></i></a></li>
                            <li><a href="https://globalvoicedirect.leaddyno.com/sharing_widget/share?channel=twitter" target="_blank"><i class="fab fa-twitter"></i></a></li>
                            <li><a href="https://globalvoicedirect.leaddyno.com/sharing_widget/share?channel=mail" target="_blank"><i class="fa fa-envelope"></i></a></li>
                            <li><a href="https://globalvoicedirect.leaddyno.com/sharing_widget/share?channel=pinterest" target="_blank"><i class="fab fa-pinterest"></i></a></li>
                        <ul>
                    </div>
                </div>
                <div id="nav_menu-3" class="col-lg-3 col-md-6 col-sm-12 footer-column footer-widget widget_nav_menu">
                    <h2>CUSTOMER SERVICE</h2>
                    <div class="menu-useful-links-container">
                        <ul id="menu-useful-links" class="menu">
                            <li id="menu-item-469" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-469">
                                <a href="/contact-us">Contact Us</a>
                            </li>
                            <li id="menu-item-468" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-468">
                                <a href="/our-offices">Our Offices</a>
                            </li>
                            <li id="menu-item-470" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-470">
                                <a href="/store">Voip Phones</a>
                            </li>
                            <li id="menu-item-471" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-471">
                                <a href="/howitworks">HowIt Works</a>
                            </li>
                            <li id="menu-item-471" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-471">
                                <a href="/privacy-policy">Privacy Policy</a>
                            </li>
                            <li id="menu-item-471" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-471">
                                <a href="/refund-policy">Refund Policy</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div id="nav_menu-2" class="col-lg-3 col-md-6 col-sm-12 footer-column footer-widget widget_nav_menu">
                    <h2>INFORMATION</h2>
                    <div class="menu-company-container">
                        <ul id="menu-company" class="menu">
                            <li id="menu-item-474" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-474">
                                <a href="/about">About Us</a>
                            </li>
                            <li id="menu-item-475" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-475">
                                <a href="/tos">Terms of Service</a>
                            </li>
                            <li id="menu-item-476" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-476">
                                <a href="/cartItems">View Cart</a>
                            </li>
                            <li id="menu-item-477" class="menu-item menu-item-type-custom menu-item-object-custom menu-item-477">
                                <a href="/package-pricing">Join Now</a>
                            </li>
                        </ul>
                        <a class="comodo-secure" href="javascript:if(window.open('https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=W&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5','tl_wnd_credentials'+(new Date()).getTime(),'toolbar=0,scrollbars=1,location=1,status=1,menubar=1,resizable=1,width=374,height=660,left=60,top=120')){};tLlB(tLTB);" onmouseover="tLeB(event,'https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=C&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5','tl_popupSC5')" onmousemove="tLXB(event)" onmouseout="tLTC('tl_popupSC5')" ondragstart="return false;"><img src="{{ asset('new_design/img/footer/combo.png')}}" alt="combo" border="0" onmousedown="return tLKB(event,'https://secure.comodo.com/ttb_searcher/trustlogo?v_querytype=W&amp;v_shortname=SC5&amp;v_search=https://globalvoicedirect.com/&amp;x=6&amp;y=5');" oncontextmenu="return tLLB(event);"></a>
                        <a class="comodo-secure" href="https://ssl.comodo.com/ev-ssl-certificates.php" id="comodoTL">EV SSL</a>
                    </div>
                </div>
                <div id="bunch_contact_info-3" class="col-lg-3 col-md-6 col-sm-12 footer-column footer-widget widget_bunch_contact_info">        
                    <div class="info-widget">
                        <h2>MAIN OFFICE</h2>            
                        <div class="text">2211, 2nd Avenue North, Suite 106 Lake Worth, FL 33461.</div>
                        <div class="cell">Call: <a href="#">+877 308 5803</a></div>
                        <div class="mail"><a href="#">support@globalvoicedirect.com</a></div>
                    </div>
                </div>  
            </div>
        </div>
    </footer>
    <section class="footer-bottom">
        <div class="container">
            <div class="container"></div>
            <div class="copyright">© 2010 - {{date("Y")}} GlobalVoiceDirect.com, All Rights Reserved.</div>
        </div>  
    </section>
    <!-- trigger -->
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
    <!-- Jquery -->
    <script src="{{ asset('/new_design/vendor/jquery/jquery.min.js')}}"></script>
    <!-- Animation Js -->
    <script src="{{ asset('/new_design/vendor/animation/js/slick.min.js')}}"></script>
    <!-- Bootstrap Js -->
    <script src="{{ asset('/new_design/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
    <!-- FontAwesome Js -->
    @unless(View::hasSection('gvd_fa_css_only'))
    <script src="{{ asset('/new_design/vendor/fonts/js/all.min.js')}}"></script>
    @endunless
    <script src="{{ asset('/new_design/vendor/fonts/js/fontawesome.min.js')}}"></script>
    <!-- Custom Js -->
    <script src="{{ asset('/new_design/js/custom.js')}}"></script>

    <script
      src="https://code.jquery.com/ui/1.12.0/jquery-ui.min.js"
      integrity="sha256-eGE6blurk5sHj+rmkfsGYeKyZx3M4bG+ZlFyA7Kns7E="
      crossorigin="anonymous"></script>
     
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script src="{{ asset('/js/owl.carousel.min.js') }}"></script>
    <script src="{{ asset('/js/imagezoom.js') }}"></script>
    <script src="{{ asset('/js/custom.js') }}"></script>
    <script src='https://www.google.com/recaptcha/api.js'></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.js"></script>
    <script src="https://kit.fontawesome.com/3c5aaf0254.js"></script>

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

            $('.animate').scroll({
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