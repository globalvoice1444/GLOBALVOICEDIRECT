@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo">
@include('users.partials.fax-dash')
<div class="fd-grid">
  @if (!$error && $hasFax)
    @include('users.partials.fax-compose', ['fdAdmin' => $admin])
  @else
    <div class="fd-card"><h3>Send a fax</h3><p class="muted" style="margin:0">{{ $error ?: 'Sending faxes is not set up on your account yet. Contact our support team to add it.' }}</p></div>
  @endif
  <div class="fd-card" style="min-width:0">
    <div class="fd-tabs"><a href="/user/fax">Inbox</a><a href="/user/fax/send" class="on">Sent</a></div>
    @if ($error)<p class="muted">{{ $error }}</p>
    @elseif (!$hasFax)<p class="muted">Sending faxes is not set up on your account yet.</p>
    @else
      <div id="fx-box"><p style="margin-top:8px"><span class="vo-spin"></span>Loading sent faxes…</p></div>
      <p class="muted" style="font-size:12.5px">Faxes sent in the last 30 days.</p>
    @endif
  </div>
</div>
</div>
@include('users.partials.vo-foot', ['page' => 'faxout'])
@push('scripts')<script src="/js/gvd-vo.js?v=4"></script><script src="/js/gvd-fax-ui.js?v=3"></script>@endpush
@endsection
