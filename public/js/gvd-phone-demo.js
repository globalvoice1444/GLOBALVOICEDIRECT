/* Sample-data login only: a look-alike of the browser phone. It never connects, registers or places a call. */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var exts = [['101', 'Front Desk'], ['102', 'Sales'], ['103', 'Support'], ['104', 'Accounting']];
  var sel = $('gp-ext'), num = $('gp-num'), call = $('gp-call'), hang = $('gp-hang'), st = $('gp-state'), pill = $('ph-pill'), msg = $('gp-msg');
  exts.forEach(function (e) { var o = document.createElement('option'); o.value = e[0]; o.textContent = 'Ext ' + e[0] + ' - ' + e[1]; sel.appendChild(o); });
  $('gp-load').style.display = 'none'; $('gp-phone').style.display = '';
  function ready() { st.textContent = 'Ready'; st.className = 'state ok'; if (pill) { pill.textContent = 'Ready'; pill.className = 'ph-status ok'; } }
  ready(); msg.textContent = 'Sample account: calls are shown for practice only.';
  var keys = [['1', ''], ['2', 'ABC'], ['3', 'DEF'], ['4', 'GHI'], ['5', 'JKL'], ['6', 'MNO'], ['7', 'PQRS'], ['8', 'TUV'], ['9', 'WXYZ'], ['*', ''], ['0', '+'], ['#', '']];
  var pad = $('gp-pad');
  keys.forEach(function (k) { var b = document.createElement('button'); b.type = 'button'; b.innerHTML = k[0] + '<small>' + k[1] + '</small>'; b.onclick = function () { num.value += k[0]; upd(); }; pad.appendChild(b); });
  function upd() { call.disabled = num.value.replace(/\D/g, '').length < 2; }
  num.addEventListener('input', upd); $('ph-bs').onclick = function () { num.value = num.value.slice(0, -1); upd(); };
  num.value = '(555) 010-0171'; upd();
  var timer = null, t0 = 0;
  call.onclick = function () {
    var who = num.value.trim(); if (!who) return;
    $('ph-who').textContent = who; $('ph-st').textContent = 'Calling…'; $('ph-tm').textContent = '';
    $('ph-live').classList.add('on'); $('ph-ctl').classList.add('on'); $('gp-mute').style.display = '';
    call.style.display = 'none'; hang.style.display = ''; st.textContent = 'On a call'; st.className = 'state';
    if (pill) { pill.textContent = 'On a call'; pill.className = 'ph-status busy'; }
    setTimeout(function () {
      if (!timer && hang.style.display !== 'none') {
        $('ph-st').textContent = 'Connected (sample call, no real call is placed)'; t0 = Date.now();
        timer = setInterval(function () { var s = Math.floor((Date.now() - t0) / 1000); $('ph-tm').textContent = Math.floor(s / 60) + ':' + ('0' + s % 60).slice(-2); }, 500);
      }
    }, 1800);
  };
  hang.onclick = function () {
    clearInterval(timer); timer = null; $('ph-live').classList.remove('on'); $('ph-ctl').classList.remove('on');
    hang.style.display = 'none'; call.style.display = ''; ready(); $('gp-errors').textContent = 'Call ended.';
  };
  $('gp-mute').onclick = function () { this.classList.toggle('act'); };
  $('ph-kp').onclick = function () { pad.style.display = pad.style.display === 'none' ? '' : 'none'; };
})();
