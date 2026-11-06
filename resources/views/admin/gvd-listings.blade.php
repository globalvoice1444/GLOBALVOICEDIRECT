@extends('admin.layouts.layout')
@section('css')<style>.gvd-bl .pill{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;background:#eef2ff;color:#4338ca}.gvd-bl dl{display:grid;grid-template-columns:200px 1fr;gap:6px 12px}.gvd-bl dt{color:#777;font-weight:400}</style>@endsection
@section('content')
@php use App\Support\BusinessListings as BL; @endphp
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gvd-bl">
 @if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
 @if($row)
 <div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center"><h5>{{ $row->business_name }} <small>{{ $row->phone }}</small></h5><a class="btn btn-sm btn-default" href="/admin/forms/listings">← All business listings</a></div><div class="card-block">
  <form method="post" action="/admin/forms/listings/status" style="display:flex;gap:8px;align-items:center;margin-bottom:14px">@csrf<input type="hidden" name="id" value="{{ $row->id }}">
   <span>Status: <span class="pill">{{ $row->status }}</span></span><select name="status" class="form-control form-control-sm" style="width:auto">@foreach(BL::STATUSES as $s)<option @selected($row->status===$s)>{{ $s }}</option>@endforeach</select><button class="btn btn-sm btn-primary">Update status</button>
   <a class="btn btn-sm btn-default" target="_blank" href="/admin/forms/listings/{{ $row->id }}/guide">Open printable guide</a></form>
  <dl>
   <dt>Account</dt><dd>{{ $row->display_name }} (user {{ $row->user_id }}@if($row->i_customer), customer {{ $row->i_customer }}@endif)</dd>
   @foreach(['business_name'=>'Business name','street'=>'Street','suite'=>'Suite','city'=>'City','state'=>'State','zip'=>'ZIP','country'=>'Country','phone'=>'Phone (ID)','website'=>'Website','email'=>'Email','category'=>'Primary category','contact_name'=>'Contact','created_at'=>'Created','updated_at'=>'Updated'] as $k=>$l)<dt>{{ $l }}</dt><dd>{{ $row->$k }}</dd>@endforeach
   <dt>Extra categories</dt><dd>{{ implode(', ', json_decode($row->extra_categories ?? '[]', true) ?: []) }}</dd>
   <dt>Description</dt><dd style="white-space:pre-wrap">{{ $row->description }}</dd>
   <dt>Hours</dt><dd>@foreach(BL::hoursText($row) as $d=>$t){{ $d }}: {{ $t }}<br>@endforeach</dd>
   <dt>Consent</dt><dd>{{ $row->consent ? 'Yes' : 'No' }} @if($row->ip)(IP {{ $row->ip }})@endif</dd>
  </dl>
 </div></div>
 @else
 <div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px"><h5>Business Listings</h5>
  <div><a href="/admin/forms/listings" class="{{ $st ? '' : 'font-weight-bold' }}">All</a>@foreach(BL::STATUSES as $s) | <a href="/admin/forms/listings?status={{ $s }}" @if($st===$s) style="font-weight:700" @endif>{{ $s }}</a>@endforeach</div></div><div class="card-block">
  <table class="table"><thead><tr><th>Customer ID (phone)</th><th>Business</th><th>Account</th><th>City / State</th><th>Category</th><th>Status</th><th>Updated</th></tr></thead><tbody>
  @forelse($rows as $x)<tr><td><a href="/admin/forms/listings/{{ $x->id }}"><b>{{ $x->phone }}</b></a></td><td>{{ $x->business_name }}</td><td>{{ $x->display_name }}</td><td>{{ $x->city }}, {{ $x->state }}</td><td>{{ $x->category }}</td><td><span class="pill">{{ $x->status }}</span></td><td style="white-space:nowrap">{{ $x->updated_at }}</td></tr>
  @empty<tr><td colspan="7" class="text-muted">No submissions yet.</td></tr>@endforelse
  </tbody></table>{{ $rows->links() }}
 </div></div>
 <div class="card"><div class="card-header"><h5>Bing export</h5></div><div class="card-block">
  <p>Exports rows with status <code>new</code> and <code>bing_queued</code> as a Bing Places bulk-upload CSV (Store ID = phone digits). New rows become <code>bing_queued</code>. Next automatic run: <b>{{ $next }}</b>. New: {{ $newCount }}, queued: {{ count($queued) }}.</p>
  <form method="post" action="/admin/forms/listings/export">@csrf<button class="btn btn-primary btn-sm">Generate Bing export now</button></form>
  <h6 style="margin-top:16px">Queued for Bing</h6>
  <form method="post" action="/admin/forms/listings/status">@csrf<input type="hidden" name="status" value="bing_uploaded"><input type="hidden" name="back" value="1">
   @forelse($queued as $q)<div><label style="font-weight:400"><input type="checkbox" name="ids[]" value="{{ $q->id }}" checked> {{ $q->phone }} – {{ $q->business_name }}</label></div>@empty<p class="text-muted">None.</p>@endforelse
   @if(count($queued))<button class="btn btn-default btn-sm">Mark selected uploaded</button>@endif</form>
  <h6 style="margin-top:16px">Export files (protected)</h6>
  <ul>@forelse($files as $fn)<li><a href="/admin/forms/listings/file/{{ $fn }}">{{ $fn }}</a></li>@empty<li class="text-muted">None yet.</li>@endforelse</ul>
  <p class="text-muted" style="font-size:12px">Column layout follows Bing Places' bulk template as best known; compare headers with Bing's current template before the first upload.</p>
 </div></div>
 @endif
</div></div></div></div>
@endsection
