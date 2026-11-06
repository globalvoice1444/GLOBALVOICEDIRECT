@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>Extensions</h5><span class="muted">Phones, voicemail, forwarding and call recording for each extension.</span></div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    @if (!$admin)<div class="ro">Only your company administrator can change extensions.</div>@endif
    @php $fw = ['N' => 'Off', 'F' => 'Forward to numbers', 'C' => 'Forward', 'Y' => 'Advanced forwarding', 'U' => 'Forward (unconditional)']; @endphp
    <h6 class="grp">Hunt groups <span class="muted">({{ count($d['hunt']) }})</span></h6>
    @if (count($d['hunt']))
      <table class="table"><thead><tr><th>Number</th><th>Name</th><th>Ring order</th><th>Members</th></tr></thead><tbody>
      @foreach ($d['hunt'] as $h)<tr><td>{{ $h['id'] }}</td><td>{{ $h['name'] }}</td><td>{{ $h['sequence'] }}</td><td>{{ $h['members'] }}</td></tr>@endforeach
      </tbody></table>
    @else <p class="muted">No hunt groups.</p> @endif
    @foreach (['employee' => 'Employee extensions', 'other' => 'Other extensions'] as $g => $t)
      @php $list = array_values(array_filter($d['rows'], fn ($r) => $r['group'] === $g)); @endphp
      <h6 class="grp">{{ $t }} <span class="muted">({{ count($list) }})</span></h6>
      <table class="table"><thead><tr><th>Ext</th><th>Name</th><th>Phone</th><th>Forwarding</th><th>Call recording</th></tr></thead><tbody>
      @foreach ($list as $r)
        <tr class="vo-row" data-id="{{ $r['i_account'] }}"><td><strong>{{ $r['ext'] }}</strong></td><td>{{ $r['name'] }}</td>
          <td><span class="st {{ (string) $r['sip'] === '1' ? 'on' : '' }}">{{ (string) $r['sip'] === '1' ? 'Registered' : 'Not registered' }}</span></td>
          <td>{{ $fw[$r['forward']] ?? $r['forward'] }}</td><td>{{ $r['recording'] === 'Y' ? 'On' : 'Off' }}</td></tr>
      @endforeach
      </tbody></table>
    @endforeach
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'extensions'])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=2"></script>@endpush
