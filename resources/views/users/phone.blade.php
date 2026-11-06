@extends('layouts.user.user_main')
@section('content')
<style>
  /* page-load bar must not track the phone's WebSocket/fetch (it never finishes and blocks clicks) */
  .pace, .pace .pace-progress, .pace .pace-activity { display:none !important; pointer-events:none !important; }
  .ph-head{display:flex;justify-content:space-between;align-items:flex-end;gap:16px;margin-bottom:16px}
  .ph-head h1{font-size:22px;margin:0 0 4px;font-weight:650}
  .ph-grid{display:grid;grid-template-columns:400px 1fr;gap:16px;align-items:start}
  @media (max-width:1000px){.ph-grid{grid-template-columns:1fr}}
  .ph-card{background:#fff;border:1px solid var(--gp-line);border-radius:10px;padding:18px}
  .ph-card h3{font-size:14px;font-weight:600;margin:0 0 12px}
  .ph-lbl{display:block;font-size:12.5px;color:var(--gp-muted);font-weight:500;margin-bottom:5px}
  .gp-content .ph-card select.ph-sel{width:100%;padding:9px 10px}
  .ph-status{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:99px;font-size:12px;background:#f1f5f9;color:#475569;font-weight:500}
  .ph-status:before{content:"";width:7px;height:7px;border-radius:50%;background:#94a3b8}
  .ph-status.ok{background:#dcfce7;color:#166534}.ph-status.ok:before{background:#16a34a}
  .ph-status.bad{background:#fef2f2;color:#b91c1c}.ph-status.bad:before{background:#dc2626}
  .ph-status.busy{background:#eef2ff;color:#3730a3}.ph-status.busy:before{background:#4f46e5;animation:phpulse 1.2s infinite}
  @keyframes phpulse{50%{opacity:.3}}
  .ph-num-wrap{position:relative;margin:16px 0 6px}
  .gp-content input.ph-num{width:100%;border:0!important;border-bottom:1px solid var(--gp-line)!important;border-radius:0!important;text-align:center;font-size:22px!important;letter-spacing:.06em;padding:10px 36px!important;outline:none!important}
  .ph-bs{position:absolute;right:2px;top:50%;transform:translateY(-50%);border:0;background:none;color:var(--gp-muted);font-size:18px;cursor:pointer;padding:4px 8px;border-radius:6px}
  .ph-bs:hover{background:#f1f5f9;color:var(--gp-text)}
  .gp-content .ph-pad{display:grid;grid-template-columns:repeat(3,64px);gap:14px 30px;justify-content:center;margin:16px 0 18px}
  .gp-content .ph-pad button{width:64px!important;height:64px!important;border-radius:50%!important;border:1px solid var(--gp-line)!important;background:#f8fafc!important;color:var(--gp-text)!important;font-size:22px!important;font-weight:500;padding:0!important;display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1;transition:background .1s,transform .05s}
  .gp-content .ph-pad button:hover{background:#eef2ff!important;border-color:#c7d2fe!important}
  .gp-content .ph-pad button:active{transform:scale(.94);background:#e0e7ff!important}
  .ph-pad button small{font-size:9px;letter-spacing:.12em;color:var(--gp-muted);margin-top:3px;font-weight:500;min-height:10px}
  .gp-content .ph-call{width:100%;padding:12px!important;font-size:15px!important;border-radius:10px!important;background:#16a34a!important;border-color:#16a34a!important;color:#fff!important}
  .gp-content .ph-call:hover:not(:disabled){background:#15803d!important}
  .gp-content .ph-call:disabled{background:#86d6a3!important;border-color:#86d6a3!important;opacity:1;cursor:not-allowed}
  .gp-content .ph-answer{width:100%;padding:12px!important;font-size:15px!important;border-radius:10px!important;background:#16a34a!important;border-color:#16a34a!important;margin-bottom:8px;animation:phpulse 1.2s infinite}
  .ph-live{display:none;text-align:center;padding:18px 12px;margin:14px 0 4px;border-radius:12px;background:linear-gradient(135deg,#eef2ff,#f8fafc);border:1px solid #e0e7ff}
  .ph-live.on{display:block}
  .ph-live .who{font-size:22px;font-weight:600;letter-spacing:.03em}
  .ph-live .stt{font-size:13px;color:#3730a3;margin-top:4px}
  .ph-live .tm{font-family:ui-monospace,Menlo,monospace;font-size:14px;margin-top:6px;color:var(--gp-text)}
  .ph-ctl{display:none;grid-template-columns:repeat(4,1fr);gap:10px;margin:14px 0 6px}
  .ph-ctl.on{display:grid}
  .gp-content .ph-ctl button{display:flex!important;flex-direction:column;align-items:center;gap:4px;padding:10px 4px!important;border-radius:10px!important;font-size:12px!important;background:#fff!important;border:1px solid var(--gp-line)!important;color:var(--gp-text)!important}
  .gp-content .ph-ctl button i{font-size:17px}
  .gp-content .ph-ctl button.act{background:#eef2ff!important;border-color:#c7d2fe!important;color:#3730a3!important}
  .gp-content .ph-ctl button:disabled{opacity:.45;cursor:not-allowed}
  .gp-content .ph-hang{width:100%;padding:12px!important;font-size:15px!important;border-radius:10px!important;background:#dc2626!important;border-color:#dc2626!important;color:#fff!important}
  .gp-content .ph-hang:hover{background:#b91c1c!important}
  #gp-state.state{display:inline-flex;align-items:center;gap:6px;padding:3px 10px;border-radius:99px;font-size:12px;background:#f1f5f9;color:#475569;font-weight:500}
  #gp-state.state.ok{background:#dcfce7;color:#166534}#gp-state.state.bad{background:#fef2f2;color:#b91c1c}
  .ph-msg{font-size:12.5px;color:var(--gp-muted);margin-top:8px;min-height:16px}
  .ph-info li{display:flex;gap:10px;padding:9px 0;border-bottom:1px solid #f1f5f9;font-size:13px;color:#334155}
  .ph-info li:last-child{border-bottom:0}.ph-info i{color:var(--gp-brand);font-size:16px;margin-top:1px}
  .ph-info{list-style:none;margin:0;padding:0}
</style>
<div class="ph-head">
  <div><h1>Phone</h1><div class="muted">Make and take calls on your extension right in your browser. Use a headset for the best sound.</div></div>
  @if ($enabled)<div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap"><span class="ph-status" id="ph-bal" style="display:none;background:#eef2ff;color:#1e3a8a"></span><span class="ph-status" id="ph-pill">Not connected</span> <a href="/user/webrtc" class="muted small">WebRTC settings</a></div>@endif
</div>
@if (!$enabled)
  <div class="ph-card"><p class="muted" style="margin:0">The browser phone is not enabled for your account yet. Contact Global Voice Direct support to turn it on.</p></div>
@else
<div class="ph-grid">
  <div class="ph-card">
    <div id="gp-load" class="muted"><span class="vo-spin"></span>Loading your extensions…</div>
    <div id="gp-phone" style="display:none">
      <label class="ph-lbl" for="gp-ext">Extension</label>
      <select class="form-control ph-sel" id="gp-ext"></select>
      <div style="margin-top:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap">
        <span id="gp-state" class="state">Off</span> <span id="gp-msg" class="muted small"></span> <a href="#" id="gp-retry" style="display:none">Retry</a>
      </div>
      <div class="ph-live" id="ph-live"><div class="who" id="ph-who"></div><div class="stt" id="ph-st"></div><div class="tm" id="ph-tm"></div></div>
      <div class="ph-num-wrap"><input class="form-control ph-num" id="gp-num" placeholder="Enter a number or extension" inputmode="tel" autocomplete="off"><button type="button" class="ph-bs" id="ph-bs" title="Delete last digit" aria-label="Delete last digit">&#9003;</button></div>
      <div class="ph-pad" id="gp-pad"></div>
      <button class="btn btn-primary ph-answer" id="gp-answer" style="display:none"><i class="feather icon-phone-incoming"></i> Answer</button>
      <button class="btn ph-call" id="gp-call" disabled><i class="feather icon-phone"></i> Call</button>
      <div class="ph-ctl" id="ph-ctl">
        <button type="button" class="btn" id="gp-mute" style="display:none"><i class="feather icon-mic-off"></i><span>Mute</span></button>
        <button type="button" class="btn" disabled title="Hold isn't available in the browser phone yet"><i class="feather icon-pause"></i><span>Hold</span></button>
        <button type="button" class="btn" id="ph-kp"><i class="feather icon-grid"></i><span>Keypad</span></button>
        <button type="button" class="btn" disabled title="Transfer isn't available in the browser phone yet"><i class="feather icon-share"></i><span>Transfer</span></button>
      </div>
      <button class="btn ph-hang" id="gp-hang" style="display:none"><i class="feather icon-phone-off"></i> Hang up</button>
      <div id="gp-errors" class="ph-msg"></div>
      <audio id="gp-audio" autoplay></audio>
    </div>
  </div>
  <div style="display:flex;flex-direction:column;gap:16px">
    <div class="ph-card">
      <h3>How it works</h3>
      <ul class="ph-info">
        <li><i class="feather icon-users"></i><span>Choose one of your extensions. The phone connects and shows <b>Ready</b>.</span></li>
        <li><i class="feather icon-grid"></i><span>Type or tap a number, or a 2–6 digit extension, then press <b>Call</b>.</span></li>
        <li><i class="feather icon-phone-incoming"></i><span>Incoming calls to that extension ring here. Press <b>Answer</b>.</span></li>
        <li><i class="feather icon-mic"></i><span>During a call you can mute, and use the keypad for menus.</span></li>
        <li><i class="feather icon-headphones"></i><span>Allow microphone access when your browser asks. A headset gives the best sound.</span></li>
      </ul>
    </div>
    <div class="ph-card">
      <div style="display:flex;justify-content:space-between;align-items:center"><h3 style="margin:0">Recent calls</h3><a href="/user/calls/inbound">Call history →</a></div>
      <p class="muted" style="margin:10px 0 0;font-size:13px">See inbound, outbound and recorded calls in Call history.</p>
    </div>
  </div>
</div>
@endif
@endsection
@if ($enabled)
@push('scripts')
<script>(function(){fetch('/user/phone/balance',{credentials:'same-origin',cache:'no-store',headers:{Accept:'application/json'}}).then(function(r){return r.ok?r.json():null}).then(function(j){var b=document.getElementById('ph-bal');if(!b||!j||j.balance===null||j.balance===undefined)return;b.textContent='Balance: '+j.balance+(j.currency?' '+j.currency:'');b.style.display='';},function(){});})();</script>
@endpush
@endif
@if ($enabled && !empty($demo))
@push('scripts')
<script src="/js/gvd-phone-demo.js?v=1"></script>
@endpush
@elseif ($enabled)
@push('scripts')
<script src="/js/gvd-sip.min.js?v=0.21.2"></script>
<script src="/js/gvd-phone.js?v={{ @filemtime(public_path('js/gvd-phone.js')) }}"></script>
<script src="/js/gvd-phone-ui.js?v={{ @filemtime(public_path('js/gvd-phone-ui.js')) }}"></script>
@endpush
@endif
