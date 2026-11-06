/* Fax dashboard (presentation only, 2026-11-01): stat cards from /user/fax/list + /user/fax/sent, inline compose posting to the
   existing POST /user/fax/send {destination, file, save[, dry]}. Photos are combined client-side into one PDF (endpoint takes PDF/TIFF/JPG). */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var token = (document.querySelector('meta[name="_token"]') || {}).content || '';
  var getj = function (u) { return fetch(u, { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } }).then(function (r) { if (!r.ok) throw 0; return r.json(); }); };
  var set = function (id, v) { var e = $(id); if (e) e.textContent = v; };

  // ---- stats (real data only; "–" if unavailable)
  function inStats(rows) {
    var wk = Date.now() / 1000 - 7 * 86400, n7 = 0, pg = 0, anyPg = false;
    rows.forEach(function (r) { if ((+r.ts || 0) >= wk) n7++; if (r.pages != null) { pg += +r.pages || 0; anyPg = true; } });
    set('fd-in', rows.length); set('fd-7', n7); set('fd-pg', anyPg ? pg : '–');
  }
  var gotIn = false;
  document.addEventListener('gvd:faxes', function (e) { gotIn = true; inStats(e.detail || []); });
  // until the live inbox answers, show the saved copies kept in the account (real rows on this page)
  var saved = [].map.call(document.querySelectorAll('#fx-saved tr[data-ts]'), function (tr) { var p = parseInt(tr.children[3].textContent, 10); return { ts: +tr.getAttribute('data-ts'), pages: isNaN(p) ? null : p }; });
  if (saved.length) inStats(saved);
  if ($('fd-in')) {
    if (!(window.GVD_VO && window.GVD_VO.page === 'fax')) getj('/user/fax/list').then(function (j) { if (!gotIn) inStats(j.rows || []); }, function () {});
    getj('/user/fax/sent').then(function (j) { set('fd-out', (j.rows || []).length); }, function () {});
  }

  // images -> one Letter-size PDF (each photo scaled to fit, JPEG embedded via DCTDecode)
  function toJpeg(f) {
    return new Promise(function (ok, bad) {
      var im = new Image(); im.onload = function () {
        var s = Math.min(1, 2000 / Math.max(im.naturalWidth, im.naturalHeight)), w = Math.round(im.naturalWidth * s), h = Math.round(im.naturalHeight * s);
        var c = document.createElement('canvas'); c.width = w; c.height = h; var x = c.getContext('2d'); x.fillStyle = '#fff'; x.fillRect(0, 0, w, h); x.drawImage(im, 0, 0, w, h);
        var bin = atob(c.toDataURL('image/jpeg', 0.85).split(',')[1]), u = new Uint8Array(bin.length); for (var i = 0; i < bin.length; i++) u[i] = bin.charCodeAt(i);
        ok({ w: w, h: h, d: u });
      }; im.onerror = function () { bad(new Error('A photo could not be read. Try another one.')); }; im.src = URL.createObjectURL(f);
    });
  }
  function buildPdf(imgs) {
    var parts = [], len = 0, offs = [], enc = function (s) { var u = new Uint8Array(s.length); for (var i = 0; i < s.length; i++) u[i] = s.charCodeAt(i) & 255; return u; };
    var push = function (x) { if (typeof x === 'string') x = enc(x); parts.push(x); len += x.length; };
    var obj = function (n, body) { offs[n] = len; push(n + ' 0 obj\n'); body.forEach(push); push('\nendobj\n'); };
    var n = imgs.length, kids = []; for (var i = 0; i < n; i++) kids.push((3 + i * 3) + ' 0 R');
    push('%PDF-1.4\n%\xE2\xE3\xCF\xD3\n');
    obj(1, ['<< /Type /Catalog /Pages 2 0 R >>']);
    obj(2, ['<< /Type /Pages /Kids [' + kids.join(' ') + '] /Count ' + n + ' >>']);
    imgs.forEach(function (im, i) {
      var p = 3 + i * 3, W = 612, H = 792, m = 18, s = Math.min((W - 2 * m) / im.w, (H - 2 * m) / im.h), dw = im.w * s, dh = im.h * s, x = (W - dw) / 2, y = (H - dh) / 2;
      var cs = 'q ' + dw.toFixed(2) + ' 0 0 ' + dh.toFixed(2) + ' ' + x.toFixed(2) + ' ' + y.toFixed(2) + ' cm /Im0 Do Q';
      obj(p, ['<< /Type /Page /Parent 2 0 R /MediaBox [0 0 612 792] /Resources << /XObject << /Im0 ' + (p + 1) + ' 0 R >> >> /Contents ' + (p + 2) + ' 0 R >>']);
      obj(p + 1, ['<< /Type /XObject /Subtype /Image /Width ' + im.w + ' /Height ' + im.h + ' /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length ' + im.d.length + ' >>\nstream\n', im.d, '\nendstream']);
      obj(p + 2, ['<< /Length ' + cs.length + ' >>\nstream\n' + cs + '\nendstream']);
    });
    var total = 3 + n * 3, xref = len, t = 'xref\n0 ' + total + '\n0000000000 65535 f \n';
    for (var k = 1; k < total; k++) t += String(offs[k]).padStart(10, '0') + ' 00000 n \n';
    push(t + 'trailer\n<< /Size ' + total + ' /Root 1 0 R >>\nstartxref\n' + xref + '\n%%EOF\n');
    return new Blob(parts, { type: 'application/pdf' });
  }
  window.GVD_FAX_PDF = function (files) { return Promise.all([].map.call(files, toJpeg)).then(buildPdf); }; // used by tests


  // ---- compose
  var go = $('fd-go'); if (!go) return;
  var msg = $('fd-msg'), file = null, photos = [];
  var say = function (t, c) { msg.textContent = t; msg.className = 'fd-msg' + (c ? ' ' + c : ''); };
  function render() {
    $('fd-fname').textContent = file ? file.name : (photos.length ? photos.length + ' photo page' + (photos.length > 1 ? 's' : '') + ', sent as one PDF' : '');
    var box = $('fd-thumbs'); box.innerHTML = '';
    photos.forEach(function (p, i) {
      var d = document.createElement('div'); d.className = 'fd-th';
      d.innerHTML = '<img alt=""><span class="n">' + (i + 1) + '</span><div class="a"><button type="button" title="Move left">&larr;</button><button type="button" title="Remove">&times;</button><button type="button" title="Move right">&rarr;</button></div>';
      d.querySelector('img').src = p.url;
      var b = d.querySelectorAll('button');
      b[0].disabled = i === 0; b[2].disabled = i === photos.length - 1;
      b[0].onclick = function () { photos.splice(i - 1, 0, photos.splice(i, 1)[0]); render(); };
      b[2].onclick = function () { photos.splice(i + 1, 0, photos.splice(i, 1)[0]); render(); };
      b[1].onclick = function () { URL.revokeObjectURL(p.url); photos.splice(i, 1); render(); };
      box.appendChild(d);
    });
  }
  function addPhotos(list) {
    [].slice.call(list || []).forEach(function (f) { if (/^image\//.test(f.type)) photos.push({ f: f, url: URL.createObjectURL(f) }); });
    if (photos.length) file = null; render(); say('');
  }
  function pickFile(f) {
    if (!f) return;
    if (!/\.(pdf|tiff?|jpe?g)$/i.test(f.name)) { say('File must be PDF, TIFF, JPG or JPEG.', 'err'); return; }
    if (f.size > 20 * 1024 * 1024) { say('File is too large (20 MB max).', 'err'); return; }
    photos.forEach(function (p) { URL.revokeObjectURL(p.url); }); photos = []; file = f; render(); say('');
  }
  $('fd-file').onchange = function () { pickFile(this.files[0]); this.value = ''; };
  $('fd-cam').onchange = function () { addPhotos(this.files); this.value = ''; };
  $('fd-photos').onchange = function () { addPhotos(this.files); this.value = ''; };
  var drop = $('fd-drop');
  ['dragenter', 'dragover'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.add('over'); }); });
  ['dragleave', 'drop'].forEach(function (ev) { drop.addEventListener(ev, function (e) { e.preventDefault(); drop.classList.remove('over'); }); });
  drop.addEventListener('drop', function (e) { var fs = e.dataTransfer.files; if (!fs.length) return; if (/^image\//.test(fs[0].type) && !/jpe?g|tiff?/i.test(fs[0].type)) addPhotos(fs); else pickFile(fs[0]); });

  go.onclick = function () {
    var to = $('fd-to').value.trim(), d = to.replace(/\D/g, '');
    if (!/^\+?[0-9() -]+$/.test(to) || d.length < 10 || d.length > 15) { say('Enter a valid fax number.', 'err'); return; }
    if (!file && !photos.length) { say('Choose a file to send.', 'err'); return; }
    go.disabled = true; say(photos.length ? 'Preparing pages…' : 'Uploading…');
    (photos.length ? Promise.all(photos.map(function (p) { return toJpeg(p.f); })).then(buildPdf).then(function (b) { return new File([b], 'fax-photos.pdf', { type: 'application/pdf' }); }) : Promise.resolve(file)).then(function (f) {
      if (f.size > 20 * 1024 * 1024) throw new Error('File is too large (20 MB max). Try fewer pages.');
      say('Uploading…');
      var fd = new FormData(); fd.append('_token', token); fd.append('destination', to); fd.append('file', f); fd.append('save', $('fd-save').checked ? '1' : '0');
      if (/[?&]dry=1/.test(location.search)) fd.append('dry', '1');
      return fetch('/user/fax/send', { method: 'POST', body: fd, credentials: 'same-origin', cache: 'no-store', headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' } })
        .then(function (r) { return r.json().catch(function () { return {}; }).then(function (j) { if (!r.ok) throw new Error(j.error || 'The fax could not be sent.'); return j; }); });
    }).then(function (j) {
      go.disabled = false;
      if (j.dry) { say('Test only, nothing sent.', 'ok'); return; }
      say('Sending fax… it will show under Sent when done.', 'ok');
      $('fd-to').value = ''; file = null; photos.forEach(function (p) { URL.revokeObjectURL(p.url); }); photos = []; render();
    }, function (e) { go.disabled = false; say(e.message || 'The fax could not be sent.', 'err'); });
  };
})();
