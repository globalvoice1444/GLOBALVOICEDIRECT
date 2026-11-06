@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>{{ $d['title'] ?? 'Calls' }}</h5></div>
  <div class="card-block">
  @if ($error)
    <p class="muted">{{ $error }}</p>
  @else
    @php $slug = $d['slug']; $q = $d['q'] ?? []; $isConf = $slug === 'conference-recordings'; $isRec = $slug === 'recorded'; @endphp
    @if (!$isConf)
    <form class="flt" method="get">
      <div><label>Time range</label><select name="range">@foreach ($d['ranges'] as $k => $l)<option value="{{ $k }}" {{ (string) $d['range'] === (string) $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>
      @if (count($d['statuses']))<div><label>Type</label><select name="status">@foreach ($d['statuses'] as $k => $l)<option value="{{ $k }}" {{ ($q['status'] ?? '') === (string) $k ? 'selected' : '' }}>{{ $l }}</option>@endforeach</select></div>@endif
      <div><label>From</label><input name="from" value="{{ $q['from'] }}" size="14"></div>
      <div><label>To</label><input name="to" value="{{ $q['to'] }}" size="14"></div>
      <div><label>Extension / line</label><input name="ext" value="{{ $q['ext'] }}" size="14"></div>
      <div><button class="btn btn-primary btn-sm" type="submit">Filter</button> <a class="btn btn-default btn-sm" href="?">Reset</a>
        <a class="btn btn-default btn-sm" href="?{{ http_build_query(array_merge(request()->query(), ['csv' => 1, 'page' => null])) }}">Download CSV</a></div>
    </form>
    @endif
    <p class="muted">{{ number_format($d['total']) }} {{ $isConf || $isRec ? 'recordings' : 'calls' }}</p>
    @if ($isRec && $admin && count($d['rows']))<p><button type="button" class="btn btn-danger btn-sm" id="vo-del" disabled>Delete selected</button></p>@endif
    <div class="table-responsive"><table class="table">
      @if ($isConf)
        <thead><tr><th>Room</th><th>Time (ET)</th><th>Size</th><th></th></tr></thead>
        <tbody>@forelse ($d['rows'] as $r)<tr><td>{{ $r['room'] }}</td><td>{{ $r['time'] }}</td><td>{{ $r['size'] }}</td><td><a href="/user/calls/conference-recording?n={{ $r['id'] }}">Download</a></td></tr>@empty<tr><td colspan="4" class="muted">No recorded conferences.</td></tr>@endforelse</tbody>
      @else
        <thead><tr>@if ($isRec && $admin)<th><input type="checkbox" id="vo-all"></th>@endif<th>Time (ET)</th><th>From</th><th>To</th><th>Number called</th><th>Extension / line</th><th>Duration</th><th>Status</th>@if ($isRec)<th>Recording</th>@endif</tr></thead>
        <tbody>
        @forelse ($d['rows'] as $r)
          <tr>@if ($isRec && $admin)<td><input type="checkbox" class="vo-sel" value="{{ $r['id'] }}"></td>@endif
            <td>{{ $r['time'] }}</td><td>{{ $r['from'] }}</td><td>{{ $r['to'] }}</td><td>{{ $r['called'] }}</td><td>{{ $r['ext'] }}</td><td>{{ $r['dur'] }}</td><td><span class="st">{{ $r['status'] }}</span></td>
            @if ($isRec)<td style="min-width:260px"><audio controls preload="none" src="/user/calls/recording?id={{ $r['id'] }}" style="height:30px;width:200px"></audio><span class="vo-rec-st muted" style="display:none;font-size:12px;margin:0 6px"><span class="vo-spin"></span>Loading…</span> <a href="/user/calls/recording?id={{ $r['id'] }}&dl=1">Download</a></td>@endif
          </tr>
        @empty
          <tr><td colspan="9" class="muted">No calls in this range.</td></tr>
        @endforelse
        </tbody>
      @endif
    </table></div>
    @php $pages = (int) ceil(($d['total'] ?: 0) / $d['per']); @endphp
    @if ($pages > 1)
      <p>@if ($d['page'] > 1)<a href="?{{ http_build_query(array_merge(request()->query(), ['page' => $d['page'] - 1])) }}">&larr; Newer</a>@endif
        <span class="muted">Page {{ $d['page'] }} of {{ $pages }}</span>
        @if ($d['page'] < $pages)<a href="?{{ http_build_query(array_merge(request()->query(), ['page' => $d['page'] + 1])) }}">Older &rarr;</a>@endif</p>
    @endif
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'calls'])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=2"></script>@endpush
@push('scripts')<script>
/* Recording player: show "Loading…" from the moment playback is requested until audio is ready; show an error if it fails. */
document.querySelectorAll('audio[src^="/user/calls/recording"]').forEach(function (a) {
  var st = a.nextElementSibling; if (!st || !st.classList.contains('vo-rec-st')) return;
  var show = function (html) { st.innerHTML = html; st.style.display = 'inline'; }, hide = function () { st.style.display = 'none'; };
  a.addEventListener('play', function () { if (a.readyState < 3) show('<span class="vo-spin"></span>Loading…'); });
  a.addEventListener('waiting', function () { show('<span class="vo-spin"></span>Loading…'); });
  ['canplay', 'playing', 'pause', 'ended'].forEach(function (ev) { a.addEventListener(ev, hide); });
  a.addEventListener('error', function () { show('Recording could not be loaded.'); });
});
</script>@endpush

