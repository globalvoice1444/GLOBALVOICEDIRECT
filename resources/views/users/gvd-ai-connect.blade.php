@extends('layouts.user.user_main')
@section('content')
<style>
.ai{--n:#0b1f44;--o:#ff7a1a;--l:#e6eaf2;--m:#5a6785;font-family:Inter,system-ui,Segoe UI,sans-serif;color:#0f1b33}
.ai *{box-sizing:border-box}.ai .hero{background:var(--n);color:#fff;border-radius:16px;padding:24px 26px;margin-bottom:18px;display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap;align-items:center}
.ai .hero h2{margin:0;color:#fff;font-size:24px;font-weight:700}.ai .hero p{margin:6px 0 0;color:#c9d3e8;font-size:14px;max-width:640px}
.ai .grid{display:grid;grid-template-columns:minmax(0,2fr) minmax(300px,1fr);gap:18px}.ai .tw{overflow-x:auto}.ai .grid>div{min-width:0}@media(max-width:1250px){.ai .grid{grid-template-columns:1fr}}@media(max-width:980px){.ai .grid{grid-template-columns:1fr}}
.ai .card{background:#fff;border-radius:14px;padding:20px;box-shadow:0 2px 10px rgba(11,31,68,.07);margin-bottom:18px;border:1px solid #eef1f7}
.ai .card h3{font-size:16px;margin:0 0 14px;display:flex;justify-content:space-between;align-items:center;font-weight:700}
.ai .btn{background:var(--o);color:#fff;border:0;border-radius:8px;padding:8px 14px;font-weight:600;font-size:13px;cursor:pointer}.ai .btn.ghost{background:#eef1f7;color:var(--n)}.ai .btn.red{background:#c62828}.ai .btn[disabled]{opacity:.5;cursor:not-allowed}
.ai table{width:100%;border-collapse:collapse;font-size:13px}.ai th{text-align:left;color:var(--m);font-weight:600;padding:8px 6px;border-bottom:1px solid var(--l)}.ai td{padding:10px 6px;border-bottom:1px solid #f0f2f7;vertical-align:top}
.ai .mono{font-family:ui-monospace,Menlo,monospace;color:#33415f}.ai .chip{display:inline-block;font-size:11px;padding:2px 8px;border-radius:20px;background:#e8eefc;color:var(--n);margin:1px}.ai .chip.w{background:#fff0e5;color:#b45200}
.ai .ok{color:#1b8a4b;font-weight:600}.ai .wait{color:#b45200;font-weight:600}.ai .off{color:#8a93a8}.ai .err{color:#c62828;font-weight:600}
.ai .small{font-size:12px;color:var(--m)}.ai .url{background:var(--n);color:#fff;padding:10px 12px;border-radius:8px;font-family:ui-monospace,monospace;font-size:13px;margin:8px 0;word-break:break-all}
.ai .kill{border:2px solid #ffd6b8;background:#fff8f2}.ai .sw{display:flex;justify-content:space-between;align-items:center;font-size:14px;gap:10px}
.ai .tg{width:46px;height:26px;border-radius:13px;background:#1b8a4b;position:relative;border:0;cursor:pointer;flex:none}.ai .tg:after{content:"";position:absolute;right:3px;top:3px;width:20px;height:20px;border-radius:50%;background:#fff}.ai .tg.offt{background:#c3c9d6}.ai .tg.offt:after{left:3px;right:auto}
.ai ol{padding-left:18px;font-size:13px;line-height:1.7;margin:0}.ai code{background:#eef1f7;padding:1px 6px;border-radius:4px}
.ai .note{background:#fff8e6;border:1px solid #fde7a7;border-radius:10px;padding:10px 14px;font-size:13px;margin-bottom:14px}
.ai .modal{position:fixed;inset:0;background:rgba(11,31,68,.45);display:flex;align-items:center;justify-content:center;z-index:9999}.ai .modal .box{background:#fff;border-radius:14px;padding:22px;width:min(520px,94vw);max-height:90vh;overflow:auto}
.ai input[type=text],.ai input[type=number]{width:100%;border:1px solid var(--l);border-radius:8px;padding:9px 11px;font-size:14px}.ai label.f{display:block;font-size:12.5px;font-weight:600;margin:12px 0 6px}
.ai .sc{display:flex;gap:8px;align-items:flex-start;font-size:13px;margin:6px 0}.ai .hide{display:none!important}.ai .msg{font-size:13px;margin-top:8px}
</style>
<div class="ai" id="ai">
 <div class="hero"><div><h2>AI Connect</h2><p>Let ChatGPT, Claude or another AI assistant use your Global Voice Direct tools: read your contacts, send email from your own mailbox, start sequences and pull reports. You approve every action before it happens.</p></div>
  <div><span class="chip" style="background:rgba(255,255,255,.12);color:#fff">{{ count(array_filter($d['keys'], fn($k)=>!$k['revoked'])) }} active key(s)</span></div></div>
 @if(!$d['live'])<div class="note"><b>Preview.</b> AI Connect is not switched on for customers yet. {{ $d['demo'] ? 'This sample account is read-only.' : '' }}</div>@endif
 <div class="grid">
  <div>
   <div class="card"><h3>API keys <button class="btn" onclick="AI.open()" {{ $d['demo'] ? 'disabled title=Sample account is read-only' : '' }}>+ Create key</button></h3>
    <div class="tw"><table><thead><tr><th>Name</th><th>Key</th><th>Permissions</th><th>Today</th><th>Last used</th><th></th></tr></thead><tbody>
    @forelse($d['keys'] as $k)
     <tr><td><b>{{ $k['name'] }}</b><div class="small">Created {{ $k['created'] }}</div></td><td class="mono">{{ $k['prefix'] }}…</td>
      <td>@foreach($k['scopes'] as $s)<span class="chip {{ in_array($s,['email:send','calls:place','sequences:run']) ? 'w' : '' }}">{{ $s }}</span>@endforeach</td>
      <td>{{ $k['used_today'] }} / {{ $k['daily_limit'] }}</td><td>{{ $k['last_used'] }}</td>
      <td>@if($k['revoked'])<span class="off">Revoked</span>@else<button class="btn ghost" onclick="AI.revoke({{ $k['id'] }})" {{ $d['demo'] ? 'disabled' : '' }}>Revoke</button>@endif</td></tr>
    @empty <tr><td colspan="6" class="small">No keys yet. Create one, then paste it into your AI assistant.</td></tr>
    @endforelse</tbody></table></div>
    <p class="small" style="margin:10px 0 0">Keys are shown once when you create them. We store only a scrambled fingerprint, so a lost key can't be recovered: revoke it and make a new one.</p></div>
   @if($d['oauth'] || count($d['apps']))
   <div class="card"><h3>Connected apps</h3>
    <div class="tw"><table><thead><tr><th>App</th><th>Permissions</th><th>Today</th><th>Last used</th><th></th></tr></thead><tbody>
    @forelse($d['apps'] as $a)
     <tr><td><b>{{ $a['name'] }}</b><div class="small">Connected {{ $a['created'] }}</div></td>
      <td>@foreach($a['scopes'] as $s)<span class="chip {{ in_array($s,['email:send','calls:place','sequences:run']) ? 'w' : '' }}">{{ $s }}</span>@endforeach</td>
      <td>{{ $a['used_today'] }} / {{ $a['daily_limit'] }}</td><td>{{ $a['last_used'] }}</td>
      <td><button class="btn ghost" onclick="AI.revokeApp({{ $a['id'] }})">Disconnect</button></td></tr>
    @empty <tr><td colspan="5" class="small">No apps connected yet. In ChatGPT or another assistant, add the server address below and choose Sign in with Global Voice Direct.</td></tr>
    @endforelse</tbody></table></div>
    <p class="small" style="margin:10px 0 0">These apps signed in with your Global Voice Direct login. Disconnecting one stops it right away.</p></div>
   @endif
   <div class="card"><h3>Activity</h3>
    <div class="tw"><table><thead><tr><th>When</th><th>Key</th><th>Action</th><th>Result</th><th>Details</th></tr></thead><tbody>
    @forelse($d['activity'] as $a)
     <tr><td>{{ $a['when'] }}</td><td>{{ $a['key'] }}</td><td class="mono">{{ $a['what'] }}@if($a['mode']) <span class="small">({{ $a['mode'] }})</span>@endif</td>
      <td class="{{ $a['status']==='ok' ? 'ok' : ($a['status']==='error' ? 'err' : 'wait') }}">{{ ucfirst($a['status']) }}</td><td class="small">{{ $a['summary'] }}</td></tr>
    @empty <tr><td colspan="5" class="small">Nothing yet. Every request from an AI assistant shows up here.</td></tr>
    @endforelse</tbody></table></div></div>
  </div>
  <div>
   <div class="card kill"><h3>Master switch</h3>
    <div class="sw"><span>{{ $d['enabled'] ? 'AI assistants can use your account' : 'All AI access is turned off' }}</span><button class="tg {{ $d['enabled'] ? '' : 'offt' }}" aria-label="AI Connect on/off" onclick="AI.toggle({{ $d['enabled'] ? 'false' : 'true' }})" {{ $d['demo'] ? 'disabled' : '' }}></button></div>
    <p class="small" style="margin:8px 0 0">Turn this off to block every key at once.</p></div>
   <div class="card"><h3>Calling line</h3>
    @if($d['line'])
     <div class="sw"><span><b>{{ $d['line']['account'] }}</b><div class="small">Linked {{ $d['line']['linked'] }}</div></span><button class="btn ghost" onclick="AI.unlink()" {{ $d['demo'] ? 'disabled' : '' }}>Unlink</button></div>
     <p class="small" style="margin:10px 0 0">When an assistant places a call, this line rings first, then connects you to the number.{{ $d['calls'] ? '' : ' AI calling is not switched on yet.' }}</p>
    @else
     <p class="small" style="margin:0 0 8px">Pick the line that rings first when your assistant places a call. We check the line, then keep its calling key encrypted for your account only.</p>
     @if($d['lines'])
      <div style="display:flex;gap:8px"><select id="aiLine" style="flex:1;border:1px solid #e6eaf2;border-radius:8px;padding:8px">@foreach($d['lines'] as $l)<option value="{{ $l['id'] }}">{{ $l['label'] }}</option>@endforeach</select><button class="btn" onclick="AI.link()" {{ $d['demo'] ? 'disabled' : '' }}>Link line</button></div>
     @else <p class="small">We couldn't load your lines. Your lines load when you sign in with your phone account login (the one for your phone lines). Sign out, sign in with that login, then come back here.</p>@endif
     <div class="msg err" id="aiLineErr"></div>
     @if(!$d['calls'])<p class="small" style="margin:10px 0 0">AI calling is not switched on yet. You can link your line now.</p>@endif
    @endif</div>
   <div class="card"><h3>Connect your assistant</h3>
    <div class="small">Server address</div><div class="url">{{ $d['endpoint'] }}</div>
    <ol><li><b>Claude:</b> Settings &gt; Connectors &gt; Add custom connector. Paste the address above and your key.</li>
     <li><b>ChatGPT:</b> {{ $d['oauth'] ? 'Settings > Apps & Connectors > Create. Paste the address above, choose OAuth, then sign in with Global Voice Direct and click Allow. ' : '' }}Custom connectors that take actions need a ChatGPT Business, Enterprise or Edu plan.</li>
     <li><b>Other tools</b> (Cursor, scripts): send <code>Authorization: Bearer YOUR_KEY</code> to the address above.</li></ol>
    <p class="small" style="margin:10px 0 0">Emails go out from your own connected mailbox, with your daily limit, unsubscribe link and tracking.{{ $d['calls'] ? '' : ' Calling through AI Connect is coming soon.' }}</p></div>
  </div>
 </div>
 <div class="modal hide" id="aiM"><div class="box">
  <div id="aiForm"><h3 style="margin:0 0 6px">Create an API key</h3><div class="small">Give each assistant its own key so you can revoke it on its own.</div>
   <label class="f">Name</label><input type="text" id="aiName" maxlength="80" placeholder="e.g. Claude, office laptop">
   <label class="f">Permissions</label>
   @foreach($d['scopes'] as $s=>$lbl)<label class="sc"><input type="checkbox" value="{{ $s }}" {{ in_array($s,['contacts:read','campaigns:read','reports:read']) ? 'checked' : '' }}><span><span class="mono">{{ $s }}</span> {{ $lbl }}</span></label>@endforeach
   <label class="f">Daily limit (actions per day)</label><input type="number" id="aiLim" min="1" max="{{ $d['max_limit'] }}" value="{{ $d['default_limit'] }}">
   <div class="msg err" id="aiErr"></div>
   <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px"><button class="btn ghost" onclick="AI.close()">Cancel</button><button class="btn" onclick="AI.create()">Create key</button></div></div>
  <div id="aiShow" class="hide"><h3 style="margin:0 0 6px">Copy your key now</h3><div class="small">For your security we won't show it again.</div>
   <div class="url" id="aiKey"></div><div style="display:flex;gap:8px;justify-content:flex-end"><button class="btn ghost" onclick="AI.copy()">Copy</button><button class="btn" onclick="location.reload()">Done</button></div></div>
 </div></div>
</div>
<script>
var AI=(function(){var T=@json(csrf_token()),B='/user/ai-connect/';
 function post(u,b){return fetch(B+u,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':T,'Accept':'application/json'},body:JSON.stringify(b)}).then(function(r){return r.json()});}
 var M=document.getElementById('aiM');
 return {open:function(){M.classList.remove('hide')},close:function(){M.classList.add('hide')},
  create:function(){var sc=[].map.call(M.querySelectorAll('input[type=checkbox]:checked'),function(x){return x.value});
   post('keys/create',{name:document.getElementById('aiName').value,scopes:sc,daily_limit:+document.getElementById('aiLim').value}).then(function(r){
    if(!r.ok){document.getElementById('aiErr').textContent=r.error||'Could not create the key.';return}
    document.getElementById('aiKey').textContent=r.key;document.getElementById('aiForm').classList.add('hide');document.getElementById('aiShow').classList.remove('hide');})},
  copy:function(){navigator.clipboard&&navigator.clipboard.writeText(document.getElementById('aiKey').textContent)},
  revokeApp:function(id){if(!confirm('Disconnect this app? It stops working right away.'))return;post('apps/revoke',{id:id}).then(function(){location.reload()})},
  revoke:function(id){if(!confirm('Revoke this key? Any assistant using it stops working right away.'))return;post('keys/revoke',{id:id}).then(function(){location.reload()})},
  link:function(){var e=document.getElementById('aiLineErr');post('line/link',{account:document.getElementById('aiLine').value}).then(function(r){if(!r.ok){e.textContent=r.error||'Could not link the line.';return}location.reload()})},
  unlink:function(){if(!confirm('Unlink this line? Your assistant will not be able to place calls.'))return;post('line/unlink',{}).then(function(r){if(r.error)alert(r.error);location.reload()})},
  toggle:function(on){post('toggle',{enabled:on}).then(function(r){if(r.error)alert(r.error);location.reload()})}};})();
</script>
@endsection
