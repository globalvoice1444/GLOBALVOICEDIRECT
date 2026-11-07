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
.avb .pill.wait{background:#fff4e5;color:#b54708}
.avb button{border:1px solid var(--gl);background:#fff;border-radius:6px;padding:4px 10px;cursor:pointer}
.avb button.dark{background:var(--gn);color:#fff;border-color:var(--gn)}
.avb .acts{display:flex;gap:6px;flex-wrap:wrap}
.avb .muted{color:var(--gm)}
</style>
@endsection
@section('content')
<div class="avb">
 <div class="card">
  <h4 style="margin:0 0 6px">AI Connect &gt; Voice bots</h4>
  <div class="muted">Every customer voice account. Provider keys and SIP passwords are not shown.</div>
  @if(session('message'))<div style="margin-top:8px" class="pill ok">{{ session('message') }}</div>@endif
  @if(session('error'))<div style="margin-top:8px" class="pill bad">{{ session('error') }}</div>@endif
 </div>
 <div class="card" style="overflow-x:auto">
  <table>
   <thead>
    <tr>
     <th>Customer</th>
     <th>Provider</th>
     <th>Has key</th>
     <th>Key last updated</th>
     <th>SIP username</th>
     <th>Provisioned status</th>
     <th>Last gateway result</th>
     <th>Created</th>
     <th>Updated</th>
     <th>Provisioned</th>
     <th></th>
    </tr>
   </thead>
   <tbody>
   @forelse($rows as $row)
    <tr>
     <td>
      <b>{{ $row['name'] }}</b>
      @if($row['login'] !== '')<div class="muted">{{ $row['login'] }}</div>@endif
      <div class="muted">Account {{ $row['account_id'] }} · user {{ $row['user_id'] }}</div>
      @if($row['email'] !== '')<div class="muted">{{ $row['email'] }}</div>@endif
     </td>
     <td>{{ $row['provider'] }}</td>
     <td>{{ $row['has_key'] ? 'Yes' : 'No' }}</td>
     <td>{{ $row['key_updated'] !== '' ? $row['key_updated'] : '—' }}</td>
     <td>{{ $row['username'] }}</td>
     <td>
      <span class="pill {{ $row['status'] === 'active' ? 'ok' : ($row['status'] === 'error' ? 'bad' : 'wait') }}">{{ $row['status'] }}</span>
      <div class="muted">{{ $row['enabled'] ? 'Enabled' : 'Disabled' }}</div>
     </td>
     <td>{{ $row['result'] !== '' ? $row['result'] : '—' }}</td>
     <td>{{ $row['created'] !== '' ? $row['created'] : '—' }}</td>
     <td>{{ $row['updated'] !== '' ? $row['updated'] : '—' }}</td>
     <td>{{ $row['provisioned'] !== '' ? $row['provisioned'] : '—' }}</td>
     <td>
      <div class="acts">
       <form method="post" action="/admin/ai-connect/voice-bots/reprovision" onsubmit="return confirm('Re-provision this voice account?')">
        @csrf
        <input type="hidden" name="user_id" value="{{ $row['user_id'] }}">
        <button class="dark" type="submit">Re-provision</button>
       </form>
       <form method="post" action="/admin/ai-connect/voice-bots/deprovision" onsubmit="return confirm('Remove this voice account from the gateway?')">
        @csrf
        <input type="hidden" name="user_id" value="{{ $row['user_id'] }}">
        <button type="submit">Deprovision</button>
       </form>
      </div>
     </td>
    </tr>
   @empty
    <tr><td colspan="11" class="muted">No voice accounts yet.</td></tr>
   @endforelse
   </tbody>
  </table>
 </div>
</div>
@endsection
