/* Multi-step presentation for an existing <form> (2026-11-01). Fields, names and the submit stay the same; the browser's own validation runs per step. */
(function () {
  document.querySelectorAll('form[data-lx-steps]').forEach(function (form) {
    var steps = [].slice.call(form.querySelectorAll('.lx-step')), nav = document.querySelectorAll('[data-lx-for="' + form.id + '"] li'), bar = document.querySelector('[data-lx-bar="' + form.id + '"] i');
    var back = form.querySelector('[data-lx-back]'), next = form.querySelector('[data-lx-next]'), sub = form.querySelector('[data-lx-submit]'), cur = 0;
    function show(i) {
      cur = Math.max(0, Math.min(steps.length - 1, i));
      steps.forEach(function (s, k) { s.classList.toggle('on', k === cur); });
      [].forEach.call(nav, function (li, k) { li.classList.toggle('on', k === cur); li.classList.toggle('done', k < cur); });
      if (bar) bar.style.width = ((cur + 1) / steps.length * 100) + '%';
      back.hidden = cur === 0; next.hidden = cur === steps.length - 1; sub.hidden = cur !== steps.length - 1;
      form.dispatchEvent(new CustomEvent('lx:step', { detail: cur }));
    }
    function ok(i) { var bad = [].slice.call(steps[i].querySelectorAll('input,select,textarea')).filter(function (el) { return !el.checkValidity(); }); if (bad.length) { bad[0].reportValidity(); return false; } return true; }
    next.onclick = function () { if (ok(cur)) { show(cur + 1); form.scrollIntoView({ behavior: 'smooth', block: 'start' }); } };
    back.onclick = function () { show(cur - 1); };
    [].forEach.call(nav, function (li, k) { li.onclick = function () { if (k <= cur) return show(k); for (var j = cur; j < k; j++) if (!ok(j)) return show(j); show(k); }; });
    // if the browser blocks submit on a field in another step, open that step
    form.addEventListener('invalid', function (e) { var s = e.target.closest('.lx-step'), k = steps.indexOf(s); if (k >= 0 && k !== cur) show(k); }, true);
    var first = steps.findIndex(function (s) { return s.querySelector('[data-lx-err]'); });
    show(first > 0 ? first : 0);
  });
})();
