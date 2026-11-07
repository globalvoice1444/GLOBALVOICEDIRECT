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
  <label class="f" for="vbUser">SIP username</label>
  @if(count($extensions) > 1)
  <select id="vbUser" {{ $demo ? 'disabled' : '' }}>
   @foreach($extensions as $ex)
    <option value="{{ $ex['username'] }}" {{ $account['username'] === $ex['username'] ? 'selected' : '' }}>{{ $ex['label'] }}</option>
   @endforeach
  </select>
  <p class="small">Choose one of your extensions. The SIP username is that extension's login and cannot be typed in.</p>
  @else
  <input type="text" id="vbUser" readonly value="{{ $account['username'] }}" aria-readonly="true">
  @if($canProvision)
  <p class="small">This is the SIP username on your extension. It cannot be changed here.</p>
  @else
  <p class="err">No SIP extension is available on this account. Sign in with your phone-line login so we can read your extensions. Provisioning stays off until an extension is available.</p>
  @endif
  @endif
  <label class="f" for="vbCid">Caller ID</label>
  <input type="text" id="vbCid" inputmode="numeric" maxlength="20" autocomplete="off" placeholder="Optional. 10 to 15 digits" value="{{ $account['caller_id'] }}" {{ $demo ? 'disabled' : '' }}>
  <label class="f" for="vbProv">Provider</label>
  <select id="vbProv" {{ $demo ? 'disabled' : '' }}>
   @foreach($providers as $id => $label)
    <option value="{{ $id }}" {{ $account['provider'] === $id ? 'selected' : '' }}>{{ $label }}</option>
   @endforeach
  </select>
  <label class="f" for="vbKey">Provider API key</label>
  <input type="password" id="vbKey" maxlength="2000" autocomplete="new-password" placeholder="{{ $account['has_key'] ? 'Key saved. Enter a new key to replace it.' : 'Paste your provider key' }}" {{ $demo ? 'disabled' : '' }}>
  <p class="small">@if($account['has_key'])<span class="mask" aria-hidden="true">••••••••</span> A key is saved for this account. It is write-only. Leave the field blank to keep it.@else No provider key is saved yet.@endif</p>
  <label class="sw"><input type="checkbox" id="vbOn" {{ $account['enabled'] ? 'checked' : '' }} {{ $demo ? 'disabled' : '' }}> Account enabled</label>
  <p class="small" style="margin-top:10px">The SIP password is generated here and stored encrypted. It is sent to the voice gateway and is not shown on this page.</p>
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
   var user=document.getElementById('vbUser');
   post('',{sip_username:user?user.value:'',caller_id:document.getElementById('vbCid').value,provider:document.getElementById('vbProv').value,enabled:document.getElementById('vbOn').checked,api_key:document.getElementById('vbKey').value}).then(function(r){
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
var dk=document.getElementById('vbDropKey'); if(dk) dk.addEventListener('click',function(){VB.dropKey();});
var dd=document.getElementById('vbDrop'); if(dd) dd.addEventListener('click',function(){VB.drop();});
</script>
@endsection
