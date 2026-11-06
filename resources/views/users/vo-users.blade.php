@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>Users</h5><span class="muted">People who can sign in to your account.</span></div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    @if ($admin)<p><button type="button" class="btn btn-primary btn-sm" id="vo-add">Add user</button></p>@else<div class="ro">Only your company administrator can change users.</div>@endif
    <table class="table"><thead><tr><th>Name</th><th>Sign-in</th><th>Email</th><th>Status</th></tr></thead><tbody>
    @forelse ($d['users'] as $u)
      <tr class="vo-row" data-id="{{ $u['i_account'] }}"><td>{{ trim($u['first'] . ' ' . $u['last']) }}</td><td>{{ $u['login'] }}</td><td>{{ $u['email'] }}</td><td><span class="st {{ $u['status'] === 'Active' ? 'on' : '' }}">{{ $u['status'] }}</span></td></tr>
    @empty <tr><td colspan="4" class="muted">No users.</td></tr> @endforelse
    </tbody></table>
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'users', 'js' => ['users' => $d['users'] ?? []]])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=2"></script>@endpush
