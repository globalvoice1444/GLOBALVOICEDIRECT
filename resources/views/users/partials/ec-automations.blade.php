@php $A = \App\Http\Controllers\EcAutomationController::data((int) session()->get('user_id'), (bool) $d['demo']); @endphp
<div data-p="seqs" class="hide">
 <div class="card"><div style="display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap"><div><h4 style="margin:0">Sequences</h4><div class="muted">A series of emails sent automatically, a set number of days after a contact joins. Sent from your connected email. New sequences start <b>paused</b>.</div></div>
  <button class="btn sm" onclick="ECA.newSeq('')">+ Blank sequence</button></div>
  <label class="f">Start from a ready-made sequence</label><div class="tpls" id="sq-start"></div></div>
 <div id="sq-list"></div>
 <div class="card hide" id="sq-rep" style="border-top:4px solid #F97316"><div style="display:flex;justify-content:space-between;align-items:center"><h4 style="margin:0;color:#1E2A36" id="sr-h">Report</h4><button class="btn sm" onclick="ECA.$('sq-rep').classList.add('hide')">Close</button></div><div id="sr-body"></div></div>
 <div class="card hide" id="sq-ed"><div style="display:flex;justify-content:space-between;align-items:center"><h4 style="margin:0" id="se-h">Edit email</h4><button class="btn sm" onclick="ECA.$('sq-ed').classList.add('hide')">Close</button></div>
  <input type="hidden" id="se-id"><input type="hidden" id="se-seq">
  <div class="row2"><div><label class="f">Send on day</label><input type="text" id="se-day" style="max-width:120px"> <span class="muted">0 = the day they join</span></div><div><label class="f">Subject</label><input type="text" id="se-subj" maxlength="200"></div></div>
  <label class="f">Message</label>
  <div class="tb"><button onclick="ECA.cmd('bold')"><b>B</b></button><button onclick="ECA.cmd('italic')"><i>I</i></button><button onclick="ECA.cmd('formatBlock','h2')">Heading</button><button onclick="ECA.cmd('formatBlock','p')">Text</button><button onclick="ECA.cmd('insertUnorderedList')">• List</button><button onclick="ECA.link()">Link</button>
   <span style="flex:1"></span>@foreach(['first_name','business_name','my_business'] as $m)<button class="mt" onclick="ECA.cmd('insertText','{{ '{' }}{{ '{' }}{{ $m }}}}')">{{ '{' }}{{ '{' }}{{ $m }}}}</button>@endforeach</div>
  <div class="ed" id="se-body" contenteditable="true" style="min-height:240px"></div>
  <div class="muted" style="margin-top:6px">Tip: click a button in the email, then use Link to set where it goes. Placeholder text must be replaced before the sequence can be turned on.</div>
  <div style="margin-top:12px;display:flex;gap:8px"><button class="btn pri" onclick="ECA.saveStep()">Save email</button></div><div class="msg" id="se-msg"></div></div>
</div>

<div data-p="autos" class="hide">
 <div class="card"><h4 style="margin-top:0">Automations</h4><div class="muted">Simple "when this happens, do that" rules for your contacts. New automations start <b>paused</b>; turn them on when ready.</div>
  <div class="row2" style="margin-top:6px"><div><label class="f">When</label><select id="au-trig"></select><input type="text" id="au-ttag" class="hide" placeholder="tag, e.g. quote-sent" style="margin-top:8px"></div>
   <div><label class="f">Then</label><select id="au-act"></select><select id="au-seq" style="margin-top:8px"></select><input type="text" id="au-tag" class="hide" placeholder="tag" style="margin-top:8px"></div></div>
  <label class="f">Name (optional)</label><input type="text" id="au-name" maxlength="150" placeholder="e.g. New leads get the welcome series">
  <div style="margin-top:12px"><button class="btn pri" onclick="ECA.saveAuto()">Create automation (paused)</button></div><div class="msg" id="au-msg"></div>
  <div class="muted" style="margin-top:10px"><b>Ideas:</b> When a contact is added → Start "Welcome series". When tag "quote-sent" is added → Start "Quote follow-up". When tag "customer" is added → Stop "Quote follow-up".</div></div>
 <div class="card" style="padding:8px 12px;overflow-x:auto"><table><thead><tr><th>Automation</th><th>Rule</th><th>Status</th><th>Runs</th><th></th></tr></thead><tbody id="au-rows"></tbody></table></div>
</div>
<script>
(function(){
 var A=@json($A,JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT), DEMO=@json((bool)$d['demo']), TOK=@json(csrf_token()), B='/user/email-campaigns/';
 var $=function(i){return document.getElementById(i);};
 function el(t,a,k){var e=document.createElement(t);if(a)for(var x in a){if(x==='text')e.textContent=a[x];else if(x==='on')e.onclick=a[x];else e.setAttribute(x,a[x]);}(k||[]).forEach(function(c){if(c!=null)e.appendChild(typeof c==='string'?document.createTextNode(c):c);});return e;}
 function post(u,b){if(DEMO)return Promise.resolve({error:'Sample account: nothing is saved or sent.'});return fetch(B+u,{method:'POST',credentials:'same-origin',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':TOK,'Accept':'application/json'},body:JSON.stringify(b)}).then(function(r){return r.json().catch(function(){return {error:'Something went wrong ('+r.status+').'};});});}
 function done(j,box){if(j.error){if(box){$(box).textContent=j.error;$(box).className='msg err';}else alert(j.error);if(j.connect)document.querySelector('[data-t=smtp]').click();return false;}location.reload();return true;}
 var LBL={ACTIVE:'On',PAUSED:'Paused'}, seqName={};A.sequences.forEach(function(s){seqName[s.id]=s.name;});
 Object.keys(A.starters).forEach(function(k){var s=A.starters[k];$('sq-start').appendChild(el('button',{'class':'tpl',on:function(){ECA.newSeq(k);}},[el('b',{text:s.name}),el('div',{'class':'muted',text:s.steps+' emails · '+s.desc})]));});
 function seqs(){var L=$('sq-list');L.innerHTML='';if(!A.sequences.length)L.appendChild(el('div',{'class':'card muted',style:'text-align:center',text:'No sequences yet. Pick a ready-made one above to get started.'}));
  A.sequences.forEach(function(s){var steps=el('table',{},[el('thead',{},[el('tr',{},[el('th',{text:'Day'}),el('th',{text:'Email'}),el('th',{})])])]),tb=el('tbody');steps.appendChild(tb);
   s.steps.forEach(function(st){tb.appendChild(el('tr',{},[el('td',{text:'Day '+st.day_offset}),el('td',{},[el('b',{text:st.subject})]),el('td',{style:'text-align:right'},[el('button',{'class':'btn sm',text:'Edit',on:function(){ECA.editStep(s,st);}}),' ',el('button',{'class':'btn sm',text:'Remove',on:function(){if(confirm('Remove this email?'))post('step/delete',{sequence_id:s.id,id:st.id}).then(function(j){done(j);});}})])]));});
   var on=s.status==='ACTIVE';
   $('sq-list').appendChild(el('div',{'class':'card'},[el('div',{style:'display:flex;justify-content:space-between;align-items:center;gap:10px;flex-wrap:wrap'},[
     el('div',{},[el('b',{style:'font-size:16px',text:s.name}),' ',el('span',{'class':'pill '+(on?'DONE':'PAUSED'),text:LBL[s.status]}),el('div',{'class':'muted',text:s.steps.length+' emails · '+s.enrolled+' enrolled ('+s.active+' in progress) · '+s.sent+' sent'+(s.failed?' · '+s.failed+' failed':'')})]),
     el('div',{},[el('button',{'class':'btn sm '+(on?'':'pri'),text:on?'Pause':'Turn on',on:function(){post('seq/update',{id:s.id,action:on?'pause':'activate'}).then(function(j){done(j);});}}),' ',
      el('button',{'class':'btn sm',style:'background:#1E2A36;color:#fff;border-color:#1E2A36',text:'\u{1F4CA} Report',on:function(){seqReport(s);}}),' ',el('button',{'class':'btn sm',text:'+ Add email',on:function(){ECA.editStep(s,null);}}),' ',
      el('button',{'class':'btn sm',text:'Enroll contacts',on:function(){var t=prompt('Enroll active contacts with which tag? Leave blank for ALL active contacts.','');if(t===null)return;post('seq/update',{id:s.id,action:'enroll',tag:t}).then(function(j){if(j.error){alert(j.error);return;}alert(j.enrolled+' contact(s) enrolled.'+(j.paused?' The sequence is paused, so nothing is sent until you turn it on.':''));location.reload();});}}),' ',
      el('button',{'class':'btn sm',text:'Rename',on:function(){var n=prompt('Sequence name',s.name);if(n)post('seq/update',{id:s.id,action:'rename',name:n}).then(function(j){done(j);});}}),' ',
      el('button',{'class':'btn sm',text:'Delete',on:function(){if(confirm('Delete this sequence and its history?'))post('seq/update',{id:s.id,action:'delete'}).then(function(j){done(j);});}})])]),
    s.steps.length?steps:el('div',{'class':'muted',style:'margin-top:8px',text:'No emails yet. Click "+ Add email".'})]));});}
 function seqReport(s){var P=$('sq-rep');P.classList.remove('hide');$('sr-h').textContent='Report · '+s.name;$('sr-body').innerHTML='';$('sr-body').appendChild(el('div',{'class':'muted',text:'Loading…'}));P.scrollIntoView({behavior:'smooth'});
  var pr=DEMO?Promise.resolve(srDemo(s)):fetch(B+'seq/'+s.id+'/report',{credentials:'same-origin',headers:{'Accept':'application/json'}}).then(function(r){return r.json();});
  pr.then(function(j){var X=$('sr-body');X.innerHTML='';if(j.error){X.appendChild(el('div',{'class':'msg err',text:j.error}));return;}var t=j.stats||{};
   var g=el('div',{style:'display:grid;grid-template-columns:repeat(auto-fit,minmax(130px,1fr));gap:10px;margin:6px 0 12px'});
   [['Sent',t.sent||0],['Unique opens',t.unique_opens||0],['Open rate',(t.open_rate||0)+'%'],['Unique clicks',t.unique_clicks||0],['Click rate',(t.ctr||0)+'%'],['Total clicks',t.clicks||0]].forEach(function(x,i){g.appendChild(el('div',{style:'background:'+(i==2||i==4?'#FFF7ED':'#F4F6F9')+';border:1px solid '+(i==2||i==4?'#FDBA74':'#E6EAF0')+';border-radius:12px;padding:12px 14px'},[el('div',{style:'font-size:22px;font-weight:700;color:'+(i==2||i==4?'#EA580C':'#1E2A36'),text:String(x[1])}),el('div',{'class':'muted',style:'font-size:12px',text:x[0]})]));});
   X.appendChild(g);X.appendChild(el('div',{style:'background:#FFF7ED;border-left:3px solid #F97316;padding:8px 12px;font-size:12px;border-radius:6px;margin-bottom:12px',text:j.note||'Opens are approximate: some email apps block images and others open them automatically. Clicks are more reliable.'}));
   if((j.links||[]).length){var lt=el('tbody');(j.links).forEach(function(l){lt.appendChild(el('tr',{},[el('td',{style:'word-break:break-all',text:l.url}),el('td',{text:String(l.uniq)}),el('td',{text:String(l.clicks)})]));});
    X.appendChild(el('h5',{style:'margin:8px 0 4px;color:#1E2A36',text:'Clicks by link'}));X.appendChild(el('div',{style:'overflow-x:auto'},[el('table',{},[el('thead',{},[el('tr',{},[el('th',{text:'Link'}),el('th',{text:'People'}),el('th',{text:'Clicks'})])]),lt])]));}
   var rt=el('tbody'),rows=j.rows||[];if(!rows.length)rt.appendChild(el('tr',{},[el('td',{colspan:6,'class':'muted',text:'No tracked emails yet. Stats appear after this sequence sends.'})]));
   rows.forEach(function(r){rt.appendChild(el('tr',{},[el('td',{text:r.email}),el('td',{'class':'muted',text:r.step||''}),el('td',{'class':'muted',text:srT(r.sent_at)}),
    el('td',{},[r.first_open?el('span',{'class':'pill',style:'background:#1E2A36;color:#fff',text:'Opened'+(r.open_count>1?' ×'+r.open_count:'')}):el('span',{'class':'muted',text:'—'}),r.open_machine?el('div',{'class':'muted',style:'font-size:11px',text:'likely automatic'}):null]),
    el('td',{},[r.clicks?el('span',{'class':'pill',style:'background:#F97316;color:#fff',text:r.clicks+' click'+(r.clicks>1?'s':'')}):el('span',{'class':'muted',text:'—'})]),el('td',{'class':'muted',text:srT(r.last_click||r.last_open)})]));});
   X.appendChild(el('h5',{style:'margin:14px 0 4px;color:#1E2A36',text:'Recipients'}));X.appendChild(el('div',{style:'overflow-x:auto'},[el('table',{},[el('thead',{},[el('tr',{},['Email','Email sent','Sent (ET)','Opened','Clicked','Last activity (ET)'].map(function(h){return el('th',{text:h});}))]),rt])]));
  }).catch(function(){$('sr-body').innerHTML='';$('sr-body').appendChild(el('div',{'class':'msg err',text:'Could not load the report. Try again.'}));});}
 function srT(v){if(!v)return '';var d=new Date(v.replace(' ','T')+'Z');return isNaN(d)?v:d.toLocaleString('en-US',{timeZone:'America/New_York',month:'short',day:'numeric',hour:'numeric',minute:'2-digit'});}
 function srDemo(s){var su=((s.steps[0]||{}).subject||'Welcome!').replace(/\{\{my_business\}\}/g,'Bright Path Salon').replace(/\{\{\w+\}\}/g,'Maria'),now=Date.now(),f=function(h){return new Date(now-h*36e5).toISOString().slice(0,19).replace('T',' ');};
  return {stats:{sent:48,unique_opens:27,open_rate:56.3,unique_clicks:9,ctr:18.8,clicks:13},links:[{url:'https://example-salon.com/book',uniq:6,clicks:8},{url:'https://example-salon.com/specials',uniq:3,clicks:5}],
   rows:[['maria.l@example.com',2,1,3],['dev.p@example.com',1,2,5],['jordan.k@example.com',1,0,8],['sam.t@example.com',0,0,0],['alex.r@example.com',3,1,26]].map(function(x){return {email:x[0],step:su,sent_at:f(30),first_open:x[1]?f(x[3]+1):null,last_open:x[1]?f(x[3]):null,open_count:x[1],clicks:x[2],last_click:x[2]?f(x[3]):null,open_machine:0};})};}
 function opts(sel,o,v){sel.innerHTML='';Object.keys(o).forEach(function(k){var x=el('option',{value:k,text:o[k]});if(k==v)x.selected=true;sel.appendChild(x);});}
 opts($('au-trig'),A.triggers);opts($('au-act'),A.actions);var so={};A.sequences.forEach(function(s){so[s.id]=s.name;});if(!A.sequences.length)so['']='(create a sequence first)';opts($('au-seq'),so);
 function auUi(){$('au-ttag').classList.toggle('hide',$('au-trig').value!=='tag_added');var sq=['enroll','stop'].indexOf($('au-act').value)>=0;$('au-seq').classList.toggle('hide',!sq);$('au-tag').classList.toggle('hide',sq);}
 $('au-trig').onchange=$('au-act').onchange=auUi;auUi();
 function rule(a){return (a.trigger==='tag_added'?'When tag "'+a.trigger_tag+'" is added':'When a contact is added')+' → '+A.actions[a.action]+((a.action==='enroll'||a.action==='stop')?' "'+(seqName[a.action_value]||'deleted sequence')+'"':' "'+a.action_value+'"');}
 function autos(){var tb=$('au-rows');tb.innerHTML='';if(!A.automations.length)tb.appendChild(el('tr',{},[el('td',{colspan:5,'class':'muted',text:'No automations yet.'})]));
  A.automations.forEach(function(a){var on=a.status==='ACTIVE';tb.appendChild(el('tr',{},[el('td',{},[el('b',{text:a.name})]),el('td',{'class':'muted',text:rule(a)}),el('td',{},[el('span',{'class':'pill '+(on?'DONE':'PAUSED'),text:LBL[a.status]})]),el('td',{'class':'muted',text:String(a.runs)}),
   el('td',{style:'text-align:right;white-space:nowrap'},[el('button',{'class':'btn sm '+(on?'':'pri'),text:on?'Pause':'Turn on',on:function(){post('auto/update',{id:a.id,action:on?'pause':'activate'}).then(function(j){done(j);});}}),' ',el('button',{'class':'btn sm',text:'Delete',on:function(){if(confirm('Delete this automation?'))post('auto/update',{id:a.id,action:'delete'}).then(function(j){done(j);});}})])]));});}
 window.ECA={$:$,
  newSeq:function(k){var n=k?'':prompt('Name your sequence','New sequence');if(!k&&!n)return;post('seq/create',{starter:k,name:n}).then(function(j){done(j);});},
  editStep:function(s,st){$('sq-ed').classList.remove('hide');$('se-h').textContent=(st?'Edit email':'New email')+' · '+s.name;$('se-seq').value=s.id;$('se-id').value=st?st.id:'';$('se-day').value=st?st.day_offset:(s.steps.length?s.steps[s.steps.length-1].day_offset+2:0);
   $('se-subj').value=st?st.subject:'';$('se-body').innerHTML=st?st.body_html:'<p>Hi {'+'{first_name}},</p><p></p>';$('se-msg').textContent='';$('sq-ed').scrollIntoView({behavior:'smooth'});},
  saveStep:function(){post('step/save',{sequence_id:$('se-seq').value,id:$('se-id').value,day_offset:$('se-day').value,subject:$('se-subj').value,body_html:$('se-body').innerHTML}).then(function(j){done(j,'se-msg');});},
  cmd:function(c,v){$('se-body').focus();document.execCommand(c,false,v||null);},
  link:function(){var u=prompt('Link address (https://…)','https://');if(u&&/^(https?:\/\/.+|mailto:.+)/i.test(u))ECA.cmd('createLink',u);},
  saveAuto:function(){var sq=['enroll','stop'].indexOf($('au-act').value)>=0;post('auto/save',{name:$('au-name').value,trigger:$('au-trig').value,trigger_tag:$('au-ttag').value,action:$('au-act').value,action_value:sq?$('au-seq').value:$('au-tag').value}).then(function(j){done(j,'au-msg');});}};
 seqs();autos();
})();
</script>
