@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<style>
.ac .tbl{width:100%;border-collapse:collapse;font-size:13px}.ac .tbl th{text-align:left;font-size:11px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;padding:8px;border-bottom:1px solid #e2e8f0}.ac .tbl td{padding:10px 8px;border-bottom:1px solid #f1f5f9;vertical-align:middle}
.ac .pill{border-radius:999px;padding:3px 10px;font-size:11.5px;font-weight:600;white-space:nowrap;background:#f1f5f9;color:#475569}
.ac .p-done,.ac .p-placed{background:#dcfce7;color:#166534}.ac .p-running,.ac .p-calling,.ac .p-queued{background:#e0e7ff;color:#3730a3}.ac .p-paused,.ac .p-ready{background:#fef3c7;color:#92400e}.ac .p-failed,.ac .p-cancelled{background:#fee2e2;color:#991b1b}
.ac .stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}.ac .st{background:#fff;border:1px solid #e2e8f0;border-radius:14px;padding:16px}.ac .st b{font-size:24px;display:block}.ac .st span{font-size:12px;color:#64748b}
.ac .bar{height:8px;background:#eef2ff;border-radius:99px;overflow:hidden}.ac .bar i{display:block;height:100%;background:linear-gradient(90deg,#4f46e5,#22c55e)}
.ac .btn2{display:inline-flex;align-items:center;gap:6px;border:0;border-radius:10px;padding:10px 16px;font-weight:600;font-size:13.5px;background:linear-gradient(135deg,#4f46e5,#6366f1);color:#fff;cursor:pointer;text-decoration:none}.ac .btn2.g{background:#fff;color:#0f172a;border:1px solid #cbd5e1}
.ac .note{background:#fff8e1;border:1px solid #ffe08a;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:16px}.ac .note.ok{background:#ecfdf5;border-color:#a7f3d0}
.ac .rc{display:flex;gap:10px;align-items:center;border:1px solid #e2e8f0;border-radius:10px;padding:10px;margin-bottom:8px;font-size:13px}.ac .rc audio{height:32px;max-width:220px}
.ac .drop{border:2px dashed #c7d2fe;border-radius:12px;padding:14px;background:#f8faff;font-size:13px}
@media(max-width:700px){.ac .stats{grid-template-columns:1fr 1fr}.ac .hide-m{display:none}}
</style>
@php
  $lbl = ['draft'=>'Draft','ready'=>'Ready','queued'=>'Starting','running'=>'Calling','done'=>'Completed','paused'=>'Paused','cancelled'=>'Cancelled','waiting'=>'Waiting','calling'=>'Calling…','placed'=>'Call placed','failed'=>'Not placed','skipped'=>'Skipped'];
  $tot = fn($id) => array_sum($counts[$id] ?? []);
  $dn = fn($id) => ($counts[$id]['placed'] ?? 0) + ($counts[$id]['failed'] ?? 0) + ($counts[$id]['skipped'] ?? 0);
  $all = $camps->sum(fn($c) => $tot($c->id)); $placed = $camps->sum(fn($c) => $counts[$c->id]['placed'] ?? 0); $failed = $camps->sum(fn($c) => $counts[$c->id]['failed'] ?? 0);
@endphp
<div class="lx ac">
 <div class="lx-hero lx-in" style="padding:22px 26px"><div class="grid"></div>
  <div class="lx-eyebrow"><i class="feather icon-radio"></i> Business</div>
  <h1 style="font-size:24px">{{ $show ? $show->name : 'Audio campaign' }}</h1>
  <p>{{ $show ? 'Progress for every number in this campaign.' : 'Play a recorded message to a list of phone numbers. Up to ' . $max . ' numbers per campaign.' }}</p>
  <div class="row2"><span class="lx-glass"><b>{{ $camps->count() }}</b> campaigns</span><span class="lx-glass"><b>{{ $all }}</b> numbers</span>
   @unless($sending)<span class="lx-glass"><span class="lx-dot off"></span> {{ $off }}</span>@endunless</div>
 </div>
 @if (session('ac_err'))<div class="note">{{ session('ac_err') }}</div>@endif
 @if (session('ac_ok'))<div class="note ok">{{ session('ac_ok') }}</div>@endif
 @if (isset($errors) && $errors->any())<div class="note">{{ $errors->first() }}</div>@endif

 @if ($show)
  @php $n = $tot($show->id); $d = $dn($show->id); @endphp
  <div class="stats"><div class="st"><b id="ac-placed">{{ $counts[$show->id]['placed'] ?? 0 }}</b><span>Calls placed</span></div><div class="st"><b id="ac-failed">{{ $counts[$show->id]['failed'] ?? 0 }}</b><span>Not placed</span></div><div class="st"><b id="ac-wait">{{ ($counts[$show->id]['waiting'] ?? 0) + ($counts[$show->id]['calling'] ?? 0) }}</b><span>Waiting</span></div><div class="st"><b>{{ $n }}</b><span>Numbers</span></div></div>
  <div class="lx-card"><div class="lx-ch" style="justify-content:space-between;flex-wrap:wrap"><div><h3>Progress by number</h3><div class="s">Status: <span class="pill p-{{ $show->status }}" id="ac-st">{{ $lbl[$show->status] ?? $show->status }}</span></div></div>
   <div style="display:flex;gap:8px;flex-wrap:wrap">
    @if (in_array($show->status, ['draft','ready','paused']))<form method="post" action="/user/audio-campaigns/{{ $show->id }}/start">@csrf<button class="btn2">Start campaign</button></form>
     <form method="post" action="/user/audio-campaigns/{{ $show->id }}/delete" onsubmit="return confirm('Delete this campaign?')">@csrf<button class="btn2 g">Delete</button></form>@endif
    @if (in_array($show->status, ['queued','running']))<form method="post" action="/user/audio-campaigns/{{ $show->id }}/pause">@csrf<button class="btn2 g">Pause campaign</button></form>@endif
    <a class="btn2 g" href="/user/audio-campaigns">All campaigns</a></div></div>
   <div class="bar" style="margin-bottom:14px"><i id="ac-bar" style="width:{{ $n ? intval($d*100/$n) : 0 }}%"></i></div>
   <table class="tbl" id="ac-rows" data-id="{{ $show->id }}" data-live="{{ in_array($show->status, ['queued','running']) ? 1 : 0 }}"><tr><th>Number</th><th>Status</th><th class="hide-m">Time (ET)</th></tr>
   @foreach (($demoTargets ?? null) ?: DB::table('gvd_audio_campaign_targets')->where('campaign_id', $show->id)->orderBy('id')->get() as $t)
    <tr><td>{{ $t->number }}</td><td><span class="pill p-{{ $t->status }}">{{ $lbl[$t->status] ?? $t->status }}</span></td><td class="hide-m">{{ $t->attempted_at ? \Illuminate\Support\Carbon::parse($t->attempted_at, 'UTC')->tz('America/New_York')->format('M j, g:i:s A') : '—' }}</td></tr>
   @endforeach</table></div>
 @else
  <div class="stats"><div class="st"><b>{{ $placed }}</b><span>Calls placed</span></div><div class="st"><b>{{ $failed }}</b><span>Not placed</span></div><div class="st"><b>{{ $camps->whereIn('status',['queued','running'])->count() }}</b><span>Calling now</span></div><div class="st"><b>{{ $camps->count() }}</b><span>Campaigns</span></div></div>
  <div class="lx-cols">
   <div class="lx-card"><div class="lx-ch"><div class="lx-ic"><i class="feather icon-plus"></i></div><div><h3>New campaign</h3><div class="s">Add numbers, pick a recording, choose when to start.</div></div></div>
    <form method="post" action="/user/audio-campaigns" enctype="multipart/form-data">@csrf
     <div class="lx-f"><label class="req">Campaign name</label><input name="name" maxlength="150" required value="{{ old('name') }}"></div>
     <div class="lx-f"><label>Upload a CSV file</label><div class="drop"><input type="file" name="csv" accept=".csv,text/csv"><div class="lx-hint">Numbers are read from the first column.</div></div></div>
     <div class="lx-f"><label>Or paste numbers, one per line</label><textarea name="numbers" rows="6" id="ac-nums">{{ old('numbers') }}</textarea><div class="lx-hint" id="ac-count">Up to {{ $max }} numbers.</div></div>
     <div class="lx-f"><label class="req">Recording</label>
      @forelse ($recs as $r)
       <label class="rc"><input type="radio" name="recording_id" value="{{ $r->id }}" {{ $loop->first ? 'checked' : '' }}><span style="flex:1"><b>{{ $r->name }}</b><span class="lx-hint" style="display:block">Uploaded {{ \Illuminate\Support\Carbon::parse($r->created_at)->format('M j, Y') }}</span></span><audio controls preload="none" src="/user/audio-campaigns/recording/{{ $r->id }}"></audio></label>
      @empty <p class="lx-hint">No recordings yet. Upload one below.</p> @endforelse
      <div class="drop"><input type="file" name="recording" accept=".mp3,.ogg,.wav,audio/mpeg,audio/ogg,audio/wav"><div class="lx-hint">Upload a new recording (MP3, OGG or WAV, up to 10 MB). Everyone on the list hears this same message. It is used instead of the choice above.</div></div></div>
     <div class="lx-f"><label>Line to call from</label>
      @if ($billing && $accounts)<select name="caller_account">@foreach ($accounts as $a)<option value="{{ $a }}">{{ $a }}</option>@endforeach</select>
      @else<input name="caller_account" disabled placeholder="Sign in again to choose a line"><span class="lx-hint">Your lines load from your account when you are signed in.</span>@endif</div>
     <div class="lx-f"><label>When to start</label><div class="g2"><select name="when"><option value="now">Start now</option><option value="later">Schedule for later</option></select><input type="datetime-local" name="scheduled_at"></div><div class="lx-hint">Eastern Time. Calls go out one at a time, about every 2 seconds.</div></div>
     <div style="display:flex;gap:10px;flex-wrap:wrap"><button class="btn2" name="action" value="start">Start campaign</button><button class="btn2 g" name="action" value="draft">Save as draft</button></div>
     @unless($sending)<p class="lx-hint" style="margin-top:10px">{{ $off }}. You can prepare and save campaigns now.</p>@endunless
    </form></div>
   <div class="lx-card"><div class="lx-ch"><div class="lx-ic"><i class="feather icon-list"></i></div><div><h3>Your campaigns</h3><div class="s">Newest first</div></div></div>
    @if ($camps->isEmpty())<p class="lx-hint">No campaigns yet.</p>@else
    <table class="tbl"><tr><th>Campaign</th><th>Progress</th><th>Status</th></tr>
    @foreach ($camps as $c) @php $n = $tot($c->id); $d = $dn($c->id); @endphp
     <tr><td><a href="/user/audio-campaigns?c={{ $c->id }}"><b>{{ $c->name }}</b></a><div class="lx-hint">{{ \Illuminate\Support\Carbon::parse($c->created_at)->format('M j, Y') }}</div></td>
      <td style="min-width:110px"><div class="bar"><i style="width:{{ $n ? intval($d*100/$n) : 0 }}%"></i></div><div class="lx-hint">{{ $d }} of {{ $n }}</div></td>
      <td><span class="pill p-{{ $c->status }}">{{ $lbl[$c->status] ?? $c->status }}</span></td></tr>
    @endforeach</table>@endif
    <p class="lx-hint" style="margin-top:12px">Only call people who agreed to hear from your business.</p></div>
  </div>
 @endif
</div>
@include('users.partials.vo-foot', ['page' => 'audio-campaigns', 'admin' => false])
@push('scripts')<script>
(function(){
 var t=document.getElementById('ac-nums'),c=document.getElementById('ac-count'),max={{ $max }};
 if(t&&c){var f=function(){var s={};t.value.split(/[\n,;]/).forEach(function(l){var d=l.replace(/\D/g,'');if(d.length==10)d='1'+d;if(d.length==11&&d[0]=='1')s[d]=1});var n=Object.keys(s).length;c.textContent=n+' valid numbers · up to '+max;c.style.color=n>max?'#b91c1c':''};t.addEventListener('input',f);f();}
 var tb=document.getElementById('ac-rows');if(!tb||tb.dataset.live!=='1')return;
 var L={waiting:'Waiting',calling:'Calling…',placed:'Call placed',failed:'Not placed',skipped:'Skipped'};
 setInterval(function(){fetch('/user/audio-campaigns/'+tb.dataset.id+'/status',{credentials:'same-origin'}).then(function(r){return r.json()}).then(function(j){
  var rows=tb.querySelectorAll('tr');j.targets.forEach(function(x,i){var r=rows[i+1];if(r){var p=r.children[1].firstChild;p.className='pill p-'+x.status;p.textContent=L[x.status]||x.status;}});
  var done=j.targets.filter(function(x){return['placed','failed','skipped'].indexOf(x.status)>=0}).length;document.getElementById('ac-bar').style.width=(j.targets.length?Math.floor(done*100/j.targets.length):0)+'%';
 })},4000);
})();
</script>@endpush
@endsection
