@extends('layouts.user.user_main')
@section('content')
<style>
.ec{--n:#1E2A36;--o:#FF5000;--l:#E6EAF0;--m:#64748B;--bg:#F6F8FB;font-family:Inter,system-ui,Segoe UI,sans-serif;color:#0F1B26}
.ec *{box-sizing:border-box}.ec .hero{background:var(--n);color:#fff;border-radius:16px;padding:24px 26px;display:flex;justify-content:space-between;align-items:center;gap:16px;flex-wrap:wrap;margin-bottom:18px}
.ec .hero h2{margin:0;font-size:24px;font-weight:700;color:#fff}.ec .hero p{margin:4px 0 0;color:#C9D3DE;font-size:14px}
.ec .chips{display:flex;gap:10px;flex-wrap:wrap;margin-top:14px}.ec .chip{background:rgba(255,255,255,.09);border-radius:10px;padding:8px 12px;font-size:13px}.ec .chip b{display:block;font-size:18px;color:#fff}
.ec .btn{display:inline-flex;align-items:center;gap:6px;border:1px solid var(--l);background:#fff;color:#0F1B26;border-radius:10px;padding:9px 16px;font-weight:600;font-size:14px;cursor:pointer;text-decoration:none;line-height:1.2}
.ec .btn:hover{border-color:#c9d2dc}.ec .btn.pri{background:var(--o);border-color:var(--o);color:#fff}.ec .btn.dark{background:var(--n);border-color:var(--n);color:#fff}.ec .btn.sm{padding:5px 10px;font-size:12.5px;border-radius:8px}.ec .btn[disabled]{opacity:.5;cursor:not-allowed}
.ec .tabs{display:flex;gap:4px;border-bottom:1px solid var(--l);margin-bottom:16px}.ec .tabs a{padding:10px 16px;color:var(--m);font-weight:600;cursor:pointer;border-bottom:2px solid transparent;text-decoration:none}.ec .tabs a.on{color:var(--n);border-color:var(--o)}
.ec .card{background:#fff;border:1px solid var(--l);border-radius:14px;padding:20px;margin-bottom:16px}.ec .muted{color:var(--m);font-size:13px}
.ec table{width:100%;border-collapse:collapse;font-size:14px}.ec th{color:var(--m);font-weight:600;font-size:12px;text-transform:uppercase;letter-spacing:.03em;text-align:left;padding:8px;border-bottom:1px solid var(--l)}.ec td{padding:10px 8px;border-bottom:1px solid #F0F2F5;vertical-align:middle}
.ec .pill{display:inline-block;border-radius:999px;padding:3px 10px;font-size:12px;font-weight:600;background:#EEF2F6;color:#475569}.ec .pill.SENDING,.ec .pill.SCHEDULED{background:#FFF1E8;color:#C2410C}.ec .pill.DONE,.ec .pill.active{background:#E7F6EC;color:#15803D}.ec .pill.PAUSED,.ec .pill.unsubscribed,.ec .pill.bounced,.ec .pill.complained{background:#FDECEC;color:#B42318}
.ec .tag{display:inline-block;background:#EEF2F6;border-radius:6px;padding:1px 7px;font-size:12px;margin:1px}
.ec input[type=text],.ec input[type=email],.ec input[type=datetime-local],.ec input[type=search],.ec select,.ec textarea{width:100%;border:1px solid var(--l);border-radius:10px;padding:9px 11px;font-size:14px;background:#fff}
.ec label.f{display:block;font-size:12.5px;font-weight:600;color:#334155;margin:14px 0 6px}.ec .row2{display:grid;grid-template-columns:1fr 1fr;gap:14px}@media(max-width:800px){.ec .row2{grid-template-columns:1fr}}
.ec .steps{display:flex;gap:8px;margin-bottom:18px;flex-wrap:wrap}.ec .step{flex:1;min-width:140px;border:1px solid var(--l);border-radius:12px;padding:10px 12px;background:#fff;cursor:pointer;font-size:13px;color:var(--m)}.ec .step b{display:block;color:#0F1B26;font-size:14px}.ec .step.on{border-color:var(--o);box-shadow:0 0 0 3px rgba(255,80,0,.12)}.ec .step.done b:after{content:" ✓";color:#15803D}
.ec .opt{border:1px solid var(--l);border-radius:12px;padding:12px 14px;display:flex;gap:10px;align-items:flex-start;cursor:pointer;margin-bottom:8px}.ec .opt.on{border-color:var(--o);background:#FFF8F4}
.ec .tb{display:flex;gap:4px;flex-wrap:wrap;border:1px solid var(--l);border-bottom:0;border-radius:10px 10px 0 0;padding:6px;background:var(--bg)}.ec .tb button{border:0;background:transparent;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:13px}.ec .tb button:hover{background:#E6EAF0}
.ec .ed{border:1px solid var(--l);border-radius:0 0 10px 10px;min-height:320px;padding:18px;outline:none;font-size:15px;line-height:1.6;background:#fff;overflow:auto}.ec .ed:focus{border-color:#c9d2dc}
.ec .tpls{display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:8px}.ec .tpl{border:1px solid var(--l);border-radius:10px;padding:10px;cursor:pointer;font-size:13px;background:#fff;text-align:left}.ec .tpl:hover{border-color:var(--o)}
.ec .mt{font-family:ui-monospace,Menlo,monospace;font-size:12px}.ec iframe.pv{width:100%;height:560px;border:1px solid var(--l);border-radius:12px;background:#F4F6F9}
.ec .msg{margin-top:10px;font-size:14px}.ec .msg.err{color:#B42318}.ec .msg.ok{color:#15803D}.ec .note{background:#FFF8E6;border:1px solid #FDE7A7;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:14px}
.ec .bar{height:8px;background:#EEF2F6;border-radius:6px;overflow:hidden;min-width:90px}.ec .bar i{display:block;height:100%;background:var(--o)}
.ec .stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(110px,1fr));gap:10px;margin:12px 0}.ec .stat{background:var(--bg);border-radius:10px;padding:10px 12px}.ec .stat b{font-size:20px;display:block}
.ec .flt{display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px}.ec .flt>*{width:auto}.ec .hide{display:none!important}
</style>
<div class="ec" id="ec">
 <div class="hero"><div><h2>Email campaigns</h2><p>Send professional emails to your customers and leads, from {{ $d['sender']['business'] }}.</p>
  <div class="chips"><div class="chip"><b>{{ number_format((int)($d['counts']->active ?? 0)) }}</b>active contacts</div><div class="chip"><b>{{ number_format($d['today']) }} / {{ number_format($d['limit']) }}</b>sent today (daily limit)</div><div class="chip"><b>{{ number_format($d['suppressed']) }}</b>unsubscribed</div></div></div>
  <div><button class="btn pri" onclick="EC.newCamp()">+ New campaign</button></div></div>
 @if($d['demo'])<div class="note">This is a sample account. You can look around, but nothing is saved or sent.</div>@endif
 @if($d['blocked'])<div class="note"><b>Sending is paused for your account.</b> {{ $d['blocked'] }}</div>@endif
 @if(!$d['smtp'] || !$d['smtp']['verified_at'])<div class="note" style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap"><span><b>Connect your email to start sending.</b> Your campaigns go out from your own mailbox (Gmail, Outlook, Yahoo, Zoho or any SMTP), so replies come straight to you.</span><button class="btn sm dark" onclick="document.querySelector('[data-t=smtp]').click()">Connect your email</button></div>@endif
 @if(!$d['live'])<div class="note">Email sending is temporarily switched off. You can still build and schedule campaigns, and they will go out once it is back on.</div>@endif

 <div id="home">
 <div class="tabs"><a data-t="camps" class="on">Campaigns</a><a data-t="contacts">Contacts</a><a data-t="seqs">Sequences</a><a data-t="autos">Automations</a><a data-t="tpls">Templates</a><a data-t="smtp">Sending email @if(!$d['smtp'] || !$d['smtp']['verified_at'])<span class="pill PAUSED" style="margin-left:4px">Connect</span>@endif</a></div>

 <div data-p="camps"><div class="card" style="padding:8px 12px;overflow-x:auto">
  <table><thead><tr><th>Campaign</th><th>Status</th><th>Audience</th><th>When (ET)</th><th>Progress</th><th>Opens</th><th></th></tr></thead><tbody id="camp-rows"></tbody></table>
  <div id="camp-empty" class="muted hide" style="padding:30px;text-align:center">No campaigns yet. Click <b>New campaign</b> to send your first email in four easy steps.</div></div>
  <div class="card hide" id="report"><div style="display:flex;justify-content:space-between;align-items:center"><h4 style="margin:0" id="rep-h">Report</h4><button class="btn sm" onclick="EC.$('report').classList.add('hide')">Close</button></div>
   <div class="stats" id="rep-stats"></div><p class="muted" style="font-size:12px;margin:6px 0">Opens are approximate: some email apps block images, and others (like Apple Mail) open them automatically. Clicks are more reliable. <!-- trk --></p><table><thead><tr><th>Email</th><th>Status</th><th>Sent (ET)</th><th>Opened</th><th>Opens</th><th>Clicks</th><th>Note</th></tr></thead><tbody id="rep-rows"></tbody></table></div>
 </div>

 <div data-p="contacts" class="hide">
  <div class="card"><div style="display:flex;justify-content:space-between;gap:10px;flex-wrap:wrap;align-items:center">
   <div class="flt" style="margin:0"><input type="search" id="c-q" placeholder="Search name, email, business" style="width:240px"><select id="c-tag" style="width:160px"><option value="">All tags</option>@foreach($d['tags'] as $t => $n)<option value="{{ $t }}">{{ $t }} ({{ $n }})</option>@endforeach</select>
    <select id="c-st" style="width:150px"><option value="">Any status</option><option value="active">Active</option><option value="unsubscribed">Unsubscribed</option><option value="bounced">Bounced</option><option value="complained">Complained</option></select></div>
   <div style="display:flex;gap:6px;flex-wrap:wrap"><button class="btn sm" onclick="EC.showAdd()">+ Add contact</button><button class="btn sm dark" onclick="EC.showImport()">Import CSV</button><a class="btn sm" href="/user/email-campaigns/contacts/export">Export</a></div></div>
   <div id="addbox" class="hide" style="margin-top:14px;border-top:1px solid var(--l);padding-top:6px"><input type="hidden" id="a-id"><div class="row2"><div><label class="f">Email *</label><input type="email" id="a-email"></div><div><label class="f">Business name</label><input type="text" id="a-biz"></div></div>
    <div class="row2"><div><label class="f">First name</label><input type="text" id="a-first"></div><div><label class="f">Last name</label><input type="text" id="a-last"></div></div><label class="f">Tags (comma separated)</label><input type="text" id="a-tags" placeholder="customers, vip">
    <div style="margin-top:12px;display:flex;gap:8px"><button class="btn pri sm" onclick="EC.saveContact()">Save contact</button><button class="btn sm" onclick="EC.$('addbox').classList.add('hide')">Cancel</button></div><div class="msg" id="a-msg"></div></div>
   <div id="impbox" class="hide" style="margin-top:14px;border-top:1px solid var(--l);padding-top:12px">
    <div class="muted">Upload a .csv (up to {{ $d['maxMb'] }} MB, {{ number_format($d['maxRows']) }} rows). Any column order works: you'll match the columns next. Duplicates and invalid emails are skipped. <a href="/user/email-campaigns/contacts/sample.csv">Download a sample</a></div>
    <div style="display:flex;gap:8px;margin-top:10px;align-items:center"><input type="file" id="i-file" accept=".csv,text/csv"><button class="btn sm dark" onclick="EC.impPreview()">Upload</button></div>
    <div id="i-map" class="hide" style="margin-top:12px"><div class="muted" id="i-info"></div><div style="overflow:auto"><table id="i-tab"></table></div>
     <div class="row2"><div><label class="f">Add a tag to everyone in this file (optional)</label><input type="text" id="i-tag" placeholder="e.g. trade-show-2026"></div><div><label class="f">&nbsp;</label><label><input type="checkbox" id="i-upd"> Update names and tags of contacts already in my list</label></div></div>
     <label style="display:block;margin-top:10px"><input type="checkbox" id="i-consent"> These people gave me permission to email them (customers, leads who opted in). I won't upload purchased lists.</label>
     <div style="margin-top:12px"><button class="btn pri sm" onclick="EC.impCommit()">Import contacts</button></div></div>
    <div class="msg" id="i-msg"></div></div>
  </div>
  <div class="card" style="padding:8px 12px"><div id="bulk" class="flt hide" style="padding:8px 0;margin:0;align-items:center"><span class="muted" id="b-n"></span><input type="text" id="b-tag" placeholder="tag" style="width:140px"><button class="btn sm" onclick="EC.bulk('tag')">Add tag</button><button class="btn sm" onclick="EC.bulk('untag')">Remove tag</button><button class="btn sm" onclick="EC.bulk('unsubscribe')">Unsubscribe</button><button class="btn sm" onclick="EC.bulk('delete')">Delete</button></div>
   <table><thead><tr><th style="width:28px"><input type="checkbox" id="c-all"></th><th>Contact</th><th>Business</th><th>Tags</th><th>Status</th><th></th></tr></thead><tbody id="c-rows"></tbody></table>
   <div style="display:flex;justify-content:space-between;align-items:center;padding:10px 0"><span class="muted" id="c-tot"></span><span><button class="btn sm" id="c-prev">‹ Prev</button> <button class="btn sm" id="c-next">Next ›</button></span></div></div>
 </div>

 <div data-p="tpls" class="hide"><div class="card"><h4 style="margin-top:0">Starter templates</h4><div class="tpls" id="tp-start"></div>
  <h4>Your saved templates</h4><div class="tpls" id="tp-mine"></div><div class="muted" id="tp-none">Save any email as a template from the Compose step.</div></div></div>

@include('users.partials.ec-automations')
 <div data-p="smtp" class="hide"><div class="card" style="max-width:820px">
  <h4 style="margin-top:0">Sending email</h4>
  <div class="muted">Campaigns are sent from your own email account, so they come from you and replies land in your inbox. Your password is encrypted and is never shown again.</div>
  @if($d['smtp'] && $d['smtp']['verified_at'])<div class="note" style="margin-top:12px;background:#E7F6EC;border-color:#BFE6CB"><b>Connected:</b> {{ $d['smtp']['from_name'] }} &lt;{{ $d['smtp']['from_email'] }}&gt; via {{ $d['smtp']['host'] }}. @if($d['smtp']['last_error'])<br><span style="color:#B42318">{{ $d['smtp']['last_error'] }}</span>@endif <button class="btn sm" style="margin-left:8px" onclick="EC.smtpDel()">Disconnect</button></div>@endif
  <label class="f">Email provider</label><div class="tpls" id="sm-prov"></div>
  <div class="note hide" id="sm-tip" style="margin-top:12px"></div>
  <div class="row2"><div><label class="f">Send from (email address)</label><input type="email" id="sm-from"></div><div><label class="f">From name</label><input type="text" id="sm-name" maxlength="100"></div></div>
  <div class="row2"><div><label class="f">Username</label><input type="text" id="sm-user" autocomplete="off"></div><div><label class="f">Password / app password</label><input type="password" id="sm-pw" autocomplete="new-password" placeholder="@if($d['smtp'])Leave blank to keep the saved one @endif"></div></div>
  <div class="row2"><div><label class="f">SMTP server</label><input type="text" id="sm-host"></div><div class="row2"><div><label class="f">Port</label><select id="sm-port"><option>587</option><option>465</option><option>2525</option><option>25</option></select></div><div><label class="f">Security</label><select id="sm-enc"><option value="tls">STARTTLS</option><option value="ssl">SSL/TLS</option></select></div></div></div>
  <div style="margin-top:16px"><button class="btn pri" onclick="EC.smtpSave()">Save and send test</button> <span class="muted">We'll log in and send a test email to your from address. It's only saved if the test works.</span></div>
  <div class="msg" id="sm-msg"></div>
  <div class="muted" style="margin-top:14px">Sending limits follow your provider (Gmail about 500 a day, Outlook about 300). We send in batches of 50, a few seconds apart, and pause automatically if too many fail. One-click Google and Microsoft sign-in is coming soon.</div>
 </div></div>
 </div>

 <div id="wiz" class="hide">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px"><h3 style="margin:0" id="w-h">New campaign</h3><button class="btn sm" onclick="EC.close()">← Back to campaigns</button></div>
  <div class="steps"><div class="step" data-s="1"><b>1. Audience</b>Who gets it</div><div class="step" data-s="2"><b>2. Compose</b>Subject and message</div><div class="step" data-s="3"><b>3. Preview &amp; test</b>Check it in your inbox</div><div class="step" data-s="4"><b>4. Send</b>Now or later</div></div>
  <div class="card" data-w="1"><label class="f" style="margin-top:0">Campaign name (only you see this)</label><input type="text" id="w-name" maxlength="150" placeholder="e.g. December newsletter">
   <label class="f">Send to</label><div class="opt" data-a="all"><input type="radio" name="aud" value="all"><div><b>All active contacts</b><div class="muted">Everyone in your list who hasn't unsubscribed</div></div></div>
   <div class="opt" data-a="tags"><input type="radio" name="aud" value="tags"><div style="flex:1"><b>Contacts with these tags</b><div class="muted">Pick one or more segments</div><div id="w-tags" style="margin-top:8px"></div></div></div>
   <div class="muted" id="w-count" style="margin-top:6px"></div>
   <div style="margin-top:16px;text-align:right"><button class="btn pri" onclick="EC.go(2)">Next: Compose →</button></div></div>
  <div class="card hide" data-w="2"><div class="row2"><div><label class="f" style="margin-top:0">Subject line</label><input type="text" id="w-subj" maxlength="200" placeholder="e.g. A special offer for you, {{ '{' }}{first_name}}"></div><div><label class="f" style="margin-top:0">Preview text (shown after the subject in the inbox)</label><input type="text" id="w-pre" maxlength="200"></div></div>
   <label class="f">Start from a template</label><div class="tpls" id="w-tpls"></div>
   <label class="f">Message</label>
   <div class="tb"><button onclick="EC.cmd('bold')"><b>B</b></button><button onclick="EC.cmd('italic')"><i>I</i></button><button onclick="EC.cmd('underline')"><u>U</u></button><button onclick="EC.cmd('formatBlock','h2')">Heading</button><button onclick="EC.cmd('formatBlock','p')">Text</button><button onclick="EC.cmd('insertUnorderedList')">• List</button><button onclick="EC.link()">Link</button><button onclick="EC.button()">Button</button><button onclick="EC.img()">Image</button><button onclick="EC.cmd('justifyCenter')">Center</button><button onclick="EC.cmd('removeFormat')">Clear</button>
    <span style="flex:1"></span>@foreach(['first_name','last_name','business_name','my_business'] as $m)<button class="mt" onclick="EC.merge('{{ $m }}')">{{ '{' }}{{ '{' }}{{ $m }}}}</button>@endforeach</div>
   <div class="ed" id="w-ed" contenteditable="true"></div>
   <div class="muted" style="margin-top:6px">Merge tags fill in each contact's details: <span class="mt">{{ '{' }}{first_name}}</span> (or "there"), <span class="mt">{{ '{' }}{business_name}}</span> (their business, or "your business"), <span class="mt">{{ '{' }}{my_business}}</span> ({{ $d['sender']['business'] }}). An unsubscribe link and your business details are added to the bottom automatically.</div>
   <label style="display:block;margin-top:10px"><input type="checkbox" id="w-savetpl"> Also save this as a template named <input type="text" id="w-tplname" style="width:220px;display:inline-block;padding:4px 8px" maxlength="150"></label>
   <div style="margin-top:16px;display:flex;justify-content:space-between"><button class="btn" onclick="EC.go(1)">← Back</button><span><button class="btn" onclick="EC.save()">Save draft</button> <button class="btn pri" onclick="EC.go(3)">Next: Preview →</button></span></div><div class="msg" id="w-msg2"></div></div>
  <div class="card hide" data-w="3"><div class="row2"><div><div class="muted">From</div><b id="p-from"></b><div class="muted" style="margin-top:6px">Replies go to</div><b id="p-reply"></b><div class="muted" style="margin-top:6px">Subject (sample contact Maria from Sample Bakery)</div><b id="p-subj"></b>
    <div style="margin-top:18px"><button class="btn dark" onclick="EC.test()">Send me a test email</button><div class="muted" style="margin-top:6px">Sent from your connected email to your own account email ({{ $d['sender']['email'] }}).</div><div class="msg" id="w-msg3"></div></div></div>
   <div><iframe class="pv" id="p-frame" sandbox=""></iframe></div></div>
   <div style="margin-top:16px;display:flex;justify-content:space-between"><button class="btn" onclick="EC.go(2)">← Back</button><button class="btn pri" onclick="EC.go(4)">Next: Send →</button></div></div>
  <div class="card hide" data-w="4"><div class="stats"><div class="stat"><b id="s-n">–</b>recipients</div><div class="stat"><b>{{ number_format($d['limit']) }}</b>max per day</div><div class="stat"><b id="s-days">–</b>days to finish</div></div>
   <div class="opt" data-when="now"><input type="radio" name="when" value="now"><div><b>Send now</b><div class="muted">Starts within 5 minutes, in batches of 50.</div></div></div>
   <div class="opt" data-when="later"><input type="radio" name="when" value="later"><div style="flex:1"><b>Schedule for later</b><div class="muted">Eastern time</div><input type="datetime-local" id="s-at" style="margin-top:8px;max-width:260px"></div></div>
   <label style="display:block;margin-top:12px"><input type="checkbox" id="s-ok"> I reviewed the test email, and everyone on this list agreed to hear from me.</label>
   <div style="margin-top:16px;display:flex;justify-content:space-between"><button class="btn" onclick="EC.go(3)">← Back</button><button class="btn pri" id="s-go" onclick="EC.send()">Send campaign</button></div><div class="msg" id="w-msg4"></div></div>
 </div>
</div>
<script>
(function(){
 var D=@json($d,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT), TOK=@json(csrf_token()), B='/user/email-campaigns';
 var EC_SM, $=function(i){return document.getElementById(i);}, cur=null, step=1, cpage=1, sel={}, imp=null;
 function el(t,a,k){var e=document.createElement(t);if(a)for(var x in a){if(x==='text')e.textContent=a[x];else if(x==='on')e.onclick=a[x];else e.setAttribute(x,a[x]);}(k||[]).forEach(function(c){if(c)e.appendChild(typeof c==='string'?document.createTextNode(c):c);});return e;}
 function post(u,b,form){var o={method:'POST',credentials:'same-origin',headers:{'X-CSRF-TOKEN':TOK,'Accept':'application/json'}};if(form){o.body=form;}else{o.headers['Content-Type']='application/json';o.body=JSON.stringify(b||{});}
  return fetch(B+u,o).then(function(r){return r.json().catch(function(){return {error:'Something went wrong ('+r.status+'). Please try again.'};});});}
 function get(u){return fetch(B+u,{credentials:'same-origin',headers:{'Accept':'application/json'}}).then(function(r){return r.json();});}
 function msg(id,t,ok){var m=$(id);m.textContent=t||'';m.className='msg '+(ok?'ok':'err');}
 function et(s){if(!s)return '—';var d=new Date(s.replace(' ','T')+'Z');return d.toLocaleString('en-US',{timeZone:'America/New_York',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});}
 var LBL={DRAFT:'Draft',SCHEDULED:'Scheduled',SENDING:'Sending',PAUSED:'Paused',DONE:'Sent'};
 function audTxt(a){try{a=JSON.parse(a||'{}');}catch(e){a={};}return a.tags&&a.tags.length?'Tags: '+a.tags.join(', '):'All active contacts';}
 // tabs
 document.querySelectorAll('.ec .tabs a').forEach(function(a){a.onclick=function(){document.querySelectorAll('.ec .tabs a').forEach(function(x){x.classList.toggle('on',x===a);});document.querySelectorAll('[data-p]').forEach(function(p){p.classList.toggle('hide',p.getAttribute('data-p')!==a.getAttribute('data-t'));});if(a.getAttribute('data-t')==='contacts')loadC();};});
 // campaigns list
 function camps(){var tb=$('camp-rows');tb.innerHTML='';$('camp-empty').classList.toggle('hide',D.campaigns.length>0);
  D.campaigns.forEach(function(c){var s=c.stats,done=s.sent+s.failed+s.skipped,pct=s.total?Math.round(done*100/s.total):0,acts=[];
   var a=function(t,f,cls){acts.push(el('button',{'class':'btn sm '+(cls||''),on:f,text:t}));acts.push(' ');};
   if(c.status==='DRAFT'||c.status==='SCHEDULED')a('Edit',function(){EC.edit(c);});
   if(c.status==='SCHEDULED'||c.status==='SENDING')a('Pause',function(){act(c,'pause');});
   if(c.status==='PAUSED')a('Resume',function(){act(c,'resume');},'pri');
   if(c.started_at)a('Report',function(){report(c);});
   a('Duplicate',function(){act(c,'duplicate');});
   if(c.status==='SCHEDULED'||c.status==='SENDING'||c.status==='PAUSED')a('Cancel',function(){if(confirm('Cancel this campaign? Emails not yet sent will not go out.'))act(c,'cancel');});
   if(c.status!=='SENDING')a('Delete',function(){if(confirm('Delete this campaign and its report?'))act(c,'delete');});
   tb.appendChild(el('tr',{},[el('td',{},[el('b',{text:c.name}),el('div',{'class':'muted',text:c.subject||'No subject yet'})]),
    el('td',{},[el('span',{'class':'pill '+c.status,text:LBL[c.status]||c.status}),c.pause_reason?el('div',{'class':'muted',text:c.pause_reason}):null]),
    el('td',{'class':'muted',text:audTxt(c.audience)}),el('td',{'class':'muted',text:et(c.scheduled_at)}),
    el('td',{},s.total?[el('div',{'class':'bar'},[el('i',{style:'width:'+pct+'%'})]),el('div',{'class':'muted',text:s.sent+' sent'+(s.failed?' · '+s.failed+' failed':'')+(s.skipped?' · '+s.skipped+' skipped':'')+' of '+s.total})]:[el('span',{'class':'muted',text:'—'})]),
    el('td',{'class':'muted',text:s.sent?Math.round(s.opened*100/s.sent)+'%':'—'}),el('td',{style:'text-align:right;min-width:210px;line-height:2.2'},acts)]));});}
 function act(c,a){post('/campaign/action',{id:c.id,action:a}).then(function(j){if(j.error){alert(j.error);return;}location.reload();});}
 function report(c){$('report').classList.remove('hide');$('rep-h').textContent='Report: '+c.name;get('/campaign/'+c.id+'/report').then(function(j){var s=j.stats;$('rep-stats').innerHTML='';
  [['Recipients',s.total],['Sent',s.sent],['Opened',s.sent?Math.round(s.opened*100/s.sent)+'%':'—'],['Clicked',s.sent?Math.round((s.unique_clicks||0)*100/s.sent)+'%':'—'],['Total clicks',s.clicks||0],['Failed',s.failed],['Skipped',s.skipped],['Waiting',s.pending+s.sending]].forEach(function(x){$('rep-stats').appendChild(el('div',{'class':'stat'},[el('b',{text:String(x[1])}),x[0]]));});
  var tb=$('rep-rows');tb.innerHTML='';(j.rows||[]).forEach(function(r){tb.appendChild(el('tr',{},[el('td',{text:r.email}),el('td',{},[el('span',{'class':'pill',text:r.status})]),el('td',{'class':'muted',text:et(r.sent_at)}),el('td',{'class':'muted',text:r.opened_at?et(r.opened_at):''}),el('td',{'class':'muted',text:r.open_count?String(r.open_count):''}),el('td',{'class':'muted',text:r.clicks?String(r.clicks):''}),el('td',{'class':'muted',text:r.reason&&r.reason.length<80?r.reason:(r.reason?'Delivery error':'')})]));});
  $('report').scrollIntoView({behavior:'smooth'});});}
 // contacts
 function loadC(p){cpage=p||cpage;get('/contacts?page='+cpage+'&q='+encodeURIComponent($('c-q').value)+'&tag='+encodeURIComponent($('c-tag').value)+'&status='+$('c-st').value).then(function(j){var tb=$('c-rows');tb.innerHTML='';sel={};bulkUi();
  if(!j.rows.length)tb.appendChild(el('tr',{},[el('td',{colspan:6,'class':'muted',style:'text-align:center;padding:26px',text:'No contacts yet. Add one, or import a CSV from your spreadsheet or CRM.'})]));
  j.rows.forEach(function(c){var cb=el('input',{type:'checkbox'});cb.onchange=function(){if(cb.checked)sel[c.id]=1;else delete sel[c.id];bulkUi();};
   tb.appendChild(el('tr',{},[el('td',{},[cb]),el('td',{},[el('b',{text:[c.first_name,c.last_name].filter(Boolean).join(' ')||'—'}),el('div',{'class':'muted',text:c.email})]),el('td',{text:c.business_name||''}),
    el('td',{},(c.tags||'').split(',').filter(Boolean).map(function(t){return el('span',{'class':'tag',text:t});})),el('td',{},[el('span',{'class':'pill '+c.status,text:c.status})]),el('td',{style:'text-align:right'},[el('button',{'class':'btn sm',text:'Edit',on:function(){EC.showAdd(c);}})])]));});
  $('c-tot').textContent=j.total+' contact'+(j.total===1?'':'s')+' · page '+j.page+' of '+j.pages;$('c-prev').disabled=j.page<=1;$('c-next').disabled=j.page>=j.pages;});}
 function bulkUi(){var n=Object.keys(sel).length;$('bulk').classList.toggle('hide',!n);$('b-n').textContent=n+' selected';}
 $('c-all').onchange=function(){document.querySelectorAll('#c-rows input[type=checkbox]').forEach(function(x){x.checked=$('c-all').checked;x.onchange();});};
 var tmr;$('c-q').oninput=function(){clearTimeout(tmr);tmr=setTimeout(function(){loadC(1);},300);};$('c-tag').onchange=$('c-st').onchange=function(){loadC(1);};
 $('c-prev').onclick=function(){loadC(cpage-1);};$('c-next').onclick=function(){loadC(cpage+1);};
 // templates
 function tplBtn(t,f){return el('button',{'class':'tpl',on:f},[el('b',{text:t.name}),el('div',{'class':'muted',text:t.subject||'Saved template'})]);}
 function tpls(){var s=$('tp-start'),m=$('tp-mine'),w=$('w-tpls');s.innerHTML=m.innerHTML=w.innerHTML='';
  D.starters.forEach(function(t){s.appendChild(tplBtn(t,function(){EC.newCamp(t);}));w.appendChild(tplBtn(t,function(){useTpl(t);}));});
  w.appendChild(el('button',{'class':'tpl',on:function(){if(confirm('Start with a blank email?'))$('w-ed').innerHTML='<p>Hi {'+'{first_name}},</p><p></p>';}},[el('b',{text:'Blank'}),el('div',{'class':'muted',text:'Start from scratch'})]));
  $('tp-none').classList.toggle('hide',D.templates.length>0);
  D.templates.forEach(function(t){var x={name:t.name,html:t.body_html};w.appendChild(tplBtn(x,function(){useTpl(x);}));
   m.appendChild(el('div',{'class':'tpl'},[el('b',{text:t.name}),el('div',{},[el('button',{'class':'btn sm',text:'Use',on:function(){EC.newCamp(x);}}),' ',el('button',{'class':'btn sm',text:'Delete',on:function(){if(confirm('Delete template?'))post('/templates/delete',{id:t.id}).then(function(){location.reload();});}})])]));});}
 function useTpl(t){if($('w-ed').textContent.trim()&&!confirm('Replace your current message with this template?'))return;$('w-ed').innerHTML=t.html;if(t.subject&&!$('w-subj').value)$('w-subj').value=t.subject;}
 // wizard
 function tagsUi(sel){var w=$('w-tags');w.innerHTML='';var ks=Object.keys(D.tags);if(!ks.length)w.appendChild(el('span',{'class':'muted',text:'No tags yet. Add tags to contacts, or when importing a CSV.'}));
  ks.forEach(function(t){var cb=el('input',{type:'checkbox',value:t});if(sel.indexOf(t)>=0)cb.checked=true;cb.onchange=function(){pick('tags');};w.appendChild(el('label',{style:'margin-right:12px;font-weight:500'},[cb,' '+t+' ('+D.tags[t]+')']));});}
 function aud(){var t=document.querySelector('input[name=aud]:checked');return t&&t.value==='tags'?Array.prototype.map.call(document.querySelectorAll('#w-tags input:checked'),function(x){return x.value;}):[];}
 function pick(a){document.querySelectorAll('[data-a]').forEach(function(o){var on=o.getAttribute('data-a')===a;o.classList.toggle('on',on);o.querySelector('input').checked=on;});count();}
 document.querySelectorAll('[data-a]').forEach(function(o){o.onclick=function(e){if(e.target.tagName!=='INPUT'||e.target.type==='radio')pick(o.getAttribute('data-a'));};});
 document.querySelectorAll('[data-when]').forEach(function(o){o.onclick=function(){document.querySelectorAll('[data-when]').forEach(function(x){x.classList.toggle('on',x===o);x.querySelector('input').checked=x===o;});};});
 function count(){post('/audience',{tags:aud()}).then(function(j){$('w-count').textContent=(j.count||0)+' contact'+(j.count===1?'':'s')+' will get this email.';$('s-n').textContent=j.count||0;$('s-days').textContent=Math.max(1,Math.ceil((j.count||0)/D.limit));});}
 function body(){return {id:cur&&cur.id,name:$('w-name').value,subject:$('w-subj').value,preview:$('w-pre').value,body_html:$('w-ed').innerHTML,tags:aud(),template_name:$('w-savetpl').checked?($('w-tplname').value||$('w-name').value):''};}
 function save(){if(D.demo)return Promise.resolve({ok:true,demo:true});return post('/campaign/save',body()).then(function(j){if(j.ok){cur=cur||{};cur.id=j.id;$('w-savetpl').checked=false;}return j;});}
 function show(n){step=n;document.querySelectorAll('[data-w]').forEach(function(c){c.classList.toggle('hide',+c.getAttribute('data-w')!==n);});document.querySelectorAll('.step').forEach(function(s){var k=+s.getAttribute('data-s');s.classList.toggle('on',k===n);s.classList.toggle('done',k<n);});window.scrollTo(0,$('wiz').offsetTop-20);}
 function preview(){post('/preview',{subject:$('w-subj').value,preview:$('w-pre').value,body_html:$('w-ed').innerHTML}).then(function(j){$('p-from').textContent=j.from;$('p-reply').textContent=j.reply_to;$('p-subj').textContent=j.subject;$('p-frame').srcdoc=j.html;});}
 document.querySelectorAll('.step').forEach(function(s){s.onclick=function(){EC.go(+s.getAttribute('data-s'));};});

 var TIPS={gmail:'Gmail / Google Workspace: turn on 2-Step Verification, then create an <b>app password</b> at myaccount.google.com/apppasswords and paste it as the password. Username is your full Gmail address.',
  outlook:'Outlook / Microsoft 365: use your full email as the username. If you use 2-step verification, create an app password. Your admin may need to allow "Authenticated SMTP" for your mailbox.',
  yahoo:'Yahoo: go to Account security and choose Generate app password. Use that as the password.',
  zoho:'Zoho: use your full email address. If 2-factor is on, create an app-specific password in Zoho Accounts.',
  other:'Enter the SMTP details from your email host (often under "Email settings" or "Mail client setup").'};
 var SM=D.smtp||{};
 function prov(k){var p=D.presets[k];document.querySelectorAll('#sm-prov .tpl').forEach(function(b){b.style.borderColor=b.getAttribute('data-k')===k?'#FF5000':'';});SM.provider=k;
  if(k!=='other'){$('sm-host').value=p[1];$('sm-port').value=String(p[2]);$('sm-enc').value=p[3];}$('sm-tip').innerHTML=TIPS[k];$('sm-tip').classList.remove('hide');}
 Object.keys(D.presets).forEach(function(k){var b=el('button',{'class':'tpl','data-k':k,on:function(){prov(k);}},[el('b',{text:D.presets[k][0]})]);$('sm-prov').appendChild(b);});
 $('sm-from').value=SM.from_email||D.sender.email||'';$('sm-name').value=SM.from_name||D.sender.business||'';$('sm-user').value=SM.username||'';$('sm-host').value=SM.host||'';if(SM.port)$('sm-port').value=String(SM.port);if(SM.encryption)$('sm-enc').value=SM.encryption;
 prov(SM.provider||'gmail');if(SM.host)$('sm-host').value=SM.host;
 $('sm-from').addEventListener('change',function(){if(!$('sm-user').value)$('sm-user').value=$('sm-from').value;});
 $('sm-port').onchange=function(){$('sm-enc').value=$('sm-port').value==='465'?'ssl':'tls';};
 EC_SM={save:function(){if(D.demo){msg('sm-msg','Sample account: nothing is saved.');return;}msg('sm-msg','Connecting and sending a test… this can take up to 20 seconds.',true);
   post('/smtp/save',{provider:SM.provider,host:$('sm-host').value,port:$('sm-port').value,encryption:$('sm-enc').value,username:$('sm-user').value,password:$('sm-pw').value,from_email:$('sm-from').value,from_name:$('sm-name').value}).then(function(j){$('sm-pw').value='';if(j.error){msg('sm-msg',j.error);return;}msg('sm-msg','Connected. A test email was sent to '+j.from+'.',true);setTimeout(function(){location.reload();},1800);});},
  del:function(){if(D.demo)return;if(confirm('Disconnect your email? Scheduled and sending campaigns will pause.'))post('/smtp/delete',{}).then(function(){location.reload();});}};
 window.EC={$:$,smtpSave:function(){EC_SM.save();},smtpDel:function(){EC_SM.del();},
  newCamp:function(t){cur=null;$('w-h').textContent='New campaign';$('w-name').value=t?t.name:'';$('w-subj').value=t?(t.subject||''):D.starters[0].subject;$('w-pre').value='';$('w-ed').innerHTML=t?t.html:(D.starters[0].html);tagsUi([]);pick('all');
   document.querySelectorAll('[data-when]')[0].click();$('s-ok').checked=false;['w-msg2','w-msg3','w-msg4'].forEach(function(i){msg(i,'');});$('home').classList.add('hide');$('wiz').classList.remove('hide');show(1);},
  edit:function(c){EC.newCamp();cur=c;$('w-h').textContent='Edit: '+c.name;$('w-name').value=c.name;$('w-subj').value=c.subject||'';$('w-pre').value=c.preview||'';$('w-ed').innerHTML=c.body_html||'';var a={};try{a=JSON.parse(c.audience||'{}');}catch(e){}
   tagsUi(a.tags||[]);pick(a.tags&&a.tags.length?'tags':'all');},
  close:function(){if(!D.demo&&cur&&cur.id){location.reload();return;}$('wiz').classList.add('hide');$('home').classList.remove('hide');},
  go:function(n){if(n>1&&!$('w-name').value.trim()){$('w-name').value='Campaign '+new Date().toLocaleDateString();}
   if(n>2){if(!$('w-subj').value.trim()){show(2);msg('w-msg2','Add a subject line.');return;}if(!$('w-ed').textContent.trim()){show(2);msg('w-msg2','Write your message.');return;}}
   if(n>=2&&!D.demo){save().then(function(j){if(j.error){msg('w-msg2',j.error);return;}show(n);if(n===3)preview();if(n===4)count();});}else{show(n);if(n===3)preview();if(n===4)count();}},
  save:function(){if(D.demo){msg('w-msg2','Sample account: nothing is saved.');return;}save().then(function(j){msg('w-msg2',j.error||'Draft saved.',!j.error);});},
  test:function(){if(D.demo){msg('w-msg3','Sample account: test emails are not sent.');return;}msg('w-msg3','Sending…',true);post('/campaign/test',{id:cur&&cur.id}).then(function(j){if(j.connect){document.querySelector('[data-t=smtp]');}msg('w-msg3',j.error||('Test sent to '+j.to+'. Check your inbox (and spam folder).'),!j.error);});},
  send:function(){if(!$('s-ok').checked){msg('w-msg4','Please tick the box to confirm.');return;}var later=document.querySelector('input[name=when]:checked').value==='later';if(later&&!$('s-at').value){msg('w-msg4','Pick a date and time.');return;}
   if(D.demo){msg('w-msg4','Sample account: nothing was sent.');return;}$('s-go').disabled=true;
   save().then(function(){return post('/campaign/schedule',{id:cur.id,confirm:'SEND',at:later?$('s-at').value:''});}).then(function(j){$('s-go').disabled=false;if(j.error){msg('w-msg4',j.error);return;}
    msg('w-msg4',(later?'Scheduled. ':'Sending has started. ')+j.recipients+' recipient'+(j.recipients===1?'':'s')+(j.days>1?', spread over about '+j.days+' days at '+j.limit+' per day':'')+'.',true);setTimeout(function(){location.reload();},2200);});},
  cmd:function(c,v){$('w-ed').focus();document.execCommand(c,false,v||null);},
  link:function(){var u=prompt('Link address (https://…)','https://');if(u&&/^(https?:\/\/|mailto:)/i.test(u))EC.cmd('createLink',u);},
  img:function(){var u=prompt('Image address (must start with https://)','https://');if(u&&/^https:\/\//i.test(u))EC.cmd('insertHTML','<img src="'+u.replace(/"/g,'')+'" alt="" style="max-width:100%">');},
  button:function(){var t=prompt('Button text','Learn more');if(!t)return;var u=prompt('Button link (https://…)','https://');if(!u||!/^https?:\/\//i.test(u))return;var a=document.createElement('a');a.href=u;a.textContent=t;a.setAttribute('style','display:inline-block;background:#1E2A36;color:#ffffff;text-decoration:none;font-weight:bold;padding:12px 22px;border-radius:8px');EC.cmd('insertHTML','<p>'+a.outerHTML+'</p>');},
  merge:function(m){var a=document.activeElement;var tag='{'+'{'+m+'}}';if(a&&(a.id==='w-subj'||a.id==='w-pre')){var p=a.selectionStart||a.value.length;a.value=a.value.slice(0,p)+tag+a.value.slice(p);}else EC.cmd('insertText',tag);},
  showAdd:function(c){$('impbox').classList.add('hide');$('addbox').classList.remove('hide');c=c||{};$('a-id').value=c.id||'';$('a-email').value=c.email||'';$('a-first').value=c.first_name||'';$('a-last').value=c.last_name||'';$('a-biz').value=c.business_name||'';$('a-tags').value=c.tags||'';msg('a-msg','');$('a-email').focus();},
  saveContact:function(){post('/contacts/save',{id:$('a-id').value,email:$('a-email').value,first_name:$('a-first').value,last_name:$('a-last').value,business_name:$('a-biz').value,tags:$('a-tags').value}).then(function(j){if(j.error){msg('a-msg',j.error);return;}msg('a-msg','Saved.',true);loadC();$('a-id').value='';});},
  showImport:function(){$('addbox').classList.add('hide');$('impbox').classList.remove('hide');$('i-map').classList.add('hide');msg('i-msg','');},
  impPreview:function(){var f=$('i-file').files[0];if(!f){msg('i-msg','Choose a CSV file first.');return;}if(f.size>D.maxMb*1048576){msg('i-msg','That file is larger than '+D.maxMb+' MB.');return;}var fd=new FormData();fd.append('file',f);msg('i-msg','Reading file…',true);
   post('/contacts/import/preview',null,fd).then(function(j){if(j.error){msg('i-msg',j.error);return;}msg('i-msg','');imp=j;var t=$('i-tab');t.innerHTML='';var hr=el('tr'),mr=el('tr');
    j.headers.forEach(function(h,i){hr.appendChild(el('th',{text:h}));var s=el('select',{'data-i':i});[['','Skip this column'],['email','Email'],['first_name','First name'],['last_name','Last name'],['business_name','Business name'],['tags','Tags']].forEach(function(o){var op=el('option',{value:o[0],text:o[1]});if(j.guess[i]===o[0])op.selected=true;s.appendChild(op);});mr.appendChild(el('td',{},[s]));});
    t.appendChild(hr);t.appendChild(mr);j.sample.forEach(function(r){t.appendChild(el('tr',{},j.headers.map(function(_,i){return el('td',{'class':'muted',text:r[i]||''});})));});
    $('i-info').textContent=j.rows+' rows found'+(j.header?' (first row used as column names)':'')+(j.truncated?'. Only the first '+D.maxRows+' rows will be imported.':'.')+' Match each column below:';$('i-map').classList.remove('hide');});},
  impCommit:function(){var map={};document.querySelectorAll('#i-tab select').forEach(function(s){map[s.getAttribute('data-i')]=s.value;});
   post('/contacts/import',{token:imp.token,map:map,tag:$('i-tag').value,update:$('i-upd').checked,consent:$('i-consent').checked}).then(function(j){if(j.error){msg('i-msg',j.error);return;}
    var t='Imported '+j.added+' new contact'+(j.added===1?'':'s')+(j.updated?', updated '+j.updated:'')+'. Skipped: '+j.duplicates+' duplicate, '+j.invalid+' invalid email'+(j.over_limit?', '+j.over_limit+' over your contact limit':'')+(j.suppressed?'. '+j.suppressed+' were previously unsubscribed and stay unsubscribed':'')+'.';
    if(j.invalid_rows&&j.invalid_rows.length)t+=' Invalid examples: '+j.invalid_rows.map(function(x){return 'row '+x.row+' "'+x.value+'"';}).join(', ');msg('i-msg',t,true);$('i-map').classList.add('hide');loadC(1);});},
  bulk:function(a){var ids=Object.keys(sel);if(a==='delete'&&!confirm('Delete '+ids.length+' contact(s)?'))return;post('/contacts/bulk',{ids:ids,action:a,tag:$('b-tag').value}).then(function(j){if(j.error){alert(j.error);return;}loadC();});}
 };
 camps();tpls();
 var tm=/[?&]tab=(\w+)/.exec(location.search);if(tm&&document.querySelector('[data-t='+tm[1]+']'))document.querySelector('[data-t='+tm[1]+']').click();
})();
</script>
@endsection
