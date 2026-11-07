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
      <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
      <link rel="stylesheet" href="/css/gvd-panel.css?v=5">
      <script>document.addEventListener("DOMContentLoaded",function(){var s=document.querySelector(".gp-side");if(s)s.scrollTop=0;});</script>
      @yield('css')
   </head>
  <body class="gp">
   @php
      $gvdVoF = \App\Portal\VoMenu::flags();
      $gpPath = Request::path();
      $gpOn = function (...$p) use ($gpPath) { foreach ($p as $x) if ($gpPath === $x || str_starts_with($gpPath, $x . '/')) return 'on'; return ''; };
      $gpNav = [
        'Calls' => [
          ['/user/home', 'Dashboard', 'icon-home', ['user/home']],
          ['/user/phone', 'Phone', 'icon-phone', ['user/phone']],
          ['/user/webrtc', 'WebRTC', 'icon-headphones', ['user/webrtc']],
          ['/user/mobile-app', 'Mobile app', 'icon-smartphone', ['user/mobile-app']],
          ['/user/calls/inbound', 'Call history', 'icon-list', ['user/calls']],
          ['/user/conference-rooms', 'Conference rooms', 'icon-mic', ['user/conference-rooms']],
          ['/user/call-block', 'Call block', 'icon-slash', ['user/call-block']],
        ],
        'Business' => [
          ['/user/fax', 'Fax', 'icon-printer', ['user/fax']],
          ['/user/greetings', 'Greetings', 'icon-volume-2', ['user/greetings']],
          ['/user/phone-scripts', 'Phone scripts', 'icon-file-text', ['user/phone-scripts']],
          ['/user/marketing-form', 'Marketing form', 'icon-edit-2', ['user/marketing-form']],
          ['/user/business-listings', 'Business listings', 'icon-map-pin', ['user/business-listings']],
          ['/user/audio-campaigns', 'Audio campaign', 'icon-radio', ['user/audio-campaigns']],
          ['/user/sms-campaigns', 'SMS campaign', 'icon-message-square', ['user/sms-campaigns']],
          ['/user/email-campaigns', 'Email campaign', 'icon-mail', ['user/email-campaigns', 'emailCampaign', 'send_email', 'ot_editor_sendEmail']],
          ...(\App\Support\AiConnect::visibleTo((int) session('user_id')) ? [['/user/ai-connect', 'AI Connect', 'icon-cpu', ['user/ai-connect']]] : []), // 2026-11-02 mcp
          ...(\App\Services\VoiceAccountProvisioner::visibleTo((int) session('user_id')) ? [['/user/ai-connect/voice-bot', 'Voice bot', 'icon-radio', ['user/ai-connect/voice-bot']]] : []),
        ],
        'Account' => [
          ['/user/profile', 'Profile', 'icon-user', ['user/profile']],
          // hidden 2026-11-01 (owner; route kept): ['/user/order', 'Orders', 'icon-package', ['user/order', 'user/view_order_detail', 'user/unpaid_order']],
          // hidden 2026-11-01 (owner; route kept): ['/payment', 'Payments', 'icon-layers', ['payment']],
          // hidden 2026-11-01 (owner; route kept): ['/user/billPay', 'Bill pay', 'icon-credit-card', ['user/billPay']],
          ['/user/credit-application', 'Credit application', 'icon-check-square', ['user/credit-application']],
          // hidden 2026-11-01 (owner; route kept): ['/user/referral_program', 'Refer a friend', 'icon-heart', ['user/referral_program']],
          // hidden 2026-11-01 (owner; route kept): ['/user/referral_dashboard', 'Referral dashboard', 'icon-bar-chart-2', ['user/referral_dashboard']],
          ['/user/howtovideo', 'How-to videos', 'icon-play-circle', ['user/howtovideo']],
        ],
        'Settings' => [
          ['/user/phone-numbers', 'Phone numbers', 'icon-hash', ['user/phone-numbers']],
          ['/user/extensions', 'Extensions', 'icon-users', ['user/extensions']],
          ['/user/users', 'Users', 'icon-user-check', ['user/users']],
          ['/user/extras', 'Extras', 'icon-plus-square', ['user/extras']],
        ],
      ];
      /* 2026-11-01 dw: always shown */ $gpNav['Settings'][] = ['/user/dial-out-numbers', 'Dial out numbers', 'icon-phone-outgoing', ['user/dial-out-numbers']];
      // Set aside (owner will add back later): Add Port is not in this menu; their routes and code are untouched.
      $gpTitle = 'Global Voice Direct';
      foreach ($gpNav as $gpItems) foreach ($gpItems as $gpI) if ($gpOn(...$gpI[3])) $gpTitle = $gpI[1];
      $gpCalls = [['/user/calls/inbound', 'Inbound', 'user/calls/inbound'], ['/user/calls/outbound', 'Outbound', 'user/calls/outbound']];
      if (!empty($gvdVoF['dialOut'])) $gpCalls[] = ['/user/calls/dial-out', 'Dial-out', 'user/calls/dial-out'];
      $gpCalls[] = ['/user/calls/recorded', 'Recorded calls', 'user/calls/recorded'];
      if (!empty($gvdVoF['confRecordings'])) $gpCalls[] = ['/user/calls/conference-recordings', 'Conference recordings', 'user/calls/conference-recordings'];
      $gpDockDemo = \App\Portal\Demo::on(); // 2026-11-01 dk docked phone
      $gpDock = $gpDockDemo || (bool) \App\Portal\WebPhone::enabledFor(\App\Fax\FaxInbox::tenantsForUser(session()->get('user_id')));
      $gpName = session('nice_name') ?: (session('display_name') ?: '');
   @endphp
   <div class="gp-shell" id="gp-shell">
      <aside class="gp-side">
         <a class="gp-brand" href="/user/home"><div class="gp-logo">GV</div><div><div class="gp-brand-name">Global Voice Direct</div><div class="gp-brand-sub">Voice &amp; Business Phone Console</div></div></a>
         <nav class="gp-nav main-menu">
         @foreach ($gpNav as $gpSec => $gpItems)
            <div class="gp-nav-title">{{ $gpSec }}</div>
            @foreach ($gpItems as $gpI)
               <a href="{{ $gpI[0] }}" class="{{ $gpOn(...$gpI[3]) }}"><i class="feather {{ $gpI[2] }}"></i><span>{{ $gpI[1] }}</span></a>
               @if ($gpI[1] === 'Call history' && $gpOn('user/calls'))
                  <div class="gp-sub">@foreach ($gpCalls as $gpC)<a href="{{ $gpC[0] }}" class="{{ $gpPath === $gpC[2] ? 'on' : '' }}">{{ $gpC[1] }}</a>@endforeach</div>
               @endif
            @endforeach
         @endforeach
         </nav>
      </aside>
      <div class="gp-main">
         <header class="gp-top">
            <div style="display:flex;align-items:center;gap:10px;min-width:0"><button type="button" class="gp-burger" onclick="document.getElementById('gp-shell').classList.toggle('gp-open')" aria-label="Menu">&#9776;</button><span class="gp-title">{{ $gpTitle }}</span></div>
            <div class="gp-user">
               @if ($gpDock)@if ($gpPath === 'user/phone')<a class="gp-dock-btn on" href="/user/phone" title="Phone" aria-label="Phone"><i class="feather icon-phone"></i></a>@else<button type="button" class="gp-dock-btn" id="gp-dock-btn" title="Phone" aria-label="Open phone"><i class="feather icon-phone"></i><span class="dot"></span></button>@endif @endif
               <div class="gp-avatar">{{ $gpName !== '' ? mb_substr($gpName, 0, 1) : 'G' }}</div>
               <div class="gp-uname">{{ $gpName }}</div>
               <a class="gp-signout" href="{{ url('user/logout') }}">Sign out</a>
            </div>
         </header>
         <ul class="breadcrumb gp-crumb"></ul>
         <main class="gp-content">
            @yield('content')
         </main>
         <footer class="gp-foot">&copy; {{ date('Y') }} Global Voice Direct. All rights reserved.</footer>
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
      {{-- pcoded.min.js removed 2026-10-31: the new shell has no pcoded menu --}}
      {{-- vertical-layout.min.js removed 2026-10-31 --}}
      <script src="{{ asset('user/bower_components/jquery.steps/js/jquery.steps.js')}}"></script>
       <script type="text/javascript" src="{{ asset('user/assets/pages/dashboard/custom-dashboard.min.js')}}"></script>
      <script type="text/javascript" src="{{ asset('user/assets/js/script.min.js')}}"></script>
      {{-- 2026-11-01y: 683 KB editor only on the pages that use it --}}
      @hasSection('needs_ckeditor')<script type="text/javascript" src="{{ asset('user/ckeditor/ckeditor.js')}}"></script>@endif
      <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
      <script src="{{ asset('user/js/jquery.imgareaselect.js')}}" type="text/javascript"></script>
      <script src="{{ asset('user/js/jquery.form.js')}}"></script>
      

      <script src="{{ asset('user/js/functions.js')}}"></script>
      @yield('scripts')
      @stack('scripts')
      @if ($gpDock && $gpPath !== 'user/phone') @include('users.partials.phone-dock') @endif
      @include('users.partials.help-bubble')
   </body>
</html>