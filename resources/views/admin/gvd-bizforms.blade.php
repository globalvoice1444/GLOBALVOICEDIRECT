@extends('admin.layouts.layout')
@section('css')<style>.gvd-bfa .pill{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;background:#eef2ff;color:#4338ca}.gvd-bfa th{white-space:nowrap}.gvd-bfa dl{display:grid;grid-template-columns:220px 1fr;gap:6px 12px}.gvd-bfa dt{color:#777;font-weight:400}</style>@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gvd-bfa">
 <div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
  <h5>{{ $t['title'] }}@if($row) – #{{ $row->id }}@elseif(!empty($legacy)) – legacy #{{ $legacy->id }}@endif</h5>
  @if($row || !empty($legacy))<a class="btn btn-sm btn-default" href="/admin/forms/{{ $t['kind'] }}">← All {{ strtolower($t['title']) }}</a>
  @else<form method="get" style="display:flex;gap:6px">@if($t['kind']==='credit')<select name="source" class="form-control form-control-sm"><option value="">All sources ({{ $counts['new'] + $counts['legacy'] }})</option><option value="new" @selected(($src ?? '')==='new')>New ({{ $counts['new'] }})</option><option value="legacy" @selected(($src ?? '')==='legacy')>Legacy ({{ $counts['legacy'] }})</option></select>@endif<select name="status" class="form-control form-control-sm"><option value="">All statuses</option>@foreach($t['statuses'] as $s)<option value="{{ $s }}" @selected($st===$s)>{{ ucfirst($s) }}</option>@endforeach</select><button class="btn btn-sm btn-primary">Filter</button></form>@endif
 </div><div class="card-block">
  @if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
  @if(!empty($legacy))
   @php $L = $legacy; $M = \App\Http\Controllers\AdminBizFormsController::LEGACY_MASK; @endphp
   <div class="alert alert-secondary" style="background:#f1f5f9">Legacy application (read-only). Sensitive fields are masked.</div>
   @if(!$reveal)<form method="post" action="/admin/forms/credit/legacy/{{ $L->id }}/reveal" onsubmit="return confirm('Reveal sensitive fields? This is logged as a security alert.')" style="margin-bottom:12px">@csrf<button class="btn btn-sm btn-warning">Reveal sensitive fields</button></form>@else<div class="alert alert-warning">Sensitive fields revealed. This view was logged.</div>@endif
   <dl>
    <dt>Account</dt><dd>{{ $L->display_name }} (user {{ $L->user_id }})</dd><dt>Submitted</dt><dd>{{ $L->created_at }}</dd>
    @foreach(['buisness_name'=>'Business name','buisness_doing'=>'DBA','buisness_title'=>'Title','first_name'=>'First name','last_name'=>'Last name','bphone'=>'Business phone','home_email'=>'Email','monthly_purchase_volume'=>'Monthly purchase volume','total_num_employees'=>'Employees','buisness_year_started'=>'Year started','order_id'=>'Order ID'] as $f=>$l)<dt>{{ $l }}</dt><dd>{{ $L->$f }}</dd>@endforeach
    <dt>Business address</dt><dd>{{ trim(implode(', ', array_filter([$L->bstreet_address1, $L->bstreet_address2, $L->bcity, $L->bstate, $L->bzip, $L->bcountry]))) }}</dd>
    <dt>Billing address</dt><dd>{{ trim(implode(', ', array_filter([$L->billing_street_address1, $L->billing_street_address2, $L->billing_city, $L->billing_state, $L->billing_zip, $L->billing_country]))) }}</dd>
    <dt>Home address</dt><dd>{{ trim(implode(', ', array_filter([$L->home_address1, $L->home_address2, $L->home_city, $L->home_state, $L->home_zip, $L->home_country]))) }}</dd>
    <dt>Home / mobile phone</dt><dd>{{ $L->home_phone }} {{ $L->home_mobile ? '/ '.$L->home_mobile : '' }}</dd>
    @foreach($M as $f=>$l)<dt>{{ $l }}</dt><dd>{{ $reveal ? ($L->$f ?: '—') : \App\Http\Controllers\AdminBizFormsController::mask($L->$f) }}@if($f==='driver_license' && $L->license_state) ({{ $L->license_state }})@endif</dd>@endforeach
    <dt>Uploaded file / signature</dt><dd>@if($file && $file['url'])<a href="{{ $file['url'] }}" target="_blank" rel="noopener">{{ $file['name'] }}</a>@if($file['image'])<div><img src="{{ $file['url'] }}" style="max-width:320px;max-height:160px;border:1px solid #eee;margin-top:6px"></div>@endif @elseif($file){{ $file['name'] }} (file missing) @else — @endif</dd>
   </dl>
  @elseif($row)
   <form method="post" action="/admin/forms/{{ $t['kind'] }}/{{ $row->id }}/status" style="display:flex;gap:8px;align-items:center;margin-bottom:14px">@csrf
    <span>Status: <span class="pill">{{ ucfirst($row->status) }}</span></span>
    <select name="status" class="form-control form-control-sm" style="width:auto">@foreach($t['statuses'] as $s)<option value="{{ $s }}" @selected($row->status===$s)>{{ ucfirst($s) }}</option>@endforeach</select>
    <button class="btn btn-sm btn-primary">Update status</button></form>
   <dl>
    <dt>Account</dt><dd>{{ $row->display_name }} (user {{ $row->user_id }}@if($row->i_customer), customer {{ $row->i_customer }}@endif)</dd>
    <dt>Submitted</dt><dd>{{ $row->created_at }}</dd>
    @if($t['kind']==='marketing')
     @foreach(['contact_name'=>'Contact name','business_name'=>'Business name','email'=>'Email','phone'=>'Phone','website'=>'Website','industry'=>'Industry','target_area'=>'Target area','contact_time'=>'Preferred contact time'] as $f=>$l)<dt>{{ $l }}</dt><dd>{{ $row->$f }}</dd>@endforeach
     <dt>Goals</dt><dd>{{ implode(', ', array_map(fn($k) => \App\Http\Controllers\BizFormsController::GOALS[$k] ?? $k, json_decode($row->goals ?? '[]', true) ?: [])) }}</dd>
     <dt>Budget range</dt><dd>{{ \App\Http\Controllers\BizFormsController::BUDGETS[$row->budget_range] ?? '—' }}</dd>
     <dt>Message</dt><dd style="white-space:pre-wrap">{{ $row->message }}</dd>
    @else
     @foreach(['legal_name'=>'Legal business name','dba'=>'DBA','entity_type'=>'Entity type','years_in_business'=>'Years in business','address'=>'Business address','phone'=>'Phone','email'=>'Email','applicant_name'=>'Owner / applicant','applicant_title'=>'Title'] as $f=>$l)<dt>{{ $l }}</dt><dd>{{ $row->$f }}</dd>@endforeach
     <dt>EIN</dt><dd>{{ $ein ?? '—' }}</dd>
     <dt>Requested credit</dt><dd>${{ number_format($row->requested_amount) }}</dd><dt>Monthly usage estimate</dt><dd>${{ number_format($row->monthly_usage) }}</dd>
     <dt>Reference 1</dt><dd>{{ $row->ref1_name }} – {{ $row->ref1_phone }}</dd><dt>Reference 2</dt><dd>{{ $row->ref2_name }} – {{ $row->ref2_phone }}</dd>
     <dt>Bank</dt><dd>{{ $row->bank_name }}</dd><dt>Agreement</dt><dd>{{ $row->agreed ? 'Accepted' : 'No' }} – signed “{{ $row->signature }}” on {{ $row->signed_date }}</dd>
    @endif
   </dl>
  @else
   <table class="table"><thead><tr><th>#</th><th>Submitted</th><th>Account</th>@if($t['kind']==='marketing')<th>Business</th><th>Contact</th>@else<th>Source</th><th>Legal name</th><th>Requested</th>@endif<th>Status</th><th></th></tr></thead><tbody>
   @forelse($rows as $x)
    <tr><td>{{ $x->id }}</td><td style="white-space:nowrap">{{ $x->created_at }}</td><td>{{ $x->display_name }}</td>
     @if($t['kind']==='marketing')<td>{{ $x->business_name }}</td><td>{{ $x->contact_name }}<div class="text-muted" style="font-size:12px">{{ $x->email }} · {{ $x->phone }}</div></td>@else<td><span class="pill" @if($x->source==='legacy') style="background:#f1f5f9;color:#475569" @endif>{{ ucfirst($x->source) }}</span></td><td>{{ $x->legal_name }}</td><td>{{ $x->requested_amount !== null ? '$'.number_format($x->requested_amount) : ($x->monthly_volume ? 'Monthly vol. '.$x->monthly_volume : '—') }}</td>@endif
     <td><span class="pill">{{ $x->status === 'legacy' ? 'Read-only' : ucfirst($x->status) }}</span></td><td><a class="btn btn-sm btn-default" href="/admin/forms/{{ $t['kind'] }}/{{ ($x->source ?? 'new')==='legacy' ? 'legacy/' : '' }}{{ $x->id }}">View</a></td></tr>
   @empty <tr><td colspan="7" class="text-muted">Nothing yet.</td></tr> @endforelse
   </tbody></table>
   {{ $rows->links() }}
  @endif
 </div></div>
</div></div></div></div>
@endsection
