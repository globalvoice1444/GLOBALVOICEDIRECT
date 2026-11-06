/* Phone page presentation only (2026-11-01). Calling logic stays in gvd-phone.js; this script only restyles and mirrors its state. */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var pad = $('gp-pad'); if (!pad) return;
  pad.classList.add('ph-pad');
  var L = { '2': 'ABC', '3': 'DEF', '4': 'GHI', '5': 'JKL', '6': 'MNO', '7': 'PQRS', '8': 'TUV', '9': 'WXYZ', '0': '+' };
  Array.prototype.forEach.call(pad.querySelectorAll('button'), function (b) {
    var k = b.textContent.trim(); b.setAttribute('aria-label', k);
    var s = document.createElement('small'); s.textContent = L[k] || ''; b.appendChild(s);
  });
  $('ph-bs').onclick = function () { var n = $('gp-num'); n.value = n.value.slice(0, -1); n.focus(); };
  var kpOn = true; $('ph-kp').onclick = function () { kpOn = !kpOn; pad.style.display = kpOn ? '' : 'none'; $('ph-kp').classList.toggle('act', kpOn); };
  // FIX 2026-11-01: the old observer rewrote innerHTML on every mutation, which re-triggered itself forever
  // (infinite microtask loop -> "page unresponsive") as soon as gvd-phone.js set textContent='Mute' on hang-up.
  var mute = $('gp-mute'), MO = { childList: true, characterData: true, subtree: true }, mo = new MutationObserver(function () {
    var t = mute.textContent.trim();
    if ((t === 'Mute' || t === 'Unmute') && !mute.querySelector('span')) {
      mo.disconnect();
      mute.innerHTML = '<i class="feather ' + (t === 'Unmute' ? 'icon-mic' : 'icon-mic-off') + '"></i><span>' + t + '</span>';
      mo.takeRecords(); mo.observe(mute, MO);
    }
    mute.classList.toggle('act', /Unmute/.test(mute.textContent));
  });
  mo.observe(mute, MO);
  var timer = null, t0 = 0, last = '';
  function fmt(s) { var m = Math.floor(s / 60), x = s % 60; return m + ':' + (x < 10 ? '0' : '') + x; }
  function sync() {
    var st = $('gp-state'), txt = st.textContent.trim(), cls = st.className;
    var pill = $('ph-pill'); if (pill) { pill.textContent = txt === 'Off' ? 'Not connected' : txt; pill.className = 'ph-status' + (/\bok\b/.test(cls) ? ' ok' : /\bbad\b/.test(cls) ? ' bad' : /Connecting|Calling|Incoming/.test(txt) ? ' busy' : ''); }
    var live = /Calling|Incoming|In call/.test(txt);
    $('ph-live').classList.toggle('on', live); $('ph-ctl').classList.toggle('on', txt === 'In call');
    $('gp-call').style.display = live ? 'none' : '';
    if (live) { $('ph-who').textContent = $('gp-num').value || (txt === 'Incoming call' ? 'Incoming call' : ''); $('ph-st').textContent = txt === 'In call' ? 'Connected' : txt; }
    if (txt === 'In call' && last !== 'In call') { t0 = Date.now(); clearInterval(timer); timer = setInterval(function () { $('ph-tm').textContent = fmt(Math.floor((Date.now() - t0) / 1000)); }, 500); }
    if (txt !== 'In call') { clearInterval(timer); timer = null; $('ph-tm').textContent = ''; }
    last = txt;
  }
  new MutationObserver(sync).observe($('gp-state'), { childList: true, characterData: true, subtree: true, attributes: true, attributeFilter: ['class'] });
  sync();
})();
