{{-- Docked browser phone (2026-11-01 dk). Same element IDs as /user/phone so the existing engine (gvd-phone.js / gvd-phone-demo.js) drives it. Not rendered on /user/phone. --}}
<style>
  #gvd-dock{position:fixed;top:70px;right:24px;width:360px;max-width:calc(100vw - 24px);max-height:calc(100vh - 84px);background:#fff;border-radius:16px;box-shadow:0 18px 48px rgba(11,37,69,.28);z-index:1060;display:none;flex-direction:column;overflow:hidden;border:1px solid #e2e8f0;font-size:14px}
  #gvd-dock.open{display:flex}
  #gvd-dock .dk-head{background:linear-gradient(135deg,#0b2545,#13315c);color:#fff;padding:12px 14px;display:flex;align-items:center;gap:10px}
  #gvd-dock .dk-head .t{flex:1;min-width:0;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  #gvd-dock .dk-head button{background:rgba(255,255,255,.12);border:0;color:#fff;border-radius:8px;width:30px;height:30px;cursor:pointer;font-size:16px;line-height:1}
  #gvd-dock .dk-head button:hover{background:rgba(255,255,255,.25)}
  #gvd-dock .dk-bal{background:#f59e0b;color:#0b2545;border-radius:99px;padding:2px 9px;font-size:12px;font-weight:700;white-space:nowrap;display:none}
  #gvd-dock .dk-body{padding:12px 16px 8px;overflow-y:auto;flex:1}
  #gvd-dock select#gp-ext{display:none}
  #gvd-dock #gp-state{display:inline-flex;align-items:center;gap:6px;padding:2px 9px;border-radius:99px;font-size:12px;background:#f1f5f9;color:#475569;font-weight:500}
  #gvd-dock #gp-state.ok{background:#dcfce7;color:#166534}#gvd-dock #gp-state.bad{background:#fef2f2;color:#b91c1c}
  #gvd-dock .dk-num{position:relative;margin:10px 0 4px}
  #gvd-dock #gp-num{width:100%;border:0;border-bottom:1px solid #e2e8f0;text-align:center;font-size:22px;letter-spacing:.05em;padding:8px 34px;outline:none;background:transparent}
  #gvd-dock #ph-bs{position:absolute;right:0;top:50%;transform:translateY(-50%);border:0;background:none;color:#64748b;font-size:18px;cursor:pointer;padding:4px 8px}
  #gvd-dock #gp-pad{display:grid;grid-template-columns:repeat(3,58px);gap:10px 26px;justify-content:center;margin:12px 0 14px}
  #gvd-dock #gp-pad button{width:58px;height:58px;border-radius:50%;border:1px solid #e2e8f0;background:#f8fafc;color:#0f172a;font-size:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;line-height:1;padding:0;cursor:pointer}
  #gvd-dock #gp-pad button:hover{background:#eef2ff;border-color:#c7d2fe}
  #gvd-dock #gp-pad button small{font-size:8.5px;letter-spacing:.12em;color:#64748b;margin-top:2px;min-height:9px}
  #gvd-dock .dk-btn{width:100%;padding:11px;border:0;border-radius:12px;color:#fff;font-size:15px;font-weight:600;cursor:pointer}
  #gvd-dock #gp-call{background:#16a34a}#gvd-dock #gp-call:disabled{background:#86d6a3;cursor:not-allowed}
  #gvd-dock #gp-answer{background:#16a34a;margin-bottom:8px}#gvd-dock #gp-hang{background:#dc2626;margin-top:8px}
  #gvd-dock .ph-live{display:none;text-align:center;padding:12px;margin:10px 0 2px;border-radius:12px;background:linear-gradient(135deg,#eef2ff,#f8fafc);border:1px solid #e0e7ff}
  #gvd-dock .ph-live.on{display:block}#gvd-dock .ph-live .who{font-size:19px;font-weight:600}#gvd-dock .ph-live .stt{font-size:12.5px;color:#3730a3;margin-top:3px}#gvd-dock .ph-live .tm{font-family:ui-monospace,Menlo,monospace;margin-top:4px}
  #gvd-dock .ph-ctl{display:none;grid-template-columns:repeat(4,1fr);gap:8px;margin:10px 0 4px}#gvd-dock .ph-ctl.on{display:grid}
  #gvd-dock .ph-ctl button{display:flex;flex-direction:column;align-items:center;gap:3px;padding:8px 2px;border-radius:10px;font-size:11.5px;background:#fff;border:1px solid #e2e8f0;color:#0f172a;cursor:pointer}
  #gvd-dock .ph-ctl button.act{background:#eef2ff;border-color:#c7d2fe;color:#3730a3}#gvd-dock .ph-ctl button:disabled{opacity:.45;cursor:not-allowed}
  #gvd-dock .dk-msg{font-size:12px;color:#64748b;min-height:14px;margin-top:6px}
  #gvd-dock .dk-tabs{display:grid;grid-template-columns:repeat(4,1fr);border-top:1px solid #e2e8f0;background:#f8fafc}
  #gvd-dock .dk-tabs a{display:flex;flex-direction:column;align-items:center;gap:2px;padding:8px 0;font-size:11px;color:#64748b;text-decoration:none}
  #gvd-dock .dk-tabs a i{font-size:17px}#gvd-dock .dk-tabs a.on,#gvd-dock .dk-tabs a:hover{color:#0b2545}#gvd-dock .dk-tabs a.on i{color:#f59e0b}
  .gp-dock-btn{position:relative;width:36px;height:36px;border-radius:10px;border:1px solid var(--gp-line,#e2e8f0);background:#fff;color:#0b2545;display:inline-flex;align-items:center;justify-content:center;cursor:pointer;font-size:17px;text-decoration:none}
  .gp-dock-btn:hover,.gp-dock-btn.on{background:#0b2545;color:#fff;border-color:#0b2545}
  .gp-dock-btn .dot{position:absolute;top:5px;right:5px;width:8px;height:8px;border-radius:50%;background:#94a3b8;border:2px solid #fff;display:none}
  .gp-dock-btn .dot.ok{display:block;background:#16a34a}
  @media (max-width:640px){#gvd-dock{top:auto;bottom:0;right:0;left:0;width:auto;max-width:none;max-height:88vh;border-radius:18px 18px 0 0}#gvd-dock #gp-pad{grid-template-columns:repeat(3,54px)}#gvd-dock #gp-pad button{width:54px;height:54px}}
</style>
<div id="gvd-dock" data-vp="{{ @filemtime(public_path('js/gvd-phone.js')) }}" data-vu="{{ @filemtime(public_path('js/gvd-phone-ui.js')) }}" role="dialog" aria-label="Phone" aria-hidden="true" data-demo="{{ !empty($gpDockDemo) ? '1' : '' }}">
  <div class="dk-head"><i class="feather icon-phone"></i><span class="t" id="dk-title">Phone</span><span class="dk-bal" id="dk-bal"></span>
    <button type="button" id="dk-min" title="Minimize" aria-label="Minimize">&#8211;</button></div>
  <div class="dk-body">
    <div id="gp-load" class="dk-msg"><span class="vo-spin"></span>Loading your extensions…</div>
    <div id="gp-phone" style="display:none">
      <select id="gp-ext" aria-label="Extension"></select>
      <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap"><span id="gp-state" class="state">Off</span> <span id="gp-msg" class="dk-msg" style="margin:0"></span> <a href="#" id="gp-retry" style="display:none;font-size:12px">Retry</a></div>
      <div class="ph-live" id="ph-live"><div class="who" id="ph-who"></div><div class="stt" id="ph-st"></div><div class="tm" id="ph-tm"></div></div>
      <div class="dk-num"><input id="gp-num" placeholder="Enter a number" inputmode="tel" autocomplete="off"><button type="button" id="ph-bs" title="Delete last digit" aria-label="Delete last digit">&#9003;</button></div>
      <div id="gp-pad"></div>
      <button type="button" class="dk-btn" id="gp-answer" style="display:none"><i class="feather icon-phone-incoming"></i> Answer</button>
      <button type="button" class="dk-btn" id="gp-call" disabled><i class="feather icon-phone"></i> Call</button>
      <div class="ph-ctl" id="ph-ctl">
        <button type="button" id="gp-mute" style="display:none"><i class="feather icon-mic-off"></i><span>Mute</span></button>
        <button type="button" disabled title="Not available yet"><i class="feather icon-pause"></i><span>Hold</span></button>
        <button type="button" id="ph-kp"><i class="feather icon-grid"></i><span>Keypad</span></button>
        <button type="button" disabled title="Not available yet"><i class="feather icon-share"></i><span>Transfer</span></button>
      </div>
      <button type="button" class="dk-btn" id="gp-hang" style="display:none"><i class="feather icon-phone-off"></i> Hang up</button>
      <div id="gp-errors" class="dk-msg"></div>
      <div class="dk-msg" style="text-align:center"><a href="/user/webrtc">Phone settings</a> · <a href="/user/mobile-app">Get the mobile app</a></div>
      <audio id="gp-audio" autoplay></audio>
    </div>
  </div>
  <nav class="dk-tabs">
    <a href="/user/calls/inbound"><i class="feather icon-clock"></i>History</a>
    <a href="/user/calls/inbound?status=voicemail"><i class="feather icon-voicemail"></i>Messages</a>
    <a href="#" class="on" id="dk-kp"><i class="feather icon-grid"></i>Keypad</a>
    <a href="/user/webrtc"><i class="feather icon-settings"></i>Settings</a>
  </nav>
</div>
<script src="/js/gvd-dock.js?v={{ @filemtime(public_path('js/gvd-dock.js')) }}"></script>
