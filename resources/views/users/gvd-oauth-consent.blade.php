<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex,nofollow">
<title>{{ isset($error) ? 'Connection stopped' : 'Connect ' . $app }} | Global Voice Direct</title>
<style>
*{box-sizing:border-box}body{margin:0;min-height:100vh;font-family:Inter,system-ui,-apple-system,Segoe UI,sans-serif;color:#0f1b33;background:radial-gradient(1200px 600px at 10% -10%,#1d3a7a 0,transparent 60%),radial-gradient(900px 500px at 110% 110%,#ff7a1a33 0,transparent 60%),#0b1f44;display:flex;align-items:center;justify-content:center;padding:28px 14px}
.c{width:min(520px,100%);background:#fff;border-radius:20px;box-shadow:0 30px 80px rgba(0,0,0,.35);overflow:hidden}
.top{padding:26px 28px 18px;border-bottom:1px solid #eef1f7;text-align:center}.top img{height:40px}
.pair{display:flex;align-items:center;justify-content:center;gap:12px;margin:18px 0 6px}.av{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:20px;color:#fff;background:linear-gradient(135deg,#0b1f44,#24479a)}.av.o{background:linear-gradient(135deg,#ff7a1a,#ff9f4d)}.dots{color:#9aa5bd;letter-spacing:3px}
h1{font-size:21px;margin:10px 0 4px;line-height:1.3}.sub{color:#5a6785;font-size:14px;margin:0}.who{display:inline-block;margin-top:10px;font-size:12.5px;background:#f2f5fb;border-radius:20px;padding:4px 12px;color:#33415f}
.b{padding:20px 28px 24px}.lbl{font-size:12px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:#5a6785;margin:0 0 10px}
.s{display:flex;gap:12px;align-items:flex-start;padding:11px 12px;border:1px solid #eef1f7;border-radius:12px;margin-bottom:8px;cursor:pointer}.s:hover{border-color:#ffd6b8;background:#fffaf5}
.s svg{width:20px;height:20px;flex:none;stroke:#ff7a1a;margin-top:1px}.s b{display:block;font-size:14px}.s span{font-size:13px;color:#5a6785}.s input{margin:3px 0 0 auto;accent-color:#ff7a1a;width:17px;height:17px;flex:none}
.note{font-size:12.5px;color:#5a6785;background:#f7f9fc;border-radius:10px;padding:10px 12px;margin:14px 0 0;line-height:1.5}.warn{background:#fff8e6;border:1px solid #fde7a7;color:#6b4e00}
.act{display:flex;gap:10px;margin-top:18px}.btn{flex:1;border:0;border-radius:12px;padding:13px;font-weight:700;font-size:15px;cursor:pointer}.al{background:#ff7a1a;color:#fff;box-shadow:0 8px 20px rgba(255,122,26,.35)}.al:hover{background:#f06a08}.dn{background:#eef1f7;color:#0b1f44}
.ft{text-align:center;font-size:12px;color:#8a93a8;padding:0 28px 22px}.ft a{color:#5a6785}
</style></head><body>
<div class="c">
 <div class="top"><img src="/images/gvdlogo.png" alt="Global Voice Direct">
 @if(isset($error))
  <h1 style="margin-top:18px">Connection stopped</h1><p class="sub">{{ $error }}</p></div>
  <div class="ft" style="padding-top:20px"><a href="/user/ai-connect">Go to AI Connect</a> · Need help? Call <a href="tel:+18773085803">1-877-308-5803</a></div>
 @else
  <div class="pair"><div class="av">{{ mb_strtoupper(mb_substr($app, 0, 1)) }}</div><span class="dots">• • •</span><div class="av o">G</div></div>
  <h1>{{ $app }} wants to use your Global Voice Direct account</h1>
  <p class="sub">It will return to <b>{{ $host }}</b> when you're done.</p>
  @if($email)<span class="who">Signed in as {{ $email }}</span>@endif</div>
 <form class="b" method="post" action="/oauth/authorize">{{ csrf_field() }}<input type="hidden" name="nonce" value="{{ $nonce }}">
  <p class="lbl">Allow {{ $app }} to</p>
  @foreach($scopes as $s)
   <label class="s"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="{{ $groups[$s][1] ?? 'M12 2v20' }}"/></svg><div><b>{{ $groups[$s][0] ?? $s }}</b><span>{{ $labels[$s] ?? $s }}</span></div><input type="checkbox" name="scopes[]" value="{{ $s }}" checked></label>
  @endforeach
  <div class="note">Anything that sends an email, adds a contact, starts a sequence or places a call shows you a preview first and only happens after you confirm. You can see every action and disconnect this app anytime under <b>AI Connect</b> in your panel.</div>
  @if(!$enabled)<div class="note warn">AI Connect is turned off for your account. The app will connect, but it can't do anything until you turn AI Connect back on.</div>@endif
  <div class="act"><button class="btn dn" name="decision" value="deny">Deny</button><button class="btn al" name="decision" value="allow">Allow</button></div>
 </form>
 <div class="ft">Only allow apps you trust. Global Voice Direct never shares your password with {{ $app }}.</div>
 @endif
</div></body></html>
