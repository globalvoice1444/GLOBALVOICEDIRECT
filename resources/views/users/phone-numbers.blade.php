@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="gvd-vo gvd-vo-legacy">
<style>
  .gvd-pn .muted{color:#888;font-size:13px}
  .gvd-pn tr.pn-row{cursor:pointer}
  .gvd-pn tr.pn-row:hover{background:#fafafa}
  .gvd-pn .st{border-radius:10px;padding:2px 8px;font-size:12px;white-space:nowrap}
  .gvd-pn .st-Active{background:#e6f4ea;color:#1e7e34}
  .gvd-pn .st-other{background:#f1f1f1;color:#666}
  .gvd-pn h6.grp{margin:18px 0 6px;font-weight:700}
  .gvd-pn .ro{background:#fff8e1;border:1px solid #ffe08a;border-radius:6px;padding:8px 12px;margin-bottom:12px;font-size:13px}
  .gvd-pn .ld{color:#aaa}
  .pn-spin{display:inline-block;width:14px;height:14px;border:2px solid #ccc;border-top-color:#1a73e8;border-radius:50%;animation:pnspin .8s linear infinite;vertical-align:middle;margin-right:6px}
  @keyframes pnspin{to{transform:rotate(360deg)}}
  #pn-panel{position:fixed;top:0;right:-480px;width:460px;max-width:95vw;height:100%;background:#fff;box-shadow:-4px 0 16px rgba(0,0,0,.15);z-index:1050;transition:right .25s;overflow-y:auto;padding:18px}
  #pn-panel.open{right:0}
  #pn-shade{display:none;position:fixed;inset:0;background:rgba(0,0,0,.25);z-index:1040}
  #pn-shade.open{display:block}
  #pn-panel label{display:block;font-size:12px;color:#666;margin:12px 0 4px}
  #pn-panel select,#pn-panel input[type=text]{width:100%;padding:6px 8px;border:1px solid #ccc;border-radius:4px}
</style>
<div class="row gvd-pn">
  <div class="col-sm-12">
    <div class="card">
      <div class="card-header">
        <h5>Phone numbers</h5>
        <span class="muted">Every phone number on your account and where its calls go.</span>
      </div>
      <div class="card-block">
        @if ($error)
          <p class="muted">{{ $error }}</p>
        @else
          @if (!$admin)<div class="ro">You can view your phone numbers. Only your company administrator can change them.</div>@endif
          @php $groups = ['vo' => 'Your phone numbers', 'other' => 'Other lines on your account', 'system' => 'System entries']; @endphp
          @foreach ($groups as $g => $title)
            @php $list = array_values(array_filter($rows, fn ($r) => $r['kind'] === $g)); @endphp
            @if (count($list))
              <h6 class="grp">{{ $title }} <span class="muted">({{ count($list) }})</span></h6>
              <div class="table-responsive"><table class="table">
                @if ($g !== 'system')
                <thead><tr><th>Number</th><th>Label</th><th>Plan</th><th>Where calls go</th><th>Caller ID</th><th>Voicemail email</th><th>Fax format</th><th>Status</th></tr></thead>
                @else
                <thead><tr><th>Entry</th><th>Plan</th><th>Status</th></tr></thead>
                @endif
                <tbody>
                @foreach ($list as $r)
                  @if ($g !== 'system')
                  <tr class="pn-row" data-id="{{ $r['i_account'] }}">
                    <td><strong>{{ $r['display'] }}</strong>@if ($r['display'] !== $r['id'])<div class="muted">{{ $r['id'] }}</div>@endif</td>
                    <td class="pn-label">{{ $r['label'] }}</td>
                    <td>{{ $r['plan'] }}</td>
                    <td class="pn-route">@if ($g === 'vo' && $r['status'] === 'Active')<span class="ld">Loading…</span>@else – @endif</td>
                    <td>{{ $r['cli']['text'] }}</td>
                    <td class="pn-email">@if ($r['status'] === 'Active')<span class="ld">Loading…</span>@else – @endif</td>
                    <td class="pn-fax">@if ($r['status'] === 'Active')<span class="ld">Loading…</span>@else – @endif</td>
                    <td><span class="st {{ $r['status'] === 'Active' ? 'st-Active' : 'st-other' }}">{{ $r['status'] }}</span></td>
                  </tr>
                  @else
                  <tr><td>{{ $r['extName'] ?? $r['id'] }}</td><td>{{ $r['plan'] }}</td><td><span class="st {{ $r['status'] === 'Active' ? 'st-Active' : 'st-other' }}">{{ $r['status'] }}</span></td></tr>
                  @endif
                @endforeach
                </tbody>
              </table></div>
            @endif
          @endforeach
        @endif
      </div>
    </div>
  </div>
</div>
<div id="pn-shade"></div>
<div id="pn-panel" aria-hidden="true"></div>
<script>window.GVD_NUM = {!! json_encode(['rows' => array_values(array_filter($rows, fn ($r) => $r['kind'] !== 'system')), 'admin' => $admin, 'exts' => $exts], JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};</script>
</div>
@endsection
@push('scripts')
<script src="/js/gvd-numbers.js?v=2"></script>
@endpush
