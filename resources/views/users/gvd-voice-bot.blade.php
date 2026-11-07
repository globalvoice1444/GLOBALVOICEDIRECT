@extends('layouts.user.user_main')
@section('content')
<style>
.vb{--n:#0b1f44;--o:#ff7a1a;--l:#e6eaf2;--m:#5a6785;font-family:Inter,system-ui,Segoe UI,sans-serif;color:#0f1b33}
.vb *{box-sizing:border-box}
.vb .hero{background:var(--n);color:#fff;border-radius:16px;padding:24px 26px;margin-bottom:18px}
.vb .hero h2{margin:0;color:#fff;font-size:24px;font-weight:700}
.vb .hero p{margin:6px 0 0;color:#c9d3e8;font-size:14px;max-width:680px}
.vb .card{background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(11,31,68,.07);margin-bottom:18px;border:1px solid #eef1f7;max-width:760px}
.vb .card h3{font-size:16px;margin:0 0 14px;font-weight:700}
.vb label.f{display:block;font-size:12.5px;font-weight:600;margin:12px 0 6px}
.vb input[type=text],.vb input[type=password],.vb select{width:100%;border:1px solid var(--l);border-radius:8px;padding:9px 11px;font-size:14px}
.vb .btn{background:var(--o);color:#fff;border:0;border-radius:8px;padding:9px 14px;font-weight:600;font-size:13px;cursor:pointer}
.vb .btn.ghost{background:#eef1f7;color:var(--n)}.vb .btn.red{background:#c62828}.vb .btn[disabled]{opacity:.5;cursor:not-allowed}
.vb .url{background:var(--n);color:#fff;padding:10px 12px;border-radius:8px;font-family:ui-monospace,monospace;font-size:13px;margin:8px 0;word-break:break-all}
.vb .small{font-size:12px;color:var(--m)}
.vb .row{display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-top:16px}
.vb .msg{font-size:13px;margin-top:10px;min-height:1.2em}
.vb .ok{color:#1b8a4b;font-weight:600}.vb .err{color:#c62828;font-weight:600}
.vb .sw{display:flex;gap:8px;align-items:center;font-size:14px;margin-top:12px}
.vb .mask{font-family:ui-monospace,monospace;letter-spacing:2px}
.vb .vb-prov-line{display:flex;align-items:center;gap:8px}
.vb .vb-prov-line select{flex:1;width:auto}
.vb .vb-info{width:28px;height:28px;flex:none;border-radius:50%;border:1px solid var(--l);background:#fff;color:var(--n);font-weight:700;line-height:1;cursor:pointer}
.vb .vb-modal{position:fixed;inset:0;z-index:2000;display:flex;align-items:center;justify-content:center;padding:24px}
.vb .vb-modal[hidden]{display:none}
.vb .vb-modal-back{position:absolute;inset:0;background:rgba(11,31,68,.45);border:0;padding:0;cursor:pointer}
.vb .vb-modal-panel{position:relative;background:#fff;border-radius:14px;max-width:560px;width:100%;max-height:min(80vh,640px);overflow:auto;padding:16px 18px 18px;box-shadow:0 16px 48px rgba(11,31,68,.28)}
.vb .vb-modal-bar{display:flex;align-items:center;justify-content:space-between;gap:12px}
.vb .vb-modal-bar h3{margin:0;font-size:16px}
.vb .vb-modal-x{background:#eef1f7;color:var(--n);border:0;border-radius:8px;padding:6px 10px;font-weight:600;cursor:pointer}
.vb .vb-detail{margin-top:12px}
.vb .vb-detail p{margin:6px 0}
.vb .vb-detail a{word-break:break-all}
</style>
<div class="vb" id="vb">
 <div class="hero"><h2>Voice bot</h2><p>Give your assistant a voice account on your line. Your provider key and the SIP password stay on this account only, and they are never shown again after you save them.</p></div>
 <div class="card">
  <h3>MCP endpoint</h3>
  <div class="small">Point your assistant at this server address.</div>
  <div class="url" id="vbMcp">{{ $endpoint }}</div>
 </div>
 <div class="card">
  <h3>Voice account</h3>
  @if($account['provisioned'])<p class="small">Status: {{ $account['status'] === 'active' ? 'Provisioned' : ucfirst($account['status']) }}.</p>@endif
  @if($account['last_error'])<p class="err">{{ $account['last_error'] }}</p>@endif
  <label class="f" for="vbUser">Extension</label>
  @if($canProvision)
  <select id="vbUser" {{ $demo ? 'disabled' : '' }}>
   @if(count($extensions) !== 1)<option value="">Choose an extension</option>@endif
   @foreach($extensions as $ex)
    <option value="{{ $ex['i_account'] }}" {{ ($account['username'] === $ex['username'] || count($extensions) === 1) ? 'selected' : '' }}>{{ $ex['label'] }}</option>
   @endforeach
  </select>
  <p class="small">Your extensions are loaded from your phone account. The SIP username is filled from the one you select. Its existing SIP password is used and is not shown here.</p>
  @else
  <select id="vbUser" disabled><option value="">No extension available</option></select>
  <p class="err">{{ $extensionMessage }}</p>
  @endif
  <label class="f" for="vbCid">Caller ID</label>
  <input type="text" id="vbCid" inputmode="numeric" maxlength="20" autocomplete="off" placeholder="Optional. 10 to 15 digits" value="{{ $account['caller_id'] }}" {{ $demo ? 'disabled' : '' }}>
  <label class="f" for="vbProv">Provider</label>
  <div class="vb-prov-line">
   <select id="vbProv" {{ $demo ? 'disabled' : '' }}>
    @foreach(['realtime_voice' => 'Realtime voice', 'text_llm' => 'Text models (speech pipeline needed)'] as $cat => $heading)
     <optgroup label="{{ $heading }}">
      @foreach($providerGroups[$cat] ?? [] as $id => $p)
       <option value="{{ $id }}" {{ $account['provider'] === $id ? 'selected' : '' }}>{{ $p['label'] }}{{ empty($p['gateway_supported']) ? ' — calls not yet supported' : '' }}</option>
      @endforeach
     </optgroup>
    @endforeach
   </select>
   <button type="button" class="vb-info" id="vbProvInfo" aria-label="Provider details" aria-haspopup="dialog" aria-controls="vbProvModal" aria-expanded="false">?</button>
  </div>
  <div class="vb-modal" id="vbProvModal" hidden>
   <button type="button" class="vb-modal-back" data-vb-close="1" aria-label="Close"></button>
   <div class="vb-modal-panel" role="dialog" aria-modal="true" aria-labelledby="vbProvTitle">
    <div class="vb-modal-bar">
     <h3 id="vbProvTitle">Provider details</h3>
     <button type="button" class="vb-modal-x" id="vbProvClose" aria-label="Close">Close</button>
    </div>
    <div id="vbProvDetail" class="vb-detail"></div>
   </div>
  </div>
  <script type="application/json" id="vbProvData"><?php echo json_encode(collect($providers)->map(function ($p) {
    return ['label' => $p['label'], 'note' => $p['setup_note'], 'url' => $p['setup_url'] ?? '', 'calls' => !empty($p['gateway_supported'])];
  }), JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_UNESCAPED_SLASHES); ?></script>
  <div id="vbCreds">
   @foreach($providers as $id => $p)
    @foreach($p['credential_fields'] as $field)
     <div class="vb-cred" data-provider="{{ $id }}" @if($account['provider'] !== $id) style="display:none" @endif>
      <label class="f" for="vb-{{ $id }}-{{ $field['name'] }}">{{ $field['label'] }}</label>
      @if(!empty($field['secret']))
       <input type="password" id="vb-{{ $id }}-{{ $field['name'] }}" data-field="{{ $field['name'] }}" maxlength="2000" autocomplete="new-password" placeholder="{{ $account['has_key'] ? 'Saved. Enter a new value to replace it.' : 'Paste the value' }}" {{ $demo ? 'disabled' : '' }}>
      @else
       <input type="text" id="vb-{{ $id }}-{{ $field['name'] }}" data-field="{{ $field['name'] }}" maxlength="200" autocomplete="off" value="{{ $account['public_credentials'][$field['name']] ?? '' }}" {{ $demo ? 'disabled' : '' }}>
      @endif
     </div>
    @endforeach
   @endforeach
  </div>
  <p class="small">@if($account['has_key'])<span class="mask" aria-hidden="true">••••••••</span> A credential is saved. Secret fields are write-only. Leave a secret blank to keep it.@else No provider credential is saved yet.@endif</p>
  <label class="sw"><input type="checkbox" id="vbOn" {{ $account['enabled'] ? 'checked' : '' }} {{ $demo ? 'disabled' : '' }}> Account enabled</label>
  <p class="small" style="margin-top:10px">The SIP password already on the extension is stored encrypted and sent to the voice gateway. It is not shown on this page.</p>
  <div class="row">
   <button class="btn" id="vbSave" type="button" {{ ($demo || !$canProvision) ? 'disabled' : '' }}>Provision voice account</button>
   <button class="btn ghost" id="vbDropKey" type="button" {{ $demo || !$account['has_key'] ? 'disabled' : '' }}>Remove provider key</button>
   @if($account['provisioned'])
   <button class="btn red" id="vbDrop" type="button" {{ $demo ? 'disabled' : '' }}>Remove voice account</button>
   @endif
  </div>
  <div class="msg" id="vbMsg" role="status"></div>
 </div>
</div>
<script>
var VB=(function(){
 var T=@json(csrf_token()), B='/user/ai-connect/voice-bot';
 function post(u,b){return fetch(B+u,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':T,'Accept':'application/json'},body:JSON.stringify(b)}).then(function(r){return r.json().then(function(j){j._status=r.status;return j;});});}
 function say(ok,t){var m=document.getElementById('vbMsg');m.className='msg '+(ok?'ok':'err');m.textContent=t||'';}
 return {
  save:function(){
   var btn=document.getElementById('vbSave'); btn.disabled=true;
   var user=document.getElementById('vbUser'), prov=document.getElementById('vbProv').value, creds={};
   document.querySelectorAll('.vb-cred').forEach(function(el){
    if (el.getAttribute('data-provider')!==prov) return;
    var input=el.querySelector('input'); if(!input) return;
    creds[input.getAttribute('data-field')]=input.value;
   });
   post('',{extension:user?user.value:'',caller_id:document.getElementById('vbCid').value,provider:prov,enabled:document.getElementById('vbOn').checked,credentials:creds,api_key:creds.api_key||''}).then(function(r){
    if(!r.ok){say(false,r.error||'Could not provision the voice account.');btn.disabled=false;return;}
    document.getElementById('vbKey').value='';
    if(r.queued){say(true,'Provisioning has started.');btn.disabled=false;return;}
    location.reload();
   }).catch(function(){say(false,'Could not provision the voice account.');btn.disabled=false;});
  },
  dropKey:function(){
   if(!confirm('Remove the saved provider key?'))return;
   post('/key/remove',{}).then(function(r){if(!r.ok){say(false,r.error||'Could not remove the provider key.');return;}location.reload();});
  },
  drop:function(){
   if(!confirm('Remove this voice account?'))return;
   post('/remove',{}).then(function(r){if(!r.ok){say(false,r.error||'Could not remove the voice account.');return;}location.reload();});
  }
 };
})();
document.getElementById('vbSave').addEventListener('click',function(){VB.save();});
(function(){
 var openBtn=document.getElementById('vbProvInfo'), modal=document.getElementById('vbProvModal'), closeBtn=document.getElementById('vbProvClose'), detail=document.getElementById('vbProvDetail'), sel=document.getElementById('vbProv'), dataEl=document.getElementById('vbProvData'), lastFocus=null, map={};
 if(!openBtn||!modal||!detail) return;
 try { map=JSON.parse(dataEl?dataEl.textContent:'{}')||{}; } catch(e) { map={}; }
 function showCreds(){if(!sel)return;document.querySelectorAll('.vb-cred').forEach(function(el){el.style.display=el.getAttribute('data-provider')===sel.value?'':'none';});}
 function render(){
  var row=sel?map[sel.value]:null;
  detail.textContent='';
  if(!row) return;
  var name=document.createElement('b'); name.id='vbProvDetailName'; name.textContent=row.label||'';
  var note=document.createElement('p'); note.id='vbProvDetailNote'; note.textContent=row.note||'';
  var status=document.createElement('p'); status.id='vbProvDetailStatus'; status.textContent=row.calls?'Calls are supported.':'Calls are not yet supported.';
  detail.appendChild(name); detail.appendChild(note);
  if(row.url){var p=document.createElement('p'), a=document.createElement('a'); a.href=row.url; a.target='_blank'; a.rel='noopener noreferrer'; a.textContent=row.url; p.appendChild(a); detail.appendChild(p);}
  detail.appendChild(status);
 }
 function openModal(){render();lastFocus=document.activeElement;modal.hidden=false;openBtn.setAttribute('aria-expanded','true');if(closeBtn)closeBtn.focus();}
 function closeModal(){if(modal.hidden)return;modal.hidden=true;openBtn.setAttribute('aria-expanded','false');if(lastFocus&&lastFocus.focus)lastFocus.focus();else openBtn.focus();}
 if(sel) sel.addEventListener('change', function(){showCreds();render();});
 openBtn.addEventListener('click',openModal);
 if(closeBtn) closeBtn.addEventListener('click',closeModal);
 modal.addEventListener('click',function(e){if(e.target.getAttribute('data-vb-close')==='1')closeModal();});
 document.addEventListener('keydown',function(e){if((e.key==='Escape'||e.key==='Esc')&&!modal.hidden){e.preventDefault();closeModal();}});
 render();
})();
var dk=document.getElementById('vbDropKey'); if(dk) dk.addEventListener('click',function(){VB.dropKey();});
var dd=document.getElementById('vbDrop'); if(dd) dd.addEventListener('click',function(){VB.drop();});
</script>
@endsection
