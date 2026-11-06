{{-- Floating help bubble (2026-11-01 hb). Chat = the same web chat widget as the public pages (same widget id), loaded only on click. --}}
<style>
  #gvd-help-btn{position:fixed;right:24px;bottom:24px;z-index:1070;width:52px;height:52px;border-radius:50%;border:0;background:#0b2545;color:#fff;font-size:24px;font-weight:700;box-shadow:0 10px 28px rgba(11,37,69,.35);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:right .2s,bottom .2s,transform .15s}
  #gvd-help-btn:hover{transform:translateY(-2px);background:#13315c}
  #gvd-help-btn.on{background:#f59e0b;color:#0b2545}
  #gvd-help{position:fixed;right:24px;bottom:88px;z-index:1071;width:360px;max-width:calc(100vw - 24px);max-height:calc(100vh - 110px);background:#fff;border-radius:16px;border:1px solid #e2e8f0;box-shadow:0 18px 48px rgba(11,37,69,.28);display:none;flex-direction:column;overflow:hidden;font-size:14px;transition:right .2s,bottom .2s}
  #gvd-help.open{display:flex}
  #gvd-help .hb-head{background:linear-gradient(135deg,#0b2545,#13315c);color:#fff;padding:14px 16px;display:flex;align-items:center;gap:10px}
  #gvd-help .hb-head b{flex:1;font-size:15px}
  #gvd-help .hb-head button{background:rgba(255,255,255,.12);border:0;color:#fff;border-radius:8px;width:30px;height:30px;cursor:pointer;font-size:15px}
  #gvd-help .hb-body{padding:14px 16px;overflow-y:auto}
  #gvd-help .hb-chat{display:flex;width:100%;align-items:center;gap:12px;padding:12px 14px;border-radius:12px;border:0;background:#f59e0b;color:#0b2545;font-weight:700;font-size:15px;cursor:pointer;text-align:left}
  #gvd-help .hb-chat small{display:block;font-weight:500;font-size:12px;color:#3b2a06}
  #gvd-help .hb-chat i{font-size:20px}
  #gvd-help h6{font-size:11px;letter-spacing:.08em;text-transform:uppercase;color:#64748b;margin:16px 0 6px;font-weight:600}
  #gvd-help .hb-links a{display:flex;align-items:center;gap:10px;padding:9px 10px;border-radius:10px;color:#0f172a;text-decoration:none}
  #gvd-help .hb-links a:hover{background:#f1f5f9}
  #gvd-help .hb-links i{color:#0b2545;font-size:16px;width:18px}
  #gvd-help input.hb-q{width:100%;padding:8px 10px;border:1px solid #e2e8f0;border-radius:10px;font-size:13.5px;outline:none}
  #gvd-help input.hb-q:focus{border-color:#0b2545}
  #gvd-help .hb-none{font-size:12.5px;color:#64748b;padding:6px 10px;display:none}
  #gvd-help .hb-frame{display:none;position:relative;flex:none;overflow:hidden;height:540px;max-height:calc(100vh - 170px)}
  #gvd-help .hb-frame iframe{position:absolute;top:0;left:0;width:100%;height:calc(100% + 26px);border:0}
  #gvd-help.chat .hb-frame{display:block}#gvd-help.chat .hb-body{display:none}
  #gvd-help .hb-back{display:none}#gvd-help.chat .hb-back{display:inline-block}
  @media (max-width:640px){#gvd-help-btn{right:14px;bottom:14px;width:48px;height:48px}
    #gvd-help{right:0;left:0;bottom:0;width:auto;max-width:none;border-radius:18px 18px 0 0;max-height:85vh}
    #gvd-help .hb-frame{height:75vh;max-height:none}}
</style>
<button type="button" id="gvd-help-btn" aria-label="Help" aria-expanded="false" title="Help">?</button>
<div id="gvd-help" role="dialog" aria-label="Help" aria-hidden="true">
  <div class="hb-head"><button type="button" class="hb-back" id="hb-back" aria-label="Back">&#8592;</button><b id="hb-title">How can we help?</b><button type="button" id="hb-x" aria-label="Close">&#10005;</button></div>
  <div class="hb-body">
    <button type="button" class="hb-chat" id="hb-chat"><i class="feather icon-message-circle"></i><span>Chat with us<small>Get answers right away, any time</small></span></button>
    <h6>How-to videos</h6>
    <input type="search" class="hb-q" id="hb-q" placeholder="Search how-to videos" aria-label="Search how-to videos">
    <div class="hb-links" id="hb-vids">
      @foreach ([['PNCq8zEjeME', 'How to send the business credit application'], ['09zz9mjywDw', 'Set up the mobile app'], ['r6sN-L-8hm0', 'Start your fax service'], ['qYaCfPpsyUk', 'Use the SMS campaign platform']] as $hv)
        <a href="/user/howtovideo?v={{ $hv[0] }}" data-t="{{ strtolower($hv[1]) }}"><i class="feather icon-play-circle"></i>{{ $hv[1] }}</a>
      @endforeach
      <div class="hb-none" id="hb-none">No videos match. <a href="/user/howtovideo" style="display:inline;padding:0">See all videos</a></div>
    </div>
    <h6>Quick links</h6>
    <div class="hb-links">
      <a href="/user/howtovideo"><i class="feather icon-video"></i>All how-to videos</a>
      <a href="/user/mobile-app"><i class="feather icon-smartphone"></i>Mobile app</a>
      <a href="tel:+18773085803"><i class="feather icon-phone"></i>Call support: (877) 308-5803</a>
      <a href="mailto:support@globalvoicedirect.com"><i class="feather icon-mail"></i>support@globalvoicedirect.com</a>
    </div>
  </div>
  <div class="hb-frame" id="hb-frame"></div>
</div>
<script>
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var btn = $('gvd-help-btn'), box = $('gvd-help');
  var CHAT = 'https://chat.ithinq.ai/w/wgt_o8bdyw4oybzkfsmnrg8sbwv5711f'; // same widget as the public pages
  function place() {
    var d = document.getElementById('gvd-dock'), open = d && d.classList.contains('open'), mob = window.innerWidth <= 640;
    var r = mob ? 14 : 24, b = mob ? 14 : 24;
    if (open) { if (mob) b = d.offsetHeight + 12; else r = 24 + d.offsetWidth + 16; }
    btn.style.right = r + 'px'; btn.style.bottom = b + 'px';
    if (!mob) { box.style.right = r + 'px'; box.style.bottom = (b + 64) + 'px'; box.style.left = ''; }
    else { box.style.right = ''; box.style.bottom = ''; }
  }
  setInterval(place, 600); window.addEventListener('resize', place); place();
  function show(on) { box.classList.toggle('open', on); btn.classList.toggle('on', on); btn.setAttribute('aria-expanded', on ? 'true' : 'false'); box.setAttribute('aria-hidden', on ? 'false' : 'true'); btn.textContent = on ? '\u2715' : '?'; place(); }
  btn.onclick = function () { show(!box.classList.contains('open')); };
  $('hb-x').onclick = function () { show(false); };
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && box.classList.contains('open')) show(false); });
  $('hb-chat').onclick = function () {
    var f = $('hb-frame');
    if (!f.firstChild) { var i = document.createElement('iframe'); i.src = CHAT; i.title = 'Chat with Global Voice Direct'; i.setAttribute('allow', 'clipboard-write'); f.appendChild(i); }
    box.classList.add('chat'); $('hb-title').textContent = 'Chat with us';
  };
  window.addEventListener('message', function (e) { // the widget's own open/close handshake, same as its loader
    var f = $('hb-frame').firstChild; if (!f || e.source !== f.contentWindow || e.origin !== 'https://chat.ithinq.ai') return;
    var t = e.data && e.data.type;
    if (t === 'ithinq:chat:ready') f.contentWindow.postMessage({ type: 'ithinq:chat:open', page: { url: location.href.slice(0, 500), title: document.title.slice(0, 200) } }, 'https://chat.ithinq.ai');
    if (t === 'ithinq:chat:close') $('hb-back').click();
  });
  $('hb-back').onclick = function () { box.classList.remove('chat'); $('hb-title').textContent = 'How can we help?'; };
  $('hb-q').oninput = function () { var q = this.value.trim().toLowerCase(), n = 0;
    document.querySelectorAll('#hb-vids a[data-t]').forEach(function (a) { var ok = !q || a.getAttribute('data-t').indexOf(q) >= 0; a.style.display = ok ? '' : 'none'; if (ok) n++; });
    $('hb-none').style.display = n ? 'none' : 'block'; };
})();
</script>
