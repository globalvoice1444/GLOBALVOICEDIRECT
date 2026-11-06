@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>Conference rooms</h5>@if (!empty($d['ext']))<span class="muted">Callers join through extension {{ $d['ext'] }}.</span>@endif</div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    @if ($admin)<p><button type="button" class="btn btn-primary btn-sm" id="vo-add">Add conference room</button></p>@else<div class="ro">Only your company administrator can change conference rooms.</div>@endif
    <table class="table"><thead><tr><th>Room</th><th>Max participants</th><th>Recording</th><th>Wait for moderator</th><th>PIN</th></tr></thead><tbody>
    @forelse ($d['rooms'] as $r)
      <tr class="vo-row" data-id="{{ $r['i_conf'] }}"><td><strong>{{ $r['name'] }}</strong></td><td>{{ $r['max_participants'] }}</td><td>{{ ($r['record'] ?? '') === 'Y' ? 'On' : 'Off' }}</td>
        <td>{{ ($r['wait_for_moderator'] ?? '') === 'Y' ? 'Yes' : 'No' }}</td><td>{{ $r['hasPin'] ? 'Set' : 'None' }}</td></tr>
    @empty <tr><td colspan="5" class="muted">No conference rooms yet.</td></tr> @endforelse
    </tbody></table>
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'conferences', 'js' => ['rooms' => $d['rooms'] ?? []]])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=2"></script>@endpush
