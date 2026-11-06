@extends('layouts.user.user_main')
@section('content')
@include('users.partials.vo-head')
<div class="row gvd-vo"><div class="col-sm-12"><div class="card">
  <div class="card-header"><h5>Call block</h5></div>
  <div class="card-block">
  @if ($error)<p class="muted">{{ $error }}</p>@else
    <div class="ro">Enter phone numbers in full international format, e.g. 1 973 865 9541.</div>
    @if (!$admin)<div class="ro">Only your company administrator can change call block.</div>@endif
    @foreach (['blocked' => 'Blocked numbers', 'areas' => 'Blocked area codes', 'allowed' => 'Allowed numbers'] as $k => $t)
      <div class="cb-card"><h6>{{ $t }} @if ($admin)<a href="#" class="vo-cb-add" data-sec="{{ $k }}" style="color:#fff;font-size:20px" aria-label="Add">+</a>@endif</h6>
        <ul>
        @forelse ($d['lists'][$k] as $n)
          <li><span>{{ $n['label'] }} <span class="muted">· on {{ count($n['on']) }} of {{ count($d['targets']) }} lines</span></span>
            @if ($admin)<a href="#" class="vo-cb-del" data-sec="{{ $k }}" data-num="{{ $n['number'] }}" aria-label="Remove">&#128465;</a>@endif</li>
        @empty <li class="muted">None</li> @endforelse
        </ul></div>
    @endforeach
  @endif
  </div>
</div></div></div>
@include('users.partials.vo-foot', ['page' => 'callblock', 'js' => ['targets' => $d['targets'] ?? [], 'lists' => $d['lists'] ?? []]])
@endsection
@push('scripts')<script src="/js/gvd-vo.js?v=2"></script>@endpush
