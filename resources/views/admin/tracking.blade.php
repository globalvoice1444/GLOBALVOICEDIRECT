@extends('admin.layouts.layout')
@section('css')
<style>.trk .tabs a{display:inline-block;padding:6px 14px;border-radius:16px;margin-right:6px;background:#eef1f5;color:#1E2A36}.trk .tabs a.on{background:#1E2A36;color:#fff}.trk td,.trk th{font-size:13px}.trk .m{color:#94a3b8;font-size:11px}.trk .note{background:#FFF7ED;border-left:3px solid #F97316;padding:8px 12px;font-size:12px;margin:10px 0}.trk tr.sel{background:#fffbea}</style>
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body trk">
<div class="card"><div class="card-header"><h5>Email tracking: opens &amp; clicks</h5>
 <div class="tabs" style="margin-top:10px">@foreach(['broadcast'=>'Broadcasts','drip'=>'Drip steps','workflow'=>'Workflow emails'] as $k=>$v)<a class="{{ $type===$k?'on':'' }}" href="?type={{ $k }}">{{ $v }}</a>@endforeach
 <a href="/admin/automations/broadcasts" style="background:none">&larr; Broadcasts</a><a href="/admin/automations/drip" style="background:none">Drip</a><a href="/admin/automations/workflows" style="background:none">Workflows</a></div>
 <div class="note"><b>Opens are approximate.</b> Some email apps block images (missed opens), and Apple Mail Privacy Protection and security scanners load images automatically (false opens). Those are flagged as <i>likely machine</i>; "Human" columns leave them out. Clicks are more reliable. Tracking applies to emails sent after Nov 2, 2026 3:30 PM ET, so earlier sends (e.g. the first part of Broadcast #1) are not counted in the rates.</div>
</div><div class="card-block">
 <table class="table table-sm"><thead><tr><th>{{ ['broadcast'=>'Broadcast','drip'=>'Drip step','workflow'=>'Workflow'][$type] }}</th>@if($sent)<th>Sent (all)</th>@endif<th>Tracked sends</th><th>Unique opens</th><th>Open rate</th><th>Human opens</th><th>Total opens</th><th>Unique clicks</th><th>CTR</th><th>Click-to-open</th><th>Total clicks</th><th></th></tr></thead><tbody>
 @php $shown = 0; @endphp
 @foreach($names as $id=>$n) @php $s = $sum[$id] ?? null; if(!$s && empty($sent[$id])) continue; $shown++; @endphp
  <tr class="{{ $ref===$id?'sel':'' }}"><td>#{{ $id }} {{ $n }}</td>@if($sent)<td>{{ $sent[$id] ?? 0 }}</td>@endif<td>{{ $s['tracked'] ?? 0 }}</td><td>{{ $s['unique_opens'] ?? 0 }}</td><td>{{ $s ? $s['open_rate'].'%' : '—' }}</td><td>{{ $s['human_opens'] ?? 0 }}</td><td>{{ $s['opens'] ?? 0 }}</td><td>{{ $s['unique_clicks'] ?? 0 }}</td><td>{{ $s ? $s['ctr'].'%' : '—' }}</td><td>{{ $s ? $s['click_to_open'].'%' : '—' }}</td><td>{{ $s['clicks'] ?? 0 }}</td>
  <td><a href="?type={{ $type }}&ref={{ $id }}">Recipients</a></td></tr>
 @endforeach
 @if(!$shown)<tr><td colspan="12" class="text-muted">No tracked sends yet.</td></tr>@endif
 </tbody></table>
 @if($ref)
  <h6 style="margin-top:20px">#{{ $ref }} {{ $names[$ref] ?? '' }}: recipients
   <span style="font-weight:400;margin-left:10px">@foreach([''=>'All','opened'=>'Opened','clicked'=>'Clicked','unopened'=>'Not opened'] as $k=>$v)<a href="?type={{ $type }}&ref={{ $ref }}&f={{ $k }}" style="margin-right:8px;{{ ($f??'')===$k?'font-weight:700':'' }}">{{ $v }}</a>@endforeach</span></h6>
  @if(count($links))<table class="table table-sm" style="max-width:900px"><thead><tr><th>Link</th><th>Unique clickers</th><th>Clicks</th></tr></thead><tbody>@foreach($links as $l)<tr><td style="word-break:break-all">{{ \Illuminate\Support\Str::limit($l->url, 110) }}</td><td>{{ $l->uniq }}</td><td>{{ $l->clicks }}</td></tr>@endforeach</tbody></table>@endif
  <table class="table table-sm"><thead><tr><th>Email</th><th>Sent (ET)</th><th>Opened</th><th>First open (ET)</th><th>Last open (ET)</th><th>Opens</th><th>Clicks</th><th>First click (ET)</th></tr></thead><tbody>
  @php $et = fn($t) => $t ? \Carbon\Carbon::parse($t)->tz('America/New_York')->format('M j g:i A') : ''; @endphp
  @forelse($rows as $x)<tr><td>{{ $x->email }}</td><td>{{ $et($x->sent_at) }}</td><td>{!! $x->first_open ? ($x->open_machine ? 'Yes <span class="m">likely machine</span>' : 'Yes') : '' !!}</td><td>{{ $et($x->first_open) }}</td><td>{{ $et($x->last_open) }}</td><td>{{ $x->open_count }}</td><td>{!! $x->clicks ? $x->clicks.($x->click_machine ? ' <span class="m">likely scanner</span>' : '') : '' !!}</td><td>{{ $et($x->first_click) }}</td></tr>
  @empty<tr><td colspan="8" class="text-muted">No tracked recipients{{ $f ? ' for this filter' : '' }}.</td></tr>@endforelse
  </tbody></table>
 @endif
</div></div></div></div></div></div>
@endsection
