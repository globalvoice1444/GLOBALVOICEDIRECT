<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>Admin Sign In | Global Voice Direct</title>
<link rel="icon" href="{{ asset('images/favicon/favicon.ico') }}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-137383409-1"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'UA-137383409-1');
    </script>
<style>
*{box-sizing:border-box}html,body{margin:0;min-height:100%}
body{font-family:Inter,system-ui,-apple-system,"Segoe UI",sans-serif;background:#F4F6F9;color:#0F1B26;-webkit-font-smoothing:antialiased;display:grid;grid-template-columns:1.1fr 1fr;min-height:100vh}
.side{position:relative;overflow:hidden;color:#fff;padding:48px 56px;display:flex;flex-direction:column;justify-content:space-between;background:radial-gradient(120% 120% at 100% 0%,rgba(255,80,0,.28) 0%,rgba(255,80,0,0) 45%),linear-gradient(135deg,#1E2A36 0%,#24384a 55%,#16202a 100%)}
.side:before{content:"";position:absolute;inset:0;background-image:linear-gradient(rgba(255,255,255,.045) 1px,transparent 1px),linear-gradient(90deg,rgba(255,255,255,.045) 1px,transparent 1px);background-size:32px 32px;mask-image:radial-gradient(circle at 70% 30%,#000,transparent 75%)}
.side>*{position:relative}.logo img{height:46px;filter:brightness(0) invert(1)}
.eb{font-size:11px;font-weight:700;letter-spacing:.18em;text-transform:uppercase;color:#FF8A3D}
.side h1{font-size:44px;line-height:1.08;letter-spacing:-.03em;font-weight:800;margin:14px 0 14px;max-width:520px}
.side h1 span{background:linear-gradient(90deg,#FF5000,#FF8A3D);-webkit-background-clip:text;background-clip:text;color:transparent}
.side p{color:rgba(255,255,255,.72);font-size:16px;line-height:1.6;max-width:460px;margin:0}
.chips{display:flex;flex-wrap:wrap;gap:10px;margin-top:28px}.chips span{font-size:13px;font-weight:600;padding:8px 14px;border-radius:999px;background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.16)}
.foot{font-size:12.5px;color:rgba(255,255,255,.5)}
.main{display:flex;align-items:center;justify-content:center;padding:40px 24px}
.card{width:100%;max-width:420px;background:#fff;border:1px solid #E6EAF0;border-radius:22px;padding:38px 34px;box-shadow:0 1px 2px rgba(15,27,38,.04),0 30px 70px -36px rgba(15,27,38,.35)}
.ico{width:46px;height:46px;border-radius:13px;display:grid;place-items:center;background:linear-gradient(180deg,rgba(255,80,0,.14),rgba(255,80,0,.04));border:1px solid rgba(255,80,0,.24);color:#E04800;margin-bottom:20px}
.card h2{font-size:24px;font-weight:800;letter-spacing:-.02em;margin:0}.card .s{color:#64748B;font-size:14px;margin:6px 0 24px}
label{display:block;font-size:13px;font-weight:600;color:#334155;margin:0 0 7px}
.f{margin-bottom:16px}.f input{width:100%;height:48px;border:1px solid #D9DFE7;border-radius:12px;padding:0 14px;font:500 15px Inter,system-ui,sans-serif;color:#0F1B26;outline:0;transition:.15s}
.f input:focus{border-color:#FF5000;box-shadow:0 0 0 4px rgba(255,80,0,.13)}
.row{display:flex;justify-content:flex-end;margin:-4px 0 20px}.row a{font-size:13px;font-weight:600;color:#C2410C;text-decoration:none}.row a:hover{text-decoration:underline}
button{width:100%;height:50px;border:0;border-radius:12px;font:700 15px Inter,system-ui,sans-serif;color:#fff;cursor:pointer;background:linear-gradient(180deg,#FF6A1F,#FF5000);box-shadow:0 12px 26px -12px rgba(255,80,0,.8);transition:transform .12s}
button:hover{transform:translateY(-1px)}button[disabled]{opacity:.75;cursor:wait;transform:none}
.msg{border-radius:12px;padding:12px 14px;font-size:13.5px;margin-bottom:18px;background:#F1F8FB;border:1px solid #CFE7F0;color:#1F6F87}
.sec{margin-top:22px;font-size:12px;color:#94A3B8;text-align:center}
@media(max-width:900px){body{grid-template-columns:1fr}.side{padding:28px 24px}.side h1{font-size:28px}.chips,.foot,.side p{display:none}.main{padding:24px 16px 40px}.card{padding:28px 22px}}
</style>
</head>
<body>
<aside class="side">
 <a class="logo" href="/"><img src="{{ asset('admin/assets/images/gvdlogo.png') }}" alt="Global Voice Direct"></a>
 <div><div class="eb">Admin Command Center</div><h1>Run the business <span>with clarity.</span></h1><p>Orders, customers, script requests, listings and alerts &mdash; one secure workspace for the Global Voice Direct team.</p>
 <div class="chips"><span>Orders</span><span>Customers</span><span>Script requests</span><span>Alerts</span></div></div>
 <div class="foot">&copy; {{ date('Y') }} Global Voice Direct. Authorized personnel only.</div>
</aside>
<main class="main">
 <div class="card">
  <div class="ico"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></div>
  <h2>Admin sign in</h2><div class="s">Welcome back. Sign in to your admin account.</div>
  @if(session()->has('message'))<div class="msg">{{ session()->get('message') }}</div>@endif
  <form method="post" action="/admin/login" id="gvdAdmLogin">
   <input type="hidden" name="_token" value="{{ csrf_token() }}">
   <div class="f"><label for="u">Username</label><input id="u" type="text" name="user" autocomplete="username" required autofocus></div>
   <div class="f"><label for="p">Password</label><input id="p" type="password" name="pass" autocomplete="current-password" required></div>
   <div class="row"><a href="/adminforgotpasswod">Forgot password?</a></div>
   <button type="submit" name="login" value="1">Sign in</button>
  </form>
  <div class="sec">Protected by encrypted connection</div>
 </div>
</main>
<script>document.getElementById('gvdAdmLogin').addEventListener('submit',function(e){var b=this.querySelector('button');if(b.disabled){e.preventDefault();return}setTimeout(function(){b.disabled=true;b.textContent='Signing in\u2026'},0)});</script>
</body>
</html>
