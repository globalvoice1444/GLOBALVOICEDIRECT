@extends('admin.layouts.layout')
@section('css')
<style>.gvd-al td{vertical-align:top}.gvd-al .sev{display:inline-block;padding:1px 8px;border-radius:10px;font-size:11px;color:#fff}.gvd-al .sev-security{background:#e74c3c}.gvd-al .sev-warning{background:#f39c12}.gvd-al .sev-info{background:#3498db}.gvd-al tr.unread{font-weight:600;background:#fffbea}.gvd-al pre{white-space:pre-wrap;font-size:12px;background:#f7f7f7;padding:8px;margin:6px 0 0;max-width:640px}</style>
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gvd-al">
 <div class="card"><div class="card-header" style="display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:8px">
  <h5>Alerts</h5>
  <form method="get" action="/admin/alerts" style="display:flex;gap:6px;align-items:center">
   <select name="type" class="form-control form-control-sm"><option value="">All types</option>@foreach($types as $t)<option value="{{ $t }}" @selected($type === $t)>{{ ucfirst($t) }}</option>@endforeach</select>
   <select name="state" class="form-control form-control-sm"><option value="">Read &amp; unread</option><option value="unread" @selected($state === 'unread')>Unread only</option></select>
   <button class="btn btn-sm btn-primary">Filter</button>
  </form>
  <form method="post" action="/admin/alerts/read">@csrf<input type="hidden" name="all" value="1"><input type="hidden" name="type" value="{{ $type }}"><button class="btn btn-sm btn-default">Mark all {{ $type ?: '' }} as read</button></form>
 </div><div class="card-block">
  @if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
  <table class="table"><thead><tr><th>#</th><th>When</th><th>Type</th><th>Alert</th><th>Account</th><th>IP</th><th></th></tr></thead><tbody>
  @forelse($alerts as $a)
   <tr class="{{ $a->read_at ? '' : 'unread' }}">
    <td>{{ $a->id }}</td><td style="white-space:nowrap">{{ $a->created_at }}</td>
    <td><span class="sev sev-{{ $a->severity }}">{{ $a->type }}</span></td>
    <td>{{ $a->title }}<details><summary style="cursor:pointer;font-weight:400;color:#3498db">Details</summary><pre>{{ json_encode(json_decode($a->details ?? 'null'), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) }}</pre></details></td>
    <td>{{ $a->display_name ?? '' }}@if($a->user_id)<div class="text-muted" style="font-weight:400;font-size:12px">user {{ $a->user_id }}@if($a->i_customer) · customer {{ $a->i_customer }}@endif</div>@endif</td>
    <td>{{ $a->ip }}</td>
    <td>@if(!$a->read_at)<form method="post" action="/admin/alerts/read">@csrf<input type="hidden" name="ids[]" value="{{ $a->id }}"><button class="btn btn-sm btn-default">Mark read</button></form>@else<span class="text-muted" style="font-size:12px">Read</span>@endif</td>
   </tr>
  @empty
   <tr><td colspan="7" class="text-muted">No alerts.</td></tr>
  @endforelse
  </tbody></table>
  {{ $alerts->links() }}
 </div></div>
</div></div></div></div>
@endsection
