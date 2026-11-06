<!doctype html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Email preferences · Global Voice Direct</title>
<style>body{margin:0;background:#F4F6F9;font-family:Inter,system-ui,Segoe UI,sans-serif;color:#0F1B26}.c{max-width:460px;margin:12vh auto;background:#fff;border:1px solid #E6EAF0;border-radius:16px;padding:32px;box-shadow:0 10px 30px -18px rgba(15,27,38,.3)}h1{font-size:22px;margin:0 0 10px}p{color:#64748B;line-height:1.6}button{background:#FF5000;color:#fff;border:0;border-radius:10px;padding:12px 22px;font-weight:600;font-size:15px;cursor:pointer}.b{height:6px;background:#1E2A36;border-radius:16px 16px 0 0;margin:-32px -32px 24px}</style></head>
<body><div class="c"><div class="b"></div>
@if($done)
 <h1>You're unsubscribed</h1><p>{{ $email }} won't get any more onboarding or marketing emails from Global Voice Direct. Account and billing notices still go out.</p>
@else
 <h1>Unsubscribe?</h1><p>Stop onboarding and marketing emails to <b>{{ $email }}</b>.</p>
 <form method="post" action="{{ $action }}">@csrf<button>Unsubscribe</button></form>
@endif
<p style="font-size:13px;margin-top:22px">Questions? support@globalvoicedirect.com · (877) 308-5803</p></div></body></html>
