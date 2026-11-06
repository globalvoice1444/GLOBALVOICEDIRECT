@extends('layouts.user.user_main')
@section('content')
<style>
  .gvd-fax td, .gvd-fax th { vertical-align: middle; }
  .gvd-fax .muted { color: #888; font-size: 13px; }
</style>
<div class="row gvd-fax">
  <div class="col-sm-12">
    <div class="card">
      @if (session()->has('error'))
        <div class="alert close_alert alert-danger"><strong>Info!</strong> {{ session()->get('error') }}</div>
      @endif
      <div class="card-header">
        <h5>Fax Inbox</h5>
        <span class="muted">Faxes sent to your numbers appear here. They are kept for {{ $retentionDays }} days, then deleted automatically.</span>
      </div>
      <div class="card-block table-border-style">
        @if ($faxes->isEmpty())
          <p class="muted">No faxes yet.</p>
        @else
        <div class="table-responsive">
          <table class="table table-hover">
            <thead><tr><th>Received (ET)</th><th>From</th><th>To</th><th>Pages</th><th>Size</th><th></th></tr></thead>
            <tbody>
            @foreach ($faxes as $f)
              <tr>
                <td>{{ \Carbon\Carbon::parse($f->received_at, 'UTC')->setTimezone('America/New_York')->format('M j, Y g:i A') }}</td>
                <td>{{ $f->caller }}</td>
                <td>{{ $f->did }}</td>
                <td>{{ $f->pages ?? '-' }}</td>
                <td>{{ number_format($f->size / 1024, 0) }} KB</td>
                <td>
                  <a class="btn btn-primary btn-sm" href="{{ $f->view_url }}" target="_blank" rel="noopener">View</a>
                  <a class="btn btn-default btn-sm" href="{{ $f->view_url }}&download=1">Download</a>
                </td>
              </tr>
            @endforeach
            </tbody>
          </table>
        </div>
        <p class="muted">View links expire after 5 minutes. Refresh this page for a new link.</p>
        @endif
      </div>
    </div>
    <div class="card">
      <div class="card-header"><h5>Recent Fax Activity</h5></div>
      <div class="card-block">
        @if ($audit->isEmpty())
          <p class="muted">No activity yet.</p>
        @else
          <table class="table table-sm">
            <thead><tr><th>When (ET)</th><th>Action</th><th>Fax</th></tr></thead>
            <tbody>
            @foreach ($audit as $a)
              <tr><td>{{ \Carbon\Carbon::parse($a->created_at)->setTimezone('America/New_York')->format('M j, Y g:i A') }}</td><td>{{ ucfirst($a->action) }}</td><td class="muted">{{ substr($a->fax_uuid, 0, 8) }}</td></tr>
            @endforeach
            </tbody>
          </table>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
