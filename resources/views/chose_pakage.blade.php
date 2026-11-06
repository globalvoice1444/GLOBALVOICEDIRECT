@extends('layouts.app')
@section('gvd_fa_css_only','1')
@section('css')
<script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src='https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);})(window,document,'script','dataLayer','GTM-KDJWX9X9');</script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=JetBrains+Mono:wght@600&display=swap" rel="stylesheet">
<style>
.gvdp{--ink:#1E2A36;--mut:#5B6875;--o:#FF5000;--o2:#C23A00;--b:#3BA9C8;--b2:#1F6F87;--c:#4BC1DB}
.gvdp *{box-sizing:border-box}
.gvdp{font-family:"Plus Jakarta Sans",system-ui,sans-serif;color:var(--ink);background:radial-gradient(900px 500px at 85% -10%,rgba(255,80,0,.10),transparent 60%),radial-gradient(800px 600px at -10% 30%,rgba(75,193,219,.16),transparent 60%),linear-gradient(#fff,#EEF5F9);overflow:hidden}
.gvdp .na{display:flex;gap:10px}
.gvdp .btn{display:inline-flex;align-items:center;gap:8px;padding:11px 18px;border-radius:12px;font-weight:700;font-size:14px;text-decoration:none}
.gvdp .gh{border:1px solid rgba(30,42,54,.15);background:#fff}
.gvdp .pri{background:linear-gradient(#FF6418,#C23A00);color:#fff;box-shadow:0 12px 24px -10px rgba(255,80,0,.6)}
.gvdp .hero{text-align:center;padding:70px 5vw 30px}
.gvdp .eb{font:600 12px/1 "JetBrains Mono",monospace;letter-spacing:.14em;text-transform:uppercase;color:var(--o2);display:inline-flex;gap:10px;align-items:center}
.gvdp .eb:before,.gvdp .eb:after{content:"";width:18px;height:1px;background:var(--o)}
.gvdp h1{font-size:clamp(36px,5.4vw,64px);line-height:1.02;letter-spacing:-.04em;margin:16px 0 14px;font-weight:800}
.gvdp h1 em{font-style:normal;background:linear-gradient(90deg,var(--o),#FF8A3D);-webkit-background-clip:text;color:transparent}
.gvdp .lead{color:var(--mut);font-size:18px;max-width:620px;margin:0 auto}
.gvdp .usp{display:inline-flex;align-items:center;gap:12px;margin-top:26px;padding:10px 18px 10px 10px;border-radius:999px;background:#fff;border:1px solid rgba(59,169,200,.35);box-shadow:0 14px 30px -18px rgba(31,111,135,.5);font-weight:600;font-size:15px}
.gvdp .usp b{background:linear-gradient(135deg,var(--b),var(--b2));color:#fff;border-radius:999px;padding:6px 12px;font-size:12px;letter-spacing:.08em}
.gvdp .grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;max-width:1180px;margin:40px auto 30px;padding:0 5vw;align-items:stretch;perspective:1200px}
.gvdp .gcard{position:relative;background:rgba(255,255,255,.9);border:1px solid rgba(30,42,54,.08);border-radius:26px;padding:30px 28px;display:flex;flex-direction:column;box-shadow:0 40px 70px -45px rgba(30,42,54,.45);transition:transform .4s cubic-bezier(.2,.7,.2,1),box-shadow .4s;animation:gup .8s both}
.gvdp .gcard:nth-child(2){animation-delay:.1s}
.gvdp .gcard:nth-child(3){animation-delay:.2s}
.gvdp .gcard:hover{transform:translateY(-8px) rotateX(2deg);box-shadow:0 50px 80px -40px rgba(30,42,54,.55)}
.gvdp .gcard.hot{background:linear-gradient(165deg,#fff 0%,#FFF4EC 100%);border:2px solid transparent;background-clip:padding-box;transform:scale(1.04);box-shadow:0 0 0 2px var(--o),0 50px 90px -40px rgba(255,80,0,.55)}
.gvdp .gcard.hot:hover{transform:scale(1.04) translateY(-8px)}
.gvdp .badge{align-self:flex-start;font:600 11px/1 "JetBrains Mono",monospace;letter-spacing:.14em;padding:8px 12px;border-radius:999px;background:rgba(59,169,200,.12);color:var(--b2)}
.gvdp .hot .badge{background:linear-gradient(#FF6418,#C23A00);color:#fff}
.gvdp .pr{font-size:56px;font-weight:800;letter-spacing:-.04em;margin:18px 0 6px;display:flex;align-items:flex-start;flex-wrap:wrap}
.gvdp .pr sup{font-size:24px;margin:10px 4px 0 0;color:var(--o)}
.gvdp .pr small{flex-basis:100%;font:600 11px/1 "JetBrains Mono",monospace;letter-spacing:.14em;color:var(--mut);margin-top:6px}
.gvdp ul{list-style:none;padding:18px 0 0;margin:18px 0 24px;border-top:1px dashed rgba(30,42,54,.15);display:grid;gap:11px;flex:1}
.gvdp li{display:flex;gap:10px;font-size:14.5px;color:#3A4652;line-height:1.4}
.gvdp li b{color:var(--ink)}
.gvdp li i{flex:none;width:20px;height:20px;border-radius:7px;display:grid;place-items:center;background:rgba(59,169,200,.15);color:var(--b2)}
.gvdp li i svg{width:12px}
.gvdp .hot li i{background:rgba(255,80,0,.12);color:var(--o2)}
.gvdp .buy{display:flex;justify-content:center;gap:10px;padding:15px;border-radius:14px;font-weight:800;text-decoration:none;background:var(--ink);color:#fff;transition:.25s}
.gvdp .buy span{transition:transform .25s}
.gvdp .buy:hover span{transform:translateX(5px)}
.gvdp .hot .buy{background:linear-gradient(#FF6418,#C23A00);box-shadow:0 16px 30px -12px rgba(255,80,0,.7)}
.gvdp .help{max-width:1180px;margin:20px auto 70px;padding:0 5vw}
.gvdp .hb{display:flex;align-items:center;justify-content:space-between;gap:20px;flex-wrap:wrap;padding:26px 30px;border-radius:24px;background:linear-gradient(120deg,#1F6F87,#3BA9C8);color:#fff;box-shadow:0 40px 70px -40px rgba(31,111,135,.8)}
.gvdp .hb h3{margin:0 0 4px;font-size:22px}
.gvdp .hb p{margin:0;opacity:.85}
.gvdp .hb .gh{background:rgba(255,255,255,.15);border-color:rgba(255,255,255,.4);color:#fff}
.gvdp .hb .pri{background:#fff;color:var(--o2)}
.gvdp .pitch{font-size:15px;line-height:1.5;color:#3A4652;margin:4px 0 0;font-weight:600}
.gvdp .hot .pitch{color:var(--o2)}
.gvdp .gcard:nth-child(3) .pitch{color:var(--b2)}
.gvdp .proof{max-width:900px;margin:0 auto 50px;padding:0 5vw;text-align:center;font-size:18px;font-weight:700}
@keyframes gup{from{opacity:0;transform:translateY(30px)}}
.gvdp .amt{display:inline-flex;align-items:flex-start;white-space:nowrap}.gvdp .amt sup{position:static;top:0;line-height:1;vertical-align:top}.gvdp h1{color:var(--ink)}.gvdp ul li:before{display:none}.gvdp .btn:hover,.gvdp .buy:hover{color:#fff;text-decoration:none}.gvdp .gh:hover{color:inherit}.gvdp .hb .gh:hover{color:#fff}.gvdp .hb .pri:hover{color:var(--o2)}
@media(max-width:900px){.gvdp .grid{grid-template-columns:1fr;gap:22px}.gvdp .gcard.hot,.gvdp .gcard.hot:hover{transform:none}.gvdp .gcard.hot{order:-1}.gvdp .hero{padding-top:44px}.gvdp .usp{font-size:13.5px;text-align:left}.gvdp .hb{padding:22px}.gvdp .hb .na{flex-wrap:wrap}}

</style>
@endsection
@section('content')
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-KDJWX9X9" height="0" width="0" style="display:none"></iframe></noscript>
<section class="header-wrap">
        <section class="page-title" style="background-image:url({{ asset('new_design/img/slider/inner-slider.jpg')}});">
            <div class="container">
                <h2>Price Plan</h2>
            </div>
        </section>
        @include('includes.top_header')
        @include('includes.navbar')
    </section>
<div class="gvdp">
<section class="hero"><div class="eb">Price Plan</div>
<h1>Every missed call is a customer <em>you paid for, gone.</em></h1>
<p class="lead">Get things done easily. Here is the reason why: every plan answers with an IVR auto attendant, rings your free mobile apps, sends VoiceMail and Fax-eMail to your inbox, and comes with 24 hours support and free activation. Pick your plan today and your next call reaches your business, not your voicemail.</p>
<div class="usp"><b>FAX INCLUDED</b> Every Global Voice Direct phone number is also a fax number.</div></section>
<p class="gvd-membership-note" style="max-width:1120px;margin:18px auto 0;padding:14px 18px;border-left:4px solid #FF5000;background:#fff7f2;border-radius:10px;font-size:15px;line-height:1.5;color:#1E2A36;box-sizing:border-box;">There is also a required <strong>$149 annual membership</strong>. You'll pay the membership first, then choose and pay for your plan. Your phone number is ordered only after checkout is complete.</p>
<section class="grid">
<article class="gcard"><div class="badge">MOST POPULAR</div><div class="pr"><span class="amt"><sup>$</sup>24.95</span><small>PER MONTH</small></div><p class="pitch">One business number, unlimited minutes, 10 extensions. Stop missing calls from your personal cell.</p><ul><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Local or Toll Free</b> 1 number</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Free Mobile Apps</b> Custom Greetings</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Minutes</b> unlimited minutes</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Extensions</b> 10 extensions</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Bandwidth</b> Unlimited</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>IVR</b> Auto attendant</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Call Forwarding</b> Call Screening</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>VoiceMail and Fax-eMail</b> Free Efax</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Inbound caller ID name</b> 411 Directory</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>24 Hours Support</b> Free Activation</span></li></ul>
<?php if(isset($_SESSION["user_id"])){ ?>
<a class="buy" data-gtm="click_signup" href="/users/connect/beta/?lang=en">Buy Now: claim this plan <span>&rarr;</span></a>
<?php }else{ ?>
<a class="buy" data-gtm="click_signup" href="/number/?package=standard&val=0">Buy Now: claim this plan <span>&rarr;</span></a>
<?php } ?></article>
<article class="gcard hot"><div class="badge">BEST VALUE</div><div class="pr"><span class="amt"><sup>$</sup>49.95</span><small>PER MONTH</small></div><p class="pitch">Two numbers and the full PBX. Your callers hear a real company, with music on hold and shared lines.</p><ul><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Local or Toll Free</b> 2 number</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Free Mobile Apps</b> Custom Greetings</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Free 1,500 mins</b> unlimited minutes</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Extensions</b> 10 extensions</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Full PBX Feature</b> Unlimited</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>IVR</b> Auto attendant</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Call Forwarding</b> Call Screening</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>VoiceMail and Fax-eMail</b> Free Efax</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Inbound caller ID name</b> 411 Directory</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Customization Music on Hold</b> Shared lines</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>24 Hours Support</b> Free Activation</span></li></ul>
<?php if(isset($_SESSION["user_id"])){ ?>
<a class="buy" data-gtm="click_signup" href="/users/connect/beta/?lang=en">Buy Now: claim this plan <span>&rarr;</span></a>
<?php }else{ ?>
<a class="buy" data-gtm="click_signup" href="/number/?package=premium&val=1">Buy Now: claim this plan <span>&rarr;</span></a>
<?php } ?></article>
<article class="gcard"><div class="badge">BEST SELLER</div><div class="pr"><span class="amt"><sup>$</sup>84.95</span><small>PER MONTH</small></div><p class="pitch">Three numbers, 10 extensions, unlimited minutes and music on hold and shared lines. Built for the owner who plans to be the biggest name in town, not the cheapest.</p><ul><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Local or Toll Free</b> 3 number</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Free Mobile Apps</b> Custom Greetings</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Minutes</b> unlimited minutes</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Extensions</b> 10 extensions</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Bandwidth</b> Unlimited</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>IVR</b> Auto attendant</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Call Forwarding</b> Call Screening</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>VoiceMail and Fax-eMail</b> Free Efax</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Inbound caller ID name</b> 411 Directory</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>Customization Music on Hold</b> Shared lines</span></li><li><i><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M5 12l5 5L20 7"/></svg></i><span><b>24 Hours Support</b> Free Activation</span></li></ul>
<?php if(isset($_SESSION["user_id"])){ ?>
<a class="buy" data-gtm="click_signup" href="/users/connect/beta/?lang=en">Buy Now: claim this plan <span>&rarr;</span></a>
<?php }else{ ?>
<a class="buy" data-gtm="click_signup" href="/number/?package=enterprise&val=2">Buy Now: claim this plan <span>&rarr;</span></a>
<?php } ?></article>
</section>
<section class="gvd-plan-videos" aria-label="See Global Voice Direct in action" style="max-width:1120px;margin:56px auto 0;padding:48px 24px;background:#f5f7fb;border-radius:20px;box-sizing:border-box;">
<p style="font-size:12px;font-weight:700;letter-spacing:.14em;text-transform:uppercase;color:#53657a;margin:0 0 10px;">Compare with confidence</p>
<h3 style="font-size:28px;font-weight:800;color:#0a2540;margin:0 0 28px;">See what every plan includes, in under a minute each</h3>
<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(min(100%,300px),1fr));gap:28px;align-items:start;">
@foreach([['CrHadPgp3Rw','Dashboard tour'],['N2Qk3NXmt3A','Phone numbers & call routing'],['t1KYJRNUEbg','Fax included with every number']] as $v)
<article style="min-width:0;text-align:left;"><h4 style="font-size:17px;font-weight:700;color:#0a2540;margin:0 0 12px;">{{ $v[1] }}</h4><div style="position:relative;width:100%;height:0;padding-bottom:56.25%;overflow:hidden;border-radius:14px;background:#0a2540;box-shadow:0 18px 44px rgba(10,37,64,.14);"><button type="button" class="gvd-yt" aria-label="Play video: Global Voice Direct: {{ $v[1] }}" data-yt="{{ $v[0] }}" data-title="Global Voice Direct: {{ $v[1] }}" style="position:absolute;inset:0;width:100%;height:100%;border:0;display:block;padding:0;cursor:pointer;background:#000;overflow:hidden;"><img src="/images/video-thumbs/{{ $v[0] }}.webp" alt="" width="1280" height="720" loading="lazy" decoding="async" style="width:100%;height:100%;object-fit:cover;display:block;"><svg aria-hidden="true" viewBox="0 0 68 48" width="68" height="48" style="position:absolute;left:50%;top:50%;transform:translate(-50%,-50%)"><path d="M66.5 7.7a8.5 8.5 0 0 0-6-6C55.3.3 34 .3 34 .3s-21.3 0-26.5 1.4a8.5 8.5 0 0 0-6 6C.1 12.9.1 24 .1 24s0 11.1 1.4 16.3a8.5 8.5 0 0 0 6 6C12.7 47.7 34 47.7 34 47.7s21.3 0 26.5-1.4a8.5 8.5 0 0 0 6-6C67.9 35.1 67.9 24 67.9 24s0-11.1-1.4-16.3z" fill="#f00"/><path d="M45 24 27 14v20" fill="#fff"/></svg></button></div></article>
@endforeach
</div>
<p style="margin:24px 0 0;font-size:15px;"><a href="https://globalvoicedirect.com/video-library/" style="color:#2448ba;font-weight:600;">Browse all how-to videos &rarr;</a></p>
</section>
<section class="help"><div class="hb"><div><h3>Tell us how you work. We'll tell you which plan fits.</h3><p>One short call. We listen first, then you choose.</p></div>
<div class="na"><a class="btn gh" data-gtm="click_chat" href="/contact-us">Chat with us now</a><a class="btn pri" data-gtm="click_phone" href="tel:+18773085803">Call now: 877 308 5803</a></div></div></section>
<section class="proof"><p>We are one of the largest wholesale voice termination suppliers in the world, with over 33 billion minutes terminated per year.</p></section>
</div>
    @include('includes.our_officials')
<script>/* GVD 2026-11-06 select_plan (passive) */document.addEventListener('click',function(e){try{var a=e.target.closest&&e.target.closest('a.buy');if(!a)return;var m=(a.getAttribute('href')||'').match(/[?&]package=(standard|premium|enterprise)\b/);window.dataLayer=window.dataLayer||[];window.dataLayer.push({event:'select_plan',funnel_step:'plan_selection',plan_tier:m?m[1]:'logged_in_user'});}catch(err){}},true);</script>
<script>/* GVD 2026-11-06 youtube facade */document.addEventListener('click',function(e){var b=e.target.closest&&e.target.closest('button.gvd-yt');if(!b)return;var f=document.createElement('iframe');f.src='https://www.youtube-nocookie.com/embed/'+b.getAttribute('data-yt')+'?autoplay=1';f.title=b.getAttribute('data-title');f.width=1280;f.height=720;f.referrerPolicy='strict-origin-when-cross-origin';f.allow='accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';f.allowFullscreen=true;f.style.cssText='position:absolute;inset:0;width:100%;height:100%;border:0;display:block;';b.replaceWith(f);});</script>
@endsection
