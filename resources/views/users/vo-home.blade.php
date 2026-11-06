@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
@php $h = (int) now('America/New_York')->format('G'); $greet = $h < 12 ? 'Good morning' : ($h < 18 ? 'Good afternoon' : 'Good evening');
 $tiles = [['/user/phone','Phone','Call from your browser','icon-phone','#4f46e5,#7c3aed'],['/user/fax','Fax','Send and receive faxes','icon-printer','#0891b2,#2563eb'],['/user/calls/inbound','Call history','Inbound, outbound, recorded','icon-list','#059669,#0d9488'],['/user/phone-numbers','Phone numbers','Where your numbers ring','icon-hash','#d97706,#ea580c'],['/user/extensions','Extensions','Lines, voicemail, forwarding','icon-users','#db2777,#9333ea'],['/user/greetings','Greetings','Your recorded greetings','icon-volume-2','#2563eb,#4f46e5'],['/user/business-listings','Business listings','Get found on maps and search','icon-map-pin','#16a34a,#0891b2'],['/user/marketing-form','Marketing','Plan your growth with us','icon-target','#7c3aed,#db2777']]; @endphp
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<style>
 .hm-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:18px}@media (max-width:900px){.hm-stats{grid-template-columns:repeat(2,1fr)}}
 .hm-stat{position:relative;overflow:hidden}.hm-stat .lx-ic{margin-bottom:10px}.hm-stat .v{font-size:30px;font-weight:700;letter-spacing:-.02em;line-height:1}.hm-stat .l{font-size:12.5px;color:#64748b;margin-top:6px}
 .hm-stat:after{content:"";position:absolute;right:-30px;top:-30px;width:100px;height:100px;border-radius:50%;background:var(--c);opacity:.08}
 .hm-tiles{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}@media (max-width:1100px){.hm-tiles{grid-template-columns:repeat(2,1fr)}}
 .gp-content a.hm-t{display:flex;flex-direction:column;gap:10px;padding:16px;border-radius:14px;color:#fff;text-decoration:none;background:linear-gradient(135deg,var(--g));position:relative;overflow:hidden;transition:transform .18s,box-shadow .18s;min-height:118px}
 .gp-content a.hm-t:hover{transform:translateY(-3px);box-shadow:0 14px 28px -12px rgba(15,23,42,.45);color:#fff}
 .hm-t:after{content:"";position:absolute;right:-24px;bottom:-24px;width:90px;height:90px;border-radius:50%;background:rgba(255,255,255,.14)}
 .hm-t .i{width:36px;height:36px;border-radius:10px;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;font-size:17px}
 .hm-t b{font-size:14.5px}.hm-t span{font-size:12px;opacity:.85}
 .hm-act{display:flex;gap:12px;align-items:center;padding:10px 0;border-top:1px solid #f1f5f9;font-size:13px}.hm-act:first-child{border-top:0}
 .hm-act .ai{width:32px;height:32px;border-radius:9px;display:flex;align-items:center;justify-content:center;flex:none}
 .hm-act .t{color:#64748b;font-size:12px;margin-left:auto;white-space:nowrap}
</style>
@php
 $gvdQuotes = [['The way to get started is to quit talking and begin doing.','Walt Disney'],['Your most unhappy customers are your greatest source of learning.','Bill Gates'],['The best way to predict the future is to create it.','Peter Drucker'],['Quality is not an act, it is a habit.','Aristotle'],['Success is not final, failure is not fatal: it is the courage to continue that counts.','Winston Churchill'],['If you are not willing to risk the usual, you will have to settle for the ordinary.','Jim Rohn'],["Opportunities don't happen. You create them.",'Chris Grosser'],["Don't be afraid to give up the good to go for the great.",'John D. Rockefeller'],["Whether you think you can or you think you can't, you're right.",'Henry Ford'],['Coming together is a beginning, staying together is progress, and working together is success.','Henry Ford'],["It always seems impossible until it's done.",'Nelson Mandela'],['Do what you do so well that they will want to see it again and bring their friends.','Walt Disney'],['Well done is better than well said.','Benjamin Franklin'],['An investment in knowledge pays the best interest.','Benjamin Franklin'],['The secret of getting ahead is getting started.','Mark Twain'],['Innovation distinguishes between a leader and a follower.','Steve Jobs'],['The only way to do great work is to love what you do.','Steve Jobs'],['Chase the vision, not the money; the money will end up following you.','Tony Hsieh'],["Your brand is what other people say about you when you're not in the room.",'Jeff Bezos'],['We see our customers as invited guests to a party, and we are the hosts.','Jeff Bezos'],['Price is what you pay. Value is what you get.','Warren Buffett'],['It takes 20 years to build a reputation and five minutes to ruin it.','Warren Buffett'],["Great things in business are never done by one person. They're done by a team of people.",'Steve Jobs'],['Done is better than perfect.','Sheryl Sandberg'],['The biggest risk is not taking any risk.','Mark Zuckerberg'],['Ideas are easy. Implementation is hard.','Guy Kawasaki'],['Make every detail perfect and limit the number of details to perfect.','Jack Dorsey'],['Small opportunities are often the beginning of great enterprises.','Demosthenes'],['Action is the foundational key to all success.','Pablo Picasso'],['Diligence is the mother of good luck.','Benjamin Franklin'],['Excellence is not a skill. It is an attitude.','Ralph Marston'],['Setting goals is the first step in turning the invisible into the visible.','Tony Robbins'],['Success usually comes to those who are too busy to be looking for it.','Henry David Thoreau'],['The harder I work, the luckier I get.','Samuel Goldwyn'],["Customers may forget what you said, but they'll never forget how you made them feel.",'Maya Angelou'],['Patience, persistence and perspiration make an unbeatable combination for success.','Napoleon Hill'],["Don't watch the clock; do what it does. Keep going.",'Sam Levenson'],['A goal without a plan is just a wish.','Antoine de Saint-Exupéry'],['Strive not to be a success, but rather to be of value.','Albert Einstein'],['Start where you are. Use what you have. Do what you can.','Arthur Ashe']];
 $gvdDay = now('America/New_York'); $gvdQ = $gvdQuotes[((int) $gvdDay->format('z') + 366 * (int) $gvdDay->format('Y')) % count($gvdQuotes)];
 $gvdRaw = trim((string) (session('nice_name') ?: ($u->display_name ?? '')));
 $gvdFirst = $gvdRaw !== '' ? \Illuminate\Support\Str::ucfirst(\Illuminate\Support\Str::lower(explode(' ', $gvdRaw)[0])) : '';
 if ($gvdFirst === '' || str_contains($gvdFirst, '@') || preg_match('/\d/', $gvdFirst) || in_array(strtolower($gvdFirst), ['customer','admin','user'], true)) $gvdFirst = '';
@endphp
<div class="lx">
 <div class="lx-hero lx-in"><div class="grid"></div>
  <div class="lx-eyebrow"><i class="feather icon-home"></i> Dashboard</div>
  <h1 class="gvd-greet"><span id="gvd-tod">{{ $greet }}</span>, <span class="gvd-name">{{ $gvdFirst !== '' ? $gvdFirst : 'there' }}</span></h1>
  <p>Everything for your business phone, fax and growth in one place.</p>
  <figure class="gvd-quote"><span class="gvd-qk">Today's thought</span><blockquote>&ldquo;{{ $gvdQ[0] }}&rdquo;</blockquote><figcaption>&mdash; {{ $gvdQ[1] }}</figcaption></figure>
  <style>/* navy banner 2026-11-01y (dashboard only) */
  .lx .lx-hero{background:radial-gradient(120% 140% at 100% 0%,rgba(255,80,0,.22) 0%,rgba(255,80,0,0) 45%),linear-gradient(135deg,#1E2A36 0%,#24384a 55%,#16202a 100%)!important;color:#fff;border-bottom:3px solid #FF5000}
  .lx .lx-hero .lx-eyebrow{color:#FF8A3D}.lx .lx-hero .lx-glass{border-color:rgba(255,255,255,.18)}.lx .lx-hero .lx-glass:hover{background:rgba(255,80,0,.22);border-color:#FF5000}
  .lx-hero .gvd-greet{font-family:Inter,system-ui,sans-serif;font-weight:800;letter-spacing:-.025em}.lx-hero .gvd-name{background:linear-gradient(90deg,#FF5000,#FF8A3D);-webkit-background-clip:text;background-clip:text;color:transparent}
  .gvd-quote{margin:18px 0 4px;max-width:720px;padding:14px 18px 14px 20px;border-left:3px solid #FF5000;border-radius:0 14px 14px 0;background:rgba(255,255,255,.08);backdrop-filter:blur(6px);font-family:Inter,system-ui,sans-serif}
  .gvd-quote .gvd-qk{display:block;font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#FF8A3D;margin-bottom:6px}
  .gvd-quote blockquote{margin:0;font-size:16px;line-height:1.5;font-weight:500;font-style:italic;color:inherit}.gvd-quote figcaption{margin-top:6px;font-size:13px;opacity:.75}</style>
  <script>(function(){var h=new Date().getHours(),e=document.getElementById('gvd-tod');if(e)e.textContent=h<12?'Good morning':(h<18?'Good afternoon':'Good evening');})();</script>
  <div class="row2"><a class="lx-glass" href="/user/phone" style="text-decoration:none"><i class="feather icon-phone-call"></i> Make a call</a><a class="lx-glass" href="/user/fax" style="text-decoration:none"><i class="feather icon-send"></i> Send a fax</a><a class="lx-glass" href="/user/calls/inbound" style="text-decoration:none"><i class="feather icon-clock"></i> Recent calls</a></div>
 </div>
 <div class="hm-stats">
  <div class="lx-card hm-stat lx-in" style="--c:#0891b2"><div class="lx-ic"><i class="feather icon-printer"></i></div><div class="v" data-count="{{ $faxWeek }}">{{ $faxWeek }}</div><div class="l">Faxes received, last 7 days</div></div>
  <div class="lx-card hm-stat lx-in lx-d1" style="--c:#4f46e5"><div class="lx-ic"><i class="feather icon-phone-incoming"></i></div><div class="v" id="hs-calls">–</div><div class="l">Calls, last 7 days</div></div>
  <div class="lx-card hm-stat lx-in lx-d2" style="--c:#db2777"><div class="lx-ic"><i class="feather icon-users"></i></div><div class="v" id="hs-ext">–</div><div class="l">Extensions</div></div>
  <div class="lx-card hm-stat lx-in lx-d3" style="--c:#d97706"><div class="lx-ic"><i class="feather icon-hash"></i></div><div class="v" id="hs-num">–</div><div class="l">Phone numbers</div></div>
 </div>
 {{-- welcome video card 2026-11-02vid --}}
 <div class="lx-card lx-in gvd-wv" id="gvd-wv" hidden>
  <style>.gvd-wv{display:grid;grid-template-columns:minmax(260px,1.1fr) 1fr;gap:22px;align-items:center;position:relative;border-top:3px solid #FF5000}
  .gvd-wv[hidden]{display:none}.gvd-wv .wv-media{position:relative;border-radius:14px;overflow:hidden;aspect-ratio:16/9;background:#1E2A36;box-shadow:0 12px 30px rgba(15,27,38,.18)}
  .gvd-wv .wv-media img,.gvd-wv .wv-media video{width:100%;height:100%;object-fit:cover;display:block}
  .gvd-wv .wv-play{position:absolute;inset:0;margin:auto;width:68px;height:68px;border-radius:50%;border:0;background:#FF5000;color:#fff;cursor:pointer;box-shadow:0 0 0 10px rgba(255,80,0,.25);transition:transform .2s}
  .gvd-wv .wv-play:hover{transform:scale(1.08)}.gvd-wv .wv-play svg{margin-left:4px}
  .gvd-wv .wv-k{font-size:11px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#FF5000}.gvd-wv h3{margin:6px 0 8px;font-weight:800;letter-spacing:-.02em}
  .gvd-wv p{margin:0 0 12px;color:#5b6b7a;line-height:1.55}.gvd-wv .wv-x{position:absolute;top:10px;right:12px;border:0;background:none;font-size:22px;line-height:1;color:#8a97a5;cursor:pointer}
  @media(max-width:760px){.gvd-wv{grid-template-columns:1fr}}</style>
  <button class="wv-x" type="button" aria-label="Dismiss welcome video" id="gvd-wv-x">&times;</button>
  <div class="wv-media" id="gvd-wv-m"><img src="/media/welcome/poster.jpg" alt="Welcome to Global Voice Direct" loading="lazy" width="1280" height="720"><button class="wv-play" type="button" id="gvd-wv-p" aria-label="Play welcome video"><svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg></button></div>
  <div><span class="wv-k">Welcome aboard</span><h3>Meet Global Voice Direct in 100 seconds</h3><p>See what's included in your account, the member perks, and what to expect in your first 30 days.</p><a class="lx-btn sec" href="/welcome-video" target="_blank" rel="noopener">Open full page</a></div>
 </div>
 <script>(function(){var c=document.getElementById('gvd-wv');if(!c)return;try{if(localStorage.getItem('gvdWvHide')==='1')return;}catch(e){}c.hidden=false;
 document.getElementById('gvd-wv-x').onclick=function(){c.hidden=true;try{localStorage.setItem('gvdWvHide','1');}catch(e){}};
 document.getElementById('gvd-wv-p').onclick=function(){var m=document.getElementById('gvd-wv-m'),v=window.matchMedia('(max-width:600px)').matches?'welcome-9x16':'welcome-16x9';
 m.innerHTML='<video controls autoplay playsinline preload="metadata" poster="/media/welcome/poster.jpg"><source src="/media/welcome/'+v+'.mp4" type="video/mp4"><track kind="captions" src="/media/welcome/welcome.vtt" srclang="en" label="English" default></video>';if(v==='welcome-9x16')m.style.aspectRatio='9/16';};})();</script>
 <div class="lx-cols">
  <div class="lx-card lx-in lx-d1">
   <div class="lx-ch"><div class="lx-ic"><i class="feather icon-grid"></i></div><div><h3>Quick actions</h3><div class="s">Jump straight to what you need.</div></div></div>
   <div class="hm-tiles">@foreach($tiles as $t)<a class="hm-t" href="{{ $t[0] }}" style="--g:{{ $t[4] }}"><span class="i"><i class="feather {{ $t[3] }}"></i></span><b>{{ $t[1] }}</b><span>{{ $t[2] }}</span></a>@endforeach</div>
  </div>
  <div class="lx-card lx-in lx-d2">
   <div class="lx-ch"><div class="lx-ic"><i class="feather icon-activity"></i></div><div><h3>Recent activity</h3><div class="s">Latest calls and faxes</div></div></div>
   <div id="hm-act">
    @foreach($recentFax as $f)<div class="hm-act" data-ts="{{ \Carbon\Carbon::parse($f->received_at, 'UTC')->timestamp }}"><span class="ai" style="background:#ecfeff;color:#0891b2"><i class="feather icon-printer"></i></span><div>Fax from <b>{{ $f->caller }}</b>@if($f->pages)<div class="lx-hint">{{ $f->pages }} {{ $f->pages == 1 ? 'page' : 'pages' }}</div>@endif</div><span class="t">{{ \Carbon\Carbon::parse($f->received_at, 'UTC')->setTimezone('America/New_York')->format('M j, g:i A') }}</span></div>@endforeach
    @if($recentFax->isEmpty())<div class="lx-empty" id="hm-empty"><svg width="96" height="72" viewBox="0 0 96 72" fill="none"><circle cx="48" cy="36" r="28" fill="#eef2ff"/><path d="M34 37h8l4-10 6 20 4-10h6" stroke="#4f46e5" stroke-width="3" fill="none" stroke-linecap="round" stroke-linejoin="round"/></svg>No recent activity yet.</div>@endif
   </div>
   <a href="/user/calls/inbound" class="lx-btn sec" style="width:100%;justify-content:center;margin-top:10px">View call history</a>
  </div>
 </div>
</div>
@push('scripts')<script>
(function(){
 function count(el,n){if(el==null)return;if(n==null){el.textContent='–';return;}var s=performance.now(),d=900;(function f(t){var p=Math.min(1,(t-s)/d);el.textContent=Math.round(n*(1-Math.pow(1-p,3)));if(p<1)requestAnimationFrame(f);})(s);}
 document.querySelectorAll('[data-count]').forEach(function(e){count(e,+e.dataset.count);});
 var esc=function(s){var d=document.createElement('div');d.textContent=s==null?'':s;return d.innerHTML;};
 fetch('/user/home/stats',{credentials:'same-origin',headers:{Accept:'application/json'}}).then(function(r){return r.json();}).then(function(j){
  if(!j.ok)return;
  count(document.getElementById('hs-calls'),j.calls_in==null&&j.calls_out==null?null:(j.calls_in||0)+(j.calls_out||0));
  count(document.getElementById('hs-ext'),j.extensions);count(document.getElementById('hs-num'),j.numbers);
  if(j.recent&&j.recent.length){var box=document.getElementById('hm-act'),em=document.getElementById('hm-empty');if(em)em.remove();
   box.insertAdjacentHTML('afterbegin',j.recent.map(function(c){var miss=/miss/i.test(c.status);return '<div class="hm-act"><span class="ai" style="background:'+(miss?'#fef2f2;color:#dc2626':'#eef2ff;color:#4f46e5')+'"><i class="feather icon-phone-'+(miss?'missed':'incoming')+'"></i></span><div>Call from <b>'+esc(c.from)+'</b><div class="lx-hint">'+esc(c.status)+(c.dur?' · '+esc(c.dur):'')+'</div></div><span class="t">'+esc(c.time)+'</span></div>';}).join(''));}
 },function(){});
})();
</script>@endpush
@endsection
