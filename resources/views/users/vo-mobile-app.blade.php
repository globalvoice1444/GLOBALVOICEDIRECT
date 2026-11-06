@extends('layouts.user.user_main')
@section('content')
<style>
  .ma-wrap{max-width:1100px}
  .ma-hero{display:flex;gap:18px;align-items:center;background:linear-gradient(135deg,#0b2545,#13315c);color:#fff;border-radius:16px;padding:22px 24px;margin-bottom:18px}
  .ma-hero .ic{width:56px;height:56px;border-radius:14px;background:#f59e0b;color:#0b2545;display:flex;align-items:center;justify-content:center;font-size:26px;flex:none}
  .ma-hero h2{margin:0 0 4px;font-size:22px;font-weight:700;color:#fff}.ma-hero p{margin:0;color:#cbd5e1}
  .ma-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px}
  .ma-card{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px;display:flex;flex-direction:column;align-items:center;text-align:center;gap:14px}
  .ma-card h3{margin:0;font-size:15px;font-weight:600;color:#0f172a}
  .ma-qr{width:168px;height:168px;padding:8px;border:1px solid #e2e8f0;border-radius:12px;background:#fff}
  .ma-qr img{width:100%;height:100%;display:block}
  .ma-badge{display:inline-flex;align-items:center;gap:10px;background:#000;color:#fff!important;border-radius:10px;padding:8px 16px;text-decoration:none!important;min-width:180px;border:1px solid #333}
  .ma-badge:hover{background:#1f2937}
  .ma-badge svg{width:26px;height:26px;flex:none}
  .ma-badge span{display:flex;flex-direction:column;text-align:left;line-height:1.1}.ma-badge small{font-size:10.5px;opacity:.85}.ma-badge b{font-size:18px;font-weight:600}
  .ma-muted{font-size:12.5px;color:#64748b;margin:0}
  .ma-soon{margin-top:16px;display:flex;gap:14px;align-items:center;background:#fffbeb;border:1px solid #fde68a;border-radius:14px;padding:16px 18px}
  .ma-soon .ic{width:40px;height:40px;border-radius:10px;background:#fef3c7;color:#b45309;display:flex;align-items:center;justify-content:center;font-size:20px;flex:none}
  .ma-soon b{display:block;color:#0f172a}.ma-soon p{margin:2px 0 0;font-size:13px;color:#475569}
  .ma-desk{margin-top:16px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px 20px;display:flex;flex-wrap:wrap;gap:10px;align-items:center}
  .ma-desk b{margin-right:8px;color:#0f172a}.ma-desk a{border:1px solid #cbd5e1;border-radius:9px;padding:7px 14px;color:#0b2545!important;font-weight:600;text-decoration:none!important}
  .ma-sign{margin-top:16px;background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:20px 22px}
  .ma-sign h3{margin:0 0 4px;font-size:16px;font-weight:700;color:#0f172a}.ma-sign>p{margin:0 0 14px;color:#64748b;font-size:13px}
  .ma-ext{display:flex;flex-wrap:wrap;gap:10px;align-items:center;margin-bottom:14px}
  .ma-ext select{min-width:240px;max-width:100%;height:40px;border:1px solid #cbd5e1;border-radius:9px;padding:0 10px;background:#fff}
  .ma-pill{display:inline-block;font-size:12px;font-weight:600;border-radius:999px;padding:4px 10px}
  .ma-pill.ok{background:#dcfce7;color:#166534}.ma-pill.no{background:#fee2e2;color:#991b1b}
  .ma-btn{border:0;border-radius:9px;padding:9px 16px;font-weight:600;cursor:pointer;background:#0b2545;color:#fff}
  .ma-btn.alt{background:#fff;color:#0b2545;border:1px solid #0b2545}.ma-btn:disabled{opacity:.5;cursor:not-allowed}
  .ma-out{display:flex;flex-wrap:wrap;gap:22px;align-items:flex-start;margin-top:6px}
  .ma-out .ma-qr{width:220px;height:220px}
  .ma-cred{flex:1;min-width:240px}.ma-cred table{width:100%;border-collapse:collapse}.ma-cred td{padding:8px 6px;border-bottom:1px solid #f1f5f9;font-size:13.5px;vertical-align:middle}
  .ma-cred td:first-child{color:#64748b;white-space:nowrap;width:42%}.ma-cred code{background:#f8fafc;border-radius:6px;padding:2px 6px;color:#0f172a;word-break:break-all}
  .ma-ic{border:1px solid #e2e8f0;background:#fff;border-radius:7px;padding:3px 7px;margin-left:4px;cursor:pointer;font-size:13px}
  .ma-warn{background:#fffbeb;border:1px solid #fde68a;border-radius:10px;padding:10px 14px;font-size:13px;color:#92400e;margin-bottom:12px}
  .ma-warn a{color:#b45309;font-weight:700}
</style>
<div class="ma-wrap">
  <div class="ma-hero"><div class="ic"><i class="feather icon-smartphone"></i></div>
    <div><h2>Global Voice Direct Mobile</h2><p>Make and take calls on your business number anywhere, right from your phone.</p></div></div>
  <div class="ma-grid">
    <div class="ma-card"><h3>iPhone and iPad</h3>
      <div class="ma-qr"><img data-qr="{{ $d['ios'] ?? '' }}" src="/img/gvd-app/qr-ios.svg" alt="QR code to download from the App Store"></div>
      <p class="ma-muted">Scan with your phone's camera</p>
      <a class="ma-badge" href="{{ $d['ios'] ?? 'https://apps.apple.com/us/app/mobyx/id924542684' }}" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M16.4 12.6c0-2.5 2-3.7 2.1-3.8-1.2-1.7-3-1.9-3.6-2-1.5-.2-3 .9-3.8.9-.8 0-2-.9-3.3-.9-1.7 0-3.3 1-4.2 2.5-1.8 3.1-.5 7.7 1.3 10.2.9 1.2 1.9 2.6 3.2 2.6 1.3-.1 1.8-.8 3.3-.8 1.5 0 2 .8 3.3.8 1.4 0 2.3-1.2 3.1-2.5 1-1.4 1.4-2.8 1.4-2.9 0 0-2.8-1.1-2.8-4.1zM14 5.1c.7-.9 1.2-2 1-3.2-1 0-2.3.7-3 1.6-.7.8-1.2 2-1.1 3.1 1.2.1 2.3-.6 3.1-1.5z"/></svg>
        <span><small>Download on the</small><b>App Store</b></span></a></div>
    <div class="ma-card"><h3>Android</h3>
      <div class="ma-qr"><img data-qr="{{ $d['android'] ?? '' }}" src="/img/gvd-app/qr-android.svg" alt="QR code to download from Google Play"></div>
      <p class="ma-muted">Scan with your phone's camera</p>
      <a class="ma-badge" href="{{ $d['android'] ?? 'https://play.google.com/store/apps/details?id=net.mobyx.android' }}" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#34a853" d="M3.6 1.8 13.5 12l-9.9 10.2c-.4-.2-.6-.6-.6-1.1V2.9c0-.5.2-.9.6-1.1z"/><path fill="#fbbc04" d="m17 15.5-3.5-3.5L17 8.5l3.9 2.2c1.1.6 1.1 2 0 2.6z"/><path fill="#ea4335" d="M13.5 12 17 15.5 5 22.4c-.5.3-1 .2-1.4-.2z"/><path fill="#4285f4" d="M3.6 1.8c.4-.4.9-.5 1.4-.2L17 8.5 13.5 12z"/></svg>
        <span><small>GET IT ON</small><b>Google Play</b></span></a></div>
  </div>
  @if (!empty($d['win']) || !empty($d['mac']) || !empty($d['lin']))
  <div class="ma-desk"><b>Desktop app</b>
    @if (!empty($d['win']))<a href="{{ $d['win'] }}" target="_blank" rel="noopener">Windows</a>@endif
    @if (!empty($d['mac']))<a href="{{ $d['mac'] }}" target="_blank" rel="noopener">Mac</a>@endif
    @if (!empty($d['lin']))<a href="{{ $d['lin'] }}" target="_blank" rel="noopener">Linux</a>@endif
  </div>
  @endif
  @if ($admin && !empty($d['signin']))
  <div class="ma-sign" id="ma-sign">
    <h3>Sign in to the app</h3>
    <p>Open the app, tap <b>Scan QR</b>, and scan the code for the extension. Each extension can be signed in on one phone.</p>
    @if (empty($d['rng']))<div class="ma-warn">{{ 'Turn on Ring In/Out to use the mobile app.' }} <a href="/user/extras">Go to Extras</a></div>@endif
    @if (!empty($d['exts']))
    <div class="ma-ext"><select id="ma-sel" aria-label="Extension">
      @foreach ($d['exts'] as $e)<option value="{{ $e['id'] }}" data-ok="{{ $e['ok'] ? 1 : 0 }}" data-why="{{ $e['why'] }}">{{ $e['label'] }}</option>@endforeach
      </select><span class="ma-pill" id="ma-st"></span>
      <button type="button" class="ma-btn" id="ma-show">Show QR</button>
      <button type="button" class="ma-btn alt" id="ma-mail">Email setup to user</button></div>
    <div id="ma-why" class="ma-warn" style="display:none"></div>
    <div id="ma-res"></div>
    @else <p class="ma-muted">No extensions found.</p> @endif
  </div>
  @elseif ($error)
  <div class="ma-soon"><div class="ic"><i class="feather icon-alert-circle"></i></div><div><b>Sign-in details</b><p>{{ $error }}</p></div></div>
  @else
  <div class="ma-soon"><div class="ic"><i class="feather icon-maximize"></i></div>
    <div><b>Need your sign-in QR?</b><p>Your company administrator can show it from this page.</p></div></div>
  @endif
</div>
<script>window.GVD_MA = {!! json_encode(['admin' => (bool) $admin, 'demo' => (bool) ($demo ?? false)]) !!};</script>
<script src="/js/vendor/gvd-qrcode.js?v=1"></script>
<script src="/js/gvd-mobile-app.js?v=1"></script>
@endsection
