@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
@php $gi = ['icon-trending-up','icon-users','icon-star','icon-map-pin','icon-globe','icon-message-circle','icon-phone-call','icon-target','icon-award','icon-zap']; @endphp
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<div class="lx">
 <div class="lx-hero lx-in"><div class="grid"></div>
  <div class="lx-eyebrow"><i class="feather icon-target"></i> Marketing intake</div>
  <h1>Let's plan your growth</h1>
  <p>Tell us about your business and goals in a few short steps. Our marketing team reviews every request and reaches out with ideas for your business.</p>
  <div class="row2"><span class="lx-glass"><i class="feather icon-clock"></i> About 3 minutes</span><span class="lx-glass"><i class="feather icon-user-check"></i> Reviewed by our team</span><span class="lx-glass"><i class="feather icon-lock"></i> Your details stay private</span></div>
 </div>
 @if(session('bf_ok'))<div class="lx-alert ok lx-in"><i class="feather icon-check-circle"></i> {{ session('bf_ok') }}</div>@endif
 <div class="lx-cols">
  <div class="lx-card lx-in lx-d1">
   @if(isset($errors) && $errors->any())<div class="lx-alert err">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
   <ul class="lx-steps" data-lx-for="mf-form"><li>Contact</li><li>Goals</li><li>Budget &amp; timing</li><li>Details</li></ul>
   <div class="lx-bar" data-lx-bar="mf-form"><i></i></div>
   <form method="post" action="/user/marketing-form" id="mf-form" data-lx-steps>@csrf
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-user"></i></div><div><h3>Who we're talking to</h3><div class="s">So the right person on our team can reach you.</div></div></div>
     <div class="g2"><div class="lx-f"><label class="req">Contact name</label><input name="contact_name" maxlength="120" required value="{{ old('contact_name') }}"></div>
     <div class="lx-f"><label class="req">Business name</label><input name="business_name" maxlength="160" required value="{{ old('business_name', $u->display_name ?? '') }}"></div></div>
     <div class="g2"><div class="lx-f"><label class="req">Email</label><input type="email" name="email" maxlength="160" required value="{{ old('email', $u->user_email ?? '') }}"></div>
     <div class="lx-f"><label class="req">Phone</label><input type="tel" name="phone" maxlength="40" required value="{{ old('phone') }}"></div></div>
     <div class="g2"><div class="lx-f"><label>Website</label><input name="website" maxlength="200" placeholder="www.example.com" value="{{ old('website') }}"></div>
     <div class="lx-f"><label>Industry</label><select name="industry"><option value="">Select…</option>@foreach($industries as $i)<option @selected(old('industry')===$i)>{{ $i }}</option>@endforeach<option @selected(old('industry')==='Other')>Other</option></select></div></div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-flag"></i></div><div><h3>What do you want to achieve?</h3><div class="s">Pick all that apply.</div></div></div>
     <div class="lx-tiles">@foreach($goals as $k => $g)<label class="lx-tile"><input type="checkbox" name="goals[]" value="{{ $k }}" @checked(in_array($k, old('goals', []), true))><span class="ti"><i class="feather {{ $gi[$loop->index % count($gi)] }}"></i></span><span style="padding-right:18px">{{ $g }}</span><span class="ck">✓</span></label>@endforeach</div>
     <div class="lx-f"><label>Target area</label><input name="target_area" maxlength="200" placeholder="City, county or region" value="{{ old('target_area') }}"></div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-sliders"></i></div><div><h3>Budget &amp; timing</h3><div class="s">Optional. It helps us suggest the right plan.</div></div></div>
     <div class="lx-l" style="margin-bottom:8px">Budget range (optional)</div>
     <div class="lx-chips"><label class="lx-chip"><input type="radio" name="budget_range" value="" @checked(!old('budget_range'))><span>Prefer not to say</span></label>@foreach($budgets as $k => $b)<label class="lx-chip"><input type="radio" name="budget_range" value="{{ $k }}" @checked(old('budget_range')===$k)><span>{{ $b }}</span></label>@endforeach</div>
     <div class="lx-l" style="margin-bottom:8px">Best time to contact you</div>
     <div class="lx-chips"><label class="lx-chip"><input type="radio" name="contact_time" value="" @checked(!old('contact_time'))><span>Any time</span></label>@foreach($times as $t)<label class="lx-chip"><input type="radio" name="contact_time" value="{{ $t }}" @checked(old('contact_time')===$t)><span>{{ $t }}</span></label>@endforeach</div>
    </div>
    <div class="lx-step">
     <div class="lx-ch"><div class="lx-ic"><i class="feather icon-edit-3"></i></div><div><h3>Anything else?</h3><div class="s">Share what's working, what isn't, or a deadline.</div></div></div>
     <div class="lx-f"><label>Message</label><textarea name="message" rows="6" maxlength="4000" placeholder="Anything else we should know?">{{ old('message') }}</textarea></div>
    </div>
    <div class="lx-nav">
     <button type="button" class="lx-btn sec" data-lx-back><i class="feather icon-arrow-left"></i> Back</button>
     <div><button type="button" class="lx-btn pri" data-lx-next>Continue <i class="feather icon-arrow-right"></i></button><button class="lx-btn pri" data-lx-submit><i class="feather icon-send"></i> Submit request</button></div>
    </div>
   </form>
  </div>
  <div class="lx-sticky">
   <div class="lx-card lx-in lx-d2">
    <div class="lx-ch" style="margin-bottom:8px"><div class="lx-ic"><i class="feather icon-clipboard"></i></div><div><h3>Your request</h3><div class="s">Updates as you fill in the form.</div></div></div>
    <div class="lx-sum" id="mf-sum"></div>
   </div>
   <div class="lx-card lx-in lx-d3">
    <div class="lx-ch" style="margin-bottom:6px"><div class="lx-ic"><i class="feather icon-inbox"></i></div><div><h3>Your requests</h3></div></div>
    @forelse($past as $p)
     <div class="lx-item"><div style="display:flex;justify-content:space-between;gap:8px"><b style="font-size:13.5px">#{{ $p->id }} {{ $p->business_name }}</b><span class="lx-badge go">{{ ucfirst($p->status) }}</span></div><div class="lx-hint">{{ \Illuminate\Support\Carbon::parse($p->created_at)->format('M j, Y g:i A') }}@php $gs = json_decode($p->goals ?? '[]', true) ?: []; @endphp @if($gs) · {{ implode(', ', array_map(fn($k) => $goals[$k] ?? $k, $gs)) }}@endif</div></div>
    @empty
     <div class="lx-empty"><svg width="96" height="72" viewBox="0 0 96 72" fill="none"><rect x="14" y="8" width="60" height="56" rx="10" fill="#eef2ff"/><rect x="24" y="22" width="40" height="5" rx="2.5" fill="#c7d2fe"/><rect x="24" y="33" width="30" height="5" rx="2.5" fill="#c7d2fe"/><rect x="24" y="44" width="22" height="5" rx="2.5" fill="#c7d2fe"/><circle cx="72" cy="50" r="12" fill="#4f46e5"/><path d="M67 50l4 4 7-8" stroke="#fff" stroke-width="2.5" fill="none" stroke-linecap="round"/></svg>No requests yet. Your first one will show here.</div>
    @endforelse
   </div>
  </div>
 </div>
</div>
<script>
(function(){
 var F=document.getElementById('mf-form'),S=document.getElementById('mf-sum');
 var esc=function(s){var d=document.createElement('div');d.textContent=s;return d.innerHTML;};
 function val(n){var e=F.querySelector('[name="'+n+'"]');return e?e.value.trim():'';}
 function radio(n){var e=F.querySelector('[name="'+n+'"]:checked');return e?e.parentNode.textContent.trim():'';}
 function row(k,v){return '<div><b>'+k+'</b>'+(v?'<span>'+esc(v)+'</span>':'<span class="no">Not set</span>')+'</div>';}
 function u(){
  var g=[].map.call(F.querySelectorAll('[name="goals[]"]:checked'),function(x){return x.closest('label').textContent.replace('✓','').trim();});
  S.innerHTML=row('Business',val('business_name'))+row('Contact',val('contact_name'))+row('Industry',val('industry'))+row('Goals',g.join(', '))+row('Target area',val('target_area'))+row('Budget',radio('budget_range'))+row('Best time',radio('contact_time'));
 }
 F.addEventListener('input',u);F.addEventListener('change',u);u();
})();
</script>
<script src="/js/gvd-stepper.js?v=1"></script>
@include('users.partials.vo-foot', ['page' => 'marketingform'])
@endsection
