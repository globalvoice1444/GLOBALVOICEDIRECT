@extends('admin.layouts.layout')
@section('css')
<style>
.gdr{--gn:#1E2A36;--go:#FF5000;--gt:#2E93B0;--gl:#E6EAF0;--gm:#64748B}
.gdr .wrap{display:grid;grid-template-columns:300px 1fr;gap:18px}@media(max-width:1100px){.gdr .wrap{grid-template-columns:1fr}}
.gdr .pill{display:inline-block;font-size:11px;font-weight:700;letter-spacing:.04em;padding:2px 9px;border-radius:20px}
.gdr .st-ACTIVE{background:#E3F6EC;color:#11804a}.gdr .st-PAUSED{background:#FFF1E6;color:#b4480a}.gdr .st-DRAFT{background:#EEF1F5;color:#56657a}
.gdr .seq{display:block;padding:12px 14px;border:1px solid var(--gl);border-radius:12px;margin-bottom:10px;color:inherit;background:#fff;text-decoration:none}
.gdr .seq.on{border-color:var(--go);box-shadow:0 0 0 3px rgba(255,80,0,.12)}.gdr .seq b{display:block;font-size:14px}.gdr .seq small{color:var(--gm)}
.gdr .tl{list-style:none;margin:0;padding:0}.gdr .tl li{display:grid;grid-template-columns:22px 74px 1fr auto;gap:12px;align-items:center;background:#fff;border:1px solid var(--gl);border-radius:12px;padding:12px 14px;margin-bottom:8px}
.gdr .tl li.ph{border:2px dashed var(--go);background:#FFF7F2;height:58px}.gdr .grip{cursor:grab;color:#9aa6b6;font-size:16px}
.gdr .day{font-weight:800;color:var(--gn);font-size:13px;background:#F4F6F9;border-radius:8px;text-align:center;padding:6px 0}.gdr .day.late{background:#FFF1E6;color:#b4480a}
.gdr .ttl{font-weight:600}.gdr .sub{color:var(--gm);font-size:12.5px}.gdr .meta{font-size:11.5px;color:var(--gm)}
.gdr .acts{display:flex;gap:4px;flex-wrap:wrap;justify-content:flex-end}.gdr .acts .btn,.gdr .acts button{font-size:12px;padding:4px 9px}
.gdr .kpis{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:14px}.gdr .kpi{background:#fff;border:1px solid var(--gl);border-radius:12px;padding:10px 16px;min-width:110px}.gdr .kpi b{font-size:20px;display:block}.gdr .kpi span{font-size:11.5px;color:var(--gm)}
.gdr .warn{background:#FFF7E6;border:1px solid #F6D58E;border-radius:12px;padding:10px 14px;margin-bottom:14px;font-size:13px}
.gdr .warn.ok{background:#EAF7F0;border-color:#A8DFC0}
.gdr .drawer{position:fixed;top:0;right:-760px;width:740px;max-width:100vw;height:100vh;background:#fff;z-index:2000;box-shadow:-20px 0 50px -20px rgba(0,0,0,.35);transition:right .25s;overflow:auto;padding:24px}
.gdr .drawer.open{right:0}.gdr .scrim{display:none;position:fixed;inset:0;background:rgba(15,27,38,.35);z-index:1999}.gdr .scrim.open{display:block}
.gdr label{font-weight:600;font-size:12.5px;margin:10px 0 4px;display:block}.gdr .row2{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.gdr textarea.body{font-family:ui-monospace,Menlo,monospace;font-size:12.5px;min-height:240px}.gdr iframe{width:100%;height:420px;border:1px solid var(--gl);border-radius:10px}
.gdr .btn-go{background:var(--go);border-color:var(--go);color:#fff}.gdr .muted{color:var(--gm)}
.gdr .tb{display:flex;gap:4px;margin-bottom:4px}.gdr .tb button{font-size:12px;padding:2px 8px}
</style>
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gdr">
 <div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
  <h5>Automations · Drip Sequences <a href="/admin/automations/tracking?type=drip" style="font-size:13px;font-weight:400;margin-left:10px">Opens &amp; clicks report</a></h5>
  <form method="post" action="/admin/automations/drip/sequence" style="display:flex;gap:6px">@csrf
   <input name="name" class="form-control form-control-sm" placeholder="New sequence name" required>
   <select name="type" class="form-control form-control-sm">@foreach($types as $k=>$v)<option value="{{ $k }}">{{ $v }}</option>@endforeach</select>
   <button class="btn btn-sm btn-default">+ Sequence</button></form>
 </div><div class="card-block">
  @if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
  @if($errors->any())<div class="alert alert-danger">{{ implode(' ', $errors->all()) }}</div>@endif
  <div class="warn {{ $enabled ? 'ok' : '' }}"><b>Global sending: {{ $enabled ? 'ON' : 'OFF' }}</b> —
   @if($enabled) ACTIVE sequences send automatically every 5 minutes (max 1 email per person per {{ config('drip.min_hours_between') }}h).
   @else kill switch is off (<code>DRIP_SENDING=false</code>). Nothing goes to customers even if a sequence is ACTIVE. Test sends to yourself still work.@endif
   · {{ $suppressed }} unsubscribed/suppressed</div>
  <div class="wrap">
   <div>
    @foreach($seqs as $s)
     <a class="seq {{ $sel && $sel->id==$s->id ? 'on' : '' }}" href="/admin/automations/drip?seq={{ $s->id }}">
      <span class="pill st-{{ $s->status }}" style="float:right">{{ $s->status }}</span>
      <b>{{ $s->name }}</b><small>{{ $types[$s->type] ?? $s->type }} · trigger: {{ str_replace('_',' ',$s->trigger) }}</small><br>
      <small>{{ (int)($seqStats[$s->id]->sent ?? 0) }} sent · {{ (int)($seqStats[$s->id]->failed ?? 0) }} failed · {{ (int)($enr[$s->id]->active ?? 0) }} enrolled</small>
     </a>
    @endforeach
   </div>
   <div>
   @if($sel)
    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:10px;flex-wrap:wrap">
     <div><h4 style="margin:0">{{ $sel->name }} <span class="pill st-{{ $sel->status }}">{{ $sel->status }}</span></h4><div class="muted" style="font-size:13px;margin-top:4px">{{ $sel->description }}</div>
      @if($sel->trigger==='verified_signup')<div class="meta" style="margin-top:4px">Auto-enrolls verified signups registered after: {{ $sel->enroll_after ?: 'the moment it is activated' }}</div>@endif</div>
     <div style="display:flex;gap:6px">
      <button class="btn btn-sm btn-go" onclick="gdrOpen(null)">+ Add email</button>
      <form method="post" action="/admin/automations/drip/sequence/{{ $sel->id }}/status" onsubmit="return {{ $sel->status==='ACTIVE' ? 'true' : "confirm('Activate this sequence? It will send to real customers when global sending is ON.')" }}">@csrf
       <input type="hidden" name="status" value="{{ $sel->status==='ACTIVE' ? 'PAUSED' : 'ACTIVE' }}">
       <button class="btn btn-sm {{ $sel->status==='ACTIVE' ? 'btn-warning' : 'btn-success' }}">{{ $sel->status==='ACTIVE' ? 'Pause sequence' : 'Activate sequence' }}</button></form>
     </div>
    </div>
    @php $tot = ['sent'=>0,'failed'=>0,'skipped'=>0]; foreach($stepStats as $x){ $tot['sent']+=$x->sent; $tot['failed']+=$x->failed; $tot['skipped']+=$x->skipped; } @endphp
    <div class="kpis" style="margin-top:14px">
     <div class="kpi"><b>{{ $steps->count() }}</b><span>emails ({{ $steps->where('status','ACTIVE')->count() }} active)</span></div>
     <div class="kpi"><b>{{ (int)($enr[$sel->id]->active ?? 0) }}</b><span>enrolled now</span></div>
     <div class="kpi"><b>{{ $tot['sent'] }}</b><span>sent</span></div><div class="kpi"><b>{{ $tot['failed'] }}</b><span>failed</span></div><div class="kpi"><b>{{ $tot['skipped'] }}</b><span>skipped</span></div>
    </div>
    <div class="meta" style="margin-bottom:8px">Drag ☰ to reorder. Day = days after enrollment; any number works, including after Day 30.</div>
    <ul class="tl" id="gdrTl">
    @php $prev = -99999; @endphp
    @forelse($steps as $st)
     @php $late = $st->day_offset < $prev; $prev = max($prev,$st->day_offset); $ss = $stepStats[$st->id] ?? null; @endphp
     <li data-id="{{ $st->id }}">
      <span class="grip" title="Drag to reorder">☰</span>
      <span class="day {{ $late ? 'late' : '' }}" title="{{ $late ? 'Earlier day than the email above it' : '' }}">Day {{ $st->day_offset }}</span>
      <div><div class="ttl">{{ $st->name }} <span class="pill st-{{ $st->status }}">{{ $st->status }}</span></div>
       <div class="sub">✉ {{ $st->subject }}</div><div class="meta">{{ $st->purpose }} @if($ss)· {{ $ss->sent }} sent · {{ $ss->failed }} failed · {{ $ss->skipped }} skipped @endif</div></div>
      <div class="acts">
       <button class="btn btn-default" onclick='gdrOpen(@json($st))'>Edit</button>
       <form method="post" action="/admin/automations/drip/step/{{ $st->id }}/status">@csrf<input type="hidden" name="status" value="{{ $st->status==='ACTIVE' ? 'PAUSED' : 'ACTIVE' }}"><button class="btn btn-default">{{ $st->status==='ACTIVE' ? 'Pause' : 'Activate' }}</button></form>
       <form method="post" action="/admin/automations/drip/step/{{ $st->id }}/duplicate">@csrf<button class="btn btn-default">Duplicate</button></form>
       <form method="post" action="/admin/automations/drip/step/{{ $st->id }}/test">@csrf<button class="btn btn-default" title="Sends only to {{ $adminEmail }}">Send test to me</button></form>
       <form method="post" action="/admin/automations/drip/step/{{ $st->id }}/delete" onsubmit="return confirm('Delete this email?')">@csrf<button class="btn btn-default" style="color:#c0392b">Delete</button></form>
      </div>
     </li>
    @empty
     <li style="display:block" class="muted">No emails yet. Click “+ Add email”.</li>
    @endforelse
    </ul>
    <button class="btn btn-sm btn-default" onclick="gdrOpen({day_offset: {{ max(31, (int)$steps->max('day_offset') + 7) }}})">+ Add email after Day {{ (int)$steps->max('day_offset') }}</button>

    @if($sel->type !== 'signup')
    <div class="card" style="margin-top:18px;box-shadow:none;border:1px solid #E6EAF0"><div class="card-block">
     <h6 style="font-weight:700">Enroll a segment</h6><div class="meta" style="margin-bottom:8px">Win-back and other sequences only get people you pick here. Unsubscribed emails are skipped. Nothing sends while the sequence is PAUSED or global sending is OFF.</div>
     <form method="post" action="/admin/automations/drip/sequence/{{ $sel->id }}/enroll" class="row2">@csrf
      <div><label>Segment</label><select name="segment" class="form-control" onchange="document.getElementById('gdrSegE').style.display=this.value=='emails'?'block':'none'">
       <option value="inactive_accounts">Verified accounts marked inactive</option><option value="registered_between">Verified accounts registered between dates</option><option value="emails">Paste a list of account emails</option></select>
       <div class="row2"><div><label>From</label><input type="date" name="from" class="form-control"></div><div><label>To</label><input type="date" name="to" class="form-control"></div></div></div>
      <div><div id="gdrSegE" style="display:none"><label>Emails</label><textarea name="emails" class="form-control" rows="4" placeholder="one per line"></textarea></div>
       <label><input type="checkbox" name="confirm" value="1" required> I picked this segment on purpose</label><button class="btn btn-sm btn-go">Enroll segment</button></div>
     </form></div></div>
    @endif

    <h6 style="margin-top:22px;font-weight:700">Recent activity</h6>
    <table class="table"><thead><tr><th>When (UTC)</th><th>Email</th><th>Subject</th><th>Status</th><th>Reason</th></tr></thead><tbody>
    @forelse($recent as $x)<tr><td>{{ $x->created_at }}</td><td>{{ $x->email }}</td><td>{{ $x->subject }}</td><td>{{ $x->status }}</td><td>{{ $x->reason }}</td></tr>
    @empty<tr><td colspan="5" class="muted">No sends yet.</td></tr>@endforelse
    </tbody></table>
   @endif
   </div>
  </div>
 </div></div>

 @if($sel)
 <div class="scrim" id="gdrScrim" onclick="gdrClose()"></div>
 <div class="drawer" id="gdrDrawer">
  <div style="display:flex;justify-content:space-between;align-items:center"><h4 id="gdrTitle" style="margin:0">Edit email</h4><button class="btn btn-sm btn-default" onclick="gdrClose()">✕</button></div>
  <form method="post" action="/admin/automations/drip/step" id="gdrForm">@csrf
   <input type="hidden" name="id"><input type="hidden" name="sequence_id" value="{{ $sel->id }}">
   <div class="row2"><div><label>Name (internal)</label><input name="name" class="form-control" required maxlength="150"></div>
    <div class="row2"><div><label>Send on day</label><input name="day_offset" type="number" class="form-control" required></div>
     <div><label>Status</label><select name="status" class="form-control"><option>DRAFT</option><option>ACTIVE</option><option>PAUSED</option></select></div></div></div>
   <label>Subject line</label><input name="subject" class="form-control" required maxlength="255">
   <label>Preview text (inbox snippet)</label><input name="preview" class="form-control" maxlength="255">
   <label>Purpose</label><input name="purpose" class="form-control" maxlength="255">
   <label>Conditions (all must pass)</label>
   <div>@foreach($conds as $k=>$v)<label style="font-weight:400;display:inline-block;margin-right:14px"><input type="checkbox" name="conditions[]" value="{{ $k }}" class="gdrC"> {{ $v }}</label>@endforeach
    <div class="meta" id="gdrCx"></div></div>
   <label>Body (HTML) — merge tags: @{{first_name}} @{{login_url}} @{{app_url}} @{{email}}. Footer and unsubscribe link are added automatically.</label>
   <div class="tb"><button type="button" class="btn btn-default" onclick="gdrWrap('<b>','</b>')"><b>B</b></button><button type="button" class="btn btn-default" onclick="gdrWrap('<p>','</p>')">¶</button>
    <button type="button" class="btn btn-default" onclick="gdrLink()">Button link</button><button type="button" class="btn btn-default" onclick="gdrIns('@{{first_name}}')">First name</button></div>
   <textarea name="body_html" class="form-control body" id="gdrBody"></textarea>
   <div style="display:flex;gap:8px;margin-top:14px"><button class="btn btn-go">Save email</button><button type="button" class="btn btn-default" onclick="gdrClose()">Cancel</button></div>
  </form>
  <div id="gdrPrevWrap" style="margin-top:18px"><label>Saved preview</label><iframe id="gdrPrev" sandbox></iframe></div>
 </div>
 @endif
</div></div></div></div>
@endsection
@section('script')
<script>
var gdrKnown=@json(array_keys($conds));
function gdrOpen(st){st=st||{};var f=document.getElementById('gdrForm');
 ['id','name','subject','preview','purpose','body_html'].forEach(function(k){f.elements[k].value=st[k]||''});
 f.elements['day_offset'].value=(st.day_offset!==undefined)?st.day_offset:0;f.elements['status'].value=st.status||'DRAFT';
 var c=[];try{c=(JSON.parse(st.conditions||'{}').all)||[]}catch(e){}if(!st.id)c=['account_active'];
 document.querySelectorAll('.gdrC').forEach(function(x){x.checked=c.indexOf(x.value)>=0});
 var unk=c.filter(function(x){return gdrKnown.indexOf(x)<0});
 document.getElementById('gdrCx').textContent=unk.length?('Not supported yet (this email will be skipped until it is): '+unk.join(', ')+'. Saving replaces it with the boxes above.'):'';
 document.getElementById('gdrTitle').textContent=st.id?'Edit email':'Add email';
 document.getElementById('gdrPrevWrap').style.display=st.id?'block':'none';if(st.id)document.getElementById('gdrPrev').src='/admin/automations/drip/step/'+st.id+'/preview';
 document.getElementById('gdrDrawer').classList.add('open');document.getElementById('gdrScrim').classList.add('open');}
function gdrClose(){document.getElementById('gdrDrawer').classList.remove('open');document.getElementById('gdrScrim').classList.remove('open');}
function gdrWrap(a,b){var t=document.getElementById('gdrBody'),s=t.selectionStart,e=t.selectionEnd;t.setRangeText(a+t.value.slice(s,e)+b,s,e,'end');t.focus();}
function gdrIns(x){var t=document.getElementById('gdrBody');t.setRangeText(x,t.selectionStart,t.selectionEnd,'end');t.focus();}
function gdrLink(){var u=prompt('Link URL','https://app.globalvoicedirect.com/');if(!u)return;var l=prompt('Button text','Sign in');if(!l)return;
 gdrIns('<a href="'+u.replace(/"/g,'')+'" style="display:inline-block;background:#FF5000;color:#fff;text-decoration:none;font-weight:600;padding:11px 20px;border-radius:10px">'+l.replace(/</g,'&lt;')+'</a>');}
jQuery(function($){if(!$.fn.sortable||!$('#gdrTl').length)return;$('#gdrTl').sortable({handle:'.grip',placeholder:'ph',update:function(){
 var ids=$('#gdrTl li[data-id]').map(function(){return $(this).data('id')}).get();
 $.post('/admin/automations/drip/sequence/{{ $sel->id ?? 0 }}/reorder',{_token:'{{ csrf_token() }}',ids:ids}).done(function(){location.reload()}).fail(function(){alert('Could not save the new order.')});}});});
</script>
@endsection
