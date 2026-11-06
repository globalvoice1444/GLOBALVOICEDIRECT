@extends('admin.layouts.layout')
@section('css')
<style>
.gbc{--gn:#1E2A36;--go:#FF5000;--gl:#E6EAF0;--gm:#64748B}
.gbc .wrap{display:grid;grid-template-columns:300px 1fr;gap:18px}@media(max-width:1100px){.gbc .wrap{grid-template-columns:1fr}}
.gbc .pill{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.04em;padding:2px 9px;border-radius:20px}
.gbc .st-SENDING{background:#E3F6EC;color:#11804a}.gbc .st-SCHEDULED{background:#E6F1FB;color:#1d5fa8}.gbc .st-PAUSED{background:#FFF1E6;color:#b4480a}.gbc .st-DRAFT{background:#EEF1F5;color:#56657a}.gbc .st-DONE{background:#1E2A36;color:#fff}
.gbc .item{display:block;padding:12px 14px;border:1px solid var(--gl);border-radius:12px;margin-bottom:10px;color:inherit;background:#fff;text-decoration:none;transition:transform .15s,box-shadow .15s}
.gbc .item:hover{transform:translateY(-1px);box-shadow:0 6px 18px -10px rgba(15,27,38,.35)}.gbc .item.on{border-color:var(--go);box-shadow:0 0 0 3px rgba(255,80,0,.12)}.gbc .item b{display:block;font-size:14px}.gbc .item small{color:var(--gm)}
.gbc .kpis{display:flex;gap:10px;flex-wrap:wrap;margin:12px 0}.gbc .kpi{background:#fff;border:1px solid var(--gl);border-radius:12px;padding:10px 16px;min-width:105px}.gbc .kpi b{font-size:20px;display:block}.gbc .kpi span{font-size:11.5px;color:var(--gm)}
.gbc .warn{background:#FFF7E6;border:1px solid #F6D58E;border-radius:12px;padding:10px 14px;margin-bottom:14px;font-size:13px}.gbc .warn.ok{background:#EAF7F0;border-color:#A8DFC0}
.gbc .box{background:#fff;border:1px solid var(--gl);border-radius:14px;padding:16px 18px;margin-bottom:14px;animation:gbcIn .25s ease both}
@keyframes gbcIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:none}}
.gbc .box h6{font-weight:800;color:var(--gn);margin:0 0 10px;font-size:13px;letter-spacing:.03em;text-transform:uppercase}
.gbc label{font-weight:600;font-size:12.5px;margin:10px 0 4px;display:block}.gbc .row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}.gbc .row3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px}
.gbc textarea.body{font-family:ui-monospace,Menlo,monospace;font-size:12.5px;min-height:300px}.gbc iframe{width:100%;height:640px;border:1px solid var(--gl);border-radius:10px;background:#F4F6F9}
.gbc .btn-go{background:var(--go);border-color:var(--go);color:#fff}.gbc .muted{color:var(--gm)}.gbc .tags button{font-size:12px;padding:2px 8px;margin:0 4px 4px 0}
.gbc table.log{width:100%;font-size:12.5px}.gbc table.log td,.gbc table.log th{padding:6px 8px;border-bottom:1px solid var(--gl)}
.gbc .split{display:grid;grid-template-columns:1fr 1fr;gap:14px}@media(max-width:1700px){.gbc .split{grid-template-columns:1fr}}
</style>
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gbc">
<div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
 <h5>Automations · Broadcasts <a href="/admin/automations/tracking?type=broadcast" style="font-size:13px;font-weight:400;margin-left:10px">Opens &amp; clicks report</a></h5>
 <form method="post" action="/admin/automations/broadcasts" style="display:flex;gap:6px">@csrf
  <input name="name" class="form-control form-control-sm" placeholder="New broadcast name" required><button class="btn btn-sm btn-go">+ Broadcast</button></form>
</div><div class="card-block">
 @if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
 @if($errors->any())<div class="alert alert-danger">{{ implode(' ', $errors->all()) }}</div>@endif
 <div class="warn {{ $enabled ? 'ok' : '' }}"><b>Global sending: {{ $enabled ? 'ON' : 'OFF' }}</b> — broadcasts only send when SCHEDULED/SENDING, throttled by the 5-minute scheduler, the daily cap, and the {{ config('drip.min_hours_between') }}h per-person limit shared with drip emails. Unsubscribe link and one-click unsubscribe header on every email · {{ $suppressed }} unsubscribed/suppressed.</div>
 <div class="wrap">
  <div>
   @forelse($list as $b) @php $c = $counts[$b->id] ?? null; @endphp
    <a class="item {{ $sel && $sel->id==$b->id ? 'on' : '' }}" href="/admin/automations/broadcasts?id={{ $b->id }}"><span class="pill st-{{ $b->status }}" style="float:right">{{ $b->status }}</span>
     <b>{{ $b->name }}</b><small>{{ (int)($c->total ?? 0) }} recipients · {{ (int)($c->sent ?? 0) }} sent · {{ $b->daily_cap }}/day</small></a>
   @empty <p class="muted">No broadcasts yet.</p> @endforelse
  </div>
  <div>
  @if(!$sel)
   <div class="box"><h6>Broadcasts</h6><p class="muted">One-off emails to your existing customers or an uploaded CSV list, sent gradually under a daily cap. Pick a broadcast on the left or create one.</p></div>
  @else @php $c = $counts[$sel->id] ?? null; $editable = in_array($sel->status, ['DRAFT','PAUSED']); @endphp
   <div style="display:flex;justify-content:space-between;align-items:flex-start;flex-wrap:wrap;gap:10px">
    <div><h4 style="margin:0">{{ $sel->name }} <span class="pill st-{{ $sel->status }}">{{ $sel->status }}</span></h4>
     <div class="meta muted" style="font-size:12.5px;margin-top:4px">Start: {{ $sel->start_at ? \Carbon\Carbon::parse($sel->start_at)->tz($tz)->format('M j, Y g:i A') . ' ET' : 'not set (starts when scheduled)' }} · Cap {{ $sel->daily_cap }}/day · {{ $today }} sent today</div></div>
    <div style="display:flex;gap:6px;flex-wrap:wrap">
     <form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/test">@csrf<button class="btn btn-sm btn-default">Send test to me</button></form>
     @if(in_array($sel->status,['DRAFT','PAUSED']))
      <form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/status" onsubmit="var v=prompt('This emails REAL customers starting at the start time, {{ $sel->daily_cap }}/day. Type SEND to schedule.');if(v!=='SEND')return false;this.confirm.value=v;">@csrf
       <input type="hidden" name="status" value="SCHEDULED"><input type="hidden" name="confirm"><button class="btn btn-sm btn-success">{{ $sel->status==='PAUSED' ? 'Resume (schedule)' : 'Schedule' }}</button></form>
     @endif
     @if(in_array($sel->status,['SCHEDULED','SENDING']))
      <form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/status">@csrf<input type="hidden" name="status" value="PAUSED"><button class="btn btn-sm btn-warning">Pause</button></form>
     @endif
     @if($sel->status==='PAUSED')<form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/status">@csrf<input type="hidden" name="status" value="DRAFT"><button class="btn btn-sm btn-default">Back to draft</button></form>@endif
    </div>
   </div>
   <div class="kpis">
    <div class="kpi"><b>{{ (int)($c->total ?? 0) }}</b><span>Recipients</span></div><div class="kpi"><b>{{ (int)($c->pending ?? 0) }}</b><span>Queued</span></div>
    <div class="kpi"><b>{{ (int)($c->sent ?? 0) }}</b><span>Sent</span></div><div class="kpi"><b>{{ (int)($c->failed ?? 0) }}</b><span>Failed</span></div>
    <div class="kpi"><b>{{ (int)($c->skipped ?? 0) }}</b><span>Skipped</span></div>
    <div class="kpi"><b>{{ ($c->pending ?? 0) ? ceil($c->pending / max(1,$sel->daily_cap)) : 0 }}</b><span>Days to finish</span></div>
   </div>
   <div class="split">
    <div>
     <form class="box" method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/save">@csrf<h6>Email</h6>
      <fieldset {{ $editable ? '' : 'disabled' }}>
      <label>Internal name</label><input name="name" class="form-control" value="{{ $sel->name }}" required>
      <label>Subject</label><input name="subject" class="form-control" value="{{ $sel->subject }}" required>
      <label>Preview text</label><input name="preview" class="form-control" value="{{ $sel->preview }}">
      <label>Body (HTML)</label>
      <div class="tags">@php $mtags = array_map(fn($x) => '{'.'{'.$x.'}'.'}', ['first_name','business_name','email','extra','app_url','login_url']); @endphp @foreach($mtags as $t)<button type="button" class="btn btn-default" onclick="gbcIns('{{ $t }}')">{{ $t }}</button>@endforeach</div>
      <textarea id="gbcBody" name="body_html" class="form-control body" required>{{ $sel->body_html }}</textarea>
      <div class="row2"><div><label>If first name is missing, say</label><input name="first_name_fallback" class="form-control" value="{{ $sel->first_name_fallback }}"></div>
       <div><label>If business name is missing, say</label><input name="business_fallback" class="form-control" value="{{ $sel->business_fallback }}"></div></div>
      <div class="row2"><div><label>Daily cap (emails/day)</label><input type="number" min="1" max="2000" name="daily_cap" class="form-control" value="{{ $sel->daily_cap }}"></div>
       <div><label>Start time (ET)</label><input type="datetime-local" name="start_at" class="form-control" value="{{ $sel->start_at ? \Carbon\Carbon::parse($sel->start_at)->tz($tz)->format('Y-m-d\TH:i') : '' }}"></div></div>
      <div style="margin-top:12px"><button class="btn btn-go">Save</button> <span class="muted" style="font-size:12px">The footer with address and unsubscribe link is added automatically.</span></div>
      </fieldset>@if(!$editable)<p class="muted" style="font-size:12px;margin-top:8px">Pause to edit.</p>@endif
     </form>
     <div class="box"><h6>Audience</h6>
      @if($editable)
      <form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/audience" style="margin-bottom:10px">@csrf<input type="hidden" name="mode" value="customers">
       <button class="btn btn-sm btn-default">+ Add all existing customers</button> <span class="muted" style="font-size:12px">verified accounts with a valid email; demo, test and unsubscribed excluded</span></form>
      <form method="post" enctype="multipart/form-data" action="/admin/automations/broadcasts/{{ $sel->id }}/audience" style="display:flex;gap:6px;align-items:center;flex-wrap:wrap">@csrf<input type="hidden" name="mode" value="csv">
       <input type="file" name="csv" accept=".csv,text/csv" class="form-control form-control-sm" style="max-width:280px" required><button class="btn btn-sm btn-default">Upload CSV</button></form>
      <p class="muted" style="font-size:12px;margin:6px 0">Columns: <code>email, first_name, business_name, extra</code> (header row required). Duplicates, invalid and unsubscribed addresses are removed.</p>
      <form method="post" action="/admin/automations/broadcasts/{{ $sel->id }}/audience" onsubmit="return confirm('Remove all queued recipients?')">@csrf<input type="hidden" name="mode" value="clear"><button class="btn btn-sm btn-link" style="padding:0">Clear queued recipients</button></form>
      @else<p class="muted">Pause to change the audience.</p>@endif
     </div>
    </div>
    <div class="box"><h6>Preview @if($sample)<span class="muted" style="text-transform:none;font-weight:400">· sample recipient #{{ $sample->id }}</span>@endif</h6>
     <div style="margin-bottom:6px"><a class="btn btn-sm btn-default" href="?id={{ $sel->id }}">Sample recipient</a> <a class="btn btn-sm btn-default" href="?id={{ $sel->id }}&demo=1">Fictional sample</a></div>
     <iframe src="/admin/automations/broadcasts/{{ $sel->id }}/preview{{ request('demo') ? '?demo=1' : '' }}"></iframe></div>
   </div>
   <div class="box"><h6>Recipient log</h6>
    <div style="margin-bottom:8px">@foreach([''=>'All','pending'=>'Queued','sent'=>'Sent','failed'=>'Failed','skipped'=>'Skipped'] as $k=>$v)<a class="btn btn-sm {{ request('st')==$k ? 'btn-go' : 'btn-default' }}" href="?id={{ $sel->id }}{{ $k ? '&st='.$k : '' }}">{{ $v }}</a> @endforeach</div>
    <table class="log"><tr><th>Email</th><th>First name</th><th>Business</th><th>Source</th><th>Status</th><th>When</th><th>Note</th><th></th></tr>
    @foreach($log as $r)<tr class="pii"><td>{{ $r->email }}</td><td>{{ $r->first_name }}</td><td>{{ $r->business_name }}</td><td>{{ $r->source }}</td><td>{{ $r->status }}</td>
     <td>{{ $r->sent_at ? \Carbon\Carbon::parse($r->sent_at)->tz($tz)->format('M j g:i A') : '' }}</td><td>{{ $r->reason }}</td><td><a target="_blank" href="/admin/automations/broadcasts/{{ $sel->id }}/preview?rec={{ $r->id }}">preview</a></td></tr>@endforeach</table>
    <div style="margin-top:8px">{{ $log->links() }}</div>
   </div>
  @endif
  </div>
 </div>
</div></div></div></div></div></div>
<script>function gbcIns(t){var a=document.getElementById('gbcBody'),s=a.selectionStart||0;a.value=a.value.slice(0,s)+t+a.value.slice(a.selectionEnd||s);a.focus();a.selectionStart=a.selectionEnd=s+t.length;}</script>
@endsection
