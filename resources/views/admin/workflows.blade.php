@extends('admin.layouts.layout')
@section('css')
@include('admin._wfcss')
@endsection
@section('content')
<div class="pcoded-inner-content"><div class="main-body"><div class="page-wrapper"><div class="page-body gwf">
<div class="top"><div><div class="crumb">Admin › Automations › <b>Workflows</b></div><h1>Automation Workflows</h1></div>
 <div class="nav"><a href="?tab=list" class="{{ $tab=='list'?'on':'' }}">Workflows</a><a href="?tab=tpl" class="{{ $tab=='tpl'?'on':'' }}">Templates</a><a href="?tab=stats" class="{{ $tab=='stats'?'on':'' }}">Analytics</a><a href="/admin/automations/drip">Drip emails</a></div>
 <form method="post" action="/admin/automations/workflows" style="display:flex;gap:6px">@csrf<input name="name" class="form-control form-control-sm" placeholder="Workflow name" required><button class="b">+ New workflow</button></form></div>
@if(session('message'))<div class="alert alert-info">{{ session('message') }}</div>@endif
<div class="sw {{ $enabled?'ok':'' }}"><b>Global send switch: {{ $enabled?'ON':'OFF' }}</b> — workflows run every 5 minutes only while this is on <i>and</i> the workflow is Active. New workflows always start Paused. Login tracking: {{ number_format($tracked) }} customers recorded.</div>
@if($tab=='list')
<div class="kpis">
 <div class="kpi pop"><div class="l">Active workflows</div><div class="v">{{ $wfs->where('status','ACTIVE')->count() }}</div><div class="s">of {{ $wfs->count() }} total</div></div>
 <div class="kpi pop" style="animation-delay:.05s"><div class="l">Contacts enrolled</div><div class="v">{{ number_format($enr->sum('total')) }}</div><div class="s">{{ number_format($enr->sum('active')) }} in progress</div></div>
 <div class="kpi pop" style="animation-delay:.1s"><div class="l">Emails sent (30d)</div><div class="v">{{ number_format($emails30) }}</div><div class="s">From workflows</div></div>
 <div class="kpi pop" style="animation-delay:.15s"><div class="l">Completed</div><div class="v">{{ number_format($enr->sum('done')) }}</div><div class="s">Reached the end</div></div></div>
<div class="panel pop" style="animation-delay:.2s"><div class="ph">WORKFLOWS</div><div class="pb">
 @if($wfs->isEmpty())<div class="empty">No workflows yet. Start from a template.</div>@else
 <table><tr><th>Workflow</th><th>Trigger</th><th>Status</th><th>Enrolled</th><th>Last run</th><th></th></tr>
 @foreach($wfs as $w)<tr><td><b>{{ $w->name }}</b><div class="hint">{{ $w->description }}</div></td><td>{{ $triggers[$w->trigger] ?? $w->trigger }}</td>
  <td><span class="st {{ $w->status }}">{{ ucfirst(strtolower($w->status)) }}</span></td><td>{{ $enr[$w->id]->total ?? 0 }}</td>
  <td>{{ $w->last_run_at ? \Carbon\Carbon::parse($w->last_run_at)->diffForHumans() : 'Never' }}</td><td><a class="b g sm" href="/admin/automations/workflows/{{ $w->id }}">Open</a></td></tr>@endforeach
 </table>@endif</div></div>
@elseif($tab=='tpl')
<div class="hint" style="margin-bottom:14px;font-size:13px">Start from a proven recipe. Every template is created <b>paused</b> so you can review before anything runs.</div>
<div class="tpls">@foreach($templates as $k=>$t)
 <div class="tpl pop" style="animation-delay:{{ $loop->index*.05 }}s"><span class="ic t">★</span><h3>{{ $t[0] }}</h3><p>{{ $t[3] }}</p>
 <div style="margin-bottom:12px"><span class="tg">{{ $triggers[$t[1]] }}</span>@foreach($t[4] as $n)<span class="tg">{{ \App\Support\Workflow::ACTIONS[$n['type']] }}</span>@endforeach</div>
 <form method="post" action="/admin/automations/workflows">@csrf<input type="hidden" name="template" value="{{ $k }}"><button class="b g">Use template</button></form></div>@endforeach</div>
@else
<div class="kpis"><div class="kpi pop"><div class="l">Enrolled</div><div class="v">{{ number_format($enr->sum('total')) }}</div></div><div class="kpi pop"><div class="l">Completed</div><div class="v">{{ number_format($enr->sum('done')) }}</div></div>
 <div class="kpi pop"><div class="l">Emails (30d)</div><div class="v">{{ number_format($emails30) }}</div></div><div class="kpi pop"><div class="l">Open rate</div><div class="v">—</div><div class="s">Tracking not enabled yet</div></div></div>
<div class="panel pop"><div class="ph">RECENT ACTIVITY</div><div class="pb">@if($recent->isEmpty())<div class="empty">Nothing has run yet.</div>@else
 <table><tr><th>When</th><th>Workflow</th><th>Step</th><th>Result</th><th>Detail</th></tr>@foreach($recent as $x)<tr><td>{{ $x->created_at }}</td><td>#{{ $x->workflow_id }}</td><td>{{ $x->action }}</td><td>{{ $x->result }}</td><td>{{ $x->detail }}</td></tr>@endforeach</table>@endif</div></div>
@endif
</div></div></div></div>
@endsection
