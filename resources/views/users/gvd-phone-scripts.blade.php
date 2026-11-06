@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>.gvd-ps textarea,.gvd-ps input,.gvd-ps select{width:100%;padding:6px 8px;border:1px solid #ccc;border-radius:4px}.gvd-ps label{font-size:12px;color:#666;margin:12px 0 4px;display:flex;justify-content:space-between;align-items:center}.gvd-ps .ps-say{font-size:11px;padding:1px 8px;line-height:18px}.gvd-ps .ps-say.on{background:#fde8e8}</style>
@php
 $psInd = array_values(array_unique(array_map(fn($s) => $s[3] ?? 'General', $d['scripts']))); $psRest = array_values(array_diff($psInd, ['General'])); sort($psRest, SORT_NATURAL|SORT_FLAG_CASE); $psInd = in_array('General', $psInd, true) ? array_merge(['General'], $psRest) : $psRest;
 $rec = $d['hasScriptRec'] ?? null; $recLocked = $rec && in_array((int) $rec->active, [1, 2], true);
 $recStatus = $rec ? ((int) $rec->active === 2 ? 'Approved' : ((int) $rec->active === 1 ? 'Submitted – awaiting recording' : 'Saved – not yet submitted')) : null;
@endphp
<div class="row gvd-vo gvd-ps">
 <div class="col-sm-12"><div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center">
  <div><h5>Downloadable phone scripts</h5><div class="muted">Pick a template, personalize the four sections, then download it or submit it for professional recording.</div></div>
  <button class="btn btn-default btn-sm" type="button" onclick="psDl('all','pdf')">⇩ Download all for this industry (PDF)</button>
 </div></div></div>
 <div class="col-lg-8"><div class="card"><div class="card-header"><h5 id="ps-title">Personalize</h5></div><div class="card-block">
  @if(session('status'))<div class="alert alert-info">{{ session('status') }}</div>@endif
  @if(isset($errors) && $errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <div class="row"><div class="col-sm-6"><label>Industry</label><select id="ps-ind" onchange="psInd(this.value)">@foreach ($psInd as $i)<option value="{{ $i }}">{{ $i }}</option>@endforeach</select></div>
  <div class="col-sm-6"><label>Script</label><select id="ps-pick" onchange="psSel(this.value)"></select></div></div>
  <div class="muted" id="ps-use" style="margin-top:6px"></div>
  <div class="row"><div class="col-sm-6"><label>Business name</label><input id="ps-biz" value="{{ $d['business'] }}" maxlength="120"></div>
  <div class="col-sm-6"><label>Business hours</label><input id="ps-hours" value="Monday through Friday, 9 AM to 5 PM" maxlength="160"></div></div>
  <label>Departments / menu options</label><input id="ps-deps" value="Sales – 1, Support – 2, Billing – 3" maxlength="400">
  <form id="ps-sub" method="post" action="{{ ($rec && !$recLocked) ? url('/updatescriptinfo') : route('saveScriptInfo') }}" enctype="multipart/form-data" onsubmit="return psSubmit()">@csrf
   @if($rec && !$recLocked)<input type="hidden" name="sid" value="{{ $rec->id }}">@endif
   <label>1) Main greeting <button type="button" class="btn btn-default ps-say" data-for="ps-main">▶ Listen</button></label><textarea id="ps-main" name="main_script" rows="4" maxlength="4000"></textarea>
   <label>2) After-hours greeting <button type="button" class="btn btn-default ps-say" data-for="ps-after">▶ Listen</button></label><textarea id="ps-after" name="hours_script" rows="4" maxlength="4000"></textarea>
   <label>3) Message on hold script <button type="button" class="btn btn-default ps-say" data-for="ps-hold">▶ Listen</button></label><textarea id="ps-hold" name="message_onhold" rows="4" maxlength="4000"></textarea>
   <label>4) Special pronunciation words <button type="button" class="btn btn-default ps-say" data-for="ps-pron">▶ Listen</button></label><textarea id="ps-pron" name="special_pronunciation_words" rows="2" maxlength="2000" placeholder="Optional – names or words the voice talent should say a certain way, with a phonetic spelling. Example: Nguyen (say “win”), Siobhan (shi-VAWN)"></textarea>
   @unless($recLocked)
   <label>Your own audio file (optional) <span class="muted">MP3 or OGG</span></label><input type="file" name="audioFile" accept=".mp3,.ogg,audio/mpeg,audio/ogg">
   @endunless
  </form>
  <label>Download format</label><select id="ps-fmt"><option value="docx">Word (.docx)</option><option value="pdf">PDF</option><option value="txt">Plain text (.txt)</option></select>
  <div style="margin-top:14px;display:flex;gap:8px;flex-wrap:wrap">
   <button type="button" class="btn btn-default" onclick="psDl(cur, document.getElementById('ps-fmt').value)">⇩ Download script</button>
   @if($rec && (int) $rec->active === 2)
    <div class="alert alert-success" style="margin:0;padding:6px 12px">✓ Your phone script is approved and recorded. Need a change? Please contact support and we'll help.</div>
   @elseif($recLocked)
    <button type="button" class="btn btn-primary" disabled title="You already have a script recording request on file">Submit for recording</button>
   @else
    <button type="submit" form="ps-sub" class="btn btn-primary">Submit for recording</button>
   @endif
  </div>
  <div class="muted" style="margin-top:8px">“Submit for recording” sends these four sections to our recording team.</div>
 </div></div></div>
 <div class="col-lg-4"><div class="card"><div class="card-header"><h5>Your recording request</h5></div><div class="card-block">
  @if($rec)
   <div><b>Request #{{ $rec->id }}</b></div><div class="muted">Status: {{ $recStatus }}</div><div class="muted">Last updated: {{ $rec->updated_at ?? $rec->created_at }}</div>
   @if($recLocked && $rec->audio_file_path)<div style="margin-top:10px"><div class="muted">Your audio</div><audio controls preload="none" style="width:100%"><source src="/user/phone-scripts/audio/{{ $rec->id }}" type="audio/mpeg"></audio></div>@endif
   @if($recLocked)<div class="muted" style="margin-top:8px">Your request is with our recording team. To make changes, please contact support.</div>@endif
  @else
   <div class="muted">No recording request yet. Personalize the sections and press “Submit for recording”.</div>
  @endif
 </div></div>
 <div class="card"><div class="card-header"><h5>Sample greetings</h5><div class="muted" style="font-size:12px">Hear how a professionally voiced main greeting can sound.</div></div><div class="card-block">
  @php $psSamples = ['General – Bilingual (English / Español)' => 'bilingual-spanish', 'Accounting / tax' => 'accounting', 'Auto repair' => 'auto-repair', 'Church / nonprofit' => 'church', 'Dental office' => 'dental', 'Gym / fitness' => 'gym', 'Home services (plumbing / HVAC)' => 'home-services', 'Hotel / guesthouse' => 'hotel', 'Insurance agency' => 'insurance', 'Law firm' => 'law', 'Medical / med spa clinic' => 'medical', 'Real estate' => 'real-estate', 'Restaurant' => 'restaurant', 'Retail store' => 'retail', 'Salon / barber' => 'salon']; @endphp
  @foreach ($psSamples as $lbl => $f)
   <div style="display:flex;justify-content:space-between;align-items:center;padding:6px 0;border-bottom:1px solid #f0f0f0"><span>{{ $lbl }}</span><button type="button" class="btn btn-default btn-sm ps-smp" data-src="{{ asset('user/data/gvd-samples/'.$f.'.mp3') }}">▶ Play</button></div>
  @endforeach
 </div></div></div>
</div>
<form id="ps-form" method="post" action="/user/phone-scripts/download" style="display:none">@csrf
 <input name="key"><input name="industry"><input name="format"><input name="business"><input name="hours"><input name="departments"><textarea name="text"></textarea>
 <textarea name="sec_main"></textarea><textarea name="sec_after"></textarea><textarea name="sec_hold"></textarea><textarea name="sec_pron"></textarea></form>
<script>
(function(){
 var S = {!! json_encode(array_map(fn($s)=>['t'=>$s[0],'u'=>$s[1],'tpl'=>$s[2],'g'=>$s[3]??'General'], $d['scripts']), JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT) !!};
 window.cur = 'main'; var src = {main:'main', after:'after', hold:'hold'}, edited = {};
 function $(id){return document.getElementById(id);}
 function v(id){return $(id).value.trim();}
 function fill(t){
  var menu = v('ps-deps').split(/[,;\n]+/).map(function(x){return x.trim();}).filter(Boolean).map(function(d,i){var m=d.match(/^(.*?)\s*[–-]\s*(\d)\b/);return m?('For '+m[1]+', press '+m[2]+'.'):('For '+d+', press '+(i+1)+'.');}).join(' ');
  return t.split('{business}').join(v('ps-biz')||'our office').split('{hours}').join(v('ps-hours')||'during regular business hours').split('{departments_menu}').join(menu).split('{departments}').join(v('ps-deps')).trim();
 }
 function type(k){ return /(^|_)after$/.test(k) ? 'after' : (/(^|_)hold$/.test(k) ? 'hold' : 'main'); }
 function find(g, t){ var ks = Object.keys(S).filter(function(k){ return S[k].g===g && type(k)===t && (t!=='main' || k==='main' || /_main$/.test(k)); }); return ks[0] || null; }
 function render(){ ['main','after','hold'].forEach(function(s){ if(!edited[s] && S[src[s]]) $('ps-'+s).value = fill(S[src[s]].tpl); }); $('ps-title').textContent = 'Personalize: '+S[cur].t; }
 window.psSel = function(k){ if(!S[k]) return; cur=k; var g=S[k].g, t=type(k);
  src = {main: find(g,'main') || 'main', after: find(g,'after') || 'after', hold: find(g,'hold') || 'hold'}; src[t] = k; edited = {};
  $('ps-pick').value=k; $('ps-use').textContent='Use for: '+S[k].u; render(); };
 window.psInd = function(g){ var p=$('ps-pick'), ks=Object.keys(S).filter(function(k){return S[k].g===g;}).sort(function(a,b){return S[a].t.localeCompare(S[b].t);}); p.innerHTML=''; ks.forEach(function(k){var o=document.createElement('option'); o.value=k; o.textContent=S[k].t; p.appendChild(o);}); if(ks.length) psSel(ks.filter(function(k){return k==='main'||/_main$/.test(k);})[0]||ks[0]); };
 ['ps-biz','ps-hours','ps-deps'].forEach(function(id){$(id).addEventListener('input', render);});
 ['main','after','hold'].forEach(function(s){ $('ps-'+s).addEventListener('input', function(){edited[s]=true;}); });
 function secs(){ return {sec_main:$('ps-main').value, sec_after:$('ps-after').value, sec_hold:$('ps-hold').value, sec_pron:$('ps-pron').value}; }
 window.psDl = function(k, fmt){ var f=$('ps-form'), x=secs(); f.key.value=k; f.industry.value=$('ps-ind').value; f.format.value=fmt; f.business.value=v('ps-biz'); f.hours.value=v('ps-hours'); f.departments.value=v('ps-deps'); f.text.value='';
  for (var n in x) f[n].value = (k==='all') ? '' : x[n]; f.submit(); };
 window.psSubmit = function(){ if(v('ps-main').length < 10){ alert('Please enter a main greeting.'); $('ps-main').focus(); return false; }
  if(!v('ps-biz')){ alert('Please enter your business name.'); $('ps-biz').focus(); return false; }
  return confirm('Submit these four sections to our recording team?'); };
 window.psCopy = function(){ try{ var x=secs(); sessionStorage.setItem('gvd_script_prefill', JSON.stringify({main:x.sec_main, after:x.sec_after, hold:x.sec_hold, pron:x.sec_pron})); }catch(e){} };
 // Listen buttons (browser speech only; hidden if unsupported)
 var syn = window.speechSynthesis, playing = null;
 if (!syn || typeof SpeechSynthesisUtterance === 'undefined') { document.querySelectorAll('.ps-say').forEach(function(b){ b.style.display='none'; }); }
 else {
  function pick(es){ var vs = syn.getVoices(), want = es ? /^es/i : /^en-US/i; return vs.filter(function(x){return want.test(x.lang);})[0] || vs.filter(function(x){return (es?/^es/i:/^en/i).test(x.lang);})[0] || null; }
  function isEs(t){ return /[ñ¿¡]|\b(gracias|por favor|llamar|nuestro|usted|horario|oprima|marque)\b/i.test(t); }
  function reset(){ document.querySelectorAll('.ps-say').forEach(function(b){ b.classList.remove('on'); b.textContent='▶ Listen'; }); playing=null; }
  document.querySelectorAll('.ps-say').forEach(function(b){ b.addEventListener('click', function(){
   var id=b.getAttribute('data-for'), was=playing; syn.cancel(); reset(); if(was===id) return;
   var t=$(id).value.trim(); if(!t) return; var u=new SpeechSynthesisUtterance(t), es=isEs(t), vo=pick(es); u.lang = es ? 'es-US' : 'en-US'; if(vo) u.voice=vo; u.rate=0.95;
   u.onend = u.onerror = function(){ if(playing===id) reset(); }; playing=id; b.classList.add('on'); b.textContent='■ Stop'; syn.speak(u); }); });
  if (syn.onvoiceschanged !== undefined) syn.onvoiceschanged = function(){};
 }
 // Sample greeting clips
 var smp = new Audio(), smpBtn = null;
 function smpReset(){ if(smpBtn){ smpBtn.textContent='▶ Play'; smpBtn=null; } }
 smp.addEventListener('ended', smpReset); smp.addEventListener('error', smpReset);
 document.querySelectorAll('.ps-smp').forEach(function(b){ b.addEventListener('click', function(){ var same = smpBtn===b; smp.pause(); smpReset(); if(same) return;
  if (window.speechSynthesis) speechSynthesis.cancel(); smp.src=b.getAttribute('data-src'); smp.play(); smpBtn=b; b.textContent='■ Stop'; }); });
 psInd($('ps-ind').value);
})();
</script>
@include('users.partials.vo-foot', ['page' => 'phonescripts'])
@endsection
