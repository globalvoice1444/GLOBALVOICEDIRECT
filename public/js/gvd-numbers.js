(function () {
  var G = window.GVD_NUM || { rows: [], admin: false, exts: [] };
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };
  var EMAIL = { none: 'Off', forward: 'Forward', notify: 'Notify only', copy: 'Copy', fwd_as_attachment: 'Forward as attachment' };
  var FAX = { pdf: 'Single PDF file', tiff: 'Single TIFF file', multi_tiff: 'One TIFF per page', multi_png: 'One PNG per page' };
  var byId = {}; G.rows.forEach(function (r) { byId[r.i_account] = r; });
  var extName = function (x) { for (var i = 0; i < G.exts.length; i++) if (G.exts[i].ext === x) return 'Ext ' + x + (G.exts[i].name ? ' – ' + G.exts[i].name : ''); return 'Ext ' + x; };
  var routeText = function (d) { return d.route === 'ROOT' ? 'Company greeting' : d.route === 'TRANSFER' ? extName(d.ext) : d.route === 'CUSTOM' ? 'Custom' : '–'; };

  function cell(tr, sel, html) { var c = tr.querySelector(sel); if (c) c.innerHTML = html; }
  function paint(r) {
    var tr = document.querySelector('tr.pn-row[data-id="' + r.i_account + '"]'); if (!tr) return;
    if (r.kind === 'vo') cell(tr, '.pn-route', esc(routeText(r.d)));
    cell(tr, '.pn-email', esc(r.d.email ? (EMAIL[r.d.email] || r.d.email) : '–'));
    cell(tr, '.pn-fax', esc(r.d.fax ? (FAX[r.d.fax] || r.d.fax) : '–'));
  }
  var inflight = {};
  function load(list) { // one request for many rows; server fetches them in parallel
    list = list.filter(function (r) { return !r.d && !inflight[r.i_account]; });
    if (!list.length) return Promise.resolve();
    var p = fetch('/user/phone-numbers/detail?ids=' + list.map(function (r) { return r.i_account; }).join(','), { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' } })
      .then(function (x) { return x.ok ? x.json() : {}; }, function () { return {}; })
      .then(function (j) { var d = (j && j.details) || {}; list.forEach(function (r) { r.d = d[r.i_account] || {}; delete inflight[r.i_account]; paint(r); }); });
    list.forEach(function (r) { inflight[r.i_account] = p; });
    return p;
  }
  var active = G.rows.filter(function (r) { return r.status === 'Active'; });
  var t0 = performance.now();
  load(active).then(function () { window.GVD_NUM_LOADED_MS = Math.round(performance.now() - t0); });

  function post(url, data) {
    var fd = new FormData(); fd.append('_token', token);
    Object.keys(data).forEach(function (k) { fd.append(k, data[k]); });
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) throw new Error(j.error || 'Something went wrong.'); return j; }); });
  }
  (function () { var m = null; try { m = sessionStorage.getItem('gvdNumMsg'); sessionStorage.removeItem('gvdNumMsg'); } catch (e) {}
    if (!m) return; var d = document.createElement('div'); d.setAttribute('role', 'status'); d.textContent = '\u2713 ' + m;
    d.style.cssText = 'position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:9999;background:#1e8e3e;color:#fff;padding:10px 18px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.2);font-weight:600';
    document.body.appendChild(d); setTimeout(function () { d.remove(); }, 4000); })();

  var panel = document.getElementById('pn-panel'), shade = document.getElementById('pn-shade');
  function close() { panel.classList.remove('open'); shade.classList.remove('open'); panel.setAttribute('aria-hidden', 'true'); }
  shade.onclick = close;
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  function opts(map, cur) { return Object.keys(map).map(function (k) { return '<option value="' + esc(k) + '"' + (k === cur ? ' selected' : '') + '>' + esc(map[k]) + '</option>'; }).join(''); }

  function open(r) {
    if (r.status === 'Active' && !r.d) {
      panel.innerHTML = '<div style="display:flex;justify-content:space-between"><h5 style="margin:0">' + esc(r.display) + '</h5><a href="#" id="pn-x" aria-label="Close">&#10005;</a></div><p style="margin-top:24px"><span class="pn-spin"></span>Loading this number\u2019s settings…</p>';
      panel.classList.add('open'); shade.classList.add('open'); panel.setAttribute('aria-hidden', 'false');
      document.getElementById('pn-x').onclick = function (e) { e.preventDefault(); close(); };
      (inflight[r.i_account] || load([r])).then(function () { if (panel.classList.contains('open')) open(r); });
      return;
    }
    var d = r.d || {}, ro = !G.admin, dis = ro ? ' disabled' : '';
    var h = '<div style="display:flex;justify-content:space-between;align-items:center"><h5 style="margin:0">' + esc(r.display) + '</h5><a href="#" id="pn-x" aria-label="Close">&#10005;</a></div>' +
      '<div class="muted">' + esc(r.plan) + ' · ' + esc(r.status) + '</div>' +
      '<label>Label (shown only in this app)</label><input type="text" id="pn-l" maxlength="60" value="' + esc(r.label) + '"' + dis + '>';
    if (r.kind === 'vo') {
      if (d.route) {
        h += '<label>When someone calls this number</label><select id="pn-r"' + dis + '>' +
          (d.route === 'CUSTOM' ? '<option value="" selected>Custom (keep as is)</option>' : '') +
          '<option value="ROOT"' + (d.route === 'ROOT' ? ' selected' : '') + '>Play the company greeting</option>' +
          '<option value="TRANSFER"' + (d.route === 'TRANSFER' ? ' selected' : '') + '>Ring an extension</option></select>' +
          '<div id="pn-ew"><label>Extension</label><select id="pn-e"' + dis + '>' + G.exts.map(function (x) { return '<option value="' + esc(x.ext) + '"' + (x.ext === d.ext ? ' selected' : '') + '>' + esc(extName(x.ext)) + '</option>'; }).join('') + '</select></div>';
      } else h += '<label>When someone calls this number</label><div class="muted">Not available for this line.</div>';
    }
    h += '<label>Caller ID shown on outgoing calls</label><select id="pn-c"' + dis + '>' +
      opts({ DEFAULT: 'Company default', VODIDNUMBER: 'This number', CALLERNUMBER: "Caller's own number (forwarded calls)" }, r.cli.mode) + '</select>';
    if (d.email || d.fax) {
      h += '<label>Voicemail to email</label><select id="pn-m"' + dis + '>' + opts(EMAIL, d.email) + '</select>' +
        (d.emailSet === false ? '<div class="muted">No email address is set for this line.</div>' : '') +
        '<label>Fax file format</label><select id="pn-f"' + dis + '>' + opts(FAX, d.fax) + '</select>';
    } else h += '<label>Voicemail and fax</label><div class="muted">' + 'Not available for this line.' + '</div>';
    if (!ro) h += '<div style="margin-top:18px"><button type="button" class="btn btn-primary" id="pn-s">Save</button> <button type="button" class="btn btn-default" id="pn-cn">Cancel</button></div>';
    else h += '<div class="muted" style="margin-top:18px">Only your company administrator can change this number.</div>';
    panel.innerHTML = h; panel.classList.add('open'); shade.classList.add('open'); panel.setAttribute('aria-hidden', 'false');
    document.getElementById('pn-x').onclick = function (e) { e.preventDefault(); close(); };
    var rs = document.getElementById('pn-r'), ew = document.getElementById('pn-ew');
    var sync = function () { if (ew) ew.style.display = rs && rs.value === 'TRANSFER' ? '' : 'none'; };
    if (rs) rs.onchange = sync; sync();
    if (ro) return;
    document.getElementById('pn-cn').onclick = close;
    document.getElementById('pn-s').onclick = function () {
      var b = this, o = { id: r.i_account }, v;
      v = document.getElementById('pn-l').value.trim(); if (v !== (r.label || '')) o.label = v;
      if (rs && rs.value) { var ev = document.getElementById('pn-e').value; if (rs.value !== d.route || (rs.value === 'TRANSFER' && ev !== d.ext)) { o.route = rs.value; if (rs.value === 'TRANSFER') o.ext = ev; } }
      v = document.getElementById('pn-c').value; if (v !== r.cli.mode) o.cli = v;
      var m = document.getElementById('pn-m'), f = document.getElementById('pn-f');
      if (m && m.value !== d.email) o.email = m.value;
      if (f && f.value !== d.fax) o.fax = f.value;
      if (Object.keys(o).length === 1) { close(); return; }
      if (o.route && !confirm('Change where calls to ' + r.display + ' go? This affects callers right away.')) return;
      b.disabled = true;
      post('/user/phone-numbers/save', o).then(function () { try { sessionStorage.setItem('gvdNumMsg', 'Saved'); } catch (e) {} location.reload(); }, function (e) { b.disabled = false; alert(e.message); });
    };
  }
  document.querySelectorAll('.gvd-pn tr.pn-row').forEach(function (tr) { tr.onclick = function () { var r = byId[tr.getAttribute('data-id')]; if (r) open(r); }; });
})();
