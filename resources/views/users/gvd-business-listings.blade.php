@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
@php use App\Support\BusinessListings as BL; $E = $edit; $hv = $E ? (json_decode($E->hours, true) ?: []) : []; $xc = $E ? (json_decode($E->extra_categories, true) ?: []) : [];
 $f = fn($k, $d = '') => old($k, $E->$k ?? $d);
 $st = ['new'=>'Received','bing_queued'=>'Queued for Bing','bing_uploaded'=>'Sent to Bing','guide_sent'=>'Guide sent'];
 $latest = $list[0] ?? null; @endphp
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<div class="lx">
 <div class="lx-hero lx-in"><div class="grid"></div>
  <div class="lx-eyebrow"><i class="feather icon-map-pin"></i> Business listings</div>
  <h1>Get found where your customers search</h1>
  <p>One set of business details, ready for Bing, Google, Apple Maps and Facebook. We submit your free Bing Places listing for you and give you a step-by-step guide for the others.</p>
  <div class="row2"><span class="lx-glass"><i class="feather icon-check-circle"></i> Free Bing Places submission</span><span class="lx-glass"><i class="feather icon-book-open"></i> Printable guides</span><span class="lx-glass"><i class="feather icon-phone"></i> Your phone number is your listing ID</span></div>
 </div>
 @if($done)
 <div class="lx-alert ok lx-in"><b>Thanks, {{ $done->contact_name }}!</b> We received the listing details for {{ $done->business_name }} ({{ BL::prettyPhone($done->phone) }}). We'll prepare your free Bing Places listing; Bing will send you a code to confirm it.
  <div style="margin-top:10px"><a class="lx-btn pri" href="/user/business-listings/{{ $done->id }}/guide" target="_blank" rel="noopener"><i class="feather icon-external-link"></i> Open your Google, Apple &amp; Facebook guide (printable)</a></div></div>
 @endif
 <div class="lx-cols">
  <div class="lx-card lx-in lx-d1">
   @if(isset($errors) && $errors->any())<div class="lx-alert err"><b>Please fix:</b>@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
   <ul class="lx-steps" data-lx-for="bl-form"><li>Business</li><li>Location</li><li>Categories</li><li>Hours</li><li>Confirm</li></ul>
   <div class="lx-bar" data-lx-bar="bl-form"><i></i></div>
   <form method="post" action="/user/business-listings" id="bl-form" data-lx-steps>@csrf
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-briefcase"></i></div><div><h3>Your business</h3><div class="s">How your name and contact details appear on maps and search.</div></div></div>
     <div class="lx-f"><label class="req">Business name</label><input name="business_name" maxlength="200" required value="{{ $f('business_name') }}"></div>
     <div class="g2"><div class="lx-f"><label class="req">Main business phone <small>(your listing ID)</small></label><input type="tel" name="phone" maxlength="30" required value="{{ old('phone', $E ? BL::prettyPhone($E->phone) : '') }}"></div><div class="lx-f"><label>Website</label><input name="website" maxlength="255" placeholder="https://" value="{{ $f('website') }}"></div></div>
     <div class="lx-f"><label class="req">Email</label><input type="email" name="email" maxlength="190" required value="{{ $f('email') }}"></div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-map"></i></div><div><h3>Location</h3><div class="s">Use the exact address customers visit or mail to.</div></div></div>
     <div class="g2" style="grid-template-columns:2fr 1fr"><div class="lx-f"><label class="req">Street address</label><input name="street" maxlength="200" required value="{{ $f('street') }}"></div><div class="lx-f"><label>Suite / unit</label><input name="suite" maxlength="100" value="{{ $f('suite') }}"></div></div>
     <div class="g4" style="grid-template-columns:2fr 1fr 1fr 1fr"><div class="lx-f"><label class="req">City</label><input name="city" maxlength="100" required value="{{ $f('city') }}"></div><div class="lx-f"><label class="req">State</label><input name="state" maxlength="50" required placeholder="FL" value="{{ $f('state') }}"></div><div class="lx-f"><label class="req">ZIP</label><input name="zip" maxlength="10" required value="{{ $f('zip') }}"></div><div class="lx-f"><label>Country</label><input name="country" maxlength="2" value="{{ $f('country', 'US') }}"></div></div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-tag"></i></div><div><h3>Categories &amp; description</h3><div class="s">Categories decide which searches you show up in.</div></div></div>
     <div class="lx-f"><label class="req">Primary category</label><input name="category" maxlength="150" required placeholder="e.g. Electrician" value="{{ $f('category') }}"></div>
     <div class="g4">@for($i = 1; $i <= 4; $i++)<div class="lx-f"><label>Extra category {{ $i }}</label><input name="cat{{ $i }}" maxlength="150" value="{{ old('cat'.$i, $xc[$i-1] ?? '') }}"></div>@endfor</div>
     <div class="lx-f"><label>Description <small>(max 750 characters)</small> <span class="lx-hint" id="bl-cnt"></span></label><textarea name="description" id="bl-desc" maxlength="750" rows="5">{{ $f('description') }}</textarea></div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-clock"></i></div><div><h3>Opening hours</h3><div class="s">Shown as open or closed to people searching right now.</div></div></div>
     <table class="lx-hrs">@foreach($days as $k => $day) @php $we = in_array($k, ['sat','sun']); $x = $hv[$k] ?? null; $closed = old() ? (bool) old("closed_$k") : ($x ? !empty($x['closed']) : $we); @endphp
      <tr><th>{{ $day }}</th><td><input type="time" name="open_{{ $k }}" value="{{ old("open_$k", $x['open'] ?? ($we ? '' : '09:00')) }}"></td><td><input type="time" name="close_{{ $k }}" value="{{ old("close_$k", $x['close'] ?? ($we ? '' : '17:00')) }}"></td><td><label class="cl"><input type="checkbox" name="closed_{{ $k }}" value="1" @checked($closed)> Closed</label></td></tr>
     @endforeach</table>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-shield"></i></div><div><h3>Confirm &amp; submit</h3><div class="s">Check the preview, then send it to us.</div></div></div>
     <div class="lx-f"><label class="req">Your name</label><input name="contact_name" maxlength="150" required value="{{ $f('contact_name') }}"></div>
     <label style="display:flex;gap:10px;align-items:flex-start;color:#334155;font-size:13.5px;margin:6px 0 4px"><input type="checkbox" name="consent" value="1" required style="margin-top:2px"> I confirm I'm authorized to list this business and agree Global Voice Direct may prepare listing data for it.</label>
     <div class="lx-hint" style="margin-top:6px">Submitting again with the same phone number updates that listing.</div>
    </div>
    <div class="lx-nav">
     <div><button type="button" class="lx-btn sec" data-lx-back><i class="feather icon-arrow-left"></i> Back</button>@if($E) <a href="/user/business-listings" class="lx-btn sec">Cancel</a>@endif</div>
     <div><button type="button" class="lx-btn pri" data-lx-next>Continue <i class="feather icon-arrow-right"></i></button><button class="lx-btn pri" data-lx-submit><i class="feather icon-send"></i> {{ $E ? 'Update my listing' : 'Get my business listings plan' }}</button></div>
    </div>
   </form>
  </div>
  <div class="lx-sticky">
   <div class="lx-card lx-in lx-d2" style="padding:14px">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px"><b style="font-size:13.5px">Live preview</b><span class="lx-tag">How it can look</span></div>
    <div class="lx-prev"><div class="lx-map"><span class="lx-pin"></span></div>
     <div class="lx-pb"><div class="n" id="pv-name"></div><div class="c" id="pv-cat"></div>
      <div class="r"><i class="feather icon-clock"></i><span id="pv-hrs"></span></div>
      <div class="r"><i class="feather icon-map-pin"></i><span id="pv-addr"></span></div>
      <div class="r"><i class="feather icon-phone"></i><span id="pv-ph"></span></div>
      <div class="r" id="pv-webr"><i class="feather icon-globe"></i><span id="pv-web"></span></div>
      <div class="r" id="pv-descr" style="color:#64748b;font-size:12.5px"><span id="pv-desc"></span></div>
      <div class="lx-acts"><span>Directions</span><span>Call</span><span>Website</span></div>
     </div></div>
    <div class="lx-hint" style="margin-top:8px">Preview only. Each platform shows listings in its own layout.</div>
   </div>
   <div class="lx-card lx-in lx-d3">
    <div class="lx-ch" style="margin-bottom:10px"><div class="lx-ic"><i class="feather icon-radio"></i></div><div><h3>Platform status</h3><div class="s">{{ $latest ? $latest->business_name : 'Submit your details to start' }}</div></div></div>
    @php $bing = !$latest ? ['Not started',''] : (in_array($latest->status, ['bing_uploaded','guide_sent']) ? ['Sent to Bing','ok'] : ($latest->status === 'bing_queued' ? ['Queued','go'] : ['Received','wait']));
         $self = !$latest ? ['Not started',''] : ($latest->status === 'guide_sent' ? ['Guide sent','go'] : ['Guide ready','go']); @endphp
    <div class="lx-plat">
     <div>Bing Places<span class="lx-badge {{ $bing[1] }}">{{ $bing[0] }}</span></div>
     <div>Google<span class="lx-badge {{ $self[1] }}">{{ $self[0] }}</span></div>
     <div>Apple Maps<span class="lx-badge {{ $self[1] }}">{{ $self[0] }}</span></div>
     <div>Facebook<span class="lx-badge {{ $self[1] }}">{{ $self[0] }}</span></div>
    </div>
    @if($latest)<a class="lx-btn sec" style="width:100%;justify-content:center;margin-top:12px" href="/user/business-listings/{{ $latest->id }}/guide" target="_blank" rel="noopener"><i class="feather icon-book-open"></i> Open the step-by-step guide</a>@endif
    <div class="lx-hint" style="margin-top:8px">We submit Bing for you. Google, Apple Maps and Facebook are verified by you with the guide.</div>
   </div>
   <div class="lx-card lx-in lx-d3">
    <div class="lx-ch" style="margin-bottom:6px"><div class="lx-ic"><i class="feather icon-layers"></i></div><div><h3>Your listings</h3></div></div>
    @forelse($list as $p)
     <div class="lx-item"><div style="display:flex;justify-content:space-between;gap:8px;align-items:flex-start"><b style="font-size:13.5px">{{ $p->business_name }}</b><span class="lx-badge go">{{ $st[$p->status] ?? $p->status }}</span></div>
      <div class="lx-hint">{{ BL::prettyPhone($p->phone) }} · {{ $p->city }}, {{ $p->state }} · updated {{ \Illuminate\Support\Carbon::parse($p->updated_at)->format('M j, Y') }}</div>
      <div style="font-size:12.5px;margin-top:4px"><a href="/user/business-listings/{{ $p->id }}/guide" target="_blank" rel="noopener">Guide</a> · <a href="/user/business-listings?edit={{ $p->id }}">Edit</a></div></div>
    @empty
     <div class="lx-empty"><svg width="96" height="72" viewBox="0 0 96 72" fill="none"><rect x="8" y="10" width="80" height="54" rx="10" fill="#eef2ff"/><path d="M20 50l14-16 10 10 12-14 20 20" stroke="#a5b4fc" stroke-width="3" fill="none" stroke-linecap="round"/><circle cx="48" cy="26" r="9" fill="#4f46e5"/><circle cx="48" cy="26" r="3.5" fill="#fff"/></svg>No listings yet. Your first one takes about five minutes.</div>
    @endforelse
   </div>
  </div>
 </div>
</div>
<script>
(function(){
 var F=document.getElementById('bl-form'),q=function(n){return F.querySelector('[name="'+n+'"]');},t=document.getElementById('bl-desc'),c=document.getElementById('bl-cnt');
 function u(){c.textContent='('+t.value.length+'/750)';}t.addEventListener('input',u);u();
 var keys=['sun','mon','tue','wed','thu','fri','sat'];
 function fmt(v){if(!v)return'';var p=v.split(':'),h=+p[0],m=p[1];return((h%12)||12)+(m!=='00'?':'+m:'')+(h<12?' AM':' PM');}
 function set(id,v,ph){var e=document.getElementById(id);e.textContent=v||ph||'';e.style.color=v?'':'#94a3b8';}
 function pv(){
  set('pv-name',q('business_name').value.trim(),'Your business name');
  var cats=[q('category').value.trim()].concat([1,2].map(function(i){return q('cat'+i).value.trim();})).filter(Boolean);set('pv-cat',cats.join(' · '),'Primary category');
  var a=[q('street').value.trim()+(q('suite').value.trim()?', '+q('suite').value.trim():''),q('city').value.trim(),[q('state').value.trim(),q('zip').value.trim()].filter(Boolean).join(' ')].filter(function(x){return x&&x!==', ';});set('pv-addr',a.join(', '),'Street, city, state ZIP');
  set('pv-ph',q('phone').value.trim(),'Main business phone');
  var w=q('website').value.trim();document.getElementById('pv-webr').style.display=w?'':'none';set('pv-web',w.replace(/^https?:\/\//,''));
  var d=t.value.trim();document.getElementById('pv-descr').style.display=d?'':'none';set('pv-desc',d.length>140?d.slice(0,140)+'…':d);
  var k=keys[new Date().getDay()],cl=F.querySelector('[name="closed_'+k+'"]'),o=q('open_'+k),e=q('close_'+k),h=document.getElementById('pv-hrs');
  if(!o){h.textContent='';return;} h.innerHTML=(cl&&cl.checked)||!o.value?'<span class="closed">Closed today</span>':'<span class="open">Open today</span> · '+fmt(o.value)+' – '+fmt(e.value);
 }
 F.addEventListener('input',pv);F.addEventListener('change',pv);pv();
})();
</script>
<script src="/js/gvd-stepper.js?v=1"></script>
@include('users.partials.vo-foot', ['page' => 'businesslistings'])
@endsection
