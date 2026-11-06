(function () {
  try { if (window.Pace) { if (Pace.options && Pace.options.ajax) { Pace.options.ajax.trackWebSockets = false; Pace.options.ajax.ignoreURLs = (Pace.options.ajax.ignoreURLs || []).concat(['rtcsip', '/user/phone/']); } Pace.options.restartOnRequestAfter = false; Pace.stop(); } } catch (e) {}
  var $ = function (id) { return document.getElementById(id); };
  var cfg = null, ua = null, user = null, muted = false, state = 'off';
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  // Local US ringback (440+480 Hz, 2s on / 4s off), played on 180 Ringing until 183 early media, answer or hang-up.
  var rb = { ctx: null, nodes: null, t: null };
  function ringStart() {
    if (rb.nodes) return;
    try {
      var C = window.AudioContext || window.webkitAudioContext; if (!C) return;
      rb.ctx = rb.ctx || new C(); if (rb.ctx.state === 'suspended') rb.ctx.resume();
      var g = rb.ctx.createGain(); g.gain.value = 0; g.connect(rb.ctx.destination);
      var o1 = rb.ctx.createOscillator(), o2 = rb.ctx.createOscillator(); o1.frequency.value = 440; o2.frequency.value = 480;
      o1.connect(g); o2.connect(g); o1.start(); o2.start(); rb.nodes = [o1, o2, g];
      var on = true, tick = function () { if (!rb.nodes) return; g.gain.value = on ? 0.12 : 0; rb.t = setTimeout(tick, on ? 2000 : 4000); on = !on; }; tick();
    } catch (e) { rb.nodes = null; }
  }
  function ringStop() {
    clearTimeout(rb.t); rb.t = null; var n = rb.nodes; rb.nodes = null;
    if (n) { try { n[0].stop(); n[1].stop(); n[2].disconnect(); } catch (e) {} }
  }
  function setState(s, msg) {
    if (s !== 'calling') ringStop();
    state = s;
    var t = { off: 'Off', connecting: 'Connecting...', ready: 'Ready', ringing: 'Incoming call', calling: 'Calling...', incall: 'In call', error: 'Error' }[s] || s;
    var el = $('gp-state'); el.textContent = t; el.className = 'state' + (s === 'ready' || s === 'incall' ? ' ok' : s === 'error' ? ' bad' : '');
    $('gp-msg').textContent = msg || '';
    var on = s !== 'off' && s !== 'error';
    $('gp-retry').style.display = s === 'error' ? '' : 'none';
    $('gp-ext').disabled = s === 'ringing' || s === 'calling' || s === 'incall';
    $('gp-call').disabled = s !== 'ready';
    $('gp-answer').style.display = s === 'ringing' ? '' : 'none';
    $('gp-hang').style.display = (s === 'ringing' || s === 'calling' || s === 'incall') ? '' : 'none';
    $('gp-mute').style.display = s === 'incall' ? '' : 'none';
  }
  // keypad
  '123456789*0#'.split('').forEach(function (k) {
    var b = document.createElement('button'); b.type = 'button'; b.className = 'btn btn-default'; b.textContent = k;
    b.onclick = function () { $('gp-num').value += k; if (state === 'incall' && user) { try { user.sendDTMF(k); } catch (e) {} } };
    $('gp-pad').appendChild(b);
  });
  (function load() {
    var body = new URLSearchParams({ _token: token });
    fetch('/user/phone/credentials', { method: 'POST', credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }, body: body })
      .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, j: j }; }); })
      .then(function (x) {
        if (!x.ok) { $('gp-load').textContent = x.j.error || 'Could not load your extensions.'; return; }
        cfg = x.j; var sel = $('gp-ext'); sel.innerHTML = '<option value="">Choose your extension</option>';
        var plans = {};
        cfg.extensions.forEach(function (e, i) {
          var p = e.plan || 'Extensions';
          if (!plans[p]) { plans[p] = document.createElement('optgroup'); plans[p].label = p; sel.appendChild(plans[p]); }
          var o = document.createElement('option'); o.value = i; o.textContent = e.label; plans[p].appendChild(o);
        });
        $('gp-errors').innerHTML = '';
        (cfg.errors || []).forEach(function (er) { var d = document.createElement('div'); d.textContent = er.extension + ': ' + er.error; $('gp-errors').appendChild(d); });
        if (!cfg.extensions.length) { $('gp-load').textContent = 'No extensions are available for the browser phone.'; return; }
        $('gp-load').style.display = 'none'; $('gp-phone').style.display = ''; setState('off', 'Choose your extension to connect.');
        var last = null; try { last = localStorage.getItem('gvdPhoneExt'); } catch (e) {}
        var idx = -1; cfg.extensions.forEach(function (e, i) { if (e.key === last) idx = i; });
        if (idx >= 0) { sel.value = String(idx); connect(); }
      })
      .catch(function () { $('gp-load').textContent = 'Phone service is unavailable right now.'; });
  })();
  function disconnect() {
    var u = user; user = null;
    if (u) u.unregister().catch(function () {}).then(function () { return u.disconnect(); }).catch(function () {});
  }
  function connect() {
    disconnect();
    var v = $('gp-ext').value;
    if (v === '') { try { localStorage.removeItem('gvdPhoneExt'); } catch (e) {} setState('off', 'Choose your extension to connect.'); return; }
    var e = cfg.extensions[+v]; if (!e) return;
    try { localStorage.setItem('gvdPhoneExt', e.key); } catch (x) {}
    setState('connecting', e.label);
    var S = window.GvdSip, me;
    var live = function (f) { return function () { if (user === me) f.apply(null, arguments); }; };
    me = user = new S.Web.SimpleUser(cfg.wss, {
      aor: 'sip:' + e.user + '@' + cfg.domain,
      media: { constraints: { audio: true, video: false }, remote: { audio: $('gp-audio') } },
      userAgentOptions: {
        authorizationUsername: e.user, authorizationPassword: e.password, displayName: e.label,
        contactParams: { transport: 'ws' }, logLevel: 'warn',
        sessionDescriptionHandlerFactoryOptions: { iceGatheringTimeout: 800, peerConnectionConfiguration: { iceServers: cfg.iceServers } } // was the 5 s default: no INVITE left until ICE gathering ended
      },
      delegate: {
        onRegistered: live(function () { setState('ready'); }),
        onUnregistered: live(function () { setState('error', state === 'connecting' ? 'Could not connect this extension.' : 'Disconnected.'); }),
        onCallReceived: live(function () { setState('ringing'); }),
        onCallAnswered: live(function () { setState('incall'); }),
        onCallHangup: live(function () { muted = false; $('gp-mute').textContent = 'Mute'; setState('ready'); }),
        onServerDisconnect: live(function () { setState('error', 'Connection lost.'); })
      }
    });
    me.connect().then(function () { return me.register(); })
      .catch(live(function (er) { try { console.error('[gvd-phone] connect/register failed:', er && (er.message || er)); } catch (x) {} setState('error', 'Could not connect this extension.'); }));
  }
  $('gp-ext').addEventListener('change', connect);
  $('gp-retry').onclick = function (ev) { ev.preventDefault(); connect(); };

  function dial() {
    var n = $('gp-num').value.replace(/[^\d*#+]/g, ''); if (!n || !user || state !== 'ready') return;
    setState('calling');
    try { var C = window.AudioContext || window.webkitAudioContext; if (C && !rb.ctx) rb.ctx = new C(); var a = $('gp-audio'); if (a) { a.muted = false; a.play && a.play().catch(function () {}); } } catch (e) {} // unlock audio inside the click gesture
    var why = function (e) {
      var nm = (e && e.name) || '', m = String((e && e.message) || e || '');
      try { console.error('[gvd-phone] call failed:', nm, m); } catch (x) {}
      if (nm === 'NotAllowedError' || /Permission|denied/i.test(m)) return 'Microphone blocked. Click the lock icon next to the address, allow Microphone, then try again.';
      if (nm === 'NotFoundError' || /Requested device not found|NotFound/i.test(m)) return 'No microphone found. Plug in a headset or microphone and try again.';
      if (nm === 'NotReadableError' || /Could not start|in use/i.test(m)) return 'Your microphone is in use by another app. Close it and try again.';
      if (/Not supported|getDescription/i.test(m)) return 'The browser could not open your microphone. Check the microphone permission and try again.';
      return 'Call failed: ' + (m || 'unknown error');
    };
    var md = navigator.mediaDevices;
    (md && md.getUserMedia ? md.getUserMedia({ audio: true }).then(function (st) { st.getTracks().forEach(function (t) { t.stop(); }); }) : Promise.reject(new Error('Not supported')))
    .then(function () { if (!user || state !== 'calling') return;
    return user.call('sip:' + n + '@' + cfg.domain, { earlyMedia: true }, { requestDelegate: {
      onReject: function (r) { var m = r && r.message; ringStop(); try { console.error('[gvd-phone] call rejected', m && m.statusCode, m && m.reasonPhrase); } catch (x) {} if (m && m.statusCode === 487) return; setState('ready', 'Call not completed: ' + (m ? m.statusCode + ' ' + m.reasonPhrase : 'rejected')); },
      onProgress: function (r) {
      var c = r && r.message && r.message.statusCode; if (state !== 'calling') return;
      if (c === 183 && r.message.body) ringStop(); else if (c === 180 || c === 183) ringStart(); // 183 with SDP = carrier early media on #gp-audio; 183 without SDP gets the local tone
    } } }); }).catch(function (e) { setState('ready', why(e)); });
  }
  $('gp-call').onclick = dial;
  $('gp-num').addEventListener('keydown', function (ev) { if (ev.key === 'Enter') dial(); });
  $('gp-answer').onclick = function () { user && user.answer().catch(function () { setState('ready', 'Could not answer. Allow microphone access.'); }); };
  var hanging = false;
  $('gp-hang').onclick = function () { ringStop(); if (!user || hanging) return; hanging = true; user.hangup().catch(function () {}).then(function () { hanging = false; }); };
  $('gp-mute').onclick = function () { if (!user) return; muted ? user.unmute() : user.mute(); muted = !muted; $('gp-mute').textContent = muted ? 'Unmute' : 'Mute'; };
  var bye = function () { if (user) { var u = user; user = null; try { u.unregister(); } catch (e) {} try { u.disconnect(); } catch (e) {} } };
  window.addEventListener('pagehide', bye); window.addEventListener('beforeunload', bye);
})();
