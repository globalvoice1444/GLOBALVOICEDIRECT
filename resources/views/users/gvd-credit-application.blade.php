@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>.gvd-bf input,.gvd-bf select,.gvd-bf textarea{width:100%;padding:7px 9px;border:1px solid #ccc;border-radius:4px}.gvd-bf label{font-size:12px;color:#666;margin:10px 0 4px;display:block}.gvd-bf .req:after{content:" *";color:#e74c3c}.gvd-bf h6{margin:18px 0 0;font-weight:600;border-bottom:1px solid #eee;padding-bottom:6px}.gvd-bf .pill{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;background:#eef2ff;color:#4338ca}</style>
<link rel="stylesheet" href="/css/gvd-lux.css?v=4"><div class="lx"><div class="lx-hero lx-in"><div class="grid"></div><div class="lx-eyebrow"><i class="feather icon-shield"></i> Credit application</div><h1>Apply for a business credit line</h1><p>A short, secure application reviewed by our team. We never ask for bank account numbers, Social Security numbers or dates of birth on this form.</p><div class="row2"><span class="lx-glass"><i class="feather icon-lock"></i> Private and secure</span><span class="lx-glass"><i class="feather icon-user-check"></i> Reviewed by our team</span></div></div></div>
<div class="row gvd-vo gvd-bf">
 <div class="col-lg-8"><div class="card"><div class="card-block">
  @if(session('bf_ok'))<div class="alert alert-success">{{ session('bf_ok') }}</div>@endif
  @if(isset($errors) && $errors->any())<div class="alert alert-danger">@foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>@endif
  <form method="post" action="/user/credit-application" autocomplete="off">@csrf
   <h6>Business</h6>
   <div class="row"><div class="col-sm-6"><label class="req">Legal business name</label><input name="legal_name" maxlength="160" required value="{{ old('legal_name', $u->display_name ?? '') }}"></div>
   <div class="col-sm-6"><label>DBA (doing business as)</label><input name="dba" maxlength="160" value="{{ old('dba') }}"></div></div>
   <div class="row"><div class="col-sm-4"><label class="req">Entity type</label><select name="entity_type" required><option value="">Select…</option>@foreach($entities as $e)<option @selected(old('entity_type')===$e)>{{ $e }}</option>@endforeach</select></div>
   <div class="col-sm-4"><label class="req">Years in business</label><input type="number" min="0" max="300" name="years_in_business" required value="{{ old('years_in_business') }}"></div>
   <div class="col-sm-4"><label>EIN (optional)</label><input name="ein" maxlength="10" placeholder="12-3456789" inputmode="numeric" value=""></div></div>
   <label class="req">Business address</label><input name="address" maxlength="300" required placeholder="Street, city, state, ZIP" value="{{ old('address') }}">
   <div class="row"><div class="col-sm-6"><label class="req">Business phone</label><input type="tel" name="phone" maxlength="40" required value="{{ old('phone') }}"></div>
   <div class="col-sm-6"><label class="req">Business email</label><input type="email" name="email" maxlength="160" required value="{{ old('email', $u->user_email ?? '') }}"></div></div>
   <h6>Applicant</h6>
   <div class="row"><div class="col-sm-6"><label class="req">Owner / applicant name</label><input name="applicant_name" maxlength="120" required value="{{ old('applicant_name') }}"></div>
   <div class="col-sm-6"><label class="req">Title</label><input name="applicant_title" maxlength="80" required placeholder="Owner, CEO, Office manager…" value="{{ old('applicant_title') }}"></div></div>
   <h6>Credit request</h6>
   <div class="row"><div class="col-sm-6"><label class="req">Requested credit amount (USD)</label><input type="number" min="1" name="requested_amount" required value="{{ old('requested_amount') }}"></div>
   <div class="col-sm-6"><label class="req">Estimated monthly usage (USD)</label><input type="number" min="0" name="monthly_usage" required value="{{ old('monthly_usage') }}"></div></div>
   <h6>Trade references</h6>
   <div class="row"><div class="col-sm-6"><label class="req">Reference 1 – name</label><input name="ref1_name" maxlength="120" required value="{{ old('ref1_name') }}"></div><div class="col-sm-6"><label class="req">Reference 1 – phone</label><input type="tel" name="ref1_phone" maxlength="40" required value="{{ old('ref1_phone') }}"></div></div>
   <div class="row"><div class="col-sm-6"><label class="req">Reference 2 – name</label><input name="ref2_name" maxlength="120" required value="{{ old('ref2_name') }}"></div><div class="col-sm-6"><label class="req">Reference 2 – phone</label><input type="tel" name="ref2_phone" maxlength="40" required value="{{ old('ref2_phone') }}"></div></div>
   <h6>Bank</h6>
   <label class="req">Bank name</label><input name="bank_name" maxlength="120" required placeholder="Bank name only – no account or routing numbers" value="{{ old('bank_name') }}">
   <h6>Agreement</h6>
   <label style="display:flex;gap:8px;align-items:flex-start;color:#333;font-size:13px"><input type="checkbox" name="agreed" value="1" required style="width:auto;margin-top:3px" @checked(old('agreed'))> I certify that the information on this application is true and complete, and I authorize Global Voice Direct to verify it, including contacting the trade references listed.</label>
   <div class="row"><div class="col-sm-8"><label class="req">Signature (type your full name)</label><input name="signature" maxlength="120" required value="{{ old('signature') }}"></div>
   <div class="col-sm-4"><label class="req">Date</label><input type="date" name="signed_date" required value="{{ old('signed_date', now()->toDateString()) }}"></div></div>
   <div style="margin-top:14px"><button class="btn btn-primary">Submit application</button></div>
  </form>
 </div></div></div>
 <div class="col-lg-4"><div class="card"><div class="card-header"><h5>Your applications</h5></div><div class="card-block">
  @forelse($past as $p)
   <div style="padding:8px 0;border-bottom:1px solid #f0f0f0"><b>#{{ $p->id }}</b> {{ $p->legal_name }} <span class="pill">{{ ucfirst($p->status) }}</span><div class="muted" style="font-size:12px">{{ \Illuminate\Support\Carbon::parse($p->created_at)->format('M j, Y g:i A') }} · requested ${{ number_format($p->requested_amount) }}</div></div>
  @empty @if(empty($legacy) || !count($legacy))<div class="muted">No applications yet.</div>@endif @endforelse
  @foreach(($legacy ?? []) as $p)
   <div style="padding:8px 0;border-bottom:1px solid #f0f0f0"><b>Earlier application</b> {{ $p->buisness_name }} <span class="pill" style="background:#f1f5f9;color:#475569">On file</span><div class="muted" style="font-size:12px">{{ \Illuminate\Support\Carbon::parse($p->created_at)->format('M j, Y') }}@if($p->taxid) · Tax ID {{ \App\Http\Controllers\AdminBizFormsController::mask($p->taxid) }}@endif</div></div>
  @endforeach
 </div></div></div>
</div>
@include('users.partials.vo-foot', ['page' => 'creditapp'])
@endsection
