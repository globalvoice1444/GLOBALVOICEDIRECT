(function () {
  var M = window.GVD_MA || {}, $ = function (i) { return document.getElementById(i); };
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  var esc = function (s) { var d = document.createElement('div'); d.textContent = s == null ? '' : String(s); return d.innerHTML; };
  function qrSvg(text) { var q = qrcode(0, 'M'); q.addData(text); q.make(); return q.createSvgTag({ cellSize: 4, margin: 2, scalable: true }); }
  // download QRs follow the configured store links
  document.querySelectorAll('img[data-qr]').forEach(function (img) {
    var u = img.getAttribute('data-qr'); if (!u || typeof qrcode !== 'function') return;
    var box = img.parentNode; try { box.innerHTML = qrSvg(u); box.firstChild.setAttribute('role', 'img'); box.firstChild.setAttribute('aria-label', img.alt); } catch (e) {}
  });
  function post(url, data) {
    var fd = new FormData(); fd.append('_token', token); Object.keys(data).forEach(function (k) { fd.append(k, data[k]); });
    return fetch(url, { method: 'POST', body: fd, credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' } })
      .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) throw new Error(j.error || j.message || (r.status === 419 ? 'Your sign-in has expired. Please refresh the page.' : 'Something went wrong.')); return j; }); });
  }
  var sel = $('ma-sel'); if (!sel || !M.admin) return;
  var st = $('ma-st'), why = $('ma-why'), res = $('ma-res'), bShow = $('ma-show'), bMail = $('ma-mail');
  function cur() { return sel.options[sel.selectedIndex]; }
  function upd() {
    var o = cur(), ok = o.getAttribute('data-ok') === '1';
    st.className = 'ma-pill ' + (ok ? 'ok' : 'no'); st.textContent = ok ? 'Ready' : 'Not available';
    bShow.disabled = bMail.disabled = !ok; res.innerHTML = '';
    if (ok) { why.style.display = 'none'; } else { why.style.display = ''; why.innerHTML = esc(o.getAttribute('data-why')) + '. <a href="/user/extras">Go to Extras</a>'; }
  }
  sel.onchange = upd; upd();
  bShow.onclick = function () {
    bShow.disabled = true; res.innerHTML = '<p class="ma-muted">Loading…</p>';
    post('/user/mobile-app/creds', { id: sel.value }).then(function (j) {
      var h = '<div class="ma-out">';
      if (j.qr) h += '<div><div class="ma-qr" id="ma-qr"></div><p class="ma-muted" style="text-align:center;margin-top:6px">Scan the QR code to sign in</p></div>';
      h += '<div class="ma-cred"><p class="ma-muted" style="margin-bottom:6px">' + (j.qr ? 'Or use the credentials' : 'Use these credentials') + '</p><table>';
      (j.rows || []).forEach(function (r, i) {
        h += '<tr><td>' + esc(r[0]) + '</td><td><code data-v="' + i + '">' + (r[2] ? '••••••••' : esc(r[1])) + '</code>' +
          (r[2] ? '<button type="button" class="ma-ic" data-eye="' + i + '" aria-label="Show ' + esc(r[0]) + '">&#128065;</button>' : '') +
          '<button type="button" class="ma-ic" data-cp="' + i + '" aria-label="Copy ' + esc(r[0]) + '">Copy</button></td></tr>';
      });
      res.innerHTML = h + '</table></div></div>';
      if (j.qr) $('ma-qr').innerHTML = qrSvg(j.qr);
      res.querySelectorAll('[data-eye]').forEach(function (b) { b.onclick = function () { var i = +b.getAttribute('data-eye'), c = res.querySelector('code[data-v="' + i + '"]'), on = c.getAttribute('data-shown') === '1';
        c.textContent = on ? '••••••••' : j.rows[i][1]; c.setAttribute('data-shown', on ? '0' : '1'); }; });
      res.querySelectorAll('[data-cp]').forEach(function (b) { b.onclick = function () { var v = j.rows[+b.getAttribute('data-cp')][1];
        (navigator.clipboard ? navigator.clipboard.writeText(v) : Promise.reject()).then(function () { b.textContent = 'Copied'; setTimeout(function () { b.textContent = 'Copy'; }, 1500); }, function () { b.textContent = 'Copy failed'; }); }; });
      bShow.disabled = false;
    }, function (e) { res.innerHTML = '<p class="ma-muted">' + esc(e.message) + '</p>'; bShow.disabled = false; });
  };
  bMail.onclick = function () {
    var name = cur().textContent;
    if (M.demo) { alert('This is a demo account, so emails are turned off.'); return; }
    if (!confirm('Email the mobile app setup to the user of ' + name + '?\n\nThey will get a QR code and sign-in details at the email address saved for this extension.')) return;
    bMail.disabled = true;
    post('/user/mobile-app/email', { id: sel.value, confirm: '1' }).then(function () { alert('Setup email sent for ' + name + '.'); bMail.disabled = false; },
      function (e) { alert(e.message); bMail.disabled = false; });
  };
})();
