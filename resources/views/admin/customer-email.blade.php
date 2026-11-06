@extends('admin.layouts.layout')
@section('css')
<style>.gce{--gn:#1E2A36;--go:#FF5000;--gl:#E6EAF0;--gm:#64748B}.gce .card{background:#fff;border:1px solid var(--gl);border-radius:12px;padding:20px;margin-bottom:16px}
.gce table{width:100%;border-collapse:collapse;font-size:13px}.gce th,.gce td{padding:8px;border-bottom:1px solid var(--gl);text-align:left;vertical-align:middle}.gce th{color:var(--gm);font-weight:600}
.gce .pill{border-radius:10px;padding:2px 8px;font-size:12px;background:#eef2f6}.gce .pill.bad{background:#fde8e8;color:#b42318}.gce .pill.ok{background:#e6f4ea;color:#1e7e34}
.gce input{border:1px solid var(--gl);border-radius:6px;padding:4px 6px;width:80px}.gce button{border:1px solid var(--gl);background:#fff;border-radius:6px;padding:4px 10px;cursor:pointer}.gce button.dark{background:var(--gn);color:#fff;border-color:var(--gn)}</style>
@endsection
@section('content')
<div class="gce">
 <div class="card"><h4 style="margin:0 0 6px">Customer email campaigns</h4>
  <div style="color:var(--gm);font-size:13px">Global switch EC_SENDING: <b>{{ $live ? 'ON' : 'OFF' }}</b> · default limit {{ $def }}/day per customer ({{ $newLim }}/day in the first 7 days) · 50 per 5-minute batch · campaigns pause automatically on high failure, bounce or complaint rates.</div>
  @if(session('message'))<div style="margin-top:8px" class="pill ok">{{ session('message') }}</div>@endif</div>
 <div class="card"><table><thead><tr><th>Customer</th><th>Contacts</th><th>Campaigns</th><th>Active</th><th>Sent 30d</th><th>Failed 30d</th><th>Bounced</th><th>Complaints</th><th>Status</th><th>Daily limit</th><th></th></tr></thead><tbody>
 @forelse($rows as $x) @php $s = $senders[$x->user_id] ?? null; @endphp
  <tr><td><b>{{ $x->user_login }}</b><br><span style="color:var(--gm)">{{ $x->user_email }}</span></td><td>{{ $contacts[$x->user_id] ?? 0 }}</td><td>{{ $x->campaigns }}</td><td>{{ (int)$x->active }}</td><td>{{ (int)$x->sent30 }}</td><td>{{ (int)$x->failed30 }}</td><td>{{ (int)$x->bounced }}</td><td>{{ (int)$x->complained }}</td>
  <td>@if($s && $s->blocked)<span class="pill bad" title="{{ $s->blocked_reason }}">Blocked</span>@else<span class="pill ok">Allowed</span>@endif</td>
  <td><form method="post" action="/admin/automations/customer-email" style="display:flex;gap:4px">@csrf<input type="hidden" name="user_id" value="{{ $x->user_id }}"><input type="hidden" name="action" value="limit"><input name="daily_limit" type="number" min="0" max="5000" placeholder="auto" value="{{ $s->daily_limit ?? '' }}"><button>Set</button></form></td>
  <td><form method="post" action="/admin/automations/customer-email" onsubmit="return confirm('Change sending for {{ $x->user_login }}?')">@csrf<input type="hidden" name="user_id" value="{{ $x->user_id }}">
   @if($s && $s->blocked)<input type="hidden" name="action" value="unblock"><button>Unblock</button>@else<input type="hidden" name="action" value="block"><button class="dark">Block sending</button>@endif</form></td></tr>
 @empty <tr><td colspan="11" style="color:var(--gm)">No customer has created an email campaign yet.</td></tr> @endforelse
 </tbody></table></div>
</div>
@endsection
