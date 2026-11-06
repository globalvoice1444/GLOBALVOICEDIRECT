@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>
  .gvd-vo .do-tog{position:relative;display:inline-block;width:38px;height:22px;vertical-align:middle}
  .gvd-vo .do-tog input{opacity:0;width:0;height:0}
  .gvd-vo .do-tog span{position:absolute;inset:0;background:#cbd5e1;border-radius:22px;cursor:pointer;transition:.2s}
  .gvd-vo .do-tog span:before{content:"";position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s}
  .gvd-vo .do-tog input:checked+span{background:#16a34a}
  .gvd-vo .do-tog input:checked+span:before{transform:translateX(16px)}
  .gvd-vo .do-tog input:disabled+span{opacity:.5;cursor:not-allowed}
  .gvd-vo .do-num{font-weight:600;letter-spacing:.02em}
</style>
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap"><h5 style="margin:0">Dial out numbers</h5>
    @if (!$error && $admin)<button type="button" class="btn btn-primary btn-sm" id="vo-add"><i class="feather icon-plus"></i> Add dial out number</button>@endif</div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    <p class="muted" style="margin-top:0">Phones you are allowed to call out from. When you call in from one of these numbers you can dial out, and the person you call sees the number you choose under <b>Show number as</b>.</p>
    @if (!$admin)<div class="ro">Only your company administrator can change dial out numbers.</div>@endif
    <table class="table"><thead><tr><th>Calling from</th><th>Nickname</th><th>Show number as</th><th>Active</th></tr></thead><tbody>
    @forelse ($d['rows'] as $r)
      <tr class="vo-row" data-id="{{ $r['i_account'] }}"><td class="do-num">{{ \App\Portal\PhoneNumbers::fmt($r['number']) }}</td><td>{{ $r['nickname'] }}</td><td>{{ $r['showLabel'] ?: '—' }}</td>
        <td><label class="do-tog" onclick="event.stopPropagation()"><input type="checkbox" class="vo-act" data-id="{{ $r['i_account'] }}" @checked($r['active']) @disabled(!$admin || $r['guarded']) aria-label="Active"><span></span></label></td></tr>
    @empty <tr><td colspan="4" class="muted">No dial out numbers yet.@if ($admin) Use <b>Add dial out number</b> to add one.@endif</td></tr> @endforelse
    </tbody></table>
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'dialout', 'js' => ['rows' => $d['rows'] ?? [], 'dids' => $d['dids'] ?? []]])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=5"></script>@endpush
