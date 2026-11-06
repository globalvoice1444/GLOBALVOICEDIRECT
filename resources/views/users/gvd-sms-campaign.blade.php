@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>.gvd-sms input,.gvd-sms select,.gvd-sms textarea{width:100%;padding:6px 8px;border:1px solid #ccc;border-radius:4px}.gvd-sms label{font-size:12px;color:#666;margin:10px 0 4px;display:block}
.gvd-sms .phone{width:260px;margin:0 auto;border:10px solid #0f172a;border-radius:32px;padding:28px 12px;min-height:340px;background:#f8fafc}.gvd-sms .bubble{background:#e2e8f0;border-radius:16px;padding:10px 12px;font-size:13px;line-height:1.4;white-space:pre-wrap;word-wrap:break-word}
.gvd-sms .mf{display:inline-block;margin:2px}.gvd-sms .st.sched{background:#e8f0fe;color:#1a56db}.gvd-sms .st.sent{background:#e6f4ea;color:#1e7e34}</style>
<div class="row gvd-vo gvd-sms">
 <div class="col-sm-12"><div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
  <div><h5>SMS Campaign</h5><div class="muted">Send text message campaigns to your contact lists from your business numbers.</div></div>
  <button class="btn btn-primary btn-sm" type="button" onclick="smsNew()">+ New SMS campaign</button></div>
  <div class="card-block">
  @unless ($d['live'])<div class="ro">Text sending isn't connected yet. You can write, save and schedule campaigns now; they'll be sent once sending is turned on for your account.</div>@endunless
  <table class="table"><thead><tr><th>Name</th><th>Status</th><th>List</th><th>Sender</th><th>Scheduled (ET)</th><th>Sent</th><th></th></tr></thead><tbody>
  @php $ln = collect($d['lists'])->pluck('name','id'); @endphp
  @forelse ($d['campaigns'] as $c)
   <tr><td><b>{{ $c['name'] }}</b></td><td><span class="st {{ $c['status']==='scheduled'?'sched':($c['status']==='sent'?'sent':'') }}">{{ ucfirst($c['status']) }}</span></td>
   <td>{{ $ln[$c['list_id']] ?? '—' }}</td><td>{{ $c['sender'] ? \App\Portal\PhoneNumbers::fmt($c['sender']) : '—' }}</td>
   <td>{{ $c['scheduled_at'] ? \Carbon\Carbon::parse($c['scheduled_at'],'UTC')->tz('America/New_York')->format('m/d/Y g:i A') : '—' }}</td><td>{{ $c['sent_count'] }}</td>
   <td style="white-space:nowrap">@if(!in_array($c['status'],['sent','sending']))<button type="button" class="btn btn-sm btn-default" onclick='smsEdit(@json($c))'>Edit</button> @endif
   @if($admin && !in_array($c['status'],['sent','sending']))<button type="button" class="btn btn-sm btn-default" onclick="smsSend({{ $c['id'] }})">Send…</button> @endif
   <button type="button" class="btn btn-sm btn-default" onclick="smsDel({{ $c['id'] }})">Delete</button></td></tr>
  @empty <tr><td colspan="7" class="muted">No campaigns yet. Use “New SMS campaign” to write your first one.</td></tr> @endforelse
  </tbody></table></div></div></div>
 <div class="col-lg-7"><div class="card"><div class="card-header"><h5 id="sms-h">Compose</h5></div><div class="card-block">
  <input type="hidden" id="sms-id">
  <label>Campaign name</label><input id="sms-name" maxlength="120" placeholder="e.g. November reminders">
  <div class="row"><div class="col-sm-6"><label>Audience / list</label><select id="sms-list"><option value="">Choose a list…</option>
   @foreach ($d['lists'] as $l)<option value="{{ $l['id'] }}">{{ $l['name'] }} ({{ number_format($l['count']) }} contacts)</option>@endforeach</select>
   @if (!count($d['lists']))<div class="muted">No contact lists yet. <a href="/smscampaign">Add contacts</a>.</div>@endif</div>
  <div class="col-sm-6"><label>Sender number</label><select id="sms-from"><option value="">Choose a number…</option>
   @foreach ($d['numbers'] as $n)<option value="{{ $n['id'] }}">{{ $n['display'] }}</option>@endforeach</select></div></div>
  <label>Message</label><textarea id="sms-msg" rows="4" maxlength="1200"></textarea>
  <div><span class="muted">Merge fields:</span> @foreach (['{first_name}','{last_name}','{business}'] as $m)<button type="button" class="btn btn-sm btn-default mf" onclick="smsIns('{{ $m }}')">{{ $m }}</button>@endforeach</div>
  <div class="muted" id="sms-count" style="margin-top:6px"></div>
  <div class="row"><div class="col-sm-6"><label>Schedule send (Eastern time, optional)</label><input type="datetime-local" id="sms-at"></div></div>
  <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
   <button type="button" class="btn btn-default" onclick="smsSave(false)">Save draft</button>
   <button type="button" class="btn btn-primary" onclick="smsSave(true)">Schedule send</button></div>
  <div id="sms-msgbox" class="muted" style="margin-top:8px"></div>
 </div></div></div>
 <div class="col-lg-5"><div class="card"><div class="card-header"><h5>Phone preview</h5></div><div class="card-block">
  <div class="phone"><div class="muted" style="text-align:center;margin-bottom:12px" id="sms-pfrom">Your number</div><div class="bubble" id="sms-prev"></div></div>
 </div></div></div>
</div>
<script>
(function(){
 var OPT=' {{ $d['optOut'] }}', TOK='{{ csrf_token() }}', BIZ=@json(session()->get('nice_name') ?: 'your business');
 var $=function(i){return document.getElementById(i);};
 function seg(s){var u=/[^\n\r\x20-\x7E£¥èéùìòÇØøÅåΔ_ΦΓΛΩΠΨΣΘΞÆæßÉ¤¡ÄÖÑÜ§¿äöñüà€]/.test(s),n=Array.from(s).length,one=u?70:160,m=u?67:153;return {n:n,s:n<=one?1:Math.ceil(n/m),e:u?'Unicode':'GSM-7'};}
 function upd(){var full=$('sms-msg').value.replace(/\s+$/,'')+OPT,g=seg(full);$('sms-count').textContent=g.n+' characters · '+g.s+' segment'+(g.s>1?'s':'')+' ('+g.e+') · opt-out text "'+OPT.trim()+'" is added automatically';
  $('sms-prev').textContent=full.split('{first_name}').join('Maria').split('{last_name}').join('Lopez').split('{business}').join(BIZ);
  var o=$('sms-from').selectedOptions[0];$('sms-pfrom').textContent=o&&o.value?o.text:'Your number';}
 ['sms-msg','sms-from'].forEach(function(i){$(i).addEventListener('input',upd);$(i).addEventListener('change',upd);});
 window.smsIns=function(m){var t=$('sms-msg'),p=t.selectionStart||t.value.length;t.value=t.value.slice(0,p)+m+t.value.slice(p);t.focus();upd();};
 window.smsNew=function(){$('sms-id').value='';$('sms-name').value='';$('sms-msg').value='';$('sms-list').value='';$('sms-from').value='';$('sms-at').value='';$('sms-h').textContent='Compose';upd();$('sms-name').focus();};
 window.smsEdit=function(c){$('sms-id').value=c.id;$('sms-name').value=c.name;$('sms-msg').value=c.message;$('sms-list').value=c.list_id||'';$('sms-from').value=c.sender||'';
  if(c.scheduled_at){var d=new Date(c.scheduled_at.replace(' ','T')+'Z');var p=new Intl.DateTimeFormat('en-CA',{timeZone:'America/New_York',year:'numeric',month:'2-digit',day:'2-digit',hour:'2-digit',minute:'2-digit',hour12:false}).formatToParts(d).reduce(function(a,x){a[x.type]=x.value;return a;},{});$('sms-at').value=p.year+'-'+p.month+'-'+p.day+'T'+(p.hour==='24'?'00':p.hour)+':'+p.minute;}else $('sms-at').value='';
  $('sms-h').textContent='Edit: '+c.name;upd();window.scrollTo(0,$('sms-h').getBoundingClientRect().top+window.scrollY-80);};
 function post(u,b){b._token=TOK;return fetch(u,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':TOK,'Accept':'application/json'},body:JSON.stringify(b)}).then(function(r){return r.json().catch(function(){return {error:'Unexpected response ('+r.status+')'};});});}
 window.smsSave=function(sched){if(sched&&!$('sms-at').value){$('sms-msgbox').textContent='Pick a date and time to schedule.';return;}
  post('/user/sms-campaigns/save',{id:$('sms-id').value,name:$('sms-name').value,message:$('sms-msg').value,list_id:$('sms-list').value,sender:$('sms-from').value,schedule_at:sched?$('sms-at').value:''}).then(function(j){if(j.error){$('sms-msgbox').textContent=j.error;return;}location.reload();});};
 window.smsDel=function(id){if(!confirm('Delete this campaign?'))return;post('/user/sms-campaigns/delete',{id:id}).then(function(){location.reload();});};
 window.smsSend=function(id){
  post('/user/sms-campaigns/send',{id:id,confirm:'SEND',dry:1}).then(function(j){if(j.error){alert(j.error);return;}
   var msg='This campaign would go to '+j.recipients+' contact'+(j.recipients===1?'':'s')+(j.skipped?' ('+j.skipped+' skipped: invalid numbers)':'')+', '+j.segments+' segment(s) each.\n\nSample:\n'+(j.sample||'(no valid contacts)');
   if(!j.live){alert(msg+'\n\nText sending isn\'t connected yet, so nothing was sent.');return;}
   if(!j.recipients||!confirm(msg+'\n\nSend now? This can\'t be undone.'))return;
   post('/user/sms-campaigns/send',{id:id,confirm:'SEND',dry:0}).then(function(k){alert(k.error||('Sent: '+k.sent+', failed: '+k.failed));location.reload();});});};
 upd();
})();
</script>
@include('users.partials.vo-foot', ['page' => 'smscampaign'])
@endsection
