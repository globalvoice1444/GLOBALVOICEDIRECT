(function () {
  var G = window.GVD_GREET || { rows: [], admin: false, holidays: [] };
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };
  var fmt = function (s) { if (!isFinite(s)) return '0:00'; s = Math.round(s); return Math.floor(s / 60) + ':' + ('0' + s % 60).slice(-2); };
  var current = null;

  // ---------- inline player ----------
  function player(el) {
    var src = el.getAttribute('data-src');
    el.innerHTML = '<button type="button" aria-label="Play">&#9658;</button><div class="bar"><i></i></div><span class="t">0:00 / –</span>';
    var btn = el.querySelector('button'), bar = el.querySelector('.bar'), fill = bar.querySelector('i'), t = el.querySelector('.t');
    var a = null;
    function ensure() {
      if (a) return a;
      a = new Audio(); a.preload = 'metadata'; a.src = src;
      a.addEventListener('loadedmetadata', upd); a.addEventListener('timeupdate', upd);
      a.addEventListener('ended', function () { btn.innerHTML = '&#9658;'; a.currentTime = 0; upd(); });
      a.addEventListener('error', function () { btn.disabled = true; t.textContent = 'Default system greeting'; });
      return a;
    }
    function upd() { fill.style.width = (a.duration ? 100 * a.currentTime / a.duration : 0) + '%'; t.textContent = fmt(a.currentTime) + ' / ' + (a.duration ? fmt(a.duration) : '–'); }
    btn.onclick = function (e) {
      e.stopPropagation(); ensure();
      if (a.paused) { if (current && current !== a) current.pause(); current = a; a.play().catch(function () {}); btn.innerHTML = '&#10074;&#10074;'; }
      else { a.pause(); btn.innerHTML = '&#9658;'; }
    };
    a = null;
    bar.onclick = function (e) { e.stopPropagation(); ensure(); if (!a.duration) return; var r = bar.getBoundingClientRect(); a.currentTime = a.duration * (e.clientX - r.left) / r.width; };
    // show duration without playing
    ensure();
    var pause = function () { if (a && !a.paused) { a.pause(); btn.innerHTML = '&#9658;'; } };
    return { pause: pause };
  }
  document.querySelectorAll('.gvd-gr .gr-player').forEach(player);

  // ---------- audio capture/convert -> WAV 8 kHz mono 16-bit ----------
  function toWav8k(arrayBuf) {
    var Ctx = window.AudioContext || window.webkitAudioContext; var ctx = new Ctx();
    return ctx.decodeAudioData(arrayBuf).then(function (buf) {
      var len = Math.ceil(buf.duration * 8000);
      var off = new OfflineAudioContext(1, Math.max(1, len), 8000);
      var s = off.createBufferSource(); s.buffer = buf; s.connect(off.destination); s.start();
      return off.startRendering();
    }).then(function (r) {
      ctx.close && ctx.close();
      var d = r.getChannelData(0), n = d.length, b = new ArrayBuffer(44 + n * 2), v = new DataView(b);
      var w = function (o, s) { for (var i = 0; i < s.length; i++) v.setUint8(o + i, s.charCodeAt(i)); };
      w(0, 'RIFF'); v.setUint32(4, 36 + n * 2, true); w(8, 'WAVE'); w(12, 'fmt '); v.setUint32(16, 16, true); v.setUint16(20, 1, true); v.setUint16(22, 1, true);
      v.setUint32(24, 8000, true); v.setUint32(28, 16000, true); v.setUint16(32, 2, true); v.setUint16(34, 16, true); w(36, 'data'); v.setUint32(40, n * 2, true);
      for (var i = 0; i < n; i++) { var x = Math.max(-1, Math.min(1, d[i])); v.setInt16(44 + i * 2, x < 0 ? x * 0x8000 : x * 0x7FFF, true); }
      return new Blob([b], { type: 'audio/wav' });
    });
  }
  function pickFile() {
    return new Promise(function (res) {
      var f = document.getElementById('gr-file'); f.value = '';
      f.onchange = function () { f.files[0] ? f.files[0].arrayBuffer().then(toWav8k).then(res, function () { alert('That file could not be read as audio.'); res(null); }) : res(null); };
      f.click();
    });
  }
  // recorder widget inside a container; calls back with a WAV blob
  function recorder(box, onReady) {
    box.innerHTML = '<button type="button" class="btn btn-sm btn-danger gr-rec">&#9679; Record</button> <button type="button" class="btn btn-sm btn-default gr-up">Upload file</button> <span class="muted gr-rs"></span><div class="gr-prev" style="margin-top:6px"></div>';
    var rec = null, chunks = [], st = box.querySelector('.gr-rs');
    function ready(blob) {
      if (!blob) return;
      var u = URL.createObjectURL(blob); box.querySelector('.gr-prev').innerHTML = '<audio controls src="' + u + '" style="width:100%"></audio>';
      st.textContent = 'Ready (' + Math.round(blob.size / 1024) + ' KB)'; onReady(blob);
    }
    box.querySelector('.gr-rec').onclick = function () {
      var b = this;
      if (rec) { rec.stop(); return; }
      navigator.mediaDevices.getUserMedia({ audio: true }).then(function (stream) {
        chunks = []; rec = new MediaRecorder(stream);
        rec.ondataavailable = function (e) { chunks.push(e.data); };
        rec.onstop = function () {
          stream.getTracks().forEach(function (t) { t.stop(); }); rec = null; b.innerHTML = '&#9679; Record again';
          st.textContent = 'Converting...';
          new Blob(chunks).arrayBuffer().then(toWav8k).then(ready, function () { st.textContent = 'Could not convert the recording.'; });
        };
        rec.start(); b.innerHTML = '&#9632; Stop'; st.textContent = 'Recording...';
      }, function () { st.textContent = 'Microphone permission is needed to record.'; });
    };
    box.querySelector('.gr-up').onclick = function () { pickFile().then(ready); };
  }

  function post(url, data) {
    var fd = new FormData(); fd.append('_token', token);
    Object.keys(data).forEach(function (k) { var v = data[k]; if (Array.isArray(v)) v.forEach(function (x) { fd.append(k + '[]', x); }); else if (v instanceof Blob) fd.append(k, v, 'greeting.wav'); else fd.append(k, v); });
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) throw new Error(j.error || 'Something went wrong.'); return j; }); });
  }
  var done = function (msg) { return function () { try { sessionStorage.setItem('gvdGreetMsg', msg); } catch (e) {} location.reload(); }; };
  (function () { var m = null; try { m = sessionStorage.getItem('gvdGreetMsg'); sessionStorage.removeItem('gvdGreetMsg'); } catch (e) {}
    if (!m) return; var d = document.createElement('div'); d.setAttribute('role', 'status'); d.textContent = '\u2713 ' + m;
    d.style.cssText = 'position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:9999;background:#1e8e3e;color:#fff;padding:10px 18px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.2);font-weight:600';
    document.body.appendChild(d); setTimeout(function () { d.remove(); }, 4000); })();
  var oops = function (e) { alert(e.message); };

  // ---------- row ⋯ menu ----------
  document.addEventListener('click', function (e) {
    var m = e.target.closest('.gr-more-btn');
    document.querySelectorAll('.gr-more.open').forEach(function (x) { if (!m || x !== m.parentNode) x.classList.remove('open'); });
    if (m) { e.preventDefault(); e.stopPropagation(); m.parentNode.classList.toggle('open'); return; }
    var act = e.target.closest('[data-act]');
    if (act) { e.preventDefault(); e.stopPropagation(); doAct(act.getAttribute('data-act'), act.getAttribute('data-target'), act.getAttribute('data-mode')); return; }
    if (e.target.closest('.gr-noclick')) return;
    var row = e.target.closest('tr.gr-row'); if (row) openPanel(G.rows[+row.getAttribute('data-i')]);
  });
  // ---------- bottom player bar (Manage greetings cards) ----------
  var bar = document.getElementById('gr-bar'), ba = null;
  if (bar) {
    var $ = function (i) { return document.getElementById(i); };
    var bupd = function () { if (!ba) return; var d = ba.duration || 0; $('gr-bt').textContent = fmt(ba.currentTime); $('gr-br').textContent = '-' + fmt(Math.max(0, d - ba.currentTime)); $('gr-bk').value = d ? Math.round(1000 * ba.currentTime / d) : 0; $('gr-bp').innerHTML = ba.paused ? '&#9654;' : '&#10074;&#10074;'; };
    document.querySelectorAll('.gr-play').forEach(function (el) {
      el.addEventListener('click', function (e) {
        e.preventDefault(); e.stopPropagation();
        if (current) current.pause();
        if (ba) ba.pause();
        ba = new Audio(el.getAttribute('data-src')); current = ba; ba.volume = $('gr-bv').value / 100;
        ba.addEventListener('timeupdate', bupd); ba.addEventListener('loadedmetadata', bupd); ba.addEventListener('ended', bupd); ba.addEventListener('pause', bupd); ba.addEventListener('play', bupd);
        ba.addEventListener('error', function () { $('gr-bn').textContent = el.getAttribute('data-name') + ' – default system greeting (no recording)'; });
        $('gr-bn').textContent = el.getAttribute('data-name'); $('gr-bd').href = el.getAttribute('data-src');
        bar.classList.add('on'); ba.play().catch(function () {});
      });
    });
    $('gr-bp').onclick = function () { if (!ba) return; if (ba.paused) { if (current && current !== ba) current.pause(); current = ba; ba.play().catch(function () {}); } else ba.pause(); };
    $('gr-bs').onclick = function () { if (!ba) return; ba.pause(); ba.currentTime = 0; bupd(); };
    $('gr-bk').oninput = function () { if (ba && ba.duration) ba.currentTime = ba.duration * this.value / 1000; };
    $('gr-bv').oninput = function () { if (ba) ba.volume = this.value / 100; };
  }

  function doAct(a, target, mode) {
    if (a === 'replace') {
      panelHtml('<h5>Replace greeting</h5><p class="muted">Record a new greeting or upload a file. It is converted automatically.</p><div id="gr-rep"></div><p><button class="btn btn-primary" id="gr-rep-save" disabled>Save</button> <button class="btn btn-default" id="gr-close2">Cancel</button></p>');
      var blob = null; recorder(document.getElementById('gr-rep'), function (b) { blob = b; document.getElementById('gr-rep-save').disabled = false; });
      document.getElementById('gr-close2').onclick = closePanel;
      if (mode === 'upload') { var ub = document.querySelector('#gr-rep .gr-up'); if (ub) ub.click(); }
      document.getElementById('gr-rep-save').onclick = function () { this.disabled = true; post('/user/greetings/replace', { target: target, audio: blob }).then(done('Saved'), oops); };
    } else if (a === 'restore') {
      if (confirm('Go back to the standard system greeting? Your custom recording will no longer play.')) post('/user/greetings/restore', { target: target }).then(done('Default greeting restored.'), oops);
    } else if (a === 'delete') {
      if (confirm('Delete this alternate greeting?')) post('/user/greetings/delete', { target: target }).then(done('Deleted.'), oops);
    }
  }

  // ---------- side panel ----------
  var panel = document.getElementById('gr-panel'), shade = document.getElementById('gr-shade');
  function panelHtml(h) { panel.innerHTML = '<a href="#" id="gr-x" style="float:right;font-size:20px">&times;</a>' + h; panel.classList.add('open'); shade.classList.add('open'); panel.setAttribute('aria-hidden', 'false'); document.getElementById('gr-x').onclick = function (e) { e.preventDefault(); closePanel(); }; }
  function closePanel() { panel.classList.remove('open'); shade.classList.remove('open'); panel.setAttribute('aria-hidden', 'true'); panel.querySelectorAll('audio').forEach(function (a) { a.pause(); }); }
  shade.onclick = closePanel;
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closePanel(); });

  function schedForm(prefix) {
    var days = ['mo', 'tu', 'we', 'th', 'fr', 'sa', 'su'], names = { mo: 'Mon', tu: 'Tue', we: 'Wed', th: 'Thu', fr: 'Fri', sa: 'Sat', su: 'Sun' };
    return '<div class="form-group"><label>When it plays</label><select class="form-control" id="' + prefix + 'kind"><option value="weekly">Weekly hours (e.g. lunch)</option><option value="dates">Specific dates</option><option value="holidays">Company holiday list</option></select></div>' +
      '<div id="' + prefix + 'weekly">' + days.map(function (d) { return '<label style="margin-right:8px"><input type="checkbox" value="' + d + '"' + (['mo', 'tu', 'we', 'th', 'fr'].indexOf(d) >= 0 ? ' checked' : '') + '> ' + names[d] + '</label>'; }).join('') +
      '<div class="row" style="margin-top:6px"><div class="col-6"><input type="time" class="form-control" id="' + prefix + 'from" value="12:00"></div><div class="col-6"><input type="time" class="form-control" id="' + prefix + 'to" value="13:00"></div></div></div>' +
      '<div id="' + prefix + 'dates" style="display:none"><input type="date" class="form-control" id="' + prefix + 'd1"><div class="muted" id="' + prefix + 'dl" style="margin:4px 0"></div><button type="button" class="btn btn-sm btn-default" id="' + prefix + 'dadd">Add date</button></div>' +
      '<div id="' + prefix + 'holidays" style="display:none" class="muted">Plays on every date in your company holiday list (below).</div>';
  }
  function schedBind(prefix) {
    var dates = [];
    var k = document.getElementById(prefix + 'kind');
    k.onchange = function () { ['weekly', 'dates', 'holidays'].forEach(function (x) { document.getElementById(prefix + x).style.display = k.value === x ? '' : 'none'; }); };
    document.getElementById(prefix + 'dadd').onclick = function () { var v = document.getElementById(prefix + 'd1').value; if (v && dates.indexOf(v) < 0) dates.push(v); document.getElementById(prefix + 'dl').textContent = dates.join(', '); };
    return function () {
      var o = { schedule: k.value };
      if (k.value === 'weekly') { o.days = [].map.call(document.querySelectorAll('#' + prefix + 'weekly input[type=checkbox]:checked'), function (c) { return c.value; }); o.from = document.getElementById(prefix + 'from').value; o.to = document.getElementById(prefix + 'to').value; }
      if (k.value === 'dates') o.dates = dates.slice();
      return o;
    };
  }
  function holidaysHtml() {
    return '<h6 style="margin-top:18px">Company holiday list</h6><div id="gr-hl">' + (G.holidays.length ? G.holidays.map(function (h) { return '<div>' + esc(h.day) + ' – ' + esc(h.name) + (G.admin ? ' <a href="#" data-hrm="' + h.id + '">remove</a>' : '') + '</div>'; }).join('') : '<div class="muted">No holidays yet.</div>') + '</div>' +
      (G.admin ? '<div class="row" style="margin-top:6px"><div class="col-5"><input type="date" class="form-control" id="gr-hd"></div><div class="col-5"><input class="form-control" id="gr-hn" placeholder="e.g. Thanksgiving"></div><div class="col-2"><button class="btn btn-sm btn-default" id="gr-hadd">Add</button></div></div><div class="muted" style="margin-top:4px">Changes apply to greetings the next time you save their schedule.</div>' : '');
  }
  function bindHolidays(row) {
    var hl = function (j) { G.holidays = j.holidays || G.holidays; openPanel(row); };
    var add = document.getElementById('gr-hadd');
    if (add) add.onclick = function () { post('/user/greetings/holidays', { action: 'add', day: document.getElementById('gr-hd').value, name: document.getElementById('gr-hn').value }).then(hl, oops); };
    panel.querySelectorAll('[data-hrm]').forEach(function (a) { a.onclick = function (e) { e.preventDefault(); post('/user/greetings/holidays', { action: 'remove', id: a.getAttribute('data-hrm') }).then(hl, oops); }; });
  }
  function openPanel(r) {
    if (!r) return;
    var alts = r.alternates.map(function (a) {
      return '<div class="gr-alt"><strong>' + esc(a.name) + '</strong> ' + (a.active ? '<span class="on">● active now</span>' : '') +
        '<div class="muted">' + esc(a.schedule) + '</div><div class="gr-player" data-src="' + esc(a.audio) + '" style="margin-top:6px"></div>' +
        (G.admin ? '<div style="margin-top:6px"><a href="#" data-act="replace" data-target="' + esc(a.key) + '">Replace</a>' + (a.deletable ? ' · <a href="#" data-act="delete" data-target="' + esc(a.key) + '">Delete</a>' : '') + '</div>' : '') + '</div>';
    }).join('') || '<p class="muted">No alternate greetings yet.</p>';
    var add = (G.admin && r.kind === 'company' && r.name === 'Company main greeting') ?
      '<h6 style="margin-top:18px">Add an alternate greeting</h6><div class="form-group"><input class="form-control" id="gr-an" placeholder="Name, e.g. Holiday or Lunch"></div>' + schedForm('gr-a-') +
      '<div class="form-group" style="margin-top:8px"><label>Audio</label><div id="gr-arec"></div></div><button class="btn btn-primary" id="gr-asave" disabled>Add greeting</button>' +
      '<div class="muted" style="margin-top:6px">The alternate plays only when your call menu sends callers to it during its schedule; this page does not change your call routing.</div>' : '';
    var sched = (G.admin && r.kind === 'company') ? '<h6 style="margin-top:18px">Change when "' + esc(r.name) + '" plays</h6><div class="muted">Now: ' + esc(r.schedule) + '</div>' + schedForm('gr-s-') + '<button class="btn btn-default btn-sm" id="gr-ssave">Save schedule</button>' : '';
    panelHtml('<h5>' + esc(r.name) + '</h5><div class="muted">' + esc(r.schedule) + '</div><div class="gr-player" data-src="' + esc(r.audio) + '" style="margin:10px 0"></div>' +
      '<h6>Alternate greetings</h6>' + alts + add + sched + (r.kind === 'company' ? holidaysHtml() : '') +
      (!G.admin ? '<p class="muted" style="margin-top:14px">View only. Your company administrator can change greetings.</p>' : ''));
    panel.querySelectorAll('.gr-player').forEach(player);
    if (add) {
      var blob = null, getS = schedBind('gr-a-');
      recorder(document.getElementById('gr-arec'), function (b) { blob = b; document.getElementById('gr-asave').disabled = false; });
      document.getElementById('gr-asave').onclick = function () { var o = getS(); o.name = document.getElementById('gr-an').value; o.audio = blob; this.disabled = true; var b = this; post('/user/greetings/alternate', o).then(done('Alternate greeting added.'), function (e) { b.disabled = false; oops(e); }); };
    }
    if (sched) { var getT = schedBind('gr-s-'); document.getElementById('gr-ssave').onclick = function () { var o = getT(); o.target = r.key; if (confirm('Change when this greeting plays? This affects what callers hear.')) post('/user/greetings/schedule', o).then(done('Schedule saved.'), oops); }; }
    if (r.kind === 'company') bindHolidays(r);
  }
})();
