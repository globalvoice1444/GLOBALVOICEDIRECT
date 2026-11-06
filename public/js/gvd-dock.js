/* Docked browser phone (2026-11-01). Loads the app's own phone engine on demand; never uses any other site. */
(function () {
  var $ = function (id) { return document.getElementById(id); };
  var dock = $('gvd-dock'); if (!dock) return;
  var picked = false, btn = $('gp-dock-btn'), demo = dock.getAttribute('data-demo') === '1', loaded = false, balDone = false;
  function load(src) { return new Promise(function (ok, no) { var s = document.createElement('script'); s.src = src; s.onload = ok; s.onerror = no; document.body.appendChild(s); }); }
  function engine() {
    if (loaded) return Promise.resolve(); loaded = true;
    var list = demo ? ['/js/gvd-phone-demo.js?v=1'] : ['/js/gvd-sip.min.js?v=0.21.2', '/js/gvd-phone.js?v=' + (dock.getAttribute('data-vp') || '5'), '/js/gvd-phone-ui.js?v=' + (dock.getAttribute('data-vu') || '2')];
    return list.reduce(function (p, s) { return p.then(function () { return load(s); }); }, Promise.resolve())
      .catch(function () { $('gp-load').textContent = 'The phone could not be loaded. Refresh the page and try again.'; });
  }
  function balance() {
    if (balDone) return; balDone = true;
    fetch('/user/phone/balance', { credentials: 'same-origin', cache: 'no-store', headers: { Accept: 'application/json' } })
      .then(function (r) { return r.ok ? r.json() : null; }).then(function (j) { var b = $('dk-bal'); if (!j || j.balance === null || j.balance === undefined) return; b.textContent = j.balance + (j.currency ? ' ' + j.currency : ''); b.title = 'Balance'; b.style.display = 'inline-block'; }, function () {});
  }
  function title() {
    var s = $('gp-ext'), o = s && s.selectedIndex >= 0 ? s.options[s.selectedIndex] : null;
    $('dk-title').textContent = o && o.value !== '' ? o.textContent : 'Phone';
    if (o && o.value !== '' && picked) { try { localStorage.setItem('gvdPhoneExtLabel', o.textContent.trim()); } catch (e) {} }
    var st = $('gp-state'), dot = btn && btn.querySelector('.dot');
    if (dot && st) dot.className = 'dot' + (/\bok\b/.test(st.className) ? ' ok' : '');
  }
  setInterval(title, 800);
  function restore() { var l = null; try { l = localStorage.getItem('gvdPhoneExtLabel'); } catch (e) {} if (l && !picked) pick(l); else picked = true; }
  function open() { dock.classList.add('open'); dock.setAttribute('aria-hidden', 'false'); if (btn) btn.classList.add('on'); try { sessionStorage.setItem('gvdDockOpen', '1'); } catch (e) {} balance(); return engine().then(function () { if (!picked) restore(); }); }
  function hide() { dock.classList.remove('open'); dock.setAttribute('aria-hidden', 'true'); if (btn) btn.classList.remove('on'); try { sessionStorage.removeItem('gvdDockOpen'); } catch (e) {} }
  function pick(label) {
    var tries = 0;
    (function go() {
      var s = $('gp-ext'), hit = -1;
      if (s) for (var i = 0; i < s.options.length; i++) if (s.options[i].textContent.trim() === label) hit = i;
      if (hit < 0) { if (++tries < 40) setTimeout(go, 300); return; }
      picked = true;
      if (s.selectedIndex !== hit) { s.selectedIndex = hit; s.dispatchEvent(new Event('change')); }
      title();
    })();
  }
  window.GvdDock = {
    open: open, hide: hide,
    select: function (label) { open().then(function () { pick(label); }); },
    off: function () { try { localStorage.removeItem('gvdPhoneExtLabel'); } catch (e) {} var s = $('gp-ext'); if (s && s.value !== '') { s.value = ''; s.dispatchEvent(new Event('change')); } hide(); }
  };
  $('dk-min').onclick = hide;
  $('dk-kp').onclick = function (e) { e.preventDefault(); var p = $('gp-pad'); if (p) p.style.display = p.style.display === 'none' ? '' : 'none'; this.classList.toggle('on', p && p.style.display !== 'none'); };
  if (btn) btn.onclick = function (e) { e.preventDefault(); dock.classList.contains('open') ? hide() : open(); };
  var off = false; try { off = localStorage.getItem('gvdPhoneOff') === '1'; } catch (e) {}
  var was = false; try { was = sessionStorage.getItem('gvdDockOpen') === '1'; } catch (e) {}
  if (was && !off) open();
})();
