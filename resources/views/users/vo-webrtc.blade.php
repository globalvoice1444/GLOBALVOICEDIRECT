@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>
  .gvd-vo .wr-list{list-style:none;margin:0;padding:0;max-width:520px}
  .gvd-vo .wr-list li{border:1px solid #e5e7eb;border-radius:10px;margin-bottom:8px}
  .gvd-vo .wr-list label{display:flex;align-items:center;gap:12px;padding:12px 14px;margin:0;cursor:pointer;font-size:14px}
  .gvd-vo .wr-list li.on{border-color:#f59e0b;background:#fffbeb}
  .gvd-vo .wr-list input{width:18px;height:18px;accent-color:#0b2545}
  .gvd-vo .wr-bal{display:inline-flex;gap:8px;align-items:center;padding:6px 12px;border-radius:99px;background:#eef2ff;color:#1e3a8a;font-weight:600;font-size:13px}
</style>
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header" style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap"><h5 style="margin:0">WebRTC</h5>
    <span class="wr-bal" id="wr-bal" style="display:none"></span></div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    <p class="muted" style="margin-top:0">Choose which extension the browser phone uses on this computer. Pick <b>Disabled</b> to turn the browser phone off.</p>
    <ul class="wr-list" id="wr-list">
      <li><label><input type="radio" name="wr" value=""> Disabled</label></li>
      @foreach ($d['exts'] as $e)<li><label><input type="radio" name="wr" value="{{ $e['key'] }}"> {{ $e['label'] }}</label></li>@endforeach
    </ul>
    @if (!count($d['exts']))<p class="muted">None of your extensions can use the browser phone yet.</p>@endif
    <p style="margin-top:14px"><a href="/user/phone" class="btn btn-primary btn-sm" id="wr-open" onclick="if(window.GvdDock){event.preventDefault();GvdDock.open();}"><i class="feather icon-phone"></i> Open phone</a> <a href="/user/mobile-app" class="btn btn-default btn-sm"><i class="feather icon-smartphone"></i> Get the mobile app</a> <span class="muted" id="wr-msg" style="margin-left:8px"></span></p>
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'webrtc', 'js' => ['exts' => $d['exts'] ?? []]])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=6"></script>@endpush
