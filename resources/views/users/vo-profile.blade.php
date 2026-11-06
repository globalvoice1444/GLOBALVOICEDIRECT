@extends('layouts.user.user_main')
@section('content')
@php
 $biz = $sections['Business'] ?? []; $acc = $sections['Account'] ?? [];
 $title = $biz['Company'] ?? ($biz['Customer name'] ?? ($fallbackName ?: 'Your account'));
 $status = $acc['Status'] ?? null;
 $icons = ['Business' => 'icon-briefcase', 'Contact' => 'icon-phone', 'Address' => 'icon-map-pin', 'Account' => 'icon-credit-card'];
 $pct = $credit['limit'] > 0 ? max(0, min(1, $credit['available'] / $credit['limit'])) : 0; $C = 2 * M_PI * 54;
@endphp
<link rel="stylesheet" href="/css/gvd-lux.css?v=4">
<div class="lx">
 <div class="lx-hero lx-in"><div class="grid"></div>
  <div style="display:flex;gap:18px;align-items:center;flex-wrap:wrap">
   <div style="width:64px;height:64px;border-radius:18px;background:rgba(255,255,255,.16);border:1px solid rgba(255,255,255,.3);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:700;backdrop-filter:blur(8px)">{{ mb_strtoupper(mb_substr($title, 0, 1)) }}</div>
   <div style="min-width:0"><div class="lx-eyebrow"><i class="feather icon-user"></i> Profile</div><h1 style="margin-bottom:6px">{{ $title }}</h1>
    <div class="row2" style="margin-top:0">
     @if($status)<span class="lx-glass"><span class="lx-dot {{ preg_match('/^(open|active|o)$/i', $status) ? '' : 'off' }}"></span> Status: {{ $status }}</span>@endif
     @if(!empty($acc['Account ID']))<span class="lx-glass"><i class="feather icon-hash"></i> Account {{ $acc['Account ID'] }}</span>@endif
     @if(!empty($acc['Customer since']))<span class="lx-glass"><i class="feather icon-calendar"></i> Since {{ $acc['Customer since'] }}</span>@endif
    </div></div>
  </div>
 </div>
 <div class="lx-cols">
  <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;align-content:start">
  @if ($error)
   <div class="lx-card lx-in" style="grid-column:1/-1"><div class="lx-ch"><div class="lx-ic"><i class="feather icon-lock"></i></div><div><h3>Account details</h3><div class="s">{{ $error }}</div></div></div>
    <div class="lx-empty"><svg width="110" height="80" viewBox="0 0 110 80" fill="none"><rect x="15" y="12" width="80" height="56" rx="12" fill="#eef2ff"/><circle cx="40" cy="36" r="10" fill="#c7d2fe"/><rect x="56" y="30" width="28" height="5" rx="2.5" fill="#c7d2fe"/><rect x="56" y="40" width="20" height="5" rx="2.5" fill="#c7d2fe"/><rect x="28" y="52" width="54" height="5" rx="2.5" fill="#e0e7ff"/></svg>Your details appear here after you sign in.</div></div>
  @else
   @foreach ($sections as $t => $rows)
   <div class="lx-card lx-in lx-d{{ min(3, $loop->index + 1) }}"><div class="lx-ch"><div class="lx-ic"><i class="feather {{ $icons[$t] ?? 'icon-info' }}"></i></div><div><h3>{{ $t }}</h3></div></div>
    <div class="lx-sum">@foreach ($rows as $k => $v)<div><b>{{ $k }}</b><span>{{ $v }}</span></div>@endforeach</div></div>
   @endforeach
   <div class="lx-hint" style="grid-column:1/-1">To change these details, contact Global Voice Direct support.</div>
  @endif
  </div>
  {{-- STATIC credit line card (owner, 2026-11-01): limit fixed at $7,500, terms fixed; to be made dynamic later. --}}
  <div class="lx-card lx-in lx-d2" data-static="credit-line">
   <div class="lx-ch"><div class="lx-ic"><i class="feather icon-trending-up"></i></div><div><h3>Credit line</h3><div class="s">Available of ${{ number_format($credit['limit'], 0) }}</div></div></div>
   <div class="lx-gauge" style="justify-content:center">
    <svg width="150" height="150" viewBox="0 0 130 130"><defs><linearGradient id="lxg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#4f46e5"/><stop offset="1" stop-color="#818cf8"/></linearGradient></defs>
     <circle cx="65" cy="65" r="54" fill="none" stroke="#eef2ff" stroke-width="12"/>
     <circle class="arc" id="lx-arc" cx="65" cy="65" r="54" fill="none" stroke="url(#lxg)" stroke-width="12" stroke-linecap="round" transform="rotate(-90 65 65)" stroke-dasharray="{{ $C }}" stroke-dashoffset="{{ $C }}" data-to="{{ $C * (1 - $pct) }}"/>
     <text x="65" y="62" text-anchor="middle" font-size="19" font-weight="700" fill="#0f172a">${{ number_format($credit['available'], 0) }}</text><text x="65" y="80" text-anchor="middle" font-size="10" fill="#64748b">available</text></svg>
   </div>
   <div class="lx-kv" style="margin-top:12px">
    <div><div class="k">Credit limit</div><div class="v">${{ number_format($credit['limit'], 2) }}</div></div>
    <div><div class="k">Balance</div><div class="v">${{ number_format($credit['balance'], 2) }}</div></div>
    <div><div class="k">Available</div><div class="v">${{ number_format($credit['available'], 2) }}</div></div>
    <div><div class="k">Finance terms</div><div class="v">{{ $credit['terms'] }}</div></div>
   </div>
  </div>
 </div>
</div>
<script>requestAnimationFrame(function(){setTimeout(function(){var a=document.getElementById('lx-arc');a.style.strokeDashoffset=a.dataset.to;},120);});</script>
@endsection
