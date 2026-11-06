@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>
  .gvd-vo .vx-row{display:flex;gap:16px;align-items:flex-start;padding:14px 0;border-bottom:1px solid #eee}
  .gvd-vo .vx-main{flex:1}.gvd-vo .vx-main h6{margin:0 0 4px;font-weight:600}.gvd-vo .vx-main p{margin:0;color:#666;font-size:13px}
  .gvd-vo .vx-fee{min-width:110px;text-align:right;white-space:nowrap}.gvd-vo .vx-ctl{min-width:56px;text-align:right}
  .gvd-vo .vx-na{color:#b94a48;font-size:12px;margin-top:4px}
  .gvd-vo .vx-sw{position:relative;display:inline-block;width:40px;height:22px}.gvd-vo .vx-sw input{opacity:0;width:0;height:0}
  .gvd-vo .vx-sl{position:absolute;inset:0;background:#ccc;border-radius:22px;transition:.2s;cursor:pointer}
  .gvd-vo .vx-sl:before{content:"";position:absolute;width:16px;height:16px;left:3px;top:3px;background:#fff;border-radius:50%;transition:.2s}
  .gvd-vo .vx-sw input:checked+.vx-sl{background:#1e7e34}.gvd-vo .vx-sw input:checked+.vx-sl:before{transform:translateX(18px)}
  .gvd-vo .vx-sw input:disabled+.vx-sl{opacity:.5;cursor:not-allowed}
  .gvd-vo .vx-lock{font-size:18px;color:#888;cursor:help}
  .gvd-vo .vx-why{margin-top:6px;font-size:12.5px;color:#92400e;background:#fffbeb;border:1px solid #fde68a;border-radius:8px;padding:6px 10px;display:inline-block}
  .gvd-vo .vx-fix{margin-left:8px;border:0;border-radius:7px;background:#0b2545;color:#fff;font-weight:600;font-size:12px;padding:4px 10px;cursor:pointer}
</style>
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>Manage extras</h5></div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    @forelse ($d['extras'] as $x)
      <div class="vx-row">
        <div class="vx-main"><h6>{{ $x['name'] }}</h6><p>{{ $x['desc'] }}</p>
          @if (!$x['qualified'] && !$x['on'])<div class="vx-na">Contact our support team to add this feature.</div>@endif
          @if ($x['lock'] !== '')<div class="vx-why">{{ $x['lock'] }}.@if ($x['code'] === 'WEBRTC' && $admin) <button type="button" class="vx-fix" data-fix="RNG">Enable Ring In/Out first</button>@endif</div>@endif</div>
        <div class="vx-fee">{{ $x['fee'] > 0 ? '$' . number_format($x['fee'], 2) . ' / month' : 'Included' }}</div>
        <div class="vx-ctl">
        @if ($x['lock'] !== '')
          <span class="vx-lock" aria-hidden="true">&#128274;</span>
        @else
          <label class="vx-sw" title="{{ $x['on'] ? 'On' : 'Off' }}"><input type="checkbox" class="vx-t" data-code="{{ $x['code'] }}" data-name="{{ $x['name'] }}" data-fee="{{ number_format($x['fee'], 2) }}"
            {{ $x['on'] ? 'checked' : '' }} {{ (!$admin || !$x['changeable']) ? 'disabled' : '' }}><span class="vx-sl"></span></label>
        @endif
        </div>
      </div>
    @empty <p class="muted">No extras.</p> @endforelse
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'extras'])
<script>document.querySelectorAll('.vx-fix').forEach(function (b) { b.onclick = function () {
  var cb = document.querySelector('input.vx-t[data-code="' + b.getAttribute('data-fix') + '"]'); if (!cb) return;
  cb.scrollIntoView({ block: 'center', behavior: 'smooth' });
  if (cb.disabled) { alert('Ring In/Out is not available on your plan yet. Contact our support team to add it.'); return; }
  if (!cb.checked) { cb.checked = true; cb.dispatchEvent(new Event('change')); } }; });</script>
@push('scripts')<script src="/js/gvd-vo.js?v=6"></script>@endpush
@endsection
