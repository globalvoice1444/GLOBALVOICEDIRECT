@extends('admin.layouts.layout')
@section('css')
<style>
.avb{--gn:#1E2A36;--gl:#E6EAF0;--gm:#64748B}
.avb .card{background:#fff;border:1px solid var(--gl);border-radius:12px;padding:20px;margin-bottom:16px}
.avb table{width:100%;border-collapse:collapse;font-size:13px}
.avb th,.avb td{padding:8px;border-bottom:1px solid var(--gl);text-align:left;vertical-align:top}
.avb th{color:var(--gm);font-weight:600}
.avb .pill{border-radius:10px;padding:2px 8px;font-size:12px;background:#eef2f6}
.avb .pill.bad{background:#fde8e8;color:#b42318}
.avb .pill.ok{background:#e6f4ea;color:#1e7e34}
.avb button,.avb .btn{border:1px solid var(--gl);background:#fff;border-radius:6px;padding:4px 10px;cursor:pointer;color:inherit;text-decoration:none;display:inline-block}
.avb button.dark,.avb .btn.dark{background:var(--gn);color:#fff;border-color:var(--gn)}
.avb .acts{display:flex;gap:6px;flex-wrap:wrap;align-items:center}
.avb .muted{color:var(--gm)}
.avb input,.avb select{border:1px solid var(--gl);border-radius:6px;padding:6px 8px}
.avb .pager{display:flex;gap:8px;align-items:center;margin-top:12px}
.avb .off{opacity:.5;pointer-events:none}
</style>
@endsection
@section('content')
<div class="avb">
 <div class="card">
  <h4 style="margin:0 0 6px">AI Connect</h4>
  <div class="muted">Customer API keys, activity, the per-customer switch, and provider availability. Secrets are not shown.</div>
  @if(session('message'))<div style="margin-top:8px" class="pill ok">{{ session('message') }}</div>@endif
  @if(session('error'))<div style="margin-top:8px" class="pill bad">{{ session('error') }}</div>@endif
 </div>

 <div class="card" style="overflow-x:auto">
  <h4 style="margin:0 0 10px">API keys</h4>
  <form method="get" action="/admin/ai-connect" class="acts" style="margin-bottom:12px">
   <input type="search" name="key_q" value="{{ $keyQ }}" placeholder="Search by customer" aria-label="Search keys by customer">
   <button class="dark" type="submit">Search</button>
  </form>
  <table>
   <thead><tr><th>Customer</th><th>Name</th><th>Prefix</th><th>Permissions</th><th>Today</th><th>Last used</th><th>Status</th><th></th></tr></thead>
   <tbody>
   @forelse($keys['rows'] as $k)
    <tr>
     <td>{{ $k['customer'] }}<div class="muted">user {{ $k['user_id'] }}</div></td>
     <td>{{ $k['name'] }}</td>
     <td>{{ $k['prefix'] }}…</td>
     <td>{{ $k['scopes'] ? implode(', ', $k['scopes']) : '—' }}</td>
     <td>{{ $k['today'] }} / {{ $k['limit'] }}</td>
     <td>{{ $k['last_used'] }}</td>
     <td><span class="pill {{ $k['status'] === 'Active' ? 'ok' : 'bad' }}">{{ $k['status'] }}</span></td>
     <td>
      @if($k['status'] === 'Active')
      <form method="post" action="/admin/ai-connect/keys/revoke" onsubmit="return confirm('Revoke this API key?')">
       @csrf
       <input type="hidden" name="key_id" value="{{ $k['id'] }}">
       <button type="submit">Revoke</button>
      </form>
      @endif
     </td>
    </tr>
   @empty
    <tr><td colspan="8" class="muted">No API keys.</td></tr>
   @endforelse
   </tbody>
  </table>
  @include('admin.partials.ai-connect-pager', ['page' => $keys['page'], 'pages' => $keys['pages'], 'param' => 'keys_page'])
 </div>

 <div class="card" style="overflow-x:auto">
  <h4 style="margin:0 0 10px">Activity</h4>
  <form method="get" action="/admin/ai-connect" class="acts" style="margin-bottom:12px">
   <input type="search" name="act_customer" value="{{ $actCustomer }}" placeholder="Customer" aria-label="Filter activity by customer">
   <input type="search" name="act_key" value="{{ $actKey }}" placeholder="Key" aria-label="Filter activity by key">
   <input type="search" name="act_action" value="{{ $actAction }}" placeholder="Action" aria-label="Filter activity by action">
   <input type="date" name="act_date" value="{{ $actDate }}" aria-label="Filter activity by date">
   <button class="dark" type="submit">Filter</button>
  </form>
  <table>
   <thead><tr><th>When</th><th>Customer</th><th>Key</th><th>Action</th><th>Status</th><th>Details</th></tr></thead>
   <tbody>
   @forelse($activity['rows'] as $a)
    <tr>
     <td>{{ $a['when'] }}</td>
     <td>{{ $a['customer'] }}</td>
     <td>{{ $a['key'] }}</td>
     <td>{{ $a['action'] }}</td>
     <td>{{ $a['status'] }}</td>
     <td>{{ $a['summary'] }}</td>
    </tr>
   @empty
    <tr><td colspan="6" class="muted">No activity.</td></tr>
   @endforelse
   </tbody>
  </table>
  @include('admin.partials.ai-connect-pager', ['page' => $activity['page'], 'pages' => $activity['pages'], 'param' => 'act_page'])
 </div>

 <div class="card" style="overflow-x:auto">
  <h4 style="margin:0 0 10px">Customer switch</h4>
  <form method="get" action="/admin/ai-connect" class="acts" style="margin-bottom:12px">
   <input type="search" name="cust_q" value="{{ $custQ }}" placeholder="Find a customer" aria-label="Find a customer">
   <button class="dark" type="submit">Search</button>
  </form>
  <table>
   <thead><tr><th>Customer</th><th>Email</th><th>AI Connect</th><th></th></tr></thead>
   <tbody>
   @forelse($customers['rows'] as $c)
    <tr>
     <td>{{ $c['customer'] }}<div class="muted">user {{ $c['user_id'] }}</div></td>
     <td>{{ $c['email'] !== '' ? $c['email'] : '—' }}</td>
     <td><span class="pill {{ $c['enabled'] ? 'ok' : 'bad' }}">{{ $c['enabled'] ? 'On' : 'Off' }}</span></td>
     <td>
      <form method="post" action="/admin/ai-connect/customers/toggle">
       @csrf
       <input type="hidden" name="user_id" value="{{ $c['user_id'] }}">
       <input type="hidden" name="enabled" value="{{ $c['enabled'] ? '0' : '1' }}">
       <button type="submit">{{ $c['enabled'] ? 'Turn off' : 'Turn on' }}</button>
      </form>
     </td>
    </tr>
   @empty
    <tr><td colspan="4" class="muted">No customers with AI Connect yet.</td></tr>
   @endforelse
   </tbody>
  </table>
  @include('admin.partials.ai-connect-pager', ['page' => $customers['page'], 'pages' => $customers['pages'], 'param' => 'cust_page'])
 </div>

 <div class="card" style="overflow-x:auto">
  <h4 style="margin:0 0 10px">Providers</h4>
  <div class="muted" style="margin-bottom:10px">The registry is read-only. Hiding a provider removes it from the customer dropdown.</div>
  <table>
   <thead><tr><th>Provider</th><th>Category</th><th>Gateway supported</th><th>Customers</th><th></th></tr></thead>
   <tbody>
   @foreach($providers as $p)
    <tr>
     <td>{{ $p['label'] }}<div class="muted">{{ $p['id'] }}</div></td>
     <td>{{ $p['category'] === 'realtime_voice' ? 'Realtime voice' : 'Text model' }}</td>
     <td>{{ $p['gateway_supported'] ? 'Yes' : 'No' }}</td>
     <td><span class="pill {{ $p['enabled'] ? 'ok' : 'bad' }}">{{ $p['enabled'] ? 'Shown' : 'Hidden' }}</span></td>
     <td>
      <form method="post" action="/admin/ai-connect/providers">
       @csrf
       <input type="hidden" name="provider" value="{{ $p['id'] }}">
       <input type="hidden" name="enabled" value="{{ $p['enabled'] ? '0' : '1' }}">
       <button type="submit">{{ $p['enabled'] ? 'Hide from customers' : 'Show to customers' }}</button>
      </form>
     </td>
    </tr>
   @endforeach
   </tbody>
  </table>
 </div>
</div>
@endsection
