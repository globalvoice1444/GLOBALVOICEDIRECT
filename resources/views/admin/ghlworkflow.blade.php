@extends('admin.layouts.layout')
@section('content')
<div class="page-header"><div class="page-block"><div class="row align-items-center"><div class="col-md-12">
<ul class="breadcrumb"><li class="breadcrumb-item"><a href="/"><i class="feather icon-home"></i></a></li><li class="breadcrumb-item">GHL Workflow</li></ul>
</div></div></div></div>
<div style="margin:20px 0;padding:22px 26px;border-radius:12px;background:#0b1f3a;color:#fff;border-left:6px solid #f7941d;box-shadow:0 6px 18px rgba(11,31,58,.18)">
  <div style="font-size:18px;font-weight:700;margin-bottom:6px">GHL Workflow</div>
  <div style="opacity:.92">{{ $ghlNotice ?? 'Go High Level is connected.' }}</div>
</div>
@endsection
