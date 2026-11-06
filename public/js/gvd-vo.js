(function () {
  var V = window.GVD_VO || { page: '', admin: false, d: {} };
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };
  var $ = function (id) { return document.getElementById(id); };

  function post(url, data) {
    var fd = new FormData(); fd.append('_token', token);
    Object.keys(data).forEach(function (k) { var v = data[k]; if (Array.isArray(v)) v.forEach(function (x) { fd.append(k + '[]', x); }); else if (v !== undefined && v !== null) fd.append(k, typeof v === 'boolean' ? (v ? '1' : '0') : v); });
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) throw new Error(j.error || 'Something went wrong.'); return j; }); });
  }
  function getj(url, ms) {
    var ctl = window.AbortController ? new AbortController() : null, tm = ctl ? setTimeout(function () { ctl.abort(); }, ms || 20000) : null;
    return fetch(url, { credentials: 'same-origin', cache: 'no-store', headers: { 'Accept': 'application/json' }, signal: ctl ? ctl.signal : undefined })
      .then(function (r) { return r.text().then(function (t) { var j; try { j = JSON.parse(t); } catch (e) { throw new Error(r.status === 401 || r.redirected ? 'Your sign-in has expired. Please refresh the page.' : 'Could not load (' + r.status + ').'); }
        if (!r.ok) throw new Error(j.error || 'Not available'); return j; }); },
        function (e) { throw new Error(e && e.name === 'AbortError' ? 'This is taking too long.' : 'Network error.'); })
      .then(function (j) { clearTimeout(tm); return j; }, function (e) { clearTimeout(tm); throw e; });
  }
  function failBox(title, msg, retry) { open(title, '<p class="muted" style="margin-top:24px">' + esc(msg) + '</p><button type="button" class="btn btn-default btn-sm" id="vo-retry">Retry</button>'); $('vo-retry').onclick = retry; }
  function saved(msg) { try { sessionStorage.setItem('gvdVoMsg', msg || 'Saved'); } catch (e) {} location.reload(); }
  (function () { var m = null; try { m = sessionStorage.getItem('gvdVoMsg'); sessionStorage.removeItem('gvdVoMsg'); } catch (e) {}
    if (!m) return; var d = document.createElement('div'); d.setAttribute('role', 'status'); d.textContent = '\u2713 ' + m;
    d.style.cssText = 'position:fixed;top:16px;left:50%;transform:translateX(-50%);z-index:9999;background:#1e8e3e;color:#fff;padding:10px 18px;border-radius:6px;box-shadow:0 2px 8px rgba(0,0,0,.2);font-weight:600';
    document.body.appendChild(d); setTimeout(function () { d.remove(); }, 4000); })();

  var panel = $('vo-panel'), shade = $('vo-shade');
  function close() { if (!panel) return; panel.classList.remove('open'); shade.classList.remove('open'); panel.setAttribute('aria-hidden', 'true'); }
  function open(title, html) {
    panel.innerHTML = '<div style="display:flex;justify-content:space-between;align-items:center"><h5 style="margin:0">' + esc(title) + '</h5><a href="#" id="vo-x" aria-label="Close">&#10005;</a></div>' + html;
    panel.classList.add('open'); shade.classList.add('open'); panel.setAttribute('aria-hidden', 'false');
    $('vo-x').onclick = function (e) { e.preventDefault(); close(); };
  }
  if (shade) shade.onclick = close;
  document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  var spin = '<p style="margin-top:24px"><span class="vo-spin"></span>Loading…</p>';
  var dis = V.admin ? '' : ' disabled';
  function chk(id, label, on) { return '<label class="chk"><input type="checkbox" id="' + id + '"' + (on ? ' checked' : '') + dis + '> ' + esc(label) + '</label>'; }
  function saveBtn(id) { return V.admin ? '<div style="margin-top:16px"><button type="button" class="btn btn-primary" id="' + id + '">Save</button></div>' : ''; }
  function reveal(btnId, url, data, fields) {
    var b = $(btnId); if (!b) return;
    b.onclick = function (e) { e.preventDefault(); b.textContent = '…'; post(url, data).then(function (j) { fields.forEach(function (f) { var el = $(f[0]); if (el) el.textContent = j[f[1]] || '(none)'; }); b.remove(); }, function (er) { b.textContent = 'Show'; alert(er.message); }); };
  }

  // ---------- Recorded calls: delete ----------
  if (V.page === 'calls') {
    var del = $('vo-del'), all = $('vo-all'), sel = function () { return Array.prototype.slice.call(document.querySelectorAll('.vo-sel:checked')).map(function (c) { return c.value; }); };
    var upd = function () { if (del) del.disabled = sel().length === 0; };
    document.querySelectorAll('.vo-sel').forEach(function (c) { c.onchange = upd; });
    if (all) all.onchange = function () { document.querySelectorAll('.vo-sel').forEach(function (c) { c.checked = all.checked; }); upd(); };
    if (del) del.onclick = function () { var ids = sel(); if (!ids.length || !confirm('Delete ' + ids.length + ' recording(s)? This cannot be undone.')) return; del.disabled = true;
      post('/user/calls/recording/delete', { ids: ids }).then(function () { saved('Deleted'); }, function (e) { del.disabled = false; alert(e.message); }); };
  }

  // ---------- Extensions ----------
  if (V.page === 'extensions') {
    var FW = { N: 'Off', F: 'Forward to numbers' };
    document.querySelectorAll('tr.vo-row').forEach(function (tr) {
      tr.onclick = function () {
        var id = tr.getAttribute('data-id');
        var load = function (fresh) { open('Extension', spin);
        getj('/user/extensions/detail?id=' + id + (fresh ? '&fresh=1' : '')).then(function (x) {
          var rec = x.recording || { attr: {} }, a = rec.attr || {};
          var h = '<div class="muted">Ext ' + esc(x.ext) + ' · ' + esc(x.name) + '</div><div class="tabs" style="margin-top:10px"><a href="#" data-t="sip" class="on">Phone</a><a href="#" data-t="vm">Voicemail</a><a href="#" data-t="fw">Forwarding</a><a href="#" data-t="rec">Recording</a></div>';
          h += '<div data-s="sip"><label>Sign-in (SIP login)</label><div>' + esc(x.login) + '</div>' + (x.domain ? '<label>Domain</label><div>' + esc(x.domain) + '</div>' : '') +
            (V.admin ? '<label>Password</label><div><span id="vo-sp">••••••••</span> <a href="#" id="vo-spb">Show</a></div>' : '') +
            '<label>Status</label><div>' + esc(x.sipStatus ? JSON.stringify(x.sipStatus) : 'Not available') + '</div></div>';
          var opt = function (m, cur) { return Object.keys(m).map(function (k) { return '<option value="' + k + '"' + (k === cur ? ' selected' : '') + '>' + esc(m[k]) + '</option>'; }).join(''); };
          h += '<div data-s="vm" style="display:none"><div id="vo-vmbox">' + spin + '</div></div>';
          var fwOpts = Object.assign({}, FW); if (x.forward && !fwOpts[x.forward]) fwOpts[x.forward] = 'Current (' + x.forward + ')';
          h += '<div data-s="fw" style="display:none"><label>Forwarding</label><select id="vo-fm"' + dis + '>' + opt(fwOpts, x.forward) + '</select>' +
            '<label>Ring order</label><select id="vo-sq"' + dis + '>' + opt({ Order: 'One by one', Simultaneous: 'All at once', Random: 'Random' }, x.sequence) + '</select>' +
            '<label>Forward to</label><div id="vo-nums"></div>' + (V.admin ? '<a href="#" id="vo-addn">+ Add number</a>' : '') + saveBtn('vo-sfw') + '</div>';
          h += '<div data-s="rec" style="display:none">' + chk('vo-ron', 'Call recording on', rec.on) + chk('vo-rin', 'Record incoming calls', a.call_recording_in && a.call_recording_in !== '0') +
            chk('vo-rout', 'Record outgoing calls', a.call_recording_out && a.call_recording_out !== '0') + chk('vo-rre', 'Record transferred calls', a.call_recording_redirect && a.call_recording_redirect !== '0') +
            chk('vo-rod', 'Record on demand', a.on_demand === 'Y') + chk('vo-rml', 'Email recordings', a.mail_recording === 'Y') + chk('vo-rau', 'Play a recording notice', a.audible_notification === 'Y') + saveBtn('vo-srec') + '</div>';
          open('Extension ' + x.ext, h);
          panel.querySelectorAll('.tabs a').forEach(function (t) { t.onclick = function (e) { e.preventDefault(); panel.querySelectorAll('.tabs a').forEach(function (z) { z.classList.toggle('on', z === t); }); panel.querySelectorAll('[data-s]').forEach(function (s) { s.style.display = s.getAttribute('data-s') === t.getAttribute('data-t') ? '' : 'none'; }); }; });
          reveal('vo-spb', '/user/extensions/secret', { id: id }, [['vo-sp', 'value']]);
          var go = function (b, data) { b.disabled = true; post('/user/extensions/save', Object.assign({ id: id }, data)).then(function () { saved(); }, function (e) { b.disabled = false; alert(e.message); }); };
          var loadVm = function () { var box = $('vo-vmbox'); if (!box) return; box.innerHTML = spin;
            getj('/user/extensions/detail?part=vm&id=' + id + (fresh ? '&fresh=1' : '')).then(function (r) { var vm = r.vm; if (!$('vo-vmbox')) return;
              if (!vm) { box.innerHTML = '<p class="muted">Not available for this extension.</p>'; return; }
              box.innerHTML = '<label>Email address</label><input type="email" id="vo-em" value="' + esc(vm.ext_email || '') + '"' + dis + '>' +
                '<label>Voicemail to email</label><select id="vo-ea"' + dis + '>' + opt({ none: 'Off', forward: 'Forward', notify: 'Notify only', copy: 'Copy', fwd_as_attachment: 'Forward as attachment' }, vm.ext_email_action) + '</select>' +
                '<label>Fax file format</label><select id="vo-ff"' + dis + '>' + opt({ pdf: 'Single PDF file', tiff: 'Single TIFF file', multi_tiff: 'One TIFF per page', multi_png: 'One PNG per page' }, vm.fax_file) + '</select>' + saveBtn('vo-svm');
              if ($('vo-svm')) $('vo-svm').onclick = function () { go(this, { section: 'voicemail', ext_email: $('vo-em').value, ext_email_action: $('vo-ea').value, fax_file: $('vo-ff').value }); };
            }, function (e) { if (!$('vo-vmbox')) return; box.innerHTML = '<p class="muted">' + esc(e.message) + '</p><a href="#" id="vo-vmr">Retry</a>'; $('vo-vmr').onclick = function (ev) { ev.preventDefault(); loadVm(); }; }); };
          loadVm();
          var nums = (x.numbers || []).slice();
          var draw = function () { $('vo-nums').innerHTML = nums.map(function (n, i) { return '<div style="display:flex;gap:6px;margin:4px 0"><input type="text" data-i="' + i + '" data-f="number" value="' + esc(n.number) + '" placeholder="Number"' + dis + ' style="flex:2">' +
            '<input type="number" data-i="' + i + '" data-f="timeout" value="' + esc(n.timeout || 20) + '" title="Ring seconds"' + dis + ' style="width:70px"><label class="chk" style="margin:0"><input type="checkbox" data-i="' + i + '" data-f="active"' + (n.active ? ' checked' : '') + dis + '>on</label>' +
            (V.admin ? '<a href="#" data-rm="' + i + '">&#10005;</a>' : '') + '</div>'; }).join('') || '<p class="muted">No numbers.</p>';
            $('vo-nums').querySelectorAll('input').forEach(function (el) { el.onchange = function () { var n = nums[+el.getAttribute('data-i')], f = el.getAttribute('data-f'); n[f] = el.type === 'checkbox' ? el.checked : el.value; }; });
            $('vo-nums').querySelectorAll('[data-rm]').forEach(function (a) { a.onclick = function (e) { e.preventDefault(); nums.splice(+a.getAttribute('data-rm'), 1); draw(); }; }); };
          draw();
          if (!V.admin) return;
          $('vo-addn').onclick = function (e) { e.preventDefault(); nums.push({ id: 0, number: '', timeout: 20, active: true }); draw(); };
          $('vo-sfw').onclick = function () { if (!confirm('Change where calls to extension ' + x.ext + ' are forwarded?')) return; go(this, { section: 'forwarding', mode: $('vo-fm').value, sequence: $('vo-sq').value, numbers: JSON.stringify(nums) }); };
          $('vo-srec').onclick = function () { go(this, { section: 'recording', on: $('vo-ron').checked, 'in': $('vo-rin').checked, out: $('vo-rout').checked, redirect: $('vo-rre').checked, on_demand: $('vo-rod').checked, mail: $('vo-rml').checked, audible: $('vo-rau').checked }); };
        }).catch(function (e) { failBox('Extension', e.message || 'Could not load this extension.', function () { load(true); }); });
        };
        load(false);
      };
    });
  }

  // ---------- Conference rooms ----------
  if (V.page === 'conferences') {
    var rooms = (V.d.rooms || []);
    var form = function (r) {
      r = r || {};
      var h = '<label>Room name</label><input type="text" id="vo-cn" maxlength="64" value="' + esc(r.name || '') + '"' + dis + '>' +
        '<label>Participant PIN</label><div>' + (r.i_conf ? '<span id="vo-p1">' + (r.hasPin ? '••••' : '(none)') + '</span> ' + (V.admin && r.hasPin ? '<a href="#" id="vo-pb">Show PINs</a>' : '') : '') + '</div>' +
        (V.admin ? '<input type="text" id="vo-pin" placeholder="' + (r.i_conf ? 'New PIN (leave empty to keep)' : 'PIN (digits)') + '">' : '') +
        '<label>Moderator PIN</label><div>' + (r.i_conf ? '<span id="vo-p2">' + (r.hasModPin ? '••••' : '(none)') + '</span>' : '') + '</div>' +
        (V.admin ? '<input type="text" id="vo-mpin" placeholder="' + (r.i_conf ? 'New moderator PIN (leave empty to keep)' : 'Moderator PIN (digits)') + '">' : '') +
        '<label>Maximum participants</label><input type="number" id="vo-mx" value="' + esc(r.max_participants || '') + '"' + dis + '>' +
        '<label>Maximum duration (minutes)</label><input type="number" id="vo-md" value="' + esc(r.max_duration || '') + '"' + dis + '>' +
        chk('vo-w', 'Wait for moderator', r.wait_for_moderator === 'Y') + chk('vo-c', 'Close when participants leave', r.close_after_participants_exit === 'Y') +
        chk('vo-an', 'Announce names', r.announce_name === 'Y') + chk('vo-aj', 'Announce join / leave', r.announce_join_leave === 'Y') +
        chk('vo-ac', 'Announce number of participants', r.announce_number_participants === 'Y') + chk('vo-r', 'Record conferences', r.record === 'Y') +
        (V.admin ? '<div style="margin-top:16px"><button type="button" class="btn btn-primary" id="vo-cs">Save</button>' + (r.i_conf ? ' <button type="button" class="btn btn-danger" id="vo-cd">Delete room</button>' : '') + '</div>' : '');
      open(r.i_conf ? r.name : 'New conference room', h);
      if (r.i_conf) reveal('vo-pb', '/user/conference-rooms/pins', { i_conf: r.i_conf }, [['vo-p1', 'pin'], ['vo-p2', 'moderator_pin']]);
      if (!V.admin) return;
      $('vo-cs').onclick = function () { var b = this; b.disabled = true;
        post('/user/conference-rooms/save', { i_conf: r.i_conf || '', name: $('vo-cn').value, pin: $('vo-pin').value, moderator_pin: $('vo-mpin').value, max_participants: $('vo-mx').value, max_duration: $('vo-md').value,
          wait_for_moderator: $('vo-w').checked, close_after_participants_exit: $('vo-c').checked, announce_name: $('vo-an').checked, announce_join_leave: $('vo-aj').checked, announce_number_participants: $('vo-ac').checked, record: $('vo-r').checked })
          .then(function () { saved(); }, function (e) { b.disabled = false; alert(e.message); }); };
      if ($('vo-cd')) $('vo-cd').onclick = function () { if (!confirm('Delete this conference room?')) return; post('/user/conference-rooms/save', { i_conf: r.i_conf, 'delete': 1 }).then(function () { saved('Deleted'); }, function (e) { alert(e.message); }); };
    };
    if ($('vo-add')) $('vo-add').onclick = function () { form(null); };
    document.querySelectorAll('tr.vo-row').forEach(function (tr) { tr.onclick = function () { var id = +tr.getAttribute('data-id'); form(rooms.filter(function (r) { return r.i_conf === id; })[0]); }; });
  }

  // ---------- Call block ----------
  if (V.page === 'callblock') {
    var T = V.d.targets || [];
    var picker = function (on) {
      return '<label>Applies to</label>' + T.map(function (t) { var c = on ? on.indexOf(t.i_account) >= 0 : !t.guarded;
        return '<label class="chk"><input type="checkbox" class="vo-t" value="' + t.i_account + '"' + (c ? ' checked' : '') + '> ' + esc(t.label) + '</label>'; }).join('');
    };
    var picked = function () { return Array.prototype.slice.call(panel.querySelectorAll('.vo-t:checked')).map(function (c) { return c.value; }); };
    var titles = { blocked: 'Block a number', areas: 'Block an area code', allowed: 'Allow a number' };
    document.querySelectorAll('.vo-cb-add').forEach(function (a) { a.onclick = function (e) { e.preventDefault(); var sec = a.getAttribute('data-sec');
      open(titles[sec], '<label>' + (sec === 'areas' ? 'Area code (3 digits)' : 'Phone number (with country code)') + '</label><input type="text" id="vo-n">' + picker(null) + '<div style="margin-top:16px"><button type="button" class="btn btn-primary" id="vo-cbs">Save</button></div>');
      $('vo-cbs').onclick = function () { var b = this, t = picked(); if (!t.length) { alert('Choose at least one number.'); return; } b.disabled = true;
        post('/user/call-block/save', { section: sec, action: 'add', number: $('vo-n').value, targets: t }).then(function () { saved(); }, function (er) { b.disabled = false; alert(er.message); }); }; }; });
    document.querySelectorAll('.vo-cb-del').forEach(function (a) { a.onclick = function (e) { e.preventDefault(); var sec = a.getAttribute('data-sec'), num = a.getAttribute('data-num');
      var item = ((V.d.lists || {})[sec] || []).filter(function (x) { return x.number === num; })[0] || { on: [] };
      open('Remove ' + num, '<p>Remove from which lines?</p>' + picker(item.on) + '<div style="margin-top:16px"><button type="button" class="btn btn-danger" id="vo-cbr">Remove</button></div>');
      $('vo-cbr').onclick = function () { var b = this, t = picked(); if (!t.length) { alert('Choose at least one number.'); return; } b.disabled = true;
        post('/user/call-block/save', { section: sec, action: 'remove', number: num, targets: t }).then(function () { saved('Removed'); }, function (er) { b.disabled = false; alert(er.message); }); }; }; });
  }

  // ---------- Users ----------
  if (V.page === 'users') {
    var U = V.d.users || [];
    var uform = function (u) {
      u = u || {};
      var h = '<label>First name</label><input type="text" id="vo-uf" value="' + esc(u.first || '') + '"' + dis + '>' + '<label>Last name</label><input type="text" id="vo-ul" value="' + esc(u.last || '') + '"' + dis + '>' +
        '<label>Email' + (u.i_account ? '' : ' (also the sign-in)') + '</label><input type="email" id="vo-ue" value="' + esc(u.email || '') + '"' + dis + '>' +
        (u.i_account ? '<label>Sign-in</label><div>' + esc(u.login) + '</div><label>Status</label><div>' + esc(u.status) + '</div>' + (V.admin ? '<label>Password</label><div><span id="vo-upw">••••••••</span> <a href="#" id="vo-upb">Show</a></div>' : '')
          : '<label>Password</label><input type="password" id="vo-up" autocomplete="new-password">') + saveBtn('vo-us');
      open(u.i_account ? (u.first + ' ' + u.last) : 'New user', h);
      if (u.i_account) reveal('vo-upb', '/user/users/secret', { id: u.i_account }, [['vo-upw', 'value']]);
      if (!V.admin) return;
      $('vo-us').onclick = function () { var b = this; b.disabled = true;
        post('/user/users/save', { id: u.i_account || '', first: $('vo-uf').value, last: $('vo-ul').value, email: $('vo-ue').value, password: u.i_account ? undefined : $('vo-up').value })
          .then(function () { saved(); }, function (e) { b.disabled = false; alert(e.message); }); };
    };
    if ($('vo-add')) $('vo-add').onclick = function () { uform(null); };
    document.querySelectorAll('tr.vo-row').forEach(function (tr) { tr.onclick = function () { var id = +tr.getAttribute('data-id'); uform(U.filter(function (u) { return u.i_account === id; })[0]); }; });
  }
  if (V.page === 'extras') {
    document.querySelectorAll('input.vx-t').forEach(function (cb) {
      cb.onchange = function () {
        var on = cb.checked, n = cb.getAttribute('data-name');
        var msg = on ? 'Enable ' + n + ' subscription for $' + cb.getAttribute('data-fee') + ' per month?' : 'Disable ' + n + ' subscription?';
        if (!confirm('Please confirm your selection\n\n' + msg)) { cb.checked = !on; return; }
        cb.disabled = true;
        post('/user/extras/toggle', { code: cb.getAttribute('data-code'), on: on }).then(function () { location.reload(); },
          function (e) { cb.checked = !on; cb.disabled = false; alert(e.message); });
      };
    });
  }

  // ---------- Fax inbox / Send fax ----------
  if (V.page === 'fax' || V.page === 'faxout') {
    var out = V.page === 'faxout', box = $('fx-box');
    var fileUrl = function (r, dl) { return '/user/fax/live/' + encodeURIComponent(r.ia) + '/' + encodeURIComponent(r.uid) + '?' + (out ? 'kind=out&' : '') + (dl ? 'download=1' : ''); };
    var load = function (fresh) {
      if (!box) return;
      box.innerHTML = '<p style="margin-top:8px"><span class="vo-spin"></span>Loading…</p>';
      getj((out ? '/user/fax/sent' : '/user/fax/list') + (fresh ? '?fresh=1' : ''), 45000).then(function (j) {
        var rows = j.rows || [];
        if (!rows.length) { box.innerHTML = '<p class="muted">' + (out ? 'No faxes sent in the last 30 days.' : 'No faxes in your inbox yet. New faxes will show up here.') + '</p>'; return; }
        var h = '<div class="table-responsive"><table class="table table-hover"><thead><tr>' + (out ? '<th>Fax</th><th>From</th><th>Time (ET)</th><th>To</th><th>Duration</th><th>Status</th>' : '<th>From</th><th>To</th><th>Date (ET)</th><th>Pages</th><th>Status</th><th></th>') + '</tr></thead><tbody>';
        rows.forEach(function (r, i) {
          if (out) {
            h += '<tr><td>' + (r.uid ? '<a href="' + esc(fileUrl(r, false)) + '" target="_blank" rel="noopener">View</a> · <a href="' + esc(fileUrl(r, true)) + '">Download</a>' : '<span class="muted">—</span>') + '</td><td>' + esc(r.from) + '</td><td>' + esc(r.date) + '</td><td>' + esc(r.to) + '</td><td>' + esc(r.dur || '-') + '</td><td><span class="st' + (!r.failed ? ' on' : '') + '">' + esc(r.status) + '</span></td></tr>';
          } else {
            h += '<tr' + (r.seen ? '' : ' class="fx-new"') + '><td>' + (r.seen ? '' : '<span class="fx-dot" title="Unread"></span>') + esc(r.from) + '</td><td>' + esc(r.to) + '</td><td>' + esc(r.date) + '</td><td>' + (r.pages == null ? '-' : esc(r.pages)) + '</td><td>' + (r.seen ? 'Read' : 'Unread') + '</td>' +
              '<td style="white-space:nowrap"><a class="btn btn-primary btn-sm" href="' + esc(fileUrl(r, false)) + '" target="_blank" rel="noopener">View</a> <a class="btn btn-default btn-sm" href="' + esc(fileUrl(r, true)) + '">Download</a>' +
              (V.admin ? ' <button type="button" class="btn btn-danger btn-sm fx-del" data-i="' + i + '">Delete</button>' : '') + '</td></tr>';
          }
        });
        box.innerHTML = h + '</tbody></table></div>';
        if (!out) document.dispatchEvent(new CustomEvent('gvd:faxes', { detail: rows }));
        box.querySelectorAll('.fx-del').forEach(function (b) {
          b.onclick = function () {
            var r = rows[+b.getAttribute('data-i')];
            if (!confirm('Delete this fax?\n\n' + r.from + '  ' + r.date + '\n\nThis cannot be undone.')) return;
            b.disabled = true;
            post('/user/fax/delete', { ia: r.ia, uid: r.uid }).then(function () { saved('Fax deleted'); }, function (e) { b.disabled = false; alert(e.message); });
          };
        });
      }, function (e) { box.innerHTML = '<p class="muted">' + esc(e.message) + '</p><button type="button" class="btn btn-default btn-sm" id="fx-retry">Retry</button>'; $('fx-retry').onclick = function () { load(true); }; });
    };
    load(false);
    if ($('fx-refresh')) $('fx-refresh').onclick = function () { load(true); };
    if ($('fx-send')) $('fx-send').onclick = function () {
      open('Send fax', '<label for="fx-to">Destination number *</label><input type="text" id="fx-to" autocomplete="off" placeholder="(555) 123-4567">' +
        '<label for="fx-file">File * (pdf, tiff, jpg, jpeg)</label><input type="file" id="fx-file" accept=".pdf,.tif,.tiff,.jpg,.jpeg,application/pdf,image/tiff,image/jpeg">' +
        '<label class="chk" style="margin-top:14px"><input type="checkbox" id="fx-save" checked> Save file in fax history</label>' +
        '<p class="muted" id="fx-msg" style="min-height:18px"></p>' +
        '<div style="margin-top:12px;display:flex;gap:8px;justify-content:flex-end"><button type="button" class="btn btn-default" id="fx-cancel">Cancel</button><button type="button" class="btn btn-primary" id="fx-go">Send</button></div>');
      $('fx-cancel').onclick = close;
      $('fx-go').onclick = function () {
        var to = $('fx-to').value.trim(), f = $('fx-file').files[0], msg = $('fx-msg');
        if (!/^\+?[0-9() -]+$/.test(to) || to.replace(/\D/g, '').length < 10) { msg.textContent = 'Enter a valid fax number.'; return; }
        if (!f) { msg.textContent = 'Choose a file to send.'; return; }
        if (!/\.(pdf|tiff?|jpe?g)$/i.test(f.name)) { msg.textContent = 'File must be PDF, TIFF, JPG or JPEG.'; return; }
        var go = $('fx-go'); go.disabled = true; msg.innerHTML = '<span class="vo-spin"></span>Uploading…';
        var dry = /[?&]dry=1/.test(location.search);
        post('/user/fax/send', { destination: to, file: f, save: $('fx-save').checked, dry: dry ? 1 : undefined }).then(function (j) {
          if (j.dry) { go.disabled = false; msg.textContent = 'Test only, nothing sent: ' + JSON.stringify({ method: j.method, params: j.params, upload_file: j.upload_file }); return; }
          close(); saved('Sending fax… it will appear here when done');
        }, function (e) { go.disabled = false; msg.textContent = e.message; });
      };
    };
  }

  // ---------- Dial out numbers ----------
  if (V.page === 'dialout') {
    var DR = V.d.rows || [], DD = V.d.dids || [];
    var dform = function (r) {
      r = r || { i_account: 0, number: '', nickname: '', show: DD.length ? DD[0].value : '', active: true };
      var lock = !V.admin || r.guarded ? ' disabled' : '';
      var h = '<label>Calling from</label><input type="text" id="do-n" inputmode="tel" maxlength="20" placeholder="e.g. 15551234567" value="' + esc(r.number) + '"' + lock + '>' +
        '<label>Nickname</label><input type="text" id="do-k" maxlength="40" value="' + esc(r.nickname) + '"' + lock + '>' +
        '<label>Show number as</label><select id="do-s"' + lock + '>' + DD.map(function (x) { return '<option value="' + esc(x.value) + '"' + (x.value === r.show ? ' selected' : '') + '>' + esc(x.label) + '</option>'; }).join('') + '</select>' +
        '<label class="chk" style="margin-top:14px"><input type="checkbox" id="do-a"' + (r.active ? ' checked' : '') + lock + '> Active</label>' +
        (V.admin && !r.guarded ? '<div style="margin-top:16px"><button type="button" class="btn btn-primary" id="do-save">Save</button>' + (r.i_account ? ' <button type="button" class="btn btn-danger" id="do-del">Delete</button>' : '') + '</div><p class="muted" id="do-msg" style="margin-top:8px"></p>' : '');
      open(r.i_account ? 'Edit dial out number' : 'Add dial out number', h);
      if (!$('do-save')) return;
      $('do-save').onclick = function () { var b = this; b.disabled = true; $('do-msg').textContent = '';
        post('/user/dial-out-numbers/save', { action: r.i_account ? 'update' : 'add', i_account: r.i_account || '', number: $('do-n').value, nickname: $('do-k').value, show: $('do-s').value, active: $('do-a').checked })
          .then(function () { saved(); }, function (e) { b.disabled = false; $('do-msg').textContent = e.message; }); };
      if ($('do-del')) $('do-del').onclick = function () { if (!confirm('Delete this dial out number?')) return;
        post('/user/dial-out-numbers/save', { action: 'delete', i_account: r.i_account }).then(function () { saved('Deleted'); }, function (e) { $('do-msg').textContent = e.message; }); };
    };
    if ($('vo-add')) $('vo-add').onclick = function () { if (!DD.length) { alert('You need at least one phone number to show as caller ID.'); return; } dform(null); };
    document.querySelectorAll('tr.vo-row').forEach(function (tr) { tr.onclick = function () { var id = +tr.getAttribute('data-id'); dform(DR.filter(function (r) { return r.i_account === id; })[0]); }; });
    document.querySelectorAll('.vo-act').forEach(function (c) { c.onchange = function () { var on = c.checked; c.disabled = true;
      post('/user/dial-out-numbers/save', { action: 'toggle', i_account: c.getAttribute('data-id'), active: on }).then(function () { saved(on ? 'Activated' : 'Deactivated'); }, function (e) { c.checked = !on; c.disabled = false; alert(e.message); }); }; });
  }

  // ---------- WebRTC: which extension the browser phone uses (kept in this browser, like the Virtual Office) ----------
  if (V.page === 'webrtc') {
    var cur = null; try { cur = localStorage.getItem('gvdPhoneExt'); } catch (e) {}
    var keys = (V.d.exts || []).map(function (e) { return e.key; });
    if (keys.indexOf(cur) < 0) cur = '';
    var mark = function () { document.querySelectorAll('#wr-list li').forEach(function (li) { var i = li.querySelector('input'); li.classList.toggle('on', i.checked); }); };
    document.querySelectorAll('#wr-list input').forEach(function (i) { i.checked = i.value === cur; i.onchange = function () {
      try { if (i.value) { localStorage.setItem('gvdPhoneExt', i.value); localStorage.removeItem('gvdPhoneOff'); } else { localStorage.removeItem('gvdPhoneExt'); localStorage.setItem('gvdPhoneOff', '1'); } } catch (e) {}
      mark(); if (window.GvdDock) { if (i.value) GvdDock.select(i.parentNode.textContent.trim()); else GvdDock.off(); } var m = $('wr-msg'); if (m) m.textContent = i.value ? 'Saved. The phone will use ' + i.parentNode.textContent.trim() + '.' : 'Saved. The browser phone is off.'; }; });
    mark();
    if (cur && window.GvdDock) { var ci = document.querySelector('#wr-list input:checked'); if (ci) GvdDock.select(ci.parentNode.textContent.trim()); }
    getj('/user/phone/balance', 15000).then(function (j) { var b = $('wr-bal'); if (!b || j.balance === null || j.balance === undefined) return; b.textContent = 'Balance: ' + j.balance + (j.currency ? ' ' + j.currency : ''); b.style.display = ''; }, function () {});
  }
})();
