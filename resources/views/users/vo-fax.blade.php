@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<style>.gvd-vo .fx-new{font-weight:700}.gvd-vo .fx-dot{display:inline-block;width:8px;height:8px;border-radius:50%;background:#1a73e8;margin-right:6px}</style>
<div class="gvd-vo">
@include('users.partials.fax-dash')
<div class="fd-grid">
  @include('users.partials.fax-compose', ['fdAdmin' => $admin && !$error])
  <div style="display:flex;flex-direction:column;gap:16px;min-width:0">
  <div class="fd-card">
    <div class="fd-tabs"><a href="/user/fax" class="on">Inbox</a><a href="/user/fax/send">Sent</a></div>
    @if ($error)<p class="muted">{{ $error }}</p>@else
      <div id="fx-box"><p style="margin-top:8px"><span class="vo-spin"></span>Loading faxes…</p></div>
    @endif
  </div>
  @if ($faxes->isNotEmpty())
  <div class="fd-card" id="fx-saved">
    <h3 style="margin-bottom:2px">Saved copies</h3><div class="muted" style="font-size:12.5px;margin-bottom:8px">Copies kept in your account for {{ $retentionDays }} days, then deleted automatically.</div>
    <div class="table-responsive"><table class="table table-hover">
      <thead><tr><th>From</th><th>To</th><th>Received (ET)</th><th>Pages</th><th></th></tr></thead><tbody>
      @foreach ($faxes as $f)
        <tr data-from="{{ substr(preg_replace('/\D/', '', (string) $f->caller), -10) }}" data-to="{{ substr(preg_replace('/\D/', '', (string) $f->did), -10) }}" data-ts="{{ \Carbon\Carbon::parse($f->received_at, 'UTC')->timestamp }}"><td>{{ $f->caller }}</td><td>{{ $f->did }}</td>
          <td>{{ \Carbon\Carbon::parse($f->received_at, 'UTC')->setTimezone('America/New_York')->format('M j, Y g:i A') }}</td><td>{{ $f->pages ?? '-' }}</td>
          <td><a class="btn btn-primary btn-sm" href="{{ $f->view_url }}" target="_blank" rel="noopener">View</a> <a class="btn btn-default btn-sm" href="{{ $f->view_url }}&download=1">Download</a></td></tr>
      @endforeach
      </tbody></table></div>
  </div>
  @endif
  </div>
</div>
</div>
@include('users.partials.vo-foot', ['page' => 'fax'])
@push('scripts')<script src="/js/gvd-vo.js?v=4"></script><script src="/js/gvd-fax-ui.js?v=3"></script>@endpush
@push('scripts')<script>
/* Hide a saved copy when the same fax (same sender, same number, received within 10 minutes) is already in the inbox above; hide the card if nothing is left. */
document.addEventListener('gvd:faxes', function (e) {
  var card = document.getElementById('fx-saved'); if (!card) return;
  var d10 = function (s) { return String(s || '').replace(/\D/g, '').slice(-10); }, live = (e.detail || []).map(function (r) { return [d10(r.from), d10(r.to), +r.ts || 0]; }), left = 0;
  card.querySelectorAll('tbody tr[data-ts]').forEach(function (tr) {
    var f = tr.getAttribute('data-from'), t = tr.getAttribute('data-to'), ts = +tr.getAttribute('data-ts');
    var dup = live.some(function (x) { return x[0] === f && x[1] === t && Math.abs(x[2] - ts) <= 600; });
    tr.style.display = dup ? 'none' : ''; if (!dup) left++;
  });
  card.style.display = left ? '' : 'none';
});
</script>@endpush
@endsection
