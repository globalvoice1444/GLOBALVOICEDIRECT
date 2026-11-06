@extends('layouts.user.user_main')
@section('content')
@php
  // How-to Videos page. Batch 1 (2026-11-05) Global Voice Direct panel walkthroughs first, then prior help videos.
  $vids = [
    ['CrHadPgp3Rw', 'Business phone system dashboard tour', 'A quick tour of your Global Voice Direct dashboard and the main tools in your account.', 'linear-gradient(135deg,#4f46e5,#1e1b4b)'],
    ['lcokcrr_lgU', 'Make business calls from your browser', 'Place and receive business calls from your computer in the browser, with your business number showing.', 'linear-gradient(135deg,#0891b2,#0c2a4a)'],
    ['N2Qk3NXmt3A', 'Business phone numbers & call routing', 'Manage your business phone numbers and set how calls are routed to you and your team.', 'linear-gradient(135deg,#059669,#052e2b)'],
    ['uCbJGPD4FIA', 'Phone extensions for your team', 'Add and manage phone extensions so callers can reach the right person on your team.', 'linear-gradient(135deg,#7c3aed,#2e1065)'],
    ['yInCaQqjQYI', 'Call history & call recording', 'Review past calls, listen to recordings, and find what you need in your call history.', 'linear-gradient(135deg,#d97706,#3b1d0b)'],
    ['t1KYJRNUEbg', 'Send fax from computer or phone', 'Send and receive faxes from your computer or phone. No fax machine needed.', 'linear-gradient(135deg,#dc2626,#3f0a0a)'],
    ['uYZaP3TiO7Q', 'Email campaigns', 'Connect your own email, import contacts or a CSV, send a campaign, and set up sequences and automations.', 'linear-gradient(135deg,#2563eb,#0f172a)'],
    ['PNCq8zEjeME', 'How to send the business credit application', 'Send your business credit application to our underwriting team so your business information is reported correctly.', 'linear-gradient(135deg,#4f46e5,#1e1b4b)'],
    ['09zz9mjywDw', 'Set up the mobile app', 'Provision the mobile app to make and receive calls on your business number from anywhere.', 'linear-gradient(135deg,#0891b2,#0c2a4a)'],
    ['r6sN-L-8hm0', 'Start your fax service', 'Set up your fax service and fax from any device. No fax machine needed.', 'linear-gradient(135deg,#059669,#052e2b)'],
    ['qYaCfPpsyUk', 'Use the SMS campaign platform', 'Send promotions, reminders and notifications to your clients. Upload your own list and send your first message in minutes.', 'linear-gradient(135deg,#d97706,#3b1d0b)'],
  ];
@endphp
<style>
 .hv-head h1{font-size:22px;margin:0 0 4px;font-weight:650}.hv-head{margin-bottom:16px}
 .hv-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:16px}
 .hv-card{background:#fff;border:1px solid var(--gp-line);border-radius:10px;overflow:hidden;cursor:pointer;text-align:left;padding:0;font:inherit;color:inherit;transition:box-shadow .15s,transform .15s}
 .hv-card:hover{box-shadow:0 6px 20px rgba(15,23,42,.08);transform:translateY(-1px)}
 .hv-th{height:140px;display:flex;align-items:center;justify-content:center;position:relative;background-size:cover;background-position:center}
 .hv-th:after{content:"";position:absolute;inset:0;background:var(--g);opacity:.82}
 .hv-play{position:relative;z-index:1;width:46px;height:46px;border-radius:50%;background:rgba(255,255,255,.92);display:flex;align-items:center;justify-content:center;color:#0f172a;font-size:18px}
 .hv-b{padding:10px 12px 12px}.hv-t{font-weight:600;font-size:13.5px}.hv-s{color:var(--gp-muted);font-size:12px}
 .hv-player{background:#fff;border:1px solid var(--gp-line);border-radius:10px;padding:14px;margin-bottom:16px;display:none}
 .hv-player .fr{position:relative;padding-top:56.25%;border-radius:8px;overflow:hidden;background:#000}
 .hv-player iframe{position:absolute;inset:0;width:100%;height:100%;border:0}
 .hv-player h3{font-size:15px;font-weight:600;margin:12px 0 4px;display:flex;justify-content:space-between;gap:8px}
</style>
<div class="hv-head"><h1>How-to Videos</h1><div class="muted">Short walkthroughs of everything in your Global Voice Direct account.</div></div>
<div class="hv-player" id="hv-player">
  <div class="fr"><iframe id="hv-frame" title="How-to video" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe></div>
  <h3><span id="hv-title"></span><button type="button" class="btn btn-default btn-sm" id="hv-close">Close</button></h3>
  <div class="muted" id="hv-desc" style="font-size:13px"></div>
</div>
<div class="hv-grid">
  @foreach ($vids as $v)
  <button type="button" class="hv-card" data-id="{{ $v[0] }}" data-t="{{ $v[1] }}" data-d="{{ $v[2] }}">
    <div class="hv-th" style="--g:{{ $v[3] }};background-image:url(https://i.ytimg.com/vi/{{ $v[0] }}/hqdefault.jpg)"><span class="hv-play"><i class="feather icon-play"></i></span></div>
    <div class="hv-b"><div class="hv-t">{{ $v[1] }}</div><div class="hv-s">Global Voice Direct Help</div></div>
  </button>
  @endforeach
</div>
@push('scripts')<script>
(function () {
  var P = document.getElementById('hv-player'), F = document.getElementById('hv-frame');
  document.querySelectorAll('.hv-card').forEach(function (c) { c.onclick = function () {
    F.src = 'https://www.youtube.com/embed/' + c.dataset.id + '?autoplay=1&rel=0';
    document.getElementById('hv-title').textContent = c.dataset.t; document.getElementById('hv-desc').textContent = c.dataset.d;
    P.style.display = 'block'; P.scrollIntoView({ behavior: 'smooth', block: 'start' });
  }; });
  var qv = new URLSearchParams(location.search).get('v'); if (qv) { var qc = document.querySelector('.hv-card[data-id="' + qv.replace(/[^\w-]/g, '') + '"]'); if (qc) qc.click(); }
  document.getElementById('hv-close').onclick = function () { F.src = 'about:blank'; P.style.display = 'none'; };
})();
</script>@endpush
@endsection
