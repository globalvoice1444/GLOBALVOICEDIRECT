@extends('admin.layouts.layout')
@section('css')
@include('admin._wfcss')
<style>
.gwf .builder{display:grid;grid-template-columns:230px 1fr;min-height:calc(100vh - 230px)}
.gwf .palette{background:#fff;border-right:1px solid var(--l);padding:14px}.gwf .palette h4{margin:14px 0 8px;font-size:11px;text-transform:uppercase;color:var(--m);letter-spacing:.6px}
.gwf .pi{display:flex;gap:8px;align-items:center;padding:8px 10px;border:1px solid var(--l);border-radius:9px;margin-bottom:6px;cursor:grab;transition:.15s;background:#fff;user-select:none}.gwf .pi:hover{border-color:var(--o);transform:translateX(2px)}
.gwf .soon{margin-left:auto;font-size:9px;background:#f1f5f9;color:var(--m);padding:2px 6px;border-radius:8px}.gwf .pi.dis{opacity:.55;cursor:not-allowed}
.gwf .canvas{position:relative;background:#f7f9fb;background-image:radial-gradient(#d7dde5 1px,transparent 1px);background-size:20px 20px;overflow:auto}
.gwf .cbar{position:sticky;top:0;display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;padding:10px 16px;background:#ffffffe6;backdrop-filter:blur(4px);border-bottom:1px solid var(--l);z-index:2}
.gwf .flow{display:flex;flex-direction:column;align-items:center;padding:28px 10px 60px}
.gwf .node{width:270px;background:#fff;border-radius:12px;box-shadow:0 3px 12px #0000001a;border-left:4px solid var(--n);padding:11px 13px;cursor:pointer;transition:.18s;position:relative}.gwf .node:hover{transform:translateY(-2px);box-shadow:0 8px 20px #0000001f}
.gwf .node.trg{border-left-color:var(--o)}.gwf .node.lg{border-left-color:#0f766e}.gwf .node.sel{outline:2px solid var(--o);outline-offset:2px}
.gwf .nk{font-size:10px;text-transform:uppercase;letter-spacing:.6px;color:var(--m)}.gwf .nt{font-weight:700;font-size:14px;margin-top:3px;color:var(--n)}.gwf .nd{color:var(--m);margin-top:2px;font-size:12px}
.gwf .nx{position:absolute;right:8px;top:6px;display:none;gap:2px}.gwf .node:hover .nx{display:flex}.gwf .nx span{font-size:11px;color:var(--m);padding:1px 5px;border-radius:5px;background:#f1f5f9}.gwf .nx span:hover{background:var(--o);color:#fff}
.gwf .ln{width:2px;height:30px;background:#cbd5e1;position:relative}.gwf .plus{position:absolute;left:-10px;top:5px;width:22px;height:22px;border-radius:50%;background:#fff;border:1px solid #cbd5e1;text-align:center;line-height:20px;color:var(--o);font-weight:700;cursor:pointer;transition:.15s}
.gwf .plus.over,.gwf .plus:hover{background:var(--o);color:#fff;transform:scale(1.2)}
.gwf .split{display:flex;gap:40px;padding-top:10px;border-top:2px solid #cbd5e1;margin-top:0}.gwf .br{display:flex;flex-direction:column;align-items:center;min-width:270px}
.gwf .bl{font-size:11px;font-weight:700;padding:3px 10px;border-radius:10px;margin-bottom:4px}.gwf .yes{background:#dcfce7;color:#166534}.gwf .no{background:#fee2e2;color:#991b1b}
.gwf .end{font-size:11px;color:#94a3b8;border:1px dashed #cbd5e1;border-radius:20px;padding:4px 14px}
.gwf .drawer{position:fixed;top:0;right:0;bottom:0;width:420px;max-width:100vw;background:#fff;box-shadow:-10px 0 30px #0002;display:none;flex-direction:column;z-index:2001}
.gwf .drawer.open{display:flex;animation:gwfSlide .35s cubic-bezier(.2,.8,.2,1)}
.gwf .dh{background:var(--n);color:#fff;padding:15px 18px;border-bottom:3px solid var(--o);font-weight:700;display:flex;justify-content:space-between}.gwf .db{padding:18px;overflow:auto;flex:1}
.gwf .db label{display:block;font-weight:600;margin:12px 0 5px;color:#334155}.gwf .db input,.gwf .db select,.gwf .db textarea{width:100%;padding:9px 10px;border:1px solid #d1d5db;border-radius:8px;font:inherit}
.gwf .df{padding:14px 18px;border-top:1px solid var(--l);display:flex;gap:8px;justify-content:flex-end}
.gwf .menu{position:absolute;z-index:5;background:#fff;border-radius:10px;box-shadow:0 10px 30px #0003;padding:6px;width:220px;animation:gwfPop .2s both}.gwf .menu div{padding:7px 10px;border-radius:7px;cursor:pointer}.gwf .menu div:hover{background:#fff4ec}
</style>
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gwf">
<div class="top"><div><div class="crumb">Admin › Automations › <a href="/admin/automations/workflows">Workflows</a> › <b>Builder</b></div><h1>{{ $wf->name }}</h1></div>
 <div class="nav"><a href="/admin/automations/workflows">Workflows</a><a class="on">Builder</a><a href="/admin/automations/workflows?tab=tpl">Templates</a><a href="/admin/automations/workflows?tab=stats">Analytics</a></div></div>
@if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
<div class="sw {{ $enabled?'ok':'' }}"><b>Global send switch: {{ $enabled?'ON':'OFF' }}</b> · This workflow is <b>{{ $wf->status }}</b> · {{ $enr->total ?? 0 }} enrolled, {{ $enr->active ?? 0 }} in progress. Emails respect unsubscribes and the max 1 email per person per {{ config('drip.min_hours_between') }}h cap.</div>
<div class="panel builder pop">
 <div class="palette"><b style="color:var(--n)">Add a step</b><div class="hint">Drag onto a + on the canvas, or click to add after the selected step</div>
  <h4>Actions</h4>
  <div class="pi" draggable="true" data-t="email"><span class="ic a">✉</span>Send email</div>
  <div class="pi" draggable="true" data-t="wait"><span class="ic lg">⏸</span>Wait</div>
  <div class="pi" draggable="true" data-t="branch"><span class="ic lg">⑂</span>If / else branch</div>
  <div class="pi" draggable="true" data-t="tag"><span class="ic a">#</span>Add / remove tag</div>
  <div class="pi" draggable="true" data-t="enroll_drip"><span class="ic a">≡</span>Enroll in drip sequence</div>
  <div class="pi" draggable="true" data-t="notify"><span class="ic a">✓</span>Notify admin / task</div>
  <h4>Coming soon</h4>
  <div class="pi dis"><span class="ic x">✆</span>SMS<span class="soon">SOON</span></div><div class="pi dis"><span class="ic x">♪</span>Voice message<span class="soon">SOON</span></div><div class="pi dis"><span class="ic x">⚡</span>Webhook<span class="soon">SOON</span></div>
 </div>
 <div class="canvas"><div class="cbar">
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap"><input id="wfName" class="form-control form-control-sm" style="width:240px" value="{{ $wf->name }}" maxlength="150"><span class="st {{ $wf->status }}">{{ ucfirst(strtolower($wf->status)) }}</span></div>
  <div style="display:flex;gap:8px;flex-wrap:wrap">
   <form method="post" action="/admin/automations/workflows/{{ $wf->id }}/test">@csrf<button class="b g sm">Send test emails to me</button></form>
   <button class="b g sm" onclick="wfSave()">Save</button>
   <form method="post" action="/admin/automations/workflows/{{ $wf->id }}/status" onsubmit="return {{ $wf->status=='ACTIVE' ? 'true' : "confirm('Activate this workflow? It starts with events from now on (no backfill). It only runs while the global send switch is on.')" }}">@csrf
    <input type="hidden" name="status" value="{{ $wf->status=='ACTIVE'?'PAUSED':'ACTIVE' }}"><button class="b sm">{{ $wf->status=='ACTIVE'?'Pause':'Activate' }}</button></form>
   @if($wf->status=='PAUSED')<form method="post" action="/admin/automations/workflows/{{ $wf->id }}/delete" onsubmit="return confirm('Delete this workflow?')">@csrf<button class="b g sm">Delete</button></form>@endif
  </div></div>
  <div class="flow" id="flow"></div>
 </div>
</div>
<div class="panel pop"><div class="ph">RECENT ACTIVITY</div><div class="pb">@if($log->isEmpty())<div class="empty">Nothing has run for this workflow yet.</div>@else
 <table><tr><th>When</th><th>Step</th><th>Result</th><th>Detail</th></tr>@foreach($log as $x)<tr><td>{{ $x->created_at }}</td><td>{{ $x->action }}</td><td>{{ $x->result }}</td><td>{{ $x->detail }}</td></tr>@endforeach</table>@endif</div></div>
<div class="drawer" id="wfDrawer"><div class="dh"><span id="wfDT">Step settings</span><span style="cursor:pointer" onclick="wfClose()">✕</span></div><div class="db" id="wfDB"></div>
 <div class="df"><button class="b g" onclick="wfDel()">Remove step</button><button class="b" onclick="wfApply()">Done</button></div></div>
</div></div></div></div>
@endsection
@section('script')
<script>
(function(){
var T=@json($wf->trigger), TC=@json(json_decode((string)$wf->trigger_config,true) ?: new stdClass), TR=@json($triggers), RULES=@json($rules);
var LIB=@json($lib), SEQS=@json($seqs), tree=@json($tree), sel=null, selPath=null, dirty=false;
var LBL={email:'Action · Email',wait:'Wait',branch:'If / else',tag:'Action · Tag',enroll_drip:'Action · Drip',notify:'Action · Notify admin'};
function esc(s){return String(s==null?'':s).replace(/[&<>"]/g,function(c){return{'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;'}[c]})}
function title(n){var c=n.config||{};switch(n.type){
 case 'email':var s=LIB.find(function(x){return x.id==c.step_id});return s?('Send “'+s.subject+'”'):'Choose an email';
 case 'wait':return 'Wait '+(c.amount||1)+' '+(c.unit||'days');
 case 'branch':return RULES[c.rule]||'Choose a condition';
 case 'tag':return ((c.op=='remove')?'Remove':'Add')+' tag “'+(c.tag||'…')+'”';
 case 'enroll_drip':var q=SEQS.find(function(x){return x.id==c.sequence_id});return q?('Enroll in '+q.name):'Choose a sequence';
 case 'notify':return c.title||'Notify admin';}return n.type}
function desc(n){var c=n.config||{};if(n.type=='email'){var s=LIB.find(function(x){return x.id==c.step_id});return s?(s.seq+(s.status!='ACTIVE'?' · '+s.status+' (will be skipped)':'')):'From drip library'}
 if(n.type=='branch'&&c.rule=='has_tag')return 'Tag: '+(c.tag||'');if(n.type=='branch'&&c.rule=='logged_in_within')return 'Within '+(c.days||7)+' days';
 if(n.type=='notify')return c.due_minutes?('Due in '+c.due_minutes+' min'):'Admin alert';if(n.type=='enroll_drip'){var q=SEQS.find(function(x){return x.id==c.sequence_id});return q?('Sequence is '+q.status):''}return ''}
var di=0;
function plus(path,idx){return '<div class="ln"><span class="plus" data-p="'+path+'" data-i="'+idx+'" title="Add a step here">+</span></div>'}
function renderPath(arr,path){var h='';arr.forEach(function(n,i){var p=path+'.'+i;
 h+='<div class="node pop '+(n.type=='wait'||n.type=='branch'?'lg':'')+(sel===n?' sel':'')+'" data-p="'+p+'" style="animation-delay:'+(Math.min(di++,12)*.05)+'s"><div class="nx"><span data-mv="-1">↑</span><span data-mv="1">↓</span></div><div class="nk">'+LBL[n.type]+'</div><div class="nt">'+esc(title(n))+'</div><div class="nd">'+esc(desc(n))+'</div></div>';
 if(n.type=='branch'){h+='<div class="ln"></div><div class="split"><div class="br"><span class="bl yes">YES</span>'+plus(p+'.yes',0)+renderPath(n.yes,p+'.yes')+'<div class="end">End</div></div><div class="br"><span class="bl no">NO</span>'+plus(p+'.no',0)+renderPath(n.no,p+'.no')+'<div class="end">End</div></div></div>'}
 else h+=plus(path,i+1)});
 return h}
function trigDesc(){return T=='no_login'?('No login for '+(TC.days||7)+' days'):(TR[T]||T)}
function render(){di=0;var f=document.getElementById('flow');
 f.innerHTML='<div class="node trg pop" data-trig="1"><div class="nk">Trigger</div><div class="nt">'+esc(trigDesc())+'</div><div class="nd">'+(T=='payment_failed'?'Fires from the billing hook when connected':(T=='manual'?'Not started automatically':'Only events after activation'))+'</div></div>'+plus('m',0)+renderPath(tree,'m')+(tree.length&&tree[tree.length-1].type=='branch'?'':'<div class="end">End</div>');}
function resolve(p){var parts=p.split('.'),arr=tree;for(var i=1;i<parts.length;i++){var k=parts[i];if(k=='yes'||k=='no')arr=arr[k];else if(i<parts.length-1&&(parts[i+1]=='yes'||parts[i+1]=='no'))arr=arr[+k];else return {arr:arr,i:+k}}return {arr:arr,i:arr.length}}
function arrOf(p){var r=resolve(p+'.0');return r.arr}
function mk(t){var n={id:0,type:t,config:{}};if(t=='wait')n.config={amount:1,unit:'days'};if(t=='branch'){n.config={rule:'logged_in_since_enrolled'};n.yes=[];n.no=[]}if(t=='tag')n.config={op:'add',tag:''};if(t=='notify')n.config={title:'Follow up with @{{first_name}}',severity:'info'};return n}
function insert(path,idx,t){var a=arrOf(path);var n=mk(t);a.splice(idx,0,n);dirty=true;sel=n;render();openD(n)}
document.addEventListener('click',function(e){var pl=e.target.closest('.gwf .plus');if(pl){e.stopPropagation();menu(pl);return}
 var mv=e.target.closest('.gwf .nx span');if(mv){var nd=mv.closest('.node'),r=resolve(nd.dataset.p),j=r.i+(+mv.dataset.mv);if(j>=0&&j<r.arr.length){var x=r.arr.splice(r.i,1)[0];r.arr.splice(j,0,x);dirty=true;render()}e.stopPropagation();return}
 var nd=e.target.closest('.gwf .node');if(nd){if(nd.dataset.trig){sel='T';openT();}else{var r=resolve(nd.dataset.p);sel=r.arr[r.i];selPath=nd.dataset.p;openD(sel)}render();return}
 var pi=e.target.closest('.gwf .pi:not(.dis)');if(pi){if(sel&&sel!=='T'&&selPath){var r=resolve(selPath);var p=selPath.split('.');p.pop();insert(p.join('.'),r.i+1,pi.dataset.t)}else insert('m',tree.length,pi.dataset.t);return}
 var m=document.querySelector('.gwf .menu');if(m&&!e.target.closest('.menu'))m.remove()});
function menu(pl){var o=document.querySelector('.gwf .menu');if(o)o.remove();var m=document.createElement('div');m.className='menu';
 Object.keys(LBL).forEach(function(t){var d=document.createElement('div');d.textContent=document.querySelector('.pi[data-t="'+t+'"]').textContent;d.onclick=function(){m.remove();insert(pl.dataset.p,+pl.dataset.i,t)};m.appendChild(d)});
 var r=pl.getBoundingClientRect(),c=document.querySelector('.gwf .canvas').getBoundingClientRect(),cv=document.querySelector('.gwf .canvas');m.style.left=(r.left-c.left+cv.scrollLeft+30)+'px';m.style.top=(r.top-c.top+cv.scrollTop)+'px';cv.appendChild(m)}
document.querySelectorAll('.gwf .pi[draggable]').forEach(function(p){p.addEventListener('dragstart',function(e){e.dataTransfer.setData('text/plain',p.dataset.t)})});
var fl=document.getElementById('flow');
fl.addEventListener('dragover',function(e){var pl=e.target.closest('.plus');if(pl){e.preventDefault();pl.classList.add('over')}});
fl.addEventListener('dragleave',function(e){var pl=e.target.closest('.plus');if(pl)pl.classList.remove('over')});
fl.addEventListener('drop',function(e){var pl=e.target.closest('.plus');if(!pl)return;e.preventDefault();insert(pl.dataset.p,+pl.dataset.i,e.dataTransfer.getData('text/plain'))});
function opt(list,val){return list.map(function(o){return '<option value="'+esc(o[0])+'"'+(String(o[0])==String(val)?' selected':'')+'>'+esc(o[1])+'</option>'}).join('')}
function openT(){document.getElementById('wfDT').textContent='Trigger';var h='<label>Start this workflow when</label><select id="fT">'+opt(Object.keys(TR).map(function(k){return[k,TR[k]]}),T)+'</select>';
 h+='<label>Days without login (for “No login”)</label><input id="fD" type="number" min="1" max="365" value="'+(TC.days||7)+'"><div class="hint">Only customers whose logins we have recorded qualify (tracking started Nov 2, 2026). Each lapse enrolls once.</div>';
 document.getElementById('wfDB').innerHTML=h;document.getElementById('wfDrawer').classList.add('open')}
function openD(n){var c=n.config||{},h='';document.getElementById('wfDT').textContent='Step settings · '+LBL[n.type];
 if(n.type=='email'){h='<label>Email (from your drip library)</label><select data-k="step_id"><option value="">Choose…</option>'+opt(LIB.map(function(s){return[s.id,s.seq+' — '+s.name+(s.status!='ACTIVE'?' ['+s.status+']':'')]}),c.step_id)+'</select><div class="hint">Edit wording in Drip emails; every workflow uses the latest version. Only ACTIVE emails send. Unsubscribed contacts are skipped and the shared per-person cap applies.</div>'}
 if(n.type=='wait')h='<label>Wait for</label><input data-k="amount" type="number" min="1" value="'+(c.amount||1)+'"><label>Unit</label><select data-k="unit">'+opt([['minutes','Minutes'],['hours','Hours'],['days','Days']],c.unit||'days')+'</select>';
 if(n.type=='branch')h='<label>Condition</label><select data-k="rule">'+opt(Object.keys(RULES).map(function(k){return[k,RULES[k]]}),c.rule)+'</select><label>Tag (for “Has tag”)</label><input data-k="tag" value="'+esc(c.tag||'')+'"><label>Days (for “within N days”)</label><input data-k="days" type="number" min="1" value="'+(c.days||7)+'"><div class="hint">YES and NO paths each end the workflow when they run out of steps.</div>';
 if(n.type=='tag')h='<label>Operation</label><select data-k="op">'+opt([['add','Add tag'],['remove','Remove tag']],c.op)+'</select><label>Tag</label><input data-k="tag" maxlength="60" value="'+esc(c.tag||'')+'">';
 if(n.type=='enroll_drip')h='<label>Drip sequence</label><select data-k="sequence_id"><option value="">Choose…</option>'+opt(SEQS.map(function(s){return[s.id,s.name+' ('+s.status+')']}),c.sequence_id)+'</select><div class="hint">The sequence only sends while it is ACTIVE. A customer is never enrolled twice in the same sequence.</div>';
 if(n.type=='notify')h='<label>Alert title</label><input data-k="title" value="'+esc(c.title||'')+'"><label>Task details</label><textarea data-k="task" rows="3">'+esc(c.task||'')+'</textarea><label>Severity</label><select data-k="severity">'+opt([['info','Info'],['warning','Warning'],['critical','Critical']],c.severity||'info')+'</select><label>Due in (minutes, optional)</label><input data-k="due_minutes" type="number" min="0" value="'+esc(c.due_minutes||'')+'"><div class="hint">Shows in Admin › Alerts with the customer’s name, login and email. Merge tags: @{{first_name}}, @{{email}}.</div>';
 document.getElementById('wfDB').innerHTML=h;document.getElementById('wfDrawer').classList.add('open')}
window.wfApply=function(){if(sel==='T'){T=document.getElementById('fT').value;TC.days=+document.getElementById('fD').value||7}else if(sel){document.querySelectorAll('#wfDB [data-k]').forEach(function(i){sel.config[i.dataset.k]=i.value})}dirty=true;wfClose();render()};
window.wfClose=function(){document.getElementById('wfDrawer').classList.remove('open')};
window.wfDel=function(){if(!sel||sel==='T')return wfClose();(function rm(a){for(var i=0;i<a.length;i++){if(a[i]===sel){a.splice(i,1);return true}if(a[i].type=='branch'&&(rm(a[i].yes)||rm(a[i].no)))return true}})(tree);sel=null;selPath=null;dirty=true;wfClose();render()};
window.wfSave=function(){jQuery.post('/admin/automations/workflows/{{ $wf->id }}/save',{_token:'{{ csrf_token() }}',name:document.getElementById('wfName').value,trigger:T,days:TC.days||7,tree:JSON.stringify(tree)})
 .done(function(){dirty=false;location.reload()}).fail(function(x){alert('Could not save: '+(x.responseJSON&&x.responseJSON.message||x.status))})};
window.addEventListener('beforeunload',function(e){if(dirty){e.preventDefault();e.returnValue=''}});
render();
})();
</script>
@endsection
