@extends('layouts.gvd-v3')
@section('css')
<link rel="stylesheet" href="{{ asset('/css/gvd-v4.css') }}?v=20261101c">
@endsection
@section('content')
@verbatim

<div class="hero" style="min-height:720px;display:grid;place-items:center"><div class="aur"><i></i><i></i><i></i></div><div class="gridbg"></div><div class="wrap" style="display:grid;place-items:center">
<div class="gb login rv" style="width:100%;max-width:460px"><div class="ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="7.5" cy="15.5" r="5.5"/><path d="m21 2-9.6 9.6M15.5 7.5l3 3L22 7l-3-3"/></svg></div><h3>Forgot Password</h3>
@endverbatim
@if(request()->has("message"))<div class="gb card" style="padding:12px 16px;margin:0 0 14px;color:#1F6F87">{{ request()->get("message") }}</div>@endif
<form id="register-form" role="form" autocomplete="off" class="form" method="post" action="resetPassword">{{ csrf_field() }}
@verbatim
<label>Email:</label><div class="in f"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 6L2 7"/></svg><input id="email" type="email" name="email" placeholder="Enter Email" required></div><button type="submit" name="recover-submit" value="RESET PASSWORD" class="btn p lg" style="width:100%;justify-content:center;margin-top:22px;border:0;cursor:pointer;font:inherit;font-weight:600">Reset Password <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></button></form>
<p style="text-align:center;margin:20px 0 0;font-size:14px"><a href="https://app.globalvoicedirect.com/">← Go Back</a></p></div>
<p class="rv d2" style="color:var(--mut);font-size:14px;margin-top:26px">Need help? <a href="tel:+18773085803" data-gtm="click_phone">Call 1-877-308-5803</a></p></div></div>

@endverbatim
@endsection
